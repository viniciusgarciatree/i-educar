<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if((DB::select("select EXISTS (SELECT FROM pg_catalog.pg_tables WHERE tablename = 'settings');"))[0]->exists == false) {
            Schema::create(
                'settings',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('key')->unique();
                    $table->text('value')->nullable();
                    $table->string('type')->default('string');
                    $table->string('description')->nullable();
                    $table->timestamps();
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
        Schema::dropIfExists('settings');
    }
}
