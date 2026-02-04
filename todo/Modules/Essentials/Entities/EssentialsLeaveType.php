<?php

namespace Modules\Essentials\Entities;

use Illuminate\Database\Eloquent\Model;

class EssentialsLeaveType extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public static function forDropdown($business_id)
    {
        $leave_types = EssentialsLeaveType::where('business_id', $business_id)
                                    ->pluck('leave_type', 'id');

        return $leave_types;
    }

    /**
     * Get current available quota for a user for this leave type
     *
     * @param int $user_id
     * @param string $date (to determine the period)
     * @return array
     */
    public function getAvailableQuota($user_id, $date = null)
    {
        if (empty($this->max_leave_count)) {
            return [
                'max_allowed' => 'Unlimited',
                'used' => 0,
                'available' => 'Unlimited'
            ];
        }

        $date = $date ? \Carbon\Carbon::parse($date) : \Carbon\Carbon::now();
        $interval = $this->leave_count_interval ?? 'year';
        
        // Determine query period
        switch ($interval) {
            case 'month':
                $start_date = $date->copy()->startOfMonth();
                $end_date = $date->copy()->endOfMonth();
                break;
            case 'year':
            default:
                $start_date = $date->copy()->startOfYear();
                $end_date = $date->copy()->endOfYear();
                break;
        }

        // Calculate used leaves in the period
        $used_leaves = \DB::table('essentials_leaves')
            ->where('business_id', $this->business_id)
            ->where('user_id', $user_id)
            ->where('essentials_leave_type_id', $this->id)
            ->whereIn('status', ['approved', 'pending'])
            ->where(function($q) use ($start_date, $end_date) {
                $q->whereBetween('start_date', [$start_date->format('Y-m-d'), $end_date->format('Y-m-d')])
                  ->orWhereBetween('end_date', [$start_date->format('Y-m-d'), $end_date->format('Y-m-d')])
                  ->orWhere(function($qq) use ($start_date, $end_date) {
                      $qq->where('start_date', '<=', $start_date->format('Y-m-d'))
                         ->where('end_date', '>=', $end_date->format('Y-m-d'));
                  });
            })
            ->get();

        $used_days = 0;
        foreach ($used_leaves as $leave) {
            $leave_start = \Carbon\Carbon::parse($leave->start_date);
            $leave_end = \Carbon\Carbon::parse($leave->end_date);
            
            // Calculate overlapping days within the period
            $overlap_start = $leave_start->max($start_date);
            $overlap_end = $leave_end->min($end_date);
            
            if ($overlap_start <= $overlap_end) {
                $used_days += $overlap_start->diffInDays($overlap_end) + 1;
            }
        }

        return [
            'max_allowed' => $this->max_leave_count,
            'used' => $used_days,
            'available' => max(0, $this->max_leave_count - $used_days),
            'interval' => $interval,
            'period_start' => $start_date->format('Y-m-d'),
            'period_end' => $end_date->format('Y-m-d')
        ];
    }
}
