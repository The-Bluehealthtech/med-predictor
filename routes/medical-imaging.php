<?php
use App\Http\Controllers\MedicalImagingController;
use Illuminate\Support\Facades\Route;
Route::prefix('health-records/{healthRecord}/imaging')->name('medical-imaging.')->middleware('auth')->group(function () {
    Route::get('/',[MedicalImagingController::class,'index'])->name('index');
    Route::post('/',[MedicalImagingController::class,'create'])->name('create');
    Route::get('/{study}',[MedicalImagingController::class,'show'])->name('show');
    Route::post('/{study}/images',[MedicalImagingController::class,'upload'])->name('upload')->middleware('throttle:20,1');
    Route::post('/{study}/identity',[MedicalImagingController::class,'identity'])->name('identity');
    Route::get('/{study}/images/{instance}/frame',[MedicalImagingController::class,'frame'])->name('frame')->middleware('throttle:180,1');
    Route::get('/{study}/images/{instance}/source',[MedicalImagingController::class,'image'])->name('image');
    Route::post('/{study}/reports',[MedicalImagingController::class,'saveReport'])->name('report.save');
    Route::post('/{study}/reports/{report}/validate',[MedicalImagingController::class,'validateReport'])->name('report.validate');
    Route::get('/{study}/reports/{report}/dicom',[MedicalImagingController::class,'export'])->name('report.dicom');
    Route::get('/{study}/reports/{report}/pdf',[MedicalImagingController::class,'pdf'])->name('report.pdf');
    Route::post('/{study}/reports/{report}/pacs',[MedicalImagingController::class,'pacs'])->name('report.pacs')->middleware('throttle:5,1');
});
