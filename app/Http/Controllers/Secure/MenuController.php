<?php

namespace App\Http\Controllers\Secure;

use App\Models\Menu;
use App\Http\Controllers\Controller;
use App\Services\MenuLocationService;
use App\Services\MenuService;
use App\DTO\MenuDto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    protected $menuLocationService;
    protected $menuService;

    public function __construct()
    {
        // Initialize the services
        $this->menuLocationService = new MenuLocationService();
        $this->menuService = new MenuService();
    }
    public function __destruct()
    {
        // Clean up resources if needed
        unset($this->menuLocationService);
        unset($this->menuService);
    }

    public function index()
    {
        $pageTitle = 'Menu';
        // Fetch Locations
        $locations = $this->menuLocationService->findAll();

        $filterLocation = request()->get('location');
        if ($filterLocation) {
            $firstLocation = $locations->where('location_code', $filterLocation)->first();
            if (!$firstLocation) {
                return redirect()->route('menus.index')->with('error', 'Invalid location selected.');
            }
        } else {
            $firstLocation = $locations->first();
        }

        // Fetch all menus with their children
        $menus = $this->menuService->findForIndex($firstLocation->location_code);

        return view('secure.menus.index', compact('menus', 'pageTitle', 'firstLocation', 'locations'));
    }

    public function create()
    {
        $pageTitle = 'Add New Menu';
        $menus = $this->menuService->findAll();
        $menus = buildMenuTree($menus);
        // Fetch Locations
        $locations = $this->menuLocationService->findAll();
        return view('secure.menus.create', compact('menus', 'pageTitle', 'locations'));
    }

    public function store(Request $request)
    {
        // Define validation rules
        $rules = [
            'title' => 'required|string|max:255',

            'url' => 'nullable',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'required|integer',
            'location' => 'required|string',
            'permission_name' => 'nullable|string|exists:permissions,name',
        ];

        // Create a validator instance and validate the request data
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors occurred.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $menuDto = new MenuDto(
                strip_tags($request->input('title')),
                // strip_tags($request->input('title_hi')), // Removed
                strip_tags($request->input('url')),
                $request->input('parent_id') ? strip_tags($request->input('parent_id')) : null,
                strip_tags($request->input('order')),
                strip_tags($request->input('location')),
                strip_tags($request->input('permission_name')),
                auth()->user()->id,
                auth()->user()->id
            );

            $menu = $this->menuService->create($menuDto);

            // Return a success response
            return response()->json([
                'success' => true,
                'message' => 'Menu created successfully.',
                'data' => $menu,
            ], 201);
        } catch (Exception $e) {
            // Log the exception or handle it as needed

            // Return an error response
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the menu.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        $pageTitle = 'Edit Menu';
        $menu = $this->menuService->findById($id);
        $menus = $this->menuService->findAllExcluding($id);
        $menus = buildMenuTree($menus); // Assuming helper function handles collection
        // Fetch Locations
        $locations = $this->menuLocationService->findAll();
        return view('secure.menus.edit', compact('menu', 'menus', 'pageTitle', 'locations'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',

            'url' => 'nullable',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'required|integer',
            'location' => 'required|string',
            'permission_name' => 'nullable|string|exists:permissions,name',
        ]);

        try {
            $menuDto = new MenuDto(
                strip_tags($request->input('title')),
                // strip_tags($request->input('title_hi')), // Removed
                strip_tags($request->input('url')),
                $request->input('parent_id') ? strip_tags($request->input('parent_id')) : null,
                strip_tags($request->input('order')),
                strip_tags($request->input('location')),
                strip_tags($request->input('permission_name')),
                null, // Created by not needed for update
                auth()->user()->id
            );

            $menu = $this->menuService->update($menuDto, $id);

            // Return a success response
            return response()->json([
                'success' => true,
                'message' => 'Menu updated successfully.',
                'data' => $menu,
                'redirect_url' => route('menus.index'),
            ], 201);
        } catch (Exception $e) {
            // Log the exception or handle it as needed

            // Return an error response
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the menu.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        $this->menuService->delete($id);
        return redirect()->route('menus.index')->with('success', 'Menu deleted successfully.');
    }

    public function updateOrder(Request $request)
    {
        $order = $request->input('order');
        $this->menuService->updateOrder($order, null);
        return response()->json(['success' => true]);
    }
}
