<?php
namespace hardness;

// Retira notas fiscais antigas já transmitidas da fila de envio
mysqli_query("UPDATE T007 SET T007_TX_Flag_Finalizada = 1 WHERE DATE_ADD(T007_Data_Hora_Protocolo_Nfe, INTERVAL 2 DAY) < NOW();");
// Retira notas fiscais emitidas pelo sistema antigo (acontecia na alumifix)
mysqli_query("UPDATE T007 SET T007_TX_Flag_Finalizada = 2, T007_TX_Resposta_Codigo = 9 WHERE T007_TX_Flag_Finalizada = 0 AND (T007_Numero_Protocolo_Nfe != '' OR T007_Numero_Protocolo_Nfe IS NOT NULL)");


//Remover fornecedores nao homologados
if(isset($g['C031']['prazoFornecedorHomologacao']) && $g['C031']['prazoFornecedorHomologacao'] > 0){
	$sqlT241 = mysqli_query("DELETE FROM T241 WHERE DATEDIFF(CURDATE(), T241_Data) > '{$g['C031']['prazoFornecedorHomologacao']}' AND T241_Flag_Homologado != 'S'");
}

//Atualizar saldo de créditos dos clientes
$sqlD024 = mysql_query("SELECT D024_Id FROM D024");
while($rowD024 = mysql_fetch_array($sqlD024)){
	mysqli_query("CALL D024_Gravar_Totalizacao('{$rowD024['D024_Id']}')");
}
