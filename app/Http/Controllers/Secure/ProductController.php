<?php

namespace App\Http\Controllers\Secure;

use Log;
use App\DTO\ProductDto;
use Response;
use Purifier;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\BrandService;
use App\Services\CategoryService;
use App\Services\IntendedUseService;
use App\Services\MaterialService;
use App\Services\ProductService;
use App\Services\ProductTypeService;
use App\Services\SizeService;
use App\Services\UnitTypeService;
use App\Services\ProductOtherSpecificationService;
use App\Services\ReturnPolicyService;
use App\DTO\ProductOtherSpecificationDto;
use App\Services\ProductMultipleImageService;
use App\DTO\ProductMultipleImageDto;
use App\Services\CountryService;

class ProductController extends Controller
{
    protected $productService;
    protected $categoryService;
    protected $materialService;
    protected $productTypeService;
    protected $brandService;
    protected $intendedUseService;
    protected $unitTypeService;
    protected $countryService;
    protected $otherSpecService;
    protected $returnPolicyService;
    protected $productMultipleImageService;

    public function __construct()
    {
        $this->productService = new ProductService();
        $this->categoryService    = new CategoryService();
        $this->materialService    = new MaterialService();
        $this->productTypeService = new ProductTypeService();
        $this->brandService       = new BrandService();
        $this->intendedUseService = new IntendedUseService();
        $this->unitTypeService   = new UnitTypeService();
        $this->otherSpecService = new ProductOtherSpecificationService();
        $this->returnPolicyService = new ReturnPolicyService();
        $this->productMultipleImageService = new ProductMultipleImageService();
        $this->countryService    = new CountryService();
    }

    public function index()
    {
        $pageTitle = "Product";
        $categories      = $this->categoryService->findAll();
        $materials       = $this->materialService->findAll();
        $productTypes    = $this->productTypeService->findAll();
        $brands          = $this->brandService->findAll();
        return view('secure.products.index', compact(
            'pageTitle',
            'categories',
            'materials',
            'productTypes',
            'brands'
        ));
    }

    // Function to get all products in for data table;
    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $products = $this->productService->findForDatatable();
            return DataTables::of($products)
                ->addColumn('action', function ($product) {
                    $button = '';
                    if (auth()->user()->can('view product')) {
                        $button .= '<a href="' . route('products.show', $product->id) . '" class="btn btn-sm btn-primary" title="View"><i class="fa fa-eye"></i></a> ';
                    }

                    if (auth()->user()->can('edit product')) {
                        $button .= '<a href="' . route('products.edit', $product->id) . '" class="btn btn-sm btn-warning" title="Edit"><i class="fa fa-edit"></i></a> ';
                    }

                    if (auth()->user()->can('delete product')) {
                        $button .= '<button class="btn btn-sm btn-danger btn-delete" data-id="' . $product->id . '" title="Delete">
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
            $data = $this->productService->findById($id);
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Add Products';

        $categories        = $this->categoryService->findAll();
        $materials         = $this->materialService->findAll();
        // $sizes             = $this->sizeService->findAll(); // Removed
        $productTypes      = $this->productTypeService->findAll();
        $brands            = $this->brandService->findAll();
        $intendedUses      = $this->intendedUseService->findAll();
        $unitTypes         = $this->unitTypeService->findAll();
        $returnPolicies    = $this->returnPolicyService->all();
        $countries         = $this->countryService->findAll();

        return view('secure.products.create', compact(
            'pageTitle',
            'categories',
            'materials',
            // 'sizes', // Removed
            'productTypes',
            'brands',
            'intendedUses',
            'unitTypes',
            'returnPolicies',
            'countries'
        ));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        try {
            $return_till_days = null;
            $return_description = null;

            if ($request->has('return_policy_id') && $request->return_policy_id) {
                $policy = $this->returnPolicyService->find($request->return_policy_id);
                if ($policy) {
                    $return_till_days = $policy->return_till_days;
                    $return_description = $policy->return_description;
                }
            }

            $dto = new ProductDto(
                $request->input('category_id'),
                strip_tags($request->input('name')),
                $request->input('mrp'),
                $request->input('selling_price'),
                $request->input('upc'),
                $request->input('brand_id'),
                $request->input('type_id'),
                $request->input('intended_use_id'),
                strip_tags($request->input('model')),
                strip_tags($request->input('mpn')),
                $request->input('expiration_date'),
                $request->input('california_prop_65_warning'),
                $request->input('country_of_origin'),
                $request->input('unit_quantity'),
                $request->input('unit_type_id'),
                $return_till_days,
                $return_description,
                $request->input('return_policy_id'),
                // $request->input('size_id', []), // Removed
                Purifier::clean($request->input('description')) ?? null,
                $request->hasFile('featured_image') ? $request->file('featured_image') : null,
                strip_tags($request->input('meta_keywords')),
                strip_tags($request->input('meta_description')),
                $request->input('material_id'),
                $request->input('product_listing_type', 0),
                $request->input('quantity'),
                $request->input('quantity'),
                $request->input('stock_availability', 1),
                $request->input('is_published', 1),
                auth()->user()->id,
                auth()->user()->id
            );

            $product = $this->productService->create($dto);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while saving product.',
                ], 500);
            }

            // Save Other Specifications
            if ($request->has('other_specs')) {
                foreach ($request->input('other_specs') as $spec) {
                    if (!empty($spec['label']) && !empty($spec['value'])) {
                        $specDto = new ProductOtherSpecificationDto(
                            $product->id,
                            $spec['label'],
                            $spec['value'],
                            auth()->user()->id,
                            auth()->user()->id
                        );
                        $this->otherSpecService->create($specDto);
                    }
                }
            }

            // Store multiple images
            if ($request->hasFile('multiple_product_image')) {
                foreach ($request->file('multiple_product_image') as $image) {
                    $imageDto = new ProductMultipleImageDto(
                        $product->id,
                        $image,
                        auth()->user()->id,
                        auth()->user()->id
                    );
                    $this->productMultipleImageService->create($imageDto);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully!'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Product addition failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server failed while processing request'
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pageTitle = 'View Products';
        $product = $this->productService->findById($id);
        return view('secure.products.show', compact('product', 'pageTitle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pageTitle = 'Edit Products';

        $product = $this->productService->findById($id);

        $categories        = $this->categoryService->findAll();
        $materials         = $this->materialService->findAll();
        // $sizes             = $this->sizeService->findAll();
        $productTypes      = $this->productTypeService->findAll();
        $brands            = $this->brandService->findAll();
        $intendedUses      = $this->intendedUseService->findAll();
        $unitTypes         = $this->unitTypeService->findAll();
        $returnPolicies    = $this->returnPolicyService->all();
        $countries         = $this->countryService->findAll();

        return view('secure.products.edit', compact(
            'product',
            'pageTitle',
            'categories',
            'materials',
            // 'sizes',
            'productTypes',
            'brands',
            'intendedUses',
            'unitTypes',
            'returnPolicies',
            'countries'
        ));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        try {
            $return_till_days = $product->return_till_days;
            $return_description = $product->return_description;

            if ($request->has('return_policy_id') && $request->return_policy_id) {
                $policy = $this->returnPolicyService->find($request->return_policy_id);
                if ($policy) {
                    $return_till_days = $policy->return_till_days;
                    $return_description = $policy->return_description;
                }
            }

            $dto = new ProductDto(
                $request->input('category_id', $product->category_id),
                strip_tags($request->input('name', $product->name)),
                $request->input('mrp', $product->mrp),
                $request->input('selling_price', $product->selling_price),
                $request->input('upc', $product->upc),
                $request->input('brand_id', $product->brand_id),
                $request->input('type_id', $product->type_id),
                $request->input('intended_use_id', $product->intended_use_id),
                strip_tags($request->input('model', $product->model)),
                strip_tags($request->input('mpn', $product->mpn)),
                $request->input('expiration_date', $product->expiration_date),
                $request->input('california_prop_65_warning', $product->california_prop_65_warning),
                $request->input('country_of_origin', $product->country_of_origin),
                $request->input('unit_quantity', $product->unit_quantity),
                $request->input('unit_type_id', $product->unit_type_id),
                $return_till_days,
                $return_description,
                $request->input('return_policy_id', $product->return_policy_id),
                // $request->input('size_id') ?: [], // Removed
                Purifier::clean($request->input('description')) ?? null,
                $request->hasFile('featured_image') ? $request->file('featured_image') : null,
                strip_tags($request->input('meta_keywords', $product->meta_keywords)),
                strip_tags($request->input('meta_description', $product->meta_description)),
                $request->input('material_id', $product->material_id),
                $request->input('product_listing_type', $product->product_listing_type),
                $request->input('quantity', $product->quantity),
                $request->input('available_quantity', $product->available_quantity),
                $request->input('stock_availability', $product->stock_availability),
                $request->input('is_published', $product->is_published),
                $product->created_by,
                auth()->user()->id
            );
            $productId = $product->id;
            $updated = $this->productService->update($dto, $productId);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while updating record.',
                ], 500);
            }

            // Update Other Specifications
            $existingSpecs = $this->otherSpecService->findByProductId($productId);
            $inputSpecs = $request->input('other_specs', []);

            // Collect IDs present in input
            $inputSpecIds = [];

            if (!empty($inputSpecs)) {
                foreach ($inputSpecs as $specData) {
                    if (!empty($specData['label']) && !empty($specData['value'])) {
                        if (isset($specData['id']) && $specData['id']) {
                            $inputSpecIds[] = $specData['id'];
                            // Update
                            $specDto = new ProductOtherSpecificationDto(
                                $product->id,
                                $specData['label'],
                                $specData['value'],
                                null,
                                auth()->user()->id
                            );
                            $this->otherSpecService->update($specDto, $specData['id']);
                        } else {
                            // Create
                            $specDto = new ProductOtherSpecificationDto(
                                $product->id,
                                $specData['label'],
                                $specData['value'],
                                auth()->user()->id,
                                auth()->user()->id
                            );
                            $this->otherSpecService->create($specDto);
                        }
                    }
                }
            }

            // Delete removed specs
            foreach ($existingSpecs as $existing) {
                if (!in_array($existing->id, $inputSpecIds)) {
                    $this->otherSpecService->delete($existing->id);
                }
            }

            // Store multiple images
            if ($request->hasFile('multiple_product_image')) {
                foreach ($request->file('multiple_product_image') as $image) {
                    $imageDto = new ProductMultipleImageDto(
                        $product->id,
                        $image,
                        auth()->user()->id,
                        auth()->user()->id
                    );
                    $this->productMultipleImageService->create($imageDto);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Record updated successfully!'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Product: updation failed: ' . $e->getMessage());
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
            $product = $this->productService->delete($id);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while deleting record.',
                ], 500);
            }

            return response()->json(['success' => true, 'message' => 'Record moved to trash successfully!']);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Product: deletion failed: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong!'], 500);
        }
    }


    public function publish(Request $request, Product $product)
    {
        try {
            $dto = new ProductDto(
                $product->category_id,
                $product->mrp,
                $product->selling_price,
                $product->upc,
                $product->brand_id,
                $product->type_id,
                $product->intended_use_id,
                $product->model,
                $product->mpn,
                $product->expiration_date,
                $product->california_prop_65_warning,
                $product->country_of_origin,
                $product->unit_quantity,
                $product->unit_type_id,
                $product->return_till_days,
                $product->return_description,
                [], // Sizes not needed for publish toggle, or pass empty/existing? Passing empty might wipe them if update is naive.
                // Since this creates a full DTO and calls update (or publish which will call update), we should ideally pass existing sizes.
                // But obtaining them is hard here without loading.
                // Let's pass [] and handle "null" in Service update logic to NOT wipe if null/empty?
                // Actually my plan for ProductService::update was: if (!is_null($dto->sizes)) delete and create.
                // ProductDto default sizes is [] (empty array).
                // If I pass [], it will wipe sizes. I should pass NULL if I don't want to change.
                // But ProductDto constructor expects array? No, $sizes = [].
                // If I pass $product->sizes->pluck('id')->toArray(), that would work.
                $product->description,
                $product->featured_image,
                $product->meta_keywords,
                $product->meta_description,
                $product->material_id,
                $product->product_listing_type,
                $product->quantity,
                $product->available_quantity,
                $product->stock_availability,
                $request->input('is_published', 1),
                $product->created_by,
                auth()->user()->id,
            );

            $updated = $this->productService->publish($dto, $product->id);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while publishing record.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Record published successfully!'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Product: publishing failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function deleteImage($id)
    {
        try {
            $deleted = $this->productMultipleImageService->delete($id);
            if ($deleted) {
                return response()->json(['success' => true, 'message' => 'Image deleted successfully']);
            }
            return response()->json(['success' => false, 'message' => 'Image not found or could not be deleted'], 404);
        } catch (\Exception $e) {
            Log::error('Product Image delete failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }
}
