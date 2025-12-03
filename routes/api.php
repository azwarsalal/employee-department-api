<?php
 use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\EmployeeController;

Route::apiResource('departments', DepartmentController::class);
Route::apiResource('employees', EmployeeController::class);

// search employee
Route::get('employees/search', [EmployeeController::class,'index']);
Route::post('departments', [DepartmentController::class,'store']);