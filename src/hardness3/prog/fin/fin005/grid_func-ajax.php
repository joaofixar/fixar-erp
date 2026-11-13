<?php
namespace hardness;
	/**
	* FIN - FIN005 - Boletos Bancários
	* Weiberlan
	* 17/11/2011
	*/

	// Classes
	require_once('bibliotecas/classes/FIN005.php');
	$FIN005 = new FIN005();
	require_once('bibliotecas/classes/FIN001.php');
	$FIN001 = new FIN001();

	set_time_limit(0); 
	ini_set('mysql.connect_timeout','0'); 
	ini_set('max_execution_time', '0');

	$resposta = array('code' => true, 'data' => array());
	
	/**
	* Ações
	*/
	switch($g['r'][3]) {
		// -> campoExclusao($array) -> fin002CampoExclusao(T015_Id)
		case 'cancelar':
			$T117_Id = isset($_REQUEST['T117_Id']) ? $_REQUEST['T117_Id'] : false;
			$retorno = $FIN005->cancelar($T117_Id);
			
			if ($retorno !== true) {
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			} else {
				// excluir
				$resposta['data'] = 'T117_Id-' . $T117_Id;
			}
		break;

		case 'gerarPDFboleto':
			global $g;
			unlink($g['pathRaiz']. $dir."boleto.pdf");
			$T117_Id = isset($_REQUEST['T117_Id']) ? $_REQUEST['T117_Id'] : false;
			$extra = isset($_REQUEST['extra']) ? $_REQUEST['extra'] : false;
			$gerarVarios = isset($_REQUEST['gerarVarios']) ? $_REQUEST['gerarVarios'] : false;
			$nT117_Id = str_replace("T117_Id-","",$T117_Id);
			if(!empty($T117_Id)){
				$link = '/hardness3/hardness/boleto/gerar_boleto.php?T117_Id=' . $nT117_Id;					
				$filename = 'boleto_' . $nT117_Id . '.pdf';
				// $filename = 'boleto.pdf';
			}
			if(!empty($r_extra) && !empty($gerarVarios)){
				$link = '/hardness3/hardness/boleto/gerar_boleto.php?extra=' . $extra . '&gerarVarios='.$gerarVarios;					
				$filename = 'boletosImpressao_' . $g['usuarioAtual'] . '.pdf';
				// $filename = 'boletosImpressao.pdf';
			}
			$link = str_replace(' ', '+', $link);
			$dir  = $g['pathDados'] . 'pdf/';
			is_dir($dir) or mkdir($dir, 0777, true);
			$file     = $dir . $filename;
			$fileFull = $g['pathRaiz'] . $file;
			log("filaFull:".$link);

			$chmod = "xvfb-run --server-num " . mt_rand(0, 98) . " -s \"-screen 0 640x480x16\" wkhtmltopdf --lowquality --margin-bottom 0 \"{$g['urlRaiz']}{$link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"";
			$retorno = shell_exec($chmod);
			log("comando:".$chmod);
			log("retorno:".$retorno);
			$resposta['code'] = true;
			$resposta['data'] = 'hardness3/'.$file;
		break;

		case 'fin005DeletarPDFBoleto':
			global $g;
			$T117_Id = isset($_REQUEST['T117_Id']) ? $_REQUEST['T117_Id'] : false;
			$extra = isset($_REQUEST['extra']) ? $_REQUEST['extra'] : false;
			$gerarVarios = isset($_REQUEST['gerarVarios']) ? $_REQUEST['gerarVarios'] : false;
			$nT117_Id = str_replace("T117_Id-","",$T117_Id);

			if(!empty($T117_Id)){
				$filename = 'boleto_' . $nT117_Id . '.pdf';
			}
			if(!empty($r_extra) && !empty($gerarVarios)){
				$filename = 'boletosImpressao_' . $g['usuarioAtual'] . '.pdf';
			}

			$dir  = $g['pathDados'] . 'pdf/';
			$fileFull = $g['pathRaiz'] . '/' . $dir . $filename;
			if(file_exists($fileFull)){
				unlink($fileFull);
			}
		break;
					
		case 'flagGerarBoleto':
			$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
			$T002_Id = isset($_REQUEST['T002_Id']) ? $_REQUEST['T002_Id'] : false;
			$FIN005->updateT002_Flag_Gerar_Boleto($T002_Id, $selected);
		break;

		case 'enviarMultiEmailBoleto':
			$resposta['code'] = false;
			$resposta['data'] = 'A base local não envia e-mail.';
			break;
			require_once("bibliotecas/classes/FIN005.php");
			$FIN005 = new FIN005();
			$erros = '';
			$enviarDanfe = (!empty($r_enviarDanfe)) ? true : false;
			$extra = $r_extra;
			$extraOriginal = base64_decode($extra); 
			$extra = gInsertExtraWhere($extraOriginal, "T117_Flag_Atualizar = 'S' AND T117_Flag_Cancelado != 'S' AND T002_Data_Recebimento = '0000-00-00'");
			log("SELECT * FROM T117 {$extra}");

			$where = str_replace("WHERE", "", stristr($extra,"WHERE"));
			$dadosQuery = array('where' => $where);
	        $retorno = $FIN001->validacoesTravarT002($dadosQuery);

	        if($retorno!==false){
	            $resposta['data'] = $retorno;
				$resposta['code'] = false;
	        }else{
	        	$sqlT117 = mysql_query("SELECT * FROM T117 {$extra}");
				while($T117 = mysql_fetch_array($sqlT117)){
					$retorno = $FIN005->enviarMultiEmailBoleto($T117['T117_Id'], $enviarDanfe);
					if($retorno !== true){
						$erros .= "<b>".$T117['T117_Nosso_Numero']."</b> - Erro: ".$retorno."<br>";
					}
				}
				if($erros != '') { 
					$erros = substr($erros, 0, -2);
					$resposta['data'] = "Erros no envio dos boletos abaixo:<br><br>".$erros;
					$resposta['code'] = false;
				} else {
					$resposta['data'] = "Os boletos foram enviados com sucesso!";
					$resposta['code'] = true;
				} 
	        }
			
		break;

		case 'enviarMultiEmailBoletoACP':
			$resposta['code'] = false;
			$resposta['data'] = 'A base local não envia e-mail.';
			break;
			require_once("bibliotecas/classes/FIN005.php");
			$FIN005 = new FIN005();
			$erros = '';
			$enviarACP = (!empty($r_enviarACP)) ? true : false;
			$extra = $r_extra;
			$extraOriginal = base64_decode($extra); 
			$extra = gInsertExtraWhere($extraOriginal, "T117_Flag_Atualizar = 'S' AND T117_Flag_Cancelado != 'S' AND T002_Data_Recebimento = '0000-00-00'");
			log("SELECT * FROM T117 {$extra}");
			$where = str_replace("WHERE", "", stristr($extra,"WHERE"));
			$dadosQuery = array('where' => $where);
	        $retorno = $FIN001->validacoesTravarT002($dadosQuery);

	        if($retorno!==false){
	            $resposta['data'] = $retorno;
				$resposta['code'] = false;
	        }else{
	        	$sqlT117 = mysql_query("SELECT * FROM T117 {$extra}");
				while($T117 = mysql_fetch_array($sqlT117)){
					$retorno = $FIN005->enviarMultiEmailBoletoACP($T117['T117_Id'], $enviarACP);
					if($retorno !== true){
						$erros .= "<b>".$T117['T117_Nosso_Numero']."</b> - Erro: ".$retorno."<br>";
					}
				}
				if($erros != '') { 
					$erros = substr($erros, 0, -2);
					$resposta['data'] = "Erros no envio dos boletos abaixo:<br><br>".$erros;
					$resposta['code'] = false;
				} else {
					$resposta['data'] = "Os boletos foram enviados com sucesso!";
					$resposta['code'] = true;
				}
	        }
		break;

		case 'enviarMultiEmailBoletoLembrete':
			$resposta['code'] = false;
			$resposta['data'] = 'A base local não envia e-mail.';
			break;
			require_once("bibliotecas/classes/FIN005.php");
			$FIN005 = new FIN005();
			$erros = '';
			$enviarDanfe = (!empty($r_enviarDanfe)) ? true : false;
			$extra = $r_extra;
			$extraOriginal = base64_decode($extra); 
			$extra = gInsertExtraWhere($extraOriginal, "T117_Flag_Atualizar = 'S' AND T117_Flag_Cancelado != 'S' AND T002_Data_Recebimento = '0000-00-00'");
			log("SELECT * FROM T117 {$extra}");
			$where = str_replace("WHERE", "", stristr($extra,"WHERE"));
			$dadosQuery = array('where' => $where);
	        $retorno = $FIN001->validacoesTravarT002($dadosQuery);

	        if($retorno!==false){
	            $resposta['data'] = $retorno;
				$resposta['code'] = false;
	        }else{
	        	$sqlT117 = mysql_query("SELECT * FROM T117 {$extra}");
				while($T117 = mysql_fetch_array($sqlT117)){
					$retorno = $FIN005->enviarMultiEmailBoletoLembrete($T117['T117_Id'], $enviarDanfe);
					if($retorno !== true){
						$erros .= "<b>".$T117['T117_Nosso_Numero']."</b> - Erro: ".$retorno."<br>";
					}
				}
				if($erros != '') { 
					$erros = substr($erros, 0, -2);
					$resposta['data'] = "Erros no envio dos boletos abaixo:<br><br>".$erros;
					$resposta['code'] = false;
				} else {
					$resposta['data'] = "Os boletos foram enviados com sucesso!";
					$resposta['code'] = true;
				} 
	        }
		break;

		case 'enviarMultiEmailBoletoCobranca':
			$resposta['code'] = false;
			$resposta['data'] = 'A base local não envia e-mail.';
			break;
			require_once("bibliotecas/classes/FIN005.php");
			$FIN005 = new FIN005();
			$erros = '';
			$enviarDanfe = (!empty($r_enviarDanfe)) ? true : false;
			$extra = $r_extra;
			$extraOriginal = base64_decode($extra); 
			$extra = gInsertExtraWhere($extraOriginal, "T117_Flag_Atualizar = 'S' AND T117_Flag_Cancelado != 'S' AND T002_Data_Recebimento = '0000-00-00'");
			log("SELECT * FROM T117 {$extra}");
			$where = str_replace("WHERE", "", stristr($extra,"WHERE"));
			$dadosQuery = array('where' => $where);
	        $retorno = $FIN001->validacoesTravarT002($dadosQuery);

	        if($retorno!==false){
	            $resposta['data'] = $retorno;
				$resposta['code'] = false;
	        }else{
	        	$sqlT117 = mysql_query("SELECT * FROM T117 {$extra}");
				while($T117 = mysql_fetch_array($sqlT117)){
					$retorno = $FIN005->enviarMultiEmailBoletoCobranca($T117['T117_Id'], $enviarDanfe);
					if($retorno !== true){
						$erros .= "<b>".$T117['T117_Nosso_Numero']."</b> - Erro: ".$retorno."<br>";
					}
				}
				if($erros != '') {
					$erros = substr($erros, 0, -2);
					$resposta['data'] = "Erros no envio dos boletos abaixo:<br><br>".$erros;
					$resposta['code'] = false;
				} else {
					$resposta['data'] = "Os boletos foram enviados com sucesso!";
					$resposta['code'] = true;
				}
	        }
		break;

		case 'marcarBoletoEnvio':
			$T117_Id = isset($r_T117_Id) ? $r_T117_Id : false;
			$check = isset($r_selected) ? $r_selected : false;
			mysql_query("UPDATE T117 SET T117_Flag_Atualizar = '{$check}' WHERE T117_Id = '{$T117_Id}'");
			$resposta['code'] = true;
		break;

		case 'fin005MarcarTodosBoletos':
			$extra = isset($r_extra) ? base64_decode($r_extra) : false;
			$sqlT117 = mysql_query("SELECT * FROM T117 ".$extra);
			$numRows = 0;
			while($T117 = mysql_fetch_array($sqlT117)){
				if($T117['T002_Data_Recebimento'] == '0000-00-00'){
					$numRows++;
					mysql_query("UPDATE T117 LEFT JOIN T002 ON T002_T117_Id = T117_Id SET T117_Flag_Enviar_Email = 'S' WHERE T117_Id = '{$T117['T117_Id']}' AND T002_Data_Recebimento = '0000-00-00'");
				}
			}
			if($numRows > 0){
				$resposta['code'] = true;
				if($numRows > 1){
					$resposta['data'] = $numRows.' boletos marcados';
				} else {
					$resposta['data'] = $numRows.' boleto marcado';
				}
			} else {
				$resposta['code'] = false;
				$resposta['data'] = 'Nenhum boleto foi marcado';
			}					
		break;

		case 'fin005DesmarcarTodosBoletos':
			mysql_query("UPDATE T117 SET T117_Flag_Enviar_Email = 'N'");
			$resposta['code'] = true;
		break;

		case 'fin005MarcarTodosBoletosAtualizar':
			$extra = isset($r_extra) ? base64_decode($r_extra) : false;
			$sqlT117 = mysql_query("SELECT * FROM T117 ".$extra);
			$numRows = 0;
			while($T117 = mysql_fetch_array($sqlT117)){
				if($T117['T002_Data_Recebimento'] == '0000-00-00'){
					$numRows++;
					mysql_query("UPDATE T117 LEFT JOIN T002 ON T002_T117_Id = T117_Id SET T117_Flag_Atualizar = 'S' WHERE T117_Id = '{$T117['T117_Id']}' AND T002_Data_Recebimento = '0000-00-00'");
				}
			}
			if($numRows > 0){
				$resposta['code'] = true;
				if($numRows > 1){
					$resposta['data'] = $numRows.' boletos marcados';
				} else {
					$resposta['data'] = $numRows.' boleto marcado';
				}
			} else {
				$resposta['code'] = false;
				$resposta['data'] = 'Nenhum boleto foi marcado';
			}					
		break;

		case 'fin005DesmarcarTodosBoletosAtualizar':
			mysql_query("UPDATE T117 SET T117_Flag_Atualizar = 'N'");
			$resposta['code'] = true;
		break;

		case 'desfazerCancelar':
			$T117_Id = isset($_REQUEST['T117_Id']) ? $_REQUEST['T117_Id'] : false;
			$T002_Id = isset($_REQUEST['T002_Id']) ? $_REQUEST['T002_Id'] : false;
			$retorno = $FIN005->desfazerCancelar($T117_Id, $T002_Id);

			if ($retorno !== true) {
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			} else {
				// excluir
				$resposta['data'] = 'T117_Id-' . $T117_Id;
			}
		break;

		case 'marcarBoletoAtualizar':
			$T117_Id = isset($r_T117_Id) ? $r_T117_Id : false;
			$check = isset($r_selected) ? $r_selected : false;
			mysql_query("UPDATE T117 SET T117_Flag_Atualizar = '{$check}' WHERE T117_Id = '{$T117_Id}'");
			$resposta['code'] = true;
		break;

		case 'validarValorMaximoBoletoSemRegistro':

			$extraOriginal = base64_decode($r_extra); 
			//$extra = gInsertExtraWhere($extraOriginal, "T117_Flag_Atualizar = 'S' AND T117_Flag_Cancelado != 'S' AND T002_Data_Recebimento = '0000-00-00'");
			$extra 	        = gInsertExtraWhere($extraOriginal, "T117_Flag_Atualizar = 'S' AND T117_C004_Id = '{$g['empresaAtual']}' AND T117_Flag_Cancelado != 'S' AND T002_Data_Recebimento = '0000-00-00'");
			$where 			= str_replace("WHERE", "", stristr($extra,"WHERE"));
			$dadosQuery 	= array('where' => $where);
	        $retorno 		= $FIN001->validacoesTravarT002($dadosQuery);
			
	        if($retorno!==false){
	            $resposta['data'] = $retorno;
				$resposta['code'] = false;
	        }else{
	        	$res = TRUE; //inicio a variável como verdadeira(não existe nenhum boleto com o valor acima do valor máximo)
				//procura todos os boletos que estão selecionados
				$T117 = mysql_query("SELECT T117_Id, 
											T117_Valor_Total, 
											D007_Valor_Maximo_Boleto_Sem_Registro, 
											T002_T035_Id, 
											T117_Flag_Atualizar,
											D024_E_Mail,
											D024_E_Mail_Boleto,
											D024_Nome_Fantasia,
											D024_Id 
									   FROM T117 
								  LEFT JOIN D007 ON D007_Id = T117_D007_Id 
						          LEFT JOIN T002 ON T002_Id = T117_T002_Id
								  LEFT JOIN D024 ON D024_Id = T002_D024_Id
									  WHERE $where");
									  
				//variável que guarda o números dos boletos que serão bloqueados por conterem um valor acima do valor máximo
				$boletos		 = "";
				$clienteSemEmail = "";
				$D024_Id 		 = array();
				while($mT117 = mysql_fetch_array($T117)){
					if ($mT117['D007_Valor_Maximo_Boleto_Sem_Registro'] > 0 && ($mT117['T117_Valor_Total'] > $mT117['D007_Valor_Maximo_Boleto_Sem_Registro'] || $mT117['T117_Valor_Total'] == 0) && $mT117['T002_T035_Id']<=0) {
						$boletos .= " {$mT117['T117_Id']} ";
						$res = FALSE;
					}	
					
					if($r_enviarEmail == 'S' && empty($mT117['D024_E_Mail']) && empty($mT117['D024_E_Mail_Boleto'])){
						
						if (in_array($mT117['D024_Id'], $D024_Id)) {
							continue;
						}
						
						$D024_Id[] = $mT117['D024_Id'];
						$clienteSemEmail .= $mT117['D024_Nome_Fantasia'].'<br>';
					}
				}	

				if($res == FALSE){
					$resposta['code'] = false;
					$resposta['data'] = 'O valor dos boleto(s)<b>'. $boletos. '</b>excede o valor limite para boletos sem registro. Registre-o(s) para continuar a operação';
				} elseif($clienteSemEmail !=""){
					$resposta['code'] = false;
					$resposta['data'] = "Os clientes abaixo estão sem email cadastrado, verifique por favor<br><br>".$clienteSemEmail;
				}else{
					$resposta['code'] = true;
				}
	        }
		break;

		case 'validarValorMaximoBoletoSemRegistroComId':
			$T117Id = isset($r_T117_Id) ? $r_T117_Id : false;
			$T117_Id = substr($T117Id, 8);
			$res = 0;

			$dadosQuery = array('where' => "T117_Id='{$T117_Id}'");
	        $retorno = $FIN001->validacoesTravarT002($dadosQuery);

	        if($retorno!==false){
	            $resposta['data'] = $retorno;
				$resposta['code'] = false;
	        }else{
	        	//Valida se o valor do boleto excedo o valor máximo dos boletos sem registros PARA EMAILS QUE AINDA NÃO FORAM ENVIADOS
				$T117 = mysql_query("SELECT T117_Valor_Total, D007_Valor_Maximo_Boleto_Sem_Registro, T002_T035_Id 
									   FROM T117
							      LEFT JOIN D007 ON D007_Id = T117_D007_Id
							      LEFT JOIN T002 ON T002_Id = T117_T002_Id
							      	  WHERE T117_Id = '{$T117_Id}'");

				$mT117 = mysql_fetch_array($T117);
				if ($mT117['D007_Valor_Maximo_Boleto_Sem_Registro'] > 0 && ($mT117['T117_Valor_Total'] > $mT117['D007_Valor_Maximo_Boleto_Sem_Registro'] || $mT117['T117_Valor_Total'] == 0) && $mT117['T002_T035_Id']<=0) {			
					$res = 1;	
				}
				log("resposta: ". $res);
				if($res == 1){
					$resposta['code'] = false;
					$resposta['data'] = 'O valor do boleto excede o valor limite para boletos sem registro. Registre-o para continuar a operação';
				}else{
					$resposta['code'] = true;
				}
	        }
		break;
	}
	
	echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>