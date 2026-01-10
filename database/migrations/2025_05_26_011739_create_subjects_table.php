<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void{
        DB::statement('CREATE TABLE subjects(
            id INT PRIMARY KEY AUTO_INCREMENT,
            college_id INT,
            department_id INT,
            subject_id VARCHAR(50) NOT NULL,
            subject_code VARCHAR(100),
            description VARCHAR(255),
            prerequisite_subject_id VARCHAR(255),
            lecture_unit INT,
            laboratory_unit INT,
            is_active BOOL DEFAULT 1,
            date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
            date_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
