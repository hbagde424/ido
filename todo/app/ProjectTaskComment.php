<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectTaskComment extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the task that owns the comment.
     */
    public function task()
    {
        return $this->belongsTo(\App\ProjectTask::class, 'project_task_id');
    }

    /**
     * Get the user who created the comment.
     */
    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }
}

