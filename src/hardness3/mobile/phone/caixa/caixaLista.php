<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

//Parametros de Pesquisa e Coluna da Pesquisa selecionada em requisições
$arqFiltro = $g['pathDadosUsuario'] . 'tabletFiltro/caixalista';
if (!file_exists($arqFiltro)) {
	$pesquisa = array('busca' => '', 'ordem' => 'T005_Id DESC', 'colunaBusca' => '', 'tipoOrdenacao' => '', 'coluna' => '');
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
$comboPesquisa .= '<option value="D024_Nome_Empresa"'.(($pesquisa['colunaBusca'] == 'D024_Nome_Empresa') ? ' selected="selected"' : '').'> Cliente </option>';
$comboPesquisa .= '<option value="C007_Primeiro_Nome"'.(($pesquisa['colunaBusca'] == 'C007_Primeiro_Nome') ? ' selected="selected"' : '').'> Vendedor </option>';
$comboPesquisa .= '<option value="T005_Id"'.(($pesquisa['colunaBusca'] == 'T005_Id') ? ' selected="selected"' : '').'> Pedido </option>';
$comboPesquisa .= '<option value="T005_Data_Emissao"'.(($pesquisa['colunaBusca'] == 'T005_Data_Emissao') ? ' selected="selected"' : '').'> Data </option>';
$confCaixaLista = true;

//Parametro para ordernar a lista de orçamentos, conforme a coluna selecionada da tabela
$colunaSelecionada = isset($_REQUEST['coluna']) ? $_REQUEST['coluna'] : 'T005_Id';
$tipoOrdenacao = isset($_REQUEST['tipoOrdenacao']) ? $_REQUEST['tipoOrdenacao'] : 'DESC';    

include('mobile/tablet/inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageCaixaLista">
EOT;
$etapa = 6;
include('mobile/tablet/inc.header.bar.php');
echo <<<EOT
		<div data-role="content">
        	<div class="ui-grid-b" style="margin-top: 7px; margin-bottom: 5px;">
                <div class="ui-block-a" style="width: 20%;">
                    <div style="float:left;">
                    	<a data-role="button" data-transition="slide" data-icon="delete" data-iconpos="left" onclick="\$.mobile.changePage( '../login.php?logout=true', { transition: 'fade'} )" data-ajax="false">
			            	Sair
				        </a>
				    </div>
                </div>
	            <div class="ui-block-b" style="width: 60%;">     		
						<div style="width: 48%; float:left; margin-top: 8px; margin-bottom: 5px;">
                    		<input placeholder="Procurar Pedido" data-type="search" class="ui-input-text ui-body-c" value="{$pesquisa['busca']}" id="procurarOrcamentoLista">
                    	</div>
                    	<div style="float:left;">
                    		<select name="colunaPesquisa" id="colunaPesquisa" data-inline="true">
						     	{$comboPesquisa}
						   </select>
					    </div>
                    	<div style="float:left;">
					        <a data-role="button" data-transition="slide" data-icon="search" data-iconpos="left" onclick="\$.mobile.changePage( './caixaLista.php', {transition: 'fade', type:'post', data: 'T005_Id={$T005_Id}&colunaPesquisa=' + encodeURIComponent($('#colunaPesquisa').val()) + '&pesquisa=' + encodeURIComponent($('#procurarOrcamentoLista').val())} )" >
			            		Procurar
					        </a>
					    </div>
	            </div>
	        </div>
                <ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 1px;">
                    <li data-role="list-divider" role="heading">
	    				<table style="width: 100%";>
	    					<tr>
EOT;

		//Verifica Ordenação da Coluna pelo clique do usuário DESC ou ASC
		$T005_Data_Emissao = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T005_Data_Emissao' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$T005_Id = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T005_Id' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$D024_Id = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D024_Id' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$D024_Nome_Empresa = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D024_Nome_Empresa' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$T005_Valor_Total_Produtos = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T005_Valor_Total_Produtos' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$T005_Valor_Total_Servicos = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T005_Valor_Total_Servicos' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$T005_Valor_Troca = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T005_Valor_Troca' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$T005_Valor_Total = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T005_Valor_Total' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$C007_Primeiro_Nome = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'C007_Primeiro_Nome' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
	
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
		$T005_Data_Emissao_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T005_Data_Emissao') ? $iconeOrdenacao : '';
		$T005_Id_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T005_Id') ? $iconeOrdenacao : '';
		$D024_Id_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D024_Id') ? $iconeOrdenacao : '';
		$D024_Nome_Empresa_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D024_Nome_Empresa') ? $iconeOrdenacao : '';
		$T005_Valor_Total_Produtos_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T005_Valor_Total_Produtos') ? $iconeOrdenacao : '';
		$T005_Valor_Total_Servicos_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T005_Valor_Total_Servicos') ? $iconeOrdenacao : '';
		$T005_Valor_Troca_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T005_Valor_Troca') ? $iconeOrdenacao : '';
		$T005_Valor_Total_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T005_Valor_Total') ? $iconeOrdenacao : '';
		$C007_Primeiro_Nome_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'C007_Primeiro_Nome') ? $iconeOrdenacao : '';
		
		if ($C007['C007_Flag_Vendedor'] == 'D') {
		// GRID: Diretor
		
		echo <<<EOT
	    						<td style="width: 7%"; align="center";><a href="caixaLista.php?coluna=T005_Data_Emissao&tipoOrdenacao={$T005_Data_Emissao}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Data{$T005_Data_Emissao_Icone}</a></td>
	    						<td style="width: 8%"; align=center><a href="caixaLista.php?coluna=T005_Id&tipoOrdenacao={$T005_Id}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Nº{$T005_Id_Icone}</a></td>
	    						<td style="width: 5%"; align="center"><a href="caixaLista.php?coluna=D024_Id&tipoOrdenacao={$D024_Id}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Cód{$D024_Id_Icone}</a></td>
	    						<td style="width: 28%";><a href="caixaLista.php?coluna=D024_Nome_Empresa&tipoOrdenacao={$D024_Nome_Empresa}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Cliente{$D024_Nome_Empresa_Icone}</td>
	    						<td style="width: 12%"; align="right"><a href="caixaLista.php?coluna=T005_Valor_Total_Produtos&tipoOrdenacao={$T005_Valor_Total_Produtos}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Produtos R$ {$T005_Valor_Total_Produtos_Icone}</a></td>
	    						<td style="width: 11%"; align="right"><a href="caixaLista.php?coluna=T005_Valor_Total_Servicos&tipoOrdenacao={$T005_Valor_Total_Servicos}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Serviços R$ {$T005_Valor_Total_Servicos_Icone}</a></td>
	    						<td style="width: 10%"; align="right"><a href="caixaLista.php?coluna=T005_Valor_Troca&tipoOrdenacao={$T005_Valor_Troca}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Trocas R$ {$T005_Valor_Troca_Icone}</a></td>
	    						<td style="width: 9%"; align="right"><a href="caixaLista.php?coluna=T005_Valor_Total&tipoOrdenacao={$T005_Valor_Total}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Valor R$ {$T005_Valor_Total_Icone}</a></td>
	    						<td style="width: 16%"; align="center"><a href="caixaLista.php?coluna=C007_Primeiro_Nome&tipoOrdenacao={$C007_Primeiro_Nome}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Vendedor{$C007_Primeiro_Nome_Icone}</a></td>
EOT;
	} else {
		// GRID: Vendedor
		echo <<<EOT
	    						<td style="width: 7%"; align="center"><a href="caixaLista.php?coluna=T005_Data_Emissao&tipoOrdenacao={$T005_Data_Emissao}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Data{$T005_Data_Emissao_Icone}</a></td>
	    						<td style="width: 8%"; align=center><a href="caixaLista.php?coluna=T005_Id&tipoOrdenacao={$T005_Id}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Nº{$T005_Id_Icone}</a></td>
	    						<td style="width: 8%"; align="center"><a href="caixaLista.php?coluna=D024_Id&tipoOrdenacao={$D024_Id}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Cód{$D024_Id_Icone}</a></td>
	    						<td style="width: 33%";><a href="caixaLista.php?coluna=D024_Nome_Empresa&tipoOrdenacao={$D024_Nome_Empresa}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Cliente{$D024_Nome_Empresa_Icone}</td>
	    						<td style="width: 12%"; align="right"><a href="caixaLista.php?coluna=T005_Valor_Total_Produtos&tipoOrdenacao={$T005_Valor_Total_Produtos}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Produtos R$ {$T005_Valor_Total_Produtos_Icone}</a></td>
	    						<td style="width: 12%"; align="right"><a href="caixaLista.php?coluna=T005_Valor_Total_Servicos&tipoOrdenacao={$T005_Valor_Total_Servicos}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Serviços R$ {$T005_Valor_Total_Servicos_Icone}</a></td>
	    						<td style="width: 10%"; align="right"><a href="caixaLista.php?coluna=T005_Valor_Troca&tipoOrdenacao={$T005_Valor_Troca}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Trocas R$ {$T005_Valor_Troca_Icone}</a></td>
	    						<td style="width: 12%"; align="right"><a href="caixaLista.php?coluna=T005_Valor_Total&tipoOrdenacao={$T005_Valor_Total}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Valor R$ {$T005_Valor_Total_Icone}</a></td>
EOT;
	}
	echo <<<EOT
							</tr>
	    				</table>
                    </li>
EOT;

// Pedidos
$pesquisaWhere = "(T005_Flag_Esta_No_Caixa = 'S') and (T005_Id LIKE '%{$pesquisa['busca']}%' OR D024_Nome_Empresa LIKE '%{$pesquisa['busca']}%' OR DATE_FORMAT(T005_Data_Emissao,'%d/%m/%Y') LIKE '%{$pesquisa['busca']}%' OR C007_Primeiro_Nome LIKE '%{$pesquisa['busca']}%')";

//Verifica Se Foi selecionado um critério para pesquisa
if($pesquisa['colunaBusca']!=''){
	//Verifica Se Foi selecionado um critério tipo data
	if($pesquisa['colunaBusca']=='T005_Data_Emissao'){
		$condicaoPesquisa = "AND date_format({$pesquisa['colunaBusca']},'%d/%m/%Y') like '%{$pesquisa['busca']}%'";
	}else if($pesquisa['colunaBusca']=='T005_Id'){
		$condicaoPesquisa = "AND {$pesquisa['colunaBusca']} = '{$pesquisa['busca']}'";
	}else{
		$condicaoPesquisa = "AND {$pesquisa['colunaBusca']} LIKE '%{$pesquisa['busca']}%'";
	}
}else{
	$condicaoPesquisa = "";
}
//Se for selecionado filtro por Status do Pedido
if($statusOrcamento!='' && $statusOrcamento!='X'){
	$extra = '';
	$sql = <<<EOT
		SELECT T005_Id, D024_Id, D024_Nome_Empresa, T005_Valor_Total_Produtos, T005_Valor_Total_Servicos, T005_Valor_Troca, T005_Valor_Total, T005_Data_Emissao, C007_Primeiro_Nome
		FROM T005 
		left join D024 on D024_Id=T005_D024_Id 
		left join C007 on C007_Id=T005_C007_Id_Vendedor_Interno
		WHERE (T005_C004_Id={$g['empresaAtual']} AND {$filtro} {$pesquisaWhere} {$condicaoPesquisa})  {$extra} ORDER BY {$pesquisa['ordem']} LIMIT 0,100;
EOT;
}else{
	$sql = <<<EOT
		SELECT T005_Id, D024_Id, D024_Nome_Empresa, T005_Valor_Total_Produtos, T005_Valor_Total_Servicos, T005_Valor_Troca, T005_Valor_Total, T005_Data_Emissao, C007_Primeiro_Nome
		FROM T005 
		left join D024 on D024_Id=T005_D024_Id 
		left join C007 on C007_Id=T005_C007_Id_Vendedor_Interno
		WHERE T005_C004_Id={$g['empresaAtual']} AND {$filtro} {$pesquisaWhere} {$condicaoPesquisa} ORDER BY {$pesquisa['ordem']} LIMIT 0,100;
EOT;
}
$res = mysql_query($sql);

while ($row = mysql_fetch_assoc($res)) {
	$row['T005_Valor_Total_Produtos'] = gCorrigeNumero($row['T005_Valor_Total_Produtos']);
	$row['T005_Valor_Total_Servicos'] = gCorrigeNumero($row['T005_Valor_Total_Servicos']);
	$row['T005_Valor_Troca'] = gCorrigeNumero($row['T005_Valor_Troca']);
	$row['T005_Valor_Total'] = gCorrigeNumero($row['T005_Valor_Total']);
	$row['T005_Data_Emissao'] = gCorrigeData($row['T005_Data_Emissao']);
	$row['D024_Nome_Empresa'] = substr($row['D024_Nome_Empresa'],0,27);
	$corLinha = "";
	$href = <<<EOT
		<a href="./caixa.php?T005_Id={$row['T005_Id']}" data-ajax="false" data-transition="slide">
EOT;
	$nomeCliente = $row['D024_Nome_Empresa'];
	if($row['T005_Flag_Status_Orcamento'] == "L"){
		$corLinha = "color:blue;";
	}elseif($row['T005_Flag_Status_Orcamento'] == "N"){
		$corLinha = "color:red;";
	}elseif($row['T005_Flag_Status_Orcamento'] == "A" or $row['T005_Flag_Status_Orcamento'] == 'F') {
		$corLinha = "color:#A1A1A1;";
		$sqlT005 = "SELECT T005_Id FROM T005 WHERE T005_T005_Id = '{$row['T005_Id']}';";
		$resT005 = mysql_query($sqlT005);
		$rowT005 = mysql_fetch_assoc($resT005);
        if ($rowT005['T005_Id']>0)
        {
    		$href = <<<EOT
	    		<a href="./caixa/pedido.php?T005_Id={$rowT005['T005_Id']}" data-ajax="false" data-transition="slide">
EOT;
		    $nomeCliente = "{$row['D024_Nome_Empresa']} ({$rowT005['T005_Id']})";
			
        }
        else
        {
		    if (empty($row['T005_Flag_PDF_Impresso']) or $row['T005_Flag_PDF_Impresso'] == "N") {
			    $corLinha = "color:green;";
		    }
		}
	} elseif($row['T005_Flag_Status_Orcamento'] == 'V') {
		$corLinha = "color:orange;";
	}
	if (empty($row['D024_Nome_Empresa']))
	{
		$row['D024_Nome_Empresa']="<font style='weight:bold;'>\"NÃO INFORMADO\"</font>";
	}
	if ($C007['C007_Flag_Vendedor'] == 'D') {
		// GRID: Diretor
		echo <<<EOT
			<li data-theme="c" data-icon="false">
		    {$href}
	    		<table style="width: 100%; {$corLinha}">
	    			<tr>
	    				<td style="width: 7%"; align="left">{$row['T005_Data_Emissao']}</td>
	    				<td style="width: 8%"; align="center">{$row['T005_Id']}</td>
	    				<td style="width: 5%"; align="center">{$row['D024_Id']}</td>
	    				<td style="width: 28%";>{$nomeCliente}</td>
	    				<td style="width: 12%"; align="right">{$row['T005_Valor_Total_Produtos']}</td>
	    				<td style="width: 11%"; align="right">{$row['T005_Valor_Total_Servicos']}</td>
	    				<td style="width: 10%"; align="right">{$row['T005_Valor_Troca']}</td>
	    				<td style="width: 9%"; align="right">{$row['T005_Valor_Total']}</td>
	    				<td style="width: 16%"; align="center">{$row['C007_Primeiro_Nome']}</td>
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
	    				<td style="width: 7%"; align="left">{$row['T005_Data_Emissao']}</td>
	    				<td style="width: 8%"; align="center">{$row['T005_Id']}</td>
	    				<td style="width: 8"; align="center">{$row['D024_Id']}</td>
	    				<td style="width: 33%";>{$nomeCliente}</td>
	    				<td style="width: 12%"; align="right">{$row['T005_Valor_Total_Produtos']}</td>
	    				<td style="width: 10%"; align="right">{$row['T005_Valor_Total_Servicos']}</td>
	    				<td style="width: 10%"; align="right">{$row['T005_Valor_Troca']}</td>
	    				<td style="width: 12%"; align="right">{$row['T005_Valor_Total']}</td>
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
echo <<<EOT
    </div>
    <script>
    	setTimeout("location.reload();",30000 ); 
    	$( document ).undelegate("#pageCaixaLista", "pageinit");
    	$( document ).delegate("#pageCaixaLista", "pageinit", function() {
  
EOT;
echo <<<EOT
    	});
    </script>
EOT;
