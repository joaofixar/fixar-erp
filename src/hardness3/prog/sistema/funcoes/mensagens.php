<?php
namespace hardness;

switch ($g['r'][3]) {
	case 'atualizarNotificacoes':
		$resposta = array('code' => true, 'data' => '');
		// apc_delete('cacheN001-'.$confUsuario['dbDatabase']);

		require_once('bibliotecas/classes/NOTIFICACOES.php');
		$NOTIFICACOES = new NOTIFICACOES();
		$NOTIFICACOES->atualizarNotificacoesGeral();
		$NOTIFICACOES->atualizarNotificacoesGeralHora();
		$NOTIFICACOES->atualizarNotificacoesGeralDia();
		
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'exibirOcultarNotificacao':
		$resposta = array('code' => true, 'data' => '');

		if(empty($g['C007']['C007_Configuracao_Notificacoes'])){
			$config = array("{$r_notificacao}" => "{$r_ocultar}");
		} else {
			$config = unserialize($g['C007']['C007_Configuracao_Notificacoes']);
			$config["{$r_notificacao}"] = "{$r_ocultar}";
		}
		$config = serialize($config);
		mysql_query("UPDATE C007 SET C007_Configuracao_Notificacoes = '{$config}' WHERE C007_Id = '{$g['usuarioAtual']}'");

		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'verificar':
		require_once('bibliotecas/classes/NOTIFICACOES.php');
		$NOTIFICACOES = new NOTIFICACOES();

		session_start();
		$callback = (isset($r_callback)) ? $r_callback : false;
		$nivel = (isset($r_nivel)) ? $r_nivel : false;
		$resposta = array('code' => true, 'data' => '');

		/*
		*	NOTIFICAÇÕES NIVEL 1 - RECUPERADAS DO CACHE PHP
		*/
		if(empty($nivel) || $nivel == '1'){
// 			$resposta['data'] .= <<<EOT
// 				$('#messageAlert .alerts').html(parseInt(0));
// EOT;

			$cacheN001 = apc_fetch('cacheN001-'.$confUsuario['dbDatabase']);

			//Verifica se o cache existe, se estiver vazio atualizamos o cache
			// if(empty($cacheN001)){
			// 	require_once('bibliotecas/classes/NOTIFICACOES.php');
			// 	$NOTIFICACOES = new NOTIFICACOES();
			// 	$NOTIFICACOES->atualizarNotificacoesGeral();
			// 	$NOTIFICACOES->atualizarNotificacoesGeralHora();
			// }
 
			$cacheN001 = unserialize($cacheN001);
			if($r_showCache == 'S'){
				print_r($cacheN001);
				die();
			}

			//Verifica se o cache foi modificado
			$C031 = array();
			$config = unserialize($g['C007']['C007_Configuracao_Notificacoes']);
			$resposta['data'] .= "var updatedNotifications = [];";
			foreach($cacheN001 as $key => $rowN001){
				$rowN001['N001_Id_Mensagem'] = 'cache1'.$rowN001['N001_Id_Mensagem'];
				$possuiAcesso = true;
				if($key === 'lastUpdate') { continue; }
				if(!empty($rowN001['N001_C007_Id']) && $rowN001['N001_C007_Id'] > 0 && $g['usuarioAtual'] != $rowN001['N001_C007_Id']) { continue; } //Verifica se é o usuário gravado
				if(!empty($rowN001['N001_Flag_Alcada']) && strpos($rowN001['N001_Flag_Alcada'], $g['C007']['C007_Flag_Vendedor'])  === false ) { continue; } //Verifica se o usario possui a alçada
				//Verifica se o usuário possuí acesso a tela que a notificação abre
				if(!empty($rowN001['N001_C029'])){
					$possuiAcesso = false;
					$rowN001['N001_C029'] = unserialize($rowN001['N001_C029']);
					foreach ($rowN001['N001_C029'] as $C039) {
						if(isset($g['c029Ids'][$C039])) { $possuiAcesso = true; }
					}
					if(!$possuiAcesso){ continue; }
				}
				//Verifica as configurações para ver se a empresa está com a notificação habilitada
				if(!empty($rowN001['N001_C031'])){
					$rowN001['N001_C031'] = unserialize($rowN001['N001_C031']);
					foreach ($rowN001['N001_C031'] as $varConfiguracao => $valor) {
						if($g['C031_global'][$rowN001['N001_C004_Id']][$varConfiguracao] != $valor){
							continue 2;
						}
					}
				}
				if(!isset($g['C019'][$rowN001['N001_C004_Id']])) { continue; } //Verifica se o usuário tem perfil na empresa da notificação
				if(empty($rowN001['N001_Mensagem'])) { $rowN001['N001_Mensagem'] = $rowN001['N001_Id_Mensagem']; }
				//if (isset($rowN001['N001_Id_Mensagem']) && !isset($g['removerMsgUmaVez'][$rowN001['N001_Id_Mensagem']])) {
		        //    $resposta['data'] .= "alertRemover('{$rowN001['N001_Id_Mensagem']}', '{$rowN001['N001_C004_Id']}');";
		        //    $g['removerMsgUmaVez'][$rowN001['N001_Id_Mensagem']] = true;
				//}
				if ((mb_detect_encoding($rowN001['N001_Mensagem'])=='UTF-8') && (!mb_detect_encoding($rowN001['N001_Mensagem'],'UTF-8',true))) {   
					$rowN001['N001_Mensagem'] = utf8_encode($rowN001['N001_Mensagem']); 
					$rowN001['N001_Mensagem'] = trim($rowN001['N001_Mensagem']);                     
			   	}
		        $item = 'false';
		        if (isset($rowN001['N001_Uniqid_Mensagem'])) {
		            $item = "'{$rowN001['N001_Uniqid_Mensagem']}'";
		        }

		        $exibir = 'false';
		        $idNotificacao = "{$rowN001['N001_Id_Mensagem']}-{$rowN001['N001_Uniqid_Mensagem']}-{$rowN001['N001_C004_Id']}";
		        if(isset($config[$idNotificacao]) && $config[$idNotificacao] == '0'){
		        	$exibir = 'true';
				}
				
				if($rowN001['lido'] == 'S'){
					$exibir = 'false';
				}

                $rowN001['N001_Mensagem'] = json_encode($rowN001['N001_Mensagem'], JSON_HEX_APOS|JSON_HEX_QUOT);
		        $resposta['data'] .= <<<EOT
		        	updatedNotifications.push('alertidroot{$rowN001['N001_Id_Mensagem']}-{$rowN001['N001_C004_Id']}');
		            alertAdicionar('{$rowN001['N001_C004_Id']}', '{$rowN001['N001_C004_Nome_Abreviado']}', '{$rowN001['N001_Id_Mensagem']}', {$item}, {$rowN001['N001_Mensagem']}, function() {
		                {$rowN001['N001_Funcao_JS']}
					}, true, '', $exibir);
					
					if(!{$exibir}){
						contador++;
					}

EOT;
			}
			//Remove as notificações que foram removidas do cache
			$resposta['data'] .= <<<EOT
				$("[class*='alertidrootcache1']").each(function(a, b){ var classCheck = $(b).attr('class').split(' '); if($.inArray(classCheck[1], updatedNotifications) == '-1') { $('#messageAlert .alerts').html(parseInt($('#messageAlert .alerts').html()) - 1); $(this).remove(); } });
EOT;
		//}
//
		///*
		//*	NOTIFICAÇÕES NIVEL 2 - EXECUTADAS A CADA 15 SEGUNDOS
		//*/
		//if(!empty($nivel) && $nivel == '2'){
		// 	if(apc_fetch('tempoNotificacaoNivel2-'.$confUsuario['dbDatabase']) && (time() - apc_fetch('tempoNotificacaoNivel2-'.$confUsuario['dbDatabase'])) < 120){
		// 		//$resposta['data'] = 'Em execucao';
		// 		//$resposta['code'] = false;
		// 		//echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
		// 		//die();
		// 	} else {
		// 		apc_store('tempoNotificacaoNivel2-'.$confUsuario['dbDatabase'], time());

		// 		$resposta['data'] .= $NOTIFICACOES->retornoAgenda();
		// 		$resposta['data'] .= $NOTIFICACOES->orcamentoRetornoContato();

		// 		apc_delete('tempoNotificacaoNivel2-'.$confUsuario['dbDatabase']);
		// 	}
		// 	if (empty($resposta['data'])) {
		// 		$resposta['code'] = false;
		// 	}
		}

		/*
		*	NOTIFICAÇÕES NIVEL 3 - EXECUTADAS A CADA 10 MINUTOS
		*/
		/*if(!empty($nivel) && $nivel == '3'){
			if(apc_fetch('tempoNotificacaoNivel3-'.$confUsuario['dbDatabase']) && (time() - apc_fetch('tempoNotificacaoNivel3-'.$confUsuario['dbDatabase'])) < 120 && $r_atualizar != 'true'){
				$resposta['data'] = 'Em execucao';
				$resposta['code'] = false;
				echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
				die();
			} else {
				apc_store('tempoNotificacaoNivel3-'.$confUsuario['dbDatabase'], time());
			}

			$resposta['data'] .= $NOTIFICACOES->financeiroAlerta();
			$resposta['data'] .= $NOTIFICACOES->historicoSemLocacao();
			$resposta['data'] .= $NOTIFICACOES->produtosEstoqueVazio();
			apc_delete('tempoNotificacaoNivel3-'.$confUsuario['dbDatabase']);
			if (empty($resposta['data'])) {
				$resposta['code'] = false;
			}
		}*/
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'montarNotificacoes':
		global $g;

		$resposta = array('code' => true, 'data' => '');
		
		// $cacheN001 = apc_fetch('cacheN001-'.$confUsuario['dbDatabase']);
		// $cacheN001 = unserialize($cacheN001);

		$Dir = $g['pathDados'] . 'tmp/notificacoes/';
		is_dir($Dir) or mkdir($Dir, 0777, true);
		$notificacoes = scandir("{$Dir}");

		if ($r_suporte == true) {
			if($g['C031']['usarSuporteInterno']=="S"){
				$retornoSuporteInterno .= notificacoesSuporteInterno();
			}
			$retornoSuporte = notificacoesSuporte();
			$resposta['data'] = <<<EOT
				{$retornoSuporte} {$retornoSuporteInterno}
			EOT;
		}

		foreach ($notificacoes as $key => $arquivo) {

			if (!strpos($arquivo,'.txt')) {
				continue;
			}

			$arquivoNome = $Dir . $arquivo;

			// Recupera o cache da memória
			$cacheN001 = unserialize(file_get_contents($arquivoNome));

			$retornoSistema = notificacoesSistema($cacheN001);

			$resposta['data'] .= <<<EOT
				{$retornoSistema}
			EOT;
	
			if ($r_showCache == 'S'){
				echo $arquivo .'<br><br>';
				echo "<pre>";
					print_r($cacheN001);
				echo "</pre>";
				continue;
			}

		}

		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";

	break;
}

function notificacoesSistema($cacheN001){
	global $g;
	
	$resposta = "";
	//Verifica se o cache foi modificado
	$C031 = array();
	$config = unserialize($g['C007']['C007_Configuracao_Notificacoes']);
	$resposta .= "var updatedNotifications = [];";

	foreach($cacheN001 as $key => $rowN001){

		$rowN001['N001_Id_Mensagem'] = 'cache1'.$rowN001['N001_Id_Mensagem'];
		$possuiAcesso = true;
		if($key === 'lastUpdate') { continue; }
		if(!empty($rowN001['N001_C007_Id']) && $rowN001['N001_C007_Id'] > 0 && $g['usuarioAtual'] != $rowN001['N001_C007_Id']) { continue; } //Verifica se é o usuário gravado
		if(!empty($rowN001['N001_Flag_Alcada']) && strpos($rowN001['N001_Flag_Alcada'], $g['C007']['C007_Flag_Vendedor'])  === false ) { continue; } //Verifica se o usario possui a alçada
		//Verifica se o usuário possuí acesso a tela que a notificação abre
		if(!empty($rowN001['N001_C029'])){
			$possuiAcesso = false;
			$rowN001['N001_C029'] = unserialize($rowN001['N001_C029']);
			foreach ($rowN001['N001_C029'] as $C039) {
				if(isset($g['c029Ids'][$C039])) { $possuiAcesso = true; }
			}
			if(!$possuiAcesso){ continue; }
		}
		//Verifica as configurações para ver se a empresa está com a notificação habilitada
		if(!empty($rowN001['N001_C031'])){
			$rowN001['N001_C031'] = unserialize($rowN001['N001_C031']);
			foreach ($rowN001['N001_C031'] as $varConfiguracao => $valor) {
				if($g['C031_global'][$rowN001['N001_C004_Id']][$varConfiguracao] != $valor){
					continue 2;
				}
			}
		}
		if(!isset($g['C019'][$rowN001['N001_C004_Id']])) { continue; } //Verifica se o usuário tem perfil na empresa da notificação
		if(empty($rowN001['N001_Mensagem'])) { $rowN001['N001_Mensagem'] = $rowN001['N001_Id_Mensagem']; }
		
		if ((mb_detect_encoding($rowN001['N001_Mensagem'])=='UTF-8') && (!mb_detect_encoding($rowN001['N001_Mensagem'],'UTF-8',true))) {   
			$rowN001['N001_Mensagem'] = utf8_encode($rowN001['N001_Mensagem']); 
			$rowN001['N001_Mensagem'] = trim($rowN001['N001_Mensagem']);                     
		}
		$item = 'false';
		if (isset($rowN001['N001_Uniqid_Mensagem'])) {
			$item = "'{$rowN001['N001_Uniqid_Mensagem']}'";
		}

		$exibir = 'false';
		$idNotificacao = "{$rowN001['N001_Id_Mensagem']}-{$rowN001['N001_Uniqid_Mensagem']}-{$rowN001['N001_C004_Id']}";
		if(isset($config[$idNotificacao]) && $config[$idNotificacao] == '0'){
			$exibir = 'true';
		}

		if($rowN001['lido'] == 'S'){
			$exibir = 'false';
			continue;
		}

		$permiteOcultar = true;
		if (isset($rowN001['N001_Permite_Ocultar'])) {
			$permiteOcultar = "'{$rowN001['N001_Permite_Ocultar']}'";
		}

        $rowN001['N001_Mensagem'] = json_encode($rowN001['N001_Mensagem'], JSON_HEX_APOS|JSON_HEX_QUOT);
		$resposta .= <<<EOT
			updatedNotifications.push('alertidroot{$rowN001['N001_Id_Mensagem']}-{$rowN001['N001_C004_Id']}');
			alertAdicionar('{$rowN001['N001_C004_Id']}', '{$rowN001['N001_C004_Nome_Abreviado']}', '{$rowN001['N001_Id_Mensagem']}', {$item}, {$rowN001['N001_Mensagem']}, function() {
				{$rowN001['N001_Funcao_JS']}
			}, true, '', $exibir,$permiteOcultar);
			
			if(!{$exibir}){
				contador++;
			}
EOT;
	}
	//Remove as notificações que foram removidas do cache
/*	$resposta .= <<<EOT
		$("[class*='alertidrootcache1']").each(function(a, b){ var classCheck = $(b).attr('class').split(' '); if($.inArray(classCheck[1], updatedNotifications) == '-1') { $('#messageAlert .alerts').html(parseInt($('#messageAlert .alerts').html()) - 1); $(this).remove(); } });
EOT;*/
	
	return $resposta;
}

function notificacoesSuporte(){
	global $g,$confUsuario,$sys;
	$resposta = "";
	if ($confUsuario['dbDatabase']!=$sys['dbDatabase']){
		// -------------------------------------------------
		// Conectando com a base e001
		// -------------------------------------------------
		$host    = $sys['dbHost'];
		$usuario = $sys['dbUser'];
		$senha   = $sys['dbPass'];
		$con = mysqli_connect($host, $usuario, $senha,$sys['dbDatabase']);
		// $dbs = mysql_select_db($sys['dbDatabase'], $con) or die (mysql_error());
	} else if ($sys['dominio'] != 'hardness.com.br') {
		$host    = '23.23.29.24';
		$usuario = 'root';
		$senha   = '=!H2r4n6s8!=';
		$con = mysqli_connect($host, $usuario, $senha,'e001');
		// $dbs = mysql_select_db('e001', $con) or die (mysql_error());
	}
	$g['conexaoBanco'] = $con;
	
	// -------------------------------------------------
	$sql = "SELECT * FROM (SELECT T234_Id, T252_Id, T252_Usuario, T252_Descricao, T252_Data_Hora, T252_C007_Id_Cliente, T234_C007_Id_Cliente
				FROM T252 
			LEFT JOIN T234 ON T252_T234_Id=T234_Id
			LEFT JOIN C007 ON T252_C007_Id = C007_Id
				WHERE T252_Flag_Lido = 'N'
				AND IFNULL(T252_C007_Id_Cliente,0) <> '{$g['usuarioAtual']}'
				AND T234_C007_Id_Cliente = '{$g['usuarioAtual']}'
				AND T234_D024_Id = '{$g['C004']['C004_D024_Id_Hardness']}'
			ORDER BY T252_Id DESC) AS CONTEUDO 
			GROUP BY T234_Id";

	$res = mysql_query($sql);
	while($row = mysql_fetch_assoc($res)) {

		$sqlHoras = mysql_query("SELECT TIMESTAMPDIFF(HOUR,'{$row['T252_Data_Hora']}', CURRENT_TIMESTAMP()) as Horas");
		$Horas = mysql_fetch_array($sqlHoras);
		if($Horas['Horas'] > 168){
			// Fecha a mensagem para não ficar acumulando caso o usuário não tenha lido
			mysql_query("UPDATE T252 SET T252_Flag_Lido = 'S' WHERE T252_T234_Id = '{$row['T234_Id']}'");
			$resposta .= <<<EOT
				alertRemover('notificacaoSuporte-{$row['T252_Id']}');
EOT;
			continue;
		}

		$style = 'background: #cdf4cd; border-color: black;';		
		$data = date('d/m/Y à\s H:i', strtotime($row['T252_Data_Hora']));
		$usuario = "<b>SUPORTE HARDNESS</b>";
		$chamado = str_pad($row['T234_Id'],8,"0",STR_PAD_LEFT);
		if(((mb_detect_encoding($row['T252_Descricao'])=='UTF-8') || (!mb_detect_encoding($row['T252_Descricao'])=='UTF-8')) && (!mb_detect_encoding($row['T252_Descricao'],'UTF-8',true))) {   
			$row['T252_Descricao'] = utf8_encode($row['T252_Descricao']); 
			$row['T252_Descricao'] = trim($row['T252_Descricao']);                     
		}
		//$mensagem = "<div style=\"float: left; font-size: 12px;\"><b>{$usuario} em {$data} - Chamado {$chamado}</div></b></br><div style=\"padding: 6px 0 0;\">" . str_replace("\n", '<br/>', str_replace('"', '\\"',str_replace("'", "\\'", html_entity_decode($row['T252_Descricao'])))) . "</div>";
		$mensagem = "<div style=\"float: left; font-size: 12px;\"><b>{$usuario} em {$data} - Chamado {$chamado}</div></b></br><div style=\"padding: 6px 0 0;\">" . $row['T252_Descricao'] . "</div>";
		$mensagem = json_encode($mensagem, JSON_HEX_APOS|JSON_HEX_QUOT);
		$resposta .= <<<EOT
			alertAdicionar(false, false, 'notificacaoSuporte-{$row['T252_Id']}', false, {$mensagem}, function() {
			$('#messageAlert .messages').find('li.alertid' + 'notificacaoSuporte-{$row['T252_Id']}').slideUp('fast', function() {
				alertRemover('notificacaoSuporte-{$row['T252_Id']}');
			});
			abrirJanela(false, divIdRootAberto(), '', menuId('novaNotificacaoSuporte'), '', 'Nova Notificação Suporte', '/outros/registroSuporte/registroSuporte/contentInteracoes/', '&acaoId='+ encodeURIComponent({$row['T234_Id']}), [1000,600]);
			}, false, '{$style}');
			contador++;

EOT;
	}
	return $resposta;
}

function notificacoesSuporteInterno(){
	global $g;
	$resposta = "";
		
	$sql = "SELECT * FROM (SELECT T234_Id, T252_Id, T252_Usuario, T252_Descricao, T252_Data_Hora, T252_C007_Id_Cliente, T234_C007_Id_Cliente
				FROM T252 
			LEFT JOIN T234 ON T252_T234_Id=T234_Id
			LEFT JOIN C007 ON T252_C007_Id = C007_Id
				WHERE T252_Flag_Lido = 'N'
				AND IFNULL(T252_C007_Id_Cliente,0) <> '{$g['usuarioAtual']}'
				AND T234_C007_Id_Cliente = '{$g['usuarioAtual']}'
			ORDER BY T252_Id DESC) AS CONTEUDO 
			GROUP BY T234_Id";

	$res = mysql_query($sql);
	while($row = mysql_fetch_assoc($res)) {
		$style = 'background: #cdf4cd; border-color: black;';		
		$data = date('d/m/Y à\s H:i', strtotime($row['T252_Data_Hora']));
		$usuario = $row['T252_Usuario'];
		$chamado = str_pad($row['T234_Id'],8,"0",STR_PAD_LEFT);
		if ((mb_detect_encoding($row['T252_Descricao'])=='UTF-8') && (!mb_detect_encoding($row['T252_Descricao'],'UTF-8',true))) {   
			$row['T252_Descricao'] = utf8_encode($row['T252_Descricao']); 
			$row['T252_Descricao'] = trim($row['T252_Descricao']);                     
		}
		$mensagem = "<div style=\"float: left; font-size: 12px;\"><b>{$usuario} em {$data} - Chamado {$chamado}</div></b></br><div style=\"padding: 6px 0 0;\">" . str_replace("\n", '<br/>', str_replace('"', '\\"',str_replace("'", "\\'", html_entity_decode($row['T252_Descricao'])))) . "</div>";
        $mensagem = json_encode($mensagem, JSON_HEX_APOS|JSON_HEX_QUOT);
		$resposta .= <<<EOT
			alertAdicionar(false, false, 'notificacaoSuporteInterno-{$row['T252_Id']}', false, $mensagem, function() {
			$('#messageAlert .messages').find('li.alertid' + 'notificacaoSuporteInterno-{$row['T252_Id']}').slideUp('fast', function() {
				alertRemover('notificacaoSuporteInterno-{$row['T252_Id']}');
			});
			abrirJanela(false, divIdRootAberto(), '', menuId('notificacaoSuporteInterno'), '', 'Nova Notificação Suporte Interno', '/outros/registroSuporteCliente/registroSuporteCliente/contentInteracoes/', '&acaoId='+ encodeURIComponent({$row['T234_Id']}), [1000,600]);
			}, false, '{$style}');
			contador++;

EOT;
	}
	return $resposta;
}