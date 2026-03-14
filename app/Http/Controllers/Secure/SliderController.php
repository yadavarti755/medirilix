<?php

namespace App\Http\Controllers\Secure;

use App\DTO\SliderDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSliderRequest;
use App\Http\Requests\UpdateSliderRequest;
use App\Models\Slider;
use App\Services\CategoryService;
use App\Services\SliderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class SliderController extends Controller
{
    protected $sliderService;
    protected $categoryService;

    public function __construct()
    {
        $this->sliderService = new SliderService();
        $this->categoryService    = new CategoryService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageTitle = 'Sliders';
        $categories = $this->categoryService->findAll();
        return view('secure.sliders.index', compact('pageTitle', 'categories'));
    }

    /**
     * Fetch a listing of the resource.
     */
    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $sliders = $this->sliderService->findAll();
            return DataTables::of($sliders)
                ->addColumn('image', function ($slider) {
                    if ($slider->file_name) {
                        return "<img src=" . $slider->file_url . " alt='Slider Image' class='img-fluid' style='max-height: 70px;'>";
                    }

                    return '';
                })
                ->addColumn('action', function ($slider) {
                    $button = '';
                    if (auth()->user()->can('view slider')) {
                        $button .= '<a href="' . route('sliders.show', $slider->id) . '" class="btn btn-sm btn-primary" title="View"><i class="fa fa-eye"></i></a> ';
                    }

                    if (auth()->user()->can('edit slider')) {
                        $button .= '<a href="' . route('sliders.edit', $slider->id) . '" class="btn btn-sm btn-warning" title="Edit"><i class="fa fa-edit"></i></a> ';
                    }

                    if (auth()->user()->can('delete slider')) {
                        $button .= '<button class="btn btn-sm btn-danger delete-slider" data-id="' . $slider->id . '" title="Delete">
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
        $pageTitle = 'Add Sliders';
        $categories = $this->categoryService->findAll();
        return view('secure.sliders.create', compact('pageTitle', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSliderRequest $request)
    {
        try {

            $sliderDto = new SliderDto(
                strip_tags($request->input('category_id')),
                strip_tags($request->input('title')),
                strip_tags($request->input('subtitle')),
                strip_tags($request->input('description')),
                $request->file('file_name'),
                0,
                auth()->user()->id,
                auth()->user()->id
            );

            $slider = $this->sliderService->create($sliderDto);

            if (!$slider) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while saving slider.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Slider created successfully!'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Slider addition failed: ' . $e->getMessage());
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
        $pageTitle = 'View Sliders';
        $slider = $this->sliderService->findById($id);
        return view('secure.sliders.show', compact('slider', 'pageTitle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pageTitle = 'Edit Sliders';
        $slider = $this->sliderService->findById($id);
        $categories = $this->categoryService->findAll();
        return view('secure.sliders.edit', compact('slider', 'pageTitle', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSliderRequest $request, Slider $slider)
    {
        try {
            $sliderDto = new SliderDto(
                strip_tags($request->input('category_id')),
                strip_tags($request->input('title')),
                strip_tags($request->input('subtitle')),
                strip_tags($request->input('description')),
                $request->hasFile('file_name') ? $request->file('file_name') : null,
                $slider->is_published,
                $slider->created_by,
                auth()->user()->id
            );
            $slider = $this->sliderService->update($sliderDto, $slider->id);

            if (!$slider) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while updating slider.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Slider updated successfully!'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Slider updation failed: ' . $e->getMessage());
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
            $slider = $this->sliderService->delete($id);
            if (!$slider) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while deleting slider.',
                ], 500);
            }

            return response()->json(['success' => true, 'message' => 'Slider moved to trash successfully!']);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Slider deletion failed: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong!'], 500);
        }
    }

    public function publish(Request $request, Slider $slider)
    {
        try {
            $sliderDto = new SliderDto(
                $slider->title,
                $slider->subtitle,
                $slider->description,
                $slider->file_name,
                $request->input('is_published'),
                $slider->created_by,
                auth()->user()->id,
            );

            $updated = $this->sliderService->publish($sliderDto, $slider->id);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while publishing slider.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Slider published successfully!'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Slider publishing failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
