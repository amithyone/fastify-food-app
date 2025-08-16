<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - AbuJaeat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #ff6b35, #f7931e);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .order-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #ff6b35;
        }
        .order-number {
            font-size: 24px;
            font-weight: bold;
            color: #ff6b35;
            margin-bottom: 10px;
        }
        .restaurant-name {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }
        .item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .item:last-child {
            border-bottom: none;
        }
        .total {
            font-size: 18px;
            font-weight: bold;
            color: #ff6b35;
            border-top: 2px solid #ff6b35;
            padding-top: 10px;
            margin-top: 10px;
        }
        .btn {
            display: inline-block;
            background: #ff6b35;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 5px;
            font-weight: bold;
        }
        .btn:hover {
            background: #e55a2b;
        }
        .qr-section {
            background: #e8f4fd;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎉 Order Confirmed!</h1>
        <p>Thank you for your order, {{ $guestUser->name ?? 'Guest' }}!</p>
    </div>

    <div class="content">
        <div class="order-details">
            <div class="order-number">Order #{{ $order->order_number }}</div>
            <div class="restaurant-name">{{ $order->restaurant->name }}</div>
            
            <div style="margin: 15px 0;">
                <strong>Order Type:</strong> {{ ucfirst($order->order_type) }}<br>
                <strong>Order Date:</strong> {{ $order->created_at->format('M d, Y \a\t g:i A') }}<br>
                <strong>Status:</strong> <span style="color: #ff6b35; font-weight: bold;">{{ ucfirst($order->status) }}</span>
            </div>

            <h3>Order Items:</h3>
            @foreach($order->items as $item)
                <div class="item">
                    <span>{{ $item->quantity }}x {{ $item->menuItem->name }}</span>
                    <span>₦{{ number_format($item->total_price, 2) }}</span>
                </div>
            @endforeach

            <div class="total">
                <div class="item">
                    <span>Subtotal:</span>
                    <span>₦{{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if($order->delivery_fee > 0)
                <div class="item">
                    <span>Delivery Fee:</span>
                    <span>₦{{ number_format($order->delivery_fee, 2) }}</span>
                </div>
                @endif
                <div class="item">
                    <span><strong>Total:</strong></span>
                    <span><strong>₦{{ number_format($order->total, 2) }}</strong></span>
                </div>
            </div>
        </div>

        <div class="qr-section">
            <h3>📱 Quick Access</h3>
            <p>You can track your order anytime using the QR code below or by clicking the button above.</p>
            <p><strong>QR Code will be available on your order page</strong></p>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $orderUrl }}" class="btn">View Order Details</a>
            <a href="{{ $dashboardUrl }}" class="btn">My Order History</a>
        </div>

        <div style="background: #fff3cd; padding: 15px; border-radius: 6px; border-left: 4px solid #ffc107;">
            <h4>🔐 Account Access</h4>
            <p>We've created a guest account for you using your email: <strong>{{ $guestUser->email }}</strong></p>
            <p>You can now:</p>
            <ul>
                <li>Track all your orders</li>
                <li>Reorder your favorite meals</li>
                <li>Get order updates via email</li>
            </ul>
        </div>

        <div class="footer">
            <p>Thank you for choosing AbuJaeat!</p>
            <p>If you have any questions, please contact us at support@abujaeat.com</p>
            <p>© {{ date('Y') }} AbuJaeat. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
