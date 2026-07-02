<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Reactable;
use App\Services\ResponseService;
use DevDojo\LaravelReactions\Models\Reaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReactionsController extends Controller
{
    public function getReaction()
    {
        $perPage = request()->get('per_page', 10);

        try {
            // Fetch the reactions with pagination
            $reactionType = Reaction::select('id', 'uuid', 'name')->paginate($perPage);

            return response()->json([
                'error' => false,
                'message' => 'Fetched Reactions successfully.',
                'data' => $reactionType->items(),
            ]);
        } catch (\Exception $e) {
            ResponseService::logErrorResponse($e, 'Fail to get reactions ' . $e->getMessage());
            return response()->json([
                'error' => 'Unable to fetch reactions at this time. Please try again later.'
            ], 500);
        }
    }

    public function react($type, $slug)
    {
        $user = Auth::user();
        $post = Post::where('slug', $slug)->first(); // Fixed typo here
        $reactionType = Reaction::where('name', $type)->first();

        $getCurrentReactor = Reactable::where('reactable_id', $post->id)->where('responder_id', $user->id)->first();
        $countIncrement = 0;
        if (isset($getCurrentReactor)) {
            $countIncrement = isset($getCurrentReactor->responder_id) ? $getCurrentReactor->responder_id : 0;
        }

        $isNew = 0;
        if ($countIncrement == 0) {
            $post->increment('reaction');
            $isNew = 1;
        }

        $userHasReacted = $post->reactions()->where('responder_id', $user->id)->first();
        $post->user_has_reacted = isset($userHasReacted) ? true : false;
        $post->emoji_type = isset($userHasReacted) ? $userHasReacted->name : "";

        $getReactCountsData = $post->getReactionsSummary();

        $post->reaction_list = $getReactCountsData->sortByDesc(function ($reaction) {
            $reactionEmoji = Reaction::where('name', $reaction->name)->first();
            $reaction->uuid = $reactionEmoji->uuid;
            return $reaction->count;
        })->values();
        
        $remove = "";
        $reactions = $post->reactions;
        foreach ($reactions as $reaction) {
            $reactorId = $reaction->getResponder();
            if (isset($reactorId) && $reactorId->id == $user->id) {
                if ($reaction->name === $type) {
                    $post->decrement('reaction');
                    $remove = 'Removed Successfully';
                } else {
                    $remove = 'Updated Successfully';
                }
            }
        }

        $user->reactTo($post, $reactionType);

        $shortReations = $post->getReactionsSummary()->toArray();
        usort($shortReations, function ($a, $b) {
            return $b['count'] - $a['count']; // Compare count in descending order
        });
        $data = [
            'count' => $post->reaction,
            'reactions' => $shortReations,
            'user_has_reacted' => isset($userHasReacted) ? false : true

        ];

        return response()->json([
            'error' => false,
            'message' => $isNew == 1 ? 'Reacted successfully.' : $remove,
            'data' => $data
        ]);
    }

    public function getReactors($type, $slug)
    {
        $defaultProfileImage = url('public/front_end/classic/images/default/profile-avatar.jpg');
        // Get the post by slug
        $post = Post::where('slug', $slug)->first();

        if (!$post) {
            return response()->json([
                'error' => true,
                'message' => 'Post not found'
            ]);
        }
        // Pagination setup
        $perPage = request()->get('per_page', 10);

        // Query setup
        $reactors = Reactable::select(
            'reactables.id',
            'reactions.name as reaction_name',
            'users.profile',
            'users.name as user_name'
        )
            ->where('reactable_id', $post->id);

        if ($type !== 'all') {
            $reactors->where('reactions.name', $type);
        }

        $reactors->join('reactions', 'reactables.reaction_id', '=', 'reactions.id')
            ->join('users', 'reactables.responder_id', '=', 'users.id');

        $reactors = $reactors->paginate($perPage);

        $reactors = $reactors->items();

        foreach ($reactors as &$reactor) {
            $reactor['profile'] = $reactor['profile'] ? url('storage/' . $reactor['profile']) : $defaultProfileImage;
        }

        return response()->json([
            'error' => false,
            'message' => 'Reactors fetched successfully',
            'data' => $reactors
        ]);
    }
}
