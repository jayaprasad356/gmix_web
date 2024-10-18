@extends('layouts.admin')

@section('title', 'Add Incentives')
@section('content-header', 'Add Incentives')
@section('content-actions')
    <a href="{{ route('staffs.edit', $staff->id) }}" class="btn btn-success"><i class="fas fa-back"></i> Back To User</a>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('staffs.add_incentives', $staff->id) }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="incentives">Incentives</label>
                    <input type="number" name="incentives" class="form-control @error('incentives') is-invalid @enderror"
                           id="incentives" placeholder="Enter incentives" value="{{ old('incentives') }}">
                    @error('incentives')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <button class="btn btn-success btn-block btn-lg" type="submit">Add Incentives</button>
            </form>
        </div>
    </div>
@endsection
