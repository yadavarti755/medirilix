<?php

namespace App\Http\Controllers\Secure;

use App\Http\Controllers\Controller;
use App\Services\SmsLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class SmsLogController extends Controller
{
    protected $smsLogService;

    public function __construct()
    {
        $this->smsLogService = new SmsLogService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageTitle = 'SMS Logs';
        return view('secure.sms_logs.index', compact('pageTitle'));
    }

    /**
     * Fetch a listing of the resource.
     */
    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $smsLogs = $this->smsLogService->findAll();

            return DataTables::of($smsLogs)
                ->addColumn('action', function ($smsLog) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['action', 'image'])
                ->make(true);
        }
    }
}
