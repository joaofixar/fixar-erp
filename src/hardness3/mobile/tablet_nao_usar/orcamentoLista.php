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

//Gera o Combo para pesquisa
$comboPesquisa = '';
$comboPesquisa .= '<option value=""'.(($pesquisa['colunaBusca'] == '') ? ' selected="selected"' : '').'> Geral </option>';
$comboPesquisa .= '<option value="D024_Nome_Fantasia"'.(($pesquisa['colunaBusca'] == 'D024_Nome_Fantasia') ? ' selected="selected"' : '').'> Cliente </option>';
$comboPesquisa .= '<option value="C007_Primeiro_Nome"'.(($pesquisa['colunaBusca'] == 'C007_Primeiro_Nome') ? ' selected="selected"' : '').'> Vendedor </option>';
$comboPesquisa .= '<option value="T003_Id"'.(($pesquisa['colunaBusca'] == 'T003_Id') ? ' selected="selected"' : '').'> Orçamento </option>';
$comboPesquisa .= '<option value="T003_Data_Emissao"'.(($pesquisa['colunaBusca'] == 'T003_Data_Emissao') ? ' selected="selected"' : '').'> Data </option>';
$confOrcamentoLista = true;

//Filtro por Status de Orcamento Analise/Negado/Aprovado/Pendente
$statusOrcamento = isset($_REQUEST['statusOrcamento']) ? $_REQUEST['statusOrcamento'] : '';



include('inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageOrcamentoLista">
EOT;
$etapa = 1;
include('inc.header.bar.php');
echo <<<EOT
		<div data-role="content">
        	<div class="ui-grid-b" style="margin-top: -10px; margin-bottom: 5px;">
                <div class="ui-block-a" style="width: 20%;">

                </div>
	            <div class="ui-block-b" style="width: 60%;">
	           		<form id="formPesquisa" name="formPesquisa" method="get" action="orcamentoLista.php?T003_Id={$T003_Id}" data-transition="fade">
	            		<div style="width: 48%; float:left; margin-top: 8px; margin-bottom: 5px;">
                    		<input name="pesquisa" placeholder="Procurar Orçamento" data-type="search" class="ui-input-text ui-body-c" value="{$pesquisa['busca']}" id="procurarOrcamentoLista">
                    	</div>
                    	<div style="float:left;">
                    		<select name="colunaPesquisa" id="colunaPesquisa" data-inline="true">
						     	{$comboPesquisa}
						   </select>
					    </div>
                    	<div style="float:left;">
					        <input type="submit" value="Procurar" data-role="button" data-transition="slide" data-icon="search" data-iconpos="left" />
					       <!-- <a data-role="button" data-transition="slide" data-icon="search" data-iconpos="left" onclick="\$.mobile.changePage( './orcamentoLista.php', {transition: 'fade', type:'post', data: 'T003_Id={$T003_Id}&colunaPesquisa=' + encodeURIComponent($('#colunaPesquisa').val()) + '&pesquisa=' + encodeURIComponent($('#procurarOrcamentoLista').val())} )" >
			            		Procurar
					        </a>-->
					    </div>
					 </form>
	            </div>
        	<div class="ui-grid-a">
        		<div class="ui-block-a">
	            </div>
	            <div class="ui-block-b" style="float: right;">
                    <a data-role="button" data-inline="true" data-transition="slide" onclick="$('<div>').simpledialog2({ animate: false, mode: 'button', headerText: 'Nova venda', headerClose: true, buttonPrompt: 'Selecione o tipo da venda', buttons : { 'Venda': { click: function () { $.mobile.changePage( './clientes.php?T003_Id=new', { transition: 'fade'} ); }, icon: '', theme: 'c' }, 'Bonificação': { click: function () { $.mobile.changePage( './clientes.php?T003_Id=new&T003_Flag_Destino_Produto=5', { transition: 'fade'} ); }, icon: '', theme: 'c' }, 'Troca': { click: function () { $.mobile.changePage( './clientes.php?T003_Id=new&T003_Flag_Destino_Produto=6', { transition: 'fade'} ); }, icon: '', theme: 'c' } } });" style="float: right;">
						Novo
					</a>
	            </div>
	        </div>
                <ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 1px;">
                    <li data-role="list-divider" role="heading">
	    				<table style="width: 100%";>
	    					<tr>
EOT;

	//Verifica Ordenação da Coluna pelo clique do usuário DESC ou ASC
	$T003_Data_Emissao = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T003_Data_Emissao' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
	$T003_Id = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T003_Id' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
	$T005_Id = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T005_Id' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
	$D024_Id = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D024_Id' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
	$D024_Nome_Fantasia = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D024_Nome_Fantasia' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
	$T003_Valor_Total_Produtos = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T003_Valor_Total_Produtos' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
	$T003_Valor_Total_Servicos = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T003_Valor_Total_Servicos' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
	$T003_Valor_Troca = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T003_Valor_Troca' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
	$T003_Valor_Total = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T003_Valor_Total' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
	$C007_Primeiro_Nome = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'C007_Primeiro_Nome' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';

	//Define Icone de Ordenação na coluna do Grid
	//Define a imagem
	if(isset($pesquisa['coluna']) && $pesquisa['tipoOrdenacao'] == 'DESC') {
		$iconeOrdenacao = "<img src='/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/arrow-down-3.png'></img>";
	} else if(isset($pesquisa['coluna']) && $pesquisa['tipoOrdenacao'] == 'ASC') {
		$iconeOrdenacao = "<img src='/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/arrow-up-3.png'></img>";
	} else {
		$iconeOrdenacao = '';
	}
	//Verifica qual coluna foi selecionada para mostrar o icone apenas na coluna selecionada
	$T003_Data_Emissao_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T003_Data_Emissao') ? $iconeOrdenacao : '';
	$T003_Id_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T003_Id') ? $iconeOrdenacao : '';
	$T005_Id_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T005_Id') ? $iconeOrdenacao : '';
	$D024_Id_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D024_Id') ? $iconeOrdenacao : '';
	$D024_Nome_Fantasia_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D024_Nome_Fantasia') ? $iconeOrdenacao : '';
	$T003_Valor_Total_Produtos_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T003_Valor_Total_Produtos') ? $iconeOrdenacao : '';
	$T003_Valor_Total_Servicos_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T003_Valor_Total_Servicos') ? $iconeOrdenacao : '';
	$T003_Valor_Troca_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T003_Valor_Troca') ? $iconeOrdenacao : '';
	$T003_Valor_Total_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T003_Valor_Total') ? $iconeOrdenacao : '';
	$C007_Primeiro_Nome_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'C007_Primeiro_Nome') ? $iconeOrdenacao : '';

	$exibirServicos = ($g['C031']['habilitarServicos'] == 'N') ? 'display: none;' : '';
	$exibirTrocas 	= ($g['C031']['habilitarTrocas'] == 'N') ? 'display: none;' : '';
	if ($C007['C007_Flag_Vendedor']=='D' OR $C007['C007_Flag_Vendedor']=='S') {
		// GRID: Diretor
		echo <<<EOT
			<td style="width: 2%;"></td>
	    	<td style="width: 8%" align="center";><a href="orcamentoLista.php?coluna=T003_Data_Emissao&tipoOrdenacao={$T003_Data_Emissao}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none; padding-bottom:0px;" data-transition="fade">Data{$T003_Data_Emissao_Icone}</a></td>
	    	<td style="width: 8%" align=center><a href="orcamentoLista.php?coluna=T003_Id&tipoOrdenacao={$T003_Id}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Orç{$T003_Id_Icone}</a></td>
	    	<td style="width: 8%" align=center><a href="orcamentoLista.php?coluna=T005_Id&tipoOrdenacao={$T005_Id}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Ped{$T005_Id_Icone}</a></td>
	    	<td style="width: 8%" align="center"><a href="orcamentoLista.php?coluna=D024_Id&tipoOrdenacao={$D024_Id}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Cód{$D024_Id_Icone}</a></td>
	    	<td style="width: 36%"><a href="orcamentoLista.php?coluna=D024_Nome_Fantasia&tipoOrdenacao={$D024_Nome_Fantasia}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Cliente{$D024_Nome_Fantasia_Icone}</td>
	    	<td style="width: 11%; {$exibirServicos}" align="right"><a href="orcamentoLista.php?coluna=T003_Valor_Total_Servicos&tipoOrdenacao={$T003_Valor_Total_Servicos}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Serviços R$ {$T003_Valor_Total_Servicos_Icone}</a></td>
	    	<td style="width: 10%; {$exibirTrocas}" align="right"><a href="orcamentoLista.php?coluna=T003_Valor_Troca&tipoOrdenacao={$T003_Valor_Troca}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Trocas R$ {$T003_Valor_Troca_Icone}</a></td>
	    	<td style="width: 10%" align="right"><a href="orcamentoLista.php?coluna=T003_Valor_Total&tipoOrdenacao={$T003_Valor_Total}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Valor R$ {$T003_Valor_Total_Icone}</a></td>
	    	<td style="width: 16%" align="center"><a href="orcamentoLista.php?coluna=C007_Primeiro_Nome&tipoOrdenacao={$C007_Primeiro_Nome}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Vendedor{$C007_Primeiro_Nome_Icone}</a></td>
EOT;
	} else {
		// GRID: Vendedor
		echo <<<EOT
			<td style="width: 2%;"></td>
	    	<td style="width: 7%" align="center"><a href="orcamentoLista.php?coluna=T003_Data_Emissao&tipoOrdenacao={$T003_Data_Emissao}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Data{$T003_Data_Emissao_Icone}</a></td>
	    	<td style="width: 6%" align=center><a href="orcamentoLista.php?coluna=T003_Id&tipoOrdenacao={$T003_Id}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Orç{$T003_Id_Icone}</a></td>
	    	<td style="width: 6%" align=center><a href="orcamentoLista.php?coluna=T005_Id&tipoOrdenacao={$T005_Id}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Ped{$T005_Id_Icone}</a></td>
	    	<td style="width: 6%" align="center"><a href="orcamentoLista.php?coluna=D024_Id&tipoOrdenacao={$D024_Id}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Cód{$D024_Id_Icone}</a></td>
	    	<td style="width: 33%"><a href="orcamentoLista.php?coluna=D024_Nome_Fantasia&tipoOrdenacao={$D024_Nome_Fantasia}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Cliente{$D024_Nome_Fantasia_Icone}</td>
	    	<td style="width: 12%; {$exibirServicos}" align="right"><a href="orcamentoLista.php?coluna=T003_Valor_Total_Servicos&tipoOrdenacao={$T003_Valor_Total_Servicos}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Serviços R$ {$T003_Valor_Total_Servicos_Icone}</a></td>
	    	<td style="width: 10%; {$exibirTrocas}" align="right"><a href="orcamentoLista.php?coluna=T003_Valor_Troca&tipoOrdenacao={$T003_Valor_Troca}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Trocas R$ {$T003_Valor_Troca_Icone}</a></td>
	    	<td style="width: 12%" align="right"><a href="orcamentoLista.php?coluna=T003_Valor_Total&tipoOrdenacao={$T003_Valor_Total}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Valor R$ {$T003_Valor_Total_Icone}</a></td>
EOT;
	}
	echo <<<EOT
			</tr>
	   	</table>
    </li>
EOT;

// Produtos



$filtro = '';
if($C007['C007_Flag_Vendedor'] == 'V'){
	//$filtro = "(vendedor.C007_Id='{$g['usuarioAtual']}' or externo.C007_Id='{$g['usuarioAtual']}' or substituto.C007_Id='{$g['usuarioAtual']}') AND";
} else if($C007['C007_Flag_Vendedor'] == 'S'){
	//$filtro = "(supervisor.C007_Id='{$g['usuarioAtual']}' or supervisor2.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') AND";
}

//$filtroStatusOrcamento = "(T003_Flag_Status_Orcamento = 'P' OR T003_Flag_Status_Orcamento = 'L' OR T003_Flag_Status_Orcamento = 'A' OR T003_Flag_Status_Orcamento = 'N' OR T003_Flag_Status_Orcamento = 'F') AND";
$pesquisaDiretor = ($C007['C007_Flag_Vendedor'] != 'D') ? '' : "OR C007_Primeiro_Nome LIKE '%{$pesquisa['busca']}%'";
if ($C007['C007_Flag_Vendedor']=='D' OR $C007['C007_Flag_Vendedor']=='S' && $pesquisa['busca'][0] == '=') {
	//$pesquisa['busca'] = preg_replace('/^=/', '', $pesquisa['busca']);
	//$pesquisaDiretor = "C007_Primeiro_Nome LIKE '%{$pesquisa['busca']}%'";
	//$pesquisaWhere = "({$pesquisaDiretor})";
} else {
	//$pesquisaWhere = "(T003_Id LIKE '%{$pesquisa['busca']}%' OR D024_Nome_Fantasia LIKE '%{$pesquisa['busca']}%' OR DATE_FORMAT(T003_Data_Emissao,'%d/%m/%Y') LIKE '%{$pesquisa['busca']}%' {$pesquisaDiretor})";
}

//Verifica Se Foi selecionado um critério para pesquisa
if($pesquisa['colunaBusca']!=''){
	//Verifica Se Foi selecionado um critério tipo data
	if($pesquisa['colunaBusca']=='T003_Data_Emissao'){
		$condicaoPesquisa = "date_format({$pesquisa['colunaBusca']},'%d/%m/%Y') like '%{$pesquisa['busca']}%'";
	}else if($pesquisa['colunaBusca']=='T003_Id'){
		$condicaoPesquisa = "{$pesquisa['colunaBusca']} = '{$pesquisa['busca']}'";
	}else{
		$condicaoPesquisa = "{$pesquisa['colunaBusca']} LIKE '%{$pesquisa['busca']}%'";
	}
}else{
	$condicaoPesquisa = "(T003_Id LIKE '%{$pesquisa['busca']}%' OR D024_Nome_Fantasia LIKE '%{$pesquisa['busca']}%' OR DATE_FORMAT(T003_Data_Emissao,'%d/%m/%Y') LIKE '%{$pesquisa['busca']}%')";
}
//Se for selecionado filtro por Status do Orçamento
if($statusOrcamento!='' && $statusOrcamento!='X'){
	$extra = '';
	if ($statusOrcamento == 'A') {
		$extra .= "AND (T003_Flag_PDF_Impresso is NULL OR T003_Flag_PDF_Impresso = '' OR T003_Flag_PDF_Impresso = 'N')";
	}
	$sql = <<<EOT
		SELECT T003_Id, T005_Id, D024_Id, D024_Nome_Fantasia, T003_Valor_Total_Produtos,T003_Flag_Status_Orcamento,T003_Flag_PDF_Impresso, T003_Valor_Total_Servicos, T003_Valor_Troca, T003_Valor_Total, T003_Data_Emissao, Vendedor.C007_Primeiro_Nome
		FROM T003
		left join D024 on D024_Id=T003_D024_Id
		left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
		left join C007 as Externo on Externo.C007_Id=D024_C007_Vendedor_Externo
		left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		left join C007 as Supervisor2 on Supervisor2.C007_Id=Vendedor.C007_C007_Id_2
		left join C007 as Substituto on Substituto.C007_Id=Vendedor.C007_Id_Vendedor_Substituto
		LEFT JOIN T005 ON T005_T003_Id = T003_Id
		WHERE (T003_C004_Id={$g['empresaAtual']} AND {$filtro} {$filtroStatusOrcamento} {$pesquisaWhere} {$condicaoPesquisa}) AND T003_Flag_Status_Orcamento='{$statusOrcamento}'  {$extra} {$g['conf']->ini('extraSqlPadraoVisualizacaoClientes')} ORDER BY {$pesquisa['ordem']} LIMIT 0,100;
EOT;
}else{
	$sql = <<<EOT
		SELECT T003_Id, T005_Id, D024_Id, D024_Nome_Fantasia, T003_Valor_Total_Produtos,T003_Flag_Status_Orcamento,T003_Flag_PDF_Impresso, T003_Valor_Total_Servicos, T003_Valor_Troca, T003_Valor_Total, T003_Data_Emissao, Vendedor.C007_Primeiro_Nome
		FROM T003
		left join D024 on D024_Id=T003_D024_Id
		left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
		left join C007 as Externo on Externo.C007_Id=D024_C007_Vendedor_Externo
		left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		left join C007 as Supervisor2 on Supervisor2.C007_Id=Vendedor.C007_C007_Id_2
		left join C007 as Substituto on Substituto.C007_Id=Vendedor.C007_Id_Vendedor_Substituto
		LEFT JOIN T005 ON T005_T003_Id = T003_Id
		WHERE T003_C004_Id={$g['empresaAtual']} AND {$filtro} {$filtroStatusOrcamento} {$pesquisaWhere} {$condicaoPesquisa} {$g['conf']->ini('extraSqlPadraoVisualizacaoClientes')} ORDER BY {$pesquisa['ordem']} LIMIT 0,100;
EOT;
}

$res = mysql_query($sql);
while ($row = mysql_fetch_assoc($res)) {
	$rowT005 = array();
	$row['T003_Valor_Total_Produtos'] = gCorrigeNumero($row['T003_Valor_Total_Produtos']);
	$row['T003_Valor_Total_Servicos'] = gCorrigeNumero($row['T003_Valor_Total_Servicos']);
	$row['T003_Valor_Troca'] = gCorrigeNumero($row['T003_Valor_Troca']);
	$row['T003_Valor_Total'] = gCorrigeNumero($row['T003_Valor_Total']);
	$row['T003_Data_Emissao'] = gCorrigeData($row['T003_Data_Emissao']);
	//$row['D024_Nome_Fantasia'] = substr($row['D024_Nome_Fantasia'];
	$iconeStatus = "<img src=\"{$imageIcones['preto']}\" />";
	$href = <<<EOT
		<a href="./orcamento.php?T003_Id={$row['T003_Id']}" data-ajax="false" data-transition="slide">
EOT;
	$nomeCliente = $row['D024_Nome_Fantasia'];
	if($row['T003_Flag_Status_Orcamento'] == "L"){
		$iconeStatus = "<img src=\"{$imageIcones['azul']}\" />";
	}elseif($row['T003_Flag_Status_Orcamento'] == "N"){
		$iconeStatus = "<img src=\"{$imageIcones['vermelho']}\" />";
	}elseif($row['T003_Flag_Status_Orcamento'] == "A" or $row['T003_Flag_Status_Orcamento'] == 'F') {
		$iconeStatus = "<img src=\"{$imageIcones['cinza']}\" />";
		$sqlT005 = "SELECT * FROM T005 WHERE T005_T003_Id = '{$row['T003_Id']}';";
		$resT005 = mysql_query($sqlT005);
		$rowT005 = mysql_fetch_assoc($resT005);
        if ($rowT005['T005_Id']>0) {
    		$href = <<<EOT
				<a onclick="\$.mobile.changePage('./pedido/pedido.php?T005_Id={$rowT005['T005_Id']}', {transition: 'slide'});">
EOT;
		    $nomeCliente = "{$row['D024_Nome_Fantasia']}";
		    if($rowT005['T005_Flag_Status_Liberacao'] == '1'){
				$iconeStatus = "<img src=\"{$imageIcones['azul']}\" />";
			} else if($rowT005['T005_Flag_Status_Liberacao'] == '2'){
				$iconeStatus = "<img src=\"{$imageIcones['verde']}\" />";
			} else if($rowT005['T005_Flag_Status_Liberacao'] == '3'){
				$iconeStatus = "<img src=\"{$imageIcones['vermelho']}\" />";
			}
        } else {
		    if (empty($row['T003_Flag_PDF_Impresso']) or $row['T003_Flag_PDF_Impresso'] == "N") {
			    $iconeStatus = "<img src=\"{$imageIcones['verde']}\" />";
		    }
		}
	} elseif($row['T003_Flag_Status_Orcamento'] == 'V') {
		$iconeStatus = "<img src=\"{$imageIcones['laranja']}\" />";
	}
	if (empty($row['D024_Nome_Fantasia']))
	{
		$row['D024_Nome_Fantasia']="<font style='weight:bold;'>\"NÃO INFORMADO\"</font>";
	}
	if ($C007['C007_Flag_Vendedor']=='D' OR $C007['C007_Flag_Vendedor']=='S') {
		// GRID: Diretor
		echo <<<EOT
			<li data-theme="c" data-icon="false">
		    {$href}
	    		<table style="width: 100%;">
	    			<tr>
	    				<td style="width: 2%;">{$iconeStatus}</td>
	    				<td style="width: 8%;" align="left">{$row['T003_Data_Emissao']}</td>
	    				<td style="width: 8%;" align="center">{$row['T003_Id']}</td>
	    				<td style="width: 8%;" align="center">{$rowT005['T005_Id']}</td>
	    				<td style="width: 8%;" align="center">{$row['D024_Id']}</td>
	    				<td style="width: 36%;">{$nomeCliente}</td>
	    				<td style="width: 11%; {$exibirServicos}" align="right">{$row['T003_Valor_Total_Servicos']}</td>
	    				<td style="width: 10%; {$exibirTrocas}" align="right">{$row['T003_Valor_Troca']}</td>
	    				<td style="width: 10%;" align="right">{$row['T003_Valor_Total']}</td>
	    				<td style="width: 16%;" align="center">{$row['C007_Primeiro_Nome']}</td>
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
	    		<table style="width: 100%;">
	    			<tr>
	    				<td style="width: 2%;">{$iconeStatus}</td>
	    				<td style="width: 7%" align="left">{$row['T003_Data_Emissao']}</td>
	    				<td style="width: 6%" align="center">{$row['T003_Id']}</td>
	    				<td style="width: 6%" align="center">{$rowT005['T005_Id']}</td>
	    				<td style="width: 6%" align="center">{$row['D024_Id']}</td>
	    				<td style="width: 33%">{$nomeCliente}</td>
	    				<td style="width: 10%; {$exibirServicos}" align="right">{$row['T003_Valor_Total_Servicos']}</td>
	    				<td style="width: 10%; {$exibirTrocas}" align="right">{$row['T003_Valor_Troca']}</td>
	    				<td style="width: 12%" align="right">{$row['T003_Valor_Total']}</td>
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

//