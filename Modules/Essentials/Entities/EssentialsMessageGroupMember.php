<?php

namespace Modules\Essentials\Entities;

use Illuminate\Database\Eloquent\Model;

class EssentialsMessageGroupMember extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the group.
     */
    public function group()
    {
        return $this->belongsTo(EssentialsMessageGroup::class, 'group_id');
    }

    /**
     * Get the user.
     */
    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    /**
     * Get who added this member.
     */
    public function addedBy()
    {
        return $this->belongsTo(\App\User::class, 'added_by');
    }
}
