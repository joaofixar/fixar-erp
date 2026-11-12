<?php
/**
* camposTipo
*/
$conf['camposTipo']['D085_Data_Previsao_Retorno'] = array('data');
$conf['camposTipo']['D085_Data_Retorno'] = array('data');

/**
* restricaoIncluirUpdate
*/
$conf['restricaoIncluirUpdate']['D085_Mensagem'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

/**
* triggers
*/
$conf['triggers']['D085']['insertAfter'] = <<<'EOT'
	global $T259_Id_After_Insert;
	$D085=mysqli_query("SELECT IFNULL(D085_T003_Id,0) as D085_T003_Id, IFNULL(D085_T005_Id,0) as D085_T005_Id, IFNULL(D085_T007_Id,0) as D085_T007_Id, D085_D024_Id, D085_C007_Id, CONCAT(D085_Data, ' ', D085_Hora) as D085_Data_Hora, D085_T259_Id, D085_T139_Id, D085_Flag_Manual, D085_Entidade from D085 where D085_Id='$chaveValor'");
	$mD085=mysql_fetch_array($D085);

	if($mD085['D085_T003_Id'] > 0){
		mysqli_query("UPDATE T003 SET T003_Status_Retorno_Email = 'OK' WHERE T003_Id = '{$mD085['D085_T003_Id']}'");
	}

 	if($mD085['D085_T003_Id'] <= 0 && $mD085['D085_T005_Id'] <= 0  && $mD085['D085_T007_Id'] <= 0){
		// inclusao manual do contato, vai processar a carteira com dados da empresa atual
		mysqli_query("CALL D024_Ativar_Cliente(0, 0, 0, '{$mD085['D085_D024_Id']}', '{$g['usuarioAtual']}', 0,'{$g['empresaAtual']}')");
	} 

	$D085_T259_Id = $mD085['D085_T259_Id'];
	if($D085_T259_Id <= 0){
		if($mD085['D085_Entidade'] == 'T003') {
			//Procura alguma tarefa existente para o e-mail ou agenda vinculado ao orçamento
			$rowT003 = mysql_fetch_assoc(mysql_query("SELECT IFNULL(T003_T139_Id,0) AS T003_T139_Id, IFNULL(T003_D085_Id,0) AS T003_D085_Id FROM T003 WHERE T003_Id = '{$mD085['D085_T003_Id']}'"));
			$rowD085 = mysql_fetch_assoc(mysql_query("SELECT D085_T259_Id FROM D085 WHERE (D085_T139_Id = '{$rowT003['T003_T139_Id']}' AND IFNULL(D085_T139_Id,0) > 0) OR D085_Id = '{$rowT003['T003_D085_Id']}' OR D085_T003_Id = '{$mD085['D085_T003_Id']}' LIMIT 1"));
		    $D085_T259_Id = $rowD085['D085_T259_Id'];
		} else if($mD085['D085_Entidade'] == 'T005') {
			$rowT005 = mysql_fetch_assoc(mysql_query("SELECT IFNULL(T005_T003_Id,0) AS T005_T003_Id FROM T005 WHERE T005_Id = '{$mD085['D085_T005_Id']}'"));
			$rowD085 = mysql_fetch_assoc(mysql_query("SELECT D085_T259_Id FROM D085 WHERE D085_T003_Id = '{$rowT005['T005_T003_Id']}' AND IFNULL(D085_T003_Id,0) > 0 LIMIT 1"));
            $D085_T259_Id = $rowD085['D085_T259_Id'];
		} else if($mD085['D085_Entidade'] == 'T007') {
			$rowT007 = mysql_fetch_assoc(mysql_query("SELECT IFNULL(T007_T005_Id,0) AS T007_T005_Id FROM T007 WHERE T007_Id = '{$mD085['D085_T007_Id']}'"));
			$rowD085 = mysql_fetch_assoc(mysql_query("SELECT D085_T259_Id FROM D085 WHERE D085_T005_Id = '{$rowT007['T007_T005_Id']}' AND IFNULL(D085_T005_Id,0) > 0 LIMIT 1"));
            $D085_T259_Id = $rowD085['D085_T259_Id'];
		} else if($mD085['D085_Entidade'] == '' && ($mD085['D085_T003_Id'] > 0 || $mD085['D085_T005_Id'] > 0)){
			if($mD085['D085_T003_Id'] > 0){
				$rowD085 = mysql_fetch_assoc(mysql_query("SELECT D085_T259_Id FROM D085 WHERE D085_T003_Id = '{$mD085['D085_T003_Id']}' AND IFNULL(D085_T003_Id,0) > 0 LIMIT 1"));
	            $D085_T259_Id = $rowD085['D085_T259_Id'];
	        } else {
	        	$rowD085 = mysql_fetch_assoc(mysql_query("SELECT D085_T259_Id FROM D085 WHERE D085_T005_Id = '{$mD085['D085_T005_Id']}' AND IFNULL(D085_T005_Id,0) > 0 LIMIT 1"));
	            $D085_T259_Id = $rowD085['D085_T259_Id'];
	        }
		}

		if($D085_T259_Id <= 0){
			mysqli_query("INSERT INTO T259 (T259_C007_Id, T259_D024_Id, T259_Data_Hora, T259_Data_Hora_Inicio) VALUES ('{$mD085['D085_C007_Id']}', '{$mD085['D085_D024_Id']}', '{$mD085['D085_Data_Hora']}', '{$mD085['D085_Data_Hora']}')");
			$D085_T259_Id = mysql_insert_id();
			mysqli_query("INSERT INTO T162 (T162_C007_Id, T162_Acao, T162_Query, T162_DataHora, T162_Tabela, T162_Registro_Id, T162_Registro, T162_IP) VALUES ('{$g['usuarioAtual']}', 'I', 'Trigger After Insert D085', '" . date('Y-m-d H:i:s') . "', 'T259', '{$D085_T259_Id}', '', '{$_SERVER['REMOTE_ADDR']}');",$g['conexaoBancoAuditoria']);
		}
		mysqli_query("UPDATE D085 SET D085_T259_Id = '{$D085_T259_Id}' WHERE D085_Id = '{$chaveValor}'");
	}

	if($mD085['D085_T139_Id'] > 0 || $mD085['D085_Flag_Manual'] == 'S'){
		mysqli_query("UPDATE T260 SET T260_Flag_Lido = 'N' WHERE T260_T259_Id = '{$D085_T259_Id}'");
	}

	mysqli_query("UPDATE T259 SET T259_Data_Hora = '{$mD085['D085_Data_Hora']}' WHERE T259_Id = '{$D085_T259_Id}'");

	$sqlT259 = mysql_query("SELECT T259_C007_Id FROM T259 WHERE T259_Id = '{$D085_T259_Id}'");
	$rowT259 = mysql_fetch_assoc($sqlT259);

	require_once('bibliotecas/funcoesCallback/assuntoTarefa.php');
    $arrayAssuntoTarefa = array(
        'campoAtual' => '',
        'todosCampos' => array(
            'T259_Id' => $D085_T259_Id,
            'T259_C007_Id' => $rowT259['T259_C007_Id']
        )
    );
    assuntoTarefa($arrayAssuntoTarefa, false);
    $T259_Id_After_Insert = $D085_T259_Id;
EOT;

$conf['triggers']['D085']['updateAfter'] = <<<'EOT'
	$D085=mysqli_query("select D085_T003_Id, D085_T005_Id, D085_T007_Id, D085_D024_Id, D085_C007_Id, CONCAT(D085_Data, ' ', D085_Hora) as D085_Data_Hora, D085_T259_Id, D085_T139_Id, D085_Flag_Manual, D085_Entidade from D085 where D085_Id='$chaveValor'");
	$mD085=mysql_fetch_array($D085);

	$sqlT259 = mysql_query("SELECT T259_C007_Id FROM T259 WHERE T259_Id = '{$mD085['D085_T259_Id']}'");
	$rowT259 = mysql_fetch_assoc($sqlT259);

	require_once('bibliotecas/funcoesCallback/assuntoTarefa.php');
    $arrayAssuntoTarefa = array(
        'campoAtual' => '',
        'todosCampos' => array(
            'T259_Id' => $mD085['D085_T259_Id'],
            'T259_C007_Id' => $rowT259['T259_C007_Id']
        )
    );
    assuntoTarefa($arrayAssuntoTarefa, false);
EOT;

$conf['restricaoTravar']['D085'] = <<<'EOT'
	global $g;
	if ($operacao == 'excluir' || $operacao == 'alterar' || $operacao == 'incluir') {
		if($g['c029Ids'][238]){
			$retorno = false;
		}else{
			$retorno = true;
		}
	}
EOT;

/**
* ignorarRelacionamentoExcluir
*/
$conf['ignorarRelacionamentoExcluir'] = array_merge($conf['ignorarRelacionamentoExcluir'], 
	array('D024_D085_Id')
);
