<section class="w-full">
    <x-contents.heading title="Appointment Details" />

    <x-contents.layout>
        <x-flash-session/>
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

            <!-- Consultation Section - Replace the existing consultation section with this -->
            @livewire('consultations.appointment-consultation-form', ['appointment' => $appointment])

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
