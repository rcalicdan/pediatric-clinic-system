<section class="w-full">
    <x-contents.heading title="Update Appointment" />

    <x-contents.layout>
        <x-flash-session />
        <div class="p-4 sm:p-6 lg:p-8">
            <form wire:submit.prevent='update' class="space-y-6">
                <!-- Patient Selection with Searchable Dropdown -->
                <div class="relative">
                    <label for="patient_search" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Patient
                        </span>
                    </label>

                    <div class="relative">
                        <input type="text" id="patient_search" wire:model.live.debounce.300ms="patientSearch"
                            wire:focus="$set('showPatientDropdown', true)" placeholder="Search by name or ID..."
                            class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            autocomplete="off">

                        @if($selectedPatient)
                        <button type="button" wire:click="clearPatientSelection"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        @else
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        @endif

                        <!-- Dropdown -->
                        @if($showPatientDropdown && count($searchedPatients) > 0)
                        <div
                            class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            @forelse($searchedPatients as $patient)
                            <div wire:click="selectPatient({{ $patient->id }})"
                                class="px-4 py-3 hover:bg-indigo-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900">{{ $patient->full_name }}</div>
                                        <div class="text-sm text-gray-500 flex items-center mt-1">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                                                ID: {{ $patient->id }}
                                            </span>
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $patient->age }} years old
                                            </span>
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>
                            @empty
                            <div class="px-4 py-3 text-gray-500 text-center">
                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                No patients found
                            </div>
                            @endforelse
                        </div>
                        @endif
                    </div>

                    @error('patient_id')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $message }}
                    </p>
                    @enderror

                    @if(!$patientSearch && !$selectedPatient)
                    <p class="mt-2 text-sm text-gray-500">Start typing to search for patients (limited to 15 results)
                    </p>
                    @endif
                </div>

                <!-- Appointment Date - Editable if waiting or if admin -->
                <div>
                    <label for="appointment_date" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            Appointment Date
                        </span>
                    </label>
                    @if ($canUpdateDate || auth()->user()->isAdmin())
                    <input type="date" id="appointment_date" wire:model.live='appointment_date'
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                        required>
                    @if (auth()->user()->isAdmin() && !$canUpdateDate)
                    <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm text-blue-700">Admin override: You can change the date despite current
                                status</p>
                        </div>
                    </div>
                    @endif
                    @else
                    <div class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-700 font-medium">{{ $appointment->appointment_date->format('M d, Y')
                                }}</span>
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                Cannot change date for current status
                            </span>
                        </div>
                    </div>
                    @endif

                    @error('appointment_date')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Queue Number Display -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                            </svg>
                            Queue Number
                        </span>
                    </label>
                    <div
                        class="w-full px-4 py-3 bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-lg">
                        <div class="flex items-center">
                            <span class="text-2xl font-bold text-indigo-600">#{{ $appointment->queue_number }}</span>
                            <span
                                class="ml-3 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                Auto-assigned
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Reason -->
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Reason for Visit
                        </span>
                    </label>
                    <textarea id="reason" wire:model.live='reason' rows="4"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"
                        placeholder="Please describe the reason for this appointment..." required></textarea>

                    @error('reason')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Status - Show all options for admin, transitions for others -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Status
                        </span>
                    </label>
                    @if ($canUpdateStatus || auth()->user()->isAdmin())
                    <select id="status" wire:model.live='status'
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                        required>
                        <option value="{{ $appointment->status->value }}">
                            {{ $appointment->status->getDisplayName() }} (Current)
                        </option>

                        @foreach ($appointment->getAllowedStatusTransitions(auth()->user()) as $statusOption)
                        <option value="{{ $statusOption->value }}">{{ $statusOption->getDisplayName() }}</option>
                        @endforeach
                    </select>

                    @if (auth()->user()->isAdmin() && !$canUpdateStatus)
                    <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm text-blue-700">Admin override: You can change status despite current
                                restrictions</p>
                        </div>
                    </div>
                    @endif
                    @else
                    <div class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span
                                class="px-3 py-1 text-sm font-semibold rounded-full {{ $appointment->getStatusBadgeClass() }}">
                                {{ $appointment->getStatusDisplayName() }}
                            </span>
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                Final status - cannot be changed
                            </span>
                        </div>
                    </div>
                    @endif

                    @error('status')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            Additional Notes
                        </span>
                    </label>
                    <textarea id="notes" wire:model.live='notes' rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"
                        placeholder="Any additional information or special instructions..."></textarea>

                    @error('notes')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <x-utils.link-button :href="route('appointments.index')" button-text="Cancel" />
                    <x-utils.submit-button wire-target="update" button-text="Update Appointment" />
                </div>
            </form>
        </div>
    </x-contents.layout>
</section>

@script
<script>
    document.addEventListener('click', function(event) {
        const dropdown = document.querySelector('[wire\\:focus="$set(\'showPatientDropdown\', true)"]');
        const dropdownMenu = document.querySelector('.absolute.z-50');
        
        if (dropdown && dropdownMenu && !dropdown.contains(event.target) && !dropdownMenu.contains(event.target)) {
            @this.set('showPatientDropdown', false);
        }
    });
</script>
@endscript