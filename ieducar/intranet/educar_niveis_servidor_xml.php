<?php

    header( 'Content-type: text/xml' );

    require_once( "include/clsBanco.inc.php" );
    require_once( "include/funcoes.inc.php" );
    require_once( "include/pmieducar/geral.inc.php" );

  require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
  Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";

    if( is_numeric( $_GET["cod_cat"] ) )
    {

        $obj_nivel = new clsPmieducarNivel();
        $lst_nivel = $obj_nivel->buscaSequenciaNivel($_GET["cod_cat"]);
        if($lst_nivel)
        {
            foreach ($lst_nivel as $nivel)
            {
                echo "  <nivel cod_nivel=\"{$nivel['cod_nivel']}\">{$nivel['nm_nivel']}</nivel>\n";
            }
        }
    }
    echo "</query>";
?>
