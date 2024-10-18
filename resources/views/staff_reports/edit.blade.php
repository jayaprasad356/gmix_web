@extends('layouts.admin')

@section('title', 'Update Staff Report')
@section('content-header', 'Update Staff Report')
@section('content-actions')
    <a href="{{route('staff_reports.index')}}" class="btn btn-success"><i class="fas fa-back"></i>Back To Reward Products</a>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('staff_reports.update', $staff_reports) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')


                <div class="form-group">
                    <label for="incentives">Incentives</label>
                    <input type="number" name="incentives" class="form-control @error('incentives') is-invalid @enderror"
                           id="incentives"
                           placeholder="incentives" value="{{ old('incentives', $staff_reports->incentives) }}">
                    @error('incentives')
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
    const fileInput = document.getElementById('image');
    const fileInputLabel = fileInput.nextElementSibling;

    fileInput.addEventListener('change', function () {
        const fileName = this.files[0].name;
        fileInputLabel.textContent = fileName;
    });
});
</script>@endsection
