<?php
    namespace hardness;

	function T292_D027_Id_Portador($array) {
		return $array['todosCampos']['D027_Portador'];
	}

	function excluirProdutoPDV($array){
		global $g;
		$restricao = $g['conf']->restricaoTravar('excluir', 'T008_Id', $array['todosCampos']['T008_Id'], 'T008_T007_Id', $array['todosCampos']['T008_T007_Id']);
		if ($restricao === true) {
			return <<<EOT
				<span onclick="ven003ExcluirProduto('{$array['todosCampos']['T008_Id']}', '{$array['todosCampos']['T008_T007_Id']}', '{$g['divId']}');"> 
					<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
				</span>
EOT;
		}
		return '';
		
	}


?>