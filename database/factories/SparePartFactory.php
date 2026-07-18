<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SparePart;
use Illuminate\Database\Eloquent\Factories\Factory;

class SparePartFactory extends Factory
{
    protected $model = SparePart::class;

    public function definition(): array
    {
        return [
            'part_number' => $this->faker->unique()->bothify('SP-#####'),
            'name' => $this->faker->word() . ' ' . $this->faker->randomElement(['Gasket', 'Seal', 'Filter', 'Bearing', 'Belt', 'O-Ring']),
            'manufacturer' => $this->faker->company(),
            'specification' => $this->faker->sentence(3),
            'unit' => $this->faker->randomElement(['pcs', 'set', 'roll', 'box']),
            'current_stock' => $this->faker->randomFloat(3, 10, 100),
            'minimum_stock' => $this->faker->randomFloat(3, 2, 10),
            'unit_price' => $this->faker->randomFloat(2, 5000, 100000),
            'is_active' => true,
            'notes' => $this->faker->paragraph(),
        ];
    }
}
