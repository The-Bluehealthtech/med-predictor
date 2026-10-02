<?php
namespace App\Services;
use Symfony\Component\Process\Process;
use Illuminate\Validation\ValidationException;
final class MedicalImagingDicom {
    public function uid(): string {
        // Exact decimal UUID conversion, without floating-point base_convert().
        $bytes=random_bytes(16); $bytes[6]=chr((ord($bytes[6]) & 15) | 64); $bytes[8]=chr((ord($bytes[8]) & 63) | 128);
        $decimal='0';
        foreach (unpack('C*',$bytes) as $byte) {
            $carry=$byte; $next='';
            for($i=strlen($decimal)-1;$i>=0;$i--) { $n=(int)$decimal[$i]*256+$carry; $next=($n%10).$next; $carry=intdiv($n,10); }
            while($carry) { $next=($carry%10).$next; $carry=intdiv($carry,10); }
            $decimal=ltrim($next,'0')?:'0';
        }
        return '2.25.'.$decimal;
    }
    public function run(string $action, array $payload): array {
        $process = new Process([config('medical_imaging.python'),base_path('bin/medical_imaging_dicom.py'),$action]);
        $process->setInput(json_encode($payload,JSON_THROW_ON_ERROR)); $process->setTimeout(90);
        $process->run();
        if (!$process->isSuccessful()) {
            // Do not log DICOM patient headers or report text.
            \Log::warning('Medical imaging worker failed',['action'=>$action,'exit_code'=>$process->getExitCode()]);
            throw ValidationException::withMessages(['images'=>'Traitement DICOM impossible. Vérifiez le format et la disponibilité du service d’imagerie.']);
        }
        return json_decode($process->getOutput(),true,512,JSON_THROW_ON_ERROR);
    }
}
