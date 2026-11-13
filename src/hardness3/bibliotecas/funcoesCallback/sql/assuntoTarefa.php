<?php
namespace hardness;

	$mensagem = '';
	$sql = mysql_query("SELECT D085_Mensagem, D085_T139_Id, D085_Flag_Manual, D085_Entidade, D085_Id_Entidade, D085_Data_Previsao_Retorno, D085_Hora_Previsao_Retorno, D085_Flag_Lido, D085_Data, D085_Hora 
						  FROM D085 WHERE D085_T259_Id='{$row['id_atividade']}' 
						   AND IFNULL(D085_T259_Id,0) > 0 
						 ORDER BY D085_Data ASC, D085_Hora ASC");
	while($D085 = mysql_fetch_array($sql)){
		if($D085['D085_Flag_Manual'] == 'S' && $D085['D085_T139_Id'] <= 0){
			$D085['D085_Mensagem'] = inteliSubstr($D085['D085_Mensagem'], 35, '');
			$mensagem .= " | {$D085['D085_Mensagem']}";
		} else if($D085['D085_Entidade'] == 'RETORNO'){
			$mensagem .= " | {$D085['D085_Mensagem']}";
		} else if($D085['D085_Entidade'] == 'EST_PEDIDO'){
			$D085['D085_Data'] = substr(gCorrigeData($D085['D085_Data']),0,5);
			$D085['D085_Hora'] = substr($D085['D085_Hora'],0,5);
			$mensagem .= " | {$D085['D085_Data']} {$D085['D085_Hora']}";
		} else {
			//Orçamento, pedido e nota
			if($D085['D085_Entidade'] == 'T003'){
				$mensagem .= " | Orç x".substr($D085['D085_Mensagem'], 10); 
			} else if($D085['D085_Entidade'] == 'T005'){
				$mensagem .= " | Ped ". substr($D085['D085_Mensagem'], 7); 
			} else if($D085['D085_Entidade'] == 'T007'){
				$mensagem .= " | NF ". substr($D085['D085_Mensagem'], 12);
			} else if($D085['D085_Entidade'] == 'T255' || $D085['D085_Entidade'] == 'T224'){
				$mensagem .= " | {$D085['D085_Mensagem']}";
			} else if($D085['D085_Entidade'] == 'T218'){
				$mensagem .= " | {$D085['D085_Mensagem']}";
			}else if($D085['D085_Entidade'] == 'T182'){
				$mensagem .= " | {$D085['D085_Mensagem']}";
			}
		}
	}
	$mensagem = substr($mensagem,3);
	$assunto = mysql_real_escape_string($mensagem);
	$row['assuntos_atividade'] =  $mensagem;
	
