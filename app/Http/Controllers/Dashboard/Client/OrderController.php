<?php

namespace App\Http\Controllers\Dashboard\Client;

use App\Category;
use App\Client;
use App\Http\Controllers\Controller;
use App\Order;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class OrderController extends Controller
{
    public function create(Client $client)
    {
        $categories = Category::with('products')->paginate();
        $orders = $client->orders()->with('products')->paginate();

        return view('dashboard.clients.orders.create', compact('client', 'categories', 'orders'));
    }


    public function store(Request $request, Client $client)
    {
        $data = $request->validate([
            'products' => 'array',
            'products.*' => 'required',
            'products.*.quantity' => 'min:1'
        ]);
        $total_price = 0;

        $order =  $client->orders()->create();
        $order->products()->attach($data['products']);

        foreach ($data['products'] as $productId => $quantity) {
            $product = Product::findOrFail($productId);
            $total_price += ($product->sale_price * $quantity['quantity']);
            $product->update(['stock' => $product->stock - $quantity['quantity']]);
        }
        $order->update(['total_price' => $total_price]);

        session()->flash('success', Lang::get('site.added_successfully'));

        return redirect()->route('dashboard.orders.index');
    }


    public function edit(Client $client, Order $order)
    {
        $categories = Category::with('products')->paginate();
        $orders = $client->orders()->with('products')->paginate();

        return view('dashboard.clients.orders.edit', compact('order', 'client', 'categories', 'orders'));
    }


    public function update(Request $request, Client $client, Order $order)
    {
        $data = $request->validate([
            'products' => 'array',
            'products.*' => 'required',
            'products.*.quantity' => 'min:1'
        ]);

        $total_price = 0;


        foreach ($order->products  as $product) {
            $product->update([
                'stock' => $product->stock + $product->pivot->quantity,
            ]);
        }

        $order->products()->sync($data['products']);

        foreach ($data['products'] as $productId => $quantity) {
            $product = Product::findOrFail($productId);
            $total_price += ($product->sale_price * $quantity['quantity']);
            $product->update(['stock' => $product->stock - $quantity['quantity']]);
        }
        $order->update(['total_price' => $total_price]);

        session()->flash('success', Lang::get('site.updated_successfully'));

        return redirect()->route('dashboard.orders.index');
    }
}
