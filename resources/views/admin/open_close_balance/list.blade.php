@extends("layout.main")
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary  border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                        <p style="color: black;"><strong>Opening Closing Balance Table (Weekly Bases)</strong></p>
                        <div>
                            <a href="{{ route('export.openCloseBalance') }}" class="btn btn-dark">Export Opening Closing Balance List</a>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2 px-3">
                    <div class="table-responsive p-0">
                        <table id="openingClosingBalance" class="table align-items-center mb-0 table-striped table-hover px-2">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Opening Balance</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Remarks</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Date and Time</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($openingClosingBalanceRecords as $openingClosingBalance)
                                    <tr>
                                        <td>{{ $openingClosingBalance->open_balance }}</td>
                                        <td>{{ $openingClosingBalance->remarks }}</td>
                                        <td>{{ $openingClosingBalance->created_at}}</td>
                                        <td class="text-center">
                                            <button class="btn btn-danger btn-sm" onclick="deleteOpeningClosingBalance(this, {{ $openingClosingBalance->id }})">Delete</button>
                                        </td>                                       
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $openingClosingBalanceRecords->links('pagination::bootstrap-4') }}

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

function deleteOpeningClosingBalance(button, id) {
    const row = $(button).parents('tr');
    const table = $('#openingClosingBalanceTable').DataTable();

    if (!confirm('Are you sure you want to delete this opening closing balance?')) {
        return;
    }

    $.ajax({
        url: "{{ route('admin.open_close_balance.destroy') }}",
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
                alert(response.message || 'Failed to delete the opening closing balance.');
            }
        },
        error: function(xhr) {
            alert('Error: ' + (xhr.responseJSON.message || 'An error occurred while deleting the opening closing balance.'));
        }
    });
}
</script>
@endsection
