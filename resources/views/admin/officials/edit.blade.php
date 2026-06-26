@extends('layouts.app')

@section('content')
<div x-data="{ mobileSidebar: false }" class="flex-1 flex flex-col md:flex-row bg-[#f8fafc]">
    @include('layouts.dashboard-sidebar')
    <div x-show="mobileSidebar" @click="mobileSidebar = false" class="fixed inset-0 bg-slate-900/40 z-20 md:hidden" x-cloak></div>

    <div class="flex-1 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-100 h-16 px-4 flex items-center justify-between md:hidden shrink-0">
            <button @click="mobileSidebar = true" type="button" class="inline-flex items-center justify-center min-w-11 min-h-11 p-2 text-slate-500" aria-label="Open menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
            <span class="text-xs font-bold uppercase text-slate-800 font-display truncate px-2">Edit Official</span>
            <div class="w-11"></div>
        </header>

        <div class="p-4 md:p-8 space-y-6 flex-1 overflow-y-auto max-w-3xl">
            <nav class="text-xs font-semibold uppercase tracking-wider pb-4 border-b border-slate-100">
                <a href="{{ route('admin.officials.index') }}" class="text-slate-400 hover:text-[#1e40af]">← Officials</a>
            </nav>

            <div class="pb-4 border-b border-slate-100">
                <h1 class="text-xl md:text-2xl font-black text-slate-800 font-display uppercase">Edit Official Profile</h1>
                <p class="text-xs text-slate-500 mt-1">{{ $official->name }}</p>
            </div>

            <form method="POST" action="{{ route('admin.officials.update', $official) }}" enctype="multipart/form-data" class="card space-y-5">
                @csrf @method('PUT')
                @include('admin.officials._form', ['official' => $official])
                <div class="pt-4 border-t border-slate-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                    <a href="{{ route('admin.officials.index') }}" class="btn-outline text-xs min-h-11 flex items-center justify-center">Cancel</a>
                    <button type="submit" class="btn-primary text-xs min-h-11">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
