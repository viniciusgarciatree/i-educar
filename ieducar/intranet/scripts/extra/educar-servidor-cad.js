

  /**
  * Carrega as opções de um campo select de funções via Ajax
  */
  function getFuncao(id_campo)
  {
    var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
    var campoFuncao      = document.getElementById(id_campo);
    campoFuncao.length   = 1;

    if (campoFuncao) {
    campoFuncao.disabled = true;
    campoFuncao.options[0].text = 'Carregando funções';

    var xml = new ajax(atualizaLstFuncao,id_campo);
    xml.envia("educar_funcao_xml.php?ins="+campoInstituicao+"&professor=true");
  }
    else {
    campoFuncao.options[0].text = 'Selecione';
  }
  }

  /**
  * Parse de resultado da chamada Ajax de getFuncao(). Adiciona cada item
  * retornado como option do select
  */
  function atualizaLstFuncao(xml)
  {
    var campoFuncao = document.getElementById(arguments[1]);

    campoFuncao.length = 1;
    campoFuncao.options[0].text = 'Selecione uma função';
    campoFuncao.disabled = false;

    funcaoChange(campoFuncao);

    var funcoes = xml.getElementsByTagName('funcao');
    if (funcoes.length) {
    for (var i = 0; i < funcoes.length; i++) {
    campoFuncao.options[campoFuncao.options.length] =
    new Option(funcoes[i].firstChild.data, funcoes[i].getAttribute('cod_funcao'), false, false);
  }
  }
    else {
    campoFuncao.options[0].text = 'A instituição não possui funções de servidores';
  }
  }


  /**
  * Altera a visibilidade de opções extras
  *
  * Quando a função escolhida para o servidor for do tipo professor, torna as
  * opções de escolha de disciplina e cursos visíveis
  *
  * É um toggle on/off
  */
  function funcaoChange(campo)
  {
    var valor = campo.value.split("-");
    var id = /[0-9]+/.exec(campo.id)[0];
    var professor = (valor[1] == true);

    var campo_img  = document.getElementById('td_disciplina['+ id +']').lastChild.lastChild;
    var campo_img2 = document.getElementById('td_curso['+ id +']').lastChild.lastChild;

    // Se for professor
    if (professor == true) {
    setVisibility(campo_img,  true);
    setVisibility(campo_img2, true);
  }
    else {
    setVisibility(campo_img,  false);
    setVisibility(campo_img2, false);
  }
  }


  /**
  * Chama as funções getFuncao e funcaoChange para todas as linhas da tabela
  * de função de servidor
  */
  function trocaTodasfuncoes() {
  for (var ct = 0; ct < tab_add_1.id; ct++) {
  // Não executa durante onload senão, funções atuais são substituídas
  if (onloadCallOnce == false) {
  getFuncao('ref_cod_funcao[' + ct + ']');
}
  funcaoChange(document.getElementById('ref_cod_funcao[' + ct + ']'));
}
}


  /**
  * Verifica se ref_cod_instituicao existe via DOM e dá um bind no evento
  * onchange do elemento para executar a função trocaTodasfuncoes()
  */
  if (document.getElementById('ref_cod_instituicao')) {
  var ref_cod_instituicao = document.getElementById('ref_cod_instituicao');

  // Função anônima para evento onchance do select de instituição
  ref_cod_instituicao.onchange = function() {
  trocaTodasfuncoes();
  var xml = new ajax(function(){});
  xml.envia("educar_limpa_sessao_curso_disciplina_servidor.php");
}
}


  /**
  * Chama as funções funcaoChange e getFuncao após a execução da função addRow
  */
  tab_add_1.afterAddRow = function () {
  funcaoChange(document.getElementById('ref_cod_funcao['+(tab_add_1.id - 1)+']'));
  getFuncao('ref_cod_funcao['+(tab_add_1.id-1)+']');
}


  /**
  * Variável de estado, deve ser checada por funções que queiram executar ou
  * não um trecho de código apenas durante o onload
  */
  var onloadCallOnce = true;
  window.onload = function() {
  trocaTodasfuncoes();
  onloadCallOnce = false;
}


  function getArrayHora(hora) {
  var array_h;
  if (hora) {
  array_h = hora.split(":");
}
  else {
  array_h = new Array(0,0);
}

  return array_h;
}

  function acao2()
  {
    var total_horas_alocadas = getArrayHora(document.getElementById('total_horas_alocadas').value);
    var carga_horaria = (document.getElementById('carga_horaria').value).replace(',', '.');

    if (parseFloat(total_horas_alocadas) > parseFloat(carga_horaria)) {
    alert('Atenção, carga horária deve ser maior que horas alocadas!');

    return false;
  }
    else {
    acao();
  }
  }

  if (document.getElementById('total_horas_alocadas')) {
  document.getElementById('total_horas_alocadas').style.textAlign='right';
}


  function popless()
  {
    var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
    var campoServidor = document.getElementById('cod_servidor').value;
    pesquisa_valores_popless1('educar_servidor_disciplina_lst.php?ref_cod_servidor='+campoServidor+'&ref_cod_instituicao='+campoInstituicao, '');
  }

  function popCurso()
  {
    var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
    var campoServidor = document.getElementById('cod_servidor').value;
    pesquisa_valores_popless('educar_servidor_curso_lst.php?ref_cod_servidor='+campoServidor+'&ref_cod_instituicao='+campoInstituicao, '');
  }

  function pesquisa_valores_popless1(caminho, campo)
  {
    new_id = DOM_divs.length;
    div = 'div_dinamico_' + new_id;
    if (caminho.indexOf('?') == -1) {
    showExpansivel(850, 500, '<iframe src="' + caminho + '?campo=' + campo + '&div=' + div + '&popless=1" frameborder="0" height="100%" width="100%" marginheight="0" marginwidth="0" name="temp_win_popless"></iframe>', 'Pesquisa de valores' );
  }
    else {
    showExpansivel(850, 500, '<iframe src="' + caminho + '&campo=' + campo + '&div=' + div + '&popless=1" frameborder="0" height="100%" width="100%" marginheight="0" marginwidth="0" name="temp_win_popless"></iframe>', 'Pesquisa de valores' );
  }
  }

  var handleGetInformacoesServidor = function(dataResponse){

  // deficiencias
  $j('#deficiencias').closest('tr').show();
  $j('#cod_docente_inep').val(dataResponse.inep).closest('tr').show();

  $deficiencias = $j('#deficiencias');

  $j.each(dataResponse.deficiencias, function(id, nome) {
  $deficiencias.children("[value=" + id + "]").attr('selected', '');
});

  $deficiencias.trigger('chosen:updated');
};

  function atualizaInformacoesServidor(){

  $j('#deficiencias').closest('tr').hide();
  $j('#deficiencias option').removeAttr('selected');
  $j('#deficiencias').trigger('chosen:updated');
  $j('#cod_docente_inep').closest('tr').hide();

  var servidor_id = $j('#cod_servidor').val();

  if (servidor_id != ''){
  var data = {
  servidor_id : servidor_id
};
  var options = {
  url : getResourceUrlBuilder.buildUrl('/module/Api/pessoa', 'info-servidor', {}),
  dataType : 'json',
  data : data,
  success : handleGetInformacoesServidor
};
  getResources(options);
}
}
  $j(document).ready(function() {

  atualizaInformacoesServidor();

  // fixup multipleSearchDeficiencias size:
  $j('#deficiencias_chzn ul').css('width', '307px');
  $j('#deficiencias_chzn input').css('height', '25px');

  $j('#cod_servidor').attr('onchange', 'atualizaInformacoesServidor();');
});

