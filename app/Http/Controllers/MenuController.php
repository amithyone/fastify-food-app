<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Story;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $categories = Category::with('menuItems')->where('is_active', true)->get();
        $menuItems = MenuItem::with('category')->where('is_available', true)->get();
        $stories = Story::active()->ordered()->get();
        
        return view('menu.index', compact('categories', 'menuItems', 'stories'));
    }

    public function show($id)
    {
        $menuItem = MenuItem::with('category')->findOrFail($id);
        return view('menu.show', compact('menuItem'));
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        
        if ($query) {
            $menuItems = MenuItem::with('category')
                ->where('is_available', true)
                ->where(function($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%");
                })
                ->get();
        } else {
            $menuItems = MenuItem::with('category')->where('is_available', true)->get();
        }
        
        return response()->json($menuItems);
    }

    public function getMenuItems(Request $request)
    {
        $categoryId = $request->get('category_id');
        
        if ($categoryId && $categoryId !== 'all') {
            $menuItems = MenuItem::with('category')
                ->where('category_id', $categoryId)
                ->where('is_available', true)
                ->get();
        } else {
            $menuItems = MenuItem::with('category')
                ->where('is_available', true)
                ->get();
        }
        
        return response()->json($menuItems);
    }

    public function getCategories()
    {
        $categories = Category::where('is_active', true)->get();
        return response()->json($categories);
    }

    public function items(Request $request)
    {
        $categoryId = $request->get('category_id');
        
        if ($categoryId && $categoryId !== 'all') {
            $menuItems = MenuItem::with('category')
                ->where('category_id', $categoryId)
                ->where('is_available', true)
                ->get();
        } else {
            $menuItems = MenuItem::with('category')
                ->where('is_available', true)
                ->get();
        }
        
        return response()->json($menuItems);
    }
}
