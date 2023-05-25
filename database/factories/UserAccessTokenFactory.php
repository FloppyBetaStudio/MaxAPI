<?php

namespace Database\Factories;

use App\Models\UserAccessToken;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class UserAccessTokenFactory extends Factory
{
    protected $model = UserAccessToken::class;

    public function definition(): array
    {
        return [
            'username' => $this->faker->userName(),
            'token' => Str::random(128),
            'dt_create' => Carbon::now(),
            'dt_expire' => Carbon::now()->addYear(),
        ];
    }
}
