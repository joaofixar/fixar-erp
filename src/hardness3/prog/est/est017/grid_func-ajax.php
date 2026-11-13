<?php
namespace hardness;
// Classes
require_once('bibliotecas/classes/EST017.php');
$EST017 = new EST017();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'est017VerificarGerarOrdemProducao':
		$sql = base64_decode($r_extra);
		$sql = gInsertExtraWhere($sql, "T006_Flag_Gerar_Producao='S' AND T006_C007_Id_Comprador = '{$g['usuarioAtual']}'");

		$res = mysql_query($sql);
		$erro = mysql_error();
		if(!empty($erro)){
			$resposta['code'] = false;
			$resposta['data'] = "Erro ao verificar produtos selecionados, por favor entre em contato com o suporte técnico.";
			break;
		}

		if(mysql_num_rows($res) > 0){
			$retorno = "<span style='font-size: 13px'>Atenção</span><br>Os produtos abaixo serão incluídos na ordem de produção<br><br>";
			while($row = mysql_fetch_array($res)){
				$row['Saldo'] = gCorrigeNumero($row['Saldo'], 5);
				$retorno .= "{$row['T006_Codigo_Produto']}  -  {$row['D001_Descricao_Produto']} - {$row['Saldo']} {$row['D037_Unidade']}<br>";
			}

			$resposta['code'] = true;
			$resposta['data'] = $retorno;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = "Nenhum produto foi selecionado, por favor verifique!";
		}
	break;

	case 'est017GerarOrdemProducao':
		$sql = base64_decode($r_extra);
		$sql = gInsertExtraWhere($sql, "T006_Flag_Gerar_Producao='S' AND T006_C007_Id_Comprador = '{$g['usuarioAtual']}'");

		$res = mysql_query($sql);
		$erro = mysql_error();
		if(!empty($erro)){
			$resposta['code'] = false;
			$resposta['data'] = "Erro ao verificar produtos selecionados, por favor entre em contato com o suporte técnico.";
			break;
		}

		if(mysql_num_rows($res) > 0){
			$T255_Id = 0;
			$retorno = $EST017->gerarOrdemProducao($sql);

			if ($retorno !== true) {
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			} else {
				$resposta['code'] = true;
				$resposta['T255_Id'] = $T255_Id;
			}
		} else {
			$resposta['code'] = false;
			$resposta['data'] = "Nenhum produto foi selecionado, por favor verifique!";
		}
	break;

	case 'checkboxGerarOrdemProducao':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T006_Id = isset($r_T006_Id) ? $r_T006_Id : false;
		$C007_Id = ($selected == 'S') ? $g['usuarioAtual'] : 0;
		mysql_query("UPDATE T006 SET T006_Flag_Gerar_Producao = '{$selected}', T006_C007_Id_Comprador = '{$C007_Id}' WHERE T006_Id = '{$T006_Id}'");
	break;

	case 'est017VerificarProcessarProducao':
		$extra = base64_decode($r_extra);
		$res = mysql_query("SELECT T256_Id FROM T256 " . $extra);
		$erro = mysql_error();
		if(!empty($erro)){
			$resposta['code'] = false;
			$resposta['data'] = "Erro ao verificar entradas de produção, por favor entre em contato com o suporte técnico.";
			break;
		}

		if(mysql_num_rows($res) > 0){
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = "Nenhuma entrada de produção pendente foi encontrada, por favor verifique!";
		}
	break;

	case 'atualizaT257Local':
		$T066_Id = isset($_REQUEST['T066_Id']) ? $_REQUEST['T066_Id'] : false;
		$T257_Id = isset($_REQUEST['T257_Id']) ? $_REQUEST['T257_Id'] : false;
		$D004_Id = getD004($T066_Id);
		mysql_query("UPDATE T257 SET T257_T066_Id = '{$T066_Id}' WHERE T257_Id = '{$T257_Id}'");
		$resposta['data'] = true;
	break;

	case 'processarProdutosBaixa':
		$retorno = $EST017->processarProdutosBaixa($r_T255_Id);
		if($retorno !== true){
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'baixarEstoqueMateriais':
		$retorno = $EST017->baixarEstoqueMateriais($r_T255_Id, $r_sql);
		if($retorno !== true){
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'atualizaT264Local':
		$T066_Id = isset($_REQUEST['T066_Id']) ? $_REQUEST['T066_Id'] : false;
		$T264_Id = isset($_REQUEST['T264_Id']) ? $_REQUEST['T264_Id'] : false;
		mysql_query("UPDATE T264 SET T264_T066_Id = '{$T066_Id}' WHERE T264_Id = '{$T264_Id}'");

		$resposta['data'] = true;
	break;

	case 'finalizarAnaliseCusto':
		$T004_Id = isset($_REQUEST['T004_Id']) ? $_REQUEST['T004_Id'] : false;
		$sqlT004 = mysql_query("SELECT T004_D009_Id FROM T004 WHERE T004_Id = '{$T004_Id}'");
		$rowT004 = mysql_fetch_array($sqlT004);

		mysql_query("UPDATE T004 SET T004_Flag_Comprar = 'C' WHERE T004_Id = '{$T004_Id}'");

		require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();
        $CAD002->D001_reprocessa_historico(0, $rowT004['T004_D009_Id']);
	break;

	case 'cancelarOrdemProducao':
		$retorno = $EST017->cancelarOrdemProducao($r_T255_Id);
		if ($retorno !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'excluirOrdemProducao':
		$retorno = $EST017->excluirOrdemProducao($r_T255_Id);
		if ($retorno !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'duplicarOrdemProducao':
		$retorno = $EST017->duplicarOrdemProducao($r_T255_Id);
		if (!is_array($retorno)) {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		} else {
			$resposta['T255_Id'] = $retorno['T255_Id'];
		}
	break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
