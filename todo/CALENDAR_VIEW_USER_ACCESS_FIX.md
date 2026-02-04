# Fix: Calendar View Access for Regular Users

## Problem Identified
Regular users who only have `essentials.view_own_attendance` permission were unable to access the calendar view tab, even though they should be able to view their own attendance in calendar format.

## Root Causes Found

### 1. **Employee Dropdown Population**
The `$employees` array was only populated when users had `essentials.crud_all_attendance` permission, leaving regular users without any employee data for the calendar view.

### 2. **Calendar View Tab Restriction**
The calendar view tab and content were placed inside `@can('essentials.crud_all_attendance')` permission blocks, making them completely inaccessible to regular users with only `view_own_attendance` permission.

## Solutions Implemented

### 1. **Updated AttendanceController.php - index() method**

**File:** `Modules/Essentials/Http/Controllers/AttendanceController.php`

**Before:**
```php
$employees = [];
if ($can_crud_all_attendance) {
    // Employee filtering logic only for crud_all_attendance users
}
```

**After:**
```php
$employees = [];
// Allow calendar view for users with either crud_all_attendance or view_own_attendance permission
if ($can_crud_all_attendance || $can_view_own_attendance) {
    // Role-based employee filtering for calendar view
    $currentUser = auth()->user();
    $user_role_id = $currentUser->roleId ?? 0;
    $auth_id = auth()->user()->id;
    
    if ($user_role_id == 1 && $can_crud_all_attendance) {
        // Admin role with full access - Show all users
        $employees = User::forDropdown($business_id, false, false, false, true);
    } elseif ($user_role_id == 14 && $can_crud_all_attendance) {
        // Sub-admin role with full access - Show users from permitted locations only
        // ... location-based filtering logic
    } else {
        // Regular users or users with only view_own_attendance - Show only themselves
        $current_user = User::find($auth_id);
        if ($current_user) {
            $full_name = trim(($current_user->surname ?? '') . ' ' . ($current_user->first_name ?? '') . ' ' . ($current_user->last_name ?? ''));
            $employees = [$auth_id => $full_name];
        }
    }
}
```

### 2. **Updated attendance/index.blade.php - Tab Structure**

**File:** `Modules/Essentials/Resources/views/attendance/index.blade.php`

#### **Tab Navigation Changes:**

**Before:**
```php
@can('essentials.crud_all_attendance')
    <li>
        <a href="#calendar_view_tab" data-toggle="tab" aria-expanded="true">
            <i class="fas fa-calendar-alt" aria-hidden="true"></i> Calendar View
        </a>
    </li>
@endcan
```

**After:**
```php
@endcan
@if(auth()->user()->can('essentials.crud_all_attendance') || auth()->user()->can('essentials.view_own_attendance'))
<li>
    <a href="#calendar_view_tab" data-toggle="tab" aria-expanded="true">
        <i class="fas fa-calendar-alt" aria-hidden="true"></i> Calendar View
    </a>
</li>
@endif
```

#### **Tab Content Changes:**

**Before:**
```php
@can('essentials.crud_all_attendance')
    <div class="tab-pane" id="calendar_view_tab">
        @include('essentials::attendance.calendar_view')
    </div>
@endcan
```

**After:**
```php
@if(auth()->user()->can('essentials.crud_all_attendance') || auth()->user()->can('essentials.view_own_attendance'))
<div class="tab-pane" id="calendar_view_tab">
    @include('essentials::attendance.calendar_view')
</div>
@endif
```

## Access Matrix After Fix

| User Role | Employee Dropdown | Calendar Tab Access | Calendar Data |
|-----------|------------------|-------------------|---------------|
| **Admin (roleId: 1)** | All users | ✅ Available | All attendance records |
| **Subadmin (roleId: 14)** | Location users only | ✅ Available | Location attendance only |
| **Regular Users** | Only themselves | ✅ Available | Own attendance only |

## Key Benefits

### For Regular Users:
- ✅ **Calendar Access**: Can now access the calendar view tab
- ✅ **Own Data**: Can view their own attendance in calendar format
- ✅ **User Experience**: Consistent interface with visual calendar representation
- ✅ **Self-Service**: No need to request attendance data from administrators

### For System Administrators:
- ✅ **Permission Consistency**: Calendar view now respects the same permission model as other attendance features
- ✅ **Security Maintained**: Users still only see data they're authorized to view
- ✅ **Role Flexibility**: Different user roles have appropriate levels of access

### For System Architecture:
- ✅ **Permission Logic**: Consistent use of both `crud_all_attendance` and `view_own_attendance` permissions
- ✅ **Role-Based Access**: Proper filtering based on user roles and location permissions
- ✅ **Scalability**: Solution works for any number of users and locations

## Testing Verification Required

### Regular User Testing:
1. **Login as Regular User** with only `essentials.view_own_attendance` permission
2. **Navigate to Attendance Module** 
3. **Verify Calendar Tab** is visible and clickable
4. **Check Employee Dropdown** shows only their own name
5. **Verify Calendar Data** shows only their own attendance records
6. **Test Date Navigation** works properly
7. **Confirm No Unauthorized Access** to other users' data

### Admin/Subadmin Testing:
1. **Verify Existing Functionality** remains unchanged
2. **Check All Users Visible** in dropdown (admin) or location users (subadmin)
3. **Confirm Calendar Data** shows appropriate records based on role
4. **Test Permission Boundaries** are maintained

### Edge Case Testing:
1. **Users with No Permissions** should not see calendar tab
2. **Users Changing Roles** should see updated access immediately
3. **Location Changes** should update subadmin visibility correctly

## Technical Implementation Details

### Permission Structure:
- `essentials.crud_all_attendance`: Full attendance management capabilities
- `essentials.view_own_attendance`: View personal attendance only
- **Calendar View Access**: Available to users with either permission

### Data Filtering:
- **Admin**: No filtering - sees all records
- **Subadmin**: Location-based filtering using `permitted_locations()`
- **Regular User**: Self-only filtering using `auth()->user()->id`

### Security Considerations:
- ✅ **Data Boundaries**: Users cannot access unauthorized records
- ✅ **Permission Checks**: Multiple layers of permission validation
- ✅ **Role Consistency**: Same logic used across todo and attendance modules
- ✅ **Location Restrictions**: Subadmin access properly limited by location permissions

## Files Modified Summary

1. **`Modules/Essentials/Http/Controllers/AttendanceController.php`**
   - Enhanced employee dropdown population logic
   - Added support for `view_own_attendance` permission
   - Maintained role-based filtering for all user types

2. **`Modules/Essentials/Resources/views/attendance/index.blade.php`**
   - Moved calendar view tab outside `crud_all_attendance` restriction
   - Added combined permission check `crud_all_attendance || view_own_attendance`
   - Maintained security boundaries for other features

## Usage Instructions

After implementing these fixes:

1. **Regular users** will automatically see the Calendar View tab in their attendance module
2. **Employee dropdown** will show only their own name for filtering
3. **Calendar data** will display only their personal attendance records
4. **All existing functionality** for admins and subadmins remains unchanged
5. **No additional configuration** is required

The calendar view will now be accessible to all users with appropriate attendance viewing permissions while maintaining proper security boundaries and role-based access control.