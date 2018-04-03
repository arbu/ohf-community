@extends('layouts.app')

@section('title', __('volunteering.volunteer')) {{-- __('volunteering.view_volunteer') --}}

@section('content')

    <div class="row">
        <div class="col-sm-3">

            <div class="card mb-4">
                <div class="card-body">

                    {{-- Person --}}
                    <h5 class="card-title">{{ $volunteer->name }}</h5>
                    @isset($volunteer->gender)
                        @if($volunteer->gender == 'female')@icon(female) 
                        @elseif($volunteer->gender == 'male')@icon(male) 
                        @endif
                    @endisset
                    @isset($volunteer->date_of_birth)
                        {{ $volunteer->date_of_birth }} ({{ $volunteer->age }})<br>
                    @endisset
                    @isset($volunteer->nationality)
                        {{ $volunteer->nationality }}<br>
                    @endisset
                    @isset($volunteer->passport_no)
                        {{ $volunteer->passport_no }}<br>
                    @endisset

                    <hr>

                    {{-- Address --}}
                    {{ $volunteer->street }}<br>
                    {{ $volunteer->zip }} {{ $volunteer->city }}<br>
                    {{ $volunteer->country }}<br>

                </div>
                
                {{-- Communication --}}
                <div class="list-group list-group-flush">
                    <a href="{{ email_url($volunteer->user->email) }}" class="list-group-item">@icon(envelope) {{ $volunteer->user->email }}</a>
                    @isset($volunteer->phone)
                        <a href="{{ phone_url($volunteer->phone) }}" class="list-group-item">@icon(phone) {{ $volunteer->phone }}</a>
                    @endisset
                    @isset($volunteer->whatsapp)
                        <a href="{{ whatsapp_url($volunteer->whatsapp) }}" class="list-group-item">@icon(whatsapp) {{ $volunteer->whatsapp }}</a>
                    @endisset
                    @isset($volunteer->skype)
                        <a href="{{ skype_call_url($volunteer->skype) }}" class="list-group-item">@icon(skype) {{ $volunteer->skype }}</a>
                    @endisset
                </div>

            </div>

            {{-- Qualifications --}}
            @php
                $qualifications = [
                    __('volunteering.professions') => $volunteer->professions,
                    __('volunteering.language_skills') => $volunteer->language_skills,
                    __('volunteering.other_skills') => $volunteer->other_skills,
                    __('volunteering.previous_experience') => $volunteer->previous_experience,
                ]
            @endphp
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">@lang('volunteering.qualifications')</h5>
                    <dl class="mb-0">
                        @foreach($qualifications as $k => $v)
                            <dt>{{ $k }}</dt>
                            <dd>
                                @isset($v)
                                    {{ $v }}
                                @else
                                    <em>@lang('app.not_specified')</em>
                                @endisset  
                            </dd>
                        @endforeach
                    </dl>
                </div>
            </div>
            
        </div>
        <div class="col-sm">

            {{-- Documents --}}
            <h4 class="mb-3">@lang('app.documents')</h4>
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
                                <a href="{{ route('volunteering.documents.download', [$volunteer, $document]) }}" class="btn btn-primary btn-sm">@lang('app.download')</a>
                                <form method="POST" action="{{ route('volunteering.documents.destroy', [$volunteer, $document]) }}" class="d-inline">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                    {{ Form::button(__('app.delete'), [ 'type' => 'submit', 'class' => 'text-danger btn btn-link btn-sm pull-right delete-confirmation', 'data-confirmation' => __('volunteering.really_delete_document', ['document' => __('volunteering.' . $document->type)]) ] ) }}
                                </form>
                            </div>
                        @endcan
                    </div>
                @endforeach
                @can('update', $volunteer)
                    <div class="card mb-4" style="max-width: 18rem;">
                        <div class="card-body text-center align-items-center d-flex justify-content-center">
                            <a class="display-3 text-primary my-5" href="javascript:;" id="upload_document_btn">@icon(upload)</a>
                        </div>
                    </div>                    
                @endcan
            </div>
            @cannot('update', $volunteer)
                @if(count($volunteer->documents) == 0)
                    <p><em>@lang('volunteering.no_documents_uploaded_yet')</em></p>
                @endif
            @endcannot

            {{-- Trips --}}
            <h4 class="mb-3">@lang('volunteering.trips')</h4>
            <p><em>@lang('volunteering.no_trips_until_now')</em></p>

        </div>
    </div>
@endsection

@section('content-footer')
    @can('update', $volunteer)
        <div class="modal" id="resourceModal" tabindex="-1" role="dialog" aria-labelledby="resourceModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    {!! Form::open(['route' => ['volunteering.documents.store', $volunteer], 'files' => true]) !!}
                        <div class="modal-header">
                            <h5 class="modal-title" id="resourceModalLabel">@lang('volunteering.upload_document')</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body pb-0">
                            {{ Form::bsFile('file', [ 'accept' => '.jpg,.jpeg,.bmp,.png,.pdf' ], __('app.choose_file')) }}
                            {{ Form::bsSelect('type', \App\VolunteerDocument::types(), null, [ 'id' => 'type' ], __('app.type')) }}
                            {{ Form::bsTextarea('remarks', null, [ 'rows' => 2, 'id' => 'remarks' ], __('app.remarks')) }}
                        </div>
                        <div class="modal-footer">
                            {{ Form::bsSubmitButton(__('app.upload'), 'upload') }}
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    @endcan
@endsection

@section('script')
    $(function(){
        $('#upload_document_btn').on('click', function(){
            $('#resourceModal').modal('show');
            $('#file').click();
        });
        $('#file').on('change',function(){
            $('#type').focus();
        });
    });
@endsection