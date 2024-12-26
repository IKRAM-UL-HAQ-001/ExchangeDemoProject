@extends("layout.main")
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                        <p style="color: black;"><strong>Bank Balance Table (Weekly Basis)</strong></p>
                        <div>
                            <a href="{{ route('export.bankBalanceList') }}" class="btn btn-dark">Bank Balance Excel</a>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2 px-3">
                    <div class="table-responsive p-0">
                        <table id="" class="table align-items-center mb-0 table-striped table-hover px-2">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">User</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Exchange</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Bank</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Account No.</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Amount</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Type</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Remarks</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Date and Time</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bankBalanceRecords as $bankBalance)
                                    @if($bankBalance->status !='freez')
                                        <tr data-user-id="{{ $bankBalance->id ?? 'N/A' }}">
                                            <td>{{ $bankBalance->user->name ?? 'N/A' }}</td>
                                            <td>{{ $bankBalance->exchange->name ?? 'N/A' }}</td>
                                            <td>{{ $bankBalance->bank->name ?? 'N/A' }}</td> <!-- Fixed missing closing <td> -->
                                            <td>{{ $bankBalance->account_number }}</td>
                                            <td>{{ $bankBalance->cash_amount }}</td>
                                            <td>{{ $bankBalance->cash_type }}</td>
                                            <td>{{ $bankBalance->remarks }}</td>
                                            <td>{{ $bankBalance->created_at }}</td>
                                            <td class="text-center">
                                                <button class="btn btn-danger btn-sm" aria-label="Delete Bank Balance" onclick="deleteBank(this, {{ $bankBalance->id }})">Delete</button>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                        {{ $bankBalanceRecords->links('pagination::bootstrap-4') }}

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function deleteBank(button) {
            const row = $(button).closest('tr');
            const userId = row.data('user-id');

            if (confirm('Are you sure you want to delete this user?')) {
                $.ajax({
                    url: '{{ route('admin.bank_balance.destroy') }}',
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
</script>
@endsection
