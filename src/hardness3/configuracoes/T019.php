<?php
/**
 * Tabela T020

 */
 
 $conf['restricaoTravar']['T019'] = <<<'EOT'
	$retorno = true;
	$sqlT019 = mysql_query("SELECT * FROM T019 WHERE T019_Id = '{$valorPk}'");
	$rowT019 = mysql_fetch_array($sqlT019);

	if ($operacao == 'excluir') {
		if (!isset($g['c029Ids'][111])) {
	 		$retorno = 'A ação 111 no perfil do usuario Não esta marcada:<br><b>"Excluir títulos e lançamentos"</b>';
		} elseif ($rowT019['T019_Conciliado'] == '1') {
	 		$retorno = 'Lançamento já conciliado.';
		}
	}

	if ($operacao == 'alterar') {
		if ($rowT019['T019_Conciliado'] == '1') {
			$retorno = 'Lançamento já conciliado.';
		} elseif(isset($g['c029Ids'][271])) {
			$retorno = 'A ação 271 no perfil do usuario esta marcada:<br><b>"Não permitir alterar lançamentos"</b>';
		}
	}
EOT;
 
$conf['restricaoIncluirUpdate']['T019_Obs_Estorno'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

  $conf['restricaoIncluirUpdate']['T019_Data_Lancamento'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

  $conf['restricaoIncluirUpdate']['T019_Referente'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

  $conf['restricaoIncluirUpdate']['T019_Valor_Lancamento'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

  $conf['restricaoIncluirUpdate']['T019_Flag_Deb_Crd'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T019_D007_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D007_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T019_D032_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D032_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T019_D014_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D014_Id', $valor);
EOT;

$conf['triggers']['T019']['insertAfter'] = <<<'EOT'
	require_once('bibliotecas/classes/FIN009.php');
	$FIN009 = new FIN009();
	$sql = mysqli_query("SELECT T019_D014_Id, T019_Data_Lancamento, IFNULL(T019_T002_Id,0) T019_T002_Id FROM T019 WHERE T019_Id = '{$chaveValor}'");
	$row = mysql_fetch_array($sql);
	if ($row['T019_T002_Id'] <= 0) {
		$mes = date("n",strtotime($row['T019_Data_Lancamento']));
		$ano = date("Y",strtotime($row['T019_Data_Lancamento']));
		$periodo = array('mes' => $mes, 'mes_final' => $mes, 'ano_atual' => $ano, 'ano' => $ano, 'ano_final' => $ano);
		$FIN009->FIN009CalculaProjecao($row['T019_D014_Id'], $periodo, null, null, "{$chaveValor}");
	}

EOT;
$conf['triggers']['T019']['updateAfter'] = <<<'EOT'
	require_once('bibliotecas/classes/FIN009.php');
	$FIN009 = new FIN009();
	$sql = mysqli_query("SELECT T019_D014_Id, T019_Data_Lancamento, IFNULL(T019_T002_Id,0) T019_T002_Id FROM T019 WHERE T019_Id = '{$chaveValor}'");
	$row = mysql_fetch_array($sql);
	if ($row['T019_T002_Id'] <= 0) {
		$mes = date("n",strtotime($row['T019_Data_Lancamento']));
		$ano = date("Y",strtotime($row['T019_Data_Lancamento']));
		$periodo = array('mes' => $mes, 'mes_final' => $mes, 'ano_atual' => $ano, 'ano' => $ano, 'ano_final' => $ano);
		$FIN009->FIN009CalculaProjecao($row['T019_D014_Id'], $periodo, null, null, "{$chaveValor}");
	}

EOT;
                      
?>