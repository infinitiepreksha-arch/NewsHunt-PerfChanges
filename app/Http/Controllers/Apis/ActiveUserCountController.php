<?php
namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\ActiveUserCount;
use Illuminate\Http\Request;

class ActiveUserCountController extends Controller
{
    public function store(Request $request)
    {
        if (! $request->has('count')) {
            return response()->json([
                'error'   => true,
                'message' => 'The count field is required.',
                'data'    => [],
            ], 400);
        }

        $request->validate([
            'count' => 'integer|min:0',
        ]);

        $record = ActiveUserCount::create([
            'date'  => now()->toDateString(),
            'time'  => now()->toTimeString(),
            'count' => $request->count,
        ]);

        return response()->json([
            'error'   => false,
            'message' => 'Active user count stored successfully',
            'data'    => $record,
        ]);
    }

    // Get records (optional)
    public function index()
    {
        $records = ActiveUserCount::orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->get();

        return response()->json([
            'error'   => false,
            'message' => 'Active user count retrieved successfully',
            'data'    => $records,
        ]);
    }
}
