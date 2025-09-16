<?php

namespace Database\Factories;

use App\Models\TransferDocument;
use App\Models\Transfer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransferDocument>
 */
class TransferDocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $documentTypes = [
            'transfer_request',
            'player_contract',
            'medical_certificate',
            'passport_copy',
            'work_permit',
            'fifa_clearance',
            'club_agreement',
            'agent_authorization',
            'financial_guarantee',
            'insurance_certificate'
        ];
        
        $statuses = ['pending', 'approved', 'rejected', 'expired'];
        
        return [
            'transfer_id' => Transfer::factory(),
            'document_type' => $this->faker->randomElement($documentTypes),
            'status' => $this->faker->randomElement($statuses),
            'file_name' => $this->faker->fileName('pdf'),
            'file_path' => 'documents/transfers/' . $this->faker->uuid() . '.pdf',
            'file_size' => $this->faker->numberBetween(100000, 5000000),
            'mime_type' => 'application/pdf',
            'uploaded_by' => $this->faker->numberBetween(1, 10),
            'uploaded_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'approved_by' => $this->faker->optional()->numberBetween(1, 10),
            'approved_at' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
            'rejected_by' => $this->faker->optional()->numberBetween(1, 10),
            'rejected_at' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
            'rejection_reason' => $this->faker->optional()->sentence(),
            'expiry_date' => $this->faker->optional()->dateTimeBetween('now', '+2 years'),
            'notes' => $this->faker->optional()->paragraph(),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Indicate that the document is a transfer request.
     */
    public function transferRequest(): static
    {
        return $this->state(fn (array $attributes) => [
            'document_type' => 'transfer_request',
        ]);
    }

    /**
     * Indicate that the document is a player contract.
     */
    public function playerContract(): static
    {
        return $this->state(fn (array $attributes) => [
            'document_type' => 'player_contract',
        ]);
    }

    /**
     * Indicate that the document is a medical certificate.
     */
    public function medicalCertificate(): static
    {
        return $this->state(fn (array $attributes) => [
            'document_type' => 'medical_certificate',
        ]);
    }

    /**
     * Indicate that the document is a passport copy.
     */
    public function passportCopy(): static
    {
        return $this->state(fn (array $attributes) => [
            'document_type' => 'passport_copy',
        ]);
    }

    /**
     * Indicate that the document is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approved_by' => $this->faker->numberBetween(1, 10),
            'approved_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the document is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'rejected_by' => $this->faker->numberBetween(1, 10),
            'rejected_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'rejection_reason' => $this->faker->sentence(),
        ]);
    }

    /**
     * Indicate that the document is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
            'expiry_date' => $this->faker->dateTimeBetween('-1 year', '-1 month'),
        ]);
    }
}
