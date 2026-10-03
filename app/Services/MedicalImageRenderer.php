<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Visionneuse commune des fichiers médicaux : DICOM rendu côté serveur (pydicom, Pillow,
 * GDCM ; bin/medical_imaging_dicom.py), TIFF/BMP convertis en PNG, images et PDF affichés
 * par le navigateur. Aucune image n'est inventée : un fichier non décodable est signalé.
 */
final class MedicalImageRenderer
{
    public const BROWSER_IMAGES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    public function __construct(private readonly MedicalImagingDicom $worker)
    {
    }

    /** dicom | image | raster | pdf | other, d'après le contenu (préambule DICM, signature PDF), puis le type déclaré. */
    public function kind(array $file): string
    {
        $bytes = $file['bytes'];
        if (strlen($bytes) > 132 && substr($bytes, 128, 4) === 'DICM') {
            return 'dicom';
        }
        if (str_starts_with($bytes, '%PDF-')) {
            return 'pdf';
        }
        $mime = (string) ((new \finfo(FILEINFO_MIME_TYPE))->buffer($bytes) ?: $file['mime']);
        if (in_array($mime, self::BROWSER_IMAGES, true)) {
            return 'image';
        }
        if (in_array($mime, ['image/tiff', 'image/bmp', 'image/x-ms-bmp'], true)) {
            return 'raster';
        }
        // DICOM sans préambule (fichier « .dcm » ancien) : l'en-tête est vérifié par le décodeur.
        return str_ends_with(strtolower($file['name']), '.dcm') ? 'dicom' : 'other';
    }

    /** En-tête technique DICOM (images, dimensions, modalité, fenêtrage) ou null si illisible. */
    public function describe(array $file): ?array
    {
        try {
            $meta = $this->worker->run('describe', ['content' => base64_encode($file['bytes'])]);
        } catch (ValidationException) {
            return null;
        }

        return ($meta['pixels'] ?? false) ? $meta : null;
    }

    /** Image PNG d'une frame DICOM (fenêtrage facultatif) ou null si le format n'est pas décodable. */
    public function frame(array $file, int $frame = 0, ?float $center = null, ?float $width = null): ?string
    {
        try {
            $result = $this->worker->run('render', array_filter(['content' => base64_encode($file['bytes']), 'frame' => $frame,
                'center' => $center, 'width' => $width], fn ($v) => $v !== null));
        } catch (ValidationException) {
            return null;
        }

        return base64_decode((string) ($result['content'] ?? ''), true) ?: null;
    }

    /** PNG d'une image TIFF / BMP. */
    public function raster(array $file): ?string
    {
        try {
            $result = $this->worker->run('raster', ['content' => base64_encode($file['bytes'])]);
        } catch (ValidationException) {
            return null;
        }

        return base64_decode((string) ($result['content'] ?? ''), true) ?: null;
    }

    public function png(string $bytes): Response
    {
        return response($bytes, 200, ['Content-Type' => 'image/png', 'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }

    /** Fichier source : affiché dans le navigateur s'il le sait (image, PDF), téléchargé sinon (DICOM…). */
    public function download(array $file): Response
    {
        $kind = $this->kind($file);
        $mime = match ($kind) {
            'pdf' => 'application/pdf',
            'image' => (string) (new \finfo(FILEINFO_MIME_TYPE))->buffer($file['bytes']),
            'dicom' => 'application/dicom',
            default => 'application/octet-stream',
        };
        $name = preg_replace('/[^A-Za-z0-9._-]+/', '_', $file['name']) ?: 'fichier';

        return response($file['bytes'], 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => (in_array($kind, ['pdf', 'image'], true) ? 'inline' : 'attachment') . '; filename="' . $name . '"',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /** Fenêtres de lecture proposées : celle de l'en-tête DICOM, puis les fenêtres usuelles du scanner (CT). */
    public function windows(?array $meta): array
    {
        $windows = ['auto' => ['label' => 'Automatique (plage des valeurs)', 'center' => null, 'width' => null]];
        if (isset($meta['window_center'], $meta['window_width'])) {
            $windows['header'] = ['label' => 'Fenêtre de l’examen', 'center' => $meta['window_center'], 'width' => $meta['window_width']];
        }
        if (($meta['modality'] ?? null) === 'CT') {
            $windows += [
                'soft' => ['label' => 'Tissus mous (40 / 400)', 'center' => 40, 'width' => 400],
                'lung' => ['label' => 'Poumon (-600 / 1500)', 'center' => -600, 'width' => 1500],
                'bone' => ['label' => 'Os (300 / 1500)', 'center' => 300, 'width' => 1500],
                'brain' => ['label' => 'Cerveau (40 / 80)', 'center' => 40, 'width' => 80],
            ];
        }

        return $windows;
    }
}
