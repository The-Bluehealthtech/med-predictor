<?php

namespace Tests\Feature;

use App\Http\Controllers\MedicalSecretaryController;
use App\Models\Appointment;
use App\Models\Document;
use App\Models\MedicalFile;
use App\Models\PCMA;
use App\Models\User;
use App\Models\Visit;
use App\Services\MedicalFileStore;
use App\Services\MedicalImageRenderer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Process\Process;
use Tests\TestCase;

/**
 * Visionneuse commune des fichiers médicaux : stockage en base (pas de disque persistant),
 * rendu DICOM côté serveur (mono et multi-images, fenêtrage), TIFF converti, accès contrôlé.
 * Les tests de rendu utilisent le vrai décodeur (MEDICAL_IMAGING_PYTHON) et sont ignorés s'il manque.
 */
class MedicalFileViewerTest extends TestCase
{
    use DatabaseTransactions;

    private int $clubId;

    private int $playerId;

    private int $athleteId;

    private User $doctor;

    protected function setUp(): void
    {
        parent::setUp();
        foreach (['pcma.show' => '/_t/pcma/{pcma}', 'health-records.index' => '/_t/hr', 'secretary.dashboard' => '/_t/sec'] as $name => $uri) {
            if (!Route::has($name)) {
                Route::get($uri, fn () => 'ok')->name($name);
            }
        }
        app('router')->getRoutes()->refreshNameLookups();
        $associationId = (int) DB::table('associations')->insertGetId(['name' => 'Fédération Imagerie', 'country' => 'Tunisie', 'created_at' => now(), 'updated_at' => now()]);
        $this->clubId = (int) DB::table('clubs')->insertGetId(['name' => 'Club Imagerie', 'association_id' => $associationId, 'created_at' => now(), 'updated_at' => now()]);
        $this->playerId = (int) DB::table('players')->insertGetId(['name' => 'Samir Image', 'first_name' => 'Samir', 'last_name' => 'Image', 'date_of_birth' => '2000-05-14',
            'club_id' => $this->clubId, 'association_id' => $associationId, 'created_at' => now(), 'updated_at' => now()]);
        $teamId = DB::table('teams')->value('id') ?? DB::table('teams')->insertGetId(['name' => 'Équipe Imagerie', 'club_id' => $this->clubId, 'created_at' => now(), 'updated_at' => now()]);
        $this->athleteId = (int) DB::table('athletes')->insertGetId(['name' => 'Samir Image', 'dob' => '2000-05-14', 'nationality' => 'TN', 'team_id' => $teamId,
            'player_id' => $this->playerId, 'created_at' => now(), 'updated_at' => now()]);
        $this->doctor = User::factory()->create(['role' => 'club_medical', 'club_id' => $this->clubId, 'status' => 'active', 'tenant_id' => 1]);
    }

    private function decoder(): void
    {
        $python = (string) config('medical_imaging.python');
        $check = new Process([$python, '-c', 'import pydicom, numpy, PIL']);
        if (!is_executable($python) || $check->run() !== 0) {
            $this->markTestSkipped('Environnement de décodage DICOM absent (MEDICAL_IMAGING_PYTHON).');
        }
    }

    /** DICOM synthétique généré par pydicom (MONOCHROME2 16 bits, $frames images, compression RLE facultative). */
    private function dicom(int $frames = 1, bool $rle = false, string $modality = 'CT'): string
    {
        $script = <<<'PY'
import sys, io, numpy as np
from pydicom.dataset import Dataset, FileMetaDataset
from pydicom.uid import ExplicitVRLittleEndian, RLELossless, CTImageStorage, generate_uid
frames, rle, modality = int(sys.argv[1]), sys.argv[2] == '1', sys.argv[3]
meta = FileMetaDataset(); meta.MediaStorageSOPClassUID = CTImageStorage; meta.MediaStorageSOPInstanceUID = generate_uid(); meta.TransferSyntaxUID = ExplicitVRLittleEndian
ds = Dataset(); ds.file_meta = meta; ds.SOPClassUID = CTImageStorage; ds.SOPInstanceUID = meta.MediaStorageSOPInstanceUID
ds.Modality = modality; ds.Rows = 32; ds.Columns = 32; ds.SamplesPerPixel = 1; ds.PhotometricInterpretation = 'MONOCHROME2'
ds.BitsAllocated = 16; ds.BitsStored = 16; ds.HighBit = 15; ds.PixelRepresentation = 0; ds.NumberOfFrames = frames
ds.RescaleSlope = 1; ds.RescaleIntercept = -1024; ds.WindowCenter = 40; ds.WindowWidth = 400
ds.PixelData = (np.arange(frames * 32 * 32, dtype=np.uint16) % 2000).tobytes()
if rle: ds.compress(RLELossless)
buf = io.BytesIO(); ds.save_as(buf, enforce_file_format=True); sys.stdout.buffer.write(buf.getvalue())
PY;
        $process = new Process([config('medical_imaging.python'), '-c', $script, (string) $frames, $rle ? '1' : '0', $modality]);
        $process->mustRun();

        return $process->getOutput();
    }

    private function stored(string $bytes, string $name, string $owner = 'pcma'): MedicalFile
    {
        return MedicalFile::query()->create(['owner_type' => $owner, 'file_name' => $name, 'mime_type' => 'application/octet-stream', 'size' => strlen($bytes),
            'sha256' => hash('sha256', $bytes), 'content_base64' => base64_encode($bytes)]);
    }

    private function pcma(array $files): PCMA
    {
        return PCMA::query()->create(['athlete_id' => $this->athleteId, 'player_id' => $this->playerId, 'assessor_id' => $this->doctor->id, 'type' => 'pcma',
            'status' => 'pending', 'result_json' => [], 'assessment_date' => '2026-09-30'] + $files);
    }

    private function document(string $ref): Document
    {
        $appointment = Appointment::create(['athlete_id' => $this->athleteId, 'created_by' => $this->doctor->id, 'appointment_date' => now(), 'appointment_type' => 'consultation', 'status' => 'Enregistré']);
        $visit = Visit::create(['athlete_id' => $this->athleteId, 'appointment_id' => $appointment->id, 'visit_date' => now(), 'visit_type' => 'consultation', 'status' => 'Enregistré']);

        return Document::query()->create(['visit_id' => $visit->id, 'document_type' => 'radiology', 'file_name' => 'irm.dcm', 'file_path' => $ref, 'file_size' => 1,
            'mime_type' => 'application/dicom', 'uploaded_by' => $this->doctor->id, 'status' => 'pending']);
    }

    public function test_store_keeps_files_in_database_and_reads_legacy_paths(): void
    {
        $this->actingAs($this->doctor);
        $store = app(MedicalFileStore::class);
        $file = $store->put(UploadedFile::fake()->createWithContent('ecg.pdf', '%PDF-1.4 test'), 'pcma', 'ecg_file');

        $this->assertSame(['%PDF-1.4 test', 'ecg.pdf'], [$store->read($file->ref())['bytes'], $store->name($file->ref())]);
        $this->assertSame(hash('sha256', '%PDF-1.4 test'), $file->sha256);
        $this->assertArrayNotHasKey('content_base64', $file->toArray(), 'contenu jamais sérialisé');
        $this->assertNull($store->read('medical-file:999999'));
        $this->assertNull($store->read('../../etc/passwd'));
    }

    public function test_secretary_upload_is_stored_in_database(): void
    {
        $secretary = User::factory()->create(['role' => 'secretary', 'club_id' => $this->clubId, 'status' => 'active', 'tenant_id' => 1]);
        $this->actingAs($secretary);
        $appointment = Appointment::create(['athlete_id' => $this->athleteId, 'created_by' => $secretary->id, 'appointment_date' => now(), 'appointment_type' => 'consultation', 'status' => 'Enregistré']);
        Visit::create(['athlete_id' => $this->athleteId, 'appointment_id' => $appointment->id, 'visit_date' => now(), 'visit_type' => 'consultation', 'status' => 'Enregistré']);
        $request = Request::create('/', 'POST', ['document_type' => 'lab_result'], [], ['document_file' => UploadedFile::fake()->createWithContent('bilan.pdf', '%PDF-1.4 bilan')]);
        $request->setUserResolver(fn () => $secretary);
        app()->instance('request', $request);

        app(MedicalSecretaryController::class)->uploadDocument($request, $appointment);

        $document = Document::query()->latest('id')->firstOrFail();
        $this->assertStringStartsWith('medical-file:', $document->file_path);
        $this->assertSame('%PDF-1.4 bilan', app(MedicalFileStore::class)->read($document->file_path)['bytes']);
    }

    public function test_kind_is_detected_from_content(): void
    {
        $renderer = app(MedicalImageRenderer::class);
        $this->assertSame('dicom', $renderer->kind(['bytes' => str_repeat("\0", 128) . 'DICM' . 'xxxx', 'name' => 'a.bin', 'mime' => null]));
        $this->assertSame('pdf', $renderer->kind(['bytes' => '%PDF-1.7 ...', 'name' => 'a.dcm', 'mime' => null]));
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
        $this->assertSame('image', $renderer->kind(['bytes' => $png, 'name' => 'x', 'mime' => null]));
        $this->assertSame('other', $renderer->kind(['bytes' => 'hello', 'name' => 'notes.txt', 'mime' => 'text/plain']));
    }

    public function test_multiframe_rle_dicom_is_rendered_with_frames_and_windows(): void
    {
        $this->decoder();
        $this->actingAs($this->doctor);
        $pcma = $this->pcma(['mri_file' => $this->stored($this->dicom(3, true), 'irm.dcm')->ref()]);

        $page = $this->get(route('medical-files.pcma', [$pcma, 'mri_file']))->assertOk();
        $page->assertSee('3 image(s)')->assertSee('RLE Lossless')->assertSee('Fenêtre de l’examen')->assertSee('Poumon (-600 / 1500)');

        foreach ([0, 2] as $frame) {
            $png = $this->get(route('medical-files.pcma.frame', [$pcma, 'mri_file']) . "?frame={$frame}&center=40&width=400")->assertOk();
            $this->assertSame('image/png', $png->headers->get('Content-Type'));
            $this->assertStringStartsWith("\x89PNG", $png->getContent());
        }
        $this->get(route('medical-files.pcma.frame', [$pcma, 'mri_file']) . '?frame=5')->assertStatus(422);
    }

    public function test_tiff_is_converted_and_unreadable_dicom_is_reported_not_simulated(): void
    {
        $this->decoder();
        $this->actingAs($this->doctor);
        $tiff = (new Process([config('medical_imaging.python'), '-c', "import io,sys\nfrom PIL import Image\nb=io.BytesIO(); Image.new('L',(40,30),90).save(b,format='TIFF'); sys.stdout.buffer.write(b.getvalue())"]))->mustRun()->getOutput();
        $broken = str_repeat("\0", 128) . 'DICM' . 'not a dataset';
        $pcma = $this->pcma(['xray_file' => $this->stored($tiff, 'radio.tif')->ref(), 'ct_scan_file' => $this->stored($broken, 'scanner.dcm')->ref()]);

        $this->assertStringStartsWith("\x89PNG", $this->get(route('medical-files.pcma.frame', [$pcma, 'xray_file']))->assertOk()->getContent());
        $this->get(route('medical-files.pcma', [$pcma, 'ct_scan_file']))->assertOk()->assertSee('ne contient pas d’image lisible');
        $this->get(route('medical-files.pcma.frame', [$pcma, 'ct_scan_file']))->assertStatus(422);
    }

    public function test_intake_document_access_follows_club_and_medical_rights(): void
    {
        $document = $this->document($this->stored('%PDF-1.4 cr', 'cr.pdf', 'visit_document')->ref());

        $this->actingAs($this->doctor)->get(route('medical-files.document', $document))->assertOk()->assertSee('<iframe', false);
        $this->actingAs(User::factory()->create(['role' => 'secretary', 'club_id' => $this->clubId, 'status' => 'active', 'tenant_id' => 1]))
            ->get(route('medical-files.document.source', $document))->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->actingAs(User::factory()->create(['role' => 'secretary', 'club_id' => $this->clubId + 1000, 'status' => 'active', 'tenant_id' => 1]))
            ->get(route('medical-files.document', $document))->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'club_admin', 'club_id' => $this->clubId, 'status' => 'active', 'tenant_id' => 1]))
            ->get(route('medical-files.document', $document))->assertForbidden();
        $this->actingAs($this->doctor)->get(route('medical-files.document', $this->document('medical-intake/1/perdu.dcm')))->assertNotFound();
    }

    public function test_browser_preview_no_longer_simulates_dicom(): void
    {
        $js = file_get_contents(public_path('js/image-viewer.js'));
        foreach (['Test Patient', 'Image DICOM de Test', 'createTestImage', 'parseDicomData'] as $fake) {
            $this->assertStringNotContainsString($fake, $js);
        }
        $this->assertStringNotContainsString('cornerstone', file_get_contents(resource_path('views/layouts/app.blade.php')));
    }
}
