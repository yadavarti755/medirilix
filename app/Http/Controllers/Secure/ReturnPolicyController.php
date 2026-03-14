<?php

namespace App\Http\Controllers\Secure;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReturnPolicyRequest;
use App\Http\Requests\UpdateReturnPolicyRequest;
use App\Services\ReturnPolicyService;
use App\DTO\ReturnPolicyDto;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ReturnPolicyController extends Controller
{
    protected $service;

    public function __construct(ReturnPolicyService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return view('secure.return_policies.index', ['pageTitle' => 'Return Policies']);
    }

    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->service->findForDatatable();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group" role="group">
                                <a href="' . route('return-policies.edit', $row->id) . '" class="btn btn-sm btn-info" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '" title="Delete">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function create()
    {
        return view('secure.return_policies.create', ['pageTitle' => 'Create Return Policy']);
    }

    public function store(StoreReturnPolicyRequest $request)
    {
        $dto = new ReturnPolicyDto(
            $request->title,
            $request->return_till_days,
            $request->return_description,
            auth()->id()
        );

        $result = $this->service->create($dto);

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Return Policy created successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'Failed to create return policy.'], 500);
    }

    public function edit($id)
    {
        $returnPolicy = $this->service->find($id);
        if (!$returnPolicy) {
            return redirect()->route('return-policies.index')->with('error', 'Return Policy not found.');
        }
        return view('secure.return_policies.edit', [
            'pageTitle' => 'Edit Return Policy',
            'returnPolicy' => $returnPolicy
        ]);
    }

    public function update(UpdateReturnPolicyRequest $request, $id)
    {
        $dto = new ReturnPolicyDto(
            $request->title,
            $request->return_till_days,
            $request->return_description,
            null,
            auth()->id()
        );

        $result = $this->service->update($dto, $id);

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Return Policy updated successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'Failed to update return policy.'], 500);
    }

    public function destroy($id)
    {
        $result = $this->service->delete($id);
        if ($result) {
            return response()->json(['success' => true, 'message' => 'Return Policy deleted successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'Failed to delete return policy.'], 500);
    }
}
