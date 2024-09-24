@extends('layouts.admin')

@section('title', 'Edit Rise Tickets')
@section('content-header', 'Edit Rise Tickets')
@section('content-actions')
    <a href="{{ route('tickets.index') }}" class="btn btn-success"><i class="fas fa-back"></i> Back To Tickets</a>
@endsection
@section('content')

<div class="card">
    <div class="card-body">

        <form action="{{ route('tickets.update', $ticket) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

          <!-- Order Details in two-column layout -->
          <div class="card mt-4">
                <div class="card-header">
                    <h4>Order Details</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Price -->
                        <div class="col-md-6">
                            <strong>Price:</strong>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $order->price }}</p>
                        </div>

                        <!-- Delivery Charges -->
                        <div class="col-md-6">
                            <strong>Delivery Charges:</strong>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $order->delivery_charges }}</p>
                        </div>

                        <!-- Payment Mode -->
                        <div class="col-md-6">
                            <strong>Payment Mode:</strong>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $order->payment_mode }}</p>
                        </div>

                        <!-- User Mobile -->
                        <div class="col-md-6">
                            <strong>User Mobile:</strong>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $order->user->mobile }}</p>
                        </div>

                        <!-- Product Name -->
                        <div class="col-md-6">
                            <strong>Product Name:</strong>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $order->product->name }}</p>
                        </div>

                        <!-- Address -->
                        <div class="col-md-6">
                            <strong>Address:</strong>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $order->addresses->first_name }} {{ $order->addresses->last_name }}</p>
                            <p>{{ $order->addresses->street_name }}</p>
                            <p>{{ $order->addresses->city }}, {{ $order->addresses->state }} - {{ $order->addresses->pincode }}</p>
                        </div>

                        <!-- Order Status -->
                        <div class="col-md-6">
                            <strong>Order Status:</strong>
                        </div>
                        <div class="col-md-6">
                        <p>
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
                            @else
                                <span class="badge badge-dark">Unknown Status</span> <!-- Handle unknown status -->
                            @endif
                            </p>
                        </div>

                        <!-- Ordered Date -->
                        <div class="col-md-6">
                            <strong>Ordered Date:</strong>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $order->ordered_date }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
    <label for="status">Status</label>
    <div class="btn-group btn-group-toggle d-block" data-toggle="buttons">
        <label class="btn btn-outline-success {{ old('status', $ticket->status) === 1 ? 'active' : '' }}">
            <input type="radio" name="status" id="status_activated" value="1" {{ old('status', $ticket->status) === 1 ? 'checked' : '' }}> Completed
        </label>
        <label class="btn btn-outline-primary {{ old('status', $ticket->status) === 0 ? 'active' : '' }}">
            <input type="radio" name="status" id="status_pending" value="0" {{ old('status', $ticket->status) === 0 ? 'checked' : '' }}> Pending
        </label>
        <label class="btn btn-outline-danger {{ old('status', $ticket->status) === 2 ? 'active' : '' }}">
            <input type="radio" name="status" id="status_cancelled" value="2" {{ old('status', $ticket->status) === 2 ? 'checked' : '' }}> Cancelled
        </label>
    </div>

            <!-- Submit Button for the status update -->
            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary">Update Ticket</button>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<script>
    $(document).ready(function () {
        bsCustomFileInput.init();
    });
</script>
@endsection