<?php
use App\Models\Setting;
use App\Models\Theme;
use App\Models\Update;
use Aws\S3\S3Client;
use Facebook\WebDriver\Exception\Internal\RuntimeException as InternalRuntimeException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

if (! function_exists('getTheme')) {
    function getTheme()
    {
        try {
            $themeData = Theme::select('slug')->where('is_default', '1')->first();
            return optional($themeData)->slug ?? 'classic';
        } catch (Throwable $e) {
            return "";
        }
    }
}

if (! function_exists('get_system_update_info')) {
    function get_system_update_info()
    {
        $updatePath   = Config::get('constants.UPDATE_PATH');
        $updaterPath  = $updatePath . 'updater.json';
        $subDirectory = (File::exists($updaterPath) && File::exists($updatePath . 'update/updater.json')) ? 'update/' : '';

        if (File::exists($updaterPath) || File::exists($updatePath . $subDirectory . 'updater.json')) {
            $updaterFilePath = File::exists($updaterPath) ? $updaterPath : $updatePath . $subDirectory . 'updater.json';
            $updaterContents = File::get($updaterFilePath);

            // Check if the file contains valid JSON data
            if (! json_decode($updaterContents)) {
                throw new InternalRuntimeException('Invalid JSON content in updater.json');
            }

            $linesArray = json_decode($updaterContents, true);

            if (! isset($linesArray['version'], $linesArray['previous'], $linesArray['manual_queries'], $linesArray['query_path'])) {
                throw new InternalRuntimeException('Invalid JSON structure in updater.json');
            }
        } else {
            throw new InternalRuntimeException('updater.json does not exist');
        }

        $dbCurrentVersion           = Update::latest()->first();
        $data['db_current_version'] = $dbCurrentVersion ? $dbCurrentVersion->version : '1.0.0';
        if ($data['db_current_version'] == $linesArray['version']) {
            $data['updated_error'] = true;
            $data['message']       = 'Oops!. This version is already updated into your system. Try another one.';
            return $data;
        }
        if ($data['db_current_version'] == $linesArray['previous']) {
            $data['file_current_version'] = $linesArray['version'];
        } else {
            $data['sequence_error'] = true;
            $data['message']        = 'Oops!. Update must performed in sequence.';
            return $data;
        }

        $data['query']      = $linesArray['manual_queries'];
        $data['query_path'] = $linesArray['query_path'];

        return $data;
    }
}

if (! function_exists('getFileName')) {
    function getFileName($file)
    {
        $fileOriginalName = $file->getClientOriginalName();
        $fileName         = preg_replace('/[^A-Za-z0-9\.]/ ', '', $fileOriginalName);
        return time() . '_' . $fileName;

    }
}

if (! function_exists('uploadFileS3Bucket')) {
    function uploadFileS3Bucket($video_file, $filename, $path, $old_file_name = "")
    {
        if ($old_file_name != "") {
            deleteFileS3Bucket($old_file_name);
        }
        $s3_bucket_name        = Setting::where('name', 's3_bucket_name')->first();
        $aws_access_key_id     = Setting::where('name', 'aws_access_key_id')->first();
        $aws_secret_access_key = Setting::where('name', 'aws_secret_access_key')->first();
        $s3Client              = new S3Client([
            'region'      => env('S3_REGION'),
            'version'     => 'latest',
            'credentials' => [
                'key'    => $aws_access_key_id->value,
                'secret' => $aws_secret_access_key->value,
            ],
        ]);

        $result = $s3Client->putObject([
            'Bucket'     => $s3_bucket_name->value,
            'Key'        => $path . $filename,
            'SourceFile' => $video_file,
            'ACL'        => 'public-read',
        ]);

        $image_url = '';
        if (isset($result['ObjectURL'])) {
            $image_url = $result['ObjectURL'];
        }
        return $image_url;
    }
}

if (! function_exists('create_label')) {
    function create_label($key, $label, $language_code)
    {
        // Extract parts
        $keyParts = explode('[', str_replace(']', '', $key));
        // Example result: ['translations', 'frontend-labels', 'aboutus', 'title']

        // Remove 'translations' from start
        array_shift($keyParts);

        // Join remaining parts into dot notation
        $translationKey = implode('.', $keyParts);

        // Input ID (replace dots with underscores)
        $inputId = str_replace('.', '_', $translationKey);

        // Get translation
        $currentValue = __($translationKey, [], $language_code);

        // Handle array cases
        if (is_array($currentValue)) {
            $currentValue = '';
        }

        // If translation not found, clear it
        $currentValue = $currentValue === $translationKey ? '' : htmlspecialchars($currentValue);

        return '
            <div class="col-md-6 mb-3 mt-1">
                <label for="' . htmlspecialchars($inputId) . '" class="form-label">' . htmlspecialchars($label) . '</label>
                <input type="text" name="' . htmlspecialchars($key) . '" id="' . htmlspecialchars($inputId) . '"
                       class="form-control" value="' . $currentValue . '">
            </div>';
    }
}

if (! function_exists('deleteFileS3Bucket')) {
    function deleteFileS3Bucket($fileName)
    {
        $s3_bucket_name        = Setting::where('name', 's3_bucket_name')->first();
        $aws_access_key_id     = Setting::where('name', 'aws_access_key_id')->first();
        $aws_secret_access_key = Setting::where('name', 'aws_secret_access_key')->first();
        $s3Client              = new S3Client([
            'region'      => env('S3_REGION'),
            'version'     => 'latest',
            'credentials' => [
                'key'    => $aws_access_key_id->value,
                'secret' => $aws_secret_access_key->value,
            ],
        ]);

        $s3Client->deleteObject([
            'Bucket' => $s3_bucket_name->value,
            'Key'    => $fileName,
        ]);
    }
}

if (! function_exists('applyMailSettingsFromDb')) {
    function applyMailSettingsFromDb()
    {
        $keys = [
            'mail_mailer',
            'mail_host',
            'mail_port',
            'mail_username',
            'mail_password',
            'mail_encryption',
            'mail_from_address',
            'mail_from_name',
        ];

        $settings = Setting::whereIn('name', $keys)->pluck('value', 'name');

        if (empty($settings['mail_host']) || empty($settings['mail_port']) || empty($settings['mail_username']) || empty($settings['mail_password'])) {
            return false;
        }

        Config::set('mail.default', $settings['mail_mailer'] ?? 'smtp');

        Config::set('mail.mailers.smtp', [
            'transport'  => 'smtp',
            'host'       => $settings['mail_host'] ?? 'smtp.gmail.com',
            // 'port'       => (int) $settings['mail_port'] ?? 587,
            'port'       => (int) ($settings['mail_port'] ?? 587),
            'encryption' => $settings['mail_encryption'] ?? 'tls',
            'username'   => $settings['mail_username'] ?? '',
            'password'   => $settings['mail_password'] ?? '',
            'timeout'    => null,
            'auth_mode'  => null,
        ]);

        Config::set('mail.from', [
            'address' => $settings['mail_from_address'] ?? 'default@example.com',
            'name'    => $settings['mail_from_name'] ?? 'News Hunt',
        ]);

        // Force Laravel to reinitialize mail manager with new config
        app()->forgetInstance('mail.manager');
        app()->forgetInstance(\Illuminate\Mail\Mailer::class);
        app()->forgetInstance(\Illuminate\Contracts\Mail\Mailer::class);

        // Recreate the mail manager
        app()->singleton('mail.manager', function ($app) {
            return new \Illuminate\Mail\MailManager($app);
        });

        return true;
    }
}

if (! function_exists('getSystemHealth')) {
    function getSystemHealth()
    {
        // PHP Version Check
        $currentPhpVersion = phpversion();
        $minRequired       = '8.2.0';
        $maxRequired       = phpversion();
        $php_ok            = version_compare($currentPhpVersion, $minRequired, '>=') &&
        version_compare($currentPhpVersion, $maxRequired, '<=');

        // Database Check
        $dbStatus  = false;
        $dbError   = null;
        $dbVersion = null;
        $dbHost    = null;
        try {
            DB::connection()->getPdo();
            $dbStatus = true;
            try {
                $dbVersion = DB::select('SELECT VERSION() as version')[0]->version ?? null;
            } catch (\Exception $e) {
                $dbVersion = 'Unknown';
            }
            $dbHost = config('database.connections.' . config('database.default') . '.host');
        } catch (\Exception $e) {
            $dbStatus = false;
            $dbError  = $e->getMessage();
        }

        // Required Extensions
        $requiredExtensions = [
            'pdo_mysql',
            'mbstring',
            'fileinfo',
            'openssl',
            'tokenizer',
            'json',
            'curl',
            'zip',
        ];
        $missingExtensions   = [];
        $installedExtensions = [];

        foreach ($requiredExtensions as $ext) {
            if (! extension_loaded($ext)) {
                $missingExtensions[] = $ext;
            } else {
                $installedExtensions[] = $ext;
            }
        }

        // Check shell_exec availability
        $shellExecEnabled = function_exists('shell_exec') &&
        ! in_array('shell_exec', array_map('trim', explode(',', ini_get('disable_functions'))));

        // File Permissions
        $paths = [
            'storage'         => storage_path(),
            'bootstrap/cache' => base_path('bootstrap/cache'),
            'logs'            => storage_path('logs'),
        ];

        $filePermissions       = [];
        $unwritableDirectories = [];
        $allPermissionsOk      = true;

        foreach ($paths as $name => $path) {
            $exists   = file_exists($path);
            $writable = $exists ? is_writable($path) : false;
            $readable = $exists ? is_readable($path) : false;

            $filePermissions[$name] = [
                'path'        => $path,
                'exists'      => $exists,
                'writable'    => $writable,
                'readable'    => $readable,
                'permissions' => $exists ? substr(sprintf('%o', fileperms($path)), -4) : null,
                'status'      => $exists && $writable && $readable,
            ];

            if (! $exists || ! $writable) {
                $allPermissionsOk        = false;
                $unwritableDirectories[] = [
                    'name'     => $name,
                    'path'     => $path,
                    'exists'   => $exists,
                    'writable' => $writable,
                ];
            }
        }

        // Mail Status
        $mailConfigured = env('MAIL_HOST') && env('MAIL_USERNAME');

        // Payment Gateways
        $payment = [
            'stripe'   => (bool) env('STRIPE_KEY'),
            'razorpay' => (bool) env('RAZORPAY_KEY'),
            'iap'      => (bool) env('IAP_SHARED_SECRET'),
        ];

        // Cron job
        $cronLastRun = cache('cron_last_run');

        return [
            'php'           => [
                'status'             => $php_ok,
                'version'            => $currentPhpVersion,
                'min_required'       => $minRequired,
                'max_required'       => $maxRequired,
                'memory_limit'       => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
            ],
            'database'      => [
                'status'  => $dbStatus,
                'name'    => config('database.connections.' . config('database.default') . '.database'),
                'driver'  => config('database.default'),
                'host'    => $dbHost,
                'version' => $dbVersion,
                'error'   => $dbError,
            ],
            'extensions'    => [
                'missing'         => $missingExtensions,
                'installed'       => $installedExtensions,
                'healthy'         => empty($missingExtensions),
                'shell_exec'      => $shellExecEnabled,
                'total_required'  => count($requiredExtensions),
                'total_installed' => count($installedExtensions),
                'total_missing'   => count($missingExtensions),
            ],
            'permissions'   => [
                'paths'                  => $filePermissions,
                'status'                 => $allPermissionsOk,
                'healthy'                => $allPermissionsOk,
                'unwritable_directories' => $unwritableDirectories,
            ],
            'mail'          => [
                'configured' => $mailConfigured,
                'host'       => env('MAIL_HOST'),
                'from'       => env('MAIL_FROM_ADDRESS'),
            ],
            'cron'          => [
                'last_run' => $cronLastRun,
            ],
            'payment'       => $payment,
            'upload_limits' => [
                'post_max_size'       => ini_get('post_max_size'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
            ],
        ];
    }
}
