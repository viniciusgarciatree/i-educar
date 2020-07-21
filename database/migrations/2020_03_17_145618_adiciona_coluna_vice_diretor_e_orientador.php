<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdicionaColunaViceDiretorEOrientador extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('pmieducar.escola', 'qtd_vice_diretor')) {
            Schema::table(
                'pmieducar.escola',
                function (Blueprint $table) {
                    $table->integer('qtd_vice_diretor')->nullable();
                }
            );
        }
        if (Schema::hasColumn('pmieducar.escola', 'qtd_orientador_comunitario')) {
            Schema::table(
                'pmieducar.escola',
                function (Blueprint $table) {
                    $table->integer('qtd_orientador_comunitario')->nullable();
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
        Schema::table('pmieducar.escola', function (Blueprint $table) {
            $table->dropColumn('qtd_vice_diretor');
            $table->dropColumn('qtd_orientador_comunitario');
        });
    }
}
