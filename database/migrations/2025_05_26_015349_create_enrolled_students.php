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
        DB::statement('CREATE TABLE enrolled_students(
            id INT PRIMARY KEY AUTO_INCREMENT,
            student_id INT,
            schedule_id INT,
            date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
            date_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );');

        DB::statement('CREATE INDEX idx_enrolled_student_id ON enrolled_students(student_id);');
        DB::statement('CREATE INDEX idx_enrolled_schedule_id ON enrolled_students(schedule_id);');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrolled_students');
    }
};
