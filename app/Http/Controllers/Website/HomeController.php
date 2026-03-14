<?php

namespace App\Http\Controllers\Website;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\PageRepository;
use App\Services\OurPartnerService;
use App\Services\MenuService;
use App\Services\SliderService;
use App\Services\SocialMediaService;
use App\Services\ProductService;
use App\Services\CategoryService;
use App\Services\ProductMultipleImageService;
use App\Services\SearchTagService;
use App\Services\AddressService;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;

class HomeController extends Controller
{
    public $pagination_count_3_grid = 18;
    public $pagination_count_4_grid = 20;

    protected $service;
    protected $menuService;
    protected $pageRepository;
    protected $sliderService;
    protected $ourPartnerService;
    protected $socialMediaService;
    protected $productService;
    protected $categoryService;
    protected $productMultipleImageService;
    protected $searchTagService;
    protected $addressService;
    protected $offerService;

    public function __construct()
    {
        $this->menuService         = new MenuService();
        $this->pageRepository      = new PageRepository();
        $this->sliderService       = new SliderService();
        $this->ourPartnerService   = new OurPartnerService();
        $this->socialMediaService  = new SocialMediaService();
        $this->productService      = new ProductService();
        $this->categoryService     = new CategoryService();
        $this->productMultipleImageService = new ProductMultipleImageService();
        $this->searchTagService    = new SearchTagService();
        $this->addressService      = new AddressService();
        $this->offerService        = new \App\Services\OfferService();
    }

    // Homepage
    public function index()
    {
        $recentProducts = $this->productService->findAll([
            'stock_availability'   => 1
        ], 24);

        $saleProducts = $this->productService->findAll([
            'product_listing_type' => 4,
            'stock_availability'   => 1
        ], 24);

        $trendingProducts = $this->productService->findAll([
            'product_listing_type' => 1,
            'stock_availability'   => 1
        ], 24);

        $categories = $this->categoryService->findAll();
        $sliders = $this->sliderService->findForPublic();
        $offers = $this->offerService->findForPublic([
            'is_active' => 1
        ]);

        return view('website.homepage', compact(
            'recentProducts',
            'trendingProducts',
            'categories',
            'sliders',
            'saleProducts',
            'offers'
        ));
    }

    // Search page
    public function search(Request $request)
    {
        if (!$request->has('q') || $request->q == '') {
            return redirect('homepage');
        }

        $searchQuery = $request->q;

        // Sorting
        $orderBy = 'id';
        $orderType = 'DESC';
        $data['sort_filter'] = '';

        if ($request->get('sort')) {
            $data['sort_filter'] = base64_decode(urldecode($request->get('sort')));
            [$orderBy, $orderType] = $this->productService->resolveSorting($data['sort_filter']);
        }

        $data['all_products'] = $this->productService->searchProducts(
            $searchQuery,
            ['stock_availability' => 1],
            $orderBy,
            $orderType,
            $this->pagination_count_3_grid
        );

        $data['pageTitle'] = ucfirst($searchQuery);
        $data['categories'] = $this->categoryService->findAll();
        $data['price'] = $this->productService->getMinMaxPrice();
        $data['slug'] = '';

        return view('website.shop', $data);
    }

    // Order summary
    // public function orderSummary()
    // {
    //     $cart = session()->get('cart', []);
    //     if (count($cart) < 1 || !Auth::check()) {
    //         return redirect('/cart');
    //     }

    //     $address = $this->addressService->getSelectedAddress(
    //         Auth::user()->id,
    //         session()->get('shipping_address')['address_id']
    //     );

    //     return view('website.order-summary', compact('address'))
    //         ->with(['pageTitle' => 'Order Summary']);
    // }

    // Search suggestions
    public function searchHints(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search_query' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'validation_error',
                'message' => $validator->errors()->all()
            ]);
        }

        $data = [];

        $categories  = $this->categoryService->searchByName($request->search_query, 4);
        $products    = $this->productService->searchNames($request->search_query, 4);

        foreach ($categories as $cat) {
            $data[] = '<a href="' . route('shop', $cat->slug) . '">' . $cat->name . '</a>';
        }

        foreach ($products as $prod) {
            $data[] = '<a href="' . route('product-details', $prod->slug) . '">' . $prod->name . '</a>';
        }

        if ($data) {
            return response()->json([
                'status' => true,
                'data'   => $data
            ]);
        }

        return response()->json([
            'status'      => false,
            'status_code' => 401,
            'message'     => 'Server is not responding. Please try again.'
        ]);
    }

    public function responseError()
    {
        if (!session()->has('error_message')) {
            return redirect('page-not-found');
        }

        $error_message = session()->get('error_message');
        return view('errors.response-error', compact('error_message'))
            ->with(['pageTitle' => 'Response Error']);
    }
}
