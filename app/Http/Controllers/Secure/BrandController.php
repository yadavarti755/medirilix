<?php

namespace App\Http\Controllers\Secure;

use App\DTO\BrandDto;
use Response;
use App\Models\Brand;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBrandRequest;
use App\Services\BrandService;

class BrandController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new BrandService();
    }

    public function index()
    {
        $pageTitle = "Brand";
        return view('secure.brands.index', compact('pageTitle'));
    }

    // Function to get all brands in for data table;
    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $brands = $this->service->findAll();
            return DataTables::of($brands)
                ->addColumn('file', function ($brand) {
                    if ($brand->file_name) {
                        return "<img src=" . $brand->file_url . " alt='Image' class='img-fluid' style='max-height: 70px;'>";
                    }

                    return '';
                })
                ->addColumn('action', function ($brand) {
                    $button = '';
                    if (auth()->user()->can('edit brand')) {
                        $button .= '<button type="button" data-id="' . $brand->id . '" class="btn btn-sm btn-warning btn-edit" title="Edit"><i class="fa fa-edit"></i></button> ';
                    }

                    if (auth()->user()->can('delete brand')) {
                        $button .= '<button class="btn btn-sm btn-danger btn-delete" data-id="' . $brand->id . '" title="Delete">
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
    public function store(StoreBrandRequest $request)
    {
        try {
            $dto = new BrandDto(
                strip_tags($request->input('name')),
                $request->hasFile('file_name') ? $request->file('file_name') : null,
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
    public function update(StoreBrandRequest $request, Brand $brand)
    {
        try {
            $dto = new BrandDto(
                strip_tags($request->input('name')),
                $request->hasFile('file_name') ? $request->file('file_name') : null,
                $brand->created_by,
                auth()->user()->id
            );
            $result = $this->service->update($dto, $brand->id);

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
            Log::error('Brand: Deletion failed: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong!'], 500);
        }
    }
}
