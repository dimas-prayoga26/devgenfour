@use('App\Models\Intern')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#102f4c">
    <title>{{ $intern ? "Verifikasi Sertifikat {$intern->number}" : 'Sertifikat tidak ditemukan' }} · Devgenfour</title>
    <link rel="icon" type="image/png" href="{{ asset('Logo/logo_icon.png') }}">
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-[#f6f9fb] font-sans text-[14px] leading-[1.6] text-navy antialiased">
    <header class="relative overflow-hidden bg-navy px-4 pt-8 pb-28 text-center text-white">
        <div class="absolute -top-24 -left-20 size-72 rounded-full bg-white/5" aria-hidden="true"></div>
        <div class="absolute -right-16 -bottom-36 size-80 rounded-full bg-brand/30" aria-hidden="true"></div>
        <a href="{{ route('home') }}" class="relative inline-flex rounded-xl bg-white px-4 py-2 shadow-[0_8px_24px_#0000002e]"><img class="w-[118px]" src="{{ asset('Logo/devgen.png') }}" width="500" height="240" alt="Devgenfour"></a>
        <p class="relative mt-6 text-[11px] font-bold tracking-[3px] text-[#8fd3f2] uppercase">Sistem Verifikasi Devgenfour</p>
        <h1 class="relative mt-1.5 text-[24px] leading-tight font-bold tracking-[-0.5px] md:text-[28px]">Verifikasi Sertifikat Magang</h1>
    </header>

    <main class="relative mx-auto -mt-20 max-w-xl px-4 pb-12">
        <section class="overflow-hidden rounded-2xl border border-line bg-white shadow-[0_20px_50px_#0c304517]">
            @if ($intern)
                <div class="flex items-center gap-3 border-b border-line bg-[#f2fbf7] px-5 py-3.5 md:px-6">
                    <span class="grid size-9 shrink-0 place-items-center rounded-full bg-[#1c9a6c] text-white">
                        <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg>
                    </span>
                    <div>
                        <p class="font-bold text-[#1c6e52]">Terverifikasi</p>
                        <p class="text-[12px] leading-snug text-[#3d7f66]">Sertifikat ini asli dan terdaftar resmi di Devgenfour.</p>
                    </div>
                </div>

                <div class="p-5 md:p-6">
                    <div class="flex items-center gap-4 md:gap-5">
                        <img class="h-32 w-24 shrink-0 rounded-xl border border-line object-cover shadow-[0_8px_20px_#0c30451a]" src="{{ $intern->photoUrl() }}" width="96" height="128" alt="Foto {{ $intern->name }}">
                        <div class="min-w-0">
                            <p class="text-[12px] font-semibold text-brand">Peserta Magang</p>
                            <h2 class="mt-0.5 text-[20px] leading-tight font-bold tracking-[-0.4px] break-words md:text-[22px]">{{ $intern->name }}</h2>
                            <p class="mt-3 inline-flex flex-wrap items-center gap-x-2 rounded-lg bg-[#eaf6fc] px-3 py-1.5">
                                <span class="text-[11px] font-semibold text-muted">No. Sertifikat</span>
                                <span class="font-mono text-[14px] font-bold tracking-[1px] text-brand">{{ $intern->number }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 rounded-xl border border-line bg-[#f8fafc] p-4">
                        <p class="text-[12px] font-semibold text-muted">Periode Magang</p>
                        <div class="mt-3 grid grid-cols-[1fr_auto_1fr] items-center gap-3">
                            <div>
                                <p class="text-[11px] text-muted">Tanggal Mulai</p>
                                <p class="font-bold">{{ Intern::formatDate($intern->start_date) }}</p>
                            </div>
                            <svg class="size-5 text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12h15m-6-6 6 6-6 6"/></svg>
                            <div class="text-right">
                                <p class="text-[11px] text-muted">Tanggal Selesai</p>
                                <p class="font-bold">{{ Intern::formatDate($intern->end_date) }}</p>
                            </div>
                        </div>
                        <p class="mt-3 border-t border-dashed border-line pt-3 text-[12px] text-muted">Durasi <span class="font-semibold text-navy">{{ $intern->durationLabel() }}</span></p>
                    </div>

                    @if ($intern->hasCertificate())
                        <a href="{{ route('interns.certificate.download', $intern) }}" class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-brand px-5 py-3 font-bold text-white transition hover:bg-[#096b96]">
                            <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 4v11m-5-5 5 5 5-5M5 20h14"/></svg>
                            Download Sertifikat
                        </a>
                    @endif
                </div>
            @else
                <div class="flex items-center gap-3 border-b border-line bg-[#fef3f2] px-5 py-3.5 md:px-6">
                    <span class="grid size-9 shrink-0 place-items-center rounded-full bg-[#d92d20] text-white">
                        <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </span>
                    <div>
                        <p class="font-bold text-[#b42318]">Tidak terverifikasi</p>
                        <p class="text-[12px] leading-snug text-[#b54a3f]">Kode QR ini tidak terdaftar di Devgenfour.</p>
                    </div>
                </div>
                <div class="p-5 md:p-6">
                    <h2 class="text-[20px] leading-tight font-bold tracking-[-0.4px]">Sertifikat tidak ditemukan</h2>
                    <p class="mt-2 text-muted">Pastikan Anda memindai QR code pada sertifikat asli. Jika ragu, hubungi kami untuk memastikan keasliannya.</p>
                </div>
            @endif
        </section>

        <p class="mt-6 flex items-start justify-center gap-2 text-center text-[12px] text-muted">
            <svg class="mt-0.5 size-3.5 shrink-0 text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/></svg>
            <span>Halaman verifikasi resmi Devgenfour. Ada pertanyaan? <a href="{{ route('home') }}#contact" class="font-semibold text-brand hover:underline">Hubungi kami</a>.</span>
        </p>
    </main>
</body>
</html>
