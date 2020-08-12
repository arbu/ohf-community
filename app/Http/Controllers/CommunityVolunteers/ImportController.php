<?php

namespace App\Http\Controllers\CommunityVolunteers;

use App\Http\Requests\CommunityVolunteers\ImportCommunityVolunteers;
use App\Imports\CommunityVolunteers\CommunityVolunteersImport;
use App\Imports\HeadingRowImport;
use App\Models\CommunityVolunteers\CommunityVolunteer;
use Illuminate\Http\Request;

class ImportController extends BaseController
{
    public function import()
    {
        $this->authorize('import', CommunityVolunteer::class);

        return view('cmtyvol.import');
    }

    public function doImport(ImportCommunityVolunteers $request)
    {
        $this->authorize('import', CommunityVolunteer::class);

        $request->validated();

        $fields = CommunityVolunteersImport::getImportFields($this->getFields());

        if ($request->map != null) {
            $fields = CommunityVolunteersImport::applyHeaderMappings($fields, $request->map);
        }

        $importer = new CommunityVolunteersImport($fields);
        $importer->import($request->file('file'));

        return redirect()
            ->route('cmtyvol.index')
            ->with('success', __('app.import_successful'));
    }

    public function getHeaderMappings(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        $table_headers = collect((new HeadingRowImport)->toArray($request->file('file'))[0][0]);

        $fields = CommunityVolunteersImport::getImportFields($this->getFields());

        return CommunityVolunteersImport::getHeaderMappings($fields, $table_headers);
    }
}
