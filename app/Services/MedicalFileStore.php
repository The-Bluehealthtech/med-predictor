<?php

namespace App\Services;

use App\Models\MedicalFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Stockage des fichiers médicaux en base. Les champs existants (PCMA.ecg_file,
 * documents.file_path…) gardent une référence « medical-file:{id} » ; une ancienne valeur
 * (chemin sur disque) reste lisible tant que le fichier existe.
 */
final class MedicalFileStore
{
    public function put(UploadedFile $file, string $ownerType, ?string $field = null): MedicalFile
    {
        $bytes = (string) file_get_contents($file->getRealPath());

        return MedicalFile::query()->create([
            'owner_type' => $ownerType,
            'field' => $field,
            'file_name' => mb_substr($file->getClientOriginalName() ?: 'fichier', 0, 255),
            'mime_type' => $file->getMimeType(),
            'size' => strlen($bytes),
            'sha256' => hash('sha256', $bytes),
            'content_base64' => base64_encode($bytes),
            'uploaded_by' => auth()->id(),
        ]);
    }

    public function file(?string $ref): ?MedicalFile
    {
        return is_string($ref) && preg_match('/^medical-file:(\d+)$/', $ref, $m) ? MedicalFile::query()->find((int) $m[1]) : null;
    }

    /** @return array{bytes:string, name:string, mime:?string}|null */
    public function read(?string $ref): ?array
    {
        if ($file = $this->file($ref)) {
            return ['bytes' => (string) base64_decode($file->content_base64, true), 'name' => $file->file_name, 'mime' => $file->mime_type];
        }
        if (!is_string($ref) || $ref === '' || str_contains($ref, '..') || str_starts_with($ref, 'medical-file:')) {
            return null;
        }
        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($ref)) {
                return ['bytes' => (string) Storage::disk($disk)->get($ref), 'name' => basename($ref), 'mime' => Storage::disk($disk)->mimeType($ref) ?: null];
            }
        }

        return null;
    }

    /** Nom affichable d'une référence. */
    public function name(?string $ref): ?string
    {
        return $this->file($ref)?->file_name ?? ($ref ? basename($ref) : null);
    }
}
