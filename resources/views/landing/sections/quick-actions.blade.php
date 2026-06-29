<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 reveal-on-scroll">
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
