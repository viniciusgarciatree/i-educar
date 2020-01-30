<?php

/**
 * Adiciona zeros a esquerda de um numero
 *
 * @param int $num
 * @param int $digitos
 * @return string
 */
function addLeadingZero($num, $digitos = 2)
{
  if (is_numeric($num)) {
    if ($digitos > 1) {
      for ($i = 1; $i < $digitos; $i++) {
        if ($num < pow(10, $i)) {
          $num = str_repeat('0', $digitos - $i) . $num;
          break;
        }
      }
    }
    return $num;
  }
  return str_repeat('0', $digitos);
}

function idFederal2int($str)
{
  $id_federal = str_replace(".", "", str_replace("-", "", str_replace("/", "", $str)));
  return preg_replace("/^0+/", "", $id_federal);
}

function int2CPF($int)
{
  $str = str_repeat('0', 11 - strlen($int)) . $int;
  return substr($str, 0, 3) . '.' . substr($str, 3, 3). '.' . substr($str, 6, 3) . '-' . substr($str, 9, 2);
}

function loadJson($file)
{
  $jsonFile = file_get_contents($file);
  return json_decode($jsonFile, true);
}

function int2CNPJ($int)
{
  if (strlen($int) < 14) {
    $str = str_repeat('0', 14 - strlen($int)) . $int;
  }
  else {
    $str = $int;
  }
  return substr($str, 0, 2) . '.' . substr($str, 2, 3). '.' . substr($str, 5, 3)
    . '/' . substr( $str, 8, 4 ) . "-" . substr($str, 12, 2);
}

/**
 * Formata um valor numérico em uma representação string de CEP.
 *
 * @param  string|int  $int
 * @return string
 */
function int2CEP($int)
{
  if ($int) {
    $int = (string) str_pad($int, 8, '0', STR_PAD_LEFT);
    return substr($int, 0, 5) . '-' . substr($int, 5, 3);
  }
  else {
    return '';
  }
}

function limpa_acentos( $str_nome )
{
  $procura1   = array('á', 'é', 'í', 'ó', 'ú', 'à', 'è', 'ì', 'ò', 'ù', 'ä', 'ë', 'ï', 'ö', 'ü', 'ç', 'ã', 'õ', 'ô', 'ê');
  $substitui1 = array('a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'c', 'a', 'o', 'o', 'e');

  $procura2   = array('Á', 'É', 'Í', 'Ó', 'Ú', 'À', 'È', 'Ì', 'Ò', 'Ù', 'Ä', 'Ë', 'Ï', 'Ö', 'Ü', 'Ç', 'Ã', 'Õ', 'Ê', 'Ô');
  $substitui2 = array('A', 'E', 'I', 'O', 'U', 'A', 'E', 'I', 'O', 'U', 'A', 'E', 'I', 'O', 'U', 'C', 'A', 'O', 'E', 'O');

  $str_nome = str_replace($procura1, $substitui1, $str_nome);
  $str_nome = str_replace($procura2, $substitui2, $str_nome);

  return $str_nome;
}

function transforma_minusculo($str_nome)
{
  $nome = mb_strtolower($str_nome);
  $arrayNome = explode(" ", $nome);
  $nome = '';

  foreach ($arrayNome as $parte) {
    if ($parte != 'de' && $parte != 'da' && $parte != 'dos' && $parte != 'do' &&
      $parte != 'das' && $parte != 'e') {
      $nome .= mb_strtoupper(mb_substr($parte, 0, 1)) . mb_substr($parte, 1) . ' ';
    }
    else {
      $nome .= $parte . ' ';
    }
  }

  $procura1   = array('Á', 'É', 'Í', 'Ó', 'Ú', 'À', 'È', 'Ì', 'Ò', 'Ù', 'Ä', 'Ë', 'Ï', 'Ö', 'Ü', 'Ç', 'Ã', 'Õ', 'Â', 'Ô');
  $substitui1 = array('á', 'é', 'í', 'ó', 'ú', 'à', 'è', 'ì', 'ò', 'ù', 'ä', 'ë', 'ï', 'ö', 'ü', 'ç', 'ã', 'õ', 'â', 'ô');

  $nome = str_replace($procura1, $substitui1, $nome);

  return $nome;
}

function minimiza_capitaliza($str)
{
  $nome = strtolower($str);
  $arrayNome = explode(' ', $nome);
  $nome ='';
  $gruda = '';

  foreach ($arrayNome as $parte) {
    if ($parte != 'de' && $parte != 'da' && $parte != 'dos' && $parte != 'do' &&
      $parte != 'das' && $parte != 'e') {
      $nome .= $gruda . strtoupper(substr($parte,0,1)).substr($parte,1);
    }
    else {
      $nome .= $gruda . $parte;
    }

    $gruda = ' ';
  }

  /**
   * @todo Mais um tratamento de acentos, ver funcoes.inc.php#transforma_minusculo().
   */
  $nome = str_replace(array('Ú','Ô','Ç','Á', 'É', 'Í', 'Ó', 'Ã', 'Ê', 'Ï', 'Ö', 'Ü', 'À', 'È', 'Ì', 'Ò', 'Ù', 'Õ'),
                      array('ú','ô','ç','á', 'é', 'í', 'ó', 'ã', 'ê', 'ï', 'ö', 'ü', 'à', 'è', 'ì', 'ò', 'ù', 'õ'), $nome );
  return $nome;
}

/**
 * Formata a data para o formato brasileiro
 *
 * @param string $data_original data que será transformada
 * @param bool $h_m determina se o a data retornada incluirá hora e minuto
 * @param bool $h_m_s determina se o a data retornada incluirá hora, minuto e segundo
 *
 * @return string
 */
function dataToBrasil($data_original, $h_m = FALSE, $h_m_s = FALSE)
{
  if ($data_original) {
    $arr_data = explode(' ', $data_original);

    $data = date('d/m/Y', strtotime($arr_data[0]));

    if ($h_m) {
      return "{$data} " . substr($arr_data[1], 0, 5);
    }
    elseif ($h_m_s) {
      return "{$data} " . substr($arr_data[1], 0, 8);
    }

    return $data;
  }

  return FALSE;
}

/**
 * Formata a data para o formato do banco
 *
 * @param string $data_original data que será transformada
 * @return string
 * @todo $data_original = NULL sempre será TRUE. Verificar que código chama
 *   esta função. Lógica falha.
 */
function dataToBanco($data_original, $inicial = NULL)
{
  if ($data_original) {
    $data = explode('/', $data_original);
    if(count($data)) {
      if (is_null($inicial)) {
        return "{$data[2]}-{$data[1]}-{$data[0]}";
      }

      if ($inicial == TRUE) {
        if ($data_original = NULL) {
          return FALSE;
        }
        else {
          return "{$data[2]}-{$data[1]}-{$data[0]} 00:00:00";
        }
      }
      elseif($inicial == FALSE) {
        if ($data_original = NULL) {
          return false;
        }
        else {
          return "{$data[2]}-{$data[1]}-{$data[0]} 23:59:59";
        }
      }
    }
    else {
      return FALSE;
    }
  }

  return FALSE;
}

/**
 * Formata uma data vinda do postgre
 *
 * @param string $data_original data que será transformada
 * @return string
 */
function dataFromPgToTime($data_original)
{
  if (strlen($data_original) > 16) {
    $data_original = substr($data_original, 0, 16);
  }

  return strtotime($data_original);
}

/**
 * Formata uma data ISO-8601 no formato do locale pt_BR.
 *
 * O formato ISO-8601 geralmente é utilizado pelos DBMS atuais nos tipos de campos datetime/timestamp.
 * O PostgreSQL utiliza este padrão.
 *
 * @param string $data_original Data que será formatada
 * @param string $formatacao    String de formatação no padrão aceito pela função date() do PHP
 * @link  http://www.php.net/date Documentação da função PHP date()
 *
 * @return string
 */
function dataFromPgToBr($data_original, $formatacao = 'd/m/Y')
{
  return date($formatacao, dataFromPgToTime($data_original));
}


/**
 * Funcao que troca caracteres acentuados por caracteres extendidos de HTML (para compatibilidade de encode).
 * Ex: á = &aacute;
 * pode substituir na ordem reversa
 *
 * @param string $text
 * @param bool $reverse
 * @return string
 * @todo Onde essa função é chamada? Transformação totalmente desnecessária.
 */
function extendChars($text, $reverse = FALSE)
{
  $chars = array("Ã", "Â", "Á", "À", "Ä", "É", "Ê", "È", "Ë", "Í", "Ì", "Ï", "Î", "Ô", "Õ", "Ó", "Ò", "Ö", "Ú", "Ù", "Û", "Ü", "Ý", "Ñ", "Ç",
                 "ã", "â", "á", "à", "ä", "é", "ê", "è", "ë", "í", "ì", "ï", "î", "ô", "õ", "ó", "ò", "ö", "ú", "ù", "û", "ü", "ý", "ñ", "ç" );
  $extends = array("&Atilde;", "&Acirc;", "&Aacute;", "&Agrave;", "&Auml;", "&Eacute;", "&Ecirc;", "&Egrave;", "&Euml;", "&Iacute;", "&Igrave;", "&Iuml;", "&Icirc;",   "&Ocirc;", "&Otilde;", "&Oacute;", "&Ograve;", "&Ouml;", "&Uacute;", "&Ugrave;", "&Ucirc;", "&Uuml;", "&Yacute;", "&Ntilde;", "&Ccedil;",
                   "&atilde;", "&acirc;", "&aacute;", "&agrave;", "&auml;", "&eacute;", "&ecirc;", "&egrave;", "&euml;", "&iacute;", "&igrave;", "&iuml;", "&icirc;",   "&ocirc;", "&otilde;", "&oacute;", "&ograve;", "&ouml;", "&uacute;", "&ugrave;", "&ucirc;", "&uuml;", "&yacute;", "&ntilde;", "&ccedil;" );

  if ($reverse) {
    return str_replace($extends, $chars, $text);
  }
  else {
    return str_replace($chars, $extends, $text);
  }
}

/**
 * @todo Casting para string ao invés de concatenação ($str = "" . $int . "";)
 */
function int2IdFederal($int) {
  $str = "" . $int . "";

  if (strlen($str) > 11) {
    if (strlen($int) < 14) {
      $str = str_repeat('0', 14 - strlen($int)) . $int;
    }

    $str = str_replace('.', '', $str);
    $str = str_replace('.', '', $str);
    $str = str_replace('-', '', $str);
    $str = str_replace('/', '', $str);

    $temp = substr( $str, 0, 2 );

    if (strlen($temp) == 2) {
      $temp .= '.';
    }

    $temp .= substr($str, 2 ,3);

    if (strlen($temp) == 6) {
      $temp .= '.';
    }

    $temp .= substr($str, 5, 3);

    if (strlen($temp) == 10) {
      $temp .= '/';
    }

    $temp .= substr( $str, 8, 4 );

    if (strlen($temp) == 15) {
      $temp .= '-';
    }

    $temp .= substr($str, 12, 2);
    return $temp;
  }
  else {
    if (strlen($int) < 11) {
      $str = str_repeat('0', 11 - strlen($int)) . $int;
    }

    $str = str_replace('.', '', $str);
    $str = str_replace('.', '', $str);
    $str = str_replace('/', '', $str);
    $str = str_replace('-', '', $str);

    $temp = substr($str, 0, 3);

    if (strlen($temp) == 3) {
      $temp .= '.';
    }

    $temp .= substr($str, 3, 3);

    if (strlen($temp) == 7) {
      $temp .= '.';
    }

    $temp .= substr($str, 6, 3);

    if (strlen($temp) == 11) {
      $temp .= '-';
    }

    $temp .= substr($str, 9, 2);

    return $temp;
  }
}

/**
 * Verifica se o valor é booleano
 * aceita como true:
 * 'true', 't', true, 1, '1', 'yes', 'y', 'sim', 's'
 *
 * @param mixed $val
 * @return bool
 */
function dbBool($val)
{
  return ($val === 'true' || $val === 't' || $val === TRUE || $val == 1 ||
    $val === 'yes' || $val === 'y' || $val === 'sim' || $val === 's');
}

function int2Nis($nis)
{
    return is_numeric($nis) ? str_pad($nis, 11, '0', STR_PAD_LEFT) : '';
}
