<?php

namespace App\Http\Controllers\Secure;

use App\Http\Controllers\Controller;
use App\Services\QueryService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class QueryController extends Controller
{
    protected $queryService;

    public function __construct()
    {
        $this->queryService = new QueryService();
    }

    public function index()
    {
        $pageTitle = 'Contact Queries';
        return view('secure.contact_queries.index', compact('pageTitle'));
    }

    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $queries = $this->queryService->findAllForDatatable();
            return DataTables::of($queries)
                ->addColumn('action', function ($query) {
                    $deleteBtn = '';
                    if (auth()->user()->can('delete contact query')) {
                        $deleteBtn = '<button class="btn btn-danger btn-sm btn-delete" data-id="' . $query->id . '"><i class="fas fa-trash-alt"></i></button>';
                    }
                    return $deleteBtn;
                    // return "<button class='btn btn-info btn-sm btnReply' id='" . base64_encode($query->id) . "'><i class='fas fa-paper-plane'></i></button>";
                })
                ->editColumn('created_at', function ($query) {
                    return date('d-m-Y H:i A', strtotime($query->created_at));
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function destroy($id)
    {
        try {
            $result = $this->queryService->delete($id);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Record deleted successfully!'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error while deleting record.',
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
