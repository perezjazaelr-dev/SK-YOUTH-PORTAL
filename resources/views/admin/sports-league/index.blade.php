@extends('layouts.app')

@section('content')
<div x-data="{ mobileSidebar: false }" class="flex-1 flex flex-col md:flex-row bg-[#f8fafc]">

    <!-- Left Sidebar -->
    @include('layouts.dashboard-sidebar')

    <div x-show="mobileSidebar" @click="mobileSidebar = false" class="fixed inset-0 bg-slate-900/40 z-20 md:hidden" x-cloak></div>

    <!-- Main Pane -->
    <div class="flex-1 flex flex-col min-w-0">
        
        <header class="bg-white border-b border-slate-100 h-16 px-4 flex items-center justify-between md:hidden shrink-0">
            <button @click="mobileSidebar = true" class="p-2 text-slate-500 hover:text-slate-800 active:scale-95 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
            <div class="flex items-center space-x-2">
                <img src="{{ asset('images/logo.png') }}" class="w-8 h-8 object-contain rounded-full bg-white p-0.5 border" alt="SK Logo">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-800 font-display">SK Namayan</span>
            </div>
            <div class="w-10"></div>
        </header>

        <div class="p-6 md:p-8 space-y-8 flex-1 overflow-y-auto">
            
            <!-- Header section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200">
                <div>
                    <span class="text-[9px] font-black text-[#1e40af] uppercase tracking-widest block font-display">SK Sports Committee</span>
                    <h1 class="text-xl font-black text-slate-800 font-display uppercase tracking-tight">Sports League Console</h1>
                </div>
            </div>

            <!-- Table Search & Status Filter Bar -->
            <div class="card p-6 bg-white border border-slate-100 rounded-3xl shadow-sm">
                <form id="filterForm" method="GET" action="{{ route('admin.sports-league.index') }}" class="space-y-4">
                    <input type="hidden" name="division" id="divisionFilterInput" value="{{ $divisionFilter ?? '' }}">
                    
                    <!-- Row 1: Search, Status, Year -->
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <!-- Search -->
                        <div class="md:col-span-6 relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Search by participant name or email..." class="pl-10 pr-4 py-2.5 w-full bg-slate-50/70 border border-slate-200/60 rounded-2xl text-xs outline-none focus:bg-white focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition font-sans placeholder-slate-400">
                        </div>

                        <!-- Status Dropdown -->
                        <div class="md:col-span-3 relative">
                            <select name="status" onchange="this.form.submit()" class="block w-full py-2.5 pl-4 pr-10 bg-slate-50/70 border border-slate-200/60 rounded-2xl text-xs text-slate-700 outline-none focus:bg-white focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition cursor-pointer appearance-none">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="declined" {{ $status == 'declined' ? 'selected' : '' }}>Declined</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>

                        <!-- Year Dropdown -->
                        <div class="md:col-span-3 relative">
                            <select name="year" onchange="this.form.submit()" class="block w-full py-2.5 pl-4 pr-10 bg-slate-50/70 border border-slate-200/60 rounded-2xl text-xs text-slate-700 outline-none focus:bg-white focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition cursor-pointer appearance-none">
                                <option value="">All Submission Years</option>
                                @foreach($years as $yr)
                                    <option value="{{ $yr }}" {{ $yearFilter == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Division Filter Pills -->
                    <div class="flex items-center gap-2 overflow-x-auto pb-2 no-scrollbar border-t border-slate-100 pt-4 mt-2">
                        <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider mr-2 shrink-0">Division:</span>
                        <button type="button" onclick="setDivisionFilter('')"
                                class="px-3.5 py-1.5 rounded-full text-[11px] font-bold tracking-wide transition active:scale-95 cursor-pointer flex items-center space-x-1.5 select-none {{ empty($divisionFilter) ? 'bg-[#1e40af] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200/80 border border-slate-200' }}">
                            <span>All Submissions</span>
                        </button>
                        <button type="button" onclick="setDivisionFilter('Basketball Midget Division')"
                                class="px-3.5 py-1.5 rounded-full text-[11px] font-bold tracking-wide transition active:scale-95 cursor-pointer flex items-center space-x-1.5 select-none {{ $divisionFilter === 'Basketball Midget Division' ? 'bg-[#1e40af] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200/80 border border-slate-200' }}">
                            <span>Basketball Midget</span>
                        </button>
                        <button type="button" onclick="setDivisionFilter('Basketball Senior Division')"
                                class="px-3.5 py-1.5 rounded-full text-[11px] font-bold tracking-wide transition active:scale-95 cursor-pointer flex items-center space-x-1.5 select-none {{ $divisionFilter === 'Basketball Senior Division' ? 'bg-[#1e40af] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200/80 border border-slate-200' }}">
                            <span>Basketball Senior</span>
                        </button>
                        <button type="button" onclick="setDivisionFilter('Volleyball Womens')"
                                class="px-3.5 py-1.5 rounded-full text-[11px] font-bold tracking-wide transition active:scale-95 cursor-pointer flex items-center space-x-1.5 select-none {{ $divisionFilter === 'Volleyball Womens' ? 'bg-[#1e40af] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200/80 border border-slate-200' }}">
                            <span>Volleyball Women's</span>
                        </button>
                        <button type="button" onclick="setDivisionFilter('Volleyball Mens Division')"
                                class="px-3.5 py-1.5 rounded-full text-[11px] font-bold tracking-wide transition active:scale-95 cursor-pointer flex items-center space-x-1.5 select-none {{ $divisionFilter === 'Volleyball Mens Division' ? 'bg-[#1e40af] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200/80 border border-slate-200' }}">
                            <span>Volleyball Men's</span>
                        </button>
                    </div>
                    <script>
                        function setDivisionFilter(val) {
                            document.getElementById('divisionFilterInput').value = val;
                            document.getElementById('filterForm').submit();
                        }
                    </script>

                    <!-- Row 2: Limit & Clear Filters -->
                    <div class="flex items-center justify-between border-t border-slate-100 pt-4 mt-2">
                        <div class="flex items-center space-x-4">
                            <!-- Page Size Limit select -->
                            <div class="relative w-32 shrink-0">
                                <select name="limit" onchange="this.form.submit()" class="block w-full py-2 pl-3 pr-8 bg-slate-50/70 border border-slate-200/60 rounded-2xl text-[11px] text-slate-650 outline-none focus:bg-white focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition cursor-pointer appearance-none font-semibold">
                                    <option value="10" {{ $limit == 10 ? 'selected' : '' }}>10 rows</option>
                                    <option value="15" {{ $limit == 15 ? 'selected' : '' }}>15 rows</option>
                                    <option value="25" {{ $limit == 25 ? 'selected' : '' }}>25 rows</option>
                                    <option value="50" {{ $limit == 50 ? 'selected' : '' }}>50 rows</option>
                                    <option value="100" {{ $limit == 100 ? 'selected' : '' }}>100 rows</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>

                            <!-- Reset Filter Link -->
                            @if($search || $status || $yearFilter || $divisionFilter || $limit != 10)
                                <a href="{{ route('admin.sports-league.index') }}" class="inline-flex items-center text-[11px] font-bold text-slate-450 hover:text-slate-600 transition space-x-1 select-none cursor-pointer pl-2 py-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89H18v3.582"></path></svg>
                                    <span>Reset Filter</span>
                                </a>
                            @endif
                        </div>

                        <!-- Right Export CSV trigger -->
                        <div>
                            <button type="submit" class="hidden"></button>
                            <a href="{{ route('dashboard.export', ['sports']) }}" class="btn-primary text-[11px] font-black uppercase py-2 px-5 flex items-center space-x-1.5 cursor-pointer bg-emerald-600 hover:bg-emerald-700 active:scale-95 transition shadow-sm border border-transparent rounded-2xl h-[38px]">
                                <span>Export CSV</span>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tab Panels Contents -->
            <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                @if($paginatedRequests->isEmpty())
                    <div class="text-center py-12 text-slate-400 text-xs font-semibold">No registrations match the search filter.</div>
                @else
                    <!-- Sports Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/75 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                                    <th class="py-4 px-6">Submitted</th>
                                    <th class="py-4 px-6">Participant Name</th>
                                    <th class="py-4 px-6">Age/Gender</th>
                                    <th class="py-4 px-6">Tournament Details</th>
                                    <th class="py-4 px-6 text-center">Schedule Date</th>
                                    <th class="py-4 px-6 text-center">Status</th>
                                    <th class="py-4 px-6">Processed By</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-600">
                                @foreach($paginatedRequests as $req)
                                    <tr class="hover:bg-slate-50/50 transition duration-150">
                                        <td class="py-4 px-6 text-[10px] text-slate-400 font-semibold uppercase">{{ $req->created_at->format('M d, Y') }}</td>
                                        <td class="py-4 px-6 font-bold text-slate-800 hover:text-[#1e40af] transition">
                                            <a href="{{ route('admin.sports-league.show', $req->id) }}">{{ $req->last_name }}, {{ $req->first_name }}</a>
                                        </td>
                                        <td class="py-4 px-6 font-semibold">{{ $req->age }} / <span class="capitalize">{{ $req->gender }}</span></td>
                                        <td class="py-4 px-6">
                                            <span class="font-bold text-slate-800">{{ $req->sport }}</span>
                                            @if($req->division)
                                                <span class="block text-[10px] text-[#1e40af] font-black mt-0.5 uppercase tracking-wide">{{ $req->division }}</span>
                                            @endif
                                            <span class="block text-[10px] text-slate-400 font-semibold mt-0.5">Team: {{ $req->team_name ?? 'None' }}</span>
                                        </td>
                                        <td class="py-4 px-6 text-center font-bold text-slate-800">{{ $req->event_date->format('M d, Y') }}</td>
                                        <td class="py-4 px-6 text-center">
                                            <span class="badge-{{ $req->status }}">{{ ucfirst($req->status) }}</span>
                                        </td>
                                        <td class="py-4 px-6 font-semibold text-slate-700">
                                            @if(in_array($req->status, ['approved', 'declined']))
                                                {{ $req->processedBy ? $req->processedBy->name : 'Desk Officer' }}
                                            @else
                                                <span class="text-slate-400 font-medium">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Navigation -->
                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $paginatedRequests->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
