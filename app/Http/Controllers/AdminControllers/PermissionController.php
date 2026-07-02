<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        ResponseService::noAnyPermissionThenRedirect(['list-permission', 'create-permission', 'update-permission', 'delete-permission']);
       return view('admin.permission.create-permission');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Permission $permission)
    {
        ResponseService::noPermissionThenSendJson('create-permission');
        $request->validate([
            'name' => 'required',
            'guard_name' => 'required'
        ]);
       $permission->name = $request->name;
       $permission->guard_name = $request->guard_name;
       $save = $permission->save();
       if($save){
           return redirect()->route('permission.index')->with('success','Created Successfully...!');
       }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        ResponseService::noPermissionThenSendJson('list-permission');
        $getData = Permission::get();
        return DataTables::of($getData)
        ->addColumn('action', function ($getData) {
            return "<a href='" . route('permission.edit', $getData->id) . "' class='btn text-primary btn-sm edit_btn' data-bs-toggle='modal' data-bs-target='#editPermissionModal' title='editPermission'> <i class='fa fa-pen'></i></a> &nbsp; " .
                    "<a href='" . route('permission.destroy', $getData->id) . "' class='btn text-danger btn-sm delete-form delete-form-reload' data-bs-toggle='tooltip' title='Delete'> <i class='fa fa-trash'></i> </a>";
        })->make(true);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        ResponseService::noPermissionThenSendJson('update-permission');
            $request->validate([
            'name' => 'required',
            'guard_name' => 'required'
        ]);
        $permission = Permission::find($request->id);
       $permission->name = $request->name;
       $permission->guard_name = $request->guard_name;
       $save = $permission->save();
       if($save){
           return redirect()->route('permission.index')->with('success','Updated Successfully...!');
       }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            ResponseService::noPermissionThenSendJson('delete-permission');
            Permission::find($id)->delete();
            ResponseService::successResponse("Topic deleted Successfully");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "PlaceController -> destroyCountry");
            ResponseService::errorResponse('Something Went Wrong');
        }
    }
}
