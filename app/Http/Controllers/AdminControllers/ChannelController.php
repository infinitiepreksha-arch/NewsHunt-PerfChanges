<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\NewsLanguage;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class ChannelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        ResponseService::noAnyPermissionThenRedirect(['list-channel', 'create-channel', 'update-channel', 'delete-channel', 'update-status-channel', 'select-newslanguage-for-channel']);
        $title          = __('message.CHANNELS');
        $news_languages = NewsLanguage::where('status', 'active')->get();

        $data = [
            'title'          => $title,
            'news_languages' => $news_languages,
        ];
        return view('admin.channel.index', $data);
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
    public function store(Request $request, Channel $channel)
    {
        ResponseService::noPermissionThenRedirect('create-channel');
        $request->validate([
            'name'             => 'required|unique:channels,name',
            'description'      => 'required',
            'logo'             => 'required|max:2000|mimes:jpg,jpeg,png,webp,svg,avif',
            'status'           => 'required',
            'news_language_id' => 'required|exists:news_languages,id',
        ], [
            'name.unique' => 'This channel name is already taken.',
        ]);

        /* Store the channel logo. */
        $file = $request->file('logo');
        if ($file) {
            $fileName = rand('0000', '9999') . $file->getClientOriginalName();
            $path     = \App\Services\FileService::resizeAndCompressUpload($file, 'images', 400, $fileName, 'webp');
            $fileName = basename($path);
        }
        $slug = Str::slug($request->name);
        if (empty($slug)) {
            $slug = 'post-' . uniqid();
        }
        $originalSlug = $slug;
        $counter      = 1;

        while (Channel::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $channel->name             = $request->name;
        $channel->description      = $request->description;
        $channel->logo             = $fileName;
        $channel->slug             = $slug;
        $channel->status           = $request->status;
        $channel->news_language_id = $request->news_language_id;
        if ($request->filled('news_language_id')) {
            $channel->news_language_id = $request->news_language_id;
        } else {
            $activeLang                = NewsLanguage::where('is_active', 1)->first();
            $channel->news_language_id = $activeLang ? $activeLang->id : null;
        }
        $save = $channel->save();

        if ($save) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Channel created successfully.',
            ]);
        } else {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong.',
            ]);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $status = $request->input('channel_status') ?? '';

        try {
            ResponseService::noPermissionThenSendJson('list-channel');
            $query = Channel::select('id', 'name', 'logo as poster_image', 'status', 'description', 'slug', 'follow_count', 'news_language_id');
            if ($status !== '' && $status !== '*') {
                $query->where('status', $status);
            }
            $getChannel = $query->get();

            $getChannel->each(function ($channel) {
                $channel->poster_image = asset('storage/images/' . $channel->poster_image);
            });

            return DataTables::of($getChannel)
                ->addColumn('action', function ($getData) {
                    $actions = '';

                    $actions = "<div class='d-flex flex-wrap gap-1'>"; // Start wrapper

                    // Check update permission
                    if (auth()->user()->can('update-channel')) {
                        $actions .= "<a href='" . route('channels.edit', $getData->id) . "' class='btn text-primary btn-sm edit_btn' data-bs-toggle='modal' data-bs-target='#editChannelModal' title='Edit Channel'>
                        <i class='fa fa-pen'></i>
                    </a>";
                    } else {
                        $actions .= "<span class='badge bg-primary text-white'>No permission for Edit Channels.</span>";
                    }

                    // Add a small space if both actions exist
                    $actions .= " &nbsp; ";

                    // Check delete permission
                    if (auth()->user()->can('delete-channel')) {
                        $actions .= "<a href='" . route('channels.destroy', $getData->id) . "' class='btn text-danger btn-sm delete-form delete-form-reload' data-bs-toggle='tooltip' title='Delete'>
                        <i class='fa fa-trash'></i>
                    </a>";
                    } else {
                        $actions .= "<span class='badge bg-danger text-white'>No permission for Delete Channels.</span>";
                    }

                    $actions .= "</div>"; // End wrapper

                    return $actions;
                })
                ->make(true);

        } catch (\Exception $e) {
            ResponseService::errorResponse('Something Went Wrong');
        }
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
    public function update(Request $request, Channel $channel)
    {
        ResponseService::noPermissionThenRedirect('update-channel');
        $request->validate([
            'name'             => [
                'required',
                Rule::unique('channels', 'name')->ignore($channel->id),
            ],
            'description'      => 'required',
            'status'           => 'required',
            'news_language_id' => 'required|exists:news_languages,id', // Add this line

        ], [
            'name.unique' => 'This channel name is already taken.',
        ]);

        $file    = $request->file('logo');
        $id      = $request->id;
        $channel = Channel::findOrFail($request->id);

        if ($file) {
            $oldImagePath = public_path('images/' . $channel->logo);
            if (file_exists($oldImagePath) && $channel->logo) {
                unlink($oldImagePath);
            }
            if ($channel->logo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete('images/' . $channel->logo);
            }

            $fileName = rand('0000', '9999') . $file->getClientOriginalName();
            $path     = \App\Services\FileService::resizeAndCompressUpload($file, 'images', 400, $fileName, 'webp');
            $fileName = basename($path);
        } else {
            $fileName = $channel->logo;
        }
        $slug = Str::slug($request->name);
        if (empty($slug)) {
            $slug = 'post-' . uniqid();
        }
        $originalSlug = $slug;
        $counter      = 1;

        while (Channel::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        $channel              = Channel::find($id);
        $channel->name        = $request->name;
        $channel->description = $request->description;
        $channel->logo        = $fileName;
        $channel->slug        = $slug;
        $channel->status      = $request->status;
        if ($request->filled('news_language_id')) {
            $channel->news_language_id = $request->news_language_id;
        } else {
            $activeLang                = NewsLanguage::where('is_active', 1)->first();
            $channel->news_language_id = $activeLang ? $activeLang->id : null;
        }
        $save = $channel->update();

        if ($save) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Channel updated successfully.',
            ]);
        } else {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong.',
            ]);
        }

    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(string $id)
    {
        try {
            ResponseService::noPermissionThenSendJson('delete-channel');
            $channel = Channel::findOrFail($id);

            $usedIn = collect([
                'posts'       => 'Posts',
                'rssFeeds'    => 'RSS Feeds',
                'enewspapers' => 'eNewspapers',
            ])->filter(function ($label, $relation) use ($channel) {
                return method_exists($channel, $relation)
                && $channel->$relation()->exists();
            })->values()->all();

            if (! empty($usedIn)) {
                $usedInList = implode(', ', $usedIn);

                return ResponseService::errorResponse(
                    "You cannot delete this Channel because it is already used in {$usedInList}.",
                    400
                );
            }

            $channel->delete();

            ResponseService::successResponse("Channel deleted Successfully");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "ChannelControler -> destroyChannel");
            ResponseService::errorResponse('Something Went Wrong');
        }
    }

    public function updateStatus(Request $request)
    {
        ResponseService::noPermissionThenSendJson('update-status-channel');

        $channel = Channel::find($request->id);

        if ($request->status === 'active') {
            $channel->status = 'active';
        } else {
            $channel->status = 'inactive';
        }
        $channel->save();
        if ($request->status == 'active') {
            return response()->json(['message' => 'Channel Activated']);
        } else {
            return response()->json(['message' => 'Channel Inactivated']);
        }
    }

}
