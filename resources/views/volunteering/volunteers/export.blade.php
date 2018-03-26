<table>
    <thead>
        <tr>
            <th>@lang('app.first_name')</th>
            <th>@lang('app.last_name')</th>
            <th>@lang('app.street')</th>
            <th>@lang('app.zip')</th>
            <th>@lang('app.city')</th>
            <th>@lang('app.country')</th>
            <th>@lang('app.nationality')</th>
            <th>@lang('app.date_of_birth')</th>
            <th>@lang('app.age')</th>
            <th>@lang('app.gender')</th>
            <th>@lang('app.email')</th>
            <th>@lang('app.phone')</th>
            <th>@lang('volunteering.whatsapp')</th>
            <th>@lang('volunteering.skype')</th>
            <th>@lang('volunteering.passort_no')</th>
            <th>@lang('volunteering.profession')</th>
            <th>@lang('volunteering.driver')</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($volunteers as $volunteer)
            <tr>
                <td>{{ $volunteer->first_name }}</td>
                <td>{{ $volunteer->last_name }}</td>
                <td>{{ $volunteer->street }}</td>
                <td>{{ $volunteer->zip }}</td>
                <td>{{ $volunteer->city }}</td>
                <td>{{ $volunteer->country }}</td>
                <td>{{ $volunteer->nationality }}</td>
                <td>{{ $volunteer->date_of_birth }}</td>
                <td>{{ $volunteer->age }}</td>
                <td>
                    @isset($volunteer->gender) 
                        @if($volunteer->gender == 'female')@lang('people.female')
                        @elseif($volunteer->gender == 'male')@lang('people.male')
                        @endif
                    @endisset
                </td>
                <td>{{ $volunteer->user->email }}</td>
                <td>{{ $volunteer->phone }}</td>
                <td>{{ $volunteer->whatsapp }}</td>
                <td>{{ $volunteer->skype }}</td>
                <td>{{ $volunteer->passport_no }}</td>
                <td>{{ $volunteer->profession }}</td>
                <td>@if($volunteer->driving_licence != null) @lang('app.yes') @else @lang('app.no') @endif</td>
            </tr>
        @endforeach
    </tbody>
</table>
