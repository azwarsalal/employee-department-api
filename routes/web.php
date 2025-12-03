<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\EmployeeController;

Route::get('/', function () {
    //dd('tff');
    return view('welcome');
});

//Route::apiResource('departments', DepartmentController::class);
//Route::apiResource('employees', EmployeeController::class);
Route::post('/check', function () {
    return 'OK';
});


// search employee
Route::get('/api/employees/search', [EmployeeController::class,'index']);
Route::any('/api/employees', [EmployeeController::class,'store']);
Route::any('/api/employees/{id}', [EmployeeController::class,'show']);
Route::any('/api/employees/delete/{id}', [EmployeeController::class,'destroy']);
Route::any('/api/employees/update/{id}', [EmployeeController::class,'update']);
//create department
Route::any('/api/departments', [DepartmentController::class,'store']);
