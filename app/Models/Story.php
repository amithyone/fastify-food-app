<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    protected $fillable = [
        'restaurant_id',
        'type',
        'title',
        'content',
        'emoji',
        'subtitle',
        'description',
        'price',
        'original_price',
        'show_button',
        'button_text',
        'button_action',
        'is_active',
        'sort_order',
        'color_gradient'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'show_button' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
