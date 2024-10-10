@extends('layouts.admin')

@section('title', 'Create Image Sliders')
@section('content-header', 'Create Image Sliders')
@section('content-actions')
    <a href="{{route('image_sliders.index')}}" class="btn btn-success"><i class="fas fa-back"></i>Back To Image Sliders</a>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('image_sliders.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           id="name"
                           placeholder="name" value="{{ old('name') }}">
                    @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="link">Link</label>
                    <input type="text" name="link" class="form-control @error('link') is-invalid @enderror"
                           id="link"
                           placeholder="link" value="{{ old('link') }}">
                    @error('link')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>


                <div class="form-group">
                    <label for="image">Image</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="image" id="image" onchange="updateProfileLabel(this)">
                        <label class="custom-file-label" for="image" id="image-label">Choose File</label>
                    </div>
                    @error('image')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <button class="btn btn-success btn-block btn-lg" type="submit">Submit</button>
            </form>
        </div>
    </div>
@endsection

@section('js')
<script src="//cdn.ckeditor.com/4.21.0/full-all/ckeditor.js"></script>
    <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            bsCustomFileInput.init();
        });

        function updateProfileLabel(input) {
            var fileName = input.files[0].name;
            var label = $(input).siblings('.custom-file-label');
            label.text(fileName);
        }
    </script>
    <script>
    // Replace CKEditor for privacy_policy and terms_conditions textareas
    document.addEventListener('DOMContentLoaded', function () {
        CKEDITOR.replace('description', {
            extraPlugins: 'colorbutton'
        });
     
    });
</script>
@endsection
