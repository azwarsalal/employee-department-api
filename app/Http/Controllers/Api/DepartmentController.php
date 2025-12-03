<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    

    /**
     * Store a newly created resource in storage.
     */
     public function index()
    {
        try {
            $departments = Department::orderBy('id', 'DESC')->get();
            return response()->json(['success' => true, 'data' => $departments], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function store(Request $request)
    { // dd('store departments depcontroller');
        $request->validate([
            'name' => 'required|string|unique:departments,name',
            'description' => 'nullable|string|unique:departments,description',
        ]);

        try {
            $department = Department::create($request->only(['name', 'description']));

            return response()->json(['success' => true, 'data' => $department], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
     public function show($id)
    {
        try {
            $department = Department::findOrFail($id);
            return response()->json(['success' => true, 'data' => $department], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Department not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable|string|unique:departments,name,' . $id,
            'code' => 'nullable|string|unique:departments,code,' . $id,
        ]);

        try {
            $department = Department::findOrFail($id);
            $department->update($request->only(['name', 'description']));

            return response()->json(['success' => true, 'data' => $department], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $department = Department::findOrFail($id);
            $department->delete();

            return response()->json(['success' => true, 'message' => 'Department deleted'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Department not found'], 404);
        }
    }
}
