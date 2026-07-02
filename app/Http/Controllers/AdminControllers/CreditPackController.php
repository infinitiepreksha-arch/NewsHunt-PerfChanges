<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\CreditPack;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class CreditPackController extends Controller
{
    /**
     * Display a listing of credit packs.
     */

    public function index(Request $request)
    {
        ResponseService::noAnyPermissionThenRedirect(['credit-packs-settings']);

        if ($request->ajax()) {
            $creditPacks = CreditPack::select([
                'id', 'name', 'product_id', 'credits', 'price', 'savings_percent',
                'tagline', 'is_popular', 'is_best_value',
            ]);

            return DataTables::of($creditPacks)
                ->editColumn('is_popular', function ($row) {
                    return $row->is_popular
                        ? '<span class="badge bg-success text-white">' . __('global.YES') . '</span>'
                        : '<span class="badge bg-danger text-white">' . __('global.NO') . '</span>';
                })
                ->editColumn('is_best_value', function ($row) {
                    return $row->is_best_value
                        ? '<span class="badge bg-success text-white">' . __('global.YES') . '</span>'
                        : '<span class="badge bg-danger text-white">' . __('global.NO') . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
        <a class="text-center delete_btn cursor-pointer"
                data-id="' . $row->id . '"
                title="Delete">
            <i class="fas fa-trash text-danger"></i>
        </a>
    ';
                })
                ->rawColumns(['action', 'is_popular', 'is_best_value'])
                ->make(true);
        }

        return view('admin.credit_packs.index');
    }

    /**
     * Store a newly created credit pack in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'            => 'required|string|max:255',
            'product_id'      => 'required|string|max:255|unique:credit_packs,product_id',
            'credits'         => 'required|integer|min:0',
            'price'           => 'required|numeric|min:0',
            'savings_percent' => 'nullable|integer|min:0|max:100',
            'tagline'         => 'required|string|max:46',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        CreditPack::create([
            'name'            => $request->name,
            'product_id'      => $request->product_id,
            'credits'         => $request->credits,
            'price'           => $request->price,
            'savings_percent' => $request->savings_percent ?? 0,
            'tagline'         => $request->tagline,
            'is_popular'      => $request->has('is_popular'),
            'is_best_value'   => $request->has('is_best_value'),
        ]);

        return response()->json([
            'success'  => 'Credit Pack created successfully.',
            'redirect' => route('credit-packs.index'),
        ]);
    }

    /**
     * Remove the specified credit pack from storage.
     */

    public function destroy(CreditPack $creditPack)
    {
        $creditPack->delete();
        return response()->json(['success' => 'Credit Pack deleted successfully.']);
    }

}
