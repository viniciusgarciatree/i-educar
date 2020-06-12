<?php

namespace iEducar\Modules\Servidores\Model;

class FuncaoExercida
{
    const DOCENTE = 1;
    const AUXILIAR_EDUCACIONAL = 2;
    const MONITOR_ATIVIDADE_COMPLEMENTAR = 3;
    const INTERPRETE_LIBRAS = 4;
    const DOCENTE_TITULAR_EAD = 5;
    const DOCENTE_TUTOR_EAD = 6;
    const GUIA_INTERPRETE_LIBRAS = 7;
    const APOIO_ALUNOS_DEFICIENCIA = 8;

    public static function getDescriptiveValues()
    {
        return [
            self::DOCENTE => 'Docente',
            self::AUXILIAR_EDUCACIONAL => 'Auxiliar/Assistente educacional',
            self::MONITOR_ATIVIDADE_COMPLEMENTAR => 'Profissional/Monitor de atividade complementar',
            self::INTERPRETE_LIBRAS => 'Tradutor Intérprete de LIBRAS',
            self::DOCENTE_TITULAR_EAD => 'Docente titular - Coordenador de tutoria (de módulo ou disciplina) - EAD',
            self::DOCENTE_TUTOR_EAD => 'Docente tutor - Auxiliar (de módulo ou disciplina) - EAD',
            self::GUIA_INTERPRETE_LIBRAS => 'Guia-Intérprete',
            self::APOIO_ALUNOS_DEFICIENCIA => 'Profissional de apoio escolar para aluno(a)s com deficiência (Lei 13.146/2015)',
        ];
    }

}
