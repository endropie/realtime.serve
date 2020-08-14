<?php

use App\DirectMessage;
use App\User;
use Illuminate\Database\Seeder;

class UserRealtimeSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(['email' => 'admin@laravel.test'], [
            'name' => 'Adminstrator',
            'password' => bcrypt('password'),
            'avatar' => 'https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y'
        ]);

        factory(User::class, 15)->create();

        factory(DirectMessage::class, 1000)->create();

    }
}
