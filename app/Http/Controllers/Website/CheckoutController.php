<?php

namespace App\Http\Controllers\Website;

use Auth;
use Response;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AddressService;
use App\Services\CountryService;
use App\Services\StateService;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    protected $addressService;
    protected $countryService;
    protected $stateService;

    // Construct
    public function __construct()
    {
        $this->addressService = new AddressService();
        $this->countryService = new CountryService();
        $this->stateService = new StateService();
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);
        if (count($cart) < 1) {
            return redirect('/cart');
        }
        setEncryptionKey();
        $userAddress = [];
        if (Auth::check()) {
            $userAddress = $this->addressService->findAllUsersAddress();
        }
        $countries = $this->countryService->findAll();
        // dd($userAddress);
        return view('website.checkout', compact('userAddress', 'countries'))->with(['pageTitle' => 'Checkout', 'type' => 'checkout']);
    }

    public function proceedCheckout(Request $request)
    {
        $cart = session()->get('cart', []);
        if (count($cart) < 1) {
            return redirect('/cart');
        }

        $inputs = [
            'payment_method' => 'required',
            'address_checked' => 'required'
        ];

        $validator = Validator::make($request->all(), $inputs);

        if ($validator->fails()) {
            return Response::json([
                'status' => 'validation_error',
                'message' => $validator->errors()->all()
            ]);
        }

        $request->session()->put('payment_method', $request->payment_method);
        $request->session()->put('shipping_address', [
            'address_id' => $request->address_checked
        ]);

        return Response::json([
            'status' => true,
            'message' => '',
            'redirect_to' => url()->to('/order-summary')
        ]);
    }
}
