<section class="w-full">
    <x-contents.heading title="Appointment Management" />

    <x-contents.layout>
        <div x-data="{ isSearchModalOpen: false }" @search-completed.window="isSearchModalOpen = false" class="p-4 sm:p-6 lg:p-8">

            <x-contents.table-head>
                <x-utils.search-button searchButtonName="Search Appointments" />
                <x-utils.create-button createButtonName="Add New Appointment" :route="route('appointments.create')" />
            </x-contents.table-head>

            <x-flash-session />

            <!-- Table -->
            <div class="overflow-x-auto bg-white shadow-md rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Queue #
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Patient
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Reason
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($appointments as $appointment)
                            <tr class="hover:bg-gray-100 transition-colors duration-150 ease-in-out">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #{{ $appointment->queue_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $appointment->patient->full_name }}
                                    <div class="text-xs text-gray-500">{{ $appointment->patient->age }} years old</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $appointment->appointment_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <div class="max-w-xs truncate" title="{{ $appointment->reason }}">
                                        {{ $appointment->reason }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $appointment->getStatusBadgeClass() }}">
                                            {{ $appointment->getStatusDisplayName() }}
                                        </span>
                                        @if ($appointment->canUpdateStatus(auth()->user()))
                                            <div class="relative" x-data="{ open: false }">
                                                <button @click="open = !open" class="text-gray-400 hover:text-gray-600">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                    </svg>
                                                </button>
                                                <div x-show="open" @click.away="open = false"
                                                    class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50">
                                                    @foreach ($appointment->getAllowedStatusTransitions(auth()->user()) as $status)
                                                        <button
                                                            wire:click="updateStatus({{ $appointment->id }}, '{{ $status->value }}')"
                                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                            Change to {{ $status->getDisplayName() }}
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-2">
                                        @can('view', $appointment)
                                            <x-utils.view-button :route="route('appointments.show', [$appointment->id])" />
                                        @endcan

                                        @can('update', $appointment)
                                            <x-utils.update-button :route="route('appointments.edit', [$appointment->id])" />
                                        @endcan

                                        @can('delete', $appointment)
                                            @if ($appointment->canBeModified(auth()->user()))
                                                <x-utils.delete-button wireClick="delete({{ $appointment->id }})" />
                                            @endif
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
                {{ $appointments->links() }}
            </div>

            <!-- Search Modal -->
            <x-modals.search-form title="Search Appointments" :isSearchModalOpen="$isSearchModalOpen">
                <div>
                    <label for="search-id" class="block text-sm font-medium text-gray-700">ID</label>
                    <input type="text" id="search-id" wire:model='searchId'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter appointment ID">
                </div>

                <div>
                    <label for="search-patient" class="block text-sm font-medium text-gray-700">Patient Name</label>
                    <input type="text" id="search-patient" wire:model='searchPatient'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter patient name">
                </div>

                <div>
                    <label for="search-date" class="block text-sm font-medium text-gray-700">Appointment Date</label>
                    <input type="date" id="search-date" wire:model='searchDate'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="search-status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="search-status" wire:model='searchStatus'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">All Statuses</option>
                        @foreach ($availableStatuses as $status)
                            <option value="{{ $status->value }}">{{ $status->getDisplayName() }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="search-queue" class="block text-sm font-medium text-gray-700">Queue Number</label>
                    <input type="number" id="search-queue" wire:model='searchQueue'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter queue number">
                </div>
            </x-modals.search-form>

        </div>
    </x-contents.layout>
</section>
