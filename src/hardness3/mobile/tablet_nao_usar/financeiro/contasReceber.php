<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');


//Parametro de Pesquisa e Coluna da Pesquisa selecionada em orçamentos
$arqFiltro = $g['pathDadosUsuario'] . 'tabletFiltro/contasReceber';
if (!file_exists($arqFiltro)) {
	$pesquisa = array('busca' => '', 'ordem' => 'T002_Data_Emissao DESC', 'colunaBusca' => '', 'tipoOrdenacao' => '', 'coluna' => '', 'filtroReceber' => '');
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
if (isset($_REQUEST['filtroReceber'])) {
	$pesquisa['filtroReceber'] = $_REQUEST['filtroReceber'];
    file_put_contents($arqFiltro, serialize($pesquisa));
}

$uniqId = uniqid();

//Gera o Combo para pesquisa
$comboPesquisa = '';
$comboPesquisa .= '<option value=""'.(($pesquisa['colunaBusca'] == '') ? ' selected="selected"' : '').'> Geral </option>';
$comboPesquisa .= '<option value="D024_Nome_Empresa"'.(($pesquisa['colunaBusca'] == 'D024_Nome_Empresa') ? ' selected="selected"' : '').'> Cliente </option>';
$comboPesquisa .= '<option value="T002_Numero_Duplicata"'.(($pesquisa['colunaBusca'] == 'T002_Numero_Duplicata') ? ' selected="selected"' : '').'> N.Dupl. </option>';
$comboPesquisa .= '<option value="T002_Data_Emissao"'.(($pesquisa['colunaBusca'] == 'T002_Data_Emissao') ? ' selected="selected"' : '').'> Data Emissão </option>';
$comboPesquisa .= '<option value="D027_Portador"'.(($pesquisa['colunaBusca'] == 'D027_Portador') ? ' selected="selected"' : '').'> Portador </option>';
$comboPesquisa .= '<option value="C007_Primeiro_Nome"'.(($pesquisa['colunaBusca'] == 'C007_Primeiro_Nome') ? ' selected="selected"' : '').'> Vendedor </option>';
$comboPesquisa .= '<option value="T002_Data_Vencimento"'.(($pesquisa['colunaBusca'] == 'T002_Data_Vencimento') ? ' selected="selected"' : '').'> Data Vencimento </option>';
$comboPesquisa .= '<option value="T002_Data_Recebimento"'.(($pesquisa['colunaBusca'] == 'T002_Data_Recebimento') ? ' selected="selected"' : '').'> Data Recebimento </option>';

//Gera o Combo para Filtro de Contas a Receber (Todas/Contas Recebidas/Contas Não Recebidas)
$comboFiltroReceber = '';
$comboFiltroReceber .= '<option value="Todas"'.(($pesquisa['filtroReceber'] == 'Todas') ? ' selected="selected"' : '').'> Todas </option>';
$comboFiltroReceber .= '<option value="Recebidas"'.(($pesquisa['filtroReceber'] == 'Recebidas') ? ' selected="selected"' : '').'> Recebidas </option>';
$comboFiltroReceber .= '<option value="NaoRecebidas"'.(($pesquisa['filtroReceber'] == 'NaoRecebidas') ? ' selected="selected"' : '').'> Não Recebidas </option>';

$confNota_FiscalLista = true;

//Filtro por Status de Nota Fiscal Analise/Negado/Aprovado/Pendente
$statusNota_Fiscal = isset($_REQUEST['statusNota_Fiscal']) ? $_REQUEST['statusNota_Fiscal'] : '';



include('mobile/tablet/inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageContasReceber">
EOT;
$etapa = 10;
include('mobile/tablet/inc.header.bar.php');

//Verifica Se Foi selecionado um critério para pesquisa
if($pesquisa['colunaBusca']!=''){
	//Verifica Se Foi selecionado um critério tipo data
	if($pesquisa['colunaBusca']=='T002_Data_Emissao' || $pesquisa['colunaBusca']=='T002_Data_Vencimento' || $pesquisa['colunaBusca']=='T002_Data_Recebimento' ){
		$condicaoPesquisa = "AND date_format({$pesquisa['colunaBusca']},'%d/%m/%Y') like '%{$pesquisa['busca']}%'";
	}else{
		$condicaoPesquisa = "AND {$pesquisa['colunaBusca']} LIKE '%{$pesquisa['busca']}%'";
	}
}else{
	$condicaoPesquisa = "AND (date_format(T002_Data_Emissao,'%d/%m/%Y') LIKE '%{$pesquisa['busca']}%' OR date_format(T002_Data_Vencimento,'%d/%m/%Y') LIKE '%{$pesquisa['busca']}%' AND date_format(T002_Data_Recebimento,'%d/%m/%Y') LIKE '%{$pesquisa['busca']}%' OR D024_Nome_Empresa LIKE '%{$pesquisa['busca']}%' OR D027_Portador LIKE '%{$pesquisa['busca']}%' OR C007_Primeiro_Nome LIKE '%{$pesquisa['busca']}%')";
}
if(!empty($pesquisa['filtroReceber']) && $pesquisa['filtroReceber']!="Todas"){
	if($pesquisa['filtroReceber']=="Recebidas"){
		$condicaoPesquisa .= "AND (T002_Data_Recebimento > 0 AND T002_Data_Recebimento IS NOT NULL)";
	}else{
		$condicaoPesquisa .= "AND (T002_Data_Recebimento = 0)";
	}
}else{
	$condicaoPesquisa .= "";
}

$extra="WHERE ((1=1)) {$condicaoPesquisa} AND (T002_C004_Id={$g['empresaAtual']} and (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null))ORDER BY {$pesquisa['ordem']} LIMIT 0,100;";
$sql = <<<EOT
	SELECT 
		D024_Id, D024_Nome_Empresa, T002_Numero_Duplicata, T002_Data_Emissao, datediff(T002_Data_Vencimento,T002_Data_Emissao) Prazo, T002_Data_Vencimento, datediff(if(T002_Data_Recebimento='0000-00-00',curdate(),T002_Data_Recebimento),T002_Data_Vencimento) Atraso, T002_Data_Recebimento, T002_Valor_Duplicata, T002_Valor_Juros, T002_Valor_Desconto, T002_Valor_Recebido, T002_Valor_Total, D027_Portador
	FROM T002
		LEFT JOIN D024 ON D024_Id=T002_D024_Id 
		LEFT JOIN D027 ON D027_Id=T002_D027_Id 
		LEFT JOIN T117 ON T117_Id=T002_T117_Id 
		LEFT JOIN D014 ON D014_Id=T002_D014_Id 
		LEFT JOIN D046 ON D046_Id=D024_D046_Id 
		LEFT JOIN T007 ON T007_Id=T002_T007_Id 
		LEFT JOIN T035 ON T035_Id=T002_T035_Id 
		LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno 
		LEFT JOIN T103 ON T002_Id=T103_T002_Id 
	{$extra}
EOT;


$sqlTotalizadores =  <<<EOT
	SELECT 
		SUM(if(T002_Data_Recebimento!='0000-00-00',T002_Valor_Recebido,0)) AS TotalRecebido,
		SUM(if(T002_Data_Recebimento='0000-00-00' and T002_Data_Vencimento<curdate(),T002_Valor_Total,0)) AS TotalAtrasado,
		SUM(if(T002_Data_Recebimento='0000-00-00',T002_Valor_Total,0)) AS TotalAReceber,
		SUM(if(T002_Data_Recebimento='0000-00-00' and T002_Data_Vencimento>=curdate(),T002_Valor_Total,0)) AS Receber,
		COUNT(*) AS TotalTitulos
	FROM T002
		LEFT JOIN D024 ON D024_Id=T002_D024_Id
		LEFT JOIN D027 ON D027_Id=T002_D027_Id
		LEFT JOIN T117 ON T117_Id=T002_T117_Id
		LEFT JOIN D014 ON D014_Id=T002_D014_Id
		LEFT JOIN D046 ON D046_Id=D024_D046_Id
		LEFT JOIN T007 ON T007_Id=T002_T007_Id
		LEFT JOIN T035 ON T035_Id=T002_T035_Id
		LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
		LEFT JOIN T103 ON T002_Id=T103_T002_Id
	{$extra}
EOT;

$resTotalizadores = mysql_query($sqlTotalizadores);
$rowTotalizadores = mysql_fetch_assoc($resTotalizadores);

$totalTitulos = $rowTotalizadores['TotalTitulos'];
$totalRecebido = gCorrigeNumero($rowTotalizadores['TotalRecebido']);
$totalAtrasado = gCorrigeNumero($rowTotalizadores['TotalAtrasado']);
$totalAReceber = gCorrigeNumero($rowTotalizadores['TotalAReceber']);
$valorAReceber = gCorrigeNumero($rowTotalizadores['Receber']);
$totalGeral = gCorrigeNumero($rowTotalizadores['TotalRecebido'] + $rowTotalizadores['TotalAReceber']);




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
	           		<form id="formPesquisa" name="formPesquisa" method="get" action="contasReceber.php?refresh={$uniqId}" data-transition="fade" data-ajax="false">   		
	            		<div style="width: 38%; float:left; margin-top: 8px; margin-bottom: 5px;">
                    		<input name="pesquisa" placeholder="Procurar" data-type="search" class="ui-input-text ui-body-c" value="{$pesquisa['busca']}" id="procurarContasReceber">
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
                    		<select name="filtroReceber" id="filtroReceber" data-inline="true">
						     	{$comboFiltroReceber}
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
                            Recebido
                        </h4>
                        <h4 style="margin: 0px;">
                            {$totalRecebido}
                        </h4>
                    </div>
                    <div class="ui-block-c" style="border-right: 2px solid #CCCCCC; text-align: center; width:16.66%; float: left;">
                        <h4 style="margin: 0px;">
                            Atrasado
                        </h4>
                        <h4 style="margin: 0px;">
                           {$totalAtrasado}
                        </h4>
                    </div>
                     <div class="ui-block-d" style="border-right: 2px solid #CCCCCC; text-align: center; width:16.66%; float: left;">
                        <h4 style="margin: 0px;">
                            A Receber
                        </h4>
                        <h4 style="margin: 0px;">
                           {$valorAReceber}
                        </h4>
                    </div>
                     <div class="ui-block-e" style="border-right: 2px solid #CCCCCC; text-align: center; width:16.66%; float: left;">
                        <h4 style="margin: 0px;">
                            Total Receber
                        </h4>
                        <h4 style="margin: 0px;">
                           {$totalAReceber}
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
		$T002_Numero_Duplicata = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T002_Numero_Duplicata' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$T002_Data_Emissao = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T002_Data_Emissao' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$Prazo = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'Prazo' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$Atraso = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'Atraso' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$T002_Valor_Total = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T002_Valor_Total' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';

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
		$T002_Numero_Duplicata_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T002_Numero_Duplicata') ? $iconeOrdenacao : '';
		$T002_Data_Emissao_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T002_Data_Emissao') ? $iconeOrdenacao : '';
		$Prazo_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'Prazo') ? $iconeOrdenacao : '';
		$Atraso_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'Atraso') ? $iconeOrdenacao : '';
		$T002_Valor_Total_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T002_Valor_Total') ? $iconeOrdenacao : '';
		
		if ($C007['C007_Flag_Vendedor']=='D' OR $C007['C007_Flag_Vendedor']=='S') {
		// GRID: Diretor
		
		echo <<<EOT
	    						<td style="width: 38%"; align="left";><a href="contasReceber.php?coluna=D024_Nome_Empresa&tipoOrdenacao={$D024_Nome_Empresa}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none; padding-bottom:0px;" data-transition="fade">Cliente{$D024_Nome_Empresa_Icone}</a></td>
	    						<td style="width: 10%"; align="center";><a href="contasReceber.php?coluna=T002_Numero_Duplicata&tipoOrdenacao={$T002_Numero_Duplicata}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none; padding-bottom:0px;" data-transition="fade">N. Dupl.{$T002_Numero_Duplicata_Icone}</a></td>
	    						<td style="width: 20%"; align="left"><a href="contasReceber.php?coluna=T002_Data_Emissao&tipoOrdenacao={$T002_Data_Emissao}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Data{$T002_Data_Emissao_Icone}</a></td>
	    						<td style="width: 7%"; align="center"><a href="contasReceber.php?coluna=Prazo&tipoOrdenacao={$Prazo}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Prz{$Prazo_Icone}</a></td>
	    						<td style="width: 7%;"; align="center"><a href="contasReceber.php?coluna=Atraso&tipoOrdenacao={$Atraso}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Ats{$Atraso_Icone}</a></td>
	    						<td style="width: 18%"; align="left"><a href="contasReceber.php?coluna=T002_Valor_Total&tipoOrdenacao={$T002_Valor_Total}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Valores{$T002_Valor_Total_Icone}</a></td>
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
	$row['T002_Numero_Duplicata'] = $row['T002_Numero_Duplicata']!="" ? $row['T002_Numero_Duplicata'] : " - ";
	$row['T002_Data_Emissao'] = gCorrigeData($row['T002_Data_Emissao']);
	$row['T002_Data_Vencimento'] = gCorrigeData($row['T002_Data_Vencimento']);
	$row['T002_Data_Recebimento'] = gCorrigeData($row['T002_Data_Recebimento']);
	$row['T002_Valor_Duplicata'] = gCorrigeNumero($row['T002_Valor_Duplicata']);
	$row['T002_Valor_Juros'] = gCorrigeNumero($row['T002_Valor_Juros']);
	$row['T002_Valor_Recebido'] = gCorrigeNumero($row['T002_Valor_Recebido']);
	$row['T002_Valor_Desconto'] = gCorrigeNumero($row['T002_Valor_Desconto']);
	$row['T002_Valor_Total'] = gCorrigeNumero($row['T002_Valor_Total']);
	if ($C007['C007_Flag_Vendedor']=='D' OR $C007['C007_Flag_Vendedor']=='S') {
		// GRID: Diretor
		echo <<<EOT
			<li data-theme="c" data-icon="false">
		    {$href}
	    		<table style="width: 100%; {$corLinha}">
	    			<tr>
	    				<td style="width: 38%"; align="left"  valign="top">{$row['D024_Nome_Empresa']} ({$row['D024_Id']})</td>
	    				<td style="width: 10%"; align="center"  valign="top">{$row['T002_Numero_Duplicata']}</td>
	    				<td style="width: 20%"; align="left"  valign="top">
	    					Emissão: <b>{$row['T002_Data_Emissao']}</b> <br/>
	    					Vencimento: <b>{$row['T002_Data_Vencimento']}</b> <br/>
	    					Recebimento: <b>{$row['T002_Data_Recebimento']}</b>
	    				</td>
	    				<td style="width: 7%"; align="center"  valign="top">{$row['Prazo']}</td>
	    				<td style="width: 7%"; align="center"  valign="top">{$row['Atraso']}</td>
	    				<td style="width: 18%"; align="left"  valign="top">
	    					Valor: <b>{$row['T002_Valor_Duplicata']}</b> <br/>
	    					Juros: <b>{$row['T002_Valor_Juros']}</b> <br/>
	    					Desconto: <b>{$row['T002_Valor_Desconto']}</b> <br/>
	    					Recebido: <b>{$row['T002_Valor_Recebido']}</b> <br/>
	    					<i><u>Total: <b>{$row['T002_Valor_Total']}</b><u></i><br/>	
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
    	$( document ).undelegate("#pageContasReceber", "pageinit");
    	$( document ).delegate("#pageContasReceber", "pageinit", function() {
    		$("#filtroReceber").bind("change", function() { 
    			$("#formPesquisa").submit();
    		});
EOT;
include('inc.swipe.events.php');
echo <<<EOT
    	});
    </script>
EOT;
