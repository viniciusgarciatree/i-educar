<?php

use App\Models\LegacyEvaluationRule;
use App\Models\LegacyInstitution;
use App\Models\LegacyRegistration;
use App\Models\LegacyRemedialRule;
use App\Models\LegacySchoolClass;
use Cocur\Slugify\Slugify;
use iEducar\Modules\Stages\Exceptions\MissingStagesException;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

require_once 'Avaliacao/Model/NotaComponenteDataMapper.php';
require_once 'Avaliacao/Model/NotaGeralDataMapper.php';
require_once 'Avaliacao/Service/Boletim.php';
require_once 'App/Model/MatriculaSituacao.php';
require_once 'RegraAvaliacao/Model/TipoPresenca.php';
require_once 'RegraAvaliacao/Model/TipoParecerDescritivo.php';

require_once 'include/pmieducar/clsPmieducarTurma.inc.php';
require_once 'include/pmieducar/clsPmieducarMatricula.inc.php';
require_once 'include/pmieducar/clsPmieducarBloqueioLancamentoFaltasNotas.inc.php';
require_once 'include/modules/clsModulesAuditoriaNota.inc.php';
require_once 'include/modules/clsModulesNotaExame.inc.php';

require_once 'Portabilis/Controller/ApiCoreController.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/Object/Utils.php';

class DiarioApiController extends ApiCoreController
{
    protected $_dataMapper = 'Avaliacao_Model_NotaComponenteDataMapper';
    protected $_processoAp = 642;
    protected $_currentMatriculaId;

    protected function validatesValueOfAttValueIsInOpcoesNotas()
    {
        return true;
    }

    protected function validatesCanChangeDiarioForAno()
    {
        $escola = App_Model_IedFinder::getEscola($this->getRequest()->escola_id);

        $ano = new clsPmieducarEscolaAnoLetivo();
        $ano->ref_cod_escola = $this->getRequest()->escola_id;
        $ano->ano = $this->getRequest()->ano;
        $ano = $ano->detalhe();

        $anoLetivoEncerrado = is_array($ano) && count($ano) > 0 &&
            $ano['ativo'] == 1 && $ano['andamento'] == 2;

        if ($escola['bloquear_lancamento_diario_anos_letivos_encerrados'] == '1' && $anoLetivoEncerrado) {
            $this->messenger->append("O ano letivo '{$this->getRequest()->ano}' está encerrado, esta escola está configurada para não permitir alterar o diário de anos letivos encerrados.");
            return false;
        }

        $objBloqueioAnoLetivo = new clsPmieducarBloqueioAnoLetivo($this->getRequest()->instituicao_id, $this->getRequest()->ano);
        $bloqueioAnoLetivo = $objBloqueioAnoLetivo->detalhe();

        if ($bloqueioAnoLetivo) {
            $dataAtual = strtotime(date("Y-m-d"));
            $data_inicio = strtotime($bloqueioAnoLetivo['data_inicio']);
            $data_fim = strtotime($bloqueioAnoLetivo['data_fim']);

            if ($dataAtual < $data_inicio || $dataAtual > $data_fim) {
                $this->messenger->append("O lançamento de notas nessa instituição está bloqueado nesta data.");
                return false;
            }
        }

        return true;
    }

    protected function validatesRegraAvaliacaoHasNota()
    {
        $isValid = $this->serviceBoletim()->getRegra()->get('tipoNota') != RegraAvaliacao_Model_Nota_TipoValor::NENHUM;

        if (!$isValid) {
            $this->messenger->append("Nota não lançada, pois a regra de avaliação não utiliza nota.");
        }

        return $isValid;
    }

    protected function validatesRegraAvaliacaoHasFormulaRecuperacao()
    {
        $isValid = $this->getRequest()->etapa != 'Rc' ||
        !is_null($this->serviceBoletim()->getRegra()->formulaRecuperacao);

        if (!$isValid) {
            $this->messenger->append("Nota de recuperação não lançada, pois a fórmula de recuperação não possui fórmula de recuperação.");
        }

        return $isValid;
    }

    protected function validatesRegraAvaliacaoHasFormulaRecuperacaoWithTypeRecuperacao()
    {
        $isValid = $this->getRequest()->etapa != 'Rc' ||
            ($this->serviceBoletim()->getRegra()->formulaRecuperacao->get('tipoFormula') ==
            FormulaMedia_Model_TipoFormula::MEDIA_RECUPERACAO);

        if (!$isValid) {
            $this->messenger->append("Nota de recuperação não lançada, pois a fórmula de recuperação é diferente do tipo média recuperação.");
        }

        return $isValid;
    }

    protected function validatesPreviousNotasHasBeenSet()
    {
        $etapaId = $this->getRequest()->etapa;
        $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        $serviceBoletim = $this->serviceBoletim();

        try {
            return $serviceBoletim->verificaNotasLancadasNasEtapasAnteriores(
                $etapaId, $componenteCurricularId
            );
        } catch (MissingStagesException $exception) {
            $this->messenger->append($exception->getMessage());
            $this->appendResponse('error', [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'extra' => $exception->getExtraInfo(),
            ]);
        } catch (Exception $e) {
            $this->messenger->append($e->getMessage());
        }

        return false;
    }

    protected function validatesPreviousFaltasHasBeenSet()
    {
        $etapaId = $this->getRequest()->etapa;
        $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        $serviceBoletim = $this->serviceBoletim();

        try {
            return $serviceBoletim->verificaFaltasLancadasNasEtapasAnteriores(
                $etapaId, $componenteCurricularId
            );
        } catch (Exception $e) {
            $this->messenger->append($e->getMessage());
        }

        return false;
    }

    // post/ delete parecer validations

    protected function validatesEtapaParecer()
    {
        $isValid = false;
        $etapa = $this->getRequest()->etapa;

        $tiposParecerAnual = array(RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE,
            RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL);

        $parecerAnual = in_array($this->serviceBoletim()->getRegra()->get('parecerDescritivo'),
            $tiposParecerAnual);

        if ($parecerAnual && $etapa != 'An') {
            $this->messenger->append("Valor inválido para o atributo 'etapa', é esperado 'An' e foi recebido '{$etapa}'.");
        } elseif (!$parecerAnual && $etapa == 'An') {
            $this->messenger->append("Valor inválido para o atributo 'etapa', é esperado um valor diferente de 'An'.");
        } else {
            $isValid = true;
        }

        return $isValid;
    }

    protected function validatesPresenceOfComponenteCurricularIdIfParecerComponente()
    {
        $tiposParecerComponente = array(RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE,
            RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE);

        $parecerPorComponente = in_array($this->serviceBoletim()->getRegra()->get('parecerDescritivo'),
            $tiposParecerComponente);

        return (!$parecerPorComponente) || $this->validatesPresenceOf('componente_curricular_id');
    }

    // post parecer validations

    protected function validatesRegraAvaliacaoHasParecer()
    {
        $tpParecer = $this->serviceBoletim()->getRegra()->get('parecerDescritivo');
        $isValid = $tpParecer != RegraAvaliacao_Model_TipoParecerDescritivo::NENHUM;

        if (!$isValid) {
            $this->messenger->append("Parecer descritivo não lançado, pois a regra de avaliação não utiliza parecer.");
        }

        return $isValid;
    }

    // delete nota validations

    protected function validatesInexistenceOfNotaExame()
    {
        $isValid = true;

        if ($this->getRequest()->etapa != 'Rc') {
            $notaExame = $this->getNotaAtual($etapa = 'Rc');
            $isValid = empty($notaExame);

            if (!$isValid) {
                $this->messenger->append('Nota da matrícula ' . $this->getRequest()->matricula_id . ' somente pode ser removida, após remover nota do exame.', 'error');
            }

        }

        return $isValid;
    }

    protected function validatesInexistenceNotasInNextEtapas()
    {
        $etapasComNota = array();

        if (is_numeric($this->getRequest()->etapa)) {
            $etapas = $this->serviceBoletim()->getOption('etapas');
            $etapa = $this->getRequest()->etapa + 1;

            for ($etapa; $etapa <= $etapas; $etapa++) {
                $nota = $this->getNotaAtual($etapa);

                if (!empty($nota)) {
                    $etapasComNota[] = $etapa;
                }

            }

            if (!empty($etapasComNota)) {
                $msg = "Nota somente pode ser removida, após remover as notas lançadas nas etapas posteriores: " .
                join(', ', $etapasComNota) . '.';
                $this->messenger->append($msg, 'error');
            }
        }

        return empty($etapasComNota);
    }

    // delete falta validations

    protected function validatesInexistenceFaltasInNextEtapas()
    {
        $etapasComFalta = array();

        if (is_numeric($this->getRequest()->etapa)) {
            $etapas = $this->serviceBoletim()->getOption('etapas');
            $etapa = $this->getRequest()->etapa + 1;

            for ($etapa; $etapa <= $etapas; $etapa++) {
                $falta = $this->getFaltaAtual($etapa);

                if (!empty($falta)) {
                    $etapasComFalta[] = $etapa;
                }

            }

            if (!empty($etapasComFalta)) {
                $this->messenger->append("Falta somente pode ser removida, após remover as faltas lançadas nas etapas posteriores: " . join(', ', $etapasComFalta) . '.', 'error');
            }

        }

        return empty($etapasComFalta);
    }

    protected function validatesPresenceOfMatriculaIdOrComponenteCurricularId()
    {
        if (empty($this->getRequest()->componente_curricular_id) && empty($this->getRequest()->matricula_id)) {
            $this->messenger->append('É necessário receber matricula_id ou componente_curricular_id.', 'error');
            return false;
        }

        return true;
    }

    protected function validatesPeriodoLancamentoFaltasNotas($showMessage = true)
    {

        $bloqueioLancamentoFaltasNotas = new clsPmieducarBloqueioLancamentoFaltasNotas(null,
            $this->getRequest()->ano_escolar,
            $this->getRequest()->escola_id,
            $this->getRequest()->etapa);

        $bloquearLancamento = $bloqueioLancamentoFaltasNotas->verificaPeriodo();

        $user = $this->getSession()->id_pessoa;
        $processoAp = 999849;
        $obj_permissao = new clsPermissoes();

        $permissaoLancamento = $obj_permissao->permissao_cadastra($processoAp, $user, 7);

        if ($bloquearLancamento || $permissaoLancamento) {
            return true;
        }

        if ($showMessage) {
            $this->messenger->append('Não é permitido realizar esta alteração fora do período de lançamento de notas/faltas.', 'error');
        }

        return false;
    }

    // responders validations

    protected function canGetMatriculas()
    {
        return $this->validatesPresenceOf(array('instituicao_id',
            'escola_id',
            'curso_id',
            'curso_id',
            'serie_id',
            'turma_id',
            'ano',
            'etapa')) &&
        $this->validatesPresenceOfMatriculaIdOrComponenteCurricularId() &&
        $this->validatesCanChangeDiarioForAno();
    }

    protected function canPost()
    {
        return $this->validatesPresenceOf('etapa') &&
        $this->validatesPresenceOf('matricula_id') &&
        $this->canChange() &&
        $this->validatesPeriodoLancamentoFaltasNotas();
    }

    protected function canPostNota()
    {
        return $this->canPost() &&
        $this->validatesValueOfAttValueIsInOpcoesNotas(false) &&
        $this->validatesPresenceOf('componente_curricular_id') &&
        $this->validatesRegraAvaliacaoHasNota() &&
        $this->validatesRegraAvaliacaoHasFormulaRecuperacao() &&
        $this->validatesRegraAvaliacaoHasFormulaRecuperacaoWithTypeRecuperacao() &&
        $this->validatesPreviousNotasHasBeenSet();
    }

    protected function canPostNotaGeral()
    {
        return $this->canPost();
    }

    protected function canPostFalta()
    {
        return $this->canPost() &&
        $this->validatesPreviousFaltasHasBeenSet();
    }

    protected function canPostParecer()
    {

        return $this->canPost() &&
        $this->validatesPresenceOf('att_value') &&
        $this->validatesEtapaParecer() &&
        $this->validatesRegraAvaliacaoHasParecer() &&
        $this->validatesPresenceOfComponenteCurricularIdIfParecerComponente();
    }

    protected function canDelete()
    {
        return $this->validatesPresenceOf('etapa');
    }

    protected function canDeleteNota()
    {
        return $this->canDelete() &&
        $this->validatesPresenceOf('componente_curricular_id') &&
        $this->validatesInexistenceOfNotaExame() &&
        $this->validatesInexistenceNotasInNextEtapas() &&
        $this->validatesPeriodoLancamentoFaltasNotas();
    }

    protected function canDeleteFalta()
    {
        return $this->canDelete() &&
        $this->validatesInexistenceFaltasInNextEtapas() &&
        $this->validatesPeriodoLancamentoFaltasNotas();
    }

    protected function canDeleteParecer()
    {
        return $this->canDelete() &&
        $this->validatesEtapaParecer() &&
        $this->validatesPresenceOfComponenteCurricularIdIfParecerComponente();
    }

    // responders

    // post
    /**
     * @throws CoreExt_Exception
     */
    protected function postNota()
    {
        if ($this->canPostNota()) {
            $nota = urldecode($this->getRequest()->att_value);
            $notaOriginal = urldecode($this->getRequest()->nota_original);
            $etapa = $this->getRequest()->etapa;

            $nota = $this->serviceBoletim()->calculateStageScore($etapa, $nota, null);

            $array_nota = array(
                'componenteCurricular' => $this->getRequest()->componente_curricular_id,
                'nota' => $nota,
                'etapa' => $etapa,
                'notaOriginal' => $notaOriginal,
            );

            if ($_notaAntiga = $this->serviceBoletim()->getNotaComponente($this->getRequest()->componente_curricular_id, $this->getRequest()->etapa)) {
                $array_nota['notaRecuperacaoParalela'] = $_notaAntiga->notaRecuperacaoParalela;
                $array_nota['notaRecuperacaoEspecifica'] = $_notaAntiga->notaRecuperacaoEspecifica;
            }

            $nota = new Avaliacao_Model_NotaComponente($array_nota);
            $this->serviceBoletim()->addNota($nota);
            $this->trySaveServiceBoletim();
            $this->inserirAuditoriaNotas($_notaAntiga, $nota);
            $this->messenger->append('Nota matrícula ' . $this->getRequest()->matricula_id . ' alterada com sucesso.', 'success');
        }

        $this->appendResponse('should_show_recuperacao_especifica', $this->shouldShowRecuperacaoEspecifica());
        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('situacao', $this->getSituacaoComponente());
        $this->appendResponse('nota_necessaria_exame', $notaNecessariaExame = $this->getNotaNecessariaExame($this->getRequest()->componente_curricular_id));
        $this->appendResponse('media', round($this->getMediaAtual($this->getRequest()->componente_curricular_id), 3));
        $this->appendResponse('media_arredondada', $this->getMediaArredondadaAtual($this->getRequest()->componente_curricular_id));

        if (!empty($notaNecessariaExame) && in_array($this->getSituacaoComponente(), array('Em exame', 'Aprovado após exame', 'Retido'))) {
            $this->createOrUpdateNotaExame($this->getRequest()->matricula_id, $this->getRequest()->componente_curricular_id, $notaNecessariaExame);
        } else {
            $this->deleteNotaExame($this->getRequest()->matricula_id, $this->getRequest()->componente_curricular_id);
        }

    }

    protected function postNotaGeral()
    {
        if ($this->canPostNotaGeral()) {
            $notaGeral = urldecode($this->getRequest()->att_value);
            $nota = new Avaliacao_Model_NotaGeral(array(
                'etapa' => $this->getRequest()->etapa,
                'nota' => $notaGeral));

            $this->serviceBoletim()->updateMediaGeral(0, $this->getRequest()->etapa);
            $this->serviceBoletim()->addNotaGeral($nota);
            $this->trySaveServiceBoletim();
            $this->messenger->append('Nota geral da matrícula ' . $this->getRequest()->matricula_id . ' alterada com sucesso.', 'success');
        }

        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('situacao', $this->getSituacaoComponente($this->getRequest()->componente_curricular_id));
        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
        $this->appendResponse('media', $this->getMediaAtual($this->getRequest()->componente_curricular_id));
        $this->appendResponse('media_arredondada', $this->getMediaArredondadaAtual($this->getRequest()->componente_curricular_id));
    }

    protected function postMedia()
    {
        if ($this->canPostMedia()) {
            $mediaLancada = urldecode($this->getRequest()->att_value);
            $componenteCurricular = $this->getRequest()->componente_curricular_id;
            $etapa = $this->getRequest()->etapa;

            $this->serviceBoletim()->updateMediaComponente($mediaLancada, $componenteCurricular, $etapa, true);
            $this->messenger->append('Média da matrícula ' . $this->getRequest()->matricula_id . ' alterada com sucesso.', 'success');
            $this->appendResponse('situacao', $this->getSituacaoComponente($this->getRequest()->componente_curricular_id));
            $this->appendResponse('media', $this->getMediaAtual($this->getRequest()->componente_curricular_id));
            $this->appendResponse('media_arredondada', $this->getMediaArredondadaAtual($this->getRequest()->componente_curricular_id));
        } else {
            $this->messenger->append('Usuário não possui permissão para alterar a média do aluno.', 'error');
        }

        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
    }

    protected function postMediaDesbloqueia() {
        if ($this->canPostMedia()) {
            $componenteCurricular = $this->getRequest()->componente_curricular_id;

            if ($this->serviceBoletim()->unlockMediaComponente($componenteCurricular)) {
                $this->messenger->append('Média desbloqueada com sucesso.', 'success');
            } else {
                $this->messenger->append('Ocorreu um erro ao desbloquear a média. Tente novamente.', 'error');
            }
        }
    }

    protected function deleteMedia()
    {
        if ($this->canDeleteMedia()) {

            $media = $this->getMediaAtual();
            if (empty($media) && !is_numeric($media)) {
                $this->messenger->append('Média matrícula ' . $this->getRequest()->matricula_id . ' inexistente ou já removida.', 'notice');
            } else {
                $this->serviceBoletim()->updateMediaComponente(0, $this->getRequest()->componente_curricular_id, $this->getRequest()->etapa);
                $this->messenger->append('Média matrícula ' . $this->getRequest()->matricula_id . ' removida com sucesso.', 'success');
            }
        }

        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('situacao', $this->getSituacaoComponente());
    }

    protected function canPostMedia()
    {
        return $this->canPostSituacaoAndNota();
    }

    protected function canDeleteMedia()
    {
        return true;
    }

    /**
     * @throws CoreExt_Exception
     */
    protected function postNotaRecuperacaoParalela()
    {
        if ($this->canPostNota()) {
            $notaOriginal = $this->getNotaOriginal();
            $notaRecuperacaoParalela = urldecode($this->getRequest()->att_value);
            $etapa = $this->getRequest()->etapa;

            $notaNova = $this->serviceBoletim()->calculateStageScore(
                $etapa, $notaOriginal, $notaRecuperacaoParalela
            );

            $nota = new Avaliacao_Model_NotaComponente(array(
                'componenteCurricular' => $this->getRequest()->componente_curricular_id,
                'etapa' => $etapa,
                'nota' => $notaNova,
                'notaRecuperacaoParalela' => $notaRecuperacaoParalela,
                'notaOriginal' => $notaOriginal)
            );

            $this->serviceBoletim()->addNota($nota);
            $this->trySaveServiceBoletim();
            $this->messenger->append('Nota de recuperação da matrícula ' . $this->getRequest()->matricula_id . ' alterada com sucesso.', 'success');
        }

        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('situacao', $this->getSituacaoComponente());
        $this->appendResponse('nota_necessaria_exame', $notaNecessariaExame = $this->getNotaNecessariaExame($this->getRequest()->componente_curricular_id));
        $this->appendResponse('nota_nova', ($notaNova > $notaOriginal ? $notaNova : null));
        $this->appendResponse('media', $this->getMediaAtual($this->getRequest()->componente_curricular_id));
        $this->appendResponse('media_arredondada', $this->getMediaArredondadaAtual($this->getRequest()->componente_curricular_id));

        if (!empty($notaNecessariaExame) && in_array($this->getSituacaoComponente(), array('Em exame', 'Aprovado após exame', 'Retido'))) {
            $this->createOrUpdateNotaExame($this->getRequest()->matricula_id, $this->getRequest()->componente_curricular_id, $notaNecessariaExame);
        } else {
            $this->deleteNotaExame($this->getRequest()->matricula_id, $this->getRequest()->componente_curricular_id);
        }

    }

    protected function postNotaRecuperacaoEspecifica()
    {
        if ($this->canPostNota()) {
            $notaOriginal = $this->getNotaOriginal();
            $notaRecuperacaoParalela = urldecode($this->getRequest()->att_value);

            $nota = new Avaliacao_Model_NotaComponente(array(
                'componenteCurricular' => $this->getRequest()->componente_curricular_id,
                'etapa' => $this->getRequest()->etapa,
                'nota' => $notaOriginal,
                'notaRecuperacaoEspecifica' => $notaRecuperacaoParalela,
                'notaOriginal' => $notaOriginal));

            $this->serviceBoletim()->addNota($nota);
            $this->trySaveServiceBoletim();
            $this->messenger->append('Nota de recuperação da matrícula ' . $this->getRequest()->matricula_id . ' alterada com sucesso.', 'success');
        }

        // Se está sendo lançada nota de recuperação, obviamente o campo deve ser visível
        $this->appendResponse('should_show_recuperacao_especifica', true);
        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('situacao', $this->getSituacaoComponente());
        $this->appendResponse('nota_necessaria_exame', $notaNecessariaExame = $this->getNotaNecessariaExame($this->getRequest()->componente_curricular_id));
        $this->appendResponse('media', $this->getMediaAtual($this->getRequest()->componente_curricular_id));
        $this->appendResponse('media_arredondada', $this->getMediaArredondadaAtual($this->getRequest()->componente_curricular_id));

        if (!empty($notaNecessariaExame) && in_array($this->getSituacaoComponente(), array('Em exame', 'Aprovado após exame', 'Retido'))) {
            $this->createOrUpdateNotaExame($this->getRequest()->matricula_id, $this->getRequest()->componente_curricular_id, $notaNecessariaExame);
        } else {
            $this->deleteNotaExame($this->getRequest()->matricula_id, $this->getRequest()->componente_curricular_id);
        }

    }

    // TODO mover validacao para canPostFalta
    protected function postFalta()
    {

        $canPost = $this->canPostFalta();
        if ($canPost && $this->serviceBoletim()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
            $canPost = $this->validatesPresenceOf('componente_curricular_id');
        }

        if ($canPost) {
            if ($this->serviceBoletim()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
                $falta = $this->getFaltaComponente();
            } elseif ($this->serviceBoletim()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::GERAL) {
                $falta = $this->getFaltaGeral();
            }

            $this->serviceBoletim()->addFalta($falta);
            $this->trySaveServiceBoletimFaltas();
            $this->messenger->append('Falta matrícula ' . $this->getRequest()->matricula_id . ' alterada com sucesso.', 'success');
        }

        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('situacao', $this->getSituacaoComponente());
        $this->appendResponse('should_show_recuperacao_especifica', $this->shouldShowRecuperacaoEspecifica());
    }

    protected function postParecer()
    {

        if ($this->canPostParecer()) {
            $tpParecer = $this->serviceBoletim()->getRegra()->get('parecerDescritivo');
            $cnsParecer = RegraAvaliacao_Model_TipoParecerDescritivo;

            if ($tpParecer == $cnsParecer::ETAPA_COMPONENTE || $tpParecer == $cnsParecer::ANUAL_COMPONENTE) {
                $parecer = $this->getParecerComponente();
            } else {
                $parecer = $this->getParecerGeral();
            }

            $this->serviceBoletim()->addParecer($parecer);
            $this->trySaveServiceBoletim();
            $this->messenger->append('Parecer descritivo matricula ' . $this->getRequest()->matricula_id . ' alterado com sucesso.', 'success');
        }

        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('situacao', $this->getSituacaoComponente());
    }

    // delete

    protected function deleteNota()
    {
        if ($this->canDeleteNota()) {

            $nota = $this->getNotaAtual();
            if (empty($nota) && !is_numeric($nota)) {
                $this->messenger->append('Nota matrícula ' . $this->getRequest()->matricula_id . ' inexistente ou já removida.', 'notice');
            } else {
                $_notaAntiga = $this->serviceBoletim()->getNotaComponente($this->getRequest()->componente_curricular_id, $this->getRequest()->etapa);
                $this->serviceBoletim()->deleteNota($this->getRequest()->etapa, $this->getRequest()->componente_curricular_id);
                $this->inserirAuditoriaNotas($_notaAntiga, $nota);
                $this->messenger->append('Nota matrícula ' . $this->getRequest()->matricula_id . ' removida com sucesso.', 'success');
            }
        }

        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('situacao', $this->getSituacaoComponente());
        $this->appendResponse('media', $this->getMediaAtual($this->getRequest()->componente_curricular_id));
        $this->appendResponse('media_arredondada', $this->getMediaArredondadaAtual($this->getRequest()->componente_curricular_id));
    }

    protected function deleteNotaRecuperacaoParalela()
    {
        if ($this->canDeleteNota()) {
            $notaOriginal = $this->getNotaOriginal();
            $notaAtual = $this->getNotaAtual();
            $nota = new Avaliacao_Model_NotaComponente(array(
                'componenteCurricular' => $this->getRequest()->componente_curricular_id,
                'etapa' => $this->getRequest()->etapa,
                'nota' => $notaOriginal,
                'notaRecuperacaoEspecifica' => null,
                'notaOriginal' => $notaOriginal));

            $this->serviceBoletim()->addNota($nota);
            $this->trySaveServiceBoletim();
            $this->messenger->append('Nota de recuperação da matrícula ' . $this->getRequest()->matricula_id . ' excluída com sucesso.', 'success');

            $this->appendResponse('situacao', $this->getSituacaoComponente());
            $this->appendResponse('nota_original', $notaOriginal);
            $this->appendResponse('media', $this->getMediaAtual($this->getRequest()->componente_curricular_id));
            $this->appendResponse('media_arredondada', $this->getMediaArredondadaAtual($this->getRequest()->componente_curricular_id));
        }

        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
    }

    protected function deleteNotaRecuperacaoEspecifica()
    {
        if ($this->canDeleteNota()) {
            $notaOriginal = $this->getNotaOriginal();
            $notaAtual = $this->getNotaAtual();
            $nota = new Avaliacao_Model_NotaComponente(array(
                'componenteCurricular' => $this->getRequest()->componente_curricular_id,
                'etapa' => $this->getRequest()->etapa,
                'nota' => $notaOriginal,
                'notaRecuperacaoParalela' => null,
                'notaOriginal' => $notaOriginal));

            $this->serviceBoletim()->addNota($nota);
            $this->trySaveServiceBoletim();
            $this->messenger->append('Nota de recuperação da matrícula ' . $this->getRequest()->matricula_id . ' excluída com sucesso.', 'success');

            $this->appendResponse('situacao', $this->getSituacaoComponente());
            $this->appendResponse('nota_original', $notaOriginal);
            $this->appendResponse('media', $this->getMediaAtual($this->getRequest()->componente_curricular_id));
            $this->appendResponse('media_arredondada', $this->getMediaArredondadaAtual($this->getRequest()->componente_curricular_id));
        }

        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
    }

    protected function deleteFalta()
    {
        $canDelete = $this->canDeleteFalta();
        $cnsPresenca = RegraAvaliacao_Model_TipoPresenca;
        $tpPresenca = $this->serviceBoletim()->getRegra()->get('tipoPresenca');

        if ($canDelete && $tpPresenca == $cnsPresenca::POR_COMPONENTE) {
            $canDelete = $this->validatesPresenceOf('componente_curricular_id');
            $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        } else {
            $componenteCurricularId = null;
        }

        if ($canDelete && is_null($this->getFaltaAtual())) {
            $this->messenger->append('Falta matrícula ' . $this->getRequest()->matricula_id . ' inexistente ou já removida.', 'notice');
        } elseif ($canDelete) {
            $this->serviceBoletim()->deleteFalta($this->getRequest()->etapa, $componenteCurricularId);
            $this->trySaveServiceBoletimFaltas();
            $this->messenger->append('Falta matrícula ' . $this->getRequest()->matricula_id . ' removida com sucesso.', 'success');
        }

        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('situacao', $this->getSituacaoComponente());
    }

    protected function deleteParecer()
    {
        if ($this->canDeleteParecer()) {
            $parecerAtual = $this->getParecerAtual();

            if ((is_null($parecerAtual) || $parecerAtual == '')) {
                $this->messenger->append('Parecer descritivo matrícula ' . $this->getRequest()->matricula_id . ' inexistente ou já removido.', 'notice');
            } else {
                $tpParecer = $this->serviceBoletim()->getRegra()->get('parecerDescritivo');
                $cnsParecer = RegraAvaliacao_Model_TipoParecerDescritivo;

                if ($tpParecer == $cnsParecer::ANUAL_COMPONENTE || $tpParecer == $cnsParecer::ETAPA_COMPONENTE) {
                    $this->serviceBoletim()->deleteParecer($this->getRequest()->etapa, $this->getRequest()->componente_curricular_id);
                } else {
                    // FIXME #parameters
                    $this->serviceBoletim()->deleteParecer($this->getRequest()->etapa, null);
                }

                $this->trySaveServiceBoletim();
                $this->messenger->append('Parecer descritivo matrícula ' . $this->getRequest()->matricula_id . ' removido com sucesso.', 'success');
            }
        }

        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('situacao', $this->getSituacaoComponente());
    }

    protected function deleteNotaGeral()
    {
        $this->serviceBoletim()->updateMediaGeral(0, $this->getRequest()->etapa);
        $this->serviceBoletim()->deleteNotaGeral($this->getRequest()->etapa);

        $this->trySaveServiceBoletim();

        $this->messenger->append('Nota geral da matrícula ' . $this->getRequest()->matricula_id . ' removida com sucesso.', 'success');
        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('situacao', $this->getSituacaoComponente());
    }

    // get

    protected function getRelocationDate()
    {
        /** @var LegacyInstitution $institution */
        $institution = app(LegacyInstitution::class);

        return $institution->relocation_date;
    }

    protected function getMatriculas()
    {
        $regras = $matriculas = [];

        if ($this->canGetMatriculas()) {
            /** @var LegacySchoolClass $schoolClass */
            $schoolClass = LegacySchoolClass::query()
                ->with([
                    'enrollments' => function ($query) {
                        /** @var Builder $query */
                        $query->when($this->getRequest()->matricula_id, function ($query) {
                            $query->where('ref_cod_matricula', $this->getRequest()->matricula_id);
                        });
                        $query->where(function ($query) {
                            $relocationDate = $this->getRelocationDate();

                            /** @var Builder $query */
                            $query->where('ativo', 1);
                            $query->when($relocationDate, function ($query) use ($relocationDate) {
                                /** @var Builder $query */
                                $query->orWhere(function ($query) use ($relocationDate) {
                                    /** @var Builder $query */
                                    $query->where('data_exclusao', '>', $relocationDate);
                                    $query->where(function ($query) {
                                        /** @var Builder $query */
                                        $query->orWhere('transferido', true);
                                        $query->orWhere('remanejado', true);
                                        $query->orWhere('reclassificado', true);
                                        $query->orWhere('abandono', true);
                                        $query->orWhere('falecido', true);
                                    });
                                });
                            });
                        });
                        $query->with([
                            'registration' => function ($query) {
                                $query->with([
                                    'student' => function ($query) {
                                        $query->with([
                                            'person' => function ($query) {
                                                $query->withCount('considerableDeficiencies');
                                            }
                                        ]);
                                    }
                                ]);
                                $query->with('dependencies');
                            }
                        ]);

                        // Pega a última enturmação na turma

                        $query->whereRaw(
                            '
                            sequencial = (
                                SELECT max(sequencial)
                                FROM pmieducar.matricula_turma mt
                                WHERE mt.ref_cod_turma = matricula_turma.ref_cod_turma
                                AND mt.ref_cod_matricula = matricula_turma.ref_cod_matricula
                            )
                            '
                        );

                        $query->whereHas('registration', function ($query) {
                            $query->whereHas('student', function ($query) {
                                $query->where('ativo', 1);
                            });
                        });

                        $query->where('ativo', 1);
                    },
                ])
                ->whereKey($this->getRequest()->turma_id)
                ->firstOrFail();

            // Ordena as enturmações pelo sequencial de fechamento e o nome da
            // pessoa conforme comportamento do código anterior.

            $enrollments = $schoolClass->enrollments->sortBy(function ($enrollment) {
                return Str::slug($enrollment->registration->student->person->name);
            })->sortBy(function ($enrollment) {
                return $enrollment->sequencial_fechamento;
            });

            // Pega a regra de avaliação da turma e busca no banco de dados
            // suas tabelas de arredondamento (numérica e conceitual), valores
            // de arredondamento para as duas tabelas e regras de recuperação.

            $evaluationRule = $schoolClass->getEvaluationRule();

            $evaluationRule->load('roundingTable.roundingValues');
            $evaluationRule->load('conceptualRoundingTable.roundingValues');
            $evaluationRule->load('remedialRules');

            // Caso a regra de avaliação possua uma regra diferenciada para
            // alunos com deficiência, também irá buscar no banco de dados por
            // suas tabelas de arredondamento (numérica e conceitual), valores
            // de arredondamento para as duas tabelas e regras de recuperação.

            if ($deficiencyEvaluationRule = $evaluationRule->deficiencyEvaluationRule) {
                $deficiencyEvaluationRule->load('roundingTable.roundingValues');
                $deficiencyEvaluationRule->load('conceptualRoundingTable.roundingValues');
                $deficiencyEvaluationRule->load('remedialRules');
            }

            foreach ($enrollments as $enrollment) {
                /*** @var LegacyRegistration $registration */
                $registration = $enrollment->registration;
                $student = $registration->student;
                $person = $student->person;

                $matricula = [];
                $matriculaId = $enrollment->ref_cod_matricula;
                $turmaId = $enrollment->ref_cod_turma;
                $serieId = $registration->ref_ref_cod_serie;
                $componenteCurricularId = $this->getRequest()->componente_curricular_id;
                $disciplinasDependenciaId = $enrollment->registration->dependencies->pluck('ref_cod_disciplina')->toArray();
                $matriculaDependencia = $enrollment->registration->dependencia;

                if (!empty($componenteCurricularId) && $matriculaDependencia && !in_array($componenteCurricularId, $disciplinasDependenciaId)) {
                    continue;
                }

                // seta id da matricula a ser usado pelo metodo serviceBoletim
                $this->setCurrentMatriculaId($matriculaId);

                if (!($enrollment->remanejado || $enrollment->transferido || $enrollment->abandono || $enrollment->reclassificado || $enrollment->falecido)) {
                    $matricula['componentes_curriculares'] = $this->loadComponentesCurricularesForMatricula($matriculaId, $turmaId, $serieId);
                }

                $matricula['matricula_id'] = $registration->getKey();
                $matricula['aluno_id'] = $student->getKey();
                $matricula['nome'] = $person->name;

                if ($enrollment->remanejado) {
                    $matricula['situacao_deslocamento'] = 'Remanejado';
                } elseif ($enrollment->transferido) {
                    $matricula['situacao_deslocamento'] = 'Transferido';
                } elseif ($enrollment->abandono) {
                    $matricula['situacao_deslocamento'] = 'Abandono';
                } elseif ($enrollment->reclassificado) {
                    $matricula['situacao_deslocamento'] = 'Reclassificado';
                } elseif ($enrollment->falecido) {
                    $matricula['situacao_deslocamento'] = 'Falecido';
                } else {
                    $matricula['situacao_deslocamento'] = null;
                }

                // Utiliza a regra de avaliação diferenciada quando o aluno
                // possua alguma deficiência que seja considerada e exista uma
                // regra de avaliação diferenciada para a turma.

                $registrationEvaluationRule = $evaluationRule;

                if ($registration->ref_ref_cod_serie != $schoolClass->grade_id) {
                    $registrationEvaluationRule = $registration->getEvaluationRule();
                }

                if ($person->considerable_deficiencies_count && $deficiencyEvaluationRule) {
                    $registrationEvaluationRule = $deficiencyEvaluationRule;
                }

                $matricula['regra'] = $this->getEvaluationRule($registrationEvaluationRule);

                $matricula['regra']['quantidade_etapas'] = $schoolClass->stages->count();

                $regras[$matricula['regra']['id']] = $matricula['regra'];

                $matriculas[] = $matricula;
            }
        }

        if ($matriculas) {
            $this->appendResponse('details', array_values($regras));
        }

        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);

        return $matriculas;
    }

    // metodos auxiliares responders

    // TODO usar esta funcao onde é verificado se parecer geral
    protected function parecerGeral()
    {
        $tiposParecerGeral = array(RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL,
            RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL);

        return in_array($this->serviceBoletim()->getRegra()->get('parecerDescritivo'), $tiposParecerGeral);
    }

    protected function setCurrentMatriculaId($matriculaId)
    {
        $this->_currentMatriculaId = $matriculaId;
    }

    protected function getCurrentMatriculaId()
    {
        // caso tenha setado _currentMatriculaId, ignora matricula_id recebido nos parametros
        if (!is_null($this->_currentMatriculaId)) {
            $matriculaId = $this->_currentMatriculaId;
        } elseif (!is_null($this->getRequest()->matricula_id)) {
            $matriculaId = $this->getRequest()->matricula_id;
        } else {
            throw new CoreExt_Exception("Não foi possivel recuperar o id da matricula atual.");
        }

        return $matriculaId;
    }

    /**
     * @param bool $reload
     *
     * @return Avaliacao_Service_Boletim
     *
     * @throws CoreExt_Exception
     */
    protected function serviceBoletim($reload = false)
    {
        $matriculaId = $this->getCurrentMatriculaId();

        if (!isset($this->_boletimServiceInstances)) {
            $this->_boletimServiceInstances = array();
        }

        // set service
        if (!isset($this->_boletimServiceInstances[$matriculaId]) || $reload) {
            try {
                $params = array(
                    'matricula' => $matriculaId,
                    'usuario' => $this->getSession()->id_pessoa,
                    'componenteCurricularId' => $this->getRequest()->componente_curricular_id,
                );
                $this->_boletimServiceInstances[$matriculaId] = new Avaliacao_Service_Boletim($params);
            } catch (Exception $e) {
                $this->messenger->append("Erro ao instanciar serviço boletim para matricula {$matriculaId}: " . $e->getMessage(), 'error', true);
            }
        }

        // validates service
        if (is_null($this->_boletimServiceInstances[$matriculaId])) {
            throw new CoreExt_Exception("Não foi possivel instanciar o serviço boletim para a matricula $matriculaId.");
        }

        return $this->_boletimServiceInstances[$matriculaId];
    }

    protected function trySaveServiceBoletim()
    {
        try {
            $this->serviceBoletim()->save();
        } catch (CoreExt_Service_Exception $e) {
            // excecoes ignoradas :( pois servico lanca excecoes de alertas, que não são exatamente erros.
            // error_log('CoreExt_Service_Exception ignorada: ' . $e->getMessage());
        }
    }

    protected function trySaveServiceBoletimFaltas()
    {
        try {
            $this->serviceBoletim()->saveFaltas();
            $this->serviceBoletim()->promover();
        } catch (CoreExt_Service_Exception $e) {
        }
    }

    // metodos auxiliares getFalta

    protected function getQuantidadeFalta()
    {
        $quantidade = (int) $this->getRequest()->att_value;

        if ($quantidade < 0) {
            $quantidade = 0;
        }

        return $quantidade;
    }

    protected function getFaltaGeral()
    {
        return new Avaliacao_Model_FaltaGeral(array(
            'quantidade' => $this->getQuantidadeFalta(),
            'etapa' => $this->getRequest()->etapa,
        ));
    }

    protected function getFaltaComponente()
    {
        return new Avaliacao_Model_FaltaComponente(array(
            'componenteCurricular' => $this->getRequest()->componente_curricular_id,
            'quantidade' => $this->getQuantidadeFalta(),
            'etapa' => $this->getRequest()->etapa,
        ));
    }

    // metodos auxiliares getParecer

    protected function getParecerComponente()
    {
        return new Avaliacao_Model_ParecerDescritivoComponente(array(
            'componenteCurricular' => $this->getRequest()->componente_curricular_id,
            'parecer' => $this->safeStringForDb($this->getRequest()->att_value),
            'etapa' => $this->getRequest()->etapa,
        ));
    }

    protected function getParecerGeral()
    {
        return new Avaliacao_Model_ParecerDescritivoGeral(array(
            'parecer' => $this->safeStringForDb($this->getRequest()->att_value),
            'etapa' => $this->getRequest()->etapa,
        ));
    }

    // metodos auxiliares getSituacaoComponente

    protected function getSituacaoComponente($ccId = null)
    {
        if (is_null($ccId)) {
            $ccId = $this->getRequest()->componente_curricular_id;
        }

        if (!$this->serviceBoletim()->exibeSituacao($ccId)) {
            return null;
        }

        $situacao = null;

        $situacoes = $this->getSituacaoComponentes();
        if(isset($situacoes[$ccId])){
            $situacao = $situacoes[$ccId];
        }

        return $this->safeString($situacao);
    }

    protected function getSituacaoComponentes()
    {
        $situacoes = array();

        try {
            $componentesCurriculares = $this->serviceBoletim()->getSituacaoComponentesCurriculares()->componentesCurriculares;
            foreach($componentesCurriculares as $componenteCurricularId => $situacaoCc){
                $situacoes[$componenteCurricularId] = $this->serviceBoletim()->exibeSituacao($componenteCurricularId) ? App_Model_MatriculaSituacao::getInstance()->getValue($situacaoCc->situacao) : null;
            }

        } catch (Exception $e) {
            $matriculaId = $this->getRequest()->matricula_id;
            $this->messenger->append("Erro ao recuperar situação da matrícula '$matriculaId': " .
                $e->getMessage());
        }

        return $situacoes;
    }

    // outros metodos auxiliares

    protected function loadComponentesCurricularesForMatricula($matriculaId, $turmaId, $serieId)
    {
        $componentesCurriculares = array();

        $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        $etapa = $this->getRequest()->etapa;

        $_componentesCurriculares = App_Model_IedFinder::getComponentesPorMatricula($matriculaId, null, null, $componenteCurricularId, $etapa, $turmaId);

        $turmaId = $this->getRequest()->turma_id;
        $situacoes = $this->getSituacaoComponentes();

        $slugify = new Slugify();

        foreach ($_componentesCurriculares as $_componente) {
            $componente = array();
            $componenteId = $_componente->get('id');
            $tipoNota = App_Model_IedFinder::getTipoNotaComponenteSerie($componenteId, $serieId);

            if (clsPmieducarTurma::verificaDisciplinaDispensada($turmaId, $componenteId)) {
                continue;
            }

            $componente['id'] = $componenteId;
            $componente['nome'] = mb_strtoupper($_componente->get('nome'), 'UTF-8');
            $componente['nota_atual'] = $this->getNotaAtual($etapa = null, $componente['id']);
            $componente['nota_exame'] = $this->getNotaExame($componente['id']);
            $componente['falta_atual'] = $this->getFaltaAtual($etapa = null, $componente['id']);
            $componente['parecer_atual'] = $this->getParecerAtual($componente['id']);
            $componente['situacao'] = $this->safeString($situacoes[$componente['id']]);
            $componente['tipo_nota'] = $tipoNota;
            $componente['ultima_etapa'] = App_Model_IedFinder::getUltimaEtapaComponente($turmaId, $componenteId);
            $gravaNotaExame = ($componente['situacao'] == 'Em exame' || $componente['situacao'] == 'Aprovado após exame' || $componente['situacao'] == 'Retido');

            $componente['nota_necessaria_exame'] = ($gravaNotaExame ? $this->getNotaNecessariaExame($componente['id']) : null);
            $componente['ordenamento'] = $_componente->get('ordenamento');
            $componente['nota_recuperacao_paralela'] = $this->getNotaRecuperacaoParalelaAtual($etapa, $componente['id']);
            $componente['nota_recuperacao_especifica'] = $this->getNotaRecuperacaoEspecificaAtual($etapa, $componente['id']);
            $componente['should_show_recuperacao_especifica'] = $this->shouldShowRecuperacaoEspecifica($etapa, $componente['id']);
            $componente['nota_original'] = $this->getNotaOriginal($etapa, $componente['id']);
            $componente['nota_geral_etapa'] = $this->getNotaGeral($etapa);
            $componente['media'] = $this->getMediaAtual($componente['id']);
            $componente['media_arredondada'] = $this->getMediaArredondadaAtual($componente['id']);
            $componente['media_bloqueada'] = $this->getMediaBloqueada($componente['id']);

            if (!empty($componente['nota_necessaria_exame'])) {
                $this->createOrUpdateNotaExame($matriculaId, $componente['id'], $componente['nota_necessaria_exame']);
            } else {
                $this->deleteNotaExame($matriculaId, $componente['id']);
            }

            //buscando pela área do conhecimento
            $area = $this->getAreaConhecimento($componente['id']);
            $nomeArea = (($area->secao != '') ? $area->secao . ' - ' : '') . $area->nome;
            $componente['ordenamento_ac'] = $area->ordenamento_ac;
            $componente['area_id'] = $area->id;
            $componente['area_nome'] = mb_strtoupper($nomeArea, 'UTF-8');

            //criando chave para ordenamento temporário
            //área de conhecimento + componente curricular

            $componente['ordem_nome_area_conhecimento'] = $slugify->slugify($nomeArea);
            $componente['ordem_componente_curricular'] = $slugify->slugify($_componente->get('nome'));
            $componentesCurriculares[] = $componente;
        }

        $ordenamentoComponentes = array();

        foreach ($componentesCurriculares as $chave => $componente) {
            $ordenamentoComponentes['ordenamento_ac'][$chave] = $componente['ordenamento_ac'];
            $ordenamentoComponentes['ordenamento'][$chave] = $componente['ordenamento'];
            $ordenamentoComponentes['ordem_nome_area_conhecimento'][$chave] = $componente['ordem_nome_area_conhecimento'];
            $ordenamentoComponentes['ordem_componente_curricular'][$chave] = $componente['ordem_componente_curricular'];
        }
        array_multisort($ordenamentoComponentes['ordenamento_ac'], SORT_ASC, SORT_NUMERIC,
            $ordenamentoComponentes['ordem_nome_area_conhecimento'], SORT_ASC,
            $ordenamentoComponentes['ordenamento'], SORT_ASC, SORT_NUMERIC,
            $ordenamentoComponentes['ordem_componente_curricular'], SORT_ASC,
            $componentesCurriculares);

        //removendo chave temporária
        $len = count($componentesCurriculares);
        for ($i = 0; $i < $len; $i++) {
            unset($componentesCurriculares[$i]['my_order']);
        }
        return $componentesCurriculares;
    }

    protected function getAreaConhecimento($componenteCurricularId = null)
    {
        if (is_null($componenteCurricularId)) {
            $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        }

        if (!is_numeric($componenteCurricularId)) {
            throw new Exception('Não foi possível obter a área de conhecimento pois não foi recebido o id do componente curricular.');
        }

        require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
        $mapper = new ComponenteCurricular_Model_ComponenteDataMapper();

        $where = array('id' => $componenteCurricularId);

        $key = json_encode($where);

        $area = Cache::store('array')->remember("getAreaConhecimento:{$key}", now()->addMinute(), function () use ($mapper, $where) {
            return $mapper->findAll(array('area_conhecimento'), $where);
        });

        $areaConhecimento = new stdClass();
        $areaConhecimento->id = $area[0]->area_conhecimento->id;
        $areaConhecimento->nome = $area[0]->area_conhecimento->nome;
        $areaConhecimento->secao = $area[0]->area_conhecimento->secao;
        $areaConhecimento->ordenamento_ac = $area[0]->area_conhecimento->ordenamento_ac;

        return $areaConhecimento;
    }

    protected function createOrUpdateNotaExame($matriculaId, $componenteCurricularId, $notaExame)
    {

        $obj = new clsModulesNotaExame($matriculaId, $componenteCurricularId, $notaExame);

        return ($obj->existe() ? $obj->edita() : $obj->cadastra());
    }

    protected function deleteNotaExame($matriculaId, $componenteCurricularId)
    {
        $obj = new clsModulesNotaExame($matriculaId, $componenteCurricularId);
        return ($obj->excluir());
    }

    /**
     * @deprecated
     *
     * @see Avaliacao_Service_Boletim::getNotaAtual()
     */
    protected function getNotaAtual($etapa = null, $componenteCurricularId = null)
    {
        // defaults
        if (is_null($componenteCurricularId)) {
            $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        }

        if (is_null($etapa)) {
            $etapa = $this->getRequest()->etapa;
        }

        // validacao
        if (!is_numeric($componenteCurricularId)) {
            throw new Exception('Não foi possivel obter a nota atual, pois não foi recebido o id do componente curricular.');
        }

        $nota = urldecode($this->serviceBoletim()->getNotaComponente($componenteCurricularId, $etapa)->nota);

        return str_replace(',', '.', $nota);
    }

    protected function getNotaGeral($etapa = null)
    {

        if (is_null($etapa)) {
            $etapa = $this->getRequest()->etapa;
        }

        $nota = urldecode($this->serviceBoletim()->getNotaGeral($etapa)->nota);

        return str_replace(',', '.', $nota);

    }

    protected function getMediaAtual($componenteCurricularId = null)
    {
        // defaults
        if (is_null($componenteCurricularId)) {
            $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        }

        // validacao
        if (!is_numeric($componenteCurricularId)) {
            throw new Exception('Não foi possivel obter a média atual, pois não foi recebido o id do componente curricular.');
        }

        $media = urldecode($this->serviceBoletim()->getMediaComponente($componenteCurricularId)->media);

        $scale = pow(10, 3);

        return floor(floatval($media) * $scale) / $scale;
    }

    protected function getMediaArredondadaAtual($componenteCurricularId = null)
    {
        // defaults
        if (is_null($componenteCurricularId)) {
            $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        }

        // validacao
        if (!is_numeric($componenteCurricularId)) {
            throw new Exception('Não foi possivel obter a média atual, pois não foi recebido o id do componente curricular.');
        }

        $media = urldecode($this->serviceBoletim()->getMediaComponente($componenteCurricularId)->mediaArredondada);

        // $media = round($media,1);

        return str_replace(',', '.', $media);
    }

    protected function getMediaBloqueada($componenteCurricularId = null)
    {
        // defaults
        if (is_null($componenteCurricularId)) {
            $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        }

        // validacao
        if (!is_numeric($componenteCurricularId)) {
            throw new Exception('Não foi possivel obter a média atual, pois não foi recebido o id do componente curricular.');
        }

        $bloqueada = (bool) $this->serviceBoletim()->getMediaComponente($componenteCurricularId)->bloqueada;

        return $bloqueada;
    }

    protected function getNotaRecuperacaoParalelaAtual($etapa = null, $componenteCurricularId = null)
    {
        // defaults
        if (is_null($componenteCurricularId)) {
            $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        }

        if (is_null($etapa)) {
            $etapa = $this->getRequest()->etapa;
        }

        // validacao
        if (!is_numeric($componenteCurricularId)) {
            throw new Exception('Não foi possivel obter a nota de recuperação paralela atual, pois não foi recebido o id do componente curricular.');
        }

        $nota = urldecode($this->serviceBoletim()->getNotaComponente($componenteCurricularId, $etapa)->notaRecuperacaoParalela);
        $nota = str_replace(',', '.', $nota);
        return $nota;
    }

    protected function shouldShowRecuperacaoEspecifica($etapa = null, $componenteCurricularId = null)
    {
        // defaults
        if (is_null($componenteCurricularId)) {
            $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        }

        if (is_null($etapa)) {
            $etapa = $this->getRequest()->etapa;
        }

        // validacao
        if (!is_numeric($componenteCurricularId)) {
            throw new Exception('Não foi possivel obter a nota de recuperação específica atual, pois não foi recebido o id do componente curricular.');
        }

        $regra = $this->serviceBoletim()->getRegra();
        $tipoRecuperacaoParalela = $regra->get('tipoRecuperacaoParalela');

        $regraRecuperacao = $regra->getRegraRecuperacaoByEtapa($etapa);

        if ($tipoRecuperacaoParalela == RegraAvaliacao_Model_TipoRecuperacaoParalela::USAR_POR_ETAPAS_ESPECIFICAS
            && $regraRecuperacao && $regraRecuperacao->getLastEtapa() == $etapa) {

            $etapas = $regraRecuperacao->getEtapas();
            $sumNota = 0;
            foreach ($etapas as $key => $_etapa) {
                $sumNota += $this->getNotaOriginal($_etapa, $componenteCurricularId);
            }

            // caso a média das notas da etapa seja menor que média definida na regra e a última nota tenha sido lançada
            // deverá exibir a nota de recuperação
            if ((($sumNota / count($etapas)) < $regraRecuperacao->get('media'))
                && is_numeric($this->getNotaOriginal($etapa, $componenteCurricularId))) {
                return true;
            } else {
                // Caso não exiba, já busca se existe a nota de recuperação e deleta ela
                $notaRecuperacao = $this->serviceBoletim()->getNotaComponente($componenteCurricularId, $regraRecuperacao->getLastEtapa());

                if ($notaRecuperacao) {
                    $nota = new Avaliacao_Model_NotaComponente(array(
                        'componenteCurricular' => $componenteCurricularId,
                        'nota' => $notaRecuperacao->notaOriginal,
                        'etapa' => $notaRecuperacao->etapa,
                        'notaOriginal' => $notaRecuperacao->notaOriginal,
                        'notaRecuperacaoParalela' => $notaRecuperacao->notaRecuperacaoParalela,
                    ));

                    $this->serviceBoletim()->addNota($nota);
                    $this->trySaveServiceBoletim();
                }
                return false;
            }
        }
        return false;
    }

    protected function getNotaRecuperacaoEspecificaAtual($etapa = null, $componenteCurricularId = null)
    {
        // defaults
        if (is_null($componenteCurricularId)) {
            $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        }

        if (is_null($etapa)) {
            $etapa = $this->getRequest()->etapa;
        }

        // validacao
        if (!is_numeric($componenteCurricularId)) {
            throw new Exception('Não foi possivel obter a nota de recuperação específica atual, pois não foi recebido o id do componente curricular.');
        }

        $nota = urldecode($this->serviceBoletim()->getNotaComponente($componenteCurricularId, $etapa)->notaRecuperacaoEspecifica);
        $nota = str_replace(',', '.', $nota);
        return $nota;
    }

    protected function getNotaOriginal($etapa = null, $componenteCurricularId = null)
    {
        // defaults
        if (is_null($componenteCurricularId)) {
            $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        }

        if (is_null($etapa)) {
            $etapa = $this->getRequest()->etapa;
        }

        // validacao
        if (!is_numeric($componenteCurricularId)) {
            throw new Exception('Não foi possivel obter a nota original, pois não foi recebido o id do componente curricular.');
        }

        $nota = urldecode($this->serviceBoletim()->getNotaComponente($componenteCurricularId, $etapa)->notaOriginal);
        $nota = str_replace(',', '.', $nota);
        return $nota;
    }

    protected function getNotaExame($componenteCurricularId = null)
    {

        $turmaId = $this->getRequest()->turma_id;
        $regra = $this->serviceBoletim()->getRegra();
        $defineComponentePorEtapa = $regra->get('definirComponentePorEtapa') == 1;
        $ultimaEtapa = $this->getRequest()->etapa == $this->serviceBoletim()->getOption('etapas');
        $ultimaEtapaComponente = App_Model_IedFinder::getUltimaEtapaComponente($turmaId, $componenteCurricularId);

        // somente recupera nota de exame se estiver buscando as matriculas da ultima etapa
        // se existe nota de exame, esta é recuperada mesmo que a regra de avaliação não use mais exame
        if ($ultimaEtapa || ($defineComponentePorEtapa && $ultimaEtapaComponente)) {
            $nota = $this->getNotaAtual($etapa = 'Rc', $componenteCurricularId);
        } else {
            $nota = '';
        }

        return $nota;
    }

    protected function getNotaNecessariaExame($componenteCurricularId = null)
    {
        if (is_null($componenteCurricularId)) {
            $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        }

        if (!$this->serviceBoletim()->exibeNotaNecessariaExame($componenteCurricularId)) {
            return null;
        }

        $nota = $this->serviceBoletim()->preverNotaRecuperacao($componenteCurricularId);

        return str_replace(',', '.', $nota);
    }

    /**
     * @deprecated
     *
     * @see Avaliacao_Service_Boletim::getFaltaAtual()
     */
    protected function getFaltaAtual($etapa = null, $componenteCurricularId = null)
    {
        // defaults
        if (is_null($componenteCurricularId)) {
            $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        }

        if (is_null($etapa)) {
            $etapa = $this->getRequest()->etapa;
        }

        if ($this->serviceBoletim()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
            if (!is_numeric($componenteCurricularId)) {
                throw new Exception('Não foi possivel obter a falta atual, pois não foi recebido o id do componente curricular.');
            }

            $falta = $this->serviceBoletim()->getFalta($etapa, $componenteCurricularId)->quantidade;
        } elseif ($this->serviceBoletim()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::GERAL) {
            $falta = $this->serviceBoletim()->getFalta($etapa)->quantidade;
        }

        return $falta;
    }

    protected function getEtapaParecer()
    {
        if ($this->getRequest()->etapa != 'An' && ($this->serviceBoletim()->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE || $this->serviceBoletim()->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL)) {
            return 'An';
        } else {
            return $this->getRequest()->etapa;
        }

    }

    protected function getParecerAtual($componenteCurricularId = null)
    {
        // defaults
        if (is_null($componenteCurricularId)) {
            $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        }

        $etapaComponente = $this->serviceBoletim()->getRegra()->get('parecerDescritivo') ==
        RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE;

        $anualComponente = $this->serviceBoletim()->getRegra()->get('parecerDescritivo') ==
        RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE;

        if ($etapaComponente or $anualComponente) {
            if (!is_numeric($componenteCurricularId)) {
                throw new Exception('Não foi possivel obter o parecer descritivo atual, pois não foi recebido o id do componente curricular.');
            }

            $parecer = $this->serviceBoletim()->getParecerDescritivo($this->getEtapaParecer(), $componenteCurricularId)->parecer;
        } else {
            $parecer = $this->serviceBoletim()->getParecerDescritivo($this->getEtapaParecer())->parecer;
        }

        return $this->safeString($parecer, $transform = false);
    }

    protected function getRoundingValues($evaluationRule, $roundingTable)
    {
        $options = [];

        if ($evaluationRule->tipo_nota != RegraAvaliacao_Model_Nota_TipoValor::NENHUM) {
            $roudingValues = $roundingTable->roundingValues;

            foreach ($roudingValues as $index => $roudingValue) {
                if ($evaluationRule->tipo_nota == RegraAvaliacao_Model_Nota_TipoValor::NUMERICA) {
                    $nota = str_replace(',', '.', (string) $roudingValue->nome);
                    $options[$nota] = $nota;
                } else {
                    $options[$index] = [
                        'valor_minimo' => str_replace(',', '.', (string) $roudingValue->valor_minimo),
                        'valor_maximo' => str_replace(',', '.', (string) $roudingValue->valor_maximo),
                        'descricao' => $this->safeString($roudingValue->nome . ' (' . $roudingValue->descricao . ')'),
                    ];
                }
            }
        }

        return $options;
    }

    protected function getNavegacaoTab()
    {
        return $this->getRequest()->navegacao_tab;
    }

    /**
     * Retorna um array com todos os dados necessários para a interface do
     * faltas e notas sobre a regra de avaliação.
     *
     * @param LegacyEvaluationRule $evaluationRule
     *
     * @return array
     */
    protected function getEvaluationRule($evaluationRule)
    {
        $rule = [
            'id' => $evaluationRule->id,
            'nome' => $evaluationRule->nome,
            'nota_maxima_geral' => $evaluationRule->nota_maxima_geral,
            'nota_minima_geral' => $evaluationRule->nota_minima_geral,
            'nota_maxima_exame_final' => $evaluationRule->nota_maxima_exame_final,
            'qtd_casas_decimais' => $evaluationRule->qtd_casas_decimais,
            'regra_diferenciada_id' => $evaluationRule->regra_diferenciada_id,
        ];

        $tpPresenca = $evaluationRule->tipo_presenca;

        if ($tpPresenca == RegraAvaliacao_Model_TipoPresenca::GERAL) {
            $rule['tipo_presenca'] = 'geral';
        } elseif ($tpPresenca == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
            $rule['tipo_presenca'] = 'por_componente';
        } else {
            $rule['tipo_presenca'] = $tpPresenca;
        }

        $tpNota = $evaluationRule->tipo_nota;

        if ($tpNota == RegraAvaliacao_Model_Nota_TipoValor::NENHUM) {
            $rule['tipo_nota'] = 'nenhum';
        } elseif ($tpNota == RegraAvaliacao_Model_Nota_TipoValor::NUMERICA) {
            $rule['tipo_nota'] = 'numerica';
        } elseif ($tpNota == RegraAvaliacao_Model_Nota_TipoValor::CONCEITUAL) {
            $rule['tipo_nota'] = 'conceitual';
        } elseif ($tpNota == RegraAvaliacao_Model_Nota_TipoValor::NUMERICACONCEITUAL) {
            $rule['tipo_nota'] = 'numericaconceitual';
        } else {
            $rule['tipo_nota'] = $tpNota;
        }

        $tpProgressao = $evaluationRule->tipo_progressao;
        $rule['progressao_manual'] = $tpProgressao == RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_MANUAL;
        $rule['progressao_continuada'] = $tpProgressao == RegraAvaliacao_Model_TipoProgressao::CONTINUADA;

        $tpParecer = $evaluationRule->parecer_descritivo;

        if ($tpParecer == RegraAvaliacao_Model_TipoParecerDescritivo::NENHUM) {
            $rule['tipo_parecer_descritivo'] = 'nenhum';
        } elseif ($tpParecer == RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE) {
            $rule['tipo_parecer_descritivo'] = 'etapa_componente';
        } elseif ($tpParecer == RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL) {
            $rule['tipo_parecer_descritivo'] = 'etapa_geral';
        } elseif ($tpParecer == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE) {
            $rule['tipo_parecer_descritivo'] = 'anual_componente';
        } elseif ($tpParecer == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL) {
            $rule['tipo_parecer_descritivo'] = 'anual_geral';
        } else {
            $rule['tipo_parecer_descritivo'] = $tpParecer;
        }

        $rule['opcoes_notas'] = $this->getRoundingValues($evaluationRule, $evaluationRule->roundingTable);

        if ($tpNota == RegraAvaliacao_Model_Nota_TipoValor::NUMERICACONCEITUAL) {
            $rule['opcoes_notas_conceituais'] = $this->getRoundingValues($evaluationRule, $evaluationRule->conceptualRoundingTable);
        }

        $rule['nomenclatura_exame'] = config('legacy.app.diario.nomenclatura_exame') == 0 ? 'exame' : 'conselho';
        $rule['regra_dependencia'] = config('legacy.app.matricula.dependencia') ? true : false;

        $tipoRecuperacaoParalela = $evaluationRule->tipo_recuperacao_paralela;

        if ($tipoRecuperacaoParalela == RegraAvaliacao_Model_TipoRecuperacaoParalela::NAO_USAR) {
            $rule['tipo_recuperacao_paralela'] = 'nao_utiliza';
        } elseif ($tipoRecuperacaoParalela == RegraAvaliacao_Model_TipoRecuperacaoParalela::USAR_POR_ETAPA) {
            $rule['tipo_recuperacao_paralela'] = 'por_etapa';
            $rule['media_recuperacao_paralela'] = $evaluationRule->media_recuperacao_paralela;
            $rule['calcula_media_rec_paralela'] = $evaluationRule->calcula_media_rec_paralela;
        } elseif ($tipoRecuperacaoParalela == RegraAvaliacao_Model_TipoRecuperacaoParalela::USAR_POR_ETAPAS_ESPECIFICAS) {
            $rule['tipo_recuperacao_paralela'] = 'etapas_especificas';

            $etapa = $this->getRequest()->etapa;

            /** @var Collection $remedialRules */
            if ($remedialRules = $evaluationRule->remedialRules) {
                /** @var LegacyRemedialRule $remedialRule */
                $remedialRule = $remedialRules->first(function ($remedialRule) use ($etapa) {
                    /** @var LegacyRemedialRule $remedialRule */
                    return in_array($etapa, $remedialRule->getStages());
                });
            }

            if (isset($remedialRule)) {
                $rule['habilita_campo_etapa_especifica'] = $remedialRule->getLastStage() == $etapa;
                $rule['tipo_recuperacao_paralela_nome'] = $remedialRule->descricao;
                $rule['tipo_recuperacao_paralela_nota_maxima'] = $remedialRule->nota_maxima;
            } else {
                $rule['habilita_campo_etapa_especifica'] = false;
                $rule['tipo_recuperacao_paralela_nome'] = '';
                $rule['tipo_recuperacao_paralela_nota_maxima'] = 0;
            }
        }

        if ($evaluationRule->nota_geral_por_etapa == '1') {
            $rule['nota_geral_por_etapa'] = "SIM";
        } else {
            $rule['nota_geral_por_etapa'] = "NAO UTILIZA";
        }

        $rule['definir_componente_por_etapa'] = $evaluationRule->definir_componente_etapa == 1;

        return $rule;
    }

    protected function inserirAuditoriaNotas($notaAntiga, $notaNova)
    {
        if ($this->usaAuditoriaNotas()) {
            $objAuditoria = new clsModulesAuditoriaNota($notaAntiga, $notaNova, $this->getRequest()->turma_id);
            $objAuditoria->cadastra();
        }
    }

    protected function usaAuditoriaNotas()
    {
        return (config('legacy.app.auditoria.notas') == "1");
    }

    public function canChange()
    {
        $user = $this->getSession()->id_pessoa;
        $processoAp = $this->_processoAp;
        $obj_permissao = new clsPermissoes();

        return $obj_permissao->permissao_cadastra($processoAp, $user, 7);
    }

    public function postSituacao()
    {
        if ($this->canPostSituacaoAndNota()) {
            $novaSituacao = $this->getRequest()->att_value;
            $matriculaId = $this->getRequest()->matricula_id;

            $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);

            $this->serviceBoletim()->alterarSituacao($novaSituacao, $matriculaId);
            $this->messenger->append('Situação da matrícula ' . $this->getRequest()->matricula_id . ' alterada com sucesso.', 'success');
        } else {
            $this->messenger->append('Usuário não possui permissão para alterar a situação da matrícula.', 'error');
        }
    }

    public function canPostSituacaoAndNota()
    {
        $this->pessoa_logada = Session::get('id_pessoa');

        $acesso = new clsPermissoes();

        return $acesso->permissao_cadastra(630, $this->pessoa_logada, 7, null, true);
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'matriculas')) {
            $this->appendResponse('matriculas', $this->getMatriculas());
            $this->appendResponse('navegacao_tab', $this->getNavegacaoTab());
            $this->appendResponse('can_change', $this->canChange());
            $this->appendResponse('locked', !$this->validatesPeriodoLancamentoFaltasNotas(false));
        } elseif ($this->isRequestFor('post', 'nota') || $this->isRequestFor('post', 'nota_exame')) {
            $this->postNota();
        } elseif ($this->isRequestFor('post', 'nota_recuperacao_paralela')) {
            $this->postNotaRecuperacaoParalela();
        } elseif ($this->isRequestFor('post', 'nota_recuperacao_especifica')) {
            $this->postNotaRecuperacaoEspecifica();
        } elseif ($this->isRequestFor('post', 'falta')) {
            $this->postFalta();
        } elseif ($this->isRequestFor('post', 'parecer')) {
            $this->postParecer();
        } elseif ($this->isRequestFor('post', 'nota_geral')) {
            $this->postNotaGeral();
        } elseif ($this->isRequestFor('post', 'media')) {
            $this->postMedia();
        } elseif ($this->isRequestFor('post', 'media_desbloqueia')) {
            $this->postMediaDesbloqueia();
        } elseif ($this->isRequestFor('delete', 'media')) {
            $this->deleteMedia();
        } elseif ($this->isRequestFor('post', 'situacao')) {
            $this->postSituacao();
        } elseif ($this->isRequestFor('delete', 'nota') || $this->isRequestFor('delete', 'nota_exame')) {
            $this->deleteNota();
        } elseif ($this->isRequestFor('delete', 'nota_recuperacao_paralela')) {
            $this->deleteNotaRecuperacaoParalela();
        } elseif ($this->isRequestFor('delete', 'nota_recuperacao_especifica')) {
            $this->deleteNotaRecuperacaoEspecifica();
        } elseif ($this->isRequestFor('delete', 'falta')) {
            $this->deleteFalta();
        } elseif ($this->isRequestFor('delete', 'parecer')) {
            $this->deleteParecer();
        } elseif ($this->isRequestFor('delete', 'nota_geral')) {
            $this->deleteNotaGeral();
        } else {
            $this->notImplementedOperationError();
        }

    }
}
