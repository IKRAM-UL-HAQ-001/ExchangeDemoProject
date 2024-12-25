@extends("layout.main")
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                        <p style="color: black;"><strong>Withdrawal Table (Weekly Bases)</strong></p>
                        <div>
                            <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#exportWithdrawalModal">Withdrawal Export</button>
                            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#cashTransactionModal">Add New Transaction</button>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2 px-3">
                    <div class="table-responsive p-0">
                        <table id="" class="table align-items-center mb-0 table-striped table-hover px-2">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">User Name </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Exchange </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Customer Name</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Amount</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Cash Type</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Bank Name</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Balance</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Created At</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $balance = 0;
                                @endphp

                                @foreach($withdrawalRecords as $withdrawal)
    
                                @php
                                        if ($withdrawal->cash_type == 'deposit') {
                                            $balance += $withdrawal->cash_amount;
                                        } elseif ($withdrawal->cash_type == 'withdrawal') {
                                            $balance -= $withdrawal->cash_amount;
                                        }
                                    @endphp
                                    @if(!in_array($withdrawal->cash_type, ['expense', 'deposit']))
                                    <tr>
                                        <td>{{ $withdrawal->user->name }}</td>
                                        <td>{{ $withdrawal->exchange->name }}</td>
                                        <td>{{ $withdrawal->customer_name }}</td>
                                        <td>{{ $withdrawal->cash_amount }}</td>
                                        <td>{{ $withdrawal->cash_type }}</td>
                                        <td>{{ $withdrawal->payment_type }}</td>
                                        <td>{{ number_format($balance, 2) }}</td>
                                        <td>{{ $withdrawal->created_at }}</td>
                                        <td>
                                            @if(!$withdrawal->approval)
                                                <form method="post" action="{{route('exchange.withdrawal.approval')}}">
                                                    @csrf
                                                    <input type="hidden" id= "approval" name="approval" value="1" readonly>
                                                    <input type="hidden" id= "id" name="id" value="{{$withdrawal->id}}" readonly>
                                                    <input type="submit" class="btn btn-warning" value=" approve">
                                                </form>
                                            @else
                                                <button class="btn btn-primary text-white" >Approved</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                        {{ $withdrawalRecords->links('pagination::bootstrap-4') }}
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
                            <div class="form-group" hidden>
                                <label class="form-label" for="cash_type">Cash Type<span class="text-danger">*</span></label>
                                <select class="form-control border px-3" id="cash_type" name="cash_type" required>
                                    <option value="withdrawal" readonly selected>Withdrawal</option>
                                </select>
                                @error('cash_type')
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
                            <div class="form-group" id="cash_amount">
                                <label for="cash_amount" class="form-label">Amount<span class="text-danger">*</span></label>
                                <input type="text" class="form-control border" name="cash_amount" placeholder="Enter Cash Amount" required>
                                @error('cash_amount')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group" id="payment_type">
                                <label class="form-label">Bank Name<span class="text-danger">*</span></label>
                                <select class="form-select px-3" name="payment_type" id="payment_type" required >
                                    <option value="" disabled selected>Select an Bank</option>
                                    @foreach($bankRecords as $bank)
                                        <option value="{{ $bank->name }}">{{ $bank->name }}</option>
                                    @endforeach
                                </select>
                                @error('payment_type')
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
<div class="modal fade" id="exportWithdrawalModal" tabindex="-1" aria-labelledby="exportWithdrawalModalLabel" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content" >
            <div class="modal-header d-flex justify-content-between align-items-center" style="background-color:#fb8c00;">
                <h5 class="modal-title" id="exportWithdrawalModalLabel" style=" color:white">Withdrawal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reportForm" action="{{ route('export.withdrawal') }}" method="POST">
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
                    <button type="submit" class="btn btn-primary"> Generate File </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </form>

            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
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
</script>
@endsection
