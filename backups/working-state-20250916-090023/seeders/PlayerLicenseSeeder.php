<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlayerLicense;
use App\Models\Player;
use App\Models\Club;
use App\Models\User;
use Carbon\Carbon;

class PlayerLicenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clubs = Club::take(5)->get();
        $players = Player::take(20)->get();
        $users = User::whereIn('role', ['club_admin', 'association_admin', 'system_admin'])->take(5)->get();
        
        if ($clubs->isEmpty() || $players->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Skipping PlayerLicenseSeeder: Not enough clubs, players, or users found.');
            return;
        }

        $licenseTypes = ['PROF', 'AMAT', 'YOUTH', 'INTL', 'TEMP'];
        
        // Clear existing licenses first
        PlayerLicense::truncate();
        
        $licenseData = [];
        
        // Create exactly 22 pending licenses
        for ($i = 0; $i < 22; $i++) {
            $player = $players[$i % $players->count()];
            $club = $clubs->random();
            $licenseType = $licenseTypes[array_rand($licenseTypes)];
            $requestedBy = $users->random();
            
            // Set player club if not already set
            if (!$player->club_id) {
                $player->update(['club_id' => $club->id]);
            }

            $licenseData[] = [
                'player_id' => $player->id,
                'club_id' => $club->id,
                'fifa_connect_id' => $player->fifa_connect_id,
                'license_number' => $this->generateLicenseNumber($club, $licenseType, $i + 1),
                'license_type' => $licenseType,
                'status' => 'pending',
                'issue_date' => null,
                'expiry_date' => null,
                'renewal_date' => null,
                'issuing_authority' => 'FA',
                'license_category' => $this->getLicenseCategory($licenseType),
                'registration_number' => 'REG-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'transfer_status' => 'registered',
                'contract_type' => $this->getContractType($licenseType),
                'contract_start_date' => null,
                'contract_end_date' => null,
                'wage_agreement' => null,
                'bonus_structure' => null,
                'release_clause' => null,
                'medical_clearance' => null,
                'fitness_certificate' => null,
                'disciplinary_record' => null,
                'international_clearance' => null,
                'work_permit' => null,
                'visa_status' => null,
                'documentation_status' => 'incomplete',
                'approval_status' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
                'rejection_reason' => null,
                'notes' => 'License application pending review',
                'requested_by' => $requestedBy->id,
                'document_path' => $this->getDocumentPath($player->id, $licenseType),
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
                'updated_at' => Carbon::now()->subDays(rand(0, 7)),
            ];
        }
        
        // Create exactly 2 rejected licenses
        for ($i = 0; $i < 2; $i++) {
            $player = $players[($i + 22) % $players->count()];
            $club = $clubs->random();
            $licenseType = $licenseTypes[array_rand($licenseTypes)];
            $requestedBy = $users->random();
            $approvedBy = $users->random();
            
            // Set player club if not already set
            if (!$player->club_id) {
                $player->update(['club_id' => $club->id]);
            }

            $licenseData[] = [
                'player_id' => $player->id,
                'club_id' => $club->id,
                'fifa_connect_id' => $player->fifa_connect_id,
                'license_number' => $this->generateLicenseNumber($club, $licenseType, $i + 23),
                'license_type' => $licenseType,
                'status' => 'revoked',
                'issue_date' => null,
                'expiry_date' => null,
                'renewal_date' => null,
                'issuing_authority' => 'FA',
                'license_category' => $this->getLicenseCategory($licenseType),
                'registration_number' => 'REG-' . str_pad($i + 23, 6, '0', STR_PAD_LEFT),
                'transfer_status' => 'registered',
                'contract_type' => $this->getContractType($licenseType),
                'contract_start_date' => null,
                'contract_end_date' => null,
                'wage_agreement' => null,
                'bonus_structure' => null,
                'release_clause' => null,
                'medical_clearance' => null,
                'fitness_certificate' => null,
                'disciplinary_record' => null,
                'international_clearance' => null,
                'work_permit' => null,
                'visa_status' => null,
                'documentation_status' => 'expired',
                'approval_status' => 'rejected',
                'approved_by' => $approvedBy->id,
                'approved_at' => Carbon::now()->subDays(rand(1, 7)),
                'rejection_reason' => $this->getRejectionReason(),
                'notes' => 'License application rejected',
                'requested_by' => $requestedBy->id,
                'document_path' => $this->getDocumentPath($player->id, $licenseType),
                'created_at' => Carbon::now()->subDays(rand(8, 30)),
                'updated_at' => Carbon::now()->subDays(rand(1, 7)),
            ];
        }

        // Insert in chunks to avoid memory issues
        foreach (array_chunk($licenseData, 10) as $chunk) {
            PlayerLicense::insert($chunk);
        }

        $this->command->info('Player licenses seeded successfully!');
        $this->command->info('Created ' . count($licenseData) . ' license records.');
        $this->command->info('License Status: 22 Pending, 0 Active, 2 Rejected');
    }

    private function generateLicenseNumber($club, $licenseType, $sequence): string
    {
        $prefix = strtoupper(substr($club->country ?? 'USA', 0, 3));
        $year = date('Y');
        $sequence = str_pad($sequence, 6, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$licenseType}-{$year}-{$sequence}";
    }

    private function getLicenseCategory($licenseType): string
    {
        $categories = [
            'PROF' => 'A',
            'AMAT' => 'B',
            'YOUTH' => 'C',
            'INTL' => 'A',
            'TEMP' => 'D'
        ];
        
        return $categories[$licenseType] ?? 'E';
    }

    private function getTransferStatus($status): string
    {
        if ($status === 'active') {
            return ['registered', 'pending_transfer'][array_rand(['registered', 'pending_transfer'])];
        }
        return 'registered';
    }

    private function getContractType($licenseType): string
    {
        if (in_array($licenseType, ['PROF', 'INTL'])) {
            return ['permanent', 'loan'][array_rand(['permanent', 'loan'])];
        }
        return 'free_agent';
    }

    private function getDocumentationStatus($status): string
    {
        if ($status === 'pending') {
            return 'incomplete';
        } elseif ($status === 'active') {
            return 'complete';
        } else {
            return 'expired';
        }
    }

    private function getApprovalStatus($status): string
    {
        switch ($status) {
            case 'pending':
                return 'pending';
            case 'active':
                return 'approved';
            case 'revoked':
                return 'rejected';
            default:
                return 'pending';
        }
    }

    private function getRejectionReason(): string
    {
        $reasons = [
            'Incomplete documentation provided',
            'Medical clearance failed',
            'Disciplinary record issues',
            'Contract terms not acceptable',
            'Age requirements not met',
            'Experience requirements not met',
            'International clearance denied',
            'Work permit application rejected'
        ];
        
        return $reasons[array_rand($reasons)];
    }

    private function getLicenseNotes($licenseType, $status): string
    {
        $notes = [];
        
        if ($licenseType === 'PROF') {
            $notes[] = 'Professional player license';
        } elseif ($licenseType === 'YOUTH') {
            $notes[] = 'Youth player license - parental consent required';
        } elseif ($licenseType === 'INTL') {
            $notes[] = 'International player license - work permit required';
        }
        
        if ($status === 'active') {
            $notes[] = 'License is currently active and valid';
        } elseif ($status === 'expired') {
            $notes[] = 'License has expired - renewal required';
        } elseif ($status === 'suspended') {
            $notes[] = 'License temporarily suspended';
        }
        
        return implode('. ', $notes);
    }

    private function getDocumentPath($playerId, $licenseType): string
    {
        return "licenses/{$licenseType}/player_{$playerId}/documents.zip";
    }
} 