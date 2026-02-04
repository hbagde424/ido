<?php

// use App\Http\Controllers\Modules;
// use Illuminate\Support\Facades\Route;

Route::middleware('web', 'authh', 'auth', 'SetSessionData', 'language', 'timezone', 'AdminSidebarMenu')->group(function () {
    Route::prefix('essentials')->group(function () {
        Route::get('/dashboard', [Modules\Essentials\Http\Controllers\DashboardController::class, 'essentialsDashboard']);
        Route::get('/install', [Modules\Essentials\Http\Controllers\InstallController::class, 'index']);
        Route::get('/install/update', [Modules\Essentials\Http\Controllers\InstallController::class, 'update']);
        Route::get('/install/uninstall', [Modules\Essentials\Http\Controllers\InstallController::class, 'uninstall']);

        Route::get('/', [Modules\Essentials\Http\Controllers\EssentialsController::class, 'index']);

        //document controller
        Route::resource('document', 'Modules\Essentials\Http\Controllers\DocumentController')->only(['index', 'store', 'destroy', 'show']);
        Route::get('document/download/{id}', [Modules\Essentials\Http\Controllers\DocumentController::class, 'download']);

        //document share controller
        Route::resource('document-share', 'Modules\Essentials\Http\Controllers\DocumentShareController')->only(['edit', 'update']);

        //todo controller
        Route::resource('todo', 'ToDoController');
        Route::get('todo-chart-data', [Modules\Essentials\Http\Controllers\ToDoController::class, 'getTaskStatusChartData']);
        Route::get('todo-calendar', [Modules\Essentials\Http\Controllers\ToDoController::class, 'calendar']);
        Route::get('todo-calendar-data', [Modules\Essentials\Http\Controllers\ToDoController::class, 'getCalendarData']);

        Route::post('todo/add-comment', [Modules\Essentials\Http\Controllers\ToDoController::class, 'addComment']);
        Route::get('todo/delete-comment/{id}', [Modules\Essentials\Http\Controllers\ToDoController::class, 'deleteComment']);
        Route::get('todo/delete-document/{id}', [Modules\Essentials\Http\Controllers\ToDoController::class, 'deleteDocument']);
        Route::post('todo/upload-document', [Modules\Essentials\Http\Controllers\ToDoController::class, 'uploadDocument']);
        Route::get('view-todo-{id}-share-docs', [Modules\Essentials\Http\Controllers\ToDoController::class, 'viewSharedDocs']);

        //reminder controller
        Route::resource('reminder', 'Modules\Essentials\Http\Controllers\ReminderController')->only(['index', 'store', 'edit', 'update', 'destroy', 'show']);

        //message controller
        Route::get('get-new-messages', [Modules\Essentials\Http\Controllers\EssentialsMessageController::class, 'getNewMessages']);
        Route::resource('messages', 'Modules\Essentials\Http\Controllers\EssentialsMessageController')->only(['index', 'store', 'destroy']);
        
        //message group controller
        Route::resource('message-groups', 'Modules\Essentials\Http\Controllers\EssentialsMessageGroupController');
        Route::get('message-groups/users/list', [Modules\Essentials\Http\Controllers\EssentialsMessageGroupController::class, 'getUsers']);

        //Allowance and deduction controller
        Route::resource('allowance-deduction', 'Modules\Essentials\Http\Controllers\EssentialsAllowanceAndDeductionController');

        Route::resource('knowledge-base', 'Modules\Essentials\Http\Controllers\KnowledgeBaseController');

        Route::get('user-sales-targets', [Modules\Essentials\Http\Controllers\DashboardController::class, 'getUserSalesTargets']);
    });

    Route::prefix('hrm')->group(function () {
        Route::get('/dashboard', [Modules\Essentials\Http\Controllers\DashboardController::class, 'hrmDashboard'])->name('hrmDashboard');
        Route::resource('/leave-type', 'Modules\Essentials\Http\Controllers\EssentialsLeaveTypeController');
        Route::resource('/leave', 'Modules\Essentials\Http\Controllers\EssentialsLeaveController');
        Route::post('/change-status', [Modules\Essentials\Http\Controllers\EssentialsLeaveController::class, 'changeStatus']);
        Route::get('/leave/activity/{id}', [Modules\Essentials\Http\Controllers\EssentialsLeaveController::class, 'activity']);
        Route::get('/user-leave-summary', [Modules\Essentials\Http\Controllers\EssentialsLeaveController::class, 'getUserLeaveSummary']);
        Route::get('/quick-leave-balance', [Modules\Essentials\Http\Controllers\EssentialsLeaveController::class, 'getQuickLeaveBalance']);
        Route::get('/quick-leave-balance-view', [Modules\Essentials\Http\Controllers\EssentialsLeaveController::class, 'getQuickLeaveBalanceView']);
        Route::post('/check-leave-quota', [Modules\Essentials\Http\Controllers\EssentialsLeaveController::class, 'checkQuota']);

        Route::get('/settings', [Modules\Essentials\Http\Controllers\EssentialsSettingsController::class, 'edit']);
        Route::post('/settings', [Modules\Essentials\Http\Controllers\EssentialsSettingsController::class, 'update']);

        Route::post('/import-attendance', [Modules\Essentials\Http\Controllers\AttendanceController::class, 'importAttendance']);
        Route::resource('/attendance', 'Modules\Essentials\Http\Controllers\AttendanceController');
        Route::post('/clock-in-clock-out', [Modules\Essentials\Http\Controllers\AttendanceController::class, 'clockInClockOut']);

        Route::post('/validate-clock-in-clock-out', [Modules\Essentials\Http\Controllers\AttendanceController::class, 'validateClockInClockOut']);

        Route::get('/get-attendance-by-shift', [Modules\Essentials\Http\Controllers\AttendanceController::class, 'getAttendanceByShift']);
        Route::get('/get-attendance-by-date', [Modules\Essentials\Http\Controllers\AttendanceController::class, 'getAttendanceByDate']);
        Route::get('/get-attendance-row/{user_id}', [Modules\Essentials\Http\Controllers\AttendanceController::class, 'getAttendanceRow']);

        Route::get(
            '/user-attendance-summary',
            [Modules\Essentials\Http\Controllers\AttendanceController::class, 'getUserAttendanceSummary']
        );

        Route::get('/get-calendar-data', [Modules\Essentials\Http\Controllers\AttendanceController::class, 'getCalendarData']);
        Route::get('/get-month-summary', [Modules\Essentials\Http\Controllers\AttendanceController::class, 'getMonthSummary']);
        Route::get('/export-attendance', [Modules\Essentials\Http\Controllers\AttendanceController::class, 'exportAttendance'])->name('attendance.export');
        Route::get('/test-export', [Modules\Essentials\Http\Controllers\AttendanceController::class, 'testExport'])->name('attendance.test.export');

        Route::get('/location-employees', [Modules\Essentials\Http\Controllers\PayrollController::class, 'getEmployeesBasedOnLocation']);
        Route::get('/my-payrolls', [Modules\Essentials\Http\Controllers\PayrollController::class, 'getMyPayrolls']);
        Route::get('/get-allowance-deduction-row', [Modules\Essentials\Http\Controllers\PayrollController::class, 'getAllowanceAndDeductionRow']);
        Route::get('/payroll-group-datatable', [Modules\Essentials\Http\Controllers\PayrollController::class, 'payrollGroupDatatable']);
        Route::get('/view/{id}/payroll-group', [Modules\Essentials\Http\Controllers\PayrollController::class, 'viewPayrollGroup']);
        Route::get('/edit/{id}/payroll-group', [Modules\Essentials\Http\Controllers\PayrollController::class, 'getEditPayrollGroup']);
        Route::post('/update-payroll-group', [Modules\Essentials\Http\Controllers\PayrollController::class, 'getUpdatePayrollGroup']);
        Route::get('/payroll-group/{id}/add-payment', [Modules\Essentials\Http\Controllers\PayrollController::class, 'addPayment']);
        Route::post('/post-payment-payroll-group', [Modules\Essentials\Http\Controllers\PayrollController::class, 'postAddPayment']);
        Route::resource('/payroll', 'Modules\Essentials\Http\Controllers\PayrollController');
        Route::resource('/holiday', 'EssentialsHolidayController');

        Route::get('/shift/assign-users/{shift_id}', [Modules\Essentials\Http\Controllers\ShiftController::class, 'getAssignUsers']);
        Route::post('/shift/assign-users', [Modules\Essentials\Http\Controllers\ShiftController::class, 'postAssignUsers']);
        Route::resource('/shift', 'Modules\Essentials\Http\Controllers\ShiftController');
        Route::get('/sales-target', [Modules\Essentials\Http\Controllers\SalesTargetController::class, 'index']);
        
        Route::get('/set-sales-target/{id}', [Modules\Essentials\Http\Controllers\SalesTargetController::class, 'setSalesTarget']);
        Route::get('/get-sales-target/{id}', [Modules\Essentials\Http\Controllers\SalesTargetController::class, 'getSalesTarget']);
Route::get('sales-targets/ajax-targets', [Modules\Essentials\Http\Controllers\SalesTargetController::class, 'ajaxTargets']);

        Route::post('/save-sales-target', [Modules\Essentials\Http\Controllers\SalesTargetController::class, 'saveSalesTarget']);
    });
});
