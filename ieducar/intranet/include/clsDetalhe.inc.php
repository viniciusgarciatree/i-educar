<?php

use iEducar\Support\Navigation\Breadcrumb;
use Illuminate\Support\Facades\View;

require_once 'Core/Controller/Page/Abstract.php';

class clsDetalhe extends Core_Controller_Page_Abstract
{
  var $titulo;
  var $largura;
  var $detalhe = array();
  var $url_novo;
  var $caption_novo = "Novo";
  var $url_editar;
  var $url_cancelar;
  var $nome_url_cancelar = "Voltar";
  var $array_botao;
  var $array_botao_url;
  var $array_botao_url_script;

    /**
     * @deprecated
     */
  function addBanner(
      $strBannerUrl = '',
      $strBannerLateralUrl = '',
      $strBannerTitulo = '',
      $boolFechaBanner = FALSE
  ) {
      // Método permanece somente para fins de compatibilidade e evitar erros.
  }

  function addDetalhe($detalhe) {
    $this->detalhe[] = $detalhe;
  }

  public function addHtml($html)
  {
      $this->detalhe['html'] = $html;
  }

  function Gerar() {
    return FALSE;
  }

  function RenderHTML()
  {
    ob_start();

    $this->_preRender();
    $this->Gerar();

    $retorno = ob_get_contents();

    ob_end_clean();

    $width = empty($this->largura) ? '' : 'width=' . $this->largura;

    $barra = '<b>' . $this->titulo . '</b>';

    if ($this->locale){
        app(Breadcrumb::class)->setLegacy($this->locale);
    }

    View::share('title', $this->titulo);

    $retorno .= "
      <!-- detalhe begin -->
      <table class='tableDetalhe' $width border='0' cellpadding='2' cellspacing='2'>
        <tr>
          <td class='formdktd' colspan='2' height='24'>{$barra}</td>
        </tr>
      ";

    if (empty($this->detalhe)) {
      $retorno .= "<tr><td class='tableDetalheLinhaSim' colspan='2'>N&atilde;o h&aacute; informa&ccedil;&atilde;o a ser apresentada.</td></tr>\n";
    }
    else
    {
      if (is_array($this->detalhe))
      {
        reset($this->detalhe);

        $campo_anterior = "";
        $md = TRUE;

        foreach ($this->detalhe as $pardetalhe)
        {
          if (is_array($pardetalhe))
          {
            $campo = $pardetalhe[0].":";
            $texto = $pardetalhe[1];

            if ($campo == $campo_anterior)
            {
              $campo = "";
            }
            else
            {
              $campo_anterior = $campo;
              $md = !$md;
            }

            if ($campo == "-:")
            {
              if (empty($texto))
              {
                $texto = '&nbsp;';
              }
              $retorno .= "<tr><td colspan='2' class='' width='20%'><span class='form'><b>$texto</b></span></td></tr>\n";
            }
            else
            {
              $classe = $md ? 'formmdtd' : 'formlttd';
              $retorno .= "<tr><td class='$classe' width='20%'>$campo</td><td class='$classe'>$texto</td></tr>\n";
            }
          }
          else
          {

            $retorno .= "<tr><td colspan='2'>$pardetalhe</td></tr>";

          }
        }
      }
    }

    $retorno .= "<tr><td class='tableDetalheLinhaSeparador' colspan='2'></td></tr>\n";

    if (!empty($this->url_editar) || !empty($this->url_cancelar) || $this->array_botao) {
      $retorno .= "
        <tr>
          <td colspan='2' align='center'>
            <script type='text/javascript'>
              function go(url) {
                document.location = url;
              }
            </script>";

      if ($this->url_novo) {
        $retorno .= "&nbsp;<input type='button' class='btn-green botaolistagem' onclick='javascript:go( \"$this->url_novo\" );' value=' {$this->caption_novo} '>&nbsp;\n";
      }

      if ($this->url_editar) {
        $retorno .= "&nbsp;<input type='button' class='botaolistagem' onclick='javascript:go( \"$this->url_editar\" );' value=' Editar '>&nbsp;\n";
      }

      if ($this->url_cancelar) {
        $retorno .= "&nbsp;<input type='button' class='botaolistagem' onclick='javascript:go( \"$this->url_cancelar\" );' value=' $this->nome_url_cancelar '>&nbsp;\n";
      }
      $retorno .= "</td></tr>";

      if ($this->array_botao_url || $this->array_botao_url_script) {
        $retorno .= "<tr><td colspan=2><table width='100%' summary=''><tr><td></td><td height='1' width='95%'  style='font-size: 0px;'>&nbsp;</td><td></td></tr></table></td></tr><tr><td colspan='2' align='center'>";
      }

      if ($this->array_botao_url) {
        for ($i = 0, $total = count($this->array_botao); $i < $total; $i++) {
          $retorno .= "&nbsp;<input type='button' class='btn_small' onclick='javascript:go( \"".$this->array_botao_url[$i]."\" );' value='".$this->array_botao[$i]."'>&nbsp;\n";
        }
      }
      elseif ($this->array_botao_url_script) {
        for ($i = 0, $total = count($this->array_botao); $i < $total; $i++) {
          $retorno .= "&nbsp;<input type='button' class='btn_small' onclick='{$this->array_botao_url_script[$i]}' value='".$this->array_botao[$i]."'>&nbsp;\n";
        }
      }

      if ($this->array_botao_url || $this->array_botao_url_script) {
        $retorno .= "</td></tr>";
      }

      $retorno .= "<tr><td colspan='2' height='1' bgcolor='#ccdce6' style='font-size: 0px;'>&nbsp;</td></tr>";
    }

    $retorno .= "
      </table><br><br>
      <!-- detalhe end -->";

    return $retorno;
  }
}
