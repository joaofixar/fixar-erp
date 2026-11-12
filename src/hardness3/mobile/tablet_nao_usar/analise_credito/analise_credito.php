<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');


//Parametro de Pesquisa e Coluna da Pesquisa selecionada em orçamentos
$arqFiltro = $g['pathDadosUsuario'] . 'tabletFiltro/analise_credito';
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
$comboPesquisa .= '<option value="T005_Data_Emissao"'.(($pesquisa['colunaBusca'] == 'T005_Data_Emissao') ? ' selected="selected"' : '').'> Data </option>';
$comboPesquisa .= '<option value="T005_Id"'.(($pesquisa['colunaBusca'] == 'T005_Id') ? ' selected="selected"' : '').'> Nº Pedido </option>';
$comboPesquisa .= '<option value="D024_Nome_Empresa"'.(($pesquisa['colunaBusca'] == 'D024_Nome_Empresa') ? ' selected="selected"' : '').'> Razão Social </option>';
$comboPesquisa .= '<option value="D024_Flag_Curva_ABC"'.(($pesquisa['colunaBusca'] == 'D024_Flag_Curva_ABC') ? ' selected="selected"' : '').'> ABC </option>';
$comboPesquisa .= '<option value="C007_Primeiro_Nome"'.(($pesquisa['colunaBusca'] == 'C007_Primeiro_Nome') ? ' selected="selected"' : '').'> Vendedor </option>';

$confNota_FiscalLista = true;

//Filtro por Status de Nota Fiscal Analise/Negado/Aprovado/Pendente
$statusNota_Fiscal = isset($_REQUEST['statusNota_Fiscal']) ? $_REQUEST['statusNota_Fiscal'] : '';



include('mobile/tablet/inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageAnalise_Credito">
EOT;
$etapa = 9;
include('mobile/tablet/inc.header.bar.php');

//Verifica Se Foi selecionado um critério para pesquisa
if($pesquisa['colunaBusca']!=''){
	//Verifica Se Foi selecionado um critério tipo data
	if($pesquisa['colunaBusca']=='T005_Data_Emissao'){
		$condicaoPesquisa = "AND date_format({$pesquisa['colunaBusca']},'%d/%m/%Y') like '%{$pesquisa['busca']}%'";
	}else{
		$condicaoPesquisa = "AND {$pesquisa['colunaBusca']} LIKE '%{$pesquisa['busca']}%'";
	}
}else{
	$condicaoPesquisa = "AND (date_format(T005_Data_Emissao,'%d/%m/%Y') LIKE '%{$pesquisa['busca']}%' OR T005_Id LIKE '%{$pesquisa['busca']}%' OR C007_Primeiro_Nome LIKE '%{$pesquisa['busca']}%' OR D024_Nome_Empresa LIKE '%{$pesquisa['busca']}%' )";
}


$extra="WHERE ((T005_C004_Id={$g['empresaAtual']}
    	and T005_Flag_Status='6') {$condicaoPesquisa}) GROUP BY T005_Id ORDER BY {$pesquisa['ordem']} LIMIT 0,100;";
$sql = <<<EOT
	SELECT T005_Data_Emissao, 
	T005_Id, 
	D024_Nome_Empresa, 
	D024_Flag_Curva_ABC, 
	if(T005_Flag_ACP="1","NF",if(T005_Flag_ACP="2","Romaneio",if(T005_Flag_ACP="3","ACP",if(T005_Flag_ACP="5","D1",if(T005_Flag_ACP="6","NFe","CF"))))) as Flag_Acp, 
	T005_Valor_Total, 
	sum(if(T002_Data_Vencimento<curdate(),T002_Valor_Total,0)) as Vencido, 
	sum(if(T002_Data_Vencimento>=curdate(),T002_Valor_Total,0)) as Vencer, 
	D024_Valor_Limite, 
	T005_Observacao_Comercial, 
	D024_Observacao_Financeiro,
	C007_Primeiro_Nome,
	D024_Id,
	T007_Numero_Nota_Fiscal,
	T005_Flag_Status,
	sum(T002_Valor_Total) Receber,
	T005_Status_Pedido(T005_Flag_Status,1) T005_Status
	FROM T005 
	left join D024 on D024_Id=T005_D024_Id
    left join T007 on T007_T005_Id=T005_Id
    left join T002 on T002_D024_Id=T005_D024_Id and T002_Data_Recebimento='0000-00-00' and (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)
    left join C007 on C007_Id=T005_C007_Id_Vendedor_Interno
	{$extra}
EOT;



echo <<<EOT
		<div data-role="content">
        	<div class="ui-grid-b" style="margin-top: -10px; margin-bottom: 0px;">
                <div class="ui-block-a" style="width: 20%;">
                    <div style="float:left;">
                    	<a data-role="button" data-transition="slide" data-icon="delete" data-iconpos="left" onclick="\$.mobile.changePage( '/hardness3/mobile/tablet/login.php?logout=true', { transition: 'fade'} )" data-ajax="false">
			            	Sair
				        </a>
				    </div>
                </div>
	            <div class="ui-block-b" style="width: 50%;">  
	           		<form id="formPesquisa" name="formPesquisa" method="get" action="analise_credito.php?T005_Id={$T005_Id}" data-transition="fade">   		
	            		<div style="width: 48%; float:left; margin-top: 8px; margin-bottom: 5px;">
                    		<input name="pesquisa" placeholder="Procurar" data-type="search" class="ui-input-text ui-body-c" value="{$pesquisa['busca']}" id="procurarAnalise_Credito">
                    	</div>
                    	<div style="float:left;">
                    		<select name="colunaPesquisa" id="colunaPesquisa" data-inline="true">
						     	{$comboPesquisa}
						   </select>
					    </div>
                    	<div style="float:left;">
					        <input type="submit" value="Procurar" data-role="button" data-transition="slide" data-icon="search" data-iconpos="left" />
					    </div>
					 </form>
	            </div>
	        </div>
	    
                <ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 0px;">
                    <li data-role="list-divider" role="heading">
	    				<table style="width: 100%";>
	    					<tr>
EOT;

		//Verifica Ordenação da Coluna pelo clique do usuário DESC ou ASC	
		$T005_Data_Emissao = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T005_Data_Emissao' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$T005_Id = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T005_Id' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$D024_Nome_Empresa = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D024_Nome_Empresa' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$D024_Flag_Curva_ABC = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D024_Flag_Curva_ABC' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$Flag_Acp = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'Flag_Acp' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
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
		$D024_Nome_Empresa_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D024_Nome_Empresa') ? $iconeOrdenacao : '';
		$D024_Flag_Curva_ABC_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D024_Flag_Curva_ABC') ? $iconeOrdenacao : '';
		$Flag_Acp_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'Flag_Acp') ? $iconeOrdenacao : '';
		$C007_Primeiro_Nome_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'C007_Primeiro_Nome') ? $iconeOrdenacao : '';
		
		if ($C007['C007_Flag_Vendedor']=='D' OR $C007['C007_Flag_Vendedor']=='S') {
		// GRID: Diretor
		
		echo <<<EOT
	    						<td style="width: 10%"; align="center";><a href="analise_credito.php?coluna=T005_Data_Emissao&tipoOrdenacao={$T005_Data_Emissao}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none; padding-bottom:0px;" data-transition="fade">Emissão{$T005_Data_Emissao_Icone}</a></td>
	    						<td style="width: 10%"; align="center";><a href="analise_credito.php?coluna=T005_Id&tipoOrdenacao={$T005_Id}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none; padding-bottom:0px;" data-transition="fade">Pedido{$T005_Id_Icone}</a></td>
	    						<td style="width: 53%"; align=left><a href="analise_credito.php?coluna=D024_Nome_Empresa&tipoOrdenacao={$D024_Nome_Empresa}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Razão Social{$D024_Nome_Empresa_Icone}</a></td>	    						
	    						<td style="width: 8%"; align="center"><a href="analise_credito.php?coluna=D024_Flag_Curva_ABC&tipoOrdenacao={$D024_Flag_Curva_ABC}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">ABC{$D024_Flag_Curva_ABC_Icone}</a></td>
	    						<td style="width: 8%"; align="center"><a href="analise_credito.php?coluna=Flag_Acp&tipoOrdenacao={$Flag_Acp}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Doc{$Flag_Acp_Icone}</td>
	    						<td style="width: 12%"; align="center"><a href="analise_credito.php?coluna=C007_Primeiro_Nome&tipoOrdenacao={$C007_Primeiro_Nome}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Vendedor{$C007_Primeiro_Nome_Icone}</a></td>
EOT;
		} 
	echo <<<EOT
							</tr>
	    				</table>
                    </li>
EOT;


$res = mysql_query($sql);
while ($row = mysql_fetch_assoc($res)) {
	$row['T005_Data_Emissao'] = gCorrigeData($row['T005_Data_Emissao']);
	$row['T005_Valor_Total'] = gCorrigeNumero($row['T005_Valor_Total']);
	$row['Vencido'] = gCorrigeNumero($row['Vencido']);
	$row['Vencer'] = gCorrigeNumero($row['Vencer']);
	$row['D024_Nome_Empresa'] = substr($row['D024_Nome_Empresa'],0,27);
	$row['D024_Flag_Curva_ABC'] = ($row['D024_Flag_Curva_ABC']!="") ? $row['D024_Flag_Curva_ABC'] : '-';
	
	//calcula o campo Valor Saldo Limite do grid	
	require_once('bibliotecas/classes/FIN007.php');
	$FIN007 = new FIN007();		
	$valorLimite=$row['D024_Valor_Limite'];
	$vencido= gCorrigeNumeroInverte($row['Vencido']);
	$vencer=gCorrigeNumeroInverte($row['Vencer']);
	$valorTotal=gCorrigeNumeroInverte($row['T005_Valor_Total']);
	$outros=$FIN007->calculaOutrosPedidos($row);
	$retorno=$valorLimite-($vencido+$vencer+$outros+$valorTotal);
	if ($retorno<0){
		$saldoValorLimite = '<span style="color:red;">' . gCorrigeNumero($retorno) . '</span>';
	}else{
		$saldoValorLimite = gCorrigeNumero($retorno);
	}
	
	// calcula o campo Forma Pagamento do grid
	$sqlFormaPagamento=mysql_query("select T090_Prazos_Dias,
                             T090_Valor_Parcela,
                             D027_Portador,
                             T090_Data_Vencimento
                                 from T090
                             left join D027 on D027_Id=T090_D027_Id                                
        						 where T090_T005_Id='{$row['T005_Id']}'");	        
    $count=0;
    $pgts=array();
    while ($retornoFormaPagamento = mysql_fetch_array($sqlFormaPagamento)){
     	$pgts[$count]=array('prazo'=>$retornoFormaPagamento['T090_Prazos_Dias'],'valorParcela'=>$retornoFormaPagamento['T090_Valor_Parcela'],'portador'=>$retornoFormaPagamento['D027_Portador'],'vencimento'=>$retornoFormaPagamento['T090_Data_Vencimento']);
    	$count++;
    }
    $retornoPagamento = $pgts;       
	$formaPagamento="";
	for($i=0;$i<sizeof($retornoPagamento);$i++){
		$formaPagamento.= '<li type="disc">' . gCorrigeData($retornoPagamento[$i]['vencimento']).' - '.number_format($retornoPagamento[$i]['valorParcela'],2,',','.').' - '.$retornoPagamento[$i]['portador'].'<br />';
	}	


	$corLinha = "";
	$href = <<<EOT
		<a href="/hardness3/mobile/tablet/analise_credito/pedidoAnalise.php?T005_Id={$row['T005_Id']}" data-ajax="false" data-transition="slide">
EOT;
	$nomeCliente = $row['D024_Nome_Empresa'];
	
	if ($C007['C007_Flag_Vendedor']=='D' OR $C007['C007_Flag_Vendedor']=='S') {
		// GRID: Diretor
		echo <<<EOT
			<li data-theme="c" data-icon="false">
		    {$href}
	    		<table style="width: 100%; {$corLinha}">
	    			<tr>		
	    				<td style="width: 10%"; align="center" valign="top">{$row['T005_Data_Emissao']}</td>
	    				<td style="width: 10%"; align="center" valign="top">
	    					{$row['T005_Id']}	
	    				</td>
	    				<td style="width: 53%"; valign="top">{$nomeCliente}</td>
	    				<td style="width: 8%"; align="center" valign="top">{$row['D024_Flag_Curva_ABC']}</td>
	    				<td style="width: 8%"; align="center" valign="top">{$row['Flag_Acp']}</td>
	    				<td style="width: 12%"; align="center" valign="top">{$row['C007_Primeiro_Nome']}</td>
	    			</tr>
	    		</table>
	    		<table style="width: 100%; margin-top:15px; {$corLinha}">
	    			<tr>
	    				<td style="width: 50%"; align="left" valign="top">
	    					<u><i>Valores:</i></u><br/><br/>
	    						<ul>
	    							<li><font style="font-weight:normal;">Valor Pedido:</font> {$row['T005_Valor_Total']}</li>
	    							<li><font style="font-weight:normal;">Outros Pedidos:</font>{$row['T005_Valor_Total']}</li>
	    							<li><font style="font-weight:normal;">Valor Vencido:</font> {$row['Vencido']}</li>
	    							<li><font style="font-weight:normal;">Valor a vencer:</font> {$row['Vencer']}</li>
	    							<li><font style="font-weight:normal;">Valor Limite:</font>{$row['D024_Valor_Limite']}</li>
	    							<li><font style="font-weight:normal;">Valor Saldo Limite:</font> {$saldoValorLimite}</li>
	    						</ul>
	    					<br/>
	    				</td>
	    				<td style="width: 50%"; align="left" valign="top">
	    					<u><i>Forma de Pagamento:</i></u><br/><br/><font style="font-weight:normal;"><ul>{$formaPagamento}</ul></font>
	    				</td>
	    			</tr>
	    			<tr>
	    				<td>
	    					<u><i>Obs. Financeira:</i></u><font style="font-weight:normal;">{$row['T005_Observacao_Comercial']}</font></br>
	    					<u><i>Obs. Comercial:</i></u><font style="font-weight:normal;">{$row['D024_Observacao_Financeiro']}</font>
	    				</td>
	    			</tr>
	    		</table>
		    </a>
		    </li>
		    <!--<div data-role="collapsible" data-collapsed="true" data-theme="c">
			<h3>Forma de Pagamento</h3>
					<div style="width: 100%;">
					    	<table style="width: 100%;">
					    		<tr>
					    			<td align="left" style="width: 100%;">
										<u><i>Forma de Pagamento:</i></u><br/><font style="font-weight:normal;"><ul>{$formaPagamento}</ul></font>
					    			</td>
					    		</tr>
					    	</table>
					</div>
		</div>-->
EOT;
	} else {
		// GRID: Vendedor
		echo <<<EOT
			<li data-theme="c" data-icon="false">
		    {$href}
	    		<table style="width: 100%; {$corLinha}">
	    			<tr>
	    				<td style="width: 7%"; align="center">{$row['D006_Codigo_CFOP']}</td>
	    				<td style="width: 7%"; align="center">{$row['T007_Data_Emissao']}</td>
	    				<td style="width: 8%"; align="center">{$row['T007_Numero_Nota_Fiscal']}</td>
	    				<td style="width: 8%"; align="center">{$row['D024_Id']}</td>
	    				<td style="width: 33%";>{$nomeCliente}</td>
	    				<td style="width: 12%"; align="right">{$row['T007_Valor_Total_Produtos']}</td>
	    				<td style="width: 10%"; align="right">{$row['T007_Valor_Total_Servicos']}</td>
	    				<td style="width: 10%"; align="right">{$row['T007_Valor_Troca']}</td>
	    				<td style="width: 18%"; align="right">{$row['T007_Valor_Total']}</td>
	    				<td style="width: 20%"; align="center">{$row['C007_Primeiro_Nome']}</td>
	    				<td style="width: 20%"; align="center">{$row['C007_Primeiro_Nome']}</td>
	    				<td style="width: 20%"; align="center">{$row['C007_Primeiro_Nome']}</td>
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
    	$( document ).undelegate("#pageAnalise_Credito", "pageinit");
    	$( document ).delegate("#pageAnalise_Credito", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
    	});
    </script>
EOT;
