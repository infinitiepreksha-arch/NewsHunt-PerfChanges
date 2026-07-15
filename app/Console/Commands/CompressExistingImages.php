<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;

class CompressExistingImages extends Command
{
    protected $signature = 'images:compress {--max-width=800} {--quality=60}';
    protected $description = 'Compress all existing local images and logos to WebP format in-place and update database references';

    public function handle()
    {
        $maxWidth = (int) $this->option('max-width');
        $quality = (int) $this->option('quality');

        $folders = ['posts_image', 'posts_extra_images', 'thumb_image', 'images', 'settings', 'news_languages'];

        foreach ($folders as $folder) {
            $this->info("Scanning folder: storage/app/public/{$folder}...");
            
            if (!Storage::disk('public')->exists($folder)) {
                $this->warn("Folder {$folder} does not exist. Skipping.");
                continue;
            }

            $files = Storage::disk('public')->files($folder);
            
            $count = 0;
            $savedBytes = 0;

            foreach ($files as $file) {
                $path = Storage::disk('public')->path($file);
                $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

                if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
                    try {
                        $originalSize = filesize($path);
                        if ($originalSize === 0) continue;

                        $img = Image::make($path);
                        $resized = false;

                        if ($img->width() > $maxWidth) {
                            $img->resize($maxWidth, null, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upSize();
                            });
                            $resized = true;
                        }

                        $pathInfo = pathinfo($path);
                        $webpFileName = $pathInfo['filename'] . '.webp';
                        $webpPath = $pathInfo['dirname'] . '/' . $webpFileName;

                        // Save as webp format
                        $img->encode('webp', $quality)->save($webpPath);

                        // Delete original if it was not already a webp
                        if ($extension !== 'webp') {
                            unlink($path);
                        }

                        clearstatcache(true, $webpPath);
                        $newSize = filesize($webpPath);
                        $saved = $originalSize - $newSize;

                        // Update database references
                        $this->updateDatabaseReferences($folder, $pathInfo['basename'], $webpFileName);

                        $savedBytes += $saved;
                        $count++;
                        $this->line("Converted: {$file} (" . round($originalSize/1024, 1) . "KB -> " . round($newSize/1024, 1) . "KB as WebP)" . ($resized ? " [resized]" : ""));
                    } catch (\Throwable $e) {
                        $this->error("Failed to convert {$file}: " . $e->getMessage());
                    }
                }
            }

            $this->info("Completed folder {$folder}: Converted {$count} files, saved " . round($savedBytes/1024/1024, 2) . " MB.");
        }
    }

    protected function updateDatabaseReferences($folder, $oldFileName, $newFileName)
    {
        $oldPath = $folder . '/' . $oldFileName;

        if ($folder === 'posts_image') {
            DB::table('posts')
                ->where('image', 'LIKE', '%' . $oldPath)
                ->orWhere('image', 'LIKE', '%' . $oldFileName)
                ->get()
                ->each(function ($post) use ($oldFileName, $newFileName) {
                    $newVal = str_replace($oldFileName, $newFileName, $post->image);
                    DB::table('posts')->where('id', $post->id)->update(['image' => $newVal]);
                });
        } elseif ($folder === 'thumb_image') {
            DB::table('posts')
                ->where('video_thumb', 'LIKE', '%' . $oldPath)
                ->orWhere('video_thumb', 'LIKE', '%' . $oldFileName)
                ->get()
                ->each(function ($post) use ($oldFileName, $newFileName) {
                    $newVal = str_replace($oldFileName, $newFileName, $post->video_thumb);
                    DB::table('posts')->where('id', $post->id)->update(['video_thumb' => $newVal]);
                });
        } elseif ($folder === 'posts_extra_images') {
            DB::table('post_images')
                ->where('image', 'LIKE', '%' . $oldPath)
                ->orWhere('image', 'LIKE', '%' . $oldFileName)
                ->get()
                ->each(function ($row) use ($oldFileName, $newFileName) {
                    $newVal = str_replace($oldFileName, $newFileName, $row->image);
                    DB::table('post_images')->where('id', $row->id)->update(['image' => $newVal]);
                });
        } elseif ($folder === 'images') {
            DB::table('topics')
                ->where('logo', $oldFileName)
                ->update(['logo' => $newFileName]);

            DB::table('channels')
                ->where('logo', $oldFileName)
                ->update(['logo' => $newFileName]);
        } elseif ($folder === 'settings') {
            DB::table('settings')
                ->where('type', 'file')
                ->where('value', 'LIKE', '%' . $oldFileName)
                ->get()
                ->each(function ($row) use ($oldFileName, $newFileName) {
                    $newVal = str_replace($oldFileName, $newFileName, $row->value);
                    DB::table('settings')->where('id', $row->id)->update(['value' => $newVal]);
                });
        } elseif ($folder === 'news_languages') {
            DB::table('news_languages')
                ->where('image', 'LIKE', '%' . $oldFileName)
                ->get()
                ->each(function ($row) use ($oldFileName, $newFileName) {
                    $newVal = str_replace($oldFileName, $newFileName, $row->image);
                    DB::table('news_languages')->where('id', $row->id)->update(['image' => $newVal]);
                });
        }
    }
}
