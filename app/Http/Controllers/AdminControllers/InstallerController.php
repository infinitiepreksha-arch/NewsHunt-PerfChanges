<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use dacoto\LaravelWizardInstaller\Controllers\InstallServerController;
use dacoto\LaravelWizardInstaller\Controllers\InstallFolderController;
use dacoto\EnvSet\Facades\EnvSet;
use Illuminate\Support\Facades\Validator;

class InstallerController extends Controller
{
    /**
     * Show the purchase code input page.
     */
    public function purchaseCodeIndex()
    {
        if (!(new InstallServerController())->check() || !(new InstallFolderController())->check()) {
            return redirect()->route('LaravelWizardInstaller::install.folders');
        }

        return view('vendor.installer.steps.purchase-code');
    }

    /**
     * Validate the purchase code and save it.
     */
    public function checkPurchaseCode(Request $request)
    {

        Validator::make($request->all(), [
            'purchase_code' => 'required|string',
        ]);

        try {
            // Build the API request
            $domainUrl = request()->getHost();
            $purchaseCode = $request->input('purchase_code');

            // Save the purchase code in the environment file
            EnvSet::setKey('APPSECRET', $purchaseCode);
            EnvSet::save();

            return redirect()->route('install.server');



            $itemId = "55506918";
            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->get(base64_decode(config('installer.block_head')), [
                        'purchase_code' => $purchaseCode,
                        'domain_url' => $domainUrl,
                        'item_id' => $itemId,
                    ]);

         

            // Handle the API response
            if ($response->failed()) {
                return view('vendor.installer.steps.purchase-code', [
                    'error' => 'Failed to validate the purchase code. Please try again.',
                ]);
            }

            $responseData = $response->json();
            if ($responseData['error'] ?? true) {
                return view('vendor.installer.steps.purchase-code', [
                    'error' => $responseData['message'] ?? 'An unknown error occurred.',
                ]);
            }

            // Save the purchase code in the environment file
            // EnvSet::setKey('APPSECRET', $purchaseCode);
            // EnvSet::save();

            return redirect()->route('install.server');
        } catch (\Throwable $e) {
            // Log the error and return with a user-friendly message
            Log::error('Purchase code validation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return view('vendor.installer.steps.purchase-code', [
                'error' => 'An error occurred while validating the purchase code. Please contact support.',
            ]);
        }
    }
}
