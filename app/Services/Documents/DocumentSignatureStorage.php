<?php

namespace App\Services\Documents;

use App\Models\DocumentSignatureRequest;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class DocumentSignatureStorage
{
    public function configuredDisk(): string
    {
        $disk = (string) config('services.document_signatures.disk', 'local');

        if (!array_key_exists($disk, (array) config('filesystems.disks', []))) {
            throw new InvalidArgumentException('Disque de stockage des signatures inconnu : '.$disk);
        }

        return $disk;
    }

    public function diskFor(DocumentSignatureRequest $request): string
    {
        $disk = (string) data_get($request->metadata, 'signed_disk', 'local');

        return array_key_exists($disk, (array) config('filesystems.disks', [])) ? $disk : 'local';
    }

    public function put(DocumentSignatureRequest $request, string $bytes): array
    {
        $disk = $this->configuredDisk();
        $path = 'document-signatures/'.$request->id.'/signed.pdf';
        $stored = Storage::disk($disk)->put($path, $bytes, ['visibility' => 'private']);

        if (!$stored) {
            throw new InvalidArgumentException('Le PDF signé n’a pas pu être stocké durablement.');
        }

        return [
            'disk' => $disk,
            'path' => $path,
            'sha256' => hash('sha256', $bytes),
        ];
    }

    public function download(DocumentSignatureRequest $request, string $filename): StreamedResponse
    {
        $path = data_get($request->metadata, 'signed_path');
        abort_unless(is_string($path) && $path !== '' && !str_contains($path, '..'), 404);

        $disk = $this->diskFor($request);
        abort_unless(Storage::disk($disk)->exists($path), 404);

        return Storage::disk($disk)->download($path, $filename, [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function status(): array
    {
        $disk = $this->configuredDisk();

        return [
            'disk' => $disk,
            'durable' => $disk !== 'local',
            'driver' => config("filesystems.disks.{$disk}.driver"),
        ];
    }
}
