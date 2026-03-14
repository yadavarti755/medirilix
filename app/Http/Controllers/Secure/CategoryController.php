<?php

namespace App\Http\Controllers\Secure;

use App\DTO\CategoryDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct()
    {
        $this->categoryService = new CategoryService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageTitle = 'Categories';
        $categories = $this->categoryService->findForBackend();
        return view('secure.categories.index', compact('pageTitle', 'categories'));
    }

    /**
     * Fetch a listing of the resource.
     */
    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $categories = $this->categoryService->findAll();
            return DataTables::of($categories)
                ->addColumn('image', function ($category) {
                    if ($category->file_name) {
                        return "<img src=" . $category->image_path . " alt='Category Image' class='img-fluid' style='max-height: 70px;'>";
                    }

                    return '';
                })
                ->addColumn('action', function ($category) {
                    $button = '';
                    if (auth()->user()->can('view category')) {
                        $button .= '<a href="' . route('categories.show', $category->id) . '" class="btn btn-sm btn-primary" title="View"><i class="fa fa-eye"></i></a> ';
                    }

                    if (auth()->user()->can('edit category')) {
                        $button .= '<a href="' . route('categories.edit', $category->id) . '" class="btn btn-sm btn-warning" title="Edit"><i class="fa fa-edit"></i></a> ';
                    }

                    if (auth()->user()->can('delete category')) {
                        $button .= '<button class="btn btn-sm btn-danger delete-category" data-id="' . $category->id . '" title="Delete">
                            <i class="fa fa-trash"></i>
                        </button>';
                    }
                    return $button;
                })
                ->rawColumns(['action', 'image'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Add Categories';
        $categories = $this->categoryService->findAll();
        return view('secure.categories.create', compact('pageTitle', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        try {
            $categoryDto = new CategoryDto(
                strip_tags($request->input('name')),
                strip_tags($request->input('description')),
                $request->file('image'),
                $request->input('parent_id'),
                $request->input('order'),
                $request->input('is_published'),
                auth()->id(),
                auth()->id()
            );

            $category = $this->categoryService->create($categoryDto);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while saving category.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully!'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Category addition failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server failed while processing request'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function show(string $id)
    {
        $pageTitle = 'View Categories';
        $category = $this->categoryService->findById($id);
        return view('secure.categories.show', compact('category', 'pageTitle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pageTitle = 'Edit Categories';
        $category = $this->categoryService->findById($id);
        $categories = $this->categoryService->findAllForEdit($id);

        $categories = buildCategoryTree($categories);
        return view('secure.categories.edit', compact('category', 'pageTitle', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {
            $categoryDto = new CategoryDto(
                strip_tags($request->input('name')),
                strip_tags($request->input('description')),
                $request->hasFile('image') ? $request->file('image') : null,
                $request->input('parent_id'),
                $request->input('order'),
                $request->input('is_published'),
                $category->created_by,
                auth()->id()
            );

            $updated = $this->categoryService->update($categoryDto, $category->id);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while updating category.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully!'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Category updation failed: ' . $e->getMessage());
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
            $category = $this->categoryService->delete($id);
            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while deleting category.',
                ], 500);
            }

            return response()->json(['message' => 'Category moved to trash successfully!']);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Category deletion failed: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong!'], 500);
        }
    }

    public function publish(Request $request, Category $category)
    {
        try {
            $categoryDto = new CategoryDto(
                $category->name,
                $category->description,
                $category->image,
                $request->input('is_published'),
                $category->remarks,
                $category->created_by,
                auth()->user()->id,
            );

            $updated = $this->categoryService->publish($categoryDto, $category->id);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while publishing category.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Category published successfully!'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Category publishing failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateOrder(Request $request)
    {
        $order = $request->input('order');
        $this->updateMenuOrder($order, null);
        return response()->json(['success' => true]);
    }

    private function updateMenuOrder(array $items, $parentId)
    {
        foreach ($items as $index => $item) {
            $menu = $this->categoryService->findById($item['id']);
            $updatedMenu = $this->categoryService->updateOrder([
                'parent_id' => $parentId,
                'order' => $index,
                'updated_by' => auth()->user()->id,
            ], $item['id']);

            if (isset($item['children'])) {
                $this->updateMenuOrder($item['children'], $menu->id);
            }
        }
    }
}
