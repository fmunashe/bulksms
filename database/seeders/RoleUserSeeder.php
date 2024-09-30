<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->where('email', '=', 'support@bulksms.com')->first()->roles()->sync(1);
        User::query()->where('email', '=', 'user@bulksms.com')->first()->roles()->sync(2);
    }
}
