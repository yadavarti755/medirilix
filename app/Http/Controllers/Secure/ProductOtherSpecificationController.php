<?php

namespace App\Http\Controllers\Secure;

use App\Http\Controllers\Controller;
use App\Services\ProductOtherSpecificationService;
use App\DTO\ProductOtherSpecificationDto;
use Illuminate\Http\Request;
use Log;

class ProductOtherSpecificationController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new ProductOtherSpecificationService();
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'label' => 'required|string',
                'value' => 'required|string',
            ]);

            $dto = new ProductOtherSpecificationDto(
                $request->input('product_id'),
                $request->input('label'),
                $request->input('value'),
                auth()->user()->id,
                auth()->user()->id
            );

            $spec = $this->service->create($dto);

            if (!$spec) {
                return response()->json(['success' => false, 'message' => 'Error saving specification.'], 500);
            }

            return response()->json(['success' => true, 'message' => 'Specification added.', 'data' => $spec], 200);
        } catch (\Exception $e) {
            Log::error('Spec addition failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server Error'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->service->delete($id);
            if ($deleted) {
                return response()->json(['success' => true, 'message' => 'Specification deleted.']);
            }
            return response()->json(['success' => false, 'message' => 'Error deleting specification.'], 500);
        } catch (\Exception $e) {
            Log::error('Spec deletion failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server Error'], 500);
        }
    }
}
