<?php

namespace Database\Seeders;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $merchant = Merchant::query()->where('trade_name', '=', 'Trixaltech')->first();
        $users = [
            [
                'name' => 'Admin',
                'email' => 'support@bulksms.com',
                'password' => Hash::make('password'),
                'remember_token' => null,
                'merchant_id' => $merchant->id
            ],
            [
                'name' => 'User',
                'email' => 'user@bulksms.com',
                'password' => Hash::make('password'),
                'remember_token' => null,
                'merchant_id' => $merchant->id
            ],
        ];

        foreach ($users as $user) {
            User::query()->firstOrCreate($user, $user);
        }

    }
}
