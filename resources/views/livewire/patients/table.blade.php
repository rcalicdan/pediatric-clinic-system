{{-- resources/views/livewire/patients/table.blade.php --}}
<section class="w-full">
    <x-contents.heading title="Patient Management" />

    <x-contents.layout>
        <div x-data="{ isSearchModalOpen: false }" @search-completed.window="isSearchModalOpen = false"
            class="p-4 sm:p-6 lg:p-8">

            <x-contents.table-head>
                <x-utils.search-button searchButtonName="Search Patients" />
                @can('create', App\Models\Patient::class)
                <x-utils.create-button createButtonName="Add New Patient" :route="route('patients.create')" />
                @endcan
            </x-contents.table-head>

            <x-flash-session />

            <!-- Table -->
            <div class="overflow-x-auto bg-white shadow-md rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Patient Name</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Guardian</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Birth Date</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Age</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Gender</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contact</th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($patients as $patient)
                        <tr class="hover:bg-gray-100 transition-colors duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $patient->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $patient->first_name }} {{ $patient->last_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $patient->guardian->first_name }} {{ $patient->guardian->last_name }}
                                <div class="text-xs text-gray-500">{{ ucfirst($patient->guardian->relationship) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ \Carbon\Carbon::parse($patient->birth_date)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ \Carbon\Carbon::parse($patient->birth_date)->age }} years
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $patient->gender === 'male' ? 'bg-blue-100 text-blue-800' : 
                                       ($patient->gender === 'female' ? 'bg-pink-100 text-pink-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($patient->gender) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $patient->guardian->contact_number }}
                                @if($patient->guardian->email)
                                <div class="text-xs text-gray-500">{{ $patient->guardian->email }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-center space-x-2">
                                    <x-utils.view-button :route="route('patients.show', $patient->id)" />
                                    @can('update', $patient)
                                    <x-utils.update-button :route="route('patients.edit', [$patient->id])" />
                                    @endcan
                                    @can('delete', $patient)
                                    <x-utils.delete-button wireClick="delete({{ $patient->id }})" />
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $patients->links() }}
            </div>

            <!-- Search Modal -->
            <x-modals.search-form title="Search Patients" :isSearchModalOpen="$isSearchModalOpen">
                <div>
                    <label for="search-id" class="block text-sm font-medium text-gray-700">Patient ID</label>
                    <input type="text" id="search-id" wire:model='searchId'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter patient ID">
                </div>

                <div>
                    <label for="search-name" class="block text-sm font-medium text-gray-700">Patient Name</label>
                    <input type="text" id="search-name" wire:model='searchName'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter patient name">
                </div>

                <div>
                    <label for="search-guardian" class="block text-sm font-medium text-gray-700">Guardian Name</label>
                    <input type="text" id="search-guardian" wire:model='searchGuardian'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter guardian name">
                </div>

                <div>
                    <label for="search-birth-date" class="block text-sm font-medium text-gray-700">Birth Date</label>
                    <input type="date" id="search-birth-date" wire:model='searchBirthDate'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="search-gender" class="block text-sm font-medium text-gray-700">Gender</label>
                    <select id="search-gender" wire:model='searchGender'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">All Genders</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </x-modals.search-form>

        </div>
    </x-contents.layout>
</section>