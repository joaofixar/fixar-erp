<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

// Vars
$pesquisa = isset($_REQUEST['pesquisa']) ? $_REQUEST['pesquisa'] : '';
$tipo = isset($_REQUEST['tipo']) ? $_REQUEST['tipo'] : false;

include('mobile/tablet/inc.header.php');
echo <<<EOT
	<div data-role="page" id="pagecaixaProcurarProduto">
EOT;
include('mobile/tablet/caixa/inc.header.bar.php');
echo <<<EOT
        	<div data-role="content">
                <div class="ui-grid-a" style="margin-top: 7px; margin-bottom: 5px;">
                	<div class="ui-block-a">
                		<a data-role="button" data-inline="true" data-transition="fade" href="./caixa.php?T005_Id={$T005_Id}" data-ajax="false">
                            Voltar
                        </a>
                        <a data-role="button" data-inline="true" data-transition="fade" href="./caixaPreCadProd.php?tipo={$tipo}&T005_Id={$T005_Id}&descricaoProduto={$_REQUEST['pesquisa']}" data-ajax="false">
							Pré Cadastrar Produto
	                    </a>
                	</div>
                    <div class="ui-block-b">
                    	<div style="float:right;">
                    		<a data-role="button" data-transition="slide" data-icon="plus" data-iconpos="left" id="adicionarProduto2" onclick="\$.mobile.changePage( './caixaProcurarProduto.php?T005_Id={$T005_Id}&tipo={$tipo}&pesquisa=' + encodeURIComponent($('#procurarProduto2').val()), { transition: 'slide'} )" data-ajax="false">
			            		Procurar
				            </a>
				        </div>
                    	<div style="width: 70%; float:right; margin-top: 14px;">
                    		<input placeholder="Procurar Produto" data-type="search" value="{$_REQUEST['pesquisa']}" class="ui-input-text ui-body-c" id="procurarProduto2">
                    	</div>
                    </div>
                </div>
                <ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 1px;">
                    <li data-role="list-divider" role="heading">
	    				<table style="width: 100%";>
	    					<tr>
	    						<td style="width: 0%";></td>
	    						<td style="width: 5%";>Código</td>
	    						<td style="width: 50%";>Descrição</td>
	    						<td style="width: 20%";>Marca</td>
	    						<td style="width: 10%"; align="right">Estoque</td>
	    						<td style="width: 15%"; align="right">Preço R$</td>
	    					</tr>
	    				</table>
                    </li>
EOT;

// Produtos
$pesquisa_ = explode(' ', $pesquisa);
if (count($pesquisa_) == 0) {
	$extra = <<<EOT
		(
		(D009_Id LIKE '%{$pesquisa}%') OR 
		(D001_Codigo_Produto LIKE '%{$pesquisa}%') OR 
		(D001_Descricao_Produto LIKE '%{$pesquisa}%') OR 
		(D082_Marca LIKE '%{$pesquisa}%') OR 
		(D009_Quantidade_Estoque_Liquido LIKE '%{$pesquisa}%') OR 
		(D001_Descricao_Ingles LIKE '%{$pesquisa}%')
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
	$tmp .= ')';
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
	SELECT D001_Id, D009_Id,D001_Flag_Pre_Cadastro, D001_Codigo_Produto, D001_Descricao_Produto, D082_Marca, D009_Quantidade_Estoque_Liquido, D001_Descricao_Ingles, D041_Valor_Base 
	FROM D001 
	left join D049 on D049_D001_Id=D001_Id left join D009 on D009_D049_Id=D049_Id left join D082 on D082_Id=D049_D082_Id left join D037 on D037_Id=D001_D037_Id left join D041 on D041_D049_Id=D049_Id
	WHERE {$extra} AND (D009_C004_Id = '{$g['empresaAtual']}' and D001_Flag_Ativo='S') 
	GROUP BY D009_Id 
	ORDER BY D001_Descricao_Produto LIMIT 0,100
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
	$row['D041_Valor_Base'] = gCorrigeNumero($row['D041_Valor_Base']);
	$row['D001_Descricao_Produto'] = ($row['D001_Flag_Pre_Cadastro'] == "S" ? "{$row['D001_Descricao_Produto']} (PRÉ)" : $row['D001_Descricao_Produto']);
	
	if ($tipo == 'troca') {
		echo <<<EOT
			<li data-theme="c" data-icon="false">
				<a href="./caixaAddProdutoTroca.php?T005_Id={$T005_Id}&D009_Id={$row['D009_Id']}&D001_Id={$row['D001_Id']}" data-ajax="false" data-transition="fade">
EOT;
	} else {
		echo <<<EOT
			<li data-theme="c" data-icon="false">
				<a href="./caixaAddProduto.php?T005_Id={$T005_Id}&D009_Id={$row['D009_Id']}&D001_Id={$row['D001_Id']}" data-ajax="false" data-transition="slide">
EOT;
	}
	echo <<<EOT
	    		<table style="width: 100%";>
	    			<tr>
	    				<td style="width: 0%";>{$image}</td>
	    				<td style="width: 5%";>{$row['D001_Codigo_Produto']}</td>
	    				<td style="width: 50%";>{$row['D001_Descricao_Produto']}</td>
	    				<td style="width: 20%";>{$row['D082_Marca']}</td>
	    				<td style="width: 10%"; align="right">{$row['D009_Quantidade_Estoque_Liquido']}</td>
	    				<td style="width: 15%"; align="right">R$ {$row['D041_Valor_Base']}</td>
	    			</tr>
	    		</table>
		    </a>
	    </li>
EOT;
}

echo <<<EOT
                </ul>
            </div>
EOT;
include('inc.footer.php');
echo <<<EOT
    </div>
    <script>
    	$( document ).undelegate("#pagecaixaProcurarProduto", "pageinit");
    	$( document ).delegate("#pagecaixaProcurarProduto", "pageinit", function() {
EOT;

echo <<<EOT
    	});
    </script>
EOT;
