<?php

namespace Database\Seeders;

use App\Models\PricePlanType;
use Illuminate\Database\Seeder;

class PricePlanTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Prepaid', 'status' => 'Active'],
            ['name' => 'Postpaid', 'status' => 'Active'],
            ['name' => 'Custom', 'status' => 'Active']
        ];

        foreach ($types as $type) {
            PricePlanType::query()->firstOrCreate($type, $type);
        }
    }
}
