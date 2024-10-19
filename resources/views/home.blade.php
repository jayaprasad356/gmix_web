@extends('layouts.admin')

@section('content-header', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- small box -->
            <div class="col-lg-4 col-6">
              <div class="small-box bg-orange">
                    <div class="inner">
                        <h3>{{ $today_customers}}</h3>
                        <p>Today Customers</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i> <!-- Example of a different icon -->
                    </div>
                    <a href="{{ route('users.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-4 col-6">
            <div class="small-box bg-purple">
                    <div class="inner">
                        <h3>{{ $today_orders }}</h3>
                        <p>Today Orders</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i> <!-- Example of a different icon -->
                    </div>
                    <a href="{{ route('orders.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-6">
            <div class="small-box bg-green">
                    <div class="inner">
                        <h3>{{ $today_profit }}</h3>
                        <p>Today Profit</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i> <!-- Example of a different icon -->
                    </div>
                    <a href="{{ route('orders.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-4 col-6">
            <div class="small-box bg-blue">
                    <div class="inner">
                        <h3>{{ $pending_tickets }}</h3>
                        <p>Pending Tickets</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-ticket-alt"></i> <!-- Example of a different icon -->
                    </div>
                    <a href="{{ route('tickets.index', ['search' => '', 'status' => 0]) }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

          


            <!-- ./col -->
        </div>
        <!-- /.row -->
    </div>
@endsection
