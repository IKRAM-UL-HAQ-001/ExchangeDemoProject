@extends("layout.main")
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-success shadow-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                        <p style="color: black;"><strong>Deposit Table (Weekly Bases)</strong></p>
                        <div>
                            <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#exportdepositModal">Deposit Export</button>
                            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#cashTransactionModal">Add New Transaction</button>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2 px-3">
                    <div class="table-responsive p-0">
                        <table id="depositTable" class="table align-items-center mb-0 table-striped table-hover px-2">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary font-weight-bolder">Reference No.</th>
                                    <th class="text-uppercase text-secondary font-weight-bolder">Customer Name</th>
                                    <th class="text-uppercase text-secondary font-weight-bolder">Amount</th>
                                    <th class="text-uppercase text-secondary font-weight-bolder">Cash Type</th>
                                    <th class="text-uppercase text-secondary font-weight-bolder">Bonus</th>
                                    <th class="text-uppercase text-secondary font-weight-bolder">Payment Type</th>
                                    <th class="text-uppercase text-secondary font-weight-bolder">Balance</th>
                                    <th class="text-uppercase text-secondary font-weight-bolder">Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $balance = 0;
                                @endphp

                                @foreach($depositRecords as $deposit)
                                    @php
                                        if ($deposit->cash_type == 'deposit') {
                                            $balance += $deposit->cash_amount;
                                        } elseif ($deposit->cash_type == 'withdrawal') {
                                            $balance -= $deposit->cash_amount;
                                        }
                                    @endphp
                                    @if(!in_array($deposit->cash_type, ['expense', 'withdrawal']))
                                    <tr>
                                        <td>{{ $deposit->reference_number }}</td>
                                        <td>{{ $deposit->customer_name }}</td>
                                        <td>{{ $deposit->cash_amount }}</td>
                                        <td>{{ $deposit->cash_type }}</td>
                                        <td>{{ $deposit->bonus_amount }}</td>
                                        <td>{{ $deposit->payment_type }}</td>
                                        <td>{{ number_format($balance, 2) }}</td>
                                        <td>{{ $deposit->created_at}}</td>
                                    </tr>
                                @endif
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
                            <!-- Column 1 -->
                            <div class="col-md-6">
                                <div class="form-group" hidden>
                                    <label class="form-label" for="cash_type">Cash Type<span class="text-danger">*</span></label>
                                        <select class="form-control border px-3" id="cash_type" name="cash_type" required>
                                            <option value="deposit" readonly selected>Deposit</option>
                                        </select>
                                    @error('cash_type')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group" id="reference_number">
                                    <label class="form-label" for="reference_number">Reference Number<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control border" name="reference_number" placeholder="Enter Reference Number">
                                    @error('reference_number')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group" id="customer_name">
                                    <label class="form-label" for="customer_name">Customer Name<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control border" name="customer_name" placeholder="Enter Customer Name">
                                    @error('customer_name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" id="customer_phone">
                                    <label class="form-label" for="customer_phone">Customer Phone Number<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control border" name="customer_phone" placeholder="Enter Customer Phone Number">
                                    @error('customer_phone')
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
                                <div class="form-group" id="bonus-amount-field">
                                    <label class="form-label" for="bonus_amount">Bonus Amount <span class="text-pink">(optional)</span></label>
                                    <input type="text" class="form-control border" name="bonus_amount" placeholder="Enter Bonus Amount if any">
                                    @error('bonus_amount')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group" id="payment-type-field">
                            <label class="form-label">Payment Type<span class="text-danger">*</span></label>
                            <div class="row">
                                @foreach(['google_pay', 'phone_pay', 'imps', 'neft', 'i20_pay'] as $index => $payment)
                                <div class="col-md-4">
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
                        <div class="form-group row mb-3 col-lg-12 mt-2 ">
                            <div class="col-lg-12 ml-auto pt-3 d-flex flex-row gap-3 justify-content-end">
                                <button type="button" class=" btn btn-dark" data-bs-dismiss="modal" aria-label="Close" id="closeModalButton">Close</button>
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="exportdepositModal" tabindex="-1" aria-labelledby="exportdepositModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center" style="background-color:#fb8c00;">
                <h5 class="modal-title" id="exportWithdrawalModalLabel" style="color:white">Deposit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reportForm" action="{{ route('export.deposit') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="sdate" class="form-label">Start Date:</label>
                        <input type="date" class="form-control border px-3" id="sdate" name="start_date" required
                            value="{{ \Carbon\Carbon::today()->toDateString() }}">
                    </div>
                    <div class="mb-3">
                        <label for="edate" class="form-label">End Date:</label>
                        <input type="date" class="form-control border px-3" id="edate" name="end_date" required
                            value="{{ \Carbon\Carbon::today()->toDateString() }}">
                    </div>
                    <button type="submit" class="btn btn-warning">
                        Generate File
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        const userTable = $('#depositTable').DataTable({
            pagingType: "full_numbers"
            , language: {
                paginate: {
                    first: '«'
                    , last: '»'
                    , next: '›'
                    , previous: '‹'
                }
            }
            , lengthMenu: [1, 10, 25, 50]
            , pageLength: 10
        });
    });
    const cashForm = $('#cashForm');
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
