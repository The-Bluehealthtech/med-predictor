<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Federation;
use App\Models\Transfer;
use App\Models\Contract;
use App\Models\TransferDocument;
use App\Models\TransferPayment;
use App\Models\Player;
use App\Models\Club;
use App\Models\User;

class TransferSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Transfer System data...');

        // Create Federations
        $this->createFederations();

        // Create Clubs if they don't exist
        $this->createClubs();

        // Create Players if they don't exist
        $this->createPlayers();

        // Create Contracts
        $this->createContracts();

        // Create Transfers
        $this->createTransfers();

        // Create Transfer Documents
        $this->createTransferDocuments();

        // Create Transfer Payments
        $this->createTransferPayments();

        $this->command->info('Transfer System data seeded successfully!');
    }

    private function createFederations(): void
    {
        $federations = [
            [
                'name' => 'Fédération Française de Football',
                'short_name' => 'FFF',
                'country' => 'France',
                'region' => 'Europe',
                'fifa_code' => 'FRA',
                'website' => 'https://www.fff.fr',
                'email' => 'contact@fff.fr',
                'phone' => '+33 1 44 31 73 00',
                'address' => '87 Boulevard de Grenelle, 75738 Paris',
                'status' => 'active',
            ],
            [
                'name' => 'The Football Association',
                'short_name' => 'FA',
                'country' => 'England',
                'region' => 'Europe',
                'fifa_code' => 'ENG',
                'website' => 'https://www.thefa.com',
                'email' => 'info@thefa.com',
                'phone' => '+44 20 7745 4545',
                'address' => 'Wembley Stadium, Wembley, London HA9 0WS',
                'status' => 'active',
            ],
            [
                'name' => 'Deutscher Fußball-Bund',
                'short_name' => 'DFB',
                'country' => 'Germany',
                'region' => 'Europe',
                'fifa_code' => 'GER',
                'website' => 'https://www.dfb.de',
                'email' => 'info@dfb.de',
                'phone' => '+49 69 6788 0',
                'address' => 'Otto-Fleck-Schneise 6, 60528 Frankfurt',
                'status' => 'active',
            ],
            [
                'name' => 'Real Federación Española de Fútbol',
                'short_name' => 'RFEF',
                'country' => 'Spain',
                'region' => 'Europe',
                'fifa_code' => 'ESP',
                'website' => 'https://www.rfef.es',
                'email' => 'info@rfef.es',
                'phone' => '+34 91 495 98 00',
                'address' => 'Ramón y Cajal, s/n, 28232 Las Rozas, Madrid',
                'status' => 'active',
            ],
            [
                'name' => 'Federazione Italiana Giuoco Calcio',
                'short_name' => 'FIGC',
                'country' => 'Italy',
                'region' => 'Europe',
                'fifa_code' => 'ITA',
                'website' => 'https://www.figc.it',
                'email' => 'info@figc.it',
                'phone' => '+39 06 84 91 1',
                'address' => 'Via Gregorio Allegri 14, 00198 Roma',
                'status' => 'active',
            ],
            [
                'name' => 'Confederação Brasileira de Futebol',
                'short_name' => 'CBF',
                'country' => 'Brazil',
                'region' => 'South America',
                'fifa_code' => 'BRA',
                'website' => 'https://www.cbf.com.br',
                'email' => 'cbf@cbf.com.br',
                'phone' => '+55 21 3572 8800',
                'address' => 'Rua Victor Civita 66, Barra da Tijuca, Rio de Janeiro',
                'status' => 'active',
            ],
            [
                'name' => 'Asociación del Fútbol Argentino',
                'short_name' => 'AFA',
                'country' => 'Argentina',
                'region' => 'South America',
                'fifa_code' => 'ARG',
                'website' => 'https://www.afa.com.ar',
                'email' => 'info@afa.com.ar',
                'phone' => '+54 11 4370 7900',
                'address' => 'Viamonte 1366, C1053ABR Buenos Aires',
                'status' => 'active',
            ],
            [
                'name' => 'Fédération Royale Marocaine de Football',
                'short_name' => 'FRMF',
                'country' => 'Morocco',
                'region' => 'Africa',
                'fifa_code' => 'MAR',
                'website' => 'https://www.frmf.ma',
                'email' => 'contact@frmf.ma',
                'phone' => '+212 5 37 77 77 77',
                'address' => 'Complexe Sportif Mohammed V, Rabat',
                'status' => 'active',
            ],
        ];

        foreach ($federations as $federationData) {
            Federation::firstOrCreate(
                ['fifa_code' => $federationData['fifa_code']],
                $federationData
            );
        }

        $this->command->info('Federations created successfully!');
    }

    private function createClubs(): void
    {
        $clubs = [
            [
                'name' => 'Paris Saint-Germain',
                'short_name' => 'PSG',
                'country' => 'France',
                'city' => 'Paris',
                'founded_year' => 1970,
                'stadium' => 'Parc des Princes',
                'stadium_capacity' => 47929,
                'website' => 'https://www.psg.fr',
                'email' => 'contact@psg.fr',
                'phone' => '+33 1 42 12 54 00',
                'address' => '24 Rue du Commandant Guilbaud, 75016 Paris',
                'status' => 'active',
            ],
            [
                'name' => 'Manchester United',
                'short_name' => 'MUFC',
                'country' => 'England',
                'city' => 'Manchester',
                'founded_year' => 1878,
                'stadium' => 'Old Trafford',
                'stadium_capacity' => 74140,
                'website' => 'https://www.manutd.com',
                'email' => 'info@manutd.com',
                'phone' => '+44 161 868 8000',
                'address' => 'Sir Matt Busby Way, Old Trafford, Manchester M16 0RA',
                'status' => 'active',
            ],
            [
                'name' => 'Bayern Munich',
                'short_name' => 'FCB',
                'country' => 'Germany',
                'city' => 'Munich',
                'founded_year' => 1900,
                'stadium' => 'Allianz Arena',
                'stadium_capacity' => 75000,
                'website' => 'https://www.fcbayern.com',
                'email' => 'info@fcbayern.com',
                'phone' => '+49 89 699 31 0',
                'address' => 'Säbener Straße 51, 81547 München',
                'status' => 'active',
            ],
            [
                'name' => 'Real Madrid',
                'short_name' => 'RMA',
                'country' => 'Spain',
                'city' => 'Madrid',
                'founded_year' => 1902,
                'stadium' => 'Santiago Bernabéu',
                'stadium_capacity' => 81044,
                'website' => 'https://www.realmadrid.com',
                'email' => 'info@realmadrid.com',
                'phone' => '+34 91 398 43 00',
                'address' => 'Av. de Concha Espina, 1, 28036 Madrid',
                'status' => 'active',
            ],
            [
                'name' => 'Juventus',
                'short_name' => 'JUV',
                'country' => 'Italy',
                'city' => 'Turin',
                'founded_year' => 1897,
                'stadium' => 'Allianz Stadium',
                'stadium_capacity' => 41507,
                'website' => 'https://www.juventus.com',
                'email' => 'info@juventus.com',
                'phone' => '+39 011 45 30 486',
                'address' => 'Corso Galileo Ferraris 32, 10128 Torino',
                'status' => 'active',
            ],
            [
                'name' => 'Flamengo',
                'short_name' => 'FLA',
                'country' => 'Brazil',
                'city' => 'Rio de Janeiro',
                'founded_year' => 1895,
                'stadium' => 'Maracanã',
                'stadium_capacity' => 78838,
                'website' => 'https://www.flamengo.com.br',
                'email' => 'contato@flamengo.com.br',
                'phone' => '+55 21 2159 1000',
                'address' => 'Av. Borges de Medeiros, 997, Lagoa, Rio de Janeiro',
                'status' => 'active',
            ],
            [
                'name' => 'Boca Juniors',
                'short_name' => 'BOC',
                'country' => 'Argentina',
                'city' => 'Buenos Aires',
                'founded_year' => 1905,
                'stadium' => 'La Bombonera',
                'stadium_capacity' => 54000,
                'website' => 'https://www.bocajuniors.com.ar',
                'email' => 'info@bocajuniors.com.ar',
                'phone' => '+54 11 4309 4700',
                'address' => 'Brandsen 805, C1161AAQ Buenos Aires',
                'status' => 'active',
            ],
            [
                'name' => 'Raja Casablanca',
                'short_name' => 'RCA',
                'country' => 'Morocco',
                'city' => 'Casablanca',
                'founded_year' => 1949,
                'stadium' => 'Stade Mohammed V',
                'stadium_capacity' => 45000,
                'website' => 'https://www.rajacasablanca.com',
                'email' => 'contact@rajacasablanca.com',
                'phone' => '+212 5 22 30 30 30',
                'address' => 'Boulevard Zerktouni, Casablanca',
                'status' => 'active',
            ],
        ];

        foreach ($clubs as $clubData) {
            Club::firstOrCreate(
                ['name' => $clubData['name']],
                $clubData
            );
        }

        $this->command->info('Clubs created successfully!');
    }

    private function createPlayers(): void
    {
        $players = [
            [
                'first_name' => 'Kylian',
                'last_name' => 'Mbappé',
                'date_of_birth' => '1998-12-20',
                'nationality' => 'France',
                'position' => 'Forward',
                'height' => 178,
                'weight' => 73,
                'preferred_foot' => 'Right',
                'jersey_number' => 7,
                'status' => 'active',
                'fifa_connect_id' => 'FIFA-PLAYER-001',
            ],
            [
                'first_name' => 'Marcus',
                'last_name' => 'Rashford',
                'date_of_birth' => '1997-10-31',
                'nationality' => 'England',
                'position' => 'Forward',
                'height' => 180,
                'weight' => 70,
                'preferred_foot' => 'Right',
                'jersey_number' => 10,
                'status' => 'active',
                'fifa_connect_id' => 'FIFA-PLAYER-002',
            ],
            [
                'first_name' => 'Harry',
                'last_name' => 'Kane',
                'date_of_birth' => '1993-07-28',
                'nationality' => 'England',
                'position' => 'Forward',
                'height' => 188,
                'weight' => 89,
                'preferred_foot' => 'Right',
                'jersey_number' => 9,
                'status' => 'active',
                'fifa_connect_id' => 'FIFA-PLAYER-003',
            ],
            [
                'first_name' => 'Erling',
                'last_name' => 'Haaland',
                'date_of_birth' => '2000-07-21',
                'nationality' => 'Norway',
                'position' => 'Forward',
                'height' => 194,
                'weight' => 88,
                'preferred_foot' => 'Left',
                'jersey_number' => 9,
                'status' => 'active',
                'fifa_connect_id' => 'FIFA-PLAYER-004',
            ],
            [
                'first_name' => 'Jude',
                'last_name' => 'Bellingham',
                'date_of_birth' => '2003-06-29',
                'nationality' => 'England',
                'position' => 'Midfielder',
                'height' => 186,
                'weight' => 75,
                'preferred_foot' => 'Right',
                'jersey_number' => 5,
                'status' => 'active',
                'fifa_connect_id' => 'FIFA-PLAYER-005',
            ],
            [
                'first_name' => 'Vinícius',
                'last_name' => 'Júnior',
                'date_of_birth' => '2000-07-12',
                'nationality' => 'Brazil',
                'position' => 'Forward',
                'height' => 176,
                'weight' => 73,
                'preferred_foot' => 'Right',
                'jersey_number' => 7,
                'status' => 'active',
                'fifa_connect_id' => 'FIFA-PLAYER-006',
            ],
            [
                'first_name' => 'Lautaro',
                'last_name' => 'Martínez',
                'date_of_birth' => '1997-08-22',
                'nationality' => 'Argentina',
                'position' => 'Forward',
                'height' => 174,
                'weight' => 72,
                'preferred_foot' => 'Right',
                'jersey_number' => 10,
                'status' => 'active',
                'fifa_connect_id' => 'FIFA-PLAYER-007',
            ],
            [
                'first_name' => 'Achraf',
                'last_name' => 'Hakimi',
                'date_of_birth' => '1998-11-04',
                'nationality' => 'Morocco',
                'position' => 'Defender',
                'height' => 181,
                'weight' => 73,
                'preferred_foot' => 'Right',
                'jersey_number' => 2,
                'status' => 'active',
                'fifa_connect_id' => 'FIFA-PLAYER-008',
            ],
        ];

        foreach ($players as $playerData) {
            Player::updateOrCreate(
                [
                    'fifa_connect_id' => $playerData['fifa_connect_id']
                ],
                $playerData
            );
        }

        $this->command->info('Players created successfully!');
    }

    private function createContracts(): void
    {
        $players = Player::all();
        $clubs = Club::all();
        $contractTypes = ['permanent', 'loan', 'trial', 'amateur'];
        $currencies = ['EUR', 'USD', 'GBP'];
        $paymentFrequencies = ['weekly', 'monthly', 'yearly'];

        foreach ($players as $player) {
            $club = $clubs->random();
            $contractType = $contractTypes[array_rand($contractTypes)];
            $currency = $currencies[array_rand($currencies)];
            $paymentFrequency = $paymentFrequencies[array_rand($paymentFrequencies)];

            $startDate = now()->subMonths(rand(1, 24));
            $endDate = $startDate->copy()->addYears(rand(1, 5));

            $salary = match($contractType) {
                'permanent' => rand(50000, 500000),
                'loan' => rand(20000, 200000),
                'trial' => rand(5000, 50000),
                'amateur' => rand(1000, 10000),
            };

            $bonus = rand(0, $salary * 0.3);

            Contract::create([
                'player_id' => $player->id,
                'club_id' => $club->id,
                'contract_type' => $contractType,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => rand(0, 1),
                'salary' => $salary,
                'bonus' => $bonus,
                'currency' => $currency,
                'payment_frequency' => $paymentFrequency,
                'clauses' => json_encode([
                    'release_clause' => rand($salary * 2, $salary * 10),
                    'buyout_clause' => rand($salary * 1.5, $salary * 5),
                    'performance_bonus' => rand(0, $salary * 0.2),
                    'appearance_bonus' => rand(0, $salary * 0.1),
                ]),
                'bonuses' => json_encode([
                    'goals' => rand(0, $salary * 0.05),
                    'assists' => rand(0, $salary * 0.03),
                    'clean_sheets' => rand(0, $salary * 0.02),
                    'appearances' => rand(0, $salary * 0.01),
                ]),
                'special_conditions' => $this->getRandomAdditionalTerms(),
                'fifa_contract_id' => 'FIFA-CON-' . strtoupper(substr(md5(rand()), 0, 12)),
                'fifa_contract_data' => json_encode([
                    'contract_number' => 'CON-' . strtoupper(substr(md5(rand()), 0, 8)),
                    'agent_commission' => rand(0, 15),
                    'agent_commission_amount' => rand(0, $salary * 0.1),
                ]),
                'created_by' => User::inRandomOrder()->first()->id ?? 1,
            ]);
        }

        $this->command->info('Contracts created successfully!');
    }

    private function createTransfers(): void
    {
        $players = Player::all();
        $clubs = Club::all();
        $federations = Federation::all();
        $transferTypes = ['permanent', 'loan', 'free_agent'];
        $transferStatuses = ['draft', 'pending', 'submitted', 'under_review', 'approved', 'rejected', 'cancelled'];
        $itcStatuses = ['not_requested', 'requested', 'pending', 'approved', 'rejected', 'expired'];
        $paymentStatuses = ['pending', 'partial', 'completed'];

        foreach ($players as $player) {
            $clubOrigin = $clubs->random();
            $clubDestination = $clubs->where('id', '!=', $clubOrigin->id)->random();
            $federationOrigin = $federations->random();
            $federationDestination = $federations->where('id', '!=', $federationOrigin->id)->random();
            
            $transferType = $transferTypes[array_rand($transferTypes)];
            $transferStatus = $transferStatuses[array_rand($transferStatuses)];
            $itcStatus = $itcStatuses[array_rand($itcStatuses)];
            $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];

            $transferFee = match($transferType) {
                'permanent' => rand(1000000, 100000000),
                'loan' => rand(100000, 5000000),
                'free_agent' => 0,
            };

            $transferWindowStart = now()->subMonths(rand(1, 6));
            $transferWindowEnd = $transferWindowStart->copy()->addMonths(rand(1, 3));
            $transferDate = $transferWindowStart->copy()->addDays(rand(1, 30));
            $contractStartDate = $transferDate->copy()->addDays(rand(1, 30));
            $contractEndDate = $contractStartDate->copy()->addYears(rand(1, 5));

            Transfer::create([
                'player_id' => $player->id,
                'club_origin_id' => $clubOrigin->id,
                'club_destination_id' => $clubDestination->id,
                'federation_origin_id' => $federationOrigin->id,
                'federation_destination_id' => $federationDestination->id,
                'transfer_type' => $transferType,
                'transfer_status' => $transferStatus,
                'itc_status' => $itcStatus,
                'transfer_window_start' => $transferWindowStart,
                'transfer_window_end' => $transferWindowEnd,
                'transfer_date' => $transferDate,
                'contract_start_date' => $contractStartDate,
                'contract_end_date' => $contractEndDate,
                'itc_request_date' => $itcStatus !== 'not_requested' ? $transferDate->copy()->subDays(rand(1, 10)) : null,
                'itc_response_date' => in_array($itcStatus, ['approved', 'rejected']) ? $transferDate->copy()->addDays(rand(1, 5)) : null,
                'transfer_fee' => $transferFee,
                'currency' => 'EUR',
                'payment_status' => $paymentStatus,
                'fifa_transfer_id' => 'FIFA-TRF-' . strtoupper(substr(md5(rand()), 0, 12)),
                'fifa_itc_id' => $itcStatus !== 'not_requested' ? 'ITC-' . strtoupper(substr(md5(rand()), 0, 8)) : null,
                'fifa_payload' => json_encode([
                    'transfer_type' => $transferType,
                    'player_data' => [
                        'name' => $player->first_name . ' ' . $player->last_name,
                        'nationality' => $player->nationality,
                        'position' => $player->position,
                    ],
                    'financial_data' => [
                        'transfer_fee' => $transferFee,
                        'currency' => 'EUR',
                    ],
                ]),
                'fifa_response' => in_array($transferStatus, ['approved', 'rejected']) ? json_encode([
                    'status' => $transferStatus,
                    'message' => $transferStatus === 'approved' ? 'Transfer approved by FIFA' : 'Transfer rejected by FIFA',
                    'timestamp' => now()->toISOString(),
                ]) : null,
                'is_minor_transfer' => $player->date_of_birth->age < 18,
                'is_international' => $federationOrigin->id !== $federationDestination->id,
                'special_conditions' => $this->getRandomAdditionalTerms(),
                'notes' => $this->getRandomNotes(),
                'created_by' => User::inRandomOrder()->first()->id ?? 1,
            ]);
        }

        $this->command->info('Transfers created successfully!');
    }

    private function createTransferDocuments(): void
    {
        $transfers = Transfer::all();
        $documentTypes = [
            'passport', 'contract', 'medical_certificate', 'work_permit',
            'fifa_clearance', 'club_agreement', 'agent_authorization',
            'financial_guarantee', 'insurance_certificate', 'parental_consent'
        ];
        $validationStatuses = ['pending', 'approved', 'rejected', 'expired'];

        foreach ($transfers as $transfer) {
            $numDocuments = rand(3, 8);
            
            for ($i = 0; $i < $numDocuments; $i++) {
                $documentType = $documentTypes[array_rand($documentTypes)];
                $validationStatus = $validationStatuses[array_rand($validationStatuses)];

                TransferDocument::create([
                    'transfer_id' => $transfer->id,
                    'uploaded_by' => User::inRandomOrder()->first()->id ?? 1,
                    'document_type' => $documentType,
                    'document_name' => ucfirst(str_replace('_', ' ', $documentType)) . ' Document',
                    'file_path' => 'documents/transfers/' . $transfer->id . '/' . $documentType . '_' . $i . '.pdf',
                    'file_name' => $documentType . '_' . $transfer->id . '_' . $i . '.pdf',
                    'mime_type' => 'application/pdf',
                    'file_size' => rand(100000, 5000000),
                    'validation_status' => $validationStatus,
                    'validation_notes' => $validationStatus === 'approved' ? 'Document approved by FIFA' : ($validationStatus === 'rejected' ? 'Document rejected - please resubmit' : null),
                    'validated_by' => in_array($validationStatus, ['approved', 'rejected']) ? (User::inRandomOrder()->first()->id ?? 1) : null,
                    'validated_at' => in_array($validationStatus, ['approved', 'rejected']) ? now()->subDays(rand(1, 15)) : null,
                    'fifa_document_id' => 'FIFA-DOC-' . strtoupper(substr(md5(rand()), 0, 12)),
                    'fifa_metadata' => json_encode([
                        'document_type' => $documentType,
                        'upload_date' => now()->toISOString(),
                        'validation_date' => in_array($validationStatus, ['approved', 'rejected']) ? now()->subDays(rand(1, 15))->toISOString() : null,
                        'fifa_reference' => 'FIFA-REF-' . strtoupper(substr(md5(rand()), 0, 8)),
                    ]),
                ]);
            }
        }

        $this->command->info('Transfer Documents created successfully!');
    }

    private function createTransferPayments(): void
    {
        $transfers = Transfer::where('transfer_fee', '>', 0)->get();
        $paymentTypes = ['transfer_fee', 'training_compensation', 'solidarity_contribution', 'other'];
        $paymentMethods = ['bank_transfer', 'check', 'cash', 'other'];
        $paymentStatuses = ['pending', 'processing', 'completed', 'failed', 'cancelled'];

        foreach ($transfers as $transfer) {
            $numPayments = rand(1, 3);
            
            for ($i = 0; $i < $numPayments; $i++) {
                $paymentType = $paymentTypes[array_rand($paymentTypes)];
                $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
                $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];

                $amount = match($paymentType) {
                    'transfer_fee' => $transfer->transfer_fee,
                    'training_compensation' => rand($transfer->transfer_fee * 0.05, $transfer->transfer_fee * 0.15),
                    'solidarity_contribution' => rand($transfer->transfer_fee * 0.01, $transfer->transfer_fee * 0.05),
                    'other' => rand(10000, 100000),
                };

                TransferPayment::create([
                    'transfer_id' => $transfer->id,
                    'payer_id' => $transfer->club_origin_id,
                    'payee_id' => $transfer->club_destination_id,
                    'payment_type' => $paymentType,
                    'amount' => $amount,
                    'currency' => $transfer->currency,
                    'payment_method' => $paymentMethod,
                    'payment_status' => $paymentStatus,
                    'due_date' => now()->addDays(rand(1, 90)),
                    'payment_date' => $paymentStatus === 'completed' ? now()->subDays(rand(1, 30)) : null,
                    'processed_at' => $paymentStatus === 'completed' ? now()->subDays(rand(1, 30)) : null,
                    'transaction_id' => 'TXN-' . strtoupper(substr(md5(rand()), 0, 12)),
                    'reference_number' => 'REF-' . strtoupper(substr(md5(rand()), 0, 8)),
                    'payment_notes' => $this->getRandomNotes(),
                    'fifa_payment_id' => 'FIFA-PAY-' . strtoupper(substr(md5(rand()), 0, 12)),
                    'fifa_payment_data' => json_encode([
                        'payment_type' => $paymentType,
                        'amount' => $amount,
                        'currency' => $transfer->currency,
                        'status' => $paymentStatus,
                        'fifa_reference' => 'FIFA-REF-' . strtoupper(substr(md5(rand()), 0, 8)),
                    ]),
                    'created_by' => User::inRandomOrder()->first()->id ?? 1,
                ]);
            }
        }

        $this->command->info('Transfer Payments created successfully!');
    }

    private function getRandomAdditionalTerms(): string
    {
        $terms = [
            'Player must maintain fitness standards',
            'Performance bonuses based on goals scored',
            'Image rights shared 50/50',
            'No international call-ups during season',
            'Mandatory medical check-ups every 6 months',
            'Social media restrictions during season',
            'Appearance bonuses for cup matches',
            'Loyalty bonus after 3 years',
            'Release clause valid after 2 years',
            'Loan recall option available',
        ];

        return $terms[array_rand($terms)];
    }

    private function getRandomNotes(): string
    {
        $notes = [
            'Standard transfer procedure followed',
            'All documents verified and approved',
            'Medical examination completed successfully',
            'Agent commission negotiated separately',
            'Payment schedule agreed upon',
            'International clearance obtained',
            'Work permit application submitted',
            'Insurance coverage confirmed',
            'Tax implications considered',
            'Compliance with FIFA regulations confirmed',
        ];

        return $notes[array_rand($notes)];
    }

    private function getRandomAgentName(): string
    {
        $agents = [
            'Jorge Mendes',
            'Mino Raiola',
            'Jonathan Barnett',
            'Pini Zahavi',
            'Volker Struth',
            'Giuseppe Riso',
            'Federico Pastorello',
            'Christian Emile',
            'Pere Guardiola',
            'José Otin',
        ];

        return $agents[array_rand($agents)];
    }
}
