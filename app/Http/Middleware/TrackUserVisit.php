<?php
namespace App\Http\Middleware;

use App\Models\ActiveUserCount;
use Closure;

class TrackUserVisit
{
    public function handle($request, Closure $next)
    {
        // Only count once per session
        if (! $request->session()->has('has_counted_visit')) {
            $timeSlot = now()->format('H:i:00');

            $existing = ActiveUserCount::where('date', now()->toDateString())
                ->where('time', $timeSlot)
                ->first();

            if ($existing) {
                $existing->increment('count');
            } else {
                ActiveUserCount::create([
                    'date'  => now()->toDateString(),
                    'time'  => $timeSlot,
                    'count' => 1,
                ]);
            }

            // Mark this session as already counted
            $request->session()->put('has_counted_visit', true);
        }

        return $next($request);
    }
}
