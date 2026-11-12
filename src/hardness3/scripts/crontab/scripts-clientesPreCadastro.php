<?php
namespace hardness;
/**
* Este script atualiza o flag ativo de todos os clientes em pré cadastro
*/

// Vars
$dias = (isset($g['C031']['diasMaxClientePreCadastro'])) ? $g['C031']['diasMaxClientePreCadastro'] : $g['conf']->ini('diasMaxClientePreCadastro');

if($dias > 0){
	$sqlD024 = mysql_query("SELECT * FROM D024 WHERE D024_Flag_Pre_Cadastro = 'S' AND D024_Flag_Ativo != 'N'");
	while($D024 = mysql_fetch_array($sqlD024)){
		if (strtotime("+{$dias} days", strtotime($D024['D024_Data_Cadastro'])) <= time()) {
			$sql = "UPDATE D024 SET D024_Flag_Ativo = 'N' WHERE D024_Id = '{$D024['D024_Id']}'";
			echo $D024['D024_Id'] . "\n";
			mysqli_query($sql);
		}
	}
}