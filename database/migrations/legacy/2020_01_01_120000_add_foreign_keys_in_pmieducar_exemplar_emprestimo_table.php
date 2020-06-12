<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarExemplarEmprestimoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.exemplar_emprestimo', function (Blueprint $table) {
            $table->foreign('ref_cod_exemplar')
               ->references('cod_exemplar')
               ->on('pmieducar.exemplar')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_cliente')
               ->references('cod_cliente')
               ->on('pmieducar.cliente')
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
        Schema::table('pmieducar.exemplar_emprestimo', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_exemplar']);
            $table->dropForeign(['ref_cod_cliente']);
        });
    }
}
