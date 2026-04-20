<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'address'
    ];

    // A client belongs to one user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A client has many invoices
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
