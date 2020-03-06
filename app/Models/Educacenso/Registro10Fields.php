<?php


namespace App\Models\Educacenso;


class Registro10Fields implements RegistroEducacenso
{
    /**
     * @var string
     */
    public $codEscola;

    /**
     * @var string
     */
    public $codigoInep;

    /**
     * @var string
     */
    public $localFuncionamento;

    /**
     * @var string
     */
    public $condicao;

    /**
     * @var string
     */
    public $aguaPotavelConsumo;

    /**
     * @var string
     */
    public $aguaRedePublica;

    /**
     * @var string
     */
    public $aguaPocoArtesiano;

    /**
     * @var string
     */
    public $aguaCacimbaCisternaPoco;

    /**
     * @var string
     */
    public $aguaFonteRio;

    /**
     * @var string
     */
    public $aguaInexistente;

    /**
     * @var string
     */
    public $energiaRedePublica;

    /**
     * @var string
     */
    public $energiaGerador;

    /**
     * @var string
     */
    public $energiaOutros;

    /**
     * @var string
     */
    public $energiaInexistente;

    /**
     * @var string
     */
    public $esgotoRedePublica;

    /**
     * @var string
     */
    public $esgotoFossaComum;

    /**
     * @var string
     */
    public $esgotoInexistente;

    /**
     * @var string
     */
    public $esgotoFossaRudimentar;

    /**
     * @var string
     */
    public $lixoColetaPeriodica;

    /**
     * @var string
     */
    public $lixoQueima;

    /**
     * @var string
     */
    public $lixoJogaOutraArea;

    /**
     * @var string
     */
    public $lixoDestinacaoPoderPublico;

    /**
     * @var string
     */
    public $lixoEnterra;

    /**
     * @var array
     */
    public $tratamentoLixo;

    /**
     * @var string
     */
    public $dependenciaSalaDiretoria;

    /**
     * @var string
     */
    public $dependenciaSalaProfessores;

    /**
     * @var string
     */
    public $dependnciaSalaSecretaria;

    /**
     * @var string
     */
    public $dependenciaLaboratorioInformatica;

    /**
     * @var string
     */
    public $dependenciaLaboratorioCiencias;

    /**
     * @var string
     */
    public $dependenciaSalaAee;

    /**
     * @var string
     */
    public $dependenciaQuadraCoberta;

    /**
     * @var string
     */
    public $dependenciaQuadraDescoberta;

    /**
     * @var string
     */
    public $dependenciaCozinha;

    /**
     * @var string
     */
    public $dependenciaBiblioteca;

    /**
     * @var string
     */
    public $dependenciaSalaLeitura;

    /**
     * @var string
     */
    public $dependenciaParqueInfantil;

    /**
     * @var string
     */
    public $dependenciaBercario;

    /**
     * @var string
     */
    public $dependenciaBanheiroFora;

    /**
     * @var string
     */
    public $dependenciaBanheiroDentro;

    /**
     * @var string
     */
    public $dependenciaBanheiroInfantil;

    /**
     * @var string
     */
    public $dependenciaBanheiroDeficiente;

    /**
     * @var string
     */
    public $dependenciaBanheiroChuveiro;

    /**
     * @var string
     */
    public $dependenciaRefeitorio;

    /**
     * @var string
     */
    public $dependenciaDispensa;

    /**
     * @var string
     */
    public $dependenciaAumoxarifado;

    /**
     * @var string
     */
    public $dependenciaAuditorio;

    /**
     * @var string
     */
    public $dependenciaPatioCoberto;

    /**
     * @var string
     */
    public $dependenciaPatioDescoberto;

    /**
     * @var string
     */
    public $dependenciaAlojamentoAluno;

    /**
     * @var string
     */
    public $dependenciaAlojamentoProfessor;

    /**
     * @var string
     */
    public $dependenciaAreaVerde;

    /**
     * @var string
     */
    public $dependenciaLavanderia;

    /**
     * @var string
     */
    public $dependenciaNenhumaRelacionada;

    /**
     * @var string
     */
    public $numeroSalasUtilizadasDentroPredio;

    /**
     * @var string
     */
    public $numeroSalasUtilizadasForaPredio;

    /**
     * @var string
     */
    public $numeroSalasClimatizadas;

    /**
     * @var string
     */
    public $numeroSalasAcessibilidade;

    /**
     * @var string
     */
    public $televisoes;

    /**
     * @var string
     */
    public $videocassetes;

    /**
     * @var string
     */
    public $dvds;

    /**
     * @var string
     */
    public $antenasParabolicas;

    /**
     * @var string
     */
    public $lousasDigitais;

    /**
     * @var string
     */
    public $copiadoras;

    /**
     * @var string
     */
    public $retroprojetores;

    /**
     * @var string
     */
    public $impressoras;

    /**
     * @var string
     */
    public $aparelhosDeSom;

    /**
     * @var string
     */
    public $projetoresDigitais;

    /**
     * @var string
     */
    public $faxs;

    /**
     * @var string
     */
    public $maquinasFotograficas;

    /**
     * @var string
     */
    public $quantidadeComputadoresAlunosMesa;

    /**
     * @var string
     */
    public $quantidadeComputadoresAlunosPortateis;

    /**
     * @var string
     */
    public $quantidadeComputadoresAlunosTablets;

    /**
     * @var string
     */
    public $computadores;

    /**
     * @var string
     */
    public $computadoresAdministrativo;

    /**
     * @var string
     */
    public $computadoresAlunos;

    /**
     * @var string
     */
    public $impressorasMultifuncionais;

    /**
     * @var string
     */
    public $totalFuncionario;

    /**
     * @var string
     */
    public $atendimentoAee;

    /**
     * @var string
     */
    public $atividadeComplementar;

    /**
     * @var string
     */
    public $localizacaoDiferenciada;

    /**
     * @var string
     */
    public $materiaisDidaticosEspecificos;

    /**
     * @var string
     */
    public $linguaMinistrada;

    /**
     * @var string
     */
    public $educacaoIndigena;

    /**
     * @var array
     */
    public $codigoLinguaIndigena;

    /**
     * @var string
     */
    public $nomeEscola;

    /**
     * @var string
     */
    public $predioCompartilhadoOutraEscola;

    /**
     * @var string
     */
    public $codigoInepEscolaCompartilhada;

    /**
     * @var string
     */
    public $codigoInepEscolaCompartilhada2;

    /**
     * @var string
     */
    public $codigoInepEscolaCompartilhada3;

    /**
     * @var string
     */
    public $codigoInepEscolaCompartilhada4;

    /**
     * @var string
     */
    public $codigoInepEscolaCompartilhada5;

    /**
     * @var string
     */
    public $codigoInepEscolaCompartilhada6;

    /**
     * @var string
     */
    public $possuiDependencias;

    /**
     * @var array
     */
    public $salasGerais;

    /**
     * @var array
     */
    public $salasFuncionais;

    /**
     * @var array
     */
    public $banheiros;

    /**
     * @var array
     */
    public $laboratorios;

    /**
     * @var array
     */
    public $salasAtividades;

    /**
     * @var array
     */
    public $dormitorios;

    /**
     * @var array
     */
    public $areasExternas;

    /**
     * @var array
     */
    public $recursosAcessibilidade;

    /**
     * @var string
     */
    public $usoInternet;

    /**
     * @var string
     */
    public $acessoInternet;

    /**
     * @var string
     */
    public $equipamentosAcessoInternet;

    /**
     * @var array
     */
    public $equipamentos;

    /**
     * @var array
     */
    public $redeLocal;

    /**
     * @var int
     */
    public $qtdSecretarioEscolar;

    /**
     * @var int
     */
    public $qtdAuxiliarAdministrativo;

    /**
     * @var int
     */
    public $qtdApoioPedagogico;

    /**
     * @var int
     */
    public $qtdCoordenadorTurno;

    /**
     * @var int
     */
    public $qtdTecnicos;

    /**
     * @var int
     */
    public $qtdBibliotecarios;

    /**
     * @var int
     */
    public $qtdSegurancas;

    /**
     * @var int
     */
    public $qtdAuxiliarServicosGerais;

    /**
     * @var int
     */
    public $qtdNutricionistas;

    /**
     * @var int
     */
    public $qtdProfissionaisPreparacao;

    /**
     * @var int
     */
    public $qtdBombeiro;

    /**
     * @var int
     */
    public $qtdPsicologo;

    /**
     * @var int
     */
    public $qtdFonoaudiologo;

    /**
     * @var array
     */
    public $orgaosColegiados;

    /**
     * @var string
     */
    public $exameSelecaoIngresso;

    /**
     * @var array
     */
    public $reservaVagasCotas;

    /**
     * @var int
     */
    public $alimentacaoEscolarAlunos;

    /**
     * @var int
     */
    public $organizacaoEnsino;

    /**
     * @var int
     */
    public $instrumentosPedagogicos;

    /**
     * @var int
     */
    public $compartilhaEspacosAtividadesIntegracao;

    /**
     * @var int
     */
    public $usaEspacosEquipamentosAtividadesRegulares;

    /**
     * @var int
     */
    public $projetoPoliticoPedagogico;

    /**
     * @var int
     */
    public $url;

    public $registro;
    public $localFuncionamentoPredioEscolar;
    public $localFuncionamentoSalasOutraEscola;
    public $localFuncionamentoGalpao;
    public $localFuncionamentoUnidadeAtendimentoSocioeducativa;
    public $localFuncionamentoUnidadePrisional;
    public $localFuncionamentoOutros;
    public $tratamentoLixoSeparacao;
    public $tratamentoLixoReaproveitamento;
    public $tratamentoLixoReciclagem;
    public $tratamentoLixoNaoFaz;
    public $dependenciaBanheiro;
    public $dependenciaBanheiroFuncionarios;
    public $dependenciaDormitorioAluno;
    public $dependenciaDormitorioProfessor;
    public $dependenciaPiscina;
    public $dependenciaSalaRepouso;
    public $dependenciaSalaArtes;
    public $dependenciaSalaMusica;
    public $dependenciaSalaDanca;
    public $dependenciaSalaMultiuso;
    public $dependenciaTerreirao;
    public $dependenciaViveiro;
    public $dependenciaSalaSecretaria;
    public $recursoCorrimao;
    public $recursoElevador;
    public $recursoPisosTateis;
    public $recursoPortaVaoLivre;
    public $recursoRampas;
    public $recursoSinalizacaoSonora;
    public $recursoSinalizacaoTatil;
    public $recursoSinalizacaoVisual;
    public $recursoNenhum;
    public $equipamentosScanner;
    public $acessoInternetAdministrativo;
    public $acessoInternetProcessoEnsino;
    public $acessoInternetAlunos;
    public $acessoInternetComunidade;
    public $acessoInternetNaoPossui;
    public $computadoresMesaAcessoInternet;
    public $dispositovosPessoaisAcessoInternet;
    public $internetBandaLarga;
    public $redeLocalCabo;
    public $redeLocalWireless;
    public $redeLocalNaoExiste;
    public $organizacaoEnsinoSerieAno;
    public $organizacaoEnsinoPeriodosSemestrais;
    public $organizacaoEnsinoGrupos;
    public $organizacaoEnsinoCiclos;
    public $organizacaoEnsinoModulos;
    public $organizacaoEnsinoAlternancia;
    public $instrumentosPedagogicosAcervo;
    public $instrumentosPedagogicosBrinquedos;
    public $instrumentosPedagogicosMateriaisCientificos;
    public $instrumentosPedagogicosEquipamentosSom;
    public $instrumentosPedagogicosInstrumentos;
    public $instrumentosPedagogicosJogos;
    public $instrumentosPedagogicosAtividadesCulturais;
    public $instrumentosPedagogicosPraticaDesportiva;
    public $instrumentosPedagogicosEducacaoIndigena;
    public $instrumentosPedagogicosEducacaoEtnicoRacial;
    public $instrumentosPedagogicosEducacaoCampo;
    public $linguaIndigena;
    public $linguaPortuguesa;
    public $linguaIndigena1;
    public $linguaIndigena2;
    public $linguaIndigena3;
    public $reservaVagasCotasAutodeclaracao;
    public $reservaVagasCotasRenda;
    public $reservaVagasCotasEscolaPublica;
    public $reservaVagasCotasPCD;
    public $reservaVagasCotasOutros;
    public $reservaVagasCotasNaoFaz;
    public $orgaoColegiadoAssociacaoPais;
    public $orgaoColegiadoAssociacaoPaisMestres;
    public $orgaoColegiadoConselho;
    public $orgaoColegiadoGremio;
    public $orgaoColegiadoOutros;
    public $orgaoColegiadoNaoExiste;

    public function hydrateModel($arrayColumns)
    {
        array_unshift($arrayColumns, null);
        unset($arrayColumns[0]);

        $this->registro = $arrayColumns[1];
        $this->codigoInep = $arrayColumns[2];
        $this->localFuncionamentoPredioEscolar = $arrayColumns[3];
        $this->localFuncionamentoSalasOutraEscola = $arrayColumns[4];
        $this->localFuncionamentoGalpao = $arrayColumns[5];
        $this->localFuncionamentoUnidadeAtendimentoSocioeducativa = $arrayColumns[6];
        $this->localFuncionamentoUnidadePrisional = $arrayColumns[7];
        $this->localFuncionamentoOutros = $arrayColumns[8];
        $this->condicao = $arrayColumns[9];
        $this->predioCompartilhadoOutraEscola = $arrayColumns[10];
        $this->codigoInepEscolaCompartilhada = $arrayColumns[11];
        $this->codigoInepEscolaCompartilhada2 = $arrayColumns[12];
        $this->codigoInepEscolaCompartilhada3 = $arrayColumns[13];
        $this->codigoInepEscolaCompartilhada4 = $arrayColumns[14];
        $this->codigoInepEscolaCompartilhada5 = $arrayColumns[15];
        $this->codigoInepEscolaCompartilhada6 = $arrayColumns[16];
        $this->aguaPotavelConsumo = $arrayColumns[17];
        $this->aguaRedePublica = $arrayColumns[18];
        $this->aguaPocoArtesiano = $arrayColumns[19];
        $this->aguaCacimbaCisternaPoco = $arrayColumns[20];
        $this->aguaFonteRio = $arrayColumns[21];
        $this->aguaInexistente = $arrayColumns[22];
        $this->energiaRedePublica = $arrayColumns[23];
        $this->energiaGerador = $arrayColumns[24];
        $this->energiaOutros = $arrayColumns[25];
        $this->energiaInexistente = $arrayColumns[26];
        $this->esgotoRedePublica = $arrayColumns[27];
        $this->esgotoFossaComum = $arrayColumns[28];
        $this->esgotoFossaRudimentar = $arrayColumns[29];
        $this->esgotoInexistente = $arrayColumns[30];
        $this->lixoColetaPeriodica = $arrayColumns[31];
        $this->lixoQueima = $arrayColumns[32];
        $this->lixoEnterra = $arrayColumns[33];
        $this->lixoDestinacaoPoderPublico = $arrayColumns[34];
        $this->lixoJogaOutraArea = $arrayColumns[35];
        $this->tratamentoLixoSeparacao = $arrayColumns[36];
        $this->tratamentoLixoReaproveitamento = $arrayColumns[37];
        $this->tratamentoLixoReciclagem = $arrayColumns[38];
        $this->tratamentoLixoNaoFaz = $arrayColumns[39];
        $this->dependenciaAumoxarifado = $arrayColumns[40];
        $this->dependenciaAreaVerde = $arrayColumns[41];
        $this->dependenciaAuditorio = $arrayColumns[42];
        $this->dependenciaBanheiro = $arrayColumns[43];
        $this->dependenciaBanheiroDeficiente = $arrayColumns[44];
        $this->dependenciaBanheiroInfantil = $arrayColumns[45];
        $this->dependenciaBanheiroFuncionarios = $arrayColumns[46];
        $this->dependenciaBanheiroChuveiro = $arrayColumns[47];
        $this->dependenciaBiblioteca = $arrayColumns[48];
        $this->dependenciaCozinha = $arrayColumns[49];
        $this->dependenciaDispensa = $arrayColumns[50];
        $this->dependenciaDormitorioAluno = $arrayColumns[51];
        $this->dependenciaDormitorioProfessor = $arrayColumns[52];
        $this->dependenciaLaboratorioCiencias = $arrayColumns[53];
        $this->dependenciaLaboratorioInformatica = $arrayColumns[54];
        $this->dependenciaParqueInfantil = $arrayColumns[55];
        $this->dependenciaPatioCoberto = $arrayColumns[56];
        $this->dependenciaPatioDescoberto = $arrayColumns[57];
        $this->dependenciaPiscina = $arrayColumns[58];
        $this->dependenciaQuadraCoberta = $arrayColumns[59];
        $this->dependenciaQuadraDescoberta = $arrayColumns[60];
        $this->dependenciaRefeitorio = $arrayColumns[61];
        $this->dependenciaSalaRepouso = $arrayColumns[62];
        $this->dependenciaSalaArtes = $arrayColumns[63];
        $this->dependenciaSalaMusica = $arrayColumns[64];
        $this->dependenciaSalaDanca = $arrayColumns[65];
        $this->dependenciaSalaMultiuso = $arrayColumns[66];
        $this->dependenciaTerreirao = $arrayColumns[67];
        $this->dependenciaViveiro = $arrayColumns[68];
        $this->dependenciaSalaDiretoria = $arrayColumns[69];
        $this->dependenciaSalaLeitura = $arrayColumns[70];
        $this->dependenciaSalaProfessores = $arrayColumns[71];
        $this->dependenciaSalaAee = $arrayColumns[72];
        $this->dependenciaSalaSecretaria = $arrayColumns[73];
        $this->dependenciaNenhumaRelacionada = $arrayColumns[74];
        $this->recursoCorrimao = $arrayColumns[75];
        $this->recursoElevador = $arrayColumns[76];
        $this->recursoPisosTateis = $arrayColumns[77];
        $this->recursoPortaVaoLivre = $arrayColumns[78];
        $this->recursoRampas = $arrayColumns[79];
        $this->recursoSinalizacaoSonora = $arrayColumns[80];
        $this->recursoSinalizacaoTatil = $arrayColumns[81];
        $this->recursoSinalizacaoVisual = $arrayColumns[82];
        $this->recursoNenhum = $arrayColumns[83];
        $this->numeroSalasUtilizadasDentroPredio = $arrayColumns[84];
        $this->numeroSalasUtilizadasForaPredio = $arrayColumns[85];
        $this->numeroSalasClimatizadas = $arrayColumns[86];
        $this->numeroSalasAcessibilidade = $arrayColumns[87];
        $this->antenasParabolicas = $arrayColumns[88];
        $this->computadores = $arrayColumns[89];
        $this->copiadoras = $arrayColumns[90];
        $this->impressoras = $arrayColumns[91];
        $this->impressorasMultifuncionais = $arrayColumns[92];
        $this->equipamentosScanner = $arrayColumns[93];
        $this->dvds = $arrayColumns[94];
        $this->aparelhosDeSom = $arrayColumns[95];
        $this->televisoes = $arrayColumns[96];
        $this->lousasDigitais = $arrayColumns[97];
        $this->projetoresDigitais = $arrayColumns[98];
        $this->quantidadeComputadoresAlunosMesa = $arrayColumns[99];
        $this->quantidadeComputadoresAlunosPortateis = $arrayColumns[100];
        $this->quantidadeComputadoresAlunosTablets = $arrayColumns[101];
        $this->acessoInternetAdministrativo = $arrayColumns[102];
        $this->acessoInternetProcessoEnsino = $arrayColumns[103];
        $this->acessoInternetAlunos = $arrayColumns[104];
        $this->acessoInternetComunidade = $arrayColumns[105];
        $this->acessoInternetNaoPossui = $arrayColumns[106];
        $this->computadoresMesaAcessoInternet = $arrayColumns[107];
        $this->dispositovosPessoaisAcessoInternet = $arrayColumns[108];
        $this->acessoInternet = $arrayColumns[109];
        $this->redeLocalCabo = $arrayColumns[110];
        $this->redeLocalWireless = $arrayColumns[111];
        $this->redeLocalNaoExiste = $arrayColumns[112];
        $this->qtdAuxiliarAdministrativo = $arrayColumns[113];
        $this->qtdAuxiliarServicosGerais = $arrayColumns[114];
        $this->qtdBibliotecarios = $arrayColumns[115];
        $this->qtdBombeiro = $arrayColumns[116];
        $this->qtdCoordenadorTurno = $arrayColumns[117];
        $this->qtdFonoaudiologo = $arrayColumns[118];
        $this->qtdNutricionistas = $arrayColumns[119];
        $this->qtdPsicologo = $arrayColumns[120];
        $this->qtdProfissionaisPreparacao = $arrayColumns[121];
        $this->qtdApoioPedagogico = $arrayColumns[122];
        $this->qtdSecretarioEscolar = $arrayColumns[123];
        $this->qtdSegurancas = $arrayColumns[124];
        $this->qtdTecnicos = $arrayColumns[125];
        $this->alimentacaoEscolarAlunos = $arrayColumns[126];
        $this->organizacaoEnsinoSerieAno = $arrayColumns[127];
        $this->organizacaoEnsinoPeriodosSemestrais = $arrayColumns[128];
        $this->organizacaoEnsinoCiclos = $arrayColumns[129];
        $this->organizacaoEnsinoGrupos = $arrayColumns[130];
        $this->organizacaoEnsinoModulos = $arrayColumns[131];
        $this->organizacaoEnsinoAlternancia = $arrayColumns[132];
        $this->instrumentosPedagogicosAcervo = $arrayColumns[133];
        $this->instrumentosPedagogicosBrinquedos = $arrayColumns[134];
        $this->instrumentosPedagogicosMateriaisCientificos = $arrayColumns[135];
        $this->instrumentosPedagogicosEquipamentosSom = $arrayColumns[136];
        $this->instrumentosPedagogicosInstrumentos = $arrayColumns[137];
        $this->instrumentosPedagogicosJogos = $arrayColumns[138];
        $this->instrumentosPedagogicosAtividadesCulturais = $arrayColumns[139];
        $this->instrumentosPedagogicosPraticaDesportiva = $arrayColumns[140];
        $this->instrumentosPedagogicosEducacaoIndigena = $arrayColumns[141];
        $this->instrumentosPedagogicosEducacaoEtnicoRacial = $arrayColumns[142];
        $this->instrumentosPedagogicosEducacaoCampo = $arrayColumns[143];
        $this->educacaoIndigena = $arrayColumns[144];
        $this->linguaIndigena = $arrayColumns[145];
        $this->linguaPortuguesa = $arrayColumns[146];
        $this->linguaIndigena1 = $arrayColumns[147];
        $this->linguaIndigena2 = $arrayColumns[148];
        $this->linguaIndigena3 = $arrayColumns[149];
        $this->exameSelecaoIngresso = $arrayColumns[150];
        $this->reservaVagasCotasAutodeclaracao = $arrayColumns[151];
        $this->reservaVagasCotasRenda = $arrayColumns[152];
        $this->reservaVagasCotasEscolaPublica = $arrayColumns[153];
        $this->reservaVagasCotasPCD = $arrayColumns[154];
        $this->reservaVagasCotasOutros = $arrayColumns[155];
        $this->reservaVagasCotasNaoFaz = $arrayColumns[156];
        $this->url = $arrayColumns[157];
        $this->compartilhaEspacosAtividadesIntegracao = $arrayColumns[158];
        $this->usaEspacosEquipamentosAtividadesRegulares = $arrayColumns[159];
        $this->orgaoColegiadoAssociacaoPais = $arrayColumns[160];
        $this->orgaoColegiadoAssociacaoPaisMestres = $arrayColumns[161];
        $this->orgaoColegiadoConselho = $arrayColumns[162];
        $this->orgaoColegiadoGremio = $arrayColumns[163];
        $this->orgaoColegiadoOutros = $arrayColumns[164];
        $this->orgaoColegiadoNaoExiste = $arrayColumns[165];
        $this->projetoPoliticoPedagogico = $arrayColumns[166];
    }
}
