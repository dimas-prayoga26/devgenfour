@use('App\Models\Intern')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#0a2844">
    <title>{{ $intern ? "Verifikasi Sertifikat {$intern->number}" : 'Sertifikat tidak ditemukan' }} · Devgenfour</title>
    <link rel="icon" type="image/png" href="{{ asset('Logo/logo_icon.png') }}">
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-[#f8fafc] font-sans text-[14px] leading-[1.6] text-navy antialiased">
    <main class="mx-auto max-w-[640px] px-4 py-10 md:py-14">
        <a href="{{ route('home') }}" class="mx-auto block w-[150px]"><img class="w-full" src="{{ asset('Logo/devgen.png') }}" width="500" height="240" alt="Devgenfour"></a>

        <section class="mt-8 rounded-2xl border border-line bg-white px-6 py-9 shadow-[0_12px_40px_#0c304510] md:px-10">
            @if ($intern)
                <div class="text-center">
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-[#bfe5d6] bg-[#e9f8f3] px-3.5 py-1 text-[12px] font-semibold text-[#1c6e52]">
                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg>
                        Terverifikasi
                    </span>
                    <h1 class="mt-4 text-[24px] leading-tight font-bold tracking-[-0.5px]">Verifikasi Sertifikat Magang</h1>
                    <p class="mt-2 text-muted">Dokumen ini telah terdaftar dan terverifikasi secara resmi oleh</p>
                    <p class="font-semibold">Devgenfour</p>
                </div>

                <p class="mt-8 flex items-center gap-4 text-[11px] font-bold tracking-[2px] whitespace-nowrap text-[#a7b4c0] uppercase"><span class="h-px flex-1 bg-line"></span>Data Magang<span class="h-px flex-1 bg-line"></span></p>

                <img class="mx-auto mt-8 h-[120px] w-[90px] rounded-lg border border-line object-cover shadow-[0_6px_18px_#0c304514]" src="{{ $intern->photoUrl() }}" width="90" height="120" alt="Foto {{ $intern->name }}">

                <dl class="mt-6 grid gap-x-6 xs:grid-cols-2 md:gap-x-8">
                    @foreach (['Nomor Magang' => $intern->number, 'Nama' => $intern->name, 'Tanggal Mulai' => Intern::formatDate($intern->start_date), 'Tanggal Selesai' => Intern::formatDate($intern->end_date)] as $label => $value)
                        <div class="border-b border-line py-4">
                            <dt class="text-[11px] font-bold tracking-[0.8px] text-[#8193a3] uppercase">{{ $label }}</dt>
                            <dd class="mt-1 text-[15px] font-medium">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>

                @if ($intern->hasCertificate())
                    <a href="{{ route('interns.certificate.download', $intern) }}" class="mt-8 flex w-full items-center justify-center gap-2 rounded-lg bg-brand px-5 py-3 font-bold text-white transition hover:bg-[#096b96]">
                        <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 4v11m-5-5 5 5 5-5M5 20h14"/></svg>
                        Download Sertifikat
                    </a>
                @endif
            @else
                <div class="text-center">
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-[#f6cfca] bg-[#fef3f2] px-3.5 py-1 text-[12px] font-semibold text-[#b42318]">
                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9.5 9.5 5 5m0-5-5 5"/></svg>
                        Tidak terverifikasi
                    </span>
                    <h1 class="mt-4 text-[24px] leading-tight font-bold tracking-[-0.5px]">Sertifikat tidak ditemukan</h1>
                    <p class="mt-2 text-muted">Kode QR ini tidak terdaftar di Devgenfour. Pastikan Anda memindai sertifikat asli.</p>
                </div>
            @endif

            <div class="mt-8 border-t border-line pt-6 text-center text-[12px] leading-relaxed text-[#8193a3]">
                <p>Keaslian dokumen ini dapat diverifikasi setiap saat melalui sistem verifikasi elektronik Devgenfour.</p>
                <p>Untuk informasi lebih lanjut, silakan <a href="{{ route('home') }}#contact" class="font-semibold text-brand hover:underline">hubungi Devgenfour</a>.</p>
            </div>
        </section>
    </main>
</body>
</html>
