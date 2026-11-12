<?php
namespace hardness;
$script = '';  

include('conf.php');
include('inc.conf.php');

// Vars
$D009_Id = isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : false;
$D001_Id = isset($_REQUEST['D001_Id']) ? $_REQUEST['D001_Id'] : false;
$T004_Id = isset($_REQUEST['T004_Id']) ? $_REQUEST['T004_Id'] : false;
$uniqId = uniqid();


// Select T004
$sqlT004 = "select * from T004 
	                   left join D009 on D009_Id=T004_D009_Id
	                   left join D049 on D049_Id=D009_D049_Id
	                   left join D082 on D082_Id=D049_D082_Id where T004_Id='{$T004_Id}'";
$resultadoT004 = mysql_query($sqlT004);
$rowT004 = mysql_fetch_array($resultadoT004);
$rowT004['T004_Quantidade'] = gCorrigeNumero($rowT004['T004_Quantidade']);
$rowT004['T004_Valor_Preco_Unitario'] = gCorrigeNumero($rowT004['T004_Valor_Preco_Unitario']);
$rowT004['T004_Descricao_Produto'] = htmlentities($rowT004['T004_Descricao_Produto']);

// Copiado de ven001/form_func-ajax/atualizarCamposProdutos/
$sql =	"SELECT T003_D024_Id from T003 WHERE T003_Id = '{$T003_Id}'";
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

    $sqlAlterarProduto = mysql_query("update T004 set 	T004_Descricao_Produto='{$Descricao}',T004_Quantidade='{$quantidade}',
    								T004_Valor_Preco_Unitario='{$preco}',
		                                T004_Percentual_Desconto=if(T004_Valor_Preco_Original>0,(1-(T004_Valor_Preco_Unitario/T004_Valor_Preco_Original))*100,0),
		                                T004_Valor_Desconto_Unitario=if(T004_Valor_Preco_Original>0,(T004_Valor_Preco_Original*({$preco}/100)),0)
		                      where T004_Id='{$T004_Id}'");
    $retorno = mysql_error();
	//$T004_Id
	if (!empty($retorno)) {
		$temErro = '<h3 style="text-align: center;">'.$retorno.'</h3>';
	} else {
		$script = <<<EOT
  			$.mobile.changePage( "./orcamento.php?T003_Id={$T003_Id}&refresh={$uniqId}", { transition: 'slide'} );
EOT;
	}
}

include('inc.header.php');
$editarPreco = ($g['C031']['permiteAlterarPrecoVenda'] == 'S' || $g['nivelAlcada'] == '4') ? '' : 'readonly';
echo <<<EOT
	<div data-role="page" id="pageorcamentoDelProduto">
EOT;
echo <<<EOT
        	<div data-role="content">
        		{$temErro}
                 <form action="./orcamentoDelProduto.php?T003_Id={$T003_Id}&T004_Id={$T004_Id}" method="POST" data-ajax="false">
	                    <div data-role="fieldcontain" style="padding: 2px 8px;">
	                        <fieldset data-role="controlgroup">
	                            <label for="textinput3" style="width:10%;">
	                                Produto
	                            </label>
	                            <input id="textinput3" placeholder="" value="{$rowT004['T004_Codigo_Produto']}" type="text" style="opacity: 0.5;" disabled="disabled" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain" style="padding: 2px 8px;">
	                        <fieldset data-role="controlgroup">
	                            <label for="textinput4" style="width:10%;">
	                                Marca
	                            </label>
	                            <input id="textinput4" placeholder="" value="{$rowT004['D082_Marca']}" type="text" style="opacity: 0.5;" disabled="disabled" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain" style="padding: 2px 8px;">
	                        <fieldset data-role="controlgroup">
	                            <label for="descricao" style="width:10%;">
	                                Descrição
	                            </label>
	                            <input name="descricao" id="descricao" placeholder="" value="{$rowT004['T004_Descricao_Produto']}" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain" style="padding: 2px 8px;">
	                        <fieldset data-role="controlgroup">
	                            <label for="quantidade" style="width:10%;">
	                                Quantidade
	                            </label>
	                            <input name="quantidade" id="quantidade" placeholder="" value="{$rowT004['T004_Quantidade']}" type="text" onkeypress="return(Verifica_Caracter(event, this));" style="width:30%; text-align: right;"/>
	                        </fieldset>
	                        <fieldset data-role="controlgroup">
	                            <label for="preco" style="width:10%;">
	                                Preço
	                            </label>
	                            <input name="preco" id="preco" placeholder="" value="{$rowT004['T004_Valor_Preco_Unitario']}" type="text" style="width:30%; text-align: right;" {$editarPreco}/>
	                        </fieldset>
	                    </div>
	                <!--
	                <div class="ui-block-b" style="width: 40%; text-align: center;">
	                	{$image}
	                </div>
	                -->
                <div style="text-align: center;">
                    <div style="width: 50%; float: left;">
                        <a data-role="button" data-transition="slide" onclick="\$.mobile.changePage( './orcamento.php?T003_Id={$T003_Id}', { transition: 'slide'} );">
                            Voltar
                        </a>
                    </div>
                    <div style="width: 50%; float: left;">
                        <a rel="external" data-ajax="false" data-role="button" data-transition="slide"{$link}target="_blank">
                            Link
                        </a>
                    </div>
                </div>
                <div style="text-align: center;">
                    <div style="width: 50%; float: left;">
                        <input type="submit" value="Alterar" />
                    </div>
                    <div style="width: 50%; float: left;">
                    	<a data-role="button" data-transition="slide" onclick="\$.mobile.changePage( './orcamentoProdutoRemover.php?T003_Id={$T003_Id}&T004_Id={$T004_Id}', { transition: 'slide'} );">
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
    	$( document ).undelegate("#pageorcamentoDelProduto", "pageinit");
    	$( document ).delegate("#pageorcamentoDelProduto", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
			{$script}
    	});
    </script>
EOT;
