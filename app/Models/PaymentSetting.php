<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'description',
        'value',
        'type',
        'status',
        'conditions'
    ];

    protected $casts = [
        'value' => 'decimal:4',
        'conditions' => 'array'
    ];

    /**
     * Get active payment settings
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get setting by key
     */
    public static function getByKey($key)
    {
        return static::where('key', $key)->active()->first();
    }

    /**
     * Get setting value by key
     */
    public static function getValue($key, $default = 0)
    {
        $setting = static::getByKey($key);
        return $setting ? $setting->value : $default;
    }

    /**
     * Check if setting is percentage type
     */
    public function isPercentage()
    {
        return $this->type === 'percentage';
    }

    /**
     * Check if setting is fixed type
     */
    public function isFixed()
    {
        return $this->type === 'fixed';
    }

    /**
     * Calculate charge amount based on subtotal
     */
    public function calculateCharge($subtotal)
    {
        if ($this->isPercentage()) {
            return ($subtotal * $this->value) / 100;
        }
        
        return $this->value;
    }

    /**
     * Check if conditions are met
     */
    public function conditionsMet($orderData = [])
    {
        if (!$this->conditions) {
            return true;
        }

        foreach ($this->conditions as $condition => $value) {
            switch ($condition) {
                case 'min_order_amount':
                    if (($orderData['subtotal'] ?? 0) < $value) {
                        return false;
                    }
                    break;
                case 'max_order_amount':
                    if (($orderData['subtotal'] ?? 0) > $value) {
                        return false;
                    }
                    break;
                case 'payment_methods':
                    if (!in_array($orderData['payment_method'] ?? '', $value)) {
                        return false;
                    }
                    break;
            }
        }

        return true;
    }

    /**
     * Get formatted value for display
     */
    public function getFormattedValueAttribute()
    {
        if ($this->isPercentage()) {
            return $this->value . '%';
        }
        
        return '₦' . number_format($this->value, 2);
    }

    /**
     * Get description with conditions
     */
    public function getFullDescriptionAttribute()
    {
        $description = $this->description ?? $this->name;
        
        if ($this->conditions) {
            $conditions = [];
            foreach ($this->conditions as $condition => $value) {
                switch ($condition) {
                    case 'min_order_amount':
                        $conditions[] = "Min order: ₦" . number_format($value, 2);
                        break;
                    case 'max_order_amount':
                        $conditions[] = "Max order: ₦" . number_format($value, 2);
                        break;
                    case 'payment_methods':
                        $conditions[] = "Payment methods: " . implode(', ', $value);
                        break;
                }
            }
            
            if (!empty($conditions)) {
                $description .= ' (' . implode(', ', $conditions) . ')';
            }
        }
        
        return $description;
    }
}
