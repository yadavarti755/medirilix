<?php

namespace App\Http\Controllers\Secure;

use App\DTO\CouponDto;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCouponRequest;
use App\Services\CouponService;
use App\Services\ProductService;
use App\Services\CategoryService;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{
    protected $service;
    protected $productService;
    protected $categoryService;

    public function __construct()
    {
        $this->service = new CouponService();
        $this->productService = new ProductService();
        $this->categoryService = new CategoryService();
    }

    public function index()
    {
        $pageTitle = "Coupons";
        // Fetch active/published items via services
        // Assuming findAll accepts WHERE conditions or returns all. Check Repository implementation if arrays work.
        // ProductService::findAll($where, $limit). Passing limit -1 or large number for all? 
        // Default limit is 10. We need ALL active products for the dropdown.
        // Let's assume findAll takes ['is_published' => 1] and limit.
        $products = $this->productService->findAll(['is_published' => 1], -1);

        // CategoryService::findForPublic() usually returns active categories (or use findAll if public is restricted)
        $categories = $this->categoryService->findForPublic();

        return view('secure.coupons.index', compact('pageTitle', 'products', 'categories'));
    }

    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $coupons = $this->service->findAll();
            return DataTables::of($coupons)
                ->addColumn('status', function ($coupon) {
                    return $coupon->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('action', function ($coupon) {
                    $button = '';
                    if (auth()->user()->can('edit coupon')) {
                        $button .= '<button type="button" data-id="' . $coupon->id . '" class="btn btn-sm btn-warning btn-edit" title="Edit"><i class="fa fa-edit"></i></button> ';
                    }

                    if (auth()->user()->can('delete coupon')) {
                        $button .= '<button class="btn btn-sm btn-danger btn-delete" data-id="' . $coupon->id . '" title="Delete">
                            <i class="fa fa-trash"></i>
                        </button>';
                    }
                    return $button;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
    }

    public function fetchOne(Request $request, $id)
    {
        try {
            $data = $this->service->findById($id);
            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            Log::error('Coupon fetch failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server failed while processing request'
            ], 500);
        }
    }

    public function store(StoreCouponRequest $request)
    {
        try {
            $dto = new CouponDto(
                strtoupper($request->input('code')),
                $request->input('description'),
                $request->input('discount_type'),
                $request->input('value'),
                $request->input('min_spend'),
                $request->input('max_discount'),
                $request->input('usage_limit_per_coupon'),
                $request->input('usage_limit_per_user'),
                $request->input('start_date'),
                $request->input('end_date'),
                $request->input('is_active', true) ? true : false,
                $request->input('product_ids', []),
                $request->input('category_ids', []),
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
                'message' => 'Coupon created successfully!'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Coupon addition failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server failed while processing request'
            ], 500);
        }
    }

    public function update(StoreCouponRequest $request, Coupon $coupon)
    {
        try {
            $dto = new CouponDto(
                strtoupper($request->input('code')),
                $request->input('description'),
                $request->input('discount_type'),
                $request->input('value'),
                $request->input('min_spend'),
                $request->input('max_discount'),
                $request->input('usage_limit_per_coupon'),
                $request->input('usage_limit_per_user'),
                $request->input('start_date'),
                $request->input('end_date'),
                $request->boolean('is_active'),
                $request->input('product_ids', []),
                $request->input('category_ids', []),
                $coupon->created_by,
                auth()->user()->id
            );

            $result = $this->service->update($dto, $coupon->id);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while updating record.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Coupon updated successfully!'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Coupon update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

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
            return response()->json(['message' => 'Coupon deleted successfully!']);
        } catch (\Exception $e) {
            Log::error('Coupon: Deletion failed: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong!'], 500);
        }
    }
}
