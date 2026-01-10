<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('CREATE TABLE departments(
            id INT PRIMARY KEY AUTO_INCREMENT,
            college_id INT,
            code VARCHAR(100) UNIQUE,
            name VARCHAR(255) NOT NULL,
            is_active BOOL DEFAULT 1,
            date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
            date_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );');
        DB::statement('CREATE INDEX idx_college_id ON departments(college_id);');
        DB::statement('CREATE INDEX idx_department_code ON departments(code(10));');
        DB::statement('CREATE INDEX idx_department_name ON departments(name(10));');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
