@extends('layouts.admin')

@section('title', 'Update Reviews')
@section('content-header', 'Update Reviews')
@section('content-actions')
    <a href="{{ route('reviews.index') }}" class="btn btn-success"><i class="fas fa-back"></i> Back To Reviews</a>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('reviews.update', $review) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="product_id">Product</label>
                    <select name="product_id" class="form-control @error('product_id') is-invalid @enderror" id="product_id">
                        <option value=''>--Select Product--</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ (old('product_id') ?? $review->product_id) == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="ratings">Ratings</label>
                    <input type="number" name="ratings" class="form-control @error('ratings') is-invalid @enderror"
                           id="ratings" placeholder="Ratings" min="1" max="5" value="{{ old('ratings', $review->ratings) }}">
                    @error('ratings')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                              id="description" placeholder="Description">{{ old('description', $review->description) }}</textarea>
                    @error('description')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                @php
                    $images = ['image1', 'image2', 'image3'];
                @endphp

                @foreach($images as $image)
                    <div class="form-group">
                        <span>Current {{ ucfirst($image) }}:</span>
                        @if ($review->$image)
                            <img src="{{ asset('storage/app/public/reviews/' . $review->$image) }}" style="max-width: 100px; max-height: 100px;">
                        @endif
                        <br>
                        <label for="{{ $image }}">New {{ ucfirst($image) }}</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="{{ $image }}" id="{{ $image }}">
                            <label class="custom-file-label" for="{{ $image }}">Choose file</label>
                            @if ($review->$image)
                                <input type="hidden" name="existing_{{ $image }}" value="{{ $review->$image }}">
                            @endif
                        </div>
                        @error($image)
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                @endforeach


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
            const fileInputs = document.querySelectorAll('.custom-file-input');
            fileInputs.forEach(input => {
                input.addEventListener('change', function () {
                    const fileName = this.files[0].name;
                    this.nextElementSibling.textContent = fileName;
                });
            });
        });
    </script>
@endsection
