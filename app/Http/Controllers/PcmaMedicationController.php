<?php
namespace App\Http\Controllers;
use App\Services\{MedicalRecordAccess, MedicationCatalogue};
use Illuminate\Http\Request;
final class PcmaMedicationController extends Controller
{
    public function search(Request $request, MedicationCatalogue $catalogue)
    {
        app(MedicalRecordAccess::class)->authorizeRole($request->user());
        $data=$request->validate(['q'=>'required|string|min:2|max:100']);
        return response()->json(['success'=>true,'products'=>$catalogue->search($data['q'])]);
    }
}
