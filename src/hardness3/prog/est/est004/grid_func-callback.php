<?php
namespace hardness;
	/**
	* Weiberlan
	* 29/11/2011
	*/

require_once('bibliotecas/funcoesCallback/abrirJanela.php');

function validaCampoAlmoxarifado($array){
	$retorno = "<a href=\"#\" onclick=\"nova_janela('/hardness3/hardness/nota_recebida/T013_imprimir_almoxarifado.php?T013_Id={$array['todosCampos']['T013_Id']}',0,0,800,500,'no','no','no','no','yes','yes',0);\" style='cursor:pointer'>Imprimir</a>";
	return $retorno;
}
function validaCampoDanfe($array){
	$retorno = "";
	$retorno = "<a href=\"#\" onclick=\"nova_janela('/hardness3/hardness/nota_recebida/gerar_danfe.php?T013_Id={$array['todosCampos']['T013_Id']}',0,0,800,500,'no','no','no','no','yes','yes',0);\" style='cursor:pointer'>DANFE</a>";
	return $retorno;
}

function validaCampoXML($array){
	// echo "<td align=center bgcolor='$cor' class='td_itens' onclick=\"javascript:parent.AbrirXML('$mT013[T013_Id]')\" style='cursor:pointer'>";

	global $confUsuario, $g;
	if (!empty($array['todosCampos']['T013_Chave_Acesso_Nfe'])){
		if (!empty($array['todosCampos']['T013_Numero_Protocolo_Nfe'])){
                //if (file_exists("dados_usuarios/{$confUsuario['dbDatabase']}/nfe/xml/recebido/{$array['todosCampos']['T013_Numero_Protocolo_Nfe']}.xml")){
                if (file_exists("{$g['pathDadosAntigo']}/nfe/xml/recebido/{$array['todosCampos']['T013_Numero_Protocolo_Nfe']}.xml")){
				$retorno = "<a href=\"/est/est004/grid_func-ajax/downloadxml/?ajax=true&T013_Id=" . urlencode($array['todosCampos']['T013_Id']) . "\">XML</a>";
			} else {
				$retorno = "<a title='ATENÇÃO! O arquivo XML referente a esta NFe não existe!'>Erro!</a>";
			}
			return $retorno;
		}
		return '';
	}else{        
		return '';
	}
	 return false;		
}


function validaCampoCancelar($array){
		global $g;

		if(!isset($g['c029Ids'][189])){
			return "-";
		}

		if($array['todosCampos']['T013_Flag_Cancelada']=='S'){
			$retorno = "";
			$retorno = <<<EOT
			 <font style='color: var(--BGVermelho);'><a href="#" onclick="EST004ReverterCancelamento('{$array['todosCampos']['T013_Id']}', '{$g['divId']}');">Cancelada</a></font>
EOT;
			return $retorno;
		}elseif($array['todosCampos']['T013_Flag_Travar_Nota']=='S'){
			$retorno="";
			//$retorno="<a href=\"#\" onclick=\"nova_janela('/hardness/nota_recebida/cancelar_nf.php?T013_Id={$array['todosCampos']['T013_Id']}&Nova_Janela=S','NF {$array['todosCampos']['T013_Id']}',0,0,800,500,'no','no','no','no','yes','yes',0);\" style='cursor:pointer'>Cancelar</a>";
			return <<<EOT
			    <a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cancelar Nota Fiscal de Entrada', '/est/est004/content/est004ContentCancelarNotaEntrada/', '&tab=geral&T013_Id=' + encodeURIComponent('{$array['todosCampos']['T013_Id']}'), [500,250]);"><u>Cancelar</u></a>
EOT;
		}else{
			//$retorno = "";
			//$retorno = "<a href=\"#\" onclick=\"/est/est004/grid_func-ajax/excluirNota/?T013_Id='" . urlencode($array['todosCampos']['T013_Id']) . "'\">Excluir</a>";
			//$retorno = "<a href=\"#\"  style='cursor:pointer'>Excluir</a>";
			return <<<EOT
				<a href="#" onclick="EST004ExcluirNota('{$array['todosCampos']['T013_Id']}', '{$g['divId']}')">Excluir</a>
EOT;
		}
	}
/*
function validaCampoCFOP($array) {
	$cClausula  = " select ";
	$cClausula .= " D006_Id, ";
	$cClausula .= " D006_Codigo_CFOP, ";
	$cClausula .= " D006_Descricao_Comercial ";
	$cClausula .= " from D006 ";
    $cClausula .= " where D006_Flag_Inativo!='S' ";
    $cClausula .= " and D006_Flag_Entrada_Saida='E'";
    $cClausula .= " order by D006_Codigo_CFOP ";
	$cSql_D006=mysql_query($cClausula);
	
	$retorno = mysql_fetch_array($cSql_D006);
	
	return $retorno['D006_Descricao_Comercial'];
	
	// if ($mC004[D018_UF]==$mT013[D018_UF] and $mT013[D030_Nome_Pais]=='BRASIL')
    // {
        // $cClausula .= " and D006_Flag_Estado='D' ";
    // }
    // elseif ($mC004[D018_UF]!=$mT013[D018_UF] and $mT013[D030_Nome_Pais]=='BRASIL')
    // {
        // $cClausula .= " and D006_Flag_Estado='F' ";
    // }
}
*/
function T014_D006_Id_CFOP($array){
	$sql = "SELECT D006_Codigo_CFOP FROM D006 WHERE D006_Id = '{$array['todosCampos']['T014_D006_Id']}'";
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	return $row['D006_Codigo_CFOP'];
}

function T014A_D014_Id_PlanoContas($array){
	$sql = "SELECT D014_SubConta FROM D014 WHERE D014_Id = '{$array['todosCampos']['T014A_D014_Id']}'";
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	return $row['D014_SubConta'];
}

function validaCampoExcluir($array){
	global $g;
	$restricao = $g['conf']->restricaoTravar('excluir', 'T014_Id', $array['todosCampos']['T014_Id'], 'T014_T013_Id', $array['todosCampos']['T014_T013_Id']);
	if ($restricao === true) {
		return <<<JS
			<span onclick="est004ExcluirProduto('{$array['todosCampos']['T014_Id']}', '{$array['todosCampos']['T014_T013_Id']}', '{$g['divId']}');"> 
				<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
			</span>
		JS;
	}
}

function coloreLinha($array){
	if ($array['todosCampos']['T013_Flag_Travar_Nota'] == 'N' or $array['todosCampos']['T013_Flag_Cancelada']=='S'){
		return "<font style='color:var(--BGVermelho)'>" . $array['todosCampos']['D024_Nome_Empresa'] . "</font>";
	}else if($array['todosCampos']['T013_Flag_Estoque_Finalizado'] == 'S'){
		return "<font style='color:var(--letra-grid-linhaCinza)'>" . $array['todosCampos']['D024_Nome_Empresa'] . "</font>";
	}else if($array['todosCampos']['T013_Flag_Travar_Nota'] == 'S'){
		return "<font style='color:var(--letra-grid-linhaPreta)'>" . $array['todosCampos']['D024_Nome_Empresa'] . "</font>";
	}
}

function coloreLinhaProdutos($array){
	global $g;
	if ($array['todosCampos']['T014_D009_Id'] <= 0 or $array['todosCampos']['D009_C004_Id'] != $g['empresaAtual']){
		return "<font style='color:var(--BGVermelho)'>" . $array['todosCampos']['T014_Descricao_Produto'] . "</font>";
	}else{
		return "<font style='color:var(--letra-grid-linhaPreta)'>" . $array['todosCampos']['T014_Descricao_Produto'] . "</font>";
	}
}

function est004_Dia_Semana($array) {
	global $g;
	$data=$array['todosCampos']['T154_Data_Vencimento'];
	return gPegaDiaSemana($data);
}

function T154_D031_Id_Portador($array) {
	return $array['todosCampos']['D031_Portador'];
}
function editarT086_Quantidade($array, $T013_Id){
	global $g;
	$qnt = number_format($array['todosCampos']['T086_Quantidade'], 3, ',', ' ');
    $qtdeEditar = $array['todosCampos']['T086_Quantidade'];
    $qtdeEditar = ($qtdeEditar>0 ? $qtdeEditar : '');
	$T225_Id = $array['todosCampos']['T225_Id'];
	return "<input style='cursor:pointer;' type='button' value='{$qnt}' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Inserir Quantidade', '/est/est004/content/est004ContentInserirQuantidade/', '&tab=geral&T013_Id=' + encodeURIComponent('{$T013_Id}') + '&T225_Id=' + encodeURIComponent('{$T225_Id}') + '&T086_Quantidade=' + encodeURIComponent('{$qtdeEditar}'), [300,150]);\" />";
}

function validaTipo($array){
	$mDocumento[1]='NF';
	$mDocumento[2]='ROM';
	$mDocumento[3]='ACP';
	$mDocumento[4]='CF';
	$mDocumento[5]='D1';
	$mDocumento[6]='NFe';
	$mDocumento[7]='NFSe';
	if (empty($array['todosCampos']['T007_Flag_ACP'])) { $array['todosCampos']['T007_Flag_ACP'] = 1; }
	$retorno = $mDocumento[$array['todosCampos']['T007_Flag_ACP']]; 
	return $retorno;
}

function receberNota($array) {
	global $g;
	return "<a href=\"#\"><span class=\"ui-icon ui-icon-circle-plus\" title=\"Liberar\" 
								onClick=\"est004ReceberNota('{$array['todosCampos']['T007_Id']}','{$array['todosCampos']['T007_Numero_Nota_Fiscal']}', '{$g['divId']}', '{$g['divIdRoot']}')\">
						  </span>
		  </a>";
}

function D006_Codigo_CFOP($array) {
	$valor = $array['campoAtual'];
	//log("{$array['campoAtual']} != {$array['todosCampos']['D6NF_Codigo']}");
	if ($array['campoAtual'] != $array['todosCampos']['D6NF_Codigo']) {
		return '<span style="font-weight: bold; color: var(--BGVermelho);">' . $valor . '</span>';
	} else {
		return '<span style="">' . $valor . '</span>';
	}
}

function T014_Classificacao_Fiscal($array) {
	$valor = $array['campoAtual'];
	if ($array['campoAtual'] != $array['todosCampos']['T014_Classificacao_Fiscal']) {
		return '<span style="font-weight: bold; color: var(--BGVermelho);">' . $valor . '</span>';
	} else {
		return '<span style="">' . $valor . '</span>';
	}
}

function T014_Valor_Total_Custo_Recebida($array) {
	$valor = gCorrigeNumero($array['campoAtual']);
	if ($array['campoAtual'] != $array['todosCampos']['T014_Valor_Total_Custo']) {
		return '<span style="font-weight: bold; color: var(--BGVermelho);">' . $valor . '</span>';
	} else {
		return '<span style="">' . $valor . '</span>';
	}
}

function T014_Valor_Total_Preco_Recebida($array) {
	$valor = gCorrigeNumero($array['campoAtual']);
	$valorNF = $array['todosCampos']['T014_Valor_Total_Custo']-$array['todosCampos']['T014_Valor_Total_Desconto'];
	if ($array['campoAtual'] != $valorNF) {
		log($array['campoAtual']."!=".$valorNF);
		return '<span style="font-weight: bold; color: var(--BGVermelho);">' . $valor . '</span>';
	} else {
		return '<span style="">' . $valor . '</span>';
	}
}


function T014_Valor_ICMS_Recebida($array) {
	$T014_Id = $array['todosCampos']['T014_Id'];
	$T014_Id_Duplicado = $array['todosCampos']['T014_Id_Duplicado'];
	$sql = mysql_query("SELECT SUM(T014_Valor_ICMS_Recebida) FROM T225 T225B left join T014 T014B on T014B.T014_T225_Id=T225B.T225_Id 
	WHERE T014B.T014_Id='{$T014_Id}' 
	   OR T014B.T014_Id_Duplicado='{$T014_Id}' 
	   OR T014B.T014_Id='{$T014_Id_Duplicado}' 
	   OR T014B.T014_Id_Duplicado='{$T014_Id_Duplicado}'");
	$row = mysql_fetch_array($sql); 
	$array['campoAtual'] = $row[0];
	$valor = gCorrigeNumero($array['campoAtual']);
	if ($array['campoAtual'] != $array['todosCampos']['T014_Valor_ICMS']) {
		return '<span style="font-weight: bold; color: var(--BGVermelho);">' . $valor . '</span>';
	} else {
		return '<span style="">' . $valor . '</span>';
	}
}

function T014_Valor_IPI_Recebida($array) {
	$T014_Id = $array['todosCampos']['T014_Id'];
	$T014_Id_Duplicado = $array['todosCampos']['T014_Id_Duplicado'];
	$sql = mysql_query("SELECT SUM(T014_Valor_IPI_Recebida) FROM T225 T225B left join T014 T014B on T014B.T014_T225_Id=T225B.T225_Id 
	WHERE T014B.T014_Id='{$T014_Id}' 
	   OR T014B.T014_Id_Duplicado='{$T014_Id}' 
	   OR T014B.T014_Id='{$T014_Id_Duplicado}' 
	   OR T014B.T014_Id_Duplicado='{$T014_Id_Duplicado}'");
	$row = mysql_fetch_array($sql); 
	$array['campoAtual'] = $row[0];
	$valor = gCorrigeNumero($array['campoAtual']);
	if ($array['campoAtual'] != $array['todosCampos']['T014_Valor_IPI']) {
		return '<span style="font-weight: bold; color: var(--BGVermelho);">' . $valor . '</span>';
	} else {
		return '<span style="">' . $valor . '</span>';
	}
}

function T014_Valor_ICMS_Substituicao_Recebida($array) {
	$T014_Id = $array['todosCampos']['T014_Id'];
	$T014_Id_Duplicado = $array['todosCampos']['T014_Id_Duplicado'];
	$sql = mysql_query("SELECT SUM(T014_Valor_ICMS_Substituicao_Retencao_Recebida) FROM T225 T225B left join T014 T014B on T014B.T014_T225_Id=T225B.T225_Id 
	WHERE T014B.T014_Id='{$T014_Id}' 
	   OR T014B.T014_Id_Duplicado='{$T014_Id}' 
	   OR T014B.T014_Id='{$T014_Id_Duplicado}' 
	   OR T014B.T014_Id_Duplicado='{$T014_Id_Duplicado}'");
	$row = mysql_fetch_array($sql); 
	$array['campoAtual'] = $row[0];
	$valor = gCorrigeNumero($array['campoAtual']);
	if ($array['campoAtual'] != $array['todosCampos']['T014_Valor_ICMS_Substituicao']) {
		return '<span style="font-weight: bold; color: var(--BGVermelho);">' . $valor . '</span>';
	} else {
		return '<span style="">' . $valor . '</span>';
	}
}

function T014_Valor_Custo_Unitario_Recebida($array) {
	$valor = number_format($array['campoAtual'],6,',','.');
	if ($array['campoAtual'] != $array['todosCampos']['T014_Valor_Custo_Final_Unitario']) {
		return '<span style="font-weight: bold; color: var(--BGVermelho);">' . $valor . '</span>';
	} else {
		return '<span style="">' . $valor . '</span>';
	}
}

function duplicarItemNFEntrada($array) {
	global$g;	
	if(isset($g['c029Ids'][189])){
		return <<<EOT
			<a href="#" onclick="duplicarItemNFEntrada('{$array['todosCampos']['T014_Id']}','{$g['divId']}'); return false;">Duplicar Item</a>
EOT;
	} else {
		return "-";
	}
}

function insereRegistroT001($array){
	global $g;
	$T014_Id = $array['todosCampos']['T014_Id'];
	
	if($g['usuarioAtual'] == 63 AND $array['todosCampos']['T001_Id'] <= 0){
		return <<<EOT
		<a href="#" onclick="insereRegistroT001('{$array['todosCampos']['T014_Id']}','{$g['divId']}')">
			<span title="Insere T001">Insere T001</span>
		</a>
EOT;
	}else{
		return $array['todosCampos']['T001_Id'];
	}
}

function comparaDescricao($array, $param = NULL){
	// [0] = T014_Valor_Custo_Unitario
	similar_text($array['todosCampos']['T014_Descricao_Produto'],$array['todosCampos']['D001_Descricao_Produto'], $comparaDescricao);
	$comparaDescricao = gCorrigeNumero($comparaDescricao);
	if ($comparaDescricao<50)  {
		return "<font style='color: var(--BGVermelho)'>{$comparaDescricao}</font>";
	} else if ($comparaDescricao>=50 && $comparaDescricao<=70)  {
		return "<font style='color:var(--BGVerde);font-weight:bold'>{$comparaDescricao}</font>";
	} else if ($comparaDescricao>70)  {
		return "<font style='color:var(--BGAzul);font-weight:bold'>{$comparaDescricao}</font>";
	} else {
		return $comparaDescricao;
	}
}

function comparaNCM($array, $param = NULL){
	// [0] = T014_Valor_Custo_Unitario
	// [1] = T014_Classificacao_Fiscal
	if ($array['todosCampos']['T225_Valor_Preco_Sem_Desconto_Unitario']==$param[0] && $array['todosCampos']['D005_Classificacao_Fiscal']!=$param[1])  {
	   	return "<font style='color: var(--BGVermelho)'>{$array['todosCampos']['D005_Classificacao_Fiscal']}</font>";
	} else {
		return $array['todosCampos']['D005_Classificacao_Fiscal'];
	}
}

function campoCancelamento($array){
	global $g;
	if($array['todosCampos']['T224_Flag_Status'] == '' || $array['todosCampos']['T224_Flag_Status'] == '0' || $array['todosCampos']['T224_Flag_Status'] == '2' || $array['todosCampos']['T224_Flag_Status'] == '3'){
		return <<<EOT
		<a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cancelar Ordem de compra', '/est/est016/content/est016contentCancelarPedido/', '&T224_Id={$array['todosCampos']['T224_Id']}', [350,200]);">
    		<span class="ui-icon ui-icon-circle-close" title="Cancelar pedido"></span>
		</a>
EOT;
	}
}

function campoTipoOperacao($array) {

	$operacoes = [
		'D' => 'DIVISÃO',
		'M' => 'MULTIPLICAÇÃO'
	];

	return $operacoes[$array['todosCampos']['D184_Conv.D184_Tipo_Operacao']] ?? '-';
}

function campoUnidadeNotaFiscal($array) {   
    $temUnidade = $array['todosCampos']['D037_Id_Conv']  !== null;
    $naoPrecisaRegra = $array['todosCampos']['D001_D037_Id'] === $array['todosCampos']['D037_Id_Conv'];
    $temRegra = $array['todosCampos']['D184_D037_Id_Conv'] !== null;  

	if( !$temUnidade) {                        
        return "<font style='background-color: var(--BGVermelho); color:white'> {$array['campoAtual']} </font>";        
    } else if($temUnidade && !$naoPrecisaRegra &&  !$temRegra) {
        return "<font style='color: var(--BGVermelho)'> {$array['campoAtual']} </font>";        
    } else {
        return $array['campoAtual'];
    }
}

function validaCampoCadastroProduto($array, $params = null){
	global $g;	

	if ($array['todosCampos']['T014_D009_Id'] > 0) {
		if(empty($array['campoAtual'])) {
			$array['campoAtual'] = 'sem código';
		}
		return abrirJanela($array, $params);
	}

	if($g['C031']['incluirProdutoNotaFiscalEntrada'] === 'S') {
	
		return <<<EOT
			<a href="#" onClick="est004IncluirProduto('{$array['todosCampos']['T014_Id']}', '{$g['divId']}', '{$g['divIdRoot']}')">		
				Incluir Produto
			</a>	
		EOT;
	}

	return '-';
}
function validaCampoTipo($array) {	
	$mDocumento[1]='NF-e';
	$mDocumento[2]='ACP';
	$mDocumento[3]='NF Consumidor';
	$mDocumento[4]='Cupom Fiscal';
	$mDocumento[5]='NF';
	$mDocumento[6]='NF/Conta de Energia Elétrica';
	$mDocumento[7]='NFS de Comunicação';
	$mDocumento[8]='NFS de Telecomunicação';
	$mDocumento[9]='Outros';
	$mDocumento[10]='NFS';
	$mDocumento[11]='NF3e';
	$mDocumento[12]='NFCom';

	if (empty($array['todosCampos']['T013_Tipo']) || !array_key_exists($array['todosCampos']['T013_Tipo'],$mDocumento)) { 
		$array['todosCampos']['T013_Tipo'] = 1; 
	}

	return $mDocumento[$array['todosCampos']['T013_Tipo']]; 
}