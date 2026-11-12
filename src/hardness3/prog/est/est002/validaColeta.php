<?php
namespace hardness;

	if ($row['T007_Flag_Cancelada'] == 'S' && $row['T007_Flag_ACP'] == 4) {
		$verifica = "Pedido - ".$row['T005_Id'] ." - Cupom Fiscal emitido para este pedido foi cancelado.";
		
	} else if (!empty($row['T007_Id']) && empty($row['T007_Numero_Nota_Fiscal']) && $row['T007_Flag_ACP'] == 4) {
		$verifica = "Pedido - ".$row['T005_Id'] ." - Cupom Fiscal ainda não emitido! Não é possível efetuar a entrega.";
		
	} else if (empty($row['T007_Numero_Protocolo_Nfe']) && ($row['T007_Flag_ACP'] == 6 || $row['T007_Flag_ACP'] == 8) && $row['T007_Protocolo_Inutilizacao'] == '') {
		$nf = ($row['T007_Flag_ACP'] == 6 ) ? "NF-e" : "NFC-e";
		$verifica = "Pedido - ".$row['T005_Id'] ." - Nota Fiscal ainda não foi transmitida! Confirmar a entrega apenas será possível depois da transmissão da {$nf} para a Sefaz.";
		
	} else if (empty($row['T007_Numero_Nota_Fiscal']) && ($row['T005_Flag_Expedicao'] == 2 && $g['C031']['modeloExpedicao'] != 2)) {
		
		//mysql_query("UPDATE T005 SET T005_Flag_Status = 27, T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1) WHERE T005_Id = '{$T005_Id}'");
		//$verifica = "Nota fiscal deste pedido foi removida! O pedido voltou para o status 'Emitir Cupom'.";
		$verifica = "Pedido - ".$row['T005_Id'] ." - Cupom Fiscal ainda não emitido! Não é possível efetuar a entrega.";
	}else if (empty($row['T007_Numero_Nota_Fiscal']) && $row['T007_Flag_ACP'] == 8 && $row['T005_Flag_Pdv_Importado'] == 'S'){

		$verifica = "Pedido - ".$row['T005_Id'] ." - Está vinculado a NFCe Id ".$row['T007_Id'].", para retornar o pedido é necessário excluir a NFCe";
	} else if (empty($row['T007_Numero_Nota_Fiscal']) || $row['T007_Flag_Cancelada'] == 'S') {
		
		mysql_query("UPDATE T005 SET T005_Flag_Status = 3, T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1) WHERE T005_Id = '{$T005_Id}'");
		$verifica = "Pedido - ".$row['T005_Id'] ." - Excluiu nota fiscal. O pedido voltou para 'Emitir Nota Fiscal'.";
		$verificaInsert = mysql_real_escape_string($verifica);
		// Inserindo histórico do pedido
		$T178  = "insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'{$verificaInsert}','')";
		log($T178);
		mysql_query($T178);
	} else if(!empty($row['T007_Protocolo_Inutilizacao'])){
		
		mysql_query("UPDATE T005 SET T005_Flag_Status = 3, T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1) WHERE T005_Id = '{$T005_Id}'");
		mysql_query("UPDATE T007 SET T007_T005_Id='' WHERE T007_Id='{$row['T007_Id']}'");
		mysql_query("UPDATE T008 SET T008_T006_Id='' WHERE T008_T007_Id='{$row['T007_Id']}'");
		$verifica = "Pedido - ".$row['T005_Id'] ." - Nota fiscal inutilizada. O pedido voltou para 'Emitir Nota Fiscal'.";
		$verificaInsert = mysql_real_escape_string($verifica);
		// Inserindo histórico do pedido
		$T178  = "insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'{$verificaInsert}','')";
		log($T178);
		mysql_query($T178);
	}
	else if(($g['C031']['plataformaECommerce'] == "magento2" || $g['C031']['plataformaECommerce2'] == "magento2")  && $row['T005_Pedido_Ecommerce'] > 0 && $row['T005_Data_Hora_Envio_Ecommerce'] == '0000-00-00 00:00:00')
	{
		$verifica = 'Faturamento do Magento 2 não enviado.';
	}
	else
	{
		$verifica='';	
	}

?>