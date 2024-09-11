@extends('layouts.admin')

@section('title', 'Create Reviews')
@section('content-header', 'Create Reviews')
@section('content-actions')
    <a href="{{ route('reviews.index') }}" class="btn btn-success">Back To Reviews</a>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="product_id">Product ID</label>
                <select name="product_id" class="form-control @error('product_id') is-invalid @enderror" id="product_id">
                    <option value=''>--select--</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" id="description">{{ old('description') }}</textarea>
                @error('description')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="ratings">Ratings</label>
                <input type="text" name="ratings" class="form-control @error('ratings') is-invalid @enderror" id="ratings" value="{{ old('ratings') }}">
                @error('ratings')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Image Inputs -->
            @foreach(range(1, 3) as $i)
                <div class="form-group">
                    <label for="image{{ $i }}">Image {{ $i }}</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="image{{ $i }}" id="image{{ $i }}" onchange="updateProfileLabel(this)">
                        <label class="custom-file-label" for="image{{ $i }}" id="image-label">Choose File</label>
                    </div>
                    @error('image' . $i)
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            @endforeach

            <button class="btn btn-success btn-block btn-lg" type="submit">Submit</button>
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

    function updateProfileLabel(input) {
        var fileName = input.files[0].name;
        var label = $(input).siblings('.custom-file-label');
        label.text(fileName);
    }
</script>
@endsection
