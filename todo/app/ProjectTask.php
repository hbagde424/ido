<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Modules\Essentials\Entities\ToDo;

class ProjectTask extends Model
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

    /**
     * Get the todo that this task is linked to.
     */
    public function todo()
    {
        return $this->belongsTo(\Modules\Essentials\Entities\ToDo::class, 'todo_id');
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // When a project task is created, create corresponding todo
        static::created(function ($projectTask) {
            if (!static::$syncing && $projectTask->project_checklist_id && !$projectTask->todo_id) {
                $projectTask->createTodoFromTask();
            }
        });

        // When a project task is updated, sync back to todo if linked
        static::updated(function ($projectTask) {
            if (!static::$syncing && $projectTask->todo) {
                $projectTask->syncToTodo();
            }
        });
    }

    /**
     * Sync project task to todo
     */
    public function syncToTodo()
    {
        if (!$this->todo || static::$syncing) {
            return;
        }

        static::$syncing = true;

        // Map project task status to todo status
        $todoStatus = $this->status ? 'Completed' : 'Incomplete';

        // Only update if status changed to avoid infinite loops
        $todoData = [];
        
        if ($this->todo->status !== $todoStatus) {
            $todoData['status'] = $todoStatus;
        }
        
        if ($this->todo->task !== $this->task_name) {
            $todoData['task'] = $this->task_name;
        }
        
        if ($this->todo->description !== $this->remark) {
            $todoData['description'] = $this->remark;
        }
        
        if ($this->start_date) {
            $startDate = date('Y-m-d H:i:s', strtotime($this->start_date));
            if ($this->todo->date !== $startDate) {
                $todoData['date'] = $startDate;
            }
        }
        
        if ($this->end_date) {
            $endDate = date('Y-m-d', strtotime($this->end_date));
            if ($this->todo->end_date !== $endDate) {
                $todoData['end_date'] = $endDate;
            }
        }

        try {
            // Only update if there are actual changes
            if (!empty($todoData)) {
                $this->todo->update($todoData);
            }
        } finally {
            static::$syncing = false;
        }
    }

    /**
     * Create a Todo from this Project Task
     */
    public function createTodoFromTask()
    {
        if (static::$syncing || $this->todo_id) {
            return; // Already has a todo or in sync loop
        }
        
        static::$syncing = true;
        
        try {
            $todo = ToDo::create([
                'business_id' => $this->project->business_id,
                'task' => $this->task_name,
                'description' => $this->remark,
                'status' => $this->status ? 'Completed' : 'Incomplete',
                'date' => $this->start_date,
                'end_date' => $this->end_date,
                'project_checklist_id' => $this->project_checklist_id,
                'created_by' => auth()->user()->id ?? $this->project->created_by,
            ]);
            
            // Link back to this project task
            $this->todo_id = $todo->id;
            $this->saveQuietly(); // Use saveQuietly to avoid triggering updated event
            
            // Assign user if present
            if ($this->user_id) {
                $todo->users()->sync([$this->user_id]);
            }
        } finally {
            static::$syncing = false;
        }
    }
}