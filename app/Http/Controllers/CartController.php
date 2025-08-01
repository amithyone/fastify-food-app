<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        $cart = Session::get('cart', []);
        $cartItems = [];
        $total = 0;

        foreach ($cart as $restaurantId => $items) {
            $restaurant = Restaurant::find($restaurantId);
            if ($restaurant) {
                $restaurantItems = [];
                $restaurantTotal = 0;

                foreach ($items as $itemId => $quantity) {
                    $menuItem = MenuItem::find($itemId);
                    if ($menuItem && $menuItem->is_available) {
                        $itemTotal = $menuItem->price * $quantity;
                        $restaurantItems[] = [
                            'id' => $menuItem->id,
                            'name' => $menuItem->name,
                            'price' => $menuItem->price,
                            'quantity' => $quantity,
                            'total' => $itemTotal,
                            'image' => $menuItem->image
                        ];
                        $restaurantTotal += $itemTotal;
                    }
                }

                if (!empty($restaurantItems)) {
                    $cartItems[] = [
                        'restaurant' => $restaurant,
                        'items' => $restaurantItems,
                        'total' => $restaurantTotal
                    ];
                    $total += $restaurantTotal;
                }
            }
        }

        // Ensure cartItems is always an array
        if (!is_array($cartItems)) {
            $cartItems = [];
        }

        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $menuItem = MenuItem::findOrFail($request->menu_item_id);
        $cart = Session::get('cart', []);

        // Initialize restaurant cart if not exists
        if (!isset($cart[$menuItem->restaurant_id])) {
            $cart[$menuItem->restaurant_id] = [];
        }

        // Add or update item quantity
        if (isset($cart[$menuItem->restaurant_id][$menuItem->id])) {
            $cart[$menuItem->restaurant_id][$menuItem->id] += $request->quantity;
        } else {
            $cart[$menuItem->restaurant_id][$menuItem->id] = $request->quantity;
        }

        Session::put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart',
            'cart_count' => $this->getCartCount()
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer|min:0'
        ]);

        $cart = Session::get('cart', []);
        $menuItem = MenuItem::findOrFail($request->menu_item_id);

        if ($request->quantity > 0) {
            $cart[$menuItem->restaurant_id][$menuItem->id] = $request->quantity;
        } else {
            unset($cart[$menuItem->restaurant_id][$menuItem->id]);
            if (empty($cart[$menuItem->restaurant_id])) {
                unset($cart[$menuItem->restaurant_id]);
            }
        }

        Session::put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated',
            'cart_count' => $this->getCartCount()
        ]);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id'
        ]);

        $cart = Session::get('cart', []);
        $menuItem = MenuItem::findOrFail($request->menu_item_id);

        unset($cart[$menuItem->restaurant_id][$menuItem->id]);
        if (empty($cart[$menuItem->restaurant_id])) {
            unset($cart[$menuItem->restaurant_id]);
        }

        Session::put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'cart_count' => $this->getCartCount()
        ]);
    }

    public function clear()
    {
        Session::forget('cart');

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared',
            'cart_count' => 0
        ]);
    }

    private function getCartCount()
    {
        $cart = Session::get('cart', []);
        $count = 0;

        foreach ($cart as $restaurantId => $items) {
            foreach ($items as $itemId => $quantity) {
                $count += $quantity;
            }
        }

        return $count;
    }

    public function count()
    {
        return response()->json([
            'count' => $this->getCartCount()
        ]);
    }
} 