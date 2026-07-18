<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\EquipmentCondition;
use App\Enums\EquipmentStatus;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EquipmentFactory extends Factory
{
    protected $model = Equipment::class;

    public function definition(): array
    {
        return [
            'tag_no' => $this->faker->unique()->bothify('####-??-???/##'),
            'technical_no' => $this->faker->bothify('##"-??-###'),
            'description' => $this->faker->sentence(3),
            'functional_location' => $this->faker->word() . ' / ' . $this->faker->word(),
            'manufacturer' => $this->faker->company(),
            'model_type' => $this->faker->word(),
            'serial_number' => $this->faker->uuid(),
            'category' => $this->faker->randomElement(['Pump', 'Valve', 'Compressor', 'Vessel']),
            'installation_date' => $this->faker->date(),
            'status' => EquipmentStatus::OPERATIONAL->value,
            'latest_condition' => EquipmentCondition::GOOD->value,
            'notes' => $this->faker->paragraph(),
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }

    public function operational(): self
    {
        return $this->state(fn () => [
            'status' => EquipmentStatus::OPERATIONAL->value,
            'latest_condition' => EquipmentCondition::GOOD->value,
        ]);
    }

    public function breakdown(): self
    {
        return $this->state(fn () => [
            'status' => EquipmentStatus::BREAKDOWN->value,
            'latest_condition' => EquipmentCondition::DAMAGED->value,
        ]);
    }

    public function underMaintenance(): self
    {
        return $this->state(fn () => [
            'status' => EquipmentStatus::UNDER_MAINTENANCE->value,
            'latest_condition' => EquipmentCondition::MINOR_ISSUE->value,
        ]);
    }
}
