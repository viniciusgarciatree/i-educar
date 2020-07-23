<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarFaltaAlunoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.falta_aluno', function (Blueprint $table) {
            $table->foreign(['ref_cod_serie', 'ref_cod_escola', 'ref_cod_disciplina'])
               ->references(['ref_ref_cod_serie', 'ref_ref_cod_escola', 'ref_cod_disciplina'])
               ->on('pmieducar.escola_serie_disciplina')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_curso_disciplina')
               ->references('cod_disciplina')
               ->on('pmieducar.disciplina')
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
        Schema::table('pmieducar.falta_aluno', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_serie', 'ref_cod_escola', 'ref_cod_disciplina']);
            $table->dropForeign(['ref_cod_curso_disciplina']);
        });
    }
}
