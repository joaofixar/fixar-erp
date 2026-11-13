<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

// Vars
$pesquisa = isset($_REQUEST['pesquisa']) ? $_REQUEST['pesquisa'] : '';
$tipo = isset($_REQUEST['tipo']) ? $_REQUEST['tipo'] : false;

if(!empty($T005['D024_Flag_Lista_Preco'])){
	$campoListaPreco = "D009_Preco_{$T005['D024_Flag_Lista_Preco']}";
} else {
	$campoListaPreco = "D009_Preco_1";
}

include('mobile/phone/inc.header.php');
echo <<<EOT
	<div data-role="page" id="pagepedidoProcurarProduto">
EOT;
echo <<<EOT
        	<div data-role="content">
                <div class="ui-grid-a" style="margin-top: -4px;">
                	<div class="ui-block-a" style="text-align: center;">
                		<a data-role="button" data-inline="true" data-transition="fade"  href="./pedido.php?T005_Id={$T005_Id}{$acao}" data-ajax="false">
                            Voltar
                        </a>
                        <!--<a data-role="button" data-inline="true" data-transition="fade" href="./pedidoPreCadProd.php?tipo={$tipo}&T005_Id={$T005_Id}&descricaoProduto={$_REQUEST['pesquisa']}" data-ajax="false">
							Pré Cadastrar Produto
	                    </a>-->
                	</div>
                    <div class="ui-block-b" style="text-align: center;">
	                    <a data-role="button" data-inline="true" data-transition="fade" href="./pedidoAddListaProdutos.php?T005_Id={$T005_Id}{$acao}&incluirProdutos=true" data-ajax="false">
							Incluir
	                    </a>
                    </div>
                </div>
                <div class="ui-grid-a" style="margin-top: -3px; margin-bottom: 2px;">
                	<div>
                    	<div style="width: 87%; float:left; margin-top: 3px;">
                    		<input placeholder="Procurar Produto" data-type="search" value="{$_REQUEST['pesquisa']}" class="ui-input-text ui-body-c" id="procurarProduto2">
                    	</div>
                    	<div style="float:left;">
                    		<a data-role="button" data-transition="slide" data-icon="search" data-iconpos="notext" id="adicionarProduto2" onclick="\$.mobile.changePage( './pedidoProcurarProduto.php?T005_Id={$T005_Id}{$acao}&tipo={$tipo}&pesquisa=' + encodeURIComponent($('#procurarProduto2').val()), { transition: 'slide'} )" data-ajax="false"></a>
				        </div>
                    </div>
                </div>
                <ul data-role="listview" style="margin-top: 1px;">
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
	SELECT D001_Id, D009_Id, D001_Flag_Pre_Cadastro, D001_Codigo_Produto, D001_Descricao_Produto, D082_Marca, D009_Quantidade_Estoque_Liquido, D001_Descricao_Ingles, T266_Valor_Preco_Sem_Desconto_Unitario, T266_Quantidade,
	D009_Preco_1, D009_Preco_2, D009_Preco_3, D009_Preco_4
	FROM D001
	left join D049 on D049_D001_Id=D001_Id left join D009 on D009_D049_Id=D049_Id left join D082 on D082_Id=D049_D082_Id left join D037 on D037_Id=D001_D037_Id
	LEFT JOIN T266 ON T266_D009_Id=D009_Id AND T266_C007_Id = '{$g['usuarioAtual']}' AND T266_D024_Id = '{$T005['T005_D024_Id']}'
	WHERE {$extra} AND (D009_C004_Id = '{$g['empresaAtual']}' and D001_Flag_Ativo='S') {$g['conf']->ini('extraProdutosTablet')}
	GROUP BY D009_Id
	ORDER BY D001_Descricao_Produto LIMIT 0,100
EOT;
$res = mysql_query($sql);
if(mysql_num_rows($res) == '0'){
	echo "<span style='margin-left:40%; font-weight:bold; font-size:20px;'>Produto não encontrado</span>";
}
require_once("bibliotecas/classes/CAD002.php");
$CAD002 = new CAD002();
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
	$row['T266_Quantidade'] = ($row['T266_Quantidade'] > 0) ? gCorrigeNumero($row['T266_Quantidade']) : '';
	$row['D001_Descricao_Produto'] = ($row['D001_Flag_Pre_Cadastro'] == "S" ? "{$row['D001_Descricao_Produto']} (PRÉ)" : $row['D001_Descricao_Produto']);
	if($row['T266_Valor_Preco_Sem_Desconto_Unitario'] > 0) {
		$row['T266_Valor_Preco_Sem_Desconto_Unitario'] = gCorrigeNumero($row['T266_Valor_Preco_Sem_Desconto_Unitario']);
	} else if($row[$campoListaPreco] > 0) {
		$row['T266_Valor_Preco_Sem_Desconto_Unitario'] = gCorrigeNumero($row[$campoListaPreco]);
	} else {
		$row['T266_Valor_Preco_Sem_Desconto_Unitario'] = '';
	}

	if($g['C031']['listarProdutosComPrecoZero'] == 'N' && gCorrigeNumeroInverte($row['T266_Valor_Preco_Sem_Desconto_Unitario']) <= 0){
		continue;
	}

	$T005['D024_Flag_Lista_Preco'] = empty($T005['D024_Flag_Lista_Preco']) ? '1' : $T005['D024_Flag_Lista_Preco'];
	$preco = $CAD002->retornaPrecoVenda($row['D009_Id'], $T005['D024_Flag_Lista_Preco']);
	$row['T266_Valor_Preco_Sem_Desconto_Unitario'] = $preco[0];

	$editarPreco = ($g['C031']['permiteAlterarPrecoVenda'] == 'S' || $g['nivelAlcada'] == '4') ? '' : 'readonly';
		echo <<<EOT
			<li data-theme="c" data-icon="false" style="padding: 2px 8px;">
	    		<div style="width: 100%; clear: both; text-align: left; margin-bottom: 4px; white-space: normal;">{$row['D001_Codigo_Produto']}&nbsp; - &nbsp;{$row['D001_Descricao_Produto']}&nbsp; - &nbsp;{$row['D082_Marca']}</div>
				<div style="width: 100%; clear: both; text-align: left; margin-bottom: 2px; white-space: normal;">Físico: {$row['D009_Quantidade_Estoque']}&nbsp; - &nbsp;Líquido: {$row['D009_Quantidade_Estoque_Liquido']}</div>
	    		<div class="ui-grid-a" style="margin: 4px 0;">
                	<div class="ui-block-a" style="padding: 2px 0px;">
	    				<div style="margin-top: 8px; float: left; margin-right: 2px;">Qtde:</div>
	    				<input name="T266_Quantidade" data-inline="true" value="{$row['T266_Quantidade']}" type="text" style="width:45%; float: left; text-align:right" onblur="\$.mobile.loadPage( './ajax.php', {transition: 'fade', type:'post', data: 'acao=gravarDadosItemPedido&' + $(this).attr('name') + '=' + encodeURIComponent($(this).val()) + '&D009_Id={$row['D009_Id']}&C007_Id={$g['usuarioAtual']}&D024_Id={$T005['T005_D024_Id']}'});" onclick="$(this).select();"  />
	    			</div>
					<div class="ui-block-b" style="padding: 2px 0px;">
						<div style="margin-top: 8px; float: left; margin-right: 2px;">Preço R$:</div>
						<input name="T266_Valor_Preco_Sem_Desconto_Unitario" data-inline="true" value="{$row['T266_Valor_Preco_Sem_Desconto_Unitario']}" type="text" style="width:45%; float: left; text-align:right" onblur="\$.mobile.loadPage( './ajax.php', {transition: 'fade', type:'post', data: 'acao=gravarDadosItemPedido&' + $(this).attr('name') + '=' + encodeURIComponent($(this).val()) + '&D009_Id={$row['D009_Id']}&C007_Id={$g['usuarioAtual']}&D024_Id={$T005['T005_D024_Id']}'});" onclick="$(this).select();" {$editarPreco}/>
					</div>
				</div>
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
    	$( document ).undelegate("#pagepedidoProcurarProduto", "pageinit");
    	$( document ).delegate("#pagepedidoProcurarProduto", "pageinit", function() {
EOT;

echo <<<EOT
    	});
    </script>
EOT;
