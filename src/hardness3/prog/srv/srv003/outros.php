<?php
namespace hardness;
switch ($g['r'][3]) {
	case 'srv003OutrosServico':
			// Utilizada em UPDATE, é o ID do registro que está sendo editado
			$D109_Id = (isset($r_acaoId)) ? $r_acaoId : false;
			$acaoId = (isset($_REQUEST['T147_Id'])) ? $_REQUEST['T147_Id'] : false;
			$acaoId_ = mysql_real_escape_string($acaoId);
			
			// Lista Serviço
			$sql = <<<EOT
				SELECT 
					T147_Titulo, D024_Nome_Empresa, T147_Tempo_Estimado, D116_Placa, D116_Modelo, D116_Cor, C007_Primeiro_Nome, D001_Descricao_Produto
				FROM
					T147
				left join T005 on T005_Id=T147_T005_Id
				left join C007 on C007_Id=T005_C007_Id_Vendedor_Interno
				left join D024 on T005_D024_Id=D024_Id
				left join D116 on D116_Id=T147_D116_Id
				left join T006 on T006_Id=T147_T006_Id
				LEFT JOIN D009 ON D009_Id=T006_D009_Id 
				LEFT JOIN D049 ON D049_Id=D009_D049_Id 
				LEFT JOIN D001 ON D001_Id=D049_D001_Id
				WHERE T147_Id = '{$acaoId_}'
EOT;
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
			// Total de Processos
			$sql = "SELECT count(*) as total FROM T148 WHERE T148_T147_Id = '{$acaoId_}';";
			$result = mysql_query($sql);
			$processos = mysql_fetch_assoc($result);
			$processos = $processos['total'];
			// Total de Materiais
			$sql = "SELECT count(*) as total FROM T149 WHERE T149_T147_Id = '{$acaoId_}';";
			$result = mysql_query($sql);
			$materiais = mysql_fetch_assoc($result);
			$materiais = $materiais['total'];
			
			$retorno =  <<<EOT
			<table class="showInfoBig">
				<tr>
					<td class="first">Serviço:</td>
					<td class="second">{$row['T147_Titulo']}</td>
				</tr>
				<tr>
					<td class="first">Produto:</td>
					<td class="second">{$row['D001_Descricao_Produto']}</td>
				</tr>
				<tr>
					<td class="first">Cliente:</td>
					<td class="second">{$row['D024_Nome_Empresa']}</td>
				</tr>
				<tr>
					<td class="first">Vendedor:</td>
					<td class="second">{$row['C007_Primeiro_Nome']}</td>
				</tr>
				<tr>
					<td class="first">Veículo:</td>
					<td class="second">{$row['D116_Placa']} {$row['D116_Modelo']} {$row['D116_Cor']}</td>
				</tr>
				<tr>
					<td class="first">Tempo Estimado:</td>
					<td class="second">{$row['T147_Tempo_Estimado']}</td>
				</tr>
EOT;
				// <tr>
					// <td class="first">Processos:</td>
					// <td class="second">Este serviço possui {$processos} processos. <span class="buttonsBottom-big"><button type="submit" id="srv003VerProcessos">Ver Processos</button></span></td>
				// </tr>
				// <tr>
					// <td class="first">Materiais:</td>
					// <td class="second">Exitem {$materiais} materiais envolvidos neste serviço. <span class="buttonsBottom-big"><button type="submit" id="srv003VerMateriais">Ver Materiais</button></span></td>
				// </tr>
			$retorno .= <<<EOT
			</table>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#srv003VerProcessos').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Processos', '/srv/srv003/content/contentProcessosServicos/', '&T147_Id={$acaoId_}', [900,550]);
				});
				$('#srv003VerMateriais').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Materiais', '/srv/srv003/content/contentMateriaisServicos/', '&T147_Id={$acaoId_}', [900,550]);
				});
			} </script></div>
EOT;
			echo $retorno;
			
			/**
			* Botões
			*/
			// Definição dos botões
			$botoesEsq = '';
			$botoesDir = '';
			//$botoesEsq .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Voltar</button>';
			$botoesEsq .= '<button type="button" onclick="window.location = \'/srv/srv003/content/srv003ContentLogin/?oneWindow=true\'; return false;">Sair</button>';
			$botoesEsq .= '<button class="btnFechar" onclick="window.location=\'/srv/srv003/content/srv003ContentOperacao/?oneWindow=true&acaoId='.$r_acaoId.'\'; return false;">Voltar</button>';
			$botoesEsq .= '<button class="btnFechar" onclick="window.location=\'/srv/srv003/content/srv003ContentOperacaoAprovar/?oneWindow=true&acaoId='.$r_acaoId.'\'; return false;">Aguardando aprovação</button>';
			$botoesDir .= '<button type="submit" id="srv003OutrosServicoIniciar">Iniciar</button>';
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesDir', $botoesDir);
			echo  $g['smarty']->fetch('big.buttons-floatBottom.tpl');
			// Ações dos botões
			// A ação do botão Enviar é definido dentro do formulário
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#srv003OutrosServicoIniciar').unbind('click').bind('click', function(data) {
						$.getJSON('/srv/srv003/outro_func-ajax/iniciarServico/?ajax=true&T147_Id=' + encodeURIComponent("{$acaoId_}") + '&D109_Id=' + encodeURIComponent("{$D109_Id}") + '&Usuario=Funcionario' + '&callback=?', function(request) {
							if (request.code) {
								var \$buttons = { 
									"Fechar": function() { 
										window.location = "/srv/srv003/content/srv003ContentOperacao/?oneWindow=true&acaoId=" + encodeURIComponent('{$D109_Id}') + "&T147_Id=" + encodeURIComponent('{$acaoId_}');
										$(this).dialog("destroy").remove(); 
									} 
								}
					    		dialogConfirm("Serviço Iniciado", "O serviço foi iniciado!<br />Por favor, retire a ordem de serviço impressa.", \$buttons, true);
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    		dialogConfirm('Erro', request.data, \$buttons, true);
							}
						});
					});
				} </script></div>
EOT;
	break;
		
	case 'srv003CabecalhoOperacao':
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
		
		$query = "SELECT * FROM D109 WHERE D109_Id='{$acaoId}'"	;
		$result = mysql_query($query);
		$linha = mysql_fetch_assoc($result);
		$nomeFuncionario = $linha['D109_Nome'];
		$now = date('H:i:s');
		echo <<<EOT
			<div style="font-size: 24px; float: left; width: 100%;">
				<div style="float: left; padding: 8px 0 0 10px;">
					Funcionário: {$nomeFuncionario}
				</div>
				<button style="margin-left:15px; font-size:18px; margin-top:3px;" type="button" id="srv003AtualizarPagina">Atualizar Tela</button>
				<div style="float: right; padding: 8px 10px 0 0;">
					<!--<img src="/hardness3/static/img/icone_relogio.png" style="width: 32; height: 32;" />-->
					<span id="horaAtual">00:00:00</span>
				</div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				setInterval(function() {
					var d = new Date();
					var hora = d.getHours();
					var minuto = d.getMinutes();
					var segundo = d.getSeconds();
					if (hora < 10) { hora = '0' + hora; }
					if (minuto < 10) { minuto = '0' + minuto; }
					if (segundo < 10) { segundo = '0' + segundo; }
					$('#horaAtual').html(hora + ':' + minuto + ':' + segundo);
				}, 1000);
				$('#srv003AtualizarPagina').unbind('click').bind('click', function(data) {
						 window.location.reload();
					});
			} </script></div>
EOT;
		break;
		
		case 'srv003CabecalhoOperacaoAprovar':
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
		
		$query = "SELECT * FROM D109 WHERE D109_Id='{$acaoId}'"	;
		$result = mysql_query($query);
		$linha = mysql_fetch_assoc($result);
		$nomeFuncionario = $linha['D109_Nome'];
		$now = date('H:i:s');
		echo <<<EOT
			<div style="font-size: 24px; float: left; width: 100%;">
				<div style="float: left; padding: 8px 0 0 10px;">
					Funcionário: {$nomeFuncionario}<br />
					<font style="font-size:22px; color: var(--BGVermelho); float:left; font-weight:bold;">Aguardando aprovação</font>
				</div>
				<button style="margin-left:15px; font-size:18px; margin-top:3px;" type="button" id="srv003AtualizarPagina">Atualizar Tela</button>
				<div style="float: right; padding: 8px 10px 0 0;">
					<!--<img src="/hardness3/static/img/icone_relogio.png" style="width: 32; height: 32;" />-->
					<span id="horaAtual">00:00:00</span>
				</div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				setInterval(function() {
					var d = new Date();
					var hora = d.getHours();
					var minuto = d.getMinutes();
					var segundo = d.getSeconds();
					if (hora < 10) { hora = '0' + hora; }
					if (minuto < 10) { minuto = '0' + minuto; }
					if (segundo < 10) { segundo = '0' + segundo; }
					$('#horaAtual').html(hora + ':' + minuto + ':' + segundo);
				}, 1000);
				$('#srv003AtualizarPagina').unbind('click').bind('click', function(data) {
						 window.location.reload();
					});
			} </script></div>
EOT;
		break;
	
	case 'srv003CabecalhoLogin':
		$now = date('H:i:s');
		echo <<<EOT
			<div style="font-size: 29px; float: left; width: 100%;">
				<div style="float: left; padding: 3px 0 0 10px;">
					Digite o seu CÓDIGO para entrar
				</div>
				<div style="float: right; padding: 3px 10px 0 0;">
					<!--<img src="/hardness3/static/img/icone_relogio.png" style="width: 32; height: 32;" />-->
					<span id="horaAtual">00:00:00</span>
				</div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				setInterval(function() {
					var d = new Date();
					var hora = d.getHours();
					var minuto = d.getMinutes();
					var segundo = d.getSeconds();
					if (hora < 10) { hora = '0' + hora; }
					if (minuto < 10) { minuto = '0' + minuto; }
					if (segundo < 10) { segundo = '0' + segundo; }
					$('#horaAtual').html(hora + ':' + minuto + ':' + segundo);
				}, 1000);
			} </script></div>
EOT;
		break;	
		
	case 'srv003OutrosLogin':
		echo <<<EOT
			<div style="font-size: 32px; position: absolute; left: 50%; margin-left: -165px; width: 600px; top: 50%; margin-top: -200px;">
				<table>
					<tr>
						<td colspan="3" style="text-align: center; padding-bottom: 20px;">
							<input id="srv003Senha" type="password" value="" style="width: 100px; padding-left: 20px;" maxlength="4">
						</td>
					</tr>
					<tr>
						<td style="padding: 10px; text-align: center; font-size: 50px; font-weight: bold;"><input class="srv003Digito" type="submit" style="cursor: pointer; width: 70px; height: 70px;" class="ui-state-default ui-corner-all" value="1"></td>
						<td style="padding: 10px; text-align: center; font-size: 50px; font-weight: bold;"><input class="srv003Digito" type="submit" style="cursor: pointer; width: 70px; height: 70px;" class="ui-state-default ui-corner-all" value="2"></td>
						<td style="padding: 10px; text-align: center; font-size: 50px; font-weight: bold;"><input class="srv003Digito" type="submit" style="cursor: pointer; width: 70px; height: 70px;" class="ui-state-default ui-corner-all" value="3"></td>
					</tr>
					<tr>
						<td style="padding: 10px; text-align: center; font-size: 50px; font-weight: bold;"><input class="srv003Digito" type="submit" style="cursor: pointer; width: 70px; height: 70px;" class="ui-state-default ui-corner-all" value="4"></td>
						<td style="padding: 10px; text-align: center; font-size: 50px; font-weight: bold;"><input class="srv003Digito" type="submit" style="cursor: pointer; width: 70px; height: 70px;" class="ui-state-default ui-corner-all" value="5"></td>
						<td style="padding: 10px; text-align: center; font-size: 50px; font-weight: bold;"><input class="srv003Digito" type="submit" style="cursor: pointer; width: 70px; height: 70px;" class="ui-state-default ui-corner-all" value="6"></td>
					</tr>
					<tr>
						<td style="padding: 10px; text-align: center; font-size: 50px; font-weight: bold;"><input class="srv003Digito" type="submit" style="cursor: pointer; width: 70px; height: 70px;" class="ui-state-default ui-corner-all" value="7"></td>
						<td style="padding: 10px; text-align: center; font-size: 50px; font-weight: bold;"><input class="srv003Digito" type="submit" style="cursor: pointer; width: 70px; height: 70px;" class="ui-state-default ui-corner-all" value="8"></td>
						<td style="padding: 10px; text-align: center; font-size: 50px; font-weight: bold;"><input class="srv003Digito" type="submit" style="cursor: pointer; width: 70px; height: 70px;" class="ui-state-default ui-corner-all" value="9"></td>
					</tr>
					<tr>
						<td colspan="3" style="text-align: center; padding-top: 20px;">
							<input id="srv003Limpar" type="submit" style="cursor: pointer; padding: 10px;" class="ui-state-default ui-corner-all" value="LIMPAR">&nbsp;
							<input id="srv003Entrar" type="submit" style="cursor: pointer; padding: 10px;" class="ui-state-default ui-corner-all" value="ENTRAR">
						</td>
					</tr>
				</table>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				setInterval(function() {
					var d = new Date();
					var hora = d.getHours();
					var minuto = d.getMinutes();
					var segundo = d.getSeconds();
					if (hora < 10) { hora = '0' + hora; }
					if (minuto < 10) { minuto = '0' + minuto; }
					if (segundo < 10) { segundo = '0' + segundo; }
					$('#horaAtual').html(hora + ':' + minuto + ':' + segundo);
				}, 1000);
				$('.srv003Digito').click(function() {
					var value = $('#srv003Senha').val();
					if (value.length < 4) {
						$('#srv003Senha').val(value + $(this).val());
					}
					return false;
				});
				$('#srv003Limpar').click(function() {
					$('#srv003Senha').val('');
					return false;
				});
				$('#srv003Entrar').click(function() {
					var value = $('#srv003Senha').val();
					$('#{$g['divId']}').showLoading();
					$.getJSON('/srv/srv003/outro_func-ajax/verificaLogin/?ajax=true&senha=' + encodeURIComponent(value) + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							window.location = request.data;
							return false;
						} else {
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    						dialogConfirm("Erro!", "Código de funcionário não encontrado.<br />Por favor, tente novamente.", buttons, true)
    						$('#srv003Senha').val('');
						}
					});
					return false;
				});
			} </script></div>
EOT;
		break;
		
		case 'srv003OutrosServicoExecucao':
			// Utilizada em UPDATE, é o ID do registro que está sendo editado
			$D109_Id = (isset($r_acaoId)) ? $r_acaoId : false;
			$acaoId = (isset($_REQUEST['T147_Id'])) ? $_REQUEST['T147_Id'] : false;
			$acaoId_ = mysql_real_escape_string($acaoId);
			
			// Lista Serviço
			$sql = <<<EOT
				SELECT 
					T147_Titulo, D024_Nome_Empresa, T147_Tempo_Estimado, D001_Descricao_Produto
				FROM
					T147
				left join T005 on T005_Id=T147_T005_Id
				left join D024 on T005_D024_Id=D024_Id
				left join T006 on T006_Id=T147_T006_Id
				LEFT JOIN D009 ON D009_Id=T006_D009_Id 
				LEFT JOIN D049 ON D049_Id=D009_D049_Id 
				LEFT JOIN D001 ON D001_Id=D049_D001_Id
				WHERE T147_Id = '{$acaoId_}'
EOT;
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
			// Total de Processos
			$sql = "SELECT count(*) as total FROM T148 WHERE T148_T147_Id = '{$acaoId_}';";
			$result = mysql_query($sql);
			$processos = mysql_fetch_assoc($result);
			$processos = $processos['total'];
			// Total de Materiais
			$sql = "SELECT count(*) as total FROM T149 WHERE T149_T147_Id = '{$acaoId_}';";
			$result = mysql_query($sql);
			$materiais = mysql_fetch_assoc($result);
			$materiais = $materiais['total'];
			
			$sql = "SELECT * FROM T147 left join T005 on T005_Id=T147_T005_Id left join C007 on C007_Id=T005_C007_Id_Vendedor_Interno left join D116 on D116_Id=T147_D116_Id WHERE T147_Id='{$acaoId_}'";
			$result = mysql_query($sql);
			$T147 = mysql_fetch_assoc($result);
			$horaIni = gCorrigeData($T147['T147_Hora_Inicio']);
			$inicio = time() - strtotime($T147['T147_Hora_Inicio']);
			$segundosTotais = $inicio;
			$segundos = $inicio;			
			$horas = floor($segundos / 3600);
			$segundos -= $horas * 3600;
			$minutos = floor($segundos / 60);
			$segundos -= $minutos * 60;			
			$inicio = str_pad($horas,2, "0", STR_PAD_LEFT).':'.str_pad($minutos,2, "0", STR_PAD_LEFT).':'.str_pad($segundos,2, "0", STR_PAD_LEFT);  			

			$retorno = <<<EOT
			<table class="showInfoBig">
				<tr>
EOT;
			if($T147['T147_Pausado'] != "S"){
			$retorno .= <<<EOT
					<td class="first" colspan="2"><font color="red">SERVIÇO EM EXECUÇÃO</font></td>
EOT;
			} else {
			$retorno .= <<<EOT
					<td class="first" colspan="2"><font color="red">SERVIÇO PAUSADO</font></td>
EOT;
			}

			$retorno .= <<<EOT
					<td class="second"></td>
				</tr>
			
				<tr>
					<td class="first">Serviço:</td>
					<td class="second">{$row['T147_Titulo']}</td>
				</tr>
				<tr>
					<td class="first">Produto:</td>
					<td class="second">{$row['D001_Descricao_Produto']}</td>
				</tr>
				<tr>
					<td class="first">Vendedor:</td>
					<td class="second">{$T147['C007_Primeiro_Nome']}</td>
				</tr>
				<tr>
					<td class="first">Cliente:</td>
					<td class="second">{$row['D024_Nome_Empresa']}</td>
				</tr>
				<tr>
					<td class="first">Veículo:</td>
					<td class="second">{$T147['D116_Placa']} - {$T147['D116_Modelo']} - {$T147['D116_Cor']}</td>
				</tr>
				<tr>
					<td class="first">Hora Início:</td>
					<td class="second">{$horaIni}</td>
				</tr>
				<tr>
					<td class="first">Tempo Estimado:</td>
					<td class="second">{$row['T147_Tempo_Estimado']}</td>
				</tr>
				<tr>
					<td class="first">Tempo Decorido:</td>
					<td class="second"><span id="srv003HoraInicio">{$inicio}</span><span id="srv003HoraInicioHidden" style="display:none">{$segundosTotais}</span></td>
				</tr>
EOT;
				// <tr>
					// <td class="first">Processos:</td>
					// <td class="second">Este serviço possui {$processos} processos. <span class="buttonsBottom-big"><button type="submit" id="srv003VerProcessos">Ver Processos</button></span></td>
				// </tr>
				// <tr>
					// <td class="first">Materiais:</td>
					// <td class="second">Exitem {$materiais} materiais envolvidos neste serviço. <span class="buttonsBottom-big"><button type="submit" id="srv003VerMateriais">Ver Materiais</button></span></td>
				// </tr>
				$retorno .=<<<EOT
				
			</table>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#srv003VerProcessos').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Processos', '/srv/srv003/content/contentProcessosServicos/', '&T147_Id={$acaoId_}', [900,550]);
				});
				$('#srv003VerMateriais').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Materiais', '/srv/srv003/content/contentMateriaisServicos/', '&T147_Id={$acaoId_}', [900,550]);
				});
				
				setInterval(function() {
					var secs = $('#srv003HoraInicioHidden').html();
				    var hours = Math.floor(secs / (60 * 60));				   
				    var divisor_for_minutes = secs % (60 * 60);
				    var minutes = Math.floor(divisor_for_minutes / 60);				 
				    var divisor_for_seconds = divisor_for_minutes % 60;
				    var seconds = Math.ceil(divisor_for_seconds);				    					
					$('#srv003HoraInicio').html(str_pad(hours,2,'0','STR_PAD_LEFT')+":"+str_pad(minutes,2,'0','STR_PAD_LEFT')+":"+str_pad(seconds,2,'0','STR_PAD_LEFT'));
					$('#srv003HoraInicioHidden').html(parseInt(secs)+1);
				}, 1000);
				
			} 
			
			function str_pad (input, pad_length, pad_string, pad_type) {
			    // Returns input string padded on the left or right to specified length with pad_string
			    //
			    // version: 1009.2513
			    // discuss at: http://phpjs.org/functions/str_pad
			    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
			    // + namespaced by: Michael White (http://getsprink.com)
			    // +      input by: Marco van Oort
			    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
			    // *     example 1: str_pad('Kevin van Zonneveld', 30, '-=', 'STR_PAD_LEFT');
			    // *     returns 1: '-=-=-=-=-=-Kevin van Zonneveld'
			    // *     example 2: str_pad('Kevin van Zonneveld', 30, '-', 'STR_PAD_BOTH');
			    // *     returns 2: '------Kevin van Zonneveld-----'
			    var half = '', pad_to_go;
			 
			    var str_pad_repeater = function (s, len) {
			        var collect = '', i;
			 
			        while (collect.length < len) {collect += s;}         collect = collect.substr(0,len);           return collect;     };       input += '';     pad_string = pad_string !== undefined ? pad_string : ' ';          if (pad_type != 'STR_PAD_LEFT' && pad_type != 'STR_PAD_RIGHT' && pad_type != 'STR_PAD_BOTH') { pad_type = 'STR_PAD_RIGHT'; }     if ((pad_to_go = pad_length - input.length) > 0) {
			        if (pad_type == 'STR_PAD_LEFT') { input = str_pad_repeater(pad_string, pad_to_go) + input; }
			        else if (pad_type == 'STR_PAD_RIGHT') { input = input + str_pad_repeater(pad_string, pad_to_go); }
			        else if (pad_type == 'STR_PAD_BOTH') {
			            half = str_pad_repeater(pad_string, Math.ceil(pad_to_go/2));
			            input = half + input + half;
			            input = input.substr(0, pad_length);
			        }
			    }
			 
			    return input;
			}			
			
			</script></div>
EOT;
			
			//Verificar se o serviço está pausado ou não para exibir o botão correto
			
			/**
			* Botões
			*/
			// Definição dos botões
			$botoesEsq = '';
			$botoesDir = '';
			//$botoesEsq .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Voltar</button>';
															  
			$botoesEsq .= '<button class="btnFechar" onclick="window.location=\'/srv/srv003/content/srv003ContentLogin/?oneWindow=true\'; return false;">Sair</button>';
			$botoesEsq .= '<button class="btnFechar" onclick="window.location=\'/srv/srv003/content/srv003ContentOperacao/?oneWindow=true&acaoId='.$r_acaoId.'\'; return false;">Voltar</button>';
			$botoesEsq .= '<button type="submit" id="srv003OutrosServicoImprimir">Imprimir</button>';
			$botoesDir .= '<button type="submit" id="srv003OutrosServicoConcluir">Concluir</button>';
			if($T147['T147_Pausado'] == "S"){
				$botoesDir .= '<button type="submit" id="srv003OutrosServicoReiniciar">Reiniciar</button>';
			} else {
				$botoesDir .= '<button type="submit" id="srv003OutrosServicoPausar">Pausar</button>';
			}
			$botoesDir .= '<button type="submit" id="srv003OutrosServicoCancelar">Cancelar Execução</button>';
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesDir', $botoesDir);
			echo  $g['smarty']->fetch('big.buttons-floatBottom.tpl');
			// Ações dos botões
			// A ação do botão Enviar é definido dentro do formulário
			
			//LINHA PARA DIRECIONAR PARA O LOGIN APÓS UMA OPERAÇÃO
			//window.location = "/srv/srv003/content/srv003ContentLogin/?oneWindow=true"; 
			
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#srv003OutrosServicoImprimir').unbind('click').bind('click', function(data) {
						$.getJSON('/srv/srv003/outro_func-ajax/ImprimirServico/?ajax=true&T147_Id=' + encodeURIComponent("{$acaoId_}") + '&D109_Id=' + encodeURIComponent("{$D109_Id}") + '&callback=?', function(request) {
							if (request.code) {
								var \$buttons = { 
									"Fechar": function() { 
										$(this).dialog("destroy").remove(); 
									} 
								}
					    		dialogConfirm("Impressão", "Por favor, retire a ordem de serviço impressa.", \$buttons, true);
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    		dialogConfirm('Erro', request.data, \$buttons, true);
							}
						});
					});
					$('#srv003OutrosServicoPausar').unbind('click').bind('click', function(data) {
						$.getJSON('/srv/srv003/outro_func-ajax/pausarServico/?ajax=true&T147_Id=' + encodeURIComponent("{$acaoId_}") + '&D109_Id=' + encodeURIComponent("{$D109_Id}") + '&Usuario=Funcionario' + '&callback=?', function(request) {
							if (request.code) {
								var \$buttons = { 
									"Fechar": function() {
										window.location = "/srv/srv003/content/srv003ContentOperacao/?oneWindow=true&acaoId=" + encodeURIComponent('{$D109_Id}') + "&T147_Id=" + encodeURIComponent('{$acaoId_}');  
										$(this).dialog("destroy").remove();
									} 
								}
					    		dialogConfirm("Serviço Pausado", "Serviço Pausado com sucesso", \$buttons, true);
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    		dialogConfirm('Erro', request.data, \$buttons, true);
							}
						});
					});
					
					$('#srv003OutrosServicoReiniciar').unbind('click').bind('click', function(data) {
						$.getJSON('/srv/srv003/outro_func-ajax/reiniciarServico/?ajax=true&T147_Id=' + encodeURIComponent("{$acaoId_}") + '&D109_Id=' + encodeURIComponent("{$D109_Id}") + '&Usuario=Funcionario' + '&callback=?', function(request) {
							if (request.code) {
								var \$buttons = { 
									"Fechar": function() {
										window.location = "/srv/srv003/content/srv003ContentOperacao/?oneWindow=true&acaoId=" + encodeURIComponent('{$D109_Id}') + "&T147_Id=" + encodeURIComponent('{$acaoId_}');
										$(this).dialog("destroy").remove();
									} 
								}
					    		dialogConfirm("Serviço Reiniciado", "Serviço reiniciado com sucesso", \$buttons, true);
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    		dialogConfirm('Erro', request.data, \$buttons, true);
							}
						});
					});
					
					$('#srv003OutrosServicoConcluir').unbind('click').bind('click', function(data) {
						$.getJSON('/srv/srv003/outro_func-ajax/concluirServico/?ajax=true&T147_Id=' + encodeURIComponent("{$acaoId_}") + '&D109_Id=' + encodeURIComponent("{$D109_Id}") + '&Usuario=Funcionario' + '&callback=?', function(request) {
							if (request.code) {
								var \$buttons = { 
									"Fechar": function() {
										window.location = "/srv/srv003/content/srv003ContentOperacao/?oneWindow=true&acaoId=" + encodeURIComponent('{$D109_Id}') + "&T147_Id=" + encodeURIComponent('{$acaoId_}');
										$(this).dialog("destroy").remove(); 
									} 
								}
					    		dialogConfirm("Serviço Concluído", "Serviço concluído com sucesso", \$buttons, true);
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    		dialogConfirm('Erro', request.data, \$buttons, true);
							}
						});
					});
					
					$('#srv003OutrosServicoCancelar').unbind('click').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();
								$(this).dialog("destroy").remove();
								$.getJSON('/srv/srv003/outro_func-ajax/cancelarServico/?ajax=true&T147_Id=' + encodeURIComponent("{$acaoId_}") + '&D109_Id=' + encodeURIComponent("{$D109_Id}") + '&Usuario=Funcionario' + '&callback=?', function(request) {
									$('#{$g['divId']}').hideLoading();
									if (request.code) {
										var \$buttons = {
											 "Fechar": function() {
											 	window.location = "/srv/srv003/content/srv003ContentOperacaoPendentes/?oneWindow=true&acaoId=" + encodeURIComponent('{$D109_Id}') + "&T147_Id=" + encodeURIComponent('{$acaoId_}');
												$(this).dialog("destroy").remove();
											 } 
										};
							    		dialogConfirm("Cancelamento do Serviço", "Execução Cancelada com Sucesso.", \$buttons, true);
									} else {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							    		dialogConfirm('Erro', request.data, \$buttons);
									}
								});
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						dialogConfirm('Cancelar Serviço', 'Você tem certeza que deseja cancelar a execução do serviço? ', \$buttons);
					});
				} </script></div>
EOT;
	echo $retorno;
	break;
	
	case 'srv003OutrosServicoAprovar':
			// Utilizada em UPDATE, é o ID do registro que está sendo editado
			$D109_Id = (isset($r_acaoId)) ? $r_acaoId : false;
			$acaoId = (isset($_REQUEST['T147_Id'])) ? $_REQUEST['T147_Id'] : false;
			$acaoId_ = mysql_real_escape_string($acaoId);
			
			// Lista Serviço
			$sql = <<<EOT
				SELECT 
					*
				FROM
					T147
				left join T005 on T005_Id=T147_T005_Id
				left join C007 on C007_Id=T005_C007_Id_Vendedor_Interno
				left join D024 on T005_D024_Id=D024_Id
				left join D116 on D116_Id=T147_D116_Id
				left join T006 on T006_Id=T147_T006_Id
				LEFT JOIN D009 ON D009_Id=T006_D009_Id 
				LEFT JOIN D049 ON D049_Id=D009_D049_Id 
				LEFT JOIN D001 ON D001_Id=D049_D001_Id
				WHERE T147_Id = '{$acaoId_}'
EOT;
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
			// Total de Processos
			$sql = "SELECT count(*) as total FROM T148 WHERE T148_T147_Id = '{$acaoId_}';";
			$result = mysql_query($sql);
			$processos = mysql_fetch_assoc($result);
			$processos = $processos['total'];
			// Total de Materiais
			$sql = "SELECT count(*) as total FROM T149 WHERE T149_T147_Id = '{$acaoId_}';";
			$result = mysql_query($sql);
			$materiais = mysql_fetch_assoc($result);
			$materiais = $materiais['total'];
			$horaFim = gCorrigeData($row['T147_Hora_Fim']);
			
			// Selecionando os dados do funcionário
			$sqlD109 = "SELECT * FROM D109 WHERE D109_Id='{$row['T147_D109_Id']}'";
			$resultD109 = mysql_query($sqlD109);
			$linhaD109 = mysql_fetch_array($resultD109);
			
			$retorno =  <<<EOT
			<table class="showInfoBig">
				<tr>
					<td class="first">Serviço:</td>
					<td class="second">{$row['T147_Titulo']}</td>
				</tr>
				<tr>
					<td class="first">Produto:</td>
					<td class="second">{$row['D001_Descricao_Produto']}</td>
				</tr>
				<tr>
					<td class="first">Cliente:</td>
					<td class="second">{$row['D024_Nome_Empresa']}</td>
				</tr>
				<tr>
					<td class="first">Vendedor:</td>
					<td class="second">{$row['C007_Primeiro_Nome']}</td>
				</tr>
				<tr>
					<td class="first">Veículo:</td>
					<td class="second">{$row['D116_Placa']} {$row['D116_Modelo']} {$row['D116_Cor']}</td>
				</tr>
				<tr>
					<td class="first">Funcionário:</td>
					<td class="second">{$linhaD109['D109_Nome']}</td>
				</tr>
				<tr>
					<td class="first">Tempo Estimado:</td>
					<td class="second">{$row['T147_Tempo_Estimado']}</td>
				</tr>
				<tr>
					<td class="first">Hora Conclusão:</td>
					<td class="second">{$horaFim}</td>
				</tr>
EOT;
				// <tr>
					// <td class="first">Processos:</td>
					// <td class="second">Este serviço possui {$processos} processos. <span class="buttonsBottom-big"><button type="submit" id="srv003VerProcessos">Ver Processos</button></span></td>
				// </tr>
				// <tr>
					// <td class="first">Materiais:</td>
					// <td class="second">Exitem {$materiais} materiais envolvidos neste serviço. <span class="buttonsBottom-big"><button type="submit" id="srv003VerMateriais">Ver Materiais</button></span></td>
				// </tr>
			$retorno .= <<<EOT
			</table>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#srv003VerProcessos').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Processos', '/srv/srv003/content/contentProcessosServicos/', '&T147_Id={$acaoId_}', [900,550]);
				});
				$('#srv003VerMateriais').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Materiais', '/srv/srv003/content/contentMateriaisServicos/', '&T147_Id={$acaoId_}', [900,550]);
				});
			} </script></div>
EOT;
			echo $retorno;
			
			/**
			* Botões
			*/
			// Definição dos botões
			$botoesEsq = '';
			$botoesDir = '';
			//$botoesEsq .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Voltar</button>';
			$botoesEsq .= '<button class="btnFechar" onclick="window.location=\'/srv/srv003/content/srv003ContentOperacao/?oneWindow=true&acaoId='.$r_acaoId.'\'; return false;">Voltar</button>';
			$botoesDir .= '<button type="submit" id="srv003OutrosServicoAprovar">Aprovado</button>';
			$botoesDir .= '<button type="submit" id="srv003OutrosServicoReprovar">Reprovado</button>';
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesDir', $botoesDir);
			echo  $g['smarty']->fetch('big.buttons-floatBottom.tpl');
			// Ações dos botões
			// A ação do botão Enviar é definido dentro do formulário
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#srv003OutrosServicoAprovar').unbind('click').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();
								$(this).dialog("destroy").remove();
								$.getJSON('/srv/srv003/outro_func-ajax/aprovarServico/?ajax=true&T147_Id=' + encodeURIComponent("{$acaoId_}") + '&D109_Id=' + encodeURIComponent("{$D109_Id}") + '&Usuario=Funcionario' + '&callback=?', function(request) {
									$('#{$g['divId']}').hideLoading();
									if (request.code) {
										var \$buttons = {
											 "Fechar": function() {
											 	$(this).dialog("destroy").remove();
											 	fecharJanela('{$g['divId']}');
											 	window.location = "/srv/srv003/content/srv003ContentOperacaoAprovar/?oneWindow=true&acaoId=" + encodeURIComponent('{$D109_Id}') + "&T147_Id=" + encodeURIComponent('{$acaoId_}');
											 	divRefresh('{$g['divIdParent']}', true);
											 } 
										};
							    		dialogConfirm('Ok', request.data, \$buttons);
									} else {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							    		dialogConfirm('Erro', request.data, \$buttons);
									}
								});
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						dialogConfirm('Aprovar serviço', 'Você tem certeza que deseja aprovar o serviço? ', \$buttons, true);
					});
					$('#srv003OutrosServicoReprovar').unbind('click').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();
								$(this).dialog("destroy").remove();
								$.getJSON('/srv/srv003/outro_func-ajax/reprovarServico/?ajax=true&T147_Id=' + encodeURIComponent("{$acaoId_}") + '&D109_Id=' + encodeURIComponent("{$D109_Id}") + '&Usuario=Funcionario' + '&callback=?', function(request) {
									$('#{$g['divId']}').hideLoading();
									if (request.code) {
										var \$buttons = {
											 "Fechar": function() {
											 	$(this).dialog("destroy").remove();
											 	fecharJanela('{$g['divId']}');
											 	window.location = "/srv/srv003/content/srv003ContentOperacaoAprovar/?oneWindow=true&acaoId=" + encodeURIComponent('{$D109_Id}') + "&T147_Id=" + encodeURIComponent('{$acaoId_}');
											 	divRefresh('{$g['divIdParent']}', true);
											 } 
										};
							    		dialogConfirm('Ok', request.data, \$buttons);
									} else {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							    		dialogConfirm('Erro', request.data, \$buttons);
									}
								});
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						dialogConfirm('Aprovar serviço', 'Você tem certeza que deseja reprovar o serviço? ', \$buttons, true);
					});
				} </script></div>
EOT;
	break;
		
	}
?>

