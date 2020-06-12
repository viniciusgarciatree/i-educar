<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarDisciplinaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.disciplina', function (Blueprint $table) {
            $table->foreign('ref_cod_curso')
               ->references('cod_curso')
               ->on('pmieducar.curso')
               ->onUpdate('restrict')
               ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.disciplina', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_curso']);
        });
    }
}
