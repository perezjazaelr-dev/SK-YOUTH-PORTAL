<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SK Namayan') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="bg-[#fefefe] text-slate-900 font-sans antialiased flex flex-col min-h-screen">

        <!-- Sticky Header Navbar -->
        @php
            $hidePublicMobileMenu = request()->routeIs('dashboard.*', 'admin.*')
                || (auth()->check() && auth()->user()->canAccessDashboard() && request()->routeIs('profile.edit'));
        @endphp
        <nav x-data="{ mobileMenuOpen: false }" class="bg-[#1e40af] text-white sticky top-0 z-40 shadow-lg border-b border-blue-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between relative">
                
                <!-- Left: Burger & Branding with Logo -->
                <div class="flex items-center space-x-3">
                    <!-- Burger Icon (Mobile only — public/end-user pages) -->
                    @unless($hidePublicMobileMenu)
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="md:hidden text-blue-100 hover:text-white focus:outline-none focus:ring-2 focus:ring-white/50 p-1.5 rounded-xl hover:bg-white/10 transition" aria-label="Toggle menu">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    @endunless

                    <!-- Branding -->
                    <a href="/" class="flex items-center space-x-2.5 group">
                        <img src="{{ asset('images/logo.png') }}" class="w-10 h-10 object-contain rounded-full bg-white p-0.5 border border-blue-200 shadow-sm transition group-hover:scale-105" alt="SK Namayan Logo">
                        <span class="text-sm font-extrabold tracking-wider text-white uppercase font-display">SK Namayan</span>
                    </a>
                    
                    <!-- Desktop Link -->
                    <a href="{{ route('news.index') }}" class="hidden md:inline-flex text-xs sm:text-sm font-bold text-blue-100 hover:text-white transition pl-4 border-l border-blue-400/40 font-display uppercase tracking-wider">
                        News
                    </a>
                </div>


                <!-- Right: Auth buttons -->
                <div class="flex items-center space-x-2 sm:space-x-3 text-sm shrink-0">
                    @if (Route::has('login'))
                        @auth
                            <div class="hidden md:flex items-center space-x-3">
                                @if(!Auth::user()->canAccessDashboard())
                                    <a href="{{ route('profile.my-requests') }}" class="text-blue-100 hover:text-white transition font-medium">My Requests</a>
                                @endif

                                @if(Auth::user()->canAccessDashboard())
                                    <a href="{{ route('dashboard.index') }}" class="bg-white/10 hover:bg-white/20 text-white font-semibold py-1.5 px-4 rounded-xl transition border border-white/10">
                                        Dashboard
                                    </a>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center min-w-11 min-h-11 md:min-w-0 md:min-h-0 bg-white text-[#1e40af] hover:bg-blue-50 font-bold py-1.5 px-3 md:px-4 rounded-xl transition border border-transparent shadow-sm text-xs uppercase tracking-wider"
                                    aria-label="Logout"
                                >
                                    <svg class="w-5 h-5 md:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    <span class="hidden md:inline">Logout</span>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center min-h-11 border border-white/20 hover:border-white text-white font-semibold py-1.5 px-3 md:px-4 rounded-xl transition hover:bg-white/5 text-xs">
                                Login
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="hidden sm:inline-flex bg-white text-[#1e40af] hover:bg-blue-50 font-bold py-1.5 px-4 rounded-xl transition border border-transparent shadow-sm">
                                    Register
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>

            </div>

            @unless($hidePublicMobileMenu)
            <!-- Mobile Menu Left-Side Drawer Overlay & Panel (Mobile only) -->
            <div x-show="mobileMenuOpen" 
                 class="fixed inset-0 z-50 md:hidden" 
                 x-cloak>
                 
                 <!-- Backdrop backdrop-blur-sm -->
                 <div x-show="mobileMenuOpen"
                      x-transition:enter="transition-opacity ease-out duration-300"
                      x-transition:enter-start="opacity-0"
                      x-transition:enter-end="opacity-100"
                      x-transition:leave="transition-opacity ease-in duration-200"
                      x-transition:leave-start="opacity-100"
                      x-transition:leave-end="opacity-0"
                      @click="mobileMenuOpen = false"
                      class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm"></div>

                 <!-- Drawer Panel (Slides from left, full height, at least 70% width) -->
                 <div x-show="mobileMenuOpen"
                      x-transition:enter="transition ease-out duration-300 transform"
                      x-transition:enter-start="-translate-x-full"
                      x-transition:enter-end="translate-x-0"
                      x-transition:leave="transition ease-in duration-200 transform"
                      x-transition:leave-start="translate-x-0"
                      x-transition:leave-end="-translate-x-full"
                      class="fixed inset-y-0 left-0 w-[75%] max-w-[320px] bg-[#1e40af] text-white shadow-2xl flex flex-col justify-between z-50 border-r border-blue-800">
                      
                      <!-- Header inside Drawer -->
                      <div class="px-5 py-4 flex items-center justify-between border-b border-blue-800">
                          <div class="flex items-center space-x-2">
                              <img src="{{ asset('images/logo.png') }}" class="w-8 h-8 object-contain rounded-full bg-white p-0.5" alt="SK Namayan Logo">
                              <span class="text-xs font-black tracking-wider uppercase font-display text-white">SK Namayan</span>
                          </div>
                          <button @click="mobileMenuOpen = false" type="button" class="text-blue-100 hover:text-white focus:outline-none p-1.5 rounded-xl hover:bg-white/10 transition" aria-label="Close menu">
                              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                          </button>
                      </div>

                      <!-- Menu Body -->
                      <div class="flex-1 overflow-y-auto px-4 py-6 space-y-4">
                          <a href="{{ route('news.index') }}" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-blue-100 hover:text-white hover:bg-blue-800 font-bold font-display uppercase tracking-wider transition">
                              <svg class="w-5 h-5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 4a2 2 0 00-2 2v3m2-3a2 2 0 012 2v3a2 2 0 01-2 2h-2m-3-10V4m-4 12h4m-4-4h4" /></svg>
                              <span>News Articles</span>
                          </a>

                          <a href="{{ route('officials.index') }}" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-blue-100 hover:text-white hover:bg-blue-800 font-bold font-display uppercase tracking-wider transition">
                              <svg class="w-5 h-5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                              <span>SK Officials</span>
                          </a>

                          <a href="{{ route('transparency.index') }}" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-blue-100 hover:text-white hover:bg-blue-800 font-bold font-display uppercase tracking-wider transition">
                              <svg class="w-5 h-5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                              <span>Transparency Board</span>
                          </a>

                          @if (Route::has('login'))
                              @auth
                                  @if(!Auth::user()->canAccessDashboard())
                                      <a href="{{ route('profile.my-requests') }}" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-blue-100 hover:text-white hover:bg-blue-800 font-bold font-display uppercase tracking-wider transition">
                                          <svg class="w-5 h-5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                                          <span>My Requests</span>
                                      </a>
                                  @endif
                                  
                                  @if(Auth::user()->canAccessDashboard())
                                      <a href="{{ route('dashboard.index') }}" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-blue-100 hover:text-white hover:bg-blue-800 font-bold font-display uppercase tracking-wider transition">
                                          <svg class="w-5 h-5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" /></svg>
                                          <span>Dashboard</span>
                                      </a>
                                  @endif
                              @endauth
                          @endif
                      </div>

                      <!-- Footer/Auth Section inside Drawer (guests only) -->
                      @guest
                      <div class="p-4 border-t border-blue-800 bg-[#193596]">
                          <div class="flex flex-col space-y-2">
                              <a href="{{ route('login') }}" @click="mobileMenuOpen = false" class="block w-full px-4 py-3 rounded-2xl border border-white/20 hover:border-white text-white hover:bg-white/10 font-bold text-center transition">
                                  Login
                              </a>
                              @if (Route::has('register'))
                                  <a href="{{ route('register') }}" @click="mobileMenuOpen = false" class="block w-full px-4 py-3 rounded-2xl bg-white text-[#1e40af] hover:bg-blue-50 font-bold text-center transition shadow-md">
                                      Register
                                  </a>
                              @endif
                          </div>
                      </div>
                      @endguest

                 </div>
            </div>
            @endunless
        </nav>

        <!-- Flash Messages Block (Modal Style matching the 2nd picture) -->
        @if (session('success') || session('error'))
            <div x-data="{ showFlashModal: true }" x-show="showFlashModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
                <!-- Backdrop with blur -->
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300" @click="showFlashModal = false"></div>
                
                <!-- Card Container -->
                <div class="relative bg-white rounded-[2rem] shadow-2xl max-w-[340px] w-full overflow-hidden border border-slate-100 transform transition-all duration-300 z-50 flex flex-col items-center pb-8"
                     x-show="showFlashModal"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95">
                    
                    @if (session('success'))
                        <!-- Top Half (Green header for success) -->
                        <div class="w-full bg-[#10b981] flex items-center justify-center py-10 relative">
                            <!-- Circular background overlay -->
                            <div class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" stroke-width="4.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Bottom Half -->
                        <div class="p-6 w-full flex flex-col items-center text-center space-y-3">
                            <h3 class="text-xl font-bold text-slate-800 font-display">Success</h3>
                            <p class="text-xs text-slate-500 font-semibold leading-relaxed px-2">
                                {{ session('success') }}
                            </p>
                        </div>
                    @else
                        <!-- Top Half (Red header for error) -->
                        <div class="w-full bg-rose-500 flex items-center justify-center py-10 relative">
                            <!-- Circular background overlay -->
                            <div class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" stroke-width="4.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Bottom Half -->
                        <div class="p-6 w-full flex flex-col items-center text-center space-y-3">
                            <h3 class="text-xl font-bold text-slate-800 font-display">Error</h3>
                            <p class="text-xs text-slate-500 font-semibold leading-relaxed px-2">
                                {{ session('error') }}
                            </p>
                        </div>
                    @endif
                    
                    <!-- Okay Button -->
                    <button @click="showFlashModal = false" class="mt-2 px-10 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-full shadow-md active:scale-95 transition-all duration-150 cursor-pointer min-w-[140px]">
                        Okay
                    </button>
                </div>
            </div>
        @endif

        <!-- Main Content Slot -->
        <main class="flex-1 flex flex-col">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800 text-xs mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Column 1: Brand -->
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <span class="font-bold text-white tracking-wider font-display uppercase">Sangguniang Kabataan Namayan</span>
                        </div>
                        <p class="leading-relaxed max-w-sm">
                            Empowering youth governance in Barangay Namayan, Mandaluyong. Offering digitalized solutions for community health, education, sports, and social welfare requests.
                        </p>
                    </div>

                    <!-- Column 2: Quick Links -->
                    <div class="space-y-3">
                        <span class="font-bold text-white uppercase tracking-wider font-display">Services Directory</span>
                        <ul class="grid grid-cols-2 gap-2 text-slate-400">
                            <li><a href="{{ route('forms.health.create') }}" class="hover:text-white transition">Health Consult</a></li>
                            <li><a href="{{ route('forms.mental-health.create') }}" class="hover:text-white transition">Mental Support</a></li>
                            <li><a href="{{ route('forms.medicine.create') }}" class="hover:text-white transition">Pabili Medicine</a></li>
                            <li><a href="{{ route('forms.silid.create') }}" class="hover:text-white transition">Silid Karunungan</a></li>
                            <li><a href="{{ route('forms.sports.create') }}" class="hover:text-white transition">Sports League</a></li>
                            <li><a href="{{ route('track.index') }}" class="hover:text-white transition">Track Request</a></li>
                            <li><a href="{{ route('officials.index') }}" class="hover:text-white transition">SK Officials</a></li>
                            <li><a href="{{ route('transparency.index') }}" class="hover:text-white transition">Transparency Board</a></li>
                            <li><a href="{{ route('about') }}" class="hover:text-white transition">About Us</a></li>
                        </ul>
                    </div>

                    <!-- Column 3: Accounts and Contact info -->
                    <div class="space-y-3">
                        <span class="font-bold text-white uppercase tracking-wider font-display">Barangay Desk</span>
                        <p class="leading-relaxed">
                            Barangay Namayan SK Office, Mandaluyong City, Metro Manila<br>
                            Phone: +63 (2) 8532 5001<br>
                            Email: info@sknamayan.gov.ph
                        </p>
                        <div class="flex items-center space-x-4 pt-1">
                            @guest
                                <a href="{{ route('login') }}" class="hover:text-white transition font-medium">Citizen Login</a>
                                <span class="text-slate-700">|</span>
                                <a href="{{ route('register') }}" class="hover:text-white transition font-medium">Create Account</a>
                            @else
                                <a href="{{ route('profile.edit') }}" class="hover:text-white transition font-medium">My Account Profile</a>
                            @endguest
                        </div>
                    </div>
                </div>

                <hr class="border-slate-800 my-8">

                <div class="flex flex-col sm:flex-row items-center justify-between text-slate-500">
                    <div>
                        &copy; {{ date('Y') }} Sangguniang Kabataan Namayan. All rights reserved.
                    </div>
                </div>
            </div>
        </footer>

        <!-- data-flash auto dismiss helper -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('[data-flash]').forEach(function(el) {
                    setTimeout(function() {
                        el.style.opacity = '0';
                        setTimeout(function() {
                            el.remove();
                        }, 300);
                    }, 5000);
                });
            });
        </script>
    </body>
</html>
