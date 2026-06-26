@php $post = $post ?? null; @endphp

<div class="space-y-1">
    <label for="title" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Document Title</label>
    <input id="title" type="text" name="title" required value="{{ old('title', $post?->title) }}" placeholder="FY 2025 SK Budget Appropriation" class="field text-xs min-h-11">
    @error('title')<span class="text-rose-600 text-[10px] font-semibold">{{ $message }}</span>@enderror
</div>

<div class="space-y-1">
    <label for="category" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Category</label>
    <select id="category" name="category" required class="field text-xs min-h-11">
        @foreach($categories as $key => $label)
            <option value="{{ $key }}" {{ old('category', $post?->category) === $key ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    @error('category')<span class="text-rose-600 text-[10px] font-semibold">{{ $message }}</span>@enderror
</div>

<div class="space-y-1">
    <label for="excerpt" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Short Summary</label>
    <textarea id="excerpt" name="excerpt" required rows="2" placeholder="Brief description shown on the transparency board cards..." class="field text-xs">{{ old('excerpt', $post?->excerpt) }}</textarea>
    @error('excerpt')<span class="text-rose-600 text-[10px] font-semibold">{{ $message }}</span>@enderror
</div>

<div class="space-y-1">
    <label for="content" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Full Content (optional)</label>
    <textarea id="content" name="content" rows="8" placeholder="Detailed disclosure text..." class="field text-xs">{{ old('content', $post?->content) }}</textarea>
    @error('content')<span class="text-rose-600 text-[10px] font-semibold">{{ $message }}</span>@enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="space-y-1">
        <label for="image" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Cover Image (optional)</label>
        @if($post?->imageUrl())
            <img src="{{ $post->imageUrl() }}" alt="" class="w-full max-w-xs rounded-xl border border-slate-100 mb-2">
        @endif
        <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp" class="field text-xs min-h-11">
        @error('image')<span class="text-rose-600 text-[10px] font-semibold">{{ $message }}</span>@enderror
    </div>
    <div class="space-y-1">
        <label for="document" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Attach File (PDF/DOC, optional)</label>
        @if($post?->fileUrl())
            <a href="{{ $post->fileUrl() }}" target="_blank" class="text-xs text-[#1e40af] font-bold block mb-2">Current file attached ↗</a>
        @endif
        <input id="document" type="file" name="document" accept=".pdf,.doc,.docx,.xls,.xlsx,image/*" class="field text-xs min-h-11">
        <span class="text-[9px] text-slate-400">Max 8MB. PDF, Word, Excel, or image.</span>
        @error('document')<span class="text-rose-600 text-[10px] font-semibold">{{ $message }}</span>@enderror
    </div>
</div>

<div class="flex items-start gap-3 pt-2">
    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $post?->is_active ?? true) ? 'checked' : '' }} class="w-4 h-4 mt-1 rounded border-slate-300 text-blue-600">
    <label for="is_active" class="text-xs font-bold text-slate-700 uppercase tracking-wider cursor-pointer">Publish on public transparency board</label>
</div>
