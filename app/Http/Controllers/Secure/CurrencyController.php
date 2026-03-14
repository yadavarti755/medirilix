<?php

namespace App\Http\Controllers\Secure;

use App\DTO\CurrencyDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCurrencyRequest;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Log;

class CurrencyController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new CurrencyService();
    }

    public function index()
    {
        $pageTitle = "Currency Master";
        return view('secure.currency.index', compact('pageTitle'));
    }

    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $currencies = $this->service->findAll();
            return DataTables::of($currencies)
                ->addColumn('action', function ($currency) {
                    $button = '';
                    $button .= '<button type="button" data-id="' . $currency->id . '" class="btn btn-sm btn-warning btn-edit" title="Edit"><i class="fa fa-edit"></i></button> ';
                    $button .= '<button class="btn btn-sm btn-danger btn-delete" data-id="' . $currency->id . '" title="Delete"><i class="fa fa-trash"></i></button>';

                    return $button;
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
            Log::error('Currency fetch failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server failed while processing request'
            ], 500);
        }
    }

    public function store(StoreCurrencyRequest $request)
    {
        try {
            $dto = new CurrencyDto(
                strip_tags($request->input('currency')),
                strip_tags($request->input('symbol')),
                strip_tags($request->input('amount_in_dollars')),
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
            Log::error('Currency addition failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server failed while processing request'
            ], 500);
        }
    }

    public function update(StoreCurrencyRequest $request, $id)
    {
        try {
            $dto = new CurrencyDto(
                strip_tags($request->input('currency')),
                strip_tags($request->input('symbol')),
                strip_tags($request->input('amount_in_dollars')),
                null,
                auth()->user()->id
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
            Log::error('Currency update failed: ' . $e->getMessage());
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
            Log::error('Currency deletion failed: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong!'], 500);
        }
    }
}
