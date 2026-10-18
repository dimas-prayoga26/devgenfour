@extends('admin.layout')

@section('title', 'Portofolio')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-[26px] font-bold tracking-[-0.8px]">Portofolio</h1>
            <p class="mt-1 text-[13px] text-muted">Kelola proyek yang tampil di bagian Portofolio website.</p>
        </div>
        <a href="{{ route('admin.projects.create') }}" class="inline-flex items-center gap-2 rounded-md bg-brand px-4 py-2.5 text-[13px] font-bold text-white transition hover:bg-[#096b96]">+ Tambah portofolio</a>
    </div>

    @if ($projects->isEmpty())
        <div class="rounded-xl border border-dashed border-[#c6d6e1] bg-white px-6 py-14 text-center">
            <p class="font-semibold">Belum ada portofolio.</p>
            <p class="mt-1 text-[13px] text-muted">Klik "Tambah portofolio" untuk menambahkan proyek pertama.</p>
        </div>
    @else
        <ul class="divide-y divide-line overflow-hidden rounded-xl border border-line bg-white">
            @foreach ($projects as $project)
                <li class="flex flex-col gap-4 p-4 md:flex-row md:items-center md:gap-5 md:px-5">
                    <div class="aspect-[3/2] w-full shrink-0 overflow-hidden rounded-lg md:w-32 {{ $project->tone->classes() }}"><img class="size-full object-contain p-1.5" src="{{ $project->imageUrl() }}" alt="" loading="lazy"></div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="grid size-7 shrink-0 place-items-center rounded-md {{ $project->tone->classes() }}" title="Ikon: {{ $project->icon }}">{{ $project->iconSvg('size-4 stroke-[1.6]') }}</span>
                            <h2 class="text-[15px] font-bold">{{ $project->name }}</h2>
                            @if ($project->is_published)
                                <span class="rounded-full bg-[#e9f8f3] px-2 py-0.5 text-[11px] font-semibold text-[#1c6e52]">Tampil</span>
                            @else
                                <span class="rounded-full bg-[#f1f5f8] px-2 py-0.5 text-[11px] font-semibold text-muted">Disembunyikan</span>
                            @endif
                        </div>
                        <p class="mt-1 text-[13px] text-muted">{{ $project->category }}</p>
                        <p class="mt-0.5 line-clamp-1 text-[12px] text-[#8193a3]">{{ $project->tags->pluck('name')->implode(', ') }}</p>
                    </div>
                    <div class="flex shrink-0 items-center gap-2">
                        <a href="{{ route('admin.projects.edit', $project) }}" class="rounded-md border border-line px-3.5 py-2 text-[13px] font-semibold transition-colors hover:border-brand hover:text-brand">Ubah</a>
                        <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" onsubmit="return confirm('Hapus portofolio ini? Tindakan ini tidak bisa dibatalkan.')">
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
