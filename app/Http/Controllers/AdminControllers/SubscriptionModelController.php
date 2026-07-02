<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\CachingService;
use App\Services\FileService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class SubscriptionModelController extends Controller
{
    private string $uploadFolder;

    public function __construct()
    {
        $this->uploadFolder = 'settings';
    }

    public function store(Request $request)
    {
        ResponseService::noPermissionThenSendJson('subscription-model-and-header/footer-script-settings');

        try {
            // New validation format like PostController
            $validator = Validator::make($request->all(), [
                'subscribe_model_title'     => 'required|string',
                'subscribe_model_sub_title' => 'required|string',
                'subscribe_model_image'     => 'nullable|file|image|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

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
                    $filePath = $request->file($key)->store($this->uploadFolder, 'public');
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

            return response()->json([
                'status'  => 'success',
                'message' => 'Subscribe Modal Setting Updated Successfully!!',
            ]);

        } catch (Throwable $th) {
            ResponseService::logErrorResponse($th, "Setting Controller -> store");
            return response()->json([
                'status'  => 'error',
                'message' => 'Something Went Wrong',
            ], 500);
        }
    }
}
