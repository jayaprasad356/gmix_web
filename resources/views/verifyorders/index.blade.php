@extends('layouts.admin')

@section('title', 'Verify Orders Management')
@section('content-header', 'Verify Orders Management')
@section('content-actions')
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-8 d-flex align-items-center">
                
            <div class="col-md-8 d-flex align-items-center">
                <!-- Checkbox for Select All -->
                <div class="form-check mr-3">
                    <input type="checkbox" class="form-check-input" id="checkAll">
                    <label class="form-check-label" for="checkAll">Select All</label>
                </div>
                <!-- Verify Button -->
                <button class="btn btn-primary mr-3" id="verifyButton">verify</button>
             </div>
            </div>
            
            <div class="col-md-4 text-md-right mt-3 mt-md-0">
                <!-- Search Form -->
                <form id="search-form" class="form-inline">
                    <div class="input-group">
                        <input type="text" id="search-input" name="search" class="form-control" placeholder="Search by...." autocomplete="off" value="{{ request()->input('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-outline-secondary" style="display: none;">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <div class="table-responsive" style="overflow-x: auto;">
             <table class="table table-bordered table-hover">
               <thead class="thead-dark">
                    <tr>
                    <th><input type="checkbox" id="checkAll"></th>
                    <th>Actions</th>
                        <th>ID <i class="fas fa-sort"></i></th>
                        <th>Payment Image<i class="fas fa-sort"></i></th>
                        <th>Status <i class="fas fa-sort"></i></th>
                        <th>Ship Rocket <i class="fas fa-sort"></i></th>
                        <th>Ordered Date <i class="fas fa-sort"></i></th>
                        <th>User Mobile <i class="fas fa-sort"></i></th>
                        <th>Staff Name <i class="fas fa-sort"></i></th>
                        <th>Door No <i class="fas fa-sort"></i></th>
                        <th>Street Name <i class="fas fa-sort"></i></th>
                        <th>City <i class="fas fa-sort"></i></th>
                        <th>Pincode <i class="fas fa-sort"></i></th>
                        <th>State <i class="fas fa-sort"></i></th>
                        <th>Landmark <i class="fas fa-sort"></i></th>
                        <th>First Name <i class="fas fa-sort"></i></th>
                        <th>Last Name <i class="fas fa-sort"></i></th>
                        <th>Product Name <i class="fas fa-sort"></i></th>
                        <th>Price <i class="fas fa-sort"></i></th>
                        <th>Delivery Charges <i class="fas fa-sort"></i></th>
                        <th>Total Price <i class="fas fa-sort"></i></th>
                        <th>Quantity <i class="fas fa-sort"></i></th>
                        <th>Payment Mode <i class="fas fa-sort"></i></th>
                        <th>Attempt 1 <i class="fas fa-sort"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                    <tr>
                    <td><input type="checkbox" class="checkbox" data-id="{{ $order->id }}"></td>
                    <td>
                            <a href="{{ route('orders.edit', $order) }}" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                            <button class="btn btn-danger btn-delete" data-url="{{route('verifyorders.destroy', $order)}}"><i class="fas fa-trash"></i></button>
                        </td>
                        <td>{{ $order->id }}</td>
                        <td>
                            @if(!empty($order->payment_image))
                                @if(Str::startsWith($order->payment_image, 'upload/images/'))
                                <a href="https://gmixstaff.graymatterworks.com/{{ $order->payment_image }}" data-lightbox="image-{{ $order->id }}">
                                        <img class="customer-img img-thumbnail img-fluid" src="https://gmixstaff.graymatterworks.com/{{ $order->payment_image }}" alt="Payment Image" style="max-width: 100px; max-height: 100px;">
                                    </a>
                                @else
                                    <!-- Otherwise, use the asset path -->
                                    <a href="{{ asset('storage/app/public/orders/' . $order->payment_image) }}" data-lightbox="image-{{ $order->id }}">
                                        <img class="customer-img img-thumbnail img-fluid" src="{{ asset('storage/app/public/orders/' . $order->payment_image) }}" alt="Payment Image" style="max-width: 100px; max-height: 100px;">
                                    </a>
                                @endif
                            @endif
                        </td>
                        <td>
                            @if ($order->status === 0)
                                <span class="badge badge-primary">Wait For Confirmation</span>
                            @elseif ($order->status === 1)
                                <span class="badge badge-success">Confirmed</span>
                            @elseif ($order->status === 2)
                                <span class="badge badge-danger">Cancelled</span>
                            @elseif ($order->status === 3)
                                <span class="badge badge-info">Shipped</span>
                            @elseif ($order->status === 4)
                                <span class="badge badge-secondary">Delivered</span>
                            @elseif ($order->status === 5)
                                <span class="badge badge-warning">COD Not-Verified</span>
                            @elseif ($order->status === 6)
                                <span class="badge badge-warning">Payment Not-Verified</span>
                            @endif
                        </td>
                        <td>
                            @if ($order->ship_rocket === 0)
                                <span class="badge badge-primary">pending</span>
                            @elseif ($order->ship_rocket === 1)
                                <span class="badge badge-success">Confirmed</span>
                            @endif
                        </td>
                        <td>{{ $order->ordered_date }}</td> 
                        <td>{{ optional($order->user)->mobile }}</td>
                        <td>{{ $order->staffs ? $order->staffs->name : 'N/A' }}</td> <!-- Display the staff name -->
                        <td>{{ optional($order->addresses)->door_no }}</td>
                        <td>{{ optional($order->addresses)->street_name }}</td>
                        <td>{{ optional($order->addresses)->city }}</td>
                        <td>{{ optional($order->addresses)->pincode }}</td>
                        <td>{{ optional($order->addresses)->state }}</td>
                        <td>{{ optional($order->addresses)->landmark }}</td>
                        <td>{{ optional($order->addresses)->first_name }}</td>
                        <td>{{ optional($order->addresses)->last_name }}</td>
                        <td>{{ optional($order->product)->name }}</td>
                        <td>{{ $order->price }}</td>
                        <td>{{ $order->delivery_charges }}</td>
                        <td>{{ $order->total_price }}</td>
                        <td>{{ $order->quantity }}</td>
                        <td>{{ $order->payment_mode }}</td>  
                         <td>{{ $order->attempt1 }}</td> 
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $orders->appends(request()->query())->links() }}
    </div>
</div>

@endsection

@section('js')
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
<script>
   $(document).ready(function () {
    let timeout; // Variable to hold the timeout ID

    // Handle search input
    $('#search-input').on('input', function () {
        clearTimeout(timeout); // Clear the previous timeout
        timeout = setTimeout(function () {
            filterVerifications(); // Call filterVerifications after 3 seconds
        }, 1000); // 3000 milliseconds = 3 seconds
    });

 
    function filterVerifications() {
        let search = $('#search-input').val();
        let url = `{{ route('verifyorders.index') }}?search=${encodeURIComponent(search)}`;
        window.location.href = url; // Redirect to the new URL
    }


        // Handle delete button click
        $(document).on('click', '.btn-delete', function () {
            const $this = $(this);
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to delete this verification?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post($this.data('url'), {_method: 'DELETE', _token: '{{ csrf_token() }}'}, function () {
                        $this.closest('tr').fadeOut(500, function () {
                            $(this).remove();
                        });
                    });
                }
            });
        });

        // Handle table sorting
        $('.table th').click(function () {
            var table = $(this).parents('table').eq(0);
            var index = $(this).index();
            var rows = table.find('tr:gt(0)').toArray().sort(comparer(index));
            this.asc = !this.asc;
            if (!this.asc) {
                rows = rows.reverse();
            }
            for (var i = 0; i < rows.length; i++) {
                table.append(rows[i]);
            }
            updateArrows(table, index, this.asc);
        });

        function comparer(index) {
            return function (a, b) {
                var valA = getCellValue(a, index),
                    valB = getCellValue(b, index);
                return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.localeCompare(valB);
            };
        }

        function getCellValue(row, index) {
            return $(row).children('td').eq(index).text();
        }

        function updateArrows(table, index, asc) {
            table.find('.arrow').remove();
            var arrow = asc ? '<i class="fas fa-arrow-up arrow"></i>' : '<i class="fas fa-arrow-down arrow"></i>';
            table.find('th').eq(index).append(arrow);
        }

        // Handle "Select All" checkbox
        $('#checkAll').change(function() {
            $('.checkbox').prop('checked', $(this).prop('checked'));
        });

        // Handle Verify Button click
        $('#verifyButton').click(function() {
            var orderIds = [];
            $('.checkbox:checked').each(function() {
                orderIds.push($(this).data('id'));
            });

            if (orderIds.length > 0) {
                // AJAX call to backend
                $.ajax({
                    url: "{{ route('verifyorders.verify') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        order_ids: orderIds
                    },
                    success: function(response) {
                        // Handle success response
                        alert('Verified successfully!');
                        location.reload(); // Reload the page or update UI as needed
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        console.error(error);
                        alert('Error updating orders. Please try again.');
                    }
                });
            } else {
                alert('Please select at least one orders.');
            }
        });
    });
</script>
@endsection
