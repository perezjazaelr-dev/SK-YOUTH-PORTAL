@extends('layouts.app')

@section('content')
<div x-data="{ mobileSidebar: false }" class="flex-1 flex flex-col md:flex-row bg-[#f8fafc] dark:bg-slate-950">
    @include('layouts.dashboard-sidebar')

    <div x-show="mobileSidebar" @click="mobileSidebar = false" class="fixed inset-0 bg-slate-900/40 z-20 md:hidden" x-cloak></div>

    <div class="flex-1 flex flex-col min-w-0">
        <div class="p-6 md:p-8 space-y-6 flex-1 overflow-y-auto">
            <div class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider pb-4 border-b border-slate-100 dark:border-slate-800">
                <a href="{{ route('dashboard.index') }}" class="text-slate-400 hover:text-[#1e40af] dark:hover:text-blue-400">Dashboard</a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-800 dark:text-slate-100">DPO Audit Logs</span>
            </div>

            <div class="space-y-1">
                <span class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Data Privacy Office</span>
                <h1 class="text-2xl font-black tracking-tight text-slate-800 dark:text-white font-display uppercase">Audit Log Review</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Review and export system activity logs for compliance and privacy oversight.</p>
                @include('admin.logs.partials.dpo-export-modal')
            </div>

            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
                <form method="GET" action="{{ route('admin.dpo.audit-logs') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search logs..."
                        class="md:col-span-2 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 px-4 py-2.5 text-sm dark:text-white">
                    <select name="action_type" onchange="this.form.submit()" class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 px-4 py-2.5 text-sm dark:text-white">
                        <option value="">All Actions</option>
                        @foreach($uniqueActions as $action)
                            <option value="{{ $action }}" @selected($actionFilter == $action)>{{ $action }}</option>
                        @endforeach
                    </select>
                    <select name="year" onchange="this.form.submit()" class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 px-4 py-2.5 text-sm dark:text-white">
                        <option value="">All Years</option>
                        @foreach($years as $yr)
                            <option value="{{ $yr }}" @selected($yearFilter == $yr)>{{ $yr }}</option>
                        @endforeach
                    </select>
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-[10px] font-black uppercase tracking-wider text-slate-400 border-b border-slate-100 dark:border-slate-800">
                                <th class="pb-3 pr-4">Timestamp</th>
                                <th class="pb-3 pr-4">User</th>
                                <th class="pb-3 pr-4">Action</th>
                                <th class="pb-3 pr-4">IP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($logs as $log)
                                <tr class="text-slate-700 dark:text-slate-200">
                                    <td class="py-3 pr-4 text-xs whitespace-nowrap">{{ $log->created_at->format('M j, Y g:i A') }}</td>
                                    <td class="py-3 pr-4 text-xs">{{ $log->user?->name ?? 'System' }}</td>
                                    <td class="py-3 pr-4"><span class="px-2 py-0.5 rounded-lg bg-blue-50 dark:bg-blue-950/40 text-[#1e40af] dark:text-blue-300 text-[10px] font-bold uppercase">{{ $log->action }}</span></td>
                                    <td class="py-3 pr-4 text-xs font-mono text-slate-500">{{ $log->ip_address }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-8 text-center text-slate-400">No audit logs found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">{{ $logs->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
