<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentsSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('INSERT INTO departments VALUES(NULL,15,"BSIT","Bachelor of Science in Information Technology",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,15,"BSCS","Bachelor of Science in Computer Science",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,15,"BSACT","Associate Technology Program",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,9,"BSAC","Bachelor of Science in Accountancy",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,9,"BASA","Bachelor of Arts in Social Studies",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,9,"BAE","Bachelor of Arts in English",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,9,"BAPS","Bachelor of Arts in Political Science",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,9,"BAMCJ","Bachelor of Arts in Mass Communication – Journalism",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,9,"BAMCSB","Bachelor of Arts in Mass Communication – Broadcasting",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,9,"BSECON","Bachelor of Science in Economics",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,9,"BSPsych","Bachelor of Science in Psychology",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,5,"BSCE","Bachelor of Science in Civil Engineering",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,5,"BSCOE","Bachelor of Science in Computer Engineering",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,5,"BSEE","Bachelor of Science in Electrical Engineering",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,5,"BSEnE","Bachelor of Science in Environmental Engineering",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,5,"BSIE","Bachelor of Science in Industrial Engineering",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,5,"BSME","Bachelor of Science in Mechanical Engineering",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,5,"BSGE","Bachelor of Science in Geodetic Engineering",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,1,"BAMCS","Bachelor of Science in Agriculture Major Crop Science",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,1,"BAMAS","Bachelor of Science in Agriculture Major Animal Science",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,1,"BAB","Bachelor of Science in AgriBusiness",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,1,"BSAE","Bachelor of Science in  Agricultural Engineering",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,1,"BATDAT","Bachelor in Agricultural Technology/Diploma in Agricultural Technology",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,2,"BSArch","Bachelor of Science in Architecture (BSArch)",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,3,"BSIS","Bachelor of Science in Islamic Studies",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,3,"DAASMSA","Bachelor of Arts in Asian Studies: Major Southeast Asia",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,3,"DAL","Diploma in Arabic Language",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,4,"CRIM","Bachelor of Science in Criminology",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,6,"BSF","Bachelor of Science in Forestry",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,6,"BSAF","Bachelor of Science in Agroforestry",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,6,"BSES","Bachelor of Science in Environment Science",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,7,"BSFT","Bachelor of Science in Food tech",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,7,"BSHEED","Bachelor of Science in Home Economics Education",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,7,"BSHRM","Bachelor of Science in Hotel and Restaurant Management",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,7,"BSND","Bachelor of Science in Nutrition and Dietetics",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,7,"DFP","Diploman in Food Processing",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,13,"BSB","Bachelor of Science in Biology",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,13,"BSC","Bachelor of Science in Chemistry",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,13,"BSM","Bachelor of Science in mathematics",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,13,"BSP","Bachelor of Science in Physics",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,13,"BSS","Bachelor of Science in Statistics",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,13,"BSSW","Bachelor of Science in Social Work",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,14,"COMDEV","Bachelor of Science in Community Development",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,16,"BEed GE","Bachelor of Elementary Education Major General Education",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,16,"BEed ECE","Bachelor of Elementary Education Major Early Childhood Education",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,16,"BEedSE","Bachelor of Elementary Education Major Special Education",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,16,"BSEdME","Bachelor of Secondary Education Major English",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,16,"BSEdMMapeh","Bachelor of Secondary Education Major Mapeh",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,16,"BSEdMSS","Bachelor of Secondary Education Major Social Studies",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,16,"BSEdMF","Bachelor of Secondary Education Major Filipino",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,16,"BSEdMBS","Bachelor of Secondary Education Major Biology Science",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,16,"BSEdMPS","Bachelor of Secondary Education Major Physical Science",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,16,"BSEdMMath","Bachelor of Secondary Education Major Mathematics",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,16,"BSEdMJE","Bachelor of Secondary Education Major: Values Education",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,10,"BSN","Bachelor of Science in Nursing",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,8,"BSL","Bachelor of Science in Law",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,11,"CPADS","College of Administrative and Development Studies",1,NOW(),NOW());');
        DB::statement('INSERT INTO departments VALUES(NULL,12,"CSPE","College of Sports Science and Physical Education",1,NOW(),NOW());');
    }
}
