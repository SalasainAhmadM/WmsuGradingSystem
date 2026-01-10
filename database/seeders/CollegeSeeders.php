<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CollegeSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('INSERT INTO colleges VALUES(NULL,"CA","College of Agriculture",1,NOW(),NOW());');
        DB::statement('INSERT INTO colleges VALUES(NULL,"COA","College of Architecture",1,NOW(),NOW());');
        DB::statement('INSERT INTO colleges VALUES(NULL,"CAIS","College of Asian and Islamic Studies",1,NOW(),NOW());');
        DB::statement('INSERT INTO colleges VALUES(NULL,"CCJE","College of Criminal Justice Education",1,NOW(),NOW());');
        DB::statement('INSERT INTO colleges VALUES(NULL,"COE","College of Engineering",1,NOW(),NOW());');
        DB::statement('INSERT INTO colleges VALUES(NULL,"CFES","College of Forestry and Environmental Studies",1,NOW(),NOW());');
        DB::statement('INSERT INTO colleges VALUES(NULL,"CHE","College of Home Economics",1,NOW(),NOW());');
        DB::statement('INSERT INTO colleges VALUES(NULL,"CL","College of Law",1,NOW(),NOW());');
        DB::statement('INSERT INTO colleges VALUES(NULL,"CLA","College of Liberal Arts",1,NOW(),NOW());');
        DB::statement('INSERT INTO colleges VALUES(NULL,"CN","College of Nursing",1,NOW(),NOW());');
        DB::statement('INSERT INTO colleges VALUES(NULL,"CPADS","College of Administrative and Development Studies",1,NOW(),NOW());');
        DB::statement('INSERT INTO colleges VALUES(NULL,"CSPE","College of Sports Science and Physical Education",1,NOW(),NOW());');
        DB::statement('INSERT INTO colleges VALUES(NULL,"CSM","College of Science and Mathematics",1,NOW(),NOW());');
        DB::statement('INSERT INTO colleges VALUES(NULL,"CSCD","College of Social Work and Community Development",1,NOW(),NOW());');
        DB::statement('INSERT INTO colleges VALUES(NULL,"CCS","College of Computing Studies",1,NOW(),NOW());');
        DB::statement('INSERT INTO colleges VALUES(NULL,"CTE","College of Teacher Education",1,NOW(),NOW());');
    }
}
