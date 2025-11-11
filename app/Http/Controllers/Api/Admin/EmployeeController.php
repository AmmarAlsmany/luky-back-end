<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class EmployeeController extends Controller
{
    /**
     * Get list of employees with pagination and filters
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $search = $request->input('search');
        $status = $request->input('status');
        $role = $request->input('role');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // Dashboard roles only
        $dashboardRoles = ['super_admin', 'manager', 'support_agent', 'content_manager', 'analyst'];

        $query = User::whereHas('roles', function ($q) use ($dashboardRoles) {
            $q->whereIn('name', $dashboardRoles);
        })
        ->with(['roles', 'city']);

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        // Status filter
        if ($status) {
            $query->where('status', $status);
        }

        // Role filter
        if ($role) {
            $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            });
        }

        // Date range filter
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Sorting
        $query->orderBy($sortBy, $sortOrder);

        $employees = $query->paginate($perPage);

        // Get stats
        $stats = [
            'total_employees' => User::whereHas('roles', fn($q) => $q->whereIn('name', $dashboardRoles))->count(),
            'active_employees' => User::whereHas('roles', fn($q) => $q->whereIn('name', $dashboardRoles))
                ->where('status', 'active')->count(),
            'inactive_employees' => User::whereHas('roles', fn($q) => $q->whereIn('name', $dashboardRoles))
                ->where('status', 'inactive')->count(),
            'super_admins' => User::role('super_admin')->count(),
            'managers' => User::role('manager')->count(),
            'support_agents' => User::role('support_agent')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'employees' => $employees->map(function ($employee) {
                    return [
                        'id' => $employee->id,
                        'name' => $employee->name,
                        'email' => $employee->email,
                        'phone' => $employee->phone,
                        'avatar' => $employee->avatar,
                        'status' => $employee->status,
                        'roles' => $employee->getRoleNames(),
                        'permissions_count' => $employee->getAllPermissions()->count(),
                        'created_by' => $employee->created_by,
                        'created_at' => $employee->created_at,
                        'last_login_at' => $employee->last_login_at,
                    ];
                }),
                'pagination' => [
                    'current_page' => $employees->currentPage(),
                    'per_page' => $employees->perPage(),
                    'total' => $employees->total(),
                    'last_page' => $employees->lastPage(),
                ],
                'stats' => $stats,
            ],
        ]);
    }

    /**
     * Get employee details
     */
    public function show($id)
    {
        $dashboardRoles = ['super_admin', 'manager', 'support_agent', 'content_manager', 'analyst'];

        $employee = User::whereHas('roles', function ($q) use ($dashboardRoles) {
            $q->whereIn('name', $dashboardRoles);
        })
        ->with(['roles', 'city'])
        ->find($id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
            ], 404);
        }

        // Get creator info
        $creator = null;
        if ($employee->created_by) {
            $creatorUser = User::find($employee->created_by);
            $creator = $creatorUser ? [
                'id' => $creatorUser->id,
                'name' => $creatorUser->name,
                'email' => $creatorUser->email,
            ] : null;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'phone' => $employee->phone,
                    'avatar' => $employee->avatar,
                    'status' => $employee->status,
                    'is_active' => $employee->is_active,
                    'city' => $employee->city,
                    'roles' => $employee->getRoleNames(),
                    'permissions' => $employee->getAllPermissions()->pluck('name'),
                    'created_by' => $creator,
                    'email_verified_at' => $employee->email_verified_at,
                    'phone_verified_at' => $employee->phone_verified_at,
                    'created_at' => $employee->created_at,
                    'last_login_at' => $employee->last_login_at,
                ],
            ],
        ]);
    }

    /**
     * Create new employee
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:8',
            'role' => 'required|in:super_admin,manager,support_agent,content_manager,analyst',
            'city_id' => 'nullable|exists:cities,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create user
        $employee = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'user_type' => 'admin',
            'city_id' => $request->city_id,
            'status' => 'active',
            'is_active' => true,
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'created_by' => auth()->id(),
        ]);

        // Assign role
        $employee->assignRole($request->role);

        // Assign additional permissions if provided
        if ($request->has('permissions')) {
            $employee->givePermissionTo($request->permissions);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'employee' => $employee->load('roles'),
                'roles' => $employee->getRoleNames(),
                'permissions' => $employee->getAllPermissions()->pluck('name'),
            ],
            'message' => 'Employee created successfully',
        ], 201);
    }

    /**
     * Update employee
     */
    public function update(Request $request, $id)
    {
        $dashboardRoles = ['super_admin', 'manager', 'support_agent', 'content_manager', 'analyst'];

        $employee = User::whereHas('roles', fn($q) => $q->whereIn('name', $dashboardRoles))->find($id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $employee->id,
            'phone' => 'sometimes|string|unique:users,phone,' . $employee->id,
            'city_id' => 'sometimes|exists:cities,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $employee->update($request->only(['name', 'email', 'phone', 'city_id']));

        return response()->json([
            'success' => true,
            'data' => ['employee' => $employee->load('roles')],
            'message' => 'Employee updated successfully',
        ]);
    }

    /**
     * Update employee status
     */
    public function updateStatus(Request $request, $id)
    {
        $dashboardRoles = ['super_admin', 'manager', 'support_agent', 'content_manager', 'analyst'];

        $employee = User::whereHas('roles', fn($q) => $q->whereIn('name', $dashboardRoles))->find($id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,inactive,suspended',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $employee->update([
            'status' => $request->status,
            'is_active' => $request->status === 'active',
        ]);

        return response()->json([
            'success' => true,
            'data' => ['employee' => $employee],
            'message' => 'Employee status updated successfully',
        ]);
    }

    /**
     * Delete employee (soft delete)
     */
    public function destroy($id)
    {
        $dashboardRoles = ['super_admin', 'manager', 'support_agent', 'content_manager', 'analyst'];

        $employee = User::whereHas('roles', fn($q) => $q->whereIn('name', $dashboardRoles))->find($id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
            ], 404);
        }

        // Prevent deleting yourself
        if ($employee->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete your own account',
            ], 403);
        }

        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Employee deleted successfully',
        ]);
    }

    /**
     * Assign or update employee role
     */
    public function assignRole(Request $request, $id)
    {
        $dashboardRoles = ['super_admin', 'manager', 'support_agent', 'content_manager', 'analyst'];

        $employee = User::whereHas('roles', fn($q) => $q->whereIn('name', $dashboardRoles))->find($id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'role' => 'required|in:super_admin,manager,support_agent,content_manager,analyst',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Remove all current roles and assign new one
        $employee->syncRoles([$request->role]);

        return response()->json([
            'success' => true,
            'data' => [
                'employee' => $employee->load('roles'),
                'roles' => $employee->getRoleNames(),
            ],
            'message' => 'Role assigned successfully',
        ]);
    }

    /**
     * Update employee permissions
     */
    public function updatePermissions(Request $request, $id)
    {
        $dashboardRoles = ['super_admin', 'manager', 'support_agent', 'content_manager', 'analyst'];

        $employee = User::whereHas('roles', fn($q) => $q->whereIn('name', $dashboardRoles))->find($id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Sync permissions (remove old, add new)
        $employee->syncPermissions($request->permissions);

        return response()->json([
            'success' => true,
            'data' => [
                'employee' => $employee,
                'permissions' => $employee->getAllPermissions()->pluck('name'),
            ],
            'message' => 'Permissions updated successfully',
        ]);
    }

    /**
     * Reset employee password
     */
    public function resetPassword(Request $request, $id)
    {
        $dashboardRoles = ['super_admin', 'manager', 'support_agent', 'content_manager', 'analyst'];

        $employee = User::whereHas('roles', fn($q) => $q->whereIn('name', $dashboardRoles))->find($id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $employee->update([
            'password' => Hash::make($request->new_password),
        ]);

        // Revoke all tokens to force re-login
        $employee->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully',
        ]);
    }

    /**
     * Get employee statistics
     */
    public function stats()
    {
        $dashboardRoles = ['super_admin', 'manager', 'support_agent', 'content_manager', 'analyst'];

        $stats = [
            'total_employees' => User::whereHas('roles', fn($q) => $q->whereIn('name', $dashboardRoles))->count(),
            'active_employees' => User::whereHas('roles', fn($q) => $q->whereIn('name', $dashboardRoles))
                ->where('status', 'active')->count(),
            'inactive_employees' => User::whereHas('roles', fn($q) => $q->whereIn('name', $dashboardRoles))
                ->where('status', 'inactive')->count(),
            'suspended_employees' => User::whereHas('roles', fn($q) => $q->whereIn('name', $dashboardRoles))
                ->where('status', 'suspended')->count(),
            'super_admins' => User::role('super_admin')->count(),
            'managers' => User::role('manager')->count(),
            'support_agents' => User::role('support_agent')->count(),
            'content_managers' => User::role('content_manager')->count(),
            'analysts' => User::role('analyst')->count(),
            'new_this_month' => User::whereHas('roles', fn($q) => $q->whereIn('name', $dashboardRoles))
                ->whereMonth('created_at', now()->month)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get available roles
     */
    public function getRoles()
    {
        $roles = Role::whereIn('name', ['super_admin', 'manager', 'support_agent', 'content_manager', 'analyst'])
            ->with('permissions')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'roles' => $roles->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'permissions' => $role->permissions->pluck('name'),
                        'permissions_count' => $role->permissions->count(),
                    ];
                }),
            ],
        ]);
    }

    /**
     * Get available permissions
     */
    public function getPermissions()
    {
        $permissions = Permission::all()->groupBy(function ($permission) {
            // Group permissions by category (e.g., view_clients, create_clients -> clients)
            $parts = explode('_', $permission->name);
            return count($parts) > 1 ? implode('_', array_slice($parts, 1)) : 'general';
        });

        return response()->json([
            'success' => true,
            'data' => [
                'permissions' => $permissions,
                'total' => Permission::count(),
            ],
        ]);
    }

    /**
     * Export employees to CSV
     */
    public function export(Request $request)
    {
        // TODO: Implement CSV export using Maatwebsite\Excel
        return response()->json([
            'success' => true,
            'message' => 'Export functionality coming soon',
        ]);
    }
}
