@extends('admin.layout')
@use('App\Models\Intern')

@section('title', 'Magang')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-[26px] font-bold tracking-[-0.8px]">Magang</h1>
            <p class="mt-1 text-[13px] text-muted">Kelola peserta magang dan cetak sertifikat dengan kode QR verifikasi.</p>
        </div>
        <a href="{{ route('admin.interns.create') }}" class="inline-flex items-center gap-2 rounded-md bg-brand px-4 py-2.5 text-[13px] font-bold text-white transition hover:bg-[#096b96]">+ Tambah magang</a>
    </div>

    @if ($interns->isEmpty())
        <div class="rounded-xl border border-dashed border-[#c6d6e1] bg-white px-6 py-14 text-center">
            <p class="font-semibold">Belum ada peserta magang.</p>
            <p class="mt-1 text-[13px] text-muted">Klik "Tambah magang" untuk menambahkan peserta pertama.</p>
        </div>
    @else
        <ul class="divide-y divide-line overflow-hidden rounded-xl border border-line bg-white">
            @foreach ($interns as $intern)
                <li class="flex flex-col gap-4 p-4 md:flex-row md:items-center md:gap-5 md:px-5">
                    <img class="size-14 shrink-0 rounded-full border border-line object-cover" src="{{ $intern->photoUrl() }}" alt="" loading="lazy">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-[15px] font-bold">{{ $intern->name }}</h2>
                            <span class="rounded-full bg-[#eaf6fc] px-2 py-0.5 font-mono text-[11px] font-semibold tracking-[0.5px] text-brand">{{ $intern->number }}</span>
                        </div>
                        <p class="mt-1 text-[13px] text-muted">{{ Intern::formatDate($intern->start_date) }} – {{ Intern::formatDate($intern->end_date) }}</p>
                    </div>
                    <div class="flex shrink-0 flex-wrap items-center gap-2">
                        <a href="{{ route('admin.interns.certificate', $intern) }}" target="_blank" rel="noopener" class="rounded-md border border-brand bg-[#eaf6fc] px-3.5 py-2 text-[13px] font-semibold text-brand transition-colors hover:bg-[#d8eef9]">Sertifikat ↗</a>
                        <a href="{{ route('admin.interns.edit', $intern) }}" class="rounded-md border border-line px-3.5 py-2 text-[13px] font-semibold transition-colors hover:border-brand hover:text-brand">Ubah</a>
                        <form method="POST" action="{{ route('admin.interns.destroy', $intern) }}" onsubmit="return confirm('Hapus peserta magang ini? Kode QR di sertifikatnya tidak akan bisa diverifikasi lagi.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="cursor-pointer rounded-md border border-line px-3.5 py-2 text-[13px] font-semibold text-[#b42318] transition-colors hover:border-[#b42318] hover:bg-[#fef3f2]">Hapus</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
@endsection
