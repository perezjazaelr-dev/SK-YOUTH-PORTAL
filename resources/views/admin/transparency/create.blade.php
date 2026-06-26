@extends('layouts.app')

@section('content')
<div x-data="{ mobileSidebar: false }" class="flex-1 flex flex-col md:flex-row bg-[#f8fafc]">
    @include('layouts.dashboard-sidebar')
    <div x-show="mobileSidebar" @click="mobileSidebar = false" class="fixed inset-0 bg-slate-900/40 z-20 md:hidden" x-cloak></div>

    <div class="flex-1 flex flex-col min-w-0">
        <div class="p-4 md:p-8 space-y-6 flex-1 overflow-y-auto max-w-3xl">
            <nav class="text-xs font-semibold uppercase tracking-wider pb-4 border-b border-slate-100">
                <a href="{{ route('admin.transparency.index') }}" class="text-slate-400 hover:text-[#1e40af]">← Transparency Board</a>
            </nav>
            <div class="pb-4 border-b border-slate-100">
                <h1 class="text-xl md:text-2xl font-black text-slate-800 font-display uppercase">Post Transparency Document</h1>
                <p class="text-xs text-slate-500 mt-1">Upload a public disclosure for the transparency board.</p>
            </div>
            <form method="POST" action="{{ route('admin.transparency.store') }}" enctype="multipart/form-data" class="card space-y-5">
                @csrf
                @include('admin.transparency._form', ['categories' => $categories])
                <div class="pt-4 border-t border-slate-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                    <a href="{{ route('admin.transparency.index') }}" class="btn-outline text-xs min-h-11 flex items-center justify-center">Cancel</a>
                    <button type="submit" class="btn-primary text-xs min-h-11">Publish Post</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
