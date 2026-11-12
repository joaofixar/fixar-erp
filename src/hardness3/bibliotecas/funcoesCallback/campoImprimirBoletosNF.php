<?php
namespace hardness;	
	function campoImprimirBoletosNF($array){
		global $g, $confUsuario;
		$path=base64_encode($confUsuario['urlRaiz'] . $g['pathWeb'] . $g['pathDados']);
		$T007_Id = $array['todosCampos']['T007_Id'];
		$D006 = mysql_query("SELECT D006_Flag_Gera_Contas FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id WHERE T007_Id = '{$T007_Id}'") ;
		$resD006 = mysql_fetch_array($D006);
		if($resD006['D006_Flag_Gera_Contas'] == 'R'){
			$D024_Id = $array['todosCampos']['D024_Id'];	
			$cQuery=md5($D024_Id.$T007_Id);
			return <<<EOT
				<a href="#" onclick="nova_janela('/hardness3/hardness/boleto/gerar_boleto_NF.php?cQuery={$cQuery}&path={$path}',0,0,750,500,'yes','no','no','no','yes','no','Impressao_Pedido');">
					<span class="ui-icon ui-icon-print" style="margin-left: 17px;" title="Imprimir Boletos"></span>
				</a>
EOT;
		} else {
			return null;
		}
	}
?>