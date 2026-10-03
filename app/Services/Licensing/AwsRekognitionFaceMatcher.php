<?php

namespace App\Services\Licensing;

use Aws\Rekognition\RekognitionClient;

final class AwsRekognitionFaceMatcher
{
    public function isConfigured(): bool
    {
        return class_exists(RekognitionClient::class)
            && filled(config('services.aws_rekognition.region'))
            && filled(config('services.aws_rekognition.key'))
            && filled(config('services.aws_rekognition.secret'));
    }

    public function status(): array
    {
        return [
            'status' => $this->isConfigured() ? 'ready' : 'not_configured',
            'label' => $this->isConfigured()
                ? 'AWS Rekognition CompareFaces configuré'
                : 'AWS Rekognition CompareFaces non connecté',
            'provider' => 'aws_rekognition',
            'region' => config('services.aws_rekognition.region'),
            'threshold' => (float) config('services.aws_rekognition.similarity_threshold', 90),
        ];
    }

    public function compare(string $sourceBytes, string $targetBytes): array
    {
        if (!$this->isConfigured()) {
            return ['status' => 'not_configured'] + $this->status();
        }

        $threshold = (float) config('services.aws_rekognition.similarity_threshold', 90);
        $credentials = [
            'key' => (string) config('services.aws_rekognition.key'),
            'secret' => (string) config('services.aws_rekognition.secret'),
        ];
        if (filled(config('services.aws_rekognition.token'))) {
            $credentials['token'] = (string) config('services.aws_rekognition.token');
        }
        $client = new RekognitionClient([
            'version' => 'latest',
            'region' => (string) config('services.aws_rekognition.region'),
            'credentials' => $credentials,
        ]);

        try {
            $result = $client->compareFaces([
                'SourceImage' => ['Bytes' => $sourceBytes],
                'TargetImage' => ['Bytes' => $targetBytes],
                'SimilarityThreshold' => 0,
                'QualityFilter' => (string) config('services.aws_rekognition.quality_filter', 'AUTO'),
            ]);
        } catch (\Throwable $e) {
            report($e);

            return [
                'status' => 'error',
                'provider' => 'aws_rekognition',
                'threshold' => $threshold,
                'message' => 'Comparaison AWS Rekognition indisponible.',
            ];
        }

        $matches = $result->get('FaceMatches') ?: [];
        $best = collect($matches)->sortByDesc(fn ($match) => (float) ($match['Similarity'] ?? 0))->first();

        return [
            'status' => 'completed',
            'provider' => 'aws_rekognition',
            'score' => $best ? (float) ($best['Similarity'] ?? 0) : 0.0,
            'threshold' => $threshold,
            'matched_above_threshold' => $best !== null && (float) ($best['Similarity'] ?? 0) >= $threshold,
            'source_face_confidence' => data_get($result->get('SourceImageFace'), 'Confidence'),
            'target_faces_unmatched' => count($result->get('UnmatchedFaces') ?: []),
            'request_id' => $result->get('@metadata')['headers']['x-amzn-requestid'] ?? null,
            'model_version' => null,
            'checked_at' => now()->toIso8601String(),
        ];
    }
}
