<?php
namespace hardness;
	
	function validaCampoCancelar($array){
		global $g;
		return <<<EOT
	    	<a href="#" onclick="est005CampoCancelar('{$array['todosCampos']['T084_Id']}', '{$g['divId']}')">
	    		Cancelar
			</a>
EOT;
	}
	
	function validaCampoExcluir($array){
		global $g;
		return <<<EOT
	    	<a href="#" onclick="est005CampoExcluir('{$array['todosCampos']['T084_Id']}', '{$g['divId']}')">
	    		Excluir
			</a>
EOT;
	}
	
	function validaNotasLancadas($array){
		if(empty($array['todosCampos']['T085_Id']))
		{
			  return "<font style=\"color: var(--BGVermelho)\">FALTA</font>";
		}
		else if ($array['todosCampos']['T084_Flag_Fechado']=='S')		
		{
	         return "<span class=\"ui-icon ui-icon-circle-check\"></span>";
		}
		else
		{
	     	return "&nbsp";
		}
	}

	function validaCampoXMLConhecimento($array){
		global $g;
		$Chave_Acesso = $array['todosCampos']['T084_Chave_Acesso_Cte'];
		$Protocolo = $array['todosCampos']['T084_Numero_Protocolo_Cte'];
		$Diretorio_CTe = $g['pathDadosAntigo'] . "cte/xml/recebido/" . $Chave_Acesso . "-" . $Protocolo . ".xml";
		
		 if (!empty($array['todosCampos']['T084_Chave_Acesso_Cte']))
		 {
		 	$retorno='';
            if (!empty($array['todosCampos']['T084_Numero_Protocolo_Cte']))
             {
     			$retorno = "<a title='Download do arquivo XML' href=\"/est/est005/grid_func-ajax/abreXML/?ajax=true&acaoId={$array['todosCampos']['T084_Id']}\">";
                if (file_exists("$Diretorio_CTe"))
                 {
                     $retorno .= "<font class=fonte><u>XML {$array['todosCampos']['T084_Numero_Conhecimento']}</u></font></a>";
                 }
             }
         }
		return $retorno;
	}

	function verificaTomadora($array){
		global $g;
			
		if($array['todosCampos']['T084A_Flag_Empresa_Tomadora'] == 'S'){
			return 'SIM';
		} elseif($array['todosCampos']['T084A_Flag_Empresa_Tomadora'] == 'N'){
			return 'NÃO';
		} else {
			return '-';
		}
	}
		
?>
