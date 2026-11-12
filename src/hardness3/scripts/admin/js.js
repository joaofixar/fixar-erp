function showAlert(title, message, buttons, widthPercentage, show, shown, close) {
	//alert(message);
	//return false;
	var buttons = (typeof(buttons) == 'undefined' || !buttons) ? '<button class="btn" data-dismiss="modal" aria-hidden="true">Fechar</button>' : buttons;
	var widthPercentage = (typeof(widthPercentage) == 'undefined' || !widthPercentage) ? false : widthPercentage;
	var show = (typeof(show) == 'undefined' || !show) ? false : show;
	var shown = (typeof(shown) == 'undefined' || !shown) ? false : shown;
	var close = (typeof(close) == 'undefined' || !close) ? false : close;
	var id = unique();
	var alert = $('<div></div>')
		.attr({
			'id': id,
			//'tabindex': '1',
			'role': 'dialog',
			'aria-labelledby': id,
			'aria-hidden': 'true'
		})
		.addClass('modal hide')
		.appendTo('body')
	var html = '\
	    <div class="modal-header">\
	    	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>\
	    	<h3 id="myModalLabel">' + title + '</h3>\
	    </div>\
	    <div class="modal-body">\
	    	<p>' + message + '</p>\
	    </div>\
	    <div class="modal-footer">\
	    	' + buttons + '\
	    </div>\
    ';
    // Callbacks
    if (show) {
    	alert
    		.on('show', function() {
		    	show(alert);
			});
	}
	if (shown) {
		alert
			.on('shown', function() {
				shown(alert);
			});
	}
	if (close) {
		alert
			.on('close', function() {
				close(alert);
			});
	}
	// Definir Tamanho
    if (widthPercentage) {
	    alert.css({
	       'width': function () { 
	           return ($(document).width() * widthPercentage) + 'px';  
	       },
	       'margin-left': function () { 
	           return -($(this).width() / 2); 
	       }
		});
	}
	// Abrir Modal
    alert.html(html).modal();
}

function unique()
{
	var str = "";
	var chars = "0123456789abcdefghijklmnopqurstuvwxyzABCDEFGHIJKLMNOPQURSTUVWXYZ";			
	for(var i = 0; i < 13; i++)
	{
		str += chars.substr( Math.floor(Math.random() * 62), 1 );
	}
	//var newDate = new Date; + newDate.getTime()
	return str;
}

function errorPhp(log, div, filtro) {
	var div = (typeof(div) == 'undefined') ? false : div;
	var filtro = (typeof(filtro) == 'undefined') ? '' : filtro;
	$.ajax({
		url: '/hardness3/scripts/admin/errosPhp.php',
		dataType: 'html',
		type: 'GET',
		cache: false,
		data: 'log=' + encodeURIComponent(log) + '&filtro=' + filtro,
		success: function(data) {
			if (div) {
				div.html(data);
				$('.modal-body').css('opacity', '1');
			} else {
				var onclick = "$('.modal-body').css('opacity', '0.3'); errorPhp('" + log + "', $(this).closest('.modal').find('.modal-body'), $(this).attr('filtro')); return false;";
				showAlert('Erros PHP', data, '<button class="btn btn-info" onclick="errorPhpFiltro(); return false;">Ocultar/Mostrar Notice</button> <button class="btn btn-info btn-refresh" onclick="' + onclick + '" filtro="">Refresh</button> <button class="btn " data-dismiss="modal" aria-hidden="true">Fechar</button>', .9);
			}
		}
	});
}

function errorPhpFiltro() {
	if ($('.btn-refresh').attr('filtro') == '') {
		$('.btn-refresh').attr('filtro', 'true');
	} else {
		$('.btn-refresh').attr('filtro', '');
	}
	$('.btn-refresh').trigger('click');
}

function nova_janela(Pagina,Topo,Posicao,Largura,Altura,Fixo,Botoes,Endereco,Status,Rolagem,Menu,Nome) {
	if (!Fixo)   {   var Fixo    = 'yes';                       }
	if (!Rolagem){   var Rolagem = 'yes';                       }
	if (!Menu)   {   var Menu    = 'yes';                       }
	if (!Status) {   var Status  = 'yes';                       }
	param = "menubar="+Menu+",toolbar="+Botoes+",location="+Endereco+",status="+Status+",scrollbars="+Rolagem+",resizable="+Fixo+",width="+Largura+",height="+Altura+",top="+Topo+",left="+Posicao;
	window.open(Pagina,Nome,param);
}

function runUpdate(empresa) {
	/*if (empresa == 'todas') {
		var buttons = {
			"Sim": function() {
				nova_janela('/hardness3/scripts/erp/runUpdate.php?empresa=' + encodeURIComponent(empresa) + '&cmd=' + '&atualizar=' + encodeURIComponent($('.ui-dialog-content textarea:first').val()),20,20,900,700,'no','no','no','no','yes','yes',0);
			},
			"Não": function() { $(this).dialog("destroy").remove(); } 
		};
		dialogConfirm('Atenção!', '', buttons, false, false, $('#atualizar'));
	} else {*/
		var cmd = prompt("Preencha o nome de um diretório ou arquivo para filtrar o update. Exemplo arquivo: prog/ven/ven001/form.php - Exemplo diretório: prog/ven/ven001");
		if (typeof(cmd) == 'string') {
			nova_janela('/hardness3/scripts/erp/runUpdate.php?empresa=' + encodeURIComponent(empresa) + '&cmd=' + encodeURIComponent(cmd),20,20,900,700,'no','no','no','no','yes','yes',0);
		}
	//}
}
function runAtualizacaoBase(empresa) {
	/*if (empresa == 'todas') {
		var buttons = {
			"Sim": function() {
				nova_janela('/hardness3/scripts/erp/runAtualizacaoBase.php?empresa=' + encodeURIComponent(empresa) + '&atualizar=' + encodeURIComponent($('.ui-dialog-content textarea:first').val()),20,20,900,700,'no','no','no','no','yes','yes',0);
			},
			"Não": function() { $(this).dialog("destroy").remove(); } 
		};
		dialogConfirm('Atenção!', '', buttons, false, false, $('#atualizar'));
	} else {*/
		var cmd = confirm("Executar atualização base?");
		if (cmd) {
			nova_janela('/hardness3/scripts/erp/runAtualizacaoBase.php?empresa=' + encodeURIComponent(empresa),20,20,900,700,'no','no','no','no','yes','yes',0);
		}
	//}
}

function dialogConfirm(titulo, texto, buttons, big, id, $jQueryHTML) {
	var big = (typeof(big) == 'undefined') ? false : big;
	var id = (typeof(id) == 'undefined') ? false : id;
	if (typeof(buttons) == 'undefined' || !buttons) {
		var buttons = {
			"Fechar": function() { $(this).dialog("destroy").remove(); }
		};
	}
	if (typeof($jQueryHTML) != 'undefined') {
		texto = $jQueryHTML.html();
	}
	var novoId = unique();
	var $confirmacao = $("<div>")
		.addClass('dialogConfirm')
    	.attr({'id': 'dc-' + novoId})
    	.appendTo('body');
    if (id) {
		$("<p id='"+id+"'>").html(texto).appendTo($confirmacao);
	} else {
		$("<p>").html(texto).appendTo($confirmacao);
	}
    $("#dc-" + novoId).dialog({
    	width: 'auto',
    	height: 'auto',
    	minWidth: 250,
		resizable: false,
		modal: true,
		title: titulo,
		buttons: buttons,
		zIndex: 9999, // (faz travar as vezes o conteúdo do dialog - tem que ser um número baixo)
		dialogClass: (big) ? 'ui-dialog-big' : ''
	});
	$("#dc-" + novoId).parent().find('.ui-dialog-titlebar a').remove();
	/*
	setTimeout(function() {
		$("#dc-" + novoId).parent().find('button:last').focus();
	}, 500);
	*/
	return novoId;
}

function sql(base, div) {
	var base = (typeof(base) == 'undefined') ? false : base;
	var filtro = (typeof(filtro) == 'undefined') ? '' : filtro;
	if (!base) {
		alert('Nenhuma base selecionada.');
		return false;
	}
	$.ajax({
		url: '/hardness3/scripts/admin/sql.php',
		dataType: 'html',
		type: 'GET',
		cache: false,
		data: 'base=' + encodeURIComponent(base),
		success: function(data) {
			if (div) {
				div.html(data);
				$('.modal-body').css('opacity', '1');
				$('#sqleditor').focus();
			} else {
				showAlert('Base: ' + base, data, '<button class="btn " data-dismiss="modal" aria-hidden="true">Fechar</button>', .9, false, function(alert) {
					editor = CodeMirror.fromTextArea(document.getElementById("sqleditor"), {
						lineNumbers: true,
						matchBrackets: true,
						mode: "mysql",
						indentUnit: 4,
						indentWithTabs: true,
						enterMode: "keep",
						tabMode: "shift"
					});
					editor.focus();
					/*editor.on('change', function (varEditor, event) {
						var line = editor.getLine(event.to.line);
						var ch = event.to.ch;
						$.ajax({
							url: '/hardness3/scripts/admin/sqlAutoComplete.php',
							dataType: 'html',
							type: 'GET',
							cache: false,
							data: 'base=' + encodeURIComponent(base) + '&line=' + encodeURIComponent(line) + '&ch=' + encodeURIComponent(ch),
							success: function(data) {
								$('#ajudacomplete').html(data);
							}
						});
					});*/
				});
			}
		}
	});
}