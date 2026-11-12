<?php
namespace hardness;
/**
* Atualização campo T003_Flag_Status_Orcamento
* 
* Este script atualiza o status de todos os orçamentos vencidos (V) - orçamento aberto por mais dias que 'diasMaxOrcamentoPendente'
*/

// Vars
$dias = (isset($g['C031']['diasMaxOrcamentoPendente'])) ? $g['C031']['diasMaxOrcamentoPendente'] : $g['conf']->ini('diasMaxOrcamentoPendente');
// Clear
$sql = "UPDATE T003 SET T003_Flag_Status_Orcamento = 'P' WHERE T003_Flag_Status_Orcamento = 'V';";
mysqli_query($sql);

// While
$sql = "SELECT T003_Id, T003_Data_Emissao, T005_Flag_Status, T003_Flag_Perdido FROM T003 left join T005 on T005_T003_Id=T003_Id WHERE T003_Flag_Status_Orcamento = 'P';";
$resultado = mysqli_query($sql);
while ($row = mysql_fetch_assoc($resultado)) {
	//echo date("d/m/Y", strtotime("+{$dias} days", strtotime($row['T003_Data_Emissao']))) . "\n";
	if($row['T005_Flag_Status']=='5'){
		continue;
	} else if ($row['T003_Flag_Perdido']=='S'){
		continue;
	} else if ($row['T005_Flag_Status']<=0) {
		if (strtotime("+{$dias} days", strtotime($row['T003_Data_Emissao'])) <= time()) {
			$sql = "UPDATE T003 SET T003_Flag_Status_Orcamento = 'V' WHERE T003_Id = '{$row['T003_Id']}'";
			echo $row['T003_Id'] . "\n";
			mysqli_query($sql);
		}
	}
}