<?php

namespace App\Http\Controllers\Website;

use App\DTO\ProductFilterDto;
use Auth;
use Illuminate\Support\Facades\Auth as AuthFacade;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\PageRepository;
use App\Services\OurPartnerService;
use App\Services\MenuService;
use App\Services\SliderService;
use App\Services\SocialMediaService;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductMultipleImage;
use App\Services\CategoryService;
use App\Services\ProductMultipleImageService;
use App\Services\ProductService;
use App\Services\CustomerReviewService;
use App\Services\OrderProductListService;
use Illuminate\Support\Facades\Config;

class ProductController extends Controller
{
    public $pagination_count_3_grid = 9;
    public $pagination_count_4_grid = 12;

    protected $menuService;
    protected $announcementService;
    protected $pageRepository;
    protected $sliderService;
    protected $ourPartnerService;
    protected $socialMediaService;
    protected $productService;
    protected $productMultipleImageService;
    protected $categoryService;
    protected $brandService;
    protected $customerReviewService;
    protected $orderProductListService;

    // Construct
    public function __construct()
    {
        $this->menuService = new MenuService();
        $this->pageRepository = new PageRepository();
        $this->sliderService = new SliderService();
        $this->ourPartnerService = new OurPartnerService();
        $this->socialMediaService = new SocialMediaService();
        $this->productService = new ProductService();
        $this->categoryService = new CategoryService();
        $this->productMultipleImageService = new ProductMultipleImageService();
        $this->brandService = new \App\Services\BrandService();
        $this->customerReviewService = new CustomerReviewService();
        $this->orderProductListService = new OrderProductListService();
    }

    public function shop(Request $request, $slug = null)
    {
        if ($request->ajax()) {

            // Decrypt custom encoded parameters if present
            $categories = $request->categories ?? [];

            // if slug is passed in request, treat it as a category filter
            if ($request->category_slug) {
                $categories[] = $request->category_slug;
            }

            $types = $request->types ?? [];
            if ($request->type) {
                // Handle legacy single type param if needed, but new system uses array
                $decodedType = customUrlDecode($request->type);
                //  Map decoded type back to key if possible or just use code
                $types[] = $decodedType;
            }

            $dto = new ProductFilterDto(
                $request->search ?? $request->q,
                $categories,
                $request->brands ?? [],
                $request->min_price,
                $request->max_price,
                $types,
                $request->sort ? base64_decode(urldecode($request->sort)) : null,
                $this->pagination_count_3_grid
            );

            $data['all_products'] = $this->productService->filterProducts($dto);

            $view = view('website.partials.shop-product-list', $data)->render();

            return response()->json([
                'status' => true,
                'html' => $view,
                'count' => $data['all_products']->count(),
                'total' => $data['all_products']->total()
            ]);
        }

        // Initial Page Load
        $data['categories'] = $this->categoryService->findAll();
        $data['brands'] = $this->brandService->findForPublic();
        $data['price'] = $this->productService->getMinMaxPrice();
        $data['slug'] = $slug;

        // Populate filter options for view
        $data['types'] = Config::get('constants.filter_by_type_code');


        // We don't load products here anymore, the page will trigger an AJAX call on load
        // OR we can pre-load for SEO. Let's pre-load using the same DTO logic for SEO purposes.

        $categories = [];
        if ($slug) {
            $categories[] = $slug;
        }

        // Check for request params on initial load (e.g. shared link)
        $types = [];
        if ($request->get('type')) {
            $types[] = \customUrlDecode($request->get('type'));
        }


        $dto = new ProductFilterDto(
            $request->input('q') ?? $request->input('search'),
            $categories,
            [],
            null,
            null,
            $types,
            null,
            $this->pagination_count_3_grid
        );

        $data['all_products'] = $this->productService->filterProducts($dto);


        $data['pageTitle'] = $slug ? ucfirst(str_replace('-', ' ', $slug)) : 'Shop';
        return view('website.shop', $data);
    }

    public function search(Request $request)
    {
        if ($request->has('q') && $request->q != '') {
            $searchQuery = $request->q;

            // Order the data by selected option
            $orderBy = 'id';
            $orderType = 'DESC';
            $data['sort_filter'] = '';
            if ($request->get('sort')) {
                $data['sort_filter'] = base64_decode(urldecode($request->get('sort')));

                if (in_array($data['sort_filter'], ['SORT_POPULARITY', 'SORT_LATEST', 'PRICE_LOW_TO_HIGH', 'PRICE_HIGH_TO_LOW'])) {
                    if ($data['sort_filter'] == 'SORT_POPULARITY') {
                        $orderBy = 'view_count';
                    }
                    if ($data['sort_filter'] == 'SORT_LATEST') {
                        $orderBy = 'id';
                    }
                    if ($data['sort_filter'] == 'PRICE_LOW_TO_HIGH') {
                        $orderBy = 'selling_price';
                        $orderType = 'ASC';
                    }
                    if ($data['sort_filter'] == 'PRICE_HIGH_TO_LOW') {
                        $orderBy = 'selling_price';
                        $orderType = 'DESC';
                    }
                }
            }


            $data['all_products'] = Product::where('name', 'LIKE', '%' . $searchQuery . '%')->where(['status' => 1, 'stock_availability' => 1])->orderBy($orderBy, $orderType)->paginate($this->pagination_count_3_grid);

            $data['pageTitle'] = \ucfirst($searchQuery);

            $data['categories'] = $this->categoryService->findAll();
            $data['price'] = [
                'min_price' => Product::min('selling_price'),
                'max_price' => Product::max('selling_price'),
            ];

            $data['slug'] = '';

            return view('website.shop', $data);
        } else {
            return redirect('homepage');
        }
    }

    public function showProduct(Request $request, $slug)
    {
        if ($slug) {
            $product = $this->productService->findBySlug($slug);
            $metaKeywords = $product->meta_keywords;
            $metaDescription = $product->meta_description;

            if (!$product) {
                return redirect('page-not-found');
            }

            // Update the view count
            $this->productService->incrementViewCount($slug);

            $data['product'] = $product;
            $data['related_products'] = $this->productService->getRelatedProducts($product->category_id, $slug);
            $data['productAllImages'] = $this->productMultipleImageService->findByProduct($product->id);
            $data['product_details'] = $product;

            // Customer Reviews
            $data['reviews'] = $this->customerReviewService->findByProductId($product->id);
            $data['canReview'] = false;

            if (Auth::check()) {
                $purchases = $this->orderProductListService->findAll([
                    'user_id' => Auth::id(),
                    'product_id' => $product->id
                ]);
                if ($purchases->isNotEmpty()) {
                    $data['canReview'] = true;
                }
            }

            return view('website.product-details', $data)->with([
                'pageTitle' => $product->name,
                'metaKeywords' => $metaKeywords,
                'metaDescription' => $metaDescription,
            ]);
        } else {
            return redirect('page-not-found');
        }
    }
}
