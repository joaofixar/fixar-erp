<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

// Vars
//Parametros de Pesquisa e Coluna da Pesquisa selecionada em requisições
$arqFiltro = $g['pathDadosUsuario'] . 'tabletFiltro/produtos';
if (!file_exists($arqFiltro)) {
	$pesquisa = array('busca' => '', 'ordem' => 'D001_Descricao_Produto', 'colunaBusca' => '', 'tipoOrdenacao' => '', 'coluna' => '');
	file_put_contents($arqFiltro, serialize($pesquisa));
}
$pesquisa = unserialize(file_get_contents($arqFiltro));
if (isset($_REQUEST['pesquisa'])) {
	$pesquisa['busca'] = $_REQUEST['pesquisa'];
	file_put_contents($arqFiltro, serialize($pesquisa));
}

//Vars de Edição no Grid
$D041_Id = isset($_REQUEST['D041_Id']) ? $_REQUEST['D041_Id'] : '';
$D041_Valor_Base = isset($_REQUEST['D041_Valor_Base']) ? $_REQUEST['D041_Valor_Base'] : '';
$D001_Id = isset($_REQUEST['D001_Id']) ? $_REQUEST['D001_Id'] : '';

$tipo = isset($_REQUEST['tipo']) ? $_REQUEST['tipo'] : false;

//Editar Dados das Colunas
if($D001_Id != ''){
	$D041_Valor_Base = gCorrigeNumeroInverte($D041_Valor_Base);
	$sqlUpdate = "UPDATE D041 SET D041_Valor_Base = {$D041_Valor_Base} WHERE D041_Id = {$D041_Id}";
	mysql_query($sqlUpdate);
	$script ="alert('Alteração realizada com sucesso!');";
}

include('inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageProdutos">
EOT;

$etapa = 6;
include('inc.header.bar.php');
echo <<<EOT
        	<div data-role="content">
        		<div style="margin-top: 0px; margin-bottom: 5px; ">
	            <div>
					<div style="width: 85%; float:left; margin-top: 0px; margin-bottom: 7px;">
                		<input placeholder="Procurar Produto" data-type="search" class="ui-input-text ui-body-c" value="{$pesquisa['busca']}" id="procurarProduto">
                	</div>
                	<div style="float:left; margin: -2px 0 0 2px;">
				        <a data-role="button" data-transition="slide" data-icon="search" data-iconpos="notext" onclick="\$.mobile.changePage( './produtos.php', {transition: 'fade', type:'post', data: 'colunaPesquisa=' + encodeURIComponent($('#colunaPesquisa').val()) + '&pesquisa=' + encodeURIComponent($('#procurarProduto').val())} )" ></a>
				    </div>
	            </div>
        	    <ul data-role="listview" style="margin-top: 1px; clear:both;">
EOT;
		//Verifica Ordenação da Coluna pelo clique do usuário DESC ou ASC
		$D001_Codigo_Produto = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D001_Codigo_Produto' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$D001_Descricao_Produto = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D001_Descricao_Produto' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$D082_Marca = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D082_Marca' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$D009_Quantidade_Estoque_Liquido = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D009_Quantidade_Estoque_Liquido' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$D041_Valor_Base = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D041_Valor_Base' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		
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
		$D001_Codigo_Produto_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D001_Codigo_Produto') ? $iconeOrdenacao : '';
		$D001_Descricao_Produto_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D001_Descricao_Produto') ? $iconeOrdenacao : '';
		$D082_Marca_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D082_Marca') ? $iconeOrdenacao : '';
		$D009_Quantidade_Estoque_Liquido_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D009_Quantidade_Estoque_Liquido') ? $iconeOrdenacao : '';
		$D041_Valor_Base_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D041_Valor_Base') ? $iconeOrdenacao : '';

// Produtos
$pesquisa_ = explode(' ', $pesquisa['busca']);
if (count($pesquisa_) == 0) {
	$extra = <<<EOT
		(
		(D009_Id LIKE '%{$pesquisa['busca']}%') OR
		(D001_Codigo_Produto LIKE '%{$pesquisa['busca']}%') OR
		(D001_Descricao_Produto LIKE '%{$pesquisa['busca']}%') OR
		(D082_Marca LIKE '%{$pesquisa['busca']}%') OR
		(D009_Quantidade_Estoque_Liquido LIKE '%{$pesquisa['busca']}%') OR
		(D001_Descricao_Ingles LIKE '%{$pesquisa['busca']}%')
		)
EOT;
} else {
	$tmp = '(';
	foreach ($pesquisa_ as $word) {
		if ($tmp == '(') {
			$tmp .= "[%] LIKE '%{$word}%'";
		} else {
			$tmp .= " AND [%] LIKE '%{$word}%'";
		}
	}
	$tmp .= ')';-
	$extra = '(';
	$extra .= str_replace('[%]', 'D009_Id', $tmp) . ' OR';
	$extra .= str_replace('[%]', 'D001_Codigo_Produto', $tmp) . ' OR';
	$extra .= str_replace('[%]', 'D001_Descricao_Produto', $tmp) . ' OR';
	$extra .= str_replace('[%]', 'D082_Marca', $tmp) . ' OR';
	$extra .= str_replace('[%]', 'D009_Quantidade_Estoque_Liquido', $tmp) . ' OR';
	$extra .= str_replace('[%]', 'D001_Descricao_Ingles', $tmp);
	$extra .= ')';
}

$sql = <<<EOT
	SELECT D001_Id, D009_Id,D001_Flag_Pre_Cadastro, D001_Codigo_Produto, D001_Descricao_Produto, D082_Marca, D009_Quantidade_Estoque_Liquido, D009_Quantidade_Estoque, D009_Preco_1, D001_Descricao_Ingles, D041_Valor_Base, D041_Id 
	FROM D001
	left join D049 on D049_D001_Id=D001_Id left join D009 on D009_D049_Id=D049_Id left join D082 on D082_Id=D049_D082_Id left join D037 on D037_Id=D001_D037_Id left join D041 on D041_D049_Id=D049_Id
	WHERE {$extra}
EOT;
	//Verifica Se foi requisitada a pesquisa
	if(isset($pesquisa['busca']) && $pesquisa['busca']!=''){
		//Se a Coluna foi Selecionada faz pesquisa pela coluna, senão fz pesquisa geral
		if(isset($pesquisa['colunaBusca']) && $pesquisa['colunaBusca']!=''){
			$sql .= " AND {$pesquisa['colunaBusca']} LIKE '%{$pesquisa['busca']}%'";
		}
	}
$sql .= <<<EOT
	AND (D009_C004_Id = '{$g['empresaAtual']}' and D001_Flag_Ativo='S') {$g['conf']->ini('extraProdutosTablet')}
	GROUP BY D009_Id
	ORDER BY {$pesquisa['ordem']} LIMIT 0,100
EOT;
$res = mysql_query($sql);
if(mysql_num_rows($res) == '0'){
	echo "<span style='margin-left:40%; font-weight:bold; font-size:20px;'>Produto não encontrado</span>";
}
while ($row = mysql_fetch_assoc($res)) {
	$sql = "SELECT * FROM T172 WHERE (T172_D001_Id = '{$row['D001_Id']}') LIMIT 0,1;";
	$res2 = mysql_query($sql);
	if (mysql_numrows($res2) > 0) {
		$row2 = mysql_fetch_assoc($res2);
		$idFoto = $row2['T172_Id'];
		$idProduto = $row['D001_Id'];
		$pathinfo = pathinfo($row2['T172_Nome_Arquivo']);
		$cArquivo = "{$g['pathRaiz']}dados_usuarios/{$confUsuario['dbDatabase']}/produtos/{$idProduto}/fotos/{$idFoto}.{$pathinfo['extension']}";
		$cArquivoWeb = "/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/produtos/{$idProduto}/fotos/{$idFoto}.{$pathinfo['extension']}";
		if (file_exists($cArquivo))
		{
			$image = "<img src='{$cArquivoWeb}' style='width: 100px; height: 100px;' />";
		}
	} else {
		$image = '';
	}
	$row['D009_Quantidade_Estoque_Liquido'] = gCorrigeNumero($row['D009_Quantidade_Estoque_Liquido']);
	$row['D009_Preco_1'] = gCorrigeNumero($row['D009_Preco_1']);
	$row['D009_Quantidade_Estoque'] = gCorrigeNumero($row['D009_Quantidade_Estoque']);
	$row['D041_Valor_Base'] = gCorrigeNumero($row['D041_Valor_Base']);
	$row['D001_Descricao_Produto'] = ($row['D001_Flag_Pre_Cadastro'] == "S" ? "{$row['D001_Descricao_Produto']} (PRÉ)" : $row['D001_Descricao_Produto']);

	echo <<<EOT
		<li data-theme="c" data-icon="false" style="padding: 8px;">
			<div style="width: 100%; clear: both; text-align: left; margin-bottom: 2px; white-space: normal;">{$row['D001_Codigo_Produto']}&nbsp; - &nbsp;{$row['D001_Descricao_Produto']}&nbsp; - &nbsp;{$row['D082_Marca']}</div>
			<div style="width: 100%; clear: both; text-align: left; margin-bottom: 2px; white-space: normal;">Físico: {$row['D009_Quantidade_Estoque']}&nbsp; - &nbsp;Líquido: {$row['D009_Quantidade_Estoque_Liquido']}</div>
			<div style="width: 100%; clear: both; text-align: left; margin-bottom: 2px; white-space: normal;">Preço: {$row['D009_Preco_1']}</div>
	    </li>
EOT;
}

echo <<<EOT
                </ul>
            </div>
    </div>
    <script>
    	$( document ).undelegate("#pageProdutos", "pageinit");
    	$( document ).delegate("#pageProdutos", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
		{$script}
    	});
    </script>
EOT;
