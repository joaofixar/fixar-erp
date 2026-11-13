<?php
namespace hardness;
/**
* FIN - FIN007 - Análise de Crédito
* 
* Funções PHP para callbacks
*/

function validaCampoRetornar($array) {
	global $g;

	return "<a href=\"#\" title=\"Retornar Pedido para Vendedor\">
				<span class=\"ui-icon ui-icon-arrowreturnthick-1-e\" onClick=\"fin007ValidaCampoRetornarAlmx('{$array['todosCampos']['T005_Id']}', '{$g['divId']}')\"></span>
			</a>";
}

function validaCampoLiberar($array) {
	global $g;
	return "<a href=\"#\"><span class=\"ui-icon ui-icon-check\" title=\"Liberar\" 
								onClick=\"fin007ValidaCampoLiberar('{$array['todosCampos']['T005_Id']}', '{$g['divId']}')\">
						  </span>
		  </a>";
}

function validaCampoNaoLiberar($array) {
	global $g;
	return "<a href=\"#\"><span class=\"ui-icon ui-icon-circle-minus\" title=\"Não Liberar\" 
								onClick=\"fin007ValidaCampoNaoLiberar(event,'{$array['todosCampos']['T005_Id']}', '{$g['divId']}',  '{$g['divIdRoot']}')\">
						  </span>
		  </a>";	
}

function validaCampoAguardar($array){
	
	global $g;
	
	$retorno = "<input type='checkbox' id='T005_Flag_Aguardar_Liberacao' title='Aguardar Liberação' onChange=\"fin007CheckboxAguardar(this, '{$array['todosCampos']['T005_Id']}', '{$g['divId']}')\" ";
	
	if ($array['todosCampos']['T005_Flag_Aguardar_Liberacao']=='1'){
		$retorno .="checked='checked'";
	}
	return $retorno .= " />";
	
}

// calcula o campo Outros Pedidos do grid
function calculaOutrosPedidos($array) {
	require_once('bibliotecas/classes/FIN007.php');
	$FIN007 = new FIN007();	
	return number_format($FIN007->calculaOutrosPedidos($array),2,',','.');	
}

// calcula os campos Vencido do grid 
function calculaValorVencido($array,$param=false) {
	global $g;
	$D024_Id = $array['todosCampos']['D024_Id'];
	$dadosCliente = mysql_query("SELECT D024_Valor_Limite, D024_Cnpj_Matriz FROM D024 WHERE D024_Id = '{$D024_Id}'");
    $dadosCliente = mysql_fetch_array($dadosCliente);  
	$filtroSaldo = "D024_Id = '{$D024_Id}'";
	if(($g['C031']['somarLimiteCnpjMatriz'] == 'S') && (!empty($dadosCliente['D024_Cnpj_Matriz']))){
        $filtroSaldo = "D024_Cnpj_Matriz = '{$dadosCliente['D024_Cnpj_Matriz']}'";
    }
    $sqlSaldo    = "SELECT sum(T002_Valor_Total) as Vencido
                      FROM T002
                 LEFT JOIN D024 ON D024_Id=T002_D024_Id
                     WHERE $filtroSaldo
                       AND T002_Data_Recebimento='0000-00-00' 
                       AND T002_Data_Vencimento<curdate()
                       AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado is null)";
    $resultSaldo = mysql_query($sqlSaldo);
    $linhaSaldo  = mysql_fetch_array($resultSaldo);
    return number_format($linhaSaldo['Vencido'],2,',','.');
}
	
// calcula o campo a Vencer do grid 
function calculaValorVencer($array) {
	global $g;
	$D024_Id = $array['todosCampos']['D024_Id'];
	$dadosCliente = mysql_query("SELECT D024_Valor_Limite, D024_Cnpj_Matriz FROM D024 WHERE D024_Id = '{$D024_Id}'");
    $dadosCliente = mysql_fetch_array($dadosCliente);    
	$filtroSaldo = "D024_Id = '{$D024_Id}'";
	if(($g['C031']['somarLimiteCnpjMatriz'] == 'S') && (!empty($dadosCliente['D024_Cnpj_Matriz']))){
        $filtroSaldo = "D024_Cnpj_Matriz = '{$dadosCliente['D024_Cnpj_Matriz']}'";
    }
    $sqlSaldo    = "SELECT sum(T002_Valor_Total) as Vencer
                      FROM T002
                 LEFT JOIN D024 ON D024_Id=T002_D024_Id
                     WHERE $filtroSaldo
                       AND T002_Data_Recebimento='0000-00-00'
                       AND T002_Data_Vencimento>=curdate() 
                       AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado is null)";
    $resultSaldo = mysql_query($sqlSaldo);
    $linhaSaldo  = mysql_fetch_array($resultSaldo);
    return number_format($linhaSaldo['Vencer'],2,',','.');
}

// calcula o campo Valor Saldo Limite do grid
function calculaSaldoLimite($array) {
	global $g;
	require_once('bibliotecas/classes/FIN007.php');
	$FIN007 = new FIN007();		
	$valorLimite=$array['todosCampos']['D024_Valor_Limite'];
	$vencido=$array['todosCampos']['sum(if(T002_Data_Vencimento<curdate(),T002_Valor_Saldo,0)) as Vencido'];
	$vencer=$array['todosCampos']['sum(if(T002_Data_Vencimento>=curdate(),T002_Valor_Saldo,0)) as Vencer'];
	$valorTotal=$array['todosCampos']['T005_Valor_Total'];
	$outros=$FIN007->calculaOutrosPedidos($array);

	$D024_Id = $array['todosCampos']['D024_Id'];

	$dadosCliente = mysql_query("SELECT D024_Valor_Limite, D024_Cnpj_Matriz FROM D024 WHERE D024_Id = '{$D024_Id}'");
	$dadosCliente = mysql_fetch_array($dadosCliente);
	$filtroSaldo = "D024_Id = '{$D024_Id}'";

	//calcula a soma dos limites das empresas vinculadas a um Cnpj Matriz
	if(($g['C031']['somarLimiteCnpjMatriz'] == 'S') && (!empty($dadosCliente['D024_Cnpj_Matriz']))){
		$filtroSaldo = "D024_Cnpj_Matriz = '{$dadosCliente['D024_Cnpj_Matriz']}'";
	}
	$dadosLimite = mysql_query("SELECT SUM(D024_Valor_Limite) AS D024_Valor_Limite, 
									   SUM(D024_Valor_Credito_Antecipado) AS D024_Valor_Credito_Antecipado, 
									   SUM(D024_Valor_Credito_Troca) AS D024_Valor_Credito_Troca 
									  FROM D024 
									 WHERE $filtroSaldo");
	$dadosLimite = mysql_fetch_array($dadosLimite);
	$dadosCliente['D024_Valor_Limite'] = $dadosLimite['D024_Valor_Limite'];

	$valorLimite = $dadosCliente['D024_Valor_Limite'];

	//calcula os campos Vencido do grid 
	$sqlSaldo    = "SELECT sum(T002_Valor_Total) as Vencido
                      FROM T002
                 LEFT JOIN D024 ON D024_Id=T002_D024_Id
                     WHERE $filtroSaldo
                       AND T002_Data_Recebimento='0000-00-00' 
                       AND T002_Data_Vencimento<curdate()
                       AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado is null)";
    $resultSaldo = mysql_query($sqlSaldo);
    $linhaSaldo  = mysql_fetch_array($resultSaldo);
    $vencido = $linhaSaldo['Vencido'];

    //calcula o campo a Vencer do grid 
    $sqlSaldo    = "SELECT sum(T002_Valor_Total) as Vencer
                      FROM T002
                 LEFT JOIN D024 ON D024_Id=T002_D024_Id
                     WHERE $filtroSaldo
                       AND T002_Data_Recebimento='0000-00-00'
                       AND T002_Data_Vencimento>=curdate() 
                       AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado is null)";
	$resultSaldo = mysql_query($sqlSaldo);
	$linhaSaldo  = mysql_fetch_array($resultSaldo);
	$vencer = $linhaSaldo['Vencer'];

	$retorno=($valorLimite-($vencido+$vencer+$outros+$valorTotal)); 
	if ($retorno<0){
		$retorno = '<span style="color: var(--BGVermelho);">'.number_format($retorno,2,',','.').'</span>';
		return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Demonstrativo Saldo Limite', '/fin/fin007/content/fin007ContentSaldoLimite/', '{$extra}&acaoId=' + encodeURIComponent('{$array['todosCampos']['D024_Id']}'), [700,400]); return false;\">{$retorno}</a>"; 
	} else{ 
		$retorno = number_format($retorno,2,',','.');
		return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Demonstrativo Saldo Limite', '/fin/fin007/content/fin007ContentSaldoLimite/', '{$extra}&acaoId=' + encodeURIComponent('{$array['todosCampos']['D024_Id']}'), [700,400]); return false;\">{$retorno}</a>"; 
	}
}


// calcula o campo Forma Pagamento do grid
function formaPagamento($array) {
	require_once('bibliotecas/classes/FIN007.php');
	$FIN007 = new FIN007();	
	$retorno = $FIN007->formaPagamento($array);	
	$ret='';
	for($i=0;$i<sizeof($retorno);$i++){
		$ret.=gCorrigeData($retorno[$i]['vencimento']).' - '.number_format($retorno[$i]['valorParcela'],2,',','.').' - '.$retorno[$i]['portador'].'<br />';
	}	
	return $ret;
}

// calcula o campo Forma Pagamento do grid
function formaPagamentoNota($array) {
	require_once('bibliotecas/classes/FIN007.php');
	$FIN007 = new FIN007();	
	$retorno = $FIN007->formaPagamentoNota($array);	
	$ret='';
	for($i=0;$i<sizeof($retorno);$i++){
		$ret.=gCorrigeData($retorno[$i]['vencimento']).' - '.number_format($retorno[$i]['valorParcela'],2,',','.').' - '.$retorno[$i]['portador'].'<br />';
	}	
	return $ret;
}

function validaCampoLiberarNota($array) {
	global $g;
	return "<a href=\"#\"><span class=\"ui-icon ui-icon-check\" title=\"Liberar\" 
								onClick=\"fin007ValidaCampoLiberarNota('{$array['todosCampos']['T007_Id']}', '{$g['divId']}')\">
						  </span>
		  </a>";
}

function validaCampoNaoLiberarNota($array) {
	global $g;
	return "<a href=\"#\"><span class=\"ui-icon ui-icon-circle-minus\" title=\"Não Liberar\" 
								onClick=\"fin007ValidaCampoNaoLiberarNota('{$array['todosCampos']['T007_Id']}', '{$g['divId']}')\">
						  </span>
		  </a>";	
}

// calcula o campo Valor Limite do grid
function calculaValorLimite($array) {
	global $g;
	$D024_Id = $array['todosCampos']['D024_Id'];
	$dadosCliente = mysql_query("SELECT D024_Valor_Limite, D024_Cnpj_Matriz FROM D024 WHERE D024_Id = '{$D024_Id}'");
	$dadosCliente = mysql_fetch_array($dadosCliente);

	$filtroSaldo = "D024_Id = '{$D024_Id}'";

	//calcula a soma dos limites das empresas vinculadas a um Cnpj Matriz
	if(($g['C031']['somarLimiteCnpjMatriz'] == 'S') && (!empty($dadosCliente['D024_Cnpj_Matriz']))){
		$filtroSaldo = "D024_Cnpj_Matriz = '{$dadosCliente['D024_Cnpj_Matriz']}'";
		$dadosLimite = mysql_query("SELECT SUM(D024_Valor_Limite) AS D024_Valor_Limite, 
										SUM(D024_Valor_Credito_Antecipado) AS D024_Valor_Credito_Antecipado, 
										SUM(D024_Valor_Credito_Troca) AS D024_Valor_Credito_Troca 
										FROM D024 
										WHERE $filtroSaldo");
		$dadosLimite = mysql_fetch_array($dadosLimite);
		$dadosCliente['D024_Valor_Limite'] = $dadosLimite['D024_Valor_Limite'];
	}

	$dadosCliente = number_format($dadosCliente['D024_Valor_Limite'],2,',','.');
    return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Demonstrativo Valor Limite', '/fin/fin007/content/fin007ContentValorLimite/', '{$extra}&acaoId=' + encodeURIComponent('{$array['todosCampos']['D024_Id']}'), [700,400]); return false;\">{$dadosCliente}</a>";         
}

function calculaPedidos($array){    
    global $g;
    $D024_Id = $array['todosCampos']['D024_Id'];
    $T005_Id = $array['todosCampos']['T005_Id'];
    $dadosCliente = mysql_query("SELECT D024_Valor_Limite, D024_Cnpj_Matriz FROM D024 WHERE D024_Id = '{$D024_Id}'");
    $dadosCliente = mysql_fetch_array($dadosCliente);    
    $filtroSaldo = "D024_Id = '{$D024_Id}'";
    if(($g['C031']['somarLimiteCnpjMatriz'] == 'S') && (!empty($dadosCliente['D024_Cnpj_Matriz']))){
        $filtroSaldo = "D024_Id = '{$D024_Id}'";
    }
    $sql     = mysql_query("SELECT sum(T005_Valor_Total)
                              FROM T005
                         LEFT JOIN T007 ON T007_T005_Id=T005_Id
                         LEFT JOIN D024 ON D024_Id=T005_D024_Id
                             WHERE $filtroSaldo
                               AND T005_Id!='$T005_Id'
                               AND T005_Flag_Status!=8
                               AND (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null)
                               AND T007_Id is null");
    $retorno = mysql_fetch_array($sql);
    return number_format($retorno[0],2,',','.');
}

// calcula o campo Valor Vencido do Demosntrativo Saldo Limite
function ValorVencido($array,$param=false) {
	global $g;
	$D024_Id = $array['todosCampos']['D024_Id'];
	$dadosCliente = mysql_query("SELECT D024_Valor_Limite, D024_Cnpj_Matriz FROM D024 WHERE D024_Id = '{$D024_Id}'");
    $dadosCliente = mysql_fetch_array($dadosCliente);  
	$filtroSaldo = "D024_Id = '{$D024_Id}'";
    $sqlSaldo    = "SELECT sum(T002_Valor_Total) as Vencido
                      FROM T002
                 LEFT JOIN D024 ON D024_Id=T002_D024_Id
                     WHERE $filtroSaldo
                       AND T002_Data_Recebimento='0000-00-00' 
                       AND T002_Data_Vencimento<curdate()
                       AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado is null)";
    $resultSaldo = mysql_query($sqlSaldo);
    $linhaSaldo  = mysql_fetch_array($resultSaldo);
    return number_format($linhaSaldo['Vencido'],2,',','.');
}
	
// calcula o campo Valor a Vencer do Demosntrativo Saldo Limite
function ValorVencer($array) {
	global $g;
	$D024_Id = $array['todosCampos']['D024_Id'];
	$dadosCliente = mysql_query("SELECT D024_Valor_Limite, D024_Cnpj_Matriz FROM D024 WHERE D024_Id = '{$D024_Id}'");
    $dadosCliente = mysql_fetch_array($dadosCliente);    
	$filtroSaldo = "D024_Id = '{$D024_Id}'";
    $sqlSaldo    = "SELECT sum(T002_Valor_Total) as Vencer
                      FROM T002
                 LEFT JOIN D024 ON D024_Id=T002_D024_Id
                     WHERE $filtroSaldo
                       AND T002_Data_Recebimento='0000-00-00'
                       AND T002_Data_Vencimento>=curdate() 
                       AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado is null)";
    $resultSaldo = mysql_query($sqlSaldo);
    $linhaSaldo  = mysql_fetch_array($resultSaldo);
    return number_format($linhaSaldo['Vencer'],2,',','.');
}

// calcula o campo Valor Saldo Limite do grid
function SaldoLimite($array) {
	global $g;
	require_once('bibliotecas/classes/FIN007.php');
	$FIN007 = new FIN007();		
	$valorLimite=$array['todosCampos']['D024_Valor_Limite'];
	$vencido=$array['todosCampos']['sum(if(T002_Data_Vencimento<curdate(),T002_Valor_Saldo,0)) as Vencido'];
	$vencer=$array['todosCampos']['sum(if(T002_Data_Vencimento>=curdate(),T002_Valor_Saldo,0)) as Vencer'];

	//calcula pedidos
    $D024_Id = $array['todosCampos']['D024_Id'];
    $T005_Id = $array['todosCampos']['T005_Id'];
    $filtroSaldo = "D024_Id = '{$D024_Id}'";
    $sql     = mysql_query("SELECT sum(T005_Valor_Total) as Pedidos
                              FROM T005
                         LEFT JOIN T007 ON T007_T005_Id=T005_Id
                         LEFT JOIN D024 ON D024_Id=T005_D024_Id
                             WHERE $filtroSaldo
                               AND T005_Id!='$T005_Id'
                               AND T005_Flag_Status!=8
                               AND (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null)
                               AND T007_Id is null");
    $retorno = mysql_fetch_array($sql);
    $pedidos = $retorno['Pedidos'];

	//calcula os campos Vencido do grid 
	$sqlVencido    = "SELECT sum(T002_Valor_Total) as Vencido
                      FROM T002
                 LEFT JOIN D024 ON D024_Id=T002_D024_Id
                     WHERE D024_Id = '{$D024_Id}'
                       AND T002_Data_Recebimento='0000-00-00' 
                       AND T002_Data_Vencimento<curdate()
                       AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado is null)";
    $resultVencido = mysql_query($sqlVencido);
    $linhaSaldo  = mysql_fetch_array($resultVencido);
    $vencido = $linhaSaldo['Vencido'];

    //calcula o campo a Vencer do grid 
    $sqlVencer    = "SELECT sum(T002_Valor_Total) as Vencer
                      FROM T002
                 LEFT JOIN D024 ON D024_Id=T002_D024_Id
                     WHERE D024_Id = '{$D024_Id}'
                       AND T002_Data_Recebimento='0000-00-00'
                       AND T002_Data_Vencimento>=curdate() 
                       AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado is null)";
	$resultVencer = mysql_query($sqlVencer);
	$linhaSaldo  = mysql_fetch_array($resultVencer);
	$vencer = $linhaSaldo['Vencer'];

	$saldoAtual=($valorLimite-($vencido+$vencer+$pedidos)); 
	if ($saldoAtual<0)
		return '<span style="color: var(--BGVermelho);">'.number_format($saldoAtual,2,',','.').'</span>';
	else 
		return number_format($saldoAtual,2,',','.').'</span>';
	
}

