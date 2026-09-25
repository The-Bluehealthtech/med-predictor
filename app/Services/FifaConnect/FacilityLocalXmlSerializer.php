<?php

namespace App\Services\FifaConnect;

use App\Models\FifaConnect\Facility;
use DOMDocument;
use DOMElement;
use RuntimeException;

class FacilityLocalXmlSerializer
{
    private const NS = 'http://fifa.com/fc';
    private const XSI = 'http://www.w3.org/2001/XMLSchema-instance';

    public function __construct(
        private readonly SchemaCatalog $catalog,
        private readonly XsdValidator $validator
    ) {
    }

    public function serialize(Facility $facility, bool $validate = true): string
    {
        $facility->loadMissing([
            'localNames',
            'fields',
            'addresses',
        ]);

        $this->assertFacility($facility);

        if ($facility->fields->isEmpty()) {
            throw new RuntimeException(
                'FacilityLocal requires at least one Field.'
            );
        }

        if ($facility->addresses->count() !== 1) {
            throw new RuntimeException(
                'FacilityLocal requires exactly one OfficialAddress.'
            );
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = true;

        $root = $document->createElementNS(self::NS, 'FacilityLocal');
        $root->setAttributeNS(
            self::XSI,
            'xsi:schemaLocation',
            self::NS . ' scenarios.xsd'
        );

        $this->setOptional(
            $root,
            'FacilityFIFAId',
            $facility->facility_fifa_id
        );
        $root->setAttribute('Status', $facility->status);
        $root->setAttribute('LocalName', $facility->local_name);
        $this->setOptional(
            $root,
            'LocalShortName',
            $facility->local_short_name
        );
        $this->setOptional(
            $root,
            'LocalSystemMAId',
            $facility->local_system_ma_id
        );
        $root->setAttribute(
            'LocalLanguage',
            $facility->local_language
        );
        $root->setAttribute(
            'LocalCountry',
            $facility->local_country
        );
        $this->setOptional(
            $root,
            'InternationalName',
            $facility->international_name
        );
        $this->setOptional(
            $root,
            'InternationalShortName',
            $facility->international_short_name
        );
        $this->setOptional(
            $root,
            'OrganisationFIFAId',
            $facility->organisation_fifa_id
        );
        $this->setOptional(
            $root,
            'ParentFacilityFIFAId',
            $facility->parent_facility_fifa_id
        );

        foreach ($facility->localNames as $name) {
            $this->catalog->assertEnum(
                'ISO639-2Type',
                $name->language
            );

            $node = $document->createElementNS(
                self::NS,
                'LocalFacilityName'
            );
            $node->setAttribute('Language', $name->language);
            $node->setAttribute('Name', $name->name);
            $this->setOptional($node, 'ShortName', $name->short_name);
            $root->appendChild($node);
        }

        foreach ($facility->fields as $field) {
            $this->catalog->assertEnum(
                'DisciplineType',
                $field->discipline
            );
            $this->catalog->assertEnum(
                'GroundNatureType',
                $field->ground_nature
            );

            $node = $document->createElementNS(self::NS, 'Field');
            $node->setAttribute(
                'OrderNumber',
                (string) $field->order_number
            );
            $node->setAttribute(
                'Discipline',
                $field->discipline
            );
            $node->setAttribute(
                'Capacity',
                (string) $field->capacity
            );
            $node->setAttribute(
                'GroundNature',
                $field->ground_nature
            );
            $this->setOptional($node, 'Length', $field->length);
            $this->setOptional($node, 'Width', $field->width);
            $this->setOptional($node, 'Latitude', $field->latitude);
            $this->setOptional($node, 'Longitude', $field->longitude);
            $root->appendChild($node);
        }

        $address = $facility->addresses->first();
        $this->catalog->assertEnum(
            'ISO3166CountryCode',
            $address->country
        );

        $addressNode = $document->createElementNS(
            self::NS,
            'OfficialAddress'
        );
        $addressNode->setAttribute('Country', $address->country);
        $this->setOptional($addressNode, 'Region', $address->region);
        $this->setOptional(
            $addressNode,
            'PostalCode',
            $address->postal_code
        );
        $addressNode->setAttribute('Town', $address->town);
        $addressNode->setAttribute('Address', $address->address);
        $root->appendChild($addressNode);

        $this->appendTextNode(
            $document,
            $root,
            'WebAddress',
            $facility->web_address
        );
        $this->appendTextNode(
            $document,
            $root,
            'Email',
            $facility->email
        );
        $this->appendTextNode(
            $document,
            $root,
            'Phone',
            $facility->phone
        );
        $this->appendTextNode(
            $document,
            $root,
            'Fax',
            $facility->fax
        );

        $document->appendChild($root);
        $xml = $document->saveXML();

        if ($validate) {
            $this->validator->assertValid($xml, 'scenarios.xsd');
        }

        return $xml;
    }

    private function assertFacility(Facility $facility): void
    {
        foreach ([
            'status',
            'local_name',
            'local_language',
            'local_country',
        ] as $field) {
            if ($facility->{$field} === null
                || $facility->{$field} === '') {
                throw new RuntimeException(
                    "FacilityLocal cannot be exported: {$field} is required."
                );
            }
        }

        $this->catalog->assertEnum(
            'SimpleStatusType',
            $facility->status
        );
        $this->catalog->assertEnum(
            'ISO639-2Type',
            $facility->local_language
        );
        $this->catalog->assertEnum(
            'ISO3166CountryCode',
            $facility->local_country
        );

        foreach ([
            'facility_fifa_id',
            'organisation_fifa_id',
            'parent_facility_fifa_id',
        ] as $field) {
            if ($facility->{$field}) {
                $this->catalog->assertFifaIdentifier(
                    $facility->{$field}
                );
            }
        }
    }

    private function appendTextNode(
        DOMDocument $document,
        DOMElement $parent,
        string $name,
        ?string $value
    ): void {
        if ($value !== null && $value !== '') {
            $parent->appendChild(
                $document->createElementNS(
                    self::NS,
                    $name,
                    htmlspecialchars($value, ENT_XML1)
                )
            );
        }
    }

    private function setOptional(
        DOMElement $element,
        string $name,
        mixed $value
    ): void {
        if ($value !== null && $value !== '') {
            $element->setAttribute($name, (string) $value);
        }
    }
}
