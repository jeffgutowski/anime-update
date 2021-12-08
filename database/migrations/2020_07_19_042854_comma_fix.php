<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CommaFix extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("update games
            set name = replace(concat('The ', name), ', The', '')
            where name like '%, The%'
            and name not like '%:%'
            and name not like '%chronicles of narnia%'
            and name not like '%new japan pro wrestling%next generation'
            and name not like '%slaine%'
            and name not like '%the fabulous wanda%'
        ");
        DB::statement("update games set name = 'Finding Nemo: Escape the Big Blue' where name = 'Finding Nemo: Escape the the Big Blue'");
        DB::statement("update games set name = 'The Bear, the Cat and the Rabbit' where name = 'The The Bear, the Cat and the Rabbit'");
        DB::statement("update games set name = 'Yago, the Coquerrestrial' where name = 'The Yago, the Coquerrestrial'");
        DB::statement("update games set name = 'The Magic of Scheherazade' where name = 'The Magic of Scheherazade, the'");
        DB::statement("update games set name = 'The Blue Marlin' where name = 'The Blue Marlin, the'");
        DB::statement("update games
            set name = replace(concat('The ', name), ', The', '')
            where name like '%, The:%'
            and name not like '%chronicles of narnia%'
            and name not like '%new japan pro wrestling%next generation'
            and name not like '%slaine%'
            and name not like '%the fabulous wanda%'
        ");
        DB::statement("update games
            set name = replace(concat('The ', name), ', The', '')
            where name like '%, The%'
            and name not like '%chronicles of narnia%'
            and name not like '%new japan pro wrestling%next generation'
            and name not like '%slaine%'
            and name not like '%the fabulous wanda%'
            and name not like '%simon the sorcerer%'
            and name not like '%dr. langeskov%'
            and name not like '%sam & max%'
            and name not like '%umd video%'
            and name not like '%Bear, the Cat%'
            and name not like '%Yago%'
        ");
        DB::statement("update games
            set name = replace(replace(name, ', The', ''), ':', ': The')
            where name like '%, The%'
            and name not like '%narnia%'
            and name like 'umd video%'
        ");
        DB::statement("update games
            set name = replace(concat('The ', name), ', The', '')
            where name like '%chronicles of narnia, the%'
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
