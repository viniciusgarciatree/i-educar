<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeGraduationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if((DB::select("select EXISTS (SELECT FROM pg_catalog.pg_tables WHERE tablename = 'employee_graduations');"))[0]->exists == false) {
            Schema::create(
                'employee_graduations',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('employee_id')->unsigned();
                    $table->integer('course_id')->unsigned();
                    $table->integer('completion_year');
                    $table->integer('college_id')->unsigned();
                    $table->integer('discipline_id')->unsigned()->nullable();
                    $table->timestamps();
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
        Schema::dropIfExists('employee_graduations');
    }
}
