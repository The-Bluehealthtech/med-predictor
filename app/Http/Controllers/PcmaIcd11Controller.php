<?php
namespace App\Http\Controllers;
use App\Services\{MedicalRecordAccess, WhoIcd11};
use Illuminate\Http\Request;
final class PcmaIcd11Controller extends Controller
{
    public function search(Request $request, WhoIcd11 $who)
    {
        app(MedicalRecordAccess::class)->authorizeRole($request->user());
        $data=$request->validate(['q'=>'required|string|min:2|max:100','language'=>'required|in:fr,en']);
        return response()->json(['success'=>true,'items'=>$who->search($data['q'],$data['language'])]);
    }
}
