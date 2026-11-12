<?php
namespace hardness;

switch ($g['r'][3]) {
	case 'contentVendasPeriodo':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/rel/relatorios/analitico_vendas/formVendasPeriodo/', '&tab=geral')
		);
		echo gProcessaAcoes($actions);
	break;

	case 'formVendasPeriodo':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '';
		$form_campo_pk = '';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'formVendasPeriodoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/rel/relatorios/analitico_vendas/formVendasPeriodo/';

		// editor
		$form_editor = array(
			'geral' => '{"Estados":{"top":76,"left":59,"width":216,"hidden":0},"dataInicial":{"top":7,"left":22,"width":88,"hidden":0},"dataFinal":{"top":30,"left":29,"width":88,"hidden":0},"C007_Id":{"top":53,"left":27.59375,"width":223,"hidden":0},"ordem":{"top":99,"left":40,"width":128,"hidden":0},"ordem2":{"top":122,"left":80,"width":128,"hidden":0}}'
		);
					
		// novo formulário
		$form = new Formulario('formVendasPeriodo');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'dataInicial' => array('name' => 'dataInicial', 'label'=>'Data inicial', 'type' => 'data', 'reject'=>true, 'value' => date('d/m/Y', strtotime("-1 year", strtotime(date('Y-m-d'))))),	
					'dataFinal' => array('name' => 'dataFinal', 'label'=>'Data final', 'type' => 'data', 'reject'=>true, 'value' => date('d/m/Y')),							
					'C007_Id' => array('name'=>'C007_Id', 'label'=>'Vendedor', 'type'=>'select', 'style'=>'width:220px;', 
						'select' => gGeraSelect('C007', 'C007_Id', 'C007_Primeiro_Nome', 'where C007_Ativo="S" ORDER BY C007_Primeiro_Nome', true)
					),
					'Estados' => array('name' => 'Estados', 'label'=>'UF','type' => 'texto', 'length' => array(28,50), 'extra'=>'title="Preencha as siglas dos estados separadas por vírgula. Ex: PR,SC,RS"'),
					'ordem' => array('name' => 'ordem', 'label' => '<br>Ordem', 'type' => 'select', 'select' => 
						array(
							array('title' => 'CLIENTE', 'value' => 'D024_Nome_Empresa'),
							array('title' => 'VALOR VENDA', 'value' => 'Venda'),
							array('title' => 'VENDEDOR', 'value' => 'C007_Primeiro_Nome'),
							array('title' => 'CIDADE', 'value' => 'D020_Nome_Cidade') 
						)
					),		
					'ordem2' => array('name' => 'ordem2', 'label' => '', 'type' => 'select', 'select' => 
						array(
							array('title' => 'CRESCENTE', 'value' => 'ASC'),
							array('title' => 'DECRESCENTE', 'value' => 'DESC')
						)
					),
					'csv' => array('name' => 'csv', 'type' => 'hidden', 'reject' => true),
				)	
			)
		);
		$form->definirFormularioArray($formArray);
		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				$Data_Inicial = $form->campoValorEnviado('dataInicial');
				$Data_Final = $form->campoValorEnviado('dataFinal');
				$C007_Id = $form->campoValorEnviado('C007_Id');
				$Estados = base64_encode($form->campoValorEnviado('Estados'));
				$Ordem = $form->campoValorEnviado('ordem');
				$Complemento_Ordem = $form->campoValorEnviado('ordem2');
				$csv = $form->campoValorEnviado('csv');
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$('#csv').val('');
						nova_janela('/rel/relatorios/analitico_vendas/relatorio/?ajax=true&Data_Inicial={$Data_Inicial}&Data_Final={$Data_Final}&C007_Id={$C007_Id}&Estados={$Estados}&Ordem={$Ordem}&Complemento_Ordem={$Complemento_Ordem}&CSV={$csv}',0,0,950,750,'no','no','no','no','yes','yes', 'Análitico de Vendas');
					} </script></div>
EOT;
			}
		}
			
		/**
		* PARTE 4: Gerar o formulário
		*/
		//$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, "Confirma a não liberação?");
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		
			
		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());		
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela));
		//echo '<div style="padding:5px">Estorno de título - ID: '.$acaoId.'</div>';
		echo $gerado['pagina'];				
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
			
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="formVendasPeriodoBtn">Imprimir</button>';
		$botoesDir .= '<button onclick="$(\'#csv\').val(\'true\'); $(\'#formVendasPeriodoBtn\').trigger(\'click\');">CSV</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');		
	break;

	case 'relatorio':
		$cCor  = "#FFFFFF";
		$cCor1 = "#C4C4C4";
		$cCor2 = "#FFFFFF";

		// Busca os dados da empresa usuária
		$cSql=mysql_query("SELECT * FROM C004 WHERE C004_Id='{$g['empresaAtual']}'");
		$mEmpresa=mysql_fetch_array($cSql);

		///////////////////////////////////////////////////////////////////////
		//-- FILTRO
		unset($mCondicao);
		unset($mMensagemCondicao);
		$C007_Id = $r_C007_Id;
		$Estados = base64_decode($r_Estados);
		$Ordem = $r_Ordem;
		$Complemento_Ordem = $r_Complemento_Ordem;
		$xData_Inicial = $r_Data_Inicial;
		$xData_Final = $r_Data_Final;
		$CSV = $r_CSV;
		//Arquivo CSV
		if(!empty($r_CSV)){
		    $dir = $g['pathDadosUsuario'] . 'tmp/';
		    is_dir($dir) or mkdir($dir, 0777, true);
		    $file = $dir . "analitico_vendas{$r_Data}.csv";
		    $fp = fopen($file, 'w');
		}

		$Data_Inicial=gCorrigeDataInverte($r_Data_Inicial);
		$Data_Final=gCorrigeDataInverte($r_Data_Final);

		if ($C007_Id>0) {
			$mCondicao[] = " D024_C007_Vendedor_Interno='$C007_Id' ";
			$mMensagemCondicao[]='POR VENDEDOR';
		}
		if ($Estados != '') {
			$Estados = str_replace(' ', '', $Estados);
			$Estados = str_replace('.', ',', $Estados);
			$Estados = strtoupper($Estados);
			$Estados = trim(trim($Estados, '.'),",");
			$Estados =  "(".$Estados .")";	
			$Estados = str_replace("(", "('", $Estados);
			$Estados = str_replace(")", "')", $Estados);
			$mCondicao[] = "D018_UF IN ".str_replace(",", "','", $Estados);

			$mMensagemCondicao[]='POR ESTADO';
		}
		if (isset($mCondicao)) {
			$cCondicao = " and ".implode(" and ",$mCondicao);
			$cMensagemCondicao = implode(" E ",$mMensagemCondicao);
		}
		if ($Ordem == 'Venda') {
		    $cClausula  = " ( ";
		    $cClausula .= " select sum(T007_Valor_Total) ";
		    $cClausula .= " from T007 ";
		    $cClausula .= " left join D006 on D006_Id=T007_D006_Id ";
		    $cClausula .= " where D006_Flag_Entrada_Saida='S' ";
		    $cClausula .= " and T007_Flag_Cancelada <> 'S' ";
		    $cClausula .= " and D006_Flag_Gera_Contas='R' ";
		    $cClausula .= " and D006_Flag_Devolucao<>'S' ";
		    $cClausula .= " and D006_Flag_Venda_Compra_Outros = 'V' ";
		    $cClausula .= " and T007_Data_Emissao >= '$Data_Inicial' ";
		    $cClausula .= " and T007_Data_Emissao <= '$Data_Final' ";
		    $cClausula .= " and T007_D024_Id=D024_Id ";
		    $cClausula .= " ) ";
		    $Ordem = $cClausula;
		}

		list($dia1,$mes1,$ano1)=explode("/",$xData_Inicial);
		list($dia2,$mes2,$ano2)=explode("/",$xData_Final);

		//calculo timestam das duas datas
		$timestamp1 = mktime(0,0,0,$mes1,$dia1,$ano1);
		$timestamp2 = mktime(4,12,0,$mes2,$dia2,$ano2);

		//diminuo a uma data a outra
		$segundos_diferenca = $timestamp1 - $timestamp2;
		//echo $segundos_diferenca;

		//converto segundos em dias
		$dias_diferenca = $segundos_diferenca / (60 * 60 * 24);

		//obtenho o valor absoluto dos dias (tiro o possível sinal negativo)
		$dias_diferenca = abs($dias_diferenca);

		//tiro os decimais aos dias de diferenca
		$dias_diferenca = floor($dias_diferenca);

		$meses_diferenca = floor($dias_diferenca/30);

		if ($meses_diferenca>0) {
			$cDiferenca_Periodo = " - Total de $meses_diferenca Mês(es)";
		} else {
			$cDiferenca_Periodo = " - Total de $dias_diferenca dia(s)";
		}

		$echo = <<<EOT
			<html>
			<title>Relatório Analítico de Vendas</title>
			<head>
			<meta name='ROBOTS' content='NOINDEX,NOFOLLOW'>  
			<meta http-equiv='Pragma' content='no-cache'>  
			<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1'>
			<link rel=stylesheet href="/hardness/classes/classe_tamanho_medio_firefox.css" type="text/css">
			<body bottommargin=0 topmargin=0 rightmargin=0 leftmargin=0 bgcolor='white'>

			        <table align=center border='0' valign=top cellpadding=3 cellspacing=1 bgcolor=white width='95%'>
			        <tr>
			          	<td valign=top>
			           		<table align=center border='0' cellspacing=1 cellpading=0 width='100%' height=10 valign=top>
			            	<tr>
			             	   <td bgcolor=white width=80%>
			              		<table cellspacing=1 cellpading=0 border='0' align=center width=100% height=70>
			               		<tr>
EOT;
       	$pathinfo = pathinfo($g['c004']['C004_Logo_Marca']);
        $Arquivo_Logo = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
		$echo .= <<<EOT
                		<td bgcolor=white align=center valign=center width=220>
                                  <img src='$Arquivo_Logo' align=center>
                               </td>
                		<td align=center>
                 		<table cellspacing=1 cellpading=0 border='0' align=center height=70>
                  		<tr><th><font class=fontemed>{$mEmpresa['C004_Nome_Empresa']}</font></th></tr>
                  		<tr><td align=center><font class=fontemed>{$mEmpresa['C004_Logradouro']} - {$mEmpresa['C004_Numero']} / {$mEmpresa['C004_Complemento']}</font></td></tr>
                  		<tr><td align=center><font class=fontemed>{$mEmpresa['C004_Cep']} - {$mEmpresa['C004_Cidade']} - {$mEmpresa['C004_Estado']}</font></td></tr>
                  		<tr><td align=center><font class=fontemed>Fone/Fax: {$mEmpresa['C004_Telefone']} Fax: {$mEmpresa['C004_Fax']}</font></td></tr>
	                  	<tr><td align=center><font class=fontemed>{$mEmpresa['C004_Site']}</font></td></tr>
	                 	</table>
	                		</td>
	               		</tr>
	              		</table>
	             	</td>
	             		<td bgcolor=white align=center width=20%>
	               		<font class='titulo_relatorio'>Analítco de Vendas por Período
	               		</font>
	             	</td>
	           		</tr>
	           		</table>
	          		</td>
	         		</tr>
	         	   <tr bgcolor=white>
	           		<td>
	             	<table width=100% cellpadding=0 cellspacing=1 border=0 height=30>
	              		<tr>
	                 		<td align=left>
	                   			<font class=fontemed><b>Período de: {$xData_Inicial} até: {$xData_Final} {$cDiferenca_Periodo}
EOT;
					$cDadosSaida.= " - ";
					if (!empty($cMensagemCondicao))
					{
						$cDadosSaida.= $cMensagemCondicao." / ";
					}
					$cDadosSaida.= "ORDENADO POR VOLUME DE VENDAS</b></font>";
					$echo .= '</td><td align=right>';
                	if ($Tipo_Impressao!='EMAIL') {
						$echo .= '<font class=fontemed><i>Impresso:'. date('d/m/Y h:m') .'</i></font>';
                	} else {
						$echo .= '<font class=fontemed><i>Enviado em:'. date('d/m/Y h:m') .'</i></font>';
                	}
				$echo .= <<<EOT
	                	</td>
	                 		<td align=right>
	                 	</td>
	               	</tr>
	             		</table>
	           		</td>
	         	</tr>
	         	<tr>
	          		<td>

	         	<table align=center border='0' bgcolor=#FFFFFF cellpadding=2 cellspacing=1 width='100%' height='100%' style='border-right:0px solid gray;border-bottom:0px solid gray;border-left:0px solid gray;border-top:0px solid gray'>
EOT;
            $cClausula  = " select ";
            $cClausula .= " D024_Id, ";
            $cClausula .= " D024_Nome_Empresa, ";
            $cClausula .= " D020_Nome_Cidade, ";
            $cClausula .= " D018_UF, ";
            //$cClausula .= " T007_Data_Saida, ";
            $cClausula .= " C007_Primeiro_Nome ";
            $cClausula .= " from D024 ";
            $cClausula .= " left join D020 on D020_Id=D024_D020_Id ";
            $cClausula .= " left join D018 on D018_Id=D020_D018_Id ";
            $cClausula .= " left join C007 on C007_Id=D024_C007_Vendedor_Interno ";
            //$cClausula .= " left join T007 on D024_Id=T007_D024_Id ";
            $cClausula .= " where D024_Data_Irregular ='0000-00-00' ";
            //$cClausula .= " and T007_Data_Saida between '$Data_Inicial' and '$Data_Final'";
            $cClausula .= " $cCondicao ";
            $cClausula .= " group by D024_Id ";
            $cClausula .= " order by $Ordem $Complemento_Ordem";
            $cSql_D024 = mysql_query($cClausula);
            echo mysql_error();

            $csvCab = array();

            $count=0;
            while ($mD024 = mysql_fetch_array($cSql_D024))
            {
            	$csvLinha = array();
                if ($cCor==$cCor2)
                {
                    $cCor=$cCor1;
                }
                else
                {
                    $cCor=$cCor2;
                }
                $echo .= '<tr bgcolor='.$cCor.' height=43>
                  		<td align=left valign=top>
                   		<font class=fontemed>'.substr($mD024['D024_Nome_Empresa'],0,20)."<br>".substr($mD024['D020_Nome_Cidade'],0,20)." / ".$mD024['D018_UF']."<br>".substr($mD024[C007_Primeiro_Nome],0,20).'</font>
                  		</td>';
                    $nMes_Atual = $mes1;
                    $nAno_Atual = $ano1;
                    $nTotal_Cliente = 0;

	                if($count==0){
	          			$csvCab[] = utf8_decode("Empresa");
	          		}else{
	          			$csvLinha[] = utf8_decode(substr($mD024['D024_Nome_Empresa'],0,20)."/".substr($mD024['D020_Nome_Cidade'],0,20)." / ".$mD024['D018_UF']."/".substr($mD024[C007_Primeiro_Nome],0,20));
	          		}
                    while (1==1) {

                    	$cClausula  = " select  ";
                    	$cClausula .= " sum(T007_Valor_Total) Venda ";
                    	$cClausula .= " from T007 ";
                    	$cClausula .= " left join D024 on D024_Id=T007_D024_Id ";
                    	$cClausula .= " left join D020 on D020_Id=D024_D020_Id ";
                    	$cClausula .= " left join D006 on D006_Id=T007_D006_Id ";
                    	$cClausula .= " left join D018 on D018_Id=D020_D018_Id ";
                    	$cClausula .= " where D006_Flag_Entrada_Saida='S' ";
                    	$cClausula .= " and T007_Flag_Cancelada <> 'S' ";
                    	$cClausula .= " and D006_Flag_Gera_Contas='R' ";
                    	$cClausula .= " and D006_Flag_Devolucao<>'S' ";
                    	$cClausula .= " and D006_Flag_Venda_Compra_Outros = 'V' ";
                    	$cClausula .= " and month(T007_Data_Emissao) = '$nMes_Atual' ";
                    	$cClausula .= " and year(T007_Data_Emissao) = '$nAno_Atual' ";
                    	$cClausula .= " and T007_D024_Id='$mD024[D024_Id]' ";
                    	$cClausula .= " $cCondicao ";
                    	$cClausula .= " group by T007_D024_Id ";
                    	$cSql_T007 = mysql_query($cClausula);
                    	echo mysql_error();
                    	$mT007 = mysql_fetch_array($cSql_T007);
                    	
                   		$echo .= '<td align="right">
                       		<font class=fontemed><small>'.str_pad($nMes_Atual,2,"0",STR_PAD_LEFT)."/".$nAno_Atual.'</small></font><br><br>
                       		<font class=fontemed>';
                       	if($count==0){
                       		$csvCab[] = utf8_decode(str_pad($nMes_Atual,2,"0",STR_PAD_LEFT)."/".$nAno_Atual);
                       	} else {
                       		$csvLinha[] = utf8_decode(number_format($mT007['Venda'],2,',','.'));
                       	}
                        if ($mT007['Venda']<='0') {
                            $echo .= '<font color="red">';
                        }
                        $echo .= number_format($mT007['Venda'],2,',','.');

                       	$echo .= '</font></td>';                      
                    	$nTotal_Cliente += $mT007['Venda'];
                    	if ($nMes_Atual==$mes2 && $nAno_Atual==$ano2){
                    		
                    	    break;
                    	}
                    	if ($nMes_Atual==12){
                    	    $nMes_Atual=1;
                    	    $nAno_Atual++;
                    	} else{
                    		$nMes_Atual++;
                    	}
                 	}
                 	$csvCab[] = utf8_decode("Total");
					$echo .= '<td align=right valign=bottom>
                       		<font class=fontemed><b>'.number_format($nTotal_Cliente,2,',','.').'</b></font>
                   	</td>
                 	</tr>';
                 	if($count==0){
                    	fputcsv($fp, $csvCab, ';');
                	}else{
                 		$csvLinha[] = utf8_decode(number_format($nTotal_Cliente,2,',','.'));
                		fputcsv($fp, $csvLinha, ';');
                	}
                	$count++;
            }
            //Persiste o arquivo no servidor
		    if(!empty($r_CSV)){
		        fclose($fp);
		    }
      	$echo .= '</table>';

      	//Verifica se o formato é CSV ou impresso
		if(!empty($r_CSV)){
		    header("Location: /hardness3/{$g['pathDadosUsuario']}tmp/analitico_vendas{$r_Data}.csv");
		} else {
		    echo $echo;
		}
	break;
}