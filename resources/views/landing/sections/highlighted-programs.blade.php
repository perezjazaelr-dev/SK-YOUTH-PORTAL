<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 reveal-on-scroll">
    <div class="text-center mb-8">
        <span class="text-xs font-black tracking-widest text-[#1e40af] uppercase font-display">Featured Initiatives</span>
        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-800 font-display mt-1.5 uppercase">Highlighted Programs</h1>
        <p class="text-xs text-slate-400 mt-2 max-w-sm mx-auto">Explore the three most requested programs directly managed by our youth representatives.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
    </div>
</section>
