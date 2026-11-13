<?php
namespace hardness;
function verificaStatus($array){
	if ($array['todosCampos']['T029_Flag_Ok']=='S')
		return 'Concluído';
	else
		return '';
}
function verificaExclusao($array){
	global $g;
	if ($array['todosCampos']['T029_Flag_Ok']=='S'){
    	return "";	
 	}
 	else{
 		return gExcluirLinha($array, array('T029_Id', $g['divId']));
 	}
}
function verificaExclusaoProduto($array){
	global $g;
	if ($array['todosCampos']['T029_Flag_Ok']=='S'){
    	return "";	
 	}
 	else{
 		return gExcluirLinha($array, array('T030_Id', $g['divId']));
 	}
}

function validaCampoNovoCusto($array){
	if ($array['todosCampos']['T029_Flag_Ok']=='N' and $array['todosCampos']['T030_Flag_Alterar_Custo']=='S'){
		return $array['todosCampos']['T030_Valor_Custo_Unitario'];
	}elseif($array['todosCampos']['T030_Flag_Alterar_Custo']=='N'){
 		return "- - -";
	}else{
		return number_format($array['todosCampos']['T030_Valor_Custo_Unitario'],4,',','.');
	}
}

function validaCampoOperacao($array){
		if ($array['todosCampos']['T030_Flag_Entrada_Saida']=='S')
		{
			return "DEBITA";
		}
		if ($array['todosCampos']['T030_Flag_Entrada_Saida']=='E')
		{
			return "CREDITA";
		}
		if ($array['todosCampos']['T030_Flag_Entrada_Saida']=='N')
		{
			return "NÃO ALTERA";
		}
		if ($array['todosCampos']['T030_Flag_Entrada_Saida']=='B')
		{
			return "BALANÇO";
		}
     
}
function validaCampoQuantidade($array){
	if ($array['todosCampos']['T029_Flag_Ok']=='N' and $array['todosCampos']['T030_Flag_Entrada_Saida']!='N')
    {
    	return $array['todosCampos']['T030_Quantidade'];
  	}
  	elseif ($array['todosCampos']['T030_Flag_Entrada_Saida']=='N')
  	{
    	return "- - -";
  	}
  	else
  	{
    	return gCorrigeNumero($array['todosCampos']['T030_Quantidade']);
  	}
}
function validaCampoAlteraCusto($array){
	if ($array['todosCampos']['T029_Flag_Ok']=='N')
     {
         if ($array['todosCampos']['T030_Flag_Alterar_Custo']=='S')
         {
            return "Sim";
         }
       
         if ($array['todosCampos']['T030_Flag_Alterar_Custo']=='N')
         {
            return "Não";
         }
     }
     else
     {
         if ($array['todosCampos']['T030_Flag_Alterar_Custo']=='S')
         {
            return "Sim";
         }
         elseif ($array['todosCampos']['T030_Flag_Alterar_Custo']=='N')
         {
            return "Não";
         }
     }
}

function insereRegistroT001($array){
	global $g;
	
	if($g['usuarioAtual'] == 63 AND $array['todosCampos']['T001_Id'] <= 0){
		return <<<EOT
		<a href="#" onclick="insereRegistroT001('{$array['todosCampos']['T030_Id']}','{$g['divId']}')">
			<span title="Insere T001">Insere T001</span>
		</a>
EOT;
	}else{
		return $array['todosCampos']['T001_Id'];
	}
}
