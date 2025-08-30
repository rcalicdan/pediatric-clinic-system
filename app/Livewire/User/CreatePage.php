<?php

namespace App\Livewire\User;

use App\Models\User;
use App\Enums\UserRoles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Rcalicdan\FiberAsync\Api\AsyncDb;
use Rcalicdan\FiberAsync\Api\DB;

class CreatePage extends Component
{
    public $first_name;
    public $last_name;
    public $email;
    public $password;
    public $password_confirmation;
    public $role;

    public function mount()
    {
        // $this->authorize('create', User::class);
        $this->role = Auth::user()->isAdmin() ? UserRoles::DOCTOR->value : UserRoles::STAFF->value;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'min:2', 'max:50'],
            'last_name' => ['required', 'min:2', 'max:50'],
            'email' => ['required', 'email', Rule::unique('users', 'email'), 'max:50'],
            'password' => ['required', 'min:8', Password::defaults()],
            'password_confirmation' => ['required', 'same:password'],
            'role' => ['required', Rule::in($this->getAvailableRoles())]
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function create()
    {
        run(function () {
            $this->authorize('create', User::class);
            $validatedData = $this->validate();

            DB::table('usersss')->insert($validatedData);
        });

        session()->flash('success', 'User created successfully.');

        return $this->redirectRoute('users.index', navigate: true);
    }

    public function getAvailableRoles(): array
    {
        $currentUser = Auth::user();

        if ($currentUser->isAdmin()) {
            return [UserRoles::DOCTOR->value, UserRoles::STAFF->value];
        }

        return [];
    }

    public function render()
    {
        return view('livewire.user.create-page', [
            'availableRoles' => $this->getAvailableRoles()
        ]);
    }
}
