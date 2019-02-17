@extends('logistics::layouts.suppliers-products')

@section('title', __('logistics::suppliers.suppliers'))

@section('wrapped-content')
    @if( ! $suppliers->isEmpty() )

        <div class="text-right mb-3">
            <a href="{{ route('logistics.suppliers.index') }}" class="btn btn-sm btn-dark">@icon(list)</a>
            <a href="{{ route('logistics.suppliers.index') }}" class="btn btn-sm btn-secondary">@icon(map)</a>
        </div>

        @foreach ($suppliers as $supplier)
            <div class="card mb-3">
                <div class="card-body">
                    @can('update', $supplier)
                        <a href="{{ route('logistics.suppliers.edit', $supplier) }}" class="pull-right btn btn-sm btn-primary">@icon(pencil)</a>
                    @endcan                    
                    <h5 class="card-title">{{ $supplier->poi->name_tr }}</h5>
                    <h6 class="card-subtitle mb-2 text-muted">{{ $supplier->category }}</h6>
                    @isset($supplier->poi->description)
                        <p class="card-text">{{ $supplier->poi->description }}</p>
                    @endisset
                    <div class="row">
                        <div class="col-sm">
                            <p class="card-text">
                                @icon(map-marker) {!! gmaps_link(str_replace(", ", "<br>", $supplier->poi->address_tr), $supplier->poi->maps_location) !!}
                                @isset($supplier->phone)
                                    <br>@icon(phone) {!! tel_link($supplier->phone) !!}
                                @endisset
                            </p>
                        </div>
                        <div class="col-sm">
                            <p class="card-text">
                                @isset($supplier->email)
                                    @icon(envelope) {!! email_link($supplier->email) !!}<br>
                                @endisset
                                @isset($supplier->website)
                                    @icon(globe) <a href="{{ $supplier->website }}" target="_blank">{{ $supplier->website }}</a><br>
                                @endisset
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{ $suppliers->links() }}

    @else
        @component('components.alert.info')
            @lang('logistics::suppliers.no_suppliers_found')
        @endcomponent
	@endif
@endsection