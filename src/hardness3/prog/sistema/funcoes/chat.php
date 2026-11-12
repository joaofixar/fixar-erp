<?php
namespace hardness;

switch ($g['r'][3]) {
	case 'enviarMensagem':
		$r_C007_Id = mysql_real_escape_string($r_C007_Id);
		$r_Mensagem = mysql_real_escape_string($r_Mensagem);
		$r_travar = mysql_real_escape_string($r_travar);
		if(empty($r_Data) || $r_Data == '0000-00-00') {
			$r_Data = date("Y-m-d H:i:s");
			$flagLido = '0';
		} else {
			$r_Tempo = empty($r_Tempo) ? '00:00' : $r_Tempo;
			$r_Data = gCorrigeDataInverte($r_Data) . ' ' . $r_Tempo . ':00';
			$r_Data = mysql_real_escape_string($r_Data);
			$flagLido = '2';
		}
		$sql = <<<EOT
INSERT INTO T237
(T237_C007_Id_Origem, T237_C007_Id_Destino, T237_Mensagem, T237_Data_Envio, T237_Flag_Travar, T237_Flag_Lido)
VALUES
('{$g['usuarioAtual']}', '{$r_C007_Id}', '{$r_Mensagem}', '{$r_Data}', '{$r_travar}', '{$flagLido}')
EOT;
		mysql_query($sql);
	break;

	case 'verificarMensagem':
		$callback = (isset($r_callback)) ? $r_callback : false;
		$resposta = array('code' => true, 'data' => '');

		$sql = <<<EOT
SELECT
	*,
	O.C007_Primeiro_Nome as O_C007_Primeiro_Nome,
	D.C007_Primeiro_Nome as D_C007_Primeiro_Nome
FROM T237
LEFT JOIN C007 as O ON O.C007_Id = T237_C007_Id_Origem
LEFT JOIN C007 as D ON D.C007_Id = T237_C007_Id_Destino
WHERE
	(T237_C007_Id_Destino = '{$g['usuarioAtual']}' AND T237_Flag_Lido = 0)
	OR
	(T237_C007_Id_Destino = '{$g['usuarioAtual']}' AND T237_Flag_Lido = 2 AND T237_Data_Envio < NOW())
	OR
	(T237_C007_Id_Origem = '{$g['usuarioAtual']}' AND T237_Flag_Lido = 0 AND T237_Flag_Travar = 'S')

EOT;
	/*
	OR (T237_Flag_Lido = 2 AND T237_Data_Envio < DATE_ADD(NOW(), INTERVAL 30 MINUTE))
	OR (T237_Flag_Lido = 3 AND T237_Data_Envio < NOW())
	*/
		$res = mysql_query($sql);
		$ids = '';
		while($row = mysql_fetch_assoc($res)) {
			$style = '';
			if ($row['T237_Flag_Travar'] == 'S') {
				$style = 'background: lightgreen;';
			}
			$data = date('d/m à\s H:i', strtotime($row['T237_Data_Envio']));
			$responder = 'true';
			if ($row['T237_Flag_Lido'] == 2 | $row['T237_Flag_Lido'] == 3) {
				$extra = '';
				$row['O_C007_Primeiro_Nome'] = substr($row['O_C007_Primeiro_Nome'], 0, 20);
				if (strtotime($row['T237_Data_Envio']) < time()) {
					$extra = 'color: red;';
					$agora = "MENSAGEM AGENDADA! ($data)";
				} else {
					$data = date('d/m à\s H:i');
					$agora = "ATENÇÃO! Faltam 30 minutos em {$data}";
				}
				$usuario = "<div style=\"float: left; font-size: 12px; font-weight: bold; {$extra}\">{$row['O_C007_Primeiro_Nome']} - {$agora}</div>";
				if ($row['T237_C007_Id_Origem'] == $g['usuarioAtual']) {
					$responder = 'false';
					$usuario = "<div style=\"float: left; font-size: 12px; font-weight: bold; {$extra}\">{$agora}</div>";
				}
				$mensagem = "{$usuario}<br/><div style=\"padding: 6px 0 0;\">" . str_replace("\n", '<br/>', str_replace("'", "\\'", htmlentities(utf8_decode($row['T237_Mensagem'])))) . "</div>";
			} else {
				$usuario = "<b>{$row['O_C007_Primeiro_Nome']}</b>";
				if ($row['T237_Flag_Travar'] == 'S' && $row['T237_C007_Id_Origem'] == $g['usuarioAtual']) {
					$usuario = "Para <b>{$row['D_C007_Primeiro_Nome']}</b>";
					$responder = 'false';
				}
				$msg = htmlentities(utf8_decode($row['T237_Mensagem']));
				$msg = str_replace("'", "\\'", $msg);
				$msg = str_replace('&lt;u&gt;', '<u>', $msg);
				$msg = str_replace('&lt;/u&gt;', '</u>', $msg);
				$msg = str_replace('&lt;u&gt;', '<b>', $msg);
				$msg = str_replace('&lt;/u&gt;', '</b>', $msg);
				$mensagem = "<div style=\"float: left; font-size: 12px;\">{$usuario} em {$data}</div><br/><div style=\"padding: 6px 0 0;\">" . str_replace("\n", '<br/>', $msg) . "</div>";
			}
			if ($row['T237_Flag_Travar'] == 'S' && $row['T237_C007_Id_Origem'] != $g['usuarioAtual']) {
				$style .= "cursor:not-allowed;\" title=\"Esta mensagem só pode ser removida pelo emitente.\"";
				$ids .= $ids ? ' && ' : '';
				$ids .= "!$(this).hasClass('alertidchat-{$row['T237_C007_Id_Origem']}-{$row['T237_Id']}')";
				$resposta['data'] .= <<<EOT
					if ($('#messageAlert .messages').find('li.alertid' + 'chat-{$row['T237_C007_Id_Origem']}-{$row['T237_Id']}').size() == 0) {
						alertAdicionar(false, false, 'chat-{$row['T237_C007_Id_Origem']}-{$row['T237_Id']}', false, '{$mensagem}', false, false, '{$style}');
					}
EOT;
			} else {
				$ids .= $ids ? ' && ' : '';
				$ids .= "!$(this).hasClass('alertidchat-{$row['T237_C007_Id_Origem']}-{$row['T237_Id']}')";
				$resposta['data'] .= <<<EOT
					if ($('#messageAlert .messages').find('li.alertid' + 'chat-{$row['T237_C007_Id_Origem']}-{$row['T237_Id']}').size() == 0) {
						alertAdicionar(false, false, 'chat-{$row['T237_C007_Id_Origem']}-{$row['T237_Id']}', false, '{$mensagem}', function() {
							acaoClickMensagemChat('chat-{$row['T237_C007_Id_Origem']}-{$row['T237_Id']}', {$responder});
							return false;
						}, false, '{$style}');
					}
EOT;
			}
		}

		// remove mensagem já lidas em outra aba ou do outro lado (recurso 'travar')
		if ($ids) {
			$resposta['data'] .= <<<EOT
			$('.alertaidchat').each(function() {
				if ({$ids}) {
					$(this).remove();
				}
			});
EOT;
		} else {
			$resposta['data'] .= <<<EOT
			$('.alertaidchat').each(function() {
				$(this).remove();
			});
EOT;
		}
		
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'novaNotificacaoSuporte':
		global $g;
		$callback = (isset($r_callback)) ? $r_callback : false;
		$resposta = array('code' => true, 'data' => '');

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
			$style = 'background: #cdf4cd; border-color: black;';		
			$data = date('d/m/Y à\s H:i', strtotime($row['T252_Data_Hora']));
			$usuario = "<b>SUPORTE HARDNESS</b>";
			$chamado = str_pad($row['T234_Id'],8,"0",STR_PAD_LEFT);
			$mensagem = "<div style=\"float: left; font-size: 12px;\"><b>{$usuario} em {$data} - Chamado {$chamado}</div></b></br><div style=\"padding: 6px 0 0;\">" . str_replace("\n", '<br/>', str_replace('"', '\\"',str_replace("'", "\\'", html_entity_decode($row['T252_Descricao'])))) . "</div>";
			$resposta['data'] .= <<<EOT
				alertAdicionar(false, false, 'notificacaoSuporte-{$row['T252_Id']}', false, '{$mensagem}', function() {
				$('#messageAlert .messages').find('li.alertid' + 'notificacaoSuporte-{$row['T252_Id']}').slideUp('fast', function() {
					alertRemover('notificacaoSuporte-{$row['T252_Id']}');
				});
				abrirJanela(false, divIdRootAberto(), '', menuId('novaNotificacaoSuporte'), '', 'Nova Notificação Suporte', '/outros/registroSuporte/registroSuporte/contentInteracoes/', '&acaoId='+ encodeURIComponent({$row['T234_Id']}), [1000,600]);
				}, false, '{$style}');
				contador++;
EOT;
		}
		
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'verificarAgenda':
		$mensagem = '';

		$sql = <<<EOT
		SELECT
			*
		FROM T237
		LEFT JOIN C007 ON C007_Id = T237_C007_Id_Destino
		WHERE
			T237_C007_Id_Origem = '{$g['usuarioAtual']}'
			AND (T237_Flag_Lido = 2 OR T237_Flag_Lido = 3)
EOT;
		$res = mysql_query($sql);
		while($row = mysql_fetch_assoc($res)) {
			$data = date('d/m à\s H:i', strtotime($row['T237_Data_Envio']));
			$responder = 'true';
			$usuario = "<div style=\"float: left; font-size: 12px;\">Para <b>{$row['C007_Primeiro_Nome']}</b> para <b>{$data}</b></div>";
			if ($row['T237_C007_Id_Destino'] == $g['usuarioAtual']) {
				$responder = 'false';
				$usuario = "<div style=\"float: left; font-size: 12px;\">Para <b>Você</b> para <b>{$data}</b></div>";
			}
			$mensagem .= "<li class=\"itemAgendado alertidagenda-{$row['T237_C007_Id_Origem']}-{$row['T237_Id']}\" onclick=\"acaoClickMensagemChat('agenda-{$row['T237_C007_Id_Origem']}-{$row['T237_Id']}', false, true); event.stopPropagation();\"><div><div style=\"float: right; font-size: 12px;\"><b></b></div>{$usuario}<br/><div style=\"padding: 6px 0 0;\">" . str_replace("\n", '<br/>', str_replace("'", "\\'", htmlentities(utf8_decode($row['T237_Mensagem'])))) . "</div></div></li>";
		}

		if (!empty($mensagem)) {
			$extra = <<<EOT
			<li class="itemAgendado" style="background-color: #D5D5D5; font-weight: bold; padding: 5px; text-align: center;">Suas próximas mensagens agendadas:</li>
EOT;
			$mensagem = $extra . $mensagem;
		}

		echo $mensagem;
	break;

	case 'marcarLido':
		$r_T237_Id = mysql_real_escape_string($r_T237_Id);
		/*
		$sql = <<<EOT
SELECT T237_Id FROM T237
WHERE
	T237_Id = '{$r_T237_Id}'
	AND T237_Flag_Lido = 2
	AND T237_Data_Envio < DATE_ADD(NOW(), INTERVAL 30 MINUTE)
EOT;
		$res = mysql_query($sql);
		if (mysql_num_rows($res) > 0) {
			$sql = <<<EOT
UPDATE T237 SET T237_Flag_Lido = 3, T237_Data_Fechado = NOW() WHERE T237_C007_Id_Destino = '{$g['usuarioAtual']}' AND T237_Id = '{$r_T237_Id}'
EOT;
		} else {
		*/
		$sql = <<<EOT
UPDATE T237 SET T237_Flag_Lido = 1, T237_Data_Fechado = NOW() WHERE (T237_C007_Id_Destino = '{$g['usuarioAtual']}' OR T237_C007_Id_Origem = '{$g['usuarioAtual']}') AND T237_Id = '{$r_T237_Id}'
EOT;
		//}
		mysql_query($sql);
	break;

	
	case 'removerMensagem':
		$r_T237_Id = mysql_real_escape_string($r_T237_Id);
		$sql = <<<EOT
DELETE FROM T237 WHERE (T237_C007_Id_Destino = '{$g['usuarioAtual']}' OR T237_C007_Id_Origem = '{$g['usuarioAtual']}') AND T237_Id = '{$r_T237_Id}'
EOT;
		mysql_query($sql);
	break;
}
