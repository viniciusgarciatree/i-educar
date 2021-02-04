<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \Illuminate\Support\Facades\DB;

class AlteraColunaComponenteCurricularAnoEscola extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('modules.componente_curricular_ano_escolar', 'carga_horaria_auxiliar')) {
            Schema::table(
                'modules.componente_curricular_ano_escolar',
                function (Blueprint $table) {
                    DB::statement('ALTER TABLE modules.componente_curricular_ano_escolar ADD COLUMN updated_atn timestamp(0) without time zone;' );
                    DB::statement( "UPDATE modules.componente_curricular_ano_escolar SET updated_atn = updated_at WHERE componente_curricular_id > 0;" );
                    DB::statement("ALTER TABLE modules.componente_curricular_ano_escolar DROP COLUMN updated_at;");
                    DB::statement( "ALTER TABLE modules.componente_curricular_ano_escolar RENAME updated_atn TO updated_at;" );
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
        //
    }
}
