<?php

namespace App\Imports\CommunityVolunteers;

use App\Imports\ImportWithMapping;
use App\Models\CommunityVolunteers\CommunityVolunteer;
use App\Models\CommunityVolunteers\Responsibility;
use Illuminate\Support\Collection;

class CommunityVolunteersImport extends ImportWithMapping
{
    protected static function getModelIdentifier() {
        return 'cmtyvol';
    }

    public function collection(Collection $rows)
    {
        $has_dates = $this->fields->where('key', 'people.starting_date')->first() != null
            && $this->fields->where('key', 'people.leaving_date')->first() != null;

        foreach ($rows as $row) {
            $cmtyvol = new CommunityVolunteer();

            $responsibilities = [];

            $this->assignImportedValues($row, $cmtyvol,
                function ($object, $field, $value) use (&$responsibilities) {
                    // responsibilities is a relationship, therefor it cannot yet be assigned,
                    // while the object does not exist in the database
                    if ($field['key'] == 'app.responsibilities') {
                        if ($value != null) {
                            foreach (preg_split('/(\s*[,;|]\s*)/', $value) as $responsibility) {
                                $responsibilities[] = $responsibility;
                            }
                        }
                        return true;
                    } else {
                        return false;
                    }
                });

            if (isset($cmtyvol->first_name) && isset($cmtyvol->family_name)) {
                $existing_cmtyvol = CommunityVolunteer::query()
                    ->where('first_name', $cmtyvol->first_name)
                    ->where('family_name', $cmtyvol->family_name)
                    ->when(! empty($cmtyvol->nationality), fn ($q) =>
                        $q->where('nationality', $cmtyvol->nationality)
                          ->orWhereNull('nationality'))
                    ->when(! empty($cmtyvol->date_of_birth), fn ($q) =>
                        $q->where('date_of_birth', $cmtyvol->date_of_birth)
                          ->orWhereNull('date_of_birth'))
                    ->first();

                if ($existing_cmtyvol !== null) {
                    $this->assignImportedValues($row, $existing_cmtyvol,
                        fn ($object, $field, $value) => $field['key'] == 'app.responsibilities');
                    $cmtyvol = $existing_cmtyvol;
                    $this->updated();
                } else {
                    $this->created();
                }

                $cmtyvol->save();

                foreach ($responsibilities as $responsibility_name) {
                    $responsibility = Responsibility::updateOrCreate([ 'name' => $responsibility_name ]);
                    $from = $has_dates ? $cmtyvol->work_starting_date : null;
                    $to = $has_dates ? $cmtyvol->work_leaving_date : null;

                    if ($cmtyvol->responsibilities()->wherePivot('start_date', $from)->wherePivot('end_date', $to)->find($responsibility) === null) {
                        $cmtyvol->responsibilities()->attach($responsibility, [
                            'start_date' => $from,
                            'end_date' => $to,
                        ]);
                        if ($has_dates) {
                            $start_dates = $cmtyvol->responsibilities->map(fn ($r) => $r->pivot->start_date);
                            $cmtyvol->work_starting_date = $start_dates->contains(null) ? null : $start_dates->min();

                            $end_dates = $cmtyvol->responsibilities->map(fn ($r) => $r->pivot->end_date);
                            $cmtyvol->work_leaving_date = $end_dates->contains(null) ? null : $end_dates->max();

                            $cmtyvol->save();
                        }
                    }
                }
            } else {
                $this->skipped();
            }
        }
    }
}
