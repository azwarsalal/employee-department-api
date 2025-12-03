<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

    // ----------------------------------------------------
    // create employees
    // ----------------------------------------------------
    public function store(StoreEmployeeRequest $request)
    {
        try {
            DB::beginTransaction();

            $employee = Employee::create($request->only([
                'department_id', 'first_name', 'last_name', 'email', 'dob'
            ]));

            // Add Phones
             if ($request->has('phones')) {
                foreach ($request->phones as $item) {
                    EmployeePhone::create([
                        'employee_id' => $employee->id,
                        'phone'       => $item['phone'] ?? '',
                        'type'        => $item['type'] ?? 'mobile',
                    ]);
                }
            }

            // Add Addresses
            if ($request->has('addresses')) {
                foreach ($request->addresses as $addr) {
                    EmployeeAddress::create([
                        'employee_id'   => $employee->id,
                        'address_line1' => $addr['address_line1'],
                        'city'          => $addr['city'] ?? null,
                        'state'         => $addr['state'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Employee created successfully.',
                'data'    => $employee->load(['phones', 'addresses'])
            ], 201);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        //
    }
}
