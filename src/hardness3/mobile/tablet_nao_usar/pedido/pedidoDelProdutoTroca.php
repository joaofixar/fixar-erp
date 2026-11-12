<?php
namespace hardness;
$script = '';  

include('conf.php');
include('inc.conf.php');

// Vars
$D009_Id = isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : false;
$D001_Id = isset($_REQUEST['D001_Id']) ? $_REQUEST['D001_Id'] : false;
$T138_Id = isset($_REQUEST['T138_Id']) ? $_REQUEST['T138_Id'] : false;

// Select T138_Id
$sqlT138 = "select * from T138
	left join D009 on D009_Id=T138_D009_Id
	left join D049 on D049_Id=D009_D049_Id
	left join D001 on D001_Id=D009_D001_Id
	left join D082 on D082_Id=D049_D082_Id where T138_Id='{$T138_Id}'";
$resultadoT138 = mysql_query($sqlT138);
$rowT138 = mysql_fetch_array($resultadoT138);
$rowT138['T138_Quantidade'] = gCorrigeNumero($rowT138['T138_Quantidade']);
$rowT138['T138_Valor_Preco_Unitario'] = gCorrigeNumero($rowT138['T138_Valor_Preco_Unitario']);

// Copiado de ven001/form_func-ajax/atualizarCamposProdutos/
$sql =	"SELECT T005_D024_Id from T005 WHERE T005_Id = '{$T005_Id}'";
$resultado = mysql_query($sql);
$row = mysql_fetch_assoc($resultado);

// Imagem
$sql = "SELECT * FROM T172 WHERE (T172_D001_Id = '{$D001_Id}') LIMIT 0,1;";
$res3 = mysql_query($sql);
if (mysql_numrows($res3) > 0) {
	$row3 = mysql_fetch_assoc($res3);
	$idFoto = $row3['T172_Id'];
	$idProduto = $D001_Id;
	$pathinfo = pathinfo($row3['T172_Nome_Arquivo']);
	$cArquivo = "{$g['pathRaiz']}dados_usuarios/{$confUsuario['dbDatabase']}/produtos/{$idProduto}/fotos/{$idFoto}.{$pathinfo['extension']}";
	$cArquivoWeb = "/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/produtos/{$idProduto}/fotos/{$idFoto}.{$pathinfo['extension']}";
	if (file_exists($cArquivo))
	{
		$image = "<img src='{$cArquivoWeb}' style='max-width: 300px; max-height: 300px;' />";
	}
} else {
	$image = '';
}

// Link
$sql = "SELECT * FROM T144 LEFT JOIN D001 ON D001_Id=T144_D001_Id WHERE (T144_D001_Id = '{$D001_Id}') ORDER BY T144_Data_Cadastro DESC";
$res4 = mysql_query($sql);
if (mysql_numrows($res4) > 0) {
	$row4 = mysql_fetch_assoc($res4);
	$link = " href=\"{$row4['T144_Url']}\"";
} else {
	$link = '';
}

// Update
$temErro = '';
if (isset($_REQUEST['T138_Quantidade'])) {
    $Descricao  = $_REQUEST['descricao'];
    $quantidade = gCorrigeNumeroInverte($_REQUEST['quantidade']);
    $preco      = gCorrigeNumeroInverte($_REQUEST['preco']);
    
    $T138_Quantidade = strtoupper(mysql_real_escape_string($_REQUEST['T138_Quantidade']));
	$T138_Valor_Preco_Unitario = mysql_real_escape_string(gCorrigeNumeroInverte($_REQUEST['T138_Valor_Preco_Unitario']));
	$T138_Operacao = strtoupper(mysql_real_escape_string($_REQUEST['T138_Operacao']));
    
    $sqlAlterarProduto = mysql_query("UPDATE T138 SET T138_Quantidade = '{$T138_Quantidade}', T138_Valor_Preco_Unitario = '{$T138_Valor_Preco_Unitario}', T138_Operacao = '{$T138_Operacao}' WHERE T138_Id='{$T138_Id}'");
    $retorno = mysql_error();
	//$T138_Id
	if (!empty($retorno)) {
		$temErro = '<h3 style="text-align: center;">'.$retorno.'</h3>';
	} else {
		$script = <<<EOT
  			$.mobile.changePage( "./pedido.php?T005_Id={$T005_Id}{$acao}", { transition: 'slide'} );
EOT;
	}
}

$T138_Operacao = array(
	array('title' => 'Parte Pagamento', 'value' => 'P', 'selected' => $rowT138['T138_Operacao'] == 'P' ? true : false),
	array('title' => 'Troca', 'value' => 'T', 'selected' => $rowT138['T138_Operacao'] == 'T' ? true : false),
);
$T138_Operacao = gProcessaSelect($T138_Operacao);

include('mobile/tablet/inc.header.php');
echo <<<EOT
	<div data-role="page" id="pagepedidoDelProdutoTroca">
EOT;
include('mobile/tablet/pedido/inc.header.bar.php');
echo <<<EOT
        <div data-role="content">
        {$temErro}
			<form action="./pedidoDelProdutoTroca.php?T005_Id={$T005_Id}&T138_Id={$T138_Id}{$acao}" method="POST" data-ajax="false">
                 <div class="ui-grid-a" style="width:100%;">
			        <ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 5px;">
			        	<li data-role="list-divider" role="heading" class="divider">
			    			<table style="width: 100%";>
			    				<tr>
			    					<td style="width: 5%; font-size: 16px;" align="center">Alterar troca pedido</td>
			    				</tr>
			    			</table>
	                    </li>
                    </ul>
				</div>
                <div class="ui-grid-b">
					<div class="ui-block-a" style="width: 60%;">
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="textinput3">
	                                Produto
	                            </label>
	                            <input id="textinput3" placeholder="" value="{$rowT138['D001_Codigo_Produto']}" type="text" style="opacity: 0.5;" disabled="disabled" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="textinput4">
	                                Marca
	                            </label>
	                            <input id="textinput4" placeholder="" value="{$rowT138['D082_Marca']}" type="text" style="opacity: 0.5;" disabled="disabled" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="descricao">
	                                Descrição
	                            </label>
	                            <input name="descricao" id="descricao" placeholder="" value="{$rowT138['D001_Descricao_Produto']}" type="text" style="opacity: 0.5;" disabled="disabled" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T138_Quantidade">
	                                Quantidade
	                            </label>
	                            <input name="T138_Quantidade" id="T138_Quantidade" placeholder="" value="{$rowT138['T138_Quantidade']}" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T138_Valor_Preco_Unitario">
	                                Preço
	                            </label>
	                            <input name="T138_Valor_Preco_Unitario" id="T138_Valor_Preco_Unitario" placeholder="" value="{$rowT138['T138_Valor_Preco_Unitario']}" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <label for="T138_Operacao">
								Operação
	                        </label>
							<select name="T138_Operacao" id="T138_Operacao">
								{$T138_Operacao}
		                    </select>
	                    </div>
	                </div>
	                <div class="ui-block-b" style="width: 40%; text-align: center;">
	                	{$image}
	                </div>
                </div>
                <div class="ui-grid-c">
                    <div class="ui-block-a">
                        <a data-role="button" data-transition="fade" href="./pedido.php?T005_Id={$T005_Id}{$acao}" data-ajax="false">
                            Voltar
                        </a>
                    </div>
                    <div class="ui-block-b">
                        <a rel="external" data-ajax="false" data-role="button" data-transition="slide"{$link}>
                            Link
                        </a>
                    </div>
                    <div class="ui-block-c">
                        <input type="submit" value="Alterar" />
                    </div>
                    <div class="ui-block-d">
                    	<a data-role="button" data-transition="fade" href="./pedidoProdutoRemoverTroca.php?T005_Id={$T005_Id}&T138_Id={$T138_Id}{$acao}">
                            Remover
                        </a>
                    </div>
                </div>
			</form>
		</div>
EOT;
include('inc.footer.php');
echo <<<EOT
    </div>
    <script>
    	$( document ).undelegate("#pagepedidoDelProdutoTroca", "pageinit");
    	$( document ).delegate("#pagepedidoDelProdutoTroca", "pageinit", function() {
EOT;

echo <<<EOT
			{$script}
    	});
    </script>
EOT;
