<?php

use App\Role;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeederTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $password = '123456';
        $user = User::create([
            'first_name' => 'Super' ,
            'last_name'  => 'Admin' ,
            'email'      => 'SuperAdmin@pos.us',
            'password'   => Hash::make($password)
        ]);

        $user->attachRole('super_admin');
    }
}
