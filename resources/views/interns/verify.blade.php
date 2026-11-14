@use('App\Models\Intern')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#0a2844">
    <title>{{ $intern ? "Verifikasi sertifikat {$intern->number}" : 'Sertifikat tidak ditemukan' }} · Devgenfour</title>
    <link rel="icon" type="image/png" href="{{ asset('Logo/logo_icon.png') }}">
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-[#f6f9fb] font-sans text-[14px] leading-[1.6] text-navy antialiased">
    <main class="mx-auto flex min-h-screen max-w-md flex-col px-4 py-8 md:py-14">
        <a href="{{ route('home') }}" class="mx-auto block w-[140px]"><img class="w-full" src="{{ asset('Logo/devgen.png') }}" width="500" height="240" alt="Devgenfour"></a>

        @if ($intern)
            <section class="mt-8 overflow-hidden rounded-2xl border border-line bg-white shadow-[0_12px_40px_#0c304512]">
                <p class="flex items-center justify-center gap-2 bg-[#e9f8f3] px-4 py-3 text-[13px] font-bold text-[#1c6e52]">
                    <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg>
                    Sertifikat terverifikasi
                </p>
                <div class="flex flex-col items-center px-6 pt-7 text-center">
                    <img class="size-32 rounded-full border-4 border-white object-cover shadow-[0_8px_24px_#0c30451f] ring-1 ring-line" src="{{ $intern->photoUrl() }}" width="128" height="128" alt="Foto {{ $intern->name }}">
                    <h1 class="mt-4 text-[22px] leading-tight font-bold tracking-[-0.5px]">{{ $intern->name }}</h1>
                    <p class="mt-1 text-[13px] text-muted">Peserta magang Devgenfour</p>
                </div>
                <dl class="m-6 divide-y divide-line rounded-xl border border-line text-[13px]">
                    <div class="flex items-center justify-between gap-4 px-4 py-3">
                        <dt class="text-muted">Nomor magang</dt>
                        <dd class="font-mono font-semibold tracking-[0.5px]">{{ $intern->number }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 px-4 py-3">
                        <dt class="text-muted">Tanggal mulai</dt>
                        <dd class="font-semibold">{{ Intern::formatDate($intern->start_date) }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 px-4 py-3">
                        <dt class="text-muted">Tanggal selesai</dt>
                        <dd class="font-semibold">{{ Intern::formatDate($intern->end_date) }}</dd>
                    </div>
                </dl>
            </section>
            <p class="mt-5 text-center text-[12px] text-muted">Data ini diterbitkan oleh Devgenfour dan sesuai dengan sertifikat yang dipindai.</p>
        @else
            <section class="mt-8 rounded-2xl border border-line bg-white p-8 text-center shadow-[0_12px_40px_#0c304512]">
                <span class="mx-auto grid size-14 place-items-center rounded-full bg-[#fef3f2] text-[#b42318]">
                    <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </span>
                <h1 class="mt-4 text-[20px] font-bold tracking-[-0.5px]">Sertifikat tidak ditemukan</h1>
                <p class="mt-2 text-[13px] text-muted">Kode QR ini tidak terdaftar di Devgenfour. Pastikan Anda memindai sertifikat asli, atau hubungi kami untuk memastikannya.</p>
            </section>
        @endif

        <p class="mt-auto pt-8 text-center"><a href="{{ route('home') }}" class="text-[13px] font-semibold text-muted transition-colors hover:text-brand">← Kunjungi website Devgenfour</a></p>
    </main>
</body>
</html>
