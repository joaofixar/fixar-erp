<?php
namespace hardness;
require_once('bibliotecas/classes/MYTCPDF.PHP');
require_once "old/lib/funcoes.php";

definirClasse('VEN002');
class VEN002_
{

    /**
     * excluir
     *
     * @param String $T003_Id
     * @return true
     */     
    public function excluir($T003_Id)
    {
        $T003_Id = mysql_real_escape_string($T003_Id);
        $sql     = "DELETE FROM T003 WHERE T003_Id='$T003_Id'";
        mysql_query($sql);
        return true;
    }

    public function acoesAposInserirPedido($T005_Id){
        return true;
    }
    
    /**
     * montarEmail
     *
     * @param String $T005_Id
     * @return array
     */
    public function montarEmail($T005_Id)
    {
        global $g, $confUsuario;
        $T005_Id = mysql_real_escape_string($T005_Id);
        $sql     = "SELECT * FROM T005 LEFT JOIN D013 ON D013_Id=T005_D013_Id WHERE T005_Id='{$T005_Id}'";
        $T005    = mysql_query($sql);
        $T005    = mysql_fetch_array($T005);
        
        $sql = <<<EOT
            SELECT * FROM T006 
            LEFT JOIN D009 ON D009_Id = T006_D009_Id 
            LEFT JOIN D049 ON D049_Id = D009_D049_Id
            LEFT JOIN D001 ON D001_Id = D049_D001_Id
            LEFT JOIN T144 ON T144_D001_Id = D001_Id
            LEFT JOIN D037 ON D037_Id=T006_D037_Id
            WHERE T006_T005_Id = '{$T005_Id}' 
            ORDER BY T006_Item_Pedido_Compra,T006_Id
EOT;
        
        $T006_sql = mysql_query($sql);
        
        $sql  = "SELECT D024_E_Mail FROM D024 WHERE D024_Id='{$T005['T005_D024_Id']}'";
        $D024 = mysql_query($sql);
        $D024 = mysql_fetch_array($D024);
        
        $array = array();
        
        $array['Para']    = (!empty($T005['D013_E_Mail'])) ? $T005['D013_E_Mail'] : $D024['D024_E_Mail'];
        $array['Assunto'] = "Pedido [{$T005_Id}]";
        
        $tabela = "font-family: 'Lucida Sans Unicode', 'Lucida Grande', Sans-Serif;";
        $tabela .= 'font-size: 12px;';
        $tabela .= 'background: #fff;';
        $tabela .= 'margin: 25px;';
        $tabela .= 'width: 800px;';
        $tabela .= 'border-collapse: collapse;';
        $tabela .= 'text-align: left;';
        $tabela .= 'border:0px;';
        
        $th = 'font-size: 13px;';
        $th . '=font-weight: normal;';
        $th .= 'color: #039;';
        $th .= 'padding: 4px 4px;';
        $th .= 'border-bottom: 2px solid #6678b1;';
        
        $td = 'border-bottom: 1px solid #ccc;';
        $td .= 'color: #000000;';
        $td .= 'padding: 4px 4px;';
        
        $corpo = '<div style="border:2px solid #E0EEEE; padding-top:10px;padding-bottom:10px;">';
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
        } else {
            $caminhoImagem = "";
        }
        $corpo .= '<img src=' . $caminhoImagem . ' style="margin-left:25px"><br /><br />';
        $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;font-weight:bold">PEDIDO Nº: ' . $T005['T005_Id'] . '<br /><br />Seguem abaixo preços e condições dos Produtos do Pedido</div>';
        $corpo .= '<table style="' . $tabela . '">';
        $corpo .= '<tbody><tr><th style="' . $th . '">Código</th><th style="' . $th . '">Descrição</th><th style="' . $th . '" align="right">Qte</th><th style="' . $th . '" align="right">Unidade</th><th style="' . $th . '" align="right">Preço</th><th style="' . $th . '" align="right">IPI</th><th style="' . $th . '" align="right">ICM</th><th style="' . $th . '" width="10" align="center">OC</th><th style="' . $th . '" width="10" align="center">Item</th><th style="' . $th . '">Link</th></tr>';
        while ($T006 = mysql_fetch_array($T006_sql)) {
            $corpo .= '<tr>';
            $corpo .= '<td style="' . $td . '">';
            if (!empty($T006['T006_Codigo_Substituto']))
                $corpo .= $T006['T006_Codigo_Substituto'];
            else
                $corpo .= $T006['T006_Codigo_Produto'];
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . '">';
            $corpo .= $T006['T006_Descricao_Produto'];
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:right;">';
            $corpo .= gCorrigeNumero($T006['T006_Quantidade']);
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:right;">';
            $corpo .= $T006['D037_Unidade'];
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:right;">';
            $corpo .= gCorrigeNumero($T006['T006_Valor_Preco_Unitario']);
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:right;">';
            $corpo .= gCorrigeNumero($T006['T006_Aliquota_IPI']);
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:right;">';
            $corpo .= gCorrigeNumero($T006['T006_Aliquota_ICMS']);
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . '" align="center">';
            $corpo .= $T006['T006_Numero_Pedido_Compra'];
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . '" align="center">';
            $corpo .= $T006['T006_Item_Pedido_Compra'];
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . '">';
            if (!empty($T006["T144_Url"]))
                $corpo .= '<a href="' . $T006["T144_Url"] . '" target=_blank style="text-decoration:none;font-weight:bold;color:#000">Ver Produto</a>';
            $corpo .= '</td>';
            $corpo .= '</tr>';
        }
        $corpo .= '</tbody>';
        $corpo .= '</table>';
        $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;"><b>Valor total Produtos: </b>&nbsp;R$' . gCorrigeNumero($T005['T005_Valor_Total']) . '</div>';
        
        $sql    = <<<EOT
        SELECT * FROM T147 
        WHERE T147_T005_Id='{$T005_Id}'
EOT;
        $result = mysql_query($sql);
        
        
        $corpo .= '<div style="border:2px solid #E0EEEE; padding-top:10px;padding-bottom:10px;">';
        $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;font-weight:bold"><br /><br />Seguem abaixo os serviços do pedido</div>';
        $corpo .= '<table style="' . $tabela . '">';
        $corpo .= '<tbody><tr><th style="' . $th . '">Código</th><th style="' . $th . '">Descrição</th><th style="' . $th . '" align="right">Qte</th><th style="' . $th . '" align="right">Preço</th>';
        while ($T147 = mysql_fetch_array($result)) {
            $corpo .= '<tr>';
            $corpo .= '<td style="' . $td . '">';
            $corpo .= $T147['T147_Id'];
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . '">';
            $corpo .= $T147['T147_Descricao'];
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:right;">';
            $corpo .= $T147['T147_Quantidade'];
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:right;">';
            $corpo .= gCorrigeNumero($T147['T147_Valor_Unitario']);
            $corpo .= '</td>';
            $corpo .= '</tr>';
            $valorTotal = ($T147['T147_Quantidade'] * $T147['T147_Valor_Unitario']);
        }
        $corpo .= '</tbody>';
        $corpo .= '</table>';
        $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;"><b>Valor total Serviços: </b>R$&nbsp;' . gCorrigeNumero(@$valorTotal) . '</div>';
        $corpo .= '</div>';
        $valorTotalPedido = (@$valorTotal + $T005['T005_Valor_Total']);
        $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;"><br /><br /><b>VALOR TOTAL DO PEDIDO: </b>&nbsp;R$' . gCorrigeNumero($valorTotalPedido) . '</div>';
        if (!empty($T005["T005_Observacao"]))
            $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;"><b>Observação: </b>&nbsp;' . $T005['T005_Observacao'] . '</div>';
        if (!empty($T005["T005_Prazo_Entrega"]))
            $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;"><br /><br /><b>Prazo de Entrega: </b>&nbsp;' . $T005['T005_Prazo_Entrega'] . '</div>';
        if (!empty($T005["T005_Frete"]))
            $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;"><b>Frete: </b>&nbsp;' . $T005['T005_Frete'] . '</div>';
        if (!empty($T005["T005_Forma_Pagamento"]))
            $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;"><b>Forma de Pagamento: </b>&nbsp;' . $T005['T005_Forma_Pagamento'] . '</div>';
        $corpo .= '</div>';
        
        
        $array['Corpo']                 = $corpo;
        $array['D024_Id']               = $T005['T005_D024_Id'];
        $array['T139_Valor_Total']      = $T005['T005_Valor_Total'];
        $sql                            = "SELECT COUNT(*) FROM T006 WHERE T006_T005_Id='{$T005_Id}'";
        $registros                      = mysql_query($sql);
        $registros                      = mysql_fetch_array($registros);
        $array['T139_Numero_Registros'] = $registros[0];
        
        return $array;
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
        global $g, $confUsuario, $sys;
        
        // Ajuste do tamanho da fonte para casos que a geração do PDF está ficando com fonte muito pequena
        $fonteMinima     = 13 + $g['C031']['vendasZoomFonteImprimirPDF'];
        $fonteMedia      = 18 + $g['C031']['vendasZoomFonteImprimirPDF'];
        $fonteGrande     = 20 + $g['C031']['vendasZoomFonteImprimirPDF'];
        $magensImpressao = $g['C031']['margemImprimirPDF'];


        require_once("bibliotecas/classes/VEN001.php");
        $VEN001 = new VEN001();

        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();        

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

            unlink($fileFull);
            
/*            $r_link = '/ven/ven002/form_func-ajax/ven002ImprimirPedido/?ajax=true&gerarHtml=1&T005_Id=' . $T005_Id . '&moeda=' . $moeda; // .'&alcadaUpdate='.$alcadaUpdate;
            $r_link = str_replace(' ', '+', $r_link);
            shell_exec("xvfb-run --server-num " . mt_rand(0, 98) . " -s \"-screen 0 640x480x16\" wkhtmltopdf --quiet --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 \"{$g['urlRaiz']}{$r_link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"");*/
            
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
            
            $magensImpressao = ($magensImpressao <= 0 || $magensImpressao == '') ? 1 : $magensImpressao;

            $r_link = '/ven/ven002/form_func-ajax/ven002ImprimirPedido/?ajax=true&gerarHtml=1&T005_Id=' . $T005_Id . '&moeda=' . $moeda; // .'&alcadaUpdate='.$alcadaUpdate;
            $r_link = str_replace(' ', '+', $r_link);
            //shell_exec("xvfb-run --server-num " . mt_rand(0, 98) . " -s \"-screen 0 640x480x16\" wkhtmltopdf --quiet --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 \"{$g['urlRaiz']}{$r_link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"");
            shell_exec("xvfb-run --server-num " . mt_rand(0, 98) . " -s \"-screen 0 640x480x16\" wkhtmltopdf --minimum-font-size {$fonteMinima} --quiet --margin-left {$magensImpressao} --margin-right {$magensImpressao} --margin-top {$magensImpressao} --margin-bottom {$magensImpressao} \"{$g['urlRaiz']}{$r_link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"");
            //log("comando:"."xvfb-run --server-num " . mt_rand(0, 98) . " -s \"-screen 0 640x480x16\" wkhtmltopdf --minimum-font-size 13 --quiet --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 \"{$g['urlRaiz']}{$r_link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"");
            
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
        T005_Prazos(T005_Id) formaPagamento,
        T005_Prazos_Portador(T005_Id) formaPagamentoPortador
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
        LEFT JOIN D021 ON D021_Id=T005_D021_Id
        LEFT JOIN D044 ON D044_Id={$moeda}
        LEFT JOIN T005A ON T005_Id=T005A_T005_Id
        WHERE T005_Id='{$T005_Id}';
EOT;
        
        $T005  = mysql_query($T005);
        $mT005 = mysql_fetch_array($T005);
        
        $representante = $VEN001->montarEmailPdfRepresentante($mT005['T005_D021_Id']);

        $simboloMoeda = $mT005['D044_Simbolo'];
        $cotacaoMoeda = ($moeda != 1) ? $mT005['T005_Cotacao_Moeda'] : 1;
        
        $T006 = <<<EOT
        SELECT *,
               unPadrao.D037_Unidade as unPadrao_D037_Unidade,
               unConversao.D037_Unidade as unConversao_D037_Unidade
        FROM T006 
        LEFT JOIN D009 ON D009_Id=T006_D009_Id 
        LEFT JOIN D049 ON D049_Id=D009_D049_Id
        LEFT JOIN D082 ON D082_Id=D049_D082_Id
        LEFT JOIN D001 ON D001_Id=D049_D001_Id
        LEFT JOIN D003 ON D003_Id=D001_D003_Id
        LEFT JOIN T144 ON T144_D001_Id=D001_Id
        LEFT JOIN D005 ON D005_Id=T006_D005_Id
        LEFT JOIN D006 ON D006_Id=T006_D006_Id
        LEFT JOIN T006A ON T006_Id=T006A_T006_Id
        LEFT JOIN D184 ON D184_Id=T006A_D184_Id
        LEFT JOIN D037 AS unPadrao ON unPadrao.D037_Id=T006_D037_Id
        LEFT JOIN D037 unConversao ON unConversao.D037_Id=D184_D037_Id
        WHERE T006_T005_Id = '{$T005_Id}' 
        AND T006_Flag_Cancelado != 'S' 
        GROUP BY T006_Id
        ORDER BY T006_Codigo_Local,T006_Item
EOT;
        $T006 = mysql_query($T006);
        
        $html .= <<<EOT
        <style>
            .moldura {
                font-size: 11px;
                cellspacing:0px;
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
                border-right:1px dotted #4F5F6E;
                border-bottom:1px dotted #4F5F6E;
                font-size:11px;
            }           
            .tdBordaR {
                font-size:11px;
                border-bottom:1px dotted #4F5F6E;
            }           
            .tdBordaL {
                border-right:1px dotted #4F5F6E;
                border-bottom:1px dotted #4F5F6E;
                font-size:11px;
            }           
            .tdTitulo {
                border-bottom:1px dotted white;
                border-right:1px dotted white;
                font-size:11px;
                background:#4F5F6E;
                color:white;           
            }           
            .tdTituloR {
                border-bottom:1px dotted white;
                font-size:11px;
                background:#4F5F6E;
                color:white;
            }           
            .tdTituloL {
                border-bottom:1px dotted white;
                border-right:1px dotted white;
                font-size:11px;
                background:#4F5F6E;
                color:white;
            }     
            tr, td, th, tbody, thead, tfoot {
                page-break-inside: avoid !important;
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
        <table class="moldura" style="border: 2px solid #4F5F6E;"  cellpadding="0" cellspacing="0">
        <tr>
        <td rowspan=2 style="text-align: center; vertical-align: middle; border-right:2px #4F5F6E solid" width='28%'>
            {$caminhoImagem}
        </td>
        <td rowspan=2 style='border-right:2px #4F5F6E solid' align=center> 
            <table class="tabela">
            <tr>
              <td>
                 <b>{$mC004['C004_Nome_Empresa']}</b>
              </td>
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
        <td style="font-size:{$fonteGrande}px; border-bottom:2px #4F5F6E solid" align="center" width='28%'>
            <b>PEDIDO<br>Nº {$T005_Numero_Ordem_Compra}<br>{$mT005['T005_Data_Emissao']}<b>
        </td>
      </tr>
      <tr>
        <td>
          <table class="tabela" align=center>
            <tr>
              <td><b><u>VENDEDOR:</u></b></td>
            </tr>
            <tr>
              <td>{$mT005['compradorC007_Primeiro_Nome']}, {$mT005['compradorC007_DDD_Telefone']}-{$mT005['compradorC007_Telefone']}</td>
            </tr>
            <tr>
              <td>{$mT005['compradorC007_Email']}</td>
            </tr>
            {$representante}
          </table>          
        </td>
      </tr>
        <tr>
        <td colspan="16" align="center" style="background:#4F5F6E;color:white;font-size:12px;font-weight:bold; border-bottom:2px #4F5F6E solid; border-top:2px #4F5F6E solid">
            CLIENTE
          </td>
        </tr>
        <tr>
          <td colspan="16">
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
                    <td>{$mT005['D024_Complemento']}</td>
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
        <tr >
        <td colspan="3" style="padding:0px 0px 0px 0px">
          <table cellspacing="0" cellpadding="4px" width="100%" height="100%" style="border:0px">
            <tr style='font-weight:bold'>
              <td align="center" style="background:#4F5F6E" class="tdTituloL">
                  It
              </td>
EOT;
        if($g['C031']['exibirFotoImpressaoVenda'] == 'S'){
            $html .= <<<EOT
              <td align="center" style="background:#4F5F6E;width:60px" class="tdTitulo">
                  Foto
              </td>
EOT;
        }
        $html .= <<<EOT
              <td align="center" style="background:#4F5F6E;width:60px" class="tdTitulo">
                Código
              </td>
              <td class="tdTitulo" style="background:#4F5F6E">
                Descrição
              </td>
EOT;
    if($g['C031']['exibirMarcaImpressaoVenda'] == 'S'){
        $html .= <<<EOT
               <td align="center" class="tdTitulo" style="background:#4F5F6E">
                   Marca
               </td>
EOT;
    }
        $html .= <<<EOT
              <td align="center" style="background:#4F5F6E" class="tdTitulo">
                UN
              </td>
              <td align="center" style="background:#4F5F6E" class="tdTitulo">
                NCM
              </td>
              <td align="center" style="background:#4F5F6E" class="tdTitulo">
                CFOP
              </td>
              <td align="center" style="background:#4F5F6E" class="tdTitulo">
                Qte
              </td>
              <td align="right" style="background:#4F5F6E" class="tdTitulo">
                Unit{$simboloMoeda}
              </td>
              <td align="right" style="background:#4F5F6E" class="tdTitulo">
                Total{$simboloMoeda}
              </td>
              <td align="center" style="background:#4F5F6E" class="tdTitulo">
                %IPI
              </td>
              <td align="center" style="background:#4F5F6E" class="tdTitulo">
                %ICM
              </td>
              <td align="right" style="background:#4F5F6E" class="tdTitulo">
                MVA
              </td>
              <td align="right" style="background:#4F5F6E" class="tdTituloR">
                ST{$simboloMoeda}
              </td>
            </tr>
EOT;
        $count          = 1;
        $valorTotalProd = 0;

        $totalAmbiente  = 0;
        $ambiente       = '';

        while ($mT006 = mysql_fetch_array($T006)) {

            $unidadeMedida = $mT006['unPadrao_D037_Unidade'];
            // Se tiver unidade de conversao, imprime os valores convertidos
            if($mT005['T005A_Flag_Conversao_Unidade'] == 'S' && $mT006['T006A_D184_Id'] > 0){
                $unidadeMedida                                   = $mT006['unConversao_D037_Unidade'];
                $mT006['T006_Quantidade']                        = $mT006['T006A_Quantidade_Comercial'];
                $mT006['T006_Valor_Preco_Sem_Desconto_Unitario'] = $mT006['T006A_Preco_Unitario_Comercial'];
            }

            if ($ambiente!=strtoupper(trim(trim($mT006['T006_Codigo_Local'])))){
                if (!empty($ambiente)){
                    $totalAmbiente = gCorrigeNumero($totalAmbiente);
                    $html .= "<tr><td align=center colspan=20 style='font-size:{$fonteMedia}px;font-weight:normal;background:#F4F4F4'>" . ucwords(strtolower("TOTAL ".$ambiente)) . " <b>R$ {$totalAmbiente}</b></td></tr>";
                }
                $ambiente = strtoupper(trim($mT006['T006_Codigo_Local']));
                $totalAmbiente  = 0;
                $html .= "<tr><td align=center colspan=20 style='font-size:{$fonteMedia}px;font-weight:bold;background:#343A4B;color:white'>{$ambiente}</td></tr>";
            }

            if (!empty($ambiente)){
                $totalAmbiente += $mT006['T006_Valor_Total_Preco'];
            }

            if (!empty($ambiente)){
                $totalAmbiente += $mT004['T004_Valor_Total_Preco'];
            }

            $mT006['T006_Quantidade']                        = gCorrigeNumero($mT006['T006_Quantidade']);
            if($g['C031']['precopadraoVendaProduto'] == 'bruto'){
                $mT006['T006_Valor_Preco_Sem_Desconto_Unitario'] = number_format($mT006['T006_Valor_Preco_Bruto']/$cotacaoMoeda, $mT005['T005_Casas_Decimais_Preco'], ',', '.');
                $mT006['T006_Valor_Total_Preco']                 = gCorrigeNumero($mT006['T006_Valor_Total_Bruto']/$cotacaoMoeda);
            } else {
                $mT006['T006_Valor_Preco_Sem_Desconto_Unitario'] = number_format($mT006['T006_Valor_Preco_Sem_Desconto_Unitario']/$cotacaoMoeda, $mT005['T005_Casas_Decimais_Preco'], ',', '.');
                $mT006['T006_Valor_Total_Preco']                 = gCorrigeNumero($mT006['T006_Valor_Total_Preco']/$cotacaoMoeda);
            }
            $mT006['T006_Aliquota_IPI']                      = gCorrigeNumero($mT006['T006_Aliquota_IPI']);
            $mT006['T006_Valor_IPI']                         = gCorrigeNumero($mT006['T006_Valor_IPI']/$cotacaoMoeda);
            $mT006['T006_Aliquota_ICMS']                     = gCorrigeNumero($mT006['T006_Aliquota_ICMS'], 2);
            $mT006['T006_Valor_ICMS']                        = gCorrigeNumero($mT006['T006_Valor_ICMS']/$cotacaoMoeda);
            $mT006['T006_Valor_ICMS_Substituicao_Retencao']  = gCorrigeNumero($mT006['T006_Valor_ICMS_Substituicao_Retencao']/$cotacaoMoeda);
            $mT006['T006_ST_VA']                             = gCorrigeNumero($mT006['T006_ST_VA']);
            $observacaoItem                                  = '<font></font>';
            if (!empty($mT006['T006_Observacao'])) {
                $observacaoItem = '<font style="background:#FCF9AE"><b>Observação:</b> ' . $mT006['T006_Observacao'] . '</font><br>';
            }
            if (!empty($mT006['T006_Codigo_Substituto'])) {
                $mT006['T006_Codigo_Substituto'] = $mT006['T006_Codigo_Substituto'] . '<br />';
            }

            $foto = $CAD002->cad002FotoProduto($mT006['D001_Id'],60);

            $html .= <<<EOT
      <tr>
EOT;
            $html .= <<<EOT
        <td align="center" class="tdBordaL">
          {$mT006['T006_Item']}
        </td>
EOT;
        if($g['C031']['exibirFotoImpressaoVenda'] == 'S'){
            $html .= <<<EOT
                <td align="center" class="tdBorda" height="50px">
                    $foto
                </td>
EOT;
        }
    $html .= <<<EOT
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
          {$unidadeMedida}
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
          {$mT006['T006_ST_VA']}
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


EOT;

if (!empty($ambiente)){
    $totalAmbiente = gCorrigeNumero($totalAmbiente);
    $html .= "<tr><td align=center colspan=20 style='font-size:{$fonteMedia}px;font-weight:normal;background:#F4F4F4'>" . ucwords(strtolower("TOTAL ".$ambiente)) . " <b>R$ {$totalAmbiente}</b></td></tr>";
}

$html .= <<<EOT
<tr>
    <td colspan="25" style="padding:0px 0px 0px 0px; border-top: 2px solid #4F5F6E;">

EOT;
        //Vai verificar se deve imprimir o valor do frete, mesma validação que é realiza para formar o preço venda e calcular o total do pedido
        $ratearFreteCusto          = ($mT005['T005A_Flag_Ratear_Frete_Custo'] != '') ? $mT005['T005A_Flag_Ratear_Frete_Custo'] : $g['C031']['ratearFreteVendaNoCusto'] == 'S';
        $mT005['T005_Valor_Frete'] = ($ratearFreteCusto == 'S' && $mT005['T005_Flag_Frete'] == '0') ? 0 : $mT005['T005_Valor_Frete'];

        /**
         * PDF - TOTAIS
         */
        $mT005['T005_Valor_Total_Produtos']              = gCorrigeNumero($mT005['T005_Valor_Total_Produtos']/$cotacaoMoeda);
        $mT005['T005_Valor_Frete']                       = gCorrigeNumero($mT005['T005_Valor_Frete']/$cotacaoMoeda);
        $mT005['T005_Valor_Total_IPI']                   = gCorrigeNumero($mT005['T005_Valor_Total_IPI']/$cotacaoMoeda);
        $mT005['T005_Valor_Total_ICMS']                  = gCorrigeNumero($mT005['T005_Valor_Total_ICMS']/$cotacaoMoeda);
        $mT005['T005_Valor_Total']                       = gCorrigeNumero($mT005['T005_Valor_Total']/$cotacaoMoeda);
        $mT005['T005_Data_Entrega']                      = gCorrigeData($mT005['T005_Data_Entrega']);
        $mT005['T005_Valor_ICMS_Substituicao_Retencao']  = gCorrigeNumero(($mT005['T005_Valor_ICMS_Substituicao_Retencao'] + $mT005['T005A_Valor_FCP_ST']) /$cotacaoMoeda);
        $mT005['T005_Valor_Desconto']                    = gCorrigeNumero($mT005['T005_Valor_Desconto']/$cotacaoMoeda);
        $mT005['T005_Valor_Desconto_Manual']             = gCorrigeNumero($mT005['T005_Valor_Desconto_Manual']/$cotacaoMoeda);
        $mT005['T005_Valor_Despesas_Acessorias']         = gCorrigeNumero($mT005['T005_Valor_Despesas_Acessorias']/$cotacaoMoeda);
        $mT005['T005_Valor_Total_Produtos_Sem_Desconto'] = gCorrigeNumero($mT005['T005_Valor_Total_Produtos_Sem_Desconto']/$cotacaoMoeda);
        $mT005['T005_Valor_Total_Partilha_ICMS_Destino'] = gCorrigeNumero($mT005['T005_Valor_Total_Partilha_ICMS_Destino']/$cotacaoMoeda);

        $desconto = <<<EOT
                <tr>
                    <td align="left">Valor produtos:</td>
                    <td align="left"><b>{$simboloMoeda} {$mT005['T005_Valor_Total_Produtos']}</b></td>
                </tr>
EOT;

        //impressão do desconto no orçamento
        if(($g['C031']['exibirDescontoImpressaoOrcPed'] == 'S')){
            if(($mT005['T005_Valor_Desconto'] < 0 && $g['C031']['mostrarDescontoProdutosAcrescimo'] == "S") || ($mT005['T005_Valor_Desconto'] > 0)){
                $desconto = <<<EOT
                    <tr>
                        <td align="left">Valor produtos: </td>
                        <td  align="left"><b>{$simboloMoeda} {$mT005['T005_Valor_Total_Produtos_Sem_Desconto']}</b></td>
                    </tr>
                    <tr>
                        <td align="left">Desconto produto: </td>
                        <td  align="left"><b>{$simboloMoeda} {$mT005['T005_Valor_Desconto']}</b></td>
                    </tr>
                    <tr>
                        <td align="left">Valor produtos líquido: </td>
                        <td  align="left"><b>{$simboloMoeda} {$mT005['T005_Valor_Total_Produtos']}</b></td>
                    </tr>
EOT;
            }
        }

        $tiposFrete = array('0' => 'Por conta do emitente', 
                            '1' => 'Por conta do destinatário',
                            '2' => 'Terceiros',
                            '3' => 'Próprio por conta do remetente',
                            '4' => 'Próprio por conta do destinatário',
                            '9' => 'Sem frete');
        $T005_Frete = $tiposFrete["{$mT005['T005_Flag_Frete']}"];

        if(empty($T005_Frete)){
            $T005_Frete = "Não informado";
        }

        $entrega = '';
        if(!empty($mT005['T005_Data_Entrega'])){
            $entrega = $mT005['T005_Data_Entrega'];
        }

        if(!empty($mT005['T005_Prazo_Entrega'])){
            if(!empty($entrega)){
                $entrega .= ' - '.$mT005['T005_Prazo_Entrega'];
            }else{
                $entrega = $mT005['T005_Prazo_Entrega'];
            }            
        }
        
    $html .= <<<EOT
            <table class="tabela" cellpadding="0" cellspacing="0" width="100%" border="0" valign="top">
                <td valign="top" width="50%" valign="top">
                    <table class="tabela"width="100%" border="0" valign="top">
                        <tr>
                            <td align="left" width="130px">Entrega: </td>
                            <td align="left"><b>{$entrega}</b></td>
                        </tr>
                        <tr>
                            <td align="left" width="130px">Prazo pagamento: </td>
                            <td align="left"><b>{$mT005['formaPagamento']}</b></td>
                        </tr>
                        <tr>
                            <td align="left" width="130px">Forma pagamento: </td>
                            <td align="left"><b>{$mT005['formaPagamentoPortador']}</b></td>
                        </tr>
                        <tr>
                            <td align="left">Frete: </td>
                            <td align="left"><b>{$T005_Frete}</b></td>
                        </tr>
                        <tr>
                            <td align="left">Transportadora: </td>
                            <td align="left"><b>{$mT005['D022_Nome_Empresa']}</b></td>
                        </tr>
                        <tr>
                            <td align="left">Ordem compra: </td>
                            <td align="left"><b>{$mT005['T005_Ordem_Compra']}</b></td>
                        </tr>
                        <tr>
                            <td align="left">Peso líquido: </td>
                            <td align="left"><b>{$mT005['T005_Peso_Total']} Kg</b></td>
                        </tr>
                        <tr>
                            <td align="left">Peso bruto: </td>
                            <td align="left"><b>{$mT005['T005_Peso_Bruto_Total']} Kg</b></td>
                        </tr>
                        <tr>
                            <td align="left">Cubagem: </td>
                            <td align="left"><b>{$mT005['T005_Total_Cubagem']}m³</b></td>
                        </tr>
                        <tr>
                            <td align="left">Total itens: </td>
                            <td align="left"><b>{$totalItens}</b></td>
                        </tr>
                    </table>
                </td>
                <td style="border-left: 1px solid #4F5F6E;" width="50%" valign="top">
                    <table class="tabela" width="100%" border="0" valign=top>
                        {$desconto}
                        <tr>
                            <td align="left">Valor IPI: </td>
                            <td  align="left"><b>{$simboloMoeda} {$mT005['T005_Valor_Total_IPI']}</b></td>
                        </tr>
                        <tr>
EOT;

                if($g['C031']['exibirDescontoImpressaoOrcPed'] == 'S'){
                    $html .= <<<EOT
                            <td align="left">Valor desconto: </td>
                            <td  align="left"><b>{$simboloMoeda} {$mT005['T005_Valor_Desconto_Manual']}</b></td>
                        </tr>
EOT;
                }

                $html .= <<<EOT
                        <tr>
                            <td align="left">Valor frete: </td>
                            <td  align="left"><b>{$simboloMoeda} {$mT005['T005_Valor_Frete']}</b></td>
                        </tr>
                        <tr>
                            <td align="left" width="150px">Despesas acessórias: </td>
                            <td align="left"><b>{$simboloMoeda} {$mT005['T005_Valor_Despesas_Acessorias']}</b></td>
                        </tr>
                        <tr>
                            <td align="left">Valor ST: </td>
                            <td align="left"><b>{$simboloMoeda} {$mT005['T005_Valor_ICMS_Substituicao_Retencao']}</b></td>
                        </tr>
                        <tr>
                            <td align="left">Valor total: </td>
                            <td align="left"  style="font-size:18px"><b>{$simboloMoeda} {$mT005['T005_Valor_Total']}</b></td>
                        </tr>
                        <tr>
                            <td align="left">Valor ICMS: </td>
                            <td align="left"><b>{$simboloMoeda} {$mT005['T005_Valor_Total_ICMS']}</b></td>
                        </tr>
                        
EOT;

    if($mT005['T005_Valor_Total_Partilha_ICMS_Destino'] > 0){
        $html .= <<<EOT
                        <tr>
                            <td align="left">Valor DIFAL: </td>
                            <td align="left"><b>{$simboloMoeda} {$mT005['T005_Valor_Total_Partilha_ICMS_Destino']}</b></td>
                        </tr>
EOT;
    }

    $html .= <<<EOT
                    </table>
                </td>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="16" style="background:#4F5F6E;color:white;font-weight:bold; border-top:2px solid #4F5F6E; border-bottom:2px solid #4F5F6E;" align="center">
            OBSERVAÇÃO  
        </td>
    </tr>
    <tr>
        <td colspan="3">
EOT;
        
        $html .= '<br>' . str_replace(chr(10), '<br>', '<b>' . $mT005['T005_Observacao']) . '</b>';
        
        $xPed     = htmlentities('<xPed>');
        $nItemPed = htmlentities('<nItemPed>');

        $nomeSiteEmpresa = (!empty($sys['nomeEmpresa']) && !empty($sys['siteEmpresa'])) ? strToUpper($sys['nomeEmpresa']) .' - '. strToUpper($sys['siteEmpresa']) : 'HARDNESS SISTEMAS - WWW.HARDNESS.COM.BR';

        $html .= <<<EOT
        
          </td>
      </tr>

    </table>
    <font style="font-size:10px;">{$nomeSiteEmpresa}</font>
    <!-- <br class="clear" /> -->
    <center>
    <span width: 100%">{$g['C004']['C004_Observacao_Rodape_Pedido']}</span>

EOT;
        
        return $html;
    }
    
    /**
     * excluirProduto
     *
     * @param String $T006_Id
     * @param String $T005_Id
     * @return String/bool
     */     
    public function excluirProduto($T006_Id, $T005_Id)
    {
        global $g;

        $C007 = mysql_query("SELECT C007_Flag_Vendedor FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}'");
        $C007 = mysql_fetch_array($C007);

        $T005  = mysql_query("SELECT T005_Id, T005_Flag_Status FROM T005 WHERE T005_Id = '{$T005_Id}'");
        $mT005 = mysql_fetch_array($T005);

        if ($mT005['T005_Flag_Status']>0 AND $mT005['T005_Flag_Status']!=14 AND $mT005['T005_Flag_Status']!=15 AND $mT005['T005_Flag_Status']!=9 && $C007['C007_Flag_Vendedor'] != 'D' && $C007['C007_Flag_Vendedor'] != 'S') {
            return 'Este pedido já foi faturado.';
        }
        
        // Verifica se existem produtos separados no estoque
        $T006  = mysql_query("SELECT T006_Id, 
                                     T006_D009_Id, 
                                     T006_Quantidade_Separacao,
                                     D006_Flag_Destaca_Desconto_NF 
                                FROM T006 
                           LEFT JOIN D006 ON D006_Id = T006_D006_Id
                               WHERE T006_Id = '{$T006_Id}'");

        $mT006 = mysql_fetch_array($T006);
        if ($mT006['T006_Quantidade_Separacao'] > 0) {
            return "Não pode ser excluido porque o produto já está separado no estoque.<br/>Por favor, verifique com o setor de expedição.";
        }
        
        $atualizarDescontoAposExclusao = 'N';
        if($mT006['D006_Flag_Destaca_Desconto_NF'] == 'S'){
            $atualizarDescontoAposExclusao = 'S';
        }
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        $T147 = mysql_query("SELECT T147_Id FROM T147 WHERE T147_T006_Id='{$T006_Id}'");
        if (mysql_num_rows($T147) > 0) {
            while ($mT147 = mysql_fetch_array($T147)) {
                $this->excluirServico($T147['T147_Id']);
            }
        }

        mysql_query("DELETE FROM T006A WHERE T006A_T006_Id='{$T006_Id}'");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return 'falha na exclusão do produto...';
        }

        mysql_query("DELETE FROM T006 WHERE T006_Id='{$T006_Id}'");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return 'falha na exclusão do produto...';
        }
        
        // Processa estoque por locação
        /*
        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();
        $CAD002->processaEstoqueLiquidoPorLocacao($mT006['T006_D009_Id']);
        */
        
		if($atualizarDescontoAposExclusao == 'S'){
            require_once('bibliotecas/classes/VEN001.php');
            $VEN001 = new VEN001();
            // Se a CFOP aplica desconto no total precisa recalcular o desconto no total
            // Esse desconto é feito pela soma dos produtos
            $VEN001->aplicaDescontoCFOP('T006', 'T005', 0, $T005_Id, 'S');	
		}

        mysql_query("call T005_Gravar_Totalizacao_4({$T005_Id});");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return 'Erro no T005_Gravar_Totalização' . mysql_error();
        }
        
        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");
        return true;
        
    }

    /**
     * cancelarProduto
     *
     * @param String $T006_Id
     * @param String $T005_Id
     * @return String/bool
     */     
    public function cancelarProduto($T006_Id, $T005_Id)
    {
        global $g;
        $C007 = mysql_query("SELECT * FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}'");
        $C007 = mysql_fetch_array($C007);
        $sql  = "SELECT T005_Id FROM T005 WHERE T005_Id = '{$T005_Id}' AND (T005_Flag_Status>0 AND T005_Flag_Status!='' AND T005_Flag_Status IS NOT NULL AND T005_Flag_Status!=14 AND T005_Flag_Status!=15)";
        $res  = mysql_query($sql);
        $rows = mysql_num_rows($res);
        if ($rows > 0 && $C007['C007_Flag_Vendedor'] != 'D' && $C007['C007_Flag_Vendedor'] != 'S') {
            return 'Este pedido já foi faturado.';
        }
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        $result = mysql_query("SELECT * FROM T147 WHERE T147_T006_Id='{$T006_Id}'");
        if (mysql_num_rows($result) > 0) {
            while ($T147 = mysql_fetch_array($result)) {
                $this->excluirServico($T147['T147_Id']);
            }
        }
        //echo "DELETE FROM T006 WHERE T006_Id='{$T006_Id}'";
        mysql_query("UPDATE T006 SET T006_Flag_Cancelado='S' WHERE T006_Id='{$T006_Id}'");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return 'falha na cancelamento do produto...';
        }
        
        mysql_query("call T005_Gravar_Totalizacao_4({$T005_Id});");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return 'Erro no T005_Gravar_Totalização' . mysql_error();
        }
        
        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");
        return true;
    }
    
    /**
     * excluirServico
     *
     * @param String $T147_Id
     * @return String/bool
     */    
    public function excluirServico($T147_Id)
    {
        global $g;
        
        $sql  = "SELECT T147_Id FROM T147 LEFT JOIN T005 ON T005_Id=T147_T005_Id WHERE T147_Id = '{$T147_Id}' AND (T005_Flag_Status>0 AND T005_Flag_Status!='' AND T005_Flag_Status IS NOT NULL)";
        $res  = mysql_query($sql);
        $rows = mysql_numrows($res);
        if ($rows > 0) {
            return 'Este pedido já foi faturado.';
        }
        // exclui primeiro os materiais e itens do serviço
        mysql_query("DELETE FROM T148 WHERE T148_T147_Id='$T147_Id'");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return 'falha na exclusão dos itens do serviço...';
        }
        mysql_query("DELETE FROM T149 WHERE T149_T147_Id='$T147_Id'");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return 'falha na exclusão dos materiais do serviço...';
        }
        
        // verificar exclusao da tabela de entrega de veiculos
        $sql    = "SELECT * FROM T147 WHERE T147_Id='{$T147_Id}'";
        $result = mysql_query($sql);
        $linha8 = mysql_fetch_array($result);
        
        $sql     = "SELECT * FROM T147 WHERE T147_D116_Id='{$linha8['T147_D116_Id']}' AND T147_T005_Id='{$linha8['T147_T005_Id']}'";
        $result1 = mysql_query($sql);
        if (mysql_num_rows($result1) == 1) {
            $sql     = "SELECT * FROM T171 WHERE T171_T005_Id='{$linha8['T147_T005_Id']}' AND T171_D116_Id='{$linha8['T147_D116_Id']}'";
            $result3 = mysql_query($sql);
            $linha2  = mysql_fetch_array($result3);
            $sql     = "DELETE FROM T171 WHERE T171_Id='{$linha2['T171_Id']}'";
            mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return "Erro 1.1 " . mysql_error();
            } else {
                $sql = "DELETE FROM T147 WHERE T147_Id='{$T147_Id}'";
                mysql_query($sql);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return "Erro 1.2 " . mysql_error();
                }
            }
        } else {
            $sql = "DELETE FROM T147 WHERE T147_Id='{$T147_Id}'";
            mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return "Erro 1.3 " . mysql_error();
            }
        }
        
        return true;
    }
    
    /**
     * cancelarServico
     *
     * @param String $T147_Id
     * @return String/bool
     */    
    public function cancelarServico($T147_Id)
    {
        global $g;
        
        if (empty($T147_Id)) {
            return "Id não recebido.";
        }
        
        mysql_query("UPDATE T147 SET T147_FLag_Cancelado='S' WHERE T147_Id='{$T147_Id}'");
        
        if (!mysql_error()) {
            return true;
        } else {
            return mysql_error();
        }
    }

     /**
     * duplicar_VEN002
     * Chamada DUPLICAR do menu automatico da janela
     *
     * @param String $dias
     * @return String
     */    
	public Function duplicar_ven002form02($divId, $divIdRoot, $tabela, $id)
    {
        global $T005_Id_Novo;

        $mT005 = array("T005_Id" => $id);
        $mT005Anterior = array();

        $retorno = $this->VEN002DuplicarPedidosSelecionados($mT005, $mT005Anterior);
        if ($retorno === true){
            //$retorno = "Pedido duplicado com sucesso.";
            //$retorno = "dialogConfirm('DUPLICAR', '{$retorno}');";
            // alterado para abrir o pedido quando duplicar
            $novoId = uniqid();
            $retorno = "$('#{$divId}').closest('.ui-dialog-content').dialog('close'); abrirJanela(false, '{$divIdRoot}', '{$divId}' ,'{$novoId}', '', 'Editar Pedido', '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent('{$T005_Id_Novo}') + '&tabela=T005', [990,605])";
    
        } else {
            $retorno = "dialogConfirm('ERRO', '{$retorno}');";
        }

        return $retorno;
    }


    /**
     * verificarDuplicarEmpresaPedidosSelecionados
     *
     * @param String $dias
     * @return String
     */    
	public Function VEN002VerificarDuplicarPedidosSelecionados($mT005,$mT005Anterior) 
    {
        return true;
    }

    /**
     * duplicarPedidosSelecionados
     *
     * @param String $dias
     * @return String
     */    
    public function VEN002DuplicarPedidosSelecionados($mT005, $mT005Anterior, $vendaFutura = false, $gerenciarTransacao = true)
    {
        global $g, $T005_Id_Novo;

        $T005 = mysql_query("select * from T005 where T005_Id='{$mT005['T005_Id']}'");
        $mT005 = mysql_fetch_assoc($T005);

        if ($gerenciarTransacao) {
            mysql_query("SET AUTOCOMMIT=0");
            mysql_query("START TRANSACTION");
        }

        $campos         = "";
        $valores        = "";
        foreach ($mT005 as $key => $value) {
            if ($key == 'T005_Id') {
                continue;
            } elseif ($key == "T005_T003_Id") {
                $campos .= $key . ", ";
                $valores .= "'',";
            } elseif ($key == "T005_T005_Id_Agrupado") {
                $campos .= $key . ", ";
                $valores .= "'',";
            } elseif ($key == "T005_T007_Id") {
                $campos .= $key . ", ";
                $valores .= "'',";
            }elseif ($key == "T005_T275_Id") {
                $campos .= $key . ", ";
                $valores .= "'',";
            }elseif($key == 'T005_Id_Pedido_Externo'){
                $campos.=$key.", ";
                $valores.= "null,";
            }elseif($key == 'T005_Flag_Marcado'){
                $campos.=$key.", ";
                $valores.= "'',";
            }elseif($key == 'T005_Flag_Status'){
                $campos.=$key.", ";
                $valores.= "'0',";
            }elseif($key == 'T005_Flag_Almoxarifado'){
                $campos.=$key.", ";
                $valores.= "'N', ";
            }elseif($key == 'T005_Nome_Status'){
                $campos.=$key.", ";
                $valores.= "'Pendente',";
            }elseif($key == 'T005_Data_Emissao'){
                $campos.=$key.", ";
                $valores.= "curdate(),";                 
            }elseif($key == 'T005_Flag_Totalizar_Peso'){
                $campos.=$key.", ";
                $valores .= "'" . mysql_real_escape_string($g['C031']['totalizarPesoVenda']) . "',";
            }elseif($key == 'T005_Liberou_Credito_Valor_Total' || $key == 'T005_Liberou_Credito_Desconto' || $key == 'T005_Liberou_Credito_Prazo_Medio' || $key == 'T005_Liberou_D024_Id' || $key == 'T005_Data_Hora_Almoxarifado' ){
                // Dados que são populados na liberação do crédito, não popular, se o pedido não tiver alteração do orignal não vai passar pela análise de crédito
                continue;
            }elseif ($key == "T005_Data_Saida") {
                $campos .= $key . ", ";
                $valores.= "'0000-00-00',";
            }elseif ($key == "T005_Hora_Saida") {
                $campos .= $key . ", ";
                $valores.= "'00:00:00',";
            }elseif ($value==NULL) {
                $campos .= $key . ", ";
                $valores .= "'',";
            } else {
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($value) . "',";
            }
        }
        $campos  = trim($campos, ', ');
        $valores = trim($valores, ',');
        $insT005 = "INSERT INTO T005 ({$campos}) VALUES ({$valores})";
        $sql = mysqli_query($insT005);
        $erro = mysql_error();
        if (!$sql) {
            mysql_query("ROLLBACK");
            return "Erro 2: $erro" ;
        }
        $T005_Id_Novo = mysql_insert_id();

        // Copia tabela estendida
        $T005A = mysql_query("select * from T005A where T005A_T005_Id='{$mT005['T005_Id']}'");
        $mT005A = mysql_fetch_assoc($T005A);

        $campos         = "";
        $valores        = "";
        foreach ($mT005A as $key => $value) {
            
            if ($key == 'T005A_Id') {
                continue;
            } elseif ($key == "T005A_T005_Id") {
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($T005_Id_Novo) . "',";
            } elseif ($key == "T005A_T005_Id_Original") {
                $campos .= $key . ", ";
                $valores .= "'',";
            } elseif ($key == "T005A_JSON") {
                continue;
            } elseif ($key == "T005A_Liberou_Credito_Portador") {
                // Dados que são populados na liberação do crédito, não popular, se o pedido não tiver alteração do orignal não vai passar pela análise de crédito.
                continue;
            } elseif ($value==NULL) {
                $campos .= $key . ", ";
                $valores .= "'',";
            }  else {
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($value) . "',";
            }
        }
        $campos  = trim($campos, ', ');
        $valores = trim($valores, ',');
        $insT005A = "INSERT INTO T005A ({$campos}) VALUES ({$valores})";
        $sql = mysqli_query($insT005A);
        $erro = mysql_error();
        if (!$sql) {
            mysql_query("ROLLBACK");
            return "Erro 3: $erro" ;
        }

    
        $T006 = mysql_query("SELECT * FROM T006 WHERE T006_T005_Id='{$mT005['T005_Id']}'");

        $erro = mysql_error();
        if (!$T006) {
            mysql_query("ROLLBACK");
            return "Erro 4: $erro" ;
        }

        $item = 1;
        while ($mT006 = mysql_fetch_assoc($T006)) {
            $campos  = "";
            $valores = "";
            foreach ($mT006 as $key => $value) {
                if ($key == 'T006_Id') {
                    continue;
                } elseif ($key == "T006_T005_Id") {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($T005_Id_Novo) . "',";
                } elseif ($key == "T006_Item") {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($item) . "',";
                } elseif ($key == "T006_C004_Id_Estoque") {
                    $campos .= $key . ", ";
                    $valores .= "NULL,";
                } elseif ($key == "T006_T008_Id") {
                    $campos .= $key . ", ";
                    $valores .= "NULL,";
                } elseif ($key == "T006_Quantidade_Separacao") {
                    $campos .= $key . ", ";
                    $valores .= "0,";
                } elseif ($key == "T006_T219_Id") {
                    $campos .= $key . ", ";
                    $valores .= "0,";
                } elseif ($key == "T006_T225_Id") {
                    $campos .= $key . ", ";
                    $valores .= "0,";
                } elseif ($key == "T006_Flag_Gerar_Cotacao") {
                    $campos .= $key . ", ";
                    $valores .= "'N',";
                } elseif ($key == "T006_Flag_Status") {
                    $campos .= $key . ", ";
                    $valores .= "'',";
                }else {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($value) . "',";
                }
            }
            $campos  = trim($campos, ', ');
            $valores = trim($valores, ',');
            $insT006 = "INSERT INTO T006 ({$campos}) VALUES ({$valores})";
            $sql = mysqli_query($insT006);
            $erro = mysql_error();
            if (!$sql) {
                mysql_query("ROLLBACK");
                return "Erro 5: $erro" ;
            }

            $T006_Id_Novo = mysql_insert_id();
            
            $T006A = mysql_query("SELECT * FROM T006A WHERE T006A_T006_Id='{$mT006['T006_Id']}'");
            $mT006A = mysql_fetch_assoc($T006A);
            $campos  = "";
            $valores = "";
            foreach ($mT006A as $key => $value) {

                if ($key == 'T006A_Id') {
                    continue;
                } elseif ($key == "T006A_T006_Id") {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($T006_Id_Novo) . "',";
                } elseif ($key == "T006A_JSON") {
                    continue;
                } elseif ($key == "T006A_Flag_Notificacao") {
                    $campos .= $key . ", ";
                    $valores .= "'',";
                } elseif ($key == "T006A_Flag_Producao_Emergencia") {
                    $campos .= $key . ", ";
                    $valores .= "'',";
                }  else {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($value) . "',";
                }
            }

            $campos  = trim($campos, ', ');
            $valores = trim($valores, ',');
            $insT006A = "INSERT INTO T006A ({$campos}) VALUES ({$valores})";
            $sql = mysqli_query($insT006A);
            $erro = mysql_error();
            if (!$sql) {
                mysql_query("ROLLBACK");
                return "Erro 6: $erro" ;
            }

            // Precisa atualizar o estoque para ajustar dados de ordem de compra, estoque pedido, ordem de producao...
            mysqli_query("UPDATE D009A SET D009A_Flag_Atualizar_Estoque = 'S' WHERE D009A_D009_Id = '{$mT006['T006_D009_Id']}'");

            $item++;
        }

        $sql = mysqli_query("UPDATE T005 SET T005_Flag_Marcado='N' WHERE T005_Id = '{$mT005['T005_Id']}'");
        $erro = mysql_error();
        if (!$sql) {
            mysql_query("ROLLBACK");
            return "Erro 7: $erro" ;
        }
        $sql = mysql_query("insert into T178 (T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) values ('{$T005_Id_Novo}','{$g['usuarioAtual']}',CURRENT_TIMESTAMP(), 'Pedido duplicado do pedido {$mT005['T005_Id']}','')");
        $erro = mysql_error();
        if (!$sql) {
            mysql_query("ROLLBACK");
            return "Erro 8: $erro" ;
        }

        // Ajusta para o novo preço
        // Parametro vendaFutura ven do APP, é uma cópia do pedido e por isso não pode recalcular
        if($g['C031']['duplicarPedidoOrcamentoAtualizaPrecoTabela'] == 'S' && !$vendaFutura){
            $this->atualizarPrecoTabela($T005_Id_Novo);
            $T006 = mysql_query("SELECT T006_Id,
                                        T006_Valor_Preco_Original
                                   FROM T006
                                  WHERE T006_T005_Id = '{$T005_Id_Novo}'");

            while($mT006 = mysql_fetch_array($T006)){
                mysql_query("UPDATE T006 SET T006_Valor_Preco_Sem_Desconto_Unitario = T006_Valor_Preco_Original, T006_Percentual_Desconto = 0 WHERE T006_Id = '{$mT006['T006_Id']}'");
            }
        }
        
        if ($gerenciarTransacao) {
            mysql_query("COMMIT");
            mysql_query("SET AUTOCOMMIT=1");
        }

        return true;
    }

    /**
     * Duplica o pedido e, quando necessário, aplica a empresa escolhida à cópia.
     */
    public function VEN002DuplicarPedidosParaEmpresa($mT005, $mT005Anterior, $C004_Id_Novo)
    {
        global $T005_Id_Novo;

        $C004_Id_Novo = (int) $C004_Id_Novo;
        if ($C004_Id_Novo <= 0) {
            return 'Informe a empresa de destino.';
        }

        $empresa = mysql_query("SELECT C004_Id FROM C004 WHERE C004_Id = '{$C004_Id_Novo}' AND C004_Flag_Ativo = 'S'");
        if (!$empresa || mysql_num_rows($empresa) == 0) {
            return 'A empresa de destino não está ativa.';
        }

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        $retorno = $this->VEN002DuplicarPedidosSelecionados($mT005, $mT005Anterior, false, false);
        if ($retorno !== true) {
            mysql_query("ROLLBACK");
            mysql_query("SET AUTOCOMMIT=1");
            return $retorno;
        }

        $pedidoNovo = mysql_fetch_assoc(mysql_query("SELECT T005_C004_Id FROM T005 WHERE T005_Id = '{$T005_Id_Novo}'"));
        if ($pedidoNovo['T005_C004_Id'] != $C004_Id_Novo) {
            $retorno = $this->VEN002MudarEmpresaPedido($T005_Id_Novo, array(), $C004_Id_Novo, false);
            if ($retorno !== true) {
                mysql_query("ROLLBACK");
                mysql_query("SET AUTOCOMMIT=1");
                return $retorno;
            }
        }

        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");
        return true;
    }
    
    /**
     * verificarMudarEmpresaPedidosSelecionados
     *
     * @param String $dias
     * @return String
     */    
	public Function VEN002VerificaMudarEmpresaPedido($mT005, $T005Anterior) 
    {
        if ($mT005['T005_Flag_Status'] != '0') {
            return "Só é possível mudar pedidos com status pendente." ;
        }
        if ($mT005['T007_Numero_Nota_Fiscal'] > 0) {
            return "Já existe nota fiscal para o pedido {$mT005['T005_Id']}.";
        }
        if ($mT005['T005_T005_Id_Agrupado'] > 0) {
            return "O pedido {$mT005['T005_Id']} está agrupado ao pedido {$mT005['T005_T005_Id_Agrupado']}";
        }
      
        return true;
    }

    /**
     * mudarEmpresaPedidosSelecionados
     *
     * @param String $dias
     * @return String
     */    
    public function VEN002MudarEmpresaPedido($T005_Id, $T005Anterior, $C004_Id_Novo, $gerenciarTransacao = true)
    {
        global $g;

        require_once("bibliotecas/classes/CAD002.php");
	    $CAD002 = new CAD002();

        if (is_array($T005_Id)){
            $T005_Id = $T005_Id['T005_Id'];
        } 

        if ($T005_Id<=0) {
            return "Faltou parâmetro Id Pedid. Informe o suporte técnico.";
        }

        // Verifica se existem produtos separados no estoque
        $T006  = mysql_query("SELECT SUM(T006_Quantidade_Separacao) AS T006_Quantidade_Separacao 
                                FROM T006 
                               WHERE T006_T005_Id = '{$T005_Id}'");
        $mT006 = mysql_fetch_array($T006);
        if ($mT006['T006_Quantidade_Separacao'] > 0) {
            return "Pedido ".$T005_Id." não pode ser mudado de empresa porque existem produtos separados no estoque<br/>Por favor verifique com o setor de expedição";
        }

        // Percorre muita tabela, cliente que tem bastante registro acaba demorando
        // Feito a troca do T005_C004_Id e T006_D009_Id a mão
        /*$retorno =  gMudarC004('T005', $T005_Id, $C004_Id_Novo);
        if ($retorno !== true ) {
            return $retorno;
        }
        */

        if ($gerenciarTransacao) {
            mysqli_query("SET AUTOCOMMIT=0");
	        mysqli_query("START TRANSACTION");
        }

        mysqli_query("UPDATE T005 SET T005_C004_Id = '{$C004_Id_Novo}' WHERE T005_Id='{$T005_Id}'");
        $erro = mysql_error();
        if ($erro) { 
            mysqli_query("ROLLBACK");
            return "Erro 1: {$erro}";
        }

        $novoD009 = mysql_query("SELECT T006_Id,
                                        D009_D049_Id,
                                        D009_Id
                                   FROM T006 
                              LEFT JOIN D009 ON D009_Id = T006_D009_Id
                                  WHERE T006_T005_Id='{$T005_Id}'");
                                  
        while($mNovoD009 = mysql_fetch_array($novoD009)){

            $D009 = mysql_query("SELECT D009_Id 
                                   FROM D009 
                                  WHERE D009_D049_Id = '{$mNovoD009['D009_D049_Id']}'
                                    AND D009_C004_Id = '{$C004_Id_Novo}'");
            $mD009 = mysql_fetch_array($D009);

            if (empty($mD009['D009_Id'])) {
                mysqli_query("ROLLBACK");
                return "Produto do pedido {$T005_Id} não possui estoque cadastrado na empresa de destino.";
            }

            mysqli_query("UPDATE T006 SET T006_D009_Id = '{$mD009['D009_Id']}',
                                          T006_C004_Id = '{$C004_Id_Novo}',
                                          T006_T066_Id = 0 
                                    WHERE T006_Id = '{$mNovoD009['T006_Id']}'");
            $erro = mysql_error();
            if ($erro) { 
                mysqli_query("ROLLBACK");
                return "Erro 2: {$erro}";
            }
        } 
              
        $C031 = mysql_query("SELECT C031_Valor 
                               FROM C031 
                              WHERE C031_Campo = 'padraoFaixaCalculoIcmsCredito' 
                                AND C031_C004_Id = '{$C004_Id_Novo}'");
        $mC031 = mysql_fetch_assoc($C031);

        mysqli_query("UPDATE T005 SET T005_T134_Id = '{$mC031['C031_Valor']}' WHERE T005_Id='{$T005_Id}'");

        $this->ven002atualizarCFOP($T005_Id);

        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();

        // Atualiza estoque dos produtos dos pedidos pendentes
        $T006 = mysql_query("SELECT *,
                                    (D009_Quantidade_Estoque-D009_Saldo_OE(D009_Id,0)-D009_Quantidade_Estoque_Loja) as Saldo
                                FROM T006 
                           LEFT JOIN T008 ON T008_T006_Id=T006_Id 
                           LEFT JOIN D009 ON D009_Id=T006_D009_Id
                           LEFT JOIN D049 ON D049_Id=D009_D049_Id
                           LEFT JOIN T005 ON T005_Id=T006_T005_Id
                           LEFT JOIN T001 ON T001_Id=D009_T001_Id
                           LEFT JOIN T219 ON T219_Id=T006_T219_Id
                               WHERE T006_T005_Id='{$T005_Id}'");

        $mD009 = array();
        while($mT006 = mysql_fetch_assoc($T006)){
            // Reprocessa estoque dos pedidos
            $CAD002->atualizaPrecoTabelaPedido($mT006);
            $CAD002->D001_reprocessa_historico($mT006['D049_D001_Id'],0,10);   
            $D009_Id = $mT006['D009_Id'];
            if ($mD009[$D009_Id] !== true) {
                $CAD002->processaEstoquePedidos($mT006['T006_D009_Id'], $mT006['Saldo']);
            } else {
                $mD009[$D009_Id] = true;
            }
        }

        $this->atualizarAliquotasPedidos($T005_Id, $C004_Id_Novo); // Atualiza aliquota após atualizar os preços dos produtos dentro do T006

        mysqli_query("update T005 set T005_Flag_Marcado='N' where T005_Id='{$T005_Id}'");
        mysqli_query("insert into T178 (T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) 
            VALUES ('{$T005_Id}', {$g['usuarioAtual']}, CURRENT_TIMESTAMP(),'Pedido transferido da empresa id {$g['empresaAtual']}','')");

        if ($gerenciarTransacao) {
            mysqli_query("COMMIT");
	        mysqli_query("SET AUTOCOMMIT=1");
        }
       
        return true;
    }

    /**
     * excluirPedido
     *
     * @param String $T005_Id
     * @return String/bool
     */    
    public function excluirPedido($T005_Id)
    {
        global $g;
        if (empty($T005_Id)) {
            return "Não foi passado o Id do Pedido, contate o suporte técnico";
        }
        
        $cClausula = " SELECT ";
        $cClausula .= " * ";
        $cClausula .= " FROM T005 ";
        $cClausula .= " WHERE T005_Id='{$T005_Id}' ";
        $cSql_T005 = mysql_query($cClausula);
        $mT005     = mysql_fetch_array($cSql_T005);
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        $cClausula = "DELETE FROM T090 WHERE T090_T005_Id='{$T005_Id}'";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro <br /><br /> $cClausula";
        }

        // Remove os complementos dos itens antes dos próprios itens. Sem isso,
        // uma eventual reutilização de T006_Id pode associar dados antigos ao novo pedido.
        $cClausula = "DELETE T006A
                        FROM T006A
                       INNER JOIN T006 ON T006_Id = T006A_T006_Id
                       WHERE T006_T005_Id = '{$T005_Id}'";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro <br /><br /> $cClausula";
        }
        
        $T006 = mysql_query("SELECT T006_Id FROM T006 WHERE T006_T005_Id='{$T005_Id}'");
        while ($mT006 = mysql_fetch_array($T006)) {
            $cClausula = "DELETE FROM T006 WHERE T006_Id='{$mT006['T006_Id']}'";
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: $cErro <br /> <br /> $cClausula";
            }
        }

        // Remove o complemento do cabeçalho antes de excluir o pedido.
        // T162 e T178 são mantidas por serem registros históricos/auditoria.
        $cClausula = "DELETE FROM T005A WHERE T005A_T005_Id = '{$T005_Id}'";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro <br /><br /> $cClausula";
        }

        $D085 = mysql_query("SELECT D085_Id, D085_T259_Id FROM D085 WHERE D085_Id_Entidade = '{$T005_Id}'AND D085_Entidade = 'T005'");        
        while ($mD085 = mysql_fetch_array($D085)) {
            $cClausula = "delete FROM D085 WHERE D085_Id='{$mD085['D085_Id']}'";
            mysql_query($cClausula);

            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: $cErro <br /><br /> $cClausula";
            }

            $cClausula = "delete FROM T259 WHERE T259_Id='{$mD085['D085_T259_Id']}'";
            mysql_query($cClausula);

            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: $cErro <br /><br /> $cClausula";
            }
        }
        
        $cClausula = "DELETE FROM T005 WHERE T005_Id='{$T005_Id}'";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro<br /><br />$cClausula";
        }
        
        $cClausula = "DELETE FROM T147 WHERE T147_T005_Id='{$T005_Id}'";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro<br /><br />$cClausula";
        }
        
        $T005 = mysql_query("SELECT T005_Id FROM T005 WHERE T005_T005_Id_Agrupado = '{$T005_Id}'");
        while ($mT005 = mysql_fetch_assoc($T005)) {
               mysql_query("update T005 set T005_T005_Id_Agrupado='0' where T005_Id='{$mT005['T005_Id']}'"); 
               mysql_query("insert into T178 (T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ('{$mT005['T005_Id']}', {$g['usuarioAtual']}, CURRENT_TIMESTAMP(),'Exclusão de pedido agrupado {$T005_Id}','')");
        }
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro<br /><br />$cClausula";
        }

        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");
        return true;
    }
    
    /**
     * excluirPedido
     *
     * @param Form $form
     * @param String $T147_Id
     * @return String/bool
     */    
    public function insereServico($form, $T147_Id)
    {
        $D112_Id = $form->campoValorEnviado('T147_D112_Id');
        $sql     = "SELECT * FROM D113 WHERE D113_D112_Id='{$D112_Id}'";
        $array1  = mysql_query($sql);
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        while ($row = mysql_fetch_assoc($array1)) {
            // D113_D112_Id, D113_D110_Id, D113_Titulo, D113_Descricao
            $sql = "INSERT INTO T148 (T148_T147_Id, T148_Titulo, T148_Descricao) VALUES ('" . mysql_real_escape_string($T147_Id) . "', '"
            //.mysql_real_escape_string($row['D113_D110_Id'])."', '"
                . mysql_real_escape_string($row['D113_Titulo']) . "', '" . mysql_real_escape_string($row['D113_Descricao']) . "');";
            mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return mysql_error();
            }
        }
        
        $sql    = "SELECT * FROM D114 WHERE D114_D112_Id='{$D112_Id}'";
        $array2 = mysql_query($sql);
        
        while ($row = mysql_fetch_assoc($array2)) {
            $sql = "INSERT INTO T149 (T149_D037_Id, T149_D009_Id, T149_T147_Id, T149_Quantidade) VALUES ('" . mysql_real_escape_string($row['D114_D037_Id']) . "', '" . mysql_real_escape_string($row['D114_D009_Id']) . "', '" . mysql_real_escape_string($T147_Id) . "', '" . mysql_real_escape_string($row['D114_Quantidade']) . "');";
            mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return mysql_error();
            }
        }
        
        $sql    = "SELECT * FROM T147 WHERE T147_Id='{$T147_Id}'";
        $result = mysql_query($sql);
        $linha  = mysql_fetch_array($result);
        
        $sql     = "SELECT * FROM T171 WHERE T171_D116_Id='{$linha['T147_D116_Id']}' AND T171_T005_Id='{$linha['T147_T005_Id']}'";
        $result2 = mysql_query($sql);
        if (mysql_num_rows($result2) == 0) {
            $sql = "INSERT INTO T171
            (T171_D116_Id, T171_T005_Id, T171_T147_Id)
                VALUES
            ('{$linha['T147_D116_Id']}', '{$linha['T147_T005_Id']}', '{$T147_Id}')";
            
            mysql_query($sql);
            
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return mysql_error();
            }
        }
        mysql_query("COMMIT");
        return true;
    }
    
    /**
     * ven002aplicarIpvMinimo
     *
     * @param String $T005_Id
     * @param String $IPV_Minimo
     * @return String/bool
     */    
    public function ven002aplicarIpvMinimo($T005_Id, $IPV_Minimo)
    {    
        if ($T005_Id == 0) {
            return ("Nao encontrado o id da entidade principal");
        }
        
        if ($IPV_Minimo == 0) {
            return "Digite o IPV Mínimo";
        }
        
        $cSql_T005 = mysql_query("SELECT * FROM T005 WHERE T005_Id='{$T005_Id}'");
        $mT005     = mysql_fetch_array($cSql_T005);
        if ($mT005['T005_Flag_Devolucao'] == 'S') {
            return "ISTO É UMA DEVOLUÇÃO";
        }
        if ($mT005['T005_Flag_Travar_Pedido'] == 1) {
            return "PEDIDO TRAVADO";
        }
        
        $cSql_T006 = mysql_query("SELECT T006_Id,
                                 T006_Valor_Custo_Unitario
                            FROM T006
                           WHERE T006_T005_Id='{$T005_Id}'");
        
        if (mysql_num_rows($cSql_T006) == 0) {
            return ("Não existem itens para aplicar o IPV");
        }
        
        while ($mT006 = mysql_fetch_array($cSql_T006)) {
            
            $Valor_Preco_Unitario = round($mT006['T006_Valor_Custo_Unitario'] * $IPV_Minimo, 2);
            
            mysql_query("UPDATE T006 
                          SET T006_Valor_Preco_Sem_Desconto_Unitario='{$Valor_Preco_Unitario}'
                        WHERE T006_Id='{$mT006['T006_Id']}'");
            
            mysql_query("call T006_Gravar_Totalizacao_4({$mT006['T006_Id']});");
        }
        
        return true;
    }
    
    /**
     * importarItens
     *
     * @param String $T005_Id
     * @param String $T005_Id_Origem
     * @return String/bool
     */    
    public function importarItens($T005_Id, $T005_Id_Origem)
    {   
        $T005_Id        = trim($T005_Id);
        $T005_Id_Origem = trim($T005_Id_Origem);

        if (empty($T005_Id)) {
            
            return "Faltou o parametro T005_Id";
        }
        if (empty($T005_Id_Origem)) {
            return "Digite o numero do pedido";
        }
        
        if ($T005_Id == $T005_Id_Origem) {
            return "Voce não pode importar o pedido para ele mesmo";
        }
        
        $cSql_T005 = mysql_query("SELECT * FROM T005 WHERE T005_Id='{$T005_Id}'");
        $mT005     = mysql_fetch_array($cSql_T005);
        
        $cSql_T005_Origem = mysql_query("SELECT * FROM T005 WHERE T005_Id='{$T005_Id_Origem}'");
        $mT005_Origem     = mysql_fetch_array($cSql_T005_Origem);
        
        if ($mT005['T005_Flag_Travar_Pedido'] == 'S') {
            return "PEDIDO ESTA TRAVADO, NÃO PODE SER ALTERADO!";
        }
        
        If ($mT005['T005_Flag_Status'] != $mT005_Origem['T005_Flag_Status']) {
            return "IMPORTAÇÃO CANCELADA! Os pedidos estão com Status diferentes.";
        }
        
        If ($mT005['T005_C004_Id'] != $mT005_Origem['T005_C004_Id']) {
            return "IMPORTAÇÃO CANCELADA! Os pedidos são de Empresas diferentes.";
        }
        
        If ($mT005['T005_D024_Id'] != $mT005_Origem['T005_D024_Id']) {
            return "IMPORTAÇÃO CANCELADA! Os pedidos são de Clientes diferentes.";
        }
        /*
        If ($mT005['T005_D006_Id'] != $mT005_Origem['T005_D006_Id']) {
            return "IMPORTAÇÃO CANCELADA! Os pedidos possuem CFOP diferentes.";
        }
        */
        /////////////////////////////////////////////////////////////////////////////////
        //-- VERIFICA SE O ITEM EXISTEM NA EMPRESA DE DESTINO
        /////////////////////////////////////////////////////////////////////////////////
        $cSql_T006 = mysql_query("SELECT t2.D009_Id,
                                         D001_Codigo_Produto,
                                         D002_Descricao_Produto
                                    FROM T006
                               LEFT JOIN D009 as t1 ON T006_D009_Id=t1.D009_Id
                               LEFT JOIN D049 ON D049_Id=t1.D009_D049_Id
                               LEFT JOIN D001 ON D001_Id=D049_D001_Id
                               LEFT JOIN D002 ON D002_Id=D001_D002_Id
                               LEFT JOIN D009 as t2 ON D049_Id=t2.D009_D049_Id AND t2.D009_C004_Id='{$mT005['T005_C004_Id']}'
                                   WHERE T006_T005_Id='{$T005_Id}'
                                     AND t2.D009_Id=0");
        while ($mT006 = mysql_fetch_array($cSql_T006)) {
            $mSemCadastro[] = "{$mT006['D001_Codigo_Produto']} - {$mT006['D002_Descricao_Produto']}";
        }
        if (mysql_num_rows($cSql_T006) > 0) {
            $SemCadastro = implode("/p", $mSemCadastro);
            return "1. Existem itens sem cadastro na empresa $mT005[T005_C004_Id]/pque estao sendo importados do pedido $T005_Id_Origem<br />$SemCadastro";
        }
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        mysql_query("UPDATE T006 SET T006_T005_Id='{$T005_Id}',T006_Numero_Pedido_Venda='{$T005_Id_Origem}' WHERE T006_T005_Id='{$T005_Id_Origem}'");
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "2. Erro: $cErro<br />Clausula:$cErro";
        }

        mysql_query("UPDATE T238 SET T238_T005_Id ='{$T005_Id}' WHERE T238_T005_Id = '{$T005_Id_Origem}'");
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "2. Erro: $cErro<br />Clausula:$cErro";
        }
        
        mysql_query("UPDATE T005 SET T005_Flag_Status='0',T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1),T005_Flag_Almoxarifado='N' WHERE T005_Id='{$T005_Id_Origem}'");
        
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "3. Erro: $cErro<br />Clausula:$cErro";
        }
        
        mysql_query("call T005_Gravar_Totalizacao_4($T005_Id)");
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "4. Erro: $cErro<br />Clausula:$cErro";
        }
        
        mysql_query("call T005_Gravar_Totalizacao_4($T005_Id_Origem)");
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "5. Erro: $cErro<br />Clausula:$cErro";
        }
        
        mysql_query("COMMIT");
        return true;
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
        $cSql_T005 = mysql_query("SELECT T005_Flag_Devolucao FROM T005 LEFT JOIN D006 ON D006_Id=T005_D006_Id LEFT JOIN D024 ON D024_Id=T005_D024_Id WHERE T005_Id='{$T005_Id}'");
        $mT005     = mysql_fetch_array($cSql_T005);

        if ($mT005['T005_Flag_Devolucao'] == 'S') {
            return "Este é um pedido para emissão de nota de devolução, não é possivel executar a atualização de aliquotas";
        }
        log("T006_Id= ".$T006_Id);

        //=====================================================================
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //-- RODA A REGRA CFOP
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        if ($T006_Id > 0) {
            $cSql_T006 = mysql_query("SELECT
                                        T006_Id,
                                        D009_Id,
                                        D001_Codigo_Produto,
                                        D001_Peso_Unitario_Kg,
                                        T005_Flag_Lista_Preco,
                                        T005_D024_Id,
                                        T005_Flag_Acp,
                                        T006_D006_Id,
                                        ifnull(T006_T219_Id,0) as T006_T219_Id,
                                        ifnull(T006_T225_Id,0) as T006_T225_Id,
                                        IFNULL(D009_Origem_Mercadoria,'') as D009_Origem_Mercadoria,
                                        D009_Preco_Pauta_ST,
                                        T006_Flag_Origem_Produto
                                        FROM T005
                                        LEFT JOIN T006 ON T005_Id=T006_T005_Id
                                        LEFT JOIN D024 ON D024_Id=T006_D024_Id
                                        LEFT JOIN D009 ON D009_Id=T006_D009_Id
                                        LEFT JOIN D049 ON D049_Id=D009_D049_Id
                                        LEFT JOIN D001 ON D001_Id=D049_D001_Id
                                        WHERE T006_Id='{$T006_Id}'");
        } else {
            $cSql_T006 = mysql_query("SELECT
                                        T006_Id,
                                        D009_Id,
                                        D001_Codigo_Produto,
                                        D001_Peso_Unitario_Kg,
                                        T005_Flag_Lista_Preco,
                                        T005_D024_Id,
                                        T005_Flag_Acp,
                                        T006_D006_Id,
                                        ifnull(T006_T219_Id,0) as T006_T219_Id,
                                        ifnull(T006_T225_Id,0) as T006_T225_Id,
                                        IFNULL(D009_Origem_Mercadoria,'') as D009_Origem_Mercadoria,
                                        D009_Preco_Pauta_ST,
                                        T006_Flag_Origem_Produto
                                        FROM T005
                                        LEFT JOIN T006 ON T005_Id=T006_T005_Id
                                        LEFT JOIN D024 ON D024_Id=T006_D024_Id
                                        LEFT JOIN D009 ON D009_Id=T006_D009_Id
                                        LEFT JOIN D049 ON D049_Id=D009_D049_Id
                                        LEFT JOIN D001 ON D001_Id=D049_D001_Id
                                        WHERE T005_Id='{$T005_Id}'");
        }
        while ($mT006 = mysql_fetch_array($cSql_T006)) {
            $arrayParametros = array();
            $arrayParametros['T006_Id']= $mT006['T006_Id'];
            $arrayParametros['D009_Id'] = $mT006['D009_Id'];   
            $arrayParametros['D009_Origem_Mercadoria']= $mT006['D009_Origem_Mercadoria'];
            $arrayParametros['T005_Flag_Lista_Preco']= $mT006['T005_Flag_Lista_Preco'];
            $arrayParametros['T005_D024_Id']= $mT006['T005_D024_Id'];
            $arrayParametros['T006_D006_Id']= $mT006['T006_D006_Id'];
            $arrayParametros['T006_T219_Id']= $mT006['T006_T219_Id'];
            $arrayParametros['T006_T225_Id']= $mT006['T006_T225_Id'];
            $arrayParametros['D001_Peso_Unitario_Kg']= $mT006['D001_Peso_Unitario_Kg'];
            $arrayParametros['D009_Preco_Pauta_ST']= $mT006['D009_Preco_Pauta_ST'];
            $arrayParametros['T006_Flag_Origem_Produto']= $mT006['T006_Flag_Origem_Produto'];
            $retorno = $this->atualizarAliquotas($T005_Id,$arrayParametros);              
            if($retorno !== true){
                return "Erro na atualizacao de aliquotas";
            }
        }
        return true;
    }

       /**
     * atualizarAliquotas
     *
     * @param String $T005_Id
     * @param String $T006_Id
     * @return String/bool
     */
    public function atualizarAliquotas($T005_Id,$arrayParametros = null)
    {
        require_once('bibliotecas/classes/FIS003.php');
        $FIS003 = new FIS003();
        require_once("bibliotecas/classes/VEN001.php");
        $VEN001 = new VEN001();
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();

        $T006_Id = $arrayParametros['T006_Id'];
        $D009_Id = $arrayParametros['D009_Id'];
        $D009_Origem_Mercadoria = $arrayParametros['D009_Origem_Mercadoria'];
        $T005_Flag_Lista_Preco = $arrayParametros['T005_Flag_Lista_Preco'];
        $T005_D024_Id = $arrayParametros['T005_D024_Id'];
        $T006_D006_Id = $arrayParametros['T006_D006_Id'];
        $T006_T219_Id = $arrayParametros['T006_T219_Id'];
        $T006_T225_Id = $arrayParametros['T006_T225_Id'];
        $D001_Peso_Unitario_Kg = $arrayParametros['D001_Peso_Unitario_Kg'];
        $D001_Cubagem_Unitaria = $arrayParametros['D001_Cubagem_Unitaria'];
        $T006_Preco_Pauta_ST   = $arrayParametros['D009_Preco_Pauta_ST'];
        $T006_Flag_Origem_Produto = $arrayParametros['T006_Flag_Origem_Produto'];

        if($D009_Origem_Mercadoria){
            $D049_Origem_Mercadoria = $D009_Origem_Mercadoria;
        }

        // Felipe Carrano 19/10/2023
        //Método retornaPrecoVenda utilizado apenas para pegar a origem do produto, não precisa das aliquotas
        //Chamando o aliquotas depois porque pode ter mudado a origem do produto e com isso pode mudar a aliquota do ICMS e IPI
        /* $Aliquotas        = $VEN001->retornaAliquotas($D009_Id, false, $T005_Id, false, false, $T006_Id);
        $demonstrativoNCM = $FIS003->demonstrativoNCM($Aliquotas['D005_Id']);
        $demonstrativoNCM = mysql_real_escape_string($demonstrativoNCM); */
        //$dadosPrecoVenda = $CAD002->retornaPrecoVenda($D009_Id, $T005_Flag_Lista_Preco, $T005_D024_Id, false, $T006_D006_Id, $Aliquotas['Aliquota_ICMS'], $T006_T219_Id, $T006_T225_Id, 4, $Aliquotas['Aliquota_PIS'], $Aliquotas['Aliquota_COFINS'], $Aliquotas['Aliquota_IPI'], $Aliquotas['ST_VA'], $Aliquotas['Percentual_Reducao_ICMS_ST'], $Aliquotas['ST_ICMS_Interno'], $Aliquotas['Percentual_Reducao_ICMS'], $Aliquotas['ICMS_Inter_Estadual'], $Aliquotas['Aliquota_IRPJ'], $Aliquotas['Aliquota_CSLL']);
        $dadosPrecoVenda = $CAD002->retornaPrecoVenda($D009_Id, $T005_Flag_Lista_Preco, $T005_D024_Id, false, $T006_D006_Id, 0, $T006_T219_Id, $T006_T225_Id, 4, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        $origemProduto   = $dadosPrecoVenda[5];

        if($T006_Flag_Origem_Produto != $origemProduto){
            // se alterou a origem pode mudar a aliquota do ICMS, atualiza aliquotas novamente
            mysqli_query("UPDATE T006 SET T006_Flag_Origem_Produto = '{$origemProduto}' WHERE T006_Id = '{$T006_Id}'");
        }

        $Aliquotas        = $VEN001->retornaAliquotas($D009_Id, false, $T005_Id, false, false, $T006_Id);
        $demonstrativoNCM = $FIS003->demonstrativoNCM($Aliquotas['D005_Id']);
        $demonstrativoNCM = mysql_real_escape_string($demonstrativoNCM);

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
        $cClausula .= " T006_Peso_Unitario='{$D001_Peso_Unitario_Kg}', ";
        $cClausula .= " T006_Cubagem_Unitaria='{$D001_Cubagem_Unitaria}', ";
        $cClausula .= " T006_Peso_Bruto='{$Aliquotas['D001_Peso_Unitario_Bruto']}', ";
        if(!is_numeric($origemProduto) || $origemProduto == '') {
            $cClausula .= " T006_Flag_Origem_Produto='{$D049_Origem_Mercadoria}', ";
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
        $cClausula .= " T006_Percentual_Base_Operacao_Propria='{$Aliquotas['Percentual_Base_Operacao_Propria']}', ";
        $cClausula .= " T006_Percentual_Trib_Federal='{$Aliquotas['Percentual_Trib_Federal']}', ";
        $cClausula .= " T006_Percentual_Trib_Estadual='{$Aliquotas['Percentual_Trib_Estadual']}', ";
        $cClausula .= " T006_Percentual_Trib_Municipal='{$Aliquotas['Percentual_Trib_Municpal']}', ";
        $cClausula .= " T006_Aliquota_ICMS_Credito='{$Aliquotas['Aliquota_ICMS_Credito']}', ";
        $cClausula .= " T006_Modalidade_ICMS_Substituicao='{$Aliquotas['Modalidade_ICMS_Substituicao']}', ";
        $cClausula .= " T006_Preco_Pauta_ST='{$T006_Preco_Pauta_ST}', ";
        $cClausula .= " T006_Modalidade_ICMS='{$Aliquotas['Modalidade_ICMS']}', ";
        $cClausula .= " T006_Percentual_Partilha_ICMS_Destino='{$Aliquotas['Percentual_Partilha_ICMS_Destino']}', ";
        $cClausula .= " T006_Percentual_Partilha_ICMS_Origem='{$Aliquotas['Percentual_Partilha_ICMS_Origem']}' ";
        $cClausula .= " WHERE T006_Id='{$T006_Id}'";
        mysql_query($cClausula);

        mysql_query("UPDATE T006A SET T006A_Percentual_Reducao_PIS            = '{$Aliquotas['Percentual_Reducao_PIS']}',
                                      T006A_Percentual_Reducao_COFINS         = '{$Aliquotas['Percentual_Reducao_COFINS']}',
                                      T006A_Percentual_Reducao_Base_DIFAL     = '{$Aliquotas['Percentual_Reducao_Base_DIFAL']}',
                                      T006A_Trib_Interna_UF_Dest_Reducao_Base = '{$Aliquotas['EIP001_Trib_Interna_UF_Dest_Reducao_Base']}',
                                      T006A_Percentual_FCP                    = '{$Aliquotas['Percentual_FCP']}',
                                      T006A_Situacao_Tributaria_CBS_IBS              = '{$Aliquotas['Situacao_Tributaria_CBS_IBS']}',
                                      T006A_Classificacao_Tributaria_CBS_IBS         = '{$Aliquotas['Classificacao_Tributaria_CBS_IBS']}',
                                      T006A_Aliquota_CBS                             = '{$Aliquotas['Aliquota_CBS']}',
                                      T006A_Aliquota_IBS_UF                          = '{$Aliquotas['Aliquota_IBS_UF']}',
                                      T006A_Aliquota_IBS_Municipal                   = '{$Aliquotas['Aliquota_IBS_Municipal']}',
                                      T006A_Percentual_Reducao_CBS                   = '{$Aliquotas['Percentual_Reducao_CBS']}',
                                      T006A_Percentual_Reducao_IBS_UF                = '{$Aliquotas['Percentual_Reducao_IBS_UF']}',
                                      T006A_Percentual_Reducao_IBS_Municipal         = '{$Aliquotas['Percentual_Reducao_IBS_Municipal']}',
                                      T006A_Percentual_Diferimento_CBS               = '{$Aliquotas['Percentual_Diferimento_CBS']}',
                                      T006A_Percentual_Diferimento_IBS_UF            = '{$Aliquotas['Percentual_Diferimento_IBS_UF']}',
                                      T006A_Percentual_Diferimento_IBS_Municipal     = '{$Aliquotas['Percentual_Diferimento_IBS_Municipal']}',
                                      T006A_Situacao_Tributaria_CBS_IBS_Regular      = '{$Aliquotas['Situacao_Tributaria_CBS_IBS_Regular']}',
                                      T006A_Classificacao_Tributaria_CBS_IBS_Regular = '{$Aliquotas['Classificacao_Tributaria_CBS_IBS_Regular']}',
                                      T006A_Aliquota_CBS_Regular                     = '{$Aliquotas['Aliquota_CBS_Regular']}',
                                      T006A_Aliquota_IBS_UF_Regular                  = '{$Aliquotas['Aliquota_IBS_UF_Regular']}',
                                      T006A_Aliquota_IBS_Municipal_Regular           = '{$Aliquotas['Aliquota_IBS_Municipal_Regular']}',
                                      T006A_Percentual_Reducao_CBS_Regular           = '{$Aliquotas['Percentual_Reducao_CBS_Regular']}',
                                      T006A_Percentual_Reducao_IBS_UF_Regular        = '{$Aliquotas['Percentual_Reducao_IBS_UF_Regular']}',
                                      T006A_Percentual_Reducao_IBS_Municipal_Regular = '{$Aliquotas['Percentual_Reducao_IBS_Municipal_Regular']}'
                                WHERE T006A_T006_Id = '{$T006_Id}'");

        return true;
    }
    
    /**
     * faturarPedido
     *
     * @param String $T005_Id
     * @return String/bool
     */     
    public function faturarPedido($T005_Id)
    {
        global $g, $Perfil_Usuario, $mensagem, $pedirSegundaLiberacao;
        unset($pedirSegundaLiberacao);
        $T005  = mysql_query("SELECT T005_Flag_Status,
                                     T005_D148_Id_Entrega,
                                     D024_D030_Id,
                                     T005_C004_Id,
                                     T005_D148_Id_Cobranca,
                                     T005_Flag_Frete,
                                     T005_D024_Id,
                                     T005_Valor_Total,
                                     T005_Valor_Total_Produtos,
                                     T005_Liberou_Credito_Valor_Total,
                                     T005_Liberou_Credito_Desconto,
                                     T005_Liberou_Credito_Prazo_Medio,
                                     T005A_Liberou_Credito_Portador,
                                     T005_D027_Id,
                                     T005_Liberou_D024_Id,
                                     T005_Hora_Emissao,
                                     T005_Data_Emissao,
                                     T005_Flag_ACP, 
                                     D024_Flag_Pessoa_Fisica_Juridica,
                                     D024_Nome_Empresa,
                                     D024_Cpf,
                                     D024_Cnpj,
                                     Cobranca.D148_D030_Id as paisCobranca, 
                                     Entrega.D148_D030_Id as paisEntrega,
                                     T224_Flag_Tipo
                                FROM T005
                           LEFT JOIN T005A ON T005A_T005_Id = T005_Id
                           LEFT JOIN D024 ON D024_Id=T005_D024_Id 
                           LEFT JOIN D148 AS Entrega  ON Entrega.D148_Id = T005_D148_Id_Entrega 
                           LEFT JOIN D148 AS Cobranca ON Cobranca.D148_Id = T005_D148_Id_Cobranca 
                           LEFT JOIN T224 ON T224_Id = T005_T224_Id
                               WHERE T005_Id='{$T005_Id}'");
        $mT005 = mysql_fetch_array($T005);

        if($mT005['T005_Flag_Status'] > 0 AND $mT005['T005_Flag_Status']!=14 AND $mT005['T005_Flag_Status']!=15 AND $mT005['T005_Flag_Status']!=9 AND $mT005['T005_Flag_Status']!=26 AND $mT005['T005_Flag_Status']!=22){
            return "Este pedido já está faturado.";
        }

        // A antecipação deve estar autorizada já ao solicitar o faturamento.
        // A geração da venda repete a validação e grava a referência fiscal.
        /*require_once('bibliotecas/classes/VEN012.php');
        $VEN012PagamentoAntecipado = new VEN012();
        $validacaoPagamentoAntecipado = $VEN012PagamentoAntecipado->validarPagamentoAntecipadoPedido($T005_Id);
        if ($validacaoPagamentoAntecipado !== true) {
            return $validacaoPagamentoAntecipado;
        } */

        if($mT005['T005_Flag_Status'] == 22 ){
            // Trava para o modelo expedicao 2, os pedidos podem ser divido na tela de faturamento comercial/balcao
            // E depois faturados, precisa verificar se a quantidade separação está batendo para lançamento no historico ao gerar nota
            $res = mysql_query("SELECT T006_Id,
                                       T238_Id,
                                       T006_Codigo_Produto,
                                       T006_Descricao_Produto,
                                       T006_Quantidade,
                                       SUM(T238_Quantidade_Separacao) AS SOMAT238
                                  FROM T006 
                             LEFT JOIN T238 ON T238_T006_Id = T006_Id 
                             LEFT JOIN D006 ON D006_Id=T006_D006_Id
                                 WHERE T006_T005_Id = '{$T005_Id}' 
                                   AND D006_Flag_Estoque != 'N'
                              GROUP BY T006_Id 
                                HAVING T238_Id IS NULL OR T006_Quantidade != SOMAT238");

            if (mysql_num_rows($res) > 0) {
                $produtos= '';
                while($row = mysql_fetch_array($res)){
                    $produtos .= $row['T006_Codigo_Produto'] .' - '. $row['T006_Descricao_Produto'] .' - Separado: '. gCorrigeNumero($row['SOMAT238']).' / Quantidade: '.gCorrigeNumero($row['T006_Quantidade'])."\n";
                }
                
                return "Os produtos abaixo não estão separados corretamente\n\n".$produtos;
            }
        }

        //Verifica como as CFOP's estão configuradas para gerar contas
        $cSql_T006_CFOP = mysql_query("SELECT D006_Flag_Gera_Contas,
                                                D006_Codigo_CFOP
                                            FROM T006
                                    LEFT JOIN D006 ON D006_Id=T006_D006_Id
                                        WHERE T006_T005_Id='{$T005_Id}'
                                        GROUP BY D006_Flag_Gera_Contas");
        if (mysql_num_rows($cSql_T006_CFOP) > 1) {
            $CFOP_Utilizados = '';
            while ($mSql_T006_CFOP = mysql_fetch_array($cSql_T006_CFOP)) {
                $CFOP_Utilizados .= $mSql_T006_CFOP['D006_Codigo_CFOP']."; ";
            }
            return "Foi selecionada mais de uma CFOP com o parametro gerar contas DIFERENTE.\nO gerar contas deve ser igual para todas as CFOP's.\n\nCFOP's utilizadas: " . $CFOP_Utilizados;
        } else if (mysql_num_rows($cSql_T006_CFOP) == 0) {
            $cSql_T006_CFOP    = mysql_query("SELECT D006_Flag_Gera_Contas,
                                                        D006_Codigo_CFOP
                                                FROM T005
                                            LEFT JOIN D006 ON D006_Id=T005_D006_Id
                                                WHERE T005_Id='{$T005_Id}'
                                            GROUP BY D006_Flag_Gera_Contas");
            $mT006_CFOP        = mysql_fetch_array($cSql_T006_CFOP);
            $cFlag_Gera_Contas = $mT006_CFOP['D006_Flag_Gera_Contas'];
        } else {
            $mT006_CFOP        = mysql_fetch_array($cSql_T006_CFOP);
            $cFlag_Gera_Contas = $mT006_CFOP['D006_Flag_Gera_Contas'];
        }
        
        if (($cFlag_Gera_Contas != 'N') AND ($cFlag_Gera_Contas != 'P') AND ($cFlag_Gera_Contas != 'R')) {
            return "O campo 'Gera contas' do cadastro da CFOP está com um valor inválido.\n\nAcesse: Fiscal -> CFOP -> Selecione a CFOP -> aba Financeiro -> Campo 'Gera Contas'.\n\nDeve estar preenchido com: 'NÃO ALTERA CONTAS', 'RECEBER' ou 'PAGAR'.\n\nPor favor, verifique.";
        }

        $retornoValidacao = $this->excecaoValidaPedido($T005_Id);
        if ($retornoValidacao !== true)
        {
            return $retornoValidacao;
        }
        
        /*$retornoValidacao = $this->validador(null, $T005_Id, null);
        if($retornoValidacao !== true){
            return $retornoValidacao;
        }*/

        $erros = '';
        // Verifica se o endereço de entrega e cobrança está preenchido com o país.
        if(($mT005['T005_D148_Id_Entrega'] > 0) AND ($mT005['paisEntrega'] <= 0)){
            $erros .= "País do endereço de entrega, no cadastro de endereço do cliente, não informado. \n";
        }
        if($mT005['D024_D030_Id'] <= 0){
            $erros .= "País do endereço padrão, no cadastro do cliente, não informado. \n\n";
        }
        if(($mT005['T005_D148_Id_Cobranca'] > 0) AND ($mT005['paisCobranca'] <= 0)){
            $erros .= "País do endereço de cobrança, no cadastro de endereço, do cliente não informado. \n";
        }

        if(!empty($erros)){
            return $erros;
        }

        // Irá verificar se deve validar dados do pedido de transferência, hoje não valida pagamento e transportadora. / T224_Flag_Tipo = 2 OC de transferencia
        $validaPedidoTransferencia = ($mT005['T224_Flag_Tipo'] == '2' && $g['C031']['validaDadosAoFaturarPedidoTransferencia'] == 'N') ? 'N' : 'S';
        $permitirVenda = $g['C031']['permitirFaturarSemEstoque'];

        $sqlEstoque  = mysql_query("SELECT T006_Id
                                      FROM T006 
                                 LEFT JOIN T005 ON T005_Id=T006_T005_Id
                                 LEFT JOIN T055 ON T055_D009_Id=T006_D009_Id AND T055_T075_Id=T006_T075_Id
                                 LEFT JOIN D006 ON D006_Id=T006_D006_Id
                                     WHERE T006_T005_Id='{$T005_Id}' 
                                       AND (T006_Quantidade>IF(IFNULL(T006_T075_Id,0)>0,IFNULL(T055_Quantidade,0),IF(IFNULL(T006_T206_Id,0)>0,D009_Quantidade_DF(T006_D009_Id,T006_T206_Id, T006_Id),IFNULL(T006_Quantidade_Estoque,0))))
                                       AND '{$permitirVenda}'='N' 
                                       AND D006_Flag_Estoque = 'D'
                                       AND IF(IFNULL(T006_C004_Id_Estoque,0) > 0, T006_C004_Id_Estoque, T005_C004_Id) = T005_C004_Id");
        
        $rowsEstoque = mysql_num_rows($sqlEstoque);
        if ($rowsEstoque > 0) {
            $msg = ($rowsEstoque > 1 ? "Há itens sem estoque suficiente, favor verificar" : "Existe item sem estoque suficiente, favor verificar");
            return $msg;
        }

        $estoqueForaErro = 0;
        $sqlEstoqueFora = mysql_query("SELECT T006_Id, T006_D009_Id, D009_D049_Id, T006_Quantidade, T006_C004_Id_Estoque 
                                        FROM T006 
                                    LEFT JOIN D009 ON D009_Id=T006_D009_Id 
                                    LEFT JOIN T005 ON T005_Id=T006_T005_Id
                                    LEFT JOIN D006 ON D006_Id=T006_D006_Id  
                                       WHERE T006_T005_Id='{$T005_Id}' 
                                        AND '{$permitirVenda}'='N' 
                                        AND D006_Flag_Estoque = 'D'
                                        AND (T006_T206_Id IS NULL OR T006_T206_Id = '') 
                                        AND IF(IFNULL(T006_C004_Id_Estoque,0) > 0, T006_C004_Id_Estoque, T005_C004_Id) != T005_C004_Id");
        while($rowEstoqueFora = mysql_fetch_array($sqlEstoqueFora)){
            $sqlD009 = mysql_query("SELECT D009_Id FROM D009 WHERE D009_D049_Id = '{$rowEstoqueFora['D009_D049_Id']}' AND D009_C004_Id = '{$rowEstoqueFora['T006_C004_Id_Estoque']}' AND D009_Quantidade_Estoque_Liquido < {$rowEstoqueFora['T006_Quantidade']}");
            if(mysql_num_rows($sqlD009) > 0){
                $estoqueForaErro++;
            }
        }

        if ($estoqueForaErro > 0) {
            $msg = ($estoqueForaErro > 1 ? "Há itens sem estoque suficiente, favor verificar" : "Existe item sem estoque suficiente, favor verificar");
            return $msg;
        }

        $D024=mysql_query("SELECT D024_Flag_Permite_ACP,T005_Flag_ACP,D024_Flag_Transporta_Diferentes_UF,(T005_Valor_Total_IPI+T005_Valor_Total_ICMS+T005_Valor_ICMS_Substituicao_Retencao) as tributos FROM T005 LEFT JOIN D024 ON D024_Id=T005_D024_Id WHERE T005_Id='{$T005_Id}'");
        $mD024=mysql_fetch_array($D024);
        if($mD024['D024_Flag_Permite_ACP']=='N' && $mD024['T005_Flag_ACP']=='3'){
            return "Não é permitido emitir ACP para este cliente.";
        }

        if($mD024['tributos']>0 && $mD024['T005_Flag_ACP']=='3'){
            return "Não é permitido TRIBUTOS para uma ACP, favor atualizar aliquotas.";
        }

        if($g['C031']['venderEstoqueMatriz'] == 'N'){
            // VERIFICA SE EXISTEM ITENS SEM PRECO
            $D009EmpresaErrada = array();
            $cSql_T006 = mysql_query("SELECT T005_C004_Id, 
                                             D009_C004_Id,
                                             T006_Codigo_Produto, 
                                             T006_Descricao_Produto,
                                             C004_Nome_Abreviado 
                                        FROM T006 
                                   LEFT JOIN T005 ON T005_Id = T006_T005_Id
                                   LEFT JOIN D009 ON D009_Id = T006_D009_Id
                                   LEFT JOIN C004 ON C004_Id = D009_C004_Id
                                       WHERE T006_T005_Id='{$T005_Id}'");
            while ($mT006 = mysql_fetch_array($cSql_T006)) {
                if($mT006['T005_C004_Id'] != $mT006['D009_C004_Id']){
                    $D009EmpresaErrada[] = "{$mT006['T006_Codigo_Produto']} - {$mT006['T006_Descricao_Produto']} : Vinculado a empresa {$mT006['C004_Nome_Abreviado']}";
                }
            }
            
            if (!empty($D009EmpresaErrada)) {
                $mD009EmpresaErrada = implode("\n", $D009EmpresaErrada);
                return "Os itens abaixo estão vinculados a outra empresa, favor verificar:\n\n" . $mD009EmpresaErrada . "\n\n";
            }
        }
        
        $sqlPreCadastroProduto  = mysql_query("SELECT T006_Id 
                                              FROM T006
                                         LEFT JOIN D009 ON D009_Id=T006_D009_Id
                                         LEFT JOIN D049 ON D049_Id=D009_D049_Id
                                         LEFT JOIN D001 ON D001_Id=D049_D001_Id
                                             WHERE T006_T005_Id='{$T005_Id}' 
                                               AND D001_Flag_Pre_Cadastro='S';
                                     ");
        $rowsPreCadastroProduto = mysql_num_rows($sqlPreCadastroProduto);
        if ($rowsPreCadastroProduto > 0) {
            return "Existem itens em pré-cadastro, não foi possível faturar o pedido. Por favor, verifique.";
        }
        
        $T006Pre = mysql_query("SELECT T006_Id FROM T006 WHERE T006_T005_Id='{$T005_Id}' AND T006_Flag_Pre_Cadastro='S'");
        if (mysql_num_rows($T006Pre) > 0) {
            return "Existem itens em pré-cadastro. Por favor conclua o cadastro dos itens.<br />Caso já tenha sido incluído, pressione o botão 'Atualizar Alíquotas', logo em seguida 'Atualizar CFOP' e por fim 'Aplicar regras CFOP'.";
        }
        
        //PARA SETAR A EXPEDIÇÃO PADRÃO DO PEDIDO, EXISTE A VARIÁVEL GLOBAL ABAIXO QUE É CONFIGURADA DENTRO DE _GLOBAL
        $statusPedido = $g['C031']['statusInicialPedido'];
        $sql          = "SELECT T005_D024_Id,T005_Data_Entrega,T005_D022_Id,T005_Flag_Status,T005_Ordem_Compra, D006_Flag_Gera_Contas, T005_Valor_Total_Produtos, T005_Valor_Total, T005_Flag_Frete FROM T005 LEFT JOIN D006 ON D006_Id=T005_D006_Id WHERE T005_Id='{$T005_Id}'";
        $resultado    = mysql_query($sql);
        $row          = mysql_fetch_array($resultado);
        
        $sql2       = "SELECT D024_Flag_Liberado,D024_Flag_Restricao FROM D024 WHERE D024_Id='{$row['T005_D024_Id']}'";
        $resultado2 = mysql_query($sql2);
        $row2       = mysql_fetch_array($resultado2);
        
        $restricaoProduto = mysql_query("SELECT T006_Id FROM T006 WHERE T006_T005_Id='{$T005_Id}' AND (T006_D009_Id='' OR T006_D009_Id IS NULL OR T006_D009_Id=0)");
        $numRestricao     = mysql_num_rows($restricaoProduto);
        if ($numRestricao > 1) {
            return "Há itens sem cadastro. Verifique!";
        } elseif ($numRestricao > 0) {
            return "Existe item sem cadastro. Verifique!";
        }
        
        //return "SQL1 --> " . $sql . "<br /><br /><br /> SQL2 -->" . $sql2 . "qualquer coisa";
        
        /** 
         * Verifica se já foi faturado o pedido
         */
        if ($row['T005_Flag_Status'] == $statusPedido) {
            return "Este pedido já foi faturado!";
        }
        
        /**
         * Verifica se é obrigatório forma de pagamento
         */
        $faturarSemPreco = $g['C031']['permissaoFaturarSemPreco'];
        if ($row['D006_Flag_Gera_Contas'] == "R" && $faturarSemPreco == 'N' && $validaPedidoTransferencia == 'S') {
            $T090 = mysql_query("SELECT SUM(T090_Valor_Parcela) as TotalParcelas FROM T090 WHERE T090_T005_Id='{$T005_Id}'");
            if (mysql_num_rows($T090) <= 0) {
                return "É necessário preencher a forma de pagamento";
            } else {
                $resT090 = mysql_fetch_array($T090);
                if ($row['T005_Valor_Total'] != $resT090['TotalParcelas']) {
                    return "O valor total da forma de pagamento difere do valor total do pedido";
                }
            }
        }

        // Verifica se o banco vinculado aos portadores da forma de pagamento está ativo
        $T090 = mysql_query("SELECT IFNULL(D007_Flag_Ativo,0) as D007_Flag_Ativo ,
                                    IFNULL(D027_D007_Id,0) as D027_D007_Id,
                                    D027_Portador,
                                    T090_D027_Id 
                               FROM T090
                          LEFT JOIN D027 ON D027_Id=T090_D027_Id
                          LEFT JOIN D007 ON D007_Id=D027_D007_Id
                              WHERE T090_T005_Id='{$T005_Id}'");

        while($mT090=mysql_fetch_array($T090)){
            if($mT090['D027_D007_Id'] > 0 && $mT090['D007_Flag_Ativo'] != 'S'){
                return "O portador \"".$mT090['D027_Portador']."\" está com a conta bancária inativa. Por favor verifique com o setor financeiro";
            }
            if($mT090['T090_D027_Id'] == 0 || empty($mT090['T090_D027_Id'])){
                return "Existem parcelas sem portador definido"; 
            }
        }

        // VERIFICA SE EXISTEM ITENS SEM PRECO
        $cSql_T006 = mysql_query("SELECT T006_Id, T006_Codigo_Produto, T006_Descricao_Produto FROM T006 WHERE T006_T005_Id='{$T005_Id}' AND T006_Valor_Preco_Unitario=0");
        while ($mT006 = mysql_fetch_array($cSql_T006)) {
            $mSem_Preco[] = "{$mT006['T006_Codigo_Produto']} - {$mT006['T006_Descricao_Produto']}";
        }
        
        if (mysql_num_rows($cSql_T006) > 0) {
            $Sem_Preco = implode("<br>", $mSem_Preco);
            return "OS ITENS ABAIXO ESTAO SEM PREÇO:" . $Sem_Preco;
        }

        /**
         *  Verifica se foi preenchido a transportadora
         */
        $usarTransportadora = $g['C031']['usarTransportadoraPedido'];
        if ((empty($row['T005_D022_Id']) || $row['T005_D022_Id'] == 0 || $row['T005_D022_Id'] == null) AND $usarTransportadora == 'S' AND $row['T005_Flag_Frete'] != 9 && $validaPedidoTransferencia == 'S') {
            return "Por favor preencha o campo \"TRANSPORTADORA\" no cabeçalho do pedido!";
        }
        /**
         *  Verifica se foi preenchido a data de entrega
         */
        if ($row['T005_Data_Entrega'] == '0000-00-00' AND $g['C031']['obrigatorioDataEntregaPedido'] == 'S') {
            return "Por favor preencha o campo \"DATA ENTREGA\" no cabeçalho do pedido!";
        }

        //Verifica se a empresa usa o campo Ordem de Comprar como obrigatório
        $usarOrdemCompra = $g['C031']['usarOrdemCompra'];
        if ($usarOrdemCompra == 'S') {
            if (empty($row['T005_Ordem_Compra'])) {
                return "Por favor preencha o campo \"ORDEM DE COMPRA\" no cabeçalho do pedido!";
            }
        }

        if($row['T005_Flag_Status'] == '22' && $g['C031']['modeloExpedicao'] == '2') {
            // Se utiliza modelo expedição 2, com esse status o pedido já passou pela expedição
            // E agora vai para a emissão de nota

            $separacao = mysql_query("SELECT SUM(T006_Quantidade) as totalQuantidade,
                                             SUM(T006_Quantidade_Separacao) as totalQuantidadeSeparacao
                                        FROM T006
                                       WHERE T006_T005_Id = '{$T005_Id}'");
            $mSeparacao = mysql_fetch_array($separacao);
            if($mSeparacao['totalQuantidade'] != $mSeparacao['totalQuantidadeSeparacao']){
                return 'Existem produtos que possuem quantidade separada menor que a quantidade do pedido';
            }

            $statusPedido = "3";
        }
        
        if ($statusPedido == "1") {
            $fraseStatus = "Enviou para impressão";
        }
        if ($statusPedido == "10") {
            $fraseStatus = "Enviou para conferência";
        }
        if ($statusPedido == "21") {
            $fraseStatus = "Enviou para peças";
        }
        if ($statusPedido == "3") {
            $fraseStatus = "Enviou para emissão de NF";
        }

        if($mT005['T005_Flag_Frete'] != 9 AND $g['C031']['permiteTransportarDiferentesUF'] == 'N' AND $mD024['D024_Flag_Transporta_Diferentes_UF'] == 'N'){
            $permiteTransporte = mysql_query("SELECT UFFaturamento.D018_UF as faturamento,  
                                                     UFEntrega.D018_UF as entrega,
                                                     D022_Id
                                                FROM T005
                                                LEFT JOIN D022 ON D022_Id=T005_D022_Id
                                                LEFT JOIN D018 AS UFFaturamento ON UFFaturamento.D018_Id=D022_D018_Id
                                                LEFT JOIN D148 ON D148_Id=T005_D148_Id_Entrega
                                                LEFT JOIN D018 AS UFEntrega ON UFEntrega.D018_Id=D148_D018_Id
                                                WHERE T005_Id='$T005_Id'");
            $mPermiteTransporte = mysql_fetch_array($permiteTransporte);
            if($mPermiteTransporte['faturamento'] != $mPermiteTransporte['entrega']){
                return "UF ".$mPermiteTransporte['faturamento']." da transportadora é diferente da UF ".$mPermiteTransporte['entrega']." do cliente, favor verificar.";
            }            
        }

        /*
        * Verifica se há produtos da Matriz para enviar pedido matriz
        */
        $produtosMatriz = false;
        $sqlD009 = mysql_query("SELECT * FROM T006 LEFT JOIN D009 ON D009_Id = T006_D009_Id WHERE D009_C004_Id != '{$g['empresaAtual']}' AND T006_T005_Id = '{$T005_Id}'");
        if(mysql_num_rows($sqlD009) > 0 && $g['C031']['venderEstoqueMatriz'] == 'S'){
            $produtosMatriz = true;
        }

        // Verificação dos produtos no pedido
        $retornoProdutos = '';
        $T006 = mysql_query("SELECT T006_Item, 
                                    T006_Numero_Pedido_Compra
                               FROM T006 
                              WHERE T006_T005_Id='{$T005_Id}'");

        while($mT006 = mysql_fetch_array($T006)){
            if(strlen(trim($mT006['T006_Numero_Pedido_Compra'])) > 15 ){
                $retornoProdutos .= "Num. OC do item " . $mT006['T006_Item'] . " deve possuir menos de 15 caracteres.\n\n";
            }
        }

        if(!empty($retornoProdutos)){
            return $retornoProdutos;
        }
        /**
         * Fatura o Pedido
         */
        $duplicarPedido = $g['conf']->ini('faturamentoMultiEmpresa');
        
        $pedirSegundaLiberacao = false;
        //////////////////////////////////////////////////////////////////
        
        $T090    = mysql_query("SELECT AVG(T090_Prazos_Dias) AS PrazoMedio FROM T090 WHERE T090_T005_Id='{$T005_Id}'");
        $mPrazos = mysql_fetch_array($T090);
        
        $Total_Desconto              = $this->calculaPercentualDescontoPedido($T005_Id);
        $Total_Desconto              = number_format($Total_Desconto, 2);
        $prazoMedio                  = number_format($mPrazos['PrazoMedio'], 2);
        $portador                    = $mT005['T005_D027_Id'];
        $Liberou_Credito_Portador    = $mT005['T005A_Liberou_Credito_Portador'];
        $Valor_Total_Venda           = $mT005['T005_Valor_Total_Produtos'];
        $Liberou_Credito_Valor_Total = $mT005['T005_Liberou_Credito_Valor_Total'];
        $Liberou_Credito_Desconto    = $mT005['T005_Liberou_Credito_Desconto'];
        $Liberou_Credito_Prazo_Medio = $mT005['T005_Liberou_Credito_Prazo_Medio'];
        $T005_Liberou_D024_Id        = $mT005['T005_Liberou_D024_Id'];
        // Valor total da venda liberado é diferente do valor total da venda atual?
        ///////////////////////////////////////////////////////////////////////////
        
        /*
        die("T005_Flag_Primeira_Liberacao_Credito: ".$row['T005_Flag_Primeira_Liberacao_Credito'] . 
        "prazoMedio: ".$prazoMedio. " Liberou_Credito_Prazo_Medio: ".$Liberou_Credito_Prazo_Medio . 
        "Total_Desconto: ".$Total_Desconto. " Liberou_Credito_Desconto: ".$Liberou_Credito_Desconto . 
        "Valor_Total_Venda: ".$Valor_Total_Venda. " Liberou_Credito_Valor_Total: ".$Liberou_Credito_Valor_Total 
        );
        */

        if ($row['T005_Flag_Primeira_Liberacao_Credito'] == '1' || number_format($prazoMedio, 2) != number_format($Liberou_Credito_Prazo_Medio, 2) || number_format($Total_Desconto, 2) != number_format($Liberou_Credito_Desconto, 2) || number_format($Valor_Total_Venda, 2) != number_format($Liberou_Credito_Valor_Total, 2) || $mT005['T005_D024_Id'] != $T005_Liberou_D024_Id
        || $portador != $Liberou_Credito_Portador) {
            $pedirSegundaLiberacao = true;
        }

        if(!empty($g['C031']['prazoSolicitarAnaliseFaturar'])){
            $mT005['T005_Hora_Emissao'] = (empty($mT005['T005_Hora_Emissao'])) ? '00:00:00' : $mT005['T005_Hora_Emissao'];
            $dataHoraAtual = time();
            $dataEmissao = explode("-", $mT005['T005_Data_Emissao']);
            $horaEmissao = explode(":", $mT005['T005_Hora_Emissao']);
            $time2 = mktime($horaEmissao[0],$horaEmissao[1],$horaEmissao[2],$dataEmissao[1],$dataEmissao[2],$dataEmissao[0]);
            $diasDesdeEmissao = round(($dataHoraAtual - $time2) / 86400);

            if($diasDesdeEmissao > $g['C031']['prazoSolicitarAnaliseFaturar']){
                $pedirSegundaLiberacao = true;
            }
        }
        //if($row['T005_Flag_Primeira_Liberacao_Credito'] == '1' || number_format($T090['PrazoMedio'],0) != $row['T005_Prazo_Medio_Orcamento']){
        //  $pedirSegundaLiberacao = true;
        //}
        mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Faturou Pedido','')");
        $creditoLiberado = $this->verificaCreditoLiberado($T005_Id);

        //Verifica se os produtos serão transportados por outra empresa
        $sqlPedidos = mysql_query("SELECT T006_Id FROM T006 LEFT JOIN T005 ON T005_Id=T006_T005_Id WHERE IF(IFNULL(T006_C004_Id_Estoque,0) > 0, T006_C004_Id_Estoque, T005_C004_Id) = T005_C004_Id AND T005_Id = '{$T005_Id}' GROUP BY T006_C004_Id_Estoque");
        if(mysql_num_rows($sqlPedidos) <= 0){
            $statusPedido = '3';
        }

        if($g['C031']['modeloExpedicao'] == '2'){
            $pedirSegundaLiberacao = true;
        }

        if ($mT005['T005_Flag_ACP'] != 4 && $creditoLiberado == false && $pedirSegundaLiberacao == true) {
            
            if($mT005['T005_Flag_Status'] == '22'){
                mysql_query("UPDATE T005 SET T005_Flag_Marcado='N', T005_Flag_Status=6, T005_Flag_Separacao_Liberar='N', T005_Flag_Separacao='N', T005_Flag_Almoxarifado='S', T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1), T005_Flag_Notificacao_Financeiro = '' WHERE T005_Id='{$T005_Id}'");
            }else{
                mysql_query("UPDATE T005 SET T005_Flag_Marcado='N', T005_Flag_Status=6, T005_Flag_Separacao_Liberar='S', T005_Flag_Separacao='S', T005_Flag_Almoxarifado='N', T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1), T005_Flag_Notificacao_Financeiro = '' WHERE T005_Id='{$T005_Id}'");
            }

            //Insere registros no histórico do pedido (T178)
            $T005Msg=mysql_query("SELECT T005_Motivo_Pedido_Liberacao from T005 WHERE T005_Id='{$T005_Id}'");
            $mT005Msg = mysql_fetch_array($T005Msg);
            $mT005Msg['T005_Motivo_Pedido_Liberacao'] = mysql_real_escape_string($mT005Msg['T005_Motivo_Pedido_Liberacao']);
            mysql_query("INSERT INTO T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'{$mT005Msg['T005_Motivo_Pedido_Liberacao']}','')");

            try {
                require_once('bibliotecas/classes/Webhook.php');
                Webhook::publishForCompany((int) $mT005['T005_C004_Id'], 'financial_analysis.requested', (int) $T005_Id);
            } catch (\Throwable $exception) {
                // Webhook is auxiliary and must not cancel invoicing.
            }

            $mensagem       = "ATENÇÂO!!!<br />O cliente possui restrições! Pedido foi enviado para análise de crédito.";
            $duplicarPedido = false;
            
        } else {
            mysql_query("UPDATE T005 
                SET T005_Flag_Marcado='N', 
                    T005_Flag_Status='{$statusPedido}', 
                    T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1), 
                    T005_Flag_Separacao='S', 
                    T005_Flag_Separacao_Liberar='N',
                    T005_Flag_Notificacao_Financeiro = '',
                    T005_Data_Hora_Almoxarifado = NOW()  
                WHERE T005_Id='{$T005_Id}'
            ");

            //Insere registros no histórico do pedido (T178)
            mysql_query("INSERT INTO T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'{$fraseStatus}','')");
            if ($statusPedido == 3)
                $mensagem = "Pedido enviado para emissão de NF";
            else
                $mensagem = "Pedido enviado para expedição";

            $retorno = $this->gerarPedidoTransporte($T005_Id);
            if($retorno !== true){
                return $retorno;
            }
        }        

        // T185
        include('prog/outros/distribuicaoItemPedidoEstoque.php');
        
        if ($duplicarPedido && $produtosMatriz){
            $duplica = $this->enviarPedidoMatriz($T005_Id);
            if ($duplica !== true) {
                $mensagem = $duplica;
            }

            //Insere os registros da separação do pedido (A SEPARAÇÃO REAL OCORRE APENAS NA MATRIZ)
            $sqlT006 = mysql_query("SELECT T006_Id, T006_T005_Id, T006_Quantidade, T066_Id, T066_D004_Id FROM T006 LEFT JOIN D009 ON D009_Id=T006_D009_Id LEFT JOIN T066 ON T066_D009_Id=D009_Id WHERE T006_T005_Id = '{$T005_Id}' GROUP BY T006_Id");
            while($T006 = mysql_fetch_array($sqlT006)){
                mysql_query("INSERT INTO T238 (T238_T006_Id, T238_T066_Id, T238_Quantidade_Separacao, T238_T005_Id, T238_D004_Id) VALUES ('{$T006['T006_Id']}', '{$T006['T066_Id']}', '{$T006['T006_Quantidade']}', '{$T006['T006_T005_Id']}', '{$T006['T066_D004_Id']}')");
                log("INSERT INTO T238 (T238_T006_Id, T238_T066_Id, T238_Quantidade_Separacao, T238_T005_Id, T238_D004_Id) VALUES ('{$T006['T006_Id']}', '{$T006['T066_Id']}', '{$T006['T006_Quantidade']}', '{$T006['T006_T005_Id']}', '{$T006['T066_D004_Id']}')");
            }
        }

        // Atualiza notificação de análise de crédito
        //require_once('bibliotecas/classes/NOTIFICACOES.php');
        //$NOTIFICACOES= new NOTIFICACOES();
        //$NOTIFICACOES->liberarCredito();
        //$NOTIFICACOES->pedidoRetornoAlmoxarifado();
        //$NOTIFICACOES->pedidoCreditoLiberado();
        //$NOTIFICACOES->pedidoCreditoNaoLiberado();

        return true;
    }
    
    /**
     * enviarPedidoMatriz
     *
     * @param String $T005_Id
     * @return String/bool
     */     
    public function enviarPedidoMatriz($T005_Id)
    {
        global $g;
        global $Perfil_Usuario, $mensagem;
        
        if ($g['conf']->ini('faturamentoMultiEmpresaIdMatriz') > 0) {
            $matriz = $g['conf']->ini('faturamentoMultiEmpresaIdMatriz');
        } else {
            return 'não foi definida a empresa matriz na flag faturamentoMultiEmpresaIdMatriz, favor verificar (ARQUIVO GLOBAL)';
        }        


        //-- Verifica se o pedido já foi duplicado
        $T005 = mysql_query("SELECT T005_Id, 
                                    C004_Nome_Abreviado
                               FROM T005
                          LEFT JOIN C004 ON C004_Id=T005_C004_Id_Estoque 
                              WHERE T005_T005_Id_Origem='{$T005_Id}'");
        $mT005 = mysql_fetch_array($T005);
        if(mysql_num_rows($T005) > 0 ){
            return "<font color='blue'><b>ATENÇÃO</b><br/></font>O Pedido Estoque <b>" . $mT005['T005_Id'] . "</b> já foi gerado na empresa <b>" . $mT005['C004_Nome_Abreviado'] . "</b>, no faturamento anterior. <br/><font color='red'>Caso haja alterações nesse pedido necessário alterar no Pedido Estoque.</font>";
        }

        
        $D006=mysql_query("SELECT D006_Flag_Venda_Compra_Outros FROM T005 LEFT JOIN D006 ON D006_Id=T005_D006_Id WHERE T005_Id='{$T005_Id}'");
        $mD006=mysql_fetch_array($D006);
        if($mD006['D006_Flag_Venda_Compra_Outros']!= 'V'){
            return true;
        }
        mysql_query("START TRANSACTION");

        //Altera status do pedido da filial para emitir NFE
        $statusPedido = 3;
        mysql_query("UPDATE T005 SET T005_Flag_Status='{$statusPedido}', T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1), T005_Flag_Separacao='S', T005_Flag_Separacao_Liberar='N' WHERE T005_Id='{$T005_Id}'");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return "Erro ao atualizar status do pedido";
        }
        //Insere registros no histórico do pedido (T178)
        $fraseStatus = "Aguardando Emissão de NF";
        mysql_query("INSERT INTO T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'{$fraseStatus}','')");
         if (mysql_error()) {
            mysql_query("ROLLBACK");
            return "Erro ao atualizar status do pedido";
        }
        
        // efetuar a cópia do pedido para a matriz                                                                                                              
        mysql_query(gRetornaSqlDuplicacao('T005', $T005_Id));
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return "Erro ao duplicar pedido";
        }
        $T005_Id_Duplicado = $g['mysqlLastId'];
        
        $T005_orig = mysql_query("SELECT * FROM T005 LEFT JOIN D024 ON D024_Id=T005_D024_Id LEFT JOIN D020 ON D020_Id=D024_D020_Id LEFT JOIN D018 ON D018_Id=D024_D018_Id WHERE T005_Id='{$T005_Id}'");
        $T005_orig = mysql_fetch_array($T005_orig);
        $obsFilial = <<<EOT
        Pedido filial: {$T005_Id}<br />
        Cliente: {$T005_orig['D024_Nome_Empresa']}<br />
        Fantasia: {$T005_orig['D024_Nome_Fantasia']}<br />
        CNPJ: {$T005_orig['D024_Cnpj']}<br />
        Cidade/UF: {$T005_orig['D020_Nome_Cidade']}/{$T005_orig['D018_UF']}<br />
        CEP/Bairro: {$T005_orig['D024_Cep']}/{$T005_orig['D024_Bairro']}<br />
        Obs almoxarifado: {$T005_orig['T005_Observacao_1']}
EOT;
        $obsFilial = str_replace("'", "", $obsFilial);

        $C004=mysql_query("SELECT * from C004 WHERE C004_Id='{$T005_orig['T005_C004_Id']}'");
        $mC004 = mysql_fetch_array($C004);
        $D024_Id_Atual = $mC004['C004_D024_Id'];
        
        mysql_query("UPDATE T005 SET T005_C004_Id='{$matriz}',T005_Flag_Status=1,T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1),T005_Flag_Almoxarifado='S',T005_Observacao_1='{$obsFilial}',T005_D024_Id='{$D024_Id_Atual}', T005_T005_Id_Origem='{$T005_Id}' WHERE T005_Id='{$T005_Id_Duplicado}'");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return "Erro ao editar pedido (duplicado)";
        }
        
        $result = mysql_query("select T006_Id, D009_D049_Id, D001_C008_Id, D009_GP_Indice_Venda from T006 
                                                left join D009 on D009_Id=T006_D009_Id 
                                                LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                                LEFT JOIN D001 ON D001_Id=D049_D001_Id
                                                where T006_T005_Id='{$T005_Id}'");
        while ($linha = mysql_fetch_array($result)) {
            mysql_query(gRetornaSqlDuplicacao('T006', $linha['T006_Id']));
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return "Erro ao duplicar item do pedido";
            }
            
            $T006_Id_Duplicado = $g['mysqlLastId'];
            // busca o D009 do item na matriz (D009_Valor_Custo_Compra trocado por D009_Valor_Custo_Unitario)
            $retorno           = mysql_query("SELECT D009_Id,D009_Quantidade_Estoque_Liquido,D009_Valor_Custo_Unitario,D009_Valor_Custo_Medio_Unitario FROM D009 WHERE D009_D049_Id='{$linha['D009_D049_Id']}' AND D009_C004_Id=$matriz");
            $ret               = mysql_fetch_array($retorno);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return "Erro ao buscar D009_Id da matriz";
            }
            if (mysql_num_rows($retorno) != 1) {
                mysql_query("ROLLBACK");
                return "Há itens não cadastrado na Matriz, verifique";
            }
            if ($ret['D009_Valor_Custo_Medio_Unitario'] <= 0) {
                mysql_query("ROLLBACK");
                return "Há itens com custo zero na Matriz, verifique";
            }

            $C004              = mysql_query("SELECT C004_GP_Indice_Pneu FROM C004 WHERE C004_Id='{$g['empresaAtual']}'");
            $mC004             = mysql_fetch_array($C004);

            $retorno2=mysql_query("select D009_GP_Indice_Venda from D009 where D009_D049_Id='{$linha['D009_D049_Id']}' and D009_C004_Id='{$g['empresaAtual']}'");
            $ret2=mysql_fetch_array($retorno2);
            
            $precoReduzido = $ret['D009_Valor_Custo_Medio_Unitario'] * $mC004['C004_GP_Indice_Pneu'];
            mysql_query("UPDATE T006 SET T006_T005_Id=$T005_Id_Duplicado,T006_Valor_Custo_Unitario={$ret['D009_Valor_Custo_Unitario']},T006_Quantidade_Estoque={$ret['D009_Quantidade_Estoque_Liquido']},T006_D009_Id='{$ret['D009_Id']}', T006_Quantidade_Separacao=0, T006_C004_Id=$matriz, T006_Valor_Preco_Sem_Desconto_Unitario=$precoReduzido WHERE T006_Id=$T006_Id_Duplicado");
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return "Erro ao editar item do pedido (duplicado)";
            }

            if($linha['D001_C008_Id'] == 7){

                // C008_Id=7 PNEU
                $indice=1.8055;
                if ($g['C004']['C004_GP_Indice_Pneu']>0){
                    $indice=$g['C004']['C004_GP_Indice_Pneu'] + $ret2['D009_GP_Indice_Venda'];
                }
                $precoReduzido = ($ret['D009_Valor_Custo_Medio_Unitario'] * $indice);
                mysql_query("update T006 set T006_Valor_Preco_Bruto='$precoReduzido',
                                        T006_Percentual_Desconto_Bruto=if(T006_Valor_Preco_Original_Bruto>0,(1-(T006_Valor_Preco_Bruto/T006_Valor_Preco_Original_Bruto))*100,0),
                                        T006_Valor_Desconto_Unitario_Bruto=if(T006_Valor_Preco_Original_Bruto>0,(T006_Valor_Preco_Original_Bruto*($precoReduzido/100)),0)
                              where T006_Id='$T006_Id_Duplicado'");
            } else if ($linha['D001_C008_Id'] == 8){

                // C008_Id=8 RODA
                $indice=1.7499;
                if ($g['C004']['C004_GP_Indice_Roda']>0){
                    $indice=$g['C004']['C004_GP_Indice_Roda'] + $ret2['D009_GP_Indice_Venda'];
                }
                $precoReduzido = ($ret['D009_Valor_Custo_Medio_Unitario'] * $indice);
                mysql_query("update T006 set T006_Valor_Preco_Bruto='$precoReduzido',
                                        T006_Percentual_Desconto_Bruto=if(T006_Valor_Preco_Original_Bruto>0,(1-(T006_Valor_Preco_Bruto/T006_Valor_Preco_Original_Bruto))*100,0),
                                        T006_Valor_Desconto_Unitario_Bruto=if(T006_Valor_Preco_Original_Bruto>0,(T006_Valor_Preco_Original_Bruto*($precoReduzido/100)),0)
                              where T006_Id='$T006_Id_Duplicado'");
            }
            
            //Duplicação dos Agregados do Produto
            $sqlT204 = mysql_query("SELECT * FROM T204 WHERE T204_T006_Id = '{$linha['T006_Id']}'");
            while ($T204 = mysql_fetch_array($sqlT204)) {
                mysql_query(gRetornaSqlDuplicacao('T204', $T204['T204_Id']));
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return "Erro ao duplicar os agregados do item do pedido";
                }
                
                $T204_Id_Duplicado = $g['mysqlLastId'];
                mysql_query("UPDATE T204 SET T204_T006_Id = '{$T006_Id_Duplicado}' WHERE T204_Id = '{$T204_Id_Duplicado}'");
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return "Erro ao editar agregado do item (duplicado)";
                }
            }
        }
        // apenas para chamar a trigger para corrigir os totais deste pedido (aproveitando pra setar flag almoxarifado)
        mysql_query("UPDATE T005 SET T005_C004_Id=T005_C004_Id,T005_Flag_Almoxarifado='S', T005_T005_Id_Origem='{$T005_Id_Duplicado}' WHERE T005_Id=$T005_Id");
        $T005_d  = mysql_query("SELECT T005_Valor_Total FROM T005 WHERE T005_Id=$T005_Id_Duplicado");
        $mT005_d = mysql_fetch_array($T005_d);
        
        // insere parcelas para pagamento da filial (dinheiro a vista)
        $cClausula = "INSERT INTO T090 (";
        $cClausula .= "T090_T005_Id,";
        $cClausula .= "T090_D027_Id,";
        $cClausula .= "T090_Numero_Parcela,";
        $cClausula .= "T090_Prazos_Dias,";
        $cClausula .= "T090_Data_Vencimento,";
        $cClausula .= "T090_Valor_Parcela";
        $cClausula .= ") VALUES (";
        $cClausula .= "'$T005_Id_Duplicado',";
        $cClausula .= "'3',";
        $cClausula .= "'1',";
        $cClausula .= "'1',";
        $cClausula .= "CURDATE()+1,";
        $cClausula .= "'{$mT005_d['T005_Valor_Total']}'";
        $cClausula .= ")";
        mysql_query($cClausula);
        
        //-- INICIO VALIDAÇÃO CRIAÇÃO PEDIDO DUPLICADO
        $T005Duplicado = mysql_query("SELECT T005_Id,
                                             C004_Nome_Abreviado
                                        FROM T005 
                                    LEFT JOIN C004 ON C004_Id=T005_C004_Id
                                       WHERE T005_C004_Id = '{$matriz}'
                                         AND T005_id='{$T005_Id_Duplicado}' 
                                    ");
        $mT005Duplicado = mysql_fetch_array($T005Duplicado);
        if(mysql_num_rows($T005Duplicado) <= 0){ 
            mysql_query("ROLLBACK");
            return "<font color='red'><b>ATENÇÃO! Ocorreu um erro ao gerar pedido estoque na empresa: <br/><br/></font>" . $mT005Duplicado['C004_Nome_Abreviado'] . " </b><br/><br/><font color='red'>Favor abrir um chamado enviando esta tela.</font>";
        }

        // -- Valida se os itens foram inseridos no pedido
        $T006Duplicado = mysql_query("SELECT T006_Codigo_Produto, 
                                             T006_Quantidade 
                                        FROM T006 
                                       WHERE T006_T005_Id='{$T005_Id_Duplicado}'
                                    ");

        if(mysql_num_rows($T006Duplicado) <=0 ){
            mysql_query("ROLLBACK");
            return "<font color='red'><b>ATENÇÃO! Ocorreu um erro ao gerar pedido estoque na empresa: <br/><br/></font>" . $mT005Duplicado['C004_Nome_Abreviado'] . " </b><br/><br/>Não foi possível inserir os itens no pedido<br/><br/><font color='red'>Favor abrir um chamado enviando esta tela.</font>";
        }else{
            $T006Original = mysql_query("SELECT T006_Codigo_Produto, 
                                                T006_Quantidade 
                                           FROM T006 
                                          WHERE T006_T005_Id='{$T005_Id}' 
                                    ");
            $errosProduto = "";
            while($mT006Original = mysql_fetch_array($T006Original) and $mT006Duplicado = mysql_fetch_array($T006Duplicado)){

                if($mT006Original['T006_Codigo_Produto'] != $mT006Duplicado['T006_Codigo_Produto']){
                    mysql_query("ROLLBACK");
                    return "<font color='red'><b>ATENÇÃO! Ocorreu um erro ao gerar pedido estoque na empresa: <br/><br/></font>" . $mT005Duplicado['C004_Nome_Abreviado'] . " </b><br/><br/>Código dos itens do Pedido Estoque diverge do pedido original<br/><br/><font color='red'>Favor abrir um chamado enviando esta tela.</font>";

                }

                if($mT006Original['T006_Quantidade'] != $mT006Duplicado['T006_Quantidade']){
                    mysql_query("ROLLBACK");
                    return "<font color='red'><b>ATENÇÃO! Ocorreu um erro ao gerar pedido estoque na empresa: <br/><br/></font>" . $mT005Duplicado['C004_Nome_Abreviado'] . " </b><br/><br/>Quantidade dos itens do Pedido Estoque diverge da do pedido original<br/><br/><font color='red'>Favor abrir um chamado enviando esta tela.</font>";
                }           
            }

        }

        // -- Valida se foram inseridas as parcelas
        $T090Duplicado = mysql_query("SELECT T090_Id
                                        FROM T090 
                                       WHERE T090_T005_Id='{$T005_Id_Duplicado}'
                                    ");
        if(mysql_num_rows($T090Duplicado) <=0 ){
            mysql_query("ROLLBACK");
            return "Pedido duplicado não gerado, não foi possível inserir as parcelas no pedido.";
        }

        //Insere registros no histórico do pedido (T178)
        $fraseStatus = "Pedido Gerado (via filial)";
        mysql_query("INSERT INTO T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id_Duplicado},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'{$fraseStatus}','')");
        mysql_query("COMMIT");
        $retorno = $this->ven002atualizarCFOP($T005_Id_Duplicado);
        if ($retorno !== true) {
            return "Pedido Matriz: " . $retorno;
        }
        //mysql_query("COMMIT");                        
        
        // T185
        $T005_Id = $T005_Id_Duplicado;
        include('prog/outros/distribuicaoItemPedidoEstoque.php');
        
        return "Pedido Estoque " . $T005_Id_Duplicado . " gerado na empresa " . $mT005Duplicado['C004_Nome_Abreviado'] . ".";   
    }
    
    /**
     * faturarEmergencia
     *
     * @param String $T005_Id
     * @param Form $form
     * @return true
     */     
    public function faturarEmergencia($T005_Id, $form)
    {
        $Observacao                = $form->campoValorEnviado('T005_Observacao_Fatura_Emergencia');
        $T005_Liberacao_Credito_Id = $form->campoValorEnviado('T005_Liberacao_Credito_Id');
        
        global $g, $Perfil_Usuario;
        
        mysql_query("UPDATE T005 SET T005_Flag_Status=3,T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1),T005_Liberacao_Credito_Id='{$T005_Liberacao_Credito_Id}',T005_Observacao_Fatura_Emergencia='{$Observacao}' WHERE T005_Id='{$T005_Id}'");
        
        //Insere registros no histórico do pedido (T178)
        mysql_query("INSERT INTO T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Aguardando Emissão NF','')");
       
        return true;   
    }
    
    /**
     * atualizarParcelas
     *
     * @param Form $form
     * @return String/bool
     */    
    public function atualizarParcelas($form)
    {
        global $g;
        
        $T005_Id                = $form->campoValorEnviado('T005_Id');
        $Total_Parcelas         = $form->campoValorEnviado('Total_Parcelas');
        $IntervalorDias         = $form->campoValorEnviado('IntervalorDias');
        $diaFixo                = $form->campoValorEnviado('diaFixo');
        $primeiroVencimentoNum  = $form->campoValorEnviado('primeiroVencimentoNum');
        $primeiroVencimentoData = $form->campoValorEnviado('primeiroVencimentoData');
        $numeroParcelas         = $form->campoValorEnviado('Total_Parcelas');
        $D027_Id                = $form->campoValorEnviado('Portador_D027_Id');
        $primeiroVencimentoTipo = $form->campoValorEnviado('selecionaPrimeiroVencimento');
        $diasVctoTipo           = $form->campoValorEnviado('selecionaDias');
        $campoUsarFreteParcela  = strtoupper($form->campoValorEnviado('usarFreteParcela'));
        $campoUsarSTParcela     = strtoupper($form->campoValorEnviado('usarSTParcela'));
        $campoUsarIPIParcela    = strtoupper($form->campoValorEnviado('usarIPIParcela'));
        
        if (($primeiroVencimentoData != "") || (!empty($primeiroVencimentoData))) {
            $primeiroVencmentoValor = $primeiroVencimentoData;
        } else if (($primeiroVencimentoNum != "") || (!empty($primeiroVencimentoNum))) {
            $primeiroVencmentoValor = $primeiroVencimentoNum;
        }else{
            return "Preencha o campo 1º Vencimento.";
        }
        
        if (($IntervalorDias != "") || (!empty($IntervalorDias))) {
            $diasVctoValor = $IntervalorDias;
        } else if (($diaFixo != "") || (!empty($diaFixo))) {
            $diasVctoValor = $diaFixo;
        }
        if ($diasVctoTipo == "I") {
            $diasVctoTipo = "Intervalo";
        } else if ($diasVctoTipo == "F") {
            $diasVctoTipo = "Dia Fixo";
        }
        
        $cSql_T005 = mysql_query("SELECT * FROM T005 WHERE T005_Id='{$T005_Id}'");
        $mT005     = mysql_fetch_array($cSql_T005);
        
        // se for "a combinar" remove as parcelas que ja existem
        if ($numeroParcelas == '0') {
            mysql_query("DELETE T090 FROM T090 WHERE T090_T005_Id='{$T005_Id}'");
            if (mysql_error()) {
                return mysql_error();
            } else {
                return true;
            }
        }
        
        $cSql_T090 = mysql_query("SELECT * FROM T090 LEFT JOIN D027 ON D027_Id=T090_D027_Id WHERE T090_T005_Id='{$T005_Id}'");
        while ($mT090 = mysql_fetch_array($cSql_T090)) {
            $mPrazoAntigo[$mT090['T090_Numero_Parcela']]    = $mT090['T090_Prazos_Dias'];
            $mPortadorAntigo[$mT090['T090_Numero_Parcela']] = $mT090['T090_D027_Id'];
        }
        
        mysql_query("DELETE T090 FROM T090 WHERE T090_T005_Id='{$T005_Id}'");
        
        
        $Valor_Total_IPI       = $mT005['T005_Valor_Total_IPI'];
        $Valor_Total_Orcamento = $mT005['T005_Valor_Total'];

        //Global descontinuada 06/01/2015
        //$usarFretePrimeiraParcela = $g['C031']['usarFretePrimeiraParcela'];
        if ($campoUsarFreteParcela == "ON") {
            $Valor_Total_Orcamento = ($Valor_Total_Orcamento - $mT005['T005_Valor_Frete']);
        }
        
        if ($campoUsarSTParcela == "ON") {
            $Valor_Total_Orcamento = ($Valor_Total_Orcamento - $mT005['T005_Valor_ICMS_Substituicao_Retencao']);
        }
        if ($campoUsarIPIParcela == "ON") {
            $Valor_Total_Orcamento = ($Valor_Total_Orcamento - $mT005['T005_Valor_Total_IPI']);
        }
        
        if ($Total_Parcelas > 0) {
            $Valor_Titulo_Parcela = ($Valor_Total_Orcamento - $Valor_Total_IPI) / $Total_Parcelas;
            //$Valor_Total_Titulo = ( ( round($Valor_Titulo_Parcela,2) * $Total_Parcelas ) + $Valor_Total_IPI );
            
            // verificar se deve adicionar juro na parcela 
            $C004              = mysql_query("SELECT * FROM C004 WHERE C004_Id='{$g['empresaAtual']}'");
            $mC004             = mysql_fetch_array($C004);
            $tipoJuros         = $mC004['C004_Tipo_Juros'];
            $taxaJuros         = $mC004['C004_Taxa_Juros'];
            $taxaJurosParcela  = 0.00;
            $valorJurosParcela = 0.00;
            $parcelasIsentas   = $mC004['C004_Parcelas_Isentas_Juros'];
            if ($Total_Parcelas > $parcelasIsentas) {
                $taxaJurosParcela = $taxaJuros;
                if ($taxaJuros > 0.00) {
                    if ($tipoJuros == 'S')
                        $Valor_Titulo_Parcela = gCorrigeNumeroInverte(gJurosSimples($Valor_Total_Orcamento, $taxaJuros, $Total_Parcelas));
                    else if ($tipoJuros == 'C')
                        $Valor_Titulo_Parcela = gCorrigeNumeroInverte(gJurosCompostos($Valor_Total_Orcamento, $taxaJuros, $Total_Parcelas));
                    $valorJurosParcela = $Valor_Titulo_Parcela - ($Valor_Total_Orcamento / $Total_Parcelas);
                } else {
                    $Valor_Titulo_Parcela = ($Valor_Total_Orcamento) / $Total_Parcelas;
                }
            } else {
                $Valor_Titulo_Parcela = ($Valor_Total_Orcamento) / $Total_Parcelas;
            }
            
            $Valor_Total_Titulo         = ((round($Valor_Titulo_Parcela, 2) * $Total_Parcelas));
            $Valor_Total_Titulo         = round($Valor_Total_Titulo, 2);
            $Diferenca_Primeira_Parcela = 0; // criado pra evitar erro de undefined no for
            if (($Valor_Total_Titulo != $Valor_Total_Orcamento) && ($Total_Parcelas <= $parcelasIsentas)) {
                $Diferenca_Primeira_Parcela = $Valor_Total_Orcamento - $Valor_Total_Titulo;
            }
        }
        // se foi definido um intervalo de dias
        if ($IntervalorDias > 0) {
            // variavel para guardar a diferença de dias entre o intervalo de dias e o dia da primeira parcela  
            $acrescimo = 0;
            for ($x = 1; $x <= $Total_Parcelas; $x++) {
                if ($x == 1) {
                    //$Valor_Titulo = ($Valor_Titulo_Parcela + $Valor_Total_IPI) + $Diferenca_Primeira_Parcela;
                    $Valor_Titulo = $Valor_Titulo_Parcela + $Diferenca_Primeira_Parcela;
                } else {
                    $Valor_Titulo = $Valor_Titulo_Parcela;
                }
                if ($D027_Id > 0) {
                    $mPortadorAntigo[$x] = $D027_Id;
                }
                
                // se for a primeira parcela
                if ($x == 1) {
                    // se o primeiro vencimento foi preenchido em dias, atribui ele
                    if (trim($primeiroVencimentoNum) != "") {
                        $mPrazoAntigo[$x] = $primeiroVencimentoNum;
                        $acrescimo        = $primeiroVencimentoNum;
                        // se o primeiro vencimento foi preenchido com uma data, transforma em intervalo e atribui
                    } else if (trim($primeiroVencimentoData) != "") {
                        $date1            = new \DateTime(date("Y-m-d"));
                        $date2            = new \DateTime(gCorrigeDataInverte($primeiroVencimentoData));
                        $interval         = $date1->diff($date2);
                        $mPrazoAntigo[$x] = $interval->format('%R%a');
                        $acrescimo        = $interval->format('%R%a');
                    }
                } else {
                    $mPrazoAntigo[$x] = (($IntervalorDias * ($x - 1)) + $acrescimo);
                }
                
                if ($x == 1 && $campoUsarFreteParcela == "ON") {
                    $Valor_Titulo += $mT005['T005_Valor_Frete'];
                }                
                if ($x == 1 && $campoUsarSTParcela == "ON") {
                    $Valor_Titulo += $mT005['T005_Valor_ICMS_Substituicao_Retencao'];
                }
                if ($x == 1 && $campoUsarIPIParcela == "ON") {
                    $Valor_Titulo += $mT005['T005_Valor_Total_IPI'];
                }
                
                $cClausula = "INSERT INTO T090 (";
                $cClausula .= "T090_T005_Id,";
                $cClausula .= "T090_D027_Id,";
                $cClausula .= "T090_Numero_Parcela,";
                $cClausula .= "T090_Prazos_Dias,";
                $cClausula .= "T090_Data_Vencimento,";
                $cClausula .= "T090_Taxa_Juros,";
                $cClausula .= "T090_Valor_Juros,";
                $cClausula .= "T090_Valor_Parcela";
                $cClausula .= ") VALUES (";
                $cClausula .= "'$T005_Id',";
                $cClausula .= "'$mPortadorAntigo[$x]',";
                $cClausula .= "'$x',";
                $cClausula .= "'$mPrazoAntigo[$x]',";
                $cClausula .= "DATE_ADD(current_date(),INTERVAL $mPrazoAntigo[$x] DAY),";
                $cClausula .= "'$taxaJurosParcela',";
                $cClausula .= "'$valorJurosParcela',";
                $cClausula .= "'$Valor_Titulo'";
                $cClausula .= ")";
                log("Pagamento pedido: ".$cClausula . '<br />');
                mysql_query($cClausula);
                
                if (mysql_error()) {
                    return mysql_error();
                }
            }
            // ou se foi colocado um dia fixo   
        } else if ($diaFixo > 0) {
            if ($diaFixo > 31)
                return "Por favor, insira um dia válido";
            $mes = date("m");
            $ano = date("Y");
            
            $diaFixoOriginal = $diaFixo;
            
            $validou = checkdate($mes, $diaFixo, $ano);
            while (!$validou) {
                $diaFixo--;
                $validou = checkdate($mes, $diaFixo, $ano);
            }
            $primeiraData = "$ano-$mes-$diaFixo";
            
            for ($x = 1; $x <= $Total_Parcelas; $x++) {
                if ($x == 1) {
                    //$Valor_Titulo = ($Valor_Titulo_Parcela + $Valor_Total_IPI) + $Diferenca_Primeira_Parcela;
                    $Valor_Titulo = $Valor_Titulo_Parcela + $Diferenca_Primeira_Parcela;
                } else {
                    $Valor_Titulo = $Valor_Titulo_Parcela;
                }
                if ($D027_Id > 0) {
                    $mPortadorAntigo[$x] = $D027_Id;
                }
                
                $dataVencimento = "0";
                $dataPrazoDias  = date("Y-m-d");
                $numMeses       = $x;
                // se for a primeira parcela
                if ($x == 1) {
                    // se o primeiro vencimento foi preenchido em dias, soma esses dias a data atual
                    if (trim($primeiroVencimentoNum) != "") {
                        $date           = strtotime("+$primeiroVencimentoNum day", strtotime(date("Y-m-d")));
                        $dataVencimento = date("Y-m-d", $date);
                        // se o primeiro vencimento foi preenchido com uma data, atribui
                    } else if (trim($primeiroVencimentoData) != "") {
                        $dataVencimento = gCorrigeDataInverte($primeiroVencimentoData);
                    }
                    $dataPrazoDias = $dataVencimento;
                    // looping feito para quando o dia fixo é menor do que o intervalo dias da primeira parcela e fica no mesmo mes
                    $dataAux       = date("Y-m-d", strtotime("+1 month", strtotime($primeiraData)));
                    $mes1          = date("m", strtotime($dataAux));
                    $mes2          = date("m", strtotime($dataPrazoDias));
                    while ((strtotime($dataPrazoDias) > strtotime($dataAux)) || ($mes1 == $mes2)) {
                        $dataAux      = date("Y-m-d", strtotime("+1 month", strtotime($dataAux)));
                        $mes1         = date("m", strtotime($dataAux));
                        $primeiraData = date("Y-m-d", strtotime("+1 month", strtotime($primeiraData)));
                    }
                    $numMeses = "0";
                } else {
                    $dataVencimento = $primeiraData;
                    $numMeses       = $x - 1;
                    // calcula a data de vencimento para depois
                    // poder calcular os dias até o vencimento da parcela (T090_Prazos_Dias)
                    $tempo          = $numMeses * 30;
                    $tempo          = "+$tempo days";
                    $date           = strtotime($tempo, strtotime($dataVencimento));
                    
                    $diaFixo = $diaFixoOriginal;
                    $validou = checkdate(date("m", $date), $diaFixo, date("Y", $date));
                    //echo date("Y-m-d", $date) . ' -- ' . $numMeses . ' ---- ' . date("m", $date),$diaFixo,date("Y", $date) . "<br />";
                    while (!$validou) {
                        $diaFixo--;
                        $validou = checkdate(date("m", $date), $diaFixo, date("Y", $date));
                    }
                    
                    $dataPrazoDias = date("Y-m-" . $diaFixo, $date);
                    $numMeses      = $x - 1;
                }
                
                if ($x == 1 && $campoUsarFreteParcela == "ON") {
                    $Valor_Titulo += $mT005['T005_Valor_Frete'];
                }                
                if ($x == 1 && $campoUsarSTParcela == "ON") {
                    $Valor_Titulo += $mT005['T005_Valor_ICMS_Substituicao_Retencao'];
                }
                if ($x == 1 && $campoUsarIPIParcela == "ON") {
                    $Valor_Titulo += $mT005['T005_Valor_Total_IPI'];
                }
                
                $cClausula = "INSERT INTO T090 (";
                $cClausula .= "T090_T005_Id,";
                $cClausula .= "T090_D027_Id,";
                $cClausula .= "T090_Numero_Parcela,";
                $cClausula .= "T090_Data_Vencimento,";
                $cClausula .= "T090_Prazos_Dias,";
                $cClausula .= "T090_Taxa_Juros,";
                $cClausula .= "T090_Valor_Juros,";
                $cClausula .= "T090_Valor_Parcela";
                $cClausula .= ") VALUES (";
                $cClausula .= "'$T005_Id',";
                $cClausula .= "'$mPortadorAntigo[$x]',";
                $cClausula .= "'$x',";
                $cClausula .= "'$dataPrazoDias',";
                //$cClausula.="DATE_ADD('$dataVencimento',INTERVAL $numMeses MONTH),";
                $cClausula .= "DATEDIFF('$dataPrazoDias',current_date()),";
                $cClausula .= "'$taxaJurosParcela',";
                $cClausula .= "'$valorJurosParcela',";
                $cClausula .= "'$Valor_Titulo'";
                $cClausula .= ")";
                log("Pagamento pedido 2: ".$cClausula . '<br />');
                mysql_query($cClausula);
                
                if (mysql_error()) {
                    return mysql_error();
                }
            }
        }
        $sqlT005    = <<<EOT
            UPDATE T005 SET
            T005_Pgto_Parcelas='{$Total_Parcelas}',
            T005_Pgto_Primeiro_Vcto_Tipo='{$primeiroVencimentoTipo}',
            T005_Pgto_Primeiro_Vcto_Valor='{$primeiroVencmentoValor}',
            T005_Pgto_Dias_Vcto_Tipo='{$diasVctoTipo}',
            T005_Pgto_Dias_Vcto_Valor='{$diasVctoValor}',
            T005_Pgto_Portador='{$D027_Id}',
            T005_Pgto_Data=CURRENT_TIMESTAMP(),
            T005_Pgto_C007_Id='{$g['usuarioAtual']}'
            WHERE T005_Id='{$T005_Id}'
EOT;
        $resultT005 = mysql_query($sqlT005, false);
        if (mysql_error()) {
            return mysql_error();
        }
        return true;
    }
    
    /**
     * incluirProdutoTroca
     *
     * @param Form $form
     * @return String/bool
     */    
    public function incluirProdutoTroca($form)
    {
        $T138_Quantidade           = $form->campoValorEnviado('T138_Quantidade');
        $T138_Observacao           = $form->campoValorEnviado('T138_Observacao');
        $T138_Valor_Preco_Unitario = $form->campoValorEnviado('T138_Valor_Preco_Unitario');
        $T138_Valor_Preco_Unitario = gCorrigeNumeroInverte($T138_Valor_Preco_Unitario);
        $T138_Operacao             = $form->campoValorEnviado('T138_Operacao');
        $T138_T005_Id              = $form->campoValorEnviado('T138_T005_Id');
        $T138_C004_Id              = $form->campoValorEnviado('T138_C004_Id');
        $T138_D009_Id              = $form->campoValorEnviado('T138_D009_Id');
        
        if ($T138_D009_Id <= 0) {
            return "nenhum produto foi selecionado";
        }
        
        $cClausula = " INSERT INTO T138 (";
        $cClausula .= " T138_T005_Id, ";
        $cClausula .= " T138_C004_Id, ";
        $cClausula .= " T138_D009_Id, ";
        $cClausula .= " T138_Operacao, ";
        $cClausula .= " T138_Valor_Preco_Unitario, ";
        $cClausula .= " T138_Quantidade, ";
        $cClausula .= " T138_Observacao ";
        $cClausula .= " ) VALUES ( ";
        $cClausula .= " '$T138_T005_Id', ";
        $cClausula .= " '$T138_C004_Id', ";
        $cClausula .= " '$T138_D009_Id', ";
        $cClausula .= " '$T138_Operacao', ";
        $cClausula .= " '$T138_Valor_Preco_Unitario', ";
        $cClausula .= " '$T138_Quantidade', ";
        $cClausula .= " '$T138_Observacao' ";
        $cClausula .= " ) ";
        
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "Erro: $cErro<br />$cClausula";
        } else {
            return true;
        }   
    }
    
    /**
     * incluirProduto
     *
     * @param Form $form
     * @param String $tipo
     * @return String/bool
     */
    public function incluirProduto($form, $tipo = null)
    {
        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();
        require_once('bibliotecas/classes/VEN001.php');
        $VEN001 = new VEN001();
        require_once('bibliotecas/classes/FIS003.php');
        $FIS003 = new FIS003();

        global $g, $T006_Id;
        $T006A_Usa_Transacao_Externa = $form->campoValorEnviado('T006A_Flag_Transacao_Externa') === 'S';

        $T006_D009_Id              = $form->campoValorEnviado('T006_D009_Id');
        $T006_D005_Id              = $form->campoValorEnviado('T006_D005_Id');
        $T006_D006_Id              = $form->campoValorEnviado('T006_D006_Id');
        $T006_Quantidade           = $form->campoValorEnviado('T006_Quantidade');
        $T006_Flag_Destino_Produto = $form->campoValorEnviado('T006_Flag_Destino_Produto');
        $T006_Valor_Preco_Unitario = $form->campoValorEnviado('T006_Valor_Preco_Sem_Desconto_Unitario');
        $T006_D037_Id              = $form->campoValorEnviado('T006_D037_Id');
        $T006_Descricao_Produto    = mysql_real_escape_string($form->campoValorEnviado('T006_Descricao_Produto'));
        $T006_Codigo_Produto       = $form->campoValorEnviado('T006_Codigo_Produto');
        $T006_IPV                  = $form->campoValorEnviado('T006_IPV');
        $T005_Id                   = $form->campoValorEnviado('T006_T005_Id');
        $T006_Codigo_Substituto    = $form->campoValorEnviado('T006_Codigo_Substituto');
        $T006_Numero_Pedido_Compra = $form->campoValorEnviado('T006_Numero_Pedido_Compra');
        $T006_Item_Pedido_Compra   = $form->campoValorEnviado('T006_Item_Pedido_Compra');
        $T006_Observacao           = $form->campoValorEnviado('T006_Observacao');
        $T006_Observacao_Entrega   = $form->campoValorEnviado('T006_Observacao_Entrega');
        $T006_Aliquota_IPI         = $form->campoValorEnviado('T006_Aliquota_IPI');
        $idVeiculoPedido           = $form->campoValorEnviado('idVeiculoPedido');
        $T006_Codigo_Local         = $form->campoValorEnviado('T006_Codigo_Local');

        $T006_D022_Id                 = $form->campoValorEnviado('T006_D022_Id');
        $T006_D144_Id                 = $form->campoValorEnviado('T006_D144_Id');
        $T006_T208_Id                 = $form->campoValorEnviado('T006_T208_Id');
        $T006_Hora_Entrega            = $form->campoValorEnviado('T006_Hora_Entrega');
        $T006A_Codigo_Obra            = $form->campoValorEnviado('T006A_Codigo_Obra');
        $T006A_Altura                 = $form->campoValorEnviado('T006A_Altura');
        $T006A_Largura                = $form->campoValorEnviado('T006A_Largura');
        $T006A_Comprimento            = $form->campoValorEnviado('T006A_Comprimento');
        $T006A_Resultado              = $form->campoValorEnviado('T006A_Resultado');
        $T006A_Flag_Transacao_Externa = $form->campoValorEnviado('T006A_Flag_Transacao_Externa') === 'S' ? 'S' : 'N';
        $T006_Data_Entrega            = gCorrigeDataInverte($form->campoValorEnviado('T006_Data_Entrega'));
        $T006_Percentual_Desconto     = gCorrigeNumeroInverte($form->campoValorEnviado('T006_Percentual_Desconto'));
        //$NcmProduto=$form->campoValorEnviado('NcmProduto');
        //$T006_Percentual_Desconto=0;
        $T006_Valor_Desconto_Unitario = 0;
        $T006_Id                      = '';

        $T006_Quantidade           = gCorrigeNumeroInverte($T006_Quantidade);
        $T006_Valor_Preco_Unitario = gCorrigeNumeroInverte($T006_Valor_Preco_Unitario);
        $T006A_Quantidade_Conversao = gCorrigeNumeroInverte($form->campoValorEnviado('T006A_Quantidade_Conversao'));

        $Aliquotas = $VEN001->retornaAliquotas($T006_D009_Id, false, $T005_Id);

        $D001  = mysql_query("SELECT D001_Id,
                  D082_Id,
                  D001_Codigo_Produto,
                  D049_Origem_Mercadoria,
                  D001_Flag_Tipo,
                  D049_Flag_Nacional_Importado,
                  D001_Cubagem_Unitaria,
                  D001_Flag_Pre_Cadastro,
                  T001_Id,
                  D009_Quantidade_OC_3(D009_Id,0) as D009_Quantidade_OC,
                  D009_Preco_Pauta_ST
               FROM D001
            LEFT JOIN D049 ON D049_D001_Id=D001_Id
            LEFT JOIN D009 ON D009_D049_Id=D049_Id
            LEFT JOIN D082 ON D082_Id=D049_D082_Id
            LEFT JOIN T001 ON T001_Id = D009_T001_Id
              WHERE D009_Id='$T006_D009_Id'");
        $mD001 = mysql_fetch_array($D001);

        if ($mD001['D001_Flag_Pre_Cadastro'] == 'S' && $g['C031']['permitePedidoProdutoPreCadastro'] == 'N') {
            return "Não foi possível incluir o produto! Este produto está em Pré-Cadastro.";
        }

        $T006_D001_Id        = $mD001['D001_Id'];
        $T006_Codigo_Produto = $mD001['D001_Codigo_Produto'];
        $T006_Preco_Pauta_ST = $mD001['D009_Preco_Pauta_ST'];

        $C007  = mysql_query("SELECT *
               FROM C007
              WHERE C007_Id={$g['usuarioAtual']}");
        $mC007 = mysql_fetch_array($C007);

        $T006  = mysql_query("SELECT T006_Item
                  FROM T006
                 WHERE T006_T005_Id='$T005_Id'
                order by T006_Item desc
                 limit 1");
        $mT006 = mysql_fetch_array($T006);

        $T006_Item = $mT006['T006_Item'] + 1;

        $cClausula = " SELECT ";
        $cClausula .= " D009_Id, ";
        $cClausula .= " D049_Id, ";
        $cClausula .= " D005_Flag_ST, ";
        $cClausula .= " D009_Valor_Custo_Unitario, ";
        $cClausula .= " D009_Flag_Preco_Tabelado, D009_Quantidade_Estoque_Liquido ";
        $cClausula .= " FROM D001 ";
        $cClausula .= " LEFT JOIN D049 ON D001_Id=D049_D001_Id ";
        $cClausula .= " LEFT JOIN D009 ON D049_Id=D009_D049_Id ";
        $cClausula .= " LEFT JOIN D005 ON D001_D005_Id=D005_Id ";
        $cClausula .= " WHERE D009_Id='$T006_D009_Id' ";
        $T006  = mysql_query($cClausula);
        $mT006 = mysql_fetch_array($T006);

        $erro = mysql_error();
        if (!empty($erro)) {
            return "Erro consulta custo produto: $erro";
        }

        if (($mT006['D009_Id']) <= 0) {
            return "Erro: Produto não encontrado";
        }

        $cClausula = " SELECT *, ";
        $cClausula .= " D053_ST_ICMS_Interno, 
                        D053_ST_ICMS_Inter_Estadual, 
                        D053_Flag_ST, 
                        D018_Id, 
                        C004_D018_Id, 
                        C004_CFOP_ST_Venda_Dentro_Estado, 
                        C004_CFOP_ST_Venda_Fora_Estado, 
                        D006_Flag_Venda_Compra_Outros,
                        T005A_Flag_Conversao_Unidade ";
        $cClausula .= " FROM T005 ";
        $cClausula .= " LEFT JOIN D024 ON D024_Id=T005_D024_Id ";
        $cClausula .= " LEFT JOIN D018 ON D018_Id=D024_D018_Id ";
        $cClausula .= " LEFT JOIN D053 ON D053_D018_Id=D024_D018_Id AND D053_C004_Id=T005_C004_Id ";
        $cClausula .= " LEFT JOIN C004 ON C004_Id=T005_C004_Id ";
        $cClausula .= " LEFT JOIN D006 ON D006_Id=T005_D006_Id ";
        $cClausula .= " LEFT JOIN T005A ON T005_Id=T005A_T005_Id ";
        $cClausula .= " WHERE T005_Id='$T005_Id' ";
        $T005  = mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "$cErro<br />$cClausula";
            exit;
        }
        $mT005 = mysql_fetch_array($T005);

        $D024_Id = $mT005['T005_D024_Id'];
 
        require_once('bibliotecas/classes/SMTProduto.php');
        $SMTProduto = new SMTProduto();
        $T006_Descricao_Produto = $SMTProduto->descricaoConversao($T006_Descricao_Produto, $T006A_Quantidade_Conversao, $T006_D001_Id);
        //$T006_Descricao_Produto = mysql_real_escape_string($T006_Descricao_Produto); já faz o escape no incio, realizando mais uma vez da problema

        // verifica qual preço usar, baseado na lista preço do orçamento ou do cliente
        $listPreco            = mysql_query("SELECT T005_Flag_Lista_Preco, 
                                                    T005_D024_Id,
                                                    T005_C004_Id, 
                                                    T005_Flag_ACP, 
                                                    T005_C004_Id_Estoque, 
                                                    T005_Casas_Decimais_Preco,
                                                    D148_D018_Id
                                               FROM T005 
                                          LEFT JOIN D148 ON D148_Id = T005_D148_Id_Entrega
                                              WHERE T005_Id='{$T005_Id}'");
        $mLista_Preco         = mysql_fetch_array($listPreco);
        $D024_Id              = $mLista_Preco['T005_D024_Id'];
        //$T006_D006_Id         = $VEN001->retornaCFOP($mLista_Preco['T005_D024_Id'], $T006_D009_Id, $mLista_Preco['T005_C004_Id'], $T006_Flag_Destino_Produto, false, false, false, $mLista_Preco['T005_C004_Id_Estoque']);
        //$demonstrativoCFOP    = $VEN001->retornaCFOP($mLista_Preco['T005_D024_Id'], $T006_D009_Id, $mLista_Preco['T005_C004_Id'], $T006_Flag_Destino_Produto, false, true, false, $mLista_Preco['T005_C004_Id_Estoque']);
        $contextoCFOP = [
            'ufEnderecoEntrega' => $mLista_Preco['D148_D018_Id'],
        ];

        $T006_D006_Id         = $VEN001->retornaCFOPContexto($mLista_Preco['T005_D024_Id'], $T006_D009_Id, $mLista_Preco['T005_C004_Id'], $T006_Flag_Destino_Produto, false, false, false, $mLista_Preco['T005_C004_Id_Estoque'], false, $contextoCFOP);
        $demonstrativoCFOP    = $VEN001->retornaCFOPContexto($mLista_Preco['T005_D024_Id'], $T006_D009_Id, $mLista_Preco['T005_C004_Id'], $T006_Flag_Destino_Produto, false, true, false, $mLista_Preco['T005_C004_Id_Estoque'], false, $contextoCFOP);
        
        $demonstrativoCFOP    = mysql_real_escape_string($demonstrativoCFOP);

        $Aliquotas            = $VEN001->retornaAliquotas($T006_D009_Id, false, $T005_Id, false, false, false, false, $T006_D006_Id);

        $demonstrativoT001    = $mD001['T001_Id'];

        //Buscando as aliquotas digitadas na tela de montagem do ipv no orçamento
        $T251 = mysql_query("SELECT SUM(T251_Aliquota-T251_Aliquota_Original) AS AliquotasIPV 
							   FROM T251 
							  WHERE T251_T005_Id = '{$T005_Id}' 
							    AND T251_Flag_Altera_Preco='S' 
								AND T251_Grupo='IPV'");
        $mT251 = mysql_fetch_array($T251);
        $TotalPorcentagemIPV = $mT251['AliquotasIPV'];
        
        //Buscando as aliquotas digitadas na tela de montagem do icf no orçamento
        $T251 = mysql_query("SELECT SUM(T251_Aliquota-T251_Aliquota_Original) AS AliquotasICF 
		                       FROM T251 
							  WHERE T251_T005_Id = '{$T005_Id}' 
							    AND T251_Flag_Altera_Preco='S' 
								AND T251_Grupo='ICF'");
        $mT251 = mysql_fetch_array($T251);
        $TotalPorcentagemICF = $mT251['AliquotasICF'];

        //Método retornaPrecoVenda atualizado com apenas um demonstrativo
        $dadosPrecoVenda         = $CAD002->retornaPrecoVenda($mT006['D009_Id'], $mLista_Preco['T005_Flag_Lista_Preco'], $D024_Id, false, $T006_D006_Id, $Aliquotas['Aliquota_ICMS'], false, false, $mLista_Preco['T005_Casas_Decimais_Preco'], $Aliquotas['Aliquota_PIS'], $Aliquotas['Aliquota_COFINS'], $Aliquotas['Aliquota_IPI'], $Aliquotas['ST_VA'], $Aliquotas['Percentual_Reducao_ICMS_ST'], $Aliquotas['ST_ICMS_Interno'], $Aliquotas['Percentual_Reducao_ICMS'], $Aliquotas['ICMS_Inter_Estadual'], $Aliquotas['Aliquota_IRPJ'], $Aliquotas['Aliquota_CSLL'], 0, $TotalPorcentagemIPV, $TotalPorcentagemICF);
        $Preco_Tabelado          = gCorrigeNumeroInverte($dadosPrecoVenda[0]);
        $demonstrativoPrecoVenda = mysql_real_escape_string(serialize($dadosPrecoVenda[1]));
        $custoUnitarioProduto    = $dadosPrecoVenda[4];

        // Buscando o percentual de tributos (lei transparências)
        $D149  = mysql_query("SELECT D149_Percentual_Trib_Transparencia FROM D149 WHERE D149_Id='{$Aliquotas['D149_Id']}'");
        $mD149 = mysql_fetch_array($D149);

        $D006  = mysql_query("SELECT D006_Percentual_Trib_Transparencia,
                                     D006_Percentual_Desconto_CFOP,
                                     D006_Flag_Destaca_Desconto_NF 
                                FROM D006 
                               WHERE D006_Id='{$T006_D006_Id}'");

        $mD006 = mysql_fetch_array($D006);

        if($mD006['D006_Percentual_Desconto_CFOP'] > 0 && $mD006['D006_Flag_Destaca_Desconto_NF'] == 'N'){
            $T006_Percentual_Desconto += $mD006['D006_Percentual_Desconto_CFOP'];
        }

        if (!empty($mD149['D149_Percentual_Trib_Transparencia']) AND $mD149['D149_Percentual_Trib_Transparencia'] > 0) {
            $T006_Percentual_Trib_Transparencia = $mD149['D149_Percentual_Trib_Transparencia'];
        } else if (!empty($mD006['D006_Percentual_Trib_Transparencia']) AND $mD006['D006_Percentual_Trib_Transparencia'] > 0) {
            $T006_Percentual_Trib_Transparencia = $mD006['D006_Percentual_Trib_Transparencia'];
        } else {
            $T006_Percentual_Trib_Transparencia = 0;
        }

        if ($T006_Valor_Preco_Unitario <= 0) {
            $T006_Valor_Preco_Unitario = $Preco_Tabelado;
        }

        if ($T006_Percentual_Desconto > 0) {
            $T006_Valor_Preco_Unitario = $T006_Valor_Preco_Unitario - ($T006_Valor_Preco_Unitario * ($T006_Percentual_Desconto / 100));
            if ($Preco_Tabelado > 0) {
                $T006_Percentual_Desconto     = ((1 - ($T006_Valor_Preco_Unitario / $Preco_Tabelado)) * 100);
                $T006_Valor_Desconto_Unitario = (($Preco_Tabelado * ($T006_Valor_Preco_Unitario / 100)));
            } else {
                $Preco_Tabelado = $T006_Valor_Preco_Unitario;
            }
        } else {
            if ($Preco_Tabelado > 0) {
                $T006_Percentual_Desconto     = ((1 - ($T006_Valor_Preco_Unitario / $Preco_Tabelado)) * 100);
                $T006_Valor_Desconto_Unitario = (($Preco_Tabelado * ($T006_Valor_Preco_Unitario / 100)));
            } else {
                $Preco_Tabelado = $T006_Valor_Preco_Unitario;
            }
        }

        if ($Aliquotas['D005_Id'] == '') {
            $demonstrativoNCM = $FIS003->demonstrativoNCM($T004_D005_Id);
        } else {
            $demonstrativoNCM = $FIS003->demonstrativoNCM($Aliquotas['D005_Id']);
        }

        $precoTabela                     = $CAD002->cad002RetornaPrecoTabela($mT006['D009_Id'], $mLista_Preco['T005_Flag_Lista_Preco'], $D024_Id, $mLista_Preco['T005_Flag_ACP'], $T006_D006_Id, $Aliquotas['Aliquota_ICMS'], $Aliquotas['Aliquota_PIS'], $Aliquotas['Aliquota_COFINS'], $Aliquotas['Aliquota_IPI'], 0, 0, $Aliquotas['Aliquota_IRPJ'], $Aliquotas['Aliquota_CSLL']);
        $demonstrativoPrecoTabela        = $precoTabela[1];
        $valorPrecoTabela                = gCorrigeNumeroInverte($precoTabela[0]);

        $precoCompra                     = $CAD002->cad002RetornaPrecoUltimaCompra($mT006['D009_Id'], $mLista_Preco['T005_Flag_Lista_Preco'], $D024_Id, $mLista_Preco['T005_Flag_ACP'], $T006_D006_Id, $Aliquotas['Aliquota_ICMS'], 4, $Aliquotas['Aliquota_PIS'], $Aliquotas['Aliquota_COFINS'], $Aliquotas['Aliquota_IPI']);
        $demonstrativo                   = $precoCompra[1];
        $valorPrecoCompra                = gCorrigeNumeroInverte($precoCompra[0]);

        $demonstrativoCustoTabela        = $CAD002->cad002demonstrativoCustoTabela($mT006['D009_Id']);

        $sqlD009Transf = mysql_query("SELECT D009_Id FROM D009 WHERE D009_D049_Id='{$mT006['D049_Id']}' AND D009_C004_Id = '{$g['C031']['C004PrecoTransferenciaGridProdutos']}'");
        $resD009Transf = mysql_fetch_array($sqlD009Transf);
        $dadosPrecoTransferencia         = $CAD002->cad002RetornaPrecoTransferencia($resD009Transf['D009_Id'], $g['C031']['C004PrecoTransferenciaGridProdutos'], $g['C004']['C004_D024_Id'], $T005_Id);
        $valorPrecoTransferencia         = gCorrigeNumeroInverte($dadosPrecoTransferencia[0]);
        $demonstrativoPrecoTransferencia = $dadosPrecoTransferencia[1];
        $valorCustoTransferencia         = $dadosPrecoTransferencia[4];
        $origemProdutoTransferencia      = $dadosPrecoTransferencia[5];

        // Demonstrativo
        $demonstrativo                   = mysql_real_escape_string(serialize($demonstrativo));
        $demonstrativoPrecoTabela        = mysql_real_escape_string(serialize($demonstrativoPrecoTabela));
        $demonstrativoPrecoTransferencia = mysql_real_escape_string(serialize($demonstrativoPrecoTransferencia));
        $demonstrativoNCM                = mysql_real_escape_string($demonstrativoNCM);
        $demonstrativoCustoTabela        = mysql_real_escape_string($demonstrativoCustoTabela);

        $cClausula = " INSERT INTO T006 (";
        $cClausula .= " T006_Aliquota_ICMS_Credito, ";
        $cClausula .= " T006_C004_Id, ";
        $cClausula .= " T006_Quantidade_Estoque, ";
        $cClausula .= " T006_Item, ";
        $cClausula .= " T006_Codigo_Produto, ";
        $cClausula .= " T006_Codigo_Substituto, ";
        $cClausula .= " T006_Descricao_Produto, ";
        $cClausula .= " T006_T005_Id, ";
        $cClausula .= " T006_D024_Id, ";
        $cClausula .= " T006_D009_Id, ";
        $cClausula .= " T006_D037_Id, ";
        $cClausula .= " T006_D082_Id, ";
        $cClausula .= " T006_Quantidade, ";
        $cClausula .= " T006_Valor_Custo_Unitario, ";
        $cClausula .= " T006_Valor_Preco_Unitario, ";
        $cClausula .= " T006_Valor_Preco_Sem_Desconto_Unitario, ";
        $cClausula .= " T006_Valor_Preco_Original, ";
        $cClausula .= " T006_Valor_Preco_Original_Bruto, ";
        $cClausula .= " T006_Percentual_Desconto, ";
        $cClausula .= " T006_Valor_Desconto_Unitario, ";
        $cClausula .= " T006_Percentual_Desconto_Bruto, ";
        $cClausula .= " T006_Valor_Desconto_Unitario_Bruto, ";
        $cClausula .= " T006_IPV, ";
        $cClausula .= " T006_Peso_Unitario, ";
        $cClausula .= " T006_Peso_Bruto, ";
        $cClausula .= " T006_D005_Id,";
        $cClausula .= " T006_Classificacao_Fiscal,";
        $cClausula .= " T006_D006_Id,";
        $cClausula .= " T006_Aliquota_IPI,";
        $cClausula .= " T006_Aliquota_ICMS,";
        $cClausula .= " T006_Percentual_Reducao_ICMS,";
        $cClausula .= " T006_Percentual_Reducao_ICMS_ST,";
        $cClausula .= " T006_ST_VA,";
        $cClausula .= " T006_Flag_ST,";
        $cClausula .= " T006_Flag_Situacao_Tributaria,";
        $cClausula .= " T006_ICMS_Inter_Estadual,";
        $cClausula .= " T006_ST_ICMS_Interno,";
        $cClausula .= " T006_ICMS_Inter_Estadual_MVA,";
        $cClausula .= " T006_ST_ICMS_Interno_MVA,";
        $cClausula .= " T006_Flag_Isento_PIS,";
        $cClausula .= " T006_Aliquota_PIS,";
        $cClausula .= " T006_Situacao_Tributaria_PIS,";
        $cClausula .= " T006_Flag_Isento_COFINS,";
        $cClausula .= " T006_Aliquota_COFINS, ";
        $cClausula .= " T006_Flag_Preco_Especial, ";
        $cClausula .= " T006_Situacao_Tributaria_COFINS, ";
        $cClausula .= " T006_Situacao_Tributaria_IPI, ";
        $cClausula .= " T006_D059_Id, ";
        $cClausula .= " T006_Observacao, ";
        $cClausula .= " T006_Numero_Pedido_Compra, ";
        $cClausula .= " T006_D022_Id, ";
        $cClausula .= " T006_T208_Id, ";
        $cClausula .= " T006_Hora_Entrega, ";
        if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
            $cClausula .= " T006_Item_Pedido_Compra, ";
            $cClausula .= " T006_Valor_Preco_Bruto, ";
        } else {
            $cClausula .= " T006_Item_Pedido_Compra, ";
        }
        $cClausula .= " T006_Cubagem_Unitaria, ";
        $cClausula .= " T006_Percentual_Trib_Transparencia, ";
        $cClausula .= " T006_D144_Id, ";
        $cClausula .= " T006_Demonstrativo_Preco, ";
        $cClausula .= " T006_Demonstrativo_Preco_Tabela, ";
        $cClausula .= " T006_Demonstrativo_Preco_Transferencia, ";
        $cClausula .= " T006_Valor_Preco_Compra, ";
        $cClausula .= " T006_Valor_Preco_Tabela, ";
        $cClausula .= " T006_Valor_Preco_Transferencia, ";
        $cClausula .= " T006_Valor_Custo_Transferencia, ";
        $cClausula .= " T006_Flag_Origem_Produto_Transferencia, ";
        $cClausula .= " T006_T001_Id, ";
        $cClausula .= " T006_Flag_Destino_Produto, ";
        $cClausula .= " T006_Observacao_Entrega, ";
        $cClausula .= " T006_D006_Id_Info, ";
        $cClausula .= " T006_D005_Id_Info, ";
        $cClausula .= " T006_Demonstrativo_Custo_Tabela, ";
        $cClausula .= " T006_Quantidade_OC, ";
        $cClausula .= " T006_Demonstrativo_Preco_Venda, ";
        $cClausula .= " T006_Aliquota_IRPJ, ";
        $cClausula .= " T006_Flag_Isento_IRPJ,";
        $cClausula .= " T006_Aliquota_CSLL, ";
        $cClausula .= " T006_Flag_Isento_CSLL,";
        $cClausula .= " T006_UF_ICMS_Substituicao_Devido,";
        $cClausula .= " T006_Percentual_Base_Operacao_Propria,";
        $cClausula .= " T006_Preco_Pauta_ST,";
        $cClausula .= " T006_Codigo_Local,";
        $cClausula .= " T006_Percentual_Partilha_ICMS_Destino,";
        $cClausula .= " T006_Percentual_Partilha_ICMS_Origem,";
        $cClausula .= " T006_Data_Entrega";
        $cClausula .= " ) VALUES ( ";
        $cClausula .= " '{$Aliquotas['Aliquota_ICMS_Credito']}', ";
        $cClausula .= " '{$g['empresaAtual']}', ";
        $cClausula .= " '{$mT006['D009_Quantidade_Estoque_Liquido']}', ";
        $cClausula .= " '$T006_Item', ";
        $cClausula .= " '$T006_Codigo_Produto', ";
        $cClausula .= " '$T006_Codigo_Substituto', ";
        $cClausula .= " '$T006_Descricao_Produto', ";
        $cClausula .= " '$T005_Id', ";
        $cClausula .= " '$D024_Id', ";
        $cClausula .= " '{$mT006['D009_Id']}', ";
        $cClausula .= " '$T006_D037_Id', ";
        $cClausula .= " '{$Aliquotas['D082_Id']}', ";
        $cClausula .= " '$T006_Quantidade', ";
        $cClausula .= " '{$custoUnitarioProduto}', ";
        //Se o produto for incluido pelo coletor, o valor unit. deve ser zero
        if ($tipo == 2) {
            $cClausula .= " '0', ";
        } else {
            $cClausula .= " '$T006_Valor_Preco_Unitario', ";
        }
        $cClausula .= " '$T006_Valor_Preco_Unitario', ";
        $cClausula .= " '{$Preco_Tabelado}', ";
        $cClausula .= " '{$Preco_Tabelado}', ";
        $cClausula .= " '$T006_Percentual_Desconto', ";
        $cClausula .= " '$T006_Valor_Desconto_Unitario', ";
        $cClausula .= " '$T006_Percentual_Desconto', ";
        $cClausula .= " '$T006_Valor_Desconto_Unitario', ";
        $cClausula .= " '$T006_Valor_Preco_Unitario/{$Aliquotas['Valor_Custo_Unitario']}', ";
        $cClausula .= " '{$Aliquotas['D001_Peso_Unitario_Kg']}', ";
        $cClausula .= " '{$Aliquotas['D001_Peso_Unitario_Bruto']}', ";
        if ($Aliquotas['D005_Id'] == '') {
            $cClausula .= " '{$T006_D005_Id}',";
        } else {
            $cClausula .= " '{$Aliquotas['D005_Id']}',";
        }
        $cClausula .= " '{$Aliquotas['Classificacao_Fiscal']}',";
        $cClausula .= " '{$T006_D006_Id}',";
        //$cClausula .= " '{$mT006['Aliquota_IPI']}',";
        if ($T006_Aliquota_IPI <= 0) {
            $cClausula .= " '{$Aliquotas['Aliquota_IPI']}',";
        } else {
            $cClausula .= " '{$T006_Aliquota_IPI}',";
        }
        $cClausula .= " '{$Aliquotas['Aliquota_ICMS']}',";
        $cClausula .= " '{$Aliquotas['Percentual_Reducao_ICMS']}',";
        $cClausula .= " '{$Aliquotas['Percentual_Reducao_ICMS_ST']}',";
        $cClausula .= " '{$Aliquotas['ST_VA']}',";
        $cClausula .= " '{$Aliquotas['Flag_ST']}',";
        $cClausula .= " '{$Aliquotas['Flag_Situacao_Tributaria']}',";
        $cClausula .= " '{$Aliquotas['ICMS_Inter_Estadual']}',";
        $cClausula .= " '{$Aliquotas['ST_ICMS_Interno']}',";
        $cClausula .= " '{$Aliquotas['ST_ICMS_Inter_Estadual_MVA']}',";
        $cClausula .= " '{$Aliquotas['ST_ICMS_Interno_MVA']}',";
        $cClausula .= " '{$Aliquotas['Flag_Isento_PIS']}',";
        $cClausula .= " '{$Aliquotas['Aliquota_PIS']}',";
        $cClausula .= " '{$Aliquotas['Situacao_Tributaria_PIS']}',";
        $cClausula .= " '{$Aliquotas['Flag_Isento_COFINS']}',";
        $cClausula .= " '{$Aliquotas['Aliquota_COFINS']}', ";
        $cClausula .= " '',";
        $cClausula .= " '{$Aliquotas['Situacao_Tributaria_COFINS']}',";
        $cClausula .= " '{$Aliquotas['Situacao_Tributaria_IPI']}',";
        $cClausula .= " '{$Aliquotas['D059_Id']}',";
        $cClausula .= " '$T006_Observacao', ";
        $cClausula .= " '$T006_Numero_Pedido_Compra', ";
        $cClausula .= " '$T006_D022_Id', ";
        $cClausula .= " '$T006_T208_Id', ";
        $cClausula .= " '$T006_Hora_Entrega', ";
        if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
            $cClausula .= " '$T006_Item_Pedido_Compra', ";
            $cClausula .= " '$T006_Valor_Preco_Unitario', ";
        } else {
            $cClausula .= " '$T006_Item_Pedido_Compra', ";
        }
        $cClausula .= " '{$mD001['D001_Cubagem_Unitaria']}', ";
        $cClausula .= " '{$T006_Percentual_Trib_Transparencia}', ";
        $cClausula .= " '{$T006_D144_Id}', ";
        $cClausula .= " '{$demonstrativo}', ";
        $cClausula .= " '{$demonstrativoPrecoTabela}', ";
        $cClausula .= " '{$demonstrativoPrecoTransferencia}', ";
        $cClausula .= " '{$valorPrecoCompra}', ";
        $cClausula .= " '{$valorPrecoTabela}', ";
        $cClausula .= " '{$valorPrecoTransferencia}', ";
        $cClausula .= " '{$valorCustoTransferencia}', ";
        $cClausula .= " '{$origemProdutoTransferencia}', ";
        $cClausula .= " '{$demonstrativoT001}', ";
        $cClausula .= " '{$T006_Flag_Destino_Produto}', ";
        $cClausula .= " '{$T006_Observacao_Entrega}', ";
        $cClausula .= " '{$demonstrativoCFOP}', ";
        $cClausula .= " '{$demonstrativoNCM}', ";
        $cClausula .= " '{$demonstrativoCustoTabela}', ";
        $cClausula .= " '{$mD001['D009_Quantidade_OC']}', ";
        $cClausula .= " '{$demonstrativoPrecoVenda}', ";
        $cClausula .= " '{$Aliquotas['Aliquota_IRPJ']}', ";
        $cClausula .= " '{$Aliquotas['Flag_Isento_IRPJ']}',";
        $cClausula .= " '{$Aliquotas['Aliquota_CSLL']}', ";
        $cClausula .= " '{$Aliquotas['Flag_Isento_CSLL']}',";
        $cClausula .= " '{$Aliquotas['UF_ICMS_Substituicao_Devido']}',";
        $cClausula .= " '{$Aliquotas['Percentual_Base_Operacao_Propria']}',";
        $cClausula .= " '{$T006_Preco_Pauta_ST}',";
        $cClausula .= " '{$T006_Codigo_Local}',";
        $cClausula .= " '{$Aliquotas['Percentual_Partilha_ICMS_Destino']}',";
        $cClausula .= " '{$Aliquotas['Percentual_Partilha_ICMS_Origem']}',";
        $cClausula .= " '{$T006_Data_Entrega}'";
        $cClausula .= " ); ";

        mysql_query($cClausula);
        $T006_Id = $g['mysqlLastId'];
        $cErro   = mysql_error();
        if (!empty($cErro)) {
            return "Erro: $cErro<br />$cClausula";
        } else {
            // Processa estoque por locação
            $CAD002->processaEstoqueLiquidoPorLocacao($mT006['D009_Id']);
            $CAD002->reprocessaOCPedidos($mT006['D009_Id']);

            // INSERE REGISTRO NA TABELA ESTENDIDA
            $dadosT006A = array(
                'T006A_Quantidade_Conversao' => $T006A_Quantidade_Conversao,
                'T006A_Codigo_Obra' => $T006A_Codigo_Obra,
                'T006A_Altura' => $T006A_Altura,
                'T006A_Largura' => $T006A_Largura,
                'T006A_Comprimento' => $T006A_Comprimento,
                'T006A_Resultado' => $T006A_Resultado,
                'T006A_Flag_Transacao_Externa' => $T006A_Flag_Transacao_Externa,
            );
            $retornoT006A = $this->incluirProdutoAddT006A($T006_Id, $dadosT006A);
            if ($retornoT006A !== true) {
                return $retornoT006A;
            }
            
            mysql_query("UPDATE T006A 
                      LEFT JOIN T006 ON T006_Id = T006A_T006_Id
                            SET T006A_Quantidade_Comercial     = IF('{$mT005['T005A_Flag_Conversao_Unidade']}' = 'S', T006_Quantidade, 0.00),
                                T006A_Preco_Unitario_Comercial = IF('{$mT005['T005A_Flag_Conversao_Unidade']}' = 'S', T006_Valor_Preco_Sem_Desconto_Unitario, 0.00),
                                T006A_D006_Id_Info             = '{$demonstrativoCFOP}',
                                T006A_Data_Hora_Insercao       = NOW(),
                                T006A_Percentual_FCP           = '{$Aliquotas['Percentual_FCP']}',
                                T006A_Percentual_Desconto_CFOP = '{$mD006['D006_Percentual_Desconto_CFOP']}'
                          WHERE T006A_T006_Id = '{$T006_Id}'");

            mysql_query("UPDATE T189 SET T189_Flag_Status = 'N' WHERE T189_T006_Id = '{$g['mysqlLastId']}'");
            
            if ($g['C031']['controleProdutoPedido'] == 'S') {
                if ($tipo == 1) {
                    $this->atualizarControleProduto($T006_Id);
                    $quantidadeSeparada = '';
                } else if ($tipo == 2) {
                    $this->inserirControleProduto($T006_Id);
                    $quantidadeSeparada = $T006_Quantidade;
                }
                $T066 = mysql_query("SELECT * FROM T066 WHERE T066_D009_Id ='{$mT006['D009_Id']}'");
                $res  = mysql_fetch_array($T066);
                $sql  = "INSERT INTO T185(
                                    T185_T006_Id,
                                    T185_T066_Id,
                                    T185_Qtd_Para_Separar,
                                    T185_Qtd_Separado
                                  )VALUES(
                                    '{$T006_Id}',
                                    '{$res['T066_Id']}',
                                    '{$T006_Quantidade}',
                                    '{$quantidadeSeparada}'
                                  )";
                mysql_query($sql);
            }

            if (!empty($T006_Codigo_Substituto)) {
                /*
                $D083 = mysql_query("SELECT D083_Id,D083_Flag_Cliente,D083_Codigo_Produto_Fornecedor FROM D083 WHERE D083_D001_Id='{$mT006['D001_Id']}' AND D083_D049_Id='{$mT006['D049_Id']}' AND D083_D024_Id='$D024_Id'");
                if (mysql_num_rows($D083) <= 0) {
                    mysql_query("INSERT INTO D083 (D083_D049_Id,D083_D001_Id,D083_D024_Id,D083_Data_Cadastro,D083_Flag_Cliente,D083_Codigo_Produto_Fornecedor) VALUES ('{$mT006['D049_Id']}','{$mT006['D001_Id']}','$D024_Id',curdate(),'S','$T006_Codigo_Substituto')");
                } else {
                    $mD083 = mysql_fetch_array($D083);
                    mysql_query("UPDATE D083 SET D083_Flag_Cliente='S',D083_Codigo_Produto_Fornecedor='$T006_Codigo_Substituto' WHERE D083_Id='{$mD083['D083_Id']}'");
                }
                */
                require_once("bibliotecas/classes/CAD002.php");
                $CAD002 = new CAD002();
                $CAD002->definirCodigoFornecedor($mT006['D009_Id'], $D024_Id, $T006_Codigo_Substituto, '', true, false);
            }
            $this->atualizarAliquotasPedidos($T005_Id, false, $T006_Id);
            $cSql = mysql_query("call T006_Gravar_Totalizacao_4('$T006_Id');");
            $cSql = mysql_query("call T005_Gravar_Totalizacao_4('$T005_Id');");
        }

        // caso o produto possua serviços associados, insere estes serviços na T147
        $result  = mysql_query("SELECT D001_Id FROM D009 LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id WHERE D009_Id='{$T006_D009_Id}'");
        $retorno = mysql_fetch_array($result);
        $D001_Id = $retorno['D001_Id'];
        $result  = mysql_query("SELECT * FROM D135 LEFT JOIN D112 ON D112_Id=D135_D112_Id WHERE D135_D001_Id='{$D001_Id}'");

        if (mysql_num_rows($result) > 0) {
            if (!$T006A_Usa_Transacao_Externa) {
                mysql_query("SET AUTOCOMMIT=0");
                mysql_query("START TRANSACTION");
            }
            while ($D135 = mysql_fetch_array($result)) {
                $cClausula = " INSERT INTO T147 (";
                $cClausula .= " T147_T005_Id, ";
                $cClausula .= " T147_D112_Id, ";
                $cClausula .= " T147_D110_Id, ";
                $cClausula .= " T147_T006_Id, ";
                $cClausula .= " T147_Titulo, ";
                $cClausula .= " T147_Descricao, ";
                $cClausula .= " T147_Tempo_Estimado, ";
                $cClausula .= " T147_Quantidade, ";
                $cClausula .= " T147_Status, ";
                $cClausula .= " T147_Valor_Unitario, ";
                $cClausula .= " T147_Valor_Total, ";
                $cClausula .= " T147_D116_Id ";
                $cClausula .= " ) VALUES ( ";
                $cClausula .= " '$T005_Id', ";
                $cClausula .= " '{$D135['D135_D112_Id']}', ";
                $cClausula .= " '{$D135['D112_D110_Id']}', ";
                $cClausula .= " '$T006_Id', ";
                $cClausula .= " '{$D135['D112_Titulo']}', ";
                $cClausula .= " '{$D135['D112_Descricao']}', ";
                $cClausula .= " '{$D135['D112_Tempo_Estimado']}', ";
                $cClausula .= " '1', ";
                $cClausula .= " 'P', ";
                $cClausula .= " '{$D135['D112_Valor_Unitario']}', ";
                $cClausula .= " '{$D135['D112_Valor_Unitario']}', ";
                $cClausula .= " '{$idVeiculoPedido}' ";
                $cClausula .= " ) ";
                mysql_query($cClausula);
                if (mysql_error()) {
                    if (!$T006A_Usa_Transacao_Externa) mysql_query("ROLLBACK");
                    return 'falha ao copiar os serviços do produto';
                }
                
                $T147_Id = $g['mysqlLastId'];
                
                $sql    = "SELECT * FROM T147 WHERE T147_Id='{$T147_Id}'";
                $result = mysql_query($sql);
                $linha  = mysql_fetch_array($result);
                
                $sql     = "SELECT * FROM T171 WHERE T171_D116_Id='{$linha['T147_D116_Id']}' AND T171_T005_Id='{$linha['T147_T005_Id']}'";
                $result2 = mysql_query($sql);
                if (mysql_num_rows($result2) == 0) {
                    $sql = "INSERT INTO T171
          (T171_D116_Id, T171_T005_Id, T171_T147_Id)
            VALUES
          ('{$linha['T147_D116_Id']}', '{$linha['T147_T005_Id']}', '{$T147_Id}')";
                    
                    mysql_query($sql);
                    
                    if (mysql_error()) {
                        if (!$T006A_Usa_Transacao_Externa) mysql_query("ROLLBACK");
                        return mysql_error();
                    }
                }
                
                $sql   = "SELECT * FROM D113 WHERE D113_D112_Id='{$D135['D135_D112_Id']}'";
                $array = mysql_query($sql);
                
                while ($row = mysql_fetch_assoc($array)) {
                    // D113_D112_Id, D113_D110_Id, D113_Titulo, D113_Descricao
                    $sql = "INSERT INTO T148 (T148_T147_Id, T148_Titulo, T148_Descricao) VALUES ('" . mysql_real_escape_string($T147_Id) . "', '"
                    //.mysql_real_escape_string($row['D113_D110_Id'])."', '"
                        . mysql_real_escape_string($row['D113_Titulo']) . "', '" . mysql_real_escape_string($row['D113_Descricao']) . "');";
                    mysql_query($sql);
                    if (mysql_error()) {
                        if (!$T006A_Usa_Transacao_Externa) mysql_query("ROLLBACK");
                        return 'Erro: ' . mysql_error();
                    }
                }
                
                $sql   = "SELECT * FROM D114 WHERE D114_D112_Id='{$D135['D135_D112_Id']}'";
                $array = mysql_query($sql);
                
                while ($row = mysql_fetch_assoc($array)) {
                    $sql = "INSERT INTO T149 (T149_D037_Id, T149_D009_Id, T149_T147_Id, T149_Quantidade) VALUES ('" . mysql_real_escape_string($row['D114_D037_Id']) . "', '" . mysql_real_escape_string($row['D114_D009_Id']) . "', '" . mysql_real_escape_string($T147_Id) . "', '" . mysql_real_escape_string($row['D114_Quantidade']) . "');";
                    mysql_query($sql);
                    if (mysql_error()) {
                        if (!$T006A_Usa_Transacao_Externa) mysql_query("ROLLBACK");
                        return 'Erro: ' . mysql_error();
                    }
                }
                
                
            }
            if (!$T006A_Usa_Transacao_Externa) {
                mysql_query("COMMIT");
                mysql_query("SET AUTOCOMMIT=1");
            }
        }
        
        if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
            mysql_query("UPDATE T006 SET T006_Valor_Preco_Sem_Desconto_Unitario = '{$T006_Valor_Preco_Unitario}' WHERE T006_Id = '{$T006_Id}'");
            mysqli_query("CALL T006_Gravar_Totalizacao_4('{$T006_Id}')");
            mysql_query("UPDATE T006 SET T006_Valor_Preco_Sem_Desconto_Unitario = T006_Valor_Preco_Sem_Desconto_Unitario/((T006_Valor_Preco_Sem_Desconto_Unitario+(T006_Valor_IPI/T006_Quantidade)+(T006_Valor_ICMS_Substituicao_Retencao/T006_Quantidade))/T006_Valor_Preco_Sem_Desconto_Unitario) WHERE T006_Id = '{$T006_Id}'");
            mysqli_query("CALL T006_Gravar_Totalizacao_4('{$T006_Id}')");
        }
        return TRUE;
    }

    public function incluirProdutoAddT006A($T006_Id, $dadosT006A)
    {
        if (!is_array($dadosT006A)) {
            return 'Erro ao inserir T006A: os dados informados são inválidos.';
        }
        $dadosT006A['T006A_T006_Id'] = $T006_Id;
        $colunas = array(); $valores = array();
        foreach ($dadosT006A as $campo => $valor) {
            if (!preg_match('/^T006A_[A-Za-z0-9_]+$/', $campo)) return "Erro ao inserir T006A: campo inválido ({$campo}).";
            $colunas[] = '`' . $campo . '`';
            $valores[] = is_null($valor) ? 'NULL' : '\'' . mysql_real_escape_string($valor) . '\'';
        }
        if (empty($colunas)) return 'Erro ao inserir T006A: nenhum campo foi informado.';
        $sql = 'INSERT INTO T006A (' . implode(', ', $colunas) . ') VALUES (' . implode(', ', $valores) . ')';
        if (!mysql_query($sql)) return 'Erro ao inserir T006A: ' . mysql_error();
        return true;
    }

    /**
     * ven002AtualizarParcelas
     *
     * @param String $T090_Id
     * @param String $campoEditado
     * @param String $valorAntigo
     * @param String $novoValor
     * @return String/bool
     */    
    public function ven002AtualizarParcelas($T090_Id, $campoEditado, $valorAntigo, $novoValor)
    {
        global $g;
        
        $query            = "SELECT * FROM T090 LEFT JOIN T005 ON T090_T005_Id='{$T090_Id}' WHERE T090_Id='{$T090_Id}'";
        $retorno          = mysql_query($query);
        $linha            = mysql_fetch_assoc($retorno);
        $dataVelhaT005    = $linha['T005_Data_Emissao'];
        $prazosDiasAntigo = $linha['T090_Prazos_Dias'];
        
        if ($campoEditado == 'T090_Prazos_Dias') {
            // if($valorAntigo == $novoValor){
            // return "O prazo é o mesmo";
            // }
            
            $date = new \DateTime($dataVelhaT005);
            $date->modify('+' . $novoValor . ' days');
            $query = "UPDATE T090 SET T090_Data_Vencimento='{$date->format('Y-m-d')}' WHERE T090_Id='{$T090_Id}'";
            mysql_query($query);
            
        } else if ($campoEditado == 'T090_Data_Vencimento') {
            
            $dataAntiga = gCorrigeDataInverte($valorAntigo);
            $dataNova   = gCorrigeDataInverte($novoValor);
            
            $timeStampDataNova   = strtotime($dataNova);
            $timeStampDataAntiga = strtotime($dataAntiga);
            
            
            if ($timeStampDataAntiga == $timeStampDataNova) {
                return 'Datas Iguais';
            } else if ($timeStampDataAntiga < $timeStampDataNova) {
                $novaData = ($timeStampDataNova - $timeStampDataAntiga);
                $dias     = date('d', $novaData);
                
                $total = ($prazosDiasAntigo + $dias);
                $query = "UPDATE T090 SET T090_Prazos_Dias='{$total}' WHERE T090_Id='{$T090_Id}'";
                mysql_query($query);
                return true;
            } else if ($timeStampDataAntiga > $timeStampDataNova) {
                $novaData = ($timeStampDataAntiga - $timeStampDataNova);
                $dias     = date('d', $novaData);
                $total    = ($prazosDiasAntigo - $dias);
                $query    = "UPDATE T090 SET T090_Prazos_Dias='{$total}' WHERE T090_Id='{$T090_Id}'";
                mysql_query($query);
                return true;
            }
            
        } else if ($campoEditado == 'T090_Valor_Parcela') {
            
            $query      = "SELECT * FROM T090 WHERE T090_Id='{$T090_Id}'";
            $retorno    = mysql_query($query);
            $linha      = mysql_fetch_assoc($retorno);
            $T005_Id    = $linha['T090_T005_Id'];
            $numParcela = $linha['T090_Numero_Parcela'];
            
            /* total antigo, pegando o valor do pedido
            $query = "SELECT T005_Valor_Total FROM T005 WHERE T005_Id='{$T005_Id}'";
            $retorno=mysql_query($query);       
            $linha = mysql_fetch_assoc($retorno);   
            $valorTotal=$linha['T005_Valor_Total'];
            */
            
            // pega o valor total com juros (se houver)
            // sem a parcela alterada, e no fim soma com o valor antigo, para pegar o valor total com juros original
            $query      = "SELECT SUM(T090_Valor_Parcela) FROM T090 WHERE T090_T005_Id='{$T005_Id}' AND T090_Id!='{$T090_Id}'";
            $retorno    = mysql_query($query);
            $linha      = mysql_fetch_assoc($retorno);
            $valorTotal = $linha['SUM(T090_Valor_Parcela)'] + gCorrigeNumeroInverte($valorAntigo);
            
            $query                  = "SELECT SUM(T090_Valor_Parcela) FROM T090 WHERE T090_T005_Id='{$T005_Id}' AND T090_Numero_Parcela<='{$numParcela}'";
            $retorno                = mysql_query($query);
            $linha                  = mysql_fetch_assoc($retorno);
            $valorParcelasRestantes = $linha['SUM(T090_Valor_Parcela)'];
            $valorRestante          = $valorTotal - $valorParcelasRestantes;
            
            $query             = "SELECT * FROM T090 WHERE T090_T005_Id='{$T005_Id}' AND T090_Numero_Parcela>'{$numParcela}'";
            $retorno           = mysql_query($query);
            $parcelasRestantes = mysql_num_rows($retorno);
            //se ainda tiverem parcelas depois da editada, divide o valor restante entre elas
            if ($parcelasRestantes != 0) {
                $valorParcelasRestantes = $valorRestante / $parcelasRestantes;
                if ($valorParcelasRestantes < 0) {
                    $query = "UPDATE T090 SET T090_Valor_Parcela='{$valorAntigo}' WHERE T090_Id='{$T090_Id}'";
                    mysql_query($query);
                    return 'O valor total das parcelas é maior que o valor do pedido.<br>Favor alterar novamente';
                }
                while ($linha = mysql_fetch_assoc($retorno)) {
                    $query = "UPDATE T090 SET T090_Valor_Parcela='{$valorParcelasRestantes}' WHERE T090_Id='{$linha['T090_Id']}'";
                    mysql_query($query);
                }
            }
            return true;
        }
        return true;
    }
    
    /**
     * inserirParcela
     *
     * @param Form $form
     * @return bool
     */     
    public function inserirParcela($form)
    {
        
        $T005_Id        = $form['T005_Id'];
        $intervaloDias  = $form['IntervalorDias'];
        $portador       = $form['D027_Id'];
        $Total_Parcelas = $form['Total_Parcelas'];
        
        $diaFixo                = $form['diaFixo'];
        $primeiroVencimentoNum  = $form['primeiroVencimentoNum'];
        $primeiroVencimentoData = $form['primeiroVencimentoData'];
        
        // se foi definido um intervalo de dias
        if ($intervaloDias > 0) {
            $valorNovo  = 0;
            $query      = "SELECT * FROM T005 WHERE T005_Id='{$T005_Id}'";
            $retorno    = mysql_query($query);
            $linha      = mysql_fetch_assoc($retorno);
            $valorTotal = $linha['T005_Valor_Total'];
            
            $query            = "SELECT * FROM T090 WHERE T090_T005_Id='{$T005_Id}' ORDER BY T090_Id DESC";
            $retorno          = mysql_query($query);
            $linha            = mysql_fetch_assoc($retorno);
            $prazoUltima      = $linha['T090_Prazos_Dias'];
            $numeroUltima     = $linha['T090_Numero_Parcela'];
            $numeroNova       = $numeroUltima + 1;
            $vencimentoUltima = $linha['T090_Data_Vencimento'];
            // se a parcela a ser inserida é a primeira, pega a data informada no campo "Primeiro Vencimento"
            // se não, a data é a ultima + intervalo de dias informado.
            if (mysql_num_rows($retorno) == 0) {
                if (trim($primeiroVencimentoNum) != "") {
                    $prazoNovo = $primeiroVencimentoNum;
                } else if (trim($primeiroVencimentoData) != "") {
                    $date1     = new \DateTime(date("Y-m-d"));
                    $date2     = new \DateTime(gCorrigeDataInverte($primeiroVencimentoData));
                    $interval  = $date1->diff($date2);
                    $prazoNovo = $interval->days;
                }
                $valorNovo = $valorTotal;
            } else {
                $prazoNovo = $prazoUltima + $intervaloDias;
                $valorNovo = 0;
            }
            
            $cClausula = "INSERT INTO T090 (";
            $cClausula .= "T090_T005_Id,";
            $cClausula .= "T090_D027_Id,";
            $cClausula .= "T090_Numero_Parcela,";
            $cClausula .= "T090_Prazos_Dias,";
            $cClausula .= "T090_Data_Vencimento,";
            $cClausula .= "T090_Valor_Parcela";
            $cClausula .= ") VALUES (";
            $cClausula .= "'$T005_Id',";
            $cClausula .= "'$portador',";
            $cClausula .= "'$numeroNova',";
            $cClausula .= "'$prazoNovo',";
            $cClausula .= "DATE_ADD(current_date(),INTERVAL $prazoNovo DAY),";
            $cClausula .= "'$valorNovo'";
            $cClausula .= ")";
            mysql_query($cClausula);
            $erro = mysql_error();
            // ou se foi colocado um dia fixo   
        } else if ($diaFixo > 0) {
            
            if ($diaFixo > 31)
                return "Por favor, insira um dia válido";
            
            $valorNovo  = 0;
            $query      = "SELECT * FROM T005 WHERE T005_Id='{$T005_Id}'";
            $retorno    = mysql_query($query);
            $linha      = mysql_fetch_assoc($retorno);
            $valorTotal = $linha['T005_Valor_Total'];
            
            $query            = "SELECT * FROM T090 WHERE T090_T005_Id='{$T005_Id}' ORDER BY T090_Id DESC";
            $retorno          = mysql_query($query);
            $linha            = mysql_fetch_assoc($retorno);
            $prazoUltima      = $linha['T090_Prazos_Dias'];
            $numeroUltima     = $linha['T090_Numero_Parcela'];
            $numeroNova       = $numeroUltima + 1;
            $vencimentoUltima = $linha['T090_Data_Vencimento'];
            if (mysql_num_rows($retorno) == 0) {
                if (trim($primeiroVencimentoNum) != "") {
                    $date     = strtotime("+$primeiroVencimentoNum day", strtotime(date("Y-m-d")));
                    $dataNova = date("Y-m-d", $date);
                } else if (trim($primeiroVencimentoData) != "") {
                    $dataNova = gCorrigeDataInverte($primeiroVencimentoData);
                }
                $valorNovo = $valorTotal;
            } else {
                $ano = date("Y", strtotime($vencimentoUltima));
                $mes = date("m", strtotime($vencimentoUltima));
                $mes++;
                if ($mes == 13) {
                    $mes = 1;
                    $ano++;
                }
                $dataSomar = "$ano-$mes-$diaFixo";
                $validou   = checkdate($mes, $diaFixo, $ano);
                while (!$validou) {
                    $diaFixo--;
                    $validou = checkdate($mes, $diaFixo, $ano);
                }
                $dataNova = "$ano-$mes-$diaFixo";
                
                $valorNovo = 0;
            }
            
            $cClausula = "INSERT INTO T090 (";
            $cClausula .= "T090_T005_Id,";
            $cClausula .= "T090_D027_Id,";
            $cClausula .= "T090_Numero_Parcela,";
            $cClausula .= "T090_Data_Vencimento,";
            $cClausula .= "T090_Prazos_Dias,";
            $cClausula .= "T090_Valor_Parcela";
            $cClausula .= ") VALUES (";
            $cClausula .= "'$T005_Id',";
            $cClausula .= "'$portador',";
            $cClausula .= "'$numeroNova',";
            $cClausula .= "'$dataNova',";
            $cClausula .= "DATEDIFF('$dataNova',current_date()),";
            $cClausula .= "'$valorNovo'";
            $cClausula .= ")";
            mysql_query($cClausula);
            $erro = mysql_error();
        }
        
        if (empty($erro)) {
            return true;
        } else {
            return false;
        }
        
    }
    
    /**
     * calcularParcelaSimulacao
     *
     * @param Form $form
     * @return String
     */    
    public function calcularParcelaSimulacao($form)
    {
        global $g;
        $T005_Id     = $form['T005_Id'];
        $numParcelas = $form['Total_Parcelas'];
        
        if ($numParcelas == "0") {
            return '';
        }
        
        $query      = "SELECT * FROM T005 WHERE T005_Id='{$T005_Id}'";
        $retorno    = mysql_query($query);
        $linha      = mysql_fetch_assoc($retorno);
        $valorTotal = $linha['T005_Valor_Total'];
        
        return gCorrigeNumero($valorTotal / $numParcelas);
    }
    
    /**
     * calcularParcelaCombo
     *
     * @param String $parcela
     * @param String $valorTotal
     * @param String $tipoJuros
     * @param String $taxaJuros
     * @param String $parcelasIsentas
     * @return String
     */    
    public function calcularParcelaCombo($parcela, $valorTotal, $tipoJuros, $taxaJuros, $parcelasIsentas)
    {
        global $g;
        if ($parcela <= $parcelasIsentas) {
            return gCorrigeNumero($valorTotal / $parcela);
        } else {
            if ($tipoJuros == 'S') {
                return gJurosSimples($valorTotal, $taxaJuros, $parcela);
            } else if ($tipoJuros == 'C') {
                return gJurosCompostos($valorTotal, $taxaJuros, $parcela);
            }
        }
        
    }
    
    /**
     * calcularParcelaSimulacao
     *
     * @param String $T005_Id
     * @return String
     */
    public function calculaTotalServicos($T005_Id)
    {
        $T005_Id = mysql_real_escape_string($T005_Id);
        $sql     = "SELECT * FROM T005 WHERE T005_Id='{$T005_Id}'";
        $T005    = mysql_query($sql);
        $T005    = mysql_fetch_array($T005);
        
        $sql      = "SELECT T147_Valor_Unitario FROM T147 WHERE T147_T005_Id='{$T005_Id}'";
        $T147_sql = mysql_query($sql);
        
        $total = 0;
        while ($T147 = mysql_fetch_array($T147_sql)) {
            $total += $T147['T147_Valor_Unitario'];
        }
        return $total;
    }
    
    /**
     * calcularParcelaSimulacao
     *
     * @param String $T005_Id
     * @return String
     */
    public function calculaTotalGeral($T005_Id)
    {  
        $totalServicos = $this->calculaTotalServicos($T005_Id);
        $sql           = "SELECT * FROM T005 WHERE T005_Id='{$T005_Id}'";

        $T005          = mysql_query($sql);
        $T005          = mysql_fetch_array($T005);
        
        return $T005['T005_Valor_Total_Servicos'] + $T005['T005_Valor_Total'];
    }
    
    /**
     * gravarObservacao
     *
     * @param Form $form
     * @param String $T005_Id
     * @return String/bool
     */
    public function gravarObservacao($form, $T005_Id)
    {
        $obs    = $form['T005_Observacao_Pagamento'];
        $sql    = "UPDATE T005 SET T005_Observacao_Pagamento='{$obs}' WHERE T005_Id='$T005_Id'";
        $result = mysqli_query($sql);
        if (mysql_error()) {
            return mysql_error();
        } else {
            return true;
        } 
    }
    
    /**
     * gravarObservacao
     *
     * @param String $T005_Id
     * @param String $T005_Valor_Desconto
     * @param String $Campo
     * @return String/bool
     */    
    public function gravarDescontoPedido($T005_Id, $T005_Valor_Desconto, $Campo)
    {    
        require_once "old/lib/funcoes.php";
        
        if (empty($T005_Id)) {
            return "Nao encontrado o T005_Id.";
        }
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        $Valor = $T005_Valor_Desconto;
        
        $cSql_T005 = mysql_query("SELECT * FROM T005 WHERE T005_Id='$T005_Id'");
        $mT005     = mysql_fetch_array($cSql_T005);
        
        if (($mT005['T005_Flag_Travar_Pedido'] == 1) AND ($mT005['T005_Flag_Status'] != '22')) {
            return "PEDIDO TRAVADO";
        }
        
        if ($Campo == 'T005_Percentual_Desconto') {
            $Valor = formata_valor_gravar($Valor, 5);
            mysql_query("UPDATE T005
                           SET T005_Percentual_Desconto='$Valor',
                               T005_Valor_Desconto=(T005_Percentual_Desconto/100)*T005_Valor_Total_Produtos_Sem_Desconto
                         WHERE T005_Id='$T005_Id'");
            
        } else if ($Campo == 'T005_Valor_Desconto') {
            $Valor = formata_valor_gravar($Valor, 3);
            $sql   = <<<EOT
           UPDATE T005
                           SET T005_Valor_Desconto='$Valor',
                               T005_Percentual_Desconto=(T005_Valor_Desconto/T005_Valor_Total_Produtos_Sem_Desconto)*100
                         WHERE T005_Id='$T005_Id'
EOT;
            mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return '1. Erro: ' . mysql_error();
            }
            
        }
        $cSql_T006 = mysql_query("SELECT T006_Id FROM T006 WHERE T006_T005_Id='$T005_Id'");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return '2. Erro: ' . mysql_error();
        }
        while ($mT006 = mysql_fetch_array($cSql_T006)) {
            mysql_query("call T006_Gravar_Totalizacao_4('{$mT006['T006_Id']}')");
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return '2. Erro: ' . mysql_error();
            }
        }
        mysql_query("call T005_Gravar_Totalizacao_4('$T005_Id')");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return '8. Erro: ' . mysql_error();
        } else {
            mysql_query("COMMIT");
            return true;
        }
        
    }
    
    /**
     * calculaPercentualDescontoPedido
     *
     * @param String $T005_Id
     * @return String
     */    
    public function calculaPercentualDescontoPedido($T005_Id)
    {
        global $g;
        
        if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {            
            $desconto = ''; // percentual de desconto
            
            $val      = '0.00';
            $retorno  = mysql_query("SELECT SUM(T006_Valor_Preco_Original_Bruto*T006_Quantidade) as Total_Pedido, 
                                            SUM(T006_Valor_Preco_Bruto*T006_Quantidade) - ifnull(T005_Valor_Desconto_Manual,0) as Total_Tabela 
                                       FROM T006 
                                  LEFT JOIN T005 ON T005_Id = T006_T005_Id
                                      WHERE T006_T005_Id='{$T005_Id}'");
            $mT006    = mysql_fetch_array($retorno);
            if($mT006['Total_Pedido'] > 0){
                $desconto = 1 - ($mT006['Total_Tabela'] / $mT006['Total_Pedido']); // calcular o percentual de desconto dos itens confirmados           
            }
            
            $desconto = $desconto * 100;
            
            return $desconto;
        } else {
            
            $desconto = ''; // percentual de desconto            
            $val      = '0.00';
            $retorno  = mysql_query("SELECT SUM(T006_Valor_Preco_Original*T006_Quantidade) as Total_Pedido, 
                                            SUM(T006_Valor_Preco_Sem_Desconto_Unitario*T006_Quantidade) - ifnull(T005_Valor_Desconto_Manual,0) as Total_Tabela 
                                       FROM T006 
                                  LEFT JOIN T005 ON T005_Id = T006_T005_Id
                                      WHERE T006_T005_Id='{$T005_Id}'");

            $mT006    = mysql_fetch_array($retorno);

            // Verifica os itens que possuem clube de benefício a ajusta o preço tabela
            // Com o desconto fixo do clube de beneficios
            $valoresClubeBeneficio = $this->retiraValorDescontoClubeBeneficios($T005_Id);
            $mT006['Total_Tabela'] += $valoresClubeBeneficio;
            // Fim verificação clube de benefícios

            if($mT006['Total_Pedido'] > 0){                
                $desconto = 1 - ($mT006['Total_Tabela'] / $mT006['Total_Pedido']); // calcular o percentual de desconto dos itens confirmados                           
            }
            
            $desconto = $desconto * 100;
            
            return $desconto;
        } 
    }

    /**
     * retiraValorDescontoClubeBeneficios
     *
     * @param String $T005_Id
     * @return String
     */ 
    public function retiraValorDescontoClubeBeneficios($T005_Id)
    {
        global $g;
        // Utilizado no APP GRUPO DE DESCONTO
        return 0;
    }
    
    /**
     * calculaPrazoMedioAlcada
     *
     * @param String $T005_Id
     * @return String
     */     
    public function calculaPrazoMedioAlcada($T005_Id)
    {   
        global $g;
        $retorno = mysql_query("SELECT * FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'");
        $C007    = mysql_fetch_array($retorno);
        
        $retorno = mysql_query("SELECT * FROM T005 WHERE T005_Id='{$T005_Id}'");
        $T005    = mysql_fetch_array($retorno);
        $total   = $T005['T005_Valor_Total_Produtos'];
        
        if ($C007['C007_Flag_Vendedor'] == 'V') {
            $regrasUsuario = $g['conf']->ini('regrasVendedor');
        } else if ($C007['C007_Flag_Vendedor'] == 'S') {
            $regrasUsuario = $g['conf']->ini('regrasSupervisor');
        }
        
        foreach ($regrasUsuario as $regra) {
            if ($total <= $regra['valor']) {
                $prazoMedioAlcada = $regra['pm'];
                break;
            }
        }
        
        if ($prazoMedioAlcada == '') {
            $prazoMedioAlcada = 0;
        }
        
        return $prazoMedioAlcada;
    }
    
    /**
     * calculaPrazoMedioAlcadaVendedor
     *
     * @param String $T005_Id
     * @return String
     */    
    public function calculaPrazoMedioAlcadaVendedor($T005_Id)
    {    
        global $g;
        
        $retorno = mysql_query("SELECT * FROM T005 WHERE T005_Id='{$T005_Id}'");
        $T005    = mysql_fetch_array($retorno);
        $total   = $T005['T005_Valor_Total_Produtos'];
        
        $regrasUsuario = $g['conf']->ini('regrasVendedor');
        
        foreach ($regrasUsuario as $regra) {
            if ($total <= $regra['valor']) {
                $prazoMedioAlcada = $regra['pm'];
                break;
            }
        }
        
        if ($prazoMedioAlcada == '') {
            $prazoMedioAlcada = 0;
        }
        
        return $prazoMedioAlcada;
    }
    
    /**
     * calculaPrazoMedioAlcadaSupervisor
     *
     * @param String $T005_Id
     * @return String
     */    
    public function calculaPrazoMedioAlcadaSupervisor($T005_Id)
    {
        global $g;
        
        $retorno = mysql_query("SELECT * FROM T005 WHERE T005_Id='{$T005_Id}'");
        $T005    = mysql_fetch_array($retorno);
        $total   = $T005['T005_Valor_Total_Produtos'];
        
        $regrasUsuario = $g['conf']->ini('regrasSupervisor');
        
        foreach ($regrasUsuario as $regra) {
            if ($total <= $regra['valor']) {
                $prazoMedioAlcada = $regra['pm'];
                break;
            }
        }
        
        if ($prazoMedioAlcada == '') {
            $prazoMedioAlcada = 0;
        }
        return $prazoMedioAlcada;
    }
    
    /**
     * retornaMaiorDescontoPedido
     *
     * @param String $T005_Id
     * @return String
     */
    public function retornaMaiorDescontoPedido($T005_Id)
    {
        
        global $g;
        
        if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
            
            $T005MaiorDesconto  = mysql_query("SELECT T006_Percentual_Desconto_Bruto FROM T005 LEFT JOIN T006 ON T006_T005_Id=T005_Id WHERE T006_T005_Id='{$T005_Id}' ORDER BY T006_Percentual_Desconto_Bruto DESC LIMIT 1");
            $mT005MaiorDesconto = mysql_fetch_array($T005MaiorDesconto);
            $maiorDesconto      = $mT005MaiorDesconto['T006_Percentual_Desconto_Bruto'];
        } else {
            $T005MaiorDesconto  = mysql_query("SELECT T006_Percentual_Desconto FROM T005 LEFT JOIN T006 ON T006_T005_Id=T005_Id WHERE T006_T005_Id='{$T005_Id}' ORDER BY T006_Percentual_Desconto_Bruto DESC LIMIT 1");
            $mT005MaiorDesconto = mysql_fetch_array($T005MaiorDesconto);
            $maiorDesconto      = $mT005MaiorDesconto['T006_Percentual_Desconto'];
        }
        return $maiorDesconto;
    }
    
    /**
     * verificaAlcada
     *
     * @param String $T005_Id
     * @return String/bool
     */    
    public function verificaAlcada($T005_Id)
    {
        global $g;
        $desconto = $this->calculaPercentualDescontoPedido($T005_Id);
        
        $retorno = mysql_query("SELECT * FROM T005 WHERE T005_Id='{$T005_Id}'");
        $T005    = mysql_fetch_array($retorno);
        $total   = $T005['T005_Valor_Total_Produtos'];
        
        $retorno = mysql_query("SELECT * FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'");
        $C007    = mysql_fetch_array($retorno);
        
        // verificação do desconto
        $alcadaUsuario = 'D';
        if ($C007['C007_Flag_Vendedor'] == 'V') {
            $alcadaUsuario = $g['conf']->ini('alcadaDescontoVendedor');
        } else if ($C007['C007_Flag_Vendedor'] == 'S') {
            $alcadaUsuario = $g['conf']->ini('alcadaDescontoSupervisor');
        } else if ($C007['C007_Flag_Vendedor'] != 'D') {
            return false;
        }
        
        // se o usuario for diretor, nao passa por liberação 
        if ($alcadaUsuario == 'D') {
            return true;
        }
        
        /**
         * O IF ABAIXO É RESPONSÁVEL POR TRATAR DO DESCONTO POR PRODUTO (CASO A VARIÁVEL GLOBAL FOR IGUAL A 'S')
         */
        $usarAlcadaPorProduto = $g['conf']->ini('usarAlcadaPorProduto');
        if ($usarAlcadaPorProduto == "S") {
            
            $alcadaUsuario = 'D';
            
            // CASO A EMPRESA USE O PREÇO BRUTO, AS VERIFICAÇÕES SÃO FEITAS EM CIMA DO DESCONTO BRUTO
            if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
                $campoUsar = "T006_Percentual_Desconto_Bruto";
            } else {
                $campoUsar = "T006_Percentual_Desconto";
            }
            
            // verifica o tipo do usuário atual. (Vendedor, supervisor) e atribui o valor do desconto permitido que é configurado nas variáveis globais
            if ($C007['C007_Flag_Vendedor'] == 'V') {
                
                $alcadaUsuario = $g['conf']->ini('alcadaDescontoVendedorPorProduto');
                
            } else if ($C007['C007_Flag_Vendedor'] == 'S') {
                
                $alcadaUsuario = $g['conf']->ini('alcadaDescontoSupervisorPorProduto');
                
            } else if ($C007['C007_Flag_Vendedor'] != 'D') {
                
                // Retonar false e exibe a mensagem que ele não tem permissão para gerar orçamentos
                return false;
            }
            // se o usuario for diretor, nao passa por liberação 
            if ($alcadaUsuario == 'D') {
                return true;
            }
            $aT005             = mysql_query("SELECT {$campoUsar} FROM T005 LEFT JOIN T006 ON T006_T005_Id=T005_Id WHERE T006_T005_Id='{$T005_Id}' ORDER BY {$campoUsar} DESC LIMIT 1");
            $mT005             = mysql_fetch_array($aT005);
            $mT005[$campoUsar] = number_format($mT005[$campoUsar], 2, ".", " ");
            $alcadaUsuario     = number_format($alcadaUsuario, 2, ".", " ");
            
            //die("Liberou alçada valor: ". $T003['T003_Liberou_Alcada_Valor_Total'] . " Total orçamento: ". $total);
            
            //die("Liberou Alçada valor total: ".$T005['T005_Liberou_Alcada_Valor_Total'] . " Valor Total do pedido: ".$total . " Flag Status: " . $T005['T005_Flag_Status'] . " 1");
            
            //die("Alçada usuário: ".$alcadaUsuario." Valor desconto: ".$mT005[$campoUsar]);
            
            // CASO JÁ TENHA PASSADO PELA LIBERAÇÃO (DADOS DA LIBERAÇÃO PREENCHIDOS)
            if ($T005['T005_Liberou_Alcada_C007_Id'] > 0 && $T005['T005_Liberou_Alcada_Data'] > 0 && ($T005['T005_Liberou_Alcada_Desconto'] >= 0 || $T005['T005_Liberou_Alcada_Prazo_Medio'] >= 0) || ($T005['T005_Flag_Status'] > 0 && $T005['T005_Flag_Status'] != '' && $T005['T005_Flag_Status'] != 14 && $T005['T005_Flag_Status'] != 15 && $T005['T005_Flag_Status'] != 13)) {
                
                if ($T005['T005_Liberou_Alcada_Valor_Total'] == $total || ($T005['T005_Flag_Status'] > 0 && $T005['T005_Flag_Status'] != '' && $T005['T005_Flag_Status'] != 14 && $T005['T005_Flag_Status'] != 15 && $T005['T005_Flag_Status'] != 13)) {
                    return true;
                    
                } else {
                    // VERIFICA SE A ALÇADA DO USUÁRIOATUAL É MENOR OU IGUAL AO MAIOR DESCONTO DADO
                    if (($alcadaUsuario < $mT003[$campoUsar]) || $T003['T005_Liberou_Alcada_Valor_Total'] != $total) {
                        
                        return "O desconto dado no pedido exige liberação. Pedir liberação? (1)";
                    }
                }
                
            } else {
                
                // VERIFICA SE A ALÇADA DO USUÁRIOATUAL É MENOR OU IGUAL AO MAIOR DESCONTO DADO
                if ($alcadaUsuario < $mT003[$campoUsar]) {
                    
                    return "O desconto dado no pedido é maior que o permitido. Pedir liberação? (2)";
                }
            }
        }
        
        if ($desconto > $alcadaUsuario) {
            if ($T005['T005_Liberou_Alcada_C007_Id'] > 0 && $T005['T005_Liberou_Alcada_Data'] > 0 && ($T005['T005_Liberou_Alcada_Desconto'] > 0 || $T005['T005_Liberou_Alcada_Prazo_Medio'] > 0) || ($T005['T005_Flag_Status'] > 0 && $T005['T005_Flag_Status'] != '' && $T005['T005_Flag_Status'] != 14 && $T005['T005_Flag_Status'] != 15 && $T005['T005_Flag_Status'] != 13)) {
                
                if (($T005['T005_Liberou_Alcada_Valor_Total'] == $total) || ($T005['T005_Flag_Status'] > 0 && $T005['T005_Flag_Status'] != '' && $T005['T005_Flag_Status'] != 14 && $T005['T005_Flag_Status'] != 15 && $T005['T005_Flag_Status'] != 13)) {
                    
                    return true;
                    
                } else {
                    return 'A liberação deste pedido exige maior alçada, pedir liberação?(1)';
                }
            } else {
                return 'A liberação deste pedido exige maior alçada, pedir liberação?(2)';
            }
        }
        
        // verificação do prazo médio   
        $prazoMedioAlcada = $this->calculaPrazoMedioAlcada($T005_Id);
        
        // verificar se o prazo médio das parcelas geradas para este pedido é menor ou igual ao $prazoMedioAlcada
        
        $retorno    = mysql_query("SELECT AVG(T090_Prazos_Dias) FROM T090 WHERE T090_T005_Id='{$T005_Id}'");
        $T090       = mysql_fetch_array($retorno);
        $prazoMedio = $T090['AVG(T090_Prazos_Dias)'];
        
        if ($prazoMedio > $prazoMedioAlcada) {
            if ($T005['T005_Liberou_Alcada_C007_Id'] > 0 && $T005['T005_Liberou_Alcada_Data'] > 0 && ($T005['T005_Liberou_Alcada_Desconto'] > 0 || $T005['T005_Liberou_Alcada_Prazo_Medio'] > 0)) {
                if ($T005['T005_Liberou_Alcada_Prazo_Medio'] == $prazoMedio) {
                    return true;
                } else {
                    return 'A liberação deste orçamento exige maior alçada, pedir liberação?(3)';
                }
            } else {
                return 'A liberação deste orçamento exige maior alçada, pedir liberação?(4)';
            }
        }
        
        //Verificação de açada por saldo mínimo de produto
        $retornoSaldo = $this->verificaFuturoSaldoProduto($T005_Id);
        if ($retornoSaldo > 0) {
            return 'A liberação deste orçamento exige maior alçada, pedir liberação?(5)';
        }
        return true;
    }
    
    /**
     * enviarParaAnalise
     *
     * @param String $T005_Id
     * @param String $alcada
     * @return bool
     */    
    public function enviarParaAnalise($T005_Id, $alcada,$acao=false)
    {
        global $g;
        mysql_query("UPDATE T005 SET T005_Flag_Status_Liberacao='1', T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1), T005_Flag_Status='13' WHERE T005_Id='{$T005_Id}'");
        $T005=mysql_query("SELECT T005_Motivo_Pedido_Liberacao FROM T005 WHERE T005_Id='{$T005_Id}'");
        $mT005 = mysql_fetch_array($T005);

        if($acao=='imprimir'){
            $msgAcao="Pedido impresso";
        } else if($acao=='faturar'){
            $msgAcao="Faturou pedido";
        } else if($acao=='enviarEmail'){
            $msgAcao="Envio de e-mail";
        }
        mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'{$msgAcao}','')");
        $sql  = "insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'{$mT005['T005_Motivo_Pedido_Liberacao']}','')";
        mysql_query($sql);
        if (mysql_error()) {
            return "Erro 1: ".mysql_error()." <br />SQL: ".$sql;
        } else {
            return true;
        }
    }
    
    /**
     * motivoPedidoLiberacao
     *
     * @param String $T005_Id
     * @param bool $restricaoAtual
     * @return String/bool
     */    
    public function motivoPedidoLiberacao($T005_Id, $restricaoAtual = false)
    {
        global $g;
        $r_acaoId = $T005_Id;
        
        //Calcula Desconto por alçada
        $descontoPedido             = $this->calculaPercentualDescontoPedido($r_acaoId);
        $descontoVendedor           = gCorrigeNumeroInverte($g['conf']->ini('alcadaDescontoVendedor'));
        $descontoSupervisor         = gCorrigeNumeroInverte($g['conf']->ini('alcadaDescontoSupervisor'));
        //Calcula prazo médio por alçada
        $prazoMedioAlcadaVendedor   = $this->calculaPrazoMedioAlcadaVendedor($r_acaoId);
        $prazoMedioAlcadaSupervisor = $this->calculaPrazoMedioAlcadaSupervisor($r_acaoId);
        $retorno                    = mysql_query("SELECT ifnull(AVG(T090_Prazos_Dias),0.00) as PrazoMedio FROM T090 WHERE T090_T005_Id='{$r_acaoId}'");
        $T005                       = mysql_fetch_array($retorno);
        $prazoMedio                 = $T005['PrazoMedio'];
        $retornoSaldoMinimo         = $this->verificaFuturoSaldoProduto($r_acaoId, $restricaoAtual);
        
        $retornoT005 = mysql_query("SELECT T005_Valor_Total_Produtos,
                                        T005_Liberou_Alcada_Valor_Total,
                                        T005_Liberou_Alcada_C007_Id,
                                        T005_Liberou_Alcada_Data,
                                        T005_Liberou_Alcada_Desconto,
                                        T005_Liberou_Alcada_Prazo_Medio 
                                        FROM T005 
                                        WHERE T005_Id='{$r_acaoId}'");
        $mT005       = mysql_fetch_array($retornoT005);
        $total       = $mT005['T005_Valor_Total_Produtos'];
        
        $motivoPedidoLiberacao = "";
        if ($retornoSaldoMinimo > 0) {
            $motivoPedidoLiberacao .= "*Limite de estoque mínimo ultrapassado.";
        }
        
        //Verificações de alçada
        if (($descontoPedido <= $descontoVendedor) && ($prazoMedio <= $prazoMedioAlcadaVendedor) && ($retornoSaldoMinimo == 0)) {
            // NÃO PRECISA DE LIBERAÇÃO DE DESCONTO E PRAZO MÉDIO
        } elseif (($descontoPedido <= $descontoSupervisor) && ($prazoMedio <= $prazoMedioAlcadaSupervisor)) {
            //Constrói variável motivo liberação
            if ($mT005['T005_Liberou_Alcada_C007_Id'] > 0 && $mT005['T005_Liberou_Alcada_Data'] > 0 && ($mT005['T005_Liberou_Alcada_Desconto'] >= 0 || $mT005['T005_Liberou_Alcada_Prazo_Medio'] >= 0)) {
                
                if ($mT005['T005_Liberou_Alcada_Valor_Total'] != $total || $mT005['T005_Liberou_Alcada_Prazo_Medio'] != $prazoMedio) {
                    
                    if (($descontoPedido > $descontoVendedor) && $g['C007']['C007_Flag_Vendedor'] == 'V') {
                        
                        $motivoPedidoLiberacao .= "\n*Desconto usado é maior que o permitido. ";
                    }
                    if (($prazoMedio > $prazoMedioAlcadaVendedor) && $g['C007']['C007_Flag_Vendedor'] == 'V') {
                        
                        $motivoPedidoLiberacao .= "\n*Prazo médio é maior que o permitido. ";
                    }
                }
            }
        } else if ($mT005['T005_Liberou_Alcada_Valor_Total'] != $total || $mT005['T005_Liberou_Alcada_Prazo_Medio'] != $prazoMedio) {
            
            if (($descontoPedido > $descontoVendedor) && $g['C007']['C007_Flag_Vendedor'] == 'V') {
                
                $motivoPedidoLiberacao .= "\n*Desconto usado é maior que o permitido. ";
            }
            if (($prazoMedio > $prazoMedioAlcadaVendedor) && $g['C007']['C007_Flag_Vendedor'] == 'V') {
                
                $motivoPedidoLiberacao .= "\n*Prazo médio é maior que o permitido. ";
            }
        } else {
            //Constrói variável motivo liberação
            if ($mT005['T005_Liberou_Alcada_C007_Id'] > 0 && $mT005['T005_Liberou_Alcada_Data'] > 0 && ($mT005['T005_Liberou_Alcada_Desconto'] >= 0 || $mT005['T005_Liberou_Alcada_Prazo_Medio'] >= 0)) {
                
                if ($mT005['T005_Liberou_Alcada_Valor_Total'] != $total || $mT005['T005_Liberou_Alcada_Prazo_Medio'] != $prazoMedio) {
                    
                    if (($descontoPedido > $descontoSupervisor) && $g['C007']['C007_Flag_Vendedor'] != 'D') {
                        
                        $motivoPedidoLiberacao .= "\n*Desconto usado é maior que o permitido. ";
                    }
                    if (($prazoMedio > $prazoMedioAlcadaSupervisor) && $g['C007']['C007_Flag_Vendedor'] != 'D') {
                        
                        $motivoPedidoLiberacao .= "\n*Prazo médio é maior que o permitido. ";
                    }
                }
            }
        }
        return $motivoPedidoLiberacao;   
    }
    
    /**
     * ven002AplicarPercentualDesconto
     *
     * @param String $T006_Id
     * @param String $campoEditado
     * @param String $valorAntigo
     * @param String $novoValor
     * @return String/bool
     */    
    public function ven002AplicarPercentualDesconto($T006_Id, $campoEditado, $valorAntigo, $novoValor)
    {
        global $g;
        if ($T006_Id <= 0) {
            return "T006_Id nao existe!";
        }
        $Campo_Editado          = $campoEditado;
        $Conteudo_Campo_Editado = $novoValor;
        
        $T005    = mysql_query("SELECT T006_T005_Id, T005_Casas_Decimais_Preco FROM T006 LEFT JOIN T005 ON T005_Id=T006_T005_Id WHERE T006_Id='{$T006_Id}'");
        $mT005   = mysql_fetch_array($T005);
        $T005_Id = $mT005['T006_T005_Id'];
        
        if (empty($Campo_Editado)) {
            return "Campo_Editado nao existe!";
        }
        
        if ($T006_Id <= 0) {
            return "T006_Id nao existe!";
        }
        $C007  = mysql_query("SELECT * FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'");
        $mC007 = mysql_fetch_array($C007);
        
        $cC004 = mysql_query("SELECT * FROM C004 WHERE C004_Id='{$g['empresaAtual']}'");
        $mC004 = mysql_fetch_array($cC004);
        
        $cClausula = " SELECT ";
        $cClausula .= " *  ";
        $cClausula .= " FROM T006 ";
        $cClausula .= " LEFT JOIN D009 ON D009_Id=T006_D009_Id ";
        $cClausula .= " LEFT JOIN D049 ON D049_Id=D009_D049_Id ";
        $cClausula .= " LEFT JOIN D001 ON D001_Id=D049_D001_Id ";
        $cClausula .= " LEFT JOIN D037 ON D037_Id=T006_D037_Id ";
        $cClausula .= " WHERE T006_Id='$T006_Id' ";
        $T006  = mysql_query("$cClausula");
        $mT006 = mysql_fetch_array($T006);
        
        if ($Campo_Editado == 'T006_Valor_Preco_Sem_Desconto_Unitario') {
            $Conteudo_Campo_Editado = gCorrigeNumeroInverte($Conteudo_Campo_Editado, $mC004['C004_Casas_Decimais_Preco'] + 1);
            
            if ($Conteudo_Campo_Editado < $mT006['T006_Valor_Custo_Unitario']) {
                //return 'Preço inválido';
            }
            $sql = "UPDATE T006 SET T006_Valor_Preco_Sem_Desconto_Unitario=round('$Conteudo_Campo_Editado', {$mT005['T005_Casas_Decimais_Preco']}),
                                    T006_Valor_Preco_Unitario=T006_Valor_Preco_Sem_Desconto_Unitario,
                                    T006_Percentual_Desconto=if(T006_Valor_Preco_Original>0,(1-(T006_Valor_Preco_Sem_Desconto_Unitario/T006_Valor_Preco_Original))*100,0),
                                    T006_Valor_Desconto_Unitario=if(T006_Valor_Preco_Original>0,(T006_Valor_Preco_Original*($Conteudo_Campo_Editado/100)),0)
                              WHERE T006_Id='$T006_Id'";
            mysql_query($sql);
            if (mysql_error()) {
                $erro = mysql_error();
                return "1 .Erro: " . $erro . ' - ' . str_replace("\n", ' ', $sql);
            }
            
        } else if ($Campo_Editado == 'T006_Valor_Preco_Bruto') {
            
            $Conteudo_Campo_Editado = gCorrigeNumeroInverte($Conteudo_Campo_Editado, $mC004['C004_Casas_Decimais_Preco'] + 1);
            
            $sql = "UPDATE T006 SET T006_Valor_Preco_Bruto='$Conteudo_Campo_Editado',
                                        T006_Percentual_Desconto_Bruto=if(T006_Valor_Preco_Original_Bruto>0,(1-(T006_Valor_Preco_Bruto/T006_Valor_Preco_Original_Bruto))*100,0),
                                        T006_Valor_Desconto_Unitario_Bruto=if(T006_Valor_Preco_Original_Bruto>0,(T006_Valor_Preco_Original_Bruto*($Conteudo_Campo_Editado/100)),0)
                              WHERE T006_Id='$T006_Id'";
            mysql_query($sql);
            if (mysql_error()) {
                $erro = mysql_error();
                return "1 .Erro: " . $erro;
            }
        } elseif ($Campo_Editado == 'T006_Percentual_Desconto') {
            if (!empty($Conteudo_Campo_Editado)) {
                $Conteudo_Campo_Editado = gCorrigeNumeroInverte($Conteudo_Campo_Editado, 5);
            } else {
                $Conteudo_Campo_Editado = 0;
            }
            //return "1: ".number_format($Conteudo_Campo_Editado,2)."<br />2: ".number_format($mT006['T006_Percentual_Desconto'],2);
            if (($mT006['T006_Valor_Preco_Original'] - ($mT006['T006_Valor_Preco_Original'] * ($Conteudo_Campo_Editado / 100))) < $mT006['T006_Valor_Custo_Unitario']) {
                // return "Desconto inválido";
            }
            
            $sql = "UPDATE T006 SET T006_Percentual_Desconto='$Conteudo_Campo_Editado',
                                        T006_Valor_Preco_Sem_Desconto_Unitario=round(T006_Valor_Preco_Original-(T006_Valor_Preco_Original*($Conteudo_Campo_Editado/100)), {$mT005['T005_Casas_Decimais_Preco']}),
                                        T006_Valor_Preco_Unitario=T006_Valor_Preco_Sem_Desconto_Unitario,
                                        T006_Valor_Desconto_Unitario=(T006_Valor_Preco_Original*($Conteudo_Campo_Editado/100))
                                  WHERE T006_Id='$T006_Id'";
            //return $sql;
            mysql_query($sql);
            if (mysql_error()) {
                $erro = mysql_error();
                return "2 .Erro: " . $erro;
            }

            if($g['C031']['utilizaUnidadeConversaoVenda'] == 'S'){
                $T005A = mysql_query("SELECT T005A_Id
                                        FROM T005A
                                       WHERE T005A_T005_Id = '{$mT006['T006_T005_Id']}'
                                         AND T005A_Flag_Conversao_Unidade = 'S'");

                if(mysql_num_rows($T005A) > 0 ){
                    // Se utiliza unidade conversão precisa atualizar os dados da tabela estendida
                    require_once('bibliotecas/classes/CAD002.php');
                    $CAD002 = new CAD002();
                    $CAD002->conversaoUnidadeProduto(false, $T006_Id, false, true);
                }          
            }
        } elseif ($Campo_Editado == 'T006_Percentual_Desconto_Bruto') {
            if (!empty($Conteudo_Campo_Editado)) {
                $Conteudo_Campo_Editado = gCorrigeNumeroInverte($Conteudo_Campo_Editado, 5);
            } else {
                $Conteudo_Campo_Editado = 0;
            }
            //return "1: ".number_format($Conteudo_Campo_Editado,2)."<br />2: ".number_format($mT006['T006_Percentual_Desconto_Bruto'],2);
            //if (number_format($Conteudo_Campo_Editado, 2) != number_format($mT006['T006_Percentual_Desconto_Bruto'], 2)) {
                
                if (($mT006['T006_Valor_Preco_Original_Bruto'] - ($mT006['T006_Valor_Preco_Original_Bruto'] * ($Conteudo_Campo_Editado / 100))) < $mT006['T006_Valor_Preco_Original_Bruto']) {
                    //return "Desconto inválido";
                }
                
                $sql = "UPDATE T006 SET T006_Percentual_Desconto_Bruto='$Conteudo_Campo_Editado',
                                            T006_Valor_Preco_Bruto=T006_Valor_Preco_Original_Bruto-(T006_Valor_Preco_Original_Bruto*($Conteudo_Campo_Editado/100)),
                                            T006_Valor_Preco_Unitario=T006_Valor_Preco_Sem_Desconto_Unitario,
                                            T006_Valor_Desconto_Unitario_Bruto=(T006_Valor_Preco_Original_Bruto*($Conteudo_Campo_Editado/100))
                                      WHERE T006_Id='$T006_Id'";
                mysql_query($sql);
                if (mysql_error()) {
                    $erro = mysql_error();
                    return "2 .Erro: " . $erro;
                }
            //}
        } else {
            
            $Conteudo_Campo_Editado = strtoupper($Conteudo_Campo_Editado);
            $Conteudo_Campo_Editado = addslashes($Conteudo_Campo_Editado);
            
            mysql_query("UPDATE T006 SET $Campo_Editado='$Conteudo_Campo_Editado'
                                  WHERE T006_Id='$T006_Id'");
            
        }
        
        $cSql = mysql_query("call T006_Gravar_Totalizacao_4('$T006_Id');");
        //$cSql=sql_query("call T003_Gravar_Totalizacao('$CRM_T003_Id');");
        return true;
    }
    
    /**
     * importarDocsPedido
     *
     * @param Form $form
     * @return String/bool
     */    
    public function importarDocsPedido($form)
    {
        global $g;
        $T005_Id               = $form->campoValorEnviado('T167_T005_Id');
        $T167_C007_Id          = $form->campoValorEnviado('T167_C007_Id');
        $T167_Data_Hora_UpLoad = $form->campoValorEnviado('T167_Data_Hora_UpLoad');
        $T005_Id_Importar      = $form->campoValorEnviado('Numero_Pedido');
        //Insere os documentos do orçamento no pedido.
        $sqlDocumentos         = "SELECT * FROM T167 WHERE T167_T005_Id='{$T005_Id_Importar}'";
        $resultDocumentos      = mysql_query($sqlDocumentos);
        if (mysql_num_rows($resultDocumentos) <= 0) {
            return "Não existe nenhum pedido com o número informado ou o pedido não possuí nenhum documento. Favor verificar.";
        }
        while ($linhaDocumentos = mysql_fetch_array($resultDocumentos)) {
            
            $sql            = <<<EOT
            INSERT INTO T167 
            (T167_T005_Id, T167_C007_Id, T167_Nome_Arquivo, T167_Descricao, T167_Data_Hora_Upload)
            VALUES
            ('{$T005_Id}','{$T167_C007_Id}','{$linhaDocumentos['T167_Nome_Arquivo']}','{$linhaDocumentos['T167_Descricao']}','{$T167_Data_Hora_UpLoad}')
EOT;
            $resultCopiaDoc = mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return 'Não foi possível importar os arquivos, erro SQL: ' . mysql_error();
            }
            
            //----Copia os arquivos para o pedido atual
            //Pega a extensão do arquivo
            $pathinfo              = pathinfo($linhaDocumentos['T167_Nome_Arquivo']);
            $pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
            
            //Pega o caminho do arquivo no orçamento
            $dirOrigem = "{$g['pathDados']}pedido";
            is_dir($dirOrigem) OR mkdir($dirOrigem, 0777, true);
            $fileOrigem = $dirOrigem . '/' . $linhaDocumentos['T167_Id'] . '.' . $pathinfo['extension'];
            
            $dirDestino = "{$g['pathDados']}pedido";
            is_dir($dirDestino) OR mkdir($dirDestino, 0777, true);
            $fileDestino = $dirDestino . '/' . $g['mysqlLastId'] . '.' . $pathinfo['extension'];
            copy($fileOrigem, $fileDestino);
        }
        return true;
    }
    
    /**
     * ven002VerificaCancelarPedido
     *
     * @param Form $form
     * @return String/bool
     */    
    public function ven002VerificaCancelarPedido($form)
    {
        global $g;
        
        $T005_Id = $form->campoValorEnviado('T005_Id');
        
        $erro    = "";
        $retorno = $this->ven002CancelarPedido($form);
        if ($retorno !== true) {
            $erro = "Pedido " . $form->campoValorEnviado('T005_Id') . " Erro: " . $retorno;
        }
        
        $T005 = mysql_query("SELECT T005_Id FROM T005 WHERE T005_T005_Id_Origem='{$T005_Id}'");
        if (mysql_num_rows($T005) > 0) {
            
            $mT005 = mysql_fetch_array($T005);
            $form->campoSetarValor('T005_Id', $mT005['T005_Id'], true);
            $retorno = $this->ven002CancelarPedido($form);
            
            if ($retorno !== true) {
                $erro .= "<br />Pedido " . $form->campoValorEnviado('T005_Id') . " Erro: " . $retorno;
            }
        }
        if ($erro == "") {
            return true;
        } else {
            return $erro;
        }
    }
    
    /**
     * ven002CancelarPedido
     *
     * @param Form $form
     * @return String/bool
     */    
    public function ven002CancelarPedido($form)
    {
        global $g;
        $T005_Id                        = $form->campoValorEnviado('T005_Id');
        $T005_D047_Id                   = $form->campoValorEnviado('T005_D047_Id');
        $T005_Observacao_Pedido_Perdido = $form->campoValorEnviado('T005_Observacao_Pedido_Perdido');
        
        /**
         * Verificações iniciais
         */
        mysql_query("START TRANSACTION");
        mysql_query("SET AUTOCOMMIT=0");

        $cSql_T007 = mysql_query("SELECT T007_Id
                                    FROM T007 
                                   WHERE T007_T005_Id='{$T005_Id}' 
                                     AND T007_Flag_Cancelada!='S' 
                                     AND T007_Flag_Inutilizado!='S' 
                                     AND T007_Protocolo_Inutilizacao=''");
        if (mysql_num_rows($cSql_T007) > 0) {
            return "Este pedido já foi faturado, não pode ser cancelado.";
        }
        
        $cSql_T005 = mysql_query("SELECT T005_Id,
                                         T005_Flag_Status,
                                         T005_Flag_Almoxarifado
                                    FROM T005 
                                   WHERE T005_Id='{$T005_Id}'");
        $mT005     = mysql_fetch_array($cSql_T005);    

        // Verifica se existem produtos separados no estoque
        //$cSql_T006Estoque = mysql_query("SELECT T006_Id FROM T006 WHERE T006_T005_Id = '{$T005_Id}' AND (T006_Quantidade_Separacao > 0 );");
        $cSql_T006Estoque = mysql_query("SELECT T238_Id 
                                           FROM T006
                                      LEFT JOIN T238 ON T238_T006_Id=T006_Id
                                          WHERE T006_T005_Id='{$T005_Id}' 
                                            AND T238_Quantidade_Separacao >0");
        if (mysql_num_rows($cSql_T006Estoque) > 0) {
            mysql_query("ROLLBACK");
            return "Não pode ser cancelado porque possui produtos já separados no estoque.<br/>Por favor, verifique com o setor de expedição.";
        }
        
        // Busca os dados do usuario
/*         $resultado = mysql_query("SELECT * 
                                    FROM C007 
                                   WHERE C007_Id='{$g['usuarioAtual']}'");
        $mUsuario  = mysql_fetch_array($resultado);
        
        $Tem_Acesso = 'N'; */
        
        if ($mT005['T005_Flag_Status'] == 8 && $g['C031']['permiteRecuperarPedidoCancelado']=='N') {
            
            return "Este pedido já está cancelado e não é permitido recuperar pedidos cancelados. Verifique a configuração de sua empresa no menu Configuração->Opções->Vendas->Pedido->Campo 'Permite recuperar pedido cancelado?'";
            
        } else if (($mT005['T005_Flag_Almoxarifado'] == 'S') AND ($mT005['T005_Flag_Status'] != '9')) {
            
            return "Este pedido esta no ALMOXARIFADO, não pode ser cancelado.";
            
        }
        
/*        $T006 = mysql_query("SELECT T006_T005_Id,
                                      T006_Flag_Status,
                                      T006_Codigo_Produto,
                                      T006_Quantidade_Separacao,
                                      T012_T011_Id,
                                      T014_Id,
                                      T013_Numero_Nota_Fiscal,
                                      T006_D009_Id
                                 FROM T006 
                            LEFT JOIN T012 ON T012_T006_Id=T006_Id
                            LEFT JOIN T014 ON T014_T012_Id=T012_Id
                            LEFT JOIN T013 ON T013_Id=T014_T013_Id     
                            WHERE T006_T005_Id='{$T005_Id}'
                             group by T006_Id");
        
        
        while ($mT006 = mysql_fetch_array($T006)) {
            if ($mT006['T012_T011_Id'] > 0) {
                if ($mT006['T014_Id'] > 0) {
                    return <<<EOT
                        {$mT006['T006_Codigo_Produto']} possui OC recebida: {$mT005['T012_T011_Id']} NF {$mT005['T013_Numero_Nota_Fiscal']}
EOT;
                } else {
                    return <<<EOT
                        {$mT006['T006_Codigo_Produto']} possui OC pendente: {$mT006['T012_T011_Id']};
EOT;
                }
            }
        }
        ////

*/
        
        $sql = mysql_query("SELECT T219_Id 
                              FROM T006 
                         LEFT JOIN T004 ON T004_Id = T006_T004_Id 
                         LEFT JOIN T219 ON T219_Id=T006_T219_Id OR T219_Id=T004_T219_Id 
                         LEFT JOIN T225 ON T225_T219_Id=T219_Id 
                             WHERE T006_T005_Id='{$T005_Id}' 
                               AND T225_Id > 0");

        if(mysql_num_rows($sql) > 0){
            return 'Este pedido gerou ordem de compra e não pode ser cancelado! Favor verificar.';  
        }

        //-- ADICIONA BARRA INVERTIDA PARA NAO DAR ERRO NAS ASPAS:
        $T005_D047_Id                   = AddSlashes($T005_D047_Id);
        $T005_Observacao_Pedido_Perdido = AddSlashes($T005_Observacao_Pedido_Perdido);
        
        //-- ELIMINA OS ESPAÇOS DOS CAMPOS:
        $T005_D047_Id                   = Trim($T005_D047_Id);
        $T005_Observacao_Pedido_Perdido = Trim($T005_Observacao_Pedido_Perdido);
        
        //-- CONVERTE EM MINUSCULO E MAIUSCULO:
        $T005_D047_Id                   = StrToMaiusculo($T005_D047_Id);
        $T005_Observacao_Pedido_Perdido = StrToMaiusculo($T005_Observacao_Pedido_Perdido);
        
        //-- VALIDAÇÕES DOS CAMPOS:
        if (strlen(trim($T005_D047_Id)) == 0) {
            return "Necessario o preenchimento do campo: Motivo";
        }
        if (strlen(trim($T005_Observacao_Pedido_Perdido)) == 0) {
            
            return "Necessario o preenchimento do campo: Observação";
        }
        
        $cSql_T005 = mysql_query("SELECT T005_Flag_Status 
                                    FROM T005
                                   WHERE T005_Flag_Status='8' 
                                     AND T005_Id='{$T005_Id}'");

        if (mysql_num_rows($cSql_T005) > 0) {
            $T005_Flag_Status        = '';
            $T005_Flag_Travar_Pedido = '0';
            $T005_Flag_Situacao      = ' ';
            $T005_Flag_Almoxarifado  = ' ';
            $historicoPedido         = 'Pedido recuperado';
        } else {
            $T005_Flag_Status        = '8';
            $T005_Flag_Travar_Pedido = '1';
            $T005_Flag_Situacao      = ' ';
            $T005_Flag_Almoxarifado  = ' ';
            $historicoPedido         = 'Pedido cancelado';
        }
        
        $T005_Observacao_Pedido_Perdido = date('d/m/Y H:i:s') . ' - ' . $T005_Observacao_Pedido_Perdido;

        $cClausula = " UPDATE T005 SET ";
        $cClausula .= " T005_Flag_Marcado='N', ";
        $cClausula .= " T005_D047_Id='{$T005_D047_Id}', ";
        $cClausula .= " T005_Observacao_Pedido_Perdido='{$T005_Observacao_Pedido_Perdido}', ";
        $cClausula .= " T005_Flag_Status='{$T005_Flag_Status}', ";
        $cClausula .= " T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1), ";
        $cClausula .= " T005_Flag_Travar_Pedido='{$T005_Flag_Travar_Pedido}', ";
        $cClausula .= " T005_Flag_Situacao='{$T005_Flag_Situacao}', ";
        $cClausula .= " T005_Flag_Almoxarifado='{$T005_Flag_Almoxarifado}',  ";
        $cClausula .= " T005_Flag_Status_Servicos=3  ";
        $cClausula .= " WHERE T005_Id='{$T005_Id}' ";
        //return $cClausula;
        $cSql  = mysql_query($cClausula);
        $cErro = mysql_error();
        if (empty($cErro)) {
            //Cria histórico do pedido
            $cClausula = "INSERT INTO T178 (T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ('{$T005_Id}','{$g['usuarioAtual']}',CURRENT_TIMESTAMP(),'{$historicoPedido}','{$T005_Observacao_Pedido_Perdido}')";
            mysql_query($cClausula);
            /*$cErro=mysql_error();
            if (empty($cErro)){
            mysql_query("ROLLBACK");
            return "0. Erro ao cancelar o pedido. Query: ".$cClausula;
            }*/
        } else {
            mysql_query("ROLLBACK");
            return "1. Erro ao cancelar o pedido. Query: " . $cClausula;
        }
        
        $cSql_T005 = mysql_query("SELECT T005_Flag_Status 
                                    FROM T005 
                                   WHERE T005_Flag_Status='8' 
                                     AND T005_Id='{$T005_Id}'");

        if (mysql_num_rows($cSql_T005) > 0) {
            mysql_query("UPDATE T006 SET T006_Flag_Status='3', 
                                         T006_T006_Id_Dividir = '0' 
                                   WHERE T006_T005_Id='{$T005_Id}'");
            if (mysql_error()) {
                
                mysql_query("ROLLBACK");
                return "2. Erro ao cancelar o pedido";
            }
            
            mysql_query("UPDATE T185 
                      LEFT JOIN T006 ON T006_Id=T185_T006_Id 
                            SET T185_Flag_Status = '2' 
                          WHERE T006_T005_Id='{$T005_Id}';");
            
            if (mysql_error()) {
                
                mysql_query("ROLLBACK");
                return "3. Erro ao cancelar o pedido";
            }
        } else {
            
            mysql_query("UPDATE T006 SET T006_Flag_Status='' WHERE T006_T005_Id='{$T005_Id}'");
            if (mysql_error()) {
                
                mysql_query("ROLLBACK");
                return "4. Erro ao cancelar o pedido";
            }
            mysql_query("UPDATE T185 
                      LEFT JOIN T006 ON T006_Id=T185_T006_Id 
                            SET T185_Flag_Status = '0' 
                          WHERE T006_T005_Id='{$T005_Id}';");
            if (mysql_error()) {
                
                mysql_query("ROLLBACK");
                return "5. Erro ao cancelar o pedido";
            }
        }
        
        mysql_query("UPDATE D009
                  LEFT JOIN T006 ON T006_D009_Id=D009_Id
                        SET D009_Quantidade_Estoque_Real='0'
                      WHERE T006_T005_Id='{$T005_Id}'");
        if (mysql_error()) {
            
            mysql_query("ROLLBACK");
            return "6. Erro ao cancelar o pedido";
        }

        //-- SE ACONTECER DO PEDIDO SER CANCELADO  E ESTAR NO ALMOX, O MESMO É RETIRADO DO ALMOX
        mysql_query("UPDATE T005 SET T005_Flag_Almoxarifado='N' WHERE T005_Id='{$T005_Id}'");
        if (mysql_error()) {
            
            mysql_query("ROLLBACK");
            return "7. Erro ao cancelar o pedido";
        }
        
        //-- ELIMINA O RELACIONAMENTO DO PEDIDO CANCELADO COM O ITEM DO ORCAMENTO
        mysql_query("UPDATE T006 SET T006_T004_Id=0 WHERE T006_T005_Id='{$T005_Id}'");
        if (mysql_error()) {
            
            mysql_query("ROLLBACK");
            return "8. Erro ao cancelar o pedido";
        }
        
        //-- RECALCULA TOTAL DO PEDIDO CANCELADO
        mysql_query("call T005_Gravar_Totalizacao_4('{$T005_Id}')");
        if (mysql_error()) {
            
            mysql_query("ROLLBACK");
            return "9. Erro ao cancelar o pedido";
        }
        
        //Atualiza estoque dos produtos do pedido
        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();
        
        $atualizarT006 = mysql_query("SELECT T006_D009_Id
                                        FROM T006 
                                       WHERE T006_T005_Id='{$T005_Id}'");
        
        while ($mAtualizarT006 = mysql_fetch_array($atualizarT006)) {
            //D009 do produto eh diferente do que estou retornando
            $atualizarEstoque = $CAD002->D001_reprocessa_historico(false, $mAtualizarT006['T006_D009_Id'], 50, true);
            if ($atualizarEstoque !== true) {
                mysql_query("ROLLBACK");
                return "Erro ao atualizar estoque dos produtos. " . $atualizarEstoque;
            }
        }
        
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return <<<EOT
            Ocorreu um erro ao tentar alterar o registro<br /><br />Erro: {$cErro}<br /><br />Clausula: {$cClausula};
EOT;
        } else {
            mysql_query("COMMIT");

            require_once('bibliotecas/webservices/cyberlog/integracaoWMS.php');
            $WMS = new WMS();

            if($WMS->isEnabled() && $g['C031']['cyberlogLockOrder'] == 'S') {
                $WMS->cancelarPedido($T005_Id);
                
                mysql_query("UPDATE T005A SET T005A_Data_Envio_WMS = '0000-00-00 00:00:00' WHERE T005A_T005_Id = '{$T005_Id}'");
            }

            return true;
        }
    }
    
    /**
     * verificaFuturoSaldoProduto
     *
     * @param String $T005_Id
     * @return String/bool
     */    
    public function verificaFuturoSaldoProduto($T005_Id, $restricaoAtual = false)
    {
        global $g;
        $saldoMinimo = $g['C031']['alcadaQuantidadeMinimaEstoque'];
        if ($saldoMinimo <= 0) {
            return 0;
        }
        $sqlT005 = mysql_query("SELECT  T006_Quantidade, 
                                        D009_Quantidade_Estoque_Liquido,
                                        T006_Liberou_Alcada_Saldo_Futuro, 
                                        C007_Flag_Vendedor,
                                        (D009_Quantidade_Estoque_Liquido-T006_Quantidade) as SaldoFuturo 
                                        FROM T006
                                        LEFT JOIN D009 ON D009_Id=T006_D009_Id
                                        LEFT JOIN T005 ON T005_Id=T006_T005_Id 
                                        LEFT JOIN C007 ON C007_Id=T005_C007_Id_Vendedor_Interno 
                                        WHERE T006_T005_Id='{$T005_Id}'");
        $retorno = 0;
        if ($restricaoAtual == false) {
            while ($rowT005 = mysql_fetch_array($sqlT005)) {
                if (($rowT005['SaldoFuturo'] < $saldoMinimo) && ($rowT005['T006_Liberou_Alcada_Saldo_Futuro'] != $rowT005['SaldoFuturo']) && $rowT005['C007_Flag_Vendedor'] == 'V') {
                    $retorno += 1;
                }
            }
        } else {
            while ($rowT005 = mysql_fetch_array($sqlT005)) {
                if (($rowT005['SaldoFuturo'] < $saldoMinimo) && $rowT005['C007_Flag_Vendedor'] == 'V') {
                    $retorno += 1;
                }
            }
        }
        
        return $retorno;
    }
    
    /**
     * gerarControleProdutosPedido
     *
     * @param String $T005_Id
     * @return true
     */    
    public function gerarControleProdutosPedido($T005_Id)
    {
        global $g;
        //INSERT de todos os produtos do pedido na T207 (Controle de Produtos)
        $T006 = mysql_query("SELECT * FROM T006 WHERE T006_T005_Id='{$T005_Id}'");
        while ($row = mysql_fetch_array($T006)) {
            mysql_query("DELETE FROM T207 WHERE T207_T006_Id = '{$row['T006_Id']}'");
            $quantidadeSeparada = $row['T006_Quantidade_Separacao'];
            for ($i = 0; $i < $row['T006_Quantidade']; $i++) {
                $sql = "INSERT INTO T207(";
                $sql .= "T207_D009_Id,";
                $sql .= "T207_T006_Id,";
                $sql .= "T207_T005_Id,";
                $sql .= "T207_C007_Id,";
                $sql .= "T207_Flag_Status,";
                $sql .= "T207_Data";
                $sql .= ") VALUES (";
                $sql .= "'{$row['T006_D009_Id']}',";
                $sql .= "'{$row['T006_Id']}',";
                $sql .= "'{$row['T006_T005_Id']}',";
                $sql .= "'{$g['usuarioAtual']}',";
                // Verifica se existe produtos separados para inserir com status "ENTREGUE (2)"
                if ($quantidadeSeparada > 0) {
                    $sql .= "'2',";
                } else {
                    $sql .= "'1',";
                }
                $sql .= "CURDATE()";
                $sql .= ")";
                mysql_query($sql);
                $quantidadeSeparada--;
            }
        }
        return true;
    }
    
    /**
     * atualizarControleProduto
     *
     * @param String $T006_Id
     * @return true
     */    
    public function atualizarControleProduto($T006_Id)
    {
        global $g;
        mysql_query("DELETE FROM T207 WHERE T207_T006_Id = '{$T006_Id}'");
        //INSERT de todos os produtos do pedido na T207 (Controle de Produtos)
        $T006               = mysql_query("SELECT * FROM T006 WHERE T006_Id='{$T006_Id}'");
        $row                = mysql_fetch_array($T006);
        $quantidadeSeparada = $row['T006_Quantidade_Separacao'];
        for ($i = 0; $i < $row['T006_Quantidade']; $i++) {
            $sql = "INSERT INTO T207(";
            $sql .= "T207_D009_Id,";
            $sql .= "T207_T006_Id,";
            $sql .= "T207_T005_Id,";
            $sql .= "T207_C007_Id,";
            $sql .= "T207_Flag_Status,";
            $sql .= "T207_Data";
            $sql .= ") VALUES (";
            $sql .= "'{$row['T006_D009_Id']}',";
            $sql .= "'{$row['T006_Id']}',";
            $sql .= "'{$row['T006_T005_Id']}',";
            $sql .= "'{$g['usuarioAtual']}',";
            // Verifica se existe produtos separados para inserir com status "ENTREGUE (2)"
            if ($quantidadeSeparada > 0) {
                $sql .= "'2',";
            } else {
                $sql .= "'1',";
            }
            $sql .= "CURDATE()";
            $sql .= ")";
            mysql_query($sql);
            $quantidadeSeparada--;
        }
        return true;
    }
    
    /**
     * inserirControleProduto
     *
     * @param String $T006_Id
     * @return true
     */    
    public function inserirControleProduto($T006_Id)
    {
        global $g;
        //INSERT de todos os produtos do pedido na T207 (Controle de Produtos)
        $T006 = mysql_query("SELECT * FROM T006 WHERE T006_Id='{$T006_Id}'");
        $row  = mysql_fetch_array($T006);
        for ($i = 0; $i < $row['T006_Quantidade']; $i++) {
            $sql = "INSERT INTO T207(";
            $sql .= "T207_D009_Id,";
            $sql .= "T207_T006_Id,";
            $sql .= "T207_T005_Id,";
            $sql .= "T207_C007_Id,";
            $sql .= "T207_Flag_Status,";
            $sql .= "T207_Data";
            $sql .= ") VALUES (";
            $sql .= "'{$row['T006_D009_Id']}',";
            $sql .= "'{$row['T006_Id']}',";
            $sql .= "'{$row['T006_T005_Id']}',";
            $sql .= "'{$g['usuarioAtual']}',";
            $sql .= "'3',";
            $sql .= "CURDATE()";
            $sql .= ")";
            mysql_query($sql);
        }
        return true;
    }
    
    /**
     * excluirControleProduto
     *
     * @param String $T006_Id
     * @return true
     */    
    public function excluirControleProduto($T006_Id)
    {
        $T207 = mysql_query("SELECT * FROM T207 WHERE T207_T006_Id = '{$T006_Id}' AND T207_Flag_Status = '2'");
        if (mysql_num_rows($T207) > 0) {
            mysql_query("UPDATE T207 SET T207_Flag_Status = '5', T207_T006_Id = '' WHERE T207_T006_Id = '{$T006_Id}'");
            return true;
        } else {
            mysql_query("DELETE FROM T207 WHERE T207_T006_Id = '{$T006_Id}'");
            return false;
        }
    }
    
    /**
     * alteraLista
     *
     * @param String $T005_Id
     * @param String $lista
     * @return true
     */    
    public function alteraLista($T005_Id, $lista)
    {
        global $g;
        mysql_query("UPDATE T005 SET T005_Flag_Lista_Preco = '{$lista}' WHERE T005_Id = '{$T005_Id}'");
        $T006 = mysql_query("SELECT * FROM T006 WHERE T006_T005_Id = '{$T005_Id}'");
        
        while ($row = mysql_fetch_array($T006)) {
            $sql  = "SELECT (D009_Preco_{$lista}) as preco FROM D009 WHERE D009_Id = '{$row['T006_D009_Id']}'";
            $D009 = mysql_query($sql);
            $res  = mysql_fetch_array($D009);
            if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
                $sql = "UPDATE T006 SET T006_Valor_Preco_Original_Bruto = '{$res['preco']}',
                                    T006_Percentual_Desconto_Bruto=if(T006_Valor_Preco_Original_Bruto>0,(1-(T006_Valor_Preco_Bruto/T006_Valor_Preco_Original_Bruto))*100,0),
                                    T006_Valor_Desconto_Unitario_Bruto=if(T006_Valor_Preco_Original_Bruto>0,(T006_Valor_Preco_Original_Bruto*('{$res['preco']}'/100)),0)
                                    WHERE T006_Id='{$row['T006_Id']}'";
                mysql_query($sql);
            } else {
                $sql = "UPDATE T006 SET T006_Valor_Preco_Original = '{$res['preco']}',
                                    T006_Percentual_Desconto=if(T006_Valor_Preco_Original>0,(1-(T006_Valor_Preco_Sem_Desconto_Unitario/T006_Valor_Preco_Original))*100,0),
                                    T006_Valor_Desconto_Unitario=if(T006_Valor_Preco_Original>0,(T006_Valor_Preco_Original*('{$res['preco']}'/100)),0)
                                    WHERE T006_Id='{$row['T006_Id']}'";
                
                mysql_query($sql);
            }
        }
        mysqli_query("CALL T005_Gravar_Totalizacao_4('{$T005_Id}')");
        return true;
    }

    /**
     * verificarAgruparEmpresaPedidosSelecionados
     *
     * @param String $dias
     * @return String
     */    
	public Function VEN002VerificarAgruparPedidosSelecionados($mT005,$mT005Anterior) 
    {
        if ($mT005['T005_Flag_Status'] != '0' && $mT005['T005_Flag_Status'] != '3') {
            return "Só é possível agrupar pedidos com status pendente ou gerar nf." ;
        }
        if ($mT005['T007_Numero_Nota_Fiscal'] > 0) {
            return "Já existe nota fiscal para o pedido {$mT005['T005_Id']}.";
        }
        if ($mT005['T005_T005_Id_Agrupado'] > 0) {
            return "O pedido {$mT005['T005_Id']} já está agrupado ao pedido {$mT005['T005_T005_Id_Agrupado']}";
        }
        if (!empty($mT005['T005_Id_Pedido_Externo'])) {
            $erro = "Não é possível agrupar pedidos com ID E-Commerce." ;
        }
        if ($mT005Anterior!==false){
            log("{$mT005Anterior['T005_D024_Id']} != {$mT005['T005_D024_Id']}");
            if ($mT005Anterior['T005_D024_Id'] != $mT005['T005_D024_Id']) {
                return "Não é possível agrupar pedidos de clientes diferentes.";
            }
        }

        return true;
    }

    
    /**
     * VEN002AgruparPedidosSelecionados
     *
     * @return String/bool
     */    
    public function VEN002AgruparPedidosSelecionados($sql)
    {
        global $g;

        if (empty($sql)) {
            return "Não recebido parâmetro sql, favor informar o suporte técnico.";
        }
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        /**
         * Início do looping
         **/
        // Agrupa os campos de ordem de compra e observacao de todos os pedidos
        $T005_Ordem_Compra = '';
        $T005_Observacao = '';
        $T005_Observacao_Comercial = '';
        $T005_Observacao_Nota_Fiscal = '';
        $T005_Observacao_1 = '';
        $T005_Valor_Frete = 0;

		$sql = gDescomprimeSql($sql);

        $sql = explode("FROM T005 ",$sql);

        $T005 = mysql_query("SELECT T005.* FROM T005 {$sql[1]}");

        if (mysql_num_rows($T005)<=0){
            return "Não encontrado pedidos para agrupar";
        }

        while ($mT005 = mysql_fetch_assoc($T005)) {
                $T005_Ordem_Compra .= $mT005['T005_Ordem_Compra'] . ' ';
                $T005_Observacao .= $mT005['T005_Observacao'] . ' ';
                $T005_Observacao_Comercial .= $mT005['T005_Observacao_Comercial'] . ' ';
                $T005_Observacao_Nota_Fiscal .= $mT005['T005_Observacao_Nota_Fiscal'] . ' ';
                $T005_Observacao_1 .= $mT005['T005_Observacao_1'] . ' ';
                $T005_Valor_Frete += $mT005['T005_Valor_Frete'];
        }
        
        // Cria o novo cabecalho
        $T005 = mysql_query("SELECT T005.* FROM T005 {$sql[1]}");
        $mT005 = mysql_fetch_assoc($T005);
        
        $T005_Id_Antigo = $mT005['T005_Id'];
        $campos         = "";
        $valores        = "";
        foreach ($mT005 as $key => $value) {
            if ($key == 'T005_Id') {
                continue;
            } elseif ($key == "T005_T003_Id") {
                $campos .= $key . ", ";
                $valores .= "'',";
            }elseif($key == 'T005_Id_Pedido_Externo'){
                $campos.=$key.", ";
                $valores.= "null,";
            } elseif ($value==NULL) {
                $campos .= $key . ", ";
                $valores .= "'',";
            } else {
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($value) . "',";
            }
        }
        $campos  = trim($campos, ', ');
        $valores = trim($valores, ',');
        $insT005 = "INSERT INTO T005 ({$campos}) VALUES ({$valores})";
        $resT005 = mysql_query($insT005);
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "Erro : " . $erro;
        }
        $T005_Id_Novo = $g['mysqlLastId'];
        
        $T005_Observacao_1 = mysql_real_escape_string($T005_Observacao_1);
        $T005_Observacao = mysql_real_escape_string($T005_Observacao);
        $T005_Observacao_Comercial = mysql_real_escape_string($T005_Observacao_Comercial);
        $T005_Observacao_Nota_Fiscal = mysql_real_escape_string($T005_Observacao_Nota_Fiscal);
        $T005_Ordem_Compra = mysql_real_escape_string($T005_Ordem_Compra);

        // Grava o agrupamento dos campos de observacao e ordem de compra
        mysql_query("UPDATE T005 SET T005_Ordem_Compra='{$T005_Ordem_Compra}',
                                     T005_Observacao='{$T005_Observacao}',
                                     T005_Observacao_Comercial='{$T005_Observacao_Comercial}',
                                     T005_Observacao_Nota_Fiscal='{$T005_Observacao_Nota_Fiscal}',
                                     T005_Observacao_1='{$T005_Observacao_1}',
                                     T005_Valor_Frete='{$T005_Valor_Frete}',
                                     T005_Flag_Marcado='N'
                               WHERE T005_Id='{$T005_Id_Novo}'");
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "Erro 1: " . mysql_error();
        }
        
        // Copia tabela estendida
        $T005A = mysql_query("select * from T005A where T005A_T005_Id='{$T005_Id_Antigo}'");
        $mT005A = mysql_fetch_assoc($T005A);

        $campos  = "";
        $valores = "";
        foreach ($mT005A as $key => $value) {
            
            if ($key == 'T005A_Id') {
                continue;
            } elseif ($key == "T005A_T005_Id") {
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($T005_Id_Novo) . "',";
            } elseif ($key == "T005A_T005_Id_Original") {
                $campos .= $key . ", ";
                $valores .= "'',";
            } elseif ($key == "T005A_JSON") {
                continue;
            } elseif ($key == "T005A_Liberou_Credito_Portador") {
                // Dados que são populados na liberação do crédito, não popular, se o pedido não tiver alteração do orignal não vai passar pela análise de crédito.
                continue;
            } elseif ($value==NULL) {
                $campos .= $key . ", ";
                $valores .= "'',";
            }  else {
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($value) . "',";
            }
        }
        $campos  = trim($campos, ', ');
        $valores = trim($valores, ',');
        $insT005A = "INSERT INTO T005A ({$campos}) VALUES ({$valores})";
        $sqlInsT005A = mysql_query($insT005A);
        $erro = mysql_error();
        if (!$sqlInsT005A) {
            mysql_query("ROLLBACK");
            return "Erro 3: $erro" ;
        }
        
        // Selecionando todos os itens dos pedidos duplicados
        $extraT006 = gRetirarGroupBy($sql['1']);
        $joinT006 = "";
        if (!strpos($extraT006,"T006")) {
            $joinT006 = " LEFT JOIN T006 ON T006_T005_Id=T005_Id ";
        } 
        
        $T006 = mysql_query("SELECT T006.* FROM T005 $joinT006 $extraT006 group by T006_Id");

        if (mysql_num_rows($T006)<=0) {
            mysql_query("ROLLBACK");
            return "Erro: Não encontrou itens nos pedidos para agrupar";
        }

        $item = 1;
        while ($mT006 = mysql_fetch_assoc($T006)) {
            $campos  = "";
            $valores = "";
            foreach ($mT006 as $key => $value) {
                if ($key == 'T006_Id') {
                    continue;
                } elseif ($key == "T006_T005_Id") {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($T005_Id_Novo) . "',";
                } elseif ($key == "T006_Item") {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($item) . "',";
                } elseif ($key == "T006_C004_Id_Estoque") {
                    $campos .= $key . ", ";
                    $valores .= "NULL,";
                } else {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($value) . "',";
                }
            }
            $campos  = trim($campos, ', ');
            $valores = trim($valores, ',');
            $insT006 = "INSERT INTO T006 ({$campos}) VALUES ({$valores})";
            $resT006 = mysql_query($insT006);
            if (!$resT006) {
                mysql_query("ROLLBACK");
                return "Erro 2: " . mysql_error();
            }
            $T006_Id = $g['mysqlLastId'];
            
            $T006A = mysql_query("SELECT * FROM T006A WHERE T006A_T006_Id='{$mT006['T006_Id']}'");
            $mT006A = mysql_fetch_assoc($T006A);
            $campos  = "";
            $valores = "";
            foreach ($mT006A as $key => $value) {

                if ($key == 'T006A_Id') {
                    continue;
                } elseif ($key == "T006A_T006_Id") {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($T006_Id) . "',";
                } elseif ($key == "T006A_JSON") {
                    continue;
                } elseif ($key == "T006A_Flag_Notificacao") {
                    $campos .= $key . ", ";
                    $valores .= "'',";
                } elseif ($key == "T006A_Flag_Producao_Emergencia") {
                    $campos .= $key . ", ";
                    $valores .= "'',";
                }  else {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($value) . "',";
                }
            }

            $campos  = trim($campos, ', ');
            $valores = trim($valores, ',');
            $insT006A = "INSERT INTO T006A ({$campos}) VALUES ({$valores})";
            $sqlT006A = mysql_query($insT006A);
            $erro = mysql_error();
            if (!$sqlT006A) {
                mysql_query("ROLLBACK");
                return "Erro 6: $erro" ;
            }
            
            $PD = mysql_query("SELECT D049_Flag_Tipo 
                                 FROM T006 
                            LEFT JOIN D009 ON D009_Id = T006_D009_Id 
                            LEFT JOIN D049 ON D049_Id = D009_D049_Id 
                            LEFT JOIN D001 ON D001_Id = D049_D009_Id 
                                WHERE T006_Id = '{$mT006['T006_Id']}'");
            $mPD = mysql_fetch_assoc($PD);
            // Verifica se o item é produzido, se for precisa buscar o ultimo T006_Id inserido no pedido
            // Porque são feitos outros insertes na tabela de engenharia que são startados pela trigger 
            if(in_array($mPD['D049_Flag_Tipo'], array('PD', 'IN'), true)){
                $item  = mysql_query("SELECT T006_Id FROM T006 WHERE T006_T005_Id = '{$T005_Id_Novo}' ORDER BY T006_Id DESC LIMIT 1");
                $mItem = mysql_fetch_assoc($item);
                $T006_Id = $mItem['T006_Id'];

            }

            $item++;
            mysql_query("UPDATE T238 SET T238_T006_Id = '{$T006_Id}' WHERE T238_T006_Id = '{$mT006['T006_Id']}'");
            mysql_query("UPDATE T219 SET T219_T006_Id = '{$T006_Id}' WHERE T219_T006_Id = '{$mT006['T006_Id']}'");
        }

        mysql_query("CALL T005_Gravar_Totalizacao_4('{$T005_Id_Novo}')");

        $T005 = mysql_query("SELECT T005.* FROM T005 {$sql[1]}");
        while ($mT005 = mysql_fetch_assoc($T005)) {
            if ($mT005['T005_Id'] != $T005_Id_Novo) {
                mysql_query("UPDATE T005 SET T005_T005_Id_Agrupado='{$T005_Id_Novo}', T005_Flag_Marcado='N' WHERE T005_Id = '{$mT005['T005_Id']}'");
                mysql_query("insert into T178 (T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) values ('{$T005_Id_Novo}','{$g['usuarioAtual']}',CURRENT_TIMESTAMP(), 'Pedido agrupado {$mT005['T005_Id']}','')");
            }
        }
        
        mysql_query("UPDATE T238 LEFT JOIN T005 ON T005_Id=T238_T005_Id  SET T238_T005_Id = '{$T005_Id_Novo}' WHERE T005_T005_Id_Agrupado = '{$T005_Id_Novo}'");

        // verifica se criou corretamente o T005 e T006
        // Aconteceu alguns casos na Ceigon onde criava o T005 e o T006 e depois o registro do T005 não existia mais
        $T005_T006 = mysql_query("SELECT T005_Id,
                                         T006_Id 
                                    FROM T005 
                               LEFT JOIN T006 ON T006_T005_Id=T005_Id 
                                   WHERE T005_Id = '{$T005_Id_Novo}'");
        $mT005_T006 = mysql_fetch_array($T005_T006);
        if($mT005_T006['T005_Id'] <=0 || $mT005_T006['T006_Id'] <=0){
            mysql_query("ROLLBACK");
            return "Houve um problema na gravação dos dados. Por favor, refaça a operação";
        }
        
        mysql_query("COMMIT");

        return true;
    }

    public function VEN002ValidaAgruparPedidosEmEtapas($sql)
    {
        global $g;

        if (empty($sql)) {
            return "Não recebido parâmetro sql, favor informar o suporte técnico.";
        }

        $sql = gDescomprimeSql($sql);
        $partesSql = explode("FROM T005 ", $sql, 2);
        $extraSql = (count($partesSql) >= 2) ? $partesSql[1] : false;
        if ($extraSql === false) {
            return "Não foi possível identificar os pedidos selecionados para o agrupamento.";
        }

        $pedidos = array();
        $sqlPedidos = mysql_query("SELECT T005.*, T007_Numero_Nota_Fiscal FROM T005 {$extraSql}");
        $erro = mysql_error();
        if (!empty($erro)) {
            return "Erro ao validar pedidos para agrupamento: {$erro}";
        }

        while ($rowPedido = mysql_fetch_assoc($sqlPedidos)) {
            $pedidos[$rowPedido['T005_Id']] = $rowPedido;
        }

        if (count($pedidos) <= 0) {
            return "Não encontrado pedidos para agrupar";
        }

        if (count($pedidos) < 2) {
            return "Selecione pelo menos dois pedidos para agrupar.";
        }

        uasort($pedidos, function ($pedidoA, $pedidoB) {
            return ((int) $pedidoA['T005_Id']) - ((int) $pedidoB['T005_Id']);
        });

        $pedidoAnterior = false;
        foreach ($pedidos as $pedido) {
            $retorno = $this->VEN002VerificarAgruparPedidosSelecionados($pedido, $pedidoAnterior);
            if ($retorno !== true) {
                return $retorno;
            }
            $pedidoAnterior = $pedido;
        }

        $extraT006 = gRetirarGroupBy($extraSql);
        $joinT006 = (strpos($extraT006, "T006") === false) ? " LEFT JOIN T006 ON T006_T005_Id=T005_Id " : "";
        $sqlItens = mysql_query("SELECT T006.T006_Id, T006.T006_T005_Id AS T005_Id, IFNULL(T006.T006_Item, T006.T006_Id) AS Item_Sequencia FROM T005 {$joinT006} {$extraT006} GROUP BY T006.T006_Id");
        $erro = mysql_error();
        if (!empty($erro)) {
            return "Erro ao consultar itens dos pedidos para agrupamento: {$erro}";
        }

        $itens = array();
        while ($rowItem = mysql_fetch_assoc($sqlItens)) {
            if (empty($rowItem['T006_Id'])) {
                continue;
            }

            $itens[] = $rowItem;
        }

        if (count($itens) <= 0) {
            return "Erro: Não encontrou itens nos pedidos para agrupar";
        }

        usort($itens, function ($itemA, $itemB) {
            if ((int) $itemA['T005_Id'] === (int) $itemB['T005_Id']) {
                if ((int) $itemA['Item_Sequencia'] === (int) $itemB['Item_Sequencia']) {
                    return ((int) $itemA['T006_Id']) - ((int) $itemB['T006_Id']);
                }

                return ((int) $itemA['Item_Sequencia']) - ((int) $itemB['Item_Sequencia']);
            }

            return ((int) $itemA['T005_Id']) - ((int) $itemB['T005_Id']);
        });

        $pedidoModelo = reset($pedidos);
        $processoKey = md5(uniqid('ven002-agrupar-', true));
        $cache = array(
            'processoKey' => $processoKey,
            'etapaAtual' => 0,
            'totalEtapas' => count($itens),
            'etapas' => $itens,
            'contexto' => array(
                'extraSql' => $extraSql,
                'pedidoModeloId' => $pedidoModelo['T005_Id'],
                'pedidosSelecionados' => array_keys($pedidos),
                'pedidosAgrupados' => array(
                    'T005_Ordem_Compra' => '',
                    'T005_Observacao' => '',
                    'T005_Observacao_Comercial' => '',
                    'T005_Observacao_Nota_Fiscal' => '',
                    'T005_Observacao_1' => '',
                    'T005_Valor_Frete' => 0
                ),
                'novoT005_Id' => 0,
                'novoT005A_Id' => 0
            ),
            'itensProcessados' => array(),
            'historicosCriados' => array()
        );

        foreach ($pedidos as $pedido) {
            $cache['contexto']['pedidosAgrupados']['T005_Ordem_Compra'] .= $pedido['T005_Ordem_Compra'] . ' ';
            $cache['contexto']['pedidosAgrupados']['T005_Observacao'] .= $pedido['T005_Observacao'] . ' ';
            $cache['contexto']['pedidosAgrupados']['T005_Observacao_Comercial'] .= $pedido['T005_Observacao_Comercial'] . ' ';
            $cache['contexto']['pedidosAgrupados']['T005_Observacao_Nota_Fiscal'] .= $pedido['T005_Observacao_Nota_Fiscal'] . ' ';
            $cache['contexto']['pedidosAgrupados']['T005_Observacao_1'] .= $pedido['T005_Observacao_1'] . ' ';
            $cache['contexto']['pedidosAgrupados']['T005_Valor_Frete'] += (float) $pedido['T005_Valor_Frete'];
        }

        $arquivoCache = "/tmp/ven002-agrupar-pedidos-etapas-{$g['usuarioAtual']}-{$processoKey}";
        if (file_exists($arquivoCache)) {
            @unlink($arquivoCache);
        }

        if (file_put_contents($arquivoCache, serialize($cache)) === false) {
            return "Erro ao salvar o cache inicial do agrupamento em etapas.";
        }

        return array(
            'processoKey' => $processoKey,
            'totalEtapas' => count($itens)
        );
    }

    public function VEN002AgruparPedidosEmEtapas($dados, $cacheAnterior = false)
    {
        global $g;

        if (empty($dados['Processo_Key']) || empty($dados['T006_Id'])) {
            return "Parâmetros insuficientes para agrupar pedidos em etapas.";
        }

        $arquivoCache = "/tmp/ven002-agrupar-pedidos-etapas-{$g['usuarioAtual']}-{$dados['Processo_Key']}";
        $cache = false;
        if (file_exists($arquivoCache)) {
            $cache = @unserialize(file_get_contents($arquivoCache));
            $cache = is_array($cache) ? $cache : false;
        }
        if ($cache === false) {
            return "Não foi possível recuperar o cache do agrupamento em etapas.";
        }

        if (!isset($cache['etapaAtual'])) {
            $cache['etapaAtual'] = 0;
        }

        $sqlT006 = mysql_query("SELECT * FROM T006 WHERE T006_Id = '{$dados['T006_Id']}' LIMIT 1");
        $mT006 = mysql_fetch_assoc($sqlT006);
        if (empty($mT006['T006_Id'])) {
            if (file_exists($arquivoCache)) {
                @unlink($arquivoCache);
            }
            if (!empty($cache['itensProcessados'])) {
                return $this->VEN002RollbackAgruparPedidosEmEtapas($cache, "Item {$dados['T006_Id']} não encontrado para o agrupamento em etapas.");
            }
            return "Item {$dados['T006_Id']} não encontrado para o agrupamento em etapas.";
        }

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        if ((int) $cache['contexto']['novoT005_Id'] <= 0) {
            $sqlPedidoModelo = mysql_query("SELECT * FROM T005 WHERE T005_Id = '{$cache['contexto']['pedidoModeloId']}' LIMIT 1");
            $pedidoModelo = mysql_fetch_assoc($sqlPedidoModelo);
            if (empty($pedidoModelo['T005_Id'])) {
                mysql_query("ROLLBACK");
                if (file_exists($arquivoCache)) {
                    @unlink($arquivoCache);
                }
                return "Não foi possível encontrar o pedido base para o agrupamento.";
            }

            $campos = array();
            $valores = array();
            foreach ($pedidoModelo as $campo => $valor) {
                if ($campo == 'T005_Id') {
                    continue;
                } elseif ($campo == 'T005_T003_Id') {
                    $campos[] = $campo;
                    $valores[] = "''";
                } elseif ($campo == 'T005_Id_Pedido_Externo') {
                    $campos[] = $campo;
                    $valores[] = "NULL";
                } elseif ($valor === null) {
                    $campos[] = $campo;
                    $valores[] = "''";
                } else {
                    $campos[] = $campo;
                    $valores[] = "'" . mysql_real_escape_string($valor) . "'";
                }
            }

            mysql_query("INSERT INTO T005 (" . implode(', ', $campos) . ") VALUES (" . implode(', ', $valores) . ")");
            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return "Erro ao criar o novo pedido agrupado: {$erro}";
            }

            $cache['contexto']['novoT005_Id'] = $g['mysqlLastId'];
            $dadosAgrupados = $cache['contexto']['pedidosAgrupados'];
            $valorOrdemCompra = "'" . mysql_real_escape_string($dadosAgrupados['T005_Ordem_Compra']) . "'";
            $valorObservacao = "'" . mysql_real_escape_string($dadosAgrupados['T005_Observacao']) . "'";
            $valorObservacaoComercial = "'" . mysql_real_escape_string($dadosAgrupados['T005_Observacao_Comercial']) . "'";
            $valorObservacaoNotaFiscal = "'" . mysql_real_escape_string($dadosAgrupados['T005_Observacao_Nota_Fiscal']) . "'";
            $valorObservacao1 = "'" . mysql_real_escape_string($dadosAgrupados['T005_Observacao_1']) . "'";
            $valorFrete = "'" . mysql_real_escape_string($dadosAgrupados['T005_Valor_Frete']) . "'";
            mysql_query("UPDATE T005 SET
                            T005_Ordem_Compra = {$valorOrdemCompra},
                            T005_Observacao = {$valorObservacao},
                            T005_Observacao_Comercial = {$valorObservacaoComercial},
                            T005_Observacao_Nota_Fiscal = {$valorObservacaoNotaFiscal},
                            T005_Observacao_1 = {$valorObservacao1},
                            T005_Valor_Frete = {$valorFrete},
                            T005_Flag_Marcado = 'N'
                        WHERE T005_Id = '{$cache['contexto']['novoT005_Id']}'");
            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return "Erro ao atualizar os dados agregados do novo pedido: {$erro}";
            }

            $sqlT005A = mysql_query("SELECT * FROM T005A WHERE T005A_T005_Id = '{$cache['contexto']['pedidoModeloId']}' LIMIT 1");
            $mT005A = mysql_fetch_assoc($sqlT005A);
            if (!empty($mT005A['T005A_Id'])) {
                $campos = array();
                $valores = array();
                foreach ($mT005A as $campo => $valor) {
                    if ($campo == 'T005A_Id') {
                        continue;
                    } elseif ($campo == 'T005A_T005_Id') {
                        $campos[] = $campo;
                        $valores[] = "'" . mysql_real_escape_string($cache['contexto']['novoT005_Id']) . "'";
                    } elseif ($campo == 'T005A_T005_Id_Original') {
                        $campos[] = $campo;
                        $valores[] = "''";
                    } elseif ($campo == 'T005A_JSON' || $campo == 'T005A_Liberou_Credito_Portador') {
                        continue;
                    } elseif ($valor === null) {
                        $campos[] = $campo;
                        $valores[] = "''";
                    } else {
                        $campos[] = $campo;
                        $valores[] = "'" . mysql_real_escape_string($valor) . "'";
                    }
                }

                mysql_query("INSERT INTO T005A (" . implode(', ', $campos) . ") VALUES (" . implode(', ', $valores) . ")");
                $erro = mysql_error();
                if (!empty($erro)) {
                    mysql_query("ROLLBACK");
                    return "Erro ao copiar a tabela estendida do novo pedido: {$erro}";
                }
                $cache['contexto']['novoT005A_Id'] = $g['mysqlLastId'];
            }
        }

        $itemNovoNumero = count($cache['itensProcessados']) + 1;
        $campos = array();
        $valores = array();
        foreach ($mT006 as $campo => $valor) {
            if ($campo == 'T006_Id') {
                continue;
            } elseif ($campo == 'T006_T005_Id') {
                $campos[] = $campo;
                $valores[] = "'" . mysql_real_escape_string($cache['contexto']['novoT005_Id']) . "'";
            } elseif ($campo == 'T006_Item') {
                $campos[] = $campo;
                $valores[] = "'" . mysql_real_escape_string($itemNovoNumero) . "'";
            } elseif ($campo == 'T006_C004_Id_Estoque') {
                $campos[] = $campo;
                $valores[] = "NULL";
            } else {
                $campos[] = $campo;
                $valores[] = ($valor === null) ? "NULL" : "'" . mysql_real_escape_string($valor) . "'";
            }
        }

        mysql_query("INSERT INTO T006 (" . implode(', ', $campos) . ") VALUES (" . implode(', ', $valores) . ")");
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            if (!empty($cache['itensProcessados'])) {
                if (file_exists($arquivoCache)) {
                    @unlink($arquivoCache);
                }
                return $this->VEN002RollbackAgruparPedidosEmEtapas($cache, "Erro ao copiar o item {$dados['T006_Id']}: {$erro}");
            }
            return "Erro ao copiar o item {$dados['T006_Id']}: {$erro}";
        }

        $novoT006_Id = $g['mysqlLastId'];
        $sqlPD = mysql_query("SELECT D049_Flag_Tipo
                                FROM T006
                                LEFT JOIN D009 ON D009_Id = T006_D009_Id
                                LEFT JOIN D049 ON D049_Id = D009_D049_Id
                                LEFT JOIN D001 ON D001_Id = D049_D009_Id
                               WHERE T006_Id = '{$mT006['T006_Id']}'
                               LIMIT 1");
        $mPD = mysql_fetch_assoc($sqlPD);
        if (in_array($mPD['D049_Flag_Tipo'], array('PD', 'IN'), true)) {
            $sqlUltimoItem = mysql_query("SELECT T006_Id FROM T006 WHERE T006_T005_Id = '{$cache['contexto']['novoT005_Id']}' ORDER BY T006_Id DESC LIMIT 1");
            $mUltimoItem = mysql_fetch_assoc($sqlUltimoItem);
            if (!empty($mUltimoItem['T006_Id'])) {
                $novoT006_Id = $mUltimoItem['T006_Id'];
            }
        }

        $novoT006A_Id = 0;
        $sqlT006A = mysql_query("SELECT * FROM T006A WHERE T006A_T006_Id = '{$mT006['T006_Id']}' LIMIT 1");
        $mT006A = mysql_fetch_assoc($sqlT006A);
        if (!empty($mT006A['T006A_Id'])) {
            $campos = array();
            $valores = array();
            foreach ($mT006A as $campo => $valor) {
                if ($campo == 'T006A_Id') {
                    continue;
                } elseif ($campo == 'T006A_T006_Id') {
                    $campos[] = $campo;
                    $valores[] = "'" . mysql_real_escape_string($novoT006_Id) . "'";
                } elseif ($campo == 'T006A_JSON') {
                    continue;
                } elseif ($campo == 'T006A_Flag_Notificacao' || $campo == 'T006A_Flag_Producao_Emergencia') {
                    $campos[] = $campo;
                    $valores[] = "''";
                } else {
                    $campos[] = $campo;
                    $valores[] = ($valor === null) ? "NULL" : "'" . mysql_real_escape_string($valor) . "'";
                }
            }

            mysql_query("INSERT INTO T006A (" . implode(', ', $campos) . ") VALUES (" . implode(', ', $valores) . ")");
            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                if (!empty($cache['itensProcessados'])) {
                    if (file_exists($arquivoCache)) {
                        @unlink($arquivoCache);
                    }
                    return $this->VEN002RollbackAgruparPedidosEmEtapas($cache, "Erro ao copiar a tabela estendida do item {$dados['T006_Id']}: {$erro}");
                }
                return "Erro ao copiar a tabela estendida do item {$dados['T006_Id']}: {$erro}";
            }
            $novoT006A_Id = $g['mysqlLastId'];
        }

        $snapshotsT219 = array();
        $sqlT219 = mysql_query("SELECT * FROM T219 WHERE T219_T006_Id = '{$mT006['T006_Id']}'");
        while ($rowT219 = mysql_fetch_assoc($sqlT219)) {
            $snapshotsT219[] = $rowT219;
        }
        mysql_query("UPDATE T219 SET T219_T006_Id = '{$novoT006_Id}' WHERE T219_T006_Id = '{$mT006['T006_Id']}'");
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            if (!empty($cache['itensProcessados'])) {
                if (file_exists($arquivoCache)) {
                    @unlink($arquivoCache);
                }
                return $this->VEN002RollbackAgruparPedidosEmEtapas($cache, "Erro ao atualizar os vínculos de compra do item {$dados['T006_Id']}: {$erro}");
            }
            return "Erro ao atualizar os vínculos de compra do item {$dados['T006_Id']}: {$erro}";
        }

        $snapshotsT238 = array();
        $sqlT238 = mysql_query("SELECT * FROM T238 WHERE T238_T006_Id = '{$mT006['T006_Id']}'");
        while ($rowT238 = mysql_fetch_assoc($sqlT238)) {
            $snapshotsT238[] = $rowT238;
        }
        mysql_query("UPDATE T238 SET T238_T006_Id = '{$novoT006_Id}' WHERE T238_T006_Id = '{$mT006['T006_Id']}'");
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            if (!empty($cache['itensProcessados'])) {
                if (file_exists($arquivoCache)) {
                    @unlink($arquivoCache);
                }
                return $this->VEN002RollbackAgruparPedidosEmEtapas($cache, "Erro ao atualizar os vínculos de separação do item {$dados['T006_Id']}: {$erro}");
            }
            return "Erro ao atualizar os vínculos de separação do item {$dados['T006_Id']}: {$erro}";
        }

        $sqlCommit = <<<SQL
            COMMIT
SQL;
        $commit = mysql_query($sqlCommit);
        $erro = mysql_error();
        if ($commit === false || !empty($erro)) {
            if (empty($erro)) {
                $erro = 'Falha ao executar o COMMIT da etapa.';
            }

            $sqlRollback = <<<SQL
                ROLLBACK
SQL;
            mysql_query($sqlRollback);
            if (!empty($cache['itensProcessados'])) {
                if (file_exists($arquivoCache)) {
                    @unlink($arquivoCache);
                }
                return $this->VEN002RollbackAgruparPedidosEmEtapas($cache, "Erro ao concluir a etapa do item {$mT006['T006_Id']}: {$erro}");
            }

            return "Erro ao concluir a etapa do item {$mT006['T006_Id']}: {$erro}";
        }

        $cache['itensProcessados'][] = array(
            'T006_Id_Origem' => $mT006['T006_Id'],
            'T006_Id_Novo' => $novoT006_Id,
            'T006A_Id_Novo' => $novoT006A_Id,
            'snapshotsT219' => $snapshotsT219,
            'snapshotsT238' => $snapshotsT238
        );
        $cache['etapaAtual'] = (int) $cache['etapaAtual'] + 1;

        if (file_put_contents($arquivoCache, serialize($cache)) === false) {
            if (file_exists($arquivoCache)) {
                @unlink($arquivoCache);
            }
            return $this->VEN002RollbackAgruparPedidosEmEtapas($cache, "Erro ao salvar o cache do agrupamento em etapas.");
        }

        if ((int) $dados['Ultimo_Item'] !== 1) {
            return true;
        }

        $retorno = $this->VEN002FinalizaAgruparPedidosEmEtapas($cache);
        if ($retorno !== true) {
            if (file_exists($arquivoCache)) {
                @unlink($arquivoCache);
            }
            return $this->VEN002RollbackAgruparPedidosEmEtapas($cache, $retorno);
        }

        if (file_exists($arquivoCache)) {
            @unlink($arquivoCache);
        }
        return true;
    }

    public function VEN002FinalizaAgruparPedidosEmEtapas($cache)
    {
        global $g;

        if (empty($cache['contexto']) || empty($cache['contexto']['pedidosSelecionados']) || !is_array($cache['contexto']['pedidosSelecionados'])) {
            return "Não foi possível finalizar o agrupamento em etapas sem os pedidos de origem.";
        }

        $pedidosSelecionados = array();
        foreach ($cache['contexto']['pedidosSelecionados'] as $pedidoId) {
            $pedidoId = (int) $pedidoId;
            if ($pedidoId > 0) {
                $pedidosSelecionados[$pedidoId] = $pedidoId;
            }
        }

        if (count($pedidosSelecionados) < 2) {
            return "Não foi possível finalizar o agrupamento com menos de dois pedidos.";
        }
        if (count($pedidosSelecionados) != count($cache['contexto']['pedidosSelecionados'])) {
            return "Não foi possível finalizar o agrupamento porque o cache possui pedido de origem inválido ou repetido.";
        }

        $novoT005_Id = !empty($cache['contexto']['novoT005_Id']) ? (int) $cache['contexto']['novoT005_Id'] : 0;
        if ($novoT005_Id <= 0) {
            return "Não foi possível finalizar o agrupamento em etapas sem o novo pedido agrupado.";
        }

        if (isset($pedidosSelecionados[$novoT005_Id])) {
            return "Não foi possível finalizar o agrupamento porque o pedido novo {$novoT005_Id} também consta como pedido de origem.";
        }

        if (!isset($cache['totalEtapas'], $cache['etapaAtual'], $cache['etapas'], $cache['itensProcessados']) || !is_array($cache['etapas']) || !is_array($cache['itensProcessados'])) {
            return "Não foi possível finalizar o agrupamento porque o cache das etapas está incompleto.";
        }

        $totalEtapas = (int) $cache['totalEtapas'];
        if ($totalEtapas <= 0 || $totalEtapas != count($cache['etapas']) || $totalEtapas != (int) $cache['etapaAtual'] || $totalEtapas != count($cache['itensProcessados'])) {
            return "Não foi possível finalizar o agrupamento porque a quantidade de etapas processadas não confere com a quantidade prevista.";
        }

        $itensOrigem = array();
        foreach ($cache['etapas'] as $etapa) {
            $T006_Id = !empty($etapa['T006_Id']) ? (int) $etapa['T006_Id'] : 0;
            $T005_Id = !empty($etapa['T005_Id']) ? (int) $etapa['T005_Id'] : 0;
            if ($T006_Id <= 0 || !isset($pedidosSelecionados[$T005_Id])) {
                return "Não foi possível finalizar o agrupamento porque uma etapa possui item ou pedido de origem inválido.";
            }
            if (isset($itensOrigem[$T006_Id])) {
                return "Não foi possível finalizar o agrupamento porque o item de origem {$T006_Id} aparece em mais de uma etapa.";
            }
            $itensOrigem[$T006_Id] = $T005_Id;
        }

        $itensNovos = array();
        $itensProcessados = array();
        $T219Esperados = array();
        $T238Esperados = array();
        foreach ($cache['itensProcessados'] as $itemProcessado) {
            if (!isset($itemProcessado['snapshotsT219'], $itemProcessado['snapshotsT238']) || !is_array($itemProcessado['snapshotsT219']) || !is_array($itemProcessado['snapshotsT238'])) {
                return "Não foi possível finalizar o agrupamento porque o cache dos vínculos de um item processado está incompleto.";
            }
            $T006_Id_Origem = !empty($itemProcessado['T006_Id_Origem']) ? (int) $itemProcessado['T006_Id_Origem'] : 0;
            $T006_Id_Novo = !empty($itemProcessado['T006_Id_Novo']) ? (int) $itemProcessado['T006_Id_Novo'] : 0;
            if (!isset($itensOrigem[$T006_Id_Origem])) {
                return "Não foi possível finalizar o agrupamento porque o item processado {$T006_Id_Origem} não pertence às etapas previstas.";
            }
            if (isset($itensProcessados[$T006_Id_Origem])) {
                return "Não foi possível finalizar o agrupamento porque o item de origem {$T006_Id_Origem} foi processado mais de uma vez.";
            }
            if ($T006_Id_Novo <= 0 || isset($itensNovos[$T006_Id_Novo])) {
                return "Não foi possível finalizar o agrupamento porque o item novo do item de origem {$T006_Id_Origem} é inválido ou foi repetido.";
            }

            $itensProcessados[$T006_Id_Origem] = $T006_Id_Novo;
            $itensNovos[$T006_Id_Novo] = $T006_Id_Origem;
            if (!empty($itemProcessado['snapshotsT219']) && is_array($itemProcessado['snapshotsT219'])) {
                foreach ($itemProcessado['snapshotsT219'] as $T219) {
                    $T219_Id = !empty($T219['T219_Id']) ? (int) $T219['T219_Id'] : 0;
                    if ($T219_Id <= 0 || isset($T219Esperados[$T219_Id])) {
                        return "Não foi possível finalizar o agrupamento porque existe um vínculo T219 inválido ou repetido no item {$T006_Id_Origem}.";
                    }
                    $T219Esperados[$T219_Id] = $T006_Id_Novo;
                }
            }
            if (!empty($itemProcessado['snapshotsT238']) && is_array($itemProcessado['snapshotsT238'])) {
                foreach ($itemProcessado['snapshotsT238'] as $T238) {
                    $T238_Id = !empty($T238['T238_Id']) ? (int) $T238['T238_Id'] : 0;
                    if ($T238_Id <= 0 || isset($T238Esperados[$T238_Id])) {
                        return "Não foi possível finalizar o agrupamento porque existe um vínculo T238 inválido ou repetido no item {$T006_Id_Origem}.";
                    }
                    $T238Esperados[$T238_Id] = $T006_Id_Novo;
                }
            }
        }

        if (count($itensProcessados) != count($itensOrigem)) {
            return "Não foi possível finalizar o agrupamento porque nem todos os itens previstos foram processados.";
        }

        $sqlAutocommit = <<<SQL
            SET AUTOCOMMIT=0
SQL;
        $autocommit = mysql_query($sqlAutocommit);
        $erro = mysql_error();
        if ($autocommit === false || !empty($erro)) {
            return "Erro ao preparar a transação de finalização do pedido agrupado {$novoT005_Id}: {$erro}";
        }
        $sqlTransaction = <<<SQL
            START TRANSACTION
SQL;
        $transaction = mysql_query($sqlTransaction);
        $erro = mysql_error();
        if ($transaction === false || !empty($erro)) {
            return "Erro ao iniciar a transação de finalização do pedido agrupado {$novoT005_Id}: {$erro}";
        }
        $sqlRollback = <<<SQL
            ROLLBACK
SQL;

        $sqlTotalizacao = <<<SQL
            CALL T005_Gravar_Totalizacao_4('{$novoT005_Id}')
SQL;
        $totalizacao = mysql_query($sqlTotalizacao);
        $erro = mysql_error();
        if ($totalizacao === false || !empty($erro)) {
            mysql_query($sqlRollback);
            return "Erro ao totalizar o novo pedido agrupado: {$erro}";
        }

        foreach ($pedidosSelecionados as $pedidoId) {
            $sqlT005 = <<<SQL
                UPDATE T005
                SET T005_T005_Id_Agrupado = '{$novoT005_Id}',
                    T005_Flag_Marcado = 'N'
                WHERE T005_Id = '{$pedidoId}'
SQL;
            $updtT005 = mysql_query($sqlT005);
            $erro = mysql_error();
            if ($updtT005 === false || !empty($erro)) {
                mysql_query($sqlRollback);
                return "Erro ao marcar o pedido {$pedidoId} como agrupado: {$erro}";
            }

            $insT178 = <<<SQL
                INSERT INTO T178 (
                    T178_T005_Id,
                    T178_C007_Id,
                    T178_Data,
                    T178_Descricao,
                    T178_Observacao
                ) VALUES (
                    '{$novoT005_Id}',
                    '{$g['usuarioAtual']}',
                    CURRENT_TIMESTAMP(),
                    'Pedido agrupado {$pedidoId}',
                    ''
                )
SQL;
            $resT178 = mysql_query($insT178);
            $erro = mysql_error();
            if ($resT178 === false || !empty($erro)) {
                mysql_query($sqlRollback);
                return "Erro ao registrar o histórico do pedido {$pedidoId}: {$erro}";
            }

            $cache['historicosCriados'][] = $g['mysqlLastId'];
        }

        $novosItensIds = array_keys($itensNovos);

        if (!empty($novosItensIds)) {
            $novosItensIdsSql = implode(', ', $novosItensIds);
            $updtT238 = <<<SQL
                UPDATE T238
                SET T238_T005_Id = '{$novoT005_Id}'
                WHERE T238_T006_Id IN ({$novosItensIdsSql})
SQL;
            $resT238 = mysql_query($updtT238);
            $erro = mysql_error();
            if ($resT238 === false || !empty($erro)) {
                mysql_query($sqlRollback);
                return "Erro ao atualizar o vínculo dos itens separados com o novo pedido agrupado: {$erro}";
            }
        }

        // A conferência usa os IDs registrados no cache. Dessa forma, itens adicionais
        // criados por triggers são aceitos, mas nenhum item previsto pode desaparecer.
        $pedidosIds = $pedidosSelecionados;
        $pedidosIds[$novoT005_Id] = $novoT005_Id;
        $pedidosIdsSql = implode(', ', $pedidosIds);
        $sqlT005 = <<<SQL
            SELECT
                T005_Id,
                T005_T005_Id_Agrupado
            FROM T005
            WHERE T005_Id IN ({$pedidosIdsSql})
SQL;
        $resT005 = mysql_query($sqlT005);
        $erro = mysql_error();
        if ($resT005 === false || !empty($erro)) {
            mysql_query($sqlRollback);
            return "Erro ao validar os pedidos do agrupamento: {$erro}";
        }
        $T005 = array();
        while ($rowT005 = mysql_fetch_assoc($resT005)) {
            $T005[(int) $rowT005['T005_Id']] = $rowT005;
        }
        if (!isset($T005[$novoT005_Id])) {
            mysql_query($sqlRollback);
            return "Não foi possível validar o pedido agrupado {$novoT005_Id}.";
        }
        foreach ($pedidosSelecionados as $pedidoId) {
            if (!isset($T005[$pedidoId]) || (int) $T005[$pedidoId]['T005_T005_Id_Agrupado'] != $novoT005_Id) {
                mysql_query($sqlRollback);
                return "O pedido de origem {$pedidoId} não foi vinculado corretamente ao pedido agrupado {$novoT005_Id}.";
            }
        }

        $itensOrigemIds = array_keys($itensOrigem);
        $T006IdsSql = implode(', ', array_merge($itensOrigemIds, $novosItensIds));
        $sqlT006 = <<<SQL
            SELECT
                T006_Id,
                T006_T005_Id
            FROM T006
            WHERE T006_Id IN ({$T006IdsSql})
SQL;
        $resT006 = mysql_query($sqlT006);
        $erro = mysql_error();
        if ($resT006 === false || !empty($erro)) {
            mysql_query($sqlRollback);
            return "Erro ao validar os itens do pedido agrupado {$novoT005_Id}: {$erro}";
        }
        $T006 = array();
        while ($rowT006 = mysql_fetch_assoc($resT006)) {
            $T006[(int) $rowT006['T006_Id']] = $rowT006;
        }
        foreach ($itensOrigem as $T006_Id => $T005_Id) {
            if (!isset($T006[$T006_Id]) || (int) $T006[$T006_Id]['T006_T005_Id'] != $T005_Id) {
                mysql_query($sqlRollback);
                return "O item de origem {$T006_Id} não pertence ao pedido selecionado {$T005_Id}.";
            }
        }
        foreach ($novosItensIds as $T006_Id) {
            if (!isset($T006[$T006_Id]) || (int) $T006[$T006_Id]['T006_T005_Id'] != $novoT005_Id) {
                mysql_query($sqlRollback);
                return "O item novo {$T006_Id} não foi vinculado corretamente ao pedido agrupado {$novoT005_Id}.";
            }
        }

        $T219Ids = array_keys($T219Esperados);
        $whereT219 = "T219_T006_Id IN (" . implode(', ', $itensOrigemIds) . ")";
        if (!empty($T219Ids)) {
            $whereT219 .= " OR T219_Id IN (" . implode(', ', $T219Ids) . ")";
        }
        $sqlT219 = <<<SQL
            SELECT
                T219_Id,
                T219_T006_Id
            FROM T219
            WHERE {$whereT219}
SQL;
        $resT219 = mysql_query($sqlT219);
        $erro = mysql_error();
        if ($resT219 === false || !empty($erro)) {
            mysql_query($sqlRollback);
            return "Erro ao validar os vínculos T219 do pedido agrupado {$novoT005_Id}: {$erro}";
        }
        $T219 = array();
        while ($rowT219 = mysql_fetch_assoc($resT219)) {
            if (isset($itensOrigem[(int) $rowT219['T219_T006_Id']])) {
                mysql_query($sqlRollback);
                return "O vínculo T219 {$rowT219['T219_Id']} permaneceu associado ao item de origem {$rowT219['T219_T006_Id']}.";
            }
            $T219[(int) $rowT219['T219_Id']] = $rowT219;
        }
        foreach ($T219Esperados as $T219_Id => $T006_Id) {
            if (!isset($T219[$T219_Id]) || (int) $T219[$T219_Id]['T219_T006_Id'] != $T006_Id) {
                mysql_query($sqlRollback);
                return "O vínculo T219 {$T219_Id} não foi transferido corretamente para o item {$T006_Id}.";
            }
        }

        $T238Ids = array_keys($T238Esperados);
        $whereT238 = "T238_T006_Id IN (" . implode(', ', $itensOrigemIds) . ")";
        if (!empty($T238Ids)) {
            $whereT238 .= " OR T238_Id IN (" . implode(', ', $T238Ids) . ")";
        }
        $sqlT238 = <<<SQL
            SELECT
                T238_Id,
                T238_T005_Id,
                T238_T006_Id
            FROM T238
            WHERE {$whereT238}
SQL;
        $resT238 = mysql_query($sqlT238);
        $erro = mysql_error();
        if ($resT238 === false || !empty($erro)) {
            mysql_query($sqlRollback);
            return "Erro ao validar os vínculos T238 do pedido agrupado {$novoT005_Id}: {$erro}";
        }
        $T238 = array();
        while ($rowT238 = mysql_fetch_assoc($resT238)) {
            if (isset($itensOrigem[(int) $rowT238['T238_T006_Id']])) {
                mysql_query($sqlRollback);
                return "O vínculo T238 {$rowT238['T238_Id']} permaneceu associado ao item de origem {$rowT238['T238_T006_Id']}.";
            }
            $T238[(int) $rowT238['T238_Id']] = $rowT238;
        }
        foreach ($T238Esperados as $T238_Id => $T006_Id) {
            if (!isset($T238[$T238_Id]) || (int) $T238[$T238_Id]['T238_T006_Id'] != $T006_Id || (int) $T238[$T238_Id]['T238_T005_Id'] != $novoT005_Id) {
                mysql_query($sqlRollback);
                return "O vínculo T238 {$T238_Id} não foi transferido corretamente para o item {$T006_Id} do pedido {$novoT005_Id}.";
            }
        }

        $sqlCommit = <<<SQL
            COMMIT
SQL;
        $commit = mysql_query($sqlCommit);
        $erro = mysql_error();
        if ($commit === false || !empty($erro)) {
            if (empty($erro)) {
                $erro = 'Falha ao executar o COMMIT da finalização.';
            }
            mysql_query($sqlRollback);
            return "Erro ao concluir a finalização do pedido agrupado {$novoT005_Id}: {$erro}";
        }
        return true;
    }

    public function VEN002RollbackAgruparPedidosEmEtapas($cache, $mensagemErroOriginal)
    {
        global $g;

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        $itensProcessados = array_reverse($cache['itensProcessados']);
        foreach ($itensProcessados as $itemProcessado) {
            if (!empty($itemProcessado['snapshotsT219'])) {
                foreach ($itemProcessado['snapshotsT219'] as $snapshotT219) {
                    $sets = array();
                    foreach ($snapshotT219 as $campo => $valor) {
                        if ($campo == 'T219_Id') {
                            continue;
                        }
                        $sets[] = "{$campo} = " . (($valor === null) ? "NULL" : "'" . mysql_real_escape_string($valor) . "'");
                    }
                    if (!empty($sets)) {
                        mysql_query("UPDATE T219 SET " . implode(', ', $sets) . " WHERE T219_Id = '{$snapshotT219['T219_Id']}'");
                    }
                    if (!empty(mysql_error())) {
                        mysql_query("ROLLBACK");
                        return "{$mensagemErroOriginal}<br>Erro ao restaurar os vínculos de compra do item {$itemProcessado['T006_Id_Origem']}: " . mysql_error();
                    }
                }
            }

            if (!empty($itemProcessado['snapshotsT238'])) {
                foreach ($itemProcessado['snapshotsT238'] as $snapshotT238) {
                    $sets = array();
                    foreach ($snapshotT238 as $campo => $valor) {
                        if ($campo == 'T238_Id') {
                            continue;
                        }
                        $sets[] = "{$campo} = " . (($valor === null) ? "NULL" : "'" . mysql_real_escape_string($valor) . "'");
                    }
                    if (!empty($sets)) {
                        mysql_query("UPDATE T238 SET " . implode(', ', $sets) . " WHERE T238_Id = '{$snapshotT238['T238_Id']}'");
                    }
                    if (!empty(mysql_error())) {
                        mysql_query("ROLLBACK");
                        return "{$mensagemErroOriginal}<br>Erro ao restaurar os vínculos de separação do item {$itemProcessado['T006_Id_Origem']}: " . mysql_error();
                    }
                }
            }

            if (!empty($itemProcessado['T006A_Id_Novo'])) {
                mysql_query("DELETE FROM T006A WHERE T006A_Id = '{$itemProcessado['T006A_Id_Novo']}'");
                $erro = mysql_error();
                if (!empty($erro)) {
                    mysql_query("ROLLBACK");
                    return "{$mensagemErroOriginal}<br>Erro ao remover o item estendido {$itemProcessado['T006A_Id_Novo']}: {$erro}";
                }
            }

            if (!empty($itemProcessado['T006_Id_Novo'])) {
                mysql_query("DELETE FROM T006 WHERE T006_Id = '{$itemProcessado['T006_Id_Novo']}'");
                $erro = mysql_error();
                if (!empty($erro)) {
                    mysql_query("ROLLBACK");
                    return "{$mensagemErroOriginal}<br>Erro ao remover o item gerado {$itemProcessado['T006_Id_Novo']}: {$erro}";
                }
            }
        }

        if (!empty($cache['historicosCriados'])) {
            foreach (array_reverse($cache['historicosCriados']) as $historicoId) {
                mysql_query("DELETE FROM T178 WHERE T178_Id = '{$historicoId}'");
                $erro = mysql_error();
                if (!empty($erro)) {
                    mysql_query("ROLLBACK");
                    return "{$mensagemErroOriginal}<br>Erro ao remover o histórico {$historicoId}: {$erro}";
                }
            }
        }

        if (!empty($cache['contexto']['pedidosSelecionados'])) {
            foreach ($cache['contexto']['pedidosSelecionados'] as $pedidoId) {
                mysql_query("UPDATE T005 SET T005_T005_Id_Agrupado = 0 WHERE T005_Id = '{$pedidoId}'");
                $erro = mysql_error();
                if (!empty($erro)) {
                    mysql_query("ROLLBACK");
                    return "{$mensagemErroOriginal}<br>Erro ao restaurar o pedido {$pedidoId}: {$erro}";
                }
            }
        }

        if (!empty($cache['contexto']['novoT005A_Id'])) {
            mysql_query("DELETE FROM T005A WHERE T005A_Id = '{$cache['contexto']['novoT005A_Id']}'");
            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return "{$mensagemErroOriginal}<br>Erro ao remover a tabela estendida do novo pedido: {$erro}";
            }
        }

        if (!empty($cache['contexto']['novoT005_Id'])) {
            mysql_query("DELETE FROM T005 WHERE T005_Id = '{$cache['contexto']['novoT005_Id']}'");
            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return "{$mensagemErroOriginal}<br>Erro ao remover o novo pedido agrupado: {$erro}";
            }
        }

        mysql_query("COMMIT");
        $arquivoCache = "/tmp/ven002-agrupar-pedidos-etapas-{$g['usuarioAtual']}-{$cache['processoKey']}";
        if (file_exists($arquivoCache)) {
            @unlink($arquivoCache);
        }

        return $mensagemErroOriginal . "<br>As alterações realizadas nas etapas anteriores foram revertidas.";
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

            $msg = "";

            if(!empty($g['C031']['portadoresLiberados'])){
                $portadoresValidos = explode(",", str_replace(" ", "", trim($g['C031']['portadoresLiberados'],",")));
                $sqlT090 = mysql_query("SELECT T090_D027_Id FROM T090 WHERE T090_T005_Id = '{$T005_Id}' GROUP BY T090_D027_Id");
                $liberado = true;
                while($rowT090 = mysql_fetch_array($sqlT090)){
                    if(!in_array($rowT090['T090_D027_Id'], $portadoresValidos) && $liberado){
                        $liberado = false;
                        $msg = mysql_real_escape_string("- Obrigatório análise de crédito para o portador {$rowT090['T090_D027_Id']}");
                    }
                }
                if($liberado){
                    $msgLiberado = mysql_real_escape_string("<b>Motivo não caiu na liberação de crédito:</b> <br />- Portador não necessita análise de crédito, portadores liberados: ".$g['C031']['portadoresLiberados']);
                    mysql_query("UPDATE T005 SET T005_Motivo_Pedido_Liberacao='{$msgLiberado}' WHERE T005_Id='{$T005_Id}'");
                    mysql_query("insert into T178 (T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) values ('{$T005_Id}','{$g['usuarioAtual']}',CURRENT_TIMESTAMP(),'$msgLiberado','')");
                    return true;
                }
            }

            $T005  = mysql_query("SELECT * FROM T005 
                                      LEFT JOIN D024 ON D024_Id=T005_D024_Id 
                                      LEFT JOIN D006 ON D006_Id=T005_D006_Id 
                                          WHERE T005_Id='{$T005_Id}'");
            $mT005 = mysql_fetch_array($T005);
           
            $duplicatasVencidas = 0;
            $sqlT002            = mysql_query("SELECT * FROM T002 
                                                   LEFT JOIN T117 ON T117_Id = T002_T117_Id 
                                                       WHERE T002_D024_Id = '{$mT005['T005_D024_Id']}' 
                                                         AND T002_Data_Recebimento = '0000-00-00' 
                                                         AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado is null)");
            
            log('D024='.$mT005['T005_D024_Id']);
            
            while ($T002 = mysql_fetch_array($sqlT002)) {
                if ($T002['T117_Id'] > 0 && $T002['T117_Data_Vencimento'] < date("Y-m-d")) {
                    $duplicatasVencidas++;
                } else if ($T002['T002_Data_Vencimento'] < date("Y-m-d")) {
                    $duplicatasVencidas++;
                }
            }

            $D024_Id = $mT005['D024_Id'];
            $filtroSaldo = "D024_Id = '{$D024_Id}'";

            //calcula a soma dos limites das empresas vinculadas a um Cnpj Matriz
            if(($g['C031']['somarLimiteCnpjMatriz'] == 'S') && (!empty($mT005['D024_Cnpj_Matriz']))){
                $filtroSaldo = "D024_Cnpj_Matriz = '{$mT005['D024_Cnpj_Matriz']}'";
                //$msg .= $filtroSaldo;

                $dadosLimite = mysql_query("SELECT SUM(D024_Valor_Limite) AS D024_Valor_Limite, 
                                        SUM(D024_Valor_Credito_Antecipado) AS D024_Valor_Credito_Antecipado, 
                                        SUM(D024_Valor_Credito_Troca) AS D024_Valor_Credito_Troca 
                                        FROM D024 
                                        WHERE $filtroSaldo");
                $dadosLimite = mysql_fetch_array($dadosLimite);
                $mT005['D024_Valor_Limite'] = $dadosLimite['D024_Valor_Limite'];
            }
        
            $valorLimite     = $mT005['D024_Valor_Limite'];
            
            //Calcula saldo do cliente
            $sqlSaldo    = "SELECT ifnull(sum(if(T002_Data_Vencimento<curdate(),T002_Valor_Total,0)),0) as Vencido, 
                                ifnull(sum(if(T002_Data_Vencimento>=curdate(),T002_Valor_Total,0)),0) as Vencer
                            FROM T002
                            LEFT JOIN D024 ON D024_Id=T002_D024_Id
                            WHERE $filtroSaldo
                            AND T002_Data_Recebimento='0000-00-00' 
                            AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado is null)";
            $resultSaldo = mysql_query($sqlSaldo);
            $linhaSaldo  = mysql_fetch_array($resultSaldo);
            
            //Verifica os pedidos em aberto
            $sqlPedidos   = mysql_query("SELECT ifnull(sum(T005_Valor_Total),0) as Pedidos
                                       FROM T005 
                                  LEFT JOIN T007 ON T007_T005_Id=T005_Id 
                                  LEFT JOIN D024 ON D024_Id=T005_D024_Id
                                      WHERE $filtroSaldo
                                        AND T005_Flag_Status != 8
                                        AND (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null) 
                                        AND T007_Id IS NULL");
            $linhaPedidos = mysql_fetch_array($sqlPedidos); 

            // CALCULA SALDO LIMITE
            $saldoAtual = ($valorLimite - ($linhaSaldo['Vencido'] + $linhaSaldo['Vencer'] + $linhaPedidos['Pedidos']));

            $msgLiberado = "<b>Motivo não caiu na análise de crédito:</b> <br />";
            if ($duplicatasVencidas > 0) {
                $msg .= ($msg != '') ? '<br>' : '';
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
    
            //$msg .= "$saldoAtual = ($valorLimite - ({$linhaSaldo['Vencido']} + {$linhaSaldo['Vencer']} + {$linhaPedidos['Pedidos']}));";

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
     * cotarProduto
     *
     * @param Form $form
     * @return true
     */    
    public function cotarProduto($form)
    {
        global $g;
        $T006_Id         = $form->campoValorEnviado('T189_T006_Id');
        $D009_Id         = $form->campoValorEnviado('T189_D009_Id');
        $T189_Observacao = $form->campoValorEnviado('T189_Observacao');
        $C007_Id         = $g['usuarioAtual'];
        
        //Busca dados da T004 para inserir na Tabela de Requisição
        $sql = "SELECT * FROM T006 
        LEFT JOIN T005 ON T005_Id = T006_T005_Id
        LEFT JOIN D024 ON D024_Id = T005_D024_Id
        LEFT JOIN D009 ON D009_Id = T006_D009_Id
        WHERE T006_Id = {$T006_Id}";
        $row = mysql_fetch_assoc(mysql_query($sql));
        
        $row['T006_Quantidade_Estoque'] = $row['T006_Quantidade_Estoque'] < 0 ? 0 : $row['T006_Quantidade_Estoque'];
        
        $quantidade = $row['T006_Quantidade'] - $row['T006_Quantidade_Estoque'];
        
        if ($quantidade <= 0) {
            $quantidade = 0;
        }
        
        //Insere dados na Tabela de Requisição 
        $sql = <<<EOT
      INSERT INTO 
      T189 
        (T189_T004_Id, T189_T006_Id, T189_D009_Id, T189_C007_Id, T189_Data_Criado, T189_Descricao_Produto, T189_Quantidade, T189_Observacao, T189_Detalhes, T189_Flag_Tipo) 
      VALUES 
        ('', '{$T006_Id}', '{$D009_Id}', '{$C007_Id}', CURRENT_TIMESTAMP(), '{$row['T006_Descricao_Produto']}', '{$quantidade}', '{$T189_Observacao}', 'Pedido Nº: {$row['T005_Id']} - {$row['D024_Nome_Empresa']}', 'C');
EOT;
        mysql_query($sql);
        
        //Seta a Flag comprar = N, para caso esteja selecionada, não permita comprar sem um fornecedor
        mysql_query("UPDATE T006 SET T006_Flag_Comprar = 'N' WHERE T006_Id = '{$T006_Id}'");
        return true;
    }
    
    /**
     * comprarProduto
     *
     * @param Form $form
     * @return true
     */    
    public function comprarProduto($form)
    {
        global $g;
        $T006_Id         = $form->campoValorEnviado('T189_T006_Id');
        $D009_Id         = $form->campoValorEnviado('T189_D009_Id');
        $T189_Observacao = $form->campoValorEnviado('T189_Observacao');
        $C007_Id         = $g['usuarioAtual'];
        
        $sql  = "SELECT * FROM T006
        LEFT JOIN T005 ON T005_Id = T006_T005_Id
        LEFT JOIN D024 ON D024_Id = T005_D024_Id
        LEFT JOIN D009 ON D009_Id = T006_D009_Id
        WHERE T006_Id = {$T006_Id}";
        $row  = mysql_fetch_assoc(mysql_query($sql));
        // Verifica se já existe uma Requisição de cotação
        $sql  = "SELECT * FROM T189 WHERE T189_T006_Id = '{$T006_Id}' AND T189_Flag_Tipo = 'C'";
        $T189 = mysql_query($sql);
        if (mysql_num_rows($T189) > 0) {
            $T189    = mysql_fetch_array($T189);
            $T189_Id = $T189[0];
            mysql_query("UPDATE T189 
            LEFT JOIN T059 ON T059_T189_Id=T189_Id 
            LEFT JOIN T174 ON T059_Id=T174_T059_Id 
            SET 
            T189_Flag_Tipo = 'P', 
            T189_Detalhes = 'Pedido Nº: {$row['T005_Id']} - {$row['D024_Nome_Empresa']}', 
            T189_Flag_Status = 'D',
            T174_Flag_Gerar_OC='1' 
            WHERE 
            T189_T006_Id = '{$T006_Id}'");
            return true;
        } else {
            
            $row['T006_Quantidade_Estoque'] = $row['T006_Quantidade_Estoque'] < 0 ? 0 : $row['T006_Quantidade_Estoque'];
            
            $quantidade = $row['T006_Quantidade'] - $row['T006_Quantidade_Estoque'];
            
            if ($quantidade <= 0) {
                $quantidade = 0;
            }
            //Insere dados na Tabela de Requisição 
            $sql = <<<EOT
        INSERT INTO T189 (
            T189_T006_Id, 
            T189_D009_Id, 
            T189_C007_Id, 
            T189_Data_Criado, 
            T189_Descricao_Produto,
            T189_Quantidade, 
            T189_Observacao, 
            T189_Detalhes, 
            T189_Flag_Tipo,
            T189_Flag_Status
          ) VALUES (
            '{$T006_Id}',
            '{$D009_Id}', 
            '{$C007_Id}', 
            CURRENT_TIMESTAMP(),
            '{$row['T006_Descricao_Produto']}', 
            '{$quantidade}', 
            '{$T189_Observacao}', 
            'Pedido Nº: {$row['T005_Id']} - {$row['D024_Nome_Empresa']}', 
            'P',
            'D' 
          );
EOT;
            mysql_query($sql);
            return true;
        }
    }
    
    /**
     * modificarProdutosSelecionados
     *
     * @param Form $form
     * @return true
     */    
    public function modificarProdutosSelecionados($form)
    {
        global $g;
        $T006_Id         = $form->campoValorEnviado('T189_T006_Id');
        $D009_Id         = $form->campoValorEnviado('T189_D009_Id');
        $T189_Observacao = $form->campoValorEnviado('T189_Observacao');
        $C007_Id         = $g['usuarioAtual'];
        
        $sql  = "SELECT * FROM T006
                LEFT JOIN T005 ON T005_Id = T006_T005_Id
                LEFT JOIN D024 ON D024_Id = T005_D024_Id
                WHERE T006_Id = {$T006_Id}";
        $row  = mysql_fetch_assoc(mysql_query($sql));
        // Verifica se já existe uma Requisição de cotação
        $sql  = "SELECT * FROM T189 WHERE T189_T006_Id = '{$T006_Id}' AND T189_Flag_Tipo = 'C'";
        $T189 = mysql_query($sql);
        if (mysql_num_rows($T189) > 0) {
            $T189    = mysql_fetch_array($T189);
            $T189_Id = $T189[0];
            mysql_query("UPDATE T189 
                        SET 
                        T189_Flag_Tipo = 'M', 
                        T189_Detalhes = 'Pedido Nº: {$row['T005_Id']} - {$row['D024_Nome_Empresa']}', 
                        T189_Flag_Status = 'D'
                        WHERE 
                        T189_T006_Id = '{$T006_Id}'");
            return true;
        } else {
            //Insere dados na Tabela de Requisição 
            $sql = <<<EOT
                INSERT INTO T189 (
                        T189_T006_Id, 
                        T189_D009_Id, 
                        T189_C007_Id, 
                        T189_Data_Criado, 
                        T189_Descricao_Produto,
                        T189_Quantidade, 
                        T189_Observacao, 
                        T189_Detalhes, 
                        T189_Flag_Tipo,
                        T189_Flag_Status
                    ) VALUES (
                        '{$T006_Id}',
                        '{$D009_Id}', 
                        '{$C007_Id}', 
                        CURRENT_TIMESTAMP(),
                        '{$row['T006_Descricao_Produto']}', 
                        '{$row['T006_Quantidade']}', 
                        '{$T189_Observacao}', 
                        'Pedido Nº: {$row['T005_Id']} - {$row['D024_Nome_Empresa']}', 
                        'M',
                        'D' 
                    );
EOT;
            mysql_query($sql);
            return true;
        }
    }
    
    /**
     * ven002atualizarCFOP
     *
     * @param String $T005_Id
     * @return String
     */
    public function ven002atualizarCFOP($T005_Id, $T006_Id = false)
    {
        require_once("bibliotecas/classes/VEN001.php");
        $VEN001 = new VEN001();
        global $g;
        //Verifica se foi passado um T004 específico
        if($T006_Id !== false){
            $extra = "AND T006_Id = '{$T006_Id}'";
        }

        $sqlT005 = mysql_query("SELECT T005_D024_Id,
                                       T006_D009_Id,
                                       T005_C004_Id,
                                       T006_Flag_Destino_Produto,
                                       T005_C004_Id_Origem,
                                       T005_C004_Id_Estoque,
                                       D001_Flag_Pre_Cadastro,
                                       T006_Id,
                                       D148_D018_Id
                                  FROM T005 
                             LEFT JOIN T006 ON T005_Id=T006_T005_Id 
                             LEFT JOIN D009 ON D009_Id=T006_D009_Id
                             LEFT JOIN D049 ON D049_Id=D009_D049_Id
                             LEFT JOIN D001 ON D001_Id=D049_D001_Id
                             LEFT JOIN D148 ON D148_Id = T005_D148_Id_Entrega
                                 WHERE T005_Id = '{$T005_Id}' {$extra}");

        
        while ($T005 = mysql_fetch_array($sqlT005)) {

            $contextoCFOP = [
                'ufEnderecoEntrega' => $T005['D148_D018_Id']
            ];

           /* $D006_Id = $VEN001->retornaCFOP($T005['T005_D024_Id'], 
                                            $T005['T006_D009_Id'], 
                                            $T005['T005_C004_Id'], 
                                            $T005['T006_Flag_Destino_Produto'], 
                                            false, 
                                            false, 
                                            $T005['T005_C004_Id_Origem'], 
                                            $T005['T005_C004_Id_Estoque']);

            $demonstrativoCFOP = $VEN001->retornaCFOP($T005['T005_D024_Id'], 
                                                      $T005['T006_D009_Id'], 
                                                      $T005['T005_C004_Id'], 
                                                      $T005['T006_Flag_Destino_Produto'], 
                                                      false, 
                                                      true, 
                                                      $T005['T005_C004_Id_Origem'], 
                                                      $T005['T005_C004_Id_Estoque']); */

            $D006_Id = $VEN001->retornaCFOPContexto($T005['T005_D024_Id'], 
                                            $T005['T006_D009_Id'], 
                                            $T005['T005_C004_Id'], 
                                            $T005['T006_Flag_Destino_Produto'], 
                                            false, 
                                            false, 
                                            $T005['T005_C004_Id_Origem'], 
                                            $T005['T005_C004_Id_Estoque'],
                                            false,
                                            $contextoCFOP);

            $demonstrativoCFOP = $VEN001->retornaCFOPContexto($T005['T005_D024_Id'], 
                                                      $T005['T006_D009_Id'], 
                                                      $T005['T005_C004_Id'], 
                                                      $T005['T006_Flag_Destino_Produto'], 
                                                      false, 
                                                      true, 
                                                      $T005['T005_C004_Id_Origem'], 
                                                      $T005['T005_C004_Id_Estoque'],
                                                      false,
                                                      $contextoCFOP);

            $demonstrativoCFOP = mysql_real_escape_string($demonstrativoCFOP);
            
            mysql_query("UPDATE T006 SET T006_D006_Id           = '{$D006_Id}', 
                                         T006_D006_Id_Info      = '{$demonstrativoCFOP}', 
                                         T006_Flag_Pre_Cadastro = '{$T005['D001_Flag_Pre_Cadastro']}'
                                   WHERE T006_Id = '{$T005['T006_Id']}'");

            // Grava demonstartivo na estendida também, clientes que estavam com muitas regras não mostrava completo, campo na estentida aceita mais caracteres
            mysqli_query("UPDATE T006A SET T006A_D006_Id_Info = '{$demonstrativoCFOP}'
                                     WHERE T006A_T006_Id = '{$T005['T006_Id']}'");

            //Atualiza as alíquotas dos pedidos
            //O método de atualizar alíquotas foi adaptado para executar em etapas, agora a sua chamada se encontra no form (Lígia Nakazato 27/06/2018)
            //$retorno = $this->atualizarAliquotasPedidos($T005_Id, false, $T005['T006_Id']);
        }
        if ($D006_Id > 0) {
            mysql_query("UPDATE T005 SET T005_D006_Id='{$D006_Id}' WHERE T005_Id='{$T005_Id}'");
        }
        return true;
    }

    /**
     * ven002atualizarCFOPManual
     *
     * @param String $T005_Id
     * @param String $D006_Id
     * @return String
     */     
    public function ven002atualizarCFOPManual($T005_Id, $D006_Id)
    {
        $erroSQLT005 = mysql_query("UPDATE T005 SET T005_D006_Id = '{$D006_Id}' WHERE T005_Id = '{$T005_Id}'");
        $erroSQLT005    = mysql_error();
        if (!empty($erroSQLT005)) {
            return $erroSQLT005;
        }
        $erroSQLT006 = mysql_query("UPDATE T006 SET T006_D006_Id = '{$D006_Id}' WHERE T006_T005_Id = '{$T005_Id}'");
        $erroSQLT006    = mysql_error();
        if (!empty($erroSQLT006)) {
            return $erroSQLT006;
        }

        //Atualiza as alíquotas dos pedidos
        //O método de atualizar alíquotas foi adaptado para executar em etapas, agora a sua chamada se encontra no form (Lígia Nakazato 29/06/2018)
        //$retorno = $this->atualizarAliquotasPedidos($T005_Id);
        return true;
    }

      /**
     * ven002atualizarCFOPManual
     *
     * @param String $T005_Id
     * @param String $D006_Id
     * @return String
     */     
    public function ven002atualizarCFOPManualPedidos($extra, $D006_Id)
    {
        $extra = base64_decode($r_extra);
        $extra = gInsertExtraWhere($extra, "T005_Flag_Marcado = 'S'");
        $sqlT005 = mysql_query("SELECT T005_Id FROM T005 {$extra}");

        $fail=0;
        $erros ="";
        while($mT005 = mysql_fetch_array($sqlT005))
        {  
            mysql_query("UPDATE T005 SET T005_D006_Id = '{$D006_Id}' WHERE T005_Id = '{$mT005['T005_Id']}'");
            $erroSQLT005    = mysql_error();
            if (!empty($erroSQLT005)) {
                $erros .= $erroSQLT005;
            }
            mysql_query("UPDATE T006 SET T006_D006_Id = '{$D006_Id}' WHERE T006_T005_Id = '{$mT005['T005_Id']}'");
            $erroSQLT006    = mysql_error();
            if (!empty($erroSQLT006)) {
                $erros .= $erroSQLT006;
            }

            //Atualiza as alíquotas dos pedidos
            //O método de atualizar alíquotas foi adaptado para executar em etapas, agora a sua chamada se encontra no form (Lígia Nakazato 28/06/2018)
           // $retorno = $this->atualizarAliquotasPedidos($mT005['T005_Id']);

            $retornar=array();
            if (empty($erros)) {
                $retornar[0]=true;
            }else{
                $retornar[0]=false;
                $fail++;
            }
        }  
       
        $retornar[1]="<br>Quantidade de pedidos não atualizados:".$fail. " <br><b>Erros: ".$erros;
        return $retornar;
    }


    /**
     * gerarPedidoTransporte
     *
     * @param String $T005_Id
     * @return String/Bool
     */ 
    public function gerarPedidoTransporte($T005_Id){
        global $g;
        
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        $sqlPedidos = mysql_query("SELECT T006_C004_Id_Estoque, T005_D024_Id FROM T006 LEFT JOIN T005 ON T005_Id=T006_T005_Id WHERE IF(IFNULL(T006_C004_Id_Estoque,0) > 0, T006_C004_Id_Estoque, T005_C004_Id) != T005_C004_Id AND T005_Id = '{$T005_Id}' GROUP BY T006_C004_Id_Estoque");
        while($rowPedidos = mysql_fetch_array($sqlPedidos)){
            $T005  = mysql_query("SELECT * FROM T005 WHERE T005_Id='{$T005_Id}'");
            $mT005 = mysql_fetch_assoc($T005);
            
            $T005_Id_Antigo = $mT005['T005_Id'];
            $campos         = "";
            $valores        = "";

            foreach ($mT005 as $key => $value) {
                if ($key == 'T005_Id') {
                    continue;
                } elseif ($key == "T005_C004_Id") {
                    $campos .= $key . ", ";
                    $valores .= "'{$rowPedidos['T006_C004_Id_Estoque']}',";
                } elseif ($key == "T005_Flag_Status") {
                    $campos .= $key . ", ";
                    $valores .= "'1',";
                } elseif ($key == "T005_Nome_Status") {
                    $campos .= $key . ", ";
                    $valores .= "T005_Status_Pedido (T005_Flag_Status, 1), ";
                } elseif ($key == "T005_T005_Id_Origem") {
                    $campos .= $key . ", ";
                    $valores .= "'{$T005_Id}',";
                } elseif ($key == "T005_C004_Id_Origem") {
                    $campos .= $key . ", ";
                    $valores .= "'{$mT005['T005_C004_Id']}',";
                } elseif ($key == "T005_Data_Emissao") {
                    $campos .= $key . ", ";
                    $valores .= "'" . date("Y-m-d") . "',";
                } elseif (in_array($key, array('T005_Flag_Notificacao','T005_Flag_Notificacao_Estoque'))) {
                    $campos .= $key . ", ";
                    $valores .= "'N',";
                } elseif (in_array($key, array('T005_Flag_Notificacao_Liberacao','T005_Flag_Notificacao_Financeiro'))) {
                    $campos .= $key . ", ";
                    $valores .= "'',";
                } else {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($value) . "',";
                }
            }
            
            $campos  = trim($campos, ', ');
            $valores = trim($valores, ',');
            $insT005 = "INSERT INTO T005 ({$campos}) VALUES ({$valores})";
            $resT005 = mysql_query($insT005);
            if (!$resT005) {
                mysql_query("ROLLBACK");
                return "Erro 1: " . mysql_error();
            }
            $novoT005_Id = $g['mysqlLastId'];
            $campos = $valores = '';
            $sqlProdutos = mysql_query("SELECT * FROM T006 WHERE T006_C004_Id_Estoque = '{$rowPedidos['T006_C004_Id_Estoque']}' AND T006_T005_Id = '{$T005_Id}'");
            while($rowProdutos = mysql_fetch_assoc($sqlProdutos)){
                /**
                 * Buscando o D009 correto para a empresa selecionada.
                 */
                $sqlD049    = "SELECT D009_D049_Id FROM D009 WHERE D009_Id='{$rowProdutos['T006_D009_Id']}'";
                $resultD049 = mysql_query($sqlD049);
                $linhaD049  = mysql_fetch_array($resultD049);
                $erroSQL    = mysql_error();
                if (!empty($erroSQL)) {
                    mysql_query("ROLLBACK");
                    return '2...' . $erroSQL;
                }

                $sqlD009    = "SELECT D009_Id FROM D009 WHERE D009_D049_Id='{$linhaD049['D009_D049_Id']}' AND D009_C004_Id='{$rowPedidos['T006_C004_Id_Estoque']}'";
                $resultD009 = mysql_query($sqlD009);
                $linhaD009  = mysql_fetch_array($resultD009);
                $erroSQL    = mysql_error();
                if (!empty($erroSQL)) {
                    mysql_query("ROLLBACK");
                    return '3' . $erroSQL;
                }
                
                /**
                 * Buscando a CFOP correta para cada item
                 */
                require_once('bibliotecas/classes/VEN001.php');
                $VEN001 = new VEN001();
                $D006_Id = $VEN001->retornaCFOP($rowPedidos['T005_D024_Id'], $linhaD009['D009_Id'], $rowPedidos['T006_C004_Id_Estoque'], false, false, false, $mT005['T005_C004_Id'], $mT005['T005_C004_Id_Estoque']);
                $demonstrativoCFOP = $VEN001->retornaCFOP($rowPedidos['T005_D024_Id'], $linhaD009['D009_Id'], $rowPedidos['T006_C004_Id_Estoque'], false, false, true, $mT005['T005_C004_Id'], $mT005['T005_C004_Id_Estoque']);
                
                // Selecionando todos os dados do T006 atual para fazer um insert do novo T006 com a quantidade nova
                foreach ($rowProdutos as $key => $value) {
                    if ($key == 'T006_Id') {
                        continue;
                    } elseif ($key == "T006_T005_Id") {
                        $campos .= $key . ", ";
                        $valores .= "'" . mysql_real_escape_string($novoT005_Id) . "',";
                    } elseif ($key == "T006_T006_Id_Origem") {
                        $campos .= $key . ", ";
                        $valores .= "'" . mysql_real_escape_string($rowProdutos['T006_Id']) . "',";
                    } elseif ($key == "T006_C004_Id") {
                        $campos .= $key . ", ";
                        $valores .= "'" . mysql_real_escape_string($rowPedidos['T006_C004_Id_Estoque']) . "',";
                    } elseif ($key == "T006_Quantidade_Dividir") {
                        $campos .= $key . ", ";
                        $valores .= "'',";
                    } elseif ($key == "T006_D006_Id") {
                        $campos .= $key . ", ";
                        $valores .= "'" . mysql_real_escape_string($D006_Id) . "',";
                    } elseif ($key == "T006_D006_Id_Info") {
                        $campos .= $key . ", ";
                        $valores .= "'" . mysql_real_escape_string($demonstrativoCFOP) . "',";
                    } elseif ($key == "T006_D009_Id") {
                        $campos .= $key . ", ";
                        $valores .= "'" . mysql_real_escape_string($linhaD009['D009_Id']) . "',";
                    } elseif ($key == "T006_C004_Id_Estoque") {
                        $campos .= $key . ", ";
                        $valores .= "NULL,";
                    } else {
                        $campos .= $key . ", ";
                        $valores .= "'" . mysql_real_escape_string($value) . "',";
                    }
                }
                $campos  = trim($campos, ', ');
                $valores = trim($valores, ',');
                
                // Monta o Insert e executa
                $insT006    = "INSERT INTO T006 ({$campos}) VALUES ({$valores})";
                $campos = $valores = '';
                $resT006    = mysql_query($insT006);
                $lastT006Id = $g['mysqlLastId'];
                $erroSQL    = mysql_error();
                if (!empty($erroSQL)) {
                    mysql_query("ROLLBACK");
                    return '1.1...' . $erroSQL;
                }

                $sqlD001 = mysql_query("SELECT D049_D001_Id FROM D049 LEFT JOIN D009 ON D049_Id=D009_D049_Id WHERE D009_Id = '{$linhaD009['D009_Id']}'");
                $resD001 = mysql_fetch_array($sqlD001);
                // Executa reprocessa histórico do produto
                $CAD002->D001_reprocessa_historico($resD001['D049_D001_Id']);
                mysql_query("call T006_Gravar_Totalizacao_4('{$lastT006Id}')");
            }
            mysql_query("UPDATE T005 SET T005_D006_Id = '{$D006_Id}' WHERE T005_Id = '{$novoT005_Id}'");
        }
        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");
        return true;
    }

    /**
     * gerarDAV
     *
     * @param String $T005_Id
     * @return String/Bool
     */ 
    public function gerarDAV($T005_Id, $imprimirHtml = false){
        global $g;
        
        if(!$imprimirHtml) {
            $dir = $g['pathDados'] . 'tmp/';
            is_dir($dir) OR mkdir($dir, 0777, true);
            $filename             = "dav-{$T005_Id}.pdf";
            $file                 = $dir . $filename;
            $fileFull             = $g['pathRaiz'] . '/' . $file;

            $r_link = '/ven/ven002/form_func-ajax/imprimirDav/?ajax=true&gerarHtml=1&T005_Id=' . $T005_Id;
            $r_link = str_replace(' ', '+', $r_link);
            shell_exec("xvfb-run --server-num " . mt_rand(0, 98) . " -s \"-screen 0 640x480x16\" wkhtmltopdf --quiet --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 \"{$g['urlRaiz']}{$r_link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"");

            return $r_link;
        }
        $T005 = mysql_fetch_array(mysql_query("SELECT T005_Id, 
                                                      T005_D024_Id,
                                                      T005_Valor_Total,
                                                      C007_Nome
                                                 FROM T005 
                                            LEFT JOIN C007 ON C007_Id=T005_C007_Id_Vendedor_Interno
                                                WHERE T005_Id = '{$T005_Id}'"));

        $D024 = mysql_fetch_array(mysql_query("SELECT D024_Flag_Pessoa_Fisica_Juridica,
                                                      D024_Cpf,
                                                      D024_Cnpj,
                                                      D024_Nome_Empresa,
                                                      D024_Endereco,
                                                      D020_Nome_Cidade,
                                                      D018_UF,
                                                      D024_Bairro
                                                 FROM D024 
                                            LEFT JOIN D020 ON D020_Id = D024_D020_Id 
                                            LEFT JOIN D018 ON D018_Id = D024_D018_Id 
                                                WHERE D024_Id = '{$T005['T005_D024_Id']}'"));

        $CpfCnpj = ($D024['D024_Flag_Pessoa_Fisica_Juridica'] == 'F') ? $D024['D024_Cpf'] : $D024['D024_Cnpj'] ;
        $html = <<<EOT
            <style>
                td {
                    border: 1px solid #000;
                    padding: 2px 3px;
                    font-size: 11px;
                    font-family: Arial;
                }
            </style>
            <div style="width: 100%; height: 100%; font-size: 13px;">
                <table cellspacing="0" cellpadding="0" style="width: 100%; height: 120px; border: 1px solid #000; margin-bottom: 15px;">
                    <tr>
                        <td colspan="2" style="text-align: center; font-size: 14px;">
                            <span style="font-size: 13px;">DAV - DOCUMENTO AUXILIAR DE VENDA - PEDIDO</span><br />
                            <b>NÃO É DOCUMENTOS FISCAL - NÃO É VÁLIDO COMO RECIBO E COMO GARANTIA DE MERCADORIA - NÃO COMPROVA PAGAMENTO</b>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center; font-weight: bold;">
                            Identificação do Estabelecimento Emitente
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 90%;">
                            Denominação: {$g['C004']['C004_Nome_Empresa']}
                        </td>
                        <td style="white-space: nowrap; padding: 0 5px">
                            CNPJ: {$g['C004']['C004_Cnpj']}
                        </td>
                    </tr>
                </table>
                <table cellspacing="0" cellpadding="0" style="width: 100%; height: 100px; border: 1px solid #000; margin-bottom: 15px;">
                    <tr>
                        <td colspan="3" style="text-align: center; font-weight: bold;">
                            Identificação do Destinatário
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 60%;">
                            Denominação: {$D024['D024_Nome_Empresa']}
                        </td>
                        <td>
                            CPF/CNPJ: {$CpfCnpj}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 40%;">
                            Endereço: {$D024['D024_Endereco']}
                        </td>
                        <td style="width: 35%;">
                            Cidade - UF: {$D024['D020_Nome_Cidade']} - {$D024['D018_UF']}
                        </td>
                        <td>
                            Bairro: {$D024['D024_Bairro']}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 40%;">
                            <span style="font-weight: bold;">Pedido de Venda:</span> <span style="font-size: 1.3em; font-weight: bold;">{$T005_Id}</span> - <span style="font-weight: bold;"> Vendedor: {$T005['C007_Nome']} </span>
                        </td>
                        <td>
                            Número do Documento Fiscal: <br/>&nbsp;
                        </td>
                    </tr>
                </table>
                <table cellspacing="0" cellpadding="0" style="width: 100%; border: 1px solid #000; margin-bottom: 15px;">
                    <tr>
                        <td style="text-align: center;">
                            Código
                        </td>
                        <td style="text-align: center;">
                            Descrição do Produto
                        </td>
                        <td style="text-align: center;">
                            Quantidade
                        </td>
                        <td style="text-align: center;">
                            Unidade
                        </td>
                        <td style="text-align: center;">
                            Valor Unitário
                        </td>
                        <td style="text-align: center;">
                            Desconto
                        </td>
                        <td style="text-align: center;">
                            Valor Total
                        </td>
                    </tr>
EOT;
        $sqlT006 = mysql_query("SELECT T006_Id,
                                       T006_Quantidade,
                                       T006_Valor_Preco_Sem_Desconto_Unitario,
                                       T006_Valor_Desconto_Unitario,
                                       T006_Valor_Total_Preco,
                                       D037_Unidade,
                                       T006_Codigo_Produto,
                                       T006_Descricao_Produto
                                  FROM T006 
                             LEFT JOIN D037 ON D037_Id=T006_D037_Id 
                                 WHERE T006_T005_Id = '{$T005_Id}'");
        while($T006 = mysql_fetch_array($sqlT006)){
            $T006['T006_Quantidade'] = gCorrigeNumero($T006['T006_Quantidade']);
            $T006['T006_Valor_Preco_Sem_Desconto_Unitario'] = gCorrigeNumero($T006['T006_Valor_Preco_Sem_Desconto_Unitario']);
            $T006['T006_Valor_Desconto_Unitario'] = gCorrigeNumero($T006['T006_Valor_Desconto_Unitario']);
            $T006['T006_Valor_Total_Preco'] = gCorrigeNumero($T006['T006_Valor_Total_Preco']);
            $html .= <<<EOT
                    <tr>
                        <td style="text-align: left;">
                            {$T006['T006_Codigo_Produto']}
                        </td>
                        <td style="text-align: left;">
                            {$T006['T006_Descricao_Produto']}
                        </td>
                        <td style="text-align: right;">
                            {$T006['T006_Quantidade']}
                        </td>
                        <td style="text-align: center;">
                            {$T006['D037_Unidade']}
                        </td>
                        <td style="text-align: right;">
                            {$T006['T006_Valor_Preco_Sem_Desconto_Unitario']}
                        </td>
                        <td style="text-align: right;">
                            {$T006['T006_Valor_Desconto_Unitario']}
                        </td>
                        <td style="text-align: right;">
                            {$T006['T006_Valor_Total_Preco']}
                        </td>
                    </tr>
EOT;
        }
        $T005['T005_Valor_Total'] = gCorrigeNumero($T005['T005_Valor_Total']);
        $html .= <<<EOT
                    <tr>
                        <td colspan="6" style="text-align: right;">
                            Total:
                        </td>
                        <td style="text-align: right;">
                            {$T005['T005_Valor_Total']}
                        </td>
                    </tr>
                </table>
                <table cellspacing="0" cellpadding="0" style="width: 100%; height: 10px; border: 1px solid #000; margin-bottom: 15px;">
                    <tr>
                        <td style="border-bottom: none;">
                            &nbsp;&nbsp;DECLARO QUE RECEBEMOS OS PRODUTOS ACIMA.
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center; height: 50px; vertical-align: bottom; padding-bottom: 8px; border-top: none;">
                                ASSINATURA: &nbsp;&nbsp;&nbsp;&nbsp;__________________________________  &nbsp;&nbsp;&nbsp;&nbsp;DATA: &nbsp;&nbsp;&nbsp;&nbsp;____ / ____ / ________
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center; font-size: 8px; padding: 3px;">
                            É VEDADA A AUTENTICAÇÃO DESTE DOCUMENTO
                        </td>
                    </tr>
                </table>
            </div>
EOT;
        return $html;
    }

    /**
     * verificaDadosPedido
     *
     * @param String $T005_Id
     * @return String/Bool
     */ 
    public function verificaDadosPedido($T005_Id)
    {
        global $g;
        $retorno = '';
        $origensValidas = array(0,1,2,3,4,5,6,7,8);
        $validaOC = '';

        $sql = mysql_query("SELECT T006_Codigo_Produto, 
                                   IFNULL(T006_D006_Id,0) AS T006_D006_Id, 
                                   IFNULL(T006_D005_Id,0) AS T006_D005_Id, 
                                   T006_Flag_Origem_Produto, 
                                   T006_Numero_Pedido_Compra, 
                                   T006_Item_Pedido_Compra,
                                   T006_Codigo_Substituto,
                                   D006_Flag_Devolucao,
                                   D006_Flag_Venda_Compra_Outros 
                              FROM T006
                         LEFT JOIN D006 ON D006_Id=T006_D006_Id 
                             WHERE T006_T005_Id = '{$T005_Id}'");
        while($row = mysql_fetch_array($sql)){
            $temp = '';
            if($row['T006_D006_Id'] <= 0){
                $temp .= "<b>".$row['T006_Codigo_Produto'] . "</b> está com a CFOP em branco";
            }
            if($row['T006_D005_Id'] <= 0){
                $temp .= ($temp == '') ? "<b>".$row['T006_Codigo_Produto'] . "</b> está com a NCM em branco" : ", com a NCM em branco";
            }
            if(!is_numeric($row['T006_Flag_Origem_Produto']) || $row['T006_Flag_Origem_Produto'] == '' || !in_array($row['T006_Flag_Origem_Produto'], $origensValidas)){
                $temp .= ($temp == '') ? "<b>".$row['T006_Codigo_Produto'] . "</b> a origem da mercadoria não é valida " : ", e a origem da mercadoria não é valida ";
                $temp .= "(origens válidas ".implode(", ", $origensValidas).")";
            }

            // Criada validação que não permite faturar pedido caso ca configuração em Configuração -> Opções -> Vendas -> Pedidos -> Obrigatório Num OC e Item OC nos produtos: esteja como Sim
            if($g['C031']['obrigatorioNumOCItemOC'] == 'S' && $row['D006_Flag_Devolucao'] != 'S' && $row['D006_Flag_Venda_Compra_Outros'] == 'V'){
                if(($row['T006_Numero_Pedido_Compra'] <= 0 || $row['T006_Item_Pedido_Compra'] <= 0) && (empty($row['T006_Numero_Pedido_Compra']) || empty($row['T006_Item_Pedido_Compra']))){
                    $validaOC = "Existe itens com <b> Num.OC/Item OC em branco </b>, isso só é permitido para CFOP que não seja venda.";
                }
            }

            // Verifica se o código substituto está preenchido para o produto no pedido
            // A configuração fica em Configuração -> Opções -> Vendas -> Geral -> Obrigatório Código Cliente ao faturar pedido
            if ($g['C031']['obrigatorioCodSubstitutoFaturamento'] == 'S') {
                if ($row['T006_Codigo_Substituto'] == ''){
                    $validaCodSubst = "Existem itens com o <b> Código Cliente em branco</b>.";
                }
            }
            
            $temp .= ($temp != '') ? '<br/>' : '';
            if($temp != ''){
                $retorno .= $temp;
            }

        }
        
        $retorno .= $validaOC;
        $retorno .= (($validaOC != '') ? '<br>' : '') . $validaCodSubst; 

        if($retorno != ''){
            return "Verifique os erros abaixo: \n<br/><br/>" . $retorno;
        }
        return true;
    }

    /**
     * processarNumeroItemPedido
     *
     * @param String $T006_Id
     * @return Bool
     */
    public function processarNumeroItemPedido($T006_Id, $novoNumeroItem)
    {
        global $g;
        $ultimoItem = 0;
        $contadorItem = 1;
        $sql = mysql_query("SELECT T006_Item, T006_Id FROM T006 WHERE T006_T005_Id = (SELECT T006_T005_Id FROM T006 WHERE T006_Id = '{$T006_Id}') AND T006_Id != '{$T006_Id}' ORDER BY T006_Item");
        while($row = mysql_fetch_array($sql)){            
            if($contadorItem == $novoNumeroItem){ $contadorItem++; }
            mysqli_query("UPDATE T006 SET T006_Item = '{$contadorItem}' WHERE T006_Id = '{$row['T006_Id']}'");
            $contadorItem++;
        }
        return true;
    }

    /**
     * processarVencidos
     *
     * @return Bool
     */
    public function VEN002ProcessarVencidos($mT005)
    {
        global $g;

        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();

        if($g['C031']['diasVencPedido'] <= 0 || $g['C031']['diasVencPedido'] == ''){
            return 'Não foi definido a quantidade de dias para vencer os pedidos.<br>Essa configuração é realizada em: <b>Configuração->Opções->Vendas->Pedido->Prazo pedido pendente</b>';
        }

        mysql_query("UPDATE T005 SET T005_Flag_Status='26', T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1) WHERE T005_Id='{$mT005['T005_Id']}'");
        $erro = mysql_error();
        if (!empty($erro)) {
            return "Erro $erro";
        }else{
            $T006 = mysql_query("SELECT T006_D009_Id,T006_Id FROM T006 WHERE T006_T005_Id='{$mT005['T005_Id']}'");
            while($mT006 = mysql_fetch_array($T006)){
                $CAD002->D001_reprocessa_historico(0, $mT006['T006_D009_Id'], 5);
                mysqli_query("UPDATE T006 SET T006_Quantidade_Estoque='0' WHERE T006_Id='{$mT005['T006_Id']}'");
            }
            //Insere registros no histórico do pedido (T178)
            mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) values ('{$mT005['T005_Id']}','{$g['usuarioAtual']}',CURRENT_TIMESTAMP(),'Vencido','Pedido ultrapassou prazo de validade')");
            $erro = mysql_error();
            if (!empty($erro)) {
                return "Erro $erro";
            }
        }

        return true;
    }

    /**
     * Gera o arquivo PDF dos documentos de exportação sem passar pelo
     * montarEmailPdf, que pode ser sobrescrito por personalizações de clientes.
     */
    public function montarDocumentoExportacaoPdf($T005_Id, $moeda = '1', $layout = 'invoice')
    {
        global $g, $confUsuario;

        $T005_Id = (int) $T005_Id;
        $moeda = empty($moeda) ? 1 : $moeda;
        $layout = $layout === 'packing' ? 'packing' : 'invoice';
        $fonteMinima = 13 + $g['C031']['vendasZoomFonteImprimirPDF'];
        $margensImpressao = $g['C031']['margemImprimirPDF'];
        $margensImpressao = ($margensImpressao <= 0 || $margensImpressao === '') ? 1 : $margensImpressao;

        $dir = $g['pathDados'] . 'tmp/';
        is_dir($dir) OR mkdir($dir, 0777, true);
        $filename = "pedido-{$T005_Id}-{$layout}.pdf";
        $file = $dir . $filename;
        $fileFull = $g['pathRaiz'] . '/' . $file;
        if (file_exists($fileFull)) {
            unlink($fileFull);
        }

        $retorno['Anexos'] = array($file);
        $retorno['AnexosWeb'] = array('/hardness3/' . $file . '?v=' . uniqid('', true));

        $r_link = '/ven/ven002/form_func-ajax/ven002ImprimirDocumentoExportacao/?ajax=true&T005_Id=' . $T005_Id . '&moeda=' . $moeda . '&layout=' . $layout;
        $r_link = str_replace(' ', '+', $r_link);
        shell_exec("xvfb-run --server-num " . mt_rand(0, 98) . " -s \"-screen 0 640x480x16\" wkhtmltopdf --minimum-font-size {$fonteMinima} --quiet --margin-left {$margensImpressao} --margin-right {$margensImpressao} --margin-top {$margensImpressao} --margin-bottom {$margensImpressao} \"{$g['urlRaiz']}{$r_link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"");

        return $retorno;
    }

    /**
     * Gera os documentos de exportação do pedido. Os campos com prefixo
     * Invoice são opcionais para manter compatibilidade com bases que ainda
     * não receberam a migração da Alumifix.
     */
    public function montarDocumentoExportacao($T005_Id, $moeda, $layout)
    {
        global $g, $confUsuario;

        $T005_Id = mysql_real_escape_string($T005_Id);
        $moeda   = (int) $moeda;
        $layout  = $layout === 'packing' ? 'packing' : 'invoice';

        $C004 = mysql_query("SELECT C004.*, D018.D018_UF AS emissorUF, D020.D020_Nome_Cidade AS emissorCidade,
                                     D030Emissor.D030_Nome_Pais AS emissorPais
                               FROM C004
                          LEFT JOIN D018 ON D018.D018_Id=C004.C004_D018_Id
                          LEFT JOIN D020 ON D020.D020_Id=C004.C004_D020_Id
                          LEFT JOIN D024 AS D024Emissor ON D024Emissor.D024_Id=C004.C004_D024_Id
                          LEFT JOIN D030 AS D030Emissor ON D030Emissor.D030_Id=D024Emissor.D024_D030_Id
                              WHERE C004.C004_Id='{$g['empresaAtual']}'");
        $mC004 = mysql_fetch_array($C004);

        $T005 = mysql_query("SELECT T005.*, T005A.*,
                                     D024.D024_Nome_Empresa, D024.D024_Endereco, D024.D024_Numero,
                                     D024.D024_Bairro, D024.D024_Complemento, D024.D024_Cep,
                                     D024.D024_Cnpj, D024.D024_Cpf, D024.D024_Inscricao_Estadual,
                                     D024.D024_DDD_Telefone_1, D024.D024_Telefone_1,
                                     D020.D020_Nome_Cidade AS clienteCidade, D018.D018_UF AS clienteUF,
                                     D030.D030_Nome_Pais AS clientePais,
                                     T005_Prazos(T005.T005_Id) AS pagamentoPadrao
                                FROM T005
                           LEFT JOIN T005A ON T005A.T005A_T005_Id=T005.T005_Id
                           LEFT JOIN D024 ON D024.D024_Id=T005.T005_D024_Id
                           LEFT JOIN D020 ON D020.D020_Id=D024.D024_D020_Id
                           LEFT JOIN D018 ON D018.D018_Id=D024.D024_D018_Id
                           LEFT JOIN D030 ON D030.D030_Id=D024.D024_D030_Id
                               WHERE T005.T005_Id='{$T005_Id}'");
        $mT005 = mysql_fetch_array($T005);

        if (empty($mT005['T005_Id'])) {
            return '<p>Pedido não encontrado.</p>';
        }

        $T006 = mysql_query("SELECT T006.T006_Item, T006.T006_Descricao_Produto, T006.T006_Quantidade,
                                     T006.T006_Valor_Preco_Sem_Desconto_Unitario, T006.T006_Valor_Total_Preco,
                                     D001.D001_Peso_Unitario_Kg, D001.D001_Quantidade_Tributavel, D037.D037_Unidade,
                                     D005.D005_Classificacao_Fiscal
                                FROM T006
                           LEFT JOIN D009 ON D009.D009_Id=T006.T006_D009_Id
                           LEFT JOIN D049 ON D049.D049_Id=D009.D009_D049_Id
                           LEFT JOIN D001 ON D001.D001_Id=D049.D049_D001_Id
                           LEFT JOIN D037 ON D037.D037_Id=T006.T006_D037_Id
                           LEFT JOIN D005 ON D005.D005_Id=T006.T006_D005_Id
                               WHERE T006.T006_T005_Id='{$T005_Id}'
                                 AND T006.T006_Flag_Cancelado!='S'
                                 AND T006.T006_Quantidade > 0
                            ORDER BY T006.T006_Item");

        $itens = array();
        $pesoLiquidoTotal = 0;
        while ($item = mysql_fetch_array($T006)) {
            $item['pesoLiquido'] = (float) $item['D001_Quantidade_Tributavel'] * (float) $item['T006_Quantidade'];
            // T006_Item pode conter lacunas quando algum item é excluído. A
            // numeração impressa precisa representar a sequência exibida.
            $item['numeroImpressao'] = count($itens) + 1;
            $pesoLiquidoTotal += $item['pesoLiquido'];
            $itens[] = $item;
        }

        $cotacao = (float) $mT005['T005_Cotacao_Moeda'];
        if ($cotacao <= 0) {
            $cotacao = 1;
        }
        $pesoBrutoPedido = (float) $mT005['T005_Peso_Bruto'];
        if ($pesoBrutoPedido <= 0) {
            $pesoBrutoPedido = (float) $mT005['T005_Peso_Bruto_Total'];
        }

        $logo = '';
        if (!empty($mC004['C004_Logo_Marca'])) {
            $pathinfo = pathinfo($mC004['C004_Logo_Marca']);
            $src = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
            $logo = '<img class="logo-exportacao" src="' . $this->documentoExportacaoEscapar($src) . '">';
        }

        $nomeEmissor = $this->documentoExportacaoEscapar($mC004['C004_Nome_Empresa']);
        $enderecoEmissor = $this->documentoExportacaoEscapar(trim($mC004['C004_Logradouro'] . ', ' . $mC004['C004_Numero'] . ' - ' . $mC004['C004_Bairro']));
        $cidadeEmissor = $this->documentoExportacaoEscapar(trim($mC004['emissorCidade'] . ' - ' . $mC004['emissorUF']));
        $paisEmissor = $this->documentoExportacaoEscapar(!empty($mC004['emissorPais']) ? $mC004['emissorPais'] : 'Brazil');
        $cnpjEmissor = $this->documentoExportacaoEscapar($mC004['C004_Cnpj']);
        $telefoneEmissor = $this->documentoExportacaoEscapar(trim($mC004['C004_DDD_Telefone'] . ' ' . $mC004['C004_Telefone']));
        $nomeCliente = $this->documentoExportacaoEscapar($mT005['D024_Nome_Empresa']);
        $enderecoCliente = $this->documentoExportacaoEscapar(trim($mT005['D024_Endereco'] . ', ' . $mT005['D024_Numero'] . ' - ' . $mT005['D024_Bairro']));
        $cidadeCliente = $this->documentoExportacaoEscapar(trim($mT005['clienteCidade'] . ' - ' . $mT005['clienteUF']));
        $paisCliente = $this->documentoExportacaoEscapar(!empty($mT005['clientePais']) ? $mT005['clientePais'] : 'Brazil');
        $fromAutomatico = $this->documentoExportacaoEscapar(trim($mC004['emissorCidade'] . '/' . (!empty($mC004['emissorPais']) ? $mC004['emissorPais'] : 'Brazil')));
        $toAutomatico = $this->documentoExportacaoEscapar(trim($mT005['clienteCidade'] . '/' . (!empty($mT005['clientePais']) ? $mT005['clientePais'] : 'Brazil')));
        $dataEmissao = $this->documentoExportacaoEscapar(gCorrigeData($mT005['T005_Data_Emissao']));
        $numeroPedido = str_pad($mT005['T005_Id'], 6, '0', STR_PAD_LEFT);

        $css = '<style>
            * { box-sizing:border-box; } body { font-family:Arial,sans-serif; color:#111; font-size:10px; margin:8px; }
            .exportacao-cabecalho { display:table; width:100%; margin-bottom:8px; } .exportacao-titulo { display:table-cell; width:70%; font-size:22px; font-weight:bold; vertical-align:middle; }
            .logo-exportacao { display:block; max-width:230px; max-height:70px; margin-left:auto; } .exportacao-logo { display:table-cell; vertical-align:middle; }
            .exportacao-blocos { width:100%; border-collapse:collapse; margin-bottom:8px; } .exportacao-blocos td { width:50%; border:1px solid #222; vertical-align:top; padding:4px; }
            .exportacao-blocos p { margin:1px 0; } .exportacao-rotulo { font-weight:bold; } .exportacao-itens { width:100%; border-collapse:collapse; margin-top:6px; }
            .exportacao-itens th, .exportacao-itens td { border:1px solid #222; padding:4px; } .exportacao-invoice-itens th, .exportacao-invoice-itens td { padding:2px 3px; } .exportacao-itens thead { display:table-header-group; page-break-after:avoid; break-after:avoid; } .exportacao-itens th { text-align:center; font-weight:bold; } .numero { text-align:right; white-space:nowrap; }
            .exportacao-rodape { width:100%; border-collapse:collapse; margin-top:8px; } .exportacao-rodape td { border:1px solid #222; padding:4px; vertical-align:top; width:25%; }
            .exportacao-rodape .assinatura-documento { width:50%; } .exportacao-rodape p { margin:2px 0; }
            .exportacao-fechamento, .exportacao-fechamento table, .exportacao-fechamento tr, .exportacao-fechamento td, .exportacao-rodape, .exportacao-rodape tr, .exportacao-rodape td { page-break-inside:avoid; break-inside:avoid; }
            .assinatura { margin-top:55px; width:260px; border-top:1px solid #222; padding-top:4px; text-align:center; font-weight:bold; }
        </style>';

        if ($layout === 'packing') {
            $html = $css . '<div class="exportacao-cabecalho"><div class="exportacao-titulo">PACKING LIST</div><div class="exportacao-logo">' . $logo . '</div></div>';
            $html .= '<table class="exportacao-blocos"><tr><td><span class="exportacao-rotulo">Exporter:</span><p><b>' . $nomeEmissor . '</b></p><p>' . $enderecoEmissor . '</p><p>' . $cidadeEmissor . ' - ' . $paisEmissor . '</p><p><b>CNPJ:</b> ' . $cnpjEmissor . ' &nbsp; <b>Telephone:</b> ' . $telefoneEmissor . '</p></td>';
            $html .= '<td><span class="exportacao-rotulo">Date:</span><p>' . $dataEmissao . '</p><span class="exportacao-rotulo">Payment Terms:</span><p>' . $this->documentoExportacaoEscapar($this->documentoExportacaoCampo($mT005, 'T005A_Invoice_Payment_Terms', $mT005['pagamentoPadrao'])) . '</p><span class="exportacao-rotulo">Authorized Signature:</span><p>&nbsp;</p></td></tr>';
            $html .= '<tr><td><span class="exportacao-rotulo">Importer:</span><p><b>' . $nomeCliente . '</b></p><p>' . $enderecoCliente . '</p><p>' . $cidadeCliente . ' - ' . $paisCliente . '</p></td>';
            $html .= '<td><span class="exportacao-rotulo">Notify:</span><p><b>' . $nomeCliente . '</b></p><p>' . $enderecoCliente . '</p><p>' . $cidadeCliente . ' - ' . $paisCliente . '</p></td></tr>';
            $html .= '<tr><td><span class="exportacao-rotulo">From:</span><p>' . $fromAutomatico . '</p><span class="exportacao-rotulo">Invoice Number:</span><p>' . $this->documentoExportacaoEscapar($this->documentoExportacaoCampo($mT005, 'T005A_Invoice_Number', $numeroPedido)) . '</p></td>';
            $html .= '<td><span class="exportacao-rotulo">To:</span><p>' . $toAutomatico . '</p><span class="exportacao-rotulo">Incoterms:</span><p>' . $this->documentoExportacaoEscapar($this->documentoExportacaoCampo($mT005, 'T005A_Invoice_Incoterms', '')) . '</p></td></tr></table>';
            $html .= '<table class="exportacao-itens"><tr><th>ITEM</th><th>DESCRIPTION</th><th>UNIT</th><th>QUANTITY</th><th>WEIGHT NET (Kgs)</th><th>WEIGHT GROSS (Kgs)</th></tr>';
            foreach ($itens as $item) {
                $pesoBrutoItem = $pesoLiquidoTotal > 0 ? $pesoBrutoPedido * ($item['pesoLiquido'] / $pesoLiquidoTotal) : null;
                $html .= '<tr><td>' . $this->documentoExportacaoEscapar($item['numeroImpressao']) . '</td><td><b>' . $this->documentoExportacaoEscapar($item['T006_Descricao_Produto']) . '</b></td><td>' . $this->documentoExportacaoEscapar($item['D037_Unidade']) . '</td><td class="numero">' . $this->documentoExportacaoNumero($item['T006_Quantidade'], 2) . '</td><td class="numero">' . $this->documentoExportacaoNumero($item['pesoLiquido'], 3) . '</td><td class="numero">' . ($pesoBrutoItem === null ? '-' : $this->documentoExportacaoNumero($pesoBrutoItem, 3)) . '</td></tr>';
            }
            $html .= '<tr><td colspan="4"><b>TOTAL</b></td><td class="numero"><b>' . $this->documentoExportacaoNumero($pesoLiquidoTotal, 3) . '</b></td><td class="numero"><b>' . ($pesoLiquidoTotal > 0 ? $this->documentoExportacaoNumero($pesoBrutoPedido, 3) : '-') . '</b></td></tr></table>';
            return $html;
        }

        $html = $css . '<div class="exportacao-cabecalho"><div class="exportacao-titulo">COMMERCIAL INVOICE</div><div class="exportacao-logo">' . $logo . '</div></div>';
        $html .= '<table class="exportacao-blocos"><tr><td><span class="exportacao-rotulo">Exporter:</span><p><b>' . $nomeEmissor . '</b></p><p>' . $enderecoEmissor . '</p><p>' . $cidadeEmissor . ' - ' . $paisEmissor . '</p><p><b>CNPJ:</b> ' . $cnpjEmissor . ' &nbsp; <b>Telephone:</b> ' . $telefoneEmissor . '</p></td>';
        $html .= '<td><span class="exportacao-rotulo">Date:</span><p>' . $dataEmissao . '</p><span class="exportacao-rotulo">Payment Terms:</span><p>' . $this->documentoExportacaoEscapar($this->documentoExportacaoCampo($mT005, 'T005A_Invoice_Payment_Terms', $mT005['pagamentoPadrao'])) . '</p><span class="exportacao-rotulo">Our Bank:</span><p>' . $this->documentoExportacaoTextoMultilinha($this->documentoExportacaoCampo($mC004, 'C004_Invoice_Our_Bank', '')) . '</p></td></tr>';
        $html .= '<tr><td><span class="exportacao-rotulo">Importer:</span><p><b>' . $nomeCliente . '</b></p><p>' . $enderecoCliente . '</p><p>' . $cidadeCliente . ' - ' . $paisCliente . '</p></td>';
        $html .= '<td><span class="exportacao-rotulo">Notify:</span><p><b>' . $nomeCliente . '</b></p><p>' . $enderecoCliente . '</p><p>' . $cidadeCliente . ' - ' . $paisCliente . '</p></td></tr>';
        $html .= '<tr><td><span class="exportacao-rotulo">From:</span><p>' . $fromAutomatico . '</p><span class="exportacao-rotulo">Invoice Number:</span><p>' . $this->documentoExportacaoEscapar($this->documentoExportacaoCampo($mT005, 'T005A_Invoice_Number', $numeroPedido)) . '</p></td>';
        $html .= '<td><span class="exportacao-rotulo">To:</span><p>' . $toAutomatico . '</p><span class="exportacao-rotulo">Incoterms:</span><p>' . $this->documentoExportacaoEscapar($this->documentoExportacaoCampo($mT005, 'T005A_Invoice_Incoterms', '')) . '</p></td></tr></table>';
        // Mantém o fechamento da invoice (últimos itens, total e rodapé) unido
        // quando houver quebra de página.
        $quantidadeItensFechamento = min(3, count($itens));
        $itensFechamento = $quantidadeItensFechamento > 0 ? array_splice($itens, -$quantidadeItensFechamento) : array();
        $totalDolar = 0;
        foreach (array_merge($itens, $itensFechamento) as $item) {
            $precoDolar = (float) $item['T006_Valor_Preco_Sem_Desconto_Unitario'] / $cotacao;
            $valorDolar = (float) $item['T006_Valor_Total_Preco'] / $cotacao;
            $totalDolar += $valorDolar;
        }

        $colunasItensInvoice = '<colgroup><col style="width:7%"><col style="width:25%"><col style="width:12%"><col style="width:7%"><col style="width:14%"><col style="width:15%"><col style="width:20%"></colgroup>';
        $cabecalhoItensInvoice = '<thead><tr><th>ITEM</th><th>DESCRIPTION</th><th>NCM</th><th>UNIT</th><th>QUANTITY</th><th>USD PRICE</th><th>USD AMOUNT</th></tr></thead>';

        if (!empty($itens)) {
            $html .= '<table class="exportacao-itens exportacao-invoice-itens">' . $colunasItensInvoice . $cabecalhoItensInvoice . '<tbody>';
            foreach ($itens as $item) {
                $precoDolar = (float) $item['T006_Valor_Preco_Sem_Desconto_Unitario'] / $cotacao;
                $valorDolar = (float) $item['T006_Valor_Total_Preco'] / $cotacao;
                $html .= '<tr><td>' . $this->documentoExportacaoEscapar($item['numeroImpressao']) . '</td><td><b>' . $this->documentoExportacaoEscapar($item['T006_Descricao_Produto']) . '</b></td><td>' . $this->documentoExportacaoEscapar($item['D005_Classificacao_Fiscal']) . '</td><td>' . $this->documentoExportacaoEscapar($item['D037_Unidade']) . '</td><td class="numero">' . $this->documentoExportacaoNumero($item['T006_Quantidade'], 2) . '</td><td class="numero">' . $this->documentoExportacaoNumero($precoDolar, 5) . '</td><td class="numero">' . $this->documentoExportacaoNumero($valorDolar, 2) . '</td></tr>';
            }
            $html .= '</tbody></table>';
        }

        $html .= '<div class="exportacao-fechamento"><table class="exportacao-itens exportacao-invoice-itens">' . $colunasItensInvoice . (empty($itens) ? $cabecalhoItensInvoice : '') . '<tbody>';
        foreach ($itensFechamento as $item) {
            $precoDolar = (float) $item['T006_Valor_Preco_Sem_Desconto_Unitario'] / $cotacao;
            $valorDolar = (float) $item['T006_Valor_Total_Preco'] / $cotacao;
            $html .= '<tr><td>' . $this->documentoExportacaoEscapar($item['numeroImpressao']) . '</td><td><b>' . $this->documentoExportacaoEscapar($item['T006_Descricao_Produto']) . '</b></td><td>' . $this->documentoExportacaoEscapar($item['D005_Classificacao_Fiscal']) . '</td><td>' . $this->documentoExportacaoEscapar($item['D037_Unidade']) . '</td><td class="numero">' . $this->documentoExportacaoNumero($item['T006_Quantidade'], 2) . '</td><td class="numero">' . $this->documentoExportacaoNumero($precoDolar, 5) . '</td><td class="numero">' . $this->documentoExportacaoNumero($valorDolar, 2) . '</td></tr>';
        }
        $html .= '<tr><td colspan="6"><b>TOTAL USD</b></td><td class="numero"><b>' . $this->documentoExportacaoNumero($totalDolar, 2) . '</b></td></tr>';
        $html .= '</tbody></table>';
        $html .= '<table class="exportacao-rodape"><tr><td><b>Net Weight:</b><p>' . $this->documentoExportacaoNumero($pesoLiquidoTotal, 3) . ' Kgs</p></td><td><b>Packages:</b><p>' . $this->documentoExportacaoNumero($mT005['T005_Quantidade_Volumes'], 0) . ' CTN&apos;s</p></td><td class="assinatura-documento" rowspan="2"><b>Authorized Signature:</b><p>&nbsp;</p></td></tr>';
        $html .= '<tr><td><b>Gross Weight:</b><p>' . $this->documentoExportacaoNumero($pesoBrutoPedido, 3) . ' Kgs</p></td><td><b>Vol in CM3:</b><p>' . $this->documentoExportacaoNumero($mT005['T005_Total_Cubagem'], 3) . ' m&sup3;</p></td></tr></table></div>';
        return $html;
    }

    private function documentoExportacaoCampo($dados, $campo, $padrao)
    {
        return isset($dados[$campo]) && trim($dados[$campo]) !== '' ? $dados[$campo] : $padrao;
    }

    private function documentoExportacaoEscapar($valor)
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    }

    private function documentoExportacaoTextoMultilinha($valor)
    {
        return nl2br($this->documentoExportacaoEscapar($valor));
    }

    private function documentoExportacaoNumero($valor, $casas)
    {
        return number_format((float) $valor, $casas, ',', '.');
    }

    public function incluirProdutosPesquisaGridPedido($T005_Id)
    {
        global $g;
        require_once('bibliotecas/classes/VEN001.php');
        $VEN001 = new VEN001();
        require_once('bibliotecas/classes/GFormWrap.php');

        $sqlT005 = mysql_query("SELECT * 
                                  FROM T005 
                             LEFT JOIN D024 ON D024_Id=T005_D024_Id
                                 WHERE T005_Id = '{$T005_Id}'");
        $rowT005 = mysql_fetch_array($sqlT005);

        if(!empty($rowT005['D024_Flag_Lista_Preco'])){
            $campoListaPreco = "D009_Preco_{$rowT005['D024_Flag_Lista_Preco']}";
        } else {
            $campoListaPreco = "D009_Preco_1";
        }

        $sqlT266 = mysql_query("SELECT * 
                                  FROM T266 
                                 WHERE T266_D024_Id = '{$rowT005['T005_D024_Id']}' 
                                   AND T266_C007_Id = '{$g['usuarioAtual']}' 
                                   AND T266_Quantidade > 0");

        while($rowT266 = mysql_fetch_array($sqlT266)){

            if($rowT266['T266_Quantidade'] <= 0) { 
                continue;
            }

            $sqlD001 = mysql_query("SELECT * 
                                      FROM D001 
                                 LEFT JOIN D049 ON D001_Id=D049_D001_Id 
                                 LEFT JOIN D009 ON D049_Id=D009_D049_Id 
                                     WHERE D009_Id = '{$rowT266['T266_D009_Id']}'");

            $rowD001 = mysql_fetch_array($sqlD001);
            $D006_Id = $VEN001->retornaCFOP($rowT005['T005_D024_Id'], $rowT266['T266_D009_Id'], $rowT005['T005_C004_Id'], $rowT005['T005_Flag_Destino_Produto'], false, false, false, $rowT005['T005_C004_Id_Estoque']);

            $precoUnitario = 0;
            if($rowT266['T266_Valor_Preco_Sem_Desconto_Unitario'] > 0) {
                $precoUnitario = gCorrigeNumero($rowT266['T266_Valor_Preco_Sem_Desconto_Unitario']);
            } else if($rowD001[$campoListaPreco] > 0) {
                $precoUnitario = gCorrigeNumero($rowD001[$campoListaPreco]);
            }

            $D160 = mysql_query("SELECT D160_Valor_Preco 
                                   FROM D160 
                                  WHERE D160_D024_Id = '{$rowT005['T005_D024_Id']}'  
                                    AND D160_D009_Id = '{$rowT266['T266_D009_Id']}'");
                                    
            if(mysql_num_rows($D160) == 1){
                $mD160 = mysql_fetch_array($D160);
                if($mD160['D160_Valor_Preco'] > 0){
                    $precoUnitario = gCorrigeNumero($mD160['D160_Valor_Preco']);
                }
            }

            $form = new GFormWrap(array(
                'T006_D009_Id'                           => $rowT266['T266_D009_Id'],
                'T006_Quantidade'                        => gCorrigeNumero($rowT266['T266_Quantidade']),
                'T006A_Quantidade_Conversao'             => gCorrigeNumero($rowT266['T266_Quantidade_Conversao']),
                'T006_Valor_Preco_Sem_Desconto_Unitario' => $precoUnitario,
                'T006_D037_Id'                           => $rowD001['D001_D037_Id'],
                'T006_Descricao_Produto'                 => $rowD001['D001_Descricao_Produto'],
                'T006_Codigo_Produto'                    => $rowD001['D001_Codigo_Produto'],
                'T006_D006_Id'                           => $D006_Id,
                'T006_T005_Id'                           => $T005_Id,
                'T006_D005_Id'                           => $rowD001['D001_D005_Id'],
                'T006_Flag_Destino_Produto'              => $rowT005['T005_Flag_Destino_Produto'],
                'T006_D022_Id'                           => $rowT005['T005_D022_Id'],
                'T006_T208_Id'                           => $rowT005['T005_T208_Id']
            ));
            $this->incluirProduto($form);
        }
        mysql_query("DELETE FROM T266 WHERE (T266_D024_Id = '{$rowT005['T005_D024_Id']}' AND T266_C007_Id = '{$g['usuarioAtual']}') OR T266_D009_Id IS NULL");
        return true;
    }

    /**
     * incluirProdutosPesquisaGridPedEtapas
     *
     * @param Array $dados - Array com os ids da T198 e T003
     * @param Array|Bool $cacheAnterior - Array com dados da execução anterior
     * @return Bool
     */
    public function incluirProdutosPesquisaGridPedEtapas($dados, $cacheAnterior = false) {
        global $g;
        require_once('bibliotecas/classes/GFormWrap.php');
        require_once('bibliotecas/classes/VEN001.php');
        $VEN001 = new VEN001();

        // Pegando os dados para a inclusão do pedido
        $sqlT005 = "SELECT T005_D024_Id, T005_C004_Id, T005_Flag_Destino_Produto, T005_C004_Id_Estoque, T005_D022_Id, T005_T208_Id, D024_Flag_Lista_Preco as flagPreco
                    FROM T005
                        LEFT JOIN D024 ON D024_Id = T005_D024_Id
                    WHERE T005_Id = '{$dados['T005_Id']}'";
        $T005 = mysql_fetch_assoc(mysql_query($sqlT005));

        $sqlT266 = "SELECT T266_Id, T266_Quantidade, T266_D009_Id, T266_Valor_Preco_Sem_Desconto_Unitario, T266_Quantidade_Conversao
                    FROM T266
                    WHERE T266_Id = '{$dados['T266_Id']}'";
        $T266 = mysql_fetch_assoc(mysql_query($sqlT266));

        $sqlD001 = "SELECT 
                        D009_Preco_1,
                        D009_Preco_2,
                        D009_Preco_3,
                        D009_Preco_4,
                        D009A_Preco_5,
                        D001_D037_Id,
                        D001_Descricao_Produto,
                        D001_Codigo_Produto,
                        D001_D005_Id
                    FROM D001 
                        LEFT JOIN D049 ON D001_Id = D049_D001_Id
                        LEFT JOIN D009 ON D049_Id = D009_D049_Id
                        LEFT JOIN D001A ON D001A_Id = D001A_D001_Id 
                        LEFT JOIN D009A ON D009A_Id = D009A_D009_Id
                    WHERE D009_Id = '{$T266['T266_D009_Id']}'";
        $D001 = mysql_fetch_assoc(mysql_query($sqlD001));

        // Validações e ajustes nos dados
        if($T266['T266_Quantidade'] <= 0) {
            mysql_query("DELETE FROM T266 WHERE T266_Id = '{$dados['T266_Id']}'");
            return true; 
        }
        
        if(!empty($T005['flagPreco'])) {
            $campoListaPreco = ($T005['flagPreco'] > 5) ? "D009A_Preco_{$T005['flagPreco']}" : "D009_Preco_{$T005['flagPreco']}";
        } else {
            $campoListaPreco = "D009_Preco_1";
        }

        $precoUnitario = 0;
        if($T266['T266_Valor_Preco_Sem_Desconto_Unitario'] > 0) {
            $precoUnitario = gCorrigeNumero($T266['T266_Valor_Preco_Sem_Desconto_Unitario']);

        } else if($D001[$campoListaPreco] > 0) {
            $precoUnitario = gCorrigeNumero($D001[$campoListaPreco]);
        }

        $sqlD160 = mysql_query("SELECT D160_Valor_Preco FROM D160 WHERE D160_D024_Id = '{$T005['T005_D024_Id']}' AND D160_D009_Id = '{$T266['T266_D009_Id']}'");
                                
        if(mysql_num_rows($sqlD160) == 1) {
            $D160 = mysql_fetch_assoc($sqlD160);
            if($D160['D160_Valor_Preco'] > 0) {
                $precoUnitario = gCorrigeNumero($D160['D160_Valor_Preco']);
            }
        }
            
        $idCFOP = $VEN001->retornaCFOP($T005['T005_D024_Id'], $T266['T266_D009_Id'], $T005['T005_C004_Id'], $T005['T005_Flag_Destino_Produto'], false, false, false, $T005['T005_C004_Id_Estoque']);

        // Monta o form para a inclusão do pedido
        $form = new GFormWrap(array(
            'T006_D009_Id'                            => $T266['T266_D009_Id'],
            'T006_Quantidade'                         => gCorrigeNumero($T266['T266_Quantidade']),
            'T006A_Quantidade_Conversao'              => gCorrigeNumero($T266['T266_Quantidade_Conversao']),
            'T006_Valor_Preco_Sem_Desconto_Unitario'  => $precoUnitario,
            'T006_D037_Id'                            => $D001['D001_D037_Id'],
            'T006_Descricao_Produto'                  => $D001['D001_Descricao_Produto'],
            'T006_Codigo_Produto'                     => $D001['D001_Codigo_Produto'],
            'T006_D006_Id'                            => $idCFOP,
            'T006_T005_Id'                            => $dados['T005_Id'],
            'T006_D005_Id'                            => $D001['D001_D005_Id'],
            'T006_Flag_Destino_Produto'               => $T005['T005_Flag_Destino_Produto'],
            'T006_D022_Id'                            => $T005['T005_D022_Id'],
            'T006_T208_Id'                            => $T005['T005_T208_Id']
        ));

        $this->incluirProduto($form);

        mysql_query("DELETE FROM T266 WHERE T266_Id = '{$dados['T266_Id']}'");

        return true;
    }

    /**
     * copiarEngenhariaProduto
     *
     * @param String $T006_Id
     * @return Bool
     */
    public function copiarEngenhariaProduto($T006_Id)
    {
        global $g;

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        $res = mysql_query("SELECT D001_Id, T006_Id,T006_T005_Id FROM T006 LEFT JOIN D009 ON D009_Id=T006_D009_Id LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id WHERE T006_Id = '{$T006_Id}'");
        while($row = mysql_fetch_array($res)){
            //Copia a tabela de composição do produto
            mysqli_query("INSERT INTO T261 (T261_D001_Id_Composicao, T261_Quantidade, T261_T006_Id) 
                              SELECT D074_D001_Id_Composicao, 
                                     D074_Quantidade, 
                                     '{$T006_Id}' 
                                FROM D074 
                               WHERE D074_D001_Id = '{$row['D001_Id']}'");
            $erro = mysql_error();
            if(!empty($erro)){

                // Inserindo log
                $data = <<<EOT
                Query: INSERT INTO T261 (T261_D001_Id_Composicao, T261_Quantidade, T261_T006_Id) 
                SELECT D074_D001_Id_Composicao, 
                       D074_Quantidade, 
                       '{$T006_Id}' 
                  FROM D074 
                 WHERE D074_D001_Id = '{$row['D001_Id']}'
                Erro: $erro
EOT;
                // Gravar arquivo no disco
                $Dir = $g['pathDados'] . 'tmp/';
                is_dir($Dir) or mkdir($Dir, 0777, true);
                $arquivoNome = "{$Dir}ProdutoProduzido-T005-{$row['T006_T005_Id']}-T006-{$T006_Id}.txt";
                @unlink($arquivoNome);
                $h = fopen($arquivoNome, 'w');
                fwrite($h, $data);
                fclose($h);

                mysql_query("ROLLBACK");
                return "Erro ao gravar os componentes, por favor entre em contato com o suporte técnico.";
            } 
            
            mysqli_query("INSERT INTO T261A (T261A_T261_Id, T261A_T006_Id) 
                             SELECT  T261_Id, 
                                     T261_T006_Id
                                FROM T261 
                               WHERE T261_T006_Id = '{$T006_Id}'");

            $sqlD075 = mysql_query("SELECT D075_Id FROM D075 WHERE D075_D001_Id = '{$row['D001_Id']}'");
            while($rowD075 = mysql_fetch_assoc($sqlD075)){
                //Copia a tabela de processos de produção do produto
                mysqli_query("INSERT INTO T262 (T262_D079_Id, T262_D078_Id, T262_Lead_Time, T262_Descricao, T262_Ordem, T262_Numero_Funcionarios, T262_T006_Id) SELECT D075_D079_Id, D075_D078_Id, D075_Lead_Time, D075_Descricao, D075_Ordem, D075_Numero_Funcionarios, '{$T006_Id}' FROM D075 WHERE D075_Id = '{$rowD075['D075_Id']}'");
                $erro = mysql_error();
                if(!empty($erro)){

                    // Inserindo log
                    $data = <<<EOT
                    Query: INSERT INTO T262 (T262_D079_Id, T262_D078_Id, T262_Lead_Time, T262_Descricao, T262_Ordem, T262_Numero_Funcionarios, T262_T006_Id) SELECT D075_D079_Id, D075_D078_Id, D075_Lead_Time, D075_Descricao, D075_Ordem, D075_Numero_Funcionarios, '{$T006_Id}' FROM D075 WHERE D075_Id = '{$rowD075['D075_Id']}'
                    Erro: $erro
EOT;
                    // Gravar arquivo no disco
                    $Dir = $g['pathDados'] . 'tmp/';
                    is_dir($Dir) or mkdir($Dir, 0777, true);
                    $arquivoNome = "{$Dir}ProdutoProduzido-T005-{$row['T006_T005_Id']}-T006-{$T006_Id}.txt";
                    @unlink($arquivoNome);
                    $h = fopen($arquivoNome, 'w');
                    fwrite($h, $data);
                    fclose($h);

                    mysql_query("ROLLBACK");
                    return "Erro ao gravar os processos, por favor entre em contato com o suporte técnico.";
                }
                $T262_Id = mysql_insert_id();                
                require_once('bibliotecas/classes/EST017.php');
                $EST017 = new EST017();
                $parametrosGatilho = array($T262_Id, $rowD075['D075_Id']);
                $erro = $EST017->gatilhoT262AposInsert($parametrosGatilho);

                if(!empty($erro)){
                    mysql_query("ROLLBACK");
                    return "Erro ao gravar engenharia do produto, por favor entre em contato com o suporte técnico. (EST017->gatilhoT262AposInsert). Erro: {$erro}";
                }

                //Copia a tabela de material de consumo dos processos de produção do produto
                mysqli_query("INSERT INTO T263 (T263_T262_Id, T263_D009_Id, T263_Quantidade) SELECT '{$T262_Id}', D076_D009_Id, D076_Quantidade FROM D076 WHERE D076_D075_Id = '{$rowD075['D075_Id']}'");
                $erro = mysql_error();
                if(!empty($erro)){

                    // Inserindo log
                    $data = <<<EOT
                    Query: INSERT INTO T263 (T263_T262_Id, T263_D009_Id, T263_Quantidade) SELECT '{$T262_Id}', D076_D009_Id, D076_Quantidade FROM D076 WHERE D076_D075_Id = '{$rowD075['D075_Id']}'
                    Erro: $erro
EOT;
                    // Gravar arquivo no disco
                    $Dir = $g['pathDados'] . 'tmp/';
                    is_dir($Dir) or mkdir($Dir, 0777, true);
                    $arquivoNome = "{$Dir}ProdutoProduzido-T005-{$row['T006_T005_Id']}-T006-{$T006_Id}.txt";
                    @unlink($arquivoNome);
                    $h = fopen($arquivoNome, 'w');
                    fwrite($h, $data);
                    fclose($h);


                    mysql_query("ROLLBACK");
                    return "Erro ao gravar os materiais de consumo, por favor entre em contato com o suporte técnico.";
                }
            }
        }
        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");
        return true;
    }

    //Metodo Personalizado por Fabio 09/05 - Criado exclusivamente para a Koisas cancelar pedidos em lote
    public function prepararCancelarPedidos($extra, $T005_Observacao_Pedido_Perdido, $T005_D047_Id){
        require_once('bibliotecas/classes/GFormWrap.php');
        $extra = base64_decode(urldecode($extra));
        $extra = gPegarParteWhere(gInsertExtraWhere($extra, "T005_Flag_Marcado = 'S' AND T005_Flag_Status != '8'"));
        $sqlT005 = mysql_query("SELECT T005_Id FROM T005 
                                left join D024 on D024_Id=T005_D024_Id
                                left join D013 on D013_Id=T005_D013_Id
                                left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
                                left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
                                left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
                                left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
                                left join C007 as Supervisor2 on Supervisor2.C007_Id=Vendedor.C007_C007_Id_2
                                left join C007 as Substituto on Substituto.C007_Id=Vendedor.C007_Id_Vendedor_Substituto
                                left join D006 on D006_Id=T005_D006_Id
                                left join D021 on D021_Id=T005_D021_Id
                                WHERE {$extra}");

        $string = '';
        while($T005 = mysql_fetch_array($sqlT005)){
            $form = array(
                'T005_Id' => $T005['T005_Id'],
                'T005_D047_Id' => $T005_D047_Id,
                'T005_Observacao_Pedido_Perdido' => $T005_Observacao_Pedido_Perdido
            );
            $form = new GFormWrap($form);
            $retorno = $this->ven002CancelarPedido($form);
            //$retorno = 'Erro ...';
            if($retorno !== true){
                $string .= $retorno . '<br />';
            }
        }

        if($string != ''){
            return $string;
        } else {
            return true;
        }
    }

    /**
     * faturarPedidosSelecionados
     *
     * @param String $extra
     * @return Bool
     */
    public function faturarPedidosSelecionados($extra)
    {
        global $g;
        $extra = gInsertExtraWhere(base64_decode($extra), "T005_Flag_Marcado = 'S'");
        $erros = array();

        $T005 = mysql_query("SELECT T005_Id FROM T005 {$extra}");

        while ($mT005 = mysql_fetch_array($T005)) {

            $retorno = $this->faturarPedidosSelecionadosPedido("{$mT005['T005_Id']}");

            if ($retorno !== true) {
                $erros = array_merge($erros, $retorno);
            }

        }

        if(!empty($erros)){
            return $erros;
        }

        return true;
    }

    /**
     * atualizarCfopAliquotaPedidoCrontab
     *
     * @param T224_Id
     * @return Bool
     */
    public function atualizarCfopAliquotaPedidoCrontab($T224_Id)
    {
        global $g;
        // Pedido que foram gerados pelo crotab pela ordem de tranferencia
        $T005 = mysql_query("SELECT T005_Id 
                               FROM T005 
                              WHERE T005_T224_Id = '{$T224_Id}'");

        while($mT005 = mysql_fetch_array($T005)){
            $this->ven002atualizarCFOP($mT005['T005_Id']);
            $this->atualizarAliquotasPedidos($mT005['T005_Id']);
            $this->atualizarPrecoTabela($mT005['T005_Id']);
        }  
        
        return true;
    }

    /**
     * gerarPedidoCompraInternoCrontab
     *
     * @param C004_Id
     * @return Bool
     */
    public function gerarPedidoCompraInternoCrontab($C004_Id)
    {
        global $g;

        $D009 = mysql_query("SELECT D009_Id,
                                    D001_D005_Id,
                                    D001_Codigo_Produto,
                                    D001_Descricao_Produto,
                                    D009_Quantidade_Comprar
                               FROM D009
                          LEFT JOIN D049 ON D049_Id = D009_D049_Id
                          LEFT JOIN D001 ON D001_Id = D049_D001_Id
                          LEFT JOIN T066 ON D009_Id = T066_D009_Id
                              WHERE D009_C004_Id = '{$C004_Id}'
                                AND D009_Quantidade_Comprar > 0
                                AND D009_C007_Id_Comprar = '{$g['usuarioAtual']}'
                                AND T066_Minimo > 0
                                AND T066_Maximo > 0");
        if(mysql_num_rows($D009) <= 0 ){
            return "Não tem itens para reposição de compra automática";
        }

        $C004 = mysql_query("SELECT C004_D024_Id
                               FROM C004
                              WHERE C004_Id = '{$C004_Id}'");

        $mC004 = mysql_fetch_array($C004);

        if($mC004['C004_D024_Id'] <= 0){
            return 'Não tem cliente definido no cadastro da empresa';
        }
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        // insere o cabeçalho
        mysql_query("INSERT INTO T005 (T005_C004_Id,
                                       T005_D024_Id,
                                       T005_C007_Id_Vendedor_Interno,
                                       T005_Flag_ACP,
                                       T005_Flag_Status,
                                       T005_Data_Emissao,
                                       T005_Hora_Emissao,
                                       T005_Flag_Destino_Produto,
                                       T005_Flag_Frete        
                                )VALUES(
                                      '{$C004_Id}',
                                      '{$mC004['C004_D024_Id']}',
                                      '{$g['usuarioAtual']}',
                                      '4',
                                      '0',
                                      CURDATE(),
                                      CURTIME(),
                                      '2',
                                      '9');");
        $error = mysql_error();
        if(!empty($error)){
            return 'erro '.$error;
        }

        $T005_Id = $g['mysqlLastId'];

        //T005A_Flag_Tipo = 2 (compra interna)
        $T005A = mysql_query("SELECT T005A_Id FROM T005 WHERE T005A_T005_Id = '{$T005_Id}'");

        if(mysql_num_rows($T005A) > 0){
            mysql_query("UPDATE T005A SET T005A_Flag_Tipo = '2' WHERE T005A_T005_Id = '{$T005_Id}'");
        } else {
            mysql_query("INSERT INTO T005A (T005A_T005_Id, T005A_Flag_Tipo) VALUES ('{$T005_Id}', '2')");
        }
                                                
        $error = mysql_error();
        if(!empty($error)){
            return 'erro '. $error;
        }

        //Insere registros no histórico do pedido (T178)
        mysql_query("INSERT INTO T178 (T178_T005_Id,
                                       T178_C007_Id,
                                       T178_Data,
                                       T178_Descricao
                             ) VALUES (
                                      '{$T005_Id}',
                                      '{$g['usuarioAtual']}',
                                       CURRENT_TIMESTAMP(),
                                       'Pedido gerado automaticamente para compras emergência interna')");
        $error = mysql_error();
        if(!empty($error)){
            return 'erro '.$error;
        }

        while($mD009 = mysql_fetch_array($D009)){

            require_once("bibliotecas/classes/VEN001.php");
            $VEN001            = new VEN001();
            $D006_Id           = $VEN001->retornaCFOP($mC004['C004_D024_Id'], $mD009['D009_Id'], $C004_Id, false , false, false, false, false);
            $demonstrativoCFOP = $VEN001->retornaCFOP($mC004['C004_D024_Id'], $mD009['D009_Id'], $C004_Id, false , false, true, false, false);
            $demonstrativoCFOP = mysql_real_escape_string($demonstrativoCFOP);

            require_once('bibliotecas/classes/FIS003.php');
            $FIS003           = new FIS003();
            $demonstrativoNCM = $FIS003->demonstrativoNCM($mD009['D001_D005_Id']);
            $demonstrativoNCM = mysql_real_escape_string($demonstrativoNCM);

            mysql_query("INSERT INTO T006 (T006_C004_Id,
                                           T006_T005_Id,
                                           T006_D009_Id,
                                           T006_D006_Id,
                                           T006_D005_Id,
                                           T006_Codigo_Produto,
                                           T006_Descricao_Produto,
                                           T006_Quantidade                                   
                                ) VALUES (
                                          '{$C004_Id}',
                                          '{$T005_Id}',
                                          '{$mD009['D009_Id']}',
                                          '{$D006_Id}',
                                          '{$mD009['D001_D005_Id']}',
                                          '{$mD009['D001_Codigo_Produto']}',
                                          '{$mD009['D001_Descricao_Produto']}',
                                          '{$mD009['D009_Quantidade_Comprar']}');");

            $error = mysql_error();
            if(!empty($error)){
                return 'erro '.$error;
            }

            $T006_Id = $g['mysqlLastId'];

            mysql_query("UPDATE T006 SET T006_D005_Id_Info = '{$demonstrativoNCM}',
                                         T006_D006_Id_Info = '{$demonstrativoCFOP}'
                                   WHERE T006_Id = '{$T006_Id}'");

            $error = mysql_error();
            if(!empty($error)){
                return 'erro '.$error;
            }
            
            mysql_query("UPDATE D009 SET D009_Quantidade_Comprar = '0',
                                         D009_C007_Id_Comprar    = '0'
                                   WHERE D009_Id = '{$mD009['D009_Id']}'");

            $error = mysql_error();
            if(!empty($error)){
                return 'erro '.$error;
            }
        }

        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");
        
        return array('T005_Id' => $T005_Id);
    }

    public function faturarPedidosSelecionadosPedido($mT005) {
        global $g;

        $T005_Id = $mT005['T005_Id'];

        require_once("bibliotecas/classes/VEN001.php");
        $VEN001 = new VEN001();

        $acao = 'faturar';

        $T005  = mysql_query("SELECT T005_Id,
                                     T005_Flag_Status_Liberacao, 
                                     T005_D024_Id,
                                     T005_Flag_Frete,
                                     T005_Valor_Frete,
                                     ROUND(SUM(T006_Valor_Total_Frete),2) AS FreteProdutos,
                                     D024_Nome_Fantasia,
                                     T005_Flag_Status
                                FROM T005 
                           LEFT JOIN T006 ON T006_T005_Id=T005_Id 
                           LEFT JOIN D024 ON D024_Id=T005_D024_Id
                               WHERE T005_Id='{$T005_Id}' 
                            GROUP BY T005_Id");

        $mT005 = mysql_fetch_array($T005);

        // $pedido = "{$mT005['T005_Id']} - {$mT005['D024_Nome_Fantasia']}";

        $erros = '';

        $retornoValidacao = $this->excecaoValidaPedido($T005_Id);
        if ($retornoValidacao !== true)
        {
            $erros .= $retornoValidacao . "\n";
        }

        if($mT005['T005_Flag_Status'] > 0 && $mT005['T005_Flag_Status'] != 14 && $mT005['T005_Flag_Status'] != 15 && $mT005['T005_Flag_Status'] != 9 && $rowT005['T005_Flag_Status'] != 26 && $rowT005['T005_Flag_Status'] != 22){
            $erros .= "Pedido já faturado \n";
        }

        $retornoRestricao = $this->verificaDadosPedido($T005_Id);
        if($retornoRestricao !== true){
            $erros .= $retornoRestricao . "\n";
        }

        if ($g['C031']['validarNFEFaturarPedido']== 'S') {

            require_once("bibliotecas/validadorNFe/validadorNFe.php");
            $validadorNFe = new validadorNFe();

            $retorno = $validadorNFe->validadorNFe(null, $T005_Id, null);

            if($retorno !== true){
                $erros .= $retorno . "\n";
            }
        }

        //Verifica o frete dos produtos caso obrigatório rateio do frete quando por conta do emitente
        if($mT005['T005_Valor_Frete'] != $mT005['FreteProdutos'] && $g['C031']['ratearFreteVendaNoCusto'] == 'S' && $mT005['T005_Flag_Frete'] == '0'){
            $erros .= 'A soma do frete dos produtos é diferente do valor do frete no cabeçalho do pedido. <p>Para corrigir clique no botão "Atualizar" em seguida a opção "Atualizar preço tabela."' . "\n";
        }

        $VEN001->verificaAlcadaNovo(false, $T005_Id, $acao);

        $T005  = mysql_query("SELECT T005_Flag_Status_Liberacao, T005_D024_Id FROM T005 WHERE T005_Id='{$T005_Id}'");
        $mT005 = mysql_fetch_array($T005);

        if ($mT005['T005_D024_Id'] == $g['C020']['C020_Cupom_Fiscal_Cliente']) {
            $mT005['T005_Flag_Status_Liberacao'] = 1;
        }

        if($mT005['T005_Flag_Status_Liberacao'] == 4){
            $erros .= "Enviado para liberação de alçada.\n";
            $retornoAnalise = $this->enviarParaAnalise($T005_Id, false, $acao);
            if($retornoAnalise !== true){
                $erros .= $retornoAnalise . "\n";
            }
        }

        //Fatura o pedido caso esteja de acordo com as regras de alçada e com os dados corretos
        if(empty($erros)){
            $this->ven002atualizarCFOP($T005_Id);
            $this->atualizarAliquotasPedidos($T005_Id);
            $retornoFaturar = $this->faturarPedido($T005_Id);
            if($retornoFaturar !== true){
                $erros .= $retornoFaturar . "\n";
            }
        }

        if(!empty($erros)){
            return 'Pedido: ' . $T005_Id . ' - ' . $erros . '<br />';
        }

        return true;

    }

    public function atualizarPrecoTabela($T005_Id){
      
        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();

        log("entrou no atualizar preço tabela");

        //Buscando as aliquotas digitadas na tela de montagem do ipv no orçamento
        $T251 = mysql_query("SELECT sum(T251_Aliquota-T251_Aliquota_Original) as AliquotasIPV from T251 WHERE T251_T005_Id = '$T005_Id' AND T251_Flag_Altera_Preco='S' AND T251_Grupo='IPV'");
        $mT251 = mysql_fetch_array($T251);
        $TotalPorcentagemIPV = $mT251['AliquotasIPV'];
        
        //Buscando as aliquotas digitadas na tela de montagem do icf no orçamento
        $T251 = mysql_query("SELECT sum(T251_Aliquota-T251_Aliquota_Original) as AliquotasICF from T251 WHERE T251_T005_Id = '$T005_Id' AND T251_Flag_Altera_Preco='S' AND T251_Grupo='ICF'");
        $mT251 = mysql_fetch_array($T251);
        $TotalPorcentagemICF = $mT251['AliquotasICF'];


$T006 = mysql_query("SELECT D009_Valor_Custo_Unitario,
                                    D009_ICF_1,
                                    D049_Origem_Mercadoria,
                                    D009_T001_Id,
                                    D009_Id,
                                    D049_Id,
                                    T005_Flag_Lista_Preco,
                                    T005_D024_Id,
                                    T005_Flag_ACP,
                                    T005_C004_Id,
                                    T006_D006_Id,
                                    T005_Aliquota_ICMS,
                                    T006_Flag_Destino_Produto,
                                    D009_T001_Id,
                                    T006_Id,
                                    T005_Id,
                                    T219_Flag_Origem_Produto,
                                    T006_D005_Id,
                                    T006_Flag_Origem_Produto,
                                    T219_Flag_Comprar,
                                    T219_Valor_Custo_Unitario,
                                    T006_T219_Id,
                                    T006_T225_Id,
                                    T006_Aliquota_ICMS,
                                    T006_Aliquota_PIS,
                                    T006_Aliquota_COFINS,
                                    T006_Aliquota_IPI,
                                    T006_D009_Id,
                                    T006_T005_Id,
                                    T006_Valor_Preco_Original,
                                    T006_Valor_Custo_Unitario,
                                    ifnull(T005_T224_Id,0) as T005_T224_Id,
                                    T006_Valor_Preco_Tabela,
                                    T006_ST_VA,
                                    T006_Percentual_Reducao_ICMS_ST,
                                    T006_ST_ICMS_Interno,
                                    T006_Percentual_Reducao_ICMS,
                                    T006_ICMS_Inter_Estadual,
                                    T006_Aliquota_IRPJ,
                                    T006_Aliquota_CSLL,
                                    T006_Flag_Transferencia,
                                    T006_Quantidade_Estoque,
                                    T006_Quantidade,
                                    T006_C004_Id_Transferencia,
                                    T006_Valor_Preco_Transferencia,
                                    T006_T001_Id,
                                    IF(IFNULL(T005_Flag_Frete, 0) = 0, T006_Valor_Frete_Unitario, 0) AS T006_Valor_Frete_Unitario,
                                    T005A_Flag_Ratear_Frete_Custo,
                                    T005_Casas_Decimais_Preco
                            FROM T006
                        LEFT JOIN T008 ON T008_T006_Id=T006_Id
                        LEFT JOIN T007 ON T007_Id=T008_T007_Id
                        LEFT JOIN D009 ON D009_Id=T006_D009_Id
                        LEFT JOIN D049 ON D049_Id=D009_D049_Id
                        LEFT JOIN T005 ON T005_Id=T006_T005_Id
                        LEFT JOIN T001 ON T001_Id=D009_T001_Id
                        LEFT JOIN T219 ON T219_Id=T006_T219_Id
                        LEFT JOIN T005A ON T005_Id=T005A_T005_Id
                            WHERE T006_T005_Id='{$T005_Id}'
                                AND (T008_Id IS NULL OR T007_Flag_Cancelada='S')
                                AND T005_Flag_Status!='8'");
        if(mysql_num_rows($T006) > 0){
            while ($mT006 = mysql_fetch_array($T006)) {
                $CAD002->atualizaPrecoTabelaPedido($mT006, true,$TotalPorcentagemIPV,$TotalPorcentagemICF);
                mysqli_query("CALL T006_Gravar_Totalizacao_4('{$mT006['T006_Id']}')");
            }
            mysqli_query("CALL T005_Gravar_Totalizacao_4('{$T005_Id}')");
            $resposta['code'] = true;
        } else {
            $resposta['data'] = "Não foi possível atualizar o preço tabela dos produtos, verifique se o pedido já foi faturado.";
            $resposta['code'] = false;
        }
        return $resposta;

    }

    public function atualizaDadosPagamento($form){
        
        global $g;

        $T005_Id                            = $form->campoValorEnviado('T005_Id');
        $T005_Pgto_Parcelas                 = $form->campoValorEnviado('T005_Pgto_Parcelas');
        $T005_Pgto_Primeiro_Vcto_Tipo       = $form->campoValorEnviado('T005_Pgto_Primeiro_Vcto_Tipo');
        $T005_Pgto_Primeiro_Vcto_Valor      = $form->campoValorEnviado('T005_Pgto_Primeiro_Vcto_Valor');
        $T005_Pgto_Primeiro_Vcto_Valor_Data = gCorrigeDataInverte($form->campoValorEnviado('T005_Pgto_Primeiro_Vcto_Valor_Data'));
        $T005_Pgto_Dias_Vcto_Tipo           = $form->campoValorEnviado('T005_Pgto_Dias_Vcto_Tipo');
        $T005_Pgto_Dias_Vcto_Valor          = $form->campoValorEnviado('T005_Pgto_Dias_Vcto_Valor');
        $T005_D027_Id                       = $form->campoValorEnviado('T005_D027_Id');
        $T005_Flag_Frete_Primeira_Parcela   = $form->campoValorEnviado('T005_Flag_Frete_Primeira_Parcela');
        $T005_Flag_ST_Primeira_Parcela      = $form->campoValorEnviado('T005_Flag_ST_Primeira_Parcela');
        $T005_Flag_IPI_Primeira_Parcela     = $form->campoValorEnviado('T005_Flag_IPI_Primeira_Parcela');

        mysqli_query("UPDATE T005 SET T005_Pgto_Parcelas                 = '{$T005_Pgto_Parcelas}',
                                      T005_Pgto_Primeiro_Vcto_Tipo       = '{$T005_Pgto_Primeiro_Vcto_Tipo}',
                                      T005_Pgto_Primeiro_Vcto_Valor      = '{$T005_Pgto_Primeiro_Vcto_Valor}',
                                      T005_Pgto_Primeiro_Vcto_Valor_Data = '{$T005_Pgto_Primeiro_Vcto_Valor_Data}',
                                      T005_Pgto_Dias_Vcto_Tipo           = '{$T005_Pgto_Dias_Vcto_Tipo}',
                                      T005_Pgto_Dias_Vcto_Valor          = '{$T005_Pgto_Dias_Vcto_Valor}',
                                      T005_D027_Id                       = '{$T005_D027_Id}',
                                      T005_Flag_Frete_Primeira_Parcela   = '{$T005_Flag_Frete_Primeira_Parcela}',
                                      T005_Flag_ST_Primeira_Parcela      = '{$T005_Flag_ST_Primeira_Parcela}',
                                      T005_Flag_IPI_Primeira_Parcela     = '{$T005_Flag_IPI_Primeira_Parcela}'
                                WHERE T005_Id = '{$T005_Id}'");
        return true;
    } 
    
    public function gerarAtividadeItemExlusao($T006_Id, $T005_Id) {
       global $g;

       $gerarAtividade = $g['C031']['gerarAtividadesManipulacaoItens'] == 'S';
        if ($gerarAtividade) {
            $atividade = '';

            $sql  = "SELECT T006_Id, ";
            $sql .=       " T006_T005_Id, ";
            $sql .=       " T006_Item, ";
            $sql .=       " T006_Codigo_Produto, ";
            $sql .=       " T006_Descricao_Produto, ";
            $sql .=       " T006_Quantidade, ";
            $sql .=       " T006_Percentual_Desconto, ";
            $sql .=       " T006_Valor_Preco_Unitario ";
            $sql .=   " FROM T006 ";
            $sql .=  " WHERE T006_Id = '{$T006_Id}' ";
            $query = mysql_query($sql);
            $dados = mysql_fetch_Array($query);

            $atividade .= '<div style="background-color: #ff7b47;">';
            $atividade .=   "<span style='padding: 5px 5px 5px 5px;'>Exclusão de item</span>";
            $atividade .=   "<table style='width:100%'>";
            $atividade .=     "<tr>";
            $atividade .=       "<th style='text-align: center; padding: 0px 5px 0px 5px;'>It</th>";
            $atividade .=       "<th style='text-align: left;   padding: 0px 5px 0px 5px;'>Produto</th>";
            $atividade .=       "<th style='text-align: right;  padding: 0px 5px 0px 5px;'>Qtde</th>";
            $atividade .=       "<th style='text-align: right;  padding: 0px 5px 0px 5px;'>Preço</th>";
            $atividade .=       "<th style='text-align: right;  padding: 0px 5px 0px 5px;'>% Desc</th>";
            $atividade .=     "</tr>";
            $atividade .=     "<tr>";
            $atividade .=       "<td style='text-align: center; padding: 0px 5px 0px 5px;'>{$dados['T006_Item']}</td>";
            $atividade .=       "<td style='text-align: left;   padding: 0px 5px 0px 5px;'>{$dados['T006_Codigo_Produto']} - {$dados['T006_Descricao_Produto']}</td>";
            $atividade .=       "<td style='text-align: right;  padding: 0px 5px 0px 5px;'>". gCorrigeNumero($dados['T006_Quantidade']) . "</td>";
            $atividade .=       "<td style='text-align: right;  padding: 0px 5px 0px 5px;'>". gCorrigeNumero($dados['T006_Valor_Preco_Unitario']) . "</td>";
            $atividade .=       "<td style='text-align: right;  padding: 0px 5px 0px 5px;'>". gCorrigeNumero($dados['T006_Percentual_Desconto']) . "</td>";
            $atividade .=     "</tr>";
            $atividade .=   "</table>";
            $atividade .= "</div>";

            $atividade = mysql_real_escape_string($atividade);

            $sqlT259_Id   = " SELECT D085_T259_Id ";
            $sqlT259_Id  .=   " FROM D085 ";
            $sqlT259_Id  .=  " WHERE D085_T005_Id = {$T005_Id} ";
            $sqlT259_Id  .=  " ORDER BY D085_Id ASC ";
            $sqlT259_Id  .=  " LIMIT 1 "; 
            $queryT259_Id = mysql_query($sqlT259_Id);
            $rowT259_Id   = mysql_fetch_assoc($queryT259_Id);
            $D085_T259_Id = $rowT259_Id['D085_T259_Id'];

            $sqlInsert  = " INSERT INTO d085 ( ";
            $sqlInsert .=   " D085_C007_Id, ";
            $sqlInsert .=   " D085_Data, ";
            $sqlInsert .=   " D085_Hora, ";
            $sqlInsert .=   " D085_Mensagem, ";
            $sqlInsert .=   " D085_C007_Id_Inclusao, ";
            $sqlInsert .=   " D085_T005_Id, ";
            $sqlInsert .=   " D085_T259_Id, ";
            $sqlInsert .=   " D085_Flag_Tipo, ";
            $sqlInsert .=   " D085_Flag_Lido, ";
            $sqlInsert .=   " D085_Flag_Manual ";
            $sqlInsert .= " ) VALUES ( ";
            $sqlInsert .=   " '{$g['usuarioAtual']}', ";
            $sqlInsert .=   " current_date(), ";
            $sqlInsert .=   " current_time(), ";
            $sqlInsert .=   " '$atividade', ";
            $sqlInsert .=   " '{$g['usuarioAtual']}', ";
            $sqlInsert .=   " '{$T005_Id}', ";
            $sqlInsert .=   " '{$D085_T259_Id}', ";
            $sqlInsert .=   " 2, ";
            $sqlInsert .=   " 'N', ";
            $sqlInsert .=   " 'N' ";
            $sqlInsert .= " ); ";

            mysql_query($sqlInsert);
        }
    }

    public function gerarAtividadeItemEdicao($T006_Id, $campoEditado, $valorAntigo, $valorNovo) {
       global $g;

       $gerarAtividade = $g['C031']['gerarAtividadesManipulacaoItens'] == 'S';
        if ($gerarAtividade) {
           $prefixo = '';
           $sufixo  = '';
           switch ($campoEditado) {
               case "T006_Quantidade":
                   $campoLocalizado = 'Quantidade';
                    break;
               case "T006A_Quantidade_Conversao":
                    $campoLocalizado = 'Qtde Conversão';
                    break;
               case "T006_Valor_Preco_Sem_Desconto_Unitario":
                   $campoLocalizado = 'Preço';
                   $prefixo         = 'R$ ';
                 break;
               case "T006_Valor_Preco_Unitario":
                   $campoLocalizado = 'Preço';
                   $prefixo         = 'R$ ';
                   break;
               case "T006_Percentual_Desconto":
                   $campoLocalizado = '% Desconto';
                   $sufixo          = ' %';
                 break;
               case "T006_Percentual_Desconto_Bruto":
                   $campoLocalizado = '% Desconto';
                   $sufixo          = ' %';
                   break;
               default:
                   $campoLocalizado = '';
             }

           if (!empty($campoLocalizado)) {
               $atividade = '';

               $sql  = "SELECT T006_Id, ";
               $sql .=       " T006_T005_Id, ";
               $sql .=       " T006_Item, ";
               $sql .=       " T006_Codigo_Produto, ";
               $sql .=       " T006_Descricao_Produto, ";
               $sql .=       " T006_Quantidade, ";
               $sql .=       " T006_Percentual_Desconto, ";
               $sql .=       " T006_Valor_Preco_Bruto ";
               $sql .=   " FROM T006 ";
               $sql .=  " WHERE T006_Id = '{$T006_Id}' ";
               $query = mysql_query($sql);
               $dados = mysql_fetch_Array($query);
               $T005_Id = $dados['T006_T005_Id'];

               $atividade .= '<div style="background-color: yellow;">';
               $atividade .=   "<span style='padding: 5px 5px 5px 5px;'>Edição de item</span>";
               $atividade .=   "<table style='width:100%'>";
               $atividade .=     "<tr>";
               $atividade .=       "<th style='text-align: center; padding: 0px 5px 0px 5px;'>It</th>";
               $atividade .=       "<th style='text-align: left;   padding: 0px 5px 0px 5px;'>Produto</th>";
               $atividade .=       "<th style='text-align: right;  padding: 0px 5px 0px 5px;'>Campo alterado</th>";
               $atividade .=       "<th style='text-align: right;  padding: 0px 5px 0px 5px;'>Valor antigo</th>";
               $atividade .=       "<th style='text-align: right;  padding: 0px 5px 0px 5px;'>Valor novo</th>";
               $atividade .=     "</tr>";
               $atividade .=     "<tr>";
               $atividade .=       "<td style='text-align: center; padding: 0px 5px 0px 5px;'>{$dados['T006_Item']}</td>";
               $atividade .=       "<td style='text-align: left;   padding: 0px 5px 0px 5px;'>{$dados['T006_Codigo_Produto']} - {$dados['T006_Descricao_Produto']}</td>";
               $atividade .=       "<td style='text-align: right;  padding: 0px 5px 0px 5px;'>". $campoLocalizado . "</td>";
               $atividade .=       "<td style='text-align: right;  padding: 0px 5px 0px 5px;'>". $prefixo . $valorAntigo . $sufixo . "</td>";
               $atividade .=       "<td style='text-align: right;  padding: 0px 5px 0px 5px;'>". $prefixo . $valorNovo   . $sufixo . "</td>";
               $atividade .=     "</tr>";
               $atividade .=   "</table>";
               $atividade .= "</div>";
   
               $atividade = mysql_real_escape_string($atividade);
   
               $sqlT259_Id   = " SELECT D085_T259_Id ";
               $sqlT259_Id  .=   " FROM D085 ";
               $sqlT259_Id  .=  " WHERE D085_T005_Id = {$T005_Id} ";
               $sqlT259_Id  .=  " ORDER BY D085_Id ASC ";
               $sqlT259_Id  .=  " LIMIT 1 "; 
               $queryT259_Id = mysql_query($sqlT259_Id);
               $rowT259_Id   = mysql_fetch_assoc($queryT259_Id);
               $D085_T259_Id = $rowT259_Id['D085_T259_Id'];
   
               $sqlInsert  = " INSERT INTO d085 ( ";
               $sqlInsert .=   " D085_C007_Id, ";
               $sqlInsert .=   " D085_Data, ";
               $sqlInsert .=   " D085_Hora, ";
               $sqlInsert .=   " D085_Mensagem, ";
               $sqlInsert .=   " D085_C007_Id_Inclusao, ";
               $sqlInsert .=   " D085_T005_Id, ";
               $sqlInsert .=   " D085_T259_Id, ";
               $sqlInsert .=   " D085_Flag_Tipo, ";
               $sqlInsert .=   " D085_Flag_Lido, ";
               $sqlInsert .=   " D085_Flag_Manual ";
               $sqlInsert .= " ) VALUES ( ";
               $sqlInsert .=   " '{$g['usuarioAtual']}', ";
               $sqlInsert .=   " current_date(), ";
               $sqlInsert .=   " current_time(), ";
               $sqlInsert .=   " '$atividade', ";
               $sqlInsert .=   " '{$g['usuarioAtual']}', ";
               $sqlInsert .=   " '{$T005_Id}', ";
               $sqlInsert .=   " '{$D085_T259_Id}', ";
               $sqlInsert .=   " 2, ";
               $sqlInsert .=   " 'N', ";
               $sqlInsert .=   " 'N' ";
               $sqlInsert .= " ); ";
   
               mysql_query($sqlInsert);
           }
        }
    }

    public function CalcularPrecoMinimo($precoCalc, $SQL_row_D009) {
        global $g;
        return gCorrigeNumero($precoCalc - ($precoCalc * $g['C031']['percentualPrecoMinimoVenda'] / 100), 4);
    }

    public function aplicarQuantidade($T006_Id, $campoEditado, $valorAntigo, $novoValor) {
        global $g;

        $utilizaQuantidadeConversao = $g['C031']['utilizaQuantidadeConversao'] === 'S';
        if (!$utilizaQuantidadeConversao) {
            return true;
        }

        if ($T006_Id <= 0) {
            return "T006_Id nao existe!";
        }
        
        if (empty($campoEditado)) {
            return "Campo_Editado nao existe!";
        }

        $sql  = " SELECT T006_T005_Id, ";
        $sql .=        " T006_Quantidade, ";
        $sql .=        " T006_Descricao_Produto, ";
        $sql .=        " T006A_Id, ";
        $sql .=        " T006A_Quantidade_Conversao, ";
        $sql .=        " D001_Id, ";
        $sql .=        " D001_Peso_Unitario_Kg, ";
        $sql .=        " D002A_Utiliza_Conversao ";
        $sql .=   " FROM T006 ";
        $sql .=        " LEFT JOIN T006A ON T006_Id = T006A_T006_Id ";
        $sql .=        " LEFT JOIN D009  ON D009_Id = T006_D009_Id ";
        $sql .=        " LEFT JOIN D049  ON D049_Id = D009_D049_Id ";
        $sql .=        " LEFT JOIN D001  ON D001_Id = D049_D001_Id ";
        $sql .=        " LEFT JOIN D002  ON D002_Id = D001_D002_Id ";
        $sql .=        " LEFT JOIN D002A ON D002_Id = D002A_D002_Id ";
        $sql .=  " WHERE T006_Id = '$T006_Id' ";
        $query  = mysql_query($sql);
        $dados  = mysql_fetch_array($query);

        $usaConversao = $dados['D002A_Utiliza_Conversao'] === 'S';
        $T005_Id      = $dados['T006_T005_Id'];

        if ($campoEditado === 'T006_Quantidade') {
            if (!$usaConversao) {
                $sqlUpdate  = " UPDATE T006A SET ";
                $sqlUpdate .=   " T006A_Quantidade_Conversao = {$dados['T006_Quantidade']} ";
                $sqlUpdate .= " WHERE T006A_Id = {$dados['T006A_Id']} ";
                mysql_query($sqlUpdate);
            }
        }

        if ($campoEditado === 'T006A_Quantidade_Conversao') {
            if ($usaConversao) {
                $quantidade = $dados['T006A_Quantidade_Conversao'] * $dados['D001_Peso_Unitario_Kg'];

                require_once('bibliotecas/classes/SMTProduto.php');
                $SMTProduto = new SMTProduto();
                $T006_Descricao_Produto = $SMTProduto->descricaoConversao($dados['T006_Descricao_Produto'], $dados['T006A_Quantidade_Conversao'], $dados['D001_Id']);
                $T006_Descricao_Produto = mysql_real_escape_string($T006_Descricao_Produto);

                $sqlUpdate  = " UPDATE T006 SET ";
                $sqlUpdate .=   " T006_Quantidade        = '{$quantidade}', ";
                $sqlUpdate .=   " T006_Descricao_Produto = '{$T006_Descricao_Produto}' ";
                $sqlUpdate .= " WHERE T006_Id = '{$T006_Id}' ";
                mysql_query($sqlUpdate);
                $cErro   = mysql_error();
            }
        }

        mysql_query("call T006_Gravar_Totalizacao_4('$T006_Id');");
        mysql_query("call T005_Gravar_Totalizacao_4('$T005_Id');");

        return true;
    }

    public function gravarQuantidadePesquisaGridPedido($D024_Id, $D009_Id, $campoEditado, $novoValor) {
        global $g;

        if (empty($D024_Id) || empty($D009_Id)) {
            return 'Erro ao alterar a quantidade, por favor entre em contato com o suporte técnico!';
        }

        $quantidade    = gCorrigeNumeroInverte($novoValor);
        $qtdeConversao = $quantidade;

        $utilizaQuantidadeConversao = $g['C031']['utilizaQuantidadeConversao'] == 'S';
        if ($utilizaQuantidadeConversao) {
            $sqlProduto  = " SELECT D001_Id, ";
            $sqlProduto .=        " D001_Peso_Unitario_Kg, ";
            $sqlProduto .=        " D002A_Utiliza_Conversao ";
            $sqlProduto .=   " FROM D009 ";
            $sqlProduto .=        " LEFT JOIN D001  ON D001_Id = D009_D001_Id ";
            $sqlProduto .=        " LEFT JOIN D002  ON D002_Id = D001_D002_Id ";
            $sqlProduto .=        " LEFT JOIN D002A ON D002_Id = D002A_D002_Id ";
            $sqlProduto .=  " WHERE D009_Id = '$D009_Id' ";
            $queryProduto  = mysql_query($sqlProduto);
            $dadosProduto  = mysql_fetch_array($queryProduto);

            $usaConversao = $dadosProduto['D002A_Utiliza_Conversao'] == 'S';

            if ($campoEditado == 'T266_Quantidade') {
                if ($usaConversao) {
                    return 'Este produto utiliza conversão e não é possivel alterar sua quantidade.<br><br>Edite o campo da quantidade conversão.';
                }
            }

            if ($campoEditado == 'T266_Quantidade_Conversao') {
                if ($usaConversao) {
                    $quantidade    = gCorrigeNumeroInverte(gCorrigeNumero($novoValor * $dadosProduto['D001_Peso_Unitario_Kg']));
                    $qtdeConversao = gCorrigeNumeroInverte($novoValor);
                } else {
                    return 'Este produto não utiliza conversão e não é possivel alterar sua quantidade de conversão.<br><br>Edite o campo de quantidade.';
                }
            }
        }

        if ($quantidade > 0) {
            $sqlConsulta  = " SELECT T266_Id ";
            $sqlConsulta .=   " FROM T266 ";
            $sqlConsulta .=  " WHERE T266_D009_Id = '{$D009_Id}' ";
            $sqlConsulta .=    " AND T266_D024_Id = '{$D024_Id}' ";
            $sqlConsulta .=    " AND T266_C007_Id = '{$g['usuarioAtual']}' ";
            $queryConsulta  = mysql_query($sqlConsulta);
            $dadoConsulta  = mysql_fetch_array($queryConsulta);

            if (!empty($dadoConsulta['T266_Id'])) {
                $sqlUpdate  = " UPDATE T266 SET ";
                $sqlUpdate .=   " T266_Quantidade           = '{$quantidade}', ";
                $sqlUpdate .=   " T266_Quantidade_Conversao = '{$qtdeConversao}' ";
                $sqlUpdate .= " WHERE T266_Id = '{$dadoConsulta['T266_Id']}' ";
                mysql_query($sqlUpdate);
            } else {
                $sqlInsert  = " INSERT INTO T266 ( ";
                $sqlInsert .=   " T266_D024_Id, ";
                $sqlInsert .=   " T266_D009_Id, ";
                $sqlInsert .=   " T266_C007_Id, ";
                $sqlInsert .=   " T266_Quantidade, ";
                $sqlInsert .=   " T266_Quantidade_Conversao ";
                $sqlInsert .= " ) VALUES ( ";
                $sqlInsert .=   " '{$D024_Id}', ";
                $sqlInsert .=   " '{$D009_Id}', ";
                $sqlInsert .=   " '{$g['usuarioAtual']}', ";
                $sqlInsert .=   " '{$quantidade}', ";
                $sqlInsert .=   " '{$qtdeConversao}' ";
                $sqlInsert .= " ) ";
                mysql_query($sqlInsert);
            }
        }

        return true;
    }

    public function dividirPedidoSaldoConferido($T005_Id, $D047_Id) {
        global $g;
           
        require_once('bibliotecas/classes/VEN012.php');
        $VEN012 = new VEN012();

        require_once('bibliotecas/classes/SMTProduto.php');
        $SMTProduto = new SMTProduto();

        #region CARREGA OS DADOS DO PEDIDO E VALIDA SE PRECISA DUPLICAR
        $sqlPedido  = " SELECT T005.* ";
        $sqlPedido .=   " FROM T005 ";
        $sqlPedido .=  " WHERE T005.T005_Id = '{$T005_Id}' ";
        $sqlPedido .=    " AND EXISTS (SELECT T006.T006_T005_Id ";
        $sqlPedido .=                  " FROM T006, T006A ";
        $sqlPedido .=                 " WHERE T005.T005_Id = T006.T006_T005_Id ";
        $sqlPedido .=                   " AND T006.T006_Id = T006A.T006A_T006_Id ";
        $sqlPedido .=                   " AND ((T006.T006_Quantidade             > IFNULL(T006.T006_Quantidade_Separacao, 0)) ";
        $sqlPedido .=                   "   OR (T006A.T006A_Quantidade_Conversao > IFNULL(T006A.T006A_Quantidade_Conversao_Separacao, 0)))) ";
        $queryPedido = mysql_query($sqlPedido);
        $dadosPedido = mysql_fetch_assoc($queryPedido);

        if (empty($dadosPedido['T005_Id'])) {
            return true;
        }
        #endregion CARREGA OS DADOS DO PEDIDO E VALIDA SE PRECISA DUPLICAR

        #region DUPLICA O PEDIDO (T005 E T005A)
        $camposT005  = "";
        $valoresT005 = "";
        $today = date("Y-m-d");
        $time = date('H:i:s');
        
        foreach ($dadosPedido as $key => $value) {
            if ($key == 'T005_Id') {
                continue;
            } elseif ($key == "T005_Data_Emissao") {
                $camposT005  .= $key . ", ";
                $valoresT005 .= "'$today',";
            } elseif ($key == "T005_Data_Saida") {
                $camposT005  .= $key . ", ";
                $valoresT005 .= "'$today',";
            } elseif ($key == "T005_Hora_Saida") {
                $camposT005  .= $key . ", ";
                $valoresT005 .= "'$time',";
            } elseif ($key == "T005_Data_Emissao_NF_Devolucao" || $key == "T005_Data_Finalizado_Caixa") {
                $camposT005  .= $key . ", ";
                $valoresT005 .= "'0000-00-00',";
            } elseif ($key == "T005_Data_Envio_Elogistics" || $key == "T005_IPV_Data_Liberado") {
                $camposT005  .= $key . ", ";
                $valoresT005 .= "'0000-00-00 00:00:00',";
            } elseif ($key == "T005_Flag_Dividido") {
                $camposT005  .= $key . ", ";
                $valoresT005 .= "'S',";
            } elseif ($value == "") {
                $camposT005  .= $key . ", ";
                $valoresT005 .= "'0',";
            }
             else {
                $camposT005  .= $key . ", ";
                $valoresT005 .= "'" . mysql_real_escape_string($value) . "',";
            }
        }
        $camposT005  = trim($camposT005, ', ');
        $valoresT005 = trim($valoresT005, ',');

        $insertT005 = "INSERT INTO T005 ({$camposT005}) VALUES ({$valoresT005})";
        $queryT005  = mysql_query($insertT005);
        if (!$queryT005) {
            mysql_query("ROLLBACK");
            return "Erro ao duplicar pedido: " . mysql_error(). " SQL: " . $insertT005;
        }
        $T005_Id_Novo = $g['mysqlLastId'];

        $sqlT005A  = " SELECT T005A.* ";
        $sqlT005A .=   " FROM T005A ";
        $sqlT005A .=  " WHERE T005A.T005A_T005_Id = '{$T005_Id}' ";
        $queryT005A = mysql_query($sqlT005A);
        $dadosT005A = mysql_fetch_assoc($queryT005A);
        
        $camposT005A  = "";
        $valoresT005A = "";
        foreach ($dadosT005A as $key => $value) {
            if ($key == 'T005A_Id') {
                continue;
            } elseif ($key == "T005A_T005_Id") {
                $camposT005A  .= $key . ", ";
                $valoresT005A .= "'{$T005_Id_Novo}',";
            } elseif ($key == "T005A_D047_Id_Divisao") {
                $camposT005A  .= $key . ", ";
                $valoresT005A .= "'{$D047_Id}',";
            } elseif ($key == "T005A_Data_Hora_Impressao" || $key == "T005A_Data_Hora_Reimpressao") {
                $camposT005A  .= $key . ", ";
                $valoresT005A .= "'0000-00-00 00:00:00',";
            } elseif ($key == "T005A_T005_Id_Original") {
                $camposT005A  .= $key . ", ";
                if ($value > 0) {
                    $valoresT005A .= "'{$value}',";
                } else {
                    $valoresT005A .= "'{$T005_Id}',";
                }
                
            } elseif ($value == "") {
                $camposT005A  .= $key . ", ";
                $valoresT005A .= "'0',";
            } else {
                $camposT005A  .= $key . ", ";
                $valoresT005A .= "'" . mysql_real_escape_string($value) . "',";
            }
        }
        $camposT005A  = trim($camposT005A, ', ');
        $valoresT005A = trim($valoresT005A, ',');

        $insertT005A = "INSERT INTO T005A ({$camposT005A}) VALUES ({$valoresT005A})";
        $queryT005A  = mysql_query($insertT005A);
        if (!$queryT005A) {
            mysql_query("ROLLBACK");
            return "Erro ao duplicar pedido: " . mysql_error(). " SQL: " . $insertT005A;
        }
        #endregion DUPLICA O PEDIDO (T005 E T005A)

        #region DUPLICA OS ITENS DO PEDIDOS QUE POSSUEM SALDO NÃO CONFERIDO
        $produtos  = array();
        $sqlItems  = " SELECT T006.*, ";
        $sqlItems .=        " T006A.*, ";
        $sqlItems .=        " D001.D001_Id, ";
        $sqlItems .=        " D002A.D002A_Utiliza_Conversao ";
        $sqlItems .=   " FROM T006A, T006 ";
		$sqlItems .=        " LEFT JOIN D009  ON D009_Id = T006_D009_Id ";
		$sqlItems .=        " LEFT JOIN D049  ON D049_Id = D009_D049_Id ";
		$sqlItems .=        " LEFT JOIN D001  ON D001_Id = D049_D001_Id ";
		$sqlItems .=        " LEFT JOIN D002  ON D002_Id = D001_D002_Id ";
		$sqlItems .=        " LEFT JOIN D002A ON D002_Id = D002A_D002_Id ";
        $sqlItems .=  " WHERE T006.T006_T005_Id = '{$T005_Id}'";
        $sqlItems .=    " AND T006.T006_Id      = T006A.T006A_T006_Id ";
        $sqlItems .=    " AND ((T006.T006_Quantidade             > IFNULL(T006.T006_Quantidade_Separacao, 0)) ";
        $sqlItems .=    "   OR (T006A.T006A_Quantidade_Conversao > IFNULL(T006A.T006A_Quantidade_Conversao_Separacao, 0))) ";
        $queryItems = mysql_query($sqlItems);
        
        while ($dadosT006 = mysql_fetch_assoc($queryItems)) {
            $usaConversao           = $dadosT006['D002A_Utiliza_Conversao'] === 'S';
            $quantidade             = floatval($dadosT006['T006_Quantidade']);
            $quantidadeSeparacao    = floatval($dadosT006['T006_Quantidade_Separacao']);
            $qtdeConversao          = floatval($dadosT006['T006A_Quantidade_Conversao']);
            $qtdeConversaoSeparacao = floatval($dadosT006['T006A_Quantidade_Conversao_Separacao']);
            $saldo                  = $quantidade    - $quantidadeSeparacao;
            $saldoConversao         = $qtdeConversao - $qtdeConversaoSeparacao;
            $D001_Id                = $dadosT006['D001_Id'];

            if (($usaConversao and $saldoConversao > 0) or (!$usaConversao and $saldo > 0)) {
                if (($usaConversao and $qtdeConversaoSeparacao == 0) or (!$usaConversao and $quantidadeSeparacao == 0)) {
                    #region CASO NÃO FOR CONFERIDO NADA, TOOD O SALDO SERÁ REPASSADO PARA O NOVO PEDIDO E O ITEM SERÁ EXCLUIDO NO PEDIDO ANTIGO
                    $deleteT006A  = " DELETE FROM T006A ";
                    $deleteT006A .= " WHERE T006A_Id = '{$dadosT006['T006A_Id']}' ";
                    $queryT006A = mysql_query($deleteT006A);
                    
                    if (!$queryT006A) {
                        mysql_query("ROLLBACK");
                        return "Erro ao duplicar pedido: " . mysql_error(). " SQL: " . $deleteT006A;
                    }

                    $deleteT006  = " DELETE FROM T006 ";
                    $deleteT006 .= " WHERE T006_Id = '{$dadosT006['T006_Id']}' ";
                    $queryT006 = mysql_query($deleteT006);
                 
                    if (!$queryT006) {
                        mysql_query("ROLLBACK");
                        return "Erro ao duplicar pedido: " . mysql_error(). " SQL: " . $deleteT006;
                    }
                    #endregion CASO NÃO FOR CONFERIDO NADA, TOOD O SALDO SERÁ REPASSADO PARA O NOVO PEDIDO E O ITEM SERÁ EXCLUIDO NO PEDIDO ANTIGO
                } else {
                    #region REALIZA UPDATE DAS QAUNTIDADES ANTIGAS
                    $T006_Descricao_Produto = $SMTProduto->descricaoConversao($dadosT006['T006_Descricao_Produto'], $qtdeConversaoSeparacao, $D001_Id);

                    $updateT006  = " UPDATE T006 SET ";
                    $updateT006 .=   " T006_Quantidade        = T006_Quantidade_Separacao, ";
                    $updateT006 .=   " T006_Descricao_Produto = '{$T006_Descricao_Produto}' ";
                    $updateT006 .= " WHERE T006_Id = '{$dadosT006['T006_Id']}' ";
                    $queryT006 = mysql_query($updateT006);
                                        
                    if (!$queryT006) {
                        mysql_query("ROLLBACK");
                        return "Erro ao duplicar pedido: " . mysql_error(). " SQL: " . $updateT006;
                    }
    
                    $updateT006A  = " UPDATE T006A SET ";
                    $updateT006A .=   " T006A_Quantidade_Conversao = T006A_Quantidade_Conversao_Separacao ";
                    $updateT006A .= " WHERE T006A_Id = '{$dadosT006['T006A_Id']}' ";
                    $queryT006A = mysql_query($updateT006A);
                    
                    if (!$queryT006A) {
                        mysql_query("ROLLBACK");
                        return "Erro ao duplicar pedido: " . mysql_error(). " SQL: " . $updateT006A;
                    }
    
                    mysql_query("call T006_Gravar_Totalizacao_4('{$dadosT006['T006_Id']}')");
                    #endregion REALIZA UPDATE DAS QAUNTIDADES ANTIGAS
                }

                #region REALIZA O INSERT DA NOVA T006
                $T006_Descricao_Produto = $SMTProduto->descricaoConversao($dadosT006['T006_Descricao_Produto'], $saldoConversao, $D001_Id);

                $camposT006  = "";
                $valoresT006 = "";
                foreach ($dadosT006 as $key => $value) {
                    if (substr($key, 0, 5) === "T006_") {
                        if ($key == 'T006_Id') {
                            continue;
                        } elseif ($key == "T006_T005_Id") {
                            $camposT006  .= $key . ", ";
                            $valoresT006 .= "'" . mysql_real_escape_string($T005_Id_Novo) . "',";
                        } elseif ($key == "T006_Descricao_Produto") {
                            $camposT006  .= $key . ", ";
                            $valoresT006 .= "'" . mysql_real_escape_string($T006_Descricao_Produto) . "',";
                        } elseif ($key == "T006_Quantidade") {
                            $camposT006  .= $key . ", ";
                            $valoresT006 .= "'" . mysql_real_escape_string($saldo) . "',";
                        } elseif ($key == "T006_Quantidade_Separacao") {
                            $camposT006  .= $key . ", ";
                            $valoresT006 .= "0,";
                        } else {
                            $camposT006  .= $key . ", ";
                            $valoresT006 .= "'" . mysql_real_escape_string($value) . "',";
                        }
                    }
                }
                $camposT006  = trim($camposT006, ', ');
                $valoresT006 = trim($valoresT006, ',');
                
                // Monta o Insert e executa
                $insertT006 = " INSERT INTO T006 ({$camposT006}) VALUES ({$valoresT006}) ";
                $queryT006 = mysql_query($insertT006);
                    
                if (!$queryT006) {
                    mysql_query("ROLLBACK");
                    return "Erro ao duplicar pedido: " . mysql_error(). " SQL: ". $insertT006;
                }
                $T006_Id_Novo = $g['mysqlLastId'];

                $camposT006A  = "";
                $valoresT006A = "";
                foreach ($dadosT006 as $key => $value) {
                    if (substr($key, 0, 6) === "T006A_") {
                        if ($key == 'T006A_Id') {
                            continue;
                        } elseif ($key == "T006A_T006_Id") {
                            $camposT006A  .= $key . ", ";
                            $valoresT006A .= "'" . mysql_real_escape_string($T006_Id_Novo) . "',";
                        } elseif ($key == "T006A_Quantidade_Conversao") {
                            $camposT006A  .= $key . ", ";
                            $valoresT006A .= "'" . mysql_real_escape_string($saldoConversao) . "',";
                        } elseif ($key == "T006A_Quantidade_Conversao_Separacao") {
                            $camposT006A  .= $key . ", ";
                            $valoresT006A .= "0,";
                        } elseif ($key == "T006A_JSON") {
                            $camposT006A  .= $key . ", ";
                            $valoresT006A .= "NULL,";
                        } else {
                            $camposT006A  .= $key . ", ";
                            $valoresT006A .= "'" . mysql_real_escape_string($value) . "',";
                        }
                    }
                }
                $camposT006A  = trim($camposT006A, ', ');
                $valoresT006A = trim($valoresT006A, ',');

                // Monta o Insert e executa
                $insertT006A = " INSERT INTO T006A ({$camposT006A}) VALUES ({$valoresT006A}) ";
                $queryT006A = mysql_query($insertT006A);
                
                
                if (!$queryT006A) {
                    mysql_query("ROLLBACK");
                    return "Erro ao duplicar pedido: " . mysql_error(). " SQL: ". $insertT006A;
                }
                #endregion REALIZA O INSERT DA NOVA T006

                #region MONTA O ARRAY PARA O DISPARO DO EMAIL
                $produto = array(
                    'usaConversao'           => $usaConversao,
                    'quantidade'             => $quantidade,
                    'quantidadeSeparacao'    => $quantidadeSeparacao,
                    'qtdeConversao'          => $qtdeConversao,
                    'qtdeConversaoSeparacao' => $qtdeConversaoSeparacao,
                    'saldo'                  => $saldo,
                    'saldoConversao'         => $saldoConversao,
                    'codigoProduto'          => $dadosT006['T006_Codigo_Produto'],
                    'descricaoProduto'       => $T006_Descricao_Produto,
                    'pedidoCompra'           => $dadosT006['T006_Numero_Pedido_Compra'],
                    'T006_Id'                => $dadosT006['T006_Id'],
                    'T225_Id'                => $dadosT006['T006_T225_Id'],
                    'D009_Id'                => $dadosT006['T006_D009_Id']
                );
    
                array_push($produtos, $produto);
                #region MONTA O ARRAY PARA O DISPARO DO EMAIL
            }
        }
        #endregion DUPLICA OS ITENS DO PEDIDOS QUE POSSUEM SALDO NÃO CONFERIDO

        mysql_query("call T005_Gravar_Totalizacao_4('{$T005_Id}')");
        mysql_query("call T005_Gravar_Totalizacao_4('{$T005_Id_Novo}')");

        $this->recalcularParcelas($T005_Id);
        mysql_query("DELETE FROM T090 WHERE T090_T005_Id={$T005_Id_Novo}'");

        //Insere registros no histórico do pedido (T178)
        $motivoSeparacao = '';

        if ($D047_Id > 0) {
            $sqlMotivo  = " SELECT D047.D047_Descricao_Motivo ";
            $sqlMotivo .=   " FROM D047 ";
            $sqlMotivo .=  " WHERE D047.D047_Id = '{$D047_Id}' ";
            $queryMotivo = mysql_query($sqlMotivo);
            $dadosMotivo = mysql_fetch_assoc($queryMotivo);

            $motivoSeparacao = $dadosMotivo['D047_Descricao_Motivo'];
        }

        $camposHistorico = 'T178_T005_Id, T178_C007_Id, T178_Data, T178_Descricao, T178_Observacao';
        mysql_query("insert into T178 ({$camposHistorico}) values ('{$T005_Id}',      '{$g['usuarioAtual']}', CURRENT_TIMESTAMP(), 'O saldo não conferido deste pedido gerou o pedido {$T005_Id_Novo} ({$motivoSeparacao})', '')");
        mysql_query("insert into T178 ({$camposHistorico}) values ('{$T005_Id_Novo}', '{$g['usuarioAtual']}', CURRENT_TIMESTAMP(), 'Pedido gerado atravéz do saldo não conferido do pedido {$T005_Id}', '')");

        $this->dividirPedidoSaldoConferidoEmail($T005_Id, $T005_Id_Novo, $motivoSeparacao, $produtos);

        $retorno = $VEN012->retornarPedido($T005_Id_Novo, 0);

        if ($retorno == true) {
            return true;
        } else {
            return $retorno;
        }
    }
    
    public function dividirPedidoSaldoConferidoEmail($T005_Id, $T005_Id_Novo, $motivoSeparacao, $produtos) {
        global $g;
        $sqlEmail   = " SELECT C007.C007_Email, ";
        $sqlEmail  .=        " C007.C007_Primeiro_Nome, ";
        $sqlEmail  .=        " D024.D024_Nome_Fantasia, ";
        $sqlEmail  .=        " T005.T005_Id ";
        $sqlEmail  .=   " FROM C007, T005, D024 ";
        $sqlEmail  .=  " WHERE C007.C007_Id = T005.T005_C007_Id_Vendedor_Interno ";
        $sqlEmail  .=    " AND D024.D024_Id = T005.T005_D024_Id ";
        $sqlEmail  .=    " AND T005.T005_Id = '{$T005_Id}' ";
        $queryEmail = mysql_query($sqlEmail);
        $dadosEmail = mysql_fetch_assoc($queryEmail);

        $destinatario = $dadosEmail['C007_Email'];
        $assunto      = 'Pedido conferido parcial - ' . $motivoSeparacao;

        $mensagem  = " <!DOCTYPE html> ";
        $mensagem .= " <html> ";
        $mensagem .=   " <head> ";
        $mensagem .=     " <title>Divisão de pedido</title> ";
        $mensagem .=     " <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'> ";
        $mensagem .=     " <style type='text/css'> ";
        $mensagem .=       " @page { ";
        $mensagem .=         " margin-top: 3cm; ";
        $mensagem .=         " margin-left: 3cm; ";
        $mensagem .=         " margin-bottom: 2cm; ";
        $mensagem .=         " margin-left: 2cm; ";
        $mensagem .=       " } ";
        $mensagem .=       " body { ";
        $mensagem .=         " font-size: 12px; ";
        $mensagem .=         " text-align: justify; ";
        $mensagem .=         " font-family: Helvetica, Arial, sans-serif; ";
        $mensagem .=       " } ";
        $mensagem .=       " table, th, td { ";
        $mensagem .=         " border: 1px solid black; ";
        $mensagem .=         " border-collapse: collapse; ";
        $mensagem .=       " } ";
        $mensagem .=       " .qtde { ";
        $mensagem .=         " text-align: right; ";
        $mensagem .=       " } ";
        $mensagem .=     " </style> ";
        $mensagem .=   " </head> ";
        $mensagem .=   " <body> ";
        $mensagem .=     " <div> ";
        $mensagem .=       " <div style='margin-bottom: 20px'> ";
        $mensagem .=         " <div>Prezado(a) {$dadosEmail['C007_Primeiro_Nome']},</div> ";
        $mensagem .=         " <div>Seu pedido não foi conferido totalmente e o seu saldo não conferido gerou um novo pedido.</div> ";
        $mensagem .=       " </div> ";
        $mensagem .=       " <div style='margin-bottom: 10px'> ";
        $mensagem .=           " <div><b>Pedido:</b> {$dadosEmail['T005_Id']}.</div> ";
        $mensagem .=           " <div><b>Cliente:</b> {$dadosEmail['D024_Nome_Fantasia']}.</div> ";
        $mensagem .=           " <div><b>Motivo da conferência parcial:</b> {$motivoSeparacao}.</div> ";
        $mensagem .=           " <div><b>Pedido Novo:</b> {$T005_Id_Novo}.</div> ";
        $mensagem .=       " </div> ";

        //dados dos itens
        $mensagem .=       " <table style='padding: 3px 10px;'> ";
        $mensagem .=         " <tr> ";
        $mensagem .=           " <th>Produto</th> ";
        $mensagem .=           " <th class='qtde'>Qtde Original</th> ";
        $mensagem .=           " <th class='qtde'>Qtde Conferida</th> ";
        $mensagem .=           " <th class='qtde'>Qtde Pedido Novo</th> ";
        $mensagem .=           " <th>Previsão Recebimento</th> ";
        $mensagem .=           " <th class='qtde'>Saldo a Receber</th> ";
        $mensagem .=           " <th>Pedido Cliente</th> ";
        $mensagem .=         " </tr> ";

        foreach ($produtos as $produto) {
            $previsaoEntrega = $this->dividirPedidoSaldoRecebimento($produto);
            $qtdeOriginal    = $produto['usaConversao'] ? $produto['qtdeConversao'] :          number_format($produto['quantidade'],          2, "," ,".");
            $qtdeConferida   = $produto['usaConversao'] ? $produto['qtdeConversaoSeparacao'] : number_format($produto['quantidadeSeparacao'], 2, "," ,".");
            $qtdePedidoNovo  = $produto['usaConversao'] ? $produto['saldoConversao'] :         number_format($produto['saldo'],               2, "," ,".");

            $mensagem .=     " <tr> ";
            $mensagem .=       " <td>{$produto['codigoProduto']} - {$produto['descricaoProduto']}</td> ";
            $mensagem .=       " <td class='qtde'>{$qtdeOriginal}</td> ";
            $mensagem .=       " <td class='qtde'>{$qtdeConferida}</td> ";
            $mensagem .=       " <td class='qtde'>{$qtdePedidoNovo}</td> ";
            $mensagem .=       " <td>{$previsaoEntrega['previsao']}</td> ";
            $mensagem .=       " <td class='qtde'>{$previsaoEntrega['saldo']}</td> ";
            $mensagem .=       " <td>{$produto['pedidoCompra']}</td> ";
            $mensagem .=     " </tr> ";
        }

        $mensagem .=       " </table> ";
        $mensagem .=     " <div style='margin-top: 30px'> ";
        $mensagem .=       " <div>Smartness - <a href='http://www.smartness.com.br' target='_blank'>www.smartness.com.br</a></div> ";
        $mensagem .=     " </div> ";
        $mensagem .=   " </body> ";
        $mensagem .= " </html> ";

        if (!empty($destinatario)) {
            $email = array(
                'C007_Id'      => $g['usuarioAtual'],
                'destinatario' => $destinatario,
                'assunto'      => $assunto,
                'corpo'        => $mensagem,
                'cc'           => $this->dividirPedidoSaldoConferidoCopiaEmail($T005_Id),
                'anexo'        => ''
            );

            require_once('bibliotecas/classes/SMTEMAIL.php');
            $SMTEMAIL = new SMTEMAIL_();
            $SMTEMAIL->geraEmail($email);
        }   
    }

    public function dividirPedidoSaldoConferidoCopiaEmail($T005_Id) {
        global $g;

        $idPerfilEmailDivisao = 2;

        require_once('bibliotecas/classes/CAD030.php');
        $CAD030 = new CAD030_();
        $emails = $CAD030->emailsPerfil($idPerfilEmailDivisao, $g['empresaAtual']);

        return $emails;
    }

    public function dividirPedidoSaldoRecebimento($produto) {
        $dados = array(
            'previsao' => 'SEM COMPRA',
            'saldo'    => 0
        );

        if (intval($produto['T225_Id']) > 0) {
            $where =  " AND T225.T225_Id = '{$produto['T225_Id']}' ";
        } else {
            $where  = " AND T225.T225_D009_Id = '{$produto['D009_Id']}' ";
            $where .= " AND NOT EXISTS (SELECT T006.T006_Id ";
            $where .=                   " FROM T006 ";
            $where .=                  " WHERE T006.T006_T225_Id = T225.T225_Id) ";
        }

        $sqlPrevEntrega  = " SELECT T225A.T225A_Data_Entrega AS Previsao, ";
        $sqlPrevEntrega .=        " T225.T225_Quantidade - IFNULL(T225_Quantidade_Recebida, 0) AS Saldo ";
        $sqlPrevEntrega .=   " FROM T225, T225A, T224 ";
        $sqlPrevEntrega .=  " WHERE T225.T225_T224_Id     = T224.T224_Id ";
        $sqlPrevEntrega .=    " AND T225A.T225A_T225_Id   = T225.T225_Id ";
        $sqlPrevEntrega .=    " AND T224.T224_Flag_Status = 3 ";
        $sqlPrevEntrega .=  $where;
        $sqlPrevEntrega .= " ORDER BY T225.T225_Data_Entrega ASC ";
        $sqlPrevEntrega .= " LIMIT 1 ";
        $queryPrevEntrega = mysql_query($sqlPrevEntrega);
        $dadosPrevEntrega = mysql_fetch_assoc($queryPrevEntrega);

        $registros = mysql_num_rows($queryPrevEntrega);
        if ($registros > 0) {
            $dados['previsao'] = gCorrigeData($dadosPrevEntrega['Previsao']);
            $dados['saldo']    = $produto['usaConversao'] ? $dadosPrevEntrega['Saldo'] : number_format($dadosPrevEntrega['Saldo'], 2, "," ,".");;
        }

        return $dados;
    }

    public function recalcularParcelas($T005_Id) {
        require_once('bibliotecas/classes/GFormWrap.php');

        mysql_query("DELETE FROM T090 WHERE T090_T005_Id={$T005_Id}'");

        $sql   = " SELECT T005_Id, ";
        $sql  .=        " T005_Pgto_Parcelas, ";
        $sql  .=        " T005_Pgto_Dias_Vcto_Valor, ";
        $sql  .=        " T005_Pgto_Dias_Vcto_Valor, ";
        $sql  .=        " T005_Pgto_Primeiro_Vcto_Valor, ";
        $sql  .=        " T005_Pgto_Primeiro_Vcto_Valor, ";
        $sql  .=        " T005_Pgto_Portador, ";
        $sql  .=        " T005_Pgto_Primeiro_Vcto_Tipo ";
        $sql  .=   " FROM T005 ";
        $sql  .=  " WHERE T005_Id='{$T005_Id}' ";
        $query = mysql_query($sql);
        $dados = mysql_fetch_assoc($query);

        $dadosPedido['T005_Id']                     = $dados['T005_Id'];
        $dadosPedido['Total_Parcelas']              = $dados['T005_Pgto_Parcelas'];
        $dadosPedido['IntervalorDias']              = $dados['T005_Pgto_Dias_Vcto_Valor'];
        $dadosPedido['diaFixo']                     = $dados['T005_Pgto_Dias_Vcto_Valor'];
        $dadosPedido['primeiroVencimentoNum']       = $dados['T005_Pgto_Primeiro_Vcto_Valor'];
        $dadosPedido['primeiroVencimentoData']      = $dados['T005_Pgto_Primeiro_Vcto_Valor'];
        $dadosPedido['Portador_D027_Id']            = $dados['T005_Pgto_Portador'];
        $dadosPedido['selecionaPrimeiroVencimento'] = $dados['T005_Pgto_Primeiro_Vcto_Tipo'];
        $dadosPedido['selecionaDias']               = 'I';
        $dadosPedido['usarFreteParcela']            = 'OFF';
        $dadosPedido['usarSTParcela']               = 'OFF';
        $dadosPedido['usarIPIParcela']              = 'OFF';
        $dadosParcela = new GFormWrap($dadosPedido);

        return $this->atualizarParcelas($dadosParcela);
    }

    /**
     * Gera uma NF-e de débito ou crédito a partir de quantidades selecionadas do pedido.
     *
     * A nota permanece em elaboração e é relacionada ao pedido pelos itens (T008_T006_Id).
     * O cabeçalho não recebe T007_T005_Id para não consumir nem encerrar o faturamento normal.
     *
     * @param array $dados
     * @return array|string
     */
    public function gerarNFDebitoCreditoPedido(array $dados)
    {
        global $g;

        require_once('bibliotecas/classes/GFormWrap.php');
        require_once('bibliotecas/classes/VEN003.php');
        require_once('bibliotecas/classes/VEN001.php');

        $T005_Id      = isset($dados['T005_Id']) ? (int) $dados['T005_Id'] : 0;
        $D024_Id      = isset($dados['D024_Id']) ? (int) $dados['D024_Id'] : 0;
        $D006_Id      = isset($dados['D006_Id']) ? (int) $dados['D006_Id'] : 0;
        $destino      = isset($dados['destino']) ? (int) $dados['destino'] : 0;
        $tipoOperacao = isset($dados['tipoOperacao']) ? strtolower(trim($dados['tipoOperacao'])) : '';
        $tipoFiscal   = isset($dados['tipoFiscal']) ? trim($dados['tipoFiscal']) : '';
        $observacao   = isset($dados['observacao']) ? trim($dados['observacao']) : '';
        $quantidades  = isset($dados['quantidades']) && is_array($dados['quantidades']) ? $dados['quantidades'] : array();
		$referencias  = isset($dados['referencias']) && is_array($dados['referencias']) ? $dados['referencias'] : array();
		$chaveReferenciaCabecalho = preg_replace('/\D/', '', isset($dados['chaveReferenciaCabecalho']) ? $dados['chaveReferenciaCabecalho'] : '');
        $finalidade   = ($tipoOperacao === 'credito') ? '5' : (($tipoOperacao === 'debito') ? '6' : '');
		$tiposValidos = ($tipoOperacao === 'credito') ? array('01', '02', '03', '04', '05', '06') : array('01', '02', '03', '04', '05', '06', '07', '08');
		$exigeReferenciaCabecalho = $tipoOperacao === 'credito' && in_array($tipoFiscal, array('01', '03', '04'), true);
		if ($tipoOperacao !== 'debito' || $tipoFiscal !== '06') {
			return 'A partir do pedido somente é permitida a NF de débito tipo 06 - Pagamento antecipado.';
		}

        if ($T005_Id <= 0 || $D024_Id <= 0 || $D006_Id <= 0 || $destino < 1 || $destino > 20 || empty($finalidade) || !in_array($tipoFiscal, $tiposValidos)) {
            return 'Os dados informados para gerar a NF de débito/crédito são inválidos.';
        }
		if ($exigeReferenciaCabecalho && strlen($chaveReferenciaCabecalho) !== 44) {
			return 'Este tipo de NF de crédito exige uma única chave de NF-e referenciada, com 44 dígitos, no cabeçalho.';
		}
		if ($tipoOperacao === 'credito' && $tipoFiscal === '02' && (int) date('Y') < 2029) {
			return 'NF de crédito tipo 02 somente pode ser emitida a partir de janeiro de 2029.';
		}
		if ($exigeReferenciaCabecalho) {
			$modeloReferencia = substr($chaveReferenciaCabecalho, 20, 2);
			$modelosPermitidos = $tipoFiscal === '03' ? array('55', '65') : array('55');
			if (!in_array($modeloReferencia, $modelosPermitidos, true)) {
				return 'A chave informada no cabeçalho possui modelo incompatível com este tipo de NF de crédito.';
			}
			if (in_array($tipoFiscal, array('03', '04'), true)) {
				$cnpjEmitente = preg_replace('/\D/', '', isset($g['C004']['C004_Cnpj']) ? $g['C004']['C004_Cnpj'] : '');
				$cnpjReferencia = substr($chaveReferenciaCabecalho, 6, 14);
				if (strlen($cnpjEmitente) === 14 && $cnpjReferencia !== $cnpjEmitente) {
					return 'A NF-e referenciada deve possuir o mesmo emitente da NF de crédito.';
				}
			}
		}
		if (!$exigeReferenciaCabecalho) {
			$chaveReferenciaCabecalho = '';
		}

        $pedidoSql = mysql_query("SELECT T005.*, D024.D024_Id AS Cliente_Ativo
                                    FROM T005
                               LEFT JOIN D024 ON D024.D024_Id = '{$D024_Id}'
                                                 AND D024.D024_Flag_Cliente = 'S'
                                                 AND D024.D024_Flag_Ativo = 'S'
                                   WHERE T005.T005_Id = '{$T005_Id}'
                                     AND T005.T005_C004_Id = '{$g['empresaAtual']}'");
        $pedido = mysql_fetch_assoc($pedidoSql);
        if (empty($pedido['T005_Id'])) {
            return 'Pedido não encontrado para a empresa atual.';
        }
        if (empty($pedido['Cliente_Ativo'])) {
            return 'Selecione um cliente ativo.';
        }
		if ($finalidade === '6' && $tipoFiscal === '06' && (int) $pedido['T005_D024_Id'] !== $D024_Id) {
			return 'Pagamento antecipado deve ser emitido para o mesmo cliente do pedido, pois a chave será referenciada na futura NF de venda.';
		}

        $cfopSql = mysql_query("SELECT D006_Id FROM D006 WHERE D006_Id = '{$D006_Id}' AND IFNULL(D006_Flag_Inativo,'N') != 'S'");
        if (mysql_num_rows($cfopSql) === 0) {
            return 'Selecione uma CFOP ativa.';
        }

        $itens = array();
        foreach ($quantidades as $T006_Id => $quantidadeInformada) {
            $T006_Id = (int) $T006_Id;
            $quantidadeTexto = trim((string) $quantidadeInformada);
            if (strpos($quantidadeTexto, ',') !== false) {
                $quantidadeTexto = str_replace('.', '', $quantidadeTexto);
                $quantidadeTexto = str_replace(',', '.', $quantidadeTexto);
            }
            $quantidade = is_numeric($quantidadeTexto) ? (float) $quantidadeTexto : -1;
            if ($quantidade == 0) {
                continue;
            }
            if ($T006_Id <= 0 || $quantidade < 0) {
                return "A quantidade informada para o item {$T006_Id} é inválida.";
            }
            $itens[$T006_Id] = $quantidade;
        }
        if (empty($itens)) {
            return 'Informe uma quantidade maior que zero em pelo menos um produto.';
        }

        $nomeLock = mysql_real_escape_string("ven002-nfdc-{$g['empresaAtual']}-{$T005_Id}-{$finalidade}");
        $lockSql = mysql_query("SELECT GET_LOCK('{$nomeLock}', 10) AS Bloqueado");
        $lock = mysql_fetch_assoc($lockSql);
        if (empty($lock['Bloqueado'])) {
            return 'Este pedido está sendo processado por outro usuário. Aguarde alguns instantes e tente novamente.';
        }

        $produtos = array();
		$referenciasDFeUtilizadas = array();
		$chavesDFeUtilizadas = array();
        foreach ($itens as $T006_Id => $quantidade) {
            $produtoSql = mysql_query("SELECT T006.*, D049.D049_Origem_Mercadoria,
                                              D049.D049_Codigo_Barras, D001.D001_Codigo_Barras,
                                              D001.D001_Codigo_Barras_Tributavel
                                         FROM T006
                                    LEFT JOIN D009 ON D009.D009_Id = T006.T006_D009_Id
                                    LEFT JOIN D049 ON D049.D049_Id = D009.D009_D049_Id
                                    LEFT JOIN D001 ON D001.D001_Id = D049.D049_D001_Id
                                        WHERE T006.T006_Id = '{$T006_Id}'
                                          AND T006.T006_T005_Id = '{$T005_Id}'
                                          AND T006.T006_Quantidade > 0
                                          AND IFNULL(T006.T006_Flag_Status,0) != 3");
            $produto = mysql_fetch_assoc($produtoSql);
            if (empty($produto['T006_Id'])) {
                mysql_query("SELECT RELEASE_LOCK('{$nomeLock}')");
                return "O item {$T006_Id} não pertence ao pedido ou não está disponível para geração.";
            }

			$geradaSql = mysql_query("SELECT IFNULL(SUM(T008.T008_Quantidade),0) AS Quantidade_Gerada
										FROM T008
								   LEFT JOIN T007 ON T007.T007_Id = T008.T008_T007_Id
								  INNER JOIN T007A ON T007A.T007A_T007_Id = T007.T007_Id
									   WHERE T008.T008_T006_Id = '{$T006_Id}'
										 AND T007.T007_Flag_Finalidade_NFe = '{$finalidade}'
										 AND T007A.T007A_Tipo_NF_Debito = '06'
                                         AND IFNULL(T007.T007_Flag_Cancelada,'N') != 'S'
                                         AND IFNULL(T007.T007_Flag_Inutilizado,'N') != 'S'");
            $gerada = mysql_fetch_assoc($geradaSql);
            if (($quantidade + (float) $gerada['Quantidade_Gerada']) > ((float) $produto['T006_Quantidade'] + 0.00001)) {
                mysql_query("SELECT RELEASE_LOCK('{$nomeLock}')");
                return "O produto {$produto['T006_Codigo_Produto']} ultrapassa a quantidade do pedido considerando as notas já geradas.";
            }
            $produto['Quantidade_NFDC'] = $quantidade;
			$referencia = isset($referencias[$T006_Id]) && is_array($referencias[$T006_Id]) ? $referencias[$T006_Id] : array();
			$chaveReferencia = preg_replace('/\D/', '', isset($referencia['chave']) ? $referencia['chave'] : '');
			$itemReferencia = (int) (isset($referencia['item']) ? $referencia['item'] : 0);
			$exigeChave = ($tipoOperacao === 'debito' && in_array($tipoFiscal, array('03', '04'))) || ($tipoOperacao === 'credito' && $tipoFiscal === '06');
			$exigeItem = ($tipoOperacao === 'debito' && $tipoFiscal === '04') || ($tipoOperacao === 'credito' && $tipoFiscal === '06');
			if ($exigeChave && strlen($chaveReferencia) !== 44) {
				mysql_query("SELECT RELEASE_LOCK('{$nomeLock}')");
				return "Informe uma chave DF-e válida de 44 dígitos para o produto {$produto['T006_Codigo_Produto']}.";
			}
			if ($exigeItem && $itemReferencia <= 0) {
				mysql_query("SELECT RELEASE_LOCK('{$nomeLock}')");
				return "Informe o item do DF-e referenciado para o produto {$produto['T006_Codigo_Produto']}.";
			}
			$produto['Chave_DFe_Referenciado'] = $exigeChave ? $chaveReferencia : '';
			$produto['Item_DFe_Referenciado'] = $exigeItem ? $itemReferencia : 0;
			if ($exigeChave) {
				$identificadorReferencia = $chaveReferencia . ':' . ($exigeItem ? $itemReferencia : 'SEM_ITEM');
				if (isset($referenciasDFeUtilizadas[$identificadorReferencia])) {
					mysql_query("SELECT RELEASE_LOCK('{$nomeLock}')");
					return "A mesma referência de DF-e foi informada em mais de um produto ({$produto['T006_Codigo_Produto']}).";
				}
				$referenciasDFeUtilizadas[$identificadorReferencia] = true;
				$chavesDFeUtilizadas[$chaveReferencia] = true;
			}
            $produtos[] = $produto;
        }
		$permiteMaisDeUmDFe = $tipoOperacao === 'debito' && $tipoFiscal === '03';
		if (!$permiteMaisDeUmDFe && count($chavesDFeUtilizadas) > 1) {
			mysql_query("SELECT RELEASE_LOCK('{$nomeLock}')");
			return 'Para este tipo de NF, todos os itens devem referenciar o mesmo documento fiscal.';
		}

        $VEN003 = new VEN003();
        $VEN001 = new VEN001();
        $dataAtual = date('d/m/Y');
        $horaAtual = date('H:i:s');
        $casasQuantidade = isset($g['c020']['C020_Casas_Decimais_Quantidade'])
            ? max(0, (int) $g['c020']['C020_Casas_Decimais_Quantidade'])
            : 2;
        $casasPreco = !empty($pedido['T005_Casas_Decimais_Preco'])
            ? max(0, (int) $pedido['T005_Casas_Decimais_Preco'])
            : 2;
        $formNota = new GFormWrap(array(
            'T007_D024_Id'                      => $D024_Id,
            'T007_D006_Id'                      => $D006_Id,
            'T007_D022_Id'                      => isset($pedido['T005_D022_Id']) ? $pedido['T005_D022_Id'] : 0,
            'T007_D036_Id'                      => isset($pedido['T005_D036_Id']) ? $pedido['T005_D036_Id'] : 0,
            'T007_Data_Emissao'                 => $dataAtual,
            'T007_Data_Saida'                   => $dataAtual,
            'T007_Hora_Emissao'                 => $horaAtual,
            'T007_Hora_Saida'                   => $horaAtual,
            'T007_Flag_ACP'                     => '6',
			// B25-110/B25-120: crédito é entrada (tpNF=0) e débito é saída (tpNF=1).
            'T007_Flag_Entrada_Saida'           => ($finalidade === '5') ? 'E' : 'S',
            'T007_Flag_Finalidade_NFe'          => $finalidade,
            'T007_Flag_Destino_Produto'         => $destino,
            'T007_Flag_Frete'                   => '9',
            'T007_Flag_PDV'                     => 'N',
            'T007_Flag_Indicador_Presenca'      => isset($pedido['T005_Flag_Indicador_Presenca']) && $pedido['T005_Flag_Indicador_Presenca'] !== '' ? $pedido['T005_Flag_Indicador_Presenca'] : '0',
            'T007_Flag_Intermediador_Transacao' => isset($pedido['T005_Flag_Intermediador_Transacao']) && $pedido['T005_Flag_Intermediador_Transacao'] !== '' ? $pedido['T005_Flag_Intermediador_Transacao'] : '0',
            'T007_Casas_Decimais_Quantidade'     => $casasQuantidade,
            'T007_Casas_Decimais_Preco'          => $casasPreco,
            'T007_Observacao_Nota_Fiscal_1'      => $observacao,
            'T007_Observacao_Nota_Fiscal_2'      => '',
            'T007A_Tipo_NF_Debito'               => ($finalidade === '6') ? $tipoFiscal : '',
            'T007A_Tipo_NF_Credito'              => ($finalidade === '5') ? $tipoFiscal : ''
        ));

        $retorno = $VEN003->incluirNota($formNota);
        if ($retorno !== true) {
            mysql_query("SELECT RELEASE_LOCK('{$nomeLock}')");
            return $retorno;
        }
        $T007_Id = (int) $g['mysqlLastId'];
		$cancelarGeracaoNFDC = function ($mensagem) use ($VEN003, $T007_Id, $nomeLock) {
			// incluirNota() abre uma transação. Todo erro posterior precisa desfazê-la
			// e restaurar o autocommit antes de excluir um eventual cabeçalho não transacional.
			mysql_query("ROLLBACK");
			mysql_query("SET AUTOCOMMIT=1");
			$notaSql = mysql_query("SELECT T007_Id FROM T007 WHERE T007_Id = '{$T007_Id}' LIMIT 1");
			if ($notaSql && mysql_num_rows($notaSql) > 0) {
				$VEN003->ven003excluirNota($T007_Id);
				mysql_query("SET AUTOCOMMIT=1");
			}
			mysql_query("SELECT RELEASE_LOCK('{$nomeLock}')");
			return $mensagem;
		};

		// Marcar antes de vincular os itens, ainda na transação da geração.
		// Não desmarcar na exclusão/cancelamento: outra antecipação pode existir.
		if ($finalidade === '6' && $tipoFiscal === '06') {
			$marcouAntecipacao = mysql_query("INSERT INTO T005A (T005A_T005_Id, T005A_Flag_Pagamento_Antecipado)
											 SELECT T005_Id, 'S' FROM T005
											 WHERE T005_Id = '{$T005_Id}' AND T005_C004_Id = '{$g['empresaAtual']}'
											 ON DUPLICATE KEY UPDATE T005A_Flag_Pagamento_Antecipado = 'S'");
			if (!$marcouAntecipacao) {
				return $cancelarGeracaoNFDC('Não foi possível marcar o pagamento antecipado no pedido: ' . mysql_error());
			}
		}

		// B25-30/B25-40: crédito 01, 03 e 04 exige exatamente uma NF-e
		// referenciada no cabeçalho. O grupo NFref é montado a partir da T132.
		if ($exigeReferenciaCabecalho) {
			$formReferencia = new GFormWrap(array(
				'T132_T007_Id' => $T007_Id,
				'T132_Chave_Acesso' => $chaveReferenciaCabecalho,
				'T132_Cupom_Numero_EFC' => '',
				'T132_Cupom_Numero_COO' => '',
				'T132_Cupom_Modelo' => '',
				'T132_UF_IBGE' => '',
				'T132_Ano_Mes' => '',
				'T132_Cnpj' => '',
				'T132_Modelo' => '',
				'T132_Serie' => '',
				'T132_Numero_Nota_Fiscal' => ''
			));
			$retornoReferencia = $VEN003->incluirNotaReferenciada($formReferencia);
			if ($retornoReferencia !== true) {
				return $cancelarGeracaoNFDC('Não foi possível gravar a NF-e referenciada no cabeçalho: ' . $retornoReferencia);
			}
		}

        foreach ($produtos as $produto) {
            $aliquotas = $VEN001->retornaAliquotas($produto['T006_D009_Id'], false, false, $T007_Id, false, false, false, $D006_Id, false);
            if (!is_array($aliquotas)) {
				return $cancelarGeracaoNFDC('Não foi possível calcular os impostos do produto ' . $produto['T006_Codigo_Produto'] . ': ' . $aliquotas);
            }

            $quantidade = $produto['Quantidade_NFDC'];
            $preco = (float) $produto['T006_Valor_Preco_Sem_Desconto_Unitario'];
            $codigoBarras = !empty($produto['D049_Codigo_Barras']) ? $produto['D049_Codigo_Barras'] : $produto['D001_Codigo_Barras'];
            $origem = ($produto['D049_Origem_Mercadoria'] !== '' && $produto['D049_Origem_Mercadoria'] !== null)
                ? $produto['D049_Origem_Mercadoria']
                : substr((string) $produto['T006_Flag_Situacao_Tributaria'], 0, 1);

            $formItem = new GFormWrap(array(
                'T008_T007_Id'                           => $T007_Id,
                'T008_D009_Id'                           => $produto['T006_D009_Id'],
                'T008_D006_Id'                           => $D006_Id,
                'T008_D005_Id'                           => !empty($aliquotas['D005_Id']) ? $aliquotas['D005_Id'] : $produto['T006_D005_Id'],
                'T008_D037_Id'                           => $produto['T006_D037_Id'],
                'T008_D059_Id'                           => isset($aliquotas['D059_Id']) ? $aliquotas['D059_Id'] : $produto['T006_D059_Id'],
                'T008_Quantidade'                        => gCorrigeNumero($quantidade, $casasQuantidade),
                'T008A_Quantidade_Comercial'             => gCorrigeNumero($quantidade, $casasQuantidade),
                'T008_Valor_Preco_Sem_Desconto_Unitario' => gCorrigeNumero($preco, $casasPreco),
                'T008A_Preco_Unitario_Comercial'         => gCorrigeNumero($preco, $casasPreco),
                'T008_Codigo_Produto'                    => $produto['T006_Codigo_Produto'],
                'T008_Codigo_Substituto'                 => $produto['T006_Codigo_Substituto'],
                'T008_Descricao_Produto'                 => $produto['T006_Descricao_Produto'],
                'T008_Codigo_Barras'                     => $codigoBarras,
                'T008_Codigo_Barras_Tributavel'          => $produto['D001_Codigo_Barras_Tributavel'],
                'T008_Item'                              => $produto['T006_Item'],
                'T008_Numero_Pedido_Compra'              => $produto['T006_Numero_Pedido_Compra'],
                'T008_Item_Pedido_Compra'                => $produto['T006_Item_Pedido_Compra'],
                'T008_Observacao'                        => $produto['T006_Observacao'],
                'T008_Informacoes_Adicionais'            => $produto['T006_Informacoes_Adicionais'],
                'T008_Peso_Unitario'                     => gCorrigeNumero($produto['T006_Peso_Unitario']),
                'T008_Peso_Bruto'                        => gCorrigeNumero($produto['T006_Peso_Bruto']),
                // O preço copiado do pedido já é líquido. Repassar também o percentual
                // faria incluirItemNota() aplicar o desconto uma segunda vez.
                'T008_Percentual_Desconto'               => '0',
                'T008_Flag_Destino_Produto'              => $destino,
                'T008_Flag_Calculo_Manual'               => 'N',
                'Codigo_Origem_Mercadoria'               => $origem,
                'T008_Aliquota_IPI'                      => gCorrigeNumero($aliquotas['Aliquota_IPI']),
                'T008_Percentual_Reducao_IPI'            => gCorrigeNumero($aliquotas['Percentual_Reducao_IPI']),
                'T008_Aliquota_ICMS'                     => gCorrigeNumero($aliquotas['Aliquota_ICMS']),
                'T008_Percentual_Reducao_ICMS'           => gCorrigeNumero($aliquotas['Percentual_Reducao_ICMS']),
                'T008_Percentual_Reducao_ICMS_ST'        => gCorrigeNumero($aliquotas['Percentual_Reducao_ICMS_ST']),
                'T008_Flag_ST'                           => $aliquotas['Flag_ST'],
                'T008_ST_VA'                             => gCorrigeNumero($aliquotas['ST_VA']),
                'T008_ST_ICMS_Interno'                   => gCorrigeNumero($aliquotas['ST_ICMS_Interno']),
                'T008_ICMS_Inter_Estadual'               => gCorrigeNumero($aliquotas['ICMS_Inter_Estadual']),
                'T008_Modalidade_ICMS'                   => $aliquotas['Modalidade_ICMS'],
                'T008_Modalidade_ICMS_Substituicao'      => $aliquotas['Modalidade_ICMS_Substituicao'],
                'T008_Situacao_Tributaria_IPI'           => $aliquotas['Situacao_Tributaria_IPI'],
                'T008_Situacao_Tributaria_PIS'           => $aliquotas['Situacao_Tributaria_PIS'],
                'T008_Situacao_Tributaria_COFINS'        => $aliquotas['Situacao_Tributaria_COFINS'],
                'T008_Aliquota_PIS'                      => gCorrigeNumero($aliquotas['Aliquota_PIS']),
                'T008_Aliquota_COFINS'                   => gCorrigeNumero($aliquotas['Aliquota_COFINS']),
                'T008_Flag_Isento_PIS'                   => $aliquotas['Flag_Isento_PIS'],
                'T008_Flag_Isento_COFINS'                => $aliquotas['Flag_Isento_COFINS'],
				'T008A_Chave_Acesso_DFe_Referenciado'    => $produto['Chave_DFe_Referenciado'],
				'T008A_Item_DFe_Referenciado'            => $produto['Item_DFe_Referenciado']
            ));

            $ultimoItemSql = mysql_query("SELECT IFNULL(MAX(T008_Id),0) AS T008_Id FROM T008 WHERE T008_T007_Id = '{$T007_Id}'");
            $ultimoItem = mysql_fetch_assoc($ultimoItemSql);
            $ultimoT008_Id = isset($ultimoItem['T008_Id']) ? (int) $ultimoItem['T008_Id'] : 0;

            $retorno = $VEN003->incluirProduto($formItem);
            if ($retorno !== true) {
				return $cancelarGeracaoNFDC('Não foi possível incluir o produto ' . $produto['T006_Codigo_Produto'] . ': ' . $retorno);
            }
			$itemNotaSql = mysql_query("SELECT T008_Id
										  FROM T008
										 WHERE T008_T007_Id = '{$T007_Id}'
										   AND T008_D009_Id = '{$produto['T006_D009_Id']}'
										   AND T008_Id > '{$ultimoT008_Id}'
									  ORDER BY T008_Id DESC LIMIT 1");
			$itemNota = mysql_fetch_assoc($itemNotaSql);
			$T008_Id = isset($itemNota['T008_Id']) ? (int) $itemNota['T008_Id'] : 0;
			if ($T008_Id <= 0) {
				return $cancelarGeracaoNFDC('Não foi possível localizar o item incluído na NF para relacioná-lo ao pedido.');
			}
            mysql_query("UPDATE T008 SET T008_T006_Id = '{$produto['T006_Id']}' WHERE T008_Id = '{$T008_Id}' AND T008_T007_Id = '{$T007_Id}'");
            if (mysql_error()) {
                $erro = mysql_error();
				return $cancelarGeracaoNFDC('Erro ao relacionar o item da NF ao pedido: ' . $erro);
            }
        }

        $itensVinculadosSql = mysql_query("SELECT COUNT(*) AS Quantidade
                                             FROM T008
                                            WHERE T008_T007_Id = '{$T007_Id}'
                                              AND T008_T006_Id > 0");
        $itensVinculados = mysql_fetch_assoc($itensVinculadosSql);
        if ((int) $itensVinculados['Quantidade'] !== count($produtos)) {
			return $cancelarGeracaoNFDC('A NF não foi gravada porque nem todos os itens puderam ser relacionados ao pedido.');
        }

        $retorno = $VEN003->atualizarAliquotasNF($T007_Id);
        if ($retorno !== true) {
			return $cancelarGeracaoNFDC('Não foi possível concluir o cálculo fiscal da NF: ' . $retorno);
        }

        $nomeOperacao = ($finalidade === '5') ? 'crédito' : 'débito';
		$statusPedidoNFDC = ($finalidade === '5') ? 'NF CRÉDITO GERADA' : 'NF DÉBITO GERADA';
		mysql_query("UPDATE T005 SET T005_Nome_Status = '{$statusPedidoNFDC}' WHERE T005_Id = '{$T005_Id}' AND T005_C004_Id = '{$g['empresaAtual']}' AND IFNULL(T005_Flag_Status,0) = 0");
		if (mysql_error()) {
			$erro = mysql_error();
			return $cancelarGeracaoNFDC('Erro ao atualizar o status do pedido: ' . $erro);
		}
        $observacaoHistorico = "NF de {$nomeOperacao} nº {$T007_Id} gerada em elaboração.";
        mysql_query("INSERT INTO T178 (T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao)
                          VALUES ('{$T005_Id}','{$g['usuarioAtual']}',CURRENT_TIMESTAMP(),'NF de " . ucfirst($nomeOperacao) . " gerada','" . mysql_real_escape_string($observacaoHistorico) . "')");

        // incluirNota() inicia uma transação. Sem o COMMIT o cabeçalho (T007) pode
        // permanecer e os itens transacionais (T008) serem desfeitos ao encerrar a requisição.
        mysql_query("COMMIT");
        $erroCommit = mysql_error();
        mysql_query("SET AUTOCOMMIT=1");
        if (!empty($erroCommit)) {
            mysql_query("SELECT RELEASE_LOCK('{$nomeLock}')");
            return 'Não foi possível confirmar a gravação da NF: ' . $erroCommit;
        }

        // Confere novamente depois do COMMIT para nunca retornar sucesso com NF sem itens.
        $itensPersistidosSql = mysql_query("SELECT COUNT(*) AS Quantidade
                                              FROM T008
                                             WHERE T008_T007_Id = '{$T007_Id}'
                                               AND T008_T006_Id > 0");
        $itensPersistidos = mysql_fetch_assoc($itensPersistidosSql);
        if ((int) $itensPersistidos['Quantidade'] !== count($produtos)) {
            $VEN003->ven003excluirNota($T007_Id);
            mysql_query("UPDATE T005
                            SET T005_Nome_Status = ''
                          WHERE T005_Id = '{$T005_Id}'
                            AND T005_C004_Id = '{$g['empresaAtual']}'
                            AND IFNULL(T005_Flag_Status,0) = 0");
            mysql_query("SELECT RELEASE_LOCK('{$nomeLock}')");
            return 'A NF não foi mantida porque os itens não persistiram após a confirmação da gravação.';
        }

        $notaSql = mysql_query("SELECT T007_Numero_Nota_Fiscal, T007_Serie FROM T007 WHERE T007_Id = '{$T007_Id}'");
        $nota = mysql_fetch_assoc($notaSql);
        mysql_query("SELECT RELEASE_LOCK('{$nomeLock}')");

        return array(
            'T007_Id' => $T007_Id,
            'numero' => $nota['T007_Numero_Nota_Fiscal'],
            'serie' => $nota['T007_Serie'],
            'operacao' => $nomeOperacao,
            'itens' => count($produtos)
        );
    }

    /* Função trigger que substitui os valores que vão ser preenchidos
    no formulário de inserção de produto, serve somente para personalização */
    public function ven002ModPreencheFormProdutosPedido($row, $params = false){return $row;}

    /* Função trigger que substitui o porcentagem desconto para as observações do pedido */
    public function ven002ModObservacaoPercentualDesconto($percentualDesconto, $arrayInfo = false){return $percentualDesconto;}

    /* Função para verificar se o valor da flag de conferência permite o faturamento do pedido */
    public function excecaoValidaPedido($T005_Id){return true;}

    public function acoesFinaisAtualizarPedido($T005_Id){
        return true;
    }
 
    public function triggerCriarPedidoIntelipost($T005_Id) {
        return true;
    }  
}

$VEN002 = new VEN002();
