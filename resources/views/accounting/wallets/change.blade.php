@extends('layouts.app')

@section('title', __('accounting.change_wallet'))

@section('content')

    <div class="list-group">
        @forelse($wallets as $wallet)
            <a href="{{ route('accounting.wallets.transactions.index', $wallet) }}" class="list-group-item list-group-item-action">
                {{ $wallet->name }}
                @if($wallet->is_default)
                    <b>(@lang('app.default'))</b>
                @endif
                <span class="float-right">{{ number_format($wallet->amount, 2) }}</span>
            </a>
        @empty
            <a class="list-group-item">
                @lang('accounting.no_wallets_found')
            </a>
        @endforelse
    </div>

@endsection
