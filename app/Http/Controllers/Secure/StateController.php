<?php

namespace App\Http\Controllers\Secure;

use Log;
use App\DTO\StateDto;
use Response;
use App\Models\State;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStateRequest;
use App\Services\CountryService;
use App\Services\StateService;

class StateController extends Controller
{
    protected $service;
    protected $countryService;

    public function __construct()
    {
        $this->service = new StateService();
        $this->countryService = new CountryService();
    }

    public function index()
    {
        $pageTitle = "State";
        $countries = $this->countryService->findAll();
        return view('secure.states.index', compact('pageTitle', 'countries'));
    }

    // Function to get all state in for data table;
    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $states = $this->service->findAll();
            return DataTables::of($states)
                ->addColumn('action', function ($state) {
                    $button = '';
                    if (auth()->user()->can('edit state')) {
                        $button .= '<button type="button" data-id="' . $state->id . '" class="btn btn-sm btn-warning btn-edit" title="Edit"><i class="fa fa-edit"></i></button> ';
                    }

                    if (auth()->user()->can('delete state')) {
                        $button .= '<button class="btn btn-sm btn-danger btn-delete" data-id="' . $state->id . '" title="Delete">
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
    public function store(StoreStateRequest $request)
    {
        try {
            $dto = new StateDto(
                $request->input('country_id'),
                strip_tags($request->input('name')),
                strip_tags($request->input('iso2')),
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
    public function update(StoreStateRequest $request, State $state)
    {
        try {
            $dto = new StateDto(
                $request->input('country_id'),
                strip_tags($request->input('name')),
                strip_tags($request->input('iso2')),
                $state->created_by,
                auth()->user()->id
            );
            $result = $this->service->update($dto, $state->id);

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
            Log::error('State: Deletion failed: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong!'], 500);
        }
    }

    public function fetchUsingCountry(Request $request, $countryId)
    {
        try {
            if ($countryId) {
                $states = $this->service->findForPublic([
                    'country_id' => $countryId
                ]);
                return response()->json(['status' => true, 'message' => 'Record fetched successfully.', "data" => $states], 200);
            } else {
                return response()->json(['message' => 'No states data found.'], 404);
            }
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('State: Error while fetching data: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong!'], 500);
        }
    }
}
