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

//Gera o Combo para pesquisa
$comboPesquisa = '';
$comboPesquisa .= '<option value=""'.(($pesquisa['colunaBusca'] == '') ? ' selected="selected"' : '').'> Geral </option>';
$comboPesquisa .= '<option value="D006_Codigo_CFOP"'.(($pesquisa['colunaBusca'] == 'D006_Codigo_CFOP') ? ' selected="selected"' : '').'> CFOP </option>';
$comboPesquisa .= '<option value="T007_Data_Emissao"'.(($pesquisa['colunaBusca'] == 'T007_Data_Emissao') ? ' selected="selected"' : '').'> Data </option>';
$comboPesquisa .= '<option value="T007_Numero_Nota_Fiscal"'.(($pesquisa['colunaBusca'] == 'T007_Numero_Nota_Fiscal') ? ' selected="selected"' : '').'> Nota Fiscal </option>';
$comboPesquisa .= '<option value="D024_Nome_Fantasia"'.(($pesquisa['colunaBusca'] == 'D024_Nome_Fantasia') ? ' selected="selected"' : '').'> Cliente </option>';
$comboPesquisa .= '<option value="Vendedor.C007_Primeiro_Nome"'.(($pesquisa['colunaBusca'] == 'Vendedor.C007_Primeiro_Nome') ? ' selected="selected"' : '').'> Vendedor </option>';

$confNota_FiscalLista = true;

//Filtro por Status de Nota Fiscal Analise/Negado/Aprovado/Pendente
$statusNota_Fiscal = isset($_REQUEST['statusNota_Fiscal']) ? $_REQUEST['statusNota_Fiscal'] : '';



include('mobile/tablet/inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageNota_FiscalLista">
EOT;
$etapa = 7;
include('mobile/tablet/inc.header.bar.php');

//Verifica Se Foi selecionado um critério para pesquisa
if($pesquisa['colunaBusca']!=''){
	//Verifica Se Foi selecionado um critério tipo data
	if($pesquisa['colunaBusca']=='T007_Data_Emissao'){
		$condicaoPesquisa = "AND date_format({$pesquisa['colunaBusca']},'%d/%m/%Y') like '%{$pesquisa['busca']}%'";
	}else if($pesquisa['colunaBusca']=='T007_Numero_Nota_Fiscal'){
		$condicaoPesquisa = "AND {$pesquisa['colunaBusca']} = '{$pesquisa['busca']}'";
	}else if($pesquisa['colunaBusca']=='D006_Codigo_CFOP'){
	    $condicaoPesquisa = "AND (D006_Codigo_CFOP LIKE '%{$pesquisa['busca']}%' OR D006_Descricao LIKE '%{$pesquisa['busca']}%')";
	}else{
		$condicaoPesquisa = "AND {$pesquisa['colunaBusca']} LIKE '%{$pesquisa['busca']}%'";
	}
}else{
	$condicaoPesquisa = "AND (date_format(T007_Data_Emissao,'%d/%m/%Y') LIKE '%{$pesquisa['busca']}%' OR T007_Numero_Nota_Fiscal = '{$pesquisa['busca']}' OR (D006_Codigo_CFOP LIKE '%{$pesquisa['busca']}%' OR D006_Descricao LIKE '%{$pesquisa['busca']}%') OR D024_Nome_Fantasia LIKE '%{$pesquisa['busca']}%')";
}

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
                <div class="ui-block-a" style="width: 20%;">
                </div>
	            <div class="ui-block-b" style="width: 60%;">
	           		<form id="formPesquisa" name="formPesquisa" method="get" action="nota_fiscalLista.php?T007_Id={$T007_Id}" data-transition="fade">
	            		<div style="width: 48%; float:left; margin-top: 8px; margin-bottom: 5px;">
                    		<input name="pesquisa" placeholder="Procurar Nota Fiscal" data-type="search" class="ui-input-text ui-body-c" value="{$pesquisa['busca']}" id="procurarNota_FiscalLista">
                    	</div>
                    	<div style="float:left;">
                    		<select name="colunaPesquisa" id="colunaPesquisa" data-inline="true">
						     	{$comboPesquisa}
						   </select>
					    </div>
                    	<div style="float:left;">
					        <input type="submit" value="Procurar" data-role="button" data-transition="slide" data-icon="search" data-iconpos="left" />
					       <!-- <a data-role="button" data-transition="slide" data-icon="search" data-iconpos="left" onclick="\$.mobile.changePage( './nota_fiscalLista.php', {transition: 'fade', type:'post', data: 'T007_Id={$T007_Id}&colunaPesquisa=' + encodeURIComponent($('#colunaPesquisa').val()) + '&pesquisa=' + encodeURIComponent($('#procurarNota_FiscalLista').val())} )" >
			            		Procurar
					        </a>-->
					    </div>
					 </form>
	            </div>
		    </div>
		    <div class="ui-grid-b" style="margin: 0px 0 5px 0; border: 1px solid #CCCCCC; box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3); padding: 8px 0;">
                    <div class="ui-block-a" style="text-align: center; width:{$tamanhoCampo}%; float: right;">
                        <h4 style="margin: 0px;">
	                            Valor Total
                        </h4>
                        <h4 style="margin: 0px;">
                            R$ {$rowTotalizadres['valorTotalGeral']}
                        </h4>
                    </div>
                    <div class="ui-block-b" style="border-right: 2px solid #CCCCCC; text-align: center; width:{$tamanhoCampo}%; float: right;">
                        <h4 style="margin: 0px;">
                            Valor Produtos
                        </h4>
                        <h4 style="margin: 0px;">
                            R$ {$rowTotalizadres['valorTotalProdutos']}
                        </h4>
                    </div>
                    <div class="ui-block-c" style="border-right: 2px solid #CCCCCC; text-align: center; width:{$tamanhoCampo}%; float: right; width:32%;	">
                        <h4 style="margin: 0px;">
                            Notas Emitidas
                        </h4>
                        <h4 style="margin: 0px;">
                            {$rowTotalizadres['qtdeNotas']}
                        </h4>
                    </div>
                </div>
                <ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 1px;">
                    <li data-role="list-divider" role="heading">
	    				<table style="width: 100%";>
	    					<tr>
EOT;

		//Verifica Ordenação da Coluna pelo clique do usuário DESC ou ASC
		$D006_Codigo_CFOP = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D006_Codigo_CFOP' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$T007_Data_Emissao = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T007_Data_Emissao' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$T007_Numero_Nota_Fiscal = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T007_Numero_Nota_Fiscal' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$D024_Id = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D024_Id' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$D024_Nome_Fantasia = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D024_Nome_Fantasia' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$T007_Valor_Total_Produtos = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T007_Valor_Total_Produtos' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$T007_Valor_Total = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T007_Valor_Total' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$C007_Primeiro_Nome = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'Vendedor.C007_Primeiro_Nome' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';

		//Define Icone de Ordenação na coluna do Grid
		//Define a imagem
		if(isset($pesquisa['coluna']) && $pesquisa['tipoOrdenacao'] == 'DESC'){
			$iconeOrdenacao = "<img src='/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/arrow-down-3.png'></img>";
		}else if(isset($pesquisa['coluna']) && $pesquisa['tipoOrdenacao'] == 'ASC'){
			$iconeOrdenacao = "<img src='/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/arrow-up-3.png'></img>";
		}else{
			$iconeOrdenacao = '';
		}
		//Verifica qual coluna foi selecionada para mostrar o icone apenas na coluna selecionada
		$D006_Codigo_CFOP_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D006_Codigo_CFOP') ? $iconeOrdenacao : '';
		$T007_Data_Emissao_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T007_Data_Emissao') ? $iconeOrdenacao : '';
		$T007_Numero_Nota_Fiscal_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T007_Numero_Nota_Fiscal') ? $iconeOrdenacao : '';
		$D024_Id_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D024_Id') ? $iconeOrdenacao : '';
		$D024_Nome_Fantasia_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D024_Nome_Fantasia') ? $iconeOrdenacao : '';
		$T007_Valor_Total_Produtos_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T007_Valor_Total_Produtos') ? $iconeOrdenacao : '';
		$T007_Valor_Total_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T007_Valor_Total') ? $iconeOrdenacao : '';
		$C007_Primeiro_Nome_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'Vendedor.C007_Primeiro_Nome') ? $iconeOrdenacao : '';

		if ($C007['C007_Flag_Vendedor']=='D' OR $C007['C007_Flag_Vendedor']=='S') {
		// GRID: Diretor
		echo <<<EOT
	    						<td style="width: 7%"; align="center";><a href="nota_fiscalLista.php?coluna=D006_Codigo_CFOP&tipoOrdenacao={$D006_Codigo_CFOP}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none; padding-bottom:0px;" data-transition="fade">CFOP{$D006_Codigo_CFOP_Icone}</a></td>
	    						<td style="width: 7%"; align="center";><a href="nota_fiscalLista.php?coluna=T007_Data_Emissao&tipoOrdenacao={$T007_Data_Emissao}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none; padding-bottom:0px;" data-transition="fade">Data{$T007_Data_Emissao_Icone}</a></td>
	    						<td style="width: 8%"; align=center><a href="nota_fiscalLista.php?coluna=T007_Numero_Nota_Fiscal&tipoOrdenacao={$T007_Numero_Nota_Fiscal}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Nº{$T007_Numero_Nota_Fiscal_Icone}</a></td>
	    						<td style="width: 5%"; align="center"><a href="nota_fiscalLista.php?coluna=D024_Id&tipoOrdenacao={$D024_Id}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Cód{$D024_Id_Icone}</a></td>
	    						<td style="width: 28%";><a href="nota_fiscalLista.php?coluna=D024_Nome_Fantasia&tipoOrdenacao={$D024_Nome_Fantasia}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Cliente{$D024_Nome_Fantasia_Icone}</td>
	    						<td style="width: 12%;"; align="right"><a href="nota_fiscalLista.php?coluna=T007_Valor_Total_Produtos&tipoOrdenacao={$T007_Valor_Total_Produtos}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Produtos R$ {$T007_Valor_Total_Produtos_Icone}</a></td>
	    						<td style="width: 9%"; align="right"><a href="nota_fiscalLista.php?coluna=T007_Valor_Total&tipoOrdenacao={$T007_Valor_Total}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Valor R$ {$T007_Valor_Total_Icone}</a></td>
	    						<td style="width: 20%"; align="center"><a href="nota_fiscalLista.php?coluna=Vendedor.C007_Primeiro_Nome&tipoOrdenacao={$C007_Primeiro_Nome}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Vendedor{$C007_Primeiro_Nome_Icone}</a></td>
EOT;
	} else {
		// GRID: Vendedor
		echo <<<EOT
	    						<td style="width: 12%"; align="center";><a href="nota_fiscalLista.php?coluna=D006_Codigo_CFOP&tipoOrdenacao={$D006_Codigo_CFOP}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none; padding-bottom:0px;" data-transition="fade">CFOP{$D006_Codigo_CFOP_Icone}</a></td>
                                <td style="width: 12%"; align="center";><a href="nota_fiscalLista.php?coluna=T007_Data_Emissao&tipoOrdenacao={$T007_Data_Emissao}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none; padding-bottom:0px;" data-transition="fade">Data{$T007_Data_Emissao_Icone}</a></td>
                                <td style="width: 8%"; align=center><a href="nota_fiscalLista.php?coluna=T007_Numero_Nota_Fiscal&tipoOrdenacao={$T007_Numero_Nota_Fiscal}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Nº{$T007_Numero_Nota_Fiscal_Icone}</a></td>
                                <td style="width: 10%"; align="center"><a href="nota_fiscalLista.php?coluna=D024_Id&tipoOrdenacao={$D024_Id}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Cód{$D024_Id_Icone}</a></td>
                                <td style="width: 33%";><a href="nota_fiscalLista.php?coluna=D024_Nome_Fantasia&tipoOrdenacao={$D024_Nome_Fantasia}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Cliente{$D024_Nome_Fantasia_Icone}</td>
                                <td style="width: 12%;"; align="right"><a href="nota_fiscalLista.php?coluna=T007_Valor_Total_Produtos&tipoOrdenacao={$T007_Valor_Total_Produtos}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Produtos R$ {$T007_Valor_Total_Produtos_Icone}</a></td>
                                <td style="width: 9%"; align="right"><a href="nota_fiscalLista.php?coluna=T007_Valor_Total&tipoOrdenacao={$T007_Valor_Total}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Valor R$ {$T007_Valor_Total_Icone}</a></td>
EOT;
	}
	echo <<<EOT
							</tr>
	    				</table>
                    </li>
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
	if ($C007['C007_Flag_Vendedor']=='D' OR $C007['C007_Flag_Vendedor']=='S') {
		// GRID: Diretor
		echo <<<EOT
			<li data-theme="c" data-icon="false">
		    {$href}
	    		<table style="width: 100%; {$corLinha}">
	    			<tr>
	    				<td style="width: 7%"; align="center">{$row['D006_Codigo_CFOP']}</td>
	    				<td style="width: 7%"; align="center">{$row['T007_Data_Emissao']}</td>
	    				<td style="width: 8%"; align="center">{$row['T007_Numero_Nota_Fiscal']}</td>
	    				<td style="width: 5%"; align="center">{$row['D024_Id']}</td>
	    				<td style="width: 28%";>{$nomeCliente}</td>
	    				<td style="width: 12%"; align="right">{$row['T007_Valor_Total_Produtos']}</td>
	    				<td style="width: 9%"; align="right">{$row['T007_Valor_Total']}</td>
	    				<td style="width: 20%"; align="center">{$row['Primeiro_Nome']}</td>
	    			</tr>
	    		</table>
		    </a>
		    </li>
EOT;
	} else {
		// GRID: Vendedor
		echo <<<EOT
			<li data-theme="c" data-icon="false">
		    {$href}
	    		<table style="width: 100%; {$corLinha}">
	    			<tr>
	    				<td style="width: 12%"; align="center">{$row['D006_Codigo_CFOP']}</td>
                        <td style="width: 12%"; align="center">{$row['T007_Data_Emissao']}</td>
                        <td style="width: 8%"; align="center">{$row['T007_Numero_Nota_Fiscal']}</td>
                        <td style="width: 10%"; align="center">{$row['D024_Id']}</td>
                        <td style="width: 33%";>{$nomeCliente}</td>
                        <td style="width: 12%"; align="right">{$row['T007_Valor_Total_Produtos']}</td>
                        <td style="width: 9%"; align="right">{$row['T007_Valor_Total']}</td>
	    			</tr>
	    		</table>
		    </a>
		    </li>
EOT;
	}
}

echo <<<EOT
                </ul>
            </div>
EOT;
include('mobile/tablet/inc.footer.php');
echo <<<EOT
	</div>	
EOT;

echo <<<EOT
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
