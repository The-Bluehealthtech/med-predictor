<?php

namespace App\Http\Controllers\Licensing;

use App\Http\Controllers\Controller;
use App\Models\PlayerLicenseDocument;
use App\Services\Licensing\LicenseWorkflow;
use Illuminate\Http\Request;

/** Consultation d'une pièce justificative : club ou fédération du périmètre de la demande. */
class LicenseDocumentController extends Controller
{
    public function __construct(private readonly LicenseWorkflow $workflow)
    {
    }

    public function show(Request $request, PlayerLicenseDocument $document)
    {
        $user = $request->user();
        $license = $document->license;
        abort_unless($license && ($this->workflow->canRequest($user) || $this->workflow->canApprove($user)) && $this->workflow->canAccess($user, $license), 403);

        $content = base64_decode((string) PlayerLicenseDocument::query()->whereKey($document->id)->value('content_base64'), true);
        abort_if($content === false, 410, 'Pièce illisible.');
        $inline = in_array($document->mime_type, ['application/pdf', 'image/jpeg', 'image/png'], true);
        $name = str_replace(['"', "\r", "\n"], '', $document->original_name);

        return response($content, 200, [
            'Content-Type' => $document->mime_type,
            'Content-Disposition' => ($inline ? 'inline' : 'attachment') . '; filename="' . $name . '"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
            'Content-Security-Policy' => 'sandbox',
        ]);
    }
}
