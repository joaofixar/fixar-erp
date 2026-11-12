<?php
namespace hardness;
$script = '';
$uniqId=uniqid();
include('conf.php');
include('inc.conf.php');

// Vars
$T189_Id = (isset($_REQUEST['T189_Id'])) ? $_REQUEST['T189_Id'] : false;
	
// Adicionar Requisição de Compra
$temErro = '';
if (isset($_REQUEST['T189_Descricao_Produto'])) {
	echo $_REQUEST['T189_Descricao_Produto'];
	die();
	$T189_Descricao_Produto = strtoupper(mysql_real_escape_string($_REQUEST['T189_Descricao_Produto']));
	$T189_Quantidade = strtoupper(mysql_real_escape_string($_REQUEST['T189_Quantidade']));
	$T189_Observacao = strtoupper(mysql_real_escape_string($_REQUEST['T189_Observacao']));
	$T189_Flag_Tipo = strtoupper(mysql_real_escape_string($_REQUEST['T189_Flag_Tipo']));
	$C007_Id = $C007_Id = $g['usuarioAtual'];
	if (!empty($_REQUEST['T189_Id'])) {
		$sql = <<<EOT
			UPDATE T189 SET
				T189_Descricao_Produto = '{$T189_Descricao_Produto}',
				T189_Quantidade = '{$T189_Quantidade}',
				T189_Observacao = '{$T189_Observacao}',
				T189_Flag_Tipo = '{$T189_Flag_Tipo}'
			WHERE
				T189_Id = '{$T189_Id}';
EOT;
		mysql_query($sql);
		$script .="alert('Requisição alterada com sucesso!');";
		$script .= "\$.mobile.changePage( './requisicoes.php?refresh={$uniqId}', { transition: 'fade'} );";
	} else {
		$sql = <<<EOT
			INSERT INTO T189 (T189_C007_Id,
								T189_Data_Criado,
								T189_Descricao_Produto, 
								T189_Quantidade, 
								T189_Observacao,
								T189_Flag_Tipo 
								) 
								VALUES 
								('{$C007_Id}', 
								CURRENT_TIMESTAMP(), 
								'{$T189_Descricao_Produto}', 
								'{$T189_Quantidade}', 
								'{$T189_Observacao}',
								'{$T189_Flag_Tipo}'
								);
EOT;
		mysql_query($sql);
		$script .="alert('Requisição criada com sucesso!');";
		$script .= "\$.mobile.changePage( './requisicoes.php?refresh={$uniqId}', { transition: 'fade'} );";
	}
}

// Incluir ou Alterar?
$btns = <<<EOT
	<div class="ui-grid-a">
        <div class="ui-block-a">
            <a data-role="button" data-transition="fade" href="./requisicoes.php" data-ajax="false">
                Voltar
            </a>
        </div>
        <div class="ui-block-b">
            <input value="Adicionar" type="submit" />
        </div>
    </div>
EOT;
if ($T189_Id) {
	$sql = "SELECT * FROM T189 WHERE T189_Id = '{$T189_Id}';";
	$rowUpdate = mysql_fetch_assoc(mysql_query($sql));
	$rowUpdate['T189_Quantidade'] = number_format($rowUpdate['T189_Quantidade'], 0);
	$btns = <<<EOT
		<div class="ui-grid-b">
		    <div class="ui-block-a">
		        <a data-role="button" data-transition="fade" href="./requisicoes.php" data-ajax="false">
		            Voltar
		        </a>
		    </div>
		    <div class="ui-block-b">
		        <input value="Alterar" type="submit" />
		    </div>
		    <!--<div class="ui-block-c">
		        <a data-role="button" data-transition="slide" onclick="\$.mobile.changePage( './requisicoesRemover.php?T189_Id={$T189_Id}', { transition: 'slide'} );">
                    Remover
                </a>
		    </div>-->
		</div>
EOT;
}


include('inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageOrcamentoPreCadProd">
EOT;
include('inc.header.bar.php');

$T189_Flag_Tipo = '';
if($rowUpdate['T189_Flag_Tipo'] == 'P'){
	$T189_Flag_Tipo .= '<option value="P"'.(($rowUpdate['T189_Flag_Tipo'] == 'P') ? ' selected="selected"' : '').'> PEDIDO VENDA </option>';
}
if($rowUpdate['T189_Flag_Tipo'] == 'O'){
	$T189_Flag_Tipo .= '<option value="O"'.(($rowUpdate['T189_Flag_Tipo'] == 'O') ? ' selected="selected"' : '').'> ORÇAMENTO VENDA </option>';
}
$T189_Flag_Tipo .= '<option value="C"'.(($rowUpdate['T189_Flag_Tipo'] == 'C') ? ' selected="selected"' : '').'> APENAS COTAR </option>';
$T189_Flag_Tipo .= '<option value="E"'.(($rowUpdate['T189_Flag_Tipo'] == 'E') ? ' selected="selected"' : '').'> REPOSIÇÃO ESTOQUE </option>';
$T189_Flag_Tipo .= '<option value="R"'.(($rowUpdate['T189_Flag_Tipo'] == 'R') ? ' selected="selected"' : '').'> CANCELADO </option>';
echo <<<EOT
        	<div data-role="content">
        		{$temErro}
                 <form action="./requisicoesCompraProduto.php" method="post" data-ajax="false" name="requisitarCompraProduto">
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T189_Descricao_Produto" style="width:9%;">
	                                Descrição
	                            </label>
	                            <input name="T189_Descricao_Produto" id="T189_Descricao_Produto" value="{$rowUpdate['T189_Descricao_Produto']}" placeholder="" type="text" style="width:60%;" />
	                        </fieldset>
	                    </div>
	             <div class="ui-grid-a">
	             	<div data-role="fieldcontain">
		                        <label for="T189_Flag_Tipo" style="width:9%;">
		                            Tipo 
		                        </label>
		                        <select data-native-menu="true" name="T189_Flag_Tipo" id="T189_Flag_Tipo" value="{$rowUpdate['T189_Flag_Tipo']}">
			                        {$T189_Flag_Tipo}
		         				</select>
	               	</div>
                 	<div class="ui-block-a">
                 		<div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T189_Quantidade">
	                                Quantidade
	                            </label>
	                            <input name="T189_Quantidade" id="T189_Quantidade" value="{$rowUpdate['T189_Quantidade']}" placeholder="" type="number" onkeypress="return(Verifica_Caracter(event, this));" />
	                        </fieldset>
	                    </div>
EOT;
				if (!empty($_REQUEST['T189_Id'])) {
	                  echo <<<EOT
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T189_Detalhes">
	                                Detalhe
	                            </label>
	                            <input name="T189_Detalhes" id="T189_Detalhes" value="{$rowUpdate['T189_Detalhes']}" placeholder="" type="text" disabled="true" />
	                        </fieldset>
	                    </div>
EOT;
	                    
	 			 }
	 			echo <<<EOT
	 			 
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T189_Observacao">
	                                Observação
	                            </label>
	                            <textarea placeholder="" name="T189_Observacao" id="T189_Observacao" class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset">{$rowUpdate['T189_Observacao']}</textarea>
	                        </fieldset>
	                    </div>
	                </div>
                </div>
                {$btns}
                <input type="hidden" name="T189_Id" value="{$T189_Id}" />
                </form>
            </div>
EOT;
include('inc.footer.php');
echo <<<EOT
    </div>
    <script>
    	$( document ).undelegate("#pageOrcamentoPreCadProd", "pageinit");
    	$( document ).delegate("#pageOrcamentoPreCadProd", "pageinit", function() {
			  	$( document ).ready(function(){
				    $(':input:visible:enabled:first').select(); 
			    });
EOT;
include('inc.swipe.events.php');
echo <<<EOT
			{$script}
    	});
    </script>
EOT;
