<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('municipalities', function(Blueprint $table){
            $table->integer('key')->nullable();
        });

        Schema::table('settlements', function(Blueprint $table){
            $table->integer('key')->nullable();
        });

        Schema::table('federal_entities', function(Blueprint $table){
            $table->integer('key')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('municipalities', function(Blueprint $table){
            $table->dropColumn('key');
        });

        Schema::table('settlements', function(Blueprint $table){
            $table->dropColumn('key');
        });

        Schema::table('federal_entities', function(Blueprint $table){
            $table->dropColumn('key');
        });
    }
};
