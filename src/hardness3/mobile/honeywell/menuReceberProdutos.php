<?php
namespace hardness;

include('conf.php');

$resposta = array('code' => true, 'data' => '');
	global $g;
	// Selecionando as notas fiscais recebidas
	$sqlT013 = <<<EOT
	SELECT * FROM T013
	left join T014 on T014_T013_Id=T013_Id
    left join T012 on T012_Id=T014_T012_Id
    left join D009 on D009_Id=T014_D009_Id
    left join D049 on D049_Id=D009_D049_Id
    left join D001 on D001_Id=D049_D001_Id
    left join D024 on D024_Id=T013_D024_Id
    left join D018 on D018_Id=D024_D018_Id
    left join D006 on D006_Id=T014_D006_Id OR D006_Id=T013_D006_Id
    where T013_C004_Id='{$g['empresaAtual']}' AND T013_Flag_Travar_Nota='S'
    GROUP BY T013_Id,D006_Codigo_CFOP ORDER BY T013_Id
EOT;
	$resultT013 = mysql_query($sqlT013);
	
	$resposta['data'] = <<<EOT
		<div class="titulo"><strong style="font-size: 140%;">Notas fiscais<br /></strong></div>
EOT;
	$i=1;
	while($linhaT013 = mysql_fetch_array($resultT013)){
		
		// Verificando se a quantidade total do produtos alocados é menor que a quantidade total dos produtos da nota
		$sqlT014 = "SELECT SUM(T014_Quantidade_Alocada) as T014_Quantidade_Alocada, SUM(T014_Quantidade) as T014_Quantidade FROM T014 WHERE T014_T013_Id='{$linhaT013['T013_Id']}'";
		$retorno = $sqlT014;
		$resultT014 = mysql_query($sqlT014);
		$linhaT014 = mysql_fetch_array($resultT014);
		$retorno .= "alocada: ".$linhaT014['T014_Quantidade_Alocada']." Total: ".$linhaT014['T014_Quantidade'];
		// echo $retorno;
		// die();
		if($linhaT014['T014_Quantidade_Alocada'] < $linhaT014['T014_Quantidade']){
			if($i<=5){
				$linhaT013['D024_Nome_Empresa'] = substr($linhaT013['D024_Nome_Empresa'], 0,25);
				$resposta['data'] .= <<<EOT
					<button class="bigBtn" id="simDE-{$linhaT013['T013_Id']}">
						NF: {$linhaT013['T013_Numero_Nota_Fiscal']}<br />
						{$linhaT013['D024_Nome_Empresa']}
					</button>
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$('#simDE-{$linhaT013['T013_Id']}').unbind('click').bind('click', function(e) {
				    		modal('Carregando...');
				    		$.getJSON('./receberProdutos.php?opcao=selecionaProd&T013_Id='+ encodeURIComponent('{$linhaT013['T013_Id']}') +'&callback=?', function(request) {
				    			modalRemove();
								$('#contentMenu').empty().html(request.data);
							});
			    		});
					} </script></div>
EOT;
				$i++;
			}
		}
	} 
		$resposta['data'] .= <<<EOT
		<input type="button" class="btn" id="voltar" value="Voltar" style="width:95%;margin-left:5px;"/>
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#voltar').unbind('click').bind('click', function() {
				window.location="./menu.php";
			});
		} </script></div>
EOT;
	
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";