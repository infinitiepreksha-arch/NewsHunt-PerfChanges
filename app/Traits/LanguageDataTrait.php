<?php
namespace App\Traits;

use App\Models\Channel;
use App\Models\Topic;
use Illuminate\Http\Request;

trait LanguageDataTrait
{
    public function getChannelsByLanguage(Request $request)
    {
        $channels = Channel::where('status', 'active')
            ->where('news_language_id', $request->news_language_id)
            ->select('id', 'name')
            ->get();

        return response()->json(['channels' => $channels]);
    }

    public function getTopicsByLanguage(Request $request)
    {
        $topics = Topic::where('status', 'active')
            ->where('news_language_id', $request->news_language_id)
            ->select('id', 'name')
            ->get();

        return response()->json(['topics' => $topics]);
    }
}
