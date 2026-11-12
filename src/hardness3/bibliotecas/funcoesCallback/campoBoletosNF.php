<?php
namespace hardness;	
	function campoBoletosNF($array){
		global $g;
		$T007_Id = $array['todosCampos']['T007_Id'];
		$D006 = mysql_query("SELECT D006_Flag_Gera_Contas FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id WHERE T007_Id = '{$T007_Id}'") ;
		$resD006 = mysql_fetch_array($D006);
		if($resD006['D006_Flag_Gera_Contas'] == 'R'){		
			if ($array['todosCampos']['T007_Data_Envio_Email']!='0000-00-00 00:00:00' and $array['todosCampos']['T007_Data_Envio_Email']!='') {
				return  "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&idNF=' + encodeURIComponent('{$array['todosCampos']['T007_Id']}'), [900,550]);\"><span class='ui-icon ui-icon-check' style=\"margin-left: 9px;\" title='E-mail enviado em: ".gCorrigeData($array['todosCampos']['T007_Data_Envio_Email'])."''> </span></a>";
			} else {
				return  "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&idNF=' + encodeURIComponent('{$array['todosCampos']['T007_Id']}'), [900,550]);\"><spam class=\"ui-icon ui-icon-mail-closed\" style=\"margin-left: 9px;\" /></a>";
			}
		} else {
			return null;
		}
	}
?>