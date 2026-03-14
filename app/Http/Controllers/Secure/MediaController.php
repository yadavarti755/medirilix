<?php

namespace App\Http\Controllers\Secure;

use App\DTO\MediaDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMediaRequest;
use App\Http\Requests\UpdateMediaRequest;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MediaController extends Controller
{
    protected $mediaService;

    public function __construct()
    {
        $this->mediaService = new MediaService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageTitle = 'Medias';
        $search = $request->get('search');

        $medias = $this->mediaService->findAllWithPagination(12, [], $search);

        // Append search parameter to pagination links
        $medias->appends($request->query());

        return view('secure.medias.index', compact('pageTitle', 'medias', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Add Medias';
        return view('secure.medias.create', compact('pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMediaRequest $request)
    {
        try {

            $mediaDto = new MediaDto(
                $request->file('file_name'),
                '',
                '',
                '',
                strip_tags($request->input('alt_text')),
                auth()->user()->id,
                auth()->user()->id
            );

            $media = $this->mediaService->create($mediaDto);

            if (!$media) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while saving media.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Media created successfully!'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Media addition failed: ' . $e->getMessage());
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
        $pageTitle = 'View Medias';
        $media = $this->mediaService->findById($id);
        return view('secure.medias.show', compact('media', 'pageTitle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pageTitle = 'Edit Medias';
        $media = $this->mediaService->findById($id);
        return view('secure.medias.edit', compact('media', 'pageTitle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMediaRequest $request, Media $media)
    {
        try {
            $mediaDto = new MediaDto(
                $request->hasFile('file_name') ? $request->file('file_name') : null,
                '',
                '',
                '',
                $request->input('alt_text'),
                $media->created_by,
                auth()->user()->id
            );
            $media = $this->mediaService->update($mediaDto, $media->id);

            if (!$media) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while updating media.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Media updated successfully!'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Media updation failed: ' . $e->getMessage());
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
            $media = $this->mediaService->delete($id);
            if (!$media) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while deleting media.',
                ], 500);
            }

            return response()->json(['message' => 'Media moved to trash successfully!']);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Media deletion failed: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong!'], 500);
        }
    }

    public function approve(Request $request, Media $media)
    {
        try {
            $mediaDto = new MediaDto(
                $media->file_name,
                $media->alt_text, // Use alt_text as original_name just to keep position or pass null? 
                // Wait, original usage was weird. Let's pass named args or nulls for unused.
                null, // original_name
                null, // mime_type
                null, // size
                $media->alt_text, // alt_text
                $media->created_by,
                auth()->user()->id,
                $request->input('is_approved'),
                0, // is_published
                strip_tags($request->input('remarks'))
            );

            $updated = $this->mediaService->approve($mediaDto, $media->id);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while approving media.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Media decision submitted successfully!'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Media approval failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function publish(Request $request, Media $media)
    {
        try {
            $mediaDto = new MediaDto(
                $media->file_name,
                null,
                null,
                null,
                $media->alt_text,
                $media->created_by,
                auth()->user()->id,
                $media->is_approved == 1 ? $media->is_approved : 1,
                $request->input('is_published'),
                $media->is_approved == 1 ? $media->remarks : 'Automatically approved while publishing the content'
            );

            $updated = $this->mediaService->publish($mediaDto, $media->id);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while publishing media.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Media published successfully!'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Media publishing failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
