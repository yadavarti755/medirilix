<?php

namespace App\Repositories;

use App\Models\PaymentGateway;

class PaymentGatewayRepository
{
    /**
     * Find all payment gateways.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAll()
    {
        return PaymentGateway::all();
    }

    /**
     * Find active payment gateways.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findActive()
    {
        return PaymentGateway::where('is_active', true)->get();
    }

    /**
     * Find payment gateway by ID.
     *
     * @param int|string $id
     * @return \App\Models\PaymentGateway|null
     */
    public function findById($id)
    {
        return PaymentGateway::find($id);
    }

    /**
     * Create a new payment gateway.
     *
     * @param array $data
     * @return \App\Models\PaymentGateway
     */
    public function create(array $data)
    {
        return PaymentGateway::create($data);
    }

    /**
     * Update an existing payment gateway.
     *
     * @param array $data
     * @param int|string $id
     * @return bool
     */
    public function update(array $data, $id)
    {
        $record = $this->findById($id);
        if ($record) {
            return $record->update($data);
        }
        return false;
    }

    /**
     * Delete a payment gateway.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete($id)
    {
        $record = $this->findById($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }
}
