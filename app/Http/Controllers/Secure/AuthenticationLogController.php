<?php

namespace App\Http\Controllers\Secure;

use App\Http\Controllers\Controller;
use App\Services\AuthenticationLogService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog;

class AuthenticationLogController extends Controller
{
    protected $authenticationLogService;

    public function __construct()
    {
        $this->authenticationLogService = new AuthenticationLogService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageTitle = 'Authentication Log';
        return view('secure.authentication_logs.index', compact('pageTitle'));
    }

    /**
     * Fetch a listing of the resource.
     */
    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $logs = AuthenticationLog::with('authenticatable.roles')->orderBy('login_at', 'DESC');

            return DataTables::of($logs)
                ->addColumn('login_at', function ($log) {
                    return $log->login_at;
                })
                ->addColumn('logout_at', function ($log) {
                    return $log->logout_at;
                })
                ->addColumn('ip_address', function ($log) {
                    return $log->ip_address ?? '-';
                })
                ->addColumn('browser', function ($log) {
                    $agent = new Agent();
                    $agent->setUserAgent($log->user_agent);
                    return $agent->browser();
                })
                ->addColumn('platform', function ($log) {
                    $agent = new Agent();
                    $agent->setUserAgent($log->user_agent);
                    return $agent->platform();
                })
                ->addColumn('login_successful_desc', function ($log) {
                    return $log->login_successful == 1 ? '<span class="badge bg-success">Success</span>' : '<span class="badge bg-danger">Failed</span>';
                })
                ->addColumn('roles', function ($log) {
                    if (!$log->authenticatable) return '-';
                    return $log->authenticatable->roles->pluck('name')->implode(', ');
                })
                ->rawColumns(['login_at', 'logout_at', 'ip_address', 'browser', 'platform', 'login_successful_desc'])
                ->make(true);
        }
    }
}
