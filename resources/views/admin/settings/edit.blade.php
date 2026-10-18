@extends('admin.layout')

@section('title', 'Pengaturan kontak')

@php
    $inputClass = 'mt-1.5 block w-full rounded-md border border-line bg-white px-3.5 py-2.5 text-[14px] text-navy placeholder:text-[#9aa8b5] focus:border-brand focus:ring-3 focus:ring-brand/15 focus:outline-none';
    $cardClass = 'rounded-xl border border-line bg-white p-5 md:p-6';
    $labelClass = 'block text-[13px] font-semibold';
    $hintClass = 'mt-1.5 text-[12px] text-muted';
    $errorClass = 'mt-1.5 text-[12px] font-semibold text-[#b42318]';
@endphp

@section('content')
    <h1 class="text-[26px] font-bold tracking-[-0.8px]">Pengaturan kontak</h1>
    <p class="mt-1 mb-6 text-[13px] text-muted">Ditampilkan di bagian Kontak dan footer website. Kosongkan kolom yang tidak ingin ditampilkan.</p>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-5">
        @csrf
        @method('PUT')

        <section class="{{ $cardClass }}">
            <h2 class="text-[15px] font-bold">Kontak</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <label for="email" class="{{ $labelClass }}">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $values['email'] ?? '') }}" maxlength="100" class="{{ $inputClass }}" placeholder="halo@devgenfour.com">
                    @error('email')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="whatsapp" class="{{ $labelClass }}">Nomor WhatsApp</label>
                    <input id="whatsapp" type="tel" name="whatsapp" value="{{ old('whatsapp', $values['whatsapp'] ?? '') }}" maxlength="20" class="{{ $inputClass }}" placeholder="0812-3456-7890">
                    <p class="{{ $hintClass }}">Boleh diawali 0 atau +62. Otomatis diubah ke format internasional untuk tombol WhatsApp.</p>
                    @error('whatsapp')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="{{ $cardClass }}">
            <h2 class="text-[15px] font-bold">Alamat & peta</h2>
            <div class="mt-4 grid gap-5 md:grid-cols-2">
                <div class="space-y-4">
                    <div>
                        <label for="address" class="{{ $labelClass }}">Alamat kantor atau kota</label>
                        <textarea id="address" name="address" rows="2" maxlength="200" class="{{ $inputClass }}" placeholder="Contoh: Indramayu, Jawa Barat">{{ old('address', $values['address'] ?? '') }}</textarea>
                        <p class="{{ $hintClass }}">Peta di footer website otomatis mencari alamat ini.</p>
                        @error('address')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="maps_embed" class="{{ $labelClass }}">Kode sematan Google Maps <span class="font-normal text-muted">(opsional)</span></label>
                        <textarea id="maps_embed" name="maps_embed" rows="3" class="{{ $inputClass }} font-mono text-[12px]" placeholder='<iframe src="https://www.google.com/maps/embed?pb=...'>{{ old('maps_embed', $values['maps_embed'] ?? '') }}</textarea>
                        <p class="{{ $hintClass }}">Agar pin tepat di lokasi kantor: buka Google Maps, cari lokasi, klik <strong>Bagikan → Sematkan peta → Salin HTML</strong>, lalu tempel di sini.</p>
                        @error('maps_embed')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <p class="{{ $labelClass }}">Pratinjau peta</p>
                    @if ($mapEmbedUrl)
                        <iframe src="{{ $mapEmbedUrl }}" title="Pratinjau peta lokasi" class="mt-1.5 block h-56 w-full rounded-lg border border-line" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    @else
                        <div class="mt-1.5 grid h-56 place-items-center rounded-lg border border-dashed border-[#c6d6e1] bg-[#f6f9fb] px-6 text-center text-[12px] text-muted">Isi alamat atau kode sematan, lalu simpan untuk melihat peta.</div>
                    @endif
                </div>
            </div>
        </section>

        <section class="{{ $cardClass }}">
            <h2 class="text-[15px] font-bold">Media sosial</h2>
            <p class="{{ $hintClass }}">Isi dengan link lengkap profil, misalnya https://instagram.com/devgenfour.</p>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                @foreach ($socialNetworks as $network => $label)
                    <div>
                        <label for="{{ $network }}" class="{{ $labelClass }}">{{ $label }}</label>
                        <div class="relative mt-1.5">
                            <svg class="pointer-events-none absolute top-1/2 left-3.5 size-4 -translate-y-1/2 text-muted" aria-hidden="true" viewBox="0 0 24 24"><use href="#icon-{{ $network }}"/></svg>
                            <input id="{{ $network }}" type="url" name="{{ $network }}" value="{{ old($network, $values[$network] ?? '') }}" maxlength="255" class="{{ str_replace('mt-1.5 ', '', $inputClass) }} pl-10" placeholder="https://">
                        </div>
                        @error($network)<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
                    </div>
                @endforeach
            </div>
        </section>

        <button type="submit" class="cursor-pointer rounded-md bg-brand px-5 py-3 text-[13px] font-bold text-white transition hover:bg-[#096b96]">Simpan pengaturan</button>
    </form>

    @include('partials.social-icons')
@endsection
