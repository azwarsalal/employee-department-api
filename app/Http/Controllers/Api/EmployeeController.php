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
    // ----------------------------------------------------
    // employees
    // ----------------------------------------------------
    public function index()
    {
        try {
           
            //with search filter

             $q = Employee::with(['department','phones','addresses']);
        // Search by name, email, department
        if ($request->filled('search')) {
            $search = $request->search;
            $q->where(function($q2) use ($search){
                $q2->where('first_name','like',"%{$search}%")
                   ->orWhere('last_name','like',"%{$search}%")
                   ->orWhere('email','like',"%{$search}%");
            });
        }
        if ($request->filled('department_id')) {
            $q->where('department_id', $request->department_id);
        }
        $employees = $q->orderBy('id','desc')->paginate($request->get('per_page', 15));
        //return EmployeeResource::collection($employees); //return response a

            //

            return response()->json(['success' => true, 'data' => $employees], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
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
    // ----------------------------------------------------
    // GET /employees/{id}
    // ----------------------------------------------------
    public function show($id)
    {
        try {
            $employee = Employee::with(['phones', 'addresses', 'department'])->findOrFail($id);

            return response()->json([
                'status' => true,
                'data'   => $employee
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Employee not found.'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    // ----------------------------------------------------
    // PUT /employees/{id}
    // ----------------------------------------------------
    public function update(UpdateEmployeeRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $employee = Employee::findOrFail($id);

            $employee->update($request->only([
                'department_id', 'first_name', 'last_name', 'email', 'dob'
            ]));

            // Delete old phones/addresses and re-add
            $employee->phones()->delete();
            $employee->addresses()->delete();

            if ($request->phones) {
                foreach ($request->phones as $phone) {
                    $employee->phones()->create($phone);
                }
            }

            if ($request->addresses) {
                foreach ($request->addresses as $address) {
                    $employee->addresses()->create($address);
                }
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Employee updated successfully.',
                'data'    => $employee->load(['phones', 'addresses'])
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
     // ----------------------------------------------------
    // DELETE /employees/{id}
    // ----------------------------------------------------
    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);

            $employee->phones()->delete();
            $employee->addresses()->delete();
            $employee->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Employee deleted successfully.'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Employee not found.'
            ], 404);
        }
    }
}
