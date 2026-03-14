<?php

namespace App\Http\Controllers\Secure;

use App\DTO\CancelReasonDto;
use App\Models\CancelReason;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Services\CancelReasonService;
use App\Http\Requests\StoreCancelReasonRequest;
use App\Http\Requests\UpdateCancelReasonRequest;
use Illuminate\Support\Facades\Log;

class CancelReasonController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new CancelReasonService();
    }

    public function index()
    {
        $pageTitle = "Cancel Reasons";
        return view('secure.cancel_reasons.index', compact('pageTitle'));
    }

    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->service->findAll();
            return Datatables::of($data)
                ->addColumn('action', function ($row) {
                    $btn = '';
                    $btn .= '<button type="button" data-id="' . $row->id . '" class="btn btn-sm btn-warning btn-edit" title="Edit"><i class="fa fa-edit"></i></button> ';
                    $btn .= '<button class="btn btn-sm btn-danger btn-delete" data-id="' . $row->id . '" title="Delete"><i class="fa fa-trash"></i></button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function fetchOne(Request $request, $id)
    {
        try {
            $data = $this->service->findById($id);
            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            Log::error('Fetch failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server failed while processing request'
            ], 500);
        }
    }

    public function store(StoreCancelReasonRequest $request)
    {
        try {
            $dto = new CancelReasonDto(
                strip_tags($request->input('title')),
                auth()->id(),
                auth()->id()
            );

            $result = $this->service->create($dto);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while saving record.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Record created successfully!'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Store failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server failed while processing request'
            ], 500);
        }
    }

    public function update(UpdateCancelReasonRequest $request, $id)
    {
        try {
            $dto = new CancelReasonDto(
                strip_tags($request->input('title')),
                null,
                auth()->id()
            );
            $result = $this->service->update($dto, $id);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while updating record.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Record updated successfully!'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

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
            Log::error('Deletion failed: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong!'], 500);
        }
    }
}
