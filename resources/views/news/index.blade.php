@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16 flex-1 font-sans space-y-16">
    
    <!-- Breadcrumbs -->
    <div class="flex items-center space-x-2 text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400 pb-4 border-b border-slate-100">
        <a href="/" class="hover:text-[#1e40af] transition duration-150">Home</a>
        <span class="text-slate-350 select-none">/</span>
        <span class="text-slate-800">News Articles</span>
    </div>

    <!-- Trending News Grid Section -->
    @if(!$trendingArticles->isEmpty())
        <div class="space-y-6 pt-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-2">
                    <span class="text-lg font-black tracking-tight text-slate-800 font-display uppercase">Trending News</span>
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($trendingArticles as $trending)
                    <div class="card p-4 hover:-translate-y-1 hover:shadow-md transition duration-300 flex flex-col justify-between group">
                        <div class="space-y-3">
                            <a href="{{ route('news.show', $trending->slug) }}" class="block overflow-hidden rounded-2xl aspect-video border border-slate-50 relative bg-slate-50">
                                @if($trending->image_path)
                                    @if(str_starts_with($trending->image_path, 'http'))
                                        <img src="{{ $trending->image_path }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="{{ $trending->title }}">
                                    @else
                                        <img src="{{ asset('storage/' . $trending->image_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="{{ $trending->title }}">
                                    @endif
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="text-slate-350 text-xl">📷</span>
                                    </div>
                                @endif
                                <div class="absolute bottom-2 left-2">
                                    <span class="bg-slate-900/65 backdrop-blur-sm border border-white/10 text-white text-[8px] font-black uppercase tracking-wider px-2 py-0.5 rounded-md">{{ $trending->category }}</span>
                                </div>
                            </a>
                            <div class="flex items-center justify-between text-[9px] font-bold text-slate-400 uppercase tracking-widest">
                                <span>{{ $trending->published_at ? \Carbon\Carbon::parse($trending->published_at)->format('M d, Y') : $trending->created_at->format('M d, Y') }}</span>
                                <span>{{ $trending->read_time }} Min Read</span>
                            </div>
                            <h3 class="text-xs font-black text-slate-800 hover:text-[#1e40af] transition font-display uppercase leading-snug">
                                <a href="{{ route('news.show', $trending->slug) }}">{{ $trending->title }}</a>
                            </h3>
                            <p class="text-[10px] text-slate-500 leading-relaxed line-clamp-3">
                                {{ $trending->excerpt }}
                            </p>
                        </div>
                        <div class="pt-3 border-t border-slate-100 mt-4 flex items-center justify-between">
                            <a href="{{ route('news.show', $trending->slug) }}" class="text-[#1e40af] hover:text-blue-700 text-[10px] font-bold uppercase tracking-wider transition flex items-center space-x-1">
                                <span>Read Full Story</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3 group-hover:translate-x-0.5 transition duration-200">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
