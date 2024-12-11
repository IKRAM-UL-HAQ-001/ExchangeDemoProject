@extends("layout.main")
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-warning shadow-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                        <p style="color: black;"><strong>Bank Balance Table (Weekly Basis)</strong></p>
                        <div>
                            <a href="{{ route('export.bankBalanceList') }}" class="btn btn-dark">Bank Balance Excel</a>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2 px-3">
                    <div class="table-responsive p-0">
                        <table id="bankBalanceTable" class="table align-items-center mb-0 table-striped table-hover px-2">
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
                                <tr>
                                    <td>{{ $bankBalance->user->name ?? 'N/A' }}</td>
                                    <td>{{ $bankBalance->exchange->name ?? 'N/A' }}</td>
                                    <td>{{ $bankBalance->bank_name ?? 'N/A' }}</td> <!-- Fixed missing closing <td> -->
                                    <td>{{ $bankBalance->account_number }}</td>
                                    <td>{{ $bankBalance->cash_amount }}</td>
                                    <td>{{ $bankBalance->cash_type }}</td>
                                    <td>{{ $bankBalance->remarks }}</td>
                                    <td>{{ $bankBalance->created_at }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm" aria-label="Delete Bank Balance" onclick="deleteBankBalance(this, {{ $bankBalance->id }})">Delete</button>
                                    </td>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        const userTable = $('#bankBalanceTable').DataTable({
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
            pageLength: 10,
            order: [[7, 'desc']] // Ensure this is correct to sort by the "Date and Time" column
        });
    });

    function deleteBankBalance(button, id) {
        const row = $(button).closest('tr');
        const table = $('#bankBalanceTable').DataTable();

        if (!confirm('Are you sure you want to delete this bank balance?')) {
            return;
        }

        $.ajax({
            url: "{{ route('admin.bank_balance.destroy') }}",
            method: "POST",
            data: {
                id: id,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    table.row(row).remove().draw();
                    alert(response.message); // Consider replacing this with a toast notification
                } else {
                    alert(response.message || 'Failed to delete the bank balance.');
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
