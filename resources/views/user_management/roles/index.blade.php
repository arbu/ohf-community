@extends('user_management.layouts.user-roles')

@section('title', __('app.users_and_roles'))

@section('wrapped-content')

    <div id="user-management-app">
        <role-management-page
            api-url="{{ route('api.roles.index') }}"
            user-list-api-url="{{ route('api.users.index') }}"
        ></role-management-page>
    </div>

@endsection

@section('footer')
    <script src="{{ asset('js/user_management.js') }}?v={{ $app_version }}"></script>
@endsection
