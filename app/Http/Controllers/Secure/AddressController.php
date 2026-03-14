<?php

namespace App\Http\Controllers\Secure;

use Log;
use App\Http\Controllers\Controller;
use App\DTO\AddressDto;
use App\Http\Requests\StoreAddressRequest;
use Auth;
use Response;
use App\Models\Address;
use App\Services\AddressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    protected $addressService;

    public function __construct()
    {
        $this->addressService = new AddressService();
    }

    // public function pincode(Request $request)
    // {
    //     $inputs = [
    //         'pincode' => 'required|numeric|digits:6',
    //     ];

    //     $validator = Validator::make($request->all(), $inputs);

    //     if ($validator->fails()) {
    //         return Response::json([
    //             'status' => 'validation_error',
    //             'message' => $validator->errors()->all()
    //         ]);
    //     }

    //     $location = DB::table('locations')
    //         ->select('city_name', 'state_id', 'state_name')
    //         ->where('pincode', $request->pincode)
    //         ->first();

    //     if ($location) {
    //         return Response::json([
    //             'status' => true,
    //             'data' => $location
    //         ]);
    //     } else {
    //         return Response::json([
    //             'status' => false,
    //             'message' => 'Server is not responding. Please try again.'
    //         ]);
    //     }
    // }

    public function store(StoreAddressRequest $request)
    {
        try {
            $dto = new AddressDto(
                Auth::user()->id,
                0,
                strip_tags($request->input('name')),
                strip_tags($request->input('phone_number')),
                strip_tags($request->input('alt_phone_number')),
                strip_tags($request->input('address')),
                strip_tags($request->input('locality')),
                strip_tags($request->input('landmark')),
                strip_tags($request->input('city')),
                strip_tags($request->input('state')),
                strip_tags($request->input('country')),
                strip_tags($request->input('pin_code')),
                1,
                Auth::user()->id,
                Auth::user()->id
            );


            $result = $this->addressService->create($dto);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while saving address.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Address created successfully!'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Address addition failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server failed while processing request'
            ], 500);
        }
    }

    public function update(StoreAddressRequest $request, Address $address)
    {
        try {
            $dto = new AddressDto(
                Auth::user()->id,
                0,
                strip_tags($request->input('name')),
                strip_tags($request->input('phone_number')),
                strip_tags($request->input('alt_phone_number')),
                strip_tags($request->input('address')),
                strip_tags($request->input('locality')),
                strip_tags($request->input('landmark')),
                strip_tags($request->input('city')),
                strip_tags($request->input('state')),
                strip_tags($request->input('country')),
                strip_tags($request->input('pin_code')),
                1,
                $address->created_by,
                Auth::user()->id
            );

            $result = $this->addressService->update($dto, [
                'user_id' => Auth::user()->id,
                'id' => $address->id
            ]);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while saving address.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Address updated successfully!'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Address addition failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server failed while processing request'
            ], 500);
        }
    }


    // Function to delete address
    public function destroy(Request $request)
    {
        if (!Auth::check()) {
            return Response::json([
                'status' => false,
                'message' => 'You are not logged in. Please login to perform this action'
            ]);
        }

        $inputs = [
            'id' => 'required',
        ];

        $validator = Validator::make($request->all(), $inputs);

        if ($validator->fails()) {
            return Response::json([
                'status' => 'validation_error',
                'message' => $validator->errors()->all()
            ]);
        }

        $id = Crypt::decryptString($request->id);

        $result = $this->addressService->delete($id);

        if ($result) {
            $output = [
                'status' => true,
                'message' => 'Address deleted successfully.'
            ];
        } else {
            $output = [
                'status' => false,
                'message' => 'Server is not responding. Please try again.'
            ];
        }

        return Response::json($output);
    }

    // Function to get single address
    public function fetchOne(Request $request)
    {
        if (!Auth::check()) {
            return Response::json([
                'status' => false,
                'message' => 'You are not logged in. Please login to perform this action'
            ]);
        }

        $inputs = [
            'id' => 'required',
        ];

        $validator = Validator::make($request->all(), $inputs);

        if ($validator->fails()) {
            return Response::json([
                'status' => 'validation_error',
                'message' => $validator->errors()->all()
            ]);
        }

        $id = Crypt::decryptString($request->id);

        $address = $this->addressService->findByUserAndId(Auth::user()->id, $id);

        if ($address) {
            $output = [
                'status' => true,
                'address' => $address
            ];
        } else {
            $output = [
                'status' => false,
                'message' => 'Server is not responding. Please try again.'
            ];
        }

        return Response::json($output);
    }
}
