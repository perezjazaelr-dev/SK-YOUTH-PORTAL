@extends('layouts.app')

@section('content')
<div class="flex-1 flex flex-col min-h-0 bg-slate-50 dark:bg-slate-950 font-sans">

    <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-[#1e3a8a] text-white shrink-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-[max(1.5rem,env(safe-area-inset-top))] pb-8 md:py-16">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-5 min-w-0">
                <a href="{{ route('landing') }}" class="hover:text-white active:scale-95 shrink-0">Home</a>
                <span aria-hidden="true" class="shrink-0">/</span>
                <span class="text-white truncate" aria-current="page">SK Officials</span>
            </nav>
            <div class="max-w-2xl space-y-2.5">
                <span class="inline-flex px-2.5 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-[9px] font-black uppercase tracking-widest">Community Leadership</span>
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-black font-display uppercase tracking-tight leading-tight">SK Officials</h1>
                <p class="text-sm text-slate-300 leading-relaxed">Meet the elected youth leaders of Barangay Namayan and learn more about their roles and programs.</p>
            </div>
            <a href="{{ route('transparency.index') }}" class="inline-flex items-center min-h-11 mt-6 px-5 bg-white/10 hover:bg-white/20 border border-white/20 font-bold text-xs uppercase tracking-wider rounded-2xl active:scale-95 transition-all">
                View Transparency Board →
            </a>
        </div>
    </section>

    {{-- Profile cards grid --}}
    <section class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-4 md:py-10 pb-[max(1.5rem,env(safe-area-inset-bottom))]">
        @if($officials->isEmpty())
            <div class="flex flex-col items-center text-center py-20 px-6 rounded-3xl bg-white dark:bg-slate-900 border border-dashed border-slate-200 dark:border-slate-700 shadow-sm">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-2xl mb-4" aria-hidden="true">👥</div>
                <h2 class="text-base font-bold text-slate-900 dark:text-slate-100">No Officials Yet</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 max-w-xs leading-relaxed">SK Namayan official profiles will be published here once available.</p>
                <a href="{{ route('landing') }}" class="mt-6 min-h-11 inline-flex items-center px-6 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm font-bold active:scale-95 transition-all">Back to Home</a>
            </div>
        @else
            <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 md:gap-6" role="list" aria-label="SK Officials">
                @foreach($officials as $official)
                    @php
                        $termEnd = $official->term ? trim(preg_replace('/.*[–\-]\s*/u', '', $official->term)) : null;
                    @endphp
                    <li class="h-full">
                        <article class="h-full flex flex-col rounded-[1.75rem] bg-white dark:bg-slate-900 p-4 shadow-[0_2px_16px_rgba(15,23,42,0.06)] dark:shadow-none dark:border dark:border-slate-800 hover:shadow-[0_8px_30px_rgba(15,23,42,0.08)] transition-shadow duration-300">

                            {{-- Photo --}}
                            <a href="{{ route('officials.show', $official->slug) }}" class="block rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-800 aspect-[4/5] mb-4 active:scale-[0.99] transition-transform duration-200" tabindex="-1" aria-hidden="true">
                                @if($official->photoUrl())
                                    <img
                                        src="{{ $official->photoUrl() }}"
                                        alt="{{ $official->name }}"
                                        class="w-full h-full object-cover object-top"
                                        loading="lazy"
                                    >
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center gap-2 bg-gradient-to-br from-slate-100 to-blue-50 dark:from-slate-800 dark:to-slate-900">
                                        <span class="w-20 h-20 rounded-2xl bg-[#1e40af] text-white text-2xl font-black font-display flex items-center justify-center shadow-md">{{ $official->initials() }}</span>
                                    </div>
                                @endif
                            </a>

                            {{-- Name & bio --}}
                            <div class="flex-1 flex flex-col min-w-0 px-0.5">
                                <a href="{{ route('officials.show', $official->slug) }}" class="group min-w-0">
                                    <h2 class="text-lg font-bold text-slate-900 dark:text-white leading-snug truncate group-hover:text-[#1e40af] dark:group-hover:text-blue-400 transition-colors">{{ $official->name }}</h2>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 line-clamp-2 leading-relaxed min-h-[2.5rem]">
                                        @if($official->bio)
                                            {{ Str::limit(strip_tags($official->bio), 90) }}
                                        @else
                                            {{ $official->position }} — Sangguniang Kabataan Namayan.
                                        @endif
                                    </p>
                                </a>

                                {{-- Stats + action row (reference layout) --}}
                                <div class="flex items-center justify-between gap-3 mt-4 pt-1">
                                    <div class="flex items-center gap-4 min-w-0">
                                        {{-- Term end year --}}
                                        @if($termEnd)
                                            <div class="flex items-center gap-1.5 shrink-0" title="Term ends {{ $termEnd }}">
                                                <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                <span class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $termEnd }}</span>
                                            </div>
                                        @endif
                                        {{-- Seat / order --}}
                                        <div class="flex items-center gap-1.5 shrink-0" title="Official rank">
                                            <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $official->sort_order }}</span>
                                        </div>
                                    </div>

                                    <a href="{{ route('officials.show', $official->slug) }}"
                                       class="inline-flex items-center justify-center min-h-11 px-5 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-100 text-sm font-bold whitespace-nowrap active:scale-95 transition-all duration-200 shrink-0"
                                       aria-label="View profile of {{ $official->name }}">
                                        View Profile
                                    </a>
                                </div>

                                {{-- Position tag below row --}}
                                <p class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wide mt-3 truncate">{{ $official->position }}</p>
                            </div>
                        </article>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
</div>
@endsection
