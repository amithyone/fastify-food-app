<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\PromotionPayment;
use App\Models\PromotionPlan;
use App\Models\PayVibeTransaction;
use App\Services\PayVibeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

class PayVibeVirtualAccountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up test configuration
        Config::set('services.payvibe.test_mode', true);
        Config::set('services.payvibe.api_key', 'test_api_key');
        Config::set('services.payvibe.secret_key', 'test_secret_key');
        Config::set('services.payvibe.base_url', 'https://payvibeapi.six3tech.com/api');
    }

    /** @test */
    public function it_can_create_virtual_account_transaction()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'phone_number' => '1234567890'
        ]);

        $restaurant = Restaurant::create([
            'name' => 'Test Restaurant',
            'description' => 'Test Description',
            'address' => 'Test Address',
            'city' => 'Test City',
            'state' => 'Test State',
            'user_id' => $user->id,
            'slug' => 'test-restaurant',
            'cuisine_type' => 'Test Cuisine',
            'whatsapp_number' => '1234567890',
            'phone_number' => '1234567890',
            'email' => 'restaurant@example.com'
        ]);

        $plan = PromotionPlan::create([
            'name' => 'Test Plan',
            'description' => 'Test Plan Description',
            'slug' => 'test-plan',
            'price' => 5000,
            'duration_days' => 30,
            'features' => ['feature1', 'feature2'],
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        $payment = PromotionPayment::create([
            'restaurant_id' => $restaurant->id,
            'promotion_plan_id' => $plan->id,
            'payment_reference' => 'TEST-VA-TXN',
            'account_number' => 'ACC-TXN',
            'amount' => 5000,
            'status' => 'pending',
            'expires_at' => now()->addHours(24)
        ]);

        $transaction = PayVibeTransaction::create([
            'payment_id' => $payment->id,
            'reference' => 'TEST-VA-TXN',
            'amount' => 5000,
            'status' => 'pending',
            'authorization_url' => null,
            'access_code' => null,
            'metadata' => [
                'restaurant_id' => $restaurant->id,
                'user_id' => $user->id,
                'payment_type' => 'promotion_payment',
                'virtual_account' => [
                    'account_number' => '1234567890',
                    'bank_name' => 'Test Bank',
                    'account_name' => 'Test Account',
                    'expires_at' => '2025-08-04T22:22:40Z'
                ]
            ]
        ]);

        $this->assertDatabaseHas('pay_vibe_transactions', [
            'reference' => 'TEST-VA-TXN',
            'amount' => 5000,
            'status' => 'pending'
        ]);

        $this->assertEquals('TEST-VA-TXN', $transaction->reference);
        $this->assertTrue($transaction->isPending());
        $this->assertArrayHasKey('virtual_account', $transaction->metadata);
        $this->assertEquals('1234567890', $transaction->metadata['virtual_account']['account_number']);
    }

    /** @test */
    public function it_can_generate_webhook_hash_for_virtual_account()
    {
        $service = new PayVibeService();
        
        $data = [
            'reference' => 'TEST-VA-123',
            'amount' => 10000,
            'status' => 'successful',
            'account_number' => '1234567890',
            'bank_name' => 'Test Bank'
        ];

        $hash = $service->generateWebhookHash($data);
        
        $this->assertIsString($hash);
        $this->assertNotEmpty($hash);
        $this->assertTrue($service->verifyWebhookHash($data, $hash));
    }

    /** @test */
    public function it_can_verify_webhook_hash_for_virtual_account()
    {
        $service = new PayVibeService();
        
        $data = [
            'reference' => 'TEST-VA-456',
            'amount' => 20000,
            'status' => 'failed',
            'account_number' => '0987654321',
            'bank_name' => 'Another Bank'
        ];

        $correctHash = $service->generateWebhookHash($data);
        $incorrectHash = 'incorrect_hash';

        $this->assertTrue($service->verifyWebhookHash($data, $correctHash));
        $this->assertFalse($service->verifyWebhookHash($data, $incorrectHash));
    }

    /** @test */
    public function it_can_get_formatted_amount_for_virtual_account()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'phone_number' => '1234567890'
        ]);

        $restaurant = Restaurant::create([
            'name' => 'Test Restaurant',
            'description' => 'Test Description',
            'address' => 'Test Address',
            'city' => 'Test City',
            'state' => 'Test State',
            'user_id' => $user->id,
            'slug' => 'test-restaurant',
            'cuisine_type' => 'Test Cuisine',
            'whatsapp_number' => '1234567890',
            'phone_number' => '1234567890',
            'email' => 'restaurant@example.com'
        ]);

        $plan = PromotionPlan::create([
            'name' => 'Test Plan',
            'description' => 'Test Plan Description',
            'slug' => 'test-plan',
            'price' => 15000,
            'duration_days' => 30,
            'features' => ['feature1', 'feature2'],
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        $payment = PromotionPayment::create([
            'restaurant_id' => $restaurant->id,
            'promotion_plan_id' => $plan->id,
            'payment_reference' => 'TEST-VA-AMOUNT',
            'account_number' => 'ACC-AMOUNT',
            'amount' => 15000,
            'status' => 'pending',
            'expires_at' => now()->addHours(24)
        ]);

        $transaction = PayVibeTransaction::create([
            'payment_id' => $payment->id,
            'reference' => 'TEST-VA-AMOUNT',
            'amount' => 15000,
            'status' => 'pending',
            'authorization_url' => null,
            'access_code' => null,
            'metadata' => [
                'restaurant_id' => $restaurant->id,
                'user_id' => $user->id,
                'payment_type' => 'promotion_payment',
                'virtual_account' => [
                    'account_number' => '1234567890',
                    'bank_name' => 'Test Bank',
                    'account_name' => 'Test Account'
                ]
            ]
        ]);

        $this->assertEquals('₦150', $transaction->formatted_amount);
    }

    /** @test */
    public function it_can_get_status_badge_for_virtual_account()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'phone_number' => '1234567890'
        ]);

        $restaurant = Restaurant::create([
            'name' => 'Test Restaurant',
            'description' => 'Test Description',
            'address' => 'Test Address',
            'city' => 'Test City',
            'state' => 'Test State',
            'user_id' => $user->id,
            'slug' => 'test-restaurant',
            'cuisine_type' => 'Test Cuisine',
            'whatsapp_number' => '1234567890',
            'phone_number' => '1234567890',
            'email' => 'restaurant@example.com'
        ]);

        $plan = PromotionPlan::create([
            'name' => 'Test Plan',
            'description' => 'Test Plan Description',
            'slug' => 'test-plan',
            'price' => 5000,
            'duration_days' => 30,
            'features' => ['feature1', 'feature2'],
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        $payment = PromotionPayment::create([
            'restaurant_id' => $restaurant->id,
            'promotion_plan_id' => $plan->id,
            'payment_reference' => 'TEST-VA-BADGE',
            'account_number' => 'ACC-BADGE',
            'amount' => 5000,
            'status' => 'pending',
            'expires_at' => now()->addHours(24)
        ]);

        $transaction = PayVibeTransaction::create([
            'payment_id' => $payment->id,
            'reference' => 'TEST-VA-BADGE',
            'amount' => 5000,
            'status' => 'pending',
            'authorization_url' => null,
            'access_code' => null,
            'metadata' => [
                'restaurant_id' => $restaurant->id,
                'user_id' => $user->id,
                'payment_type' => 'promotion_payment',
                'virtual_account' => [
                    'account_number' => '1234567890',
                    'bank_name' => 'Test Bank',
                    'account_name' => 'Test Account'
                ]
            ]
        ]);

        $badgeClass = $transaction->status_badge;
        
        $this->assertStringContainsString('bg-yellow-100', $badgeClass);
        $this->assertStringContainsString('text-yellow-800', $badgeClass);
    }
} 