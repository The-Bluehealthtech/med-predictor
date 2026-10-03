<?php

namespace Tests\Feature;

use App\Models\FhirPatientLink;
use App\Models\Player;
use App\Models\User;
use App\Services\DicomWeb\DicomWebClient;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Process\Process;
use Tests\Concerns\GrantsSharingConsent;
use Tests\TestCase;

/**
 * Imagerie des établissements dans la visionneuse de FIT par DICOMweb (IHE RAD WIA) :
 * QIDO-RS (séries, images), WADO-RS (image d'origine, multipart/related), accès contrôlé.
 */
class DicomWebViewerTest extends TestCase
{
    use DatabaseTransactions;
    use GrantsSharingConsent;

    private const FHIR = 'http://fit-fhir.test/fhir';

    private const PACS = 'https://pacs.example.org/dicom-web';

    private const STUDY = '1.2.840.113619.2.55.3';

    private const SERIES = '1.2.840.113619.2.55.3.1';

    private Player $player;

    private User $doctor;

    protected function setUp(): void
    {
        parent::setUp();
        if (!Route::has('health-records.index')) {
            Route::get('/_t/health-records', fn () => 'ok')->name('health-records.index');
            app('router')->getRoutes()->refreshNameLookups();
        }
        config(['fhir.base_url' => self::FHIR, 'medical_imaging.pacs_url' => self::PACS, 'medical_imaging.pacs_token' => 'jeton-pacs']);
        $associationId = (int) DB::table('associations')->insertGetId(['name' => 'Fédération DICOMweb', 'country' => 'Tunisie', 'created_at' => now(), 'updated_at' => now()]);
        $clubId = (int) DB::table('clubs')->insertGetId(['name' => 'Club DICOMweb', 'association_id' => $associationId, 'created_at' => now(), 'updated_at' => now()]);
        $id = DB::table('players')->insertGetId(['name' => 'Samir Imagerie', 'first_name' => 'Samir', 'last_name' => 'Imagerie', 'club_id' => $clubId,
            'association_id' => $associationId, 'created_at' => now(), 'updated_at' => now()]);
        $this->player = Player::withoutGlobalScopes()->findOrFail($id);
        $this->doctor = User::factory()->create(['role' => 'club_medical', 'club_id' => $clubId, 'status' => 'active', 'tenant_id' => 1]);
        FhirPatientLink::query()->create(['player_id' => $id, 'role' => 'fit', 'patient_id' => 'fit-1', 'status' => 'linked']);
    }

    private function fakeServers(?string $dicom = null): void
    {
        $boundary = 'fit-test-boundary';
        Http::fake([
            self::FHIR . '/ImagingStudy?*' => Http::response(['resourceType' => 'Bundle', 'entry' => [['resource' => [
                'resourceType' => 'ImagingStudy', 'id' => 'i1', 'started' => '2026-09-29T09:30:00Z', 'description' => 'IRM genou droit', 'subject' => ['reference' => 'Patient/fit-1'],
                'identifier' => [['system' => 'urn:dicom:uid', 'value' => 'urn:oid:' . self::STUDY]], 'modality' => [['code' => 'MR']]]]]]),
            self::PACS . '/studies/' . self::STUDY . '/series' => Http::response([
                ['0020000E' => ['vr' => 'UI', 'Value' => [self::SERIES . '2']], '00200011' => ['vr' => 'IS', 'Value' => [2]], '00080060' => ['vr' => 'CS', 'Value' => ['MR']], '0008103E' => ['vr' => 'LO', 'Value' => ['T2 coronal']]],
                ['0020000E' => ['vr' => 'UI', 'Value' => [self::SERIES]], '00200011' => ['vr' => 'IS', 'Value' => [1]], '00080060' => ['vr' => 'CS', 'Value' => ['MR']], '0008103E' => ['vr' => 'LO', 'Value' => ['T1 sagittal']], '00201209' => ['vr' => 'IS', 'Value' => [2]]],
            ]),
            self::PACS . '/studies/' . self::STUDY . '/series/' . self::SERIES . '/instances' => Http::response([
                ['00080018' => ['vr' => 'UI', 'Value' => ['1.2.3.2']], '00200013' => ['vr' => 'IS', 'Value' => [2]]],
                ['00080018' => ['vr' => 'UI', 'Value' => ['1.2.3.1']], '00200013' => ['vr' => 'IS', 'Value' => [1]], '00280008' => ['vr' => 'IS', 'Value' => [2]]],
            ]),
            self::PACS . '/studies/' . self::STUDY . '/series/' . self::SERIES . '/instances/1.2.3.1/metadata' => Http::response([
                ['00281050' => ['vr' => 'DS', 'Value' => [300]], '00281051' => ['vr' => 'DS', 'Value' => [1200]]],
            ]),
            self::PACS . '/studies/' . self::STUDY . '/series/' . self::SERIES . '/instances/1.2.3.1' => Http::response(
                "--{$boundary}\r\nContent-Type: application/dicom\r\n\r\n" . ($dicom ?? 'DICM') . "\r\n--{$boundary}--\r\n", 200,
                ['Content-Type' => 'multipart/related; type="application/dicom"; boundary=' . $boundary]),
        ]);
    }

    public function test_multipart_related_bodies_are_extracted_byte_exact(): void
    {
        $binary = "DICM\x00\x01\r\n--pas-une-limite\xff";
        $body = "--b1\r\nContent-Type: application/dicom\r\nContent-Location: x\r\n\r\n{$binary}\r\n--b1\r\nContent-Type: application/dicom\r\n\r\nSECOND\r\n--b1--\r\n";

        $this->assertSame([$binary, 'SECOND'], DicomWebClient::multipart('multipart/related; type="application/dicom"; boundary="b1"', $body));
        $this->assertSame([], DicomWebClient::multipart('application/dicom', $body));
    }

    public function test_study_page_lists_series_and_images_from_qido(): void
    {
        $this->grantSharingConsent($this->player);
        $this->fakeServers();

        $page = $this->actingAs($this->doctor)->get(route('clinical.dicomweb.study', ['player' => $this->player->id, 'study' => self::STUDY]))->assertOk();

        $page->assertSee('IRM genou droit')->assertSee('T1 sagittal')->assertSee('T2 coronal')->assertSee('Fenêtre de l’examen', false);
        $page->assertSee('max="3"', false); // 2 images de l'instance 1.2.3.1 (multi-images) + 1 image de 1.2.3.2
        $this->assertStringContainsString('{"instance":"1.2.3.1","frame":1}', html_entity_decode($page->getContent()));
        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/series') && $r->hasHeader('Accept', 'application/dicom+json') && $r->hasHeader('Authorization', 'Bearer jeton-pacs'));
        $this->assertTrue(DB::table('audit_logs')->where('action', 'dicomweb_study_view')->exists());
    }

    public function test_access_requires_consent_a_linked_study_and_a_secure_pacs(): void
    {
        $this->fakeServers();
        $url = route('clinical.dicomweb.study', ['player' => $this->player->id, 'study' => self::STUDY]);
        $this->actingAs($this->doctor)->get($url)->assertForbidden(); // sans consentement (IHE PCF)

        $this->grantSharingConsent($this->player);
        $this->actingAs($this->doctor)->get(route('clinical.dicomweb.study', ['player' => $this->player->id, 'study' => '1.2.3.999']))->assertNotFound();
        $this->actingAs($this->doctor)->get(route('clinical.dicomweb.frame', ['player' => $this->player->id, 'study' => self::STUDY, 'series' => self::SERIES, 'instance' => '1.2.3.1']))
            ->assertForbidden(); // image demandée sans avoir ouvert l'examen

        config(['medical_imaging.pacs_url' => 'http://pacs.insecure.test']);
        $this->actingAs($this->doctor)->get($url)->assertStatus(503);
    }

    public function test_wado_rs_image_is_rendered_by_fit(): void
    {
        $python = (string) config('medical_imaging.python');
        if (!is_executable($python) || (new Process([$python, '-c', 'import pydicom, numpy, PIL']))->run() !== 0) {
            $this->markTestSkipped('Environnement de décodage DICOM absent (MEDICAL_IMAGING_PYTHON).');
        }
        $script = "import io,sys,numpy as np\nfrom pydicom.dataset import Dataset,FileMetaDataset\nfrom pydicom.uid import ExplicitVRLittleEndian,MRImageStorage\n"
            . "m=FileMetaDataset();m.MediaStorageSOPClassUID=MRImageStorage;m.MediaStorageSOPInstanceUID='1.2.3.1';m.TransferSyntaxUID=ExplicitVRLittleEndian\n"
            . "d=Dataset();d.file_meta=m;d.SOPClassUID=MRImageStorage;d.SOPInstanceUID='1.2.3.1';d.Modality='MR';d.Rows=16;d.Columns=16;d.SamplesPerPixel=1\n"
            . "d.PhotometricInterpretation='MONOCHROME2';d.BitsAllocated=16;d.BitsStored=16;d.HighBit=15;d.PixelRepresentation=0;d.NumberOfFrames=2\n"
            . "d.PixelData=(np.arange(2*16*16,dtype=np.uint16)).tobytes()\nb=io.BytesIO();d.save_as(b,enforce_file_format=True);sys.stdout.buffer.write(b.getvalue())";
        $dicom = (new Process([$python, '-c', $script]))->mustRun()->getOutput();
        $this->grantSharingConsent($this->player);
        $this->fakeServers($dicom);

        $this->actingAs($this->doctor)->get(route('clinical.dicomweb.study', ['player' => $this->player->id, 'study' => self::STUDY]))->assertOk();
        $png = $this->actingAs($this->doctor)->get(route('clinical.dicomweb.frame', ['player' => $this->player->id, 'study' => self::STUDY, 'series' => self::SERIES, 'instance' => '1.2.3.1']) . '?frame=1&center=300&width=1200');

        $png->assertOk()->assertHeader('Content-Type', 'image/png');
        $this->assertStringStartsWith("\x89PNG", $png->getContent());
        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/instances/1.2.3.1') && str_contains($r->header('Accept')[0] ?? '', 'multipart/related; type="application/dicom"'));
    }
}
