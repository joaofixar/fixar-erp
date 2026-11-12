<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {
    case 'est017CabecalhoOrdemProducao':
        // Select
        $sql = "SELECT * FROM T255 LEFT JOIN C007 ON C007_Id=T255_C007_Id WHERE T255_Id = '{$r_acaoId}'";
        $resultado = mysql_query($sql);
        $row = mysql_fetch_assoc($resultado);
        $row['T255_Data_Emissao'] = gCorrigeData($row['T255_Data_Emissao']);
        if ($row['T255_Flag_Status'] == '99') {
            $status = 'Finalizado';
        } else if ($row['T255_Flag_Status'] == '98') {
            $status = 'Cancelado';
        } else {
            $status = 'Aberto';
        }
        // Print
        echo <<<EOT
            <div class="contentCabecalhoLayout">
                <table class="tabela ui-state-default ui-corner-all">
                    <tr class="linhaDesc">
                        <td width='50px'>OP</td>
                        <td width='70px'>Emissão</td>
                        <td width='150px'>Usuário</td>
                        <td width='150px'>Status</td>
                    </tr>
                    <tr class="linhaVal">
                        <td align='center'>{$row['T255_Id']}</td>
                        <td>{$row['T255_Data_Emissao']}</td>
                        <td>{$row['C007_Primeiro_Nome']}</td>
                        <td>{$status}</td>
                    </tr>
                </table>
            </div>
EOT;

    break;

    case 'est017CabecalhoEntradaProducao':
		// Select
		$sql = "SELECT * FROM T256
                    LEFT JOIN D009 on D009_Id=T256_D009_Id
                    LEFT JOIN D049 on D049_Id=D009_D049_Id
                    LEFT JOIN D001 on D001_Id=D049_D001_Id WHERE T256_Id = '{$r_acaoId}'";
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);

        $row['T256_Quantidade'] = gCorrigeNumero($row['T256_Quantidade'], 3);
        $row['T256_Quantidade_Produzida'] = gCorrigeNumero($row['T256_Quantidade_Produzida'], 3);
        $row['T256_Quantidade_Saldo'] = gCorrigeNumero($row['T256_Quantidade_Saldo'], 3);
		// Print
		echo <<<EOT
			<div class="contentCabecalhoLayout" style="cursor: pointer;" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar Ordem de produção', '/est/est017/content/est017ContentOrdemProducao/', '&acaoId=' + encodeURIComponent('{$row['T256_T255_Id']}') + '&tabela=T225', [990,605]);">
				<table class="tabela ui-state-default ui-corner-all">
					<tr class="linhaDesc">
						<td width='50px'>OP</td>
						<td width='80px'>Código</td>
						<td width='200px'>Descrição</td>
                        <td width='60px'>Quantidade</td>
                        <td width='60px'>Produzido</td>
						<td width='60px'>Saldo</td>
					</tr>
					<tr class="linhaVal">
						<td align='center'>{$row['T256_T255_Id']}</td>
						<td>{$row['D001_Codigo_Produto']}</td>
						<td>{$row['D001_Descricao_Produto']}</td>
                        <td>{$row['T256_Quantidade']}</td>
                        <td>{$row['T256_Quantidade_Produzida']}</td>
						<td>{$row['T256_Quantidade_Saldo']}</td>
					</tr>
				</table>
			</div>
EOT;

	break;

    case 'imprimirOrdemProducao':
        $T255_Id = $r_T255_Id;
        $agora = date("d/m/Y H:i:s");
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
            $caminhoImagem = "<img src=\"{$caminhoImagem}\">";
        } else {
            $caminhoImagem = "";
        }

        $cabecalho = <<<EOT
                    <table align=center border='0' valign=top cellpadding=3 cellspacing=0 bgcolor=white>
                        <tr>
                            <td valign=top>
                                {$caminhoImagem}
                            </td>
                            <td bgcolor=white width=80%>
                                <table cellspacing=0 cellpading=0 border='0' align=center height=70>
                                    <tr>
                                        <td><font class=fonte style='font-size:12px'><b>{$g['C004']['C004_Nome_Empresa']}</b></font></td>
                                    </tr>
                                    <tr>
                                        <td><font class=fonte>{$g['C004']['C004_Logradouro']}, {$g['C004']['C004_Numero']} {$g['C004']['C004_Complemento']}</font></td>
                                    </tr>
                                    <tr>
                                        <td><font class=fonte>{$g['C004']['C004_Cep']} - {$g['C004']['C004_Cidade']} - {$g['C004']['C004_Estado']}</font></td>
                                    </tr>
                                    <tr>
                                        <td><font class=fonte>Fone/Fax: {$g['C004']['C004_Telefone']} Fax: {$g['C004']['C004_Fax']}</font></td>
                                    </tr>
                                    <tr>
                                        <td><font class=fonte>{$g['C004']['C004_Site']}</font></td>
                                    </tr>
                                </table>
                            </td>
                            <td bgcolor=white align=center width=20%>
                                <font class='titulo_relatorio'>Ordem de Produção {$T255_Id}</font>
                                </font><br>
                            </td>
                        </tr>
                        <tr bgcolor=white>
                            <td colspan=3 style="text-align: right;">
                                <font class=fontemed>Impresso</b> &nbsp;<b>{$agora}</b></font>
                            </td>
                        </tr>
                    </table>
EOT;
        $varCabecalhoTela = ($r_imprimirSequencia == 'false') ? 'none' : 'inline';
        $varCabecalhoImpresso = ($r_imprimirSequencia == 'false') ? 'inline' : 'none';
        $pageBreak = ($r_imprimirSequencia == 'false') ? 'always' : 'none';
        $html = <<<EOT
            <html>
                <head>
                    <title>Impressão Ordem de Produção {$T255_Id}</title>
                    <link rel=stylesheet href="/hardness/classes/classe_tamanho_medio_firefox.css" type="text/css">
                </head>
                <style>
                    .fontemed {
                        font-size: 11px;
                        font-family: Arial, Verdana;
                    }
                    @media screen {
                        div.cabecalhoTela {
                            display: inline;
                        }
                        div.cabecalhoImpresso {
                            display: none;
                        }
                    }
                    @media print {
                        div.cabecalhoTela {
                            display: {$varCabecalhoTela};
                        }
                        div.cabecalhoImpresso {
                            display: {$varCabecalhoImpresso};
                        }
                    }
                </style>
                <body bottommargin=0 topmargin=0 rightmargin=0 leftmargin=0 bgcolor='white'>
                    <div style="width: 820px; margin: auto;">
                    <div class="cabecalhoTela">{$cabecalho}</div>
EOT;
        $sqlT256 = mysql_query("SELECT *,Date_add(T005_Data_Emissao, interval T006_Observacao_Entrega day) dataEntrega
                                    FROM T256 
                                    LEFT JOIN D009 ON D009_Id=T256_D009_Id 
                                    LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                    LEFT JOIN D001 ON D001_Id=D049_D001_Id
                                    LEFT JOIN D037 ON D037_Id=D001_D037_Id
                                    LEFT JOIN D082 ON D082_Id=D049_D082_Id
                                    LEFT JOIN T006 on T006_Id=T256_T006_Id
                                    LEFT JOIN T005 on T005_Id=T006_T005_Id
                                    LEFT JOIN D024 on D024_Id=T005_D024_Id
                                   WHERE T256_T255_Id = '{$r_T255_Id}'");
        while($rowT256 = mysql_fetch_assoc($sqlT256)){
            //$rowT256['D001_Descricao_Produto'] = substr($rowT256['D001_Descricao_Produto'], 0, 25);
            $quantidadeT256 = $rowT256['T256_Quantidade'];
            $rowT256['T256_Quantidade'] = gCorrigeNumero($rowT256['T256_Quantidade'],3);
            $rowT256['T256_Data_Previsao_Entrega'] = gCorrigeData($rowT256['T256_Data_Previsao_Entrega']);
            $rowT256['D082_Marca'] = substr($rowT256['D082_Marca'], 0, 20);
            $rowT256['T005_Data_Emissao'] = gCorrigeData($rowT256['T005_Data_Emissao']); 
            $rowT256['dataEntrega'] = gCorrigeData($rowT256['dataEntrega']); 
            if ($rowT256['D001_Flag_Desenho_Tecnico']=='N') {
                $rowT256['D001_Flag_Desenho_Tecnico']='Não';
            } else {
                $rowT256['D001_Flag_Desenho_Tecnico']='Sim';
            }
            $html .= <<<EOT
                    <div class="cabecalhoImpresso">{$cabecalho}</div>
                    <div class="fontemed" style='font-size: 16px; text-align:left; margin: 2px; font-weight: bold;'>Produto</div>
                    <table border=0 cellspacing=2 width=100% cellpadding=2>
                        <tr bgcolor='silver'>
                            <td align=center width='60' class="fontemed">
                                Qte
                            </td>
                            <td align=center width='60' class="fontemed">
                                Un
                            </td>
                            <td align=left  class="fontemed">
                                Referência
                            </td>
                            <td align=center  class="fontemed">
                                D.Técnico
                            </td>
                            <td align=center width='300' class="fontemed">
                                Produto
                            </td>
                            <td align=center width='140' class="fontemed">
                                Previsão entrega
                            </td>
                            <td align=left width='170' class="fontemed">
                                Observação
                            </td>
                        </tr>
                        <tr bgcolor=white Id='T256_Id-{$rowT256['T256_Id']}' height=19>
                            <td align=center class="fontemed" style='font-size:13px;font-weight:bold'>
                                {$rowT256['T256_Quantidade']}
                            </td>
                            <td align=center class="fontemed" style='font-size:13px;font-weight:bold'>
                                {$rowT256['D037_Unidade']}
                            </td>
                            <td align=left class="fontemed" style='font-size:13px;font-weight:bold'>
                                {$rowT256['D001_Codigo_Produto']}
                            </td>
                            <td align=center class="fontemed" style='font-size:13px;font-weight:bold'>
                                {$rowT256['D001_Flag_Desenho_Tecnico']}
                            </td>
                            <td align=left class="fontemed" style='font-size:13px;font-weight:bold'>
                                {$rowT256['D001_Descricao_Produto']}
                            </td>
                            <td align=center class="fontemed" style='font-size:13px;font-weight:bold'>
                                {$rowT256['T256_Data_Previsao_Entrega']}
                            </td>
                            <td align=left class="fontemed" style='font-size:13px;font-weight:bold'>
                                {$rowT256['T256_Observacao_Producao']}
                            </td>
                        </tr>
                    </table>
 
                    <table border=0 cellspacing=2 width=100% cellpadding=2>
                         <tr bgcolor='silver'>
                            <td align=center class="fontemed">
                                Pedido
                            </td>
                            <td align=left class="fontemed">
                                Cliente
                            </td>
                            <td align=center  class="fontemed">
                                Emissão
                            </td>
                            <td align=center class="fontemed">
                                Entrega
                            </td>
                            <td align=center class="fontemed">
                                Prazo
                            </td>
                        </tr>
                        <tr bgcolor=white Id='T256_Id-{$rowT256['T256_Id']}' height=19>
                            <td align=center class="fontemed" style='font-size:12px;font-weight:normal'>
                                {$rowT256['T005_Id']}
                            </td>
                            <td align=left class="fontemed" style='font-size:12px;font-weight:normal'>
                                {$rowT256['D024_Id']}
                            </td>
                            <td align=center class="fontemed" style='font-size:12px;font-weight:normal'>
                                {$rowT256['T005_Data_Emissao']}
                            </td>
                            <td align=center class="fontemed" style='font-size:12px;font-weight:normal'>
                                {$rowT256['dataEntrega']}
                            </td>
                            <td align=center class="fontemed" style='font-size:12px;font-weight:normal'>
                                {$rowT256['T006_Observacao_Entrega']}
                            </td>
                           </tr>
                    </table>
                    <p>
                    <div class="fontemed" style='font-size: 16px; text-align:left; margin: 2px; font-weight: bold;'>Matéria prima</div>
                    <table bgcolor=silver cellspacing=1 width=100% cellpadding=2>
                        <tr bgcolor=white>
                            <td align=left style="background-color: #DDD;" width='100' class="fontepeq">
                                Referência
                            </td>
                            <td align=center style="background-color: #DDD;" class="fontepeq">
                                Produto
                            </td>
                            <td align=center style="background-color: #DDD;" class="fontepeq">
                                Un
                            </td>
                            <td align=center style="background-color: #DDD;" width='60' class="fontepeq">
                                Qtde. unitária
                            </td>
                            <td align=center style="background-color: #DDD;" width='60' class="fontepeq">
                                Qtde total
                            </td>
                            <td align=center style="background-color: #DDD;" width='60' class="fontepeq">
                                Estoque
                            </td>
                            <td align=center style="background-color: #DDD;" width='100' class="fontepeq">
                                Locações
                            </td>
                        </tr>
EOT;
            $sqlT261 = mysql_query("SELECT * 
                                      FROM T261
                                 LEFT JOIN D001 ON D001_Id = T261_D001_Id_Composicao
                                 LEFT JOIN D049 ON D001_Id = D049_D001_Id
                                 LEFT JOIN D009 ON D049_Id = D009_D049_Id
                                 LEFT JOIN D037 ON D037_Id = D001_D037_Id
                                 LEFT JOIN D082 ON D082_Id = D049_D082_Id
                                     WHERE T261_T256_Id = '{$rowT256['T256_Id']}' 
                                       AND D009_C004_Id='{$g['empresaAtual']}'
                                       AND IFNULL(D009_Id,0) > 0");
            //Matéria prima
            while($rowT261 = mysql_fetch_assoc($sqlT261)){
                $quantidadeTotal = gCorrigeNumero($rowT261['T261_Quantidade']*$quantidadeT256, 3);
                $rowT261['T261_Quantidade'] = gCorrigeNumero($rowT261['T261_Quantidade'],5);
                $rowT261['D009_Quantidade_Estoque_Liquido'] = gCorrigeNumero($rowT261['D009_Quantidade_Estoque_Liquido'],3);
                //$rowT261['D001_Descricao_Produto'] = substr($rowT261['D001_Descricao_Produto'], 0, 25);
                $rowT261['D082_Marca'] = substr($rowT261['D082_Marca'], 0, 20);
                $html .= <<<EOT
                        <tr bgcolor=white>
                            <td class="fontemed" style="text-align: left; font-size:12px;">
                                {$rowT261['D001_Codigo_Produto']}
                            </td>
                            <td class="fontemed" style="text-align: left; font-size:12px;">
                                {$rowT261['D001_Descricao_Produto']}
                            </td>
                            <td class="fontemed" style="text-align: center; font-size:12px;">
                                {$rowT261['D037_Unidade']}
                            </td>
                            <td class="fontemed" style="text-align: center; font-size:12px;">
                                {$rowT261['T261_Quantidade']}
                            </td>
                            <td class="fontemed" style="text-align: center; font-size:12px;">
                                {$quantidadeTotal}
                            </td>
                            <td class="fontemed" style="text-align: right; font-size:12px;">
                                {$rowT261['D009_Quantidade_Estoque_Liquido']}
                            </td>
EOT;
                $locacoes = '';
                $sqlD004 = mysql_query("SELECT * FROM T066 LEFT JOIN D004 ON D004_Id=T066_D004_Id WHERE T066_D009_Id = '{$rowT261['D009_Id']}'");
                while ($rowD004 = mysql_fetch_assoc($sqlD004)) {
                    $locacoes .= (empty($locacoes)) ? $rowD004['D004_Local'] : ', ' . $rowD004['D004_Local'];
                }
                $html .= <<<EOT
                            <td class="fontemed" style="text-align: left; font-size:12px;">
                                {$locacoes}
                            </td>
                        </tr>
EOT;
            }
            $html .= <<<EOT
                    </table>
                    <br>
                    <div class="fontemed" style='font-size: 16px; text-align:left; margin: 2px; font-weight: bold;'>Processos</div>
                    <table bgcolor=silver cellspacing=1 width=100% cellpadding=2 style="page-break-after: {$pageBreak}; margin-bottom: 20px;">
EOT;
            $sqlT262 = mysql_query("SELECT * FROM T262
                                        LEFT JOIN D078 on D078_Id = T262_D078_Id 
                                        LEFT JOIN D079 on D079_Id = T262_D079_Id 
                                            WHERE T262_T256_Id = '{$rowT256['T256_Id']}'
                                         ORDER BY T262_Ordem");
            //Processos
            while($rowT262 = mysql_fetch_assoc($sqlT262)){
                $rowT262['T262_Lead_Time'] = gCorrigeNumero($rowT262['T262_Lead_Time']);
                $html .= <<<EOT
                        <tr bgcolor=white>
                            <td style="text-align: center; background-color: #DDD;" class="fontepeq">
                                Ordem
                            </td>
                            <td style="text-align: center; background-color: #DDD;" class="fontepeq">
                                Máquina
                            </td>
                            <td style="text-align: center; background-color: #DDD;" class="fontepeq">
                                Função
                            </td>
                            <td style="text-align: center; background-color: #DDD;" class="fontepeq">
                                Descrição
                            </td>
                            <td style="text-align: center; background-color: #DDD;" class="fontepeq">
                                Funcionários
                            </td>
                            <td style="text-align: center; background-color: #DDD;" class="fontepeq">
                                Lead time
                            </td>
                            <td style="text-align: center; background-color: #DDD;" class="fontepeq">
                                Material
                            </td>
                        </tr>
                        <tr bgcolor=white>
                            <td class="fontemed" style="text-align: center; vertical-align: top; font-size:12px;">
                                {$rowT262['T262_Ordem']}
                            </td>
                            <td class="fontemed" style="text-align: left; vertical-align: top; font-size:12px;">
                                {$rowT262['D079_Nome']}
                            </td>
                            <td class="fontemed" style="text-align: left; vertical-align: top; font-size:12px;">
                                {$rowT262['D078_Nome']}
                            </td>
                            <td class="fontemed" style="text-align: left; vertical-align: top; font-size:12px;">
                                {$rowT262['T262_Descricao']}
                            </td>
                            <td class="fontemed" style="text-align: center; vertical-align: top; font-size:12px;">
                                {$rowT262['T262_Numero_Funcionarios']}
                            </td>
                            <td class="fontemed" style="text-align: center; vertical-align: top; font-size:12px;">
                                {$rowT262['T262_Lead_Time']}
                            </td>
                            <td class="fontemed" style="text-align: left; font-size:12px; vertical-align: top;">
EOT;
                //Material consumo
                $sqlT263 = mysql_query("SELECT D001_Codigo_Produto, D001_Descricao_Produto, T263_Quantidade, D009_Quantidade_Estoque_Liquido 
                                          FROM T263
                                     LEFT JOIN D009 on D009_Id = T263_D009_Id
                                     LEFT JOIN D049 ON D049_Id = D009_D049_Id
                                     LEFT JOIN D001 ON D001_Id = D049_D001_Id
                                     LEFT JOIN D037 ON D037_Id = D001_D037_Id
                                     WHERE T263_T262_Id = '{$rowT262['T262_Id']}'");
                if(mysql_num_rows($sqlT263) > 0){
                    $html .= <<<EOT
                                <table style="width: 100%">
                                    <tr>
                                        <td class="fontepeq">Código</td>
                                        <td class="fontepeq">Descrição</td>
                                        <td style="text-align: right;" class="fontepeq">Quantidade</td>
                                        <td style="text-align: right;" class="fontepeq">Estoque</td>
                                    </tr>
EOT;
                    while($rowT263 = mysql_fetch_assoc($sqlT263)){
                        $rowT263['T263_Quantidade'] = gCorrigeNumero($rowT263['T263_Quantidade']);
                        $rowT263['D009_Quantidade_Estoque_Liquido'] = gCorrigeNumero($rowT263['D009_Quantidade_Estoque_Liquido']);
                        $html .= <<<EOT
                                    <tr>
                                        <td style="font-size: 10px">{$rowT263['D001_Codigo_Produto']}</td>
                                        <td style="font-size: 10px">{$rowT263['D001_Descricao_Produto']}</td>
                                        <td style="font-size: 10px; text-align: right;">{$rowT263['T263_Quantidade']}</td>
                                        <td style="font-size: 10px; text-align: right;">{$rowT263['D009_Quantidade_Estoque_Liquido']}</td>
                                    </tr>
EOT;
                    }
                    $html .= <<<EOT
                                </table>
EOT;
                }
                $html .= <<<EOT
                            </td>
                        </tr>
                        <tr bgcolor="white">
                            <td colspan="7" style="padding: 0px;">
                                <table style="width: 100.3%;; margin: -1px;" bgcolor=silver cellspacing=1 width=100% cellpadding=2>
                                    <tr bgcolor="white">
                                        <td style="text-align: center; width: 50px;" class="fontepeq">Data</td>
                                        <td style="text-align: center; width: 50px;" class="fontepeq">Hora início</td>
                                        <td style="text-align: center; width: 50px;" class="fontepeq">Hora fim</td>
                                        <td style="text-align: center; width: 200px;" class="fontepeq">Observação</td>
                                        <td style="text-align: center; width: 70px;" class="fontepeq">Visto</td>
                                    </tr>
                                    <tr bgcolor="white">
                                        <td class="fontemed" style="text-align: center; font-size:12px;">&nbsp;</td>
                                        <td class="fontemed" style="text-align: center; font-size:12px;">&nbsp;</td>
                                        <td class="fontemed" style="text-align: center; font-size:12px;">&nbsp;</td>
                                        <td class="fontemed" style="text-align: center; font-size:12px;">&nbsp;</td>
                                        <td class="fontemed" style="text-align: center; font-size:12px;">&nbsp;</td>
                                    </tr>
                                    <tr bgcolor="white">
                                        <td class="fontemed" style="text-align: center; font-size:12px;">&nbsp;</td>
                                        <td class="fontemed" style="text-align: center; font-size:12px;">&nbsp;</td>
                                        <td class="fontemed" style="text-align: center; font-size:12px;">&nbsp;</td>
                                        <td class="fontemed" style="text-align: center; font-size:12px;">&nbsp;</td>
                                        <td class="fontemed" style="text-align: center; font-size:12px;">&nbsp;</td>
                                    </tr>
                                    <tr bgcolor="white">
                                        <td class="fontemed" style="text-align: center; font-size:12px;">&nbsp;</td>
                                        <td class="fontemed" style="text-align: center; font-size:12px;">&nbsp;</td>
                                        <td class="fontemed" style="text-align: center; font-size:12px;">&nbsp;</td>
                                        <td class="fontemed" style="text-align: center; font-size:12px;">&nbsp;</td>
                                        <td class="fontemed" style="text-align: center; font-size:12px;">&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
EOT;
            }
            $html .= <<<EOT
                        
                    </table>
EOT;
        }
        $html .= <<<EOT
                </div>
                </body>
            </html>
EOT;
        echo $html;
    break;
}
