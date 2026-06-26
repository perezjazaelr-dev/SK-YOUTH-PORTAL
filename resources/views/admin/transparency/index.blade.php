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
            <span class="text-xs font-bold uppercase text-slate-800 font-display">Transparency</span>
            <div class="w-11"></div>
        </header>

        <div class="p-4 md:p-8 space-y-6 flex-1 overflow-y-auto">
            <nav class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider pb-4 border-b border-slate-100">
                <a href="{{ route('dashboard.index') }}" class="text-slate-400 hover:text-[#1e40af]">Dashboard</a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-800">Transparency Board</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-100">
                <div class="space-y-1">
                    <span class="text-[10px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Open Governance</span>
                    <h1 class="text-xl md:text-2xl font-black text-slate-800 font-display uppercase">Manage Transparency Posts</h1>
                    <p class="text-xs text-slate-500">Publish budget reports, resolutions, and public disclosures for citizens.</p>
                </div>
                <a href="{{ route('admin.transparency.create') }}" class="btn-primary text-xs shrink-0 min-h-11 inline-flex items-center justify-center">Post Document</a>
            </div>

            <div class="card p-0 overflow-hidden">
                @if($posts->isEmpty())
                    <div class="text-center py-16 px-4">
                        <p class="text-sm font-bold text-slate-700 uppercase">No Transparency Posts</p>
                        <p class="text-xs text-slate-400 mt-1">Publish the first public disclosure document.</p>
                    </div>
                @else
                    <ul class="md:hidden divide-y divide-slate-100">
                        @foreach($posts as $post)
                            <li class="p-4 space-y-2">
                                <div class="flex items-start justify-between gap-2 min-w-0">
                                    <p class="text-sm font-bold text-slate-800 line-clamp-2">{{ $post->title }}</p>
                                    @if($post->is_active)
                                        <span class="shrink-0 text-[9px] font-black uppercase px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-full">Live</span>
                                    @else
                                        <span class="shrink-0 text-[9px] font-black uppercase px-2 py-0.5 bg-slate-100 text-slate-500 rounded-full">Hidden</span>
                                    @endif
                                </div>
                                <p class="text-[10px] text-[#1e40af] font-bold uppercase">{{ $post->categoryLabel() }}</p>
                                <p class="text-[10px] text-slate-400">{{ $post->published_at?->format('M d, Y') ?? 'Draft' }}</p>
                                <div class="flex gap-2 pt-1">
                                    <a href="{{ route('admin.transparency.edit', $post) }}" class="text-[10px] font-bold uppercase text-[#1e40af] px-3 py-2 bg-blue-50 rounded-lg min-h-10 inline-flex items-center">Edit</a>
                                    <form method="POST" action="{{ route('admin.transparency.destroy', $post) }}" onsubmit="return confirm('Delete this post?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-[10px] font-bold uppercase text-rose-700 px-3 py-2 bg-rose-50 rounded-lg min-h-10">Delete</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="p-4 pl-6">Title</th>
                                    <th class="p-4">Category</th>
                                    <th class="p-4">Published</th>
                                    <th class="p-4">File</th>
                                    <th class="p-4">Status</th>
                                    <th class="p-4 pr-6 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($posts as $post)
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="p-4 pl-6 font-bold text-slate-800 max-w-xs truncate">{{ $post->title }}</td>
                                        <td class="p-4 text-slate-600">{{ $post->categoryLabel() }}</td>
                                        <td class="p-4 text-slate-500">{{ $post->published_at?->format('M d, Y') ?? '—' }}</td>
                                        <td class="p-4">{!! $post->file_path ? '✓' : '—' !!}</td>
                                        <td class="p-4">
                                            @if($post->is_active)
                                                <span class="text-[9px] font-black uppercase px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-full">Live</span>
                                            @else
                                                <span class="text-[9px] font-black uppercase px-2 py-0.5 bg-slate-100 text-slate-500 rounded-full">Hidden</span>
                                            @endif
                                        </td>
                                        <td class="p-4 pr-6 text-right space-x-2 whitespace-nowrap">
                                            @if($post->is_active && $post->published_at)
                                                <a href="{{ route('transparency.show', $post->slug) }}" target="_blank" class="text-[10px] font-bold uppercase text-slate-500">View</a>
                                            @endif
                                            <a href="{{ route('admin.transparency.edit', $post) }}" class="text-[10px] font-bold uppercase text-[#1e40af] px-2 py-1 bg-blue-50 rounded-lg">Edit</a>
                                            <form method="POST" action="{{ route('admin.transparency.destroy', $post) }}" class="inline" onsubmit="return confirm('Delete?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-[10px] font-bold uppercase text-rose-700 px-2 py-1 bg-rose-50 rounded-lg">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-slate-100">{{ $posts->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
