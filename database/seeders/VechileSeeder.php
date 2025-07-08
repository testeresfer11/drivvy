<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class VechileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('vechiles')->insert([
            'make' => 'Audi',
            'model' => '100, 200, 4000, 4000CS Quattro, 4000s, 80, 80/90, A3, A4, A5, A6, A7, A8, Allroad, Cabriolet, Coupe GT, Coupe Quattro, Q5, Q7, Quattro, Other',
            'type' => 'Sedan, Hatchback, Van, SUV, Pickup, Other	',
            'color' => 'Grey, Black, White, Blue, Red, Sliver, Orange, Green, Bronze, Yellow, Other	',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('vechiles')->insert([
            'make' => 'BMW',
            'model' => '1 series, 3 series, 325, 330, 5 series, 525, 650, 7 series, 745, 750, 760, 8 series, Alpina B7, M, M Roadster, M3, M5, M6, X3, X5, X5M, X6, X6M, Z3, Z4, Z4M, Z4M Roadster, Z8, Other',
            'type' => 'Sedan, Hatchback, Van, SUV, Pickup, Other',
            'color' => 'Grey, Black, White, Blue, Red, Silver, Orange, Green, Bronze, Yellow, Other',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('vechiles')->insert([
            'make' => 'Honda',
            'model' => 'Accord, Accord Crosstour, Civic, Civic GX, Civic Si, Crosstour, CR-V, CR-X, CR-Z, Del Sol, Element, FCX Clarity, Fit, Insight, Odyssey, Passport, Pilot, Prelude, Ridgeline, S2000, Other',
            'type' => 'Sedan, Hatchback, Van, SUV, Pickup, Other',
            'color' => 'Grey, Black, White, Blue, Red, Sliver, Orange, Green, Bronze, Yellow, Other',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('vechiles')->insert([
            'make' => 'Hyundai',
            'model' => 'Accent, Azera, Elantra, Entourage, Equus, Excel, Genesis, Genesis Coupe, HED-5, Santa Fe, Scoupe, Sonata, Tiburon, Tucson, Veloster, Veracruz, XG300, XG350, Other',
            'type' => 'Sedan, Hatchback, Van, SUV, Pickup, Other',
            'color' => 'Grey, Black, White, Blue, Red, Sliver, Orange, Green, Bronze, Yellow, Other',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
