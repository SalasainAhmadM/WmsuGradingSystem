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
        DB::statement('CREATE TABLE lab_lec_grades(
            id INT PRIMARY KEY AUTO_INCREMENT,
            schedule_id INT,
            lab_lec_id INT,
            student_id INT,
            grade DOUBLE,
            sub_weight DOUBLE,
            other VARCHAR(50),
            date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
            date_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_lec_grades');
    }
};
