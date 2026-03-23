<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository
{
    /**
     * Find all orders with eager loading for the admin panel.
     */
    public function findAll(): Collection
    {
        return Order::with(['user'])->get();
    }

    /**
     * Find a single order by ID with relationships.
     */
    public function findById(int $id): ?Order
    {
        // Eager load only the main relationships needed for viewing the order
        return Order::with(['user'])->find($id);
    }

    /**
     * Find a single order by Order Number.
     */
    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return Order::where('order_number', $orderNumber)
            ->with(['user'])
            ->first();
    }

    /**
     * Create a new order.
     *
     * @param array $data Data from OrderDto
     */
    public function create(array $data): Order
    {
        return Order::create($data);
    }

    /**
     * Update an existing order.
     */
    public function update(array $data, int $id): bool
    {
        $order = Order::find($id);
        if ($order) {
            return $order->update($data);
        }
        return false;
    }

    /**
     * Delete an order.
     */
    /**
     * Delete an order.
     */
    public function delete(int $id): bool
    {
        $order = Order::find($id);
        if ($order) {
            return $order->delete();
        }
        return false;
    }

    public function findForUser($userId)
    {
        return Order::where('user_id', $userId)->orderBy('id', 'desc')->paginate(10);
    }

    public function findForAdmin(array $filters)
    {
        $query = Order::query();

        if (isset($filters['order_status'])) {
            $query->where('order_status', $filters['order_status']);
        } else {
            // order status should not be PENDING
            $query->where('order_status', '!=', 'PENDING');
        }

        if (isset($filters['date_from']) && isset($filters['date_to'])) {
            $query->whereBetween('order_date', [$filters['date_from'], $filters['date_to']]);
        } elseif (isset($filters['date_from'])) {
            $query->whereBetween('order_date', [$filters['date_from'], date('Y-m-d H:i:s')]);
        }

        if (isset($filters['order_date'])) {
            $query->where('order_date', 'LIKE', '%' . $filters['order_date'] . '%');
        }

        if (isset($filters['order_number'])) {
            $query->where('order_number', 'LIKE', '%' . $filters['order_number'] . '%');
        }

        if (isset($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        return $query->orderBy('id', 'desc')->get();
    }

    public function findByOrderNumberAndUser($orderNumber, $userId)
    {
        return Order::where('order_number', $orderNumber)
            ->where('user_id', $userId)
            ->first();
    }

    public function updateByOrderNumber($orderNumber, array $data)
    {
        return Order::where('order_number', $orderNumber)->update($data);
    }

    public function updateStatus($orderNumber, $userId, $status)
    {
        return Order::where([
            'order_number' => $orderNumber,
            'user_id' => $userId
        ])->update(['order_status' => $status]);
    }
}
