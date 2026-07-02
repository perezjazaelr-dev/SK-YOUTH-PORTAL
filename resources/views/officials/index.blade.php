@extends('layouts.app')

@section('content')
<div class="flex-1 flex flex-col min-h-0 bg-slate-900 text-slate-100 font-sans pb-12">

    <!-- Hero Header Panel -->
    <section class="bg-gradient-to-br from-slate-950 via-slate-900 to-[#0f172a] text-white shrink-0 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-[max(1.5rem,env(safe-area-inset-top))] pb-8 md:py-12">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-4 min-w-0">
                <a href="{{ route('landing') }}" class="hover:text-white active:scale-95 shrink-0">Home</a>
                <span aria-hidden="true" class="shrink-0">/</span>
                <span class="text-white truncate" aria-current="page">SK Officials</span>
            </nav>
            
            <div class="max-w-2xl space-y-2">
                <span class="inline-flex px-2.5 py-1 rounded-full bg-blue-500/10 border border-blue-500/30 text-blue-400 text-[9px] font-black uppercase tracking-widest animate-pulse">
                    Namayan Hero Selection
                </span>
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-black font-display uppercase tracking-tight leading-tight bg-clip-text text-transparent bg-gradient-to-r from-white via-slate-200 to-blue-400">
                    Elected Youth Council
                </h1>
                <p class="text-xs sm:text-sm text-slate-400 leading-relaxed">
                    Click on any council member in the roster below to unlock their profile, view their leadership statistics, and explore their focus areas.
                </p>
            </div>
        </div>
    </section>

    <!-- Interactive Hero Grid -->
    <section class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6 md:py-10">
        @if($officials->isEmpty())
            <div class="flex flex-col items-center text-center py-20 px-6 rounded-3xl bg-slate-950/60 border border-dashed border-slate-800 shadow-xl">
                <div class="w-16 h-16 rounded-2xl bg-slate-900 flex items-center justify-center text-2xl mb-4" aria-hidden="true">👥</div>
                <h2 class="text-base font-bold text-white">Roster Unavailable</h2>
                <p class="text-sm text-slate-400 mt-2 max-w-xs leading-relaxed">The SK Namayan official roster will be updated shortly.</p>
                <a href="{{ route('landing') }}" class="mt-6 min-h-11 inline-flex items-center px-6 rounded-full bg-slate-800 hover:bg-slate-700 text-white text-sm font-bold active:scale-95 transition-all">Back to Home</a>
            </div>
        @else
            <!-- Alpine.js MOBA Dashboard Selection Wrapper -->
            <div x-data="{ activeId: {{ $officials->first()->id }} }" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Main Board View (Col span 8) -->
                <div class="lg:col-span-8 bg-slate-950/40 border border-slate-800/80 rounded-3xl p-6 shadow-2xl relative min-h-[460px] overflow-hidden backdrop-blur-sm">
                    
                    <!-- Decorative HUD Corner Accents -->
                    <div class="absolute top-0 left-0 w-8 h-8 border-t-2 border-l-2 border-blue-500/40 rounded-tl-3xl"></div>
                    <div class="absolute top-0 right-0 w-8 h-8 border-t-2 border-r-2 border-blue-500/40 rounded-tr-3xl"></div>
                    <div class="absolute bottom-0 left-0 w-8 h-8 border-b-2 border-l-2 border-blue-500/40 rounded-bl-3xl"></div>
                    <div class="absolute bottom-0 right-0 w-8 h-8 border-b-2 border-r-2 border-blue-500/40 rounded-br-3xl"></div>

                    @foreach($officials as $official)
                        @php
                            // Deterministic attribute levels for gamified feel
                            $leadership = 80 + (($official->id * 7) % 18);
                            $community = 75 + (($official->id * 11) % 21);
                            $transparency = 78 + (($official->id * 13) % 19);

                            $focusArea = match($official->sort_order % 5) {
                                0 => ['name' => 'Sports & Youth Wellness', 'level' => 90, 'color' => 'bg-rose-500', 'text' => 'text-rose-400', 'badge' => '🏆 Sports'],
                                1 => ['name' => 'Education & Literacy Advocacy', 'level' => 94, 'color' => 'bg-blue-500', 'text' => 'text-blue-400', 'badge' => '📚 Education'],
                                2 => ['name' => 'Health & Sanitation Oversight', 'level' => 88, 'color' => 'bg-emerald-500', 'text' => 'text-emerald-400', 'badge' => '🏥 Health'],
                                3 => ['name' => 'Environmental Stewardship', 'level' => 92, 'color' => 'bg-teal-500', 'text' => 'text-teal-400', 'badge' => '🌱 Environment'],
                                default => ['name' => 'Digital Literacy & Policy', 'level' => 91, 'color' => 'bg-indigo-500', 'text' => 'text-indigo-400', 'badge' => '⚙️ Tech & Policy'],
                            };
                        @endphp
                        
                        <!-- Selected Profile Container -->
                        <div x-show="activeId === {{ $official->id }}"
                             x-transition:enter="transition ease-out duration-300 transform"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
                            
                            <!-- Left: Giant Portrait Pane -->
                            <div class="md:col-span-5 flex flex-col items-center">
                                <div class="relative w-full aspect-[4/5] rounded-2xl overflow-hidden bg-slate-800 border-2 border-slate-700/60 shadow-lg group">
                                    @if($official->photoUrl())
                                        <img src="{{ $official->photoUrl() }}" alt="{{ $official->name }}" class="w-full h-full object-cover object-top">
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-slate-900 to-slate-800">
                                            <span class="w-24 h-24 rounded-3xl bg-blue-600/30 text-blue-400 text-3xl font-black font-display flex items-center justify-center border border-blue-500/20 shadow-inner">
                                                {{ $official->initials() }}
                                            </span>
                                        </div>
                                    @endif
                                    <!-- MOBA Rank Overlay badge -->
                                    <div class="absolute top-3 left-3 bg-slate-950/80 border border-slate-800 px-3 py-1 rounded-full text-[9px] font-black tracking-widest text-blue-400 shadow-md">
                                        LEVEL {{ 90 + $official->sort_order }}
                                    </div>
                                </div>

                                <!-- Contact Details Card -->
                                <div class="w-full mt-4 bg-slate-900/60 rounded-2xl border border-slate-800/80 p-3.5 space-y-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-slate-500 shrink-0">📧</span>
                                        <span class="text-[10px] text-slate-350 truncate font-mono" title="{{ $official->email ?? 'Not available' }}">
                                            {{ $official->email ?? 'no-email@namayan.gov' }}
                                        </span>
                                    </div>
                                    @if($official->contact_number)
                                        <div class="flex items-center gap-2">
                                            <span class="text-slate-500 shrink-0">📞</span>
                                            <span class="text-[10px] text-slate-350 font-mono">{{ $official->contact_number }}</span>
                                        </div>
                                    @endif
                                    <div class="flex items-center gap-2">
                                        <span class="text-slate-500 shrink-0">📅</span>
                                        <span class="text-[10px] text-slate-350 font-mono">Term: {{ $official->term ?? 'Active Year' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Statistics & Focus Attributes -->
                            <div class="md:col-span-7 space-y-6">
                                <div>
                                    <!-- Focus class badge -->
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full border {{ $focusArea['color'] }}/10 border-{{ $focusArea['color'] }}/20 text-xs font-bold uppercase tracking-wider {{ $focusArea['text'] }} mb-2">
                                        {{ $focusArea['badge'] }}
                                    </span>
                                    
                                    <h2 class="text-2xl font-black font-display uppercase tracking-tight text-white leading-tight">
                                        {{ $official->name }}
                                    </h2>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-400 block mt-1">
                                        {{ $official->position }}
                                    </span>
                                </div>

                                <!-- Attribute Progress Bars (MOBA-style) -->
                                <div class="space-y-4">
                                    <h3 class="text-[10px] font-black uppercase text-slate-400 tracking-widest border-b border-slate-800 pb-2">
                                        Leadership Statistics
                                    </h3>

                                    <!-- Stat 1: Leadership -->
                                    <div class="space-y-1.5">
                                        <div class="flex justify-between text-[10px] font-bold uppercase tracking-wide">
                                            <span class="text-slate-300">👑 Leadership Presence</span>
                                            <span class="text-blue-400 font-mono">{{ $leadership }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-900 rounded-full h-2.5 overflow-hidden border border-slate-800/80">
                                            <div class="bg-gradient-to-r from-blue-600 to-blue-400 h-full rounded-full transition-all duration-1000" 
                                                 :style="'width: ' + (activeId === {{ $official->id }} ? '{{ $leadership }}%' : '0%')"></div>
                                        </div>
                                    </div>

                                    <!-- Stat 2: Community Impact -->
                                    <div class="space-y-1.5">
                                        <div class="flex justify-between text-[10px] font-bold uppercase tracking-wide">
                                            <span class="text-slate-300">👥 Community Engagement</span>
                                            <span class="text-blue-400 font-mono">{{ $community }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-900 rounded-full h-2.5 overflow-hidden border border-slate-800/80">
                                            <div class="bg-gradient-to-r from-purple-600 to-purple-400 h-full rounded-full transition-all duration-1000" 
                                                 :style="'width: ' + (activeId === {{ $official->id }} ? '{{ $community }}%' : '0%')"></div>
                                        </div>
                                    </div>

                                    <!-- Stat 3: Transparency -->
                                    <div class="space-y-1.5">
                                        <div class="flex justify-between text-[10px] font-bold uppercase tracking-wide">
                                            <span class="text-slate-300">💎 Governance Transparency</span>
                                            <span class="text-blue-400 font-mono">{{ $transparency }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-900 rounded-full h-2.5 overflow-hidden border border-slate-800/80">
                                            <div class="bg-gradient-to-r from-indigo-600 to-indigo-400 h-full rounded-full transition-all duration-1000" 
                                                 :style="'width: ' + (activeId === {{ $official->id }} ? '{{ $transparency }}%' : '0%')"></div>
                                        </div>
                                    </div>

                                    <!-- Stat 4: Specialty Focus Area -->
                                    <div class="space-y-1.5">
                                        <div class="flex justify-between text-[10px] font-bold uppercase tracking-wide">
                                            <span class="text-slate-300">⚡ Specialty: {{ $focusArea['name'] }}</span>
                                            <span class="{{ $focusArea['text'] }} font-mono font-black">{{ $focusArea['level'] }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-900 rounded-full h-2.5 overflow-hidden border border-slate-800/80">
                                            <div class="h-full rounded-full transition-all duration-1000 {{ $focusArea['color'] }}" 
                                                 :style="'width: ' + (activeId === {{ $official->id }} ? '{{ $focusArea['level'] }}%' : '0%')"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bio Details -->
                                <div class="space-y-2 pt-2">
                                    <h3 class="text-[10px] font-black uppercase text-slate-400 tracking-widest border-b border-slate-800 pb-2">
                                        Leader Biography
                                    </h3>
                                    <p class="text-xs text-slate-350 leading-relaxed font-sans mt-2">
                                        @if($official->bio)
                                            {{ strip_tags($official->bio) }}
                                        @else
                                            {{ $official->name }} serves as the {{ $official->position }} for Barangay Namayan's youth council. Committed to organizing key initiatives, supporting local guidelines, and driving civic participation.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Right/Bottom: Hero Selection Roster Frame (Col span 4) -->
                <div class="lg:col-span-4 bg-slate-950/20 border border-slate-800 rounded-3xl p-5 space-y-4 backdrop-blur-sm">
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Council Roster</h3>
                        <p class="text-[10px] text-slate-500 mt-1">Select portrait to inspect leader status</p>
                    </div>

                    <!-- Interactive Grid of Round Portrait Buttons -->
                    <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-3 gap-3.5">
                        @foreach($officials as $official)
                            <button @click="activeId = {{ $official->id }}" 
                                    :class="activeId === {{ $official->id }} ? 'border-blue-500 ring-2 ring-blue-500/50 scale-105 shadow-[0_0_15px_rgba(59,130,246,0.3)] bg-slate-900' : 'border-slate-800 opacity-60 hover:opacity-100 hover:scale-102 hover:border-slate-700 bg-slate-950'"
                                    class="relative aspect-square rounded-full overflow-hidden border-2 transition-all duration-200 p-0.5 focus:outline-none">
                                
                                <div class="w-full h-full rounded-full overflow-hidden bg-slate-800 relative">
                                    @if($official->photoUrl())
                                        <img src="{{ $official->photoUrl() }}" alt="{{ $official->name }}" class="w-full h-full object-cover object-top">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-slate-900 font-display font-black text-xs text-blue-400">
                                            {{ $official->initials() }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Glow Indicator on Active -->
                                <div x-show="activeId === {{ $official->id }}" class="absolute inset-0 bg-blue-500/10 rounded-full pointer-events-none" x-cloak></div>
                            </button>
                        @endforeach
                    </div>
                </div>

            </div>
        @endif
    </section>
</div>
@endsection
