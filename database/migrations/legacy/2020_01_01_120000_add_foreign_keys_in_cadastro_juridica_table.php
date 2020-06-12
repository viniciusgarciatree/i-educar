<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInCadastroJuridicaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cadastro.juridica', function (Blueprint $table) {
            $table->foreign('idpes_rev')
               ->references('idpes')
               ->on('cadastro.pessoa')
               ->onDelete('set null');

            $table->foreign('idpes_cad')
               ->references('idpes')
               ->on('cadastro.pessoa')
               ->onDelete('set null');

            $table->foreign('idpes')
               ->references('idpes')
               ->on('cadastro.pessoa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cadastro.juridica', function (Blueprint $table) {
            $table->dropForeign(['idpes_rev']);
            $table->dropForeign(['idpes_cad']);
            $table->dropForeign(['idpes']);
        });
    }
}
