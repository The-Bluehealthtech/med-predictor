<?php

namespace App\Services\FifaConnect;

use App\Models\FifaConnect\Organisation;
use DOMDocument;
use DOMElement;
use RuntimeException;

class OrganisationLocalXmlSerializer
{
    private const NS = 'http://fifa.com/fc';
    private const XSI = 'http://www.w3.org/2001/XMLSchema-instance';

    public function __construct(
        private readonly SchemaCatalog $catalog,
        private readonly XsdValidator $validator
    ) {
    }

    public function serialize(
        Organisation $organisation,
        bool $validate = true
    ): string {
        $organisation->loadMissing([
            'localNames',
            'nationalIdentifiers',
            'supportedDisciplines',
            'addresses',
        ]);

        $this->assertOrganisation($organisation);

        if ($organisation->addresses->count() !== 1) {
            throw new RuntimeException(
                'OrganisationLocal requires exactly one OfficialAddress.'
            );
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = true;

        $root = $document->createElementNS(self::NS, 'OrganisationLocal');
        $root->setAttributeNS(
            self::XSI,
            'xsi:schemaLocation',
            self::NS . ' scenarios.xsd'
        );

        $this->setOptional(
            $root,
            'OrganisationFIFAId',
            $organisation->organisation_fifa_id
        );
        $root->setAttribute('Status', $organisation->status);
        $root->setAttribute('LocalName', $organisation->local_name);
        $this->setOptional(
            $root,
            'LocalShortName',
            $organisation->local_short_name
        );
        $this->setOptional(
            $root,
            'LocalSystemMAId',
            $organisation->local_system_ma_id
        );
        $root->setAttribute(
            'LocalLanguage',
            $organisation->local_language
        );
        $root->setAttribute(
            'LocalCountry',
            $organisation->local_country
        );
        $this->setOptional(
            $root,
            'InternationalName',
            $organisation->international_name
        );
        $this->setOptional(
            $root,
            'InternationalShortName',
            $organisation->international_short_name
        );
        $root->setAttribute(
            'OrganisationNature',
            $organisation->organisation_nature
        );
        $this->setOptional(
            $root,
            'FoundationDate',
            $organisation->foundation_date?->format('Y-m-d')
        );
        $this->setOptional(
            $root,
            'DissolutionDate',
            $organisation->dissolution_date?->format('Y-m-d')
        );
        $this->setOptional(
            $root,
            'ParentOrganisationFIFAId',
            $organisation->parent_organisation_fifa_id
        );

        foreach ($organisation->nationalIdentifiers as $identifier) {
            $this->catalog->assertEnum(
                'NationalIdentifierNatureType',
                $identifier->nature
            );
            $this->catalog->assertEnum(
                'ISO3166CountryCode',
                $identifier->country
            );

            $node = $document->createElementNS(
                self::NS,
                'NationalIdentifier'
            );
            $node->setAttribute('Identifier', $identifier->identifier);
            $node->setAttribute(
                'NationalIdentifierNature',
                $identifier->nature
            );
            $node->setAttribute('Country', $identifier->country);
            $this->setOptional(
                $node,
                'DateFrom',
                $identifier->date_from?->format('Y-m-d')
            );
            $this->setOptional(
                $node,
                'DateTo',
                $identifier->date_to?->format('Y-m-d')
            );
            $this->setOptional(
                $node,
                'Description',
                $identifier->description
            );
            $root->appendChild($node);
        }

        foreach ($organisation->localNames as $name) {
            $this->catalog->assertEnum(
                'ISO639-2Type',
                $name->language
            );

            $node = $document->createElementNS(
                self::NS,
                'LocalOrganisationName'
            );
            $node->setAttribute('Language', $name->language);
            $node->setAttribute('Name', $name->name);
            $this->setOptional($node, 'ShortName', $name->short_name);
            $root->appendChild($node);
        }

        foreach ($organisation->supportedDisciplines as $supported) {
            $node = $document->createElementNS(
                self::NS,
                'SupportedDiscipline'
            );

            if ($supported->discipline !== null) {
                $this->catalog->assertEnum(
                    'DisciplineType',
                    $supported->discipline
                );
                $node->setAttribute(
                    'Discipline',
                    $supported->discipline
                );
            }

            if ($supported->gender !== null) {
                $this->catalog->assertEnum(
                    'GenderType',
                    $supported->gender
                );
                $node->setAttribute('Gender', $supported->gender);
            }

            $root->appendChild($node);
        }

        $root->appendChild(
            $this->addressNode(
                $document,
                $organisation->addresses->first()
            )
        );

        $this->appendTextNode(
            $document,
            $root,
            'WebAddress',
            $organisation->web_address
        );
        $this->appendTextNode(
            $document,
            $root,
            'Email',
            $organisation->email
        );
        $this->appendTextNode(
            $document,
            $root,
            'Phone',
            $organisation->phone
        );
        $this->appendTextNode(
            $document,
            $root,
            'Fax',
            $organisation->fax
        );

        PictureXml::append(
            $document,
            $root,
            $organisation->picture,
            'Logo'
        );

        $document->appendChild($root);
        $xml = $document->saveXML();

        if ($validate) {
            $this->validator->assertValid($xml, 'scenarios.xsd');
        }

        return $xml;
    }

    private function assertOrganisation(Organisation $organisation): void
    {
        foreach ([
            'status',
            'local_name',
            'local_language',
            'local_country',
            'organisation_nature',
        ] as $field) {
            if ($organisation->{$field} === null
                || $organisation->{$field} === '') {
                throw new RuntimeException(
                    "OrganisationLocal cannot be exported: {$field} is required."
                );
            }
        }

        $this->catalog->assertEnum(
            'SimpleStatusType',
            $organisation->status
        );
        $this->catalog->assertEnum(
            'OrganisationNatureType',
            $organisation->organisation_nature
        );
        $this->catalog->assertEnum(
            'ISO639-2Type',
            $organisation->local_language
        );
        $this->catalog->assertEnum(
            'ISO3166CountryCode',
            $organisation->local_country
        );

        if ($organisation->organisation_fifa_id) {
            $this->catalog->assertFifaIdentifier(
                $organisation->organisation_fifa_id
            );
        }

        if ($organisation->parent_organisation_fifa_id) {
            $this->catalog->assertFifaIdentifier(
                $organisation->parent_organisation_fifa_id
            );
        }
    }

    private function addressNode(
        DOMDocument $document,
        $address
    ): DOMElement {
        foreach (['country', 'town', 'address'] as $field) {
            if ($address->{$field} === null || $address->{$field} === '') {
                throw new RuntimeException(
                    "OfficialAddress requires {$field}."
                );
            }
        }

        $this->catalog->assertEnum(
            'ISO3166CountryCode',
            $address->country
        );

        $node = $document->createElementNS(
            self::NS,
            'OfficialAddress'
        );
        $node->setAttribute('Country', $address->country);
        $this->setOptional($node, 'Region', $address->region);
        $this->setOptional(
            $node,
            'PostalCode',
            $address->postal_code
        );
        $node->setAttribute('Town', $address->town);
        $node->setAttribute('Address', $address->address);

        return $node;
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
