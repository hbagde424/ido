<?php

namespace Modules\Essentials\Entities;

use Illuminate\Database\Eloquent\Model;
use App\ProjectTask;

class ToDo extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Flag to prevent infinite sync loops
     */
    protected static $syncing = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'essentials_to_dos';

    public function users()
    {
        return $this->belongsToMany(\App\User::class, 'essentials_todos_users', 'todo_id', 'user_id');
    }

    public function assigned_by()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    public function comments()
    {
        return $this->hasMany(\Modules\Essentials\Entities\EssentialsTodoComment::class, 'task_id')->orderBy('id', 'desc');
    }

    public function media()
    {
        return $this->morphMany(\App\Media::class, 'model');
    }

    /**
     * Get the project checklist that owns the todo.
     */
    public function projectChecklist()
    {
        return $this->belongsTo(\App\ProjectChecklist::class, 'project_checklist_id');
    }

    /**
     * Get the project task linked to this todo.
     */
    public function projectTask()
    {
        return $this->hasOne(\App\ProjectTask::class, 'todo_id');
    }

    public static function getTaskStatus()
    {
        $statuses = [
            'In progress' =>"In Progress",
            'Incomplete' => "Incomplete", 
            'Completed' => "Completed", 
        ];

        return $statuses;
    }

    public static function getTaskPriorities()
    {
        $priorities = [
            'low' => __('essentials::lang.low'),
            'medium' => __('essentials::lang.medium'),
            'high' => __('essentials::lang.high'),
        ];

        return $priorities;
    }

    /**
     * Attributes to be logged for activity
     */
    public function getLogPropertiesAttribute()
    {
        $properties = ['status'];

        return $properties;
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // When a todo is created, create corresponding project task
        static::created(function ($todo) {
            if ($todo->project_checklist_id) {
                $todo->syncToProjectTask();
            }
        });

        // When a todo is updated, update corresponding project task
        static::updated(function ($todo) {
            if (!static::$syncing && $todo->project_checklist_id && $todo->projectTask) {
                $todo->syncToProjectTask();
            }
        });

        // When a todo is deleted, delete corresponding project task
        static::deleting(function ($todo) {
            if ($todo->projectTask) {
                $todo->projectTask->delete();
            }
        });
    }

    /**
     * Sync todo to project task
     */
    public function syncToProjectTask()
    {
        if (!$this->project_checklist_id || static::$syncing) {
            return;
        }

        static::$syncing = true;

        // Map todo status to project task status
        $projectTaskStatus = 0;
        if ($this->status === 'Completed') {
            $projectTaskStatus = 1;
        }

        // Get first assigned user if available
        $userId = $this->users()->first() ? $this->users()->first()->id : null;
        if (!$userId && isset($this->user_id)) {
            $userId = $this->user_id;
        }

        $projectTaskData = [
            'project_checklist_id' => $this->project_checklist_id,
            'task_name' => $this->task,
            'status' => $projectTaskStatus,
            'remark' => $this->description ? strip_tags($this->description) : null,
            'start_date' => $this->date ? date('Y-m-d', strtotime($this->date)) : null,
            'end_date' => $this->end_date ? date('Y-m-d', strtotime($this->end_date)) : null,
            'user_id' => $userId,
            'todo_id' => $this->id,
        ];

        try {
            if ($this->projectTask) {
                // Only update if values actually changed
                $hasChanges = false;
                $projectTask = $this->projectTask;
                
                if ($projectTask->task_name !== $this->task) {
                    $hasChanges = true;
                } elseif ($projectTask->status != $projectTaskStatus) {
                    $hasChanges = true;
                } elseif ($projectTask->remark !== ($this->description ? strip_tags($this->description) : null)) {
                    $hasChanges = true;
                } elseif ($projectTask->user_id != $userId) {
                    $hasChanges = true;
                }
                
                if ($hasChanges) {
                    $this->projectTask->update($projectTaskData);
                }
            } else {
                // Create new project task
                ProjectTask::create($projectTaskData);
            }
        } finally {
            static::$syncing = false;
        }
    }
}
