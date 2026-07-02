<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Services\ResponseService;
use Yajra\DataTables\Facades\DataTables;

class ContactUsAdminController extends Controller
{
    public function view()
    {
        ResponseService::noAnyPermissionThenRedirect(['list-contactus', 'delete-contactus', 'view-contactus']);

        $title = __('page.CONTACT-US');

        return view('admin.contact_us.contact-us', compact('title'));
    }

    public function show()
    {
        ResponseService::noPermissionThenRedirect('list-contactus');

        $getData = Contact::select('*')->get();

        $getData->each(function ($contactData) {
            $contactData->name  = $contactData->first_name . ' ' . $contactData->last_name;
            $contactData->phone = $contactData->country_code . ' ' . $contactData->phone_number;
        });
        return DataTables::of($getData)
            ->addColumn('action', function ($getData) {
                $buttons = '';

                if (auth()->user()->can('view-contactus')) {
                    $buttons .= '
                <button class="btn text-primary btn-sm edit_btn"
                    data-bs-toggle="modal"
                    data-bs-target="#contact-us-modal"
                    data-id="' . $getData->id . '"
                    title="View Contact">
                    <i class="fa fa-eye"></i>
                </button>
            ';
                } else {
                    $buttons .= "<span class='badge bg-primary text-white m-1'>No permission for View Details.</span>";
                }

                if (auth()->user()->can('delete-contactus')) {
                    $buttons .= '
                <button class="btn text-danger btn-sm delete_btn contact_us_btn"
                    data-id="' . $getData->id . '"
                    title="Delete Contact">
                    <i class="fa fa-trash"></i>
                </button>
            ';
                } else {
                    $buttons .= "<span class='badge bg-danger text-white m-1'>No permission for Delete</span>";

                }
                $buttons .= "</div>";
                return $buttons;
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function destroy($id)
    {
        ResponseService::noPermissionThenRedirect('delete-contactus');

        $contact = Contact::find($id);

        $contact->delete();

        return response()->json(['status' => 'success', 'message' => 'Contact deleted successfully.']);
    }

}
