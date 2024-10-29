@extends("layout.main")
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                        <p style="color: white;"><strong>Customer Table</strong></p>
                        <div>
                        <a href="{{ route('export.customer') }}" class="btn btn-dark">Customer Export Excel</a>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2 px-3">
                    <div class="table-responsive p-0">
                        <table id="customerTable" class="table align-items-center mb-0 table-striped table-hover px-2">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">User</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Exchange</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Name</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cash Amount</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Remarks</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customerRecords as $customer)
                                <tr>
                                    <td>{{ $customer->user->name }}</td>
                                    <td>{{ $customer->exchange->name }}</td>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->cash_amount }}</td>
                                    <td>{{ $customer->remarks }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm" aria-label="Delete Bank Balance" onclick="deleteCustomer(this, {{ $customer->id }})">Delete</button>
                                    </td>
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

    function deleteCustomer(button, id) {
        const row = $(button).closest('tr');
        const table = $('#customerTable').DataTable();

        if (!confirm('Are you sure you want to delete this Customer?')) {
            return;
        }

        $.ajax({
            url: "{{ route('admin.customer.destroy') }}"
            , method: "POST"
            , data: {
                id: id
                , _token: '{{ csrf_token() }}'
            }
            , success: function(response) {
                if (response.success) {
                    table.row(row).remove().draw();
                    alert(response.message); // Consider replacing this with a toast notification
                } else {
                    alert(response.message || 'Failed to delete the bank balance.');
                }
            }
            , error: function(xhr) {
                console.error(xhr.responseText);
                alert('Error: ' + xhr.status + ' - ' + xhr.statusText);
            }
        });
    }

</script>


@endsection

