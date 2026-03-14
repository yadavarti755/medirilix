<?php

namespace App\Http\Controllers\Secure;

use App\DTO\AnnouncementDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAnnouncementRequest;
use App\Http\Requests\UpdateAnnouncementRequest;
use App\Models\Announcement;
use App\Services\AnnouncementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class AnnouncementController extends Controller
{
    protected $announcementService;

    public function __construct()
    {
        $this->announcementService = new AnnouncementService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageTitle = 'Announcements';
        return view('secure.announcements.index', compact('pageTitle'));
    }

    /**
     * Fetch a listing of the resource.
     */
    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $announcements = $this->announcementService->findAll();

            return DataTables::of($announcements)
                ->addColumn('type', function ($announcement) {
                    return ucfirst($announcement->file_or_link ?? 'N/A');
                })
                ->addColumn('preview', function ($announcement) {
                    if ($announcement->file_or_link === 'file') {
                        $files = '';
                        if ($announcement->file_name) {
                            $files .= "<a href='" . $announcement->file_url . "' target='_BLANK'>View File (En)</a>";
                        }

                        if ($announcement->file_name_hi) {
                            $files .= "<br /><br /> <a href='" . $announcement->file_url_hi . "' target='_BLANK'>View View File (Hi)</a>";
                        }

                        return $files;
                    }

                    if ($announcement->file_or_link === 'link' && $announcement->page_link) {
                        return "<a href='" . e($announcement->page_link) . "' target='_blank'>View Link</a>";
                    }

                    return '—';
                })
                ->addColumn('action', function ($announcement) {
                    $button = '';
                    if (auth()->user()->can('view announcement')) {
                        $button .= '<a href="' . route('announcements.show', $announcement->id) . '" class="btn btn-sm btn-primary" title="View"><i class="fa fa-eye"></i></a> ';
                    }

                    if (auth()->user()->can('edit announcement')) {
                        $button .= '<a href="' . route('announcements.edit', $announcement->id) . '" class="btn btn-sm btn-warning" title="Edit"><i class="fa fa-edit"></i></a> ';
                    }

                    if (auth()->user()->can('delete announcement')) {
                        $button .= '<button class="btn btn-sm btn-danger delete-announcement" data-id="' . $announcement->id . '" title="Delete">
                        <i class="fa fa-trash"></i>
                    </button>';
                    }
                    return $button;
                })
                ->rawColumns(['action', 'preview'])
                ->make(true);
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Add Announcements';
        return view('secure.announcements.create', compact('pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAnnouncementRequest $request)
    {
        try {

            $announcementDto = new AnnouncementDto(
                strip_tags($request->input('title')),
                strip_tags($request->input('title_hi')),
                strip_tags($request->input('description')),
                strip_tags($request->input('description_hi')),
                $request->input('file_or_link'),
                $request->hasFile('file_name') ? $request->file('file_name') : null,
                $request->hasFile('file_name_hi') ? $request->file('file_name_hi') : null,
                strip_tags($request->input('page_link')) ?? null,
                $request->input('status'),
                0,
                0,
                null,
                auth()->user()->id,
                auth()->user()->id
            );


            $announcement = $this->announcementService->create($announcementDto);

            if (!$announcement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while saving announcement.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Announcement created successfully!'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Announcement addition failed: ' . $e->getMessage());
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
        $pageTitle = 'View Announcements';
        $announcement = $this->announcementService->findById($id);
        return view('secure.announcements.show', compact('announcement', 'pageTitle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pageTitle = 'Edit Announcements';
        $announcement = $this->announcementService->findById($id);
        return view('secure.announcements.edit', compact('announcement', 'pageTitle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAnnouncementRequest $request, Announcement $announcement)
    {
        try {
            $announcementDto = new AnnouncementDto(
                strip_tags($request->input('title')),
                strip_tags($request->input('title_hi')),
                strip_tags($request->input('description')),
                strip_tags($request->input('description_hi')),
                $request->input('file_or_link'),
                $request->hasFile('file_name') ? $request->file('file_name') : null,
                $request->hasFile('file_name_hi') ? $request->file('file_name_hi') : null,
                strip_tags($request->input('page_link')) ?? null,
                $request->input('status'),
                $announcement->is_approved,
                $announcement->is_published,
                $announcement->remarks,
                $announcement->created_by,
                auth()->user()->id
            );
            $announcement = $this->announcementService->update($announcementDto, $announcement->id);

            if (!$announcement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while updating announcement.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Announcement updated successfully!'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Announcement updation failed: ' . $e->getMessage());
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
            $announcement = $this->announcementService->delete($id);
            if (!$announcement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while deleting announcement.',
                ], 500);
            }

            return response()->json(['message' => 'Announcement moved to trash successfully!']);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Announcement deletion failed: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong!'], 500);
        }
    }

    public function approve(Request $request, Announcement $announcement)
    {
        try {
            $announcementDto = new AnnouncementDto(
                $announcement->title,
                $announcement->title_hi,
                $announcement->description,
                $announcement->description_hi,
                $announcement->file_or_link,
                $announcement->file_name,
                $announcement->file_name_hi,
                $announcement->page_link,
                $announcement->status,
                $request->input('is_approved'),
                0,
                $request->input('remarks'),
                $announcement->created_by,
                auth()->user()->id
            );

            $updated = $this->announcementService->approve($announcementDto, $announcement->id);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while approving announcement.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Announcement decision submitted successfully!'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Announcement approval failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function publish(Request $request, Announcement $announcement)
    {
        try {
            $announcementDto = new AnnouncementDto(
                $announcement->title,
                $announcement->title_hi,
                $announcement->description,
                $announcement->description_hi,
                $announcement->file_or_link,
                $announcement->file_name,
                $announcement->file_name_hi,
                $announcement->page_link,
                $announcement->status,
                $announcement->is_approved == 1 ? $announcement->is_approved : 1,
                $request->input('is_published'),
                $announcement->is_approved == 1 ? $announcement->remarks : 'Automatically approved while publishing the content',
                $announcement->created_by,
                auth()->user()->id
            );

            $updated = $this->announcementService->publish($announcementDto, $announcement->id);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while publishing announcement.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Announcement published successfully!'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Announcement publishing failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
