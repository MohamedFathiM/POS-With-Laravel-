<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Order;
use Illuminate\Support\Facades\Lang;

class OrderController extends Controller
{

    public function index()
    {
        $orders = Order::paginate();

        return view('dashboard.orders.index', compact('orders'));
    }


    public function products(Order $order)
    {
        $products = $order->products()->get();

        return view('dashboard.orders._products', compact('products', 'order'));
    }

    public function destroy(Order $order)
    {
        foreach ($order->products  as $product) {
            $product->update([
                'stock' => $product->stock + $product->pivot->quantity,
            ]);
        }
        $order->delete();
        session()->flash('success', Lang::get('site.deleted_successfully'));

        return redirect()->route('dashboard.orders.index');
    }
}
