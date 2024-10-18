@extends('layouts.admin')

@section('title', 'Staff Transactions Management')
@section('content-header', 'Staff Transactions Management')
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
                <!-- Staff Filter Dropdown -->
                <form id="staff-filter-form" action="{{ route('staff_transactions.index') }}" method="GET">
                    <div class="form-group">
                        <label for="staff-filter">Filter by Staff</label>
                        <select name="staff_id" id="staff-filter" class="form-control">
                            <option value="">All Staff</option>
                            @foreach ($staffs as $staff)
                                <option value="{{ $staff->id }}" {{ request('staff_id') == $staff->id ? 'selected' : '' }}>
                                    {{ $staff->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            <div class="col-md-4 text-md-right mt-3 mt-md-0">
                <!-- Search Form -->
                <form id="search-form" action="{{ route('staff_transactions.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" id="search-input" name="search" class="form-control" placeholder="Search by..." autocomplete="off" value="{{ request()->input('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit" style="display: none;">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>ID <i class="fas fa-sort"></i></th>
                        <th>Staff Name <i class="fas fa-sort"></i></th>
                        <th>Type <i class="fas fa-sort"></i></th>
                        <th>Points <i class="fas fa-sort"></i></th>
                        <th>DateTime <i class="fas fa-sort"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($staff_transactions as $staff_transaction)
                    <tr>
                        <td>{{ $staff_transaction->id }}</td>
                        <td>{{ optional($staff_transaction->staff)->name }}</td>
                        <td>{{ $staff_transaction->type }}</td>
                        <td>{{ $staff_transaction->amount }}</td>
                        <td>{{ $staff_transaction->datetime }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $staff_transactions->appends(request()->query())->links() }}
    </div>
</div>
@endsection

@section('js')
    <script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
   $(document).ready(function () {
        $('#staff-filter').change(function () {
            // Automatically submit the form when staff is selected
            $('#staff-filter-form').submit();
        });

        // Handle search input with debounce
        let debounceTimeout;
        $('#search-input').on('input', function () {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(function () {
                $('#search-form').submit();
            }, 500); // Adjust delay as needed
        });

        // Sorting functionality and other scripts here...
    });
    </script>
@endsection
