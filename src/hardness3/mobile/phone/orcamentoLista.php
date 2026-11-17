<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');


//Parametro de Pesquisa e Coluna da Pesquisa selecionada em orçamentos
$arqFiltro = $g['pathDadosUsuario'] . 'tabletFiltro/orcamentoLista';
if (!file_exists($arqFiltro)) {
	$pesquisa = array('busca' => '', 'ordem' => 'T003_Id DESC', 'colunaBusca' => '', 'tipoOrdenacao' => '', 'coluna' => '');
	file_put_contents($arqFiltro, serialize($pesquisa));
}
$pesquisa = unserialize(file_get_contents($arqFiltro));
if (isset($_REQUEST['pesquisa'])) {
	$pesquisa['busca'] = $_REQUEST['pesquisa'];
	file_put_contents($arqFiltro, serialize($pesquisa));
}	
if (isset($_REQUEST['colunaPesquisa'])) {
	$pesquisa['colunaBusca'] = $_REQUEST['colunaPesquisa'];
	file_put_contents($arqFiltro, serialize($pesquisa));
}
if (isset($_REQUEST['coluna'])) {
	$pesquisa['tipoOrdenacao'] = $_REQUEST['tipoOrdenacao'];
	$pesquisa['coluna'] = $_REQUEST['coluna'];
    $pesquisa['ordem'] = $_REQUEST['coluna'] . isset($_REQUEST['tipoOrdenacao']) ? ' ' . $_REQUEST['coluna'] . ' ' . $_REQUEST['tipoOrdenacao'] : ' DESC';
    file_put_contents($arqFiltro, serialize($pesquisa));
}

$confOrcamentoLista = true;

include('inc.header.php');

echo <<<EOT
	<div data-role="page" id="pageOrcamentoLista">
	<style>
		.iconLeft a{
	    	padding-left: 2em !important;
		    padding-right: 1.1em !important;
		}
		.iconLeft span {
		    right: auto;
		    left: 10px;
		    transform: scaleX(-1);
		}
	</style>
EOT;
$etapa = 1;
include('inc.header.bar.php');
echo <<<EOT
		<div data-role="content">
        	<div class="ui-grid-b">
	            <div class="ui-block-b" style="width: 100%;">
	           		<form id="formPesquisa" name="formPesquisa" method="get" action="orcamentoLista.php?T003_Id={$T003_Id}" data-transition="fade">
	            		<div style="width: 73%; float:left; margin-top: 5px; margin-bottom: 5px;">
                    		<input name="pesquisa" placeholder="Procurar" data-type="search" class="ui-input-text ui-body-c" value="{$pesquisa['busca']}" id="procurarOrcamentoLista">
                    	</div>
                    	<div style="float:left; margin: 2px 0 0 2px;">
					        <button type="submit" data-role="button" data-transition="slide" data-icon="search" data-iconpos="notext" />
					    </div>
					</form>
					<div style="float:left; margin-top: 2px;">
						<a data-role="button" data-inline="true" data-iconpos="notext" data-transition="slide"  data-icon="plus" onclick="$('<div>').simpledialog2({ animate: false, mode: 'button', headerText: 'Nova venda', headerClose: true, buttonPrompt: 'Selecione o tipo da venda', buttons : { 'Venda': { click: function () { $.mobile.changePage( './clientes.php?T003_Id=new', { transition: 'fade'} ); }, icon: '', theme: 'c' }, 'Bonificação': { click: function () { $.mobile.changePage( './clientes.php?T003_Id=new&T003_Flag_Destino_Produto=5', { transition: 'fade'} ); }, icon: '', theme: 'c' }, 'Troca': { click: function () { $.mobile.changePage( './clientes.php?T003_Id=new&T003_Flag_Destino_Produto=6', { transition: 'fade'} ); }, icon: '', theme: 'c' } } });" style="float: right;"></a>
				    </div>
	            </div>
	        </div>
                <ul data-role="listview" style="margin-top: 1px;">
EOT;

// Produtos
//$filtro = ($C007['C007_Flag_Vendedor'] == 'D') ? '' : "T003_C007_Id_Vendedor_Interno = '{$g['usuarioAtual']}' AND";
$filtroStatusOrcamento = "(T003_Flag_Status_Orcamento = 'P' OR T003_Flag_Status_Orcamento = 'L' OR T003_Flag_Status_Orcamento = 'A' OR T003_Flag_Status_Orcamento = 'N' OR T003_Flag_Status_Orcamento = 'F') AND";
$pesquisaDiretor = ($C007['C007_Flag_Vendedor'] != 'D') ? '' : "OR Vendedor.C007_Primeiro_Nome LIKE '%{$pesquisa['busca']}%'";
if ($C007['C007_Flag_Vendedor'] == 'D' && $pesquisa['busca'][0] == '=') {
	$pesquisa['busca'] = preg_replace('/^=/', '', $pesquisa['busca']);
	$pesquisaDiretor = "Vendedor.C007_Primeiro_Nome LIKE '%{$pesquisa['busca']}%'";
	$pesquisaWhere = "({$pesquisaDiretor})";
} else {
	$pesquisaWhere = "(T003_Id LIKE '%{$pesquisa['busca']}%' OR D024_Nome_Fantasia LIKE '%{$pesquisa['busca']}%' OR DATE_FORMAT(T003_Data_Emissao,'%d/%m/%Y') LIKE '%{$pesquisa['busca']}%' {$pesquisaDiretor} OR T005_Id LIKE '%{$pesquisa['busca']}%' OR IF(T005_Id > 0, T005_Nome_Status, 'Orçamento') LIKE '%{$pesquisa['busca']}%')";
}
//Se for selecionado filtro por Status do Orçamento
if($statusOrcamento!='' && $statusOrcamento!='X'){
	$extra = '';
	if ($statusOrcamento == 'A') {
		$extra .= "AND (T003_Flag_PDF_Impresso is NULL OR T003_Flag_PDF_Impresso = '' OR T003_Flag_PDF_Impresso = 'N')";
	}
	$sql = <<<EOT
		SELECT T003_Id, D024_Id, D024_Nome_Fantasia, T003_Valor_Total_Produtos,T003_Flag_Status_Orcamento,T003_Flag_PDF_Impresso, T003_Valor_Total_Servicos, T003_Valor_Troca, T003_Valor_Total, T003_Data_Emissao, Vendedor.C007_Primeiro_Nome as C007_Primeiro_Nome
		FROM T003
		left join D021 on D021_Id=T003_D021_Id
		left join D024 on D024_Id=T003_D024_Id
		left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
		left join C007 as Externo on Externo.C007_Id=T003_C007_Id_Vendedor_Externo
		left join C007 as Liberou on Liberou.C007_Id=T003_Liberou_Alcada_C007_Id
		left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		left join C007 as Supervisor2 on Supervisor2.C007_Id=Vendedor.C007_C007_Id_2
		left join C007 as Substituto on Substituto.C007_Id=Vendedor.C007_Id_Vendedor_Substituto
		WHERE (T003_C004_Id={$g['empresaAtual']} AND {$filtro} {$filtroStatusOrcamento} {$pesquisaWhere} ) AND T003_Flag_Status_Orcamento!='C' AND IF(T005_Id > 0, T005_Flag_Status != '8',1=1) {$extra} {$g['conf']->ini('extraSqlPadraoVisualizacaoClientes')} ORDER BY {$pesquisa['ordem']} LIMIT 0,50;
EOT;
}else{
	$sql = <<<EOT
	SELECT T003_Id, D024_Id, D024_Nome_Fantasia, T003_Valor_Total_Produtos,T003_Flag_Status_Orcamento,T003_Flag_PDF_Impresso, T003_Valor_Total_Servicos, T003_Valor_Troca, T003_Valor_Total, T003_Data_Emissao, Vendedor.C007_Primeiro_Nome as C007_Primeiro_Nome
	FROM T003
	left join D021 on D021_Id=T003_D021_Id
	left join D024 on D024_Id=T003_D024_Id
	left join T005 on T003_Id=T005_T003_Id
	left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
	left join C007 as Externo on Externo.C007_Id=T003_C007_Id_Vendedor_Externo
	left join C007 as Liberou on Liberou.C007_Id=T003_Liberou_Alcada_C007_Id
	left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
	left join C007 as Supervisor2 on Supervisor2.C007_Id=Vendedor.C007_C007_Id_2
	left join C007 as Substituto on Substituto.C007_Id=Vendedor.C007_Id_Vendedor_Substituto
	WHERE T003_C004_Id={$g['empresaAtual']} AND {$filtro} {$filtroStatusOrcamento} {$pesquisaWhere} {$g['conf']->ini('extraSqlPadraoVisualizacaoClientes')} AND T003_Flag_Status_Orcamento!='C' AND IF(T005_Id > 0, T005_Flag_Status != '8',1=1) ORDER BY {$pesquisa['ordem']} LIMIT 0,50;
	EOT;
}

$res = mysql_query($sql);

while ($row = mysql_fetch_assoc($res)) {
	$row['T003_Valor_Total_Produtos'] = gCorrigeNumero($row['T003_Valor_Total_Produtos']);
	$row['T003_Valor_Total_Servicos'] = gCorrigeNumero($row['T003_Valor_Total_Servicos']);
	$row['T003_Valor_Troca'] = gCorrigeNumero($row['T003_Valor_Troca']);
	$row['T003_Valor_Total'] = gCorrigeNumero($row['T003_Valor_Total']);
	$row['T003_Data_Emissao'] = gCorrigeData($row['T003_Data_Emissao']);
	$row['D024_Nome_Fantasia'] = substr($row['D024_Nome_Fantasia'],0,27);
	$corStatus = "preto";
	$status = "Orçamento";
	$textoPedido = "";
	$href = <<<EOT
		<a href="./orcamento.php?T003_Id={$row['T003_Id']}&T003_Flag_Status_Orcamento={$statusOrcamento}" data-transition="slide" style="padding: 0px;">
EOT;
	$nomeCliente = $row['D024_Nome_Fantasia'];
	if($row['T003_Flag_Status_Orcamento'] == "A" or $row['T003_Flag_Status_Orcamento'] == 'F') {
		$sqlT005 = "SELECT T005_Id, T005_Nome_Status, T005_Flag_Status, T005_Data_Entrega, T005_T007_Id FROM T005 WHERE T005_T003_Id = '{$row['T003_Id']}';";
		$resT005 = mysql_query($sqlT005);
		$rowT005 = mysql_fetch_assoc($resT005);
		if($rowT005['T005_Id'] > 0) {
			$dadosNotaFiscal = '';
			$corStatus = "verde";
			$status = 'Pedido '.substr($rowT005['T005_Nome_Status'],0,1) . strtolower(substr($rowT005['T005_Nome_Status'],1));
			$rowT005['T005_Data_Entrega'] = gCorrigeData($rowT005['T005_Data_Entrega']);
        	$textoPedido = "Ped: {$rowT005['T005_Id']}";
        	$dataEntrega = "Entrega: {$rowT005['T005_Data_Entrega']}";
    		$href = <<<EOT
	    		<a href="./pedido/pedido.php?T005_Id={$rowT005['T005_Id']}&T003_Flag_Status_Orcamento={$statusOrcamento}" data-transition="slide" style="padding: 0px;">
EOT;
			switch ($rowT005['T005_Flag_Status']) {
				case '0':
					$corStatus = "azul";
					break;
				case '4':
					$corStatus = "cinza";
					break;
				case '5':
					$corStatus = "cinza";
					break;
				case '15':
					$corStatus = "vermelho";
					break;
				case '26':
					$corStatus = "laranja";
					break;
			}
			if($rowT005['T005_Flag_Status'] == '4' || $rowT005['T005_Flag_Status'] == '5'){
				$sqlT007 = mysql_query("SELECT T007_Numero_Nota_Fiscal, T007_Data_Emissao FROM T007 WHERE T007_Flag_Cancelada != 'S' AND (T007_T005_Id = '{$rowT005['T005_Id']}' OR IF('{$rowT005['T005_T007_Id']}' > 0, '{$rowT005['T005_T007_Id']}'=T007_Id, 1=1))");
				if(mysql_num_rows($sqlT007) > 0){
					$rowT007 = mysql_fetch_assoc($sqlT007);
					$rowT007['T007_Data_Emissao'] = gCorrigeData($rowT007['T007_Data_Emissao']);
					$dadosNotaFiscal = <<<EOT
						<div style="width: 100%; clear: both; text-align: left;margin: 2px;">NF: {$rowT007['T007_Numero_Nota_Fiscal']} - Emissão: {$rowT007['T007_Data_Emissao']} </div>
EOT;
				}
			}
        }
	}
	if (empty($row['D024_Nome_Fantasia']))
	{
		$row['D024_Nome_Fantasia']="<font style='weight:bold;'>\"NÃO INFORMADO\"</font>";
	}
	echo <<<EOT
		<li data-theme="c" id="status-{$corStatus}" data-icon="custom" class="iconLeft">
	    {$href}
	    	<div style="width: 100%; white-space: nowrap; padding: 8px !important;">
    			<div style="width: 100%; clear: both; text-align: left;margin: 2px;">Orç: {$row['T003_Id']}&nbsp;&nbsp;{$textoPedido}</div>
    			<div style="width: 100%; clear: both; text-align: left;margin: 2px;">Emissão: {$row['T003_Data_Emissao']}&nbsp;&nbsp;{$dataEntrega}</div>
    			<div style="width: 100%; clear: both; text-align: left;margin: 2px;">{$nomeCliente} - {$row['D024_Id']}</div>
    			<div style="width: 100%; clear: both; text-align: left;margin: 2px; white-space: normal;">{$row['C007_Primeiro_Nome']}&nbsp;-&nbsp;R$: {$row['T003_Valor_Total']}&nbsp;-&nbsp;{$status}</div>
    			{$dadosNotaFiscal}
	    	</div>
	    </a>
	    </li>
EOT;
}
echo <<<EOT
                </ul>
            </div>
	</div>
EOT;
include('inc.footer.php');
echo <<<EOT
    </div>
    <script>
    	setTimeout("location.reload();",1000 * 60);
		$( document ).undelegate("#pageOrcamentoLista", "pageinit");
    	$( document ).delegate("#pageOrcamentoLista", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
    	});
    </script>
EOT;
mysql_free_result($res);
mysql_free_result($resT005);
mysql_free_result($resT007);
