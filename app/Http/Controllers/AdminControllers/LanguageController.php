<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Services\CachingService;
use App\Services\FileService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Throwable;

class LanguageController extends Controller
{
    private string $uploadFolder;
    const SOMTHING_WENT_WRONG = 'Something Went Wrong';
    const APPLICATION_JSON    = 'application/json';
    const NULLABLE_JSON       = 'nullable|mimes:json';

    public function __construct()
    {
        $this->uploadFolder = "language";
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        ResponseService::noAnyPermissionThenRedirect(['language-translation-settings']);

        $english = Language::find(1);
        if (! $english || empty($english->name) || empty($english->code) || empty($english->admin_panel_files) || empty($english->web_files) || empty($english->image)) {
            Language::updateOrInsert(
                ['id' => 1],
                [
                    'name'              => 'English',
                    'code'              => 'en',
                    'admin_panel_files' => json_encode([
                        "lang/en/message.php",
                        "lang/en/page.php",
                        "lang/en/global.php",
                    ]),
                    'web_files'         => json_encode([
                        "lang/en/frontend-labels.php",
                    ]),
                    'image'             => 'language/en.svg',
                ]
            );
        }
        $languages            = Language::all() ?? collect([]);
        $selected_language_id = request()->query('language_id', $languages->first()->id ?? 0);

        $selected_language = Language::find($selected_language_id);
        $language_code     = $selected_language ? $selected_language->code : app()->getLocale();

        $data = [
            'languages'            => $languages,
            'language_code'        => $language_code,
            'selected_language_id' => $selected_language_id,
            'selected_language'    => $selected_language,
        ];

        return view('admin.settings.language', $data);
    }

    public function store_language(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'            => 'required',
            'name_in_english' => 'required|regex:/^[\pL\s]+$/u',
            'code'            => 'required|unique:languages,code',
            'rtl'             => 'nullable',
            'image'           => 'required|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }

        try {
            $data = $request->all();
            if ($request->hasFile('image')) {
                $data['image'] = FileService::upload($request->file('image'), $this->uploadFolder);
            }

            Language::create($data);

            return response()->json([
                'success'  => 'Language Created Successfully.',
                'redirect' => route('language.index'),
            ]);

        } catch (Throwable $th) {
            ResponseService::logErrorRedirect($th, "Language Controller -> Store Language");
            ResponseService::errorResponse(self::SOMTHING_WENT_WRONG);
        }
    }

    public function getTranslations($id)
    {
        try {
            $language     = Language::findOrFail($id);
            $translations = [
                'message'  => $this->loadTranslations($language->code, 'message'),
                'page'     => $this->loadTranslations($language->code, 'page'),
                'global'   => $this->loadTranslations($language->code, 'global'),
                'frontend' => $this->loadTranslations($language->code, 'frontend-labels'),
            ];

            return response()->json($translations);
        } catch (Throwable $th) {
            return response()->json(['error' => self::SOMTHING_WENT_WRONG], 500);
        }
    }

    private function loadTranslations($languageCode, $fileName)
    {
        $filePath = resource_path("lang/{$languageCode}/{$fileName}.php");
        if (File::exists($filePath)) {
            $translations = include $filePath;
            return is_array($translations) ? $translations : [];
        }
        return [];
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'language_id'  => 'required|exists:languages,id',
            'tab_type'     => 'required|in:admin_panel,frontend',
            'translations' => 'required|array',
        ]);

        if ($validator->fails()) {
            return ResponseService::validationError($validator->errors()->first());
        }

        try {
            $language     = Language::findOrFail($request->language_id);
            $languageCode = $language->code;
            $translations = $request->translations;
            $tabType      = $request->tab_type;

            $fileMappings = $tabType === 'admin_panel'
                ? ['message' => 'message', 'page' => 'page', 'global' => 'global']
                : ['frontend-labels' => 'frontend-labels'];

            $savedFiles = [];

            foreach ($fileMappings as $inputKey => $fileName) {
                if (isset($translations[$inputKey])) {
                    $filePath = resource_path("lang/{$languageCode}/{$fileName}.php");

                    if (! File::exists(dirname($filePath))) {
                        File::makeDirectory(dirname($filePath), 0755, true);
                    }

                    $existingTranslations = File::exists($filePath)
                        ? include $filePath
                        : [];

                    if (! is_array($existingTranslations)) {
                        $existingTranslations = [];
                    }

                    $updatedTranslations = $this->arrayMergeRecursiveDistinct($existingTranslations, $translations[$inputKey]);

                    File::put($filePath, "<?php\n\nreturn " . var_export($updatedTranslations, true) . ";\n");

                    $savedFiles[] = "lang/{$languageCode}/{$fileName}.php";
                }
            }

            // ✅ Update JSON fields
            if ($tabType === 'admin_panel') {
                $existingFiles               = $language->admin_panel_files ?? [];
                $language->admin_panel_files = array_values(array_unique(array_merge($existingFiles, $savedFiles)));
            } else {
                $existingFiles       = $language->web_files ?? [];
                $language->web_files = array_values(array_unique(array_merge($existingFiles, $savedFiles)));
            }

            $language->save();

            CachingService::removeCache(config('constants.CACHE.LANGUAGE'));

            return redirect()->back()->with('success', 'Translations successfully updated.');
        } catch (Throwable $th) {
            ResponseService::logErrorRedirect($th, "Language Controller -> Store");
            return ResponseService::errorResponse(self::SOMTHING_WENT_WRONG);
        }
    }

    /**
     * ✅ Helper: Deep merge arrays recursively (preserving nested keys)
     */
    private function arrayMergeRecursiveDistinct(array &$array1, array &$array2): array
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->arrayMergeRecursiveDistinct($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    public function uploadFile(Request $request)
    {

        // Custom validation for PHP files
        $request->validate([
            'language_id'  => 'required|exists:languages,id',
            'files'        => 'required|array|min:1',
            'files.*.type' => 'required_with:files.*.file|in:message,page,global,frontend-labels',
        ]);

        try {
            $language = Language::findOrFail($request->input('language_id'));
            $files    = $request->input('files');

            $adminPanelFiles = $language->admin_panel_files ?? [];
            $webFiles        = $language->web_files ?? [];

            $uploadedCount = 0;
            $skippedFiles  = [];

            foreach ($files as $index => $fileData) {

                // Check if file exists
                if (! isset($fileData['type'])) {
                    $skippedFiles[] = "File slot {$index}: No type specified";
                    continue;
                }

                if (! $request->hasFile("files.{$index}.file")) {
                    $skippedFiles[] = "{$fileData['type']}: No file uploaded";
                    continue;
                }

                $fileType     = $fileData['type'];
                $uploadedFile = $request->file("files.{$index}.file");

                // Check file extension
                if ($uploadedFile->getClientOriginalExtension() !== 'php') {
                    return redirect()->back()
                        ->with('error', "File for {$fileType} must have .php extension (got: {$uploadedFile->getClientOriginalExtension()})")
                        ->withInput();
                }

                // Check file size (max 2MB)
                if ($uploadedFile->getSize() > 2048 * 1024) {
                    return redirect()->back()
                        ->with('error', "File for {$fileType} is too large (max 2MB)")
                        ->withInput();
                }

                $tempPath = $uploadedFile->getRealPath();
                try {
                    $content = file_get_contents($tempPath);

                    if (empty($content)) {
                        return redirect()->back()
                            ->with('error', "File for {$fileType} is empty")
                            ->withInput();
                    }
                    $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

                    // Check if it starts with <?php
                    if (! preg_match('/^\s*<\?php/i', $content)) {
                        return redirect()->back()
                            ->with('error', "Invalid PHP file for {$fileType}. File must start with <?php")
                            ->withInput();
                    }

                    // Check if it contains a return statement
                    if (! preg_match('/return\s+(array\s*\(|\[)/i', $content)) {
                        return redirect()->back()
                            ->with('error', "Invalid translation file for {$fileType}. File must contain 'return array(' or 'return ['")
                            ->withInput();
                    }

                    ob_start();
                    error_reporting(0);
                    $tempTranslations = @include $tempPath;
                    error_reporting(E_ALL);
                    $output = ob_get_clean();

                    $output = trim($output);
                    if (! empty($output) && strlen($output) > 0) {
                        if (strlen($output) > 5) {
                            return redirect()->back()
                                ->with('error', "PHP file for {$fileType} contains output or errors")
                                ->withInput();
                        }
                    }

                    if (! is_array($tempTranslations)) {
                        return redirect()->back()
                            ->with('error', "Invalid translation file for {$fileType}. File must return an array, got " . gettype($tempTranslations))
                            ->withInput();
                    }

                } catch (Throwable $e) {
                    return redirect()->back()
                        ->with('error', "Error validating PHP file for {$fileType}: " . $e->getMessage())
                        ->withInput();
                }

                $langDirectory = resource_path("lang/{$language->code}");

                if (! File::exists($langDirectory)) {
                    File::makeDirectory($langDirectory, 0755, true);
                }

                $fileName = $fileType . '.php';
                $filePath = "{$langDirectory}/{$fileName}";

                try {
                    $moved = $uploadedFile->move($langDirectory, $fileName);
                    if (! File::exists($filePath)) {
                        throw new \Exception("File was not created at expected path: {$filePath}");
                    }

                } catch (Throwable $e) {
                    return redirect()->back()
                        ->with('error', "Failed to save file for {$fileType}: " . $e->getMessage())
                        ->withInput();
                }

                $relativePath = "lang/{$language->code}/{$fileName}";

                $isWebFile = ($fileType === 'frontend-labels');

                if ($isWebFile) {
                    if (! in_array($relativePath, $webFiles)) {
                        $webFiles[] = $relativePath;
                    }
                } else {
                    if (! in_array($relativePath, $adminPanelFiles)) {
                        $adminPanelFiles[] = $relativePath;
                    }
                }

                $uploadedCount++;
            }

            if ($uploadedCount === 0) {
                $message = 'No files were uploaded. ';
                if (! empty($skippedFiles)) {
                    $message .= 'Details: ' . implode(', ', $skippedFiles);
                }
                return redirect()->back()
                    ->with('error', $message)
                    ->withInput();
            }

            $language->admin_panel_files = array_values(array_unique($adminPanelFiles));
            $language->web_files         = array_values(array_unique($webFiles));
            $language->save();

            Log::info("Language model updated successfully");

            CachingService::removeCache(config('constants.CACHE.LANGUAGE'));

            return redirect()->back()->with('success', "{$uploadedCount} file(s) uploaded and saved successfully.");

        } catch (Throwable $th) {

            ResponseService::logErrorRedirect($th, "Language Controller -> Upload File");
            return redirect()->back()->with('error', self::SOMTHING_WENT_WRONG . ': ' . $th->getMessage());
        }
    }

    /**
     * Download sample translation files from English language PHP files
     */
    public function downloadSample($type)
    {
        try {
            // Valid file types
            $validTypes = ['message', 'page', 'global', 'frontend-labels'];

            if (! in_array($type, $validTypes)) {
                return redirect()->back()->with('error', 'Invalid sample file type.');
            }

            // Get English language file path
            $filePath = resource_path("lang/en/{$type}.php");

            // Check if file exists
            if (! File::exists($filePath)) {
                return redirect()->back()->with('error', "English {$type}.php file not found. Please ensure the file exists at: resources/lang/en/{$type}.php");
            }

            // Load translations from PHP file to validate
            $translations = include $filePath;

            if (! is_array($translations)) {
                return redirect()->back()->with('error', "Invalid format in {$type}.php file.");
            }

            // Return the PHP file directly
            $fileName = $type . '.php';

            return Response::file($filePath, [
                'Content-Type'        => 'text/plain',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        } catch (Throwable $th) {
            ResponseService::logErrorRedirect($th, "Language Controller -> Download Sample");
            return redirect()->back()->with('error', 'Error loading sample file: ' . $th->getMessage());
        }
    }

    /**
     * Download existing translation file
     */
    public function downloadFile($id, Request $request)
    {
        try {
            $language = Language::findOrFail($id);
            $fileName = $request->query('file');
            $fileType = $request->query('type');

            $filePath = resource_path("lang/{$language->code}/{$fileName}");

            if (! File::exists($filePath)) {
                return redirect()->back()->with('error', 'File not found.');
            }

            // Return the PHP file directly
            return Response::file($filePath, [
                'Content-Type'        => 'text/plain',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        } catch (Throwable $th) {
            ResponseService::logErrorRedirect($th, "Language Controller -> Download File");
            return redirect()->back()->with('error', self::SOMTHING_WENT_WRONG);
        }
    }

    /**
     * Delete translation file
     */
    public function deleteFile($id, Request $request)
    {
        try {
            $language = Language::findOrFail($id);
            $fileName = $request->query('file');
            $fileType = $request->query('type');

            if (! $fileName || ! $fileType) {
                return redirect()->back()->with('error', 'Invalid file or type.');
            }

            // Prevent deleting English files
            if ($language->code === 'en') {
                return redirect()->back()->with('error', 'Cannot delete English language files.');
            }

            $filePath     = resource_path("lang/{$language->code}/{$fileName}");
            $relativePath = "lang/{$language->code}/{$fileName}";

            // Delete the physical file if it exists
            if (File::exists($filePath)) {
                File::delete($filePath);
            }

            // Handle admin_panel files
            if ($fileType === 'admin_panel') {
                $adminFiles                  = $language->admin_panel_files ?? [];
                $adminFiles                  = array_values(array_filter($adminFiles, fn($file) => $file !== $relativePath));
                $language->admin_panel_files = $adminFiles;
            }

            // Handle web files
            if ($fileType === 'web') {
                $webFiles            = $language->web_files ?? [];
                $webFiles            = array_values(array_filter($webFiles, fn($file) => $file !== $relativePath));
                $language->web_files = $webFiles;
            }

            $language->save(); // Save the updated JSON array

            CachingService::removeCache(config('constants.CACHE.LANGUAGE'));

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'File deleted successfully.']);
            }
        } catch (Throwable $th) {
            ResponseService::logErrorRedirect($th, "Language Controller -> Delete File");
            return redirect()->back()->with('error', self::SOMTHING_WENT_WRONG);
        }
    }

    public function setLanguage($languageCode)
    {
        $language = Language::where('code', $languageCode)->first();
        if (! empty($language)) {
            Session::put('admin_locale', $language->code);
            Session::put('admin_language', (object) $language->toArray());
            Session::save();
            app()->setLocale($language->code);
        }

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Language changed successfully',
                'locale'  => $languageCode
            ]);
        }

        return redirect()->back();
    }
}
