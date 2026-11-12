<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {
	/**
	 * Cabeçalho Cliente
	 * Chamado em: cad001tabCadastro
	 */
	case 'cad002CabecalhoProduto':
		// Select
if(isset($r_D049_Id) && $r_D049_Id > 0){
	$sql = <<<EOT
		SELECT 
			D001_Codigo_Produto,
            D001_Descricao_Produto,
            D082_Marca,
            D001_Flag_Pre_Cadastro,
			D001_Flag_Ativo,
			D005_Observacao,
			D049_Modelo
		FROM 
			D001
			LEFT JOIN D005 on D005_Id=D001_D005_Id
            LEFT JOIN D049 on D049_D001_Id=D001_Id AND D049_Id = '{$r_D049_Id}'
            LEFT JOIN D082 on D082_Id=D049_D082_Id
		WHERE
			(D001_Id='{$r_acaoId}')
EOT;
} else {
	$sql = <<<EOT
		SELECT 
			D001_Codigo_Produto,
            D001_Descricao_Produto,
            D082_Marca,
            D001_Flag_Pre_Cadastro,
			D001_Flag_Ativo,
			D005_Observacao
		FROM 
			D001
			LEFT JOIN D005 on D005_Id=D001_D005_Id
            LEFT JOIN D049 on D049_D001_Id=D001_Id
            LEFT JOIN D082 on D082_Id=D049_D082_Id
		WHERE
			(D001_Id='{$r_acaoId}')
EOT;
}
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);
		
		// Registro vazios = FALTA
		foreach ($row as &$row_) {
			if (empty($row_)) { 
				$row_ = '<span style="color: var(--BGVermelho);"></span>';
			}
		}
		if($row['D001_Flag_Pre_Cadastro'] == "N"){
			$preCad = "Não";
		} else {
			$preCad = "Sim";
		}
		if($row['D001_Flag_Ativo'] == "N"){
			$ativo = "Não";
		} else {
			$ativo = "Sim";
		}
		$D005_Observacao = substr($row['D005_Observacao'], 0, 25);
		if(!empty($D005_Observacao)){
			$D005_Observacao = " - ".$D005_Observacao;
		}
		// Print
		if(isset($r_D049_Id) && $r_D049_Id > 0){
			echo <<<EOT
				<div class="contentCabecalhoLayout" style="height: 40px;">
					<table class="tabela ui-state-default ui-corner-all" style="height: 100%">
						<tr class="linhaDesc">
							<td>Código</td>
							<td>Sub-Item</td>
							<td>Produto</td>
							<td>Marca</td>
						</tr>
						<tr class="linhaVal">
							<td>{$row['D001_Codigo_Produto']}</td>
							<td>{$row['D049_Modelo']}</td>
							<td>{$row['D001_Descricao_Produto']}</td>
							<td>{$row['D082_Marca']}</td>
						</tr>
					</table>
				</div>
EOT;
		} else {
			echo <<<EOT
				<div class="contentCabecalhoLayout" style="height: 40px;">
					<table class="tabela ui-state-default ui-corner-all" style="height: 100%">
						<tr class="linhaDesc">
							<td>Código</td>
							<td>Produto</td>
						</tr>
						<tr class="linhaVal">
							<td>{$row['D001_Codigo_Produto']}</td>
							<td>{$row['D001_Descricao_Produto']}</td>
						</tr>
					</table>
				</div>
EOT;
		}
	break;

	case 'cad002EspecificacoesProduto':
		$sqlD001 = mysql_query("SELECT * FROM D001 WHERE D001_Id = '{$r_acaoId}'");
		$D001 = mysql_fetch_array($sqlD001);

		echo <<<EOT
			<div style="border-radius: 5px; margin: 0.1px; padding: 3.5px; border: 1px solid #000; height: 91.5%; width: 97.5%; background-color: #FFF; overflow: auto;">
				<b>{$D001['D001_Descricao_Produto']}</b><br />
				{$D001['D001_Especificacoes']}
			</div>
EOT;
	break;

	case 'cad002CabecalhoListaFornecedor':
		// Select
		$sql = mysql_query("SELECT * FROM D070 LEFT JOIN D024 ON D024_Id = D070_D024_Id LEFT JOIN D082 ON D082_Id = D070_D082_Id WHERE D070_Id = '{$r_acaoId}'");
		$res = mysql_fetch_array($sql);
		// Print
		echo <<<EOT
			<div class="contentCabecalhoLayout">
				<table class="tabela ui-state-default ui-corner-all">
					<tr class="linhaDesc">
						<td>Lista</td>
						<td>Código</td>
						<td>Fornecedor</td>
						<td>Fantasia</td>
						<td>Marca</td>
					</tr>
					<tr class="linhaVal">
						<td>{$r_acaoId}</td>
						<td>{$res['D024_Id']}</td>
						<td>{$res['D024_Nome_Empresa']}</td>
						<td>{$res['D024_Nome_Fantasia']}</td>
						<td>{$res['D082_Marca']}</td>
					</tr>
				</table>
			</div>
EOT;

	break;

	case 'demonstrativoCusto':
		global $g;

		if(!empty($r_acaoId) && $r_acaoId > 0){
		  $r_T001_Id = $r_acaoId;
		}

		$C004=mysql_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
		$mC004=mysql_fetch_array($C004);

		$cSql_T001 = mysql_query("select *
		                        from T001 
		                   left join T014 on T014_Id=T001_T014_Id
		                   left join T013 on T013_Id=T014_T013_Id
		                   left join D006 on D006_Id=T001_D006_Id 
		           left join D024 on D024_Id=T001_D024_Id
		                   left join T219 on T219_Id=T001_T219_Id
		                   left join T218 on T218_Id=T219_T218_Id
		           left join D009 on D009_Id=T001_D009_Id
				   left join T001A ON T001_Id=T001A_T001_Id
		                       where T001_Id='{$r_T001_Id}'");
		$mT001 = mysql_fetch_array($cSql_T001);

		if (empty($mT001[T001_Demonstrativo_Custo])) {
		  //msg_alerta('Demonstração não diponivel para esse histórico');
		  //echo "<script>self.close();</script>";
		  //exit;
		}

		//$C007=mysql_query("select * from C007 where C007_Id='$Usuario_Atual'");
		//$mC007=mysql_fetch_array($C007);
		     
		unset($mDemonstrativo_Custo);
		$mDemonstrativo_Custo[] = " ,,Preço,,".number_format($mT001[T001_Valor_Preco_Unitario],5,',','.');
		$Custo_Final=$mT001[T001_Valor_Preco_Unitario];
		$mDemonstrativo_Custo[] = "-,,Desconto nos Itens,,".number_format($mT001[T001_Valor_Desconto_Unitario],5,',','.');
		$Custo_Final-=$mT001[T001_Valor_Desconto_Unitario];
		$mDemonstrativo_Custo[] = " ,,Preço Liquido,,".number_format($mT001[T001_Valor_Preco_Unitario]-$mT001[T001_Valor_Desconto_Unitario],5,',','.');
		//$mDemonstrativo_Custo[] = ",, ,,";
		//if ($mC004[C004_Flag_Empresa_Simples]!='1' and $mT001[D006_Flag_Deb_Crd_ICMS]=='S')
		//{
		        //$mDemonstrativo_Custo[] = "-,,$mT001[T001_Aliquota_ICMS]% ICMS,,".number_format($Custo_Final*($mT001[T001_Aliquota_ICMS]/100),5,',','.');
		        $mDemonstrativo_Custo[] = " ,,$mT001[T001_Aliquota_ICMS]% ICMS,,".number_format($mT001[T001_Valor_ICMS],5,',','.');
		    //$Custo_Final-=round($mT001[T001_Valor_ICMS],2);
		//}

        $mDemonstrativo_Custo[] = " ,,$mT001[T001_Aliquota_PIS]% PIS,,".number_format($mT001[T001_Valor_PIS],5,',','.');
        $mDemonstrativo_Custo[] = " ,,$mT001[T001_Aliquota_COFINS]% COFINS,,".number_format($mT001[T001_Valor_COFINS],5,',','.');

		//if ($mT001[D006_Flag_Credita_IPI]!='S')
		//{
		        $mDemonstrativo_Custo[] = "+,,$mT001[T001_Aliquota_IPI]% IPI,,".number_format($mT001[T001_Valor_IPI],5,',','.');
		    $Custo_Final+=$mT001[T001_Valor_IPI];
		//}

		/*
		if ($mT001[D006_Flag_Substituicao_Tributaria] == 'S'
		&& $mT001[D006_Flag_Venda_Compra_Outros]=='T'
		&& $mT001[T013_C004_Id]==2)
		{
		        $mDemonstrativo_Custo[] = " ,,ICMS Substituição,,".number_format($mT001[T014_Valor_ICMS_Substituicao_Unitario],5,',','.');

		}
		else
		{ */

		//$mDemonstrativo_Custo[] = "+,,ICMS Dif.Aliquotas,,".number_format($mT001[T001_Valor_ICMS_Diferenca_Aliquotas],5,',','.');
		//$Custo_Final+=$mT001[T001_Valor_ICMS_Diferenca_Aliquotas];

		if ($mT001[D006_Flag_Substituicao_Tributaria]=='S')
		{
		    $mDemonstrativo_Custo[] = "+,,$mT001[T001_Percentual_ST]% ST,,".number_format($mT001[T001_Valor_ST],5,',','.');
		    $Custo_Final+=$mT001[T001_Valor_ST];
		} else if($mT001[D006_Flag_Substituicao_Tributaria]!='S' && $mT001[T001_Valor_ST] > 0){
			$mDemonstrativo_Custo[] = " ,,<font color='red'>$mT001[T001_Percentual_ST]% ST</font>,,<font color='red'>".number_format($mT001[T001_Valor_ST],5,',','.')."</font>";
		} else {
			$mDemonstrativo_Custo[] = " ,,$mT001[T001_Percentual_ST]% ST,,".number_format($mT001[T001_Valor_ST],5,',','.')."";
		}

		$mDemonstrativo_Custo[] = "+,,$mT001[T001_Aliquota_ICMS_Diferenca_Aliquotas]% DIFAL ICMS,,".number_format($mT001[T001_Valor_ICMS_Diferenca_Aliquotas],5,',','.')."";
        $Custo_Final+=$mT001[T001_Valor_ICMS_Diferenca_Aliquotas];

		//}
		$mDemonstrativo_Custo[] = "+,,Frete,,".number_format($mT001[T001_Valor_Frete],5,',','.');
		$Custo_Final+=round($mT001[T001_Valor_Frete],2);
		$mDemonstrativo_Custo[] = "+,,Seguro,,".number_format($mT001[T001_Valor_Seguro],5,',','.');
		$Custo_Final+=round($mT001[T001_Valor_Seguro]/$mT001[T001_Quantidade],2);
		$mDemonstrativo_Custo[] = "+,,Despesas Acessórias,,".number_format($mT001[T001_Valor_Despesas_Acessorias],5,',','.');
		$Custo_Final+=round($mT001[T001_Valor_Despesas_Acessorias],2);
		//$mDemonstrativo_Custo[] = ",, ,,";
		if($mT001[T001A_Valor_Acrescimo_Custo] > 0){
			$mDemonstrativo_Custo[] = "+,,Acrescimo,," . number_format($mT001[T001A_Valor_Acrescimo_Custo], 5, ',', '.');
			$Custo_Final+=round($mT001[T001A_Valor_Acrescimo_Custo],2);
		}
		$mDemonstrativo_Custo[] = "=,,<font style='font-weight:bold'>Custo Final,,<font style='font-weight:bold'>".number_format($Custo_Final,5,',','.')."</font>";

		if($mT001['D009_Flag_Custo_Utilizado'] == 'M'){
			$style = "style='margin-bottom: -10px;'";
			$mDemonstrativo_Custo[] = "=,,<font style='font-weight:bold; color: blue;'>Médio,,<font style='font-weight:bold'>".gCorrigeNumero($mT001['D009_Valor_Custo_Medio_Unitario'], 5)."</font>";
		}


		//if ($mC007[C007_Flag_Vendedor]=='XXX')
		/*
		if ($Corrigir_Custo=='S')
		{
		  if (number_format($mT001[T001_Valor_Preco_Unitario],2)!=number_format($Custo_Final,2))
		  {
		      //$mDemonstrativo_Custo[] = "!,,ERRO! Custo Final correto,,".number_format($Custo_Final,2,',','.');
		    mysql_query("update T001 set T001_Valor_Preco_Unitario=$Custo_Final where T001_T014_Id=$mT001[T014_Id]");
		  }
		  if (number_format($mT001[T001_Quantidade]!=number_format($mT001[T001_Quantidade])))
		  {
		      //$mDemonstrativo_Custo[] = "!,,ERRO! Custo Final correto,,".number_format($Custo_Final,2,',','.');
		    mysql_query("update T001 set T001_Quantidade=$mT001[T001_Quantidade] where T001_T014_Id=$mT001[T014_Id]");
		  }
		}
		if ($mT001[D049_Flag_Nacional_Importado]=='I') 
		{
		  $mT001[T014_Valor_Custo_Final_Unitario]=$mT001[T014_Valor_Custo_Final_Unitario]/$mT001[T013_Valor_Dolar];
		  $mDemonstrativo_Custo[] = "=,,Dólar,,".number_format($mT001[T013_Valor_Dolar],4,',','.');
		  $mDemonstrativo_Custo[] = "=,,Custo Final US,,".number_format($mT001[T014_Valor_Custo_Final_Unitario],2,',','.');
		}

		$mDemonstrativo_Custo[] = "";
		$mDemonstrativo_Custo[] = ",,% Redução ICMS,,".number_format($mT001[T014_Percentual_Reducao_ICMS],2,',','.');
		$mDemonstrativo_Custo[] = ",,Base ICMS,,".number_format($mT001[T014_Valor_Base_ICMS]/$mT001[T001_Quantidade],2,',','.');

		$mDemonstrativo_Custo[] = "";
		$mDemonstrativo_Custo[] = ",,% Redução IPI,,".number_format($mT001[T014_Percentual_Reducao_IPI],2,',','.');
		$mDemonstrativo_Custo[] = ",,Base IPI,,".number_format($mT001[T014_Valor_Base_IPI]/$mT001[T001_Quantidade],2,',','.');
		*/

		$mT001['D024_Nome_Empresa'] = '-'.substr($mT001['D024_Nome_Empresa'], 0, 12);
		$mT001['D006_Descricao'] = ($mT001['D006_Descricao'] != '') ? substr($mT001['D006_Descricao'],0,17) . ' - ' : '';

		//if($mT001['T001_T014_Id'] > 0){
		  //$eventoClick = "nova_janela('/hardness3/hardness/nota_recebida/gerar_danfe.php?T013_Id={$mT001['T013_Id']}',0,0,800,500,'no','no','no','no','yes','yes',0);";
		  $eventoClick = "abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Histórico do Produto', '/ven/ven001/content/ven001ContentHistoricoProduto/', '&acaoId=' + encodeURIComponent('{$mT001['D009_D049_Id']}') + '&tabela=T001', [990,605]);";
		//} else if($mT001['T001_T219_Id'] > 0){
		//    $eventoClick = "abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Cotação de compra', '/est/est015/content/est015ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent('{$mT001['T218_Id']}') + '&tabela=T218', [990,605]);";
		//}

		//echo "<div style='font-size: 14px;white-space: pre-wrap; border-bottom: 1px solid black' align='center' class='fonte'><a href=\"#\" onclick=\"{$eventoClick}\" style='cursor:pointer'><b>$mT001[T001_Descricao_Operacao] - $mT001[D024_Nome_Empresa]</a></b><br />$mT001[D006_Codigo_CFOP] - $mT001[D006_Descricao]</div>";
		$mT001['T001_Data_Lancamento'] = (gCorrigeData($mT001['T001_Data_Lancamento']) != '') ? '-'.gCorrigeData($mT001['T001_Data_Lancamento']) : '';
		echo "<div style='font-size: 11px;white-space: pre-wrap; border-bottom: 1px solid black' align='center' class='fonte'><a style=\"text-decoration: none; color: black;\" href=\"#\" onclick=\"{$eventoClick}\" style='cursor:pointer'><b>$mT001[T001_Descricao_Operacao]$mT001[D024_Nome_Empresa]{$mT001['T001_Data_Lancamento']}</a></b></div>";

		$Demonstrativo_Custo=implode("\n",$mDemonstrativo_Custo);

		$mDemonstrativo=explode("\n",$Demonstrativo_Custo);

		if($r_popUp == true){
		  echo "<table width=80% cellspacing=0 cellpadding=0 align=center style='font-size: 14px;'>";
		} else {
		  echo "<table width=100% cellspacing=0 cellpadding=0 align=center {$style}>";
		}
		for ($Linha=0;$Linha<count($mDemonstrativo);$Linha++) {

		    echo "<tr>";
		    $mColuna=explode(",,",$mDemonstrativo[$Linha]);
		    for ($Coluna=0;$Coluna<count($mColuna);$Coluna++) {
		    	if($style != '' && $Linha+4 > count($mDemonstrativo)){
					echo "<td style='padding: 1px 3px;'";
		    	} else {
		    		echo "<td style='padding: 2px 3px;'";
		    	}
		        if ($Coluna+1==count($mColuna)) {
		           echo " align=right";
		        }
		        echo ">";
					echo "<font class=fonte style='font-size:11px'>$mColuna[$Coluna]</font>";
		        echo "</td>";
		    }
		    if (empty($mDemonstrativo[$Linha])) {
		        echo "<td><hr></td>";
		    }
		    echo "</tr>";
		}
		echo "</table>";/*


		echo "<p align='center' class='fonte'>$mT001[D006_Codigo_CFOP] - $mT001[D006_Descricao]</p>";

		$corLinha = '#D3EFFC';

		if($r_popUp == true){
		  echo "<table width=80% cellspacing=0 cellpadding=0 align=center style='font-size: 14px;'>";
		} else {
		  echo "<table width=100% cellspacing=0 cellpadding=0 align=center>";
		}
		for ($Linha=0;$Linha<count($mDemonstrativo);$Linha++) {

		    if ($corLinha=='#D3EFFC'){
		        $corLinha = 'white';
		    } else {
		        $corLinha = 'white';
		    }
		    echo "<tr bgcolor='$corLinha'>";
		    $mColuna=explode(",,",$mDemonstrativo[$Linha]);
		    for ($Coluna=0;$Coluna<count($mColuna);$Coluna++) {
		        echo "<td";
		        if ($Coluna+1==count($mColuna)) {
		           echo " align=right";
		        }
		        echo ">";
		          echo "<font class=fonte>$mColuna[$Coluna]</font>";
		        echo "</td>";
		    }
		    if (empty($mDemonstrativo[$Linha])) {
		        echo "<td><hr></td>";
		    }
		    echo "</tr>";
		}
		echo "</table>";


		//if ($Usuario_Atual=='63')
		//{
		//  echo "<p align=center><input type='button' class='botao' value='Corrigir custo' onclick=\"location.replace('T001_demonstracao_custo.php3?Abre_Janela=$Abre_Janela&T001_Id=$T001_Id&Corrigir_Custo=S')\">";
		//}
		/*
		$mDemonstrativo=explode("\n",$mT001[T001_Demonstrativo_Custo]);

		echo "<br><br><br>";

		echo "<p align=center>";
		  echo "<font class=fonte><b>Demostração gravada na data de entrada da NF</b></font>";
		echo "</p>"
		;

		echo "<table width=90% align=center bgcolor=silver>";
		for ($Linha=0;$Linha<count($mDemonstrativo);$Linha++) {

		    echo "<tr>";
		    $mColuna=explode(",,",$mDemonstrativo[$Linha]);
		    for ($Coluna=0;$Coluna<count($mColuna);$Coluna++) {
		        echo "<td";
		        if ($Coluna+1==count($mColuna)) {
		           echo " align=right";
		        }
		        echo ">";
		          echo "<font class=fonte>$mColuna[$Coluna]</font>";
		        echo "</td>";
		    }
		    if (empty($mDemonstrativo[$Linha])) {
		        echo "<td><hr></td>";
		    }
		    echo "</tr>";
		}
		echo "</table>";*/
	break;

	case 'retornoAtualizacaoNuvemshop':
		//$cache = unserialize(apc_fetch("cacheTray-{$g['usuarioAtual']}"));
		//$cache = unserialize(file_get_contents("/tmp/cacheTray-{$g['usuarioAtual']}"));
		//apc_delete("cacheTray-{$g['usuarioAtual']}");
		//@unlink("/tmp/cacheTray-{$g['usuarioAtual']}");

		$html.=<<<EOT
            <style>
                table, th {
                	font-size:12px;
                    text-align:center;
                }
                td{
                	vertical-align: bottom;
                	margin: 5px 15px;
                    padding:10px 5px;
                    border-bottom: 1px solid #000;
                    text-align:left;
                }
                tr{
                    border-collapse:collapse;
                }   
                font{
                	font-size:13px;
                }
            </style>
EOT;
		echo "<font><b><br/>Produtos Atualizados</font>";

		echo "<br><br><br>";

        $html.=<<<EOT
           
EOT;

	
       	echo $html;

	break;

	case 'retornoAtualizacaoEstoqueTray':
		//$cache = unserialize(apc_fetch("cacheTray-{$g['usuarioAtual']}"));
		$cache = unserialize(file_get_contents("/tmp/cacheTray-{$g['usuarioAtual']}"));
		//apc_delete("cacheTray-{$g['usuarioAtual']}");
		@unlink("/tmp/cacheTray-{$g['usuarioAtual']}");

		$html.=<<<EOT
            <style>
                table, th {
                	font-size:12px;
                    text-align:center;
                }
                td{
                	vertical-align: bottom;
                	margin: 5px 15px;
                    padding:10px 5px;
                    border-bottom: 1px solid #000;
                    text-align:left;
                }
                tr{
                    border-collapse:collapse;
                }   
                font{
                	font-size:13px;
                }
            </style>
EOT;
		echo "<font><b><br/>Produtos retornados Tray: </b>".$r_totalRegistros."</font>";
		echo "<font><b><br/>Produtos atualizados: </b>".$r_atualizados."</font>";
		echo "<font><b><br/>Produtos com erro: </b>".$r_erro."</font>";
		echo "<font><b><br/>Produtos encontrados no sistema: </b>".$r_encontrados."</font>";
		echo "<font><b><br/>Produtos não encontrados sistema: </b>".$r_naoEncontrados."</font><br>";
        echo "<font color='red'><b><br/>Em caso de erros acesse: Menu Outros->Log Erros</font>";

		echo "<br><br><br>";

        $html.=<<<EOT
            <table>
                <tr class="border-bottom">
                 	<th>ITEM</th>
                    <th>EAN</th>
                    <th>NOME</th> 
                    <th>ESTOQUE <br> ANTERIOR <br> TRAY </th>
                    <th> ESTOQUE <br> SISTEMA </th>
                    <th>STATUS ENVIO</th>
                 	<th>ERRO</th>
                </tr>
                 <tr>
                    <td></td>
                    <td></td>
                    <td style="width: 330px;"></td> 
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
EOT;
        $count = 0;       
		foreach ($cache as $key => $produto) {
			$count++;
			if(empty($produto['erro'])){
				$styleErro = "style='text-align:center;'";
				$erro = " - ";
			}else{
				$styleErro = "style='color: var(--BGVermelho)'"; 
				$erro = $produto['erro'];
			}


			if(stristr($produto['retornoTray'], "Atualizado")){
				$styleRetorno = "style='color:green'";
			}else{
				$styleRetorno = "style='color: var(--BGVermelho)'";
			}

			$html .= <<<EOT
                <tr>	
                	<td>{$count}</td>
                    <td>{$produto['ean']}</td>
                    <td style="width: 330px;">{$produto['nome']}</td> 
                    <td style="text-align:center;">{$produto['estoqueTray']}</td>
                    <td style="text-align:center;">{$produto['estoqueSistema']}</td>
                    <td $styleRetorno>{$produto['retornoTray']}</td>
                    <td $styleErro>{$erro}</td>
                </tr>
EOT;

		}
	
	$html.=<<<EOT
            </table>
EOT;

	
       	echo $html;

	break;

	case 'demonstrativoCustoTabela':
		global $g;

		$C004=mysql_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
		$mC004=mysql_fetch_array($C004);

		$cSql_D009 = mysql_query("select *
		                        from D009
		                   left join D069 on D069_Id=D009_D069_Id
						   left join D070 on D070_Id=D069_D070_Id
						   left join D006 on D006_Id=D069_D006_Id
						   left join D024 on D024_Id=D070_D024_Id
		                       where D009_Id='{$r_D009_Id}' AND D069_Flag_Tabela_Principal='S'");
		$mD069 = mysql_fetch_array($cSql_D009);

		if (empty($mD069[T001_Demonstrativo_Custo])) {
			//msg_alerta('Demonstração não diponivel para esse histórico');
			//echo "<script>self.close();</script>";
			//exit;
		}

		//$C007=mysql_query("select * from C007 where C007_Id='$Usuario_Atual'");
		//$mC007=mysql_fetch_array($C007);
		     
		unset($mDemonstrativo_Custo);
		$mDemonstrativo_Custo[] = " ,,Preço,,".number_format($mD069[D069_Valor_Preco_Original],5,',','.');
		$Custo_Final=$mD069[D069_Valor_Preco_Original];
		$mDemonstrativo_Custo[] = "-,,Desconto nos Itens,,".number_format($mD069[D069_Valor_Preco_Original]*($mD069[D069_Percentual_Desconto]/100),5,',','.');
		$Custo_Final-=$mD069[D069_Valor_Preco_Original]*($mD069[D069_Percentual_Desconto]/100);
		$mDemonstrativo_Custo[] = " ,,Preço Liquido,,".number_format($mD069[D069_Valor_Preco_Unitario],5,',','.');
		//$mDemonstrativo_Custo[] = ",, ,,";
		if ($mC004[C004_Flag_Empresa_Simples]!='1') // and $mD069[D006_Flag_Deb_Crd_ICMS]=='S'
		{
			//$mDemonstrativo_Custo[] = "-,,$mD069[D069_Aliquota_ICMS]% ICMS,,".number_format($Custo_Final*($mD069[T014_Aliquota_ICMS]/100),5,',','.');
		        $mDemonstrativo_Custo[] = " ,,$mD069[D069_Aliquota_ICMS]% ICMS,,".number_format($mD069[D069_Valor_ICMS]/$mD069[D069_Quantidade],5,',','.');
			//$Custo_Final-=round($mD069[D069_Valor_ICMS]/$mD069[D069_Quantidade],2);
		}
		$mDemonstrativo_Custo[] = " ,,$mD069[D069_Aliquota_PIS]% PIS,,".number_format($mD069[D069_Valor_PIS]/$mD069[D069_Quantidade],5,',','.');
		$mDemonstrativo_Custo[] = " ,,$mD069[D069_Aliquota_COFINS]% COFINS,,".number_format($mD069[D069_Valor_COFINS]/$mD069[D069_Quantidade],5,',','.');

		//if ($mD069[D006_Flag_Credita_IPI]!='S')
		//{
		        $mDemonstrativo_Custo[] = "+,,$mD069[D069_Aliquota_IPI]% IPI,,".number_format($mD069[D069_Valor_IPI]/$mD069[D069_Quantidade],5,',','.');
		    $Custo_Final+=$mD069[D069_Valor_IPI]/$mD069[D069_Quantidade];
		//}

		/*
		if ($mD069[D006_Flag_Substituicao_Tributaria] == 'S'
		&& $mD069[D006_Flag_Venda_Compra_Outros]=='T'
		&& $mD069[T013_C004_Id]==2)
		{
		        $mDemonstrativo_Custo[] = " ,,ICMS Substituição,,".number_format($mD069[T014_Valor_ICMS_Substituicao_Unitario],5,',','.');

		}
		else
		{ */

		//$mDemonstrativo_Custo[] = "+,,ICMS Dif.Aliquotas,,".number_format($mD069[T014_Valor_ICMS_Diferenca_Aliquotas]/$mD069[T014_Quantidade],5,',','.');
		//$Custo_Final+=$mD069[T014_Valor_ICMS_Diferenca_Aliquotas]/$mD069[T014_Quantidade];

		//if ($mD069[D069_Flag_ST]=='S')
			//{
		    $mDemonstrativo_Custo[] = "+,,$mD069[D069_Percentual_ST]% ST,,".number_format($mD069[D069_Valor_ICMS_Substituicao_Retencao],5,',','.');
		    $Custo_Final+=$mD069[D069_Valor_ICMS_Substituicao_Retencao];
		//}
		//}

		$mDemonstrativo_Custo[] = "+,,$mD069[D069_Aliquota_ICMS_Diferenca_Aliquotas]% ICMS DIFAL,,".number_format($mD069[D069_Valor_ICMS_Diferenca_Aliquotas],5,',','.');
		$Custo_Final+=$mD069[D069_Valor_ICMS_Diferenca_Aliquotas];

		$mDemonstrativo_Custo[] = "+,,Frete,,".number_format($mD069[D069_Valor_Frete],5,',','.');
		$Custo_Final+=$mD069[D069_Valor_Frete];
		$mDemonstrativo_Custo[] = "+,,Seguro,,".number_format($mD069[T014_Valor_Seguro]/$mD069[T014_Quantidade],5,',','.');
		$Custo_Final+=round($mD069[T014_Valor_Seguro]/$mD069[T014_Quantidade],2);
		$mDemonstrativo_Custo[] = "+,,Despesas Acessórias,,".number_format($mD069[T014_Valor_Despesas_Acessorias]/$mD069[T014_Quantidade],5,',','.');
		$Custo_Final+=round($mD069[T014_Valor_Despesas_Acessorias]/$mD069[T014_Quantidade],2);
		//$mDemonstrativo_Custo[] = ",, ,,";
		$mDemonstrativo_Custo[] = "=,,<font style='font-weight:bold'>Custo Final,,<font style='font-weight:bold'>".number_format($Custo_Final,5,',','.')."</font>";
		//$mDemonstrativo_Custo[] = "=,,Custo Final,,".number_format($Custo_Final,5,',','.');


		//if ($mC007[C007_Flag_Vendedor]=='XXX')
		/*
		if ($Corrigir_Custo=='S')
		{
			if (number_format($mD069[T001_Valor_Custo_Unitario],2)!=number_format($Custo_Final,2))
			{
			    //$mDemonstrativo_Custo[] = "!,,ERRO! Custo Final correto,,".number_format($Custo_Final,2,',','.');
				mysql_query("update T001 set T001_Valor_Custo_Unitario=$Custo_Final where T001_T014_Id=$mD069[T014_Id]");
			}
			if (number_format($mD069[T014_Quantidade]!=number_format($mD069[T001_Quantidade])))
			{
			    //$mDemonstrativo_Custo[] = "!,,ERRO! Custo Final correto,,".number_format($Custo_Final,2,',','.');
				mysql_query("update T001 set T001_Quantidade=$mD069[T014_Quantidade] where T001_T014_Id=$mD069[T014_Id]");
			}
		}
		if ($mD069[D049_Flag_Nacional_Importado]=='I') 
		{
			$mD069[T014_Valor_Custo_Final_Unitario]=$mD069[T014_Valor_Custo_Final_Unitario]/$mD069[T013_Valor_Dolar];
			$mDemonstrativo_Custo[] = "=,,Dólar,,".number_format($mD069[T013_Valor_Dolar],4,',','.');
			$mDemonstrativo_Custo[] = "=,,Custo Final US,,".number_format($mD069[T014_Valor_Custo_Final_Unitario],2,',','.');
		}

		$mDemonstrativo_Custo[] = "";
		$mDemonstrativo_Custo[] = ",,% Redução ICMS,,".number_format($mD069[T014_Percentual_Reducao_ICMS],2,',','.');
		$mDemonstrativo_Custo[] = ",,Base ICMS,,".number_format($mD069[T014_Valor_Base_ICMS]/$mD069[T014_Quantidade],2,',','.');

		$mDemonstrativo_Custo[] = "";
		$mDemonstrativo_Custo[] = ",,% Redução IPI,,".number_format($mD069[T014_Percentual_Reducao_IPI],2,',','.');
		$mDemonstrativo_Custo[] = ",,Base IPI,,".number_format($mD069[T014_Valor_Base_IPI]/$mD069[T014_Quantidade],2,',','.');
		*/

		$mD069['D024_Nome_Empresa'] = substr($mD069['D024_Nome_Empresa'], 0, 19);
		$mD069['D006_Descricao'] = substr($mD069['D006_Descricao'],0,20);

		//echo "<div style='font-size: 14px; border-bottom:1px solid black' align='center' class='fonte'><b>Tabela - $mD069[D024_Nome_Empresa]</b><br />$mD069[D006_Codigo_CFOP] - $mD069[D006_Descricao]</div>";
		if($mD069['D069_D070_Id'] > 0){
			$descricao = '-'.$mD069['D069_D070_Id'].'-'.$mD069['D024_Nome_Empresa'];
		}
		echo "<div style='font-size: 11px; border-bottom:1px solid black; color: black' align='center' class='fonte'><b>TAB{$descricao}</b></div>";
		$Demonstrativo_Custo=implode("\n",$mDemonstrativo_Custo);

		$mDemonstrativo=explode("\n",$Demonstrativo_Custo);

		echo "<table width=100% cellspacing=0 cellpadding=0 align=center>";

		for ($Linha=0;$Linha<count($mDemonstrativo);$Linha++) {

		    echo "<tr>";
		    $mColuna=explode(",,",$mDemonstrativo[$Linha]);
		    for ($Coluna=0;$Coluna<count($mColuna);$Coluna++) {
				echo "<td style='padding: 2px 3px;'";
		        if ($Coluna+1==count($mColuna)) {
		           echo " align=right";
		        }
		        echo ">";
				echo "<font class=fonte style='font-size:11px'>$mColuna[$Coluna]</font>";
		        echo "</td>";
		    }
		    if (empty($mDemonstrativo[$Linha])) {
		        echo "<td><hr></td>";
		    }
		    echo "</tr>";
		}
		echo "</table>";/*


		echo "<p align='center' class='fonte'>$mD069[D006_Codigo_CFOP] - $mD069[D006_Descricao]</p>";

		$corLinha = '#D3EFFC';

		if($r_popUp == true){
			echo "<table width=80% cellspacing=0 cellpadding=0 align=center style='font-size: 14px;'>";
		} else {
			echo "<table width=100% cellspacing=0 cellpadding=0 align=center>";
		}
		for ($Linha=0;$Linha<count($mDemonstrativo);$Linha++) {

		    if ($corLinha=='#D3EFFC'){
		        $corLinha = 'white';
		    } else {
		        $corLinha = 'white';
		    }
		    echo "<tr bgcolor='$corLinha'>";
		    $mColuna=explode(",,",$mDemonstrativo[$Linha]);
		    for ($Coluna=0;$Coluna<count($mColuna);$Coluna++) {
		        echo "<td";
		        if ($Coluna+1==count($mColuna)) {
		           echo " align=right";
		        }
		        echo ">";
		          echo "<font class=fonte>$mColuna[$Coluna]</font>";
		        echo "</td>";
		    }
		    if (empty($mDemonstrativo[$Linha])) {
		        echo "<td><hr></td>";
		    }
		    echo "</tr>";
		}
		echo "</table>";


		//if ($Usuario_Atual=='63')
		//{
		//	echo "<p align=center><input type='button' class='botao' value='Corrigir custo' onclick=\"location.replace('T001_demonstracao_custo.php3?Abre_Janela=$Abre_Janela&T001_Id=$T001_Id&Corrigir_Custo=S')\">";
		//}
		/*
		$mDemonstrativo=explode("\n",$mD069[T001_Demonstrativo_Custo]);

		echo "<br><br><br>";

		echo "<p align=center>";
		  echo "<font class=fonte><b>Demostração gravada na data de entrada da NF</b></font>";
		echo "</p>"
		;

		echo "<table width=90% align=center bgcolor=silver>";
		for ($Linha=0;$Linha<count($mDemonstrativo);$Linha++) {

		    echo "<tr>";
		    $mColuna=explode(",,",$mDemonstrativo[$Linha]);
		    for ($Coluna=0;$Coluna<count($mColuna);$Coluna++) {
		        echo "<td";
		        if ($Coluna+1==count($mColuna)) {
		           echo " align=right";
		        }
		        echo ">";
		          echo "<font class=fonte>$mColuna[$Coluna]</font>";
		        echo "</td>";
		    }
		    if (empty($mDemonstrativo[$Linha])) {
		        echo "<td><hr></td>";
		    }
		    echo "</tr>";
		}
		echo "</table>";*/

	break;

	case 'demonstrativoCustoCompras':
		global $g;

		$C004=mysql_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
		$mC004=mysql_fetch_array($C004);
		$style = '';
		if($r_tabela == 'T219'){
			$sqlT219 = mysql_query("SELECT ((round(T219_Valor_ST,2)/(round(T219_Valor_Total_Preco,2)+round(T219_Valor_IPI,2)))*100) AS ST, D024_Nome_Empresa, T218_Data_Emissao, T219.* FROM T219 LEFT JOIN T218 ON T218_Id=T219_T218_Id LEFT JOIN D024 ON D024_Id=T218_D024_Id WHERE T219_Id = '{$r_acaoId}'");
			$T219 = mysql_fetch_array($sqlT219);

			unset($mDemonstrativo_Custo);
			$mDemonstrativo_Custo[] = " ,,Preço,,".gCorrigeNumero($T219['T219_Valor_Preco_Unitario'], 5);
			$Custo_Final=$T219['T219_Valor_Preco_Unitario'];
			$mDemonstrativo_Custo[] = "-,,Desconto nos Itens,,".gCorrigeNumero($T219['T219_Valor_Preco_Unitario']*($T219['T219_Percentual_Desconto']/100), 5);
			$Custo_Final-=$T219['T219_Valor_Preco_Unitario']*($T219['T219_Percentual_Desconto']/100);
			$mDemonstrativo_Custo[] = " ,,Preço Liquido,,".gCorrigeNumero($T219['T219_Valor_Preco_Unitario'], 5);

			if($mC004['C004_Flag_Empresa_Simples'] != '1') {
				$mDemonstrativo_Custo[] = " ,,{$T219['T219_Aliquota_ICMS']}% ICMS,," . gCorrigeNumero($T219['T219_Valor_ICMS']/$T219['T219_Quantidade'], 5);
			}
			$mDemonstrativo_Custo[] = " ,,{$T219['T219_Aliquota_PIS']}% PIS,," . gCorrigeNumero($T219['T219_Valor_PIS']/$T219['T219_Quantidade'], 5);
			$mDemonstrativo_Custo[] = " ,,{$T219['T219_Aliquota_COFINS']}% COFINS,," . gCorrigeNumero($T219['T219_Valor_COFINS']/$T219['T219_Quantidade'], 5);
			
			$mDemonstrativo_Custo[] = "+,,{$T219['T219_Aliquota_IPI']}% IPI,,".gCorrigeNumero($T219['T219_Valor_IPI']/$T219['T219_Quantidade'], 5);
			$Custo_Final+=$T219['T219_Valor_IPI']/$T219['T219_Quantidade'];

			$mDemonstrativo_Custo[] = "+,,{$T219['ST']}% ST,,".gCorrigeNumero($T219['T219_Valor_ST']/$T219['T219_Quantidade'], 5);
			$Custo_Final+=$T219['T219_Valor_ST']/$T219['T219_Quantidade'];

			$mDemonstrativo_Custo[] = "+,,Frete,,".gCorrigeNumero(($T219['T219_Valor_Frete']+$T219['T219_Valor_Frete_Total'])/$T219['T219_Quantidade'], 5);
			$Custo_Final+=($T219['T219_Valor_Frete']+$T219['T219_Valor_Frete_Total'])/$T219['T219_Quantidade'];
			$mDemonstrativo_Custo[] = "+,,Seguro,,".gCorrigeNumero($T219[T014_Valor_Seguro]/$T219[T014_Quantidade], 5);
			$Custo_Final+=round($T219[T014_Valor_Seguro]/$T219[T014_Quantidade],2);
			$mDemonstrativo_Custo[] = "+,,Despesas Acessórias,,".gCorrigeNumero($T219['T219_Valor_Despesas_Acessorias_Unitario'], 5);
			$Custo_Final+=round($T219['T219_Valor_Despesas_Acessorias_Unitario'],2);
			$mDemonstrativo_Custo[] = "=,,<font style='font-weight:bold'>Custo Final,,<font style='font-weight:bold'>".gCorrigeNumero($Custo_Final, 5)."</font>";

			if($T219['T219_Quantidade_Estoque'] > 0 && $T219['T219_Valor_Custo_Medio_Unitario'] > 0){
				$style = "style='margin-bottom: -10px;'";
				$mDemonstrativo_Custo[] = "=,,<font style='font-weight:bold; color: blue;'>Médio (Estq: ".gCorrigeNumero($T219['T219_Quantidade_Estoque'])."),,<font style='font-weight:bold'>".gCorrigeNumero($T219['T219_Valor_Custo_Medio_Unitario'], 5)."</font>";
			}

			$T219['D024_Nome_Empresa'] = substr($T219['D024_Nome_Empresa'] ,0 ,12);
			$T219['T218_Data_Emissao'] = gCorrigeData($T219['T218_Data_Emissao']);
			echo "<div style='font-size: 11px; border-bottom:1px solid black; color: black' align='center' class='fonte'><b>COT-{$T219['T219_T218_Id']}-{$T219['D024_Nome_Empresa']}-{$T219['T218_Data_Emissao']}</b></div>";
		} else {
			$sqlT225 = mysql_query("SELECT ((T225_Valor_ICMS_Substituicao_Retencao/(T225_Valor_Total_Preco+T225_Valor_IPI))*100) AS ST, D024_Nome_Empresa, T224_Data_Emissao, T225.* FROM T225 LEFT JOIN T224 ON T224_Id=T225_T224_Id LEFT JOIN D024 ON D024_Id=T224_D024_Id WHERE T225_Id = '{$r_acaoId}'");
			$T225 = mysql_fetch_array($sqlT225);

			unset($mDemonstrativo_Custo);
			$T225['T225_Percentual_Desconto'] = 0;
			$mDemonstrativo_Custo[] = " ,,Preço,,".gCorrigeNumero($T225['T225_Valor_Preco_Unitario'], 5);
			$Custo_Final=$T225['T225_Valor_Preco_Unitario'];
			$mDemonstrativo_Custo[] = "-,,Desconto nos Itens,,".gCorrigeNumero($T225['T225_Valor_Preco_Unitario']*($T225['T225_Percentual_Desconto']/100), 5);
			$Custo_Final-=$T225['T225_Valor_Preco_Unitario']*($T225['T225_Percentual_Desconto']/100);
			$mDemonstrativo_Custo[] = " ,,Preço Liquido,,".gCorrigeNumero($T225['T225_Valor_Preco_Unitario'], 5);

			if($mC004['C004_Flag_Empresa_Simples'] != '1') {
				$mDemonstrativo_Custo[] = " ,,{$T225['T225_Aliquota_ICMS']}% ICMS,," . gCorrigeNumero($T225['T225_Valor_ICMS']/$T225['T225_Quantidade'], 5);
			}
			$mDemonstrativo_Custo[] = " ,,{$T225['T225_Aliquota_PIS']}% PIS,," . gCorrigeNumero($T225['T225_Valor_PIS']/$T225['T225_Quantidade'], 5);
			$mDemonstrativo_Custo[] = " ,,{$T225['T225_Aliquota_COFINS']}% COFINS,," . gCorrigeNumero($T225['T225_Valor_COFINS']/$T225['T225_Quantidade'], 5);
			
			$mDemonstrativo_Custo[] = "+,,{$T225['T225_Aliquota_IPI']}% IPI,,".gCorrigeNumero($T225['T225_Valor_IPI']/$T225['T225_Quantidade'], 5);
			$Custo_Final+=$T225['T225_Valor_IPI']/$T225['T225_Quantidade'];

			$mDemonstrativo_Custo[] = "+,,{$T225['ST']}% ST,,".gCorrigeNumero($T225['T225_Valor_ICMS_Substituicao_Retencao']/$T225['T225_Quantidade'], 5);
			$Custo_Final+=$T225['T225_Valor_ICMS_Substituicao_Retencao']/$T225['T225_Quantidade'];

			$mDemonstrativo_Custo[] = "+,,Frete,,".gCorrigeNumero($T225['T225_Valor_Frete'], 5);
			$Custo_Final+=$T225['T225_Valor_Frete'];
			$mDemonstrativo_Custo[] = "+,,Seguro,,".gCorrigeNumero($T225[T014_Valor_Seguro]/$T225[T014_Quantidade], 5);
			$Custo_Final+=round($T225[T014_Valor_Seguro]/$T225[T014_Quantidade],2);
			$mDemonstrativo_Custo[] = "+,,Despesas Acessórias,,".gCorrigeNumero($T225['T225_Valor_Despesas_Acessorias_Unitario'], 5);
			$Custo_Final+=round($T225['T225_Valor_Despesas_Acessorias_Unitario'],2);
			$mDemonstrativo_Custo[] = "=,,<font style='font-weight:bold'>Custo Final,,<font style='font-weight:bold'>".gCorrigeNumero($Custo_Final, 5)."</font>";

			if($T225['T225_Quantidade_Estoque'] > 0 && $T225['T225_Valor_Custo_Medio_Unitario'] > 0){
				$style = "style='margin-bottom: -10px;'";
				$mDemonstrativo_Custo[] = "=,,<font style='font-weight:bold; color: blue;'>Médio (Estq: ".gCorrigeNumero($T225['T225_Quantidade_Estoque'])."),,<font style='font-weight:bold'>".gCorrigeNumero($T225['T225_Valor_Custo_Medio_Unitario'], 5)."</font>";
			}

			$T225['D024_Nome_Empresa'] = substr($T225['D024_Nome_Empresa'], 0, 12);
			$T225['T224_Data_Emissao'] = gCorrigeData($T225['T224_Data_Emissao']);
			echo "<div style='font-size: 11px; border-bottom:1px solid black; color: black' align='center' class='fonte'><b>OC-{$T225['T225_T224_Id']}-{$T225['D024_Nome_Empresa']}-{$T225['T224_Data_Emissao']}</b></div>";
		}

		$Demonstrativo_Custo=implode("\n",$mDemonstrativo_Custo);

		$mDemonstrativo=explode("\n",$Demonstrativo_Custo);

		echo "<table width=100% cellspacing=0 cellpadding=0 align=center {$style}>";

		for ($Linha=0;$Linha<count($mDemonstrativo);$Linha++) {

		    echo "<tr>";
		    $mColuna=explode(",,",$mDemonstrativo[$Linha]);
		    for ($Coluna=0;$Coluna<count($mColuna);$Coluna++) {
		    	if($style != '' && $Linha+4 > count($mDemonstrativo)){
					echo "<td style='padding: 1px 3px;'";
		    	} else {
		    		echo "<td style='padding: 2px 3px;'";
		    	}
		        if ($Coluna+1==count($mColuna)) {
		           echo " align=right";
		        }
		        echo ">";
				echo "<font class=fonte style='font-size:11px'>$mColuna[$Coluna]</font>";
		        echo "</td>";
		    }
		    if (empty($mDemonstrativo[$Linha])) {
		        echo "<td><hr></td>";
		    }
		    echo "</tr>";
		}
		echo "</table>";
	break;

	case 'cad002CabecalhoMontagem':
		// Select
		$resultado = mysql_query("SELECT * FROM T001 WHERE T001_Id = '{$r_T001_Id}'");
		$row = mysql_fetch_assoc($resultado);
		
		// Registro vazios = FALTA
		foreach ($row as &$row_) {
			if (empty($row_)) { 
				$row_ = '<span style="color: var(--BGVermelho);"></span>';
			}			
		}
		$row['T001_Quantidade'] = gCorrigeNumero($row['T001_Quantidade']);
		echo <<<EOT
			<div class="contentCabecalhoLayout">
				<table class="tabela ui-state-default ui-corner-all">
					<tr class="linhaDesc">
						<td>Montagem</td>
						<td>Quantidade</td>
					</tr>
					<tr class="linhaVal">
						<td>{$row['T001_Descricao_Operacao']}</td>
						<td>{$row['T001_Quantidade']}</td>
					</tr>
				</table>
			</div>
EOT;
	break;

	case 'descontosPedidosCliente':
			$sqlT005 = mysql_query("SELECT T005_Id, T005_Data_Emissao, T005_Valor_Desconto, C007_Primeiro_Nome
							FROM T005 
							LEFT JOIN C007 ON C007_Id=T005_C007_Id_Vendedor_Interno
							WHERE T005_D024_Id = '{$r_acaoId}'
							AND (T005_Flag_Status = '' OR T005_Flag_Status = 0)");
			$retorno = <<<EOT
				<table cellpadding=0 cellspacing=0 style='border: 1px solid #000'>
					<tr>
						<td style='border: 1px solid #000'>Pedido</td>
						<td style='border: 1px solid #000'>Emissão</td>
						<td style='border: 1px solid #000'>Desconto</td>
						<td style='border: 1px solid #000'>Vendedor</td>
					</tr>
EOT;
			while($rowT005 = mysql_fetch_array($sqlT005)){
				$rowT005['T005_Data_Emissao'] = gCorrigeData($rowT005['T005_Data_Emissao']);
				$rowT005['T005_Valor_Desconto'] = gCorrigeNumero($rowT005['T005_Valor_Desconto']);
				$retorno .= <<<EOT
					<tr>
						<td style='border: 1px solid #000'>{$rowT005['T005_Id']}</td>
						<td style='border: 1px solid #000'>{$rowT005['T005_Data_Emissao']}</td>
						<td align="right" style='border: 1px solid #000'>{$rowT005['T005_Valor_Desconto']}</td>
						<td style='border: 1px solid #000'>{$rowT005['C007_Primeiro_Nome']}</td>
					</tr>
EOT;
			}
			$retorno .= "</table>";
			echo $retorno;
	break;

	case 'cad002CabecalhoSimilar':
		// Select
		$resultado = mysql_query("SELECT * FROM D001 WHERE D001_Id = '{$r_D001_Id}'");
		$row = mysql_fetch_assoc($resultado);

		// Registro vazios = FALTA
		foreach ($row as &$row_) {
			if (empty($row_)) { 
				$row_ = '<span style="color: var(--BGVermelho);"></span>';
			}
		}
		echo <<<EOT
			<div class="contentCabecalhoLayout">
				<table class="tabela ui-state-default ui-corner-all">
					<tr class="linhaDesc">
						<td>Código</td>
						<td>Descrição</td>
					</tr>
					<tr class="linhaVal">
						<td>{$row['D001_Codigo_Produto']}</td>
						<td>{$row['D001_Descricao_Produto']}</td>
					</tr>
				</table>
			</div>
EOT;
	break;

	case 'cad002InfoCustoVendaProduto':
		$extra = unserialize(base64_decode($r_extra));
		$dataAtualizacaoCusto = $extra[0];
		$dataUltimaVenda = $extra[1];
		$custoUtilizado = $extra[2];

		$diasAtualizacaoCusto = round(abs(strtotime(date("Y-m-d"))-strtotime($dataAtualizacaoCusto))/86400);
		$diasUltimaVenda = round(abs(strtotime(date("Y-m-d"))-strtotime($dataUltimaVenda))/86400);

		$prazoValidadeCusto01 = $g['C031']['prazoValidadeCusto01'];
		$prazoValidadeCusto02 = $g['C031']['prazoValidadeCusto02'];
		$prazoValidadeCusto03 = $g['C031']['prazoValidadeCusto03'];
		$prazoUltimaVenda01 = $g['C031']['prazoUltimaVenda01'];
		$prazoUltimaVenda02 = $g['C031']['prazoUltimaVenda02'];
		$prazoUltimaVenda03 = $g['C031']['prazoUltimaVenda03'];

		if($diasAtualizacaoCusto <= $prazoValidadeCusto01 && $prazoValidadeCusto01 > 0){
			$backgroundCusto = " var(--celula-verde); color: var(--letra2)";
		} else if($diasAtualizacaoCusto <= $prazoValidadeCusto02 && $prazoValidadeCusto02 > 0){
			$backgroundCusto = "#FFFF94";
		} else if($diasAtualizacaoCusto <= $prazoValidadeCusto03 && $prazoValidadeCusto03 > 0){
			$backgroundCusto = "#FF6666";
		} else {
			$backgroundCusto = "#9166BC";
		}

		if($diasUltimaVenda <= $prazoUltimaVenda01 && $prazoUltimaVenda01 > 0){
			$backgroundVenda = " var(--celula-verde); color: var(--letra2)";
		} else if($diasUltimaVenda <= $prazoUltimaVenda02 && $prazoUltimaVenda02 > 0){
			$backgroundVenda = "#FFFF94";
		} else if($diasUltimaVenda <= $prazoUltimaVenda03 && $prazoUltimaVenda03 > 0){
			$backgroundVenda = "#FF6666";
		} else {
			$backgroundVenda = "#9166BC";
		}

		if($dataAtualizacaoCusto == "0000-00-00 00:00:00"){
			$mensagemAtualizacaoCusto = "Data de atualização do custo não registrada";
		} else {
			$dataAtualizacaoCusto = gCorrigeData($dataAtualizacaoCusto, true);
			$mensagemAtualizacaoCusto = "Custo atualizado {$dataAtualizacaoCusto} ({$diasAtualizacaoCusto} dias)";
		}

		if($dataUltimaVenda == '0000-00-00'){
			$mensagemUltimaVenda = "Este produto nunca foi vendido nesta empresa.";
		} else {
			$dataUltimaVenda = gCorrigeData($dataUltimaVenda);
			$mensagemUltimaVenda = "Última venda {$dataUltimaVenda} ({$diasUltimaVenda} dias)";
		}
		switch ($custoUtilizado) {
			case 'T':
				$tipoCusto = 'tabela';
			break;
			case 'U':
				$tipoCusto = 'da última compra';
			break;
			case 'M':
				$tipoCusto = 'médio';
			break;
		}
		$custoUtilizado = ($custoUtilizado == '') ? '?' : $custoUtilizado;
		echo <<<EOT
			<div style="color: #000000; font-weight: normal; font-size: 13px;">
			<center>
				<b>{$mensagemAtualizacaoCusto}</b><br>
				<b>{$mensagemUltimaVenda}</b><br><br>
			</center>
			<div style="background-color:  var(--celula-verde); color: var(--letra2); width: 15px; text-align: center; float: left;"><font color="#000000;">{$custoUtilizado}</font></div><span style="float: left; margin-left: 5px;"> Custo {$tipoCusto} atualizado em até {$prazoValidadeCusto01} dias</span><br>
			<div style="background-color: #FFFF94; width: 15px; text-align: center; float: left;"><font color="#000000;">{$custoUtilizado}</font></div><span style="float: left; margin-left: 5px;"> Custo {$tipoCusto} atualizado a mais de {$prazoValidadeCusto01} dias</span><br>
			<div style="background-color: #FF6666; width: 15px; text-align: center; float: left;"><font color="#000000;">{$custoUtilizado}</font></div><span style="float: left; margin-left: 5px;"> Custo {$tipoCusto} atualizado a mais de {$prazoValidadeCusto02} dias</span><br>
			<div style="background-color: #9166BC; width: 15px; text-align: center; float: left;"><font color="#000000;">{$custoUtilizado}</font></div><span style="float: left; margin-left: 5px;"> Custo {$tipoCusto} atualizado a mais de {$prazoValidadeCusto03} dias</span><br>
			<br>
			<div style="background-color:  var(--celula-verde); color: var(--letra2); width: 15px; text-align: center; float: left;"><font color="#000000;">V</font></div><span style="float: left; margin-left: 5px;"> Data da última venda em até {$prazoUltimaVenda01} dias</span><br>
			<div style="background-color: #FFFF94; width: 15px; text-align: center; float: left;"><font color="#000000;">V</font></div><span style="float: left; margin-left: 5px;"> Data da última venda a mais de {$prazoUltimaVenda01} dias</span><br>
			<div style="background-color: #FF6666; width: 15px; text-align: center; float: left;"><font color="#000000;">V</font></div><span style="float: left; margin-left: 5px;"> Data da última venda a mais de {$prazoUltimaVenda02} dias</span><br>
			<div style="background-color: #9166BC; width: 15px; text-align: center; float: left;"><font color="#000000;">V</font></div><span style="float: left; margin-left: 5px;"> Data da última venda a mais de {$prazoUltimaVenda03} dias</span><br>
			</div>
EOT;
	break;

	case 'cad002ManualCSVProdutos':
	echo<<<EOT
	
	<style>
		.tabela{
			border:1px solid black;
			border-colapse: colapse;	
			cellspace="10";
			font-size:20px;
			padding:10px 15px;
			background-color:#000;
			text-align:left;
		}
		.LinhaReferencia{
			text-align:center;
			background: rgba(76, 175, 80, 0);
			padding:0px 20px 0px 27px;
		}
		.linhaTabela{				
			font-size:15px;
			padding:10px 15px;
			background-color:#FFF;
			text-align:center;
			

		}	
	</style>

	<div style="padding-left:10px;">
	<h2>Modelo de documento CSV</h2>
	<h3>Observações:</h3>
	<p style="font-size:12px">-Abaixo está o formato modelo do documento para a importação!
	<br/>-O documento deve ter <strong>22 colunas</strong> ao todo, caso seja necessário modificar o número de colunas para a importação, contactar o suporte técnico.
	<br/>-Os nomes das colunas devem estar necessáriamente na primeira linha(1) do documento e ter a mesma gráfia do documento modelo.
	<br/>-A extesão do documento deve ser CSV (separado por vírgulas)(*.csv)
	<br/>-Ao final do documento não deve haver nenhuma linha em branco.
	<br/>-<strong>Os campos com * são de preenchimento obrigatório.</strong>
	<br/>-O campo <strong>Codigo_Fornecedor</strong> é de preenchimeno obrigatório caso o campo <strong>Codigo_Produto_Fornecedor</strong> esteja preenchido.
	<br/>-O campo <strong>Codigo_Fornecedor</strong> é de preenchimeno obrigatório caso o campo <strong>EAN_Produto_Fornecedor</strong> esteja preenchido.
	<br/>-Nos campos numéricos utilizar apenas vírgula como separador decimal e não utilizar ponto. Ex: 2,50; 2500,36
    <br/>-O campo <strong>Pre-Cadastro</strong> deve ser preenchdo com S (para sim) ou N (para Não), caso esteja em branco será cadastrado automaticamente como 'S'.
	</p>
	</div>
	<table cellspacing=1px bgcolor="black">		
		<tr>		
			<td class="linhaTabela">*Codigo</td>
			<td class="linhaTabela">*Descricao</td>
			<td class="linhaTabela">Departamento</td>
			<td class="linhaTabela">Setor</td>
			<td class="linhaTabela">Grupo</td>
			<td class="linhaTabela">Sub-Grupo</td>
			<td class="linhaTabela">NCM</td>
			<td class="linhaTabela">Origem</td>
			<td class="linhaTabela">Marca</td>
			<td class="linhaTabela">Unidade</td>
			<td class="linhaTabela">Custo</td>
			<td class="linhaTabela">Preco1</td>
			<td class="linhaTabela">Preco2</td>
			<td class="linhaTabela">Preco3</td>
			<td class="linhaTabela">Preco4</td>
			<td class="linhaTabela">Peso_Liquido</td>
			<td class="linhaTabela">Peso_Bruto</td>
			<td class="linhaTabela">Locacao</td>
			<td class="linhaTabela">Codigo_Fornecedor</td>
			<td class="linhaTabela">Codigo_Produto_Fornecedor</td>
			<td class="linhaTabela">EAN_Produto_Fornecedor</td>
			<td class="linhaTabela">Link Foto</td>
			<td class="linhaTabela">Pre_Cadastro</td>
		</tr>
		<tr>			
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
		</tr>
		<tr>			
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
		</tr>
		<tr>			
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
		</tr>
		<tr>		
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
		</tr>	
	</table>
EOT;
	break;

	case 'cad002ManualCSVProdutosFornecedores':
	echo<<<EOT
	
	<style>
		.tabela{
			border:1px solid black;
			border-colapse: colapse;	
			cellspace="10";
			font-size:20px;
			padding:10px 15px;
			background-color:#000;
			text-align:left;
		}
		.LinhaReferencia{
			text-align:center;
			background: rgba(76, 175, 80, 0);
			padding:0px 20px 0px 27px;
		}
		.linhaTabela{				
			font-size:15px;
			padding:10px 15px;
			background-color:#FFF;
			text-align:center;
			

		}	
	</style>

	<div style="padding-left:10px;">
	<h2>Modelo de documento CSV</h2>
	<h3>Observações:</h3>
	<p style="font-size:12px">
	-O documento deve ter <strong>2 colunas</strong> ao todo, caso seja necessário modificar o número de colunas para a importação, contactar o suporte técnico.
	<br/>-Os nomes das colunas devem estar necessáriamente na primeira linha(1) do documento e ter a mesma gráfia do documento modelo.
	<br/>-A extesão do documento deve ser CSV (separado por vírgulas)(*.csv)
	<br/>-Ao final do documento não deve haver nenhuma linha em branco.
	<font color="red"><br/> -Linhas duplicadas serão inseridas apenas <strong>uma vez</strong> no banco de dados.</font>
	</p>
	</div>
	<table cellspacing=1px bgcolor="black">		
		<tr>		
			<td class="linhaTabela">Codigo_Produto</td>
			<td class="linhaTabela">Codigo_Fornecedor</td>
		</tr>
		<tr>			
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
		</tr>
		<tr>			
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
		</tr>
		<tr>			
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
		</tr>
		<tr>		
			<td class="linhaTabela">---</td>
			<td class="linhaTabela">---</td>
		</tr>	
	</table>
EOT;
	break;

	case 'importarEstoquePreco':
		global $g;
		set_time_limit(0);

		$C033 = mysql_query("select C033_Id,
		                            C033_D024_Id,
		                            C033_Login,
						    	    C033_Senha,
									C033_Url,
									C033_Metodo
						       from C033
							  where C033_Id >= '{$r_C033_Id}' 
						   order by C033_Id");
		
		$mC033 = mysql_fetch_array($C033);

		$r_C033_Id = $mC033['C033_Id'];

		$completo = $_GET['completo'];
		$senha = trim($mC033['C033_Senha']);
		$login = trim($mC033['C033_Login']);
		$url   = trim($mC033['C033_Url']);
		$D024_Id = $mC033['C033_D024_Id'];
		$Metodo = trim($mC033['C033_Metodo']);

		echo "<font style='font-size:22px;font-weight:bold'>Importação API Estoque e Preço - Fornecedor ID $D024_Id</font><br><Br>";
		echo "<font style='font-size:14px'>Id API: $r_C033_Id</font><br>";
		echo "<font style='font-size:14px'>Url: $url</font><br>";
		echo "<font style='font-size:14px'>Login: $login</font><br>";
		echo "<font style='font-size:14px'>Senha: $senha</font><br>";
		echo "<font style='font-size:14px'>Método: $Metodo</font><br><br>";
		
		if (!isset($r_data)) {

			echo "<br><br><br><br><font style='font-size:18px;font-wheight:bold'>Aguarde...</font><br><br>";
				
			$ch = curl_init($url); 
		
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "$Metodo");
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic '.base64_encode("$login:$senha")));
			$data = curl_exec($ch);
		
			$info = curl_getinfo($ch);
			curl_close($ch);

			// Gravar arquivo no disco
			$arquivoDir = $g['pathDados'] . 'tmp/';
			is_dir($arquivoDir) OR mkdir($arquivoDir, 0777, true);
			$arquivoNome = "{$arquivoDir}dataOVD.json";
			@unlink($arquivoNome);
			$h = fopen($arquivoNome, 'w');
			fwrite($h, $data);
			fclose($h);
			
			echo <<<EOT
			  <script>
			    var url_atual = window.location.href;
				location.replace(url_atual + '&data=true&C033_Id=' + $r_C033_Id);
			  </script>
EOT;
		} else {

			$C033 = mysql_query("select C033_Id from C033 order by C033_Id desc");
			$mC033 = mysql_fetch_array($C033);

            if ($r_C033_Id >= $mC033['C033_Id']){
				$timeOut = 3600;
				$r_C033_Id = 1;
			} else {
				$timeOut = 30;
				$r_C033_Id++;
			}

			$httpRefresh = '?ajax=true&C033_Id=' . $r_C033_Id;
				
			echo '<META HTTP-EQUIV="Refresh" CONTENT="' . $timeOut . ';URL=' . $httpRefresh . '">';
			echo "<font style='font-family:arial'>";
	
			echo "<br><br> " . $httpRefresh . " - " . $timeOut . "<br><br>";

			// Gravar arquivo no disco
			$arquivoDir = $g['pathDados'] . 'tmp/';
			$arquivoNome = "{$arquivoDir}dataOVD.json";
			$h = fopen($arquivoNome, 'r');
			$data = fread($h, filesize($arquivoNome));
			fclose($h);

			$mEstoque = (array) json_decode($data);
		
			$estoque = (array) json_decode( json_encode($mEstoque['detalheProdutos']));

			require_once('bibliotecas/classes/CAD002.php');
			$CAD002 = new CAD002;

			$opcoes = array('campoNCM' => false, 'campoIPI' => false, 'campoNossoCodigo' => false, 'campoOrigemProduto' => false, 'campoCFOP' => false, 'campoICMS' => false, 'campoICMSST' => false);

			$date = date('Y-m-d H:i:s');
		
			echo "<b>Inicio - $date </b><br><br>";
		
			if (!$data) {
				echo "<font color='red'><b>PROCESSO NÃO FUNCIONOU, AVISE O SUPORTE TECNICO.</font></b><br/><br/>";
				print_r($info).'<br><br>';
				echo $data;
			} else {
				if (empty($estoque)){
					echo "A conexão não retornou dados. Aguarde próxima tentativa.<br><br>";	
					echo "Retorno do fornecedor: <br><br>";
					echo $data.'<br><br>';
					echo "<a href='http://".$_SERVER['HTTP_HOST']."/cad/cad002/outro/importarEstoquePreco/?ajax=true'><input type=button value='Tentar novamente'></a><br><br>";
					echo "<br><br><br>";
					$date = date('Y-m-d H:i:s');
		
					echo "<b>Fim - $date | Total itens: $count</b>";
				}else{
					//Como vai estar usando o Completo desde o começo para pegar o valor em estoque esse botão fica desnecessário
					// echo "<a href='https://".$_SERVER['HTTP_HOST']."/cad/cad002/outro/importarEstoquePreco/?ajax=true&completo=true'><input type=button value='Forçar importação completa'></a><br><br>";
		
					// Refaz a conexão devido ao timeout e a demora na resposta 
					//mysqli_close($g['conexaoBanco']);
					
					//$conexaoTemp = @mysqli_connect('hardness-prd.cqfcavbrwplk.us-east-1.rds.amazonaws.com', 'root', 'EUXZ1WVY6g3mqE17OQ6Csd6dUAp6BTVMO','e229');

					//$mysqli = new \mysqli("hardness-prd.cqfcavbrwplk.us-east-1.rds.amazonaws.com", "root", "EUXZ1WVY6g3mqE17OQ6Csd6dUAp6BTVMO", "e229");
					// mysql_select_db('e229', $conexaoTemp);

					//echo $mysqli->error;
		
					$count = 0;
					$countupdate=0;
					$countContinue=0;
					$countProdutoNaoExistente=0;
					$ProdutosInexistentes='';
					foreach ($estoque as $key1 => $value1) {
						$value1 = objectToArray($value1);
						$value1['codigo'] = trim($value1['codigo']);
						$D069 = mysql_query("SELECT D069_Id,D069_Quantidade_Estoque,D069_Valor_Preco_Original, D069_D009_Id, D069_Codigo_Produto FROM D069 LEFT JOIN D070 ON D070_Id=D069_D070_Id WHERE D070_D024_Id=$D024_Id AND D069_Codigo_Produto='{$value1['codigo']}' AND D070_Flag_Ativo='S'");
						echo mysql_error();
						if(mysql_num_rows($D069) > 0){
							while ($mD069 = mysql_fetch_array($D069)) {
								$updateEstoque = '';
								$updatePreco   = '';
								if (number_format($mD069['D069_Quantidade_Estoque'],3)!=number_format($value1['saldo'],3)) {
									$updateEstoque = " D069_Quantidade_Estoque_Anterior='{$mD069['D069_Quantidade_Estoque']}' ,D069_Quantidade_Estoque = '{$value1['saldo']}', D069_Data_Atualizacao_Estoque=now() ";
								}
								if ($mD069['D069_Valor_Preco_Original']!=$value1['valor']) {
									if (!empty($updateEstoque)) {
										$updateEstoque .= ', ';
									}
									$updatePreco = "  D069_Valor_Preco_Original_Anterior='{$mD069['D069_Valor_Preco_Original']}', D069_Valor_Preco_Original = '{$value1['valor']}', D069_Data_Atualizacao_Preco=now() ";
								}
								if (!empty($updateEstoque) || !empty($updatePreco)){
									mysql_query("update D069 set $updateEstoque $updatePreco where D069_Id = '{$mD069['D069_Id']}'");
									echo mysql_error();
									if (!empty($updatePreco)){
										//$CAD002->atualizarAliquotasProdutos($mD069['D069_Id'], $opcoes);
										mysql_query("call D069_Gravar_Totalizacao('{$mD069['D069_Id']}', '{$g['usuarioAtual']}')");
										$CAD002->D001_reprocessa_historico(false, $mD069['D069_D009_Id'], 10);
										$produtosPrecoAtualizado .= $mD069['D069_Codigo_Produto']." - ";
									}
								}
							}
							$countupdate++;
						} else {
							$ProdutosInexistentes .= $value1['codigo']." - ";
							$countProdutoNaoExistente++;
						}
						$count++;
					}
					echo "Produtos não atualizados (quantidade de estoque da OVD igual a quantidade de estoque no sistema): ".$countContinue."<br />";
					echo "Produtos atualizados: ".$countupdate."<br />";
					echo "Produtos que não existem na tabela da OVD: ".$countProdutoNaoExistente."<br />";
					echo "<br><br><br>";
					$date = date('Y-m-d H:i:s');
		
					echo "<b>Fim - $date | Total itens: $count</b>";
		
					echo "<h2>Lista de produtos com preço atualizado: </h2>";
					echo $produtosPrecoAtualizado;
					echo "</br>";
					echo "<h2>Lista de produtos inexistentes em tabelas <b>ATIVAS</b> do fornecedor <b>ID $D024_Id</b>: </h2>";
					echo $ProdutosInexistentes;

					
				}
			}
		}

	break;

	case 'importarEstoque':

		global $g;

		set_time_limit(0);

		$completo = $_GET['completo'];

		echo '<META HTTP-EQUIV="Refresh" CONTENT="3600;URL=https://'.$_SERVER['HTTP_HOST'].'/cad/cad002/outro/importarEstoque/?ajax=true">';

		echo "<font style='font-family:arial'>";

		if (isset($completo)){
			$completo = '?completo=true';
		}
			
		//$url = "http://ecommerce.ovd.com.br:7075/ecommerce/api/estoque$completo";
		$url = "https://app.ovd.com.br/ecommerce/api/estoque$completo";
		$ch = curl_init($url); 

		//$ch = curl_init("https://app.ovd.com.br/ecommerce/api/playlist/consultarestoque?token=52ac4c1f-886c-4490-8f79-bf6bb425b7b0");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic '.base64_encode("14213283000164:delta19@")));  // Alteração (gmp2019@) em 23/09/2019  chamado Jean 38795
		//curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
		// Senha antiga: GMP@5515
		$data = curl_exec($ch);
		//echo "<pre>";
		//print_r($data);
		//echo "</pre>";
		//die();
		$info = curl_getinfo($ch);
		curl_close($ch);

		echo "<font style='font-size:22px;font-weight:bold'>Importação OVD</font><br><Br>";
		echo "<font style='font-size:14px'>Enderço: $url</font><br>";
		echo "<font style='font-size:14px'>Login: 14213283000164</font><br>";
		echo "<font style='font-size:14px'>Senha: gmp2019@</font><br><br>";

		$mEstoque = (array) json_decode($data);

		$estoque = (array) json_decode( json_encode($mEstoque['saldoProdutos']) );

		$date = date('Y-m-d H:i:s');

		echo "<b>Inicio - $date </b><br><br>";

		if (!$data) {
			echo "<font color='red'><b>PROCESSO NÃO FUNCIONOU, AVISE O SUPORTE TECNICO.</font></b><br/><br/>";
			print_r($info).'<br><br>';
			echo $data;
		} else {
			if (empty($estoque)){
				echo "A conexão não retornou dados. Aguarde próxima tentativa.<br><br>";	
				echo "Retorno do fornecedor: <br><br>";
				echo $data.'<br><br>';
				echo "<a href='https://".$_SERVER['HTTP_HOST']."/cad/cad002/outro/importarEstoque/?ajax=true'><input type=button value='Tentar novamente'></a><br><br>";
			}	

			echo "<a href='https://".$_SERVER['HTTP_HOST']."/cad/cad002/outro/importarEstoque/?ajax=true&completo=true'><input type=button value='Forçar importação completa'></a><br><br>";

			// Refaz a conexão devido ao timeout e a demora na resposta 
			mysqli_close($g['conexaoBanco']);
			$conexaoTemp = @mysqli_connect('hardness-prd.cqfcavbrwplk.us-east-1.rds.amazonaws.com', 'root', 'EUXZ1WVY6g3mqE17OQ6Csd6dUAp6BTVMO','e229');
			$g['conexaoBanco'] = $conexaoTemp;
			// mysql_select_db('e229', $conexaoTemp);
			//print_r($estoque);
			//die();
			$count = 0;
			$countupdate=0;
			$countContinue=0;
			$countProdutoNaoExistente=0;
			$ProdutosInexistentes='';
			foreach ($estoque as $key1 => $value1) {
				$value1 = objectToArray($value1);
				//echo "<pre>";
				//print_r($value1);
				//die("chegou");
				foreach($value1 as $key2 => $value2){
					$D069=mysqli_query("SELECT D069_Id FROM D069 LEFT JOIN D070 ON D070_Id=D069_D070_Id WHERE D070_D024_Id=29 AND D069_Codigo_Produto='{$value2['key']}' AND D069_Quantidade_Estoque='{$value2['value']}' AND D070_Flag_Ativo='S'");
					if(mysql_num_rows($D069) == 3){
						//echo "Entrou continue<br />";
						$countContinue++;
						//continue;
					} else {
						$D069=mysqli_query("SELECT D069_Id FROM D069 LEFT JOIN D070 ON D070_Id=D069_D070_Id WHERE D070_D024_Id=29 AND D069_Codigo_Produto='{$value2['key']}' AND D070_Flag_Ativo='S'");
						echo mysql_error();
						if(mysql_num_rows($D069) > 0){
							mysqli_query("update D069 left join D070 on D070_Id=D069_D070_Id set D069_Quantidade_Estoque='{$value2['value']}' where D069_Codigo_Produto='{$value2['key']}' and D070_D024_Id=29 AND D070_Flag_Ativo='S'");
							$countupdate++;
							echo mysql_error();
						} else {
							$ProdutosInexistentes .= $value2['key']." - ";
							$countProdutoNaoExistente++;
						}
						//echo("update D069 left join D070 on D070_Id=D069_D070_Id set D069_Quantidade_Estoque='{$value2['value']}' where D069_Codigo_Produto='{$value2['key']}' and D070_D024_Id=29<br />");
						
					}
					
					
					
					$count++;
					//foreach($value2 as $value){
						//echo "Importando: Código: {$value2['key']} Estoque: {$value2['value']} <br>";
						
						//echo "update D069 left join D070 on D070_Id=D069_D070_Id set D069_Quantidade_Estoque='{$value2['value']}' where D069_Codigo_Produto='{$value2['key']}' and D070_D024_Id=29<br />";
					
						
					//}
				}
			}
		}
		echo "Produtos não atualizados (quantidade de estoque da OVD igual a quantidade de estoque no sistema): ".$countContinue."<br />";		
		echo "Produtos atualizados: ".$countupdate."<br />";
		echo "Produtos que não existem na tabela da OVD: ".$countProdutoNaoExistente."<br />";

		echo "<br><br><br>";

		$date = date('Y-m-d H:i:s');

		echo "<b>Fim - $date | Total itens: $count</b>";

		echo "<h2>Lista de produtos inexistentes: </h2>";
		echo $ProdutosInexistentes;

	break;

	case 'atualizarTabelaGeral':
		require_once('bibliotecas/classes/CAD002.php');

		$camposObrigatorios = $CAD002->retornaArrayTabelasGeral();
		$qD070 = "SELECT D070_Id,
						 D024_Nome_Empresa,
						 C004_Nome_Abreviado
					FROM D070
			   LEFT JOIN D024 ON D070_D024_Id = D024_Id
			   LEFT JOIN C004 ON D070_C004_Id = C004_Id
				ORDER BY D070_Id";
		$D070 = mysql_query($qD070);
		if($erro = mysql_error()){
			$erro = "Erro na query de seleção de fornecedores em cad/cad002/outro/atualizarTabelaGeral. Erro: [$erro] | Query: [$qD070]";
			log($erro);
			error_log($erro);
			echo "Erro ao tentar encontrar as tabelas de fornecedor para importação. Por favor contatar o suporte.";
			break;
		} else if(mysql_num_rows($D070) <= 0) {
			echo "Não existe nenhuma tabela de fornecedor cadastrada no sistema.";
			break;
		}

		$camposCsv = implode(";", array_column($camposObrigatorios, 'nomeCampo'));
		// Funções javascript que enviam o formulário dessa importação CSV ------------------------------
		echo <<<EOT
			<script>
				$(document).ready(function() {
					$('#tabelaGeral').submit(function(e) {
						e.preventDefault();
						var formData = new FormData(this);
						formData.append('ajax', true);

						var str_fornecedores = '';
						var select = document.getElementById('tabelas_fornecedor');
						var options = select.options;
						
						for (var i = 0; i < options.length; i++) {
							if(options[i].selected == true){
								str_fornecedores += options[i].innerHTML + "<br>";
							}
						}

						var buttons = {
							"Sim": function() {
								$('#envioCsvTabelaGeral').val('');
								var idProgressBar = dialogAlert('Processando',"<span id='progressBar'>Processando...</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
								$.ajax({
									url: '/cad/cad002/form_func-ajax/tabelaGeral/', // Substitua pela URL da sua função de importação
									type: 'post',
									data: formData,
									dataType: 'jsonp',
									contentType: false,
									processData: false
								}).done(function(data){
									var txtArea = "<textarea disabled rows='10' cols='60'>"+data.data+"</textarea>";
									dialogConfirm('Importar CSV', txtArea);
									$('#dc-' + idProgressBar).dialog("destroy").remove();
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						dialogConfirm('Confirmar Importar CSV', 'Confirma a importação dos dados para as seguintes tabelas de fornecedores?<br><br><b>' + str_fornecedores + '</b>', buttons);
					});
				});
				function download_csv_file() {  
					var csv = '{$camposCsv}';
					var hiddenElement = document.createElement('a');  
					hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURI(csv);  
					hiddenElement.target = '_blank';  
					hiddenElement.download = 'modelo_csv_atualizar_tabela_fornecedor_geral.csv';  
					hiddenElement.click();  
				}
				function selectOptions(arraySelecao) {
					var select = document.getElementById('tabelas_fornecedor');
					var options = select.options;
					
					for (var i = 0; i < options.length; i++) {
						options[i].selected = false;
					}

					for (var i = 0; i < options.length; i++) {
						if(arraySelecao.includes(parseInt(options[i].value))){
							options[i].selected = true;
						}
					}
				}
			</script>
EOT;

		// Formulário ------------------------------------------------------------------------
		echo "<div style='display: inline-block;'>";
			echo "<div style='display: inline-block; width: 710px; height: 100px;vertical-align: top;'>";
				echo"<form id='tabelaGeral'>";
				echo"<br><input type='file' id='envioCsvTabelaGeral' name='envioCsvTabelaGeral' accept='.csv' /><br><br>";
				echo"<select style='height:190px;width:700px' id='tabelas_fornecedor' name='tabelas_fornecedor[]' multiple>";
				while($mD070 = mysql_fetch_assoc($D070)){
					$D070_Id = $mD070['D070_Id'];
					$D024_Nome_Empresa = $mD070['D024_Nome_Empresa'];
					$C004_Nome_Abreviado = $mD070['C004_Nome_Abreviado'];

					echo "<option value='$D070_Id'>$D070_Id - $D024_Nome_Empresa - $C004_Nome_Abreviado</option>";
				}
				echo "</select><br><br>";
				echo"<input style='border-radius: 10%; ' type='submit' value='Enviar' />";
				echo"</form>";
			echo "</div>";
			
			echo "<div style='display: inline-block; width: 500px; height: 50px;vertical-align: top;'>";
				echo "<br><br><br><br>";
				$botoesPreSelect = $CAD002->Cad002AtualizarTabelaGeralFornecedorBotoesPreSelect();
				foreach($botoesPreSelect as $key => $value){
					echo "<button onclick='selectOptions([{$value['tabelas']}])'>{$value['nome']}</button>";
				}
			echo "</div>";
		echo "</div>";

		echo "<br><br><br><br><br><br><br><br><br><br><br><br><br><br>";

		// Parte que indica como criar o CSV ---------------------------------------------------------------
		echo "<h1>  Abaixo está o formato modelo do documento para a importação! </h1> <br>";
		echo"<p style='font-size: 15px;'> - O documento deve ter ". count($camposObrigatorios) ." colunas ao todo, caso seja necessário modificar o número de colunas para a importação, contactar o suporte técnico. <br>";
		echo"- Os nomes das colunas devem estar necessáriamente na primeira linha(1) do documento e ter a mesma gráfia do documento modelo. <br>";
		echo"- A extensão do documento deve ser CSV (separado por vírgulas)(*.csv) <br>";
		echo"- Ao final do documento não deve haver nenhuma linha em branco. <br>";
		echo "- **ATENÇÃO** Se um campo ficar em branco, ele vai ser zerado pelo sistema.<br>";
		echo "- Você pode usar a tag [NAO-ALTERAR] dentro da linha do CSV para não alterar o dado.<br>";
		echo"- Os campos com * são de preenchimento obrigatório.<br>";
		foreach ($camposObrigatorios as $key => $campo) {
			if($camposObrigatorios[$key]['aviso'])
			{
				echo "- No campo {$camposObrigatorios[$key]['nomeCampo']}: {$camposObrigatorios[$key]['aviso']} <br>";
			}
		}
		echo "<br><a onclick='download_csv_file()'><u>Baixar Modelo CSV</u></a>";
		echo"<br>";
		echo"<br>";
		echo"<table cellspacing=1px bgcolor='black'>";
		echo"<tr>"; 
		foreach ($camposObrigatorios as $key => $campo) {
			if($camposObrigatorios[$key]['obrigatorio'])
			{
				echo"	<td style='font-size:15px; padding:10px 15px; text-align:center; background-color:#FFF;'>*{$campo['nomeCampo']}</td>";
			}else{
				echo"	<td style='font-size:15px; padding:10px 15px; text-align:center; background-color:#FFF;'>{$campo['nomeCampo']}</td>";
			}
			
		}
		echo"</tr>";
		echo"<tr>"; 
		foreach ($camposObrigatorios as $campo) {
			echo"	<td style ='text-align:center; font-size:15px;' >---</td>";
		}
		echo"</tr>";
		echo"</table>";
	break;	

}

function objectToArray($d) {
	if (is_object($d)) {
		// Gets the properties of the given object
		// with get_object_vars function
		$d = get_object_vars($d);
	}

	if (is_array($d)) {
		/*
		   * Return array converted to object
			* Using __FUNCTION__ (Magic constant)
			* for recursive call
			*/
		return array_map(__FUNCTION__, $d);
		}
	else {
		// Return array
		return $d;
	}
}

