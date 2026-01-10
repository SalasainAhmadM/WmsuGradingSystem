<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchoolYearSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('INSERT INTO school_years VALUES(
            NULL,
            YEAR(NOW())-1,
            YEAR(NOW()),
            DATE("2024-08-1"),
            DATE("2025-05-31"),
            NOW(),
            NOW()
        );');
        DB::statement('INSERT INTO school_years VALUES(
            NULL,
            YEAR(NOW()),
            YEAR(NOW())+1,
            DATE("2025-08-1"),
            DATE("2026-05-31"),
            NOW(),
            NOW()
        );');
      
    }
}
