<?php

class Portabilis_View_Helper_Input_Resource_SimpleSearchPaisSemBrasil extends Portabilis_View_Helper_Input_SimpleSearch
{
    protected function resourceValue($id)
    {
        if ($id) {
            $sql = 'select nome from public.pais where idpais = $1';
            $options = ['params' => $id, 'return_only' => 'first-field'];
            $nome = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);

            return Portabilis_String_Utils::toLatin1($nome, ['transform' => true, 'escape' => false]);
        }
    }

    public function simpleSearchPaisSemBrasil($attrName, $options = [])
    {
        $defaultOptions = [
            'objectName' => 'pais',
            'apiController' => 'PaisSemBrasil',
            'apiResource' => 'pais-sem-brasil-search'
        ];

        $options = $this->mergeOptions($options, $defaultOptions);

        parent::simpleSearch($options['objectName'], $attrName, $options);
    }

    protected function inputPlaceholder($inputOptions)
    {
        return 'Informe o código ou nome do pais de origem';
    }
}
