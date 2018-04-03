@extends('layouts.app')

@section('title', 'Apply for a trip')

@section('content')

    {!! Form::open(['route' => ['volunteering.profile.storeTrip']]) !!}

        <div class="card mb-4">
            <div class="card-header">Journey</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md">
                        <div class="row">
                            <label class="col" for="arrival">Arrival date</label>
                            <label class="col" for="departure">Departure date</label>
                        </div>
                        <div class="input-group input-daterange" id="datepicker">
                            <input type="text" class="form-control" name="arrival" id="arrival" />
                            <span class="input-group-addon px-3">to</span>
                            <input type="text" class="form-control" name="departure" id="departure" />
                        </div>
                    </div>
                    <div class="col-md-auto mt-4 mt-md-0">
                        <p class="d-none d-md-block">Accommodation</p>
                        {{ Form::bsCheckbox('need_accomodation', null, [], 'I need accommodation') }}
                    </div>
                </div>
            </div>
        </div>
    
        <div class="card mb-4">
            <div class="card-header">Engagement</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md">
                        <p>Preferred type of work</p>
                        @foreach($type_of_work as $tow)
                            {{ Form::bsCheckbox('type_of_work', $tow, [], $tow) }}
                        @endforeach
                        {{ Form::bsCheckbox('type_of_work', 'Other', [], 'Other') }}
                        {{ Form::bsText('other_type_of_work', null, [ ], '') }}
                    </div>
                </div>
            </div>
        </div>

        <p>
            {{ Form::bsSubmitButton('Apply') }}
        </p>

    {!! Form::close() !!}

@endsection

@section('script')
    $('#datepicker').datepicker({
        format: "yyyy-mm-dd",
        todayHighlight: true,
        calendarWeeks: true
    });
@endsection
