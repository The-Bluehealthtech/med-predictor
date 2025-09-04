<?php

namespace App\Exports;

use App\Models\PlayerLicense;
use App\Models\Player;
use App\Models\Club;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Illuminate\Support\Facades\Log;

class LicensesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsErrors
{
    protected $clubId;
    protected $importMode;
    protected $rowCount = 0;
    protected $errors = [];

    public function __construct($clubId, $importMode = 'create')
    {
        $this->clubId = $clubId;
        $this->importMode = $importMode;
    }

    public function model(array $row)
    {
        $this->rowCount++;

        // Find player by name and club
        $player = Player::where('name', $row['player_name'])
            ->where('club_id', $this->clubId)
            ->first();

        if (!$player) {
            throw new \Exception("Player '{$row['player_name']}' not found in club");
        }

        // Check if license already exists
        $existingLicense = PlayerLicense::where('player_id', $player->id)
            ->where('license_type', strtolower($row['license_type']))
            ->first();

        if ($existingLicense && $this->importMode === 'create') {
            throw new \Exception("License already exists for player '{$row['player_name']}'");
        }

        $licenseData = [
            'player_id' => $player->id,
            'club_id' => $this->clubId,
            'license_type' => strtolower($row['license_type']),
            'status' => strtolower($row['status'] ?? 'pending'),
            'contract_start_date' => $row['contract_start_date'] ?? null,
            'contract_end_date' => $row['contract_end_date'] ?? null,
            'expiry_date' => $row['expiry_date'] ?? null,
            'requested_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now()
        ];

        if ($existingLicense && $this->importMode === 'update') {
            $existingLicense->update($licenseData);
            return $existingLicense;
        }

        return new PlayerLicense($licenseData);
    }

    public function rules(): array
    {
        return [
            'player_name' => 'required|string|max:255',
            'license_type' => 'required|in:professional,amateur,youth,temporary,international',
            'status' => 'nullable|in:pending,active,expired,rejected,suspended',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after:contract_start_date',
            'expiry_date' => 'nullable|date'
        ];
    }

    public function customValidationMessages()
    {
        return [
            'player_name.required' => 'Player name is required',
            'license_type.required' => 'License type is required',
            'license_type.in' => 'License type must be one of: professional, amateur, youth, temporary, international',
            'status.in' => 'Status must be one of: pending, active, expired, rejected, suspended',
            'contract_end_date.after' => 'Contract end date must be after contract start date'
        ];
    }

    public function onError(\Throwable $e)
    {
        $this->errors[] = "Row {$this->rowCount}: " . $e->getMessage();
        Log::error("License import error on row {$this->rowCount}: " . $e->getMessage());
    }

    public function getRowCount()
    {
        return $this->rowCount;
    }

    public function getErrors()
    {
        return $this->errors;
    }
} 