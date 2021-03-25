<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_tipo_ocorrencia_disciplinar;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_tipo;
    public $descricao;
    public $max_ocorrencias;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Tipo Ocorr&ecirc;ncia Disciplinar - Detalhe';

        $this->cod_tipo_ocorrencia_disciplinar=$_GET['cod_tipo_ocorrencia_disciplinar'];

        $tmp_obj = new clsPmieducarTipoOcorrenciaDisciplinar($this->cod_tipo_ocorrencia_disciplinar);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('educar_tipo_ocorrencia_disciplinar_lst.php');
        }

        $obj_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
        $obj_instituicao_det = $obj_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $obj_instituicao_det['nm_instituicao'];

        $obj_permissao = new clsPermissoes();
        $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1) {
            if ($registro['ref_cod_instituicao']) {
                $this->addDetalhe([ 'Institui&ccedil;&atilde;o', "{$registro['ref_cod_instituicao']}"]);
            }
        }
        if ($registro['nm_tipo']) {
            $this->addDetalhe([ 'Tipo Ocorr&ecirc;ncia Disciplinar', "{$registro['nm_tipo']}"]);
        }
        if ($registro['descricao']) {
            $this->addDetalhe([ 'Descri&ccedil;&atilde;o', "{$registro['descricao']}"]);
        }
        if ($registro['max_ocorrencias']) {
            $this->addDetalhe([ 'M&aacute;ximo Ocorr&ecirc;ncias', "{$registro['max_ocorrencias']}"]);
        }

        if ($obj_permissao->permissao_cadastra(580, $this->pessoa_logada, 3)) {
            $this->url_novo = 'educar_tipo_ocorrencia_disciplinar_cad.php';
            $this->url_editar = "educar_tipo_ocorrencia_disciplinar_cad.php?cod_tipo_ocorrencia_disciplinar={$registro['cod_tipo_ocorrencia_disciplinar']}";
        }
        $this->url_cancelar = 'educar_tipo_ocorrencia_disciplinar_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do tipo de ocorrência disciplinar', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'i-Educar - Tipo Ocorr&ecirc;ncia Disciplinar';
        $this->processoAp = '580';
    }
};
