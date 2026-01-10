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
        DB::statement('CREATE TABLE academic_ranks(
            id INT PRIMARY KEY AUTO_INCREMENT,
            code VARCHAR(100) UNIQUE,
            name VARCHAR(255) NOT NULL,
            is_active BOOL DEFAULT 1,
            date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
            date_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );');


        DB::statement('INSERT INTO academic_ranks VALUES(NULL,"I-1","Instructor I",1,NOW(),NOW());');
        DB::statement('INSERT INTO academic_ranks VALUES(NULL,"I-2","Instructor II",1,NOW(),NOW());');
        DB::statement('INSERT INTO academic_ranks VALUES(NULL,"I-3","Instructor III",1,NOW(),NOW());');
        DB::statement('INSERT INTO academic_ranks VALUES(NULL,"P-1","Professor I",1,NOW(),NOW());');
        DB::statement('INSERT INTO academic_ranks VALUES(NULL,"P-2","Professor II",1,NOW(),NOW());');
        DB::statement('INSERT INTO academic_ranks VALUES(NULL,"P-3","Professor III",1,NOW(),NOW());');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_ranks');
    }
};
