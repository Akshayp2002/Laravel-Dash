<div
    class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
    {{-- <x-application-logo class="block h-12 w-auto" /> --}}

    <h1 class="mt-8 text-2xl font-medium text-gray-900 dark:text-white">
        Welcome {{ \Auth::user()->name ?? 'Buddy' }} !
    </h1>
</div>

<div
    class="bg-gray-200 dark:bg-gray-800 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8 border-b border-gray-200 dark:border-gray-700">
    <div>

        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                class="size-6 stroke-gray-400">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
            </svg>
            <h2 class="ms-3 text-xl font-semibold text-gray-900 dark:text-white">
                <a href="{{ config('app.url') }}/telescope">Laravel Telescope</a>
            </h2>
        </div>

        <p class="mt-4 text-sm">
            <a href="{{ config('app.url') }}/telescope"
                class="inline-flex items-center font-semibold text-indigo-700 dark:text-indigo-300">
                Open Laravel Telescope

                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                    class="ms-1 size-5 fill-indigo-500 dark:fill-indigo-200">
                    <path fill-rule="evenodd"
                        d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z"
                        clip-rule="evenodd" />
                </svg>
            </a>
        </p>
    </div>

    <div>
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                class="size-6 stroke-gray-400">
                <path stroke-linecap="round"
                    d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" />
            </svg>
            <h2 class="ms-3 text-xl font-semibold text-gray-900 dark:text-white">
                <a href="{{ config('app.url') }}/pulse">Pulse</a>
            </h2>
        </div>

        <p class="mt-4 text-sm">
            <a href="{{ config('app.url') }}/pulse"
                class="inline-flex items-center font-semibold text-indigo-700 dark:text-indigo-300">
                Open Pulse
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                    class="ms-1 size-5 fill-indigo-500 dark:fill-indigo-200">
                    <path fill-rule="evenodd"
                        d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z"
                        clip-rule="evenodd" />
                </svg>
            </a>
        </p>
    </div>
</div>


<div
    class="bg-gray-200 dark:bg-gray-800 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8 border-b border-gray-200 dark:border-gray-700">
    <div>
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                class="size-6 stroke-gray-400">
                <path stroke-linecap="round"
                    d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" />
            </svg>
            <h2 class="ms-3 text-xl font-semibold text-gray-900 dark:text-white">
                <a href="{{ config('app.url') }}/log-viewer">Log Viewer</a>
            </h2>
        </div>

        <p class="mt-4 text-sm">
            <a href="{{ config('app.url') }}/log-viewer"
                class="inline-flex items-center font-semibold text-indigo-700 dark:text-indigo-300">
                Open Log Viewer
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                    class="ms-1 size-5 fill-indigo-500 dark:fill-indigo-200">
                    <path fill-rule="evenodd"
                        d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z"
                        clip-rule="evenodd" />
                </svg>
            </a>
        </p>
    </div>
    <div>
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                class="size-6 stroke-gray-400">
                <path stroke-linecap="round"
                    d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" />
            </svg>
            <h2 class="ms-3 text-xl font-semibold text-gray-900 dark:text-white">
                <a href="{{ config('app.url') }}/horizon">Laravel Horizon</a>
            </h2>
        </div>

        <p class="mt-4 text-sm">
            <a href="{{ config('app.url') }}/horizon"
                class="inline-flex items-center font-semibold text-indigo-700 dark:text-indigo-300">
                Open Laravel Horizon
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                    class="ms-1 size-5 fill-indigo-500 dark:fill-indigo-200">
                    <path fill-rule="evenodd"
                        d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z"
                        clip-rule="evenodd" />
                </svg>
            </a>
        </p>
    </div>
</div>


<div
    class="bg-gray-200 dark:bg-gray-800 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8 border-b border-gray-200 dark:border-gray-700">
    <div>
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                class="size-6 stroke-gray-400">
                <path stroke-linecap="round"
                    d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" />
            </svg>
            <h2 class="ms-3 text-xl font-semibold text-gray-900 dark:text-white">
                <a href="{{ config('app.url') }}/docs/api">Scramble doc</a>
            </h2>
        </div>

        <p class="mt-4 text-sm">
            <a href="{{ config('app.url') }}/docs/api"
                class="inline-flex items-center font-semibold text-indigo-700 dark:text-indigo-300">
                Open Scramble doc
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                    class="ms-1 size-5 fill-indigo-500 dark:fill-indigo-200">
                    <path fill-rule="evenodd"
                        d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z"
                        clip-rule="evenodd" />
                </svg>
            </a>
        </p>
    </div>
    <div>
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                class="size-6 stroke-gray-400">
                <path stroke-linecap="round"
                    d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" />
            </svg>
            <h2 class="ms-3 text-xl font-semibold text-gray-900 dark:text-white">
                <a href="https://laracasts.com">Auditable Console</a>
            </h2>
        </div>

        <p class="mt-4 text-sm">
            <a href="{{ route('audit') }}"
                class="inline-flex items-center font-semibold text-indigo-700 dark:text-indigo-300">
                Auditable
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                    class="ms-1 size-5 fill-indigo-500 dark:fill-indigo-200">
                    <path fill-rule="evenodd"
                        d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z"
                        clip-rule="evenodd" />
                </svg>
            </a>
        </p>
    </div>
</div>
