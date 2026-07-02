<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Support\Carbon;

class DailyReadStat extends Model
{

    use HasFactory;

    protected $table = 'daily_read_stats';

    // No auto-increment ID (composite key)
    public $incrementing = false;

    /**
     * This table uses a composite key (user_id, date) and has no `id` column.
     */
    protected $primaryKey = null;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'date',
        'article_read_count',
        'story_read_count',
        'epaper_read_count',
        'total_article_read_count',
        'total_story_read_count',
        'total_epaper_read_count',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Relationship: belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Use composite key (user_id, date) for updates/deletes.
     */
    protected function setKeysForSaveQuery($query)
    {
        $query->where('user_id', $this->getAttribute('user_id'))
            ->where('date', $this->getAttribute('date'));

        return $query;
    }

    /**
     * Create or fetch today's stats row for a user and sync totals.
     *
     * $totals keys: article, story, epaper (ints; use -1 for unlimited)
     */
    public static function forUserToday(int $userId, array $totals): self
    {
        $today = Carbon::today()->toDateString();

        // Check if today's record exists
        $stat = self::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        if (!$stat) {
            // Delete old records for this user to keep the table clean and avoid limit confusion
            self::where('user_id', $userId)->where('date', '<', $today)->delete();

            $stat = self::create([
                'user_id'                  => $userId,
                'date'                     => $today,
                'article_read_count'       => 0,
                'story_read_count'         => 0,
                'epaper_read_count'        => 0,
                'total_article_read_count' => (int) ($totals['article'] ?? 0),
                'total_story_read_count'   => (int) ($totals['story'] ?? 0),
                'total_epaper_read_count'  => (int) ($totals['epaper'] ?? 0),
            ]);
        }

        $stat->syncTotals($totals);
        $stat->refresh();

        return $stat;
    }

    /**
     * Sync totals to current settings (or unlimited).
     */
    public function syncTotals(array $totals): void
    {
        $this->update([
            'total_article_read_count' => (int) ($totals['article'] ?? $this->total_article_read_count ?? 0),
            'total_story_read_count'   => (int) ($totals['story'] ?? $this->total_story_read_count ?? 0),
            'total_epaper_read_count'  => (int) ($totals['epaper'] ?? $this->total_epaper_read_count ?? 0),
        ]);
    }

    public function setUnlimitedTotals(): void
    {
        $this->update([
            'total_article_read_count' => -1,
            'total_story_read_count'   => -1,
            'total_epaper_read_count'  => -1,
        ]);
    }

    /**
     * Increment a counter if within limit.
     *
     * $type: article|story|epaper
     * Returns true if access is allowed (incremented or unlimited), false if limit reached.
     */
    public function allowAndIncrement(string $type): bool
    {
        $type = strtolower($type);

        $map = [
            'article' => ['count' => 'article_read_count', 'total' => 'total_article_read_count'],
            'story'   => ['count' => 'story_read_count', 'total' => 'total_story_read_count'],
            'epaper'  => ['count' => 'epaper_read_count', 'total' => 'total_epaper_read_count'],
        ];

        if (! isset($map[$type])) {
            return true; // unknown type => don't block
        }

        $countField = $map[$type]['count'];
        $totalField = $map[$type]['total'];

        $limit = (int) ($this->{$totalField} ?? 0);
        if ($limit === -1) {
            return true; // unlimited
        }

        $current = (int) ($this->{$countField} ?? 0);
        if ($current >= $limit) {
            return false;
        }

        // Explicitly target today's date to avoid updating old records
        $today = Carbon::today()->toDateString();
        self::where('user_id', $this->user_id)
            ->where('date', $today)
            ->increment($countField);

        // keep instance in sync for later checks
        $this->refresh();

        return true;
    }
}
