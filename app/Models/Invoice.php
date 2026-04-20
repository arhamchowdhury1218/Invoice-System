<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'user_id',
        'client_id',
        'invoice_number',
        'issue_date',
        'due_date',
        'subtotal',
        'discount_percent',
        'tax_percent',
        'total',
        'status',
        'notes'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date'   => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // Auto-detect overdue when accessing status
    public function getStatusAttribute($value)
    {
        if ($value !== 'paid' && $this->due_date < now()) {
            return 'overdue';
        }
        return $value;
    }

    // Auto-generate invoice number
    public static function generateNumber(): string
    {
        $last = self::latest()->first();
        $next = $last ? ((int) substr($last->invoice_number, 4)) + 1 : 1;
        return 'INV-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }

    // Calculate total with discount and tax
    public function calculateTotal(): float
    {
        $afterDiscount = $this->subtotal * (1 - $this->discount_percent / 100);
        return round($afterDiscount * (1 + $this->tax_percent / 100), 2);
    }
}
