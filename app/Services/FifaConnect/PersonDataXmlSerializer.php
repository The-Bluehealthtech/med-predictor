<?php

namespace App\Services\FifaConnect;

use App\Models\FifaConnect\Person;
use Carbon\CarbonInterface;
use DOMDocument;
use Illuminate\Support\Collection;
use RuntimeException;

class PersonDataXmlSerializer
{
    private const NS = 'http://fifa.com/fc';
    private const XSI = 'http://www.w3.org/2001/XMLSchema-instance';

    public function __construct(
        private readonly PersonLocalXmlSerializer $persons,
        private readonly XsdValidator $validator
    ) {
    }

    public function serialize(
        Collection|array $people,
        ?CarbonInterface $exportDate = null,
        bool $validate = true
    ): string {
        $document = new DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = true;
        $root = $document->createElementNS(self::NS, 'PersonData');
        $root->setAttributeNS(self::XSI, 'xsi:schemaLocation',
            self::NS . ' scenarios.xsd');
        $root->setAttribute('ExportDate', ($exportDate ?? now())->format('Y-m-d'));

        foreach ($people as $person) {
            if (!$person instanceof Person) {
                throw new RuntimeException('PersonData accepts canonical Person models only.');
            }
            $source = new DOMDocument();
            if (!$source->loadXML($this->persons->serialize($person, false), LIBXML_NONET)) {
                throw new RuntimeException('Unable to serialize PersonLocal.');
            }
            $node = $document->importNode($source->documentElement, true);
            $node->removeAttributeNS(self::XSI, 'schemaLocation');
            $root->appendChild($node);
        }

        $document->appendChild($root);
        $xml = $document->saveXML();
        if ($validate) {
            $this->validator->assertValid($xml, 'scenarios.xsd');
        }
        return $xml;
    }
}
