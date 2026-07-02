<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\ActiveUserCount;
use App\Models\Channel;
use App\Models\Favorite;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Story;
use App\Models\Subscription;
use App\Models\Topic;
use App\Models\Transaction;
use App\Models\User;
use App\Services\ResponseService;
use Carbon\Carbon;
use Google\Client as GoogleClient;
use Google\Service\AdSense as GoogleServiceAdSense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;

class DashboardController extends Controller
{
    const DATE_CREATED_AT   = 'DATE(created_at)';
    const QUERY_SELECT_DATA = 'COUNT(*) as count, DATE(created_at) as date';

    private $adsenseService     = null;
    private $isAdsenseConnected = false;

    public function __construct()
    {
        $this->initializeAdsense();
    }

    private function initializeAdsense()
    {
        try {
            // Fetch AdSense settings from the database using the Setting model
            $clientId     = Setting::where('name', 'adsense_client_id')->value('value');
            $clientSecret = Setting::where('name', 'adsense_client_secret')->value('value');
            $redirectUri  = Setting::where('name', 'adsense_redirect_uri')->value('value');
            $tokenData    = Setting::where('name', 'adsense_token')->value('value'); // JSON stored as text

            if (! $clientId || ! $clientSecret || ! $redirectUri) {
                Log::info('AdSense configuration not found in settings table');
                return;
            }

            $client = new GoogleClient();
            $client->setApplicationName('AdSense API Integration');
            $client->setClientId($clientId);
            $client->setClientSecret($clientSecret);
            $client->setRedirectUri($redirectUri);
            $client->setScopes(['https://www.googleapis.com/auth/adsense.readonly']);
            $client->setAccessType('offline');
            $client->setPrompt('select_account consent');

            if ($tokenData) {
                $accessToken = json_decode($tokenData, true);
                $client->setAccessToken($accessToken);

                // Refresh token if expired
                if ($client->isAccessTokenExpired() && isset($accessToken['refresh_token'])) {
                    $newToken = $client->fetchAccessTokenWithRefreshToken($accessToken['refresh_token']);

                    // Ensure 'name' and 'value' columns exist before updating
                    $table = (new Setting)->getTable();
                    if (! Schema::hasColumn($table, 'name')) {
                        Schema::table($table, function (Blueprint $table) {
                            $table->string('name')->unique()->after('id');
                        });
                    }
                    if (! Schema::hasColumn($table, 'value')) {
                        Schema::table($table, function (Blueprint $table) {
                            $table->text('value')->nullable()->after('name');
                        });
                    }

                    // Save refreshed token back to settings
                    Setting::updateOrCreate(
                        ['name' => 'adsense_token'],
                        ['value' => json_encode($newToken), 'updated_at' => now()]
                    );

                    $client->setAccessToken($newToken);
                }
            } else {
                // No token found, redirect to Google OAuth
                return redirect($client->createAuthUrl());
            }

            $this->adsenseService     = new GoogleServiceAdSense($client);
            $this->isAdsenseConnected = true;
            Log::info('AdSense service initialized successfully');

        } catch (\Exception $e) {
            Log::error('AdSense initialization error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            $this->isAdsenseConnected = false;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (! Auth::check()) {
            return redirect()->route('admin.login');
        }

        $currentYear    = date('Y');
        $currentMonth   = date('n');
        $selectedYear   = $request->input('year', $currentYear);
        $selectedMonth  = $request->input('month', $currentMonth);
        $availableYears = range($currentYear - 5, $currentYear);

        $user_count = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        })->count();
        $channel_count = Channel::count();
        $post_count    = Post::count();
        $topic_count   = Topic::count();

        $monthlyData = $this->getMonthlyData($selectedYear, $selectedMonth);
        $recentUsers = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        })->latest()->take(5)->get();

        $mostLikedPosts = Post::with(['channel', 'topic'])
            ->orderByDesc('reaction')
            ->take(5)
            ->get()
            ->map(function ($post) {
                $post->publish_date = Carbon::parse($post->publish_date)->diffForHumans();
                return $post;
            });

        $active_user_count = ActiveUserCount::where('date', Carbon::today()->toDateString())
            ->sum('count');

        $mostViewedStories = Story::orderByDesc('story_count')
            ->take(5)
            ->get();

        $data = compact(
            'user_count',
            'channel_count',
            'post_count',
            'topic_count',
            'selectedMonth',
            'availableYears',
            'selectedYear',
            'recentUsers',
            'mostLikedPosts',
            'monthlyData',
            'mostViewedStories',
            'active_user_count'
        );

        return view('admin.Dashboard', $data);
    }
    /**
     * Get Monthly Data for a given date range
     */
    private function getMonthlyData($startDate, $endDate)
    {
        // Ensure startDate and endDate are Carbon instances
        if (is_numeric($startDate) && is_numeric($endDate)) {
            $startDate = Carbon::create($startDate, $endDate, 1)->startOfMonth();
            $endDate   = Carbon::create($startDate, $endDate, 1)->endOfMonth();
        } else {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate   = Carbon::parse($endDate)->endOfDay();
        }

        // Validate date range
        if ($startDate->gt($endDate)) {
            throw new \Exception('Start date cannot be after end date');
        }

        // Fetch top 3 channels by follow_count
        $topChannels = Channel::select('id', 'name', 'follow_count', 'logo as logo')
            ->orderByDesc('follow_count')
            ->take(5)
            ->get()
            ->toArray();

        // Fetch daily counts for other models
        $dailyData = [
            'users'               => User::whereDoesntHave('roles', function ($query) {
                $query->where('name', 'admin');
            })
                ->selectRaw(self::QUERY_SELECT_DATA)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupByRaw(self::DATE_CREATED_AT)
                ->get(),
            'active_users'        => ActiveUserCount::selectRaw('date as date, SUM(count) as count')
                ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                ->groupBy('date')
                ->get(),
            'active_users_hourly' => ActiveUserCount::selectRaw('time as hour, SUM(count) as count')
                ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                ->groupBy('time')
                ->get(),
            'Channels'            => Channel::selectRaw(self::QUERY_SELECT_DATA)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupByRaw(self::DATE_CREATED_AT)
                ->get(),

            'comments'            => Post::selectRaw(self::QUERY_SELECT_DATA)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupByRaw(self::DATE_CREATED_AT)
                ->get(),

            'UserVideoLike'       => Favorite::selectRaw(self::QUERY_SELECT_DATA)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupByRaw(self::DATE_CREATED_AT)
                ->get(),

            'posts'               => Post::selectRaw(self::QUERY_SELECT_DATA)
                ->whereIn('type', ['post', 'audio'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupByRaw(self::DATE_CREATED_AT)
                ->get(),

            'videos'              => Post::selectRaw(self::QUERY_SELECT_DATA)
                ->whereIn('type', ['video', 'youtube'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupByRaw(self::DATE_CREATED_AT)
                ->get(),

            'mostLikedPosts'      => Post::selectRaw('MAX(CAST(reaction AS UNSIGNED)) as max_reactions, DATE(created_at) as date')
                ->whereIn('type', ['audio', 'post'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('reaction')
                ->where('reaction', '!=', '')
                ->groupByRaw(self::DATE_CREATED_AT)
                ->get(),

            'mostLikedVideos'     => Post::selectRaw('MAX(CAST(reaction AS UNSIGNED)) as max_reactions, DATE(created_at) as date')
                ->whereIn('type', ['video', 'youtube'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('reaction')
                ->where('reaction', '!=', '')
                ->groupByRaw(self::DATE_CREATED_AT)
                ->get(),

            'subscriptions'       => Subscription::selectRaw(self::QUERY_SELECT_DATA)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupByRaw(self::DATE_CREATED_AT)
                ->get(),

            'transactions'        => Transaction::selectRaw(self::QUERY_SELECT_DATA)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupByRaw(self::DATE_CREATED_AT)
                ->get(),
        ];

        // Initialize formatted data arrays
        $formattedData = [
            'labels'              => [],
            'users'               => [],
            'active_users'        => [],
            'active_users_hourly' => array_fill(0, 24, 0),
            'Channels'            => [],
            'comments'            => [],
            'UserVideoLike'       => [],
            'posts'               => [],
            'videos'              => [],
            'mostLikedPosts'      => [],
            'mostLikedVideos'     => [],
            'subscriptions'       => [],
            'transactions'        => [],
            'topChannels'         => $topChannels, // Add top channels
        ];

        // Iterate over each day in the date range
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $formattedDate             = $currentDate->format('Y-m-d');
            $formattedData['labels'][] = $currentDate->format('d M');

            $formattedData['users'][]           = $dailyData['users']->firstWhere('date', $formattedDate)->count ?? 0;
            $formattedData['active_users'][]    = $dailyData['active_users']->firstWhere('date', $formattedDate)->count ?? 0;
            $formattedData['Channels'][]        = $dailyData['Channels']->firstWhere('date', $formattedDate)->count ?? 0;
            $formattedData['comments'][]        = $dailyData['comments']->firstWhere('date', $formattedDate)->count ?? 0;
            $formattedData['UserVideoLike'][]   = $dailyData['UserVideoLike']->firstWhere('date', $formattedDate)->count ?? 0;
            $formattedData['posts'][]           = $dailyData['posts']->firstWhere('date', $formattedDate)->count ?? 0;
            $formattedData['videos'][]          = $dailyData['videos']->firstWhere('date', $formattedDate)->count ?? 0;
            $formattedData['mostLikedPosts'][]  = $dailyData['mostLikedPosts']->firstWhere('date', $formattedDate)->max_reactions ?? 0;
            $formattedData['mostLikedVideos'][] = $dailyData['mostLikedVideos']->firstWhere('date', $formattedDate)->max_reactions ?? 0;
            $formattedData['subscriptions'][]   = $dailyData['subscriptions']->firstWhere('date', $formattedDate)->count ?? 0;
            $formattedData['transactions'][]    = $dailyData['transactions']->firstWhere('date', $formattedDate)->count ?? 0;

            $currentDate->addDay();
        }

        foreach ($dailyData['active_users_hourly'] as $hourlyData) {
            $hour                                        = $hourlyData->hour;
            $formattedData['active_users_hourly'][$hour] = $hourlyData->count ?? 0;
        }

        return $formattedData;
    }

    /**
     * Get Date Range Data for AJAX request
     */
    public function getMonthYearData(Request $request)
    {
        try {
            $startDate = $request->query('start');
            $endDate   = $request->query('end');

            if (! $startDate || ! $endDate) {
                return response()->json([
                    'error'               => true,
                    'message'             => 'Start and end dates are required',
                    'labels'              => [],
                    'posts'               => [],
                    'videos'              => [],
                    'mostLikedPosts'      => [],
                    'mostLikedVideos'     => [],
                    'topChannels'         => [],
                    'subscriptions'       => [],
                    'transactions'        => [],
                    'active_users'        => [],
                    'active_users_hourly' => array_fill(0, 24, 0),
                    'adsense'             => [],
                ], 400);
            }

            try {
                $startDate = Carbon::parse($startDate);
                $endDate   = Carbon::parse($endDate);
            } catch (\Exception $e) {
                return response()->json([
                    'error'               => true,
                    'message'             => 'Invalid date format',
                    'labels'              => [],
                    'posts'               => [],
                    'videos'              => [],
                    'mostLikedPosts'      => [],
                    'mostLikedVideos'     => [],
                    'topChannels'         => [],
                    'subscriptions'       => [],
                    'transactions'        => [],
                    'active_users'        => [],
                    'active_users_hourly' => array_fill(0, 24, 0),
                    'adsense'             => [],
                ], 400);
            }

            $monthlyData = $this->getMonthlyData($startDate, $endDate);
            $adsenseData = $this->getAdsenseData($startDate, $endDate);

            return response()->json([
                'labels'              => $monthlyData['labels'],
                'posts'               => $monthlyData['posts'],
                'videos'              => $monthlyData['videos'],
                'mostLikedPosts'      => $monthlyData['mostLikedPosts'],
                'mostLikedVideos'     => $monthlyData['mostLikedVideos'],
                'topChannels'         => $monthlyData['topChannels'],
                'subscriptions'       => $monthlyData['subscriptions'],
                'transactions'        => $monthlyData['transactions'],
                'active_users'        => $monthlyData['active_users'],
                'active_users_hourly' => $monthlyData['active_users_hourly'],
                'adsense'             => $adsenseData,

            ]);
        } catch (Throwable $th) {
            Log::error('Error fetching chart data: ' . $th->getMessage());
            return response()->json([
                'error'               => true,
                'message'             => 'Error fetching chart data',
                'labels'              => [],
                'posts'               => [],
                'videos'              => [],
                'mostLikedPosts'      => [],
                'mostLikedVideos'     => [],
                'topChannels'         => [],
                'subscriptions'       => [],
                'transactions'        => [],
                'active_users'        => [],
                'active_users_hourly' => array_fill(0, 24, 0),
                'adsense'             => $this->getEmptyAdsenseData(),
            ], 500);
        }
    }

    /**
     * Get Empty AdSense Data structure
     */
    private function getEmptyAdsenseData()
    {
        return [
            'labels'       => [],
            'impressions'  => [],
            'clicks'       => [],
            'earnings'     => [],
            'ctr'          => [],
            'is_demo'      => false,
            'is_connected' => false,
        ];
    }

    /**
     * Get Real AdSense Data or Empty Data
     */
    private function getAdsenseData(Carbon $startDate, Carbon $endDate)
    {
        // If AdSense is not connected or service is not available, return empty data
        if (! $this->isAdsenseConnected || ! $this->adsenseService) {
            Log::info('AdSense not connected, returning empty data');
            return $this->getEmptyAdsenseData();
        }

        try {
            $data = $this->getRealAdsenseData($startDate, $endDate);
            Log::info('Fetched real AdSense data successfully');
            return $data;
        } catch (\Exception $e) {
            Log::error('AdSense API Error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            // Return empty data instead of throwing error
            return $this->getEmptyAdsenseData();
        }
    }
    /**
     * Get Real Google AdSense Data
     */
    private function getRealAdsenseData(Carbon $startDate, Carbon $endDate)
    {
        $labels      = [];
        $impressions = [];
        $clicks      = [];
        $earnings    = [];
        $ctr         = [];

        try {
            // Get accounts
            $accountsList = $this->adsenseService->accounts->listAccounts();
            $accounts     = $accountsList->getAccounts();

            if (empty($accounts)) {
                throw new \Exception('No AdSense accounts found');
            }

            $accountName = $accounts[0]->getName();
            Log::info('Using AdSense account: ' . $accountName);

            // Create date objects
            $startDateObj = new \Google\Service\AdSense\Date();
            $startDateObj->setYear((int) $startDate->format('Y'));
            $startDateObj->setMonth((int) $startDate->format('m'));
            $startDateObj->setDay((int) $startDate->format('d'));

            $endDateObj = new \Google\Service\AdSense\Date();
            $endDateObj->setYear((int) $endDate->format('Y'));
            $endDateObj->setMonth((int) $endDate->format('m'));
            $endDateObj->setDay((int) $endDate->format('d'));

            // Generate report
            $report = $this->adsenseService->accounts_reports->generate($accountName, [
                'dateRange'  => 'CUSTOM',
                'startDate'  => $startDateObj,
                'endDate'    => $endDateObj,
                'dimensions' => ['DATE'],
                'metrics'    => ['IMPRESSIONS', 'CLICKS', 'ESTIMATED_EARNINGS'],
            ]);

            // Process results
            $rows = $report->getRows();
            if ($rows) {
                foreach ($rows as $row) {
                    $dimensionValues = $row->getDimensionValues();
                    $metricValues    = $row->getMetricValues();

                    $dateStr  = $dimensionValues[0]->getValue();
                    $date     = Carbon::createFromFormat('Ymd', $dateStr);
                    $labels[] = $date->format('d M');

                    $imp  = (int) $metricValues[0]->getValue();
                    $clk  = (int) $metricValues[1]->getValue();
                    $earn = (float) $metricValues[2]->getValue();

                    $impressions[] = $imp;
                    $clicks[]      = $clk;
                    $earnings[]    = round($earn, 2);
                    $ctr[]         = $imp > 0 ? round(($clk / $imp) * 100, 2) : 0;
                }
            }

            Log::info('Successfully fetched AdSense data for ' . count($labels) . ' days');

            return [
                'labels'       => $labels,
                'impressions'  => $impressions,
                'clicks'       => $clicks,
                'earnings'     => $earnings,
                'ctr'          => $ctr,
                'is_demo'      => false,
                'is_connected' => true,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getRealAdsenseData: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * OAuth Callback for Google AdSense
     */
    public function adsenseCallback(Request $request)
    {
        try {
            // Get AdSense credentials from settings
            $clientId     = Setting::where('name', 'adsense_client_id')->value('value');
            $clientSecret = Setting::where('name', 'adsense_client_secret')->value('value');
            $redirectUri  = Setting::where('name', 'adsense_redirect_uri')->value('value');
            $tokenData    = Setting::where('name', 'adsense_token')->value('value'); // stored as JSON

            if (! $clientId || ! $clientSecret || ! $redirectUri) {
                return redirect()->route('dashboard')
                    ->with('error', 'Google AdSense credentials are not configured');
            }

            $client = new GoogleClient();
            $client->setClientId($clientId);
            $client->setClientSecret($clientSecret);
            $client->setRedirectUri($redirectUri);
            $client->setScopes(['https://www.googleapis.com/auth/adsense.readonly']);
            $client->setAccessType('offline');
            $client->setPrompt('select_account consent');

            // If token already exists, set it
            if ($tokenData) {
                $token = json_decode($tokenData, true);
                $client->setAccessToken($token);
            }

            // Handle OAuth callback
            if ($request->has('code')) {
                $token = $client->fetchAccessTokenWithAuthCode($request->code);

                if (isset($token['error'])) {
                    throw new \Exception($token['error_description'] ?? $token['error']);
                }

                // Ensure columns exist before saving token
                $table = (new Setting)->getTable();
                if (! Schema::hasColumn($table, 'name')) {
                    Schema::table($table, function (Blueprint $table) {
                        $table->string('name')->unique()->after('id');
                    });
                }
                if (! Schema::hasColumn($table, 'value')) {
                    Schema::table($table, function (Blueprint $table) {
                        $table->text('value')->nullable()->after('name');
                    });
                }

                // Save token in settings
                Setting::updateOrCreate(
                    ['name' => 'adsense_token'],
                    ['value' => json_encode($token), 'updated_at' => now()]
                );

                Log::info('AdSense token saved successfully');

                return redirect()->route('dashboard')
                    ->with('success', 'Google AdSense connected successfully!');
            }

            // If no code, redirect to Google OAuth
            $authUrl = $client->createAuthUrl();
            Log::info('Redirecting to Google OAuth');
            return redirect($authUrl);

        } catch (\Exception $e) {
            Log::error('AdSense OAuth Error: ' . $e->getMessage());
            return redirect()->route('dashboard')
                ->with('error', 'Failed to connect Google AdSense: ' . $e->getMessage());
        }
    }

    public function create()
    {}
    public function store(Request $request)
    {}
    public function show(string $id)
    {}
    public function edit(string $id)
    {}
    public function update(Request $request, string $id)
    {}
    public function destroy(string $id)
    {}

    public function changePassword()
    {
        return view('admin.models.change-password');
    }

    public function changePasswordUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password'     => 'required',
            'new_password'     => 'required|min:8',
            'confirm_password' => 'required|same:new_password',
        ]);
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $user_id = Auth::user()->id;
            $user    = User::find($user_id);
            if (! Hash::check($request->old_password, Auth::user()->password)) {
                ResponseService::errorResponse("Incorrect old password, please try again.");
            }
            $user->password = Hash::make($request->confirm_password);
            $user->update();

            // Logout user after password change
            Auth::logout();
            return response()->json([
                'status'   => 'success',
                'message'  => 'Password changed successfully. Please login again.',
                'redirect' => route('admin.login'),
            ]);
        } catch (Throwable $th) {
            ResponseService::logErrorResponse($th, "DashboardController --> changePasswordUpdate");
            ResponseService::errorResponse();
        }
    }

    public function changeProfile()
    {
        return view('admin.models.change-profile');
    }

    public function changeProfileUpdate(Request $request)
    {
        try {
            $user_id = Auth::user()->id;
            $user    = User::find($user_id);

            $user->name  = $request->name;
            $user->email = $request->email;
            if (isset($request->password)) {
                $password       = Hash::make($request->password);
                $user->password = $password;
            }

            if ($request->hasFile('profile')) {
                if ($user->profile && Storage::exists('public/' . $user->profile)) {
                    Storage::delete('public/' . $user->profile);
                }

                $logoPath      = $request->file('profile')->store('profile_images', 'public');
                $user->profile = $logoPath;
            }
            $user->update();

            // return response()->json(['error' => false, 'message' => "Profile Updated Successfully"]);
            return response()->json([
                'status'   => 'success',
                'message'  => 'Profile Updated Successfully',
                'redirect' => url('/admin/dashboard'),
            ]);
        } catch (Throwable $th) {
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function permissionRestricted(Request $request)
    {
        return view('admin.errors.permission_restricted');
    }

}
