@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-1 font-sans">
    
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

    <!-- Profiling Notice Banner -->
    @if($hasProfile)
        <div class="mb-8 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-between text-emerald-800 shadow-sm">
            <div class="flex items-center space-x-3 text-xs font-medium">
                <span class="text-lg">🟢</span>
                <div>
                    <strong class="font-bold block uppercase tracking-wide text-[10px] text-emerald-900">KK Profiling Registered</strong>
                    <span>Your Katipunan ng Kabataan profile registry is active and verified.</span>
                </div>
            </div>
        </div>
    @else
        <div class="mb-8 p-5 bg-amber-50 border border-amber-250 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-amber-850 shadow-sm animate-pulse-subtle">
            <div class="flex items-start space-x-3 text-xs leading-relaxed">
                <span class="text-lg mt-0.5">⚠️</span>
                <div>
                    <strong class="font-bold block uppercase tracking-wide text-[10px] text-amber-900">Profile Registry Required</strong>
                    <span>You have not registered your Katipunan ng Kabataan profiling form in this system. Please complete it to help us serve you better.</span>
                </div>
            </div>
            <a href="{{ route('profile.profiling.create') }}" class="btn-primary text-xs font-black uppercase py-2.5 px-5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl active:scale-95 transition shadow-sm shrink-0 text-center">
                Complete Self Profiling
            </a>
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
            </div>
        @endif
    </div>

</div>
@endsection
