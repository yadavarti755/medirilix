<?php

namespace App\Http\Controllers\Secure;

use App\DTO\PaymentMethodDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;
use App\Models\PaymentMethod;
use App\Services\PaymentMethodService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class PaymentMethodController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new PaymentMethodService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageTitle = 'Payment Methods';
        return view('secure.payment-methods.index', compact('pageTitle'));
    }

    /**
     * Fetch a listing of the resource for Datatable.
     */
    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $paymentMethods = $this->service->findAll();
            return DataTables::of($paymentMethods)
                ->addColumn('file', function ($paymentMethod) {
                    if ($paymentMethod->image) {
                        return "<img src=" . $paymentMethod->file_url . " alt='Payment Method' class='img-fluid' style='max-height: 50px;'>";
                    }
                    return '';
                })
                ->addColumn('action', function ($paymentMethod) {
                    $button = '';

                    if (Auth::user()->can('edit payment method')) {
                        $button .= '<button type="button" data-id="' . $paymentMethod->id . '" class="btn btn-sm btn-warning btn-edit" title="Edit"><i class="fa fa-edit"></i></button> ';
                    }

                    if (Auth::user()->can('delete payment method')) {
                        $button .= '<button class="btn btn-sm btn-danger btn-delete" data-id="' . $paymentMethod->id . '" title="Delete">
                            <i class="fa fa-trash"></i>
                        </button>';
                    }
                    return $button;
                })
                ->rawColumns(['action', 'file'])
                ->make(true);
        }
    }

    /**
     * Fetch single detail
     */
    public function fetchOne(Request $request, $id)
    {
        try {
            $data = $this->service->findById($id);
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentMethodRequest $request)
    {
        try {
            $paymentMethodDto = new PaymentMethodDto(
                strip_tags($request->input('title')),
                $request->file('image'),
                1, // Defaulting to published for this style, or keeping as 0? Slider used 0. Brand doesn't have is_published in store? Wait, let's check Brand store. Brand store doesn't have is_published.
                Auth::user()->id,
                Auth::user()->id
            );

            $result = $this->service->create($paymentMethodDto);

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

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentMethod)
    {
        try {
            $paymentMethodDto = new PaymentMethodDto(
                strip_tags($request->input('title')),
                $request->hasFile('image') ? $request->file('image') : null,
                $paymentMethod->is_published,
                $paymentMethod->created_by,
                Auth::user()->id
            );
            $result = $this->service->update($paymentMethodDto, $paymentMethod->id);

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

            return response()->json(['success' => true, 'message' => 'Record moved to trash successfully!']);
        } catch (\Exception $e) {
            Log::error('Record deletion failed: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong!'], 500);
        }
    }

    public function publish(Request $request, PaymentMethod $paymentMethod)
    {
        try {
            $paymentMethodDto = new PaymentMethodDto(
                $paymentMethod->title,
                $paymentMethod->image,
                $request->input('is_published'),
                $paymentMethod->created_by,
                Auth::user()->id
            );

            $updated = $this->service->publish($paymentMethodDto, $paymentMethod->id);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while publishing payment method.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment method published successfully!'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Payment Method publishing failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
