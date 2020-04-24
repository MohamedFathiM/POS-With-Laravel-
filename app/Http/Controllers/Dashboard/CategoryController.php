<?php

namespace App\Http\Controllers\Dashboard;

use App\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::search($request)->paginate();

        return view('dashboard.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('dashboard.categories.create');
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'ar.*' => 'required|unique:category_translations',
            'en.*' => 'required|unique:category_translations'
        ]);
        Category::create($data);
        session()->flash('success',Lang::get('site.added_successfully'));

        return redirect()->route('dashboard.categories.index');
    }

    public function edit(Category $category)
    {
        return view('dashboard.categories.edit',compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'ar.*' => 'required|unique:category_translations,name,' . $category->id,
            'en.*' => 'required|unique:category_translations,name,' . $category->id,
        ]);

        $category->update($data);
        session()->flash('success',Lang::get('site.updated_successfully'));

        return redirect()->route('dashboard.categories.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        //
    }
}
