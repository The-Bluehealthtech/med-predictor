<?php

namespace Tests\Unit\Services;

use App\Models\FifaConnect\Person;
use App\Models\FifaConnect\PersonLocalName;
use App\Models\FifaConnect\PersonNationalIdentifier;
use App\Models\FifaConnect\Registration;
use App\Services\FifaConnect\PersonLocalXmlSerializer;
use App\Services\FifaConnect\SchemaCatalog;
use App\Services\FifaConnect\XsdValidator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Tests\TestCase;

class PersonLocalXmlSerializerTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dir = sys_get_temp_dir()
            . '/fc-person-xsd-' . bin2hex(random_bytes(6));

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
  <xs:simpleType name="RegistrationLevelType">
    <xs:restriction base="xs:string">
      <xs:enumeration value="pro"/>
      <xs:enumeration value="amateur"/>
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="PlayerRegistrationNatureType">
    <xs:restriction base="xs:string">
      <xs:enumeration value="Registration"/>
      <xs:enumeration value="Loan"/>
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="DisciplineType">
    <xs:restriction base="xs:string">
      <xs:enumeration value="Football"/>
      <xs:enumeration value="Futsal"/>
      <xs:enumeration value="BeachSoccer"/>
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="TeamOfficialRoleType">
    <xs:restriction base="xs:string">
      <xs:enumeration value="Coach"/>
      <xs:enumeration value="TeamDoctor"/>
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="MatchOfficialRoleType">
    <xs:restriction base="xs:string">
      <xs:enumeration value="Referee"/>
      <xs:enumeration value="VAR"/>
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="OrganisationOfficialRoleType">
    <xs:restriction base="xs:string">
      <xs:enumeration value="President"/>
      <xs:enumeration value="Other"/>
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

    public function test_person_local_and_player_registration_are_serialized_in_xsd_order(): void
    {
        $person = $this->person();

        $localName = new PersonLocalName([
            'language' => 'fre',
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
        ]);

        $identifier = new PersonNationalIdentifier([
            'identifier' => 'P12345',
            'nature' => 'PassportNumber',
            'country' => 'FR',
        ]);

        $registration = new Registration([
            'person_fifa_id' => 'ABC123A',
            'organisation_fifa_id' => 'DEF456B',
            'registration_type' => Registration::TYPE_PLAYER,
            'status' => 'active',
            'registration_valid_from' => Carbon::parse('2026-01-01'),
            'registration_valid_to' => null,
            'level' => 'pro',
            'discipline' => 'Football',
            'registration_nature' => 'Registration',
        ]);

        $person->setRelation(
            'localNames',
            new Collection([$localName])
        );
        $person->setRelation(
            'nationalIdentifiers',
            new Collection([$identifier])
        );
        $person->setRelation(
            'registrations',
            new Collection([$registration])
        );
        $person->setRelation(
            'certifications',
            new Collection()
        );

        $serializer = new PersonLocalXmlSerializer(
            new SchemaCatalog(),
            new XsdValidator()
        );

        $xml = $serializer->serialize($person, false);

        $this->assertStringContainsString(
            '<PersonLocal',
            $xml
        );
        $this->assertStringContainsString(
            'PersonFIFAId="ABC123A"',
            $xml
        );
        $this->assertStringContainsString(
            '<LocalPersonName',
            $xml
        );
        $this->assertStringContainsString(
            '<NationalIdentifier',
            $xml
        );
        $this->assertStringContainsString(
            '<PlayerRegistration',
            $xml
        );

        $this->assertLessThan(
            strpos($xml, '<NationalIdentifier'),
            strpos($xml, '<LocalPersonName')
        );
        $this->assertLessThan(
            strpos($xml, '<PlayerRegistration'),
            strpos($xml, '<NationalIdentifier')
        );
    }

    public function test_required_person_field_is_never_invented(): void
    {
        $person = $this->person();
        $person->place_of_birth = null;

        $person->setRelation('localNames', new Collection());
        $person->setRelation('nationalIdentifiers', new Collection());
        $person->setRelation('registrations', new Collection());
        $person->setRelation('certifications', new Collection());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('place_of_birth is required');

        (new PersonLocalXmlSerializer(
            new SchemaCatalog(),
            new XsdValidator()
        ))->serialize($person, false);
    }

    public function test_invalid_registration_discipline_is_rejected(): void
    {
        $person = $this->person();

        $registration = new Registration([
            'person_fifa_id' => 'ABC123A',
            'organisation_fifa_id' => 'DEF456B',
            'registration_type' => Registration::TYPE_PLAYER,
            'status' => 'active',
            'registration_valid_from' => Carbon::parse('2026-01-01'),
            'level' => 'pro',
            'discipline' => 'Soccer',
            'registration_nature' => 'Registration',
        ]);

        $person->setRelation('localNames', new Collection());
        $person->setRelation('nationalIdentifiers', new Collection());
        $person->setRelation('registrations', new Collection([$registration]));
        $person->setRelation('certifications', new Collection());

        $this->expectException(RuntimeException::class);

        (new PersonLocalXmlSerializer(
            new SchemaCatalog(),
            new XsdValidator()
        ))->serialize($person, false);
    }

    private function person(): Person
    {
        return new Person([
            'person_fifa_id' => 'ABC123A',
            'international_first_name' => 'Jean',
            'international_last_name' => 'Dupont',
            'local_first_name' => 'Jean',
            'local_last_name' => 'Dupont',
            'local_language' => 'fre',
            'local_country' => 'FR',
            'gender' => 'male',
            'nationality' => 'FR',
            'date_of_birth' => Carbon::parse('2000-01-02'),
            'country_of_birth' => 'FR',
            'place_of_birth' => 'Paris',
        ]);
    }
}
