<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (empty(User::where('email', "adm@turno.com")->first())) {
            DB::table('users')->insert([
                'name' => "Admin",
                'email' => "adm@turno.com",
                'password' => Hash::make('password'),
                'balance' => 0,
                'account_number' => '0000000000',
                'remember_token' => Str::random(10),
                'user_type' => 'A',
                'created_at' => Carbon::now()
            ]);
        }
    }
}
