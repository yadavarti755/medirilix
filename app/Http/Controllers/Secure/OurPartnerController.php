<?php

namespace App\Http\Controllers\Secure;

use App\DTO\OurPartnerDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOurPartnerRequest;
use App\Http\Requests\UpdateOurPartnerRequest;
use App\Models\OurPartner;
use App\Services\OurPartnerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class OurPartnerController extends Controller
{
    protected $ourPortalService;

    public function __construct()
    {
        $this->ourPortalService = new OurPartnerService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageTitle = 'Our Partner';
        return view('secure.our_partners.index', compact('pageTitle'));
    }

    /**
     * Fetch a listing of the resource.
     */
    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $ourPartners = $this->ourPortalService->findAll();
            return DataTables::of($ourPartners)
                ->addColumn('image', function ($ourPartner) {
                    if ($ourPartner->file_name) {
                        return "<img src=" . $ourPartner->file_name_full_path . " alt='Our partner Image' class='img-fluid' style='height: 60px; object-fit: contain;'>";
                    }

                    return '';
                })
                ->addColumn('action', function ($ourPartner) {
                    $button = '';
                    if (auth()->user()->can('edit our partner')) {
                        $button .= '<a href="' . route('our-partners.edit', $ourPartner->id) . '" class="btn btn-sm btn-warning" title="Edit"><i class="fa fa-edit"></i></a> ';
                    }

                    if (auth()->user()->can('delete our partner')) {
                        $button .= '<button class="btn btn-sm btn-danger delete-our-partner" data-id="' . $ourPartner->id . '" title="Delete">
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
        $pageTitle = 'Add Our partner';
        return view('secure.our_partners.create', compact('pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOurPartnerRequest $request)
    {
        try {

            $ourPartnerDto = new OurPartnerDto(
                $request->file('file_name'),
                strip_tags($request->input('title')),
                strip_tags($request->input('link')),
                auth()->user()->id,
                auth()->user()->id
            );

            $ourPartner = $this->ourPortalService->create($ourPartnerDto);

            if (!$ourPartner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while saving our partner.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Our partner created successfully!'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Our partner addition failed: ' . $e->getMessage());
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
        $pageTitle = 'View Our partner';
        $ourPartner = $this->ourPortalService->findById($id);
        return view('secure.our_partners.show', compact('ourPartner', 'pageTitle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pageTitle = 'Edit Our partner';
        $ourPartner = $this->ourPortalService->findById($id);
        return view('secure.our_partners.edit', compact('ourPartner', 'pageTitle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOurPartnerRequest $request, OurPartner $ourPartner)
    {
        try {
            $ourPartnerDto = new OurPartnerDto(
                $request->hasFile('file_name') ? $request->file('file_name') : null,
                strip_tags($request->input('title')),
                strip_tags($request->input('link')),
                auth()->user()->id,
                auth()->user()->id
            );
            $ourPartner = $this->ourPortalService->update($ourPartnerDto, $ourPartner->id);

            if (!$ourPartner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while updating our partner.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Our partner updated successfully!'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Our partner updation failed: ' . $e->getMessage());
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
            $ourPartner = $this->ourPortalService->delete($id);
            if (!$ourPartner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while deleting our partner.',
                ], 500);
            }

            return response()->json(['message' => 'Our partner moved to trash successfully!']);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Our partner deletion failed: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong!'], 500);
        }
    }
}
