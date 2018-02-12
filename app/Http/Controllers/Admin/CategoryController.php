<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Category;


class CategoryController extends Controller
{
    //
    public function index()
    {
        $categories = Category::all();
        return view('admin.category.index', ['categories' => $categories]);
    }

    public function create()
    {
        return view('admin.category.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required'
        ]);
        Category::create($request->all());
        return redirect()->route('category.index');
    }

    public function edit($id)
    {
        $category = Category::find($id);
        $statusList = Category::statusList();
        return view('admin.category.edit', compact('category', 'statusList'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required'
        ]);
        $category = Category::find($id);
        $category->update($request->all());
        return redirect()->route('category.index');
    }

    public function destroy($id)
    {
        Category::find($id)->delete();
        return redirect()->route('category.index');
    }
}
