<!doctype html>
<html lang="{{ config('app.locale') }}">
	@include('layouts.include.head')
    <body class="p-0 m-0 bg-light">

		<div class="container">
			<div class="row justify-content-center">
				<div class="">

					<div class="px-5 px-sm-0 text-center">
						<img src="{{ asset('/img/logo_login.png') }}" class="img-fluid text-center my-sm-0 p-4" style="max-width: 492px" />
					</div>

					<div class="card mb-4">
						<div class="card-body p-md-5">

							<h1 class="display-4 text-center mb-4 mb-md-5">@yield('title')</h1>

							@yield('content')

						</div>
					</div>
					<p class="text-center">
						<small>
							@foreach (language()->allowed() as $code => $name)
								<a href="{{ language()->back($code) }}">@if( App::getLocale() == $code )<strong>{{ $name }}</strong>@else{{ $name }}@endif</a> &nbsp;
							@endforeach
						</small>
					</p>
					<p class="text-center">
						<small><a href="{{ route('userPrivacyPolicy') }}" target="_blank">@lang('app.privacy_policy')</a></small>
					</p>
				</div>
			</div>
		</div>

        <script src="{{ asset('js/app.js') }}?v={{ $app_version }}"></script>

    </body>
</html>