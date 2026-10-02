<?php
namespace App\Services;
use App\Models\{Player,ImagingStudy};
use Illuminate\Support\Facades\Schema;
final class AgeVerificationService {
    public function assess(Player $player, bool $includeMissing = true): array {
        $out=['flags'=>[],'confirmed'=>[],'coverage'=>'Insuffisant'];
        if (!Schema::hasTable('fit_imaging_studies') || !Schema::hasTable('fit_imaging_reports')) return $out;
        $studies=ImagingStudy::where('player_id',$player->id)->with(['reports'=>fn($q)=>$q->where('status','validated')])->get();
        $birth=$player->date_of_birth?->format('Y-m-d'); $count=0;
        foreach ($studies as $study) {
            $source=$study->source_identity['birth_date']??null;
            if ($birth && $source && $source!==str_replace('-','',$birth)) {
                $out['flags'][]=$this->flag('review','Date de naissance différente dans les métadonnées de l’examen #'.$study->id.'.','Métadonnées DICOM / profil FIT');
            }
            $report=$study->reports->first();
            if (!$report || $study->purpose!=='age_u17') continue;
            $count++; $age=$report->age_review??[];
            if (($age['population']??'')!=='male') {
                $out['flags'][]=$this->flag('incomplete','Examen U-17 #'.$study->id.' : protocole masculin non applicable ou population non documentée.','Limites du protocole IRM du radius distal');
            } elseif ($report->quality!=='interpretable' || empty($age['grade'])) {
                $out['flags'][]=$this->flag('incomplete','Examen U-17 #'.$study->id.' : maturité osseuse indéterminée.','Compte rendu radiologique validé');
            } elseif ((int)$age['grade']===6) {
                $out['flags'][]=$this->flag('review','Examen U-17 #'.$study->id.' : fusion complète (grade VI). Revue spécialisée et documentaire nécessaire ; aucun âge réel n’est déduit.','IRM du radius distal — compte rendu v'.$report->version);
            } else {
                $out['confirmed'][]='IRM U-17 #'.$study->id.' : grade '.$age['grade'].' documenté ; ce résultat ne certifie pas l’âge.';
            }
            if (!empty($age['second_grade']) && $age['second_grade']!=$age['grade']) {
                $out['flags'][]=$this->flag('review','Lectures IRM discordantes pour l’examen #'.$study->id.'. Consulter la justification de la conclusion.','Première / seconde lecture');
            }
            if ($birth && !empty($age['declared_birth_date']) && $birth!==$age['declared_birth_date']) {
                $out['flags'][]=$this->flag('review','Date de naissance FIT modifiée depuis le compte rendu U-17 #'.$study->id.'.','Date déclarée conservée lors de la validation');
            }
            foreach ($age['evidence']??[] as $e) {
                if ($birth && !empty($e['birth_date']) && $birth!==$e['birth_date']) {
                    $out['flags'][]=$this->flag('review','Date de naissance discordante dans une pièce relue : '.$e['source'].' (examen #'.$study->id.').','Revue documentaire humaine : '.$e['reference']);
                }
            }
            if (empty($age['cutoff_birth_date'])) $out['flags'][]=$this->flag('incomplete','Règle de naissance de la compétition non renseignée pour l’examen #'.$study->id.'.','Règlement de compétition à vérifier');
            elseif ($birth && $birth<$age['cutoff_birth_date']) $out['flags'][]=$this->flag('review','Date déclarée antérieure au seuil saisi pour '.$age['competition'].'. Vérifier le règlement et son édition.','Règle documentaire saisie, sans décision automatique');
        }
        if ($count) $out['coverage']='Partiel';
        if (!$count && $includeMissing) $out['flags'][]=$this->flag('incomplete','Aucun compte rendu de vérification U-17 validé disponible.','Couverture documentaire FIT');
        return $out;
    }
    private function flag(string $severity,string $label,string $source): array { return compact('severity','label','source'); }
}
