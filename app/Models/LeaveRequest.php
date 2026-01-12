<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    // Use the Oracle view
    protected $table = 'leave_requests';
    protected $primaryKey = 'request_id';
    public $timestamps = false;
    public $incrementing = true;

    protected $guarded = [];

    public function employee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn($value) => match ($value) {
                0 => 'Pending',
                1 => 'Approved',
                2 => 'Rejected',
                default => 'Pending',
            },
        );
    }
}
