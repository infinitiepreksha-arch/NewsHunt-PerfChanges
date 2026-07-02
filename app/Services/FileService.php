<?php
namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class FileService
{
    /**
     * @param $requestFile
     * @param $folder
     * @return string
     */
    const RESOURCE_LANG = 'resources/lang/';
    public static function compressAndUpload($requestFile, $folder)
    {
        $file_name = uniqid('', true) . time() . '.' . $requestFile->getClientOriginalExtension();
        if (in_array($requestFile->getClientOriginalExtension(), ['jpg', 'jpeg', 'png'])) {
            // Check the Extension should be jpg or png and do compression
            $image = Image::make($requestFile)->encode(null, 60);
            Storage::disk('public')->put($folder . '/' . $file_name, $image);
        } else {
            // Else assign file as it is
            $file = $requestFile;
            $file->storeAs($folder, $file_name, 'public');
        }
        return $folder . '/' . $file_name;
    }

    /**
     * @param $requestFile
     * @param $folder
     * @return string
     */
    public static function upload($requestFile, $folder)
    {
        $file_name = uniqid('', true) . time() . '.' . $requestFile->getClientOriginalExtension();
        $requestFile->storeAs($folder, $file_name, 'public');
        return $folder . '/' . $file_name;
    }

    /**
     * @param $requestFile
     * @param $folder
     * @param $deleteRawOriginalImage
     * @return string
     */
    public static function replace($requestFile, $folder, $deleteRawOriginalImage)
    {
        self::delete($deleteRawOriginalImage);
        return self::upload($requestFile, $folder);
    }

    /**
     * @param $requestFile
     * @param $folder
     * @param $deleteRawOriginalImage
     * @return string
     */
    public static function compressAndReplace($requestFile, $folder, $deleteRawOriginalImage)
    {
        if (! empty($deleteRawOriginalImage)) {
            self::delete($deleteRawOriginalImage);
        }
        return self::compressAndUpload($requestFile, $folder);
    }

    /**
     * @param $requestFile
     * @param $code
     * @return string
     */
    public static function uploadLanguageFile($requestFile, $code)
    {
        $filename = $code . '.' . $requestFile->getClientOriginalExtension();
        if (file_exists(base_path(self::RESOURCE_LANG) . $filename)) {
            File::delete(base_path(self::RESOURCE_LANG) . $filename);
        }
        $requestFile->move(base_path(self::RESOURCE_LANG), $filename);
        return $filename;
    }

    /**
     * @param $file
     * @return string
     */
    public static function deleteLanguageFile($file)
    {
        if (file_exists(base_path(self::RESOURCE_LANG) . $file)) {
            File::delete(base_path(self::RESOURCE_LANG) . $file);
        }
        return true;
    }

    /**
     * @param $image = rawOriginalPath
     * @return bool
     */
    public static function delete($image)
    {
        if (! empty($image) && Storage::disk('public')->exists($image)) {
            return Storage::disk('public')->delete($image);
        }

        //Image does not exist in server so feel free to upload new image
        return true;
    }

    public static function saveTranslations(array $translations, string $languageCode, string $fileName): bool
    {
        $filePath  = resource_path("lang/{$languageCode}/{$fileName}.php");
        $directory = dirname($filePath);
        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
        $existingTranslations = File::exists($filePath) ? include $filePath : [];
        $translations         = array_merge($existingTranslations, $translations);
        $content              = "<?php\n\nreturn " . var_export($translations, true) . ";\n";
        return File::put($filePath, $content) !== false;
    }
}
