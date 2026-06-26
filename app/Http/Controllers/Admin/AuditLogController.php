<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    /**
     * Display a list of system audit logs.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $actionFilter = $request->input('action_type');
        $yearFilter = $request->input('year');
        
        $limit = $request->input('limit', 20);
        if (!in_array($limit, [10, 15, 25, 50, 100])) {
            $limit = 20;
        }

        $query = ActivityLog::with('user')->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('payload', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($actionFilter) {
            $query->where('action', $actionFilter);
        }

        if ($yearFilter) {
            $query->whereYear('created_at', $yearFilter);
        }

        $logs = $query->paginate($limit)->withQueryString();

        // Get unique actions for filter select dropdown
        $uniqueActions = ActivityLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        $driver = \DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            $years = ActivityLog::selectRaw("strftime('%Y', created_at) as year")
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year')
                ->toArray();
        } else {
            $years = ActivityLog::selectRaw('YEAR(created_at) as year')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year')
                ->toArray();
        }
        if (empty($years)) {
            $years = [date('Y')];
        }

        return view('admin.logs.index', compact(
            'logs', 'search', 'actionFilter', 'uniqueActions', 'yearFilter', 'limit', 'years'
        ));
    }
}
