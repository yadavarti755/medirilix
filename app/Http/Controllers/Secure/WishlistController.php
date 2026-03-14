<?php

namespace App\Http\Controllers\Secure;

use App\DTO\WishlistDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWishlistRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use App\Services\ProductService;
use App\Services\WishlistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class WishlistController extends Controller
{
    protected $wishlistService;
    protected $productService;

    public function __construct()
    {
        $this->wishlistService = new WishlistService();
        $this->productService = new ProductService();
    }

    public function index()
    {
        $pageTitle = 'My Wishlist';
        $user_id = auth()->user()->id;
        $wishlists = $this->wishlistService->findByUser($user_id);

        return view('secure.wishlists.user_wishlist', compact('pageTitle', 'wishlists'));
    }

    // Function to delete wishlist
    public function destroy(Request $request)
    {
        try {
            $id = $request->id;
            $result = $this->wishlistService->delete($id);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Wishlist removed successfully.'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while removing wishlist.'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(StoreWishlistRequest $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login to add product in wishlist.'
                ], 401);
            }

            // Check if the product is already added in wishlist or not
            $wishlists = $this->wishlistService->findAll([
                'user_id' => Auth::user()->id,
                'product_id' => $request->product_id
            ]);

            if ($wishlists->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product is already added into wishlist.'
                ], 409); // Conflict
            }

            $dto = new WishlistDto(
                Auth::user()->id,
                $request->product_id,
                auth()->user()->id,
                auth()->user()->id
            );

            $result = $this->wishlistService->create($dto);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product added to wishlist.'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Server is not responding. Please try again.'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
