@extends("layout.main")
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-warning shadow-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                        <p style="color: black;"><strong>Cash Transactions</strong></p>
                        <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#cashTransactionModal">Add New Transaction</button>
                    </div>
                </div>
                <div class="card-body px-0 pb-2 px-3">
                    <div class="table-responsive p-0">
                        <table id="cashTable" class="table align-items-center mb-0 table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Cash Type</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Amount</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Total Balance</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Date and Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $balance = 0;
                            @endphp
                                @foreach($cashRecords as $cash)
                                    @php
                                        if ($cash->cash_type == 'deposit') {
                                            $balance += $cash->cash_amount;
                                        } elseif ($cash->cash_type == 'withdrawal') {
                                            $balance -= $cash->cash_amount;
                                        } elseif ($cash->cash_type == 'expense') {
                                            $balance -= $cash->cash_amount;
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ ucfirst($cash->cash_type) }}</td>
                                        <td>{{ number_format($cash->cash_amount, 2) }}</td>
                                        <td >{{$balance}}</td>
                                        <td>{{ $cash->created_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cash Transaction Modal -->
    <div class="modal fade" id="cashTransactionModal" tabindex="-1" aria-labelledby="cashTransactionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="cashTransactionModalLabel">Cash Transaction Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeModalButton"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success text-white" id='success' style="display:none;">
                        {{ session('success') }}
                    </div>
                    <div class="alert alert-danger text-white" id='error' style="display:none;">
                        {{ session('error') }}
                    </div>
                    <form id="cashForm" action="{{ route('exchange.cash.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="form-group">
                                <label class="form-label" for="cash_type">Cash Type<span class="text-danger">*</span></label>
                                <select class="form-control border px-3" id="cash_type" name="cash_type" required>
                                    <option disabled selected>Choose Cash Type</option>
                                    <option value="deposit" {{ old('cash_type') == 'deposit' ? 'selected' : '' }}>Deposit</option>
                                    <option value="withdrawal" {{ old('cash_type') == 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                                    <option value="expense" {{ old('cash_type') == 'expense' ? 'selected' : '' }}>Expense</option>
                                </select>
                                @error('cash_type')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group" id="reference_number" style="display: none;">
                                <label class="form-label" for="reference_number">Reference Number<span class="text-danger">*</span></label>
                                <input type="text" class="form-control border" name="reference_number" placeholder="Enter Reference Number" >
                                @error('reference_number')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group" id="customer_name" style="display: none;">
                                <label class="form-label" for="customer_name">Customer Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control border" name="customer_name" placeholder="Enter Customer Name">
                                @error('customer_name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group" id="cash_amount">
                                <label for="cash_amount" class="form-label">Amount<span class="text-danger">*</span></label>
                                <input type="text" class="form-control border" name="cash_amount" placeholder="Enter Cash Amount" required>
                                @error('cash_amount')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group" id="bonus-amount-field" style="display: none;">
                                <label class="form-label" for="bonus_amount">Bonus Amount <span class="text-pink">(optional)</span></label>
                                <input type="text" class="form-control border" name="bonus_amount" placeholder="Enter Bonus Amount if any">
                                @error('bonus_amount')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group" id="payment-type-field" style="display: none;">
                                <label class="form-label">Payment Type<span class="text-danger">*</span></label>
                                <div class="row">
                                    @foreach(['google_pay', 'phone_pay', 'imps', 'neft', 'i20_pay'] as $index => $payment)
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_type" id="payment_{{ $payment }}" value="{{ $payment }}">
                                            <label class="form-check-label" for="payment_{{ $payment }}">
                                                {{ ucfirst(str_replace('_', ' ', $payment)) }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @error('payment_type')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group" id="remarks">
                                <label class="form-label" for="remarks">Remarks <span class="text-pink">(optional)</span></label>
                                <input type="text" class="form-control border" name="remarks" placeholder="Enter Remarks if any">
                                @error('remarks')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                        <div class="form-group row mb-3 col-lg-12 mt-2 ">
                            <div class="col-lg-12 ml-auto pt-3 d-flex flex-row gap-3 justify-content-end">
                                <button type="button" class=" btn btn-dark" data-bs-dismiss="modal" aria-label="Close" id="closeModalButton">Close</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
    $('#cashTable').DataTable({
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

    const cashTypeSelect = $('#cash_type');
    const cashForm = $('#cashForm');

    function toggleFields() {
        const cashType = cashTypeSelect.val();

        // Hide all optional fields by default
        $('#reference_number').hide();
        $('#customer_name').hide();
        $('#bonus-amount-field').hide();
        $('#payment-type-field').hide();
        $('#remarks').hide();
        $('#cash_amount').hide();
        
        // Show fields conditionally based on selected cash type
        if (cashType === 'deposit') {
            $('#reference_number').show();
            $('#customer_name').show();
            $('#bonus-amount-field').show();
            $('#payment-type-field').show();
        } else if (cashType === 'withdrawal') {
            $('#customer_name').show();
        } 

        // Remarks should be shown for all types except the default
        if (cashType) {
            $('#remarks').show();
            $('#cash_amount').show();
        }
    }

    // Call the function initially and on every change
    toggleFields();
    cashTypeSelect.on('change', toggleFields);

    // Handle form submission
    cashForm.on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        $.ajax({
            url: cashForm.attr('action'),
            type: 'POST',
            data: cashForm.serialize(),
            success: function(response) {
                if (response.success) {
                    $('#error').hide();
                    $('#success').text(response.message).show();
                    cashForm[0].reset();
                    setTimeout(() => {
                        $('#success').hide();
                        }, 2000);
                } else {
                    $('#error').text(response.message).show();
                    $('#success').hide();
                    setTimeout(() => {
                        $('#error').hide();
                        }, 2000);
                }
            },
            error: function(xhr) {
                const errorMessage = xhr.responseJSON?.message || 'An unexpected error occurred!';
                $('#error').text(errorMessage).show();
                $('#success').hide();
                setTimeout(() => {
                        $('#error').hide();
                        }, 2000);
            }
        });
    });

    $('#closeModalButton').on('click', function() {
        cashForm[0].reset();
        location.reload();
    });
});

</script>

@endsection
