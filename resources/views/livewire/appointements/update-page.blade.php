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
                        @foreach($patients as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->full_name }} ({{ $patient->age }} years old)
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
                    @if($canUpdateDate || auth()->user()->isAdmin())
                    <input type="date" id="appointment_date" name="appointment_date" wire:model.live='appointment_date'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        required>
                    @if(auth()->user()->isAdmin() && !$canUpdateDate)
                    <p class="mt-1 text-xs text-blue-600">Admin override: You can change the date despite current status
                    </p>
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
                    @if($canUpdateStatus || auth()->user()->isAdmin())
                    <select id="status" name="status" wire:model.live='status'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        required>
                        <option value="{{ $appointment->status->value }}">{{ $appointment->status->getDisplayName() }}
                            (Current)</option>

                        @if(auth()->user()->isAdmin())
                        {{-- Admin can change to any status --}}
                        @foreach(App\Enums\AppointmentStatuses::cases() as $statusOption)
                        @if($statusOption !== $appointment->status)
                        <option value="{{ $statusOption->value }}">{{ $statusOption->getDisplayName() }}</option>
                        @endif
                        @endforeach
                        @else
                        {{-- Non-admin follows normal transitions --}}
                        @foreach($availableStatuses as $statusOption)
                        <option value="{{ $statusOption->value }}">{{ $statusOption->getDisplayName() }}</option>
                        @endforeach
                        @endif
                    </select>

                    @if(auth()->user()->isAdmin() && !$canUpdateStatus)
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