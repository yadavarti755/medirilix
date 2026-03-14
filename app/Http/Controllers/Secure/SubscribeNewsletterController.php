<?php

namespace App\Http\Controllers\Secure;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Services\SubscribeNewsletterService;
use Illuminate\Support\Facades\Log;

class SubscribeNewsletterController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new SubscribeNewsletterService();
    }

    public function index()
    {
        $pageTitle = "Newsletter Subscribers";
        return view('secure.newsletter.index', compact('pageTitle'));
    }

    // Function to get all data for data table;
    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->service->findAll();
            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $button = '';
                    if (auth()->user()->can('delete newsletter')) {
                        $button .= '<button class="btn btn-sm btn-danger btn-delete" data-id="' . $row->id . '" title="Delete">
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $result = $this->service->delete($id);
            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while deleting record.',
                ], 500);
            }
            return response()->json(['message' => 'Record moved to trash successfully!']);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('SubscribeNewsletter: Deletion failed: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong!'], 500);
        }
    }
}
