<?php
namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReportReasonTypeApiController extends Controller
{
    /**
     * Get all report reason types
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $reportTypes = DB::table('report_types')
                ->select('id', 'title')
                ->orderBy('created_at', 'desc')
                ->get();

            $reportTypes->push([
                'id'    => 0,
                'title' => 'Other',
            ]);

            return response()->json([
                'error'   => false,
                'message' => 'Report reason types fetched successfully.',
                'data'    => $reportTypes,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error'   => true,
                'message' => 'Something went wrong while fetching report reason types.',
                'data'    => $e->getMessage(),
            ], 500);
        }
    }

    public function getWebType(): JsonResponse
    {
        try {
            $reportTypes = DB::table('report_types')
                ->select('id', 'title')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'error'   => false,
                'message' => 'Report reason types fetched successfully.',
                'data'    => $reportTypes,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error'   => true,
                'message' => 'Something went wrong while fetching report reason types.',
                'data'    => $e->getMessage(),
            ], 500);
        }
    }
}
