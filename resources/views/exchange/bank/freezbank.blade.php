@extends('layout.main')
@section('content')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div
                            class="bg-gradient-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                            <p style="color: black;"><strong>Freez Bank Balance Table (Weekly Bases)</strong></p>
                            <div>
                                <button type="button" class="btn btn-light" data-bs-toggle="modal"
                                    data-bs-target="#addBankModal">Freez Bank Form</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-2 px-3">
                        <div class="table-responsive p-0">
                            <table id="bank" class="table align-items-center mb-0 table-striped table-hover px-2">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Bank Name</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Cash Type</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Cash Amount</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Remarks</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Bank Balance</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Created At</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Action</th>
                                    </tr>
                                </thead>
                                @php
                                    // Create an associative array to store the bank balances and track first occurrences
                                    $bankBalances = [];
                                    $firstEntryFlags = [];
                                @endphp
                                <tbody>
                                    @foreach ($bankEntryRecords as $bankEntry)
                                        @php
                                            if (!isset($bankBalances[$bankEntry->bank_name])) {
                                                $bankBalances[$bankEntry->bank_name] = 0;
                                                $firstEntryFlags[$bankEntry->bank_name] = true; // Mark the first entry
                                            }

                                            // Add or subtract cash based on the cash type
                                            if (strtolower($bankEntry->cash_type) == 'add') {
                                                $bankBalances[$bankEntry->bank_name] += $bankEntry->cash_amount;
                                            } elseif (strtolower($bankEntry->cash_type) == 'minus') {
                                                $bankBalances[$bankEntry->bank_name] -= $bankEntry->cash_amount;
                                            }
                                        @endphp

                                        <tr>
                                            <td>{{ $bankEntry->bank_name }}</td>
                                            <td>{{ $bankEntry->cash_type }}</td>
                                            <td>{{ $bankEntry->cash_amount }}</td>
                                            <td>{{ $bankEntry->remarks }}</td>
                                            <td>
                                                {{-- Show the balance accordingly for the first entry or the updated balance for subsequent entries --}}
                                                @if ($firstEntryFlags[$bankEntry->bank_name])
                                                {{-- For the first occurrence, set the initial balance as the current cash amount --}}
                                                {{ $bankBalances[$bankEntry->bank_name] }}
                                                @php
                                                        $firstEntryFlags[$bankEntry->bank_name] = false; // Mark the first occurrence as processed
                                                        @endphp
                                                @else
                                                {{-- Display the updated bank balance for subsequent entries --}}
                                                {{ $bankBalances[$bankEntry->bank_name] }}
                                                @endif
                                            </td>
                                            <td>{{ $bankEntry->created_at }}</td>
                                            <td>
                                                <form action="{{ route('exchange.bank.un-freeze') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $bankEntry->id }}">
                                                    <button type="submit" class="btn btn-danger">UnFreez</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $bankEntryRecords->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="addBankModal" tabindex="-1" aria-labelledby="addBankModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="bg-gradient-primary modal-header d-flex justify-content-between align-items-center">
                        <h5 class="modal-title" id="addBankModalLabel" style="color:white">Add Bank Balance</h5>
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
                            <form id="bankForm" method="post" action="{{ route('exchange.bank.store') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="bank_name" class="form-label">Bank Name<span
                                                class="text-danger">*</span></label>
                                        <select class="js-select2 form-control border" id="bank_name" name="bank_name"
                                            required>
                                            <option disabled selected>Choose Bank Name</option>
                                            @foreach ($bankRecords as $bank)
                                                <option value="{{ $bank->name }}">{{ $bank->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="bank_balance" class="form-label">
                                            Bank Balance
                                            <small class="text-danger">readonly</small>
                                        </label>
                                        <input type="text" class="form-control border" id="bank_balance"
                                            name="bank_balance" readonly>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="cash_amount" class="form-label">Cash Amount<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control border" id="cash_amount"
                                            name="cash_amount" placeholder="Enter Cash Amount"
                                            value="{{ old('cash_amount') }}" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="remarks" class="form-label">Remarks<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control border" id="remarks" name="remarks"
                                            placeholder="Enter Remarks" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                        id="closeModalButton">Close</button>
                                    <button type="submit" class="btn btn-primary" id="submitBankEntry">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {

            $('#bank_name').change(function() {
                var bankName = $(this).val();

                $('#bankForm input, #bankForm select').prop('disabled', true);

                $.ajax({
                    url: '{{ route('exchange.bank.post') }}',
                    type: 'POST',
                    data: {
                        bank_name: bankName,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.balance !== undefined) {
                            $('#bank_balance').val(response.balance);
                        } else {
                            console.warn('Balance not found in response:', response);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                    },
                    complete: function() {
                        $('#bankForm input, #bankForm select').prop('disabled', false);
                    }
                });
            });

            // Submit Bank Entry
            $('#submitBankEntry').click(function(event) {
                event.preventDefault();

                const accountNumber = $('#account_number').val(); // Ensure this is correctly defined
                const bankName = $('#bank_name').val();
                const freez = 'freez';
                const cashAmount = $('#cash_amount').val();
                const cashType = "minus";
                const remarks = $('#remarks').val();

                $.ajax({
                    url: "{{ route('exchange.bank.store') }}",
                    method: "POST",
                    data: {
                        account_number: accountNumber,
                        bank_name: bankName,
                        cash_amount: cashAmount,
                        freez: freez,
                        cash_type: cashType,
                        remarks: remarks,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.message) {
                            $('#error').hide();
                            $('#success').text(response.message).show();
                            $('#bankForm')[0].reset(); // Reset the form
                            setTimeout(() => {
                                $('#success').hide();
                                $('#addBankModal').modal(
                                'hide'); // Close modal after success
                            }, 2000);
                            location.reload(); // Reload to update the table
                        } else {
                            $('#success').hide();
                            $('#error').text(response.message).show();
                        }
                    },
                    error: function(xhr) {
                        const errorMessage = xhr.responseJSON?.message ||
                            'An unexpected error occurred!';
                        $('#error').text(errorMessage).show();
                        $('#success').hide();
                        setTimeout(() => {
                            $('#error').hide();
                        }, 2000);
                    }
                });
            });
            $('#closeModalButton').on('click', function() {
                $('#bankForm')[0].reset();
                location.reload();
            });
        });
    </script>

    <style>
        .form-control.border {
            border: 1px solid #007bff;
            border-radius: 0.25rem;
        }
    </style>
@endsection
