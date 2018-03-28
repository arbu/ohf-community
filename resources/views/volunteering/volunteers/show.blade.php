@extends('layouts.app')

@section('title', $volunteer->name . ' - ' .__('volunteering.volunteer')) {{-- __('volunteering.view_volunteer') --}}

@section('content')

    <ul class="list-group list-group-flush">

        {{-- Person --}}
        <li class="list-group-item">
            <div class="row">
                <div class="col-sm-2 d-none d-md-block">@lang('people.person')</div>
                <div class="col-sm-auto">
                    @isset($volunteer->gender)
                        @if($volunteer->gender == 'female')@icon(female) 
                        @elseif($volunteer->gender == 'male')@icon(male) 
                        @endif
                    @endisset               
                    {{ $volunteer->name }}
                </div>
                @isset($volunteer->date_of_birth)
                    <div class="col-sm-auto">
                        {{ $volunteer->date_of_birth }} ({{ $volunteer->age }}) 
                    </div>
                @endisset
                @isset($volunteer->nationality)
                    <div class="col-sm-auto">
                        {{ $volunteer->nationality }}
                    </div>
                @endisset
                @isset($volunteer->passport_no)
                    <div class="col-sm-auto">
                        {{ $volunteer->passport_no }}
                    </div>
                @endisset
            </div>
        </li>

        {{-- Address --}}
        <li class="list-group-item">
            <div class="row">
                <div class="col-sm-2 d-none d-md-block">@lang('app.address')</div>
                <div class="col-sm">
                    {{ $volunteer->street }}<br>
                    {{ $volunteer->zip }} {{ $volunteer->city }}<br>
                    {{ $volunteer->country }}<br>
                </div>
            </div>
        </li>

        {{-- Communication --}}
        <li class="list-group-item">
            <div class="row align-items-center">
                <div class="col-sm-2 d-none d-md-block">@lang('app.communication')</div>
                <div class="col-sm">
                    <div class="row">
                        <div class="col-md-auto mb-3 mb-md-0">
                            <a href="{{ email_url($volunteer->user->email) }}" class="btn btn-light btn-block">@icon(envelope) {{ $volunteer->user->email }}</a>
                        </div>
                        @isset($volunteer->phone)
                            <div class="col-md-auto mb-3 mb-md-0">
                                <a href="{{ phone_url($volunteer->phone) }}" class="btn btn-light btn-block">@icon(phone) {{ $volunteer->phone }}</a>
                            </div>
                        @endisset
                        @isset($volunteer->whatsapp)
                            <div class="col-md-auto mb-3 mb-md-0">
                                <a href="{{ whatsapp_url($volunteer->whatsapp) }}" class="btn btn-light btn-block">@icon(whatsapp) {{ $volunteer->whatsapp }}</a>
                            </div>
                        @endisset
                        @isset($volunteer->skype)
                            <div class="col-md-auto">
                                <a href="{{ skype_call_url($volunteer->skype) }}" class="btn btn-light btn-block">@icon(skype) {{ $volunteer->skype }}</a>
                            </div>
                        @endisset
                    </div>
                </div>
            </div>
        </li>

        {{-- Qualifications --}}
        <li class="list-group-item">
            <div class="row">
                <div class="col-sm-2 d-none d-md-block">@lang('volunteering.qualifications')</div>
                <div class="col-sm">
                    <table class="table">
                        <tr>
                            <th class="fit">@lang('volunteering.professions')</th>
                            <td>
                                @isset($volunteer->professions)
                                    {{ $volunteer->professions }}
                                @else
                                    <em>@lang('app.not_specified')</em>
                                @endisset  
                            </td>
                        </tr>
                        <tr>
                            <th class="fit">@lang('volunteering.other_skills')</th>
                            <td>
                                @isset($volunteer->other_skills)
                                    {!! nl2br(e($volunteer->other_skills)) !!}
                                @else
                                    <em>@lang('app.not_specified')</em>
                                @endisset    
                            </td>
                        </tr>
                        <tr>
                            <th class="fit">@lang('volunteering.language_skills')</th>
                            <td>
                                @isset($volunteer->language_skills)
                                    {!! nl2br(e($volunteer->language_skills)) !!}
                                @else
                                    <em>@lang('app.not_specified')</em>
                                @endisset    
                            </td>
                        </tr>
                        <tr>
                            <th class="fit">@lang('volunteering.previous_experience')</th>
                            <td>
                                @isset($volunteer->previous_experience)
                                    {!! nl2br(e($volunteer->previous_experience)) !!}
                                @else
                                    <em>@lang('app.not_specified')</em>
                                @endisset
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </li>

        {{-- Documents --}}
        <li class="list-group-item">
            <div class="row">
                <div class="col-sm-2 d-none d-md-block">@lang('app.documents')</div>
                <div class="col-sm">
                    @foreach($documentTypes as $documentType)
                        <div class="row mb-3">
                            <div class="col-sm-4 col-lg-3">@lang('volunteering.'.$documentType)</div>
                            <div class="col">
                                @isset($volunteer->$documentType)
                                    <a href="{{ route('volunteers.document', $volunteer) }}?type={{ $documentType }}" class="btn btn-light btn-block">@lang('app.download')</a>
                                @else
                                    <em>@lang('volunteering.document_missing').</em>
                                @endisset
                            </div>
                            <div class="col-sm-auto">
                                {!! Form::open(['route' => ['volunteers.uploadDocument', $volunteer], 'files' => true]) !!}
                                    {{ Form::file('file', null, [ 'class' => 'form-control-file'  ]) }}
                                    {{ Form::hidden('type', $documentType) }}
                                    <button type="submit" value="{{ $documentType }}" class="btn btn-primary btn-sm">@lang('app.upload')</button>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </li>
    </ul>

@endsection

@section('script')

@endsection
