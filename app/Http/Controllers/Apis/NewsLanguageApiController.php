<?php
namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\NewsLanguage;
use App\Models\NewsLanguageStatus;
use App\Models\UserFcm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NewsLanguageApiController extends Controller
{
    //<><><><><><> GET ALL NEWS LANGUAGES  <><><><><><>

    public function getNewsLanguages(Request $request)
    {
        $latestStatus = NewsLanguageStatus::latest('created_at')->first();
        if ($latestStatus && $latestStatus->status === 'active') {
            // Automatically get user ID from auth
            $userId = Auth::id();

            $followedLanguageIds = DB::table('news_languages_subscribers')
                ->where('user_id', $userId)
                ->pluck('news_language_id')
                ->toArray();

            $defaultLanguage = NewsLanguage::where('is_active', 1)->first();

            $news_languages = NewsLanguage::where('status', 'active')->get();
            $news_languages = $news_languages->map(function ($lang) use ($followedLanguageIds, $defaultLanguage) {
                $lang->image       = $lang->image ? url('storage/' . $lang->image) : null;
                $lang->is_selected = in_array((int) $lang->id, array_map('intval', $followedLanguageIds)) || ($defaultLanguage && $lang->id == $defaultLanguage->id);
                $attributes        = $lang->getAttributes();
                $lang->setRawAttributes($attributes);
                $lang->syncOriginal();
                return $lang;
            });

            return response()->json([
                'error'   => false,
                'data'    => $news_languages,
                'message' => 'News languages retrieved successfully',
            ]);
        }
        return response()->json([
            'error'            => true,
            'data'             => [],
            'default_language' => null,
            'message'          => 'No active news language status found because the news language status is inactive',
        ]);
    }

    public function getPostsByNewsLanguage(Request $request)
    {
        $fcmId          = $request->fcmId;
        $newsLanguageId = $request->news_language_id;

        // Validate input
        if (! $fcmId) {
            return response()->json([
                'error'   => true,
                'message' => 'fcm_id is required',
                'data'    => [],
            ], 422);
        } elseif (! $newsLanguageId) {
            return response()->json([
                'error'   => true,
                'message' => 'news_language_id is required',
                'data'    => [],
            ], 422);
        }

        // Check language exists
        $language = NewsLanguage::find($newsLanguageId);
        if (! $language) {
            return response()->json([
                'error'   => true,
                'message' => 'Invalid news language ID',
                'data'    => [],
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | STORE / UPDATE USER FCM
        |--------------------------------------------------------------------------
        */

        $userFcm = UserFcm::where('fcm_id', $fcmId)->first();

        if ($userFcm) {
            $userFcm->update([
                'news_language_id' => $newsLanguageId,
            ]);
        } else {
            $userFcm = UserFcm::create([
                'fcm_id'           => $fcmId,
                'news_language_id' => $newsLanguageId,
            ]);
        }

        return response()->json([
            'error'   => false,
            'message' => 'News language saved successfully',
            'data'    => [
                'fcm_id'           => $fcmId,
                'news_language_id' => $newsLanguageId,
            ],
        ]);
    }
}
