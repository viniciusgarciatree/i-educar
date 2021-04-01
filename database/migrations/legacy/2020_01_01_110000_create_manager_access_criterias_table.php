<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManagerAccessCriteriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if((DB::select("select EXISTS (SELECT FROM pg_catalog.pg_tables WHERE tablename = 'manager_access_criterias');"))[0]->exists == false) {
            Schema::create(
                'manager_access_criterias',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('name');
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
        Schema::dropIfExists('manager_access_criterias');
    }
}
