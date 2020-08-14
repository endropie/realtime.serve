<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\DirectMessage;
use App\User;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {

    $faker->addProvider(new Ottaviano\Faker\Gravatar($faker));

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
        'avatar' => $faker->gravatarUrl()
    ];
});

$factory->define(DirectMessage::class, function (Faker $faker) {
    do {
        $sender = rand(1, 15);
        $receiver = rand(1, 15);
    } while ($sender === $receiver);

    $dt = Carbon::createFromTimeStamp($faker->dateTimeBetween('-1 month')->getTimestamp());

    $received = rand(1,10) > 5; $readed = rand(1,10) > 5;

    return [
        'sender_id' => $sender,
        'receiver_id' => $receiver,
        'text' => $faker->sentence,
        'created_at' => $dt->toDateTimeString(),
        'received_at' => $received ? $dt->addSeconds(rand(1,60))->toDateTimeString() : null,
        'readed_at' => $received && $readed ? $dt->addSeconds(rand(100,200))->toDateTimeString() : null,
    ];
});
