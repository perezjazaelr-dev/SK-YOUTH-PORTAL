<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DpoAuditLogController extends Controller
{
    /**
     * Display audit logs for DPO / clearance holders.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $actionFilter = $request->input('action_type');
        $yearFilter = $request->input('year');

        $limit = (int) $request->input('limit', 20);
        if (!in_array($limit, [10, 15, 25, 50, 100], true)) {
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
        $uniqueActions = ActivityLog::select('action')->distinct()->orderBy('action')->pluck('action');

        $driver = \DB::connection()->getDriverName();
        $years = $driver === 'sqlite'
            ? ActivityLog::selectRaw("strftime('%Y', created_at) as year")->distinct()->orderBy('year', 'desc')->pluck('year')->toArray()
            : ActivityLog::selectRaw('YEAR(created_at) as year')->distinct()->orderBy('year', 'desc')->pluck('year')->toArray();

        if (empty($years)) {
            $years = [date('Y')];
        }

        return view('admin.dpo.audit-logs', compact(
            'logs', 'search', 'actionFilter', 'uniqueActions', 'yearFilter', 'limit', 'years'
        ));
    }

    /**
     * Export ActivityLog entries as CSV for DPO review.
     */
    public function export(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
            'action_type' => ['nullable', 'string', 'max:100'],
        ]);

        $query = ActivityLog::with('user')
            ->whereDate('created_at', '>=', $validated['date_from'])
            ->whereDate('created_at', '<=', $validated['date_to'])
            ->orderBy('created_at');

        if (!empty($validated['action_type'])) {
            $query->where('action', $validated['action_type']);
        }

        $filename = 'dpo-audit-log-' . $validated['date_from'] . '_to_' . $validated['date_to'] . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Timestamp', 'User', 'Email', 'Action', 'Subject Type', 'Subject ID', 'IP Address', 'Payload']);

            $query->chunk(500, function ($logs) use ($handle) {
                foreach ($logs as $log) {
                    fputcsv($handle, [
                        $log->id,
                        $log->created_at?->toDateTimeString(),
                        $log->user?->name ?? 'System',
                        $log->user?->email ?? '',
                        $log->action,
                        $log->subject_type,
                        $log->subject_id,
                        $log->ip_address,
                        is_array($log->payload) ? json_encode($log->payload) : (string) $log->payload,
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
