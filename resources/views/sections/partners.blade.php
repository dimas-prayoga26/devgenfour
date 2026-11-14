@if(count($partners))
@php
    // Each half of the marquee must be wider than the container, otherwise a gap shows before the loop restarts.
    $rounds = max(1, (int) ceil(8 / count($partners)));
@endphp
<section class="bg-white py-12 md:pt-16.5 md:pb-14.5" aria-labelledby="partners-title">
    <div class="mx-auto max-w-[1320px] px-4 xs:px-5 md:px-8 xl:px-12">
        <div data-reveal class="text-center data-[reveal=hidden]:opacity-0 data-[reveal=shown]:animate-reveal">
            <h2 id="partners-title" class="mt-3.25 text-[25px] leading-[1.35] font-bold tracking-[-1.1px] whitespace-pre-line md:mt-3.75 md:text-[29px] md:tracking-[-1.35px]">{{ __('company.partners_title') }}</h2>
            <p class="mt-3.25 text-[12px] text-muted">{{ __('company.partners_description') }}</p>
        </div>
        <div data-reveal class="group mt-8.5 overflow-hidden data-[reveal=hidden]:opacity-0 data-[reveal=shown]:animate-reveal [--reveal-delay:120ms] [mask-image:linear-gradient(to_right,transparent,black_7%,black_93%,transparent)] motion-reduce:overflow-x-auto motion-reduce:[mask-image:none]">
            <div class="flex w-max animate-marquee group-focus-within:[animation-play-state:paused] group-hover:[animation-play-state:paused] group-active:[animation-play-state:paused]">
                @for($copy = 0; $copy < 2; $copy++)
                    <ul class="flex shrink-0 gap-4 pr-4 md:gap-6 md:pr-6 @if($copy === 1) motion-reduce:hidden @endif" @if($copy === 1) aria-hidden="true" @endif>
                        @for($round = 0; $round < $rounds; $round++)
                            @foreach($partners as $partner)
                                <li class="group/logo grid h-22 w-40 place-items-center overflow-hidden rounded-[10px] border border-[#e8eef2] bg-white transition-colors duration-300 hover:border-[#95cce4] md:h-26 md:w-47.5 @if($round > 0) motion-reduce:hidden @endif" @if($copy === 0 && $round > 0) aria-hidden="true" @endif>
                                    <span class="block h-16 w-33.75 md:h-18 md:w-37.5 {{ $partner['logo_scale'] ?? '' }}"><img class="size-full object-contain opacity-75 transition duration-300 group-hover/logo:scale-105 group-hover/logo:opacity-100" src="{{ asset($partner['logo']) }}" alt="{{ $copy === 0 && $round === 0 ? $partner['name'] : '' }}" loading="lazy"></span>
                                </li>
                            @endforeach
                        @endfor
                    </ul>
                @endfor
            </div>
        </div>
    </div>
</section>
@endif
