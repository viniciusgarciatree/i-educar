<?php

class ParecerController extends Core_Controller_Page_EditController
{
    protected $_dataMapper        = 'Avaliacao_Model_ParecerDescritivoGeralDataMapper';
    protected $_titulo            = 'Avaliação do aluno | Parecer Descritivo';
    protected $_processoAp        = 642;
    protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
    protected $_saveOption        = true;
    protected $_deleteOption      = false;

    /**
     * @var Avaliacao_Service_Boletim
     */
    protected $_service = null;

    /**
     * @var RegraAvaliacao_Model_Regra
     */
    protected $_regra = null;

    /**
     * @var int
     */
    protected $_matricula = null;

    /**
     * @var int
     */
    protected $_componenteCurricular = null;

    /**
     * @var string
     */
    protected $_etapa = null;

    /**
     * @var Avaliacao_Model_ParecerDescritivoAbstract
     */
    protected $_parecer = null;

    /**
     * @see Core_Controller_Page_EditController#_preConstruct()
     */
    protected function _preConstruct()
    {
        // Id do usuário na session
        $usuario = \Illuminate\Support\Facades\Auth::id();

        $this->_options = [
      'new_success'         => 'boletim',
      'new_success_params' => ['matricula' => $this->getRequest()->matricula],
      'edit_success'        => 'boletim',
      'edit_success_params' => ['matricula' => $this->getRequest()->matricula],
    ];

        $this->_service = new Avaliacao_Service_Boletim([
      'matricula' => $this->getRequest()->matricula,
      'usuario'   => $usuario
    ]);

        $this->_regra = $this->_service->getRegra();
    }

    /**
     * @see Core_Controller_Page_EditController#_initNovo()
     */
    protected function _initNovo()
    {
        $this->_etapa                = $this->getRequest()->etapa;
        $this->_matricula            = $this->getRequest()->matricula;
        $this->_componenteCurricular = $this->getRequest()->componenteCurricular;

        if (isset($this->_etapa) && isset($this->_matricula)) {
            return false;
        }

        if ($this->_regra->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE &&
        !isset($this->_componenteCurricular)) {
            return false;
        }

        return true;
    }

    /**
     * @see Core_Controller_Page_EditController#_initEditar()
     */
    protected function _initEditar()
    {
        $this->_parecer = $this->_service->getParecerDescritivo($this->_etapa, $this->_componenteCurricular);

        return true;
    }

    /**
     * @see clsCadastro#Gerar()
     */
    public function Gerar()
    {
        $this->campoOculto('matricula', $this->_matricula);
        $this->campoOculto('etapa', $this->_etapa);
        $this->campoOculto('componenteCurricular', $this->_componenteCurricular);

        $matricula = $this->_service->getOption('matriculaData');

        $this->campoRotulo('1nome', 'Nome', $matricula['nome']);
        $this->campoRotulo('2curso', 'Curso', $matricula['curso_nome']);
        $this->campoRotulo('3serie', 'Série', $matricula['serie_nome']);
        $this->campoRotulo('4turma', 'Turma', $matricula['turma_nome']);

        if ($this->_regra->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL) {
            $this->campoRotulo('5etapa', 'Etapa', $this->_etapa == 'Rc' ? 'Recuperação' : $this->_etapa);
        } else {
            $this->campoRotulo('5etapa', 'Etapa', 'Anual');
        }

        if ($this->_componenteCurricular) {
            $componentes = $this->_service->getComponentes();
            $this->campoRotulo('6componente_curricular', 'Componente curricular', $componentes[$this->_componenteCurricular]);
        }

        $this->campoMemo('parecer', 'Parecer', $this->_parecer, 40, 10, true);
    }

    /**
     * @see Core_Controller_Page_EditController#_save()
     */
    protected function _save()
    {
        // Instancia o objeto correto e passa para o service
        if ($this->_regra->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE) {
            $parecer = new Avaliacao_Model_ParecerDescritivoComponente([
        'componenteCurricular' => $this->getRequest()->componenteCurricular,
        'parecer' => $this->getRequest()->parecer,
        'etapa'   => $this->getRequest()->etapa
      ]);
        } else {
            $parecer = new Avaliacao_Model_ParecerDescritivoGeral([
        'parecer' => $this->getRequest()->parecer,
        'etapa'   => $this->getRequest()->etapa
      ]);
        }

        $this->_service->addParecer($parecer);

        try {
            $this->_service->save();
        } catch (CoreExt_Service_Exception $e) {
            // Ok. Não pode promover por se tratar de progressão manual ou por estar em andamento
        } catch (Exception $e) {
            $this->mensagem = 'Erro no preenchimento do formulário. ';

            return false;
        }

        return true;
    }
}
