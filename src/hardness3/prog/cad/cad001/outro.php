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
	case 'cad001CabecalhoCliente':
		// Select
		$sql = <<<EOT
		SELECT
			*
		FROM
			D024
		LEFT JOIN D020 ON D020_Id=D024_D020_Id
		LEFT JOIN D018 ON D018_Id=D024_D018_Id
		WHERE
			(D024_Id='{$r_acaoId}')
EOT;
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);

		// Registro vazios = FALTA
		foreach ($row as &$row_) {
			if (empty($row_)) { 
				$row_ = '<span style="color: var(--BGVermelho);">FALTA</span>';
			}
		}

		$D024_Nome_Empresa = gTruncar($row['D024_Nome_Empresa'], 50);
		$D020_Nome_Cidade = gTruncar($row['D020_Nome_Cidade'], 19);

		$linkCRM = "onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'CRM - Cliente', '/crm/crm001/content/crm001ContentPrincipalCRM/', '&D024_Id=' + encodeURIComponent('{$r_acaoId}') + '&tabela=D024', 'auto');\"";
		$linkSRM = "onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'SRM - Fornecedor', '/srm/srm001/content/srm001ContentPrincipalSRM/', '&D024_Id=' + encodeURIComponent('{$r_acaoId}') + '&tabela=D024', 'auto');\"";
		if($row['D024_Flag_Cliente'] == 'S' && $row['D024_Flag_Fornecedor'] == 'S'){
			$atalho = "<td rowspan='2' style='width: 50px'><button {$linkCRM}>CRM</button></td><td rowspan='2' style='width: 50px'><button {$linkSRM}>SRM</button></td>";
		} else if($row['D024_Flag_Cliente'] == 'S') {
            if(gTemPermissao(104)){
			    $atalho = "<td rowspan='2' style='width: 50px;'></td><td rowspan='2' style='width: 50px;'><button {$linkCRM}>CRM</button></td>";
            }
		} else if($row['D024_Flag_Fornecedor'] == 'S'){
            if(gTemPermissao(181)){
			    $atalho = "<td rowspan='2' style='width: 50px;'></td><td rowspan='2' style='width: 50px;'><button {$linkSRM}>SRM</button></td>";
            }
		}

		$habilitarLinkCadastro = ($r_habilitarLinkCadastro == 'S') ? "style=\"cursor: pointer;\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cliente', '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent('{$row['D024_Id']}'), [960,583]);\"" : '';
		// Print
		echo <<<EOT
			<div class="contentCabecalhoLayout" {$habilitarLinkCadastro}>
				<table class="tabela ui-state-default ui-corner-all">
					<tr class="linhaDesc">
						<td width='70px'>Código</td>
						<td width='400px'>Cliente</td>
						<td width='150px'>Cidade</td>
						<td width='150px'>Estado</td>
						<td></td>
						{$atalho}
					</tr>
					<tr class="linhaVal">
						<td align='center'>{$row['D024_Id']}</td>
						<td>{$D024_Nome_Empresa}</td>
						<td>{$D020_Nome_Cidade}</td>
						<td>{$row['D018_UF']}</td>
						<td></td>
					</tr>
				</table>
			</div>
EOT;

	break;

	/**
	 * Cabeçalho Agenda
	 */
	case 'cad001CabecalhoAgenda':
		// Select
		if(isset($r_D024_Id) && $r_D024_Id > 0 && $r_acaoId <= 0){
			$resultado = mysql_query("SELECT * FROM D024 LEFT JOIN D020 ON D020_Id=D024_D020_Id LEFT JOIN D018 ON D018_Id=D024_D018_Id WHERE D024_Id = '{$r_D024_Id}'");
		} else {
			$resultado = mysql_query("SELECT * FROM D085 LEFT JOIN D024 ON D024_Id=D085_D024_Id LEFT JOIN D020 ON D020_Id=D024_D020_Id LEFT JOIN D018 ON D018_Id=D024_D018_Id LEFT JOIN T003 ON T003_Id=D085_T003_Id WHERE D085_Id = '{$r_acaoId}'");
		}
		$row = mysql_fetch_assoc($resultado);

		if(isset($r_T003_Id) && $r_T003_Id > 0){
			$resultado = mysql_query("SELECT * FROM T003 WHERE T003_Id = '{$r_T003_Id}'");
			$row2 = mysql_fetch_assoc($resultado);
			$row = array_merge($row, $row2);
		}

		if($row['T003_Id'] > 0 && ($g['nivelAlcada'] == '4' || $row['T003_C007_Id_Vendedor_Interno'] == $g['usuarioAtual'])){
			$row['T003_Id'] = "<span style=\"cursor: pointer;\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Orçamento', '/ven/ven001/content/ven001ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent('{$row['T003_Id']}') + '&tabela=T003', [990,605]);\") + '&tabela=D024', 'auto');\"><u>{$row['T003_Id']}</u></span>";
		}

		// Registro vazios = FALTA
		foreach ($row as &$row_) {
			if (empty($row_)) {
				$row_ = '<span>-</span>';
			}
		}

		$D024_Nome_Empresa = gTruncar($row['D024_Nome_Empresa'], 30);

		$row['T003_Data_Emissao'] = gCorrigeData($row['T003_Data_Emissao']);
		$row['T003_Valor_Total'] = gCorrigeNumero($row['T003_Valor_Total']);

        $urlCadastroCliente = <<<EOT
            onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'CRM - Cliente', '/crm/crm001/content/crm001ContentPrincipalCRM/', '&D024_Id=' + encodeURIComponent('{$row['D024_Id']}') + '&tabela=D024', 'auto');"
EOT;

		// Print
		echo <<<EOT
			<div class="contentCabecalhoLayout">
				<table class="tabela ui-state-default ui-corner-all" style="cursor:pointer;">
					<tr class="linhaDesc" {$urlCadastroCliente}>
						<td width='5%' align='center'>Código</td>
						<td width='40%'>Cliente</td>
						<td width='15%'>Telefone</td>
						<td width='25%'>Cidade</td>
						<td width='5%'>UF</td>
					</tr>
					<tr class="linhaVal" {$urlCadastroCliente}>
						<td style='text-align: center; padding-right: 2px;'>{$row['D024_Id']}</td>
						<td>{$D024_Nome_Empresa}</td>
						<td>{$row['D024_DDD_Telefone_1']} - {$row['D024_Telefone_1']}</td>
						<td>{$row['D020_Nome_Cidade']}</td>
						<td>{$row['D020_UF']}</td>
					</tr>
				</table>
			</div>
EOT;

	break;

	case 'curvaABCCliente':

        //pega os valores dos parâmetros da curva
        if(!empty($g['C031']['curvaABCclientes'])){

            $parametrosCurvaABC = explode(",", str_replace(" ", "", trim($g['C031']['curvaABCclientes'],",")));
            $pCurvaA = ($parametrosCurvaABC[0]/100);
            $pCurvaB = ($parametrosCurvaABC[1]/100);
            $pCurvaC = ($parametrosCurvaABC[2]/100);
            $somaParametros = ($pCurvaA+$pCurvaB+$pCurvaC) *100;

        }

        $sqlD024 = mysql_query("SELECT D024_Id, 
                                       D024_Nome_Empresa, 
                                       D024_Nome_Fantasia 
                                  FROM D024 
                                 WHERE D024_Id = '{$r_acaoId}'");

        $rowD024 = mysql_fetch_array($sqlD024);

        $html = <<<EOT
            <style>
            	.columnB {
            		border-bottom: 1px solid gray;
            		padding: 3px !important;
            	}
            	.columnBL {
            		border-bottom: 1px solid gray;
            		border-left: 1px solid gray;
            		padding: 3px !important;
            	}
            	.columnBR {
            		border-bottom: 1px solid gray;
            		border-right: 1px solid gray;
            		padding: 3px !important;
            	}
            	.columnBLR {
            		border-left: 1px solid gray;
            		border-bottom: 1px solid gray;
            		border-right: 1px solid gray;
            		padding: 3px !important;
            	}
            	.columnT {
            		border-top: 1px solid gray;
            		padding: 3px !important;
            	}
            	.columnTL {
            		border-top: 1px solid gray;
            		border-left: 1px solid gray;
            		padding: 3px !important;
            	}
            	.columnTR {
            		border-top: 1px solid gray;
            		border-right: 1px solid gray;
            		padding: 3px !important;
            	}
            	.columnTLR {
            		border-left: 1px solid gray;
            		border-top: 1px solid gray;
            		border-right: 1px solid gray;
            		padding: 3px !important;
            	}
            </style>
	        <div>
            <div style="width: 770px; height: 190px;">
            <div style="font-weight: bold;">
                {$rowD024['D024_Id']} - {$rowD024['D024_Nome_Empresa']}
            </div>
                <table cellspacing="0" cellpadding="2" style="border: 2px solid grey; margin: auto; width: 380px; float: left; margin-bottom: 10px;">
            <tr>
                <td class='columnBR' rowspan="2" style='font-weight: bold; text-align: center;'>Mês</td>
                <td class='columnBLR' colspan="4" style='font-weight: bold; text-align: center;'>Local</td>
                <td class='columnBL' colspan="4" style='font-weight: bold; text-align: center;'>Grupo</td>
            </tr>
            <tr>
                <td class='columnBLR' style='font-weight: bold; text-align: center; padding: 1px 5px;'>Itens</td>
                <td class='columnBR' style='font-weight: bold; text-align: center; padding: 1px 5px;'>Qtde.</td>
                <td class='columnBR' style='font-weight: bold; text-align: center; padding: 1px 5px;'>R$</td>
                <td class='columnBR' style='font-weight: bold; text-align: center; padding: 1px 5px;'>Freq.</td>
                <td class='columnBL' style='font-weight: bold; text-align: center; padding: 1px 5px;'>Itens</td>
                <td class='columnBL' style='font-weight: bold; text-align: center; padding: 1px 5px;'>Qtde.</td>
                <td class='columnBL' style='font-weight: bold; text-align: center; padding: 1px 5px;'>R$</td>
                <td class='columnBL' style='font-weight: bold; text-align: center; padding: 1px 5px;'>Freq.</td>
            </tr>
EOT;
        //preenche o relatório de venda da empresa e do grupo    
        $total           = 0;
        $valorTotal      = 0;
        $frequencia      = 0;
        $totalItens      = 0;
        $totalGrupo      = 0;
        $valorTotalGrupo = 0;
        $frequenciaGrupo = 0;
        $totalItensGrupo = 0;


        $mes     = strtotime('-12 month');
        $xLast12 = date('Y-m-d', $mes);

        for ($i=0;$i<13;$i++){

            $dataInicial = date('Y-m-1', $mes);
            $dataFinal   = date('Y-m-t', $mes);
            $tituloMes   = date('m/Y', $mes);


            $sqlEmpresa = mysql_query("SELECT COUNT(DISTINCT D049_D001_Id) AS Itens, 
                                              SUM(T008_Quantidade) AS Quantidade, 
                                              SUM(T008_Valor_Preco_Unitario*T008_Quantidade) AS ValorTotal, 
                                              COUNT(DISTINCT T007_Id) AS Frequencia 
                                         FROM T007 
                                    LEFT JOIN T008 ON T008_T007_Id=T007_Id 
                                    LEFT JOIN D009 ON D009_Id=T008_D009_Id 
                                    LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                    LEFT JOIN D006 ON D006_Id=T007_D006_Id 
                                    LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno 
                                        WHERE T007_Data_Emissao >= '{$dataInicial}' 
                                          AND T007_Data_Emissao <= '{$dataFinal}' 
                                          AND T007_D024_Id = '{$r_acaoId}'
                                          AND T007_C004_Id = '{$g['empresaAtual']}'
                                          AND D006_Flag_Venda_Compra_Outros='V' 
                                          AND T007_Flag_Cancelada!='S'");

            $resEmpresa = mysql_fetch_array($sqlEmpresa);


            $total      += $resEmpresa['Quantidade'];
            $valorTotal += $resEmpresa['ValorTotal'];
            $frequencia += $resEmpresa['Frequencia'];
            $totalItens += $resEmpresa['Itens'];


            $resEmpresa['Quantidade'] = gCorrigeNumero($resEmpresa['Quantidade']);
            $resEmpresa['ValorTotal'] = gCorrigeNumero($resEmpresa['ValorTotal']);

            $sqlGrupo = mysql_query("SELECT COUNT(DISTINCT D049_D001_Id) AS Itens, 
                                            SUM(T008_Quantidade) AS Quantidade, 
                                            SUM(T008_Valor_Preco_Unitario*T008_Quantidade) AS ValorTotal, 
                                            COUNT(DISTINCT T007_Id) AS Frequencia 
                                       FROM T007 
                                  LEFT JOIN T008 ON T008_T007_Id=T007_Id 
                                  LEFT JOIN D009 ON D009_Id=T008_D009_Id 
                                  LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                  LEFT JOIN D006 ON D006_Id=T007_D006_Id 
                                  LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno 
                                      WHERE T007_Data_Emissao >= '{$dataInicial}'  
                                        AND T007_Data_Emissao <= '{$dataFinal}' 
                                        AND T007_D024_Id = '{$r_acaoId}'
                                        AND D006_Flag_Venda_Compra_Outros='V' 
                                        AND T007_Flag_Cancelada!='S'");

            $resGrupo    = mysql_fetch_array($sqlGrupo);

            $totalGrupo      += $resGrupo['Quantidade'];
            $valorTotalGrupo += $resGrupo['ValorTotal'];
            $frequenciaGrupo += $resGrupo['Frequencia'];
            $totalItensGrupo += $resGrupo['Itens'];

            $resGrupo['Quantidade'] = gCorrigeNumero($resGrupo['Quantidade']);
            $resGrupo['ValorTotal'] = gCorrigeNumero($resGrupo['ValorTotal']);

            $html .= <<<EOT
                <tr>
                    <td class='columnTR' style='text-align: center;'>{$tituloMes}</td>
                    <td class='columnTLR' style='text-align: right;'>{$resEmpresa['Itens']}</td>
                    <td class='columnTR' style='text-align: right;'>{$resEmpresa['Quantidade']}</td>
                    <td class='columnTR' style='text-align: right;'>{$resEmpresa['ValorTotal']}</td>
                    <td class='columnTR' style='text-align: right;'>{$resEmpresa['Frequencia']}</td>
                    <td class='columnTL' style='text-align: right;'>{$resGrupo['Itens']}</td>
                    <td class='columnTL' style='text-align: right;'>{$resGrupo['Quantidade']}</td>
                    <td class='columnTL' style='text-align: right;'>{$resGrupo['ValorTotal']}</td>
                    <td class='columnTL' style='text-align: right;'>{$resGrupo['Frequencia']}</td>
                </tr>
EOT;
            $mes = strtotime('+1 month', $mes);
        	$dataInicial_ = ($i == 0) ? $dataInicial : $dataInicial_;
        	$dataFinal_   = ($i == 12) ? $dataFinal : $dataFinal_;
        }

        // Somando os valores acima quando entra no loop
/* 		//SQL para obter o total de itens vendidos durante os 12 meses
		$sqlEmpresa = mysql_query("SELECT COUNT(DISTINCT D049_D001_Id) AS Itens 
                                     FROM T007 
                                LEFT JOIN T008 ON T008_T007_Id=T007_Id 
                                LEFT JOIN D009 ON D009_Id=T008_D009_Id 
                                LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                LEFT JOIN D006 ON D006_Id=T007_D006_Id 
                                LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno 
                                   WHERE T007_Data_Emissao >= '{$dataInicial_}' 
                                     AND T007_Data_Emissao <= '{$dataFinal_}'  
                                     AND T007_D024_Id = '{$r_acaoId}' 
                                     AND T007_C004_Id = '{$g['empresaAtual']}'
                                     AND T007_Flag_Cancelada != 'S' 
                                     AND D006_Flag_Venda_Compra_Outros = 'V'");

        $resEmpresa = mysql_fetch_array($sqlEmpresa);

		$sqlGrupo = mysql_query("SELECT COUNT(DISTINCT D049_D001_Id) AS Itens 
                                   FROM T007 
                              LEFT JOIN T008 ON T008_T007_Id=T007_Id 
                              LEFT JOIN D009 ON D009_Id=T008_D009_Id 
                              LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                              LEFT JOIN D006 ON D006_Id=T007_D006_Id 
                              LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno 
                                  WHERE T007_Data_Emissao >= '{$dataInicial_}' 
                                    AND T007_Data_Emissao <= '{$dataFinal_}'
                                    AND T007_D024_Id = '{$r_acaoId}' 
                                    AND T007_Flag_Cancelada!='S' 
                                    AND D006_Flag_Venda_Compra_Outros='V'");

        $resGrupo = mysql_fetch_array($sqlGrupo); */

        $valorTotal      = gCorrigeNumero($valorTotal);
        $valorTotalGrupo = gCorrigeNumero($valorTotalGrupo);

        $html .= <<<EOT
            <tr>
                <td class='columnTR' style='font-weight: bold;'>Total</td>
                <td class='columnTLR' style='font-weight: bold; text-align: right;'>{$totalItens}</td>
                <td class='columnTR' style='font-weight: bold; text-align: right;'>{$total}</td>
                <td class='columnTR' style='font-weight: bold; text-align: right;'>{$valorTotal}</td>
                <td class='columnTR' style='font-weight: bold; text-align: right;'>{$frequencia}</td>
                <td class='columnTL' style='font-weight: bold; text-align: right;'>{$totalItensGrupo}</td>
                <td class='columnTL' style='font-weight: bold; text-align: right;'>{$totalGrupo}</td>
                <td class='columnTL' style='font-weight: bold; text-align: right;'>{$valorTotalGrupo}</td>
                <td class='columnTL' style='font-weight: bold; text-align: right;'>{$frequenciaGrupo}</td>
            </tr>
EOT;

        //calcula o total vendido de todos os produtos da curva ABC nos últimos 12 meses a partir da data de hoje
        $sqlTotalVendido = mysql_query("SELECT SUM(T007_Valor_Total_Produtos) AS TotalVendido 
                                          FROM T007 
                                     LEFT JOIN D006 ON D006_Id=T007_D006_Id 
                                     LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno 
                                     LEFT JOIN D024 ON D024_Id=T007_D024_Id 
                                         WHERE T007_Data_Emissao >= '{$xLast12}'  
                                           AND T007_Flag_Cancelada!='S' 
                                           AND D006_Flag_Venda_Compra_Outros='V' 
                                           AND D024_Flag_Empresas_Grupo!='S'");

        $rowTotalVendido = mysql_fetch_array($sqlTotalVendido);

        $CurvaA = abreviarNumero($rowTotalVendido['TotalVendido'] * $pCurvaA);
        $CurvaB = abreviarNumero($rowTotalVendido['TotalVendido'] * $pCurvaB);
        $CurvaC = abreviarNumero($rowTotalVendido['TotalVendido'] * $pCurvaC);


        //calcula os valores de venda para cada curva
        $sqlCurvaA = mysql_query("SELECT MAX(valorTotal) as valorMaxCurvaA, 
                                         MIN(valorTotal) as valorMinCurvaA, 
                                         COUNT(DISTINCT T007_D024_Id) AS totalItens 
                                    FROM (SELECT SUM(T007_Valor_Total_Produtos) AS valorTotal, 
                                                 T007_D024_Id 
                                            FROM T007 
                                       LEFT JOIN D006 ON D006_Id=T007_D006_Id 
                                       LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno 
                                       LEFT JOIN D024 ON D024_Id=T007_D024_Id 
                                           WHERE T007_Data_Emissao >= '{$xLast12}'  
                                             AND D024_Flag_Curva_ABC = 'A' 
                                             AND T007_Flag_Cancelada!='S' 
                                             AND D006_Flag_Venda_Compra_Outros='V'                                     
                                             AND D024_Flag_Empresas_Grupo!='S' 
                                        GROUP BY T007_D024_Id) AS T7");
        $rowCurvaA = mysql_fetch_array($sqlCurvaA);

        $sqlCurvaB = mysql_query("SELECT MAX(valorTotal) as valorMaxCurvaB,
                                         MIN(valorTotal) as valorMinCurvaB, 
                                         COUNT(DISTINCT T007_D024_Id) AS totalItens 
                                   FROM (SELECT SUM(T007_Valor_Total_Produtos) AS valorTotal, 
                                                T007_D024_Id 
                                           FROM T007 
                                      LEFT JOIN D006 ON D006_Id=T007_D006_Id 
                                      LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno 
                                      LEFT JOIN D024 ON D024_Id=T007_D024_Id 
                                          WHERE T007_Flag_Cancelada!='S' 
                                            AND D006_Flag_Venda_Compra_Outros='V' 
                                            AND T007_Data_Emissao >= '{$xLast12}' 
                                            AND D024_Flag_Curva_ABC = 'B' 
                                            AND D024_Flag_Empresas_Grupo!='S' 
                                       GROUP BY T007_D024_Id) AS T7");
        $rowCurvaB = mysql_fetch_array($sqlCurvaB);

        $sqlCurvaC = mysql_query("SELECT MAX(valorTotal) as valorMaxCurvaC, 
                                         MIN(valorTotal) as valorMinCurvaC, 
                                         COUNT(DISTINCT T007_D024_Id) AS totalItens 
                                    FROM (SELECT SUM(T007_Valor_Total_Produtos) AS valorTotal, 
                                                 T007_D024_Id 
                                            FROM T007 
                                       LEFT JOIN D006 ON D006_Id=T007_D006_Id 
                                       LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno 
                                       LEFT JOIN D024 ON D024_Id=T007_D024_Id 
                                           WHERE T007_Flag_Cancelada!='S' 
                                            AND D006_Flag_Venda_Compra_Outros='V' 
                                            AND T007_Data_Emissao >= '{$xLast12}' 
                                            AND D024_Flag_Curva_ABC = 'C' 
                                            AND D024_Flag_Empresas_Grupo!='S' 
                                       GROUP BY T007_D024_Id) AS T7");
        $rowCurvaC = mysql_fetch_array($sqlCurvaC);

        //preenche o relatório da Curva ABC Grupo - valor vendido    
        $valorMaxCurvaA = abreviarNumero($rowCurvaA['valorMaxCurvaA']);
        $valorMinCurvaA = abreviarNumero($rowCurvaA['valorMinCurvaA']);
        $valorMaxCurvaB = abreviarNumero($rowCurvaB['valorMaxCurvaB']);
        $valorMinCurvaB = abreviarNumero($rowCurvaB['valorMinCurvaB']);
        $valorMaxCurvaC = abreviarNumero($rowCurvaC['valorMaxCurvaC']);
        $valorMinCurvaC = abreviarNumero($rowCurvaC['valorMinCurvaC']);

        $rowTotalVendido['TotalVendido'] = gCorrigeNumero($rowTotalVendido['TotalVendido']);

        $totalItens = $rowCurvaA['totalItens'] + $rowCurvaB['totalItens'] + $rowCurvaC['totalItens'];
        $html .= <<<EOT
            </table>
            </td>
            <td valign=top>
            <table cellspacing="0" style="float: left; margin-left: 10px; border: 2px solid grey; min-width: 300px;">
            <tr>
                <td class='columnB' colspan="5" style='text-align: center; font-weight: bold;'>Curva ABC Grupo - Valor</td>
            </tr>
            <tr>
                <td class='columnTR' style='text-align: center;'>A ({$parametrosCurvaABC[0]}%)</td>
                <td class='columnTLR' colspan="2" style='text-align: center;'>{$CurvaA} ({$rowCurvaA['totalItens']} clientes)</td>
                <td class='columnT' colspan="2" style='text-align: center;'>{$valorMaxCurvaA} à {$valorMinCurvaA}</td>
            </tr>
            <tr>
                <td class='columnTR' style='text-align: center;'>B ({$parametrosCurvaABC[1]}%)</td>
                <td class='columnTLR' colspan="2" style='text-align: center;'>{$CurvaB} ({$rowCurvaB['totalItens']} clientes)</td>
                <td class='columnT' colspan="2" style='text-align: center;'>{$valorMaxCurvaB} à {$valorMinCurvaB}</td>
            </tr>
            <tr>
                <td class='columnTR' style='text-align: center;'>C ({$parametrosCurvaABC[2]}%)</td>
                <td class='columnTLR' colspan="2" style='text-align: center;'>{$CurvaC} ({$rowCurvaC['totalItens']} clientes)</td>
                <td class='columnT' colspan="2" style='text-align: center;'>{$valorMaxCurvaC} à {$valorMinCurvaC}</td>
            </tr>
            <tr>
                <td class='columnTR' style='font-weight: bold; text-align: center;'>100%</td>
                <td class='columnTL' colspan="4" style='font-weight: bold; text-align: center;'>R$ {$rowTotalVendido['TotalVendido']} ({$totalItens} clientes)</td>
            </tr>
            </table>
            </td></tr></table>
            <span style="font-size: 10px; margin-left: 44px;">* Dados processados com base nos últimos 12 meses</span>
        </div>
EOT;
        echo $html;

        //----------------------------------------------------------------------------------------------------------------------------
        //calcula a quantidade total vendida de todos os produtos da curva ABC nos últimos 12 meses a partir da data de hoje
        $sqlTotalVendidoQtd = mysql_query("SELECT SUM(T008_Quantidade) AS QtdTotalVendido 
                                             FROM T007 
                                        LEFT JOIN T008 ON T008_T007_Id=T007_Id 
                                        LEFT JOIN D006 ON D006_Id=T007_D006_Id 
                                        LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno 
                                        LEFT JOIN D024 ON D024_Id=T007_D024_Id 
                                            WHERE T007_Data_Emissao >= '{$xLast12}'  
                                              AND T007_Flag_Cancelada!='S' 
                                              AND D006_Flag_Venda_Compra_Outros='V'                                               
                                              AND D024_Flag_Empresas_Grupo!='S'");

        $rowTotalVendidoQtd = mysql_fetch_array($sqlTotalVendidoQtd);

        $CurvaAQtd = abreviarNumero($rowTotalVendidoQtd['QtdTotalVendido']*$pCurvaA);
        $CurvaBQtd = abreviarNumero($rowTotalVendidoQtd['QtdTotalVendido']*$pCurvaB);
        $CurvaCQtd = abreviarNumero($rowTotalVendidoQtd['QtdTotalVendido']*$pCurvaC);

        //calcula os valores de venda para cada curva
        $sqlCurvaAQtd = mysql_query("SELECT MAX(QtdTotal) as QtdMaxCurvaA, 
                                            MIN(QtdTotal) as QtdMinCurvaA, 
                                            COUNT(DISTINCT T007_D024_Id) AS totalItens 
                                       FROM (SELECT SUM(T008_Quantidade) AS QtdTotal, 
                                                    T007_D024_Id 
                                               FROM T007 
                                          LEFT JOIN T008 ON T008_T007_Id=T007_Id 
                                          LEFT JOIN D006 ON D006_Id=T007_D006_Id 
                                          LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno 
                                          LEFT JOIN D024 ON D024_Id=T007_D024_Id 
                                              WHERE T007_Data_Emissao >= '{$xLast12}' 
                                                AND D024_Flag_Curva_ABC_Qtd = 'A'  
                                                AND T007_Flag_Cancelada!='S' 
                                                AND D006_Flag_Venda_Compra_Outros='V'                                                    
                                                AND D024_Flag_Empresas_Grupo!='S' 
                                           GROUP BY T007_D024_Id) AS T7");

        $rowCurvaAQtd = mysql_fetch_array($sqlCurvaAQtd);

        $sqlCurvaBQtd = mysql_query("SELECT MAX(QtdTotal) as QtdMaxCurvaB, 
                                            MIN(QtdTotal) as QtdMinCurvaB, 
                                            COUNT(DISTINCT T007_D024_Id) AS totalItens 
                                       FROM (SELECT SUM(T008_Quantidade) AS QtdTotal, 
                                                    T007_D024_Id 
                                               FROM T007 
                                          LEFT JOIN T008 ON T008_T007_Id=T007_Id 
                                          LEFT JOIN D006 ON D006_Id=T007_D006_Id 
                                          LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno 
                                          LEFT JOIN D024 ON D024_Id=T007_D024_Id 
                                              WHERE T007_Data_Emissao >= '{$xLast12}'  
                                                AND D024_Flag_Curva_ABC_Qtd = 'B'
                                                AND T007_Flag_Cancelada!='S'
                                                AND D006_Flag_Venda_Compra_Outros='V'                                               
                                                AND D024_Flag_Empresas_Grupo!='S' 
                                           GROUP BY T007_D024_Id) AS T7");

        $rowCurvaBQtd = mysql_fetch_array($sqlCurvaBQtd);

        $sqlCurvaCQtd = mysql_query("SELECT MAX(QtdTotal) as QtdMaxCurvaC, 
                                            MIN(QtdTotal) as QtdMinCurvaC, 
                                            COUNT(DISTINCT T007_D024_Id) AS totalItens 
                                       FROM (SELECT SUM(T008_Quantidade) AS QtdTotal, 
                                                    T007_D024_Id 
                                               FROM T007 
                                          LEFT JOIN T008 ON T008_T007_Id=T007_Id 
                                          LEFT JOIN D006 ON D006_Id=T007_D006_Id 
                                          LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno 
                                          LEFT JOIN D024 ON D024_Id=T007_D024_Id 
                                              WHERE T007_Data_Emissao >= '{$xLast12}'
                                                AND D024_Flag_Curva_ABC_Qtd = 'C'  
                                                AND T007_Flag_Cancelada!='S' 
                                                AND D006_Flag_Venda_Compra_Outros='V'                                                  
                                                AND D024_Flag_Empresas_Grupo!='S' 
                                           GROUP BY T007_D024_Id) AS T7");

        $rowCurvaCQtd = mysql_fetch_array($sqlCurvaCQtd);

        //preenche o relatório da Curva ABC Grupo - valor vendido    
        $QtdMaxCurvaA = round(abreviarNumero($rowCurvaAQtd['QtdMaxCurvaA']));
        $QtdMinCurvaA = round(abreviarNumero($rowCurvaAQtd['QtdMinCurvaA']));
        $QtdMaxCurvaB = round(abreviarNumero($rowCurvaBQtd['QtdMaxCurvaB']));
        $QtdMinCurvaB = round(abreviarNumero($rowCurvaBQtd['QtdMinCurvaB']));
        $QtdMaxCurvaC = round(abreviarNumero($rowCurvaCQtd['QtdMaxCurvaC']));
        $QtdMinCurvaC = round(abreviarNumero($rowCurvaCQtd['QtdMinCurvaC']));

        $rowTotalVendidoQtd['QtdTotalVendido'] = gCorrigeNumero($rowTotalVendidoQtd['QtdTotalVendido']);
        $totalItens = $rowCurvaAQtd['totalItens'] + $rowCurvaBQtd['totalItens'] + $rowCurvaCQtd['totalItens'];
        $htmlQtd .= <<<EOT
            </table>
            <tr>
                <table cellspacing="0" style="float: left; margin-left: 10px; border: 2px solid grey; min-width: 300px;">
            <tr>
                <td class='columnB' colspan="5" style='text-align: center; font-weight: bold;'>Curva ABC Grupo - Quantidade</td>
            </tr>
            <tr>
                <td class='columnTR' style='text-align: center;'>A ({$parametrosCurvaABC[0]}%)</td>
                <td class='columnTLR' colspan="2" style='text-align: center;'>{$CurvaAQtd} ({$rowCurvaA['totalItens']} clientes)</td>
                <td class='columnT' colspan="2" style='text-align: center;'>{$QtdMaxCurvaA} à {$QtdMinCurvaA}</td>
            </tr>
            <tr>
                <td class='columnTR' style='text-align: center;'>B ({$parametrosCurvaABC[1]}%)</td>
                <td class='columnTLR' colspan="2" style='text-align: center;'>{$CurvaBQtd} ({$rowCurvaB['totalItens']} clientes)</td>
                <td class='columnT' colspan="2" style='text-align: center;'>{$QtdMaxCurvaB} à {$QtdMinCurvaB}</td>
            </tr>
            <tr>
                <td class='columnTR' style='text-align: center;'>C ({$parametrosCurvaABC[2]}%)</td>
                <td class='columnTLR' colspan="2" style='text-align: center;'>{$CurvaCQtd} ({$rowCurvaC['totalItens']} clientes)</td>
                <td class='columnT' colspan="2" style='text-align: center;'>{$QtdMaxCurvaC} à {$QtdMinCurvaC}</td>
            </tr>
            <tr>
                <td class='columnTR' style='font-weight: bold; text-align: center;'>100%</td>
                <td class='columnTL' colspan="4" style='font-weight: bold; text-align: center;'>{$rowTotalVendidoQtd['QtdTotalVendido']} ({$totalItens} clientes)</td>
            </tr>
            </table>
            </td></tr></table>
            <span style="font-size: 10px; margin-left: 44px;">* Dados processados com base nos últimos 12 meses</span>
        </div>
EOT;
        echo $htmlQtd;

    break;

    case 'classeCliente':
        $mes = strtotime('-12 month');
        $xLast12 = date('Y-m-d', $mes);
        $sqlD024 = mysql_query("SELECT D024_Id, 
                                       D024_Nome_Empresa, 
                                       D024_Nome_Fantasia, 
                                       D024_Flag_Classe, 
                                       PERIOD_DIFF(DATE_FORMAT(NOW(), '%Y%m'), DATE_FORMAT(IF(IFNULL(D024_Data_Cadastro,'0000-00-00')='0000-00-00', CURDATE(), D024_Data_Cadastro), '%Y%m')) AS mesesCadastro 
                                  FROM D024 
                                 WHERE D024_Id = '{$r_acaoId}'");
        $rowD024 = mysql_fetch_array($sqlD024);

        $D024_Nome_Empresa = substr($rowD024['D024_Nome_Empresa'], 0, 43);

        $sqlC031 = mysql_query("SELECT * FROM C031 WHERE (C031_Campo = 'mesesProcessarClasseCliente'
                                                       OR C031_campo = 'diasConsiderarAtraso'
                                                       OR C031_Campo LIKE 'ticketMedioClienteClasse%'
                                                       OR C031_Campo LIKE 'mediaVendaMesClienteClasse%'
                                                       OR C031_Campo LIKE 'diasAtrasoPgtoClienteClasse%'
                                                       OR C031_Campo LIKE 'mesesCadastroClienteClasse%'
                                                       OR C031_Campo LIKE 'percentualAtrasoClienteClasse%'
                                                       OR C031_Campo LIKE 'prazoVendaClienteClasse%'
                                                       OR C031_Campo LIKE 'qtdTitulosAtrasoValidarMaiorAtrasoClasse%') AND C031_C004_Id = '1'");
        $C031 = array();
        while($rowC031 = mysql_fetch_assoc($sqlC031)){
            $C031[$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
        }

        $C031['ticketMedioClienteClasseA']      = gCorrigeNumero($C031['ticketMedioClienteClasseA']);
        $C031['ticketMedioClienteClasseB']      = gCorrigeNumero($C031['ticketMedioClienteClasseB']);
        $C031['ticketMedioClienteClasseC']      = gCorrigeNumero($C031['ticketMedioClienteClasseC']);
        $C031['ticketMedioClienteClasseD']      = gCorrigeNumero($C031['ticketMedioClienteClasseD']);
        $C031['mediaVendaMesClienteClasseA']    = gCorrigeNumero($C031['mediaVendaMesClienteClasseA']);
        $C031['mediaVendaMesClienteClasseB']    = gCorrigeNumero($C031['mediaVendaMesClienteClasseB']);
        $C031['mediaVendaMesClienteClasseC']    = gCorrigeNumero($C031['mediaVendaMesClienteClasseC']);
        $C031['mediaVendaMesClienteClasseD']    = gCorrigeNumero($C031['mediaVendaMesClienteClasseD']);
        $C031['percentualAtrasoClienteClasseA'] = gCorrigeNumero($C031['percentualAtrasoClienteClasseA']);
        $C031['percentualAtrasoClienteClasseB'] = gCorrigeNumero($C031['percentualAtrasoClienteClasseB']);
        $C031['percentualAtrasoClienteClasseC'] = gCorrigeNumero($C031['percentualAtrasoClienteClasseC']);
        $C031['percentualAtrasoClienteClasseD'] = gCorrigeNumero($C031['percentualAtrasoClienteClasseD']);

        $dataInicio  = date("Y-m-d", strtotime("-{$C031['mesesProcessarClasseCliente']} months"));
        $dataVenda   = strtotime(date("Y-m-d"));
        $ultimaVenda = strtotime($dataInicio);
        $diff        = $dataVenda-$ultimaVenda;
        $totalDias   = floor($diff/(60*60*24));

        //Maior atraso do cliente
        $sqlT002 = mysql_query("SELECT MAX(DATEDIFF(IF(T002_Data_Recebimento = '0000-00-00',CURDATE(),T002_Data_Recebimento),T002_Data_Vencimento)) AS MaiorAtraso, 
                                       ((SUM(IF(DATEDIFF(IF(T002_Data_Recebimento = '0000-00-00',CURDATE(),T002_Data_Recebimento),T002_Data_Vencimento) >= {$g['C031']['diasConsiderarAtraso']}, 1, 0))/COUNT(DISTINCT T002_Id))*100) AS PercentualAtrasos, 
                                       SUM(IF(DATEDIFF(IF(T002_Data_Recebimento = '0000-00-00',CURDATE(),T002_Data_Recebimento),T002_Data_Vencimento) >= {$g['C031']['diasConsiderarAtraso']}, 1, 0)) as qtdTitulosAtrasos
                                  FROM T002 
                                 WHERE T002_Data_Emissao >= '{$dataInicio}' 
                                   AND T002_Flag_Cancelada = 'N' 
                                   AND T002_D024_Id = '{$r_acaoId}' 
                              ORDER BY DATEDIFF(IF(T002_Data_Recebimento = '0000-00-00',CURDATE(),T002_Data_Recebimento),T002_Data_Vencimento) DESC");

        $rowT002 = mysql_fetch_array($sqlT002);

        $rowT002['MaiorAtraso']       = ($rowT002['MaiorAtraso'] < 0) ? '0' : $rowT002['MaiorAtraso'];
        $rowT002['PercentualAtrasos'] = gCorrigeNumero($rowT002['PercentualAtrasos']);
        $rowT002['qtdTitulosAtrasos'] = gCorrigeNumero($rowT002['qtdTitulosAtrasos']);
        $validaQtdTitulosAtraso       = false;

        if($rowD024['D024_Flag_Classe'] == 'A' && $g['C031']['qtdTitulosAtrasoValidarMaiorAtrasoClasseA'] > 1){

            $qtdAtrasos = mysql_query("SELECT COUNT(*) as qtdAtrasos 
                                         FROM T002
                                        WHERE T002_D024_Id = '{$r_acaoId}'
                                          AND T002_Data_Emissao >= '{$dataInicio}'
                                          AND DATEDIFF(IF(T002_Data_Recebimento='0000-00-00',CURDATE(),T002_Data_Recebimento),T002_Data_Vencimento) >= {$g['C031']['diasAtrasoPgtoClienteClasseA']}");
        
            $mQtdAtrasos = mysql_fetch_array($qtdAtrasos);

            $qtdTitulosAtraso       = $mQtdAtrasos['qtdAtrasos'];
            $maiorAtrasoClasse      = $C031['diasAtrasoPgtoClienteClasseA'];
            $validaQtdTitulosAtraso = true;
        }  
    
        if($rowD024['D024_Flag_Classe'] == 'B' && $g['C031']['qtdTitulosAtrasoValidarMaiorAtrasoClasseB'] > 1){
    
            $qtdAtrasos = mysql_query("SELECT COUNT(*) as qtdAtrasos 
                                         FROM T002
                                        WHERE T002_D024_Id = '{$r_acaoId}'
                                          AND T002_Data_Emissao >= '{$dataInicio}'
                                          AND DATEDIFF(IF(T002_Data_Recebimento='0000-00-00',CURDATE(),T002_Data_Recebimento),T002_Data_Vencimento) >= {$g['C031']['diasAtrasoPgtoClienteClasseB']}");
            
            $mQtdAtrasos = mysql_fetch_array($qtdAtrasos);

            $qtdTitulosAtraso       = $mQtdAtrasos['qtdAtrasos'];
            $maiorAtrasoClasse      = $C031['diasAtrasoPgtoClienteClasseB'];
            $validaQtdTitulosAtraso = true;

        }  
    
        if($rowD024['D024_Flag_Classe'] == 'C' && $g['C031']['qtdTitulosAtrasoValidarMaiorAtrasoClasseC'] > 1){
    
            $qtdAtrasos = mysql_query("SELECT COUNT(*) as qtdAtrasos 
                                         FROM T002
                                        WHERE T002_D024_Id = '{$r_acaoId}'
                                          AND T002_Data_Emissao >= '{$dataInicio}'
                                          AND DATEDIFF(IF(T002_Data_Recebimento='0000-00-00',CURDATE(),T002_Data_Recebimento),T002_Data_Vencimento) >= {$g['C031']['diasAtrasoPgtoClienteClasseC']}");
            
            $mQtdAtrasos = mysql_fetch_array($qtdAtrasos);

            $qtdTitulosAtraso       = $mQtdAtrasos['qtdAtrasos'];
            $maiorAtrasoClasse      = $C031['diasAtrasoPgtoClienteClasseC'];
            $validaQtdTitulosAtraso = true;

        }  
    
        if($rowD024['D024_Flag_Classe'] == 'D' && $g['C031']['qtdTitulosAtrasoValidarMaiorAtrasoClasseD'] > 1){
    
            $qtdAtrasos = mysql_query("SELECT COUNT(*) as qtdAtrasos 
                                         FROM T002
                                        WHERE T002_D024_Id = '{$r_acaoId}'
                                          AND T002_Data_Emissao >= '{$dataInicio}'
                                          AND DATEDIFF(IF(T002_Data_Recebimento='0000-00-00',CURDATE(),T002_Data_Recebimento),T002_Data_Vencimento) >= {$g['C031']['diasAtrasoPgtoClienteClasseD']}");
            
            $mQtdAtrasos = mysql_fetch_array($qtdAtrasos);

            $qtdTitulosAtraso       = $mQtdAtrasos['qtdAtrasos'];
            $maiorAtrasoClasse      = $C031['diasAtrasoPgtoClienteClasseD'];
            $validaQtdTitulosAtraso = true;

        }  

        $colspan1 = ($validaQtdTitulosAtraso === true) ? 8 : 7;
        $colspan2 = ($validaQtdTitulosAtraso === true) ? 7 : 6;

        //Ticket médio do cliente
        $sqlT007 = mysql_query("SELECT (SUM(T007_Valor_Total_Produtos)/COUNT(T007_Id)) AS TicketMedio,
                                       (SUM(T007_Valor_Total_Produtos)/{$C031['mesesProcessarClasseCliente']}) AS MediaMes, 
                                       SUM(T007_Valor_Total_Produtos) AS TotalVendas, 
                                       COUNT(T007_Id) AS Frequencia 
                                  FROM T007 
                             LEFT JOIN D006 ON D006_Id=T007_D006_Id
                                 WHERE T007_Data_Emissao >= '{$dataInicio}' 
                                   AND T007_D024_Id = '{$r_acaoId}'
                                   AND T007_Flag_Cancelada!='S' 
                                   AND D006_Flag_Venda_Compra_Outros='V'");
        $rowT007 = mysql_fetch_array($sqlT007);

        $ticketMedio = gCorrigeNumero($rowT007['TicketMedio']);
        $mediaMes    = gCorrigeNumero($rowT007['MediaMes']);
        $totalVendas = gCorrigeNumero($rowT007['TotalVendas']);
        $frequencia  = $rowT007['Frequencia'];

        //Meses de cadastro e prazo entre vendas
        $prazos = array();
        $ultimaVenda = $dataInicio;
        $sqlT007 = mysql_query("SELECT T007_Data_Emissao 
                                  FROM T007 
                             LEFT JOIN D006 ON D006_Id=T007_D006_Id 
                                 WHERE T007_Data_Emissao >= '{$dataInicio}' 
                                   AND T007_D024_Id = '{$rowD024['D024_Id']}' 
                                   AND T007_Flag_Cancelada!='S' 
                                   AND D006_Flag_Venda_Compra_Outros='V'   
                              ORDER BY T007_Data_Emissao");

        if(mysql_num_rows($sqlT007) > 0){
            while($rowT007 = mysql_fetch_array($sqlT007)){
                $dataVenda   = strtotime($rowT007['T007_Data_Emissao']);
                $ultimaVenda = strtotime($ultimaVenda);
                $diff = $dataVenda-$ultimaVenda;
                $prazos[] = floor($diff/(60*60*24));
                $ultimaVenda = $rowT007['T007_Data_Emissao'];
            }
            //Adiciona o prazo da última venda à data atual
            $dataVenda   = strtotime(date("Y-m-d"));
            $ultimaVenda = strtotime($ultimaVenda);
            $diff = $dataVenda-$ultimaVenda;
            $prazos[] = floor($diff/(60*60*24));

            rsort($prazos);
            $maiorPrazo = $prazos[0];
        } else {
            $maiorPrazo = $totalDias;
        }

        $diasContarAtraso = '';
        if($g['C031']['diasConsiderarAtraso'] > 1){
            $diasContarAtraso = "(A partir de {$g['C031']['diasConsiderarAtraso']} dias)";
        }

        $html = <<<EOT
            <style>
                .columnB {
                    border-bottom: 1px solid gray;
                    padding: 3px !important;
                }
                .columnBL {
                    border-bottom: 1px solid gray;
                    border-left: 1px solid gray;
                    padding: 3px !important;
                }
                .columnBR {
                    border-bottom: 1px solid gray;
                    border-right: 1px solid gray;
                    padding: 3px !important;
                }
                .columnBLR {
                    border-left: 1px solid gray;
                    border-bottom: 1px solid gray;
                    border-right: 1px solid gray;
                    padding: 3px !important;
                }
                .columnT {
                    border-top: 1px solid gray;
                    padding: 3px !important;
                }
                .columnTL {
                    border-top: 1px solid gray;
                    border-left: 1px solid gray;
                    padding: 3px !important;
                }
                .columnTR {
                    border-top: 1px solid gray;
                    border-right: 1px solid gray;
                    padding: 3px !important;
                }
                .columnTLR {
                    border-left: 1px solid gray;
                    border-top: 1px solid gray;
                    border-right: 1px solid gray;
                    padding: 3px !important;
                }
            </style>
            <div style="height: auto">
            <div style="font-weight: bold; height: 14px;" title="{$rowD024['D024_Id']} - {$rowD024['D024_Nome_Empresa']}">{$rowD024['D024_Id']} - {$D024_Nome_Empresa}</div>
            <div style="font-weight: bold; margin-top: 3px;">Vendas: R$ {$totalVendas} - Frequência: {$frequencia}</div>
            <table cellspacing="0" cellpadding="2" style="border: 2px solid grey; margin: auto; width: 100%; float: left; margin: 5px 0px 10px 0px;">
                <tr>
                    <td class='columnBR' style='font-weight: bold; text-align: center; padding: 1px 5px;'>Classe</td>
                    <td class='columnBL' style='font-weight: bold; text-align: center; padding: 1px 5px;'>Média <br>mês</td>
                    <td class='columnBL' style='font-weight: bold; text-align: center; padding: 1px 5px;'>Ticket <br>médio</td>
                    <td class='columnBL' style='font-weight: bold; text-align: center; padding: 1px 5px;'>Meses <br>cadastrado</td>
                    <td class='columnBL' style='font-weight: bold; text-align: center; padding: 1px 5px;'>Intervalo <br>vendas <br>(dias)</td>
                    <td class='columnBL' style='font-weight: bold; text-align: center; padding: 1px 5px;'>Maior <br>atraso <br>(dias)</td>
EOT;

                    if($validaQtdTitulosAtraso === true){
                        $html .= <<<EOT
                            <td class='columnBL' style='font-weight: bold; text-align: center; padding: 1px 5px;'>Qtd tít.atraso<br>maior/igual à $maiorAtrasoClasse dias<br><i><u>(maior atraso)</u></i></td>
EOT;
                    }
            $html .= <<<EOT

                    <td class='columnBL' style='font-weight: bold; text-align: center; padding: 1px 5px;'>Percentual <br>atrasos<br>$diasContarAtraso</td>
                </tr>
                <tr>
                    <td class='columnTR' style='font-weight: bold; text-align: center; padding: 1px 5px;'>{$rowD024['D024_Flag_Classe']}</td>
                    <td class='columnTL' style='font-weight: bold; text-align: center; padding: 1px 5px;'>{$mediaMes}</td>
                    <td class='columnTL' style='font-weight: bold; text-align: center; padding: 1px 5px;'>{$ticketMedio}</td>
                    <td class='columnTL' style='font-weight: bold; text-align: center; padding: 1px 5px;'>{$rowD024['mesesCadastro']}</td>
                    <td class='columnTL' style='font-weight: bold; text-align: center; padding: 1px 5px;'>{$maiorPrazo}</td>
                    <td class='columnTL' style='font-weight: bold; text-align: center; padding: 1px 5px;'>{$rowT002['MaiorAtraso']}</td>
EOT;
            if($validaQtdTitulosAtraso === true){
                $html .= <<<EOT
                    <td class='columnTL' style='font-weight: bold; text-align: center; padding: 1px 5px;'>{$qtdTitulosAtraso}</td>
EOT;           
            }       
            
            $html .= <<<EOT
         
                    <td class='columnTL' style='font-weight: bold; text-align: center; padding: 1px 5px;'>{$rowT002['PercentualAtrasos']} %</td>
                </tr>
                <tr>
                    <td class='columnT' colspan="{$colspan1}" style='font-weight: bold; text-align: center; padding: 1px 1px;'>Parâmetros</td>
                </tr>
                <tr>
                    <td class='columnTR' style='font-size: 11px; text-align: center; padding: 1px 5px;'>A</td>
                    <td class='columnTL' style='font-size: 11px; text-align: right; padding: 1px 5px;'>{$C031['mediaVendaMesClienteClasseA']}</td>
                    <td class='columnTL' style='font-size: 11px; text-align: right; padding: 1px 5px;'>{$C031['ticketMedioClienteClasseA']}</td>
                    <td class='columnTL' style='font-size: 11px; text-align: center; padding: 1px 5px;'>{$C031['mesesCadastroClienteClasseA']}</td>
                    <td class='columnTL' style='font-size: 11px; text-align: center; padding: 1px 5px;'>{$C031['prazoVendaClienteClasseA']}</td>
                    <td class='columnTL' style='font-size: 11px; text-align: center; padding: 1px 5px;'>{$C031['diasAtrasoPgtoClienteClasseA']}</td>
EOT;
            if($validaQtdTitulosAtraso === true){
                $html .= <<<EOT
                    <td class='columnTL' style='font-size: 11px; text-align: center; padding: 1px 5px;'>{$C031['qtdTitulosAtrasoValidarMaiorAtrasoClasseA']}</td>
EOT;
            }

            $html .= <<<EOT
                    <td class='columnTL' style='font-size: 11px; text-align: center; padding: 1px 5px;'>{$C031['percentualAtrasoClienteClasseA']} %</td>
                </tr>
                <tr>
                    <td class='columnTR' style='font-size: 11px; text-align: center; padding: 1px 5px;'>B</td>
                    <td class='columnTL' style='font-size: 11px; text-align: right; padding: 1px 5px;'>{$C031['mediaVendaMesClienteClasseB']}</td>
                    <td class='columnTL' style='font-size: 11px; text-align: right; padding: 1px 5px;'>{$C031['ticketMedioClienteClasseB']}</td>
                    <td class='columnTL' style='font-size: 11px; text-align: center; padding: 1px 5px;'>{$C031['mesesCadastroClienteClasseB']}</td>
                    <td class='columnTL' style='font-size: 11px; text-align: center; padding: 1px 5px;'>{$C031['prazoVendaClienteClasseB']}</td>
                    <td class='columnTL' style='font-size: 11px; text-align: center; padding: 1px 5px;'>{$C031['diasAtrasoPgtoClienteClasseB']}</td>
EOT;            
            if($validaQtdTitulosAtraso === true){
                $html .= <<<EOT
                    <td class='columnTL' style='font-size: 11px; text-align: center; padding: 1px 5px;'>{$C031['qtdTitulosAtrasoValidarMaiorAtrasoClasseB']}</td>
EOT;
            }

            $html .= <<<EOT
                    <td class='columnTL' style='font-size: 11px; text-align: center; padding: 1px 5px;'>{$C031['percentualAtrasoClienteClasseB']} %</td>
                </tr>
                <tr>
                    <td class='columnTR' style='font-size: 11px; text-align: center; padding: 1px 5px;'>C</td>
                    <td class='columnTL' style='font-size: 11px; text-align: right; padding: 1px 5px;'>{$C031['mediaVendaMesClienteClasseC']}</td>
                    <td class='columnTL' style='font-size: 11px; text-align: right; padding: 1px 5px;'>{$C031['ticketMedioClienteClasseC']}</td>
                    <td class='columnTL' style='font-size: 11px; text-align: center; padding: 1px 5px;'>{$C031['mesesCadastroClienteClasseC']}</td>
                    <td class='columnTL' style='font-size: 11px; text-align: center; padding: 1px 5px;'>{$C031['prazoVendaClienteClasseC']}</td>
                    <td class='columnTL' style='font-size: 11px; text-align: center; padding: 1px 5px;'>{$C031['diasAtrasoPgtoClienteClasseC']}</td>
EOT; 
            if($validaQtdTitulosAtraso === true){
                $html .= <<<EOT
                    <td class='columnTL' style='font-size: 11px; text-align: center; padding: 1px 5px;'>{$C031['qtdTitulosAtrasoValidarMaiorAtrasoClasseC']}</td>
EOT;
            }

            $html .= <<<EOT
                    <td class='columnTL' style='font-size: 11px; text-align: center; padding: 1px 5px;'>{$C031['percentualAtrasoClienteClasseC']} %</td>
                </tr>
                <tr>
                    <td class='columnTR' style='font-size: 11px; text-align: center; padding: 1px 5px;'>D</td>
                    <td class='columnTL' style='font-size: 11px; text-align: right; padding: 1px 5px;'>{$C031['mediaVendaMesClienteClasseD']}</td>
                    <td class='columnTL' style='font-size: 11px; text-align: right; padding: 1px 5px;'>{$C031['ticketMedioClienteClasseD']}</td>
                    <td class='columnTL' style='font-size: 11px; text-align: center; padding: 1px 5px;'>{$C031['mesesCadastroClienteClasseD']}</td>
                    <td class='columnTL' style='font-size: 11px; text-align: center; padding: 1px 5px;'>{$C031['prazoVendaClienteClasseD']}</td>
                    <td class='columnTL' style='font-size: 11px; text-align: center; padding: 1px 5px;'>{$C031['diasAtrasoPgtoClienteClasseD']}</td>
EOT; 
            if($validaQtdTitulosAtraso === true){
                $html .= <<<EOT
                    <td class='columnTL' style='font-size: 11px; text-align: center; padding: 1px 5px;'>{$C031['qtdTitulosAtrasoValidarMaiorAtrasoClasseD']}</td>
EOT;
            }    

            $html .= <<<EOT
                    <td class='columnTL' style='font-size: 11px; text-align: center; padding: 1px 5px;'>{$C031['percentualAtrasoClienteClasseD']} %</td>
                </tr>
                <tr>
                    <td class='columnTR' style='font-size: 11px; text-align: center; padding: 1px 5px;'>X</td>
                    <td colspan="{$colspan2}" class='columnTL' style='font-size: 11px; text-align: center; padding: 1px 5px;'>Não se encaixa nos parâmetros</td>
                </tr>
            </table>
            <div style="font-size: 12px">* Dados processados com base nos últimos {$C031['mesesProcessarClasseCliente']} meses</div>
            </div>
EOT;
        echo $html;
    break;

    case 'cad001ManualCSVCliente':

        echo <<<EOT
        
        <style>
            .tabelaManual{
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
            .linhaTabelaManual{               
                font-size:15px;
                padding:10px 15px;
                background-color:#FFF;
                text-align:center;
                

            }   
        </style>

        <div style="padding-left:10px;">
        <h2>Modelo de documento CSV</h2>
        <h3>Observações:</h3>
        <p style="font-size:12px">-Abaixo está o formato do modelo do documento para a importação!
        <br/>-O documento deve ter <strong>63 colunas</strong> ao todo, caso seja necessário modificar o número de colunas para a importação, contactar o suporte técnico.
        <br/>-Os nomes das colunas devem estar necessáriamente na primeira linha(1) do documento e ter a mesma grafia do documento modelo.
        <br/>-A extensão do documento deve ser CSV (Delimitado por vírgulas)(*.csv)
        <br/>-Ao final do documento não deve haver nenhuma linha em branco. 
        <br/>-Os campos obrigatórios estão identificados pelo (*).
        <br/>-O campo Pessoa deve ser preenchido com P(Pessoa física), J(Pessoa Jurídica) ou I(Pessoa Internacional). Caso seja preenchido com J o campo CNPJ é obrigatório, caso seja preenchido com F o campo CPF é orbigatório.
        <br/>-Informe o CPF no formato "XXX.XXX.XXX-XX".
        <br/>-Informe o CNPJ no formato "XX.XXX.XXX/XXXX-XX".
        <br/>-Os campos Pre-Cadastro,Cliente,Fornecedor,Permite Emitir ACP,Cliente Ativo, Credito Liberado, Protestar, Agrupamento Auto,Empresa Grupo somente podem ser preenchidos por S(Sim) ou N(Não).
        <br/>-O campo Tipo permite os seguintes valores 0-(não contriguinte), 1-(Consumidor Final), 2-(Revenda), 3-(Produtor rural), 4-(Simples), 5-(Industria).
        <br/>-O campo CRT permite os seguintes valores 1-(Simples nacional), 2-(simples nascional é excesso de sublimite), 3-(Regime nacional).
        <br/>-O campo Lista Preco permite os seguintes valores 1-(Lista Preco 1), 2-(Lista Preco 2), 3-(Lista Preco 3), 4-(Lista Preco 4).
        <br/>-O campo Sit. Comercial permite os seguintes valores A-(Ativo) ou I-(Inativo).
        <br/>-Nos campos numéricos utilizar apenas vírgula como separador decimal e não utilizar ponto. Ex: 2,50; 2500,36
        <br/>-<a href="{$confUsuario['urlRaiz']}hardness3/prog/cad/cad001/modeloCSVcliente.csv?ajax=true" style="color:green" target=_blank>BAIXAR MODELO CSV</a>
        </p>
        </div>
        <table cellspacing=1px bgcolor="black">     
            <tr>        
                <td class="linhaTabelaManual">*Nome</td>
                <td class="linhaTabelaManual" >*Fantasia</td>
                <td class="linhaTabelaManual" >*CNPJ</td>
                <td class="linhaTabelaManual" >*CPF</td>
                <td class="linhaTabelaManual" >IE</td>
                <td class="linhaTabelaManual" >RG</td>
                <td class="linhaTabelaManual" >*Pessoa</td>
                <td class="linhaTabelaManual" >*Tipo</td>
                <td class="linhaTabelaManual" >*DDD Telefone 1</td>
                <td class="linhaTabelaManual" >*Telefone 1</td>
                <td class="linhaTabelaManual" >DDD Telefone 2</td>
                <td class="linhaTabelaManual" >Telefone 2</td>
                <td class="linhaTabelaManual" >Contato</td>
                <td class="linhaTabelaManual" >DDD Celular</td>
                <td class="linhaTabelaManual" >Celular</td>
                <td class="linhaTabelaManual" >DDD Fax</td>
                <td class="linhaTabelaManual" >Fax</td>
                <td class="linhaTabelaManual" >E-mail</td>
                <td class="linhaTabelaManual" >E-mail NF</td>
                <td class="linhaTabelaManual" >E-mail Boleto</td>
                <td class="linhaTabelaManual" >Obs</td>
                <td class="linhaTabelaManual" >CEP</td>
                <td class="linhaTabelaManual" >Endereco</td>
                <td class="linhaTabelaManual" >Complemento</td>
                <td class="linhaTabelaManual" >Numero</td>
                <td class="linhaTabelaManual" >Bairro</td>
                <td class="linhaTabelaManual" >*UF</td>
                <td class="linhaTabelaManual" >*Cidade</td>
                <td class="linhaTabelaManual" >*Pais</td>
                <td class="linhaTabelaManual" >*CRT</td>
                <td class="linhaTabelaManual" >Website</td>
                <td class="linhaTabelaManual" >*Cliente</td>
                <td class="linhaTabelaManual" >*Fornecedor</td>
                <td class="linhaTabelaManual" >*Pre-Cadastro</td>
                <td class="linhaTabelaManual" >Fundacao/nascimento</td>
                <td class="linhaTabelaManual" >*Lista Preco</td>
                <td class="linhaTabelaManual" >Vendedor Principal</td>
                <td class="linhaTabelaManual" >Comissao</td>
                <td class="linhaTabelaManual" >Vendedor Externo</td>
                <td class="linhaTabelaManual" >Comissao</td>
                <td class="linhaTabelaManual" >Representante</td>
                <td class="linhaTabelaManual" >Comissao</td>
                <td class="linhaTabelaManual" >Data Ativo</td>
                <td class="linhaTabelaManual" >Data Inativo</td>
                <td class="linhaTabelaManual" >Sit.Comercial</td>
                <td class="linhaTabelaManual" >Permite Emitir ACP</td>
                <td class="linhaTabelaManual" >Transportadora</td>
                <td class="linhaTabelaManual" >Obs Comercial</td>
                <td class="linhaTabelaManual" >IPV Venda</td>
                <td class="linhaTabelaManual" >Matriz</td>
                <td class="linhaTabelaManual" >Dia visita</td>
                <td class="linhaTabelaManual" >*Cliente ativo?</td>
                <td class="linhaTabelaManual" >Credito Liberado</td>
                <td class="linhaTabelaManual" >Obs Financeira</td>
                <td class="linhaTabelaManual" >Instrucao pagamento</td>
                <td class="linhaTabelaManual" >Valor Limite/Fat.Minimo</td>
                <td class="linhaTabelaManual" >Protestar?</td>
                <td class="linhaTabelaManual" >Agrupamento Auto.</td>
                <td class="linhaTabelaManual" >Empresa Grupo</td>
                <td class="linhaTabelaManual" >1 Vencimento</td>
                <td class="linhaTabelaManual" >Dia fixo/Intervalo</td>
                <td class="linhaTabelaManual" >Num. Parcelas</td>
                <td class="linhaTabelaManual" >Portador</td>
            </tr>
            <tr>            
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
            </tr>
            <tr>            
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
            </tr>
            <tr>            
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>               
            </tr>
            <tr>        
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>               
            </tr>   
        </table>
EOT;
 
    break;

	/**
	 * Mostrar Sintegra
	 * Chamado em: cad001tabCadastro
	 */
	case 'cad001MostraSintegra':
        global $g;

        $D024 = mysql_query("select D024_Json_Sintegra,D024_CNPJ,D024_Id,D024_Data_Json_Sintegra from D024 where D024_Id='$r_acaoId'");
        $mD024 = mysql_fetch_assoc($D024);

        echo mysql_error();

        $array = unserialize($mD024['D024_Json_Sintegra']);
        $mD024['D024_Data_Json_Sintegra'] = gCorrigeData($mD024['D024_Data_Json_Sintegra']);

        echo "<p><font style='font-weight:bold;font-size:18px;align:center'>SINTEGRA</font> &nbsp; <input type='button' id='atualizaSintegra' value='Atualizar'>&nbsp;&nbsp;<font style='font-size:12px'>Atualizado no sistema {$mD024['D024_Data_Json_Sintegra']}</font></p>";

        echo arrayToHTML($array);

        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') { 
                $('#atualizaSintegra').unbind('click').bind('click', function(data) {
                    var \$buttons = {
                        "Sim": function() {
                            $('#' + '{$g['divId']}').showLoading();
                            $.getJSON('/sistema/funcoes/pesquisaCNPJ/retornaSintegraCliente/?CNPJ=' + encodeURIComponent('{$mD024['D024_CNPJ']}') + '&D024_Id=' + encodeURIComponent('{$mD024['D024_Id']}') + '&ajax=true&callback=?', function(request) {
                                if (request.code) {
                                    var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                                    dialogConfirm('Ok', 'Sintegra atualizado com sucesso!');
                                    divRefresh('{$g['divId']}', true);
                                } else {
                                    var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                                    dialogConfirm('Erro', request.data, \$buttons);
                                }
                                $('#' + '{$g['divId']}').hideLoading();
                            });
                            $(this).dialog("destroy").remove();
                        },
                        "Não": function() { $(this).dialog("destroy").remove(); } 
                    };
                    dialogConfirm('Atualizar Sintegra', 'Confirma atualização dos dados Sintegra?', \$buttons);
                });
            } </script></div>
        EOT;

    break;

}

function arrayToHTML($array){

    $html = "<table cellspacing=2 style='font-size:13px'>";

    foreach($array as $key => $value) {
        $key = ucwords(str_replace('_',' ',$key));
        $html .= "<tr><td style='background:var(--fundo-campo-form);padding:4px;font-weight:bold'>";
            $html .= $key;
        $html .= "</td>";
        if (is_array($value)){
            $html .= "<td>";
                $html .= arrayToHTML($value);
            $html .= "</td></tr>";
        } else {
            $html .= "<td style='background:var(--fundo-campo-form);padding:4px'>";
                $html .= $value;
            $html .= "</td></tr>";
        }
    }

    $html .= "</table>";

    return $html;
}

		

