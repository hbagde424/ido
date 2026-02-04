# Calendar View Attendance Role-Based Filtering Implementation

## Summary
Implemented role-based filtering for the calendar view attendance system to ensure users see attendance data appropriate to their role and permissions:

- **Admin (roleId: 1)**: Can view attendance for all users in the system
- **Subadmin (roleId: 14)**: Can view attendance only for users from their permitted locations
- **Other users**: Can view only their own attendance

## Files Modified

### 1. `Modules/Essentials/Http/Controllers/AttendanceController.php`

#### Changes in `index()` method (Lines ~169-171):

**Before:**
```php
$employees = [];
if ($can_crud_all_attendance) {
    $employees = User::forDropdown($business_id, false, false, false, true);
}
```

**After:**
```php
$employees = [];
if ($can_crud_all_attendance) {
    // Role-based employee filtering for calendar view
    $currentUser = auth()->user();
    $user_role_id = $currentUser->roleId ?? 0;
    $auth_id = auth()->user()->id;
    
    if ($user_role_id == 1) {
        // Admin role - Show all users
        $employees = User::forDropdown($business_id, false, false, false, true);
    } elseif ($user_role_id == 14) {
        // Sub-admin role - Show users from permitted locations only
        $user_permissions = $currentUser->permissions->pluck('name')->all();
        $permitted_locations = [];
        
        // Check for location permissions (handle both formats: location.X and location.location.X)
        foreach ($user_permissions as $permission) {
            if (strpos($permission, 'location.') === 0) {
                // Extract location ID from permission
                if (strpos($permission, 'location.location.') === 0) {
                    // Handle double prefix: location.location.X
                    $location_id = str_replace('location.location.', '', $permission);
                } else {
                    // Handle single prefix: location.X
                    $location_id = str_replace('location.', '', $permission);
                }
                if (is_numeric($location_id)) {
                    $permitted_locations[] = (int)$location_id;
                }
            }
        }
        
        if (!empty($permitted_locations)) {
            // Get users from permitted locations
            $location_users = User::where('business_id', $business_id)
                ->user()
                ->whereIn('location_id', $permitted_locations)
                ->select('id', 'first_name', 'last_name', 'surname')
                ->get();
            
            // Format users for dropdown
            $employees = $location_users->mapWithKeys(function ($user) {
                $full_name = trim(($user->surname ?? '') . ' ' . ($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                return [$user->id => $full_name];
            })->filter(function ($name) {
                return !empty(trim($name));
            });
        } else {
            // If no location permissions, show only themselves
            $current_user = User::find($auth_id);
            if ($current_user) {
                $full_name = trim(($current_user->surname ?? '') . ' ' . ($current_user->first_name ?? '') . ' ' . ($current_user->last_name ?? ''));
                $employees = [$auth_id => $full_name];
            }
        }
    } else {
        // Other roles - Show only themselves
        $current_user = User::find($auth_id);
        if ($current_user) {
            $full_name = trim(($current_user->surname ?? '') . ' ' . ($current_user->first_name ?? '') . ' ' . ($current_user->last_name ?? ''));
            $employees = [$auth_id => $full_name];
        }
    }
}
```

#### Changes in `getCalendarData()` method:

**1. Simplified attendance query (Lines ~1054-1065):**

**Before:**
```php
$attendance_query = EssentialsAttendance::where('essentials_attendances.business_id', $business_id)
    ->join('users as u', 'u.id', '=', 'essentials_attendances.user_id')
    ->leftJoin('essentials_shifts as es', 'es.id', '=', 'essentials_attendances.essentials_shift_id')
    ->leftJoin('model_has_permissions as mhp', 'mhp.model_id', '=', 'u.id')
    ->leftJoin('permissions as p', 'p.id', '=', 'mhp.permission_id')
    ->select([...]);
```

**After:**
```php
$attendance_query = EssentialsAttendance::where('essentials_attendances.business_id', $business_id)
    ->join('users as u', 'u.id', '=', 'essentials_attendances.user_id')
    ->leftJoin('essentials_shifts as es', 'es.id', '=', 'essentials_attendances.essentials_shift_id')
    ->select([...]);
```

**2. Fixed role-based visibility logic (Lines ~1067-1079):**

**Before:**
```php
if ($user_role_id == 1) {
    // Admin: no extra restriction
} elseif ($user_role_id == 14) {
    // Sub-admin: limit by permitted locations if any, else fallback to own
    if (!empty($permitted_locations)) {
        $attendance_query->whereIn(DB::raw("CAST(REPLACE(p.name, 'location.', '') AS UNSIGNED)"), $permitted_locations);
    } else {
        $attendance_query->where('essentials_attendances.user_id', $currentUser->id);
    }
} else {
    // Other roles: only own
    $attendance_query->where('essentials_attendances.user_id', $currentUser->id);
}
```

**After:**
```php
if ($user_role_id == 1) {
    // Admin: no extra restriction - can see all attendance
} elseif ($user_role_id == 14) {
    // Sub-admin: limit by permitted locations if any, else fallback to own
    if (!empty($permitted_locations)) {
        // Filter attendance to only show those from users in permitted locations
        $attendance_query->whereIn('u.location_id', $permitted_locations);
    } else {
        $attendance_query->where('essentials_attendances.user_id', $currentUser->id);
    }
} else {
    // Other roles: only own attendance
    $attendance_query->where('essentials_attendances.user_id', $currentUser->id);
}
```

**3. Improved absent/sunday day logic (Lines ~1199-1213):**

**Before:**
```php
$users_query = User::where('business_id', $business_id)
    ->where('is_active', 1)
    ->join('model_has_permissions as mhp', 'mhp.model_id', '=', 'users.id')
    ->join('permissions as p', 'p.id', '=', 'mhp.permission_id')
    ->where('p.name', 'like', 'location.%');

if ($permitted_locations != 'all') {
    $users_query->whereIn(DB::raw("CAST(REPLACE(p.name, 'location.', '') AS UNSIGNED)"), $permitted_locations);
}

$all_users = $users_query->select('users.*')->distinct()->get();
```

**After:**
```php
$users_query = User::where('business_id', $business_id)
    ->where('is_active', 1);

// Apply same role-based filtering for absent/sunday days
if ($user_role_id == 1) {
    // Admin: can see all users
} elseif ($user_role_id == 14) {
    // Sub-admin: limit by permitted locations
    if (!empty($permitted_locations)) {
        $users_query->whereIn('location_id', $permitted_locations);
    } else {
        $users_query->where('id', $currentUser->id);
    }
} else {
    // Other roles: only themselves
    $users_query->where('id', $currentUser->id);
}

$all_users = $users_query->get();
```

**4. Fixed permission safeguard logic (Lines ~1095-1099):**

**Before:**
```php
if (!$can_crud_all_attendance && $can_view_own_attendance && $user_role_id == 1) {
    $attendance_query->where('essentials_attendances.user_id', auth()->user()->id);
}
```

**After:**
```php
if (!$can_crud_all_attendance && $can_view_own_attendance) {
    // If user doesn't have crud_all_attendance permission, limit to own attendance only
    $attendance_query->where('essentials_attendances.user_id', auth()->user()->id);
}
```

## Key Improvements

### 1. **Consistent Role-Based Logic**
- Same logic across both employee dropdown population and calendar data filtering
- Consistent with TodoController implementation
- Proper handling of location permission formats (`location.X` and `location.location.X`)

### 2. **Simplified Query Structure**
- Removed complex permission joins that were causing filtering issues
- Direct location_id filtering for better performance and accuracy
- Cleaner and more maintainable code

### 3. **Proper Permission Boundaries**
- **Admin (roleId: 1)**: Full access to all users and their attendance
- **Subadmin (roleId: 14)**: Access limited to users from their permitted locations
- **Regular users**: Access only to their own attendance data

### 4. **Enhanced Security**
- Multiple layers of permission checking
- Consistent enforcement across all calendar-related operations
- Prevents unauthorized data access

## Access Matrix

| User Role | Employee Dropdown | Calendar Data | Absent/Sunday Days |
|-----------|------------------|---------------|-------------------|
| **Admin (roleId: 1)** | All users in business | All attendance records | All active users |
| **Subadmin (roleId: 14)** | Users from permitted locations | Attendance from permitted location users | Users from permitted locations |
| **Regular Users** | Only themselves | Only their own attendance | Only themselves |

## Benefits

### For Admins:
- ✅ Complete visibility across all locations and users
- ✅ Full management capabilities unchanged
- ✅ No restriction on calendar view data

### For Subadmins:
- ✅ Can view attendance for all users in their assigned locations
- ✅ Cannot access attendance from unauthorized locations
- ✅ Proper role-based management capabilities
- ✅ Consistent experience across todo and attendance systems

### For Regular Users:
- ✅ Can view only their own attendance data
- ✅ No unauthorized access to other users' data
- ✅ Simple, focused interface

### For System Security:
- ✅ Maintains proper data boundaries
- ✅ Location-based access control
- ✅ Consistent permission model across modules
- ✅ No data leakage between locations

## Testing Recommendations

### Admin Testing:
1. **Login as Admin**: Verify dropdown shows all users
2. **Calendar View**: Confirm attendance data from all locations is visible
3. **Filter by Employee**: Test individual employee filtering works
4. **Absent/Sunday Display**: Verify all users show absent/sunday indicators

### Subadmin Testing:
1. **Login as Subadmin**: Verify dropdown shows only permitted location users
2. **Calendar View**: Confirm only attendance from permitted locations is visible
3. **Cross-Location Testing**: Ensure users from other locations are not visible
4. **Permission Boundary**: Test that changing location permissions updates visibility

### Regular User Testing:
1. **Login as Regular User**: Verify dropdown shows only themselves
2. **Calendar View**: Confirm only own attendance is visible
3. **Access Restrictions**: Ensure no access to other users' data

### Integration Testing:
1. **Permission Changes**: Test behavior when user permissions are modified
2. **Location Changes**: Test when users are moved between locations
3. **Role Changes**: Test when user roles are updated
4. **Data Consistency**: Verify calendar data matches employee dropdown options

## Usage Instructions

The calendar view attendance will now automatically:

1. **Populate Employee Dropdown** based on user role and permissions
2. **Filter Calendar Data** to show only authorized attendance records
3. **Display Absent/Sunday Indicators** for appropriate users only
4. **Maintain Security Boundaries** across all calendar operations

No additional configuration is required - the system will automatically apply the appropriate filtering based on the logged-in user's role and location permissions.

## Compatibility

This implementation:
- ✅ Maintains backward compatibility with existing admin users
- ✅ Preserves all existing calendar functionality
- ✅ Works with existing permission system
- ✅ Compatible with location-based permission formats
- ✅ Integrates seamlessly with todo system role logic