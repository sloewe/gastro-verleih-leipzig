<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-gtc-light font-sans text-gtc-ink antialiased">
        <flux:header container class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-zinc-200/50">
            <flux:navbar>
                <flux:navbar.item href="{{ route('home') }}" class="flex items-center gap-2">
                    <x-app-logo class="size-8" />
                    <span class="text-lg font-semibold text-gtc-green">Green Temper Coffee</span>
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <flux:navbar class="hidden md:flex">
                <flux:navbar.item href="{{ route('home') }}" :current="request()->routeIs('home')">{{ __('Home') }}</flux:navbar.item>
                {{-- Weitere Nav-Items können hier folgen --}}
            </flux:navbar>

            <flux:spacer />

            <flux:navbar>
                @auth
                    <flux:navbar.item href="{{ route('dashboard') }}" icon="layout-grid">{{ __('Admin') }}</flux:navbar.item>
                @else
                    <flux:navbar.item href="{{ route('login') }}" icon="user-circle">{{ __('Login') }}</flux:navbar.item>
                @endauth
            </flux:navbar>
        </flux:header>

        <flux:main container class="py-12">
            {{ $slot }}
        </flux:main>

        <footer class="bg-white border-t border-zinc-200 mt-20 py-12">
            <flux:container>
                <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="flex items-center gap-2">
                        <x-app-logo class="size-6" />
                        <span class="font-semibold text-gtc-green tracking-tight">Green Temper Coffee</span>
                    </div>

                    <flux:text variant="subtle" size="sm">
                        &copy; {{ date('Year') }} Green Temper Coffee. {{ __('Alle Rechte vorbehalten.') }}
                    </flux:text>

                    <div class="flex gap-4">
                        <flux:link href="#" variant="ghost" size="sm">{{ __('Impressum') }}</flux:link>
                        <flux:link href="#" variant="ghost" size="sm">{{ __('Datenschutz') }}</flux:link>
                    </div>
                </div>
            </flux:container>
        </footer>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
