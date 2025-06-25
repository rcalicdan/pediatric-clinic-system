<section class="w-full">
    <x-contents.heading title="Consultation Details" />

    <x-contents.layout>
        <div class="p-4 sm:p-6 lg:p-8">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('consultations.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Consultations
                </a>
            </div>

            <!-- Consultation Details -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Consultation #{{ $consultation->id }}
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                            Consultation record details and medical information
                        </p>
                    </div>

                    @can('update', $consultation)
                        <a href="{{ route('consultations.edit', $consultation->id) }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Edit Consultation
                        </a>
                    @endcan
                </div>

                <div class="border-t border-gray-200">
                    <dl>
                        <!-- Patient Information -->
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Patient</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <div class="font-medium">{{ $consultation->appointment->patient->full_name }}</div>
                                <div class="text-gray-500">
                                    Age: {{ $consultation->appointment->patient->age }} years •
                                    {{ ucfirst($consultation->appointment->patient->gender) }}
                                </div>
                                <div class="mt-1 text-gray-500">
                                    Guardian: {{ $consultation->appointment->patient->guardian->full_name }}
                                    ({{ ucfirst($consultation->appointment->patient->guardian->relationship) }})
                                </div>
                            </dd>
                        </div>

                        <!-- Doctor Information -->
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Doctor</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <div class="font-medium">{{ $consultation->doctor->full_name }}</div>
                                <div class="text-gray-500">{{ ucfirst($consultation->doctor->role) }}</div>
                            </dd>
                        </div>

                        <!-- Appointment Information -->
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Appointment Details</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <div>Date: {{ $consultation->appointment->appointment_date->format('F d, Y') }}</div>
                                <div>Queue Number: #{{ $consultation->appointment->queue_number }}</div>
                                <div>Status: <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $consultation->appointment->getStatusBadgeClass() }}">
                                        {{ $consultation->appointment->getStatusDisplayName() }}
                                    </span></div>
                                @if ($consultation->appointment->reason)
                                    <div class="mt-1">Reason: {{ $consultation->appointment->reason }}</div>
                                @endif
                            </dd>
                        </div>

                        <!-- Diagnosis -->
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Diagnosis</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $consultation->diagnosis ?: 'Not specified' }}
                            </dd>
                        </div>

                        <!-- Treatment -->
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Treatment</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $consultation->treatment ?: 'Not specified' }}
                            </dd>
                        </div>

                        <!-- Prescription -->
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Prescription</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $consultation->prescription ?: 'No prescription' }}
                            </dd>
                        </div>

                        <!-- Vital Signs -->
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Vital Signs</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                @if ($consultation->height_cm || $consultation->weight_kg || $consultation->temperature_c)
                                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                                        @if ($consultation->height_cm)
                                            <div>
                                                <span class="font-medium">Height:</span> {{ $consultation->height_cm }}
                                                cm
                                            </div>
                                        @endif
                                        @if ($consultation->weight_kg)
                                            <div>
                                                <span class="font-medium">Weight:</span> {{ $consultation->weight_kg }}
                                                kg
                                            </div>
                                        @endif
                                        @if ($consultation->temperature_c)
                                            <div>
                                                <span class="font-medium">Temperature:</span>
                                                {{ $consultation->temperature_c }}°C
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    Not recorded
                                @endif
                            </dd>
                        </div>

                        <!-- Notes -->
                        @if ($consultation->notes)
                            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Consultation Notes</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <div class="whitespace-pre-wrap">{{ $consultation->notes }}</div>
                                </dd>
                            </div>
                        @endif

                        <!-- Timestamps -->
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Consultation Date</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $consultation->created_at->format('F d, Y g:i A') }}
                                @if ($consultation->updated_at != $consultation->created_at)
                                    <div class="text-gray-500 text-xs mt-1">
                                        Last updated: {{ $consultation->updated_at->format('F d, Y g:i A') }}
                                    </div>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </x-contents.layout>
</section>
