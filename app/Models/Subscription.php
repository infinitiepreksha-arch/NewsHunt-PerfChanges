<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscription';

    protected $fillable = [
        'user_id',
        'plan_id',
        'feature_id',
        'plan_tenure_id',
        'duration',
        'status',
        'start_date',
        'end_date',
        'article_count',
        'transaction_id',
        'story_count',
        'e_paper_count',
    ];

    protected $casts = [
        'duration'   => 'integer',
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    /**
     * Get the user that owns the subscription.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the plan associated with the subscription.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the feature associated with the subscription.
     */
    public function feature()
    {
        return $this->belongsTo(Feature::class, 'feature_id');
    }

    /**
     * Get the transactions for the subscription.
     */
    public function transactions()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');

    }

    /**
     * Get the plan tenure associated with the subscription.
     */
    public function planTenure()
    {
        return $this->belongsTo(PlanTenure::class);
    }

    /**
     * Scope a query to only include the current active subscription.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrentActive($query)
    {
        $today = now();
        return $query->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->where('status', '=', 'active');
    }

    /**
     * Increment the article_count and story_count by the given values.
     *
     * @param int $articleIncrement
     * @param int $storyIncrement
     * @return void
     */
    public function incrementCounts($articleIncrement = 1, $storyIncrement = 1, $ePaperIncrement = 1)
    {
        $this->increment('story_count', $storyIncrement);
        $this->increment('article_count', $articleIncrement);
        $this->increment('e_paper_count', $ePaperIncrement);
    }

    /**
     * Increment the article_count and story_count by the given values with validation.
     *
     * @param int $articleIncrement
     * @param int $storyIncrement
     * @throws \Exception
     * @return void
     */

    /**
     * Increment the article_count with validation.
     *
     * @param int $articleIncrement
     * @throws \Exception
     * @return void
     */
    public function incrementArticleCountWithValidation($articleIncrement = 1)
    {
        if ($this->hasReachedPostLimits()) {
            throw new \Exception('Maximum limit reached for articles.');
        }

        $this->increment('article_count', $articleIncrement);
    }

    /**
     * Increment the e_paper_count with validation.
     *
     * @param int $ePaperIncrement
     * @throws \Exception
     * @return void
     */
    public function incrementEPaperCountWithValidation($ePaperIncrement = 1)
    {
        if ($this->hasReachedEPaperLimits()) {
            throw new \Exception('Maximum limit reached for e-papers and magazines.');
        }

        $this->increment('e_paper_count', $ePaperIncrement);
    }

    /**
     * Increment the story_count with validation.
     *
     * @param int $storyIncrement
     * @throws \Exception
     * @return void
     */
    public function incrementStoryCountWithValidation($storyIncrement = 1)
    {
        if ($this->hasReachedStoryLimits()) {
            throw new \Exception('Maximum limit reached for stories.');
        }

        $this->increment('story_count', $storyIncrement);
    }
    public function hasReachedPostLimits()
    {
        $transaction = $this->transactions;

        if (! $transaction) {
            return false; // No transaction, no limit to check
        }

        $planDetails = $transaction->plan_details;

        $maxArticles = $planDetails['features'][0]['number_of_articles'];

        return $this->article_count >= $maxArticles;
    }
    public function hasReachedStoryLimits()
    {
        $transaction = $this->transactions;

        if (! $transaction) {
            return false; // No transaction, no limit to check
        }

        $planDetails = $transaction->plan_details;

        $maxStories = $planDetails['features'][0]['number_of_stories'];

        return $this->story_count >= $maxStories;
    }

    public function hasReachedEPaperLimits()
    {
        $transaction = $this->transactions;

        if (! $transaction) {
            return false; // No transaction, no limit to check
        }

        $planDetails = $transaction->plan_details;

        $maxEPapers = $planDetails['features'][0]['number_of_e_papers_and_magazines'] ?? null;

        if ($maxEPapers === null) {
            return false; // No limit set
        }

        return $this->e_paper_count >= $maxEPapers;
    }
}
