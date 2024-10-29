@extends("layout.main")
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                        <p style="color: white;"><strong>Opening Closing Balance Table</strong></p>
                        <div>
                            <a href="{{ route('export.openCloseBalance') }}" class="btn btn-dark">Export Opening Closing Balance List</a>
                            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addOpenCloseBalanceModal">Opening Closing Balance Form</button>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2 px-3">
                    <div class="table-responsive p-0">
                        <table id="openingClosingBalanceTable" class="table align-items-center mb-0 table-striped table-hover px-2">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Opening Balance</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Closing Balance</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Balance</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $balance = 0;
                            @endphp
                                @foreach($openingClosingBalanceRecords as $openingClosingBalance)
                                    <tr>
                                        @if($loop->first)
                                            <td>{{ $openingClosingBalance->open_balance }}</td>
                                            <td>{{ $openingClosingBalance->close_balance }}</td>
                                            @php
                                                $balance =  $openingClosingBalance->open_balance + $openingClosingBalance->close_balance; 
                                            @endphp
                                             <td>{{$balance}}</td>
                                             <td>{{ $openingClosingBalance->remarks }}</td>
                                        @else
                                            <td>{{ $openingClosingBalance->open_balance }}</td>
                                            <td>{{ $openingClosingBalance->close_balance }}</td>
                                            @php
                                                $balance =  $balance + $openingClosingBalance->close_balance; 
                                            @endphp
                                            <td>{{$balance}}</td>
                                            <td>{{ $openingClosingBalance->remarks }}</td>
                                        @endif
                                       
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

<div class="modal fade" id="addOpenCloseBalanceModal" tabindex="-1" aria-labelledby="addOpenCloseBalanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title" id="addOpenCloseBalanceModalLabel" style="color:white">Add Opening Closing Balance</h5>
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
                
                    <form id="OpenCloseBalanceForm" action="{{route('exchange.open_close_balance.store')}}" method="post">
                        @csrf
                        <div class="col-md-12 mb-3">
                            <label for="open_balance" class="form-label">Opening Balance<span class="text-danger">*</span></label>
                            <input type="text" class="form-control border" id="open_balance" name="open_balance" placeholder="Enter Opening Balance" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="close_balance" class="form-label">Closing Balance<span class="text-danger">*</span></label>
                            <input type="text" class="form-control border" id="close_balance" name="close_balance" placeholder="Enter Closing Balance" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="remarks" class="form-label">Remarks<span class="text-danger">*</span></label>
                            <input type="text" class="form-control border" id="remarks" name="remarks" placeholder="Enter Remarks" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
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
$(document).ready(function () {
    const userTable = $('#openingClosingBalanceTable').DataTable({
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

    const openCloseBalanceForm = $('#OpenCloseBalanceForm'); // Correct form selector

    // Submit button click event
    openCloseBalanceForm.on('submit', function (e) {
        e.preventDefault(); // Prevent default button action

        $.ajax({
            url: openCloseBalanceForm.attr('action'), // Use form's action attribute
            type: 'POST',
            data: openCloseBalanceForm.serialize(),
            success: function (response) {
                if (response.success) {
                    // Display success message
                    $('#error').hide();
                    $('#success').text(response.message).show(); 

                    // Reset the form and hide error message
                    openCloseBalanceForm[0].reset(); 
                    setTimeout(() => {
                        $('#success').hide();
                        }, 2000);
                        location.reload();
                    // Close modal after a slight delay for better UX
                } else {
                    // Display the error message from the response
                    $('#success').hide();
                    $('#error').show().text(response.message);
                    setTimeout(() => {
                        $('#error').hide();
                        }, 2000);
                }
            },
            error: function (xhr) {
                // Handle server errors
                const errorMessage = xhr.responseJSON?.message || 'An unexpected error occurred!';
                $('#success').hide();
                $('#error').text(errorMessage).fadeIn().delay(3000).fadeOut();
                setTimeout(() => {
                        $('#error').hide();
                        }, 2000);
            }
        });
    });

    // Reset form and hide messages when the modal is closed
    $('#addOpenCloseBalanceModal').on('hidden.bs.modal', function () {
        openCloseBalanceForm[0].reset(); // Reset form fields
        $('#success, #error').hide(); 
        location.reload();// Hide both messages
    });
});


</script>
@endsection
