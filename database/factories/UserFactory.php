<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $accountNumber = null;
        do {
            $accountNumber = mt_rand(1000000000, 9999999999);
        } while (substr($accountNumber, 0, 1) === '0' || User::where('account_number', $accountNumber)->exists());

    return [
        'name' => $this->faker->name,
        'email' => $this->faker->unique()->safeEmail,
        'username' => $this->faker->unique()->userName,
        'password' => static::$password ??= Hash::make('password'),
        'account_number' => $accountNumber,
        'remember_token' => Str::random(10),
        'balance' => round($this->faker->randomFloat(2, 0, 1000), 2),
    ];
    }
}
