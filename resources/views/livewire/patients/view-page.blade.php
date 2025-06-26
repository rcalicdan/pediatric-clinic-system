<section class="w-full">
    <x-contents.heading title="Patient Details" />

    <x-contents.layout>
        <div class="p-4 sm:p-6 lg:p-8">

            <!-- Patient Information Card -->
            <div class="bg-white shadow-md rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Patient Information</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Patient ID</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $patient->id }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Full Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $patient->first_name }} {{ $patient->last_name }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Birth Date</label>
                            <p class="mt-1 text-sm text-gray-900">{{
                                \Carbon\Carbon::parse($patient->birth_date)->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Age</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $patient->age }} years</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Gender</label>
                            <p class="mt-1">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $patient->gender === 'male' ? 'bg-blue-100 text-blue-800' : 
                                       ($patient->gender === 'female' ? 'bg-pink-100 text-pink-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($patient->gender) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guardian Information Card -->
            @if($patient->guardian)
            <div class="bg-white shadow-md rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Guardian Information</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Guardian Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $patient->guardian->first_name }} {{
                                $patient->guardian->last_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Relationship</label>
                            <p class="mt-1 text-sm text-gray-900">{{ ucfirst($patient->guardian->relationship) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Contact Number</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $patient->guardian->contact_number }}</p>
                        </div>
                        @if($patient->guardian->email)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Email</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $patient->guardian->email }}</p>
                        </div>
                        @endif
                        @if($patient->guardian->address)
                        <div class="md:col-span-2 lg:col-span-3">
                            <label class="block text-sm font-medium text-gray-500">Address</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $patient->guardian->address }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Appointments Section -->
            <div class="bg-white shadow-md rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Appointments History</h3>
                        @can('create', App\Models\Appointment::class)
                        <x-utils.create-button createButtonName="Schedule Appointment"
                            :route="route('appointments.create', ['patient' => $patient->id])" />
                        @endcan
                    </div>
                </div>

                @if($appointments->count() > 0)
                <!-- Appointments Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date & Time
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Reason
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Consultation
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Notes
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($appointments as $appointment)
                            <tr class="hover:bg-gray-100 transition-colors duration-150 ease-in-out">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('M d, Y') }}
                                    <div class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('h:i A') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <div class="max-w-xs truncate">{{ $appointment->reason }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $appointment->status->getBadgeClass() }}">
                                        {{ $appointment->status->getDisplayName() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    @if($appointment->consultation)
                                    <span class="text-green-600">✓ Completed</span>
                                    @else
                                    <span class="text-gray-400">Not Started</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    @if($appointment->notes)
                                    <div class="max-w-xs truncate" title="{{ $appointment->notes }}">
                                        {{ $appointment->notes }}
                                    </div>
                                    @else
                                    <span class="text-gray-400">No notes</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-2">
                                        @can('view', $appointment)
                                        <a href="{{ route('appointments.show', $appointment->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900 text-xs">
                                            View
                                        </a>
                                        @endcan
                                        @can('update', $appointment)
                                        <x-utils.update-button :route="route('appointments.edit', $appointment->id)" />
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $appointments->links() }}
                </div>
                @else
                <!-- No Appointments Message -->
                <div class="px-6 py-12 text-center">
                    <div class="text-gray-400">
                        <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4m-4 8h4m-4-4h4m-4-4h4M4 7h16a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V8a1 1 0 011-1z" />
                        </svg>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No appointments</h3>
                    <p class="mt-1 text-sm text-gray-500">This patient has no appointment history.</p>
                    @can('create', App\Models\Appointment::class)
                    <div class="mt-6">
                        <x-utils.create-button createButtonName="Schedule First Appointment"
                            :route="route('appointments.create', ['patient' => $patient->id])" />
                    </div>
                    @endcan
                </div>
                @endif
            </div>

            <!-- Back Button -->
            <div class="mt-6">
                <x-utils.link-button :href="route('patients.index')" buttonText="Back to Patients" />
            </div>

        </div>
    </x-contents.layout>
</section>