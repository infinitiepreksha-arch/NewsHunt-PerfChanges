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
    public static function compressAndUpload($requestFile, $folder, $format = 'webp')
    {
        $extension = strtolower($requestFile->getClientOriginalExtension());
        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp']);
        
        $outExtension = ($isImage && $format === 'webp') ? 'webp' : $extension;
        $file_name = uniqid('', true) . time() . '.' . $outExtension;

        if ($isImage) {
            // Check the Extension should be jpg, jpeg, png, or webp and do compression & conversion
            $image = Image::make($requestFile)->encode($format, 60);
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

    public static function resizeAndCompressUpload($requestFile, $folder, $maxWidth = 800, $fileName = null, $format = 'webp')
    {
        $extension = strtolower($requestFile->getClientOriginalExtension());
        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp']);

        if (empty($fileName)) {
            $outExtension = ($isImage && $format === 'webp') ? 'webp' : $extension;
            $fileName = uniqid('', true) . time() . '.' . $outExtension;
        } else {
            // If custom fileName has original extension, replace it with .webp if needed
            if ($isImage && $format === 'webp') {
                $pathInfo = pathinfo($fileName);
                $fileName = $pathInfo['filename'] . '.webp';
            }
        }

        if ($isImage) {
            $img = \Intervention\Image\Facades\Image::make($requestFile->path());

            // Resize if wider than maxWidth
            if ($img->width() > $maxWidth) {
                $img->resize($maxWidth, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upSize();
                });
            }

            // Quality compression and WebP conversion (60%)
            $compressedData = $img->encode($format, 60);
            \Illuminate\Support\Facades\Storage::disk('public')->put($folder . '/' . $fileName, $compressedData);
        } else {
            $requestFile->storeAs($folder, $fileName, 'public');
        }

        return $folder . '/' . $fileName;
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
