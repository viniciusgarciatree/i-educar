<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDesenvolvidoPorMenus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('menus', 'desenvolvido_por')) {
            Schema::table('menus', function (Blueprint $table)
            {
                $table->enum('desenvolvido_por', ['portabilis', 'versa'])->default('portabilis');
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
