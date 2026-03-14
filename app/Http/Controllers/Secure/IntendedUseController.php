<?php

namespace App\Http\Controllers\Secure;

use App\DTO\IntendedUseDto;
use Response;
use App\Models\IntendedUse;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIntendedUseRequest;
use App\Services\IntendedUseService;

class IntendedUseController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new IntendedUseService();
    }

    public function index()
    {
        $pageTitle = "Intended Use";
        return view('secure.intended_uses.index', compact('pageTitle'));
    }

    // Function to get all intended uses in for data table;
    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $intendedUses = $this->service->findAll();
            return DataTables::of($intendedUses)
                ->addColumn('action', function ($intendedUse) {
                    $button = '';
                    if (auth()->user()->can('edit intended use')) {
                        $button .= '<button type="button" data-id="' . $intendedUse->id . '" class="btn btn-sm btn-warning btn-edit" title="Edit"><i class="fa fa-edit"></i></button> ';
                    }

                    if (auth()->user()->can('delete intended use')) {
                        $button .= '<button class="btn btn-sm btn-danger btn-delete" data-id="' . $intendedUse->id . '" title="Delete">
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
    public function store(StoreIntendedUseRequest $request)
    {
        try {
            $dto = new IntendedUseDto(
                strip_tags($request->input('name')),
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
    public function update(StoreIntendedUseRequest $request, IntendedUse $intendedUse)
    {
        try {
            $dto = new IntendedUseDto(
                strip_tags($request->input('name')),
                $intendedUse->created_by,
                auth()->user()->id
            );
            $result = $this->service->update($dto, $intendedUse->id);

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
            Log::error('IntendedUse: Deletion failed: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong!'], 500);
        }
    }
}
