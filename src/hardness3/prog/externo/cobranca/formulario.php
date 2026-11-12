<?php
namespace hardness;
global $g,$confUsuario;

switch($g['r'][3]){
	case 'form':
		global $g, $sys;
		//-- CONEXAO DIRETA COM O BANCO DE DADOS
		$con =@mysqli_connect($sys['dbHost'], $sys['dbUser'], $sys['dbPass'],$sys['dbDatabase']);
		$g['conexaoBanco'] = $con;

		// $dbs =@mysql_select_db($sys['dbDatabase'], $con) or die (mysql_error());
		$sqlT117 = mysql_query("SELECT *, DATEDIFF(CURDATE(), T002_Data_Vencimento) as diasVencido FROM T117 LEFT JOIN D007 ON D007_Id = T117_D007_Id LEFT JOIN T002 ON T002_T117_Id = T117_Id WHERE T117_Id = '{$_REQUEST['id']}'");
		$T117 = mysql_fetch_assoc($sqlT117);

		$diasVencido = $T117['diasVencido'];
		$dataVencimentoOriginal = gCorrigeData($T117['T002_Data_Vencimento']);
		$vencimento = date("d/m/Y");
		$valor = gCorrigeNumero($T117['T002_Valor_Total']);
		$juros = gCorrigeNumero((($T117['T117_Percentual_Juros_Dia']/30)/100)*$T117['T002_Valor_Total']);
		$multa = gCorrigeNumero(($T117['T117_Percentual_Multa']/100)*$T117['T002_Valor_Total']);
		$percentualJuros = $T117['T117_Percentual_Juros_Dia'];
		$percentualMulta = $T117['T117_Percentual_Multa'];
		echo <<<EOT
			<div class="formulario" style="width: 515px; height: 150px; float: left; border: 1px solid #AAA; border-radius: 3px; margin: 2px 5px 15px 15px; background-color: #FFF; font-family: Arial; font-size: 13px; padding: 20px;">
				<div style="width: 57%; float: left;">
					<div style="z-index: 999999999999; text-align: right;">
					<form id="form-event">
						<input type="hidden" id="id" value="{$_REQUEST['id']}" />
						<label for="vencimento">Novo Vencimento</label>
						<input type="text" id="vencimento" value="{$vencimento}" class="text ui-widget-content ui-corner-all campo_input_data" name="vencimento" style="background: white; margin-bottom: 5px; color:black" readonly /><br />
						
						<label for="valor">Valor Original</label>
						<input type="text" id="valor" name="valor" value="{$valor}" readonly class="text ui-widget-content ui-corner-all" style="margin-bottom: 5px;" /><br />

						<label for="vencimentoOriginal">Vencimento Original</label>
						<input type="text" id="vencimentoOriginal" name="vencimentoOriginal" value="{$dataVencimentoOriginal}" readonly class="text ui-widget-content ui-corner-all" style="margin-bottom: 5px;" /><br />
						
						<label for="multa">Multa</label>
						<input type="text" id="multa" name="multa" value="{$multa}" readonly class="text ui-widget-content ui-corner-all" style="margin-bottom: 5px;" /><br />
						
						<label for="juros">Juros Diario</label>
						<input type="text" id="juros" name="juros" value="{$juros}" readonly class="text ui-widget-content ui-corner-all" style="margin-bottom: 5px;" />
						<input id="calcular" type="button" value="Calcular" class="ui-button ui-widget ui-state-default ui-corner-all" style=" margin-top: 10px;" />
					</form>
					</div>
					<input id="percentualMulta" type="hidden" value="{$percentualMulta}">
					<input id="percentualJuros" type="hidden" value="{$percentualJuros}">
					<input id="diasVencido" type="hidden" value="{$diasVencido}">
				</div>
				<div style="width: 43%; float: left; padding-top: 30px;" align="center">
					<span id="valorCalculado" style="font-size: 22px; font-family: Arial; font-weight: bold; margin-top: 20px;"></span><br />
					<input id="atualizarVencimento" type="button" value="Gerar novo boleto" class="ui-button ui-widget ui-state-default ui-corner-all" style="display: none; margin-top: 20px;" /><br />
				</div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#vencimento').datepicker({dateFormat: 'dd/mm/yy', minDate: 0, maxDate: 0});
				var dataInput = $(".campo_input_data");
				dataInput.datepicker( "option", "dayNamesMin", ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'] );
				dataInput.datepicker( "option", "monthNames", ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'] );
				
				function dialogConfirm(titulo, texto, buttons) {
					var novoId = unique();
					var confirmacao = $("<div>")
						.addClass('dialogConfirm')
						.attr({'id': 'dc-' + novoId})
						.appendTo('body');
					$("<p>").html(texto).appendTo(confirmacao);
					$("#dc-" + novoId).dialog({
						width: 'auto',
						height: 'auto',
						minWidth: 250,
						resizable: false,
						modal: true,
						title: titulo,
						buttons: buttons,
						zIndex: 9999
					});
					$("#dc-" + novoId).parent().find('.ui-dialog-titlebar a').remove();
					return novoId;
				}
				
				$('#calcular').click(function(){
					var valorBoleto = formataValor($('#valor').val());
					var jurosDiario = ( ( ($('#percentualJuros').val()/30)/100)*valorBoleto);
					var multaVencimento = ( ( $('#percentualMulta').val()/100)*valorBoleto);
					var dataVencimento = formataData($('#vencimentoOriginal').val());
					var dataPagamento = formataData($('#vencimento').val());

					valorBoleto = parseFloat(valorBoleto);
					jurosDiario = parseFloat(jurosDiario);
					multaVencimento = parseFloat(multaVencimento);
					//var diasVencido = quantidadeDias(dataPagamento, dataVencimento);
					var diasVencido = $('#diasVencido').val();
					if(multaVencimento > 0 && dataPagamento > dataVencimento){
						valorBoleto += multaVencimento;
					}
					if(jurosDiario > 0 && diasVencido> 0){
						jurosDiario = jurosDiario.toFixed(2);
						var jurosDiarioTotal = jurosDiario*diasVencido;
						valorBoleto += jurosDiarioTotal;
					}
					$('#valorCalculado').html('Valor atualizado<br /> R$ '+ number_format(valorBoleto, 2, ',', '.'));
					$('#atualizarVencimento').css('display', 'block');
				});

				$('#vencimento').change(function() {
					$('#valorCalculado').html('');
					$('#atualizarVencimento').css('display', 'none');
				});

				$('#xatualizarVencimento').click(function(){
					var \$buttons = {
						"Atualizar": function() { 
							var formulario = $("#form-event").serialize();
							$.ajax({
								url: '/externo/cobranca/formulario/event/?ajax=true&noEmp=true&u=1&p=1&id=' + encodeURIComponent('{$_REQUEST['id']}') + '&a=' + encodeURIComponent('{$_REQUEST['a']}'),
								dataType: 'html',
								type: 'post',
								data: formulario,
								success: function(data) {
									$('.formulario').remove();
									var \$buttons = {
										"Imprimir Boleto": function() { 
											window.open(data,'Boleto','scrollbars=auto,width=800,height=700');
											document.location.reload(true);
										},
										"Fechar": function() { 
											document.location.reload(true);
										}
									};
									dialogConfirm("Geração de Boleto", "Boleto gerado com sucesso!", \$buttons);
								}
							});
						},
						"Fechar": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm("Geração de Boleto", "Deseja Atualizar o Boleto?", \$buttons);
				});

				$('#atualizarVencimento').click(function(){
					var \$buttons = {
						"Atualizar": function() { 
							var formulario = $("#form-event").serialize();
							$.ajax({
								url: '/externo/cobranca/formulario/event/?ajax=true&noEmp=true&u=1&p=1&id=' + encodeURIComponent('{$_REQUEST['id']}') + '&a=' + encodeURIComponent('{$_REQUEST['a']}'),
								dataType: 'html',
								type: 'post',
								data: formulario,
								success: function(request) {
									request = JSON.parse(request);
									$('.formulario').remove();
									if(request[0]){
										var \$buttons = {
											"Imprimir Boleto": function() { 
												window.open(request[1],'Boleto','scrollbars=auto,width=800,height=700');
												document.location.reload(true);
											},
											"Fechar": function() { 
												document.location.reload(true);
											}
										};
										dialogConfirm("Geração de Boleto", "Boleto gerado com sucesso!", \$buttons);
									}else{
										var \$buttons = {
											"Fechar": function() { 
												document.location.reload(true);
											}
										};
										dialogConfirm("Atualização", "<font style='font-size:13px;'>Erro na atualização do boleto:<br><br> " + request[1] + "</font>", \$buttons);
									}									
								}
							});
						},
						"Fechar": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm("Geração de Boleto", "Deseja Atualizar o Boleto?", \$buttons);
				});

				function formataData(data) {
					var novaData = data.split("/");
					return novaData[2] + "-" + novaData[1] + "-" + novaData[0];
				}

				function quantidadeDias(dataInicial, dataFinal){
					var oneDay = 24*60*60*1000;
					var inicial = dataInicial.split("-");
					var final = dataFinal.split("-");
					var firstDate = new Date(inicial[0],inicial[1],inicial[2]);
					var secondDate = new Date(final[0],final[1],final[2]);
					return Math.round(Math.abs((firstDate.getTime() - secondDate.getTime())/(oneDay)));
				}

				function formataValor(e){e=e.replace(".","");e=e.replace(",",".");return e}
				function number_format(e,t,n,r){var i=e,s=t;i=!isFinite(+i)?0:+i;s=!isFinite(+s)?0:Math.abs(s);var o=typeof r=="undefined"?",":r;var u=typeof n=="undefined"?".":n;var a=s>0?i.toFixed(s):Math.round(i).toFixed(s);var f=Math.abs(i).toFixed(s);var l,c;if(f>=1e3){l=f.split(/\D/);c=l[0].length%3||3;l[0]=a.slice(0,c+(i<0))+l[0].slice(c).replace(/(\d{3})/g,o+"$1");a=l.join(u)}else{a=a.replace(".",u)}return a}
				function unique(){var e="";var t="0123456789abcdefghijklmnopqurstuvwxyzABCDEFGHIJKLMNOPQURSTUVWXYZ";for(var n=0;n<13;n++){e+=t.substr(Math.floor(Math.random()*62),1)}return e}
			}</script></div>
EOT;
	break;

	case 'xevent':
		$D024_Id = trim($_REQUEST['a']);
		
		require_once('bibliotecas/classes/FIN005.php');
		$FIN005 = new FIN005();
		require_once('bibliotecas/classes/GFormWrap.php');
		
		$T117 = mysql_fetch_assoc(mysql_query("SELECT T117_Percentual_Multa,T117_Percentual_Juros_Dia,T117_Valor_Total FROM T117 WHERE T117_Id = '{$_REQUEST['id']}' AND md5(T117_D024_Id) = '{$D024_Id}'"));
		$array['gerarJurosMulta'] = 'S';
		$array['T117_Percentual_Multa'] = gCorrigeNumero($T117['T117_Percentual_Multa']);
		$array['T117_Percentual_Juros_Dia'] = gCorrigeNumero($T117['T117_Percentual_Juros_Dia']);
		$array['T117_Data_Vencimento'] = $_POST['vencimento'];
		//$array['T117_Data_Vencimento'] = strtotime("+1 day", strtotime(gCorrigeDataInverte($_POST['vencimento'])));
		//$array['T117_Data_Vencimento'] = date("d/m/Y", $array['T117_Data_Vencimento']);
		//$array['T117_Valor_Total'] = 0; //Não deve ser utilizado - se enviado valor maior do que ZERO, este será o valor do boleto
		$array['T117_Valor_Total'] = gCorrigeNumero($T117['T117_Valor_Total']);
		$array['T117_Id'] = $_REQUEST['id'];

		$formulario = new GFormWrap($array);
		$retorno = $FIN005->atualizarVencimentoBoleto($formulario, true);

		if($retorno === true){
			//echo "Boleto atualizado com sucesso!";
			$T117 = mysql_fetch_assoc(mysql_query("SELECT * FROM T117 WHERE T117_Id = '{$g['mysqlLastId']}'"));
			$cQuery=md5($T117['T117_Id'].$T117['T117_D024_Id']);
			$path=base64_encode($confUsuario['urlRaiz'] . $g['pathWeb'] . $g['pathDados']);
			$db = md5($confUsuario['dbDatabase']);
			echo $confUsuario['urlRaiz']."/hardness3/prog/externo/cobranca/gerar_boleto_empresa.php?cQuery=".$cQuery."&path=".$path."&query=".$db."&buscarBoletoBanco=suporte";
		}

	break;

	case 'event':
		global $sys,$confUsuario,$g;
		$resposta = array(false,"Erro não definido, contate suporte técnico.");
		$D024_Id = trim($_REQUEST['a']);
		
		require_once('bibliotecas/classes/FIN005.php');
		$FIN005 = new FIN005();
		require_once('bibliotecas/classes/GFormWrap.php');
			
		$T117 = mysql_fetch_assoc(mysql_query("SELECT T117_Percentual_Multa,T117_Percentual_Juros_Dia,T117_Valor_Total FROM T117 WHERE T117_Id = '{$_REQUEST['id']}' AND md5(T117_D024_Id) = '{$D024_Id}'"));
		$array['gerarJurosMulta'] = 'S';
		$array['T117_Percentual_Multa'] = gCorrigeNumero($T117['T117_Percentual_Multa']);
		$array['T117_Percentual_Juros_Dia'] = gCorrigeNumero($T117['T117_Percentual_Juros_Dia']);
		$array['T117_Data_Vencimento'] = $_POST['vencimento'];
		$array['T117_Data_Emissao'] = date("d/m/Y");
		$array['T117_Valor_Total'] = gCorrigeNumero($T117['T117_Valor_Total']);
		$array['T117_Id'] = $_REQUEST['id'];
		$array['atualizarValor'] = "2";

		$formulario = new GFormWrap($array);
		$retorno = $FIN005->atualizarVencimentoBoleto($formulario, true);
		if($retorno === true){
			$T117 = mysql_fetch_assoc(mysql_query("SELECT * FROM T117 WHERE T117_Id = '{$g['mysqlLastId']}'"));
			$cQuery=md5($T117['T117_Id'].$T117['T117_D024_Id']);
			$path=base64_encode($confUsuario['urlRaiz'] . $g['pathWeb'] . $g['pathDados']);
			$db = md5($confUsuario['dbDatabase']);
			$resposta[0] = true;
        	$resposta[1] = $confUsuario['urlRaiz']."/hardness3/prog/externo/cobranca/gerar_boleto_empresa.php?cQuery=".$cQuery."&path=".$path."&query=".$db."&buscarBoletoBanco=suporte";
		}else{
			$resposta[0] = false;
        	$resposta[1] = $retorno;
		}
		echo json_encode($resposta);

	break;
}