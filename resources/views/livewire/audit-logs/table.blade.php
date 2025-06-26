<section class="w-full">
    <x-contents.heading title="Audit Logs" />

    <x-contents.layout>
        <div wire:poll='10s' x-data="{ isSearchModalOpen: false }" @search-completed.window="isSearchModalOpen = false"
            class="p-4 sm:p-6 lg:p-8">

            <x-contents.table-head>
                <x-utils.search-button searchButtonName="Search Audit Logs" />
            </x-contents.table-head>

            <!-- Table -->
            <div class="overflow-x-auto bg-white shadow-md rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Event</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Model</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Record ID</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                User</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                IP Address</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date & Time</th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($auditLogs as $auditLog)
                        <tr class="hover:bg-gray-100 transition-colors duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $this->getEventBadgeClass($auditLog->event) }}">
                                    {{ ucfirst($auditLog->event) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ class_basename($auditLog->auditable_type) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $auditLog->auditable_id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                @if($auditLog->user)
                                    {{ $auditLog->user->full_name ?? $auditLog->user->name }}
                                    <div class="text-xs text-gray-500">ID: {{ $auditLog->user_id }}</div>
                                @else
                                    <span class="text-gray-500">System</span>
                                    @if($auditLog->user_id)
                                        <div class="text-xs text-gray-500">ID: {{ $auditLog->user_id }}</div>
                                    @endif
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $auditLog->ip_address ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $auditLog->created_at->format('M d, Y') }}
                                <div class="text-xs text-gray-500">{{ $auditLog->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-center space-x-2">
                                    @can('view', $auditLog)
                                    <x-utils.view-button :route="route('audit-logs.show', $auditLog->id)" />
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $auditLogs->links() }}
            </div>

            <!-- Search Modal -->
            <x-modals.search-form title="Search Audit Logs" :isSearchModalOpen="$isSearchModalOpen">
                <div>
                    <label for="search-event" class="block text-sm font-medium text-gray-700">Event</label>
                    <select id="search-event" wire:model='searchEvent'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">All Events</option>
                        <option value="created">Created</option>
                        <option value="updated">Updated</option>
                        <option value="deleted">Deleted</option>
                        <option value="restored">Restored</option>
                    </select>
                </div>

                <div>
                    <label for="search-user" class="block text-sm font-medium text-gray-700">User Name</label>
                    <input type="text" id="search-user" wire:model='searchUser'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter user name">
                </div>

                <div>
                    <label for="search-user-id" class="block text-sm font-medium text-gray-700">User ID</label>
                    <input type="number" id="search-user-id" wire:model='searchUserId'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter user ID">
                </div>

                <div>
                    <label for="search-type" class="block text-sm font-medium text-gray-700">Model Type</label>
                    <input type="text" id="search-type" wire:model='searchType'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter model type (e.g., Patient, User)">
                </div>

                <div>
                    <label for="search-date" class="block text-sm font-medium text-gray-700">Date</label>
                    <input type="date" id="search-date" wire:model='searchDate'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </x-modals.search-form>

        </div>
    </x-contents.layout>
</section>