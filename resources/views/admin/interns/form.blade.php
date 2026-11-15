@php
    $inputClass = 'mt-1.5 block w-full rounded-md border border-line bg-white px-3.5 py-2.5 text-[14px] text-navy placeholder:text-[#9aa8b5] focus:border-brand focus:ring-3 focus:ring-brand/15 focus:outline-none';
    $cardClass = 'rounded-xl border border-line bg-white p-5 md:p-6';
    $labelClass = 'block text-[13px] font-semibold';
    $hintClass = 'mt-1.5 text-[12px] text-muted';
    $errorClass = 'mt-1.5 text-[12px] font-semibold text-[#b42318]';
    $fileClass = 'mt-2 block w-full text-[13px] text-muted file:mr-3 file:cursor-pointer file:rounded-md file:border-0 file:bg-[#eaf6fc] file:px-3.5 file:py-2 file:text-[13px] file:font-semibold file:text-brand hover:file:bg-[#d8eef9]';
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-5">
    @csrf
    @method($method)

    <section class="{{ $cardClass }}">
        <h2 class="text-[15px] font-bold">Data peserta</h2>
        @if ($intern->exists)
            <p class="{{ $hintClass }}">No. Sertifikat <span class="font-mono font-semibold text-navy">{{ $intern->number }}</span> sudah tercetak di sertifikat, jadi tidak berubah walaupun tanggal mulai diganti.</p>
        @else
            <p class="{{ $hintClass }}">No. Sertifikat dibuat otomatis dari urutan dan tahun tanggal mulai, misalnya <span class="font-mono font-semibold text-navy">0001{{ now()->year }}</span> untuk peserta pertama tahun {{ now()->year }}.</p>
        @endif
        <div class="mt-4 space-y-4">
            <div>
                <label for="name" class="{{ $labelClass }}">Nama lengkap</label>
                <input id="name" type="text" name="name" value="{{ old('name', $intern->name) }}" maxlength="100" required class="{{ $inputClass }}" placeholder="Nama sesuai yang dicetak di sertifikat">
                @error('name')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label for="start_date" class="{{ $labelClass }}">Tanggal mulai</label>
                    <input id="start_date" type="date" name="start_date" value="{{ old('start_date', $intern->start_date?->toDateString()) }}" required class="{{ $inputClass }}">
                    @error('start_date')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="end_date" class="{{ $labelClass }}">Tanggal selesai</label>
                    <input id="end_date" type="date" name="end_date" value="{{ old('end_date', $intern->end_date?->toDateString()) }}" required class="{{ $inputClass }}">
                    @error('end_date')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
    </section>

    <section class="{{ $cardClass }}">
        <h2 class="text-[15px] font-bold">Foto Profile</h2>
        <div class="mt-4">
            <label for="photo" class="{{ $labelClass }}">Foto Profile</label>
            @if ($intern->exists)
                <img class="mt-2 h-32 w-24 rounded-lg border border-line object-cover" src="{{ $intern->photoUrl() }}" width="96" height="128" alt="Foto saat ini">
            @endif
            <input id="photo" type="file" name="photo" accept="image/jpeg,image/png,image/webp" @required(! $intern->exists) class="{{ $fileClass }}">
            <p class="{{ $hintClass }}">JPG, PNG, atau WebP, maksimal 2 MB. Foto tegak seperti pas foto (rasio 3:4, misalnya 600 × 800 px) tampil paling pas.@if ($intern->exists) Kosongkan jika tidak ingin mengganti foto.@endif</p>
            @error('photo')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
        </div>
    </section>

    <section class="{{ $cardClass }}">
        <h2 class="text-[15px] font-bold">Sertifikat</h2>
        <p class="{{ $hintClass }}">
            @if ($intern->exists)
                Link verifikasi peserta ini: <a href="{{ $intern->verificationUrl() }}" target="_blank" rel="noopener" class="font-semibold break-all text-brand hover:underline">{{ $intern->verificationUrl() }}</a>
            @else
                Boleh dikosongkan dulu, lalu diunggah nanti lewat tombol Ubah.
            @endif
        </p>
        <div class="mt-4">
            <label for="certificate" class="{{ $labelClass }}">File sertifikat</label>
            @if ($intern->hasCertificate())
                <p class="mt-2 text-[13px]">Sertifikat saat ini: <a href="{{ route('interns.certificate.download', $intern) }}" class="font-semibold text-brand hover:underline">{{ $intern->certificateDownloadName() }}</a></p>
            @endif
            <input id="certificate" type="file" name="certificate" accept="application/pdf,image/jpeg,image/png" class="{{ $fileClass }}">
            <p class="{{ $hintClass }}">PDF, JPG, atau PNG, maksimal 5 MB.@if ($intern->hasCertificate()) Kosongkan jika tidak ingin mengganti sertifikat.@endif</p>
            @error('certificate')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
        </div>
    </section>

    <div class="flex flex-wrap items-center gap-3">
        <button type="submit" class="cursor-pointer rounded-md bg-brand px-5 py-3 text-[13px] font-bold text-white transition hover:bg-[#096b96]">Simpan</button>
        <a href="{{ route('admin.interns.index') }}" class="rounded-md px-4 py-3 text-[13px] font-semibold text-muted transition-colors hover:text-navy">Batal</a>
    </div>
</form>
