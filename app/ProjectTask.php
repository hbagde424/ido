<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectTask extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the project that owns the task.
     */
    public function project()
    {
        return $this->belongsTo(\App\ProjectChecklist::class, 'project_checklist_id');
    }

    /**
     * Get the comments for the task.
     */
    public function comments()
    {
        return $this->hasMany(\App\ProjectTaskComment::class, 'project_task_id');
    }

    /**
     * Get the user assigned to the task.
     */
    public function assignedUser()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }
}