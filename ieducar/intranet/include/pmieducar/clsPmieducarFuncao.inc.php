<?php

use iEducar\Legacy\Model;

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarFuncao extends Model
{
    public $cod_funcao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_funcao;
    public $abreviatura;
    public $professor;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;

    public function __construct($cod_funcao = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $nm_funcao = null, $abreviatura = null, $professor = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $ref_cod_instituicao = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}funcao";

        $this->_campos_lista = $this->_todos_campos = 'cod_funcao, ref_usuario_exc, ref_usuario_cad, nm_funcao, abreviatura, professor, data_cadastro, data_exclusao, ativo, ref_cod_instituicao';

        if (is_numeric($ref_usuario_exc)) {
                    $this->ref_usuario_exc = $ref_usuario_exc;
        }
        if (is_numeric($ref_usuario_cad)) {
                    $this->ref_usuario_cad = $ref_usuario_cad;
        }
        if (is_numeric($cod_funcao)) {
            $this->cod_funcao = $cod_funcao;
        }
        if (is_string($nm_funcao)) {
            $this->nm_funcao = $nm_funcao;
        }
        if (is_string($abreviatura)) {
            $this->abreviatura = $abreviatura;
        }
        if (is_numeric($professor)) {
            $this->professor = $professor;
        }
        if (is_string($data_cadastro)) {
            $this->data_cadastro = $data_cadastro;
        }
        if (is_string($data_exclusao)) {
            $this->data_exclusao = $data_exclusao;
        }
        if (is_numeric($ativo)) {
            $this->ativo = $ativo;
        }

        if (is_numeric($ref_cod_instituicao)) {
                    $this->ref_cod_instituicao = $ref_cod_instituicao;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_usuario_cad) && is_string($this->nm_funcao) && is_string($this->abreviatura) && is_numeric($this->professor) && is_numeric($this->ref_cod_instituicao)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_funcao)) {
                $nm_funcao = $db->escapeString($this->nm_funcao);
                $campos .= "{$gruda}nm_funcao";
                $valores .= "{$gruda}'{$nm_funcao}'";
                $gruda = ', ';
            }
            if (is_string($this->abreviatura)) {
                $abreviatura = $db->escapeString($this->abreviatura);
                $campos .= "{$gruda}abreviatura";
                $valores .= "{$gruda}'{$abreviatura}'";
                $gruda = ', ';
            }
            if (is_numeric($this->professor)) {
                $campos .= "{$gruda}professor";
                $valores .= "{$gruda}'{$this->professor}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_instituicao)) {
                $campos .= "{$gruda}ref_cod_instituicao";
                $valores .= "{$gruda}'{$this->ref_cod_instituicao}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_funcao_seq");
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
        if (is_numeric($this->cod_funcao) && is_numeric($this->ref_usuario_exc) && is_numeric($this->ref_cod_instituicao)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_usuario_exc)) {
                $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_funcao)) {
                $nm_funcao = $db->escapeString($this->nm_funcao);
                $set .= "{$gruda}nm_funcao = '{$nm_funcao}'";
                $gruda = ', ';
            }
            if (is_string($this->abreviatura)) {
                $abreviatura = $db->escapeString($this->abreviatura);
                $set .= "{$gruda}abreviatura = '{$abreviatura}'";
                $gruda = ', ';
            }
            if (is_numeric($this->professor)) {
                $set .= "{$gruda}professor = '{$this->professor}'";
                $gruda = ', ';
            }
            if (is_string($this->data_cadastro)) {
                $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
                $gruda = ', ';
            }
            $set .= "{$gruda}data_exclusao = NOW()";
            $gruda = ', ';
            if (is_numeric($this->ativo)) {
                $set .= "{$gruda}ativo = '{$this->ativo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_instituicao)) {
                $set .= "{$gruda}ref_cod_instituicao = '{$this->ref_cod_instituicao}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_funcao = '{$this->cod_funcao}'");

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
    public function lista($int_cod_funcao = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $str_nm_funcao = null, $str_abreviatura = null, $int_professor = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_ref_cod_instituicao = null, $int_ref_cod_curso = null)
    {
        $db = new clsBanco();

        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_cod_funcao)) {
            $filtros .= "{$whereAnd} cod_funcao = '{$int_cod_funcao}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nm_funcao)) {
            $nm_funcao = $db->escapeString($str_nm_funcao);
            $filtros .= "{$whereAnd} nm_funcao LIKE '%{$nm_funcao}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_abreviatura)) {
            $abreviatura = $db->escapeString($str_abreviatura);
            $filtros .= "{$whereAnd} abreviatura LIKE '%{$abreviatura}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_professor)) {
            $filtros .= "{$whereAnd} professor = '{$int_professor}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} ativo = '0'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
            $whereAnd = ' AND ';
        }

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
        if (is_numeric($this->cod_funcao)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_funcao = '{$this->cod_funcao}'");
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
        if (is_numeric($this->cod_funcao) && is_numeric($this->ref_cod_instituicao)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_funcao = '{$this->cod_funcao}' AND ref_cod_instituicao = '{$this->ref_cod_instituicao}'");
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
        if (is_numeric($this->cod_funcao) && is_numeric($this->ref_usuario_exc) && is_numeric($this->ref_cod_instituicao)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }
}
