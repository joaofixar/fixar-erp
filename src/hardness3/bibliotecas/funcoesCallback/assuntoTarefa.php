<?php
namespace hardness;

function assuntoTarefa($array, $param=false){
	global $g;
	//$array['campoAtual'] = '';
	if(empty($array['campoAtual'])){
		$sql = "SELECT C007_Email_POP_Servidor, C007_Email_SMTP_Usuario, C007_Email_SMTP_Senha, C007_Email_POP_Porta FROM C007 WHERE C007_Id = '{$array['todosCampos']['T259_C007_Id']}';";
		$resultado = mysql_query($sql);
		$C007 = mysql_fetch_assoc($resultado);
		require_once('sistema/comunicacaoEmail.php');

		$retorno = '';
		$sql = mysql_query("SELECT D085_Mensagem, 
								   D085_T139_Id, 
								   D085_Flag_Manual, 
								   D085_Entidade,
								   D085_Id_Entidade,
								   D085_Data_Previsao_Retorno,
								   D085_Hora_Previsao_Retorno,
								   D085_Flag_Lido,
								   D085_Data, 
								   D085_Hora 
							  FROM D085 
						     WHERE D085_T259_Id='{$array['todosCampos']['T259_Id']}' 
						       AND IFNULL(D085_T259_Id,0) > 0
						  ORDER BY D085_Data ASC, D085_Hora ASC");

		while($row = mysql_fetch_array($sql)){
		
			$iconeCarta = "";
			$iconeWhats = "";

			if($row['D085_Flag_Manual'] == 'S' && $row['D085_T139_Id'] <= 0){
				//Interações manuais
				$row['D085_Mensagem'] = inteliSubstr($row['D085_Mensagem'], 35, '');

				$sqlCheck = mysql_query("SELECT * FROM D085 WHERE D085_T259_Id = '{$array['todosCampos']['T259_Id']}' AND D085_Entidade = 'RETORNO' AND D085_Data = '{$row['D085_Data_Previsao_Retorno']}' AND D085_Hora = '{$row['D085_Hora_Previsao_Retorno']}'");
				if($row['D085_Data_Previsao_Retorno'] != '0000-00-00' && mysql_num_rows($sqlCheck) <= 0){
					$retornoAgendado = "<span class=\"ui-icon-tarefas\" style=\"float: left; background-position: -80px -114px; margin-left: -2px;\"></span>";
				}
				$retorno .= "<div style=\"float: left; border-radius: 4px; background-color: #fff; color: #000 !important; padding: 1px 3px 0px 1px; margin-right: 4px; font-weight: normal\"><span class=\"ui-icon-tarefas\" style=\"background-position: -128px -98px; float: left; margin-top: -1px;\" />{$retornoAgendado}{$row['D085_Mensagem']}</div>";
			} else if($row['D085_T139_Id'] > 0){
				//Email
				// $imagemAnexo = "";
				// $mail = new comunicacaoEmail($C007['C007_Email_POP_Servidor'], $C007['C007_Email_SMTP_Usuario'], $C007['C007_Email_SMTP_Senha'], false, $row['D085_T139_Id'], $C007['C007_Email_POP_Porta']);
				// $anexos = $mail->listaAnexos($row['D085_T139_Id']);
				// $campos = $mail->pegarCamposEmail($row['D085_T139_Id']);
				// $iconeCarta = ($campos['T139_Flag_Lido'] == '0') ? 'background-position: -80px -97px;' : 'background-position: -96px -97px;';
				// if (sizeof($anexos) > 0) {
				// 	$imagemAnexo = "<span class=\"ui-icon ui-icon-paper-clip\" style=\"width: 13px; float: left; margin-left: -3px; margin-right: 2px;\" />";
				// }
				// $row['D085_Mensagem'] = inteliSubstr($row['D085_Mensagem'], 35, '');
				// $retorno .= "<div style=\"float: left; border-radius: 4px; color: #000 !important; padding: 2px 3px 0px 1px; margin-right: 4px;\"><span class=\"ui-icon\" style=\"float: left; {$iconeCarta}\" />{$imagemAnexo}{$row['D085_Mensagem']}</div>";
			} else if($row['D085_Entidade'] == 'RETORNO'){
				$retorno .= "<div style=\"float: left; font-size: 11px; background-color: #F9D4A5; border-radius: 4px; color: #000 !important; padding: 2px 3px 2px 3px; margin-right: 4px;\">{$row['D085_Mensagem']}</div>";
			} else if($row['D085_Entidade'] == 'EST_PEDIDO'){//F2FFF2
				$row['D085_Data'] = substr(gCorrigeData($row['D085_Data']),0,5);
				$row['D085_Hora'] = substr($row['D085_Hora'],0,5);
				$retorno .= "<div style=\"float: left; font-size: 11px; background-color: #DBFDDB; border-radius: 4px; color: #000 !important; padding: 2px 3px 2px 3px; margin-right: 4px;\"><span class=\"ui-icon-tarefas ui-icon-cart\" style=\"margin-top: -3px; width: 13px; float: left; margin-left: -3px; margin-right: 2px;\" />{$row['D085_Data']} {$row['D085_Hora']}</div>";
			} else {

				$sqlT139 = mysql_query("SELECT T139_Id 
									      	  FROM T139 
										     WHERE T139_{$row['D085_Entidade']}_Id = '{$row['D085_Id_Entidade']}' 
										       AND T139_Pasta = 'S' 
										       AND T139_Para NOT LIKE '%Whatsapp:%'
									      ORDER BY T139_Id DESC LIMIT 1");													
				
				$rowT139 = mysql_fetch_assoc($sqlT139);

				if($rowT139['T139_Id'] > 0){
					$iconeCarta = "<span class=\"ui-icon-tarefas\" style=\"float: right; background-position: -80px -99px; height: 12px;\" />";
				}

				$whats = mysql_query("SELECT T139_Id, 
										     T139_Flag_Lido 
									    FROM T139 
									   WHERE T139_{$row['D085_Entidade']}_Id = '{$row['D085_Id_Entidade']}' 
									     AND T139_Pasta = 'S'
									     AND T139_Para LIKE '%Whatsapp:%'
								    ORDER BY T139_Id DESC LIMIT 1");								  				

				$mWhats = mysql_fetch_assoc($whats);				

				if ($mWhats['T139_Id']> 0){
					$iconeWhats = "<span class=\"fab fa-whatsapp\" style=\"float: right; margin-left: 4px ; background-position: -80px -99px; height: 12px;\"></span>";
				}
  				
				//Orçamento, pedido e nota
				if($row['D085_Entidade'] == 'T003'){
					$retorno .= "<div style=\"float: left; font-size: 11px; background-color: #A5DDF9; border-radius: 4px; color: #000 !important; padding: 2px 3px 2px 3px; margin-right: 4px;\">Orç ".substr($row['D085_Mensagem'], 10)." {$iconeWhats}{$iconeCarta}</div>"; //<span class=\"ui-icon ui-icon-document\" style=\"background-position: -32px -98px; float: left;\" />
				} else if($row['D085_Entidade'] == 'T005'){
					$retorno .= "<div style=\"float: left; font-size: 11px; background-color: #FDFAA8; border-radius: 4px; color: #000 !important; padding: 2px 3px 2px 3px; margin-right: 4px;\">Ped ". substr($row['D085_Mensagem'], 7)." {$iconeWhats}{$iconeCarta}</div>"; //<span class=\"ui-icon ui-icon-document\" style=\"background-position: -32px -98px; float: left;\" />
				} else if($row['D085_Entidade'] == 'T007'){
					$doc     = mysql_query("SELECT T007_Flag_ACP FROM T007 WHERE T007_Id = '{$row['D085_Id_Entidade']}'");
					$mDoc    = mysql_fetch_array($doc);
					$tipo    = ($mDoc['T007_Flag_ACP'] == 8) ? 'NFC' : 'NF';
					$retorno .= "<div style=\"float: left; font-size: 11px; background-color: #A5EAA5; border-radius: 4px; color: #000 !important; padding: 2px 3px 2px 3px; margin-right: 4px;\">{$tipo} ". substr($row['D085_Mensagem'], 12)." {$iconeWhats}{$iconeCarta}</div>"; //<span class=\"ui-icon ui-icon-document\" style=\"background-position: -32px -98px; float: left;\" />
				} else if($row['D085_Entidade'] == 'T255' || $row['D085_Entidade'] == 'T224'){
					$retorno .= "<div style=\"float: left; font-size: 11px; background-color: #DE92FD; border-radius: 4px; color: #000 !important; padding: 2px 3px 2px 3px; margin-right: 4px;\">{$row['D085_Mensagem']}</div>"; //<span class=\"ui-icon ui-icon-document\" style=\"background-position: -32px -98px; float: left;\" />
				} else if($row['D085_Entidade'] == 'T218'){
					$retorno .= "<div style=\"float: left; font-size: 11px; background-color: #FD9292; border-radius: 4px; color: #000 !important; padding: 2px 3px 2px 3px; margin-right: 4px;\">{$row['D085_Mensagem']}</div>"; //<span class=\"ui-icon ui-icon-document\" style=\"background-position: -32px -98px; float: left;\" />
				}else if($row['D085_Entidade'] == 'T182'){
					$retorno .= "<div style=\"float: left; font-size: 11px; background-color: #FF6347; border-radius: 4px; color: #000 !important; padding: 2px 3px 2px 3px; margin-right: 4px;\">{$row['D085_Mensagem']}</div>";
				}
			}
		}
		$assunto = mysql_real_escape_string($retorno);
		mysql_query("UPDATE T259 SET T259_Assunto = '{$assunto}' WHERE T259_Id = '{$array['todosCampos']['T259_Id']}'");
		return $retorno;
	} else {
		return $array['campoAtual'];
	}
}