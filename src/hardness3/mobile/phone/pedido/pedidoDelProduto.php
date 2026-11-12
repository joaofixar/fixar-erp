<?php
namespace hardness;
$script = '';  

include('conf.php');
include('inc.conf.php');

// Vars
$D009_Id = isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : false;
$D001_Id = isset($_REQUEST['D001_Id']) ? $_REQUEST['D001_Id'] : false;
$T006_Id = isset($_REQUEST['T006_Id']) ? $_REQUEST['T006_Id'] : false;
$uniqId = uniqid();

// Select T006
$sqlT006 = "select * from T006 
	                   left join D009 on D009_Id=T006_D009_Id
	                   left join D049 on D049_Id=D009_D049_Id
	                   left join D082 on D082_Id=D049_D082_Id where T006_Id='{$T006_Id}'";
$resultadoT006 = mysql_query($sqlT006);
$rowT006 = mysql_fetch_array($resultadoT006);
$rowT006['T006_Quantidade'] = gCorrigeNumero($rowT006['T006_Quantidade']);
$rowT006['T006_Valor_Preco_Unitario'] = gCorrigeNumero($rowT006['T006_Valor_Preco_Unitario']);
$rowT006['T006_Descricao_Produto'] = htmlentities($rowT006['T006_Descricao_Produto']);
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
if (isset($_REQUEST['descricao'])) {
    $Descricao  = $_REQUEST['descricao'];
    $quantidade = gCorrigeNumeroInverte($_REQUEST['quantidade']);
    $preco      = gCorrigeNumeroInverte($_REQUEST['preco']);

    $sqlAlterarProduto = mysql_query("update T006 set 	T006_Descricao_Produto='{$Descricao}',T006_Quantidade='{$quantidade}',
    								T006_Valor_Preco_Unitario='{$preco}', T006_Valor_Preco_Sem_Desconto_Unitario='{$preco}',
		                                T006_Percentual_Desconto=if(T006_Valor_Preco_Original>0,(1-(T006_Valor_Preco_Unitario/T006_Valor_Preco_Original))*100,0),
		                                T006_Valor_Desconto_Unitario=if(T006_Valor_Preco_Original>0,(T006_Valor_Preco_Original*(T006_Percentual_Desconto/100)),0)
		                      where T006_Id='{$T006_Id}'");
    $retorno = mysql_error();
	//$T006_Id
	if (!empty($retorno)) {
		$temErro = '<h3 style="text-align: center;">'.$retorno.'</h3>';
	} else {
		$script = <<<EOT
  			$.mobile.changePage( "./pedido.php?T005_Id={$T005_Id}&refresh={$uniqId}", { transition: 'slide'} );
EOT;
	}
}

include('mobile/tablet/inc.header.php');
echo <<<EOT
	<div data-role="page" id="pagepedidoDelProduto">
EOT;
include('mobile/tablet/pedido/inc.header.bar.php');
//Verifica se pode ser alterado o item do pedido
$restricao = ($g['conf']->restricaoTravar('alterar', 'T006_Id', $T006_Id) === true && isset($g['c029Ids'][213])) ? '' : 'disabled="disabled"';
$editarPreco = ($g['C031']['permiteAlterarPrecoVenda'] == 'S' || $g['nivelAlcada'] == '4') ? '' : 'readonly';
echo <<<EOT
        	<div data-role="content">
        		{$temErro}
                 <form action="./pedidoDelProduto.php?T005_Id={$T005_Id}&T006_Id={$T006_Id}" method="POST" data-ajax="false">
	                    <div data-role="fieldcontain" style="padding: 2px 8px;">
	                        <fieldset data-role="controlgroup">
	                            <label for="textinput3" style="width:10%;">
	                                Produto
	                            </label>
	                            <input id="textinput3" placeholder="" value="{$rowT006['T006_Codigo_Produto']}" type="text" style="opacity: 0.5;" disabled="disabled" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain" style="padding: 2px 8px;">
	                        <fieldset data-role="controlgroup">
	                            <label for="textinput4" style="width:10%;">
	                                Marca
	                            </label>
	                            <input id="textinput4" placeholder="" value="{$rowT006['D082_Marca']}" type="text" style="opacity: 0.5;" disabled="disabled" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain" style="padding: 2px 8px;">
	                        <fieldset data-role="controlgroup">
	                            <label for="descricao" style="width:10%;">
	                                Descrição
	                            </label>
	                            <input name="descricao" id="descricao" placeholder="" value="{$rowT006['T006_Descricao_Produto']}" type="text" {$restricao} />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain" style="padding: 2px 8px;">
	                        <fieldset data-role="controlgroup">
	                            <label for="quantidade" style="width:10%;">
	                                Quantidade
	                            </label>
	                            <input name="quantidade" id="quantidade" placeholder="" value="{$rowT006['T006_Quantidade']}" type="text" onkeypress="return(Verifica_Caracter(event, this));" style="width:30%; text-align: right;" {$restricao} />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain" style="padding: 2px 8px;">
	                        <fieldset data-role="controlgroup">
	                            <label for="preco" style="width:10%;">
	                                Preço
	                            </label>
	                            <input name="preco" id="preco" placeholder="" value="{$rowT006['T006_Valor_Preco_Unitario']}" type="text" style="width:30%; text-align: right;" {$editarPreco} {$restricao} />
	                        </fieldset>
	                    </div>
	                <!--
	                <div class="ui-block-b" style="width: 40%; text-align: center;">
	                	{$image}
	                </div>
	                -->
                <div style="text-align: center;">
                    <div style="width: 50%; float: left;">
                        <a data-role="button" data-transition="slide" onclick="\$.mobile.changePage( './pedido.php?T005_Id={$T005_Id}', { transition: 'slide'} );">
                            Voltar
                        </a>
                    </div>
                    <div style="width: 50%; float: left;">
                        <a rel="external" data-ajax="false" data-role="button" data-transition="slide"{$link}>
                            Link
                        </a>
                    </div>
                </div>
                <div style="text-align: center;">
                    <div style="width: 50%; float: left;">
                        <input type="submit" value="Alterar" {$restricao} />
                    </div>
                    <div style="width: 50%; float: left;">
                    	<input type="button" data-role="button" data-transition="slide" onclick="\$.mobile.changePage( './pedidoProdutoRemover.php?T005_Id={$T005_Id}&T006_Id={$T006_Id}', { transition: 'slide'} );" value="Remover" {$restricao} />
                    </div>
                </div>


                </form>
            </div>
EOT;
include('inc.footer.php');
echo <<<EOT
    </div>
    <script>
    	$( document ).undelegate("#pagepedidoDelProduto", "pageinit");
    	$( document ).delegate("#pagepedidoDelProduto", "pageinit", function() {
EOT;

echo <<<EOT
			{$script}
    	});
    </script>
EOT;
