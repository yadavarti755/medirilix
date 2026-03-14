<?php

namespace App\Http\Controllers\Secure;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;
use App\Services\RoleService;
use App\DTO\RoleDto;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct()
    {
        $this->roleService = new RoleService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageTitle = "Roles";
        $roles = $this->roleService->findAllWithPermissions();
        return view('secure.roles.index', compact('pageTitle', 'roles'));
    }

    public function fetchRolesForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $roles = $this->roleService->findForDatatable();

            return DataTables::of($roles)
                ->addColumn('permissions', function ($role) {
                    if ($role->name == 'SUPERADMIN') {
                        return '<span class="badge bg-success rounded-pill">Full Permissions</span>';
                    }
                    if ($role->name == 'EMPLOYEE') {
                        return '<span class="badge bg-success rounded-pill">Employee Permissions</span>';
                    }
                    // If no permissions
                    if ($role->permissions->pluck('name')->count() < 1) {
                        return '<span class="badge bg-danger rounded-pill">No Permissions</span>';
                    }
                    // If permissions assigned
                    $lists = '<div class="btn-group">
                                <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Permissions List
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" style="max-height: 300px; overflow-y: auto;">';
                    foreach ($role->permissions->pluck('name') as $permission) {
                        $lists .= '<li class="list-group-item py-2 px-2">' . $permission . '</li>';
                    }
                    $lists .= '</ul></div>';
                    return $lists;
                })
                ->addColumn('action', function ($role) {
                    return '<a href="' . route('roles.edit', $role->id) . '" class="btn btn-sm btn-warning" title="Edit"><i class="fa fa-edit"></i></a>
                    <button class="btn btn-sm btn-danger delete-role" data-id="' . $role->id . '" title="Delete">
                        <i class="fa fa-trash"></i>
                    </button>';
                })
                ->rawColumns(['permissions', 'action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = "Create Role";
        $permissionGroups = $this->roleService->getAllPermissionsGrouped(); // Group by stored category
        return view('secure.roles.create', compact('permissionGroups', 'pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Custom validation messages
            $messages = [
                'name.required' => 'Role name is required.',
                'name.unique' => 'Role name must be unique.',
                'name.min' => 'Role name must be at least 3 characters.',
                // 'permissions.required' => 'Please select at least one permission.'
            ];

            // Validate request
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:roles,name|min:3',
                // 'permissions' => 'array|required'
            ], $messages);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Create role and assign permissions
            $roleDto = new RoleDto(
                $request->name,
                $request->landing_page_url,
                $request->permissions,
                Auth::id(),
                Auth::id()
            );

            $this->roleService->create($roleDto);

            return response()->json(['message' => 'Role created successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $role = $this->roleService->findById($id);
        $permissionGroups = $this->roleService->getAllPermissionsGroupedByScope();
        $rolePermissions = $this->roleService->getPermissionsForRole($role)->pluck('name')->toArray();

        return view('secure.roles.edit', compact('role', 'permissionGroups', 'rolePermissions'))->with('pageTitle', 'Edit Role');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|min:3|unique:roles,name,' . $id,
                // 'permissions' => 'array|required'
            ]);

            $roleDto = new RoleDto(
                $request->name,
                $request->landing_page_url,
                $request->permissions,
                null,
                Auth::id()
            );

            $this->roleService->update($roleDto, $id);

            return response()->json(['message' => 'Role updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->roleService->delete($id);

            return response()->json(['message' => 'Role moved to trash successfully!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong!'], 500);
        }
    }

    public function getPermissionsByRole(Request $request)
    {
        try {
            $roleName = $request->input('role');
            $role = $this->roleService->findByName($roleName);

            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role not found'
                ], 404);
            }

            // Get only permissions that belong to this role
            $rolePermissions = $this->roleService->getPermissionsForRole($role);

            // Group these permissions by their group attribute
            $permissionGroups = $rolePermissions->groupBy('group');

            return response()->json([
                'success' => true,
                'permissions' => $rolePermissions,
                'permissionGroups' => $permissionGroups
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server failed while processing request'
            ], 500);
        }
    }
}
