<?php
return true;
echo <<<EOT
	$(document).unbind('swipeleft');
    $(document).bind('swipeleft',function(event){
    	var T003_Id = 'last';
    	var file = event.currentTarget.location.pathname;
    	var param = event.currentTarget.location.search;
    	var param_ = param.split('&');
		$.each(param_, function(index, value) {
			if (value.match('T003_Id')) {
				var acaoId = value.split('=');
				T003_Id = (acaoId[1] == 'new') ? 'last' : acaoId[1];
			}
		});
    	/*if (file.match(/orcamentoLista\.php/)) {
    		$.mobile.changePage( './orcamento.php?T003_Id=' + T003_Id, { transition: 'slide', reloadPage: true} );
    	}*/
    	if (file.match(/orcamento\.php/)) {
    		$.mobile.changePage( './clientes.php?T003_Id=' + T003_Id, { transition: 'slide', reloadPage: true} );
    	}
    	if (file.match(/clientes\.php/)) {
    		$.mobile.changePage( './pagamento.php?T003_Id=' + T003_Id, { transition: 'slide', reloadPage: true} );
    	}
    	if (file.match(/pagamento\.php/)) {
    		$.mobile.changePage( './gerarPedido.php?T003_Id=' + T003_Id, { transition: 'slide', reloadPage: true, ajax: false} );
    	}
    	if (file.match(/pagamentoGrid\.php/)) {
    		$.mobile.changePage( './gerarPedido.php?T003_Id=' + T003_Id, { transition: 'slide', reloadPage: true, ajax: false} );
    	}
    });
    $(document).unbind('swiperight');
    $(document).bind('swiperight',function(event){
    	var T003_Id = 'last';
    	var file = event.currentTarget.location.pathname;
    	var param = event.currentTarget.location.search;
    	var param_ = param.split('&');
		$.each(param_, function(index, value) {
			if (value.match('T003_Id')) {
				var acaoId = value.split('=');
				T003_Id = (acaoId[1] == 'new') ? 'last' : acaoId[1];
			}
		});
    	if (file.match(/orcamento\.php/)) {
    		$.mobile.changePage( './orcamentoLista.php?T003_Id=' + T003_Id, { transition: 'slide', reloadPage: true, reverse: true } );
    	}
    	if (file.match(/clientes\.php/)) {
    		$.mobile.changePage( './orcamento.php?T003_Id=' + T003_Id, { transition: 'slide', reloadPage: true, reverse: true } );
    	}
    	if (file.match(/pagamento\.php/)) {
    		$.mobile.changePage( './clientes.php?T003_Id=' + T003_Id, { transition: 'slide', reloadPage: true, reverse: true } );
    	}
    	if (file.match(/pagamentoGrid\.php/)) {
    		$.mobile.changePage( './clientes.php?T003_Id=' + T003_Id, { transition: 'slide', reloadPage: true, reverse: true } );
    	}
    	if (file.match(/gerarPedido\.php/)) {
    		$.mobile.changePage( './pagamento.php?T003_Id=' + T003_Id, { transition: 'slide', reloadPage: true, reverse: true } );
    	}
    });
EOT;
