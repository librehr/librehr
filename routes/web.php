<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "we-b" middleware group. Make something great!
|
*/

Route::get('/download/{uuid}', function (Request $request, $uuid) {
    $document = \App\Models\Document::query()->where('uuid', $uuid);
    $authUser = Auth::user();

    if ($authUser->IsAdmin) {
        $document = $document->firstOrFail();
        return response()->download(
            Storage::path($document->path)
        );
    }

    $document = $document->where('user_id', $authUser->id)->firstOrFail();

    return response()->download(
        Storage::path($document->path)
    );
})->name('download-document')->middleware(['auth']);
