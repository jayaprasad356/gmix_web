@extends('layouts.admin')

@section('title', 'Orders Management')
@section('content-header', 'Orders Management')
@section('content-actions')
    <a href="{{ route('orders.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> Add New Orders</a>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <!-- Action Buttons and Search -->
        <div class="row mb-4">
            <!-- Left side (Checkbox and Buttons) -->
            <div class="col-lg-8 col-md-12 d-flex align-items-center flex-wrap">
                <!-- Checkbox for Select All -->
                <div class="form-check mr-3 mb-2 mb-lg-0">
                    <input type="checkbox" class="form-check-input" id="checkAll">
                    <label class="form-check-label" for="checkAll">Select All</label>
                </div>
                
                <!-- Status Filter Dropdown -->
                <div class="form-group mb-2 mb-lg-4">
                    <label for="status-select">Select Status:</label>
                    <select name="status" id="status-select" class="form-control">
                        <option value="0">Wait For Confirmation</option>
                        <option value="1">Confirmed</option>
                        <option value="2" >Cancelled</option>
                        <option value="3">Shipped</option>
                        <option value="4">Delivered</option>
                    </select>
                </div>
                
                <!-- Status Buttons -->
                <div class="d-flex flex-wrap align-items-center mt-2 pl-lg-4">
                    <button class="btn btn-success mr-2 mb-2 mb-lg-0" id="verifyButton">Approved</button>
                </div>
            </div>
            
            <!-- Right side (Search) -->
            <div class="col-lg-4 col-md-12 mt-3 mt-lg-0">
                <!-- Search Form -->
                <form action="{{ route('orders.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" id="search-input" name="search" class="form-control" placeholder="Search by..." autocomplete="off" value="{{ request()->input('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit" style="display: none;">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-md-12">
            <form id="filter-form" action="{{ route('orders.index') }}" method="GET">
                <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="status-filter">Filter by Status:</label>
                    <select name="status" id="status-filter" class="form-control">
                        <option value="">All</option>
                        <option value="0" {{ request()->input('status') === '0' ? 'selected' : '' }}>Wait For Confirmation</option>
                        <option value="1" {{ request()->input('status') === '1' ? 'selected' : '' }}>Confirmed</option>
                        <option value="2" {{ request()->input('status') === '2' ? 'selected' : '' }}>Cancelled</option>
                        <option value="3" {{ request()->input('status') === '3' ? 'selected' : '' }}>Shipped</option>
                        <option value="4" {{ request()->input('status') === '4' ? 'selected' : '' }}>Delivered</option>
                    </select>
                </div>
                </div>
            </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                    <th><input type="checkbox" id="checkAll"></th>
                    <th>Actions</th>
                        <th>ID <i class="fas fa-sort"></i></th>
                        <th>First Name <i class="fas fa-sort"></i></th>
                        <th>Last Name <i class="fas fa-sort"></i></th>
                        <th>Product Name <i class="fas fa-sort"></i></th>
                        <th>Price <i class="fas fa-sort"></i></th>
                        <th>Delivery Charges <i class="fas fa-sort"></i></th>
                        <th>Total Price <i class="fas fa-sort"></i></th>
                        <th>Payment Mode <i class="fas fa-sort"></i></th>
                        <th>Door No <i class="fas fa-sort"></i></th>
                        <th>Street Name <i class="fas fa-sort"></i></th>
                        <th>City <i class="fas fa-sort"></i></th>
                        <th>Pincode <i class="fas fa-sort"></i></th>
                        <th>State <i class="fas fa-sort"></i></th>
                        <th>Landmark <i class="fas fa-sort"></i></th>
                        <th>Status <i class="fas fa-sort"></i></th>
                        <th>Ship Rocket <i class="fas fa-sort"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                    <tr>
                    <td><input type="checkbox" class="checkbox" data-id="{{ $order->id }}"></td>
                    <td>
                            <a href="{{ route('orders.edit', $order) }}" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                        </td>
                        <td>{{ $order->id }}</td>
                        <td>{{ optional($order->addresses)->first_name }}</td>
                        <td>{{ optional($order->addresses)->last_name }}</td>
                        <td>{{ optional($order->product)->name }}</td>
                        <td>{{ $order->price }}</td>
                        <td>{{ $order->delivery_charges }}</td>
                        <td>{{ $order->total_price }}</td>
                        <td>{{ $order->payment_mode }}</td>
                        <td>{{ optional($order->addresses)->door_no }}</td>
                        <td>{{ optional($order->addresses)->street_name }}</td>
                        <td>{{ optional($order->addresses)->city }}</td>
                        <td>{{ optional($order->addresses)->pincode }}</td>
                        <td>{{ optional($order->addresses)->state }}</td>
                        <td>{{ optional($order->addresses)->landmark }}</td>
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
                            @endif
                        </td>
                        <td>
                            @if ($order->ship_rocket === 0)
                                <span class="badge badge-primary">pending</span>
                            @elseif ($order->ship_rocket === 1)
                                <span class="badge badge-success">Confirmed</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </table>
        </div>
       
        {{ $orders->appends(request()->query())->links() }}

    </div>
</div>

@endsection

@section('js')
    <script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
      $(document).ready(function () {
        // Function to get URL parameters
        function getQueryParams() {
            const params = {};
            window.location.search.substring(1).split("&").forEach(function (pair) {
                const [key, value] = pair.split("=");
                params[key] = decodeURIComponent(value);
            });
            return params;
        }

        // Load initial parameters
        const queryParams = getQueryParams();
        $('#search-input').val(queryParams.search || '');
        $('#status-filter').val(queryParams.status || '');

        // Handle search input
        $('#search-input').on('input', function () {
            filterUsers();
        });

        // Handle status filter change
        $('#status-filter').change(function () {
            filterUsers();
        });

        let debounceTimer;

        function filterUsers() {
            clearTimeout(debounceTimer);

            debounceTimer = setTimeout(function() {
                let search = $('#search-input').val();
                let status = $('#status-filter').val();

                window.location.search = `search=${encodeURIComponent(search)}&status=${encodeURIComponent(status)}`;
            }, 500); // Adjust the delay (in milliseconds) as needed
        }
       
        // Handle delete button click
        $(document).on('click', '.btn-delete', function () {
            $this = $(this);
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Are you sure?',
                text: "Do you really want to delete this user?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.post($this.data('url'), {_method: 'DELETE', _token: '{{csrf_token()}}'}, function (res) {
                        $this.closest('tr').fadeOut(500, function () {
                            $(this).remove();
                        })
                    })
                }
            })
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
            // Update arrows
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
        $('#checkAll').change(function () {
            $('.checkbox').prop('checked', $(this).prop('checked'));
        });

        $('#verifyButton').click(function () {
            verifySelectedOrders();
        });

        function verifySelectedOrders() {
            var orderIds = [];
            $('.checkbox:checked').each(function () {
                orderIds.push($(this).data('id'));
            });

            if (orderIds.length === 0) {
                alert('Please select at least one order to update status.');
                return;
            }

            // Get the selected status from the dropdown
            var selectedStatus = $('#status-select').val();

            $.ajax({
                url: '{{ route("orders.verify") }}',
                type: 'POST',
                data: {
                    order_ids: orderIds,
                    status: selectedStatus,  // Send selected status
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.success) {
                        location.reload(); // Reload the page to reflect changes
                    } else {
                        alert('Failed to update status. Please try again.');
                    }
                },
                error: function () {
                    alert('Failed to update status. Please try again.');
                }
            });
        }
    });
</script>
@endsection