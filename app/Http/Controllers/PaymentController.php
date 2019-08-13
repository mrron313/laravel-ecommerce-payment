<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $transactionId = uniqid(true) . microtime(true);
        $total = 0;

        if(session('cart')){
            foreach(session('cart') as $id => $details)
                $total += $details['price'] * $details['quantity'];
        }

        $order = $user->orders()->create([
            'total_price' => $total,
            'coupon_amount' => 0,
            'paid_amount' => $total,
            'status' => Order::STATUS_PENDING,
            'transaction_id' => $transactionId,
            'currency' => Order::CURRENCY_BDT,
        ]);

        return view('confirm', compact('order', 'user'));

    }
}
