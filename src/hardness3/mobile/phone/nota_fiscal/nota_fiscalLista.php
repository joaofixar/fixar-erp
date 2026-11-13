<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');


//Parametro de Pesquisa e Coluna da Pesquisa selecionada em orçamentos
$arqFiltro = $g['pathDadosUsuario'] . 'tabletFiltro/nota_fiscalLista';
if (!file_exists($arqFiltro)) {
	$pesquisa = array('busca' => '', 'ordem' => 'T007_Numero_Nota_Fiscal DESC', 'colunaBusca' => '', 'tipoOrdenacao' => '', 'coluna' => '');
	file_put_contents($arqFiltro, serialize($pesquisa));
}

$pesquisa = unserialize(file_get_contents($arqFiltro));
if (isset($_REQUEST['pesquisa'])) {
	$pesquisa['busca'] = $_REQUEST['pesquisa'];
	file_put_contents($arqFiltro, serialize($pesquisa));
}

$confNota_FiscalLista = true;

//Filtro por Status de Nota Fiscal Analise/Negado/Aprovado/Pendente
$statusNota_Fiscal = isset($_REQUEST['statusNota_Fiscal']) ? $_REQUEST['statusNota_Fiscal'] : '';



include('mobile/phone/inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageNota_FiscalLista">
EOT;
$etapa = 7;
include('mobile/phone/inc.header.bar.php');

$condicaoPesquisa = "AND (date_format(T007_Data_Emissao,'%d/%m/%Y') LIKE '%{$pesquisa['busca']}%' OR T007_Numero_Nota_Fiscal = '{$pesquisa['busca']}' OR (D006_Codigo_CFOP LIKE '%{$pesquisa['busca']}%' OR D006_Descricao LIKE '%{$pesquisa['busca']}%') OR D024_Nome_Fantasia LIKE '%{$pesquisa['busca']}%')";

$filtro = '';
if($C007['C007_Flag_Vendedor'] == 'V'){
	$filtro = "AND (Vendedor.C007_Id='{$g['usuarioAtual']}')";
} else if($C007['C007_Flag_Vendedor'] == 'S'){
	$filtro = "AND (Vendedor.C007_Id='{$g['usuarioAtual']}' OR Supervisor.C007_Id='{$g['usuarioAtual']}')";
}

if($statusNota_Fiscal!='' && $statusNota_Fiscal!='X'){
	$extra = "WHERE (T007_C004_Id={$g['empresaAtual']} {$condicaoPesquisa}) {$filtro} AND T007_Flag_Cancelada='{$statusNota_Fiscal}' ORDER BY {$pesquisa['ordem']} LIMIT 0,100;";
} else {
	$extra = "WHERE (T007_C004_Id={$g['empresaAtual']} {$condicaoPesquisa}) {$filtro} ORDER BY {$pesquisa['ordem']} LIMIT 0,100;";
}
$sql = <<<EOT
	SELECT T007_Id, T007_Numero_Nota_Fiscal, T007_Flag_Cancelada, D006_Codigo_CFOP, D024_Id, D024_Nome_Fantasia, T007_Valor_Total_Produtos, T007_Valor_Total, T007_Data_Emissao, Vendedor.C007_Primeiro_Nome as Primeiro_Nome
	FROM T007
	left join D024 on D024_Id=T007_D024_Id
	left join D006 on D006_Id=T007_D006_Id
	left join C007 as Vendedor on Vendedor.C007_Id=T007_C007_Id_Vendedor_Interno
	left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
	{$extra}
EOT;
$sqlTotalizadores =  <<<EOT
	SELECT COUNT(T007_Id) AS qtdeNotas, SUM(T007_Valor_Total_Produtos) AS valorTotalProdutos, SUM(T007_Valor_Total) AS valorTotalGeral FROM T007 left join D024 on D024_Id=T007_D024_Id
	left join D006 on D006_Id=T007_D006_Id
	left join C007 as Vendedor on Vendedor.C007_Id=T007_C007_Id_Vendedor_Interno
	left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
	{$extra}
EOT;
$resTotalizadores = mysql_query($sqlTotalizadores);
$rowTotalizadres = mysql_fetch_array($resTotalizadores);
$rowTotalizadres['valorTotalGeral'] = gCorrigeNumero($rowTotalizadres['valorTotalGeral']);
$rowTotalizadres['valorTotalProdutos'] = gCorrigeNumero($rowTotalizadres['valorTotalProdutos']);

echo <<<EOT
		<div data-role="content">
        	<div class="ui-grid-b" style="margin-top: -10px; margin-bottom: 0px;">
	            <div>
	           		<form id="formPesquisa" name="formPesquisa" method="get" action="nota_fiscalLista.php?T007_Id={$T007_Id}" data-transition="fade">
	            		<div style="width: 80%; float:left; margin-top: 8px; margin-bottom: 5px;">
                    		<input name="pesquisa" placeholder="Procurar Nota Fiscal" data-type="search" class="ui-input-text ui-body-c" value="{$pesquisa['busca']}" id="procurarNota_FiscalLista">
                    	</div>
                    	<div style="float:left; margin-top: 3px;">
					        <input type="submit" data-role="button" data-transition="slide" data-icon="search" data-iconpos="notext" />
					    </div>
					 </form>
	            </div>
		    </div>
			<div style="margin: 0px 0 5px 0; border: 1px solid #CCCCCC; box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3); padding: 8px 0;">
			    <div class="ui-grid-a">
		            <div class="ui-block-a" style="border-right: 2px solid #CCCCCC; text-align: center; width: 48%;">
		                <h4 style="margin: 0px;">
		                    Notas Emitidas
		                </h4>
		                <h4 style="margin: 0px;">
		                    {$rowTotalizadres['qtdeNotas']}
		                </h4>
		            </div>
		            <div class="ui-block-b" style="text-align: center;">
		                <h4 style="margin: 0px;">
		                    Valor Produtos
		                </h4>
		                <h4 style="margin: 0px;">
		                    R$ {$rowTotalizadres['valorTotalProdutos']}
		                </h4>
		            </div>
		        </div>
		        <div style="margin-top: 5px;">
		            <div style="text-align: center;">
		                <h4 style="margin: 0px;">
		                    Valor Total
		                </h4>
		                <h4 style="margin: 0px;">
		                    R$ {$rowTotalizadres['valorTotalGeral']}
		                </h4>
		            </div>
		        </div>
            </div>
            <ul data-role="listview" style="margin-top: 1px;">
EOT;


$res = mysql_query($sql);
while ($row = mysql_fetch_assoc($res)) {
	$row['T007_Valor_Total_Produtos'] = gCorrigeNumero($row['T007_Valor_Total_Produtos']);
	$row['T007_Valor_Total'] = gCorrigeNumero($row['T007_Valor_Total']);
	$row['T007_Data_Emissao'] = gCorrigeData($row['T007_Data_Emissao']);
	$row['D024_Nome_Fantasia'] = substr($row['D024_Nome_Fantasia'],0,27);
	$corLinha = "";
	$href = <<<EOT
		<a href="/hardness3/hardness/danfe/imprimir_danfe.php?T007_Id={$T007_Id}{$row['T007_Id']}" data-ajax="false">
EOT;
	$nomeCliente = $row['D024_Nome_Fantasia'];
	if($row['T007_Flag_Cancelada'] == "S"){
		$corLinha = "color:red;";
	}
	echo <<<EOT
		<li data-theme="c" data-icon="false">
		    {$href}
		    	<div style="width: 100%; clear: both; text-align: left; margin: 2px;">Nº: {$row['T007_Numero_Nota_Fiscal']} - {$row['T007_Data_Emissao']} - {$row['D006_Codigo_CFOP']}</div>
		    	<div style="width: 100%; clear: both; text-align: left; margin: 2px; whites-space: normal;">{$row['D024_Id']}&nbsp; - &nbsp;{$nomeCliente}</div>
		    	<div style="width: 100%; clear: both; text-align: left; margin: 2px;">Produtos: R$ {$row['T007_Valor_Total_Produtos']} - Total: R$ {$row['T007_Valor_Total']}</div>
				<div style="width: 100%; clear: both; text-align: left; margin: 2px;">Vendedor: {$row['Primeiro_Nome']}</div>
		    </a>
	    </li>
EOT;
}

echo <<<EOT
                </ul>
            </div>
	</div>
    </div>
    <script>
    	setTimeout("location.reload();", 1000 * 60);
    	$( document ).undelegate("#pageNota_FiscalLista", "pageinit");
    	$( document ).delegate("#pageNota_FiscalLista", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
    	});
    </script>
EOT;
