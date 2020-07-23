<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarDistribuicaoUniformeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.distribuicao_uniforme', function (Blueprint $table) {
            $table->foreign('ref_cod_escola')
               ->references('cod_escola')
               ->on('pmieducar.escola');

            $table->foreign('ref_cod_aluno')
               ->references('cod_aluno')
               ->on('pmieducar.aluno')
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
        Schema::table('pmieducar.distribuicao_uniforme', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_escola']);
            $table->dropForeign(['ref_cod_aluno']);
        });
    }
}
