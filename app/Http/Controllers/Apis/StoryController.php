<?php
namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\NewsLanguage;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

const STORAGE_PATH = 'storage/';

class StoryController extends Controller
{
    /**
     * Normalize and validate news_language_id(s) from input.
     *
     * @param mixed $newsLanguageIds
     * @return array
     */
    protected function normalizeNewsLanguageIds($newsLanguageIds)
    {
        if (is_null($newsLanguageIds)) {
            return [];
        }
        if (! is_array($newsLanguageIds)) {
            $newsLanguageIds = explode(',', str_replace(['[', ']'], '', $newsLanguageIds));
        }
        return array_filter($newsLanguageIds, 'is_numeric');
    }

    public function index(Request $request, $type, $topic = '')
    {
        try {
            if ($type == "home") {
                $response = $this->home($request);
            } elseif ($type == "all-stories") {
                $response = $this->categories($request);
            } elseif ($type == "topic") {
                $response = $this->topic($request, $topic);
            }
            return $response;
        } catch (\Exception $e) {
            Log::error('Error in StoryController@index: ' . $e->getMessage());
            return response()->json([
                'error'   => true,
                'message' => 'An error occurred while fetching stories.',
                'data'    => [$e],
            ], 500);
        }
    }

    private function home(Request $request, $story_limit = 1, $topic_limit = 10)
    {
        // Normalize and validate news_language_id(s)
        $newsLanguageIds = $this->normalizeNewsLanguageIds($request->news_language_id);

        // Build the query for topics with stories and slides
        $query = Topic::whereHas('stories', function ($query) use ($newsLanguageIds) {
            // Apply language filter to stories
            if (! empty($newsLanguageIds)) {
                $validLanguageIds = NewsLanguage::whereIn('id', $newsLanguageIds)->pluck('id')->toArray();
                if (! empty($validLanguageIds)) {
                    $query->whereIn('news_language_id', $validLanguageIds);
                }
            } else {
                // Fetch default active language
                $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
                if ($defaultActiveLanguage) {
                    $query->where('news_language_id', $defaultActiveLanguage->id);
                }
            }
        })
            ->with([
                'stories'              => function ($query) use ($newsLanguageIds) {
                    $query->select('id', 'title', 'slug', 'topic_id')
                        ->whereHas('topic', function ($q) {
                            $q->where('status', 'active');
                        })
                        ->orderBy('created_at', 'DESC');

                    // Apply language filter
                    if (! empty($newsLanguageIds)) {
                        $validLanguageIds = NewsLanguage::whereIn('id', $newsLanguageIds)->pluck('id')->toArray();
                        if (! empty($validLanguageIds)) {
                            $query->whereIn('news_language_id', $validLanguageIds);
                        }
                    } else {
                        // Fetch default active language
                        $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
                        if ($defaultActiveLanguage) {
                            $query->where('news_language_id', $defaultActiveLanguage->id);
                        }
                    }
                },
                'stories.story_slides' => function ($query) {
                    $query->select('id', 'story_id', 'title', 'image', 'description', 'order', 'animation_details')
                        ->orderBy('order', 'asc');
                },
            ])
            ->select('id', 'name', 'slug')
            ->limit($topic_limit);

        $topics = $query->get();

        // Shuffle stories within each topic and limit to story_limit
        $topics->each(function ($topic) use ($story_limit) {
            $topicStories = $topic->stories->shuffle()->take($story_limit);

            // Assign shuffled stories back to the topic
            $topic->setRelation('stories', $topicStories);

            // Process each story within the topic
            $topic->stories->each(function ($story) use ($topic) {
                // Set topic_name for each story
                $story->topic_name = $topic->name;

                // Process image paths
                $story->story_slides->map(function ($storySlide) {
                    $storySlide->image = asset(STORAGE_PATH . $storySlide->image);
                    return $storySlide;
                });
            });
        });

        return response()->json([
            'error'   => false,
            'message' => 'Home stories fetched successfully',
            'data'    => $topics,
        ]);
    }

    private function categories(Request $request)
    {
        $perPage = request()->get('per_page', 10);

        // Normalize and validate news_language_id(s)
        $newsLanguageIds = $this->normalizeNewsLanguageIds($request->news_language_id);

        // Build the query for topics with stories and slides
        $query = Topic::whereHas('stories', function ($query) use ($newsLanguageIds) {
            // Apply language filter to stories
            if (! empty($newsLanguageIds)) {
                $validLanguageIds = NewsLanguage::whereIn('id', $newsLanguageIds)->pluck('id')->toArray();
                if (! empty($validLanguageIds)) {
                    $query->whereIn('news_language_id', $validLanguageIds);
                }
            } else {
                // Fetch default active language
                $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
                if ($defaultActiveLanguage) {
                    $query->where('news_language_id', $defaultActiveLanguage->id);
                }
            }
        })
            ->with([
                'stories'              => function ($query) use ($newsLanguageIds) {
                    $query->select('id', 'title', 'slug', 'topic_id')
                        ->whereHas('topic', function ($q) {
                            $q->where('status', 'active');
                        })
                        ->orderBy('created_at', 'DESC')
                        ->take(10);

                    // Apply language filter
                    if (! empty($newsLanguageIds)) {
                        $validLanguageIds = NewsLanguage::whereIn('id', $newsLanguageIds)->pluck('id')->toArray();
                        if (! empty($validLanguageIds)) {
                            $query->whereIn('news_language_id', $validLanguageIds);
                        }
                    } else {
                        // Fetch default active language
                        $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
                        if ($defaultActiveLanguage) {
                            $query->where('news_language_id', $defaultActiveLanguage->id);
                        }
                    }
                },
                'stories.story_slides' => function ($query) {
                    $query->select('id', 'story_id', 'title', 'image', 'description', 'order', 'animation_details')
                        ->orderBy('order', 'asc');
                },
            ])
            ->select('id', 'name', 'slug');

        $topics = $query->paginate($perPage)
            ->map(function ($topic) {
                $topic->stories->each(function ($story) {
                    $story->story_slides->map(function ($storySlide) {
                        $storySlide->image = asset(STORAGE_PATH . $storySlide->image);
                        return $storySlide;
                    });
                });
                return $topic;
            });

        return response()->json([
            'error'   => false,
            'message' => 'All stories fetched successfully',
            'data'    => $topics,
        ]);
    }

    private function topic(Request $request, $topic)
    {
        $perPage = request()->get('per_page', 10);

        // Normalize and validate news_language_id(s)
        $newsLanguageIds = $this->normalizeNewsLanguageIds($request->news_language_id);

        // Build the query for topics with stories and slides
        $query = Topic::where('slug', $topic)
            ->whereHas('stories', function ($query) use ($newsLanguageIds) {
                // Apply language filter to stories
                if (! empty($newsLanguageIds)) {
                    $validLanguageIds = NewsLanguage::whereIn('id', $newsLanguageIds)->pluck('id')->toArray();
                    if (! empty($validLanguageIds)) {
                        $query->whereIn('news_language_id', $validLanguageIds);
                    }
                } else {
                    // Fetch default active language
                    $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
                    if ($defaultActiveLanguage) {
                        $query->where('news_language_id', $defaultActiveLanguage->id);
                    }
                }
            })
            ->with([
                'stories'              => function ($query) use ($perPage, $newsLanguageIds) {
                    $query->select('id', 'title', 'slug', 'topic_id')
                        ->whereHas('topic', function ($q) {
                            $q->where('status', 'active');
                        });

                    // Apply language filter
                    if (! empty($newsLanguageIds)) {
                        $validLanguageIds = NewsLanguage::whereIn('id', $newsLanguageIds)->pluck('id')->toArray();
                        if (! empty($validLanguageIds)) {
                            $query->whereIn('news_language_id', $validLanguageIds);
                        }
                    } else {
                        // Fetch default active language
                        $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
                        if ($defaultActiveLanguage) {
                            $query->where('news_language_id', $defaultActiveLanguage->id);
                        }
                    }

                    $query->paginate($perPage);
                },
                'stories.story_slides' => function ($query) {
                    $query->select('id', 'story_id', 'title', 'image', 'description', 'order', 'animation_details')
                        ->orderBy('order', 'asc');
                },
            ])
            ->select('id', 'name', 'slug');

        $topics = $query->get()->map(function ($topic) {
            $topic->stories->each(function ($story) {
                $story->story_slides->map(function ($storySlide) {
                    $storySlide->image = asset(STORAGE_PATH . $storySlide->image);
                    return $storySlide;
                });
            });
            return $topic;
        });

        return response()->json([
            'error'   => false,
            'message' => 'All stories fetched successfully',
            'data'    => $topics,
        ]);
    }
}
