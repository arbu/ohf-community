@extends('layouts.app')

@section('title', __('library.library'))

@section('content')
    <div class="row">
        <div class="col-md">
            <div class="card mt-3">
                <div class="card-header">@lang('people.person')</div>
                <div class="card-body pb-2">
                    {{ Form::bsAutocomplete('person_id', null, route('people.filterPersons'), ['placeholder' => __('people.search_existing_person')], '') }}
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card mt-3">
                <div class="card-header">@lang('library.book')</div>
                <div class="card-body pb-2">
                    {{ Form::bsAutocomplete('book_id', null, route('library.books.filter'), ['placeholder' => __('library.search_title_author_isbn')], '') }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    function navigateToPerson() {
        if ($('#person_id').val()) {
            document.location='/library/lending/person/' + $('#person_id').val();
        }
    }
    function navigateToBook() {
        if ($('#book_id').val()) {
            document.location='/library/lending/book/' + $('#book_id').val();
        }
    }
    $(function(){
        $('#person_id').on('change', navigateToPerson);
        $('#book_id').on('change', navigateToBook);
    });
@endsection
