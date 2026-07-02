<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\NewsLanguage;
use App\Models\Story;
use App\Models\StorySlide;
use App\Models\Topic;
use App\Services\ResponseService;
use App\Traits\LanguageDataTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class StoryController extends Controller
{
    use LanguageDataTrait;
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function publicIndex()
    {
        $title = __('page.STORIES');

        ResponseService::noAnyPermissionThenRedirect(['list-story', 'create-story', 'update-story', 'delete-story', 'select-newslanguage-for-story', 'select-topic-for-story']);
        $stories = Story::with(['story_slides' => function ($query) {
            $query->orderBy('order', 'asc');
        }, 'topic'])->latest()->get();

        return view('admin.webstory.all_story', compact('stories', 'title'));
    }

    public function create_story(Request $request)
    {
        ResponseService::noPermissionThenRedirect('create-story');

        $title = __('page.CREATE_STORY');

        $topic          = Topic::select('id', 'name')->where('status', 'active')->get();
        $news_languages = NewsLanguage::where('status', 'active')->get();

        $data = [
            'title'          => $title,
            'topic'          => $topic,
            'news_languages' => $news_languages,
        ];

        return view('admin.webstory.create_story', $data);
    }

    public function store(Request $request)
    {
        ResponseService::noPermissionThenRedirect('create-story');

        $image_size_type = $request->input('image_size_type', 'fixed');
        $image_rules     = 'required|image|mimes:webp,jpeg,png,jpg,gif,svg|max:8004';
        if ($image_size_type === 'fixed') {
            $image_rules .= '|dimensions:width=1080,height=1920';
        }

        $request->validate([
            'topic_id'             => 'required|exists:topics,id',
            'title'                => 'required|string|max:255',
            'image_size_type'      => 'required|in:fixed,random',
            'slides'               => 'required|array|min:1',
            'slides.*.title'       => 'nullable|string|max:255',
            'slides.*.description' => 'required|string',
            'slides.*.image'       => $image_rules,
            'slide_order'          => 'required|json',
            'news_language_id'     => 'required|exists:news_languages,id',
        ]);

        DB::beginTransaction();
        try {
            // Create story
            $slug = Str::slug($request->title);
            if (empty($slug)) {
                $slug = 'story-' . uniqid();
            }
            $topic          = Topic::findOrFail($request->topic_id);
            $newsLanguageId = $request->news_language_id ?? $topic->news_language_id;

            $story = Story::create([
                'topic_id'         => $request->topic_id,
                'title'            => $request->title,
                'slug'             => $slug,
                'news_language_id' => $newsLanguageId,
                'image_size_type'  => $request->image_size_type,
            ]);

            // Process slides
            $slideOrder = json_decode($request->slide_order, true);
            foreach ($slideOrder as $order => $index) {
                $slide = $request->slides[$index];
                $path  = $slide['image']->store('story_slides', 'public');

                StorySlide::create([
                    'story_id'          => $story->id,
                    'title'             => $slide['title'] ?? "",
                    'description'       => $slide['description'] ?? null,
                    'image'             => $path,
                    'order'             => $order,
                    'animation_details' => json_decode($request->animation_settings, true),
                ]);
            }
            DB::commit();

            return redirect()->route('stories.publicIndex')->with('success', 'Story created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating story: ' . $e->getMessage());
            return back()->with('error', 'Failed to create story. Please try again.');
        }
    }

    public function edit(Story $story)
    {
        ResponseService::noPermissionThenRedirect('update-story');

        $title = __('page.UPDATE_STORY');

        $topic          = Topic::select('id', 'name')->where('status', 'active')->get();
        $news_languages = NewsLanguage::where('status', 'active')->get();

        // Get all topics for the story's language
        $topic = Topic::where('news_language_id', $story->news_language_id)
            ->get(['id', 'name']);
        $story->load('story_slides');

        $animations = [];
        foreach ($story->story_slides as $slide) {
            $animations[$slide->id] = is_string($slide->animation_details)
                ? json_decode($slide->animation_details, true)
                : $slide->animation_details;
        }

        return view('admin.webstory.edit', compact('story', 'title', 'topic', 'animations', 'news_languages'));
    }

    public function update(Request $request, Story $story)
    {
        ResponseService::noPermissionThenRedirect('update-story');

        $image_size_type = $request->input('image_size_type', 'fixed');
        $imageRules      = [];
        if ($request->has('slides')) {
            foreach ($request->slides as $index => $slideData) {
                $slideId          = $slideData['id'] ?? null;
                $hasExistingImage = false;

                if ($slideId) {
                    $existingSlide    = StorySlide::find($slideId);
                    $hasExistingImage = $existingSlide && ! empty($existingSlide->image);
                }

                $rules = ($hasExistingImage ? 'nullable' : 'required') . '|image|mimes:webp,jpeg,png,jpg,gif,svg|max:8004';
                if ($image_size_type === 'fixed' && isset($slideData['image']) && $slideData['image'] instanceof \Illuminate\Http\UploadedFile) {
                    $rules .= '|dimensions:width=1080,height=1920';
                }
                $imageRules["slides.{$index}.image"] = $rules;
            }
        }

        // Validation — use Validator::make() so errors are returned as JSON for AJAX
        $validator = Validator::make($request->all(), array_merge([
            'topic_id'             => 'required|exists:topics,id',
            'title'                => 'required|string|max:255',
            'image_size_type'      => 'required|in:fixed,random',
            'slides'               => 'required|array|min:1',
            'slides.*.title'       => 'nullable|string|max:255',
            'slides.*.description' => 'required|string',
            'slide_order'          => 'required|json',
            'delete_slides'        => 'nullable|array',
            'news_language_id'     => 'required|exists:news_languages,id',
        ], $imageRules));

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Update story details
            $slug = Str::slug($request->title);
            if (empty($slug)) {
                $slug = 'story-' . uniqid();
            }

            $topic          = Topic::findOrFail($request->topic_id);
            $newsLanguageId = $request->news_language_id ?? $topic->news_language_id;

            $story->update([
                'topic_id'         => $request->topic_id,
                'title'            => $request->title,
                'slug'             => $slug,
                'news_language_id' => $newsLanguageId,
                'image_size_type'  => $request->image_size_type,
            ]);

            // Handle deleted slides first
            if ($request->has('delete_slides')) {
                foreach ($request->delete_slides as $slideId) {
                    $slide = StorySlide::find($slideId);
                    if ($slide && $slide->story_id === $story->id) {
                        Storage::disk('public')->delete($slide->image);
                        $slide->delete();
                    }
                }
            }

            // Process remaining slides
            $slideOrder       = json_decode($request->slide_order, true);
            $existingSlideIds = $story->story_slides->pluck('id')->toArray();
            $updatedSlideIds  = [];

            foreach ($slideOrder as $order => $index) {
                $slideData = $request->slides[$index];
                $slideId   = $slideData['id'] ?? null;

                if ($request->has('delete_slides') && in_array($slideId, $request->delete_slides)) {
                    continue;
                }

                $slideUpdateData = [
                    'story_id'          => $story->id,
                    'title'             => $slideData['title'] ?? "",
                    'description'       => $slideData['description'] ?? null,
                    'order'             => $order,
                    'animation_details' => json_decode($request->animation_settings, true),
                ];

                if (isset($slideData['image']) && $slideData['image'] instanceof \Illuminate\Http\UploadedFile) {
                    $slideUpdateData['image'] = $slideData['image']->store('story_slides', 'public');
                }

                if ($slideId) {
                    // Update existing slide
                    $slide = StorySlide::find($slideId);
                    if ($slide) {
                        if (isset($slideUpdateData['image'])) {
                            Storage::disk('public')->delete($slide->image);
                        }
                        $slide->where(['id' => $slideId])->update($slideUpdateData);
                        $updatedSlideIds[] = $slideId;
                    }
                } else {
                    // Create new slide
                    $slide             = StorySlide::create($slideUpdateData);
                    $updatedSlideIds[] = $slide->id;
                }
            }

            DB::commit();
            return response()->json([
                'status'   => 'success',
                'message'  => 'Story updated successfully.',
                'redirect' => route('stories.publicIndex'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating story: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Failed to update story. Please try again.',
            ], 500);
        }
    }

    public function destroy(Story $story)
    {
        ResponseService::noPermissionThenRedirect('delete-story');

        $story->delete();
        return redirect()->route('stories.publicIndex')->with('success', 'Story deleted successfully.');
    }

    public function reorderView(Story $story)
    {
        $story->load(['story_slides' => function ($query) {
            $query->orderBy('order', 'asc');
        }]);

        return view('admin.webstory.reorder', compact('story'));
    }

    public function updateOrder(Request $request, Story $story)
    {
        try {
            DB::beginTransaction();

            $order = $request->input('order');

            $slideCount = $story->story_slides()->whereIn('id', $order)->count();
            if ($slideCount !== count($order)) {
                throw new \Exception('Invalid slide IDs provided');
            }

            // Update order for each slide
            foreach ($order as $index => $id) {
                StorySlide::where('id', $id)
                    ->where('story_id', $story->id)
                    ->update(['order' => $index]);
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Slide order updated successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating slide order: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to update slide order',
            ], 500);
        }
    }
}
