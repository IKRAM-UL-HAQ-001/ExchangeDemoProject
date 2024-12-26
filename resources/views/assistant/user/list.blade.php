@extends('layout.main')
@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                            <p style="color: black;"><strong>User Table</strong></p>
                            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addUserModal">Add New User</button>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-2 px-3">
                        <div class="table-responsive p-0" style="overflow-y: hidden;">
                            <table id="user" class="table align-items-center mb-0 table-striped table-hover px-2">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary font-weight-bolder text-dark">User Name</th>
                                        <th class="text-uppercase text-secondary font-weight-bolder text-dark ps-2">Exchange Name</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Date and Time</th>
                                        <th class="text-center text-uppercase text-secondary font-weight-bolder text-dark">Permission</th>
                                        <th class="text-center text-uppercase text-secondary font-weight-bolder text-dark">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($userRecords as $user)
                                        <tr data-user-id="{{ $user->id }}" data-exchange-id="{{ $user->exchange->id ?? 'N/A' }}">
                                            <td>{{ $user->name ?? 'N/A' }}</td>
                                            <td>{{ $user->exchange->name ?? 'N/A' }}</td>
                                            <td>{{ $user->created_at->format('Y-m-d H:i:s') ?? 'N/A' }}</td>
                                            <td class="text-center">
                                                <form action="{{ route('assistant.user.status') }}" method="POST" class="toggle-form d-flex justify-content-center">
                                                    @csrf
                                                    <input type="hidden" name="userId" value="{{ $user->id }}">
                                                    <input type="checkbox" id="checkbox-{{ $user->id }}" name="status_toggle"
                                                        {{ $user->status === 'active' ? 'checked' : '' }}
                                                        onchange="toggleStatus(this)">
                                                    <label for="checkbox-{{ $user->id }}" class="button bg-white">
                                                        <div class="dot"></div>
                                                    </label>
                                                </form>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-danger btn-sm" onclick="deleteUser(this)">Delete</button>
                                                <button class="btn btn-primary btn-sm" onclick="editUser(this)">Edit</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $userRecords->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add User Modal -->
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="bg-gradient-primary modal-header d-flex justify-content-between align-items-center">
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
                                <label for="type" class="form-label">User Type</label>
                                <select class="form-select px-3" id="type" required>
                                    <option disabled selected>Select User Type</option>
                                    <option value="deposit">Deposit</option>
                                    <option value="withdrawal">Withdrawal</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password <small class="text-danger">Password must be 8 digits.</small></label>
                                <input type="password" class="form-control border px-3" id="password" placeholder="Enter Password" required>
                            </div>
                            <div class="mb-3">
                                <label for="exchange" class="form-label">Exchange</label>
                                <select class="form-select px-3" id="exchange" required>
                                    <option value="" disabled selected>Select an exchange</option>
                                    @foreach ($exchangeRecords as $exchange)
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
                    <div class="bg-gradient-primary modal-header d-flex justify-content-between align-items-center">
                        <h5 class="modal-title" id="editUserModalLabel" style="color:white">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editUserForm">
                            @csrf
                            <input type="hidden" id="editUserId">
                            <div class="mb-3">
                                <label for="editName" class="form-label">User Name</label>
                                <input type="text" class="form-control border px-3" id="editName" placeholder="Enter User Name" required>
                            </div>
                            <div class="mb-3">
                                <label for="editType" class="form-label">User Type</label>
                                <select class="form-select px-3" id="editType" required>
                                    <option disabled selected>Select User Type</option>
                                    <option value="deposit">Deposit</option>
                                    <option value="withdrawal">Withdrawal</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="editPassword" class="form-label">Password</label>
                                <input type="password" class="form-control border px-3" id="editPassword" placeholder="Enter Password">
                            </div>
                            <div class="mb-3">
                                <label for="editExchange" class="form-label">Exchange</label>
                                <select class="form-select px-3" id="editExchange" required>
                                    <option value="" disabled selected>Select an exchange</option>
                                    @foreach ($exchangeRecords as $exchange)
                                        <option value="{{ $exchange->id }}">{{ $exchange->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="saveUserChanges()">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function addUser() {
            const name = $('#name').val();
            const password = $('#password').val();
            const exchange = $('#exchange').val();
            const type = $('#type').val();

            $.ajax({
                url: '{{ route('assistant.user.post') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    name: name,
                    type: type,
                    password: password,
                    exchange: exchange
                },
                success: function(response) {
                    if (response.message === "User added successfully!") {
                        $('#addUserModal').modal('hide');
                        window.location.reload();
                    }
                },
                error: function() {
                    alert('Error adding user!');
                }
            });
        }

        function deleteUser(button) {
            const row = $(button).closest('tr');
            const userId = row.data('user-id');

            if (confirm('Are you sure you want to delete this user?')) {
                $.ajax({
                    url: '{{ route('assistant.user.destroy') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: userId
                    },
                    success: function(response) {
                        if (response.success) {
                            row.remove();
                        }
                    },
                    error: function() {
                        alert('Failed to delete user.');
                    }
                });
            }
        }

        function toggleStatus(checkbox) {
            const userId = $(checkbox).closest('form').find('input[name="userId"]').val();
            const status = checkbox.checked ? 'active' : 'inactive';

            $.ajax({
                url: '{{ route('assistant.user.status') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    userId: userId,
                    status: status
                },
                success: function(response) {
                    if (!response.success) {
                        alert('Status updated.');
                    }
                },
                error: function() {
                    alert('Error updating status.');
                }
            });
        }

        function editUser(button) {
            const row = $(button).closest('tr');
            const userId = row.data('user-id');
            const userName = row.find('td:nth-child(1)').text();
            const exchangeId = row.data('exchange-id');
            const type = "deposit"; // Default value, adjust logic based on your data.

            $('#editUserId').val(userId);
            $('#editName').val(userName.trim());
            $('#editExchange').val(exchangeId);
            $('#editType').val(type);

            $('#editUserModal').modal('show');
        }

        function saveUserChanges() {
            const userId = $('#editUserId').val();
            const name = $('#editName').val();
            const type = $('#editType').val();
            const exchange = $('#editExchange').val();
            const password = $('#editPassword').val();

            $.ajax({
                url: '{{ route('assistant.user.update') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: userId,
                    name: name,
                    type: type,
                    exchange: exchange,
                    password: password
                },
                success: function(response) {
                    if (response.message) { // Check if message exists
                        const modal = new bootstrap.Modal(document.getElementById('editUserModal')); // Get modal instance
                        modal.hide(); // Close the modal
                        alert(response.message); // Show success or error message
                        window.location.reload(); // Reload page to reflect changes
                    } else {
                        alert('Failed to update user.'); // Show error message if no message in response
                    }
                },
                error: function() {
                    alert('An error occurred while updating the user.');
                }
            });
        }

    </script>
@endsection
