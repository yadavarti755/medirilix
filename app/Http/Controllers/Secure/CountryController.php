<?php

namespace App\Http\Controllers\Secure;

use Log;
use App\DTO\CountryDto;
use Response;
use App\Models\Country;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCountryRequest;
use App\Services\CountryService;

class CountryController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new CountryService();
    }

    public function index()
    {
        $pageTitle = "Country";
        return view('secure.countries.index', compact('pageTitle'));
    }

    // Function to get all country in for data table;
    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $countries = $this->service->findAll();
            return DataTables::of($countries)
                ->addColumn('action', function ($country) {
                    $button = '';
                    if (auth()->user()->can('edit country')) {
                        $button .= '<button type="button" data-id="' . $country->id . '" class="btn btn-sm btn-warning btn-edit" title="Edit"><i class="fa fa-edit"></i></button> ';
                    }

                    if (auth()->user()->can('delete country')) {
                        $button .= '<button class="btn btn-sm btn-danger btn-delete" data-id="' . $country->id . '" title="Delete">
                            <i class="fa fa-trash"></i>
                        </button>';
                    }
                    return $button;
                })
                ->rawColumns(['action', 'file'])
                ->make(true);
        }
    }

    // Fetch single detail
    public function fetchOne(Request $request, $id)
    {
        try {
            $data = $this->service->findById($id);
            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            Log::error('Record addition failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server failed while processing request'
            ], 500);
        }
    }

    // Function to create
    public function store(StoreCountryRequest $request)
    {
        try {
            $dto = new CountryDto(
                strip_tags($request->input('name')),
                strtoupper(strip_tags($request->input('iso2'))),
                strip_tags($request->input('phone_code')),
                strip_tags($request->input('currency')),
                auth()->user()->id,
                auth()->user()->id
            );

            $result = $this->service->create($dto);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while saving record.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Record created successfully!'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Record addition failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server failed while processing request'
            ], 500);
        }
    }

    // Function to update
    public function update(StoreCountryRequest $request, Country $country)
    {
        try {
            $dto = new CountryDto(
                strip_tags($request->input('name')),
                strtoupper(strip_tags($request->input('iso2'))),
                strip_tags($request->input('phone_code')),
                strip_tags($request->input('currency')),
                $country->created_by,
                auth()->user()->id
            );
            $result = $this->service->update($dto, $country->id);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while updating record.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Record updated successfully!'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Record updation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $result = $this->service->delete($id);
            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while deleting record.',
                ], 500);
            }
            return response()->json(['message' => 'Record moved to trash successfully!']);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Country: Deletion failed: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong!'], 500);
        }
    }
}
