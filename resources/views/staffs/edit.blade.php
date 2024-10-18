@extends('layouts.admin')

@section('title', 'Create staffs')
@section('content-header', 'Create staffs')
@section('content-actions')
    <a href="{{ route('staffs.index') }}" class="btn btn-success"><i class="fas fa-back"></i> Back To Staffs</a>
@endsection
@section('content')

<div class="card">
    <div class="card-body">
        <form action="{{ route('staffs.update', $staffs->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <!-- Add Incentives Button -->
            <a href="{{ route('staffs.add_incentives', $staffs->id) }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Incentives</a>
            
            <!-- Form Inputs -->
                <div class="form-group">
                    <br>
                    <label for="name">Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           id="name"
                           placeholder="Name" value="{{ old('name', $staffs->name) }}">
                    @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <br>
                    <label for="mobile">Mobile</label>
                    <input type="number" name="mobile" class="form-control @error('mobile') is-invalid @enderror"
                           id="mobile"
                           placeholder="mobile" value="{{ old('mobile', $staffs->mobile) }}">
                    @error('mobile')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <br>
                    <label for="password">Password</label>
                    <input type="text" name="password" class="form-control @error('password') is-invalid @enderror"
                           id="password"
                           placeholder="password" value="{{ old('password', $staffs->password) }}">
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <br>
                    <label for="incentives">Incentives</label>
                    <input type="number" name="incentives" class="form-control @error('incentives') is-invalid @enderror"
                           id="incentives"
                           placeholder="incentives" value="{{ old('incentives', $staffs->incentives) }}">
                    @error('incentives')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <br>
                    <label for="total_incentives">Total Incentives</label>
                    <input type="text" name="total_incentives" class="form-control @error('total_incentives') is-invalid @enderror"
                           id="total_incentives"
                           placeholder="total_incentives" value="{{ old('total_incentives', $staffs->total_incentives) }}">
                    @error('total_incentives')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <br>
                    <label for="bank">Bank Name</label>
                    <input type="text" name="bank" class="form-control @error('bank') is-invalid @enderror"
                           id="bank"
                           placeholder="bank" value="{{ old('bank', $staffs->bank) }}">
                    @error('bank')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <br>
                    <label for="branch">Branch Name</label>
                    <input type="text" name="branch" class="form-control @error('branch') is-invalid @enderror"
                           id="branch"
                           placeholder="branch" value="{{ old('branch', $staffs->branch) }}">
                    @error('branch')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <br>
                    <label for="ifsc">Ifsc Code</label>
                    <input type="text" name="ifsc" class="form-control @error('ifsc') is-invalid @enderror"
                           id="ifsc"
                           placeholder="ifsc" value="{{ old('ifsc', $staffs->ifsc) }}">
                    @error('ifsc')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <br>
                    <label for="account_number">Account Number</label>
                    <input type="number" name="account_number" class="form-control @error('account_number') is-invalid @enderror"
                           id="account_number"
                           placeholder="account_number" value="{{ old('account_number', $staffs->account_number) }}">
                    @error('account_number')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <br>
                    <label for="holder_name">Holder Name</label>
                    <input type="text" name="holder_name" class="form-control @error('holder_name') is-invalid @enderror"
                           id="holder_name"
                           placeholder="holder_name" value="{{ old('holder_name', $staffs->holder_name) }}">
                    @error('holder_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>


                <button class="btn btn-success btn-block btn-lg" type="submit">Save Changes</button>
            </form>
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
    <script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('profile');
    const fileInputLabel = fileInput.nextElementSibling;

    fileInput.addEventListener('change', function () {
        const fileName = this.files[0].name;
        fileInputLabel.textContent = fileName;
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('cover_img');
    const fileInputLabel = fileInput.nextElementSibling;

    fileInput.addEventListener('change', function () {
        const fileName = this.files[0].name;
        fileInputLabel.textContent = fileName;
    });
});
</script>
@endsection
