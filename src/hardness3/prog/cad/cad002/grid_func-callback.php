<?php
namespace hardness;
	function validarFlagPadrao($array){
		global $g,$r_acaoId;
		$retorno = "<input type='checkbox' name='D049_Flag_Padrao' id='D049_Flag_Padrao' onChange=\"cad002CheckboxFlagPadrao(this, '{$r_acaoId}', '{$g['divId']}')\"";
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
		$exibir = false;
		$sql = mysql_query("SELECT D001_Flag_Pre_Cadastro FROM T140 LEFT JOIN D001 ON D001_Id=T140_D001_Id WHERE T140_Id = '{$array['todosCampos']['T140_Id']}'");
		$row = mysql_fetch_assoc($sql);
		
		if ($row['D001_Flag_Pre_Cadastro'] == 'S'){
			if(isset($g['c029Ids'][187])){
				$exibir = true;
			}
		} else {
			if(isset($g['c029Ids'][186])){
				$exibir = true;
			}
		}
		if($exibir){
	    	return <<<EOT
	    		<a href="#" onclick="cad002CampoExcluir1('{$array['todosCampos']['T140_Id']}', '{$array['todosCampos']['T303_D001_Id']}','{$g['divId']}')">
	    			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
				</a>
EOT;
		}
	}
	
	function validaRevenda($array){
		if($array['todosCampos']['D009_Flag_Preco_Tabelado']=='S'){ // or $array['todosCampos']['C020_IPV_Revenda'] <=0
			return "<input type='text' name='D041_Valor_Preco_Revenda' id='D041_Valor_Preco_Revenda' tabindex=3 value='{$array['todosCampos']['D041_Valor_Preco_Revenda']}' />";
		}else{
			return $array['todosCampos']['D041_Valor_Preco_Revenda'];
		}
	}
	
	function corEstoque($array){
		if($array['todosCampos']['D009_Quantidade_Estoque']<0){
			$array['todosCampos']['D009_Quantidade_Estoque'] = gCorrigeNumero($array['todosCampos']['D009_Quantidade_Estoque']);
			return "<font style='color: var(--BGVermelho)'>{$array['todosCampos']['D009_Quantidade_Estoque']}</font>";
		}else{
			return gCorrigeNumero($array['todosCampos']['D009_Quantidade_Estoque']);
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
		if(!empty($array['todosCampos']['T140_Link'])){
			return '-';
		} else {
			$id = $array['todosCampos']['T140_Id'];
			$retorno = "<a href=\"/cad/cad002/grid_func-ajax/abreCertificado/?ajax=true&acaoId={$id}\">Abrir</a>";
			return $retorno;
		}
	}

	function editarHistorico($array){
		global $g;
		$novoIdTelaForm = uniqid();
			$imagem = <<<EOT
				<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Cadastro Histórico', '/cad/cad002/content/cad002contentEditarHistorico/','&acaoId={$array['todosCampos']['T001_Id']}&tabela=T001&D009_Id={$array['todosCampos']['D009_Id']}', [700,400]);">
				   <div class="icone ui-state-default ui-corner-all">
		   				<span class="ui-icon ui-icon-pencil" title="Editar Cadastro"></span>
				   </div>
				</span>
EOT;
		return $imagem;
	}
	function mostrarMarca($array) {
		return $array['todosCampos']['D082_Marca'];
	}
	function mostrarTipo($array) {
		return $array['todosCampos']['C008_Tipo'];
	}
	function mostrarGrupo($array) {
		return $array['todosCampos']['D060_Descricao_Grupo'];
	}
	function mostrarSubGrupo($array) {
		return $array['todosCampos']['D002_Grupo'];
	}
	function mostrarLinha($array) {
		return $array['todosCampos']['D003_Nome_Linha'];
	}
	function mostrarUnidade($array) {
		return $array['todosCampos']['D037_Unidade'];
	}
	
	function abrirFotoProduto($array){
		if(!empty($array['todosCampos']['T172_Link_Video'])){
			return '-';
		}
		
		$idFoto    = $array['todosCampos']['T172_Id'];
		$idProduto = $array['todosCampos']['T172_D001_Id'];
		$retorno = "<a href=\"/cad/cad002/grid_func-ajax/abreFotoProduto/?ajax=true&idFoto={$idFoto}&idProduto={$idProduto}\">Abrir</a>";
		
		return $retorno;
	}
	
	function QteLiquido($array){
		$ret=mysql_query("SELECT D009_Quantidade_MP({$array['todosCampos']['D009_Id']},{$array['todosCampos']['T075_Id']})");
		$ret=mysql_fetch_array($ret);
		$quant=gCorrigeNumero($ret[0]);
		return $quant;
	}

function validaEditarMarca($array){
	global $g;
	return "<li title=\"Editar Marca\" class=\"ui-state-default ui-corner-all\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Marca', '/cad/cad002/content/cad002ContentEditarMarca/', '&acaoId=' + encodeURIComponent({$array['todosCampos']['D049_Id']}) + '&D001_Id=' + encodeURIComponent({$array['todosCampos']['D001_Id']}), [720,420]);\">
				<span class=\"ui-icon ui-icon-pencil\"></span>
	        </li>";
}

function validaPrecoRetorno1($array, $campoRetorno){
	global $g;
	
	$D009_Id=$array['todosCampos']['D009_Id'];
	$D009_Preco_1 = $array['todosCampos']['D009_Preco_1'];
	$sql = <<<EOT
	SELECT T098_D009_Preco_1, T098_Data from T098 WHERE T098_D009_Id='{$D009_Id}' ORDER BY T098_Id DESC;
EOT;
	$result = mysql_query($sql);
	
	$count=1;
	$ultimaAlteracao="";
	while($linha = mysql_fetch_array($result)){
		$data = explode(" ", $linha['T098_Data']);
		$data = $data[0];
		
		if($count==1){
			$ultimaAlteracao = $data;	
		}
		if(($D009_Preco_1 != $linha['T098_D009_Preco_1']) && ($linha['T098_D009_Preco_1'] != '0.00')){
			if($campoRetorno=="preco"){
				return $linha['T098_D009_Preco_1'];
			}else if($campoRetorno=="data"){
				return gCorrigeData($data);
			}
		}
		$count++;
	}
	
	if($campoRetorno=="preco"){
		return $D009_Preco_1;
	}else if($campoRetorno=="data"){
		return gCorrigeData($ultimaAlteracao);
	}
}
function validaPrecoRetorno2($array, $campoRetorno){
	global $g;
	
	$D009_Id=$array['todosCampos']['D009_Id'];
	$D009_Preco_2 = $array['todosCampos']['D009_Preco_2'];
	$sql = <<<EOT
	SELECT T098_D009_Preco_2, T098_Data from T098 WHERE T098_D009_Id='{$D009_Id}' ORDER BY T098_Id DESC;
EOT;
	$result = mysql_query($sql);
	
	$count=1;
	$ultimaAlteracao="";
	while($linha = mysql_fetch_array($result)){
		$data = explode(" ", $linha['T098_Data']);
		$data = $data[0];
		if($count==1){
			$ultimaAlteracao = $data;	
		}
		if(($D009_Preco_2 != $linha['T098_D009_Preco_2']) && ($linha['T098_D009_Preco_2'] != '0.00')){
			if($campoRetorno=="preco"){
				return $linha['T098_D009_Preco_2'];
			}else if($campoRetorno=="data"){
				return gCorrigeData($data);
			}
		}
		$count++;
	}
	if($campoRetorno=="preco"){
		return $D009_Preco_2;
	}else if($campoRetorno=="data"){
		return gCorrigeData($ultimaAlteracao);
	}
}
function validaPrecoRetorno3($array, $campoRetorno){
	global $g;
	
	$D009_Id=$array['todosCampos']['D009_Id'];
	$D009_Preco_3 = $array['todosCampos']['D009_Preco_3'];
	$sql = <<<EOT
		SELECT T098_D009_Preco_3, T098_Data from T098 WHERE T098_D009_Id='{$D009_Id}' ORDER BY T098_Id DESC;
EOT;
	$result = mysql_query($sql);
	
	$count=1;
	$ultimaAlteracao="";
	while($linha = mysql_fetch_array($result)){
		$data = explode(" ", $linha['T098_Data']);
		$data = $data[0];
		if($count==1){
			$ultimaAlteracao = $data;	
		}
		if(($D009_Preco_3 != $linha['T098_D009_Preco_3']) && ($linha['T098_D009_Preco_3'] != '0.00')){
			if($campoRetorno=="preco"){
				return $linha['T098_D009_Preco_3'];
			}else if($campoRetorno=="data"){
				return gCorrigeData($data);
			}
		}
		$count++;
	}
	if($campoRetorno=="preco"){
		return $D009_Preco_3;
	}else if($campoRetorno=="data"){
		return gCorrigeData($ultimaAlteracao);
	}
}

function statusProdutoMarca($array){
	$D049_Flag_Ativo = $array['todosCampos']['D049_Flag_Ativo'];
	if($D049_Flag_Ativo == '' || $D049_Flag_Ativo == null){
		return $array['todosCampos']['D001_Flag_Ativo'];
	} else {
		return $D049_Flag_Ativo;
	}
}

function validaPrecoRetorno1Promocao($array, $campoRetorno){
	global $g;
	
	$D009_Id=$array['todosCampos']['D009_Id'];
	$D009_Preco_1 = $array['todosCampos']['D009_Preco_1'];
	$sql = <<<EOT
	SELECT T098_D009_Preco_1, T098_Data, T098_Flag_Promocao from T098 WHERE T098_D009_Id='{$D009_Id}' ORDER BY T098_Id DESC;
EOT;
	$result = mysql_query($sql);
	
	$count=1;
	$ultimaAlteracao="";
	while($linha = mysql_fetch_array($result)){
		$data = explode(" ", $linha['T098_Data']);
		$data = $data[0];
		
		if($count==1){
			$ultimaAlteracao = $data;	
		}
		if(($D009_Preco_1 != $linha['T098_D009_Preco_1']) && ($linha['T098_D009_Preco_1'] != '0.00') && ($linha['T098_Flag_Promocao'] == "N")){
			if($campoRetorno=="preco"){
				return $linha['T098_D009_Preco_1'];
			}else if($campoRetorno=="data"){
				return gCorrigeData($data);
			}
		}
		$count++;
	}
	
	if($campoRetorno=="preco"){
		return $D009_Preco_1;
	}else if($campoRetorno=="data"){
		return gCorrigeData($ultimaAlteracao);
	}
}

function validaPrecoRetorno2Promocao($array, $campoRetorno){
	global $g;
	
	$D009_Id=$array['todosCampos']['D009_Id'];
	$D009_Preco_2 = $array['todosCampos']['D009_Preco_2'];
	$sql = <<<EOT
	SELECT T098_D009_Preco_2, T098_Data, T098_Flag_Promocao from T098 WHERE T098_D009_Id='{$D009_Id}' ORDER BY T098_Id DESC;
EOT;
	$result = mysql_query($sql);
	
	$count=1;
	$ultimaAlteracao="";
	while($linha = mysql_fetch_array($result)){
		$data = explode(" ", $linha['T098_Data']);
		$data = $data[0];
		if($count==1){
			$ultimaAlteracao = $data;	
		}
		if(($D009_Preco_2 != $linha['T098_D009_Preco_2']) && ($linha['T098_D009_Preco_2'] != '0.00') && ($linha['T098_Flag_Promocao'] == "N")){
			if($campoRetorno=="preco"){
				return $linha['T098_D009_Preco_2'];
			}else if($campoRetorno=="data"){
				return gCorrigeData($data);
			}
		}
		$count++;
	}
	if($campoRetorno=="preco"){
		return $D009_Preco_2;
	}else if($campoRetorno=="data"){
		return gCorrigeData($ultimaAlteracao);
	}
}

function validaPrecoRetorno3Promocao($array, $campoRetorno){
	global $g;
	
	$D009_Id=$array['todosCampos']['D009_Id'];
	$D009_Preco_3 = $array['todosCampos']['D009_Preco_3'];
	$sql = <<<EOT
		SELECT T098_D009_Preco_3, T098_Data, T098_Flag_Promocao from T098 WHERE T098_D009_Id='{$D009_Id}' ORDER BY T098_Id DESC;
EOT;
	$result = mysql_query($sql);
	
	$count=1;
	$ultimaAlteracao="";
	while($linha = mysql_fetch_array($result)){
		$data = explode(" ", $linha['T098_Data']);
		$data = $data[0];
		if($count==1){
			$ultimaAlteracao = $data;	
		}
		if(($D009_Preco_3 != $linha['T098_D009_Preco_3']) && ($linha['T098_D009_Preco_3'] != '0.00') && ($linha['T098_Flag_Promocao'] == "N")){
			if($campoRetorno=="preco"){
				return $linha['T098_D009_Preco_3'];
			}else if($campoRetorno=="data"){
				return gCorrigeData($data);
			}
		}
		$count++;
	}
	if($campoRetorno=="preco"){
		return $D009_Preco_3;
	}else if($campoRetorno=="data"){
		return gCorrigeData($ultimaAlteracao);
	}
}

	function retornaTipoOperacao($array){
		if (($array['todosCampos']['D184_Tipo_Operacao']) === 'D'){
			return 'DIVISÃO';
		}
		return 'MULTIPLICAÇÃO';
	}

	function verificaCiap($array){
		$D009_Id = $array['todosCampos']['D009_Id'];
		
		$T316 = mysql_query("SELECT T316_Id
							   FROM T316
						  LEFT JOIN T014 ON T014_Id = T316_T014_Id
						      WHERE T014_D009_Id = '{$D009_Id}'
							  LIMIT 1;");
		if(mysql_num_rows($T316) > 0){
			return "<font style='background:green;color:white;padding:4px;border-radius: .425rem;'>SIM</font>";
		}else{
			return "<font style='background:red;color:white;padding:4px;border-radius: .425rem;'>NÃO</font>";
		}		
	}
	
?>
