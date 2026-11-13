<div class="divHidden">
	<script type="text/javascript"> if (typeof jQuery != 'undefined') {
		function alteraEstado(elem, divId) {
		    $('#' + divId).showLoading();
		    $.getJSON('/fis/fis012/form_func-ajax/alteraEstado/?ajax=true&T284_D018_Id=' + encodeURIComponent($('#T284_D018_Id').val()) + '&callback=?', function(request) {
		        $('#' + divId).hideLoading();
				if (request.code) {
					$('#T284_D020_Id').html(request.data['T284_D020_Id']);
					$('#' + divId + ' #T284_Cidade_Descarregamento_Codigo_IBGE').val(request.data['T284_Cidade_Descarregamento_Codigo_IBGE']);
					reloadUI('#' . divId);
				} else {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
		    		dialogConfirm("Erro!", request.data, $buttons)
				}
			});
		}

		function alteraEstadoDescarregamento(elem, divId) {
		    $('#' + divId).showLoading();
		    $.getJSON('/fis/fis012/form_func-ajax/alteraEstadoDescarregamento/?ajax=true&Novo_D018_Descarregamento=' + encodeURIComponent($('#Novo_D018_Descarregamento').val()) + '&callback=?', function(request) {
		        $('#' + divId).hideLoading();
				if (request.code) {
					$('#Novo_D020_Descarregamento').html(request.data['Novo_D020_Descarregamento']);
					reloadUI('#' . divId);
				} else {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
		    		dialogConfirm("Erro!", request.data, $buttons)
				}
			});
		}

		function alteraEstadoCabecalho(elem, divId) {
		    $('#' + divId).showLoading();
		    $.getJSON('/fis/fis012/form_func-ajax/alteraEstadoCabecalho/?ajax=true&T276_D018_Id_Carregamento=' + encodeURIComponent($('#T276_D018_Id_Carregamento').val()) + '&callback=?', function(request) {
		        $('#' + divId).hideLoading();
				if (request.code) {
					$('#T276_D020_Id_Carregamento').html(request.data['T276_D020_Id_Carregamento']);
					reloadUI('#' . divId);
				} else {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
		    		dialogConfirm("Erro!", request.data, $buttons)
				}
			});
		}

		function carregaDadosNfeMdfe(elem, divId) {
			if ($('#T284_T007_Id').val()) {
				$('#' + divId).showLoading();
				$.getJSON('/fis/fis012/form_func-ajax/carregaDadosNfeMdfe/?ajax=true&T284_T007_Id=' + encodeURIComponent($('#T284_T007_Id').val()) + '&callback=?', function(request) {
					$('#' + divId).hideLoading();
					if (request.code) {
						if (!request.data['T284_T276_Id']) {
							if(!request.data['T284_Chave_De_Acesso_NFe']){
								var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
								dialogConfirm("Erro!", "Essa nota está sem a Chave de acesso.", $buttons)
							}else{
								$('#' + divId + ' #T284_Chave_De_Acesso_NFe').val(request.data['T284_Chave_De_Acesso_NFe']).prop('readonly', true).css('background-color', '#ede7de');
								$('#' + divId + ' #T284_Peso_Bruto_Total').val(request.data['T284_Peso_Bruto_Total']);
								$('#' + divId + ' #T284_Valor_Carga').val(request.data['T284_Valor_Carga']).prop('readonly', true).css('background-color', '#ede7de');
								$('#' + divId + ' #T284_Numero_NFe').val(request.data['T284_Numero_NFe']);
								$('#' + divId + ' #T284_Destinatario_Nome').val(request.data['T284_Destinatario_Nome']).prop('readonly', true).css('background-color', '#ede7de');
								$('#' + divId + ' #T284_Cidade_Descarregamento').val(request.data['T284_Cidade_Descarregamento']);
								$('#' + divId + ' #T284_Cidade_Descarregamento_Codigo_IBGE').val(request.data['T284_Cidade_Descarregamento_Codigo_IBGE']);
								$('#T284_D018_Id').html(request.data['T284_D018_Id']);
								$('#T284_D020_Id').html(request.data['T284_D020_Id']);
							}
						} else {
							var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
							dialogConfirm("Erro!", "Essa NF já está vinculada ao MDF-e: " + request.data['T276_Numero_MDFe'], $buttons)
						}

					} else {
						var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
						dialogConfirm("Erro!", request.data['erro'], $buttons)
					}
				});
			}
		}

		function carregaDadosMotorista(elem, divId) {
			if ($('#T278_D109_Id').val()) {
			    $('#' + divId).showLoading();
			    $.getJSON('/fis/fis012/form_func-ajax/carregaDadosMotorista/?ajax=true&T278_D109_Id=' + encodeURIComponent($('#T278_D109_Id').val()) + '&callback=?', function(request) {
			        $('#' + divId).hideLoading();
					if (request.code) {
						$('#' + divId + ' #T278_Nome').val(request.data['T278_Nome']);
						$('#' + divId + ' #T278_CPF').val(request.data['T278_CPF']);
						$('#' + divId + ' #D109_Flag_Ativo').val(request.data['D109_Flag_Ativo']);
						reloadUI('#' . divId);
					} else {
						var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
			    		dialogConfirm("Erro!", request.data, $buttons)
					}
				});
			}
		}

		function carregaSeguradora(elem, divId) {
		    if ($('#T279_D172_Id').val()) {
		    	$('#' + divId).showLoading();
			    $.getJSON('/fis/fis012/form_func-ajax/carregaSeguradora/?ajax=true&T279_D172_Id=' + encodeURIComponent($('#T279_D172_Id').val()) + '&callback=?', function(request) {
			        $('#' + divId).hideLoading();
					if (request.code) {
						$('#' + divId + ' #T279_Seguradora_Nome').val(request.data['T279_Seguradora_Nome']);
						$('#' + divId + ' #T279_Seguradora_CNPJ').val(request.data['T279_Seguradora_CNPJ']);

						reloadUI('#' . divId);
					}
				});
		    }
		}

		function verificaVeiculo(elem, divId) {  
			if (!isempty($('#T283_D170_Id').val())) {
		    	$('#' + divId).showLoading();
			    $.getJSON('/fis/fis012/form_func-ajax/verificaVeiculo/?ajax=true&T283_D170_Id=' + encodeURIComponent($('#T283_D170_Id').val()) + '&callback=?', function(request) {
			        $('#' + divId).hideLoading();
					if (request.code) {
						//campos que serão setados
						$('#' + divId + ' #T283_Placa').val(request.data['T283_Placa']);
						$('#' + divId + ' #T283_Renavam').val(request.data['T283_Renavam']);
						$('#' + divId + ' #T283_Tara').val(request.data['T283_Tara']);
						$('#' + divId + ' #T283_Capacidade_Kg').val(request.data['T283_Capacidade_Kg']);
						$('#' + divId + ' #T283_Capacidade_M3').val(request.data['T283_Capacidade_M3']);
						$('#' + divId + ' #T283_Identificacao_Veiculo').val(request.data['T283_Identificacao_Veiculo']);
						$('#T283_Flag_Rodado').html(request.data['T283_Flag_Rodado']);
						$('#T283_Flag_Carroceria').html(request.data['T283_Flag_Carroceria']);
						$('#T283_Categoria_Veiculo').html(request.data['T283_Categoria_Veiculo']);
						$('#T283_UF_Licenciamento').html(request.data['T283_UF_Licenciamento']);
					} else {
						var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
	    				dialogConfirm("Erro!", request.data, $buttons)
					}
				});
			}
		}
	
		function estadoDescarregamento(elem, divId) {
		    $('#' + divId).showLoading();
		    $.getJSON('/fis/fis012/form_func-ajax/estadoDescarregamento/?ajax=true&T276_D018_Id_Descarregamento=' + encodeURIComponent($('#T276_D018_Id_Descarregamento').val()) + '&callback=?', function(request) {
		        $('#' + divId).hideLoading();
				if (request.code) {
					$('#T276_D020_Id_Descarregamento').html(request.data['T276_D020_Id_Descarregamento']);
					reloadUI('#' . divId);
				} else {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
		    		dialogConfirm("Erro!", request.data, $buttons)
				}
			});
		}
	} </script>
</div>
