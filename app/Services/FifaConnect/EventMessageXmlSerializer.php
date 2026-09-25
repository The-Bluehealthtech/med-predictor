<?php

namespace App\Services\FifaConnect;

use App\Models\FifaConnect\MatchEvent;
use Carbon\CarbonInterface;
use DOMDocument;
use DOMElement;
use RuntimeException;

class EventMessageXmlSerializer
{
    private const NS = 'http://fifa.com/fc';
    private const XSI = 'http://www.w3.org/2001/XMLSchema-instance';

    public function __construct(
        private readonly SchemaCatalog $catalog,
        private readonly XsdValidator $validator
    ) {
    }

    public function serialize(
        MatchEvent $event,
        string $messageNature,
        string $matchFifaId,
        ?CarbonInterface $exportDate = null,
        array $scores = [],
        bool $validate = true
    ): string {
        $this->assertEvent(
            $event,
            $messageNature,
            $matchFifaId,
            $scores
        );

        $document = new DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = true;

        $root = $document->createElementNS(
            self::NS,
            'EventMessage'
        );
        $root->setAttributeNS(
            self::XSI,
            'xsi:schemaLocation',
            self::NS . ' scenarios.xsd'
        );

        $root->setAttribute(
            'MatchPhase',
            $event->match_phase
        );
        $root->setAttribute(
            'Minute',
            (string) $event->minute
        );
        $this->setOptional(
            $root,
            'StoppageTime',
            $event->stoppage_time
        );
        $root->setAttribute(
            'EventType',
            $event->event_type
        );
        $this->setOptional(
            $root,
            'EventDetailType',
            $event->event_detail_type
        );
        $this->setOptional(
            $root,
            'PlayerFIFAId',
            $event->player_fifa_id
        );
        $this->setOptional(
            $root,
            'TeamOfficialFIFAId',
            $event->team_official_fifa_id
        );
        $this->setOptional(
            $root,
            'PlayerFIFAId2',
            $event->player_fifa_id_2
        );
        $this->setOptional(
            $root,
            'MatchTeam',
            $event->match_team
        );
        $root->setAttribute('EventId', $event->event_id);
        $this->setOptional(
            $root,
            'PlayerShirtNumber',
            $event->player_shirt_number
        );
        $this->setOptional(
            $root,
            'PlayerShirtNumber2',
            $event->player_shirt_number_2
        );
        $root->setAttribute(
            'MessageNature',
            $messageNature
        );
        $root->setAttribute(
            'MatchFIFAId',
            $matchFifaId
        );

        // scenarios.xsd declares ExportDateTime as fc:Date.
        $root->setAttribute(
            'ExportDateTime',
            ($exportDate ?? now())->format('Y-m-d')
        );

        foreach ($scores as $score) {
            $scoreNode = $document->createElementNS(
                self::NS,
                'Score'
            );

            if (is_array($score) && isset($score['xml_fragment'])) {
                $document->formatOutput = false;
                $fragment = new DOMDocument();
                $wrapped = '<Root xmlns="' . self::NS . '">'
                    . $score['xml_fragment'] . '</Root>';
                if (!$fragment->loadXML($wrapped, LIBXML_NONET)
                    || $fragment->documentElement?->childElementCount !== 1
                    || $fragment->documentElement->firstElementChild?->localName !== 'Score'
                    || $fragment->documentElement->firstElementChild?->namespaceURI !== self::NS) {
                    throw new RuntimeException('Invalid EventMessage Score XML fragment.');
                }
                $root->appendChild($document->importNode($fragment->documentElement->firstElementChild, true));
                continue;
            }

            if (is_array($score)) {
                foreach ($score as $key => $value) {
                    if (!is_scalar($value) && $value !== null) {
                        throw new RuntimeException(
                            'EventMessage Score values must be scalar.'
                        );
                    }

                    if ($value !== null) {
                        $scoreNode->setAttribute(
                            (string) $key,
                            (string) $value
                        );
                    }
                }
            } elseif (is_scalar($score)) {
                $scoreNode->nodeValue = (string) $score;
            } else {
                throw new RuntimeException(
                    'EventMessage Score must be scalar or an attribute map.'
                );
            }

            $root->appendChild($scoreNode);
        }

        $document->appendChild($root);
        $xml = $document->saveXML();

        if ($validate) {
            $this->validator->assertValid(
                $xml,
                'scenarios.xsd'
            );
        }

        return $xml;
    }

    public function serializeModel(
        \App\Models\FifaConnect\EventMessage $message,
        bool $validate = true
    ): string {
        $message->loadMissing('scores');

        $event = new MatchEvent([
            'event_id' => $message->event_id,
            'match_phase' => $message->match_phase,
            'minute' => $message->minute,
            'stoppage_time' => $message->stoppage_time,
            'event_type' => $message->event_type,
            'event_detail_type' => $message->event_detail_type,
            'player_fifa_id' => $message->player_fifa_id,
            'team_official_fifa_id' => $message->team_official_fifa_id,
            'player_fifa_id_2' => $message->player_fifa_id_2,
            'match_team' => $message->match_team,
            'player_shirt_number' => $message->player_shirt_number,
            'player_shirt_number_2' => $message->player_shirt_number_2,
        ]);

        return $this->serialize(
            $event,
            $message->message_nature,
            $message->match_fifa_id,
            $message->export_date_time,
            $message->scores->map(fn ($score) => $score->xml_fragment
                ? ['xml_fragment' => $score->xml_fragment]
                : $score->value)->all(),
            $validate
        );
    }

    private function assertEvent(
        MatchEvent $event,
        string $messageNature,
        string $matchFifaId,
        array $scores
    ): void {
        foreach ([
            'match_phase',
            'minute',
            'event_type',
            'event_id',
        ] as $field) {
            if ($event->{$field} === null || $event->{$field} === '') {
                throw new RuntimeException(
                    "EventMessage cannot be exported: {$field} is required."
                );
            }
        }

        if (count($scores) < 1 || count($scores) > 2) {
            throw new RuntimeException(
                'EventMessage requires one or two Score elements.'
            );
        }

        $this->catalog->assertEnum(
            'MessageNatureType',
            $messageNature
        );
        $this->catalog->assertFifaIdentifier(
            $matchFifaId
        );
        $this->catalog->assertEnum(
            'MatchPhaseNatureType',
            $event->match_phase
        );
        $this->catalog->assertEnum(
            'MatchEventNatureType',
            $event->event_type
        );

        if ($event->event_detail_type !== null) {
            $this->catalog->assertEnum(
                'MatchEventDetailNatureType',
                $event->event_detail_type
            );
        }

        if ($event->match_team !== null) {
            $this->catalog->assertEnum(
                'MatchTeamNatureType',
                $event->match_team
            );
        }

        foreach ([
            $event->player_fifa_id,
            $event->team_official_fifa_id,
            $event->player_fifa_id_2,
        ] as $id) {
            if ($id) {
                $this->catalog->assertFifaIdentifier($id);
            }
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
