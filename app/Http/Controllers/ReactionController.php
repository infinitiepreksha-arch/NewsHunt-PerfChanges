<?php
namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Reactable;
use DevDojo\LaravelReactions\Models\Reaction;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
    public function react(Request $request, $postId)
    {
        $post               = Post::find($postId);
        $user               = auth()->user();
        $userId             = $user->id ?? 0;
        $type               = $request->input('type');
        $reactionType       = Reaction::where('name', $type)->first();
        $reactions          = Reactable::where('reactable_id', $post->id)->where('responder_id', $user->id)->first();
        $reactorId          = $reactions->responder_id ?? 0;
        $isNew              = 0;
        $remove_user_review = 0;

        if (isset($reactions->reaction_id) && isset($reactionType->id) && $reactions->reaction_id == $reactionType->id) {
            $remove_user_review = 1;
        }

        if ($reactorId == 0) {
            $post->increment('reaction');
            $isNew = 1;
        }

        $reactions = $post->reactions;
        foreach ($reactions as $reaction) {
            $reactorId = $reaction->getResponder();
            if ($reactorId->id == $userId) {
                if ($reaction->name === $type) {
                    $post->decrement('reaction');
                    $remove = false;
                } else {
                    $remove = true;
                }
            }
        }
        $postReactionCount = $post->reaction;
        $user->reactTo($post, $reactionType);

        return response()->json([
            'message'            => __('frontend-labels.reactions.added_success'),
            'isNew'              => $isNew,
            'count'              => $postReactionCount,
            'reactors'           => $this->getreactData($postId),
            'isRemove'           => $remove ?? '',
            'remove_user_review' => $remove_user_review,
        ]);
    }

    // In your ReactionController
    public function getReactions($postId)
    {
        $post      = Post::findOrFail($postId);
        $reactions = $post->reactions()->with('user')->paginate(10);

        return response()->json([
            'users'         => $reactions->items(),
            'next_page_url' => $reactions->nextPageUrl(),
        ]);
    }

    /* Ajex response for get reactor users for spacific post */
    public function getreactData($post_id)
    {
        $post = Post::find($post_id);

        if (! $post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        $getReactCountsData = collect($post->getReactionsSummary());

        $getReactCounts = $getReactCountsData->sortByDesc(function ($reaction) {
            return $reaction->count;
        });

        $reactionNames = $getReactCounts->pluck('name')->toArray();
        $reactions     = Reaction::whereIn('name', $reactionNames)->get()->keyBy('name');

        $reactionUsers = [];

        foreach ($getReactCounts as $getReractCount) {
            $reaction             = $reactions->get($getReractCount->name);
            $getReractCount->uuid = $reaction ? $reaction->uuid : null;

            $reactionUsers[$getReractCount->name] = [];

            foreach ($post->reactions as $reactor) {
                $userDetails  = $reactor->getResponder();
                $reactionName = $reactor->name;

                if ($getReractCount->name === $reactionName) {
                    $reactionUsers[$getReractCount->name][] = $userDetails->toArray();
                }
            }

            $getReractCount->users = $reactionUsers[$getReractCount->name];
        }

        $reactionData = $getReactCounts->map(function ($reaction) {
            return [
                'name'  => $reaction->name,
                'count' => $reaction->count,
                'uuid'  => $reaction->uuid,
                'users' => $reaction->users,
            ];
        });

        // Convert the map result into a numerically indexed array
        return response()->json(array_values($reactionData->toArray()));
    }
}
