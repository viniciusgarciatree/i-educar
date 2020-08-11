<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarCalendarioDiaAnotacaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if((DB::select("select EXISTS (SELECT FROM pg_catalog.pg_tables WHERE schemaname = 'pmieducar' AND tablename = 'calendario_dia_anotacao');"))[0]->exists == false) {
            DB::unprepared(
                '
                SET default_with_oids = true;
                
                CREATE TABLE pmieducar.calendario_dia_anotacao (
                    ref_dia integer NOT NULL,
                    ref_mes integer NOT NULL,
                    ref_ref_cod_calendario_ano_letivo integer NOT NULL,
                    ref_cod_calendario_anotacao integer NOT NULL
                );
                
                ALTER TABLE ONLY pmieducar.calendario_dia_anotacao
                    ADD CONSTRAINT calendario_dia_anotacao_pkey PRIMARY KEY (ref_dia, ref_mes, ref_ref_cod_calendario_ano_letivo, ref_cod_calendario_anotacao);
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
        Schema::dropIfExists('pmieducar.calendario_dia_anotacao');
    }
}
