<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Report;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Report::updateOrCreate(['id' => 1], [
            'type' => 'Fraudulent activity',
        ]);

        Report::updateOrCreate(['id' => 2], [
            'type' => 'Dangerous or inappropriate behaviour',
        ]);

        Report::updateOrCreate(['id' => 3], [
            'type' => 'Problem on the profile',
        ]);

        Report::updateOrCreate(['id' => 4], [
            'type' => 'Price or payment method questionable',
        ]);
    }
}
