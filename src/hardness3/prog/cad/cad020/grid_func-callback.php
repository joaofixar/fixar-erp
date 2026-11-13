<?php
namespace hardness;
	function validarFlagPadrao($array){
		$retorno = "<input type='checkbox' name='D049_Flag_Padrao' id='D049_Flag_Padrao' onChange=''";
		if($array['todosCampos']['D049_Flag_Padrao'] == 'S'){
			return $retorno .="checked='checked'";
		}
		$retorno .= " />";
		return $retorno;
	}
	
	function validaCampoExcluir(){
		return "<span class=\"ui-icon ui-icon-circle-close\" title=\"Excluir\"></span>";
	}
	
	function validaCampoExcluir1($array)	{
		global $g;
    	return <<<EOT
    		<a href="#" onclick="cad002CampoExcluir1('{$array['todosCampos']['T140_Id']}', '{$g['divId']}')">
    			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
			</a>
EOT;
	}
	
 	function mostrarLocacao($array) {
		return $array['todosCampos']['D004_Local'];
	}
	
	function validaRevenda($array){
		if($array['todosCampos']['D009_Flag_Preco_Tabelado']=='S'){ // or $array['todosCampos']['C020_IPV_Revenda'] <=0
			return "<input type='text' name='D041_Valor_Preco_Revenda' id='D041_Valor_Preco_Revenda' tabindex=3 value='{$array['todosCampos']['D041_Valor_Preco_Revenda']}' />";
		}else{
			return $array['todosCampos']['D041_Valor_Preco_Revenda'];
		}
	}
	
	function validaIPV1($array){
		if($array['todosCampos']['D009_Valor_Custo_Unitario'] > 0){
			return number_format($array['todosCampos']['D041_Valor_Preco_Revenda']/$array['todosCampos']['D009_Valor_Custo_Unitario'],4,',','.');
		}
	}
	
	function validaConsumidorAtacado($array){
		if($array['todosCampos']['D009_Flag_Preco_Tabelado']=='S'){ //|| $array['todosCampos']['C020_IPV_Revenda']<=0
			return "<input type='text' name='D041_Valor_Base' id='D041_Valor_Base'value='".number_format($array['todosCampos']['D041_Valor_Base'],2,',','.')."' />" ;
		}else{
			return number_format($array['todosCampos']['D041_Valor_Base'],2,',','.');
		}
	}
	
	function validaIPV2($array){
		if($array['todosCampos']['D009_Valor_Custo_Unitario']>0){
			return number_format($array['todosCampos']['D041_Valor_Base']/$array['todosCampos']['D009_Valor_Custo_Unitario'],4,',','.');
		}
	}
	
	function validaConsumidorVarejo($array){
		
		if($array['todosCampos']['D009_Flag_Preco_Tabelado']=='S'){// || $array['todosCampos']['C020_IPV_Revenda']<=0
			return "<input type='text' name='D041_Valor_Base' id='D041_Valor_Base'value='{$array['todosCampos']['D041_Valor_Preco_Consumidor']}' />" ;
		}else{
			return $array['todosCampos']['D041_Valor_Preco_Consumidor'];
		}
	}
	
	function validaIPV3($array){
		if($array['todosCampos']['D009_Valor_Custo_Unitario'] > 0){
			return number_format($array['todosCampos']['D041_Valor_Preco_Consumidor']/$array['todosCampos']['D009_Valor_Custo_Unitario'],4,',','.');
		}
	}
	
	function validaPrecoTabelado($array){
		$retorno = "<input type='checkbox' name='D009_Flag_Preco_Tabelado' id='D009_Flag_Preco_Tabelado' ";
		if($array['todosCampos']['D009_Flag_Preco_Tabelado']=='S'){
			return $retorno.= "checked='checked'";
		}
		$retorno .=" />";
		return $retorno;
	}
	
	function validaICF($array){
          if ($array['todosCampos']['D009_Valor_Custo_Compra']>0)
          {
              $D009_Valor_Custo_Compra=$array['todosCampos']['D009_Valor_Custo_Compra'];
          }
          else
          {
              $D009_Valor_Custo_Compra=1;
          }
          
		  return number_format($array['todosCampos']['D009_Valor_Custo_Unitario']/$D009_Valor_Custo_Compra,4,',','.');
	}

	function validaIpvHistorico($array){
		if($array['todosCampos']['T001_Valor_Custo_Unitario']>0){
			return number_format($array['todosCampos']['T001_Valor_Preco_Unitario']/$array['todosCampos']['T001_Valor_Custo_Unitario'],4,',','.');
		}
	}
 	function validaCustoTotalMontagem($array){
 		return number_format($array['todosCampos']['D009_Valor_Custo_Compra']*$array['todosCampos']['D042_Quantidade'],2,',','.');
 	}

	function validaCustoTotal2Montagem($array){
		return number_format($array['todosCampos']['D049_Valor_Preco_Tabela']*$array['todosCampos']['D042_Quantidade'],2,',','.');
	}
	function validaObervacaoMontagem($array){
		return str_replace("\n","<br />",$array['todosCampos']['D042_Observacao']);
	}
	function formatarNumeroCustoCompra($array){
		return number_format($array['todosCampos']['D009_Valor_Custo_Compra'],2,',','.');
	}
	function formatarNumeroCustoTabela($array){
		return number_format($array['todosCampos']['D049_Valor_Preco_Tabela'],2,',','.');
	}
	function validaIpvPedidos($array){
		if($array['todosCampos']['T006_Valor_Custo_Unitario']>0){
			if ($array['todosCampos']['T006_Valor_Custo_Unitario']>0)
			{
				return number_format($array['todosCampos']['T006_Valor_Preco_Unitario']/$array['todosCampos']['T006_Valor_Custo_Unitario'],4,',','.');
			}
		}
	}
	function validaMarca($array){
		if (empty($array['todosCampos']['D082_Marca'])){
	    	return @$array['todosCampos']['D024_Nome_Fantasia'];
        } else {
        	return $array['todosCampos']['D082_Marca'];
        }
	}
	
	function validaFornecedorCertificados($array){
		$sql = "SELECT D024_Nome_Empresa FROM D024 WHERE D024_Id=" . $array['todosCampos']['T140_D024_Id'];
		$query = mysql_query($sql);
		$retorno =  mysql_fetch_assoc($query);
		return $retorno['D024_Nome_Empresa'];
	}
	
	function abrirCertificado($array){
			$id = $array['todosCampos']['T140_Id'];
			$retorno = "<a href=\"/cad/cad002/grid_func-ajax/abreCertificado/?ajax=true&acaoId={$id}\">Abrir</a>";
			return $retorno;
	}
	function campoLocacao($array){
		global $g;
		return "<span class=\"ui-state-default ui-corner-all icone\" onclick=\"abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Locação', '/cad/cad002/content/cad002ContentCadastroLocacao/','&D049_Id=' + encodeURIComponent({$array['todosCampos']['D049_Id']}), [400,200]);\" title=\"Adicionar Locação\">
					<span class=\"ui-icon ui-icon-circle-plus\">
					</span>
				</span>" . $array['campoAtual'];
			
	}
	
	/**
	* Esta função é antiga, utilizada somente antes do REFACTORY do sistema de ETIQUETAS
	* A partir de agora, todas as etiquetas são gerencidas/criadas pelo editor de ediquetas
	* Somente foi mantido este código, porque a CRV utiliza
	* Na CRV as etiquetas possuem várias particularidades, que com o tempo devem ser passadas para o editor de etiquetas
	*/
	function campoImpTag($array){
		global $g;
		
		$id = $array['todosCampos']['D049_Id'];
		$etiquetasProdutoPedirQtd = true;
		
		if (!$etiquetasProdutoPedirQtd) {
			return "<li class=\"ui-state-default ui-corner-all icone\" style=\"list-style: none;\" onclick=\"cad020ImpTag('{$id}', '{$g['divId']}'); return false;\" title=\"Imprimir Etiqueta\">
						<span class=\"ui-icon ui-icon-tag\">
						</span>
					</li>";
		} else {
			return "<li class=\"ui-state-default ui-corner-all icone\" style=\"list-style: none;\" title=\"Imprimir Etiqueta\">
						<span class=\"ui-icon ui-icon-tag\" onclick=\"abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Informe a Quantidade', '/sistema/etiquetas/content/sistemaEtiquetasQtdProduto/','&D00X_Id={$id}', [300,200]);\">
						</span>
					</li>";
		}
		
		/*$etiquetasProdutoModelos = $g['conf']->ini('etiquetasProdutoModelos');
		if (count($etiquetasProdutoModelos) < 2) {
			return "<li class=\"ui-state-default ui-corner-all icone\" style=\"list-style: none;\" onclick=\"cad020ImpTag('{$id}', '{$g['divId']}'); return false;\" title=\"Imprimir Etiqueta\">
						<span class=\"ui-icon ui-icon-tag\">
						</span>
					</li>";
		} else {
			return "<li class=\"ui-state-default ui-corner-all icone\" style=\"list-style: none;\" onclick=\"cad020ImpTag('{$id}', '{$g['divId']}'); return false;\" title=\"Imprimir Etiqueta\">
						<span class=\"ui-icon ui-icon-tag\" onclick=\"abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Seleção de Modelos de Etiqueta', '/sistema/etiquetas/content/sistemaEtiquetasSelecao/','&D00X_Id={$id}', [500,300]);\">
						</span>
					</li>";
		}*/
	}
    
    function validaCampoImprimir($array){
        global $g,$r_acaoId;
        $retorno = "<input onclick=\"cad020CheckboxImprimir(this, '{$array['todosCampos']['D009_Id']}', '{$g['divId']}')\" type='checkbox' id='D009_Flag_Imprimir_Etiqueta' title='Imprimir'";
        if ($array['todosCampos']['D009_Flag_Imprimir_Etiqueta']=='S'){
            return $retorno .="checked='checked'";
        }
        return $retorno .= " />";
    }

	function percentualEstoqueFisico($array){
		global $g;

		return (($array['todosCampos']['D009_Quantidade_Estoque']/$array['totais']['Estoque_Fisico'])*100).'%';

	}

	function percentualEstoqueLiquido($array){
		global $g;

		return (($array['todosCampos']['D009_Quantidade_Estoque_Liquido']/$array['totais']['Estoque_Liquido'])*100).'%';

	}

?>
