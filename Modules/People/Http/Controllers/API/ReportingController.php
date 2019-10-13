<?php

namespace Modules\People\Http\Controllers\API;

use App\Http\Controllers\Reporting\BaseReportingController;

use Modules\People\Entities\Person;

use Illuminate\Http\Request;

use Carbon\Carbon;

class ReportingController extends BaseReportingController
{
    /**
     * Nationalities
     */
    function nationalities()
    {
        return response()->json([
            'labels' => null,
            'datasets' => collect(Person::getNationalities())
                ->map(function($e){ return [$e]; })
                ->toArray(),
        ]);
    }

    /**
     * Gender distribution
     */
    function genderDistribution()
    {
        $gender = Person::getGenderDistribution();
        return response()->json([
            'labels' => array_keys($gender),
            'datasets' => [array_values($gender)],
        ]);
    }

    /**
     * Age distribution
     */
    function ageDistribution()
    {
        $data = Person::getAgeDistribution();
        return response()->json([
            'labels' => array_keys($data),
            'datasets' => [array_values($data)],
        ]);
    }

    /**
     * Registrations per day
     */
    function registrationsPerDay(Request $request)
    {
        list($dateFrom, $dateTo) = parent::getDatePeriodFromRequest($request, 30);

        $data = Person::getRegistrationsPerDay($dateFrom, $dateTo);
        return response()->json([
            'labels' => array_keys($data),
            'datasets' => [
                'Registrations' => array_values($data),
            ]
        ]);
    }

}