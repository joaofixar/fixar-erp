<?php
/**
* camposTipo
* Define qual é o tipo do campo. Com essa informação é feita a formatação dele no FORM, GRID, etc.
*/
$conf['camposTipo']['T089_Data_Vencimento'] = array('data');


$conf['triggers']['T085']['insertAfter'] = <<<'EOT'
	
	$aT085=mysql_query("SELECT * FROM T085 WHERE T085_Id='{$chaveValor}'");
	$mT085=mysql_fetch_array($aT085);

	$T084=mysql_query("SELECT * FROM T084 WHERE T084_Id='{$mT085['T085_T084_Id']}'");
	$mT084=mysql_fetch_array($T084);
	$VConhecimento = $mT084['T084_Valor_Conhecimento'] + $mT084['T084_Valor_ICMS_ST'];

	$T085 = mysql_query("SELECT SUM(T085_Valor_Total_Nota) as Total_Soma FROM T085 WHERE T085_T084_Id='{$mT085['T085_T084_Id']}'");
	$mT085a = mysql_fetch_array($T085);
	$VTotal = $mT085a['Total_Soma'];

	$percentual = $VConhecimento / $VTotal;
	$T085 = mysql_query("SELECT * FROM T085 WHERE T085_T084_Id='{$mT085['T085_T084_Id']}'");
	$T085_Id = 0;
	while($mT085=mysql_fetch_array($T085)){
		$rateio = $mT085['T085_Valor_Total_Nota'] * $percentual;
		$T085_Id = $mT085['T085_Id'];
		mysql_query("UPDATE T085 SET T085_Valor_Rateio='{$rateio}' WHERE T085_Id='{$mT085['T085_Id']}'");
	}
	
	$sql = mysql_query("SELECT SUM(T085_Valor_Rateio) as Soma_Rateio FROM T085 WHERE T085_T084_Id='{$mT085['T085_T084_Id']}'");
	$row = mysql_fetch_array($sql);
	
	if($row['Soma_Rateio'] > 0){

		if($row['Soma_Rateio'] < $VConhecimento){
			//die("Soma Rateio: ".$row['Soma_Rateio']." VConhecimento: ".$VConhecimento);
			$diferenca = $VConhecimento - $row['Soma_Rateio'];
			mysql_query("UPDATE T085 SET T085_Valor_Rateio = T085_Valor_Rateio + '{$diferenca}' WHERE T085_Id='{$T085_Id}'");
		}
	}


EOT;

$conf['triggers']['T085']['deleteBefore'] = <<<'EOT'
	global $T085_T084_Id_Apos_Excluir;
	$sql = mysql_query("SELECT T085_T084_Id from T085 WHERE T085_Id='{$chaveValor}'");
	$mSql = mysql_fetch_array($sql);

	$T085_T084_Id_Apos_Excluir = $mSql['T085_T084_Id'];
EOT;


$conf['triggers']['T085']['deleteAfter'] = <<<'EOT'

	global $T085_T084_Id_Apos_Excluir;

	$T084=mysql_query("SELECT * FROM T084 WHERE T084_Id='{$T085_T084_Id_Apos_Excluir}'");
	$mT084=mysql_fetch_array($T084);
	$VConhecimento = $mT084['T084_Valor_Conhecimento'] + $mT084['T084_Valor_ICMS_ST'];

	$T085 = mysql_query("SELECT SUM(T085_Valor_Total_Nota) as Total_Soma FROM T085 WHERE T085_T084_Id='{$T085_T084_Id_Apos_Excluir}'");
	$mT085a = mysql_fetch_array($T085);
	$VTotal = $mT085a['Total_Soma'];

	$percentual = $VConhecimento / $VTotal;
	$T085 = mysql_query("SELECT * FROM T085 WHERE T085_T084_Id='{$T085_T084_Id_Apos_Excluir}'");
	$T085_Id = 0;
	while($mT085=mysql_fetch_array($T085)){
		$rateio = $mT085['T085_Valor_Total_Nota'] * $percentual;
		$T085_Id = $mT085['T085_Id'];
		mysql_query("UPDATE T085 SET T085_Valor_Rateio='{$rateio}' WHERE T085_Id='{$mT085['T085_Id']}'");
	}

	$sql = mysql_query("SELECT SUM(T085_Valor_Rateio) as Soma_Rateio FROM T085 WHERE T085_T084_Id='{$chaveValor}'");
	$row = mysql_fetch_array($sql);
	if($row['Soma_Rateio'] < $VConhecimento){
		$diferenca = $VConhecimento - $row['Soma_Rateio'];
		mysql_query("UPDATE T085 SET T085_Valor_Rateio = T085_Valor_Rateio + '{$diferenca}' WHERE T085_Id='{$T085_Id}'");
	}

	unset($T085_T084_Id_Apos_Excluir);


EOT;

// restricaoTravar
$conf['restricaoTravar']['T085'] = <<<'EOT'
	$retorno = true;	
	if($operacao == 'alterar' || $operacao == 'excluir') {
		$T084 = mysql_query("SELECT T084_Flag_Travar, T084_Flag_Entrada_Saida FROM T084 WHERE T084_Id = '{$valorPkParent}'");
		$mT084 = mysql_fetch_array($T084);

		if($mT084['T084_Flag_Entrada_Saida'] == 'S') {
			if($mT084['T084_Flag_Travar'] == 'S') {
				$retorno = 'CT-e já finalizado, para altera-lo utilize o botão "Habilitar edição"';
			}
		}
	}
EOT;