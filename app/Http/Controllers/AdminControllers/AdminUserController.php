<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        ResponseService::noAnyPermissionThenRedirect(['list-adminuser', 'create-adminuser', 'delete-adminuser']);
        $roles = Role::where('custom_role', 1)->get();
        $title = __('page.ADMIN');
        return view('admin.admin.index', compact('roles', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        ResponseService::noPermissionThenRedirect('create-adminuser');
        $roles = Role::where('custom_role', 1)->get();
        return view('admin.admin.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        ResponseService::noPermissionThenSendJson('create-adminuser');
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'role'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        try {
            DB::beginTransaction();
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->syncRoles($request->role);
            DB::commit();
            return response()->json([
                'status'   => 'success',
                'message'  => 'Admin User created Successfully',
                'redirect' => url('admin/admin-users'),
            ]);
        } catch (Throwable $th) {
            DB::rollBack();
            Log::error('AdminUserController --> store: ' . $th->getMessage());
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
        ResponseService::noPermissionThenRedirect('list-adminuser');
        define('LINK_PREFIX', "<a href='");

        $getData = User::withTrashed()->with('roles')->orderBy('id', 'DESC')->orderBy('id', 'DESC')->whereHas('roles', function ($q) {
            $q->where('custom_role', 1);
        });

        return DataTables::eloquent($getData)
            ->addColumn('role_name', function ($user) {
                return $user->roles->pluck('name')->implode(', '); // show multiple roles if exist
            })
            ->addColumn('action', function ($user) {
                $operate = '';

                // Check delete-adminuser permission
                if (auth()->user()->can('delete-adminuser')) {
                    $operate .= "<a href='" . route('admin-users.destroy', $user->id) . "'
        class='btn text-danger btn-sm admin-user-delete-form'
        data-id='{$user->id}'
        data-bs-toggle='tooltip'
        title='Delete'>
        <i class='fa fa-trash'></i>
     </a>";

                } else {
                    $operate .= "<span class='badge bg-danger text-white'>No permission for Delete</span>";
                }

                return $operate;
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            ResponseService::noPermissionThenSendJson('delete-adminuser');
            User::withTrashed()->findOrFail($id)->forceDelete();
            ResponseService::successResponse('User Delete Successfully');
        } catch (Throwable $th) {
            ResponseService::logErrorResponse($th, "AdminUserController --> delete");
            ResponseService::errorResponse();
        }
    }

    public function updateFCMID(Request $request)
    {
        $user         = User::find($request->id);
        $user->fcm_id = $request->token;
        $user->save();
    }
}
