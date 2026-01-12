<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    // Use the Oracle view
    protected $table = 'attendances';
    protected $primaryKey = 'attendance_id';
    public $timestamps = false;
    public $incrementing = true;

    protected $guarded = [];

    protected $casts = [
        'clock_in_time' => 'string',
        'clock_out_time' => 'string',
    ];

    public function employee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function schedule(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'shift_id', 'shift_id');
    }

    public function on_time(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Attendance::class)->where('status', 'on_time');
    }
}
