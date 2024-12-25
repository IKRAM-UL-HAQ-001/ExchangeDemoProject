@extends("layout.main")
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                        <p style="color: black;"><strong>Vender Payment Table</strong></p>
                        <div>
                            <a href="{{ route('export.venderPayment') }}" class="btn btn-dark">Export Vender Payment List</a>
                            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addVenderPaymentModal">Add New Vender Payment</button>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2 px-3">
                    <div class="table-responsive p-0" style="overflow-y: hidden;">
                        <table id="venderPayment" class="table align-items-center mb-0 table-striped table-hover px-2">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Paid Amount</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Remaining Amount</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Payment Type</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Remarks</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Date and Time</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder  ">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($venderPaymentRecords as $venderPayment)
                                    <tr data-user-id="{{ $venderPayment->id ?? 'N/A' }}"
                                    data-exchange-id="{{ $venderPayment->exchange->id ?? 'N/A' }}">
                                        <td>{{ $venderPayment->paid_amount}}</td>
                                        <td>{{ $venderPayment->remaining_amount}}</td>
                                        <td>{{ $venderPayment->payment_type}}</td>
                                        <td>{{ $venderPayment->remarks}}</td>
                                        <td>{{ $venderPayment->created_at}}</td>
                                        <td class="text-center">
                                            <button class="btn btn-danger btn-sm" onclick="deleteVenderPayment(this)">Delete</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $venderPaymentRecords->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addVenderPaymentModal" tabindex="-1" aria-labelledby="addVenderPaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="bg-gradient-primary modal-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title" id="addVenderPaymentModalLabel" style="color:white">Add New Vender Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addVenderPaymentForm" method="post" action="">
                        <div class="mb-3">
                            <label for="paid_amount" class="form-label">Paid Amount</label>
                            <input type="text" class="form-control border px-3" id="paid_amount" name="paid_amount" placeholder="Enter Paid Amount" required>
                        </div>
                        <div class="mb-3">
                            <label for="remaining_amount" class="form-label">Remaining Amount</label>
                            <input type="text" class="form-control border px-3" id="remaining_amount" name="remaining_amount" placeholder="Enter Remaining Amount" required>
                        </div>
                        <div class="mb-3">
                            <label for="payment_type" class="form-label">Payment Type</label>
                            <select class="form-select px-3" id="payment_type" required >
                                <option value="" disabled selected>Select an Payment Type</option>
                                <option value="rent"> Rent</option>
                                <option value="salary"> salary</option>
                                <option value="patty_cash"> Patty Cash</option>
                                <option value="bank_payment"> Bank Payment</option>
                                <option value="master_payment"> Master Payment</option>
                                <option value="general_expense"> General Expense</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <input type="text" class="form-control border px-3" id="remarks" name="remarks" placeholder="Enter Remarks" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="addVenderPayment()">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

        function addVenderPayment() {
            const paid_amount = document.getElementById('paid_amount').value;
            const remaining_amount = document.getElementById('remaining_amount').value;
            const payment_type = document.getElementById('payment_type').value;
            const remarks = document.getElementById('remarks').value;

            $.ajax({
                url: "{{ route('assistant.vender_payment.store') }}",
                method: "POST",
                data: {
                    paid_amount: paid_amount,
                    remaining_amount: remaining_amount,
                    payment_type: payment_type,
                    remarks: remarks,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.message) {
                        alert(response.message);
                        location.reload();
                        closeModal();
                    }

                    $('#addVenderPaymentModal').modal('hide');
                    document.getElementById('addVenderPaymentForm').reset();
                },
                error: function(xhr) {
                    alert('Error: ' + (xhr.responseJSON.message || 'An error occurred while adding the vender payment.'));
                }
            });
        }
        function deleteVenderPayment(button) {
            const row = $(button).closest('tr');
            const userId = row.data('user-id');

            if (confirm('Are you sure you want to delete this user?')) {
                $.ajax({
                    url: '{{ route('assistant.vender_payment.destroy') }}',
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
</script>

@endsection
