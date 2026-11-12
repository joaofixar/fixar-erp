<?php
/**
 * 11/11/2011
 *
 * Tabela D013
 *
 * Documentação:
 *		http://hardness/wiki/index.php?title=Restri%C3%A7%C3%B5es_de_Campos
 */

$conf['restricaoIncluirUpdate']['D013_D024_Id'] = <<<'EOT'
$retorno = rVerificaFK('D024_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D013_Nome_Contato'] = <<<'EOT'
$retorno = rRegx('naoVazio', $valor);
EOT;

$conf['camposTipo']['D013_Data_Nascimento'] = array('data');

$conf['triggers']['D013']['deleteBefore'] = <<<'EOT'
    $sql = "delete from D013A where D013A_D013_Id = '{$chaveValor}'";    
    mysqli_query($sql);
EOT;

$conf['triggers']['D013']['insertAfter'] = <<<'EOT'

	$D013A = mysql_query("SELECT D013A_Id FROM D013A WHERE D013A_D013_Id = '{$chaveValor}'");
	if(mysql_num_rows($D013A) > 0){
		mysqli_query("UPDATE D013A SET D013A_Data_Inclusao = NOW() WHERE D013A_D013_Id = '{$chaveValor}'");
	} else {
	 	mysqli_query("INSERT INTO D013A (D013A_D013_Id,D013A_Data_Inclusao) VALUES ('{$chaveValor}', NOW())");
	}
		
EOT;

$conf['triggers']['D013']['updateAfter'] = <<<'EOT'

	$D013A = mysql_query("SELECT D013A_Id FROM D013A WHERE D013A_D013_Id = '{$chaveValor}'");
	if(mysql_num_rows($D013A) > 0){
		mysqli_query("UPDATE D013A SET D013A_Data_Ultima_Alteracao = NOW() WHERE D013A_D013_Id = '{$chaveValor}'");
	} else {
	 	mysqli_query("INSERT INTO D013A (D013A_D013_Id,D013A_Data_Ultima_Alteracao) VALUES ('{$chaveValor}', NOW())");
	}

EOT;

$conf['restricaoTravar']['D013'] = <<<'EOT'
	global $g;
	if ($operacao == 'excluir' || $operacao == 'alterar' || $operacao == 'incluir') {
		if($g['c029Ids'][232]){
			$retorno = false;
		}else{
			$retorno = true;
		}
	}
EOT;
?>