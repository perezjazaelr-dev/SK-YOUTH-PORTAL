@php $official = $official ?? null; @endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="space-y-1">
        <label for="name" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Full Name</label>
        <input id="name" type="text" name="name" required value="{{ old('name', $official?->name) }}" placeholder="Juan Dela Cruz" class="field text-xs min-h-11">
        @error('name')<span class="text-rose-600 text-[10px] font-semibold">{{ $message }}</span>@enderror
    </div>
    <div class="space-y-1">
        <label for="position" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Position / Title</label>
        <input id="position" type="text" name="position" required value="{{ old('position', $official?->position) }}" placeholder="SK Chairperson" class="field text-xs min-h-11">
        @error('position')<span class="text-rose-600 text-[10px] font-semibold">{{ $message }}</span>@enderror
    </div>
</div>

<div class="space-y-1.5">
    <label for="photo" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Official Photo {{ $official ? '(optional — leave blank to keep current)' : '' }}</label>
    <x-file-upload name="photo" id="photo" required="{{ $official ? 'false' : 'true' }}" accept="image/jpeg,image/png,image/webp" placeholder="Drag photo here or click to browse." existing-url="{{ $official?->photoUrl() }}" />
    @error('photo')<span class="text-rose-600 text-[10px] font-semibold">{{ $message }}</span>@enderror
</div>

<div class="space-y-1">
    <label for="bio" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Biography / About</label>
    <textarea id="bio" name="bio" rows="5" placeholder="Brief background, advocacy, and role description..." class="field text-xs">{{ old('bio', $official?->bio) }}</textarea>
    @error('bio')<span class="text-rose-600 text-[10px] font-semibold">{{ $message }}</span>@enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="space-y-1">
        <label for="email" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Email (optional)</label>
        <input id="email" type="email" name="email" value="{{ old('email', $official?->email) }}" class="field text-xs min-h-11">
        @error('email')<span class="text-rose-600 text-[10px] font-semibold">{{ $message }}</span>@enderror
    </div>
    <div class="space-y-1">
        <label for="contact_number" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Contact Number (optional)</label>
        <input id="contact_number" type="text" name="contact_number" value="{{ old('contact_number', $official?->contact_number) }}" class="field text-xs min-h-11">
        @error('contact_number')<span class="text-rose-600 text-[10px] font-semibold">{{ $message }}</span>@enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="space-y-1">
        <label for="term" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Term of Office (optional)</label>
        <input id="term" type="text" name="term" value="{{ old('term', $official?->term) }}" placeholder="2023 – 2026" class="field text-xs min-h-11">
        @error('term')<span class="text-rose-600 text-[10px] font-semibold">{{ $message }}</span>@enderror
    </div>
    <div class="space-y-1">
        <label for="sort_order" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Display Order</label>
        <input id="sort_order" type="number" name="sort_order" min="0" value="{{ old('sort_order', $official?->sort_order ?? 0) }}" class="field text-xs min-h-11">
        <span class="text-[9px] text-slate-400">Lower numbers appear first (Chairperson = 1).</span>
        @error('sort_order')<span class="text-rose-600 text-[10px] font-semibold">{{ $message }}</span>@enderror
    </div>
</div>

<div class="flex items-start gap-3 pt-2">
    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $official?->is_active ?? true) ? 'checked' : '' }} class="w-4 h-4 mt-1 rounded border-slate-300 text-blue-600">
    <label for="is_active" class="text-xs font-bold text-slate-700 uppercase tracking-wider cursor-pointer">Show on public officials page</label>
</div>
