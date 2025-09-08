// Routes pour l'internationalisation
Route::get('/language/{locale}', [App\Http\Controllers\LanguageController::class, 'switchLanguage'])->name('language.switch');
Route::get('/api/language/current', [App\Http\Controllers\LanguageController::class, 'getCurrentLanguage'])->name('language.current');


