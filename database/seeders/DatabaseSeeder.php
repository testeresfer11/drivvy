<?php

namespace Database\Seeders;

use App\Models\{User, Rides, Reviews, Cars, Bookings, Messages, Notifications,Review,Payments};
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        // Cars::factory(10)->create();
        // Rides::factory(10)->create();
        // Bookings::factory(10)->create();
        // Reviews::factory(10)->create();
        //  Payments::factory(10)->create();
        //  Messages::factory(10)->create();

        //$this->call(DummySeeder::class);
        //$this->call(VechileSeeder::class);
        //$this->call(PolicySeeder::class);
        //$this->call(CountrySeeder::class);
        //$this->call(ReportSeeder::class);
    }
}
