@use('App\Enums\ProjectTone')
@php
    $inputClass = 'mt-1.5 block w-full rounded-md border border-line bg-white px-3.5 py-2.5 text-[14px] text-navy placeholder:text-[#9aa8b5] focus:border-brand focus:ring-3 focus:ring-brand/15 focus:outline-none';
    $cardClass = 'rounded-xl border border-line bg-white p-5 md:p-6';
    $labelClass = 'block text-[13px] font-semibold';
    $hintClass = 'mt-1.5 text-[12px] text-muted';
    $errorClass = 'mt-1.5 text-[12px] font-semibold text-[#b42318]';
    $selectedTags = (array) old('tags', $project->exists ? $project->tags->pluck('name')->all() : []);
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-5">
    @csrf
    @method($method)

    <section class="{{ $cardClass }}">
        <h2 class="text-[15px] font-bold">Informasi proyek</h2>
        <div class="mt-4">
            <label for="name" class="{{ $labelClass }}">Nama proyek</label>
            <input id="name" type="text" name="name" value="{{ old('name', $project->name) }}" maxlength="100" required class="{{ $inputClass }}" placeholder="Contoh: Nusa Commerce">
            @error('name')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
        </div>
    </section>

    <section class="{{ $cardClass }}">
        <h2 class="text-[15px] font-bold">Konten</h2>
        <p class="{{ $hintClass }}">
            @if ($canTranslate)
                Tulis dalam Bahasa Indonesia. Versi Bahasa Inggris dibuat otomatis saat disimpan.
            @else
                Tulis dalam Bahasa Indonesia. Isi DEEPL_API_KEY di file .env agar versi Bahasa Inggris dibuat otomatis.
            @endif
        </p>
        <div class="mt-4 space-y-4">
            <div>
                <label for="category" class="{{ $labelClass }}">Kategori</label>
                <input id="category" type="text" name="category" value="{{ old('category', $project->category) }}" maxlength="60" required class="{{ $inputClass }}" placeholder="Contoh: Toko Online">
                <p class="{{ $hintClass }}">Ikon dipilih otomatis dan langsung berganti mengikuti kategori yang diketik.</p>
                <p id="icon-preview" data-url="{{ route('admin.project-icon-preview.show') }}" data-saved-category="{{ $project->category }}" class="mt-2 flex items-center gap-2 text-[12px] text-muted {{ $project->icon_svg ? '' : 'hidden' }}" aria-live="polite">
                    Ikon: <span data-icon-box class="grid size-8 place-items-center rounded-md bg-[#eaf6fc] text-brand transition-opacity">{{ $project->iconSvg('size-4.5 stroke-[1.6]') }}</span><code data-icon-name class="text-navy">{{ $project->icon }}</code>
                </p>
                @error('category')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="description" class="{{ $labelClass }}">Deskripsi</label>
                <textarea id="description" name="description" rows="4" maxlength="500" required class="{{ $inputClass }}">{{ old('description', $project->description) }}</textarea>
                @error('description')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="tags" class="{{ $labelClass }}">Tag fitur utama</label>
                <div data-tag-input data-max="5" data-suggestions="{{ json_encode($tagSuggestions, JSON_UNESCAPED_UNICODE) }}">
                    <input id="tags" type="text" name="tags" value="{{ implode(', ', $selectedTags) }}" required data-tag-source class="{{ $inputClass }}" placeholder="Ketik tag, lalu tekan Enter">
                </div>
                <p class="{{ $hintClass }}">Pilih tag yang sudah ada atau ketik tag baru lalu tekan Enter. Maksimal 5 tag.</p>
                @error('tags')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
                @error('tags.*')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
            </div>
        </div>
    </section>

    <section class="{{ $cardClass }}">
        <h2 class="text-[15px] font-bold">Gambar & tampilan</h2>
        <div class="mt-4 space-y-5">
            <div>
                <label for="image" class="{{ $labelClass }}">Gambar</label>
                @if ($project->exists)
                    <div class="mt-2 aspect-[3/2] w-full max-w-xs overflow-hidden rounded-lg {{ $project->tone->classes() }}"><img class="size-full object-contain p-2" src="{{ $project->imageUrl() }}" alt="Gambar saat ini"></div>
                @endif
                <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp" @required(! $project->exists) class="mt-2 block w-full text-[13px] text-muted file:mr-3 file:cursor-pointer file:rounded-md file:border-0 file:bg-[#eaf6fc] file:px-3.5 file:py-2 file:text-[13px] file:font-semibold file:text-brand hover:file:bg-[#d8eef9]">
                <p class="{{ $hintClass }}">JPG, PNG, atau WebP, maksimal 2 MB. Rasio 3:2 (misalnya 1200 × 800 px) tampil paling pas.@if ($project->exists) Kosongkan jika tidak ingin mengganti gambar.@endif</p>
                @error('image')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
            </div>
            <div>
                <fieldset>
                    <legend class="{{ $labelClass }}">Warna latar</legend>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach (ProjectTone::cases() as $tone)
                            <label class="cursor-pointer">
                                <input type="radio" name="tone" value="{{ $tone->value }}" class="peer sr-only" @checked(old('tone', $project->tone?->value) === $tone->value) required>
                                <span class="flex items-center gap-2 rounded-md border border-line px-3 py-2 text-[13px] font-semibold transition peer-checked:border-brand peer-checked:ring-3 peer-checked:ring-brand/15 peer-focus-visible:border-brand"><span class="size-4 rounded-full border border-current {{ $tone->classes() }}"></span>{{ $tone->label() }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('tone')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
                </fieldset>
            </div>
        </div>
    </section>

    <section class="{{ $cardClass }}">
        <h2 class="text-[15px] font-bold">Status</h2>
        <div class="mt-4 space-y-4">
            <label class="flex items-start gap-3">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $project->is_published)) class="mt-1 size-4 accent-brand">
                <span>
                    <span class="block font-semibold">Tampilkan di website</span>
                    <span class="block text-[12px] text-muted">Matikan untuk menyimpannya sebagai draf tanpa menghapus.</span>
                </span>
            </label>
        </div>
    </section>

    <div class="flex flex-wrap items-center gap-3">
        <button type="submit" class="cursor-pointer rounded-md bg-brand px-5 py-3 text-[13px] font-bold text-white transition hover:bg-[#096b96]">Simpan</button>
        <a href="{{ route('admin.projects.index') }}" class="rounded-md px-4 py-3 text-[13px] font-semibold text-muted transition-colors hover:text-navy">Batal</a>
    </div>
</form>
