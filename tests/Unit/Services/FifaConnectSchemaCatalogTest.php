<?php

namespace Tests\Unit\Services;

use App\Services\FifaConnect\SchemaCatalog;
use RuntimeException;
use Tests\TestCase;

class FifaConnectSchemaCatalogTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dir = sys_get_temp_dir()
            . '/fc-xsd-' . bin2hex(random_bytes(6));

        mkdir($this->dir, 0777, true);

        $this->writeSchema('generic.xsd', <<<'XML'
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

        $this->writeSchema('registration.xsd', <<<'XML'
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
            $this->writeSchema($file, '<?xml version="1.0"?><xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema"/>');
        }

        config(['services.fifa_connect.xsd_path' => $this->dir]);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->dir . '/*') ?: [] as $file) {
            @unlink($file);
        }

        @rmdir($this->dir);

        parent::tearDown();
    }

    public function test_catalog_reads_enum_values_from_xsd(): void
    {
        $catalog = new SchemaCatalog();

        $this->assertSame(
            ['male', 'female'],
            $catalog->enumValues('GenderType')
        );

        $this->assertSame(
            ['Football', 'Futsal', 'BeachSoccer'],
            $catalog->enumValues('DisciplineType')
        );
    }

    public function test_valid_fifa_identifier_matches_xsd_facets(): void
    {
        $catalog = new SchemaCatalog();

        $catalog->assertFifaIdentifier('ABC123A');

        $this->addToAssertionCount(1);
    }

    public function test_synthetic_application_identifier_is_rejected(): void
    {
        $this->expectException(RuntimeException::class);

        (new SchemaCatalog())->assertFifaIdentifier(
            'FIFA2026TUN001'
        );
    }

    public function test_unknown_enum_value_is_rejected(): void
    {
        $this->expectException(RuntimeException::class);

        (new SchemaCatalog())->assertEnum(
            'DisciplineType',
            'Soccer'
        );
    }

    private function writeSchema(string $file, string $xml): void
    {
        file_put_contents($this->dir . '/' . $file, $xml);
    }
}
