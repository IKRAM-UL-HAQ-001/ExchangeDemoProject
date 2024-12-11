@extends("layout.main")
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-warning shadow-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                        <p style="color: black;"><strong>Opening Closing Balance Table (Weekly Bases)</strong></p>
                        <div>
                            <a href="{{ route('export.openCloseBalance') }}" class="btn btn-dark">Export Opening Closing Balance List</a>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2 px-3">
                    <div class="table-responsive p-0">
                        <table id="openingClosingBalanceTable" class="table align-items-center mb-0 table-striped table-hover px-2">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Opening Balance </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Closing Balance</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Total Balance</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Remarks</th>
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
                                                $balance =  $openingClosingBalance->close_balance; 
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
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
});
</script>

@endsection
