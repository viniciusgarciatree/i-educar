<?php

use App\Models\NotificationType;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class InsertNotificationTypeTransferencia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $results = DB::select('select * from public.notification_type where id = :id', ['id' => NotificationType::TRANSFER]);
        if(count($results) ==0) {
            DB::table('public.notification_type')->insert(
                [
                    'id'   => NotificationType::TRANSFER,
                    'name' => 'Transferência'
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
