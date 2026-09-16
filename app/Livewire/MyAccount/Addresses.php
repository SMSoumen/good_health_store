<?php

namespace App\Livewire\MyAccount;

use App\Http\Controllers\AddressController;
use App\Models\Address;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class Addresses extends Component
{
    protected $listeners = ['refreshComponent', 'deleteAddress', 'resetForm'];
    public $name = '';
    public $type = '';
    public $mobile = '';
    public $alternate_mobile = '';
    public $address = '';
    public $landmark = '';
    public $city_name = '';
    public $pincode = '';
    public $state = '';
    public $country = '';
    public $latitude = '';
    public $longitude = '';
    public $address_id = '';

    public function render(AddressController $addressController)
    {
        $user = Auth::user();
        $res = $this->get_Address($addressController);
        return view('livewire.' . config('constants.theme') . '.my-account.addresses', [
            'user_info' => $user,
            'addresses' => $res
        ])->title("Addresses |");
    }

    public function get_address($addressController)
    {
        $user = Auth::user();
        $res = $addressController->getAddress($user->id);
        return $res;
    }

    
    public function add_address(Request $request)
    {
        $user_id = Auth::user()->id ?? "";
        
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string',
                'type' => 'required',
                'mobile' => 'required|digits_between:1,16|numeric',
                'alternate_mobile' => 'nullable|digits_between:1,16|numeric',
                'address' => 'required',
                'landmark' => 'required',
                'city_name' => 'required',
                'pincode' => 'required',
                'state' => 'required',
                'country' => 'required',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
            ]
        );

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['error'] = true;
            $response['message'] = $errors;
            return $response;
        }

        // Initialize existing address variable
        $existingAddress = null;
        
        // Get existing address if updating
        if (isset($request->address_id) && !empty($request->address_id)) {
            // Scoped by user_id so one customer cannot update another's address.
            $existingAddress = Address::where('id', $request->address_id)
                ->where('user_id', $user_id)
                ->first();
        }

        // Fetch city_id based on the selected city name
        $request['user_id'] = $user_id;
        $cityName = $request['city_name'];

        // Check if city is provided and valid, otherwise keep the old value
        if ($cityName === 'false' || empty($cityName)) {
            $city_id = $existingAddress->city_id ?? null;
            $city_name = $existingAddress->city ?? null;
        } else {
            // Decode the JSON and get the 'en' field
            $decodedCityName = json_decode($cityName, true);
            $city_name = $decodedCityName['en'] ?? null; // Get 'en' field from JSON

            // Fetch city_id based on the 'en' name
            $city = City::where('name->en', $city_name)->first();

            // dd($city);
            $city_id = $city ? $city->id : null;
        }

        // Fetch country_code based on the selected country name
        $countryName = $request['country'];

        // If country is not provided or is set to 'false', keep the old value
        if ($countryName === 'false' || empty($countryName)) {
            $country_code = $existingAddress->country_code ?? null;
            $country = $existingAddress->country ?? null;
            // `country` is read straight off the request below, so the retained
            // value has to be written back -- otherwise the literal string
            // "false" the select2 posts is what lands in the column.
            $request['country'] = $country;
        } else {
            $country = DB::table('countries')
                ->select('*')
                ->where('name', $countryName)
                ->first();
            $country_code = $country ? $country->phonecode : null;
        }

        // The city/country select2s post the literal string "false" when nothing
        // is picked, which satisfies `required` but resolves to null here. On the
        // create path there is no existing address to fall back on, so that
        // reached the DB as a null `city` and blew up as a 500 instead of
        // telling the customer what was wrong.
        $selection_errors = [];
        if (empty($city_name)) {
            $selection_errors['city_name'] = ['Please select a city.'];
        }
        if (empty($request['country']) || $request['country'] === 'false') {
            $selection_errors['country'] = ['Please select a country.'];
        }
        if (!empty($selection_errors)) {
            return [
                'error' => true,
                'message' => $selection_errors,
            ];
        }

        // Add city_id and country_code to address data
        $request['city_id'] = $city_id;
        $request['city'] = $city_name;
        $request['country_code'] = $country_code;

        // Prepare address data
        $address_data = $request->only([
            'user_id',
            'name',
            'type',
            'mobile',
            'alternate_mobile',
            'address',
            'landmark',
            'city',
            'city_id',
            'pincode',
            'country',
            'state',
            'latitude',
            'longitude',
            'country_code'
        ]);
        // dd($address_data);
        // If an address_id is provided, update the existing address
        if (isset($request->address_id) && !empty($request->address_id)) {
            $address_id = $request->address_id;

            // Check if address exists
            if (!$existingAddress) {
                return [
                    'error' => true,
                    'message' => 'Address not found.'
                ];
            }

            // Retain old city and country if not selected properly (i.e., value is 'false')
            if ($request->city_name == 'false' || empty($request->city_name)) {
                $address_data['city'] = $existingAddress->city;
                $address_data['city_id'] = $existingAddress->city_id;
            }

            if ($request->country == 'false' || empty($request->country)) {
                $address_data['country'] = $existingAddress->country;
                $address_data['country_code'] = $existingAddress->country_code;
            }

            // Ownership is verified above, so a zero-row result here just means
            // nothing actually changed -- MySQL reports rows *changed*, not
            // matched, so resubmitting the form untouched would otherwise be
            // reported to the customer as a failure.
            try {
                Address::where('id', $address_id)
                    ->where('user_id', $user_id)
                    ->update($address_data);
            } catch (\Exception $e) {
                return [
                    'error' => true,
                    'message' => 'Failed to update address. Please try again.'
                ];
            }

            return [
                'error' => false,
                'message' => 'Address updated successfully!'
            ];
        } else {
            // Make the user's first address the default automatically.
            $address_data['is_default'] = Address::where('user_id', $user_id)->count() === 0 ? 1 : 0;

            // Insert new address if no address_id is provided
            $address_id = Address::insertGetId($address_data);
            if (!$address_id) {
                $response = [
                    'error' => true,
                    'message' => 'Failed to add address. Please try again.'
                ];
                return $response;
            }
            $response = [
                'error' => false,
                'message' => 'Address added successfully!'
            ];
            return $response;
        }
    }

    public function edit_address(Request $request)
    {
        $addressId = $request->input('address_id');

        // Scope to the signed-in user. This endpoint is routed and reachable
        // directly, so an unscoped find() would hand any customer's name,
        // phone number and address to any logged-in visitor.
        $address_data = Address::where('id', $addressId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$address_data) {
            return response()->json([
                'error' => true,
                'message' => 'Address not found.',
            ], 404);
        }

        return $address_data;
    }

    public function deleteAddress($id)
    {
        $user = Auth::user();

        $data = [
            'user_id' => $user->id,
            'id' => $id,
        ];
        deleteDetails($data, Address::class);
    }

    public function setDefault($address_id)
    {
        $user = Auth::user();
        $address = Address::where('id', $address_id)->where('user_id', $user->id)->first();
        if ($address) {
            // Update the is_default status for all addresses of the user
            Address::where('user_id', $user->id)->update(['is_default' => 0]);
            updateDetails(['is_default' => '1'], ['id' => $address_id], Address::class);
        }
    }

    public function openAddModal()
    {
        $this->resetForm();
    }
    public function refreshComponent()
    {
        $this->dispatch('$refresh');
    }

    public function resetForm()
    {
        $this->reset([
            'name',
            'type',
            'mobile',
            'alternate_mobile',
            'address',
            'landmark',
            'city_name',
            'pincode',
            'state',
            'country',
            'latitude',
            'longitude',
            'address_id',
        ]);

        $this->resetErrorBag();
        $this->resetValidation();
    }

}
