<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');


//Parametro de Pesquisa e Coluna da Pesquisa selecionada em orçamentos
$arqFiltro = $g['pathDadosUsuario'] . 'tabletFiltro/contasPagar';
if (!file_exists($arqFiltro)) {
	$pesquisa = array('busca' => '', 'ordem' => 'T015_Data_Emissao DESC', 'colunaBusca' => '', 'tipoOrdenacao' => '', 'coluna' => '', 'filtroPagar' => '');
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

if (isset($_REQUEST['filtroPagar'])) {
	$pesquisa['filtroPagar'] = $_REQUEST['filtroPagar'];
    file_put_contents($arqFiltro, serialize($pesquisa));
}

$uniqId = uniqid();

//Gera o Combo para pesquisa
$comboPesquisa = '';
$comboPesquisa .= '<option value=""'.(($pesquisa['colunaBusca'] == '') ? ' selected="selected"' : '').'> Geral </option>';
$comboPesquisa .= '<option value="D024_Nome_Empresa"'.(($pesquisa['colunaBusca'] == 'D024_Nome_Empresa') ? ' selected="selected"' : '').'> Fornecedor</option>';
$comboPesquisa .= '<option value="T015_Numero_Duplicata"'.(($pesquisa['colunaBusca'] == 'T015_Numero_Duplicata') ? ' selected="selected"' : '').'> Dupl. </option>';
$comboPesquisa .= '<option value="T015_Data_Emissao"'.(($pesquisa['colunaBusca'] == 'T015_Data_Emissao') ? ' selected="selected"' : '').'> Data Emissão </option>';
$comboPesquisa .= '<option value="D031_Portador"'.(($pesquisa['colunaBusca'] == 'D031_Portador') ? ' selected="selected"' : '').'> Portador </option>';
$comboPesquisa .= '<option value="D014_SubConta"'.(($pesquisa['colunaBusca'] == 'D014_SubConta') ? ' selected="selected"' : '').'> Conta </option>';
$comboPesquisa .= '<option value="T015_Data_Vencimento"'.(($pesquisa['colunaBusca'] == 'T015_Data_Vencimento') ? ' selected="selected"' : '').'> Data Vencimento </option>';
$comboPesquisa .= '<option value="T015_Data_Pagamento"'.(($pesquisa['colunaBusca'] == 'T015_Data_Pagamento') ? ' selected="selected"' : '').'> Data Pagamento </option>';

//Gera o Combo para Filtro de Contas a Receber (Todas/Contas Recebidas/Contas Não Recebidas)
$comboFiltroPagar = '';
$comboFiltroPagar .= '<option value="Todas"'.(($pesquisa['filtroPagar'] == 'Todas') ? ' selected="selected"' : '').'> Todas </option>';
$comboFiltroPagar .= '<option value="Pagas"'.(($pesquisa['filtroPagar'] == 'Pagas') ? ' selected="selected"' : '').'> Pagas </option>';
$comboFiltroPagar .= '<option value="NaoPagas"'.(($pesquisa['filtroPagar'] == 'NaoPagas') ? ' selected="selected"' : '').'> Não Pagas</option>';




include('mobile/tablet/inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageContasPagar">
EOT;
$etapa = 11;
include('mobile/tablet/inc.header.bar.php');

//Verifica Se Foi selecionado um critério para pesquisa
if($pesquisa['colunaBusca']!=''){
	//Verifica Se Foi selecionado um critério tipo data
	if($pesquisa['colunaBusca']=='T015_Data_Emissao' || $pesquisa['colunaBusca']=='T015_Data_Vencimento' || $pesquisa['colunaBusca']=='T015_Data_Pagamento' ){
		$condicaoPesquisa = "AND date_format({$pesquisa['colunaBusca']},'%d/%m/%Y') like '%{$pesquisa['busca']}%'";
	}else{
		$condicaoPesquisa = "AND {$pesquisa['colunaBusca']} LIKE '%{$pesquisa['busca']}%'";
	}
}else{
	$condicaoPesquisa = "AND (date_format(T015_Data_Emissao,'%d/%m/%Y') LIKE '%{$pesquisa['busca']}%' OR date_format(T015_Data_Vencimento,'%d/%m/%Y') LIKE '%{$pesquisa['busca']}%' OR D024_Nome_Empresa LIKE '%{$pesquisa['busca']}%' OR D031_Portador LIKE '%{$pesquisa['busca']}%' OR C007_Primeiro_Nome LIKE '%{$pesquisa['busca']}%')";
}

if(!empty($pesquisa['filtroPagar']) && $pesquisa['filtroPagar']!="Todas"){
	if($pesquisa['filtroPagar']=="Pagas"){
		$condicaoPesquisa .= "AND (T015_Data_Pagamento > 0 AND T015_Data_Pagamento IS NOT NULL)";
	}else{
		$condicaoPesquisa .= "AND (T015_Data_Pagamento = 0)";
	}
}else{
	$condicaoPesquisa .= "";
}

$extra="WHERE ((1=1)) {$condicaoPesquisa} AND (T015_C004_Id={$g['empresaAtual']}) ORDER BY {$pesquisa['ordem']} LIMIT 0,100;";
$sql = <<<EOT
	SELECT 
		D024_Id, D024_Nome_Empresa, D014_SubConta, T015_Numero_Duplicata, T015_Data_Emissao, datediff(T015_Data_Vencimento,T015_Data_Emissao) Prazo, T015_Data_Vencimento, T015_Data_Pagamento, T015_Valor_Duplicata, T015_Valor_Juros, T015_Valor_Desconto, T015_Valor_Pago, T015_Valor_Total, D031_Portador
	FROM T015
		left join D024 on D024_Id=T015_D024_Id
		left join D073 on D073_Id=T015_D073_Id
		left join D031 on D031_Id=T015_D031_Id
		left join D014 on D014_Id=T015_D014_Id
		left join T007 on T007_Id=T015_T007_Id
		left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
	{$extra}
EOT;



$sqlTotalizadores =  <<<EOT
	SELECT 
		SUM(T015_Valor_Pago) AS ValorTotalPago,
		SUM(if(T015_Data_Pagamento='0000-00-00' and T015_Data_Vencimento<curdate(),T015_Valor_Total,0)) AS ValorTotalVencido,
		SUM(if(T015_Data_Pagamento='0000-00-00' and T015_Data_Vencimento>=curdate(),T015_Valor_Total,0)) AS ValorAVencer,
		SUM(T015_Valor_Total) AS ValorTotalAPagar,
		COUNT(*) AS TotalTitulos
	FROM T015
		left join D024 on D024_Id=T015_D024_Id
		left join D073 on D073_Id=T015_D073_Id
		left join D031 on D031_Id=T015_D031_Id
		left join D014 on D014_Id=T015_D014_Id
		left join T007 on T007_Id=T015_T007_Id
		left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
	{$extra}
EOT;

$resTotalizadores = mysql_query($sqlTotalizadores);
$rowTotalizadores = mysql_fetch_assoc($resTotalizadores);

$totalTitulos = $rowTotalizadores['TotalTitulos'];
$totalAPagar = gCorrigeNumero($rowTotalizadores['ValorTotalAPagar']);
$totalAVencer = gCorrigeNumero($rowTotalizadores['ValorAVencer']);
$totalVencido = gCorrigeNumero($rowTotalizadores['TotalAReceber']);
$totalPago = gCorrigeNumero($rowTotalizadores['ValorTotalPago']);
$totalGeral = gCorrigeNumero($rowTotalizadores['ValorTotalAPagar'] + $rowTotalizadores['ValorAVencer'] + $rowTotalizadores['TotalAReceber'] + $rowTotalizadores['ValorTotalPago']);

echo <<<EOT
		<div data-role="content">
        	<div class="ui-grid-b" style="margin-top: -10px; margin-bottom: 0px;">
                <div class="ui-block-a" style="width: 15%;">
                    <div style="float:left;">
                    	<a data-role="button" data-transition="slide" data-icon="delete" data-iconpos="left" onclick="\$.mobile.changePage( './login.php?logout=true', { transition: 'fade'} )" data-ajax="false">
			            	Sair
				        </a>
				    </div>
                </div>
	            <div class="ui-block-b" style="width: 85%;">  
	           		<form id="formPesquisa" name="formPesquisa" method="get" action="contasPagar.php" data-transition="fade" data-ajax="false">   		
	            		<div style="width: 38%; float:left; margin-top: 8px; margin-bottom: 5px;">
                    		<input name="pesquisa" placeholder="Procurar" data-type="search" class="ui-input-text ui-body-c" value="{$pesquisa['busca']}" id="procurarContasPagar">
                    	</div>
                    	<div style="float:left;">
                    		<select name="colunaPesquisa" id="colunaPesquisa" data-inline="true">
						     	{$comboPesquisa}
						   </select>
					    </div>
                    	<div style="float:left;">
					        <input type="submit" value="Procurar" data-role="button" data-transition="slide" data-icon="search" data-iconpos="left" />
					       <!-- <a data-role="button" data-transition="slide" data-icon="search" data-iconpos="left" onclick="\$.mobile.changePage( './contasReceber.php', {transition: 'fade', type:'post', data: 'T007_Id={$T007_Id}&colunaPesquisa=' + encodeURIComponent($('#colunaPesquisa').val()) + '&pesquisa=' + encodeURIComponent($('#procurarNota_FiscalLista').val())} )" >
			            		Procurar
					        </a>-->					        
					    </div>
					    <div style="float:right;">
                    		<select name="filtroPagar" id="filtroPagar" data-inline="true">
						     	{$comboFiltroPagar}
						   </select>
					    </div>
					 </form>
	            </div>
		    </div>
		    <div class="ui-grid-e" style="margin: 0px 0 5px 0; border: 1px solid #CCCCCC; box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3); padding: 8px 0;">
                    <div class="ui-block-a" style="border-right: 2px solid #CCCCCC; text-align: center; width:15.66%; float: left;">
                        <h4 style="margin: 0px;">
                            Títulos
                        </h4>
                        <h4 style="margin: 0px;">
                            {$totalTitulos}
                        </h4>
                    </div>
		    		<div class="ui-block-b" style="border-right: 2px solid #CCCCCC; text-align: center; width:16.66%; float: left;">
                        <h4 style="margin: 0px;">
                            Pago
                        </h4>
                        <h4 style="margin: 0px;">
                            {$totalPago}
                        </h4>
                    </div>
                    <div class="ui-block-c" style="border-right: 2px solid #CCCCCC; text-align: center; width:16.66%; float: left;">
                        <h4 style="margin: 0px;">
                            Vencido
                        </h4>
                        <h4 style="margin: 0px;">
                           {$totalVencido}
                        </h4>
                    </div>
                     <div class="ui-block-d" style="border-right: 2px solid #CCCCCC; text-align: center; width:16.66%; float: left;">
                        <h4 style="margin: 0px;">
                            A Vencer
                        </h4>
                        <h4 style="margin: 0px;">
                           {$totalAVencer}
                        </h4>
                    </div>
                     <div class="ui-block-e" style="border-right: 2px solid #CCCCCC; text-align: center; width:16.66%; float: left;">
                        <h4 style="margin: 0px;">
                            Total Pagar
                        </h4>
                        <h4 style="margin: 0px;">
                           {$totalAPagar}
                        </h4>
                    </div>
                    <div class="ui-block-f" style="text-align: center; width:16.66%; float: left;">
                        <h4 style="margin: 0px;">
                            Total Geral
                        </h4>
                        <h4 style="margin: 0px;">
                           {$totalGeral}
                        </h4>
                    </div>
                </div>   
                <ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 1px;">
                    <li data-role="list-divider" role="heading">
	    				<table style="width: 100%";>
	    					<tr>
EOT;

		//Verifica Ordenação da Coluna pelo clique do usuário DESC ou ASC
		$D024_Nome_Empresa = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D024_Nome_Empresa' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$D014_SubConta = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D014_SubConta' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$T015_Numero_Duplicata = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T015_Numero_Duplicata' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$T015_Data_Emissao = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T015_Data_Emissao' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$Prazo = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'Prazo' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$Atraso = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'Atraso' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$T015_Valor_Total = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T015_Valor_Total' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';

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
		$D024_Nome_Empresa_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D024_Nome_Empresa') ? $iconeOrdenacao : '';
		$D014_SubConta_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D014_SubConta') ? $iconeOrdenacao : '';
		$T015_Numero_Duplicata_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T015_Numero_Duplicata') ? $iconeOrdenacao : '';
		$T015_Data_Emissao_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T015_Data_Emissao') ? $iconeOrdenacao : '';
		$Prazo_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'Prazo') ? $iconeOrdenacao : '';
		$Atraso_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'Atraso') ? $iconeOrdenacao : '';
		$T015_Valor_Total_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T015_Valor_Total') ? $iconeOrdenacao : '';
		
		if ($C007['C007_Flag_Vendedor']=='D' OR $C007['C007_Flag_Vendedor']=='S') {
		// GRID: Diretor
		
		echo <<<EOT
	    						<td style="width: 38%"; align="left";><a href="contasPagar.php?coluna=D024_Nome_Empresa&tipoOrdenacao={$D024_Nome_Empresa}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none; padding-bottom:0px;" data-transition="fade">Fornecedor{$D024_Nome_Empresa_Icone}</a></td>
	    						<td style="width: 8%"; align="left";><a href="contasPagar.php?coluna=D014_SubConta&tipoOrdenacao={$D014_SubConta}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none; padding-bottom:0px;" data-transition="fade">Conta{$D014_SubConta_Icone}</a></td>
	    						<td style="width: 9%"; align="center";><a href="contasPagar.php?coluna=T015_Numero_Duplicata&tipoOrdenacao={$T015_Numero_Duplicata}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none; padding-bottom:0px;" data-transition="fade">Dupl.{$T015_Numero_Duplicata_Icone}</a></td>
	    						<td style="width: 20%"; align="left"><a href="contasPagar.php?coluna=T015_Data_Emissao&tipoOrdenacao={$T015_Data_Emissao}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Data{$T015_Data_Emissao_Icone}</a></td>
	    						<td style="width: 7%"; align="center"><a href="contasPagar.php?coluna=Prazo&tipoOrdenacao={$Prazo}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Prz{$Prazo_Icone}</a></td>
	    						<td style="width: 18%"; align="left"><a href="contasPagar.php?coluna=T015_Valor_Total&tipoOrdenacao={$T015_Valor_Total}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Valores{$T015_Valor_Total_Icone}</a></td>
EOT;
		}
	echo <<<EOT
							</tr>
	    				</table>
                    </li>
EOT;


$res = mysql_query($sql);
while ($row = mysql_fetch_assoc($res)) {
	$row['D027_Portador'] = $row['D027_Portador']!="" ? $row['D027_Portador'] : " - ";
	$row['T015_Numero_Duplicata'] = $row['T015_Numero_Duplicata']!="" ? $row['T015_Numero_Duplicata'] : " - ";
	$row['T015_Data_Emissao'] = gCorrigeData($row['T015_Data_Emissao']);
	$row['T015_Data_Vencimento'] = gCorrigeData($row['T015_Data_Vencimento']);
	$row['T015_Data_Pagamento'] = gCorrigeData($row['T015_Data_Pagamento']);
	$row['T015_Valor_Duplicata'] = gCorrigeNumero($row['T015_Valor_Duplicata']);
	$row['T015_Valor_Juros'] = gCorrigeNumero($row['T015_Valor_Juros']);
	$row['T015_Valor_Recebido'] = gCorrigeNumero($row['T015_Valor_Recebido']);
	$row['T015_Valor_Desconto'] = gCorrigeNumero($row['T015_Valor_Desconto']);
	$row['T015_Valor_Total'] = gCorrigeNumero($row['T015_Valor_Total']);
	if ($C007['C007_Flag_Vendedor']=='D' OR $C007['C007_Flag_Vendedor']=='S') {
		// GRID: Diretor
		echo <<<EOT
			<li data-theme="c" data-icon="false">
		    {$href}
	    		<table style="width: 100%; {$corLinha}">
	    			<tr>
	    				<td style="width: 38%"; align="left"  valign="top">{$row['D024_Nome_Empresa']} ({$row['D024_Id']})</td>
	    				<td style="width: 8%"; align="center"  valign="top">{$row['Atraso']}</td>
	    				<td style="width: 9%"; align="center"  valign="top">{$row['T015_Numero_Duplicata']}</td>
	    				<td style="width: 20%"; align="left"  valign="top">
	    					Emissão: <b>{$row['T015_Data_Emissao']}</b> <br/>
	    					Vencimento: <b>{$row['T015_Data_Vencimento']}</b> <br/>
	    					Pagamento: <b>{$row['T015_Data_Pagamento']}</b>
	    				</td>
	    				<td style="width: 7%"; align="center"  valign="top">{$row['Prazo']}</td>
	    				<td style="width: 18%"; align="left"  valign="top">
	    					Valor: <b>{$row['T015_Valor_Duplicata']}</b> <br/>
	    					Juros: <b>{$row['T015_Valor_Juros']}</b> <br/>
	    					Desconto: <b>{$row['T015_Valor_Desconto']}</b> <br/>
	    					Pago: <b>{$row['T015_Valor_Recebido']}</b> <br/>
	    					<i><u>Total: <b>{$row['T015_Valor_Total']}</b><u></i><br/>	
	    				</td>
	    			</tr>
	    			<tr>
	    				<td style="width: 13%"; align="left"  valign="top"><b>Portador:</b> {$row['D027_Portador']}</td>
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
    	$( document ).undelegate("#pageContasPagar", "pageinit");
    	$( document ).delegate("#pageContasPagar", "pageinit", function() {
    		$("#filtroPagar").bind("change", function() { 
    			$("#formPesquisa").submit();
    		});
EOT;
include('inc.swipe.events.php');
echo <<<EOT
    	});
    </script>
EOT;
