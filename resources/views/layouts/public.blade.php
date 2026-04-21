<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-gtc-light font-sans text-gtc-ink antialiased">
        @php($navigationCategories = \App\Models\Category::query()->orderBy('name')->get())

        <flux:header container class="sticky top-0 z-50 border-b border-zinc-200/70 bg-white/95 shadow-sm backdrop-blur-md">
            <flux:navbar>
                <flux:navbar.item href="{{ route('home') }}" class="flex items-center gap-2">
                    <x-app-logo class="size-8" />
                    <span class="text-lg font-semibold text-gtc-green">Gastro-Verleih Leipzig</span>
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <flux:navbar class="hidden md:flex">
                <flux:navbar.item
                    href="{{ route('home') }}"
                    :current="request()->routeIs('home')"
                    class="font-medium !text-gtc-green transition-colors duration-150 hover:!text-gtc-leaf focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gtc-green/35 active:!text-gtc-green/80"
                >
                    {{ __('Home') }}
                </flux:navbar.item>
                <flux:dropdown position="bottom" align="start">
                    <flux:navbar.item
                        icon-trailing="chevron-down"
                        :current="request()->routeIs('category.show')"
                        class="font-medium !text-gtc-green transition-colors duration-150 hover:!text-gtc-leaf focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gtc-green/35 active:!text-gtc-green/80"
                    >
                        {{ __('Produkte') }}
                    </flux:navbar.item>

                    <flux:menu class="!bg-white dark:!bg-white !border-zinc-200 dark:!border-zinc-200">
                        @foreach ($navigationCategories as $navigationCategory)
                            <flux:menu.item
                                :href="route('category.show', $navigationCategory->slug)"
                                class="font-medium !text-gtc-green !bg-white dark:!bg-white transition-colors duration-150 hover:!bg-gtc-mint dark:hover:!bg-gtc-mint hover:!text-gtc-leaf focus-visible:!bg-gtc-mint dark:focus-visible:!bg-gtc-mint focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gtc-green/35 active:!bg-gtc-mint dark:active:!bg-gtc-mint active:!text-gtc-green/80"
                            >
                                {{ $navigationCategory->name }}
                            </flux:menu.item>
                        @endforeach
                    </flux:menu>
                </flux:dropdown>
            </flux:navbar>

            <flux:spacer />

            <flux:navbar>
                @auth
                    <flux:navbar.item
                        href="{{ route('dashboard') }}"
                        icon="layout-grid"
                        class="font-medium !text-gtc-green transition-colors duration-150 hover:!text-gtc-leaf focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gtc-green/35 active:!text-gtc-green/80"
                    >
                        {{ __('Admin') }}
                    </flux:navbar.item>
                @else
                    <flux:navbar.item
                        href="{{ route('login') }}"
                        icon="user-circle"
                        class="font-medium !text-gtc-green transition-colors duration-150 hover:!text-gtc-leaf focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gtc-green/35 active:!text-gtc-green/80"
                    >
                        {{ __('Login') }}
                    </flux:navbar.item>
                @endauth
            </flux:navbar>
        </flux:header>

        <flux:main container class="py-12">
            {{ $slot }}
        </flux:main>

        <flux:footer container class="bg-white border-t border-zinc-200 mt-20 py-12">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-2">
                    <x-app-logo class="size-6" />
                    <span class="font-semibold text-gtc-green tracking-tight">Gastro-Verleih Leipzig</span>
                </div>

                <flux:text variant="subtle" size="sm">
                    &copy; {{ date('Y') }} Gastro-Verleih Leipzig. {{ __('Alle Rechte vorbehalten.') }}
                </flux:text>

                <div class="flex gap-4">
                    <flux:link
                        href="#"
                        variant="ghost"
                        size="sm"
                        class="text-gtc-green transition-colors duration-150 hover:text-gtc-leaf focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gtc-green/35 active:text-gtc-green/80"
                    >
                        {{ __('Impressum') }}
                    </flux:link>
                    <flux:link
                        href="#"
                        variant="ghost"
                        size="sm"
                        class="text-gtc-green transition-colors duration-150 hover:text-gtc-leaf focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gtc-green/35 active:text-gtc-green/80"
                    >
                        {{ __('Datenschutz') }}
                    </flux:link>
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
