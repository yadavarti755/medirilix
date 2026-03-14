<?php

namespace App\Http\Controllers\Secure;

use Purifier;
use App\DTO\SocialMediaDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSocialMediaRequest;
use App\Http\Requests\UpdateSocialMediaRequest;
use App\Models\SocialMedia;
use App\Services\SocialMediaPlatformService;
use App\Services\SocialMediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class SocialMediaController extends Controller
{
    protected $socialMediaService;
    protected $socialMediaPlatformService;

    public function __construct()
    {
        $this->socialMediaService = new SocialMediaService();
        $this->socialMediaPlatformService = new SocialMediaPlatformService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageTitle = 'Social Medias';
        return view('secure.social_medias.index', compact('pageTitle'));
    }

    /**
     * Fetch a listing of the resource.
     */
    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $socialMedias = $this->socialMediaService->findAll();

            return DataTables::of($socialMedias)
                ->addColumn('action', function ($socialMedia) {
                    $button = '';
                    if (auth()->user()->can('view social media')) {
                        $button .= '<a href="' . route('social-medias.show', $socialMedia->id) . '" class="btn btn-sm btn-primary" title="View"><i class="fa fa-eye"></i></a> ';
                    }

                    if (auth()->user()->can('edit social media')) {
                        $button .= '<a href="' . route('social-medias.edit', $socialMedia->id) . '" class="btn btn-sm btn-warning" title="Edit"><i class="fa fa-edit"></i></a> ';
                    }

                    if (auth()->user()->can('delete social media')) {
                        $button .= '<button class="btn btn-sm btn-danger delete-social-media" data-id="' . $socialMedia->id . '" title="Delete">
                        <i class="fa fa-trash"></i>
                    </button>';
                    }
                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Add Social Medias';
        $socialMediaPlatforms = $this->socialMediaPlatformService->findAll();
        return view('secure.social_medias.create', compact('pageTitle', 'socialMediaPlatforms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSocialMediaRequest $request)
    {
        try {

            $socialMediaDto = new SocialMediaDto(
                strip_tags($request->input('type')),
                strip_tags($request->input('name')),
                strip_tags($request->input('url')),
                $request->input('icon_class'),
                auth()->user()->id,
                auth()->user()->id
            );

            $socialMedia = $this->socialMediaService->create($socialMediaDto);

            if (!$socialMedia) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while saving social media.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Social media created successfully!'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Social media addition failed: ' . $e->getMessage());
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
        $pageTitle = 'View Social Medias';
        $socialMedia = $this->socialMediaService->findById($id);
        return view('secure.social_medias.show', compact('socialMedia', 'pageTitle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pageTitle = 'Edit Social Medias';
        $socialMedia = $this->socialMediaService->findById($id);
        $socialMediaPlatforms = $this->socialMediaPlatformService->findAll();
        return view('secure.social_medias.edit', compact('socialMedia', 'pageTitle', 'socialMediaPlatforms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSocialMediaRequest $request, SocialMedia $socialMedia)
    {
        try {
            $socialMediaDto = new SocialMediaDto(
                strip_tags($request->input('type')),
                strip_tags($request->input('name')),
                strip_tags($request->input('url')),
                $request->input('icon_class'),
                $socialMedia->created_by,
                auth()->user()->id
            );

            $socialMedia = $this->socialMediaService->update($socialMediaDto, $socialMedia->id);

            if (!$socialMedia) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while updating social media.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Social media updated successfully!'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Social media updation failed: ' . $e->getMessage());
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
            $socialMedia = $this->socialMediaService->delete($id);
            if (!$socialMedia) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while deleting social media.',
                ], 500);
            }

            return response()->json(['message' => 'Social media moved to trash successfully!']);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Social media deletion failed: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong!'], 500);
        }
    }
}
