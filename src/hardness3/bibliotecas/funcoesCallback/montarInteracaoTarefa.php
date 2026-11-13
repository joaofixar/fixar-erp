<?php
namespace hardness;

function montarInteracaoTarefa($conteudo, $D085_Id, $tipo = 'R', $visao = 'vertical') {
	global $g;
	$boxShadow = "-webkit-box-shadow: 1px 1px 1px 1px silver; -moz-box-shadow: 1px 1px 1px 1px silver; box-shadow: 1px 1px 1px 1px silver;";
	$contato = (isset($conteudo['contato'][0])) ? ' - '.$conteudo['contato'][0] : '';
	$contato .= (!empty($conteudo['contato'][1])) ? " ({$conteudo['contato'][1]})" : '';

	$sql = mysql_query("SELECT * FROM T139 WHERE T139_Id = '{$conteudo['T139_Id']}'");
	$row = mysql_fetch_array($sql);
	//Verifica e-mail enviado
	if($conteudo['T139_Id'] > 0){
		// title="Visualizar e-mail" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Leitura de Email', '/etc/etc006/content/etc006ContentEmail/', '&pasta=E&acaoId=' + encodeURIComponent('{$conteudo['T139_Id']}'), [900,550]);"
		$email = <<<EOT
			<span class="ui-icon-tarefas ui-icon-mail-closed" style="float: left;"></span>
EOT;
	} else {
		$email = <<<EOT
			<span class="ui-icon-tarefas ui-icon-mail-closed" title="" style="opacity: 0.4; float: left; display: none;"></span>
EOT;
	}

	//Monta botão de impressão
	if($conteudo['entidade'][0] != ''){
		switch ($conteudo['entidade'][0]) {
  			case 'T003':
  				$impressao = <<<EOT
  					$('#{$g['divId']}').showLoading();
  					$.getJSON('/ven/ven001/form_func-ajax/ven001ImprimirOrcamento/?ajax=true&T003_Id=' + encodeURIComponent('{$conteudo['entidade'][1]}') + '&callback=?', function(request) {
  						$('#{$g['divId']}').hideLoading();
  						if(request.code === true){
  							window.open(request.data);
  						} else {
  							var \$buttons = { 'Fechar': function() { $(this).dialog('destroy').remove(); } }
	    					dialogConfirm('Erro', request.data, \$buttons);
  						}
  					});
EOT;
  			break;
  			case 'T005':
  				$impressao = <<<EOT
  					$('#{$g['divId']}').showLoading();
  					$.getJSON('/ven/ven002/form_func-ajax/ven002ImprimirPedido/?ajax=true&T005_Id=' + encodeURIComponent('{$conteudo['entidade'][1]}') + '&callback=?', function(request) { 
  						$('#{$g['divId']}').hideLoading();
  						if(request.code===true) {
  							window.open(request.data);
  						} else {
  							var \$buttons = { 'Fechar': function() { $(this).dialog('destroy').remove(); } }
	    					dialogConfirm('Erro', request.data, \$buttons);
	    				}
	    			});
EOT;
  			break;
  			case 'T007':
  				$impressao = <<<EOT
  					nova_janela('/hardness3/hardness/danfe/imprimir_danfe.php?T007_Id=' + encodeURIComponent('{$conteudo['entidade'][1]}') ,0,0,1000,600,'no','no','no','no','no','no','Danfe');
EOT;
  			break;
  			case 'T139':
  				$impressao = <<<EOT
  					nova_janela('/etc/etc006/form/etc006FormEmail/?ajax=true&acaoId='+encodeURIComponent('{$conteudo['entidade'][1]}'),0,0,1000,600,'no','no','no','no','yes','yes','Imprimir E-mail');
EOT;
  			break;
  		}
  		$impressao = str_replace("\n", ' ', $impressao);
		$impressao = <<<EOT
			<span class="ui-icon-tarefas ui-icon-print" title="" style="cursor: pointer; float: left;" onclick="{$impressao}"></span>
EOT;
	} else {
		$impressao = <<<EOT
			<span class="ui-icon-tarefas ui-icon-print" title="" style="opacity: 0.4; float: left;"></span>
EOT;
	}
	$cor = ($tipo == 'R') ? '#C1FFB6' : '#FFFFFF';
	$margin = ($tipo == 'R') ? '5px 5px 1px 1px' : '5px 1px 1px 5px';
	switch ($conteudo['entidade'][0]) {
		case 'RETORNO':
			$cor = '#F9D4A5';
			$cssEntidade = "display: inline-block;";
			break;
		case 'T003':
			$cor = '#A5DDF9';
			$cssEntidade = "display: inline-block;";
			break;
		case 'T005':
			$cor = '#FDFAA8';
			$cssEntidade = "display: inline-block;";
			break;
		case 'T007':
			$cor = '#A5EAA5';
			$cssEntidade = "display: inline-block;";
			break;
		case 'T224':
		case 'T255':
			$cor = '#DE92FD';
			$cssEntidade = "display: inline-block;";
			break;
		case 'T218':
			$cor = '#FD9292';
			$cssEntidade = "display: inline-block;";
			break;
		case 'T182':
			$cor = '#FF6347';
			$cssEntidade = "display: inline-block;";
			break;
		case 'EST_PEDIDO':
			$cor = '#DBFDDB';
			$cssEntidade = "display: inline-block;";
			break;
	}
	if($visao == 'horizontal'){
		$fontSize = "font-size: 13px";
		//$ajusteVisao = 'width: 280px;';
	} else {
		$ajusteVisao = '';
		$fontSize = "font-size: 13px";
	}
	$retorno = <<<EOT
		<div id="D085_Id-{$D085_Id}" style="display: inline-block; vertical-align: top; {$ajusteVisao}{$cssEntidade} box-sizing:border-box; margin: {$margin}; background-color: {$cor}; border-radius: 10px; padding: 5px; {$boxShadow} cursor: pointer;" class="registroInteracao" entidade="{$conteudo['entidade'][0]}" idEntidade="{$conteudo['entidade'][1]}" T003_Id="{$conteudo['T003_Id']}" T182_Id="{$conteudo['T182_Id']}" T005_Id="{$conteudo['T005_Id']}">
			<div style="float: left; font-size: 12px; color: #000; width: 100%; white-space: normal;">
EOT;
	if($conteudo['entidade'][0] != 'RETORNO'){
	$retorno .= <<<EOT
				<div style="font-size: 11px; color: #222; font-weight: normal; margin-bottom: 3px; float: left;">{$conteudo['icones']} <b>{$conteudo['usuario']}</b> em {$conteudo['data']} <b>{$contato}</b> {$conteudo['retornoAgendado']}</div>
				<div style="margin-bottom: 3px; float: right; text-align: right;">
					<!--{$impressao}-->
					<!--<span class="ui-icon-tarefas ui-icon-extlink abrirInteracao" style="cursor: pointer;" entidade="{$conteudo['entidade'][0]}" idEntidade="{$conteudo['entidade'][1]}"></span>-->
				</div>
				<div style="clear: both;" />
EOT;
	}
	$retorno .= <<<EOT
				<div style="{$fontSize}; cursor: pointer; width: 100%;">
					{$conteudo['mensagem']}
				</div>
				<div class="mensagemOculta" style="display: none;"><hr style="border:none; color:#909090; background-color:#B0B0B0; height: 1px; width: 99%;">{$conteudo['mensagemOculta']}</div>
			</div>
			<div style="clear: both;" />
		</div>
		<div style="clear: both;" />
EOT;
	
	$alertT003 = gGeraAlertMsg('Aviso', 'Este orçamento já está aberto',false);
	$alertT005 = gGeraAlertMsg('Aviso', 'Este pedido já está aberto',false);

	//$retorno .= ($visao == 'vertical')	? '<div style="clear: both;" />' : '';
	$retorno .= <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			//$(".registroInteracao").unbind('click').click(function () {
			//    var header = $(this);
			//    //getting the next element
			//    var content = header.next();
			//    //open up the content needed - toggle the slide- if visible, slide up, if not slidedown.
			//    content.slideToggle(500, function () {
			//    });
			//});

			$(".registroInteracao").unbind('click').click(function () {
				var entidade = $(this).attr('entidade');
				var id = $(this).attr('idEntidade');
				if(entidade == 'T003'){
					var T003_Id = $(this).attr('T003_Id');
					if(T003_Id != id){
						abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar Orçamento', '/ven/ven001/content/ven001ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent(id) + '&tabela=T003', [990,605]);
					}else{
						{$alertT003}
					}
				} else if(entidade == 'T005'){
					var T005_Id = $(this).attr('T005_Id');
					if(T005_Id != id){
						abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar Pedido', '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela=T005', [990,605]);
					}else{
						{$alertT005}
					}
				} else if(entidade == 'T007'){
					abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar Nota Fiscal', '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent(id) + '&tabela=T007', [990,605]);
				} else if(entidade == 'T139'){
					abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Leitura de Email', '/etc/etc006/content/etc006ContentEmail/', '&pasta=E&acaoId=' + encodeURIComponent(id) + '&T259_Id=' + encodeURIComponent('{$conteudo['D085_T259_Id']}') + '&C007_Id=' + encodeURIComponent('{$conteudo['D085_C007_Id']}'), [900,550]);
				} else if(entidade == 'D085'){
					abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar Agenda', '/crm/crm001/content/crm001contentEditarAgenda/', '&acaoId=' + encodeURIComponent(id), [700,450]);
				}else if(entidade == 'T182'){
					abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Editar Produto - ID: ' + id, '/est/est011/content/est011ContentIncluirProduto/', '&acaoId=' + encodeURIComponent(id) + '&tabela=T182', [580,590]);
				}	
			});
		} </script></div>
EOT;
	return $retorno;
}