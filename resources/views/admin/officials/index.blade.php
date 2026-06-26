@extends('layouts.app')

@section('content')
<div x-data="{ mobileSidebar: false }" class="flex-1 flex flex-col md:flex-row bg-[#f8fafc]">
    @include('layouts.dashboard-sidebar')
    <div x-show="mobileSidebar" @click="mobileSidebar = false" class="fixed inset-0 bg-slate-900/40 z-20 md:hidden" x-cloak></div>

    <div class="flex-1 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-100 h-16 px-4 flex items-center justify-between md:hidden shrink-0">
            <button @click="mobileSidebar = true" type="button" class="inline-flex items-center justify-center min-w-11 min-h-11 p-2 text-slate-500 hover:text-slate-800 active:scale-95 transition" aria-label="Open menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-800 font-display">SK Officials</span>
            <div class="w-11"></div>
        </header>

        <div class="p-4 md:p-8 space-y-6 flex-1 overflow-y-auto">
            <nav class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider pb-4 border-b border-slate-100">
                <a href="{{ route('dashboard.index') }}" class="text-slate-400 hover:text-[#1e40af]">Dashboard</a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-800">SK Officials</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-100">
                <div class="space-y-1">
                    <span class="text-[10px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Governance</span>
                    <h1 class="text-xl md:text-2xl font-black text-slate-800 font-display uppercase">Manage SK Officials</h1>
                    <p class="text-xs text-slate-500">Add and update elected officer profiles shown on the public officials page.</p>
                </div>
                <a href="{{ route('admin.officials.create') }}" class="btn-primary text-xs shrink-0 min-h-11 inline-flex items-center justify-center">Add Official Profile</a>
            </div>

            <div class="card p-0 overflow-hidden">
                @if($officials->isEmpty())
                    <div class="text-center py-16 px-4">
                        <p class="text-sm font-bold text-slate-700 uppercase">No Officials Added</p>
                        <p class="text-xs text-slate-400 mt-1">Create the first SK official profile for the public page.</p>
                    </div>
                @else
                    {{-- Mobile cards --}}
                    <ul class="md:hidden divide-y divide-slate-100">
                        @foreach($officials as $official)
                            <li class="p-4 flex gap-3 min-w-0">
                                <div class="w-14 h-14 shrink-0 rounded-xl overflow-hidden bg-slate-100 border border-slate-100">
                                    @if($official->photoUrl())
                                        <img src="{{ $official->photoUrl() }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-[#1e40af] text-white text-xs font-black">{{ $official->initials() }}</div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0 space-y-2">
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 truncate">{{ $official->name }}</p>
                                        <p class="text-[10px] text-slate-500 truncate">{{ $official->position }}</p>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        @if($official->is_active)
                                            <span class="text-[9px] font-black uppercase px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-full">Active</span>
                                        @else
                                            <span class="text-[9px] font-black uppercase px-2 py-0.5 bg-slate-100 text-slate-500 rounded-full">Hidden</span>
                                        @endif
                                        <span class="text-[9px] text-slate-400">Order: {{ $official->sort_order }}</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.officials.edit', $official) }}" class="text-[10px] font-bold uppercase text-[#1e40af] px-3 py-2 bg-blue-50 rounded-lg min-h-10 inline-flex items-center">Edit</a>
                                        <form method="POST" action="{{ route('admin.officials.destroy', $official) }}" onsubmit="return confirm('Delete this official profile?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-[10px] font-bold uppercase text-rose-700 px-3 py-2 bg-rose-50 rounded-lg min-h-10">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    {{-- Desktop table --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="p-4 pl-6">Photo</th>
                                    <th class="p-4">Name</th>
                                    <th class="p-4">Position</th>
                                    <th class="p-4">Term</th>
                                    <th class="p-4">Order</th>
                                    <th class="p-4">Status</th>
                                    <th class="p-4 pr-6 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($officials as $official)
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="p-4 pl-6">
                                            <div class="w-10 h-10 rounded-lg overflow-hidden bg-slate-100">
                                                @if($official->photoUrl())
                                                    <img src="{{ $official->photoUrl() }}" alt="" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center bg-[#1e40af] text-white text-[10px] font-black">{{ $official->initials() }}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="p-4 font-bold text-slate-800">{{ $official->name }}</td>
                                        <td class="p-4 text-slate-600">{{ $official->position }}</td>
                                        <td class="p-4 text-slate-500">{{ $official->term ?? '—' }}</td>
                                        <td class="p-4 text-slate-500">{{ $official->sort_order }}</td>
                                        <td class="p-4">
                                            @if($official->is_active)
                                                <span class="text-[9px] font-black uppercase px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-full">Active</span>
                                            @else
                                                <span class="text-[9px] font-black uppercase px-2 py-0.5 bg-slate-100 text-slate-500 rounded-full">Hidden</span>
                                            @endif
                                        </td>
                                        <td class="p-4 pr-6 text-right space-x-2 whitespace-nowrap">
                                            <a href="{{ route('officials.show', $official->slug) }}" target="_blank" class="text-[10px] font-bold uppercase text-slate-500 hover:text-[#1e40af]">View</a>
                                            <a href="{{ route('admin.officials.edit', $official) }}" class="text-[10px] font-bold uppercase text-[#1e40af] px-2 py-1 bg-blue-50 rounded-lg">Edit</a>
                                            <form method="POST" action="{{ route('admin.officials.destroy', $official) }}" class="inline" onsubmit="return confirm('Delete this official profile?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-[10px] font-bold uppercase text-rose-700 px-2 py-1 bg-rose-50 rounded-lg">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-slate-100">{{ $officials->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
