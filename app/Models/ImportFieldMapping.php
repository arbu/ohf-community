<?php

namespace App\Models;

use Iatstuti\Database\Support\NullableFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ImportFieldMapping extends Model
{
    use NullableFields;

    protected $nullable = [
        'to',
    ];

    protected $fillable = [
        'model',
        'from',
        'to',
        'append',
    ];

    public function scopeModel(Builder $query, string $model)
    {
        return $query->where('model', $model);
    }

    public static function setCachedField($model, $from, $to = null, $append = false)
    {
        self::updateOrCreate([
            'model' => $model,
            'from' => $from,
        ], [
            'to' => $to,
            'append' => $append,
        ]);
    }

    public static function getCachedFields($model, $input_fields, $fallbacks = [])
    {
        $cached_mappings = self::model($model)
            ->whereIn('from', $input_fields)
            ->get();

        return $input_fields->mapWithKeys(fn ($f) => [
            $f => $cached_mappings->contains('from', $f) ? [
                'value' => $cached_mappings->firstWhere('from', $f)['to'],
                'append' => $cached_mappings->firstWhere('from', $f)['append'],
            ] : [
                'value' => $fallbacks->get(strtolower($f)),
                'append' => false,
            ],
        ]);
    }
}
