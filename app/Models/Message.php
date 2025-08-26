<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    public const MESSAGE_STATUS_SELECT = [
        'Delivered' => 'Delivered',
        'Undelivered' => 'Undelivered',
        'Pending' => 'Pending',
        'Expired' => 'Expired',
        'InsufficientCredit' => 'Insufficient Credit'
    ];
    use HasFactory, SoftDeletes, HasUuids;

    protected $guarded = [];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
