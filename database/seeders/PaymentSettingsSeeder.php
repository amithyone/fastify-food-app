<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentSetting;

class PaymentSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'service_charge',
                'name' => 'Service Charge',
                'description' => 'Service charge applied to all orders',
                'value' => 5.0, // 5%
                'type' => 'percentage',
                'status' => 'active',
                'conditions' => [
                    'min_order_amount' => 1000 // Minimum order amount of ₦1,000
                ]
            ],
            [
                'key' => 'tax_rate',
                'name' => 'Value Added Tax (VAT)',
                'description' => 'VAT applied to all orders',
                'value' => 7.5, // 7.5%
                'type' => 'percentage',
                'status' => 'active',
                'conditions' => null
            ],
            [
                'key' => 'delivery_fee',
                'name' => 'Delivery Fee',
                'description' => 'Standard delivery fee',
                'value' => 500, // ₦500
                'type' => 'fixed',
                'status' => 'active',
                'conditions' => [
                    'min_order_amount' => 2000 // Free delivery for orders above ₦2,000
                ]
            ],
            [
                'key' => 'delivery_fee_free',
                'name' => 'Free Delivery',
                'description' => 'Free delivery for orders above ₦2,000',
                'value' => 0,
                'type' => 'fixed',
                'status' => 'active',
                'conditions' => [
                    'min_order_amount' => 2000
                ]
            ],
            [
                'key' => 'discount_welcome10',
                'name' => 'Welcome Discount',
                'description' => '10% off for new customers',
                'value' => 10.0, // 10%
                'type' => 'percentage',
                'status' => 'active',
                'conditions' => [
                    'min_order_amount' => 1000
                ]
            ],
            [
                'key' => 'discount_loyalty5',
                'name' => 'Loyalty Discount',
                'description' => '5% off for returning customers',
                'value' => 5.0, // 5%
                'type' => 'percentage',
                'status' => 'active',
                'conditions' => [
                    'min_order_amount' => 1500
                ]
            ],
            [
                'key' => 'service_charge_card',
                'name' => 'Card Payment Service Charge',
                'description' => 'Additional service charge for card payments',
                'value' => 2.5, // 2.5%
                'type' => 'percentage',
                'status' => 'active',
                'conditions' => [
                    'payment_methods' => ['card', 'transfer']
                ]
            ]
        ];

        foreach ($settings as $setting) {
            PaymentSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Payment settings seeded successfully!');
    }
}
