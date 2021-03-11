<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesEtapasCursoEducacensoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if((DB::select("select EXISTS (SELECT FROM pg_catalog.pg_tables WHERE schemaname = 'modules' AND tablename = 'etapas_curso_educacenso');"))[0]->exists == false) {
            DB::unprepared(
                '
                SET default_with_oids = false;
                
                CREATE TABLE modules.etapas_curso_educacenso (
                    etapa_id integer NOT NULL,
                    curso_id integer NOT NULL
                );
                
                ALTER TABLE ONLY modules.etapas_curso_educacenso
                    ADD CONSTRAINT etapas_curso_educacenso_pk PRIMARY KEY (etapa_id, curso_id);
            '
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
        Schema::dropIfExists('modules.etapas_curso_educacenso');
    }
}
