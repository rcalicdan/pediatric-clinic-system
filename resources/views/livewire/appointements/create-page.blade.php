<section class="w-full" x-data="patientSelector()">
    <x-contents.heading title="Create New Appointment" />

    <x-contents.layout>
        <div class="p-4 sm:p-6 lg:p-8">
            <x-flash-session />
            <form wire:submit.prevent='create' class="space-y-6">
                <!-- Patient Selection with Searchable Dropdown -->
                <div class="relative" x-data="{ open: @entangle('showPatientDropdown') }">
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
                            x-on:focus="open = true" x-on:click.away="open = false" x-on:keydown.escape="open = false"
                            x-on:keydown.arrow-down.prevent="$wire.call('focusNext')"
                            x-on:keydown.arrow-up.prevent="$wire.call('focusPrevious')"
                            x-on:keydown.enter.prevent="$wire.call('selectFocusedPatient')"
                            placeholder="Search by name or ID..."
                            class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            autocomplete="off">

                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                            <button type="button" x-show="$wire.selectedPatient"
                                x-on:click="$wire.call('clearPatientSelection')"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            <div x-show="!$wire.selectedPatient" class="text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Dropdown -->
                        <div x-show="open && $wire.searchedPatients.length > 0"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-1"
                            class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            <template x-for="(patient, index) in $wire.searchedPatients" :key="patient.id">
                                <div x-on:click="$wire.call('selectPatient', patient.id)"
                                    x-on:mouseenter="$wire.set('focusedIndex', index)"
                                    :class="{ 'bg-indigo-50': $wire.focusedIndex === index }"
                                    class="px-4 py-3 hover:bg-indigo-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900" x-text="patient.full_name"></div>
                                            <div class="text-sm text-gray-500 flex items-center mt-1">
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                                                    ID: <span x-text="patient.id"></span>
                                                </span>
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    <span x-text="patient.age"></span> years old
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
                            </template>

                            <div x-show="$wire.searchedPatients.length === 0 && $wire.patientSearch.length > 0"
                                class="px-4 py-3 text-gray-500 text-center">
                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                No patients found
                            </div>
                        </div>
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

                    <div x-show="!$wire.patientSearch && !$wire.selectedPatient"
                        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100" class="mt-2 text-sm text-gray-500">
                        Start typing to search for patients (limited to 15 results)
                    </div>
                </div>

                <!-- Appointment Date -->
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
                    <input type="date" id="appointment_date" wire:model.live='appointment_date'
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                        required>

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
                        x-data="{ count: $wire.entangle('reason').length }"
                        x-on:input="count = $event.target.value.length"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"
                        placeholder="Please describe the reason for this appointment..." maxlength="255"
                        required></textarea>
                    <div class="mt-1 text-xs text-gray-500 text-right"
                        x-data="{ count: $wire.entangle('reason').length }">
                        <span x-text="count || 0"></span>/255 characters
                    </div>

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
                            Additional Notes (Optional)
                        </span>
                    </label>
                    <textarea id="notes" wire:model.live='notes' rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"
                        placeholder="Any additional information or special instructions..." maxlength="500"></textarea>
                    <div class="mt-1 text-xs text-gray-500 text-right"
                        x-data="{ count: $wire.entangle('notes').length }">
                        <span x-text="count || 0"></span>/500 characters
                    </div>

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

                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <x-utils.link-button :href="route('appointments.index')" button-text="Cancel" />
                    <x-utils.submit-button wire-target="create" button-text="Create Appointment" />
                </div>
            </form>
        </div>
    </x-contents.layout>
</section>