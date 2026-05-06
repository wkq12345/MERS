<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function index()
    {
        $superAdmin = User::where('role_id', 1)->get();
        $data = [
            'superAdmin' => $superAdmin,
            'status' => '200',
        ];

        return response()->json($data, 200);
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 422);
        } else {
            $superAdmin = new User();
            $superAdmin->name = $request->name;
            $superAdmin->email = $request->email;
            $superAdmin->password = bcrypt('password');
            $superAdmin->role_id = 1;
            $superAdmin->save();

            return response()->json(['message' => 'Super Admin created successfully', 'superAdmin' => $superAdmin], 201);
        }
    }

    public function edit(Request $request, $id)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 422);
        } else {
            $superAdmin = User::where('role_id', 1)->find($id);
            if (!$superAdmin) {
                return response()->json(['message' => 'Super Admin not found'], 404);
            }

            $superAdmin->name = $request->name;
            $superAdmin->email = $request->email;
            if ($request->filled('password')) {
                $superAdmin->password = bcrypt($request->password);
            }
            $superAdmin->save();

            return response()->json(['message' => 'Super Admin updated successfully', 'superAdmin' => $superAdmin], 200);
        }
    }
    public function destroy($id)
    {
        $superAdmin = User::where('role_id', 1)->find($id);
        if (!$superAdmin) {
            return response()->json(['message' => 'Super Admin not found'], 404);
        }

        $superAdmin->delete();
        return response()->json(['message' => 'Super Admin deleted successfully'], 200);
    }
}
