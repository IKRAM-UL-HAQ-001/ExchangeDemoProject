@extends('layout.main')
@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div
                            class="bg-gradient-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                            <p style="color: black;"><strong>Deposit - Withdrawal Table (Weekly Basis)</strong></p>
                            <div>
                                 <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#exportdepositModal">Deposit Export</button>
                                 <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#exportWithdrawalModal">Withdrawal Export</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-2 px-3">
                        <div class="table-responsive p-0" style="overflow-y: hidden;">
                            <table id="depositWithdrawal"
                                class="table align-items-center mb-0 table-striped table-hover px-2">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">User</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Exchange</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Reference No.</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Customer</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Phone Number</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Amount</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Type</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Bonus</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Payment</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Balance</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Date and Time</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @php
                                        $balance = 0;
                                    @endphp

                                    @foreach ($depositWithdrawalRecords as $depositWithdrawal)
                                        @php
                                            if ($depositWithdrawal->cash_type == 'deposit') {
                                                $balance += $depositWithdrawal->cash_amount;
                                            } elseif ($depositWithdrawal->cash_type == 'withdrawal') {
                                                $balance -= $depositWithdrawal->cash_amount;
                                            }
                                        @endphp
                                        @if ($depositWithdrawal->cash_type != 'expense')
                                            <tr data-user-id="{{ $depositWithdrawal->id ?? 'N/A' }}"
                                            data-exchange-id="{{ $depositWithdrawal->exchange->id ?? 'N/A' }}">
                                                <td>{{ $depositWithdrawal->user->name }}</td>
                                                <td>{{ $depositWithdrawal->exchange->name }}</td>
                                                <td>{{ $depositWithdrawal->reference_number }}</td>
                                                <td>{{ $depositWithdrawal->customer_name }}</td>
                                                <td>{{ $depositWithdrawal->customer_Phone }}</td>
                                                <td>{{ $depositWithdrawal->cash_amount }}</td>
                                                <td>{{ $depositWithdrawal->cash_type }}</td>
                                                <td>{{ $depositWithdrawal->bonus_amount }}</td>
                                                <td>{{ $depositWithdrawal->payment_type }}</td>
                                                <td>{{ number_format($balance, 2) }}</td>
                                                <td>{{ $depositWithdrawal->created_at }}</td>
                                                <td class="text-center">
                                                    <button class="btn btn-danger btn-sm"
                                                        onclick="deleteDepositWithdrawal(this)">
                                                        Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        {{ $depositWithdrawalRecords->links('pagination::bootstrap-4') }}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<div class="modal fade" id="exportWithdrawalModal" tabindex="-1" aria-labelledby="exportWithdrawalModalLabel" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content" >
            <div class="bg-gradient-primary modal-header d-flex justify-content-between align-items-center" style="background-color:#fb8c00;">
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
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </form>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exportdepositModal" tabindex="-1" aria-labelledby="exportdepositModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="bg-gradient-primary modal-header d-flex justify-content-between align-items-center" style="background-color:#fb8c00;">
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
                    <button type="submit" class="btn btn-primary">
                        Generate File
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function deleteDepositWithdrawal(button) {
        const row = $(button).closest('tr');
        const userId = row.data('user-id');
        if (confirm('Are you sure you want to delete this data?')) {
            $.ajax({
                url: '{{ route('assistant.deposit_withdrawal.destroy') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: userId
                },
                success: function(response) {
                    if (response.success) {
                        row.remove();
                    }
                }
            });
        }
    }
</script>
@endsection
