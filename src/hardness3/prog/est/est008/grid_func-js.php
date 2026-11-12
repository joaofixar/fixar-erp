<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
// -> campoExclusao($array)

	function est008CheckboxGerarOC(checkbox,T174_Id,divId) {
		var selected;
		if($(checkbox).is(':checked'))
			selected='1';
		else
			selected='2';
		$('#' + divId).showLoading();				
		$.getJSON('/est/est008/grid_func-ajax/checkboxGerarOC/?ajax=true&T174_Id=' + encodeURIComponent(T174_Id) + '&selected=' + selected + '&callback=?', function(request) {
			$('#' + divId).hideLoading();
		});	
	}
	
	function est008CheckboxFlagIndisponivel(checkbox,T174_Id,divId) {
		var selected;
		if($(checkbox).is(':checked'))
			selected='S';
		else
			selected='N';
		$('#' + divId).showLoading();				
		$.getJSON('/est/est008/grid_func-ajax/CheckboxFlagIndisponivel/?ajax=true&T174_Id=' + encodeURIComponent(T174_Id) + '&selected=' + selected + '&callback=?', function(request) {
			$('#' + divId).hideLoading();
		});	
	}

	function abrirImpressao(T010_Id, D024_Id){
		$.getJSON('/est/est008/form_func-ajax/est008ImprimirCotacaoCompra/?ajax=true&T010_Id=' + encodeURIComponent(T010_Id) + '&D024_Id=' + encodeURIComponent(D024_Id) + '&callback=?', function(request) {
			window.open(request.data);
		});
	}
	
	function duplicaProdutoCotacao(T059_Id, divId){
		$.getJSON('/est/est008/grid_func-ajax/duplicaProduto/?ajax=true&T059_Id=' + encodeURIComponent(T059_Id) + '&callback=?', function(request) {
			if(request.code == true){
				divRefresh(divId, true);
			}
		});	
	}
	
	function duplicaProdutoCotacaoFornecedor(T059_Id, T174_Id, divId){
		$.getJSON('/est/est008/grid_func-ajax/duplicaProdutoForn/?ajax=true&T059_Id=' + encodeURIComponent(T059_Id) + '&T174_Id=' + encodeURIComponent(T174_Id) + '&callback=?', function(request) {
			if(request.code == true){
				divRefresh(divId, true);
			}
		});	
	}
	
	function updateT174_Flag_Excluido(T174_Id, divId){
		var buttons = {
			"Sim": function() {
				//var T005_Id = $('#{$g['divId']} #T005_Id').val();
				$.getJSON('/est/est008/grid_func-ajax/ajaxUpdateT174_Flag_Excluido/?ajax=true&T174_Id=' + encodeURIComponent(T174_Id) + '&callback=?', function(request) {
					if(request.code == true){
						divRefresh(divId, true);
					}
				});	
				$(this).dialog("destroy").remove();
			},
			"Não": function() { $(this).dialog("destroy").remove(); } 
		};
		dialogConfirm('Excluir Produto', 'Você tem certeza? ', buttons);
	}
	function est008CheckboxExcluido(T174_Id, divId){
		$.getJSON('/est/est008/grid_func-ajax/est008updatecheckboxExcluido/?ajax=true&T174_Id=' + encodeURIComponent(T174_Id) + '&callback=?', function(request) {
			if(request.code == true){
				divRefresh(divId, true);
			}
		});	
	}
		
} </script></div>