@extends("layout.main")
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                        <p style="color: black;"><strong>Expense Table (Weekly Bases)</strong></p>
                        <div>
                        <a href="{{ route('export.expense') }}" class="btn btn-dark">Expense Export</a>                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2 px-3">
                    <div class="table-responsive p-0" style="overflow-y: hidden;">
                        <table id="expense" class="table align-items-center mb-0 table-striped table-hover px-2">
                            <thead>
                                <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">User </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Exchange </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Amount</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Type</th>
                                    {{-- <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Remarks</th> --}}
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Balance</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Date and Time</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder  ">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $balance = 0;
                                @endphp

                                @foreach($expenseRecords as $expense)
                                    @php
                                        if ($expense->cash_type == 'deposit') {
                                            $balance += $expense->cash_amount;
                                        } elseif ($expense->cash_type == 'withdrawal') {
                                            $balance -= $expense->cash_amount;
                                        }elseif ($expense->cash_type == 'expense') {
                                            $balance -= $expense->cash_amount;
                                        }
                                    @endphp
                                @if($expense->cash_type == "expense")
                                    <tr>
                                        <td>{{ $expense->user->name }}</td>
                                        <td>{{ $expense->exchange->name }}</td>
                                        <td>{{ $expense->cash_amount }}</td>
                                        <td>{{ $expense->cash_type }}</td>
                                        {{-- <td>{{ $expense->remarks }}</td> --}}
                                        <td>{{ $balance}}</td>
                                        <td>{{ $expense->created_at}}</td>
                                        <td class="text-center">
                                            <button class="btn btn-danger btn-sm" onclick="deleteExpense(this, {{ $expense->id }})">Delete</button>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                        {{ $expenseRecords->links('pagination::bootstrap-4') }}

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function deleteExpense(button, id) {
    const row = $(button).parents('tr');
    const table = $('#expenseTable').DataTable();

    if (!confirm('Are you sure you want to delete this Expense?')) {
        return;
    }

    $.ajax({
        url: "{{ route('assistant.expense.destroy') }}",
        method: "POST",
        data: {
            id: id,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                table.row(row).remove().draw();
            }
        }
    });
}
</script>

@endsection
