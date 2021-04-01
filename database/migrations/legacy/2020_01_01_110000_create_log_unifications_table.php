<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogUnificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if((DB::select("select EXISTS (SELECT FROM pg_catalog.pg_tables WHERE tablename = 'log_unifications');"))[0]->exists == false) {
            Schema::create(
                'log_unifications',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('type');
                    $table->integer('main_id');
                    $table->json('duplicates_id');
                    $table->integer('created_by');
                    $table->integer('updated_by');
                    $table->boolean('active')->default(true);
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
        Schema::dropIfExists('log_unifications');
    }
}
