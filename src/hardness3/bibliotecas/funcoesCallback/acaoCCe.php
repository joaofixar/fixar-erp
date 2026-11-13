<?php
namespace hardness;

function acaoCCe($array, $param=false){
	global $g;

	if($array['todosCampos']['T199_Flag_Enviado'] == 'S'){
		if($array['todosCampos']['T199_Data_Envio_Email_Cce']!='0000-00-00 00:00:00' && $array['todosCampos']['T199_Data_Envio_Email_Cce']!=''){
			$retorno = "E-mail enviado em: <br/>".$array['todosCampos']['T199_Data_Envio_Email_Cce'];
		} else {
			$retorno = "Enviar E-mail";
		}
		return "<button href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T199_Id=' + encodeURIComponent('{$array['todosCampos']['T199_Id']}'), [900,550]); return false;\">{$retorno}</button>";
	}
	$id = uniqid();
	$onclick = <<<EOT
		var buttons = {
			'Sim': function() {
				$(this).dialog('destroy').remove();
				$('#{$id}').find('span').text('Transmitindo...').attr('disabled','disabled').css('opacity','0.5');
				$.ajax({
					url: '../hardness3/hardness/carta_correcao/enviar_cce.php?T199_Id={$array['todosCampos']['T199_Id']}',
					dataType: 'html',
					data: 'ajax=true&T199_Id=' + encodeURIComponent('{$array['todosCampos']['T199_Id']}'),
					type: 'post',
					success: function(response) {
						if(response.indexOf('Essa Carta de Correção já foi enviada') > 0){
							dialogConfirm('Atenção', 'Esta carta de correção já foi transmitida.');
							divRefresh('{$g['divId']}');
						} else if(response.indexOf('CCe Enviada<script>parent.Recarregar()</script>') > 0){
							dialogConfirm('Sucesso', 'Carta de correção transmitida com sucesso!');
							divRefresh('{$g['divId']}');
						} else {
							dialogConfirm('Erro', response);
							divRefresh('{$g['divId']}');
						}
					}
				});
			},
			'Não': function() { $(this).dialog('destroy').remove(); } 
		};
		dialogConfirm('Transmitir CC-e', 'Você tem certeza? ', buttons);	
EOT;
	$onclick = str_replace("\n", ' ', $onclick);
	return <<<EOT
		<input type='button' id="{$id}" onclick="{$onclick}" value="Transmitir CC-e">
EOT;
}
