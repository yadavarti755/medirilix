<?php

namespace App\Http\Controllers\Secure;

use App\DTO\SizeDto;
use Response;
use App\Models\Size;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSizeRequest;
use App\Services\SizeService;

class SizeController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new SizeService();
    }

    public function index()
    {
        $pageTitle = "Size";
        return view('secure.sizes.index', compact('pageTitle'));
    }

    // Function to get all sizes in for data table;
    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $sizes = $this->service->findAll();
            return DataTables::of($sizes)
                ->addColumn('action', function ($size) {
                    $button = '';
                    if (auth()->user()->can('edit size')) {
                        $button .= '<button type="button" data-id="' . $size->id . '" class="btn btn-sm btn-warning btn-edit" title="Edit"><i class="fa fa-edit"></i></button> ';
                    }

                    if (auth()->user()->can('delete size')) {
                        $button .= '<button class="btn btn-sm btn-danger btn-delete" data-id="' . $size->id . '" title="Delete">
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
    public function store(StoreSizeRequest $request)
    {
        try {

            $dto = new SizeDto(
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
    public function update(StoreSizeRequest $request, Size $size)
    {
        try {
            $dto = new SizeDto(
                strip_tags($request->input('name')),
                $size->created_by,
                auth()->user()->id
            );
            $result = $this->service->update($dto, $size->id);

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
            Log::error('Size: Deletion failed: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong!'], 500);
        }
    }
}
