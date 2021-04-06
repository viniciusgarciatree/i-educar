<?php

return new class extends clsListagem {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    public $limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    public $offset;

    public $cod_tipo_ensino;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_tipo;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Tipo Ensino - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $get_escola = false;
        include('include/pmieducar/educar_campo_lista.php');
        $obj_permissao = new clsPermissoes();
        $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);

        switch ($nivel_usuario) {
            case 1:
                $this->addCabecalhos([
                    'Tipo Ensino',
                    'Institui&ccedil;&atilde;o'
                ]);
                break;

            default:
                $this->addCabecalhos([
                    'Tipo Ensino'
                ]);
                break;
        }

        // Filtros de Foreign Keys

        // outros Filtros
        $this->campoTexto('nm_tipo', 'Nome Tipo', $this->nm_tipo, 30, 255, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_tipo_ensino = new clsPmieducarTipoEnsino();
        $obj_tipo_ensino->setOrderby('nm_tipo ASC');
        $obj_tipo_ensino->setLimite($this->limite, $this->offset);

        $lista = $obj_tipo_ensino->lista(
            $this->cod_tipo_ensino,
            null,
            null,
            $this->nm_tipo,
            null,
            null,
            1,
            $this->ref_cod_instituicao
        );

        $total = $obj_tipo_ensino->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $obj_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
                $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
                $registro['ref_cod_instituicao'] = $obj_cod_instituicao_det['nm_instituicao'];

                switch ($nivel_usuario) {
                    case 1:
                        $this->addLinhas([
                            "<a href=\"educar_tipo_ensino_det.php?cod_tipo_ensino={$registro['cod_tipo_ensino']}\">{$registro['nm_tipo']}</a>",
                            "<a href=\"educar_tipo_ensino_det.php?cod_tipo_ensino={$registro['cod_tipo_ensino']}\">{$registro['ref_cod_instituicao']}</a>"
                        ]);
                        break;

                    default:
                        $this->addLinhas([
                            "<a href=\"educar_tipo_ensino_det.php?cod_tipo_ensino={$registro['cod_tipo_ensino']}\">{$registro['nm_tipo']}</a>"
                        ]);
                        break;
                }
            }
        }

        //** Verificacao de permissao para exclusao

        if ($obj_permissao->permissao_cadastra(558, $this->pessoa_logada, 3)) {
            $this->acao = 'go("educar_tipo_ensino_cad.php")';
            $this->nome_acao = 'Novo';
        }
        //**

        $this->addPaginador2('educar_tipo_ensino_lst.php', $total, $_GET, $this->nome, $this->limite);
        $this->largura = '100%';

        $this->breadcrumb('Listagem de tipos de ensino', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'i-Educar - Tipo Ensino';
        $this->processoAp = '558';
    }
};
