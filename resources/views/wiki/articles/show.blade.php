@extends('layouts.app')

@section('title', __('wiki.show_article'))

@section('content')

    <h1>{{ $article->title }}</h1>
    <div class="row">
        <div class="col-lg">
            {!! $article->content !!}
        </div>

        {{-- Side Addons --}}
        @if ($article->addons->count() > 0)
            <div class="col-lg-5">
                {{-- <div class="card">
                    <div class="card-body"> --}}
                        @foreach($article->addons->sortBy('sort') as $addon)
                            @if(view()->exists('wiki.articles.addons.' . $addon->type))
                                <h4>
                                    @isset($addon->caption)
                                        {{ $addon->caption }}
                                    @else 
                                        @lang('wiki.' . $addon->type)
                                    @endif
                                </h4>
                                @include('wiki.articles.addons.' . $addon->type, $addon->args)
                            @else
                                @component('components.alert.error')
                                    @lang('wiki.addon_view_missing', [ 'type' => $addon->type ])
                                @endcomponent
                            @endif
                        @endforeach
                    {{-- </div>
                </div> --}}
            @endif
        </div>
    </div>

    <hr>

    {{-- Tags --}}
    @if(count($article->tags) > 0)
        <p>
            <strong>@lang('app.tags'):</strong>
            @foreach($article->tags->sortBy('name') as $tag)
                <a href="{{ route('wiki.articles.tag', $tag) }}">{{ $tag->name }}</a>@if(!$loop->last), @endif
            @endforeach
        </p>
    @endif

    @php
        $audit = $article->audits()->with('user')->latest()->first();
    @endphp
    @isset($audit)
        <p><small><span title="{{ $audit->getMetadata()['audit_created_at'] }}">{{ (new Carbon\Carbon($audit->getMetadata()['audit_created_at']))->diffForHumans() }}</span> @lang('app.updated_by') {{ $audit->getMetadata()['user_name'] }}</small></p>
    @endif
@endsection
