<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\Plan;
use App\Models\PlanTenure;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PricingPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        ResponseService::noAnyPermissionThenRedirect(['list-plan', 'create-plan', 'update-plan', 'delete-plan']);

        $title = __('page.MEMBERSHIP_PLANS');
        $plans = Plan::with(['features_plan', 'planTenures'])
            ->withExists(['subscriptions as is_active' => function ($query) {
                $query->where('status', '!=', 'expired')
                    ->where('end_date', '>=', now()->toDateString());
            }])
            ->get();

        return view('admin.membership_plan.index', compact('plans', 'title'));
    }

    public function create()
    {
        ResponseService::noPermissionThenRedirect('create-plan');
        $title = __('page.CREATE_PLAN');
        $plans = Plan::with(['features_plan', 'planTenures'])->get();

        $data = [
            'title' => $title,
            'plans' => $plans,
        ];
        return view('admin.membership_plan.create', $data);
    }

    public function store(Request $request)
    {
        ResponseService::noPermissionThenRedirect('create-plan');

        $validator = Validator::make($request->all(), [
            'name'                             => 'required|string|max:255|unique:plans,name',
            'slug'                             => 'required|string|max:255|unique:plans',
            'duration'                         => 'required|array',
            'duration.*'                       => 'required|integer|min:1',
            'price'                            => 'required|array',
            'price.*'                          => 'required|numeric|min:0',
            'status'                           => 'boolean',
            'product_id'                       => 'nullable|array',
            'product_id.*'                     => 'nullable|string|max:255',
            'tenure_name'                      => 'required|array',
            'tenure_name.*'                    => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s]+$/'],

            'number_of_articles'               => 'required|numeric|min:1',
            'number_of_stories'                => 'required|numeric|min:1',
            'number_of_e_papers_and_magazines' => 'required|numeric|min:1',

        ], [
            'duration.*.required'                       => 'Please enter a duration for each tenure.',
            'duration.*.integer'                        => 'Duration must be a valid number.',
            'price.*.required'                          => 'Please enter a price for each tenure.',
            'price.*.numeric'                           => 'Price must be a valid number.',
            'tenure_name.*.required'                    => 'Please enter a name for each tenure.',
            'tenure_name.*.regex'                       => 'Tenure name must only contain letters.',
            'number_of_articles.required'               => 'Please enter the number of articles.',
            'number_of_articles.numeric'                => 'The number of articles must be a valid number.',
            'number_of_articles.min'                    => 'The number of articles cannot be less than 1.',

            'number_of_stories.required'                => 'Please enter the number of stories.',
            'number_of_stories.numeric'                 => 'The number of stories must be a valid number.',
            'number_of_stories.min'                     => 'The number of stories cannot be less than 1.',

            'number_of_e_papers_and_magazines.required' => 'Please enter the number of e-papers or magazines.',
            'number_of_e_papers_and_magazines.numeric'  => 'The number of e-papers or magazines must be a valid number.',
            'number_of_e_papers_and_magazines.min'      => 'The number of e-papers or magazines cannot be less than 1.',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        DB::beginTransaction();

        try {
            // Create the plan with the status field
            $plan = Plan::create([
                'name'        => $request->name,
                'description' => $request->description,
                'slug'        => $request->slug,
                'status'      => $request->status ?? 0,
            ]);

            // Create features for the plan
            $feature = Feature::create([
                'plan_id'                          => $plan->id,
                'is_ads_free'                      => $request->has('is_ads_free') ? 1 : 0,
                'number_of_articles'               => $request->number_of_articles ?? 0,
                'number_of_stories'                => $request->number_of_stories ?? 0,
                'number_of_e_papers_and_magazines' => $request->number_of_e_papers_and_magazines ?? 0,
            ]);

            // Create plan tenures
            foreach ($request->duration as $key => $duration) {
                if (! empty($duration)) {
                    PlanTenure::create([
                        'name'       => $request->tenure_name[$key] ?? 'Plan for ' . $duration . ' months',
                        'plan_id'    => $plan->id,
                        'duration'   => $duration,
                        'price'      => $request->price[$key],
                        'product_id' => $request->product_id[$key] ?? null,
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'status'   => 'success',
                'message'  => 'Pricing plan created successfully!',
                'redirect' => route('pricing-plans.index'),
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to create pricing plan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified pricing plan.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        ResponseService::noPermissionThenRedirect('update-plan');

        $title = __('page.UPDATE_PLAN');
        $plan  = Plan::with(['features', 'planTenures'])->findOrFail($id);

        if ($plan->hasActiveSubscriptions()) {
            return redirect()->route('pricing-plans.index')->with('error', 'You cannot edit this plan because it has active subscriptions.');
        }

        $data = [
            'title' => $title,
            'plan'  => $plan, // Pass single plan instead of $plans
        ];

        return view('admin.membership_plan.edit', $data);
    }

    /**
     * Update the specified pricing plan in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        ResponseService::noPermissionThenRedirect('update-plan');

        $plan = Plan::findOrFail($id);

        $validator = Validator::make($request->all(), [
            // 'name'                             => 'required|string|max:255',
            'name'                             => 'required|string|max:255|unique:plans,name,' . $id,
            'slug'                             => 'required|string|max:255|unique:plans,slug,' . $id, // unique except this plan
            'tenure_name'                      => 'required|array',
            'tenure_name.*'                    => 'required|string|max:255',
            'duration'                         => 'required|array',
            'duration.*'                       => 'required|integer|min:1',
            'price'                            => 'required|array',
            'price.*'                          => 'required|numeric|min:1',
            'status'                           => 'boolean',
            'product_id'                       => 'nullable|array',
            'product_id.*'                     => 'nullable|string|max:255',
            'tenure_id'                        => 'nullable|array',
            'tenure_id.*'                      => 'nullable|integer',
            'number_of_articles'               => 'required|numeric|min:1',
            'number_of_stories'                => 'required|numeric|min:1',
            'number_of_e_papers_and_magazines' => 'required|numeric|min:1',
        ], [
            'tenure_name.*.required'                    => 'Please enter a name for each tenure.',
            'tenure_name.*.string'                      => 'Tenure name must be a valid string.',
            'duration.*.required'                       => 'Please enter a duration for each tenure.',
            'duration.*.integer'                        => 'Duration must be a valid number.',
            'price.*.required'                          => 'Please enter a price for each tenure.',
            'price.*.numeric'                           => 'Price must be a valid number.',
            'price.*.min'                               => 'The Price cannot be less than 1.',
            'number_of_articles.required'               => 'Please enter the number of articles.',
            'number_of_articles.numeric'                => 'The number of articles must be a valid number.',
            'number_of_articles.min'                    => 'The number of articles cannot be less than 1.',

            'number_of_stories.required'                => 'Please enter the number of stories.',
            'number_of_stories.numeric'                 => 'The number of stories must be a valid number.',
            'number_of_stories.min'                     => 'The number of stories cannot be less than 1.',

            'number_of_e_papers_and_magazines.required' => 'Please enter the number of e-papers or magazines.',
            'number_of_e_papers_and_magazines.numeric'  => 'The number of e-papers or magazines must be a valid number.',
            'number_of_e_papers_and_magazines.min'      => 'The number of e-papers or magazines cannot be less than 1.',

        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($plan->hasActiveSubscriptions()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'You cannot update this plan because it has active subscriptions.',
            ], 403);
        }

        DB::beginTransaction();

        try {
            // Update plan
            $plan->update([
                'name'        => $request->name,
                'description' => $request->description,
                'slug'        => $request->slug,
                'status'      => $request->status ?? 0, // Ensure it defaults to 0 (inactive) if not checked

            ]);

            // Update features
            $plan->features()->update([
                'is_ads_free'                      => $request->has('is_ads_free') ? 1 : 0,
                'number_of_articles'               => $request->number_of_articles ?? 0,
                'number_of_stories'                => $request->number_of_stories ?? 0,
                'number_of_e_papers_and_magazines' => $request->number_of_e_papers_and_magazines ?? 0,
            ]);

            // Update existing tenures or create new ones
            $submittedTenureIds = $request->tenure_id ?? [];

            foreach ($request->duration as $key => $duration) {
                if (! empty($duration)) {
                    $tenureData = [
                        'name'       => $request->tenure_name[$key] ?? 'Plan for ' . $duration . ' months',
                        'plan_id'    => $plan->id,
                        'duration'   => $duration,
                        'price'      => $request->price[$key],
                        'product_id' => $request->product_id[$key] ?? null,
                    ];

                    $tenureId = $submittedTenureIds[$key] ?? null;

                    if ($tenureId) {
                        // Update existing tenure
                        PlanTenure::where('id', $tenureId)->update($tenureData);
                    } else {
                        // Create new tenure
                        PlanTenure::create($tenureData);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'status'   => 'success',
                'message'  => 'Pricing plan updated successfully!.',
                'redirect' => route('pricing-plans.index'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to update pricing plan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified pricing plan from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ResponseService::noPermissionThenRedirect('delete-plan');

        try {
            $plan = Plan::findOrFail($id);
            if ($plan->hasActiveSubscriptions()) {
                return redirect()->back()->with('error', 'You cannot delete this plan because it has active subscriptions.');
            }
            $plan->delete();
            return redirect()->route('pricing-plans.index')->with('success', 'Pricing plan deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete pricing plan: ' . $e->getMessage());
        }
    }

    /**
     * Change the status of the pricing plan.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleStatus($id)
    {
        $plan         = Plan::findOrFail($id);
        $plan->status = ! $plan->status;
        $plan->save();

        return response()->json(['success' => true, 'status' => $plan->status]);
    }
}
