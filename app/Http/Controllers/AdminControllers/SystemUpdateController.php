<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Update;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use ZipArchive;

class SystemUpdateController extends Controller
{
    private string $destinationPath;

    public function __construct()
    {
        $this->destinationPath = base_path() . '/update/tmp/';
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        ResponseService::noAnyPermissionThenRedirect(['system-update-settings']);

        $system_version = Update::latest()->first();
        return view('admin.system-update.index', compact('system_version'));
    }

    public function is_dir_empty($dir)
    {
        if (! is_readable($dir)) {
            return null;
        }

        return (count(scandir($dir)) == 2);
    }

    /**
     * Handles the update process for the system or application.
     *
     * Steps:
     * 1. Increases the script's execution time to handle large updates.
     * 2. Validates the uploaded file and ensures it is a zip file.
     * 3. Creates the necessary directory structure to store the update files.
     * 4. Moves the uploaded file to the target directory.
     * 5. Extracts the contents of the zip file to the update directory.
     * 6. Validates the update package structure by checking for required files (e.g., `package.json`).
     * 7. Reads the `package.json` file for:
     *    - Folder creation: Creates directories specified in the JSON file.
     *    - File copying: Copies files from the package to their respective locations.
     *    - Archive extraction: Extracts nested archives to specified locations.
     * 8. Runs database migrations and executes SQL queries if provided.
     * 9. Cleans up temporary files and directories after the update process.
     * 10. Clears application caches to apply changes.
     * 11. Returns appropriate responses for success or errors.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function update(Request $request)
    {
        ini_set('max_execution_time', 900);
        $zip            = new ZipArchive();
        $updatePath     = Config::get('constants.UPDATE_PATH') ?? "update/";
        $fullUpdatePath = public_path($updatePath);

        if (! empty($_FILES['update_file']['name'][0])) {
            if (! File::exists(public_path($updatePath))) {
                File::makeDirectory(public_path($updatePath), 0777, true);
            }

            $uploadData = $request->file('update_file');
            $ext        = trim(strtolower($uploadData->getClientOriginalExtension()));

            // Check if the extension is zip
            if ($ext != "zip") {
                $response = [
                    "error"   => true,
                    "message" => "Please insert a valid Zip File.",
                ];
                return response()->json($response);
            }

            if ($uploadData->move(public_path($updatePath))) {

                $filename = $uploadData->getFilename();
                ## Extract the zip file ---- start
                $zip = new ZipArchive();
                $res = $zip->open(public_path($updatePath) . $filename);

                if ($res === true) {
                    $extractPath = public_path($updatePath);
                    // Extract file
                    $zip->extractTo($extractPath);
                    $zip->close();
                    if (file_exists($updatePath . "package.json") || file_exists($updatePath . "plugin/package.json")) {

                        $system_info = get_system_update_info();
                        if (isset($system_info['updated_error']) || isset($system_info['sequence_error'])) {
                            $response = [
                                'error'   => true,
                                'message' => $system_info['message'],
                            ];
                            File::deleteDirectory($updatePath);
                            return response()->json($response);
                        }

                        /* Plugin / Module installer script */
                        $sub_directory = (file_exists($updatePath . "plugin/package.json")) ? "plugin/" : "";

                        if (file_exists($updatePath . $sub_directory . "package.json")) {
                            $package_data = file_get_contents($updatePath . $sub_directory . "package.json");
                            $package_data = json_decode($package_data, true);
                            if (! empty($package_data)) {
                                /* Folders Creation - check if folders.json is set if yes then create folders listed in that file */
                                if (isset($package_data['folders']) && ! empty($package_data['folders'])) {
                                    $jsonFilePath = $updatePath . $sub_directory . $package_data['folders'];

                                    if (file_exists($jsonFilePath)) {
                                        $lines_array = file_get_contents($jsonFilePath);

                                        if ($lines_array !== false && ! empty($lines_array)) {
                                            $lines_array = json_decode($lines_array, true);

                                            if ($lines_array !== null) {
                                                foreach ($lines_array as $key => $line) {
                                                    $sourcePath  = public_path($key);
                                                    $destination = base_path($line);

                                                    // Ensure directory existence
                                                    if (! is_dir($destination) && ! file_exists($destination)) {
                                                        mkdir($destination, 0777, true);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                /* Files Copy - check if files.json is set if yes then copy the files listed in that file */
                                if (isset($package_data['files']) && ! empty($package_data['files'])) {
                                    /* copy files from source to destination as set in the file */
                                    if (file_exists($updatePath . $sub_directory . $package_data['files'])) {
                                        $lines_array = file_get_contents($updatePath . $sub_directory . $package_data['files']);
                                        if (! empty($lines_array)) {
                                            $lines_array = json_decode($lines_array);
                                            foreach ($lines_array as $key => $line) {

                                                $sourcePath = public_path($updatePath) . $sub_directory . $key;
                                                $sourcePath = str_replace('/', DIRECTORY_SEPARATOR, $sourcePath);

                                                $destination          = base_path($line);
                                                $destination          = str_replace('/', DIRECTORY_SEPARATOR, $destination);
                                                $destinationDirectory = dirname($destination);

                                                if (! is_dir($destinationDirectory)) {
                                                    mkdir($destinationDirectory, 0755, true);
                                                }

                                                if (file_exists($sourcePath)) {
                                                    copy($sourcePath, $destination);
                                                }
                                            }
                                        }
                                    }
                                }
                                /* ZIP Extraction - check if archives.json is set if yes then extract the files on destination as mentioned */
                                if (isset($package_data['archives']) && ! empty($package_data['archives'])) {
                                    /* extract the archives in the destination folder as set in the file */
                                    if (file_exists($updatePath . $sub_directory . $package_data['archives'])) {
                                        $lines_array = file_get_contents($updatePath . $sub_directory . $package_data['archives']);
                                        if (! empty($lines_array)) {
                                            $lines_array = json_decode($lines_array);
                                            $zip         = new ZipArchive;
                                            foreach ($lines_array as $source => $destination) {
                                                // $source = $updatePath . $sub_directory . $source; // Full path to source file
                                                $destination = base_path($destination);
                                                $destination = str_replace('/', DIRECTORY_SEPARATOR, $destination); // Replace forward slashes with the correct directory separator
                                                $res         = $zip->open(public_path($updatePath) . $sub_directory . $source);
                                                if ($res === true) {
                                                    $zip->extractTo($destination);
                                                    $zip->close();
                                                }
                                            }
                                        }
                                    }
                                }

                                /* run the migration if there is any */
                                $pathToMigrationDir = public_path($updatePath) . $sub_directory . 'update-files/database/migrations';
                                $pathToMigrationDir = str_replace('/', DIRECTORY_SEPARATOR, $pathToMigrationDir);
                                $pathToMigrations   = 'public/' . $updatePath . $sub_directory . 'update-files/database/migrations';
                                $pathToMigrations   = str_replace('/', DIRECTORY_SEPARATOR, $pathToMigrations);

                                if (is_dir($pathToMigrationDir)) {
                                    try {
                                        Artisan::call('migrate');
                                    } catch (\Throwable $e) {
                                        // Handle any exceptions or errors
                                    }
                                }
                                if (isset($package_data['manual_queries']) && $package_data['manual_queries'] && isset($package_data['query_path']) && $package_data['query_path'] != "") {
                                    $sqlContent = File::get($fullUpdatePath . $package_data['query_path']);
                                    $queries    = explode(';', $sqlContent);
                                    foreach ($queries as $query) {
                                        $query = trim($query);
                                        if (! empty($query)) {
                                            try {
                                                DB::statement($query);
                                            } catch (\Throwable $e) {
                                                // Handle any exceptions or errors
                                            }
                                        }
                                    }
                                }

                                $data = ['version' => $system_info['file_current_version']];
                                Update::create($data);

                                File::deleteDirectory(public_path($updatePath));

                                // Clear application caches
                                Artisan::call('cache:clear');
                                Artisan::call('config:clear');
                                Artisan::call('route:clear');
                                Artisan::call('view:clear');
                                $response = [
                                    'error'   => false,
                                    'message' => 'Congratulations! Version ' . $package_data['version'] . ' is successfully installed.',
                                ];

                                return response()->json($response);
                            } else {
                                $response = [
                                    'error'   => true,
                                    'message' => 'Invalid plugin installer file!. No package data found / missing package data.',
                                ];
                                File::deleteDirectory(public_path($updatePath));
                                return response()->json($response);
                            }
                        }
                    } else {
                        $response = [
                            'error'   => true,
                            'message' => 'Invalid update file! It seems like you are trying to update the system using the wrong file.',
                        ];

                        File::deleteDirectory(public_path($updatePath));
                    }
                } else {
                    $response['error']   = true;
                    $response['message'] = "Extraction failed.";
                }
            } else {
                $response['error']   = true;
                $response['message'] = $uploadData->getErrorString();
            }
        } else {
            $response['error']   = true;
            $response['message'] = 'You did not select a file to upload.';
        }
        return response()->json($response);
    }

}
