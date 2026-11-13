<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

include('inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageClientesProcurar">
EOT;
$etapa = 5;
include('inc.header.bar.php');


//Parametro de Pesquisa e Coluna da Pesquisa selecionada em requisições
$arqFiltro = $g['pathDadosUsuario'] . 'tabletFiltro/clientesLista';
if (!file_exists($arqFiltro)) {
	$pesquisa = array('busca' => '', 'ordem' => "{$g['C031']['definirOrdemDeOrdenacao']}", 'colunaBusca' => '', 'tipoOrdenacao' => '', 'coluna' => '');
	file_put_contents($arqFiltro, serialize($pesquisa));
}
$pesquisa = unserialize(file_get_contents($arqFiltro));
if (isset($_REQUEST['pesquisa'])) {
	$pesquisa['busca'] = $_REQUEST['pesquisa'];
	file_put_contents($arqFiltro, serialize($pesquisa));
}

echo <<<EOT
        	<div data-role="content">
                <div class="ui-grid-a" style="margin-top: -10px; margin-bottom: 0px;">
		            <div>
							<div style="width: 75%; float:left; margin-top: 8px; margin-bottom: 5px;">
	                    		<input placeholder="Procurar Cliente" data-type="search" class="ui-input-text ui-body-c" value="{$pesquisa['busca']}" id="procurarCliente">
	                    	</div>
	                    	<div style="float:left; margin: 5px 0 0 2px;">
						        <a data-role="button" data-transition="slide" data-icon="search" data-iconpos="notext" onclick="\$.mobile.changePage( './clientesLista.php', {transition: 'fade', type:'post', data: 'colunaPesquisa=' + encodeURIComponent($('#colunaPesquisa').val()) + '&pesquisa=' + encodeURIComponent($('#procurarCliente').val())} )" ></a>
						    </div>
						    <div style="float:left; margin: 5px 0 0 2px;">
						        <a data-inline="true" data-role="button" data-transition="fade" data-icon="plus" data-iconpos="notext" href="././clientesEditar.php" data-ajax="false"></a>
						    </div>
		            </div>
                </div>
                <ul data-role="listview" style="margin-top: 1px; clear:both;">
EOT;
     	//Verifica Ordenação da Coluna pelo clique do usuário DESC ou ASC
     	$D024_Id = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D024_Id' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
     	$D024_Nome_Fantasia = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D024_Nome_Fantasia' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
     	$D020_Nome_Cidade = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D020_Nome_Cidade' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
     	$D018_UF = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D018_UF' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';

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
     	$D024_Id_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D024_Id') ? $iconeOrdenacao : '';
     	$D024_Nome_Fantasia_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D024_Nome_Fantasia') ? $iconeOrdenacao : '';
     	$D020_Nome_Cidade_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D020_Nome_Cidade') ? $iconeOrdenacao : '';
     	$D018_UF_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D018_UF') ? $iconeOrdenacao : '';

$filtro = '';
// Clientes
$sql = <<<EOT
	SELECT D024_Id, D024_Dia_Visita, D024_Nome_Fantasia, D024_Nome_Empresa, D024_Cep, D024_Endereco, D024_Numero, D024_Complemento, D024_Bairro, D020_Nome_Cidade, D018_UF, D024_Cnpj, D024_Cpf, D024_RG, D024_DDD_Telefone_1, D024_Telefone_1, (select D013_DDD_Telefone_1 from D013 where D013_D024_Id=D024_Id and D013_Telefone_1 != '' and D013_Telefone_1 is not null ORDER BY D013_Id ASC LIMIT 1) as D013_DDD_Telefone_1,(select D013_Telefone_1 from D013 where D013_D024_Id=D024_Id and D013_Telefone_1 != '' and D013_Telefone_1 is not null ORDER BY D013_Id ASC LIMIT 1) as D013_Telefone_1, Vendedor.C007_Primeiro_Nome as C007_Primeiro_Nome FROM D024 
	left join D020 on D020_Id=D024_D020_Id
	left join D018 on D018_Id=D024_D018_Id
	left join D084 on D084_Id=D020_D084_Id
	left join D085 on D085_Id=D024_D085_Id
	left join D046 on D046_Id=D024_D046_Id
	left join D021 on D021_Id=D024_D021_Id
	left join D116 on D024_Id=D116_D024_Id
	left join C007 as Vendedor on Vendedor.C007_Id=D024_C007_Vendedor_Interno
	left join C007 as Externo on Externo.C007_Id=D024_C007_Vendedor_Externo
	left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
	left join C007 as Supervisor2 on Supervisor2.C007_Id=Vendedor.C007_C007_Id_2
	left join C007 as Substituto on Substituto.C007_Id=Vendedor.C007_Id_Vendedor_Substituto
	WHERE ((1=1) AND ((D024_Id = '{$pesquisa['busca']}') OR (D024_Bairro LIKE '%{$pesquisa['busca']}%') OR (D020_Nome_Cidade LIKE '%{$pesquisa['busca']}%') OR (D024_Dia_Visita LIKE '%{$pesquisa['busca']}%') OR (D018_UF LIKE '%{$pesquisa['busca']}%') OR (D024_Nome_Empresa LIKE '%{$pesquisa['busca']}%') OR ( D024_Nome_Fantasia LIKE '%{$pesquisa['busca']}%')) AND D024_Flag_Ativo='S' AND (D024_C004_Id='{$g['empresaAtual']}' OR IFNULL(D024_C004_Id,0)=0))
	{$filtro}
	{$g['conf']->ini('extraSqlPadraoVisualizacaoClientes')}
	GROUP BY D024_Id
	ORDER BY {$g['C031']['definirOrdemDeOrdenacao']}
    LIMIT 0,100
EOT;
$res = mysql_query($sql);
while ($row = mysql_fetch_assoc($res)) {
	$linhaCliente = montaDadosCliente($row);
	echo <<<EOT
		<li data-theme="c" data-icon="false">
			<a href="./clientesEditar.php?D024_Id={$row['D024_Id']}&update=true" data-ajax="false">
				{$linhaCliente}
		    </a>
	    </li>
EOT;
}

echo <<<EOT
                </ul>
            </div>
    </div>
    <script>
    	$( document ).undelegate("#pageClientesProcurar", "pageinit");
    	$( document ).delegate("#pageClientesProcurar", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
    	});
    </script>
EOT;
mysql_free_result($res);
