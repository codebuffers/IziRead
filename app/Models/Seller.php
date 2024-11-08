<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Seller extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_id';

    protected $fillable = ['seller_name', 'phone', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}