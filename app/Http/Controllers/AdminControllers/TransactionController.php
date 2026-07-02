<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Services\ResponseService;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        ResponseService::noAnyPermissionThenRedirect(['list-transaction', 'show-transaction']);
        $title        = __('page.TRANSACTIONS');
        $transactions = Transaction::with(['user', 'subscription', 'plan', 'feature'])
            ->orderBy('created_at', 'desc')
            ->paginate();

        $data = [
            'transactions' => $transactions,
            'title'        => $title,
        ];

        return view('admin.transaction.index', $data);
    }

    public function show($id)
    {
        ResponseService::noPermissionThenRedirect('show-transaction');
        $transaction = Transaction::with(['user', 'subscription', 'plan', 'feature'])
            ->findOrFail($id);

        $userSubscriptions = [];
        if ($transaction->user) {
            $userSubscriptions = Subscription::where('user_id', $transaction->user->id)
                ->where('transaction_id', $transaction->id)
                ->with([
                    'plan' => function ($query) {
                        $query->with(['features_plan', 'planTenures']);
                    },
                ])
                ->get();

                                                                // Filter subscriptions based on feature_id and plan_tenure_id if provided
            $featureId    = request()->input('feature_id');     // Assuming feature_id is passed via request
            $planTenureId = request()->input('plan_tenure_id'); // Assuming plan_tenure_id is passed via request

            if ($featureId) {
                $userSubscriptions = $userSubscriptions->filter(function ($subscription) use ($featureId) {
                    return $subscription->plan->features_plan->id == $featureId;
                });
            }

            if ($planTenureId) {
                $userSubscriptions = $userSubscriptions->filter(function ($subscription) use ($planTenureId) {
                    return $subscription->plan->planTenures->contains('id', $planTenureId);
                });
            }
        }

        return view('admin.transaction.show', compact('transaction', 'userSubscriptions'));
    }
}
