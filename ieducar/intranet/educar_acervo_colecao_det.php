<?php


require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Cole&ccedil&atilde;o" );
        $this->processoAp = "593";
    }
}

class indice extends clsDetalhe
{
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    var $titulo;

    var $cod_acervo_colecao;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_colecao;
    var $descricao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Gerar()
    {
        $this->titulo = "Cole&ccedil&atilde;o - Detalhe";

        $this->cod_acervo_colecao=$_GET["cod_acervo_colecao"];

        $tmp_obj = new clsPmieducarAcervoColecao( $this->cod_acervo_colecao );
        $registro = $tmp_obj->detalhe();

        $obj_ref_cod_biblioteca = new clsPmieducarBiblioteca( $registro["ref_cod_biblioteca"] );
        $det_ref_cod_biblioteca = $obj_ref_cod_biblioteca->detalhe();
        $registro["ref_cod_biblioteca"] = $det_ref_cod_biblioteca["nm_biblioteca"];

        $registro["ref_cod_instituicao"] = $det_ref_cod_biblioteca["ref_cod_instituicao"];
        $obj_ref_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
        $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
        $registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];

        $registro["ref_cod_escola"] = $det_ref_cod_biblioteca["ref_cod_escola"];
        $obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_cod_escola"] );
        $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
        $idpes = $det_ref_cod_escola["ref_idpes"];

        $obj_escola = new clsPessoaJuridica($idpes);
        $obj_escola_det = $obj_escola->detalhe();
        $registro["ref_cod_escola"] = $obj_escola_det["fantasia"];

    $obj_permissoes = new clsPermissoes();
    $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

    if( ! $registro )
    {
        $this->simpleRedirect('educar_acervo_colecao_lst.php');
    }

    if ($nivel_usuario == 1)
    {
      if( $registro["ref_cod_instituicao"] )
      {
        $this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$registro["ref_cod_instituicao"]}") );
      }
    }

    if ($nivel_usuario == 1 || $nivel_usuario == 2)
    {
        if( $registro["ref_cod_escola"] )
        {
            $this->addDetalhe( array( "Escola", "{$registro["ref_cod_escola"]}") );
        }
    }

    if( $registro["ref_cod_biblioteca"] )
    {
        $this->addDetalhe( array( "Biblioteca", "{$registro["ref_cod_biblioteca"]}") );
    }


    if( $registro["cod_acervo_colecao"] )
    {
        $this->addDetalhe( array( "C&oacute;digo Cole&ccedil;&atilde;o", "{$registro["cod_acervo_colecao"]}") );
    }
    if( $registro["nm_colecao"] )
    {
        $this->addDetalhe( array( "Cole&ccedil;&atilde;o", "{$registro["nm_colecao"]}") );
    }
    if( $registro["descricao"] )
    {
        $this->addDetalhe( array( "Descri&ccedil;&atilde;o", "{$registro["descricao"]}") );
    }
    $obj_permissoes = new clsPermissoes();
    if( $obj_permissoes->permissao_cadastra( 593, $this->pessoa_logada, 11 ) )
    {
        $this->url_novo = "educar_acervo_colecao_cad.php";
        $this->url_editar = "educar_acervo_colecao_cad.php?cod_acervo_colecao={$registro["cod_acervo_colecao"]}";
    }

    $this->url_cancelar = "educar_acervo_colecao_lst.php";
    $this->largura = "100%";

    $this->breadcrumb('Detalhes da coleção', [
        url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
    ]);

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
