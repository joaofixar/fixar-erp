<?php
namespace hardness;
require_once("old/lib/funcoes.php");

/**
* FIS012 - FIS012 
*/
function txSefazCTe($array, $param = false){
	
	global $g;
						
	$return = '';

	$ambienteCTe = '';
	if($array['todosCampos']['T084_Ambiente_CTe'] == 2){
		$ambienteCTe .= "<button type='button' style='display: none; color: black; font-weight: bold; font-size:14px; background: #FFD700;'>";
		$ambienteCTe .= "Enviar CT-e em Homologação sem valor fiscal";
		$ambienteCTe .= "</button>";
	}else{
		$ambienteCTe  = "<button type='button' style='display: none; color: green; font-weight: bold; font-size:14px;'>";
		$ambienteCTe .= "Enviar CT-e";
		$ambienteCTe .= "</button>";
	}
		
	$id = md5(uniqid() . time());
	$func = 'f' . md5(uniqid() . time());
	$return .= <<<EOT
	<div id="{$id}">
	<div style="float: left; vertical-align: top; display: flex;">
		<!-- botão first -->
		{$ambienteCTe} 
		<!-- botão 1 -->
		<button type="button" style="display: none; color: #FA4242; font-weight: bold; font-size:14px;" scan='N' refazer='N'>
			Consultar Recibo
		</button>
		<!-- botão 2 -->				
		<button type="button" style="display: none; opacity: 0.5; color: green; font-weight: bold; font-size:14px;">
			DACTE
		</button>
		<!-- botão 3 -->				
		<button type="button" style="display: none; opacity: 0.5; color: green; font-weight: bold; font-size:14px;">
			Fechar 
		</button>
	</div>
	<div style="float: left; padding: 5px 11px;">
		<span class="progresso"></span>
	</div>
	<div style="float: left;">
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		$('#{$id} button:first').click(function() {			
			if ($(this).css('opacity') == '0.5') {
				return false;
			}
			$(this).css('opacity', '0.5').text('Transmitindo...');
			$('#{$id} .progresso').html('Iniciando processo...');
			$('<img>').attr('src', '/hardness3/static/img/nfe2.gif').css('margin-right', '13px').prependTo($('#{$id} .progresso'));			
			var acao = $(this).attr('recuperar') == 'S' ? 'recuperaDuplicidadeCTe' : 'enviaCTe';
			if (acao == 'recuperaDuplicidadeCTe') {
				$(this).text('Consultando autorização...');
			}
			$.getJSON('/est/est020/grid_func-ajax/' + acao + '/?ajax=true&T084_Id=' + encodeURIComponent('{$array['todosCampos']['T084_Id']}') + '&callback=?', function(request) {
				setTimeout($func, 000);
			});			
		});
		$('#{$id} button:eq(1)').click(function() {<!--Consultar Recibo-->
			$('#{$id} button:eq(1)').hide();
			if ($(this).css('opacity') == '0.5') {
				return false;
			}
			$(this).css('opacity', '0.5').find('span').text('Consultando CT-e...');
			$('#{$id} .progresso').html('Iniciando processo...');
			$('<img>').attr('src', '/hardness3/static/img/nfe2.gif').css('margin-right', '13px').prependTo($('#{$id} .progresso'));
			$.getJSON('/est/est020/grid_func-ajax/consultaReciboCTe/?ajax=true&T084_Id=' + encodeURIComponent('{$array['todosCampos']['T084_Id']}') + '&callback=?', function(request) {
				setTimeout('{$func}()', 1000);
			});
		});		
		$('#{$id} button:eq(2)').click(function() {
			if ($(this).css('opacity') == '0.5') {
				return false;
			}			
			// Exemplo: abrirJanela
			$('#{$g['divId']}').showLoading();
			$.ajax({
				url: '/sistema/personalizacaoLowCode/grid_func-ajax/telaGerarPdf/',
				type: 'GET',
				dataType: 'jsonp',
				data: {
					'ajax': 'true',
					'acaoId': '9da6468cc29f3b4f47d95d743fc68e8e',
					'link': '/sistema/personalizacao/tela/991025d354f9429b1f56e30b19ea5619/?ajax=true&acaoId=' + encodeURIComponent('{$array['todosCampos']['T084_Id']}'),
				},
				success: function(resposta) {
					$('#{$g['divId']}').hideLoading();
					window.open(resposta.data, '_blank');
				}
			});
		});
		$('#{$id} button:eq(3)').click(function() {
			if ($(this).css('opacity') == '0.5') {
				return false;
			}
			$(this).closest('tr').remove();
			$.getJSON('/est/est020/grid_func-ajax/fechar/?ajax=true&T084_Id=' + encodeURIComponent('{$array['todosCampos']['T084_Id']}') +  '&callback=?', function(request) {
				//
			});
		});
		function {$func}() {
			$.getJSON('/est/est020/grid_func-ajax/consultaStatusCTe/?ajax=true&T084_Id=' + encodeURIComponent('{$array['todosCampos']['T084_Id']}') + '&callback=?', 
				function(request) {
					$('#T084_Id-{$array['todosCampos']['T084_Id']}-after').remove();
					$('#T084_Id-{$array['todosCampos']['T084_Id']}-space1').remove();									
					
					if(request.T084_TX_Resposta_Codigo_Interno < 100) {						
						if(request.T084_TX_Resposta_Codigo_Interno != 0) {
							if(request.T084_TX_Resposta_Codigo_Interno != 7 && request.T084_TX_Resposta_Codigo_Interno != 8) {
								$('#{$g['divId']}').find('#T084_Id-{$array['todosCampos']['T084_Id']}').removeClass('linhaSuccess linhaErrorNoColor').addClass('linhaInfo');
								$('#{$id} .progresso').html(request.codigoTXT);
								$('<img>').attr('src', '/hardness3/static/img/nfe2.gif').css('margin-right', '13px').prependTo($('#{$id} .progresso'));
								setTimeout('{$func}()', 1000);
							} else if(request.T084_TX_Resposta_Codigo_Interno == 7) {
								$('#{$g['divId']}').find('#T084_Id-{$array['todosCampos']['T084_Id']}').removeClass('linhaSuccess linhaErrorNoColor');
								$('#{$id} button:first').hide();	
								$('#{$id} .progresso').html('');							
								$('#{$id} button:eq(1)')
									.show()
									.attr("disabled", true);
								setTimeout(() => $('#{$id} button:eq(1)').attr("disabled", false),3000);									
							} else {
								$('#{$id} .progresso').html('');	
								$('#{$id} button:first').hide();								
								$('#{$id} button:eq(1)').hide();								
								$('#{$id} button:eq(2)').show().css('opacity', '1');								
								$('#{$id} button:eq(3)').show().css('opacity', '1');
							}
						} else {
							$('#{$g['divId']}').find('#T084_Id-{$array['todosCampos']['T084_Id']}').removeClass('linhaErrorNoColor linhaSuccess linhaInfo');							
							$('#{$id} button:first').show();
						}
					} else {
						// Insere uma linha de erro logo abaixo desse tr
						var tr = $('<tr></tr>')
							.attr({
								'id': 'T084_Id-{$array['todosCampos']['T084_Id']}-after',
								'class': $('#T084_Id-{$array['todosCampos']['T084_Id']}').attr('class')
							})
							.insertAfter($('#{$g['divId']}').find('#T084_Id-{$array['todosCampos']['T084_Id']}'));
						tr.removeClass('linhaSuccess linhaInfo').addClass('linhaError');
						$('<tr style="background-color: white;"><td colspan="100" style="padding: 2px; height: 0;"></td></tr>')
							.attr({
								'id': 'T084_Id-{$array['todosCampos']['T084_Id']}-space1',
							})							
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
											
						$('#{$id} .progresso').html('');
						$('#{$g['divId']}').find('#T084_Id-{$array['todosCampos']['T084_Id']}').removeClass('linhaSuccess linhaInfo').addClass('linhaErrorNoColor');
						if (request.T084_TX_Resposta_Codigo_Interno == 202) {							
							div1.html('<span style="color: black;font-size:16px;">' + request.codigoTXT + ': ' + request.T084_TX_Retorno_Codigo + ' - ' + request.T084_TX_Retorno_Motivo + ' </span><br />');
														
							$('<a>')
								.attr('href', '#')
								.css({
									'background-color': '#FFF',
									'color': '#EE6363'
								})
								.appendTo(div1);
						} else {
							div1.html('<span style="color: red;">' + request.codigoTXT + '</span>');
						}
					
						// Em 204 a SEFAZ já autorizou a numeração. O botão consulta a
						// chave autenticada; não retransmite o documento fiscal.
						$('#{$id} button:first')							
							.show()
							.css({'opacity': '1','padding':'3px', 'font-size':'14px'})
							.attr('refazer', 'N')
							.attr('recuperar', request.T084_TX_Retorno_Codigo == 204 ? 'S' : 'N')
							.text(request.T084_TX_Retorno_Codigo == 204 ? 'Recuperar autorização' : 'Tentar Novamente');
																							
					}
			});
		}
		{$func}();
} </script>
</div>
EOT;

	$sqlT084 = mysql_query("SELECT T084_Flag_Travar,T084_TX_Retorno_Codigo,T084_TX_Retorno_Motivo,T084_TX_Resposta_Codigo_Interno,T084_TX_Timestamp_Envio_Sefaz
							  FROM T084
							 WHERE T084_Id = '{$array['todosCampos']['T084_Id']}' 
							 	AND T084_C004_Id='{$g['empresaAtual']}'");
	$mT084 = mysql_fetch_array($sqlT084);

	// Trava: Não será possível emitir o CT-e, se não estiver finalizado
	if($mT084['T084_Flag_Travar'] == 'N'){
		$return = "<span style='color:red;'>O CT-e não está finalizado - finalize o CT-e para transmitir.</span>";
	}	

	// 	Trava: Não será possível tentar transmitir um mdfe novamente antes de dois minutos do último erro				
	// $horaUltimoEnvio = explode(" ", $mT084['T084_TX_Timestamp_Envio_Sefaz']);	
	// log("HORA ULTIMO ENVIO");
	// log($horaUltimoEnvio);
	// log("DATA ATUAL SISTEMA: " . date('Y-m-d\TH:i:sP'));

	// if(mysql_num_rows($sqlT084) > 0 && $mT084['T084_TX_Resposta_Codigo_Interno'] == '202' && $horaUltimoEnvio[0] == date('Y-m-d')){		
	// 	$hora = $horaUltimoEnvio[1];
	// 	$horaNova = strtotime("$hora + 2 minutes"); 
	// 	$horaNovaFormatada = date("H:i:s",$horaNova);
	// 	if($horaNovaFormatada > date('H:i:s')){
	// 		$return = "<span style='color:red;'>Verifique e corrija o erro, tente novamente a partir das {$horaNovaFormatada} <Br /> (É necessário aguardar 2 minutos para nova tentativa após ocorrência de erro)</span>";
	// 		$return .= "<div style='color:red;'><br />Erro SEFAZ {$mT084['T084_TX_Retorno_Codigo']}:</br>{$mT084['T084_TX_Retorno_Motivo']}</div>";
	// 	}
	// }	

	return $return;
}
