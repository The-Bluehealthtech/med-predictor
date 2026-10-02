<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('club_officials')) return;
        $coaches = [
            'Abha' => 'Damir Burić',
            'Al Ahli' => 'Marino Pušić',
            'Al Fateh' => 'Khaled Fahad Al Atawi',
            'Al Fayha' => 'Fábio Luiz Carille de Araujo',
            'Al Faisaly' => 'Giovanni Solinas',
            'Al Hazm' => 'Jalel Kadri',
            'Al Hilal' => 'Simone Inzaghi',
            'Al Ettifaq' => 'Arthur Thomas Papastamatiou',
            'Al Ittihad' => 'Jens Wissing',
            'Al Khaleej' => 'José Manuel Martins Teixeira Gomes',
            'Al Kholood' => 'Desmond Buckingham',
            'Al Nassr' => 'Angelos Postecoglou',
            'Al Qadsiah' => 'Brendan Rodgers',
            'Al Riyadh' => 'Maurício Corrêa Dulac',
            'Al Shabab' => 'Thomas Letsch',
            'Al Taawoun' => 'Vuk Rašović',
            'Diriyah' => 'Bruno Miguel Silva do Nascimento',
            'NEOM' => 'Christophe Galtier',
        ];
        $leaders = [
            'Abha' => ['name' => 'Ahmed Al-Hodithy', 'role' => 'President', 'description' => 'President', 'source' => 'lequipe'],
            'Al Ahli' => ['name' => 'Fabrice Bocquet', 'role' => 'Other', 'description' => 'Chief Executive Officer', 'source' => 'alahlifc'],
            'Al Fateh' => ['name' => 'Mansour Ibrahim Al-Afaliq', 'role' => 'President', 'description' => 'Chairman of the Board', 'source' => 'fatehclub'],
            'Al Fayha' => ['name' => 'Tawfiq Al-Modaiheem', 'role' => 'President', 'description' => 'President', 'source' => 'public_directory'],
            'Al Faisaly' => ['name' => 'Abdulmajeed Al-Omaim', 'role' => 'President', 'description' => 'Chairman', 'source' => 'public_directory'],
            'Al Hazm' => ['name' => 'Salman Al-Malik', 'role' => 'President', 'description' => 'President', 'source' => 'public_directory'],
            'Al Hilal' => ['name' => 'Nawaf bin Saad', 'role' => 'President', 'description' => 'Chairman of the Board', 'source' => 'alhilal'],
            'Al Ettifaq' => ['name' => 'Samer Al-Misehal', 'role' => 'President', 'description' => 'President', 'source' => 'ettifaq'],
            'Al Ittihad' => ['name' => 'Domingos Oliveira', 'role' => 'Other', 'description' => 'Chief Executive Officer', 'source' => 'ittihadclub'],
            'Al Khaleej' => ['name' => 'Ahmed Khuraidah', 'role' => 'President', 'description' => 'President', 'source' => 'public_directory'],
            'Al Kholood' => ['name' => 'Ben Harburg', 'role' => 'President', 'description' => 'Chairman / Owner representative', 'source' => 'spl'],
            'Al Nassr' => ['name' => 'Abdullah Al-Majid', 'role' => 'President', 'description' => 'President', 'source' => 'public_directory'],
            'Al Qadsiah' => ['name' => 'Rami Al-Turki', 'role' => 'President', 'description' => 'Acting Chairman', 'source' => 'okaz'],
            'Al Riyadh' => ['name' => 'Bandar Al-Muqail', 'role' => 'President', 'description' => 'President', 'source' => 'public_directory'],
            'Al Shabab' => ['name' => 'Abdulaziz bin Fahd Al-Malik', 'role' => 'President', 'description' => 'President', 'source' => 'alshabab'],
            'Al Taawoun' => ['name' => 'Badr Al-Ghannam', 'role' => 'President', 'description' => 'President', 'source' => 'public_directory'],
            'Diriyah' => ['name' => 'Khalid bin Mohammed bin Saud', 'role' => 'President', 'description' => 'Chairman of the Board', 'source' => 'diriyahcompany'],
            'NEOM' => ['name' => 'Meshari Al-Motairi', 'role' => 'President', 'description' => 'Chairman', 'source' => 'neom'],
        ];
        $clubs = DB::table('clubs')->get(['id','name']);
        foreach ($coaches as $key => $name) {
            $club = $this->findClub($clubs, $key); if (! $club) continue;
            DB::table('club_officials')->where('club_id',$club->id)->where('registration_type','TeamOfficial')->where('team_official_role','Coach')->where('is_head_coach',true)->update(['is_head_coach'=>false,'status'=>'inactive','updated_at'=>now()]);
            $this->upsert($club->id,$name,'TeamOfficial','Coach',null,'Head coach','spl','https://www.spl.com.sa/en/teams',true);
        }
        foreach ($leaders as $key => $item) {
            $club = $this->findClub($clubs, $key); if (! $club) continue;
            $this->upsert($club->id,$item['name'],'OrganisationOfficial',null,$item['role'],$item['description'],$item['source'],null,false);
        }
    }
    private function upsert(int $clubId,string $name,string $type,?string $teamRole,?string $orgRole,string $description,string $source,?string $url,bool $head): void
    {
        $parts=preg_split('/\s+/u',trim($name)) ?: []; $first=array_shift($parts) ?: $name; $last=implode(' ',$parts) ?: $first;
        $existing=DB::table('club_officials')->where('club_id',$clubId)->where('registration_type',$type)->whereRaw("LOWER(TRIM(international_first_name || ' ' || international_last_name)) = ?",[mb_strtolower($name)])->first();
        $data=['club_id'=>$clubId,'international_first_name'=>$first,'international_last_name'=>$last,'gender'=>'male','registration_type'=>$type,'team_official_role'=>$teamRole,'organisation_official_role'=>$orgRole,'role_description'=>$description,'is_head_coach'=>$head,'status'=>'active','discipline'=>'Football','registration_valid_from'=>'2026-07-01','source'=>$source,'source_url'=>$url,'retrieved_at'=>now(),'updated_at'=>now()];
        if ($existing) DB::table('club_officials')->where('id',$existing->id)->update($data); else { $data['created_at']=now(); DB::table('club_officials')->insert($data); }
    }
    private function findClub($clubs,string $needle): ?object
    {
        $norm=fn($v)=>Str::of($v)->ascii()->lower()->replaceMatches('/[^a-z0-9]+/',' ')->trim()->toString(); $n=$norm($needle);
        foreach($clubs as $club) { $c=$norm($club->name); if($c===$n || str_starts_with($c,$n.' ') || str_starts_with($n,$c.' ') || ($n==='al nassr'&&str_contains($c,'al nasr')) || ($n==='al qadsiah'&&str_contains($c,'al quadisiya')) || ($n==='al ettifaq'&&str_contains($c,'al ittifaq')) || ($n==='al hazm'&&str_contains($c,'al hazem')) || ($n==='al faisaly'&&str_contains($c,'al faysaly'))) return $club; } return null;
    }
    public function down(): void {}
};
