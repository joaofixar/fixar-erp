<?php
namespace hardness;
require_once("old/lib/funcoes.php");

/**
* FIS012 - FIS012 
*/
function txSefazMDFe($array, $param = false){
	
	global $g;

	$imprimirAutomatico = ($param['imprimirAutomatico'] == true) ? 'S' : 'N';	
	$encerrar = ($param['encerrar'] == 'S') ? 'S' : 'N';	
	$encerrado = ($param['encerrado'] == 'S') ? 'S' : 'N';

	/**
	* Esté código é duplicado (com algumas alterações no sistema antigo!
	* Alterou aqui, alterar lá também
	*/
	// Caso incomum: protocolo foi limpo na mão, será enviada novamente
	if (empty($array['todosCampos']['T276_Numero_Protocolo_MDFe']) && $array['todosCampos']['T276_TX_Resposta_Codigo'] == 9) {
		$sql = <<<EOT
UPDATE 
	T276 
SET 
	T276_TX_Resposta_Codigo = 0
	, T276_TX_Resposta_Extra = ''
	, T276_TX_Retorno_Sefaz = ''
	, T276_TX_Flag_Finalizada = 0
	, T276_TX_Flag_Finalizada = '0000-00-00 00:00:00'
WHERE 
	T276_Id = '{$array['todosCampos']['T276_Id']}';
EOT;
		mysql_query($sql);
		$array['todosCampos']['T276_TX_Resposta_Codigo'] = 0;
		$array['todosCampos']['T276_TX_Resposta_Extra'] = '';
		$array['todosCampos']['T276_TX_Retorno_Sefaz'] = '';
		$array['todosCampos']['T276_TX_Flag_Finalizada'] = 0;
		$array['todosCampos']['T276_Data_Envio_XML'] = '0000-00-00 00:00:00';

	}
	$Query = urlencode(md5($array['todosCampos']['T276_C004_Id'].'|'.$array['todosCampos']['T276_Id']));

	$emailSpace = '';
	$return = '';

	$ambienteMDFe = '';
	if($array['todosCampos']['T276_Ambiente_MDFe'] == 2){
		$ambienteMDFe .= "<button type='button' style='display: none; color: black; font-weight: bold; font-size:14px; background: #FFD700;' scan='N' refazer='N' class='btnEnviarMDFE'>";
		$ambienteMDFe .= "Enviar MDF-e em Homologação sem valor fiscal";
		$ambienteMDFe .= "</button>";
	}else{
		$ambienteMDFe  = "<button type='button' style='display: none; color: green; font-weight: bold; font-size:14px;' scan='N' refazer='N' class='btnEnviarMDFE'>";
		$ambienteMDFe .= "Enviar MDF-e";
		$ambienteMDFe .= "</button>";
	}
	
	//if ($array['todosCampos']['T276_TX_Resposta_Codigo'] == 0) {
	$id = md5(uniqid() . time());
	$func = 'f' . md5(uniqid() . time());
	$return .= <<<EOT
<div id="{$id}">
	<div style="float: left;">
		<!-- botão first -->
		{$ambienteMDFe}{$emailSpace}
		<!-- botão 1 -->
		<button type="button" style="display: none; color: #FA4242; font-weight: bold; font-size:14px;" scan='N' refazer='N'>
			Encerrar MDF-e{$emailSpace}
		</button>
		<!-- botão 2 -->
		<button type="button" style="display: none; font-size:14px;">
			DAMDFE{$emailSpace}
		</button>
		<!-- botão 3 -->
		<button type="button" style="display: none; opacity: 0.5; color: green; font-weight: bold; font-size:14px;">
			Fechar{$emailSpace}
		</button>
	</div>
	<div style="float: left; padding: 5px 11px;">
		<span class="progresso"></span>
	</div>
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	$('#{$id} button:first').click(function() { 
		if ($(this).css('opacity') == '0.5') {
			return false;
		}
		$(this).css('opacity', '0.5').find('span').text('Transmitindo...');
		$('#{$id} .progresso').html('Iniciando processo...');
		$('<img>').attr('src', '/hardness3/static/img/nfe2.gif').css('margin-right', '13px').prependTo($('#{$id} .progresso'));
		$.getJSON('/fis/fis012/grid_func-ajax/txSefazMDFe/?ajax=true&tx=true&T276_Id=' + encodeURIComponent('{$array['todosCampos']['T276_Id']}') + '&refazer=' + encodeURIComponent($(this).attr('refazer')) + '&scan=' + encodeURIComponent($(this).attr('scan')) + '&callback=?', function(request) {
			setTimeout('{$func}()', 1000);
		});
	});
	$('#{$id} button:eq(1)').click(function() {<!--Encerrar-->
		$('#{$id} button:eq(0)').hide();
		if ($(this).css('opacity') == '0.5') {
			return false;
		}
		$(this).css('opacity', '0.5').find('span').text('Encerrando MDF-e...');
		$('#{$id} .progresso').html('Iniciando processo...');
		$('<img>').attr('src', '/hardness3/static/img/nfe2.gif').css('margin-right', '13px').prependTo($('#{$id} .progresso'));
		$.getJSON('/fis/fis012/grid_func-ajax/txSefazMDFe/?ajax=true&tx=true&T276_Id=' + encodeURIComponent('{$array['todosCampos']['T276_Id']}') + '&refazer=' + encodeURIComponent($(this).attr('refazer')) + '&scan=' + encodeURIComponent($(this).attr('scan')) + '&callback=?', function(request) {
			setTimeout('{$func}()', 1000);
		});
	});
	$('#{$id} button:eq(2)').click(function() {
		window.open('/hardness3/hardness/damdfe/imprimir_damdfe.php?Query={$Query}&vlrNF={$valorNaDanfe}&T276_Id={$array['todosCampos']['T276_Id']}&T276_C004_Id={$array['todosCampos']['T276_C004_Id']}','DANFE {$array['todosCampos']['T276_Id']}','toolbar=yes,menubar=yes,location=no,scrollbars=yes,status=yes,resizable=yes,width=990,height=542,top=0,left=0');
	});
	$('#{$id} button:eq(3)').click(function() {
		if ($(this).css('opacity') == '0.5') {
			return false;
		}
		$(this).closest('tr').remove();
		$.getJSON('/fis/fis012/grid_func-ajax/fechar/?ajax=true&T276_Id=' + encodeURIComponent('{$array['todosCampos']['T276_Id']}') + '&callback=?', function(request) {
			//
		});
	});
	function {$func}() {
		$.getJSON('/fis/fis012/grid_func-ajax/txSefazMDFe/?ajax=true&T276_Id=' + encodeURIComponent('{$array['todosCampos']['T276_Id']}') +'&encerrar= '+ encodeURIComponent('{$encerrar}')+'&encerrado= '+ encodeURIComponent('{$encerrado}')+ '&callback=?', function(request) {
			$('#T276_Id-{$array['todosCampos']['T276_Id']}-after').remove();
			$('#T276_Id-{$array['todosCampos']['T276_Id']}-space1').remove();
			if (!request.exibir) {
				$('#{$id} button:eq(0)').hide();
				$('#{$id} button:eq(1)').hide();
				return false;
			}
			// Faz a validação da UF de descarregamento, se estiver tudo certo mostra o botão para encerrar o MDF-e
			if(request.encerrar){
				if('{$array['todosCampos']['descarregamentoD018.D018_UF']}' == '0' || '{$array['todosCampos']['descarregamentoD018.D018_UF']}' == null || '{$array['todosCampos']['descarregamentoD018.D018_UF']}' <= 0 ){
					$('#{$id} button:eq(1)').hide();
					$('#{$id} button:first').hide();	
					$('#{$id} .progresso').html(request.teste);
				}else{					
					$('#{$id} button:eq(1)').show();
					$('#{$id} button:first').hide();
				}
			}else if(request.encerrado){
				$('#{$id} button:eq(1)').hide();
				$('#{$id} button:first').hide();
				$('#{$id} button:eq(2)').show();
				$('#{$id} button:eq(3)').show().css('opacity', '1');
			}else{
				$('#{$id} button:eq(1)').hide();
				$('#{$id} button:first').show();
			}
			if (request.T276_TX_Resposta_Codigo < 100) {
				if (request.T276_TX_Resposta_Codigo != 0) {
					if (request.T276_TX_Resposta_Codigo != 9 && request.T276_TX_Resposta_Codigo != 15) {
						//$('#{$id} button:eq(0)').show();
						$('#{$g['divId']}').find('#T276_Id-{$array['todosCampos']['T276_Id']}').removeClass('linhaSuccess linhaErrorNoColor').addClass('linhaInfo');
						$('#{$id} .progresso').html(request.codigoTXT);
						$('<img>').attr('src', '/hardness3/static/img/nfe2.gif').css('margin-right', '13px').prependTo($('#{$id} .progresso'));
						setTimeout('{$func}()', 1000);
					} else {
						if (request.denegada == '1') {
							var cor = 'red';
						} else {
							var cor = 'green';
						}
						$('#{$id} .progresso').html('<span style="color: ' + cor + '; font-weight: bold;">' + request.codigoTXT + '</span>');
						if((request.encerrar && request.T276_TX_Resposta_Codigo == 15)){
							$('#{$g['divId']}').find('#T276_Id-{$array['todosCampos']['T276_Id']}').removeClass('linhaErrorNoColor linhaInfo').addClass('linhaSuccess');
							$('#{$id} button:eq(1)').hide();
							$('#{$id} button:eq(2)').show();
							$('#{$id} button:eq(3)').show().css('opacity', '1');
						}else if(request.encerrar && request.T276_TX_Resposta_Codigo == 9){
							$('#{$g['divId']}').find('#T276_Id-{$array['todosCampos']['T276_Id']}').removeClass('linhaSuccess linhaErrorNoColor');
							$('#{$id} .progresso').html('');
							$('#{$id} button:eq(1)').show();
						}else if(request.encerrado){
							$('#{$id} button:eq(1)').hide();
							$('#{$id} button:first').hide();
							$('#{$id} button:eq(2)').show();
							//$('#{$id} button:eq(3)').show().css('opacity', '1');
						}else{
							$('#{$g['divId']}').find('#T276_Id-{$array['todosCampos']['T276_Id']}').removeClass('linhaErrorNoColor linhaInfo').addClass('linhaSuccess');
							$('#{$id} button:eq(0)').hide();
							$('#{$id} button:eq(1)').hide();
						}
					}
				}else {
					$('#{$g['divId']}').find('#T276_Id-{$array['todosCampos']['T276_Id']}').removeClass('linhaSuccess linhaErrorNoColor linhaInfo');
					$('#{$id} .progresso').html(request.codigoTXT);
				}
			}else {
				// Insere uma linha de erro logo abaixo desse tr
				var tr = $('<tr></tr>')
					.attr({
						'id': 'T276_Id-{$array['todosCampos']['T276_Id']}-after',
						'class': $('#T276_Id-{$array['todosCampos']['T276_Id']}').attr('class')
					}).insertAfter($('#{$g['divId']}').find('#T276_Id-{$array['todosCampos']['T276_Id']}'));
					tr.removeClass('linhaSuccess linhaInfo').addClass('linhaError');
				$('<tr style="background-color: white;"><td colspan="100" style="padding: 2px; height: 0;"></td></tr>')
					.attr({
						'id': 'T276_Id-{$array['todosCampos']['T276_Id']}-space1',
					})
					//.insertAfter(tr);
				var td = $('<td></td>')
					.attr({
						'colspan': '100',
					})
					.css({
						'text-align': 'center'
					})
					.html('')
					.appendTo(tr);
				var table = $('<table style="width: 100%;" border=0 cellspacing=0></table>')
					.appendTo(td);
				var div1 = $('<td style="border: 2px solid #EE6363;background:#fff;"></td>')
					.css({
						'width': '49.99%',
						 'max-width': '49.99%',
						 'white-space': 'normal',
   						 'font-size': '14px'
					})
					.appendTo(table);
				if (request.T276_TX_Mensagem == '') {
					var div3 = $('<td></td>')
						.css({
							'width': '0%'
						})
						.html('')
						.appendTo(table);
				} else {
					var div3 = $('<td></td>')
						.css({
							'width': '49.99%'
						})
						.html('<span style="font-size: 14px; font-weight: bold; color: blue;">' + request.T276_TX_Mensagem + '</span><br /><br />Hardness suporte: (41) 3344-2938')
						.appendTo(table);
				}
				var div2 = $('<td style="border: 2px solid #EE6363;background:#fff;"></td>')
					.css({
						'width': '49.99%',
						'font-size':'16px',
						'color':'#333333',
						'padding': '5px', 
						'font-weight':'bold'
					})
					.appendTo(table);
				$('#{$id} .progresso').html('');
				$('#{$g['divId']}').find('#T276_Id-{$array['todosCampos']['T276_Id']}').removeClass('linhaSuccess linhaInfo').addClass('linhaErrorNoColor');

				if (request.T276_TX_Resposta_Codigo == 202) {
					if (request.T276_TX_Retorno_Sefaz_40) {
						div1.html('<span style="color: black; font-weight: bold;font-size:16px;">' + request.codigoTXT + ': ' + request.T276_TX_Retorno_Sefaz_40 + ' ... ' + '</span><br />');
					} else {
						div1.html('<span style="color: black; font-weight: bold;font-size:16px;">' + request.codigoTXT + ': ' + request.T276_TX_Retorno_Sefaz + ' </span><br />');
					}
					
					$('<a>')
						.attr('href', '#')
						.css({
							'font-weight': 'bold',
							'background-color': '#FFF',
							'color': '#EE6363'
						})
						.appendTo(div1);
				} else {
					div1.html('<span style="color: red; font-weight: bold;">' + request.codigoTXT + '</span>');
				}
				$('<span>').html(request.Procedimento).appendTo(div2);
				//$('<span>').html(request.T276_TX_Mensagem).appendTo(div1);
				// Deu erro, botão agora é 'Retransmissão'
				if(request.encerrar){
					$('#{$id} button:eq(1)')
						.show()
						.css({'opacity': '1','padding':'3px', 'font-size':'14px'})
						.attr('refazer', 'N')
						.find('span').text('Tentar Novamente');
				}else{
					$('#{$id} button:first')
						.show()
						.css({'opacity': '1','padding':'3px', 'font-size':'14px'})
						.attr('refazer', 'N')
						.find('span').text('Tentar Novamente');
				}
			}
		});
	}
	{$func}();
} </script>
</div>
EOT;
	$sqlT276 = mysql_query("SELECT T276_Flag_Travar_MDFe
							  FROM T276 
							 WHERE T276_Id = '{$array['todosCampos']['T276_Id']}'");
	$T276 = mysql_fetch_array($sqlT276);

	// Trava: Não será possível emitir o mdf-e se o mdf-e não estiver finalizado
	if($T276['T276_Flag_Travar_MDFe'] == 'N'){
		$return = "<span style='color:red;'>O MDF-e não está finalizado - finalize o MDF-e para transmitir.</span>";
	}	

	// 	Trava: Não será possível tentar transmitir um mdfe novamente antes de dois minutos do último erro
	if($encerrado == 'N'){
		$minutos = 2;
		$T285 = mysql_query("SELECT T285_Data_Transmicao,
									T285_Codigo_Retorno 
			 				   FROM T285 
			 				  WHERE T285_T276_Id='{$array['todosCampos']['T276_Id']}' 
			 				    AND T285_Modelo='58' 
			 			   ORDER BY T285_Id DESC,T285_Data_Transmicao");
		$mT285 = mysql_fetch_array($T285);
		$horaUltimoEnvio = explode(" ", $mT285['T285_Data_Transmicao']);
		$horasSomada = strtotime('+{$minutos} minute',$horaUltimoEnvio[1]);
		if(mysql_num_rows($T285) > 0 && $mT285['T285_Codigo_Retorno'] != '100' && $horaUltimoEnvio[0] == date('Y-m-d')){
			$hora = $horaUltimoEnvio[1];
			$horaNova = strtotime("$hora + 2 minutes"); 
			$horaNovaFormatada = date("H:i:s",$horaNova);
			if($horaNovaFormatada > date('H:i:s')){
				$return = "<span style='color:red;'>Verifique e corrija o erro, tente novamente a partir das <b>{$horaNovaFormatada}</b> <Br /> (É necessário aguardar 2 minutos para nova tentativa após ocorrência de erro)</span>";
				$return .= "<div style='color:red;'><br /><b>Erro Sefaz:</b></br>{$D006['T276_TX_Retorno_Sefaz']}</div>";
			}
		}
	}
	return $return;
}
