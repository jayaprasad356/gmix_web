@extends('layouts.admin')

@section('title', 'Rise Tickets Management')
@section('content-header', 'Rise Tickets Management')
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
        <div class="col-md-8"></div> <!-- Create a placeholder column to push the search box to the right -->
        <div class="col-md-4">
            <!-- Search Form -->
            <form action="{{ route('tickets.index') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by....">
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Actions</th>
                    <th>ID <i class="fas fa-sort"></i></th>
                    <th>Staff Name <i class="fas fa-sort"></i></th>
                    <th>Order ID <i class="fas fa-sort"></i></th>
                    <th>Title <i class="fas fa-sort"></i></th>
                    <th>Description <i class="fas fa-sort"></i></th>
                    <th>Status <i class="fas fa-sort"></i></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tickets as $ticket)
                <tr>
                    <td>
                    <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                        <button class="btn btn-danger btn-delete" data-url="{{route('tickets.destroy', $ticket)}}"><i class="fas fa-trash"></i></button>
                    </td>
                    <td>{{ $ticket->id }}</td>
                    <!-- Display the staff name -->
                    <td>{{ $ticket->order && $ticket->order->user && $ticket->order->user->staff ? $ticket->order->user->staff->name : 'N/A' }}</td> <!-- Access staff name through order and user -->
                    <td>{{ $ticket->order_id }}</td>
                    <td>{{ $ticket->title }}</td>
                    <td>{{ $ticket->description }}</td>
                    <td>
                        <span class="{{ $ticket->status == 1 ? 'text-enable' : ($ticket->status == 2 ? 'text-cancelled' : 'text-pending') }}">
                            {{ $ticket->status == 1 ? 'Completed' : ($ticket->status == 2 ? 'Cancelled' : 'Pending') }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $tickets->links() }}
    </div>
</div>
@endsection

@section('js')
    <script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script>
        $(document).ready(function () {
            // Submit the form when user selection changes
            $('#tickets').change(function () {
                if ($(this).val() !== '') {
                    $('#tickets-form').submit();
                } else {
                    window.location.href = "{{ route('tickets.index') }}";
                }
            });
        });

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
                text: "Do you really want to delete this ticket?",
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
    </script>
@endsection
