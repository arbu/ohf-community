<!doctype html>
<html lang="{{ config('app.locale') }}" class="h-100">
    @include('layouts.include.head')
    <body class="h-100 d-flex flex-column">

        <app-root
            id="app"
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
            @auth authorized @endauth
            @auth avatar-image-header="{{ Auth::user()->avatarUrl('site_header') }}" @endauth
            login-url="{{ route('login') }}"
            @if(View::hasSection('title')) title="@yield('title')" @endif
            home-url="{{ route('home') }}"
            @if(isset($buttons['back']) && $buttons['back']['authorized']) back-url="{{ $buttons['back']['url'] }}" @endif
            @if (isset($buttons) && sizeof($buttons) > 0) :buttons='@json($buttons)' @endif
            @if (isset($menu) && sizeof($menu) > 0) :menu='@json($menu)' @endif
            @isset($content_padding) content-padding-class="{{ $content_padding }}" @endisset
        >

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

        </app-root>

        @yield('content-footer')

        <script src="{{ asset('js/app.js') }}?v={{ $app_version }}"></script>
        <script>
            @yield('script')
        </script>
        @yield('footer')

    </body>
</html>
