<?php

namespace App\Http\Controllers\Secure;

use App\DTO\PaymentGatewayDto;
use Response;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentGatewayRequest;
use App\Services\PaymentGatewayService;
use Illuminate\Support\Facades\Log;

class PaymentGatewayController extends Controller
{
    protected $service;

    public function __construct(PaymentGatewayService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $pageTitle = "Payment Gateway";
        return view('secure.payment_gateways.index', compact('pageTitle'));
    }

    // Function to get all payment gateways in for data table;
    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $paymentGateways = $this->service->findAll();
            return DataTables::of($paymentGateways)
                ->addColumn('image', function ($paymentGateway) {
                    if ($paymentGateway->file_url) {
                        return "<img src='" . $paymentGateway->file_url . "' alt='Image' class='img-fluid' style='max-height: 50px;'>";
                    }
                    return '';
                })
                ->addColumn('status', function ($paymentGateway) {
                    if ($paymentGateway->is_active) {
                        return '<span class="badge bg-success">Active</span>';
                    }
                    return '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('action', function ($paymentGateway) {
                    $button = '';
                    if (auth()->user()->can('edit payment gateway')) {
                        $button .= '<button type="button" data-id="' . $paymentGateway->id . '" class="btn btn-sm btn-warning btn-edit" title="Edit"><i class="fa fa-edit"></i></button> ';
                    }

                    if (auth()->user()->can('delete payment gateway')) {
                        $button .= '<button class="btn btn-sm btn-danger btn-delete" data-id="' . $paymentGateway->id . '" title="Delete">
                            <i class="fa fa-trash"></i>
                        </button>';
                    }
                    return $button;
                })
                ->rawColumns(['action', 'image', 'status'])
                ->make(true);
        }
    }

    // Fetch single detail
    public function fetchOne(Request $request, $id)
    {
        try {
            $data = $this->service->findById($id);
            if ($data) {
                $data->append('file_url'); // Append accessor
            }
            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            Log::error('Record fetch failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server failed while processing request'
            ], 500);
        }
    }

    // Function to create
    public function store(StorePaymentGatewayRequest $request)
    {
        try {
            $dto = new PaymentGatewayDto(
                strip_tags($request->input('gateway_name')),
                strip_tags($request->input('app_id')),
                strip_tags($request->input('client_id_or_key')),
                strip_tags($request->input('client_secret')),
                $request->hasFile('image') ? $request->file('image') : null,
                $request->input('is_active', 1),
                auth()->user()->id,
                auth()->user()->id
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
            Log::error('Record addition failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server failed while processing request'
            ], 500);
        }
    }


    // Function to update
    public function update(StorePaymentGatewayRequest $request, PaymentGateway $paymentGateway)
    {
        try {
            $dto = new PaymentGatewayDto(
                strip_tags($request->input('gateway_name')),
                strip_tags($request->input('app_id')),
                strip_tags($request->input('client_id_or_key')),
                strip_tags($request->input('client_secret')),
                $request->hasFile('image') ? $request->file('image') : null,
                $request->input('is_active', 1),
                $paymentGateway->created_by,
                auth()->user()->id
            );
            $result = $this->service->update($dto, $paymentGateway->id);

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
            Log::error('Record updation failed: ' . $e->getMessage());
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
            Log::error('PaymentGateway: Deletion failed: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong!'], 500);
        }
    }
}
