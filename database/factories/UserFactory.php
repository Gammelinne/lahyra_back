<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $password = 'kiki';
        $gender = ['M', 'F', 'O'];
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'pseudo' => $this->faker->unique()->userName,
            'avatar' => $this->faker->imageUrl(640, 480, 'people', true, 'Faker'),
            'email' => $this->faker->unique()->safeEmail,
            'phone' => '0' . $this->faker->unique()->numberBetween(600000000, 799999999),
            'birthday' => $this->faker->date($format = 'Y-m-d', $max = 'now'),
            'bio' => $this->faker->text($maxNbChars = 120),
            'gender' => $gender[rand(0, 2)],
            'address' => $this->faker->address,
            'password' => bcrypt($password),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
