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
        error_reporting(error_reporting() & ~E_DEPRECATED);
        config(['debugbar.capture_ajax' => false]);
        Livewire::component('my-ad-report', MyAdReport::class);

        $incrementBuster = function() {
            try {
                if (!\Illuminate\Support\Facades\Cache::has('view_composer_cache_buster')) {
                    \Illuminate\Support\Facades\Cache::forever('view_composer_cache_buster', 1);
                } else {
                    \Illuminate\Support\Facades\Cache::increment('view_composer_cache_buster');
                }
            } catch (\Throwable $e) {}
        };

        // Auto-clear Web Language lists when modified
        \App\Models\Language::saved(function () use ($incrementBuster) {
            \Illuminate\Support\Facades\Cache::forget('all_languages_list');
            $incrementBuster();
        });
        \App\Models\Language::deleted(function () use ($incrementBuster) {
            \Illuminate\Support\Facades\Cache::forget('all_languages_list');
            $incrementBuster();
        });

        // Auto-clear News Language lists and default active news language when modified
        \App\Models\NewsLanguage::saved(function () use ($incrementBuster) {
            \Illuminate\Support\Facades\Cache::forget('all_news_languages_list');
            \Illuminate\Support\Facades\Cache::forget('news_language_default_active');
            $incrementBuster();
        });
        \App\Models\NewsLanguage::deleted(function () use ($incrementBuster) {
            \Illuminate\Support\Facades\Cache::forget('all_news_languages_list');
            \Illuminate\Support\Facades\Cache::forget('news_language_default_active');
            $incrementBuster();
        });

        // Auto-bust View Composer cache when setting is changed
        \App\Models\Setting::saved(function () use ($incrementBuster) {
            \Illuminate\Support\Facades\Cache::forget('view_composer_settings_list');
            $incrementBuster();
        });
        \App\Models\Setting::deleted(function () use ($incrementBuster) {
            \Illuminate\Support\Facades\Cache::forget('view_composer_settings_list');
            $incrementBuster();
        });

        // Auto-clear active PaymentSetting cache when modified
        \App\Models\PaymentSetting::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('active_payment_setting');
        });
        \App\Models\PaymentSetting::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('active_payment_setting');
        });

        // Auto-clear active theme slug cache when modified
        \App\Models\Theme::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('active_theme_slug');
        });
        \App\Models\Theme::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('active_theme_slug');
        });

        // Auto-clear NewsLanguageSubscriber cache when modified
        \App\Models\NewsLanguageSubscriber::saved(function ($model) {
            \Illuminate\Support\Facades\Cache::forget("user_subscribed_languages_{$model->user_id}");
        });
        \App\Models\NewsLanguageSubscriber::deleted(function ($model) {
            \Illuminate\Support\Facades\Cache::forget("user_subscribed_languages_{$model->user_id}");
        });

        View::composer('*', function ($view) {
            $request = request();
            if ($request->attributes->has('shared_view_data')) {
                $sharedViewData = $request->attributes->get('shared_view_data');
                if (isset($sharedViewData['finalLanguageCode'])) {
                    app()->setLocale($sharedViewData['finalLanguageCode']);
                }
                $view->with($sharedViewData);
                return;
            }

            try {
                $userId = Auth::id() ?? 0;

                if ($request->attributes->has('settings_cache')) {
                    $allSettings = $request->attributes->get('settings_cache');
                } else {
                    $allSettings = \Illuminate\Support\Facades\Cache::rememberForever('view_composer_settings_list', function () {
                        return \Illuminate\Support\Facades\DB::table('settings')->select('name', 'value', 'updated_at')->get()->keyBy('name');
                    });
                    $request->attributes->set('settings_cache', $allSettings);
                }
                $getSetting = function ($name) use ($allSettings) {
                    return $allSettings->get($name);
                };

                // Cache all languages and news languages forever to avoid repeatedly querying
                $allLanguages = \Illuminate\Support\Facades\Cache::rememberForever('all_languages_list', function() {
                    return Language::all();
                });

                $allNewsLanguages = \Illuminate\Support\Facades\Cache::rememberForever('all_news_languages_list', function() {
                    return NewsLanguage::all();
                });

                // Get subscribed language IDs
                if ($request->attributes->has('subscribed_language_ids')) {
                    $subscribedLanguageIds = $request->attributes->get('subscribed_language_ids');
                } else {
                    if ($userId) {
                        $subscribedLanguageIds = \Illuminate\Support\Facades\Cache::remember("user_subscribed_languages_{$userId}", 3600, function () use ($userId) {
                            return NewsLanguageSubscriber::where('user_id', $userId)->pluck('news_language_id');
                        });
                        if ($subscribedLanguageIds->isEmpty()) {
                            if ($request->attributes->has('active_language_cache')) {
                                $defaultLanguage = $request->attributes->get('active_language_cache');
                            } else {
                                $defaultLanguage = \Illuminate\Support\Facades\Cache::rememberForever('news_language_default_active', function() {
                                    return NewsLanguage::where('is_active', 1)->first();
                                });
                                $request->attributes->set('active_language_cache', $defaultLanguage);
                            }
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
                            if ($request->attributes->has('active_language_cache')) {
                                $defaultActiveLanguage = $request->attributes->get('active_language_cache');
                            } else {
                                $defaultActiveLanguage = \Illuminate\Support\Facades\Cache::rememberForever('news_language_default_active', function() {
                                    return NewsLanguage::where('is_active', 1)->first();
                                });
                                $request->attributes->set('active_language_cache', $defaultActiveLanguage);
                            }
                            $subscribedLanguageIds = $defaultActiveLanguage ? collect([$defaultActiveLanguage->id]) : collect();
                        }
                    }
                    $request->attributes->set('subscribed_language_ids', $subscribedLanguageIds);
                }

                // Resolve finalLanguageCode and web_languages
                $composerData = [];
                if (request()->is('admin*')) {
                    $finalLanguageCode = Session::get('admin_locale', config('app.locale'));
                    app()->setLocale($finalLanguageCode);

                    $web_languages = $languages = $allLanguages;
                    $composerData['languages']         = $languages;
                    $composerData['finalLanguageCode'] = $finalLanguageCode;
                } elseif (Session::has('web_locale') && Session::get('web_locale') != null) {
                    $finalLanguageCode = Session::get('web_locale');
                    app()->setLocale($finalLanguageCode);

                    $web_languages = $allLanguages;
                    $composerData['web_languages']     = $web_languages;
                    $composerData['finalLanguageCode'] = $finalLanguageCode;
                } else {
                    $checklanguageCode = $allNewsLanguages->whereIn('id', $subscribedLanguageIds)->first();

                    if ($checklanguageCode) {
                        $webLanguage = $allLanguages->where('code', $checklanguageCode->code)->first();
                    }

                    if (empty($webLanguage)) {
                        $defaultLocale = config('app.locale');
                        $webLanguage   = $allLanguages->where('code', $defaultLocale)->first();
                    }

                    Session::put('web_locale', $webLanguage->code);
                    Session::put('web_language', (object) $webLanguage->toArray());
                    Session::save();

                    app()->setLocale($webLanguage->code);

                    $finalLanguageCode = $webLanguage->code;
                    $web_languages     = $allLanguages;
                    $composerData['web_languages']     = $web_languages;
                    $composerData['finalLanguageCode'] = $finalLanguageCode;
                }

                $langCode = 'zxx';
                $dir      = 'ltr';

                if ($subscribedLanguageIds->isNotEmpty()) {
                    $firstLangId = $subscribedLanguageIds->first();
                    $cachedActiveLang = $request->attributes->get('active_language_cache');
                    if ($cachedActiveLang && $cachedActiveLang->id == $firstLangId) {
                        $newsLang = $cachedActiveLang;
                    } else {
                        $newsLang = $allNewsLanguages->where('id', $firstLangId)->first();
                    }
                    if ($newsLang) {
                        $langCode = $newsLang->code ?? 'zxx';
                        $dir      = ($langCode === 'ar') ? 'rtl' : 'ltr';
                    }
                }

                $subscribedStr = $subscribedLanguageIds->isNotEmpty() ? $subscribedLanguageIds->implode('_') : 'none';
                $cacheBuster = \Illuminate\Support\Facades\Cache::get('view_composer_cache_buster', 1);
                $cacheKey = "shared_view_composer_data_" . $finalLanguageCode . "_" . $subscribedStr . "_" . $cacheBuster;

                $cachedComposerData = \Illuminate\Support\Facades\Cache::remember($cacheKey, 600, function () use ($subscribedLanguageIds, $allSettings, $getSetting, $langCode, $dir, $finalLanguageCode, $web_languages, $allNewsLanguages) {
                    $defaultImageSetting = $getSetting('default_image');
                    $defaultImage = $defaultImageSetting ? url('storage/' . $defaultImageSetting->value) : '';

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

                    $channels = Channel::select('id', 'name', 'slug', 'logo')
                        ->where('status', 'active')
                        ->whereHas('posts', function ($query) use ($subscribedLanguageIds) {
                            if ($subscribedLanguageIds->isNotEmpty()) {
                                $query->whereIn('news_language_id', $subscribedLanguageIds);
                            }
                        })
                        ->take(6)
                        ->get();

                    foreach ($channels as $channel) {
                        $channel->posts = Post::select('id', 'image', 'video', 'video_thumb', 'type', 'title', 'slug', 'comment', 'publish_date', 'reaction', 'pubdate', 'view_count', 'status', 'channel_id')
                            ->where('posts.status', 'active')
                            ->where('channel_id', $channel->id)
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
                                $q->whereIn('posts.news_language_id', $subscribedLanguageIds);
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
                                    $item->publish_date = \Carbon\Carbon::parse($item->publish_date)->diffForHumans();
                                } elseif ($item->pubdate) {
                                    $item->pubdate = \Carbon\Carbon::parse($item->pubdate)->diffForHumans();
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
                                $item->publish_date = \Carbon\Carbon::parse($item->publish_date)->diffForHumans();
                            } elseif ($item->pubdate) {
                                $item->pubdate = \Carbon\Carbon::parse($item->pubdate)->diffForHumans();
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

                    $socialsettings = $allSettings->map(fn($item) => $item->value);
                    $news_languages_overwrite = $allNewsLanguages->where('status', 'active');
                    $news_language_status     = NewsLanguageStatus::getCurrentStatus();
                    $newsletterSettings       = $this->getNewsletterSettings($allSettings);

                    $freeTrialSettings = [
                        'free_trial_status'                       => $getSetting('free_trial_status')->value ?? '0',
                        'free_trial_post_limit'                   => $getSetting('free_trial_post_limit')->value ?? '0',
                        'free_trial_story_limit'                  => $getSetting('free_trial_story_limit')->value ?? '0',
                        'free_trial_e_papers_and_magazines_limit' => $getSetting('free_trial_e_papers_and_magazines_limit')->value ?? '0',
                    ];
                    $cookiesPopupStatus = $getSetting('cookies_popup_status');

                    $free_trial_status = ($freeTrialSettings['free_trial_status'] ?? '0');
                    $free_trial_post_limit = (($free_trial_status == '1') ? -1 : ($freeTrialSettings['free_trial_post_limit'] ?? '0'));
                    $free_trial_story_limit = (($free_trial_status == '1') ? -1 : ($freeTrialSettings['free_trial_story_limit'] ?? '0'));
                    $free_trial_epaper_limit = (($free_trial_status == '1') ? -1 : ($freeTrialSettings['free_trial_e_papers_and_magazines_limit'] ?? '0'));

                    return [
                        'favicon'                           => $getSetting('favicon_icon') ? url('storage/' . $getSetting('favicon_icon')->value) : '',
                        'webTitle'                          => $getSetting('company_name'),
                        'post_label'                        => $getSetting('news_label_place_holder'),
                        'headerPosts'                       => $this->getRecentPosts($subscribedLanguageIds, 8),
                        'termsOfCondition'                  => $getSetting('terms_conditions'),
                        'socialMedia'                       => $allSettings->values()->toArray(),
                        'channels'                          => $channels,
                        'topics'                            => $topics,
                        'dark_logo'                         => $getSetting('dark_logo'),
                        'light_logo'                        => $getSetting('light_logo'),
                        'dark_logo_size'                    => $getSetting('dark_logo_size'),
                        'light_logo_size'                   => $getSetting('light_logo_size'),
                        'play_store_link'                   => $getSetting('play_store_link'),
                        'app_store_link'                    => $getSetting('app_store_link'),
                        'application_download_popup_on_web' => $getSetting('application_download_popup_on_web'),
                        'app_scheme'                        => $getSetting('android_shceme'),
                        'ios_shceme'                        => $getSetting('ios_shceme'),
                        'header_script'                     => $getSetting('header_script'),
                        'footer_script'                     => $getSetting('footer_script'),
                        'placeholder_image'                 => $getSetting('placeholder_image'),
                        'sponsor_ad_rotation_time'          => $getSetting('sponsor_ad_rotation_time'),
                        'seo_title'                         => $getSetting('seo_title'),
                        'meta_description'                  => $getSetting('meta_description'),
                        'meta_keywords'                     => $getSetting('meta_keywords'),
                        'free_trial_status'                 => $free_trial_status,
                        'free_trial_post_limit'             => $free_trial_post_limit,
                        'free_trial_story_limit'            => $free_trial_story_limit,
                        'free_trial_epaper_limit'           => $free_trial_epaper_limit,
                        'freeTrialStatus'                   => $free_trial_status,
                        'freeTrialPostLimit'                => $free_trial_post_limit,
                        'freeTrialStoryLimit'               => $free_trial_story_limit,
                        'freeTrialEpaperLimit'              => $free_trial_epaper_limit,
                        'getTheme'                          => $this->getTheme(),
                        'news_languages_overwrite'          => $news_languages_overwrite,
                        'news_language_status'              => $news_language_status,
                        'subscribedLanguageIds'             => $subscribedLanguageIds,
                        'langCode'                          => $langCode,
                        'dir'                               => $dir,
                        'newsletterSettings'                => $newsletterSettings,
                        'socialsettings'                    => $socialsettings,
                        'firebaseConfig'                    => $this->getFirebaseConfig($allSettings),
                        'web_languages'                     => $web_languages,
                        'finalLanguageCode'                 => $finalLanguageCode,
                        'defaultImage'                      => $defaultImage,
                        'cookiesPopupStatus'                => $cookiesPopupStatus,
                        'web_theme_primary_colour'          => $getSetting('web_theme_primary_colour') ? $getSetting('web_theme_primary_colour')->value : '#9c0d0d',
                        'web_font'                          => $getSetting('web_font') ? $getSetting('web_font')->value : 'Poppins',
                    ];
                });

                $composerData = array_merge($composerData, $cachedComposerData);
                $sharedViewData = $composerData;
                request()->attributes->set('shared_view_data', $sharedViewData);
                $view->with($sharedViewData);
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
        // changes done here by P
        /* OLD CODE:
        try {
            $themeData = Theme::select('slug')->where('is_default', '1')->first();
            return optional($themeData)->slug ?? 'classic';
        } catch (Throwable $e) {
            return "";
        }
        */
        return getTheme();
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
    protected function getRecentPosts($subscribedLanguageIds, $limit)
    {
        try {
            // Build query with optional language filter
            $query = Post::select('title', 'slug', 'status')
                ->where('posts.status', 'active')
                ->orderBy('publish_date', 'DESC');

            if ($subscribedLanguageIds && $subscribedLanguageIds->isNotEmpty()) {
                $query->whereIn('news_language_id', $subscribedLanguageIds);
            }

            return $query->take($limit)->get();
        } catch (Throwable $e) {
            Log::error('Error in getRecentPosts: ' . $e->getMessage());
            return collect(); // Return empty collection instead of empty string for consistency
        }
    }

    private function getNewsletterSettings($allSettings = null)
    {
        // changes done here by P
        /* OLD CODE:
        $settings = Setting::whereIn('name', [
            'subscribe_model_title',
            'subscribe_model_sub_title',
            'subscribe_model_status',
            'subscribe_model_image',
        ])->pluck('value', 'name');
        */
        if ($allSettings) {
            $settings = $allSettings->map(fn($item) => $item->value);
        } else {
            $settings = Setting::whereIn('name', [
                'subscribe_model_title',
                'subscribe_model_sub_title',
                'subscribe_model_status',
                'subscribe_model_image',
            ])->pluck('value', 'name');
        }

        return [
            'title'    => $settings['subscribe_model_title'] ?? 'Subscribe to the Newsletter',
            'subtitle' => $settings['subscribe_model_sub_title'] ?? 'Join 10k+ people to get notified about new posts, news and tips.',
            'status'   => $settings['subscribe_model_status'] ?? '0',
            'image'    => $settings['subscribe_model_image'] ?? '',
        ];
    }

    protected function getFirebaseConfig($allSettings = null)
    {
        try {
            // changes done here by P
            /* OLD CODE:
            $firebaseSettings = Setting::whereIn('name', [
                'apiKey',
                'authDomain',
                'projectId',
                'storageBucket',
                'messagingSenderId',
                'appId',
                'measurementId',
            ])->pluck('value', 'name');
            */
            if ($allSettings) {
                $firebaseSettings = $allSettings->map(fn($item) => $item->value);
            } else {
                $firebaseSettings = Setting::whereIn('name', [
                    'apiKey',
                    'authDomain',
                    'projectId',
                    'storageBucket',
                    'messagingSenderId',
                    'appId',
                    'measurementId',
                ])->pluck('value', 'name');
            }

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
