<?php

namespace Tests\Unit\Services;

use App\Models\FifaConnect\Address;
use App\Models\FifaConnect\Facility;
use App\Models\FifaConnect\Field;
use App\Models\FifaConnect\Organisation;
use App\Models\FifaConnect\OrganisationLocalName;
use App\Models\FifaConnect\SupportedDiscipline;
use App\Services\FifaConnect\FacilityLocalXmlSerializer;
use App\Services\FifaConnect\OrganisationLocalXmlSerializer;
use App\Services\FifaConnect\SchemaCatalog;
use App\Services\FifaConnect\XsdValidator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Tests\TestCase;

class OrganisationFacilityXmlSerializerTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dir = sys_get_temp_dir()
            . '/fc-org-xsd-' . bin2hex(random_bytes(6));

        mkdir($this->dir, 0777, true);

        mkdir($this->dir . '/includes', 0777, true);

        file_put_contents(
            $this->dir . '/includes/iso639-2-language-code.xsd',
            <<<'XML'
<?xml version="1.0"?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">
  <xs:simpleType name="ISO639-2Type">
    <xs:restriction base="xs:string">
      <xs:enumeration value="fre"/>
      <xs:enumeration value="eng"/>
    </xs:restriction>
  </xs:simpleType>
</xs:schema>
XML
        );

        file_put_contents(
            $this->dir . '/includes/iso3166-country-code.xsd',
            <<<'XML'
<?xml version="1.0"?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">
  <xs:simpleType name="ISO3166CountryCode">
    <xs:restriction base="xs:string">
      <xs:enumeration value="FR"/>
      <xs:enumeration value="NC"/>
    </xs:restriction>
  </xs:simpleType>
</xs:schema>
XML
        );

        file_put_contents(
            $this->dir . '/includes/iso3166-13-country-code.xsd',
            <<<'XML'
<?xml version="1.0"?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">
  <xs:simpleType name="ISO3166-13CountryCode">
    <xs:restriction base="xs:string">
      <xs:enumeration value="FR"/>
      <xs:enumeration value="NC"/>
    </xs:restriction>
  </xs:simpleType>
</xs:schema>
XML
        );

        file_put_contents(
            $this->dir . '/includes/iso4217-currency-code.xsd',
            <<<'XML'
<?xml version="1.0"?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">
  <xs:simpleType name="ISO4217CurrencyCode">
    <xs:restriction base="xs:string">
      <xs:enumeration value="EUR"/>
      <xs:enumeration value="XPF"/>
    </xs:restriction>
  </xs:simpleType>
</xs:schema>
XML
        );


        file_put_contents($this->dir . '/generic.xsd', <<<'XML'
<?xml version="1.0"?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">
  <xs:simpleType name="GenderType">
    <xs:restriction base="xs:string">
      <xs:enumeration value="male"/>
      <xs:enumeration value="female"/>
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="NationalIdentifierNatureType">
    <xs:restriction base="xs:string">
      <xs:enumeration value="PassportNumber"/>
      <xs:enumeration value="PersonalID"/>
    </xs:restriction>
  </xs:simpleType>
</xs:schema>
XML);

        file_put_contents($this->dir . '/registration.xsd', <<<'XML'
<?xml version="1.0"?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">
  <xs:simpleType name="FIFAIdentifier">
    <xs:restriction base="xs:string">
      <xs:length value="7"/>
      <xs:pattern value="^[0123456789ABCDEFGHIJKLMNPQRSTUVWXYZ]{6}[0123456789ABCDEFGHIJKLMNPQRSTU]$"/>
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="SimpleStatusType">
    <xs:restriction base="xs:string">
      <xs:enumeration value="active"/>
      <xs:enumeration value="inactive"/>
      <xs:enumeration value="pending"/>
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="OrganisationNatureType">
    <xs:restriction base="xs:string">
      <xs:enumeration value="NationalAssociation"/>
      <xs:enumeration value="Club"/>
      <xs:enumeration value="Team"/>
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="DisciplineType">
    <xs:restriction base="xs:string">
      <xs:enumeration value="Football"/>
      <xs:enumeration value="Futsal"/>
      <xs:enumeration value="BeachSoccer"/>
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="GroundNatureType">
    <xs:restriction base="xs:string">
      <xs:enumeration value="grass"/>
      <xs:enumeration value="turf"/>
      <xs:enumeration value="unknown"/>
    </xs:restriction>
  </xs:simpleType>
</xs:schema>
XML);

        foreach (['competition.xsd', 'discipline.xsd', 'scenarios.xsd'] as $file) {
            file_put_contents(
                $this->dir . '/' . $file,
                '<?xml version="1.0"?><xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema"/>'
            );
        }

        config(['services.fifa_connect.xsd_path' => $this->dir]);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->dir);

        parent::tearDown();
    }

    public function test_organisation_local_requires_exactly_one_address(): void
    {
        $organisation = $this->organisation();
        $organisation->setRelation('localNames', new Collection());
        $organisation->setRelation('nationalIdentifiers', new Collection());
        $organisation->setRelation('supportedDisciplines', new Collection());
        $organisation->setRelation('addresses', new Collection());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('exactly one OfficialAddress');

        $this->organisationSerializer()->serialize(
            $organisation,
            false
        );
    }

    public function test_organisation_local_serializes_supported_discipline_and_address(): void
    {
        $organisation = $this->organisation();

        $organisation->setRelation(
            'localNames',
            new Collection([
                new OrganisationLocalName([
                    'language' => 'fre',
                    'name' => 'Club Exemple',
                    'short_name' => 'CE',
                ]),
            ])
        );
        $organisation->setRelation(
            'nationalIdentifiers',
            new Collection()
        );
        $organisation->setRelation(
            'supportedDisciplines',
            new Collection([
                new SupportedDiscipline([
                    'discipline' => 'Football',
                    'gender' => 'male',
                ]),
            ])
        );
        $organisation->setRelation(
            'addresses',
            new Collection([$this->address('organisation')])
        );

        $xml = $this->organisationSerializer()->serialize(
            $organisation,
            false
        );

        $this->assertStringContainsString(
            '<OrganisationLocal',
            $xml
        );
        $this->assertStringContainsString(
            'OrganisationNature="Club"',
            $xml
        );
        $this->assertStringContainsString(
            '<SupportedDiscipline Discipline="Football" Gender="male"',
            $xml
        );
        $this->assertStringContainsString(
            '<OfficialAddress Country="FR"',
            $xml
        );
    }

    public function test_facility_requires_at_least_one_field(): void
    {
        $facility = $this->facility();
        $facility->setRelation('localNames', new Collection());
        $facility->setRelation('fields', new Collection());
        $facility->setRelation(
            'addresses',
            new Collection([$this->address('facility')])
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('at least one Field');

        $this->facilitySerializer()->serialize($facility, false);
    }

    public function test_facility_serializes_field_before_official_address(): void
    {
        $facility = $this->facility();

        $facility->setRelation('localNames', new Collection());
        $facility->setRelation(
            'fields',
            new Collection([
                new Field([
                    'order_number' => 1,
                    'discipline' => 'Football',
                    'capacity' => 1200,
                    'ground_nature' => 'grass',
                    'length' => 105,
                    'width' => 68,
                ]),
            ])
        );
        $facility->setRelation(
            'addresses',
            new Collection([$this->address('facility')])
        );

        $xml = $this->facilitySerializer()->serialize(
            $facility,
            false
        );

        $this->assertStringContainsString(
            '<FacilityLocal',
            $xml
        );
        $this->assertStringContainsString(
            '<Field OrderNumber="1" Discipline="Football"',
            $xml
        );

        $this->assertLessThan(
            strpos($xml, '<OfficialAddress'),
            strpos($xml, '<Field')
        );
    }

    private function organisation(): Organisation
    {
        return new Organisation([
            'organisation_fifa_id' => 'ABC123A',
            'status' => 'active',
            'local_name' => 'Club Exemple',
            'local_language' => 'fre',
            'local_country' => 'FR',
            'international_name' => 'Example Club',
            'organisation_nature' => 'Club',
        ]);
    }

    private function facility(): Facility
    {
        return new Facility([
            'facility_fifa_id' => 'ABC123A',
            'status' => 'active',
            'local_name' => 'Stade Exemple',
            'local_language' => 'fre',
            'local_country' => 'FR',
            'international_name' => 'Example Stadium',
        ]);
    }

    private function address(string $ownerType): Address
    {
        return new Address([
            'owner_type' => $ownerType,
            'country' => 'FR',
            'region' => 'IDF',
            'postal_code' => '75001',
            'town' => 'Paris',
            'address' => '1 rue Exemple',
        ]);
    }

    private function organisationSerializer(): OrganisationLocalXmlSerializer
    {
        return new OrganisationLocalXmlSerializer(
            new SchemaCatalog(),
            new XsdValidator()
        );
    }

    private function facilitySerializer(): FacilityLocalXmlSerializer
    {
        return new FacilityLocalXmlSerializer(
            new SchemaCatalog(),
            new XsdValidator()
        );
    }
}
