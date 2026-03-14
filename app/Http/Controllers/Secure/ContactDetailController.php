<?php

namespace App\Http\Controllers\Secure;

use App\DTO\ContactDetailDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactDetailRequest;
use App\Http\Requests\UpdateContactDetailRequest;
use App\Models\ContactDetail;
use App\Services\ContactDetailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ContactDetailController extends Controller
{
    protected $contactDetailService;

    public function __construct()
    {
        $this->contactDetailService = new ContactDetailService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageTitle = 'Contact Details';
        return view('secure.contact_details.index', compact('pageTitle'));
    }

    /**
     * Fetch a listing of the resource.
     */
    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $contactDetails = $this->contactDetailService->findAll();

            return DataTables::of($contactDetails)
                ->addColumn('type', function ($contactDetail) {
                    return ucfirst($contactDetail->file_or_link ?? 'N/A');
                })
                ->addColumn('action', function ($contactDetail) {
                    $button = '';
                    if (auth()->user()->can('view contact detail')) {
                        $button .= '<a href="' . route('contact-details.show', $contactDetail->id) . '" class="btn btn-sm btn-primary" title="View"><i class="fa fa-eye"></i></a> ';
                    }

                    if (auth()->user()->can('edit contact detail')) {
                        $button .= '<a href="' . route('contact-details.edit', $contactDetail->id) . '" class="btn btn-sm btn-warning" title="Edit"><i class="fa fa-edit"></i></a> ';
                    }

                    if (auth()->user()->can('delete contact detail')) {
                        $button .= '<button class="btn btn-sm btn-danger delete-contact-detail" data-id="' . $contactDetail->id . '" title="Delete">
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
        $pageTitle = 'Add Contact Details';
        return view('secure.contact_details.create', compact('pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactDetailRequest $request)
    {
        try {

            $contactDetailDto = new ContactDetailDto(
                strip_tags($request->input('address')),
                strip_tags($request->input('phone_numbers')),
                strip_tags($request->input('email_ids')),
                $request->input('is_primary', false),
                auth()->user()->id,
                auth()->user()->id
            );


            $contactDetail = $this->contactDetailService->create($contactDetailDto);

            if (!$contactDetail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while saving contact detail.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Contact Detail created successfully!'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Contact Detail addition failed: ' . $e->getMessage());
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
        $pageTitle = 'View Contact Details';
        $contactDetail = $this->contactDetailService->findById($id);
        return view('secure.contact_details.show', compact('contactDetail', 'pageTitle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pageTitle = 'Edit Contact Details';
        $contactDetail = $this->contactDetailService->findById($id);
        return view('secure.contact_details.edit', compact('contactDetail', 'pageTitle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContactDetailRequest $request, ContactDetail $contactDetail)
    {
        try {
            $contactDetailDto = new ContactDetailDto(
                strip_tags($request->input('address')),
                strip_tags($request->input('phone_numbers')),
                strip_tags($request->input('email_ids')),
                $request->input('is_primary'),
                $contactDetail->created_by,
                auth()->user()->id
            );

            $contactDetail = $this->contactDetailService->update($contactDetailDto, $contactDetail->id);

            if (!$contactDetail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while updating contact detail.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Contact Detail updated successfully!'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Contact Detail updation failed: ' . $e->getMessage());
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
            $contactDetail = $this->contactDetailService->delete($id);
            if (!$contactDetail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while deleting contact detail.',
                ], 500);
            }

            return response()->json(['message' => 'Contact Detail moved to trash successfully!']);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Contact Detail deletion failed: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong!'], 500);
        }
    }
}
