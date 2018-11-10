<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Util\BadgeCreator;
use App\Helper;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;

class BadgeMakerController extends Controller
{
    private static function getSources() {
        $sources = [
            [
                'key' => 'helpers',
                'label' => __('people.helpers'),
                'allowed' => Auth::user()->can('list', Helper::class),
            ],
            [
                'key' => 'file',
                'label' => __('app.file'),
                'allowed' => true,
            ],
        ];
        return collect($sources)->where('allowed', true)->pluck('label', 'key');
    }

    public function index(Request $request) {
        $sources = self::getSources();
        $source = $request->has('source') && $sources->keys()->contains($request->source) 
            ? $request->source
            : $sources->keys()->first();

        return view('badges.index', [
            'source' => $source,
            'sources' => $sources,
        ]);
    }

    public function make(Request $request) {
        $validator = Validator::make($request->all(), [
            'source' => [
                'required',
                Rule::in(self::getSources()->keys()),
            ],
            'file' => [
                'file',
                'required_if:source,file'
            ],
        ])->validate();

        $persons = [];
        $title = null;
        
        // Source: Helpers
        if ($request->source == 'helpers') {
            $persons = Helper::active()
                ->get()
                ->map(function($helper){ return self::helperToBadgePerson($helper); });
            $title = __('people.badges') . ' ' .__('people.helpers');
        }
        // Source: File
        else if ($request->source == 'file') {
            $file = $request->file('file');
            \Excel::selectSheets()->load($file, function($reader) use(&$persons) {
                $reader->each(function($sheet) use(&$persons) {
                    $sheet->each(function($row) use(&$persons) {
                        if (isset($row->name) && isset($row->position))
                        $persons[] = [
                            'name' => $row->name,
                            'position' => $row->position,
                            'id' => $row->id ?? null,
                        ];
                    });
                });
            });
            $title = __('people.badges');
        }

        // Ensure there are records
        Validator::make([], [])
            ->after(function ($validator) use($persons) {
                if (count($persons) == 0) {
                    $validator->errors()->add('source', __('app.empty_data_source'));
                }
            })
            ->validate();

        $badgeCreator = new BadgeCreator($persons);
        $badgeCreator->createPdf($title);
    }

    private static function helperToBadgePerson($helper) {
        return [
            'name' => $helper->person->nickname ?? $helper->person->name,
            'position' => is_array($helper->responsibilities) ? implode(', ', $helper->responsibilities) : '',
            'id' =>  substr($helper->person->public_id, 0, 7),
        ];
    }
}
