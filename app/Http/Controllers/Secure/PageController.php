<?php

namespace App\Http\Controllers\Secure;

use Purifier;
use App\DTO\PageDto;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Models\Page;

use Illuminate\Http\Request;
use App\Services\MenuService;

use App\Services\PageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class PageController extends Controller
{
    protected $menuService;
    protected $pageService;
    protected $pageFileService;

    public function __construct()
    {
        $this->menuService = new MenuService();
        $this->pageService = new PageService();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageTitle = 'Pages';
        return view('secure.pages.index', compact('pageTitle'));
    }

    /**
     * Fetch a listing of the resource.
     */
    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $users = $this->pageService->findAllForDatatable();
            return DataTables::of($users)
                ->addColumn('action', function ($page) {
                    $button = '';
                    if (auth()->user()->can('view page')) {
                        $button .= '<a href="' . route('pages.show', $page->id) . '" class="btn btn-sm btn-primary" title="View"><i class="fa fa-eye"></i></a> ';
                    }

                    if (auth()->user()->can('edit page')) {
                        $button .= '<a href="' . route('pages.edit', $page->id) . '" class="btn btn-sm btn-warning" title="Edit"><i class="fa fa-edit"></i></a> ';
                    }

                    if (auth()->user()->can('delete page')) {
                        $button .= '<button class="btn btn-sm btn-danger delete-page" data-id="' . $page->id . '" title="Delete">
                            <i class="fa fa-trash"></i>
                        </button>';
                    }
                    return $button;
                })
                ->editColumn('slug', function ($page) {
                    return url()->to($page->slug);
                })
                ->rawColumns(['roles', 'action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Create Page';
        $menus = $this->menuService->findAll();
        return view('secure.pages.create', compact('pageTitle', 'menus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreatePageRequest $request)
    {
        DB::beginTransaction();
        try {
            // Create a PageDto instance with validated request data
            $pageDto = new PageDto(
                $request->input('menu_id') ? strip_tags($request->input('menu_id')) : 0,
                strip_tags($request->input('title')),
                Purifier::clean($request->input('content')) ?? null,
                0, // is_published
                auth()->user()->id, // created_by
                auth()->user()->id // updated_by
            );

            // Use the PageService to create a new page
            $page = $this->pageService->create($pageDto);

            if (!$page) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Error while saving page.',
                ], 500);
            }



            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Page created successfully!',
                'redirect_url' => route('pages.edit', $page->id),
            ], 201);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Page creation failed: ' . $e->getMessage());

            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pageTitle = 'View Page';
        $page = $this->pageService->findById($id);
        return view('secure.pages.show', compact('page', 'pageTitle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Page $page)
    {
        $pageTitle = 'Edit Page';
        $menus = $this->menuService->findAll();
        return view('secure.pages.edit', compact('pageTitle', 'menus', 'page'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePageRequest $request, Page $page)
    {
        DB::beginTransaction();
        try {
            // Create a PageDto instance with validated request data
            $pageDto = new PageDto(
                $request->input('menu_id') ? strip_tags($request->input('menu_id')) : 0,
                strip_tags($request->input('title')),
                Purifier::clean($request->input('content')) ?? null,
                $page->is_published,
                $page->created_by,
                auth()->user()->id
            );

            // Use the PageService to create a new page
            $page = $this->pageService->update($pageDto, $page->id);

            if (!$page) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while saving page.',
                ], 500);
            }



            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Page updated successfully!',
                'redirect_url' => route('pages.edit', $page->id),
            ], 201);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Page updation failed: ' . $e->getMessage());

            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Page $page)
    {
        DB::beginTransaction();
        try {
            $result = $this->pageService->delete($page->id);
            if (!$result) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Error while deleting page.',
                ], 500);
            }



            DB::commit();
            return response()->json(['message' => 'Page deleted successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Something went wrong!'], 500);
        }
    }





    public function publish(Request $request, Page $page)
    {
        try {
            $pageDto = new PageDto(
                $page->menu_id,
                $page->title,
                $page->content,
                $request->input('is_published'),
                $page->created_by,
                auth()->user()->id
            );

            $updated = $this->pageService->publish($pageDto, $page->id);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while publishing page.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Page published successfully!'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Page publishing failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
