<?php

namespace App\Services;

use App\Models\PaymentSetting;

class PaymentCalculationService
{
    /**
     * Calculate all charges for an order
     */
    public function calculateOrderCharges($subtotal, $orderData = [])
    {
        $charges = [
            'subtotal' => $subtotal,
            'service_charge' => 0,
            'tax_amount' => 0,
            'delivery_fee' => 0,
            'discount_amount' => 0,
            'total' => $subtotal,
            'breakdown' => []
        ];

        // Calculate service charge
        $serviceChargeSetting = PaymentSetting::getByKey('service_charge');
        if ($serviceChargeSetting && $serviceChargeSetting->conditionsMet($orderData)) {
            $charges['service_charge'] = $serviceChargeSetting->calculateCharge($subtotal);
            $charges['breakdown']['service_charge'] = [
                'name' => $serviceChargeSetting->name,
                'value' => $serviceChargeSetting->value,
                'type' => $serviceChargeSetting->type,
                'amount' => $charges['service_charge'],
                'description' => $serviceChargeSetting->full_description
            ];
        }

        // Calculate tax
        $taxSetting = PaymentSetting::getByKey('tax_rate');
        if ($taxSetting && $taxSetting->conditionsMet($orderData)) {
            $charges['tax_amount'] = $taxSetting->calculateCharge($subtotal);
            $charges['breakdown']['tax'] = [
                'name' => $taxSetting->name,
                'value' => $taxSetting->value,
                'type' => $taxSetting->type,
                'amount' => $charges['tax_amount'],
                'description' => $taxSetting->full_description
            ];
        }

        // Calculate delivery fee
        $deliveryFeeSetting = PaymentSetting::getByKey('delivery_fee');
        if ($deliveryFeeSetting && $deliveryFeeSetting->conditionsMet($orderData)) {
            $charges['delivery_fee'] = $deliveryFeeSetting->calculateCharge($subtotal);
            $charges['breakdown']['delivery_fee'] = [
                'name' => $deliveryFeeSetting->name,
                'value' => $deliveryFeeSetting->value,
                'type' => $deliveryFeeSetting->type,
                'amount' => $charges['delivery_fee'],
                'description' => $deliveryFeeSetting->full_description
            ];
        }

        // Calculate total
        $charges['total'] = $charges['subtotal'] + 
                           $charges['service_charge'] + 
                           $charges['tax_amount'] + 
                           $charges['delivery_fee'] - 
                           $charges['discount_amount'];

        return $charges;
    }

    /**
     * Apply discount to order
     */
    public function applyDiscount($orderData, $discountCode = null)
    {
        if (!$discountCode) {
            return $orderData;
        }

        // Get discount setting
        $discountSetting = PaymentSetting::getByKey('discount_' . $discountCode);
        if (!$discountSetting || !$discountSetting->conditionsMet($orderData)) {
            return $orderData;
        }

        $discountAmount = $discountSetting->calculateCharge($orderData['subtotal']);
        
        $orderData['discount_amount'] = $discountAmount;
        $orderData['discount_code'] = $discountCode;
        $orderData['total'] -= $discountAmount;
        
        $orderData['breakdown']['discount'] = [
            'name' => $discountSetting->name,
            'value' => $discountSetting->value,
            'type' => $discountSetting->type,
            'amount' => $discountAmount,
            'description' => $discountSetting->full_description
        ];

        return $orderData;
    }

    /**
     * Get all active payment settings
     */
    public function getActiveSettings()
    {
        return PaymentSetting::active()->get();
    }

    /**
     * Get setting by key
     */
    public function getSetting($key)
    {
        return PaymentSetting::getByKey($key);
    }

    /**
     * Update payment setting
     */
    public function updateSetting($key, $data)
    {
        $setting = PaymentSetting::where('key', $key)->first();
        
        if (!$setting) {
            return PaymentSetting::create(array_merge(['key' => $key], $data));
        }

        $setting->update($data);
        return $setting;
    }

    /**
     * Get formatted breakdown for display
     */
    public function getFormattedBreakdown($breakdown)
    {
        $formatted = [];
        
        foreach ($breakdown as $key => $item) {
            $formatted[] = [
                'name' => $item['name'],
                'amount' => '₦' . number_format($item['amount'], 2),
                'description' => $item['description']
            ];
        }
        
        return $formatted;
    }

    /**
     * Validate discount code
     */
    public function validateDiscountCode($code, $orderData = [])
    {
        $discountSetting = PaymentSetting::getByKey('discount_' . $code);
        
        if (!$discountSetting) {
            return ['valid' => false, 'message' => 'Invalid discount code'];
        }

        if (!$discountSetting->conditionsMet($orderData)) {
            return ['valid' => false, 'message' => 'Discount code conditions not met'];
        }

        return [
            'valid' => true, 
            'message' => 'Discount code applied',
            'setting' => $discountSetting
        ];
    }
} 