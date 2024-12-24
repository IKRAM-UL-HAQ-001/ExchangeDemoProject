@extends('layout.main')
@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div
                            class="bg-gradient-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                            <p style="color: black;"><strong>Excel Files Table</strong></p>
                            <div>
                                <button type="button" class="btn btn-light" data-bs-toggle="modal"
                                    data-bs-target="#uploadExcelModal">Upload Excel File</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-2 px-3">
                        <div class="table-responsive p-0">
                            <table id="excelTable" class="table align-items-center mb-0 table-striped table-hover px-2">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Customer Name</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Customer Phone</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Exchange</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Upload Date
                                            and Time</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($excelFiles as $file)
                                        <tr>
                                            <td>{{ $file->customer_name }}</td>
                                            <td>{{ $file->customer_phone }}</td>
                                            <td>{{ $file->exchange->name }}</td>
                                            <td>{{ $file->created_at }}</td>
                                            <td class="text-center">
                                                <button class="btn btn-danger btn-sm"
                                                    onclick="deleteExcelFile(this, {{ $file->id }})">Delete</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $excelFiles->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="uploadExcelModal" tabindex="-1" aria-labelledby="uploadExcelModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="bg-gradient-primary modal-header d-flex justify-content-between align-items-center">
                        <h5 class="modal-title" id="uploadExcelModalLabel" style="color:white">Upload Excel File</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success text-white" id='success' style="display:none;">
                            {{ session('success') }}
                        </div>
                        <div class="alert alert-danger text-white" id='error' style="display:none;">
                            {{ session('error') }}
                        </div>
                        <form id="uploadExcelForm" method="post" action="{{ route('admin.file.post') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <select class="form-control" id="exchange" name="exchange_id">
                                    <option value="" disabled selected>Select Your Exchange</option>
                                    @foreach ($exchangeRecords as $exchange)
                                        <option value="{{ $exchange->id ?? 'N/A' }}">{{ $exchange->name ?? 'N/A' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="excel_file" class="form-label">Excel File</label>
                                <input type="file" class="form-control border px-3" id="excel_file" name="excel_file"
                                    accept=".xlsx, .xls, .csv" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Upload File</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            setTimeout(() => {
                $('#success').fadeOut('slow');
                $('#error').fadeOut('slow');
            }, 3000);
        });


        function deleteExcelFile(button, id) {
            $.ajax({
                url: `{{ route('admin.file.destroy') }}`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id
                },
                success: function(response) {
                    $(button).closest('tr').remove();
                    alert(response.message);
                },
                error: function(error) {
                    alert('Error deleting file.');
                }
            });
        }
    </script>
@endsection
