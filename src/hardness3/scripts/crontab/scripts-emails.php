<?php
namespace hardness;

$dirAnexos = "{$g['pathDados']}anexosEmail/";
$dirImagens = "{$g['pathDados']}arquivoEmail/";
//Removendo imagens temporarias
$dirImagensTmp = "{$g['pathDados']}arquivoEmail/tmp/";
$return = shell_exec("rm -f {$dirImagensTmp}*");

//Removendo emails excluidos a mais de 2 dias
$data = date("Y-m-d", strtotime("-2 days"));
$sql = <<<EOT
SELECT T139_Id FROM T139 WHERE T139_Flag_Excluir = 1 AND T139_Data_Hora < '{$data}';
EOT;
$res = mysql_query($sql);
while ($row = mysql_fetch_assoc($res)) {
	$arquivo = $dirAnexos . $row['T139_Id'] . '-0';
	if (file_exists($arquivo)) {
		shell_exec("rm -f {$dirAnexos}{$row['T139_Id']}-*");
		echo 'Anexos:' . $arquivo . "\n";
	}
	if (is_dir($dirImagens.$row['T139_Id'])){
		$files = glob($dirImagens.$row['T139_Id'].'/*', GLOB_MARK);
        foreach($files as $file) {
        	unlink($file);
        }
		rmdir("{$dirImagens}{$row['T139_Id']}");
		echo 'Imagens: ' . $dirImagens.$row['T139_Id'] . "\n";
	}
	mysql_query("DELETE FROM T139 WHERE T139_Id = '{$row['T139_Id']}'");
	//Remove o registro na agenda (D085) e verifica se a tarefa (T259) que está inserido possui outros registros (D085), caso não tenha, removemos a tarefa
	$sqlD085 = mysql_query("SELECT D085_T259_Id FROM D085 WHERE D085_T139_Id = '{$row['T139_Id']}'");
	$rowD085 = mysql_fetch_assoc($sqlD085);
	mysql_query("DELETE FROM D085 WHERE D085_T139_Id = '{$row['T139_Id']}'");
	$sqlT259 = mysql_query("SELECT D085_Id FROM D085 WHERE D085_T259_Id = '{$rowD085['D085_T259_Id']}'");
	if(mysql_num_rows($sqlT259) <= 0){
		mysql_query("DELETE FROM T259 WHERE T259_Id = '{$rowD085['D085_T259_Id']}'");
	}
}

//Removendo emails da caixa de spam com mais de 30 dias
$data = date("Y-m-d", strtotime("-1 month"));
$sql = <<<EOT
SELECT T139_Id FROM T139 WHERE T139_Flag_Spam = 'S' AND T139_Data_Hora < '{$data}';
EOT;
$res = mysql_query($sql);
while ($row = mysql_fetch_assoc($res)) {
	$arquivo = $dirAnexos . $row['T139_Id'] . '-0';
	if (file_exists($arquivo)) {
		shell_exec("rm -f {$dirAnexos}{$row['T139_Id']}-*");
		echo $arquivo . "\n";
	}
	mysql_query("DELETE FROM T139 WHERE T139_Id = '{$row['T139_Id']}'");
	//Remove o registro na agenda (D085) e verifica se a tarefa (T259) que está inserido possui outros registros (D085), caso não tenha, removemos a tarefa
	$sqlD085 = mysql_query("SELECT D085_T259_Id FROM D085 WHERE D085_T139_Id = '{$row['T139_Id']}'");
	$rowD085 = mysql_fetch_assoc($sqlD085);
	mysql_query("DELETE FROM D085 WHERE D085_T139_Id = '{$row['T139_Id']}'");
	$sqlT259 = mysql_query("SELECT D085_Id FROM D085 WHERE D085_T259_Id = '{$rowD085['D085_T259_Id']}'");
	if(mysql_num_rows($sqlT259) <= 0){
		mysql_query("DELETE FROM T259 WHERE T259_Id = '{$rowD085['D085_T259_Id']}'");
	}
}

//Removendo emails da caixa de entrada a mais de 1 mês
//$data = date("Y-m-d", strtotime("-1 month"));
//$sql = <<<EOT
//SELECT T139_Id FROM T139 WHERE T139_Pasta = 'E' AND T139_Data_Hora < '{$data}';
//EOT;
//$res = mysql_query($sql);
//while ($row = mysql_fetch_assoc($res)) {
//	$arquivo = $dir . $row['T139_Id'] . '-0';
//	if (file_exists($arquivo)) {
//		shell_exec("rm -f {$dir}{$row['T139_Id']}-*");
//		echo $arquivo . "\n";
//	}
//	mysql_query("DELETE FROM T139 WHERE T139_Id = '{$row['T139_Id']}'");
//}
