<?php

namespace App\Http\Requests;
use App\Models\Addresses;

use Illuminate\Foundation\Http\FormRequest;

class AddressesStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile' => [
                'required',
                'numeric',
                'digits_between:10,15',
                'different:alternate_mobile', // Ensure mobile and alternate_mobile are different
                function ($attribute, $value, $fail) {
                    // Custom rule to check if door_no, street_name, and landmark already exist together
                    if (Addresses::where('door_no', $this->door_no)
                        ->where('street_name', $this->street_name)
                        ->where('landmark', $this->landmark)
                        ->exists()) {
                        $fail('The address with the same Door No, Street Name, and Landmark already exists.');
                    }
                }
            ],
            'alternate_mobile' => [
                'required',
                'numeric',
                'digits_between:10,15',
                'different:mobile', // Ensure alternate_mobile and mobile are different
            ],
            'door_no' => 'required|string|max:255',
            'street_name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'pincode' => 'required|numeric|digits:6',
            'state' => 'required|string|max:255',
            'landmark' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'mobile.different' => 'Mobile and Alternate Mobile must be different.',
            'alternate_mobile.different' => 'Alternate Mobile and Mobile must be different.',
            'address.unique' => 'The address with the same Door No, Street Name, and Landmark already exists.',
        ];
    }

}
