@extends('layouts.app')

@section('content')
<div x-data="{ selectedConsultation: null, replyMessage: '', mobileSidebar: false }" class="flex-1 flex flex-col md:flex-row bg-[#f8fafc] dark:bg-slate-955">

    <!-- Left Sidebar -->
    @include('layouts.dashboard-sidebar')

    <div x-show="mobileSidebar" @click="mobileSidebar = false" class="fixed inset-0 bg-slate-900/40 z-20 md:hidden" x-cloak></div>

    <!-- Main Content Pane -->
    <div class="flex-1 flex flex-col min-w-0">
        
        <header class="bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800 h-16 px-4 flex items-center justify-between md:hidden shrink-0">
            <button @click="mobileSidebar = true" class="p-2 text-slate-500 hover:text-slate-800 dark:text-slate-405 dark:hover:text-white active:scale-95 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
            <div class="flex items-center space-x-2">
                <img src="{{ asset('images/logo.png') }}" class="w-8 h-8 object-contain rounded-full bg-white p-0.5 border" alt="SK Logo">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-white font-display">SK Namayan</span>
            </div>
            <div class="w-10"></div>
        </header>

        <div class="p-6 md:p-8 space-y-6 flex-1 overflow-y-auto">
            
            <!-- Breadcrumbs -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider">
                    <a href="{{ route('dashboard.index') }}" class="text-slate-400 hover:text-[#1e40af] dark:text-slate-500 dark:hover:text-blue-455 transition duration-150">Dashboard</a>
                    <span class="text-slate-300 dark:text-slate-700">/</span>
                    <span class="text-slate-800 dark:text-slate-200 font-semibold">Anonymous Consultations</span>
                </div>
            </div>

            <!-- Page Title -->
            <div class="space-y-1">
                <div class="flex items-center space-x-2">
                    <span class="text-[10px] font-black text-[#1e40af] dark:text-blue-400 uppercase tracking-widest block font-display">SKonsulta platform</span>
                    <span class="px-2 py-0.5 bg-blue-50 dark:bg-blue-950/40 text-[#1e40af] dark:text-blue-400 border border-blue-100 dark:border-blue-900/30 rounded-md text-[9px] font-bold uppercase font-mono">
                        {{ $consultations->total() }} Total Submissions
                    </span>
                </div>
                <h1 class="text-2xl font-black tracking-tight text-slate-800 dark:text-white font-display uppercase mt-1">Consultation Inbox</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Securely manage, update, and reply to youth inquiries under full user anonymity.</p>
            </div>

            <!-- Search & Filters Card -->
            <div class="card p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl shadow-sm">
                <form method="GET" action="{{ route('admin.consultations.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <!-- Search Box (Col span 8) -->
                        <div class="md:col-span-8 relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ $search }}" 
                                placeholder="Search by tracking ID, subject, message details..." 
                                class="pl-10 pr-4 py-2.5 w-full bg-slate-50/70 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 rounded-2xl text-xs outline-none focus:bg-white dark:focus:bg-slate-950 focus:border-[#1e40af] dark:focus:border-blue-500 focus:ring-4 focus:ring-blue-600/5 dark:text-white transition font-sans placeholder-slate-400 dark:placeholder-slate-500"
                            >
                        </div>

                        <!-- Status Filter Dropdown (Col span 4) -->
                        <div class="md:col-span-4 relative">
                            <select 
                                name="status" 
                                onchange="this.form.submit()"
                                class="block w-full py-2.5 pl-4 pr-10 bg-slate-50/70 dark:bg-slate-955/40 border border-slate-200/60 dark:border-slate-800 rounded-2xl text-xs text-slate-705 dark:text-slate-300 outline-none focus:bg-white dark:focus:bg-slate-950 focus:border-[#1e40af] dark:focus:border-blue-500 focus:ring-4 focus:ring-blue-600/5 transition cursor-pointer appearance-none"
                            >
                                <option value="">All Statuses</option>
                                <option value="Pending" {{ $statusFilter === 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="In Review" {{ $statusFilter === 'In Review' ? 'selected' : '' }}>In Review</option>
                                <option value="Resolved" {{ $statusFilter === 'Resolved' ? 'selected' : '' }}>Resolved</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Inbox List/Table -->
            <div class="card p-0 overflow-hidden bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl shadow-sm">
                @if($consultations->isEmpty())
                    <div class="text-center py-16 px-4 space-y-4">
                        <div class="w-16 h-16 bg-slate-50 dark:bg-slate-950 text-slate-400 dark:text-slate-600 border border-slate-100 dark:border-slate-800 rounded-3xl flex items-center justify-center mx-auto text-2xl">📥</div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">No Consultations Found</h3>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 max-w-sm mx-auto">There are no consultations matching your filter or inbox is empty.</p>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/75 dark:bg-slate-950 border-b border-slate-100 dark:border-slate-800 text-[10px] font-bold text-slate-400 dark:text-slate-555 uppercase tracking-wider font-display">
                                    <th class="p-4 pl-6">Tracking ID</th>
                                    <th class="p-4">Subject</th>
                                    <th class="p-4">Status</th>
                                    <th class="p-4">Last Updated</th>
                                    <th class="p-4 pr-6 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-xs">
                                @foreach($consultations as $item)
                                    @php
                                        $badgeColor = match($item->status) {
                                            'Pending' => 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-900/30',
                                            'In Review' => 'bg-blue-50 dark:bg-blue-955/40 text-blue-700 dark:text-blue-400 border-blue-150 dark:border-blue-900/30',
                                            'Resolved' => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-900/30',
                                            default => 'bg-slate-50 dark:bg-slate-900 text-slate-700 border-slate-100'
                                        };
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition duration-150">
                                        <td class="p-4 pl-6 font-mono text-slate-800 dark:text-white font-bold whitespace-nowrap">{{ $item->tracking_id }}</td>
                                        <td class="p-4 font-semibold text-slate-700 dark:text-slate-200">
                                            <span class="block truncate max-w-xs">{{ $item->subject }}</span>
                                            <span class="block text-[10px] text-slate-400 dark:text-slate-500 font-normal truncate max-w-xs">{{ Str::limit($item->message, 80) }}</span>
                                        </td>
                                        <td class="p-4 whitespace-nowrap">
                                            <span class="px-2.5 py-0.5 border rounded-full text-[9px] font-extrabold uppercase tracking-wide font-display {{ $badgeColor }}">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                        <td class="p-4 text-slate-500 dark:text-slate-400 font-mono whitespace-nowrap">
                                            {{ $item->updated_at?->format('Y-m-d H:i') }}
                                            <span class="text-[10px] text-slate-400 dark:text-slate-500 block">{{ $item->updated_at?->diffForHumans() }}</span>
                                        </td>
                                        <td class="p-4 pr-6 text-right whitespace-nowrap">
                                            <button @click='selectedConsultation = {
                                                        id: {{ $item->id }},
                                                        tracking_id: "{{ $item->tracking_id }}",
                                                        subject: "{{ addslashes($item->subject) }}",
                                                        message: "{{ addslashes($item->message) }}",
                                                        status: "{{ $item->status }}",
                                                        date: "{{ $item->created_at?->format("Y-m-d H:i:s") }}",
                                                        replies: @json($item->replies ?? []),
                                                        status_route: "{{ route("admin.consultations.update-status", $item) }}",
                                                        reply_route: "{{ route("admin.consultations.reply", $item) }}"
                                                    }' 
                                                    class="inline-flex items-center px-2.5 py-1 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:text-[#1e40af] dark:hover:text-blue-400 hover:border-[#1e40af] dark:hover:border-blue-500 font-bold rounded-lg transition text-[10px] uppercase tracking-wider active:scale-95">
                                                Review & Reply
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($consultations->hasPages())
                        <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                            {{ $consultations->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>

    </div>

    <!-- Review & Reply Modal -->
    <div x-show="selectedConsultation !== null" 
         class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0 flex items-center justify-center" 
         style="display: none;"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak>
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-xs transition-opacity" 
             @click="selectedConsultation = null"></div>

        <!-- Modal Card -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl overflow-hidden shadow-2xl transform transition-all sm:w-full sm:max-w-2xl mx-auto z-10 border border-slate-100 dark:border-slate-800 max-h-[90vh] flex flex-col"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
             
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-700 to-blue-900 dark:from-slate-800 dark:to-slate-950 px-6 py-4 flex items-center justify-between text-white shrink-0 border-b border-transparent dark:border-slate-800">
                <div>
                    <span class="text-[9px] font-black uppercase tracking-widest text-blue-200 dark:text-blue-300">Consultation Inspector</span>
                    <h3 class="text-sm font-extrabold uppercase tracking-wide font-display mt-0.5 text-white" x-text="selectedConsultation ? 'Review Request: ' + selectedConsultation.tracking_id : ''"></h3>
                </div>
                <button @click="selectedConsultation = null" class="text-white/80 hover:text-white transition active:scale-95 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-6 space-y-5 overflow-y-auto flex-1 text-xs">
                
                <!-- Status Update Panel -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/60 border border-slate-100 dark:border-slate-800">
                    <div>
                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Submitted On</span>
                        <span class="font-mono text-slate-700 dark:text-slate-350 text-[10px]" x-text="selectedConsultation ? selectedConsultation.date : ''"></span>
                    </div>
                    
                    <form method="POST" :action="selectedConsultation ? selectedConsultation.status_route : ''" class="flex items-center gap-2">
                        @csrf
                        @method('PATCH')
                        <label class="text-[9px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Status:</label>
                        <select name="status" @change="this.form.submit()" class="py-1.5 pl-3 pr-8 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-bold text-slate-700 dark:text-slate-300 outline-none focus:border-[#1e40af] focus:ring-2 focus:ring-blue-600/5 transition cursor-pointer">
                            <option value="Pending" :selected="selectedConsultation && selectedConsultation.status === 'Pending'">Pending</option>
                            <option value="In Review" :selected="selectedConsultation && selectedConsultation.status === 'In Review'">In Review</option>
                            <option value="Resolved" :selected="selectedConsultation && selectedConsultation.status === 'Resolved'">Resolved</option>
                        </select>
                    </form>
                </div>

                <!-- Message Inquiry Block -->
                <div class="space-y-1.5">
                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Subject Inquiry</span>
                    <h4 class="text-sm font-extrabold text-slate-800 dark:text-white" x-text="selectedConsultation ? selectedConsultation.subject : ''"></h4>
                    
                    <div class="p-4 bg-slate-50 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800 rounded-2xl text-slate-700 dark:text-slate-300 leading-relaxed font-sans mt-2 break-words" x-text="selectedConsultation ? selectedConsultation.message : ''"></div>
                </div>

                <!-- Reply History Pane -->
                <div class="space-y-2">
                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Consultation History & Replies</span>
                    
                    <div class="space-y-3 max-h-[220px] overflow-y-auto pr-1">
                        <template x-if="selectedConsultation && selectedConsultation.replies && selectedConsultation.replies.length > 0">
                            <div class="space-y-2">
                                <template x-for="(reply, index) in selectedConsultation.replies" :key="index">
                                    <div class="p-3.5 rounded-2xl border bg-[#f8fafc] dark:bg-slate-950 border-slate-100 dark:border-slate-850 flex flex-col gap-1">
                                        <div class="flex items-center justify-between">
                                            <span class="font-extrabold text-slate-800 dark:text-slate-200" x-text="reply.sender"></span>
                                            <span class="text-[9px] font-mono text-slate-400 dark:text-slate-500" x-text="reply.timestamp"></span>
                                        </div>
                                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed font-sans break-words" x-text="reply.message"></p>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="!selectedConsultation || !selectedConsultation.replies || selectedConsultation.replies.length === 0">
                            <p class="text-slate-400 italic py-2 text-center">// Inquirer is awaiting a reply.</p>
                        </template>
                    </div>
                </div>

                <!-- Admin Response Form -->
                <form method="POST" :action="selectedConsultation ? selectedConsultation.reply_route : ''" class="space-y-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                    @csrf
                    <div>
                        <label class="text-[9px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1 block">Compose Reply Message</label>
                        <textarea 
                            name="message" 
                            x-model="replyMessage"
                            required
                            placeholder="Type response message here... User will be able to retrieve this anonymously using their tracking token ID." 
                            rows="4" 
                            class="w-full rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-950 px-4 py-3 text-xs dark:text-white outline-none focus:bg-white focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition resize-none placeholder-slate-400 dark:placeholder-slate-500"
                        ></textarea>
                    </div>
                    
                    <div class="flex items-center justify-end gap-3">
                        <button type="button" @click="selectedConsultation = null" class="px-4 py-2 rounded-xl text-[11px] font-bold uppercase text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 transition">Close</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#1e40af] hover:bg-blue-700 text-white text-[11px] font-black uppercase tracking-wider transition active:scale-95 shadow-sm">Send Response</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
