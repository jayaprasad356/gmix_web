@extends('layouts.admin')

@section('title', 'Update Orders')
@section('content-header', 'Update Orders')
@section('content-actions')
    <a href="{{ route('orders.index') }}" class="btn btn-success"><i class="fas fa-back"></i> Back To Orders</a>
@endsection

@section('content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('orders.update', $order) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="address_id" value="{{ $addresses->id }}">

                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                           id="first_name"
                           placeholder="First Name" value="{{ old('first_name', $addresses->first_name) }}">
                    @error('first_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                        id="last_name"
                        placeholder="Last Name" value="{{ old('last_name', $addresses->last_name) }}">
                    @error('last_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="mobile">Mobile</label>
                    <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror"
                           id="mobile"
                           placeholder="Mobile" value="{{ old('mobile', $addresses->mobile) }}">
                    @error('mobile')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="alternate_mobile">Alternate Mobile</label>
                    <input type="text" name="alternate_mobile" class="form-control @error('alternate_mobile') is-invalid @enderror"
                           id="alternate_mobile"
                           placeholder="Alternate Mobile" value="{{ old('alternate_mobile', $addresses->alternate_mobile) }}">
                    @error('alternate_mobile')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="door_no">Door No</label>
                    <input type="text" name="door_no" class="form-control @error('door_no') is-invalid @enderror"
                           id="door_no"
                           placeholder="Door No" value="{{ old('door_no', $addresses->door_no) }}">
                    @error('door_no')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="street_name">Street Name</label>
                    <input type="text" name="street_name" class="form-control @error('street_name') is-invalid @enderror"
                           id="street_name"
                           placeholder="Street Name" value="{{ old('street_name', $addresses->street_name) }}">
                    @error('street_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
                           id="city"
                           placeholder="City" value="{{ old('city', $addresses->city) }}">
                    @error('city')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="pincode">Pincode</label>
                    <input type="text" name="pincode" class="form-control @error('pincode') is-invalid @enderror"
                           id="pincode"
                           placeholder="Pincode" value="{{ old('pincode', $addresses->pincode) }}">
                    @error('pincode')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="state">State</label>
                    <input type="text" name="state" class="form-control @error('state') is-invalid @enderror"
                           id="state"
                           placeholder="State" value="{{ old('state', $addresses->state) }}">
                    @error('state')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="landmark">Landmark</label>
                    <input type="text" name="landmark" class="form-control @error('landmark') is-invalid @enderror"
                           id="landmark"
                           placeholder="Landmark" value="{{ old('landmark', $addresses->landmark) }}">
                    @error('landmark')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <!-- Chat Conversation Image -->
                <div class="form-group">
                    <span>Current Image:</span>
                    @if(Str::startsWith($order->chat_conversation, 'upload/images/'))
                        <a href="https://gmixstaff.graymatterworks.com/{{ $order->chat_conversation }}" data-lightbox="image-{{ $order->id }}">
                            <img class="customer-img img-thumbnail img-fluid" src="https://gmixstaff.graymatterworks.com/{{ $order->chat_conversation }}" alt="Chat Conversation Image" style="max-width: 100px; max-height: 100px;">
                        </a>
                    @else
                        <!-- Otherwise, use the asset path -->
                        <a href="{{ asset('storage/app/public/orders/' . $order->chat_conversation) }}" data-lightbox="image-{{ $order->id }}">
                            <img class="customer-img img-thumbnail img-fluid" src="{{ asset('storage/app/public/orders/' . $order->chat_conversation) }}" alt="Chat Conversation Image" style="max-width: 100px; max-height: 100px;">
                        </a>
                    @endif
                    <br>
                    <label for="chat_conversation">New Chat Conversation Image</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="chat_conversation" id="inputFileChat" required>
                        <label class="custom-file-label" for="inputFileChat">Choose file</label>
                        @if($order->chat_conversation)
                            <input type="hidden" name="existing_chat_image" value="{{ $order->chat_conversation }}">
                        @endif
                    </div>
                    @error('chat_conversation')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
            <span>Current Image:</span>
            <div id="current_payment_image" style="{{ old('payment_mode', $order->payment_mode) === 'COD' ? 'display: none;' : '' }}"> <!-- Hide if COD is selected -->
                @if(Str::startsWith($order->payment_image, 'upload/images/'))
                    <a href="https://gmixstaff.graymatterworks.com/{{ $order->payment_image }}" data-lightbox="image-{{ $order->id }}">
                        <img class="customer-img img-thumbnail img-fluid" src="https://gmixstaff.graymatterworks.com/{{ $order->payment_image }}" alt="Payment Image" style="max-width: 100px; max-height: 100px;">
                    </a>
                @else
                    <a href="{{ asset('storage/app/public/orders/' . $order->payment_image) }}" data-lightbox="image-{{ $order->id }}">
                        <img class="customer-img img-thumbnail img-fluid" src="{{ asset('storage/app/public/orders/' . $order->payment_image) }}" alt="Payment Image" style="max-width: 100px; max-height: 100px;">
                    </a>
                @endif
            <br>
            <div class="form-group" id="payment_image_field" style="display: none;"> <!-- Hide initially -->
                <label for="payment_image">New Payment Image</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" name="payment_image" id="inputFilePayment">
                    <label class="custom-file-label" for="inputFilePayment">Choose file</label>
                    @if($order->payment_image)
                        <input type="hidden" name="existing_payment_image" value="{{ $order->payment_image }}">
                    @endif
                </div>
                @error('payment_image')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>

        <!-- Payment Mode Selection -->
        <div class="form-group">
            <label for="payment_mode">Payment Mode</label>
            <div class="btn-group btn-group-toggle d-block" data-toggle="buttons">
                <label class="btn btn-outline-success {{ old('payment_mode', $order->payment_mode) === "Prepaid" ? 'active' : '' }}">
                    <input type="radio" name="payment_mode" id="payment_mode_prepaid" value="Prepaid" {{ old('payment_mode', $order->payment_mode) === "Prepaid" ? 'checked' : '' }}> Prepaid
                </label>
                <label class="btn btn-outline-primary {{ old('payment_mode', $order->payment_mode) === "COD" ? 'active' : '' }}">
                    <input type="radio" name="payment_mode" id="payment_mode_cod" value="COD" {{ old('payment_mode', $order->payment_mode) === "COD" ? 'checked' : '' }}> COD
                </label>
            </div>
        </div>

        <button class="btn btn-success btn-block btn-lg" type="submit">Save Changes</button>
        </form>
        </div>
</div>

@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<script>
    $(document).ready(function () {
        // Check if bsCustomFileInput is defined
        if (typeof bsCustomFileInput !== 'undefined') {
            // Initialize custom file input styling
            bsCustomFileInput.init();
        } else {
            console.error('bsCustomFileInput is not defined. Please check the script path.');
        }

        // Function to toggle visibility of the payment image and input based on selected payment mode
        function togglePaymentImageField() {
            const isPrepaid = $('#payment_mode_prepaid').is(':checked'); // Check if Prepaid is selected
            const paymentImageField = $('#payment_image_field'); // Select payment image field
            const currentPaymentImage = $('#current_payment_image'); // Select current payment image field

            // Show or hide the payment image field
            if (isPrepaid) {
                paymentImageField.show(); // Show the input
                currentPaymentImage.show(); // Show the current image
            } else {
                paymentImageField.hide(); // Hide the input
                $('#inputFilePayment').val(''); // Clear the input if COD
                paymentImageField.find('.custom-file-label').html('Choose file'); // Reset label
                currentPaymentImage.hide(); // Hide the current image
            }
        }

        // Initial check on page load to set visibility based on the currently selected payment mode
        togglePaymentImageField();

        // Add event listener to radio buttons for payment mode selection
        $('input[name="payment_mode"]').change(togglePaymentImageField);

        // Update file input labels when a file is chosen
        $('#inputFileChat').on('change', function() {
            const fileName = this.files[0] ? this.files[0].name : 'Choose file';
            $(this).next('.custom-file-label').html(fileName);
        });

        $('#inputFilePayment').on('change', function() {
            const fileName = this.files[0] ? this.files[0].name : 'Choose file';
            $(this).next('.custom-file-label').html(fileName);
        });
    });
</script>

@endsection
