@extends('layouts.admin')

@section('title', 'Withdrawals Management')
@section('content-header', 'Withdrawals Management')
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
                <!-- Checkbox for Select All -->
                <div class="form-check mr-3">
                    <input type="checkbox" class="form-check-input" id="checkAll">
                    <label class="form-check-label" for="checkAll">Select All</label>
                </div>
                <!-- Verify Button -->
                <button class="btn btn-primary mr-3" id="verifyButton">Paid</button>

                </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-8 d-flex align-items-center">
                <!-- Filter by Status -->
                <div class="form-group mb-0 d-flex align-items-center">
                    <label for="status-filter"  class="mr-1 mb-0 flex-shrink-0">Filter by status:</label>
                    <select name="status" id="status-filter" class="form-control">
                        <option value="0" {{ request()->input('status') === '0' ? 'selected' : '' }}>Pending</option>
                        <option value="1" {{ request()->input('status') === '1' ? 'selected' : '' }}>Paid</option>
                    </select>
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
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>Checkbox</th>
                        <th>ID <i class="fas fa-sort"></i></th>
                        <th>Staff Name <i class="fas fa-sort"></i></th>
                        <th>Staff Mobile <i class="fas fa-sort"></i></th>
                        <th>Amount <i class="fas fa-sort"></i></th>
                        <th>Status <i class="fas fa-sort"></i></th>
                        <th>DateTime <i class="fas fa-sort"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($withdrawals as $withdrawal)
                    <tr>
                        <td><input type="checkbox" class="checkbox" data-id="{{ $withdrawal->id }}"></td>
                        <td>{{ $withdrawal->id }}</td>
                        <td>{{ optional($withdrawal->staffs)->name }}</td>
                        <td>{{ optional($withdrawal->staffs)->mobile }}</td>
                        <td>{{ $withdrawal->amount }}</td>
                        <td>
                            <span class="
                                {{ $withdrawal->status == 0 ? 'text-pending' : '' }}
                                {{ $withdrawal->status == 1 ? 'text-paid' : '' }}
                                {{ $withdrawal->status == 2 ? 'text-cancelled' : '' }}
                            ">
                                {{ $withdrawal->status == 0 ? 'Pending' : '' }}
                                {{ $withdrawal->status == 1 ? 'Paid' : '' }}
                                {{ $withdrawal->status == 2 ? 'Cancelled' : '' }}
                            </span>
                        </td>
                        <td>{{ $withdrawal->datetime }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $withdrawals->appends(request()->query())->links() }}
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

    // Handle status filter change
    $('#status-filter').change(function () {
        clearTimeout(timeout); // Clear the previous timeout
        timeout = setTimeout(function () {
            filterVerifications();
        }, ); 
    });

    function filterVerifications() {
        let search = $('#search-input').val();
        let status = $('#status-filter').val();
        let url = `{{ route('withdrawals.index') }}?search=${encodeURIComponent(search)}&status=${encodeURIComponent(status)}`;
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
            var withdrawalIds = [];
            $('.checkbox:checked').each(function() {
                withdrawalIds.push($(this).data('id'));
            });

            if (withdrawalIds.length > 0) {
                // AJAX call to backend
                $.ajax({
                    url: "{{ route('withdrawals.verify') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        withdrawal_ids: withdrawalIds
                    },
                    success: function(response) {
                        // Handle success response
                        alert('Paid successfully!');
                        location.reload(); // Reload the page or update UI as needed
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        console.error(error);
                        alert('Error updating withdrawals. Please try again.');
                    }
                });
            } else {
                alert('Please select at least one withdrawals.');
            }
        });
    });
</script>
@endsection
