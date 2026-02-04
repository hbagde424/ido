<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectChecklist extends Model
{
    use SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the tasks associated with the project.
     */
    public function tasks()
    {
        return $this->hasMany(\App\ProjectTask::class);
    }

    /**
     * Get the business that owns the project.
     */
    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }

    /**
     * Get the user who created the project.
     */
    public function createdBy()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    /**
     * Get the user who updated the project.
     */
    public function updatedBy()
    {
        return $this->belongsTo(\App\User::class, 'updated_by');
    }

    /**
     * The users assigned to the project.
     */
    public function users()
    {
        return $this->belongsToMany(\App\User::class, 'project_checklist_user');
    }

    /**
     * Get the project lead user.
     */
    public function projectLead()
    {
        return $this->belongsTo(\App\User::class, 'project_lead_id');
    }

    /**
     * Get project status based on various conditions
     * Returns both old status (Complete/In Progress/Incomplete) and extra status if applicable
     */
    public function getProjectStatus()
    {
        $total_tasks = $this->tasks->count();
        $progress = 0;
        
        if ($total_tasks > 0) {
            $completed_tasks = $this->tasks->where('status', 1)->count();
            $progress = round(($completed_tasks / $total_tasks) * 100);
        }
        
        $now = \Carbon\Carbon::now();
        $created_at = \Carbon\Carbon::parse($this->created_at);
        $days_since_creation = $created_at->diffInDays($now);
        $last_viewed_at = $this->last_viewed_at ? \Carbon\Carbon::parse($this->last_viewed_at) : null;
        $days_since_last_view = $last_viewed_at ? $last_viewed_at->diffInDays($now) : $days_since_creation;
        $end_date = $this->end_date ? \Carbon\Carbon::parse($this->end_date) : null;
        $is_overdue = $end_date && $end_date->isPast() && $progress < 100;
        
        // Determine old status (Complete, In Progress, or Incomplete)
        $old_status = 'Incomplete';
        $old_badge_color = 'danger';
        
        if ($progress == 100) {
            $old_status = 'Complete';
            $old_badge_color = 'success';
        } elseif ($progress >= 10 && $progress < 100) {
            $old_status = 'In Progress';
            $old_badge_color = 'warning';
        } else {
            $old_status = 'Incomplete';
            $old_badge_color = 'danger';
        }
        
        // Determine extra status if applicable
        $extra_status = null;
        $extra_badge_color = 'danger';
        
        // 4. Cancelled - project created but not opened for 10+ days AND no tasks ever created
        if ($days_since_last_view >= 10 && $total_tasks == 0) {
            $extra_status = 'Cancelled';
        }
        // 2. Overdue - project end date passed but not complete
        elseif ($is_overdue) {
            $extra_status = 'Overdue';
        }
        // 3. On Hold - project created but not viewed after 2 days (but not if project is complete)
        elseif ($days_since_last_view >= 2 && $progress < 100) {
            $extra_status = 'On Hold';
        }
        // 1. Not Started Yet - project created but no task created
        elseif ($total_tasks == 0) {
            $extra_status = 'Not Started Yet';
        }
        
        return [
            'old_status' => $old_status,
            'old_badge_color' => $old_badge_color,
            'extra_status' => $extra_status,
            'extra_badge_color' => $extra_badge_color,
            'status' => $extra_status ? $extra_status : $old_status, // For backward compatibility
            'badge_color' => $extra_status ? $extra_badge_color : $old_badge_color // For backward compatibility
        ];
    }
}