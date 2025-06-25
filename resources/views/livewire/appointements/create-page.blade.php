<section class="w-full">
    <x-contents.heading title="Create New Appointment" />

    <x-contents.layout>
        <div class="p-4 sm:p-6 lg:p-8">
            <form wire:submit.prevent='create' class="space-y-6">
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

                <!-- Appointment Date -->
                <div>
                    <label for="appointment_date" class="block text-sm font-medium text-gray-700">Appointment
                        Date</label>
                    <input type="date" id="appointment_date" name="appointment_date" wire:model.live='appointment_date'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        required>

                    @error('appointment_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
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

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                    <textarea id="notes" name="notes" wire:model.live='notes' rows="3"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Additional notes"></textarea>

                    @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <x-utils.submit-button wire-target="create" button-text="Create Appointment" />
                    <x-utils.link-button :href="route('appointments.index')" button-text="Cancel" />
                </div>
            </form>
        </div>
    </x-contents.layout>
</section>