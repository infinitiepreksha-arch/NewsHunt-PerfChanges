<?php
namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    public function index()
    {
        $theme = getTheme();
        $title = __('frontend-labels.contactus.title');
        return view('front_end.' . $theme . '.pages.contact-us', compact('theme', 'title'));

    }

    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name'  => 'nullable|string|max:255',
            'email'      => 'required|email|max:255',
            'phone'      => 'nullable|string|max:30',
            'message'    => 'required|string',
        ]);

        $countryCode    = '';
        $nationalNumber = '';

        // If phone is provided, validate and parse it
        if (! empty($validated['phone'])) {
            $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
            try {
                $phoneNumber    = $phoneUtil->parse($validated['phone'], null);
                $countryCode    = '+' . $phoneNumber->getCountryCode();
                $nationalNumber = $phoneNumber->getNationalNumber();
            } catch (\libphonenumber\NumberParseException $e) {
                return response()->json([
                    'success' => false,
                    'errors'  => [
                        'phone' => 'Invalid phone number format',
                    ],
                ], 422);
            }
        }

        // Create a new contact record in the database
        Contact::updateOrCreate(
            ['email' => $validated['email']], // match by email
            [
                'first_name'   => $validated['first_name'] ?? "",
                'last_name'    => $validated['last_name'] ?? "",
                'country_code' => $countryCode,
                'phone_number' => $nationalNumber,
                'message'      => $validated['message'],
            ]
        );

        // Return a JSON response for success
        return response()->json([
            'success' => true,
            'message' => __('frontend-labels.contactus.thank_you_message'),
        ]);
    }

}
