<?php
namespace hardness;
$script = '';

include('conf.php');
include('inc.conf.php');

// Vars
$T004_Id = isset($_REQUEST['T004_Id']) ? $_REQUEST['T004_Id'] : false;
	
// Adicionar Requisição de Compra
$temErro = '';
if (isset($_REQUEST['T189_Descricao_Produto'])) {
	$T189_Descricao_Produto = strtoupper(mysql_real_escape_string($_REQUEST['T189_Descricao_Produto']));
	$T189_Quantidade = strtoupper(mysql_real_escape_string($_REQUEST['T189_Quantidade']));
	$T189_Observacao = strtoupper(mysql_real_escape_string($_REQUEST['T189_Observacao']));
	$T189_Flag_Tipo = strtoupper(mysql_real_escape_string($_REQUEST['T189_Flag_Tipo']));
	$C007_Id = $C007_Id = $g['usuarioAtual'];
	
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
		
		//SQL para Zerar o Preço do item do orçamento, quando é requisitada a cotação do item
		$sqlUpdate = <<<EOT
			UPDATE T004 SET T004_Valor_Preco_Unitario = 0 WHERE T004_Id = {$T004_Id};
EOT;
		
		mysql_query($sqlUpdate);
		
		$script .="alert('Cotação realizada com sucesso!');";
			$script .= "\$.mobile.changePage( './orcamento.php?T003_Id={$T003_Id}&refresh={$uniqId}', { transition: 'fade'} );";
	}


include('inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageOrcamentoPreCadProd">
EOT;
include('inc.header.bar.php');
echo <<<EOT
        	<div data-role="content">
        		{$temErro}
                 <form action="./orcamentoRequisitarProd.php?T003_Id={$T003_Id}&T004_Id={$T004_Id}&T189_Flag_Tipo=O" method="post" data-ajax="false">
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T189_Descricao_Produto" style="width:9%;">
	                                Descrição
	                            </label>
	                            <input name="T189_Descricao_Produto" id="T189_Descricao_Produto" value="" placeholder="" type="text" style="width:60%;" />
	                        </fieldset>
	                    </div>
	             <div class="ui-grid-a">
                 	<div class="ui-block-a">
                 		<div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T189_Quantidade">
	                                Quantidade
	                            </label>
	                            <input name="T189_Quantidade" id="T189_Quantidade" value="" placeholder="" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T189_Observacao">
	                                Observação
	                            </label>
	                            <textarea placeholder="" name="T189_Observacao" id="T189_Observacao" value="" class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset"></textarea>
	                        </fieldset>
	                    </div>
	                </div>
                </div>
                <div class="ui-grid-a">
                    <div class="ui-block-a">
                        <a data-role="button" data-transition="fade" href="./orcamento.php?T003_Id={$T003_Id}" data-ajax="false">
                            Voltar
                        </a>
                    </div>
                    <div class="ui-block-b">
                    	<input value="Adicionar" type="submit" />
                    </div>
                </div>
                </form>
            </div>
EOT;
include('inc.footer.php');
echo <<<EOT
    </div>
    <script>
    	$( document ).undelegate("#pageOrcamentoPreCadProd", "pageinit");
    	$( document ).delegate("#pageOrcamentoPreCadProd", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
			{$script}
    	});
    </script>
EOT;
