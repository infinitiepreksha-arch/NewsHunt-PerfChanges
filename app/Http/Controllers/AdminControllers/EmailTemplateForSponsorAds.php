<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class EmailTemplateForSponsorAds extends Controller
{

    public function index()
    {
        ResponseService::noAnyPermissionThenRedirect(['list-SponsorEmailtemplate', 'create-SponsorEmailtemplate', 'view-detail-SponsorEmailtemplate', 'delete-SponsorEmailtemplate', 'change-status-SponsorEmailtemplate']);
        $title     = __('page.SPONSOR_EMAIL_TEMPLATES_DETAILS');
        $pre_title = __('page.SPONSOR_EMAIL_TEMPLATES_DETAILS');
        $templates = EmailTemplate::all();
        $data      = [
            'title'     => $title,
            'pre_title' => $pre_title,
            'templates' => $templates,
        ];

        return view('admin.email_template_sponsor_ads.index', $data);
    }

    public function create()
    {
        ResponseService::noPermissionThenRedirect('create-SponsorEmailtemplate');

        $title     = __('page.CREATE_EMAIL_TEMPLATE');
        $pre_title = __('page.CREATE_EMAIL_TEMPLATE');

        $data = [
            'title'     => $title,
            'pre_title' => $pre_title,
        ];

        return view('admin.email_template_sponsor_ads.create', $data);
    }

    public function store(Request $request)
    {
        ResponseService::noPermissionThenRedirect('create-SponsorEmailtemplate');
        try
        {
            $validated = $request->validate([
                'title'        => 'required|string|max:255',
                'post_count'   => 'nullable|integer|min:1',
                'layout_width' => 'required|integer|min:300',
                'html_content' => 'required',
                'status'       => 'required|in:active,inactive',
                'subject'      => 'required|string|max:255',
                'logo'         => 'nullable|image|mimes:webp,jpeg,png,jpg,gif,svg|max:8004',
                'image'        => 'nullable|image|mimes:webp,jpeg,png,jpg,gif,svg|max:8004',
                'closing'      => 'nullable|string|max:255',
                'signature'    => 'nullable|string',
                'footer_text'  => 'nullable|string',
            ]);

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

            $logoPath       = null;
            $imagePath      = null;
            $attachmentPath = null;

            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('email_templates/logos', 'public');
            }

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('email_templates/images', 'public');
            }

            EmailTemplate::create([
                'title'        => $request->title,
                'slug'         => $slug,
                'layout_width' => $request->layout_width,
                'post_count'   => $request->post_count,
                'html_content' => $request->html_content,
                'status'       => $request->status,
                'type'         => 'sponsor',
                'subject'      => $request->subject,
                'logo'         => $logoPath,
                'image'        => $imagePath,
                'closing'      => $request->closing,
                'signature'    => $request->signature,
                'footer_text'  => $request->footer_text,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'status'   => 'success',
                    'message'  => 'Email template created successfully.',
                    'redirect' => route('email-Sponsor-Ads.index'),
                ]);
            }

            return redirect()
                ->route('email-Sponsor-Ads.index')
                ->with('success', 'Email template created successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'errors' => $e->errors()], 422);
            }
            throw $e; // fallback for normal request
        }
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
                // Select all columns from the database
                $query = EmailTemplate::select(
                    'id',
                    'title',
                    'slug',
                    'post_count',
                    'layout_width',
                    'html_content',
                    'status',
                    'subject',
                    'logo',
                    'image',
                    'type',
                    'closing',
                    'signature',
                    'footer_text',
                    'created_at'
                )->where('type', 'sponsor');

                if ($status !== '' && $status !== '*') {
                    $query->where('status', $status);
                }

                return DataTables::of($query)
                    ->addColumn('action', function ($template) {
                        $actions = '';

                        if (auth()->user()->can('view-SponsorEmailtemplate')) {
                            $actions .= "<a class='btn text-info btn-sm preview_btn' title='Preview Template'>
                        <i class='fa fa-eye'></i>
                     </a>";
                        } else {
                            $actions .= "<span class='badge bg-primary text-white'>No permission for View.</span>";
                        }

                        if (auth()->user()->can('delete-SponsorEmailtemplate')) {
                            $actions .= " <a href='" . route('email-Sponsor-Ads.destroy', $template->id) . "'
                        class='btn text-danger btn-sm delete-form delete-form-reload'
                        data-bs-toggle='tooltip' title='Delete'>
                        <i class='fa fa-trash'></i>
                     </a>";
                        } else {
                            $actions .= "<span class='badge bg-danger text-white'>No permission for Delete.</span>";
                        }

                        return $actions;
                    })

                    ->editColumn('created_at', fn($t) => $t->created_at ? $t->created_at->format('Y-m-d') : '')
                    ->editColumn('logo', fn($t) => $t->logo ?? '')
                    ->editColumn('image', fn($t) => $t->image ?? '')
                    ->editColumn('subject', fn($t) => $t->subject ?? '')
                    ->editColumn('type', fn($t) => $t->type ?? '')
                    ->editColumn('closing', fn($t) => $t->closing ?? '')
                    ->editColumn('signature', fn($t) => $t->signature ?? '')
                    ->editColumn('footer_text', fn($t) => $t->footer_text ?? '')
                    ->rawColumns(['action', 'logo', 'image', 'signature', 'footer_text'])
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
        ResponseService::noPermissionThenSendJson('delete-SponsorEmailtemplate');
        try {

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
        try {
            ResponseService::noPermissionThenSendJson('change-status-emailtemplate');

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
