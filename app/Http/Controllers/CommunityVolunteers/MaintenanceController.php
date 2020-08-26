<?php

namespace App\Http\Controllers\CommunityVolunteers;

use App\Models\CommunityVolunteers\CommunityVolunteer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MaintenanceController extends BaseController
{
    private const MONTHS_NO_ACTIVITY_SINCE = 2;

    private static function getAlumniSince()
    {
        return CommunityVolunteer::query()
            ->whereNotNull('work_leaving_date')
            ->whereDate('work_leaving_date', '<', Carbon::today()->subMonth(self::MONTHS_NO_ACTIVITY_SINCE));
    }

    public function maintenance()
    {
        return view('cmtyvol.maintenance', [
            'months_alumni_since' => self::MONTHS_NO_ACTIVITY_SINCE,
            'num_all' => CommunityVolunteer::count(),
            'num_alumni_since_n_months' => self::getAlumniSince()->count(),
            'num_alumni' => CommunityVolunteer::workStatus('alumni')->count(),
            'num_future' => CommunityVolunteer::workStatus('future')->count(),
        ]);
    }

    public function doMaintenance(Request $request)
    {
        $request->validate([
            'remove_alumni_since' => 'nullable|in:on,off',
            'remove_all_alumni' => 'nullable|in:on,off',
            'remove_future' => 'nullable|in:on,off',
            'remove_all' => 'nullable|in:on,off',
        ]);

        $cnt = 0;
        if (isset($request->remove_alumni_since) && $request->remove_alumni_since === 'on') {
            $cnt += self::getAlumniSince()->delete();
        }
        if (isset($request->remove_all_alumni) && $request->remove_all_alumni === 'on') {
            $cnt += CommunityVolunteer::workStatus('alumni')->delete();
        }
        if (isset($request->remove_future) && $request->remove_future === 'on') {
            $cnt += CommunityVolunteer::workStatus('future')->delete();
        }
        if (isset($request->remove_all) && $request->remove_all === 'on') {
            $cnt += CommunityVolunteer::query()->delete();
        }
        return redirect()
            ->route('cmtyvol.index')
            ->with('success', __('people.removed_n_persons', [ 'num' => $cnt ]));
    }
}
