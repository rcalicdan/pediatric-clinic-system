<div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Consultation Record
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Medical consultation details for this appointment
            </p>
        </div>

        @if ($consultation)
            <div class="flex space-x-2">
                @can('update', $consultation)
                    <button wire:click="toggleForm"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ $showForm ? 'Cancel' : 'Edit Consultation' }}
                    </button>
                @endcan

                @can('delete', $consultation)
                    <button wire:click="delete" wire:confirm="Are you sure you want to delete this consultation?"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Delete
                    </button>
                @endcan
            </div>
        @else
            @can('create', \App\Models\Consultation::class)
                <button wire:click="toggleForm"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    {{ $showForm ? 'Cancel' : 'Add Consultation' }}
                </button>
            @endcan
        @endif
    </div>

    @if ($showForm)
        <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
            <form wire:submit="save" class="space-y-6">
                @if ($errors->has('general'))
                    <div class="rounded-md bg-red-50 p-4">
                        <div class="text-sm text-red-700">{{ $errors->first('general') }}</div>
                    </div>
                @endif

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Doctor Selection -->
                    <div class="sm:col-span-2">
                        <label for="user_id" class="block text-sm font-medium text-gray-700">Doctor</label>
                        @if (auth()->user()->isAdmin())
                            <select wire:model="user_id" id="user_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select a doctor</option>
                                @foreach ($doctors as $doctor)
                                    <option value="{{ $doctor->id }}">{{ $doctor->full_name }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" value="{{ auth()->user()->full_name }}" readonly
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500 sm:text-sm">
                            <input type="hidden" wire:model="user_id" value="{{ auth()->id() }}">
                            <p class="mt-1 text-xs text-gray-500">You can only assign consultations to yourself.</p>
                        @endif
                        @error('user_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Diagnosis -->
                    <div class="sm:col-span-2">
                        <label for="diagnosis" class="block text-sm font-medium text-gray-700">Diagnosis</label>
                        <textarea wire:model="diagnosis" id="diagnosis" rows="3"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Enter diagnosis..."></textarea>
                        @error('diagnosis')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Treatment -->
                    <div class="sm:col-span-2">
                        <label for="treatment" class="block text-sm font-medium text-gray-700">Treatment</label>
                        <textarea wire:model="treatment" id="treatment" rows="3"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Enter treatment plan..."></textarea>
                        @error('treatment')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prescription -->
                    <div class="sm:col-span-2">
                        <label for="prescription" class="block text-sm font-medium text-gray-700">Prescription</label>
                        <textarea wire:model="prescription" id="prescription" rows="3"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Enter prescription details..."></textarea>
                        @error('prescription')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Vital Signs -->
                    <div class="sm:col-span-2">
                        <h4 class="text-sm font-medium text-gray-900 mb-4">Vital Signs</h4>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div>
                                <label for="height_cm" class="block text-sm font-medium text-gray-700">Height
                                    (cm)</label>
                                <input wire:model="height_cm" type="number" id="height_cm" step="0.1"
                                    min="0" max="300"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('height_cm')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="weight_kg" class="block text-sm font-medium text-gray-700">Weight
                                    (kg)</label>
                                <input wire:model="weight_kg" type="number" id="weight_kg" step="0.1"
                                    min="0" max="500"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('weight_kg')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="temperature_c" class="block text-sm font-medium text-gray-700">Temperature
                                    (°C)</label>
                                <input wire:model="temperature_c" type="number" id="temperature_c" step="0.1"
                                    min="30" max="50"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('temperature_c')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="sm:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                        <textarea wire:model="notes" id="notes" rows="4"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Enter any additional notes..."></textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" wire:click="toggleForm"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </button>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ $isEditing ? 'Update' : 'Create' }} Consultation
                    </button>
                </div>
            </form>
        </div>
    @elseif($consultation)
        <div class="border-t border-gray-200">
            <dl>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Doctor</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $consultation->doctor->full_name }}
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Diagnosis</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $consultation->diagnosis ?: 'Not specified' }}
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Treatment</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $consultation->treatment ?: 'Not specified' }}
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Prescription</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $consultation->prescription ?: 'No prescription' }}
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Vital Signs</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @if ($consultation->height_cm || $consultation->weight_kg || $consultation->temperature_c)
                            <div class="space-y-1">
                                @if ($consultation->height_cm)
                                    <div>Height: {{ $consultation->height_cm }} cm</div>
                                @endif
                                @if ($consultation->weight_kg)
                                    <div>Weight: {{ $consultation->weight_kg }} kg</div>
                                @endif
                                @if ($consultation->temperature_c)
                                    <div>Temperature: {{ $consultation->temperature_c }}°C</div>
                                @endif
                            </div>
                        @else
                            Not recorded
                        @endif
                    </dd>
                </div>
                @if ($consultation->notes)
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Consultation Notes</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $consultation->notes }}
                        </dd>
                    </div>
                @endif
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Consultation Date</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $consultation->created_at->format('F d, Y g:i A') }}
                    </dd>
                </div>
            </dl>
        </div>
    @else
        <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No consultation record</h3>
                <p class="mt-1 text-sm text-gray-500">
                    This appointment doesn't have a consultation record yet.
                </p>
                @can('create', \App\Models\Consultation::class)
                    <div class="mt-6">
                        <button wire:click="toggleForm"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Add Consultation
                        </button>
                    </div>
                @endcan
            </div>
        </div>
    @endif
</div>
