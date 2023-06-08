<?php

use Illuminate\Database\Seeder;
use App\Models\Preference;

class PreferenceSeeder extends Seeder
{
    public function run()
    {
        Preference::create([
            'id' => 1,
            'name' => 1,
            'price' => 1,
            'sku' => 1,
            'details' => 1,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

