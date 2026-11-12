<?php
namespace hardness;
/**
 * Ações
 */
switch($g['r'][3]){
	case 'etc017CabecalhoTarefa':
		$sqlT259 = mysql_query("SELECT * FROM T259 LEFT JOIN D024 ON D024_Id=T259_D024_Id WHERE T259_Id = '{$r_acaoId}'");
		$rowT259 = mysql_fetch_assoc($sqlT259);
		$rowT259['T259_Data_Hora'] = gCorrigeData($rowT259['T259_Data_Hora']);
		// Print
		echo <<<EOT
			<div class="contentCabecalhoLayout" style="cursor: pointer;" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'CRM - Cliente', '/crm/crm001/content/crm001ContentPrincipalCRM/', '&D024_Id=' + encodeURIComponent('{$rowT259['D024_Id']}') + '&tabela=D024', 'auto');">
				<table class="tabela ui-state-default ui-corner-all">
					<tr class="linhaDesc" {$urlCadastroCliente}>
						<td width='5%' align='center'>Código</td>
						<td width='50%'>Cliente</td>
						<td width='15%'>Último contato</td>
					</tr>
					<tr class="linhaVal" {$urlCadastroCliente}>
						<td align='center'>{$rowT259['D024_Id']}</td>
						<td>{$rowT259['D024_Nome_Empresa']}</td>
						<td>{$rowT259['T259_Data_Hora']}</td>
					</tr>
				</table>
			</div>
EOT;

	break;

	case 'etc017LeituraAgenda':
		$sql = mysql_query("SELECT D085_Mensagem FROM D085 WHERE D085_Id = '{$r_acaoId}'");
		$row = mysql_fetch_assoc($sql);
		$textareaId = uniqid();
		echo <<<EOT
			<textarea id="{$textareaId}" disabled="disabled" style="margin: 10px 0 0 10px; width: 100%; height: 100%;">{$row['D085_Mensagem']}</textarea>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				function resizeEditorChamado(){
					setTimeout(function() {
						var widthTab = parseFloat($('#{$g['divId']}').css('width'));
						var heightTab = parseFloat($('#{$g['divId']}').css('height'));
						$('#{$textareaId}').css({'width': widthTab-30, 'height': heightTab-31});
					}, 50);
				}

				$('.ui-dialog:last .ui-icon-newwin').bind("click", function(data, ui) {
					setTimeout(function() {
						resizeEditorChamado();
					}, 50);
				});

				resizeEditorChamado();
		    } </script></div>
EOT;
	break;
}

