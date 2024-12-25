@extends("layout.main")
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary  border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                        <p style="color: black;"><strong>Master Settling Table  (Yearly Bases)</strong></p>
                        <div>
                            <a href="{{ route('export.masterSettlingListWeekly') }}" class="btn btn-dark">Weekly Master Settling Excel</a>
                            <a href="{{ route('export.masterSettlingListMonthly') }}" class="btn btn-dark">Monthly Master Settling Excel</a>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2 px-3">
                    <div class="table-responsive p-0" style="overflow-y: hidden;">
                        <table id="masterSettling" class="table align-items-center mb-0 table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">User</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Exchange</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">White Label</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Credit Reff</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Settling Point</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Price</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Total Amount</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Date and Time</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder  ">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($masterSettlingRecords as $masterSettling)
                                <tr   data-user-id="{{ $masterSettling->id ?? 'N/A' }}">
                                    <td>{{ $masterSettling->user->name }}</td>
                                    <td>{{ $masterSettling->exchange->name }}</td>
                                    <td>{{ $masterSettling->white_label }}</td>
                                    <td>{{ $masterSettling->credit_reff }}</td>
                                    <td>{{ $masterSettling->settling_point }}</td>
                                    <td>{{ $masterSettling->price }}</td>
                                    <td>{{ $masterSettling->settling_point * $masterSettling->price }}</td>
                                    <td>{{ $masterSettling->created_at }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm" aria-label="Delete Master Settling" onclick="deleteOwnerProfit(this)">Delete</button>
                                        <button class="btn btn-primary btn-sm" aria-label="Edit Master Settling" onclick="openEditModal({{ json_encode($masterSettling) }})">Edit</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $masterSettlingRecords->links('pagination::bootstrap-4') }}

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="bg-gradient-primary modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Master Settling</h5>
                <button type="button" class="close" aria-label="Close" onclick="resetEditFormAndCloseModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="alertMessage" class="alert d-none" role="alert"></div>
                <form id="editForm">
                    <input type="hidden" id="editId" name="id">
                    <div class="form-group">
                        <label for="editWhiteLabel">White Label</label>
                        <input type="text" class="form-control" id="editWhiteLabel" name="white_label" required>
                    </div>
                    <div class="form-group">
                        <label for="editCreditReff">Credit Reff</label>
                        <input type="text" class="form-control" id="editCreditReff" name="credit_reff" required>
                    </div>
                    <div class="form-group">
                        <label for="editSettlingPoint">Settling Point</label>
                        <input type="text" class="form-control" id="editSettlingPoint" name="settling_point" required>
                    </div>
                    <div class="form-group">
                        <label for="editPrice">Price</label>
                        <input type="text" class="form-control" id="editPrice" name="price" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="resetEditForm()">Close</button>
                <button type="button" class="btn btn-primary" onclick="updateMasterSettling()">Save changes</button>
            </div>
        </div>
    </div>
</div>
<style>
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>

function deleteOwnerProfit(button) {
            const row = $(button).closest('tr');
            const userId = row.data('user-id');

            if (confirm('Are you sure you want to delete this user?')) {
                $.ajax({
                    url: '{{ route('admin.master_settling.destroy') }}',
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
                        alert('Failed to delete bank.');
                    }
                });
            }
        }


    function openEditModal(masterSettling) {
        $('#editId').val(masterSettling.id);
        $('#editWhiteLabel').val(masterSettling.white_label);
        $('#editCreditReff').val(masterSettling.credit_reff);
        $('#editSettlingPoint').val(masterSettling.settling_point);
        $('#editPrice').val(masterSettling.price);
        $('#editModal').modal('show');
    }

    function updateMasterSettling() {
        const data = {
            id: $('#editId').val(),
            white_label: $('#editWhiteLabel').val(),
            credit_reff: $('#editCreditReff').val(),
            settling_point: $('#editSettlingPoint').val(),
            price: $('#editPrice').val(),
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: "{{ route('admin.master_settling.update') }}",
            method: "POST",
            data: data,
            success: function(response) {
                $('#alertMessage').removeClass('d-none').removeClass('alert-danger').addClass('alert-success');
                $('#alertMessage').text(response.message).show();

                if (response.success) {
                    setTimeout(() => {
                        $('#editModal').modal('hide');
                        location.reload(); // Reload to reflect changes
                    }, 3000);
                } else {
                    // Show error message
                    $('#alertMessage').removeClass('alert-success').addClass('alert-danger');
                }
            },
            error: function(xhr) {
                $('#alertMessage').removeClass('d-none').removeClass('alert-success').addClass('alert-danger');
                $('#alertMessage').text('Error: ' + (xhr.responseJSON?.message || 'Please fill in all fields.')).show();
                setTimeout(() => {
                    $('#alertMessage').addClass('d-none');
                }, 3000);
            }
        });
    }

    function resetEditForm() {
        $('#editForm')[0].reset(); // Reset the form
        $('#alertMessage').addClass('d-none'); // Hide alert message
        $('#editModal').modal('hide'); // Close the modal
    }
    function resetEditFormAndCloseModal() {
        resetEditForm();
        $('#editModal').modal('hide');
    }
</script>
@endsection
