<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SK Namayan') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

        <!-- Dark Mode Guard Script -->
        <script>
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="bg-[#fefefe] dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans antialiased flex flex-col min-h-screen">

        <!-- Sticky Header Navbar -->
        @php
            $hidePublicMobileMenu = request()->routeIs('dashboard.*', 'admin.*')
                || (auth()->check() && auth()->user()->canAccessDashboard() && request()->routeIs('profile.edit'));
        @endphp
        <nav x-data="{ mobileMenuOpen: false }" class="bg-[#1e40af] dark:bg-slate-900 text-white sticky top-0 z-40 shadow-lg border-b border-blue-800 dark:border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between relative">

                <!-- Left: Burger & Branding with Logo -->
                <div class="flex items-center space-x-3">
                    <!-- Burger Icon (public/end-user pages) -->
                    @unless($hidePublicMobileMenu)
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="inline-flex text-blue-100 hover:text-white focus:outline-none focus:ring-2 focus:ring-white/50 p-1.5 rounded-xl hover:bg-white/10 transition" aria-label="Toggle menu">
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
                </div>


                <!-- Right: Nav options & dropdowns -->
                <div class="flex items-center space-x-2 sm:space-x-3 text-sm shrink-0" 
                     x-data="{ 
                         darkMode: localStorage.getItem('theme') === 'dark',
                         notifOpen: false, 
                         profileOpen: false 
                     }"
                     x-init="$watch('darkMode', val => {
                         if (val) {
                             document.documentElement.classList.add('dark');
                         } else {
                             document.documentElement.classList.remove('dark');
                         }
                     })">

                    <!-- Theme Toggle Switch -->
                    <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')"
                            type="button"
                            class="p-2 rounded-xl text-blue-100 hover:text-white hover:bg-white/10 transition focus:outline-none"
                            aria-label="Toggle theme">
                        <!-- Moon Icon (Light Mode active, click to set Dark Mode) -->
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <!-- Sun Icon (Dark Mode active, click to set Light Mode) -->
                        <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-cloak>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M14 12a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </button>

                    @if (Route::has('login'))
                        @auth
                            <!-- Notification Center Dropdown -->
                            <div class="relative">
                                @php
                                    $unreadCount = Auth::user()->notifications()->whereNull('read_at')->count();
                                    $notifications = Auth::user()->notifications()->take(5)->get();
                                @endphp
                                <button @click="notifOpen = !notifOpen; profileOpen = false" 
                                        type="button" 
                                        class="p-2 rounded-xl text-blue-100 hover:text-white hover:bg-white/10 transition focus:outline-none relative">
                                    <!-- Bell Icon -->
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                    @if($unreadCount > 0)
                                        <span class="absolute top-1.5 right-1.5 flex h-2 w-2">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                                        </span>
                                    @endif
                                </button>

                                <!-- Notifications Dropdown Panel -->
                                <div x-show="notifOpen" 
                                     @click.away="notifOpen = false"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 scale-95 mt-0"
                                     x-transition:enter-end="opacity-100 scale-100 mt-2"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 scale-100 mt-2"
                                     x-transition:leave-end="opacity-0 scale-95 mt-0"
                                     class="absolute right-0 w-80 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-xl py-3 z-50 text-slate-800 dark:text-slate-100"
                                     x-cloak>
                                    <div class="px-4 pb-2 border-b border-slate-100 dark:border-slate-850 flex items-center justify-between">
                                        <span class="font-bold text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Notifications</span>
                                        @if($unreadCount > 0)
                                            <form method="POST" action="{{ route('notifications.read-all') }}">
                                                @csrf
                                                <button type="submit" class="text-[10px] text-blue-600 dark:text-blue-400 font-bold hover:underline">
                                                    Mark all read
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                    <div class="max-h-64 overflow-y-auto">
                                        @forelse($notifications as $notif)
                                            <form method="POST" action="{{ route('notifications.read', $notif) }}" class="block">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="w-full text-left px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-850 transition border-b border-slate-50 dark:border-slate-850/30 flex flex-col space-y-1">
                                                    <div class="flex items-center justify-between">
                                                        <span class="font-bold text-xs {{ $notif->read_at ? 'text-slate-500 dark:text-slate-400' : 'text-slate-850 dark:text-white' }}">
                                                            {{ $notif->title }}
                                                        </span>
                                                        @if(!$notif->read_at)
                                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600 shrink-0"></span>
                                                        @endif
                                                    </div>
                                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-normal">
                                                        {{ $notif->message }}
                                                    </p>
                                                    <span class="text-[9px] text-slate-400">{{ $notif->created_at->diffForHumans() }}</span>
                                                </button>
                                            </form>
                                        @empty
                                            <div class="px-4 py-6 text-center text-xs text-slate-400 dark:text-slate-500">
                                                No notifications yet.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <!-- User Profile Dropdown -->
                            <div class="relative">
                                @php
                                    $initials = '';
                                    $user = Auth::user();
                                    $initials = strtoupper(substr($user->first_name ?? $user->name, 0, 1) . substr($user->last_name ?? '', 0, 1));
                                @endphp
                                <button @click="profileOpen = !profileOpen; notifOpen = false" 
                                        type="button" 
                                        class="flex items-center focus:outline-none active:scale-95 transition"
                                        aria-label="User Menu">
                                    <div class="w-8 h-8 rounded-full bg-white dark:bg-slate-800 text-[#1e40af] dark:text-blue-400 font-extrabold text-xs flex items-center justify-center border border-white/20 shadow-sm">
                                        {{ $initials }}
                                    </div>
                                </button>

                                <!-- Dropdown Panel -->
                                <div x-show="profileOpen" 
                                     @click.away="profileOpen = false"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 scale-95 mt-0"
                                     x-transition:enter-end="opacity-100 scale-100 mt-2"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 scale-100 mt-2"
                                     x-transition:leave-end="opacity-0 scale-95 mt-0"
                                     class="absolute right-0 w-56 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-xl py-2 z-50 text-slate-800 dark:text-slate-100"
                                     x-cloak>
                                    <div class="px-4 py-2.5 border-b border-slate-100 dark:border-slate-850">
                                        <p class="font-extrabold text-xs text-slate-850 dark:text-white truncate">{{ $user->name }}</p>
                                        <p class="text-[10px] text-slate-400 truncate">{{ $user->email }}</p>
                                    </div>
                                    <div class="py-1">
                                        @if($user->canAccessDashboard())
                                            <a href="{{ route('dashboard.index') }}" @click="profileOpen = false" class="flex items-center space-x-2 px-4 py-2 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-850 transition">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" /></svg>
                                                <span>Dashboard</span>
                                            </a>
                                        @else
                                            <a href="{{ route('profile.my-requests') }}" @click="profileOpen = false" class="flex items-center space-x-2 px-4 py-2 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-850 transition">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                                                <span>My Requests</span>
                                            </a>
                                        @endif
                                        <a href="{{ route('profile.edit') }}" @click="profileOpen = false" class="flex items-center space-x-2 px-4 py-2 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-850 transition">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                            <span>Account Settings</span>
                                        </a>
                                    </div>
                                    <div class="border-t border-slate-100 dark:border-slate-850 pt-1">
                                        <form method="POST" action="{{ route('logout') }}" class="block">
                                            @csrf
                                            <button type="submit" class="w-full flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-rose-650 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition text-left">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                                <span>Logout</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
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
            <!-- Menu Drawer Overlay & Panel -->
            <div x-show="mobileMenuOpen"
                 class="fixed inset-0 z-50"
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
                      class="fixed inset-y-0 left-0 w-[75%] max-w-[320px] bg-gradient-to-b from-[#1e40af] to-[#0f172a] dark:from-slate-900 dark:to-slate-950 text-white shadow-2xl flex flex-col justify-between z-50 border-r border-white/10 dark:border-slate-800">

                      <!-- Header inside Drawer -->
                      <div class="px-5 py-5 flex items-center justify-between border-b border-white/10 dark:border-slate-800">
                          <div class="flex items-center space-x-2.5">
                              <img src="{{ asset('images/logo.png') }}" class="w-9 h-9 object-contain rounded-full bg-white p-0.5 shadow-md shadow-black/10" alt="SK Namayan Logo">
                              <span class="text-xs font-black tracking-widest uppercase font-display text-white">SK Namayan</span>
                          </div>
                          <button @click="mobileMenuOpen = false" type="button" class="text-blue-100 hover:text-white focus:outline-none p-2 rounded-xl hover:bg-white/10 active:scale-95 transition-all" aria-label="Close menu">
                              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                          </button>
                      </div>

                      <!-- Menu Body -->
                      <div class="flex-1 overflow-y-auto px-4 py-8 space-y-3">
                          <a href="/" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-blue-100 hover:text-white hover:bg-white/10 hover:translate-x-1 font-bold font-display uppercase tracking-wider text-[11px] transition-all duration-300">
                              <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                              <span>Home</span>
                          </a>

                          <a href="{{ route('news.index') }}" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-blue-100 hover:text-white hover:bg-white/10 hover:translate-x-1 font-bold font-display uppercase tracking-wider text-[11px] transition-all duration-300">
                              <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 4a2 2 0 00-2 2v3m2-3a2 2 0 012 2v3a2 2 0 01-2 2h-2m-3-10V4m-4 12h4m-4-4h4" /></svg>
                              <span>News Articles</span>
                          </a>

                          <a href="{{ route('officials.index') }}" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-blue-100 hover:text-white hover:bg-white/10 hover:translate-x-1 font-bold font-display uppercase tracking-wider text-[11px] transition-all duration-300">
                              <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                              <span>SK Officials</span>
                          </a>

                          <a href="{{ route('transparency.index') }}" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-blue-100 hover:text-white hover:bg-white/10 hover:translate-x-1 font-bold font-display uppercase tracking-wider text-[11px] transition-all duration-300">
                              <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                              <span>Transparency Board</span>
                          </a>

                          @if (Route::has('login'))
                              @auth
                                  @if(!Auth::user()->canAccessDashboard())
                                      <a href="{{ route('profile.my-requests') }}" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-blue-100 hover:text-white hover:bg-white/10 hover:translate-x-1 font-bold font-display uppercase tracking-wider text-[11px] transition-all duration-300">
                                          <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                                          <span>My Requests</span>
                                      </a>

                                      <a href="{{ route('forms.sports.create') }}" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-blue-100 hover:text-white hover:bg-white/10 hover:translate-x-1 font-bold font-display uppercase tracking-wider text-[11px] transition-all duration-300">
                                          <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5a2 2 0 10-2 2h2zm0 13a4 4 0 100-8 4 4 0 000 8z"></path></svg>
                                          <span>Sports League</span>
                                      </a>

                                      <a href="{{ route('profile.profiling.create') }}" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-blue-100 hover:text-white hover:bg-white/10 hover:translate-x-1 font-bold font-display uppercase tracking-wider text-[11px] transition-all duration-300">
                                          <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                          <span>KK Self-Profiling</span>
                                      </a>
                                  @endif

                                  @if(Auth::user()->canAccessDashboard())
                                      <a href="{{ route('dashboard.index') }}" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-blue-100 hover:text-white hover:bg-white/10 hover:translate-x-1 font-bold font-display uppercase tracking-wider text-[11px] transition-all duration-300">
                                          <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" /></svg>
                                          <span>Dashboard</span>
                                      </a>
                                  @endif
                              @endauth
                          @endif
                      </div>

                      <!-- Footer/Auth Section inside Drawer (guests only) -->
                      @guest
                      <div class="p-5 border-t border-white/10 dark:border-slate-800 bg-[#0f172a]/20">
                          <div class="flex flex-col space-y-2.5">
                              <a href="{{ route('login') }}" @click="mobileMenuOpen = false" class="block w-full px-4 py-2.5 rounded-xl border border-white/20 hover:border-white text-white hover:bg-white/10 font-bold text-center text-xs transition active:scale-95">
                                  Login
                              </a>
                              @if (Route::has('register'))
                                  <a href="{{ route('register') }}" @click="mobileMenuOpen = false" class="block w-full px-4 py-2.5 rounded-xl bg-white text-[#1e40af] hover:bg-blue-50 font-bold text-center text-xs transition shadow-md active:scale-95">
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
