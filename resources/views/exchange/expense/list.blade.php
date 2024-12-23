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
                            <a href="{{ route('export.expense') }}" class="btn btn-dark">Expense Export</a>
                            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#cashTransactionModal">Add New Transaction</button>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2 px-3">
                    <div class="table-responsive p-0">
                        <table id="expense" class="table align-items-center mb-0 table-striped table-hover px-2">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">User</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Exchange</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Cash Amount</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Cash Type</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Total Balance</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Created At</th>
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
                                        <td>{{ $expense->user->name}}</td>
                                        <td>{{ $expense->exchange->name}}</td>
                                        <td>{{ $expense->cash_amount}}</td>
                                        <td>{{ $expense->cash_type}}</td>
                                        <td>{{ $balance}}</td>
                                        <td>{{ $expense->created_at}}</td>
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
    <!-- Cash Transaction Modal -->
    <div class="modal fade" id="cashTransactionModal" tabindex="-1" aria-labelledby="cashTransactionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class=" bg-gradient-primary modal-header">
                    <h5 class="modal-title text-white" id="cashTransactionModalLabel">Cash Transaction Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeModalButton"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success text-white" id='success' style="display:none;">
                        {{ session('success') }}
                    </div>
                    <div class="alert alert-danger text-white" id='error' style="display:none;">
                        {{ session('error') }}
                    </div>
                    <form id="cashForm" action="{{ route('exchange.cash.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="form-group" hidden>
                                <label class="form-label" for="cash_type">Cash Type<span class="text-danger">*</span></label>
                                    <select class="form-control border px-3" id="cash_type" name="cash_type" required>
                                        <option value="expense" selected readonly>Expense</option>
                                    </select>
                                @error('cash_type')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group" id="cash_amount">
                                <label for="cash_amount" class="form-label">Amount<span class="text-danger">*</span></label>
                                <input type="text" class="form-control border" name="cash_amount" placeholder="Enter Cash Amount" required>
                                @error('cash_amount')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row mb-3 col-lg-12 mt-2 ">
                            <div class="col-lg-12 ml-auto pt-3 d-flex flex-row gap-3 justify-content-end">
                                <button type="button" class=" btn btn-dark" data-bs-dismiss="modal" aria-label="Close" id="closeModalButton">Close</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
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
    const cashForm = $('#cashForm');
    cashForm.on('submit', function(e) {

            $.ajax({
                url: cashForm.attr('action'),
                type: 'POST',
                data: cashForm.serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#error').hide();
                        $('#success').text(response.message).show();
                        cashForm[0].reset();
                        setTimeout(() => {
                            $('#success').hide();
                        }, 2000);
                    } else {
                        $('#error').text(response.message).show();
                        $('#success').hide();
                        setTimeout(() => {
                            $('#error').hide();
                        }, 2000);
                    }
                },
                error: function(xhr) {
                    const errorMessage = xhr.responseJSON?.message || 'An unexpected error occurred!';
                    $('#error').text(errorMessage).show();
                    $('#success').hide();
                    setTimeout(() => {
                        $('#error').hide();
                    }, 2000);
                }
            });
            return false;
        });

        $('#closeModalButton').on('click', function() {
            cashForm[0].reset();
            location.reload();
        });
</script>

@endsection
