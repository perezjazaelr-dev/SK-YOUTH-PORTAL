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

        <div class="p-6 md:p-8 space-y-6 flex-1 overflow-y-auto">
            
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <span class="text-[10px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Branding Manager</span>
                    <h1 class="text-2xl font-black tracking-tight text-slate-800 font-display uppercase mt-1">Sponsor Partnerships</h1>
                    <p class="text-xs text-slate-500 mt-1">Add and manage active sponsor logos shown on the landing page footer slider.</p>
                </div>
                <a href="{{ route('admin.partners.create') }}" class="btn-primary text-xs shrink-0 flex items-center space-x-1">
                    <span>➕ Add New Sponsor</span>
                </a>
            </div>

            <!-- Partners Grid -->
            <div class="card p-0 overflow-hidden bg-white border border-slate-100">
                @if($partners->isEmpty())
                    <div class="text-center py-16 px-4 space-y-4">
                        <div class="w-16 h-16 bg-slate-50 text-slate-400 border border-slate-100 rounded-3xl flex items-center justify-center mx-auto text-2xl">🤝</div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">No Sponsors Added</h3>
                            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Upload partner/sponsor logos to showcase them dynamically on the public landing page.</p>
                        </div>
                        <div class="pt-2">
                            <a href="{{ route('admin.partners.create') }}" class="btn-primary text-xs">Add Your First Sponsor</a>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider font-display">
                                    <th class="p-4 pl-6">Sponsor Logo</th>
                                    <th class="p-4">Name</th>
                                    <th class="p-4">Website Link</th>
                                    <th class="p-4">Status</th>
                                    <th class="p-4 pr-6 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                @foreach($partners as $partner)
                                    <tr class="hover:bg-slate-50/50 transition duration-150">
                                        <!-- Logo -->
                                        <td class="p-4 pl-6">
                                            <div class="w-16 h-10 bg-slate-50 rounded-lg border border-slate-100 p-1 flex items-center justify-center overflow-hidden">
                                                <img src="{{ asset('storage/' . $partner->logo_path) }}" class="max-w-full max-h-full object-contain" alt="{{ $partner->name }} logo">
                                            </div>
                                        </td>
                                        <!-- Name -->
                                        <td class="p-4 font-bold text-slate-800">
                                            {{ $partner->name }}
                                        </td>
                                        <!-- Link -->
                                        <td class="p-4 font-mono text-slate-400 select-all">
                                            @if($partner->website_url)
                                                <a href="{{ $partner->website_url }}" target="_blank" class="text-blue-600 hover:underline flex items-center space-x-1">
                                                    <span>{{ Str::limit($partner->website_url, 30) }}</span>
                                                    <svg class="w-3 h-3 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                </a>
                                            @else
                                                <span class="text-slate-300 italic">None</span>
                                            @endif
                                        </td>
                                        <!-- Status -->
                                        <td class="p-4">
                                            @if($partner->is_active)
                                                <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 rounded-full text-[9px] font-extrabold uppercase tracking-wide font-display">Active</span>
                                            @else
                                                <span class="px-2.5 py-0.5 bg-slate-100 text-slate-500 rounded-full text-[9px] font-extrabold uppercase tracking-wide font-display">Inactive</span>
                                            @endif
                                        </td>
                                        <!-- Actions -->
                                        <td class="p-4 pr-6 text-right space-x-1.5 whitespace-nowrap">
                                            <a href="{{ route('admin.partners.edit', $partner->id) }}" class="inline-flex items-center px-2.5 py-1 border border-slate-200 text-slate-600 hover:text-[#1e40af] hover:border-[#1e40af] font-bold rounded-lg transition text-[10px] uppercase tracking-wider active:scale-95">
                                                Edit
                                            </a>
                                                <x-alert-dialog>
                                                    <x-slot:trigger>
                                                        <button type="button" class="inline-flex items-center px-2.5 py-1 bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold rounded-lg transition text-[10px] uppercase tracking-wider active:scale-95 border border-transparent">
                                                            Delete
                                                        </button>
                                                    </x-slot:trigger>
                                                    
                                                     <x-slot:icon>
                                                         <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                         </svg>
                                                     </x-slot:icon>
                                                    
                                                    <x-slot:title>
                                                        Delete Sponsor
                                                    </x-slot:title>
                                                    
                                                    <x-slot:description>
                                                        Are you sure you want to delete "{{ $partner->name }}"? This will permanently remove their logo from public view. This action cannot be undone.
                                                    </x-slot:description>
                                                    
                                                    <x-slot:footer>
                                                        <button type="button" @click="open = false" class="btn-outline text-xs py-2 px-4">
                                                            Cancel
                                                        </button>
                                                        <form method="POST" action="{{ route('admin.partners.destroy', $partner->id) }}" class="inline">
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
                    @if($partners->hasPages())
                        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                            {{ $partners->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>

    </div>

</div>
@endsection
