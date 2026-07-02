<?php
namespace App\Providers;

use App\Http\Livewire\MyAdReport;
use App\Models\Channel;
use App\Models\Language;
use App\Models\NewsLanguage;
use App\Models\NewsLanguageStatus;
use App\Models\NewsLanguageSubscriber;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Theme;
use App\Models\Topic;
use App\Services\CachingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    const TIME_FORMATE = 'Y-m-d H:i';
    public function register()
    {
        //
    }

    public function boot()
    {
        Livewire::component('my-ad-report', MyAdReport::class);
        View::composer('*', function ($view) {
            try {
                $userId = Auth::id() ?? 0;

                // changes done here by P
                /* OLD CODE:
                $defaultImage = url('storage/' . $this->getSetting('default_image')->value);
                */
                static $allSettings = null;
                if ($allSettings === null) {
                    $allSettings = Setting::select('name', 'value', 'updated_at')->get()->keyBy('name');
                }
                $getSetting = function ($name) use ($allSettings) {
                    return $allSettings->get($name);
                };
                $defaultImageSetting = $getSetting('default_image');
                $defaultImage = $defaultImageSetting ? url('storage/' . $defaultImageSetting->value) : '';

                // Get subscribed language IDs
                if ($userId) {
                    $subscribedLanguageIds = NewsLanguageSubscriber::where('user_id', $userId)->pluck('news_language_id');
                    if ($subscribedLanguageIds->isEmpty()) {
                        $defaultLanguage = NewsLanguage::where('is_active', 1)->first();
                        if ($defaultLanguage) {
                            NewsLanguageSubscriber::create([
                                'user_id'          => $userId,
                                'news_language_id' => $defaultLanguage->id,
                            ]);
                            $subscribedLanguageIds = collect([$defaultLanguage->id]);
                        }
                    }
                } else {
                    $sessionLanguageId = session('selected_news_language');
                    if ($sessionLanguageId) {
                        $subscribedLanguageIds = collect([$sessionLanguageId]);
                    } else {
                        $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
                        $subscribedLanguageIds = $defaultActiveLanguage ? collect([$defaultActiveLanguage->id]) : collect();
                    }
                }

                // Fetch topics
                $topics = Topic::select('id', 'name', 'slug', 'logo')
                    ->where('status', 'active')
                    ->whereHas('posts', function ($query) use ($subscribedLanguageIds) {
                        if ($subscribedLanguageIds->isNotEmpty()) {
                            $query->whereIn('news_language_id', $subscribedLanguageIds);
                        }
                    })
                    ->orderBy('categorie_order', 'asc')
                    ->take(8)
                    ->get();

                // changes done here by P
                /* OLD CODE:
                // Fetch posts for each topic
                foreach ($topics as $topic) {
                    $topicPostsQuery = Post::select('id', 'image', 'video', 'video_thumb', 'type', 'title', 'slug', 'comment', 'publish_date', 'pubdate', 'status', 'view_count', 'reaction')
                        ->where('posts.status', 'active')
                        ->whereHas('channel', function ($query) {
                            $query->where('status', 'active');
                        })
                        ->whereHas('topic', function ($q) {
                            $q->where('status', 'active');
                        })
                        ->where('topic_id', $topic->id);

                    if ($userId) {
                        $subscribedLanguageIds = NewsLanguageSubscriber::where('user_id', $userId)->pluck('news_language_id');
                    } else {
                        $sessionLanguageId = session('selected_news_language');
                        if ($sessionLanguageId) {
                            // If user selected a language, use it (even if not active)
                            $subscribedLanguageIds = collect([$sessionLanguageId]);
                        } else {
                            // If not selected, use the first active language
                            $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
                            $subscribedLanguageIds = $defaultActiveLanguage ? collect([$defaultActiveLanguage->id]) : collect();
                        }
                    }
                    if ($subscribedLanguageIds->isNotEmpty()) {
                        $topicPostsQuery->whereIn('posts.news_language_id', $subscribedLanguageIds);
                    }

                    $topic->posts = $topicPostsQuery
                        ->orderBy('publish_date', 'DESC')
                        ->take(5)
                        ->get()
                        ->map(function ($item) use ($defaultImage) {
                            $item->image = $item->image ?? $defaultImage;

                            if ($item->publish_date) {
                                $item->publish_date = Carbon::parse($item->publish_date)->diffForHumans();
                            } elseif ($item->pubdate) {
                                $item->pubdate = Carbon::parse($item->pubdate)->diffForHumans();
                            }
                            return $item;
                        });
                }
                */

                // NEW OPTIMIZED CODE:
                $topicIds = $topics->pluck('id');
                if ($topicIds->isNotEmpty()) {
                    $allTopicPosts = Post::select('id', 'image', 'video', 'video_thumb', 'type', 'title', 'slug', 'comment', 'publish_date', 'pubdate', 'status', 'view_count', 'reaction', 'topic_id')
                        ->where('posts.status', 'active')
                        ->whereIn('topic_id', $topicIds)
                        ->whereHas('channel', function ($query) {
                            $query->where('status', 'active');
                        })
                        ->when($subscribedLanguageIds->isNotEmpty(), function ($q) use ($subscribedLanguageIds) {
                            $q->whereIn('posts.news_language_id', $subscribedLanguageIds);
                        })
                        ->orderBy('publish_date', 'DESC')
                        ->get()
                        ->groupBy('topic_id');
                } else {
                    $allTopicPosts = collect();
                }

                foreach ($topics as $topic) {
                    $topic->posts = ($allTopicPosts->get($topic->id) ?? collect())
                        ->take(5)
                        ->map(function ($item) use ($defaultImage) {
                            $item->image = $item->image ?? $defaultImage;
                            if ($item->publish_date) {
                                $item->publish_date = Carbon::parse($item->publish_date)->diffForHumans();
                            } elseif ($item->pubdate) {
                                $item->pubdate = Carbon::parse($item->pubdate)->diffForHumans();
                            }
                            return $item;
                        });
                }

                $topics = $topics->filter(function ($topic) {
                    return $topic->posts->isNotEmpty();
                })->values();

                $langCode = 'zxx';
                $dir      = 'ltr';

                if ($subscribedLanguageIds->isNotEmpty()) {
                    $newsLang = NewsLanguage::find($subscribedLanguageIds->first());
                    if ($newsLang) {
                        $langCode = $newsLang->code ?? 'zxx';
                        $dir      = ($langCode === 'ar') ? 'rtl' : 'ltr';
                    }
                }

                $channels = Channel::select('id', 'name', 'slug', 'logo')
                    ->where('status', 'active')
                    ->whereHas('posts', function ($query) use ($subscribedLanguageIds) {
                        if ($subscribedLanguageIds->isNotEmpty()) {
                            $query->whereIn('news_language_id', $subscribedLanguageIds);
                        }
                    })
                    ->take(6)
                    ->get();

                // changes done here by P
                /* OLD CODE:
                // Fetch posts for each selected channel
                foreach ($channels as $channel) {
                    $channelPostsQuery = Post::select('id', 'image', 'video', 'video_thumb', 'type', 'title', 'slug', 'comment', 'publish_date', 'reaction', 'pubdate', 'view_count', 'status')
                        ->where('posts.status', 'active')
                        ->whereHas('channel', function ($query) {
                            $query->where('status', 'active');
                        })
                    // ->whereHas('topic', function ($q) {
                    //     $q->where('status', 'active');
                    // })
                        ->where(function ($q) {
                            $q->whereHas('topic', function ($query) {
                                $query->where('status', 'active');
                            })
                                ->orWhereDoesntHave('topic'); // 👈 makes topic optional
                        })
                        ->where('channel_id', $channel->id);

                    if ($subscribedLanguageIds->isNotEmpty()) {
                        $channelPostsQuery->whereIn('news_language_id', $subscribedLanguageIds);
                    }

                    $channel->posts = $channelPostsQuery
                        ->orderBy('publish_date', 'DESC')
                        ->take(4)
                        ->get()
                        ->map(function ($item) use ($defaultImage) {
                            $item->image = $item->image ?? $defaultImage;
                            if ($item->image === url('storage')) {
                                $item->image = $defaultImage;
                            } else {
                                $item->image = $item->image;
                            }

                            if ($item->publish_date) {
                                $item->publish_date = Carbon::parse($item->publish_date)->diffForHumans();
                            } elseif ($item->pubdate) {
                                $item->pubdate = Carbon::parse($item->pubdate)->diffForHumans();
                            }
                            return $item;
                        });
                }
                */

                // NEW OPTIMIZED CODE:
                $channelIds = $channels->pluck('id');
                if ($channelIds->isNotEmpty()) {
                    $allChannelPosts = Post::select('id', 'image', 'video', 'video_thumb', 'type', 'title', 'slug', 'comment', 'publish_date', 'reaction', 'pubdate', 'view_count', 'status', 'channel_id')
                        ->where('posts.status', 'active')
                        ->whereIn('channel_id', $channelIds)
                        ->whereHas('channel', function ($query) {
                            $query->where('status', 'active');
                        })
                        ->where(function ($q) {
                            $q->whereHas('topic', function ($query) {
                                $query->where('status', 'active');
                            })
                                ->orWhereDoesntHave('topic');
                        })
                        ->when($subscribedLanguageIds->isNotEmpty(), function ($q) use ($subscribedLanguageIds) {
                            $q->whereIn('news_language_id', $subscribedLanguageIds);
                        })
                        ->orderBy('publish_date', 'DESC')
                        ->get()
                        ->groupBy('channel_id');
                } else {
                    $allChannelPosts = collect();
                }

                foreach ($channels as $channel) {
                    $channel->posts = ($allChannelPosts->get($channel->id) ?? collect())
                        ->take(4)
                        ->map(function ($item) use ($defaultImage) {
                            $item->image = $item->image ?? $defaultImage;
                            if ($item->image === url('storage')) {
                                $item->image = $defaultImage;
                            }
                            if ($item->publish_date) {
                                $item->publish_date = Carbon::parse($item->publish_date)->diffForHumans();
                            } elseif ($item->pubdate) {
                                $item->pubdate = Carbon::parse($item->pubdate)->diffForHumans();
                            }
                            return $item;
                        });
                }

                $channels = $channels->filter(function ($channel) {
                    return $channel->posts->isNotEmpty();
                })->values();

                $firstChannelPosts = Post::select('id', 'image', 'video', 'video_thumb', 'type', 'slug', 'title', 'comment', 'reaction', 'view_count', 'publish_date', 'pubdate', 'status')
                    ->when($subscribedLanguageIds->isNotEmpty(), function ($query) use ($subscribedLanguageIds) {
                        $query->whereIn('news_language_id', $subscribedLanguageIds);
                    })
                    ->where('posts.status', 'active')
                    ->whereHas('channel', function ($query) {
                        $query->where('status', 'active');
                    })
                    ->where(function ($q) {
                        $q->whereHas('topic', function ($query) {
                            $query->where('status', 'active');
                        })
                            ->orWhereDoesntHave('topic');
                    })
                    ->orderBy('publish_date', 'DESC')
                    ->take(4)
                    ->get()
                    ->map(function ($item) use ($defaultImage) {
                        $item->image = $item->image ?? $defaultImage;
                        if ($item->image === url('storage')) {
                            $item->image = $defaultImage;
                        }
                        if ($item->publish_date) {
                            $item->publish_date = Carbon::parse($item->publish_date)->diffForHumans();
                        } elseif ($item->pubdate) {
                            $item->pubdate = Carbon::parse($item->pubdate)->diffForHumans();
                        }
                        return $item;
                    });

                $data = [
                    [
                        "id"    => 0,
                        "name"  => __('frontend-labels.home.all'),
                        "slug"  => "",
                        "posts" => $firstChannelPosts,
                    ],
                ];
                $channels->prepend((object) $data[0]);
                $socialsettings = Setting::pluck('value', 'name');
                $news_languages_overwrite = NewsLanguage::where('status', 'active')->get();
                $news_language_status     = NewsLanguageStatus::getCurrentStatus();
                $newsletterSettings       = $this->getNewsletterSettings();

                // ---------------------------------------------
                // 1️⃣ Determine Locale based on Route
                // ---------------------------------------------
                if (request()->is('admin*')) {
                    $finalLanguageCode = Session::get('admin_locale', config('app.locale'));
                    app()->setLocale($finalLanguageCode);

                    $web_languages = $languages = Language::all();
                    $view->with([
                        'languages'         => $languages,
                        'finalLanguageCode' => $finalLanguageCode,
                    ]);
                } elseif (Session::has('web_locale') && Session::get('web_locale') != null) {
                    // 2️⃣ If web language already selected → DO NOT override
                    $finalLanguageCode = Session::get('web_locale');
                    app()->setLocale($finalLanguageCode);

                    $web_languages = Language::all();

                    // push to view
                    $view->with([
                        'web_languages'     => $web_languages,
                        'finalLanguageCode' => $finalLanguageCode,
                    ]);

                } else {

                    // ---------------------------------------------
                    // 3️⃣ No web language → apply subscription language
                    // ---------------------------------------------

                    $checklanguageCode = NewsLanguage::find($subscribedLanguageIds)->first();

                    if ($checklanguageCode) {
                        $webLanguage = Language::where('code', $checklanguageCode->code)->first();
                    }

                    // ---------------------------------------------
                    // 4️⃣ If no mapping → fallback to default
                    // ---------------------------------------------
                    if (empty($webLanguage)) {
                        $defaultLocale = config('app.locale');
                        $webLanguage   = Language::where('code', $defaultLocale)->first();
                    }

                    // ---------------------------------------------
                    // 5️⃣ Save this as current web locale
                    // ---------------------------------------------
                    Session::put('web_locale', $webLanguage->code);
                    Session::put('web_language', (object) $webLanguage->toArray());
                    Session::save();

                    app()->setLocale($webLanguage->code);

                    $finalLanguageCode = $webLanguage->code;
                    $web_languages     = Language::all();

                    // push to view
                    $view->with([
                        'web_languages'     => $web_languages,
                        'finalLanguageCode' => $finalLanguageCode,
                    ]);
                }

                $freeTrialSettings = Setting::whereIn('name', [
                    'free_trial_status',
                    'free_trial_post_limit',
                    'free_trial_story_limit',
                    'free_trial_e_papers_and_magazines_limit'
                ])->pluck('value', 'name');
                $cookiesPopupStatus = Setting::select('value')->where('name', 'cookies_popup_status')->first();

                $view->with([
                    // changes done here by P
                    // 'favicon'                           => $this->getFavicon(),
                    'favicon'                           => $getSetting('favicon_icon') ? url('storage/' . $getSetting('favicon_icon')->value) : '',

                    // changes done here by P
                    // 'webTitle'                          => $this->getSetting('company_name'),
                    'webTitle'                          => $getSetting('company_name'),

                    // changes done here by P
                    // 'post_label'                        => $this->getSetting('news_label_place_holder'),
                    'post_label'                        => $getSetting('news_label_place_holder'),

                    'headerPosts'                       => $this->getRecentPosts(8),

                    // changes done here by P
                    // 'termsOfCondition'                  => $this->getSetting('terms_conditions'),
                    'termsOfCondition'                  => $getSetting('terms_conditions'),

                    'socialMedia'                       => Setting::select('name', 'value', 'updated_at')->get()->toArray(),
                    'channels'                          => $channels,
                    'topics'                            => $topics,

                    // changes done here by P
                    // 'dark_logo'                         => $this->getSetting('dark_logo'),
                    'dark_logo'                         => $getSetting('dark_logo'),

                    // changes done here by P
                    // 'light_logo'                        => $this->getSetting('light_logo'),
                    'light_logo'                        => $getSetting('light_logo'),

                    // changes done here by P
                    // 'dark_logo_size'                    => $this->getSetting('dark_logo_size'),
                    'dark_logo_size'                    => $getSetting('dark_logo_size'),

                    // changes done here by P
                    // 'light_logo_size'                   => $this->getSetting('light_logo_size'),
                    'light_logo_size'                   => $getSetting('light_logo_size'),

                    // changes done here by P
                    // 'play_store_link'                   => $this->getSetting('play_store_link'),
                    'play_store_link'                   => $getSetting('play_store_link'),

                    // changes done here by P
                    // 'app_store_link'                    => $this->getSetting('app_store_link'),
                    'app_store_link'                    => $getSetting('app_store_link'),

                    // changes done here by P
                    // 'application_download_popup_on_web' => $this->getSetting('application_download_popup_on_web'),
                    'application_download_popup_on_web' => $getSetting('application_download_popup_on_web'),

                    // changes done here by P
                    // 'app_scheme'                        => $this->getSetting('android_shceme'),
                    'app_scheme'                        => $getSetting('android_shceme'),

                    // changes done here by P
                    // 'ios_shceme'                        => $this->getSetting('ios_shceme'),
                    'ios_shceme'                        => $getSetting('ios_shceme'),

                    // changes done here by P
                    // 'header_script'                     => $this->getSetting('header_script'),
                    'header_script'                     => $getSetting('header_script'),

                    // changes done here by P
                    // 'footer_script'                     => $this->getSetting('footer_script'),
                    'footer_script'                     => $getSetting('footer_script'),

                    // changes done here by P
                    // 'placeholder_image'                 => $this->getSetting('placeholder_image'),
                    'placeholder_image'                 => $getSetting('placeholder_image'),

                    // changes done here by P
                    // 'sponsor_ad_rotation_time'          => $this->getSetting('sponsor_ad_rotation_time'),
                    'sponsor_ad_rotation_time'          => $getSetting('sponsor_ad_rotation_time'),

                    // changes done here by P
                    // 'seo_title'                         => $this->getSetting('seo_title'),
                    'seo_title'                         => $getSetting('seo_title'),

                    // changes done here by P
                    // 'meta_description'                  => $this->getSetting('meta_description'),
                    'meta_description'                  => $getSetting('meta_description'),

                    // changes done here by P
                    // 'meta_keywords'                     => $this->getSetting('meta_keywords'),
                    'meta_keywords'                     => $getSetting('meta_keywords'),

                    'free_trial_status'                 => $free_trial_status = ($freeTrialSettings['free_trial_status'] ?? '0'),
                    'free_trial_post_limit'             => $free_trial_post_limit = (($free_trial_status == '1') ? -1 : ($freeTrialSettings['free_trial_post_limit'] ?? '0')),
                    'free_trial_story_limit'            => $free_trial_story_limit = (($free_trial_status == '1') ? -1 : ($freeTrialSettings['free_trial_story_limit'] ?? '0')),
                    'free_trial_epaper_limit'           => $free_trial_epaper_limit = (($free_trial_status == '1') ? -1 : ($freeTrialSettings['free_trial_e_papers_and_magazines_limit'] ?? '0')),
                    
                    // Also pass as camelCase for consistency with controller updates
                    'freeTrialStatus'                   => $free_trial_status,
                    'freeTrialPostLimit'                => $free_trial_post_limit,
                    'freeTrialStoryLimit'               => $free_trial_story_limit,
                    'freeTrialEpaperLimit'              => $free_trial_epaper_limit,
                    'getTheme'                          => $this->getTheme(),
                    'news_languages_overwrite'          => $news_languages_overwrite,
                    'news_language_status'              => $news_language_status,
                    'subscribedLanguageIds'             => $subscribedLanguageIds, // Pass subscribed IDs to the view
                    'langCode'                          => $langCode,
                    'dir'                               => $dir,
                    'newsletterSettings'                => $newsletterSettings,
                    'socialsettings'                    => $socialsettings,
                    'firebaseConfig'                    => $this->getFirebaseConfig(),
                    'web_languages'                     => $web_languages,
                    'finalLanguageCode'                 => $finalLanguageCode,
                    'defaultImage'                      => $defaultImage,
                    'cookiesPopupStatus'                => $cookiesPopupStatus,

                    // changes done here by P
                    // 'web_theme_primary_colour'          => $this->getSetting('web_theme_primary_colour') ? $this->getSetting('web_theme_primary_colour')->value : '#9c0d0d',
                    'web_theme_primary_colour'          => $getSetting('web_theme_primary_colour') ? $getSetting('web_theme_primary_colour')->value : '#9c0d0d',

                    // changes done here by P
                    // 'web_font'                          => $this->getSetting('web_font') ? $this->getSetting('web_font')->value : 'Poppins',
                    'web_font'                          => $getSetting('web_font') ? $getSetting('web_font')->value : 'Poppins',
                ]);
            } catch (Throwable $e) {
                Log::error('Error in View Composer: ' . $e->getMessage());
                return $e;
            }
        });
    }

    // Add this new method in AppServiceProvider class
    public function getChannelPostsData($channelId)
    {
        $userId = Auth::id() ?? 0;

        $defaultImage = Setting::where('name', 'default_image')->first()->value ?? null;

        // Get subscribed language IDs (same logic as boot method)
        if ($userId) {
            $subscribedLanguageIds = NewsLanguageSubscriber::where('user_id', $userId)
                ->pluck('news_language_id');
        } else {
            $sessionLanguageId = session('selected_news_language');
            if ($sessionLanguageId) {
                $subscribedLanguageIds = collect([$sessionLanguageId]);
            } else {
                $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
                $subscribedLanguageIds = $defaultActiveLanguage ? collect([$defaultActiveLanguage->id]) : collect();
            }
        }

        // Fetch posts based on channel ID
        if ($channelId == 0) {
            // All posts (existing logic)
            $posts = Post::select('id', 'image', 'video', 'video_thumb', 'type', 'slug', 'title', 'comment', 'reaction', 'view_count', 'publish_date', 'pubdate', 'status')
                ->when($subscribedLanguageIds->isNotEmpty(), function ($query) use ($subscribedLanguageIds) {
                    $query->whereIn('news_language_id', $subscribedLanguageIds);
                })
                ->where('posts.status', 'active')
                ->whereHas('channel', function ($query) {
                    $query->where('status', 'active');
                })
            // ->whereHas('topic', function ($q) {
            //     $q->where('status', 'active');
            // })
                ->where(function ($q) {
                    $q->whereHas('topic', function ($query) {
                        $query->where('status', 'active');
                    })
                        ->orWhereDoesntHave('topic'); // 👈 makes topic optional
                })
                ->orderBy('publish_date', 'DESC')
                ->take(4)
                ->get();
        } else {
            // Channel-specific posts (existing logic)
            $posts = Post::select('id', 'image', 'video', 'video_thumb', 'type', 'title', 'slug', 'comment', 'publish_date', 'reaction', 'pubdate', 'view_count', 'status')
                ->where('posts.status', 'active')
                ->where('channel_id', $channelId)
                ->when($subscribedLanguageIds->isNotEmpty(), function ($query) use ($subscribedLanguageIds) {
                    $query->whereIn('news_language_id', $subscribedLanguageIds);
                })
                ->whereHas('channel', function ($query) {
                    $query->where('status', 'active');
                })
            // ->whereHas('topic', function ($q) {
            //     $q->where('status', 'active');
            // })
                ->where(function ($q) {
                    $q->whereHas('topic', function ($query) {
                        $query->where('status', 'active');
                    })
                        ->orWhereDoesntHave('topic'); // 👈 makes topic optional
                })
                ->orderBy('publish_date', 'DESC')
                ->take(4)
                ->get();
        }

        // Format posts (existing logic)
        $posts = $posts->map(function ($item) use ($defaultImage) {
            $item->image             = $item->image ?? $defaultImage;
            $item->publish_date_news = Carbon::parse($item->publish_date)->format(self::TIME_FORMATE);
            if ($item->publish_date) {
                $item->publish_date = Carbon::parse($item->publish_date_news)->diffForHumans();
            } elseif ($item->pubdate) {
                $item->pubdate = Carbon::parse($item->pubdate)->diffForHumans();
            }

            return $item;
        });
        return $posts;
    }
    protected function getTheme()
    {
        try {
            $themeData = Theme::select('slug')->where('is_default', '1')->first();
            return optional($themeData)->slug ?? 'classic';
        } catch (Throwable $e) {
            return "";
        }
    }

    protected function getFavicon()
    {
        return CachingService::getSystemSettings('favicon_icon');
    }

    protected function getSetting($name)
    {
        try {
            return Setting::select('name', 'value', 'updated_at')->where('name', $name)->first();
        } catch (Throwable $e) {
            return "";
        }
    }
    protected function getRecentPosts($limit)
    {
        try {
            $userId = Auth::id() ?? 0;

            if ($userId) {
                $subscribedLanguageIds = NewsLanguageSubscriber::where('user_id', $userId)->pluck('news_language_id');
            } else {
                $sessionLanguageId = session('selected_news_language');
                if ($sessionLanguageId) {
                    // If user selected a language, use it (even if not active)
                    $subscribedLanguageIds = collect([$sessionLanguageId]);
                } else {
                    // If not selected, use the first active language
                    $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
                    $subscribedLanguageIds = $defaultActiveLanguage ? collect([$defaultActiveLanguage->id]) : collect();
                }
            }
            // Build query with optional language filter
            $query = Post::select('title', 'slug', 'status')
                ->where('posts.status', 'active')
                ->orderBy('publish_date', 'DESC');

            if ($subscribedLanguageIds->isNotEmpty()) {
                $query->whereIn('news_language_id', $subscribedLanguageIds);
            }

            return $query->take($limit)->get();
        } catch (Throwable $e) {
            Log::error('Error in getRecentPosts: ' . $e->getMessage());
            return collect(); // Return empty collection instead of empty string for consistency
        }
    }

    private function getNewsletterSettings()
    {
        $settings = Setting::whereIn('name', [
            'subscribe_model_title',
            'subscribe_model_sub_title',
            'subscribe_model_status',
            'subscribe_model_image',
        ])->pluck('value', 'name');

        return [
            'title'    => $settings['subscribe_model_title'] ?? 'Subscribe to the Newsletter',
            'subtitle' => $settings['subscribe_model_sub_title'] ?? 'Join 10k+ people to get notified about new posts, news and tips.',
            'status'   => $settings['subscribe_model_status'] ?? '0',
            'image'    => $settings['subscribe_model_image'] ?? '',
        ];
    }

    protected function getFirebaseConfig()
    {
        try {
            $firebaseSettings = Setting::whereIn('name', [
                'apiKey',
                'authDomain',
                'projectId',
                'storageBucket',
                'messagingSenderId',
                'appId',
                'measurementId',
            ])->pluck('value', 'name');

            return [
                'apiKey'            => $firebaseSettings['apiKey'] ?? '',
                'authDomain'        => $firebaseSettings['authDomain'] ?? '',
                'projectId'         => $firebaseSettings['projectId'] ?? '',
                'storageBucket'     => $firebaseSettings['storageBucket'] ?? '',
                'messagingSenderId' => $firebaseSettings['messagingSenderId'] ?? '',
                'appId'             => $firebaseSettings['appId'] ?? '',
                'measurementId'     => $firebaseSettings['measurementId'] ?? '',
            ];
        } catch (Throwable $e) {
            Log::error('Error getting Firebase config: ' . $e->getMessage());
            return [];
        }
    }

}
