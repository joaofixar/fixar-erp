<?php
namespace hardness;


$resposta = array('code' => true, 'data' => array());

	switch($g['r'][3]) {
        
        case 'conferenciaCegaFim':

            $id = base64_decode($r_acaoId);
            $flag = base64_decode($r_flag);
            $itens = json_decode($r_itens, true);
            $r_divId = isset($r_divId) ? $r_divId : '';

            $qtdDigitada = 0; $codDigitado = array(); 
            
            foreach ($itens as $item) { 
                $cod = $item['cod']; $qtd = $item['qtd']; 
                if (!in_array($cod, $codDigitado)) { $codDigitado[] = $cod;}
                $qtdDigitada += $qtd;
            }

            $codigoDigitado = count($codDigitado); 

            if (!isset($_SESSION['cacheKey'][$id])) {
                $query = "SELECT SUM(T014_Quantidade) AS qtdBanco, COUNT(*) AS codBanco, T014_flag_Conferencia_Cega AS flag_conf FROM T014 WHERE T014_T013_Id =".$id;
                $T014 = mysql_query($query);
                $_SESSION['cacheKey'][$id] = mysql_fetch_array($T014);
            }
                
            $mT014 = $_SESSION['cacheKey'][$id];

            $qtdTotal = intval($mT014['qtdBanco']);

            if ($mT014['qtdBanco'] == $qtdDigitada && $mT014['codBanco'] == $codigoDigitado) {
                echo <<<EOT

                    finalizarNotaEstoque();
                
                    function finalizarNotaEstoque(){
                        $.getJSON('/est/est004/form_func-ajax/verificaFinalizarNotaValidar/?ajax=true&T013_Id=' + encodeURIComponent("{$id}") + '&callback=?', function(request) {
                            if (request.code) {
                                $.getJSON('/est/est004/form_func-ajax/finalizarNotaEntradaEstoque/?ajax=true&T013_Id=' + encodeURIComponent("{$id}") + '&callback=?', function(request) {
                                    if (request.code) {
                                        $('#progressBar').html('<br><b><font color="green">100% Estoque finalizado ✔</font></b></br><b>' + request.percentual + '<b>% reprocessando histórico</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
                                        est004ReprocessandoHistorico(idProgressBar, $id,1);                            
                                    }
                                });
                            }
                        });
                                
                        return false;
                    }

                    function est004ReprocessandoHistorico(idProgressBar, T013_Id,inicio){
                        $.getJSON('/est/est004/form_func-ajax/reprocessaHistoricoNotaEntradaEstoque/?ajax=true&T013_Id=' + encodeURIComponent("{$id}") + '&inicio=' + inicio + '&callback=?', function(request) {
                            if (request.concluido == 0) {
                                $('#progressBar').html('<br><b><font color="green">100% Estoque finalizado ✔</font></b></br><b>' + request.percentual + '<b>% reprocessando histórico</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
                                est004ReprocessandoHistorico(idProgressBar,T013_Id,0);
                            }else{
                                console.log("Erro atualizar Estoque");
                            } 
                        });	                  
                    }
                
                    $.ajax({
                        type: "POST",
                        url: "/est/est004/form_func-ajax/statusPronto/",
                        data: { ajax: true, T013_Id: encodeURIComponent("{$id}") },
                        dataType: "json",
                        success: function(request) {},
                        error: function(error) {
                            console.log("Erro!!! Conclusão conferencia Cega");
                        }
                    });
                
                    $(".cabecalho").html("Conferencia Realizada com Sucesso!!").css({
                        "color": "#0f790cf5","font-weight": "bold", "padding": "10px", 
                    });

                    setTimeout(function() { 
                        fecharJanela('{$g['divId']}');
                        divRefresh('{$g['divIdParent']}', true);
                    }, 3000);
                    
                    $(".item").css({ "display": "none",});
EOT;
            } else if ($qtdDigitada == 0 || $codigoDigitado == 0 || $qtdDigitada == '' || $codigoDigitado == '') {
                echo <<<EOT

                    $(".cabecalho").html("Necessario conferir as notas para concluir!!").css({
                        "color": "#ff0000","font-weight": "bold", "padding": "10px", 
                    });
EOT;
            } else if ($mT014['qtdBanco'] !== $qtdDigitada || $mT014['codBanco'] !== $codigoDigitado) {
                echo <<<EOT

                    $(".cabecalho").html("Erro, Dados divergentes!!").css({
                        "color": "#ff0000","font-weight": "bold", "padding": "10px", 
                    });
EOT;
            } else {
                echo <<<EOT
                    $(".cabecalho").html("Erro, instabilidade no sistema, contate o suporte!!").css({
                        "color": "#ff0000","font-weight": "bold", "padding": "10px", 
                    });
EOT;}
        break;

        case 'CalcConfCegaFim':


			$id = base64_decode($r_acaoId);
			$itens = json_decode($r_itens, true);

			if (!isset($_SESSION['progressCache'][$id])) {
				$query = "SELECT SUM(T014_Quantidade) AS qtdBanco, COUNT(*) AS codBanco FROM T014 WHERE T014_T013_Id =".$id;
				$T014 = mysql_query($query);
				$_SESSION['progressCache'][$id] = mysql_fetch_array($T014);
			}
		
			$mT014 = $_SESSION['progressCache'][$id];

			$qtdTotal = intval($mT014['qtdBanco']);

			$codDigitado = array_unique(array_column($itens, 'cod'));
			$qtdDigitada = array_sum(array_column($itens, 'qtd'));
		
			$codigoDigitado = count($codDigitado);
			
			$pg = ($qtdDigitada / $mT014['qtdBanco']) * 100;
			$pg = min($pg, 100);

			echo <<<EOT
				$(".progress").css({ "width": "{$pg}%",});
				$(".progress-info").html(" $qtdDigitada / {$qtdTotal}");
EOT;
		break;
    }

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
