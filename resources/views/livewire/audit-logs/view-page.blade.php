<section class="w-full">
    <x-contents.heading title="Audit Log Details" />

    <x-contents.layout>
        <div class="p-4 sm:p-6 lg:p-8">

            <!-- Audit Log Information Card -->
            <div class="bg-white shadow-md rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Audit Log Information</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Event</label>
                            <p class="mt-1">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $this->getEventBadgeClass($auditLog->event) }}">
                                    {{ ucfirst($auditLog->event) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Model Type</label>
                            <p class="mt-1 text-sm text-gray-900">{{ class_basename($auditLog->auditable_type) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Record ID</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $auditLog->auditable_id }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">User</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $auditLog->user ? $auditLog->user->full_name : 'System'
                                }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">IP Address</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $auditLog->ip_address ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Date & Time</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $auditLog->created_at->format('M d, Y h:i A') }}
                            </p>
                        </div>
                        @if($auditLog->user_agent)
                        <div class="md:col-span-2 lg:col-span-3">
                            <label class="block text-sm font-medium text-gray-500">User Agent</label>
                            <p class="mt-1 text-sm text-gray-900 break-all">{{ $auditLog->user_agent }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Old Values Card -->
            @if($auditLog->old_values && !empty($auditLog->old_values))
            <div class="bg-white shadow-md rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Previous Values</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="bg-red-50 border border-red-200 rounded-md p-4">
                        <pre
                            class="text-sm text-gray-700 whitespace-pre-wrap">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            </div>
            @endif

            <!-- New Values Card -->
            @if($auditLog->new_values && !empty($auditLog->new_values))
            <div class="bg-white shadow-md rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">New Values</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="bg-green-50 border border-green-200 rounded-md p-4">
                        <pre
                            class="text-sm text-gray-700 whitespace-pre-wrap">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            </div>
            @endif

            <!-- Changes Summary -->
            @if($auditLog->old_values && $auditLog->new_values)
            <div class="bg-white shadow-md rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Changes Summary</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-4">
                        @foreach($auditLog->new_values as $key => $newValue)
                        @if(isset($auditLog->old_values[$key]) && $auditLog->old_values[$key] !== $newValue)
                        <div class="border-l-4 border-blue-400 pl-4">
                            <div class="text-sm font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $key)) }}
                            </div>
                            <div class="mt-1 text-sm text-red-600">
                                <span class="font-medium">From:</span> {{ $auditLog->old_values[$key] ?? 'null' }}
                            </div>
                            <div class="text-sm text-green-600">
                                <span class="font-medium">To:</span> {{ $newValue ?? 'null' }}
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Back Button -->
            <div class="mt-6">
                <x-utils.link-button :href="route('audit-logs.index')" buttonText="Back to Audit Logs" />
            </div>

        </div>
    </x-contents.layout>
</section>