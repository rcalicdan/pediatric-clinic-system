<section class="w-full">
    <x-contents.heading title="Edit Consultation" />

    <x-contents.layout>
        <div class="p-4 sm:p-6 lg:p-8">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('consultations.show', $consultation->id) }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Consultation
                </a>
            </div>

            <!-- Patient Info Card -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-8">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Patient Information
                    </h3>
                    <div class="mt-2 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-sm text-gray-500">Patient Name</p>
                            <p class="font-medium">{{ $consultation->appointment->patient->full_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Age & Gender</p>
                            <p class="font-medium">{{ $consultation->appointment->patient->age }} years,
                                {{ ucfirst($consultation->appointment->patient->gender) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Appointment Date</p>
                            <p class="font-medium">{{ $consultation->appointment->appointment_date->format('F d, Y') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Queue Number</p>
                            <p class="font-medium">#{{ $consultation->appointment->queue_number }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reuse the existing consultation form component -->
            <livewire:consultations.appointment-consultation-form :appointment="$consultation->appointment" :key="$consultation->id" />
        </div>
    </x-contents.layout>
</section>
