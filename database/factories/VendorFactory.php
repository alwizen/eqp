<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendorFactory extends Factory
{
    protected $model = Vendor::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->bothify('VND-####'),
            'name' => $this->faker->company(),
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->safeEmail(),
            'contact_person' => $this->faker->name(),
            'scope_of_work' => $this->faker->sentence(5),
            'is_active' => true,
            'notes' => $this->faker->paragraph(),
        ];
    }
}
