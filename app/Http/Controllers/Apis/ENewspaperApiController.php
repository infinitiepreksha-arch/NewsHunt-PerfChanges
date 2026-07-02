<?php
namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\ENewspaper;
use App\Models\NewsLanguage;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ENewspaperApiController extends Controller
{
    public function getenewspaper(Request $request)
    {
        $newsLanguageId = $request->news_language_id;
        $perPage        = $request->get('per_page', 10);

        $query = ENewspaper::with(['channel:id,name,slug,logo', 'newsLanguage:id,name'])
            ->whereHas('channel', function ($q) {
                $q->where('status', 'active');
            })
            ->whereHas('topic', function ($q) {
                $q->where('status', 'active');
            })
            ->orderBy('date', 'desc');

        if ($newsLanguageId) {
            $query->where('news_language_id', $newsLanguageId);
        }

        $paginated = $query->paginate($perPage);

        $eNewspapers = $paginated->getCollection()->map(function ($item) {
            return [
                'id'               => $item->id,
                'news_language_id' => $item->news_language_id,
                'date'             => $item->date ? Carbon::parse($item->date)->diffForHumans() : null,
                'type'             => $item->type,
                'pdf_url'          => $item->pdf_path ? asset('storage/' . $item->pdf_path) : null,
                'thumbnail_url'    => $item->thumbnail ? asset('storage/' . $item->thumbnail) : null,
                'channel_name'     => $item->channel->name ?? null,
                'channel_slug'     => $item->channel->slug ?? null,
                'channel_logo'     => $item->channel->logo ? asset('storage/images/' . $item->channel->logo) : null,
                'language_name'    => $item->newsLanguage->name ?? null,
                'added_by_name'    => $item->added_by_name ? $item->added_by_name : null,
                'calendar_date'    => $item->date ? Carbon::parse($item->date)->format('d-m-Y') : null,
            ];
        })->values();

        return response()->json([

            'error'   => false,
            'message' => "Data retrieved successfully!!",
            'data'    => $eNewspapers,
        ]);
    }
}
