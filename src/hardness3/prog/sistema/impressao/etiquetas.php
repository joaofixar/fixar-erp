<?php
namespace hardness;
/**
* Atualiza o arquivo de filtros com base na busca
*/
global $g;
$impressaoJava = $g['C031']['impressaoJava'];

if($impressaoJava == 1){
	include ('prog/sistema/impressao/etiquetasApplet.php');
}else if($impressaoJava == 2){
	include ('prog/sistema/impressao/etiquetasJava.php');
}

function retornoWhere($r_where, $r_acaoId){
	global $g;
	if(!empty($r_where)){
		$where = base64_decode($r_where);
	}else{
		$where = "LEFT JOIN C008 ON C008_Id=D001_C008_Id
				  LEFT JOIN D049 ON D049_D001_Id=D001_Id
				  LEFT JOIN D009 ON D009_D049_Id=D049_Id AND D009_C004_Id='{$g['empresaAtual']}'
				      WHERE D001_Id = '{$r_acaoId}'";
	}

	return $where;
}

function geraArquivoTXT($opcao, $impressora, $quantidade, $conteudo){
	global $g;

	// Gravando o arquivo em ASCII para imprimir
	//id da impressora + a opção que foi clicada + quantidade + usuario que solicitou + data e hora atual
	$dataAtual = date('dmY_Hms');
	$dir = $g['pathDados'] . '/tmp/impressao/';
	if (!is_dir($dir)){
		mkdir($dir, 0777);
	}

	$C007 = mysql_query("SELECT C007_Primeiro_Nome FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'");
	$mC007 = mysql_fetch_assoc($C007);
	$nomeDoArquivo = $impressora . "_" . $opcao . "_qte" . $quantidade . "_" . strtolower($mC007['C007_Primeiro_Nome']) . "_" . $dataAtual . ".etq";
	return file_put_contents($dir . $nomeDoArquivo, $conteudo);
}

function quantidadeArquviosServidor(){
	global $g;

	$dir = $g['pathDados'] . '/tmp/impressao/';
	$quantidadeArquivos = count(glob("{$dir}*.etq", GLOB_BRACE | GLOB_NOSORT ));

	return $quantidadeArquivos;

}
