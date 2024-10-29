@extends("layout.main")
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">

                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                        <p style="color: white;"><strong>User Table</strong></p>                        
                        <div>
                            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addUserModal">Add New User</button>
                        </div>
                     </div>
                </div>
                <div class="card-body px-0 pb-2 px-3">
                    <div class="table-responsive p-0">
                        <table id="userTable" class="table align-items-center mb-0 table-striped table-hover px-2">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">User Name</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Exchange Name</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                                </tr>
                            </thead>
                            
                            <tbody id="userTableBody">
                                @foreach($userRecords as $user)
                                <tr data-user-id="{{ $user->id ?? 'N/A' }}" data-exchange-id="{{ $user->exchange->id ?? 'N/A' }}">
                                    <td style="width: 45%;">{{ $user->name ?? 'N/A'  }}</td>
                                    <td style="width: 45%;">{{ $user->exchange->name ?? 'N/A' }}</td>
                                    <td style="width: 10%; text-align: center;">
                                        <button class="btn btn-danger btn-sm" onclick="deleteUser(this)">Delete</button>
                                        <button class="btn btn-warning btn-sm" onclick="editUser(this)">Edit</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title" id="addUserModalLabel" style="color:white">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">User Name</label>
                            <input type="text" class="form-control border px-3" id="name" placeholder="Enter User Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control border px-3" id="password" placeholder="Enter Password" required>
                        </div>
                        <div class="mb-3">
                            <label for="exchange" class="form-label">Exchange</label>
                            <select class="form-select px-3" id="exchange" required >
                                <option value="" disabled selected>Select an exchange</option>
                                @foreach($exchangeRecords as $exchange)
                                    <option value="{{ $exchange->id }}">{{ $exchange->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="addUser()">Save User</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title" id="editUserModalLabel" style="color:white">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm">
                        @csrf
                        <input type="hidden" id="editUserId">
                        <div class="mb-3">
                            <label for="editName" class="form-label">User Name</label>
                            <input type="text" class="form-control border px-3" id="editName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPassword" class="form-label">Password (leave empty to keep unchanged)</label>
                            <input type="password" class="form-control border px-3" id="editPassword">
                        </div>
                        <div class="mb-3">
                            <label for="editExchange" class="form-label">Exchange</label>
                            <select class="form-select px-3" id="editExchange" required>
                                <option value="" disabled selected>Select an exchange</option>
                                @foreach($exchangeRecords as $exchange)
                                    <option value="{{ $exchange->id }}">{{ $exchange->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="updateUser()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function () {
        // Initialize DataTable
        const userTable = $('#userTable').DataTable({
            pagingType: "full_numbers",
            language: {
                paginate: {
                    first: '«',
                    last: '»',
                    next: '›',
                    previous: '‹'
                }
            },
            lengthMenu: [5, 10, 25, 50],
            pageLength: 10
        });

        // Set up CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Add User Function
        window.addUser = function () {
            const name = $('#name').val();
            const password = $('#password').val();
            const exchange = $('#exchange').val();

            $.ajax({
                url: "{{ route('admin.user.post') }}",
                method: "POST",
                data: {
                    name: name,
                    password: password,
                    exchange: exchange
                },
                success: function (response) {
                    alert(response.message);

                    // Add the new user to DataTable
                    userTable.row.add([
                        response.user.name,
                        response.exchange_name ?? 'N/A',
                        `
                            <button class="btn btn-danger btn-sm" onclick="deleteUser(this)">Delete</button>
                            <button class="btn btn-warning btn-sm" onclick="editUser(this)">Edit</button>
                        `
                    ]).draw(false);

                    $('#addUserModal').modal('hide');
                    $('#addUserForm')[0].reset();
                },
                error: function (xhr) {
                    alert("Error adding user: " + xhr.responseJSON.message);
                }
            });
        };

        // Delete User Function
        window.deleteUser = function (button) {
            const row = $(button).closest('tr');
            const userId = row.data('user-id');

            if (!confirm('Are you sure you want to delete this user?')) {
                return;
            }

            $.ajax({
                url: "{{ route('admin.user.destroy') }}",
                method: "POST",
                data: { id: userId },
                success: function (response) {
                    alert(response.message);
                    // Remove the user from DataTable
                    userTable.row(row).remove().draw();
                },
                error: function (xhr) {
                    alert("Error deleting user: " + xhr.responseJSON.message);
                }
            });
        };

        // Edit User Modal and Populate Fields
        window.editUser = function (button) {
            const row = $(button).closest('tr');
            const userId = row.data('user-id');
            const userName = row.find('td:nth-child(1)').text();
            const exchangeId = row.data('exchange-id');

            $('#editUserId').val(userId);
            $('#editName').val(userName);
            $('#editExchange').val(exchangeId);
            $('#editUserModal').modal('show');
        };

        // Update User Function
        window.updateUser = function () {
            const userId = $('#editUserId').val();
            const name = $('#editName').val();
            const password = $('#editPassword').val();
            const exchange = $('#editExchange').val();

            $.ajax({
                url: "{{ route('admin.user.update') }}",
                method: "POST",
                data: {
                    id: userId,
                    name: name,
                    password: password,
                    exchange: exchange
                },
                success: function (response) {
                    alert(response.message);
                    $('#editUserModal').modal('hide');
                    userTable.ajax.reload(null, false); // Reload table data without page refresh
                },
                error: function (xhr) {
                    alert("Error updating user: " + xhr.responseJSON.message);
                }
            });
        };

        // Close Modal and Reset Form
        window.closeModal = function () {
            var myModalEl = document.getElementById('addUserModal');
            var modal = bootstrap.Modal.getInstance(myModalEl);
            modal.hide();
            $('#addUserForm')[0].reset();
        };
    });
</script>

@endsection
