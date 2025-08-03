<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\PromotionPayment;
use App\Models\PromotionPlan;
use App\Models\PayVibeTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

class PayVibeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up test configuration
        Config::set('services.payvibe.test_mode', true);
        Config::set('services.payvibe.api_key', 'test_api_key');
        Config::set('services.payvibe.secret_key', 'test_secret_key');
        Config::set('services.payvibe.base_url', 'https://api.payvibe.com');
    }

    /** @test */
    public function it_can_create_payvibe_transaction()
    {
        // Create test data without factories
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
            'payment_reference' => 'TEST-REF-123',
            'account_number' => 'ACC-123456',
            'amount' => 5000, // 50 naira in kobo
            'status' => 'pending',
            'expires_at' => now()->addHours(24)
        ]);

        $transaction = PayVibeTransaction::create([
            'payment_id' => $payment->id,
            'reference' => 'TEST-REF-123',
            'amount' => 5000,
            'status' => 'pending',
            'authorization_url' => 'https://payvibe.com/pay/test',
            'metadata' => [
                'restaurant_id' => $restaurant->id,
                'user_id' => $user->id,
                'payment_type' => 'promotion_payment'
            ]
        ]);

        $this->assertDatabaseHas('pay_vibe_transactions', [
            'reference' => 'TEST-REF-123',
            'amount' => 5000,
            'status' => 'pending'
        ]);

        $this->assertEquals('TEST-REF-123', $transaction->reference);
        $this->assertEquals('₦50', $transaction->formatted_amount);
        $this->assertTrue($transaction->isPending());
    }

    /** @test */
    public function it_can_mark_transaction_as_successful()
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
            'price' => 10000,
            'duration_days' => 30,
            'features' => ['feature1', 'feature2'],
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        $payment = PromotionPayment::create([
            'restaurant_id' => $restaurant->id,
            'promotion_plan_id' => $plan->id,
            'payment_reference' => 'TEST-REF-456',
            'account_number' => 'ACC-456789',
            'amount' => 10000, // 100 naira in kobo
            'status' => 'pending',
            'expires_at' => now()->addHours(24)
        ]);

        $transaction = PayVibeTransaction::create([
            'payment_id' => $payment->id,
            'reference' => 'TEST-REF-456',
            'amount' => 10000,
            'status' => 'pending',
            'authorization_url' => 'https://payvibe.com/pay/test',
            'metadata' => [
                'restaurant_id' => $restaurant->id,
                'user_id' => $user->id,
                'payment_type' => 'promotion_payment'
            ]
        ]);

        $transaction->markAsSuccessful('GATEWAY-REF-123', 10000, ['status' => 'successful']);

        $this->assertTrue($transaction->isSuccessful());
        $this->assertEquals('GATEWAY-REF-123', $transaction->gateway_reference);
        $this->assertEquals(10000, $transaction->amount_received);
    }

    /** @test */
    public function it_can_mark_transaction_as_failed()
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
            'price' => 7500,
            'duration_days' => 30,
            'features' => ['feature1', 'feature2'],
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        $payment = PromotionPayment::create([
            'restaurant_id' => $restaurant->id,
            'promotion_plan_id' => $plan->id,
            'payment_reference' => 'TEST-REF-789',
            'account_number' => 'ACC-789012',
            'amount' => 7500, // 75 naira in kobo
            'status' => 'pending',
            'expires_at' => now()->addHours(24)
        ]);

        $transaction = PayVibeTransaction::create([
            'payment_id' => $payment->id,
            'reference' => 'TEST-REF-789',
            'amount' => 7500,
            'status' => 'pending',
            'authorization_url' => 'https://payvibe.com/pay/test',
            'metadata' => [
                'restaurant_id' => $restaurant->id,
                'user_id' => $user->id,
                'payment_type' => 'promotion_payment'
            ]
        ]);

        $transaction->markAsFailed(['status' => 'failed', 'reason' => 'Insufficient funds']);

        $this->assertTrue($transaction->isFailed());
        $this->assertEquals('failed', $transaction->status);
    }

    /** @test */
    public function it_can_generate_webhook_hash()
    {
        $service = new \App\Services\PayVibeService();
        
        $data = [
            'reference' => 'TEST-REF-123',
            'amount' => 5000,
            'status' => 'successful'
        ];

        $hash = $service->generateWebhookHash($data);
        
        $this->assertIsString($hash);
        $this->assertNotEmpty($hash);
        $this->assertTrue($service->verifyWebhookHash($data, $hash));
    }

    /** @test */
    public function it_can_verify_webhook_hash()
    {
        $service = new \App\Services\PayVibeService();
        
        $data = [
            'reference' => 'TEST-REF-456',
            'amount' => 10000,
            'status' => 'failed'
        ];

        $correctHash = $service->generateWebhookHash($data);
        $incorrectHash = 'incorrect_hash';

        $this->assertTrue($service->verifyWebhookHash($data, $correctHash));
        $this->assertFalse($service->verifyWebhookHash($data, $incorrectHash));
    }

    /** @test */
    public function it_can_get_formatted_amount()
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
            'price' => 25000,
            'duration_days' => 30,
            'features' => ['feature1', 'feature2'],
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        $payment = PromotionPayment::create([
            'restaurant_id' => $restaurant->id,
            'promotion_plan_id' => $plan->id,
            'payment_reference' => 'TEST-REF-999',
            'account_number' => 'ACC-999999',
            'amount' => 25000, // 250 naira in kobo
            'status' => 'pending',
            'expires_at' => now()->addHours(24)
        ]);

        $transaction = PayVibeTransaction::create([
            'payment_id' => $payment->id,
            'reference' => 'TEST-REF-999',
            'amount' => 25000,
            'status' => 'pending',
            'authorization_url' => 'https://payvibe.com/pay/test',
            'metadata' => [
                'restaurant_id' => $restaurant->id,
                'user_id' => $user->id,
                'payment_type' => 'promotion_payment'
            ]
        ]);

        $this->assertEquals('₦250', $transaction->formatted_amount);
    }

    /** @test */
    public function it_can_get_status_badge_class()
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
            'payment_reference' => 'TEST-REF-BADGE',
            'account_number' => 'ACC-BADGE',
            'amount' => 5000,
            'status' => 'pending',
            'expires_at' => now()->addHours(24)
        ]);

        $transaction = PayVibeTransaction::create([
            'payment_id' => $payment->id,
            'reference' => 'TEST-REF-BADGE',
            'amount' => 5000,
            'status' => 'pending',
            'authorization_url' => 'https://payvibe.com/pay/test',
            'metadata' => [
                'restaurant_id' => $restaurant->id,
                'user_id' => $user->id,
                'payment_type' => 'promotion_payment'
            ]
        ]);

        $badgeClass = $transaction->status_badge;
        
        $this->assertStringContainsString('bg-yellow-100', $badgeClass);
        $this->assertStringContainsString('text-yellow-800', $badgeClass);
    }
}
