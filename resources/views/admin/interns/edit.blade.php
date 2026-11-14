@extends('admin.layout')

@section('title', 'Ubah magang')

@section('content')
    <a href="{{ route('admin.interns.index') }}" class="text-[13px] font-semibold text-muted transition-colors hover:text-brand">← Kembali ke daftar</a>
    <h1 class="mt-3 mb-6 text-[26px] font-bold tracking-[-0.8px]">Ubah magang</h1>
    @include('admin.interns.form', ['intern' => $intern, 'action' => route('admin.interns.update', $intern), 'method' => 'PUT'])
@endsection
