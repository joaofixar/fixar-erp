<?php
namespace hardness;
/**
* Este código PHP irá substituir o callback: retornaPrecoVenda()
*/

function retornaPrecoVenda ($array,$param=false) {
	global $g;
	
	if($param[1]>0){


		$T003_Flag_Lista_Preco=$param[1];

		$precoTabelado = 0;

		switch($T003_Flag_Lista_Preco){
			case '1':
				$precoTabelado = $array['todosCampos']['D009_Preco_1'];
			break;
			case '2':
				$precoTabelado = $array['todosCampos']['D009_Preco_2'];
			break;
			case '3':
				$precoTabelado = $array['todosCampos']['D009_Preco_3'];
			break;
		}	
	}

	if(!isset($array['todosCampos']['T003_Id']) && isset($param[0])){
		$array['todosCampos']['T003_Id']=$param[0];
	}

	$sql = "SELECT T003_D024_Id, T003_C004_Id, T003_Flag_Lista_Preco, T003_Flag_Acp, T003_Flag_Destino_Produto, T003_C004_Id_Estoque FROM T003 WHERE T003_Id = '{$array['todosCampos']['T003_Id']}'";
	$T003 = mysql_query($sql);
	$mT003 = mysql_fetch_assoc($T003);

	if ($precoTabelado > 0 || $g['C031']['tipoPreco']=='M'){

		// Antes de retornar o preço tabelado precisa verificar se o cliente tem tabela cadastrada
		$D160 = mysql_query("SELECT D160_Valor_Preco 
							   FROM D160 
							  WHERE D160_D024_Id = '{$mT003['T003_D024_Id']}'  
								AND D160_D009_Id = '{$array['todosCampos']['D009_Id']}'");
		if(mysql_num_rows($D160) == 1){
			$mD160 = mysql_fetch_array($D160);
			if($mD160['D160_Valor_Preco'] > 0){
				$precoTabelado = $mD160['D160_Valor_Preco'];
			}
		}
		return number_format($precoTabelado, 4, ',', '.');
	}

	//Buscando as aliquotas digitadas na tela de montagem do ipv no orçamento
	$T250 = mysql_query("SELECT SUM(T250_Aliquota-T250_Aliquota_Original) AS AliquotasIPV 
						   FROM T250 
						  WHERE T250_T003_Id = '{$array['todosCampos']['T003_Id']}' 
							AND T250_Flag_Altera_Preco='S' 
							AND T250_Grupo='IPV'");
	$mT250 = mysql_fetch_array($T250);
	$TotalPorcentagemIPV = $mT250['AliquotasIPV'];

	//Buscando as aliquotas digitadas na tela de montagem do icf no orçamento
	$T250icf = mysql_query("SELECT SUM(T250_Aliquota-T250_Aliquota_Original) AS AliquotasICF 
							  FROM T250 
							 WHERE T250_T003_Id = '{$array['todosCampos']['T003_Id']}' 
							   AND T250_Flag_Altera_Preco='S' 
							   AND T250_Grupo='ICF'");
	$mT250icf = mysql_fetch_array($T250icf);
	$TotalPorcentagemICF = $mT250icf['AliquotasICF'];

	// Preço e demonstrativo
	require_once('bibliotecas/classes/CAD002.php');
	$CAD002 = new CAD002();
	require_once('bibliotecas/classes/VEN001.php');
	$VEN001 = new VEN001();

	$cfop 	   = $VEN001->retornaCFOP($mT003['T003_D024_Id'], $array['todosCampos']['D009_Id'], $mT003['T003_C004_Id'], $mT003['T003_Flag_Destino_Produto'], false, false, false, $mT003['T003_C004_Id_Estoque']);
	$aliquotas = $VEN001->retornaAliquotas($array['todosCampos']['D009_Id'], $array['todosCampos']['T003_Id'], false, false, false, false, false, $cfop);
	$preco 	   = $CAD002->retornaPrecoVenda($array['todosCampos']['D009_Id'], $mT003['T003_Flag_Lista_Preco'], $mT003['T003_D024_Id'], false, $cfop, $aliquotas['Aliquota_ICMS'], false, false, 4, $aliquotas['Aliquota_PIS'], $aliquotas['Aliquota_COFINS'], $aliquotas['Aliquota_IPI'], $aliquotas['ST_VA'], $aliquotas['Percentual_Reducao_ICMS_ST'], $aliquotas['ST_ICMS_Interno'], $aliquotas['Percentual_Reducao_ICMS'], $aliquotas['ICMS_Inter_Estadual'], $aliquotas['Aliquota_IRPJ'], $aliquotas['Aliquota_CSLL'], 0, $TotalPorcentagemIPV, $TotalPorcentagemICF);
	
	return $preco[0];

	/*
	// Monta demonstrativo
	$idRetorno = 'popover-retorno-' . uniqid();
$retorno = "<table cellpadding=0 cellspacing=0 style='display: none; border: 0; margin: 0; padding: 0;' id='{$idRetorno}'>";
$demonstrativo = $preco[1];
foreach ($demonstrativo as $campo) {
	$border = '';
	$valor = gCorrigeNumero($campo['valor'],4);
	$retorno .= <<<EOT
<tr>
	<td style="{$border}">
		{$campo['campo']}
	</td>
	<td style="{$border}">
		{$campo['operacao']}
	</td>
	<td style="{$border} text-align: right;">
		{$valor}
	</td>
</tr>
EOT;
}
$retorno .= <<<EOT
<tr>
	<td style="border-top: 1px solid black;">
		 
	</td>
	<td style="border-top: 1px solid black;">
		=
	</td>
	<td style="border-top: 1px solid black; text-align: right;">
		{$row['T004_Valor_Preco_Original']}
	</td>
</tr>
</table>
EOT;
	echo $retorno;
	
	// abrirNotificacao (é replicado aqui para não precisa de mais uma chamada AJAX)
	$titulo = 'Preço de Venda';
	$id = 'popover-' . uniqid();
	$onclick = <<<EOT
if ($('#{$id}').data('show')) {
	$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
} else {
	$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);

			$('#{$id}').popover('destroy');
			$('#{$id}').popover({
			    html: true,
			    placement: 'right',
			    title: '{$titulo}',
			    content: $('#{$idRetorno}').html()
			});
			$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
			$('#{$id}').popover('show');
			$('#{$id}').data('show', true);
			$('<span class=\'ui-icon ui-icon-closethick\' style=\'float: right; margin: -3px -8px;\'>close</span>').bind('click', function() {
				$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
				$('body').unbind('click');
			}).appendTo($('.popover-title'));
			$('body').unbind('mousedown').bind('mousedown', function(event) {
				if (!$(event.target).closest('.popover').size()) {
					$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
					$('body').unbind('mousedown');
				}
			});

}
EOT;
	$onclick = str_replace("\n", ' ', $onclick);
	return <<<EOT
<a id="{$id}" href="#" onclick="{$onclick}">{$preco[0]}</a>
EOT;
	*/
}
