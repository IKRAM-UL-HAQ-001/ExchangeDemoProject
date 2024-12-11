@extends("layout.main")
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-warning shadow-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                        <p style="color: black;"><strong>User Table</strong></p>
                        <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addUserModal">Add New User</button>
                    </div>
                </div>
                <div class="card-body px-0 pb-2 px-3">
                    <div class="table-responsive p-0" style="overflow-y: hidden;">
                        <table id="userTable" class="table align-items-center mb-0 table-striped table-hover px-2">
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
                                    <tr data-user-id="{{ $user->id ?? 'N/A' }}" data-exchange-id="{{ $user->exchange->id ?? 'N/A' }}">
                                            <td>{{ $user->name ?? 'N/A' }}</td>
                                            <td>{{ $user->exchange->name ?? 'N/A' }}</td>
                                            <td>{{ $user->created_at ?? 'N/A' }}</td>
                                            <td class="text-center">
                                                <form action="{{ route('admin.user.status') }}" method="POST" class="toggle-form d-flex justify-content-center">
                                                    @csrf
                                                    <input type="hidden" name="userId" value="{{ $user->id }}">
                                                    <input type="hidden" name="status" value="{{ $user->status }}">
                                                    <input type="checkbox" id="checkbox-{{ $user->id }}" name="status_toggle"
                                                           {{ $user->status === 'active' ? 'checked' : '' }} onchange="toggleStatus(this)">
                                                    <label for="checkbox-{{ $user->id }}" class="button bg-white">
                                                        <div class="dot"></div>
                                                    </label>
                                                </form>
                                            </td>
                                        <td class="text-center">
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

    <!-- Modal -->
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
                    <button type="button" class="btn btn-warning" onclick="addUser()">Save User</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
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
            lengthMenu: [1, 10, 25, 50],
            pageLength: 5,
            order: [[2, 'desc']]
        });
});

function addUser() {
    const name = $('#name').val();
    const password = $('#password').val();
    const exchange = $('#exchange').val();

    $.ajax({
        url: '/admin/user/add',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            name: name,
            password: password,
            exchange: exchange
        },
        success: function(response) {
            if (response.success) {
                alert('User added successfully');
                $('#addUserModal').modal('hide');
                location.reload();
            }
        },
        error: function(response) {
            alert('Error adding user!');
        }
    });
}


function deleteUser(button) {
    if (confirm('Are you sure you want to delete this user?')) {
        const row = $(button).closest('tr');
        const userId = row.data('user-id');

        $.ajax({
            url: '/admin/user/delete',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                userId: userId
            },
            success: function(response) {
                if (response.success) {
                    alert('User deleted successfully');
                    row.remove();
                }
            },
            error: function(response) {
                alert('Error deleting user!');
            }
        });
    }
}

function toggleStatus(checkbox) {
    const userId = $(checkbox).closest('form').find('input[name="userId"]').val();
    const status = checkbox.checked ? 'active' : 'inactive';

    $.ajax({
        url: '/admin/user/status',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            userId: userId,
            status: status
        },
        success: function(response) {
            if (response.success) {
                alert('Status updated');
            }
        },
        error: function(response) {
            alert('Error updating status');
        }
    });
}
</script>
@endsection
