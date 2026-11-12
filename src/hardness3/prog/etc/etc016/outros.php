<?php
namespace hardness;

switch ($g['r'][3]) {
	case 'etc016VerificarPorCodbarra':
		$r_pdv = isset($r_pdv) ? '1' : '0';
		$r_id = !isset($r_id) ? '' : $r_id;
		$r_flag = !isset($r_flag) ? '' : $r_flag;
		$r_qtd = !isset($r_qtd) ? '' : $r_qtd;
		$r_concluirUrl = isset($r_concluirUrl) ? base64_decode($r_concluirUrl) : '';
		$r_concluirParams = isset($r_concluirParams) ? base64_decode($r_concluirParams) : '';
		$r_preco = !isset($r_preco) ? '' : $r_preco;
		$r_acaoPosConcluirUrl = isset($r_acaoPosConcluirUrl) ? base64_decode($r_acaoPosConcluirUrl) : '';
		$r_acaoPosConcluirParams = isset($r_acaoPosConcluirParams) ? base64_decode($r_acaoPosConcluirParams) : '';

		if ($r_sql) {
			echo <<<EOT
<div style="width: 100%; background: #E3E1E1; height: 30px; text-align: center; padding: 10px 0 0 10px; font-size: 1.5em; color: green; font-weight: bold; border-bottom: 1px solid #BABABA;">
	<span class="cabecalho">Leia um código de barras...</span>
	<div class="cabecalhototal" style="float: right; font-weight: bold; margin: -3px 36px 0 0; font-size: 1.3em; color: blue;"></div>
</div>
<div class="corpo" style="width: 100%; height: 100%; overflow-y: scroll;">
	<table style="width: 100%;" cellspacing="0">
	</table>
</div>
<div class="rodapeCodBarra" style="width: 100%; background: #E3E1E1; height: 34px; text-align: center; padding: 6px 0 0 10px; font-size: 1.2em; color: green; font-weight: bold; border-top: 1px solid #BABABA;">
	<button type="button" id="botaoCancelarConf" style="display: none;">Cancelar</button>
	<button type="button">Concluir</button>
</div>
EOT;
		} else {
			echo <<<EOT
<div style="width: 100%; background: #E3E1E1; height: 30px; text-align: center; padding: 10px 0 0 10px; font-size: 1.5em; color: green; font-weight: bold; border-bottom: 1px solid #BABABA;">
	<span class="cabecalho">Leia o ID para iniciar a conferência...</span>
	<div class="cabecalhototal" style="float: right; font-weight: bold; margin: -3px 36px 0 0; font-size: 1.3em; color: blue;"></div>
</div>
<div class="corpo" style="width: 100%; height: 100%; overflow-y: scroll;">
	<table style="width: 100%;" cellspacing="0">
	</table>
</div>
EOT;
		}
		echo <<<EOT
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	// Elementos
	var cabecalho = $('#{$g['divId']} .cabecalho');
	var cabecalhototal = $('#{$g['divId']} .cabecalhototal');
	var corpo = $('#{$g['divId']} .corpo');
	var table = corpo.find('table');
	var rodape = $('#{$g['divId']} .rodapeCodBarra');

	// Ajustes do elementos
	$('#{$g['divId']}').parent().css({'padding': '0'});
	cabecalho
		.width(cabecalho.width() - 10);
	corpo
		.height(corpo.height() - (41 * 2));
	rodape
		.width(rodape.width() - 10);

	// Ajustes de interface
	function verCodBarraAjustesUI() {
		var somaTotal = 0;
		var maiorCasasDecimais = 0;
		// linha por linha
		$('#{$g['divId']} .corpo .item').each(function() {
			// opacity da linha
			var item = $(this);
			if (item.attr('verificado') == 0) {
				item.css('opacity', '0.4');
			} else {
				item.css('opacity', '1');
			}
			// preco
			if (item.find('.mostrarPreco').size() > 0) {
				var preco_original = Corrige_Numero_Inverte(item.find('.mostrarPreco').attr('preco-original'));
				var casasDecimais = preco_original.split('.');
				var casasDecimais = casasDecimais[1].length;
				if (casasDecimais > maiorCasasDecimais) {
					maiorCasasDecimais = casasDecimais;
				}
				var quantidade = item.find('input').val() != '' ? item.find('input').val() : 1;
				var valor = parseFloat(preco_original) * parseInt(quantidade);
				somaTotal += valor;
				item.find('.mostrarPreco').html('R$ ' + number_format( valor, casasDecimais, ',', '.' ));
			}
		});
		// mostra total geral
		if (somaTotal > 0) {
			cabecalhototal.html('R$ ' + number_format( somaTotal, maiorCasasDecimais, ',', '.' ));
		} else {
			cabecalhototal.html('');
		}
		// cores das linhas
		$('#{$g['divId']} .corpo .item').removeClass('color1').removeClass('color2');
		$('#{$g['divId']} .corpo .item:odd').addClass('color1');
		$('#{$g['divId']} .corpo .item:even').addClass('color2');
	}
	verCodBarraAjustesUI();

	// Identifica quando um cod. de barras é lido
	codBarraLastTime = 0;
	codBarraScan = '';
	$(document).unbind('keydown').bind('keydown', function(e) {
		// 46 - delete
		if ( e.which == 46 ) {
			corpo.find('.item:first').remove();
			verCodBarraAjustesUI();
		}
	});
	$(document).unbind('keypress').bind('keypress', function(e) {
		// não faz nada se tiver o focus no input
		if ($(':focus').is('input')) { return true; }
		// 
		var agora = new Date().getTime();
		if ((agora - codBarraLastTime) > 500 && !codBarraLendo) {
			codBarraScan = '';
		}
		//console.log(e.which + ' - ' + String.fromCharCode(e.which));
		// 42 = barra de espaço
		if ( e.which == 42 ) {
			codBarraScan = '';
			cabecalho.css('color', 'green');
			cabecalho.html('Lendo código de: ');
			codBarraLendo = true;
		}
		// 8 - backspace
		if ( e.which == 8 ) {
			if (codBarraScan != '' && codBarraScan != '*') {
				codBarraScan = codBarraScan.substr(0,codBarraScan.length-1);
				cabecalho.html('Lendo código de: ' + codBarraScan);
			}
			return false;
		}
		// 13 - enter
		if ( e.which == 13 ) {
			if (codBarraScan != '') {
EOT;
if ($r_sql) {
echo <<<EOT
				cabecalho.css('color', 'blue');
				cabecalho.html('Procurando código: ' + codBarraScan + ' ...');
				if (codBarraLendo) {
					codBarraScan = codBarraScan.substr(1);
				}

				var itens = {};

				//Recupera os itens do html
				corpo.find('.item').each(function(i, val) {					
					var codItem = $(this).attr('cod');			
					var valorItem = $(this).find('input').val();
				
					if(!itens.hasOwnProperty(codItem)) {
						itens[codItem] = 0;
					}
					
					itens[codItem] += valorItem == '' ? 1 : parseInt(valorItem);
				});
				
				var itens = JSON.stringify(itens);

				// item não encontrado... fazer um ajax nos produtos
				if ('{$r_pdv}' == '0') {
					var url = '/etc/etc016/ajax/etc016ProdutoNovo/';
					var params = 'ajax=true&codbarra=' + encodeURIComponent(codBarraScan) + '&itens=' + encodeURIComponent(itens) + '&sql=' + encodeURIComponent('{$r_sql}') + '&flag=' + encodeURIComponent('{$r_flag}') + '&cod=' + encodeURIComponent('{$r_cod}') + '&qtd=' + encodeURIComponent('{$r_qtd}') + '&desc=' + encodeURIComponent('{$r_desc}') + '&preco=' + encodeURIComponent('{$r_preco}') + '&callback=?';
				} else {
					var url = '/etc/etc016/ajax/etc016ProdutoNovoPDV/';
					var params = 'ajax=true&codbarra=' + encodeURIComponent(codBarraScan) + '&itens=' + encodeURIComponent(itens) + '&sql=' + encodeURIComponent('{$r_sql}') + '&cod=' + encodeURIComponent('{$r_cod}') + '&id=' + encodeURIComponent('{$r_id}') + '&desc=' + encodeURIComponent('{$r_desc}') + '&preco=' + encodeURIComponent('{$r_preco}') + '&callback=?';
				}
				$.ajax({
					url: url,
					dataType: 'json',
					data: params,
					type: 'post',
					success: function(request) {
						if (request.code) {
							cabecalho.css('color', 'green');
							cabecalho.html('ENCONTRADO!');
							$(request.data).prependTo(table).show('slow', function() {
								verCodBarraAjustesUI();
								$(this).effect("highlight", {}, 3000);
								//$(this).find('input').focus();
							});
						} else {
							// cod. barra não encontrado (exibir mensagem)
							cabecalho.css('color', 'red');
							cabecalho.html(request.data);
							reproduzirSomErro();
						}
					}
				});
				codBarraScan = '';
EOT;
} else {
echo <<<EOT
				cabecalho.css('color', 'blue');
				cabecalho.html('Procurando ID: ' + codBarraScan + ' ...');
				if (codBarraLendo) {
					codBarraScan = codBarraScan.substr(1);
				}
				$('#{$g['divId']}').data('codvar', codBarraScan);
				$.ajax({
					url: '/etc/etc016/ajax/etc016SqlCodVar/',
					dataType: 'json',
					data: 'ajax=true&codbarra=' + encodeURIComponent(codBarraScan) + '&sql_codvar=' + encodeURIComponent('{$r_sql_codvar}') + '&callback=?',
					type: 'post',
					success: function(request) {
						if (request.code) {
							cabecalho.css('color', 'green');
							cabecalho.html('ENCONTRADO!');
							setTimeout(function() {
								divSetarAcaoId('{$g['divId']}', encodeURIComponent(request.data), 'sql');
								$('#{$g['divId']}').data('sql_codvar', '{$r_sql_codvar}');
								divSetarAcaoId('{$g['divId']}', '', 'sql_codvar');
								divRefresh('{$g['divId']}');
							}, 1000);
						} else {
							// cod. barra não encontrado (exibir mensagem)
							cabecalho.css('color', 'red');
							cabecalho.html(request.data);
							reproduzirSomErro();
						}
					}
				});
				codBarraScan = '';
EOT;
}
echo <<<EOT
			} else {
				cabecalho.css('color', 'green');
				cabecalho.html('Leia um código de barras...');
			}
			codBarraLendo = false;
		} else if ( e.which == 32 ) {
			if (corpo.find('input').size()) {
				cabecalho.css('color', 'green');
				cabecalho.html('Digite a Quantidade e aperte ENTER...');
				corpo.find('input:first').focus();
			}
		} else {
			codBarraLastTime = agora;
			codBarraScan = codBarraScan + String.fromCharCode(e.which);
			if (codBarraLendo) {
				cabecalho.css('color', 'blue');
				cabecalho.html('Lendo código de: ' + codBarraScan);
			}
		}		
	});

	// Botão 'Concluir'
	rodape.find('button').unbind('click').bind('click', function() {
		var itens = {};
		corpo.find('.item').each(function(i, val) {
			itens[i] = {};
			itens[i]['cod'] = $(this).attr('cod');
			itens[i]['qtd'] = $(this).find('input').val();
			if (itens[i].qtd == '') {
				itens[i].qtd = 1;
			}
		});
		var itens = JSON.stringify(itens);
		// verifica...
		if ('{$r_pdv}' == '0') {
			var url = '/etc/etc016/ajax/etc016Verificar/';
			var params = 'ajax=true&itens=' + encodeURIComponent(itens) + '&sql=' + encodeURIComponent('{$r_sql}') + '&flag=' + encodeURIComponent('{$r_flag}') + '&cod=' + encodeURIComponent('{$r_cod}') + '&qtd=' + encodeURIComponent('{$r_qtd}') + '&desc=' + encodeURIComponent('{$r_desc}') +  '&callback=?';
		} else {
			var url = '{$r_concluirUrl}';
			var params = 'ajax=true&itens=' + encodeURIComponent(itens) + '{$r_concluirParams}' + '&callback=?';
		}
		//$('#{$g['divId']}').showLoading();
		$.ajax({
			url: url,
			dataType: 'json',
			data: params,
			type: 'post',
			success: function(request) {
				if ('{$r_acaoPosConcluirUrl}' != '') {
					$.ajax({
						url: '{$r_acaoPosConcluirUrl}',
						dataType: 'html',
						data: '&ajax=true&flag=' + encodeURIComponent('{$r_flag}') + '&sql_codvar=' + encodeURIComponent($('#{$g['divId']}').data('sql_codvar')) + '&codvar=' + encodeURIComponent($('#{$g['divId']}').data('codvar'))  + '&divId=' + encodeURIComponent('{$g['divId']}')  + '{$r_acaoPosConcluirParams}',
						type: 'post',
						success: function(request) {
							eval(request);
						}
					});
				} else {
					//$('#{$g['divId']}').hideLoading();
					var sql_codvar = $('#{$g['divId']}').data('sql_codvar');
					if (sql_codvar) {
						cabecalho.css('color', 'green');
						cabecalho.html('CONCLUIDO!');
						setTimeout(function() {
							divSetarAcaoId('{$g['divId']}', '', 'sql');
							divSetarAcaoId('{$g['divId']}', encodeURIComponent(sql_codvar), 'sql_codvar');
							divRefresh('{$g['divId']}');
						}, 1000);
					} else {
						//console.log(request);
						$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
						divRefresh('{$g['divIdParent']}', true);
					}
				}
			}
		});
	});

	// Botão Fechar
	var sql_codvar = $('#{$g['divId']}').data('sql_codvar');
	if (sql_codvar) {
		$('#botaoCancelarConf').unbind('click').bind('click', function() {
			divSetarAcaoId('{$g['divId']}', '', 'sql');
			divSetarAcaoId('{$g['divId']}', encodeURIComponent(sql_codvar), 'sql_codvar');
			divRefresh('{$g['divId']}');
		}).show();
	} else {
		$('#botaoCancelarConf').hide();
	}


	function reproduzirSomErro(){ 
		var som = new Audio("data:audio/wav;base64,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qJrx4KOQjahZxWbcZgztj2c49nKmkId44S71j0c8eV9yDK6uPRzx5X18eDvjvQ6yKo9ZSS6l//8elePK/Lf//IInrOF/FvDoADYAGBMGb7FtErm5MXMlmPAJQVgWta7Zx2go+8xJ0UiCb8LHHdftWyLJE0QIAIsI+UbXu67dZMjmgDGCGl1H+vpF4NSDckSIkk7Vd+sxEhBQMRU8j/12UIRhzSaUdQ+rQU5kGeFxm+hb1oh6pWWmv3uvmReDl0UnvtapVaIzo1jZbf/pD6ElLqSX+rUmOQNpJFa/r+sa4e/pBlAABoAAAAA3CUgShLdGIxsY7AUABPRrgCABdDuQ5GC7DqPQCgbbJUAoRSUj+NIEig0YfyWUho1VBBBA//uQZB4ABZx5zfMakeAAAAmwAAAAF5F3P0w9GtAAACfAAAAAwLhMDmAYWMgVEG1U0FIGCBgXBXAtfMH10000EEEEEECUBYln03TTTdNBDZopopYvrTTdNa325mImNg3TTPV9q3pmY0xoO6bv3r00y+IDGid/9aaaZTGMuj9mpu9Mpio1dXrr5HERTZSmqU36A3CumzN/9Robv/Xx4v9ijkSRSNLQhAWumap82WRSBUqXStV/YcS+XVLnSS+WLDroqArFkMEsAS+eWmrUzrO0oEmE40RlMZ5+ODIkAyKAGUwZ3mVKmcamcJnMW26MRPgUw6j+LkhyHGVGYjSUUKNpuJUQoOIAyDvEyG8S5yfK6dhZc0Tx1KI/gviKL6qvvFs1+bWtaz58uUNnryq6kt5RzOCkPWlVqVX2a/EEBUdU1KrXLf40GoiiFXK///qpoiDXrOgqDR38JB0bw7SoL+ZB9o1RCkQjQ2CBYZKd/+VJxZRRZlqSkKiws0WFxUyCwsKiMy7hUVFhIaCrNQsKkTIsLivwKKigsj8XYlwt/WKi2N4d//uQRCSAAjURNIHpMZBGYiaQPSYyAAABLAAAAAAAACWAAAAApUF/Mg+0aohSIRobBAsMlO//Kk4soosy1JSFRYWaLC4qZBYWFRGZdwqKiwkNBVmoWFSJkWFxX4FFRQWR+LsS4W/rFRb/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////VEFHAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAU291bmRib3kuZGUAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMjAwNGh0dHA6Ly93d3cuc291bmRib3kuZGUAAAAAAAAAACU=");  

	  	setTimeout(function(){ som.play(); },500);	

	  	som.play();
	}
} </script></div>
EOT;
	break;
}


/*
	// Produtos de um T013
	if (isset($r_T013_Id)) {
		$sql = <<<EOT
SELECT 
	T014_Id,
	T014_Descricao_Produto,
	T014_Quantidade,
	if(D037_Unidade!=NULL,D037_Unidade,T014_Unidade) as Unidade,
	D001_Id
FROM T014 
left join D009 on D009_Id=T014_D009_Id
left join D049 on D049_Id=D009_D049_Id
left join D001 on D001_Id=D049_D001_Id
left join D037 on D037_Id=D001_D037_Id
WHERE T014_T013_Id = '{$r_T013_Id}'
EOT;
		$resT014 = mysql_query($sql);
		while ($rowT014 = mysql_fetch_assoc($resT014)) {
			$descricao = "{$rowT014['D001_Id']} - {$rowT014['T014_Descricao_Produto']}"; //(" . gCorrigeNumero($rowT014['T014_Quantidade'], 3) . " {$rowT014['Unidade']})";
			echo <<<EOT
		<div class="item" style="width: 100%; height: 35px;" verificado="0" idnota="{$rowT014['T014_Id']}" idproduto="" codbarra="{$rowT014['D001_Id']}">
	 		<div style="font-size: 1.3em; font-weight: bold; padding: 10px 0 0 10px">{$descricao}</div>
		</div>
EOT;
		}
	}
*/