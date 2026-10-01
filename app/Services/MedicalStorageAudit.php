<?php
namespace App\Services;
use Illuminate\Support\Facades\{DB,Schema};
final class MedicalStorageAudit
{
    // Audit en lecture seule de chaque joueur ; aucune donnée clinique ou nominative dans la sortie.
    public function run(): array
    {
        $definitions=config('medical_sections.sections',[]);
        $available=Schema::getColumnListing('health_records');
        $counts=array_fill_keys(array_keys($definitions),0);
        $players=DB::table('players')->pluck('id');
        $withRecord=0;
        foreach($players as $id) {
            $records=DB::table('health_records')->where('player_id',$id)->get();
            if($records->isNotEmpty()) $withRecord++;
            foreach($definitions as $section=>$def) {
                if(!in_array($def['column'],$available,true)) continue;
                if($records->contains(function($r)use($def){
                    $value=$r->{$def['column']} ?? null;
                    return $value!==null && !in_array(trim((string)$value),['','[]','{}','null','Données non disponibles'],true);
                })) $counts[$section]++;
            }
        }
        return ['database_driver'=>DB::connection()->getDriverName(),'read_only'=>true,
            'players'=>$players->count(),'players_with_medical_record'=>$withRecord,
            'players_without_medical_record'=>$players->count()-$withRecord,
            'orphan_medical_records'=>DB::table('health_records')->whereNotNull('player_id')
                ->whereNotIn('player_id',DB::table('players')->select('id'))->count(),
            'legacy_records_without_player_id'=>DB::table('health_records')->whereNull('player_id')->count(),
            'players_with_data_by_section'=>$counts,
            'missing_columns'=>array_values(array_diff(array_column($definitions,'column'),$available)),
            'private_documents_schema_present'=>Schema::hasTable('health_record_documents'),
            'pcma_types_present'=>Schema::hasTable('pcmas')?DB::table('pcmas')->distinct()->pluck('type')->all():[],
            'scope'=>'Connected primary database only; no conclusion about another deployment database'];
    }
}
