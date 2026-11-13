<?php
namespace hardness;
	/**
	* FIN - FIN005 - Boletos Bancários
	* Weiberlan
	* 17/11/2011
	*/
	function campoCancelar($array){
		global $g;
		$T117_T002_Id = $array['todosCampos']['T117_T002_Id'];
		$sqlT002 = mysql_query("SELECT T002_T117_Id FROM T002 WHERE T002_Id = '{$T117_T002_Id}'");
		$T002 = mysql_fetch_array($sqlT002);
		if ($array['todosCampos']['T117_Flag_Cancelado']=='N')
		{
			//No antigo sistema: <a href=\"javascript:cancelar_boleto('$mT117[T117_Id]');\" style='color:blue;text-decoration:none;'>";
			return "<a href=\"#\" onclick=\"fin005CampoCancelar('{$array['todosCampos']['T117_Id']}', '{$g['divId']}')\">Cancelar</a>";
		} else {
			if($T117_T002_Id > 0 && $T002['T002_T117_Id'] == 0){
				return "<a href=\"#\" onclick=\"fin005CampoDesfazerCancelamento('{$array['todosCampos']['T117_Id']}', '{$T117_T002_Id}','{$g['divId']}')\">Restaurar</a>";
			} else {
				return "Cancelado";
			}
		}
	}

	function campoeMail($array){
		global $g;
		if ($array['todosCampos']['T117_Flag_Cancelado']=='S')
		{
			return "&nbsp;";
		}
		else
		{
			// Verificando se o T007 vinculado ao T002 existe
			$T002_T007=mysql_query("SELECT T002_T007_Id 
								      FROM T002 
								 LEFT JOIN T117 ON T117_Id=T002_T117_Id 
								     WHERE T117_Id='{$array['todosCampos']['T117_Id']}'");

			while($mT002_T007=mysql_fetch_array($T002_T007)){

				if($mT002_T007['T002_T007_Id'] > 0){
					$T007=mysql_query("SELECT T007_Id 
										 FROM T007 
										WHERE T007_Id='{$mT002_T007['T002_T007_Id']}'");

					if(mysql_num_rows($T007)<=0){
						return "<font color='red'>NF EXCLUÍDA</font>";
					}
				}
			}

			// Verificando se o T007 vinculado ao T002 existe
			$T002_T007=mysql_query("SELECT T002_T007_Id,
										   IFNULL(D007_T002.D007_Flag_Ativo, D007_T117.D007_Flag_Ativo) AS D007_Flag_Ativo 
									  FROM T002 
								 LEFT JOIN T117 ON T117_Id=T002_T117_Id 
								 LEFT JOIN D027 ON D027_Id=T002_D027_Id 
								 LEFT JOIN D007 AS D007_T002 ON D007_T002.D007_Id=T002_D007_Id
								 LEFT JOIN D007 AS D007_T117 ON D007_T117.D007_Id=T117_D007_Id
								     WHERE T117_Id='{$array['todosCampos']['T117_Id']}'");
			while($mT002_T007=mysql_fetch_array($T002_T007)){
				if($mT002_T007['D007_Flag_Ativo'] != 'S'){
					return "<font color='red'>CONTA INATIVA</font>";
				}
			}	

			if ($array['todosCampos']['T117_Data_Envio_Email']!='0000-00-00 00:00:00' and $array['todosCampos']['T117_Data_Envio_Email']!='')
			{
				//Valida se o valor do boleto excedo o valor máximo dos boletos sem registros PARA EMAILS JÁ ENVIADOS
				$T117_Id = $array['todosCampos']['T117_Id'];
				$T117 = mysql_query("SELECT T117_Valor_Total, 
											D007_Valor_Maximo_Boleto_Sem_Registro, 
											T002_T035_Id 
									   FROM T117
							      LEFT JOIN D007 ON D007_Id = T117_D007_Id
							      LEFT JOIN T002 ON T002_Id = T117_T002_Id
							      	  WHERE T117_Id = '{$T117_Id}'");

				$mT117 = mysql_fetch_array($T117);
				if ($mT117['D007_Valor_Maximo_Boleto_Sem_Registro'] > 0 && ($mT117['T117_Valor_Total'] > $mT117['D007_Valor_Maximo_Boleto_Sem_Registro'] || $mT117['T117_Valor_Total'] == 0) && $mT117['T002_T035_Id']<=0) {
					// Mensagem
					$alert = gGeraAlertMsg('Operação Cancelada', 'O valor do boleto excede o valor limite para boletos sem registro. Registre-o para continuar a operação', false);
					return  "<a href='#' onclick=\"dialogConfirm('Operação Cancelada', 'O valor do boleto excede o valor limite para boletos sem registro. Registre-o para continuar a operação')\"><span class='ui-icon ui-icon-check' title='E-mail enviado em: ".gCorrigeData($array['todosCampos']['T117_Data_Envio_Email'])."''> </span></a>";
				}else{
					return  "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T117_Id=' + encodeURIComponent('{$array['todosCampos']['T117_Id']}') + '&D024_Id=' + encodeURIComponent('{$array['todosCampos']['T117_D024_Id']}'), [900,550]);\"><span class='ui-icon ui-icon-check' title='E-mail enviado em: ".gCorrigeData($array['todosCampos']['T117_Data_Envio_Email'])."''> </span></a>";
				}
			}else{
				//Valida se o valor do boleto excedo o valor máximo dos boletos sem registros PARA EMAILS QUE AINDA NÃO FORAM ENVIADOS
				$T117_Id = $array['todosCampos']['T117_Id'];
				$T117 = mysql_query("SELECT T117_Valor_Total, D007_Valor_Maximo_Boleto_Sem_Registro, T002_T035_Id 
									   FROM T117
							      LEFT JOIN D007 ON D007_Id = T117_D007_Id
							      LEFT JOIN T002 ON T002_Id = T117_T002_Id
							      	  WHERE T117_Id = '{$T117_Id}'");

				$mT117 = mysql_fetch_array($T117);
				if ($mT117['D007_Valor_Maximo_Boleto_Sem_Registro'] > 0 && ($mT117['T117_Valor_Total'] > $mT117['D007_Valor_Maximo_Boleto_Sem_Registro'] || $mT117['T117_Valor_Total'] == 0) && $mT117['T117_T035_Id']<=0) {
					// Mensagem
					$alert = gGeraAlertMsg('Operação Cancelada', 'O valor do boleto excede o valor limite para boletos sem registro. Registre-o para continuar a operação', false);
					return  "<a href='#' onclick=\"dialogConfirm('Operação Cancelada', 'O valor do boleto excede o valor limite para boletos sem registro. Registre-o para continuar a operação')\"><img src='/hardness3/static/img/tmp/icone_enviar.gif' /></a>";
				}else{
					return  "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T117_Id=' + encodeURIComponent('{$array['todosCampos']['T117_Id']}') + '&D024_Id=' + encodeURIComponent('{$array['todosCampos']['T117_D024_Id']}'), [900,550]);\"><img src='/hardness3/static/img/tmp/icone_enviar.gif' /></a>";
				}
			}
		}
	}
	
	function campoGerar($array){
		global $g;
		$checked = '';
		if ($array['todosCampos']['T002_Flag_Gerar_Boleto']){
			$checked = " checked = 'checked'";
		}
		return "<input type='checkbox' title='Selecionar Título' onChange=\"fin005FlagGerarBoleto(this, '{$array['todosCampos']['T002_Id']}', '{$g['divId']}');\" {$checked}>";
	}

	function campoAtualizarVencimento($array){
		global $g;
		if ($array['todosCampos']['T117_Flag_Cancelado']=='S') {
			return "Cancelado";
		} else if($array['todosCampos']['T002_Data_Recebimento'] != '0000-00-00' && !empty($array['todosCampos']['T002_Data_Recebimento'])){
			return 'Pago';
		} else {
			return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Atualizar Vencimento Boleto', '/fin/fin005/content/fin005ContentAtualizarVencimento/', '&T117_Id=' + encodeURIComponent('{$array['todosCampos']['T117_Id']}'), [450,330]);\">Atualizar Vencimento</a>";
		}
	}
?>
