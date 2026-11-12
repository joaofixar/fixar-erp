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
$rowT006['T006_Valor_Preco_Original'] = gCorrigeNumero($rowT006['T006_Valor_Preco_Original']);

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

//Autorizacao do Caixa para incluir produto no pedido (produto incluido pelo almoxarifado)

if(@$_REQUEST['autorizarProduto'] == true){
	$T207 = mysql_query("SELECT * FROM T207 LEFT JOIN T006 ON T006_Id = T207_T006_Id WHERE T207_T006_Id = '{$T006_Id}' AND T207_Flag_Status = '3'");
	if(mysql_num_rows($T207) > 0){
		$res = mysql_fetch_array($T207); 
		mysql_query("UPDATE T207 SET T207_Flag_Status = '2' WHERE T207_T006_Id = '{$T006_Id}'");

		$sql = mysql_query("SELECT * FROM T207 WHERE T207_Flag_Status = '3' AND T207_T005_Id = '{$res['T006_T005_Id']}'");
		if(mysql_num_rows($sql) == 0){
			mysql_query("UPDATE T005 SET T005_Flag_Status_Servicos = '1' WHERE T005_Id = '{$res['T006_T005_Id']}'");
		}
	}	
}

// Update
$temErro = '';
if (isset($_REQUEST['descricao'])) {
	//Busca o D041_Valor_Base, para validar se o preço inserido é menor que o preço original
	/*
	$sqlD041 = "SELECT D041_Valor_Base FROM D041 WHERE D041_D049_Id = {$rowT006['D009_D049_Id']}";
	$resultadoD041 = mysql_query($sqlD041);
	$rowD041 = mysql_fetch_assoc($resultadoD041);
	$precoOriginal = $rowD041['D041_Valor_Base'];
	 */
	 
	$Descricao      = $_REQUEST['descricao'];
	$quantidade    = gCorrigeNumeroInverte($_REQUEST['quantidade']);
	$precoOriginal = gCorrigeNumeroInverte($_REQUEST['precoOriginal']);
	$preco         = gCorrigeNumeroInverte($_REQUEST['preco']);

	if($preco>=$precoOriginal || $_REQUEST['cotar'] == 's'){

	    $sqlAltProduto = "update T006 set T006_Descricao_Produto='{$Descricao}',T006_Quantidade='{$quantidade}',
    							 T006_Valor_Preco_Unitario='{$preco}', T006_Valor_Preco_Sem_Desconto_Unitario='{$preco}', T006_Valor_Preco_Original='{$precoOriginal}',
                                 T006_Percentual_Desconto=if(T006_Valor_Preco_Original>0,(1-(T006_Valor_Preco_Unitario/T006_Valor_Preco_Original))*100,0),
                                 T006_Valor_Desconto_Unitario=if(T006_Valor_Preco_Original>0,(T006_Valor_Preco_Original*(T006_Percentual_Desconto/100)),0)
		                         where T006_Id='{$T006_Id}'";
		$result = mysql_query($sqlAltProduto);

		require_once("bibliotecas/classes/VEN002.php");
		$VEN002 = new VEN002();
		$VEN002->atualizarControleProduto($T006_Id);
		//Alteração necessária para não afetar o sistema de separação do produto
		mysql_query("UPDATE T185 SET T185_Qtd_Para_Separar = '{$quantidade}' WHERE T185_T006_Id = '{$T006_Id}'");
	    //echo $sqlAltProduto;
	    //die();
	    
	    $retorno = mysql_error();
		//$T006_Id
		if (!empty($retorno)) {
			$temErro = '<h3 style="text-align: center;">'.$retorno.'</h3>';
		} else if(isset($_REQUEST['comprar'])) {
			$script = <<<EOT
				\$.mobile.changePage( "./pedidoProdutoComprar.php?T005_Id={$T005_Id}&T006_Id={$T006_Id}&D009_Id={$D009_Id}&refresh={$uniqId}{$acao}", { transition: 'slide'} );
EOT;
		} else if(isset($_REQUEST['cotar'])) {
			$script = <<<EOT
				\$.mobile.changePage( "./pedidoProdutoCotar.php?T005_Id={$T005_Id}&T006_Id={$T006_Id}&D009_Id={$D009_Id}{$acao}", { transition: 'slide'} );
EOT;
		} else {			
			$script = <<<EOT
	  			$.mobile.changePage( "./pedido.php?T005_Id={$T005_Id}&refresh={$uniqId}{$acao}", { transition: 'slide'} );
EOT;
		}
	}else{
		$script = <<<EOT
	  		alert('O Preço do Produto não pode ser menor que o Preço Original');
EOT;
	}
}

include('mobile/tablet/inc.header.php');
echo <<<EOT
	<div data-role="page" id="pagepedidoDelProduto">
EOT;
include('mobile/tablet/pedido/inc.header.bar.php');

$editarPreco = ($g['C031']['permiteAlterarPrecoVenda'] == 'S' || $g['nivelAlcada'] == '4') ? '' : 'readonly';
echo <<<EOT
        <div data-role="content">
        	{$temErro}
                <form name="pedido" action="./pedidoDelProduto.php?T005_Id={$T005_Id}&T006_Id={$T006_Id}&autorizarProduto=true{$acao}" method="POST" data-ajax="false">
                <div class="ui-grid-a" style="width:100%; height:10%;">
        			<ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 5px; clear:both;">
        				<li data-role="list-divider" role="heading" class="divider">
		    				<table style="width: 100%";>
		    					<tr>
		    						<td style="width: 5%; font-size: 16px;" align="center">Alterar produto pedido</td>
		    					</tr>
		    				</table>
	                    </li>
                    </ul>
        		</div>
                <div class="ui-block-a" style="width:100%;">
                	<div data-role="fieldcontain">
                        <fieldset data-role="controlgroup">
                            <div class="ui-block-a" style="width:70%">
	                            <label for="textinput3" style="width:22.5%;">
	                                Produto
	                            </label>
	                            <input id="textinput3" placeholder="" value="{$rowT006['T006_Codigo_Produto']}" type="text" style="opacity: 0.5;width:42.5%;" disabled="disabled" />
                            </div>
                            <!--<div class="ui-block-b" style="width:30%;">
								<a data-role="button" data-transition="slide" style="width:93.5%;" onclick="\$.mobile.changePage( './pedidoAgregados.php?T006_Id={$T006_Id}&T005_Id={$T005_Id}{$acao}', {transition: 'slide'} );">
									Agregados
								</a>
							</div>-->
	                    </fieldset>
                    </div>
                    <div data-role="fieldcontain">
                        <fieldset data-role="controlgroup">
                            <label for="textinput4" style="width:15%;">
                                Marca
                            </label>
                            <input id="textinput4" placeholder="" value="{$rowT006['D082_Marca']}" type="text" style="opacity: 0.5;width:30%;" disabled="disabled" />
                        </fieldset>
                    </div>
                    <div data-role="fieldcontain">
                        <fieldset data-role="controlgroup">
                            <label for="descricao" style="width:15%;">
                                Descrição
                            </label>
                            <input name="descricao" id="descricao" placeholder="" value="{$rowT006['T006_Descricao_Produto']}" type="text" style="width:80%;"/>
                        </fieldset>
                    </div>
                    <div data-role="fieldcontain">
                        <fieldset data-role="controlgroup">
                            <label for="quantidade" style="width:15%;">
                                Quantidade
                            </label>
                            <input name="quantidade" id="quantidade" placeholder="" value="{$rowT006['T006_Quantidade']}" type="text" onkeypress="return(Verifica_Caracter(event, this));" style="width:30%;"/>
							<!-- <label for="separado" style="width:8%; margin-left: 5%;">
                                Separado
                            </label>
                            <input name="separado" id="separado" placeholder="" value="{$rowT006['T006_Quantidade_Separacao']}" type="text" style="opacity: 0.5; width:30%;"  disabled="disabled"/>-->
						</fieldset>
                    </div>
                    <div data-role="fieldcontain">
                        <fieldset data-role="controlgroup">
                            <label for="precoOriginal" style="width:15%;">
                                Preço Original
                            </label>
                            <input name="precoOriginal" id="precoOriginal" placeholder="" value="{$rowT006['T006_Valor_Preco_Original']}" type="text" style="opacity: 0.5;width:30%;"></input>
EOT;
							$T207 = mysql_query("SELECT * FROM T207 WHERE T207_T006_Id = '{$T006_Id}' AND T207_Flag_Status = '3'");
							if(mysql_num_rows($T207) > 0) {
								echo <<<EOT
								<label for="autorizarProduto" style="width:15%; margin-left: 5%; font-weight: bold; color:red;">
                                	Altere o preço para confirmar o produto
                            	</label>
                            </fieldset>
                    </div>
                    <div data-role="fieldcontain">
                        <fieldset data-role="controlgroup">
                            <label for="preco" style="width:15%;">
                                Preço
                            </label>
                            <input name="preco" id="preco" placeholder="" value="" type="text" style="width:30%;" {$editarPreco}/>
                        </fieldset>
                    </div>
EOT;
							} else {
echo <<<EOT
					    </fieldset>
                    </div>
                    <div data-role="fieldcontain">
                        <fieldset data-role="controlgroup">
                            <label for="preco" style="width:15%;">
                                Preço
                            </label>
                            <input name="preco" id="preco" placeholder="" value="{$rowT006['T006_Valor_Preco_Unitario']}" type="text" style="width:30%;" {$editarPreco}/>
                        </fieldset>
                    </div>
EOT;
							}
//<a data-role="button" data-transition="slide" onclick="\$.mobile.changePage( './pedido.php?T005_Id={$T005_Id}', { transition: 'slide'} );">
echo <<<EOT
				</div>
              
                <!--
                <div class="ui-block-b" style="width: 100%; text-align: center;">
                	{$image}
                </div>
                -->
                <div class="ui-block-b" style="width:100%;">
                    <div class="ui-block-a" style="width:16%">
                        <a data-role="button" data-transition="slide" onclick="\$.mobile.changePage( './pedido.php?T005_Id={$T005_Id}{$acao}', {transition: 'slide'} );">
                            Voltar
                        </a>
                    </div>
                    <div class="ui-block-b" style="width:16%">
                        <a rel="external" data-ajax="false" data-role="button" data-transition="slide"{$link}>
                            Link
                        </a>
                    </div>
                    <div class="ui-block-c" style="width:17%">
                        <input type="submit" value="Alterar"
                        	onclick="if ($('.ui-listview .ui-body-e').size() == 0 && {$rowsT174} > 0) {
                        				alert('Selecione uma cotação para continuar');
                        				return false;
                        			} else if($('#quantidade').val() <= '0,00') {
                        				alert('A quantidade deve ser maior que zero');
                        				return false;
                        			} else if('{$rowT006['T006_Quantidade_Separacao']}' > 0 && $('#quantidade').val() < '{$separado}'){
										alert('A quantidade informada deve ser maior que a quantidade separada');
                        				return false;
                        			}" />
                    </div>
                    <div class="ui-block-d" style="width:17%">
                    	<input type="button" data-transition="slide" value="Remover"
                    		onclick="
                    			if('{$rowT006['T006_Quantidade_Separacao']}' == 0 || '{$C007['C007_Flag_Vendedor']}' == 'D' || '{$C007['C007_Flag_Vendedor']}' == 'S') {
                					\$.mobile.changePage( './pedidoProdutoRemover.php?T005_Id={$T005_Id}&T006_Id={$T006_Id}{$acao}', {transition: 'slide'} ); 
								} else {
									alert('O produto não pode ser cancelado porque ja esta separado');
								}
							">
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
