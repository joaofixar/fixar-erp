function number_format( number, decimals, dec_point, thousands_sep ) {
	// %        nota 1: Para 1000.55 retorna com precisão 1 no FF/Opera é 1,000.5, mas no IE é 1,000.6
	// *     exemplo 1: number_format(1234.56);
	// *     retorno 1: '1,235'
	// *     exemplo 2: number_format(1234.56, 2, ',', ' ');
	// *     retorno 2: '1 234,56'
	// *     exemplo 3: number_format(1234.5678, 2, '.', '');
	// *     retorno 3: '1234.57'
	// *     exemplo 4: number_format(67, 2, ',', '.');
	// *     retorno 4: '67,00'
	// *     exemplo 5: number_format(1000);
	// *     retorno 5: '1,000'
	// *     exemplo 6: number_format(67.311, 2);
	// *     retorno 6: '67.31'
 
	var n = number, prec = decimals;
	n = !isFinite(+n) ? 0 : +n;
	prec = !isFinite(+prec) ? 0 : Math.abs(prec);
	var sep = (typeof thousands_sep == "undefined") ? ',' : thousands_sep;
	var dec = (typeof dec_point == "undefined") ? '.' : dec_point;
 
	var s = (prec > 0) ? n.toFixed(prec) : Math.round(n).toFixed(prec); //fix for IE parseFloat(0.55).toFixed(0) = 0;
 
	var abs = Math.abs(n).toFixed(prec);
	var _, i;
 
	if (abs >= 1000) {
		_ = abs.split(/\D/);
		i = _[0].length % 3 || 3;
 
		_[0] = s.slice(0,i + (n < 0)) +
			  _[0].slice(i).replace(/(\d{3})/g, sep+'$1');
 
		s = _.join(dec);
	} else {
		s = s.replace('.', dec);
	}
 
	return s;
}

function formatarCNPJ(campo,o,e,nome_form) {
	var vr = o.value;
	var tam = vr.length;
	var strCheck = '0123456789';
	var cTecla = '';
	var nCodTecla = (window.Event) ? e.which : e.keyCode;
	var cValor= vr;

	// Se for um enter, pula para o proximo campo
	//Skip_Campo(campo,e,nome_form);

	if ((nCodTecla==8) || (nCodTecla==0) || (nCodTecla == 13)) {
	   return true;
	}

	// Verifica se é um Valor ou Numero
	cTecla = String.fromCharCode(nCodTecla);


	// Não é um Número ou Valor Válido
	if (strCheck.indexOf(cTecla) == -1) {
		return false;
	}

	if (tam == 2)
		cValor = vr.substr( 0, 2 ) + '.';

	if (tam == 6)
		cValor =  vr.substr( 0, 6 ) + '.';

	if  (tam == 10)
		cValor = vr.substr( 0, 10) + '/';

	if  (tam == 15)
		cValor = vr.substr( 0, 15) + '-';

	o.value=cValor;

	if(tam < 18){
		$('#erroCnpj').remove();
		$(o).parent().removeClass('has-error');
	}
}

function formatarCPF(campo,o,e,nome_form) {
	var vr = o.value;
	var tam = vr.length;
	var strCheck = '0123456789';
	var cTecla = '';
	var nCodTecla = (window.Event) ? e.which : e.keyCode;
	var cValor= vr;

	// Se for um enter, pula para o proximo campo
	//Skip_Campo(campo,e,nome_form);

	if ((nCodTecla==8) || (nCodTecla==0) || (nCodTecla == 13)) {
	   return true;
	}

	// Verifica se é um Valor ou Numero
	cTecla = String.fromCharCode(nCodTecla);

	// Não é um Número ou Valor Válido
	if (strCheck.indexOf(cTecla) == -1) return false;

	if (tam == 3)
		cValor = vr.substr( 0, 3 ) + '.';

	if (tam == 7)
		cValor =  vr.substr( 0, 7 ) + '.';

	if  (tam == 11)
		cValor = vr.substr( 0, 11) + '-';

	o.value=cValor;
}

function validaCNPJ(campo){
	$.getJSON('/ajax/?funcao=validaCNPJ&D024_Cnpj=' + encodeURIComponent($(campo).val()) + '&callback=?', function(request) {
		if(!request.code) {
			$(campo).parent().addClass('has-error');
			$('#erroCnpj').remove();
			if(request.data != 'invalido'){
				$(campo).parent().append('<label id="erroCnpj" class="control-label" for="inputError">Este CNPJ já está cadastrado.</label>');
			} else {
				$(campo).parent().append('<label id="erroCnpj" class="control-label" for="inputError">Este CNPJ não é válido.</label>');
			}
			return false;
		} else {
			$(campo).parent().removeClass('has-error');
			$('#erroCnpj').remove();
			return true;
		}
	});
}

function validaCPF(campo){
	$.getJSON('/ajax/?funcao=validaCPF&D024_CPF_Responsavel=' + encodeURIComponent($(campo).val()) + '&callback=?', function(request) {
		if(!request.code) {
			$(campo).parent().addClass('has-error');
			$('#erroCpf').remove();
			$(campo).parent().append('<label id="erroCpf" class="control-label" for="inputError">Este CPF não é válido.</label>');
			return false;
		} else {
			$(campo).parent().removeClass('has-error');
			$('#erroCpf').remove();
			return true;
		}
	});
}

function atualizarComboCidades(campo){
	$.getJSON('/ajax/?funcao=atualizarComboCidades&D024_D018_Id=' + encodeURIComponent($(campo).val()) + '&callback=?', function(request) {
		if (request.code) {
			$('#D024_D020_Id').html(request.data);
		}
	});
}

function validaCamposFormulario(){
	$('.erroCampo').remove();
	var erros = false;
	$('.has-error').removeClass('has-error');
	$("#formCadastro input[type=text], #formCadastro input[type=password]").each(function() {
		if(this.value == '' && $(this).attr('id') != 'D024_Complemento' && $(this).attr('id') != 'D024_Contador' && $(this).attr('id') != 'D024_Telefone_Contador' && $(this).attr('id') != 'D024_Cargo_Responsavel'){
			$(this).parent().addClass('has-error');
			if($(this).attr('id') == 'Dominio'){
				$(this).parent().parent().append('<label class="erroCampo control-label" style="float: left; color: #b94a48;" for="inputError">Este campo deve ser preenchido!</label>');
			} else if($(this).attr('id') != 'D024_DDD_Telefone'){
				$(this).parent().append('<label class="erroCampo control-label" style="float: left; color: #b94a48;" for="inputError">Este campo deve ser preenchido!</label>');
			}
			if($(this).attr('id') == 'D024_Cnpj'){
				erros = validaCNPJ(this);
			}
			erros = true;
		}
	});
	$("#formCadastro select").each(function() {
		if(this.value <= 0){
			$(this).parent().addClass('has-error');
			$(this).parent().append('<label class="erroCampo" class="control-label" for="inputError" style="color: #b94a48;">Este campo deve ser preenchido!</label>');
			erros = true;
		}
	});
	if(erros == true){
		return false;
	} else {
		return true;
	}
}

function validaSenha(){
	if($('#senha').val() != '' && $('#senha_confirma').val() != ''){
		if($('#senha').val() != $('#senha_confirma').val()){
			$('.has-success').removeClass('has-success');
			$('#senha').parent().addClass('has-warning');
			$('#erroSenha').remove();
			$('#senha_confirma').parent().addClass('has-warning');
			$('#senha_confirma').parent().append('<label id="erroSenha" class="control-label" for="inputWarning">As senhas devem ser iguais!</label>');
		} else {
			$('.has-warning').removeClass('has-warning');
			$('#erroSenha').remove();
			$('#senha').parent().addClass('has-success');
			$('#senha_confirma').parent().addClass('has-success');
			
		}
	} else {
		$('.has-warning').removeClass('has-warning');
		$('#erroSenha').remove();
	}
}

function validaDominioEmpresa(campo, server){
	$.getJSON('/ajax/?funcao=validaDominioEmpresa&Dominio=' + encodeURIComponent($(campo).val()) + '&Server=' + encodeURIComponent(server) + '&callback=?', function(request) {
		if(!request.code) {
			$(campo).parent().addClass('has-error');
			$('#erroDominio').remove();
			$(campo).parent().parent().append('<label id="erroDominio" class="control-label" for="inputError">' + request.data + '</label>');
			return false;
		} else {
			$(campo).parent().removeClass('has-error');
			$('#erroDominio').remove();
			return true;
		}
	});
}