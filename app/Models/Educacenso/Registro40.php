<?php

namespace App\Models\Educacenso;

use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola;
use iEducar\Modules\Educacenso\Model\TratamentoLixo;
use iEducar\Modules\Educacenso\Model\RecursosAcessibilidade;
use iEducar\Modules\Educacenso\Model\UsoInternet;
use iEducar\Modules\Educacenso\Model\Equipamentos;
use iEducar\Modules\Educacenso\Model\ReservaVagasCotas;
use iEducar\Modules\Educacenso\Model\RedeLocal;
use iEducar\Modules\Educacenso\Model\OrgaosColegiados;

class Registro40 implements RegistroEducacenso, ItemOfRegistro30
{
    public $registro;

    public $inepEscola;

    public $codigoPessoa;

    public $inepGestor;

    public $cargo;

    public $criterioAcesso;

    public $especificacaoCriterioAcesso;

    public $tipoVinculo;

    public $dependenciaAdministrativa;

    public $situacaoFuncionamento;

    public function isDependenciaAdministrativaPublica()
    {
        return $this->dependenciaAdministrativa == DependenciaAdministrativaEscola::MUNICIPAL ||
            $this->dependenciaAdministrativa == DependenciaAdministrativaEscola::ESTADUAL ||
            $this->dependenciaAdministrativa == DependenciaAdministrativaEscola::FEDERAL;
    }

    public function getCodigoPessoa()
    {
        return $this->codigoPessoa;
    }

    public function getCodigoAluno()
    {
        return null;
    }

    public function getCodigoServidor()
    {
        return $this->codigoPessoa;
    }

    /**
     * Retorna a propriedade da classe correspondente ao dado no arquivo do censo
     *
     * @param int $column
     * @return string
     */
    public function getProperty($column)
    {
        // TODO: Implement getProperty() method.
    }
}
