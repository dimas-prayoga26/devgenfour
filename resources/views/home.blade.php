<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ __('company.description') }}">
    <meta name="theme-color" content="#0a2844">
    <title>{{ __('company.title') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('Logo/logo_icon.png') }}">
    <link rel="alternate" hreflang="id" href="{{ route('home', ['lang' => 'id']) }}">
    <link rel="alternate" hreflang="en" href="{{ route('home', ['lang' => 'en']) }}">
    <link rel="preload" href="{{ asset('assets/company/jakarta-400.ttf') }}" as="font" type="font/ttf" crossorigin>
    @vite('resources/css/app.css')
    <script src="{{ asset('assets/company/company.js') }}?v=4" defer></script>
</head>
<body id="top" class="font-sans text-[14px] leading-[1.7] text-navy antialiased selection:bg-[#b7e9fa] selection:text-navy">
    <a class="fixed -top-25 left-5 z-100 bg-navy px-5 py-3 text-white focus:top-3" href="#main">{{ __('company.skip') }}</a>
    <svg class="absolute size-0 overflow-hidden" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <symbol id="icon-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12h15m-6-6 6 6-6 6"/></symbol>
        <symbol id="icon-diagonal" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M6 18 18 6M6 6h12v12"/></symbol>
        <symbol id="icon-web" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="3"/><path d="M3 9h18M7 6.5h.01M10 6.5h.01M8 13l-2 2 2 2m8-4 2 2-2 2m-3-5-2 7"/></symbol>
        <symbol id="icon-code" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m8 7-5 5 5 5m8-10 5 5-5 5m-3-14-2 18"/></symbol>
        <symbol id="icon-design" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 9 5-9 5-9-5 9-5Zm-9 9 9 5 9-5M3 16l9 5 9-5"/></symbol>
        <symbol id="icon-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 4 4L19 6"/></symbol>
        <symbol id="icon-spark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 2.5 6.5L21 12l-6.5 2.5L12 21l-2.5-6.5L3 12l6.5-2.5L12 3Z"/></symbol>
    </svg>
    @include('partials.social-icons')
    <header class="site-header sticky top-0 z-30 border-b border-[#edf1f4] bg-white/97 backdrop-blur-md transition-shadow duration-300 data-scrolled:shadow-[0_10px_30px_#102f4c12]">
        <div class="pointer-events-none absolute inset-x-0 -bottom-px hidden h-0.5 origin-left bg-brand [animation-fill-mode:both] [animation-name:grow-x] [animation-timeline:scroll(root)] [animation-timing-function:linear] motion-safe:supports-[animation-timeline:scroll()]:block" aria-hidden="true"></div>
        <div class="mx-auto flex h-19 max-w-[1320px] items-center justify-between gap-3 px-4 xs:px-5 md:h-20.5 md:gap-6.25 md:px-8 lg:h-25 lg:gap-4.5 xl:gap-7.5 xl:px-12">
            <a class="block w-[125px] shrink-0 xs:w-[143px] md:w-[155px] xl:w-[180px]" href="#top" aria-label="Devgenfour"><img class="w-full" src="{{ asset('Logo/devgen.png') }}" width="500" height="240" alt="Devgenfour — Smart, Fast and Innovative"></a>
            <nav class="main-nav absolute inset-x-0 top-full hidden flex-col items-stretch gap-1 border-b border-line bg-white px-8 py-5.5 shadow-[0_15px_20px_#102f4c0a] [&.is-open]:flex [&.is-open]:animate-drop-in lg:static lg:ml-auto lg:flex lg:flex-row lg:items-center lg:gap-3.75 lg:border-0 lg:bg-transparent lg:p-0 lg:shadow-none xl:mr-7 xl:gap-7.25" id="main-nav" aria-label="{{ __('company.menu') }}">
                @foreach (__('company.nav') as $anchor => $label)
                    <a class="relative rounded-md p-3 text-[14px] font-semibold transition-colors in-[.is-open]:animate-drop-in hover:text-brand active:text-brand max-lg:active:bg-[#f1f5f8] aria-[current=location]:text-brand lg:px-0 lg:text-[12px] lg:after:absolute lg:after:bottom-1.25 lg:after:left-0 lg:after:h-0.5 lg:after:w-0 lg:after:bg-brand lg:after:transition-[width] lg:after:duration-200 lg:hover:after:w-full lg:aria-[current=location]:after:w-full" href="#{{ $anchor }}" style="animation-delay: {{ 40 + $loop->index * 40 }}ms">{{ $label }}</a>
                @endforeach
            </nav>
            <div class="flex items-center gap-1.25 xs:gap-2.5 md:gap-4.5 lg:gap-3 xl:gap-5.75">
                <div class="language-switch flex items-center gap-px rounded-[7px] bg-[#f1f5f8] p-1" role="group" aria-label="{{ __('company.language') }}">
                    @foreach (['id' => 'Indonesia', 'en' => 'English'] as $locale => $label)
                        <a class="grid min-h-8.5 min-w-[31px] place-items-center rounded-[5px] text-[11px] font-bold text-muted transition hover:text-navy active:scale-90 aria-[current=true]:bg-white aria-[current=true]:text-navy aria-[current=true]:shadow-[0_1px_5px_#102f4c15] md:min-h-8 md:min-w-[33px]" href="{{ route('home', ['lang' => $locale]) }}" lang="{{ $locale }}" hreflang="{{ $locale }}" aria-label="{{ $label }}" @if(app()->getLocale() === $locale) aria-current="true" @endif>{{ strtoupper($locale) }}</a>
                    @endforeach
                </div>
                <a class="group hidden min-h-11 items-center justify-center gap-3.25 rounded-md border border-transparent bg-brand px-4.25 py-2.5 text-[11px] font-bold text-white transition duration-200 hover:-translate-y-0.5 hover:bg-[#096b96] active:translate-y-0 active:scale-[0.97] md:inline-flex" href="#contact">{{ __('company.discuss') }} <svg class="size-4.75 shrink-0 stroke-[1.6] transition-transform duration-200 group-hover:translate-x-0.5 group-hover:-translate-y-0.5"><use href="#icon-diagonal"/></svg></a>
                <button class="menu-toggle group flex h-11 w-10 cursor-pointer flex-col items-center justify-center gap-1.5 rounded-md transition active:scale-90 active:bg-[#f1f5f8] lg:hidden" type="button" aria-expanded="false" aria-controls="main-nav" aria-label="{{ __('company.menu') }}" data-open-label="{{ __('company.menu') }}" data-close-label="{{ __('company.close_menu') }}"><span class="block h-[1.5px] w-5.5 bg-navy transition-transform duration-200 group-aria-expanded:translate-y-[3.75px] group-aria-expanded:rotate-45"></span><span class="block h-[1.5px] w-5.5 bg-navy transition-transform duration-200 group-aria-expanded:-translate-y-[3.75px] group-aria-expanded:-rotate-45"></span></button>
            </div>
        </div>
    </header>
    <main id="main">
        <section class="overflow-hidden bg-[linear-gradient(115deg,#fff_40%,#f4fbff_100%)]" aria-labelledby="hero-title">
            <div class="mx-auto grid max-w-[1320px] grid-cols-1 items-center gap-8.75 px-4 pt-10.5 pb-9 xs:px-5 md:grid-cols-[1.05fr_1fr] md:px-8 md:pt-13 md:pb-13.75 lg:gap-11.25 lg:pt-17.5 lg:pb-18.5 xl:gap-16 xl:px-12 2xl:py-22.5">
                <div>
                    <div class="flex animate-fade-up items-center gap-2.25 text-[8px] leading-[1.6] font-extrabold tracking-[1.3px] text-brand md:tracking-[1.1px] lg:text-[10px] lg:tracking-[1.7px]"><span class="relative size-1.5 shrink-0 rounded-full bg-brand shadow-[0_0_0_4px_#e5f5fb] before:absolute before:inset-0 before:animate-live before:rounded-full before:bg-brand"></span>{{ __('company.eyebrow') }}</div>
                    <h1 class="my-5.5 text-[40px] leading-[1.16] font-extrabold tracking-[-2.2px] xs:text-[clamp(43px,9vw,60px)] md:my-6 md:text-[43px] md:tracking-[-2px] lg:text-[52px] lg:tracking-[-2.9px] xl:text-[clamp(44px,4.45vw,64px)]" id="hero-title">
                        <span class="block animate-blur-in [animation-delay:100ms]">{{ __('company.hero_first') }}</span>
                        <span class="block animate-blur-in [animation-delay:220ms]">{{ __('company.hero_second') }}</span>
                        <span class="block animate-blur-in [animation-delay:340ms]"><span class="relative text-brand">{{ __('company.hero_accent') }}<svg class="absolute top-3.75 -right-9.5 h-8.5 w-6.5 animate-twinkle stroke-[1.2] [animation-delay:1.3s] xs:-right-12.75 xs:w-8.5"><use href="#icon-spark"/></svg></span></span>
                    </h1>
                    <p class="max-w-[480px] animate-fade-up text-[13px] leading-[1.9] text-muted [animation-delay:480ms] md:max-w-[470px] md:text-[12px] md:leading-[1.95] lg:text-[14px]">{{ __('company.hero_description') }}</p>
                    <div class="mt-6.5 flex animate-fade-up flex-wrap items-center gap-3.75 [animation-delay:600ms] xs:gap-5 md:mt-7.25 md:flex-col md:items-start md:gap-3.75 lg:flex-row lg:items-center lg:gap-4.25 xl:flex-nowrap xl:gap-5.5">
                        <a href="#contact" class="group inline-flex min-h-12.75 items-center justify-center gap-3 rounded-md border border-transparent bg-brand px-4.5 py-3.75 text-[11px] font-bold text-white transition duration-200 hover:-translate-y-0.5 hover:bg-[#096b96] active:translate-y-0 active:scale-[0.97] md:min-h-13.5 md:gap-5 md:px-6 md:py-4.25 md:text-[12px]">{{ __('company.hero_cta') }}<svg class="size-4.75 shrink-0 stroke-[1.6] transition-transform duration-200 group-hover:translate-x-1"><use href="#icon-arrow"/></svg></a>
                        <a href="#services" class="group inline-flex items-center gap-1.75 text-[10px] font-bold transition-colors hover:text-brand active:text-brand md:gap-3 md:text-[11px]">{{ __('company.explore') }}<span class="inline-block text-[19px] transition-transform duration-200 group-hover:translate-x-0.5 group-hover:-translate-y-0.5" aria-hidden="true">↗</span></a>
                    </div>
                    <p class="mt-5.75 flex animate-fade-up items-center gap-2.5 text-[8px] text-[#728496] [animation-delay:720ms] xs:text-[9px] md:mt-7.5 md:text-[8px] lg:text-[10px]"><span class="h-px w-6 origin-left animate-grow-x bg-[#97b7c9] [animation-delay:900ms]"></span>{{ __('company.hero_note') }}</p>
                </div>
                <div data-spotlight class="group/visual relative mr-1.25 ml-2.75 animate-fade-up pt-3 pb-7 [animation-delay:250ms] md:mx-0 md:pt-3.25">
                    <div class="absolute -top-2 -right-3 z-3 grid size-15.75 rotate-12 animate-pop place-items-center rounded-full border-8 border-[#f6fbff] bg-[#dcf3fc] text-brand transition-transform duration-500 [animation-delay:1s] group-lit/visual:rotate-[102deg] md:-top-3 lg:-right-6 lg:size-18.5" aria-hidden="true"><svg class="size-8.75 shrink-0 stroke-[1.6]"><use href="#icon-spark"/></svg></div>
                    <div class="relative h-85 overflow-hidden rounded-2xl rounded-tr-[60px] bg-[#bcc9ce] after:absolute after:inset-x-0 after:top-[30%] after:bottom-0 after:bg-[linear-gradient(transparent,#082a42d9)] xs:h-95 md:h-107.5 md:rounded-tr-[76px] lg:h-117.5 xl:h-121 2xl:h-130"><img class="size-full object-cover object-[44%_center] transition-transform duration-700 group-lit/visual:scale-105" src="{{ asset('assets/company/collaboration.jpg') }}" alt="{{ __('company.image_alt') }}" width="1200" height="800" fetchpriority="high"><div class="absolute bottom-7.25 left-6.5 z-1 text-white md:bottom-7.5 md:left-6.25 lg:bottom-9.25 lg:left-8.75"><span class="text-[8px] font-semibold tracking-[2px] text-[#d0eafa] lg:text-[9px]">{{ __('company.visual_label') }}</span><p class="mt-2.5 text-[25px] leading-[1.4] font-semibold tracking-[-0.8px] whitespace-pre-line md:text-[22px] lg:text-[27px]">{{ str_replace('\n', "\n", __('company.visual_title')) }}</p></div></div>
                    <div class="absolute top-9.25 -left-2.75 z-4 flex animate-fade-up items-center gap-3 rounded-[10px] border border-[#eff4f7] bg-white px-4.25 py-3.5 shadow-[0_12px_40px_#0c30451a] transition-transform duration-500 [animation-delay:800ms] group-lit/visual:-translate-y-1.5 md:top-11.25 md:-left-3.75 md:p-3 lg:top-13.25 lg:-left-7.5 lg:px-5 lg:py-4.25"><span class="grid size-9.5 place-items-center rounded-full bg-[#e9f8f3] text-[#1c916a]"><svg class="h-6 w-4.75 shrink-0 stroke-[1.6]"><use href="#icon-check"/></svg></span><div><strong class="block text-[11px] md:text-[10px] lg:text-[12px]">{{ __('company.visual_badge') }}</strong><span class="mt-0.5 block text-[9px] text-muted md:text-[8px] lg:text-[9px]">{{ __('company.visual_badge_sub') }}</span></div><svg class="ml-3 hidden h-6 w-4.5 shrink-0 stroke-[1.6] text-[#81c5e1] lg:block"><use href="#icon-spark"/></svg></div>
                    <div class="absolute right-0 bottom-0 left-1.25 flex items-center gap-3.5 text-[8px] font-semibold tracking-[1.2px] text-[#6e8191]" aria-hidden="true"><span>DEVGENFOUR</span><i class="h-px flex-1 bg-[#c6dce8]"></i><span>IDEA → IMPACT</span></div>
                </div>
            </div>
        </section>
        <section id="about" class="bg-[#f6f9fb] py-14.5 md:py-17.5 lg:py-23.5">
            <div class="mx-auto grid max-w-[1320px] grid-cols-1 items-center gap-8.75 px-4 xs:px-5 md:grid-cols-[.9fr_1.1fr] md:px-8 lg:grid-cols-[1fr_1.1fr] lg:gap-13.75 xl:gap-23.75 xl:px-12">
                <div data-spotlight data-reveal class="group/art relative mx-auto flex h-85 w-full max-w-[500px] flex-col items-center justify-center overflow-hidden rounded-xl bg-[#e1f2fa] data-[reveal=hidden]:opacity-0 data-[reveal=shown]:animate-reveal md:mx-0 md:h-115 md:max-w-none lg:h-122.5" aria-hidden="true">
                    <div class="absolute top-[39%] left-1/2 size-65 -translate-x-1/2 -translate-y-1/2 rounded-full border border-[#9ccce15c] transition duration-500 group-lit/art:scale-110 group-lit/art:border-[#9ccce1]"></div>
                    <div class="absolute top-[39%] left-1/2 size-97.5 -translate-x-1/2 -translate-y-1/2 rounded-full border border-[#9ccce15c] transition duration-700 group-lit/art:scale-106 group-lit/art:border-[#9ccce1b3]"></div>
                    <div class="absolute top-[39%] left-1/2 size-132.5 -translate-x-1/2 -translate-y-1/2 rounded-full border border-[#9ccce15c] transition duration-1000 group-lit/art:scale-103 group-lit/art:border-[#9ccce180]"></div>
                    <span class="absolute top-11 left-6.25 z-3 -rotate-9 rounded-[5px] border border-[#e0edf3] bg-[#ffffffdd] px-3.25 py-1.75 font-mono text-[11px] text-[#47768e] shadow-[0_5px_20px_#0c304505] transition-transform duration-500 group-lit/art:-translate-y-1.5 group-lit/art:-rotate-14 md:top-16.25 md:left-3.75 lg:left-8">&lt; ideas /&gt;</span>
                    <img class="z-2 mb-3 size-27 object-contain transition-transform duration-500 group-lit/art:scale-105 md:mb-6 md:size-35.5" src="{{ asset('Logo/logo_icon.png') }}" width="220" height="220" alt="" loading="lazy">
                    <span class="absolute top-43.75 right-5 z-3 rotate-7 rounded-[5px] border border-[#e0edf3] bg-[#ffffffdd] px-3.25 py-1.75 font-mono text-[11px] text-[#47768e] shadow-[0_5px_20px_#0c304505] transition-transform duration-500 group-lit/art:translate-y-1.5 group-lit/art:rotate-12 md:top-64.5 md:right-2.5 lg:right-5.75">{ possibilities }</span>
                    <div class="z-2 mt-2.5 text-center text-[26px] leading-[1.3] font-extrabold tracking-[-1px] md:text-[29px]">SMART. FAST.<br><span class="text-brand">INNOVATIVE.</span></div>
                    <span class="absolute bottom-4.5 left-7.5 text-[7px] tracking-[1.8px] text-[#5e8ca1] md:bottom-6.5 md:text-[8px]">DEVGENFOUR / OUR DNA</span>
                </div>
                <div data-reveal class="data-[reveal=hidden]:opacity-0 data-[reveal=shown]:animate-reveal [--reveal-delay:120ms]">
                    <p class="flex items-center gap-2.25 text-[9px] leading-[1.6] font-extrabold tracking-[1.5px] text-brand md:text-[10px] md:tracking-[1.7px]">{{ __('company.about_label') }}</p>
                    <h2 class="mt-3.25 text-[29px] leading-[1.35] font-bold tracking-[-1.1px] whitespace-pre-line md:mt-3.75 md:tracking-[-1.35px] lg:text-[35px]">{{ str_replace('\n', "\n", __('company.about_title')) }}</h2>
                    <p class="mt-4.5 text-[12px] leading-[1.9] text-muted lg:text-[13px]">{{ __('company.about_description') }}</p>
                    <p class="mt-4.5 text-[12px] leading-[1.9] text-muted lg:text-[13px]">{{ __('company.about_description_two') }}</p>
                    <div class="mt-6.5 grid gap-4.25">
                        @foreach (__('company.values') as $value)
                            <div data-spotlight class="group flex items-start gap-3"><span class="mt-0.75 grid size-6 shrink-0 place-items-center rounded-full bg-[#e3f2f8] text-brand transition-colors duration-200 group-lit:bg-brand group-lit:text-white"><svg class="size-3.5 shrink-0 stroke-[1.6]"><use href="#icon-check"/></svg></span><p class="leading-[1.65]"><strong class="block text-[12px]">{{ $value['title'] }}</strong><span class="text-[11px] text-muted">{{ $value['description'] }}</span></p></div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
        <section id="services" class="py-14.5 md:py-17.5 lg:py-23.5">
            <div class="mx-auto max-w-[1320px] px-4 xs:px-5 md:px-8 xl:px-12">
                <div data-reveal class="mb-7 data-[reveal=hidden]:opacity-0 data-[reveal=shown]:animate-reveal md:mb-10.5 md:flex md:items-end md:justify-between md:gap-10 lg:gap-12.5">
                    <div>
                        <p class="flex items-center gap-2.25 text-[9px] leading-[1.6] font-extrabold tracking-[1.5px] text-brand md:text-[10px] md:tracking-[1.7px]">{{ __('company.services_label') }}</p>
                        <h2 class="mt-3.25 text-[29px] leading-[1.35] font-bold tracking-[-1.1px] whitespace-pre-line md:mt-3.75 md:tracking-[-1.35px] lg:text-[35px]">{{ str_replace('\n', "\n", __('company.services_title')) }}</h2>
                    </div>
                    <p class="mt-5 max-w-[450px] pb-1 text-[12px] leading-[1.9] text-muted md:mt-0 md:max-w-[300px] lg:max-w-[358px] lg:text-[13px]">{{ __('company.services_description') }}</p>
                </div>
                <div class="grid grid-cols-1 gap-4.25 md:grid-cols-3 md:gap-3.5 lg:gap-5.5">
                    @foreach (__('company.services') as $service)
                        <article data-spotlight data-reveal class="group flex flex-col rounded-[10px] border border-line bg-white p-6.75 transition duration-300 lit:-translate-y-1.25 lit:border-[#95cce4] lit:shadow-[0_14px_34px_#0c304512] data-[reveal=hidden]:opacity-0 data-[reveal=shown]:animate-reveal md:px-4.5 md:py-5.75 md:[--reveal-delay:calc(var(--i)*100ms)] lg:px-5.5 lg:py-6.25 xl:px-6.75 xl:pt-7.5 xl:pb-5.75" style="--i: {{ $loop->index }}">
                            <div class="mb-5.5 flex items-center justify-between md:mb-6.25"><div class="grid size-12.75 place-items-center rounded-[10px] bg-[#eaf6fc] text-brand transition duration-300 group-lit:-rotate-6 group-lit:bg-brand group-lit:text-white"><svg class="size-6.5 shrink-0 stroke-[1.6]"><use href="#icon-{{ $service['icon'] }}"/></svg></div></div>
                            <h3 class="mb-3.25 text-[19px] font-bold tracking-[-0.5px] md:text-[15px] lg:text-[18px]">{{ $service['title'] }}</h3>
                            <p class="mb-5.75 text-[12px] leading-[1.9] text-muted md:text-[11px] lg:text-[12px]">{{ $service['description'] }}</p>
                            <div class="mt-auto mb-6.75 flex flex-wrap gap-1.5">@foreach ($service['tags'] as $tag)<span class="rounded-sm bg-[#f3f6f8] px-2 py-1 text-[9px] text-[#627789] md:text-[8px] lg:text-[9px]">{{ $tag }}</span>@endforeach</div>
                            <a href="#contact" class="flex items-center justify-between border-t border-[#e8eef2] pt-5 text-[11px] font-bold transition-colors group-lit:text-brand active:text-brand md:text-[10px] lg:text-[11px]">{{ __('company.service_cta') }}<svg class="h-6 w-5 shrink-0 stroke-[1.6] text-brand transition-transform duration-300 group-lit:rotate-45"><use href="#icon-diagonal"/></svg></a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
        @include('sections.portfolio', ['projects' => $projects])
        @include('sections.partners', ['partners' => config('company.partners')])
        <section id="process" class="border-t border-line py-14.5 md:py-17.5 lg:py-23.5">
            <div class="mx-auto max-w-[1320px] px-4 xs:px-5 md:px-8 xl:px-12">
                <div data-reveal class="mb-7 data-[reveal=hidden]:opacity-0 data-[reveal=shown]:animate-reveal md:mb-10.5 md:flex md:items-end md:justify-between md:gap-10 lg:gap-12.5">
                    <div>
                        <p class="flex items-center gap-2.25 text-[9px] leading-[1.6] font-extrabold tracking-[1.5px] text-brand md:text-[10px] md:tracking-[1.7px]">{{ __('company.process_label') }}</p>
                        <h2 class="mt-3.25 text-[29px] leading-[1.35] font-bold tracking-[-1.1px] whitespace-pre-line md:mt-3.75 md:tracking-[-1.35px] lg:text-[35px]">{{ str_replace('\n', "\n", __('company.process_title')) }}</h2>
                    </div>
                    <p class="mt-5 max-w-[450px] pb-1 text-[12px] leading-[1.9] text-muted md:mt-0 md:max-w-[300px] lg:max-w-[358px] lg:text-[13px]">{{ __('company.process_description') }}</p>
                </div>
                <ol class="mt-7.5 grid grid-cols-2 gap-x-6 gap-y-7 md:mt-12.5 md:grid-cols-4 md:gap-5.75 lg:gap-8">
                    @foreach (__('company.steps') as $step)
                        <li data-spotlight data-reveal class="group [--reveal-delay:calc(var(--column)*90ms)] data-[reveal=hidden]:opacity-0 data-[reveal=shown]:animate-reveal md:[--reveal-delay:calc(var(--i)*90ms)]" style="--i: {{ $loop->index }}; --column: {{ $loop->index % 2 }}"><div class="flex items-center justify-between border-t border-[#bbd5e3] pt-4.75 text-[26px] font-semibold text-brand transition-colors duration-300 group-lit:border-brand">0{{ $loop->iteration }}<svg class="h-6 w-5 shrink-0 stroke-[1.6] text-[#8ba7b7] transition duration-300 group-last:invisible group-lit:translate-x-1 group-lit:text-brand"><use href="#icon-arrow"/></svg></div><h3 class="mt-4.5 mb-2.5 text-[13px] font-bold tracking-[-0.3px] lg:text-[15px]">{{ $step['title'] }}</h3><p class="text-[11px] leading-[1.9] text-muted lg:text-[12px]">{{ $step['description'] }}</p></li>
                    @endforeach
                </ol>
            </div>
        </section>
        <section class="border-t border-line py-14.5 md:py-17.5 lg:pt-18.75 lg:pb-23.5">
            <div class="mx-auto grid max-w-[1320px] grid-cols-1 gap-6.75 px-4 xs:px-5 md:grid-cols-[.9fr_1.2fr] md:gap-11.25 md:px-8 lg:gap-16.25 xl:gap-25 xl:px-12">
                <div data-reveal class="data-[reveal=hidden]:opacity-0 data-[reveal=shown]:animate-reveal">
                    <p class="flex items-center gap-2.25 text-[9px] leading-[1.6] font-extrabold tracking-[1.5px] text-brand md:text-[10px] md:tracking-[1.7px]">{{ __('company.faq_label') }}</p>
                    <h2 class="mt-3.25 text-[29px] leading-[1.35] font-bold tracking-[-1.1px] whitespace-pre-line md:mt-3.75 md:tracking-[-1.35px] lg:text-[35px]">{{ str_replace('\n', "\n", __('company.faq_title')) }}</h2>
                    <svg class="mt-6.25 hidden size-12 shrink-0 stroke-1 text-[#9ad8ed] md:block" aria-hidden="true"><use href="#icon-spark"/></svg>
                </div>
                <div data-reveal class="data-[reveal=hidden]:opacity-0 data-[reveal=shown]:animate-reveal md:[--reveal-delay:120ms]">
                    @foreach (__('company.faqs') as $faq)
                        <details class="group border-b border-line first:border-t" name="faq" @if($loop->first) open @endif><summary class="flex cursor-pointer list-none items-center justify-between gap-6.25 py-5 text-[12px] font-semibold transition-colors group-open:text-brand hover:text-brand active:text-brand md:py-5.5 lg:text-[13px] [&::-webkit-details-marker]:hidden">{{ $faq['question'] }}<span class="shrink-0 text-[22px] font-normal text-brand transition-transform duration-200 group-open:rotate-45" aria-hidden="true">+</span></summary><p class="pr-4.5 pb-5.75 text-[12px] leading-[1.9] text-muted group-open:animate-drop-in md:pr-8.75">{{ $faq['answer'] }}</p></details>
                    @endforeach
                </div>
            </div>
        </section>
        <section id="contact" class="pb-12.5 md:pb-18.75">
            <div class="mx-auto max-w-[1320px] px-4 xs:px-5 md:px-8 xl:px-12">
                <div data-spotlight data-reveal class="group/contact relative flex items-center justify-between gap-12.5 overflow-hidden rounded-xl bg-[#103650] data-[reveal=hidden]:opacity-0 data-[reveal=shown]:animate-reveal px-5.75 py-7.5 text-white xs:px-6.75 xs:py-8.75 md:rounded-[15px] md:p-11.25 lg:px-16.25 lg:py-14.5">
                    <div class="relative z-2">
                        <p class="flex max-w-[230px] items-center gap-2.25 text-[8px] leading-[1.6] font-extrabold tracking-[1.5px] text-[#8ad2ee] md:max-w-none md:text-[9px] md:tracking-[1.7px]">{{ __('company.contact_label') }}</p>
                        <h2 class="mt-4 text-[27px] leading-[1.35] font-bold tracking-[-1.1px] whitespace-pre-line xs:text-[30px] md:text-[34px] md:tracking-[-1.6px] lg:text-[40px]">{{ str_replace('\n', "\n", __('company.contact_title')) }}</h2>
                        <p class="mt-4.75 max-w-[430px] text-[12px] leading-[1.9] text-[#bdced8]">{{ __('company.contact_description') }}</p>
                        <div class="mt-6.25 flex flex-wrap gap-3">
                            @if($contact->whatsappNumber())
                                <a class="group inline-flex min-h-12.75 items-center justify-center gap-3 rounded-md border border-transparent bg-white px-4.5 py-3.75 text-[11px] font-bold text-navy transition duration-200 hover:-translate-y-0.5 hover:bg-[#e1f2fa] active:translate-y-0 active:scale-[0.97] active:bg-[#e1f2fa] md:min-h-13.5 md:gap-5 md:px-6 md:py-4.25 md:text-[12px]" href="{{ $contact->whatsappUrl(__('company.whatsapp_message')) }}" target="_blank" rel="noopener noreferrer">{{ __('company.whatsapp_cta') }}<svg class="size-4.75 shrink-0 stroke-[1.6] transition-transform duration-200 group-hover:translate-x-0.5 group-hover:-translate-y-0.5"><use href="#icon-diagonal"/></svg></a>
                            @endif
                            @if($contact->email())
                                <a class="group inline-flex min-h-12.75 items-center justify-center gap-3 rounded-md border border-transparent bg-white px-4.5 py-3.75 text-[11px] font-bold text-navy transition duration-200 hover:-translate-y-0.5 hover:bg-[#e1f2fa] active:translate-y-0 active:scale-[0.97] active:bg-[#e1f2fa] md:min-h-13.5 md:gap-5 md:px-6 md:py-4.25 md:text-[12px]" href="mailto:{{ $contact->email() }}?subject={{ rawurlencode(__('company.email_subject')) }}">{{ __('company.email_cta') }}<svg class="size-4.75 shrink-0 stroke-[1.6] transition-transform duration-200 group-hover:translate-x-1"><use href="#icon-arrow"/></svg></a>
                            @endif
                            @unless($contact->hasDirectContact())
                                <p class="border-t border-[#3e5a6e] pt-3.75 text-[10px] text-[#c5dbe7] md:text-[11px]">{{ __('company.contact_pending') }}</p>
                            @endunless
                        </div>
                        @if($contact->address() || $contact->socialLinks())
                            <div class="mt-6.5 flex flex-wrap items-center gap-x-6 gap-y-3.5 text-[12px] text-[#bdced8]">
                                @if($contact->address())
                                    <p class="flex items-center gap-2"><svg class="size-4 shrink-0 stroke-[1.6] text-[#8ad2ee]"><use href="#icon-map-pin"/></svg>{{ $contact->address() }}</p>
                                @endif
                                @if($contact->socialLinks())
                                    <ul class="flex items-center gap-2" aria-label="{{ __('company.social_label') }}">
                                        @foreach($contact->socialLinks() as $link)
                                            <li><a class="grid size-9 place-items-center rounded-full border border-[#ffffff2e] text-white transition duration-200 hover:-translate-y-0.5 hover:border-[#a1d6ec] hover:bg-[#a1d6ec] hover:text-[#103650] active:scale-90 active:border-[#a1d6ec] active:bg-[#a1d6ec] active:text-[#103650]" href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $link['label'] }}"><svg class="size-4" aria-hidden="true"><use href="#icon-{{ $link['network'] }}"/></svg></a></li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div data-spotlight class="peer/symbol group/symbol relative z-1 hidden size-30 shrink-0 rounded-full border border-[#77b2d166] transition-[border-color,scale] duration-400 ease-[cubic-bezier(.2,.8,.2,1)] before:absolute before:-inset-px before:scale-0 before:rounded-full before:bg-[#a1d6ec] before:transition-transform before:duration-450 before:ease-[cubic-bezier(.2,.8,.2,1)] lit:scale-105 lit:animate-ring-pulse lit:border-[#a1d6ec] lit:before:scale-100 md:block lg:mr-5 lg:size-38.75" aria-hidden="true"><span class="absolute inset-0 grid place-items-center overflow-hidden rounded-full"><svg class="size-18.75 shrink-0 stroke-1 text-[#a1d6ec] transition-colors duration-300 group-lit/symbol:animate-arrow-fly group-lit/symbol:text-[#103650]"><use href="#icon-diagonal"/></svg></span></div>
                    <div class="pointer-events-none absolute -top-10 -right-87.5 size-135 rounded-full border border-[#7cb4d11c] transition duration-700 ease-out before:absolute before:inset-13.75 before:rounded-full before:border before:border-[#7cb4d11c] before:transition before:duration-500 before:ease-out after:absolute after:-inset-13.75 after:rounded-full after:border after:border-[#7cb4d11c] after:transition after:duration-1000 after:ease-out peer-lit/symbol:scale-106 peer-lit/symbol:border-[#7cb4d155] peer-lit/symbol:before:scale-108 peer-lit/symbol:before:border-[#a1d6ec66] peer-lit/symbol:after:scale-106 peer-lit/symbol:after:border-[#7cb4d140] group-data-lit/contact:scale-106 group-data-lit/contact:border-[#7cb4d155] group-data-lit/contact:before:scale-108 group-data-lit/contact:before:border-[#a1d6ec66] group-data-lit/contact:after:scale-106 group-data-lit/contact:after:border-[#7cb4d140] md:-right-25" aria-hidden="true"></div>
                </div>
            </div>
        </section>
    </main>
    @php
        $hasContactList = $contact->email() !== null || $contact->whatsappNumber() !== null || $contact->address() !== null;
        $mapEmbedUrl = $contact->mapEmbedUrl();
        $footerColumns = match (true) {
            $hasContactList && $mapEmbedUrl !== null => 'md:grid-cols-2 lg:grid-cols-[1.25fr_0.7fr_1.05fr_1.45fr]',
            $hasContactList || $mapEmbedUrl !== null => 'md:grid-cols-2 lg:grid-cols-[1.4fr_0.8fr_1.4fr]',
            default => 'md:grid-cols-[1.6fr_1fr]',
        };
        $footerHeading = 'text-[10px] leading-[1.6] font-extrabold tracking-[1.7px] text-brand';
    @endphp
    <footer class="border-t border-line bg-[#f6f9fb]">
        <div class="mx-auto max-w-[1320px] px-4 xs:px-5 md:px-8 xl:px-12">
            <div class="grid gap-10 pt-12 pb-10 md:gap-x-12 md:gap-y-11 md:pt-16 md:pb-14 lg:gap-x-10 {{ $footerColumns }}">
                <div data-reveal class="data-[reveal=hidden]:opacity-0 data-[reveal=shown]:animate-reveal">
                    <a class="-ml-1.75 block w-[140px] md:w-[155px]" href="#top" aria-label="Devgenfour"><img class="w-full" src="{{ asset('Logo/devgen.png') }}" width="500" height="240" loading="lazy" alt="Devgenfour — Smart, Fast and Innovative"></a>
                    <p class="mt-4 max-w-[300px] text-[13px] leading-[1.8] text-muted">{{ __('company.footer_description') }}</p>
                    @if($contact->socialLinks())
                        <ul class="mt-6 flex flex-wrap items-center gap-2" aria-label="{{ __('company.social_label') }}">
                            @foreach($contact->socialLinks() as $link)
                                <li><a class="grid size-9.5 place-items-center rounded-full border border-line bg-white text-muted transition duration-200 hover:-translate-y-0.5 hover:border-brand hover:bg-brand hover:text-white active:scale-90 active:border-brand active:bg-brand active:text-white" href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $link['label'] }}"><svg class="size-4" aria-hidden="true"><use href="#icon-{{ $link['network'] }}"/></svg></a></li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <nav data-reveal class="data-[reveal=hidden]:opacity-0 data-[reveal=shown]:animate-reveal md:[--reveal-delay:80ms]" aria-label="Footer">
                    <p class="{{ $footerHeading }}">{{ __('company.footer_menu') }}</p>
                    <ul class="mt-5 grid grid-cols-2 gap-x-6 gap-y-3 text-[13px] font-semibold text-navy md:grid-cols-1">
                        @foreach (__('company.nav') as $anchor => $label)
                            <li><a class="group inline-flex items-center gap-1.5 transition-colors hover:text-brand active:text-brand" href="#{{ $anchor }}">{{ $label }}<span class="-translate-x-1 text-brand opacity-0 transition duration-200 group-hover:translate-x-0 group-hover:opacity-100 group-active:translate-x-0 group-active:opacity-100" aria-hidden="true">→</span></a></li>
                        @endforeach
                    </ul>
                </nav>
                @if($hasContactList)
                    <div data-reveal class="data-[reveal=hidden]:opacity-0 data-[reveal=shown]:animate-reveal md:[--reveal-delay:160ms]">
                        <p class="{{ $footerHeading }}">{{ __('company.footer_contact') }}</p>
                        <ul class="mt-5 space-y-3.5 text-[13px] text-navy">
                            @if($contact->email())
                                <li><a class="group flex items-center gap-3" href="mailto:{{ $contact->email() }}"><span class="grid size-8.5 shrink-0 place-items-center rounded-full bg-white text-brand ring-1 ring-line transition-colors group-hover:bg-brand group-hover:text-white group-hover:ring-brand group-active:bg-brand group-active:text-white group-active:ring-brand"><svg class="size-4 stroke-[1.6]"><use href="#icon-mail"/></svg></span><span class="break-all transition-colors group-hover:text-brand group-active:text-brand">{{ $contact->email() }}</span></a></li>
                            @endif
                            @if($contact->whatsappNumber())
                                <li><a class="group flex items-center gap-3" href="{{ $contact->whatsappUrl(__('company.whatsapp_message')) }}" target="_blank" rel="noopener noreferrer"><span class="grid size-8.5 shrink-0 place-items-center rounded-full bg-white text-brand ring-1 ring-line transition-colors group-hover:bg-brand group-hover:text-white group-hover:ring-brand group-active:bg-brand group-active:text-white group-active:ring-brand"><svg class="size-4 stroke-[1.6]"><use href="#icon-phone"/></svg></span><span class="transition-colors group-hover:text-brand group-active:text-brand">{{ $contact->whatsapp() }}</span></a></li>
                            @endif
                            @if($contact->address())
                                <li><a class="group flex items-center gap-3" href="{{ $contact->mapUrl() }}" target="_blank" rel="noopener noreferrer" title="{{ __('company.map_open') }}"><span class="grid size-8.5 shrink-0 place-items-center rounded-full bg-white text-brand ring-1 ring-line transition-colors group-hover:bg-brand group-hover:text-white group-hover:ring-brand group-active:bg-brand group-active:text-white group-active:ring-brand"><svg class="size-4 stroke-[1.6]"><use href="#icon-map-pin"/></svg></span><span class="transition-colors group-hover:text-brand group-active:text-brand">{{ $contact->address() }}</span></a></li>
                            @endif
                        </ul>
                    </div>
                @endif
                @if($mapEmbedUrl !== null)
                    <div data-reveal class="data-[reveal=hidden]:opacity-0 data-[reveal=shown]:animate-reveal md:[--reveal-delay:240ms] {{ $hasContactList ? '' : 'md:col-span-2 lg:col-span-1' }}">
                        <p class="{{ $footerHeading }}">{{ __('company.footer_location') }}</p>
                        <div class="mt-5 rounded-2xl border border-line bg-white p-1.5 shadow-[0_12px_30px_#0c304510]">
                            <iframe src="{{ $mapEmbedUrl }}" title="{{ __('company.map_title') }}" class="block h-48 w-full rounded-xl md:h-52" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                        </div>
                    </div>
                @endif
            </div>
            <div class="flex flex-col gap-3 border-t border-line py-6 text-[12px] text-[#728290] md:flex-row md:items-center md:justify-between"><p>© {{ date('Y') }} Devgenfour. {{ __('company.rights') }}</p><a class="group inline-flex items-center gap-2 font-semibold transition-colors hover:text-brand active:text-brand" href="#top">{{ __('company.back_top') }}<span class="inline-block transition-transform duration-200 group-hover:-translate-y-0.5 group-active:-translate-y-0.5" aria-hidden="true">↑</span></a></div>
        </div>
    </footer>
</body>
</html>
