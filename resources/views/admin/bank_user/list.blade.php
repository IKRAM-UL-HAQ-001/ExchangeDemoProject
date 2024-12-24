@extends("layout.main")
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                        <p style="color: black;"><strong>Bank User Table</strong></p>
                        <div>
                            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addBankUserModal">Add Bank User</button>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2 px-3">
                    <div class="table-responsive p-0">
                        <table id="bankUser" class="table align-items-center mb-0 table-striped table-hover px-2">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">User Name</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Date and Time</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder  ">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bankUserRecords as $bankUser)
                                    <tr data-user-id="{{ $bankUser->id ?? 'N/A' }}"
                                    data-exchange-id="{{ $bankUser->exchange->id ?? 'N/A' }}">
                                        <td>{{ $bankUser->user->name }}</td>
                                        <td>{{ $bankUser->created_at }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-danger btn-sm" onclick="deleteBankUser(this)">Delete</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $bankUserRecords->links('pagination::bootstrap-4') }}

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addBankUserModal" tabindex="-1" aria-labelledby="addBankUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="bg-gradient-primary modal-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title" id="addBankUserModalLabel" style="color:white">Add New Bank</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addBankUserForm" method="post" action="">
                        <div class="mb-3">
                            <label for="bank_user" class="form-label">person</label>
                            <select class="form-select px-3" id="bank_user" required >
                                <option value="" disabled selected>Select an Bank User</option>
                                @foreach($userRecords as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="addBankUser()">Save Bank User</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

function addBankUser() {
    const bank_user = document.getElementById('bank_user').value;
    $.ajax({
        url: "{{ route('admin.bank_user.store') }}",
        method: "POST",
        data: {
            bank_user : bank_user,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.message) {
                alert(response.message);
                closeModal();
            }

            $('#addBankUserModal').modal('hide');
            document.getElementById('addBankUserForm').reset();
        },
        error: function(xhr) {
            alert('Error: ' + (xhr.responseJSON.message || 'An error occurred while adding the bank.'));
        }
    });
}

function deleteBankUser(button) {
    const row = $(button).closest('tr');
        const userId = row.data('user-id');

        if (confirm('Are you sure you want to delete this bank user?')) {
            $.ajax({
                url: '{{ route('admin.bank_user.destroy') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: userId
                },
                success: function(response) {
                    if (response.success) {
                        row.remove();
                    } else {
                    alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Failed to delete user.');
                }
            });
        }
    }
</script>

@endsection
