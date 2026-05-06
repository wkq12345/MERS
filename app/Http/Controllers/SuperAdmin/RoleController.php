<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        $data = [
            'roles' => $roles,
            'status' => '200',
        ];

        return response()->json($data, 200);
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'role_name' => 'required|unique:roles,role_name',
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 422);
        } else {
            $role = new Role();
            $role->role_name = $request->role_name;
            $role->save();

            return response()->json(['message' => 'Role created successfully', 'role' => $role], 201);
        }
    }

    public function edit(Request $request, $id)
    {
        $validation = Validator::make($request->all(), [
            'role_name' => 'required|unique:roles,role_name,' . $id,
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 422);
        } else {
            $role = Role::find($id);
            if (!$role) {
                return response()->json(['message' => 'Role not found'], 404);
            }

            $role->role_name = $request->role_name;
            $role->save();

            return response()->json(['message' => 'Role updated successfully', 'role' => $role], 200);
        }
    }
    public function destroy($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        $role->delete();
        return response()->json(['message' => 'Role deleted successfully'], 200);
    }
}
