<?php

namespace Modules\Essentials\Entities;

use Illuminate\Database\Eloquent\Model;

class EssentialsPolicy extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    protected $table = 'essentials_policies';

    /**
     * Policy types
     */
    public static $policy_types = [
        'company_policy' => 'Company Policy',
        'hr_policy' => 'HR Policy',
        'leave_policy' => 'Leave Policy',
        'posh_policy' => 'POSH Policy',
        'nda_policy' => 'NDA Policy',
    ];

    /**
     * Policy statuses
     */
    public static $statuses = [
        'pending' => 'Pending',
        'signed' => 'Signed',
        'rejected' => 'Rejected',
    ];

    /**
     * Get the user associated with the policy
     */
    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    /**
     * Get policy type label
     */
    public function getPolicyTypeLabel()
    {
        return self::$policy_types[$this->policy_type] ?? $this->policy_type;
    }

    /**
     * Get status label
     */
    public function getStatusLabel()
    {
        return self::$statuses[$this->status] ?? $this->status;
    }

    /**
     * Get all policies for a user
     */
    public static function getUserPolicies($business_id, $user_id)
    {
        return self::where('business_id', $business_id)
                   ->where('user_id', $user_id)
                   ->get();
    }

    /**
     * Get all policies by type
     */
    public static function getPoliciesByType($business_id, $policy_type)
    {
        return self::where('business_id', $business_id)
                   ->where('policy_type', $policy_type)
                   ->get();
    }
}
