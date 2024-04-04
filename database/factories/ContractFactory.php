<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\ContractType;
use App\Models\Place;
use App\Models\Planning;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contract>
 */
class ContractFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory(1)->create([
            'password' => Hash::make('12345678')
        ]);

        return [
            'business_id' => Business::query()->inRandomOrder()->first()->id,
            'contract_type_id' => ContractType::query()->inRandomOrder()->first()->id,
            'planning_id' => Planning::query()->inRandomOrder()->first()->id,
            'place_id' => Place::query()->inRandomOrder()->first()->id,
            'user_id' => $user->first()->id,
            'team_id' => Team::query()->inRandomOrder()->first()->id,
            'start' => $this->faker->dateTimeThisYear(),
        ];
    }
}
