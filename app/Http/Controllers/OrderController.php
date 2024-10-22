<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\CartItem;
use App\Repositories\OrderRepositoryInterface;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(OrderRepositoryInterface $orderRepository)
    {
        $orderData = $orderRepository->getOrderData();

        return view('orders.index', ['orders' => $orderData]);
    }
}

