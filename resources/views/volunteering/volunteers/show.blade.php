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
        @php
            $qualifications = [
                __('volunteering.professions') => $volunteer->professions,
                __('volunteering.other_skills') => $volunteer->other_skills,
                __('volunteering.language_skills') => $volunteer->language_skills,
                __('volunteering.previous_experience') => $volunteer->previous_experience,
            ]
        @endphp
        <li class="list-group-item">
            <div class="row">
                <div class="col-sm-2 d-none d-md-block">@lang('volunteering.qualifications')</div>
                <div class="col-sm">
                    <table>
                        @foreach($qualifications as $k => $v)
                        <tr>
                            <th class="fit align-top d-block d-sm-table-cell pb-sm-2 pr-sm-4">{{ $k }}</th>
                            <td class="d-block d-sm-table-cell pb-2">
                                @isset($v)
                                    {{ $v }}
                                @else
                                    <em>@lang('app.not_specified')</em>
                                @endisset  
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </li>

        {{-- Documents --}}
        <li class="list-group-item">
            <div class="row">
                <div class="col-sm-2 d-none d-md-block">@lang('app.documents')</div>
                <div class="col-sm">
                    @if(count($volunteer->documents) > 0)
                        <div class="card-deck">
                            @foreach($volunteer->documents as $document)
                                <div class="card mb-4" style="max-width: 18rem;">
                                    @if($document->extension == 'png' || $document->extension == 'jpeg')
                                        <img class="card-img-top" src="{{ base64_img(Storage::get($document->file)) }}" alt="@lang('volunteering.' . $document->type)">
                                    @elseif($document->extension == 'pdf')
                                        <p class="card-text bg-info text-light text-center lead py-5">@icon(file-pdf-o) PDF</p>
                                    @else
                                        <p class="card-text bg-info text-light text-center lead py-5">@icon(file-o) {{ $document->extension }}</p>
                                    @endif
                                    <div class="card-body">
                                        <h5 class="card-title">@lang('volunteering.' . $document->type)</h5>
                                        @isset($document->remarks)
                                            <p class="card-text">{!! nl2br(e($document->remarks)) !!}</p>
                                        @endisset
                                        <p class="card-text"><small class="text-muted">Uploaded {{ $document->created_at->toDateString() }}</small></p>
                                    </div>
                                    @can('update', $volunteer)
                                        <div class="card-footer">
                                            <a href="{{ route('volunteers.document', [$volunteer, $document]) }}" class="btn btn-primary btn-sm">@lang('app.download')</a>
                                            <a href="" class="text-danger btn btn-link btn-sm pull-right">@lang('app.delete')</a>
                                        </div>
                                    @endcan
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p><em>@lang('volunteering.no_documents_uploaded_yet')</em></p>
                    @endif
                    @can('update', $volunteer)
                        {!! Form::open(['route' => ['volunteers.uploadDocument', $volunteer], 'files' => true]) !!}
                            <div class="card mb-4">
                                <div class="card-header">@lang('volunteering.upload_document')</div>
                                <div class="card-body">
                                    <div class="form-group">
                                        {{ Form::file('file', null, [ 'class' => 'form-control-file'  ]) }}
                                    </div>
                                    {{ Form::bsSelect('type', \App\VolunteerDocument::types(), null, [], __('app.type')) }}
                                    {{ Form::bsTextarea('remarks', null, [ 'rows' => 2 ], __('app.remarks')) }}
                                    {{ Form::bsSubmitButton(__('app.upload'), 'upload') }}
                                </div>
                            </div>
                        {!! Form::close() !!}
                    @endcan       
                </div>
            </div>
        </li>
    </ul>

@endsection

@section('script')

@endsection
