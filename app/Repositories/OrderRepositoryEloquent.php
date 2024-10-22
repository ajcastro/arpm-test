<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Order;

class OrderRepositoryEloquent implements OrderRepositoryInterface
{
    public function getOrderData(): array
    {
        // Here I applied DDD(Domain-Driven Design) and created a repository for the order aggregate.
        // Here we load the whole order aggregate together with its items entities.
        // The Order entity is our aggregate root here.
        // Using this pattern improves our code readability and maintainability.

        // Performance:
        // We are sorting the orders by completed_at performed in database level which leads to better application performance.
        // We also remove unnecessary loops in our previous code.

        // In the future we can also return array of Data Transfer Objects(DTOs) instead of just array to enforce data structure
        // when other layers used this method.
        return Order::query()
            ->with('items')
            ->orderByDesc('completed_at')
            ->get()
            ->map(function (Order $order) {
                return [
                    'order_id' => $order->id,
                    'customer_name' => $order->customer->name,
                    'total_amount' => $order->getTotalAmount(),
                    'items_count' => $order->getItemsCount(),
                    'last_added_to_cart' => $order->getLastAddedToCart(),
                    'completed_order_exists' => $order->completedOrderExists(),
                    'created_at' => $order->created_at,
                ];
            })
            ->all();
    }
}
