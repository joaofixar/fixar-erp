<?php
namespace hardness;
global $g;

$arquivo = $r_sped;
$dataFinal = $r_dataFinal;
$dataInicial = $r_dataInicial;
$cnpj = str_replace(".", "", $g['C004']['C004_Cnpj']);
$cnpj = str_replace("-", "", $cnpj);
$cnpj = str_replace("/", "", $cnpj);
// if($r_erros=="true"){
// 	$nomeArquivo = "RELATORIO_ERROS_FISCAL_".$cnpj."_".$dataInicial."_".$dataFinal.".txt";
// }else{
	$nomeArquivo = $arquivo."_".$cnpj."_".$dataInicial."_".$dataFinal.".txt";
// }	
$diretorio = $g['pathDadosUsuario']."arq_temp/".$nomeArquivo;
header('Content-type: application/x-msdownload');
header('Content-Disposition: attachment; filename='.$nomeArquivo);
header('Pragma: no-cache');
header('Expires: 0');
echo file_get_contents($diretorio);