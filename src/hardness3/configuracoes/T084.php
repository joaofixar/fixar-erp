<?php
/**
* camposTipo
* Define qual é o tipo do campo. Com essa informação é feita a formatação dele no FORM, GRID, etc.
*/

// restricaoTravar
$conf['restricaoTravar']['T084'] = <<<'EOT'
	$retorno = true;
	$T084=mysql_query("SELECT T084_Flag_Entrada_Saida,T084_Flag_Cancelado,T084_Numero_Protocolo_Cte,T084_Flag_Travar FROM T084 WHERE T084_Id='{$valorPk}'");
	$mT084=mysql_fetch_array($T084);	

	if($mT084['T084_Flag_Entrada_Saida'] == 'S') {	
		if($operacao == 'alterar') {						
			if(!empty($mT084['T084_Numero_Protocolo_Cte']))  {
				$retorno = 'Não é possível alterar CT-e autorizado.';
			} else {	
				if($mT084['T084_Flag_Travar'] == 'S') {
					$retorno = 'CT-e finalizado, para altera-lo utilize o botão "Habilitar edição"';										
				}else if($mT084['T084_Flag_Cancelado'] == 'S') {								
					$retorno = 'CT-e esta Cancelado!';
				}
			}
		}	
	}		
EOT;


$conf['triggers']['T084']['updateAfter'] = <<<'EOT'

	$T084=mysql_query("SELECT * FROM T084 WHERE T084_Id='{$chaveValor}'");
	$mT084=mysql_fetch_array($T084);
	$VConhecimento = $mT084['T084_Valor_Conhecimento'] + $mT084['T084_Valor_ICMS_ST'];

	$T085 = mysql_query("SELECT SUM(T085_Valor_Total_Nota) as Total_Soma FROM T085 WHERE T085_T084_Id='{$chaveValor}'");
	$mT085a = mysql_fetch_array($T085);
	$VTotal = $mT085a['Total_Soma'];

	$percentual = $VConhecimento / $VTotal;
	$T085_Id = 0;
	$T085 = mysql_query("SELECT * FROM T085 WHERE T085_T084_Id='{$chaveValor}'");
	while($mT085=mysql_fetch_array($T085)){
		$rateio = $mT085['T085_Valor_Total_Nota'] * $percentual;
		mysql_query("UPDATE T085 SET T085_Valor_Rateio='{$rateio}' WHERE T085_Id='{$mT085['T085_Id']}'");
		$T085_Id = $mT085['T085_Id'];
	}

	$sql = mysql_query("SELECT SUM(T085_Valor_Rateio) as Soma_Rateio FROM T085 WHERE T085_T084_Id='{$chaveValor}'");
	$row = mysql_fetch_array($sql);
	if($row['Soma_Rateio'] < $VConhecimento){
		$diferenca = $VConhecimento - $row['Soma_Rateio'];
		mysql_query("UPDATE T085 SET T085_Valor_Rateio = T085_Valor_Rateio + '{$diferenca}' WHERE T085_Id='{$T085_Id}'");
	}

EOT;


