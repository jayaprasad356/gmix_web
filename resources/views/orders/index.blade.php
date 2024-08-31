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
                
                <!-- Status Buttons -->
                <div class="d-flex flex-wrap align-items-center">
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
                                <option value="0" {{ request()->input('status') === '0' ? 'selected' : '' }}>Pending</option>
                                <option value="1" {{ request()->input('status') === '1' ? 'selected' : '' }}>Verified</option>
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
                        <th>ID <i class="fas fa-sort"></i></th>
                        <th>User Name <i class="fas fa-sort"></i></th>
                        <th>Address Name <i class="fas fa-sort"></i></th>
                        <th>Product Name <i class="fas fa-sort"></i></th>
                        <th>Price <i class="fas fa-sort"></i></th>
                        <th>Delivery Charges <i class="fas fa-sort"></i></th>
                        <th>Payment Mode <i class="fas fa-sort"></i></th>
                        <th>Live Tracking <i class="fas fa-sort"></i></th>
                        <th>Status <i class="fas fa-sort"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                    <tr>
                    <td><input type="checkbox" class="checkbox" data-id="{{ $order->id }}"></td>
                        <td>{{ $order->id }}</td>
                        <td>{{ optional($order->user)->name }}</td>
                        <td>{{ optional($order->addresses)->name }}</td>
                        <td>{{ optional($order->product)->name }}</td>
                        <td>{{ $order->price }}</td>
                        <td>{{ $order->delivery_charges }}</td>
                        <td>{{ $order->payment_mode }}</td>
                        <td>{{ $order->live_tracking }}</td>
                        <td>
                                @if ($order->status === 1)
                                    <span class="badge badge-success">Verified</span>
                                @elseif ($order->status === 0)
                                    <span class="badge badge-primary">Pending</span>
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
    });
    </script>

    <script>
        $(document).ready(function () {
            // Handle "Select All" checkbox
            $('#checkAll').change(function () {
                $('.checkbox').prop('checked', $(this).prop('checked'));
            });


            // Handle Approve Button click
            $('#verifyButton').click(function () {
                updateStatus(1); // Status 1 for Approved
            });

        });

        // Function to update status via AJAX
        function updateStatus(status) {
            var orderIds = [];
            $('.checkbox:checked').each(function () {
                orderIds.push($(this).data('id'));
            });

            if (orderIds.length === 0) {
                alert('Please select at least one trip to update status.');
                return;
            }

            $.ajax({
                url: '{{ route("orders.verify") }}',
                type: 'POST',
                data: {
                    order_ids: orderIds,
                    status: status,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Failed to update status. Please try again.');
                    }
                },
                error: function () {
                    alert('Failed to update status. Please try again.');
                }
            });
        }
    </script>
@endsection
