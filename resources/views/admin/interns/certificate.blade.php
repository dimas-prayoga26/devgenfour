@use('App\Models\Intern')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Sertifikat {{ $intern->number }} · {{ $intern->name }}</title>
    <link rel="icon" type="image/png" href="{{ asset('Logo/logo_icon.png') }}">
    @vite('resources/css/app.css')
    {{-- The paper size can only be set with an @page rule; Tailwind has no utility for it. --}}
    <style>@page { size: A4 landscape; margin: 0; }</style>
</head>
<body class="bg-[#e9eef2] font-sans text-navy antialiased [print-color-adjust:exact] print:bg-white">
    <div class="sticky top-0 z-10 flex flex-wrap items-center justify-between gap-3 border-b border-line bg-white px-4 py-3 print:hidden">
        <a href="{{ route('admin.interns.index') }}" class="text-[13px] font-semibold text-muted transition-colors hover:text-brand">← Kembali ke daftar magang</a>
        <div class="flex items-center gap-4">
            <p class="hidden text-[12px] text-muted md:block">Saat mencetak, pilih kertas A4 dan aktifkan "Grafis latar belakang".</p>
            <button type="button" onclick="window.print()" class="cursor-pointer rounded-md bg-brand px-4 py-2.5 text-[13px] font-bold text-white transition hover:bg-[#096b96]">Cetak / Simpan PDF</button>
        </div>
    </div>

    <div class="overflow-x-auto p-6 print:overflow-visible print:p-0">
        <main class="relative mx-auto h-[210mm] w-[297mm] overflow-hidden bg-white shadow-[0_20px_60px_#0c304524] print:shadow-none">
            <div class="absolute -top-[40mm] -left-[40mm] size-[110mm] rounded-full bg-[#eaf6fc]" aria-hidden="true"></div>
            <div class="absolute -right-[36mm] -bottom-[46mm] size-[120mm] rounded-full bg-[#eaf6fc]" aria-hidden="true"></div>
            <div class="absolute inset-y-0 left-0 w-[4mm] bg-brand" aria-hidden="true"></div>
            <div class="absolute inset-[10mm] border-2 border-navy" aria-hidden="true"></div>
            <div class="absolute inset-[12.5mm] border border-brand/40" aria-hidden="true"></div>

            <div class="relative flex h-full flex-col items-center px-[30mm] pt-[22mm] pb-[20mm] text-center">
                <img class="w-[46mm]" src="{{ asset('Logo/devgen.png') }}" width="500" height="240" alt="Devgenfour">

                <h1 class="mt-[7mm] text-[34pt] leading-none font-extrabold tracking-[-1px]">Sertifikat Magang</h1>
                <p class="mt-[2.5mm] text-[10pt] font-bold tracking-[5px] text-brand uppercase">Certificate of Internship</p>
                <p class="mt-[3mm] text-[10pt] text-muted">Nomor <span class="font-mono font-semibold text-navy">{{ $intern->number }}</span></p>

                <p class="mt-[9mm] text-[11pt] text-muted">Diberikan kepada</p>
                <p class="mt-[2mm] max-w-full border-b-2 border-brand px-[10mm] pb-[2mm] text-[30pt] leading-tight font-bold tracking-[-0.5px] text-balance">{{ $intern->name }}</p>
                <p class="mt-[6mm] max-w-[190mm] text-[11.5pt] leading-relaxed text-muted">
                    Atas partisipasi dan dedikasinya dalam menyelesaikan program magang di
                    <span class="font-bold text-navy">Devgenfour</span> pada periode
                    <span class="font-bold whitespace-nowrap text-navy">{{ Intern::formatDate($intern->start_date) }}</span> sampai
                    <span class="font-bold whitespace-nowrap text-navy">{{ Intern::formatDate($intern->end_date) }}</span>.
                </p>

                <div class="mt-auto flex w-full items-end justify-between text-left">
                    <div class="flex items-end gap-[4mm]">
                        <div class="size-[28mm] shrink-0 [&>svg]:size-full">{{ $qrCode }}</div>
                        <div class="max-w-[62mm] pb-[1mm]">
                            <p class="text-[9pt] font-bold">Pindai untuk verifikasi</p>
                            <p class="mt-[1mm] text-[7.5pt] leading-snug break-all text-muted">{{ $intern->verificationUrl() }}</p>
                        </div>
                    </div>
                    <div class="w-[64mm] text-center">
                        <p class="text-[10pt] text-muted">{{ Intern::formatDate($intern->end_date) }}</p>
                        <p class="mt-[18mm] border-t border-navy pt-[2mm] text-[11pt] font-bold">Devgenfour</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
