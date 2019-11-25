<?php

namespace Modules\Accounting\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Modules\Accounting\Entities\MoneyTransaction;

use Illuminate\Http\Request;

class ReceiptsController extends Controller
{
    public function listReceipts(MoneyTransaction $transaction)
    {
        $this->authorize('view', $transaction);

        return response()->json($transaction->getReceiptPictures());
    }

    public function updateReceipts(Request $request, MoneyTransaction $transaction)
    {
        $this->authorize('update', $transaction);

        $request->validate([
            'files' => [
                'required',
                'array'
            ],
            'files.*' => [
                'file',
                'image'
            ]
        ]);

        $urls = [];
        foreach ($request->file('files') as $file) {
            $urls[] = $transaction->addReceiptPicture($file);
        }
        $transaction->save();

        return response()->json($urls, 201);
    }

    public function deleteReceipt(Request $request, MoneyTransaction $transaction)
    {
        $this->authorize('update', $transaction);

        $request->validate([
            'path' => [
                'required',
                'string'
            ]
        ]);

        $transaction->deleteReceiptPictureByUrl($request->input('path'));
        $transaction->save();

        return response(null, 204);
    }
}
