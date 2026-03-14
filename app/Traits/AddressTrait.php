<?php

namespace App\Traits;

use Auth;
use App\Models\Address;
use App\Models\OrderAddress;

/**
 * 
 */
trait AddressTrait
{
    public function getUserAddressUsingID($id)
    {
        return Address::rightJoin('states', 'addresses.state', '=', 'states.id')
            ->select('addresses.*', 'states.name')
            ->where('addresses.user_id', Auth::user()->id)
            ->where('addresses.id', $id)->first();
    }

    public function getOrderAddressUsingOrderNo($userID, $orderNumber)
    {
        return OrderAddress::join('states', 'order_addresses.state', '=', 'states.id')
            ->select('order_addresses.*', 'states.name')
            ->where('order_addresses.user_id', $userID)
            ->where('order_addresses.order_number', $orderNumber)->first();
    }
}
