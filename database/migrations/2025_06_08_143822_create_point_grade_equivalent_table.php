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
        DB::statement('CREATE TABLE point_grade_equivalent(
            id INT PRIMARY KEY AUTO_INCREMENT,
            minimum DOUBLE,
            maximum DOUBLE,
            grade VARCHAR(255), 
            date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
            date_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );');

        DB::statement('INSERT INTO point_grade_equivalent VALUES(NULL,97,100,"1",NOW(),NOW());');
        DB::statement('INSERT INTO point_grade_equivalent VALUES(NULL,94,96,"1.25",NOW(),NOW());');
        DB::statement('INSERT INTO point_grade_equivalent VALUES(NULL,91,93,"1.50",NOW(),NOW());');
        DB::statement('INSERT INTO point_grade_equivalent VALUES(NULL,88,90,"1.75",NOW(),NOW());');
        DB::statement('INSERT INTO point_grade_equivalent VALUES(NULL,85,87,"2.0",NOW(),NOW());');
        DB::statement('INSERT INTO point_grade_equivalent VALUES(NULL,82,84,"2.25",NOW(),NOW());');
        DB::statement('INSERT INTO point_grade_equivalent VALUES(NULL,79,81,"2.50",NOW(),NOW());');
        DB::statement('INSERT INTO point_grade_equivalent VALUES(NULL,76,78,"2.75",NOW(),NOW());');
        DB::statement('INSERT INTO point_grade_equivalent VALUES(NULL,75,75,"3.0",NOW(),NOW());');
        DB::statement('INSERT INTO point_grade_equivalent VALUES(NULL,-1,74,"5.0",NOW(),NOW());');
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_grade_equivalent');
    }
};
