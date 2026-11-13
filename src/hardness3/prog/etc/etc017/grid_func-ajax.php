<?php
namespace hardness;
$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'processarTarefas':
		//Atualizando registros D085
		mysql_query("UPDATE D085 SET D085_T007_Id = D085_Id_Entidade WHERE D085_Entidade = 'T007' AND D085_T007_Id IS NULL;");
		mysql_query("UPDATE D085 SET D085_T005_Id = D085_Id_Entidade WHERE D085_Entidade = 'T005' AND D085_T005_Id IS NULL;");
		mysql_query("UPDATE D085 SET D085_T003_Id = D085_Id_Entidade WHERE D085_Entidade = 'T003' AND D085_T003_Id IS NULL;");
		mysql_query("UPDATE D085 SET D085_Flag_Manual = 'S' WHERE D085_Entidade = '' AND IFNULL(D085_T003_Id,0) <= 0 AND IFNULL(D085_T005_Id,0) <= 0 AND IFNULL(D085_T007_Id,0) <= 0 AND IFNULL(D085_T139_Id,0) <= 0;");
		mysql_query("UPDATE D085 SET D085_Flag_Manual = 'N' WHERE D085_Entidade != '' OR D085_T139_Id > 0;");
		//Vinculando orçamentos, pedidos e notas
		mysql_query("UPDATE D085
		LEFT JOIN T007 ON T007_Id=D085_T007_Id
		LEFT JOIN T005 ON T005_Id=T007_T005_Id
		SET D085_T003_Id = T005_T003_Id,
		D085_T005_Id = T007_T005_Id
		WHERE D085_T007_Id > 0;");
		mysql_query("UPDATE D085
		LEFT JOIN T003 ON T003_Id=D085_T003_Id
		LEFT JOIN T005 ON T003_Id=T005_T003_Id
		LEFT JOIN T007 ON T005_Id=T007_T005_Id
		SET D085_T005_Id = T005_Id,
		D085_T007_Id = T007_Id
		WHERE D085_T003_Id > 0;");
		mysql_query("UPDATE D085
		LEFT JOIN T005 ON T005_Id=D085_T005_Id
		LEFT JOIN T003 ON T003_Id=T005_T003_Id
		LEFT JOIN T007 ON T005_Id=T007_T005_Id
		SET D085_T003_Id = T005_T003_Id,
		D085_T007_Id = T007_Id
		WHERE D085_T005_Id > 0;");

		//Processando orçamentos em aberto
		$sql = mysql_query("SELECT GROUP_CONCAT(D085_Id SEPARATOR ', ') AS Ids, D085_T003_Id, COUNT(*) AS Interacoes, D085_D024_Id, D085_C007_Id FROM D085 WHERE D085_T003_Id > 0 AND IFNULL(D085_T259_Id,0) <= 0 GROUP BY D085_T003_Id HAVING Interacoes > 1 ORDER BY D085_Data DESC");
		while($row = mysql_fetch_array($sql)){
			mysql_query("INSERT INTO T259 (T259_D024_Id, T259_C007_Id, T259_Data_Hora_Inicio) VALUES ('{$row['D085_D024_Id']}', '{$row['D085_C007_Id']}', CURRENT_TIMESTAMP)");
			$T259_Id = $g['mysqlLastId'];
			$sqlInteracoes = mysql_query("SELECT D085_Id, CONCAT(D085_Data, ' ', D085_Hora) as D085_Data_Hora, D085_Mensagem FROM D085 WHERE D085_T003_Id = '{$row['D085_T003_Id']}' AND D085_Id IN({$row['Ids']}) ORDER BY CONCAT(D085_Data, ' ', D085_Hora) ASC");
			$D085_Data_Hora_Inicio = '';
			while($rowInteracoes = mysql_fetch_array($sqlInteracoes)){
				mysql_query("UPDATE D085 SET D085_T259_Id = '{$T259_Id}' WHERE D085_Id = '{$rowInteracoes['D085_Id']}'");
				$D085_Data_Hora = $rowInteracoes['D085_Data_Hora'];
				if(empty($D085_Data_Hora_Inicio)) { $D085_Data_Hora_Inicio = $D085_Data_Hora; }
			}
			mysql_query("UPDATE T259 SET T259_Data_Hora = '{$D085_Data_Hora}', T259_Data_Hora_Inicio = '{$D085_Data_Hora_Inicio}' WHERE T259_Id = '{$T259_Id}'");
		}

		//Processando pedidos em aberto (sem orçamento)
		$sql = mysql_query("SELECT GROUP_CONCAT(D085_Id SEPARATOR ', ') AS Ids, D085_T005_Id, COUNT(*) AS Interacoes, D085_D024_Id, D085_C007_Id FROM D085 WHERE IFNULL(D085_T003_Id,0) <= 0 AND D085_T005_Id > 0 AND IFNULL(D085_T259_Id,0) <= 0 GROUP BY D085_T005_Id HAVING Interacoes > 1 ORDER BY D085_Data DESC");
		while($row = mysql_fetch_array($sql)){
			mysql_query("INSERT INTO T259 (T259_D024_Id, T259_C007_Id, T259_Data_Hora_Inicio) VALUES ('{$row['D085_D024_Id']}', '{$row['D085_C007_Id']}', CURRENT_TIMESTAMP)");
			$T259_Id = $g['mysqlLastId'];
			$sqlInteracoes = mysql_query("SELECT D085_Id, CONCAT(D085_Data, ' ', D085_Hora) as D085_Data_Hora, D085_Mensagem FROM D085 WHERE D085_T005_Id = '{$row['D085_T005_Id']}' AND D085_Id IN({$row['Ids']}) ORDER BY CONCAT(D085_Data, ' ', D085_Hora) ASC");
			$D085_Data_Hora_Inicio = '';
			while($rowInteracoes = mysql_fetch_array($sqlInteracoes)){
				mysql_query("UPDATE D085 SET D085_T259_Id = '{$T259_Id}' WHERE D085_Id = '{$rowInteracoes['D085_Id']}'");
				$D085_Data_Hora = $rowInteracoes['D085_Data_Hora'];
				if(empty($D085_Data_Hora_Inicio)) { $D085_Data_Hora_Inicio = $D085_Data_Hora; }
			}
			mysql_query("UPDATE T259 SET T259_Data_Hora = '{$D085_Data_Hora}', T259_Data_Hora_Inicio = '{$D085_Data_Hora_Inicio}' WHERE T259_Id = '{$T259_Id}'");
		}

		//Processando todos os D085 sem vinculo
		$D085_Data_Hora_Inicio = '';
		$sql = mysql_query("SELECT D085_Id, D085_D024_Id, D085_C007_Id, CONCAT(D085_Data, ' ', D085_Hora) as D085_Data_Hora FROM D085 WHERE IFNULL(D085_T259_Id,0) <= 0 ORDER BY CONCAT(D085_Data, ' ', D085_Hora) DESC");
		while($row = mysql_fetch_array($sql)){
			if(empty($D085_Data_Hora_Inicio)) { $D085_Data_Hora_Inicio = $D085_Data_Hora; }
			mysql_query("INSERT INTO T259 (T259_D024_Id, T259_C007_Id, T259_Data_Hora, T259_Data_Hora_Inicio) VALUES ('{$row['D085_D024_Id']}', '{$row['D085_C007_Id']}', '{$row['D085_Data_Hora']}', '{$D085_Data_Hora_Inicio}')");
			$T259_Id = $g['mysqlLastId'];
			mysql_query("UPDATE D085 SET D085_T259_Id = '{$T259_Id}' WHERE D085_Id = '{$row['D085_Id']}'");
		}

		//Processando COTAÇÃO
		$sqlT218 = mysql_query("SELECT T219_T218_Id FROM T219 LEFT JOIN T218 ON T218_Id=T219_T218_Id WHERE IFNULL(T219_T004_Id,0) > 0 OR IFNULL(T219_T006_Id,0) > 0 GROUP BY T219_T218_Id ORDER BY T218_Data_Emissao DESC");
		while($rowT218 = mysql_fetch_assoc($sqlT218)){
			$sqlVenda = mysql_query("SELECT IFNULL(T004_T003_Id,0) AS T004_T003_Id, IFNULL(T006_T005_Id,0) AS T006_T005_Id, T218_Grupo, T218_Data_Emissao FROM T219
		                                                                                  LEFT JOIN T218 ON T218_Id=T219_T218_Id
		                                                                                  LEFT JOIN T006 ON T006_Id=T219_T006_Id
		                                                                                  LEFT JOIN T004 ON T004_Id=T219_T004_Id
		                                                                                  WHERE T219_T218_Id = '{$rowT218['T219_T218_Id']}'
		                                                                                  GROUP BY T004_T003_Id, T006_T005_Id");
			while($rowVenda = mysql_fetch_assoc($sqlVenda)){
				//Verifica a tarefa do orçamento/pedido
				$sqlTarefa = mysql_query("SELECT D085_T259_Id, D085_Hora FROM D085 WHERE (D085_T005_Id = '{$rowVenda['T006_T005_Id']}' AND IFNULL(D085_T005_Id,0) > 0) OR (D085_T003_Id = '{$rowVenda['T004_T003_Id']}' AND IFNULL(D085_T003_Id,0) > 0)");
				if(mysql_num_rows($sqlTarefa) > 0){
					$rowTarefa = mysql_fetch_array($sqlTarefa);
					$sqlCheck = mysql_query("SELECT * FROM D085 WHERE D085_T259_Id = '{$rowTarefa['D085_T259_Id']}' AND D085_Id_Entidade = '{$rowVenda['T218_Grupo']}' AND D085_Entidade = 'T218'");
					if(mysql_num_rows($sqlCheck) <= 0){
						mysql_query("INSERT INTO D085 (D085_T259_Id, D085_C007_Id, D085_Data, D085_Hora, D085_Id_Entidade, D085_Entidade, D085_Mensagem) VALUES ('{$rowTarefa['D085_T259_Id']}', '{$g['usuarioAtual']}', '{$rowVenda['T218_Data_Emissao']}', ADDTIME('{$rowTarefa['D085_Hora']}', '00:01:00'), '{$rowVenda['T218_Grupo']}', 'T218', 'Cot {$rowVenda['T218_Grupo']}')");
					}
				}
			}
		}

		//Processando OC
		$sqlT224 = mysql_query("SELECT T225_T224_Id FROM T225 LEFT JOIN T224 ON T224_Id=T225_T224_Id LEFT JOIN T219 ON T219_Id=T225_T219_Id WHERE IFNULL(T219_T006_Id,0) > 0 GROUP BY T225_T224_Id ORDER BY T224_Data_Emissao DESC");
		while($rowT224 = mysql_fetch_assoc($sqlT224)){
			//Verifica as tarefas de cada produto ligado a um pedido
			$sqlVenda = mysql_query("SELECT T224_Id, T006_T005_Id, T224_Data_Emissao, T218_Grupo FROM T225
																LEFT JOIN T224 ON T224_Id=T225_T224_Id
		                                                        LEFT JOIN T219 ON T219_Id=T225_T219_Id
																LEFT JOIN T218 ON T218_Id=T219_T218_Id
		                                                        LEFT JOIN T006 ON T006_Id=T219_T006_Id
		                                                        WHERE T225_T224_Id = '{$rowT224['T225_T224_Id']}'
		                                                        GROUP BY T006_T005_Id");
			while($rowVenda = mysql_fetch_assoc($sqlVenda)){
				//Verifica a tarefa do pedido
				$sqlTarefa = mysql_query("SELECT D085_T259_Id, D085_Data, D085_Hora FROM D085 WHERE D085_Entidade = 'T005' AND D085_T005_Id = '{$rowVenda['T006_T005_Id']}' AND IFNULL(D085_T005_Id,0) > 0");
				if(mysql_num_rows($sqlTarefa) > 0){
					$rowTarefa = mysql_fetch_array($sqlTarefa);
					$sqlCheck = mysql_query("SELECT * FROM D085 WHERE D085_T259_Id = '{$rowTarefa['D085_T259_Id']}' AND D085_Id_Entidade = '{$rowT224['T225_T224_Id']}' AND D085_Entidade = 'T224'");
					$horaTarefaOriginal = $rowTarefa['D085_Hora'];
					if(mysql_num_rows($sqlCheck) <= 0){
						$sqlHoraCotacao = mysql_query("SELECT D085_Hora FROM D085 WHERE D085_Entidade = 'T218' AND D085_Id_Entidade = '{$rowVenda['T218_Grupo']}' AND D085_T259_Id = '{$rowTarefa['D085_T259_Id']}'");
						$rowHoraCotacao = mysql_fetch_assoc($sqlHoraCotacao);
						if(date('d', strtotime($rowTarefa['D085_Data'])) < date('d', strtotime($rowVenda['T224_Data_Emissao']))) { 
							$rowTarefa['D085_Hora'] = '00:00:00'; 
						} else if(date('d', strtotime($rowTarefa['D085_Data'])) == date('d', strtotime($rowVenda['T224_Data_Emissao']))){
							$rowTarefa['D085_Hora'] = $rowHoraCotacao['D085_Hora'];
						}
						if(strtotime($horaTarefaOriginal) > strtotime($rowHoraCotacao['D085_Hora'])){
							$rowTarefa['D085_Hora'] = $horaTarefaOriginal;
						}
						mysql_query("INSERT INTO D085 (D085_T259_Id, D085_C007_Id, D085_Data, D085_Hora, D085_Id_Entidade, D085_Entidade, D085_Mensagem) VALUES ('{$rowTarefa['D085_T259_Id']}', '{$g['usuarioAtual']}', '{$rowVenda['T224_Data_Emissao']}', ADDTIME('{$rowTarefa['D085_Hora']}', '00:01:00'), '{$rowT224['T225_T224_Id']}', 'T224', 'OC {$rowVenda['T224_Id']}')");
					}
				}
			}
		}
	break;

	case 'excluirTarefa':
		$T259_Id = isset($r_T259_Id) ? $r_T259_Id : false;
		mysql_query("UPDATE T259 SET T259_Flag_Excluido = 'S', T259_Data_Exclusao = CURRENT_TIMESTAMP() WHERE T259_Id = '{$T259_Id}'");
	break;

	case 'recuperarTarefa':
		$T259_Id = isset($r_T259_Id) ? $r_T259_Id : false;
		mysql_query("UPDATE T259 SET T259_Flag_Excluido = 'N' WHERE T259_Id = '{$T259_Id}'");
		mysql_query("DELETE FROM T260 WHERE T260_T259_Id = '{$T259_Id}'");
	break;

	case 'atualizarAssuntoTarefa':
		require_once('bibliotecas/funcoesCallback/assuntoTarefa.php');
		$sqlT259 = mysql_query("SELECT T259_C007_Id FROM T259 WHERE T259_Id = '{$r_T259_Id}'");
		$rowT259 = mysql_fetch_array($sqlT259);
		$arrayAssuntoTarefa = array(
		    'campoAtual' => '',
		    'todosCampos' => array(
		        'T259_Id' => $r_T259_Id,
		        'T259_C007_Id' => $rowT259['T259_C007_Id']
		    )
		);
		assuntoTarefa($arrayAssuntoTarefa, false);
	break;

	case 'mudarVisao':
		$file = $g['pathDadosUsuario'] . 'gridFiltro/' . $r_tela;
		if(file_exists($file)) {
			$content = unserialize(file_get_contents($file));
			$content['visao'] = !empty($r_visao) ? $r_visao : 'vertical';
			file_put_contents($file, serialize($content));
		} else {
			$content['visao'] = !empty($r_visao) ? $r_visao : 'vertical';
			file_put_contents($file, serialize($content));
		}
	break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";