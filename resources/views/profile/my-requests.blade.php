@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-1 font-sans">
            
            <!-- Page Header -->
            <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-100">
                <div>
                    <span class="text-xs font-black tracking-widest text-[#1e40af] uppercase block">Citizen Portal</span>
                    <h1 class="text-2xl font-black text-slate-800 font-display uppercase tracking-tight mt-1">My Submitted Requests</h1>
                    <p class="text-xs text-slate-500 mt-1">Review and monitor the status of requests submitted under your email address ({{ auth()->user()->email }}).</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="/" class="btn-primary text-xs py-2 px-4 shadow-sm bg-[#1e40af] hover:bg-blue-700 font-bold rounded-xl text-white transition cursor-pointer">New Request</a>
                    <a href="{{ route('profile.edit') }}" class="btn-outline text-xs py-2 px-4 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 transition cursor-pointer font-bold">Account Settings</a>
                </div>
            </div>

            <!-- Session Notifications -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-805 rounded-2xl text-xs font-semibold flex items-center gap-2 shadow-sm animate-fade-in">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-805 rounded-2xl text-xs font-semibold flex items-center gap-2 shadow-sm animate-fade-in">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 text-blue-805 rounded-2xl text-xs font-semibold flex items-center gap-2 shadow-sm animate-fade-in">
                    <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

    <!-- Profiling Notice Banner -->
    @if(!$profile)
        <div class="mb-8 p-5 bg-rose-50 border border-rose-250 rounded-2xl flex flex-col gap-4 text-rose-850 shadow-sm animate-pulse-subtle">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="flex items-start gap-3">
                    <span class="text-xl">⚠️</span>
                    <div class="flex-1 space-y-0.5">
                        <strong class="font-bold block uppercase tracking-wide text-[10px] text-rose-900">Profile Registry Pending (0% Complete)</strong>
                        <p class="text-xs leading-relaxed">You have not registered your Katipunan ng Kabataan profiling form in this system. <strong>All services and sports league registrations are currently locked.</strong> Please complete it to unlock these options.</p>
                    </div>
                </div>
                <a href="{{ route('profile.profiling.create') }}" class="btn-primary text-xs font-black uppercase py-2.5 px-5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl active:scale-95 transition shadow-sm shrink-0 text-center">
                    Complete Self Profiling
                </a>
            </div>
            <div class="space-y-1">
                <div class="flex justify-between text-[9px] font-bold text-rose-800 uppercase tracking-wider">
                    <span>Completeness Status</span>
                    <span>0% Complete</span>
                </div>
                <div class="w-full bg-rose-100 border border-rose-200/50 h-3 rounded-full overflow-hidden">
                    <div class="bg-rose-450 h-full rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>
        </div>
    @elseif($profile->status === 'pending')
        <div class="mb-8 p-5 bg-amber-50 border border-amber-250 rounded-2xl flex flex-col gap-4 text-amber-850 shadow-sm animate-pulse-subtle">
            <div class="flex items-start gap-3">
                <span class="text-xl">⏳</span>
                <div class="flex-1 space-y-0.5">
                    <strong class="font-bold block uppercase tracking-wide text-[10px] text-amber-900">Awaiting Admin Review (50% Complete)</strong>
                    <p class="text-xs">Your self-profiling has been submitted and is currently pending review by our desk officers. All services and registrations will unlock once approved.</p>
                </div>
            </div>
            <div class="space-y-1">
                <div class="flex justify-between text-[9px] font-bold text-amber-850 uppercase tracking-wider">
                    <span>Completeness Status</span>
                    <span>50% Complete (Pending Approval)</span>
                </div>
                <div class="w-full bg-amber-100 border border-amber-200/50 h-3 rounded-full overflow-hidden">
                    <div class="bg-amber-500 h-full rounded-full transition-all duration-300" style="width: 50%"></div>
                </div>
            </div>
            <!-- Stepper progress bar -->
            <div class="pt-4 border-t border-amber-200/30">
                <div class="w-full max-w-xl mx-auto py-2">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-400 select-none">
                        <!-- Step 1 -->
                        <div class="flex flex-col items-center relative text-amber-700">
                            <div class="w-7 h-7 rounded-full bg-amber-600 text-white flex items-center justify-center font-bold text-[10px]">1</div>
                            <span class="mt-1 text-[9px] uppercase font-bold tracking-wider text-amber-700">Submitted</span>
                        </div>
                        <div class="flex-1 border-t-2 mx-2 border-slate-200"></div>

                        <!-- Step 2 -->
                        <div class="flex flex-col items-center relative text-slate-405">
                            <div class="w-7 h-7 rounded-full border-2 border-slate-200 bg-white text-slate-400 flex items-center justify-center font-bold text-[10px]">2</div>
                            <span class="mt-1 text-[9px] uppercase font-bold tracking-wider text-slate-400">Under Review</span>
                        </div>
                        <div class="flex-1 border-t-2 mx-2 border-slate-200"></div>

                        <!-- Step 3 -->
                        <div class="flex flex-col items-center relative text-slate-405">
                            <div class="w-7 h-7 rounded-full border-2 border-slate-200 bg-white text-slate-400 flex items-center justify-center font-bold text-[10px]">3</div>
                            <span class="mt-1 text-[9px] uppercase font-bold tracking-wider text-slate-400">Verified</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif($profile->status === 'declined')
        <div class="mb-8 p-5 bg-rose-50 border border-rose-250 rounded-2xl flex flex-col gap-4 text-rose-850 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="flex items-start gap-3">
                    <span class="text-xl">❌</span>
                    <div class="flex-1 space-y-0.5">
                        <strong class="font-bold block uppercase tracking-wide text-[10px] text-rose-900">Self-Profiling Declined (0% Complete)</strong>
                        <p class="text-xs">Your self-profiling registry has been declined by the admin/staff. Please review your details and re-submit.</p>
                    </div>
                </div>
                <a href="{{ route('profile.profiling.create') }}" class="btn-primary text-xs font-black uppercase py-2.5 px-5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl active:scale-95 transition shadow-sm shrink-0 text-center">
                    Re-submit Profiling
                </a>
            </div>
            <div class="space-y-1">
                <div class="flex justify-between text-[9px] font-bold text-rose-800 uppercase tracking-wider">
                    <span>Completeness Status</span>
                    <span>0% Complete (Declined)</span>
                </div>
                <div class="w-full bg-rose-100 border border-rose-200/50 h-3 rounded-full overflow-hidden">
                    <div class="bg-rose-450 h-full rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>
            <!-- Stepper progress bar -->
            <div class="pt-4 border-t border-rose-200/30">
                <div class="w-full max-w-xl mx-auto py-2">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-400 select-none">
                        <!-- Step 1 -->
                        <div class="flex flex-col items-center relative text-rose-700">
                            <div class="w-7 h-7 rounded-full bg-rose-600 text-white flex items-center justify-center font-bold text-[10px]">1</div>
                            <span class="mt-1 text-[9px] uppercase font-bold tracking-wider text-rose-700">Submitted</span>
                        </div>
                        <div class="flex-1 border-t-2 mx-2 border-rose-600"></div>

                        <!-- Step 2 -->
                        <div class="flex flex-col items-center relative text-rose-700">
                            <div class="w-7 h-7 rounded-full bg-rose-600 text-white flex items-center justify-center font-bold text-[10px]">2</div>
                            <span class="mt-1 text-[9px] uppercase font-bold tracking-wider text-rose-700">Under Review</span>
                        </div>
                        <div class="flex-1 border-t-2 mx-2 border-rose-600"></div>

                        <!-- Step 3 -->
                        <div class="flex flex-col items-center relative text-rose-700">
                            <div class="w-7 h-7 rounded-full bg-rose-600 text-white flex items-center justify-center font-bold text-[10px]">3</div>
                            <span class="mt-1 text-[9px] uppercase font-bold tracking-wider text-rose-700">Declined</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif($profile->status === 'approved')
        <div class="mb-8 p-5 bg-emerald-50 border border-emerald-250 rounded-2xl flex flex-col gap-4 text-emerald-850 shadow-sm animate-fade-in">
            <div class="flex items-start gap-3">
                <span class="text-xl">🛡️</span>
                <div class="flex-1 space-y-0.5">
                    <strong class="font-bold block uppercase tracking-wide text-[10px] text-emerald-900">KK Profile Verified (100% Complete)</strong>
                    <p class="text-xs">Your Katipunan ng Kabataan profile registry is active. You can now request services and register for the sports league.</p>
                </div>
            </div>
            <div class="space-y-1">
                <div class="flex justify-between text-[9px] font-bold text-emerald-800 uppercase tracking-wider">
                    <span>Completeness Status</span>
                    <span>100% Complete</span>
                </div>
                <div class="w-full bg-emerald-100 border border-emerald-200/50 h-3 rounded-full overflow-hidden">
                    <div class="bg-emerald-600 h-full rounded-full transition-all duration-300" style="width: 100%"></div>
                </div>
            </div>
            <!-- Stepper progress bar -->
            <div class="pt-4 border-t border-emerald-200/30">
                <div class="w-full max-w-xl mx-auto py-2">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-400 select-none">
                        <!-- Step 1 -->
                        <div class="flex flex-col items-center relative text-emerald-700">
                            <div class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-[10px]">1</div>
                            <span class="mt-1 text-[9px] uppercase font-bold tracking-wider text-emerald-700">Submitted</span>
                        </div>
                        <div class="flex-1 border-t-2 mx-2 border-emerald-600"></div>

                        <!-- Step 2 -->
                        <div class="flex flex-col items-center relative text-emerald-700">
                            <div class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-[10px]">2</div>
                            <span class="mt-1 text-[9px] uppercase font-bold tracking-wider text-emerald-700">Under Review</span>
                        </div>
                        <div class="flex-1 border-t-2 mx-2 border-emerald-600"></div>

                        <!-- Step 3 -->
                        <div class="flex flex-col items-center relative text-emerald-700">
                            <div class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-[10px]">3</div>
                            <span class="mt-1 text-[9px] uppercase font-bold tracking-wider text-emerald-700">Verified</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Stat count badges in a simple, professional row -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white border border-slate-100 rounded-2xl p-4 flex items-center justify-between shadow-sm">
            <div>
                <span class="block text-2xl font-black text-slate-800">{{ $total }}</span>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Total Filed</span>
            </div>
            <div class="w-8 h-8 rounded-lg bg-slate-50 text-slate-400 flex items-center justify-center font-bold text-sm">📋</div>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-4 flex items-center justify-between shadow-sm border-l-4 border-l-amber-400">
            <div>
                <span class="block text-2xl font-black text-slate-800">{{ $pending }}</span>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Pending</span>
            </div>
            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center font-bold text-sm">⏳</div>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-4 flex items-center justify-between shadow-sm border-l-4 border-l-emerald-500">
            <div>
                <span class="block text-2xl font-black text-slate-800">{{ $approved }}</span>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Approved</span>
            </div>
            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center font-bold text-sm">✓</div>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-4 flex items-center justify-between shadow-sm border-l-4 border-l-rose-500">
            <div>
                <span class="block text-2xl font-black text-slate-800">{{ $declined }}</span>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Declined</span>
            </div>
            <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center font-bold text-sm">✗</div>
        </div>
    </div>

    <!-- Requests list table -->
    <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
        @if($results->isEmpty())
            <div class="text-center py-16 px-4 space-y-4">
                <div class="w-12 h-12 bg-slate-50 text-slate-350 rounded-2xl flex items-center justify-center mx-auto text-xl">📄</div>
                <div>
                    <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">No requests submitted yet</h3>
                    <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto leading-relaxed">You haven't submitted any service requests under your email address.</p>
                </div>
                <a href="/" class="inline-block px-5 py-2 bg-[#1e40af] text-white text-xs font-bold uppercase tracking-wider rounded-xl hover:bg-blue-700 transition">Create First Request</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/75 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                            <th class="py-4 px-6">Reference No</th>
                            <th class="py-4 px-6">Service Type</th>
                            <th class="py-4 px-6">Details</th>
                            <th class="py-4 px-6">Filed Date</th>
                            <th class="py-4 px-6 text-center">Status</th>
                            <th class="py-4 px-6">Processed By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-600">
                        @foreach($results as $req)
                            @php
                                $referenceNumber = 'SK-' . $req->type_prefix . '-' . str_pad($req->id, 5, '0', STR_PAD_LEFT);
                                
                                $badgeClass = match($req->status) {
                                    'approved' => 'badge-approved',
                                    'declined' => 'badge-declined',
                                    'review' => 'badge-review',
                                    default => 'badge-pending'
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-4 px-6 font-mono font-bold text-slate-800">{{ $referenceNumber }}</td>
                                <td class="py-4 px-6">
                                    <span class="px-2.5 py-0.5 bg-blue-50 text-blue-800 rounded-full text-[9px] font-extrabold uppercase tracking-wide">{{ $req->type_label }}</span>
                                </td>
                                <td class="py-4 px-6 font-medium text-slate-700 max-w-sm" title="{{ $req->detail }}">
                                    <div>{{ $req->detail }}</div>
                                    @if(!empty($req->custom_fields) && is_array($req->custom_fields))
                                        <div class="flex flex-wrap gap-x-2 text-[9px] text-slate-400 mt-0.5">
                                            @foreach($req->custom_fields as $key => $val)
                                                <span>{{ ucwords(str_replace('_', ' ', $key)) }}: <strong class="text-slate-500 font-semibold">{{ $val }}</strong></span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-slate-400 font-medium">{{ $req->created_at->format('M d, Y') }}</td>
                                <td class="py-4 px-6 text-center">
                                    <span class="{{ $badgeClass }}">{{ ucfirst($req->status) }}</span>
                                </td>
                                <td class="py-4 px-6 font-semibold text-slate-700">
                                    @if(in_array($req->status, ['approved', 'declined']))
                                        {{ $req->processedBy ? $req->processedBy->name : 'Desk Officer' }}
                                    @else
                                        <span class="text-slate-400 font-medium">Pending Review</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
        @endif
</div>
@endsection
