<?php
namespace hardness;
function estoqueLP($array){
	global $g;
	$diasMP=$g['C031']['diasMedioPrazo'];
	$data = strtotime("+$diasMP day", strtotime(now));
	$dataMP = date("Y-m-d", $data);	
	
	$retorno=mysql_query("select sum(T055_Quantidade) as QteOC from T055 left join T075 on T075_Id=T055_T075_Id where T055_D009_Id={$array['todosCampos']['D009_Id']} and T075_Data_Previsao>'{$dataMP}'");
	$retorno=mysql_fetch_array($retorno);
    $qteOC=$retorno['QteOC'];
	$retorno=mysql_query("SELECT T075_Id FROM T075 LEFT JOIN T055 ON T075_Id=T055_T075_Id LEFT JOIN D009 ON T055_D009_Id=D009_Id WHERE T055_D009_Id='{$array['todosCampos']['D009_Id']}' and T075_Data_Previsao>'{$dataMP}' and T075_Data_Previsao>0");
	$qtePed=0;
	while ($ret=mysql_fetch_array($retorno)){
		$retorno1=mysql_query("select sum(T006_Quantidade) 
				from T006 
				left join T005 on T005_Id=T006_T005_Id 
				where T005_Flag_Status!=5
			    and T006_Flag_Status!='3'
			    and T005_Flag_Status!='7'
			    and T005_Flag_Status!='8'
			    and T005_Flag_Status!='11'
			    and T006_T075_Id='{$ret['T075_Id']}'");
		$ret1=mysql_fetch_array($retorno1);
		$qtePed+=$ret1[0];
	}        
	$liquido=number_format($qteOC-$qtePed,2);
	
	/*
	// Tentativa de fazer tudo em uma única SQL (para ficar mais rápido)
	// Mais na verdade ficou bem mais lento... tem que pensar em pré calcular estes valores e colocar em um campo na tabela
	$sql = <<<EOT
		SELECT 
		(
			SELECT 
				sum(T055_Quantidade)
			FROM 
				T055 
			LEFT JOIN T075 on T075_Id=T055_T075_Id 
			WHERE 
				T055_D009_Id={$array['todosCampos']['D009_Id']}
				AND T075_Data_Previsao > '{$dataMP}'
		) as QteOC, 
		(	
			SELECT 
				SUM(T006_Quantidade)
			FROM 
				T006 
			LEFT JOIN T005 on T005_Id=T006_T005_Id 
			WHERE 
				T005_Flag_Status!=5
				AND T006_Flag_Status!='3'
				AND T005_Flag_Status!='7'
				AND T005_Flag_Status!='8'
				AND T005_Flag_Status!='11'
				AND T006_T075_Id IN
			(
				SELECT 
					T075_Id 
				FROM 
					T075 
				LEFT JOIN T055 ON T075_Id=T055_T075_Id
				LEFT JOIN D009 ON T055_D009_Id=D009_Id 
				WHERE 
					T055_D009_Id='{$array['todosCampos']['D009_Id']}' 
					AND T075_Data_Previsao>'{$dataMP}'
					AND T075_Data_Previsao > 0
			)
		) as qtdPed
EOT;
	$res = mysql_query($sql);
	$row = mysql_query($res);
	$liquido=number_format($row['qteOC']-$row['qtePed'],2);
	*/
	
	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Ordens de Compra', '/crm/crm001/content/crm001ContentDiLP/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['D049_Id']}'), [700,400]); return false;\">{$liquido}</a>";
}