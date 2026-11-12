<?php
namespace hardness;
function estoqueMP($array){
	global $g;
	$diasMP=$g['C031']['diasMedioPrazo'];
	$data = strtotime("+$diasMP day", strtotime("now"));
	$dataMP = date("Y-m-d", $data);	
	
	$retorno=mysql_query("select sum(T055_Quantidade) as QteOC from T055 left join T075 on T075_Id=T055_T075_Id where T055_D009_Id={$array['todosCampos']['D009_Id']} AND ifnull(T075_Data_Estoque,'0000-00-00')='0000-00-00' and T075_Data_Previsao<='{$dataMP}' and T075_Data_Previsao>0");        
	$retorno=mysql_fetch_array($retorno);

    $qteOC=$retorno['QteOC'];
	
	$retorno=mysql_query("SELECT T075_Id FROM T075 LEFT JOIN T055 ON T075_Id=T055_T075_Id LEFT JOIN D009 ON T055_D009_Id=D009_Id WHERE T055_D009_Id='{$array['todosCampos']['D009_Id']}' and T075_Data_Previsao<='{$dataMP}' group by T075_Id");
	$qtePed=0;
	while ($ret=mysql_fetch_array($retorno)){
		$retorno1=mysql_query("select sum(T006_Quantidade) 
				from T006 
				left join T005 on T005_Id=T006_T005_Id 
				where T005_Flag_Status!=5
			    and T005_Flag_Status!='7'
			    and T005_Flag_Status!='8'
			    and T005_Flag_Status!='11'
				and T006_T075_Id='{$ret['T075_Id']}'
				and T006_D009_Id='{$array['todosCampos']['D009_Id']}'");
		$ret1=mysql_fetch_array($retorno1);
		$qtePed+=$ret1[0];
	}
	$liquido=number_format($qteOC-$qtePed,2);    
	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Ordens de Compra', '/crm/crm001/content/crm001ContentDiMP/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['D049_Id']}'), [700,400]); return false;\">{$liquido}</a>";    
}
