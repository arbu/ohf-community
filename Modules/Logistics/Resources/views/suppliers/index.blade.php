@extends('logistics::layouts.suppliers-products')

@section('title', __('logistics::suppliers.suppliers'))

@section('wrapped-content')

    <div class="form-row">
        <div class="col">
            {!! Form::open(['route' => ['logistics.suppliers.index'], 'method' => 'get']) !!}
                <div class="input-group mb-3">
                    {{ Form::search('filter', isset($filter) ? $filter : null, [ 'class' => 'form-control form-control-sm', 'placeholder' => __('app.filter') . '...' ]) }}
                    <div class="input-group-append">
                        <button class="btn btn-primary btn-sm" type="submit">@icon(search)</button> 
                        @if(isset($filter))
                            <a class="btn btn-secondary btn-sm" href="{{ route('logistics.suppliers.index', ['reset_filter']) }}">@icon(eraser)</a> 
                        @endif
                    </div>
                </div>
            {!! Form::close() !!}
        </div>
        @if( ! $suppliers->isEmpty() )
            <div class="col-auto">
                <div class="btn-group" role="group">
                    <a href="{{ route('logistics.suppliers.index') }}" class="btn btn-sm btn-dark">@icon(list)</a>
                    <a href="{{ route('logistics.suppliers.index') }}" class="btn btn-sm btn-secondary">@icon(map)</a>
                </div>
            </div>
        @endif
    </div>

    @if( ! $suppliers->isEmpty() )
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
                    <div class="row mb-3">
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
                    <a href="#" class="card-link text-dark">@icon(shopping-basket) Products</a>
                    <a href="#" class="card-link text-dark">@icon(file-text-o) Services</a>
                    {{-- <p class="card-text"><small class="text-muted"><a href="" class="text-muted">Products: Apples, Pears, Wrenches, ...</a></small></p> --}}
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