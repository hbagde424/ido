<?php

namespace Modules\Essentials\Entities;

use Illuminate\Database\Eloquent\Model;

class EssentialsMessageGroup extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the creator of the group.
     */
    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    /**
     * Get all members of the group.
     */
    public function members()
    {
        return $this->belongsToMany(\App\User::class, 'essentials_message_group_members', 'group_id', 'user_id')
                    ->withPivot('added_by', 'created_at')
                    ->withTimestamps();
    }

    /**
     * Get group members with pivot data.
     */
    public function groupMembers()
    {
        return $this->hasMany(EssentialsMessageGroupMember::class, 'group_id');
    }

    /**
     * Get messages for this group.
     */
    public function messages()
    {
        return $this->hasMany(EssentialsMessage::class, 'group_id');
    }

    /**
     * Check if user is member of this group.
     */
    public function isMember($user_id)
    {
        return $this->members()->where('user_id', $user_id)->exists();
    }

    /**
     * Add member to group.
     */
    public function addMember($user_id, $added_by)
    {
        return $this->members()->attach($user_id, [
            'added_by' => $added_by,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Remove member from group.
     */
    public function removeMember($user_id)
    {
        return $this->members()->detach($user_id);
    }
}
