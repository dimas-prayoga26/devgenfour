@extends('admin.layout')

@section('title', 'Tambah portofolio')

@section('content')
    <a href="{{ route('admin.projects.index') }}" class="text-[13px] font-semibold text-muted transition-colors hover:text-brand">← Kembali ke daftar</a>
    <h1 class="mt-3 mb-6 text-[26px] font-bold tracking-[-0.8px]">Tambah portofolio</h1>
    @include('admin.projects.form', ['project' => $project, 'action' => route('admin.projects.store'), 'method' => 'POST'])
@endsection
