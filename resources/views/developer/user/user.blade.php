<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Users') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <table id="usersTable"
                class="display w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200">
                <thead class="bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-200">
                    <tr>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Team Name</th>
                        <th>Personel Team ID</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    @push('scripts')
        <script>
           $(document).ready(function() {
    var table = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('users') }}", // Replace with actual route
            type: "GET",
            data: function(d) {
                d.draw = d.draw || 1;
                d.search.value = d.search.value || '';           
            }
        },
        columns: [
            {
                data: "email",
                name: "email"
            },
            {
                data: "role",
                name: "role"
            },
            {
                data: "team_name",
                name: "team_name"
            },
            {
                data: "personal_team",
                name: "personal_team"
            }
        ],
        initComplete: function() {
            // Add column search input boxes inside the column headers
            this.api().columns().every(function() {
                var column = this;
                var header = $(column.header());
                var title = header.text();

                // Add a search box for each column header
                header.html(title + ' <input type="text" placeholder="Search ' + title + '" />');

                // Listen for keyup events on the column search input fields
                $('input', header).on('keyup change', function() {
                    column.search(this.value).draw(); // Trigger search and redraw for that column
                });
            });
        }
    });
});


        </script>
    @endpush

</x-app-layout>
