<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminAccount\UpdateAdminRequest;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function index()
    {
        $admins = Admin::with('admin_roles:id,role')->paginate(5);
        return response()->json($admins);
    }

    public function show($id)
    {
        $admin = Admin::with('admin_roles')->findOrFail($id);
        return response()->json($admin);
    }

    public function update(UpdateAdminRequest $request, $id)
    {
        $validatedData = $request->validated();

        $admin = Admin::findOrFail($id);
        $admin->role_id = $validatedData['role_id'];
        $admin->save();
        $admin->load('admin_roles');

        return response()->json($admin);
    }

    public function destroy($id)
    {
        Admin::destroy($id);
        return response()->json(null, 204);
    }
}
