<?php
// <meta name="apple-mobile-web-app-capable" content="yes" />
// user-scalable=no
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
		<title>HARDNESS SISTEMAS - MOBILE</title>
        <link rel="stylesheet" href="/hardness3/mobile/tablet/jquery.mobile-1.0.1/jquery.mobile-1.0.1.css" />
        <link rel="stylesheet" href="/hardness3/mobile/tablet/jtsage-jquery-mobile-datebox-cddb96c/css/jquery.mobile.datebox.css" />
        <link rel="stylesheet" type="text/css" href="/hardness3/mobile/tablet/simpledialog/jquery.mobile.simpledialog.min.css" /> 
        <style>
        	body {
        		font-size: 85%;
			}
			.ui-content {
				padding-top: 5px;
			}
			label.ui-input-text {
				font-size: 15px;
			}
			input.ui-input-text, textarea.ui-input-text {
				font-size: 15px;
			}
            .ui-header .ui-title {
            	font-size: 1.1em;
            	margin: 0.3em 40px 0.5em;
            	text-align: left;
			}
			.ui-header .ui-title .hleft {
				float: left;
			}
			.ui-header .ui-title .hright {
				float: right;
			}
			input {
				text-transform: uppercase;
			}
			.ui-grid-d {
   				 overflow: hidden;
			}

			.menuPrincipal li{margin-right:2px;}
			.nav-glyphish .ui-btn .ui-btn-inner { padding-top: 33px !important;}
			.nav-glyphish .ui-btn .ui-icon { width: 30px!important; height: 30px!important; margin-left: -15px !important; margin-top: -6px !important; box-shadow: none!important; -moz-box-shadow: none!important; -webkit-box-shadow: none!important; -webkit-border-radius: 0 !important; border-radius: 0 !important; }
			#menu-orcamento .ui-icon { background:  url(/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/icons/104-index-cards.png) 50% 50% no-repeat; background-size: 26px 17px; }
			#menu-produto .ui-icon { background:  url(/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/icons/15-tags.png) 50% 50% no-repeat; background-size: 24px 25px;  }
			#menu-cliente .ui-icon { background:  url(/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/icons/112-group.png) 50% 50% no-repeat;  background-size: 32px 21px; }
			#menu-pagamento .ui-icon { background:  url(/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/icons/192-credit-card.png) 50% 50% no-repeat;  background-size: 28px 18px; }
			#gerarPedidoBarraIcon .ui-icon { background:  url(/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/icons/117-todo.png) 50% 50% no-repeat;  background-size: 20px 18px; }
			#finalizarPedidoBarraIcon .ui-icon { background:  url(/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/icons/117-todo.png) 50% 50% no-repeat;  background-size: 28px 18px; }
			#menu-requisicao .ui-icon { background:  url(/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/icons/80-shopping-cart.png) 50% 50% no-repeat;  background-size: 24px 22px; }
			#menu-servico .ui-icon { background:  url(/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/icons/158-wrench-2.png) 50% 50% no-repeat;  background-size: 24px 22px; }
			#menu-produto-precos .ui-icon { background:  url(/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/icons/161-calculator.png) 50% 50% no-repeat;  background-size: 20px 22px; }
			/*#menu-relatorio .ui-icon { background:  url(/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/icons/17-bar-chart.png) 50% 50% no-repeat;  background-size: 24px 22px; }*/
			#menu-conversas .ui-icon { background:  url(/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/icons/09-chat-2.png) 50% 50% no-repeat;  background-size: 24px 22px; }
			#menu-caixa .ui-icon { background:  url(/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/icons/172-pricetag.png) 50% 50% no-repeat;  background-size: 24px 22px; }
			#menu-nota-fiscal .ui-icon { background:  url(/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/icons/179-notepad.png) 50% 50% no-repeat;  background-size: 22px 22px; }
			#menu-analise-credito .ui-icon { background:  url(/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/icons/16-line-chart.png) 50% 50% no-repeat;  background-size: 24px 22px; }
			#menu-contas-receber .ui-icon { background:  url(/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/icons/40-inbox.png) 50% 50% no-repeat;  background-size: 24px 22px; }
			#menu-contas-pagar .ui-icon { background:  url(/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/icons/44-shoebox.png) 50% 50% no-repeat;  background-size: 24px 22px; }
			#menu-devolucao .ui-icon { background:  url(/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/icons/02-redo.png) 50% 50% no-repeat;  background-size: 24px 22px; }
			#menu-compra .ui-icon { background:  url(/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/icons/35-shopping.png) 50% 50% no-repeat;  background-size: 24px 22px; }
			#menu-estoque .ui-icon { background:  url(/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/icons/33-cabinet.png) 50% 50% no-repeat;  background-size: 24px 22px; }
			#menu-resumos-gerenciais .ui-icon { background:  url(/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/icons/17-bar-chart.png) 50% 50% no-repeat;  background-size: 24px 22px; }
			#voltar .ui-icon { background:  url(/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/icons/01-refresh.png) 50% 50% no-repeat;  background-size: 24px 26px; }
			#menu-empresa .ui-icon { background:  url(/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/icons/53-house.png) 50% 50% no-repeat;  background-size: 22px 22px; }
			#menu-sair .ui-icon { background:  url(/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/icons/00-close.png) 50% 50% no-repeat;  background-size: 22px 22px; }
        </style>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script> 
        <script src="/hardness3/mobile/tablet/jquery.mobile-1.0.1/jquery.mobile-1.0.1.js"></script>
        <script src="/hardness3/mobile/tablet/jtsage-jquery-mobile-datebox-cddb96c/js/jquery.mobile.datebox.js"></script>
        <script src="/hardness3/mobile/tablet/jtsage-jquery-mobile-datebox-cddb96c/js/datebox.i8n.status.js"></script>
        <script src="/hardness3/mobile/tablet/jquery.mobile-1.0.1/jquery.mobile.init.js"></script>
		<script type="text/javascript" src="/hardness3/mobile/tablet/simpledialog/jquery.mobile.simpledialog2.min.js"></script>
		<script type="text/javascript" src="/hardness3/mobile/tablet/js/func.js"></script>
    </head>
	<body>