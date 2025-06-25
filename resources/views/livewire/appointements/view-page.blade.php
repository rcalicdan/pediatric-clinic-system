<section class="w-full">
    <x-contents.heading title="Appointment Details" />

    <x-contents.layout>
        <div class="p-4 sm:p-6 lg:p-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Appointment #{{ $appointment->queue_number }}
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        {{ $appointment->appointment_date->format('F d, Y') }}
                    </p>
                </div>
                <div class="border-t border-gray-200">
                    <dl>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Patient</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $appointment->patient->full_name }}
                                <span class="text-gray-500">({{ $appointment->patient->age }} years old)</span>
                            </dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Queue Number</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                #{{ $appointment->queue_number }}
                            </dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Appointment Date</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $appointment->appointment_date->format('F d, Y') }}
                            </dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $appointment->getStatusBadgeClass() }}">
                                    {{ $appointment->getStatusDisplayName() }}
                                </span>
                            </dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Reason for Visit</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $appointment->reason }}
                            </dd>
                        </div>
                        @if ($appointment->notes)
                            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Notes</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {{ $appointment->notes }}
                                </dd>
                            </div>
                        @endif
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Created At</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $appointment->created_at->format('F d, Y g:i A') }}
                            </dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $appointment->updated_at->format('F d, Y g:i A') }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

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
                    @can('create', \App\Models\Consultation::class)
                        <x-utils.link-button :href="route('consultations.create', ['appointment' => $appointment->id])" button-text="Add Consultation"
                            class="bg-green-600 hover:bg-green-700 text-white" />
                    @endcan
                </div>

                @if ($appointment->consultation)
                    <div class="border-t border-gray-200">
                        <dl>
                            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Doctor</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {{ $appointment->consultation->doctor->name }}
                                </dd>
                            </div>
                            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Diagnosis</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {{ $appointment->consultation->diagnosis ?: 'Not specified' }}
                                </dd>
                            </div>
                            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Treatment</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {{ $appointment->consultation->treatment ?: 'Not specified' }}
                                </dd>
                            </div>
                            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Prescription</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {{ $appointment->consultation->prescription ?: 'No prescription' }}
                                </dd>
                            </div>
                            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Vital Signs</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    @if (
                                        $appointment->consultation->height_cm ||
                                            $appointment->consultation->weight_kg ||
                                            $appointment->consultation->temperature_c)
                                        <div class="space-y-1">
                                            @if ($appointment->consultation->height_cm)
                                                <div>Height: {{ $appointment->consultation->height_cm }} cm</div>
                                            @endif
                                            @if ($appointment->consultation->weight_kg)
                                                <div>Weight: {{ $appointment->consultation->weight_kg }} kg</div>
                                            @endif
                                            @if ($appointment->consultation->temperature_c)
                                                <div>Temperature: {{ $appointment->consultation->temperature_c }}°C
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        Not recorded
                                    @endif
                                </dd>
                            </div>
                            @if ($appointment->consultation->notes)
                                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Consultation Notes</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $appointment->consultation->notes }}
                                    </dd>
                                </div>
                            @endif
                            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Consultation Date</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {{ $appointment->consultation->created_at->format('F d, Y g:i A') }}
                                </dd>
                            </div>
                        </dl>
                        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                            @can('update', $appointment->consultation)
                                <x-utils.link-button :href="route('consultations.edit', $appointment->consultation->id)" button-text="Edit Consultation"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white mr-2" />
                            @endcan
                            @can('view', $appointment->consultation)
                                <x-utils.link-button :href="route('consultations.show', $appointment->consultation->id)" button-text="View Full Details"
                                    class="bg-gray-600 hover:bg-gray-700 text-white" />
                            @endcan
                        </div>
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
                                    <x-utils.link-button :href="route('consultations.create', ['appointment' => $appointment->id])" button-text="Add Consultation"
                                        class="bg-green-600 hover:bg-green-700 text-white" />
                                </div>
                            @endcan
                        </div>
                    </div>
                @endif
            </div>

            @if ($appointment->invoice)
                <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Invoice
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                            Billing information for this appointment
                        </p>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                        <x-utils.link-button :href="route('invoices.show', $appointment->invoice->id)" button-text="View Invoice"
                            class="bg-blue-600 hover:bg-blue-700 text-white" />
                    </div>
                </div>
            @endif

            <div class="mt-6 flex justify-end space-x-3">
                @can('update', $appointment)
                    <x-utils.link-button :href="route('appointments.edit', $appointment->id)" button-text="Edit Appointment"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white" />
                @endcan
                <x-utils.link-button :href="route('appointments.index')" button-text="Back to List" />
            </div>
        </div>
    </x-contents.layout>
</section>
