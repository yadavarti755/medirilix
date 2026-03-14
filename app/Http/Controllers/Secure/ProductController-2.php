<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Response;
use App\Models\Size;
use App\Models\Product;
use App\Models\Category;
use App\Models\Material;
use App\Models\ProductSize;
use App\Models\SubCategory;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\ProductMultipleImage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function allProducts()
    {
        return view('admin.products.all-products');
    }

    // Function to get all products in for data table;
    public function getAllProducts()
    {
        $query = Product::query();
        $query->when(request('category'), function ($q) {
            return $q->where('category_id', base64_decode(request('category')));
        });
        $query->when(request('product_listing_type'), function ($q) {
            return $q->where('product_listing_type', request('product_listing_type'));
        });
        $query->when(request('survey_status'), function ($q) {
            return $q->where('survey_status', request('survey_status'));
        });
        $query->when(request('product_name'), function ($q) {
            return $q->where('name', 'LIKE', '%' . request('product_name') . '%');
        });
        $query->when(request('product_sku'), function ($q) {
            return $q->where('product_sku', 'LIKE', '%' . request('product_sku') . '%');
        });

        $allProducts = $query->where(['status' => 1])->orderBy('id', 'desc')->get();

        return Datatables::of($allProducts)
            ->addColumn('action', function ($allProducts) {
                $button = "<a class='btn btn-primary btn-sm' href='" . url('/admin/product/edit/' . Crypt::encryptString($allProducts->id)) . "'><i class='fas fa-edit'></i></a> <button class='btn btn-danger btn-sm deleteProduct' id='" . base64_encode($allProducts->id) . "'><i class='fas fa-trash'></i></button>";
                return $button;
            })
            ->editColumn('category_id', function ($allProducts) {
                $categoryDetails = Category::where('id', $allProducts->category_id)->first();
                $categoryName = 'Uncategorized';
                if ($categoryDetails) {
                    $categoryName = $categoryDetails->name;
                }
                return $categoryName;
            })
            ->editColumn('sub_category_id', function ($allProducts) {
                $subcategoryDetails = SubCategory::where('id', $allProducts->sub_category_id)->first();
                $subcategoryName = 'Uncategorized';
                if ($subcategoryDetails) {
                    $subcategoryName = $subcategoryDetails->name;
                }
                return $subcategoryName;
            })
            ->editColumn('product_listing_type', function ($allProducts) {
                if ($allProducts->product_listing_type) {
                    return Config::get('constants.product_listing_type')[$allProducts->product_listing_type];
                }
                return '';
            })
            ->editColumn('stock_availability', function ($allProducts) {
                return Config::get('constants.stock_availability')[$allProducts->stock_availability];
            })
            ->editColumn('status', function ($allProducts) {
                $status = ($allProducts->status == 1) ? 'Active' : 'Inactive';
                return $status;
            })
            ->editColumn('featured_image', function ($allProducts) {
                $featured_image = asset('storage/product_images/' . $allProducts->featured_image);
                return $featured_image;
            })
            ->make(true);
    }

    // Function to show add view for product
    public function addProduct()
    {
        $categories = Category::where('status', 1)->get();
        $subcategories = Subcategory::where('status', 1)->get();
        $sizes = Size::where('status', 1)->get();
        $materials = Material::where('status', 1)->get();
        $operationType = 'ADD';
        $page_heading = 'Add New Product';
        $product = new Product; // For error handling in add page
        $encrypted_product_id = '';

        return view('admin.products.add-product', compact('categories', 'subcategories', 'operationType', 'product', 'page_heading', 'encrypted_product_id', 'sizes', 'materials'));
    }

    // Store products
    public function storeProduct(Request $request)
    {
        DB::beginTransaction();
        try {
            $inputs = [
                'name' => 'required',
                'product_sku' => 'required|unique:products,product_sku',
                'mrp' => 'required|numeric',
                'selling_price' => 'required|numeric',
                'quantity' => 'required|numeric',
                'stock_availability' => 'required|numeric',
                'category' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
            ];

            $validator = Validator::make($request->all(), $inputs);

            if ($validator->fails()) {
                return Response::json([
                    'status' => 'validation_error',
                    'message' => $validator->errors()->all()
                ]);
            } else {

                // Get image
                if ($request->hasFile('image')) {
                    $fileName = 'product-' . time() . '.' . $request->image->extension();
                    $request->file('image')->storeAs('public/product_images', $fileName);
                    if (!$request->file('image')->isValid()) {
                        return Response::json([
                            'status' => false,
                            'message' => 'Error while uploading image. Please try again.'
                        ]);
                    }
                } else {
                    return Response::json([
                        'status' => false,
                        'message' => 'Featured image is required.'
                    ]);
                };

                // Check if slug is exist or not
                $slug = Str::slug($request->name);
                $checkSlugResult = Product::where('slug', $slug)->get();
                if ($checkSlugResult->count() > 0) {
                    $slug = $slug . '-' . (count($checkSlugResult) + 1);
                }

                $result = Product::create([
                    'slug' => $slug,
                    'category_id' => base64_decode($request->category),
                    'sub_category_id' => base64_decode($request->subcategory),
                    'name' => $request->name,
                    'product_sku' => $request->product_sku,
                    'mrp' => $request->mrp,
                    'selling_price' => $request->selling_price,
                    'description' => $request->description,
                    'meta_keywords' => $request->meta_keywords,
                    'meta_description' => $request->meta_description,
                    'featured_image' => $fileName,
                    // 'material_id' => base64_decode($request->material),
                    'product_listing_type' => $request->product_listing_type,
                    'quantity' => $request->quantity,
                    'available_quantity' => $request->quantity,
                    'stock_availability' => $request->stock_availability,
                    'created_by' => Auth::user()->username,
                    'last_updated_by' => Auth::user()->username,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ]);

                if ($result) {
                    // Multiple images
                    if ($request->hasFile('multiple_product_image')) {
                        if ($files = $request->file('multiple_product_image')) {
                            $multipleImages = array();
                            foreach ($files as $file) {
                                if (!$file->isValid()) {
                                    return Response::json([
                                        'status' => false,
                                        'message' => 'Error while uploading multiple image. Please try again.'
                                    ]);
                                } else {
                                    $imageName = 'product-' . time() . '.' . $file->extension();
                                    $file->storeAs('public/product_images/product_multiple_images', $imageName);
                                    $multipleImages[] = [
                                        'product_id' => $result->id,
                                        'image_name' => $imageName,
                                        'created_at' => now(),
                                        'updated_at' => now()
                                    ];
                                }
                            }
                            ProductMultipleImage::insert($multipleImages);
                        }
                    }

                    // // Product Sizes
                    // if (count($request->size) > 0) {
                    //     $sizeArray = [];
                    //     foreach ($request->size as $key => $size) {
                    //         array_push($sizeArray, [
                    //             'product_id' => $result->id,
                    //             'size_id' => \base64_decode($size)
                    //         ]);
                    //     }
                    //     ProductSize::insert($sizeArray);
                    // }

                    DB::commit();
                    return Response::json([
                        'status' => true,
                        'message' => 'Product saved successfully.'
                    ]);
                } else {
                    DB::rollback();
                    return Response::json([
                        'status' => false,
                        'message' => 'Server is not responding. Please try again.'
                    ]);
                }
            }
        } catch (Exception $e) {
            DB::rollback();
            return Response::json([
                'status' => false,
                'message' => 'Something went wrong. Please try again.'
            ]);
        }
    }

    // Update products
    public function updateProduct(Request $request)
    {
        $product_id = Crypt::decryptString($request->product_id);

        $inputs = [
            'name' => 'required',
            'product_sku' => 'required|unique:products,product_sku,' . $product_id,
            'mrp' => 'required|numeric',
            'selling_price' => 'required|numeric',
            // 'material' => 'required',
            // 'size' => 'required',
            'quantity' => 'required|numeric',
            'stock_availability' => 'required|numeric',
            'category' => 'required',

            'image' => 'image|mimes:jpeg,png,jpg|max:5120'
        ];

        $validator = Validator::make($request->all(), $inputs);

        if ($validator->fails()) {
            return Response::json([
                'status' => false,
                'message' => $validator->errors()->all()
            ]);
        } else {

            // Get image
            if ($request->hasFile('image')) {
                $fileName = 'product-' . time() . '.' . $request->image->extension();
                $request->file('image')->storeAs('public/product_images', $fileName);
                if (!$request->file('image')->isValid()) {
                    return Response::json([
                        'status' => false,
                        'message' => 'Error while uploading image. Please try again.'
                    ]);
                }
            } else {
                $fileName = $request->hidden_featured_image;
            };

            // Check if the name of product is changed or not (To make slug)
            $productData = Product::where('id', $product_id)->first();
            if ($productData->name != $request->name) {
                // Check if slug is exist or not
                $slug = Str::slug($request->name);
                $checkSlugResult = Product::where('slug', $slug)->get();

                if ($checkSlugResult->count() > 0) {
                    $slug = $slug . '-' . (count($checkSlugResult) + 1);
                }
            } else {
                $slug = $productData->slug;
            }

            $result = Product::where('id', $product_id)->update([
                'slug' => $slug,
                'category_id' => base64_decode($request->category),
                'sub_category_id' => base64_decode($request->subcategory),
                'name' => $request->name,
                'product_sku' => $request->product_sku,
                'mrp' => $request->mrp,
                'selling_price' => $request->selling_price,
                'description' => $request->description,
                'meta_keywords' => $request->meta_keywords,
                'meta_description' => $request->meta_description,
                'featured_image' => $fileName,
                // 'material_id' => base64_decode($request->material),
                'product_listing_type' => $request->product_listing_type,
                'quantity' => $request->quantity,
                'available_quantity' => $request->quantity,
                'stock_availability' => $request->stock_availability,
                'last_updated_by' => Auth::user()->username,
                'updated_at' => $this->date
            ]);

            if ($result) {

                // Multiple images
                if ($request->hasFile('multiple_product_image')) {
                    if ($files = $request->file('multiple_product_image')) {
                        $multipleImages = array();
                        foreach ($files as $file) {
                            if (!$file->isValid()) {
                                return Response::json([
                                    'status' => false,
                                    'message' => 'Error while uploading multiple image. Please try again.'
                                ]);
                            } else {
                                $imageName = 'product-' . time() . '.' . $file->extension();
                                $file->storeAs('public/product_images/product_multiple_images', $imageName);
                                $multipleImages[] = [
                                    'product_id' => $product_id,
                                    'image_name' => $imageName,
                                    'updated_at' => now()
                                ];
                            }
                        }
                        ProductMultipleImage::insert($multipleImages);
                    }
                }

                return Response::json([
                    'status' => true,
                    'message' => 'Product updated successfully.'
                ]);
            } else {
                return Response::json([
                    'status' => false,
                    'message' => 'Server is not responding. Please try again.'
                ]);
            }
        }
    }

    // Function to delete product
    public function deleteProduct(Request $request)
    {
        $id = base64_decode($request->id);
        $data = Product::find($id);
        if ($data->count() > 0) {
            $result = Product::where(['id' => $id])->update([
                'status' => 0,
                'last_updated_by' => Auth::user()->username
            ]);

            if ($result) {
                $output = [
                    'status' => true,
                    'message' => 'Record deleted successfully.'
                ];
            } else {
                $output = [
                    'status' => false,
                    'message' => 'Server is not responding. Please try again.'
                ];
            }
        } else {
            $output = [
                'status' => false,
                'message' => 'Something went wrong. Please try again or contact support.'
            ];
        }

        return Response::json($output);
    }

    // Function to edit product
    public function editProduct()
    {
        $categories = Category::where('status', 1)->get();
        $subcategories = Subcategory::where('status', 1)->get();
        $sizes = Size::where('status', 1)->get();
        $materials = Material::where('status', 1)->get();

        $encrypted_product_id = request()->segment(4);
        $decrypted_product_id = Crypt::decryptString($encrypted_product_id);
        $product = Product::find($decrypted_product_id);

        $productAllImages = ProductMultipleImage::where([
            'product_id' => $product->id,
            'status' => 1
        ])->get();

        $page_heading = 'Edit New Product';
        // dd($product);
        $operationType = 'EDIT';
        return view('admin.products.add-product', compact('categories', 'subcategories', 'operationType', 'product', 'page_heading', 'encrypted_product_id', 'sizes', 'materials', 'productAllImages'));
    }

    // Function to delete product multiple imahe
    public function deleteProductMultipleImage(Request $request)
    {
        $inputs = [
            'id' => 'required',
            'product_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $inputs);

        if ($validator->fails()) {
            return Response::json([
                'status' => 'validation_error',
                'message' => $validator->errors()->all()
            ]);
        }

        $id = Crypt::decryptString($request->id);
        $productId = Crypt::decryptString($request->product_id);
        $data = ProductMultipleImage::find($id);

        if ($data->count() > 0) {
            $result = ProductMultipleImage::where(['id' => $id, 'product_id' => $productId])->update([
                'status' => 0,
                'updated_at' => now()
            ]);

            if ($result) {
                $output = [
                    'status' => true,
                    'message' => 'Image deleted successfully.'
                ];
            } else {
                $output = [
                    'status' => false,
                    'message' => 'Server is not responding. Please try again.'
                ];
            }
        } else {
            $output = [
                'status' => false,
                'message' => 'Something went wrong. Please try again or contact support.'
            ];
        }

        return Response::json($output);
    }
}
