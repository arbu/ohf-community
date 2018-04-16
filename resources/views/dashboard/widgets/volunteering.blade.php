<div class="card mb-4">
    <div class="card-header">
        @lang('volunteering.volunteer_coordination')
        <a class="pull-right" href="{{ route('volunteering.volunteers.index')  }}">@lang('app.manage')</a>
    </div>
    <div class="card-body pb-2">
        <p>
            @lang('volunteering.there_are_n_volunteers_in_our_datanase', [
                'num' => '<strong>' . $volunteers . '</strong>',
            ])<br>
            @if($applications > 0)
                @lang('volunteering.there_are_n_open_applications', [
                    'num' => '<strong><a href="' . route('volunteering.trips.index') . '">' . $applications . '</a></strong>',
                ])<br>
            @endif
        </p>
    </div>
</div>
