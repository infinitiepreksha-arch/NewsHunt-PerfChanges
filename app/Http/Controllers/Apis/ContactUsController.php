<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Validator;

class ContactUsController extends Controller
{

    /**
     * Get all contact us entries
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllContacts(Request $request)
    {
        // Get all contacts
        $contacts = Contact::select('*')->get();
        
        // Format the contacts data
        $formattedContacts = $contacts->map(function ($contact) {
            return [
                'id' => $contact->id,
                'name' => $contact->first_name . ' ' . $contact->last_name,
                'email' => $contact->email,
                'phone' => $contact->country_code . ' ' . $contact->phone_number,
                'message' => $contact->message,
                'created_at' => $contact->created_at->format('Y-m-d H:i:s'),
            ];
        });
        
        return response()->json([
            'status' => true,
            'data' => $formattedContacts,
            'message' => 'Contact list retrieved successfully',
        ]);
    }

    // create contact us
    public function createContactUs(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'country_code' => 'nullable|string|max:10',
            'phone_number' => 'nullable|string|max:20',
            'message' => 'required|string',
        ]);
    
        // Create a new contact entry
        $contact = Contact::create([
            'first_name' => $request->first_name ?? "",
            'last_name' => $request->last_name ?? "",
            'email' => $request->email,
            'country_code' => $request->country_code ?? "",
            'phone_number' => $request->phone_number ?? "",
            'message' => $request->message,
        ]);
    
        return response()->json([
            'status' => true,
            'data' => $contact,
            'message' => 'Your message has been sent successfully!',
        ]);
    }
    /**
     * Delete contact by ID
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Get single contact by ID
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getContact($id)
    {
        $contact = Contact::find($id);
        
        if (!$contact) {
            return response()->json([
                'status' => false,
                'message' => 'Contact not found',
            ], 404);
        }
        
        return response()->json([
            'status' => true,
            'data' => [
                'id' => $contact->id,
                'first_name' => $contact->first_name,
                'last_name' => $contact->last_name,
                'email' => $contact->email,
                'phone' => $contact->country_code . ' ' . $contact->phone_number,
                'message' => $contact->message,
                'created_at' => $contact->created_at->format('Y-m-d H:i:s'),
            ],
            'message' => 'Contact retrieved sucessfully!!',
        ]);
    }
}