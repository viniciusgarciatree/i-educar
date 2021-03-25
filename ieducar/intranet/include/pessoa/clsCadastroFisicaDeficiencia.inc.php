<?php

use iEducar\Legacy\Model;

class clsCadastroFisicaDeficiencia extends Model
{
    public $ref_idpes;
    public $ref_cod_deficiencia;

    /**
     * Construtor (PHP 4)
     *
     * @return object
     */
    public function __construct($ref_idpes = null, $ref_cod_deficiencia = null)
    {
        $db = new clsBanco();
        $this->_schema = 'cadastro.';
        $this->_tabela = "{$this->_schema}fisica_deficiencia";

        $this->_campos_lista = $this->_todos_campos = 'ref_idpes, ref_cod_deficiencia';

        if (is_numeric($ref_idpes)) {
            $this->ref_idpes = $ref_idpes;
        }
        if (is_numeric($ref_cod_deficiencia)) {
            $this->ref_cod_deficiencia = $ref_cod_deficiencia;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_idpes) && is_numeric($this->ref_cod_deficiencia)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_idpes)) {
                $campos .= "{$gruda}ref_idpes";
                $valores .= "{$gruda}'{$this->ref_idpes}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_deficiencia)) {
                $campos .= "{$gruda}ref_cod_deficiencia";
                $valores .= "{$gruda}'{$this->ref_cod_deficiencia}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");
            $db->Consulta("UPDATE cadastro.deficiencia SET updated_at = now() WHERE cod_deficiencia = {$this->ref_cod_deficiencia}");

            return true;
        }

        return false;
    }

    /**
     * Edita os dados de um registro
     *
     * @return bool
     */
    public function edita()
    {
        if (is_numeric($this->ref_idpes) && is_numeric($this->ref_cod_deficiencia)) {
            $db = new clsBanco();
            $set = '';

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_idpes = '{$this->ref_idpes}' AND ref_cod_deficiencia = '{$this->ref_cod_deficiencia}'");
                $db->Consulta("UPDATE cadastro.deficiencia SET updated_at = now() WHERE cod_deficiencia = {$this->ref_cod_deficiencia}");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @return array
     */
    public function lista($int_ref_idpes = null, $int_ref_cod_deficiencia = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_idpes)) {
            $filtros .= "{$whereAnd} ref_idpes = '{$int_ref_idpes}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_deficiencia)) {
            $filtros .= "{$whereAnd} ref_cod_deficiencia = '{$int_ref_cod_deficiencia}'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} {$filtros}");

        $db->Consulta($sql);

        if ($countCampos > 1) {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                $tupla['_total'] = $this->_total;
                $resultado[] = $tupla;
            }
        } else {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $resultado[] = $tupla[$this->_campos_lista];
            }
        }
        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->ref_idpes) && is_numeric($this->ref_cod_deficiencia)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_idpes = '{$this->ref_idpes}' AND ref_cod_deficiencia = '{$this->ref_cod_deficiencia}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function existe()
    {
        if (is_numeric($this->ref_idpes) && is_numeric($this->ref_cod_deficiencia)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_idpes = '{$this->ref_idpes}' AND ref_cod_deficiencia = '{$this->ref_cod_deficiencia}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Exclui um registro
     *
     * @return bool
     */
    public function excluir()
    {
        if (is_numeric($this->ref_idpes) && is_numeric($this->ref_cod_deficiencia)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_idpes = '{$this->ref_idpes}' AND ref_cod_deficiencia = '{$this->ref_cod_deficiencia}'");
            $db->Consulta("UPDATE cadastro.deficiencia SET updated_at = now() WHERE cod_deficiencia = {$this->ref_cod_deficiencia}");

            return true;
        }

        return false;
    }
}
