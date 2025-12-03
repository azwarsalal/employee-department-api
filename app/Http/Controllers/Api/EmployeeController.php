<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeePhone;
use App\Models\EmployeeAddress;

use Illuminate\Http\Request;
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
    public function index(Request $request)
{
    try {
        $q = Employee::select('employees.*', 'departments.name as department_name')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id');

        // Search by name, email
        if ($request->filled('search')) {
            $search = $request->search;
           //dd($search);
            $q->where(function($query) use ($search) {
                $query->where('employees.first_name', 'like', "%{$search}%")
                      ->orWhere('employees.last_name', 'like', "%{$search}%")
                      ->orWhere('employees.email', 'like', "%{$search}%");
            });
        }

        // Filter by department
        if ($request->filled('department_id')) {
            $q->where('employees.department_id', $request->department_id);
        }

        // Pagination
        $employees = $q->orderBy('employees.id', 'desc')
                       ->paginate($request->get('per_page', 15));

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
    public function store(Request $request)
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
                      $city= isset($addr['city']) ? $addr['city'] : '';
                      $state= isset($addr['state']) ? $addr['state'] : '';
                      $address_line1= isset($addr['address_line1']) ? $addr['address_line1'] : '';
                    EmployeeAddress::create([
                        'employee_id'   => $employee->id,
                        'address_line1' => $address_line1,
                        'city'          => $city,
                        'state'         => $state,
                        'country'         => 'INDIA',
                    ]);
                }
            }

            DB::commit();
           

            return response()->json([
                'status'  => true,
                'message' => 'Employee created successfully.',
                'data'    => $employee
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
           // $employee = Employee::with(['phones', 'addresses', 'department'])->findOrFail($id);
            $employee = Employee::findOrFail($id);

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

    //store department
    public function storeDepartment(Request $request)
    { dd('store departments');
        $request->validate([
            'name' => 'required|string|unique:departments,name',
            'code' => 'nullable|string|unique:departments,code',
        ]);

        try {
            $department = Department::create($request->only(['name', 'code']));

            return response()->json(['success' => true, 'data' => $department], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
