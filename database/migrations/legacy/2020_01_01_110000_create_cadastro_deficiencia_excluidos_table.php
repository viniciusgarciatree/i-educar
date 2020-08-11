<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCadastroDeficienciaExcluidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if((DB::select("select EXISTS (SELECT FROM pg_catalog.pg_tables WHERE schemaname = 'cadastro' AND tablename = 'deficiencia_excluidos');"))[0]->exists == false) {
            Schema::create(
                'cadastro.deficiencia_excluidos',
                function (Blueprint $table) {
                    $table->integer('cod_deficiencia')->primary();
                    $table->string('nm_deficiencia');
                    $table->integer('deficiencia_educacenso')->nullable();
                    $table->boolean('desconsidera_regra_diferenciada')->nullable();
                    $table->timestamps();
                    $table->softDeletes();
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
        Schema::dropIfExists('cadastro.deficiencia_excluidos');
    }
}
