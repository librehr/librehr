<?php

namespace App\Observers;

use App\Models\Document;
use App\Models\Request;
use App\Services\Notifications;

class DocumentObserver
{
    /**
     * Handle the Document "created" event.
     */
    public function created(Document $document): void
    {
        $document = $document->load(['user.contracts', 'uploadedBy']);

        if (data_get($document, 'user_id') !== \Auth::id()) {
            Notifications::notify(
                Notifications\Resources\Document::class,
                $document,
                data_get($document, 'user_id')
            );
        }

        if (data_get($document, 'attributes.signature')) {
            $request = Request::query()->firstOrCreate(['name' => 'signs']);

            $document->requests()->attach($document->id, [
                'request_id' => data_get($request, 'id'),
                'user_id' => data_get($document, 'user_id'),
                'contract_id' => $document->user->getActiveContractId(),
                'created_at' => now(),
            ]);

        }
    }

    /**
     * Handle the Document "updated" event.
     */
    public function updated(Document $document): void
    {
        //
    }

    /**
     * Handle the Document "deleted" event.
     */
    public function deleted(Document $document): void
    {
        //
    }

    /**
     * Handle the Document "restored" event.
     */
    public function restored(Document $document): void
    {
        //
    }

    /**
     * Handle the Document "force deleted" event.
     */
    public function forceDeleted(Document $document): void
    {
        //
    }
}
