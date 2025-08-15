<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class OrderNotifications extends Component
{
    public $restaurant;
    public $newOrders = [];
    public $unreadCount = 0;
    public $showNotifications = false;
    public $lastOrderId = 0;
    
    protected $listeners = ['orderCreated' => 'checkForNewOrders'];

    public function mount($restaurantSlug = null)
    {
        if ($restaurantSlug) {
            $this->restaurant = Restaurant::where('slug', $restaurantSlug)->first();
        } elseif (Auth::check()) {
            // Get the user's managed restaurant
            $user = Auth::user();
            // Check if user is a manager (you may need to adjust this based on your user model)
            if (method_exists($user, 'isManager') && $user->isManager()) {
                $this->restaurant = $user->managedRestaurant ?? null;
            }
        }
        
        if ($this->restaurant) {
            $this->lastOrderId = $this->restaurant->orders()->max('id') ?? 0;
            $this->loadUnreadOrders();
        }
    }

    public function loadUnreadOrders()
    {
        if (!$this->restaurant) return;
        
        $this->newOrders = $this->restaurant->orders()
            ->where('id', '>', $this->lastOrderId)
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name ?? 'Guest',
                    'total' => $order->total,
                    'status' => $order->status,
                    'created_at' => $order->created_at->diffForHumans(),
                    'items_count' => $order->orderItems->count(),
                    'delivery_method' => $order->delivery_method
                ];
            })
            ->toArray();
            
        $this->unreadCount = count($this->newOrders);
    }

    public function checkForNewOrders()
    {
        if (!$this->restaurant) return;
        
        $latestOrderId = $this->restaurant->orders()->max('id') ?? 0;
        
        if ($latestOrderId > $this->lastOrderId) {
            $this->loadUnreadOrders();
            $this->lastOrderId = $latestOrderId;
            
            // Show notification sound and visual alert
            $this->dispatch('newOrderNotification', [
                'count' => $this->unreadCount,
                'message' => "You have {$this->unreadCount} new order(s)!"
            ]);
        }
    }

    public function markAsRead($orderId)
    {
        // Remove the order from new orders list
        $this->newOrders = array_filter($this->newOrders, function ($order) use ($orderId) {
            return $order['id'] != $orderId;
        });
        
        $this->unreadCount = count($this->newOrders);
    }

    public function markAllAsRead()
    {
        $this->newOrders = [];
        $this->unreadCount = 0;
    }

    public function toggleNotifications()
    {
        $this->showNotifications = !$this->showNotifications;
    }

    public function viewOrder($orderId)
    {
        $this->markAsRead($orderId);
        $this->showNotifications = false;
        
        // Redirect to order details
        return redirect()->route('restaurant.orders.show', [
            'slug' => $this->restaurant->slug,
            'order' => $orderId
        ]);
    }

    public function render()
    {
        return view('livewire.order-notifications');
    }
}
