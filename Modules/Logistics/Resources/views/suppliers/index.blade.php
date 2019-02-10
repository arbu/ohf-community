@extends('layouts.app')

@section('title', __('logistics::suppliers.suppliers'))

@section('content')

    @if( ! $suppliers->isEmpty() )
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>@lang('app.name')</th>
                        <th class="d-none d-sm-table-cell">@lang('app.address')</th>
                        <th>@lang('app.category')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($suppliers as $supplier)
                        <tr>
                            <td>
                                <a href="{{ route('logistics.suppliers.show', $supplier) }}">{{ $supplier->name_tr }}</a>
                            </td>
                            <td class="d-none d-sm-table-cell">
                                {{ $supplier->address_tr }}
                            </td>
                            <td>{{ $supplier->category }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $suppliers->links() }}
    @else
        @component('components.alert.info')
            @lang('logistics::suppliers.no_suppliers_found')
        @endcomponent
	@endif
	
@endsection
