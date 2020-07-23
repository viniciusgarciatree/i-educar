<?php

namespace App\Http\Controllers;

use App\Jobs\DatabaseToCsvExporter;
use App\Models\Exporter\Export;
use App\Models\Exporter\SocialAssistance;
use App\Models\Exporter\Student;
use App\Models\Exporter\Teacher;
use App\Process;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExportController extends Controller
{
    /**
     * @param Request $request
     *
     * @return View
     */
    public function index(Request $request)
    {
        $this->breadcrumb('Exportações', [
            url('/intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);

        $this->menu(Process::DATA_EXPORT);

        $query = Export::query();

        $query->where('user_id', $request->user()->getKey())
            ->orderByDesc('created_at');

        return view('export.index', [
            'exports' => $query->paginate(),
        ]);
    }

    /**
     * @param Request $request
     * @param Export  $export
     *
     * @return View
     */
    public function form(Request $request, Export $export)
    {
        $this->breadcrumb('Nova Exportação', [
            url('/intranet/educar_configuracoes_index.php') => 'Configurações',
            route('export.index') => 'Exportações',
        ]);

        $this->menu(Process::DATA_EXPORT);

        return view('export.new', [
            'export' => $export,
            'exportation' => $export->getExportByCode(
                $request->query('type', 1)
            ),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function export(Request $request)
    {
        if (empty($request->filled(['agree']))) {
            return redirect()->route('export.form');
        }

        $export = Export::create(
            $this->filter($request)
        );

        $this->dispatch(
            new DatabaseToCsvExporter($export)
        );

        return redirect()->route('export.index');
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    protected function filter(Request $request)
    {
        $data = $request->merge([
            'hash' => md5(time()),
            'user_id' => $request->user()->getKey(),
        ])->only([
            'model', 'fields', 'hash', 'user_id',
        ]);

        $model = $data['model'];

        if ($model === Student::class) {
            $data = $this->filterStudents($request, $data, 'exporter_student');
        }

        if ($model === Teacher::class) {
            $data = $this->filterTeachers($request, $data);
        }

        if ($model === SocialAssistance::class) {
            $data = $this->filterStudents($request, $data, 'exporter_social_assistance');
        }

        return $data;
    }

    /**
     * @param Request $request
     * @param array   $data
     *
     * @return array
     */
    protected function filterStudents(Request $request, $data, $table)
    {
        $data['filename'] = 'alunos.csv';

        if ($status = $request->input('situacao_matricula')) {
            $data['filters'][] = [
                'column' => $table . '.status',
                'operator' => '=',
                'value' => $status,
            ];
        }

        if ($year = $request->input('ano')) {
            $data['filters'][] = [
                'column' => $table . '.year',
                'operator' => '=',
                'value' => intval($year),
            ];
        }

        if ($request->input('ref_cod_escola')) {
            $data['filters'][] = [
                'column' => $table . '.school_id',
                'operator' => 'in',
                'value' => [$request->input('ref_cod_escola')]
            ];
        } elseif ($request->user()->isSchooling()) {
            $data['filters'][] = [
                'column' => $table . '.school_id',
                'operator' => 'in',
                'value' => $request->user()->schools->pluck('cod_escola')->all(),
            ];
        }

        return $data;
    }

    /**
     * @param Request $request
     * @param array   $data
     *
     * @return array
     */
    public function filterTeachers(Request $request, $data)
    {
        $data['filename'] = 'professores.csv';

        if ($year = $request->input('ano')) {
            $data['filters'][] = [
                'column' => 'exporter_teacher.year',
                'operator' => '=',
                'value' => intval($year),
            ];
        }

        if ($request->input('ref_cod_escola')) {
            $data['filters'][] = [
                'column' => 'exporter_teacher.school_id',
                'operator' => 'in',
                'value' => [$request->input('ref_cod_escola')]
            ];
        } elseif ($request->user()->isSchooling()) {
            $data['filters'][] = [
                'column' => 'exporter_teacher.school_id',
                'operator' => 'in',
                'value' => $request->user()->schools->pluck('cod_escola')->all(),
            ];
        }

        return $data;
    }
}
