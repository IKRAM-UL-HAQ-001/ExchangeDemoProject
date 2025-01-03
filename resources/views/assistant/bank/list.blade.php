@extends("layout.main")
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                        <p style="color: black;"><strong>Bank Table</strong></p>
                        <div>
                            <a href="{{ route('export.bank') }}" class="btn btn-dark">Export Bank List</a>
                            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addBankModal">Add New Bank</button>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2 px-3">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 table-striped table-hover px-2">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Bank Name</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Date and Time</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bankRecords as $bank)
                                    <tr data-bank_id="{{ $bank->id }}">
                                        <td>{{ $bank->name }}</td>
                                        <td>{{ $bank->created_at }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-danger btn-sm" onclick="deleteBank(this)">Delete</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $bankRecords->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addBankModal" tabindex="-1" aria-labelledby="addBankModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="bg-gradient-primary modal-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title" id="addBankModalLabel" style="color:white">Add New Bank</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success text-white" id="success" style="display:none;"></div>
                    <div class="alert alert-danger text-white" id="error" style="display:none;"></div>
                    <form id="addBankForm">
                        <div class="mb-3">
                            <label for="name" class="form-label">Bank Name</label>
                            <input type="text" class="form-control border px-3" id="name" name="name" placeholder="Enter Bank Name" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="addBank()">Save Bank</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function addBank() {
    const name = $('#name').val();

    $.ajax({
        url: "{{ route('assistant.bank.store') }}",
        method: "POST",
        data: {
            name: name,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                $('#success').text(response.message).show();
                $('#error').hide();
                $('#addBankModal').modal('hide');
                $('#addBankForm')[0].reset();
                setTimeout(() => window.location.reload(), 1500);
            }
        },
        error: function(xhr) {
            $('#success').hide();
            $('#error').text(xhr.responseJSON.message || 'An error occurred while adding the bank.').show();
        }
    });
}

function deleteBank(button) {
    const row = $(button).closest('tr');
    const bankId = row.data('bank_id');

    if (confirm('Are you sure you want to delete this bank?')) {
        $.ajax({
            url: '{{ route('assistant.bank.destroy') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: bankId
            },
            success: function(response) {
                if (response.success) {
                    row.remove();
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert(xhr.responseJSON.message || 'An error occurred while deleting the bank.');
            }
        });
    }
}
</script>
@endsection
