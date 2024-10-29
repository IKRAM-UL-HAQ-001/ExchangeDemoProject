@extends("layout.main")
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                        <p style="color: white;"><strong>Bank Balance Table</strong></p>
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
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">User</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Exchange</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Bank</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Account No.</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Amount</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Type</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bankBalanceRecords as $bankBalance)
                                <tr>
                                    <td>{{ $bankBalance->user->name }}</td>
                                    <td>{{ $bankBalance->exchange->name }}</td>
                                    <td>{{ $bankBalance->name }}</td>
                                    <td>{{ $bankBalance->account_number }}</td>
                                    <td>{{ $bankBalance->cash_amount }}</td>
                                    <td>{{ $bankBalance->cash_type }}</td>
                                    <td>{{ $bankBalance->remarks }}</td>
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
            pagingType: "full_numbers"
            , language: {
                paginate: {
                    first: '«'
                    , last: '»'
                    , next: '›'
                    , previous: '‹'
                }
            }
            , lengthMenu: [5, 10, 25, 50]
            , pageLength: 10
        });
    });

</script>
@endsection
