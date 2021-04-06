<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_infra_comodo_funcao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_funcao;
    public $desc_funcao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_escola;
    public $ref_cod_instituicao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_infra_comodo_funcao=$_GET['cod_infra_comodo_funcao'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(572, $this->pessoa_logada, 7, 'educar_infra_comodo_funcao_lst.php');

        /*if( is_numeric( $this->cod_infra_comodo_funcao ) )
        {

            $obj = new clsPmieducarInfraComodoFuncao();
            $lst  = $obj->lista( $this->cod_infra_comodo_funcao );
            if (is_array($lst))
            {
                $registro = array_shift($lst);
                if( $registro )
                {
                    foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                        $this->$campo = $val;

                    //** verificao de permissao para exclusao
                    $this->fexcluir = $obj_permissoes->permissao_excluir(572,$this->pessoa_logada,7);
                    //**

                    $retorno = "Editar";
                }else{
                    header( "Location: educar_infra_comodo_funcao_lst.php" );
                    die();
                }
            }
        }*/
//      $this->url_cancelar = ($retorno == "Editar") ? "educar_infra_comodo_funcao_det.php?cod_infra_comodo_funcao={$registro["cod_infra_comodo_funcao"]}" : "educar_infra_comodo_funcao_lst.php";
        $this->nome_url_cancelar = 'Cancelar';
        $this->script_cancelar = 'window.parent.fechaExpansivel("div_dinamico_"+(parent.DOM_divs.length-1));';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_infra_comodo_funcao', $this->cod_infra_comodo_funcao);
        if ($_GET['precisa_lista']) {
            $obrigatorio = true;
            $get_escola = true;
            include('include/pmieducar/educar_campo_lista.php');
        } else {
            $this->campoOculto('ref_cod_instituicao', $this->ref_cod_instituicao);
            $this->campoOculto('ref_cod_escola', $this->ref_cod_escola);
        }
        // text
        $this->campoTexto('nm_funcao', 'Tipo', $this->nm_funcao, 30, 255, true);
        $this->campoMemo('desc_funcao', 'Descrição do tipo', $this->desc_funcao, 60, 5, false);

        // data
    }

    public function Novo()
    {
        $obj = new clsPmieducarInfraComodoFuncao(null, null, $this->pessoa_logada, $this->nm_funcao, $this->desc_funcao, null, null, 1, $this->ref_cod_escola);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            echo "<script>
                        if (parent.document.getElementById('ref_cod_infra_comodo_funcao').disabled)
                            parent.document.getElementById('ref_cod_infra_comodo_funcao').options[0] = new Option('Selecione uma função cômodo', '', false, false);
                        parent.document.getElementById('ref_cod_infra_comodo_funcao').options[parent.document.getElementById('ref_cod_infra_comodo_funcao').options.length] = new Option('$this->nm_funcao', '$cadastrou', false, false);
                        parent.document.getElementById('ref_cod_infra_comodo_funcao').value = '$cadastrou';
                        parent.document.getElementById('ref_cod_infra_comodo_funcao').disabled = false;
                        window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length-1));
                    </script>";
            die();
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function Editar()
    {
    }

    public function Excluir()
    {
    }

    public function makeExtra()
    {
        if (! $_GET['precisa_lista']) {
            return file_get_contents(__DIR__ . '/scripts/extra/educar-infra-comodo-funcao-cad-pop.js');
        }

        return '';
    }

    public function Formular()
    {
        $this->title = 'i-Educar - Tipo de ambiente ';
        $this->processoAp = '572';
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
};
