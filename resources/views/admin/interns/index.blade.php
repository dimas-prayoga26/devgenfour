@extends('admin.layout')
@use('App\Models\Intern')

@section('title', 'Magang')

@section('content')
    @php
        $actionClass = 'rounded-md border border-line px-2.5 py-1.5 text-[12px] font-semibold whitespace-nowrap transition-colors hover:border-brand hover:text-brand';
    @endphp

    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-[26px] font-bold tracking-[-0.8px]">Magang</h1>
            <p class="mt-1 text-[13px] text-muted">Kelola peserta magang dan unggah sertifikatnya agar bisa diunduh dari halaman verifikasi.</p>
        </div>
        <a href="{{ route('admin.interns.create') }}" class="inline-flex items-center gap-2 rounded-md bg-brand px-4 py-2.5 text-[13px] font-bold text-white transition hover:bg-[#096b96]">+ Tambah magang</a>
    </div>

    @if ($interns->isEmpty())
        <div class="rounded-xl border border-dashed border-[#c6d6e1] bg-white px-6 py-14 text-center">
            <p class="font-semibold">Belum ada peserta magang.</p>
            <p class="mt-1 text-[13px] text-muted">Klik "Tambah magang" untuk menambahkan peserta pertama.</p>
        </div>
    @else
        <div class="overflow-x-auto rounded-xl border border-line bg-white">
            <table class="w-full min-w-[920px] text-left text-[13px]">
                <thead class="border-b border-line bg-[#f6f9fb] text-[12px] text-muted">
                    <tr>
                        <th scope="col" class="px-4 py-3 font-semibold">Foto Profile</th>
                        <th scope="col" class="px-4 py-3 font-semibold">Nama</th>
                        <th scope="col" class="px-4 py-3 font-semibold">Nomor Magang</th>
                        <th scope="col" class="px-4 py-3 font-semibold">Tanggal Mulai</th>
                        <th scope="col" class="px-4 py-3 font-semibold">Tanggal Selesai</th>
                        <th scope="col" class="px-4 py-3 font-semibold">Sertifikat</th>
                        <th scope="col" class="px-4 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @foreach ($interns as $intern)
                        <tr class="align-middle">
                            <td class="px-4 py-3"><img class="h-12 w-9 rounded-md border border-line object-cover" src="{{ $intern->photoUrl() }}" width="36" height="48" alt="Foto {{ $intern->name }}" loading="lazy"></td>
                            <td class="px-4 py-3 font-semibold">{{ $intern->name }}</td>
                            <td class="px-4 py-3"><span class="rounded-full bg-[#eaf6fc] px-2 py-0.5 font-mono text-[12px] font-semibold tracking-[0.5px] text-brand">{{ $intern->number }}</span></td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ Intern::formatDate($intern->start_date) }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ Intern::formatDate($intern->end_date) }}</td>
                            <td class="px-4 py-3">
                                @if ($intern->hasCertificate())
                                    <a href="{{ route('interns.certificate.download', $intern) }}" class="rounded-full bg-[#e9f8f3] px-2 py-0.5 text-[12px] font-semibold whitespace-nowrap text-[#1c6e52] hover:underline">Sudah diunggah</a>
                                @else
                                    <span class="rounded-full bg-[#f1f5f8] px-2 py-0.5 text-[12px] font-semibold whitespace-nowrap text-muted">Belum ada</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('interns.verify', $intern) }}" target="_blank" rel="noopener" class="{{ $actionClass }}">Detail ↗</a>
                                    <a href="{{ route('admin.interns.edit', $intern) }}" class="{{ $actionClass }}">Ubah</a>
                                    <form method="POST" action="{{ route('admin.interns.destroy', $intern) }}" onsubmit="return confirm('Hapus peserta magang ini? Kode QR di sertifikatnya tidak akan bisa diverifikasi lagi.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="cursor-pointer rounded-md border border-line px-2.5 py-1.5 text-[12px] font-semibold text-[#b42318] transition-colors hover:border-[#b42318] hover:bg-[#fef3f2]">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
