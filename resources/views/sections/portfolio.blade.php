@if($projects->isNotEmpty())
<section id="portfolio" class="border-t border-line bg-[#f4f9fc] py-14.5 md:py-17.5 lg:py-23.5" aria-labelledby="portfolio-title">
    <div class="mx-auto max-w-[1320px] px-4 xs:px-5 md:px-8 xl:px-12">
        <div data-reveal class="mb-7 data-[reveal=hidden]:opacity-0 data-[reveal=shown]:animate-reveal md:mb-10.5 md:flex md:items-end md:justify-between md:gap-10 lg:gap-12.5">
            <div>
                <p class="flex items-center gap-2.25 text-[9px] leading-[1.6] font-extrabold tracking-[1.5px] text-brand md:text-[10px] md:tracking-[1.7px]">{{ __('company.portfolio_label') }}</p>
                <h2 id="portfolio-title" class="mt-3.25 text-[29px] leading-[1.35] font-bold tracking-[-1.1px] whitespace-pre-line md:mt-3.75 md:tracking-[-1.35px] lg:text-[35px]">{{ str_replace('\n', "\n", __('company.portfolio_title')) }}</h2>
            </div>
            <p class="mt-5 max-w-[450px] pb-1 text-[12px] leading-[1.9] text-muted md:mt-0 md:max-w-[300px] lg:max-w-[358px] lg:text-[13px]">{{ __('company.portfolio_description') }}</p>
        </div>
        <div data-reveal class="portfolio-carousel data-[reveal=hidden]:opacity-0 data-[reveal=shown]:animate-reveal [--reveal-delay:120ms]" role="region" aria-roledescription="{{ __('company.portfolio_carousel') }}" aria-labelledby="portfolio-title">
            <div class="portfolio-track flex cursor-grab snap-x snap-mandatory overflow-x-auto overscroll-x-contain rounded-2xl border border-line bg-white [scrollbar-width:none] focus-visible:outline-3 focus-visible:outline-offset-5 focus-visible:outline-brand [&::-webkit-scrollbar]:hidden [&.is-dragging]:cursor-grabbing [&.is-dragging]:snap-none [&.is-dragging]:select-none" id="portfolio-track" tabindex="0" aria-label="{{ __('company.portfolio_hint') }}">
                @foreach($projects as $project)
                    <article data-spotlight class="portfolio-slide group grid min-w-0 flex-[0_0_100%] snap-start snap-always grid-cols-1 grid-rows-[auto_1fr] md:grid-cols-[1.1fr_1fr] md:grid-rows-none lg:grid-cols-[1.45fr_1fr]" role="group" aria-roledescription="{{ __('company.portfolio_slide') }}" aria-label="{{ $loop->iteration }} / {{ $loop->count }} — {{ $project->name }}">
                        <div class="flex flex-col justify-center overflow-hidden px-5 pt-5 pb-2 md:px-4.5 md:py-5.5 lg:px-7 lg:pt-6 lg:pb-3.5 {{ $project->tone->classes() }}">
                            <div class="flex flex-wrap items-center justify-between gap-3 text-[9px] font-semibold md:text-[10px] lg:flex-nowrap">
                                <span class="flex items-center gap-2">{{ $project->iconSvg('size-4.5 shrink-0 stroke-[1.6]') }}{{ $project->translated('category') }}</span>
                            </div>
                            <img class="mt-3 aspect-[3/2] w-full object-contain drop-shadow-[0_14px_14px_#173b4d13] transition-transform duration-500 group-lit:-translate-y-1 group-lit:scale-[1.02]" src="{{ $project->imageUrl() }}" width="960" height="640" alt="{{ $project->name }} — {{ $project->translated('category') }}" loading="lazy" draggable="false">
                        </div>
                        <div class="flex flex-col justify-center p-6.25 md:p-6.5 lg:px-9 lg:py-8.5">
                            <div class="mb-4.25 flex items-center justify-between md:mb-6"><span class="grid size-11 place-items-center rounded-[11px] transition-transform duration-300 group-lit:-rotate-6 md:size-13.5 md:rounded-[14px] {{ $project->tone->classes() }}">{{ $project->iconSvg('size-6 shrink-0 stroke-[1.6] md:size-7.25') }}</span></div>
                            <p class="text-[10px] font-bold tracking-[0.5px] text-brand">{{ $project->translated('category') }}</p>
                            <h3 class="mt-2.25 mb-4.25 text-[24px] leading-[1.3] font-bold tracking-[-0.9px] md:text-[23px] lg:text-[27px]">{{ $project->name }}</h3>
                            <p class="text-[12px] leading-[1.95] text-muted">{{ $project->translated('description') }}</p>
                            <p class="mt-5 mb-2.5 text-[8px] font-bold tracking-[1.5px] text-[#758591] md:mt-6.5">{{ __('company.portfolio_scope') }}</p>
                            <div class="flex flex-wrap gap-1.5">@foreach($project->tags as $tag)<span class="rounded-sm bg-[#f3f6f8] px-2 py-1 text-[9px] text-[#627789] md:text-[8px] lg:text-[9px]">{{ $tag->translatedName() }}</span>@endforeach</div>                        </div>
                    </article>
                @endforeach
            </div>
            <div class="mt-3.75 flex flex-wrap items-center justify-between gap-2 md:mt-5.75 md:flex-nowrap md:gap-6" @if(count($projects) < 2) hidden @endif>
                <div class="flex gap-0.5" role="group" aria-label="{{ __('company.nav.portfolio') }}">
                    @foreach($projects as $project)
                        <button class="group grid h-10 w-6.5 cursor-pointer place-items-center md:w-7.5" type="button" data-slide="{{ $loop->index }}" aria-label="{{ __('company.portfolio_go', ['number' => $loop->iteration]) }}" aria-controls="portfolio-track" @if($loop->first) aria-current="true" @endif><span class="h-1.5 w-1.5 rounded-[10px] bg-[#bdced9] transition-[width,background-color] duration-200 group-hover:bg-[#8fb3c7] group-aria-[current=true]:w-6 group-aria-[current=true]:bg-brand"></span></button>
                    @endforeach
                </div>
                <div class="flex items-center gap-2 md:gap-2.5">
                    <span class="portfolio-position min-w-[53px] text-[10px] text-[#657d8c] tabular-nums md:mr-2.25 md:text-[11px]" aria-live="polite" aria-atomic="true">01 / {{ str_pad(count($projects), 2, '0', STR_PAD_LEFT) }}</span>
                    <button class="portfolio-previous group grid size-11 cursor-pointer place-items-center rounded-full border border-[#cadce6] text-navy transition duration-200 hover:border-brand hover:bg-brand hover:text-white active:scale-90 active:border-brand active:bg-brand active:text-white" type="button" aria-label="{{ __('company.portfolio_previous') }}" aria-controls="portfolio-track"><svg class="size-5 shrink-0 rotate-180 stroke-[1.6] transition-transform duration-200 group-hover:-translate-x-0.5" aria-hidden="true"><use href="#icon-arrow"/></svg></button>
                    <button class="portfolio-next group grid size-11 cursor-pointer place-items-center rounded-full border border-[#cadce6] text-navy transition duration-200 hover:border-brand hover:bg-brand hover:text-white active:scale-90 active:border-brand active:bg-brand active:text-white" type="button" aria-label="{{ __('company.portfolio_next') }}" aria-controls="portfolio-track"><svg class="size-5 shrink-0 stroke-[1.6] transition-transform duration-200 group-hover:translate-x-0.5" aria-hidden="true"><use href="#icon-arrow"/></svg></button>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
