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
        DB::statement('CREATE TABLE faculty(
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT, 
            college_id INT,
            department_id INT,
            code VARCHAR(100) UNIQUE,
            academic_rank_id INT,  
            designation_id INT,
            faculty_type_id INT,
            release_time ENUM("Without Release Time", "With Release Time"),
	        hours_per_week INT ,
            date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
            date_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculty');
    }
};
