<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head-app')
    </head>
    <body class="min-h-screen bg-neutral-100 antialiased dark:bg-zinc-800">
        <div class="bg-muted flex min-h-svh flex-col items-center justify-center gap-6 p-6 dark:bg-zinc-900 md:p-10">
            <div class="flex w-full max-w-md flex-col gap-6">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                    <span class="flex h-9 w-9 items-center justify-center rounded-md bg-white dark:bg-zinc-800">
                        <x-app-logo-icon class="size-7 object-contain" />
                    </span>

                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                </a>

                <div class="flex flex-col gap-6">
                    <div class="rounded-xl border bg-white text-stone-800 shadow-xs dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                        <div class="px-10 py-8">{{ $slot }}</div>
                    </div>
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
