<!doctype html>
<html lang="{{ config('app.locale') }}" class="h-100">
    @include('layouts.include.head')
    <body class="h-100 d-flex flex-column">

        <div class="site-wrapper h-100" id="app">

            <div class="site-canvas h-100">

                {{-- Side navigation --}}
                @auth
                    <app-drawer
                        @isset($signet_url) signet-url="{{ $signet_url }}" @endisset
                        app-name="{{ config('app.name') }}"
                        userprofile-url="{{ route('userprofile') }}"
                        user-name="{{ Auth::user()->name }}"
                        render-time="{{ round((microtime(true) - LARAVEL_START)*1000) }}"
                        product-name="{{ config('app.product_name') }}"
                        app-version="{{ $app_version }}"
                        changelog-url="{{ route('changelog') }}"
                        product-url="{{ config('app.product_url') }}"
                        logout-url="{{ route('logout') }}"
                        avatar-image="{{ Auth::user()->avatarUrl() }}"
                        :nav-items='@json($nav)'
                    ></app-drawer>
                @endauth

                {{-- Main --}}
                <main class="d-flex flex-column h-100">

                    {{-- Site header --}}
                    <header class="site-header">
                        <site-nav
                            @auth authorized @endauth
                            @auth avatar-image="{{ Auth::user()->avatarUrl('site_header') }}" @endauth
                            login-url="{{ route('login') }}"
                            userprofile-url="{{ route('userprofile') }}"
                            @if(View::hasSection('title')) title="@yield('title')" @endif
                            home-url="{{ route('home') }}"
                            @isset($signet_url) signet-url="{{ $signet_url }}" @endisset
                            app-name="{{ config('app.name') }}"
                            @if(isset($buttons['back']) && $buttons['back']['authorized']) back-url="{{ $buttons['back']['url'] }}" @endif
                            logout-url="{{ route('logout') }}"
                            @if (isset($buttons) && sizeof($buttons) > 0) :buttons='@json($buttons)' @endif
                            @if (isset($menu) && sizeof($menu) > 0) :menu='@json($menu)' @endif
                        ></site-nav>
                    </header>

                    {{-- Content --}}
                    <article class="site-content container-fluid {{ $content_padding ?? 'pt-3' }}">

                        {{-- Success message --}}
                        @if (session('success'))
                            <div class="snack-message"><span class="pr-1">@icon(check-square)</span> {{ session('success') }}</div>
                        @endif

                        {{-- Info message --}}
                        @if (session('info'))
                            <div class="snack-message">{{ session('info') }}</div>
                        @endif

                        {{-- Error message --}}
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                @icon(exclamation-triangle) {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        {{-- Validation error --}}
                        @if (count($errors) > 0)
                            <div class="alert alert-danger alert-dismissible fade show">
                                @icon(exclamation-triangle) @lang('app.validation_failed')
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        {{-- Content --}}
                        @yield('content')

                        {{-- Floating action button --}}
                        @if(isset($buttons['action']) && $buttons['action']['authorized'] )
                            @include('components.action-button', [ 'route' => $buttons['action']['url'], 'icon' => $buttons['action']['icon_floating'] ])
                        @endif

                    </article>

                    <div id="overlay" class="position-absolute h-100 w-100"></div>

                    <div id="overlay_dark" class="position-absolute h-100 w-100"></div>

                </main>

            </div>

        </div>

        @yield('content-footer')

        <script src="{{ asset('js/app.js') }}?v={{ $app_version }}"></script>
        <script>
            @yield('script')
        </script>
        @yield('footer')

    </body>
</html>
