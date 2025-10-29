<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomFieldController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/contacts/all', [ContactController::class, 'getAll']);
Route::get('/', [ContactController::class, 'index'])->name('contacts.index');
Route::resource('contacts', ContactController::class)->except(['show']);
Route::post('/contacts/merge-preview', [ContactController::class, 'mergePreview'])->name('contacts.merge-preview');
Route::post('/contacts/merge', [ContactController::class, 'merge'])->name('contacts.merge');
Route::get('/contacts/search', [ContactController::class, 'search'])->name('contacts.search');
Route::resource('custom-fields', CustomFieldController::class)->except(['show']);