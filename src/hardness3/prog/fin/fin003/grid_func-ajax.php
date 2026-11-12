<?php
namespace hardness;
/**
 * FIN - FIN001 - Contas a Receber
 *
 * Processamento de ações AJAX
 */

// Classes
require_once('bibliotecas/classes/FIN003.php');
$FIN003 = new FIN003();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	
	case 'carregaInputsTotais':
	
				
		$T019_Id = isset($_REQUEST['T019_Id']) ? $_REQUEST['T019_Id'] : false;
		
		$retorno = $FIN003->listaTotais($T019_Id);
		//print_r($retorno);
		$resposta['lancamento'] = number_format($retorno['Recebimentos'],2,',','.');
		$resposta['recebimento'] = number_format($retorno['Pagamentos'],2,',','.');
		$resposta['pagamento'] = number_format($retorno['Lancamentos'],2,',','.');
		$resposta['resultado']=number_format($retorno['Recebimentos'] + $retorno['Pagamentos'],2,',','.');
				
	break;
	
	case 'atualizarSaldos':
		$D007_Id = isset($r_D007_Id) ? $r_D007_Id : false;
		
		$T019=mysql_query("select T019_Id,T019_Valor_Lancamento,T019_Saldo,T019_Data_Lancamento,T019_Flag_Excluido from T019 where T019_D007_Id='$D007_Id' order by T019_Data_Lancamento,T019_Id");

		$Saldo=0;

		while ($mT019=mysql_fetch_array($T019)) {
	       if ($mT019['T019_Flag_Excluido']=='S')
	       {
	          mysqli_query("update T019 set T019_Saldo='0' where T019_Id='{$mT019['T019_Id']}'");
	          continue;
	       }
	       $Saldo+=$mT019['T019_Valor_Lancamento'];
	       if (number_format($Saldo,2)!=number_format($mT019['T019_Saldo'],2)) {
	           mysqli_query("update T019 set T019_Saldo='$Saldo' where T019_Id='{$mT019['T019_Id']}'");
	       }
	       //echo $mT019['T019_Data_Lancamento'].' - '.$mT019['T019_Valor_Lancamento'].' - '.$mT019['T019_Saldo'].'<br />';
		}
		$sql="update D007 set D007_Valor_Saldo='$Saldo' where D007_Id='$D007_Id'";
		$request=mysql_query($sql);
		
		if ($request) {
			$resposta['code'] = true;
			$resposta['data'] = "Saldo atualizado!";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = "Erro SQL: $sql";
		}
			
	break;

	case 'marcarRegistroExtrato':
		$T216_Id = isset($r_T216_Id) ? $r_T216_Id : false;
		$check = isset($r_selected) ? $r_selected : false;
		mysql_query("UPDATE T216 SET T216_Flag_Importar = '{$check}' WHERE T216_Id = '{$T216_Id}'");
		$resposta['code'] = true;
	break;

	case 'duplicarTransacao':
		$T216_Id = isset($r_T216_Id) ? $r_T216_Id : false;
		mysql_query(gRetornaSqlDuplicacao('T216', $T216_Id));
		if($T216_Id !== false){
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
		}
	break;

	case 'campoMarcarDesmembramento':
		$T015_Id = isset($r_T015_Id) ? $r_T015_Id : false;
		$check = isset($r_selected) ? $r_selected : false;
		mysql_query("UPDATE T015 SET T015_Flag_Marcar_Lancamento = '{$check}' WHERE T015_Id = '{$T015_Id}'");
		$resposta['code'] = true;
	break;

	case 'campoMarcarDesmembramentoRecebida':
		$T002_Id = isset($r_T002_Id) ? $r_T002_Id : false;
		$check = isset($r_selected) ? $r_selected : false;
		mysql_query("UPDATE T002 SET T002_Flag_Marcar_Lancamento = '{$check}' WHERE T002_Id = '{$T002_Id}'");
		$resposta['code'] = true;
	break;

	case 'desmembrarTransacao':
		require_once("bibliotecas/classes/FIN003.php");
		$extra = gInsertExtraWhere($r_extra, "(T015_Flag_Marcar_Lancamento = 'S')");
		$extra = explode("FROM T015", $extra);
		$extra = $extra[1];
		$T216_Id = isset($r_T216_Id) ? $r_T216_Id : false;
		$FIN003 = new FIN003();
		$retorno = $FIN003->desmembrarTransacao($T216_Id, $extra);
		if($retorno === true){
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'desmembrarTransacaoRecebida':
		require_once("bibliotecas/classes/FIN003.php");
		$extra = gInsertExtraWhere($r_extra, "(T002_Flag_Marcar_Lancamento = 'S')");
		$extra = explode("FROM T002", $extra);
		$extra = $extra[1];
		$T216_Id = isset($r_T216_Id) ? $r_T216_Id : false;
		$FIN003 = new FIN003();
		$retorno = $FIN003->desmembrarTransacaoRecebida($T216_Id, $extra);
		if($retorno === true){
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'atualizarPortador':
		$T216_Id = isset($r_T216_Id) ? $r_T216_Id : false;
		$tabela = isset($r_tabela) ? $r_tabela : false;
		$idPortador = isset($r_idPortador) ? $r_idPortador : false;

		if($tabela == 'D031'){
			$campoA = 'T216_D031_Id';
			$campoB = 'T216_D027_Id';
		} else {
			$campoA = 'T216_D027_Id';
			$campoB = 'T216_D031_Id';
		}

		mysql_query("UPDATE T216 SET {$campoA} = '{$idPortador}', {$campoB} = 0 WHERE T216_Id = '{$T216_Id}'");
		$error = mysql_error();
		if(!empty($error)){
			$resposta['code'] = false;
			$resposta['data'] = "Erro: " . $error;
		} else {
			$resposta['code'] = true;
		}
	break;

	case 'atualizarPortadorRegras':
		$T217_Id = isset($r_T217_Id) ? $r_T217_Id : false;
		$tabela = isset($r_tabela) ? $r_tabela : false;
		$idPortador = isset($r_idPortador) ? $r_idPortador : false;

		if($tabela == 'D031'){
			$campoA = 'T217_D031_Id';
			$campoB = 'T217_D027_Id';
		} else {
			$campoA = 'T217_D027_Id';
			$campoB = 'T217_D031_Id';
		}
		
		mysql_query("UPDATE T217 SET {$campoA} = '{$idPortador}', {$campoB} = 0 WHERE T217_Id = '{$T217_Id}'");
		$error = mysql_error();
		if(!empty($error)){
			$resposta['code'] = false;
			$resposta['data'] = "Erro: " . $error;
		} else {
			$resposta['code'] = true;
		}
	break;

	case 'atualizarCampoConciliado':
		$resposta = array('code' => true, 'data' => array());

		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T019_Id = isset($_REQUEST['T019_Id']) ? $_REQUEST['T019_Id'] : false;
		$sql = "UPDATE T019 SET T019_Conciliado = '{$selected}' WHERE T019_Id = '{$T019_Id}'";
		mysql_query($sql);

		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'removerVinculoTransacao':
		$T216_Id = isset($r_T216_Id) ? $r_T216_Id : false;
		mysql_query("UPDATE T216 SET T216_T002_Id = 0, T216_T015_Id = 0 WHERE T216_Id = '{$T216_Id}'");
		$erro = mysql_error();
		if(empty($erro)){
			$resposta['code'] = true;
			$resposta['data'] = 'Título desvinculado com sucesso!';
		} else {
			$resposta['code'] = false;
			$resposta['data'] = 'Erro ao desvincular título, por favor entre em contato com o suporte técnico.';
		}
	break;

	case 'desvincularLancamentoTransacao':
		$T216_Id = isset($r_T216_Id) ? $r_T216_Id : false;
		mysql_query("UPDATE T216 SET T216_T019_Id = 0 WHERE T216_Id = '{$T216_Id}'");
		$erro = mysql_error();
		if(empty($erro)){
			$resposta['code'] = true;
			$resposta['data'] = 'Transação desvinculada com sucesso!';
		} else {
			$resposta['code'] = false;
			$resposta['data'] = 'Erro ao desvincular a transação, por favor entre em contato com o suporte técnico.';
		}
	break;

	case 'excluirTransacaoExtrato':
		$T216_Id = isset($r_T216_Id) ? $r_T216_Id : false;
		$T216_Flag_Excluido = ($r_T216_Flag_Excluido == 'N') ? 'S' : 'N';
		$operacao = ($T216_Flag_Excluido == 'S') ? 'excluída' : 'recuperada';
		mysql_query("UPDATE T216 SET T216_Flag_Excluido = '{$T216_Flag_Excluido}' WHERE T216_Id = '{$T216_Id}'");
		$erro = mysql_error();
		if(empty($erro)){
			$resposta['code'] = true;
			$resposta['data'] = "Transação {$operacao} com sucesso!";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = "A transação não pode ser {$operacao}, por favor entre em contato com o suporte técnico.";
		}
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>