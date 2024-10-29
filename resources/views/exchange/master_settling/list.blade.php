@extends("layout.main")
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                        <p style="color: white;"><strong>Master Settling Table</strong></p>
                        <div>
                            <a href="{{ route('export.masterSettlingListWeekly') }}" class="btn btn-dark">Weekly Master Settling Excel</a>
                            <a href="{{ route('export.masterSettlingListMonthly') }}" class="btn btn-dark">Monthly Master Settling Excel</a>
                            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addMasterSettlingModal">Master Settling Form</button>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2 px-3">
                    <div class="table-responsive p-0">
                        <table id="masterSettlingTable" class="table align-items-center mb-0 table-striped table-hover px-2">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">User</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Exchange</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">White Label</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Credit Reff</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Settling Point</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Price</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($masterSettlingRecords as $masterSettling)
                                <tr>
                                    <td>{{ $masterSettling->user->name }}</td>
                                    <td>{{ $masterSettling->exchange->name }}</td>
                                    <td>{{ $masterSettling->white_label }}</td>
                                    <td>{{ $masterSettling->credit_reff }}</td>
                                    <td>{{ $masterSettling->settling_point }}</td>
                                    <td>{{ $masterSettling->price }}</td>
                                    <td>{{ $masterSettling->total_amount }}</td>
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

<div class="modal fade" id="addMasterSettlingModal" tabindex="-1" aria-labelledby="addMasterSettlingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title" id="addMasterSettlingModalLabel" style="color:white">Add Master Settling Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-validation">
                    <div class="alert alert-success text-white" id='success' style="display:none;">
                        {{ session('success') }}
                    </div>
                    <div class="alert alert-danger text-white" id='error' style="display:none;">
                        {{ session('error') }}
                    </div>
                    <form id="masterSettlingForm" method="post">
                        @csrf
                        <div class="col-md-12 mb-3">
                            <label for="white_label" class="form-label">White Label<span class="text-danger">*</span></label>
                            <input type="text" class="form-control border" id="white_label" name="white_label" placeholder="Enter White Label" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="credit_reff" class="form-label">Credit Reff<span class="text-danger">*</span></label>
                            <input type="text" class="form-control border" id="credit_reff" name="credit_reff" placeholder="Enter Credit Reff" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="settling_point" class="form-label">Settling Point<span class="text-danger">*</span></label>
                            <input type="text" class="form-control border" id="settling_point" name="settling_point" placeholder="Enter Settling Point" required>
                        </div>
                        <div class="col-md-12 mb-3">    
                            <label for="price" class="form-label">Price<span class="text-danger">*</span></label>
                            <input type="number" class="form-control border" id="price" name="price" placeholder="Enter Price" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" -d="closeModalButton">Close</button>
                            <button type="button" class="btn btn-primary" id="submitMasterSettlingEntry">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        const userTable = $('#masterSettlingTable').DataTable({
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
            pageLength: 10
        });
    });

    $('#submitMasterSettlingEntry').click(function(event) {
        event.preventDefault(); 
        const whiteLabel = $('#white_label').val();
        const creditReff = $('#credit_reff').val();
        const settlingPoint = $('#settling_point').val();
        const price = $('#price').val();
        $.ajax({
            url: "{{ route('exchange.master_settling.store') }}",
            method: "POST",
            data: {
                white_label: whiteLabel,
                credit_reff: creditReff,
                settling_point: settlingPoint,
                price: price,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.message) {
                    $('#error').hide();
                    $('#success').text(response.message).show();
                    $('#masterSettlingForm')[0].reset(); // Reset the form
                    setTimeout(() => {
                        $('#success').hide();
                        }, 2000);
                        location.reload(); // Reload the DataTable to reflect new data
                }else {
                    // Handle errors returned from server
                    $('#success').hide();
                    $('#error').text(response.message).show();
                }
            },
            error: function(xhr) {
                const errorMessage = xhr.responseJSON?.message;
                $('#error').text('Please Fill The All The Fields').show();
                $('#success').hide();
                setTimeout(() => {
                        $('#error').hide();
                        }, 2000);
            }
        });
    });
    $('#closeModalButton').on('click', function() {
        cashForm[0].reset();
        location.reload();
    });
</script>
@endsection
