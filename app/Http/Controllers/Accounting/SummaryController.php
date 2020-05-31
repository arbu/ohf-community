<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;

class SummaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function summary()
    {
        $this->authorize('view-accounting-summary');

        return view('accounting.transactions.summary');
    }

}
