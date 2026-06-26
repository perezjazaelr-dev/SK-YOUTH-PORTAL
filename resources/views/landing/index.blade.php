@extends('layouts.app')

@section('content')
<!-- Landing page container using Alpine.js for modal state -->
<div x-data="{
    activeCategory: null,
    showModal: false,
    activeForm: '{{ session('failed_form') ?? request()->query('form') }}',
    isAuthenticated: {{ Auth::check() ? 'true' : 'false' }},
    categoriesData: {
        'education': {
            label: 'EDUCATION',
            subtopics: [
                { name: 'SILID KARUNUNGAN', url: '{{ route('forms.silid.create') }}', active: true },
                { name: 'TRACK REQUEST', url: '{{ route('track.index') }}', active: true },
                { name: 'TIPD', url: '#', active: false },
                { name: 'OTHER PROJECTS', url: '#', active: false }
            ]
        },
        'health': {
            label: 'HEALTH',
            subtopics: [
                { name: 'MENTAL HEALTH SUPPORT', url: '{{ route('forms.mental-health.create') }}', active: true },
                { name: 'HEALTH CONSULTATION', url: '{{ route('forms.health.create') }}', active: true },
                { name: 'SPORTS', url: '{{ route('forms.sports.create') }}', active: true },
                { name: 'PABILI MEDICINE SERVICES', url: '{{ route('forms.medicine.create') }}', active: true },
                { name: 'TRACK REQUEST', url: '{{ route('track.index') }}', active: true }
            ]
        },
        'governance': {
            label: 'GOVERNANCE',
            subtopics: [
                { name: 'TRACK REQUEST', url: '{{ route('track.index') }}', active: true },
                { name: 'SANGGUNIANG KABATAAN ASSEMBLY', url: '#', active: false },
                { name: 'LEGISLATIVE TRACKER', url: '#', active: false }
            ]
        },
        'active-citizenship': {
            label: 'ACTIVE CITIZENSHIP',
            subtopics: [
                { name: 'TRACK REQUEST', url: '{{ route('track.index') }}', active: true },
                { name: 'YOUTH VOLUNTEER CORPS', url: '#', active: false }
            ]
        },
        'social-inclusion': {
            label: 'SOCIAL INCLUSION',
            subtopics: [
                { name: 'TRACK REQUEST', url: '{{ route('track.index') }}', active: true },
                { name: 'ACCESSIBILITY AID', url: '#', active: false }
            ]
        },
        'peace-building': {
            label: 'PEACE BUILDING',
            subtopics: [
                { name: 'TRACK REQUEST', url: '{{ route('track.index') }}', active: true },
                { name: 'CONFLICT RESOLUTION', url: '#', active: false }
            ]
        },
        'environment': {
            label: 'ENVIRONMENT',
            subtopics: [
                { name: 'TRACK REQUEST', url: '{{ route('track.index') }}', active: true },
                { name: 'ECO-WARRIORS REGISTRATION', url: '#', active: false }
            ]
        },
        'youth-employment': {
            label: 'YOUTH EMPLOYMENT & EMPOWERMENT',
            subtopics: [
                { name: 'TRACK REQUEST', url: '{{ route('track.index') }}', active: true },
                { name: 'INTERNSHIP PORTAL', url: '#', active: false },
                { name: 'SK LIKHA WORKSHOPS', url: '#', active: false }
            ]
        },
        'agriculture': {
            label: 'AGRICULTURE',
            subtopics: [
                { name: 'TRACK REQUEST', url: '{{ route('track.index') }}', active: true },
                { name: 'KABATAANG AGRI-PINS', url: '#', active: false }
            ]
        },
        'global-mobility': {
            label: 'GLOBAL MOBILITY',
            subtopics: [
                { name: 'TRACK REQUEST', url: '{{ route('track.index') }}', active: true },
                { name: 'SCHOLARSHIP VERIFICATION', url: '#', active: false }
            ]
        }
    },
    openCategory(key) {
        this.activeCategory = key;
        this.showModal = true;
    },
    openForm(formName) {
        if (!this.isAuthenticated) {
            window.location.href = '{{ route('login') }}';
            return;
        }
        this.activeForm = formName;
    },
    handleCtaClick(url) {
        if (!url || url === '#') return;
        if (url.includes('health-consultation') || url.includes('mental-health') || url.includes('pabili-medicine') || url.includes('silid-karunungan') || url.includes('sports-registration')) {
            if (!this.isAuthenticated) {
                window.location.href = '{{ route('login') }}';
                return;
            }
        }
        if (url.includes('health-consultation')) {
            this.activeForm = 'health';
        } else if (url.includes('mental-health')) {
            this.activeForm = 'mental-health';
        } else if (url.includes('pabili-medicine')) {
            this.activeForm = 'medicine';
        } else if (url.includes('silid-karunungan')) {
            this.activeForm = 'silid';
        } else if (url.includes('sports-registration')) {
            this.activeForm = 'sports';
        } else {
            window.location.href = url;
        }
    }
}" class="space-y-16">

    <!-- 1. Hero Carousel (Alpine.js, 4 slides, autoplay 5s, arrows, dots, Unsplash) -->
    <section x-data="{
        activeSlide: 0,
        slides: {{ json_encode($formattedSlides) }},
        next() { this.activeSlide = (this.activeSlide + 1) % this.slides.length },
        prev() { this.activeSlide = (this.activeSlide - 1 + this.slides.length) % this.slides.length },
        init() { setInterval(() => this.next(), 5000) }
    }" class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
        <div class="relative h-[280px] sm:h-[360px] md:h-[450px] rounded-3xl overflow-hidden shadow-lg border border-slate-100 bg-slate-900 carousel-shadow">
            
            <template x-for="(slide, index) in slides" :key="index">
                <div x-show="activeSlide === index"
                     x-transition:enter="transition ease-out duration-700"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-500"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute inset-0 bg-cover bg-center flex flex-col justify-end text-white p-6 sm:p-12 md:p-16"
                     :style="`background-image: linear-gradient(to top, rgba(15, 23, 42, 0.95), rgba(15, 23, 42, 0.4)), url(${slide.image})`">
                    
                    <div class="relative z-10 max-w-2xl space-y-4">
                        <span class="bg-blue-600/35 border border-blue-400/20 text-blue-200 text-[9px] font-black uppercase tracking-[0.25em] px-3.5 py-1.5 rounded-full backdrop-blur-md">Barangay Namayan SK</span>
                        <h2 class="text-2xl sm:text-4xl md:text-5xl font-extrabold tracking-tight font-display text-white leading-tight" x-text="slide.title"></h2>
                        <p class="text-slate-300 text-xs sm:text-sm md:text-base max-w-xl font-medium leading-relaxed" x-text="slide.desc"></p>
                        <div class="flex flex-wrap gap-3 pt-2">
                            <a :href="slide.url1" @click.prevent="handleCtaClick(slide.url1)" class="btn-primary" x-text="slide.cta1"></a>
                            <a href="{{ route('track.index') }}" class="btn-outline text-white hover:text-[#1e40af] border-white/20 hover:bg-white">
                                Track Request
                            </a>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Slide Nav Arrows -->
            <button @click="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-xl bg-white/10 hover:bg-white/25 border border-white/10 backdrop-blur-md text-white flex items-center justify-center transition active:scale-95 group">
                <svg class="w-5 h-5 group-hover:-translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <button @click="next()" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-xl bg-white/10 hover:bg-white/25 border border-white/10 backdrop-blur-md text-white flex items-center justify-center transition active:scale-95 group">
                <svg class="w-5 h-5 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
            </button>

            <!-- Dots -->
            <div class="absolute bottom-5 left-1/2 -translate-x-1/2 flex space-x-2.5">
                <template x-for="(slide, index) in slides" :key="index">
                    <button @click="activeSlide = index"
                            class="w-2.5 h-2.5 rounded-full transition-all duration-300"
                            :class="activeSlide === index ? 'bg-white w-6 shadow-sm' : 'bg-white/30 hover:bg-white/55'"></button>
                </template>
            </div>
        </div>
    </section>

    <!-- 2. Quick Action Strip (5 inline buttons for the 4 main forms + Track Request) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 flex flex-wrap items-center justify-center gap-3">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider px-3">Quick Forms:</span>
            <a href="{{ route('forms.health.create') }}" @click.prevent="openForm('health')" class="btn-outline btn-sm space-x-1.5 shadow-sm">
                <x-category-icon name="health" class="w-4 h-4 text-emerald-600" />
                <span>Health Consult</span>
            </a>
            <a href="{{ route('forms.mental-health.create') }}" @click.prevent="openForm('mental-health')" class="btn-outline btn-sm space-x-1.5 shadow-sm">
                <x-category-icon name="mental-health" class="w-4 h-4 text-purple-600" />
                <span>Mental Support</span>
            </a>
            <a href="{{ route('forms.medicine.create') }}" @click.prevent="openForm('medicine')" class="btn-outline btn-sm space-x-1.5 shadow-sm">
                <x-category-icon name="medicine" class="w-4 h-4 text-amber-600" />
                <span>Pabili Medicine</span>
            </a>
            <a href="{{ route('forms.silid.create') }}" @click.prevent="openForm('silid')" class="btn-outline btn-sm space-x-1.5 shadow-sm">
                <x-category-icon name="education" class="w-4 h-4 text-indigo-600" />
                <span>Silid Study</span>
            </a>
            <a href="{{ route('forms.sports.create') }}" @click.prevent="openForm('sports')" class="btn-outline btn-sm space-x-1.5 shadow-sm">
                <x-category-icon name="sports" class="w-4 h-4 text-blue-600" />
                <span>Sports League</span>
            </a>
            <a href="{{ route('track.index') }}" class="btn-primary btn-sm space-x-1.5 shadow-sm">
                <x-category-icon name="track" class="w-4 h-4" />
                <span>Track Request</span>
            </a>
        </div>
    </section>

    <!-- 3. Title Projects Grid (10-column layout) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <span class="text-xs font-black tracking-widest text-[#1e40af] uppercase font-display">Interactive Catalog</span>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-800 font-display mt-1.5 uppercase">Our Services</h1>
            <p class="text-xs text-slate-400 mt-2 max-w-md mx-auto">Select a project area below to see subtopics, check schedules, or apply for service assistance in Barangay Namayan.</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach($categories as $key => $cat)
                <a href="{{ route('projects.committee', ['project_slug' => 'sk-namayan-youth-services', 'committee_slug' => $key]) }}"
                   class="h-32 bg-white border border-slate-100 hover:border-[#1e40af] text-slate-700 hover:text-[#1e40af] hover:-translate-y-1 hover:shadow-md transition-all duration-300 rounded-2xl flex flex-col justify-center items-center text-center p-5 group relative overflow-hidden active:scale-95">
                    
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-100 group-hover:scale-105 transition duration-200 mb-3">
                        <x-category-icon name="{{ $key }}" class="w-6 h-6 text-blue-600" />
                    </div>

                    <span class="font-extrabold text-[10px] sm:text-xs tracking-wider uppercase font-display leading-tight">{{ $cat['label'] }}</span>
                </a>
            @endforeach
        </div>
    </section>

    <!-- 5. Featured Programs Cards (3-column grid) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <span class="text-xs font-black tracking-widest text-[#1e40af] uppercase font-display">Featured Initiatives</span>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-800 font-display mt-1.5 uppercase">Highlighted Programs</h1>
            <p class="text-xs text-slate-400 mt-2 max-w-sm mx-auto">Explore the three most requested programs directly managed by our youth representatives.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Health Consultation -->
            <div class="card flex flex-col justify-between h-full hover:-translate-y-1 hover:shadow-md transition">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <x-category-icon name="health" class="w-5 h-5 text-emerald-600" />
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide font-display">Health Consultation</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Access qualified medical and mental wellness services. Book private consultation check-up appointments safely online.
                    </p>
                </div>
                <div class="pt-5 border-t border-slate-100 mt-5">
                    <a href="{{ route('forms.health.create') }}" @click.prevent="openForm('health')" class="btn-primary w-full">Book Consultation</a>
                </div>
            </div>

            <!-- Silid Karunungan -->
            <div class="card flex flex-col justify-between h-full hover:-translate-y-1 hover:shadow-md transition">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <x-category-icon name="education" class="w-5 h-5 text-indigo-600" />
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide font-display">Silid Karunungan</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Reserve dedicated workspace slots in our modern local library. Study in a quiet environment with high-speed internet.
                    </p>
                </div>
                <div class="pt-5 border-t border-slate-100 mt-5">
                    <a href="{{ route('forms.silid.create') }}" @click.prevent="openForm('silid')" class="btn-primary w-full">Book Library Slot</a>
                </div>
            </div>

            <!-- Pabili Medicine -->
            <div class="card flex flex-col justify-between h-full hover:-translate-y-1 hover:shadow-md transition">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                        <x-category-icon name="medicine" class="w-5 h-5 text-amber-600" />
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide font-display">Pabili Medicine</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        SK Pabili Medicine assists youth households with medicine purchases. Submit address details to arrange deliveries.
                    </p>
                </div>
                <div class="pt-5 border-t border-slate-100 mt-5">
                    <a href="{{ route('forms.medicine.create') }}" @click.prevent="openForm('medicine')" class="btn-primary w-full">Apply for Medicine</a>
                </div>
        </div>
    </section>

    <!-- News Section (Featured & Recent) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="text-center mb-8">
            <span class="text-xs font-black tracking-widest text-[#1e40af] uppercase font-display">Namayan Feed</span>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-800 font-display mt-1.5 uppercase">News Articles</h1>
            <p class="text-xs text-slate-400 mt-2 max-w-sm mx-auto">Stay updated with the latest community reports, athletic achievements, and local stories.</p>
        </div>

        <!-- Featured & Recent Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Column: Featured Article (Col span 7) -->
            @if($featuredArticle)
                <div class="lg:col-span-7 flex flex-col group">
                    <a href="{{ route('news.show', $featuredArticle->slug) }}" class="block overflow-hidden rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition duration-300 relative aspect-video bg-slate-50">
                        @if($featuredArticle->image_path)
                            @if(str_starts_with($featuredArticle->image_path, 'http'))
                                <img src="{{ $featuredArticle->image_path }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="{{ $featuredArticle->title }}">
                            @else
                                <img src="{{ asset('storage/' . $featuredArticle->image_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="{{ $featuredArticle->title }}">
                            @endif
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <span class="text-slate-350 text-3xl">📷</span>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent"></div>
                        <div class="absolute bottom-5 left-5 right-5 space-y-2">
                            <span class="bg-blue-600 text-white text-[9px] font-black uppercase tracking-wider px-2.5 py-1 rounded-md">{{ $featuredArticle->category }}</span>
                            <span class="text-white/80 text-[10px] font-bold uppercase tracking-wider ml-2">{{ $featuredArticle->read_time }} Min Read</span>
                        </div>
                    </a>
                    <div class="mt-4 space-y-2">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            {{ $featuredArticle->published_at ? \Carbon\Carbon::parse($featuredArticle->published_at)->format('M d, Y') : $featuredArticle->created_at->format('M d, Y') }}
                        </span>
                        <h2 class="text-lg sm:text-xl font-black text-slate-800 leading-snug tracking-tight font-display hover:text-[#1e40af] transition uppercase">
                            <a href="{{ route('news.show', $featuredArticle->slug) }}">{{ $featuredArticle->title }}</a>
                        </h2>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            {{ $featuredArticle->excerpt }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Right Column: Recent Articles (Col span 5) -->
            <div class="lg:col-span-5 space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-xs font-black tracking-wider text-slate-400 uppercase font-display">Recent Stories</h3>
                </div>
                <div class="space-y-4">
                    @forelse($recentArticles as $recent)
                        <div class="flex items-start space-x-4 group">
                            <a href="{{ route('news.show', $recent->slug) }}" class="w-24 h-16 sm:w-28 sm:h-20 rounded-2xl overflow-hidden border border-slate-100 shrink-0 block relative bg-slate-50">
                                @if($recent->image_path)
                                    @if(str_starts_with($recent->image_path, 'http'))
                                        <img src="{{ $recent->image_path }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="{{ $recent->title }}">
                                    @else
                                        <img src="{{ asset('storage/' . $recent->image_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="{{ $recent->title }}">
                                    @endif
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="text-slate-350 text-lg">📷</span>
                                    </div>
                                @endif
                            </a>
                            <div class="space-y-1">
                                <div class="flex items-center space-x-2">
                                    <span class="text-[9px] font-extrabold text-[#1e40af] uppercase tracking-wider">{{ $recent->category }}</span>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">•</span>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $recent->read_time }} Min Read</span>
                                </div>
                                <h4 class="text-xs font-extrabold text-slate-800 hover:text-[#1e40af] transition font-display uppercase leading-tight line-clamp-2">
                                    <a href="{{ route('news.show', $recent->slug) }}">{{ $recent->title }}</a>
                                </h4>
                                <p class="text-[10px] text-slate-450 leading-relaxed line-clamp-2">
                                    {{ $recent->excerpt }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-400 text-xs py-4">No recent articles found.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <!-- 6. Announcements Accordion (Alpine.js, type-aware colors) -->
    <section class="max-w-3xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-8">
            <span class="text-xs font-black tracking-widest text-[#1e40af] uppercase font-display">Latest Broadcasts</span>
            <h1 class="text-2xl font-black tracking-tight text-slate-800 font-display mt-1.5 uppercase">Announcements</h1>
            <p class="text-xs text-slate-400 mt-2">Check active policy broadcasts, eligibility definitions, and public updates below.</p>
        </div>

        @if($announcements->isEmpty())
            <div class="text-center py-8 text-slate-400 text-xs bg-slate-50 border border-slate-100 rounded-2xl">
                No active announcements at the moment. Check back later!
            </div>
        @else
            <div x-data="{ activeAccordion: 0 }" class="space-y-3">
                @foreach($announcements as $index => $ann)
                    @php
                        // Type color bindings
                        $headerClass = match($ann->type) {
                            'success' => 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border-emerald-100',
                            'warning' => 'bg-amber-50 text-amber-800 hover:bg-amber-100 border-amber-100',
                            'info' => 'bg-blue-50 text-blue-800 hover:bg-blue-100 border-blue-100',
                            default => 'bg-slate-50 text-slate-800 hover:bg-slate-100 border-slate-100'
                        };
                        $bodyClass = match($ann->type) {
                            'success' => 'bg-emerald-50/20 border-emerald-100/50 text-slate-700',
                            'warning' => 'bg-amber-50/20 border-amber-100/50 text-slate-700',
                            'info' => 'bg-blue-50/20 border-blue-100/50 text-slate-700',
                            default => 'bg-slate-50/20 border-slate-100/50 text-slate-700'
                        };
                    @endphp
                    
                    <div class="border rounded-2xl overflow-hidden transition duration-200">
                        <button @click="activeAccordion = (activeAccordion === {{ $index }} ? null : {{ $index }})"
                                class="w-full text-left px-5 py-4 font-bold text-xs tracking-wider uppercase flex items-center justify-between transition {{ $headerClass }}">
                            <span class="font-display">{{ $ann->title }}</span>
                            <svg class="w-4 h-4 transform transition-transform duration-200" 
                                 :class="activeAccordion === {{ $index }} ? 'rotate-180' : ''" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div x-show="activeAccordion === {{ $index }}" 
                             x-collapse 
                             class="p-5 text-xs leading-relaxed border-t {{ $bodyClass }}">
                            <p class="mb-3">{{ $ann->body }}</p>
                            <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider block">Published: {{ $ann->published_at->format('M d, Y h:i A') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <!-- 7. CTA Banner (Full-bleed gradient banner, blobs) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        <div class="relative rounded-3xl bg-gradient-to-r from-blue-700 to-indigo-900 text-white py-12 px-6 sm:p-16 overflow-hidden text-center shadow-lg">
            
            <!-- Decorative gradient blobs -->
            <div class="absolute -top-16 -left-16 w-48 h-48 bg-blue-500/20 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-16 -right-16 w-56 h-56 bg-indigo-500/20 rounded-full blur-2xl"></div>

            <div class="relative z-10 max-w-xl mx-auto space-y-4">
                <h2 class="text-3xl font-black font-display text-white uppercase tracking-tight leading-tight">Kabataan, Kilos na!</h2>
                <p class="text-slate-200 text-xs sm:text-sm max-w-sm mx-auto leading-relaxed">
                    Create an account today to easily keep logs of all your requests and submit applications without repetitive typing.
                </p>
                <div class="flex items-center justify-center gap-3 pt-3">
                    @guest
                        <a href="{{ route('register') }}" class="px-5 py-2.5 bg-white text-blue-900 hover:bg-blue-50 font-bold text-xs uppercase tracking-wider rounded-xl transition active:scale-95 shadow-md">Create Account</a>
                        <a href="{{ route('login') }}" class="px-5 py-2.5 border border-white/20 hover:border-white text-white font-bold text-xs uppercase tracking-wider rounded-xl transition hover:bg-white/5 active:scale-95">Sign In</a>
                    @else
                        @if(Auth::user()->canAccessDashboard())
                            <a href="{{ route('dashboard.index') }}" class="px-5 py-2.5 bg-white text-blue-900 hover:bg-blue-50 font-bold text-xs uppercase tracking-wider rounded-xl transition active:scale-95 shadow-md">View Admin Dashboard</a>
                        @else
                            <a href="{{ route('profile.my-requests') }}" class="px-5 py-2.5 bg-white text-blue-900 hover:bg-blue-50 font-bold text-xs uppercase tracking-wider rounded-xl transition active:scale-95 shadow-md">View My Dashboard</a>
                        @endif
                    @endguest
                </div>
            </div>
        </div>
    </section>

    <!-- 8. Sponsor/Partners logo slider -->
    @if(!$partners->isEmpty())
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="text-center mb-6">
            <span class="text-[9px] font-black tracking-widest text-slate-400 uppercase font-display block">Sponsors & Partners</span>
            <h2 class="text-lg font-black tracking-tight text-slate-500 font-display uppercase mt-1">Our Community Partners</h2>
        </div>

        <div class="relative overflow-hidden w-full bg-slate-50 border border-slate-100 rounded-2xl py-6 px-4">
            <div class="flex items-center space-x-12 animate-marquee whitespace-nowrap min-w-full">
                @foreach($partners as $partner)
                    <div class="inline-block shrink-0 transition-opacity duration-300 hover:opacity-100 opacity-60">
                        @if($partner->website_url)
                            <a href="{{ $partner->website_url }}" target="_blank" title="{{ $partner->name }}" class="block">
                                <img src="{{ asset('storage/' . $partner->logo_path) }}" class="h-12 w-auto max-w-[150px] object-contain" alt="{{ $partner->name }}">
                            </a>
                        @else
                            <img src="{{ asset('storage/' . $partner->logo_path) }}" class="h-12 w-auto max-w-[150px] object-contain" alt="{{ $partner->name }}" title="{{ $partner->name }}">
                        @endif
                    </div>
                @endforeach

                <!-- Duplicate for seamless scroll -->
                @foreach($partners as $partner)
                    <div class="inline-block shrink-0 transition-opacity duration-300 hover:opacity-100 opacity-60">
                        @if($partner->website_url)
                            <a href="{{ $partner->website_url }}" target="_blank" title="{{ $partner->name }}" class="block">
                                <img src="{{ asset('storage/' . $partner->logo_path) }}" class="h-12 w-auto max-w-[150px] object-contain" alt="{{ $partner->name }}">
                            </a>
                        @else
                            <img src="{{ asset('storage/' . $partner->logo_path) }}" class="h-12 w-auto max-w-[150px] object-contain" alt="{{ $partner->name }}" title="{{ $partner->name }}">
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <style>
        @keyframes marquee {
            0% { transform: translateX(0%); }
            100% { transform: translateX(-50%); }
        }
        .animate-marquee {
            display: flex;
            width: max-content;
            animation: marquee 25s linear infinite;
        }
        .animate-marquee:hover {
            animation-play-state: paused;
        }
    </style>
    @endif

    <!-- Overlays / Modals for all forms -->
    <div x-show="activeForm" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-cloak>
        
        <!-- Backdrop shadow -->
        <div class="fixed inset-0 bg-slate-950/45 backdrop-blur-sm transition-opacity"></div>

        @php
            $timeOptions = [];
            for ($h = 8; $h <= 17; $h++) {
                $formattedH = str_pad($h, 2, '0', STR_PAD_LEFT);
                $timeOptions["$formattedH:00"] = "$formattedH:00";
                if ($h < 17) {
                    $timeOptions["$formattedH:30"] = "$formattedH:30";
                }
            }
            $genderOptions = [
                'Male' => 'Male',
                'Female' => 'Female',
                'Prefer not to say' => 'Prefer not to say'
            ];
            $sportOptions = [
                'Basketball' => 'Basketball',
                'Volleyball' => 'Volleyball',
                'Football' => 'Football',
                'Badminton' => 'Badminton',
                'Table Tennis' => 'Table Tennis',
                'Swimming' => 'Swimming',
                'Athletics' => 'Athletics',
                'Boxing' => 'Boxing',
                'Martial Arts' => 'Martial Arts',
                'Esports' => 'Esports',
                'Other' => 'Other'
            ];
        @endphp

        <!-- Modal Wrapper -->
        <div class="flex min-h-screen items-center justify-center p-4">
            
            <!-- Modal Box Container -->
            <div class="max-w-2xl w-full relative z-10 transition-all transform max-h-[90vh] flex flex-col overflow-y-auto"
                 @click.stop>
                
                <!-- 1. HEALTH CONSULTATION FORM -->
                <div x-show="activeForm === 'health'" class="w-full relative">
                    <button type="button" @click="activeForm = null" 
                            class="absolute right-4 top-4 text-white hover:text-slate-200 bg-white/10 hover:bg-white/20 p-2 rounded-full transition z-20 focus:outline-none focus:ring-2 focus:ring-white/50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    <x-form-card 
                        title="Health Consultation" 
                        subtitle="Apply for free medical guidance or health services from SK Namayan representatives." 
                        action="{{ route('forms.health.store') }}"
                    >
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <x-form-input label="First Name" name="first_name" required="true" />
                            <x-form-input label="Last Name" name="last_name" required="true" />
                            <x-form-input label="Middle Name" name="middle_name" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-input label="Age" name="age" type="number" min="0" max="120" required="true" />
                            <x-form-select label="Gender" name="gender" required="true" :options="$genderOptions" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-input label="Email Address" name="email" type="email" required="true" />
                            <x-form-input label="Contact Number" name="contact_number" required="true" placeholder="e.g. 09123456789" />
                        </div>

                        <x-form-input label="Concerns" name="concerns" type="textarea" required="true" placeholder="Detail your symptoms, advice needed, or other medical inquiries..." />

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-input label="Preferred Date" name="preferred_date" type="date" min="{{ date('Y-m-d') }}" required="true" />
                            <x-form-select label="Preferred Time Slot" name="preferred_time" required="true" :options="$timeOptions" />
                        </div>

                        @php $healthInit = $initiatives['forms.health.create'] ?? null; @endphp
                        @if($healthInit && is_array($healthInit->custom_fields) && count($healthInit->custom_fields) > 0)
                            <div class="space-y-4 pt-4 border-t border-slate-100 mt-4">
                                <span class="text-[10px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Additional Information Required</span>
                                <div class="grid grid-cols-1 gap-4">
                                    @foreach($healthInit->custom_fields as $field)
                                        <x-form-input 
                                            label="{{ $field['label'] }}" 
                                            name="custom_fields[{{ $field['name'] }}]" 
                                            type="{{ $field['type'] ?? 'text' }}" 
                                            required="{{ ($field['required'] ?? false) ? 'true' : 'false' }}" 
                                            placeholder="{{ $field['placeholder'] ?? '' }}" 
                                        />
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="pt-4">
                            <button type="submit" class="btn-primary w-full">Submit Health Consultation Request</button>
                        </div>
                    </x-form-card>
                </div>

                <!-- 2. MENTAL HEALTH SUPPORT FORM -->
                <div x-show="activeForm === 'mental-health'" class="w-full relative">
                    <button type="button" @click="activeForm = null" 
                            class="absolute right-4 top-4 text-white hover:text-slate-200 bg-white/10 hover:bg-white/20 p-2 rounded-full transition z-20 focus:outline-none focus:ring-2 focus:ring-white/50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    <x-form-card 
                        title="Mental Health Support Portal" 
                        subtitle="Confidential counseling and mental wellness assistance for SK Namayan youth." 
                        action="{{ route('forms.mental-health.store') }}"
                    >
                        <!-- Confidentiality Banner -->
                        <div class="p-4 bg-blue-50 border-l-4 border-blue-500 rounded-xl text-blue-800 text-xs flex items-start space-x-2.5 mb-2 leading-relaxed">
                            <span class="text-base select-none">🔒</span>
                            <div>
                                <span class="font-bold">Confidentiality Guarantee:</span> All details shared in this request are strictly private and will only be accessible by the designated health support team under professional code.
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <x-form-input label="First Name" name="first_name" required="true" />
                            <x-form-input label="Last Name" name="last_name" required="true" />
                            <x-form-input label="Middle Name" name="middle_name" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-input label="Age" name="age" type="number" min="0" max="120" required="true" />
                            <x-form-select label="Gender" name="gender" required="true" :options="$genderOptions" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-input label="Email Address" name="email" type="email" required="true" />
                            <x-form-input label="Contact Number" name="contact_number" required="true" placeholder="e.g. 09123456789" />
                        </div>

                        <x-form-input label="Describe what you are going through (Your mental wellness concerns)" name="concerns" type="textarea" required="true" placeholder="Please feel free to express your mental health queries or challenges..." />

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-input label="Preferred Date" name="preferred_date" type="date" min="{{ date('Y-m-d') }}" required="true" />
                            <x-form-select label="Preferred Time Slot" name="preferred_time" required="true" :options="$timeOptions" />
                        </div>

                        @php $mentalInit = $initiatives['forms.mental-health.create'] ?? null; @endphp
                        @if($mentalInit && is_array($mentalInit->custom_fields) && count($mentalInit->custom_fields) > 0)
                            <div class="space-y-4 pt-4 border-t border-slate-100 mt-4">
                                <span class="text-[10px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Additional Information Required</span>
                                <div class="grid grid-cols-1 gap-4">
                                    @foreach($mentalInit->custom_fields as $field)
                                        <x-form-input 
                                            label="{{ $field['label'] }}" 
                                            name="custom_fields[{{ $field['name'] }}]" 
                                            type="{{ $field['type'] ?? 'text' }}" 
                                            required="{{ ($field['required'] ?? false) ? 'true' : 'false' }}" 
                                            placeholder="{{ $field['placeholder'] ?? '' }}" 
                                        />
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="pt-4">
                            <button type="submit" class="btn-primary w-full">Submit Confidential Request</button>
                        </div>
                    </x-form-card>
                </div>

                <!-- 3. PABILI MEDICINE SERVICES FORM -->
                <div x-show="activeForm === 'medicine'" class="w-full relative">
                    <button type="button" @click="activeForm = null" 
                            class="absolute right-4 top-4 text-white hover:text-slate-200 bg-white/10 hover:bg-white/20 p-2 rounded-full transition z-20 focus:outline-none focus:ring-2 focus:ring-white/50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    <x-form-card 
                        title="Pabili Medicine Services" 
                        subtitle="Request essential medicine purchasing support and delivery services to your home." 
                        action="{{ route('forms.medicine.store') }}"
                    >
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-input label="Requestor First Name" name="requestor_first_name" required="true" />
                            <x-form-input label="Requestor Last Name" name="requestor_last_name" required="true" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-input label="Requestor Age" name="requestor_age" type="number" min="0" max="120" required="true" />
                            <x-form-select label="Requestor Gender" name="requestor_gender" required="true" :options="$genderOptions" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-input label="Email Address" name="email" type="email" required="true" />
                            <x-form-input label="Contact Number" name="contact_number" required="true" placeholder="e.g. 09123456789" />
                        </div>

                        <x-form-input label="Complete Delivery Address" name="complete_address" type="textarea" required="true" placeholder="Enter house number, street, barangay, and landmark..." />

                        @php $medInit = $initiatives['forms.medicine.create'] ?? null; @endphp
                        @if($medInit && is_array($medInit->custom_fields) && count($medInit->custom_fields) > 0)
                            <div class="space-y-4 pt-4 border-t border-slate-100 mt-4">
                                <span class="text-[10px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Additional Information Required</span>
                                <div class="grid grid-cols-1 gap-4">
                                    @foreach($medInit->custom_fields as $field)
                                        <x-form-input 
                                            label="{{ $field['label'] }}" 
                                            name="custom_fields[{{ $field['name'] }}]" 
                                            type="{{ $field['type'] ?? 'text' }}" 
                                            required="{{ ($field['required'] ?? false) ? 'true' : 'false' }}" 
                                            placeholder="{{ $field['placeholder'] ?? '' }}" 
                                        />
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="pt-4">
                            <button type="submit" class="btn-primary w-full">Submit Medicine Request</button>
                        </div>
                    </x-form-card>
                </div>

                <!-- 4. SILID KARUNUNGAN BOOKING FORM -->
                <div x-show="activeForm === 'silid'" class="w-full relative">
                    <button type="button" @click="activeForm = null" 
                            class="absolute right-4 top-4 text-white hover:text-slate-200 bg-white/10 hover:bg-white/20 p-2 rounded-full transition z-20 focus:outline-none focus:ring-2 focus:ring-white/50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    <x-form-card 
                        title="Silid Karunungan Booking" 
                        subtitle="Book studying slots at local research library facilities with internet access." 
                        action="{{ route('forms.silid.store') }}"
                    >
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <x-form-input label="Requestor First Name" name="requestor_first_name" required="true" />
                            <x-form-input label="Requestor Last Name" name="requestor_last_name" required="true" />
                            <x-form-input label="Requestor Middle Name" name="requestor_middle_name" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <x-form-input label="Requestor Age" name="requestor_age" type="number" min="0" max="120" required="true" />
                            <x-form-input label="Email Address" name="email" type="email" required="true" />
                            <x-form-input label="Contact Number" name="contact_number" required="true" placeholder="e.g. 09123456789" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-input label="Preferred Date" name="preferred_date" type="date" min="{{ date('Y-m-d') }}" required="true" />
                            <x-form-select label="Preferred Time Slot" name="preferred_time" required="true" :options="$timeOptions" />
                        </div>

                        @php $silidInit = $initiatives['forms.silid.create'] ?? null; @endphp
                        @if($silidInit && is_array($silidInit->custom_fields) && count($silidInit->custom_fields) > 0)
                            <div class="space-y-4 pt-4 border-t border-slate-100 mt-4">
                                <span class="text-[10px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Additional Information Required</span>
                                <div class="grid grid-cols-1 gap-4">
                                    @foreach($silidInit->custom_fields as $field)
                                        <x-form-input 
                                            label="{{ $field['label'] }}" 
                                            name="custom_fields[{{ $field['name'] }}]" 
                                            type="{{ $field['type'] ?? 'text' }}" 
                                            required="{{ ($field['required'] ?? false) ? 'true' : 'false' }}" 
                                            placeholder="{{ $field['placeholder'] ?? '' }}" 
                                        />
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="pt-4">
                            <button type="submit" class="btn-primary w-full">Submit Booking Request</button>
                        </div>
                    </x-form-card>
                </div>

                <!-- 5. SPORTS REGISTRATION FORM -->
                <div x-show="activeForm === 'sports'" class="w-full relative">
                    <button type="button" @click="activeForm = null" 
                            class="absolute right-4 top-4 text-white hover:text-slate-200 bg-white/10 hover:bg-white/20 p-2 rounded-full transition z-20 focus:outline-none focus:ring-2 focus:ring-white/50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    <x-form-card 
                        title="Sports Registration" 
                        subtitle="Apply for local leagues, sports activities, and tournament registrations organized by SK Namayan." 
                        action="{{ route('forms.sports.store') }}"
                    >
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <x-form-input label="First Name" name="first_name" required="true" />
                            <x-form-input label="Last Name" name="last_name" required="true" />
                            <x-form-input label="Middle Name" name="middle_name" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-input label="Age (10–30)" name="age" type="number" min="10" max="30" required="true" />
                            <x-form-select label="Gender" name="gender" required="true" :options="$genderOptions" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-input label="Email Address" name="email" type="email" required="true" />
                            <x-form-input label="Contact Number" name="contact_number" required="true" placeholder="e.g. 09123456789" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-select label="Choose Sport" name="sport" required="true" :options="$sportOptions" />
                            <x-form-input label="Team Name (Optional)" name="team_name" placeholder="Leave empty if signing up individually" />
                        </div>

                        <x-form-input label="Preferred Event Date" name="event_date" type="date" min="{{ date('Y-m-d') }}" required="true" />

                        <x-form-input label="Remarks / Queries" name="remarks" type="textarea" placeholder="Add any special requirements, team configurations, or general remarks..." />

                        @php $sportsInit = $initiatives['forms.sports.create'] ?? null; @endphp
                        @if($sportsInit && is_array($sportsInit->custom_fields) && count($sportsInit->custom_fields) > 0)
                            <div class="space-y-4 pt-4 border-t border-slate-100 mt-4">
                                <span class="text-[10px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Additional Information Required</span>
                                <div class="grid grid-cols-1 gap-4">
                                    @foreach($sportsInit->custom_fields as $field)
                                        <x-form-input 
                                            label="{{ $field['label'] }}" 
                                            name="custom_fields[{{ $field['name'] }}]" 
                                            type="{{ $field['type'] ?? 'text' }}" 
                                            required="{{ ($field['required'] ?? false) ? 'true' : 'false' }}" 
                                            placeholder="{{ $field['placeholder'] ?? '' }}" 
                                        />
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="pt-4">
                            <button type="submit" class="btn-primary w-full">Submit Sports Registration</button>
                        </div>
                    </x-form-card>
                </div>

            </div>
        </div>
    </div>

    <!-- Submission Success Confirmation Modal -->
    @if(session('submitted_success'))
    <div x-data="{ showSuccess: true }" 
         x-show="showSuccess" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-cloak>
         
         <!-- Backdrop shadow -->
         <div class="fixed inset-0 bg-slate-950/45 backdrop-blur-sm transition-opacity" @click="showSuccess = false"></div>

         <!-- Modal Wrapper -->
         <div class="flex min-h-screen items-center justify-center p-4">
             
             <!-- Modal Box -->
             <div class="bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-100 max-w-lg w-full relative z-10 p-6 sm:p-8 text-center space-y-6"
                  @click.stop>
                  
                  <!-- Close Button -->
                  <button type="button" @click="showSuccess = false" 
                          class="absolute right-4 top-4 text-slate-400 hover:text-slate-600 p-2 rounded-full transition focus:outline-none">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                  </button>

                  <!-- Animated Success Checkmark -->
                  <div class="relative flex items-center justify-center w-20 h-20 mx-auto">
                      <span class="animate-ping absolute inline-flex h-16 w-16 rounded-full bg-emerald-400 opacity-20"></span>
                      <div class="relative rounded-full w-16 h-16 bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                      </div>
                  </div>

                  <div>
                      <h2 class="text-2xl font-black font-display text-slate-800 uppercase tracking-tight">Request Submitted!</h2>
                      <p class="text-xs text-slate-400 mt-2">Thank you! Your digital request has been successfully filed with the SK council.</p>
                  </div>

                  <!-- Reference card with dashed border -->
                  <div class="p-5 bg-blue-50/50 border-2 border-dashed border-blue-200 rounded-2xl max-w-sm mx-auto text-center space-y-1">
                      <span class="text-[9px] font-black text-blue-500 uppercase tracking-widest block font-display">Reference Number</span>
                      <span class="text-xl font-mono font-black text-[#1e40af] select-all">{{ session('referenceNumber') }}</span>
                      <p class="text-[10px] text-slate-400 pt-1">Copy this code to track your status at any time.</p>
                  </div>

                  <!-- Details summary table -->
                  <div class="card p-0 overflow-hidden text-left border border-slate-100 text-xs">
                      <div class="bg-slate-50 border-b border-slate-100 px-5 py-2.5">
                          <span class="font-bold text-slate-700 font-display uppercase tracking-wider">Submission Summary</span>
                      </div>
                      <table class="w-full">
                          <tbody class="divide-y divide-slate-100 text-slate-600">
                              <tr>
                                  <td class="px-5 py-2.5 font-semibold text-slate-400 w-1/3">Request Type</td>
                                  <td class="px-5 py-2.5 font-bold text-slate-800">{{ session('type') }}</td>
                              </tr>
                              <tr>
                                  <td class="px-5 py-2.5 font-semibold text-slate-400">Requestor Name</td>
                                  <td class="px-5 py-2.5 text-slate-800 font-medium">{{ session('name') }}</td>
                              </tr>
                              <tr>
                                  <td class="px-5 py-2.5 font-semibold text-slate-400">Email Address</td>
                                  <td class="px-5 py-2.5 text-slate-800 font-mono">{{ session('email') }}</td>
                              </tr>
                              <tr>
                                  <td class="px-5 py-2.5 font-semibold text-slate-400">Preferred Details</td>
                                  <td class="px-5 py-2.5 text-slate-800 font-medium">{{ session('detail') }}</td>
                              </tr>
                              <tr>
                                  <td class="px-5 py-2.5 font-semibold text-slate-400">Initial Status</td>
                                  <td class="px-5 py-2.5">
                                      <span class="badge-pending">Pending</span>
                                  </td>
                              </tr>
                              <tr>
                                  <td class="px-5 py-2.5 font-semibold text-slate-400">Date Submitted</td>
                                  <td class="px-5 py-2.5 text-slate-800">{{ session('date') }}</td>
                              </tr>
                          </tbody>
                      </table>
                  </div>

                  <!-- Email note -->
                  <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl text-xs text-emerald-800 max-w-md mx-auto leading-relaxed flex items-start space-x-3 shadow-sm text-left">
                      <span class="text-xl shrink-0">✉️</span>
                      <div>
                          <span class="font-bold block text-emerald-950 text-sm mb-0.5">Confirmation Email Sent!</span>
                          A receipt and confirmation details have been sent to <span class="font-semibold underline text-emerald-950 font-mono">{{ session('email') }}</span>. Please check your inbox (and spam folder) for updates.
                      </div>
                  </div>

                  <!-- Action buttons -->
                  <div class="flex items-center justify-center gap-3 pt-2">
                      <a href="{{ route('track.index') }}?email={{ urlencode(session('email')) }}" class="btn-primary">Track Request</a>
                      <button type="button" @click="showSuccess = false" class="btn-outline">Close</button>
                  </div>

             </div>
         </div>
    </div>
    @endif

</div>
@endsection
