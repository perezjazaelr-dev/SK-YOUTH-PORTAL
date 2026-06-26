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
                    <span class="text-[10px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Aggregated Schedule</span>
                    <h1 class="text-2xl font-black tracking-tight text-slate-800 font-display uppercase mt-1">Master Calendar</h1>
                    <p class="text-xs text-slate-500 mt-1">Unified calendar view for consultations, sports events, facility reservations, and medicine deliveries.</p>
                </div>
            </div>

            <!-- Double Grid Calendar + Filters -->
            <div class="grid grid-cols-1 xl:grid-cols-4 gap-6 items-start">
                
                <!-- Left: FullCalendar view (3 Cols) -->
                <div class="xl:col-span-3 card p-5 md:p-6 bg-white shadow-sm border border-slate-100">
                    <!-- Calendar Container -->
                    <div id="calendar" class="min-h-[500px]"></div>
                </div>

                <!-- Right: Filters & Colors (1 Col) -->
                <div class="space-y-6">
                    
                    <!-- Color legend -->
                    <div class="card p-5 space-y-4">
                        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider font-display">Legend & Categories</h3>
                        <hr class="border-slate-100">
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3">
                                <span class="w-4 h-4 rounded-md bg-[#eff6ff] border-l-[3.5px] border-l-[#3b82f6] border border-blue-200/50 block shrink-0"></span>
                                <span class="text-xs text-slate-600 font-semibold">🏥 Health Consultation</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="w-4 h-4 rounded-md bg-[#fdf2f8] border-l-[3.5px] border-l-[#ec4899] border border-pink-200/50 block shrink-0"></span>
                                <span class="text-xs text-slate-600 font-semibold">📚 Silid Karunungan Bookings</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="w-4 h-4 rounded-md bg-[#ecfdf5] border-l-[3.5px] border-l-[#10b981] border border-emerald-200/50 block shrink-0"></span>
                                <span class="text-xs text-slate-600 font-semibold">⚽ Sports Registration</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="w-4 h-4 rounded-md bg-[#f5f3ff] border-l-[3.5px] border-l-[#8b5cf6] border border-purple-200/50 block shrink-0"></span>
                                <span class="text-xs text-slate-600 font-semibold">💊 Medicine Services</span>
                            </div>
                        </div>
                    </div>

                    <!-- Client-side Interactive Filters -->
                    <div class="card p-5 space-y-4 bg-white border border-slate-100 rounded-3xl shadow-sm">
                        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider font-display">Timeline Filters</h3>
                        <hr class="border-slate-100">
                        
                        <div class="space-y-4">
                            <!-- Service filter list -->
                            <div>
                                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Service Filter</span>
                                <div class="space-y-2">
                                    <!-- Health Filter -->
                                    <label class="relative flex items-center justify-between p-2.5 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition cursor-pointer select-none overflow-hidden">
                                        <input type="checkbox" id="filter-health" checked class="sr-only peer filter-checkbox" />
                                        <div class="absolute inset-0 border border-transparent rounded-xl peer-checked:border-blue-300 peer-checked:bg-blue-50/20 transition-all pointer-events-none"></div>
                                        <div class="flex items-center space-x-2.5 relative z-10">
                                            <span class="w-2.5 h-2.5 rounded-full bg-[#3b82f6] block"></span>
                                            <span class="text-xs font-bold text-slate-650 peer-checked:text-blue-900 transition-colors">Health Consults</span>
                                        </div>
                                        <div class="w-8 h-4.5 bg-slate-200 peer-checked:bg-blue-600 rounded-full transition-all relative flex items-center z-10">
                                            <div class="w-3.5 h-3.5 bg-white rounded-full transition-transform transform translate-x-0.5 peer-checked:translate-x-4 shadow-xs"></div>
                                        </div>
                                    </label>

                                    <!-- Silid Filter -->
                                    <label class="relative flex items-center justify-between p-2.5 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition cursor-pointer select-none overflow-hidden">
                                        <input type="checkbox" id="filter-silid" checked class="sr-only peer filter-checkbox" />
                                        <div class="absolute inset-0 border border-transparent rounded-xl peer-checked:border-pink-300 peer-checked:bg-pink-50/20 transition-all pointer-events-none"></div>
                                        <div class="flex items-center space-x-2.5 relative z-10">
                                            <span class="w-2.5 h-2.5 rounded-full bg-[#ec4899] block"></span>
                                            <span class="text-xs font-bold text-slate-650 peer-checked:text-pink-900 transition-colors">Silid Karunungan</span>
                                        </div>
                                        <div class="w-8 h-4.5 bg-slate-200 peer-checked:bg-pink-600 rounded-full transition-all relative flex items-center z-10">
                                            <div class="w-3.5 h-3.5 bg-white rounded-full transition-transform transform translate-x-0.5 peer-checked:translate-x-4 shadow-xs"></div>
                                        </div>
                                    </label>

                                    <!-- Sports Filter -->
                                    <label class="relative flex items-center justify-between p-2.5 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition cursor-pointer select-none overflow-hidden">
                                        <input type="checkbox" id="filter-sports" checked class="sr-only peer filter-checkbox" />
                                        <div class="absolute inset-0 border border-transparent rounded-xl peer-checked:border-emerald-300 peer-checked:bg-emerald-50/20 transition-all pointer-events-none"></div>
                                        <div class="flex items-center space-x-2.5 relative z-10">
                                            <span class="w-2.5 h-2.5 rounded-full bg-[#10b981] block"></span>
                                            <span class="text-xs font-bold text-slate-650 peer-checked:text-emerald-900 transition-colors">Sports Leagues</span>
                                        </div>
                                        <div class="w-8 h-4.5 bg-slate-200 peer-checked:bg-emerald-600 rounded-full transition-all relative flex items-center z-10">
                                            <div class="w-3.5 h-3.5 bg-white rounded-full transition-transform transform translate-x-0.5 peer-checked:translate-x-4 shadow-xs"></div>
                                        </div>
                                    </label>

                                    <!-- Medicine Filter -->
                                    <label class="relative flex items-center justify-between p-2.5 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition cursor-pointer select-none overflow-hidden">
                                        <input type="checkbox" id="filter-medicine" checked class="sr-only peer filter-checkbox" />
                                        <div class="absolute inset-0 border border-transparent rounded-xl peer-checked:border-purple-300 peer-checked:bg-purple-50/20 transition-all pointer-events-none"></div>
                                        <div class="flex items-center space-x-2.5 relative z-10">
                                            <span class="w-2.5 h-2.5 rounded-full bg-[#8b5cf6] block"></span>
                                            <span class="text-xs font-bold text-slate-650 peer-checked:text-purple-900 transition-colors">Medicine Deliveries</span>
                                        </div>
                                        <div class="w-8 h-4.5 bg-slate-200 peer-checked:bg-purple-600 rounded-full transition-all relative flex items-center z-10">
                                            <div class="w-3.5 h-3.5 bg-white rounded-full transition-transform transform translate-x-0.5 peer-checked:translate-x-4 shadow-xs"></div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Status filter list -->
                            <div>
                                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Status Filter</span>
                                <div class="space-y-2">
                                    <!-- Pending Filter -->
                                    <label class="relative flex items-center justify-between p-2.5 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition cursor-pointer select-none overflow-hidden">
                                        <input type="checkbox" id="filter-pending" checked class="sr-only peer filter-checkbox" />
                                        <div class="absolute inset-0 border border-transparent rounded-xl peer-checked:border-amber-300 peer-checked:bg-amber-50/20 transition-all pointer-events-none"></div>
                                        <div class="flex items-center space-x-2.5 relative z-10">
                                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 block"></span>
                                            <span class="text-xs font-bold text-slate-650 peer-checked:text-amber-900 transition-colors">Pending Items</span>
                                        </div>
                                        <div class="w-8 h-4.5 bg-slate-200 peer-checked:bg-amber-500 rounded-full transition-all relative flex items-center z-10">
                                            <div class="w-3.5 h-3.5 bg-white rounded-full transition-transform transform translate-x-0.5 peer-checked:translate-x-4 shadow-xs"></div>
                                        </div>
                                    </label>

                                    <!-- Approved Filter -->
                                    <label class="relative flex items-center justify-between p-2.5 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition cursor-pointer select-none overflow-hidden">
                                        <input type="checkbox" id="filter-approved" checked class="sr-only peer filter-checkbox" />
                                        <div class="absolute inset-0 border border-transparent rounded-xl peer-checked:border-emerald-300 peer-checked:bg-emerald-50/20 transition-all pointer-events-none"></div>
                                        <div class="flex items-center space-x-2.5 relative z-10">
                                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 block"></span>
                                            <span class="text-xs font-bold text-slate-650 peer-checked:text-emerald-900 transition-colors">Approved / Confirmed</span>
                                        </div>
                                        <div class="w-8 h-4.5 bg-slate-200 peer-checked:bg-emerald-600 rounded-full transition-all relative flex items-center z-10">
                                            <div class="w-3.5 h-3.5 bg-white rounded-full transition-transform transform translate-x-0.5 peer-checked:translate-x-4 shadow-xs"></div>
                                        </div>
                                    </label>

                                    <!-- Declined Filter -->
                                    <label class="relative flex items-center justify-between p-2.5 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition cursor-pointer select-none overflow-hidden">
                                        <input type="checkbox" id="filter-declined" checked class="sr-only peer filter-checkbox" />
                                        <div class="absolute inset-0 border border-transparent rounded-xl peer-checked:border-rose-300 peer-checked:bg-rose-50/20 transition-all pointer-events-none"></div>
                                        <div class="flex items-center space-x-2.5 relative z-10">
                                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 block"></span>
                                            <span class="text-xs font-bold text-slate-650 peer-checked:text-rose-900 transition-colors">Declined Items</span>
                                        </div>
                                        <div class="w-8 h-4.5 bg-slate-200 peer-checked:bg-rose-500 rounded-full transition-all relative flex items-center z-10">
                                            <div class="w-3.5 h-3.5 bg-white rounded-full transition-transform transform translate-x-0.5 peer-checked:translate-x-4 shadow-xs"></div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<!-- Styles and Scripts for FullCalendar -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

<style>
    .fc {
        font-family: inherit;
        font-size: 0.82rem;
    }
    .fc-header-toolbar {
        margin-bottom: 1.5rem !important;
        flex-wrap: wrap;
        gap: 0.6rem;
    }
    .fc-toolbar-title {
        font-size: 1.25rem !important;
        font-weight: 850 !important;
        text-transform: uppercase !important;
        letter-spacing: -0.02em !important;
        color: #1e293b !important;
    }
    .fc-button {
        box-shadow: none !important;
    }
    .fc-button-primary {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        color: #475569 !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        font-size: 9px !important;
        letter-spacing: 0.05em !important;
        border-radius: 12px !important;
        padding: 8px 16px !important;
        transition: all 0.15s ease !important;
    }
    .fc-button-primary:hover {
        background-color: #f8fafc !important;
        color: #1e40af !important;
        border-color: #cbd5e1 !important;
    }
    .fc-button-primary:focus {
        box-shadow: 0 0 0 2px rgba(30, 64, 175, 0.15) !important;
    }
    .fc-button-primary:disabled {
        background-color: #f1f5f9 !important;
        border-color: #e2e8f0 !important;
        color: #94a3b8 !important;
        opacity: 0.7 !important;
    }
    .fc-button-active {
        background-color: #1e40af !important;
        border-color: #1e40af !important;
        color: #ffffff !important;
    }
    .fc-button-active:hover {
        background-color: #1d4ed8 !important;
        border-color: #1d4ed8 !important;
        color: #ffffff !important;
    }
    .fc-event {
        cursor: pointer;
        border-radius: 8px !important;
        padding: 4px 8px !important;
        font-weight: 700 !important;
        font-size: 11px !important;
        border: none !important;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        margin: 2px 0 !important;
    }
    .fc-event:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
    }
    
    /* Health Event: Soft Blue, Strong Left Border */
    .event-health {
        background-color: #eff6ff !important;
        color: #1d4ed8 !important;
        border-left: 3.5px solid #3b82f6 !important;
    }
    .event-health:hover {
        background-color: #dbeafe !important;
    }

    /* Silid Event: Soft Pink, Strong Left Border */
    .event-silid {
        background-color: #fdf2f8 !important;
        color: #be185d !important;
        border-left: 3.5px solid #ec4899 !important;
    }
    .event-silid:hover {
        background-color: #fce7f3 !important;
    }

    /* Sports Event: Soft Green, Strong Left Border */
    .event-sports {
        background-color: #ecfdf5 !important;
        color: #047857 !important;
        border-left: 3.5px solid #10b981 !important;
    }
    .event-sports:hover {
        background-color: #d1fae5 !important;
    }

    /* Medicine Event: Soft Purple, Strong Left Border */
    .event-medicine {
        background-color: #f5f3ff !important;
        color: #6d28d9 !important;
        border-left: 3.5px solid #8b5cf6 !important;
    }
    .event-medicine:hover {
        background-color: #ede9fe !important;
    }

    /* Event status tags styling */
    .status-declined {
        opacity: 0.65;
        text-decoration: line-through;
    }
    .status-pending {
        border-left-style: dashed !important;
    }

    .fc-theme-standard td, .fc-theme-standard th {
        border-color: #f1f5f9 !important;
    }
    .fc-col-header-cell {
        background-color: #f8fafc;
        padding: 10px 0 !important;
        text-transform: uppercase;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.08em;
        color: #64748b;
        border-bottom: 2px solid #e2e8f0 !important;
    }
    .fc-daygrid-day:hover {
        background-color: #f8fafc;
    }
    .fc-daygrid-day-number {
        font-weight: 700 !important;
        color: #475569 !important;
        font-size: 11px !important;
        padding: 8px !important;
    }
    .fc-day-today {
        background-color: rgba(239, 246, 255, 0.4) !important;
    }
    .fc-daygrid-event-dot {
        display: none !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            editable: false,
            selectable: false,
            events: function(info, successCallback, failureCallback) {
                var url = new URL('{{ route("dashboard.calendar.events") }}', window.location.origin);
                url.searchParams.set('start', info.startStr);
                url.searchParams.set('end', info.endStr);

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        // Apply client-side filters
                        const filtered = data.filter(event => {
                            const typeChecked = document.getElementById('filter-' + event.extendedProps.type)?.checked ?? true;
                            const statusChecked = document.getElementById('filter-' + event.extendedProps.status)?.checked ?? true;
                            return typeChecked && statusChecked;
                        });
                        successCallback(filtered);
                    })
                    .catch(error => {
                        console.error('Error fetching calendar events:', error);
                        failureCallback(error);
                    });
            },
            eventDidMount: function(info) {
                var type = info.event.extendedProps.type;
                var status = info.event.extendedProps.status;

                // Strip default solid colors returned from Laravel backend JSON
                info.el.style.backgroundColor = '';
                info.el.style.borderColor = '';
                info.el.style.color = '';

                // Add classes for styling via modern CSS rules
                info.el.classList.add('event-' + type);
                info.el.classList.add('status-' + status);
            },
            eventClick: function(info) {
                if (info.event.url) {
                    info.jsEvent.preventDefault();
                    window.location.href = info.event.url;
                }
            }
        });

        calendar.render();

        // Listen for filter checkbox changes to reload events dynamically
        document.querySelectorAll('.filter-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                calendar.refetchEvents();
            });
        });
    });
</script>
@endsection
