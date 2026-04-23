<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    @include('partials.head')
</head>
<body class="public-page">
@php($navigationCategories = App\Models\Category::query()->orderBy('name')->get())
@php($inquiryListCount = collect(session('inquiry_list.items', []))->sum(fn (array $item): int => max(1, (int) ($item['quantity'] ?? 1))))
@php($currentCategoryParameter = request()->route('category'))
@php($currentCategorySlug = is_object($currentCategoryParameter) ? ($currentCategoryParameter->slug ?? null) : $currentCategoryParameter)

<header class="public-header bg-white/95">
    <div class="container public-header__container">
        <a href="{{ route('home') }}" class="public-header__brand-link">
            <x-app-logo class="size-8"/>
            <span class="public-header__brand-title">Gastro-Verleih Leipzig</span>
        </a>

        <nav class="public-header__desktop-nav" aria-label="{{ __('Hauptnavigation') }}">
            <a
                href="{{ route('home') }}"
                class="public-header__nav-item !text-gtc-green {{ request()->routeIs('home') ? 'is-active' : '' }}"
                @if (request()->routeIs('home')) aria-current="page" @endif
            >
                {{ __('Home') }}
            </a>

            <details data-public-products-dropdown class="public-header__dropdown {{ request()->routeIs('category.show') ? 'is-active' : '' }}">
                <summary class="public-header__nav-item">
                    {{ __('Produkte') }}
                </summary>

                <div class="public-header__menu" role="menu">
                    @foreach ($navigationCategories as $navigationCategory)
                        <a
                            href="{{ route('category.show', $navigationCategory->slug) }}"
                            class="public-header__menu-item {{ request()->routeIs('category.show') && (string) $currentCategorySlug === (string) $navigationCategory->slug ? 'is-active' : '' }}"
                            @if (request()->routeIs('category.show') && (string) $currentCategorySlug === (string) $navigationCategory->slug) aria-current="page" @endif
                        >
                            {{ $navigationCategory->name }}
                        </a>
                    @endforeach
                </div>
            </details>

            <a
                href="{{ route('inquiry.list') }}"
                class="public-header__nav-item !text-gtc-green {{ request()->routeIs('inquiry.list') ? 'is-active' : '' }}"
                @if (request()->routeIs('inquiry.list')) aria-current="page" @endif
            >
                <span class="public-header__inquiry-label">
                    {{ __('Anfrageliste') }}

                    @if ($inquiryListCount > 0)
                        <span
                            data-inquiry-count-badge
                            data-inquiry-count="{{ $inquiryListCount }}"
                            class="public-header__inquiry-badge"
                        >
                            {{ $inquiryListCount }}
                        </span>
                    @endif
                </span>
            </a>
        </nav>

        <nav class="public-header__mobile-nav md:hidden" aria-label="{{ __('Mobile Navigation') }}">
            <details class="public-header__mobile-dropdown">
                <summary class="public-header__nav-item">
                    {{ __('Menü') }}
                </summary>

                <div class="public-header__menu">
                    <a
                        href="{{ route('home') }}"
                        class="public-header__menu-item {{ request()->routeIs('home') ? 'is-active' : '' }}"
                        @if (request()->routeIs('home')) aria-current="page" @endif
                    >
                        {{ __('Home') }}
                    </a>
                    <a
                        href="{{ route('inquiry.list') }}"
                        class="public-header__menu-item {{ request()->routeIs('inquiry.list') ? 'is-active' : '' }}"
                        @if (request()->routeIs('inquiry.list')) aria-current="page" @endif
                    >
                        {{ __('Anfrageliste') }}
                    </a>

                    @foreach ($navigationCategories as $navigationCategory)
                        <a
                            href="{{ route('category.show', $navigationCategory->slug) }}"
                            class="public-header__menu-item {{ request()->routeIs('category.show') && (string) $currentCategorySlug === (string) $navigationCategory->slug ? 'is-active' : '' }}"
                            @if (request()->routeIs('category.show') && (string) $currentCategorySlug === (string) $navigationCategory->slug) aria-current="page" @endif
                        >
                            {{ $navigationCategory->name }}
                        </a>
                    @endforeach
                </div>
            </details>
        </nav>
    </div>
</header>

<main class="public-main">
    <flux:container>
        {{ $slot }}
    </flux:container>
</main>

<footer class="public-footer">
    <div class="container public-footer__content">
        <p class="public-footer__copyright">
            &copy; {{ date('Y') }} Gastro-Verleih Leipzig. {{ __('Alle Rechte vorbehalten.') }}
        </p>

        <div class="public-footer__links">
            <a href="{{ route('content.page', 'impressum') }}" class="public-footer__link">
                {{ __('Impressum') }}
            </a>
            <a href="{{ route('content.page', 'datenschutz') }}" class="public-footer__link">
                {{ __('Datenschutz') }}
            </a>
            @auth
                <a href="{{ route('dashboard') }}" class="public-footer__link">
                    {{ __('Admin') }}
                </a>
            @else
                <a href="{{ route('login') }}" class="public-footer__link">
                    {{ __('Login') }}
                </a>
            @endauth
        </div>
    </div>
</footer>

@persist('toast')
<flux:toast.group>
    <flux:toast/>
</flux:toast.group>
@endpersist

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const productsDropdown = document.querySelector('[data-public-products-dropdown]');

        if (!productsDropdown) {
            return;
        }

        document.addEventListener('click', (event) => {
            if (productsDropdown.open && !productsDropdown.contains(event.target)) {
                productsDropdown.open = false;
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && productsDropdown.open) {
                productsDropdown.open = false;
            }
        });
    });
</script>

@fluxScripts
</body>
</html>
