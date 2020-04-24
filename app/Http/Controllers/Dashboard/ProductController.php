<?php

namespace App\Http\Controllers\Dashboard;

use App\Category;
use App\Http\Controllers\Controller;
use App\product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        $categories = Category::all();

        $products = Product::search($request)->paginate();

        return view('dashboard.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('dashboard.products.create', compact('categories'));
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'ar.name' => 'required|unique:product_translations,name',
            'en.name' => 'required|unique:product_translations,name',
            'ar.description' => '',
            'en.description' => '',
            'image' => '',
            'purchase_price' => 'required',
            'sale_price' => 'required',
            'stock' => 'required',
            'category_id' => 'required',
            'category_id.*' => 'exists:categories,id',
        ]);


        if ($request->image) {
            $image = $request->image->hashName();
            $data['image'] = $image;
            Image::make($request->image)->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save(public_path('uploads/product_images/' . $image));
        }

        Product::create($data);

        session()->flash('success', Lang::get('site.added_successfully'));

        return redirect()->route('dashboard.products.index');
    }

    public function edit(product $product)
    {
        $categories = Category::all();

        return view('dashboard.products.edit', compact('product', 'categories'));
    }


    public function update(Request $request, product $product)
    {
        $data = $request->validate([
            'ar.name' => 'required|unique:product_translations,name,' . $product->id,
            'en.name' => 'required|unique:product_translations,name,' . $product->id,
            'ar.description' => '',
            'en.description' => '',
            'image' => '',
            'purchase_price' => 'required',
            'sale_price' => 'required',
            'stock' => 'required',
            'category_id' => 'required',
            'category_id.*' => 'exists:categories,id',
        ]);

        if ($product->image != 'default.jpg') {
            Storage::disk('public_uploads')->delete('user_images/' . $product->image);
        }

        if ($request->image) {
            $image = $request->image->hashName();
            $data['image'] = $image;
            Image::make($request->image)->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save(public_path('uploads/product_images/' . $image));
        }

        $product->update($data);

        session()->flash('success', Lang::get('site.updated_successfully'));

        return redirect()->route('dashboard.products.index');
    }

    public function destroy(product $product)
    {
        if ($product->image != 'default.jpg') {
            Storage::disk('public_uploads')->delete('user_images/' . $product->image);
        }

        $product->delete();
        session()->flash('success', Lang::get('site.deleted_successfully'));

        return redirect()->route('dashboard.products.index');
    }
}
