<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

echo <<<EOT
	<div data-role="dialog">
EOT;

// Vars
$T006_Id = isset($_REQUEST['T006_Id']) ? $_REQUEST['T006_Id'] : false;
$uniqId=uniqid();

$sql = mysql_query("SELECT *
					 FROM T189
					 LEFT JOIN T059 ON T059_T189_Id=T189_Id
					 LEFT JOIN T185 ON T185_T006_Id=T189_T006_Id
					 LEFT JOIN T174 ON T174_T059_Id=T059_Id
					 LEFT JOIN T173 ON T173_Id=T174_T173_Id
					 LEFT JOIN T012 ON T012_T174_Id=T174_Id OR T012_T189_Id=T189_Id 
					 LEFT JOIN T011 ON T011_Id=T012_T011_Id
					 WHERE T189_T006_Id = '{$T006_Id}'");
$res = mysql_fetch_array($sql);
if($res['T189_Flag_Tipo'] == 'C' || mysql_num_rows($sql) == 0) {
		echo <<<EOT
				<div data-role="header" data-theme="d">
					<h1>Remover Produto?</h1>
				</div>
				<div data-role="content" data-theme="c">
					<p>Deseja cancelar este produto do Pedido?</p>
EOT;
					if($res['T185_Qtd_Separado'] > 0){ echo 'ATENÇÃO O PRODUTO JA FOI SEPARADO!'; }
echo <<<EOT
					<a data-role="button" data-theme="b" onclick="\$.mobile.changePage( './pedido.php?T005_Id={$T005_Id}&T006_Id={$T006_Id}&refresh={$uniqId}{$acao}', { transition: 'slide'} );">Sim</a>
					<a data-role="button" data-rel="back" data-theme="c">Não</a>    
				</div>
			</div>
EOT;
} else if($res['T189_Flag_Tipo'] == 'P' && $res['T011_Id'] == '' && ($res['T189_C007_Id_Comprar'] == 0 || $res['T189_C007_Id_Comprar'] == '')){
	echo <<<EOT
				<div data-role="header" data-theme="d">
					<h1>Remover Produto?</h1>
				</div>
				<div data-role="content" data-theme="c">
					<p>Deseja cancelar este produto do Pedido?</p>	
EOT;
					if($res['T185_Qtd_Separado'] > 0){ echo 'ATENÇÃO O PRODUTO JA FOI SEPARADO!'; }
echo <<<EOT
					<a data-role="button" data-theme="b" onclick="\$.mobile.changePage( './pedido.php?T005_Id={$T005_Id}&T006_Id={$T006_Id}&refresh={$uniqId}{$acao}', { transition: 'slide'} );">Sim</a>
					<a data-role="button" data-rel="back" data-theme="c">Não</a>    
				</div>
			</div>
EOT;
} else {
	echo <<<EOT
		<div data-role="header" data-theme="d">
			<h1>Não foi possível cancelar o Produto</h1>
		</div>
		<div data-role="content" data-theme="c">
				<p>Não foi possivel cancelar, este item possui uma requisição de compra!</p>
				<a data-role="button" data-rel="back" data-theme="c">Ok</a>    
			</div>
		</div>
	</div>
EOT;

}
