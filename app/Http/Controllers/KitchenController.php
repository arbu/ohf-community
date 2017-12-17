<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Article;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class KitchenController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request) {
        Validator::make($request->all(), [
            'date' => 'date',
            'type' => 'in:incomming,outgoing',
        ])->validate();

        if (isset($request->date)) {
            $date = new Carbon($request->date);
        } else {
            $date = Carbon::today();
        }

        return view('kitchen.index', [
            'date' => $date,
            'data' => [
                'incomming' => Article::where('type', 'incomming')->orderBy('name')->get(),
                'outgoing' => Article::where('type', 'outgoing')->orderBy('name')->get(),
            ],
            'activeType' => isset($request->type) ? $request->type : session('kitchenArticleType', 'incomming'),
        ]);
    }

    public function store(Request $request) {
        Validator::make($request->all(), [
            'date' => 'date',
            'type' => 'in:incomming,outgoing',
        ])->validate();

        if (isset($request->date)) {
            $date = new Carbon($request->date);
        } else {
            $date = Carbon::today();
        }

        $request->session()->flash('kitchenArticleType', $request->type);

        $updated = false;

        if (isset($request->value)) {
            foreach ($request->value as $k => $v) {
                if (isset($v) && is_numeric($v)) {
                    $article = Article::find($k);
                    if ($article != null) {
                        $value = $v - $article->dayTransactions($date);
                        if ($value != 0) {
                            $transaction = new Transaction();
                            $transaction->value = $value;
                            if (!$date->isToday()) {
                                $transaction->created_at = $date->endOfDay();
                            }
                            $article->transactions()->save($transaction);
                            $updated = true;
                        }
                    }
                }
            }
        }

        if (!empty($request->new_name) && !empty($request->new_value) && !empty($request->new_unit)) {
            $article = Article::where('name', $request->new_name)->where('type', $request->type)->first();
            if ($article == null) {
                $article = new Article();
                $article->name = $request->new_name;
                $article->unit = $request->new_unit;
                $article->type = $request->type;
                $article->save();
            }
            $transaction = new Transaction();
            $transaction->value = $request->new_value;
            if (!$date->isToday()) {
                $transaction->created_at = $date->endOfDay();
            }
            $article->transactions()->save($transaction);
            $updated = true;
        }

        return redirect()->route('kitchen.index')
            ->with('info', $updated ? 'Values have been updated.' : 'No changes.');

    }

    public function showArticle(Article $article) {
        $date_start = Carbon::today()->startOfMonth();
        while ($date_start->dayOfWeek != Carbon::MONDAY) {
            $date_start->subDay();
        }

        $date_end = Carbon::today()->endOfMonth();
        while ($date_end->dayOfWeek != Carbon::SUNDAY) {
            $date_end->addDay();
        }

        return view('kitchen.article', [
            'article' => $article,
            'date_start' => $date_start,
            'date_end' => $date_end,
        ]);
    }
}
