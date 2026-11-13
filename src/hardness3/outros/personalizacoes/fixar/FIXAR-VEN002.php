<?php
namespace hardness;
/**
* Alterar (sobre-escrever) Métodos de uma Classe
*/

class VEN002 extends VEN002_ {
    // defina os métodos para sobreescrever
    public function xmontarEmailPdf($T005_Id, $imprimirHtml = false, $moeda = '1')
    {
        global $g, $confUsuario;
        
        if (!$imprimirHtml) {
            // Arquivo
            $dir = $g['pathDados'] . 'tmp/';
            is_dir($dir) OR mkdir($dir, 0777, true);
            $filename             = "pedido-{$T005_Id}.pdf";
            $file                 = $dir . $filename;
            $fileFull             = $g['pathRaiz'] . '/' . $file;
            $retorno['Anexos']    = array(
                $file
            );
            $retorno['AnexosWeb'] = array(
                '/hardness3/' . $file
            );
            
            $r_link = '/ven/ven002/form_func-ajax/ven002ImprimirPedido/?ajax=true&gerarHtml=1&T005_Id=' . $T005_Id . '&moeda=' . $moeda; // .'&alcadaUpdate='.$alcadaUpdate;
            $r_link = str_replace(' ', '+', $r_link);
            shell_exec("xvfb-run --server-num " . mt_rand(0, 98) . " -s \"-screen 0 640x480x16\" wkhtmltopdf --quiet --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 \"{$g['urlRaiz']}{$r_link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"");
            
            $T005 = <<<EOT
                SELECT * FROM T005 
                LEFT JOIN D013 ON D013_Id=T005_D013_Id
                LEFT JOIN D024 ON D024_Id=T005_D024_Id
                WHERE T005_Id='{$T005_Id}';
EOT;
            $T005 = mysql_query($T005);
            $T005 = mysql_fetch_array($T005);
            
            // Array de retorno para criação do e-mail
            $retorno['Para']    = (!empty($T005['D013_E_Mail'])) ? $T005['D013_E_Mail'] : $T005['D024_E_Mail'];
            $retorno['Assunto'] = "Pedido Nº[{$T005_Id}]";
            
            $retorno['Corpo'] = "<img src='{$caminhoImagem}' style='margin-left:10px'><font size='3'><br /><br /><br /><br /></font><font style='font-size:16px'>Segue pedido de venda número <b>$T005_Id</b> em anexo.</font><font size='3'><br /><br /></font>";
            
            $r_link = '/ven/ven002/form_func-ajax/ven002ImprimirPedido/?ajax=true&gerarHtml=1&T005_Id=' . $T005_Id . '&moeda=' . $moeda; // .'&alcadaUpdate='.$alcadaUpdate;
            $r_link = str_replace(' ', '+', $r_link);
            shell_exec("xvfb-run --server-num " . mt_rand(0, 98) . " -s \"-screen 0 640x480x16\" wkhtmltopdf --quiet --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 \"{$g['urlRaiz']}{$r_link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"");
            
            
            return $retorno;
        }
        
        /**
        * SELECT's
        */
        $C004 = mysql_query("SELECT * FROM C004 WHERE C004_Id = '{$g['empresaAtual']}'");
        $resC004 = mysql_fetch_array($C004);
        $footer = $resC004['C004_Observacao_Rodape_Pedido'];
        
        $T005_Id = mysql_real_escape_string($T005_Id);
        $sql = "SELECT * FROM T005 
                   LEFT JOIN D013 ON D013_Id=T005_D013_Id 
                   LEFT JOIN D116 ON D116_Id=T005_D116_Id 
                   LEFT JOIN D133 ON D133_Id=D116_D133_Id 
                   LEFT JOIN D134 ON D134_Id=D116_D134_Id 
                   LEFT JOIN D024 ON D024_Id=T005_D024_Id 
                   LEFT JOIN D020 ON D020_Id=D024_D020_Id
                   LEFT JOIN D018 ON D018_Id=D024_D018_Id 
                   LEFT JOIN D022 on D022_Id=T005_D022_Id
                   WHERE T005_Id='{$T005_Id}'";
        $T005 = mysql_query($sql);
        $T005 = mysql_fetch_array($T005);
        
        $sql = <<<EOT
            SELECT * FROM T006 
            LEFT JOIN D009 ON D009_Id=T006_D009_Id 
            LEFT JOIN D049 ON D049_Id=D009_D049_Id
            LEFT JOIN D001 ON D001_Id=D049_D001_Id
            LEFT JOIN D003 ON D003_Id=D001_D003_Id
            LEFT JOIN T144 ON T144_D001_Id=D001_Id
            LEFT JOIN D037 ON D037_Id=T006_D037_Id
            LEFT JOIN D005 ON D005_Id=T006_D005_Id
            WHERE T006_T005_Id = '{$T005_Id}' 
            GROUP BY T006_Id
            ORDER BY T006_Item_Pedido_Compra,T006_Id
EOT;
        $T006_sql = mysql_query($sql);                  
        
        $sql = "SELECT D024_E_Mail FROM D024 WHERE D024_Id='{$T005['T005_D024_Id']}'";
        $D024 = mysql_query($sql);
        $D024 = mysql_fetch_array($D024);   
        
        $sql = "SELECT * FROM D013 WHERE D013_Id='{$T005['T005_D013_Id']}'";
        $D013 = mysql_query($sql);
        $D013 = mysql_fetch_array($D013);
        
        // Array de retorno para criação do e-mail
        $array = array();
        $array['Para'] = (!empty($T005['D013_E_Mail'])) ? $T005['D013_E_Mail'] : $D024['D024_E_Mail'];
        $array['Assunto'] = "Pedido Nº[{$T005_Id}]";

        // solução temporaria - esse problema só acontece nesse pedido (tem que trocar este PDF para o novo formato)
        $border = '';
        if ($T005_Id != '5768' && $T005_Id != '4887') {
            $border = "border: 1px solid #000000;";
        }
        
        $html = '';
        /**
        * PDF - STYLE
        */
        $html .= <<<EOT
            <html>
            </head>
            <style>
                body {
                    font-family: Helvetica;
                }
                .header .text {
                    font-size: 12px;
                }
                .header .text2 {
                    font-size: 16px;
                    font-weight: bold;
                }
                .titulo {
                    background-color: #E1ECF1;
                }
                .titulo_superior {
                    font-size: 16px;
                }
                .itens td {
                    {$border}
                }
                .textIncluso {
                    font-size: 12px;
                    color: red;
                    background-color: #E1ECF1;
                }
            </style>
            </head>
            <body>
EOT;
                
        /**
        * PDF - HEADER
        */
        if(!empty($g['c004']['C004_Logo_Marca'])){
            $pathinfo = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
            $caminhoImagem = "<img src=\"{$caminhoImagem}\">";
        }else{
            $caminhoImagem="";
        }
        if(!empty($T005['D024_Cnpj'])){
            $cpfCnpj = $T005['D024_Cnpj'];
        }else{
            $cpfCnpj = $T005['D024_Cpf'];
        }
        $tel="";
        if(!empty($D013['D013_Telefone_1'])){
            $tel = '('.$D013['D013_DDD_Telefone_1'].') '.$D013['D013_Telefone_1'];
        }else if(!empty($T005['D024_Telefone_1'])){
            $tel = '('.$T005['D024_DDD_Telefone_1'].') '.$T005['D024_Telefone_1'];
        }
        
        if($T005['D024_Nome_Empresa'] == $T005['D024_Nome_Fantasia']){
            $Nome_Empresa = <<<EOT
                <tr>
                    <td class="tdGeral">{$T005['D024_Nome_Empresa']}</td>
                </tr>
EOT;
        }else{
            $Nome_Empresa = <<<EOT
                <tr>
                    <td class="tdGeral">{$T005['D024_Nome_Empresa']}</td>
                </tr>
                <tr>
                    <td class="tdGeral">{$T005['D024_Nome_Fantasia']}</td>
                </tr>
EOT;
        }
        $T005['D024_Nome_Empresa']=substr($T005['D024_Nome_Empresa'],0,60);
        $dataEmissao = gCorrigeData($T005['T005_Data_Emissao']);
        // $html .= <<<EOT
            // <table class="header">
                // <tr>
                    // <td width="150" align="center">{$caminhoImagem}</td>
                    // <td width="40"></td>
                    // <td align="left" class="text" width="330">
                        // <b>{$T003['D024_Id']} - {$T003['D024_Nome_Empresa']}</b><br>
                        // CNPJ: $cpfCnpj<br>
                        // <b>Att: {$D013['D013_Nome_Contato']} - {$tel}</b><br>
                        // {$T003['D024_Endereco']} - {$T003['D024_Numero']} <br>
                        // {$T003['D024_Bairro']} - {$T003['D024_Cep']} - {$T003['D020_Nome_Cidade']} - {$T003['D018_UF']}
                    // </td>
                    // <td align="center" width="150" class="text2">
                        // <br>
                        // ORÇAMENTO<br>Nº {$T003['T003_Id']}<br>{$dataEmissao}
                    // </td>
                // </tr>
            // </table>
            // <br />
// EOT;
        $sqlC004 = "SELECT * FROM C004 left join D018 on D018_Id=C004_D018_Id left join D020 on D020_Id=C004_D020_Id WHERE C004_Id='{$g['empresaAtual']}'";
        $resultC004 = mysql_query($sqlC004);
        $linhaC004 = mysql_fetch_array($resultC004);
        
        $sqlC007 = "SELECT * FROM C007 WHERE C007_Id='{$T005['T005_C007_Id_Vendedor_Interno']}'";
        $resultC007 = mysql_query($sqlC007);
        $linhaC007 = mysql_fetch_array($resultC007);
        
        if(($T005['D024_Cnpj'] != "") || (!empty($T005['D024_Cnpj']))){
            $cpfCnpj = "<b>CNPJ:</b> ".$T005['D024_Cnpj'];
        }else{
            $cpfCnpj = "<b>CPF:</b> ".$T005['D024_Cpf'];
        }
        $T005_Data_Emissao = gCorrigeData($T005['T005_Data_Emissao']);

        $html .=<<<EOT
            <table style="width: 100%">
                <tr>
                    <td align="left">{$caminhoImagem}</td>
                    <td align="right" style="font-size: 34px;"><b>Pedido</b> Nº {$T005_Id}          </td>
                </tr>
            </table>
            <br />
            <table class="header" width="100%">
                <tr>
                    <td style="vertical-align:top; width: 33%">
                        <table align="left" style="font-size:16px;"  cellpadding="0">
                            <tr>
                                <td class="titulo_superior"><b><u>Fornecedor</u></b></td>
                            </tr>
                            <tr>
                                <td class="tdGeral">{$linhaC004['C004_Nome_Empresa']}</td>
                            </tr>
                            <tr>
                                <td class="tdGeral">{$linhaC004['C004_Logradouro']}, {$linhaC004['C004_Numero']} - {$linhaC004['C004_Bairro']}</td>
                            </tr>
                            <tr>
                                <td class="tdGeral"><b>CEP:</b> {$linhaC004['C004_Cep']} {$linhaC004['D020_Nome_Cidade']} - {$linhaC004['D018_UF']}</td>
                            </tr>
                            <tr>
                                <td class="tdGeral"><b>CNPJ:</b> {$linhaC004['C004_Cnpj']}</td>
                            </tr>
                            <tr>
                                <td class="tdGeral"><b>Incr. Estadual:</b> {$linhaC004['C004_Inscricao_Estadual']}</td>
                            </tr>
                            <tr>
                                <td class="tdGeral">{$linhaC004['C004_Site']}</td>
                            </tr>
                        </table>
                    </td>
                    <td style="vertical-align:top; width: 33%">
                        <table align="left" style="font-size:16px;"  cellpadding="0">
                            <tr>
                                <td class="titulo_superior"><b><u>Cliente</u></b></td>
                            </tr>
                            <tr>
                                <td class="tdGeral"><b>Cód.:</b> {$T005['D024_Id']}</td>
                            </tr>
                            {$Nome_Empresa}
                            <tr>
                                <td class="tdGeral">{$T005['D024_Endereco']}, {$T005['D024_Numero']} - {$T005['D024_Bairro']}</td>
                            </tr>
                            <tr>
                                <td class="tdGeral"><b>CEP:</b> {$T005['D024_Cep']} {$T005['D020_Nome_Cidade']} - {$T005['D018_UF']}</td>
                            </tr>
                            <tr>
                                <td class="tdGeral">{$cpfCnpj}</td>
                            </tr>
                            <tr>
                                <td class="tdGeral"><b>Incr. Estadual:</b> {$T005['D024_Inscricao_Estadual']}</td>
                            </tr>
                            <tr>
                                <td class="tdGeral"><b>Contato: </b>{$D013['D013_Nome_Contato']}</td>
                            </tr>
                            <tr>
                                <td class="tdGeral"><b>Tel. </b>{$tel}</td>
                            </tr>
                        </table>
                    </td>
                    <td style="vertical-align:top; width: 33%">
                        <table align="left" style="font-size:16px;" cellpadding="0">
                            <tr>
                                <td class="titulo_superior"><b><u>Vendedor</u></b></td>
                            </tr>
                            <tr>
                                <td class="tdGeral"><b>Nº Pedido:</b> {$T005_Id}</td>
                            </tr>
                            <tr>
                                <td class="tdGeral"><b>Data:</b> {$T005_Data_Emissao}</td>
                            </tr>
                            <tr>
                                <td class="tdGeral"><b>Vendedor:</b> {$linhaC007['C007_Primeiro_Nome']}</td>
                            </tr>
                            <tr>
                                <td class="tdGeral"><b>E-mail:</b> {$linhaC007['C007_Email']}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
EOT;
        
        /**
        * PDF - TABELA PRODUTOS
        */
        $html .= <<<EOT
            <div style="height:20px; width:100%;"></div>
                <table class="itens" cellspacing="0" cellpadding="2">
                    <tr class="titulo">
                        <td width="3%">It</td>
                        <td width="10%">Código</td>
                        <td align="center" width="10%">Cód. Cli</td>
                        <td width="29%">Descrição</td>
                        <td align="center" width="4%">Und</td>
                        <td align="center" width="8%">NCM</td>
                        <td align="right" width="7%">Quant</td>
EOT;
        //Verifica se existe algum produto com ST
        $sqlVerificaST = mysql_query("SELECT * FROM T006 WHERE T006_T005_Id = '{$T005_Id}' AND T006_Flag_ST='S' GROUP BY T006_Id");
        if(mysql_num_rows($sqlVerificaST) <= 0){
            $html .= <<<EOT
                        <td align="right" width="10%">Unitário R\$</td>
                        <td align="right" width="10%">Total R\$</td>
                        <td align="center" width="4%">IPI</td>
                        <td align="center" width="6%">R$ IPI</td>
                        <!--
                        <td align="right" width="13%">Unit <font style="color:red;"><b>sem</b></font> IPI R\$</td>
                        <td align="center" width="4%">IPI</td>
                        <td align="right" width="14%">Total <font style="color:red;"><b>com</b></font> IPI R\$</td>
                        -->
EOT;
        }else{
            $html .= <<<EOT
                        <td align="right" width="10%">Unitário R\$</td>
                        <td align="right" width="10%">Total R\$</td>
                        <td align="center" width="4%">% IPI</td>
                        <td align="center" width="6%">R$ IPI</td>
                        <!--
                        <td align="right" width="13%">Unit <font style="color:red;"><b>sem</b></font> IPI e ST R\$</td>
                        <td align="center" width="5%">IPI</td> 
                        <td align="right" width="14%">Total <font style="color:red;"><b>com</b></font> IPI e ST R\$</td>
                        -->
EOT;
        }
        $html .= <<<EOT
                    </tr>
EOT;
        $count = 1;
        $valorTotalProd = 0;
        while($T006 = mysql_fetch_array($T006_sql)){
            $codigo = $T006['T006_Codigo_Produto'];
            $codigoCliente = $T006['T006_Codigo_Substituto'];
            $qtd = gCorrigeNumero($T006['T006_Quantidade']);
            //$unitario = gCorrigeNumero($T006['T006_Valor_Preco_Unitario']);
//                  $unitario = gCorrigeNumero($T006['T006_Valor_Preco_Sem_Desconto_Unitario'] + round($T006['T006_Valor_IPI']/$T006['T006_Quantidade'],10)+round($T006['T006_Valor_ICMS_Substituicao_Retencao']/$T006['T006_Quantidade'],10));
//                  $total = $T006['T006_Valor_Total_Preco_Sem_Desconto']+$T006['T006_Valor_IPI']+$T006['T006_Valor_ICMS_Substituicao_Retencao'];
//                  $unitario = gCorrigeNumero($T006['T006_Valor_Preco_Sem_Desconto_Unitario']);
//                  $total = $T006['T006_Valor_Total_Preco_Sem_Desconto'];
            $unitario = gCorrigeNumero($T006['T006_Valor_Preco_Sem_Desconto_Unitario']);
            $total = $T006['T006_Valor_Preco_Sem_Desconto_Unitario']*$T006['T006_Quantidade'];
            $valorTotalProd += $total;
            $ipi = number_format($T006['T006_Aliquota_IPI'],0);
            $valorIPI = gCorrigeNumero($T006['T006_Valor_IPI']);
            $icm = number_format($T006['T006_Aliquota_ICMS'],0);
            $link = '';
            if ($T006["T144_Url"]) {
                $T006["T144_Url"] = strpos($T006["T144_Url"], 'http') !== false ? $T006["T144_Url"] : 'http://' . $T006["T144_Url"];
                $link = "<a href=\"{$T006["T144_Url"]}\">ver produto</a>";
            }
            //if (!empty($T004["T144_Url"]))
            //  $corpo.='<a href="'.$T004["T144_Url"].'" target=_blank style="text-decoration:none;font-weight:bold;color:#000">Ver Produto</a>';
            if (!empty($T006['D003_Imagem'])) {
                $T006['D003_Imagem'] = explode(".", $T006['D003_Imagem']);
                $dir = "/hardness3/{$g['pathDados']}produtos/linhas/{$T006['D003_Imagem'][0]}.{$T006['D003_Imagem'][1]}";
            } else {
                $dir = "/hardness3/static/img/indisponivel100x100.jpg";
            }

            $html .= <<<EOT
            <tr style="font-size: 14px">
EOT;
            $D001 = mysql_query("SELECT D001_D003_Id FROM D001 WHERE D001_Id='{$T006['D001_Id']}'");
            //$T006['T006_Descricao_Produto'] = substr($T006['T006_Descricao_Produto'],0,36);
            
            // Contando o número de caracteres da descrição para adicionar br's nos agregados
            $numCaracteres = strlen($T006['T006_Descricao_Produto']);
            if($numCaracteres > 42){
                $br = "<br />";
            }else if($numCaracteres > 84){
                $br = "<br /><br />";
            }else{
                $br = "";
            }
            
            //$T004['T004_Observacao'] = substr($T004['T004_Observacao'],0,36);
            $T006['T006_Observacao'] = (!empty($T006['T006_Observacao']) ? "<br /><b><i>Obs.: {$T006['T006_Observacao']}</i></b>" : "");                    
            $html .=<<<EOT
                <td align="center">
                    {$T006['T006_Item']}
                </td>
                <td align="center">
                    {$codigo}
                </td>
                <td align="center">
                    {$codigoCliente}
                </td>
                <td align="left">
                    <b>{$T006['T006_Descricao_Produto']}</b>
EOT;
                //Imprime descrição dos agregados dos produtos
                $sqlAgregados = "select * from T204 where T204_T006_Id='{$T006['T006_Id']}' order by T204_Id ASC";
                $resultAgregados = mysql_query($sqlAgregados);
                $totalAgregados = 0;
                while($T204 = mysql_fetch_array($resultAgregados)){
                $totalAgregados += $T204['T204_Valor_Unitario'];
                $html .= <<<EOT
                    <br ></EOT><i>{$T204['T204_Descricao']}</i>
EOT;
                }
                if ($T006['T006_Valor_Total_Frete']>0){
                $html .= <<<EOT
                    <br ></EOT><i>VALOR FRETE</i>
EOT;
                }
                $totalGeral = '';
                $stringTotal = '';
                if(mysql_num_rows($resultAgregados) > 0 or $T006['T006_Valor_Total_Frete']>0){
                    $totalGeral = "<br /><b>" . gCorrigeNumero($totalAgregados+$total+$T006['T006_Valor_Total_Frete']) . "</b>";
                    $stringTotal = "";  //"<br /><b><i>Total</i></b>";
                }
                
             $html .=<<<EOT
                    {$stringTotal}
                    {$T006['T006_Observacao']}
                </td>
                <td>
                    {$T006['D037_Unidade']}
                </td>
                <td>
                    {$T006['D005_Classificacao_Fiscal']}
                </td>
                <td align="right">
                    <b>{$qtd}</b>
                </td>
                <!--<td align="right">
                    <b>{$unitario}</b>
EOT;
            $sqlAgregados = "select * from T204 where T204_T006_Id='{$T006['T006_Id']}' order by T204_Id ASC";
            $resultAgregados = mysql_query($sqlAgregados);
            while($T204 = mysql_fetch_array($resultAgregados)){
            
                $T204['T204_Valor_Unitario'] = gCorrigeNumero($T204['T204_Valor_Unitario']);
                $html .= <<<EOT
                    <br ></EOT>{$T204['T204_Valor_Unitario']}
EOT;
            }
            if ($T006['T006_Valor_Total_Frete']>0){
                $T006['T006_Valor_Frete'] = gCorrigeNumero($T006['T006_Valor_Frete']);
                $html .= <<<EOT
                    <br ></EOT>{$T006['T006_Valor_Total_Frete']}
EOT;
            }
            $total = gCorrigeNumero($total);
                
            $html .= <<<EOT
                {$totalGeral}
                </td>
                -->
                <td align="right">
                    <b>{$unitario}</b>
                </td>
                <!--<td align="right">
                    {$total}
                </td>
                -->
                <td align="right">
                    <b>{$total}</b>
EOT;
            $html .= $br;
            $sqlAgregados = "select * from T204 where T204_T006_Id='{$T006['T006_Id']}' order by T204_Id ASC";
            $resultAgregados = mysql_query($sqlAgregados);
            while($T204 = mysql_fetch_array($resultAgregados)){
                
                $T204['T204_Valor_Unitario'] = gCorrigeNumero($T204['T204_Valor_Unitario']);
                $html .= <<<EOT
                    <br ></EOT>{$T204['T204_Valor_Unitario']}
EOT;
            }
            
            if ($T006['T006_Valor_Total_Frete']>0){
                $T006['T006_Valor_Total_Frete'] = gCorrigeNumero($T006['T006_Valor_Total_Frete']);
                $html .= <<<EOT
                    <br ></EOT>{$T006['T006_Valor_Total_Frete']}
EOT;
            }
            $html .= <<<EOT
                {$totalGeral}
                </td>
                <td align="center">
                    {$ipi}%
                </td>
                <td align="right">
                    {$valorIPI}
                </td>
            </tr>
EOT;
            $count = $count + 1;
        }
        $html .= '</table>';
        
        /**
        * PDF - FORMA DE PAGAMENTO
        */
        $sqlParcelas = <<<EOT
            SELECT T090_Id, T090_Numero_Parcela, T090_Prazos_Dias, T090_Data_Vencimento, T090_Valor_Parcela, T090_D027_Id, D027_Portador, D027_Flag_Tipo FROM T090 left join D027 on D027_Id=T090_D027_Id WHERE T090_T005_Id = '{$T005_Id}' LIMIT 0,100
EOT;
        $resParcelas= mysql_query($sqlParcelas);
        
        if(mysql_num_rows($resParcelas) > 0){
            $html .= <<<EOT
                <br>
                <br>
                    <span align="left">Seguem abaixo as parcelas deste orçamento:</span>
                <br>
                    <table class="itens" cellspacing="0" cellpadding="2">
                        <tr class="titulo">
                            <td style="width: 60px;">Parcela</td>
                            <td style="width: 60px;">Prazos</td>
                            <td style="width: 60px;" align="center">Vencimento</td>
                            <td style="width: 150px;" align="right">Valor Parcela</td>
                            <td style="width: 150px;" align="left">Portador</td>
                        </tr>
EOT;
            $valorTotal = 0;
            $It=1;
            while($T090 = mysql_fetch_array($resParcelas)){
                $Vencimento = gCorrigeData($T090['T090_Data_Vencimento']);
                $valorParcela = gCOrrigeNumero($T090['T090_Valor_Parcela']);
                $Vencimento = gCorrigeData($T090['T090_Data_Vencimento']);

                $html .= <<<EOT
                        <tr>
                            <td align="center">
                                {$T090['T090_Numero_Parcela']}
                            </td>
                            <td align="center">
                                {$T090['T090_Prazos_Dias']}
                            </td>
                            <td align="center">
                                {$Vencimento}
                            </td>
                            <td align="right">
                                {$valorParcela}
                            </td>
                            <td align="left">
                                <b>{$T090['D027_Portador']}</b>
                            </td>
                        </tr>
EOT;
                $It++;
            }
            $html .= '</table>';
        }
        
        
        /**
        * PDF - TABELA SERVIÇOS
        */
        $sql = <<<EOT
        SELECT * FROM T147 
        WHERE T147_T005_Id='{$T005_Id}'
EOT;
        $result = mysql_query($sql);
        
        $valorTotalOrcamento = $T005['T005_Valor_Total'];
        if (mysql_num_rows($result) > 0) {
            $html .= <<<EOT
                <br>
                <span align="left">Seguem abaixo os serviços do orçamento:</span>
                <br>
                <table class="itens" cellspacing="0" cellpadding="2">
                    <tr class="titulo">
                        <td align="center" width="10%">Código</td>
                        <td align="center" width="60%">Descrição</td>
                        <td align="right" width="10%">Qte</td>
                        <td align="right" width="20%">Preço</td>
                    </tr>
EOT;
            $valorTotal = 0;
            while($T147 = mysql_fetch_array($result)){
                $titulo = $T147['T147_Titulo'] . " - " . $T147['T147_Descricao'];
                $preco = gCorrigeNumero($T147['T147_Valor_Unitario']);
                $valorTotal += ($T147['T147_Quantidade'] * $T147['T147_Valor_Unitario']);
                $html .= <<<EOT
                    <tr>
                        <td align="center">
                            {$T147['T147_Id']}
                        </td>
                        <td align="left">
                            {$titulo}
                        </td>
                        <td align="right">
                            {$T147['T14_Quantidade']}
                        </td>
                        <td align="right">
                            {$preco}
                        </td>
                    </tr>
EOT;
                            
            }
            $html .= '</table>';
            $html .= '<span align="left">Valor total dos Serviços: </b>' . gCorrigeNumero($valorTotal) . '</b></span>';
            $valorTotalOrcamento += $valorTotal;
        }
        
        /**
        * PDF - TOTAIS
        */
        //$valorTotalProd = gCorrigeNumero($T005['T005_Valor_Total_Produtos']);
        $sqlAgregados=mysql_query("SELECT SUM(T204_Valor_Unitario) as Total_Agregados FROM T204 LEFT JOIN T006 ON T006_Id=T204_T006_Id LEFT JOIN T005 ON T005_Id=T006_T005_Id WHERE T005_Id='{$T005_Id}'"); 
        $resultAgregados=mysql_fetch_array($sqlAgregados);
        $valorTotal = gCorrigeNumero($T005['T005_Valor_Total']+$resultAgregados['Total_Agregados']);
        $resultAgregados['Total_Agregados'] = gCorrigeNumero($resultAgregados['Total_Agregados']);
        $T005['T005_Valor_Frete']=gCorrigeNumero($T005['T005_Valor_Frete']);
        $valorTotalProd = gCorrigeNumero($valorTotalProd);
        $ipi = gCorrigeNumero($T005['T005_Valor_Total_IPI']);
        $T005['T005_Valor_ST'] = gCorrigeNumero($T005['T005_Valor_ST']);
        $T005['T005_Data_Entrega'] = gCorrigeData($T005['T005_Data_Entrega']);
        $html .= <<<EOT
            <br><br />
            <table style="font-size: 14px; text-align: left;">
                <tr>
                    <td style="width: 125px;">Prazo de Entrega: </td>
                    <td style="width: 370px;"><b>{$T005['T005_Data_Entrega']}</b></td>
                    <td style="width: 145px;">Valor total Produtos: </td>
                    <td style="width: 280px;"><b>R$ {$valorTotalProd}</b></td>
                </tr>
EOT;
        switch($T005['T005_Flag_Frete']){
            case '0':
                $T005_Frete = "Por conta do emitente";
                break;
            case '1':
                $T005_Frete = "Por conta do destinatário";
                break;
            case '2':
                $T005_Frete = "Terceiros";
                break;
            case '9':
                $T005_Frete = "Sem frete";
                break;
            default:
                $T005_Frete = "Não informado";
                break;
        }

        $T005['T005_Valor_Total_IPI'] = gCorrigeNumero($T005['T005_Valor_Total_IPI']);
        $html .= <<<EOT
                <tr>
                    <td style="width: 125px;">Frete: </td>
                    <td style="width: 370px;"><b>{$T005_Frete}</b></td>
                    <td style="width: 145px;">Valor total Serviços: </td>
                    <td style="width: 280px;"><b>R$ {$resultAgregados['Total_Agregados']}</b></td>
                </tr>
                <tr>
                    <td style="width: 125px;">Transportadora: </td>
                    <td style="width: 370px;"><b>{$T005['D022_Nome_Empresa']}</b></td>
                    <td style="width: 145px;">Valor IPI: </td>
                    <td style="width: 280px;"><b>R$ {$T005['T005_Valor_Total_IPI']}</b></td>
                </tr>
                <tr>
                    <td style="width: 125px;"></td>
                    <td style="width: 370px;"></td>
                    <td style="width: 145px;">Valor frete: </td>
                    <td style="width: 280px;"><b>R$ {$T005['T005_Valor_Frete']}</b></td>
                </tr>
                <tr>
                    <td style="width: 125px;"></td>
                    <td style="width: 370px;"></td>
                    <td style="width: 145px;">Valor total: </td>
                    <td style="width: 280px;"><b>R$ {$valorTotal}</b></td>
                </tr>

                    
EOT;
        $html .= "</table>
            <br>";
        if (!empty($T005["T005_Observacao"])) {
            $html .= '<br><span align="left">Observação: <br><b>'.str_replace(chr(10),'<br>',$T005['T005_Observacao']).'</b></span>';
        }
        // Assinatura
        if (!empty($g['c007']['C007_Email_Assinatura']) && trim($g['c007']['C007_Email_Assinatura']) != '<br>') {
            $assinatura = gCorrigeAssinatura($g['c007']['C007_Email_Assinatura']);
            $html .= "<br /><br /><span align=\"left\" style=\"font-size: 16px;\">{$assinatura}</span>";
        }

        $html .= "</body></html>";        
        return $html;
    }
	
	/**
     * verificaCreditoLiberado
     *
     * @param String $T005_Id
     * @return bool
     */    
    public function verificaCreditoLiberado($T005_Id)
    {
		
        global $g, $pedirSegundaLiberacao;

        log("entrou em verificaCreditoLiberado");
        
        if ($g['C031']['usarControleCredito'] == 'S') {
            //Verifica se é pagamento a vista e se o portador está especificado na configuração global
            if(!empty($g['C031']['portadoresLiberadosAVista'])){
                $sqlT090 = mysql_query("SELECT T090_Prazos_Dias, T090_D027_Id FROM T090 WHERE T090_T005_Id = '{$T005_Id}'");
                if(mysql_num_rows($sqlT090) == 1){
                    $rowT090 = mysql_fetch_array($sqlT090);
                    $portadoresValidos = explode(",", str_replace(" ", "", trim($g['C031']['portadoresLiberadosAVista'],",")));
                    if(in_array($rowT090['T090_D027_Id'], $portadoresValidos) && $rowT090['T090_Prazos_Dias'] == '0'){
                        $msgLiberado = mysql_real_escape_string("<b>Motivo não caiu na liberação de crédito:</b> <br />- Liberado por pagamento a vista. Prazo pedido: ".$rowT090['T090_Prazos_Dias']." dias, portadores liberados a vista: ".$g['C031']['portadoresLiberadosAVista']);
                        log($msgLiberado);
                        mysql_query("UPDATE T005 SET T005_Motivo_Pedido_Liberacao='{$msgLiberado}' WHERE T005_Id='{$T005_Id}'");
                        mysql_query("insert into T178 (T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) values ('{$T005_Id}','{$g['usuarioAtual']}',CURRENT_TIMESTAMP(),'$msgLiberado','')");
                        return true;
                    }
                }
            }

            if(!empty($g['C031']['portadoresLiberados'])){
                $portadoresValidos = explode(",", str_replace(" ", "", trim($g['C031']['portadoresLiberados'],",")));
                $sqlT090 = mysql_query("SELECT T090_D027_Id FROM T090 WHERE T090_T005_Id = '{$T005_Id}' GROUP BY T090_D027_Id");
                $liberado = true;
                while($rowT090 = mysql_fetch_array($sqlT090)){
                    if(!in_array($rowT090['T090_D027_Id'], $portadoresValidos) && $liberado){
                        $liberado = false;
                    }
                }
                if($liberado){
                    $msgLiberado = mysql_real_escape_string("<b>Motivo não caiu na liberação de crédito:</b> <br />- Portador não necessita análise de crédito, portadores liberados: ".$g['C031']['portadoresLiberados']);
                    log($msgLiberado);
                    mysql_query("UPDATE T005 SET T005_Motivo_Pedido_Liberacao='{$msgLiberado}' WHERE T005_Id='{$T005_Id}'");
                    mysql_query("insert into T178 (T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) values ('{$T005_Id}','{$g['usuarioAtual']}',CURRENT_TIMESTAMP(),'$msgLiberado','')");
                    return true;
                }
            }

            $T005  = mysql_query("SELECT * FROM T005 LEFT JOIN D024 ON D024_Id=T005_D024_Id LEFT JOIN D006 ON D006_Id=T005_D006_Id WHERE T005_Id='{$T005_Id}'");
            $mT005 = mysql_fetch_array($T005);
           
            $duplicatasVencidas = 0;
            $sqlT002            = mysql_query("SELECT * FROM T002 LEFT JOIN T117 ON T117_Id = T002_T117_Id WHERE T002_D024_Id = '{$mT005['T005_D024_Id']}' AND T002_Data_Recebimento = '0000-00-00' AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado is null)");
            
            log('D024='.$mT005['T005_D024_Id']);
            
            while ($T002 = mysql_fetch_array($sqlT002)) {
                if ($T002['T117_Id'] > 0 && $T002['T117_Data_Vencimento'] < date("Y-m-d")) {
                    log("Entrou IF ---------- T117: ".$T002['T117_Id']." Data vencimento: ".$T002['T117_Data_Vencimento']." date: ".date("Y-m-d"));
                    $duplicatasVencidas++;
                } else if ($T002['T002_Data_Vencimento'] < date("Y-m-d")) {
                    log("Entrou ELSE ---------- Data vencimento: ".$T002['T002_Data_Vencimento']." date: ".date("Y-m-d"));
                    $duplicatasVencidas++;
                }
            }
            
            //Calcula saldo do cliente
            $sqlSaldo    = "SELECT ifnull(sum(if(T002_Data_Vencimento<curdate(),T002_Valor_Total,0)),0) as Vencido, 
                                ifnull(sum(if(T002_Data_Vencimento>=curdate(),T002_Valor_Total,0)),0) as Vencer
                            FROM T002
                            WHERE T002_D024_Id='{$mT005['D024_Id']}'
                            AND T002_Data_Recebimento='0000-00-00' 
                            AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado is null)";
            $resultSaldo = mysql_query($sqlSaldo);
            $linhaSaldo  = mysql_fetch_array($resultSaldo);
            
            //Verifica os pedidos em aberto
            $sqlPedidos     = "SELECT ifnull(sum(T005_Valor_Total),0) as Pedidos
                                    FROM T005
                               LEFT JOIN T007 ON T007_T005_Id=T005_Id
                                   WHERE T005_D024_Id='{$mT005['D024_Id']}'
                                     AND T007_Id is null
                                     AND T005_Flag_Status != '8' AND (T005_T005_Id_Agrupado IS NULL OR T005_T005_Id_Agrupado='' OR T005_T005_Id_Agrupado=0)";
            $retornoPedidos = mysql_query($sqlPedidos);
            $linhaPedidos   = mysql_fetch_array($retornoPedidos);
            
            $saldoAtual = $mT005['D024_Valor_Limite'] - ($linhaSaldo['Vencido'] + $linhaSaldo['Vencer'] + $linhaPedidos['Pedidos']);
        
            $msg = "";
            $msgLiberado = "<b>Motivo não caiu na análise de crédito:</b> <br />";
            if ($duplicatasVencidas > 0) {
                $msg .= "- Cliente possui duplicatas atrasadas.";
            } else {
                $msgLiberado .= "- Cliente não possui duplicatas atrasadas.";
            }

            if ($mT005['D024_Flag_Liberado'] == "N") {
                $msg .= ($msg != '') ? '<br>' : '';
                $msg .= "- Cliente possui o campo 'Crédito Liberado' definido como NÃO.";
            } else {
                $msgLiberado .= ($msgLiberado != '') ? '<br>' : '';
                $msgLiberado .= "- Cliente possui o campo 'Crédito Liberado' definido como SIM.";
            }
    
            if ($saldoAtual < 0) {
                $msg .= ($msg != '') ? '<br>' : '';
                $msg .= "- Cliente possui limite de crédito insuficiente. Valor pedido R$ ".number_format($mT005['T005_Valor_Total'],2,',','.')." Saldo R$ ".number_format($saldoAtual,2,',','.').".";
            } else {
                $msgLiberado .= ($msgLiberado != '') ? '<br>' : '';
                $msgLiberado .= "- Cliente possui limite de crédito suficiente. Valor pedido R$ ".number_format($mT005['T005_Valor_Total'],2,',','.')." Saldo R$ ".number_format($saldoAtual,2,',','.').".";
            }

            if($mT005['D024_Flag_Pre_Cadastro'] == 'S'){
                $msg .= ($msg != '') ? '<br>' : '';
                $msg .= "- O cliente está em pré cadastro.";
            } else {
                $msgLiberado .= ($msgLiberado != '') ? '<br>' : '';
                $msgLiberado .= "- O cliente não está em pré cadastro";   
            }
   
            if(!empty($g['C031']['portadoresNaoLiberado'])){
                $portadoresValidos = explode(",", str_replace(" ", "", trim($g['C031']['portadoresNaoLiberado'],",")));
                $sqlT090 = mysql_query("SELECT T090_D027_Id, D027_Portador FROM T090 LEFT JOIN D027 ON D027_ID=T090_D027_Id WHERE T090_T005_Id = '{$T005_Id}' GROUP BY T090_D027_Id");
                $liberado = true;
                while($rowT090 = mysql_fetch_array($sqlT090)){
                    if(in_array($rowT090['T090_D027_Id'], $portadoresValidos)){
                       $liberado = false;
                       break;
                    }
                }
                if(!$liberado){
                    $msg .= ($msg != '') ? '<br>' : '';
                    $msg .= mysql_real_escape_string("- Obrigatório analise crédito para {$rowT090['D027_Portador']}");
                }
            }

            if ($mT005['D006_Flag_Venda_Compra_Outros']=='V'){
                //Verifica número de dias desde a última venda
                $sqlUltVenda=mysql_query("SELECT datediff(curdate(),T007_Data_Emissao) as ultVendaDias,
                                                 T007_Id
                                            FROM T007
                                       LEFT JOIN D006 ON D006_Id = T007_D006_Id    
                                           WHERE T007_D024_Id='{$mT005['D024_Id']}'
                                             AND T007_Flag_Cancelada!='S'
                                             AND D006_Flag_Venda_Compra_Outros='V'
                                             AND D006_Flag_Devolucao!='S'
                                           order by T007_Id desc limit 1");
                log("SELECT datediff(curdate(),T007_Data_Emissao) as ultVendaDias,
                                                 T007_Id
                                            FROM T007
                                       LEFT JOIN D006 ON D006_Id = T007_D006_Id    
                                           WHERE T007_D024_Id='{$mT005['D024_Id']}'
                                             AND T007_Flag_Cancelada!='S'
                                             AND D006_Flag_Venda_Compra_Outros='V'
                                             AND D006_Flag_Devolucao!='S'
                                           order by T007_Id desc limit 1");
                $mUltVenda  = mysql_fetch_array($sqlUltVenda);
                if ($mUltVenda['T007_Id']<=0) {
                    $msg .= ($msg != '') ? '<br>' : '';
                    $msg .= mysql_real_escape_string("- Primeira venda para este cliente");
                } else if ($g['C031']['diasUltimaVendaPedirAnaliseCredito']>0 and $mUltVenda['ultVendaDias']>=$g['C031']['diasUltimaVendaPedirAnaliseCredito']) {
                    $msg .= ($msg != '') ? '<br>' : '';
                    $msg .= mysql_real_escape_string("- Última venda superior a {$g['C031']['diasUltimaVendaPedirAnaliseCredito']} dias. ({$mUltVenda['ultVendaDias']} dias)");
                }
            }

            if($msg != ''){
                $msg = mysql_real_escape_string($msg);
                //$msgCab = "<b>Enviou para análise de crédito.</b><br /><br /> Motivo(s):<br />";
                //$msg = $msgCab . $msg;

                log($msg);
                mysql_query("UPDATE T005 SET T005_Motivo_Pedido_Liberacao='{$msg}' WHERE T005_Id='{$T005_Id}'", false);
                return false;
            } else {
                $msgLiberado = mysql_real_escape_string($msgLiberado);
                log($msgLiberado);
                mysql_query("UPDATE T005 SET T005_Motivo_Pedido_Liberacao='{$msgLiberado}' WHERE T005_Id='{$T005_Id}'", false);
                mysql_query("insert into T178 (T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) values ('{$T005_Id}','{$g['usuarioAtual']}',CURRENT_TIMESTAMP(),'$msgLiberado','')");
                return true;
            }
        } else {
            $msgLiberado = mysql_real_escape_string("- A análise de crédito está desativada na configuração");
            log($msgLiberado);
            mysql_query("UPDATE T005 SET T005_Motivo_Pedido_Liberacao='{$msgLiberado}' WHERE T005_Id='{$T005_Id}'");
            mysql_query("insert into T178 (T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) values ('{$T005_Id}','{$g['usuarioAtual']}',CURRENT_TIMESTAMP(),'$msgLiberado','')");
            return true;
        }
    
	}
	
	/**
     * montarEmailPdf
     *
     * @param String $T005_Id
     * @param bool $imprimirHtml
     * @return String/array
     */ 
    public function montarEmailPdf($T005_Id, $imprimirHtml = false, $moeda = '1')
    {
        global $g, $confUsuario;

        $moeda = empty($moeda) ? 1 : $moeda;
        
        if (!$imprimirHtml) {
            // Arquivo
            $dir = $g['pathDados'] . 'tmp/';
            is_dir($dir) OR mkdir($dir, 0777, true);
            $filename             = "pedido-{$T005_Id}.pdf";
            $file                 = $dir . $filename;
            $fileFull             = $g['pathRaiz'] . '/' . $file;
            $retorno['Anexos']    = array(
                $file
            );
            $retorno['AnexosWeb'] = array(
                '/hardness3/' . $file
            );
            
            $r_link = '/ven/ven002/form_func-ajax/ven002ImprimirPedido/?ajax=true&gerarHtml=1&T005_Id=' . $T005_Id . '&moeda=' . $moeda; // .'&alcadaUpdate='.$alcadaUpdate;
            $r_link = str_replace(' ', '+', $r_link);
            shell_exec("xvfb-run --server-num " . mt_rand(0, 98) . " -s \"-screen 0 640x480x16\" wkhtmltopdf --quiet --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 \"{$g['urlRaiz']}{$r_link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"");
            
            $T005 = <<<EOT
                SELECT * FROM T005 
                LEFT JOIN D013 ON D013_Id=T005_D013_Id
                LEFT JOIN D024 ON D024_Id=T005_D024_Id
                WHERE T005_Id='{$T005_Id}';
EOT;
            $T005 = mysql_query($T005);
            $T005 = mysql_fetch_array($T005);
            
            // Array de retorno para criação do e-mail
            $retorno['Para']    = (!empty($T005['D013_E_Mail'])) ? $T005['D013_E_Mail'] : $T005['D024_E_Mail'];
            $retorno['Assunto'] = "Pedido Nº[{$T005_Id}]";
            
            $retorno['Corpo'] = "<img src='{$caminhoImagem}' style='margin-left:10px'><font size='3'><br /><br /><br /><br /></font><font style='font-size:16px'>Segue pedido de venda número <b>$T005_Id</b> em anexo.</font><font size='3'><br /><br /></font>";
            
            $r_link = '/ven/ven002/form_func-ajax/ven002ImprimirPedido/?ajax=true&gerarHtml=1&T005_Id=' . $T005_Id . '&moeda=' . $moeda; // .'&alcadaUpdate='.$alcadaUpdate;
            $r_link = str_replace(' ', '+', $r_link);
            shell_exec("xvfb-run --server-num " . mt_rand(0, 98) . " -s \"-screen 0 640x480x16\" wkhtmltopdf --quiet --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 \"{$g['urlRaiz']}{$r_link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"");
            
            
            return $retorno;
        }
        
        /**
         * SELECT's
         */
        $C004  = mysql_query("SELECT * FROM C004 LEFT JOIN D018 ON D018_Id=C004_D018_Id LEFT JOIN D020 ON D020_Id=C004_D020_Id WHERE C004_Id = '{$g['empresaAtual']}'");
        $mC004 = mysql_fetch_array($C004);
        
        $T005_Id = mysql_real_escape_string($T005_Id);
        $T005    = <<<EOT
        SELECT *,
        comprador.C007_Primeiro_Nome as compradorC007_Primeiro_Nome,
        comprador.C007_Email as compradorC007_Email,
        comprador.C007_DDD_Telefone as compradorC007_DDD_Telefone,
        comprador.C007_Telefone as compradorC007_Telefone,
        analista.C007_Primeiro_Nome as analistaC007_Primeiro_Nome,
        T005_Status_Pedido(T005_Flag_Status,1) as status,
        T005_Prazos(T005_Id) formaPagamento
        FROM T005 
        LEFT JOIN D013 ON D013_Id=T005_D013_Id 
        LEFT JOIN D116 ON D116_Id=T005_D116_Id 
        LEFT JOIN D133 ON D133_Id=D116_D133_Id 
        LEFT JOIN D134 ON D134_Id=D116_D134_Id 
        LEFT JOIN D024 ON D024_Id=T005_D024_Id 
        LEFT JOIN D020 ON D020_Id=D024_D020_Id
        LEFT JOIN D018 ON D018_Id=D024_D018_Id 
        LEFT JOIN D022 ON D022_Id=T005_D022_Id
        LEFT JOIN C007 AS comprador ON comprador.C007_Id=T005_C007_Id_Vendedor_Interno
        LEFT JOIN C007 AS analista ON analista.C007_Id=T005_Liberou_Alcada_C007_Id
        LEFT JOIN D044 ON D044_Id={$moeda}
        WHERE T005_Id='{$T005_Id}';
EOT;
        
        $T005  = mysql_query($T005);
        $mT005 = mysql_fetch_array($T005);
        
        $simboloMoeda = $mT005['D044_Simbolo'];
        $cotacaoMoeda = ($moeda != 1) ? $mT005['T005_Cotacao_Moeda'] : 1;
        
        $T006 = <<<EOT
        SELECT * FROM T006 
        LEFT JOIN D009 ON D009_Id=T006_D009_Id 
        LEFT JOIN D049 ON D049_Id=D009_D049_Id
        LEFT JOIN D082 ON D082_Id=D049_D082_Id
        LEFT JOIN D001 ON D001_Id=D049_D001_Id
        LEFT JOIN D003 ON D003_Id=D001_D003_Id
        LEFT JOIN T144 ON T144_D001_Id=D001_Id
        LEFT JOIN D037 ON D037_Id=T006_D037_Id
        LEFT JOIN D005 ON D005_Id=T006_D005_Id
        LEFT JOIN D006 ON D006_Id=T006_D006_Id
        WHERE T006_T005_Id = '{$T005_Id}' 
                  AND T006_Flag_Cancelado != 'S' 
        GROUP BY T006_Id
        ORDER BY T006_Item
EOT;
        $T006 = mysql_query($T006);
        
        
        $html .= <<<EOT
            <style>
                .moldura {
                  font-size: 11px;
                  background:black;
                  cellspacing:1px;
                  width:100%;
                  padding: 0px;
                }     
                .moldura td {
                  background:white;
                  padding:1px 1px 1px 1px;
                }     
                .tabela {
                  cellspacing:0px;
                  font-size:11px;
                }     
                .tdBorda {
                  border-right:1px solid black;
                  border-bottom:1px dotted black;
                  font-size:11px;
                }     
                .tdBordaR {
                  font-size:11px;
                  border-bottom:1px dotted black;
                }     
                .tdBordaL {
                  border-right:1px solid black;
                  border-bottom:1px dotted black;
                  font-size:11px;
                }     
                .tdTitulo {
                  border-bottom:1px solid black;
                  border-right:1px solid black;
                  font-size:11px;
                  background:lightblue;
                }     
                .tdTituloR {
                  border-bottom:1px solid black;
                  font-size:11px;
                  background:lightblue;
                }     
                .tdTituloL {
                  border-bottom:1px solid black;
                  border-right:1px solid black;
                  font-size:11px;
                  background:lightblue;
                }     
                table {
                    font-family: Arial;
                }
            </style>
EOT;
        
        /**
         * PDF - HEADER
         */
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
            $caminhoImagem = "<img src=\"{$caminhoImagem}\">";
        } else {
            $caminhoImagem = "";
        }
        
        $mT005['T005_Data_Emissao'] = gCorrigeData($mT005['T005_Data_Emissao']);
        $T005_Numero_Ordem_Compra   = str_pad($T005_Id, 6, "0", STR_PAD_LEFT);

        if (!empty($mT005['D013_Telefone_1'])) {
            $tel = $mT005['D013_DDD_Telefone_1'] . ' ' . $mT005['D013_Telefone_1'];
        } else if (!empty($mT005['D024_Telefone_1'])) {
            $tel = $mT005['D024_DDD_Telefone_1'] . ' ' . $mT005['D024_Telefone_1'];
        }
        $mT005['D024_Telefone_2'] = trim($mT005['D024_Telefone_2']);
        $tel .= !empty($mT005['D024_Telefone_2']) ? " / {$mT005['D024_DDD_Telefone_2']} {$mT005['D024_Telefone_2']}" : "";
        
        $html .= <<<EOT
    <table class="moldura" height="1040px">
        <tr>
        <td rowspan=2>
          {$caminhoImagem}
        </td>
        <td rowspan=2>
          <table class="tabela">
            <tr>
              <td>{$mC004['C004_Nome_Empresa']}</td>
            </tr>
            <tr>
              <td>{$mC004['C004_Logradouro']}, {$mC004['C004_Numero']} - {$mC004['C004_Bairro']}</td>
            </tr>
            <tr>
              <td><b>Telefone:</b> {$mC004['C004_DDD_Telefone']}-{$mC004['C004_Telefone']}  <b>Fax:</b> {$mC004['C004_DDD_Fax']}-{$mC004['C004_Fax']}</td>
            </tr>
            <tr>
              <td><b>CEP:</b> {$mC004['C004_Cep']} {$mC004['D020_Nome_Cidade']} - {$mC004['D018_UF']}</td>
            </tr>
            <tr>
              <td><b>CNPJ:</b> {$mC004['C004_Cnpj']}</td>
            </tr>
            <tr>
              <td><b>Inscr. Estadual:</b> {$mC004['C004_Inscricao_Estadual']}</td>
            </tr>
            <tr>
              <td><b>E-Mail:</b> {$mC004['C004_E_Mail']}</td>
            </tr>
            <tr>
              <td><b>Site:</b>{$mC004['C004_Site']}</td>
            </tr>
          </table>
        </td>
        <td style="font-size:18px" align="center">
          <b>Pedido<br>Nº {$T005_Numero_Ordem_Compra}<br>{$mT005['T005_Data_Emissao']}<b>
        </td>
      </tr>
      <tr>
        <td>
          <table class="tabela">
            <tr>
              <td><b><u>VENDEDOR:</u></b></td>
            </tr>
            <tr>
              <td>{$mT005['compradorC007_Primeiro_Nome']}</td>
            </tr>
            <tr>
              <td>{$mT005['compradorC007_Email']}</td>
            </tr>
            <tr>
              <td>{$mT005['compradorC007_DDD_Telefone']}-{$mT005['compradorC007_Telefone']}</td>
            </tr>
          </table>          
        </td>
      </tr>
        <tr>
          <td colspan="3" align="center" style="background:lightblue;font-size:12px;font-weight:bold">
            CLIENTE
          </td>
        </tr>
        <tr>
          <td colspan="3">
          <table class="tabela" style="width:100%">
            <tr>
              <td width="50%">
                <table class="tabela" width="100%">
                  <tr>
                    <td><b>{$mT005['D024_Id']} - {$mT005['D024_Nome_Empresa']}</b></td>
                  </tr>
                  <tr>
                    <td>{$mT005['D024_Endereco']}, {$mT005['D024_Numero']} - {$mT005['D024_Bairro']}</td>
                  </tr>
                  <tr>
                    <td><b>CEP:</b> {$mT005['D024_Cep']} {$mT005['D020_Nome_Cidade']} - {$mT005['D018_UF']}</td>
                  </tr>
                  <tr>
                    <td><b>CNPJ/CPF:</b> {$mT005['D024_Cnpj']}{$mT005['D024_Cpf']}</td>
                  </tr>
                </table>
              </td>
              <td width="50%">
                <table class="tabela" width="100%">
                  <tr>
                    <td><b>Incr. Estadual:</b> {$mT005['D024_Inscricao_Estadual']}</td>
                  </tr>
                  <tr>
                    <td><b>Contato: </b>{$mT005['D013_Nome_Contato']}</td>
                  </tr>
                  <tr>
                    <td><b>Telefone: </b>{$tel}</td>
                  </tr>
                  <tr>
                    <td><b>E-mail: </b>{$mT005['D013_E_Mail']}</td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
          </td>
      </tr>
EOT;
        
        /**
         * PDF - TABELA PRODUTOS
         */
        $html .= <<<EOT
        <tr>
        <td colspan="3" style="padding:0px 0px 0px 0px">
          <table cellspacing="0" cellpadding="4px" width="100%" height="100%" style="border:0px">
            <tr>
              <td align="center" style="background:lightblue" class="tdTituloL">
                  It
              </td>
              <td align="center" style="background:lightblue;width:60px" class="tdTitulo">
                Código
              </td>
              <td class="tdTitulo" style="background:lightblue">
                Descrição
              </td>
EOT;
    if($g['C031']['exibirMarcaImpressaoVenda'] == 'S'){
        $html .= <<<EOT
               <td align="center" class="tdTitulo" style="background:lightblue">
                   Marca
               </td>
EOT;
    }
        $html .= <<<EOT
              <td align="center" style="background:lightblue" class="tdTitulo">
                Und
              </td>
              <td align="center" style="background:lightblue" class="tdTitulo">
                NCM
              </td>
              <td align="center" style="background:lightblue" class="tdTitulo">
                CFOP
              </td>
              <td align="center" style="background:lightblue" class="tdTitulo">
                Quant
              </td>
              <td align="right" style="background:lightblue" class="tdTitulo">
                Unitário {$simboloMoeda}
              </td>
              <td align="right" style="background:lightblue" class="tdTitulo">
                Total {$simboloMoeda}
              </td>
              <td align="center" style="background:lightblue" class="tdTitulo">
                IPI %
              </td>
              <td align="center" style="background:lightblue" class="tdTitulo">
                ICM %
              </td>
              <td align="right" style="background:lightblue" class="tdTitulo">
                MVA
              </td>
              <td align="right" style="background:lightblue" class="tdTituloR">
                ST {$simboloMoeda}
              </td>
            </tr>
EOT;
        $count          = 1;
        $valorTotalProd = 0;
        while ($mT006 = mysql_fetch_array($T006)) {
            
            $mT006['T006_Quantidade']                        = gCorrigeNumero($mT006['T006_Quantidade']);
            $mT006['T006_Valor_Preco_Sem_Desconto_Unitario'] = number_format($mT006['T006_Valor_Preco_Sem_Desconto_Unitario']/$cotacaoMoeda, $mT005['T005_Casas_Decimais_Preco'], ',', '.');
            $mT006['T006_Valor_Total_Preco']                 = gCorrigeNumero($mT006['T006_Valor_Total_Preco']/$cotacaoMoeda);
            $mT006['T006_Aliquota_IPI']                      = number_format($mT006['T006_Aliquota_IPI'], 0);
            $mT006['T006_Valor_IPI']                         = gCorrigeNumero($mT006['T006_Valor_IPI']/$cotacaoMoeda);
            $mT006['T006_Aliquota_ICMS']                     = number_format($mT006['T006_Aliquota_ICMS'], 0);
            $mT006['T006_Valor_ICMS']                        = gCorrigeNumero($mT006['T006_Valor_ICMS']/$cotacaoMoeda);
            $mT006['T006_Valor_ICMS_Substituicao_Retencao']  = gCorrigeNumero($mT006['T006_Valor_ICMS_Substituicao_Retencao']/$cotacaoMoeda);
            $mT006['T006_ST_VA_Ajustada']                    = gCorrigeNumero($mT006['T006_ST_VA_Ajustada']);
            $observacaoItem                                  = '<font></font>';
            if (!empty($mT006['T006_Observacao'])) {
                $observacaoItem = '<font style="background:#FCF9AE"><b>Observação:</b> ' . $mT006['T006_Observacao'] . '</font><br>';
            }
            if (!empty($mT006['T006_Codigo_Substituto'])) {
                $mT006['T006_Codigo_Substituto'] = $mT006['T006_Codigo_Substituto'] . '<br />';
            }
            
            $html .= <<<EOT
      <tr>
EOT;
            $html .= <<<EOT
        <td align="center" class="tdBordaL">
          {$mT006['T006_Item']}
        </td>
        <td align="center" class="tdBorda">
           <b>{$mT006['T006_Codigo_Substituto']}</b>
          {$mT006['T006_Codigo_Produto']}
        </td>
        <td class="tdBorda">
          <b>{$mT006['T006_Descricao_Produto']}</b><br>
          {$observacaoItem}
          <font style='color:#0F079E'><i>Prazo entrega em dias: <b>{$mT006['T006_Observacao_Entrega']}</b></i></font>
        </td>
EOT;
    if($g['C031']['exibirMarcaImpressaoVenda'] == 'S'){
        $html .= <<<EOT
        <td align="center" class="tdBorda">
            {$mT006['D082_Marca']}
        </td>
EOT;
    }
        $html .= <<<EOT
        <td align="center" class="tdBorda">
          {$mT006['D037_Unidade']}
        </td>
        <td align="center" class="tdBorda">
          {$mT006['D005_Classificacao_Fiscal']}
        </td>
        <td align="center" class="tdBorda">
          {$mT006['D006_Codigo_CFOP']}
        </td>
        <td align="center" class="tdBorda">
          <b>{$mT006['T006_Quantidade']}</b>
        </td>
        <td align="right" class="tdBorda">
          <b>{$mT006['T006_Valor_Preco_Sem_Desconto_Unitario']}</b>
        </td>
        <td align="right" class="tdBorda">
          {$mT006['T006_Valor_Total_Preco']}
        </td>
        <td align="center" class="tdBorda">
          {$mT006['T006_Aliquota_IPI']}
        </td>
        <td align="center" class="tdBorda">
          {$mT006['T006_Aliquota_ICMS']}
        </td>
        <td align="right" class="tdBorda">
          {$mT006['T006_ST_VA_Ajustada']}
        </td>
        <td align="right" class="tdBordaR">
          {$mT006['T006_Valor_ICMS_Substituicao_Retencao']}
        </td>
      </tr>
EOT;
            $count = $count + 1;
        }
        $html .= <<<EOT
    </table>
        
          </td>
      </tr>

        <tr>
        <td colspan="3" style="padding:0px 0px 0px 0px">

EOT;
        
        /**
         * PDF - TOTAIS
         */
        $mT005['T005_Valor_Total_Produtos']             = gCorrigeNumero($mT005['T005_Valor_Total_Produtos']/$cotacaoMoeda);
        $mT005['T005_Valor_Frete']                      = gCorrigeNumero($mT005['T005_Valor_Frete']/$cotacaoMoeda);
        $mT005['T005_Valor_Total_IPI']                  = gCorrigeNumero($mT005['T005_Valor_Total_IPI']/$cotacaoMoeda);
        $mT005['T005_Valor_Total_ICMS']                 = gCorrigeNumero($mT005['T005_Valor_Total_ICMS']/$cotacaoMoeda);
        $mT005['T005_Valor_Total']                      = gCorrigeNumero($mT005['T005_Valor_Total']/$cotacaoMoeda);
        $mT005['T005_Data_Entrega']                     = gCorrigeData($mT005['T005_Data_Entrega']);
        $mT005['T005_Valor_ICMS_Substituicao_Retencao'] = gCorrigeNumero($mT005['T005_Valor_ICMS_Substituicao_Retencao']/$cotacaoMoeda);
        $mT005['T005_Valor_Total_Partilha_ICMS_Destino'] = gCorrigeNumero($mT005['T005_Valor_Total_Partilha_ICMS_Destino']/$cotacaoMoeda);

        $html .= <<<EOT
      <table class="tabela" cellspacing="0px" cellpadding="4px">
        <tr>
          <td align="left" width="105">Forma pagamento: </td><td align="left" width="330" style="border-right:1px solid black"><b>{$mT005['formaPagamento']}</b></td>
          <td align="left" width="105">Valor produtos: </td><td  align="left" width="100"><b>{$simboloMoeda} {$mT005['T005_Valor_Total_Produtos']}</b></td>
        </tr>
EOT;
        switch ($mT005['T005_Flag_Frete']) {
            case '0':
                $T005_Frete = "Por conta do emitente";
                break;
            case '1':
                $T005_Frete = "Por conta do destinatário";
                break;
            case '2':
                $T005_Frete = "Terceiros";
                break;
            case '9':
                $T005_Frete = "Sem frete";
                break;
            default:
                $T005_Frete = "Não informado";
                break;
        }
        
        $html .= <<<EOT
        <tr>
          <td align="left" width="105">Frete: </td><td align="left" style="border-right:1px solid black"><b>{$T005_Frete}</b></td>
          <td align="left" width="105">Valor IPI: </td><td  align="left"><b>{$simboloMoeda} {$mT005['T005_Valor_Total_IPI']}</b></td>
        </tr>
        <tr>
          <td align="left" width="105">Transportadora: </td><td align="left" style="border-right:1px solid black"><b>{$mT005['D022_Nome_Empresa']}</b></td>
          <td align="left" width="105">Valor frete: </td><td  align="left"><b>{$simboloMoeda} {$mT005['T005_Valor_Frete']}</b></td>
        </tr>
        <tr>
          <td align="left" width="105">Prazo de Entrega: </td><td align="left" width="330" style="border-right:1px solid black"><b>{$mT005['T005_Data_Entrega']}</b></td>
          <td align="left" width="105">Valor ST: </td><td align="left"><b>{$simboloMoeda} {$mT005['T005_Valor_ICMS_Substituicao_Retencao']}</b></td>
        </tr>
        <tr>
          <td align="left" width="105"></td><td align="left" width="330" style="border-right:1px solid black"></td>
          <td align="left" width="105">Valor total: </td><td align="left" style="font-size:18px"><b>{$simboloMoeda} {$mT005['T005_Valor_Total']}</b></td>
        </tr>
        <tr>
          <td align="left" width="105"></td><td align="left"  style="border-right:1px solid black"></td>
          <td align="left" width="105">Valor ICMS: </td><td align="left"><b>{$simboloMoeda} {$mT005['T005_Valor_Total_ICMS']}</b></td>
        </tr>
      
EOT;
        if($mT005['T005_Valor_Total_Partilha_ICMS_Destino'] > 0 ){
            $html .= <<<EOT
            <tr>
                <td align="left" width="105"></td><td align="left"  style="border-right:1px solid black"></td>
                <td align="left" width="105">Valor DIFAL: </td><td align="left"><b>{$simboloMoeda} {$mT005['T005_Valor_Total_Partilha_ICMS_Destino']}</b></td>
            </tr>
EOT;
        }
        
        $html .= "</table>";
        
        $html .= <<<EOT
          </td>
      </tr>

        <tr>
        <td colspan="3" style="background:lightblue;font-weight:bold" align="center">
          OBSERVAÇÕES 
          </td>
      </tr>
        <tr>
        <td colspan="3">
EOT;
        
        $html .= '<br>' . str_replace(chr(10), '<br>', '<b>' . $mT005['T005_Observacao']) . '</b>\n';
        
        $xPed     = htmlentities('<xPed>');
        $nItemPed = htmlentities('<nItemPed>');
        
        $html .= <<<EOT
        
          </td>
      </tr>
        <tr>
        <td colspan="3" height="100%">

          </td>
      </tr>

    </table>
    <font style="font-size:10px;">Hardness Sistemas - www.hardness.com.br</font>
    <!-- <br class="clear" /> -->
    <center>
    <span width: 100%">{$g['C004']['C004_Observacao_Rodape_Pedido']}</span>

EOT;
        
        return $html;
    
		
	}
	
	/**
     * atualizarAliquotasPedidos
     *
     * @param String $T005_Id
     * @param String $C004_Id
     * @param String $T006_Id
     * @return String/bool
     */
    public function atualizarAliquotasPedidos($T005_Id, $C004_Id = false, $T006_Id = 0)
    {
        global $g;
        $C004_Id = ($C004_Id == false) ? $g['empresaAtual'] : $C004_Id;

        require_once('bibliotecas/classes/FIS003.php');
        $FIS003 = new FIS003();
        require_once("bibliotecas/classes/VEN001.php");
        $VEN001 = new VEN001();
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();

        if (empty($T005_Id)) {
            return 'O variável T005_Id nao foi passada para o programa: atualizar_aliquotas.php3';
        }
        $cSql_T005 = mysql_query("SELECT * FROM T005 LEFT JOIN D006 ON D006_Id=T005_D006_Id LEFT JOIN D024 ON D024_Id=T005_D024_Id WHERE T005_Id='{$T005_Id}'");
        $mT005     = mysql_fetch_array($cSql_T005);

        if ($mT005['T005_Flag_Devolucao'] == 'S') {
            return "Este é um pedido para emissão de nota de devolução, não é possivel executar a atualização de aliquotas";
        }

        //=====================================================================
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //-- RODA A REGRA CFOP
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        if ($T006_Id > 0) {
            $cSql_T006 = mysql_query("SELECT *,
                                        T006_Id,
                                        D009_Id,
                                        D001_Codigo_Produto,
                                        D009_Id,
                                        D001_Peso_Unitario_Kg,
                                        T005_Flag_Lista_Preco,
                                        T005_D024_Id,
                                        T005_Flag_Acp,
                                        T006_D006_Id,
                                        ifnull(T006_T219_Id,0) as T006_T219_Id,
                                        ifnull(T006_T225_Id,0) as T006_T225_Id,
                                        IFNULL(D009_Origem_Mercadoria,'') as D009_Origem_Mercadoria
                                        FROM T005
                                        LEFT JOIN T006 ON T005_Id=T006_T005_Id
                                        LEFT JOIN D024 ON D024_Id=T006_D024_Id
                                        LEFT JOIN D009 ON D009_Id=T006_D009_Id
                                        LEFT JOIN D049 ON D049_Id=D009_D049_Id
                                        LEFT JOIN D001 ON D001_Id=D049_D001_Id
                                        WHERE T006_Id='{$T006_Id}'");
        } else {
            $cSql_T006 = mysql_query("SELECT *,
                                        T006_Id,
                                        D009_Id,
                                        D001_Codigo_Produto,
                                        D009_Id,
                                        D001_Peso_Unitario_Kg,
                                        T005_Flag_Lista_Preco,
                                        T005_D024_Id,
                                        T005_Flag_Acp,
                                        T006_D006_Id,
                                        ifnull(T006_T219_Id,0) as T006_T219_Id,
                                        ifnull(T006_T225_Id,0) as T006_T225_Id,
                                        IFNULL(D009_Origem_Mercadoria,'') as D009_Origem_Mercadoria
                                        FROM T005
                                        LEFT JOIN T006 ON T005_Id=T006_T005_Id
                                        LEFT JOIN D024 ON D024_Id=T006_D024_Id
                                        LEFT JOIN D009 ON D009_Id=T006_D009_Id
                                        LEFT JOIN D049 ON D049_Id=D009_D049_Id
                                        LEFT JOIN D001 ON D001_Id=D049_D001_Id
                                        WHERE T005_Id='{$T005_Id}'");
        }

        while ($mT006 = mysql_fetch_array($cSql_T006)) {
            if($mT006['D009_Origem_Mercadoria']){
                $mT006['D049_Origem_Mercadoria'] = $mT006['D009_Origem_Mercadoria'];
            }

            $Aliquotas        = $VEN001->retornaAliquotas($mT006['D009_Id'], false, $T005_Id, false, false, $mT006['T006_Id']);
            $demonstrativoNCM = $FIS003->demonstrativoNCM($Aliquotas['D005_Id']);
            $demonstrativoNCM = mysql_real_escape_string($demonstrativoNCM);

            //Método retornaPrecoVenda atualizado com apenas um demonstrativo
            $dadosPrecoVenda = $CAD002->retornaPrecoVenda($mT006['D009_Id'], $mT006['T005_Flag_Lista_Preco'], $mT006['T005_D024_Id'], false, $mT006['T006_D006_Id'], $Aliquotas['Aliquota_ICMS'], $mT006['T006_T219_Id'], $mT006['T006_T225_Id'], 4, $Aliquotas['Aliquota_PIS'], $Aliquotas['Aliquota_COFINS'], $Aliquotas['Aliquota_IPI'], $Aliquotas['ST_VA'], $Aliquotas['Percentual_Reducao_ICMS_ST'], $Aliquotas['ST_ICMS_Interno'], $Aliquotas['Percentual_Reducao_ICMS'], $Aliquotas['ICMS_Inter_Estadual'], $Aliquotas['Aliquota_IRPJ'], $Aliquotas['Aliquota_CSLL']);
            $origemProduto   = $dadosPrecoVenda[5];

            if ($g['C031']['atualizarAliquotaUnidadeProduto']=='S'){
                $updateD037 =  "T006_D037_Id = D001_D037_Id, ";
            }


            $cClausula = " UPDATE T006 ";
            $cClausula .= " LEFT JOIN D006 ON D006_Id=T006_D006_Id ";
            $cClausula .= " LEFT JOIN D059 ON D059_Id=D006_D059_Id ";
            $cClausula .= " LEFT JOIN T005 ON T005_Id=T006_T005_Id ";
            $cClausula .= " LEFT JOIN C004 ON C004_Id=T005_C004_Id ";
            $cClausula .= " LEFT JOIN D005 ON D005_Id=T006_D005_Id ";
            $cClausula .= " LEFT JOIN D149 ON D005_Id=D149_D005_Id AND D149_C004_Id=C004_Id ";
            $cClausula .= " LEFT JOIN D009 ON D009_Id=T006_D009_Id ";
            $cClausula .= " LEFT JOIN D049 ON D049_Id=D009_D049_Id ";
            $cClausula .= " LEFT JOIN D001 ON D001_Id=D049_D001_Id ";
            $cClausula .= " SET ";
            $cClausula .= " T006_Aliquota_IPI='{$Aliquotas['Aliquota_IPI']}',";
            $cClausula .= " T006_Percentual_Reducao_IPI='{$Aliquotas['Percentual_Reducao_IPI']}',";
            $cClausula .= " T006_Situacao_Tributaria_IPI='{$Aliquotas['Situacao_Tributaria_IPI']}', ";
            $cClausula .= " T006_Aliquota_ICMS='{$Aliquotas['Aliquota_ICMS']}',";
            $cClausula .= " T006_Percentual_Reducao_ICMS='{$Aliquotas['Percentual_Reducao_ICMS']}',";
            $cClausula .= " T006_Percentual_Reducao_ICMS_ST='{$Aliquotas['Percentual_Reducao_ICMS_ST']}',";
            $cClausula .= " T006_Flag_ST='{$Aliquotas['Flag_ST']}', ";
            $cClausula .= " T006_D005_Id='{$Aliquotas['D005_Id']}', ";
            $cClausula .= " T006_D059_Id='{$Aliquotas['D059_Id']}', ";
            $cClausula .= " T006_ST_VA='{$Aliquotas['ST_VA']}', ";
            $cClausula .= " T006_ST_ICMS_Interno='{$Aliquotas['ST_ICMS_Interno']}',";
            $cClausula .= " T006_ICMS_Inter_Estadual='{$Aliquotas['ICMS_Inter_Estadual']}',";
            $cClausula .= " T006_ICMS_Inter_Estadual_MVA='{$Aliquotas['ST_ICMS_Inter_Estadual_MVA']}',";
            $cClausula .= " T006_ST_ICMS_Interno_MVA='{$Aliquotas['ST_ICMS_Interno_MVA']}',";
            $cClausula .= "$updateD037";
            $cClausula .= " T005_Aliquota_ICMS='{$Aliquotas['Aliquota_ICMS']}',";
            $cClausula .= " T005_Percentual_Reducao_ICMS='{$Aliquotas['Percentual_Reducao_ICMS']}',";
            $cClausula .= " T006_Flag_Isento_PIS='{$Aliquotas['Flag_Isento_PIS']}',";
            $cClausula .= " T006_Aliquota_PIS='{$Aliquotas['Aliquota_PIS']}',";
            $cClausula .= " T006_Situacao_Tributaria_PIS='{$Aliquotas['Situacao_Tributaria_PIS']}',";
            $cClausula .= " T006_Flag_Isento_COFINS='{$Aliquotas['Flag_Isento_COFINS']}',";
            $cClausula .= " T006_Aliquota_COFINS='{$Aliquotas['Aliquota_COFINS']}',";
            $cClausula .= " T006_Situacao_Tributaria_COFINS='{$Aliquotas['Situacao_Tributaria_COFINS']}', ";
            $cClausula .= " T006_Classificacao_Fiscal='{$Aliquotas['Classificacao_Fiscal']}',";
            $cClausula .= " T006_Flag_Situacao_Tributaria='{$Aliquotas['Flag_Situacao_Tributaria']}', ";
            $cClausula .= " T006_Peso_Unitario='{$mT006['D001_Peso_Unitario_Kg']}', ";
            $cClausula .= " T006_Peso_Bruto='{$Aliquotas['D001_Peso_Unitario_Bruto']}', ";
            if(!is_numeric($origemProduto) || $origemProduto == '') {
                $cClausula .= " T006_Flag_Origem_Produto='{$mT006['D049_Origem_Mercadoria']}', ";
            } else {
                $cClausula .= " T006_Flag_Origem_Produto='{$origemProduto}', ";
            }
            //$cClausula.=" T006_Valor_Preco_Original=D009_Preco_1, ";
            $cClausula .= " T006_Percentual_Desconto=((T006_Valor_Preco_Original - T006_Valor_Preco_Sem_Desconto_Unitario) / T006_Valor_Preco_Original)*100, ";
            $cClausula .= " T006_D005_Id_Info='{$demonstrativoNCM}', ";
            $cClausula .= " T006_Aliquota_ICMS_Oculto='{$Aliquotas['Aliquota_ICMS_Oculto']}', ";
            $cClausula .= " T006_Percentual_Reducao_ICMS_Oculto='{$Aliquotas['Percentual_Reducao_ICMS_Oculto']}', ";
            $cClausula .= " T006_Motivo_Desoneracao_ICMS='{$Aliquotas['Motivo_Desoneracao_ICMS']}', ";
            $cClausula .= " T006_IPV_Sem_Lucro='{$Aliquotas['IPV_Sem_Lucro']}', ";
            $cClausula .= " T006_Aliquota_IRPJ='{$Aliquotas['Aliquota_IRPJ']}', ";
            $cClausula .= " T006_Flag_Isento_IRPJ=D006_Flag_Isento_IRPJ, ";
            $cClausula .= " T006_Aliquota_CSLL='{$Aliquotas['Aliquota_CSLL']}', ";
            $cClausula .= " T006_Flag_Isento_CSLL=D006_Flag_Isento_CSLL, ";
            $cClausula .= " T006_UF_ICMS_Substituicao_Devido='{$Aliquotas['UF_ICMS_Substituicao_Devido']}', ";
            $cClausula .= " T006_Percentual_Base_Operacao_Propria='{$Aliquotas['Percentual_Base_Operacao_Propria']}' ";
            $cClausula .= " WHERE T006_Id='{$mT006['T006_Id']}'";
            mysql_query($cClausula);
        }
        return true;
    
		
	}
}





