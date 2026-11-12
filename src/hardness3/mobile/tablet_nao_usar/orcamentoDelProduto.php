<?php
namespace hardness;
$script = '';  

include('conf.php');
include('inc.conf.php');


// Vars
$D009_Id = isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : false;
$D001_Id = isset($_REQUEST['D001_Id']) ? $_REQUEST['D001_Id'] : false;
$T004_Id = isset($_REQUEST['T004_Id']) ? $_REQUEST['T004_Id'] : false;
$T003_Id = isset($_REQUEST['T003_Id']) ? $_REQUEST['T003_Id'] : false;
$T174_Id = isset($_REQUEST['T174_Id']) ? $_REQUEST['T174_Id'] : false;
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
$rowT004['T004_Valor_Preco_Original'] = gCorrigeNumero($rowT004['T004_Valor_Preco_Original']);
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
if (isset($_REQUEST['descricao']) || isset($_REQUEST['cotar'])) {
	
	//Busca o T004_Valor_Preco_Original, para validar se o preço inserido é menor que o preço original
	$precoOriginal = gCorrigeNumeroInverte($_REQUEST['precoOriginal']);
	$preco = gCorrigeNumeroInverte($_REQUEST['preco']);
	if($preco >= $precoOriginal){
	    $Descricao  = $_REQUEST['descricao'];
	    $quantidade = gCorrigeNumeroInverte($_REQUEST['quantidade']);
	    $preco      = gCorrigeNumeroInverte($_REQUEST['preco']);

	    $sqlAlterarProduto = "update T004 set T004_Descricao_Produto='{$Descricao}',T004_Quantidade='{$quantidade}',
     								 T004_Valor_Preco_Unitario='{$preco}', T004_Valor_Preco_Sem_Desconto_Unitario='{$precoOriginal}',T004_Valor_Preco_Original='{$precoOriginal}',
		                             T004_Percentual_Desconto=if(T004_Valor_Preco_Original>0,(1-(T004_Valor_Preco_Unitario/T004_Valor_Preco_Original))*100,0),
		                             T004_Valor_Desconto_Unitario=if(T004_Valor_Preco_Original>0,(T004_Valor_Preco_Original*(T004_Percentual_Desconto/100)),0)
			                      where T004_Id='{$T004_Id}'";

	   //echo $sqlAlterarProduto;
	   //die();
	    $sqlAlterarProduto = mysql_query($sqlAlterarProduto);
	    $retorno = mysql_error();
		//$T004_Id
		if (!empty($retorno)) {
			$temErro = '<h3 style="text-align: center;">'.$retorno.'</h3>';
		} else if(isset($_REQUEST['cotar'])){
			$script = <<<EOT
			$.mobile.changePage( './orcamentoProdutoCotar.php?T003_Id={$T003_Id}&T004_Id={$T004_Id}&D009_Id={$D009_Id}&T189_Flag_Tipo=C', { transition: 'slide'} );
EOT;
		} else {
			$script = <<<EOT
	  			$.mobile.changePage( "./orcamento.php?T003_Id={$T003_Id}&refresh={$uniqId}", { transition: 'slide'} );
EOT;
		}
	}else{
		$script = <<<EOT
	  				alert('O Preço do Produto não pode ser menor que o Preço Original ($preco<$precoOriginal)');
EOT;
	}
}

include('inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageorcamentoDelProduto">
EOT;
include('inc.header.bar.php');
$editarPreco = ($g['C031']['permiteAlterarPrecoVenda'] == 'S' || $g['nivelAlcada'] == '4') ? '' : 'readonly';
echo <<<EOT
			<div data-role="content">
        		{$temErro}
        		<form name="orcamento" action="./orcamentoDelProduto.php?T003_Id={$T003_Id}&T004_Id={$T004_Id}" method="POST" data-ajax="false">
        			<div class="ui-block-a" style="width:100%; height:10%;">
	        			<ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 5px; clear:both;">
	        				<li data-role="list-divider" role="heading" class="divider">
			    				<table style="width: 100%";>
			    					<tr>
			    						<td style="width: 5%; font-size: 16px;" align="center">Alterar produto orçamento</td>
			    					</tr>
			    				</table>
		                    </li>
	                    </ul>
        			</div>
        			<div class="ui-block-b" style="width:100%;">
		            	<div data-role="fieldcontain">
		                	<fieldset data-role="controlgroup">
	                            <label for="textinput3" style="width:10%;">
	                                Produto
	                            </label>
	                            <input id="textinput3" placeholder="" value="{$rowT004['T004_Codigo_Produto']}" type="text" style="opacity: 0.5;width:30%;" disabled="disabled" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="textinput4" style="width:10%;">
	                                Marca
	                            </label>
	                            <input id="textinput4" placeholder="" value="{$rowT004['D082_Marca']}" type="text" style="opacity: 0.5;width:30%;" disabled="disabled" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="descricao" style="width:10%;">
	                                Descrição
	                            </label>
	                            <input name="descricao" id="descricao" placeholder="" value="{$rowT004['T004_Descricao_Produto']}" type="text" style="width:80%;"/>
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="quantidade" style="width:10%;">
	                                Quantidade
	                            </label>
	                            <input name="quantidade" id="quantidade" placeholder="" value="{$rowT004['T004_Quantidade']}" type="number" onkeypress="return(Verifica_Caracter(event, this));" style="width:30%;"/>
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="precoOriginal" style="width:10%;">
	                                Preço Original
	                            </label>
	                            <input name="precoOriginal" id="precoOriginal" placeholder="" value="{$rowT004['T004_Valor_Preco_Original']}" type="text" style="opacity: 0.5;width:30%;"/>
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="preco" style="width:10%;">
	                                Preço
	                            </label>
	                            <input name="preco" id="preco" placeholder="" value="{$rowT004['T004_Valor_Preco_Unitario']}" type="text" style="width:30%;" {$editarPreco}/>
	                        </fieldset>
                    	</div>
                    </div>
	                <!--
	                <div class="ui-block-b" style="width: 100%; text-align: center;">
	                	{$image}
	                </div>
	                -->
	                <div class="ui-block-c" style="width:100%;">
		                <div class="ui-grid-a">
		                    <div class="ui-block-a" style="width:20%">
		                        <a data-role="button" data-transition="slide" onclick="\$.mobile.changePage( './orcamento.php?T003_Id={$T003_Id}', { transition: 'slide'} );">
		                            Voltar
		                        </a>
		                    </div>
		                    <div class="ui-block-b" style="width:20%">
		                        <a rel="external" data-ajax="false" data-role="button" data-transition="slide"{$link}>
		                            Link
		                        </a>
		                    </div>
		                    <div class="ui-block-c" style="width:20%">
		                        <input value="Alterar" type="submit" onclick="if ($('.ui-listview .ui-body-e').size() == 0 && {$rowsT174} > 0) { alert('Selecione uma cotação para continuar'); return false; } else if($('#quantidade').val() <= '0,00') { alert('A quantidade deve ser maior que zero'); return false; } "/>
							</div>
		                    <div class="ui-block-d" style="width:20%">
		                    	<a data-role="button" data-transition="slide" onclick="\$.mobile.changePage( './orcamentoProdutoRemover.php?T003_Id={$T003_Id}&T004_Id={$T004_Id}', { transition: 'slide'} );">
		                            Excluir
		                        </a>
		                    </div>
						</div>
                </form>
            </div>
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
