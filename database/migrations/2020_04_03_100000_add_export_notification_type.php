<?php

use App\Models\NotificationType;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class AddExportNotificationType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $results = DB::select('select * from public.notification_type where id = :id', ['id' => NotificationType::EXPORT_STUDENT]);
        if(count($results) ==0) {
            DB::table('public.notification_type')->insert(
                [
                    'id'   => NotificationType::EXPORT_STUDENT,
                    'name' => 'Exportação de dados de alunos'
                ]
            );
        }

        $results = DB::select('select * from public.notification_type where id = :id', ['id' => NotificationType::EXPORT_TEACHER]);
        if(count($results) ==0) {
            DB::table('public.notification_type')->insert(
                [
                    'id'   => NotificationType::EXPORT_TEACHER,
                    'name' => 'Exportação de dados de professores'
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
        DB::table('public.notification_type')->delete(NotificationType::EXPORT_STUDENT);
        DB::table('public.notification_type')->delete(NotificationType::EXPORT_TEACHER);
    }
}
