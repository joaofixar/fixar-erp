<?php
namespace hardness;
switch ($g['r'][3]) {
	case 'cad010OutrosCadastrarDigital':
		echo <<<EOT
			<div style="font-size: 32px; position: absolute; left: 50%; margin-left: -165px; width: 600px; top: 50%; margin-top: -200px;">
				<table>
					<tr>
						<td colspan="3" style="text-align: center; padding-bottom: 20px;">
							<input id="cad010Senha" type="password" value="" style="width: 100px; padding-left: 20px;" maxlength="4">
						</td>
					</tr>
					<tr>
						<td style="padding: 10px; text-align: center; font-size: 50px; font-weight: bold;"><input class="cad010Digito" type="submit" style="cursor: pointer; width: 70px; height: 70px;" class="ui-state-default ui-corner-all" value="1"></td>
						<td style="padding: 10px; text-align: center; font-size: 50px; font-weight: bold;"><input class="cad010Digito" type="submit" style="cursor: pointer; width: 70px; height: 70px;" class="ui-state-default ui-corner-all" value="2"></td>
						<td style="padding: 10px; text-align: center; font-size: 50px; font-weight: bold;"><input class="cad010Digito" type="submit" style="cursor: pointer; width: 70px; height: 70px;" class="ui-state-default ui-corner-all" value="3"></td>
					</tr>
					<tr>
						<td style="padding: 10px; text-align: center; font-size: 50px; font-weight: bold;"><input class="cad010Digito" type="submit" style="cursor: pointer; width: 70px; height: 70px;" class="ui-state-default ui-corner-all" value="4"></td>
						<td style="padding: 10px; text-align: center; font-size: 50px; font-weight: bold;"><input class="cad010Digito" type="submit" style="cursor: pointer; width: 70px; height: 70px;" class="ui-state-default ui-corner-all" value="5"></td>
						<td style="padding: 10px; text-align: center; font-size: 50px; font-weight: bold;"><input class="cad010Digito" type="submit" style="cursor: pointer; width: 70px; height: 70px;" class="ui-state-default ui-corner-all" value="6"></td>
					</tr>
					<tr>
						<td style="padding: 10px; text-align: center; font-size: 50px; font-weight: bold;"><input class="cad010Digito" type="submit" style="cursor: pointer; width: 70px; height: 70px;" class="ui-state-default ui-corner-all" value="7"></td>
						<td style="padding: 10px; text-align: center; font-size: 50px; font-weight: bold;"><input class="cad010Digito" type="submit" style="cursor: pointer; width: 70px; height: 70px;" class="ui-state-default ui-corner-all" value="8"></td>
						<td style="padding: 10px; text-align: center; font-size: 50px; font-weight: bold;"><input class="cad010Digito" type="submit" style="cursor: pointer; width: 70px; height: 70px;" class="ui-state-default ui-corner-all" value="9"></td>
					</tr>
					<tr>
						<td colspan="3" style="text-align: center; padding-top: 20px;">
							<input id="cad010Limpar" type="submit" style="cursor: pointer; padding: 10px;" class="ui-state-default ui-corner-all" value="LIMPAR">&nbsp;
							<input id="cad010Entrar" type="submit" style="cursor: pointer; padding: 10px;" class="ui-state-default ui-corner-all" value="ENTRAR">
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
				$('.cad010Digito').click(function() {
					var value = $('#cad010Senha').val();
					if (value.length < 4) {
						$('#cad010Senha').val(value + $(this).val());
					}
					return false;
				});
				$('#cad010Limpar').click(function() {
					$('#cad010Senha').val('');
					return false;
				});
				$('#cad010Entrar').click(function() {
					var value = $('#cad010Senha').val();
					$('#cad010Senha').val('');
					$('#{$g['divId']}').showLoading();
					$.getJSON('/cad/cad010/outro_func-ajax/verificaLogin/?ajax=true&senha=' + encodeURIComponent(value) + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							var \$buttons = {
								"Sim": function() {
									cad010CadastrarDigital(request.D109_Id);
									$(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); } 
								};
								dialogConfirm('Cadastro de Digital', request.data, \$buttons, true);
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    			dialogConfirm('Erro', request.data, \$buttons, true);
							}
					});
					return false;
				});
EOT;
			echo <<<'EOT'
				function cad010CadastrarDigital(D109_Id){
					netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
					var exe = window.Components.classes['@mozilla.org/file/local;1'].createInstance(Components.interfaces.nsILocalFile);
					exe.initWithPath("C:\\hardness\\hardnessCadDigital.exe");
					var run = window.Components.classes['@mozilla.org/process/util;1'].createInstance(Components.interfaces.nsIProcess);
					run.init(exe);
					var parameters = [D109_Id];
					run.run(false, parameters, parameters.length);
				}
			} </script></div>
EOT;
		break;		
	}
?>

