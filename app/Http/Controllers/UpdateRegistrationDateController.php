<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateRegistrationDateRequest;
use App\Models\LegacyRegistration;
use App\Process;
use App\Services\RegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UpdateRegistrationDateController extends Controller
{
    /**
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $this->breadcrumb('Atualização da data de entrada e enturmação em lote', [
            url('intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);

        $this->menu(Process::UPDATE_REGISTRATION_DATE);

        return view('registration.update-registration-date.index', ['user' => $request->user()]);
    }

    /**
     * Atualiza a data de entrada e enturmação de acordo com o filtro
     *
     * @param UpdateRegistrationDateRequest $request
     * @param RegistrationService $registrationService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(UpdateRegistrationDateRequest $request, RegistrationService $registrationService)
    {
        $query = LegacyRegistration::query();

        if ($request->get('ano')) {
            $query->where('ano', $request->get('ano'));
        }

        if ($request->get('ref_cod_escola')) {
            $query->where('ref_ref_cod_escola', $request->get('ref_cod_escola'));
        }

        if ($request->get('ref_cod_curso')) {
            $query->where('ref_cod_curso', $request->get('ref_cod_curso'));
        }

        if ($request->get('ref_cod_turma')) {
            $schoolClassId = $request->get('ref_cod_turma');
            $query->whereHas('enrollments', function ($enrollmentQuery) use ($schoolClassId) {
                $enrollmentQuery->where('ref_cod_turma', $schoolClassId);
            });
        }

        if ($request->get('ref_cod_serie')) {
            $query->where('ref_ref_cod_serie', $request->get('ref_cod_serie'));
        }

        if ($request->get('situacao')) {
            $query->where('aprovado', $request->get('situacao'));
        }

        $registrations = $query->get();

        DB::beginTransaction();

        $newDate = \DateTime::createFromFormat('d/m/Y', $request->get('nova_data'));

        foreach ($registrations as $registration) {
            $registrationService->updateRegistrationDate($registration, $newDate);
            $registrationService->updateEnrollmentsDate($registration, $newDate);
        }

        //DB::commit();

        return redirect()->route('update-registration-date.index')->with('success', count($registrations) . ' matrículas atualizadas com sucesso.');
    }
}
