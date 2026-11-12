<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	
	function fin007ValidaCampoLiberar(T005_Id,divId,divIdRoot){
		$('#' + divId).showLoading();
		$.getJSON('/fin/fin007/grid_func-ajax/validarCamposNFe/?ajax=true&T005_Id=' + encodeURIComponent(T005_Id) + '&callback=?', function(request) {
			$('#' + divId).hideLoading();
			if(request.code){
				var $buttons = {
					"Sim": function() {
						$('#' + divId).showLoading();
						$.getJSON('/fin/fin007/grid_func-ajax/validaCampoLiberar/?ajax=true&T005_Id=' + encodeURIComponent(T005_Id) + '&callback=?', function(request) {
							$('#' + divId).hideLoading();
							if (request.code) {
								dialogConfirm('Ok', request.data);
								divRefresh((divId), true);
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
						$(this).dialog("destroy").remove();
					},
					"Não": function() { $(this).dialog("destroy").remove(); } 
				};
				dialogConfirm('Liberar Pedido', 'Deseja liberar o pedido?', $buttons);
			}else if(request.code === false){
				var buttons = {
					"Cancelar": function(){
						$(this).dialog("destroy").remove();
					},
					"Continuar": function(){
						$(this).dialog("destroy").remove();
						var $buttons = {
							"Sim": function() {
								$('#' + divId).showLoading();
								$.getJSON('/fin/fin007/grid_func-ajax/validaCampoLiberar/?ajax=true&T005_Id=' + encodeURIComponent(T005_Id) + '&callback=?', function(request) {
									$('#' + divId).hideLoading();
									if (request.code) {
										dialogConfirm('Ok', request.data);
										divRefresh((divId), true);
									} else {
										dialogConfirm('Erro', request.data);
									}
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						dialogConfirm('Liberar Pedido', 'Deseja liberar o pedido?', $buttons);
					}
				};
				dialogConfirm('Alertas de Validação NFe', '<textarea style=" background-color: #FFF; width:900px; height:400px; text-transform: none;">' + request.data + '</textarea>', buttons);
			}
		});
	
	}
	
	function fin007ValidaCampoRetornarAlmx(T005_Id, divId){
		var $buttons = {
			"Sim": function() {
				$('#' + divId).showLoading();
				$.getJSON('/fin/fin007/grid_func-ajax/fin007ValidaCampoRetornarAlmx/?ajax=true&T005_Id=' + encodeURIComponent(T005_Id) + '&callback=?', function(request) {
					$('#' + divId).hideLoading();
					if (request.code) {
						dialogConfirm('Ok', request.data);
						divRefresh((divId), true);
					} else {
						dialogConfirm('Erro', request.data);
					}
				});
				$(this).dialog("destroy").remove();
			},
			"Não": function() { $(this).dialog("destroy").remove(); } 
		};
		dialogConfirm('Retornar Pedido', 'Deseja retornar o pedido para o vendedor?', $buttons);
	}
	
	function fin007ValidaCampoNaoLiberar(event,T005_Id, divId, divIdRoot){
		var content=unique();
			abrirJanela(event, divIdRoot, divId, content, '', 'Não liberar título - ID: '+T005_Id, '/fin/fin007/form/fin007FormNaoLiberar/', '&acaoId=' + T005_Id + '&tab=geral', [475,200]);			
	}
	
	
	function fin007CheckboxAguardar(checkbox,T005_Id,divId) {
	 var selected;
	 if($(checkbox).is(':checked'))
		 selected='1';
	 else
		 selected='2';
 	
	 $('#' + divId).showLoading();				
	 $.getJSON('/fin/fin007/grid_func-ajax/fin007CheckboxAguardar/?ajax=true&T005_Id=' + encodeURIComponent(T005_Id) + '&selected=' + selected + '&callback=?', function(request) {
		 $('#' + divId).hideLoading();					
	 });
	}


	 function fin007ValidaCampoLiberarNota(T007_Id,divId,divIdRoot){
		var $buttons = {
			"Sim": function() {
				$('#' + divId).showLoading();
				$.getJSON('/fin/fin007/grid_func-ajax/validaCampoLiberarNota/?ajax=true&T007_Id=' + encodeURIComponent(T007_Id) + '&callback=?', function(request) {
					$('#' + divId).hideLoading();
					if (request.code) {
						dialogConfirm('Ok', request.data);
						divRefresh((divId), true);
					} else {
						dialogConfirm('Erro', request.data);
					}
				});
				$(this).dialog("destroy").remove();
			},
			"Não": function() { $(this).dialog("destroy").remove(); } 
		};
		dialogConfirm('Liberar Nota', 'Deseja liberar a nota?', $buttons);
	}

	function fin007ValidaCampoNaoLiberarNota(T007_Id,divId,divIdRoot){
		var $buttons = {
			"Sim": function() {
				$('#' + divId).showLoading();
				$.getJSON('/fin/fin007/grid_func-ajax/validaCampoNaoLiberarNota/?ajax=true&T007_Id=' + encodeURIComponent(T007_Id) + '&callback=?', function(request) {
					$('#' + divId).hideLoading();
					if (request.code) {
						dialogConfirm('Ok', request.data);
						divRefresh((divId), true);
					} else {
						dialogConfirm('Erro', request.data);
					}
				});
				$(this).dialog("destroy").remove();
			},
			"Não": function() { $(this).dialog("destroy").remove(); } 
		};
		dialogConfirm('Liberar Nota', 'Não liberar a nota?', $buttons);
	}
	
	
} </script></div>