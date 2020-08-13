<?php

namespace App\Imports;

use App\Models\ImportFieldMapping;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use PhpOffice\PhpSpreadsheet\Shared\Date;

abstract class ImportWithMapping implements ToCollection, WithHeadingRow
{
    use Importable;

    protected $fields;

    public $stats;

    public function __construct(Collection $fields)
    {
        HeadingRowFormatter::default('none');

        $this->fields = $fields;

        $this ->stats = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
        ];
    }

    abstract protected static function getModelIdentifier();

    public static function getImportFields($fields)
    {
        return collect($fields)
            ->where('overview_only', false)
            ->filter(fn ($f) => isset($f['assign']) && is_callable($f['assign']))
            ->map(fn ($f) => [
                'key' => $f['label_key'],
                'labels' => static::getAllTranslations($f['label_key'])
                    ->concat(isset($f['import_labels']) && is_array($f['import_labels']) ? $f['import_labels'] : [])
                    ->map(fn ($l) => strtolower($l)),
                'append' => false,
                'assign' => $f['assign'],
                'get' => $f['value'],
                'format' => isset($f['format']) ? $f['format'] : (isset($f['form_type']) ? $f['form_type'] : ''),
            ]);
    }

    public static function getHeaderMappings($fields, $table_headers)
    {
        $variations = $fields->mapWithKeys(fn ($f) =>
            $f['labels']->mapWithKeys(fn ($l) => [ $l => $f['key'] ])
        );

        $available = collect([ null => '-- ' . __('app.dont_import') . ' --' ])
            ->merge($fields->mapWithKeys(fn ($f) => [ $f['key'] => __($f['key']) ]));

        return [
            'headers' => $table_headers,
            'available' => $available,
            'defaults' => ImportFieldMapping::getCachedFields(static::getModelIdentifier(), $table_headers, $variations),
        ];
    }

    public static function applyHeaderMappings($fields, $map)
    {
        collect($map)->each(fn ($m) =>
            ImportFieldMapping::setCachedField(static::getModelIdentifier(), $m['from'], $m['to'], isset($m['append'])));

        return collect($map)
            ->filter(fn ($m) => $m['to'] != null)
            ->map(fn ($m) => [
                'key' => $m['to'],
                'labels' => collect([ strtolower($m['from']) ]),
                'append' => isset($m['append']),
                'assign' => $fields->firstWhere('key', $m['to'])['assign'],
                'get' => $fields->firstWhere('key', $m['to'])['get'],
                'format' => $fields->firstWhere('key', $m['to'])['format'],
            ]);
    }

    protected function assignImportedValues($row, $object, $value_handler = null)
    {
        $row->each(function ($value, $label) use ($object, $value_handler) {
            if ($value === 'N/A') {
                $value = null;
            }
            $this->fields->each(function ($f) use ($object, $label, $value, $value_handler) {
                if ($f['labels']->containsStrict(strtolower($label))) {
                    try {
                        if ($f['format'] === 'date' && (is_int($value) || is_float($value))) {
                            $value = Date::excelToDateTimeObject($value);
                        }
                        if (! is_callable($value_handler) || ! $value_handler($object, $f, $value)) {
                            if ($f['append']) {
                                static::appendToField($object, $f['get'], $f['assign'], $value);
                            } else {
                                $f['assign']($object, $value);
                            }
                        }
                    } catch (Exception $e) {
                        Log::warning('Cannot import community volunteer: ' . $e->getMessage());
                    }
                }
            });
        });
    }

    protected static function appendToField($object, $get, $assign, $value) {
        $old_value = is_callable($get) ? $get($object) : $object[$get];
        $assign($object, $value);

        if ($old_value != null) {
            $new_value = is_callable($get) ? $get($object) : $object[$get];

            if (is_array($old_value)) {
                $assign($object, array_merge($old_value, $new_value));
            } else if (is_string($old_value)) {
                $assign($object, $old_value . ', ' . $new_value);
            } else if (is_object($old_value) && get_class($old_value) == 'Illuminate\Database\Eloquent\Collection') {
                $assign($object, $old_value->concat($new_value));
            } else {
                Log::warning('Cannot append value of type: ' . gettype($old_value));
            }
        }
    }

    protected static function getAllTranslations($key) {
        return collect(language()->allowed())
            ->keys()
            ->map(fn ($lk) => __($key, [], $lk));
    }

    protected function created($amount = 1) {
        $this->stats['created'] += $amount;
    }

    protected function updated($amount = 1) {
        $this->stats['updated'] += $amount;
    }

    protected function skipped($amount = 1) {
        $this->stats['skipped'] += $amount;
    }
}
