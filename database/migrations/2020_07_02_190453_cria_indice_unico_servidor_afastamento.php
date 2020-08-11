<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CriaIndiceUnicoServidorAfastamento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('servidor_afastamento', function (Blueprint $table) {
            $table->unique([
                'ref_cod_servidor',
                'sequencial',
                'ref_ref_cod_instituicao',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('servidor_afastamento', function (Blueprint $table) {
            $table->dropUnique([
                'ref_cod_servidor',
                'sequencial',
                'ref_ref_cod_instituicao',
            ]);
        });
    }
}
