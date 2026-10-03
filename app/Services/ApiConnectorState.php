<?php

namespace App\Services;

use App\Models\SystemSetting;

final class ApiConnectorState
{
    public function enabled(string $slug, bool $default = false): bool
    {
        return (bool) SystemSetting::get($this->key($slug), $default);
    }

    public function setEnabled(string $slug, bool $enabled, ?int $userId = null): void
    {
        $key = $this->key($slug);
        $setting = SystemSetting::query()->firstOrCreate(
            ['key' => $key],
            [
                'name' => 'API connector ' . $slug,
                'description' => 'État d’activation administré depuis /modules/api-connectors.',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'api_connectors',
                'is_public' => false,
                'is_editable' => true,
                'is_required' => false,
                'default_value' => '0',
            ]
        );
        $setting->update(['value' => $enabled ? '1' : '0', 'updated_by' => $userId]);
        \Illuminate\Support\Facades\Cache::forget('system_setting_' . $key);
    }

    private function key(string $slug): string
    {
        return 'api_connector.' . $slug . '.enabled';
    }
}
