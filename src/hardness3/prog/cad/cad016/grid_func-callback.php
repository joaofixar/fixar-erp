<?php
namespace hardness;
  /**
 * RubiaYwata
 * 16/11/2011
 */

function trataFlagSupervisorVendedor($array){
	
	if($array['campoAtual'] == 'V') return 'Vendedor';
	else if($array['campoAtual'] == 'S') return 'Supervisor';
	else if($array['campoAtual'] == 'D') return 'Vendedor';
	
}

function selecionaSupervisor($array){

	$cClausula  = " select ";
	$cClausula .= " C007_Id, ";
	$cClausula .= " C007_Nome ";
	$cClausula .= " from C007 ";
	$cClausula .= " where (C007_Flag_Vendedor='S' or C007_Flag_Vendedor='D') and C007_Ativo='S' ";
	$cClausula .= " order by C007_Nome ";
	$cSql_C007=mysql_query($cClausula);
	echo mysql_error();
	while ($mC007=mysql_fetch_array($cSql_C007))
	{

		if ($mC007['C007_Id']==$array['todosCampos']['C007_C007_Id'])
		{
			     return $mC007[1];
		}	
	}
}

function trataFlagExpedicao($array){
	
  	if($array['campoAtual'] == '1') return 'Comercial';
	else if($array['campoAtual'] == '2') return 'Balcão';
//	else return '-';
	
}

function trataAtivo($array){

 	if($array['campoAtual'] == 'S') return 'Sim';
	else if($array['campoAtual'] == 'N') return 'Não';
//	else return '-';
}

function trataEspecial($array){
    	
   	if($array['campoAtual'] == 'S') return 'Sim';
	else if($array['campoAtual'] == 'N') return 'Não';
//	else return '-';
}

function trataUsuarioAncora($array){

	if($array['campoAtual'] == 'S') return 'Sim';
	else if($array['campoAtual'] == 'N') return 'Não';
//	else return '-';

}

?>
