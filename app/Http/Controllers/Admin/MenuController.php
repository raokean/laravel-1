<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Menu;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::all();
        return view('admin.menu.index', compact('menus'));
    }

    public function edit($id)
    {
        $menu = Menu::find($id);
        return view('admin.menu.edit', compact('menu'));
    }

    public function create()
    {
        $typeList = Menu::typeList();
        $pageList = Menu::pageList();
        $menuList = Menu::getSelectTree();

        //dd($menuList);
        $categoryList = Menu::categoryList();
        return view('admin.menu.create',
            compact('typeList', 'pageList', 'menuList', 'categoryList')
        );
    }

    public function store(Request $request)
    {
        dd($request->all());
        $this->validate($request,[
            'title' => 'required',
            'type' => 'required',
        ]);
        Menu::add($request->all());
    }
}
