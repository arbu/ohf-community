<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Support\Accounting\Webling\Entities\Period;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WeblingApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $this->authorize('book-accounting-transactions-externally');

        return view('accounting.webling.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function prepare(Request $request)
    {
        $this->authorize('book-accounting-transactions-externally');

        $this->validateRequest($request);

        return view('accounting.webling.prepare', [
            'period_id' => $request->period,
            'from' => $request->from,
            'to' => $request->to,
        ]);
    }

    private function validateRequest($request)
    {
        $request->validate([
            'period' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $period = Period::find($value);
                    if ($period === null) {
                        return $fail('Period does not exist.');
                    }
                    if ($period->state != 'open') {
                        return $fail('Period \'' . $period->title . '\' is not open.');
                    }
                },
            ],
            'from' => [
                'required',
                'date',
            ],
            'to' => [
                'required',
                'date',
            ],
        ]);
    }
}
