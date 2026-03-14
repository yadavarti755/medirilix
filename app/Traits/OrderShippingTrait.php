<?php

namespace App\Traits;

use App\Models\Order;
use App\Models\OrderProductList;
use App\Models\ShipRocketOrderShippingDetails;
use App\Models\ShipRocketReturnOrder;
use App\Models\User;
use Illuminate\Support\Facades\Config;

/**
 * 
 */
trait OrderShippingTrait
{
    use AddressTrait;
    public function authenticateUser()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/auth/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                "email": "' . Config::get('services.shiprocket.email') . '",
                "password": "' . Config::get('services.shiprocket.password') . '"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($response);
        // dd($result->token);
        return $result->token;
    }

    public function pushOrderToShipRocket($orderNumber, $orderBoxData)
    {
        $token = $this->authenticateUser();
        if (!$token) {
            return [
                'status' => false,
                'msg' => 'Invalid token of shiprocket'
            ];
        }

        // Get the order details
        $order = Order::where('order_number', $orderNumber)->first();

        $orderProductsList = OrderProductList::where('order_number', $order->order_number)->get();
        $orderAddress = $this->getOrderAddressUsingOrderNo($order->user_id, $order->order_number);
        $user = User::where('user_id', $order->user_id)->first();

        $products = [];
        foreach ($orderProductsList as $product) {
            array_push($products, [
                'name' => $product->product_name,
                'sku' => $product->product_name,
                'units' => $product->quantity,
                'selling_price' => $product->price,
                'discount' => "",
                'tax' => "",
                'hsn' => "",
            ]);
        }
        // $products = json_encode($products);

        $curl = curl_init();
        $post_fields = [
            "order_id" => $order->order_number,
            "order_date" => $order->order_date,
            "pickup_location" => "Manjeet",
            "channel_id" => "",
            "comment" => "Artificial Gehna",
            "billing_customer_name" => $orderAddress->person_name,
            "billing_last_name" => "",
            "billing_address" => $orderAddress->address,
            "billing_address_2" => $orderAddress->locality,
            "billing_city" => $orderAddress->city,
            "billing_pincode" => $orderAddress->pincode,
            "billing_state" => $orderAddress->state_name,
            "billing_country" => $orderAddress->country,
            "billing_email" => $user->email,
            "billing_phone" => $orderAddress->person_contact_number,
            "shipping_is_billing" => true,
            "shipping_customer_name" => "",
            "shipping_last_name" => "",
            "shipping_address" => "",
            "shipping_address_2" => "",
            "shipping_city" => "",
            "shipping_pincode" => "",
            "shipping_country" => "",
            "shipping_state" => "",
            "shipping_email" => "",
            "shipping_phone" => "",
            "order_items" => $products,
            "payment_method" => "Prepaid",
            "shipping_charges" => 0,
            "giftwrap_charges" => 0,
            "transaction_charges" => 0,
            "total_discount" => 0,
            "sub_total" => $order->total_price,
            "length" => $orderBoxData['length'],
            "breadth" => $orderBoxData['breadth'],
            "height" => $orderBoxData['height'],
            "weight" => $orderBoxData['weight'],
        ];

        $data = array(
            CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/orders/create/adhoc',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($post_fields),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token . ''
            ),
        );
        // print_r($data);
        // die;
        curl_setopt_array($curl, $data);

        $response = curl_exec($curl);
        curl_close($curl);

        $result = json_decode($response);

        if ($result->status_code == 1) {
            // Insert the response of ship rocket in database
            $result = ShipRocketOrderShippingDetails::create([
                "ag_order_number" => $order->order_number,
                "order_id" => $result->order_id,
                "shipment_id" => $result->shipment_id,
                "status" => $result->status,
                "status_code" => $result->status_code,
                "onboarding_completed_now" => $result->onboarding_completed_now,
                "awb_code" => $result->awb_code,
                "courier_company_id" => $result->courier_company_id,
                "courier_name" => $result->courier_name,
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        } else {
            return [
                'status' => false,
                'message' => 'Ship rocket:' . $result->message
            ];
        }

        return [
            'status' => true,
            'message' => 'Product uploaded on ship rocket panel.'
        ];
    }

    public function trackShipRocketOrder($shippmentId)
    {
        $token = $this->authenticateUser();
        if (!$token) {
            return [
                'status' => false,
                'msg' => 'Invalid token of shiprocket'
            ];
        }


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/courier/track/shipment/' . $shippmentId,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token . ''
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $result = json_decode($response);

        return $result;
    }


    public function pushReturnRequestOrderToShipRocket($return_requests, $orderNumber, $orderBoxData)
    {
        $token = $this->authenticateUser();
        if (!$token) {
            return [
                'status' => false,
                'msg' => 'Invalid token of shiprocket'
            ];
        }

        $products = [];
        // Get the order details
        $order = Order::where('order_number', $orderNumber)->first();
        $orderAddress = $this->getOrderAddressUsingOrderNo($order->user_id, $order->order_number);
        $user = User::where('user_id', $order->user_id)->first();

        // Update the each request id status
        foreach ($return_requests as $rr) {
            $opl = OrderProductList::where([
                'product_id' => $rr->product_id
            ])->first();

            array_push($products, [
                'sku' => $opl->product_sku,
                'name' => $opl->product_name,
                'units' => $opl->quantity,
                'selling_price' => $opl->price,
                'discount' => "",
                'hsn' => "",
                "qc_enable" => false,
            ]);
        }

        $post_fields = [
            "order_id" => $return_requests[0]['return_code'],
            "order_date" => $return_requests[0]['return_date'],
            "channel_id" => "2925018",
            "pickup_customer_name" => $orderAddress->person_name,
            "pickup_last_name" => "",
            "pickup_address" => $orderAddress->address,
            "pickup_address_2" => $orderAddress->locality,
            "pickup_city" => $orderAddress->city,
            "pickup_state" => $orderAddress->state_name,
            "pickup_country" => $orderAddress->country,
            "pickup_pincode" => $orderAddress->pincode,
            "pickup_email" => $user->email,
            "pickup_phone" => $orderAddress->person_contact_number,
            "pickup_isd_code" => "91",
            "pickup_location_id" => "",
            "shipping_customer_name" => Config::get('constants.warehouse_address')['warehouse_first_name'],
            "shipping_last_name" => Config::get('constants.warehouse_address')['warehouse_last_name'],
            "shipping_address" => Config::get('constants.warehouse_address')['warehouse_address'],
            "shipping_address_2" => Config::get('constants.warehouse_address')['warehouse_address_2'],
            "shipping_city" => Config::get('constants.warehouse_address')['warehouse_city'],
            "shipping_country" => Config::get('constants.warehouse_address')['warehouse_pincode'],
            "shipping_pincode" => Config::get('constants.warehouse_address')['warehouse_country'],
            "shipping_state" => Config::get('constants.warehouse_address')['warehouse_state'],
            "shipping_email" => Config::get('constants.warehouse_address')['warehouse_email'],
            "shipping_isd_code" => "91",
            "shipping_phone" => Config::get('constants.warehouse_address')['warehouse_phone'],
            "order_items" => $products,
            "payment_method" => "PREPAID",
            "total_discount" => 0,
            "sub_total" => $order->total_price,
            "length" => $orderBoxData['length'],
            "breadth" => $orderBoxData['breadth'],
            "height" => $orderBoxData['height'],
            "weight" => $orderBoxData['weight'],
        ];

        // print_r(json_encode($post_fields));
        // die;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/orders/create/return',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($post_fields),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token . ''
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $result = json_decode($response);

        if ($result->status_code == 21) {
            // Insert the response of ship rocket in database
            $result = ShipRocketReturnOrder::create([
                "ag_return_code" => $order->order_number,
                "order_id" => $result->order_id,
                "shipment_id" => $result->shipment_id,
                "status" => $result->status,
                "status_code" => $result->status_code,
                "company_name" => (isset($result->company_name)) ? $result->company_name : '',
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        } else {
            return [
                'status' => false,
                'message' => 'Ship rocket:' . $result->message
            ];
        }

        return [
            'status' => true,
            'message' => 'Product uploaded on ship rocket panel.'
        ];
    }
}
