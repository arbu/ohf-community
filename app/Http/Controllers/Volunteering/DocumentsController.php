<?php

namespace App\Http\Controllers\Volunteering;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Volunteering\UploadVolunteerDocument;
use App\Volunteer;
use App\VolunteerDocument;

class DocumentsController extends Controller
{
    function download(Volunteer $volunteer, VolunteerDocument $document) {
        $this->authorize('view', $volunteer);

        $name = $volunteer->name . ' - ' . __('volunteering.' . $document->type) . ' - ' . $document->created_at->toDateString() . '.' . $document->extension;
        return Storage::download($document->file, $name);
    }

    function store(Volunteer $volunteer, UploadVolunteerDocument $request) {
        $this->authorize('update', $volunteer);

        $document = new VolunteerDocument();
        $document->type = $request->type;
        $document->remarks = $request->remarks;
        $document->file = $request->file('file')->store('volunteers');
        $document->extension = $request->file('file')->extension();

        $volunteer->documents()->save($document);

        return redirect()->route('volunteering.volunteers.show', $volunteer)
            ->with('success', __('volunteering.document_has_been_uploaded', ['document' => __('volunteering.' . $document->type)]));
    }

    function destroy(Volunteer $volunteer, VolunteerDocument $document) {
        $this->authorize('update', $volunteer);

        // File is deleted by VolunteerDocument::deleting event automatically
        $document->delete();

        return redirect()->route('volunteering.volunteers.show', $volunteer)
            ->with('success', __('volunteering.document_has_been_removed', ['document' => __('volunteering.' . $document->type)]));
    }

}
