@extends("layout.main")
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-warning shadow-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                        <p style="color: black;"><strong>Master Settling Table  (Yearly Bases)</strong></p>
                        <div>
                        <a href="{{ route('export.masterSettlingListWeekly') }}" class="btn btn-dark">Weekly Master Settling Excel</a>
                        <a href="{{ route('export.masterSettlingListMonthly')}}" class="btn btn-dark">Monthly Master Settling Excel</a>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2 px-3">
                    <div class="table-responsive p-0">
                        <table id="customerTable" class="table align-items-center mb-0 table-striped table-hover px-2">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">User</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Exchange</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">White Label</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Credit Reff</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Settling Point</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Price</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Total Amount</th>
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
                                    <td>{{ $masterSettling->settling_point * $masterSettling->price }}</td>
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
        const userTable = $('#customerTable').DataTable({
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

