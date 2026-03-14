<?php

namespace App\Http\Controllers\Secure;

use App\DTO\OfferDto;
use Response;
use App\Models\Offer;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOfferRequest;
use App\Services\OfferService;
use Illuminate\Support\Facades\Log;

class OfferController extends Controller
{
    protected $service;
    protected $productService;
    protected $categoryService;

    public function __construct()
    {
        $this->service = new OfferService();
        $this->productService = new \App\Services\ProductService();
        $this->categoryService = new \App\Services\CategoryService();
    }

    public function index()
    {
        $pageTitle = "Offer";
        $products = $this->productService->findAll(['stock_availability' => 1], 1000); // Fetch active products
        $categories = $this->categoryService->findAll(); // Fetch all categories
        return view('secure.offers.index', compact('pageTitle', 'products', 'categories'));
    }

    // Function to get all offers in for data table;
    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $offers = Offer::with(['product', 'category'])->get();
            return DataTables::of($offers)
                ->addColumn('image', function ($offer) {
                    if ($offer->image) {
                        return "<img src=" . $offer->image_url . " alt='Image' class='img-fluid' style='max-height: 70px;'>";
                    }

                    return '';
                })
                ->addColumn('related_item', function ($offer) {
                    if ($offer->type == 'product' && $offer->product) {
                        return $offer->product->name;
                    } elseif ($offer->type == 'category' && $offer->category) {
                        return $offer->category->name;
                    }
                    return '-';
                })
                ->addColumn('status', function ($offer) {
                    if ($offer->is_active == 1) {
                        return '<span class="badge bg-success">Active</span>';
                    } else {
                        return '<span class="badge bg-danger">Inactive</span>';
                    }
                })
                ->addColumn('action', function ($offer) {
                    $button = '';
                    if (auth()->user()->can('edit offer')) {
                        $button .= '<button type="button" data-id="' . $offer->id . '" class="btn btn-sm btn-warning btn-edit" title="Edit"><i class="fa fa-edit"></i></button> ';
                    }

                    if (auth()->user()->can('delete offer')) {
                        $button .= '<button class="btn btn-sm btn-danger btn-delete" data-id="' . $offer->id . '" title="Delete">
                            <i class="fa fa-trash"></i>
                        </button>';
                    }
                    return $button;
                })
                ->rawColumns(['action', 'image', 'status'])
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
    public function store(StoreOfferRequest $request)
    {
        try {
            $dto = new OfferDto(
                strip_tags($request->input('title')),
                strip_tags($request->input('description')),
                $request->hasFile('image') ? $request->file('image') : null,
                $request->input('type'),
                $request->input('type_id'),
                $request->input('is_active'),
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
    public function update(StoreOfferRequest $request, Offer $offer)
    {
        try {
            $dto = new OfferDto(
                strip_tags($request->input('title')),
                strip_tags($request->input('description')),
                $request->hasFile('image') ? $request->file('image') : null,
                $request->input('type'),
                $request->input('type_id'),
                $request->input('is_active'),
                $offer->created_by,
                auth()->user()->id
            );
            $result = $this->service->update($dto, $offer->id);

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
            Log::error('Offer: Deletion failed: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong!'], 500);
        }
    }
}
