<?php

namespace App\Http\Controllers\Secure;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class AuditLogController extends Controller
{
    protected $auditLogService;

    public function __construct()
    {
        $this->auditLogService = new AuditLogService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageTitle = 'Audit Log';
        return view('secure.audit_logs.index', compact('pageTitle'));
    }

    /**
     * Fetch a listing of the resource.
     */
    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $auditLogs = $this->auditLogService->findAllForDatatable();

            return DataTables::of($auditLogs)
                ->addColumn('causer', function ($row) {
                    return $row->causer ? ['id' => $row->causer->id, 'name' => $row->causer->name] : null;
                })
                ->addColumn('changes', function ($row) {
                    $changes = [];

                    $old = $row->properties['old'] ?? [];
                    $new = $row->properties['attributes'] ?? [];

                    foreach ($new as $key => $value) {
                        $oldValue = $old[$key] ?? null;

                        if ($oldValue !== $value) {
                            $changes[] = "<strong>" . Str::title(str_replace('_', ' ', $key)) . "</strong>: " .
                                "<span class='text-danger'>" . e($oldValue) . "</span> → " .
                                "<span class='text-success'>" . e($value) . "</span>";
                        }
                    }

                    return implode('<br>', $changes);
                })
                ->rawColumns(['causer', 'changes'])
                ->make(true);
        }
    }
}
