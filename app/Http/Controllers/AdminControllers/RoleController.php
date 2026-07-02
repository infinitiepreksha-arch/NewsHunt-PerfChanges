<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        ResponseService::noAnyPermissionThenRedirect(['list-role', 'create-role', 'delete-role', 'update-role']);

        $roles     = Role::orderBy('id', 'DESC')->get();
        $title     = __('page.ROLE_MANAGEMENTS');
        $pre_title = __('page.ADMIN_USER');
        return view('admin.roles.index', compact('roles', 'title', 'pre_title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        ResponseService::noPermissionThenRedirect('create-role');

        Artisan::call('db:seed', [
            '--class' => 'PermissionsTableSeeder',
            '--force' => true,
        ]);

        $permissions = Permission::select('id', 'name')->get();

        $actionLabels = [
            'list'    => 'List',
            'create'  => 'Create',
            'store'   => 'Store',
            'show'    => 'Show',
            'edit'    => 'Edit',
            'update'  => 'Update',
            'delete'  => 'Delete',
            'toggle'  => 'Toggle',
            'status'  => 'Status',
            'reorder' => 'Reorder',
            'select'  => 'Select',
        ];

        $groupedPermissions = [];

        foreach ($permissions as $permission) {
            $parts = explode('-', $permission->name);

            // First part = action
            $action = strtolower(array_shift($parts));

            // Last part = group/category (e.g. post, story, channel)
            $group = ucwords(end($parts));

            // Module label = everything except the action
            $module = implode(' ', $parts);

            // Format label
            $actionLabel = $actionLabels[$action] ?? ucfirst($action);
            $moduleLabel = ucwords(str_replace('-', ' ', $module));
            $label       = trim($actionLabel . ' ' . $moduleLabel);

            if (! isset($groupedPermissions[$group])) {
                $groupedPermissions[$group] = [];
            }

            $groupedPermissions[$group][] = (object) [
                'id'    => $permission->id,
                'name'  => $permission->name,
                'label' => $label,
            ];
        }

        $title     = __('page.CREATE_ROLE');
        $pre_title = __('page.ROLE_MANAGEMENTS');

        return view('admin.roles.create', compact('groupedPermissions', 'title', 'pre_title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        ResponseService::noPermissionThenRedirect('create-role');
        $validator = Validator::make($request->all(), [
            'name'       => 'required|unique:roles,name',
            'permission' => 'required|array',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        try {
            DB::beginTransaction();
            $role = Role::create(['name' => $request->input('name'), 'custom_role' => 1]);

            $role->syncPermissions($request->input('permission'));
            DB::commit();
            return response()->json([
                'status'   => 'success',
                'message'  => 'Role created Successfully',
                'redirect' => route('roles.index'),
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, "Role Controller -> store", null, false);
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $getData = Role::select('id', 'name')->where('custom_role', 1)->get();

        return DataTables::of($getData)
            ->addColumn('no', function () {
                static $index = 1;
                return $index++;
            })
            ->addColumn('action', function ($row) {
                $actions = '';

                // Edit button (update-role)
                if (auth()->user()->can('update-role')) {
                    $actions .= "<a href='" . route('roles.edit', $row->id) . "'
                            class='btn text-primary btn-sm'
                            data-bs-toggle='tooltip'
                            title='Edit'>
                            <i class='fa fa-pen'></i>
                         </a> &nbsp;";
                } else {
                    $actions .= "<span class='badge bg-primary text-white me-1'>No permission for Edit</span>";
                }

                // Delete button (delete-role)
                if (auth()->user()->can('delete-role')) {
                    $actions .= "<a href='" . route('roles.destroy', $row->id) . "'
                            class='btn text-danger btn-sm delete-form'
                            data-bs-toggle='tooltip'
                            title='Delete' id='delete-role'>
                            <i class='fa fa-trash'></i>
                         </a>";
                } else {
                    $actions .= "<span class='badge bg-danger text-white'>No permission for Delete</span>";
                }

                return $actions;
            })
            ->make(true);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        ResponseService::noPermissionThenRedirect('update-role');

        $role = Role::findOrFail($id);

        // Ensure permissions are seeded
        if (Permission::count() === 0) {
            Artisan::call('db:seed', [
                '--class' => 'PermissionsTableSeeder',
                '--force' => true,
            ]);
        }

        // Fetch all permissions
        $permissions = Permission::select('id', 'name')->get();

        // Fetch existing role permissions
        $rolePermissions = DB::table("role_has_permissions")
            ->where("role_id", $id)
            ->pluck('permission_id')
            ->toArray();

        // Action labels
        $actionLabels = [
            'list'    => 'List',
            'create'  => 'Create',
            'store'   => 'Store',
            'show'    => 'Show',
            'edit'    => 'Edit',
            'update'  => 'Update',
            'delete'  => 'Delete',
            'toggle'  => 'Toggle',
            'status'  => 'Status',
            'reorder' => 'Reorder',
            'select'  => 'Select',
        ];

        // Group permissions
        $groupedPermissions = [];

        foreach ($permissions as $permission) {
            $parts = explode('-', $permission->name);

            // First part = action
            $action = strtolower(array_shift($parts));

            // Last part = group/category (e.g. post, story, channel)
            $group = ucwords(end($parts));

            // Module label = everything except the action
            $module = implode(' ', $parts);

            // Format label
            $actionLabel = $actionLabels[$action] ?? ucfirst($action);
            $moduleLabel = ucwords(str_replace('-', ' ', $module));
            $label       = trim($actionLabel . ' ' . $moduleLabel);

            if (! isset($groupedPermissions[$group])) {
                $groupedPermissions[$group] = [];
            }

            $groupedPermissions[$group][] = (object) [
                'id'         => $permission->id,
                'name'       => $permission->name,
                'label'      => $label,
                'is_checked' => in_array($permission->id, $rolePermissions), // ✅ pre-check
            ];
        }

        $title     = __('page.EDIT_ROLE');
        $pre_title = __('page.ROLE_MANAGEMENTS');

        return view('admin.roles.edit', compact('role', 'groupedPermissions', 'title', 'pre_title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        ResponseService::noPermissionThenRedirect('update-role');
        $validator = Validator::make($request->all(), [
            'name'       => 'required|unique:roles,name,' . $id,
            'permission' => 'required|array',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        try {
            DB::beginTransaction();
            $role       = Role::findOrFail($id);
            $role->name = $request->input('name');
            $role->save();
            $role->syncPermissions($request->input('permission'));
            DB::commit();
            return response()->json([
                'status'   => 'success',
                'message'  => 'Role Updated Successfully',
                'redirect' => route('roles.index'),
            ]);
        } catch (Throwable $th) {
            DB::rollBack();
            ResponseService::logErrorResponse($th, "RoleController -> update", null, false);
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            ResponseService::noPermissionThenSendJson('delete-role');
            $role = Role::withCount('users')->findOrFail($id);
            if ($role->users_count) {
                ResponseService::errorResponse('cannot_delete_because_data_is_associated_with_other_data');
            } else {
                Role::findOrFail($id)->delete();
                ResponseService::successResponse('Data Deleted Successfully');
            }
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function list($id)
    {
        ResponseService::noPermissionThenRedirect('list-role');
        $role            = Role::findOrFail($id);
        $rolePermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")->where("role_has_permissions.role_id", $id)->get();

        return view('roles.show', compact('role', 'rolePermissions'));
    }
}
