<?php

namespace App\Services\FifaConnect;

use App\Models\FifaConnect\MandatoryData;
use App\Models\FifaConnect\MandatoryPart;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class MandatoryDataService
{
    public function persist(array $definition, string $sourceReference): MandatoryData
    {
        $service = $definition['service_name'] ?? null;
        $parameter = $definition['parameter_name'] ?? null;
        $parts = $definition['parts'] ?? null;
        if (array_diff(array_keys($definition), ['service_name', 'parameter_name', 'parts'])
            || !is_string($service) || trim($service) === ''
            || !is_string($parameter) || trim($parameter) === ''
            || !is_string($sourceReference) || trim($sourceReference) === ''
            || !is_array($parts) || !array_is_list($parts)
            || count($parts) < 1 || count($parts) > 500) {
            throw new InvalidArgumentException('Invalid MandatoryData definition or source.');
        }
        $parts = array_map(fn ($part) => $this->normalizePart($part), $parts);
        return DB::transaction(function () use ($service, $parameter, $parts, $sourceReference) {
            $data = MandatoryData::query()->updateOrCreate(
                ['service_name' => $service, 'parameter_name' => $parameter],
                [
                    'source_reference' => $sourceReference,
                    'payload_hash' => hash('sha256', json_encode(
                        [$service, $parameter, $parts], JSON_THROW_ON_ERROR
                    )),
                    'received_at' => now(),
                ]
            );
            MandatoryPart::query()->where('mandatory_data_id', $data->id)->delete();
            foreach ($parts as $index => $part) {
                $this->savePart($data->id, $part, $index + 1);
            }
            return $data->refresh();
        });
    }

    private function normalizePart(mixed $part): array
    {
        if (!is_array($part)
            || array_diff(array_keys($part), ['path', 'is_attribute', 'sub_path'])
            || !is_string($part['path'] ?? null)
            || trim($part['path']) === ''
            || !is_bool($part['is_attribute'] ?? null)
            || (array_key_exists('sub_path', $part)
                && $part['sub_path'] !== null && !is_array($part['sub_path']))) {
            throw new InvalidArgumentException('Invalid MandatoryPart.');
        }
        return [
            'path' => $part['path'],
            'is_attribute' => $part['is_attribute'],
            'sub_path' => isset($part['sub_path'])
                ? $this->normalizePart($part['sub_path']) : null,
        ];
    }

    private function savePart(
        int $dataId, array $part, int $order, ?int $parentId = null
    ): void {
        $model = MandatoryPart::query()->create([
            'mandatory_data_id' => $dataId,
            'parent_part_id' => $parentId,
            'order_number' => $order,
            'path' => $part['path'],
            'is_attribute' => $part['is_attribute'],
        ]);
        if ($part['sub_path'] !== null) {
            $this->savePart($dataId, $part['sub_path'], 1, $model->id);
        }
    }
}
