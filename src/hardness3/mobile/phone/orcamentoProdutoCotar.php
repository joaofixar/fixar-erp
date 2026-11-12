<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');
include('inc.header.php');
require_once('bibliotecas/classes/VEN005.php');
$VEN005 = new VEN005();
$script = "";

if($_REQUEST['btn'] == "Cotar"){
	$T004_Id = $_REQUEST['T004_Id'];
	$D009_Id = $_REQUEST['D009_Id'];
	$C007_Id = $g['usuarioAtual'];
	$T189_Observacao = $_REQUEST['T189_Observacao'];
	$T189_Flag_Tipo = $_REQUEST['T189_Flag_Tipo'];
	
	
	//Busca dados da T004 para inserir na Tabela de Requisição
	$sql = <<<EOT
		SELECT T004_Descricao_Produto, T004_Quantidade 
		FROM T004 			
		WHERE 
			T004_Id = {$T004_Id};
EOT;
		$row = mysql_fetch_assoc(mysql_query($sql));
	
	//Insere dados na Tabela de Requisição 
	$sql = <<<EOT
		INSERT INTO 
		T189 
			(T189_T004_Id, T189_T006_Id, T189_D009_Id, T189_C007_Id, T189_Data_Criado, T189_Descricao_Produto, T189_Quantidade, T189_Observacao, T189_Detalhes, T189_Flag_Tipo) 
		VALUES 
			('{$T004_Id}', '', '{$D009_Id}', '$C007_Id', CURRENT_TIMESTAMP(), '{$row['T004_Descricao_Produto']}', '{$row['T004_Quantidade']}', '{$T189_Observacao}', 'Orcamento Nº: {$T003_Id} - {$T003['D024_Nome_Empresa']}', '$T189_Flag_Tipo' );
EOT;
		mysql_query($sql);
		
		//SQL para Zerar o Preço do item do orçamento, quando é requisitada a cotação do item
		mysqli_query("UPDATE T004 SET T004_Valor_Preco_Unitario = 0 WHERE T004_Id = '{$T004_Id}';");
		
			$script .="alert('Requisição realizada com sucesso!');";
			$script .= "\$.mobile.changePage( './orcamento.php?T003_Id={$T003_Id}&refresh={$uniqId}', { transition: 'fade'} );";
}

echo <<<EOT
	<div data-role="dialog" id="orcamentoAprovacao" data-ajax="false">
		<div data-role="header" data-theme="d">
			<h1>Deseja Realizar a Cotação deste Produto?</h1>
		</div>
		<div data-role="content" data-theme="c">
			{$temErro}
			<form method="POST" action="./orcamentoProdutoCotar.php?acao=cotar&T003_Id={$T003_Id}&T004_Id={$_REQUEST['T004_Id']}&D009_Id={$_REQUEST['D009_Id']}&T189_Flag_Tipo=O" data-ajax="false">
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
include('inc.swipe.events.php');
echo <<<EOT
		{$script}
    	});
    </script>
EOT;
