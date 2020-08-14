<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class DirectMessage extends Model
{
    const UPDATED_AT = null;

    // protected $fillable = ['sender_id', 'receiver_id', 'text'];
    protected $guarded = [];

    public function sender()
    {
        return $this->belongsTo(User::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class);
    }

    public function getCreatedAtAttribute()
    {
        return (new Carbon($this->attributes['created_at']))->format('Y-m-d h:i:s');
    }
}
