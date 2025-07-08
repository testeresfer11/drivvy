<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
use App\Models\Policies;

class PolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Policies::updateOrCreate(['id' => 1], [
            'type' => 'terms',
            'content' => 'Initial terms and conditions content.',
        ]);

        Policies::updateOrCreate(['id' => 2], [
            'type' => 'privacy',
            'content' => 'Initial privacy policy content.',
        ]);

        Policies::updateOrCreate(['id' => 3], [
            'type' => 'refund',
            'content' => 'Initial privacy policy content.',
        ]);
    }
}
