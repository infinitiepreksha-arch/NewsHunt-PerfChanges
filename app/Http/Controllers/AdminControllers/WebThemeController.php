<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Theme;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class WebThemeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        ResponseService::noAnyPermissionThenRedirect(['list-webtheme', 'create-webtheme', 'update-webtheme', 'delete-webtheme']);
        return view('admin.settings.web-theme');
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
    public function store(Request $request, Theme $theme)
    {
        ResponseService::noPermissionThenRedirect('create-webtheme');
        $request->validate([
            'name'   => 'required',
            'status' => 'required',
            'image'  => 'required|max:2000|mimes:jpg,jpeg,png,webp,svg',
        ], );

        /* Store the channel logo. */
        $file = $request->file('image');
        if ($file) {
            $fileName = rand('0000', '9999') . $file->getClientOriginalName();
            $path     = $file->storeAs('theme_image', $fileName, 'public');
            storage_path('app/public/' . $path);
        }
        $slug         = Str::slug($request->name);
        $originalSlug = $slug;
        $counter      = 1;
        while (Theme::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $theme->name   = $request->name;
        $theme->image  = $fileName;
        $theme->slug   = $slug;
        $theme->status = $request->status;
        $save          = $theme->save();
        if ($save) {
            return redirect()->route('web_theme.index')->with('success', 'Theme created successfully.');
        } else {
            return redirect()->back()->with('error', 'Somthing went wrong.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        ResponseService::noPermissionThenRedirect('list-webtheme');
        $getTheme = Theme::select('id', 'image', 'name', 'slug', 'is_default', 'status')->get();

        $getTheme->each(function ($channel) {
            $channel->image = asset('storage/theme_image/' . $channel->image);
        });

        return DataTables::of($getTheme)
            ->addColumn('action', function ($getData) {

                return;
                "<a href='" . route('topics.edit', $getData->id) . "' class='btn text-primary btn-sm edit_btn' data-bs-toggle='modal' data-bs-target='#editWebTheme' title='editTheme'> <i class='fa fa-pen'></i></a> &nbsp; " .
                "<a href='" . route('topics.destroy', $getData->id) . "' class='btn text-danger btn-sm delete-form delete-form-reload' data-bs-toggle='tooltip' title='Delete'> <i class='fa fa-trash'></i> </a>";
            })
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        ResponseService::noPermissionThenRedirect('update-webtheme');
        $request->validate([
            'name'   => 'required',
            'status' => 'required',
            'image'  => 'nullable|max:2000|mimes:jpg,jpeg,png,webp,svg',
        ]);
        $theme = Theme::findOrFail($id);

        if ($request->hasFile('image')) {

            if ($theme->image) {
                $oldImagePath = storage_path('app/public/theme_image/' . $theme->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $file     = $request->file('image');
            $fileName = rand('0000', '9999') . $file->getClientOriginalName();
            $file->storeAs('theme_image', $fileName, 'public');
            $theme->image = $fileName;
        }

        // Update the theme details
        $slug         = Str::slug($request->name);
        $originalSlug = $slug;
        $counter      = 1;

        // Check if the slug already exists
        while (Theme::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $theme->name   = $request->name;
        $theme->slug   = $slug;
        $theme->status = $request->status;

        // Save the updated theme
        $save = $theme->save();
        if ($save) {
            return redirect()->route('web_theme.index')->with('success', 'Theme updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function updateStatus(Request $request)
    {
        try {

            $theme = Theme::find($request->id);

            if ($request->status == '1') {
                Theme::where('id', '!=', $request->id)->update(['is_default' => 0]);
            }

            $theme->is_default = $request->status;
            $theme->save();

            return response()->json([
                'message' => $request->status == '1' ? 'Theme Activated.' : 'Theme Inactivated',
            ]);

        } catch (Throwable $e) {
            return "";
        }
    }

}
