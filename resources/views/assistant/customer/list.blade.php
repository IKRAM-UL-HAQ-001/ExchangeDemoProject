@extends("layout.main")
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                        <p style="color: black;"><strong>Customer Table</strong></p>
                        <div>
                        <a href="{{ route('export.customer') }}" class="btn btn-dark">Customer Export Excel</a>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2 px-3">
                    <div class="table-responsive p-0">
                        <table id="customer" class="table align-items-center mb-0 table-striped table-hover px-2">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">User</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Exchange</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Name</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Cash Amount</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Remarks</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ">Date and Time</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder  ">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customerRecords as $customer)
                                <tr  data-user-id="{{ $customer->id ?? 'N/A' }}">
                                    <td>{{ $customer->user->name }}</td>
                                    <td>{{ $customer->exchange->name }}</td>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->cash_amount }}</td>
                                    <td>{{ $customer->remarks }}</td>
                                    <td>{{ $customer->created_at }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm" aria-label="Delete Bank Balance" onclick="deleteBank(this, {{ $customer->id }})">Delete</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $customerRecords->links('pagination::bootstrap-4') }}
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

function deleteBank(button) {
            const row = $(button).closest('tr');
            const userId = row.data('user-id');

            if (confirm('Are you sure you want to delete this user?')) {
                $.ajax({
                    url: '{{ route('assistant.customer.destroy') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: userId
                    },
                    success: function(response) {
                        if (response.success) {
                            row.remove();
                        } 
                    }
                });
            }
        }

</script>


@endsection

