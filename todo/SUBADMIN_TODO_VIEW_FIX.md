# Subadmin Todo View 404 Error Fix

## Problem Description
Subadmins were experiencing 404 errors when trying to view todo details (e.g., `/essentials/todo/3647`), while the same functionality worked fine for admin users. This was preventing subadmins from accessing todo details even for tasks assigned to users in their permitted locations.

## Root Cause Analysis
The issue was in the `show()` method of `ToDoController.php`. The method had simplified access control that only allowed:

1. **Admins**: Full access to all todos
2. **Non-admins**: Access only to todos they were directly assigned to

This logic **did not account for subadmins** (roleId: 14) who should have access to todos assigned to **any user in their permitted locations**.

### Original Problematic Code:
```php
// Simplified access control - check if user is assigned to the task or is admin
$is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

if (!$is_admin) {
    // For non-admin users, only show tasks they are assigned to
    $query->whereHas('users', function ($q) {
        $q->where('user_id', auth()->user()->id);
    });
}
```

## Solution Implemented
Updated the `show()` method to use the same **role-based and location-based permission logic** that's already working correctly in the `index()` method.

### Fixed Code:
```php
// Role-based access control consistent with index method
$currentUser = auth()->user();
$user_role_id = $currentUser->roleId ?? 0;
$auth_id = auth()->user()->id;

if ($user_role_id == 1) {
    // Admin role (ID: 1) - See all TODOs (no filtering)
    // No additional filtering applied
} elseif ($user_role_id == 14) {
    // Sub-admin role (ID: 14) - See TODOs based on location permissions
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
        // Filter TODOs to only show those assigned to users from permitted locations
        $query->whereHas('users', function ($q) use ($permitted_locations) {
            $q->whereIn('location_id', $permitted_locations);
        });
    } else {
        // If no location permissions, show only their own TODOs
        $query->whereHas('users', function ($q) use ($auth_id) {
            $q->where('user_id', $auth_id);
        });
    }
} else {
    // Any other role - Show only their own TODOs
    $query->whereHas('users', function ($q) use ($auth_id) {
        $q->where('user_id', $auth_id);
    });
}
```

## Changes Made

### File Modified:
- `Modules/Essentials/Http/Controllers/ToDoController.php`

### Method Updated:
- `show($id)` method - Line ~493-503

### Key Improvements:
1. **Consistent Permission Logic**: Now matches the same logic used in `index()` method
2. **Role-Based Access**: Proper handling for different user roles (Admin, Subadmin, Regular users)
3. **Location-Based Filtering**: Subadmins can view todos from users in their permitted locations
4. **Permission Format Handling**: Supports both `location.X` and `location.location.X` formats
5. **Fallback Security**: Users without location permissions only see their own todos

## Access Matrix After Fix

| User Role | Access Level | Can View |
|-----------|-------------|----------|
| **Admin (roleId: 1)** | Full Access | All todos from all locations |
| **Subadmin (roleId: 14)** | Location-Based | Todos assigned to users in their permitted locations |
| **Regular Users** | Personal Only | Only todos they are directly assigned to |

## Benefits of the Fix

### For Subadmins:
- ✅ Can now view todo details for all users in their locations
- ✅ No more 404 errors when accessing legitimate todos
- ✅ Consistent experience between todo list and todo details
- ✅ Proper management capabilities for their team's tasks

### For System Security:
- ✅ Maintains proper access boundaries
- ✅ Users still cannot access todos outside their permitted locations
- ✅ Preserves role-based security model
- ✅ No unauthorized data exposure

### For System Consistency:
- ✅ Same permission logic across all todo operations (list, view, edit)
- ✅ Reduces maintenance complexity
- ✅ Predictable behavior for users
- ✅ Easier troubleshooting and support

## Testing Recommendations

### Subadmin Testing:
1. **Login as Subadmin**: Use a subadmin account with specific location permissions
2. **View Todo List**: Verify todos from permitted locations are visible
3. **Click Todo Names**: Test that todo detail links work without 404 errors
4. **Access Todo URLs**: Directly test URLs like `/essentials/todo/XXXX`
5. **Cross-Location Testing**: Ensure todos from other locations still return 404

### Admin Testing:
1. **Verify Admin Access**: Confirm admins still have full access to all todos
2. **No Regression**: Ensure existing admin functionality is unchanged

### Regular User Testing:
1. **Personal Todos**: Verify regular users can still view their assigned todos
2. **Access Restrictions**: Confirm they cannot access others' todos
3. **No Unauthorized Access**: Ensure they can't access todos from other locations

### Edge Cases:
1. **No Location Permissions**: Test subadmins without location permissions
2. **Invalid Todo IDs**: Verify proper 404 handling for non-existent todos
3. **Permission Changes**: Test behavior when user permissions are modified

## Verification Steps

To verify the fix is working:

1. **Subadmin Login**: 
   ```
   - Login with subadmin credentials
   - Navigate to todo list
   - Click on any todo name/link
   - Should open todo details without 404 error
   ```

2. **Direct URL Access**:
   ```
   - Access: https://akalptechnomediasolutions.com/checkin/essentials/todo/3647
   - Should display todo details if subadmin has access to that user's location
   - Should show 404 only if todo is from unauthorized location
   ```

3. **Permission Boundary Testing**:
   ```
   - Try accessing todos from different locations
   - Verify access is granted/denied based on location permissions
   ```

This fix resolves the 404 error issue while maintaining proper security boundaries and providing subadmins with the appropriate level of access to manage their team's todos.