<?php

namespace App\DTO;

class OrderAddressDto
{
    public $user_id;
    public $order_number;
    public $person_name;
    public $person_contact_number;
    public $person_alt_contact_number;
    public $address;
    public $locality;
    public $landmark;
    public $city;
    public $state;
    public $country;
    public $pincode;
    public $created_by;
    public $updated_by;

    public function __construct(
        $user_id,
        $order_number,
        $person_name,
        $person_contact_number = null,
        $person_alt_contact_number = null,
        $address = null,
        $locality = null,
        $landmark = null,
        $city = null,
        $state = null,
        $country = null,
        $pincode = null,
        $created_by = null,
        $updated_by = null
    ) {
        $this->user_id = $user_id;
        $this->order_number = $order_number;
        $this->person_name = $person_name;
        $this->person_contact_number = $person_contact_number;
        $this->person_alt_contact_number = $person_alt_contact_number;
        $this->address = $address;
        $this->locality = $locality;
        $this->landmark = $landmark;
        $this->city = $city;
        $this->state = $state;
        $this->country = $country;
        $this->pincode = $pincode;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
