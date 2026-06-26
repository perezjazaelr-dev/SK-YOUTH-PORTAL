@extends('layouts.app')

@section('content')
<div x-data="{ mobileSidebar: false }" class="flex-1 flex flex-col md:flex-row bg-[#f8fafc]">

    <!-- Left Sidebar -->
    @include('layouts.dashboard-sidebar')

    <div x-show="mobileSidebar" @click="mobileSidebar = false" class="fixed inset-0 bg-slate-900/40 z-20 md:hidden" x-cloak></div>

    <!-- Main Content Pane -->
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

        <div class="p-6 md:p-8 space-y-6 flex-1 overflow-y-auto font-sans">
            
            <!-- Breadcrumbs -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider">
                    <a href="{{ route('dashboard.index') }}" class="text-slate-400 hover:text-[#1e40af] transition duration-150">Dashboard</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-800">News Articles</span>
                </div>
            </div>

            <!-- Page Title and Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-100">
                <div class="space-y-1">
                    <span class="text-[10px] font-black text-[#1e40af] uppercase tracking-widest block font-display">News & Updates</span>
                    <h1 class="text-2xl font-black tracking-tight text-slate-800 font-display uppercase mt-1">Manage News Articles</h1>
                    <p class="text-xs text-slate-500 mt-1">Upload, edit, and categorize announcements or articles displayed on the homepage.</p>
                </div>
                <a href="{{ route('admin.news.create') }}" class="btn-primary text-xs shrink-0 flex items-center space-x-2 bg-[#1e40af] text-white hover:bg-blue-700 py-2.5 px-4 font-bold rounded-xl transition shadow-sm">
                    <span>Create News Article</span>
                </a>
            </div>

            <!-- Articles List -->
            <div class="card p-0 overflow-hidden bg-white border border-slate-100 rounded-3xl shadow-sm">
                @if($articles->isEmpty())
                    <div class="text-center py-16 px-4 space-y-4">
                        <div class="w-16 h-16 bg-slate-50 text-slate-400 border border-slate-100 rounded-3xl flex items-center justify-center mx-auto text-2xl">📰</div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">No Articles Published</h3>
                            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Click "Create News Article" above to publish your first update on the citizen website.</p>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider font-display">
                                    <th class="p-4 pl-6">Preview</th>
                                    <th class="p-4">Title</th>
                                    <th class="p-4">Category</th>
                                    <th class="p-4">Read Time</th>
                                    <th class="p-4 text-center">Status Badges</th>
                                    <th class="p-4 pr-6 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-600">
                                @foreach($articles as $article)
                                    <tr class="hover:bg-slate-50/50 transition duration-150">
                                        <!-- Image Preview -->
                                        <td class="p-4 pl-6">
                                            <div class="w-20 h-12 bg-slate-50 rounded-xl border border-slate-100 overflow-hidden flex items-center justify-center">
                                                @if($article->image_path)
                                                    <img src="{{ asset('storage/' . $article->image_path) }}" class="w-full h-full object-cover" alt="Article thumbnail">
                                                @else
                                                    <span class="text-slate-350 text-lg">📷</span>
                                                @endif
                                            </div>
                                        </td>
                                        <!-- Title -->
                                        <td class="p-4 font-bold text-slate-800 max-w-xs truncate" title="{{ $article->title }}">
                                            <a href="{{ route('news.show', $article->slug) }}" target="_blank" class="hover:text-[#1e40af] transition">{{ $article->title }}</a>
                                        </td>
                                        <!-- Category -->
                                        <td class="p-4 font-semibold text-slate-700 capitalize">
                                            {{ $article->category }}
                                        </td>
                                        <!-- Read Time -->
                                        <td class="p-4 font-medium text-slate-500">
                                            {{ $article->read_time }} Min
                                        </td>
                                        <!-- Featured/Trending Status -->
                                        <td class="p-4 text-center space-x-1 whitespace-nowrap">
                                            @if($article->is_featured)
                                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-full text-[9px] font-black uppercase tracking-wider">Featured</span>
                                            @endif
                                            @if($article->is_trending)
                                                <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded-full text-[9px] font-black uppercase tracking-wider">Trending</span>
                                            @endif
                                            @if(!$article->is_featured && !$article->is_trending)
                                                <span class="px-2 py-0.5 bg-slate-100 text-slate-400 rounded-full text-[9px] font-black uppercase tracking-wider">Standard</span>
                                            @endif
                                        </td>
                                        <!-- Actions -->
                                        <td class="p-4 pr-6 text-right whitespace-nowrap space-x-2">
                                            <a href="{{ route('admin.news.edit', $article->id) }}" class="inline-flex items-center px-2.5 py-1.5 bg-blue-50 text-[#1e40af] hover:bg-blue-100 font-bold rounded-lg transition text-[10px] uppercase tracking-wider active:scale-95 border border-transparent">
                                                Edit
                                            </a>
                                            
                                            <x-alert-dialog>
                                                <x-slot:trigger>
                                                    <button type="button" class="inline-flex items-center px-2.5 py-1.5 bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold rounded-lg transition text-[10px] uppercase tracking-wider active:scale-95 border border-transparent">
                                                        Delete
                                                    </button>
                                                </x-slot:trigger>
                                                
                                                <x-slot:icon>
                                                    <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                    </svg>
                                                </x-slot:icon>
                                                
                                                <x-slot:title>
                                                    Delete News Article
                                                </x-slot:title>
                                                
                                                <x-slot:description>
                                                    Are you sure you want to permanently delete "{{ $article->title }}"? This will remove it from the homepage. This action cannot be undone.
                                                </x-slot:description>
                                                
                                                <x-slot:footer>
                                                    <button type="button" @click="open = false" class="btn-outline text-xs py-2 px-4 border border-slate-250 hover:bg-slate-50 transition rounded-xl font-bold">
                                                        Cancel
                                                    </button>
                                                    <form method="POST" action="{{ route('admin.news.destroy', $article->id) }}" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-bold py-2 px-4 rounded-xl text-xs transition active:scale-95 shadow-sm hover:shadow-md border border-transparent">
                                                            Confirm Delete
                                                        </button>
                                                    </form>
                                                </x-slot:footer>
                                            </x-alert-dialog>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $articles->links() }}
                    </div>
                @endif
            </div>

        </div>

    </div>

</div>
@endsection
