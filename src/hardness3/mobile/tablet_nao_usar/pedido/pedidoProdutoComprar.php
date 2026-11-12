<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');
include('mobile/tablet/inc.header.php');
require_once('bibliotecas/classes/VEN005.php');
$VEN005 = new VEN005();
$script = "";

$T005_Id = $_REQUEST['T005_Id'];
$T006_Id = $_REQUEST['T006_Id'];
$D009_Id = $_REQUEST['D009_Id'];
$D001_Id = $_REQUEST['D001_Id'];
$C007_Id = $g['usuarioAtual'];
$T189_Observacao = $_REQUEST['T189_Observacao'];
$T189_Flag_Tipo = $_REQUEST['T189_Flag_Tipo'];
$uniqId = uniqid();


if($_REQUEST['btn'] == "Comprar"){
	$login = $_REQUEST['login'];
	$senha = $_REQUEST['senha'];
	$sql = mysql_query("SELECT * FROM C007 WHERE C007_Id = '{$C007_Id}'");
	$res = mysql_fetch_array($sql);
	//if($res['C007_Login'] == $login && base64_decode($res['C007_Senha']) == $senha){
		//Busca dados da T006 para inserir na Tabela de Requisição
		$sql = <<<EOT
			SELECT T006_Descricao_Produto, T006_Quantidade 
			FROM T006 			
			WHERE 
				T006_Id = {$T006_Id};
EOT;
		$row = mysql_fetch_assoc(mysql_query($sql));
		//Verifica se já existe uma Requisição de cotação
		$sql = "SELECT * FROM T189 WHERE T189_T006_Id = '{$T006_Id}' AND T189_Flag_Tipo = 'C'";
		$T189 = mysql_query($sql);
		if(mysql_num_rows($T189) > 0){
			$T189 = mysql_fetch_array($T189);
			$T189_Id = $T189[0];
			mysql_query("UPDATE T189 SET T189_Flag_Tipo = '{$T189_Flag_Tipo}', T189_Detalhes = 'Pedido Nº: {$T005_Id} - {$T005['D024_Nome_Empresa']}', T189_Flag_Status = 'D' WHERE T189_T006_Id = '{$T006_Id}'");
		} else {		
			//Insere dados na Tabela de Requisição 
			$sql = <<<EOT
				INSERT INTO	T189 (
						T189_T006_Id, 
						T189_D009_Id, 
						T189_C007_Id, 
						T189_Data_Criado, 
						T189_Descricao_Produto,
						T189_Quantidade, 
						T189_Observacao, 
						T189_Detalhes, 
						T189_Flag_Tipo
						) VALUES (
						'{$T006_Id}',
						'{$D009_Id}', 
						'$C007_Id', 
						CURRENT_TIMESTAMP(),
						'{$row['T006_Descricao_Produto']}', 
						'{$row['T006_Quantidade']}', 
						'{$T189_Observacao}', 
						'Pedido Nº: {$T005_Id} - {$T005['D024_Nome_Empresa']}', 
						'$T189_Flag_Tipo' 
				);
EOT;
			mysql_query($sql);
		}
			$script .="alert('Requisição realizada com sucesso!');";
			$script .= "\$.mobile.changePage( './pedido.php?T005_Id={$T005_Id}&refresh={$uniqId}{$acao}', { transition: 'fade'} );";
	} else {
		echo <<<EOT
		<script>
			alert('Erro! Login ou Senha inválida.');
		</script>	
EOT;
	$script.="\$.mobile.changePage( \"./pedidoProdutoComprar.php?T005_Id={$T005_Id}&T006_Id={$T006_Id}&D009_Id={$D009_Id}&refresh={$uniqId}{$acao}\", { transition: 'slide'} );";
	//}
}

echo <<<EOT
	<div data-role="dialog" id="pedidoProdutoComprar" data-ajax="false">
		<style>
			input{
				text-transform:lowercase;
			}
		</style>
		<div data-role="header" data-theme="d">
			<h1>Enviar solicitação de compra</h1>
		</div>
		<div data-role="content" data-theme="c">
			{$temErro}
			<form method="POST" action="./pedidoProdutoComprar.php?T005_Id={$T005_Id}&T006_Id={$T006_Id}&D009_Id={$D009_Id}&T189_Flag_Tipo=P{$acao}" data-ajax="false">
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
	                    <!--<div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="login" style="width:15%;">
	                                Login
	                            </label>
	                            <input id="login" name="login" placeholder="" type="text" style="width:30%;" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="senha" style="width:15%;">
	                                Senha
	                            </label>
	                            <input id="senha" name="senha" placeholder="" type="password" style="width:30%;" />
	                        </fieldset>
	                    </div>-->
	                </div>
                </div>
                <div class="ui-grid-b">
                	<div class="ui-block-a">
						<a data-role="button" data-transition="slide" href="pedido.php?T005_Id={$T005_Id}{$acao}">
            				Voltar
        				</a>
        			</div>
                	<div class="ui-block-b">
	                    <input type="submit" name="btn" id="btn" value="Comprar">
                	</div>
                	
                </div>
            </form>    
		</div>
	</div>
	<script>
    	$( document ).undelegate("#pedidoProdutoComprar", "pageinit");
    	$( document ).delegate("#pedidoProdutoComprar", "pageinit", function() {
			{$script}
    	});
    </script>
EOT;
