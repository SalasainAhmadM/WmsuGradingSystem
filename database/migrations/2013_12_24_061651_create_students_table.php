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
        DB::statement('CREATE TABLE students(
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT,
            college_id INT,
            department_id INT,
            year_level_id INT, 
            code VARCHAR(100) UNIQUE,
            first_name VARCHAR(255)  NOT NULL,
            middle_name VARCHAR(255) ,
            last_name VARCHAR(255) NOT NULL,
            suffix VARCHAR(255),
            email VARCHAR(100) UNIQUE,
            is_active BOOL DEFAULT 1,
            date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
            date_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );');

        DB::statement('CREATE INDEX idx_students_student_code ON students(code(10));');
        DB::statement('CREATE INDEX idx_students_fullname ON students(first_name(10),middle_name(10),last_name(10),suffix(10));');
        DB::statement('CREATE INDEX idx_students_email ON students(email(10));');
        DB::statement('CREATE INDEX idx_students_college_id ON students(college_id);');
        DB::statement('CREATE INDEX idx_students_department_id ON students(department_id);');
        
      
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
