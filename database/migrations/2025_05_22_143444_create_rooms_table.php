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
        DB::statement('CREATE TABLE rooms(
            id INT PRIMARY KEY AUTO_INCREMENT,
            code VARCHAR(100) UNIQUE,
            name VARCHAR(255) NOT NULL,
            is_active BOOL DEFAULT 1,
            date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
            date_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );');

        DB::statement('INSERT INTO rooms VALUES(NULL,"LR1","Lecture Room 1",1,NOW(),NOW());');
        DB::statement('INSERT INTO rooms VALUES(NULL,"LR2","Lecture Room 1",1,NOW(),NOW());');
        DB::statement('INSERT INTO rooms VALUES(NULL,"CLAR","College of Liberal Arts Room",1,NOW(),NOW());');
        DB::statement('INSERT INTO rooms VALUES(NULL,"LAB1","Laboratory Room 1",1,NOW(),NOW());');
        DB::statement('INSERT INTO rooms VALUES(NULL,"LAB2","Laboratory Room 2",1,NOW(),NOW());');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
