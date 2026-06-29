@extends('layouts.app')

@section('content')
@if(Auth::user()->canAccessDashboard())
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

            <div class="p-6 md:p-8 space-y-6 flex-1 overflow-y-auto">
                <div class="max-w-xl space-y-8">
                    <div>
                        <span class="text-[10px] font-black tracking-widest text-[#1e40af] uppercase font-display block">Settings Portal</span>
                        <h1 class="text-2xl font-black tracking-tight text-slate-800 font-display uppercase mt-1">Edit Account Profile</h1>
                        <p class="text-xs text-slate-400 mt-1">Manage your basic contact info, change security credentials, or delete your account records.</p>
                    </div>

                    @include('profile.partials.edit-forms')
                </div>
            </div>
        </div>
    </div>
@else
    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-1 font-sans space-y-8">
        <div>
            <span class="text-[10px] font-black tracking-widest text-[#1e40af] uppercase font-display block">Settings Portal</span>
            <h1 class="text-2xl font-black tracking-tight text-slate-800 font-display uppercase mt-1">Edit Account Profile</h1>
            <p class="text-xs text-slate-400 mt-1">Manage your basic contact info, change security credentials, or delete your account records.</p>
        </div>

        @include('profile.partials.edit-forms')
    </div>
@endif
@endsection
