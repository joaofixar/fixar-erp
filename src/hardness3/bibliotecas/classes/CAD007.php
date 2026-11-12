<?php
namespace hardness;
definirClasse('CAD007');
class CAD007_
 {

    /**
     * excluir
     *
     * @param String $T140_Id
     * @return true
     */ 
 	public function excluir($T140_Id) {
		global $g;
		$T140_Id = mysql_real_escape_string($T140_Id);

		$sql = "DELETE FROM T303 WHERE T303_T140_Id='$T140_Id'";
		mysql_query($sql);
		$sql = "DELETE FROM T140 WHERE T140_Id='$T140_Id'";
		mysql_query($sql);
		return true;
	}	

	/**
	 * incluirItem
	 *
	 * @param Form $form
	 * @return String/bool
	 */
	public function incluirItem($form)
	{
		global $g;
		
		$T140_Id = $form->campoValorEnviado('T303_T140_Id');
		$D001_Id = $form->campoValorEnviado('T303_D001_Id');
		
		if($D001_Id <= 0 || empty($D001_Id)){
			return 'Selecione o produto';
		}

		$cClausula = <<<EOT
			INSERT INTO T303 (
				T303_T140_Id, 
				T303_D001_Id 
			) VALUES ( 
				'{$T140_Id}', 
				'{$D001_Id}'
			) 
	EOT;			
		mysql_query($cClausula);
		$cErro = mysql_error();
		if (!empty($cErro)) {
			return "Erro: " . $cErro . "<br />" . $cClausula;
		} else {
			return true;
		}
	}
}

?>