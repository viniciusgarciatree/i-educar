<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateComponenteCurricularAnoEscola extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('modules.componente_curricular_ano_escolar', 'carga_horaria_auxiliar')) {
            Schema::table('modules.componente_curricular_ano_escolar', function (Blueprint $table)
            {
                if (Schema::hasColumn('modules.componente_curricular_ano_escolar', 'carga_horaria_auxiliar')) {
                    $table->string('carga_horaria_auxiliar',8)->nullable();
                }
                if (Schema::hasColumn('modules.componente_curricular_ano_escolar', 'aulas_semanais')) {
                    $table->integer('aulas_semanais')->nullable();
                }

                if (Schema::hasColumn('modules.componente_curricular_ano_escolar', 'hora_aula')) {
                    $table->integer('hora_aula')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
