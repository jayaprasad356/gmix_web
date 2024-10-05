@extends('layouts.admin')

@section('title', 'Staff Reports Management')
@section('content-header', 'Staff Reports')
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
            <div class="col-md-8">
                <!-- Date Filter Form -->
                <form action="{{ route('staff_reports.index') }}" method="GET" id="filter-form">
                    <!-- Date Filter Dropdown -->
                    <select name="date_filter" id="date-filter" class="form-control" style="width: 200px;">
                        <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="yesterday" {{ request('date_filter') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                        <option value="this_week" {{ request('date_filter') == 'this_week' ? 'selected' : '' }}>This Week</option>
                        <option value="last_7_days" {{ request('date_filter') == 'last_7_days' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="this_month" {{ request('date_filter') == 'this_month' ? 'selected' : '' }}>This Month</option>
                    </select>
            </div>
            <div class="col-md-4">
                <!-- Search Input Field -->
                <div class="input-group">
                    <input type="text" name="search" id="search-input" class="form-control" placeholder="Search by Staff Name" value="{{ request('search') }}">
                </div>
            </div>
        </div>
                <!-- Submit the form on search or filter change -->
                <button type="submit" class="d-none">Submit</button>
            </form>

            <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Staff Name</th>
                        <th>Total Orders</th>
                        <th>COD Orders</th>
                        <th>Prepaid Orders</th>
                        <th>Quantity (kg)</th>
                        <th>Week Quantity (kg)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($staffs as $staff)
                        <tr>
                            <td>{{ $staff->id }}</td>
                            <td>{{ $staff->name }}</td>
                            <td>{{ $staff->total_orders }}</td>
                            <td>{{ $staff->cod_orders }}</td>
                            <td>{{ $staff->prepaid_orders }}</td>
                            <td>
                                @php
                                    $total_grams = isset($staff->total_grams) ? $staff->total_grams : 0;
                                    $total_kg = $total_grams / 1000;
                                    echo number_format($total_kg, 2) . ' kg';
                                @endphp
                            </td>
                            <td>
                                @php
                                    $weekly_grams = isset($staff->weekly_grams) ? $staff->weekly_grams : 0;
                                    $weekly_kg = $weekly_grams / 1000;
                                    echo number_format($weekly_kg, 2) . ' kg';
                                @endphp
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Show the total quantity below the table -->
        <div class="row mt-4">
            <div class="col-md-12">
                <h5>Total Quantity: <strong>{{ number_format($totalKg, 2) }} kg</strong></h5>
            </div>
        </div>

        {{ $staffs->links() }}
    </div>
</div>
@endsection

@section('js')
    <script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

    <script>
        document.getElementById('date-filter').addEventListener('change', function() {
            document.getElementById('filter-form').submit();
        });

        document.getElementById('search-input').addEventListener('input', function() {
            clearTimeout(this.debounceTimeout);
            this.debounceTimeout = setTimeout(function() {
                document.getElementById('filter-form').submit();
            }, 500); // Debounce delay
        });
    </script>
   <script>
        $(document).ready(function () {
            // Submit the form when user selection changes
            $('#user-filter').change(function () {
                if ($(this).val() !== '') {
                    $('#user-filter-form').submit();
                } else {
                    window.location.href = "{{ route('staff_reports.index') }}";
                }
            });
        });

        $(document).ready(function () {
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
                    text: "Do you really want to delete this customer?",
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
            })
        });

        $(document).ready(function() {
            $('.table th').click(function() {
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
                return function(a, b) {
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
@endsection
