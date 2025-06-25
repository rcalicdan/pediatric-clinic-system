<section class="w-full">
    <x-contents.heading title="Update Appointment" />

    <x-contents.layout>
        <x-flash-session />
        <div class="p-4 sm:p-6 lg:p-8">
            <form wire:submit.prevent='update' class="space-y-6">
                <!-- Patient Selection -->
                <div>
                    <label for="patient_id" class="block text-sm font-medium text-gray-700">Patient</label>
                    <select id="patient_id" name="patient_id" wire:model.live='patient_id'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        required>
                        <option value="">Select a patient</option>
                        @foreach ($patients as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->full_name }} ({{ $patient->age }} years
                            old)
                        </option>
                        @endforeach
                    </select>

                    @error('patient_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Appointment Date - Editable if waiting or if admin -->
                <div>
                    <label for="appointment_date" class="block text-sm font-medium text-gray-700">Appointment
                        Date</label>
                    @if ($canUpdateDate || auth()->user()->isAdmin())
                    <input type="date" id="appointment_date" name="appointment_date" wire:model.live='appointment_date'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        required>
                    @if (auth()->user()->isAdmin() && !$canUpdateDate)
                    <p class="mt-1 text-xs text-blue-600">Admin override: You can change the date despite
                        current status
                    </p>
                    @endif

                    <!-- Conflict Warning -->
                    @if ($hasConflict && $conflictingAppointment)
                    <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">
                                    Scheduling Conflict Detected
                                </h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>This patient already has an appointment on {{
                                        \Carbon\Carbon::parse($appointment_date)->format('M d, Y') }}:</p>
                                    <ul class="list-disc list-inside mt-1">
                                        <li>Queue #{{ $conflictingAppointment->queue_number }}</li>
                                        <li>Reason: {{ $conflictingAppointment->reason }}</li>
                                        <li>Status: {{ $conflictingAppointment->getStatusDisplayName() }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @else
                    <div class="mt-1 px-3 py-2 bg-gray-50 border border-gray-300 rounded-md">
                        <span class="text-gray-700">{{ $appointment->appointment_date->format('M d, Y') }}</span>
                        <span class="text-xs text-gray-500 ml-2">(Cannot change date for current status)</span>
                    </div>
                    @endif

                    @error('appointment_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Queue Number Display -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Queue Number</label>
                    <div class="mt-1 px-3 py-2 bg-gray-50 border border-gray-300 rounded-md">
                        <span class="text-gray-700 font-semibold">#{{ $appointment->queue_number }}</span>
                        <span class="text-xs text-gray-500 ml-2">(Auto-assigned)</span>
                    </div>
                </div>

                <!-- Reason -->
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700">Reason for Visit</label>
                    <textarea id="reason" name="reason" wire:model.live='reason' rows="3"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter reason for appointment" required></textarea>

                    @error('reason')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status - Show all options for admin, transitions for others -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    @if ($canUpdateStatus || auth()->user()->isAdmin())
                    <select id="status" name="status" wire:model.live='status'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        required>
                        <option value="{{ $appointment->status->value }}">
                            {{ $appointment->status->getDisplayName() }}
                            (Current)</option>

                        @foreach ($appointment->getAllowedStatusTransitions(auth()->user()) as $statusOption)
                        <option value="{{ $statusOption->value }}">{{ $statusOption->getDisplayName() }}
                        </option>
                        @endforeach
                    </select>

                    @if (auth()->user()->isAdmin() && !$canUpdateStatus)
                    <p class="mt-1 text-xs text-blue-600">Admin override: You can change status despite current
                        restrictions</p>
                    @endif
                    @else
                    <div class="mt-1 px-3 py-2 bg-gray-50 border border-gray-300 rounded-md">
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $appointment->getStatusBadgeClass() }}">
                            {{ $appointment->getStatusDisplayName() }}
                        </span>
                        <span class="text-xs text-gray-500 ml-2">(Final status - cannot be changed)</span>
                    </div>
                    @endif

                    @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea id="notes" name="notes" wire:model.live='notes' rows="3"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Additional notes"></textarea>

                    @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <x-utils.submit-button wire-target="update" button-text="Update Appointment" />
                    <x-utils.link-button :href="route('appointments.index')" button-text="Cancel" />
                </div>
            </form>
        </div>
    </x-contents.layout>
</section>