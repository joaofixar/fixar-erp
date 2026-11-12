<?php
namespace hardness;

	$assunto = $row['entidade_assunto'];
	$tipo = "";

	if($assunto == 'RETORNO'){
		$tipo = "RETORNO";
	} else if($assunto == 'EST_PEDIDO'){
		$tipo = "ENTRADA_ESTOQUE_PEDIDO";
	} else if($assunto == 'T003'){
		$tipo = "ORCAMENTO"; 
	} else if($assunto == 'T005'){
		$tipo = "PEDIDO"; 
	} else if($assunto == 'T007'){
		$tipo = "NF";
	} else if($assunto == 'T255'){
		$tipo = "OP";
	} else if($assunto == 'T224'){
		$tipo = "OC";
	} else if($assunto == 'T218'){
		$tipo = "COTACAO";
	}else if($assunto == 'T182'){
		$tipo = "RMA";
	}else{
		$tipo = "INTERACAO";
	}
	

	$row['tipo_assunto'] =  mysql_real_escape_string($tipo);
