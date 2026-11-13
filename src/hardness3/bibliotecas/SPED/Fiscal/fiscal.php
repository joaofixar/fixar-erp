<?php
namespace hardness;

global $g;
DEFINE("_SALTO_LINHA",CHR(13).CHR(10));
$layout = '';
$mes                  = date("m",strtotime($dataInicial));
$ano                  = date("Y",strtotime($dataFinal));
$tipoEFD              = $tipoEscrituracao;
$NUM_REC_ANTERIOR     = ($tipoEFD == '1') ? $numeroRecibo : '';
$IND_SIT_ESP          = isset($situacaoEspecial) ? $situacaoEspecial : '';
$casasDecimaisSPED    = $g['C031']['CasasDecimaisSPED'];
$casasDecimaisBlocoK  = $g['C031']['CasasDecimaisBlocoK'];
$array0200            = array();
$array0190            = array();
$array0150            = array();

switch ($g['C031']['dataNFRecebidaSPED']) {
    case '2': $dataNFRecebida = 'T013_Data_Emissao'; break;
    case '3': $dataNFRecebida = 'T013_Data_Entrada_Estoque'; break;
    default:  $dataNFRecebida = 'T013_Data_Entrada';
}

switch ($g['C031']['dataNFRecebidaLancarSPED']) {
    case '2': $dataNFRecebidaLancarSPED = 'T013_Data_Emissao'; break;
    case '3': $dataNFRecebidaLancarSPED = 'T013_Data_Entrada_Estoque'; break;
    default:  $dataNFRecebidaLancarSPED = 'T013_Data_Entrada';
}

// tradando a data para as notas de entrada	
$dataInicialHora = $dataInicial.' 00:00:00';
$dataFinalHora   = $dataFinal. ' 23:59:59';
//-----------------------------------------

$D001_C004_Id         = ($apenasProdutoEmpresa == 'S') ? "AND (D001_C004_Id='{$g['empresaAtual']}' OR D001_C004_Id='' OR D001_C004_Id IS NULL)" : '' ;
$dataInventarioBlocoH = (!empty($dataInventarioBlocoH)) ? $dataInventarioBlocoH : $dataInventario ;
$gerarBlocoEDetalhado = isset($gerarBlocoEDetalhado) && $gerarBlocoEDetalhado == 'S' ? 'S' : 'N';

$arquivo = $r_sped;
$cnpj = str_replace(".", "", $g['C004']['C004_Cnpj']);
$cnpj = str_replace("-", "", $cnpj);
$cnpj = str_replace("/", "", $cnpj);

//MONTANDO O LAYOUT DO SPED OU BLOCO K DEPENDENDO DO QUE FOI SELECIONADO NO FORMULÁRIO
if($tipoDocumento=="S"){
    
    include_once('bloco_B.php');

    include_once('bloco_C.php');

    include_once('bloco_D.php');

    include_once('bloco_E.php');

    include_once('bloco_G.php');

    include_once('bloco_H.php');

    include_once('bloco_K.php');

    include_once('bloco_1.php');

    include_once('bloco_0.php');

    $layout = $layout0200 . $layout;

    preg_match_all('/((^|\n)\|[^\|]+)/mi', $layout, $array);
    $totais = array();
    foreach ($array[0] as $value) {
        $value = trim($value);
        $value = str_replace('|', '', $value);
        if (isset($totais[$value])) {
            $totais[$value]++;
        } else {
            $totais[$value] = 1;
        }
    }
    $layout .= '|9001|0|' . _SALTO_LINHA;
    foreach ($totais as $key => $value) {
        $layout .= '|9900|'. $key . '|' . $value . '|' . _SALTO_LINHA;
    }
    //Calcula o total de registros 9900 e os 4 que finalizam
    $totais = count($totais)+4;
    $layout .= '|9900|9001|1|' . _SALTO_LINHA;
    $layout .= '|9900|9990|1|' . _SALTO_LINHA;
    $layout .= '|9900|9999|1|' . _SALTO_LINHA;
    $layout .= '|9900|9900|' . $totais . '|' . _SALTO_LINHA;
    $layout .= '|9990|';
    //Pega o total de registros 9900 + os registros do bloco 9 que restaram
    $layout .= $totais+3 . '|' . _SALTO_LINHA;
    $linhas = substr_count($layout, CHR(13).CHR(10));
    $layout .= '|9999|';
    $layout .= $linhas+1 . '|' . _SALTO_LINHA; 
   
}else if($tipoDocumento=="K"){
    include_once('bloco_K.php');
}

//VERIFICANDO SE EXISTEM ERROS PARA FAZER O DOWNLOAD DO ARQUIVO DE RELATORIO DE ERROS OU DO ARQUIVO COM O SPED
// if(!empty($erros)){
//     $nomeArquivo = "RELATORIO_ERROS_FISCAL_".$cnpj."_".$dataInicial."_".$dataFinal.".txt";

//     $diretorio = $g['pathDadosUsuario']."arq_temp/".$nomeArquivo;

//     if(file_exists($g['pathDadosUsuario']."arq_temp/")){
//         chmod($g['pathDadosUsuario']."arq_temp/", 0777);
//         file_put_contents($diretorio,$erros);
//     }else{
//         mkdir($g['pathDadosUsuario']."arq_temp/", 0777, true);
//         file_put_contents($diretorio,$erros);
//     }
//     echo gGeraAlertMsg('Erros', 'Existem erros de validação, verifique o relatório gerado.');
//     echo  <<<EOT
//         <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
//             $("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
//         } </script></div>
// EOT;
// }else{
    $nomeArquivo = "FISCAL_".$cnpj."_".$dataInicial."_".$dataFinal.".txt";
    $diretorio = $g['pathDadosUsuario']."arq_temp/".$nomeArquivo;
    if(file_exists($g['pathDadosUsuario']."arq_temp/")){
        chmod($g['pathDadosUsuario']."arq_temp/", 0777);
        file_put_contents($diretorio,$layout);
    }else{
        mkdir($g['pathDadosUsuario']."arq_temp/", 0777, true);
        file_put_contents($diretorio,$layout);
    }

    echo gGeraAlertMsg('Sucesso', 'Arquivo gerado com sucesso!');
    echo  <<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            $("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
        } </script></div>
EOT;
// }

function insertRegistro0460($D188_Codigo_Observacao, $D188_Descricao_Observacao){
    global $array0460;

    if(!in_array($D188_Codigo_Observacao, array_column($array0460, 'D188_Codigo_Observacao'))){                
        $array0460[] = [
            'D188_Codigo_Observacao' => $D188_Codigo_Observacao, 
            'D188_Descricao_Observacao' =>  $D188_Descricao_Observacao, 
        ];
    }
}

function insertRegistro0450($codigoInformacao, $descricao){
    global $array0450;

    if(!in_array($codigoInformacao, array_column($array0450, 'codigoInformacao'))){                
        $array0450[] = [
            'codigoInformacao' => $codigoInformacao, 
            'descricao' =>  $descricao, 
        ];
    }
}


function verificaRegistro0200($D001_Id){
    global $array0200;
    if(in_array($D001_Id, $array0200)){
        return true;
    }else{
        $array0200[] = $D001_Id;
        return false;
    }
}

function verificaRegistro0150($D024_Id){
    global $array0150;
    if(in_array($D024_Id, $array0150)){
        return true;
    }else{
        $array0150[] = $D024_Id;
        return false;
    }
}

function verificaRegistro0190($D037_Id){
    global $array0190;
    if(in_array($D037_Id, $array0190)){
        return true;
    }else{
        $array0190[] = $D037_Id;
        return false;
    }
}

//FUNÇÕES GERAIS PARA TRATAMENTO DE DADOS
function formataData($data){
	$data = explode('-', $data);	
	$dia = @$data[2];
	$mes = @$data[1];
	$ano = $data[0];
	return $dia . $mes . $ano;
}

function formataDecimal($numero){
    global $g;
	return number_format($numero,$g['C031']['CasasDecimaisSPED'], ',', '');
}

function formataDecimalBlocoK($numero){
    global $g;
    return number_format($numero,$g['C031']['CasasDecimaisBlocoK'], ',', '');
}

function removeChars($string){
	$string = trim($string);
	return preg_replace('/[^a-zA-Z0-9]/','', $string);
}

function tirarEspacos($string){
    $string = preg_replace("/\s+/", " ",$string);
    $string = trim($string);
    return $string;
}

function adicionarEspape($x)
{
    $x=trim($x);
    $x = preg_replace("/\s+/", " ",$x);
    
    $replace = array(
        '&lt;' => '', '&gt;' => '', '&#039;' => '', '&amp;' => '',
        '&quot;' => '', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'Ae',
        '&Auml;' => 'A', 'Å' => 'A', 'Ā' => 'A', 'Ą' => 'A', 'Ă' => 'A', 'Æ' => 'Ae',
        'Ç' => 'C', 'Ć' => 'C', 'Č' => 'C', 'Ĉ' => 'C', 'Ċ' => 'C', 'Ď' => 'D', 'Đ' => 'D',
        'Ð' => 'D', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ē' => 'E',
        'Ę' => 'E', 'Ě' => 'E', 'Ĕ' => 'E', 'Ė' => 'E', 'Ĝ' => 'G', 'Ğ' => 'G',
        'Ġ' => 'G', 'Ģ' => 'G', 'Ĥ' => 'H', 'Ħ' => 'H', 'Ì' => 'I', 'Í' => 'I',
        'Î' => 'I', 'Ï' => 'I', 'Ī' => 'I', 'Ĩ' => 'I', 'Ĭ' => 'I', 'Į' => 'I',
        'İ' => 'I', 'Ĳ' => 'IJ', 'Ĵ' => 'J', 'Ķ' => 'K', 'Ł' => 'K', 'Ľ' => 'K',
        'Ĺ' => 'K', 'Ļ' => 'K', 'Ŀ' => 'K', 'Ñ' => 'N', 'Ń' => 'N', 'Ň' => 'N',
        'Ņ' => 'N', 'Ŋ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
        'Ö' => 'O', '&Ouml;' => 'Oe', 'Ø' => 'O', 'Ō' => 'O', 'Ő' => 'O', 'Ŏ' => 'O',
        'Œ' => 'OE', 'Ŕ' => 'R', 'Ř' => 'R', 'Ŗ' => 'R', 'Ś' => 'S', 'Š' => 'S',
        'Ş' => 'S', 'Ŝ' => 'S', 'Ș' => 'S', 'Ť' => 'T', 'Ţ' => 'T', 'Ŧ' => 'T',
        'Ț' => 'T', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'Ue', 'Ū' => 'U',
        '&Uuml;' => 'Ue', 'Ů' => 'U', 'Ű' => 'U', 'Ŭ' => 'U', 'Ũ' => 'U', 'Ų' => 'U',
        'Ŵ' => 'W', 'Ý' => 'Y', 'Ŷ' => 'Y', 'Ÿ' => 'Y', 'Ź' => 'Z', 'Ž' => 'Z',
        'Ż' => 'Z', 'Þ' => 'T', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a',
        'ä' => 'ae', '&auml;' => 'ae', 'å' => 'a', 'ā' => 'a', 'ą' => 'a', 'ă' => 'a',
        'æ' => 'ae', 'ç' => 'c', 'ć' => 'c', 'č' => 'c', 'ĉ' => 'c', 'ċ' => 'c',
        'ď' => 'd', 'đ' => 'd', 'ð' => 'd', 'è' => 'e', 'é' => 'e', 'ê' => 'e',
        'ë' => 'e', 'ē' => 'e', 'ę' => 'e', 'ě' => 'e', 'ĕ' => 'e', 'ė' => 'e',
        'ƒ' => 'f', 'ĝ' => 'g', 'ğ' => 'g', 'ġ' => 'g', 'ģ' => 'g', 'ĥ' => 'h',
        'ħ' => 'h', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ī' => 'i',
        'ĩ' => 'i', 'ĭ' => 'i', 'į' => 'i', 'ı' => 'i', 'ĳ' => 'ij', 'ĵ' => 'j',
        'ķ' => 'k', 'ĸ' => 'k', 'ł' => 'l', 'ľ' => 'l', 'ĺ' => 'l', 'ļ' => 'l',
        'ŀ' => 'l', 'ñ' => 'n', 'ń' => 'n', 'ň' => 'n', 'ņ' => 'n', 'ŉ' => 'n',
        'ŋ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'oe',
        '&ouml;' => 'oe', 'ø' => 'o', 'ō' => 'o', 'ő' => 'o', 'ŏ' => 'o', 'œ' => 'oe',
        'ŕ' => 'r', 'ř' => 'r', 'ŗ' => 'r', 'š' => 's', 'ù' => 'u', 'ú' => 'u',
        'û' => 'u', 'ü' => 'ue', 'ū' => 'u', '&uuml;' => 'ue', 'ů' => 'u', 'ű' => 'u',
        'ŭ' => 'u', 'ũ' => 'u', 'ų' => 'u', 'ŵ' => 'w', 'ý' => 'y', 'ÿ' => 'y',
        'ŷ' => 'y', 'ž' => 'z', 'ż' => 'z', 'ź' => 'z', 'þ' => 't', 'ß' => 'ss',
        'ſ' => 'ss', 'ый' => 'iy', 'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G',
        'Д' => 'D', 'Е' => 'E', 'Ё' => 'YO', 'Ж' => 'ZH', 'З' => 'Z', 'И' => 'I',
        'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
        'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F',
        'Х' => 'H', 'Ц' => 'C', 'Ч' => 'CH', 'Ш' => 'SH', 'Щ' => 'SCH', 'Ъ' => '',
        'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'YU', 'Я' => 'YA', 'а' => 'a',
        'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo',
        'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l',
        'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's',
        'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
        'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e',
        'ю' => 'yu', 'я' => 'ya', '▒' => 'A', '|' => ''
    );

    $x = str_replace(array_keys($replace), $replace, $x);

    /*if ((mb_detect_encoding($x)=='UTF-8') && (!mb_detect_encoding($x,'UTF-8',true))) {     
       // $x = utf8_decode($x);
    }*/

   // $x = utf8_decode($x);

    ////-- ELIMINA CARACTERES ESPECIAIS
    define( "ACENTOS" , "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖŒØŠÙÚÛÜÝŽÞàáâãäåæçèéêëìíîïðñòóôõö&œøšùúûüýžþºª&.$" );
    define( "NORMAL"  , "AAAAAAACEEEEIIIIDNOOOOO   UUUUY  aaaaaaaceeeeiiii noooooE   uuuuy  BCED$" );
    
    $x = strtr($x, ACENTOS, NORMAL);

    //$x = urldecode($x);
    $novoX = '';
    for ($i = 0; $i < strlen($x); $i++) {
        $code = ord($x[$i]);
        if ($code == 192) { $novoX .= 'A'; continue; }
        if ($code == 193) { $novoX .= 'A'; continue; }
        if ($code == 194) { $novoX .= 'A'; continue; }
        if ($code == 195) { $novoX .= 'A'; continue; }
        if ($code == 196) { $novoX .= 'A'; continue; }
        if ($code == 197) { $novoX .= 'A'; continue; }
        if ($code == 198) { $novoX .= 'A'; continue; }
        if ($code == 199) { $novoX .= 'C'; continue; }
        if ($code == 200) { $novoX .= 'E'; continue; }
        if ($code == 201) { $novoX .= 'E'; continue; }
        if ($code == 202) { $novoX .= 'E'; continue; }
        if ($code == 203) { $novoX .= 'E'; continue; }
        if ($code == 204) { $novoX .= 'I'; continue; }
        if ($code == 205) { $novoX .= 'I'; continue; }
        if ($code == 206) { $novoX .= 'I'; continue; }
        if ($code == 207) { $novoX .= 'I'; continue; }
        if ($code == 210) { $novoX .= 'O'; continue; }
        if ($code == 211) { $novoX .= 'O'; continue; }
        if ($code == 212) { $novoX .= 'O'; continue; }
        if ($code == 213) { $novoX .= 'O'; continue; }
        if ($code == 214) { $novoX .= 'O'; continue; }
        if ($code == 217) { $novoX .= 'U'; continue; }
        if ($code == 218) { $novoX .= 'U'; continue; }
        if ($code == 219) { $novoX .= 'U'; continue; }
        if ($code == 220) { $novoX .= 'U'; continue; }
        if ($code == 224) { $novoX .= 'A'; continue; }
        if ($code == 225) { $novoX .= 'A'; continue; }
        if ($code == 226) { $novoX .= 'A'; continue; }
        if ($code == 227) { $novoX .= 'A'; continue; }
        if ($code == 228) { $novoX .= 'A'; continue; }
        if ($code == 229) { $novoX .= 'A'; continue; }
        if ($code == 231) { $novoX .= 'C'; continue; }
        if ($code == 232) { $novoX .= 'E'; continue; }
        if ($code == 233) { $novoX .= 'E'; continue; }
        if ($code == 234) { $novoX .= 'E'; continue; }
        if ($code == 235) { $novoX .= 'E'; continue; }
        if ($code == 236) { $novoX .= 'I'; continue; }
        if ($code == 237) { $novoX .= 'I'; continue; }
        if ($code == 238) { $novoX .= 'I'; continue; }
        if ($code == 239) { $novoX .= 'I'; continue; }
        if ($code == 242) { $novoX .= 'O'; continue; }
        if ($code == 243) { $novoX .= 'O'; continue; }
        if ($code == 244) { $novoX .= 'O'; continue; }
        if ($code == 245) { $novoX .= 'O'; continue; }
        if ($code == 246) { $novoX .= 'O'; continue; }
        if ($code == 249) { $novoX .= 'U'; continue; }
        if ($code == 250) { $novoX .= 'U'; continue; }
        if ($code == 251) { $novoX .= 'U'; continue; }
        if ($code == 252) { $novoX .= 'U'; continue; }
        $novoX .= $x[$i];
    }
    
    $x = $novoX;

    
    $cCharPermitido='&ABCDEFGHIJLKYMNOPQRSTUVXWZ 1234567890()[]{}.,+_-:/=%?;!@|$"<>';
    for ($n = 0;$n<strlen($x);$n++)
    {
         $c=substr($x,$n,1);
         if (strpos($cCharPermitido,strtoupper($c))!==FALSE)
         {
            $r.=$c;
         }
         else
         {
            $r.="";
         }
    }
    $x=$r;
    
    $x = trim($x);

    return "$x";
}

if(!empty($arrayT316)) {
	// Verifica se tem ativo imoblizado para dar a baixa, essa variavel arrayT316 está sendo populado no bloco G
    // Registros lançados referente ao bloco G e Bloco 0 (0300 0305 0500 0600)
	baixaAtivoImobilizadoCiap($arrayT316);
}

function baixaAtivoImobilizadoCiap($T316){

	foreach(array_unique($T316) as $T316_Id){
		mysqli_query("UPDATE T316 SET T316_Flag_Ativo_Baixado = 'S', T316_Data_Fim_Apropriacao_Credito = NOW()  WHERE T316_Id = '{$T316_Id}'");
	}
}

