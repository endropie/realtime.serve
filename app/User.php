<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'created_at', 'updated_at', 'email_verified_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function send_messages()
    {
        return $this->hasMany(DirectMessage::class, 'sender_id');
    }

    public function receive_messages()
    {
        return $this->hasMany(DirectMessage::class, 'receiver_id');
    }

    public function getRoleNamesAttribute ()
    {
        return ['all'];
    }

    public function scopeHasConversation ($query, $withUser)
    {
        return $query
            ->whereHas('send_messages', function ($q) use ($withUser) {
                return $q->where('receiver_id', $withUser->id);
            })
            ->orWhereHas('receive_messages', function ($q) use ($withUser) {
                return $q->where('sender_id', $withUser->id);
            });
    }

    public function getDirectMessages ($withUser)
    {
        return DirectMessage::where(function ($q) use ($withUser) {
            return $q->when($withUser,
              function($q) use ($withUser) {
                  return $q->orWhere(function ($q) use ($withUser) {
                      return $q->where('receiver_id', $this->id)->where('sender_id', $withUser->id);
                  })->orWhere(function ($q) use ($withUser) {
                      return $q->where('sender_id', $this->id)->where('receiver_id', $withUser->id);
                  });
              },
              function($q) {
                  return $q->orWhere(function ($q) {
                      return $q->where('receiver_id', $this->id);
                  })->orWhere(function ($q) {
                      return $q->where('sender_id', $this->id);
              });
          });
        });
    }

    public function getLastMessage ($withUser)
    {
        return $this->getDirectMessages($withUser)->latest()->first();
    }
}
