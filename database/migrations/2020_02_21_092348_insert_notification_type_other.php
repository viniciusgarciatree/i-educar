<?php

use App\Models\NotificationType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class InsertNotificationTypeOther extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $results = DB::select('select * from public.notification_type where id = :id', ['id' => NotificationType::OTHER]);
        if(count($results) ==0) {
            DB::table('public.notification_type')->insert(
                [
                    'id'   => NotificationType::OTHER,
                    'name' => 'Outros'
                ]
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
        DB::table('public.notification_type')->delete(NotificationType::OTHER);
    }
}
