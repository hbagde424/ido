<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/ 

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::get('send-notification', [NotificationController::class, 'sendNotification']);


Route::post('register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/userstore', [AuthController::class, 'userstore']);
Route::post('/todolist', [AuthController::class, 'todolist']);
Route::post('/todocreate', [AuthController::class, 'todocreate']);
Route::post('/sendTodoNotifiction', [AuthController::class, 'sendTodoNotifiction']);
 
 Route::post('/reminderlist', [AuthController::class, 'reminderlist']);
 Route::post('/remindercreate', [AuthController::class, 'remindercreate']);

Route::post('/mark-todos-read', [AuthController::class, 'markTodosAsRead']);

 Route::post('/todocommentlist', [AuthController::class, 'todocommentlist']);
 Route::post('/addTodoComment', [AuthController::class, 'addTodoComment']);
 
 Route::post('/deleteTodo', [AuthController::class, 'deleteTodo']);
 Route::post('/deleteReminder', [AuthController::class, 'deleteReminder']);

 Route::post('/getCustomers', [AuthController::class, 'getCustomers']);
 Route::get('/getAllUsers', [AuthController::class, 'getAllUsers']);
 Route::get('/getRolesAndLocations', [AuthController::class, 'getRolesAndLocations']);
Route::post('/updateUser/', [AuthController::class, 'updateUser']);
 
Route::delete('/deleteUser/{id}', [AuthController::class, 'deleteUser']);

Route::post('/locations', [AuthController::class, 'store']); // Create Location
Route::post('/locationupdate/', [AuthController::class, 'update']); // Update Location
Route::delete('/locations/{id}', [AuthController::class, 'delete']); // Delete Location

Route::post('/updatetaskstatus/', [AuthController::class, 'updateTaskStatus']);

 Route::post('/getAllUsersWithCount', [AuthController::class, 'getAllUsersWithCount']);

Route::post('/attendance/check-in', [AuthController::class, 'checkIn']);
Route::post('/attendance/check-out', [AuthController::class, 'checkOut']);


