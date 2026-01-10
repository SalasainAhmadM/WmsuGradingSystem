<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('CREATE TABLE year_levels(
            id INT PRIMARY KEY AUTO_INCREMENT,
            year_level VARCHAR(100) UNIQUE,
            is_active BOOLEAN default 1,
            date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
            date_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );');
        DB::statement('INSERT INTO year_levels VALUES(
            NULL,
            "1st year",
            1,
            NOW(),
            NOW()
        );');
        DB::statement('INSERT INTO year_levels VALUES(
            NULL,
            "2nd year",
            1,
            NOW(),
            NOW()
        );');
        DB::statement('INSERT INTO year_levels VALUES(
            NULL,
            "3rd year",
            1,
            NOW(),
            NOW()
        );');
        DB::statement('INSERT INTO year_levels VALUES(
            NULL,
            "4th year",
            1,
            NOW(),
            NOW()
        );');
        DB::statement('INSERT INTO year_levels VALUES(
            NULL,
            "5th year",
            1,
            NOW(),
            NOW()
        );');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('year_levels');
    }
};
