@extends('layouts.app')

@section('content')
<div class="flex-1 bg-[#f8fafc] dark:bg-slate-950 font-sans min-h-screen py-12" x-data="skonsultaForm()">
    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
            <a href="{{ route('landing') }}" class="hover:text-[#1e40af] dark:hover:text-blue-400">Home</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-800 dark:text-slate-100">SKONSULTA Portal</span>
        </div>

        <!-- Page Header -->
        <div class="space-y-2">
            <span class="inline-flex px-2.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase tracking-widest">
                Anonymous Inquiries
            </span>
            <h1 class="text-3xl font-black text-slate-800 dark:text-white font-display uppercase tracking-tight leading-tight">
                SKONSULTA Anonymous Platform
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                Submit concerns, suggestions, or reports anonymously to the Sangguniang Kabataan. You will receive a tracking ID to check the status of your inquiries.
            </p>
        </div>

        <!-- Form Wrapper -->
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
            <form @submit.prevent="submitForm" class="space-y-5" enctype="multipart/form-data">
                
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5">Category <span class="text-rose-500">*</span></label>
                    <select x-model="form.category" required class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/5 transition cursor-pointer">
                        <option value="">Select Category</option>
                        <option value="General Concern">General Concern</option>
                        <option value="Suggestion">Suggestion</option>
                        <option value="Report">Report / Complaint</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5">Subject <span class="text-rose-500">*</span></label>
                    <input type="text" x-model="form.subject" required placeholder="Briefly state your concern..." class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/5 transition">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5">Detailed Message <span class="text-rose-500">*</span></label>
                    <textarea x-model="form.message" required placeholder="Type your message in detail here..." rows="5" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/5 transition resize-none"></textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5">Attachment (Optional)</label>
                    <input type="file" @change="handleFile" class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-emerald-50 file:text-emerald-700 dark:file:bg-slate-800 dark:file:text-emerald-350 hover:file:bg-emerald-100 dark:hover:file:bg-slate-750 transition cursor-pointer">
                    <p class="text-[9px] text-slate-400 mt-1.5">Allowed formats: JPG, PNG, PDF, DOCX (Max 10MB)</p>
                </div>

                <div x-show="errorMessage" class="p-3.5 bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/35 rounded-xl text-rose-600 dark:text-rose-400 text-xs font-semibold" x-cloak>
                    <span x-text="errorMessage"></span>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('skonsulta.track') }}" class="text-xs font-bold text-slate-450 hover:text-slate-655 transition">Track Existing Consultation</a>
                    <button type="submit" :disabled="loading" class="inline-flex items-center px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-black uppercase tracking-wider transition active:scale-95 shadow-sm disabled:opacity-50">
                        <span x-text="loading ? 'Submitting...' : 'Submit Anonymously'"></span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Success Modal (generated SKO- tracking ID and copying mechanism) -->
        <div x-show="showSuccessModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" x-cloak>
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-xl max-w-sm w-full space-y-4 text-center transform scale-100 transition">
                <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-950/45 text-emerald-600 dark:text-emerald-300 flex items-center justify-center text-xl mx-auto">✓</div>
                <div class="space-y-1">
                    <h3 class="text-base font-black text-slate-800 dark:text-white uppercase font-display tracking-tight">Submission Completed</h3>
                    <p class="text-[11px] text-slate-400">Save your anonymous tracking ID below to check for administrative replies.</p>
                </div>

                <!-- Tracking ID Card -->
                <div class="p-3 bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 rounded-2xl flex items-center justify-between font-mono text-sm font-bold text-slate-800 dark:text-white">
                    <span x-text="trackingId" id="skoTrackingId"></span>
                    <button @click="copyTrackingId" class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline select-none font-sans font-bold">
                        <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                    </button>
                </div>

                <div class="pt-2">
                    <button @click="closeSuccessModal" class="w-full py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold uppercase transition">
                        Done
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function skonsultaForm() {
    return {
        form: {
            category: '',
            subject: '',
            message: '',
        },
        attachment: null,
        loading: false,
        errorMessage: '',
        showSuccessModal: false,
        trackingId: '',
        copied: false,
        handleFile(e) {
            this.attachment = e.target.files[0];
        },
        async submitForm() {
            this.loading = true;
            this.errorMessage = '';
            
            const formData = new FormData();
            formData.append('category', this.form.category);
            formData.append('subject', this.form.subject);
            formData.append('message', this.form.message);
            if (this.attachment) {
                formData.append('attachment', this.attachment);
            }

            try {
                const response = await fetch('{{ route("consultations.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    this.trackingId = data.tracking_id;
                    this.showSuccessModal = true;
                    // Reset Form
                    this.form.category = '';
                    this.form.subject = '';
                    this.form.message = '';
                    this.attachment = null;
                } else {
                    this.errorMessage = data.message || 'Validation errors occurred. Please try again.';
                }
            } catch (error) {
                this.errorMessage = 'A network error occurred. Please try again.';
            } finally {
                this.loading = false;
            }
        },
        copyTrackingId() {
            navigator.clipboard.writeText(this.trackingId);
            this.copied = true;
            setTimeout(() => this.copied = false, 2000);
        },
        closeSuccessModal() {
            this.showSuccessModal = false;
            window.location.href = '{{ route("skonsulta.track") }}?tracking_id=' + this.trackingId;
        }
    }
}
</script>
@endsection
