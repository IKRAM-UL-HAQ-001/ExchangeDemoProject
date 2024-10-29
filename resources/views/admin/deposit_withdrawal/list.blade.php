@extends("layout.main")
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                        <p style="color: white;"><strong>Deposit - Withdrawal Table</strong></p>
                        <div>
                        <a href="{{ route('export.deposit') }}" class="btn btn-dark">Deposit Export</a>
                        <a href="{{ route('export.withdrawal') }}" class="btn btn-dark">Withdrawal Export</a>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2 px-3">
                    <div class="table-responsive p-0">
                        <table id="depositWithdrawalTable" class="table align-items-center mb-0 table-striped table-hover px-2">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">User </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Exchange </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Reference No.</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Customer </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Amount</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Type</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Bonus</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Payment</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Remarks</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Balance</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $balance = 0;
                                @endphp

                                @foreach($depositWithdrawalRecords as $depositWithdrawal)
                                    @php
                                        if ($depositWithdrawal->cash_type == 'deposit') {
                                            $balance += $depositWithdrawal->cash_amount;
                                        } elseif ($depositWithdrawal->cash_type == 'withdrawal') {
                                            $balance -= $depositWithdrawal->cash_amount;
                                        }
                                    @endphp
                                @if($depositWithdrawal->cash_type !="expense")
                                    <tr>
                                        <td>{{ $depositWithdrawal->user->name }}</td>
                                        <td>{{ $depositWithdrawal->exchange->name }}</td>
                                        <td>{{ $depositWithdrawal->reference_number }}</td>
                                        <td>{{ $depositWithdrawal->customer_name }}</td>
                                        <td>{{ $depositWithdrawal->cash_amount }}</td>
                                        <td>{{ $depositWithdrawal->cash_type }}</td>
                                        <td>{{ $depositWithdrawal->bonus_amount }}</td>
                                        <td>{{ $depositWithdrawal->payment_type }}</td>
                                        <td>{{ $depositWithdrawal->remarks }}</td>
                                        <td>{{ number_format($balance, 2) }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-danger btn-sm" 
                                                    onclick="deleteDepositWithdrawal(this, {{ $depositWithdrawal->id }})">
                                                Delete
                                            </button>
                                        </td>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
        const userTable = $('#depositWithdrawalTable').DataTable({
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

function deleteDepositWithdrawal(button, id) {
    const row = $(button).parents('tr');
    const table = $('#depositWithdrawalTable').DataTable();

    if (!confirm('Are you sure you want to delete this deposit/Withdrawal?')) {
        return;
    }

    $.ajax({
        url: "{{ route('admin.deposit_withdrawal.destroy') }}",
        method: "POST",
        data: {
            id: id,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                table.row(row).remove().draw();
                alert(response.message);
            } else {
                alert(response.message || 'Failed to delete the Deposit/Withdrawal.');
            }
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            alert('Error: ' + xhr.status + ' - ' + xhr.statusText);
        }
    });
}
</script>
@endsection
