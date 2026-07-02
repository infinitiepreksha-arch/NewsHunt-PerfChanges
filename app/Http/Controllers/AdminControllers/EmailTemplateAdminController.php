<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

// Add this import

class EmailTemplateAdminController extends Controller
{
    public function index()
    {
        ResponseService::noAnyPermissionThenRedirect(['list-emailtemplate', 'create-emailtemplate', 'view-detail-emailtemplate', 'delete-emailtemplate', 'change-status-emailtemplate']);
        $title     = __('page.EMAIL_TEMPLATE_DETAILS');
        $pre_title = __('page.EMAIL_TEMPLATE_DETAILS');
        $templates = EmailTemplate::all();
        $data      = [
            'title'     => $title,
            'pre_title' => $pre_title,
            'templates' => $templates,
        ];

        return view('admin.email_template.index', $data);
    }

    public function create()
    {
        ResponseService::noPermissionThenRedirect('create-emailtemplate');

        $title     = __('page.CREATE_EMAIL_TEMPLATE');
        $pre_title = __('page.CREATE_EMAIL_TEMPLATE');

        $data = [
            'title'     => $title,
            'pre_title' => $pre_title,
        ];

        return view('admin.email_template.create', $data);
    }

    public function store(Request $request)
    {
        ResponseService::noPermissionThenRedirect('create-emailtemplate');

        $validator = Validator::make($request->all(), [
            'title'        => 'required|string|max:255|unique:email_templates,title',
            'post_count'   => 'required|integer|min:1',
            'layout_width' => 'required|integer|min:300',
            'html_content' => 'required',
            'status'       => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        // Auto-generate a unique slug
        $baseSlug = Str::slug($request->title);
        $slug     = $baseSlug;
        if (empty($slug)) {
            $slug = 'temp-' . uniqid();
        }

        $count = 1;

        while (EmailTemplate::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count++;
        }

        EmailTemplate::create([
            'title'        => $request->title,
            'slug'         => $slug,
            'post_count'   => $request->post_count,
            'layout_width' => $request->layout_width,
            'html_content' => $request->html_content,
            'status'       => $request->status,
            'type'         => 'custom',
        ]);

        // return redirect()->route('email-template.index')->with('success', 'Email template created successfully.');
        return response()->json([
            'success'  => true,
            'message'  => 'Email template created successfully.',
            'redirect' => route('email-template.index'), // Added for JS redirect
        ]);
    }

    /**
     * Display the specified resource for DataTables.
     * This method should handle AJAX requests for DataTables
     */
    public function show(Request $request, $id = null)
    {

        if ($request->ajax()) {
            $status = $request->input('template_status') ?? '';

            try {
                $query = EmailTemplate::select('id', 'title', 'slug', 'post_count', 'layout_width', 'html_content', 'status', 'created_at')->where('type', 'custom');

                if ($status !== '' && $status !== '*') {
                    $query->where('status', $status);
                }

                $templates = $query->get();

                return DataTables::of($templates)
                    ->addColumn('action', function ($template) {
                        $actions = '';
                        if (auth()->user()->can('view-detail-emailtemplate')) {
                            $actions .= "<a
                            class='btn text-info btn-sm preview_btn'
                            title='Preview Template'>
                            <i class='fa fa-eye'></i>
                         </a> &nbsp;";
                        } else {
                            $actions .= "<span class='badge bg-primary text-white me-1'>No permission for View</span>";
                        }

                        if (auth()->user()->can('delete-emailtemplate')) {
                            $actions .= "<a href='" . route('email-template.destroy', $template->id) . "'
                            class='btn text-danger btn-sm delete-form delete-form-reload'
                            data-bs-toggle='tooltip'
                            title='Delete'>
                            <i class='fa fa-trash'></i>
                         </a>";
                        } else {
                            $actions .= "<span class='badge bg-danger text-white'>No permission for Delete</span>";
                        }

                        return $actions;
                    })
                    ->editColumn('created_at', function ($template) {
                        return $template->created_at ? $template->created_at->format('Y-m-d') : '';
                    })
                    ->rawColumns(['action'])
                    ->make(true);

            } catch (\Exception $e) {
                return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
            }
        }

        if ($id) {
            $template = EmailTemplate::findOrFail($id);
            return response()->json($template);
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        try {
            ResponseService::noPermissionThenSendJson('delete-emailtemplate');

            $template = EmailTemplate::findOrFail($id);
            $template->delete();

            return response()->json(['message' => 'Email template deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }

    }

    /**
     * Update the status of the specified resource.
     */
    public function updateStatus(Request $request)
    {
        ResponseService::noPermissionThenSendJson('change-status-emailtemplate');

        try {
            $template         = EmailTemplate::findOrFail($request->id);
            $template->status = $request->status;
            $template->save();

            $message = $request->status === 'active'
                ? 'Email template activated successfully'
                : 'Email template deactivated successfully';

            return response()->json(['message' => $message]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}
