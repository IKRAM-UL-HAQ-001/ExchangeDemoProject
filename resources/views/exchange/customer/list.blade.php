@extends("layout.main")
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                        <p style="color: white;"><strong>Customer Table</strong></p>
                        <div>
                            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addCustomerModal">Customer Form</button>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2 px-3">
                    <div class="table-responsive p-0">
                        <table id="customerTable" class="table align-items-center mb-0 table-striped table-hover px-2">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Exchange</th>
                                    <th>Reference Number</th>
                                    <th>Customer Name</th>
                                    <th>Amount</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customerRecords as $customer)
                                <tr>
                                    <td>{{ $customer->user->name }}</td>
                                    <td>{{ $customer->exchange->name }}</td>
                                    <td>{{ $customer->reference_number }}</td>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->cash_amount }}</td>
                                    <td>{{ $customer->remarks }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title" id="addCustomerModalLabel" style="color:white">Add Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-validation">
                    <div class="alert alert-success text-white" id='success' style="display:none;">
                        {{ session('success') }}
                    </div>
                    <div class="alert alert-danger text-white" id='error' style="display:none;">
                        {{ session('error') }}
                    </div>
                    <form id="customerForm" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="reference_number" class="form-label">Reference Number</label>
                                <input type="text" class="form-control border" id="reference_number" name="reference_number" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Customer Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control border" id="name" name="name" placeholder="Enter Customer Name" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cash_amount" class="form-label">Cash Amount<span class="text-danger">*</span></label>
                                <input type="text" class="form-control border" id="cash_amount" name="cash_amount" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="remarks" class="form-label">Remarks<span class="text-danger">*</span></label>
                                <input type="text" class="form-control border" id="remarks" name="remarks" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="closeModalButton">Close</button>
                            <button type="button" class="btn btn-primary" id="submitCustomerEntry">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        // Ensure you include DataTables CSS as well
        const userTable = $('#customerTable').DataTable({
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
    
        $('#submitCustomerEntry').click(function(event) {
            event.preventDefault();
    
            const referenceNumber = $('#reference_number').val();
            const name = $('#name').val();
            const cashAmount = $('#cash_amount').val();
            const remarks = $('#remarks').val();
    
            $.ajax({
                url: "{{ route('exchange.customer.store') }}",
                method: "POST",
                data: {
                    reference_number: referenceNumber,
                    name: name,
                    cash_amount: cashAmount,
                    remarks: remarks,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.message) {
                        $('#error').hide();
                        $('#success').text(response.message).show();
                        $('#customerForm')[0].reset();
                        setTimeout(() => {
                            $('#error').hide();
                        }, 2000);
                        location.reload();
                    }else {
                        // Handle errors returned from server
                        $('#success').hide();
                        $('#error').text(response.message).show();
                        setTimeout(() => {
                            $('#error').hide();
                        }, 2000);
                        
                    }
                },
                error: function(xhr) {
                    $('#success').hide();
                    $('#error').text(response.message).show();
                    setTimeout(() => {
                        $('#error').hide();
                    }, 2000);
                }
            });
        });
    });
    $('#closeModalButton').on('click', function() {
        cashForm[0].reset();
        location.reload();
    });
    </script>
    
    @endsection
    