<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        @include('partials.head')
    </head>
    <body class="public-page">
        @php($navigationCategories = \App\Models\Category::query()->orderBy('name')->get())
        @php($inquiryListCount = collect(session('inquiry_list.items', []))->sum(fn (array $item): int => max(1, (int) ($item['quantity'] ?? 1))))

        <flux:header container class="public-header">
            <flux:navbar>
                <flux:navbar.item href="{{ route('home') }}" class="public-header__brand-link">
                    <x-app-logo class="size-8" />
                    <span class="public-header__brand-title">Gastro-Verleih Leipzig</span>
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <flux:navbar class="public-header__desktop-nav">
                <flux:navbar.item
                    href="{{ route('home') }}"
                    :current="request()->routeIs('home')"
                    class="public-header__nav-item"
                >
                    {{ __('Home') }}
                </flux:navbar.item>
                <flux:dropdown position="bottom" align="start">
                    <flux:navbar.item
                        icon-trailing="chevron-down"
                        :current="request()->routeIs('category.show')"
                        class="public-header__nav-item"
                    >
                        {{ __('Produkte') }}
                    </flux:navbar.item>

                    <flux:menu class="public-header__menu">
                        @foreach ($navigationCategories as $navigationCategory)
                            <flux:menu.item
                                :href="route('category.show', $navigationCategory->slug)"
                                class="public-header__menu-item"
                            >
                                {{ $navigationCategory->name }}
                            </flux:menu.item>
                        @endforeach
                    </flux:menu>
                </flux:dropdown>
                <flux:navbar.item
                    href="{{ route('inquiry.list') }}"
                    :current="request()->routeIs('inquiry.list')"
                    class="public-header__nav-item"
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
                </flux:navbar.item>
            </flux:navbar>

            <flux:navbar class="public-header__mobile-nav">
                <flux:dropdown position="bottom" align="end">
                    <flux:navbar.item
                        icon-trailing="chevron-down"
                        class="public-header__nav-item"
                    >
                        {{ __('Menü') }}
                    </flux:navbar.item>

                    <flux:menu class="public-header__menu">
                        <flux:menu.item
                            :href="route('home')"
                            class="public-header__menu-item"
                        >
                            {{ __('Home') }}
                        </flux:menu.item>
                        <flux:menu.item
                            :href="route('inquiry.list')"
                            class="public-header__menu-item"
                        >
                            {{ __('Anfrageliste') }}
                        </flux:menu.item>

                        @foreach ($navigationCategories as $navigationCategory)
                            <flux:menu.item
                                :href="route('category.show', $navigationCategory->slug)"
                                class="public-header__menu-item"
                            >
                                {{ $navigationCategory->name }}
                            </flux:menu.item>
                        @endforeach

                    </flux:menu>
                </flux:dropdown>
            </flux:navbar>

        </flux:header>

        <flux:main container class="public-main">
            {{ $slot }}
        </flux:main>

        <flux:footer container class="public-footer">
            <div class="public-footer__content">
                <div class="public-footer__brand">
                    <x-app-logo class="size-6" />
                    <span class="public-footer__brand-title">Gastro-Verleih Leipzig</span>
                </div>

                <flux:text variant="subtle" size="sm">
                    &copy; {{ date('Y') }} Gastro-Verleih Leipzig. {{ __('Alle Rechte vorbehalten.') }}
                </flux:text>

                <div class="public-footer__links">
                    <flux:link
                        href="#"
                        variant="ghost"
                        size="sm"
                        class="public-footer__link"
                    >
                        {{ __('Impressum') }}
                    </flux:link>
                    <flux:link
                        href="#"
                        variant="ghost"
                        size="sm"
                        class="public-footer__link"
                    >
                        {{ __('Datenschutz') }}
                    </flux:link>
                    @auth
                        <flux:link
                            :href="route('dashboard')"
                            variant="ghost"
                            size="sm"
                            class="public-footer__link"
                        >
                            {{ __('Admin') }}
                        </flux:link>
                    @else
                        <flux:link
                            :href="route('login')"
                            variant="ghost"
                            size="sm"
                            class="public-footer__link"
                        >
                            {{ __('Login') }}
                        </flux:link>
                    @endauth
                </div>
            </div>
        </flux:footer>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
