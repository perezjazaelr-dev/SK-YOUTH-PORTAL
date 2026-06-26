@extends('layouts.app')

@section('content')
<!-- Custom Styles for clean UI scroll track -->
<style>
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<div x-data="{ 
    mobileSidebar: false, 
    activeCommitteeId: 'all', 
    showAddCommittee: false, 
    showAddInitiative: false, 
    activeAdminTab: 'structure',
    showEditInitiative: false,
    editInitiative: { id: null, title: '', description: '', form_route: '', custom_fields: [] },
    newCustomFields: [],
    openEditModal(initiative) {
        this.editInitiative = {
            id: initiative.id,
            title: initiative.title ?? '',
            description: initiative.description ?? '',
            form_route: initiative.form_route ?? '',
            custom_fields: Array.isArray(initiative.custom_fields)
                ? JSON.parse(JSON.stringify(initiative.custom_fields))
                : []
        };
        this.showEditInitiative = true;
    }
}" class="flex-1 flex flex-col md:flex-row bg-[#f8fafc]">

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

        <div class="p-6 md:p-8 space-y-8 flex-1 overflow-y-auto">
            
            <!-- Breadcrumbs -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider">
                    <a href="{{ route('dashboard.index') }}" class="text-slate-400 hover:text-[#1e40af] transition duration-150">Dashboard</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-800">Portal Structure</span>
                </div>
            </div>

            <!-- Header Block -->
            <div class="space-y-1">
                <div class="flex items-center space-x-2.5">
                    <span class="text-[10px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Portal Architecture</span>
                    <span class="px-2 py-0.5 bg-blue-50 text-[#1e40af] rounded-md text-[9px] font-bold uppercase font-mono">
                        {{ $committees->count() }} Committees &bull; {{ $committees->sum(fn($c) => $c->initiatives->count()) }} Initiatives
                    </span>
                </div>
                <h1 class="text-2xl font-black tracking-tight text-slate-800 font-display uppercase mt-1">Portal Structure Management</h1>
                <p class="text-xs text-slate-500 mt-1">Configure and manage committees (subtopics) and their corresponding project initiatives.</p>
            </div>

            <!-- Filter Tabs Section (Full Width, Styled like Reference Image) -->
            @if(!$committees->isEmpty())
                <div class="bg-white border border-slate-100 p-6 rounded-3xl shadow-sm space-y-6">
                    <!-- Top Tabs Bar (Styled exactly like reference image) -->
                    <div class="border-b border-slate-200 flex space-x-6 text-xs font-bold uppercase tracking-wider mb-4 relative">
                        <button @click="activeAdminTab = 'structure'"
                                :class="activeAdminTab === 'structure' ? 'text-[#1e40af] border-b-2 border-[#1e40af] -mb-px pb-3' : 'text-slate-400 hover:text-[#1e40af] pb-3'"
                                class="transition duration-150 outline-none font-display font-black tracking-widest cursor-pointer">
                            Portal Structure Explorer
                        </button>
                        <button @click="activeAdminTab = 'guidelines'"
                                :class="activeAdminTab === 'guidelines' ? 'text-[#1e40af] border-b-2 border-[#1e40af] -mb-px pb-3' : 'text-slate-400 hover:text-[#1e40af] pb-3'"
                                class="transition duration-150 outline-none font-display font-black tracking-widest cursor-pointer">
                            System Management Guidelines
                        </button>
                    </div>

                    <!-- Bottom Filter Pills (Visible when structure tab is active) -->
                    <div x-show="activeAdminTab === 'structure'" class="space-y-4">
                        <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
                            <button @click="activeCommitteeId = 'all'"
                                    :class="activeCommitteeId === 'all' ? 'bg-[#1e40af] text-white font-bold shadow-sm shadow-blue-500/10 border border-[#1e40af]' : 'bg-transparent border border-[#1e40af] text-[#1e40af] hover:bg-blue-50/50'"
                                    class="px-4 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition duration-150 shrink-0 active:scale-95 whitespace-nowrap cursor-pointer"
                            >
                                All Committees
                            </button>
                            @foreach($committees as $cItem)
                                <button @click="activeCommitteeId = {{ $cItem->id }}"
                                        :class="activeCommitteeId === {{ $cItem->id }} ? 'bg-[#1e40af] text-white font-bold shadow-sm shadow-blue-500/10 border border-[#1e40af]' : 'bg-transparent border border-[#1e40af] text-[#1e40af] hover:bg-blue-50/50'"
                                        class="px-4 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition duration-150 shrink-0 active:scale-95 whitespace-nowrap font-display cursor-pointer"
                                >
                                    {{ $cItem->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Guidelines tab content -->
            <div x-show="activeAdminTab === 'guidelines'" 
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="card bg-white border border-slate-100 p-6 rounded-3xl space-y-6"
                 x-cloak>
                 <div class="space-y-2">
                     <span class="text-[9px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Administrative Roles</span>
                     <h3 class="text-base font-bold text-slate-800 uppercase tracking-wide font-display">Portal Access & Structure Control Guide</h3>
                     <p class="text-xs text-slate-500 leading-relaxed font-medium">The SK Portal structure relies on strict role-based access control (RBAC). Ensure configurations are updated in accordance with the security definitions below.</p>
                 </div>
                 
                 <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                     <div class="p-5 border border-slate-100 rounded-2xl bg-slate-50/50 space-y-2">
                         <div class="flex items-center space-x-2">
                             <span class="text-lg">🛡️</span>
                             <h4 class="text-xs font-black uppercase text-slate-800 font-display">Administrator (Admin)</h4>
                         </div>
                         <p class="text-[11px] text-slate-500 leading-relaxed">Full read, write, and delete permissions. Only Admins can register new Committees, configure new Project Initiatives, link forms, or permanently delete categories from the system.</p>
                     </div>
                     <div class="p-5 border border-slate-100 rounded-2xl bg-slate-50/50 space-y-2">
                         <div class="flex items-center space-x-2">
                             <span class="text-lg">👤</span>
                             <h4 class="text-xs font-black uppercase text-slate-800 font-display">Staff Member</h4>
                         </div>
                         <p class="text-[11px] text-slate-500 leading-relaxed">Read-only permissions for portal structure. Staff members can view committees and projects, evaluate submitted request documents, and manage/schedule timeline events on the Master Calendar.</p>
                     </div>
                     <div class="p-5 border border-slate-100 rounded-2xl bg-slate-50/50 space-y-2">
                         <div class="flex items-center space-x-2">
                             <span class="text-lg">🔑</span>
                             <h4 class="text-xs font-black uppercase text-slate-800 font-display">Data Privacy Officer (DPO)</h4>
                         </div>
                         <p class="text-[11px] text-slate-500 leading-relaxed">Data governance and masking supervisor. DPO has standard read access for structures, and is responsible for auditing masked personal identifiable information (PII) on public-facing exports and listings.</p>
                     </div>
                 </div>
            </div>

            <!-- Two Column Layout -->
            <div x-show="activeAdminTab === 'structure'" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Left Column: Committees (5 cols) -->
                <div class="lg:col-span-5 space-y-6">
                    
                    <!-- Header Section -->
                    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-black uppercase tracking-wider text-slate-700 font-display">Committees & Subtopics</h2>
                            <p class="text-[11px] text-slate-400">Configure public organizing categories.</p>
                        </div>
                        @if(Auth::user()->isAdmin())
                            <span class="sr-only">Add New Committee</span>
                            <button @click="showAddCommittee = !showAddCommittee" 
                                    class="btn-primary btn-sm flex items-center space-x-1">
                                <span x-text="showAddCommittee ? '✖ Close' : '➕ Add'">➕ Add</span>
                            </button>
                        @endif
                    </div>

                    @if(Auth::user()->isAdmin())
                        <!-- Inline Add Committee Form Card -->
                        <div x-show="showAddCommittee" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform translate-y-0"
                             x-transition:leave-end="opacity-0 transform -translate-y-2"
                             class="card bg-slate-50 border border-slate-200/60 p-5 rounded-2xl shadow-inner-sm"
                             x-cloak>
                            <form method="POST" action="{{ route('admin.structure.committee.store') }}" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="name" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Committee / Subtopic Name</label>
                                    <input type="text" name="name" id="name" required class="field text-xs py-2.5 bg-white" placeholder="e.g. Education & Library Services" value="{{ old('name') }}">
                                    @error('name')
                                        <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="submit" class="btn-primary text-xs w-full justify-center py-2.5">Add Committee</button>
                            </form>
                        </div>
                    @endif

                    <!-- Committees List -->
                    <div class="space-y-3">
                        @if($committees->isEmpty())
                            <div class="card bg-white p-6 border border-slate-100 text-center text-slate-400 text-xs">
                                No committees configured yet.
                            </div>
                        @else
                            @foreach($committees as $committee)
                                <div class="card bg-white p-4 border border-slate-100 shadow-sm rounded-2xl flex items-center justify-between gap-4 hover:border-blue-200 hover:shadow-md transition duration-205">
                                    <div class="flex items-center space-x-3 min-w-0">
                                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#1e40af] flex items-center justify-center font-black text-sm shrink-0 border border-blue-100/50">
                                            {{ substr($committee->name, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="text-xs font-black text-slate-800 truncate" title="{{ $committee->name }}">{{ $committee->name }}</h4>
                                            <div class="flex items-center space-x-2 text-[10px] text-slate-400 mt-0.5">
                                                <span class="font-mono bg-slate-50 px-1.5 py-0.5 rounded border border-slate-100 truncate max-w-[120px]">{{ $committee->slug }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2 shrink-0">
                                        <span class="px-2.5 py-1 bg-blue-50/70 border border-blue-100/50 text-[#1e40af] rounded-lg text-[9px] font-bold uppercase tracking-wider">
                                            {{ $committee->initiatives->count() }} Projects
                                        </span>
                                        @if(Auth::user()->isAdmin())
                                            <x-alert-dialog>
                                                <x-slot:trigger>
                                                    <button type="button" class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 rounded-xl transition active:scale-90 cursor-pointer" title="Delete Committee">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </x-slot:trigger>
                                                
                                                 <x-slot:icon>
                                                     <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                     </svg>
                                                 </x-slot:icon>
                                                
                                                <x-slot:title>
                                                    Delete Committee
                                                </x-slot:title>
                                                
                                                <x-slot:description>
                                                    WARNING: Deleting the committee "{{ $committee->name }}" will permanently delete all its associated initiatives/projects and accomplishment reports. This action cannot be undone.
                                                </x-slot:description>
                                                
                                                <x-slot:footer>
                                                    <button type="button" @click="open = false" class="btn-outline text-xs py-2 px-4">
                                                        Cancel
                                                    </button>
                                                    <form method="POST" action="{{ route('admin.structure.committee.destroy', $committee->id) }}" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn-danger py-2 text-xs">
                                                            Confirm Delete
                                                        </button>
                                                    </form>
                                                </x-slot:footer>
                                            </x-alert-dialog>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Right Column: Initiatives (7 cols) -->
                <div class="lg:col-span-7 space-y-6">
                    
                    <!-- Header Section -->
                    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-black uppercase tracking-wider text-slate-700 font-display">Initiatives & Projects</h2>
                            <p class="text-[11px] text-slate-400">Configure online request forms and projects.</p>
                        </div>
                        @if(Auth::user()->isAdmin())
                            <span class="sr-only">Add New Initiative</span>
                            <button @click="showAddInitiative = !showAddInitiative" 
                                    class="btn-primary btn-sm flex items-center space-x-1">
                                <span x-text="showAddInitiative ? '✖ Close' : '➕ Add'">➕ Add</span>
                            </button>
                        @endif
                    </div>

                    @if(Auth::user()->isAdmin())
                        <!-- Inline Add Initiative Form Card -->
                        <div x-show="showAddInitiative" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform translate-y-0"
                             x-transition:leave-end="opacity-0 transform -translate-y-2"
                             class="card bg-slate-50 border border-slate-200/60 p-5 rounded-2xl shadow-inner-sm"
                             x-cloak>
                            <form method="POST" action="{{ route('admin.structure.initiative.store') }}" class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="committee_id" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Target Committee</label>
                                        <select name="committee_id" id="committee_id" required class="field text-xs py-2.5 pr-8 bg-white">
                                            <option value="">Select Committee</option>
                                            @foreach($committees as $c)
                                                <option value="{{ $c->id }}" {{ old('committee_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('committee_id')
                                            <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="form_route" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Linked Form (Optional)</label>
                                        <select name="form_route" id="form_route" class="field text-xs py-2.5 pr-8 bg-white">
                                            <option value="">None (No Form Linked)</option>
                                            <option value="forms.health.create" {{ old('form_route') == 'forms.health.create' ? 'selected' : '' }}>Health Consultation Form</option>
                                            <option value="forms.mental-health.create" {{ old('form_route') == 'forms.mental-health.create' ? 'selected' : '' }}>Mental Health Support Form</option>
                                            <option value="forms.medicine.create" {{ old('form_route') == 'forms.medicine.create' ? 'selected' : '' }}>Pabili Medicine Services Form</option>
                                            <option value="forms.silid.create" {{ old('form_route') == 'forms.silid.create' ? 'selected' : '' }}>Silid Karunungan Booking Form</option>
                                            <option value="forms.sports.create" {{ old('form_route') == 'forms.sports.create' ? 'selected' : '' }}>Sports League Registration Form</option>
                                        </select>
                                        @error('form_route')
                                            <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div>
                                    <label for="title" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Initiative Title</label>
                                    <input type="text" name="title" id="title" required class="field text-xs py-2.5 bg-white" placeholder="e.g. Alternative Learning System" value="{{ old('title') }}">
                                    @error('title')
                                        <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="description" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Description</label>
                                    <textarea name="description" id="description" required rows="3" class="field text-xs py-2.5 bg-white" placeholder="Provide a short overview of this program initiative...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Custom Fields Builder for Add Initiative -->
                                <div class="space-y-4 pt-4 border-t border-slate-200 mt-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-700 font-display">Custom Form Fields</h4>
                                            <p class="text-[10px] text-slate-400">Configure custom fields to request from citizens upon submission.</p>
                                        </div>
                                        <button type="button" 
                                                @click="newCustomFields.push({ label: '', name: '', type: 'text', required: false, placeholder: '' })"
                                                class="btn-success btn-sm flex items-center space-x-1">
                                            <span>➕ Add Field</span>
                                        </button>
                                    </div>

                                    <div class="space-y-4">
                                        <!-- Empty State for custom fields -->
                                        <div x-show="newCustomFields.length === 0" class="text-center py-6 border border-dashed border-slate-200 rounded-2xl bg-white text-[11px] text-slate-400">
                                            No custom fields configured. Standard fields will be used.
                                        </div>

                                        <!-- Fields List -->
                                        <template x-for="(field, index) in newCustomFields" :key="index">
                                            <div class="p-4 bg-white border border-slate-200/60 rounded-2xl space-y-4 relative shadow-sm">
                                                <!-- Delete field button -->
                                                <button type="button" 
                                                        @click="newCustomFields.splice(index, 1)" 
                                                        class="absolute top-3 right-3 text-slate-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-slate-50 border border-transparent hover:border-slate-100 transition active:scale-90" 
                                                        title="Remove Field">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>

                                                <!-- Label & Technical Name -->
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mr-6">
                                                    <div>
                                                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Field Label (Citizen Visible)</label>
                                                        <input type="text" 
                                                               :name="`custom_fields[${index}][label]`" 
                                                               x-model="field.label" 
                                                               required 
                                                               @input="if(!field.name) { field.name = field.label.toLowerCase().replace(/[^a-z0-9]/g, '_').substring(0,30); }"
                                                               class="field text-[11px] py-2 bg-slate-50/50" 
                                                               placeholder="e.g. High School Attended">
                                                    </div>
                                                    <div>
                                                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Field Key (Short alphanumeric name)</label>
                                                        <input type="text" 
                                                               :name="`custom_fields[${index}][name]`" 
                                                               x-model="field.name" 
                                                               required 
                                                               @input="field.name = field.name.toLowerCase().replace(/[^a-z0-9_]/g, '_')"
                                                               class="field text-[11px] py-2 bg-slate-50/50 font-mono" 
                                                               placeholder="e.g. high_school_attended">
                                                    </div>
                                                </div>

                                                <!-- Type, Placeholder & Required options -->
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                    <div>
                                                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Field Input Type</label>
                                                        <select :name="`custom_fields[${index}][type]`" x-model="field.type" class="field text-[11px] py-2 pr-8 bg-slate-50/50">
                                                            <option value="text">Single Line Text</option>
                                                            <option value="textarea">Multi-line Paragraph</option>
                                                            <option value="number">Numeric Value</option>
                                                            <option value="date">Date Selector</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Input Placeholder / Hint</label>
                                                        <input type="text" :name="`custom_fields[${index}][placeholder]`" x-model="field.placeholder" class="field text-[11px] py-2 bg-slate-50/50" placeholder="e.g. Enter name of school...">
                                                    </div>
                                                    <div class="flex items-center pt-5">
                                                        <label class="inline-flex items-center text-[10px] font-bold text-slate-500 uppercase tracking-wider cursor-pointer select-none">
                                                            <input type="checkbox" 
                                                                   :name="`custom_fields[${index}][required]`" 
                                                                   x-model="field.required" 
                                                                   class="rounded border-slate-300 text-[#1e40af] focus:ring-[#1e40af]/20 mr-2 w-4 h-4">
                                                            Required Input Field
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <button type="submit" class="btn-primary text-xs w-full justify-center py-2.5">Add Initiative</button>
                            </form>
                        </div>
                    @endif

                    <!-- Initiatives Grouped List -->
                    <div class="space-y-4">
                        @if($committees->isEmpty())
                            <div class="card bg-white p-6 border border-slate-100 text-center text-slate-400 text-xs">
                                Setup committees first to view initiatives.
                            </div>
                        @else
                            @foreach($committees as $committee)
                                <div x-show="activeCommitteeId === 'all' || activeCommitteeId === {{ $committee->id }}"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-98"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     class="card bg-white p-5 md:p-6 border border-slate-100 shadow-sm rounded-3xl hover:border-blue-150 transition duration-200">
                                    
                                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                                        <div class="flex items-center space-x-2">
                                            <span class="w-1.5 h-4 bg-[#1e40af] rounded-full"></span>
                                            <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider font-display">{{ $committee->name }}</h4>
                                        </div>
                                        <span class="px-2 py-0.5 bg-slate-50 border border-slate-100 text-slate-500 rounded-full text-[9px] font-bold uppercase tracking-wide">
                                            {{ $committee->initiatives->count() }} Initiatives
                                        </span>
                                    </div>

                                    @if($committee->initiatives->isEmpty())
                                        <p class="text-slate-400 italic text-xs py-2">No initiatives registered under this committee yet.</p>
                                    @else
                                        <div class="space-y-4 divide-y divide-slate-100">
                                            @foreach($committee->initiatives as $index => $initiative)
                                                <div class="pt-4 {{ $index === 0 ? 'pt-0 border-t-0' : '' }} group/initiative">
                                                    <div class="flex items-start justify-between gap-4">
                                                        <div class="space-y-1.5 flex-1 min-w-0">
                                                            <h5 class="text-xs font-black text-slate-850 group-hover/initiative:text-[#1e40af] transition duration-150 truncate" title="{{ $initiative->title }}">{{ $initiative->title }}</h5>
                                                            <p class="text-xs text-slate-500 leading-relaxed">{{ $initiative->description }}</p>
                                                            <div class="flex flex-wrap items-center gap-1.5 pt-1">
                                                                @if($initiative->form_route)
                                                                    <span class="px-2 py-0.5 bg-blue-50 border border-blue-100/30 text-blue-700 rounded-lg text-[9px] font-bold tracking-wide font-mono">
                                                                        Form: {{ $initiative->form_route }}
                                                                    </span>
                                                                @else
                                                                    <span class="px-2 py-0.5 bg-slate-50 border border-slate-100 text-slate-400 rounded-lg text-[9px] font-bold tracking-wide font-mono">
                                                                        No Form Linked
                                                                    </span>
                                                                @endif
                                                                <span class="px-2 py-0.5 bg-emerald-50 border border-emerald-100/30 text-emerald-700 rounded-lg text-[9px] font-bold tracking-wide font-mono">
                                                                    {{ $initiative->accomplishment_reports_count ?? 0 }} Reports
                                                                </span>
                                                            </div>
                                                        </div>
                                                        @if(Auth::user()->isAdmin())
                                                            <div class="flex items-center space-x-1 shrink-0">
                                                                <button type="button" @click="openEditModal(@js(['id' => $initiative->id, 'title' => $initiative->title, 'description' => $initiative->description, 'form_route' => $initiative->form_route ?? '', 'custom_fields' => $initiative->custom_fields ?? []]))" class="p-2 bg-blue-50 hover:bg-blue-100 text-[#1e40af] hover:text-blue-800 rounded-xl transition active:scale-90 cursor-pointer mr-1" title="Edit Initiative" aria-label="Edit {{ $initiative->title }}">
                                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                                    </svg>
                                                                </button>
                                                                
                                                                <x-alert-dialog>
                                                                    <x-slot:trigger>
                                                                        <button type="button" class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 rounded-xl transition active:scale-90 cursor-pointer" title="Delete Initiative">
                                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                            </svg>
                                                                        </button>
                                                                    </x-slot:trigger>
                                                                
                                                                 <x-slot:icon>
                                                                     <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                                     </svg>
                                                                 </x-slot:icon>
                                                                
                                                                <x-slot:title>
                                                                    Delete Initiative
                                                                </x-slot:title>
                                                                
                                                                <x-slot:description>
                                                                    Are you sure you want to permanently delete the initiative "{{ $initiative->title }}"? This will remove the initiative and all its accomplishment reports. This action cannot be undone.
                                                                </x-slot:description>
                                                                
                                                                <x-slot:footer>
                                                                    <button type="button" @click="open = false" class="btn-outline text-xs py-2 px-4">
                                                                        Cancel
                                                                    </button>
                                                                    <form method="POST" action="{{ route('admin.structure.initiative.destroy', $initiative->id) }}" class="inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn-danger py-2 text-xs">
                                                                            Confirm Delete
                                                                        </button>
                                                                    </form>
                                                                </x-slot:footer>
                                                                </x-alert-dialog>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

            </div>

        </div>

    </div>

    {{-- Edit Initiative Modal (must stay inside x-data scope) --}}
    <div x-show="showEditInitiative"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto">
        
        <div class="fixed inset-0 bg-slate-950/45 backdrop-blur-sm transition-opacity" @click="showEditInitiative = false"></div>

        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-100 max-w-2xl w-full relative z-10 p-6 sm:p-8 space-y-6 max-h-[90vh] flex flex-col"
                 @click.stop>
                 
                 <div class="flex justify-between items-center pb-3 border-b border-slate-100 shrink-0">
                     <div>
                         <span class="text-[9px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Configure Initiative Settings</span>
                         <h3 class="text-base font-black text-slate-800 uppercase tracking-wider font-display">Edit Initiative (Project)</h3>
                     </div>
                     <button type="button" @click="showEditInitiative = false" 
                             class="text-slate-400 hover:text-slate-600 p-2 rounded-full hover:bg-slate-50 transition min-w-11 min-h-11 flex items-center justify-center"
                             aria-label="Close edit modal">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                     </button>
                 </div>

                 <form :action="`{{ url('/admin/structure/initiatives') }}/${editInitiative.id}`" method="POST" class="space-y-6 overflow-y-auto flex-1 pr-2 no-scrollbar">
                     @csrf
                     @method('PUT')

                     <div class="space-y-4">
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                             <div>
                                 <label for="edit_title" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Initiative Title</label>
                                 <input type="text" name="title" id="edit_title" required x-model="editInitiative.title" class="field text-xs py-2.5 bg-white" placeholder="e.g. Alternative Learning System">
                             </div>
                             <div>
                                 <label for="edit_form_route" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Linked Form (Optional)</label>
                                 <select name="form_route" id="edit_form_route" x-model="editInitiative.form_route" class="field text-xs py-2.5 pr-8 bg-white">
                                     <option value="">None (No Form Linked)</option>
                                     <option value="forms.health.create">Health Consultation Form</option>
                                     <option value="forms.mental-health.create">Mental Health Support Form</option>
                                     <option value="forms.medicine.create">Pabili Medicine Services Form</option>
                                     <option value="forms.silid.create">Silid Karunungan Booking Form</option>
                                     <option value="forms.sports.create">Sports League Registration Form</option>
                                 </select>
                             </div>
                         </div>

                         <div>
                             <label for="edit_description" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Description</label>
                             <textarea name="description" id="edit_description" required rows="3" x-model="editInitiative.description" class="field text-xs py-2.5 bg-white" placeholder="Provide a short overview of this program initiative..."></textarea>
                         </div>
                     </div>

                     <div class="space-y-4 pt-4 border-t border-slate-100">
                         <div class="flex items-center justify-between">
                             <div>
                                 <h4 class="text-xs font-black uppercase tracking-wider text-slate-700 font-display">Custom Form Fields</h4>
                                 <p class="text-[10px] text-slate-400">Configure custom fields to request from citizens upon submission.</p>
                             </div>
                             <button type="button" 
                                     @click="editInitiative.custom_fields.push({ label: '', name: '', type: 'text', required: false, placeholder: '' })"
                                     class="btn-success btn-sm flex items-center space-x-1">
                                 <span>➕ Add Field</span>
                             </button>
                         </div>

                         <div class="space-y-4">
                             <div x-show="editInitiative.custom_fields.length === 0" class="text-center py-6 border border-dashed border-slate-200 rounded-2xl bg-slate-50/50 text-[11px] text-slate-400">
                                 No custom fields configured. Standard fields will be used.
                             </div>

                             <template x-for="(field, index) in editInitiative.custom_fields" :key="index">
                                 <div class="p-4 bg-slate-50 border border-slate-200/60 rounded-2xl space-y-4 relative shadow-sm">
                                     <button type="button" 
                                             @click="editInitiative.custom_fields.splice(index, 1)" 
                                             class="absolute top-3 right-3 text-slate-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-white border border-transparent hover:border-slate-100 transition active:scale-90" 
                                             title="Remove Field">
                                         <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                         </svg>
                                     </button>

                                     <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mr-6">
                                         <div>
                                             <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Field Label (Citizen Visible)</label>
                                             <input type="text" 
                                                    :name="`custom_fields[${index}][label]`" 
                                                    x-model="field.label" 
                                                    required 
                                                    @input="if(!field.name) { field.name = field.label.toLowerCase().replace(/[^a-z0-9]/g, '_').substring(0,30); }"
                                                    class="field text-[11px] py-2 bg-white" 
                                                    placeholder="e.g. High School Attended">
                                         </div>
                                         <div>
                                             <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Field Key (Short alphanumeric name)</label>
                                             <input type="text" 
                                                    :name="`custom_fields[${index}][name]`" 
                                                    x-model="field.name" 
                                                    required 
                                                    @input="field.name = field.name.toLowerCase().replace(/[^a-z0-9_]/g, '_')"
                                                    class="field text-[11px] py-2 bg-white font-mono" 
                                                    placeholder="e.g. high_school_attended">
                                         </div>
                                     </div>

                                     <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                         <div>
                                             <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Field Input Type</label>
                                             <select :name="`custom_fields[${index}][type]`" x-model="field.type" class="field text-[11px] py-2 pr-8 bg-white">
                                                 <option value="text">Single Line Text</option>
                                                 <option value="textarea">Multi-line Paragraph</option>
                                                 <option value="number">Numeric Value</option>
                                                 <option value="date">Date Selector</option>
                                             </select>
                                         </div>
                                         <div>
                                             <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Input Placeholder / Hint</label>
                                             <input type="text" :name="`custom_fields[${index}][placeholder]`" x-model="field.placeholder" class="field text-[11px] py-2 bg-white" placeholder="e.g. Enter name of school...">
                                         </div>
                                         <div class="flex items-center pt-5">
                                             <label class="inline-flex items-center text-[10px] font-bold text-slate-500 uppercase tracking-wider cursor-pointer select-none">
                                                 <input type="checkbox" 
                                                        :name="`custom_fields[${index}][required]`" 
                                                        x-model="field.required" 
                                                        class="rounded border-slate-300 text-[#1e40af] focus:ring-[#1e40af]/20 mr-2 w-4 h-4">
                                                 Required Input Field
                                             </label>
                                         </div>
                                     </div>
                                 </div>
                             </template>
                         </div>
                     </div>

                     <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-100 shrink-0">
                         <button type="button" @click="showEditInitiative = false" class="btn-outline text-xs py-2.5 px-4 font-bold min-h-11">
                             Cancel
                         </button>
                         <button type="submit" class="btn-primary text-xs py-2.5 px-5 font-bold min-h-11">
                             Save Changes
                         </button>
                     </div>
                 </form>
            </div>
        </div>
    </div>

</div>
@endsection
