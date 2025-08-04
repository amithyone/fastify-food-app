<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppVerificationService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Handle WhatsApp webhook verification
     */
    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        Log::info('WhatsApp webhook verification request', [
            'mode' => $mode,
            'token' => $token,
            'challenge' => $challenge
        ]);

        $result = $this->whatsappService->verifyWebhook($mode, $token, $challenge);

        if ($result['success']) {
            return response($result['challenge'], 200);
        } else {
            return response('Forbidden', 403);
        }
    }

    /**
     * Handle incoming WhatsApp messages
     */
    public function webhook(Request $request)
    {
        Log::info('WhatsApp webhook received', [
            'body' => $request->all()
        ]);

        $data = $request->all();

        // Handle different types of webhook events
        if (isset($data['entry'][0]['changes'][0]['value']['messages'])) {
            $messages = $data['entry'][0]['changes'][0]['value']['messages'];
            
            foreach ($messages as $message) {
                $this->handleIncomingMessage($message);
            }
        }

        // Handle message status updates
        if (isset($data['entry'][0]['changes'][0]['value']['statuses'])) {
            $statuses = $data['entry'][0]['changes'][0]['value']['statuses'];
            
            foreach ($statuses as $status) {
                $this->handleMessageStatus($status);
            }
        }

        return response('OK', 200);
    }

    /**
     * Handle incoming message
     */
    protected function handleIncomingMessage(array $message): void
    {
        $from = $message['from'];
        $type = $message['type'] ?? 'text';
        $timestamp = $message['timestamp'];

        Log::info('Processing incoming WhatsApp message', [
            'from' => $from,
            'type' => $type,
            'timestamp' => $timestamp
        ]);

        switch ($type) {
            case 'text':
                $this->handleTextMessage($from, $message['text']['body']);
                break;
            case 'button':
                $this->handleButtonMessage($from, $message['button']['text']);
                break;
            case 'interactive':
                $this->handleInteractiveMessage($from, $message['interactive']);
                break;
            default:
                Log::info('Unhandled message type', ['type' => $type]);
                break;
        }
    }

    /**
     * Handle text message
     */
    protected function handleTextMessage(string $from, string $text): void
    {
        Log::info('Processing text message', [
            'from' => $from,
            'text' => $text
        ]);

        // Handle different commands
        $text = strtolower(trim($text));

        if (str_starts_with($text, 'order')) {
            $this->handleOrderCommand($from, $text);
        } elseif (str_starts_with($text, 'menu')) {
            $this->handleMenuCommand($from);
        } elseif (str_starts_with($text, 'help')) {
            $this->handleHelpCommand($from);
        } else {
            $this->sendDefaultResponse($from);
        }
    }

    /**
     * Handle order command
     */
    protected function handleOrderCommand(string $from, string $text): void
    {
        // Extract order number from text (e.g., "order 12345")
        preg_match('/order\s+(\w+)/', $text, $matches);
        $orderNumber = $matches[1] ?? null;

        if ($orderNumber) {
            // Look up order in database
            $order = \App\Models\Order::where('order_number', $orderNumber)->first();
            
            if ($order) {
                $message = "Order #{$order->order_number}\n";
                $message .= "Status: {$order->status}\n";
                $message .= "Total: ₦{$order->total_amount}\n";
                $message .= "Created: " . $order->created_at->format('M d, Y H:i');
            } else {
                $message = "Order #{$orderNumber} not found. Please check the order number and try again.";
            }
        } else {
            $message = "Please provide an order number. Example: 'order 12345'";
        }

        $this->whatsappService->sendMessage($from, $message);
    }

    /**
     * Handle menu command
     */
    protected function handleMenuCommand(string $from): void
    {
        $message = "🍽️ *Our Menu*\n\n";
        $message .= "Reply with the number to order:\n\n";
        $message .= "1. Jollof Rice & Plantain - ₦2,500\n";
        $message .= "2. Amala & Ewedu - ₦2,800\n";
        $message .= "3. Pounded Yam & Egusi - ₦3,000\n";
        $message .= "4. Fried Rice & Chicken - ₦3,500\n\n";
        $message .= "To place an order, visit our website or call us directly.";

        $this->whatsappService->sendMessage($from, $message);
    }

    /**
     * Handle help command
     */
    protected function handleHelpCommand(string $from): void
    {
        $message = "🤝 *How can we help you?*\n\n";
        $message .= "Available commands:\n";
        $message .= "• *menu* - View our menu\n";
        $message .= "• *order [number]* - Check order status\n";
        $message .= "• *help* - Show this help message\n\n";
        $message .= "For immediate assistance, call us or visit our website.";

        $this->whatsappService->sendMessage($from, $message);
    }

    /**
     * Send default response
     */
    protected function sendDefaultResponse(string $from): void
    {
        $message = "👋 Welcome to our restaurant!\n\n";
        $message .= "Type *menu* to see our offerings\n";
        $message .= "Type *order [number]* to check order status\n";
        $message .= "Type *help* for more options";

        $this->whatsappService->sendMessage($from, $message);
    }

    /**
     * Handle button message
     */
    protected function handleButtonMessage(string $from, string $buttonText): void
    {
        Log::info('Processing button message', [
            'from' => $from,
            'button_text' => $buttonText
        ]);

        // Handle different button actions
        switch (strtolower($buttonText)) {
            case 'view menu':
                $this->handleMenuCommand($from);
                break;
            case 'order status':
                $message = "Please provide your order number. Example: 'order 12345'";
                $this->whatsappService->sendMessage($from, $message);
                break;
            default:
                $this->sendDefaultResponse($from);
                break;
        }
    }

    /**
     * Handle interactive message
     */
    protected function handleInteractiveMessage(string $from, array $interactive): void
    {
        Log::info('Processing interactive message', [
            'from' => $from,
            'interactive' => $interactive
        ]);

        if (isset($interactive['list_reply'])) {
            $selectedId = $interactive['list_reply']['id'];
            $this->handleListSelection($from, $selectedId);
        } elseif (isset($interactive['button_reply'])) {
            $buttonText = $interactive['button_reply']['title'];
            $this->handleButtonMessage($from, $buttonText);
        }
    }

    /**
     * Handle list selection
     */
    protected function handleListSelection(string $from, string $selectedId): void
    {
        switch ($selectedId) {
            case 'menu':
                $this->handleMenuCommand($from);
                break;
            case 'order_status':
                $message = "Please provide your order number. Example: 'order 12345'";
                $this->whatsappService->sendMessage($from, $message);
                break;
            case 'contact':
                $message = "📞 Contact Us\n\n";
                $message .= "Phone: +234 XXX XXX XXXX\n";
                $message .= "Email: info@restaurant.com\n";
                $message .= "Address: 123 Main Street, Lagos";
                $this->whatsappService->sendMessage($from, $message);
                break;
            default:
                $this->sendDefaultResponse($from);
                break;
        }
    }

    /**
     * Handle message status updates
     */
    protected function handleMessageStatus(array $status): void
    {
        $messageId = $status['id'];
        $statusType = $status['status'];
        $timestamp = $status['timestamp'];

        Log::info('Message status update', [
            'message_id' => $messageId,
            'status' => $statusType,
            'timestamp' => $timestamp
        ]);

        // You can store message status in database for tracking
        // or handle different status types (sent, delivered, read, failed)
    }

    /**
     * Send order confirmation via WhatsApp
     */
    public function sendOrderConfirmation(string $phoneNumber, array $orderData): array
    {
        return $this->whatsappService->sendOrderConfirmation($phoneNumber, $orderData);
    }

    /**
     * Send order status update via WhatsApp
     */
    public function sendOrderStatusUpdate(string $phoneNumber, array $orderData): array
    {
        return $this->whatsappService->sendOrderStatusUpdate($phoneNumber, $orderData);
    }

    /**
     * Send OTP via WhatsApp
     */
    public function sendOTP(string $phoneNumber, string $otp): array
    {
        return $this->whatsappService->sendOTP($phoneNumber, $otp);
    }
} 