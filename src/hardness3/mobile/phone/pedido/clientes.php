<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

require_once("bibliotecas/classes/VEN001.php");
$VEN001 = new VEN001();

$script = "";
if (isset($_REQUEST['D024_Id'])) {
	$D024_Id = mysql_real_escape_string($_REQUEST['D024_Id']);
	if (isset($_REQUEST['remover'])) {
		$sql = "UPDATE T005 SET T005_D024_Id = '' WHERE T005_Id = '{$T005_Id}';";
		mysql_query($sql);
	} else {

        // BUSCA A CFOP PADRAO PARA O ORCAMENTO
		$sql = "SELECT D024_D006_Id, 
				       D024_D018_Id,
				       D024_Flag_Revenda_Consumidor,
				       D024_Flag_Contribuinte
				  FROM D024 WHERE D024_Id=" . $D024_Id;
		$result = mysql_query($sql);
		$aD024 = mysql_fetch_assoc($result);
		
		echo mysql_error();
		
		$sql = "SELECT C004_CFOP_Venda_Dentro_Estado, 
				       C004_CFOP_Venda_Fora_Estado, 
				       C004_CFOP_ST_Venda_Fora_Estado, 
				       C004_CFOP_ST_Venda_Dentro_Estado, 
				       C004_CFOP_CONSUMO_Venda_Fora_Estado, 
				       C004_CFOP_CONSUMO_Venda_Dentro_Estado, 
				       C004_D018_Id 
				  FROM C004 WHERE C004_Id=" . $g['c004']['C004_Id'];
		$result = mysql_query($sql);
		$aC004 = mysql_fetch_assoc($result);
		
		echo mysql_error();
		
		$D006_Id = $aD024['D024_D006_Id'];
		
        if ($D006_Id<=0)
        {
			if ($aD024['D024_D018_Id'] != $aC004['C004_D018_Id'])
			{
			    $D006_Id = $aC004['C004_CFOP_Venda_Fora_Estado'];
			}
			else
			{       
			    $D006_Id = $aC004['C004_CFOP_Venda_Dentro_Estado'];
			}	
			if ($aD024['D024_Flag_Contribuinte'] == '1')
			{
				if ($aD024['D024_D018_Id'] != $aC004['C004_D018_Id'])
				{
				    $D006_Id = $aC004['C004_CFOP_CONSUMO_Venda_Fora_Estado'];
				}
				else
				{
				    $D006_Id = $aC004['C004_CFOP_CONSUMO_Venda_Dentro_Estado'];
				}	
			}
		}
		// FIM BUSCA CFOP

		$T005_Flag_Destino_Produto = $T005['T005_Flag_Destino_Produto'];
		if(empty($T005['T005_Flag_Destino_Produto']) && $g['C031']['definirDestinoVendaTipoCliente'] == 'S'){
			switch ($aD024['D024_Flag_Contribuinte']) {
				case '5':
					$T005_Flag_Destino_Produto = '1';
					break;
				case '2':
					$T005_Flag_Destino_Produto = '2';
					break;
				case '1':
					$T005_Flag_Destino_Produto = '3';
					break;
			}
		}

		// GRAVA CLIENTE E CFOP NO ORCAMENTO
		$sql = "UPDATE T005 SET T005_D006_Id = '{$D006_Id}', T005_D024_Id = '{$D024_Id}', T005_Flag_Destino_Produto = '{$T005_Flag_Destino_Produto}' WHERE T005_Id = '{$T005_Id}';";
		mysql_query($sql);

		// GRAVA CFOP NOS ITENS DO ORCAMENTO
		$sql = "UPDATE T006 SET T006_D006_Id = '{$D006_Id}' WHERE T006_T005_Id = '{$T005_Id}';";
		mysql_query($sql);
		
	}
}

if (isset($_REQUEST['D116_Id'])) {
	$D116_Id = mysql_real_escape_string($_REQUEST['D116_Id']);
	$sql = "UPDATE T005 SET T005_D116_Id = '{$D116_Id}' WHERE T005_Id = '{$T005_Id}';";
	mysql_query($sql);
}
if (isset($_REQUEST['D013_Id'])) {
	$D013_Id = mysql_real_escape_string($_REQUEST['D013_Id']);
	$sql = "UPDATE T005 SET T005_D013_Id = '{$D013_Id}' WHERE T005_Id = '{$T005_Id}';";
	mysql_query($sql);
}

if (isset($_REQUEST['T005_Flag_Destino_Produto'])) {
	$T005_Flag_Destino_Produto = mysql_real_escape_string($_REQUEST['T005_Flag_Destino_Produto']);
	$sql = "UPDATE T005 SET T005_Flag_Destino_Produto = '{$T005_Flag_Destino_Produto}' WHERE T005_Id = '{$T005_Id}';";
	mysql_query($sql);
}

if (isset($_REQUEST['T005_Observacao'])) {
	$_REQUEST['T005_Observacao'] = mysql_real_escape_string($_REQUEST['T005_Observacao']);
	$_REQUEST['T005_Hora_Inicio_Servico'] = mysql_real_escape_string($_REQUEST['T005_Hora_Inicio_Servico']);
	$_REQUEST['T005_Hora_Fim_Servico'] = mysql_real_escape_string($_REQUEST['T005_Hora_Fim_Servico']);
	$sql = "UPDATE T005 SET T005_Observacao = '{$_REQUEST['T005_Observacao']}',
					T005_Hora_Inicio_Servico = '{$_REQUEST['T005_Hora_Inicio_Servico']}',
					T005_Hora_Fim_Servico = '{$_REQUEST['T005_Hora_Fim_Servico']}',
					T005_Flag_Aguardando_Entrega = '{$_REQUEST['T005_Flag_Aguardando_Entrega']}'
					WHERE T005_Id = '{$T005_Id}'";
	mysql_query($sql);
	$uniqId=uniqid();
	$script = "alert('Dados salvos com sucesso!'); ";
	$script .= <<<EOT
	$.mobile.changePage( './clientes.php?T005_Id={$T005_Id}&refresh={$uniqId}', { transition: 'fade'} );
EOT;
}

if(isset($_REQUEST['T005_Data_Entrega']) || isset($_REQUEST['T005_D022_Id']) || isset($_REQUEST['T005_T208_Id'])){
	$T005_Data_Entrega = gCorrigeDataInverte(mysql_real_escape_string($_REQUEST['T005_Data_Entrega']));
	$T005_D022_Id = mysql_real_escape_string($_REQUEST['T005_D022_Id']);
	$T005_T208_Id = mysql_real_escape_string($_REQUEST['T005_T208_Id']);

	$sql = "UPDATE T005 SET 
					T005_Data_Entrega = '{$T005_Data_Entrega}',
					T005_D022_Id = '{$T005_D022_Id}',
					T005_T208_Id = '{$T005_T208_Id}'
					WHERE T005_Id = '{$T005_Id}'";
	mysql_query($sql);

	mysql_query("UPDATE T006 SET T006_D022_Id = '{$T005_D022_Id}', T006_T208_Id = '{$T005_T208_Id}' WHERE T006_T005_Id = '{$T005_Id}'");
	$uniqId=uniqid();
	$script = "alert('Dados salvos com sucesso!'); ";
}
//Select campo "Cliente aguardando Entrega?"
if($T005['T005_Flag_Aguardando_Entrega'] == 'S'){
	$Options = <<<EOT
		<option value='S' selected='selected'>Sim</option>
		<option value='N'>Não</option>
EOT;
}else{
	$Options = <<<EOT
		<option value='S'>Sim</option>
		<option value='N' selected='selected'>Não</option>
EOT;
	
}

include('inc.conf.php');

include('mobile/phone/inc.header.php');

echo <<<EOT
	<div data-role="page" id="pageClientes">
EOT;
$etapa = 3;
include('mobile/phone/inc.header.bar.php');
include('inc.header.page.php');

//Restrição alterar pedido
$restricao = ($g['conf']->restricaoTravar('alterar', 'T005_Id', $T005_Id) === true && isset($g['c029Ids'][213])) ? '' : 'disabled="disabled"';

$selected_T005_Flag_Destino_Produto_ = ($T005['T005_Flag_Destino_Produto'] == '') ? 'selected="selected"' : true;
$selected_T005_Flag_Destino_Produto_1 = ($T005['T005_Flag_Destino_Produto'] == '1') ? 'selected="selected"' : '';
$selected_T005_Flag_Destino_Produto_2 = ($T005['T005_Flag_Destino_Produto'] == '2') ? 'selected="selected"' : '';
$selected_T005_Flag_Destino_Produto_3 = ($T005['T005_Flag_Destino_Produto'] == '3') ? 'selected="selected"' : '';
$selected_T005_Flag_Destino_Produto_4 = ($T005['T005_Flag_Destino_Produto'] == '4') ? 'selected="selected"' : '';
$selected_T005_Flag_Destino_Produto_5 = ($T005['T005_Flag_Destino_Produto'] == '5') ? 'selected="selected"' : '';
$selected_T005_Flag_Destino_Produto_6 = ($T005['T005_Flag_Destino_Produto'] == '6') ? 'selected="selected"' : '';
$selected_T005_Flag_Destino_Produto_7 = ($T005['T005_Flag_Destino_Produto'] == '7') ? 'selected="selected"' : '';
$selected_T005_Flag_Destino_Produto_8 = ($T005['T005_Flag_Destino_Produto'] == '8') ? 'selected="selected"' : '';
$selected_T005_Flag_Destino_Produto_9 = ($T005['T005_Flag_Destino_Produto'] == '9') ? 'selected="selected"' : '';
$selected_T005_Flag_Destino_Produto_10 = ($T005['T005_Flag_Destino_Produto'] == '10') ? 'selected="selected"' : '';
$selected_T005_Flag_Destino_Produto_11 = ($T005['T005_Flag_Destino_Produto'] == '11') ? 'selected="selected"' : '';
$selected_T005_Flag_Destino_Produto_12 = ($T005['T005_Flag_Destino_Produto'] == '12') ? 'selected="selected"' : '';
$selected_T005_Flag_Destino_Produto_13 = ($T005['T005_Flag_Destino_Produto'] == '13') ? 'selected="selected"' : '';
$selected_T005_Flag_Destino_Produto_14 = ($T005['T005_Flag_Destino_Produto'] == '14') ? 'selected="selected"' : '';
$selected_T005_Flag_Destino_Produto_15 = ($T005['T005_Flag_Destino_Produto'] == '15') ? 'selected="selected"' : '';
$selected_T005_Flag_Destino_Produto_16 = ($T005['T005_Flag_Destino_Produto'] == '16') ? 'selected="selected"' : '';
$selected_T005_Flag_Destino_Produto_17 = ($T005['T005_Flag_Destino_Produto'] == '17') ? 'selected="selected"' : '';
$selected_T005_Flag_Destino_Produto_18 = ($T005['T005_Flag_Destino_Produto'] == '18') ? 'selected="selected"' : '';
$selected_T005_Flag_Destino_Produto_19 = ($T005['T005_Flag_Destino_Produto'] == '19') ? 'selected="selected"' : '';
$selected_T005_Flag_Destino_Produto_20 = ($T005['T005_Flag_Destino_Produto'] == '20') ? 'selected="selected"' : '';

$sqlObs = "SELECT T005_T208_Id,T005_D022_Id,T005_Data_Entrega,D024_Id, D024_Observacao_Comercial, D024_Observacao_Financeiro, D024_Valor_Limite, D024_Valor_Credito_Antecipado, D024_Valor_Credito_Troca, D024_Prazo_Parcela, D024_Dias_Vencimento, D024_Instrucao_Pagamento,
				  D024_Quantidade_Parcelas, D027_Portador, T005_Prazos(T005_Id) as Prazo, Liberou.C007_Primeiro_Nome AS Liberou, LiberouOrcamento.C007_Primeiro_Nome AS LiberouOrcamento
             FROM T005
        LEFT JOIN D024 ON D024_Id = T005_D024_Id
        LEFT JOIN T006 ON T006_T005_Id = T005_Id
        LEFT JOIN D009 ON D009_Id = T006_D009_Id
        LEFT JOIN D049 ON D049_Id = D009_D049_Id
        LEFT JOIN D001 ON D001_Id = D049_D001_Id
        LEFT JOIN T003 ON T003_Id = T005_T003_Id
        LEFT JOIN D027 ON D027_Id = D024_D027_Id
        LEFT JOIN C007 AS Liberou ON Liberou.C007_Id=T005_Liberou_Alcada_C007_Id
        LEFT JOIN C007 AS LiberouOrcamento ON LiberouOrcamento.C007_Id=T003_Liberou_Alcada_C007_Id
            WHERE T005_Id='{$T005_Id}'";
$sqlObs = mysql_query($sqlObs);
$rowObs= mysql_fetch_array($sqlObs);
$rowObs['T005_Data_Entrega'] = gCorrigeData($rowObs['T005_Data_Entrega']);

$obsComercial    = substr($rowObs['D024_Observacao_Comercial'], 0, 140);
$obsFinanceiro   = substr($rowObs['D024_Observacao_Financeiro'], 0, 180);
$valorLimite     = gCorrigeNumero($rowObs['D024_Valor_Limite']);

//Calcula saldo do cliente
$sqlSaldo = "SELECT ifnull(sum(if(T002_Data_Vencimento<curdate(),T002_Valor_Total,0)),0) as Vencido,
                    ifnull(sum(if(T002_Data_Vencimento>=curdate(),T002_Valor_Total,0)),0) as Vencer
                FROM T002
                WHERE T002_D024_Id='{$rowObs['D024_Id']}'
                and T002_Data_Recebimento='0000-00-00' 
                and (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)";
$resultSaldo = mysql_query($sqlSaldo);
$linhaSaldo= mysql_fetch_array($resultSaldo);

$sqlPedidos   = mysql_query("SELECT ifnull(sum(T005_Valor_Total),0) as Pedidos FROM T005 LEFT JOIN T007 ON T007_T005_Id=T005_Id WHERE T005_D024_Id='{$rowObs['D024_Id']}' AND T005_Flag_Status != '8' AND T007_Id IS NULL");
$linhaPedidos = mysql_fetch_array($sqlPedidos);
$rowObs['D024_Valor_Limite'] = (!empty($rowObs['D024_Valor_Limite']) ? $rowObs['D024_Valor_Limite'] : '0.00');
$saldoAtual=gCorrigeNumero($rowObs['D024_Valor_Limite']-($linhaSaldo['Vencido']+$linhaSaldo['Vencer']+$linhaPedidos['Pedidos']));

$valorCreditoAntecipado = gCorrigeNumero($rowObs['D024_Valor_Credito_Antecipado']);
$valorCreditoTroca = gCorrigeNumero($rowObs['D024_Valor_Credito_Troca']);

$obs='';
$obs .=	empty($rowObs['D024_Prazo_Parcela']) 				? '' : " 1º Venc: ".$rowObs['D024_Prazo_Parcela']."<br />";
$obs .= empty($rowObs['D024_Dias_Vencimento']) 			? '' : " Dia Fixo/Intervalo: ".$rowObs['D024_Dias_Vencimento']."<br />";
$obs .= empty($rowObs['D024_Quantidade_Parcelas']) 		? '' : " Nº Parcelas: ".$rowObs['D024_Quantidade_Parcelas']."<br />";
$obs .= empty($rowObs['D027_Portador']) 					? '' : " Portador: ".$rowObs['D027_Portador']."<br />";

// Montando Combobox transportadora
$sql = "SELECT * FROM D022 ORDER BY D022_Nome_Empresa";
$sqlTransp = mysql_query($sql);
$selectTransp = "<option value=''>...</option>";
while($rowTransp = mysql_fetch_array($sqlTransp)){
	if($rowTransp['D022_Id'] == $rowObs['T005_D022_Id']){
		$selectTransp .= "<option selected=\"selected\" value=\"{$rowTransp['D022_Id']}\">
							 {$rowTransp['D022_Nome_Empresa']}
						    </option>";
	}else{
		$selectTransp .= "<option value=\"{$rowTransp['D022_Id']}\">
							 {$rowTransp['D022_Nome_Empresa']}
						    </option>";
	}
}
// Montando Combobox Rota
$sql = "SELECT * FROM T208 ORDER BY T208_Rota";
$sqlRota = mysql_query($sql);
$selectRota = "<option value=''>...</option>";
while($rowRota = mysql_fetch_array($sqlRota)){
	if($rowRota['T208_Id'] == $rowObs['T005_T208_Id']){
		$selectRota .= "<option selected=\"selected\" value=\"{$rowRota['T208_Id']}\">
							 {$rowRota['T208_Rota']} - {$rowRota['T208_Producao']}
						    </option>";
	}else{
		$selectRota .= "<option value=\"{$rowRota['T208_Id']}\">
							 {$rowRota['T208_Rota']} - {$rowRota['T208_Producao']}
						    </option>";
	}
}
echo <<<EOT
	<div data-role="content">
       	<div data-role="collapsible" data-collapsed="false" data-theme="c" data-content-theme="c">
			<h3>Clientes</h3>
				<div class="ui-grid-a">
EOT;
$T005 = mysql_query("SELECT * FROM T005 LEFT JOIN D024 ON D024_Id=T005_D024_Id LEFT JOIN D020 ON D020_Id=D024_D020_Id LEFT JOIN D018 ON D018_Id=D024_D018_Id WHERE T005_Id = '{$T005_Id}'");
$T005 = mysql_fetch_array($T005);
if (empty($T005['T005_D024_Id'])) {
	echo <<<EOT
					<div class="ui-block-a" style="width: 68%;">
						<input placeholder="Digite o Nome do cliente ou CPF/CNPJ" data-type="search" class="ui-input-text ui-body-c" id="procurarCliente">
					</div>
					<div class="ui-block-b" style="width: 28%; margin-left: 10px; margin-top: -2px;">
						<a data-inline="true" data-role="button" data-transition="slide" data-icon="search" data-iconpos="notext" onclick="$.mobile.changePage( './clientesProcurar.php?T005_Id={$T005_Id}&pesquisa=' + encodeURIComponent($('#procurarCliente').val()), { transition: 'slide'} );"></a>
				        <a data-inline="true" data-role="button" data-transition="fade" data-icon="plus" data-iconpos="notext" href="./clientesPreCadastro.php?T005_Id={$T005_Id}" data-ajax="false"></a>
					</div>
EOT;
} else {
	$dadosCliente = montaDadosCliente($T005);
	echo <<<EOT
					<div class="ui-block-a" style="width: 68%;">
						<input class="ui-input-text ui-body-c" disabled="disabled" style="opacity: 1; text-transform:none;" value="{$T005['D024_Nome_Fantasia']}">
					</div>
					<div class="ui-block-b" style="width: 28%; margin-left: 10px; margin-top: -2px; margin-bottom: 3px;">
EOT;
	if(isset($_REQUEST['acao'])){
		$href = ($restricao == '') ? "./../clientesEditar.php?D024_Id={$T005['T005_D024_Id']}&T005_Id={$T005_Id}&update=true{$acao}" : '';
		$opacity = ($restricao == '') ? '' : 'style="opacity: 0.5"';
		echo <<<EOT
				        <a data-inline="true" data-role="button" data-transition="fade" data-icon="gear" data-iconpos="notext" href="{$href}" data-ajax="false" {$opacity}></a>
EOT;
	} else {
		$href = ($restricao == '') ? "./clientesPreCadastro.php?T005_Id={$T005_Id}&D024_Id={$T005['T005_D024_Id']}&update=true" : '';
		$opacity = ($restricao == '') ? '' : 'style="opacity: 0.5"';
		echo <<<EOT
						<a data-inline="true" data-role="button" data-transition="fade" data-icon="gear" data-iconpos="notext" href="{$href}" data-ajax="false" {$opacity}></a>
EOT;
	}
	$href = ($restricao == '') ? "./clientes.php?T005_Id={$T005_Id}&D024_Id={$T005['T005_D024_Id']}&remover=true" : '';
	$opacity = ($restricao == '') ? '' : 'style="opacity: 0.5"';
	echo <<<EOT
						<a data-inline="true" data-role="button" data-transition="fade" data-icon="delete" data-iconpos="notext" href="{$href}" data-ajax="false" {$opacity}></a>
					</div>
					{$dadosCliente}
EOT;
}
echo <<<EOT
			</div>
		</div>
		<div data-role="collapsible" data-collapsed="true" data-theme="c" data-content-theme="c">
			<h3>Destino</h3>
			<select id="T005_Flag_Destino_Produto" name="T005_Flag_Destino_Produto" onchange="$.mobile.changePage( './clientes.php?T005_Id={$T005_Id}&T005_Flag_Destino_Produto=' + $(this).val(), { transition: 'fade'} );" {$restricao}>
				<option {$selected_T005_Flag_Destino_Produto_} value="">...</option>
				<option {$selected_T005_Flag_Destino_Produto_1} value="1">1 - INDUSTRIALIZAÇÃO</option>
				<option {$selected_T005_Flag_Destino_Produto_2} value="2">2 - REVENDA</option>
				<option {$selected_T005_Flag_Destino_Produto_3} value="3">3 - CONSUMO</option>
				<option {$selected_T005_Flag_Destino_Produto_4} value="4">4 - ESPECIAL</option>
				<option {$selected_T005_Flag_Destino_Produto_5} value="5">5 - BONIFICAÇÃO</option>
				<option {$selected_T005_Flag_Destino_Produto_6} value="6">6 - TROCA</option>
				<option {$selected_T005_Flag_Destino_Produto_7} value="7">7 - DESMONSTRAÇÃO</option>
				<option {$selected_T005_Flag_Destino_Produto_8} value="8">8 - CONSIGNAÇÃO</option>
				<option {$selected_T005_Flag_Destino_Produto_9} value="9">9 - VENDA FUTURA</option>
				<option {$selected_T005_Flag_Destino_Produto_10} value="10">10 - VENDA CONTA E ORDEM</option>
				<option {$selected_T005_Flag_Destino_Produto_11} value="11">11 - VENDA PRÓ-EMPREGO</option>
				<option {$selected_T005_Flag_Destino_Produto_12} value="12">12 - REMESSA PARA CONSERTO</option>
				<option {$selected_T005_Flag_Destino_Produto_13} value="13">13 - REMESSA PARA iNDUSTRIALIZAÇÃO</option>
				<option {$selected_T005_Flag_Destino_Produto_14} value="14">14 - OUTRAS SAÍDAS</option>
				<option {$selected_T005_Flag_Destino_Produto_15} value="15">15 - AMOSTRA</option>
				<option {$selected_T005_Flag_Destino_Produto_16} value="16">16 - DOAÇÃO</option>
				<option {$selected_T005_Flag_Destino_Produto_17} value="17">17 - VENDA DE ATIVO IMOBILIZADO</option>
				<option {$selected_T005_Flag_Destino_Produto_18} value="18">18 - TRANSFERÊNCIA ENTRE EMPRESAS</option>
				<option {$selected_T005_Flag_Destino_Produto_19} value="19">19 - REMESSA TRIANGULAR</option>
				<option {$selected_T005_Flag_Destino_Produto_20} value="20">20 - REMESSA VENDA FUTURA</option>
			</select>
		</div>
		<div data-role="collapsible" data-collapsed="true" data-theme="c" data-content-theme="c">
			<h3>Entrega</h3>
			<form action="clientes.php?T005_Id={$T005_Id}" method="POST" data-ajax="false">
				<div data-role="fieldcontain" style="padding-top: 0px;">
                    <h4>Data entrega</h4>
					<input name="T005_Data_Entrega" id="T005_Data_Entrega" value="{$rowObs['T005_Data_Entrega']}" placeholder="" type="date" data-role="datebox" data-options='{"mode": "calbox", "dateFormat": "dd/mm/YYYY"}' {$restricao} />
                    <h4>Transportadora</h4>
					<select data-native-menu="true" name="T005_D022_Id" id="T005_D022_Id" {$restricao}>
				    	{$selectTransp}
				    </select>
                    <h4>Rota</h4>
					<select data-native-menu="true" name="T005_T208_Id" id="T005_T208_Id" {$restricao}>
				    	{$selectRota}
				    </select>
				</div>
		        <input id="textinput7" data-inline="true" type="submit" data-role="button" data-transition="fade" data-icon="more" data-iconpos="left" data-ajax="false" value="Salvar" {$restricao} />
			</form>
		</div>
		<div data-role="collapsible" data-collapsed="true" data-theme="c" data-content-theme="c">
			<!--<h3>Observação e Previsão entrega veículo</h3>-->
			<h3>Observação</h3>
			<form action="clientes.php?T005_Id={$T005_Id}" method="POST" data-ajax="false">
				<div data-role="fieldcontain" style="margin-top: 0">
					<label for="T005_Observacao" style="width: 10%;">
	                    <h3>Observação</h3>
		            </label>
					<textarea name="T005_Observacao" id="T005_Observacao" {$restricao}>{$T005['T005_Observacao']}</textarea>
				</div>
		        <input id="textinput3" data-inline="true" type="submit" data-role="button" data-transition="fade" data-icon="more" data-iconpos="left" data-ajax="false" value="Salvar" {$restricao} />
			</form>
		</div>
		<div data-role="collapsible" data-collapsed="true" data-theme="c" data-content-theme="c">
			<h3>Financeiro / Comercial</h3>
			<div data-role="fieldcontain">
				 <div class="comercial" title='{$rowObs['D024_Observacao_Comercial']}'>
                    <b>Observação comercial:</b><br />
                    <span style="color: red;">{$obsComercial}</span>
                </div>
                <div class="liberado">
                    <span>Orçamento liberado por: <span style="color: red;">{$rowObs['LiberouOrcamento']}</span></span><br />
                    <span>Pedido liberado por: <span style="color: red;">{$rowObs['Liberou']}</span></span>
                </div>
                <div class="financeiro" title='{$rowObs['D024_Observacao_Financeiro']}'>
                    <b>Observação financeiro:</b><br />
                    <span style="color: red;">{$obsFinanceiro}</span>
                </div>
                <div class="limite">
                    Prazo Pagamento: <span style="color: red;">{$rowObs['Prazo']}</span><br>
                    Limite: <span style="color: red;">{$valorLimite}</span>
                    Saldo disponível: <span style="color: red;">{$saldoAtual}</span><br />
                    Crédito Antecipado: <span style="color: red;">{$valorCreditoAntecipado}</span> Crédito Troca: <span style="color: red;">{$valorCreditoTroca}</span>
                </div>
			</div>
			<div data-role="fieldcontain">
				<div style="color: red;">
					<span style="font-weight: bold; color: #333;">Informação de Pagamento</span><br>{$obs}{$rowObs['D024_Instrucao_Pagamento']}
				</div>
			</div>
		</div>
		<div data-role="collapsible" data-collapsed="true" data-theme="c" data-content-theme="c">
			<h3>Contatos</h3>
					<div class="ui-grid-solo">
EOT;
if (empty($T005['T005_D024_Id'])) {
	echo <<<EOT
						<h3>Selecione um cliente.</h3>
EOT;
} else {
	$sql = "SELECT D013_Id,D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1 FROM D013 where D013_D024_Id='{$T005['T005_D024_Id']}' ORDER BY D013_Id;";
	$res = mysql_query($sql);
	
	$tmp = 0;
	while ($row = mysql_fetch_assoc($res)) {
		$checked = ($T005['T005_D013_Id'] == $row['D013_Id']) ? ' checked="checked"' : '';
		echo <<<EOT
						<input type="radio" id="T005_D013_Id-{$row['D013_Id']}" name="T005_D013_Id-{$row['D013_Id']}" value="{$row['D013_Id']}"{$checked} onclick="$.mobile.changePage( './clientes.php?T005_Id={$T005_Id}&D013_Id={$row['D013_Id']}', { transition: 'fade'} );" {$restricao}/>
						<label for="T005_D013_Id-{$row['D013_Id']}">{$row['D013_Nome_Contato']} - ({$row['D013_DDD_Telefone_1']}){$row['D013_Telefone_1']}</label> 
EOT;
	}
	$href = ($restricao == '') ? "./clientesAddContato.php?T005_Id={$T005_Id}&D024_Id={$T005['T005_D024_Id']}" : '';
	$opacity = ($restricao == '') ? '' : 'style="opacity: 0.5"';
	echo <<<EOT
						<a data-inline="true" data-role="button" data-transition="fade" data-icon="plus" data-iconpos="left" href="{$href}" data-ajax="false" {$opacity}>
							Novo Contato
						</a>
EOT;
}
echo <<<EOT
					</div>
					<div class="ui-block-b">
					</div>
		</div>
	</div>
EOT;
include('inc.footer.php');
echo <<<EOT
	</div>
    </div>
    <script>
    	$( document ).undelegate("#pageClientes", "pageinit");
    	$( document ).delegate("#pageClientes", "pageinit", function() {
			{$script}
    	});
    </script>
EOT;
