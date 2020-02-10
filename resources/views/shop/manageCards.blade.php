@extends('layouts.app')

@section('title', __('shop.shop'))

@section('content')

    <div id="shop-app">
        @php
            $lang_arr = lang_arr([
                'app.date',
                'app.actions',
                'app.loading',
                'shop.cards',
                'shop.no_suitable_cards_found',
                'shop.delete_cards',
                'shop.non_redeemed_cards',
                'shop.expired',
                'shop.redeemed_cards',
                'shop.no_cards_redeemed_so_far_today'
            ]);
        @endphp

        <shop-card-manager-page
            list-cards-url="{{ route('shop.cards.listRedeemedToday') }}"
            summary-url="{{ route('shop.cards.listNonRedeemedByDay') }}"
            delete-url="{{ route('shop.cards.deleteNonRedeemedByDay') }}"
            :lang='@json($lang_arr)'
        ></shop-card-manager-page>
    </div>

@endsection

@section('footer')
    <script src="{{ asset('js/shop.js') }}?v={{ $app_version }}"></script>
@endsection