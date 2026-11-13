<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');
include('mobile/tablet/inc.header.php');
require_once('bibliotecas/classes/VEN005.php');
$VEN005 = new VEN005();
$script = "";

if($_REQUEST['btn'] == "Cotar"){
	$T006_Id = $_REQUEST['T006_Id'];
	$D009_Id = $_REQUEST['D009_Id'];
	$C007_Id = $g['usuarioAtual'];
	$T189_Observacao = $_REQUEST['T189_Observacao'];
	$T189_Flag_Tipo = $_REQUEST['T189_Flag_Tipo'];
	
	if ($T006_Id<=0)
	{
		echo "Nao recebi T006_Id";
		exit;
	}
	
    	
	//Busca dados da T006 para inserir na Tabela de Requisição
	$sql = <<<EOT
		SELECT T006_Descricao_Produto, T006_Quantidade 
		FROM T006 			
		WHERE 
			T006_Id = {$T006_Id};
EOT;
		$row = mysql_fetch_assoc(mysql_query($sql));
	
	//Insere dados na Tabela de Requisição 
	$sql = <<<EOT
		INSERT INTO 
		T189 
			(T189_T004_Id, T189_T006_Id, T189_D009_Id, T189_C007_Id, T189_Data_Criado, T189_Descricao_Produto, T189_Quantidade, T189_Observacao, T189_Detalhes, T189_Flag_Tipo) 
		VALUES 
			('', '{$T006_Id}', '{$D009_Id}', '$C007_Id', CURRENT_TIMESTAMP(), '{$row['T006_Descricao_Produto']}', '{$row['T006_Quantidade']}', '{$T189_Observacao}', 'Pedido Nº: {$T005_Id} - {$T005['D024_Nome_Empresa']}', '$T189_Flag_Tipo' );
EOT;
		mysql_query($sql);
		
		//SQL para Zerar o Preço do item do orçamento, quando é requisitada a cotação do item
		mysqli_query("UPDATE T006 SET T006_Valor_Preco_Unitario = 0 WHERE T006_Id = '{$T006_Id}';");
		
			$script .="alert('Requisição realizada com sucesso!');";                                                                 
			$script .= "\$.mobile.changePage( './pedido.php?T005_Id={$T005_Id}&refresh={$uniqId}{$acao}', { transition: 'fade'} );";     
}

echo <<<EOT
	<div data-role="dialog" id="orcamentoAprovacao" data-ajax="false">
		<div data-role="header" data-theme="d">
			<h1>Enviar requisição de cotação de compra</h1>
		</div>
		<div data-role="content" data-theme="c">
			{$temErro}
			<form method="POST" action="./pedidoProdutoCotar.php?acao=cotar&T005_Id={$T005_Id}&T006_Id={$_REQUEST['T006_Id']}&D009_Id={$_REQUEST['D009_Id']}&T189_Flag_Tipo=C{$acao}" data-ajax="false">
                 <div class="ui-grid-a">
                 	<div class="ui-block-a" style="width: 60%;">
	                     <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T189_Observacao">
	                                Observação: 
	                            </label>
	                            	<textarea name="T189_Observacao" id="T189_Observacao" value="" style="width:150%;height:100px;"></textarea>
	                        </fieldset>
	                    </div>
	                </div>
                </div>
                <div class="ui-grid-b">
                	<div class="ui-block-a">
						<a data-role="button" data-transition="slide" data-rel="back">
            				Voltar
        				</a>
        			</div>
                	<div class="ui-block-b">
	                    <input type="submit" name="btn" value="Cotar">
                	</div>
                	
                </div>
            </form>    
		</div>
	</div>
	 <script>
    	$( document ).undelegate("#orcamentoAprovacao", "pageinit");
    	$( document ).delegate("#orcamentoAprovacao", "pageinit", function() {
EOT;

include('./mobile/tablet/inc.swipe.events.php');
echo <<<EOT
		{$script}
    	});
    </script>
EOT;
