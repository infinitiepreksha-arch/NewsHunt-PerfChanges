<?php
namespace App\Http\Controllers;

use App\Services\ResponseService;
use App\Traits\LanguageDataTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Throwable;

// Add this import at the top
/*Create Method which are common across the system*/

class Controller extends BaseController
{
    use LanguageDataTrait;
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function changeRowOrder(Request $request)
    {
        try {
            $request->validate([
                'data'   => 'required|array',
                'table'  => 'required|string',
                'column' => 'nullable',
            ]);
            $column = $request->column ?? "sequence";

            $data = [];
            foreach ($request->data as $index => $row) {
                $data[] = [
                    'id'             => $row['id'],
                    (string) $column => $index,
                ];
            }
            DB::table($request->table)->upsert($data, ['id'], [(string) $column]);
            ResponseService::successResponse(__('frontend-labels.orders.order_changed_success'));

        } catch (Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function changeStatus(Request $request)
    {
        try {
            $request->validate([
                'id'     => 'required|numeric',
                'status' => 'required|boolean',
                'table'  => 'required|string',
                'column' => 'nullable',
            ]);
            $column = $request->column ?? "status";

            //Special case for deleted_at column
            if ($column == "deleted_at") {
                //If status is active then deleted_At will be empty otherwise it will have the current time
                $request->status = ($request->status) ? null : now();
            }
            DB::table($request->table)->where('id', $request->id)->update([(string) $column => $request->status]);
            ResponseService::successResponse(__('frontend-labels.orders.status_updated_success'));

        } catch (Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function readLanguageFile()
    {
        try {

            header('Content-Type: text/javascript');

            $lang = Session::get('language');

            $test  = $lang->code ?? "en";
            $files = resource_path('lang/' . $test . '.json');

            echo 'window.languageLabels = ' . File::get($files);
            exit();
        } catch (Throwable $th) {
            ResponseService::errorResponse($th);
        }
    }

    public function generateRandomName($length = 8)
    {
        return ucfirst(Str::lower(Str::random($length)));
    }
}
