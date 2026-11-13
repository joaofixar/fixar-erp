<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');


//Parametro de Pesquisa e Coluna da Pesquisa selecionada em orçamentos
$arqFiltro = $g['pathDadosUsuario'] . 'tabletFiltro/resumoClientes';
if (!file_exists($arqFiltro)) {
	$pesquisa = array('busca' => '', 'ordem' => 'R001_Data DESC', 'colunaBusca' => '', 'tipoOrdenacao' => '', 'coluna' => '');
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

$uniqId = uniqid();

//Gera o Combo para pesquisa
$comboPesquisa = '';
$comboPesquisa .= '<option value=""'.(($pesquisa['colunaBusca'] == '') ? ' selected="selected"' : '').'> Geral </option>';
$comboPesquisa .= '<option value="R001_Data"'.(($pesquisa['colunaBusca'] == 'R001_Data') ? ' selected="selected"' : '').'> Data </option>';


include('mobile/tablet/inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageResumoClientes">
EOT;
$etapa = 1;
include('inc.header.bar.php');

//Verifica Se Foi selecionado um critério para pesquisa
if($pesquisa['colunaBusca']!=''){
	//Verifica Se Foi selecionado um critério tipo data
	if($pesquisa['colunaBusca']=='R001_Data'){
		$condicaoPesquisa = "AND date_format({$pesquisa['colunaBusca']},'%d/%m/%Y') like '%{$pesquisa['busca']}%'";
	}else{
		$condicaoPesquisa = "AND {$pesquisa['colunaBusca']} LIKE '%{$pesquisa['busca']}%'";
	}
}else{
	$condicaoPesquisa = "AND (date_format(R001_Data,'%d/%m/%Y') LIKE '%{$pesquisa['busca']}%')";
}

$extra="WHERE ((1=1)) {$condicaoPesquisa} AND (R001_C007_Id_Vendedor = 0 AND R001_C007_Id_Supervisor = 0 AND R001_D049_Id = 0 AND R001_D024_Id = 0 AND R001_D006_Id = 0) ORDER BY {$pesquisa['ordem']} LIMIT 0,100;";
$sql = <<<EOT
	SELECT 
		R001_Id, R001_Data, R001_Clientes_Nr, R001_Clientes_Novos, R001_Clientes_Ativos, R001_Clientes_Inativos, R001_Clientes_Contatos
	FROM R001
		LEFT JOIN C007 AS Vendedor ON Vendedor.C007_Id=R001_C007_Id_Vendedor 
		LEFT JOIN C007 AS Supervisor ON Supervisor.C007_Id=R001_C007_Id_Supervisor
	{$extra}
EOT;

echo <<<EOT
		<div data-role="content">
        	<div class="ui-grid-b" style="margin-top: -6px; margin-bottom: 0px;">
                <div class="ui-block-a" style="width: 15%;">
                    <div style="float:left;">
                    	<a data-role="button" data-transition="slide" data-icon="delete" data-iconpos="left" onclick="\$.mobile.changePage( '/hardness3/mobile/tablet/login.php?logout=true', { transition: 'fade'} )" data-ajax="false">
			            	Sair
				        </a>
				    </div>
                </div>
	            <div class="ui-block-b" style="width: 85%;">  
	           		<form id="formPesquisa" name="formPesquisa" method="get" action="resumoClientes.php?refresh={$uniqId}" data-transition="fade" data-ajax="false">   		
	            		<div style="width: 38%; float:left; margin-top: 8px; margin-bottom: 5px;">
                    		<input name="pesquisa" placeholder="Procurar" data-type="search" class="ui-input-text ui-body-c" value="{$pesquisa['busca']}" id="procurarResumoClientes">
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
                <ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 1px;">
                    <li data-role="list-divider" role="heading">
	    				<table style="width: 100%";>
	    					<tr>
EOT;

		//Verifica Ordenação da Coluna pelo clique do usuário DESC ou ASC
		$R001_Data = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'R001_Data' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$R001_Clientes_Nr = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'R001_Clientes_Nr' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$R001_Clientes_Novos = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'R001_Clientes_Novos' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$R001_Clientes_Ativos = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'R001_Clientes_Ativos' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$R001_Clientes_Inativos = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'R001_Clientes_Inativos' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$R001_Clientes_Contatos = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'R001_Clientes_Contatos' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';

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
		$R001_Data_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'R001_Data') ? $iconeOrdenacao : '';
		$R001_Clientes_Nr_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'R001_Clientes_Nr') ? $iconeOrdenacao : '';
		$R001_Clientes_Novos_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'R001_Clientes_Novos') ? $iconeOrdenacao : '';
		$R001_Clientes_Ativos_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'R001_Clientes_Ativos') ? $iconeOrdenacao : '';
		$R001_Clientes_Inativos_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'R001_Clientes_Inativos') ? $iconeOrdenacao : '';
		$R001_Clientes_Contatos_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'R001_Clientes_Contatos') ? $iconeOrdenacao : '';
		
		if ($C007['C007_Flag_Vendedor']=='D' OR $C007['C007_Flag_Vendedor']=='S') {
		// GRID: Diretor
		
		echo <<<EOT
								<td style="width: 14.28%"; align="left";><a href="resumoClientes.php?coluna=R001_Data&tipoOrdenacao={$R001_Data}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none; padding-bottom:0px;" data-transition="fade">Data{$R001_Data_Icone}</a></td>
	    						<td style="width: 14.28%"; align="center";><a href="#" style="color:#FFF; text-decoration:none; padding-bottom:0px;" data-transition="fade">Dia</a></td>
	    						<td style="width: 14.28%"; align="center";><a href="resumoClientes.php?coluna=R001_Clientes_Nr&tipoOrdenacao={$R001_Clientes_Nr}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none; padding-bottom:0px;" data-transition="fade">Clientes{$R001_Clientes_Nr_Icone}</a></td>
	    						<td style="width: 14.28%"; align="center";><a href="resumoClientes.php?coluna=R001_Clientes_Novos&tipoOrdenacao={$R001_Clientes_Novos}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none; padding-bottom:0px;" data-transition="fade">Novos{$R001_Clientes_Novos_Icone}</a></td>
	    						<td style="width: 14.28%"; align="center"><a href="resumoClientes.php?coluna=R001_Clientes_Ativos&tipoOrdenacao={$R001_Clientes_Ativos}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Ativos{$R001_Clientes_Ativos_Icone}</a></td>
	    						<td style="width: 14.28%;"; align="center"><a href="resumoClientes.php?coluna=R001_Clientes_Inativos&tipoOrdenacao={$R001_Clientes_Inativos}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Inativos{$R001_Clientes_Inativos_Icone}</a></td>
	    						<td style="width: 14.28%"; align="center"><a href="resumoClientes.php?coluna=R001_Clientes_Contatos&tipoOrdenacao={$R001_Clientes_Contatos}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Contatos{$R001_Clientes_Contatos_Icone}</a></td>
EOT;
		}
	echo <<<EOT
							</tr>
	    				</table>
                    </li>
EOT;

$res = mysql_query($sql);
while ($row = mysql_fetch_assoc($res)) {
	$R001_Data = gCorrigeData($row['R001_Data']);
	$diaDaSemana = gPegaDiaSemana($row['R001_Data']);
	if($diaDaSemana == "Sábado"){
		$diaDaSemana = substr($diaDaSemana, 0, 4);
	} else {
		$diaDaSemana = substr($diaDaSemana, 0, 3);
	}
	if ($C007['C007_Flag_Vendedor']=='D' OR $C007['C007_Flag_Vendedor']=='S') {
		// GRID: Diretor
		echo <<<EOT
			<li data-theme="c" data-icon="false">
		    {$href}
	    		<table style="width: 100%; {$corLinha}">
	    			<tr>
	    				<td style="width: 14.28%"; align="left"  valign="top">{$R001_Data}</td>
	    				<td style="width: 14.28%"; align="center"  valign="top">{$diaDaSemana}</td>
	    				<td style="width: 14.28%"; align="center"  valign="top">{$row['R001_Clientes_Nr']}</td>
	    				<td style="width: 14.28%"; align="center"  valign="top">{$row['R001_Clientes_Novos']}</td>
	    				<td style="width: 14.28%"; align="center"  valign="top">{$row['R001_Clientes_Ativos']}</td>
	    				<td style="width: 14.28%"; align="center"  valign="top">{$row['R001_Clientes_Inativos']}</td>
	    				<td style="width: 14.28%"; align="center"  valign="top">{$row['R001_Clientes_Contatos']}</td>
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
    	$( document ).undelegate("#pageResumoClientes", "pageinit");
    	$( document ).delegate("#pageResumoClientes", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
    	});
    </script>
EOT;
