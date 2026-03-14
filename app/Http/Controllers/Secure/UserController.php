<?php

namespace App\Http\Controllers\Secure;

use App\DTO\UserDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageTitle = 'Users';
        return view('secure.users.index', compact('pageTitle'));
    }

    /**
     * Fetch a listing of the resource.
     */
    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $users = $this->userService->findAll();
            return DataTables::of($users)
                ->addColumn('roles', function ($user) {
                    return $user->roles->pluck('name')->join(', ');
                })
                ->addColumn('action', function ($user) {
                    $button = '';

                    if (auth()->user()->can('reset password')) {
                        if (!in_array('USER', $user->roles->pluck('name')->toArray())) {
                            $button .= '<button class="btn btn-sm btn-primary btn-reset-password" data-id="' . $user->id . '" title="Reset Password">
                                <i class="fa fa-sync"></i>
                            </button> ';

                            if (auth()->user()->can('edit user')) {
                                $button .= '<a href="' . route('users.edit', $user->id) . '" class="btn btn-sm btn-warning" title="Edit"><i class="fa fa-edit"></i></a> ';
                            }

                            if (auth()->user()->can('delete user')) {
                                if (!in_array('SUPERADMIN', $user->roles->pluck('name')->toArray())) {
                                    $button .= '<button class="btn btn-sm btn-danger delete-user" data-id="' . $user->id . '" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>';
                                }
                            }
                        }
                    }


                    return $button;
                })
                ->rawColumns(['roles', 'action', 'permissions'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Add Users';
        $roles = Role::where('name', '!=', 'EMPLOYEE')->get();
        return view('secure.users.create', compact('pageTitle', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        try {

            // Get the selected role
            $roleName = $request->input('roles');
            $role = Role::where('name', $roleName)->first();

            // Get permissions for the role
            // $permissions = $role ? $role->permissions->pluck('name')->toArray() : [];

            $userDto = new UserDto(
                strip_tags($request->input('name')),
                strip_tags($request->input('email')),
                strip_tags($request->input('mobile_number')),
                $request->input('password') ? Hash::make($request->input('password')) : Hash::make(env('DEFAULT_RESET_PASSWORD')),
                $request->input('roles'),
                // $permissions,
                auth()->user()->id,
                auth()->user()->id
            );

            $user = $this->userService->create($userDto);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while saving user.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'User created successfully!'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server failed while processing request'
            ], 500);
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
    public function edit(string $id)
    {
        $user = $this->userService->findById($id);
        $roles = Role::where('name', '!=', 'EMPLOYEE')->get();
        $userRoles = $user->roles->pluck('name')->toArray();

        // Get user's direct permissions
        // $userPermissions = $user->getDirectPermissions()->pluck('name')->toArray();
        return view('secure.users.edit', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            // Get the selected role
            $roleName = $request->input('roles');
            $role = Role::where('name', $roleName)->first();

            // $permissions = $request->input('permissions', []);

            $userDto = new UserDto(
                strip_tags($request->input('name')),
                strip_tags($request->input('email')),
                strip_tags($request->input('mobile_number')),
                '',
                $request->input('roles'),
                // $permissions,
                $user->created_by,
                auth()->user()->id
            );
            $user = $this->userService->update($userDto, $user->id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while updating user.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = $this->userService->findById($id);
            if (in_array('SUPERADMIN', $user->roles->pluck('name')->toArray())) {
                return response()->json(['message' => 'You can not delete superadmin'], 400);
            }

            $user = $this->userService->delete($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while deleting user.',
                ], 500);
            }

            return response()->json(['message' => 'User moved to trash successfully!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong!'], 500);
        }
    }

    public function resetPassword(User $user)
    {
        try {
            $userDto = new UserDto(
                $user->name,
                $user->email,
                $user->mobile_number,
                Hash::make(rand(99999, 99999999)),
                $user->getRoleNames()->toArray(), // Get current roles
                $user->created_by,
                auth()->id() // Current user as updated_by
            );

            $user = $this->userService->resetPassword($userDto, $user->id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while reseting user password.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'User password reseted successfully!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
