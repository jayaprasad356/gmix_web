@extends('layouts.admin')

@section('title', 'Update Image Sliders')
@section('content-header', 'Update Image Sliders')
@section('content-actions')
    <a href="{{route('image_sliders.index')}}" class="btn btn-success"><i class="fas fa-back"></i>Back To Image Sliders</a>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">

        <form action="{{ route('image_sliders.update', $image_sliders) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           id="name"
                           placeholder="Name" value="{{ old('name', $image_sliders->name) }}">
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
                           placeholder="link" value="{{ old('link', $image_sliders->link) }}">
                    @error('link')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror 
                </div>


                <div class="form-group">
                    <span>Current Image:</span>
                    <img src="{{ asset('storage/app/public/image_sliders/' . $image_sliders->image) }}" alt="{{ $image_sliders->name }}" style="max-width: 100px; max-height: 100px;">
                    <br>
                    <label for="image">New Image</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="image" id="image">
                        <label class="custom-file-label" for="image">Choose file</label>
                        @if($image_sliders->image)
                            <input type="hidden" name="existing_image" value="{{ $image_sliders->image }}">
                        @endif
                    </div>
                    @error('image')
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
</script>
<script src="//cdn.ckeditor.com/4.21.0/full-all/ckeditor.js"></script>
<script>
    // Replace CKEditor for privacy_policy and terms_conditions textareas
    document.addEventListener('DOMContentLoaded', function () {
        CKEDITOR.replace('description', {
            extraPlugins: 'colorbutton'
        });
    });
</script>
@endsection
