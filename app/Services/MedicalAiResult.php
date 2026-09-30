<?php
namespace App\Services;
final class MedicalAiResult
{
    public function normalize(array $result): array
    {
        $this->rejectSimulated($result);
        if (($result['success'] ?? null) !== true) throw new \RuntimeException('AI result unavailable');
        $data = $result['analysis'] ?? null;
        if (is_array($data) && isset($data['text'])) {
            $this->rejectSimulated($data);
            $text = preg_replace('/^\x60\x60\x60(?:json)?\s*|\s*\x60\x60\x60$/i', '', trim($data['text']));
            $data = json_decode($text, true);
        }
        if (!is_array($data) || !$data || array_is_list($data)) {
            throw new \RuntimeException('Invalid structured AI result');
        }
        $this->rejectSimulated($data);
        return ['success' => true, 'analysis' => $data, 'requires_medical_review' => true];
    }
    public function rejectSimulated(array $data): void
    {
        if (!empty($data['mockMode']) || !empty($data['mock_mode']) || !empty($data['fallback'])
            || isset($data['success']) && $data['success'] !== true
            || preg_match('/fallback/i', $data['note'] ?? '')) {
            throw new \RuntimeException('Simulated or failed AI result rejected');
        }
        foreach ($data as $value) if (is_array($value)) $this->rejectSimulated($value);
    }
}
