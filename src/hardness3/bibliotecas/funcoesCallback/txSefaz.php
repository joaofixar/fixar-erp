<?php
namespace hardness;

require_once('bibliotecas/classes/NFeRejeicao225.php');

/**
* VEN - VEN001 - Cadastro de Bens
*/
function txSefaz($array, $param = false){
	global $g, $confUsuario;

	$imprimirAutomatico = ($param['imprimirAutomatico'] == true) ? 'S' : 'N';
	// Liberação de Nota
	$sql = "SELECT T007_Flag_Analise_Credito FROM T007 WHERE T007_Id = '{$array['todosCampos']['T007_Id']}'";
	$resT007 = mysql_query($sql);
	$rowT007 = mysql_fetch_assoc($resT007);
	if ($rowT007['T007_Flag_Analise_Credito'] == 1) {
		return '<span style="color: var(--BGAzul);">Aguardando liberação</span>';
	} else if ($rowT007['T007_Flag_Analise_Credito'] == 3) {
		return '<span style="color: var(--BGVermelho);">Liberação negada</span>';
	} else if ($rowT007['T007_Flag_Analise_Credito'] == 2) {
		//return '<span style="color: var(--letra-tab-hover);">Liberado</span>';
	}

	// Colocado este código abaixo direto no grid para executar apenas 1 vez.

	/*
	$sqlNotas = "SELECT T007_Numero_Nota_Fiscal, T007_Serie FROM T007
				WHERE T007_Flag_ACP = '{$array['todosCampos']['T007_Flag_ACP']}' AND T007_C004_Id = '{$g['empresaAtual']}' AND T007_Serie < 900
				ORDER BY T007_Serie DESC,floor(T007_Numero_Nota_Fiscal) DESC LIMIT 20";
	$query = mysql_query($sqlNotas);
	$notaAnterior = 0;
	while($row = mysql_fetch_array($query)){
         //echo $notaAnterior .' e '. $row['T007_Numero_Nota_Fiscal'] . '<BR />';
         if ($notaAnterior>0){
        	$diferenca = $notaAnterior - $row['T007_Numero_Nota_Fiscal']; 
        	//echo $diferenca . '<br/>';
        	if ($diferenca == 2){
        		$mensagemErro .= "Falta a nota " . ($notaAnterior-1) . '<br/>';
        	} else if ($diferenca > 2){
        		$mensagemErro .= "Faltam as notas " . ($row['T007_Numero_Nota_Fiscal'] + 1) . " a " . ($notaAnterior - 1) . '<br/>';
        	}
         }
         $notaAnterior = $row['T007_Numero_Nota_Fiscal'];
	}

	if (!empty($mensagemErro)){
		return "<font style='color: var(--BGVermelho);'>ATENÇÃO!!<br/>" . $mensagemErro . " Favor inutilizar as numerações que estão faltando!!</font>";
	} 
	*/
	/**
	* Esté código é duplicado (com algumas alterações no sistema antigo!
	* Alterou aqui, alterar lá também
	*/
	// Caso incomum: protocolo foi limpo na mão, será enviada novamente
	if (empty($array['todosCampos']['T007_Numero_Protocolo_Nfe']) && $array['todosCampos']['T007_TX_Resposta_Codigo'] == 9) {
		$sql = <<<EOT
			UPDATE 
				T007 
			SET 
				T007_TX_Resposta_Codigo = 0
				, T007_TX_Resposta_Extra = ''
				, T007_TX_Retorno_Sefaz = ''
				, T007_TX_Flag_Finalizada = 0
				, T007_TX_Flag_Finalizada = '0000-00-00 00:00:00'
			WHERE 
				T007_Id = '{$array['todosCampos']['T007_Id']}';
EOT;
		mysql_query($sql);
		$array['todosCampos']['T007_TX_Resposta_Codigo'] = 0;
		$array['todosCampos']['T007_TX_Resposta_Extra'] = '';
		$array['todosCampos']['T007_TX_Retorno_Sefaz'] = '';
		$array['todosCampos']['T007_TX_Flag_Finalizada'] = 0;
		$array['todosCampos']['T007_Data_Envio_XML'] = '0000-00-00 00:00:00';
	}
  
  	// Caso incomum: NFCe está com o T007_Flag_Travar_Nota = 'N' mas está autorizada com protocolo, já está no grid ven012GridFilaDeEnviadas
	if (!empty($array['todosCampos']['T007_Numero_Protocolo_Nfe']) && $array['todosCampos']['T007_Flag_Travar_Nota'] == 'N') {
		$sql = <<<EOT
			UPDATE T007 
			   SET T007_Flag_Travar_Nota = 'S'
			 WHERE T007_Id = '{$array['todosCampos']['T007_Id']}';
EOT;
		mysqli_query($sql);
	}
	
	$Query = urlencode(md5($array['todosCampos']['T007_D024_Id'].'|'.$array['todosCampos']['T007_Id']));
	
	$obrigatorioEnvioDanfeViaEmail = ($g['C031']['obrigatorioEnvioDanfeViaEmail'] == 'S') ? '1' : '0';
	$opacityBotaoFechar = "opacity: 0.5";
	if($array['todosCampos']['T007_Flag_ACP'] == 8 && ($array['todosCampos']['T007_D024_Id'] == $g['C031']['codigoClienteCupomFiscal'])){
		// se for NFCe e o cliente for o mesmo definido na global não é obrigatorio enviar email
		$obrigatorioEnvioDanfeViaEmail = '0';
		$opacityBotaoFechar = "opacity: 1";
	}
	$valorNaDanfe = $g['C031']['exibirValorNFDANFE'];
	$emailEnviado = '0';
	$emailEnviadoTxt = 'E-mail DANFE';
	$emailSpace = '';
	if($array['todosCampos']['T007_Data_Envio_XML'] != '0000-00-00 00:00:00' && $array['todosCampos']['T007_Data_Envio_XML']!='') {
		$emailEnviado = '1';
		$array['todosCampos']['T007_Data_Envio_XML'] = gCorrigeData($array['todosCampos']['T007_Data_Envio_XML']);
		$emailEnviadoTxt = "E-mail enviado em:<br />{$array['todosCampos']['T007_Data_Envio_XML']}";
		$emailSpace = '<br />&nbsp;';
	}

	$T117=mysql_query("SELECT T117_Id FROM T117 
						left join T002 on T002_T117_Id=T117_Id
						where T002_T007_Id = '{$array['todosCampos']['T007_Id']}'
						GROUP BY T117_Id");
	$rT117 = mysql_num_rows($T117);

	$boletosEnviadosTxt = 'E-mail Boletos';
	if($array['todosCampos']['T007_Data_Envio_Email_Boleto'] != '0000-00-00 00:00:00' && $array['todosCampos']['T007_Data_Envio_Email_Boleto']!='') {
		//$emailEnviado = '1';
		$array['todosCampos']['T007_Data_Envio_Email_Boleto'] = gCorrigeData($array['todosCampos']['T007_Data_Envio_Email_Boleto']);
		if($rT117 == 1){
			$boletosEnviadosTxt = "Boleto enviado em:<br />{$array['todosCampos']['T007_Data_Envio_Email_Boleto']}";	
		} else if($rT117>1){
			$boletosEnviadosTxt = "Boletos enviados em:<br />{$array['todosCampos']['T007_Data_Envio_Email_Boleto']}";
		}
		$emailSpace = '<br />&nbsp;';
	}

	if($array['todosCampos']['T007_Flag_ACP']==8){
		$textoBotaoImp   = 'DANFCe';
		if($array['todosCampos']['T007A_Flag_Contingencia_Offline'] == 'S'){
			$textoBotaoEnvio = 'Enviar NFC-e <small>OFFLINE</small>';
		} else {
			$textoBotaoEnvio = 'Enviar NFC-e';
		}
		$textoBotaoEnvioSVC = "Enviar OFFLINE{$emailSpace}<small> - contingência</small>";;
		
	}else{
		$textoBotaoImp   = 'DANFe';
		$textoBotaoEnvio = 'Enviar NF-e';
		$textoBotaoEnvioSVC = "Enviar por SVC{$emailSpace}<small> - contingência</small>";
	}

	$fechaJanelaPDV = 'S';
	if(!empty($array['todosCampos']['naoFechaJanelaPDV'])){
		$fechaJanelaPDV = 'N';
	}

/* 	// Verifica se será habilitado a emissão em contingência ou não
	$Q007 = mysql_query("SELECT Q007_Valor FROM global.Q007 WHERE Q007_Campo='habilitarSVC'");
	$mQ007 = mysql_fetch_array($Q007);

	if(empty($mQ007['Q007_Valor']) || $mQ007['Q007_Valor'] != date('d/m/Y')){
		$hideSVC = 'hide()';
	}else{
		$hideSVC = 'show()';
	} */

	// Verifica se será habilitado a emissão em contingência ou não
	$C020 = mysql_query("SELECT C020_NFe_SVC_Ativado, C020_NFCe_SVC_Ativado FROM C020 WHERE C020_C004_Id='{$g['empresaAtual']}'");
	$mC020 = mysql_fetch_array($C020);

	if($array['todosCampos']['T007_Flag_ACP'] == 6){
		if($mC020['C020_NFe_SVC_Ativado'] == 'N'){
			$envioSVC = 'N';
			$hideSVC = 'hide()';
			$hideEnvioNormal = 'show()';
		}else{
			$envioSVC = 'S';
			$hideSVC = 'show()';
			$hideEnvioNormal = 'hide()';
		}

	} else {
		if($mC020['C020_NFCe_SVC_Ativado'] == 'N'){
			$envioSVC = 'N';
			$hideSVC = 'hide()';
			$hideEnvioNormal = 'show()';
		}else{
			$envioSVC = 'S';
			$hideSVC = 'show()';
			$hideEnvioNormal = 'hide()';
		}
	}

	$sqlDANFe  = " SELECT D175.D175_Nome ";
	$sqlDANFe .= "   FROM D175 ";
	$sqlDANFe .= "  WHERE D175.D175_Tipo_Impressora = 7 ";
	$sqlDANFe .= "  ORDER BY D175.D175_Id ASC ";
	$sqlDANFe .= "  LIMIT 1 ";
	$fetchDANFe = mysql_query($sqlDANFe);
	$dadosDANFe = mysql_fetch_array($fetchDANFe);

	$NFe = "/hardness3/hardness/danfe/imprimir_danfe.php?Query={$Query}&vlrNF={$valorNaDanfe}&T007_Id={$array['todosCampos']['T007_Id']}";
	$impressaoDANFe = "window.open('{$NFe}','DANFE {$array['todosCampos']['T007_Id']}','toolbar=yes,menubar=yes,location=no,scrollbars=yes,status=yes,resizable=yes,width=990,height=542,top=0,left=0');";

	if (!empty($dadosDANFe['D175_Nome'])) {
		$impressaoDANFe = "printSMT('{$NFe}', '{$dadosDANFe['D175_Nome']}');";
	}

	$sqlDANFCe  = " SELECT D175.D175_Nome ";
	$sqlDANFCe .= "   FROM D175 ";
	$sqlDANFCe .= "  WHERE D175.D175_Tipo_Impressora = 8 ";
	$sqlDANFCe .= "  ORDER BY D175.D175_Id ASC ";
	$sqlDANFCe .= "  LIMIT 1 ";
	$fetchDANFCe = mysql_query($sqlDANFCe);
	$dadosDANFCe = mysql_fetch_array($fetchDANFCe);

	$impressaoDANFCe = <<<EOT
	var buttons = {
		"Impressão": function() {
			window.open('{$NFe}&impressaoHtml=true','DANFE{$Query}','toolbar=yes,menubar=yes,location=no,scrollbars=no,toolbar=no,status=yes,resizable=yes,width=750,height=642,top=0,left=0');
			$(this).dialog("destroy").remove();
		},
		"PDF": function() {
			window.open('{$NFe}&impressaoHtml=false','DANFE{$Query}','toolbar=yes,menubar=yes,location=no,scrollbars=yes,status=yes,resizable=yes,width=990,height=542,top=0,left=0');
			$(this).dialog("destroy").remove();
		},
		"Cancelar": function() { 
			$(this).dialog("destroy").remove();
		}
	}

	const titulo = '{$mC020['C020_NFe_SVC_Ativado']}' == 'S' ? 'DANFCe Contingência' : 'DANFCe';

	var dialog = dialogConfirm(titulo, 'Selecione a maneira que deseja abrir o DANFCe', buttons);
EOT;

	$impressoraDANFCe = "";
	if (!empty($dadosDANFCe['D175_Nome'])) {
		$impressoraDANFCe = $dadosDANFCe['D175_Nome'];
		$impressaoDANFCe  = "printSMT('{$NFe}&impressaoHtml=false&impressaoDANFCe=true', '{$impressoraDANFCe}');";
	}
	
	$return = '';
	//if ($array['todosCampos']['T007_TX_Resposta_Codigo'] == 0) {
		$id = md5(uniqid() . time());
		$func = 'f' . md5(uniqid() . time());
		$return .= <<<EOT
<div id="{$id}">
	<div style="float: left; vertical-align: top; display: flex;">
		<button type="button" style="display: none; color: var(--letra-tab-hover); font-size:14px; height: 40px;" scan='N' refazer='N' class="btnEnviarNFE">
			{$textoBotaoEnvio}{$emailSpace}
		</button>
		<button type="button" style="display: none; color: #FA4242; font-size:14px; height: 40px;" scan='S' refazer='N' class="btnEnviarNFeSVC">
			{$textoBotaoEnvioSVC}
		</button>
		<button type="button" style="display: none; font-size:14px; height: 40px;">
			{$textoBotaoImp}
		</button>
		<button type="button" style="display: none; font-size:14px; height: 40px;">
			{$emailEnviadoTxt}
		</button>
		<button type="button" style="display: none; font-size:14px; height: 40px;">
			Boletos
		</button>
		<button type="button" style="display: none; font-size:14px; height: 40px;">
			{$boletosEnviadosTxt}
		</button>
		<button type="button" style="display: none; {$opacityBotaoFechar}; color: var(--letra-tab-hover); font-size:14px; height: 40px;">
			Fechar
		</button>
		<button type="button" style="display: none; color: #FA4242; font-weight: bold; font-size:14px;height: 40px;" numeroLote="{$array['todosCampos']['T007_Numero_Lote']}" func="{$func}">
			Consultar Recibo
		</button>
	</div>
	<div style="float: left; padding: 5px 11px;">
		<span class="progresso"></span>
	</div>
	<div style="float: left;">
<div>
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	$('#{$id} button:first').click(function() {
		$('#{$id} button:eq(1)').hide();
		if ($(this).css('opacity') == '0.5') {
			return false;
		}
		$(this).css('opacity', '0.5').find('span').text('Transmitindo...');
		$('#{$id} .progresso').html('Iniciando processo...');
		$('<img>').attr('src', '/hardness3/static/img/nfe2.gif').css('margin-right', '13px').prependTo($('#{$id} .progresso'));
		$.getJSON('/ven/ven012/grid_func-ajax/txSefaz/?ajax=true&tx=true&T007_Id=' + encodeURIComponent('{$array['todosCampos']['T007_Id']}') + '&refazer=' + encodeURIComponent($(this).attr('refazer')) + '&scan=' + encodeURIComponent($(this).attr('scan')) + '&callback=?', function(request) {
		setTimeout('{$func}()', 1000);
		});
	});
	$('#{$id} button:eq(1)').click(function() {
		var btn = $(this);
		if (btn.css('opacity') == '0.5') {
			return false;
		}
		var buttons = {
			"Continuar": function() {
				$('#{$id} button:eq(0)').hide();
				btn.css('opacity', '0.5').find('span').text('Transmitindo por SVC...');
				$('#{$id} .progresso').html('Iniciando processo...');
				$('<img>').attr('src', '/hardness3/static/img/nfe2.gif').css('margin-right', '13px').prependTo($('#{$id} .progresso'));
				$.getJSON('/ven/ven012/grid_func-ajax/txSefaz/?ajax=true&tx=true&T007_Id=' + encodeURIComponent('{$array['todosCampos']['T007_Id']}') + '&refazer=' + encodeURIComponent(btn.attr('refazer')) + '&scan=' + encodeURIComponent(btn.attr('scan')) + '&callback=?', function(request) {
					setTimeout('{$func}()', 1000);
				});
				$(this).dialog("destroy").remove(); 
			},
			"Cancelar": function() { $(this).dialog("destroy").remove(); }
		};
    	dialogConfirm("Envio via SVC", '<span style="color: var(--BGVermelho); font-size: 1.7em;">ATENÇÃO! ENVIO POR SERVIÇO DE CONTINGÊNCIA</span><br /><br /><span style="color: var(--BGVermelho); font-size: 14px;">O envio através do SVC (Sefaz Virtual Contingência) só deve ser usado caso o serviço da Sefaz do seu estado esteja indisponível.</span><br /><br />Este tipo de transmissão ocorre em âmbito nacional, em detrimento ao envio normal, que ocorre à nível estadual.<br /><br /><strong>O envio através do SVC possui plena legitimidade<br />e é aceito normalmente por todos os orgãos e empresas.</strong></span>', buttons);
	});
	$('#{$id} button:eq(2)').click(function() {
		if('{$array['todosCampos']['T007_Flag_ACP']}' == '8'){
			{$impressaoDANFCe}
		} else {
			{$impressaoDANFe}
		}

		if ('{$obrigatorioEnvioDanfeViaEmail}' == '0' || ('{$obrigatorioEnvioDanfeViaEmail}' == '1' && '{$emailEnviado}' == '1')) {
			$('#{$id} button:eq(6)').show().css('opacity', '1');
		}

		if('{$array['todosCampos']['T007_Flag_ACP']}' == '8' && '{$array['todosCampos']['T007_Numero_Protocolo_Nfe']}' === '') {
			$('#{$id} button:eq(6)').hide();
			return;
		}
		
		$.getJSON('/ven/ven012/grid_func-ajax/fechar/?ajax=true&T007_Id=' + encodeURIComponent('{$array['todosCampos']['T007_Id']}') + '&T007_TX_Flag_Finalizada=2' + '&callback=?', function(request) {});
	});
	$('#{$id} button:eq(3)').click(function() {
		if ('{$obrigatorioEnvioDanfeViaEmail}' == '0' || ('{$obrigatorioEnvioDanfeViaEmail}' == '1' && '{$emailEnviado}' == '1')) {
			$('#{$id} button:eq(6)').show().css('opacity', '1');
		}
		abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T007_Id=' + encodeURIComponent("{$array['todosCampos']['T007_Id']}"), [900,550]);
		$.getJSON('/ven/ven012/grid_func-ajax/fechar/?ajax=true&T007_Id=' + encodeURIComponent('{$array['todosCampos']['T007_Id']}') + '&T007_TX_Flag_Finalizada=2' + '&callback=?', function(request) {
			//
		});
	});
	$('#{$id} button:eq(4)').click(function() {
		if ('{$obrigatorioEnvioDanfeViaEmail}' == '0' || ('{$obrigatorioEnvioDanfeViaEmail}' == '1' && '{$emailEnviado}' == '1')) {
			$('#{$id} button:eq(4)').show().css('opacity', '1');
		}
		$.getJSON('/ven/ven003/grid_func-ajax/gerarPDFboleto/?ajax=true&T007_Id='+encodeURIComponent('{$array['todosCampos']['T007_Id']}')+'&callback=?', function(request) {    
			if(request.code){
				$('#{$g['divId']}').hideLoading();
				var w = window.open('{$confUsuario['urlRaiz']}' + request.data,'_blank');
				w.addEventListener("load", function(event) {
					$.getJSON('/ven/ven003/grid_func-ajax/ven003DeletarPDFBoleto/?ajax=true&T007_Id=' + encodeURIComponent('{$array['todosCampos']['T007_Id']}') + '&callback=?', function(request) {});
				});
			} else {
				dialogConfirm('Operação cancelada', request.data);
			}
		});
	});
	$('#{$id} button:eq(5)').click(function() {
		if ('{$obrigatorioEnvioDanfeViaEmail}' == '0' || ('{$obrigatorioEnvioDanfeViaEmail}' == '1' && '{$emailEnviado}' == '1')) {
			$('#{$id} button:eq(5)').show().css('opacity', '1');
		}
		abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&idNF=' + encodeURIComponent("{$array['todosCampos']['T007_Id']}"), [900,550]);
		$.getJSON('/ven/ven012/grid_func-ajax/fechar/?ajax=true&T007_Id=' + encodeURIComponent('{$array['todosCampos']['T007_Id']}') + '&T007_TX_Flag_Finalizada=2' + '&callback=?', function(request) {
			//
		});
	});
	$('#{$id} button:eq(6)').click(function() {
		if ($(this).css('opacity') == '0.5') {
			return false;
		}
		$(this).closest('tr').remove();
		$.getJSON('/ven/ven012/grid_func-ajax/fechar/?ajax=true&T007_Id=' + encodeURIComponent('{$array['todosCampos']['T007_Id']}') + '&callback=?', function(request) {
			//
		});
	});
	$('#{$id} button:eq(7)').click(function() {
		$('#{$id} button:eq(1)').hide();
		if ($(this).css('opacity') == '0.5') {
			return false;
		}				
		$('#{$id} .progresso').html('Iniciando consulta...');
		$('<img>').attr('src', '/hardness3/static/img/nfe2.gif').css('margin-right', '13px').prependTo($('#{$id} .progresso'));
		$.getJSON('/ven/ven012/grid_func-ajax/ven012consultaReciboNfe/?ajax=true&T007_Id=' + encodeURIComponent('{$array['todosCampos']['T007_Id']}') + '&callback=?', function(request) {
			$('button[numeroLote="{$array['todosCampos']['T007_Numero_Lote']}"]').each(function() {
				var func =  $(this).attr('func');								
				func = window[func];			
				if (typeof func === "function") {					
					setTimeout(func, 1000);												
				}
			});
		});		
	});
	function {$func}() {
		$.getJSON('/ven/ven012/grid_func-ajax/txSefaz/?ajax=true&T007_Id=' + encodeURIComponent('{$array['todosCampos']['T007_Id']}') + '&callback=?', function(request) {			
			$('#T007_Id-{$array['todosCampos']['T007_Id']}-after').remove();
			$('#T007_Id-{$array['todosCampos']['T007_Id']}-space1').remove();
			if (!request.exibir) {
				$('#{$id} button:eq(0)').hide();
				$('#{$id} button:eq(1)').hide();
				return false;
			}
			//$('#{$id} button:eq(1)').{$hideSVC};
			if (request.T007_TX_Resposta_Codigo < 100) {
				if (request.T007_TX_Resposta_Codigo != 0) {
					if(request.T007_TX_Resposta_Codigo == 6 && request.Lote) {
						$('#{$g['divId']}').find('#T007_Id-{$array['todosCampos']['T007_Id']}').removeClass('linhaSuccess linhaErrorNoColor');
						$('#{$id} button:first').hide();
						$('#{$id} .progresso').html('');
						$('#{$id} button:eq(7)')
							.show()
							.attr("disabled", true);
						setTimeout(() => $('#{$id} button:eq(7)').attr("disabled", false),3000);	
					}  else if (request.T007_TX_Resposta_Codigo == 15) {

						$('#{$id} .progresso').html('');
						$('#T007_Id-{$array['todosCampos']['T007_Id']}-contingencia-mensagem').remove();
						$('#{$g['divId']}').find('#T007_Id-{$array['todosCampos']['T007_Id']}').removeClass('linhaInfo');

						if('{$envioSVC}' == 'S') {
							$('#{$id} button:eq(0)').hide();
							$('#{$id} button:eq(1)').hide();
							$('#{$id} button:eq(2)').show();
							$('#{$id} > div:nth-child(1)').append('<div id="T007_Id-{$array['todosCampos']['T007_Id']}-contingencia-mensagem" style="display: flex; align-items: center; margin-left: 10px;"> <span>PENDENTE DE AUTORIZAÇÃO</span> </div>');
						} else {
							$('#{$id} button:eq(0)').show();
							$('#{$id} button:eq(2)').hide();
						}												
					} else  if (request.T007_TX_Resposta_Codigo != 9) {
						$('#{$g['divId']}').find('#T007_Id-{$array['todosCampos']['T007_Id']}').removeClass('linhaSuccess linhaErrorNoColor').addClass('linhaInfo');
						$('#{$id} .progresso').html(request.codigoTXT);
						$('<img>').attr('src', '/hardness3/static/img/nfe2.gif').css('margin-right', '13px').prependTo($('#{$id} .progresso'));
						setTimeout('{$func}()', 1000);
					} else {
						$('#{$g['divId']}').find('#T007_Id-{$array['todosCampos']['T007_Id']}').removeClass('linhaErrorNoColor linhaInfo').addClass('linhaSuccess');
						if (request.denegada == '1') {
							var cor = 'red';
						} else {
							var cor = 'var(--letra-tab-hover)';
						}
						$('#{$id} .progresso').html('<span style="color: ' + cor + ';">' + request.codigoTXT + '</span>');
						// TODO: botão gerar DANFE / Enviar via E-mail
						$('#{$id} button:eq(0)').hide();
						$('#{$id} button:eq(1)').hide();
						$('#{$id} button:eq(2)').show();
						$('#{$id} button:eq(3)').html('<span class="ui-button-text">' + request.emailEnviadoTxt + '</span>').show();
						if ('{$obrigatorioEnvioDanfeViaEmail}' == '0' || ('{$obrigatorioEnvioDanfeViaEmail}' == '1' && request.emailEnviado == '1')) {
							$('#{$id} button:eq(6)').show();
							$('#{$id} button:eq(1)').hide();
						}
						if('{$rT117}' > '0'){
							$('#{$id} button:eq(4)').show().css('opacity', '1');
							$('#{$id} button:eq(5)').show().css('opacity', '1');
						}
						$('#{$id} button:eq(7)').hide();

						//Se PDV imprime automaticamente na tela de transmissão de dentro da nota
						if('{$imprimirAutomatico}' == 'S'){
							fecharJanela('{$g['divId']}');
							if('{$fechaJanelaPDV}' == 'S'){
								fecharJanela('{$g['divIdParent']}');
							}

							if('{$impressoraDANFCe}' != ''){
								{$impressaoDANFCe}
							} else {
								window.open('/hardness3/hardness/danfe/imprimir_danfe.php?Query={$Query}&vlrNF={$valorNaDanfe}&T007_Id={$T007_Id}&impressaoHtml=true','DANFE{$Query}','toolbar=yes,menubar=yes,location=no,scrollbars=no,toolbar=no,status=yes,resizable=yes,width=750,height=642,top=0,left=0');
							}
						}

						// Se o e-mail foi enviado (envio automatico)
						if (request.emailEnviado == '1') {
							$.getJSON('/ven/ven012/grid_func-ajax/fechar/?ajax=true&T007_Id=' + encodeURIComponent('{$array['todosCampos']['T007_Id']}') + '&T007_TX_Flag_Finalizada=2' + '&callback=?', function(request) {
								//
							});
							$('#{$id} button:eq(6)').show().css('opacity', '1');
							$('#{$id} button:eq(1)').hide();
						}
					}
				} else {
					$('#{$g['divId']}').find('#T007_Id-{$array['todosCampos']['T007_Id']}').removeClass('linhaErrorNoColor linhaSuccess linhaInfo');					
						$('#{$id} button:eq(0)').{$hideEnvioNormal};
						$('#{$id} button:eq(1)').{$hideSVC};
				}
			} else {
				// Insere uma linha de erro logo abaixo desse tr
				var tr = $('<tr></tr>')
					.attr({
						'id': 'T007_Id-{$array['todosCampos']['T007_Id']}-after',
						'class': $('#T007_Id-{$array['todosCampos']['T007_Id']}').attr('class')
					})
					.insertAfter($('#{$g['divId']}').find('#T007_Id-{$array['todosCampos']['T007_Id']}'));
				tr.removeClass('linhaSuccess linhaInfo').addClass('linhaError');
				$('<tr style="background-color: white;"><td colspan="100" style="padding: 2px; height: 0;"></td></tr>')
					.attr({
						'id': 'T007_Id-{$array['todosCampos']['T007_Id']}-space1',
					})
					//.insertAfter(tr);
				var td = $('<td></td>')
					.attr({
						'colspan': tr.prev().find('> td').length,
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
				if (request.T007_TX_Mensagem == '') {
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
						.html('<span style="font-size: 14px; color: var(--BGAzul);">' + request.T007_TX_Mensagem + '</span><br /><br />Hardness suporte: (41) 3344-2938')
						.appendTo(table);
				}
				var div2 = $('<td style="border: 2px solid #EE6363;background:#fff;"></td>')
					.css({
						'width': '49.99%',
						'font-size':'16px',
						'color':'#333333',
						'padding': '5px', 
					})
					.appendTo(table);
				$('#{$id} .progresso').html('');
				$('#{$g['divId']}').find('#T007_Id-{$array['todosCampos']['T007_Id']}').removeClass('linhaSuccess linhaInfo').addClass('linhaErrorNoColor');
				if (request.T007_TX_Resposta_Codigo == 202) {
					if (request.T007_TX_Retorno_Sefaz_40) {
						div1.html('<span style="color: black;font-size:16px;">' + request.codigoTXT + ': ' + request.T007_TX_Retorno_Sefaz_40 + ' ... ' + '</span><br />');
					} else {
						div1.html('<span style="color: black;font-size:16px;">' + request.codigoTXT + ': ' + request.T007_TX_Retorno_Sefaz + ' </span><br />');
					}
					
					$('<a>')
						.attr('href', '#')
						.css({
							'background-color': '#FFF',
							'color': '#EE6363'
						})
						.appendTo(div1);
				} else {
					div1.html('<span style="color: var(--BGVermelho);">' + request.codigoTXT + '</span>');
				}
				$('<span>').html(request.Procedimento).appendTo(div2);
				if (request.layoutCompacto225) {
					// A rejeição 225 usa a largura da grade e distribui a orientação
					// horizontalmente para reduzir a altura do bloco.
					div1.hide();
					div3.hide();
					table.css({
						'width': '98%',
						'max-width': '1750px',
						'margin': '6px auto',
						'table-layout': 'fixed'
					});
					div2
						.attr('colspan', '3')
						.css({
							'width': 'auto',
							'max-width': '1750px',
							'padding': '8px 12px',
							'font-size': '14px',
							'line-height': '1.25',
							'text-align': 'left',
							'vertical-align': 'top',
							'white-space': 'normal',
							'overflow-wrap': 'break-word',
							'word-break': 'break-word',
							'border-radius': '4px'
						});
					$('<div></div>')
						.css({
							'margin-bottom': '7px',
							'padding-bottom': '5px',
							'border-bottom': '1px solid #e3a4a4',
							'font-size': '15px',
							'color': 'var(--BGVermelho)'
						})
						.html('<strong>' + request.codigoTXT + ':</strong> ' + request.T007_TX_Retorno_Sefaz)
						.prependTo(div2);
				}
				//$('<span>').html(request.T007_TX_Mensagem).appendTo(div1);
				// Deu erro, botão agora é 'Retransmissão'
				if(!request.Contingencia) {
					$('#{$id} button:first')
						.{$hideEnvioNormal}
						.css({'opacity': '1','padding':'3px', 'font-size':'14px'})
						.attr('refazer', 'N')
						.find('span').text('Tentar Novamente');
				} else {
					$('#{$id} button:eq(1)')
					.{$hideSVC}
					.css({'opacity': '1','padding':'3px', 'font-size':'14px'})
					.attr('refazer', 'N')
					.find('span').text('Tentar Novamente - Contingência');
				}
				
				$('#{$id} button:eq(7)').hide();
				// So mostra se tiver ativo
				//if (request.status_scan == '1') {
					$('#{$id} button:eq(1)')
						.{$hideSVC};
				//}
			}
			if ('{$array['todosCampos']['T007_TX_Flag_Finalizada']}' == '2') {
				if ('{$obrigatorioEnvioDanfeViaEmail}' == '0' || ('{$obrigatorioEnvioDanfeViaEmail}' == '1' && '{$emailEnviado}' == '1')) {
					$('#{$id} button:eq(6)').show().css('opacity', '1').show();
				}
			}
			// Denegado
			if (request.denegada == '1') {
				$('#{$id} button:eq(0)').hide();
				$('#{$id} button:eq(1)').hide();
				$('#{$id} button:eq(2)').hide();
				$('#{$id} button:eq(3)').hide();
				$('#{$id} button:eq(4)').hide();
				$('#{$id} button:eq(5)').hide();
				$('#{$id} button:eq(6)').show().css('opacity', '1');
				$('#{$id} button:eq(7)').hide();
				return false;
			}
		});
	}
	{$func}();
} </script></div>
EOT;
	//}
	$sqlD006 = mysql_query("SELECT * FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id WHERE T007_Id = '{$array['todosCampos']['T007_Id']}'");
	$D006 = mysql_fetch_array($sqlD006);
	if (($D006['T007_TX_Resposta_Codigo'] == 201 || $D006['T007_TX_Resposta_Codigo'] == 202)
		&& trim((string) $D006['T007_TX_Resposta_Extra']) === '225') {
		$diagnostico225 = NFeRejeicao225::interpretar(
			$D006['T007_TX_Retorno_Sefaz'],
			isset($D006['T007_Arquivo_XML_Temp']) ? $D006['T007_Arquivo_XML_Temp'] : ''
		);
		$resumo225 = htmlspecialchars($diagnostico225['resumo'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
		$onde225 = htmlspecialchars($diagnostico225['ondeVerificar'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
		$orientacao225 = htmlspecialchars($diagnostico225['orientacao'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
		$D006['T007_TX_Retorno_Sefaz'] = '<strong>' . $resumo225 . '</strong><br />'
			. 'Onde verificar: ' . $onde225 . '<br />'
			. 'Como corrigir: ' . $orientacao225;
	}
	if($array['todosCampos']['T007_Flag_Almoxarifado'] == 'S' && empty($array['todosCampos']['T007_Numero_Protocolo_Nfe']) && $D006['D006_Flag_Gera_Contas'] == 'R' && $D006['T007_Flag_Finalidade_NFe']!=2){
		// Verifica se o valor da Nota está igual ao valor dos boletos e dos titulos a receber
		$valorTitulos = 0;
		$valorBoletos = 0;
		$outrosPagamentos = 0;
		$valorNotaFiscal = $array['todosCampos']['T007_Valor_Total'];
		$flagBoleto = false;
		$sqlT002 = mysql_query("SELECT * FROM T002 LEFT JOIN D027 ON D027_Id = T002_D027_Id LEFT JOIN T117 ON T117_Id = T002_T117_Id WHERE T002_T007_Id = '{$array['todosCampos']['T007_Id']}' AND (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)");
		while($T002 = mysql_fetch_array($sqlT002)){
			$valorTitulos += $T002['T002_Valor_Duplicata'];			
			if($T002['D027_Flag_Boleto'] == 'S') {
				$flagBoleto = true;
				if($T002['T117_Flag_Cancelado'] != 'S'){					
					$valorBoletos += $T002['T117_Valor_Total'];
				}
			} else {
				$outrosPagamentos += $T002['T002_Valor_Duplicata'];
			}			
		}
		$valorTotalAReceber = $valorBoletos + $outrosPagamentos;
		if($flagBoleto){ //Verifica os titulos e os boletos
			if(number_format($valorTitulos,2) != number_format($valorNotaFiscal,2) || number_format($valorTotalAReceber,2) != number_Format($valorNotaFiscal,2)) {
				$valorBoletos = gCorrigeNumero($valorBoletos);
				$valorNotaFiscal = gCorrigeNumero($valorNotaFiscal);
				$valorTitulos = gCorrigeNumero($valorTitulos);
				$outrosPagamentos = gCorrigeNumero($outrosPagamentos);		
				$return = "<span style='color: var(--BGVermelho);'>O Valor da nota está diferente dos titulos a receber! NF: $valorNotaFiscal Titulos: $valorTitulos Boletos: $valorBoletos</span>";
				if (!empty($D006['T007_TX_Retorno_Sefaz'])) {
					$return .= "<div style='color: var(--BGVermelho);'>Erro Sefaz:</br>{$D006['T007_TX_Retorno_Sefaz']}</div>";
				}
			}
		} else { //Verifica apenas os titulos (casos como depósito ou transferencia bancaria)
			if(number_format($valorTitulos,2) != number_format($valorNotaFiscal,2)) {
				$valorNotaFiscal = gCorrigeNumero($valorNotaFiscal);
				$valorTitulos = gCorrigeNumero($valorTitulos);
				$return = "<span style='color: var(--BGVermelho);'>O Valor da nota está diferente dos titulos a receber! $valorNotaFiscal Titulos: $valorTitulos </span>";
				if (!empty($D006['T007_TX_Retorno_Sefaz'])) {
					$return .= "<div style='color: var(--BGVermelho);'>Erro Sefaz:</br>{$D006['T007_TX_Retorno_Sefaz']}</div>";
				}
			}
		}
	}

	// Trava: Não será possível emitir a nota se a nota não estiver finalizada
	if($D006['T007_Flag_Travar_Nota'] == 'N'){
		$return = "<span style='color: var(--BGVermelho);'>A nota fiscal não está finalizada - finalize a nota fiscal para transmitir.</span>";
	}

	// 	Trava: Não será possível tentar trabsmitir uma nota novamente antes de dois minutos do último erro
 	$minutos = 2;
	$modelo = ($array['todosCampos']['T007_Flag_ACP'] == '6') ? '55' : '65';
	$T127 = mysql_query("SELECT * FROM T127 WHERE T127_Numero_Nota_Fiscal='{$array['todosCampos']['T007_Numero_Nota_Fiscal']}' AND T127_C004_Id='{$g['empresaAtual']}' AND T127_Modelo='{$modelo}' ORDER BY T127_Id DESC,T127_Data_Transmicao");
	$mT127 = mysql_fetch_array($T127);
	$horaUltimoEnvio = explode(" ", $mT127['T127_Data_Transmicao']);
	$horasSomada = strtotime('+{$minutos} minute',$horaUltimoEnvio[1]);
	if(mysql_num_rows($T127) > 0 && (!empty($mT127['T127_Codigo_Retorno']) && $mT127['T127_Codigo_Retorno'] != '100') && $horaUltimoEnvio[0] == date('Y-m-d')){
		$hora = $horaUltimoEnvio[1];
		$horaNova = strtotime("$hora + 2 minutes"); 
		$horaNovaFormatada = date("H:i:s",$horaNova);
		if($horaNovaFormatada > date('H:i:s')){
			$return = "<span style='color: var(--BGVermelho);'>Verifique e corrija o erro, tente novamente a partir das {$horaNovaFormatada} <Br /> (É necessário aguardar 2 minutos para nova tentativa após ocorrência de erro)</span>";
			$return .= "<div style='color: var(--BGVermelho);'><br />Erro Sefaz:</br>{$D006['T007_TX_Retorno_Sefaz']}</div>";
		}
	}  
	
 	if(mysql_num_rows($T127) > 0 && $envioSVC == 'S' && $mT127['T127_Data_Transmicao'] < $g['c020']['C020_Data_NFe_SVC_Ativado']){
		$return = "<span style='color: var(--BGVermelho);'>Envio já realizado pelo processo normal, não pode ser enviada novamente via SVC <Br /></span>";
	} 


	// Trava: Não será possível emitir a nota se tiver algum problema no estoque
	// Ou seja, já tem que existir um T001_Id (quando vem da LOJA pode exemplo) ou tem que ter uma quantidade de separação (quando vem da Expedição)
	$sql = <<<EOT
SELECT 
	T008_Id,
	T001_Id,
	T006_Quantidade_Separacao,
	T005_Flag_Status
FROM T008 
LEFT JOIN D006 ON D006_Id=T008_D006_Id
LEFT JOIN T001 ON T001_T008_Id=T008_Id
LEFT JOIN T006 ON T008_T006_Id=T006_Id
LEFT JOIN T005 ON T005_Id=T006_T005_Id
WHERE
	T008_T007_Id = '{$array['todosCampos']['T007_Id']}'
	AND T007_Flag_Cancelada != 'S'
	AND D006_Flag_Estoque != 'N'
	AND IFNULL(T001_Id, 0) = 0
	AND IFNULL(T006_Quantidade_Separacao, 0) = 0
	AND T005_Flag_Status > 2
	AND IF(IFNULL(T006_C004_Id_Estoque,0) > 0, T006_C004_Id_Estoque, T005_C004_Id) = T005_C004_Id
EOT;
	$res = mysql_query($sql);
	if (mysql_num_rows($res) > 0) {
		$return = "<span style='color: var(--BGVermelho);'>Existe um problema na separação dos produtos - verifique se o pedido foi separado corretamente.</span>";
		if (!empty($D006['T007_TX_Retorno_Sefaz'])) {
			$return .= "<div style='color: var(--BGVermelho);'>Erro Sefaz:</br>{$D006['T007_TX_Retorno_Sefaz']}</div>";
		}
	}

	$sql = mysql_query("SELECT D006_Observacao, ifnull(T007_Valor_Total_ICMS_Oculto, 0) as T007_Valor_Total_ICMS_Oculto, ifnull(T007_Valor_Total_Base_ICMS_Oculto, 0) as T007_Valor_Total_Base_ICMS_Oculto FROM T008 LEFT JOIN T007 ON T007_Id=T008_T007_Id LEFT JOIN D006 ON T008_D006_Id=D006_Id WHERE T008_T007_Id = '{$array['todosCampos']['T007_Id']}' GROUP BY D006_Id");
	while($row = mysql_fetch_array($sql)){
		if(strpos($row['D006_Observacao'], 'ICMS_OCULTO') !== false){
			if($row['T007_Valor_Total_ICMS_Oculto'] <= 0 || $row['T007_Valor_Total_Base_ICMS_Oculto'] <= 0){
				$return = "<span style='color: var(--BGVermelho);'>Existe um problema nas informações complementares da nota fiscal, verifique se o valor e a base de cálculo de ICMS estão preenchidos.</span>";
				if (!empty($D006['T007_TX_Retorno_Sefaz'])) {
					$return .= "<div style='color: var(--BGVermelho);'>Erro Sefaz:</br>{$D006['T007_TX_Retorno_Sefaz']}</div>";
				}			
			}
		}
	}
	return $return;
}
