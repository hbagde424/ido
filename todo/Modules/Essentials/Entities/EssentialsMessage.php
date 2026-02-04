<?php

namespace Modules\Essentials\Entities;

use Illuminate\Database\Eloquent\Model;

class EssentialsMessage extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'message_type' => 'string',
    ];

    /**
     * Get sender.
     */
    public function sender()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    /**
     * Get recipient user (for direct messages).
     */
    public function recipient()
    {
        return $this->belongsTo(\App\User::class, 'recipient_user_id');
    }

    /**
     * Get group (for group messages).
     */
    public function group()
    {
        return $this->belongsTo(EssentialsMessageGroup::class, 'group_id');
    }

    /**
     * Get message recipients.
     */
    public function recipients()
    {
        return $this->hasMany(EssentialsMessageRecipient::class, 'message_id');
    }

    /**
     * Get location.
     */
    public function location()
    {
        return $this->belongsTo(\App\BusinessLocation::class, 'location_id');
    }

    /**
     * Scope for location messages.
     */
    public function scopeLocationMessages($query)
    {
        return $query->where('message_type', 'location');
    }

    /**
     * Scope for user messages.
     */
    public function scopeUserMessages($query)
    {
        return $query->where('message_type', 'user');
    }

    /**
     * Scope for group messages.
     */
    public function scopeGroupMessages($query)
    {
        return $query->where('message_type', 'group');
    }

    /**
     * Scope for messages sent to specific user.
     */
    public function scopeToUser($query, $user_id)
    {
        return $query->where('recipient_user_id', $user_id);
    }

    /**
     * Scope for messages from specific user.
     */
    public function scopeFromUser($query, $user_id)
    {
        return $query->where('user_id', $user_id);
    }

    /**
     * Scope for messages in specific group.
     */
    public function scopeInGroup($query, $group_id)
    {
        return $query->where('group_id', $group_id);
    }
}
