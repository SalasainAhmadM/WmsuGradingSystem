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
         DB::statement('CREATE TABLE school_work_scores(
            id INT PRIMARY KEY AUTO_INCREMENT,
            schedule_id INT,
            student_id INT,
            term_id INT,
            school_work_id INT,
            score DOUBLE,
            max_score DOUBLE,
            date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
            date_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_work_scores');
    }
};
