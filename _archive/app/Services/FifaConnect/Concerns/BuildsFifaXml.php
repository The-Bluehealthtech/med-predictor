<?php

namespace App\Services\FifaConnect\Concerns;

use App\Models\FifaConnect\MatchEvent;
use App\Models\FifaConnect\Picture;
use DOMDocument;
use DOMElement;
use RuntimeException;

trait BuildsFifaXml
{
    protected function setOptional(
        DOMElement $element,
        string $name,
        mixed $value
    ): void {
        if ($value !== null && $value !== '') {
            $element->setAttribute($name, (string) $value);
        }
    }

    protected function setOptionalFifaId(
        DOMElement $element,
        string $name,
        ?string $value
    ): void {
        if ($value !== null && $value !== '') {
            $this->catalog->assertFifaIdentifier($value);
            $element->setAttribute($name, $value);
        }
    }

    protected function appendPicture(
        DOMDocument $document,
        DOMElement $parent,
        ?Picture $picture,
        string $wrapperName
    ): void {
        if (!$picture) {
            return;
        }

        $wrapper = $document->createElementNS(
            'http://fifa.com/fc',
            $wrapperName
        );

        if ($picture->storage_mode === 'embedded') {
            if (!$picture->embedded_base64) {
                throw new RuntimeException(
                    "{$wrapperName} embedded picture requires content."
                );
            }

            $child = $document->createElementNS(
                'http://fifa.com/fc',
                'PictureEmbedded'
            );
            $child->appendChild(
                $document->createTextNode(
                    $picture->embedded_base64
                )
            );

            if ($picture->mime_type) {
                $child->setAttributeNS(
                    'http://www.w3.org/2005/05/xmlmime',
                    'xmime:contentType',
                    $picture->mime_type
                );
            }

            $wrapper->appendChild($child);
        } elseif ($picture->storage_mode === 'link') {
            if (!$picture->picture_link || !$picture->mime_type) {
                throw new RuntimeException(
                    "{$wrapperName} linked picture requires link and mime type."
                );
            }

            $child = $document->createElementNS(
                'http://fifa.com/fc',
                'PictureLink'
            );
            $child->setAttribute(
                'PictureLink',
                $picture->picture_link
            );
            $child->setAttribute(
                'MimeType',
                $picture->mime_type
            );
            $wrapper->appendChild($child);
        } else {
            throw new RuntimeException(
                "Unsupported FIFA picture storage mode: {$picture->storage_mode}."
            );
        }

        $parent->appendChild($wrapper);
    }

    protected function boolValue(bool $value): string
    {
        return $value ? 'true' : 'false';
    }

    protected function dateTimeValue($value): string
    {
        return $value->format('Y-m-d\TH:i:sP');
    }

    protected function matchEventNode(
        DOMDocument $document,
        object $event,
        bool $detail = false,
        string $elementName = 'MatchEvent'
    ): DOMElement {
        foreach ([
            'match_phase',
            'minute',
            'event_type',
        ] as $field) {
            if ($event->{$field} === null || $event->{$field} === '') {
                throw new RuntimeException(
                    "MatchEvent requires {$field}."
                );
            }
        }

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

        if ($detail && !$event->event_id) {
            throw new RuntimeException(
                'Detailed MatchEvent requires EventId.'
            );
        }

        $node = $document->createElementNS(
            'http://fifa.com/fc',
            $elementName
        );

        $node->setAttribute('MatchPhase', $event->match_phase);
        $node->setAttribute('Minute', (string) $event->minute);
        $this->setOptional(
            $node,
            'StoppageTime',
            $event->stoppage_time
        );
        $node->setAttribute('EventType', $event->event_type);
        $this->setOptional(
            $node,
            'EventDetailType',
            $event->event_detail_type
        );
        $this->setOptionalFifaId(
            $node,
            'PlayerFIFAId',
            $event->player_fifa_id
        );
        $this->setOptionalFifaId(
            $node,
            'TeamOfficialFIFAId',
            $event->team_official_fifa_id
        );
        $this->setOptionalFifaId(
            $node,
            'PlayerFIFAId2',
            $event->player_fifa_id_2
        );
        $this->setOptional(
            $node,
            'MatchTeam',
            $event->match_team
        );

        if ($detail) {
            $node->setAttribute('EventId', $event->event_id);
            $this->setOptional(
                $node,
                'PlayerShirtNumber',
                $event->player_shirt_number
            );
            $this->setOptional(
                $node,
                'PlayerShirtNumber2',
                $event->player_shirt_number_2
            );
        }

        return $node;
    }
}
