<?php

namespace App\Services\FifaConnect;

use App\Models\FifaConnect\Picture;
use DOMDocument;
use DOMElement;
use RuntimeException;

class PictureXml
{
    public static function append(
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
}
