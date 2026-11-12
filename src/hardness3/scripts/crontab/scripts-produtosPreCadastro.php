<?php
namespace hardness;
/**
* Este script atualiza o flag ativo de todos os produtos em pré cadastro
*/

// Vars
$dias = (isset($g['C031']['diasMaxProdutoPreCadastro'])) ? $g['C031']['diasMaxProdutoPreCadastro'] : $g['conf']->ini('diasMaxProdutoPreCadastro');

if($dias > 0){
	$sqlD001 = mysql_query("SELECT * FROM D001 WHERE D001_Flag_Pre_Cadastro = 'S' AND D001_Flag_Ativo != 'N'");
	while($D001 = mysql_fetch_array($sqlD001)){
		if (strtotime("+{$dias} days", strtotime($D001['D001_Data_Cadastro'])) <= time()) {
			$sql = "UPDATE D001 SET D001_Flag_Ativo = 'N' WHERE D001_Id = '{$D001['D001_Id']}'";
			echo $D001['D001_Codigo_Produto'] . "\n";
			mysqli_query($sql);
		}
	}
}