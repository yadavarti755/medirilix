<?php

namespace App\Http\Controllers\Secure;

use App\Http\Controllers\Controller;
use App\Services\EmailLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class EmailLogController extends Controller
{
    protected $emailLogService;

    public function __construct()
    {
        $this->emailLogService = new EmailLogService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageTitle = 'Email Logs';
        return view('secure.email_logs.index', compact('pageTitle'));
    }

    /**
     * Fetch a listing of the resource.
     */
    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $emailLogs = $this->emailLogService->findAll();

            return DataTables::of($emailLogs)
                ->addColumn('action', function ($emailLog) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['action', 'image'])
                ->make(true);
        }
    }
}
