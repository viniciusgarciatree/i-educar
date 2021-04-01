<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(count(DB::select("select EXISTS (SELECT FROM pg_catalog.pg_tables WHERE schemaname = 'public' AND tablename = 'places');"))[0]=="false") {
            Schema::create(
                'public.places',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('city_id');
                    $table->string('address');
                    $table->string('number')->nullable();
                    $table->string('complement')->nullable();
                    $table->string('neighborhood');
                    $table->string('postal_code');
                    $table->float('latitude')->nullable();
                    $table->float('longitude')->nullable();
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
        Schema::dropIfExists('public.places');
    }
}
