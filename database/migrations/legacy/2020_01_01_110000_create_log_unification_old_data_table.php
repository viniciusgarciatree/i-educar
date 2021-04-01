<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogUnificationOldDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if((DB::select("select EXISTS (SELECT FROM pg_catalog.pg_tables WHERE tablename = 'log_unification_old_data');"))[0]->exists == false) {
            Schema::create(
                'log_unification_old_data',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('unification_id')->unsigned();
                    $table->string('table');
                    $table->json('keys');
                    $table->json('old_data');
                }
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_unification_old_data');
    }
}
