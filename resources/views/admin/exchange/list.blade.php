@extends("layout.main")
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                        <p style="color: black;"><strong>Exchange Table</strong></p>
                        <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addExchangeModal">Add New Exchange</button>
                    </div>
                </div>
                <div class="card-body px-0 pb-2 px-3">
                    <div class="table-responsive p-0">
                        <table id="exchange" class="table align-items-center mb-0 table-striped table-hover px-2">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary font-weight-bolder">Exchange Name</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Date and Time</th>
                                    <th class="text-center text-uppercase text-secondary font-weight-bolder">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($exchangeRecords as $exchange)
                                <tr data-user-id="{{ $exchange->id ?? 'N/A' }}">
                                    <td>{{ $exchange->name }}</td>
                                    <td>{{ $exchange->created_at }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm" onclick="deleteExchange(this, {{ $exchange->id }})">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $exchangeRecords->links('pagination::bootstrap-4') }}

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addExchangeModal" tabindex="-1" aria-labelledby="addExchangeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="bg-gradient-primary modal-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title" id="addExchangeModalLabel" style="color:white">Add New Exchange</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addExchangeForm">
                        <div class="mb-3">
                            <label for="name" class="form-label">Exchange Name</label>
                            <input type="text" class="form-control border px-3" id="name" name="name" placeholder="Enter Exchange Name" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="addExchange()">Save Exchange</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

function addExchange() {
    const name = document.getElementById('name').value;

    $.ajax({
        url: "{{ route('admin.exchange.store') }}",
        method: "POST",
        data: {
            name: name,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.message) {
                alert(response.message);
                closeModal();
            }

            $('#addExchangeModal').modal('hide');
            document.getElementById('addExchangeForm').reset();
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            alert('Error: ' + xhr.status + ' - ' + xhr.statusText);
        }
    });
}


function deleteExchange(button) {
            const row = $(button).closest('tr');
            const userId = row.data('user-id');

            if (confirm('Are you sure you want to delete this user?')) {
                $.ajax({
                    url: '{{ route('admin.exchange.destroy') }}',
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
