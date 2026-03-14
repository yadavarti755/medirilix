<?php

namespace App\Http\Controllers\Secure;

use App\DTO\MaterialDto;
use Response;
use App\Models\Material;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMaterialRequest;
use App\Services\MaterialService;

class MaterialController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new MaterialService();
    }

    public function index()
    {
        $pageTitle = "Material";
        return view('secure.materials.index', compact('pageTitle'));
    }

    // Function to get all materials in for data table;
    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $materials = $this->service->findAll();
            return DataTables::of($materials)
                ->addColumn('action', function ($material) {
                    $button = '';
                    if (auth()->user()->can('edit material')) {
                        $button .= '<button type="button" data-id="' . $material->id . '" class="btn btn-sm btn-warning btn-edit" title="Edit"><i class="fa fa-edit"></i></button> ';
                    }

                    if (auth()->user()->can('delete material')) {
                        $button .= '<button class="btn btn-sm btn-danger btn-delete" data-id="' . $material->id . '" title="Delete">
                            <i class="fa fa-trash"></i>
                        </button>';
                    }
                    return $button;
                })
                ->rawColumns(['action', 'image'])
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
    public function store(StoreMaterialRequest $request)
    {
        try {
            $dto = new MaterialDto(
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
    public function update(StoreMaterialRequest $request, Material $material)
    {
        try {
            $dto = new MaterialDto(
                strip_tags($request->input('name')),
                $material->created_by,
                auth()->user()->id
            );
            $result = $this->service->update($dto, $material->id);

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
            Log::error('Material: Deletion failed: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong!'], 500);
        }
    }
}
