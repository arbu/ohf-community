@extends('layouts.app')

@section('title', __('logistics::suppliers.view_supplier'))

@section('content')

    <h1>{{ $supplier->poi->name_tr }}</h1>
    <p>{{ $supplier->category }}</p>
    <hr>
    <p>
        @icon(map-marker) {!! gmaps_link($supplier->poi->address_tr, $supplier->poi->maps_location) !!}<br>
        @isset($supplier->phone)
            @icon(phone) {!! tel_link($supplier->phone) !!}<br>
        @endisset
        @isset($supplier->email)
            @icon(envelope) {!! email_link($supplier->email) !!}<br>
        @endisset
        @isset($supplier->website)
            @icon(globe) <a href="{{ $supplier->website }}" target="_blank">{{ $supplier->website }}</a><br>
        @endisset
    </p>

    {{-- Remarks --}}
    @isset($supplier->remarks)
        <hr>
        <p><em>{!! nl2br(e($supplier->remarks)) !!}</em></p>
    @endisset

    {{-- Products --}}
    <div class="card mb-3">
        <div class="card-header">@lang('logistics::products.products')</div>
        <ul class="list-group list-group-flush">
            @forelse($supplier->products->sortBy('name') as $product)
                <li class="list-group-item">
                    {{ $product->name_tr }}
                    <small class="text-muted pull-right">{{ $product->category }}</small>
                </li>
            @empty
                <li class="list-group-item">
                    <em>@lang('logistics::products.no_products_registered')</em>
                </li>
            @endforelse
        </ul>
    </div>

@endsection