<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title') · Admin Devgenfour</title>
    <link rel="icon" type="image/png" href="{{ asset('Logo/logo_icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
</head>
<body class="min-h-screen bg-[#f6f9fb] font-sans text-[14px] leading-[1.6] text-navy antialiased">
    @auth
        <header class="border-b border-line bg-white">
            <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4 md:px-8">
                <div class="flex items-center gap-3 md:gap-6">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.projects.index') }}" class="block w-[118px]"><img class="w-full" src="{{ asset('Logo/devgen.png') }}" width="500" height="240" alt="Devgenfour"></a>
                        <span class="hidden rounded-full bg-[#eaf6fc] px-2.5 py-0.5 text-[10px] font-extrabold tracking-[1px] text-brand md:inline">ADMIN</span>
                    </div>
                    <nav class="flex items-center gap-1" aria-label="Menu admin">
                        @foreach (['admin.projects.index' => ['Portofolio', 'admin.projects.*'], 'admin.interns.index' => ['Magang', 'admin.interns.*'], 'admin.settings.edit' => ['Pengaturan', 'admin.settings.*']] as $route => [$label, $pattern])
                            <a href="{{ route($route) }}" @if (request()->routeIs($pattern)) aria-current="page" @endif class="rounded-md px-3 py-2 text-[13px] font-semibold text-muted transition-colors hover:text-navy aria-[current=page]:bg-[#eaf6fc] aria-[current=page]:text-brand">{{ $label }}</a>
                        @endforeach
                    </nav>
                </div>
                <div class="flex items-center gap-2 md:gap-5">
                    <a href="{{ route('home') }}" target="_blank" rel="noopener" class="hidden text-[13px] font-semibold text-muted transition-colors hover:text-brand md:inline">Lihat website ↗</a>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="cursor-pointer rounded-md border border-line px-3.5 py-2 text-[13px] font-semibold transition-colors hover:border-brand hover:text-brand">Keluar</button>
                    </form>
                </div>
            </div>
        </header>
    @endauth
    <main class="mx-auto max-w-6xl px-4 py-8 md:px-8 md:py-10">
        @if (session('status'))
            <div class="mb-6 rounded-lg border border-[#bfe5d6] bg-[#e9f8f3] px-4 py-3 text-[13px] font-semibold text-[#1c6e52]" role="status">{{ session('status') }}</div>
        @endif
        @if (session('warning'))
            <div class="mb-6 rounded-lg border border-[#f3d9a8] bg-[#fdf6e7] px-4 py-3 text-[13px] font-semibold text-[#8a5a12]" role="alert">{{ session('warning') }}</div>
        @endif
        @yield('content')
    </main>
</body>
</html>
