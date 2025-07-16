<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
       {
           $users = [
               [
                   'name' => 'Alice',
                   'email' => 'alice@example.com',
                   'password' => Hash::make('password'),
               ],
               [
                   'name' => 'Jack',
                   'email' => 'jack@example.com',
                   'password' => Hash::make('password'),
               ],
               [
                   'name' => 'Bob',
                   'email' => 'bob@example.com',
                   'password' => Hash::make('password'),
               ],
               [
                   'name' => 'Test',
                   'email' => 'test@example.com',
                   'password' => Hash::make('password'),
               ],
               [
                   'name' => 'Lilly',
                   'email' => 'lilly@example.com',
                   'password' => Hash::make('password'),
               ],
           ];

           foreach ($users as $user) {
               User::create($user);
           }
       }
}
