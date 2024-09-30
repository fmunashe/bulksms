<?php

namespace Database\Seeders;

use App\Models\Merchant;
use Illuminate\Database\Seeder;

class MerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $merchants = [
            ['trade_name' => "Trixaltech", 'email' => 'support@trixaltech.com', 'mobile' => '0778234258', 'contact_person' => 'Farai', 'contact_person_mobile' => '0778234258', 'contact_person_email' => 'zihovem@gmail.com', 'address' => 'Harare Zimbabwe']
        ];
        foreach ($merchants as $merchant) {
            Merchant::query()->firstOrCreate($merchant, $merchant);
        }
    }
}
