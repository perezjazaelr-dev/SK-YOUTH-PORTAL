@extends('layouts.app')

@section('content')
<div class="flex-1 bg-[#f8fafc] dark:bg-slate-950 font-sans min-h-screen py-12" x-data="skonsultaTracker()">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
            <a href="{{ route('landing') }}" class="hover:text-[#1e40af] dark:hover:text-blue-400">Home</a>
            <span class="text-slate-300">/</span>
            <a href="{{ route('skonsulta.index') }}" class="hover:text-[#1e40af] dark:hover:text-blue-400">SKONSULTA</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-800 dark:text-slate-100 font-bold">Track Inquiries</span>
        </div>

        <!-- Page Header -->
        <div class="space-y-2">
            <h1 class="text-3xl font-black text-slate-800 dark:text-white font-display uppercase tracking-tight leading-tight">
                Track Inquiry Status
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                Enter your unique SKO- tracking ID to check the evaluation status, review details, and read replies from Sangguniang Kabataan administrative officers.
            </p>
        </div>

        <!-- Tracking search form card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
            <form @submit.prevent="trackRequest" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <input type="text" x-model="searchId" required placeholder="e.g. SKO-XXXXXX" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 px-4 py-3 text-xs dark:text-white outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/5 transition font-mono uppercase font-black">
                </div>
                <button type="submit" :disabled="loading" class="sm:w-auto px-6 py-3 rounded-xl bg-slate-850 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white text-[11px] font-black uppercase tracking-wider transition active:scale-95 shadow-sm shrink-0">
                    <span x-text="loading ? 'Tracking...' : 'Search'"></span>
                </button>
            </form>
            
            <div x-show="errorMessage" class="text-xs font-bold text-rose-500 bg-rose-50 dark:bg-rose-955/20 p-3 rounded-xl border border-rose-100 dark:border-rose-900/35" x-cloak>
                <span x-text="errorMessage"></span>
            </div>
        </div>

        <!-- Result Container -->
        <div x-show="record" class="space-y-6" x-cloak>
            
            <!-- Ticket Info Card -->
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <span class="text-[9px] font-mono font-bold text-slate-400 uppercase tracking-widest block">Tracking ID</span>
                        <h2 class="text-base font-black text-slate-800 dark:text-white font-mono uppercase tracking-tight" x-text="record.tracking_id"></h2>
                    </div>
                    <div>
                        <span :class="{
                            'bg-amber-500/10 border-amber-500/20 text-amber-600 dark:text-amber-400': record.status === 'Pending',
                            'bg-blue-500/10 border-blue-500/20 text-blue-600 dark:text-blue-400': record.status === 'In Review',
                            'bg-emerald-500/10 border-emerald-500/20 text-emerald-600 dark:text-emerald-400': record.status === 'Resolved'
                        }" class="inline-flex px-3 py-1 rounded-full border text-[10px] font-black uppercase tracking-wider" x-text="record.status"></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5">Category</span>
                        <p class="text-xs text-slate-800 dark:text-slate-200 font-extrabold" x-text="record.category || 'General Concern'"></p>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5">Submitted Date</span>
                        <p class="text-xs text-slate-800 dark:text-slate-200 font-extrabold" x-text="record.created_at"></p>
                    </div>
                </div>

                <div>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5">Subject</span>
                    <p class="text-xs text-slate-800 dark:text-slate-200 font-extrabold leading-relaxed" x-text="record.subject"></p>
                </div>

                <div>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5">Message Inquiry</span>
                    <p class="text-xs text-slate-600 dark:text-slate-450 leading-relaxed whitespace-pre-line bg-slate-50 dark:bg-slate-950 p-4 rounded-2xl border border-slate-100 dark:border-slate-850" x-text="record.message"></p>
                </div>

                <template x-if="record.attachment">
                    <div class="pt-2">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Attachment</span>
                        <a :href="record.attachment" target="_blank" class="inline-flex items-center gap-2 text-xs font-bold text-[#1e40af] dark:text-blue-400 hover:underline">
                            📎 View uploaded file attachment
                        </a>
                    </div>
                </template>
            </div>

            <!-- Admin Replies Thread Section -->
            <div class="space-y-4">
                <h3 class="text-xs font-black uppercase text-slate-800 dark:text-slate-100 tracking-wider">Administrative Thread (Replies)</h3>
                
                <div class="space-y-3">
                    <template x-if="record.replies.length === 0">
                        <div class="p-6 text-center border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-3xl bg-slate-50/50 dark:bg-slate-900/30 text-xs text-slate-400">
                            No administrative responses or comments have been posted yet. Check back later.
                        </div>
                    </template>

                    <template x-for="rep in record.replies" :key="rep.timestamp">
                        <div class="p-5 border border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-3xl space-y-2.5 shadow-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-black uppercase text-emerald-600 dark:text-emerald-400 font-display" x-text="rep.sender"></span>
                                <span class="text-[9px] font-mono text-slate-400" x-text="rep.timestamp"></span>
                            </div>
                            <p class="text-xs text-slate-655 dark:text-slate-400 leading-relaxed whitespace-pre-line" x-text="rep.message"></p>
                        </div>
                    </template>
                </div>
            </div>

        </div>

    </div>
</div>

<script>
function skonsultaTracker() {
    return {
        searchId: '',
        loading: false,
        errorMessage: '',
        record: null,
        init() {
            // Check if tracking ID is passed in URL query string
            const urlParams = new URLSearchParams(window.location.search);
            const initialId = urlParams.get('tracking_id');
            if (initialId) {
                this.searchId = initialId;
                this.trackRequest();
            }
        },
        async trackRequest() {
            if (!this.searchId) return;
            this.loading = true;
            this.errorMessage = '';
            this.record = null;

            try {
                const response = await fetch('{{ route("consultations.track") }}?tracking_id=' + encodeURIComponent(this.searchId));
                const data = await response.json();

                if (response.ok) {
                    this.record = data;
                } else {
                    this.errorMessage = data.error || 'Tracking details not found. Verify the ID and try again.';
                }
            } catch (error) {
                this.errorMessage = 'Network connection issue occurred.';
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection
