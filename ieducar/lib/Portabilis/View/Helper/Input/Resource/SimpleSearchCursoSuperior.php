<?php

require_once 'lib/Portabilis/View/Helper/Input/SimpleSearch.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/Portabilis/String/Utils.php';

class Portabilis_View_Helper_Input_Resource_SimpleSearchCursoSuperior extends Portabilis_View_Helper_Input_SimpleSearch
{
    protected function resourceValue($id)
    {
        if ($id) {
            $sql = '
                select curso_id || \' - \' || nome || \' / \' || coalesce(
                (
                    case grau_academico
                        when 1 then \'Tecnológico\'
                        when 2 then \'Licenciatura\'
                        when 3 then \'Bacharelado\'
                        when 4 then \'Sequencial\'
                    end
                ), \'\') as nome
                from modules.educacenso_curso_superior
                where id = $1
            ';

            $options = ['params' => $id, 'return_only' => 'first-row'];
            $curso_superior = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
            $nome = $curso_superior['nome'];

            return Portabilis_String_Utils::toLatin1($nome, ['transform' => true, 'escape' => false]);
        }
    }

    public function simpleSearchCursoSuperior($attrName, $options = [])
    {
        $defaultOptions = [
            'objectName' => 'cursosuperior',
            'apiController' => 'CursoSuperior',
            'apiResource' => 'cursosuperior-search',
            'showIdOnValue' => false
        ];

        $options = $this->mergeOptions($options, $defaultOptions);

        parent::simpleSearch($options['objectName'], $attrName, $options);
    }

    protected function inputPlaceholder($inputOptions)
    {
        return 'Informe o código ou nome do curso';
    }
}
