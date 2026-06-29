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
                    <a href="{{ route('admin.news.index') }}" class="text-slate-400 hover:text-[#1e40af] transition duration-150">News Articles</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-850">Edit Article</span>
                </div>
            </div>

            <!-- Page Title -->
            <div class="pb-4 border-b border-slate-100">
                <span class="text-[10px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Authoring Center</span>
                <h1 class="text-2xl font-black tracking-tight text-slate-800 font-display uppercase mt-1">Edit News Article</h1>
                <p class="text-xs text-slate-500 mt-1">Modify article headline, text content, cover photo, or toggle feature section placements.</p>
            </div>

            <!-- Edit Form -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 md:p-8 shadow-sm max-w-4xl">
                <form method="POST" action="{{ route('admin.news.update', $news->id) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Title -->
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-450 uppercase tracking-wider block">Article Title</label>
                            <input 
                                type="text" 
                                name="title" 
                                required 
                                placeholder="Enter article headline..." 
                                value="{{ old('title', $news->title) }}"
                                class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2.5"
                            >
                            @error('title')
                                <span class="text-rose-650 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Cover Image -->
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-455 uppercase tracking-wider block">Cover / Feature Photo (Optional)</label>
                            <x-file-upload name="image" accept="image/*" placeholder="Drag your cover photo here or click to browse." existing-url="{{ $news->image_path ? asset('storage/' . $news->image_path) : null }}" />
                            <span class="text-[9px] text-slate-400 block mt-0.5">Leave blank to keep current cover image. Supported: JPG, PNG, WEBP.</span>
                            @error('image')
                                <span class="text-rose-655 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Category -->
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-450 uppercase tracking-wider block">Category</label>
                            <input 
                                type="text" 
                                name="category" 
                                required 
                                placeholder="e.g. Swimming, Agriculture, Education" 
                                value="{{ old('category', $news->category) }}"
                                class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2.5"
                            >
                            @error('category')
                                <span class="text-rose-650 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Read Time -->
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-450 uppercase tracking-wider block">Estimated Read Time (Minutes)</label>
                            <input 
                                type="number" 
                                name="read_time" 
                                required 
                                min="1"
                                placeholder="e.g. 10" 
                                value="{{ old('read_time', $news->read_time) }}"
                                class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2.5"
                            >
                            @error('read_time')
                                <span class="text-rose-650 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Excerpt -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-450 uppercase tracking-wider block">Short Summary (Excerpt)</label>
                        <textarea 
                            name="excerpt" 
                            required 
                            rows="2" 
                            placeholder="Write a brief teaser description (1-2 sentences) shown on news list cards..." 
                            class="field focus:ring-4 focus:ring-blue-600/10 text-xs"
                        >{{ old('excerpt', $news->excerpt) }}</textarea>
                        @error('excerpt')
                            <span class="text-rose-650 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Content -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-450 uppercase tracking-wider block">Full Article Content</label>
                        <textarea 
                            name="content" 
                            required 
                            rows="12" 
                            placeholder="Write the full story body content here..." 
                            class="field focus:ring-4 focus:ring-blue-600/10 text-xs"
                        >{{ old('content', $news->content) }}</textarea>
                        @error('content')
                            <span class="text-rose-650 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Placement Status Toggles -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                        <div class="flex items-start space-x-3">
                            <input 
                                type="checkbox" 
                                id="is_featured" 
                                name="is_featured" 
                                value="1" 
                                {{ old('is_featured', $news->is_featured) ? 'checked' : '' }}
                                class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500 mt-0.5"
                            >
                            <div>
                                <label for="is_featured" class="text-xs font-bold text-slate-700 uppercase tracking-wider block cursor-pointer select-none">Set as Main Featured Article</label>
                                <span class="text-[10px] text-slate-450 block mt-0.5">Displays as the large, primary visual card on the top left of the homepage. Only one article can be featured at a time.</span>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <input 
                                type="checkbox" 
                                id="is_trending" 
                                name="is_trending" 
                                value="1" 
                                {{ old('is_trending', $news->is_trending) ? 'checked' : '' }}
                                class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500 mt-0.5"
                            >
                            <div>
                                <label for="is_trending" class="text-xs font-bold text-slate-700 uppercase tracking-wider block cursor-pointer select-none">Display in Trending News</label>
                                <span class="text-[10px] text-slate-450 block mt-0.5">Adds this article into the bottom "Trending News" 3-column catalog section of the homepage.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="pt-6 border-t border-slate-100 flex items-center justify-end space-x-2">
                        <a href="{{ route('admin.news.index') }}" class="btn-outline text-xs py-2.5 px-6 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 transition cursor-pointer font-bold">
                            Cancel
                        </a>
                        <button type="submit" class="btn-primary text-xs py-2.5 px-6 bg-[#1e40af] text-white hover:bg-blue-700 font-bold rounded-xl transition shadow-sm cursor-pointer">
                            Update Article
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>

</div>
@endsection
