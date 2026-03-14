<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class OfferController extends Controller
{
    // Construct
    public function __construct() {}

    public function offer(Request $request)
    {

        try {
            if (request()->has('type')) {
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

                // Default where condition
                $where = [
                    'status' => 1
                ];

                // ===================================================
                $type = customUrlDecode(request()->type);

                // Check type validity
                if (!in_array($type, ['UNDER_PRICE', 'START_PRICE', 'DISCOUNT_OFF'])) {
                    return redirect(route('shop'));
                }

                // Price filter products
                if (in_array($type, ['UNDER_PRICE', 'START_PRICE'])) {
                    $price = 0;
                    $filterSymbol = '>';

                    if ($type == 'UNDER_PRICE') {
                        if (request()->price) {
                            $price = customUrlDecode(request()->price);
                            $filterSymbol = '<';
                        }
                    }

                    if ($type == 'START_PRICE') {
                        if (request()->price) {
                            $price = customUrlDecode(request()->price);
                            $filterSymbol = '>';
                        }

                        if (request()->category) {
                            $where['category_id'] = Category::where('slug', 'LIKE', '%' . customUrlDecode(request()->category) . '%')->where('status', 1)->first()->id;
                        }
                    }

                    $data['all_products'] = Product::where('selling_price', $filterSymbol, $price)->where($where)->orderBy($orderBy, $orderType)->paginate($this->pagination_count_4_grid);
                }

                // Percentage discount of products =====================
                if ($type == 'DISCOUNT_OFF') {
                    $percent = customUrlDecode(request()->percent);
                    $filterSymbol = '>';

                    $query = Product::query();
                    $query->where(function ($query) use ($percent) {
                        $query->where(DB::raw('((mrp-selling_price)/mrp)*100'), '>=', $percent);
                    });

                    $data['all_products'] = $query->where(['status' => 1])->orderBy($orderBy, $orderType)->paginate($this->pagination_count_4_grid);
                }

                $data['pageTitle'] = 'Offer';
                return view('website.offer', $data);
            }
        } catch (Error $e) {
            return redirect(route('shop'));
        }
        return redirect(route('shop'));
    }
}
