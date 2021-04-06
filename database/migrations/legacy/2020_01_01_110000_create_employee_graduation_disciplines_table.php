<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeGraduationDisciplinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if((DB::select("select EXISTS (SELECT FROM pg_catalog.pg_tables WHERE tablename = 'employee_graduation_disciplines');"))[0]->exists == false) {
            Schema::create(
                'employee_graduation_disciplines',
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
        Schema::dropIfExists('employee_graduation_disciplines');
    }
}
