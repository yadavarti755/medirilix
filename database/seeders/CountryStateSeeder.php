<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountryStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load countries
        $countriesJson = file_get_contents(database_path('seeders/countries.json'));
        $countries = json_decode($countriesJson, true);

        $countryMap = []; // Map iso2 => id

        foreach ($countries as $country) {
            DB::table('countries')->insert([
                'id' => $country['id'],
                'name' => $country['name'],
                'iso2' => $country['iso2'],
                'phone_code' => $country['phonecode'] ?? null,
                'currency' => $country['currency'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $countryMap[$country['iso2']] = $country['id'];
        }

        // Load states
        $statesJson = file_get_contents(database_path('seeders/states.json'));
        $states = json_decode($statesJson, true);

        foreach ($states as $state) {
            if (!isset($countryMap[$state['country_code']])) continue;

            DB::table('states')->insert([
                'id' => $state['id'],
                'country_id' => $countryMap[$state['country_code']],
                'name' => $state['name'],
                'iso2' => $state['iso2'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
