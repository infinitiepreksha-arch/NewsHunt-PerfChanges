<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\CachingService;
use App\Services\FileService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Throwable;

class LogoSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private string $uploadFolder;
    protected $helperService;

    public function __construct()
    {
        $this->uploadFolder = 'settings';
    }

    public function store(Request $request)
    {
        ResponseService::noAnyPermissionThenRedirect(['logo-management-and-web-settings']);
        try {
            // Validate file inputs
            $request->validate([
                'company_logo'  => 'nullable|file|image|max:2048',
                'favicon_icon'  => 'nullable|file|image|max:2048',
                'dark_logo'     => 'nullable|file|image|max:2048',
                'light_logo'    => 'nullable|file|image|max:2048',
                'default_image' => 'nullable|file|image|max:2048', // Add this
                'web_theme_primary_colour' => 'nullable|string|size:7|regex:/^#[a-fA-F0-9]{6}$/',
                'app_theme_primary_colour' => 'nullable|string|size:7|regex:/^#[a-fA-F0-9]{6}$/',
                'web_font'                 => 'nullable|string',
                'app_font'                 => 'nullable|string',
            ]);

            $inputs = $request->input();
            unset($inputs['_token']);
            $data = [];
            foreach ($inputs as $key => $input) {
                $data[] = [
                    'name'  => $key,
                    'value' => $input,
                    'type'  => 'string',
                ];
            }

            // Set upload folder
            $this->uploadFolder = 'settings';

            // Fetch old images to delete from disk storage
            $oldSettingFiles = Setting::whereIn('name', collect($request->files)->keys())->get();
            foreach ($request->files as $key => $file) {
                if ($request->hasFile($key)) {
                    $filePath = FileService::resizeAndCompressUpload($request->file($key), $this->uploadFolder, 800, null, 'webp');
                    $data[]   = [
                        'name'  => $key,
                        'value' => $filePath,
                        'type'  => 'file',
                    ];
                    $oldFile = $oldSettingFiles->first(function ($old) use ($key) {
                        return $old->name == $key;
                    });
                    if (! empty($oldFile)) {
                        FileService::delete($oldFile->getRawOriginal('value'));
                    }
                }
            }

            Setting::upsert($data, 'name', ['value']);
            CachingService::removeCache(config('constants.CACHE.SETTINGS'));
            return redirect()->back()->with('success', 'Settings Updated Successfully!!');

        } catch (Throwable $th) {
            ResponseService::logErrorResponse($th, "Setting Controller -> store");
            ResponseService::errorResponse('Something Went Wrong');
        }
    }
}
