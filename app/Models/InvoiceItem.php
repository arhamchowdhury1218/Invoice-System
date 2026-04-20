<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'description',
        'quantity',
        'unit_price',
        'total'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // Auto-calculate item total
    protected static function booted()
    {
        static::saving(function ($item) {
            $item->total = round($item->quantity * $item->unit_price, 2);
        });
    }
}
