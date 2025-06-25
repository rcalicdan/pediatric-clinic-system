<section class="w-full" x-data="patientCreateForm()" x-init="init()">
    <x-contents.heading title="Create New Patient" />

    <x-contents.layout>
        <div class="p-4 sm:p-6 lg:p-8">
            <!-- Progress Indicator -->
            <div class="mb-8">
                <div class="flex items-center">
                    <div class="flex items-center text-sm">
                        <div class="flex items-center {{ $currentStep >= 1 ? 'text-indigo-600' : 'text-gray-500' }}">
                            <div
                                class="flex-shrink-0 w-8 h-8 border-2 {{ $currentStep >= 1 ? 'border-indigo-600 bg-indigo-600' : 'border-gray-300' }} rounded-full flex items-center justify-center">
                                <span class="text-white font-medium">1</span>
                            </div>
                            <span class="ml-2 font-medium">Guardian Information</span>
                        </div>
                        <div class="flex-1 h-1 mx-4 {{ $currentStep > 1 ? 'bg-indigo-600' : 'bg-gray-300' }}"></div>
                        <div class="flex items-center {{ $currentStep >= 2 ? 'text-indigo-600' : 'text-gray-500' }}">
                            <div
                                class="flex-shrink-0 w-8 h-8 border-2 {{ $currentStep >= 2 ? 'border-indigo-600 bg-indigo-600' : 'border-gray-300' }} rounded-full flex items-center justify-center">
                                <span
                                    class="{{ $currentStep >= 2 ? 'text-white' : 'text-gray-500' }} font-medium">2</span>
                            </div>
                            <span class="ml-2 font-medium">Patient Information</span>
                        </div>
                    </div>
                </div>
            </div>

            <x-flash-session />

            @if ($currentStep === 1)
            <!-- Guardian Form -->
            <form wire:submit.prevent='submitGuardian' class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Guardian Information</h3>

                <!-- Guardian First Name -->
                <div>
                    <label for="guardian_first_name" class="block text-sm font-medium text-gray-700">First
                        Name</label>
                    <input type="text" id="guardian_first_name" wire:model.live='guardian_first_name'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter guardian's first name" required>
                    @error('guardian_first_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Guardian Last Name -->
                <div>
                    <label for="guardian_last_name" class="block text-sm font-medium text-gray-700">Last
                        Name</label>
                    <input type="text" id="guardian_last_name" wire:model.live='guardian_last_name'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter guardian's last name" required>
                    @error('guardian_last_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact Number -->
                <div>
                    <label for="guardian_contact_number" class="block text-sm font-medium text-gray-700">Contact
                        Number</label>
                    <input type="text" id="guardian_contact_number" wire:model.live='guardian_contact_number'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter contact number" required>
                    @error('guardian_contact_number')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="guardian_email" class="block text-sm font-medium text-gray-700">Email
                        (Optional)</label>
                    <input type="email" id="guardian_email" wire:model.live='guardian_email'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter email address">
                    @error('guardian_email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Relationship -->
                <div>
                    <label for="guardian_relationship"
                        class="block text-sm font-medium text-gray-700">Relationship</label>
                    <select id="guardian_relationship" wire:model.live='guardian_relationship'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        required>
                        <option value="">Select relationship</option>
                        @foreach ($relationshipOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('guardian_relationship')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div>
                    <label for="guardian_address" class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea id="guardian_address" wire:model.live='guardian_address' rows="3"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter address" required></textarea>
                    @error('guardian_address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-2">
                    <x-utils.submit-button wire-target="submitGuardian" button-text="Next: Patient Information" />
                    <button type="button" @click="handleCancel()"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </button>
                </div>
            </form>
            @endif

            @if ($currentStep === 2)
            <!-- Patient Form -->
            <form wire:submit.prevent='submitPatient' class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Patient Information</h3>

                <!-- Patient First Name -->
                <div>
                    <label for="patient_first_name" class="block text-sm font-medium text-gray-700">First
                        Name</label>
                    <input type="text" id="patient_first_name" wire:model.live='patient_first_name'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter patient's first name" required>
                    @error('patient_first_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Patient Last Name -->
                <div>
                    <label for="patient_last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                    <input type="text" id="patient_last_name" wire:model.live='patient_last_name'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter patient's last name" required>
                    @error('patient_last_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Birth Date -->
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700">Birth Date</label>
                    <input type="date" id="birth_date" wire:model.live='birth_date'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        required>
                    @error('birth_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gender -->
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                    <select id="gender" wire:model.live='gender'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        required>
                        <option value="">Select gender</option>
                        @foreach ($genderOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('gender')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-between">
                    <button type="button" wire:click="previousStep"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Previous: Guardian Information
                    </button>
                    <div class="flex space-x-2">
                        <x-utils.submit-button wire-target="submitPatient" button-text="Create Patient" />
                        <button type="button" @click="handleCancel()"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </button>
                    </div>
                </div>
            </form>
            @endif
        </div>
    </x-contents.layout>

    <script>
        function patientCreateForm() {
            return {
                init() {
                    this.setupNavigationHandlers();
                },

                setupNavigationHandlers() {
                    window.addEventListener('beforeunload', () => {
                        this.cleanupOnExit();
                    });

                    window.addEventListener('popstate', () => {
                        this.cleanupOnExit();
                    });

                    document.addEventListener('livewire:navigating', () => {
                        this.cleanupOnExit();
                    });
                },

                handleCancel() {
                    this.$wire.call('cancel');
                },

                cleanupOnExit() {
                    this.$wire.call('cleanupTempGuardian');
                }
            }
        }
    </script>
</section>