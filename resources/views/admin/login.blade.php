@extends('admin.layout')

@section('title', 'Masuk')

@section('content')
    <div class="mx-auto mt-4 max-w-sm md:mt-14">
        <div class="mb-7 flex flex-col items-center text-center">
            <a href="{{ route('home') }}" class="block w-[150px]"><img class="w-full" src="{{ asset('Logo/devgen.png') }}" width="500" height="240" alt="Devgenfour"></a>
            <h1 class="mt-6 text-[22px] font-bold tracking-[-0.5px]">Masuk ke admin</h1>
            <p class="mt-1 text-[13px] text-muted">Kelola portofolio website Devgenfour.</p>
        </div>
        <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-4 rounded-xl border border-line bg-white p-6 shadow-[0_12px_40px_#0c304512]">
            @csrf
            <div>
                <label for="email" class="block text-[13px] font-semibold">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="mt-1.5 block w-full rounded-md border border-line bg-white px-3.5 py-2.5 text-[14px] focus:border-brand focus:ring-3 focus:ring-brand/15 focus:outline-none">
                @error('email')<p class="mt-1.5 text-[12px] font-semibold text-[#b42318]">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password" class="block text-[13px] font-semibold">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password" class="mt-1.5 block w-full rounded-md border border-line bg-white px-3.5 py-2.5 text-[14px] focus:border-brand focus:ring-3 focus:ring-brand/15 focus:outline-none">
                @error('password')<p class="mt-1.5 text-[12px] font-semibold text-[#b42318]">{{ $message }}</p>@enderror
            </div>
            <label class="flex items-center gap-2.5 text-[13px] text-muted">
                <input type="checkbox" name="remember" value="1" class="size-4 accent-brand">
                Ingat saya di perangkat ini
            </label>
            <button type="submit" class="w-full cursor-pointer rounded-md bg-brand px-4 py-3 text-[13px] font-bold text-white transition hover:bg-[#096b96]">Masuk</button>
        </form>
        <p class="mt-6 text-center"><a href="{{ route('home') }}" class="text-[13px] font-semibold text-muted transition-colors hover:text-brand">← Kembali ke website</a></p>
    </div>
@endsection
