<?php

namespace App\Repositories;

use App\Models\ShipRocketOrderShippingDetails;

class ShipRocketOrderShippingDetailsRepository
{
    public function findAll()
    {
        return ShipRocketOrderShippingDetails::get();
    }

    public function findById($id)
    {
        return ShipRocketOrderShippingDetails::find($id);
    }

    public function create($data)
    {
        return ShipRocketOrderShippingDetails::create($data);
    }

    public function update($data, $id)
    {
        $result = ShipRocketOrderShippingDetails::find($id);
        if ($result) {
            $result = $result->update($data);
            if (!$result) {
                return false;
            }
            return $result;
        }
        return false;
    }

    public function delete($id)
    {
        $result = ShipRocketOrderShippingDetails::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }

    public function findByOrderNumber($orderNumber)
    {
        return ShipRocketOrderShippingDetails::where('ag_order_number', $orderNumber)->orderBy('id', 'DESC')->first();
    }
}
