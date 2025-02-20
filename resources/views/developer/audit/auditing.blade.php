<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Audit Logs') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                Total Records: {{ $totalCount }}
            </h3>
            <span class="text-sm text-gray-600 dark:text-gray-400">
                Showing {{ $audits->count() }} records on this page, Total: {{ $totalCount }}
            </span>
        </div>

        <!-- Search Form -->
        <form method="GET" action="{{ route('audit') }}" class="mb-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search audit logs..."
                class="px-4 py-2 border rounded-md focus:ring focus:ring-indigo-200 dark:bg-gray-800 dark:text-gray-200">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Search
            </button>
            @if (request('search'))
                <a href="{{ route('audit') }}" class="ml-2 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Reset
                </a>
            @endif
        </form>

        <!-- Table -->
        <div class="bg-white dark:bg-gray-900 shadow overflow-hidden sm:rounded-lg p-2">
            <div class="overflow-auto">
                <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-600">
                    <thead class="bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">User ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Event</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">New Values</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Created At</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-300 dark:divide-gray-700">
                        @foreach ($audits as $audit)
                            <tr class="cursor-pointer"
                                data-details="{{ json_encode(
                                    [
                                        'ID' => $audit->id,
                                        'User Type' => $audit->user_type ?? '--',
                                        'User ID' => $audit->user_id ?? '--',
                                        'Event' => ucfirst($audit->event),
                                        'Auditable Type' => $audit->auditable_type,
                                        'Auditable ID' => $audit->auditable_id,
                                        'Old Values' => json_decode($audit->old_values, true) ?? [],
                                        'New Values' => json_decode($audit->new_values, true) ?? [],
                                        'URL' => $audit->url ?? '--',
                                        'IP Address' => $audit->ip_address ?? '--',
                                        'User Agent' => $audit->user_agent ?? '--',
                                        'Tags' => $audit->tags ?? '--',
                                        'Created At' => $audit->created_at->format('Y-m-d H:i:s'),
                                    ],
                                    JSON_PRETTY_PRINT,
                                ) }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $audit->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $audit->user_id ?? '--' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @php
                                        $colors = [
                                            'created' => 'bg-green-500 text-white',
                                            'deleted' => 'bg-red-500 text-white',
                                            'updated' => 'bg-yellow-500 text-gray-900',
                                        ];
                                        $eventColor = $colors[$audit->event] ?? 'bg-gray-500 text-white';
                                    @endphp
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $eventColor }}">
                                        {{ ucfirst($audit->event) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ json_encode(json_decode($audit->new_values, true)) ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $audit->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $audits->links() }}
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="auditModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-4xl w-full p-6 relative">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Audit Log Details</h3>
            <pre id="auditDetails"
                class="mt-4 p-4 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md overflow-auto max-h-96"></pre>
            <button onclick="closeModal()"
                class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Close
            </button>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll("tbody tr").forEach(row => {
                row.addEventListener("click", function() {
                    const details = this.dataset.details;
                    document.getElementById("auditDetails").textContent = details;
                    document.getElementById("auditModal").classList.remove("hidden");
                });
            });
        });

        function closeModal() {
            document.getElementById("auditModal").classList.add("hidden");
        }
    </script>

</x-app-layout>
