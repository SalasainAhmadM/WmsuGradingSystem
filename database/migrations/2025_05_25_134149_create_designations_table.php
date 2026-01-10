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
        DB::statement('CREATE TABLE designations(
            id INT PRIMARY KEY AUTO_INCREMENT,
            code VARCHAR(100) UNIQUE,
            name VARCHAR(255) NOT NULL,
            is_active BOOL DEFAULT 1,
            date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
            date_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );');

        DB::statement('INSERT INTO designations VALUES(NULL,"P","Professor",1,NOW(),NOW());');
        DB::statement('INSERT INTO designations VALUES(NULL,"AstP","Assistant Professor",1,NOW(),NOW());');
        DB::statement('INSERT INTO designations VALUES(NULL,"AS","Academic Staff",1,NOW(),NOW());');
        DB::statement('INSERT INTO designations VALUES(NULL,"AsP","Associate Professor",1,NOW(),NOW());');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designations');
    }
};
