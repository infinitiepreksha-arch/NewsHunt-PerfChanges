<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\Plan;
use App\Models\PlanTenure;
use App\Models\Subscription;
use App\Models\User;
use App\Services\ResponseService;
use App\Models\Transaction;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        ResponseService::noAnyPermissionThenRedirect(['list-subscription']);

        $users         = User::all();
        $plans         = Plan::all();
        $features      = Feature::all();
        $planTenures   = PlanTenure::with('plan')->get();
        $subscriptions = Subscription::with(['user', 'plan', 'feature', 'transactions'])->get();
    
        // Get features for each plan
        $planFeatures = [];
        foreach ($plans as $plan) {
            $feature                 = Feature::where('plan_id', $plan->id)->first();
            $planFeatures[$plan->id] = $feature ? $feature->id : null;
        }
        $transactions = Transaction::with(['user', 'subscription'])->get();

        $data = [
            'users'         => $users,
            'plans'         => $plans,
            'features'      => $features,
            'planTenures'   => $planTenures,
            'planFeatures'  => $planFeatures,
            'subscriptions' => $subscriptions,
            'transactions' => $transactions,
        ];
        return view('admin.subscription.index', $data);
    }
}
