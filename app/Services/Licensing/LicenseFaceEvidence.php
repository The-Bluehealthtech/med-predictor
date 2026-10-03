<?php

namespace App\Services\Licensing;

use App\Models\PlayerLicense;
use App\Models\PlayerLicenseDocument;
use Illuminate\Support\Facades\Storage;

final class LicenseFaceEvidence
{
    public function collect(PlayerLicense $license): array
    {
        $license->loadMissing(['documents', 'photo']);
        $sources = [];

        if ($bytes = $this->licensePhotoBytes($license)) {
            $sources['license_photo'] = ['label' => 'Photo licence', 'bytes' => $bytes];
        }

        foreach (['photo' => 'submitted_photo', 'identity' => 'identity_document'] as $type => $key) {
            $doc = $license->documents->where('document_type', $type)->sortByDesc('id')->first();
            if ($doc && ($bytes = $this->documentBytes($doc))) {
                $sources[$key] = ['label' => $doc->label(), 'bytes' => $bytes];
            }
        }

        return $sources;
    }

    private function documentBytes(PlayerLicenseDocument $document): ?string
    {
        if (!str_starts_with((string) $document->mime_type, 'image/')) {
            return null;
        }

        $encoded = PlayerLicenseDocument::query()->whereKey($document->id)->value('content_base64');
        $bytes = base64_decode((string) $encoded, true);

        return $this->validImage($bytes) ? $bytes : null;
    }

    private function licensePhotoBytes(PlayerLicense $license): ?string
    {
        $path = $license->photo?->photo_path;
        if (!$path || !Storage::disk('public')->exists($path)) {
            return null;
        }

        $bytes = Storage::disk('public')->get($path);

        return $this->validImage($bytes) ? $bytes : null;
    }

    private function validImage($bytes): bool
    {
        return is_string($bytes)
            && $bytes !== ''
            && strlen($bytes) <= 5 * 1024 * 1024
            && getimagesizefromstring($bytes) !== false;
    }
}
