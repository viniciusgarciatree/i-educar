<?php


use App\Models\City;
use App\Models\State;
use Illuminate\Support\Facades\Session;

require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/Geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Municipio" );
        $this->processoAp = "0";
        $this->renderBanner = false;
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
}

class indice extends clsListagem
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    var $pessoa_logada;

    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    var $titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    var $limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    var $offset;

    var $idmun;
    var $nome;
    var $sigla_uf;

    function Gerar()
    {
        Session::put([
            'campo1' => $_GET["campo1"] ? $_GET["campo1"] : Session::get('campo1')
        ]);
        Session::save();
        Session::start();

        $this->titulo = "Municipio - Listagem";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;

        //

        $this->addCabecalhos( array(
            "Cidade",
            "Estado"
        ) );


        $array_uf = array('' => 'Todos') + State::getListKeyAbbreviation()->toArray();

        if(!isset($this->sigla_uf))
        {
            $this->sigla_uf = config('legacy.app.locale.province', '');
        }




        // outros Filtros

        $this->campoLista("sigla_uf", "UF", $array_uf, $this->sigla_uf, "", false, "","", $disabled);
        $this->campoTexto( "nome", "Cidade", $this->nome, 30, 255, false );
    //  $this->campoTexto( "sigla_uf", "Sigla Uf", $this->sigla_uf, 30, 255, false );


        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $cities = City::query()
            ->with('state')
            ->where('name', 'ilike', "%{$this->nome}%")
            ->whereHas('state', function ($query) {
                $query->where('abbreviation', $this->sigla_uf);
            })
            ->orderBy('name')
            ->paginate(null, ['*'], $pageName = "pagina_{$this->nome}");

        $total = $cities->total();

        foreach ($cities as $city) {
            $campo1 = Session::get('campo1');
            $script = " onclick=\"addSel1('{$campo1}','{$city->id}','{$city->name}'); fecha();\"";
            $this->addLinhas( array(
                "<a href=\"javascript:void(0);\" {$script}>{$city->name}</a>",
                "<a href=\"javascript:void(0);\" {$script}>{$city->state->name}</a>"
            ) );
        }

        $this->addPaginador2( "educar_pesquisa_municipio_lst.php", $total, $_GET, $this->nome, $this->limite );

        $this->largura = "100%";
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
<script>
function addSel1( campo, valor, texto )
{
    obj = window.parent.document.getElementById( campo );
    novoIndice = obj.options.length;
    obj.options[novoIndice] = new Option( texto );
    opcao = obj.options[novoIndice];
    opcao.value = valor;
    opcao.selected = true;
    setTimeout( "obj.onchange", 100 );
}

function addVal1( campo,valor )
{

    obj =  window.parent.document.getElementById( campo );
    obj.value = valor;
}

function fecha()
{
    window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length*1-1));
    //window.parent.document.forms[0].submit();
}
</script>
