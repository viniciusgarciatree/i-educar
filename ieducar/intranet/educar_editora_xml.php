<?php

    header( 'Content-type: text/xml' );

    require_once( "include/clsBanco.inc.php" );
    require_once( "include/funcoes.inc.php" );

  require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
  Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn('colecoes');

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"colecoes\">\n";

    if( is_numeric( $_GET["bib"] ) )
    {
        $db = new clsBanco();
        $db->Consulta( "
        SELECT
            cod_acervo_editora,
            nm_editora ||
            CASE WHEN cidade IS NULL OR cidade = ''
                      THEN  ''
                 ELSE ' - ' || cidade ||
                      CASE WHEN bairro IS NULL OR bairro = ''
                                THEN ''
                           ELSE ', ' || bairro||
                            CASE WHEN logradouro IS NULL OR logradouro = ''
                                      THEN ''
                                 ELSE ', ' || CASE WHEN ref_idtlog IS NULL OR ref_idtlog = '' THEN '' ELSE initcap(descricao) || ' ' END ||logradouro
                            END
                      END
            END AS nm_editora
        FROM pmieducar.acervo_editora
       LEFT JOIN urbano.tipo_logradouro ON (tipo_logradouro.idtlog = acervo_editora.ref_idtlog)
       WHERE
            ativo = 1
            AND ref_cod_biblioteca = '{$_GET["bib"]}'
        ORDER BY
            nm_editora ASC
        ");

        if ($db->numLinhas())
        {
            while ( $db->ProximoRegistro() )
            {
                list( $cod, $nome) = $db->Tupla();
                $nome = str_replace('&', 'e', $nome);
                echo "  <acervo_editora cod_editora=\"{$cod}\" >{$nome}</acervo_editora>\n";
            }
        }
    }
    echo "</query>";
?>
