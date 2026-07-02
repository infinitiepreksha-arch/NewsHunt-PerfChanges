<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Cerbero\JsonParser\JsonParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class CountryController extends Controller
{
    const ERROR_MESSAGE = 'Something Went Wrong';
    /**
     * Display a listing of the resource.
     */
    public function countryIndex() {
        $countries = JsonParser::parse(resource_path('countries.json'))->pointers(['/-/name', '/-/id', '/-/emoji'])->toArray();
        $dbCountries = Country::select('name')->get();
        foreach ($countries as $key => $country) {
            $countries[$key]['is_already_exists'] = $dbCountries->contains(static function ($dbCountry) use ($country) {
                return $country['name'] == $dbCountry->name;
            });
        }
        $title =  __('COUNTRIES');
        return view('admin.places.country', compact('countries','title'));
    }
    
    /**
     *  Fetch Country List
     */
    public function countryShow(Request $request)
    {
        try {
            
            $countries = Country::select(['id', 'name', 'emoji'])->get();
            
            return DataTables::of($countries)
            ->addColumn('action', function ($country) {
                return "<a href='" . route('countries.destroy', $country->id) . "' class='btn text-danger btn-sm delete-form delete-form-reload' data-bs-toggle='tooltip' title='Delete'> <i class='fa fa-trash'></i> </a>";
            })
            ->make(true);
    
        } catch (\Throwable $e) {
            ResponseService::logErrorResponse($e, "CustomFieldController -> countryShow");
            
            return ResponseService::errorResponse(self::ERROR_MESSAGE);
        }
    }


    public function destroyCountry($id) {
        try {

            Country::find($id)->delete();
            ResponseService::successResponse("Country deleted Successfully");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "PlaceController -> destroyCountry");
            ResponseService::errorResponse(self::ERROR_MESSAGE);
        }
    }
    
    public function citySearch(Request $request) {
        try {
            $cities = City::where('state_id', $request->state_id)->select(['id', 'name'])->orderBy('name', 'ASC')->get();
            ResponseService::successResponse("Cities fetched Successfully", $cities);
        } catch (Throwable $th) {
            ResponseService::logErrorRedirect($th, "PlaceController -> citySearch");
            ResponseService::errorResponse();
        }

    }

    public function cityIndex() {
        $countries = Country::all();
        $states = State::get();
        return view('places.city', compact('countries', 'states'));
    }

    public function importCountry(Request $request) {
        $validator = Validator::make($request->all(), [
            'countries'   => 'required|array',
            'countries.*' => 'integer',
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $country_id = $request->countries;
            DB::beginTransaction();
            foreach (JsonParser::parse(resource_path('world.json')) as $country) {
                if (in_array($country['id'], $country_id, false)) {
                    Country::create([
                        ...$country,
                        'timezones'    => json_encode($country['timezones'], JSON_THROW_ON_ERROR),
                        'translations' => json_encode($country['translations'], JSON_THROW_ON_ERROR),
                        'region_id'    => null,
                        'subregion_id' => null,
                    ]);


                    foreach ($country['states'] as $state) {
                        State::create([
                            ...$state,
                            'country_id' => $country['id']
                        ]);

                        $cities = [];
                        foreach ($state['cities'] as $city) {
                            $cities[] = [
                                ...$city,
                                'state_id'     => $state['id'],
                                'state_code'   => $state['state_code'],
                                'country_id'   => $country['id'],
                                'country_code' => $country['iso2'],
                            ];
                        }

                        City::insert($cities);
                    }

                    /*Stop the JSON file reading if country_id array is empty*/
                    unset($country_id[array_search($country['id'], $country_id, true)]);
                    if (empty($country_id)) {
                        break;
                    }
                }
            }
            DB::commit();
            ResponseService::successResponse("Country imported successfully");
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, "CustomFieldController -> show");
            ResponseService::errorResponse(self::ERROR_MESSAGE);
        }

    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
