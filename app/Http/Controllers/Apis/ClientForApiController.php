<?php
namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\ClientForm;
use Illuminate\Http\Request;

class ClientForApiController extends Controller
{
    public function store(Request $request)
    {
        // Check required fields one by one
        if (empty($request->first_name)) {
            return response()->json([
                'error'   => true,
                'message' => 'First Name is required.',
            ], 422);
        }

        if (empty($request->work_email)) {
            return response()->json([
                'error'   => true,
                'message' => 'Work Email is required.',
            ], 422);
        }

        if (empty($request->country)) {
            return response()->json([
                'error'   => true,
                'message' => 'Country is required.',
            ], 422);
        }

        // If all required fields are present, create record
        $form = ClientForm::create([
            'first_name' => $request->first_name,
            'work_email' => $request->work_email,
            'country'    => $request->country,
        ]);

        return response()->json([
            'error'   => false,
            'message' => 'Form submitted successfully.',
            'data'    => $form,
        ], 201);
    }
}
