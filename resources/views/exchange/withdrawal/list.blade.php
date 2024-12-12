@extends("layout.main")
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-success shadow-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                        <p style="color: black;"><strong>Withdrawal Table (Weekly Bases)</strong></p>
                        <div>
                            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#exportWithdrawalModal">Withdrawal Export</button>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2 px-3">
                    <div class="table-responsive p-0">
                        <table id="withdrawalTable" class="table align-items-center mb-0 table-striped table-hover px-2">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">User </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Exchange </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Customer Name</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Amount</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Cash Type</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Remarks</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Balance</th>
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
                                        <td>{{ $withdrawal->remarks }}</td>
                                        <td>{{ number_format($balance, 2) }}</td>
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
                    <button type="submit" class="btn btn-warning"> Generate File </button>
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
        const userTable = $('#withdrawalTable').DataTable({
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
</script>
@endsection