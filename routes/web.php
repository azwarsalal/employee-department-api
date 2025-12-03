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
//create department
Route::any('/api/departments', [DepartmentController::class,'store']);
