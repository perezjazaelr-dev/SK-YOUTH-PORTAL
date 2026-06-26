@extends('layouts.app')

@section('content')
<div x-data="{ 
    mobileSidebar: false,
    activeTab: ['health', 'medicine', 'silid', 'sports'].includes('{{ request()->query('tab') }}') ? '{{ request()->query('tab') }}' : 'health',
    stats: {
        health: {{ json_encode($healthStats) }},
        medicine: {{ json_encode($medicineStats) }},
        silid: {{ json_encode($silidStats) }},
        sports: {{ json_encode($sportsStats) }}
    },
    setActiveTab(tab) {
        this.activeTab = tab;
        const url = new URL(window.location);
        url.searchParams.set('tab', tab);
        window.history.pushState({}, '', url);
    }
}" class="flex-1 flex flex-col md:flex-row bg-[#f8fafc]">

    <!-- Left Sidebar Navigation -->
    @include('layouts.dashboard-sidebar')

    <!-- Overlay back shadow on mobile -->
    <div x-show="mobileSidebar" 
         @click="mobileSidebar = false" 
         class="fixed inset-0 bg-slate-900/40 z-20 md:hidden"
         x-cloak></div>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
        
        <!-- Mobile Sidebar Trigger Header -->
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

        <!-- Page Main Wrapper -->
        <div class="p-6 md:p-8 space-y-8 flex-1 overflow-y-auto font-sans">
            
            <!-- Breadcrumbs / Overview Top Bar -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider">
                    <a href="{{ route('dashboard.index') }}" class="text-slate-400 hover:text-[#1e40af]">Dashboard</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-800">Requests</span>
                </div>
            </div>

            <!-- Page Header -->
            <div class="space-y-1">
                <span class="text-[10px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Citizen Submissions</span>
                <h1 class="text-2xl font-black text-slate-800 font-display uppercase tracking-tight">Service Requests Evaluation</h1>
                <p class="text-xs text-slate-500">Search requestor profiles, review detailed logs, or download data files directly.</p>
            </div>

            <!-- Analytics Cards Widget -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Total Card -->
                <div class="card p-5 bg-white border border-slate-100 rounded-3xl shadow-sm flex flex-col justify-between hover:shadow-md transition">
                    <div class="flex items-center justify-between text-slate-400">
                        <span class="text-[9px] font-black uppercase tracking-wider">Total Submissions</span>
                        <x-category-icon name="dashboard" class="w-4 h-4 text-slate-400" />
                    </div>
                    <div class="mt-3">
                        <span class="block text-xl font-black font-display text-slate-800" x-text="stats[activeTab].total"></span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">All Records</span>
                    </div>
                </div>

                <!-- Pending Card -->
                <div class="card p-5 bg-white border border-slate-100 rounded-3xl shadow-sm flex flex-col justify-between hover:shadow-md transition">
                    <div class="flex items-center justify-between text-amber-500">
                        <span class="text-[9px] font-black uppercase tracking-wider">Pending Review</span>
                        <x-category-icon name="pending" class="w-4 h-4 text-amber-500" />
                    </div>
                    <div class="mt-3 flex items-baseline space-x-2">
                        <span class="block text-xl font-black font-display text-amber-600" x-text="stats[activeTab].pending"></span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Action Required</span>
                    </div>
                </div>

                <!-- Approved Card -->
                <div class="card p-5 bg-white border border-slate-100 rounded-3xl shadow-sm flex flex-col justify-between hover:shadow-md transition">
                    <div class="flex items-center justify-between text-emerald-500">
                        <span class="text-[9px] font-black uppercase tracking-wider">Approved / Active</span>
                        <x-category-icon name="health" class="w-4 h-4 text-emerald-500" />
                    </div>
                    <div class="mt-3">
                        <span class="block text-xl font-black font-display text-emerald-600" x-text="stats[activeTab].approved"></span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Completed / Scheduled</span>
                    </div>
                </div>

                <!-- Declined Card -->
                <div class="card p-5 bg-white border border-slate-100 rounded-3xl shadow-sm flex flex-col justify-between hover:shadow-md transition font-display">
                    <div class="flex items-center justify-between text-rose-500 font-sans">
                        <span class="text-[9px] font-black uppercase tracking-wider">Declined</span>
                        <x-category-icon name="logs" class="w-4 h-4 text-rose-500" />
                    </div>
                    <div class="mt-3 font-sans">
                        <span class="block text-xl font-black font-display text-rose-600" x-text="stats[activeTab].declined"></span>
                        <span class="text-[9px] font-bold text-slate-450 uppercase tracking-wider">Rejected Requests</span>
                    </div>
                </div>
            </div>

            <!-- Tabbed Requests Database Grid -->
            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <!-- Tab buttons -->
                    <div class="flex border-b border-slate-200 overflow-x-auto whitespace-nowrap scrollbar-none text-xs font-bold uppercase tracking-wider">
                        <button @click="setActiveTab('health')"
                                :class="activeTab === 'health' ? 'border-[#1e40af] text-[#1e40af]' : 'border-transparent text-slate-400 hover:text-slate-600'"
                                class="py-3 px-5 border-b-2 transition select-none flex items-center space-x-1.5 cursor-pointer">
                            <x-category-icon name="health" class="w-4 h-4" />
                            <span>Health Consult ({{ $healthRequests->total() }})</span>
                            @if($healthStats['pending'] > 0)
                                <span class="w-2 h-2 bg-rose-600 rounded-full inline-block shadow-sm animate-pulse ml-1 shrink-0"></span>
                            @endif
                        </button>
                        <button @click="setActiveTab('medicine')"
                                :class="activeTab === 'medicine' ? 'border-[#1e40af] text-[#1e40af]' : 'border-transparent text-slate-400 hover:text-slate-600'"
                                class="py-3 px-5 border-b-2 transition select-none flex items-center space-x-1.5 cursor-pointer">
                            <x-category-icon name="medicine" class="w-4 h-4" />
                            <span>Medicine Deliveries ({{ $medicineRequests->total() }})</span>
                            @if($medicineStats['pending'] > 0)
                                <span class="w-2 h-2 bg-rose-600 rounded-full inline-block shadow-sm animate-pulse ml-1 shrink-0"></span>
                            @endif
                        </button>
                        <button @click="setActiveTab('silid')"
                                :class="activeTab === 'silid' ? 'border-[#1e40af] text-[#1e40af]' : 'border-transparent text-slate-400 hover:text-slate-600'"
                                class="py-3 px-5 border-b-2 transition select-none flex items-center space-x-1.5 cursor-pointer">
                            <x-category-icon name="education" class="w-4 h-4" />
                            <span>Study Space bookings ({{ $silidRequests->total() }})</span>
                            @if($silidStats['pending'] > 0)
                                <span class="w-2 h-2 bg-rose-600 rounded-full inline-block shadow-sm animate-pulse ml-1 shrink-0"></span>
                            @endif
                        </button>
                        <button @click="setActiveTab('sports')"
                                :class="activeTab === 'sports' ? 'border-[#1e40af] text-[#1e40af]' : 'border-transparent text-slate-400 hover:text-slate-600'"
                                class="py-3 px-5 border-b-2 transition select-none flex items-center space-x-1.5 cursor-pointer">
                            <x-category-icon name="sports" class="w-4 h-4" />
                            <span>Sports Leagues ({{ $sportsRequests->total() }})</span>
                            @if($sportsStats['pending'] > 0)
                                <span class="w-2 h-2 bg-rose-600 rounded-full inline-block shadow-sm animate-pulse ml-1 shrink-0"></span>
                            @endif
                        </button>
                    </div>

                    </div>
                </div>

                <!-- Table Search & Status Filter Bar -->
                <div class="card p-6 bg-white border border-slate-100 rounded-3xl shadow-sm">
                    <form id="filterForm" method="GET" action="{{ route('dashboard.requests.index') }}" class="space-y-4">
                        <input type="hidden" id="tabInput" name="tab" :value="activeTab">
                        
                        <!-- Row 1: Search, Status, Year -->
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                            <!-- Search (Col span 6) -->
                            <div class="md:col-span-6 relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input 
                                    type="text" 
                                    name="search" 
                                    value="{{ $search }}" 
                                    placeholder="Search by requestor name or email address..." 
                                    class="pl-10 pr-4 py-2.5 w-full bg-slate-50/70 border border-slate-200/60 rounded-2xl text-xs outline-none focus:bg-white focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition font-sans placeholder-slate-400"
                                >
                            </div>

                            <!-- Status Dropdown (Col span 3) -->
                            <div class="md:col-span-3 relative">
                                <select 
                                    name="status" 
                                    onchange="this.form.submit()"
                                    class="block w-full py-2.5 pl-4 pr-10 bg-slate-50/70 border border-slate-200/60 rounded-2xl text-xs text-slate-700 outline-none focus:bg-white focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition cursor-pointer appearance-none"
                                >
                                    <option value="">All Statuses</option>
                                    <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="declined" {{ $status == 'declined' ? 'selected' : '' }}>Declined</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>

                            <!-- Year Dropdown (Col span 3) -->
                            <div class="md:col-span-3 relative">
                                <select 
                                    name="year" 
                                    onchange="this.form.submit()"
                                    class="block w-full py-2.5 pl-4 pr-10 bg-slate-50/70 border border-slate-200/60 rounded-2xl text-xs text-slate-700 outline-none focus:bg-white focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition cursor-pointer appearance-none"
                                >
                                    <option value="">All Submission Years</option>
                                    @foreach($years as $yr)
                                        <option value="{{ $yr }}" {{ $yearFilter == $yr ? 'selected' : '' }}>{{ $yr }} Year</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>

                        <!-- Row 2: Limit, Reset, CSV Export -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2 border-t border-slate-100/60">
                            <div class="flex items-center gap-3">
                                <!-- Page Size Limit select -->
                                <div class="relative w-32 shrink-0">
                                    <select 
                                        name="limit" 
                                        onchange="this.form.submit()"
                                        class="block w-full py-2 pl-3 pr-8 bg-slate-50/70 border border-slate-200/60 rounded-2xl text-[11px] text-slate-650 outline-none focus:bg-white focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition cursor-pointer appearance-none font-semibold"
                                    >
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
                                @if($search || $status || $yearFilter || $limit != 10)
                                    <a href="{{ route('dashboard.requests.index') }}" 
                                       class="inline-flex items-center text-[11px] font-bold text-slate-450 hover:text-slate-600 transition space-x-1 select-none cursor-pointer pl-2 py-1.5"
                                    >
                                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89H18v3.582"></path></svg>
                                        <span>Reset Filter</span>
                                    </a>
                                @endif
                            </div>

                            <!-- Right Primary Trigger -->
                            <div>
                                <button type="submit" class="hidden"></button>
                                <a :href="`{{ url('/dashboard/export') }}/${activeTab}`" 
                                   class="btn-primary text-[11px] font-black uppercase py-2 px-5 flex items-center space-x-1.5 cursor-pointer bg-emerald-600 hover:bg-emerald-700 active:scale-95 transition shadow-sm border border-transparent rounded-2xl">
                                    <span>Export CSV</span>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Tab Panels Contents -->
                <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                    
                    <!-- Panel 1: Health -->
                    <div x-show="activeTab === 'health'">
                        @if($healthRequests->isEmpty())
                            <div class="text-center py-12 text-slate-400 text-xs">No health consultation records match the search filter.</div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="bg-slate-50/75 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                                            <th class="py-4 px-6">Submitted</th>
                                            <th class="py-4 px-6">Name</th>
                                            <th class="py-4 px-6">Age/Gender</th>
                                            <th class="py-4 px-6">Email & Contact</th>
                                            <th class="py-4 px-6 text-center">Schedule</th>
                                            <th class="py-4 px-6 text-center">Status</th>
                                            <th class="py-4 px-6">Processed By</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-slate-600">
                                        @foreach($healthRequests as $req)
                                            <tr class="hover:bg-slate-50/50 transition duration-150">
                                                <td class="py-4 px-6 text-[10px] text-slate-400 font-semibold uppercase">{{ $req->created_at->format('M d, Y') }}</td>
                                                <td class="py-4 px-6 font-bold text-slate-800 hover:text-[#1e40af] transition">
                                                    <a href="{{ route('dashboard.requests.show', ['health', $req->id]) }}">{{ $req->last_name }}, {{ $req->first_name }}</a>
                                                </td>
                                                <td class="py-4 px-6 font-semibold">{{ $req->age }} / <span class="capitalize">{{ $req->gender }}</span></td>
                                                <td class="py-4 px-6 font-mono">{{ $req->email }}<br><span class="text-slate-400 text-[10px] font-semibold">{{ $req->contact_number }}</span></td>
                                                <td class="py-4 px-6 text-center">
                                                    <span class="block font-bold text-slate-800">{{ $req->preferred_date->format('M d, Y') }}</span>
                                                    <span class="block text-[10px] text-[#1e40af] font-semibold mt-0.5">{{ $req->preferred_time }}</span>
                                                </td>
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
                            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                                {{ $healthRequests->links() }}
                            </div>
                        @endif
                    </div>

                    <!-- Panel 2: Medicine -->
                    <div x-show="activeTab === 'medicine'" x-cloak>
                        @if($medicineRequests->isEmpty())
                            <div class="text-center py-12 text-slate-400 text-xs">No medicine request records match the search filter.</div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="bg-slate-50/75 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                                            <th class="py-4 px-6">Submitted</th>
                                            <th class="py-4 px-6">Requestor Name</th>
                                            <th class="py-4 px-6">Age/Gender</th>
                                            <th class="py-4 px-6">Email & Contact</th>
                                            <th class="py-4 px-6">Address</th>
                                            <th class="py-4 px-6 text-center">Status</th>
                                            <th class="py-4 px-6">Processed By</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-slate-600">
                                        @foreach($medicineRequests as $req)
                                            <tr class="hover:bg-slate-50/50 transition duration-150">
                                                <td class="py-4 px-6 text-[10px] text-slate-400 font-semibold uppercase">{{ $req->created_at->format('M d, Y') }}</td>
                                                <td class="py-4 px-6 font-bold text-slate-800 hover:text-[#1e40af] transition">
                                                    <a href="{{ route('dashboard.requests.show', ['medicine', $req->id]) }}">{{ $req->requestor_last_name }}, {{ $req->requestor_first_name }}</a>
                                                </td>
                                                <td class="py-4 px-6 font-semibold">{{ $req->requestor_age }} / <span class="capitalize">{{ $req->requestor_gender }}</span></td>
                                                <td class="py-4 px-6 font-mono">{{ $req->email }}<br><span class="text-slate-400 text-[10px] font-semibold">{{ $req->contact_number }}</span></td>
                                                <td class="py-4 px-6 max-w-xs truncate" title="{{ $req->complete_address }}">{{ $req->complete_address }}</td>
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
                            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                                {{ $medicineRequests->links() }}
                            </div>
                        @endif
                    </div>

                    <!-- Panel 3: Silid -->
                    <div x-show="activeTab === 'silid'" x-cloak>
                        @if($silidRequests->isEmpty())
                            <div class="text-center py-12 text-slate-400 text-xs">No Silid Karunungan requests match the search filter.</div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="bg-slate-50/75 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                                            <th class="py-4 px-6">Submitted</th>
                                            <th class="py-4 px-6">Requestor Name</th>
                                            <th class="py-4 px-6">Age</th>
                                            <th class="py-4 px-6">Email & Contact</th>
                                            <th class="py-4 px-6 text-center">Preferred Schedule</th>
                                            <th class="py-4 px-6 text-center">Status</th>
                                            <th class="py-4 px-6">Processed By</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-slate-600">
                                        @foreach($silidRequests as $req)
                                            <tr class="hover:bg-slate-50/50 transition duration-150">
                                                <td class="py-4 px-6 text-[10px] text-slate-400 font-semibold uppercase">{{ $req->created_at->format('M d, Y') }}</td>
                                                <td class="py-4 px-6 font-bold text-slate-800 hover:text-[#1e40af] transition">
                                                    <a href="{{ route('dashboard.requests.show', ['silid', $req->id]) }}">{{ $req->requestor_last_name }}, {{ $req->requestor_first_name }} {{ $req->requestor_middle_name }}</a>
                                                </td>
                                                <td class="py-4 px-6 font-semibold">{{ $req->requestor_age }} yrs</td>
                                                <td class="py-4 px-6 font-mono">{{ $req->email }}<br><span class="text-slate-400 text-[10px] font-semibold">{{ $req->contact_number }}</span></td>
                                                <td class="py-4 px-6 text-center">
                                                    <span class="block font-bold text-slate-800">{{ $req->preferred_date->format('M d, Y') }}</span>
                                                    <span class="block text-[10px] text-[#1e40af] font-semibold mt-0.5">{{ $req->preferred_time }}</span>
                                                </td>
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
                            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                                {{ $silidRequests->links() }}
                            </div>
                        @endif
                    </div>

                    <!-- Panel 4: Sports -->
                    <div x-show="activeTab === 'sports'" x-cloak>
                        @if($sportsRequests->isEmpty())
                            <div class="text-center py-12 text-slate-400 text-xs">No sports registration records match the search filter.</div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="bg-slate-50/75 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                                            <th class="py-4 px-6">Submitted</th>
                                            <th class="py-4 px-6">Participant Name</th>
                                            <th class="py-4 px-6">Age/Gender</th>
                                            <th class="py-4 px-6">Sport (Team)</th>
                                            <th class="py-4 px-6 text-center">Schedule Date</th>
                                            <th class="py-4 px-6 text-center">Status</th>
                                            <th class="py-4 px-6">Processed By</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-slate-600">
                                        @foreach($sportsRequests as $req)
                                            <tr class="hover:bg-slate-50/50 transition duration-150">
                                                <td class="py-4 px-6 text-[10px] text-slate-400 font-semibold uppercase">{{ $req->created_at->format('M d, Y') }}</td>
                                                <td class="py-4 px-6 font-bold text-slate-800 hover:text-[#1e40af] transition">
                                                    <a href="{{ route('dashboard.requests.show', ['sports', $req->id]) }}">{{ $req->last_name }}, {{ $req->first_name }}</a>
                                                </td>
                                                <td class="py-4 px-6 font-semibold">{{ $req->age }} / <span class="capitalize">{{ $req->gender }}</span></td>
                                                <td class="py-4 px-6">
                                                    <span class="font-bold text-slate-800">{{ $req->sport }}</span>
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
                            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                                {{ $sportsRequests->links() }}
                            </div>
                        @endif
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
@endsection
