<?php

namespace Database\Factories;

use App\Models\Retailer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class RetailerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Retailer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => $this->faker->randomNumber(),
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'document_number' => $this->faker->text,
            'password' => Hash::make('secret123'),
        ];
    }
}
