<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

global $g;

echo <<<EOT
	<div data-role="dialog"  id="pagecontroleCaixa">
EOT;

if(@$_REQUEST['alertaCaixa'] == true) {
		echo <<<EOT
				<div data-role="header" data-theme="d">
					<h1>Controle de Caixa</h1>
				</div>
				<div data-role="content" data-theme="c" style="font-weight: bold;">
EOT;
$sql = mysql_query("SELECT * FROM T119 ORDER BY T119_Id DESC LIMIT 1");
if(mysql_num_rows($sql) > 0){
	$res = mysql_fetch_array($sql);
	if($res['T119_Data_Abertura'] != DATE("Y-m-d")){
		echo '<p>ABRA O CAIXA DE HOJE PARA REALIZAR ALTERAÇÕES NO PEDIDO</p>';
	} else {
		echo '<p>ABRA O CAIXA PARA REALIZAR ALTERAÇÕES NO PEDIDO</p>';	
	}
} else {
	echo '<p>ABRA O CAIXA PARA REALIZAR ALTERAÇÕES NO PEDIDO</p>';
}
echo <<<EOT
					<a data-role="button" data-rel="back" data-theme="c">Voltar</a>    
				</div>
			</div>
EOT;
}

if(@$_REQUEST['abrirCaixa'] == true) {
		echo <<<EOT
				<div data-role="header" data-theme="d">
					<h1>Controle de Caixa</h1>
				</div>
				<div data-role="content" data-theme="c">
					<p>Deseja abrir o caixa ?</p>
					<a data-role="button" data-theme="b" onclick="\$.mobile.changePage( './caixaLista.php?abrirCaixa=true', { transition: 'slide'} );">Sim</a>
					<a data-role="button" data-rel="back" data-theme="c">Não</a>    
				</div>
			</div>
EOT;
}

if(@$_REQUEST['fecharCaixa'] == true){
	$T005 = mysql_query("SELECT * FROM T005 WHERE T005_Flag_Status_Servicos = '0'");
	//$T207 = mysql_query("SELECT * FROM T207 WHERE T207_Flag_Status = '3' OR T207_Flag_Status = '5' GROUP BY T207_T005_Id");
	echo <<<EOT
		<div data-role="header" data-theme="d">
			<h1>Controle de Caixa</h1>
		</div>
EOT;
	$rows = mysql_num_rows($T005);
	if($rows > 0){		
			echo <<<EOT
				<div data-role="content" data-theme="c">
					<p>O caixa não pode ser fechado</p>
EOT;
		if($rows > 1){
			$pedidos = '';
			while($row = mysql_fetch_array($T005)){
				$pedidos .= $row['T005_Id'] . '<br />';
			}	
			echo <<<EOT
				Verificar os pedidos:<br /> {$pedidos}
EOT;
		} else {
			$T005 = mysql_fetch_array($T005);
			echo <<<EOT
				Verificar o pedido {$T005['T005_Id']}
EOT;
		}
		echo <<<EOT
				<a data-role="button" data-rel="back" data-theme="c">Voltar</a>    
			</div>
		</div>
EOT;
	} else {
		echo <<<EOT
			<div data-role="content" data-theme="c">
				<p>Deseja fechar o caixa ?</p>
				<a data-role="button" data-theme="b" onclick="\$.mobile.changePage( './caixaLista.php?fecharCaixa=true', { transition: 'slide'} );">Sim</a>
				<a data-role="button" data-rel="back" data-theme="c">Não</a>    
			</div>
		</div>
EOT;
	}	
}
