<?php

namespace App\Http\Controllers\Dashboard;

use App\Category;
use App\Client;
use App\Http\Controllers\Controller;
use App\Order;
use App\Product;
use App\User;

class DashboardController extends Controller
{
    public function index()
    {
        $clients_count = Client::count();
        $users_count  = User::whereRoleIs('admin')->count();
        $products_count  = Product::count();
        $categories_count  = Category::count();
        $sales_data = Order::selectRaw('
        Year(created_at) year ,
        MONTH(created_at) month ,
        SUM(total_price) sum')->groupBy('month','year')->get();


        return view('dashboard.welcome', compact('clients_count', 'users_count', 'products_count','categories_count','sales_data'));
    }
}
