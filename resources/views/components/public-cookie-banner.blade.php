<div
    data-cookie-consent-banner
    role="region"
    aria-labelledby="cookie-consent-title"
    class="fixed inset-x-0 bottom-0 z-50 border-t border-black/10 bg-white/95 shadow-[0_-12px_32px_rgba(15,23,42,0.12)] backdrop-blur"
>
    <div class="container flex flex-col gap-4 py-4 sm:py-5 lg:flex-row lg:items-end lg:justify-between">
        <div class="max-w-3xl space-y-2 text-sm">
            <p id="cookie-consent-title" class="text-base font-semibold text-gtc-green">
                {{ __('cookieBannerTitle') }}
            </p>

            <p class="leading-6 text-gtc-ink/80">
                {{ __('cookieBannerDescription') }}
            </p>

            <p class="leading-6 text-gtc-muted">
                {{ __('cookieBannerRequiredOnly') }}
                {{ __('cookieBannerFurtherInformation') }}
                <a
                    href="{{ route('content.page', 'impressum') }}"
                    class="font-medium text-gtc-green underline decoration-gtc-green/50 underline-offset-4 transition hover:decoration-gtc-green"
                >
                    {{ __('legalNotice') }}
                </a>
                {{ __('cookieBannerAnd') }}
                <a
                    href="{{ route('content.page', 'datenschutz') }}"
                    class="font-medium text-gtc-green underline decoration-gtc-green/50 underline-offset-4 transition hover:decoration-gtc-green"
                >
                    {{ __('privacyPolicy') }}
                </a>.
            </p>
        </div>

        <div class="flex shrink-0 items-center">
            <button
                type="button"
                data-cookie-consent-accept
                class="btn-primary-inquiry inline-flex min-h-11 items-center justify-center rounded-full px-5 py-3 text-sm font-semibold shadow-sm"
            >
                {{ __('cookieBannerAccept') }}
            </button>
        </div>
    </div>
</div>
