<?php

namespace App\Livewire\User;

use App\Models\User;
use App\Enums\UserRoles;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdatePage extends Component
{
    public User $user;
    public $first_name = '';
    public $last_name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $role = '';

    public function mount(User $user)
    {
        // $this->authorize('update', $user);
        $this->user = $user;
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->role = $user->role;
    }

    public function rules()
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user->id)
            ]
        ];

        if ($this->canChangeRole()) {
            $rules['role'] = ['required', Rule::in($this->getAvailableRoles())];
        }

        if (!empty($this->password)) {
            $rules['password'] = ['required', 'min:8', Password::defaults()];
            $rules['password_confirmation'] = ['required','same:password'];
        }

        return $rules;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        $this->authorize('update', $this->user);
        $this->validate();
        
        $updateData = [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
        ];

        if ($this->canChangeRole()) {
            $updateData['role'] = $this->role;
        }

        if (!empty($this->password)) {
            $updateData['password'] = $this->password;
        }

        $this->user->update($updateData);
        session()->flash('success', 'User updated successfully!');
    }

    public function canChangeRole(): bool
    {
        $currentUser = Auth::user();
        
        if ($currentUser->id === $this->user->id) {
            return false;
        }
        
        if ($currentUser->isAdmin()) {
            return true;
        }
        
        if ($currentUser->isDoctor() && !$this->user->isAdmin()) {
            return true;
        }
        
        return false;
    }

    public function getAvailableRoles(): array
    {
        $currentUser = Auth::user();
        
        if ($currentUser->isAdmin()) {
            return [UserRoles::DOCTOR->value, UserRoles::STAFF->value];
        } elseif ($currentUser->isDoctor()) {
            return [UserRoles::STAFF->value];
        }
        
        return [];
    }

    public function render()
    {
        return view('livewire.user.update-page', [
            'availableRoles' => $this->getAvailableRoles(),
            'canChangeRole' => $this->canChangeRole()
        ]);
    }
}