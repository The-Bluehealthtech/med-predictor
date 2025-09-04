<?php

namespace App\Exports;

use App\Models\PlayerLicense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LicensesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $clubId;

    public function __construct($clubId = null)
    {
        $this->clubId = $clubId;
    }

    public function collection()
    {
        $query = PlayerLicense::with(['player', 'club', 'approvedByUser', 'requestedByUser']);
        
        if ($this->clubId) {
            $query->where('club_id', $this->clubId);
        }
        
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'License ID',
            'Player Name',
            'Club',
            'License Type',
            'Status',
            'Contract Start Date',
            'Contract End Date',
            'Expiry Date',
            'Requested By',
            'Approved By',
            'Created Date',
            'Last Updated'
        ];
    }

    public function map($license): array
    {
        return [
            $license->id,
            $license->player ? $license->player->name : 'N/A',
            $license->club ? $license->club->name : 'N/A',
            ucfirst($license->license_type ?? 'N/A'),
            ucfirst($license->status ?? 'N/A'),
            $license->contract_start_date ? date('Y-m-d', strtotime($license->contract_start_date)) : 'N/A',
            $license->contract_end_date ? date('Y-m-d', strtotime($license->contract_end_date)) : 'N/A',
            $license->expiry_date ? date('Y-m-d', strtotime($license->expiry_date)) : 'N/A',
            $license->requestedByUser ? $license->requestedByUser->name : 'N/A',
            $license->approvedByUser ? $license->approvedByUser->name : 'N/A',
            $license->created_at ? $license->created_at->format('Y-m-d H:i:s') : 'N/A',
            $license->updated_at ? $license->updated_at->format('Y-m-d H:i:s') : 'N/A'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ]
            ]
        ];
    }
} 