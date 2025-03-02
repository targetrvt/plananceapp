<?php
namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'type',
        'amount',
        'date',
        'description',
        'category',
        'user_id',
        'receipt_image'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
