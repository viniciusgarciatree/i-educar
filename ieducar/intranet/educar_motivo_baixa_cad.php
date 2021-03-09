<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Motivo Baixa" );
        $this->processoAp = "600";
    }
}

class indice extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    var $pessoa_logada;

    var $cod_motivo_baixa;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_motivo_baixa;
    var $descricao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_biblioteca;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->cod_motivo_baixa=$_GET["cod_motivo_baixa"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 600, $this->pessoa_logada, 11,  "educar_motivo_baixa_lst.php" );

        if( is_numeric( $this->cod_motivo_baixa ) )
        {

            $obj = new clsPmieducarMotivoBaixa( $this->cod_motivo_baixa );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

            if( $obj_permissoes->permissao_excluir( 600, $this->pessoa_logada, 11 ) )
            {
                $this->fexcluir = true;
            }

                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_motivo_baixa_det.php?cod_motivo_baixa={$registro["cod_motivo_baixa"]}" : "educar_motivo_baixa_lst.php";
        $this->nome_url_cancelar = "Cancelar";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $this->breadcrumb($nomeMenu . ' motivo de baixa', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);

        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_motivo_baixa", $this->cod_motivo_baixa );

    //foreign keys
    $this->inputsHelper()->dynamic(array('instituicao', 'escola', 'biblioteca'));

        // text
        $this->campoTexto( "nm_motivo_baixa", "Motivo Baixa", $this->nm_motivo_baixa, 30, 255, true );
        $this->campoMemo( "descricao", "Descri&ccedil;&atilde;o", $this->descricao, 60, 5, false );
    }

    function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 600, $this->pessoa_logada, 11,  "educar_motivo_baixa_lst.php" );

        $obj = new clsPmieducarMotivoBaixa( null, null, $this->pessoa_logada, $this->nm_motivo_baixa, $this->descricao, null, null, 1, $this->ref_cod_biblioteca );
        $this->cod_motivo_baixa = $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
      $obj->cod_motivo_baixa = $this->cod_motivo_baixa;
            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            $this->simpleRedirect('educar_motivo_baixa_lst.php');
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 600, $this->pessoa_logada, 11,  "educar_motivo_baixa_lst.php" );

        $obj = new clsPmieducarMotivoBaixa($this->cod_motivo_baixa, $this->pessoa_logada, null, $this->nm_motivo_baixa, $this->descricao, null, null, 1, $this->ref_cod_biblioteca);
        $editou = $obj->edita();
        if( $editou )
        {
            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_motivo_baixa_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

    function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir( 600, $this->pessoa_logada, 11,  "educar_motivo_baixa_lst.php" );

        $obj = new clsPmieducarMotivoBaixa($this->cod_motivo_baixa, $this->pessoa_logada, null, null, null, null, null, 0);
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_motivo_baixa_lst.php');
        }

        $this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";

        return false;
    }
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();
?>
