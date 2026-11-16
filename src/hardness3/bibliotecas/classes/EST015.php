<?php
namespace hardness;

require_once('bibliotecas/classes/MYTCPDF.PHP');
require_once "old/lib/funcoes.php";

definirClasse('EST015');
class EST015_
{
    public function montarEmailLinkPolux($r_T218_Id){
        $retorno = array();
        return $retorno;
    }
    
    /**
     * excluir - Excluir uma cotacao
     *
     * @param int $T218
     * @return true/string(erro)
     */
    public function excluir($T218_Id)
    {
        if (empty($T218_Id)) {
            return "Não foi passado o Id do Pedido, contate o suporte técnico";
        }
        
        $cClausula = " select ";
        $cClausula .= " * ";
        $cClausula .= " from T218 ";
        $cClausula .= " where T218_Id='{$T218_Id}' ";
        $cSql_T218 = mysql_query($cClausula);
        $mT218     = mysql_fetch_array($cSql_T218);
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        $cClausula = "delete from T220 where T220_T218_Id='{$T218_Id}'";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro <br /><br /> $cClausula";
        }
        
        $T219 = mysql_query("select T219_Id from T219 where T219_T218_Id='{$T218_Id}'");
        while ($mT219 = mysql_fetch_array($T219)) {
            $cClausula = "delete from T202 where T202_T219_Id='{$mT219['T219_Id']}'";
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: $cErro <br /><br /> $cClausula";
            }
            $cClausula = "delete from T219 where T219_Id='{$mT219['T219_Id']}'";
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: $cErro <br /> <br /> $cClausula";
            }
        }
        
        $cClausula = "delete from T218 where T218_Id='{$T218_Id}'";
        mysql_query($cClausula);
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
     * calculaTotalServicos - Calcula o valor total de serviços do orçamento
     *
     * @param int $T218
     * @return true/string(erro)
     */
    public function calculaTotalServicos($T218_Id)
    {
        $T218_Id = mysql_real_escape_string($T218_Id);
        $sql     = "SELECT * FROM T218 where T218_Id='{$T218_Id}'";
        $T218    = mysql_query($sql);
        $T218    = mysql_fetch_array($T218);
        
        $sql      = "SELECT T223_Valor_Total FROM T223 where T223_T218_Id='{$T218_Id}'";
        $T223_sql = mysql_query($sql);
        
        $total = 0;
        while ($T223 = mysql_fetch_array($T223_sql)) {
            $total += $T223['T223_Valor_Total'];
        }
        
        return $total;
    }
    
    /**
     * calculaTotalGeral - Atualiza e retorna o total geral do orçamento (com os serviços)
     *
     * @param int $T218
     * @return true/string(erro)
     */
    public function calculaTotalGeral($T218_Id)
    {
        
        $totalServicos = $this->calculaTotalServicos($T218_Id);
        $sql           = "SELECT * FROM T218 where T218_Id='{$T218_Id}'";
        $T218          = mysql_query($sql);
        $T218          = mysql_fetch_array($T218);
        
        return $T218['T218_Valor_Total_Servicos'] + $T218['T218_Valor_Total'];
    }
    
    /**
     * calculaTotalServicosConfirmado - Calcula o valor total de serviços do orçamento
     *
     * @param int $T218
     * @return true/string(erro)
     */
    public function calculaTotalServicosConfirmado($T218_Id)
    {
        $T218_Id = mysql_real_escape_string($T218_Id);
        $sql     = "SELECT * FROM T218 where T218_Id='{$T218_Id}'";
        $T218    = mysql_query($sql);
        $T218    = mysql_fetch_array($T218);
        
        $sql      = "SELECT * FROM T223 where T223_T218_Id='{$T218_Id}'";
        $T223_sql = mysql_query($sql);
        
        $total = 0;
        while ($T223 = mysql_fetch_array($T223_sql)) {
            $total += $T223['T223_Quantidade_Confirmada'] * $T223['T223_Valor_Unitario'];
        }
        
        return $total;
    }
    
    /**
     * calculaTotalGeralConfirmado - Atualiza e retorna o total geral do orçamento (com os serviços)
     *
     * @param int $T218
     * @return true/string(erro)
     */
    public function calculaTotalGeralConfirmado($T218_Id)
    {
        
        $totalServicosConfirmado = $this->calculaTotalServicosConfirmado($T218_Id);
        $sql                     = "SELECT * FROM T218 where T218_Id='{$T218_Id}'";
        $T218                    = mysql_query($sql);
        $T218                    = mysql_fetch_array($T218);
        
        return $T218['T218_Valor_Total_Servicos'] + $T218['T218_Valor_Total_Confirmado'];
    }
    
    /**
     * montarEmail - Retorna os dados para ser enviado o e-mail
     * A resposta é um array:
     *  array(
     *      'Para' => '',
     *      'Assunto' => '',
     *      'Corpo' => '',
     *      'D024_Id' => 0
     *      'T139_Valor_Total' => 0.00
     *      'T139_Numero_Registros' => 0
     *  )
     * 
     * @param string $T218_Id
     */
    public function montarEmail($T218_Id)
    {
        global $g, $confUsuario;
        $T218_Id = mysql_real_escape_string($T218_Id);
        $sql     = "SELECT * FROM T218 LEFT JOIN D013 ON D013_Id=T218_D013_Id WHERE T218_Id='{$T218_Id}'";
        $T218    = mysql_query($sql);
        $T218    = mysql_fetch_array($T218);
        
        $sql  = "SELECT D024_E_Mail FROM D024 WHERE D024_Id='{$T218['T218_D024_Id']}'";
        $D024 = mysql_query($sql);
        $D024 = mysql_fetch_array($D024);
        
        $array = array();
        
        $array['Para']    = (!empty($T218['D013_E_Mail'])) ? $T218['D013_E_Mail'] : $D024['D024_E_Mail'];
        $array['Assunto'] = "Cotação de Compra Nº {$T218_Id}";
        
        
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
        } else {
            $caminhoImagem = "";
        }
        
        $dir = $g['pathDados'] . 'tmp/';
        is_dir($dir) or mkdir($dir, 0777, true);
        $filename = "cotacao-compra-{$T218_Id}.pdf";
        $file     = $dir . $filename;
        $fileFull = $g['pathRaiz'] . '/' . $file;
        
        $r_link = '/est/est015/form_func-ajax/est015ImprimirOrcamento/?ajax=true&gerarHtml=1&T218_Id=' . $T218_Id . '&alcadaUpdate=' . $alcadaUpdate;
        $r_link = str_replace(' ', '+', $r_link);
        shell_exec("xvfb-run --server-num " . mt_rand(0, 98) . " -s \"-screen 0 640x480x16\" wkhtmltopdf --quiet --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 \"{$g['urlRaiz']}{$r_link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"");
        
        $array['Anexos']    = array(
            $file
        );
        $array['AnexosWeb'] = array(
            '/hardness3/' . $file
        );
        
        $array['Corpo']   = <<<EOT
            <!--<img src='{$caminhoImagem}' style='margin-left:10px'><br /><br /><br /><br />-->
            <font style='font-size:16px'>Segue nossa cotação de Compra <b>$T218_Id</b> em anexo.</font><br /><br /><br />
EOT;
        $array['D024_Id'] = $T218['T218_D024_Id'];
        return $array;
    }
    
    /**
     * montarEmailPdf - Retorna os dados para ser enviado o e-mail
     * A resposta é um array:
     *  array(
     *      'Para' => '',
     *      'Assunto' => '',
     *      'Corpo' => '',
     *      'D024_Id' => 0
     *      'T139_Valor_Total' => 0.00
     *      'T139_Numero_Registros' => 0
     *  )
     * 
     * @param string $T218_Id
     */
    public function montarEmailPdf($T218_Id)
    {
        global $g, $confUsuario, $sys;
        
        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();

        /**
         * SELECT's
         */
        $C004  = mysql_query("SELECT * FROM C004 left join D018 on D018_Id=C004_D018_Id left join D020 on D020_Id=C004_D020_Id WHERE C004_Id = '{$g['empresaAtual']}'");
        $mC004 = mysql_fetch_array($C004);
        
        $T218_Id = mysql_real_escape_string($T218_Id);
        $T218    = <<<EOT
                SELECT * FROM T218 
                LEFT JOIN D013 ON D013_Id=T218_D013_Id 
                LEFT JOIN D116 ON D116_Id=T218_D116_Id 
                LEFT JOIN D133 ON D133_Id=D116_D133_Id 
                LEFT JOIN D134 ON D134_Id=D116_D134_Id 
                LEFT JOIN D024 ON D024_Id=T218_D024_Id 
                LEFT JOIN D020 ON D020_Id=D024_D020_Id
                LEFT JOIN D018 ON D018_Id=D024_D018_Id 
                LEFT JOIN D022 ON D022_Id=T218_D022_Id
                LEFT JOIN C007 ON C007_Id=T218_C007_Id_Vendedor_Interno
                WHERE T218_Id='{$T218_Id}';
EOT;
        
        $T218  = mysql_query($T218);
        $mT218 = mysql_fetch_array($T218);
        
        $T219 = <<<EOT
                SELECT * FROM T219 
                LEFT JOIN D009 ON D009_Id=T219_D009_Id 
                LEFT JOIN D049 ON D049_Id=D009_D049_Id
                LEFT JOIN D082 ON D082_Id=D049_D082_Id
                LEFT JOIN D001 ON D001_Id=D049_D001_Id
                LEFT JOIN D003 ON D003_Id=D001_D003_Id
                LEFT JOIN T144 ON T144_D001_Id=D001_Id
                LEFT JOIN D037 ON D037_Id=T219_D037_Id
                LEFT JOIN D005 ON D005_Id=T219_D005_Id
                LEFT JOIN D006 ON D006_Id=T219_D006_Id
                LEFT JOIN T219A ON T219_Id=T219A_T219_Id
                WHERE T219_T218_Id = '{$T218_Id}' 
                GROUP BY T219_Id
                ORDER BY T219_Item,T219_Id
EOT;
        $T219 = mysql_query($T219);
        
        
        $html .= <<<EOT
    <style>
      .moldura {
        font-size: 11px;
        background:black;
        cellspacing:1px;
        width:100%;
        padding: 0px;
        border: 2px solid #000;
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
        border-top:0px;
      }     
      .tdBordaR {
        font-size:11px;
        border-bottom:1px dotted black;
        border-top:0px;
      }     
      .tdBordaL {
        border-right:1px solid black;
        border-bottom:1px dotted black;
        font-size:11px;
        border-top:0px;
      }     
      .tdTitulo {
        border-bottom:1px solid black;
        border-right:1px solid black;
        font-size:11px;
        background:lightblue;
        border-top:0px;
        font-weight:bold;
      }     
      .tdTituloR {
        border-bottom:1px solid black;
        font-size:11px;
        background:lightblue;
        border-top:0px;
        font-weight:bold;
      }     
      .tdTituloL {
        border-bottom:1px solid black;
        border-right:1px solid black;
        font-size:11px;
        background:lightblue;
        border-top:0px;
        font-weight:bold;
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
        // (TODO) passar para o arq. conf
        $caminhoImagem = str_replace('201.22.57.137:8282', 'eurosul', $caminhoImagem);
        $caminhoImagem = str_replace('201.22.57.137:8181', 'sigma', $caminhoImagem);
        
        $mT218['T218_Data_Emissao'] = gCorrigeData($mT218['T218_Data_Emissao']);
        $T218_Numero_Ordem_Compra   = str_pad($T218_Id, 6, "0", STR_PAD_LEFT);
        
        $html .= <<<EOT
        <table class="moldura" height="1040px" cellspacing="0">
            <tr>
                <td style="padding:0px 0px 0px 0px">
                   <table cellspacing="0" cellpadding="4px" width="100%" height="100%" style="border:0px">
                        <tr>
                            <td rowspan=2 style="border-right: 2px solid #000;border-bottom: 2px solid #000;width:27%" align='center'>
                                {$caminhoImagem}
                            </td>
                            <td rowspan=2 style="border-right: 2px solid #000;border-bottom: 2px solid #000;">
                                <table class="tabela">
                                    <tr>
                                        <td>{$mC004['C004_Nome_Empresa']}-</td>
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
                            <td style="font-size:22px; border-bottom: 2px solid #000;width:27%" align="center">
                                <b>Cotação compra<br>Nº {$T218_Numero_Ordem_Compra}<br>{$mT218['T218_Data_Emissao']}<b>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 2px solid #000;width:27%" >
                                <table class="tabela">
                                    <tr>
                                        <td><b><u>COMPRADOR:</u></b></td>
                                    </tr>
                                    <tr>
                                        <td>{$mT218['C007_Primeiro_Nome']}</td>
                                    </tr>
                                    <tr>
                                        <td>{$mT218['C007_Email']}</td>
                                    </tr>
                                    <tr>
                                        <td>{$mT218['C007_DDD_Telefone']}-{$mT218['C007_Telefone']}</td>
                                    </tr>
                                </table>          
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="padding:0px 0px 0px 0px">
                    <table cellspacing="0" cellpadding="4px" width="100%" height="100%" style="border:0px">
                        <tr>
                            <td colspan="{$colspan}" align="center" style="background:lightblue;font-size:12px;font-weight:bold; border-bottom: 2px solid #000;">
                                FORNECEDOR
                            </td>
                        </tr>
                        <tr>
                            <td colspan="{$colspan}" style="border-bottom: 2px solid #000;">
                                <table class="tabela" style="width:100%">
                                    <tr>
                                        <td width="50%">
                                            <table class="tabela" width="100%">
                                            <tr>
                                                <td><b>{$mT218['D024_Nome_Empresa']}</b></td>
                                            </tr>
                                            <tr>
                                                <td>{$mT218['D024_Endereco']}, {$mT218['D024_Numero']} - {$mT218['D024_Bairro']}</td>
                                            </tr>
                                            <tr>
                                                <td><b>CEP:</b> {$mT218['D024_Cep']} {$mT218['D020_Nome_Cidade']} - {$mT218['D018_UF']}</td>
                                            </tr>
                                            <tr>
                                                <td><b>CNPJ/CPF:</b> {$mT218['D024_Cnpj']}{$mT218['D024_Cpf']}</td>
                                            </tr>
                                            </table>
                                        </td>
                                        <td width="50%">
                                            <table class="tabela" width="100%">
                                            <tr>
                                                <td><b>Incr. Estadual:</b> {$mT218['D024_Inscricao_Estadual']}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Contato: </b>{$mT218['D013_Nome_Contato']}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Telefone: </b>{$mT218['D013_Telefone_1']}</td>
                                            </tr>
                                            <tr>
                                                <td><b>E-mail: </b>{$mT218['D013_E_Mail']}</td>
                                            </tr>
                                            </table>
                                        </td>
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
          <td style="padding:0px 0px 0px 0px">
           <table cellspacing="0" cellpadding="4px" width="100%" height="100%" style="border:0px">
            <tr>
              <td align="center" style="background:lightblue" class="tdTituloL">
                  It
              </td>
EOT;
              if($g['C031']['exibirFotoImpressaoCompra'] == 'S'){
                    $html .= <<<EOT
                        <td align="center" style="background:lightblue;width:60px" class="tdTitulo">
                            Foto
                        </td>
EOT;
              }                            
        $html .= <<<EOT
              <td align="center" style="background:lightblue;width:60px" class="tdTitulo">
                Código
              </td>
              <td class="tdTitulo" style="background:lightblue">
                Descrição
              </td>
EOT;
    if($g['C031']['exibirMarcaImpressaoCompra'] == 'S'){
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
                Unitário R\$
              </td>
              <td align="right" style="background:lightblue" class="tdTitulo">
                Total R\$
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
                ST R\$
              </td>
            </tr>
EOT;

        $count          = 1;
        $valorTotalProd = 0;
        $valorTotalFCP  = 0;
        while ($mT219 = mysql_fetch_array($T219)) {
            $mT219['T219_Quantidade']                        = gCorrigeNumero($mT219['T219_Quantidade']);
            $mT219['T219_Valor_Preco_Sem_Desconto_Unitario'] = number_format($mT219['T219_Valor_Preco_Sem_Desconto_Unitario'], 5, ',', '.');
            $mT219['T219_Valor_Total_Preco']                 = gCorrigeNumero($mT219['T219_Valor_Total_Preco']);
            $mT219['T219_Aliquota_IPI']                      = gCorrigeNumero($mT219['T219_Aliquota_IPI']);
            $mT219['T219_Valor_IPI']                         = gCorrigeNumero($mT219['T219_Valor_IPI']);
            $mT219['T219_Aliquota_ICMS']                     = number_format($mT219['T219_Aliquota_ICMS'], 0);
            $mT219['T219_Valor_ICMS']                        = gCorrigeNumero($mT219['T219_Valor_ICMS']);
            $mT219['T219_Valor_ICMS_Substituicao_Retencao']  = gCorrigeNumero($mT219['T219_Valor_ICMS_Substituicao_Retencao']);
            $mT219['T219_ST_VA_Ajustada']                    = gCorrigeNumero($mT219['T219_ST_VA_Ajustada']);
            $valorTotalFCP += $mT219['T219A_Valor_FCP_ST'];

            $observacaoItem                                  = '<font></font>';
            if (!empty($mT219['T219_Observacao'])) {
                $observacaoItem = '<font style="background:#FCF9AE"><b>Observação:</b> ' . $mT219['T219_Observacao'] . '</font><br>';
            }
            if (!empty($mT219['T219_Codigo_Substituto'])) {
                //$mT219['T219_Codigo_Produto']=$mT219['T219_Codigo_Substituto'];
            }

            if($g['C031']['exibirFotoImpressaoCompra'] == 'S'){

                $foto = $CAD002->cad002FotoProduto($mT219['D001_Id'],60);

            }
            
            $html .= <<<EOT
            <tr>
EOT;
            
            if (!empty($mT219['T219_Codigo_Substituto'])) {
                $mT219['T219_Codigo_Substituto'] = '<b>' . $mT219['T219_Codigo_Substituto'] . '</b><br />';
            }
            $html .= <<<EOT
                <td align="center" class="tdBordaL">
                    {$mT219['T219_Item']}
                </td>
EOT;
           if($g['C031']['exibirFotoImpressaoCompra'] == 'S'){
           $html .= <<<EOT
                <td align="center" class="tdBorda" height="50px">
                   $foto
                </td>
EOT;
            }
            $html .= <<<EOT
                <td align="center" class="tdBorda">
                    {$mT219['T219_Codigo_Substituto']}
                    {$mT219['T219_Codigo_Produto']}
                </td>
                <td class="tdBorda">
                    <b>{$mT219['T219_Descricao_Produto']} {$mT219['D049_Modelo']}</b><br>
                    {$observacaoItem}
                    <font style='color:#0F079E'><i>Prazo entrega em dias:<b> {$mT219['T219_Observacao_Entrega']} </b></i></font>
                </td>
EOT;
    if($g['C031']['exibirMarcaImpressaoCompra'] == 'S'){
        $html .= <<<EOT
                 <td align="center" class="tdBorda">
                    {$mT219['D082_Marca']}
                </td>
EOT;
    }
        $html .= <<<EOT
                <td align="center" class="tdBorda">
                    {$mT219['D037_Unidade']}
                </td>
                <td align="center" class="tdBorda">
                    {$mT219['D005_Classificacao_Fiscal']}
                </td>
                <td align="center" class="tdBorda">
                    {$mT219['D006_Codigo_CFOP']}
                </td>
                <td align="center" class="tdBorda">
                    <b>{$mT219['T219_Quantidade']}</b>
                </td>
                <td align="right" class="tdBorda">
                    <b>{$mT219['T219_Valor_Preco_Sem_Desconto_Unitario']}</b>
                </td>
                <td align="right" class="tdBorda">
                    {$mT219['T219_Valor_Total_Preco']}
                </td>
                <td align="center" class="tdBorda">
                    {$mT219['T219_Aliquota_IPI']}
                </td>
                <td align="center" class="tdBorda">
                    {$mT219['T219_Aliquota_ICMS']}
                </td>
                <td align="right" class="tdBorda">
                    {$mT219['T219_ST_VA_Ajustada']}
                </td>
                <td align="right" class="tdBordaR">
                    {$mT219['T219_Valor_ST']}
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
                <td colspan="3" style="padding:0px 0px 0px 0px; border-top: 2px solid #000;">

EOT;
        
        /**
         * PDF - TOTAIS
         */
        $mT218['T218_Valor_Total_Produtos']             = gCorrigeNumero($mT218['T218_Valor_Total_Produtos']);
        $mT218['T218_Valor_Frete']                      = gCorrigeNumero($mT218['T218_Valor_Frete']);
        $mT218['T218_Valor_Total_IPI']                  = gCorrigeNumero($mT218['T218_Valor_Total_IPI']);
        $mT218['T218_Valor_Total_ICMS']                 = gCorrigeNumero($mT218['T218_Valor_Total_ICMS']);
        $mT218['T218_Valor_Total']                      = gCorrigeNumero($mT218['T218_Valor_Total']);
        $mT218['T218_Data_Entrega']                     = gCorrigeData($mT218['T218_Data_Entrega']);
        $mT218['T218_Valor_ICMS_Substituicao_Retencao'] = gCorrigeNumero($mT218['T218_Valor_ICMS_Substituicao_Retencao']);

        $html .= <<<EOT
            <table class="tabela" cellspacing="0px" cellpadding="4px">
                <tr>
                    <td align="left" width="105">Forma pagamento: </td><td align="left" width="330" style="border-right:2px solid black"><b>{$mT218['T218_Forma_Pagamento']}</b></td>
                    <td align="left" width="105">Valor produtos: </td><td  align="left" width="100"><b>R$ {$mT218['T218_Valor_Total_Produtos']}</b></td>
                </tr>
EOT;
        switch ($mT218['T218_Flag_Frete']) {
            case '':
                $T218_Frete = "";
                break;
            case '0':
                $T218_Frete = "Por conta do emitente";
                break;
            case '1':
                $T218_Frete = "Por conta do destinatário";
                break;
            case '2':
                $T218_Frete = "Terceiros";
                break;
            case '9':
                $T218_Frete = "Sem frete";
                break;
            default:
                $T218_Frete = "Não informado";
                break;
        }
        
        $html .= <<<EOT
                <tr>
                    <td align="left" width="105">Frete: </td><td align="left" style="border-right:2px solid black"><b>{$T218_Frete}</b></td>
                    <td align="left" width="105">Valor IPI: </td><td  align="left"><b>R$ {$mT218['T218_Valor_Total_IPI']}</b></td>
                </tr>
                <tr>
                    <td align="left" width="105">Transportadora: </td><td align="left" style="border-right:2px solid black"><b>{$mT218['D022_Nome_Empresa']}</b></td>
                    <td align="left" width="105">Valor frete: </td><td  align="left"><b>R$ {$mT218['T218_Valor_Frete']}</b></td>
                </tr>
                <tr>
                    <td align="left" width="105"></td><td align="left" width="330" style="border-right:2px solid black"></td>
                    <td align="left" width="105">Valor ST: </td><td align="left"><b>R$ {$mT218['T218_Valor_ST']}</b></td>
                </tr>
EOT;
        if($valorTotalFCP > 0){
            $valorTotalFCP = gCorrigeNumero($valorTotalFCP);
            $html .= <<<EOT
                <tr>
                    <td align="left" width="105"></td><td align="left" width="330" style="border-right:2px solid black"></td>
                    <td align="left" width="105">Valor FCP ST: </td><td align="left"><b>R$ {$valorTotalFCP}</b></td>
                </tr>
EOT;
        }
    
        $html .= <<<EOT
                <tr>
                    <td align="left" width="105"></td><td align="left" width="330" style="border-right:2px solid black"></td>
                    <td align="left" width="105">Valor total: </td><td align="left" style="font-size:18px"><b>R$ {$mT218['T218_Valor_Total']}</b></td>
                </tr>
                <tr>
                    <td align="left" width="105"></td><td align="left"  style="border-right:2px solid black"></td>
                    <td align="left" width="105">Valor ICMS: </td><td align="left"><b>R$ {$mT218['T218_Valor_Total_ICMS']}</b></td>
                </tr>
            
EOT;
        
        $html .= "</table>";
        
        $html .= <<<EOT
                </td>
            </tr>

            <tr>
                <td colspan="3" style="background:lightblue;font-weight:bold; border-top: 2px solid #000; border-bottom: 2px solid #000;" align="center">
                    OBSERVAÇÕES 
                </td>
            </tr>
            <tr>
                <td colspan="3">
EOT;
        
        $html .= '<br>' . str_replace(chr(10), '<br>', '<b>' . $mT218['T218_Observacao']) . '</b><br><br>';
        if(!empty($mC004['C004_Observacao_Cotacao'])){
            $html .= "<font style='color:red;font-weight:bold'>Observações:</font><br><br>" . str_replace("\n", "<br>", $mC004['C004_Observacao_Cotacao']);
        }

        $nomeSiteEmpresa = (!empty($sys['nomeEmpresa']) && !empty($sys['siteEmpresa'])) ? strToUpper($sys['nomeEmpresa']) .' - '. strToUpper($sys['siteEmpresa']) : 'HARDNESS SISTEMAS - WWW.HARDNESS.COM.BR';

        $html .= <<<EOT
                
                </td>
            </tr>

            <tr>
                <td colspan="3" height="100%">
                </td>
            </tr>

        </table>
        <font style="font-size:10px;">{$nomeSiteEmpresa}</font>

EOT;
        
        return $html;
        
    }
    
    /**
     * est015montarPdfGrid - Gera o PDF do Grid de Orçamentos a partir da sql passada por parametro (Tablet)
     *
     * @param string $sqlT218
     */
    public function est015montarPdfGrid($sqlT218)
    {
        global $g, $confUsuario;
        
        $resultT218 = mysql_query($sqlT218);
        
        
        $sqlC004    = "SELECT C004_Nome_Empresa FROM C004 WHERE C004_Id='{$g['empresaAtual']}'";
        $resultC004 = mysql_query($sqlC004);
        $C004       = mysql_fetch_array($resultC004);
        
        $sqlC007    = "SELECT * FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'";
        $resultC007 = mysql_query($sqlC007);
        $C007       = mysql_fetch_array($resultC007);
        
        //$layout = $g['conf']->ini('layoutOrcamentoEmailPdf');
        
        // Array de retorno para criação do e-mail
        $array            = array();
        $array['Para']    = "";
        $array['Assunto'] = "Cotação";
        
        /**
         * PDF
         */
        require_once('bibliotecas/tcpdf/config/lang/eng.php');
        require_once('bibliotecas/tcpdf/tcpdf.php');
        
        // create new PDF document
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Hardness Sistemas');
        // remove default header/footer
        $pdf->setPrintHeader(false);
        //$pdf->setPrintFooter(true);
        $pdf->SetMargins(8, 8, 8, 8);
        // set font
        $pdf->SetFont('helvetica', '', 7);
        //set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        // Orientação da página
        $pdf->setPageOrientation('P');
        // add a page
        $pdf->AddPage();
        
        $html = '';
        /**
         * PDF - STYLE
         */
        $html .= <<<EOT
                <style>
                    .header .text {
                        font-size: 25px;
                    }
                    .header .text2 {
                        font-size: 35px;
                        font-weight: bold;
                    }
                    .titulo {
                        background-color: #E1ECF1;
                    }
                    .titulo_superior {
                        font-size: 30px;
                    }
                    .itens td {
                        border: 1px solid black;
                    }
                    .textIncluso {
                        font-size: 18px;
                        color: red;
                        background-color: #E1ECF1;
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
        // (TODO) passar para o arq. conf
        $caminhoImagem = str_replace('201.22.57.137:8282', 'eurosul', $caminhoImagem);
        $caminhoImagem = str_replace('201.22.57.137:8181', 'sigma', $caminhoImagem);
        $html .= <<<EOT
        <table style='height:10px;border:1px solid black;'>
            <tr>
                <td align="left">{$g['c004']['C004_Nome_Empresa']}</td>
                <td align="right" style="font-size: 230%;"><b>Cotação</b></td>
            </tr>
        </table>
EOT;
        
        /**
         * PDF - TABELA PRODUTOS
         */
        
        if ($C007['C007_Flag_Vendedor'] == 'D' OR $C007['C007_Flag_Vendedor'] == 'S') {
            $html .= <<<EOT
            <div style="height:20px; width:100%;"></div>
                    <table class="itens" cellspacing="0" cellpadding="2">
                        <tr class="titulo">
                            <td style="width: 7%"; align="center";>Data</td>
                            <td style="width: 5%"; align=center>Nº</td>
                            <td style="width: 5%"; align="center">Cód</td>
                            <td style="width: 30%"; align="left">Cliente</td>
                            <td style="width: 10%;"; align="right">Produtos R$ </td>
                            <td style="width: 10%"; align="right">Serviços R$ </td>
                            <td style="width: 10%; "; align="right">Trocas R$ </td>
                            <td style="width: 10%"; align="right">Valor R$ </td>
                            <td style="width: 13%"; align="center">Vendedor</td>
                        </tr>
                    </table>
EOT;
        } else {
            $html .= <<<EOT
            <div style="height:20px; width:100%;"></div>
                    <table class="itens" cellspacing="0" cellpadding="2">
                        <tr class="titulo">
                            <td style="width: 7%"; align="center">Data</td>
                            <td style="width: 5%"; align=center>Nº</td>
                            <td style="width: 8%"; align="center">Cód</td>
                            <td style="width: 33%"; align="left">Cliente</td>
                            <td style="width: 12%"; align="right">Produtos R$ </td>
                            <td style="width: 12%"; align="right">Serviços R$ </td>
                            <td style="width: 10%"; align="right">Trocas R$ </td>
                            <td style="width: 12%"; align="right">Valor R$ </td>
                        </tr>
                    </table>
EOT;
        }
        
        
        $html .= <<<EOT
        <table class="itens" cellspacing="0" cellpadding="2">
EOT;
        while ($rowT218 = mysql_fetch_array($resultT218)) {
            ;
            $rowT218['T218_Valor_Total_Produtos'] = gCorrigeNumero($rowT218['T218_Valor_Total_Produtos']);
            $rowT218['T218_Valor_Total_Servicos'] = gCorrigeNumero($rowT218['T218_Valor_Total_Servicos']);
            $rowT218['T218_Valor_Troca']          = gCorrigeNumero($rowT218['T218_Valor_Troca']);
            $rowT218['T218_Valor_Total']          = gCorrigeNumero($rowT218['T218_Valor_Total']);
            $rowT218['T218_Data_Emissao']         = gCorrigeData($rowT218['T218_Data_Emissao']);
            $rowT218['D024_Nome_Empresa']         = gTruncar($rowT218['D024_Nome_Empresa'], 27, " ");
            $nomeCliente                          = $rowT218['D024_Nome_Empresa'];
            if ($rowT218['T218_Flag_Status_Orcamento'] == "A" or $rowT218['T218_Flag_Status_Orcamento'] == 'F') {
                $corLinha = "color:#A1A1A1;";
                $sqlT224  = "SELECT T224_Id FROM T224 WHERE T224_T218_Id = '{$rowT218['T218_Id']}';";
                $resT224  = mysql_query($sqlT224);
                $rowT224  = mysql_fetch_assoc($resT224);
                if ($rowT224['T224_Id'] > 0) {
                    $nomeCliente = "{$rowT218['D024_Nome_Empresa']} ({$rowT224['T224_Id']})";
                }
                
            }
            
            if ($C007['C007_Flag_Vendedor'] == 'D' OR $C007['C007_Flag_Vendedor'] == 'S') {
                $html .= <<<EOT
            
                <tr>
                    <td style="width: 7%"; align="left">{$rowT218['T218_Data_Emissao']}</td>
                    <td style="width: 5%"; align="center">{$rowT218['T218_Id']}</td>
                    <td style="width: 5%"; align="center">{$rowT218['D024_Id']}</td>
                    <td style="width: 30%"; align="left">{$nomeCliente}</td>
                    <td style="width: 10%"; align="right">{$rowT218['T218_Valor_Total_Produtos']}</td>
                    <td style="width: 10%"; align="right">{$rowT218['T218_Valor_Total_Servicos']}</td>
                    <td style="width: 10%"; align="right">{$rowT218['T218_Valor_Troca']}</td>
                    <td style="width: 10%"; align="right">{$rowT218['T218_Valor_Total']}</td>
                    <td style="width: 13%"; align="center">{$rowT218['C007_Primeiro_Nome']}</td>
                </tr>
                
EOT;
            } else {
                $html .= <<<EOT
                <tr>
                    <td style="width: 7%"; align="left">{$rowT218['T218_Data_Emissao']}</td>
                    <td style="width: 5%"; align="center">{$rowT218['T218_Id']}</td>
                    <td style="width: 8%"; align="center">{$rowT218['D024_Id']}</td>
                    <td style="width: 33%"; align="left">{$nomeCliente}</td>
                    <td style="width: 12%"; align="right">{$rowT218['T218_Valor_Total_Produtos']}</td>
                    <td style="width: 12%"; align="right">{$rowT218['T218_Valor_Total_Servicos']}</td>
                    <td style="width: 10%"; align="right">{$rowT218['T218_Valor_Troca']}</td>
                    <td style="width: 12%"; align="right">{$rowT218['T218_Valor_Total']}</td>
                </tr>           
EOT;
                
            }
        }
        $html .= '</table>';
        
        
        /**
         * PDF - GERA!
         */
        $pdf->writeHTML($html, true, false, true, false, 'C');
        // reset pointer to the last page
        $pdf->lastPage();
        // get data
        $data = $pdf->getPDFData();
        
        // Gravar arquivo no disco
        $anexoDir = $g['pathDados'] . 'tmp/';
        is_dir($anexoDir) or mkdir($anexoDir, 0777, true);
        $anexoNome = "{$anexoDir}orcamentoLista.pdf";
        @unlink($anexoNome);
        $h = fopen($anexoNome, 'w');
        fwrite($h, $data);
        fclose($h);
        // Adiciona como anexo
        $array['Anexos']    = array(
            $anexoNome
        );
        $array['AnexosWeb'] = array(
            '/hardness3/' . $anexoNome
        );
        // Logo         
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
        } else {
            $caminhoImagem = "";
        }
        // (TODO) passar para o arq. conf
        $caminhoImagem  = str_replace('201.22.57.137:8282', 'eurosul', $caminhoImagem);
        $caminhoImagem  = str_replace('201.22.57.137:8181', 'sigma', $caminhoImagem);
        // Variaveis enviadas para o modulo de e-mails (que serão gravadas na tabela T139)
        $array['Corpo'] = "<img src='{$caminhoImagem}' style='margin-left:10px'><br /><br /><br /><br /><font style='font-size:16px'>Lista de cotações em anexo.</font><br /><br /><br />";
        
        return $array;
    }
    
    /**
     * pagamento
     *
     * @param Form $form
     * @param int $T218_Id
     */
    public function pagamento($form, $T218_Id)
    {
        $Total_Parcelas = $form->campoValorEnviado('Total_Parcelas');
        $IntervalorDias = $form->campoValorEnviado('IntervalorDias');
        $D031_Id        = $form->campoValorEnviado('Portador_D031_Id');
        
        
        $cSql_T218 = mysql_query("select * from T218 where T218_Id='{$T218_Id}'");
        $mT218     = mysql_fetch_array($cSql_T218);
        
        /* campo T007_T218_Id não existe na tabela T007 (copiado do sistema antigo
         * 
         * ) 
         $sql=mysql_query("select T007_Id from T007 where T007_T218_Id='{$T218_Id}' and T007_Flag_Cancelada!='S'");
         if (mysql_num_rows($sql)>0) {
         return 'Nota Fiscal jÃ¡ foi impressa, pedido não pode ser alterado';
         }
         */
        
        //-- PEGA O VALOR TOTAL A VISTA
        $cSql_T220           = mysql_query("select SUM(T220_Valor_Parcela) from T220 left join D031 on D031_Id=T220_D031_Id where T220_T218_Id='{$T218_Id}'");
        $mT220_Total_A_Vista = mysql_fetch_array($cSql_T220);
        
        
        $cSql_T220 = mysql_query("select * from T220 left join D031 on D031_Id=T220_D031_Id where T220_T218_Id='{$T218_Id}'");
        while ($mT220 = mysql_fetch_array($cSql_T220)) {
            $mPrazoAntigo[$mT220['T220_Numero_Parcela']]    = $mT220['T220_Prazos_Dias'];
            $mPortadorAntigo[$mT220['T220_Numero_Parcela']] = $mT220['T220_D031_Id'];
        }
        
        mysql_query("delete T220 from T220 left join D031 on D031_Id=T220_D031_Id where T220_T218_Id='{$T218_Id}'");
        
        
        $Valor_Total_IPI       = $mT218['T218_Valor_Total_IPI'];
        $Valor_Total_Orcamento = $mT218['T218_Valor_Total'] - $mT220_Total_A_Vista[0];
        
        if ($Total_Parcelas > 0) {
            $Valor_Titulo_Parcela       = ($Valor_Total_Orcamento - $Valor_Total_IPI) / $Total_Parcelas;
            $Valor_Total_Titulo         = ((round($Valor_Titulo_Parcela, 2) * $Total_Parcelas) + $Valor_Total_IPI);
            $Valor_Total_Titulo         = round($Valor_Total_Titulo, 2);
            $Diferenca_Primeira_Parcela = 0; // criado pra evitar erro de undefined no for
            if ($Valor_Total_Titulo != $Valor_Total_Orcamento) {
                $Diferenca_Primeira_Parcela = $Valor_Total_Orcamento - $Valor_Total_Titulo;
            }
        }
        
        
        for ($x = 1; $x <= $Total_Parcelas; $x++) {
            if ($x == 1) {
                $Valor_Titulo = ($Valor_Titulo_Parcela + $Valor_Total_IPI) + $Diferenca_Primeira_Parcela;
            } else {
                $Valor_Titulo = $Valor_Titulo_Parcela;
            }
            if ($D031_Id > 0) {
                $mPortadorAntigo[$x] = $D031_Id;
            }
            //if (empty($mPrazoAntigo[$x])) {
            $mPrazoAntigo[$x] = ($IntervalorDias * $x);
            //}
            $cClausula  = "insert into T220 (";
            $cClausula .= "T220_T218_Id,";
            $cClausula .= "T220_D031_Id,";
            $cClausula .= "T220_Numero_Parcela,";
            $cClausula .= "T220_Prazos_Dias,";
            $cClausula .= "T220_Data_Vencimento,";
            $cClausula .= "T220_Valor_Parcela";
            $cClausula .= ") values (";
            $cClausula .= "'$T218_Id',";
            $cClausula .= "'$mPortadorAntigo[$x]',";
            $cClausula .= "'$x',";
            $cClausula .= "'$mPrazoAntigo[$x]',";
            $cClausula .= "DATE_ADD(current_date(),INTERVAL $mPrazoAntigo[$x] DAY),";
            $cClausula .= "'$Valor_Titulo'";
            $cClausula .= ")";
            mysql_query($cClausula);
            
            if (mysql_error()) {
                return 'Erro: ' . mysql_error();
            }
        }
        return true;
    }
    
    /**
     * insereServico
     *
     * @param Form $form
     * @param int $T223_Id
     */
    public function insereServico($form, $T223_Id)
    {
        
        $D112_Id = $form->campoValorEnviado('T223_D112_Id');
        // baixa de tÃ­tulos
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        
        $sql   = "SELECT * FROM D113 WHERE D113_D112_Id='{$D112_Id}'";
        $array = mysql_query($sql);
        
        while ($row = mysql_fetch_assoc($array)) {
            // D113_D112_Id, D113_D110_Id, D113_Titulo, D113_Descricao
            $sql = "INSERT INTO T146 (T146_T223_Id, T146_Titulo, T146_Descricao) VALUES ('" . mysql_real_escape_string($T223_Id) . "', '"
            //.mysql_real_escape_string($row['D113_D110_Id'])."', '"
                . mysql_real_escape_string($row['D113_Titulo']) . "', '" . mysql_real_escape_string($row['D113_Descricao']) . "');";
            mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return 'Erro: ' . mysql_error();
            }
        }
        
        $sql   = "SELECT * FROM D114 WHERE D114_D112_Id='{$D112_Id}'";
        $array = mysql_query($sql);
        
        while ($row = mysql_fetch_assoc($array)) {
            $sql = "INSERT INTO T152 (T152_D037_Id, T152_D009_Id, T152_T223_Id, T152_Quantidade) VALUES ('" . mysql_real_escape_string($row['D114_D037_Id']) . "', '" . mysql_real_escape_string($row['D114_D009_Id']) . "', '" . mysql_real_escape_string($T223_Id) . "', '" . mysql_real_escape_string($row['D114_Quantidade']) . "');";
            mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return 'Erro: ' . mysql_error();
            }
        }
        
        mysql_query("UPDATE T223 SET T223_Valor_Total = (T223_Valor_Unitario*T223_Quantidade) WHERE T223_Id=$T223_Id");
        
        mysql_query("COMMIT");
        return true;
    }
    
    /**
     * gerarPedido
     *
     * @param int $T218_Id
     */
    public function gerarPedido($T218_Id, $listaT218 = '', $grupo = false)
    {
        global $g, $T224_Id;
        $T218_Id = (isset($T218_Id)) ? $T218_Id : false;
        
        //$T218_D024_Id;
        //$T218_Data_Emissao;
        // inserir um novo registro na T224 levando: T224_T218_Id, e os dois acima
        if ($T218_Id) {
            if($listaT218 != ''){
                $sqlD024 = mysql_query("SELECT DISTINCT T218_D024_Id FROM T218_Id IN ({$listaT218})");
                if(mysql_num_rows($sqlD024) > 1){
                    return "Erro ao gerar Ordem de Compra, por favor, contate o suporte técnico.";
                }
            }

            mysql_query("SET AUTOCOMMIT=0");
            mysql_query("START TRANSACTION");

            //Verifica se a cotação está vinculada a uma venda, caso esteja, necessita que o pedido esteja gerado
            $extraWhere = ($listaT218 != '') ? "(T219_T218_Id ='{$T218_Id}' OR T219_T218_Id IN ({$listaT218}))" : "T219_T218_Id ='{$T218_Id}'";
            $sqlVenda   = mysql_query("SELECT T219_Codigo_Produto, T219_T218_Id, IFNULL(T219_T006_Id,0) AS T219_T006_Id, IFNULL(T219_T004_Id,0) AS T219_T004_Id, IFNULL(T006_T219_Id,0) AS T006_T219_Id FROM T219 LEFT JOIN T006 ON T006_Id=T219_T006_Id WHERE T219_Flag_Comprar='S' AND {$extraWhere}");
            while($resVenda = mysql_fetch_array($sqlVenda)){
                if($resVenda['T219_T004_Id'] > 0 && ($resVenda['T219_T006_Id'] <= 0 || $resVenda['T006_T219_Id'] <= 0)){
                    return "A cotação está vinculada a um orçamento, é necessário a geração do pedido para gerar a ordem de compra (Item: {$resVenda['T219_Codigo_Produto']} - Cotação: {$resVenda['T219_T218_Id']}) ";
                }
            }

            $extraWhere = ($listaT218 != '') ? "(T219_T218_Id ='{$T218_Id}' OR T219_T218_Id IN ({$listaT218}))" : "T219_T218_Id ='{$T218_Id}'";
            $sqlPreCadastro  = mysql_query("SELECT T219_Id 
                                       FROM T219 
                                  LEFT JOIN T055 on T055_D009_Id=T219_D009_Id AND T055_T075_Id=T219_T075_Id
                                  LEFT JOIN D009 on D009_Id=T219_D009_Id 
                                  LEFT JOIN D049 on D049_Id=D009_D049_Id
                                  LEFT JOIN D001 on D001_Id=D049_D001_Id
                                  LEFT JOIN T225 on T225_T219_Id=T219_Id 
                                      WHERE T219_Flag_Comprar='S'
                                        AND D001_Flag_Pre_Cadastro='S'
                                        AND {$extraWhere}");
            $rowsPreCadastro = mysql_num_rows($sqlPreCadastro);

            if ($rowsPreCadastro > 0 && $g['C031']['permiteOrdemCompraProdutoPreCadastro'] == 'N') {
                return "Não é possível gerar OC para itens em pré-cadastro";
            }

            //Verifica se há itens da cotação sem CFOP, NCM ou com origem em branco
            $retornoRestricao = $this->verificaDadosCotacao($extraWhere . " AND T219_Flag_Comprar='S'");
            if($retornoRestricao !== true){
                return $retornoRestricao;
            }

            $sql = "SELECT * FROM T218 LEFT JOIN D006 on D006_Id=T218_D006_Id LEFT JOIN D024 ON D024_Id=T218_D024_Id LEFT JOIN D020 ON D020_Id=D024_D020_Id WHERE T218_Id='$T218_Id'";
            $array = mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return '1 Erro: ' . mysql_error();
            }
            
            $orcamento = mysql_fetch_assoc($array);
            
            if ($orcamento['D024_Flag_Pessoa_Fisica_Juridica'] == "J") {
                if (empty($orcamento['D024_Cnpj'])) {
                    return "CNPJ da empresa em branco";
                }
            } else if ($orcamento['D024_Flag_Pessoa_Fisica_Juridica'] == "F") {
                if (empty($orcamento['D024_Cpf'])) {
                    return "Cpf do cliente em branco";
                }
            }


            if($grupo === false){
                $sqlT224 = mysql_query("SELECT T224_Grupo FROM T224 ORDER BY T224_Grupo DESC LIMIT 1");
                $resT224 = mysql_fetch_array($sqlT224);
                $grupo = $resT224['T224_Grupo'] + 1;
            }
            
            //Variaveis padrão para tipo de transporte e motorista da coleta
            $conf_T224_D109_Id = $g['conf']->camposValorPadrao('T224_D109_Id');
            $conf_T224_Flag_Transporte_Coleta = $g['conf']->camposValorPadrao('T224_Flag_Transporte_Coleta');

            $T224_D109_Id = isset($conf_T224_D109_Id) ? $conf_T224_D109_Id : '';
            $T224_Flag_Transporte_Coleta = isset($conf_T224_Flag_Transporte_Coleta) ? $conf_T224_Flag_Transporte_Coleta : '';
            
            $sql     = "INSERT INTO T224 (T224_T218_Id,
                                      T224_D013_Id, 
                                      T224_D024_Id, 
                                      T224_D021_Id, 
                                      T224_C004_Id, 
                                      T224_D006_Id, 
                                      T224_C007_Id_Vendedor_Interno, 
                                      T224_Observacao,
                                      T224_Observacao_Nota_Fiscal, 
                                      T224_Flag_ACP, 
                                      T224_Flag_Status, 
                                      T224_Data_Emissao,
                                      T224_Flag_Situacao,
                                      T224_Flag_Revenda_Consumidor,
                                      T224_D022_Id,
                                      T224_D022_Id_Default,                                   
                                      T224_Observacao_Pagamento,
                                      T224_Pgto_Parcelas,
                                      T224_Pgto_Primeiro_Vcto_Tipo,
                                      T224_Pgto_Primeiro_Vcto_Valor,
                                      T224_Pgto_Dias_Vcto_Tipo,
                                      T224_Pgto_Dias_Vcto_Valor,
                                      T224_Pgto_Portador,
                                      T224_Pgto_Data,
                                      T224_Pgto_C007_Id,
                                      T224_D116_Id,
                                      T224_Liberou_Alcada_C007_Id,
                                      T224_Liberou_Alcada_Data,
                                      T224_Liberou_Alcada_IPV,
                                      T224_Liberou_Alcada_Valor_Total,
                                      T224_Liberou_Alcada_Prazo_Medio,
                                      T224_Liberou_Alcada_Desconto,
                                      T224_ST_ICMS_Interno,
                                      T224_ICMS_Inter_Estadual,
                                      T224_Flag_Frete,
                                      T224_Flag_Lista_Preco,
                                      T224_Valor_Frete,
                                      T224_Flag_Expedicao,
                                      T224_Percentual_Comissao,
                                      T224_Valor_Desconto,
                                      T224_Valor_Desconto_Manual,
                                      T224_Flag_Almoxarifado,
                                      T224_Total_Cubagem,
                                      T224_Peso_Total,
                                      T224_Peso_Bruto_Total,
                                      T224_Valor_Total_Trib_Transparencia,
                                      T224_Flag_Status_Liberacao,
                                      T224_Prazo_Entrega,
                                      T224_Prazos_Pagamentos,
                                      T224_Grupo,
                                      T224_Flag_Calcular_ST,
                                      T224_Flag_Calcular_IPI,
                                      T224_Flag_Calcular_ICMS,
                                      T224_D109_Id,
                                      T224_Flag_Transporte_Coleta,
                                      T224_Flag_Destino_Produto
                                      ) VALUES (
                                       '" . mysql_real_escape_string($T218_Id) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_D013_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_D024_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_D021_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_C004_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_D006_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_C007_Id_Vendedor_Interno']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Observacao']) . "',
                                       '" . mysql_real_escape_string($orcamento['D006_Observacao']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Flag_Acp']) . "',
                                       '0',
                                       CURDATE(),
                                       '1',
                                       '" . mysql_real_escape_string($orcamento['D024_Flag_Revenda_Consumidor']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_D022_Id']) . "',                                     
                                       '" . mysql_real_escape_string($orcamento['D020_D022_Id']) . "',                                     
                                       '" . mysql_real_escape_string($orcamento['T218_Observacao_Pagamento']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Pgto_Parcelas']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Pgto_Primeiro_Vcto_Tipo']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Pgto_Primeiro_Vcto_Valor']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Pgto_Dias_Vcto_Tipo']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Pgto_Dias_Vcto_Valor']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Pgto_Portador']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Pgto_Data']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Pgto_C007_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_D116_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Liberou_Alcada_C007_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Liberou_Alcada_Data']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Liberou_Alcada_IPV']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Liberou_Alcada_Valor_Total']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Liberou_Alcada_Prazo_Medio']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Liberou_Alcada_Desconto']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_ST_ICMS_Interno']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_ICMS_Inter_Estadual']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Flag_Frete']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Flag_Lista_Preco']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Valor_Frete']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Flag_Expedicao']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Percentual_Comissao']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Valor_Desconto']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Valor_Desconto_Manual']) . "',
                                       'N',
                                       '" . mysql_real_escape_string($orcamento['T218_Total_Cubagem']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Peso_Total']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Peso_Bruto_Total']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Valor_Total_Trib_Transparencia']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Flag_Status_Liberacao']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Prazo_Entrega']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Forma_Pagamento']) . "',
                                       '" . mysql_real_escape_string($grupo) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Flag_Calcular_ST']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Flag_Calcular_IPI']) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Flag_Calcular_ICMS']) . "',
                                       '" . mysql_real_escape_string($T224_D109_Id) . "',
                                       '" . mysql_real_escape_string($T224_Flag_Transporte_Coleta) . "',
                                       '" . mysql_real_escape_string($orcamento['T218_Flag_Destino_Produto']) . "'
                                       );";
            $retorno = mysql_query($sql);
            $erro    = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return '2 Erro: ' . $erro;
            }
            $T224_Id = $g['mysqlLastId'];
            //Insere um registro na tabela estendida 
            mysql_query("INSERT INTO T224A(T224A_T224_Id, T224A_Hora_Emissao) VALUES ('{$T224_Id}', CURTIME())");
            //Insere registros no histórico do pedido (T228)
            mysql_query("INSERT INTO T228(T228_T224_Id,T228_C007_Id,T228_Data,T228_Descricao,T228_Observacao) VALUES ({$T224_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Pedido Gerado','')");
            //Define o Orçamento como Fechado
            $extraWhere = ($listaT218 != '') ? "(T218_Id ='{$T218_Id}' OR T218_Id IN ({$listaT218}))" : "T218_Id ='{$T218_Id}'";
            mysql_query("UPDATE T218 SET T218_Flag_Perdido='F' WHERE {$extraWhere}");
            
            //selecionar da T220 e inserir na T226 (pagamento)
            $extraWhere = ($listaT218 != '') ? "(T220_T218_Id ='{$T218_Id}' OR T220_T218_Id IN ({$listaT218}))" : "T220_T218_Id ='{$T218_Id}'";
            $query7  = "SELECT * FROM T220 WHERE {$extraWhere}";
            $result9 = mysql_query($query7);
            $prazos = '';
            while ($row = mysql_fetch_array($result9)) {
                $sql90 = <<<EOT
                INSERT INTO T226 (T226_T224_Id, T226_D031_Id, T226_Numero_Parcela, T226_Prazos_Dias, T226_Data_Vencimento, T226_Valor_Parcela) 
                VALUES
                ('{$T224_Id}', '{$row['T220_D031_Id']}', '{$row['T220_Numero_Parcela']}', '{$row['T220_Prazos_Dias']}', '{$row['T220_Data_Vencimento']}', '{$row['T220_Valor_Parcela']}')
EOT;
                $retorno .= $sql90 . "<br />";
                $result = mysql_query($sql90);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return '22: Erro Parcelas: ' . mysql_error();
                }
                $prazos .= $row['T220_Prazos_Dias'].'/';
            }
            if($prazos != ''){
                $prazos = substr($prazos, 0, -1);
                
                //Atualiza o campo T224_Prazos_Pagamentos
                mysql_query("UPDATE T224 SET T224_Prazos_Pagamentos = '{$prazos}' WHERE T224_Id = '{$T224_Id}'");
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return 'Erro Forma de Pagamento: ' . mysql_error();
                }
            }

            
            //Insere os documentos do orçamento no pedido.
            $extraWhere = ($listaT218 != '') ? "(T221_T218_Id ='{$T218_Id}' OR T221_T218_Id IN ({$listaT218}))" : "T221_T218_Id ='{$T218_Id}'";
            $sqlDocumentos    = "SELECT * FROM T221 WHERE {$extraWhere}";
            $resultDocumentos = mysql_query($sqlDocumentos);
            while ($linhaDocumentos = mysql_fetch_array($resultDocumentos)) {
                $sql            = <<<EOT
                INSERT INTO T227 
                (T227_T224_Id, T227_C007_Id, T227_Nome_Arquivo, T227_Descricao, T227_Data_Hora_Upload)
                VALUES
                ('{$T224_Id}','{$linhaDocumentos['T221_C007_Id']}','{$linhaDocumentos['T221_Nome_Arquivo']}','{$linhaDocumentos['T221_Descricao']}','{$linhaDocumentos['T221_Data_Hora_Upload']}')
EOT;
                $resultCopiaDoc = mysql_query($sql);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return '19 Erro: ' . mysql_error();
                }
                
                //----Copia o arquivo da pasta de orçamentos para pasta de pedidos
                //Pega a extensão do arquivo
                $pathinfo              = pathinfo($linhaDocumentos['T221_Nome_Arquivo']);
                $pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
                
                //Cotacao salva na pasta cotacaoNovo e estava pegando os documentos do orçamento
                //$dirOrigem = "{$g['pathDados']}orcamento";
                //Pega o caminho do arquivo no orçamento
                $dirOrigem = "{$g['pathDados']}cotacaoNovo";
                is_dir($dirOrigem) or mkdir($dirOrigem, 0777, true);
                $fileOrigem = $dirOrigem . '/' . $linhaDocumentos['T221_Id'] . '.' . $pathinfo['extension'];
                
                $dirDestino = "{$g['pathDados']}ordemCompra";
                is_dir($dirDestino) or mkdir($dirDestino, 0777, true);
                $fileDestino = $dirDestino . '/' . $g['mysqlLastId'] . '.' . $pathinfo['extension'];
                copy($fileOrigem, $fileDestino);
            }
            
            // neste momento insere apenas os serviços (T223) que nao estão associados a produtos (avulsos)
            $extraWhere = ($listaT218 != '') ? "(T223_T218_Id ='{$T218_Id}' OR T223_T218_Id IN ({$listaT218}))" : "T223_T218_Id ='{$T218_Id}'";
            $sql    = "SELECT * FROM T223 WHERE T223_T219_Id IS NULL AND {$extraWhere}";
            $result = mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return '3 Erro: ' . mysql_error();
            }
            
            
            while ($orcamentoSer = mysql_fetch_array($result)) {
                
                $sql     = "INSERT INTO T230 (T230_T224_Id, 
                                              T230_T223_Id, 
                                              T230_D110_Id,
                                              T230_D112_Id,
                                              T230_Titulo, 
                                              T230_Descricao, 
                                              T230_Tempo_Estimado, 
                                              T230_Quantidade,
                                              T230_Valor_Unitario,
                                              T230_Valor_Total,
                                              T230_Hora_Inicio,
                                              T230_Hora_Fim,
                                              T230_Status,
                                              T230_D116_Id) 
                                              VALUES 
                                              ('" . mysql_real_escape_string($T224_Id) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T223_Id']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T223_D110_Id']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T223_D112_Id']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T223_Titulo']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T223_Descricao']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T223_Tempo_Estimado']) . "',
                                                                     '1',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T223_Valor_Unitario']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T223_Valor_Unitario']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T223_Hora_Inicio']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T223_Hora_Fim']) . "',
                                                                     'P',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T223_D116_Id']) . "'
                                                                     );";
                $retorno = mysql_query($sql);
                //return $sql;               
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return '4 Erro: ' . mysql_error();
                }
                $T230_Id = $g['mysqlLastId'];
                
                //selecionar todos da T146 onde T146_T223_Id = T223_Id
                //mais um looping para cada T146 inserir um registro na T148
                
                $sql     = "SELECT * FROM T146 WHERE T146_T223_Id=" . $orcamentoSer['T223_Id'];
                $result2 = mysql_query($sql);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return '5 Erro: ' . mysql_error();
                }
                
                while ($processo = mysql_fetch_assoc($result2)) {
                    $sql     = "INSERT INTO T148 (T148_Id,
                                                  T148_T230_Id,                                               
                                                  T148_Titulo,
                                                  T148_Descricao)
                                                  VALUES
                                                  ('',
                                                   '" . mysql_real_escape_string($T230_Id) . "',                                                   
                                                   '" . mysql_real_escape_string($processo['T146_Titulo']) . "',
                                                   '" . mysql_real_escape_string($processo['T146_Descricao']) . "'
                                                   );";
                    $retorno = mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return '11 Erro: ' . mysql_error();
                    }
                }
                $sql     = "SELECT * FROM T152 WHERE T152_T223_Id=" . $orcamentoSer['T223_Id'];
                $result3 = mysql_query($sql);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return '5 Erro: ' . mysql_error();
                }
                
                while ($material = mysql_fetch_assoc($result3)) {
                    $sql = "INSERT INTO T149(T149_Id,
                                                T149_D009_Id,
                                                T149_D037_Id,
                                                T149_T230_Id,
                                                T149_Quantidade)
                                                VALUES
                                                ('',
                                                 '" . mysql_real_escape_string($material['T152_D009_Id']) . "',
                                                 '" . mysql_real_escape_string($material['T152_D037_Id']) . "',
                                                 '" . mysql_real_escape_string($T230_Id) . "',
                                                 '" . mysql_real_escape_string($material['T152_Quantidade']) . "'
                                                 );";
                    
                    $retorno = mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return '7 Erro: ' . mysql_error();
                    }
                }
                
            }
            
            //selecionar todos da T219 onde T219_T218_Id = $T218
            //inserir na T225 todos da T219 selecionados
            $extraWhere = ($listaT218 != '') ? "(T219_T218_Id ='{$T218_Id}' OR T219_T218_Id IN ({$listaT218}))" : "T219_T218_Id ='{$T218_Id}'";
            $sql             = <<<EOT
            SELECT * FROM T219 
            LEFT JOIN T225 ON T225_T219_Id=T219_Id 
            LEFT JOIN D009 ON D009_Id=T219_D009_Id
            LEFT JOIN D005 on D005_Id=T219_D005_Id
            LEFT JOIN T219A ON T219_Id=T219A_T219_Id
            WHERE T219_Quantidade>0 and T219_Flag_Comprar='S' AND {$extraWhere};
EOT;
            $retornoConsulta = mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return '8 Erro: ' . mysql_error();
            }
            
            if (mysql_num_rows($retornoConsulta) <= 0) {
                //mysql_query("ROLLBACK");
                // return 'Não existem ítens para gerar pedido.';
                // O produto pode ter apenas serviços. Regra retirada
            }
            
            // variavel que soma quantos itens do orçamento foram inseridos no pedido
            $countConfirmados = 0;
            $restricaoProduto = '';
            while ($linha = mysql_fetch_assoc($retornoConsulta)) {
                
                /*              // seleciona todos os pedidos gerados a partir do orçamento atual  
                $sql = "SELECT * FROM T224 WHERE T224_T218_Id='$T218_Id';";
                $ret = mysql_query($sql);
                // variavel que verifica se o item do orçamento atual jÃ¡ estÃ¡ em algum pedido ja gerado deste orçamento
                $incluir=true;
                // para cada pedido jÃ¡ gerado a partir do orçamento atual
                while($reg = mysql_fetch_assoc($ret)){
                // seleciona os itens deste pedido
                // e verifica se algum destes itens é igual ao item atual
                $sql1 = "SELECT * FROM T225 WHERE T225_T224_Id='{$reg['T224_Id']}';";                   
                $ret1 = mysql_query($sql1);
                $item=mysql_fetch_assoc($ret1);
                if (!empty($item['T225_D009_Id'])){                     
                if ($item['T225_T219_Id']==$linha['T219_Id']){
                $incluir=false; 
                }
                }
                }
                */
                if($g['C031']['permiteOrdemCompraProdutoPreCadastro'] == 'N'){
                    //Verifica se existe algum produto pré cadastro
                    $sqlRestricao     = "SELECT T219_Codigo_Produto,D001_Flag_Pre_Cadastro FROM T219 LEFT JOIN D009 ON D009_Id=T219_D009_Id LEFT JOIN D001 ON D001_Id=D009_D001_Id WHERE T219_Quantidade_Confirmada>0 and T219_Id={$linha['T219_Id']}";
                    $retornoRestricao = mysql_query($sqlRestricao);
                    $rowRestricao     = mysql_fetch_array($retornoRestricao);
                    if ($rowRestricao['D001_Flag_Pre_Cadastro'] == 'S') {
                        $restricaoProduto .= "O produto " . $rowRestricao['T219_Codigo_Produto'] . " é pré-cadastro.<br />";
                    }
                }
                
                $linha['T219_Valor_Preco_Original'] = ($linha['T219_Valor_Preco_Original'] > 0 ? $linha['T219_Valor_Preco_Original'] : $linha['T219_Valor_Preco_Sem_Desconto_Unitario']);
                $countConfirmados++;
                $sql  = "INSERT INTO T225 (T225_Codigo_Substituto,
                                          T225_Flag_Status,
                                          T225_Item,
                                          T225_T219_Id,
                                          T225_D037_Id,
                                          T225_D005_Id,
                                          T225_D006_Id,
                                          T225_Classificacao_Fiscal,
                                          T225_D009_Id,
                                          T225_Codigo_Produto,
                                          T225_Marca,
                                          T225_Descricao_Produto,
                                          T225_Quantidade,
                                          T225_Valor_Preco_Bruto,
                                          T225_Valor_Custo_Unitario,
                                          T225_Valor_Preco_Unitario,
                                          T225_Valor_Preco_Sem_Desconto_Unitario,
                                          T225_Valor_Preco_Original_Bruto,
                                          T225_Percentual_Desconto_Bruto,
                                          T225_IPV,
                                          T225_T224_Id,
                                          T225_C004_Id,
                                          T225_Numero_Pedido_Compra,
                                          T225_Item_Pedido_Compra,
                                          T225_Aliquota_IPI,
                                          T225_Aliquota_ICMS,
                                          T225_Percentual_Reducao_ICMS,
                                          T225_Percentual_Reducao_ICMS_ST,
                                          T225_ST_VA,
                                          T225_Flag_ST,
                                          T225_Flag_Situacao_Tributaria,
                                          T225_ICMS_Inter_Estadual,
                                          T225_ST_ICMS_Interno,
                                          T225_Flag_Isento_PIS,
                                          T225_Aliquota_PIS,
                                          T225_Situacao_Tributaria_PIS,
                                          T225_Flag_Isento_COFINS,
                                          T225_Aliquota_COFINS,
                                          T225_Flag_Preco_Especial,
                                          T225_Situacao_Tributaria_COFINS,
                                          T225_Situacao_Tributaria_IPI,
                                          T225_D059_Id,
                                          T225_Valor_Preco_Original,
                                          T225_Percentual_Desconto,
                                          T225_Liberou_Alcada_Saldo_Futuro,
                                          T225_Quantidade_Estoque,
                                          T225_T075_Id,
                                          T225_T206_Id,
                                          T225_Cubagem_Unitaria,
                                          T225_Total_Cubagem,
                                          T225_Peso_Bruto,
                                          T225_Peso_Bruto_Total,
                                          T225_Peso_Unitario,
                                          T225_Peso_Total,
                                          T225_Valor_Trib_Transparencia,
                                          T225_Percentual_Trib_Transparencia,
                                          T225_Flag_Pre_Cadastro,
                                          T225_D144_Id,
                                          T225_Observacao_Entrega,
                                          T225_Observacao,
                                          T225_Flag_Origem_Produto,
                                          T225_Valor_PReco_Venda_Negociado,
                                          T225_Valor_Preco_Venda,
                                          T225_Percentual_Frete,
                                          T225_Percentual_Financeiro,
                                          T225_D006_Id_Info,
                                          T225_D005_Id_Info,
                                          T225_Valor_ICMS_Substituicao_Retencao,
                                          T225_Percentual_ST,
                                          T225_Flag_Destino_Produto
                                          )
                                      VALUES
                                         ('" . mysql_real_escape_string($linha['T219_Codigo_Substituto']) . "',
                                          '',
                                          '" . mysql_real_escape_string($linha['T219_Item']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Id']) . "',
                                          '" . mysql_real_escape_string($linha['T219_D037_Id']) . "',
                                          '" . mysql_real_escape_string($linha['T219_D005_Id']) . "',
                                          '" . mysql_real_escape_string($linha['T219_D006_Id']) . "',
                                          '" . mysql_real_escape_string($linha['D005_Classificacao_Fiscal']) . "',
                                          '" . mysql_real_escape_string($linha['T219_D009_Id']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Codigo_Produto']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Marca']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Descricao_Produto']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Quantidade']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Valor_Preco_Bruto']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Valor_Custo_Unitario']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Valor_Preco_Unitario']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Valor_Preco_Unitario']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Valor_Preco_Original_Bruto']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Percentual_Desconto_Bruto']) . "',
                                          '" . mysql_real_escape_string($linha['T219_IPV']) . "',
                                          '" . mysql_real_escape_string($T224_Id) . "',
                                          '" . mysql_real_escape_string($linha['T219_C004_Id']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Numero_Pedido_Compra']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Item_Pedido_Compra']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Aliquota_IPI']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Aliquota_ICMS']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Percentual_Reducao_ICMS']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Percentual_Reducao_ICMS_ST']) . "',
                                          '" . mysql_real_escape_string($linha['T219_ST_VA']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Flag_ST']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Flag_Situacao_Tributaria']) . "',
                                          '" . mysql_real_escape_string($linha['T219_ICMS_Inter_Estadual']) . "',
                                          '" . mysql_real_escape_string($linha['T219_ST_ICMS_Interno']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Flag_Isento_PIS']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Aliquota_PIS']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Situacao_Tributaria_PIS']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Flag_Isento_COFINS']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Aliquota_COFINS']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Flag_Preco_Especial']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Situacao_Tributaria_COFINS']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Situacao_Tributaria_IPI']) . "',
                                          '" . mysql_real_escape_string($linha['T219_D059_Id']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Valor_Preco_Original']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Percentual_Desconto']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Liberou_Alcada_Saldo_Futuro']) . "',
                                          '" . mysql_real_escape_string($linha['D009_Quantidade_Estoque_Liquido']) . "',
                                          '" . mysql_real_escape_string($linha['T219_T075_Id']) . "',
                                          '" . mysql_real_escape_string($linha['T219_T206_Id']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Cubagem_Unitaria']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Total_Cubagem']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Peso_Bruto']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Peso_Bruto_Total']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Peso_Unitario']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Peso_Total']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Valor_Trib_Transparencia']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Percentual_Trib_Transparencia']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Flag_Pre_Cadastro']) . "',
                                          '" . mysql_real_escape_string($linha['T219_D144_Id']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Observacao_Entrega']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Observacao']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Flag_Origem_Produto']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Valor_Preco_Venda_Negociado']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Valor_Preco_Venda']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Percentual_Frete']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Percentual_Financeiro']) . "',
                                          '" . mysql_real_escape_string($linha['T219_D006_Id_Info']) . "',
                                          '" . mysql_real_escape_string($linha['T219_D005_Id_Info']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Valor_ST']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Percentual_ST']) . "',
                                          '" . mysql_real_escape_string($linha['T219_Flag_Destino_Produto']) . "'
                                          )";
                $erro = mysql_error();
                //return $sql;
                $ret  = mysql_query($sql);
                log($sql);
                if (!empty($erro)) {
                    mysql_query("ROLLBACK");
                    return '9 Erro: ' . $erro;
                }
                
                $T225_Id = $g['mysqlLastId'];

                /* Snapshot fiscal da cotação gravado junto com o item da OC. */
                $T225A = mysql_query("INSERT INTO T225A (
                                                         T225A_T225_Id,
                                                         T225A_Aliquota_FCP,
                                                         T225A_Valor_Base_FCP,
                                                         T225A_Valor_FCP,
                                                         T225A_Valor_Base_FCP_ST,
                                                         T225A_Valor_FCP_ST,
                                                         T225A_Situacao_Tributaria_CBS_IBS,
                                                         T225A_Classificacao_Tributaria_CBS_IBS,
                                                         T225A_Situacao_Tributaria_CBS_IBS_Regular,
                                                         T225A_Classificacao_Tributaria_CBS_IBS_Regular,
                                                         T225A_Aliquota_CBS,
                                                         T225A_Percentual_Reducao_CBS,
                                                         T225A_Aliquota_CBS_Efetiva,
                                                         T225A_Valor_CBS,
                                                         T225A_Percentual_Diferimento_CBS,
                                                         T225A_Valor_Diferimento_CBS,
                                                         T225A_Aliquota_CBS_Regular,
                                                         T225A_Percentual_Reducao_CBS_Regular,
                                                         T225A_Aliquota_CBS_Efetiva_Regular,
                                                         T225A_Valor_CBS_Regular,
                                                         T225A_Aliquota_IBS_Municipal,
                                                         T225A_Percentual_Reducao_IBS_Municipal,
                                                         T225A_Aliquota_IBS_Municipal_Efetiva,
                                                         T225A_Valor_IBS_Municipal,
                                                         T225A_Percentual_Diferimento_IBS_Municipal,
                                                         T225A_Valor_Diferimento_IBS_Municipal,
                                                         T225A_Aliquota_IBS_Municipal_Regular,
                                                         T225A_Percentual_Reducao_IBS_Municipal_Regular,
                                                         T225A_Aliquota_IBS_Municipal_Efetiva_Regular,
                                                         T225A_Valor_IBS_Municipal_Regular,
                                                         T225A_Aliquota_IBS_UF,
                                                         T225A_Percentual_Reducao_IBS_UF,
                                                         T225A_Aliquota_IBS_UF_Efetiva,
                                                         T225A_Valor_IBS_UF,
                                                         T225A_Percentual_Diferimento_IBS_UF,
                                                         T225A_Valor_Diferimento_IBS_UF,
                                                         T225A_Aliquota_IBS_UF_Regular,
                                                         T225A_Percentual_Reducao_IBS_UF_Regular,
                                                         T225A_Aliquota_IBS_UF_Efetiva_Regular,
                                                         T225A_Valor_IBS_UF_Regular,
                                                         T225A_Valor_Base_CBS_IBS,
                                                         T225A_Valor_Total_Reforma,
                                                         T225A_Valor_IBS)
                                                VALUES (
                                                        '{$T225_Id}',
                                                        '{$linha['T219A_Aliquota_FCP']}',
                                                        '{$linha['T219A_Valor_Base_FCP']}',
                                                        '{$linha['T219A_Valor_FCP']}',
                                                        '{$linha['T219A_Valor_Base_FCP_ST']}',
                                                        '{$linha['T219A_Valor_FCP_ST']}',
                                                        '{$linha['T219A_Situacao_Tributaria_CBS_IBS']}',
                                                        '{$linha['T219A_Classificacao_Tributaria_CBS_IBS']}',
                                                        '{$linha['T219A_Situacao_Tributaria_CBS_IBS_Regular']}',
                                                        '{$linha['T219A_Classificacao_Tributaria_CBS_IBS_Regular']}',
                                                        '{$linha['T219A_Aliquota_CBS']}',
                                                        '{$linha['T219A_Percentual_Reducao_CBS']}',
                                                        '{$linha['T219A_Aliquota_CBS_Efetiva']}',
                                                        '{$linha['T219A_Valor_CBS']}',
                                                        '{$linha['T219A_Percentual_Diferimento_CBS']}',
                                                        '{$linha['T219A_Valor_Diferimento_CBS']}',
                                                        '{$linha['T219A_Aliquota_CBS_Regular']}',
                                                        '{$linha['T219A_Percentual_Reducao_CBS_Regular']}',
                                                        '{$linha['T219A_Aliquota_CBS_Efetiva_Regular']}',
                                                        '{$linha['T219A_Valor_CBS_Regular']}',
                                                        '{$linha['T219A_Aliquota_IBS_Municipal']}',
                                                        '{$linha['T219A_Percentual_Reducao_IBS_Municipal']}',
                                                        '{$linha['T219A_Aliquota_IBS_Municipal_Efetiva']}',
                                                        '{$linha['T219A_Valor_IBS_Municipal']}',
                                                        '{$linha['T219A_Percentual_Diferimento_IBS_Municipal']}',
                                                        '{$linha['T219A_Valor_Diferimento_IBS_Municipal']}',
                                                        '{$linha['T219A_Aliquota_IBS_Municipal_Regular']}',
                                                        '{$linha['T219A_Percentual_Reducao_IBS_Municipal_Regular']}',
                                                        '{$linha['T219A_Aliquota_IBS_Municipal_Efetiva_Regular']}',
                                                        '{$linha['T219A_Valor_IBS_Municipal_Regular']}',
                                                        '{$linha['T219A_Aliquota_IBS_UF']}',
                                                        '{$linha['T219A_Percentual_Reducao_IBS_UF']}',
                                                        '{$linha['T219A_Aliquota_IBS_UF_Efetiva']}',
                                                        '{$linha['T219A_Valor_IBS_UF']}',
                                                        '{$linha['T219A_Percentual_Diferimento_IBS_UF']}',
                                                        '{$linha['T219A_Valor_Diferimento_IBS_UF']}',
                                                        '{$linha['T219A_Aliquota_IBS_UF_Regular']}',
                                                        '{$linha['T219A_Percentual_Reducao_IBS_UF_Regular']}',
                                                        '{$linha['T219A_Aliquota_IBS_UF_Efetiva_Regular']}',
                                                        '{$linha['T219A_Valor_IBS_UF_Regular']}',
                                                        '{$linha['T219A_Valor_Base_CBS_IBS']}',
                                                        '{$linha['T219A_Valor_Total_Reforma']}',
                                                        '{$linha['T219A_Valor_IBS']}')");
                $erro = mysql_error();
                if(!empty($erro)){
                    mysql_query("ROLLBACK");
                    return $erro;
                }

                mysql_query("UPDATE T219 set T219_Flag_Comprar='N' where T219_Id='{$linha['T219_Id']}'");
                mysql_query("CALL T225_Gravar_Totalizacao('$T225_Id')");
                
                log("call T225_Gravar_Totalizacao('$T225_Id')");
                $erro = mysql_error();
                if (!empty($erro)) {
                    log($erro);
                    mysql_query("ROLLBACK");
                    return '10 Erro: ' . $erro;
                }
                
                //SELECT T225_Id, T066_Id FROM T225 LEFT JOIN T066 ON T066_D009_Id=T225_D009_Id WHERE T225_Id = '294670'
                
                // verificar se tem algum T223 com T223_T219_Id igual ao T219_Id que acabou de ser inserido na T225
                // ou seja, insere na T230 os serviços que estão associados ao ítem que acabou de ser inserido 
                
                $sql    = "SELECT * FROM T223 WHERE T223_T218_Id='$T218_Id' AND T223_T219_Id='{$linha['T219_Id']}'";
                $result = mysql_query($sql);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return 'Erro: ' . mysql_error();
                }
                
                while ($orcamentoSer = mysql_fetch_array($result)) {
                    
                    $sql     = "INSERT INTO T230 (T230_T224_Id, 
                                              T230_T223_Id, 
                                              T230_D110_Id,
                                              T230_D112_Id,
                                              T230_Titulo, 
                                              T230_Descricao, 
                                              T230_Tempo_Estimado, 
                                              T230_Quantidade,
                                              T230_Valor_Unitario,
                                              T230_Valor_Total,
                                              T230_Hora_Inicio,
                                              T230_Hora_Fim,
                                              T230_Status,
                                              T230_T225_Id, 
                                              T230_D116_Id)
                                              VALUES 
                                              ('" . mysql_real_escape_string($T224_Id) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T223_Id']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T223_D110_Id']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T223_D112_Id']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T223_Titulo']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T223_Descricao']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T223_Tempo_Estimado']) . "',
                                                                     '1',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T223_Valor_Unitario']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T223_Valor_Unitario']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T223_Hora_Inicio']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T223_Hora_Fim']) . "',
                                                                     'P',
                                                                     '" . mysql_real_escape_string($T225_Id) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T223_D116_Id']) . "'
                                                                     );";
                    $retorno = mysql_query($sql);
                    //return $sql;               
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return '4 Erro: ' . mysql_error();
                    }
                    $T230_Id = $g['mysqlLastId'];
                    
                    //selecionar todos da T146 onde T146_T223_Id = T223_Id
                    //mais um looping para cada T146 inserir um registro na T148
                    
                    $sql     = "SELECT * FROM T146 WHERE T146_T223_Id=" . $orcamentoSer['T223_Id'];
                    $result2 = mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return '5 Erro: ' . mysql_error();
                    }
                    
                    while ($processo = mysql_fetch_assoc($result2)) {
                        $sql     = "INSERT INTO T148 (T148_Id,
                                                  T148_T230_Id,                                               
                                                  T148_Titulo,
                                                  T148_Descricao)
                                                  VALUES
                                                  ('',
                                                   '" . mysql_real_escape_string($T230_Id) . "',                                                   
                                                   '" . mysql_real_escape_string($processo['T146_Titulo']) . "',
                                                   '" . mysql_real_escape_string($processo['T146_Descricao']) . "'
                                                   );";
                        $retorno = mysql_query($sql);
                        if (mysql_error()) {
                            mysql_query("ROLLBACK");
                            return '11 Erro: ' . mysql_error();
                        }
                    }
                    $sql     = "SELECT * FROM T152 WHERE T152_T223_Id=" . $orcamentoSer['T223_Id'];
                    $result3 = mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return '5 Erro: ' . mysql_error();
                    }
                    
                    while ($material = mysql_fetch_assoc($result3)) {
                        $sql = "INSERT INTO T149(T149_Id,
                                                T149_D009_Id,
                                                T149_D037_Id,
                                                T149_T230_Id,
                                                T149_Quantidade)
                                                VALUES
                                                ('',
                                                 '" . mysql_real_escape_string($material['T152_D009_Id']) . "',
                                                 '" . mysql_real_escape_string($material['T152_D037_Id']) . "',
                                                 '" . mysql_real_escape_string($T230_Id) . "',
                                                 '" . mysql_real_escape_string($material['T152_Quantidade']) . "'
                                                 );";
                        
                        $retorno = mysql_query($sql);
                        if (mysql_error()) {
                            mysql_query("ROLLBACK");
                            return '7 Erro: ' . mysql_error();
                        }
                    }
                    
                }
                
                //Copia agregados do produto
                $sql = "SELECT * FROM T202 WHERE T202_T219_Id='{$linha['T219_Id']}'";
                
                $result = mysql_query($sql);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return 'Erro: ' . mysql_error();
                }
                
                while ($orcamentoSer = mysql_fetch_array($result)) {
                    $sql     = "INSERT INTO T204 (T204_T225_Id, 
                                                      T204_Descricao, 
                                                      T204_Observacao,
                                                      T204_Valor_Unitario)
                                                      VALUES 
                                                      ('" . mysql_real_escape_string($T225_Id) . "',
                                                                               '" . mysql_real_escape_string($orcamentoSer['T202_Descricao']) . "',
                                                                               '" . mysql_real_escape_string($orcamentoSer['T202_Observacao']) . "',
                                                                               '" . mysql_real_escape_string($orcamentoSer['T202_Valor_Unitario']) . "'
                                                                               );";
                    $retorno = mysql_query($sql);
                    //return $sql;                 
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return '4 Erro: ' . mysql_error();
                    }
                }
            }
            
            
            $sql     = "SELECT * FROM T230 WHERE T230_T224_Id='{$T224_Id}'";
            $result9 = mysql_query($sql);
            while ($linhaVeiculo = mysql_fetch_array($result9)) {
                //Verificando se já existe o veículo de T230 cadastrado na T231
                $sql    = "SELECT * FROM T231 WHERE T231_D116_Id='{$linhaVeiculo['T230_D116_Id']}' AND T231_T224_Id='{$T224_Id}'";
                $result = mysql_query($sql);
                if (mysql_num_rows($result) == 0) {
                    $sql           = <<<EOT
                    INSERT INTO T231 
                    (T231_D116_Id, T231_T224_Id, T231_T230_Id)
                    VALUES
                    ('{$linhaVeiculo['T230_D116_Id']}','{$T224_Id}','{$linhaVeiculo['T230_Id']}')
EOT;
                    $resultEntrega = mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return '19 Erro: ' . mysql_error();
                    }
                }
            }
            
            $extraWhere = ($listaT218 != '') ? "(T222_T218_Id ='{$T218_Id}' OR T222_T218_Id IN ({$listaT218}))" : "T222_T218_Id ='{$T218_Id}'";
            mysql_query("UPDATE T222 SET T222_T224_Id='$T224_Id' WHERE {$extraWhere}");
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return 'Erro: ' . mysql_error();
            }
            

            mysql_query("call T224_Gravar_Totalizacao_3($T224_Id)");
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return '11 Erro: ' . mysql_error();
            }
            if (!empty($restricaoProduto)) {
                mysql_query("ROLLBACK");
                return $restricaoProduto . "<br />Por favor conclua o cadastro dos itens acima.";
            }
            
            mysql_query("COMMIT");
            mysql_query("SET AUTOCOMMIT=1");
            require_once("bibliotecas/classes/CAD002.php");
            $CAD002 = new CAD002();
            $sqlT225 = mysql_query("SELECT T225_D009_Id FROM T225 WHERE T225_T224_Id = '{$T224_Id}'");
            while($T225 = mysql_fetch_array($sqlT225)){
                $CAD002->reprocessaOCPedidos($T225['T225_D009_Id']);
            }
            return true;
        } else {
            return "ERRO: T218_Id não existe";
        }
        
    }
    
    /**
     * aplicarIpvMinimo
     *
     * @param int $T218_Id
     * @param decimal $IPV_Minimo
     */
    public function aplicarIpvMinimo($T218_Id, $IPV_Minimo)
    {
        global $g;
        //-- Cria ou inicia uma sessï¿½o
        //require "../include/sessao.inc";
        
        //-- Conexï¿½o com o banco de dados
        //require "../include/conexao.inc";
        
        //-- Funï¿½ï¿½es Genï¿½ricas em PHP
        //require "../func/funcoes_genericas.inc";
        
        if ($T218_Id == 0) {
            return "Nao encontrado o id da entidade principal";
        }
        
        //$IPV_Minimo=formata_valor_gravar($IPV_Minimo,5);
        
        if ($IPV_Minimo == 0) {
            
            return "Digite o IPV MÃ­nimo";
        }
        
        $cmysql_T218 = mysql_query("select * from T218 where T218_Id='$T218_Id'");
        $mT218       = mysql_fetch_array($cmysql_T218);
        
        $cmysql_T219 = mysql_query("select T219_Id,
                                     T219_Valor_Custo_Unitario
                                from T219
                           left join T225 on T225_T219_Id=T219_Id 
                               where T219_T218_Id='$T218_Id' 
                                 and (T219_Flag_Preco_Especial is Null or T219_Flag_Preco_Especial='')
                                 and T225_Id is Null");
        if (mysql_num_rows($cmysql_T219) == 0) {
            
            return "Não existem itens para aplicar o IPV";
        }
        while ($mT219 = mysql_fetch_array($cmysql_T219)) {
            
            $Valor_Preco_Unitario = $mT219['T219_Valor_Custo_Unitario'] * $IPV_Minimo;
            
            mysql_query("update T219
                            set T219_Valor_Preco_Unitario='$Valor_Preco_Unitario',
                                T219_Percentual_Desconto=if(T219_Valor_Preco_Original>0,(1-(T219_Valor_Preco_Unitario/T219_Valor_Preco_Original))*100,0),
                                T219_Valor_Desconto_Unitario=if(T219_Valor_Preco_Original>0,(T219_Valor_Preco_Original*(T219_Valor_Preco_Unitario/100)),0)
                          where T219_Id='$mT219[T219_Id]'");
            
            mysql_query("call T219_Gravar_Totalizacao({$mT219['T219_Id']});");
        }
        
        // echo "<script>";
        // echo "top.window.frames.iFrame_Orcamento.Recarrega_Itens();";
        // echo "</script>";
        return true;
    }
    
    /**
     * importarOrcamento
     *
     * @param int $T218_Id
     * @param int $T218_Id_Origem
     */
    public function importarOrcamento($T218_Id, $T218_Id_Origem)
    {
        global $g;
        
        if (empty($T218_Id)) {
            
            return "Faltou o parametro T218_Id";
        }
        if (empty($T218_Id_Origem)) {
            
            return "Digite o numero do orçamento";
        }
        
        /*
        $cmysql_T224=mysql_query("select T224_Id from T224 where T224_T218_Id='$T218_Id' and T224_Flag_Status!=8");
        if (mysql_num_rows($cmysql_T224)>0) {
        echo "<script>";
        echo "parent.MostrarMensagem('none');";
        echo "parent.MaisOpcoes();";
        echo "parent.document.getElementById('T218_Id_Origem').focus();";
        echo "</script>";
        msg_alerta("PEDIDO Jï¿½ FOI GERADO, ORï¿½AMENTO Nï¿½O PODE SER ALTERADO");
        exit; 
        }
        */
        
        $cmysql_T218 = mysql_query("select * from T218 where T218_Id='$T218_Id'");
        $mT218       = mysql_fetch_array($cmysql_T218);
        
        /////////////////////////////////////////////////////////////////////////////////
        //-- VERIFICA SE O ITEM EXISTEM NA EMPRESA DE DESTINO
        /////////////////////////////////////////////////////////////////////////////////
        $cmysql_T219 = mysql_query("select t2.D009_Id,
                                 D001_Codigo_Produto,
                                 D002_Descricao_Produto
                            from T219
                       left join D009 as t1 on T219_D009_Id=t1.D009_Id
                       left join D049 on D049_Id=t1.D009_D049_Id
                       left join D001 on D001_Id=D049_D001_Id
                       left join D002 on D002_Id=D001_D002_Id
                       left join D009 as t2 on D049_Id=t2.D009_D049_Id and t2.D009_C004_Id='{$g['empresaAtual']}'
                           where T219_T218_Id='$T218_Id'
                             and t2.D009_Id=0");
        while ($mT219 = mysql_fetch_array($cmysql_T219)) {
            $mSemCadastro[] = "{$mT219['D001_Codigo_Produto']} - {$mT219['D002_Descricao_Produto']}";
        }
        if (mysql_num_rows($cmysql_T219) > 0) {
            $SemCadastro = implode("/p", $mSemCadastro);
            
            return "Existem itens sem cadastro na empresa {$g['empresaAtual']}/pque estao sendo importados do orï¿½amento $T218_Id_Origem/p/p$SemCadastro";
        }
        
        
        $cmysql_T219 = mysql_query("select  T219_D005_Id,
                                  T219_D037_Id,
                                  T219_D006_Id,
                                  T219_D024_Id,
                                  T219_D082_Id,
                                  t2.D009_Id T219_D009_Id,
                                  t2.D009_D004_Id,
                                  T219_Quantidade,
                                  T219_Descricao_Produto,
                                  D009_Preco_Unitario(T219_D009_Id,'{$mT218['T218_D024_Id']}',D009_IPV_ICMS('{$mT218['T218_C004_Id']}','{$mT218['T218_D024_Id']}',T219_D009_Id,'{$mT218['T218_D006_Id']}',
                                                                   '{$mT218['T218_D018_Id']}','{$mT218['T218_Flag_Revenda_Consumidor']}','{$mT218['T218_Percentual_Comissao']}','{$mT218['T218_Flag_Acp']}',1),if(
                                                                   '{$mT218['T218_Flag_Revenda_Consumidor']}'='C',1,0),'{$mT218['T218_D006_Id']}') as T219_Valor_Preco_Original_Unitario,
                                  T219_Valor_Custo_Unitario,
                                  T219_Quantidade_Confirmada,
                                  T219_Numero_Orcamento_Venda,
                                  T219_Codigo_Produto,
                                  T219_Valor_Preco_Sem_Desconto_Unitario,
                                  T219_Valor_Preco_Unitario,
                                  T219_Percentual_Desconto,
                                  T219_Valor_Preco_Original,
                                  T219_Codigo_Substituto,
                                  T219_Marca,
                                  T219_Peso_Unitario,
                                  D009_Origem_Preco(T219_D009_Id,'{$mT218['T218_D024_Id']}',if('{$mT218['T218_Flag_Revenda_Consumidor']}'='C',1,0)) as T219_Flag_Preco_Especial,
                                  T219_Classificacao_Fiscal 
                             from T219 
                        left join D009 as t1 on T219_D009_Id=t1.D009_Id
                        left join D049 on D049_Id=t1.D009_D049_Id
                        left join D009 as t2 on D049_Id=t2.D009_D049_Id and t2.D009_C004_Id='{$g['empresaAtual']}'
                            where T219_T218_Id='{$T218_Id_Origem}'");
        
        while ($mT219 = mysql_fetch_array($cmysql_T219)) {
            
            if ($mT219['T219_Flag_Preco_Especial'] == 'O')
                $mT219['T219_Valor_Preco_Sem_Desconto_Unitario'] = $mT219['T219_Valor_Preco_Original_Unitario'];
            
            unset($mGravar);
            $mGravar['T219_D005_Id']                           = $mT219['T219_D005_Id'];
            $mGravar['T219_D037_Id']                           = $mT219['T219_D037_Id'];
            $mGravar['T219_D006_Id']                           = $mT219['T219_D006_Id'];
            $mGravar['T219_T218_Id']                           = $T218_Id;
            $mGravar['T219_D024_Id']                           = $mT219['T219_D024_Id'];
            $mGravar['T219_D009_Id']                           = $mT219['T219_D009_Id'];
            $mGravar['T219_D004_Id']                           = $mT219['D009_D004_Id'];
            $mGravar['T219_D082_Id']                           = $mT219['T219_D082_Id'];
            $mGravar['T219_C004_Id']                           = $g['empresaAtual'];
            $mGravar['T219_Item']                              = 0;
            $mGravar['T219_Quantidade']                        = $mT219['T219_Quantidade'];
            $mGravar['T219_Descricao_Produto']                 = $mT219['T219_Descricao_Produto'];
            $mGravar['T219_Observacao_Entrega']                = '';
            $mGravar['T219_Valor_Preco_Sem_Desconto_Unitario'] = $mT219['T219_Valor_Preco_Sem_Desconto_Unitario'];
            $mGravar['T219_Valor_Preco_Unitario']              = $mT219['T219_Valor_Preco_Unitario'];
            $mGravar['T219_Valor_Preco_Original']              = $mT219['T219_Valor_Preco_Original'];
            $mGravar['T219_Percentual_Desconto']               = $mT219['T219_Percentual_Desconto'];
            $mGravar['T219_Valor_Custo_Unitario']              = $mT219['T219_Valor_Custo_Unitario'];
            $mGravar['T219_Quantidade_Confirmada']             = $mT219['T219_Quantidade_Confirmada'];
            $mGravar['T219_Numero_Orcamento_Venda']            = $mT219['T219_Numero_Orcamento_Venda'];
            $mGravar['T219_Codigo_Produto']                    = $mT219['T219_Codigo_Produto'];
            $mGravar['T219_Codigo_Substituto']                 = $mT219['T219_Codigo_Substituto'];
            $mGravar['T219_Marca']                             = $mT219['T219_Marca'];
            $mGravar['T219_Peso_Unitario']                     = $mT219['T219_Peso_Unitario'];
            $mGravar['T219_Flag_Preco_Especial']               = $mT219['T219_Flag_Preco_Especial'];
            $mGravar['T219_Classificacao_Fiscal']              = $mT219['T219_Classificacao_Fiscal'];
            
            //esta função estÃ¡ em func/gravar_entidade.php3
            gravar_entidade($mGravar, 'T219', 0, '', 'T218', $T218_Id, 'MANUAL', '001');
            
            
        }
        
        mysql_query("call T218_Gravar_Totalizacao_3($T218_Id)");
        
        //echo "<script>location.replace('atualizar_aliquotas.php3?T218_Id=$T218_Id');</script>";
        return true;
    }
    
    public function atualizarAliquotas($T218_Id, $C004_Id = false, $r_usarTabelaFornecedor = false, $arrayParametros = null, $opcoes = null)
    { 
        global $g;
        require_once('bibliotecas/classes/FIS003.php');
        $FIS003 = new FIS003();


        $C004_Id = ($C004_Id == false) ? $g['empresaAtual'] : $C004_Id;

        $D009_Id= $arrayParametros['D009_Id'];
        $T218_Flag_Calcular_IPI= $arrayParametros['T218_Flag_Calcular_IPI'];
        $T218_Flag_Calcular_ICMS= $arrayParametros['T218_Flag_Calcular_ICMS'];
        $T218_Flag_Calcular_ST= $arrayParametros['T218_Flag_Calcular_ST'];
        $T219_Id = $arrayParametros['T219_Id'];   
        $T219_Item = $arrayParametros['T219_Item']; 
        $T219_Flag_Origem_Produto = $arrayParametros['T219_Flag_Origem_Produto'];
        $D001_Codigo_Produto = $arrayParametros['D001_Codigo_Produto'];   
        $D001_D037_Id = $arrayParametros['D001_D037_Id'];
        $D001_Peso_Unitario_Kg = $arrayParametros['D001_Peso_Unitario_Kg']; 
        $atualizarCampo = function ($campo) use ($opcoes) {
            return !is_array($opcoes) || !array_key_exists($campo, $opcoes)
                || ($opcoes[$campo] !== false && $opcoes[$campo] !== 'false');
        };
        
        // $Item = 1;

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        $produtosIncompletos = '';

        if($r_usarTabelaFornecedor==true){
            mysqli_query("UPDATE T218 SET T218_Flag_Calcular_ST='S',T218_Flag_Calcular_IPI='S',T218_Flag_Calcular_ICMS='S' WHERE T218_Id='{$T218_Id}'");
        }
            
        $Aliquotas = $this->retornaAliquotas($D009_Id, $T218_Id, false, false, $T219_Id, false, false, false, false, $r_usarTabelaFornecedor);
        if (!is_array($Aliquotas)) {
            mysql_query("ROLLBACK");
            return "Erro ao consultar alíquotas do item {$T219_Id}: {$Aliquotas}";
        }

        // Não permite que uma falha na consulta fiscal apague a NCM já gravada
        // no item ou confirme a cotação com a NCM em branco.
        if (empty($Aliquotas['D005_Id']) && $g['C031']['AtualizarAliquotasExigeNCM'] == 'S') {
            mysql_query("ROLLBACK");
            return "Não foi encontrada NCM para o item {$D001_Codigo_Produto} da cotação {$T218_Id}";
        }

        if($r_usarTabelaFornecedor == true){
            if($Aliquotas['D069_Id'] <= 0){
                $produtoSemTabela = (empty($produtoSemTabela)) ? $D001_Codigo_Produto : ', '.$D001_Codigo_Produto;
            }else if($Aliquotas['D006_Id'] <= 0){
                $produtoSemCFOP = (empty($produtoSemCFOP)) ? $D001_Codigo_Produto : ', '.$D001_Codigo_Produto;
            }else if($Aliquotas['Origem_Mercadoria'] == ''){
                $produtoSemOrigem = (empty($produtoSemOrigem)) ? $D001_Codigo_Produto : ', '.$D001_Codigo_Produto;
            }
        }
                    
        // if ($mT219_Item <= 0) {
        //     $T219_Item = $Item;
        // }

        // if (!($T219_Flag_Origem_Produto === '') and ($r_usarTabelaFornecedor == false)) {
        //     $Aliquotas['Origem_Mercadoria'] = $T219_Flag_Origem_Produto;
        // }
        if (!empty($T219_Flag_Origem_Produto) and ($r_usarTabelaFornecedor == false)) {
            $Aliquotas['Origem_Mercadoria'] = $T219_Flag_Origem_Produto;
        }
        // $Item++;
        
        $demonstrativoNCM = $FIS003->demonstrativoNCM($Aliquotas['D005_Id']);
        $demonstrativoNCM = mysql_real_escape_string($demonstrativoNCM);

        if($r_usarTabelaFornecedor == true){


            $sql = "UPDATE T219 SET T219_D005_Id='{$Aliquotas['D005_Id']}',";
            $sql .= "T219_Classificacao_Fiscal='{$Aliquotas['D005_Classificacao_Fiscal']}',";
            $sql .= "T219_Item='{$T219_Item}',";
            $sql .= "T219_D037_Id='{$D001_D037_Id}',";
            $sql .= "T219_D006_Id='{$Aliquotas['D006_Id']}',";
            $sql .= "T219_Aliquota_PIS='{$Aliquotas['Aliquota_PIS']}',";
            $sql .= "T219_Aliquota_COFINS='{$Aliquotas['Aliquota_COFINS']}',";
            $sql .= "T219_Aliquota_IPI='{$Aliquotas['Aliquota_IPI']}',";
            $sql .= "T219_Percentual_Reducao_IPI='{$Aliquotas['Percentual_Reducao_IPI']}',";
            $sql .= "T219_Aliquota_ICMS='{$Aliquotas['Aliquota_ICMS']}',";
            $sql .= "T219_Percentual_Reducao_ICMS='{$Aliquotas['Percentual_Reducao_ICMS']}',";
            $sql .= "T219_Percentual_Reducao_ICMS_ST='{$Aliquotas['Percentual_Reducao_ICMS_ST']}',";
            $sql .= "T219_Flag_ST='{$Aliquotas['Flag_ST']}',";
            $sql .= "T219_ST_VA='{$Aliquotas['ST_VA']}',";
            $sql .= "T219_ICMS_Inter_Estadual='{$Aliquotas['ICMS_Inter_Estadual']}',";
            $sql .= "T219_ST_ICMS_Interno='{$Aliquotas['ST_ICMS_Interno']}',";
            $sql .= "T219_ICMS_Inter_Estadual_MVA='{$Aliquotas['ST_ICMS_Inter_Estadual_MVA']}',";
            $sql .= "T219_ST_ICMS_Interno_MVA='{$Aliquotas['ST_ICMS_Interno_MVA']}',";
            $sql .= "T219_Flag_Origem_Produto='{$Aliquotas['Origem_Mercadoria']}',";
            $sql .= "T219_D059_Id='{$Aliquotas['D059_Id']}',";
            $sql .= "T219_Flag_Situacao_Tributaria='{$Aliquotas['Flag_Situacao_Tributaria']}',";
            $sql .= "T219_Flag_Isento_PIS='{$Aliquotas['Flag_Isento_PIS']}',";
            $sql .= "T219_Aliquota_PIS='{$Aliquotas['Aliquota_PIS']}',";
            $sql .= "T219_Situacao_Tributaria_PIS='{$Aliquotas['Situacao_Tributaria_PIS']}',";
            $sql .= "T219_Aliquota_COFINS='{$Aliquotas['Aliquota_COFINS']}',";
            $sql .= "T219_Situacao_Tributaria_COFINS='{$Aliquotas['Situacao_Tributaria_COFINS']}',";
            $sql .= "T219_Flag_Isento_COFINS='{$Aliquotas['Flag_Isento_COFINS']}',";
            $sql .= "T219_Situacao_Tributaria_IPI='{$Aliquotas['Situacao_Tributaria_IPI']}',";
            $sql .= "T219_Peso_Unitario='{$D001_Peso_Unitario_Kg}',";
            $sql .= "T219_Peso_Bruto='{$Aliquotas['D001_Peso_Unitario_Bruto']}',";
            $sql .= "T219_Percentual_Trib_Transparencia='{$Aliquotas['Percentual_Trib_Transparencia']}',";
            $sql .= "T219_Percentual_ST='{$Aliquotas['Percentual_ST']}',";


            /* Trigger serve para aplicar algum desconto ou fazer algum
            outro cálculo mirabulante na personalização */
            $Valor_Item_Tabela_Fornecedor = $this->est015TriggerAlteraValorItemTabelaFornecedor($Aliquotas['Valor_Item_Tabela_Fornecedor'], $T219_Id);
            if(is_string($Valor_Item_Tabela_Fornecedor)) // Deu erro, personalização deve retornar uma string se der erro
            {
                mysql_query("ROLLBACK");
                return $Valor_Item_Tabela_Fornecedor;
            }

            $sql .= "T219_Valor_Preco_Unitario='{$Valor_Item_Tabela_Fornecedor}',";
            $sql .= "T219_D005_Id_Info='{$demonstrativoNCM}'";
            $sql .= "WHERE T219_Id='{$T219_Id}'";
        
        } else {

            $sql = "UPDATE T219 SET T219_D005_Id='{$Aliquotas['D005_Id']}',";
            $sql .= "T219_Classificacao_Fiscal='{$Aliquotas['D005_Classificacao_Fiscal']}',";
            $sql .= "T219_Item='{$T219_Item}',";
            $sql .= "T219_D037_Id='{$D001_D037_Id}',";
            if($r_usarTabelaFornecedor == true){
                $sql .= "T219_D006_Id='{$Aliquotas['D006_Id']}',";
                $sql .= "T219_Aliquota_PIS='{$Aliquotas['Aliquota_PIS']}',";
                $sql .= "T219_Aliquota_COFINS='{$Aliquotas['Aliquota_COFINS']}',";
            }
            if($T218_Flag_Calcular_IPI == 'S'){
                $sql .= "T219_Aliquota_IPI='{$Aliquotas['Aliquota_IPI']}',";
                $sql .= "T219_Percentual_Reducao_IPI='{$Aliquotas['Percentual_Reducao_IPI']}',";
                log(" em T218_Flag_Calcular_IPI = " . $Aliquotas['Aliquota_IPI']);
            }
            if($T218_Flag_Calcular_ICMS == 'S'){
                $sql .= "T219_Aliquota_ICMS='{$Aliquotas['Aliquota_ICMS']}',";
            }
            if($T218_Flag_Calcular_ST == 'S'){
                $sql .= "T219_Flag_ST='{$Aliquotas['Flag_ST']}',";
                $sql .= "T219_ST_VA='{$Aliquotas['ST_VA']}',";
                $sql .= "T219_ICMS_Inter_Estadual_MVA='{$Aliquotas['ST_ICMS_Inter_Estadual_MVA']}',";
                $sql .= "T219_ST_ICMS_Interno_MVA='{$Aliquotas['ST_ICMS_Interno_MVA']}',";

            }

            // Retirado O T219_ST_ICMS_Interno e T219_ICMS_Inter_Estadual da verificação do T218_Flag_Calcular_ST para poder calcular o DIFAL com esse valor
            $sql .= "T219_Percentual_Reducao_ICMS='{$Aliquotas['Percentual_Reducao_ICMS']}',";
            $sql .= "T219_Percentual_Reducao_ICMS_ST='{$Aliquotas['Percentual_Reducao_ICMS_ST']}',";
            $sql .= "T219_ST_ICMS_Interno='{$Aliquotas['ST_ICMS_Interno']}',";
            $sql .= "T219_ICMS_Inter_Estadual='{$Aliquotas['ICMS_Inter_Estadual']}',";
            $sql .= "T219_Flag_Origem_Produto='{$Aliquotas['Origem_Mercadoria']}',";
            $sql .= "T219_D059_Id='{$Aliquotas['D059_Id']}',";
            $sql .= "T219_Flag_Situacao_Tributaria='{$Aliquotas['Flag_Situacao_Tributaria']}',";
            $sql .= "T219_Flag_Isento_PIS='{$Aliquotas['Flag_Isento_PIS']}',";
            $sql .= "T219_Aliquota_PIS='{$Aliquotas['Aliquota_PIS']}',";
            $sql .= "T219_Situacao_Tributaria_PIS='{$Aliquotas['Situacao_Tributaria_PIS']}',";
            $sql .= "T219_Aliquota_COFINS='{$Aliquotas['Aliquota_COFINS']}',";
            $sql .= "T219_Situacao_Tributaria_COFINS='{$Aliquotas['Situacao_Tributaria_COFINS']}',";
            $sql .= "T219_Flag_Isento_COFINS='{$Aliquotas['Flag_Isento_COFINS']}',";
            $sql .= "T219_Situacao_Tributaria_IPI='{$Aliquotas['Situacao_Tributaria_IPI']}',";
            $sql .= "T219_Peso_Unitario='{$D001_Peso_Unitario_Kg}',";
            $sql .= "T219_Peso_Bruto='{$Aliquotas['D001_Peso_Unitario_Bruto']}',";
            $sql .= "T219_Percentual_Trib_Transparencia='{$Aliquotas['Percentual_Trib_Transparencia']}',";
            $sql .= "T219_Percentual_ST='0',";
            $sql .= "T219_D005_Id_Info='{$demonstrativoNCM}', ";
            $sql .= "T219_Aliquota_ICMS_Diferenca_Aliquotas='{$Aliquotas['Aliquota_DIFAL']}' ";
            $sql .= "WHERE T219_Id='{$T219_Id}'";
        }
        if (is_array($opcoes)) {
            $camposPorOpcao = array(
                'ipi' => array('T219_Aliquota_IPI', 'T219_Percentual_Reducao_IPI', 'T219_Situacao_Tributaria_IPI'),
                'icms' => array('T219_Aliquota_ICMS', 'T219_Percentual_Reducao_ICMS', 'T219_Aliquota_ICMS_Diferenca_Aliquotas'),
                'st' => array('T219_Percentual_Reducao_ICMS_ST', 'T219_Flag_ST', 'T219_ST_VA', 'T219_ICMS_Inter_Estadual', 'T219_ST_ICMS_Interno', 'T219_ICMS_Inter_Estadual_MVA', 'T219_ST_ICMS_Interno_MVA', 'T219_Percentual_ST'),
                'pisCofins' => array('T219_Aliquota_PIS', 'T219_Aliquota_COFINS', 'T219_Flag_Isento_PIS', 'T219_Situacao_Tributaria_PIS', 'T219_Flag_Isento_COFINS', 'T219_Situacao_Tributaria_COFINS'),
                'cfop' => array('T219_D006_Id'),
                'origem' => array('T219_Flag_Origem_Produto'),
                'ncm' => array('T219_D005_Id', 'T219_Classificacao_Fiscal', 'T219_D059_Id', 'T219_Flag_Situacao_Tributaria', 'T219_D005_Id_Info'),
                'unidade' => array('T219_D037_Id'),
                'peso' => array('T219_Peso_Unitario', 'T219_Peso_Bruto'),
                'transparencia' => array('T219_Percentual_Trib_Transparencia'),
                'precoTabela' => array('T219_Valor_Preco_Unitario')
            );
            foreach ($camposPorOpcao as $opcao => $campos) {
                if (!$atualizarCampo($opcao)) {
                    foreach ($campos as $campo) {
                        $sql = preg_replace('/\\s*' . $campo . "\\s*=\\s*'[^']*'\\s*,?/", '', $sql);
                    }
                }
            }
        }
        mysql_query($sql);
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "Erro atualizando item do orcamento ID {$T219_Id}: $erro";
        }

        if ($atualizarCampo('icms')) {
        $T219A = mysql_query("SELECT T219A_Id FROM T219A WHERE T219A_T219_Id='$T219_Id'");
        if (mysql_num_rows($T219A) > 0) {
            $sql = "UPDATE T219A 
                       SET T219A_Percentual_Reducao_ICMS_IE_ST  = '{$Aliquotas['Percentual_Reducao_ICMS_IE_ST']}',
                           T219A_Percentual_Reducao_ICMS_INT_ST = '{$Aliquotas['Percentual_Reducao_ICMS_INT_ST']}', 
                           T219A_Aliquota_FCP                   = '{$Aliquotas['Percentual_FCP']}',
                           T219A_Situacao_Tributaria_CBS_IBS = '{$Aliquotas['Situacao_Tributaria_CBS_IBS']}',
                           T219A_Classificacao_Tributaria_CBS_IBS = '{$Aliquotas['Classificacao_Tributaria_CBS_IBS']}',
                           T219A_Aliquota_CBS = '{$Aliquotas['Aliquota_CBS']}',
                           T219A_Aliquota_IBS_UF = '{$Aliquotas['Aliquota_IBS_UF']}',
                           T219A_Aliquota_IBS_Municipal = '{$Aliquotas['Aliquota_IBS_Municipal']}',
                           T219A_Percentual_Reducao_CBS = '{$Aliquotas['Percentual_Reducao_CBS']}',
                           T219A_Percentual_Reducao_IBS_UF = '{$Aliquotas['Percentual_Reducao_IBS_UF']}',
                           T219A_Percentual_Reducao_IBS_Municipal = '{$Aliquotas['Percentual_Reducao_IBS_Municipal']}',
                           T219A_Percentual_Diferimento_CBS = '{$Aliquotas['Percentual_Diferimento_CBS']}',
                           T219A_Percentual_Diferimento_IBS_UF = '{$Aliquotas['Percentual_Diferimento_IBS_UF']}',
                           T219A_Percentual_Diferimento_IBS_Municipal = '{$Aliquotas['Percentual_Diferimento_IBS_Municipal']}',
                           T219A_Situacao_Tributaria_CBS_IBS_Regular = '{$Aliquotas['Situacao_Tributaria_CBS_IBS_Regular']}',
                           T219A_Classificacao_Tributaria_CBS_IBS_Regular = '{$Aliquotas['Classificacao_Tributaria_CBS_IBS_Regular']}',
                           T219A_Aliquota_CBS_Regular = '{$Aliquotas['Aliquota_CBS_Regular']}',
                           T219A_Aliquota_IBS_UF_Regular = '{$Aliquotas['Aliquota_IBS_UF_Regular']}',
                           T219A_Aliquota_IBS_Municipal_Regular = '{$Aliquotas['Aliquota_IBS_Municipal_Regular']}',
                           T219A_Percentual_Reducao_CBS_Regular = '{$Aliquotas['Percentual_Reducao_CBS_Regular']}',
                           T219A_Percentual_Reducao_IBS_UF_Regular = '{$Aliquotas['Percentual_Reducao_IBS_UF_Regular']}',
                           T219A_Percentual_Reducao_IBS_Municipal_Regular = '{$Aliquotas['Percentual_Reducao_IBS_Municipal_Regular']}'
                     WHERE T219A_T219_Id='{$T219_Id}'";
        } else {
            $sql = "INSERT INTO T219A (T219A_T219_Id,
                                       T219A_Percentual_Reducao_ICMS_IE_ST,
                                       T219A_Percentual_Reducao_ICMS_INT_ST,
                                       T219A_Aliquota_FCP,
                                       T219A_Situacao_Tributaria_CBS_IBS,
                                       T219A_Classificacao_Tributaria_CBS_IBS,
                                       T219A_Aliquota_CBS,
                                       T219A_Aliquota_IBS_UF,
                                       T219A_Aliquota_IBS_Municipal,
                                       T219A_Percentual_Reducao_CBS,
                                       T219A_Percentual_Reducao_IBS_UF,
                                       T219A_Percentual_Reducao_IBS_Municipal,
                                       T219A_Percentual_Diferimento_CBS,
                                       T219A_Percentual_Diferimento_IBS_UF,
                                       T219A_Percentual_Diferimento_IBS_Municipal,
                                       T219A_Situacao_Tributaria_CBS_IBS_Regular,
                                       T219A_Classificacao_Tributaria_CBS_IBS_Regular,
                                       T219A_Aliquota_CBS_Regular,
                                       T219A_Aliquota_IBS_UF_Regular,
                                       T219A_Aliquota_IBS_Municipal_Regular,
                                       T219A_Percentual_Reducao_CBS_Regular,
                                       T219A_Percentual_Reducao_IBS_UF_Regular,
                                       T219A_Percentual_Reducao_IBS_Municipal_Regular)
                               VALUES ('{$T219_Id}', 
                                       '{$Aliquotas['Percentual_Reducao_ICMS_IE_ST']}',
                                       '{$Aliquotas['Percentual_Reducao_ICMS_INT_ST']}',
                                       '{$Aliquotas['Percentual_FCP']}',
                                       '{$Aliquotas['Situacao_Tributaria_CBS_IBS']}',
                                       '{$Aliquotas['Classificacao_Tributaria_CBS_IBS']}',
                                       '{$Aliquotas['Aliquota_CBS']}',
                                       '{$Aliquotas['Aliquota_IBS_UF']}',
                                       '{$Aliquotas['Aliquota_IBS_Municipal']}',
                                       '{$Aliquotas['Percentual_Reducao_CBS']}',
                                       '{$Aliquotas['Percentual_Reducao_IBS_UF']}',
                                       '{$Aliquotas['Percentual_Reducao_IBS_Municipal']}',
                                       '{$Aliquotas['Percentual_Diferimento_CBS']}',
                                       '{$Aliquotas['Percentual_Diferimento_IBS_UF']}',
                                       '{$Aliquotas['Percentual_Diferimento_IBS_Municipal']}',
                                       '{$Aliquotas['Situacao_Tributaria_CBS_IBS_Regular']}',
                                       '{$Aliquotas['Classificacao_Tributaria_CBS_IBS_Regular']}',
                                       '{$Aliquotas['Aliquota_CBS_Regular']}',
                                       '{$Aliquotas['Aliquota_IBS_UF_Regular']}',
                                       '{$Aliquotas['Aliquota_IBS_Municipal_Regular']}',
                                       '{$Aliquotas['Percentual_Reducao_CBS_Regular']}',
                                       '{$Aliquotas['Percentual_Reducao_IBS_UF_Regular']}',
                                       '{$Aliquotas['Percentual_Reducao_IBS_Municipal_Regular']}')";
        }

        mysql_query($sql);
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "Erro atualizando item da cotação T219A ID T219_Id {$T219_Id}: $erro";
        }
        }

        $T218A = mysql_query("SELECT T218A_Id FROM T218A WHERE T218A_T218_Id='{$T218_Id}'");
        if (mysql_num_rows($T218A) <= 0) {
            $sql = "INSERT INTO T218A (T218A_T218_Id) VALUES ('{$T218_Id}')";
            mysql_query($sql);
            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return "Erro ao fazer registro da tabela estendida T218A: $erro";
            } 
        }
    
        log($sql);
        mysql_query("call T219_Gravar_Totalizacao('{$T219_Id}');");  

        if ($atualizarCampo('cfop') && !empty($produtoSemCFOP)){
            mysql_query("ROLLBACK");
            return 'O produto ' . $produtoSemCFOP . ' está com CFOP em branco na Tabela do Fornecedor.';
        }
        if ($atualizarCampo('origem') && !empty($produtoSemOrigem)){
            mysql_query("ROLLBACK");
            return 'O produto ' . $produtoSemOrigem . ' está com Origem em branco na Tabela do Fornecedor.';
        }
        if (!empty($produtoSemTabela)){
            mysql_query("ROLLBACK");
            return 'O produto ' . $produtoSemTabela . ' não está cadastrado na Tabela do Fornecedor';
        }

        mysql_query("COMMIT");
        return true;
    }
    
    /**
     * incluirProdutoTroca
     *
     * @param Form $form
     */
    public function incluirProdutoTroca($form)
    {
        $T222_Quantidade           = $form->campoValorEnviado('T222_Quantidade');
        $T222_Observacao           = $form->campoValorEnviado('T222_Observacao');
        $T222_Valor_Preco_Unitario = $form->campoValorEnviado('T222_Valor_Preco_Unitario');
        $T222_Valor_Preco_Unitario = gCorrigeNumeroInverte($T222_Valor_Preco_Unitario);
        $T222_Operacao             = $form->campoValorEnviado('T222_Operacao');
        $T222_T218_Id              = $form->campoValorEnviado('T222_T218_Id');
        $T222_C004_Id              = $form->campoValorEnviado('T222_C004_Id');
        $T222_D009_Id              = $form->campoValorEnviado('T222_D009_Id');
        
        if ($T222_D009_Id <= 0)
            return "nenhum produto foi selecionado";
        
        $cClausula = " insert into T222 (";
        $cClausula .= " T222_T218_Id, ";
        $cClausula .= " T222_C004_Id, ";
        $cClausula .= " T222_D009_Id, ";
        $cClausula .= " T222_Operacao, ";
        $cClausula .= " T222_Valor_Preco_Unitario, ";
        $cClausula .= " T222_Quantidade, ";
        $cClausula .= " T222_Observacao ";
        $cClausula .= " ) values ( ";
        $cClausula .= " '$T222_T218_Id', ";
        $cClausula .= " '$T222_C004_Id', ";
        $cClausula .= " '$T222_D009_Id', ";
        $cClausula .= " '$T222_Operacao', ";
        $cClausula .= " '$T222_Valor_Preco_Unitario', ";
        $cClausula .= " '$T222_Quantidade', ";
        $cClausula .= " '$T222_Observacao' ";
        $cClausula .= " ) ";
        
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro))
            return "Erro: $cErro/p/p$cClausula";
        else
            return true;
    }
    
    /**
     * incluirProduto
     *
     * @param Form $form
     * @param int $D009_Id
     * @param int $T218_Id
     */
    public function incluirProduto($form, $D009_Id = false, $T218_Id = false)
    {
        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();
        global $g, $T219_Id;
        if ($D009_Id > 0) {
            
            /*
            T219_Ultimo_Preco(D001_Id,'$mT218[T218_D024_Id]','$T218_Id',1) as Preco_Consultado, 
            T219_Ultimo_Preco(D001_Id,'$mT218[T218_D024_Id]','$T218_Id',2) as Preco_Confirmado 
            */
            
            $sql       = "SELECT T218_D024_Id from T218 WHERE T218_Id = '{$T218_Id}'";
            $resultado = mysql_query($sql);
            $rowT218   = mysql_fetch_assoc($resultado);
            
            
            $sql = <<<EOT
            SELECT  *
                    from D001
                left join D049 on D049_D001_Id=D001_Id
                left join D083 on D083_D001_Id=D001_Id AND D083_D024_Id = '{$rowT218['T218_D024_Id']}'
                left join D009 on D009_D049_Id=D049_Id
                left join D082 on D082_Id=D049_D082_Id
                left join D041 on D041_D049_Id=D049_Id
                left join D005 on D005_Id=D001_D005_Id
                    WHERE D009_Id = '{$D009_Id}'
EOT;
            
            $resultado = mysql_query($sql);
            $row       = mysql_fetch_assoc($resultado);
            
            // Aliquota IPI
            //$row['D005_Aliquota_IPI'] = number_format($row['D005_Aliquota_IPI'],2,',','.');
            $cClausula = " select *, ";
            $cClausula .= " D053_ST_ICMS_Interno_Compra, 
                          D053_ST_ICMS_Inter_Estadual_Compra, 
                          D053_Flag_ST_Compra, 
                          D018_Id, 
                          C004_D018_Id, 
                          C004_CFOP_ST_Venda_Dentro_Estado, 
                          C004_CFOP_ST_Venda_Fora_Estado,
                          C004_CFOP_CONSUMO_Venda_Dentro_Estado,
                          C004_CFOP_CONSUMO_Venda_Fora_Estado,
                          T218_Flag_Lista_Preco,
                          D024_Flag_Contribuinte ";
            $cClausula .= " from T218 ";
            $cClausula .= " left join D024 on D024_Id=T218_D024_Id ";
            $cClausula .= " left join D018 on D018_Id=D024_D018_Id ";
            $cClausula .= " left join D053 on D053_D018_Id=D024_D018_Id and D053_C004_Id=T218_C004_Id ";
            $cClausula .= " left join C004 on C004_Id=T218_C004_Id ";
            $cClausula .= " where T218_Id='{$T218_Id}'";
            $T218  = mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                return "$cErro/p/p$cClausula";
                exit;
            }
            $mT218 = mysql_fetch_array($T218);
            $sql   = <<<EOT
                SELECT 
                    D009_Id, 
                    D009_IPI('{$mT218['T218_C004_Id']}',D009_Id,'{$mT218['T218_D024_Id']}','{$mT218['T218_D006_Id']}','1','{$mT218['T218_Flag_Acp']}') as Aliquota_IPI 
                FROM 
                    D009 
                where D009_Id='{$D009_Id}'
EOT;
            /**
             * DEFININDO QUAL O PREÇO QUE SERÁ RETORNADO
             * 
             * $row['D041_Valor_Base'] = number_format($row['D041_Valor_Base'],2,',','.');
             */
            if (isset($mT218['T218_Flag_Lista_Preco'])) {
                //die("D049: ".$row['D049_Id']." Lista: ".$mT218['T218_Flag_Lista_Preco']);
                $Preco_Tabelado = $CAD002->cad002RetornaPrecoCompra($row['D009_Id'], $mT218['T218_Flag_Lista_Preco'], null);
            } else {
                $Preco_Tabelado = $CAD002->cad002RetornaPrecoCompra($row['D009_Id'], null, $mT218['T218_D024_Id']);
            }
            
            $result              = mysql_query($sql);
            $row2                = mysql_fetch_assoc($result);
            $row['Aliquota_IPI'] = !isset($row2['Aliquota_IPI']) ? '0,00' : number_format($row2['Aliquota_IPI'], 2, ',', '.');
            
            /**
             * Os campos que estão atribuidos o valor zero que é não são selecionados nos selects acima.
             */
            $T219_D009_Id              = $D009_Id;
            $T219_Quantidade           = 0;
            $T219_Valor_Preco_Unitario = $Preco_Tabelado;
            $T219_D037_Id              = $row['D001_D037_Id'];
            $T219_Descricao_Produto    = mysql_real_escape_string($row['D001_Descricao_Produto']);
            $T219_Codigo_Produto       = $row['D001_Codigo_Produto'];
            $T219_IPV                  = "";
            $T218_Id;
            $T219_Codigo_Substituto       = $row['D083_Codigo_Produto_Fornecedor'];
            $T219_Numero_Pedido_Compra    = '';
            $T219_Item_Pedido_Compra      = 'NULL';
            $T219_Observacao              = '';
            $T219_Aliquota_IPI            = $row['Aliquota_IPI'];
            $idVeiculoOrcamento           = 0;
            $T219_Id                      = '';
            $T219_Percentual_Desconto     = 0;
            $T219_Valor_Desconto_Unitario = 0;
            $T219_Quantidade              = gCorrigeNumeroInverte($T219_Quantidade);
            $T219_Valor_Preco_Unitario    = gCorrigeNumeroInverte($T219_Valor_Preco_Unitario);
        } else {
            
            /**
             * ATENÇÃO!!!
             * 
             * Sempre que for utlizar a variável $form, é necessário adicioná-la dentro desse else
             * esse método de incluir produto no orçamento é usado também quando o orçamento é gerado
             * via catálogo no CRM, e nesse caso, nunca teremos a variável form e não irá funcionar a inclusão
             * dos itens via tab CATÁLOGO
             */
            
            $T219_D009_Id                 = $form->campoValorEnviado('T219_D009_Id');
            $T219_D144_Id                 = $form->campoValorEnviado('T219_D144_Id');
            $T219_Quantidade              = $form->campoValorEnviado('T219_Quantidade');
            $T219_Valor_Preco_Unitario    = $form->campoValorEnviado('T219_Valor_Preco_Sem_Desconto_Unitario');
            $T219_D037_Id                 = $form->campoValorEnviado('T219_D037_Id');
            $T219_Descricao_Produto       = mysql_real_escape_string($form->campoValorEnviado('T219_Descricao_Produto'));
            $T219_Codigo_Produto          = $form->campoValorEnviado('T219_Codigo_Produto');
            $T219_IPV                     = $form->campoValorEnviado('T219_IPV');
            $T219_D006_Id                 = $form->campoValorEnviado('T219_D006_Id');
            $T218_Id                      = $form->campoValorEnviado('T219_T218_Id');
            $T219_Codigo_Substituto       = $form->campoValorEnviado('T219_Codigo_Substituto');
            $T219_Numero_Pedido_Compra    = $form->campoValorEnviado('T219_Numero_Pedido_Compra');
            $T219_Item_Pedido_Compra      = $form->campoValorEnviado('T219_Item_Pedido_Compra');
            $T219_Item_Pedido_Compra      = (empty($T219_Item_Pedido_Compra) ? 'NULL' : $T219_Item_Pedido_Compra);
            $T219_Observacao              = $form->campoValorEnviado('T219_Observacao');
            $T219_Aliquota_IPI            = $form->campoValorEnviado('T219_Aliquota_IPI');
            $idVeiculoOrcamento           = $form->campoValorEnviado('idVeiculoOrcamento');
            $T219_D005_Id                 = $form->campoValorEnviado('T219_D005_Id');
            $T219_Percentual_Desconto     = gCorrigeNumeroInverte($form->campoValorEnviado('T219_Percentual_Desconto'));
            $T219_Id                      = '';
            //$NcmProduto=$form->campoValorEnviado('NcmProduto');
            //$T219_Percentual_Desconto=0;
            $T219_Valor_Desconto_Unitario = 0;
            
            $T219_Quantidade           = gCorrigeNumeroInverte($T219_Quantidade);
            $T219_Valor_Preco_Unitario = gCorrigeNumeroInverte($T219_Valor_Preco_Unitario);
        }
        
        $Aliquotas = $this->retornaAliquotas($T219_D009_Id, $T218_Id, false);
        
        $D001  = mysql_query("select D001_Id,
                                  D082_Id,
                                  D001_Codigo_Produto,
                                  D049_Origem_Mercadoria,
                                  D001_Flag_Tipo,
                                  D049_Flag_Nacional_Importado,
                                  D001_Cubagem_Unitaria
                             from D001
                        left join D049 on D049_D001_Id=D001_Id
                        left join D009 on D009_D049_Id=D049_Id
                        left join D082 on D082_Id=D049_D082_Id
                            where D009_Id='$T219_D009_Id'");
        $mD001 = mysql_fetch_array($D001);
        
        $T219_D001_Id        = $mD001['D001_Id'];
        $T219_Codigo_Produto = $mD001['D001_Codigo_Produto'];
        
        $C007  = mysql_query("select *
                             from C007
                            where C007_Id={$g['usuarioAtual']}");
        $mC007 = mysql_fetch_array($C007);
        
        $T219  = mysql_query("select T219_Item
                                      from T219
                                     where T219_T218_Id='$T218_Id'
                                  order by T219_Item desc
                                     limit 1");
        $mT219 = mysql_fetch_array($T219);
        
        $T219_Item = $mT219['T219_Item'] + 1;
        
        $cClausula = " select ";
        $cClausula .= " D009_Id, ";
        $cClausula .= " D005_Flag_ST, ";
        $cClausula .= " D009_Valor_Custo_Unitario, ";
        $cClausula .= " D009_Flag_Preco_Tabelado, ";
        $cClausula .= " D001_Flag_Pre_Cadastro ";
        $cClausula .= " from D001 ";
        $cClausula .= " left join D049 on D001_Id=D049_D001_Id ";
        $cClausula .= " left join D009 on D049_Id=D009_D049_Id ";
        $cClausula .= " left join D005 on D005_Id=D001_D005_Id ";
        $cClausula .= " where D009_Id='$T219_D009_Id' ";
        $T219  = mysql_query($cClausula);
        $mT219 = mysql_fetch_array($T219);
        
        $erro = mysql_error();
        if (!empty($erro) && !$D009_Id) {
            return "Erro consulta custo produto: $erro";
        }
        
        if (($mT219['D009_Id']) <= 0) {
            return "Erro: Produto não encontrado";
        }
        
        $D024_Id = $mT218['T218_D024_Id'];
        
        // verifica qual preço usar, baseado na lista preço do orçamento ou do cliente
        
        $listPreco    = mysql_query("SELECT T218_Flag_Lista_Preco FROM T218 WHERE T218_Id='{$T218_Id}'");
        $mLista_Preco = mysql_fetch_array($listPreco);
        
        if (!empty($mLista_Preco['T218_Flag_Lista_Preco'])) {
            $Preco_Tabelado = $CAD002->retornaPrecoVenda($mT219['D009_Id'], $mLista_Preco['T218_Flag_Lista_Preco'], null);
        } else {
            $Preco_Tabelado = $CAD002->retornaPrecoVenda($mT219['D009_Id'], null, $D024_Id);
        }
        $Preco_Tabelado = gCorrigeNumeroInverte($Preco_Tabelado);
        
        if ($T219_Aliquota_IPI > 0 || $Aliquotas['Aliquota_IPI']) {
            $D088_Codigo_ST = '50';
        } else {
            $D088_Codigo_ST = '53';
        }
        
        if ($T219_Percentual_Desconto > 0) {
            
            $T219_Valor_Preco_Unitario = $T219_Valor_Preco_Unitario - ($T219_Valor_Preco_Unitario * ($T219_Percentual_Desconto / 100));
            
            if ($Preco_Tabelado > 0) {
                $T219_Percentual_Desconto     = ((1 - ($T219_Valor_Preco_Unitario / $Preco_Tabelado)) * 100);
                $T219_Valor_Desconto_Unitario = (($Preco_Tabelado * ($T219_Valor_Preco_Unitario / 100)));
            } else {
                $Preco_Tabelado = $T219_Valor_Preco_Unitario;
            }
            
        } else {
            
            if ($Preco_Tabelado > 0) {
                $T219_Percentual_Desconto     = ((1 - ($T219_Valor_Preco_Unitario / $Preco_Tabelado)) * 100);
                $T219_Valor_Desconto_Unitario = (($Preco_Tabelado * ($T219_Valor_Preco_Unitario / 100)));
            } else {
                $Preco_Tabelado = $T219_Valor_Preco_Unitario;
            }
            
        }
        
        // Buscando o percentual de tributos (lei transparências)           
        $D149  = mysql_query("SELECT D149_Percentual_Trib_Transparencia FROM D149 WHERE D149_Id='{$Aliquotas['D149_Id']}'");
        $mD149 = mysql_fetch_array($D149);
        
        $D006  = mysql_query("SELECT D006_Percentual_Trib_Transparencia FROM D006 WHERE D006_Id='{$T219_D006_Id}'");
        $mD006 = mysql_fetch_array($D006);
        
        if (!empty($mD149['D149_Percentual_Trib_Transparencia']) AND $mD149['D149_Percentual_Trib_Transparencia'] > 0) {
            
            $T219_Percentual_Trib_Transparencia = $mD149['D149_Percentual_Trib_Transparencia'];
            
        } else if (!empty($mD006['D006_Percentual_Trib_Transparencia']) AND $mD006['D006_Percentual_Trib_Transparencia'] > 0) {
            
            $T219_Percentual_Trib_Transparencia = $mD006['D006_Percentual_Trib_Transparencia'];
            
        } else {
            
            $T219_Percentual_Trib_Transparencia = 0;
        }
        
        $cClausula = " insert into T219 (";
        $cClausula .= " T219_C004_Id, ";
        $cClausula .= " T219_Item, ";
        $cClausula .= " T219_Codigo_Produto, ";
        $cClausula .= " T219_Codigo_Substituto, ";
        $cClausula .= " T219_Descricao_Produto, ";
        $cClausula .= " T219_T218_Id, ";
        $cClausula .= " T219_D024_Id, ";
        $cClausula .= " T219_D009_Id, ";
        $cClausula .= " T219_D037_Id, ";
        $cClausula .= " T219_D082_Id, ";
        $cClausula .= " T219_Quantidade, ";
        $cClausula .= " T219_Valor_Custo_Unitario, ";
        $cClausula .= " T219_Valor_Preco_Unitario, ";
        $cClausula .= " T219_Valor_Preco_Sem_Desconto_Unitario, ";
        $cClausula .= " T219_Valor_Preco_Original, ";
        $cClausula .= " T219_Valor_Preco_Original_Bruto, ";
        $cClausula .= " T219_Percentual_Desconto, ";
        $cClausula .= " T219_Valor_Desconto_Unitario, ";
        $cClausula .= " T219_Percentual_Desconto_Bruto, ";
        $cClausula .= " T219_Valor_Desconto_Unitario_Bruto, ";
        $cClausula .= " T219_IPV, ";
        $cClausula .= " T219_Peso_Unitario, ";
        $cClausula .= " T219_Peso_Bruto, ";
        $cClausula .= " T219_D005_Id,";
        $cClausula .= " T219_Classificacao_Fiscal,";
        $cClausula .= " T219_D006_Id,";
        $cClausula .= " T219_Aliquota_IPI,";
        $cClausula .= " T219_Aliquota_ICMS,";
        $cClausula .= " T219_Percentual_Reducao_ICMS,";
        $cClausula .= " T219_Percentual_Reducao_ICMS_ST,";
        $cClausula .= " T219_ST_VA,";
        $cClausula .= " T219_Flag_ST,";
        $cClausula .= " T219_Flag_Situacao_Tributaria,";
        $cClausula .= " T219_ICMS_Inter_Estadual,";
        $cClausula .= " T219_ST_ICMS_Interno,";
        $cClausula .= " T219_ICMS_Inter_Estadual_MVA,";
        $cClausula .= " T219_ST_ICMS_Interno_MVA,";
        $cClausula .= " T219_Flag_Isento_PIS,";
        $cClausula .= " T219_Aliquota_PIS,";
        $cClausula .= " T219_Situacao_Tributaria_PIS,";
        $cClausula .= " T219_Flag_Isento_COFINS,";
        $cClausula .= " T219_Aliquota_COFINS, ";
        $cClausula .= " T219_Flag_Preco_Especial, ";
        $cClausula .= " T219_Situacao_Tributaria_COFINS, ";
        $cClausula .= " T219_Situacao_Tributaria_IPI, ";
        $cClausula .= " T219_D059_Id, ";
        $cClausula .= " T219_Observacao, ";
        $cClausula .= " T219_Numero_Pedido_Compra, ";
        
        if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
            $cClausula .= " T219_Item_Pedido_Compra, ";
            $cClausula .= " T219_Valor_Preco_Bruto, ";
        } else {
            $cClausula .= " T219_Item_Pedido_Compra, ";
        }
        $cClausula .= " T219_Cubagem_Unitaria, ";
        $cClausula .= " T219_Percentual_Trib_Transparencia, ";
        $cClausula .= " T219_Flag_Pre_Cadastro, ";
        $cClausula .= " T219_D144_Id, ";
        $cClausula .= " T219_Flag_Cancelado ";
        $cClausula .= " ) values ( ";
        $cClausula .= " '{$g['empresaAtual']}', ";
        $cClausula .= " '{$T219_Item}', ";
        $cClausula .= " '{$T219_Codigo_Produto}', ";
        $cClausula .= " '{$T219_Codigo_Substituto}', ";
        $cClausula .= " '{$T219_Descricao_Produto}', ";
        $cClausula .= " '{$T218_Id}', ";
        $cClausula .= " '{$D024_Id}', ";
        $cClausula .= " '{$T219_D009_Id}', ";
        $cClausula .= " '{$T219_D037_Id}', ";
        $cClausula .= " '{$Aliquotas['D082_Id']}', ";
        $cClausula .= " '{$T219_Quantidade}', ";
        $cClausula .= " '{$Aliquotas['Valor_Custo_Unitario']}', ";
        $cClausula .= " '{$T219_Valor_Preco_Unitario}', ";
        $cClausula .= " '{$Preco_Tabelado}', ";
        $cClausula .= " '{$Preco_Tabelado}', ";
        $cClausula .= " '{$Preco_Tabelado}', ";
        $cClausula .= " '{$T219_Percentual_Desconto}', ";
        $cClausula .= " '{$T219_Valor_Desconto_Unitario}', ";
        $cClausula .= " '{$T219_Percentual_Desconto}', ";
        $cClausula .= " '{$T219_Valor_Desconto_Unitario}', ";
        $cClausula .= " '{$T219_Valor_Preco_Unitario}/{$mT219['D009_Valor_Custo_Unitario']}', ";
        $cClausula .= " '{$Aliquotas['D001_Peso_Unitario_Kg']}', ";
        $cClausula .= " '{$Aliquotas['D001_Peso_Unitario_Bruto']}', ";
        if ($Aliquotas['D005_Id'] == '') {
            $cClausula .= " '{$T219_D005_Id}',";
        } else {
            $cClausula .= " '{$Aliquotas['D005_Id']}',";
        }
        $cClausula .= " '{$Aliquotas['Classificacao_Fiscal']}',";
        $cClausula .= " '{$T219_D006_Id}',";
        //$cClausula .= " '{$mT219['Aliquota_IPI']}',";
        if ($T219_Aliquota_IPI <= 0) {
            $cClausula .= " '{$Aliquotas['Aliquota_IPI']}',";
        } else {
            $cClausula .= " '{$T219_Aliquota_IPI}',";
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
        $cClausula .= " '{$D088_Codigo_ST}',";
        $cClausula .= " '{$Aliquotas['D059_Id']}',";
        $cClausula .= " '$T219_Observacao', ";
        $cClausula .= " '$T219_Numero_Pedido_Compra', ";
        
        if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
            $cClausula .= " '{$T219_Item_Pedido_Compra}', ";
            $cClausula .= " '{$T219_Valor_Preco_Unitario}', ";
        } else {
            $cClausula .= " '{$T219_Item_Pedido_Compra}', ";
        }
        $cClausula .= " '{$mD001['D001_Cubagem_Unitaria']}', ";
        $cClausula .= " '{$T219_Percentual_Trib_Transparencia}', ";
        $cClausula .= " '{$mT219['D001_Flag_Pre_Cadastro']}', ";
        $cClausula .= " '{$T219_D144_Id}', ";
        $cClausula .= " 'N' ";
        $cClausula .= " ); ";
        mysql_query($cClausula);
        $T219_Id = $g['mysqlLastId'];
        $cErro   = mysql_error();
        if (!empty($cErro)) {
            return "Erro: $cErro/p/p$cClausula";
        } else {
            if (!empty($T219_Codigo_Substituto)) {
                /*
                $D083 = mysql_query("select D083_Id,D083_Flag_Cliente,D083_Codigo_Produto_Fornecedor from D083 where D083_D001_Id='{$mT219['D001_Id']}' and D083_D049_Id='{$mT219['D049_Id']}' and D083_D024_Id='$D024_Id'");
                if (mysql_num_rows($D083) <= 0) {
                    mysql_query("insert into D083 (D083_D049_Id,D083_D001_Id,D083_D024_Id,D083_Data_Cadastro,D083_Flag_Cliente,D083_Codigo_Produto_Fornecedor) values ('{$mT219['D049_Id']}','{$mT219['D001_Id']}','$D024_Id',curdate(),'S','$T219_Codigo_Substituto')");
                } else {
                    $mD083 = mysql_fetch_array($D083);
                    if (trim($mD083['D083_Codigo_Produto_Fornecedor']) == '') {
                        mysql_query("update D083 set D083_Flag_Cliente='S',D083_Codigo_Produto_Fornecedor='$T219_Codigo_Substituto' where D083_Id='{$mD083['D083_Id']}'");
                    }
                }
                */
                require_once("bibliotecas/classes/CAD002.php");
                $CAD002 = new CAD002();
                $CAD002->definirCodigoFornecedor($mT219['D009_Id'], $D024_Id, $T219_Codigo_Substituto, '', true, false);
            }
            //Atualiza as aliquotas com o D006 correto do T219
            $this->atualizarAliquotasProduto($T218_Id, $T219_Id);
            $cSql = mysql_query("call T219_Gravar_Totalizacao('$T219_Id');");
            $cSql = mysql_query("call T218_Gravar_Totalizacao_3('$T218_Id');");
        }
        
        
        // caso o produto possua serviços associados, insere estes serviços na T223         
        $result  = mysql_query("SELECT D001_Id FROM D009 LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id WHERE D009_Id='{$T219_D009_Id}'");
        $retorno = mysql_fetch_array($result);
        $D001_Id = $retorno['D001_Id'];
        $result  = mysql_query("SELECT * FROM D135 LEFT JOIN D112 ON D112_Id=D135_D112_Id WHERE D135_D001_Id='{$D001_Id}'");
        
        if (mysql_num_rows($result) > 0) {
            mysql_query("SET AUTOCOMMIT=0");
            mysql_query("START TRANSACTION");
            while ($D135 = mysql_fetch_array($result)) {
                $cClausula = " insert into T223 (";
                $cClausula .= " T223_T218_Id, ";
                $cClausula .= " T223_D112_Id, ";
                $cClausula .= " T223_D110_Id, ";
                $cClausula .= " T223_T219_Id, ";
                $cClausula .= " T223_Titulo, ";
                $cClausula .= " T223_Descricao, ";
                $cClausula .= " T223_Tempo_Estimado, ";
                $cClausula .= " T223_Quantidade, ";
                $cClausula .= " T223_Quantidade_Confirmada, ";
                $cClausula .= " T223_Valor_Unitario, ";
                $cClausula .= " T223_Valor_Total, ";
                $cClausula .= " T223_D116_Id ";
                $cClausula .= " ) values ( ";
                $cClausula .= " '$T218_Id', ";
                $cClausula .= " '{$D135['D135_D112_Id']}', ";
                $cClausula .= " '{$D135['D112_D110_Id']}', ";
                $cClausula .= " '$T219_Id', ";
                $cClausula .= " '{$D135['D112_Titulo']}', ";
                $cClausula .= " '{$D135['D112_Descricao']}', ";
                $cClausula .= " '{$D135['D112_Tempo_Estimado']}', ";
                $cClausula .= " '1', ";
                $cClausula .= " '1', ";
                $cClausula .= " '{$D135['D112_Valor_Unitario']}', ";
                $cClausula .= " '{$D135['D112_Valor_Unitario']}', ";
                $cClausula .= " '{$idVeiculoOrcamento}' ";
                $cClausula .= " ) ";
                mysql_query($cClausula);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return 'falha ao copiar os serviços do produto';
                }
                
                $T223_Id = $g['mysqlLastId'];
                
                $sql   = "SELECT * FROM D113 WHERE D113_D112_Id='{$D135['D135_D112_Id']}'";
                $array = mysql_query($sql);
                
                while ($row = mysql_fetch_assoc($array)) {
                    // D113_D112_Id, D113_D110_Id, D113_Titulo, D113_Descricao
                    $sql = "INSERT INTO T146 (T146_T223_Id, T146_Titulo, T146_Descricao) VALUES ('" . mysql_real_escape_string($T223_Id) . "', '"
                    //.mysql_real_escape_string($row['D113_D110_Id'])."', '"
                        . mysql_real_escape_string($row['D113_Titulo']) . "', '" . mysql_real_escape_string($row['D113_Descricao']) . "');";
                    mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return 'Erro: ' . mysql_error();
                    }
                }
                
                $sql   = "SELECT * FROM D114 WHERE D114_D112_Id='{$D135['D135_D112_Id']}'";
                $array = mysql_query($sql);
                
                while ($row = mysql_fetch_assoc($array)) {
                    $sql = "INSERT INTO T152 (T152_D037_Id, T152_D009_Id, T152_T223_Id, T152_Quantidade) VALUES ('" . mysql_real_escape_string($row['D114_D037_Id']) . "', '" . mysql_real_escape_string($row['D114_D009_Id']) . "', '" . mysql_real_escape_string($T223_Id) . "', '" . mysql_real_escape_string($row['D114_Quantidade']) . "');";
                    mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return 'Erro: ' . mysql_error();
                    }
                }
                
                
            }
            
            mysql_query("COMMIT");
        }
        
        mysql_query("update T219 set T219_Valor_Preco_Bruto=T219_Valor_Preco_Bruto where T219_Id='$T219_Id'");
        
        return TRUE;
    }
    
    /**
     * excluirServico
     *
     * @param int $T223_Id
     */
    public function excluirServico($T223_Id)
    {
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        mysql_query("DELETE FROM T146 WHERE T146_T223_Id='$T223_Id'");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return 'falha na exclusão dos itens do serviço...';
        }
        mysql_query("DELETE FROM T152 WHERE T152_T223_Id='$T223_Id'");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return 'falha na exclusão dos materiais do serviço...';
        }
        mysql_query("DELETE FROM T223 WHERE T223_Id='$T223_Id'");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return 'falha na exclusão do serviço...';
        }
        
        mysql_query("COMMIT");
        
        return true;
    }
    
    /**
     * excluirProduto
     *
     * @param int $T219_Id
     */
    public function excluirProduto($T219_Id)
    {
        $result = mysql_query("SELECT * FROM T223 WHERE T223_T219_Id='{$T219_Id}'");
        if (mysql_num_rows($result) > 0) {
            while ($T223 = mysql_fetch_array($result)) {
                $this->excluirServico($T223['T223_Id']);
            }
        }

        $sqlT004 = mysql_query("SELECT * FROM T004 WHERE T004_T219_Id = '{$T219_Id}'");
        if(mysql_num_rows($sqlT004) > 0){
            while($rowT004 = mysql_fetch_array($sqlT004)){
                mysql_query("UPDATE T004 SET T004_T219_Id = 0, T004_Flag_Comprar='S' WHERE T004_Id = '{$rowT004['T004_Id']}'");
            }
        }

        $sqlT006 = mysql_query("SELECT * FROM T006 WHERE T006_T219_Id = '{$T219_Id}'");
        if(mysql_num_rows($sqlT006) > 0){
            while($rowT006 = mysql_fetch_array($sqlT006)){
                mysql_query("UPDATE T006 SET T006_T219_Id = 0 WHERE T006_Id = '{$rowT006['T006_Id']}'");
            }
        }
        
        mysql_query("DELETE FROM T219 WHERE T219_Id='{$T219_Id}'");
        if (mysql_error()) {
            return 'falha na exclusão do produto...';
        } else {
            return true;
        }
    }
    
    /**
     * cancelarOrcamento
     *
     * @param Form $form
     */
    public function cancelarOrcamento($form)
    {
        $T218_Id                           = $form->campoValorEnviado('T218_Id');
        $T218_Observacao_Orcamento_Perdido = $form->campoValorEnviado('T218_Observacao_Orcamento_Perdido');
        $T218_D047_Id                      = $form->campoValorEnviado('T218_D047_Id');
        
        if (empty($T218_Id)) {
            return "T218_Id não existe";
        }
        
        $T218_Observacao_Orcamento_Perdido = AddSlashes($T218_Observacao_Orcamento_Perdido);
        $T218_Observacao_Orcamento_Perdido = Trim($T218_Observacao_Orcamento_Perdido);
        
        if (empty($T218_Observacao_Orcamento_Perdido)) {
            return "Preencha o campo Observação.";
        }
        
        if (strlen(trim($T218_Observacao_Orcamento_Perdido)) < 20) {
            return "A Observação deve ter mo ninimo 20 charateres";
        }
        
        //Cancelando os itens da cotação, e desvinculando o orçamento e o pedido da cotação
        $sqlT219 = mysql_query("SELECT T219_Id FROM T219 WHERE T219_T218_Id = '{$T218_Id}'");
        while($rowT219 = mysql_fetch_array($sqlT219)){
            $cSql  = mysqli_query("UPDATE T219 SET T219_Flag_Cancelado = 'S', T219_T004_Id = 0, T219_T006_Id = 0 WHERE T219_Id = '{$rowT219['T219_Id']}'");
            $cErro = mysql_error();
            if (!empty($cErro)) {
                return "Ocorreu um erro ao cancelar a cotação. Por favor, contate o suporte técnico.";
            }
            
             $sqlT004 = mysql_query("SELECT * FROM T004 WHERE T004_T219_Id = '{$rowT219['T219_Id']}'");
            if(mysql_num_rows($sqlT004) > 0){
                while($rowT004 = mysql_fetch_array($sqlT004)){
                    mysql_query("UPDATE T004 SET T004_T219_Id = 0 WHERE T004_Id = '{$rowT004['T004_Id']}'");
                }
            }

            $sqlT006 = mysql_query("SELECT * FROM T006 WHERE T006_T219_Id = '{$rowT219['T219_Id']}'");
            if(mysql_num_rows($sqlT006) > 0){
                while($rowT006 = mysql_fetch_array($sqlT006)){
                    mysql_query("UPDATE T006 SET T006_T219_Id = 0 WHERE T006_Id = '{$rowT006['T006_Id']}'");
                }
            }
        }

        $cClausula = " update T218 set ";
        $cClausula .= " T218_Observacao_Orcamento_Perdido='{$T218_Observacao_Orcamento_Perdido}',  ";
        $cClausula .= " T218_D047_Id='{$T218_D047_Id}',  ";
        $cClausula .= " T218_Flag_Perdido='S'  ";
        $cClausula .= " where T218_Id='{$T218_Id}' ";
        $cSql  = mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "Ocorreu um erro ao cancelar a cotação.(2) Por favor, contate o suporte técnico.";
        } else {
            return true;
        }
    }
    
    /**
     * calcularParcelaCombo
     *
     * @param string $parcela
     * @param string $valorTotal
     * @param string $tipoJuros
     * @param string $taxaJuros
     * @param string $parcelasIsentas
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
     * calcularParcelaCombo
     *
     * @param Form $form
     */
    public function inserirParcela($form)
    {
        global $g;
        
        $T218_Id        = $form['T218_Id'];
        $intervaloDias  = $form['IntervalorDias'];
        $portador       = $form['D031_Id'];
        $Total_Parcelas = $form['Total_Parcelas'];
        
        $diaFixo                = $form['diaFixo'];
        $primeiroVencimentoNum  = $form['primeiroVencimentoNum'];
        $primeiroVencimentoData = $form['primeiroVencimentoData'];
        
        $primeiroVencimentoNum = empty($primeiroVencimentoNum) ? $g['conf']->camposValorPadrao('primeiroVencimentoNum') : $primeiroVencimentoNum;
        $intervaloDias         = empty($intervaloDias) ? $g['conf']->camposValorPadrao('IntervalorDias') : $intervaloDias;
        
        
        // se foi definido um intervalo de dias
        if ($intervaloDias > 0) {
            $valorNovo  = 0;
            $query      = "SELECT * FROM T218 WHERE T218_Id='{$T218_Id}'";
            $retorno    = mysql_query($query);
            $linha      = mysql_fetch_assoc($retorno);
            $valorTotal = $linha['T218_Valor_Total'];
            
            $query            = "SELECT * FROM T220 WHERE T220_T218_Id='{$T218_Id}' ORDER BY T220_Id DESC";
            $retorno          = mysql_query($query);
            $linha            = mysql_fetch_assoc($retorno);
            $prazoUltima      = $linha['T220_Prazos_Dias'];
            $numeroUltima     = $linha['T220_Numero_Parcela'];
            $numeroNova       = $numeroUltima + 1;
            $VencimentoUltima = $linha['T220_Data_Vencimento'];
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
            
            $cClausula = "insert into T220 (";
            $cClausula .= "T220_T218_Id,";
            $cClausula .= "T220_D031_Id,";
            $cClausula .= "T220_Numero_Parcela,";
            $cClausula .= "T220_Prazos_Dias,";
            $cClausula .= "T220_Data_Vencimento,";
            $cClausula .= "T220_Valor_Parcela";
            $cClausula .= ") values (";
            $cClausula .= "'$T218_Id',";
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
            $query      = "SELECT * FROM T218 WHERE T218_Id='{$T218_Id}'";
            $retorno    = mysql_query($query);
            $linha      = mysql_fetch_assoc($retorno);
            $valorTotal = $linha['T218_Valor_Total'];
            
            $query            = "SELECT * FROM T220 WHERE T220_T218_Id='{$T218_Id}' ORDER BY T220_Id DESC";
            $retorno          = mysql_query($query);
            $linha            = mysql_fetch_assoc($retorno);
            $prazoUltima      = $linha['T220_Prazos_Dias'];
            $numeroUltima     = $linha['T220_Numero_Parcela'];
            $numeroNova       = $numeroUltima + 1;
            $VencimentoUltima = $linha['T220_Data_Vencimento'];
            if (mysql_num_rows($retorno) == 0) {
                if (trim($primeiroVencimentoNum) != "") {
                    $date     = strtotime("+$primeiroVencimentoNum day", strtotime(date("Y-m-d")));
                    $dataNova = date("Y-m-d", $date);
                } else if (trim($primeiroVencimentoData) != "") {
                    $dataNova = gCorrigeDataInverte($primeiroVencimentoData);
                }
                $valorNovo = $valorTotal;
            } else {
                $ano = date("Y", strtotime($VencimentoUltima));
                $mes = date("m", strtotime($VencimentoUltima));
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
                $dataNova  = "$ano-$mes-$diaFixo";
                $valorNovo = 0;
            }
            
            $cClausula = "insert into T220 (";
            $cClausula .= "T220_T218_Id,";
            $cClausula .= "T220_D031_Id,";
            $cClausula .= "T220_Numero_Parcela,";
            $cClausula .= "T220_Data_Vencimento,";
            $cClausula .= "T220_Prazos_Dias,";
            $cClausula .= "T220_Valor_Parcela";
            $cClausula .= ") values (";
            $cClausula .= "'$T218_Id',";
            $cClausula .= "'$portador',";
            $cClausula .= "'$numeroNova',";
            $cClausula .= "'$dataNova',";
            $cClausula .= "DATEDIFF('$dataNova',current_date()),";
            $cClausula .= "'$valorNovo'";
            $cClausula .= ")";
            
            mysql_query($cClausula);
            $erro = mysql_error();
        }
        
        if (empty($erro))
            return true;
        else
            return false;
        
    }
    
    /**
     * motivoPedidoLiberacao
     *
     * @param int $T218_Id
     * @param boolean $restricaoAtual
     */
    public function motivoPedidoLiberacao($T218_Id, $restricaoAtual = false)
    {
        global $g;
        $r_acaoId = $T218_Id;
        
        //Calcula Desconto por alçada
        $descontoOrcamento          = $this->calculaPercentualDescontoOrcamento($r_acaoId);
        $descontoVendedor           = gCorrigeNumeroInverte($g['conf']->ini('alcadaDescontoVendedor'));
        $descontoSupervisor         = gCorrigeNumeroInverte($g['conf']->ini('alcadaDescontoSupervisor'));
        //Calcula prazo médio por alçada
        $prazoMedioAlcadaVendedor   = $this->calculaPrazoMedioAlcadaVendedor($r_acaoId);
        $prazoMedioAlcadaSupervisor = $this->calculaPrazoMedioAlcadaSupervisor($r_acaoId);
        $retorno                    = mysql_query("SELECT ifnull(AVG(T220_Prazos_Dias),0.00) as PrazoMedio FROM T220 WHERE T220_T218_Id='{$r_acaoId}'");
        $T218                       = mysql_fetch_array($retorno);
        $retornoT218                = mysql_query("SELECT T218_Valor_Total_Produtos,
                                        T218_Valor_Total_Produtos_Confirmado,
                                        T218_Liberou_Alcada_Valor_Total,
                                        T218_Liberou_Alcada_C007_Id,
                                        T218_Liberou_Alcada_Data,
                                        T218_Liberou_Alcada_Desconto,
                                        T218_Liberou_Alcada_Prazo_Medio 
                                        FROM T218 
                                        WHERE T218_Id='{$r_acaoId}'");
        $mT218                      = mysql_fetch_array($retornoT218);
        
        $prazoMedio         = $T218['PrazoMedio'];
        $retornoSaldoMinimo = $this->verificaFuturoSaldoProduto($r_acaoId, $restricaoAtual);
        
        $totalConfirmado = $mT218['T218_Valor_Total_Produtos_Confirmado'];
        $totalOrcamento  = $mT218['T218_Valor_Total_Produtos'];
        $total           = '';
        if ($totalConfirmado <= 0) {
            $total = $totalOrcamento;
        } else {
            $total = $totalConfirmado;
        }
        
        $motivoPedidoLiberacao = "";
        if ($retornoSaldoMinimo > 0) {
            $motivoPedidoLiberacao .= "*Limite de estoque mínimo ultrapassado.";
        }
        //Verificações de alçada
        if (($descontoOrcamento <= $descontoVendedor) && ($prazoMedio <= $prazoMedioAlcadaVendedor) && ($retornoSaldoMinimo == 0)) {
            
            // NÃO PRECISA DE LIBERAÇÃO DE DESCONTO E PRAZO MÉDIO
            
        } elseif (($descontoOrcamento <= $descontoSupervisor) && ($prazoMedio <= $prazoMedioAlcadaSupervisor)) {
            //Constrói variável motivo liberação
            if ($mT218['T218_Liberou_Alcada_C007_Id'] > 0 && $mT218['T218_Liberou_Alcada_Data'] > 0 && ($mT218['T218_Liberou_Alcada_Desconto'] >= 0 || $mT218['T218_Liberou_Alcada_Prazo_Medio'] >= 0)) {
                
                if ($mT218['T218_Liberou_Alcada_Valor_Total'] != $total || $mT218['T218_Liberou_Alcada_Prazo_Medio'] != $prazoMedio) {
                    
                    if (($descontoOrcamento > $descontoVendedor) && $g['C007']['C007_Flag_Vendedor'] == 'V') {
                        
                        $motivoPedidoLiberacao .= "\n*Desconto usado é maior que o permitido. ";
                        
                    }
                    
                    if (($prazoMedio > $prazoMedioAlcadaVendedor) && $g['C007']['C007_Flag_Vendedor'] == 'V') {
                        
                        $motivoPedidoLiberacao .= "\n*Prazo médio é maior que o permitido. ";
                        
                    }
                }
            }
            // CASO NÃO TENHA ENTRADO NENHUMA VEZ NA LIBERAÇÃO
        } else if (($descontoOrcamento > $descontoVendedor) && $g['C007']['C007_Flag_Vendedor'] == 'V' && $mT218['T218_Liberou_Alcada_Valor_Total'] != $total) {
            
            $motivoPedidoLiberacao .= "\n*Desconto usado é maior que o permitido. ";
            
        } else if (($prazoMedio > $prazoMedioAlcadaVendedor) && $g['C007']['C007_Flag_Vendedor'] == 'V' && $mT218['T218_Liberou_Alcada_Prazo_Medio'] != $prazoMedio) {
            
            $motivoPedidoLiberacao .= "\n*Prazo médio é maior que o permitido. ";
            
        } else {
            //Constrói variável motivo liberação
            if ($mT218['T218_Liberou_Alcada_C007_Id'] > 0 && $mT218['T218_Liberou_Alcada_Data'] > 0 && ($mT218['T218_Liberou_Alcada_Desconto'] >= 0 || $mT218['T218_Liberou_Alcada_Prazo_Medio'] >= 0)) {
                
                if ($mT218['T218_Liberou_Alcada_Valor_Total'] != $total || $mT218['T218_Liberou_Alcada_Prazo_Medio'] != $prazoMedio) {
                    
                    if (($descontoOrcamento > $descontoSupervisor) && $g['C007']['C007_Flag_Vendedor'] != 'D') {
                        
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
     * calcularParcelaSimulacao
     *
     * @param Form $form
     */
    public function calcularParcelaSimulacao($form)
    {
        global $g;
        $T218_Id     = $form['T218_Id'];
        $numParcelas = $form['Total_Parcelas'];
        
        if ($numParcelas == "0")
            return '';
        
        $query      = "SELECT * FROM T218 WHERE T218_Id='{$T218_Id}'";
        $retorno    = mysql_query($query);
        $linha      = mysql_fetch_assoc($retorno);
        $valorTotal = $linha['T218_Valor_Total'];
        
        return gCorrigeNumero($valorTotal / $numParcelas);
    }
    
    /**
     * est015atualizarParcelas
     *
     * @param Form $form
     */
    public function est015atualizarParcelas($form)
    {
        global $g;
        
        $T218_Id                = $form->campoValorEnviado('T218_Id');
        $Total_Parcelas         = $form->campoValorEnviado('Total_Parcelas');
        $IntervalorDias         = $form->campoValorEnviado('IntervalorDias');
        $diaFixo                = $form->campoValorEnviado('diaFixo');
        $primeiroVencimentoNum  = $form->campoValorEnviado('primeiroVencimentoNum');
        $primeiroVencimentoData = $form->campoValorEnviado('primeiroVencimentoData');
        $numeroParcelas         = $form->campoValorEnviado('Total_Parcelas');
        $D031_Id                = $form->campoValorEnviado('Portador_D031_Id');
        $primeiroVencimentoTipo = $form->campoValorEnviado('selecionaPrimeiroVencimento');
        $diasVctoTipo           = $form->campoValorEnviado('selecionaDias');
        $campoUsarFreteParcela  = $form->campoValorEnviado('usarFreteParcela');
        $campoUsarSTParcela     = $form->campoValorEnviado('usarSTParcela');
        $campoUsarIPIParcela    = $form->campoValorEnviado('usarIPIParcela');
        
        if (($primeiroVencimentoData != "") || (!empty($primeiroVencimentoData))) {
            $primeiroVencimentoValor = $primeiroVencimentoData;
        } else if (($primeiroVencimentoNum != "") || (!empty($primeiroVencimentoNum))) {
            $primeiroVencimentoValor = $primeiroVencimentoNum;
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
        
        $cSql_T218 = mysql_query("select * from T218 where T218_Id='{$T218_Id}'");
        $mT218     = mysql_fetch_array($cSql_T218);
        
        // se for "a combinar" remove as parcelas que ja existem
        if ($numeroParcelas == '0') {
            mysql_query("delete T220 from T220 where T220_T218_Id='{$T218_Id}'");
            if (mysql_error())
                return mysql_error();
            else
                return true;
        }
        
        /* campo T007_T218_Id não existe na tabela T007 (copiado do sistema antigo
         * 
         * ) 
         $sql=mysql_query("select T007_Id from T007 where T007_T218_Id='{$T218_Id}' and T007_Flag_Cancelada!='S'");
         if (mysql_num_rows($sql)>0) {
         return 'Nota Fiscal já foi impressa, pedido não pode ser alterado';
         }
         */
        
        //-- PEGA O VALOR TOTAL A VISTA
       /* $cSql_T220           = mysql_query("select SUM(T220_Valor_Parcela) from T220 left join D031 on D031_Id=T220_D031_Id where T220_T218_Id='{$T218_Id}'");
        $mT220_Total_A_Vista = mysql_fetch_array($cSql_T220);*/
        
        
        $cSql_T220 = mysql_query("select * from T220 left join D031 on D031_Id=T220_D031_Id where T220_T218_Id='{$T218_Id}'");
        while ($mT220 = mysql_fetch_array($cSql_T220)) {
            $mPrazoAntigo[$mT220['T220_Numero_Parcela']]    = $mT220['T220_Prazos_Dias'];
            $mPortadorAntigo[$mT220['T220_Numero_Parcela']] = $mT220['T220_D031_Id'];
        }
        
        mysql_query("delete T220 from T220 where T220_T218_Id='{$T218_Id}'");
        
        
        $Valor_Total_IPI       = $mT218['T218_Valor_Total_IPI'];
        $Valor_Total_Orcamento = $mT218['T218_Valor_Total'];
        
        /**
         * Caso a variável global usarFretePrimeiraParcela for igual a SIM, está tirando o frete do valor total para adicionar a primeira parcela
         */
        $usarFretePrimeiraParcela = $g['C031']['usarFretePrimeiraParcela'];
        if ($usarFretePrimeiraParcela == "S" && $campoUsarFreteParcela == "on") {
            $Valor_Total_Orcamento = ($Valor_Total_Orcamento - $mT218['T218_Valor_Frete']);
        }
        
        if ($campoUsarSTParcela == "on") {
            $Valor_Total_Orcamento = ($Valor_Total_Orcamento - $mT218['T218_Valor_ST']);
        }
        
        if ($campoUsarIPIParcela == "on") {
            $Valor_Total_Orcamento = ($Valor_Total_Orcamento - $mT218['T218_Valor_Total_IPI']);
        }
        
        if ($Total_Parcelas > 0) {
            //$Valor_Titulo_Parcela = ( $Valor_Total_Orcamento - $Valor_Total_IPI ) / $Total_Parcelas;      
            //$Valor_Total_Titulo = ( ( round($Valor_Titulo_Parcela,2) * $Total_Parcelas ) + $Valor_Total_IPI );
            
            // verificar se deve adicionar juro na parcela 
            $C004              = mysql_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
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
        if ($IntervalorDias >= 0) {
            // variavel para guardar a diferença de dias entre o intervalo de dias e o dia da primeira parcela  
            $acrescimo = 0;
            for ($x = 1; $x <= $Total_Parcelas; $x++) {
                if ($x == 1) {
                    //$Valor_Titulo = ($Valor_Titulo_Parcela + $Valor_Total_IPI) + $Diferenca_Primeira_Parcela;
                    $Valor_Titulo = ($Valor_Titulo_Parcela) + $Diferenca_Primeira_Parcela;
                } else {
                    $Valor_Titulo = $Valor_Titulo_Parcela;
                }
                if ($D031_Id > 0) {
                    $mPortadorAntigo[$x] = $D031_Id;
                }
                
                // se for a primeira parcela
                if ($x == 1) {
                    // se o primeiro Vencimento foi preenchido em dias, atribui ele
                    if (trim($primeiroVencimentoNum) != "") {
                        $mPrazoAntigo[$x] = $primeiroVencimentoNum;
                        $acrescimo        = $primeiroVencimentoNum;
                        // se o primeiro Vencimento foi preenchido com uma data, transforma em intervalo e atribui
                    } else if (trim($primeiroVencimentoData) != "") {
                        $date1            = new \DateTime(date("Y-m-d"));
                        $date2            = new \DateTime(gCorrigeDataInverte($primeiroVencimentoData));
                        $interval         = $date1->diff($date2);
                        $mPrazoAntigo[$x] = $interval->days;
                        $acrescimo        = $interval->days;
                    }
                } else {
                    $mPrazoAntigo[$x] = (($IntervalorDias * ($x - 1)) + $acrescimo);
                }
                
                /**
                 * Caso a variável global usarFretePrimeiraParcela for igual a SIM, está tirando o frete do valor e fazendo a soma na primeira parcela
                 */
                if ($usarFretePrimeiraParcela == "S" && $x == 1 && $campoUsarFreteParcela == "on") {
                    $Valor_Titulo += $mT218['T218_Valor_Frete'];
                }
                
                if ($x == 1 && $campoUsarSTParcela == "on") {
                    $Valor_Titulo += $mT218['T218_Valor_ST'];
                }
                if ($x == 1 && $campoUsarIPIParcela == "on") {
                    $Valor_Titulo += $mT218['T218_Valor_Total_IPI'];
                }
                
                $cClausula = "insert into T220 (";
                $cClausula .= "T220_T218_Id,";
                $cClausula .= "T220_D031_Id,";
                $cClausula .= "T220_Numero_Parcela,";
                $cClausula .= "T220_Prazos_Dias,";
                $cClausula .= "T220_Data_Vencimento,";
                $cClausula .= "T220_Taxa_Juros,";
                $cClausula .= "T220_Valor_Juros,";
                $cClausula .= "T220_Valor_Parcela";
                $cClausula .= ") values (";
                $cClausula .= "'$T218_Id',";
                $cClausula .= "'$mPortadorAntigo[$x]',";
                $cClausula .= "'$x',";
                $cClausula .= "'$mPrazoAntigo[$x]',";
                $cClausula .= "DATE_ADD(current_date(),INTERVAL $mPrazoAntigo[$x] DAY),";
                $cClausula .= "'$taxaJurosParcela',";
                $cClausula .= "'$valorJurosParcela',";
                $cClausula .= "'$Valor_Titulo'";
                $cClausula .= ")";
                mysql_query($cClausula);
                
                if (mysql_error()) {
                    return "1. " . mysql_error();
                }
            }
            // ou se foi colocado um dia fixo   
        } else if ($diaFixo > 0) {
            if ($diaFixo > 31)
                return "Por favor, insira um dia válido";
            $mes     = date("m");
            $ano     = date("Y");
            $validou = checkdate($mes, $diaFixo, $ano);
            
            while (!$validou) {
                $diaFixo--;
                $validou = checkdate($mes, $diaFixo, $ano);
            }
            $primeiraData = "$ano-$mes-$diaFixo";
            
            for ($x = 1; $x <= $Total_Parcelas; $x++) {
                if ($x == 1) {
                    //$Valor_Titulo = ($Valor_Titulo_Parcela + $Valor_Total_IPI) + $Diferenca_Primeira_Parcela;
                    $Valor_Titulo = ($Valor_Titulo_Parcela) + $Diferenca_Primeira_Parcela;
                } else {
                    $Valor_Titulo = $Valor_Titulo_Parcela;
                }
                if ($D031_Id > 0) {
                    $mPortadorAntigo[$x] = $D031_Id;
                }
                
                $dataVencimento = "0";
                $dataPrazoDias  = date("Y-m-d");
                $numMeses       = $x;
                // se for a primeira parcela
                if ($x == 1) {
                    // se o primeiro Vencimento foi preenchido em dias, soma esses dias a data atual
                    if (trim($primeiroVencimentoNum) != "") {
                        $date           = strtotime("+$primeiroVencimentoNum day", strtotime(date("Y-m-d")));
                        $dataVencimento = date("Y-m-d", $date);
                        // se o primeiro Vencimento foi preenchido com uma data, atribui
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
                    // calcula a data de Vencimento para depois
                    // poder calcular os dias até o Vencimento da parcela (T220_Prazos_Dias)                        
                    $date           = strtotime("+$numMeses month", strtotime($dataVencimento));
                    $dataPrazoDias  = date("Y-m-d", $date);
                    $numMeses       = $x - 1;
                }
                
                
                $cClausula = "insert into T220 (";
                $cClausula .= "T220_T218_Id,";
                $cClausula .= "T220_D031_Id,";
                $cClausula .= "T220_Numero_Parcela,";
                $cClausula .= "T220_Data_Vencimento,";
                $cClausula .= "T220_Prazos_Dias,";
                $cClausula .= "T220_Taxa_Juros,";
                $cClausula .= "T220_Valor_Juros,";
                $cClausula .= "T220_Valor_Parcela";
                $cClausula .= ") values (";
                $cClausula .= "'$T218_Id',";
                $cClausula .= "'$mPortadorAntigo[$x]',";
                $cClausula .= "'$x',";
                $cClausula .= "DATE_ADD('$dataVencimento',INTERVAL $numMeses MONTH),";
                $cClausula .= "DATEDIFF('$dataPrazoDias',current_date()),";
                $cClausula .= "'$taxaJurosParcela',";
                $cClausula .= "'$valorJurosParcela',";
                $cClausula .= "'$Valor_Titulo'";
                $cClausula .= ")";
                mysql_query($cClausula);
                
                if (mysql_error()) {
                    return "2. " . mysql_error();
                }
            }
        }
        
        $sqlT218 = <<<EOT
            UPDATE T218 SET
            T218_Pgto_Parcelas='{$Total_Parcelas}',
            T218_Pgto_Primeiro_Vcto_Tipo='{$primeiroVencimentoTipo}',
            T218_Pgto_Primeiro_Vcto_Valor='{$primeiroVencimentoValor}',
            T218_Pgto_Dias_Vcto_Tipo='{$diasVctoTipo}',
            T218_Pgto_Dias_Vcto_Valor='{$diasVctoValor}',
            T218_Pgto_Portador='{$D031_Id}',
            T218_Pgto_Data=CURRENT_TIMESTAMP(),
            T218_Pgto_C007_Id='{$g['usuarioAtual']}'
            WHERE T218_Id='{$T218_Id}'
EOT;
        mysql_query("call T218_Gravar_Totalizacao_3('{$T218_Id}')");
        $resultT218 = mysql_query($sqlT218);
        if (mysql_error()) {
            return mysql_error();
        }
        return true;
    }
    
    /**
     * est015AtualizarParcelasGrid
     *
     * @param int $T220_Id
     * @param string $campoEditado
     * @param string $valorAntigo
     * @param string $novoValor
     */
    public function est015AtualizarParcelasGrid($T220_Id, $campoEditado, $valorAntigo, $novoValor)
    {
        
        $query            = "SELECT * FROM T220 left join T218 on T220_T218_Id='{$T220_Id}' WHERE T220_Id='{$T220_Id}'";
        $retorno          = mysql_query($query);
        $linha            = mysql_fetch_assoc($retorno);
        $dataVelhaT218    = $linha['T218_Data_Emissao'];
        $prazosDiasAntigo = $linha['T220_Prazos_Dias'];
        
        if ($campoEditado == 'T220_Prazos_Dias') {
            // if($valorAntigo == $novoValor){
            // return "O prazo é o mesmo";
            // }
            
            $date = new \DateTime($dataVelhaT218);
            $date->modify('+' . $novoValor . ' days');
            $query = "UPDATE T220 SET T220_Data_Vencimento='{$date->format('Y-m-d')}' WHERE T220_Id='{$T220_Id}'";
            mysql_query($query);
            
        } else if ($campoEditado == 'T220_Data_Vencimento') {
            
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
                $query = "UPDATE T220 SET T220_Prazos_Dias='{$total}' WHERE T220_Id='{$T220_Id}'";
                mysql_query($query);
                return true;
            } else if ($timeStampDataAntiga > $timeStampDataNova) {
                $novaData = ($timeStampDataAntiga - $timeStampDataNova);
                $dias     = date('d', $novaData);
                $total    = ($prazosDiasAntigo - $dias);
                $query    = "UPDATE T220 SET T220_Prazos_Dias='{$total}' WHERE T220_Id='{$T220_Id}'";
                mysql_query($query);
                return true;
            }
            
        } else if ($campoEditado == 'T220_Valor_Parcela') {
            
            $query      = "SELECT * FROM T220 WHERE T220_Id='{$T220_Id}'";
            $retorno    = mysql_query($query);
            $linha      = mysql_fetch_assoc($retorno);
            $T218_Id    = $linha['T220_T218_Id'];
            $numParcela = $linha['T220_Numero_Parcela'];
            
            /* total antigo, pegando o valor do orçamento
            $query = "SELECT T218_Valor_Total FROM T218 WHERE T218_Id='{$T218_Id}'";
            $retorno=mysql_query($query);       
            $linha = mysql_fetch_assoc($retorno);   
            $valorTotal=$linha['T218_Valor_Total'];
            */
            
            // pega o valor total com juros (se houver)
            // sem a parcela alterada, e no fim soma com o valor antigo, para pegar o valor total com juros original
            $query      = "SELECT SUM(T220_Valor_Parcela) FROM T220 WHERE T220_T218_Id='{$T218_Id}' AND T220_Id!='{$T220_Id}'";
            $retorno    = mysql_query($query);
            $linha      = mysql_fetch_assoc($retorno);
            $valorTotal = $linha['SUM(T220_Valor_Parcela)'] + gCorrigeNumeroInverte($valorAntigo);
            
            $query                  = "SELECT SUM(T220_Valor_Parcela) FROM T220 WHERE T220_T218_Id='{$T218_Id}' AND T220_Numero_Parcela<='{$numParcela}'";
            $retorno                = mysql_query($query);
            $linha                  = mysql_fetch_assoc($retorno);
            $valorParcelasRestantes = $linha['SUM(T220_Valor_Parcela)'];
            $valorRestante          = $valorTotal - $valorParcelasRestantes;
            
            $query             = "SELECT * FROM T220 WHERE T220_T218_Id='{$T218_Id}' AND T220_Numero_Parcela>'{$numParcela}'";
            $retorno           = mysql_query($query);
            $parcelasRestantes = mysql_num_rows($retorno);
            //se ainda tiverem parcelas depois da editada, divide o valor restante entre elas
            if ($parcelasRestantes != 0) {
                $valorParcelasRestantes = $valorRestante / $parcelasRestantes;
                if ($valorParcelasRestantes < 0) {
                    $query = "UPDATE T220 SET T220_Valor_Parcela='{$valorAntigo}' WHERE T220_Id='{$T220_Id}'";
                    mysql_query($query);
                    return 'O valor inserido irá gerar parcelas negativas... Favor alterar novamente';
                }
                
                while ($linha = mysql_fetch_assoc($retorno)) {
                    $query = "UPDATE T220 SET T220_Valor_Parcela='{$valorParcelasRestantes}' WHERE T220_Id='{$linha['T220_Id']}'";
                    mysql_query($query);
                }
            }
            return true;
            
        }
        
        return true;
    }
    
    /**
     * gravarDesconto
     *
     * @param int $T218_Id
     * @param string $T218_Valor_Desconto
     * @param string $T218_Valor_Desconto_Confirmado
     * @param string $Campo
     */
    public function gravarDesconto($T218_Id, $T218_Valor_Desconto, $T218_Valor_Desconto_Confirmado, $Campo)
    {
        
        require_once "old/lib/funcoes.php";
        
        if (empty($T218_Id)) {
            return "Nao encontrado o T218_Id.";
        }
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        $sql       = <<<EOT
        select sum(T219_Valor_Total_Preco_Sem_Desconto) as Total_Sem_Desconto,
        sum(T219_Valor_Total_Preco_Sem_Desconto_Confirmado) as Total_Sem_Desconto_Confirmado
        from T219 where T219_T218_Id='$T218_Id' and T219_Flag_Preco_Especial!='O'
EOT;
        $cSql_T219 = mysql_query($sql);
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return '1. Erro: ' . mysql_error();
        }
        $mT219 = mysql_fetch_array($cSql_T219);
        if ($Campo == 'T218_Percentual_Desconto') {
            $Valor = formata_valor_gravar($Valor, 5);
            $sql   = <<<EOT
           "update T218 
            set T218_Percentual_Desconto='$Valor',
                   T218_Percentual_Desconto_Confirmado='$Valor',
                   T218_Valor_Desconto=(T218_Percentual_Desconto/100)*{$mT219['Total_Sem_Desconto']}
             where T218_Id='$T218_Id'"
EOT;
            mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return '2. Erro: ' . mysql_error();
            }
        } else if ($Campo == 'T218_Valor_Desconto') {
            $T218_Valor_Desconto = formata_valor_gravar($T218_Valor_Desconto, 3);
            $sql                 = <<<EOT
           update T218 
           set T218_Valor_Desconto='$T218_Valor_Desconto',
               T218_Valor_Desconto_Confirmado='$T218_Valor_Desconto',
               T218_Percentual_Desconto=(T218_Valor_Desconto/{$mT219['Total_Sem_Desconto']})*100,
               T218_Percentual_Desconto_Confirmado=(T218_Valor_Desconto/{$mT219['Total_Sem_Desconto']})*100
         where T218_Id='$T218_Id'
EOT;
            mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return '3. Erro: ' . mysql_error();
            }
            $cSql_T218 = mysql_query("select * from T218 where T218_Id='$T218_Id'");
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return '4. Erro: ' . mysql_error();
            }
            
        } else if ($Campo == 'T218_Percentual_Desconto_Confirmado') {
            $Valor = formata_valor_gravar($Valor, 5);
            $sql   = <<<EOT
           update T218 
                           set T218_Percentual_Desconto_Confirmado='$Valor',
                               T218_Valor_Desconto_Confirmado=(T218_Percentual_Desconto_Confirmado/100)*{$mT219['Total_Sem_Desconto_Confirmado']}
                         where T218_Id='$T218_Id'
EOT;
            mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return '5. Erro: ' . mysql_error();
            }
        } else if ($Campo == 'T218_Valor_Desconto_Confirmado') {
            $T218_Valor_Desconto_Confirmado = formata_valor_gravar($T218_Valor_Desconto_Confirmado, 3);
            $sql                            = <<<EOT
           update T218 
           set T218_Valor_Desconto_Confirmado='$T218_Valor_Desconto_Confirmado',
               T218_Percentual_Desconto_Confirmado=(T218_Valor_Desconto_Confirmado/{$mT219['Total_Sem_Desconto_Confirmado']})*100
           where T218_Id='$T218_Id'
EOT;
            mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return '6. Erro: ' . mysql_error();
            }
            
        }
        $cSql_T219 = <<<EOT
        select T219_Id from T219 where T219_T218_Id='$T218_Id'
EOT;
        $cSql_T219 = mysql_query($cSql_T219);
        while ($mT219 = mysql_fetch_array($cSql_T219)) {
            $sql = <<<EOT
              call T219_Gravar_Totalizacao('{$mT219['T219_Id']}')
EOT;
            mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return '7. Erro: ' . mysql_error();
            }
        }
        $sql = <<<EOT
        call T218_Gravar_Totalizacao_3('$T218_Id')
EOT;
        mysql_query($sql);
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return '8. Erro: ' . mysql_error();
        } else {
            mysql_query("COMMIT");
            return true;
        }
    }
    
    /**
     * gravarObservacao - Grava a Observação do pagamento
     *
     * @param From $form
     * @param int $T224_Id
     * @return true/string(Erro)
     */
    public function gravarObservacao($form, $T218_Id)
    {
        $obs    = $form['T218_Observacao_Pagamento'];
        $sql    = "update T218 SET T218_Observacao_Pagamento='{$obs}' WHERE T218_Id='$T218_Id'";
        $result = mysql_query($sql);
        if (mysql_error()) {
            return mysql_error();
        } else {
            return true;
        }
        
    }
    
    /**
     * processarNumeroItemOrcamento
     *
     * @param String $T219_Id
     * @return Bool
     */
    public function processarNumeroItemCotacao($T219_Id, $novoNumeroItem)
    {
        global $g;
        $ultimoItem = 0;
        $contadorItem = 1;
        $sql = mysql_query("SELECT T219_Item, T219_Id FROM T219 WHERE T219_T218_Id = (SELECT T219_T218_Id FROM T219 WHERE T219_Id = '{$T219_Id}') AND T219_Id != '{$T219_Id}' ORDER BY T219_Item");
        while($row = mysql_fetch_array($sql)){
            if($contadorItem == $novoNumeroItem){ $contadorItem++; }
            mysqli_query("UPDATE T219 SET T219_Item = '{$contadorItem}' WHERE T219_Id = '{$row['T219_Id']}'");
            $contadorItem++;
        }
        return true;
    }

    /**
     * importarItensOrcamento - Importa Itens de outro orçamento
     *
     * @param Form $form
     * @return true/string(Erro)
     */
    public function importarItensOrcamento($form)
    {
        global $g;
        $T218_Id        = $form->campoValorEnviado('T218_Id');
        $T218_Id_Origem = $form->campoValorEnviado('T218_Id_Origem');
        
        if (empty($T218_Id)) {
            return "Faltou o parametro T218_Id";
        }
        if (empty($T218_Id_Origem)) {
            return "Digite o numero da Cotação";
        }
        
        /*
        $cSql_T224=sql_query("select T224_Id from T224 where T224_T218_Id='$T218_Id' and T224_Flag_Status!=8");
        if (sql_num_rows($cSql_T224)>0) {
        echo "<script>";
        echo "parent.MostrarMensagem('none');";
        echo "parent.MaisOpcoes();";
        echo "parent.document.getElementById('T218_Id_Origem').focus();";
        echo "</script>";
        msg_alerta("PEDIDO J� FOI GERADO, OR�AMENTO N�O PODE SER ALTERADO");
        exit; 
        }
        */
        
        $cSql_T218 = mysql_query("select * from T218 where T218_Id='$T218_Id'");
        $mT218     = mysql_fetch_array($cSql_T218);
        
        /////////////////////////////////////////////////////////////////////////////////
        //-- VERIFICA SE O ITEM EXISTEM NA EMPRESA DE DESTINO
        /////////////////////////////////////////////////////////////////////////////////
        $cSql_T219 = mysql_query("select t2.D009_Id,
                                     D001_Codigo_Produto,
                                     D002_Descricao_Produto
                                from T219
                           left join D009 as t1 on T219_D009_Id=t1.D009_Id
                           left join D049 on D049_Id=t1.D009_D049_Id
                           left join D001 on D001_Id=D049_D001_Id
                           left join D002 on D002_Id=D001_D002_Id
                           left join D009 as t2 on D049_Id=t2.D009_D049_Id and t2.D009_C004_Id='{$g['empresaAtual']}'
                               where T219_T218_Id='$T218_Id'
                                 and t2.D009_Id=0");
        while ($mT219 = mysql_fetch_array($cSql_T219)) {
            $mSemCadastro[] = "{$mT219['D001_Codigo_Produto']} - {$mT219['D002_Descricao_Produto']}";
        }
        if (mysql_num_rows($cSql_T219) > 0) {
            $SemCadastro = implode("/p", $mSemCadastro);
            return "Existem itens sem cadastro na empresa " . $g['empesaAtual'] . " que estão sendo importados da cotação " . $T218_Id_Origem . "<br />" . $SemCadastro;
        }
        
        
        $cSql_T219 = mysql_query("select * from T219 where T219_T218_Id='$T218_Id_Origem'");
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        while ($linhaT219 = mysql_fetch_array($cSql_T219)) {

            if($linhaT219['T219_C004_Id'] != $g['empresaAtual']){

                $D009 = mysql_query("SELECT empresaAtual.D009_Id as D009_Id
                                       FROM D009 AS empresaCotacao
                                  LEFT JOIN D009 AS empresaAtual ON empresaAtual.D009_D049_Id = empresaCotacao.D009_D049_Id AND empresaAtual.D009_C004_Id='{$g['empresaAtual']}'
                                      WHERE empresaCotacao.D009_Id='{$linhaT219['T219_D009_Id']}'"); 

                $erro = mysql_error();
                if (!empty($erro)) {
                    mysql_query("ROLLBACK");
                    return $erro;
                }

                $mD009 = mysql_fetch_assoc($D009);


                if ($mD009['D009_Id']<=0){
                    mysql_query("ROLLBACK");
                    return "Erro ao encontrar o D009_Id da empresa atual, favor informar o suporte técnico";
                }

                $linhaT219['T219_D009_Id'] = $mD009['D009_Id'];
                $linhaT219['T219_C004_Id'] = $g['empresaAtual'];
            }

            $insertT219 = <<<EOT
            insert into T219 (
                T219_C004_Id,
                T219_Item,
                T219_Codigo_Produto,
                T219_Codigo_Substituto,
                T219_Descricao_Produto,
                T219_T218_Id,
                T219_D024_Id,
                T219_D009_Id,
                T219_D037_Id,
                T219_D082_Id,
                T219_Quantidade,
                T219_Valor_Custo_Unitario,
                T219_Valor_Preco_Unitario,
                T219_Valor_Preco_Sem_Desconto_Unitario,
                T219_Valor_Preco_Original,
                T219_Percentual_Desconto,
                T219_Valor_Desconto_Unitario,
                T219_IPV,
                T219_Peso_Unitario,
                T219_D005_Id,
                T219_Classificacao_Fiscal,
                T219_D006_Id,
                T219_Aliquota_IPI,
                T219_Aliquota_ICMS,
                T219_Percentual_Reducao_ICMS,
                T219_Percentual_Reducao_ICMS_ST,
                T219_ST_VA,
                T219_Flag_ST,
                T219_Flag_Situacao_Tributaria,
                T219_ICMS_Inter_Estadual,
                T219_ST_ICMS_Interno,
                T219_Flag_Isento_PIS,
                T219_Aliquota_PIS,
                T219_Situacao_Tributaria_PIS,
                T219_Flag_Isento_COFINS,
                T219_Aliquota_COFINS,
                T219_Flag_Preco_Especial,
                T219_Situacao_Tributaria_COFINS,
                T219_Situacao_Tributaria_IPI,
                T219_D059_Id,
                T219_Observacao,
                T219_Numero_Pedido_Compra,
                T219_Item_Pedido_Compra
            ) VALUES (
                '{$linhaT219['T219_C004_Id']}',
                '{$linhaT219['T219_Item']}',
                '{$linhaT219['T219_Codigo_Produto']}',
                '{$linhaT219['T219_Codigo_Substituto']}',
                '{$linhaT219['T219_Descricao_Produto']}',
                '{$T218_Id}',
                '{$linhaT219['T219_D024_Id']}',
                '{$linhaT219['T219_D009_Id']}',
                '{$linhaT219['T219_D037_Id']}',
                '{$linhaT219['T219_D082_Id']}',
                '{$linhaT219['T219_Quantidade']}',
                '{$linhaT219['T219_Valor_Custo_Unitario']}',
                '{$linhaT219['T219_Valor_Preco_Unitario']}',
                '{$linhaT219['T219_Valor_Preco_Sem_Desconto_Unitario']}',
                '{$linhaT219['T219_Valor_Preco_Original']}',
                '{$linhaT219['T219_Percentual_Desconto']}',
                '{$linhaT219['T219_Valor_Desconto_Unitario']}',
                '{$linhaT219['T219_IPV']}',
                '{$linhaT219['T219_Peso_Unitario']}',
                '{$linhaT219['T219_D005_Id']}',
                '{$linhaT219['T219_Classificacao_Fiscal']}',
                '{$linhaT219['T219_D006_Id']}',
                '{$linhaT219['T219_Aliquota_IPI']}',
                '{$linhaT219['T219_Aliquota_ICMS']}',
                '{$linhaT219['T219_Percentual_Reducao_ICMS']}',
                '{$linhaT219['T219_Percentual_Reducao_ICMS_ST']}',
                '{$linhaT219['T219_ST_VA']}',
                '{$linhaT219['T219_Flag_ST']}',
                '{$linhaT219['T219_Flag_Situacao_Tributaria']}',
                '{$linhaT219['T219_ICMS_Inter_Estadual']}',
                '{$linhaT219['T219_ST_ICMS_Interno']}',
                '{$linhaT219['T219_Flag_Isento_PIS']}',
                '{$linhaT219['T219_Aliquota_PIS']}',
                '{$linhaT219['T219_Situacao_Tributaria_PIS']}',
                '{$linhaT219['T219_Flag_Isento_COFINS']}',
                '{$linhaT219['T219_Aliquota_COFINS']}',
                '{$linhaT219['T219_Flag_Preco_Especial']}',
                '{$linhaT219['T219_Situacao_Tributaria_COFINS']}',
                '{$linhaT219['T219_Situacao_Tributaria_IPI']}',
                '{$linhaT219['T219_D059_Id']}',
                '{$linhaT219['T219_Observacao']}',
                '{$linhaT219['T219_Numero_Pedido_Compra']}',
                '{$linhaT219['T219_Item_Pedido_Compra']}'
            );
EOT;
            mysql_query($insertT219);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return mysql_error();
            }
            
            $T219Novo = $g['mysqlLastId'];
            
            $sqlT223 = mysql_query("select * from T223 where T223_T219_Id='{$linhaT219['T219_Id']}'");
            
            while ($linhaT223 = mysql_fetch_array($sqlT223)) {
                
                $cClausula = <<<EOT
                    insert into T223 (
                        T223_T218_Id,
                        T223_D112_Id,
                        T223_D110_Id,
                        T223_T219_Id,
                        T223_Titulo,
                        T223_Descricao,
                        T223_Tempo_Estimado,
                        T223_Quantidade,
                        T223_Quantidade_Confirmada,
                        T223_Valor_Unitario,
                        T223_Valor_Total,
                        T223_D116_Id
                    ) values (
                        '{$T218_Id}',
                        '{$linhaT223['T223_D112_Id']}',
                        '{$linhaT223['T223_D110_Id']}',
                        '{$T219Novo}',
                        '{$linhaT223['T223_Titulo']}',
                        '{$linhaT223['T223_Descricao']}',
                        '{$linhaT223['T223_Tempo_Estimado']}',
                        '{$linhaT223['T223_Quantidade']}',
                        '{$linhaT223['T223_Quantidade_Confirmada']}',
                        '{$linhaT223['T223_Valor_Unitario']}',
                        '{$linhaT223['T223_Valor_Total']}',
                        '{$mT218['T218_D116_Id']}'
                    )
EOT;
                
                mysql_query($cClausula);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return 'falha ao copiar os serviços do produto';
                }
            }
        }
        
        mysql_query("call T218_Gravar_Totalizacao_3($T218_Id)");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return 'falha ao gravar totalização';
        }
        
        mysql_query("COMMIT");
        
        return true;
        
        //o método de atualizar alíquotas agora será realziado no form em etapas - Lígia Nakazato 22/08/2018
        //$retorno = $this->atualizarAliquotas($T218_Id);
        
        // if ($retorno === true) {
        //     return true;
        // } else {
        //     return $retorno;
        // }
    }
    
    /*
     * calculaPercentualDescontoOrcamento
     *  retorna o percentual de desconto atual do orçamento
     *  se tiver quantidade confirmada faz só em cima destes
     *  senão, de todos os itens do orçamento
     *
     * @param int $T218_Id
     */
    public function calculaPercentualDescontoOrcamento($T218_Id)
    {
        
        global $g;
        
        $retorno  = mysql_query("SELECT * FROM T218 WHERE T218_Id='{$T218_Id}'");
        $T218     = mysql_fetch_array($retorno);
        $total    = '';
        $desconto = ''; // percentual de desconto
        if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
            $totalConfirmado = $T218['T218_Valor_Total_Confirmado'];
            $totalOrcamento  = $T218['T218_Valor_Total'];
            if ($totalConfirmado <= 0) {
                $total = $totalOrcamento;
                if ($total <= 0) {
                    return 0;
                }
                $val      = '0.00';
                $retorno  = mysql_query("SELECT sum(T219_Valor_Preco_Original_Bruto*T219_Quantidade) as Total_Orcamento, sum(T219_Valor_Preco_Bruto*T219_Quantidade) as Total_Tabela from T219 WHERE T219_T218_Id='{$T218_Id}'");
                $mT219    = mysql_fetch_array($retorno);
                $desconto = 1 - ($mT219['Total_Tabela'] / $mT219['Total_Orcamento']); // calcular o percentual de desconto dos itens confirmados            
                
                $desconto = $desconto * 100;
                
            } else {
                $total    = $totalConfirmado;
                $val      = '0.00';
                $retorno  = mysql_query("SELECT sum(T219_Valor_Preco_Original_Bruto*T219_Quantidade_Confirmada) as Total_Orcamento, sum(T219_Valor_Preco_Bruto*T219_Quantidade_Confirmada) as Total_Tabela from T219 WHERE T219_T218_Id='{$T218_Id}'");
                $mT219    = mysql_fetch_array($retorno);
                $desconto = 1 - ($mT219['Total_Tabela'] / $mT219['Total_Orcamento']); // calcular o percentual de desconto dos itens confirmados            
                
                $desconto = $desconto * 100;
            }
            
            return $desconto;
        } else {
            $totalConfirmado = $T218['T218_Valor_Total_Produtos_Confirmado'];
            $totalOrcamento  = $T218['T218_Valor_Total_Produtos'];
            if ($totalConfirmado <= 0) {
                $total = $totalOrcamento;
                if ($total <= 0) {
                    return 0;
                }
                $val      = '0.00';
                $retorno  = mysql_query("SELECT sum(T219_Valor_Preco_Original*T219_Quantidade) as Total_Orcamento, sum(T219_Valor_Preco_Unitario*T219_Quantidade) as Total_Tabela from T219 WHERE T219_T218_Id='{$T218_Id}'");
                $mT219    = mysql_fetch_array($retorno);
                $desconto = 1 - ($mT219['Total_Tabela'] / $mT219['Total_Orcamento']); // calcular o percentual de desconto dos itens confirmados            
                
                $desconto = $desconto * 100;
                
            } else {
                $total    = $totalConfirmado;
                $val      = '0.00';
                $retorno  = mysql_query("SELECT sum(T219_Valor_Preco_Original*T219_Quantidade_Confirmada) as Total_Orcamento, sum(T219_Valor_Preco_Unitario*T219_Quantidade_Confirmada) as Total_Tabela from T219 WHERE T219_T218_Id='{$T218_Id}'");
                $mT219    = mysql_fetch_array($retorno);
                $desconto = 1 - ($mT219['Total_Tabela'] / $mT219['Total_Orcamento']); // calcular o percentual de desconto dos itens confirmados            
                
                $desconto = $desconto * 100;
            }
            
            return $desconto;
        }
    }

    /**
     * gerarContasAPagar - Método que retorna o maior desconto dado no orçamento
     *
     * @param int $T218_Id
     */
    public function retornaMaiorDescontoOrcamento($T218_Id)
    {
        global $g;
        if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
            
            $T218MaiorDesconto  = mysql_query("SELECT T219_Percentual_Desconto_Bruto FROM T218 LEFT JOIN T219 on T219_T218_Id=T218_Id WHERE T219_T218_Id='{$T218_Id}' ORDER BY T219_Percentual_Desconto_Bruto DESC LIMIT 1");
            $mT218MaiorDesconto = mysql_fetch_array($T218MaiorDesconto);
            $maiorDesconto      = $mT218MaiorDesconto['T219_Percentual_Desconto_Bruto'];
        } else {
            $T218MaiorDesconto  = mysql_query("SELECT T219_Percentual_Desconto FROM T218 LEFT JOIN T219 on T219_T218_Id=T218_Id WHERE T219_T218_Id='{$T218_Id}' ORDER BY T219_Percentual_Desconto_Bruto DESC LIMIT 1");
            $mT218MaiorDesconto = mysql_fetch_array($T218MaiorDesconto);
            $maiorDesconto      = $mT218MaiorDesconto['T219_Percentual_Desconto'];
        }
        return $maiorDesconto;
    }
    
    /**
     * verificaFuturoSaldoProduto
     *
     * @param int $T218_Id
     * @param boolean $restricaoAtua
     */
    public function verificaFuturoSaldoProduto($T218_Id, $restricaoAtual = false)
    {
        global $g;
        $saldoMinimo = $g['C031']['alcadaQuantidadeMinimaEstoque'];
        if ($saldoMinimo <= 0) {
            return 0;
        }
        $sqlT219 = mysql_query("SELECT if(T219_Quantidade_Confirmada>0,T219_Quantidade_Confirmada,T219_Quantidade) as QuantidadeItens, 
                                        D009_Quantidade_Estoque_Liquido,
                                        T219_Liberou_Alcada_Saldo_Futuro,
                                        C007_Flag_Vendedor, 
                                        (D009_Quantidade_Estoque_Liquido-(if(T219_Quantidade_Confirmada>0,T219_Quantidade_Confirmada,T219_Quantidade))) as SaldoFuturo 
                                        FROM T219 
                                        LEFT JOIN D009 on D009_Id=T219_D009_Id 
                                        LEFT JOIN T218 on T218_Id=T219_T218_Id 
                                        LEFT JOIN C007 on C007_Id=T218_C007_Id_Vendedor_Interno 
                                        WHERE T219_T218_Id='{$T218_Id}'");
        
        $retorno = 0;
        if ($restricaoAtual == false) {
            //Neste caso verifica se já foi aprovado
            while ($rowT219 = mysql_fetch_array($sqlT219)) {
                if (($rowT219['SaldoFuturo'] < $saldoMinimo) && ($rowT219['T219_Liberou_Alcada_Saldo_Futuro'] != $rowT219['SaldoFuturo']) && $rowT219['C007_Flag_Vendedor'] == 'V') {
                    $retorno += 1;
                }
            }
        } else {
            while ($rowT219 = mysql_fetch_array($sqlT219)) {
                if (($rowT219['SaldoFuturo'] < $saldoMinimo) && $rowT219['C007_Flag_Vendedor'] == 'V') {
                    $retorno += 1;
                }
            }
        }
        return $retorno;
    }
    
    /**
     * calculaPrazoMedioAlcada
     *
     * @param int $T218_Id
     * @param boolean $viaTablet
     */
    public function calculaPrazoMedioAlcada($T218_Id, $viaTablet)
    {
        
        global $g;
        $retorno = mysql_query("SELECT * FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'");
        $C007    = mysql_fetch_array($retorno);
        
        $retorno = mysql_query("SELECT * FROM T218 WHERE T218_Id='{$T218_Id}'");
        $T218    = mysql_fetch_array($retorno);
        
        $totalConfirmado  = $T218['T218_Valor_Total_Produtos_Confirmado'];
        $totalOrcamento   = $T218['T218_Valor_Total_Produtos'];
        $total            = '';
        $prazoMedioAlcada = '';
        if ($viaTablet == false) {
            if ($totalConfirmado <= 0) {
                $total = $totalOrcamento;
            } else {
                $total = $totalConfirmado;
            }
        } else {
            $total = $T009['T218_Valor_Total'];
        }
        
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
        
        if ($prazoMedioAlcada == '')
            $prazoMedioAlcada = 0;
        
        return $prazoMedioAlcada;
    }
    
    /**
     * calculaPrazoMedioAlcadaVendedor
     *
     * @param int $T218_Id
     * @param boolean $viaTablet
     */
    public function calculaPrazoMedioAlcadaVendedor($T218_Id, $viaTablet = false)
    {
        
        global $g;
        
        $retorno          = mysql_query("SELECT * FROM T218 WHERE T218_Id='{$T218_Id}'");
        $T218             = mysql_fetch_array($retorno);
        $totalConfirmado  = $T218['T218_Valor_Total_Produtos_Confirmado'];
        $totalOrcamento   = $T218['T218_Valor_Total_Produtos'];
        $total            = '';
        $prazoMedioAlcada = '';
        if ($viaTablet == false) {
            if ($totalConfirmado <= 0) {
                $total = $totalOrcamento;
            } else {
                $total = $totalConfirmado;
            }
        } else {
            $total = $T218['T218_Valor_Total'];
        }
        $regrasUsuario = $g['conf']->ini('regrasVendedor');
        
        
        foreach ($regrasUsuario as $regra) {
            if ($total <= $regra['valor']) {
                $prazoMedioAlcada = $regra['pm'];
                break;
            }
        }
        
        if ($prazoMedioAlcada == '')
            $prazoMedioAlcada = 0;
        
        return $prazoMedioAlcada;
    }
    
    /**
     * calculaPrazoMedioAlcadaSupervisor
     *
     * @param int $T218_Id
     * @param boolean $viaTablet
     */
    public function calculaPrazoMedioAlcadaSupervisor($T218_Id, $viaTablet = false)
    {
        
        global $g;
        
        $retorno          = mysql_query("SELECT * FROM T218 WHERE T218_Id='{$T218_Id}'");
        $T218             = mysql_fetch_array($retorno);
        $totalConfirmado  = $T218['T218_Valor_Total_Produtos_Confirmado'];
        $totalOrcamento   = $T218['T218_Valor_Total_Produtos'];
        $total            = '';
        $prazoMedioAlcada = '';
        if ($viaTablet == false) {
            if ($totalConfirmado <= 0) {
                $total = $totalOrcamento;
            } else {
                $total = $totalConfirmado;
            }
        } else {
            $total = $T218['T218_Valor_Total'];
        }
        
        $regrasUsuario = $g['conf']->ini('regrasSupervisor');
        
        
        foreach ($regrasUsuario as $regra) {
            if ($total <= $regra['valor']) {
                $prazoMedioAlcada = $regra['pm'];
                break;
            }
        }
        
        if ($prazoMedioAlcada == '')
            $prazoMedioAlcada = 0;
        
        return $prazoMedioAlcada;
    }
    
    /**
     * enviarParaAnalise - função chamada quando o desconto está fora da alçada do usuário 
     *
     * @param int $T218_Id
     * @param string $alcada
     */
    public function enviarParaAnalise($T218_Id, $alcada)
    {
        $motivoPedidoLiberacao = $this->motivoPedidoLiberacao($T218_Id);
        mysql_query("UPDATE T218 set T218_Flag_Status_Orcamento='L', T218_Flag_Status_Liberacao='1',T218_Flag_PDF_Impresso='N',T218_Motivo_Pedido_Liberacao='{$motivoPedidoLiberacao}' WHERE T218_Id='{$T218_Id}'");
        if (mysql_error())
            return mysql_error();
        else
            return true;
    }
    
    /**
     * est015aplicarDesconto - Método para aplicar desconto em todos os ítens do orçamento
     *
     * @param int $T218_Id
     * @param string $CampoAplicarDesconto
     */
    public function est015aplicarDesconto($T218_Id, $Desconto, $CampoAplicarDesconto)
    {
        global $g;
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        //if(empty($Desconto)){
        //return "Insira um Desconto";
        //}
        //if(empty($T218_Id)){
        //return "Insira um Desconto";
        //}
        $Conteudo_Campo_Editado = $Desconto;
        $sql                    = "SELECT * FROM T219 WHERE T219_T218_Id='{$T218_Id}'";
        $result                 = mysql_query($sql);
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return "Erro: " . mysql_error();
        }
        if (!empty($Conteudo_Campo_Editado)) {
            $Conteudo_Campo_Editado = gCorrigeNumeroInverte($Conteudo_Campo_Editado, 5);
        } else {
            $Conteudo_Campo_Editado = 0;
        }
        if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
            
            while ($mT219 = mysql_fetch_array($result)) {
                
                if ($CampoAplicarDesconto == "tabela") {
                    
                    // se o desconto for em cima do preço de tabela, será aplicado o desconto com base no campo T219_Valor_Preco_Original_Bruto
                    
                    if (number_format($Conteudo_Campo_Editado, 2) != number_format($mT219['T219_Percentual_Desconto_Bruto'], 2)) {
                        if (($mT219['T219_Valor_Preco_Original_Bruto'] - ($mT219['T219_Valor_Preco_Original_Bruto'] * ($Conteudo_Campo_Editado / 100))) < $mT219['T219_Valor_Preco_Unitario']) {
                            //return "Desconto inválido";
                        }
                        $sql = "update T219 set T219_Percentual_Desconto_Bruto='$Conteudo_Campo_Editado',
                                T219_Valor_Preco_Bruto=T219_Valor_Preco_Original_Bruto-(T219_Valor_Preco_Original_Bruto*($Conteudo_Campo_Editado/100)),
                                T219_Valor_Desconto_Unitario_Bruto=(T219_Valor_Preco_Original_Bruto*($Conteudo_Campo_Editado/100))
                          where T219_Id='{$mT219['T219_Id']}'";
                        mysql_query($sql);
                        if (mysql_error()) {
                            mysql_query("ROLLBACK");
                            return "Erro: " . mysql_error();
                        }
                    }
                    //mysql_query("UPDATE T218 SET T218_Valor_Desconto_Manual=(SELECT SUM(T219_Valor_Desconto_Unitario_Bruto) FROM T219 WHERE T219_T218_Id='{$T218_Id}') WHERE T218_Id='{$T218_Id}'");
                    $cSql = mysql_query("call T219_Gravar_Totalizacao('{$mT219['T219_Id']}');");
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return "Erro: " . mysql_error();
                    }
                    
                } else {
                    
                    // se o desconto for em cima do preço do pedido, será aplicado o desconto com base no campo T219_Valor_Preco_Bruto
                    
                    //                  if (($mT219['T219_Valor_Preco_Bruto']-($mT219['T219_Valor_Preco_Bruto']*($Conteudo_Campo_Editado/100)))<$mT219['T219_Valor_Preco_Unitario']){
                    //                      return "Desconto inválido";
                    //                  }
                    $sql = "update T219 set                                 
                            T219_Valor_Preco_Bruto=T219_Valor_Preco_Bruto-(T219_Valor_Preco_Bruto*($Conteudo_Campo_Editado/100)),
                            T219_Percentual_Desconto_Bruto=if(T219_Valor_Preco_Original_Bruto>0,(1-(T219_Valor_Preco_Bruto/T219_Valor_Preco_Original_Bruto))*100,0),
                            T219_Valor_Desconto_Unitario_Bruto=if(T219_Valor_Preco_Original_Bruto>0,(T219_Valor_Preco_Original_Bruto-T219_Valor_Preco_Bruto),0)
                      where T219_Id='{$mT219['T219_Id']}'";
                    mysql_query($sql);
                    $erro = mysql_error();
                    if (!empty($erro)) {
                        mysql_query("ROLLBACK");
                        return "Erro: " . mysql_error();
                    }
                    //mysql_query("UPDATE T218 SET T218_Valor_Desconto_Manual=(SELECT SUM(T219_Valor_Desconto_Unitario_Bruto) FROM T219 WHERE T219_T218_Id='{$T218_Id}') WHERE T218_Id='{$T218_Id}'");
                    $cSql = mysql_query("call T219_Gravar_Totalizacao('{$mT219['T219_Id']}');");
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return "Erro: " . mysql_error();
                    }
                }
            }
            
        } else {
            
            while ($mT219 = mysql_fetch_array($result)) {
                
                if ($CampoAplicarDesconto == "tabela") {
                    
                    // se o desconto for em cima do preço de tabela, será aplicado o desconto com base no campo T219_Valor_Preco_Original
                    if (($mT219['T219_Valor_Preco_Original'] - ($mT219['T219_Valor_Preco_Original'] * ($Conteudo_Campo_Editado / 100))) < $mT219['T219_Valor_Custo_Unitario']) {
                        //return "Desconto inválido";
                    }
                    $sql = "update T219 set T219_Percentual_Desconto='$Conteudo_Campo_Editado',
                            T219_Valor_Preco_Unitario=T219_Valor_Preco_Original-(T219_Valor_Preco_Original*($Conteudo_Campo_Editado/100)),
                            T219_Valor_Desconto_Unitario=(T219_Valor_Preco_Original*($Conteudo_Campo_Editado/100))
                      where T219_Id='{$mT219['T219_Id']}'";
                    mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return "Erro: " . mysql_error();
                    }
                    //mysql_query("UPDATE T218 SET T218_Valor_Desconto_Manual=(SELECT SUM(T219_Valor_Desconto_Unitario) FROM T219 WHERE T219_T218_Id='{$T218_Id}') WHERE T218_Id='{$T218_Id}'");
                    $cSql = mysql_query("call T219_Gravar_Totalizacao('{$mT219['T219_Id']}');");
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return "Erro: " . mysql_error();
                    }
                    
                } else {
                    
                    // se o desconto for em cima do preço do pedido, será aplicado o desconto com base no campo T219_Valor_Preco_Sem_Desconto_Unitario
                    $sql = "update T219 set T219_Percentual_Desconto='$Conteudo_Campo_Editado',
                            T219_Valor_Preco_Unitario=T219_Valor_Preco_Unitario-(T219_Valor_Preco_Unitario*($Conteudo_Campo_Editado/100)),
                            T219_Valor_Desconto_Unitario=(T219_Valor_Preco_Unitario*($Conteudo_Campo_Editado/100))
                      where T219_Id='{$mT219['T219_Id']}'";
                    mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return "Erro: " . mysql_error();
                    }
                    //mysql_query("UPDATE T218 SET T218_Valor_Desconto_Manual=(SELECT SUM(T219_Valor_Desconto_Unitario) FROM T219 WHERE T219_T218_Id='{$T218_Id}') WHERE T218_Id='{$T218_Id}'");
                    $cSql = mysql_query("call T219_Gravar_Totalizacao('{$mT219['T219_Id']}');");
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return "Erro: " . mysql_error();
                    }
                }
            }
        }
        mysql_query("COMMIT");
        return true;
    }
    
    /**
     * est015aplicarDescontoReais
     *
     * @param int $T218_Id
     * @param string $DescontoReais
     */
    public function est015aplicarDescontoReais($T218_Id, $DescontoReais)
    {
        global $g;
        $this->est015aplicarDesconto($T218_Id, '0,00000', 'tabela');
        $T218               = mysql_query("SELECT T218_Valor_Total FROM T218 WHERE T218_Id='{$T218_Id}'");
        $mT218              = mysql_fetch_array($T218);
        $desconto           = gCorrigeNumeroInverte($DescontoReais);
        $percentualDesconto = gCorrigeNumero(($desconto / $mT218['T218_Valor_Total']) * 100, 5);
        return $this->est015aplicarDesconto($T218_Id, $percentualDesconto, 'tabela');
    }
    
    /**
     * est015aplicarDescontoTotal
     *
     * @param int $T218_Id
     * @param string $DescontoTotal
     */
    public function est015aplicarDescontoTotal($T218_Id, $DescontoTotal)
    {
        global $g;
        $this->est015aplicarDesconto($T218_Id, '0,00000', 'tabela');
        $T218     = mysql_query("SELECT T218_Valor_Total_Produtos,T218_Valor_Total FROM T218 WHERE T218_Id='{$T218_Id}'");
        $mT218    = mysql_fetch_array($T218);
        $desconto = gCorrigeNumeroInverte($DescontoTotal);
        if ($desconto > 0) {
            $desconto           = $mT218['T218_Valor_Total'] - $desconto;
            $percentualDesconto = gCorrigeNumero(($desconto / $mT218['T218_Valor_Total']) * 100, 5);
        }
        return $this->est015aplicarDesconto($T218_Id, $percentualDesconto, 'tabela');
    }
    
    /**
     * est015AplicarPercentualDesconto
     *
     * @param int $T218_Id
     * @param string $campoEditado
     * @param string $valorAntigo
     * @param string $novoValor
     */
    public function est015AplicarPercentualDesconto($T219_Id, $campoEditado, $valorAntigo, $novoValor)
    {
        global $g;
        if ($T219_Id <= 0) {
            return "T219_Id nao existe!";
        }
        $Campo_Editado          = $campoEditado;
        $Conteudo_Campo_Editado = $novoValor;
        
        if (empty($Campo_Editado)) {
            return "Campo_Editado nao existe!";
        }
        
        if ($T219_Id <= 0) {
            return "T219_Id nao existe!";
        }
        
        $C007  = mysql_query("select * from C007 where C007_Id='{$g['usuarioAtual']}'");
        $mC007 = mysql_fetch_array($C007);
        
        $cC004 = mysql_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
        $mC004 = mysql_fetch_array($cC004);
        
        $cClausula = " select ";
        $cClausula .= " *  ";
        $cClausula .= " from T219 ";
        $cClausula .= " left join D009 on D009_Id=T219_D009_Id ";
        $cClausula .= " left join D049 on D049_Id=D009_D049_Id ";
        $cClausula .= " left join D001 on D001_Id=D049_D001_Id ";
        $cClausula .= " left join D037 on D037_Id=T219_D037_Id ";
        $cClausula .= " where T219_Id='$T219_Id' ";
        $T219  = mysql_query("$cClausula");
        $mT219 = mysql_fetch_array($T219);
        
        if ($Campo_Editado == 'T219_Valor_Preco_Unitario') {
            
            $Conteudo_Campo_Editado = gCorrigeNumeroInverte($Conteudo_Campo_Editado);
            
            if ($Conteudo_Campo_Editado < $mT219['T219_Valor_Custo_Unitario']) {
                //return 'Preço inválido';
            }
            
            if ($mT219['T219_Valor_Preco_Original'] > 0) {
                $campoUpdate = 'T219_Valor_Preco_Unitario';
            } else {
                $campoUpdate = 'T219_Valor_Preco_Sem_Desconto_Unitario';
            }
            mysql_query("update T219 set {$campoUpdate}='{$Conteudo_Campo_Editado}',
                                        T219_Percentual_Desconto=if(T219_Valor_Preco_Original>0,(1-(T219_Valor_Preco_Unitario/T219_Valor_Preco_Original))*100,0),
                                        T219_Valor_Desconto_Unitario=if(T219_Valor_Preco_Original>0,(T219_Valor_Preco_Original*($Conteudo_Campo_Editado/100)),0)
                              where T219_Id='$T219_Id'");
            
        } elseif ($Campo_Editado == 'T219_Valor_Preco_Bruto') {
            $Conteudo_Campo_Editado = gCorrigeNumeroInverte($Conteudo_Campo_Editado, $mC004['C004_Casas_Decimais_Preco'] + 1);
            
            mysql_query("update T219 set T219_Valor_Preco_Bruto='{$Conteudo_Campo_Editado}',
                                        T219_Percentual_Desconto_Bruto=if(T219_Valor_Preco_Original_Bruto>0,(1-(T219_Valor_Preco_Bruto/T219_Valor_Preco_Original_Bruto))*100,0),
                                        T219_Valor_Desconto_Unitario_Bruto=if(T219_Valor_Preco_Original_Bruto>0,(T219_Valor_Preco_Original_Bruto*($Conteudo_Campo_Editado/100)),0)
                              where T219_Id='$T219_Id'");
        } elseif ($Campo_Editado == 'T219_Percentual_Desconto') {
            //return "$T219_Id, $campoEditado, $valorAntigo, $novoValor, {$mT219['T219_Percentual_Desconto']}";
            //T219_Valor_Preco_Unitario
            if (!empty($Conteudo_Campo_Editado)) {
                $Conteudo_Campo_Editado = gCorrigeNumeroInverte($Conteudo_Campo_Editado, 5);
            } else {
                $Conteudo_Campo_Editado = 0;
            }
            //return "1: ".number_format($Conteudo_Campo_Editado,2)."<br />2: ".number_format($mT219['T219_Percentual_Desconto'],2);
            
            if (($mT219['T219_Valor_Preco_Original'] - ($mT219['T219_Valor_Preco_Original'] * ($Conteudo_Campo_Editado / 100))) < $mT219['T219_Valor_Custo_Unitario']) {
                //return "Desconto inválido";
            }
            mysql_query("update T219 set T219_Percentual_Desconto='$Conteudo_Campo_Editado',
                                        T219_Valor_Preco_Unitario=T219_Valor_Preco_Original-(T219_Valor_Preco_Original*($Conteudo_Campo_Editado/100)),
                                        T219_Valor_Desconto_Unitario=(T219_Valor_Preco_Original*($Conteudo_Campo_Editado/100))
                                  where T219_Id='$T219_Id'");
            
        } elseif ($Campo_Editado == 'T219_Percentual_Desconto_Bruto') {
            //return "$T219_Id, $campoEditado, $valorAntigo, $novoValor, {$mT219['T219_Percentual_Desconto']}";
            if (!empty($Conteudo_Campo_Editado)) {
                $Conteudo_Campo_Editado = formata_valor_gravar($Conteudo_Campo_Editado, 6);
            } else {
                $Conteudo_Campo_Editado = 0;
            }
            //return "1: ".number_format($valorAntigo,2)."<br />2: ".number_format($mT219['T219_Percentual_Desconto_Bruto'],2);
            
            if (number_format($Conteudo_Campo_Editado, 2) != number_format($mT219['T219_Percentual_Desconto_Bruto'], 2)) {
                if (($mT219['T219_Valor_Preco_Original_Bruto'] - ($mT219['T219_Valor_Preco_Original_Bruto'] * ($Conteudo_Campo_Editado / 100))) < $mT219['T219_Valor_Custo_Unitario']) {
                    //return "Desconto inválido";
                }
                mysql_query("update T219 set T219_Percentual_Desconto_Bruto='$Conteudo_Campo_Editado',
                                            T219_Valor_Preco_Bruto=T219_Valor_Preco_Original_Bruto-(T219_Valor_Preco_Original_Bruto*($Conteudo_Campo_Editado/100)),
                                            T219_Valor_Desconto_Unitario_Bruto=(T219_Valor_Preco_Original_Bruto*($Conteudo_Campo_Editado/100))
                                      where T219_Id='$T219_Id'");
            }
            
        } elseif ($Campo_Editado == 'T219_Peso_Unitario') {
            
            $Conteudo_Campo_Editado = formata_valor_gravar($Conteudo_Campo_Editado, 4);
            
            mysql_query("update T219 set $Campo_Editado='$Conteudo_Campo_Editado'
                               where T219_Id='$T219_Id'");
            
        } elseif ($Campo_Editado == 'T219_Hora_Entrega') {
            mysql_query("update T219 set $Campo_Editado='$Conteudo_Campo_Editado'
                               where T219_Id='$T219_Id'");
            
        } elseif ($Campo_Editado == 'T219_Observacao') {
            mysql_query("update T219 set $Campo_Editado='$Conteudo_Campo_Editado'
                               where T219_Id='$T219_Id'");
        } elseif ($Campo_Editado == 'T219_D037_Id') {
            
            $cClausula = " select ";
            $cClausula .= " D009_Id, ";
            $cClausula .= " D037_Unidade, ";
            $cClausula .= " T219_Peso_Unitario, ";
            $cClausula .= " T219_Quantidade ";
            $cClausula .= " from T219 ";
            $cClausula .= " left join D009 on D009_Id=T219_D009_Id ";
            $cClausula .= " left join D049 on D049_Id=D009_D049_Id ";
            $cClausula .= " left join D001 on D001_Id=D049_D001_Id ";
            $cClausula .= " left join D037 on D037_Id='$Conteudo_Campo_Editado'";
            $cClausula .= " where T219_Id='$T219_Id' ";
            $T219  = mysql_query("$cClausula");
            $mT219 = mysql_fetch_array($T219);
            
            mysql_query("update T219 set $Campo_Editado='$Conteudo_Campo_Editado'
                               where T219_Id='$T219_Id'");
            
        } else if ($Campo_Editado == 'T219_Codigo_Substituto') {
            
            $Conteudo_Campo_Editado = strtoupper($Conteudo_Campo_Editado);
            $Conteudo_Campo_Editado = addslashes($Conteudo_Campo_Editado);
            
            mysql_query("update T219 set $Campo_Editado='$Conteudo_Campo_Editado'
                                  where T219_Id='$T219_Id'");
            
            $D001  = mysql_query("select D001_Id,
                                     D049_Id,
                                     D009_Id
                                from T219
                           left join D009 on D009_Id=T219_D009_Id
                           left join D049 on D049_Id=D009_D049_Id
                           left join D001 on D001_Id=D049_D001_Id
                               where T219_Id='$T219_Id'");
            $mD001 = mysql_fetch_array($D001);
            
            /*
            $D083 = mysql_query("select D083_Id,D083_Flag_Cliente,D083_Codigo_Produto_Fornecedor from D083 where D083_D001_Id='{$mD001['D001_Id']}' and D083_D049_Id='{$mD001['D049_Id']}' and D083_D024_Id='$D024_Id'");
            if (mysql_num_rows($D083) <= 0) {
                mysql_query("insert into D083 (D083_D049_Id,D083_D001_Id,D083_D024_Id,D083_Data_Cadastro,D083_Flag_Cliente,D083_Codigo_Produto_Fornecedor) values ('{$mD001['D049_Id']}','{$mD001['D001_Id']}','$D024_Id',curdate(),'S','$Conteudo_Campo_Editado')");
            } else {
                $mD083 = mysql_fetch_array($D083);
                mysql_query("update D083 set D083_Flag_Cliente='S',D083_Codigo_Produto_Fornecedor='$Conteudo_Campo_Editado' where D083_Id='{$mD083['D083_Id']}'");
            }
            */
            require_once("bibliotecas/classes/CAD002.php");
            $CAD002 = new CAD002();
            $CAD002->definirCodigoFornecedor($mD001['D009_Id'], $D024_Id, $Conteudo_Campo_Editado, '', true, false);
        } else {
            
            $Conteudo_Campo_Editado = strtoupper($Conteudo_Campo_Editado);
            $Conteudo_Campo_Editado = addslashes($Conteudo_Campo_Editado);
            
            mysql_query("update T219 set $Campo_Editado='$Conteudo_Campo_Editado'
                                  where T219_Id='$T219_Id'");
            
        }
        
        if ($Campo_Editado == 'T219_Item') {
            $T219  = mysql_query("select T219_Item from T219 where T219_Id='$T219_Id'");
            $mT219 = mysql_fetch_array($T219);
            
            if ($mT219['T219_Item'] > $Conteudo_Campo_Editado) {
                mysql_query("update T219 set T219_Reordenar=0 where T219_Id='$T219_Id'");
            } else {
                mysql_query("update T219 set T219_Reordenar=2 where T219_Id='$T219_Id'");
            }
        }
        
        $cSql = mysql_query("call T219_Gravar_Totalizacao('$T219_Id');");
        //$cSql=sql_query("call T218_Gravar_Totalizacao('$CRM_T218_Id');");
        return true;
    }
    
    /**
     * retornaCFOP
     *
     * @param int $D024_Id
     * @param int $D009_Id
     * @param int $C004_Id
     * @param string $Origem_Mercadoria
     * @param Bool $retornaLog  
     * @param int $D069_Id
     * @return String/bool
     */
    public function retornaCFOP($D024_Id, $D009_Id = null, $C004_Id = null, $Origem_Mercadoria = NULL, $retornaLog = false, $D069_Id = false, $Destino = null)
    {
        global $g;
        $tipo;
        $C004_Id = ($C004_Id == null) ? $g['empresaAtual'] : $C004_Id;
        
        if ($D009_Id) {
            $tipo    = 'I';
            $retorno = mysql_query("SELECT                              
                                    D024_Flag_Contribuinte,
                                    D024_Flag_Pessoa_Fisica_Juridica,
                                    D024_Flag_Revenda_Consumidor,
                                    D024_Codigo_Regime_Tributario,
                                    D024_D006_Id,
                                    D018_UF,    
                                    D018_Id,                            
                                    D053_Flag_ST_Compra,
                                    D001_Flag_Tipo,
                                    D001_Peso_Unitario_Kg,
                                    D001_D060_Id,
                                    D001_D003_Id,
                                    D049_Flag_Nacional_Importado,
                                    D049_Origem_Mercadoria,
                                    D149_Flag_ST,
                                    D001_Codigo_Produto,
                                    D149_UF_ST_Compras,
                                    D005_Id,
                                    D005_Classificacao_Fiscal,
                                    C004_Codigo_Regime_Tributario,
                                    C004_D018_Id,
                                    C004_Id,
                                    D149_Id                             
                                FROM D024
                                    LEFT JOIN C004 ON C004_Id={$C004_Id}
                                    LEFT JOIN D018 ON D018_Id=D024_D018_Id
                                    LEFT JOIN D053 ON D053_D018_Id=D018_Id
                                    LEFT JOIN D009 ON D009_Id={$D009_Id}
                                    LEFT JOIN D049 ON D049_Id=D009_D049_Id
                                    LEFT JOIN D001 ON D001_Id=D049_D001_Id
                                    LEFT JOIN D005 ON D005_Id=D001_D005_Id
                                    LEFT JOIN D149 ON D149_D005_Id=D005_Id AND D149_C004_Id={$C004_Id}
                                WHERE 
                                    D024_Id={$D024_Id} AND D053_C004_Id={$C004_Id}
            ");
        } else {
            $D069=mysql_query("SELECT D069_NCM from D069 WHERE D069_Id='{$D069_Id}'");
            $mD069=mysql_fetch_array($D069);
            $D069_NCM = preg_replace("/[^0-9]/", "",$mD069['D069_NCM']);
            $tipo    = 'I';
            $retorno = mysql_query("SELECT  
                                    D024_Id,                            
                                    D024_Flag_Contribuinte,
                                    D024_Flag_Pessoa_Fisica_Juridica,
                                    D024_Flag_Revenda_Consumidor,
                                    D024_Codigo_Regime_Tributario,
                                    D024_D006_Id,
                                    D018_UF,
                                    D018_Id,                                
                                    D053_Flag_ST_Compra,
                                    C004_Codigo_Regime_Tributario,
                                    C004_D018_Id,
                                    C004_Id,
                                    D149_Flag_ST,
                                    D149_UF_ST_Compras
                                FROM D024
                                    LEFT JOIN D069 ON D069_Id={$D069_Id}
                                    LEFT JOIN D005 ON D005_Classificacao_Fiscal={$D069_NCM}
                                    LEFT JOIN D149 ON D149_D005_Id=D005_Id AND D149_C004_Id={$C004_Id}
                                    LEFT JOIN C004 ON C004_Id={$C004_Id}
                                    LEFT JOIN D018 ON D018_Id=D024_D018_Id
                                    LEFT JOIN D053 ON D053_D018_Id=D018_Id
                                WHERE 
                                    D024_Id={$D024_Id} AND D053_C004_Id={$C004_Id}

            ");            
        }
        $registro = mysql_fetch_array($retorno);
        
        $D145_C004_Id = $registro['C004_Id'];
        
        $D145_C004_Codigo_Regime_Tributario = $registro['C004_Codigo_Regime_Tributario'];
        
        $D145_Flag_Revenda_Consumidor = $registro['D024_Flag_Revenda_Consumidor'];
        
        // o valor do tipo "não contribuinte" que no cad cliente é 0 foi substituido por 9 
        $D145_D024_Flag_Contribuinte = $registro['D024_Flag_Contribuinte'];
        if ($D145_D024_Flag_Contribuinte == 0)
            $D145_D024_Flag_Contribuinte = 9;
        
        if ($registro['D018_Id'] == $registro['C004_D018_Id'])
            $D145_Dentro_Fora_Estado = 'D';
        else
            $D145_Dentro_Fora_Estado = 'F';
        
        $D145_UF_Tem_ST = $registro['D053_Flag_ST_Compra'];
        
        if ($registro['D149_Flag_ST'])
            $D145_NCM_Tem_ST = $registro['D149_Flag_ST'];
        else
            $D145_NCM_Tem_ST = "";
        
        $D145_D005_Id = $registro['D005_Id'];
        
        if ($registro['D024_Flag_Pessoa_Fisica_Juridica'] == "F")
            $D145_Pessoa_Fisica_Juridica = "PF";
        elseif ($registro['D024_Flag_Pessoa_Fisica_Juridica'] == "J")
            $D145_Pessoa_Fisica_Juridica = "PJ";
        elseif ($registro['D024_Flag_Pessoa_Fisica_Juridica'] == "I")
            $D145_Pessoa_Fisica_Juridica = "PI";
        
        if ($g['C031']['multimarcas'] == 'S') {
            if ($registro['D049_Flag_Nacional_Importado'] == "N")
                $D145_Produzido_Adquirido = "A";
            else if ($registro['D049_Flag_Nacional_Importado'] == "I")
                $D145_Produzido_Adquirido = "P";
            else
                $D145_Produzido_Adquirido = "";
        } else {
            if ($registro['D001_Flag_Tipo'] == "COMPRADO")
                $D145_Produzido_Adquirido = "A";
            else if ($registro['D001_Flag_Tipo'] == "PRODUZIDO")
                $D145_Produzido_Adquirido = "P";
            else
                $D145_Produzido_Adquirido = "";
        }
        
        if ($Origem_Mercadoria != NULL) {
            $registro['D049_Origem_Mercadoria'] = $Origem_Mercadoria;
        }
        $D145_Origem_Mercadoria = $registro['D049_Origem_Mercadoria'];
        
        //die("D049_Flag_Nacional_Importado->".$D049_Flag_Nacional_Importado);
        $retorno = mysql_query("SELECT *, D145_Quantidade_Regras(D145_Id) as qtdeRegras FROM D145 LEFT JOIN D006 ON D006_Id=D145_D006_Id WHERE D145_Flag_Inativar='N' AND D006_Flag_Inativo='N' AND D145_Flag_Cabecalho_Item='$tipo'");
        log("SELECT *, D145_Quantidade_Regras(D145_Id) as qtdeRegras FROM D145 LEFT JOIN D006 ON D006_Id=D145_D006_Id WHERE D145_Flag_Inativar='N' AND D006_Flag_Inativo='N' AND D145_Flag_Cabecalho_Item='$tipo'");
        $validas;
        $valida;
        $err = "";
        $log = "Início Log D009_Id: {$D009_Id} {$registro['D001_Codigo_Produto']}<br />";
        while ($regras = mysql_fetch_assoc($retorno)) {
            $iconeAbrirJanela = <<<EOT
                if([[permissaoRegras]]){
                    abrirJanela(false, divIdRootAberto(), '[[divId]]', unique(), '', 'Editar Regra', '/fis/fis001/content/fis001content03Compras/', '&acaoId=' + encodeURIComponent('{$regras['D145_Id']}') + '&tabela=D145', [600,550]);
                }
EOT;
            $log .= "<br />Entrou while D145_Id: {$regras['D145_Id']} - {$regras['D006_Codigo_CFOP']} - {$regras['D006_Descricao']}<br />";
            $demonstrativoDadosRegra = "<h3><font color=\"green\">Regra: {$regras['D145_Id']} ({$regras['qtdeRegras']}) - {$regras['D006_Codigo_CFOP']} - {$regras['D006_Descricao']}</font><span class=\"gear\" style=\"float: right; cursor: pointer; width: 16px; height: 16px; right: 0; position: relative;\" onclick=\"{$iconeAbrirJanela} event.preventDefault(); return false;\" /></h3><div>";
            $demonstrativoRegra = "";
            $arrayDemo[] = array();

            if (trim($regras['D145_Empresas_Destinatario']) != "") {
                $log .= "ENTROU IF D145_Empresas_Destinatario != ''<br />";
                $demonstrativoRegra .= "Fornecedor específicado na regra<br />";

                $pos = strpos($regras['D145_Empresas_Destinatario'], $D024_Id . ',');
                if ($pos === false) {
                    $log .= " Entrou CONTINUE D145_Empresas_Destinatario D145_Id='{$regras['D145_Id']}' D024_Id = '{$regras['D145_Empresas_Destinatario']}' != '$D024_Id'<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."Fornecedor não específicado na regra</div>"
                    );
                    $err .= '0';
                    continue;
                }
            }

            if (trim($regras['D145_Empresas_Destinatario_Ignorar']) != "") {
                $log .= "ENTROU IF D145_Empresas_Destinatario_Ignorar != ''<br />";
                $demonstrativoRegra .= "Fornecedor não usar não específicado na regra<br />";
                log("D024_Id: ".$D024_Id.", Regras CFOP: ".$regras['D145_Empresas_Destinatario_Ignorar']);
                $pos = strpos($regras['D145_Empresas_Destinatario_Ignorar'], $D024_Id . ',');
                log("STR POS: ".$pos);
                if ($pos !== false) {
                    $log .= " Entrou CONTINUE D145_Empresas_Destinatario_Ignorar D145_Id='{$regras['D145_Id']}' D024_Id = '{$regras['D145_Empresas_Destinatario_Ignorar']}' != '$D024_Id'<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."Fornecedor não usar específicado na regra</div>"
                    );
                    $err .= '0';
                    continue;
                }
            }
            
            if ($registro['D024_D006_Id'] > 0 and $regras['D145_Flag_Preferencial_Sobre_D024_D006_Id'] != "S") {
                $log .= "Entrou IF D024_D006_Id > AND D145_Flag_Preferencial_Sobre_D024_D006_Id != S  D145_Id='{$regras['D145_Id']}'<br />";
                $demonstrativoRegra .= "Fornecedor possuí CFOP preferencial<br />";
                $err .= '0';
                continue;
            }
            
            if (trim($regras['D145_Empresas']) != "") {
                $log .= "Entrou IF D145_Empresas != ''<br />";
                $demonstrativoRegra .= "Empresa específicada na regra<br />";

                $pos = strpos($regras['D145_Empresas'], $D145_C004_Id . ',');
                if ($pos === false) {
                    $log .= " Entrou CONTINUE D145_Empresas D145_Id='{$regras['D145_Id']}' D145_C004_Id: {$D145_C004_Id}<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."Empresa não específicada na regra</div>"
                    );
                    $err .= '0';
                    continue;
                }
            }
            if ($regras['D145_C004_Id'] > 0) {
                $log = "Entrou IF D145_C004_Id > 0 D145_Id='{$regras['D145_Id']}'<br />";
                $demonstrativoRegra .= "Regra específica da empresa<br />";

                if ($regras['D145_C004_Id'] != $D145_C004_Id) {
                    $log .= "Entrou CONTINUE D145_C004_Id: {$regras['D145_C004_Id']} != D145_C004_Id: {$D145_C004_Id} D145_Id='{$regras['D145_Id']}'<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."Regra específica para outra empresa</div>"
                    );
                    $err .= '0';
                    continue;
                }
            }
            if (trim($regras['D145_Flag_Revenda_Consumidor']) != "") {
                $log .= "Entrou D145_Flag_Revenda_Consumidor != '' D145_Id='{$regras['D145_Id']}'<br />";
                $demonstrativoRegra .= "Fornecedor revendedor/consumidor<br />";
                
                $pos = strpos($regras['D145_Flag_Revenda_Consumidor'], $D145_Flag_Revenda_Consumidor . ',');
                if ($pos === false) {
                    $log .= "Entrou CONTINUE D145_Flag_Revenda_Consumidor. D145_Id='{$regras['D145_Id']}'<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."Fornecedor não revendedor/consumidor</div>"
                    );
                    $err .= '0';
                    continue;
                }
            }
            if ($regras['D145_C004_Codigo_Regime_Tributario'] != "0") {
                $log .= "Entrou D145_C004_Codigo_Regime_Tributario != 0 D145_Id='{$regras['D145_Id']}'<br />";
                $demonstrativoRegra .= "Regime tributário da empresa específicado na regra<br />";
                
                if ($regras['D145_C004_Codigo_Regime_Tributario'] != $D145_C004_Codigo_Regime_Tributario) {
                    $log .= "Entrou CONTINUE D145_C004_Codigo_Regime_Tributario: {$regras['D145_C004_Codigo_Regime_Tributario']} != D145_C004_Codigo_Regime_Tributario: {$D145_C004_Codigo_Regime_Tributario} D145_Id='{$regras['D145_Id']}'<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."Regime tributário da empresa diferente da regra</div>"
                    );
                    $err .= '1';
                    continue;
                }
            }
            if ($regras['D145_D024_Flag_Contribuinte'] != "0") {
                $log .= "Entrou D145_D024_Flag_Contribuinte != 0 D145_Id='{$regras['D145_Id']}'<br />";
                $demonstrativoRegra .= "Tipo contribuinte do fornecedor na regra<br />";
                
                if ($regras['D145_D024_Flag_Contribuinte'] != $D145_D024_Flag_Contribuinte) {
                    $log .= "Entrou CONTINUE D145_D024_Flag_Contribuinte: {$regras['D145_D024_Flag_Contribuinte']} != D145_D024_Flag_Contribuinte: {$D145_D024_Flag_Contribuinte} D145_Id='{$regras['D145_Id']}'<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."Tipo contribuinte do fornecedor diferente da regra</div>"
                    );
                    $err .= '2';
                    continue;
                }
            }
            if ($regras['D145_Dentro_Fora_Estado'] != "") {
                $fornecedorEstado = ($D145_Dentro_Fora_Estado == 'D') ? 'dentro' : 'fora';
                $regraEstado = ($regras['D145_Dentro_Fora_Estado'] == 'D') ? 'dentro' : 'fora';
                
                $log .= "Entrou D145_Dentro_Fora_Estado != '' D145_Id='{$regras['D145_Id']}'<br />";
                $demonstrativoRegra .= "Fornecedor de {$fornecedorEstado} do estado<br />";
                if ($regras['D145_Dentro_Fora_Estado'] != $D145_Dentro_Fora_Estado) {
                    $log .= "Entrou CONTINUE D145_Dentro_Fora_Estado: {$regras['D145_Dentro_Fora_Estado']} != D145_Dentro_Fora_Estado: {$D145_Dentro_Fora_Estado} D145_Id='{$regras['D145_Id']}'<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."Fornecedor de {$fornecedorEstado} do estado, regra para {$regraEstado}</div>"
                    );
                    $err .= '3';
                    continue;
                }
            }
            if (trim($regras['D145_Estados_Especificos']) != "") {
                $log .= "Entrou D145_Estados_Especificos != '' D145_Id='{$regras['D145_Id']}'<br />";
                $demonstrativoRegra .= "Estado do fornecedor está na regra<br />";
                
                $pos = strpos($regras['D145_Estados_Especificos'], $registro['D018_UF']);
                if ($pos === false) {
                    $log .= "Entrou CONTINUE D145_Estados_Especificos: {$regras['D145_Estados_Especificos']} != D145_Estados_Especificos: {$D145_Estados_Especificos} D145_Id='{$regras['D145_Id']}'<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."Estado do fornecedor não está na regra</div>"
                    );
                    $err .= '4';
                    continue;
                }
            }
            if ($regras['D145_UF_Tem_ST'] != "") {
                $UFFornecedorST = ($D145_UF_Tem_ST == 'S') ? 'possuí' : 'não possuí';
                $UFRegraST = ($regras['D145_UF_Tem_ST'] == 'S') ? 'possuí' : 'não possuí';

                $log .= "Entrou D145_UF_Tem_ST != '' D145_Id='{$regras['D145_Id']}'<br />";
                $demonstrativoRegra .= "Estado do fornecedor {$UFFornecedorST} ST<br />";
                if ($regras['D145_UF_Tem_ST'] != $D145_UF_Tem_ST) {
                    $log .= "Entrou CONTINUE D145_UF_Tem_ST: {$regras['D145_UF_Tem_ST']} != D145_UF_Tem_ST: {$D145_UF_Tem_ST} D145_Id='{$regras['D145_Id']}'<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."Estado do fornecedor {$UFFornecedorST} ST e a regra {$UFRegraST}</div>"
                    );
                    $err .= '5';
                    continue;
                }
            }
            if ($regras['D145_Pessoa_Fisica_Juridica'] != "") {
                if($D145_Pessoa_Fisica_Juridica == 'PF') {
                    $pessoaFornecedor = 'física';
                } else if($D145_Pessoa_Fisica_Juridica == 'PJ') {
                    $pessoaFornecedor = 'jurídica';
                } else {
                    $pessoaFornecedor = 'internacional';
                }

                if($regras['D145_Pessoa_Fisica_Juridica'] == 'PF') {
                    $pessoaRegra = 'física';
                } else if($regras['D145_Pessoa_Fisica_Juridica'] == 'PJ') {
                    $pessoaRegra = 'jurídica';
                } else {
                    $pessoaRegra = 'internacional';
                }

                $log .= "Entrou D145_Pessoa_Fisica_Juridica != '' D145_Id='{$regras['D145_Id']}'<br />";
                $demonstrativoRegra .= "Fornecedor é pessoa {$pessoaFornecedor}<br />";
                if ($regras['D145_Pessoa_Fisica_Juridica'] != $D145_Pessoa_Fisica_Juridica) {
                    $log .= "Entrou CONTINUE D145_Pessoa_Fisica_Juridica: {$regras['D145_Pessoa_Fisica_Juridica']} != D145_Pessoa_Fisica_Juridica: {$D145_Pessoa_Fisica_Juridica} D145_Id='{$regras['D145_Id']}'<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."Fornecedor é pessoa {$pessoaFornecedor} e a regra é para pessoa {$pessoaRegra}</div>"
                    );
                    $err .= '6';
                    continue;
                }
            }

            if (trim($regras['D145_Codigo_Regime_Tributario']) != "") {
                $log .= "Entrou D145_Codigo_Regime_Tributario != '' D145_Id='{$regras['D145_Id']}'<br />";
                $demonstrativoRegra .= "Regime tributário do fornecedor específicado na regra<br />";

                $pos = strpos($regras['D145_Codigo_Regime_Tributario'], $registro['D024_Codigo_Regime_Tributario']);
                if ($pos === false) {
                    $log .= "Entrou CONTINUE D145_Codigo_Regime_Tributario D145_Id='{$regras['D145_Id']}'<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."Regime tributário do fornecedor não foi específicada na regra</div>"
                    );
                    $err .= '4';
                    continue;
                }
            }

            // as proximas duas regras se aplicam apenas se for item
            if ($regras['D145_Flag_Cabecalho_Item'] == 'I') {
                
                $log .= "Entrou IF D145_Flag_Cabecalho_Item='I'<br />";
                
                if ($regras['D145_NCM_Tem_ST'] != "") {
                    $NCMST = ($D145_NCM_Tem_ST == 'S') ? 'possuí ST' : 'não possuí ST';
                    $regraST = ($regras['D145_NCM_Tem_ST'] == 'S') ? 'possuí ST' : 'não possuí ST';
                    
                    $log .= "Entrou D145_NCM_Tem_ST != '' D145_Id='{$regras['D145_Id']}'<br />";
                    $demonstrativoRegra .= "NCM do produto {$NCMST}<br />";
                    if ($regras['D145_NCM_Tem_ST'] != $D145_NCM_Tem_ST) {
                        $log .= "Entrou CONTINUE D145_NCM_Tem_ST: {$regras['D145_NCM_Tem_ST']} != D145_NCM_Tem_ST: {$D145_NCM_Tem_ST} D145_Id='{$regras['D145_Id']}'<br />";
                        $demonstrativoRegra = "";
                        $arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."NCM do produto {$NCMST} e a regra {$regraST}</div>"
                        );
                        $err .= '7';
                        continue;
                    }
                }
                if ($regras['D145_Flag_UF_Convenio_NCM_ST'] == "S") {
                    $log .= "Entrou D145_Flag_UF_Convenio_NCM_ST != '' D145_Id='{$regras['D145_Id']}'<br />";
                    $demonstrativoRegra .= "Estado do fornecedor possuí convenio NCM/UF ST<br />";

                    $pos = strpos($registro['D149_UF_ST_Compras'], $registro['D018_UF']);
                    if ($pos === false) {
                        $log .= "Entrou CONTINUE D149_UF_ST_Compras D145_Id='{$regras['D145_Id']}'<br />";
                        $demonstrativoRegra = "";
                        $arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."Estado do fornecedor não especificado no campo UF ST da NCM</div>"
                        );
                        $err .= '10';
                        continue;
                    }
                }
                if ($regras['D145_Flag_UF_Convenio_NCM_ST'] == "N") {
                    $log .= "Entrou D145_Flag_UF_Convenio_NCM_ST== 'N' D145_Id='{$regras['D145_Id']}'<br />";
                    $demonstrativoRegra .= "Estado do fornecedor não possuí convenio NCM/UF ST<br />";

                    $pos = strpos($regras['D149_UF_ST_Compras'], $registro['D018_UF']);
                    if ($pos === true) {
                        $log .= "Entrou CONTINUE D149_UF_ST_Compras: {$regras['D149_UF_ST_Compras']} != D149_UF_ST_Compras: {$D149_UF_ST_Compras} D145_Id='{$regras['D145_Id']}'<br />";
                        $demonstrativoRegra = "";
                        $arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."Estado do fornecedor está específicado no campo UF ST da NCM</div>"
                        );
                        $err .= '10';
                        continue;
                    }
                }
                if ($regras['D145_Produzido_Adquirido'] != "") {
                    $tipoProduto = ($D145_Produzido_Adquirido == 'P') ? 'produzido' : 'adquirido';
                    $tipoRegra = ($regras['D145_Produzido_Adquirido'] == 'P') ? 'produzidos' : 'adquiridos';

                    $log .= "Entrou D145_Produzido_Adquirido != '' D145_Id='{$regras['D145_Id']}'<br />";
                    $demonstrativoRegra .= "O produto é {$tipoProduto}<br />";
                    if ($regras['D145_Produzido_Adquirido'] != $D145_Produzido_Adquirido) {
                        $log .= "Entrou CONTINUE D145_Produzido_Adquirido: {$regras['D145_Produzido_Adquirido']} != D145_Produzido_Adquirido: {$D145_Produzido_Adquirido} D145_Id='{$regras['D145_Id']}'<br />";
                        $demonstrativoRegra = "";
                        $arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."O produto é {$tipoProduto} e a regra é para produtos {$tipoRegra}</div>"
                        );
                        $err .= '8';
                        continue;
                    }
                }
                if ($regras['D145_Origem_Mercadoria'] != "") {
                    if($D145_Origem_Mercadoria == '0' || $D145_Origem_Mercadoria == '3' || $D145_Origem_Mercadoria == '4' || $D145_Origem_Mercadoria == '5'){
                        $origemProduto = 'nacional ('.$D145_Origem_Mercadoria.')';
                    } else if($D145_Origem_Mercadoria == '1' || $D145_Origem_Mercadoria == '2' || $D145_Origem_Mercadoria == '6' || $D145_Origem_Mercadoria == '7'){
                        $origemProduto = 'estrangeira ('.$D145_Origem_Mercadoria.')';
                    }

                    $log .= "Entrou D145_Origem_Mercadoria != '' D145_Id='{$regras['D145_Id']}'<br />";
                    $demonstrativoRegra .= "A origem do produto é {$origemProduto}<br />";
                    $pos = strpos($regras['D145_Origem_Mercadoria'], $D145_Origem_Mercadoria . ',');
                    if ($pos === false) {
                        $log .= "Entrou CONTINUE D145_Origem_Mercadoria: {$regras['D145_Origem_Mercadoria']} != D145_Origem_Mercadoria: {$D145_Origem_Mercadoria} D145_Id='{$regras['D145_Id']}'<br />";
                        $demonstrativoRegra = "";
                        $arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."A origem do produto é {$origemProduto} e a regra é para produtos de outras origens</div>"
                        );
                        $err .= '8';
                        continue;
                    }
                }
                if ($regras['D145_Peso_Minimo'] > 0 or $regras['D145_Peso_Maximo'] > 0) {
                    $log .= "Entrou D145_Peso_Minimo >= '0' OR {$regras['D145_Peso_Maximo']}>0 D145_Id='{$regras['D145_Id']}'<br />";
                    $demonstrativoRegra .= "O peso do produto está de acordo com o específicado<br />";

                    if ($registro['D001_Peso_Unitario_Kg'] < $regras['D145_Peso_Minimo'] or $registro['D001_Peso_Unitario_Kg'] > $regras['D145_Peso_Maximo']) {
                        $log .= "Entrou CONTINUE D001_Peso_Unitario_Kg: {$regras['D001_Peso_Unitario_Kg']} != D001_Peso_Unitario_Kg: {$D001_Peso_Unitario_Kg} D145_Id='{$regras['D145_Id']}'<br />";
                        $demonstrativoRegra = "";
                        $arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."O peso do produto ultrapassa os valores da regra</div>"
                        );
                        $err .= '8';
                        continue;
                    }
                }
                if (trim($regras['D145_NCM']) != "") {
                    $log .= "Entrou D145_NCM != '' D145_Id='{$regras['D145_Id']}'<br />";
                    $demonstrativoRegra .= "NCM do produto específicada na regra<br />";

                    $pos = strpos($regras['D145_NCM'], $registro['D005_Classificacao_Fiscal']);

                    if ($pos === false){
                        // se não encontrar pelo numero da NCM procura se tem o D149_Id
                        $pos = strpos(','.$regras['D145_NCM'].',', ','.$registro['D149_Id'].',');                       
                    }

                    if ($pos === false) {
                        $log .= "Entrou CONTINUE D145_NCM D145_Id='{$regras['D145_Id']}'<br />";
                        $demonstrativoRegra = "";
                        $arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."NCM do produto não foi específicada na regra</div>"
                        );
                        $err .= '4';
                        continue;
                    }
                }
            }
            if ($regras['D145_D005_Id'] > 0) {
                $log .= "Entrou D145_D005_Id >0 D145_Id='{$regras['D145_Id']}'<br />";
                $demonstrativoRegra .= "NCM do produto específicada na regra<br />";

                if ($regras['D145_D005_Id'] != $D145_D005_Id) {
                    $log .= "Entrou CONTINUE D145_D005_Id: {$regras['D145_D005_Id']} != D145_D005_Id: {$D145_D005_Id} D145_Id='{$regras['D145_Id']}'<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."NCM do produto diferente da regra</div>"
                    );
                    $err .= '9';
                    continue;
                }
            }

            // Setor produto (separar por vírgula):
            if (trim($regras['D145_D060_Ids']) != "") {
                $log .= "Entrou D145_D060_Ids != '' D145_Id='{$regras['D145_Id']}'<br />";
                $D145_D060_Ids = array_map('trim', explode(',', $regras['D145_D060_Ids']));
                $demonstrativoRegra .= "Setor do produto específicada na regra<br />";
                if (!in_array($registro['D001_D060_Id'], $D145_D060_Ids)) {
                    $log .= "Entrou CONTINUE D145_D060_Ids: {$regras['D145_D060_Ids']} != D145_D060_Ids: {$registro['D001_D060_Id']} D145_Id='{$regras['D145_Id']}'<br />";
                    //$demonstrativoRegra .= "<b><font color=red>(-)</font> Setor produto, no cadastro do produto, </b>não confere com o campo especificado nesta regra CFOP.<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)." Setor do produto não confere com o campo especificado nesta regra CFOP</div>"
                    );
                    $err .= '9';
                    continue;
                }
            }          
            
            // Grupo produto (separar por vírgula):
            if (trim($regras['D145_D003_Ids']) != "") {
                $log .= "Entrou D145_D003_Ids != '' D145_Id='{$regras['D145_Id']}'<br />";
                $D145_D003_Ids = array_map('trim', explode(',', $regras['D145_D003_Ids']));
                $demonstrativoRegra .= "Grupo do produto específicada na regra<br />";
                if (!in_array($registro['D001_D003_Id'], $D145_D003_Ids)) {
                    $log .= "Entrou CONTINUE D145_D003_Ids: {$regras['D145_D003_Ids']} != D145_D003_Ids: {$registro['D001_D003_Id']} D145_Id='{$regras['D145_Id']}'<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)." Grupo do produto não confere com o campo especificado nesta regra CFOP</div>"
                    );
                    $err .= '9';
                    continue;
                }
            }   

             // Destino Mercadoria - não aplica para tabela fornecedor       
            if ($regras['D145_Flag_Destino_Produto'] != "" && empty($D069_Id)) {                
                $log .= "Entrou D145_Flag_Destino_Produto != '' D145_Id='{$regras['D145_Id']}'<br />";
                $demonstrativoRegra .= "O destino do produto, confere com o campo especificado nesta regra CFOP.<br />";
                
                if ($regras['D145_Flag_Destino_Produto'] != $Destino) {                    
                    $log .= "Entrou CONTINUE D145_Flag_Destino_Produto: {$regras['D145_Flag_Destino_Produto']} != D145_Flag_Destino_Produto: {$Destino} D145_Id='{$regras['D145_Id']}'<br />";
                    $demonstrativoRegra = "";                    
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."O destino do produto, não confere com o campo especificado nesta regra CFOP.</div>"
                    );
                    $err .= '10';
                    continue;
                } 
            }

            $arrayDemo[] = array(
                'order' => $regras['qtdeRegras']+100,
                'texto' => $demonstrativoDadosRegra.$demonstrativoRegra."</div>"
            );

            $log .= "Aplicou essa regra<br />";
            
            $validas[] = $regras['D145_Id'];
            $valida    = $regras['D145_D006_Id'];
        }
        $log = $log . " <br />TOTAL REGRAS ENCONTRADAS: " . count($validas);
        log($log);        

        usort($arrayDemo, function($a, $b) {
            if ($a['order'] == $b['order']) {
                return 0;
            }
            return ($a['order'] < $b['order']) ? 1 : -1;
        });
        $demonstrativo = "<script>$(function() {\$(\"#demonstrativoCFOP\").accordion({collapsible: true, autoHeight: false, icons: false}); });</script><div id=\"demonstrativoCFOP\">";
        foreach ($arrayDemo as $key => $value) {
            $demonstrativo .= $value['texto'];
        }

        $demonstrativo .= "</div><br /><b>TOTAL REGRAS ENCONTRADAS: " . count($validas) . "</b>";
        $demonstrativo .= "<b style=\"float: right\">".date("d/m/Y H:i:s")."</b><br />";
        if($retornaLog == true){
            return $demonstrativo;
        }
        
        if (count($validas) == 0) {
            if ($registro['D024_D006_Id'] > 0) {
                return $registro['D024_D006_Id'];
            }
            return false;
        } else if (count($validas) == 1) {
            log('CFOP retornada: ' . $valida);
            return $valida;
        } else {
            $qtde_regras = 0;
            $valida      = 0;
            for ($i = 0; $i < count($validas); $i++) {
                $D145  = mysql_query("SELECT D145_Quantidade_Regras('{$validas[$i]}') as qtde_regras FROM D145 LEFT JOIN D006 ON D006_Id=D145_D006_Id WHERE D145_Flag_Inativar='N' AND D006_Flag_Inativo='N' limit 1");
                $mD145 = mysql_fetch_array($D145);
                if ($mD145['qtde_regras'] > $qtde_regras) {
                    $D145_1      = mysql_query("SELECT D145_D006_Id FROM D145 WHERE D145_Id='{$validas[$i]}'");
                    $mD145_1     = mysql_fetch_array($D145_1);
                    $qtde_regras = $mD145['qtde_regras'];
                    $valida      = $mD145_1['D145_D006_Id'];
                }
            }
            log('CFOP retornada: ' . $valida);
            return $valida;
        }
        
    }
    
    /**
     * alteraLista
     *
     * @param int $T218_Id
     * @param string $lista
     */
    public function alteraLista($T218_Id, $lista)
    {
        global $g;
        mysql_query("UPDATE T218 SET T218_Flag_Lista_Preco = '{$lista}' WHERE T218_Id = '{$T218_Id}'");
        
        $T219 = mysql_query("SELECT * FROM T219 WHERE T219_T218_Id = '{$T218_Id}'");
        
        while ($row = mysql_fetch_array($T219)) {
            $sql  = "SELECT (D009_Preco_{$lista}) as preco FROM D009 WHERE D009_Id = '{$row['T219_D009_Id']}'";
            $D009 = mysql_query($sql);
            $res  = mysql_fetch_array($D009);
            if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
                $sql = "UPDATE T219 SET T219_Valor_Preco_Original_Bruto = '{$res['preco']}',
                                    T219_Percentual_Desconto_Bruto=if(T219_Valor_Preco_Original_Bruto>0,(1-(T219_Valor_Preco_Bruto/T219_Valor_Preco_Original_Bruto))*100,0),
                                    T219_Valor_Desconto_Unitario_Bruto=if(T219_Valor_Preco_Original_Bruto>0,(T219_Valor_Preco_Original_Bruto*('{$res['preco']}'/100)),0)
                                    WHERE T219_Id = '{$row['T219_Id']}'";
                mysql_query($sql);
            } else {
                $sql = "UPDATE T219 SET T219_Valor_Preco_Original = '{$res['preco']}',
                                    T219_Percentual_Desconto = if(T219_Valor_Preco_Original>0,(1-(T219_Valor_Preco_Unitario/T219_Valor_Preco_Original))*100,0),
                                    T219_Valor_Desconto_Unitario = if(T219_Valor_Preco_Original>0,(T219_Valor_Preco_Original*('{$res['preco']}'/100)),0)
                                    WHERE T219_Id = '{$row['T219_Id']}'";
                mysql_query($sql);
            }
        }
        mysqli_query("CALL T218_Gravar_Totalizacao_3('{$T218_Id}')");
        return true;
    }
    
    /**
     * updateCheckboxDividir
     *
     * @param int $id
     * @param string $selected
     * @param string $TXXX
     */
    public function updateCheckboxDividir($id, $selected, $TXXX)
    {
        global $g;
        
        $id       = mysql_real_escape_string($id);
        $selected = mysql_real_escape_string($selected);
        $TXXX     = mysql_real_escape_string($TXXX);
        
        mysql_query("UPDATE {$TXXX} SET {$TXXX}_Flag_Dividir='$selected' WHERE {$TXXX}_Id='{$id}'");
        return true;
    }
    
    /**
     * est015MudarFilial
     *
     * @param int $T218_Id_Antigo
     * @param int $C004_Id_Novo
     */
    public function est015MudarFilial($T218_Id_Antigo, $C004_Id_Novo)
    {
        global $g, $novoT218_Id;
        //die("T218 Antigo: " . $T218_Id_Antigo . "<br />C004 novo: " .$C004_Id_Novo);
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        $D024  = mysql_query("SELECT T218_D024_Id FROM T218 WHERE T218_Id='{$T218_Id_Antigo}'");
        $mD024 = mysql_fetch_array($D024);
        
        $D006_Id = $this->retornaCFOP($mD024['T218_D024_Id'], false, $C004_Id_Novo);
        
        $sqlUpT218    = "UPDATE T218 SET T218_C004_Id='{$C004_Id_Novo}', T218_D006_Id='{$D006_Id}' WHERE T218_Id='{$T218_Id_Antigo}'";
        $resultUpT218 = mysql_query($sqlUpT218);
        
        if (!$resultUpT218) {
            mysql_query("ROLLBACK");
            //die($sqlUpT218);
            return '1' . mysql_error();
        }
        
        // Nesse método (mudar filial) não é criado um novo pedido, é apenas alterado o C004
        $novoT218_Id = $T218_Id_Antigo;
        
        // Seleciona os itens do pedido para ver se existe algum marcado
        $sqlT219    = "SELECT * FROM T219 WHERE T219_T218_Id='{$T218_Id_Antigo}';";
        $resultT219 = mysql_query($sqlT219);
        
        // Usa a SQL da T219 feita no início do método para fazer update dos teins que foram selecionados para gerar outro pedido 
        while ($linhaT219 = mysql_fetch_array($resultT219)) {
            /**
             * Buscando o D009 correto para a empresa selecionada.
             */
            $sqlD049    = "SELECT D009_D049_Id FROM D009 WHERE D009_Id='{$linhaT219['T219_D009_Id']}'";
            $resultD049 = mysql_query($sqlD049);
            $linhaD049  = mysql_fetch_array($resultD049);
            $erroSQL    = mysql_error();
            if (!empty($erroSQL)) {
                mysql_query("ROLLBACK");
                return '2...' . $erroSQL;
            }
            
            $sqlD009    = "SELECT D009_Id FROM D009 WHERE D009_D049_Id='{$linhaD049['D009_D049_Id']}' AND D009_C004_Id='{$C004_Id_Novo}'";
            $resultD009 = mysql_query($sqlD009);
            $linhaD009  = mysql_fetch_array($resultD009);
            $erroSQL    = mysql_error();
            if (!empty($erroSQL)) {
                mysql_query("ROLLBACK");
                return '3' . $erroSQL;
            }
            if (empty($linhaD009['D009_Id'])) {
                mysql_query("ROLLBACK");
                return 'D009_Id não encontrado';
            }
            
            /**
             * Buscando a CFOP correta para cada item
             */
            $D006_Id = $this->retornaCFOP($mD024['T218_D024_Id'], $linhaD009['D009_Id'], $C004_Id_Novo);
            
            /**
             * Faz update nos itens do pedido alterando o T224, C004 (id da empresa nova) e o D009 da nova empresa
             */
            $upT219 = "UPDATE T219 SET T219_C004_Id='{$C004_Id_Novo}', T219_D009_Id='{$linhaD009['D009_Id']}', T219_D006_Id='{$D006_Id}' WHERE T219_Id='{$linhaT219['T219_Id']}'";
            mysql_query($upT219);
            $erroSQL = mysql_error();
            if (!empty($erroSQL)) {
                mysql_query("ROLLBACK");
                return '4' . $erroSQL;
            }
            //mysql_query("call T225_Gravar_Totalizacao('{$linhaT225['T225_Id']}')");
        }
        
        /**
         *  selecionando todas as trocas do pedido
         */
        $sqlT222    = "SELECT * FROM T222 WHERE T222_T218_Id='{$T218_Id_Antigo}';";
        $resultT222 = mysql_query($sqlT222);
        while ($linhaT222 = mysql_fetch_array($resultT222)) {
            
            /**
             * Buscando o D009 correto para a empresa selecionada.
             */
            $sqlD049    = "SELECT D009_D049_Id FROM D009 WHERE D009_Id='{$linhaT222['T222_D009_Id']}'";
            $resultD049 = mysql_query($sqlD049);
            $linhaD049  = mysql_fetch_array($resultD049);
            $erroSQL    = mysql_error();
            if (!empty($erroSQL)) {
                mysql_query("ROLLBACK");
                return '2...' . $erroSQL;
            }
            
            $sqlD009    = "SELECT D009_Id FROM D009 WHERE D009_D049_Id='{$linhaD049['D009_D049_Id']}' AND D009_C004_Id='{$C004_Id_Novo}'";
            $resultD009 = mysql_query($sqlD009);
            $linhaD009  = mysql_fetch_array($resultD009);
            $erroSQL    = mysql_error();
            if (!empty($erroSQL)) {
                mysql_query("ROLLBACK");
                return '3' . $erroSQL;
            }
            if (empty($linhaD009['D009_Id'])) {
                mysql_query("ROLLBACK");
                return '2. D009_Id não encontrado';
            }
            
            $upT222     = "UPDATE T222 SET T222_C004_Id='{$C004_Id_Novo}', T222_D009_Id='{$linhaD009['D009_Id']}' WHERE T222_Id='{$linhaT222['T222_Id']}'";
            $resultT222 = mysql_query($upT222);
            if (!$resultT222) {
                mysql_query("ROLLBACK");
                return '4' . mysql_error();
            }
        }
        //mysql_query("call T224_Gravar_Totalizacao_3('{$T224_Id_Novo}')");
        $ret = $this->atualizarAliquotas($novoT218_Id, $C004_Id_Novo);
        if ($ret === true) {
            mysql_query("COMMIT");
            return true;
        } else {
            mysql_query("ROLLBACK");
            return $ret;
        }
    }
    
    /**
     * est015DividirOrcamento
     *
     * @param int $T218_Id_Antigo
     * @param int $C004_Id_Novo
     */
    public function est015DividirOrcamento($T218_Id_Antigo, $C004_Id_Novo)
    {
        global $g, $novoT218_Id;
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        // seleciona o cabeçalho do orçamento para inserir um cópia dele com o novo C004 
        $T218  = mysql_query("select * from T218 where T218_Id='{$T218_Id_Antigo}'");
        $mT218 = mysql_fetch_assoc($T218);
        
        // Buscando a CFOP para o cabeçalho do orçamento
        $D006_Id = $this->retornaCFOP($mT218['T218_D024_Id'], false, $C004_Id_Novo);
        
        $campos  = "";
        $valores = "";
        foreach ($mT218 as $key => $value) {
            if ($key == 'T218_Id') {
                continue;
            } elseif ($key == 'T218_C004_Id') {
                
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($C004_Id_Novo) . "',";
                
            } elseif ($key == 'T218_D006_Id') {
                
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($D006_Id) . "',";
                
            } else {
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($value) . "',";
            }
        }
        $campos  = trim($campos, ', ');
        $valores = trim($valores, ',');
        $insT218 = "INSERT INTO T218 ({$campos}) VALUES ({$valores})";
        
        // executa a inserção na T224 com o  novo
        $resultInsT218 = mysql_query($insT218);
        if (!$resultInsT218) {
            mysql_query("ROLLBACK");
            die($insT224);
            return '1' . mysql_error();
        }
        
        $novoT218_Id = $g['mysqlLastId'];
        
        // Seleciona os itens do pedido para ver se existe algum marcado
        $sqlT219    = "SELECT * FROM T219 WHERE T219_T218_Id='{$T218_Id_Antigo}' AND T219_Flag_Dividir='S';";
        $resultT219 = mysql_query($sqlT219);
        
        // Usa a SQL da T219 feita no início do método para fazer update dos teins que foram selecionados para gerar outro pedido 
        while ($linhaT219 = mysql_fetch_array($resultT219)) {
            /**
             * Buscando o D009 correto para a empresa selecionada.
             */
            $sqlD049    = "SELECT D009_D049_Id FROM D009 WHERE D009_Id='{$linhaT219['T219_D009_Id']}'";
            $resultD049 = mysql_query($sqlD049);
            $linhaD049  = mysql_fetch_array($resultD049);
            $erroSQL    = mysql_error();
            if (!empty($erroSQL)) {
                mysql_query("ROLLBACK");
                return '2...' . $erroSQL;
            }
            
            $sqlD009    = "SELECT D009_Id FROM D009 WHERE D009_D049_Id='{$linhaD049['D009_D049_Id']}' AND D009_C004_Id='{$C004_Id_Novo}'";
            $resultD009 = mysql_query($sqlD009);
            $linhaD009  = mysql_fetch_array($resultD009);
            $erroSQL    = mysql_error();
            if (!empty($erroSQL)) {
                mysql_query("ROLLBACK");
                return '3' . $erroSQL;
            }
            if (empty($linhaD009['D009_Id'])) {
                mysql_query("ROLLBACK");
                return 'D009_Id não encontrado';
            }
            
            /**
             * Buscando a CFOP correta para cada item
             */
            $D006_Id = $this->retornaCFOP($mT218['T218_D024_Id'], $linhaD009['D009_Id'], $C004_Id_Novo);
            
            /**
             * Faz update nos itens do pedido alterando o T218, C004 (id da empresa nova) e o D009 da nova empresa
             */
            $upT219 = "UPDATE T219 SET T219_T218_Id='{$novoT218_Id}', T219_C004_Id='{$C004_Id_Novo}', T219_Flag_Dividir='N', T219_D009_Id='{$linhaD009['D009_Id']}', T219_D006_Id='{$D006_Id}' WHERE T219_Id='{$linhaT219['T219_Id']}'";
            mysql_query($upT219);
            $erroSQL = mysql_error();
            if (!empty($erroSQL)) {
                mysql_query("ROLLBACK");
                return '4' . $erroSQL;
            }
            //mysql_query("call T219_Gravar_Totalizacao('{$linhaT219['T225_Id']}')");
        }
        
        /**
         *  selecionando todos os serviços do pedido que tenham marcado a flag dividir 
         */
        $sqlT223    = <<<EOT
        SELECT * FROM T223 WHERE T223_T218_Id='{$T218_Id_Antigo}' AND T223_Flag_Dividir='S';
EOT;
        $resultT223 = mysql_query($sqlT223);
        while ($linhaT223 = mysql_fetch_array($resultT223)) {
            $upT223       = "UPDATE T223 SET T223_T218_Id='{$novoT218_Id}', T223_Flag_Dividir='N' WHERE T223_Id='{$linhaT223['T223_Id']}'";
            $resultUpT223 = mysql_query($upT223);
            if (!$resultUpT223) {
                mysql_query("ROLLBACK");
                return '3' . mysql_error();
            }
        }
        
        /**
         *  selecionando todas as trocas do pedido
         */
        $sqlT222    = "SELECT * FROM T222 WHERE T222_T218_Id='{$T218_Id_Antigo}' AND T222_Flag_Dividir='S';";
        $resultT222 = mysql_query($sqlT222);
        while ($linhaT222 = mysql_fetch_array($resultT222)) {
            
            /**
             * Buscando o D009 correto para a empresa selecionada.
             */
            $sqlD049    = "SELECT D009_D049_Id FROM D009 WHERE D009_Id='{$linhaT222['T222_D009_Id']}'";
            $resultD049 = mysql_query($sqlD049);
            $linhaD049  = mysql_fetch_array($resultD049);
            $erroSQL    = mysql_error();
            if (!empty($erroSQL)) {
                mysql_query("ROLLBACK");
                return '2...' . $erroSQL;
            }
            
            $sqlD009    = "SELECT D009_Id FROM D009 WHERE D009_D049_Id='{$linhaD049['D009_D049_Id']}' AND D009_C004_Id='{$C004_Id_Novo}'";
            $resultD009 = mysql_query($sqlD009);
            $linhaD009  = mysql_fetch_array($resultD009);
            $erroSQL    = mysql_error();
            if (!empty($erroSQL)) {
                mysql_query("ROLLBACK");
                return '3' . $erroSQL;
            }
            if (empty($linhaD009['D009_Id'])) {
                mysql_query("ROLLBACK");
                return '2. D009_Id não encontrado';
            }
            
            $upT222      = "UPDATE T222 SET T222_T218_Id='{$novoT218_Id}', T222_Flag_Dividir='N', T222_C004_Id='{$C004_Id_Novo}', T222_D009_Id='{$linhaD009['D009_Id']}' WHERE T222_Id='{$linhaT222['T222_Id']}'";
            $xresultT222 = mysql_query($upT222);
            if (!$xresultT222) {
                mysql_query("ROLLBACK");
                return '4' . mysql_error();
            }
        }
        mysql_query("call T218_Gravar_Totalizacao_3('{$novoT218_Id}')");
        mysql_query("call T218_Gravar_Totalizacao_3('{$T218_Id_Antigo}')");
        
        $ret = $this->atualizarAliquotas($novoT218_Id, $C004_Id_Novo);
        if ($ret === true) {
            mysql_query("COMMIT");
            return true;
        } else {
            mysql_query("ROLLBACK");
            return $ret;
        }
    }
    
    /**
     * retornaltimoPrecoVenda - Método que retorna o último preço que o produto foi estdido para um determinado Fornecedor
     *
     * @param int $D024_Id
     * @param int $D009_id
     */
    public function retornaltimoPrecoVenda($D024_Id, $D009_Id)
    {
        
        global $g;
        
        $retorno = "";
        if (empty($D024_Id)) {
            return "D024_Id não recebido";
        } else if (empty($D009_Id)) {
            return "D009_Id não recebido";
        }
        
        if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
            $precoOrcamento = "T219_Valor_Preco_Bruto";
            $precoPedido    = "T225_Valor_Preco_Bruto";
        } else {
            $precoOrcamento = "T219_Valor_Preco_Unitario";
            $precoPedido    = "T225_Valor_Preco_Sem_Desconto_Unitario";
        }
        
        $D024  = mysql_query("SELECT D024_Cnpj_Matriz FROM D024 WHERE D024_Id='{$D024_Id}'");
        $mD024 = mysql_fetch_array($D024);
        if (!empty($mD024['D024_Cnpj_Matriz'])) {
            $extra = " OR D024_Cnpj_Matriz='{$mD024['D024_Cnpj_Matriz']}' ";
        } else {
            $extra = "";
        }
        
        // Seleciona a última NF desse D009 para esse D024
        $T007  = mysql_query("SELECT T007_Id, 
                                    D037_Unidade, 
                                    T008_Quantidade, 
                                    T008_Valor_Preco_Sem_Desconto_Unitario, 
                                    T007_Data_Emissao
                                FROM T008
                                    LEFT JOIN T007 ON T007_Id=T008_T007_Id
                                    LEFT JOIN D037 ON D037_Id=T008_D037_Id
                                    LEFT JOIN D024 ON D024_Id=T007_D024_Id
                                WHERE T007_D024_Id='{$D024_Id}'
                                    AND T008_D009_Id='{$D009_Id}'
                                    {$extra}
                                    ORDER BY T007_Id DESC LIMIT 1");
        $mT007 = mysql_fetch_assoc($T007);
        
        // Selecionando o último pedido desse D009 para esse D024
        $T224  = mysql_query("SELECT T224_Id, 
                                    D037_Unidade, 
                                    T225_Quantidade, 
                                    $precoPedido, 
                                    T224_Data_Emissao, 
                                    T224_Nome_Status AS StatusPedido, 
                                    IFNULL(T007_Numero_Nota_Fiscal,0) AS NumeroNota 
                                FROM T225
                                    LEFT JOIN T224 ON T224_Id=T225_T224_Id
                                    LEFT JOIN D037 ON D037_Id=T225_D037_Id
                                    LEFT JOIN T007 ON T007_T224_Id=T224_Id
                                    LEFT JOIN D024 ON D024_Id=T224_D024_Id
                                WHERE T224_D024_Id='{$D024_Id}'
                                    AND T225_D009_Id='{$D009_Id}'
                                    {$extra}
                                    ORDER BY T224_Id DESC LIMIT 1");
        $mT224 = mysql_fetch_assoc($T224);
        
        // Selecionando o último orçamento desse D009 para esse D024
        $T218 = mysql_query("SELECT T218_Id, 
                                    D037_Unidade, 
                                    T219_Quantidade, 
                                    $precoOrcamento,
                                    T218_Data_Emissao 
                                FROM T219
                                    LEFT JOIN T218 ON T218_Id=T219_T218_Id
                                    LEFT JOIN D037 ON D037_Id=T219_D037_Id
                                    LEFT JOIN D024 ON D024_Id=T218_D024_Id
                                WHERE T218_D024_Id='{$D024_Id}'
                                    AND T219_D009_Id='{$D009_Id}'
                                    {$extra}
                                    ORDER BY T218_Id DESC LIMIT 1");
        log("SELECT T218_Id, 
                                    D037_Unidade, 
                                    T219_Quantidade, 
                                    $precoOrcamento,
                                    T218_Data_Emissao 
                                FROM T219
                                    LEFT JOIN T218 ON T218_Id=T219_T218_Id
                                    LEFT JOIN D037 ON D037_Id=T219_D037_Id
                                    LEFT JOIN D024 ON D024_Id=T218_D024_Id
                                WHERE T218_D024_Id='{$D024_Id}'
                                    AND T219_D009_Id='{$D009_Id}'
                                    {$extra}
                                    ORDER BY T218_Id DESC LIMIT 1");
        $mT218 = mysql_fetch_assoc($T218);
        
        // Caso exista um orçamento, pedido e uma nota, ele verifica qual é mais recente para retornar
        if ((!empty($mT224)) && (!empty($mT218)) && (!empty($mT007))) {
            
            
            if (($mT218['T218_Data_Emissao'] > $mT224['T224_Data_Emissao']) || ($mT218['T218_Data_Emissao'] > $mT007['T007_Data_Emissao'])) {
                // Caso o orçamento seja mais recenete que todos, retornar o orçametno
                $mT218[$precoOrcamento]     = gCorrigeNumero($mT218[$precoOrcamento]);
                $mT218['T218_Data_Emissao'] = gCorrigeData($mT218['T218_Data_Emissao']);
                $mT218['T219_Quantidade']   = gCorrigeNumero($mT218['T219_Quantidade']);
                
                $retorno = "Cotação de compra N.{$mT218['T218_Id']} - {$mT218['D037_Unidade']} {$mT218['T219_Quantidade']} - R\$ {$mT218[$precoOrcamento]} - {$mT218['T218_Data_Emissao']}";
                return $retorno;
                
            } else if (($mT224['T224_Data_Emissao'] > $mT218['T218_Data_Emissao']) || ($mT224['T224_Data_Emissao'] > $mT007['T007_Data_Emissao'])) {
                
                // Caso o pedido seja mais recenete que todos, retornar o pedido
                $mT224[$precoPedido]        = gCorrigeNumero($mT224[$precoPedido]);
                $mT224['T224_Data_Emissao'] = gCorrigeData($mT224['T224_Data_Emissao']);
                $mT224['T225_Quantidade']   = gCorrigeNumero($mT224['T225_Quantidade']);
                
                $retorno = "Pedido N.{$mT224['T224_Id']} - {$mT224['D037_Unidade']} {$mT224['T225_Quantidade']} - R\$ {$mT224[$precoPedido]} - {$mT224['T224_Data_Emissao']} - {$mT224['StatusPedido']}";
                return $retorno;
            } else {
                // Retonar a nota
                $mT007['T008_Valor_Preco_Sem_Desconto_Unitario'] = gCorrigeNumero($mT007['T008_Valor_Preco_Sem_Desconto_Unitario']);
                $mT007['T007_Data_Emissao']                      = gCorrigeData($mT007['T007_Data_Emissao']);
                $mT007['T008_Quantidade']                        = gCorrigeNumero($mT007['T008_Quantidade']);
                
                $retorno = "Nota N.{$mT007['T007_Numero_Nota_Fiscal']} - {$mT007['D037_Unidade']} {$mT007['T008_Quantidade']} - R\$ {$mT007['T008_Valor_Preco_Sem_Desconto_Unitario']} - {$mT007['T007_Data_Emissao']}";
                return $retorno;
            }
            
        }
        if (!empty($mT007)) {
            // Caso exista alguma nota, já é retornado o valor mais recente
            $mT007['T008_Valor_Preco_Sem_Desconto_Unitario'] = gCorrigeNumero($mT007['T008_Valor_Preco_Sem_Desconto_Unitario']);
            $mT007['T007_Data_Emissao']                      = gCorrigeData($mT007['T007_Data_Emissao']);
            $mT007['T008_Quantidade']                        = gCorrigeNumero($mT007['T008_Quantidade']);
            
            $retorno = "Nota N.{$mT007['T007_Numero_Nota_Fiscal']} - {$mT007['D037_Unidade']} {$mT007['T008_Quantidade']} - R\$ {$mT007['T008_Valor_Preco_Sem_Desconto_Unitario']} - {$mT007['T007_Data_Emissao']}";
            return $retorno;
        } else if (!empty($mT224)) {
            // Caso exista algum pedido, já é retornado o valor mais recente
            $mT224[$precoPedido]        = gCorrigeNumero($mT224[$precoPedido]);
            $mT224['T224_Data_Emissao'] = gCorrigeData($mT224['T224_Data_Emissao']);
            $mT224['T225_Quantidade']   = gCorrigeNumero($mT224['T225_Quantidade']);
            
            $retorno = "Pedido N.{$mT224['T224_Id']} - {$mT224['D037_Unidade']} {$mT224['T225_Quantidade']} - R\$ {$mT224[$precoPedido]} - {$mT224['T224_Data_Emissao']} - {$mT224['StatusPedido']}";
            return $retorno;
        } else if (!empty($mT218)) {
            // Caso exista algum orçamento já é retornado o mais recente
            $mT218[$precoOrcamento]     = gCorrigeNumero($mT218[$precoOrcamento]);
            $mT218['T218_Data_Emissao'] = gCorrigeData($mT218['T218_Data_Emissao']);
            $mT218['T219_Quantidade']   = gCorrigeNumero($mT218['T219_Quantidade']);
            
            $retorno = "Cotação de compra N.{$mT218['T218_Id']} - {$mT218['D037_Unidade']} {$mT218['T219_Quantidade']} - R\$ {$mT218[$precoOrcamento]} - {$mT218['T218_Data_Emissao']}";
            return $retorno;
        } else {
            return "Primeira Venda";
        }
    }
    
    /**
     * duplicarPedidosSelecionados
     *
     * @param int $dias
     */
    public function duplicarPedidosSelecionados($dias)
    {
        global $g;
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        $sqlT224 = mysql_query("SELECT * FROM T224 ORDER BY T224_Processo_Id DESC LIMIT 1");
        $resT224 = mysql_fetch_array($sqlT224);
        $sqlC007 = mysql_query("SELECT * FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}'");
        $resC007 = mysql_fetch_array($sqlC007);
        $sqlC004 = mysql_query("SELECT * FROM C004 WHERE C004_Id = '{$g['empresaAtual']}'");
        $resC004 = mysql_fetch_array($sqlC004);
        
        $processoLog = 'Duplicação de Pedido<br />';
        $processoLog .= 'Usuario: ' . $resC007['C007_Primeiro_Nome'] . '(' . $g['usuarioAtual'] . ')<br />';
        $processoLog .= 'Empresa: ' . $resC004['C004_Nome_Abreviado'] . '(' . $g['empresaAtual'] . ')<br />';
        $processoLog .= 'Intervalo de dias: ' . $dias . '<br />';
        $processoLog .= 'Data: ' . date('d/m/Y', time()) . ' - Hora: ' . date('H:i:s', time());
        
        $lastProcessoId = $resT224['T224_Processo_Id'] + 1;
        
        $AllT224 = mysql_query("SELECT * FROM T224 WHERE T224_Flag_Duplicar='S'");
        while ($mT224 = mysql_fetch_assoc($AllT224)) {
            //$Data_Emissao = strtotime($mT224['T224_Data_Emissao']);
            
            //$Data_Emissao = date("Y-m-d",strtotime(date("Y-m-d", strtotime($mT224['T224_Data_Emissao'])) . " +{$dias} day"));
            $Data_Entrega = date("Y-m-d", strtotime(date("Y-m-d", strtotime($mT224['T224_Data_Emissao'])) . " +{$dias} day"));
            
            $T224_Id_Antigo = $mT224['T224_Id'];
            $campos         = "";
            $valores        = "";
            foreach ($mT224 as $key => $value) {
                if ($key == 'T224_Id') {
                    continue;
                } elseif ($key == 'T224_Data_Emissao') {
                    $campos .= $key . ", ";
                    $valores .= "CURDATE(),";
                } elseif ($key == 'T224_Data_Entrega') {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($Data_Entrega) . "',";
                } elseif ($key == 'T224_Flag_Status') {
                    $campos .= $key . ", ";
                    $valores .= "'0',";
                } elseif ($key == 'T224_Nome_Status') {
                    $campos .= $key . ", ";
                    $valores .= "'Pendente',";
                } else {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($value) . "',";
                }
            }
            $campos  = trim($campos, ', ');
            $valores = trim($valores, ',');
            $insT224 = "INSERT INTO T224 ({$campos}) VALUES ({$valores})";
            $resT224 = mysql_query($insT224);
            if (!$resT224) {
                mysql_query("ROLLBACK");
                return "Erro 1: " . mysql_error();
            }
            $T224_Id_Novo = $g['mysqlLastId'];
            mysql_query("UPDATE T224 SET T224_Processo_Id = '{$lastProcessoId}', T224_Processo_Log = '{$processoLog}' WHERE T224_Id = '{$T224_Id_Novo}'");
            
            // Selecionando todos os itens do pedido
            $T225 = mysql_query("SELECT * FROM T225 WHERE T225_T224_Id='{$T224_Id_Antigo}'");
            
            while ($mT225 = mysql_fetch_assoc($T225)) {
                $campos  = "";
                $valores = "";
                foreach ($mT225 as $key => $value) {
                    if ($key == 'T225_Id') {
                        continue;
                    } elseif ($key == "T225_T224_Id") {
                        $campos .= $key . ", ";
                        $valores .= "'" . mysql_real_escape_string($T224_Id_Novo) . "',";
                    } else {
                        $campos .= $key . ", ";
                        $valores .= "'" . mysql_real_escape_string($value) . "',";
                    }
                }
                $campos  = trim($campos, ', ');
                $valores = trim($valores, ',');
                $insT225 = "INSERT INTO T225 ({$campos}) VALUES ({$valores})";
                $resT225 = mysql_query($insT225);
                if (!$resT225) {
                    mysql_query("ROLLBACK");
                    return "Erro 2: " . mysql_error();
                }
            }
        }
        mysql_query("update T224 set T224_Flag_Duplicar='N'");
        mysql_query("COMMIT");
        return true;
    }
    
    /**
     * est015ClientesProcessar
     */
    public function est015ClientesProcessar()
    {
        // Método definido inicialmente na empresa FIXAR!!!
        return "Esse processo não está definido para sua empresa. Por favor, contate o suporte técnico.<br />est015->est015ClientesProcessar()";
    }
    
    /**
     * observacaoEntrega
     *
     * @param int $D009_Id
     * @param string $T219_Quantidade
     * @param string $T219_Codigo_Produto
     */
    public function observacaoEntrega($D009_Id, $T219_Quantidade, $T219_Codigo_Produto)
    {
        global $g;
        $T219_Codigo_Produto = trim($T219_Codigo_Produto);
        $T219_Quantidade     = formata_valor_gravar($T219_Quantidade, 3);
        
        
        if (($T219_Quantidade > 0) and (!empty($T219_Codigo_Produto))) {
            $cD009 = mysql_query("select (select sum((ET.D009_Quantidade_Estoque_Real-D009_Quantidade_Pedido(ET.D009_Id,1))+D009_Quantidade_Montagem(ET.D009_Id)) from D009 as ET where ET.D009_D049_Id=D049_Id and ET.D009_C004_Id<>'{$g['empresaAtual']}') as D009_Estoque_Lojas,
                                    (select sum((ET.D009_Quantidade_Estoque_Real-D009_Quantidade_Pedido(ET.D009_Id,1))+D009_Quantidade_Montagem(ET.D009_Id)) from D009 as ET where ET.D009_Id='{$D009_Id}') as D009_Estoque_Local
                                from D001
                          left join D002 on D002_Id=D001_D002_Id
                          left join D049 on D049_D001_Id=D001_Id
                          left join D009 on D009_D049_Id=D049_Id
                          left join D024 on D024_Id=D049_D024_Id
                              where D001_Codigo_Produto='{$T219_Codigo_Produto}'
                                and (select sum(T1.D009_Quantidade_Estoque_Real)
                                from D009 as T1
                          left join D049 as T2 on T2.D049_Id=T1.D009_D049_Id
                              where T2.D049_D001_Id=D001_Id
                                and T2.D049_D024_Id=D024_Id)>0
                                and D009_C004_Id='{$g['empresaAtual']}'");
            
            $mD009 = mysql_fetch_array($cD009);
            if (mysql_num_rows($cD009) == 0) {
                $T219_Observacao_Entrega = 'INDISPONIVEL';
            } else if ($mD009['D009_Estoque_Local'] >= $T219_Quantidade) {
                $T219_Observacao_Entrega = 'IMEDIATO';
            } else if (($mD009['D009_Estoque_Local'] > 0) and ($T219_Quantidade > $mD009['D009_Estoque_Local'])) {
                $T219_Observacao_Entrega = $mD009['D009_Estoque_Local'] . ' IMDTO';
                if ($mD009['D009_Estoque_Lojas'] > 0) {
                    $T219_Observacao_Entrega .= '/';
                    $T219_Observacao_Entrega .= ($T219_Quantidade - $mD009['D009_Estoque_Local']) . ' 24 HS';
                }
            } else if ($mD009['D009_Estoque_Lojas'] > 0) {
                $T219_Observacao_Entrega = '24 HORAS';
            } else {
                $T219_Observacao_Entrega = 'INDISPONIVEL';
            }
            return $T219_Observacao_Entrega;
        }
        return false;
    }
    
    /**
     * retornaAliquotas
     *
     * @param int $D009_Id
     */
    public function retornaAliquotas($D009_Id, $T218_Id = false, $T224_Id = false, $T013_Id = false, $T219_Id = false, $T225_Id = false, $T014_Id = false, $D070_Id = false, $D069_Id = false, $r_usarTabelaFornecedor = false)
    {       
        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();
        global $g;
        $mProduto = array();
        
        if ($T218_Id > 0) {        //COTAÇÃO COMPRA
            $cClausula = " select * ";
            $cClausula .= " from T218 ";
            $cClausula .= " left join D024 on D024_Id=T218_D024_Id ";
            $cClausula .= " left join D018 on D018_Id=D024_D018_Id ";
            $cClausula .= " left join D053 on D053_D018_Id=D024_D018_Id and D053_C004_Id=T218_C004_Id ";
            $cClausula .= " left join C004 on C004_Id=T218_C004_Id ";
            $cClausula .= " left join D006 on D006_Id=T218_D006_Id ";
            $cClausula .= " where T218_Id='{$T218_Id}' ";
            
            $Cab   = mysql_query($cClausula);
            $cErro = mysql_error();
            
            if (!empty($cErro)) {
                return "{$cErro}<br />{$cClausula}";
                exit;
            }
            $mCab = mysql_fetch_array($Cab);

            $D024_Id                 = $mCab['T218_D024_Id'];
            $C004_Id                 = $mCab['T218_C004_Id'];
            $D018_Id                 = $mCab['D024_D018_Id'];
            $Flag_Acp                = $mCab['T218_Flag_Acp'];
            $Flag_Revenda_Consumidor = $mCab['D024_Flag_Revenda_Consumidor'];
            $Flag_Contribuinte       = $mCab['T218_Flag_Contribuinte'];
            $Percentual_Comissao     = $mCab['T218_Percentual_Comissao'];
            $Aliquota_ICMS_Credito   = "";
            
            $cClausula = " SELECT * FROM T219 WHERE T219_Id='{$T219_Id}'";
            $sqlT219   = mysql_query($cClausula);
            $cErro     = mysql_error();
            if (!empty($cErro)) {
                return "{$cErro}<br />{$cClausula}";
                exit;
            }

            //ATUALIZA ALIQUOTAS CONFORME TABELA DO FORNECEDOR
            if($r_usarTabelaFornecedor == true){

                $T219              = mysql_fetch_array($sqlT219);
                $D006_Id           = $T219['T219_D006_Id'];
                $Origem_Mercadoria = $T219['T219_Flag_Origem_Produto'];
                
                if (empty($T219['T219_D006_Id'])) {
                    $D006_Id = $mCab['T218_D006_Id'];
                } else {
                    $D006_Id = $T219['T219_D006_Id'];
                }
                
                $cClausula = " select * ,";
                $cClausula .= " D088_Com_Aliquota.D088_Codigo_ST as D088_Codigo_ST, ";
                $cClausula .= " D088_Sem_Aliquota.D088_Codigo_ST as D088_Codigo_ST_Sem_Aliquota ";
                $cClausula .= " from D006 ";
                $cClausula .= " left join D059 on D059_Id=D006_D059_Id ";
                $cClausula .= " left join D088 as D088_Com_Aliquota ON D088_Com_Aliquota.D088_Id=D006_D088_Id ";
                $cClausula .= " left join D088 as D088_Sem_Aliquota ON D088_Sem_Aliquota.D088_Id=D006_D088_Id_Sem_Aliquota ";
                $cClausula .= " where D006_Id='{$D006_Id}'";
                $D006 = mysql_query($cClausula);
                if (!empty($cErro)) {
                    return "$cErro/p/p$cClausula";
                }
                $mD006 = mysql_fetch_array($D006);

                $cClausula = " select ";
                $cClausula .= " *, ";
                $cClausula .= " D069_Aliquota_IPI as Aliquota_IPI, ";
                $cClausula .= " 0 as Percentual_Reducao_IPI, ";
                $cClausula .= " D069_Aliquota_ICMS as Aliquota_ICMS, ";
                $cClausula .= " D069_Aliquota_PIS as Aliquota_PIS, ";
                $cClausula .= " D069_Aliquota_COFINS as Aliquota_COFINS, ";
                $cClausula .= " D069_Percentual_Reducao_ICMS as Percentual_Reducao_ICMS, ";
                $cClausula .= " D069_Flag_Origem_Produto as D049_Origem_Mercadoria, ";
                $cClausula .= " D069_ST_VA as D149_Valor_VA, ";
                $cClausula .= " D069_D006_Id, ";
                $cClausula .= " D069_Flag_ST as Flag_ST, ";
                $cClausula .= " D069_ICMS_Inter_Estadual as D053_ST_ICMS_Inter_Estadual_Compra, ";
                $cClausula .= " D069_ST_ICMS_Interno as D053_ST_ICMS_Interno_Compra, ";
                $cClausula .= " D069_Percentual_ST as Percentual_ST ";
                $cClausula .= " from D069 ";
                $cClausula .= " left join D070 on D070_Id=D069_D070_Id ";
                $cClausula .= " left join D009 on D009_Id=D069_D009_Id ";
                $cClausula .= " left join D049 on D049_Id=D009_D049_Id ";
                $cClausula .= " left join D001 on D001_Id=D049_D001_Id ";
                $cClausula .= " left join D041 on D041_D049_Id=D049_Id ";
                $cClausula .= " left join D037 on D037_Id=D001_D037_Id ";
                $cClausula .= " left join D024 on D024_Id={$D024_Id} ";
                $cClausula .= " left join D018 on D018_Id=D024_D018_Id ";
                $cClausula .= " left join D005 on D005_Id=D001_D005_Id OR D005_Id = (SELECT T025_D005_Id FROM D001 LEFT JOIN T025 ON T025_D015_Id=D001_D015_Id AND T025_D002_Id=D001_D002_Id WHERE D001_Id=D001_Id LIMIT 1)";
                $cClausula .= " left join D149 on D005_Id=D149_D005_Id AND D149_C004_Id={$C004_Id}";
                $cClausula .= " left join D088 on D088_Id=D149_D088_Id ";
                $cClausula .= " where D069_D009_Id='$D009_Id' AND D069_Flag_Tabela_Principal='S'";
                $cClausula .= " AND D070_D024_Id='$D024_Id' ";
                $cClausula .= " ORDER BY D069_Id desc ";
                $Produto  = mysql_query($cClausula);
                $cErro = mysql_error();
            
                if (!empty($cErro)) {
                    return "{$cErro}<br />{$cClausula}";
                    exit;
                }

                $mProduto = mysql_fetch_array($Produto);

                $Flag_ST = $mProduto['Flag_ST'];
                $mCab['D053_ST_ICMS_Inter_Estadual_Compra'] = $mProduto['D053_ST_ICMS_Inter_Estadual_Compra'];
                $mCab['D053_ST_ICMS_Interno_Compra'] = $mProduto['D053_ST_ICMS_Interno_Compra'];
                $D006_Id = $mProduto['D069_D006_Id'];
 

            //ATUALIZA ALIQUOTAS CONFORME O SISTEMA (JEITO PADRÃO)
            }else{
                
                $T219              = mysql_fetch_array($sqlT219);
                $D006_Id           = $T219['T219_D006_Id'];
                $Origem_Mercadoria = $T219['T219_Flag_Origem_Produto'];
                $Aliquota_ICMS_IE  = $T219['T219_Aliquota_ICMS'];
                $ICMS_Interno      = $T219['T219_ST_ICMS_Interno'];
                
                if (empty($T219['T219_D006_Id'])) {
                    $D006_Id = $mCab['T218_D006_Id'];
                } else {
                    $D006_Id = $T219['T219_D006_Id'];
                }
                
                
                $cClausula = " select * ,";
                $cClausula .= " D088_Com_Aliquota.D088_Codigo_ST as D088_Codigo_ST, ";
                $cClausula .= " D088_Sem_Aliquota.D088_Codigo_ST as D088_Codigo_ST_Sem_Aliquota ";
                $cClausula .= " from D006 ";
                $cClausula .= " left join D059 on D059_Id=D006_D059_Id ";
                $cClausula .= " left join D088 as D088_Com_Aliquota ON D088_Com_Aliquota.D088_Id=D006_D088_Id ";
                $cClausula .= " left join D088 as D088_Sem_Aliquota ON D088_Sem_Aliquota.D088_Id=D006_D088_Id_Sem_Aliquota ";
                $cClausula .= " where D006_Id='{$D006_Id}'";
                $D006 = mysql_query($cClausula);
                if (!empty($cErro)) {
                    return "$cErro/p/p$cClausula";
                }
                $mD006 = mysql_fetch_array($D006);
                                
                $cClausula = " select ";
                $cClausula .= " *, ";
                $cClausula .= " D009_IPI_compra('$C004_Id',D009_Id,'$D024_Id','$D006_Id','1','$Flag_Acp',NULL) as Aliquota_IPI, ";
                $cClausula .= " D009_IPI_compra('$C004_Id',D009_Id,'$D024_Id','$D006_Id','2','$Flag_Acp',NULL) as Percentual_Reducao_IPI, ";
                $cClausula .= " D009_IPV_ICMS('$C004_Id','$D024_Id',D009_Id,'$D006_Id','$D018_Id','$Flag_Revenda_Consumidor','$Percentual_Comissao','$Flag_Acp',2) as Aliquota_ICMS, ";
                $cClausula .= " D009_IPV_ICMS('$C004_Id','$D024_Id',D009_Id,'$D006_Id','$D018_Id','$Flag_Revenda_Consumidor','$Percentual_Comissao','$Flag_Acp',3) as Percentual_Reducao_ICMS, ";
                $cClausula .= " IFNULL(D009_Origem_Mercadoria, '') as D009_Origem_Mercadoria ";
                $cClausula .= " from D001 ";
                $cClausula .= " left join D049 on D001_Id=D049_D001_Id ";
                $cClausula .= " left join D009 on D049_Id=D009_D049_Id ";
                $cClausula .= " left join D041 on D041_D049_Id=D049_Id ";
                $cClausula .= " left join D037 on D037_Id=D001_D037_Id ";
                $cClausula .= " left join D024 on D024_Id={$D024_Id} ";
                $cClausula .= " left join D018 on D018_Id=D024_D018_Id ";
                $cClausula .= " left join D005 on D005_Id=D001_D005_Id OR D005_Id = (SELECT T025_D005_Id FROM D001 LEFT JOIN T025 ON T025_D015_Id=D001_D015_Id AND T025_D002_Id=D001_D002_Id WHERE D001_Id=D001_Id LIMIT 1)";
                $cClausula .= " left join D149 on D005_Id=D149_D005_Id AND D149_C004_Id={$C004_Id}";
                $cClausula .= " left join D088 on D088_Id=D149_D088_Id ";
                $cClausula .= " where D009_Id='$D009_Id' ";
                $Produto  = mysql_query($cClausula);
                $cErro = mysql_error();
                if (!empty($cErro)) {
                    return "{$cErro}<br />{$cClausula}";
                }

                if (mysql_num_rows($Produto) <= 0) {
                    return "Não foram encontrados dados fiscais para o produto D009 ID {$D009_Id}";
                }

                $mProduto = mysql_fetch_array($Produto);
                
            }
            
        }else if ($T224_Id > 0) {          //ORDEM COMPRA
            $cClausula = " select * ";
            $cClausula .= " from T224 ";
            $cClausula .= " left join D024 on D024_Id=T224_D024_Id ";
            $cClausula .= " left join D018 on D018_Id=D024_D018_Id ";
            $cClausula .= " left join D053 on D053_D018_Id=D024_D018_Id and D053_C004_Id=T224_C004_Id ";
            $cClausula .= " left join C004 on C004_Id=T224_C004_Id ";
            $cClausula .= " left join D006 on D006_Id=T224_D006_Id ";
            $cClausula .= " where T224_Id='$T224_Id' ";
            
            $Cab   = mysql_query($cClausula);
            $cErro = mysql_error();
            
            if (!empty($cErro)) {
                return "{$cErro}<br />{$cClausula}";
                exit;
            }
            $mCab = mysql_fetch_array($Cab);
            
            $D024_Id                 = $mCab['T224_D024_Id'];
            $C004_Id                 = $mCab['T224_C004_Id'];
            $D018_Id                 = $mCab['D024_D018_Id'];
            $Flag_Acp                = $mCab['T224_Flag_ACP'];
            $Flag_Revenda_Consumidor = $mCab['D024_Flag_Revenda_Consumidor'];
            $Flag_Contribuinte       = $mCab['D024_Flag_Contribuinte'];
            $Percentual_Comissao     = $mCab['T224_Percentual_Comissao'];
            $Aliquota_ICMS_Credito   = "";
            
            $sqlT225 = mysql_query("SELECT * FROM T225 WHERE T225_Id = '{$T225_Id}'");
            $T225    = mysql_fetch_array($sqlT225);
            $cErro   = mysql_error();
            if (!empty($cErro)) {
                return "{$cErro}<br />{$cClausula}";
                exit;
            }

            $Origem_Mercadoria = $T225['T225_Flag_Origem_Produto'];
            
            if (empty($T225['T225_D006_Id'])) {
                $D006_Id = $mCab['T224_D006_Id'];
            } else {
                $D006_Id = $T225['T225_D006_Id'];
            }

            //ATUALIZA ALIQUOTAS CONFORME TABELA DO FORNECEDOR
            if ($r_usarTabelaFornecedor == true) {

                $cClausula = " select * ,";
                $cClausula .= " D088_Com_Aliquota.D088_Codigo_ST as D088_Codigo_ST, ";
                $cClausula .= " D088_Sem_Aliquota.D088_Codigo_ST as D088_Codigo_ST_Sem_Aliquota ";
                $cClausula .= " from D006 ";
                $cClausula .= " left join D059 on D059_Id=D006_D059_Id ";
                $cClausula .= " left join D088 as D088_Com_Aliquota ON D088_Com_Aliquota.D088_Id=D006_D088_Id ";
                $cClausula .= " left join D088 as D088_Sem_Aliquota ON D088_Sem_Aliquota.D088_Id=D006_D088_Id_Sem_Aliquota ";
                $cClausula .= " where D006_Id='{$D006_Id}'";
                $D006 = mysql_query($cClausula);
                if (!empty($cErro)) {
                    return "$cErro/p/p$cClausula";
                }
                $mD006 = mysql_fetch_array($D006);
                                
                $cClausula = " select ";
                $cClausula .= " *, ";
                $cClausula .= " D069_Aliquota_IPI as Aliquota_IPI, ";
                $cClausula .= " 0 as Percentual_Reducao_IPI, ";
                $cClausula .= " D069_Aliquota_ICMS as Aliquota_ICMS, ";
                $cClausula .= " D069_Aliquota_PIS as Aliquota_PIS, ";
                $cClausula .= " D069_Aliquota_COFINS as Aliquota_COFINS, ";
                $cClausula .= " D069_Percentual_Reducao_ICMS as Percentual_Reducao_ICMS, ";
                $cClausula .= " D069_Flag_Origem_Produto as D049_Origem_Mercadoria, ";
                $cClausula .= " D069_ST_VA as D149_Valor_VA, ";
                $cClausula .= " D069_D006_Id, ";
                $cClausula .= " D069_Flag_ST as Flag_ST, ";
                $cClausula .= " D069_ICMS_Inter_Estadual as D053_ST_ICMS_Inter_Estadual_Compra, ";
                $cClausula .= " D069_ST_ICMS_Interno as D053_ST_ICMS_Interno_Compra, ";
                $cClausula .= " D069_Percentual_ST as Percentual_ST ";
                $cClausula .= " from D069 ";
                $cClausula .= " left join D070 on D070_Id=D069_D070_Id ";
                $cClausula .= " left join D009 on D009_Id=D069_D009_Id ";
                $cClausula .= " left join D049 on D049_Id=D009_D049_Id ";
                $cClausula .= " left join D001 on D001_Id=D049_D001_Id ";
                $cClausula .= " left join D041 on D041_D049_Id=D049_Id ";
                $cClausula .= " left join D037 on D037_Id=D001_D037_Id ";
                $cClausula .= " left join D024 on D024_Id={$D024_Id} ";
                $cClausula .= " left join D018 on D018_Id=D024_D018_Id ";
                $cClausula .= " left join D005 on D005_Id=D001_D005_Id OR D005_Id = (SELECT T025_D005_Id FROM D001 LEFT JOIN T025 ON T025_D015_Id=D001_D015_Id AND T025_D002_Id=D001_D002_Id WHERE D001_Id=D001_Id LIMIT 1)";
                $cClausula .= " left join D149 on D005_Id=D149_D005_Id AND D149_C004_Id={$C004_Id}";
                $cClausula .= " left join D088 on D088_Id=D149_D088_Id ";
                $cClausula .= " where D069_D009_Id='$D009_Id' AND D069_Flag_Tabela_Principal='S' ";
                $cClausula .= " AND D070_D024_Id='$D024_Id' ";
                $cClausula .= " ORDER BY D069_Id desc ";
                $Produto  = mysql_query($cClausula);
                $cErro = mysql_error();
    
                if (!empty($cErro)) {
                    return "{$cErro}<br />{$cClausula}";
                    exit;

                }

                $mProduto = mysql_fetch_array($Produto);
                // print_r($mProduto);
                // die();

                $Flag_ST = $mProduto['Flag_ST'];
                $mCab['D053_ST_ICMS_Inter_Estadual_Compra'] = $mProduto['D053_ST_ICMS_Inter_Estadual_Compra'];
                $mCab['D053_ST_ICMS_Interno_Compra']        = $mProduto['D053_ST_ICMS_Interno_Compra'];
                $D006_Id                                    = $mProduto['D069_D006_Id'];

            }
            //ATUALIZA ALIQUOTAS CONFORME O SISTEMA (JEITO PADRÃO)
            else{
                $cClausula = " select * ,";
                $cClausula .= " D088_Com_Aliquota.D088_Codigo_ST as D088_Codigo_ST, ";
                $cClausula .= " D088_Sem_Aliquota.D088_Codigo_ST as D088_Codigo_ST_Sem_Aliquota ";
                $cClausula .= " from D006 ";
                $cClausula .= " left join D059 on D059_Id=D006_D059_Id ";
                $cClausula .= " left join D088 as D088_Com_Aliquota ON D088_Com_Aliquota.D088_Id=D006_D088_Id ";
                $cClausula .= " left join D088 as D088_Sem_Aliquota ON D088_Sem_Aliquota.D088_Id=D006_D088_Id_Sem_Aliquota ";
                $cClausula .= " where D006_Id='{$D006_Id}'";
                $D006 = mysql_query($cClausula);
                if (!empty($cErro)) {
                    return "$cErro/p/p$cClausula";
                }
                $mD006 = mysql_fetch_array($D006);
                               
                $cClausula = " select ";
                $cClausula .= " *, ";
                $cClausula .= " D009_IPI_compra('$C004_Id',D009_Id,'$D024_Id','$D006_Id','1','$Flag_Acp',NULL) as Aliquota_IPI, ";
                $cClausula .= " D009_IPI_compra('$C004_Id',D009_Id,'$D024_Id','$D006_Id','2','$Flag_Acp',NULL) as Percentual_Reducao_IPI, ";
                $cClausula .= " D009_IPV_ICMS('$C004_Id','$D024_Id',D009_Id,'$D006_Id','$D018_Id','$Flag_Revenda_Consumidor','$Percentual_Comissao','$Flag_Acp',2) as Aliquota_ICMS, ";
                $cClausula .= " D009_IPV_ICMS('$C004_Id','$D024_Id',D009_Id,'$D006_Id','$D018_Id','$Flag_Revenda_Consumidor','$Percentual_Comissao','$Flag_Acp',3) as Percentual_Reducao_ICMS, ";
                $cClausula .= " IFNULL(D009_Origem_Mercadoria, '') as D009_Origem_Mercadoria ";
                $cClausula .= " from D001 ";
                $cClausula .= " left join D049 on D001_Id=D049_D001_Id ";
                $cClausula .= " left join D009 on D049_Id=D009_D049_Id ";
                $cClausula .= " left join D041 on D041_D049_Id=D049_Id ";
                $cClausula .= " left join D037 on D037_Id=D001_D037_Id ";
                $cClausula .= " left join D024 on D024_Id={$D024_Id} ";
                $cClausula .= " left join D018 on D018_Id=D024_D018_Id ";
                $cClausula .= " left join D005 on D005_Id=D001_D005_Id OR D005_Id = (SELECT T025_D005_Id FROM D001 LEFT JOIN T025 ON T025_D015_Id=D001_D015_Id AND T025_D002_Id=D001_D002_Id WHERE D001_Id=D001_Id LIMIT 1)";
                $cClausula .= " left join D149 on D005_Id=D149_D005_Id AND D149_C004_Id={$C004_Id}";
                $cClausula .= " left join D088 on D088_Id=D149_D088_Id ";
                $cClausula .= " where D009_Id='$D009_Id' ";
                $Produto  = mysql_query($cClausula);
                $mProduto = mysql_fetch_array($Produto);

            }
        } else if ($D070_Id > 0) {     //NOTA FISCAL 
            
            $cClausula = " select * ";
            $cClausula .= " from D070 ";
            $cClausula .= " left join D024 on D024_Id=D070_D024_Id ";
            $cClausula .= " left join D018 on D018_Id=D024_D018_Id ";
            $cClausula .= " left join D020 on D020_Id=D024_D020_Id ";
            $cClausula .= " left join D053 on D053_D018_Id=D024_D018_Id and D053_C004_Id=D070_C004_Id ";
            $cClausula .= " left join C004 on C004_Id=D070_C004_Id ";
            $cClausula .= " where D070_Id='$D070_Id' ";
            
            $Cab   = mysql_query($cClausula);
            $cErro = mysql_error();
            
            if (!empty($cErro)) {
                return "{$cErro}<br />{$cClausula}";
                exit;
            }
            $mCab = mysql_fetch_array($Cab);
            
            $D024_Id                 = $mCab['D070_D024_Id'];
            $C004_Id                 = $mCab['C004_Id'];
            $D018_Id                 = $mCab['D024_D018_Id'];
            $Flag_Acp                = 6;
            $Flag_Revenda_Consumidor = $mCab['D024_Flag_Revenda_Consumidor'];
            $Flag_Contribuinte       = $mCab['D024_Flag_Contribuinte'];
            $Percentual_Comissao     = 0;
            $Aliquota_ICMS_Credito   = "";
            
            $sqlD069 = mysql_query("SELECT * FROM D069 WHERE D069_Id = '{$D069_Id}'");
            $D069    = mysql_fetch_array($sqlD069);
            $cErro   = mysql_error();
            if (!empty($cErro)) {
                return "{$cErro}<br />{$cClausula}";
                exit;
            }
            $Origem_Mercadoria = $D069['D069_Flag_Origem_Produto'];
            
            $D006_Id = $D069['D069_D006_Id'];
            
            $cClausula = " select * ,";
            $cClausula .= " D088_Com_Aliquota.D088_Codigo_ST as D088_Codigo_ST, ";
            $cClausula .= " D088_Sem_Aliquota.D088_Codigo_ST as D088_Codigo_ST_Sem_Aliquota ";
            $cClausula .= " from D006 ";
            $cClausula .= " left join D059 on D059_Id=D006_D059_Id ";
            $cClausula .= " left join D088 as D088_Com_Aliquota ON D088_Com_Aliquota.D088_Id=D006_D088_Id ";
            $cClausula .= " left join D088 as D088_Sem_Aliquota ON D088_Sem_Aliquota.D088_Id=D006_D088_Id_Sem_Aliquota ";
            $cClausula .= " where D006_Id='{$D006_Id}'";
            $D006 = mysql_query($cClausula);
            if (!empty($cErro)) {
                return "$cErro/p/p$cClausula";
            }
            $mD006 = mysql_fetch_array($D006);
                        
            if($D009_Id > 0){
                $cClausula = " select ";
                $cClausula .= " *, ";
                $cClausula .= " D009_IPI_compra('$C004_Id',D009_Id,'$D024_Id','$D006_Id','1','$Flag_Acp','{$D069['D069_D005_Id']}') as Aliquota_IPI, ";
                $cClausula .= " D009_IPI_compra('$C004_Id',D009_Id,'$D024_Id','$D006_Id','2','$Flag_Acp','{$D069['D069_D005_Id']}') as Percentual_Reducao_IPI, ";
                $cClausula .= " D009_IPV_ICMS('$C004_Id','$D024_Id',D009_Id,'$D006_Id','$D018_Id','$Flag_Revenda_Consumidor','$Percentual_Comissao','$Flag_Acp',2) as Aliquota_ICMS, ";
                $cClausula .= " D009_IPV_ICMS('$C004_Id','$D024_Id',D009_Id,'$D006_Id','$D018_Id','$Flag_Revenda_Consumidor','$Percentual_Comissao','$Flag_Acp',3) as Percentual_Reducao_ICMS, ";
                $cClausula .= " IFNULL(D009_Origem_Mercadoria, '') as D009_Origem_Mercadoria ";
                $cClausula .= " from D001 ";
                $cClausula .= " left join D049 on D001_Id=D049_D001_Id ";
                $cClausula .= " left join D009 on D049_Id=D009_D049_Id ";
                $cClausula .= " left join D041 on D041_D049_Id=D049_Id ";
                $cClausula .= " left join D037 on D037_Id=D001_D037_Id ";
                $cClausula .= " left join D024 on D024_Id={$D024_Id} ";
                $cClausula .= " left join D018 on D018_Id=D024_D018_Id ";
                $cClausula .= " left join D005 on D005_Id='{$D069['D069_D005_Id']}' ";
                $cClausula .= " left join D149 on D005_Id=D149_D005_Id AND D149_C004_Id={$C004_Id}";
                $cClausula .= " left join D088 on D088_Id=D149_D088_Id ";
                $cClausula .= " where D009_Id='$D009_Id' ";
                $Produto  = mysql_query($cClausula);
                $mProduto = mysql_fetch_array($Produto);
            } else {
                $sqlD069IPI = mysql_query("SELECT D149.*, D149_Aliquota_IPI AS Aliquota_IPI, D149_Percentual_Reducao_IPI AS Percentual_Reducao_IPI, D069_Flag_Origem_Produto AS D049_Origem_Mercadoria FROM D069
                    LEFT JOIN D149 ON D149_D005_Id = D069_D005_Id AND D149_C004_Id = '{$g['empresaAtual']}'
                    WHERE D069_Id = '{$D069_Id}'");
                $mProduto = mysql_fetch_array($sqlD069IPI);
                // retirado || $mCab['D020_Flag_IPI'] == 'S' por ser compra e a cidade destino é a do cliente
                if($Flag_Acp == '3' || $mD006['D006_Flag_Isento_IPI'] == 'S'  || $mCab['D024_Flag_Isento_IPI'] == '1' || $rowD069IPI['D149_Flag_Isento_IPI'] == '1'){
                    $mProduto['Aliquota_IPI'] = 0;
                    $mProduto['Percentual_Reducao_IPI'] = 0;
                }
            }
                        
        } else if ($T013_Id > 0) {       //PRODUTO           
            $cClausula = " select * ";
            $cClausula .= " from T013 ";
            $cClausula .= " left join D024 on D024_Id=T013_D024_Id ";
            $cClausula .= " left join D018 on D018_Id=D024_D018_Id ";
            $cClausula .= " left join D053 on D053_D018_Id=D024_D018_Id and D053_C004_Id=T013_C004_Id ";
            $cClausula .= " left join C004 on C004_Id=T013_C004_Id ";
            // $cClausula .= " left join T134 on T134_Id=T013_T134_Id ";//Não tem vinculo com o Simples Nacional
            $cClausula .= " left join D006 on D006_Id=T013_D006_Id ";
            $cClausula .= " where T013_Id='$T013_Id' ";
                        
            $Cab   = mysql_query($cClausula);
            $cErro = mysql_error();
            
            if (!empty($cErro)) {
                return "{$cErro}<br />{$cClausula}";
                exit;
            }
            $mCab = mysql_fetch_array($Cab);
            
            $D024_Id                 = $mCab['T013_D024_Id'];
            $C004_Id                 = $mCab['T013_C004_Id'];
            $D018_Id                 = $mCab['D024_D018_Id'];
            // $Flag_Acp                = $mCab['T013_Flag_ACP']; //Modificar para o correspondente em T013
            $Flag_Acp                = 6; //Fixo 6 NF-e na nota de saida         
            $Flag_Revenda_Consumidor = $mCab['D024_Flag_Revenda_Consumidor'];
            $Flag_Contribuinte       = $mCab['D024_Flag_Contribuinte'];
            $Percentual_Comissao     = $mCab['T013_Percentual_Comissao_Representante'];
            // $Aliquota_ICMS_Credito   = $mCab['T134_ICMS'];
            $Aliquota_ICMS_Credito = "";

            
            $sqlT014 = mysql_query("SELECT * FROM T014 WHERE T014_Id = '{$T014_Id}'");
            $cErro   = mysql_error();
            if (!empty($cErro)) {
                return "{$cErro}<br />{$cClausula}";
                exit;
            }
            $T014    = mysql_fetch_array($sqlT014);
            $D006_Id = $T014['T014_D006_Id'];
            
            $cClausula = " select * ,";
            $cClausula .= " D088_Com_Aliquota.D088_Codigo_ST as D088_Codigo_ST, ";
            $cClausula .= " D088_Sem_Aliquota.D088_Codigo_ST as D088_Codigo_ST_Sem_Aliquota ";
            $cClausula .= " from D006 ";
            $cClausula .= " left join D059 on D059_Id=D006_D059_Id ";
            $cClausula .= " left join D088 as D088_Com_Aliquota ON D088_Com_Aliquota.D088_Id=D006_D088_Id ";
            $cClausula .= " left join D088 as D088_Sem_Aliquota ON D088_Sem_Aliquota.D088_Id=D006_D088_Id_Sem_Aliquota ";
            $cClausula .= " where D006_Id='{$D006_Id}'";
            $D006 = mysql_query($cClausula);            
            if (!empty($cErro)) {
                return "$cErro/p/p$cClausula";
            }
            $mD006 = mysql_fetch_array($D006);
                        
            $cClausula = " select ";
            $cClausula .= " *, ";
            $cClausula .= " C004_Percentual_PIS as Aliquota_PIS, ";
            $cClausula .= " C004_Percentual_COFINS as Aliquota_COFINS, ";
            $cClausula .= " D009_IPI_compra('$C004_Id',D009_Id,'$D024_Id','$D006_Id','1','$Flag_Acp',NULL) as Aliquota_IPI, ";
            $cClausula .= " D009_IPI_compra('$C004_Id',D009_Id,'$D024_Id','$D006_Id','2','$Flag_Acp',NULL) as Percentual_Reducao_IPI, ";
            $cClausula .= " D009_IPV_ICMS('$C004_Id','$D024_Id',D009_Id,'$D006_Id','$D018_Id','$Flag_Revenda_Consumidor','$Percentual_Comissao','$Flag_Acp',2) as Aliquota_ICMS, ";
            $cClausula .= " D009_IPV_ICMS('$C004_Id','$D024_Id',D009_Id,'$D006_Id','$D018_Id','$Flag_Revenda_Consumidor','$Percentual_Comissao','$Flag_Acp',3) as Percentual_Reducao_ICMS, ";
            $cClausula .= " IFNULL(D009_Origem_Mercadoria, '') as D009_Origem_Mercadoria ";
            $cClausula .= " from D001 ";
            $cClausula .= " left join D049 on D001_Id=D049_D001_Id ";
            $cClausula .= " left join D009 on D049_Id=D009_D049_Id ";
            $cClausula .= " left join D041 on D041_D049_Id=D049_Id ";
            $cClausula .= " left join D037 on D037_Id=D001_D037_Id ";
            $cClausula .= " left join D024 on D024_Id={$D024_Id} ";
            $cClausula .= " left join D018 on D018_Id=D024_D018_Id ";
            $cClausula .= " left join D005 on D005_Id=D001_D005_Id OR D005_Id = (SELECT T025_D005_Id FROM D001 LEFT JOIN T025 ON T025_D015_Id=D001_D015_Id AND T025_D002_Id=D001_D002_Id WHERE D001_Id=D001_Id LIMIT 1)";
            $cClausula .= " left join D149 on D005_Id=D149_D005_Id AND D149_C004_Id={$C004_Id}";
            $cClausula .= " left join D088 on D088_Id=D149_D088_Id ";
            $cClausula .= " left join C004 on C004_Id={$C004_Id}";
            $cClausula .= " where D009_Id='$D009_Id' ";
            $Produto  = mysql_query($cClausula);
            $mProduto = mysql_fetch_array($Produto);                        
            
            if($mD006['D006_Flag_Isento_PIS'] == 'N' && !empty($mProduto['D149_Flag_Isento_PIS'])) {
                $mD006['D006_Flag_Isento_PIS'] = $mProduto['D149_Flag_Isento_PIS'];  
            }

            if($mD006['D006_Flag_Isento_COFINS'] == 'N' && !empty($mProduto['D149_Flag_Isento_COFINS'])) {
                $mD006['D006_Flag_Isento_COFINS'] = $mProduto['D149_Flag_Isento_COFINS'];  
            }            
            //C004_Flag_Isento_IPI 1 = Sim 2 = Não               
            if($mProduto['C004_Flag_Isento_IPI'] == 1) {                
                $mProduto['Aliquota_IPI'] = 0;
                $mProduto['Percentual_Reducao_IPI'] = 0;
            }

        } else {
            
            // Nenhum dado recebido
            
        }

        // Utilizar retornaCFOP apenas se o orcamento for uma Venda
        /*if ($mCab['D006_Flag_Venda_Compra_Outros']=='V')
        {
        $CFOP=$this->retornaCFOP($D024_Id,$D009_Id);
        
        if ($CFOP>0){
        $D006_Id = $CFOP;
        }else if ($CFOP===false){
        $D006_Id = $D006_Id;
        }else{
        return $CFOP;
        }
        }*/

        if ($r_usarTabelaFornecedor == false) {            
            require_once("bibliotecas/classes/CAD002.php");
            $CAD002 = new CAD002();
            
            if (!empty($Origem_Mercadoria)) {
                $mProduto['D049_Origem_Mercadoria'] = $Origem_Mercadoria;

                if($mProduto['D009_Origem_Mercadoria'] != ''){
                    $mProduto['D049_Origem_Mercadoria'] = $mProduto['D009_Origem_Mercadoria'];
                }
            }
            
            $mProduto['Aliquota_ICMS']        = $CAD002->retornaICMSCompra($C004_Id, $D024_Id, $mProduto['D009_Id'], $D006_Id, $D018_Id, $Flag_Revenda_Consumidor, $Percentual_Comissao, $Flag_Acp, '2',$Origem_Mercadoria);
            //die("retornaICMS({$C004_Id}, {$D024_Id}, {$mProduto['D009_Id']}, {$D006_Id}, {$D018_Id}, {$Flag_Revenda_Consumidor}, {$Percentual_Comissao}, {$Flag_Acp}, '2') ALÍQUOTA->".$Aliquota_ICMS);
            $mProduto['Percentual_Reducao_ICMS'] = $CAD002->retornaICMSCompra($C004_Id, $D024_Id, $mProduto['D009_Id'], $D006_Id, $D018_Id, $Flag_Revenda_Consumidor, $Percentual_Comissao, $Flag_Acp, '3',$Origem_Mercadoria);

            if ($mProduto['D049_Origem_Mercadoria'] == '1' or $mProduto['D049_Origem_Mercadoria'] == '2' or $mProduto['D049_Origem_Mercadoria'] == '7' or $mProduto['D049_Origem_Mercadoria'] == '3') {
                $mCab['D053_ST_ICMS_Inter_Estadual_Compra'] =  $mProduto['Aliquota_ICMS'];
            }

            //Verifica as exceções de valor de MVA e ICMS da NCM                        
            if (!empty($mProduto['D149_Regras_MVA_Compra'])) {

                preg_match_all("/(MVA|MVAA) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA_Compra'], $D149_Regras_MVA);
                $valoresRegras = $D149_Regras_MVA[3];
                if (in_array($mCab['D018_UF'], $D149_Regras_MVA[2])) {
                    $regra = array_search($mProduto['D018_UF'], $D149_Regras_MVA[2]);                    
                    if (!empty($valoresRegras[$regra])) {
                        $mProduto['D149_Valor_VA'] = str_replace(",", ".", $valoresRegras[$regra]);   
                    }
                }

                preg_match_all("/(ICM|ICMS) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA_Compra'], $D149_Regras_ICM);
                $valoresRegras = $D149_Regras_ICM[3];
                if (in_array($mCab['D018_UF'], $D149_Regras_ICM[2])) {
                    $regra = array_search($mProduto['D018_UF'], $D149_Regras_ICM[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mProduto['Aliquota_ICMS'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                } else if (in_array('XX', $D149_Regras_ICM[2])) {
                    $regra = array_search('XX', $D149_Regras_ICM[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mProduto['Aliquota_ICMS'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                }

                preg_match_all("/(ICMIE|ICMSIE) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA_Compra'], $D149_Regras_ICM);
                $valoresRegras = $D149_Regras_ICM[3];
                if (in_array($mCab['D018_UF'], $D149_Regras_ICM[2])) {
                    $regra = array_search($mProduto['D018_UF'], $D149_Regras_ICM[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mCab['D053_ST_ICMS_Inter_Estadual_Compra'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                } else if (in_array('XX', $D149_Regras_ICM[2])) {
                    $regra = array_search('XX', $D149_Regras_ICM[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mCab['D053_ST_ICMS_Inter_Estadual_Compra'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                }

                preg_match_all("/(ICMINT|ICMSINT) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA_Compra'], $D149_Regras_ICM);
                $valoresRegras = $D149_Regras_ICM[3];
                if (in_array($mCab['D018_UF'], $D149_Regras_ICM[2])) {
                    $regra = array_search($mProduto['D018_UF'], $D149_Regras_ICM[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mCab['D053_ST_ICMS_Interno_Compra'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                } else if (in_array('XX', $D149_Regras_ICM[2])) {
                    $regra = array_search('XX', $D149_Regras_ICM[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mCab['D053_ST_ICMS_Interno_Compra'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                }
                
                preg_match_all("/(REDICM|REDICMS) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA_Compra'], $D149_Regras_ICM);
                $valoresRegras = $D149_Regras_ICM[3];
                if (in_array($mCab['D018_UF'], $D149_Regras_ICM[2])) {
                    $regra = array_search($mProduto['D018_UF'], $D149_Regras_ICM[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mProduto['Percentual_Reducao_ICMS'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                } else if (in_array('XX', $D149_Regras_ICM[2])) {
                    $regra = array_search('XX', $D149_Regras_ICM[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mProduto['Percentual_Reducao_ICMS'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                }

                preg_match_all("/(REDICMIEST|REDICMSIEST) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA_Compra'], $D149_Regras_ICM);
                $valoresRegras = $D149_Regras_ICM[3];
                if (in_array($mCab['D018_UF'], $D149_Regras_ICM[2])) {
                    $regra = array_search($mProduto['D018_UF'], $D149_Regras_ICM[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mProduto['Percentual_Reducao_ICMS_IE_ST'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                } else if (in_array('XX', $D149_Regras_ICM[2])) {
                    $regra = array_search('XX', $D149_Regras_ICM[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mProduto['Percentual_Reducao_ICMS_IE_ST'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                }

                preg_match_all("/(REDICMINTST|REDICMSINTST) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA_Compra'], $D149_Regras_ICM);
                $valoresRegras = $D149_Regras_ICM[3];
                if (in_array($mCab['D018_UF'], $D149_Regras_ICM[2])) {
                    $regra = array_search($mProduto['D018_UF'], $D149_Regras_ICM[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mProduto['Percentual_Reducao_ICMS_INT_ST'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                } else if (in_array('XX', $D149_Regras_ICM[2])) {
                    $regra = array_search('XX', $D149_Regras_ICM[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mProduto['Percentual_Reducao_ICMS_INT_ST'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                }

                preg_match_all("/(REDICMST|REDICMSST) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA_Compra'], $D149_Regras_ICM);
                $valoresRegras = $D149_Regras_ICM[3];
                if (in_array($mCab['D018_UF'], $D149_Regras_ICM[2])) {
                    $regra = array_search($mProduto['D018_UF'], $D149_Regras_ICM[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mD006['D006_ST_ICMS_Interno_Reducao'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                } else if (in_array('XX', $D149_Regras_ICM[2])) {
                    $regra = array_search('XX', $D149_Regras_ICM[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mD006['D006_ST_ICMS_Interno_Reducao'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                }

                if($mProduto['Aliquota_IPI'] > 0){
                    preg_match_all("/(IPI|IPI) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA_Compra'], $D149_Regras_IPI);
                    $valoresRegras = $D149_Regras_IPI[3];
                    if (in_array($mCab['D018_UF'], $D149_Regras_IPI[2])) {
                        $regra = array_search($mProduto['D018_UF'], $D149_Regras_IPI[2]);
                        if (!empty($valoresRegras[$regra])) {
                            $mProduto['Aliquota_IPI'] = str_replace(",", ".", $valoresRegras[$regra]);
                        }
                    } else if (in_array('XX', $D149_Regras_IPI[2])) {
                        $regra = array_search('XX', $D149_Regras_IPI[2]);
                        if (!empty($valoresRegras[$regra])) {
                            $mProduto['Aliquota_IPI'] = str_replace(",", ".", $valoresRegras[$regra]);
                        }
                    }   
                }             
            }
            
            if ($mProduto['D149_Aliquota_ICMS_Interno_Compra'] > 0) {
                $mCab['D053_ST_ICMS_Interno_Compra'] = $mProduto['D149_Aliquota_ICMS_Interno_Compra'];
            }

            if ($mProduto['D049_Origem_Mercadoria'] == '1' or $mProduto['D049_Origem_Mercadoria'] == '2' or $mProduto['D049_Origem_Mercadoria'] == '7' or $mProduto['D049_Origem_Mercadoria'] == '3') {
                //$mCab['D053_ST_ICMS_Inter_Estadual_Compra'] =  $mCab['D053_Aliquota_ICMS_Importado_Compra'];
                
                preg_match_all("/(ICMIMP|ICMSIMP) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA_Compra'], $D149_Regras_ICM);
                $valoresRegras = $D149_Regras_ICM[3];
                if (in_array($mCab['D018_UF'], $D149_Regras_ICM[2])) {
                    $regra = array_search($mProduto['D018_UF'], $D149_Regras_ICM[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mProduto['Aliquota_ICMS'] = str_replace(",", ".", $valoresRegras[$regra]);
                        $mCab['D053_ST_ICMS_Inter_Estadual_Compra'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                } else if (in_array('XX', $D149_Regras_ICM[2])) {
                    $regra = array_search('XX', $D149_Regras_ICM[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mProduto['Aliquota_ICMS'] = str_replace(",", ".", $valoresRegras[$regra]);
                        $mCab['D053_ST_ICMS_Inter_Estadual_Compra'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                }

                preg_match_all("/(ICMIEIMP|ICMSIEIMP) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA_Compra'], $D149_Regras_ICM);
                $valoresRegras = $D149_Regras_ICM[3];
                if (in_array($mCab['D018_UF'], $D149_Regras_ICM[2])) {
                    $regra = array_search($mProduto['D018_UF'], $D149_Regras_ICM[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mCab['D053_ST_ICMS_Inter_Estadual_Compra'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                } else if (in_array('XX', $D149_Regras_ICM[2])) {
                    $regra = array_search('XX', $D149_Regras_ICM[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mCab['D053_ST_ICMS_Inter_Estadual_Compra'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                }

                preg_match_all("/(ICMINTIMP|ICMSINTIMP) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA_Compra'], $D149_Regras_ICM);
                $valoresRegras = $D149_Regras_ICM[3];
                if (in_array($mCab['D018_UF'], $D149_Regras_ICM[2])) {
                    $regra = array_search($mProduto['D018_UF'], $D149_Regras_ICM[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mCab['D053_ST_ICMS_Interno_Compra'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                } else if (in_array('XX', $D149_Regras_ICM[2])) {
                    $regra = array_search('XX', $D149_Regras_ICM[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mCab['D053_ST_ICMS_Interno_Compra'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                }

                preg_match_all("/(MVAIMP|MVAAIMP) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA_Compra'], $D149_Regras_MVA);
                $valoresRegras = $D149_Regras_MVA[3];
                if (in_array($mCab['D018_UF'], $D149_Regras_MVA[2])) {
                    $regra = array_search($mProduto['D018_UF'], $D149_Regras_MVA[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mProduto['D149_Valor_VA'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                } else if (in_array('XX', $D149_Regras_MVA[2])) {
                    $regra = array_search('XX', $D149_Regras_MVA[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mProduto['D149_Valor_VA'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                }
            }
                                    
            if($mD006['D006_Flag_Calcula_FCP'] == 'S' || $mD006['D006_Flag_Calcula_FCP_ST'] == 'S' || $mD006['D006_Flag_Calcula_FCP_Retido'] == 'S'){                    
                
                $percentualFCP = $mCab['D053_Percentual_FCP_Compra']; 
                if($mD006['D006_Percentual_FCP'] > 0){
                    $percentualFCP = $mD006['D006_Percentual_FCP'];
                }                        
                
            }
            // Da diferença no valor do MVA usando function TRIB_ST_VA_Ajustada
            if($mD006['D006_Somar_FCP_ICMS_Interno'] == 'S'){                
                $mCab['D053_ST_ICMS_Interno_Compra'] = $mCab['D053_ST_ICMS_Interno_Compra'] + $mCab['D053_Percentual_FCP_Compra'];
            }

            $D053_ST_ICMS_Inter_Estadual = $mCab['D053_ST_ICMS_Inter_Estadual_Compra'];
            $D053_ST_ICMS_Interno        = $mCab['D053_ST_ICMS_Interno_Compra'];
        
            if ($mD006['D006_Flag_Isento_ICMS'] == 'S') {
                $mProduto['Aliquota_ICMS'] = 0;
            }
            if ($mD006['D006_ST_ICMS_Inter_Estadual_MVA'] > 0)
                $mCab['D053_ST_ICMS_Inter_Estadual_Compra'] = $mD006['D006_ST_ICMS_Inter_Estadual_MVA'];
            if ($mD006['D006_ST_ICMS_Interno_MVA'] > 0)
                $mCab['D053_ST_ICMS_Interno_Compra'] = $mD006['D006_ST_ICMS_Interno_MVA'];
            
            if ($mD006['D006_ST_MVA'] > 0)
                $mProduto['D149_Valor_VA'] = $mD006['D006_ST_MVA'];
            
            
            $mCab['D053_ST_ICMS_Inter_Estadual_Compra'] = $D053_ST_ICMS_Inter_Estadual;
            $mCab['D053_ST_ICMS_Interno_Compra']        = $D053_ST_ICMS_Interno;
            
            if ($mD006['D006_ST_ICMS_Inter_Estadual'] > 0)
                $mCab['D053_ST_ICMS_Inter_Estadual_Compra'] = $mD006['D006_ST_ICMS_Inter_Estadual'];
            if ($mD006['D006_ST_ICMS_Interno'] > 0)
                $mCab['D053_ST_ICMS_Interno_Compra'] = $mD006['D006_ST_ICMS_Interno'];
            
            if ($mD006['D006_Flag_ST_VA'] == "N") {
                $mProduto['D149_Valor_VA'] = 0;
            }
            
            if ((($Flag_Acp != '1') and ($Flag_Acp != '6')) or ($mD006['D006_Flag_Substituicao_Tributaria'] != 'S')) {
                log("Zerar D149_Flag_ST caso ACP diferente de nota ou CFOP sem ST = ".$mProduto['D149_Flag_ST'].' D006_Flag_Substituicao_Tributaria = '.$mD006['D006_Flag_Substituicao_Tributaria'].' Flag_ACP = '.$Flag_Acp);
                $mProduto['D149_Flag_ST']  = "N";
                $mProduto['D149_Valor_VA'] = 0;
            }
            
            if ($mD006['D006_Flag_Substituicao_Tributaria'] == 'S' && $mProduto['D149_Flag_ST'] == 'S') {
                $Flag_ST = 'S';
            } else {
                log("Zerar Flag_ST caso NCM ou CFOP sem ST = ".$mProduto['D149_Flag_ST'].' D006_Flag_Substituicao_Tributaria = '.$mD006['D006_Flag_Substituicao_Tributaria'].' Flag_ACP = '.$Flag_Acp);
                $Flag_ST                                      = 'N';
                //$mCab['D053_ST_ICMS_Interno_Compra']          = 0;
                //$mCab['D053_ST_ICMS_Inter_Estadual_Compra']   = 0;
                $mD006['D006_ST_ICMS_Interno_Reducao_Compra'] = 0;
                $mProduto['D149_Valor_VA']                    = 0;
            }
            
            //Aplica a redução de MVA
            if ($mD006['D006_Reducao_MVA'] > 0 && $mProduto['D149_Valor_VA'] > 0) {
                $mProduto['D149_Valor_VA'] = $mProduto['D149_Valor_VA'] * ((100 - $mD006['D006_Reducao_MVA']) / 100);
            }
            
            $Flag_Situacao_Tributaria = '0' . $mD006['D059_Codigo_ST'];
            
            
            // Verificando a Situação tributária
            if ((!empty($mProduto['D049_Origem_Mercadoria']) or $mProduto['D049_Origem_Mercadoria'] == '0') AND $mProduto['D049_Origem_Mercadoria'] != '') {
                $Flag_Situacao_Tributaria = $mProduto['D049_Origem_Mercadoria'] . $mD006['D059_Codigo_ST'];
                
            } else if (!empty($mProduto['D049_Flag_Nacional_Importado'])) {
                if ($mProduto['D049_Flag_Nacional_Importado'] == "N") {
                    $Flag_Situacao_Tributaria = '0' . $mD006['D059_Codigo_ST'];
                } else {
                    $Flag_Situacao_Tributaria = '1' . $mD006['D059_Codigo_ST'];
                }
            } else if (!empty($mProduto['D001_Flag_Tipo'])) {
                if ($mProduto['D001_Flag_Tipo'] == "PRODUZIDO") {
                    $Flag_Situacao_Tributaria = '1' . $mD006['D059_Codigo_ST'];
                } else {
                    $Flag_Situacao_Tributaria = '0' . $mD006['D059_Codigo_ST'];
                }
            }
            
            // Verifica se tem reducao na aliquota do IPI
            if($mProduto['Aliquota_IPI'] > 0 && $mD006['D006_Reducao_Aliquota_IPI'] > 0){
                $mProduto['Aliquota_IPI'] = $mProduto['Aliquota_IPI'] * ((100 - $mD006['D006_Reducao_Aliquota_IPI']) / 100);
            }

            if($mProduto['D149_Aliquota_PIS'] > 0){
                $mProduto['Aliquota_PIS'] = $mProduto['D149_Aliquota_PIS'];               
            }

            if($mProduto['D149_Aliquota_COFINS'] > 0){
                $mProduto['Aliquota_COFINS'] = $mProduto['D149_Aliquota_COFINS'];      
            }
            
            //Verifica qual CST IPI usar
            if($mProduto['Aliquota_IPI'] <=0 && $mD006['D006_D088_Id_Sem_Aliquota'] > 0) {
                $mD006['D088_Codigo_ST'] = $mD006['D088_Codigo_ST_Sem_Aliquota'];
            }

            // Buscando o percentual de tributos (lei transparências)
            $D149a  = mysql_query("SELECT D149_Percentual_Trib_Transparencia FROM D149 WHERE D149_Id='{$mProduto['D149_Id']}'");
            $mD149a = mysql_fetch_array($D149a);
            
            $D006a  = mysql_query("SELECT D006_Percentual_Trib_Transparencia FROM D006 WHERE D006_Id='{$D006_Id}'");
            $mD006a = mysql_fetch_array($D006a);
            
            if (!empty($mD149a['D149_Percentual_Trib_Transparencia']) AND $mD149a['D149_Percentual_Trib_Transparencia'] > 0) {
                
                $Percentual_Trib_Transparencia = $mD149a['D149_Percentual_Trib_Transparencia'];
                
            } else if (!empty($mD006a['D006_Percentual_Trib_Transparencia']) AND $mD006a['D006_Percentual_Trib_Transparencia'] > 0) {
                
                $Percentual_Trib_Transparencia = $mD006a['D006_Percentual_Trib_Transparencia'];
                
            } else {
                
                $Percentual_Trib_Transparencia = 0;
            }
        }    

        // Definindo o array de retorno
        $array = array();
        $array['D069_Id']                       = @$mProduto['D069_Id'];
        $array['D005_Id']                       = @$mProduto['D005_Id'];
        $array['D149_Id']                       = @$mProduto['D149_Id'];
        $array['Classificacao_Fiscal']          = @$mProduto['D005_Classificacao_Fiscal'];
        $array['D006_Id']                       = @$D006_Id;
        $array['Aliquota_IPI']                  = @$mProduto['Aliquota_IPI']; 
        $array['Aliquota_ICMS']                 = @$mProduto['Aliquota_ICMS'];       
        $array['Aliquota_PIS']                  = @$mProduto['Aliquota_PIS'];
        $array['Aliquota_COFINS']               = @$mProduto['Aliquota_COFINS'];
        $array['Percentual_Reducao_ICMS']       = @$mProduto['Percentual_Reducao_ICMS'];
        $array['Percentual_Reducao_ICMS_IE_ST'] = @$mProduto['Percentual_Reducao_ICMS_IE_ST'];
        $array['Percentual_Reducao_ICMS_INT_ST']= @$mProduto['Percentual_Reducao_ICMS_INT_ST'];
        $array['Percentual_Reducao_ICMS_ST']    = @$mD006['D006_ST_ICMS_Interno_Reducao'];
        $array['ST_VA']                         = @$mProduto['D149_Valor_VA'];
        $array['Flag_ST']                       = @$Flag_ST;
        $array['Origem_Mercadoria']             = @$mProduto['D049_Origem_Mercadoria'];
        $array['Flag_Situacao_Tributaria']      = @$Flag_Situacao_Tributaria;
        $array['ICMS_Inter_Estadual']           = @$mCab['D053_ST_ICMS_Inter_Estadual_Compra'];
        $array['ST_ICMS_Interno']               = @$mCab['D053_ST_ICMS_Interno_Compra'];
        $array['ST_ICMS_Inter_Estadual_MVA']    = @$mD006['D006_ST_ICMS_Inter_Estadual_MVA'];
        $array['ST_ICMS_Interno_MVA']           = @$mD006['D006_ST_ICMS_Interno_MVA'];
        $array['Flag_Isento_PIS']               = @$mD006['D006_Flag_Isento_PIS'];
        //$array['Aliquota_PIS']                  = @$mCab['C004_Percentual_PIS'];
        $array['Situacao_Tributaria_PIS']       = @$mD006['D006_Situacao_Tributaria_PIS'];
        $array['Flag_Isento_COFINS']            = @$mD006['D006_Flag_Isento_COFINS'];
        //$array['Aliquota_COFINS']               = @$mCab['C004_Percentual_COFINS'];
        $array['Situacao_Tributaria_COFINS']    = @$mD006['D006_Situacao_Tributaria_COFINS'];
        $array['Situacao_Tributaria_IPI']       = @$mD006['D088_Codigo_ST'];
        $array['D059_Id']                       = @$mD006['D059_Id'];
        $array['D082_Id']                       = @$mProduto['D049_D082_Id'];
        $array['Valor_Custo_Unitario']          = @$mProduto['D009_Valor_Custo_Unitario'];
        $array['D001_Peso_Unitario_Kg']         = @$mProduto['D001_Peso_Unitario_Kg'];
        $array['D001_Peso_Unitario_Bruto']      = @$mProduto['D001_Peso_Unitario_Bruto'];
        $array['Percentual_Reducao_IPI']        = @$mProduto['Percentual_Reducao_IPI'];
        $array['Aliquota_ICMS_Credito']         = @$Aliquota_ICMS_Credito;
        $array['Percentual_Trib_Transparencia'] = @$Percentual_Trib_Transparencia;
        $array['Flag_Pre_Cadastro']             = @$mProduto['D001_Flag_Pre_Cadastro'];
        $array['Percentual_ST']                 = @$mProduto['Percentual_ST'];
        $array['Valor_Item_Tabela_Fornecedor']  = @$mProduto['D069_Valor_Preco_Unitario'];
        $array['Aliquota_DIFAL']                = @$Aliquota_DIFAL;
        $array['Percentual_FCP']                = @$percentualFCP;
        
        if($mCab['D024_Flag_Pessoa_Fisica_Juridica'] == 'I'){
            $array['Aliquota_IPI']                  = 0;
            $array['Aliquota_ICMS']                 = 0;
            $array['Percentual_Reducao_ICMS']       = 0;
            $array['Percentual_Reducao_ICMS_ST']    = 0;
            $array['ST_VA']                         = 0;
            $array['Flag_ST']                       = @$Flag_ST;
            $array['Flag_Situacao_Tributaria']      = @$Flag_Situacao_Tributaria;
            $array['ICMS_Inter_Estadual']           = @$mCab['D053_ST_ICMS_Inter_Estadual_Compra'];
            $array['ST_ICMS_Interno']               = @$mCab['D053_ST_ICMS_Interno_Compra'];
            $array['ST_ICMS_Inter_Estadual_MVA']    = @$mD006['D006_ST_ICMS_Inter_Estadual_MVA'];
            $array['ST_ICMS_Interno_MVA']           = @$mD006['D006_ST_ICMS_Interno_MVA'];
            $array['Flag_Isento_PIS']               = @$mD006['D006_Flag_Isento_PIS'];
            $array['Aliquota_PIS']                  = @$mCab['C004_Percentual_PIS'];
            $array['Situacao_Tributaria_PIS']       = @$mD006['D006_Situacao_Tributaria_PIS'];
            $array['Flag_Isento_COFINS']            = @$mD006['D006_Flag_Isento_COFINS'];
            $array['Aliquota_COFINS']               = @$mCab['C004_Percentual_COFINS'];
            $array['Situacao_Tributaria_COFINS']    = @$mD006['D006_Situacao_Tributaria_COFINS'];
            $array['Situacao_Tributaria_IPI']       = @$mD006['D088_Codigo_ST'];
            $array['Valor_Custo_Unitario']          = @$mProduto['D009_Valor_Custo_Unitario'];
            $array['D001_Peso_Unitario_Kg']         = @$mProduto['D001_Peso_Unitario_Kg'];
            $array['D001_Peso_Unitario_Bruto']      = @$mProduto['D001_Peso_Unitario_Bruto'];
            $array['Percentual_Reducao_IPI']        = @$mProduto['Percentual_Reducao_IPI'];
            $array['Aliquota_ICMS_Credito']         = @$Aliquota_ICMS_Credito;
            $array['Percentual_Trib_Transparencia'] = @$Percentual_Trib_Transparencia;
            $array['Flag_Pre_Cadastro']             = @$mProduto['D001_Flag_Pre_Cadastro'];
        }

        /* [REFORMA TRIBUTÁRIA]
         * Segue a mesma prioridade usada na venda: configuração da CFOP e,
         * na ausência dela, a classificação fiscal da NCM do produto.
         */
        require_once('bibliotecas/classes/ReformaTributaria.php');
        $reformaTributaria = new ReformaTributaria();

        if (!empty($mD006['D006_Situacao_Tributaria_CBS_IBS']) && !empty($mD006['D006_Classificacao_Tributaria_CBS_IBS'])) {
            $situacaoCBSIBS = $mD006['D006_Situacao_Tributaria_CBS_IBS'];
            $classificacaoCBSIBS = $mD006['D006_Classificacao_Tributaria_CBS_IBS'];
            $situacaoCBSIBSRegular = $mD006['D006_Situacao_Tributaria_CBS_IBS_Regular'];
            $classificacaoCBSIBSRegular = $mD006['D006_Classificacao_Tributaria_CBS_IBS_Regular'];
        } else {
            $situacaoCBSIBS = $mProduto['D005_Situacao_Tributaria_CBS_IBS'];
            $classificacaoCBSIBS = $mProduto['D005_Classificacao_Tributaria_CBS_IBS'];
            $situacaoCBSIBSRegular = $mProduto['D005_Situacao_Tributaria_CBS_IBS_Regular'];
            $classificacaoCBSIBSRegular = $mProduto['D005_Classificacao_Tributaria_CBS_IBS_Regular'];
        }

        $aliquotasReforma = $reformaTributaria->getAliquotas($classificacaoCBSIBS ?? '');
        $aliquotasReformaRegular = $reformaTributaria->getAliquotas($classificacaoCBSIBSRegular ?? '');

        $array['Situacao_Tributaria_CBS_IBS'] = $situacaoCBSIBS;
        $array['Classificacao_Tributaria_CBS_IBS'] = $classificacaoCBSIBS;
        $array['Aliquota_CBS'] = $aliquotasReforma['aliquotaCbs'] ?? 0;
        $array['Aliquota_IBS_UF'] = $aliquotasReforma['aliquotaIbsUf'] ?? 0;
        $array['Aliquota_IBS_Municipal'] = $aliquotasReforma['aliquotaIbsMunicipal'] ?? 0;
        $array['Percentual_Reducao_CBS'] = $aliquotasReforma['percentualReducaoCbs'] ?? 0;
        $array['Percentual_Reducao_IBS_UF'] = $aliquotasReforma['percentualReducaoIbsUf'] ?? 0;
        $array['Percentual_Reducao_IBS_Municipal'] = $aliquotasReforma['percentualReducaoIbsMunicipal'] ?? 0;
        $array['Percentual_Diferimento_CBS'] = $aliquotasReforma['percentualDiferimentoCbs'] ?? 0;
        $array['Percentual_Diferimento_IBS_UF'] = $aliquotasReforma['percentualDiferimentoIbsUf'] ?? 0;
        $array['Percentual_Diferimento_IBS_Municipal'] = $aliquotasReforma['percentualDiferimentoIbsMunicipal'] ?? 0;
        $array['Situacao_Tributaria_CBS_IBS_Regular'] = $situacaoCBSIBSRegular;
        $array['Classificacao_Tributaria_CBS_IBS_Regular'] = $classificacaoCBSIBSRegular;
        $array['Aliquota_CBS_Regular'] = $aliquotasReformaRegular['aliquotaCbs'] ?? 0;
        $array['Aliquota_IBS_UF_Regular'] = $aliquotasReformaRegular['aliquotaIbsUf'] ?? 0;
        $array['Aliquota_IBS_Municipal_Regular'] = $aliquotasReformaRegular['aliquotaIbsMunicipal'] ?? 0;
        $array['Percentual_Reducao_CBS_Regular'] = $aliquotasReformaRegular['percentualReducaoCbs'] ?? 0;
        $array['Percentual_Reducao_IBS_UF_Regular'] = $aliquotasReformaRegular['percentualReducaoIbsUf'] ?? 0;
        $array['Percentual_Reducao_IBS_Municipal_Regular'] = $aliquotasReformaRegular['percentualReducaoIbsMunicipal'] ?? 0;

        return $array;
    }

    /**
     * cotarProduto
     *
     * @param Form $form
     */
    public function cotarProduto($form)
    {
        global $g;
        $T219_Id                       = $form->campoValorEnviado('T189_T219_Id');
        $D009_Id                       = $form->campoValorEnviado('T189_D009_Id');
        $T189_Observacao               = $form->campoValorEnviado('T189_Observacao');
        $C007_Id                       = $g['usuarioAtual'];
        //Busca dados da T219 para inserir na Tabela de Requisição
        $sql                           = "SELECT * FROM T219 
                LEFT JOIN T218 ON T218_Id = T219_T218_Id
                LEFT JOIN D024 ON D024_Id = T218_D024_Id
                WHERE T219_Id = {$T219_Id}";
        $row                           = mysql_fetch_assoc(mysql_query($sql));
        //Insere dados na Tabela de Requisição
        $row['T219_Descricao_Produto'] = mysql_real_escape_string($row['T219_Descricao_Produto']);
        $T189_Observacao               = mysql_real_escape_string($T189_Observacao);
        $row['D024_Nome_Empresa']      = mysql_real_escape_string($row['D024_Nome_Empresa']);
        $row['T219_Descricao_Produto'] = mysql_real_escape_string($row['T219_Descricao_Produto']);
        $T189_Observacao               = mysql_real_escape_string($T189_Observacao);
        $row['D024_Nome_Empresa']      = mysql_real_escape_string($row['D024_Nome_Empresa']);
        $sql                           = <<<EOT
            INSERT INTO 
            T189 
                (T189_T219_Id, T189_T225_Id, T189_D009_Id, T189_C007_Id, T189_Data_Criado, T189_Descricao_Produto, T189_Quantidade, T189_Observacao, T189_Detalhes, T189_Flag_Tipo) 
            VALUES 
                ('{$T219_Id}', '', '{$D009_Id}', '{$C007_Id}', CURRENT_TIMESTAMP(), '{$row['T219_Descricao_Produto']}', '{$row['T219_Quantidade']}', '{$T189_Observacao}', 'Orcamento Nº: {$row['T218_Id']} - {$row['D024_Nome_Empresa']}', 'C');
EOT;
        mysql_query($sql);
        return true;
    }
    
    /**
     * est015atualizarCFOP
     *
     * @param int $T218_Id
     */
    public function est015atualizarCFOP($T218_Id, $opcoes = null)
    {
        global $g;
        if (is_array($opcoes) && isset($opcoes['cfop']) && $opcoes['cfop'] === false) return true;
        $sqlT218 = mysql_query("SELECT * FROM T218 LEFT JOIN T219 ON T218_Id = T219_T218_Id WHERE T218_Id = '{$T218_Id}'");
        while ($T218 = mysql_fetch_array($sqlT218)) {
            log("regra item: retornaCFOP({$T218['T218_D024_Id']}, {$T218['T219_D009_Id']}, {$g['empresaAtual']})");
            $D006_Id = $this->retornaCFOP($T218['T218_D024_Id'], $T218['T219_D009_Id'], $g['empresaAtual'], $T218['T219_Flag_Origem_Produto'], false,false, $T218['T219_Flag_Destino_Produto']);
            log("CFOP retornada= ".$D006_Id);
            $demonstrativoCFOP = $this->retornaCFOP($T218['T218_D024_Id'], $T218['T219_D009_Id'], $g['empresaAtual'], $T218['T219_Flag_Origem_Produto'], true, false, $T218['T219_Flag_Destino_Produto']);
            $demonstrativoCFOP = mysql_real_escape_string($demonstrativoCFOP);
            mysql_query("UPDATE T219 
                LEFT JOIN D009 ON D009_Id=T219_D009_Id
                LEFT JOIN D049 ON D049_Id=D009_D049_Id
                LEFT JOIN D001 ON D001_Id=D049_D001_Id
                SET T219_D006_Id = '{$D006_Id}', T219_D006_Id_Info = '{$demonstrativoCFOP}', T219_Flag_Pre_Cadastro=D001_Flag_Pre_Cadastro WHERE T219_Id = '{$T218['T219_Id']}'");
            $erro = mysql_error();
            if(!empty($erro)){
                mysql_query("ROLLBACK");
                return "Erro1: " . $erro;
            }

        }

        if ($D006_Id > 0) {
            mysql_query("UPDATE T218 SET T218_D006_Id='{$D006_Id}' WHERE T218_Id='{$T218_Id}'");
            $erro = mysql_error();
            if(!empty($erro)){
                mysql_query("ROLLBACK");
                return "Erro2: " . $erro;
            }

        }
        return true;
        // $retorno = $this->atualizarAliquotas($T218_Id);
        // return $retorno;
    }
    
    /**
     * atualizarAliquotasProduto
     *
     * @param int $T218_Id
     * @param int $T219_Id
     */
    public function atualizarAliquotasProduto($T218_Id, $T219_Id)
    {
        global $g;
        $sqlT219 = mysql_query("SELECT * FROM T219 
                                LEFT JOIN T218 ON T218_Id = T219_T218_Id
                                LEFT JOIN D009 ON D009_Id = T219_D009_Id
                                LEFT JOIN D049 ON D049_Id = D009_D049_Id
                                LEFT JOIN D001 ON D001_Id = D049_D001_Id
                                WHERE T219_Id = '{$T219_Id}'");
        $T219    = mysql_fetch_array($sqlT219);

        $Aliquotas = $this->retornaAliquotas($T219['T219_D009_Id'], $T218_Id, false, false, $T219_Id);
        $sql = "UPDATE T219 SET T219_D005_Id='{$Aliquotas['D005_Id']}',";
        $sql .= "T219_Classificacao_Fiscal='{$Aliquotas['D005_Classificacao_Fiscal']}',";
        if($T219['T218_Flag_Calcular_IPI'] == 'S'){
            $sql .= "T219_Aliquota_IPI='{$Aliquotas['Aliquota_IPI']}',";
            $sql .= "T219_Percentual_Reducao_IPI='{$Aliquotas['Percentual_Reducao_IPI']}',";
        }
        if($T219['T218_Flag_Calcular_ICMS'] == 'S'){
            $sql .= "T219_Aliquota_ICMS='{$Aliquotas['Aliquota_ICMS']}',";
            $sql .= "T219_Percentual_Reducao_ICMS='{$Aliquotas['Percentual_Reducao_ICMS']}',";
        }
        if($T219['T218_Flag_Calcular_ST'] == 'S'){
            $sql .= "T219_Percentual_Reducao_ICMS_ST='{$Aliquotas['Percentual_Reducao_ICMS_ST']}',";
            $sql .= "T219_Flag_ST='{$Aliquotas['Flag_ST']}',";
            $sql .= "T219_ST_VA='{$Aliquotas['ST_VA']}',";
            $sql .= "T219_ICMS_Inter_Estadual_MVA='{$Aliquotas['ST_ICMS_Inter_Estadual_MVA']}',";
            $sql .= "T219_ST_ICMS_Interno_MVA='{$Aliquotas['ST_ICMS_Interno_MVA']}',";
        }

        $sql .= "T219_ICMS_Inter_Estadual='{$Aliquotas['ICMS_Inter_Estadual']}',";
        $sql .= "T219_ST_ICMS_Interno='{$Aliquotas['ST_ICMS_Interno']}',";
        $sql .= "T219_Flag_Origem_Produto='{$Aliquotas['Origem_Mercadoria']}',";
        $sql .= "T219_D059_Id='{$Aliquotas['D059_Id']}',";
        $sql .= "T219_Flag_Situacao_Tributaria='{$Aliquotas['Flag_Situacao_Tributaria']}',";
        $sql .= "T219_Flag_Isento_PIS='{$Aliquotas['Flag_Isento_PIS']}',";
        $sql .= "T219_Aliquota_PIS='{$Aliquotas['Aliquota_PIS']}',";
        $sql .= "T219_Situacao_Tributaria_PIS='{$Aliquotas['Situacao_Tributaria_PIS']}',";
        $sql .= "T219_Aliquota_COFINS='{$Aliquotas['Aliquota_COFINS']}',";
        $sql .= "T219_Situacao_Tributaria_COFINS='{$Aliquotas['Situacao_Tributaria_COFINS']}',";
        $sql .= "T219_Flag_Isento_COFINS='{$Aliquotas['Flag_Isento_COFINS']}',";
        $sql .= "T219_Situacao_Tributaria_IPI='{$Aliquotas['Situacao_Tributaria_IPI']}',";
        $sql .= "T219_Peso_Unitario='{$T219['D001_Peso_Unitario_Kg']}'";
        $sql .= "WHERE T219_Id='{$T219_Id}'";
        mysql_query($sql);
        $erro = mysql_error();
        if (!empty($erro)) {
            return "Erro atualizando item do orcamento ID {$T219_Id}: $erro";
        }
        mysql_query("UPDATE T219A SET
                        T219A_Situacao_Tributaria_CBS_IBS = '{$Aliquotas['Situacao_Tributaria_CBS_IBS']}',
                        T219A_Classificacao_Tributaria_CBS_IBS = '{$Aliquotas['Classificacao_Tributaria_CBS_IBS']}',
                        T219A_Aliquota_CBS = '{$Aliquotas['Aliquota_CBS']}',
                        T219A_Aliquota_IBS_UF = '{$Aliquotas['Aliquota_IBS_UF']}',
                        T219A_Aliquota_IBS_Municipal = '{$Aliquotas['Aliquota_IBS_Municipal']}',
                        T219A_Percentual_Reducao_CBS = '{$Aliquotas['Percentual_Reducao_CBS']}',
                        T219A_Percentual_Reducao_IBS_UF = '{$Aliquotas['Percentual_Reducao_IBS_UF']}',
                        T219A_Percentual_Reducao_IBS_Municipal = '{$Aliquotas['Percentual_Reducao_IBS_Municipal']}',
                        T219A_Percentual_Diferimento_CBS = '{$Aliquotas['Percentual_Diferimento_CBS']}',
                        T219A_Percentual_Diferimento_IBS_UF = '{$Aliquotas['Percentual_Diferimento_IBS_UF']}',
                        T219A_Percentual_Diferimento_IBS_Municipal = '{$Aliquotas['Percentual_Diferimento_IBS_Municipal']}',
                        T219A_Situacao_Tributaria_CBS_IBS_Regular = '{$Aliquotas['Situacao_Tributaria_CBS_IBS_Regular']}',
                        T219A_Classificacao_Tributaria_CBS_IBS_Regular = '{$Aliquotas['Classificacao_Tributaria_CBS_IBS_Regular']}',
                        T219A_Aliquota_CBS_Regular = '{$Aliquotas['Aliquota_CBS_Regular']}',
                        T219A_Aliquota_IBS_UF_Regular = '{$Aliquotas['Aliquota_IBS_UF_Regular']}',
                        T219A_Aliquota_IBS_Municipal_Regular = '{$Aliquotas['Aliquota_IBS_Municipal_Regular']}',
                        T219A_Percentual_Reducao_CBS_Regular = '{$Aliquotas['Percentual_Reducao_CBS_Regular']}',
                        T219A_Percentual_Reducao_IBS_UF_Regular = '{$Aliquotas['Percentual_Reducao_IBS_UF_Regular']}',
                        T219A_Percentual_Reducao_IBS_Municipal_Regular = '{$Aliquotas['Percentual_Reducao_IBS_Municipal_Regular']}'
                     WHERE T219A_T219_Id='{$T219_Id}'");
        if ($erro = mysql_error()) {
            return "Erro atualizando Reforma Tributária do item da cotação {$T219_Id}: {$erro}";
        }
        return true;
    }
    
    /**
     * calcularComissao
     *
     * @param string $valorTotalPreco
     * @param string $percentualDesconto
     */
    public function calcularComissao($valorTotalPreco, $percentualDesconto)
    {
        return 'Este metódo ainda não foi definido!';
    }
    
    /**
     * CRM001IncluirAgendaGeral
     *
     * @param Form $form
     * @param string $extra
     */
    public function CRM001IncluirAgendaGeral($form, $extra)
    {
        global $g;
        
        $extra = base64_decode(urldecode($extra));
        
        $D085_C007_Id_Inclusao      = $form->campoValorEnviado('D085_C007_Id_Inclusao');
        $D085_Data                  = gCorrigeDataInverte($form->campoValorEnviado('D085_Data'));
        $D085_Data_Retorno          = gCorrigeDataInverte($form->campoValorEnviado('D085_Data_Retorno'));
        $D085_Data_Previsao_Retorno = gCorrigeDataInverte($form->campoValorEnviado('D085_Data_Previsao_Retorno'));
        $D085_Mensagem              = $form->campoValorEnviado('D085_Mensagem');
        $D085_Flag_Manual           = $form->campoValorEnviado('D085_Flag_Manual');
        $D085_C007_Id               = $form->campoValorEnviado('D085_C007_Id');
        
        $extra = gInsertExtraWhere($extra, "D024_Flag_Enviar_Agenda='S'");
        
        $D024 = <<<EOT
        SELECT * FROM D024 {$extra}
EOT;
        $D024 = mysql_query($D024);
        if (mysql_error($D024)) {
            return "Erro de SQL:  1: " . mysql_error();
        }
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        while ($mD024 = mysql_fetch_array($D024)) {
            
            mysql_query("
                    INSERT INTO D085 (
                            D085_D024_Id,
                            D085_C007_Id,
                            D085_Data,
                            D085_Mensagem,
                            D085_Flag_Manual,
                            D085_Data_Previsao_Retorno,
                            D085_C007_Id_Inclusao
                        ) VALUES (
                            '{$mD024['D024_Id']}',
                            '{$D085_C007_Id}',
                            '{$D085_Data}',
                            '{$D085_Mensagem}',
                            '{$D085_Flag_Manual}',
                            '{$D085_Data_Previsao_Retorno}',
                            '{$D085_C007_Id_Inclusao}'
                        );
                ");
            $erro = mysql_error();
            if ($erro) {
                return "Erro: " . $erro;
                mysql_query("ROLLBACK");
            }
            
        }
        mysql_query("COMMIT");
        return true;
    }
    
    /**
     * insereCotacaoItensViaRequisicao
     */
    public function insereCotacaoItensViaRequisicao()
    {
        global $g, $T218_Id;
        
        $T218       = <<<EOT
        INSERT INTO T218 (
            T218_C007_Id_Vendedor_Interno,
            T218_C004_Id,
            T218_Data_Emissao
        ) VALUES (
            '{$g['usuarioAtual']}',
            '{$g['empresaAtual']}',
            CURDATE()
        );
EOT;
        $resultT218 = mysql_query($T218);
        if (!$resultT218) {
            $erro = mysql_error();
            return "Erro: " . $erro;
        }
        $T218_Id = $g['mysqlLastId'];
        
        $this->insereItemEstoqueRequisicao($T218_Id);
        
        return true;
    }
    
    /**
     * insereItemEstoqueRequisicao
     *
     * @param int $T218_Id
     */
    public function insereItemEstoqueRequisicao($T218_Id)
    {
        global $g;
        $sql     = <<<EOT
                select 
                *,
                D001_Codigo_Produto,
                D001_Descricao_Produto,
                D009_Quantidade_Comprar_Calculada,
                D009_Id,
                T189_Quantidade,
                T189_Flag_Cotar,
                T189_Id,
                D041_Valor_Base,
                T189_Descricao_Produto,
                T189_D024_Id
                from T189
                left join D009 on D009_Id=T189_D009_Id
                left join D049 on D049_Id=D009_D049_Id
                left join D001 on D001_Id=D049_D001_Id
                left join D082 on D082_Id=D049_D082_Id
                left join D041 on D041_D049_Id=D049_Id
                left join D005 on D005_Id=D001_D005_Id
                left join D037 ON D037_Id=D001_D037_Id
                where T189_Flag_Cotar='S' AND 
                T189_C007_Id_Cotar = '{$g['usuarioAtual']}'
EOT;
        $retorno = mysql_query($sql);
        
        $count = 1;
        while ($row = mysql_fetch_array($retorno)) {
            
            $sqlInsert = <<<EOT
                insert into T219
                (T219_T218_Id,
                T219_Codigo_Produto,
                T219_Descricao_Produto,
                T219_Quantidade,
                T219_Valor_Custo_Unitario, 
                T219_D009_Id,
                T219_T189_Id,
                T219_C004_Id,
                T219_Item,
                T219_D037_Id,
                T219_T189_Id)
                values 
                ('{$T218_Id}',
                '{$row['D001_Codigo_Produto']}',
                '{$row['T189_Descricao_Produto']}',
                '{$row['T189_Quantidade']}',
                '{$row['D041_Valor_Base']}',
                '{$row['D009_Id']}',
                '{$row['T189_Id']}',
                '{$g['empresaAtual']}',
                '{$count}',
                '{$row['D001_D037_Id']}',
                '{$row['T189_Id']}')
EOT;
            mysql_query($sqlInsert);
            mysql_query("UPDATE T189 SET T189_Flag_Cotar = 'N' WHERE T189_C007_Id_Cotar='{$g['usuarioAtual']}';");
            
            $cSql = mysql_query("call T219_Gravar_Totalizacao('{$g['mysqlLastId']}');");
            $cSql = mysql_query("call T218_Gravar_Totalizacao_3('{$T010_Id}');");
            $count++;
        }
    }
    
    /**
     * est015NaoLiberarOrcamento
     *
     * @param Form $form
     */
    public function est015NaoLiberarOrcamento($form)
    {
        global $g;
        $T218_Id                                     = $form->campoValorEnviado('T218_Id');
        $T218_Liberou_Alcada_Observacao_Nao_Liberado = $form->campoValorEnviado('T218_Liberou_Alcada_Observacao_Nao_Liberado');
        
        if (empty($T218_Id)) {
            return "T218_Id não existe";
        }
        
        $T218_Liberou_Alcada_Observacao_Nao_Liberado = trim($T218_Liberou_Alcada_Observacao_Nao_Liberado);
        $T218_Liberou_Alcada_Observacao_Nao_Liberado = addslashes($T218_Liberou_Alcada_Observacao_Nao_Liberado);
        
        if (empty($T218_Liberou_Alcada_Observacao_Nao_Liberado)) {
            return "Preencha o campo Motivo.";
        }
        $cSql  = mysql_query("update T218 set T218_Liberou_Alcada_C007_Id='{$g['usuarioAtual']}',
                                      T218_Liberou_Alcada_Data=now(),
                                      T218_Flag_PDF_Impresso='N',
                                      T218_Flag_Status_Orcamento='N',
                                      T218_Liberou_Alcada_IPV=0,
                                      T218_Liberou_Alcada_Prazo_Medio=0,
                                      T218_Liberou_Alcada_Desconto=0,
                                      T218_Liberou_Alcada_Observacao_Nao_Liberado='{$T218_Liberou_Alcada_Observacao_Nao_Liberado}',
                                      T218_Flag_Status_Liberacao='3',
                                      T218_Flag_Visualizou_Liberacao='N'
                                where T218_Id='{$T218_Id}'");
        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "Ocorreu um erro ao tentar alterar o registro/p/pErro:$cErro/p/pClausula:$cClausula";
        } else {
            return true;
        }
    }
    
    /**
     * est015AprovarOrcamento
     *
     * @param int $T218_Id
     * @param boolean $viaTablet
     */
    public function est015AprovarOrcamento($T218_Id, $viaTablet = false)
    {
        global $g;
        $retorno         = mysql_query("SELECT T218_Valor_Total_Produtos,T218_Valor_Total_Confirmado,T218_Valor_Total,T218_Valor_Desconto,T218_Valor_Desconto_Manual FROM T218 WHERE T218_Id='{$T218_Id}'");
        $T218            = mysql_fetch_array($retorno);
        $totalConfirmado = $T218['T218_Valor_Total_Produtos_Confirmado'];
        $totalOrcamento  = $T218['T218_Valor_Total_Produtos'];
        $total           = "";
        if ($viaTablet == false) {
            $desconto = $this->calculaPercentualDescontoOrcamento($T218_Id);
            if ($totalConfirmado <= 0) {
                $total = $totalOrcamento;
            } else {
                $total = $totalConfirmado;
            }
        } else {
            //$desconto = (($T218['T218_Valor_Desconto']+$T218['T218_Valor_Desconto_Manual'])*100)/($T218['T218_Valor_Desconto_Manual']+$T218['T218_Valor_Desconto']+$T218['T218_Valor_Total']);
            $desconto = ($T218['T218_Valor_Desconto_Manual'] * 100) / ($T218['T218_Valor_Desconto_Manual'] + $T218['T218_Valor_Total']);
            $total    = $T218['T218_Valor_Total'];
        }
        
        $retorno = mysql_query("SELECT AVG(T089_Prazos_Dias) FROM T089 WHERE T089_T218_Id='{$T218_Id}'");
        $T089    = mysql_fetch_array($retorno);
        
        $total = $T218['T218_Valor_Total'];
        
        $cSql  = mysql_query("update T218 set T218_Liberou_Alcada_C007_Id='{$g['usuarioAtual']}',
                                      T218_Liberou_Alcada_Data=now(),
                                      T218_Flag_PDF_Impresso='N',
                                      T218_Liberou_Alcada_IPV=T218_IPV,
                                      T218_Flag_Status_Orcamento='A',
                                      T218_Liberou_Alcada_Valor_Total='{$total}',
                                      T218_Liberou_Alcada_Cortesia_Valor_Total='{$total}',
                                      T218_Flag_Liberou_Alcada_Cortesia='S',
                                      T218_Flag_Status_Liberacao='2',
                                      T218_Liberou_Alcada_Prazo_Medio='{$T089['AVG(T089_Prazos_Dias)']}',
                                      T218_Motivo_Pedido_Liberacao='',
                                      T218_Liberou_Alcada_Desconto='{$desconto}',
                                      T218_Flag_Visualizou_Liberacao='N',
                                      T218_Liberou_Alcada_Observacao_Nao_Liberado=''
                                where T218_Id='{$T218_Id}'");
        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "Ocorreu um erro ao tentar alterar o registro/p/pErro:$cErro/p/pClausula:$cClausula";
        }
        
        $sqlT219 = mysql_query("UPDATE T219 LEFT JOIN D009 on D009_Id=T219_D009_Id SET T219_Liberou_Alcada_Saldo_Futuro=D009_Quantidade_Estoque_Liquido-if(T219_Quantidade_Confirmada>0,T219_Quantidade_Confirmada,T219_Quantidade) WHERE T219_T218_Id='{$T218_Id}'");
        return true;
    }
    
    /**
     * montarEmailLink
     *
     * @param int $T218_Id
     */
    public function montarEmailLink($T218_Id)
    {
        global $g, $confUsuario;
        
        $T218_Id  = mysql_real_escape_string($T218_Id);
        $sql      = "SELECT * FROM T218 LEFT JOIN D024 ON D024_Id=T218_D024_Id LEFT JOIN D013 ON D013_Id = T218_D013_Id where T218_Id='{$T218_Id}'";
        $Consulta = mysql_query($sql);
        $Consulta = mysql_fetch_array($Consulta);
        //echo "CPF: ".$Consulta['D024_Cpf']."<br />CNPJ: ".$Consulta['D024_Cnpj'];
        
        //retira os caracteres especiais para verificar se o campo está preenchido
        $cpfVerificar  = preg_replace("/[.-]/i", "", $Consulta['D024_Cpf']);
        $cnpjVerificar = preg_replace("/[.\/-]/i", "", $Consulta['D024_Cnpj']);
        
        $cpfCnpj = '';
        if (!empty($cpfVerificar)) {
            $cpfCnpj = "CPF: " . $Consulta['D024_Cpf'];
        } elseif (!empty($cnpjVerificar)) {
            $cpfCnpj = "CNPJ: " . $Consulta['D024_Cnpj'];
        }
        // echo "<br /><br />".$cpfCnpj;
        // die();
        
        $D024_Id   = $Consulta['T218_D024_Id'];
        $parametro = base64_encode($g['empresaAtual'] . '|' . $T218_Id . '|' . $D024_Id);
        
        $sql          = "select C004_Nome_Empresa,C004_Site,C004_Logo_Marca,C004_Nome_Abreviado from C004 where C004_Id='{$g['empresaAtual']}'";
        $empresaAtual = mysql_query($sql);
        $empresaAtual = mysql_fetch_array($empresaAtual);
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
        }
        
        $array['Para']    = (!empty($Consulta['D013_E_Mail'])) ? $Consulta['D013_E_Mail'] : $Consulta['D024_E_Mail'];
        $array['Assunto'] = "Cotação de Compra Nº[{$Consulta['T218_Id']}]";
        $array['Corpo']   = '<div style="border:2px solid #E0EEEE; padding-top:10px;padding-bottom:10px;">';
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
        } else {
            $caminhoImagem = "";
        }
        //$array['Corpo'] .= '<img src=' . $caminhoImagem . ' style="margin-left:25px"><br /><br />';
        /*
         * <span style="font-family:arial,verdana;font-size:16px;font-weight:bold">
         Olá {$Consulta['D024_Nome_Empresa']},<br /><br />
         </span>
         * <span style="font-family:arial,verdana;font-size:14px;">{$empresaAtual['C004_Nome_Abreviado']}</span><br /><br />
         * 
         * <div style="font-size:10px; line-height:16px;">
         E-mail enviado por <br /> {$empresaAtual['C004_Nome_Empresa']} <br /> <a href="{$empresaAtual['C004_Site']}">{$empresaAtual['C004_Site']}<a />
         </div>
         */
        $array['Corpo'] .= <<<EOT
            <div style="background-color:white; width:500px; color:black; padding:8px;">
                <div style="clear:both; padding-top:16px;"> 
                    <span style="font-family:arial,verdana;font-size:14px;">
                        Segue link para acessar nossa cotação de compra número {$Consulta['T218_Id']}<br /><br />
                        Empresa: {$Consulta['D024_Nome_Empresa']}<br />
                        {$cpfCnpj}<br /><br />
                        <a href="{$confUsuario['urlRaiz']}est/est015/outro/est015OutrosCotacaoCompraOnline/?ajax=true&p={$parametro}&oneWindow=true">Link Cotação N. {$Consulta['T218_Id']}</a><br /><br />
                    </span>
                </div>
            </div>
            
EOT;
        //Link para imagem relativa a empresa logada
        //<img style="float:left; margin-right:10px;" src="{$caminhoImagem}" /> E-mail enviado por <br /> {$empresaAtual['C004_Nome_Empresa']} <br /> <a href="{$empresaAtual['C004_Site']}">{$empresaAtual['C004_Site']}<a />
        return $array;
    }
    
    /**
     * insereItemEstoque
     *
     * @param int $T218_Id
     * @param string $extra
     */
    public function insereItemEstoque($T218_Id, $extra)
    {
        global $g;
        $sqlT218 = mysql_query("SELECT T218_D024_Id FROM T218 WHERE T218_Id = '{$T218_Id}'");
        $rowT218 = mysql_fetch_array($sqlT218);

        $extraOriginal = base64_decode($extra);
        $extra         = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "D009_Flag_Cotar='S' and D009_C004_Id='{$g['empresaAtual']}' AND D009_C007_Id_Comprar = '{$g['usuarioAtual']}'"));

        $sql     = <<<EOT
                select 
                D049_Origem_Mercadoria,
                D001_Codigo_Produto,
                D083_Codigo_Produto_Fornecedor,
                D001_Descricao_Produto,
                D009_Quantidade_Comprar_Calculada,
                D009_Quantidade_Comprar,
                D009_Id,
                D041_Valor_Base,
                D009_C004_Id
                from D009   
                LEFT JOIN T187 on T187_D009_Id=D009_Id 
                LEFT JOIN D049 on D049_Id=D009_D049_Id 
                LEFT JOIN D001 on D001_Id=D049_D001_Id 
                LEFT JOIN D082 on D082_Id=D049_D082_Id 
                LEFT JOIN D083 ON D001_Id=D083_D001_Id AND D083_D024_Id = '{$rowT218['T218_D024_Id']}'
                LEFT JOIN D037 on D037_Id=D001_D037_Id 
                LEFT JOIN C008 on C008_Id=D001_C008_Id 
                LEFT JOIN D003 on D003_Id=D001_D003_Id 
                LEFT JOIN D060 on D001_D060_Id=D060_Id 
                LEFT JOIN D002 on D001_D002_Id=D002_Id 
                LEFT JOIN D015 on D001_D015_Id=D015_Id 
                LEFT JOIN T001 on T001_Id=D009_T001_Id 
                left join D024 as ultimaCompra on ultimaCompra.D024_Id = T001_D024_Id
                left join D041 on D041_D049_Id=D049_Id
                left join D005 on D005_Id=D001_D005_Id
                left join C007 on C007_Id=D009_C007_Id_Comprar
                LEFT JOIN T241 ON T241_D001_Id=D001_Id
                LEFT JOIN D024 as fornecedor on fornecedor.D024_Id=T241_D024_Id
                LEFT JOIN D009A ON D009A_D009_Id = D009_Id
                WHERE {$extra}
                GROUP BY D009_Id
EOT;
        $retorno = mysql_query($sql);
        $erro = mysql_error();
        if(!empty($erro)){
            mysql_query("ROLLBACK");
            return "Erro1: " . $erro;
        }
        
        $i = 1;
        while ($row = mysql_fetch_array($retorno)) {
            
            $QuantidadeComprar = ($row['D009_Quantidade_Comprar'] > 0) ? $row['D009_Quantidade_Comprar'] : $row['D009_Quantidade_Comprar_Calculada'];

            /* Função trigger de personalização, caso você queira personalizar multiplicação de vários itens */
            $arrayTipoEmbalagens = $this->est015RetornaArrayItensCotacao($QuantidadeComprar, $row['D041_Valor_Base'], $row['D009_Id']);
            if(is_string($arrayTipoEmbalagens)) // Deu erro, a trigger deve dar um ROLLBACK
            {
                mysql_query("ROLLBACK");
                return $arrayTipoEmbalagens;
            }

            foreach($arrayTipoEmbalagens as $key => $value)
            {
                $sqlInsert = <<<EOT
                    insert into T219
                    (T219_Flag_Origem_Produto,
                    T219_T218_Id,
                    T219_C004_Id,
                    T219_Codigo_Produto,
                    T219_Codigo_Substituto,
                    T219_Descricao_Produto,
                    T219_Quantidade,
                    T219_Valor_Custo_Unitario, 
                    T219_D009_Id,
                    T219_Item,
                    T219_D037_Id,
                    T219_D005_Id)
                    values 
                    ('{$row['D049_Origem_Mercadoria']}',
                    '{$T218_Id}',
                    '{$row['D009_C004_Id']}',
                    '{$row['D001_Codigo_Produto']}',
                    '{$row['D083_Codigo_Produto_Fornecedor']}',
                    '{$row['D001_Descricao_Produto']}',
                    '{$value['qtd_itens']}',
                    '{$row['D041_Valor_Base']}',
                    '{$row['D009_Id']}',
                    '{$i}',
                    '{$row['D037_Id']}',
                    '{$row['D005_Id']}'
                    );
EOT;
                mysql_query($sqlInsert); 
                $erro = mysql_error();
                if(!empty($erro)){
                    mysql_query("ROLLBACK");
                    return "Erro1: " . $erro;
                }
                
                $retornoCriaT219A = $this->est015TriggerPersonalizacaoCriaT219A($g['mysqlLastId'], $value);
                if(is_string($retornoCriaT219A)) // Deu erro
                {   
                    mysql_query("ROLLBACK");
                    return $retornoCriaT219A;
                }

                $i++;
                mysql_query("UPDATE D009 SET D009_Flag_Cotar = 'N', D009_C007_Id_Comprar = '0' WHERE D009_Id = '{$row['D009_Id']}'");
                $erro = mysql_error();
                if(!empty($erro)){
                    mysql_query("ROLLBACK");
                    return "Erro3: " . $erro;
                } 
            }
        }
        //esses métodos agora serão executados no grid em etapas - Lígia Nakazato: 22/08/2018
        // $retorno = $this->est015atualizarCFOP($T218_Id);
        // $retorno = $this->atualizarAliquotas($T218_Id, $g['empresaAtual']);
        
        // $retorno = mysql_query("select T219_Id from T219 where T219_T218_Id='{$T218_Id}'");
        
        // while ($row = mysql_fetch_array($retorno)) {
        //     $cSql = mysql_query("call T219_Gravar_Totalizacao('{$row['T219_Id']}');");
        // }
        
        // $cSql = mysql_query("call T218_Gravar_Totalizacao_3('{$T218_Id}');");
        return true;
    }
    
    /**
     * duplicar_est015FormProdutos
     *
     * @param string $divId
     * @param string $divIdRoot
     * @param string $tabela
     * @param int $T218_Id_Antigo
     */
    public function duplicar_est015FormProdutos($divId, $divIdRoot, $tabela, $T218_Id_Antigo)
    {
        
        global $g;
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        // Selecionando a cotação
        $T218  = mysql_query("SELECT * FROM T218 where T218_Id='{$T218_Id_Antigo}'");
        $mT218 = mysql_fetch_assoc($T218);
        
        if (mysql_num_rows($T218) == 0) {
            $retorno = <<<EOT
                var \$buttons = {
                    "Fechar": function() { $(this).dialog("destroy").remove(); }
                };
                dialogConfirm("Erro", "Pedido não encontrado", \$buttons);
EOT;
            return $retorno;
        }

/*         $grupo  = mysql_query("SELECT T218_Grupo FROM T218 ORDER BY T218_Grupo DESC LIMIT 1");
        $mGrupo = mysql_fetch_array($grupo);
        $mGrupo['T218_Grupo'] += 1; */
        
        $campos  = "";
        $valores = "";
        foreach ($mT218 as $key => $value) {
            if ($key == 'T218_Id') {
                continue;
            }elseif($key == 'T218_Prazo_Entrega'){
                continue;
            } else {
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($value) . "',";
            }
        }
        $campos  = trim($campos, ', ');
        $valores = trim($valores, ',');
        $insT218 = "INSERT INTO T218 ({$campos}) VALUES ({$valores})";
        $resT218 = mysql_query($insT218);
        if (!$resT218) {
            mysql_query("ROLLBACK");
            return "Erro 1: " . mysql_error();
        }
        $T218_Id_Novo = $g['mysqlLastId'];
        
        $camposZerar = array(
            'T219_Observacao_Entrega',
            'T219_Prazo_Entrega_Dias',
            'T219_Aliquota_ICMS',
            'T219_Aliquota_ICMS_Credito',
            'T219_Aliquota_IPI',
            'T219_Aliquota_PIS',
            'T219_Aliquota_COFINS',
            'T219_Valor_Preco_Unitario',
            'T219_Valor_Preco_Unitario_Confirmado',
            'T219_Valor_Preco_Sem_Desconto_Unitario',
            'T219_Valor_Total_Preco_Sem_Desconto',
            'T219_Valor_Total_Preco',
            'T219_Valor_Total_Preco_Confirmado',
            'T219_Valor_Total_Preco_Sem_Desconto_Confirmado',
            'T219_Ultimo_Preco',
            'T219_Ultimo_Preco_Confirmado',
            'T219_Valor_Ultimo_Preco_Consulta',
            'T219_Valor_Ultimo_Preco_Confirmado',
            'T219_Obs_Preco',
            'T219_Valor_Preco_Original',
            'T219_Valor_Preco_Original_Bruto',
            'T219_Valor_Preco_Bruto',
            'T219_Valor_Preco_Venda',
            'T219_Percentual_Reducao_ICMS',
            'T219_Percentual_Base_Operacao_Propria',
            'T219_Percentual_Reducao_IPI',
            'T219_Percentual_Reducao_ICMS_ST',
            'T219_Percentual_Desconto',
            'T219_Percentual_Desconto_Bruto',
            'T219_Percentual_Comissao',
            'T219_Percentual_Trib_Transparencia',
            'T219_Percentual_Frete',
            'T219_Percentual_Financeiro',
            'T219_Valor_Custo_Unitario',
            'T219_Valor_Custo_Final_Unitario',
            'T219_Valor_Custo_Final_Unitario_Confirmado',
            'T219_Valor_Total_Custo',
            'T219_Valor_Total_Custo_Confirmado',
            'T219_Valor_Total_Custo_Final',
            'T219_Valor_Total_Custo_Final_Confirmado',
            'T219_Valor_Custo_Compra_Unitario',
            'T219_Valor_Custo_Tabela_Unitario',
            'T219_Valor_Custo_Unitario_Forn',
            'T219_ICMS_Inter_Estadual',
            'T219_ST_ICMS_Interno',
            'T219_ST_VA',
            'T219_ST_VA_Ajustada',
            'T219_Codigo_Substituto'
        );
        
        // Selecionando todos os itens da cotação
        $T219 = mysql_query("SELECT * FROM T219 WHERE T219_T218_Id='{$T218_Id_Antigo}'");
        while ($mT219 = mysql_fetch_assoc($T219)) {
            $campos  = "";
            $valores = "";
            foreach ($mT219 as $key => $value) {
                if ($key == 'T219_Id') {
                    continue;
                } else if ($key == "T219_T218_Id") {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($T218_Id_Novo) . "',";
                } else if (!in_array($key, $camposZerar)) {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($value) . "',";
                }
            }
            $campos  = trim($campos, ', ');
            $valores = trim($valores, ',');
            $insT219 = "INSERT INTO T219 ({$campos}) VALUES ({$valores})";
            $resT219 = mysql_query($insT219);
            if (!$resT219) {
                mysql_query("ROLLBACK");
                return "Erro 2: " . mysql_error();
            }
        }
        
        $T218_Id = $T218_Id_Novo;
        mysql_query("COMMIT");
        $novoId = uniqid();
        return "$('#{$divId}').closest('.ui-dialog-content').dialog('close'); abrirJanela(false, '{$divIdRoot}', '{$divId}' ,'{$novoId}', '', 'Editar Cotação de Compra', '/est/est015/content/est015ContentOrcamento/', '&acaoId=' + encodeURIComponent('{$T218_Id}') + '&tabela=T218&abrirCotacao=true&atualizarCodigoFornecedor=true', [990,605]);";
    }
    
    /**
     * EST015ProcessarCotacaoVencida
     */
    public function EST015ProcessarCotacaoVencida()
    {
        // Para utilizar este método basta personalizá-lo em cada empresa.
        return true;
    }
    
    /**
     * insereItemCotacaoEmergencia
     *
     * @param int $T218_Id
     */
    public function insereItemCotacaoEmergencia($T218_Id, $extra,$T003_Id)
    {
        log("entra no insereItemCotacaoEmergencia");
        global $g;
        $arrayRetorno = array();

        $sqlD024 = mysql_query("SELECT T218_D024_Id FROM T218 WHERE T218_Id = '{$T218_Id}'");
        $rowD024 = mysql_fetch_array($sqlD024);

        $extraOriginal = base64_decode($extra);
        $extra = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T004_Flag_Gerar_Cotacao='S' AND T004_C007_Id_Comprador = '{$g['usuarioAtual']}'"));
        if($T003_Id){
            $sql     = <<<EOT
                SELECT T004_Id,
                D001_Codigo_Produto,
                D001_Descricao_Produto,
                D009_Quantidade_Comprar_Calculada,
                IF(T004_Quantidade>D009_Quantidade_Estoque_Liquido, T004_Quantidade-if(D009_Quantidade_Estoque_Liquido>0,D009_Quantidade_Estoque_Liquido,0), T004_Quantidade) as T004_Quantidade,
                D009_Id,
                T004_Valor_Preco_Sem_Desconto_Unitario,
                D009_C004_Id,
                D005_Id,
                D037_Id,
                D049_Origem_Mercadoria,
                D083_Codigo_Produto_Fornecedor,
                T005_Id
                from T004 
                LEFT JOIN D009 on D009_Id=T004_D009_Id
                LEFT JOIN D049 on D049_Id=D009_D049_Id
                LEFT JOIN D001 on D001_Id=D049_D001_Id
                LEFT JOIN D082 on D082_Id=D049_D082_Id
                LEFT JOIN D037 on D037_Id=T004_D037_Id
                LEFT JOIN D005 on D005_Id=T004_D005_Id
                LEFT JOIN D006 on D006_Id=T004_D006_Id
                LEFT JOIN T006 on T006_T004_Id=T004_Id
                LEFT JOIN T005 ON T005_Id=T006_T005_Id
                LEFT JOIN T003 ON T003_Id=T004_T003_Id
                LEFT JOIN D024 as Cliente ON T003_D024_Id=Cliente.D024_Id
                LEFT JOIN T219 ON T004_Id=T219_T004_Id
                LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
                LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
                LEFT JOIN C007 AS marcou ON marcou.C007_Id = T004_C007_Id_Comprador
                LEFT JOIN T241 ON T241_D001_Id=D001_Id
                LEFT JOIN D024 as Fornecedor ON T241_D024_Id=Fornecedor.D024_Id
                LEFT JOIN D083 ON D001_Id=D083_D001_Id AND D083_D024_Id = '{$rowD024['T218_D024_Id']}'
                LEFT JOIN T004A ON T004_Id=T004A_T004_Id
                WHERE {$extra}
                AND T004_T003_Id={$T003_Id}
                AND IFNULL(T004_T219_Id,0)<=0 AND IFNULL(T219_Id,0)<=0
                GROUP BY T004_Id
EOT;
        }else{
            $sql     = <<<EOT
                SELECT T004_Id,
                D001_Codigo_Produto,
                D001_Descricao_Produto,
                D009_Quantidade_Comprar_Calculada,
                IF(T004_Quantidade>D009_Quantidade_Estoque_Liquido, T004_Quantidade-if(D009_Quantidade_Estoque_Liquido>0,D009_Quantidade_Estoque_Liquido,0), T004_Quantidade) as T004_Quantidade,
                D009_Id,
                T004_Valor_Preco_Sem_Desconto_Unitario,
                D009_C004_Id,
                D005_Id,
                D037_Id,
                D049_Origem_Mercadoria,
                D083_Codigo_Produto_Fornecedor,
                T005_Id
                from T004 
                LEFT JOIN D009 on D009_Id=T004_D009_Id
                LEFT JOIN D049 on D049_Id=D009_D049_Id
                LEFT JOIN D001 on D001_Id=D049_D001_Id
                LEFT JOIN D082 on D082_Id=D049_D082_Id
                LEFT JOIN D037 on D037_Id=T004_D037_Id
                LEFT JOIN D005 on D005_Id=T004_D005_Id
                LEFT JOIN D006 on D006_Id=T004_D006_Id
                LEFT JOIN T006 on T006_T004_Id=T004_Id
                LEFT JOIN T006A ON T006A_T006_Id=T006_Id
                LEFT JOIN T005 ON T005_Id=T006_T005_Id
                LEFT JOIN T003 ON T003_Id=T004_T003_Id
                LEFT JOIN D024 as Cliente ON T003_D024_Id=Cliente.D024_Id
                LEFT JOIN T219 ON T004_Id=T219_T004_Id
                LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
                LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
                LEFT JOIN C007 AS marcou ON marcou.C007_Id = T004_C007_Id_Comprador
                LEFT JOIN T241 ON T241_D001_Id=D001_Id
                LEFT JOIN D024 as Fornecedor ON T241_D024_Id=Fornecedor.D024_Id
                LEFT JOIN D083 ON D001_Id=D083_D001_Id AND D083_D024_Id = '{$rowD024['T218_D024_Id']}'
                LEFT JOIN T004A ON T004_Id=T004A_T004_Id
                WHERE {$extra}
                GROUP BY T004_Id
EOT;
        }
        log("SQL insereItemCotacaoEmergencia= ".$sql);
        
        $retorno = mysql_query($sql);

        $i = 1;
        while ($row = mysql_fetch_array($retorno)) {
            
            // Tratando caracteres de uma string
            $row['D001_Descricao_Produto'] = mysql_real_escape_string($row['D001_Descricao_Produto']);

            $T005_Id = $row['T005_Id'];
            $sqlInsert = "insert into T219 ";
            $sqlInsert .= "(T219_T218_Id, ";
            $sqlInsert .= "T219_C004_Id, ";
            $sqlInsert .= "T219_Codigo_Produto, ";
            $sqlInsert .= "T219_Codigo_Substituto, ";
            $sqlInsert .= "T219_Descricao_Produto, ";
            $sqlInsert .= "T219_Quantidade, ";
            $sqlInsert .= "T219_Valor_Custo_Unitario,  ";
            $sqlInsert .= "T219_D009_Id, ";
            $sqlInsert .= "T219_Item, ";
            $sqlInsert .= "T219_D037_Id, ";
            $sqlInsert .= "T219_D005_Id, ";
            $sqlInsert .= "T219_Flag_Origem_Produto, ";
            $sqlInsert .= "T219_T004_Id) ";
            $sqlInsert .= "values  ";
            $sqlInsert .= "('{$T218_Id}', ";
            $sqlInsert .= "'{$row['D009_C004_Id']}', ";
            $sqlInsert .= "'{$row['D001_Codigo_Produto']}', ";
            $sqlInsert .= "'{$row['D083_Codigo_Produto_Fornecedor']}', ";
            $sqlInsert .= "'{$row['D001_Descricao_Produto']}', ";
            $sqlInsert .= "'{$row['T004_Quantidade']}', ";
            $sqlInsert .= "'{$row['T004_Valor_Preco_Sem_Desconto_Unitario']}', ";
            $sqlInsert .= "'{$row['D009_Id']}', ";
            $sqlInsert .= "'{$i}', ";
            $sqlInsert .= "'{$row['D037_Id']}', ";
            $sqlInsert .= "'{$row['D005_Id']}', ";
            $sqlInsert .= "'{$row['D049_Origem_Mercadoria']}', ";
            $sqlInsert .= "'{$row['T004_Id']}' ";
            $sqlInsert .= "); ";
            mysql_query($sqlInsert);
            $erro = mysql_error();
            if(!empty($erro)){
                mysql_query("ROLLBACK");
                return "Erro: " . $erro . " <br/><br/>SQL = " . $sqlInsert;
            }
            $i++;
            
            if($T003_Id){
                $T219_Id = $g['mysqlLastId'];
                mysql_query("UPDATE T004 SET T004_T219_Id={$T219_Id} WHERE T004_Id={$row['T004_Id']}");
            }            
        }
        //esses métodos agora serão executados no grid em etapas - Lígia Nakazato 22/08/2018
        // $retorno = $this->est015atualizarCFOP($T218_Id);
        // $retorno = $this->atualizarAliquotas($T218_Id, $g['empresaAtual']);

        // $retorno = mysql_query("select T219_Id from T219 where T219_T218_Id='{$T218_Id}'");

        // while ($row = mysql_fetch_array($retorno)) {
        //     $cSql = mysql_query("call T219_Gravar_Totalizacao('{$row['T219_Id']}');");
        // }

        // $cSql = mysql_query("call T218_Gravar_Totalizacao_3('{$T218_Id}');");
        // $cSql = mysql_query("call T005_Gravar_Totalizacao_4('{$T005_Id}');");
        // return true;
        $arrayRetorno['T005_Id'] = $T005_Id;
        return $arrayRetorno;
    }

    /**
     * insereItemCotacaoPedidoEmergencia
     *
     * @param int $T218_Id
     */
    public function insereItemCotacaoPedidoEmergencia($T218_Id, $extra)
    {
        global $g;

        $sqlD024 = mysql_query("SELECT T218_D024_Id FROM T218 WHERE T218_Id = '{$T218_Id}'");
        $rowD024 = mysql_fetch_array($sqlD024);

        $extraOriginal = base64_decode($extra);
        $extra = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T006_Flag_Gerar_Cotacao='S' AND T006_C007_Id_Comprador = '{$g['usuarioAtual']}'"), true);
        
        $sql = <<<EOT
                SELECT T006_Id,
                D001_Codigo_Produto,
                D001_Descricao_Produto,
                D009_Quantidade_Comprar_Calculada,
                T006_Quantidade,
                T006_Quantidade-if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0)-T006_Quantidade_OC as Qtde_Comprar,
                D009_Id,
                D001_Id,
                T006_Valor_Preco_Sem_Desconto_Unitario,
                D009_C004_Id,
                D005_Id,
                D037_Id,
                D083_Codigo_Produto_Fornecedor,
                ifnull(D049_Flag_Tipo,'CP') AS D049_Flag_Tipo,
                T005_Id
                from T006
                LEFT JOIN T006A ON T006A_T006_Id=T006_Id
                LEFT JOIN T261 on T006_Id=T261_T006_Id
                LEFT JOIN D009 on D009_Id=T006_D009_Id
                LEFT JOIN D049 on D049_Id=D009_D049_Id
                LEFT JOIN D001 on D001_Id=D049_D001_Id
                LEFT JOIN D082 on D082_Id=D049_D082_Id
                LEFT JOIN D037 on D037_Id=T006_D037_Id
                LEFT JOIN D005 on D005_Id=T006_D005_Id
                LEFT JOIN D006 on D006_Id=T006_D006_Id
                LEFT JOIN T005 ON T005_Id=T006_T005_Id
                LEFT JOIN D024 as Cliente ON T005_D024_Id=Cliente.D024_Id
                LEFT JOIN T003 ON T003_Id=T005_T003_Id
                LEFT JOIN T004 ON T004_Id=T006_T004_Id
                LEFT JOIN T219 ON T006_Id=T219_T006_Id
                LEFT JOIN T218 ON T218_Id=T219_T218_Id
                LEFT JOIN T224 ON T218_Id=T224_T218_Id
                LEFT JOIN T225 ON T225_Id=T006_T225_Id
                LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
                LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
                LEFT JOIN C007 AS marcou ON marcou.C007_Id = T006_C007_Id_Comprador
                LEFT JOIN T241 ON T241_D001_Id=D001_Id
                LEFT JOIN D024 as Fornecedor ON T241_D024_Id=Fornecedor.D024_Id
                LEFT JOIN D083 ON D001_Id=D083_D001_Id AND D083_D024_Id = '{$rowD024['T218_D024_Id']}'
                WHERE {$extra}
                GROUP BY T006_Id
EOT;
        $retorno = mysql_query($sql);

        $i = 1;
        while ($row = mysql_fetch_array($retorno)) {
            $T005_Id = $row['T005_Id'];
            if(!in_array($row['D049_Flag_Tipo'], array('PD', 'IN'), true)){
                $row['D001_Codigo_Produto'] = mysql_real_escape_string($row['D001_Codigo_Produto']);
                $row['D083_Codigo_Produto_Fornecedor'] = mysql_real_escape_string($row['D083_Codigo_Produto_Fornecedor']);
                $row['D001_Descricao_Produto'] = mysql_real_escape_string($row['D001_Descricao_Produto']);

                $sqlInsert = "insert into T219 ";
                $sqlInsert .= "(T219_T218_Id, ";
                $sqlInsert .= "T219_C004_Id, ";
                $sqlInsert .= "T219_Codigo_Produto, ";
                $sqlInsert .= "T219_Codigo_Substituto, ";
                $sqlInsert .= "T219_Descricao_Produto, ";
                $sqlInsert .= "T219_Quantidade, ";
                $sqlInsert .= "T219_Valor_Custo_Unitario,  ";
                $sqlInsert .= "T219_D009_Id, ";
                $sqlInsert .= "T219_Item, ";
                $sqlInsert .= "T219_D037_Id, ";
                $sqlInsert .= "T219_D005_Id, ";
                $sqlInsert .= "T219_T006_Id) ";
                $sqlInsert .= "values  ";
                $sqlInsert .= "('{$T218_Id}', ";
                $sqlInsert .= "'{$row['D009_C004_Id']}', ";
                $sqlInsert .= "'{$row['D001_Codigo_Produto']}', ";
                $sqlInsert .= "'{$row['D083_Codigo_Produto_Fornecedor']}', ";
                $sqlInsert .= "'{$row['D001_Descricao_Produto']}', ";
                $sqlInsert .= "'{$row['Qtde_Comprar']}', ";
                $sqlInsert .= "'{$row['T006_Valor_Preco_Sem_Desconto_Unitario']}', ";
                $sqlInsert .= "'{$row['D009_Id']}', ";
                $sqlInsert .= "'{$i}', ";
                $sqlInsert .= "'{$row['D037_Id']}', ";
                $sqlInsert .= "'{$row['D005_Id']}', ";
                $sqlInsert .= "'{$row['T006_Id']}' ";
                $sqlInsert .= "); ";
                mysql_query($sqlInsert);
                $i++;
            } else {
                $sqlComponentes = mysql_query("SELECT D009_Id,
                                                      D009_C004_Id,
                                                      D037_Id,
                                                      D005_Id,
                                                      D083_Codigo_Produto_Fornecedor,
                                                      D001_Codigo_Produto,
                                                      D001_Descricao_Produto,
                                                      D074_Quantidade
                                                 FROM D074
                                            LEFT JOIN D001 ON D001_Id = D074_D001_Id_Composicao 
                                            LEFT JOIN D049 ON D001_Id = D049_D001_Id
                                            LEFT JOIN D009 ON D049_Id = D009_D049_Id
                                            LEFT JOIN D037 ON D037_Id = D001_D037_Id
                                            LEFT JOIN D005 ON D005_Id = D001_D005_Id
                                            LEFT JOIN D083 ON D001_Id=D083_D001_Id AND D083_D024_Id = '{$rowD024['T218_D024_Id']}'
                                                WHERE D074_D001_Id = '{$row['D001_Id']}' 
                                                  AND D009_C004_Id ='{$g['empresaAtual']}'
                                                  AND D049_Flag_Tipo = 'CP'");
                while($rowComponentes = mysql_fetch_array($sqlComponentes)){
                    $rowComponentes['Qtde_Comprar'] = $row['Qtde_Comprar']*$rowComponentes['D074_Quantidade'];
                    $rowComponentes['D001_Codigo_Produto'] = mysql_real_escape_string($rowComponentes['D001_Codigo_Produto']);
                    $rowComponentes['D083_Codigo_Produto_Fornecedor'] = mysql_real_escape_string($rowComponentes['D083_Codigo_Produto_Fornecedor']);
                    $rowComponentes['D001_Descricao_Produto'] = mysql_real_escape_string($rowComponentes['D001_Descricao_Produto']);

                    $sqlInsert = "insert into T219 ";
                    $sqlInsert .= "(T219_T218_Id, ";
                    $sqlInsert .= "T219_C004_Id, ";
                    $sqlInsert .= "T219_Codigo_Produto, ";
                    $sqlInsert .= "T219_Codigo_Substituto, ";
                    $sqlInsert .= "T219_Descricao_Produto, ";
                    $sqlInsert .= "T219_Quantidade, ";
                    $sqlInsert .= "T219_Valor_Custo_Unitario,  ";
                    $sqlInsert .= "T219_D009_Id, ";
                    $sqlInsert .= "T219_Item, ";
                    $sqlInsert .= "T219_D037_Id, ";
                    $sqlInsert .= "T219_D005_Id, ";
                    $sqlInsert .= "T219_T006_Id) ";
                    $sqlInsert .= "values  ";
                    $sqlInsert .= "('{$T218_Id}', ";
                    $sqlInsert .= "'{$rowComponentes['D009_C004_Id']}', ";
                    $sqlInsert .= "'{$rowComponentes['D001_Codigo_Produto']}', ";
                    $sqlInsert .= "'{$rowComponentes['D083_Codigo_Produto_Fornecedor']}', ";
                    $sqlInsert .= "'{$rowComponentes['D001_Descricao_Produto']}', ";
                    $sqlInsert .= "'{$rowComponentes['Qtde_Comprar']}', ";
                    $sqlInsert .= "'0', ";
                    $sqlInsert .= "'{$rowComponentes['D009_Id']}', ";
                    $sqlInsert .= "'{$i}', ";
                    $sqlInsert .= "'{$rowComponentes['D037_Id']}', ";
                    $sqlInsert .= "'{$rowComponentes['D005_Id']}', ";
                    $sqlInsert .= "'{$row['T006_Id']}' ";
                    $sqlInsert .= "); ";

                    log("insert Ligia: ".$sqlInsert);
                    mysql_query($sqlInsert);
                    $i++;
                }
            }

        }
        //esses métodos agora serão realizados no grid em etapas Lígia Nakazato 22/08/2018
        //$retorno = $this->est015atualizarCFOP($T218_Id);
        // $retorno = $this->atualizarAliquotas($T218_Id, $g['empresaAtual']);

        // $retorno = mysql_query("select T219_Id from T219 where T219_T218_Id='{$T218_Id}'");
        // while ($row = mysql_fetch_array($retorno)) {
        //     $cSql = mysql_query("call T219_Gravar_Totalizacao('{$row['T219_Id']}');");
        // }

        // $cSql = mysql_query("call T218_Gravar_Totalizacao_3('{$T218_Id}');");
        // $cSql = mysql_query("call T005_Gravar_Totalizacao_4('{$T005_Id}');");
        return $T005_Id;
        // return true;
    }

    /**
     * retornaUltimoPrecoCompra
     *
     * @param int $D049_Id
     * @param int $D024_Id
     * @return String
     */
    public function retornaUltimoPrecoCompra($D049_Id, $D024_Id) {
        global $g;
        $D024  = mysql_query("SELECT D024_Cnpj_Matriz FROM D024 WHERE D024_Id='{$D024_Id}'");
        $mD024 = mysql_fetch_array($D024);
        if (!empty($mD024['D024_Cnpj_Matriz'])) {
            $extra = " OR D024_Cnpj_Matriz='{$mD024['D024_Cnpj_Matriz']}' ";
        } else {
            $extra = "";
        }
        // Seleciona a última NF desse D009 para esse D024 pelo historico
        $T001  = mysql_query("SELECT T001_Id, 
                        T001_Quantidade, 
                        T001_Valor_Preco_Unitario, 
                        T001_Descricao_Operacao,
                        T001_Data_Lancamento,
                        C007_Primeiro_Nome,
                        T001_C004_Id
                   FROM T001
                      LEFT JOIN D024 ON D024_Id=T001_D024_Id
                      LEFT JOIN T014 ON T014_Id=T001_T014_Id
                      LEFT JOIN T225 ON T225_Id=T014_T225_Id 
                      LEFT JOIN T224 ON T224_Id=T225_T224_Id
                      LEFT JOIN C007 ON C007_Id=T224_C007_Id_Vendedor_Interno  
                      LEFT JOIN D009 ON D009_Id=T001_D009_Id
                    WHERE D024_Id='{$D024_Id}'
                      AND D009_D049_Id='{$D049_Id}'
                      AND T001_Flag_Operacao='E'
                      ORDER BY T001_Id DESC LIMIT 1");
        $mT001 = mysql_fetch_assoc($T001);
        $mT001['C007_Primeiro_Nome'] = substr($mT001['C007_Primeiro_Nome'], 0, 8);
        
        // Seleciona a última NF desse D009 para esse D024
        $T013  = mysql_query("SELECT T013_Id,
                        T014_Unidade, 
                        T014_Quantidade, 
                        T014_Valor_Custo_Unitario, 
                        T013_Numero_Nota_Fiscal,
                        T013_Data_Emissao,
                        C007_Primeiro_Nome,
                        T013_C004_Id
                    FROM T014
                      LEFT JOIN T013 ON T013_Id=T014_T013_Id
                      LEFT JOIN D024 ON D024_Id=T013_D024_Id
                      LEFT JOIN T225 ON T225_Id=T014_T225_Id 
                      LEFT JOIN T224 ON T224_Id=T225_T224_Id
                      LEFT JOIN C007 ON C007_Id=T224_C007_Id_Vendedor_Interno
                      LEFT JOIN D009 ON D009_Id=T014_D009_Id
                    WHERE T013_D024_Id='{$D024_Id}'
                      AND D009_D049_Id='{$D049_Id}'
                      {$extra}
                      ORDER BY T013_Id DESC LIMIT 1");
        $mT013 = mysql_fetch_assoc($T013);
        $mT013['C007_Primeiro_Nome'] = substr($mT013['C007_Primeiro_Nome'], 0, 8);
        
        // Selecionando o último pedido desse D009 para esse D024
        $T224  = mysql_query("SELECT T224_Id, 
                        T225_Quantidade, 
                        T225_Valor_Preco_Unitario, 
                        T224_Data_Emissao,
                        C007_Primeiro_Nome,
                        T224_C004_Id 
                    FROM T225
                      LEFT JOIN T224 ON T224_Id=T225_T224_Id
                      LEFT JOIN T007 ON T007_T224_Id=T224_Id
                      LEFT JOIN D024 ON D024_Id=T224_D024_Id
                      LEFT JOIN C007 ON C007_Id=T224_C007_Id_Vendedor_Interno
                      LEFT JOIN D009 ON D009_Id=T225_D009_Id
                    WHERE T224_D024_Id='{$D024_Id}'
                      AND D009_D049_Id='{$D049_Id}'
                      {$extra}
                      ORDER BY T224_Id DESC LIMIT 1");
        $mT224 = mysql_fetch_assoc($T224);
        $mT224['C007_Primeiro_Nome'] = substr($mT224['C007_Primeiro_Nome'], 0, 8);
        
        // Selecionando o último orçamento desse D009 para esse D024
        $T218  = mysql_query("SELECT T218_Id, 
                      T219_Quantidade, 
                      T219_Valor_Preco_Unitario,
                      T218_Data_Emissao,
                      C007_Primeiro_Nome,
                      T218_C004_Id 
                    FROM T219
                      LEFT JOIN T218 ON T218_Id=T219_T218_Id
                      LEFT JOIN D024 ON D024_Id=T218_D024_Id
                      LEFT JOIN C007 ON C007_Id=T218_C007_Id_Vendedor_Interno
                      LEFT JOIN D009 ON D009_Id=T219_D009_Id
                    WHERE T218_D024_Id='{$D024_Id}'
                      AND D009_D049_Id='{$D049_Id}'
                      {$extra}
                      ORDER BY T218_Id DESC LIMIT 1");
        $mT218 = mysql_fetch_assoc($T218);
        $mT218['C007_Primeiro_Nome'] = substr($mT218['C007_Primeiro_Nome'], 0, 8);
        
        $retorno = '';
        if (mysql_num_rows($T001) > 0) {
            // Caso exista alguma nota, já é retornado o valor mais recente
            $mT001['T001_Valor_Preco_Unitario'] = gCorrigeNumero($mT001['T001_Valor_Preco_Unitario']);
            $mT001['T001_Data_Lancamento']      = gCorrigeData($mT001['T001_Data_Lancamento']);
            $mT001['T001_Quantidade']           = gCorrigeNumero($mT001['T001_Quantidade']);
            
            $retorno .= "{$mT001['T001_Descricao_Operacao']} - Qnt: {$mT001['T001_Quantidade']} - R\$ {$mT001['T001_Valor_Preco_Unitario']} - {$mT001['T001_Data_Lancamento']} - {$mT001['C007_Primeiro_Nome']} ({$mT001['T001_C004_Id']})\n";
        } else if (mysql_num_rows($T013) > 0) {
            // Caso exista alguma nota, já é retornado o valor mais recente
            $mT013['T014_Valor_Custo_Unitario'] = gCorrigeNumero($mT013['T014_Valor_Custo_Unitario']);
            $mT013['T013_Data_Emissao']         = gCorrigeData($mT013['T013_Data_Emissao']);
            $mT013['T014_Quantidade']           = gCorrigeNumero($mT013['T014_Quantidade']);
            
            $retorno .= "NFE-{$mT013['T013_Numero_Nota_Fiscal']} - Qnt: {$mT013['T014_Quantidade']} - R\$ {$mT013['T014_Valor_Custo_Unitario']} - {$mT013['T013_Data_Emissao']} - {$mT013['C007_Primeiro_Nome']} ({$mT013['T013_C004_Id']})\n";
        } 

        if (mysql_num_rows($T224) > 0) {
            // Caso exista algum pedido, já é retornado o valor mais recente
            $mT224['T225_Valor_Preco_Unitario']  = gCorrigeNumero($mT224['T225_Valor_Preco_Unitario']);
            $mT224['T224_Data_Emissao']          = gCorrigeData($mT224['T224_Data_Emissao']);
            $mT224['T225_Quantidade']            = gCorrigeNumero($mT224['T225_Quantidade']);
            
            $retorno .= "OC-{$mT224['T224_Id']} - Qnt: {$mT224['T225_Quantidade']} - R\$ {$mT224['T225_Valor_Preco_Unitario']} - {$mT224['T224_Data_Emissao']} - {$mT224['C007_Primeiro_Nome']} ({$mT224['T224_C004_Id']})\n";
        } 

        if (mysql_num_rows($T218) > 0) {
            // Caso exista algum orçamento já é retornado o mais recente
            $mT218['T219_Valor_Preco_Unitario'] = gCorrigeNumero($mT218['T219_Valor_Preco_Unitario']);
            $mT218['T218_Data_Emissao']         = gCorrigeData($mT218['T218_Data_Emissao']);
            $mT218['T219_Quantidade']           = gCorrigeNumero($mT218['T219_Quantidade']);
            
            $retorno .= "COT-{$mT218['T218_Id']} - Qnt: {$mT218['T219_Quantidade']} - R\$ {$mT218['T219_Valor_Preco_Unitario']} - {$mT218['T218_Data_Emissao']} - {$mT218['C007_Primeiro_Nome']} ({$mT218['T218_C004_Id']})";
        }

        if($retorno == ''){
            return "Primeira venda";
        } else {
            return  $retorno;
        }
    }

    /**
     * atualizarPrecoNegociado
     *
     * @param int $T219_Id
     * @param int $T004_Id
     * @param int $T006_Id
     * @return true
     */
    public function atualizarPrecoNegociado($T219_Id, $T004_Id, $T006_Id)
    {
        global $g;
        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();

        if($T006_Id > 0){
            $sqlT006 = mysql_query("SELECT T006_D009_Id, 
                                           T005_Flag_Lista_Preco,
                                           T005_D024_Id,
                                           T005_Flag_ACP,
                                           T006_D006_Id,
                                           T006_Aliquota_ICMS,
                                           T006_Aliquota_PIS,
                                           T006_Aliquota_COFINS,
                                           T219_D006_Id,
                                           T219_D005_Id,
                                           T219_Flag_Origem_Produto,
                                           T006_Flag_Origem_Produto,
                                           T006_D005_Id,
                                           T006_Aliquota_IPI,
                                           T006_ST_VA,
                                           T006_Percentual_Reducao_ICMS_ST,
                                           T006_ST_ICMS_Interno,
                                           T006_Percentual_Reducao_ICMS,
                                           T006_ICMS_Inter_Estadual,
                                           T006_Aliquota_IRPJ,
                                           T006_Aliquota_CSLL,
                                           T006_T219_Id,
                                           T006_Valor_Preco_Original,
                                           T006_Demonstrativo_Preco
                                      FROM T006 
                                 LEFT JOIN T005 ON T005_Id=T006_T005_Id 
                                 LEFT JOIN T219 ON T006_Id=T219_T006_Id 
                                     WHERE T006_Id = '{$T006_Id}'");
            $mT006 = mysql_fetch_array($sqlT006);

            if ($mT006['T006_D006_Id']<=0 || $mT006['T006_D005_Id']<=0 || !isset($mT006['T006_Flag_Origem_Produto']) || $mT006['T219_D006_Id']<=0 || $mT006['T219_D005_Id']<=0 || !isset($mT006['T219_Flag_Origem_Produto']) || $mT006['T006_T219_Id'] <= 0 ){
                return true;
            }

            // Só vai atualizar o preço nas cotações se as informações acima baterem e se alguma cotação do grupo atualizou o preço na venda
            $dadosPrecoVenda = $CAD002->retornaPrecoVenda($mT006['T006_D009_Id'], $mT006['T005_Flag_Lista_Preco'], $mT006['T005_D024_Id'], false, $mT006['T006_D006_Id'], $mT006['T006_Aliquota_ICMS'], $T219_Id, false, 4, $mT006['T006_Aliquota_PIS'], $mT006['T006_Aliquota_COFINS'], $mT006['T006_Aliquota_IPI'], $mT006['T006_ST_VA'], $mT006['T006_Percentual_Reducao_ICMS_ST'], $mT006['T006_ST_ICMS_Interno'], $mT006['T006_Percentual_Reducao_ICMS'], $mT006['T006_ICMS_Inter_Estadual'], $mT006['T006_Aliquota_IRPJ'], $mT006['T006_Aliquota_CSLL']);
            $precoVenda      = gCorrigeNumeroInverte($dadosPrecoVenda[0]); 
            $demonstrativo   = mysql_real_escape_string(serialize($mT006['T006_Demonstrativo_Preco']));

            
            //$precoVenda      = $mT004['T004_Valor_Preco_Original'];
            //$demonstrativo   = mysql_real_escape_string(serialize($mT004['T004_Demonstrativo_Preco']));

            mysqli_query("UPDATE T219 SET T219_Valor_Preco_Venda = '{$precoVenda}', T219_Demonstrativo_Preco = '{$demonstrativo}' WHERE T219_Id = '{$T219_Id}'");

        } else if($T004_Id > 0){
            $sqlT004 = mysql_query("SELECT T004_D009_Id,
                                           T003_Flag_Lista_Preco,
                                           T003_D024_Id,
                                           T003_Flag_ACP,
                                           T004_D006_Id,
                                           T004_Aliquota_ICMS,
                                           T004_Aliquota_PIS,
                                           T004_Aliquota_COFINS,
                                           T004_D005_Id,
                                           T004_Flag_Origem_Produto,
                                           T219_D005_Id,
                                           T219_D006_Id,
                                           T219_Flag_Origem_Produto,
                                           T004_Aliquota_IPI,
                                           T004_T219_Id,
                                           T004_Valor_Preco_Original,
                                           T004_Demonstrativo_Preco
                                      FROM T004
                                 LEFT JOIN T003 ON T003_Id=T004_T003_Id
                                 LEFT JOIN T219 ON T004_Id=T219_T004_Id
                                     WHERE T004_Id = '{$T004_Id}'");
            $mT004 = mysql_fetch_array($sqlT004);

            if ($mT004['T004_D006_Id']<=0 || $mT004['T004_D005_Id']<=0 || !isset($mT004['T004_Flag_Origem_Produto']) || $mT004['T219_D006_Id']<=0 || $mT004['T219_D005_Id']<=0 || !isset($mT004['T219_Flag_Origem_Produto']) || $mT004['T004_T219_Id'] <= 0){
                return true;
            }
            $dadosPrecoVenda = $CAD002->retornaPrecoVenda($mT004['T004_D009_Id'], $mT004['T003_Flag_Lista_Preco'], $mT004['T003_D024_Id'], false, $mT004['T004_D006_Id'], $mT004['T004_Aliquota_ICMS'], $T219_Id, false, 4, $mT004['T004_Aliquota_PIS'], $mT004['T004_Aliquota_COFINS'], $mT004['T004_Aliquota_IPI'], $mT004['T004_ST_VA'], $mT004['T004_Percentual_Reducao_ICMS_ST'], $mT004['T004_ST_ICMS_Interno'], $mT004['T004_Percentual_Reducao_ICMS'], $mT004['T004_ICMS_Inter_Estadual'], $mT004['T004_Aliquota_IRPJ'], $mT004['T004_Aliquota_CSLL']);
            $precoVenda      = gCorrigeNumeroInverte($dadosPrecoVenda[0]);
            $demonstrativo   = mysql_real_escape_string(serialize($dadosPrecoVenda[1]));

            // Só vai atualizar o preço nas cotações se as informações acima baterem e se alguma cotação do grupo atualizou o preço na venda
            //$precoVenda      = $mT004['T004_Valor_Preco_Original'];
            //$demonstrativo   = mysql_real_escape_string(serialize($mT004['T004_Demonstrativo_Preco']));

            mysqli_query("UPDATE T219 SET T219_Valor_Preco_Venda = '{$precoVenda}', T219_Demonstrativo_Preco = '{$demonstrativo}' WHERE T219_Id = '{$T219_Id}'");
        }
        return true;
    }

    /**
     * gerarCotacaoAutomatica
     *
     * @param string $extra
     * @return true/string (erros)
     */
    public function gerarCotacaoAutomatica($extra)
    {
        global $g;

        $countErro = 0;
        $arrayD024 = array();
        $sql = "SELECT Fornecedor.D024_Id, GROUP_CONCAT(T004_Id SEPARATOR ', ') as T004_Ids, COUNT(T004_Id) as count FROM T004 
                LEFT JOIN D009 on D009_Id=T004_D009_Id
                LEFT JOIN D049 on D049_Id=D009_D049_Id
                LEFT JOIN D001 on D001_Id=D049_D001_Id
                LEFT JOIN D082 on D082_Id=D049_D082_Id
                LEFT JOIN D037 on D037_Id=T004_D037_Id
                LEFT JOIN D005 on D005_Id=T004_D005_Id
                LEFT JOIN D006 on D006_Id=T004_D006_Id
                LEFT JOIN T006 on T006_T004_Id=T004_Id
                LEFT JOIN T005 ON T005_Id=T006_T005_Id
                LEFT JOIN T003 ON T003_Id=T004_T003_Id
                LEFT JOIN D024 as Cliente ON T003_D024_Id=Cliente.D024_Id
                LEFT JOIN T219 ON T004_Id=T219_T004_Id
                LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
                LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
                LEFT JOIN C007 AS marcou ON marcou.C007_Id = T004_C007_Id_Comprador
                LEFT JOIN T241 ON T241_D001_Id=D001_Id
                LEFT JOIN D024 as Fornecedor ON T241_D024_Id=Fornecedor.D024_Id
                LEFT JOIN T004A ON T004_Id=T004A_T004_Id
                WHERE {$extra}";
        $sql = explode("LIMIT", $sql);
        $sql = $sql[0];
        $sql .= " GROUP BY Fornecedor.D024_Id";


        $retorno = mysql_query($sql);
        while($row = mysql_fetch_array($retorno)){
            if(empty($row['D024_Id']) || $row['D024_Id'] == 0){
                $countErro = $row['count'];
            }
            $arrayD024[$row['D024_Id']] = $row['T004_Ids'];
        }

        if($countErro > 0){
            if($countErro == 1){
                return "Existe um produto sem fornecedor.";
            } else {
                return "Existem {$countErro} produtos sem fornecedor.";
            }
        }

        $sqlT218 = mysql_query("SELECT T218_Grupo FROM T218 ORDER BY T218_Grupo DESC LIMIT 1");
        $resT218 = mysql_fetch_array($sqlT218);
        $grupo = $resT218['T218_Grupo'] + 1;

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        foreach ($arrayD024 as $D024_Id => $T004_Ids) {
            $D013_Id = 0;
            $sqlD013 = mysql_query("SELECT T218_D013_Id FROM T218 WHERE T218_C007_Id_Vendedor_Interno = '{$g['usuarioAtual']}' AND T218_D024_Id = '{$D024_Id}' AND T218_D013_Id > 0 ORDER BY T218_Id DESC LIMIT 1");
            if(mysql_num_rows($sqlD013) == 1){
                $rowD013 = mysql_fetch_array($sqlD013);
                $D013_Id = $rowD013['T218_D013_Id'];
            }

            //Verifica se o contato está ativo
            $sqlD013 = mysql_query("SELECT * FROM D013 WHERE D013_Id = '{$D013_Id}' AND D013_Flag_Ativo = 'S'");
            if(mysql_num_rows($sqlD013) <= 0) {
                //Se estiver inativo, buscamos o primeiro contato ativo
                $sqlD013 = mysql_query("SELECT D013_Id FROM D013 WHERE D013_D024_Id = '{$D024_Id}' AND D013_Flag_Ativo = 'S' LIMIT 1");
                $rowD013 = mysql_fetch_array($sqlD013);
                $D013_Id = $rowD013['D013_Id'];
            }

            $calcularST = ($g['C031']['comprasCalcularST'] == 'S') ? 'S' : 'N';
            $calcularIPI = ($g['C031']['comprasCalcularIPI'] == 'S') ? 'S' : 'N';
            $calcularICMS = ($g['C031']['comprasCalcularICMS'] == 'S') ? 'S' : 'N';
            mysql_query("INSERT INTO T218 (T218_D024_Id, T218_C004_Id, T218_D013_Id, T218_C007_Id_Vendedor_Interno, T218_Data_Emissao, T218_Flag_Acp, T218_Grupo, T218_Flag_Calcular_ST, T218_Flag_Calcular_IPI, T218_Flag_Calcular_ICMS) VALUES ('{$D024_Id}', '{$g['empresaAtual']}', '{$D013_Id}', '{$g['usuarioAtual']}', CURDATE(), '6', '{$grupo}', '{$calcularST}', '{$calcularIPI}', '{$calcularICMS}')");
            $erro = mysql_error();
            if(!empty($erro)){
                mysql_query("ROLLBACK");
                return "Erro: " . $erro;
            }
            $T218_Id = $g['mysqlLastId'];

            $hora  = date("H:i:s");
            $T218A = mysql_query("INSERT INTO T218A(T218A_T218_Id,T218A_Hora_Emissao)VALUES('{$T218_Id}','{$hora}')");
            $erro  = mysql_error();
            if(!empty($erro)){
                mysql_query("ROLLBACK");
                return "Erro: " . $erro;
            }

            $sql = <<<EOT
                    SELECT T004_Id,
                    D001_Codigo_Produto,
                    D001_Descricao_Produto,
                    D009_Quantidade_Comprar_Calculada,
                    IF(T004_Quantidade>D009_Quantidade_Estoque_Liquido, T004_Quantidade-if(D009_Quantidade_Estoque_Liquido>0,D009_Quantidade_Estoque_Liquido,0), T004_Quantidade) as T004_Quantidade,
                    D009_Id,
                    T004_Valor_Preco_Sem_Desconto_Unitario,
                    D009_C004_Id,
                    D005_Id,
                    D037_Id,
                    D049_Origem_Mercadoria,
                    D083_Codigo_Produto_Fornecedor
                    from T004 
                    LEFT JOIN D009 on D009_Id=T004_D009_Id
                    LEFT JOIN D049 on D049_Id=D009_D049_Id
                    LEFT JOIN D001 on D001_Id=D049_D001_Id
                    LEFT JOIN D082 on D082_Id=D049_D082_Id
                    LEFT JOIN D037 on D037_Id=T004_D037_Id
                    LEFT JOIN D005 on D005_Id=T004_D005_Id
                    LEFT JOIN D006 on D006_Id=T004_D006_Id
                    LEFT JOIN T006 on T006_T004_Id=T004_Id
                    LEFT JOIN D083 ON D083_D001_Id=D001_Id AND D083_D024_Id = '$D024_Id'
                    WHERE T004_Id IN ({$T004_Ids})
                    GROUP BY T004_Id
EOT;
            $retorno = mysql_query($sql);

            $i = 1;
            while ($row = mysql_fetch_array($retorno)) {
                $row['D009_C004_Id'] = mysql_real_escape_string($row['D009_C004_Id']);
                $row['D001_Codigo_Produto'] = mysql_real_escape_string($row['D001_Codigo_Produto']);
                $row['D083_Codigo_Produto_Fornecedor'] = mysql_real_escape_string($row['D083_Codigo_Produto_Fornecedor']);
                $row['D001_Descricao_Produto'] = mysql_real_escape_string($row['D001_Descricao_Produto']);
                $row['T004_Quantidade'] = mysql_real_escape_string($row['T004_Quantidade']);
                $row['T004_Valor_Preco_Sem_Desconto_Unitario'] = mysql_real_escape_string($row['T004_Valor_Preco_Sem_Desconto_Unitario']);
                $row['D009_Id'] = mysql_real_escape_string($row['D009_Id']);
                $row['D037_Id'] = mysql_real_escape_string($row['D037_Id']);
                $row['D005_Id'] = mysql_real_escape_string($row['D005_Id']);
                $row['D049_Origem_Mercadoria'] = mysql_real_escape_string($row['D049_Origem_Mercadoria']);
                $row['T004_Id'] = mysql_real_escape_string($row['T004_Id']);

                $sqlInsert = "insert into T219 ";
                $sqlInsert .= "(T219_T218_Id, ";
                $sqlInsert .= "T219_C004_Id, ";
                $sqlInsert .= "T219_Codigo_Produto, ";
                $sqlInsert .= "T219_Codigo_Substituto, ";
                $sqlInsert .= "T219_Descricao_Produto, ";
                $sqlInsert .= "T219_Quantidade, ";
                $sqlInsert .= "T219_Valor_Custo_Unitario,  ";
                $sqlInsert .= "T219_D009_Id, ";
                $sqlInsert .= "T219_Item, ";
                $sqlInsert .= "T219_D037_Id, ";
                $sqlInsert .= "T219_D005_Id, ";
                $sqlInsert .= "T219_Flag_Origem_Produto, ";
                $sqlInsert .= "T219_T004_Id) ";
                $sqlInsert .= "values  ";
                $sqlInsert .= "('{$T218_Id}', ";
                $sqlInsert .= "'{$row['D009_C004_Id']}', ";
                $sqlInsert .= "'{$row['D001_Codigo_Produto']}', ";
                $sqlInsert .= "'{$row['D083_Codigo_Produto_Fornecedor']}', ";
                $sqlInsert .= "'{$row['D001_Descricao_Produto']}', ";
                $sqlInsert .= "'{$row['T004_Quantidade']}', ";
                $sqlInsert .= "'{$row['T004_Valor_Preco_Sem_Desconto_Unitario']}', ";
                $sqlInsert .= "'{$row['D009_Id']}', ";
                $sqlInsert .= "'{$i}', ";
                $sqlInsert .= "'{$row['D037_Id']}', ";
                $sqlInsert .= "'{$row['D005_Id']}', ";
                $sqlInsert .= "'{$row['D049_Origem_Mercadoria']}', ";
                $sqlInsert .= "'{$row['T004_Id']}' ";
                $sqlInsert .= "); ";
                mysql_query($sqlInsert);
                $erro = mysql_error();
                if(!empty($erro)){
                    mysql_query("ROLLBACK");
                    return "Erro: " . $erro;
                }
                $i++;
            }

            // $retorno = $this->est015atualizarCFOP($T218_Id);
            // $retorno = $this->atualizarAliquotas($T218_Id, $g['empresaAtual']);

            // $retorno = mysql_query("select T219_Id from T219 where T219_T218_Id='{$T218_Id}'");
            // $erro = mysql_error();
            // if(!empty($erro)){
            //     mysql_query("ROLLBACK");
            //     return "Erro: " . $erro;
            // }
            // while ($row = mysql_fetch_array($retorno)) {
            //     $cSql = mysql_query("call T219_Gravar_Totalizacao('{$row['T219_Id']}');");
            //     $erro = mysql_error();
            //     if(!empty($erro)){
            //         mysql_query("ROLLBACK");
            //         return "Erro: " . $erro;
            //     }
            // }
            
            // $cSql = mysql_query("call T218_Gravar_Totalizacao_3('{$T218_Id}');");
            // $erro = mysql_error();
            // if(!empty($erro)){
            //     mysql_query("ROLLBACK");
            //     return "Erro: " . $erro;
            // }
        }
        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");
        return array('grupo' => $grupo);
    }

    /**
     * gerarCotacaoPedidoAutomatica
     *
     * @param string $extra
     * @return true/string (erros)
     */
    public function gerarCotacaoPedidoAutomatica($extra)
    {
        global $g;

        $countErro = 0;
        $arrayD024 = array();
        $sql = "SELECT Fornecedor.D024_Id, GROUP_CONCAT(T006_Id SEPARATOR ', ') as T006_Ids, COUNT(T006_Id) as count FROM T006 
                LEFT JOIN T006A ON T006A_T006_Id = T006_Id
                LEFT JOIN T261 on T006_Id=T261_T006_Id
                LEFT JOIN D009 on D009_Id=T006_D009_Id
                LEFT JOIN D049 on D049_Id=D009_D049_Id
                LEFT JOIN D001 on D001_Id=D049_D001_Id
                LEFT JOIN D082 on D082_Id=D049_D082_Id
                LEFT JOIN D037 on D037_Id=T006_D037_Id
                LEFT JOIN D005 on D005_Id=T006_D005_Id
                LEFT JOIN D006 on D006_Id=T006_D006_Id
                LEFT JOIN T005 ON T005_Id=T006_T005_Id
                LEFT JOIN D024 as Cliente ON T005_D024_Id=Cliente.D024_Id
                LEFT JOIN T003 ON T003_Id=T005_T003_Id
                LEFT JOIN T004 ON T004_Id=T006_T004_Id
                LEFT JOIN T219 ON T006_Id=T219_T006_Id
                LEFT JOIN T218 ON T218_Id=T219_T218_Id
                LEFT JOIN T224 ON T218_Id=T224_T218_Id
                LEFT JOIN T225 ON T225_Id=T006_T225_Id
                LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
                LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
                LEFT JOIN C007 AS marcou ON marcou.C007_Id = T006_C007_Id_Comprador
                LEFT JOIN T241 ON T241_D001_Id=D001_Id
                LEFT JOIN D024 as Fornecedor ON T241_D024_Id=Fornecedor.D024_Id
                WHERE {$extra}";
        $sql = explode("LIMIT", $sql);
        $sql = $sql[0];
        $sql .= " GROUP BY Fornecedor.D024_Id";

        $retorno = mysql_query($sql);
        while($row = mysql_fetch_array($retorno)){
            if(empty($row['D024_Id']) || $row['D024_Id'] == 0){
                $countErro = $row['count'];
            }
            $arrayD024[$row['D024_Id']] = $row['T006_Ids'];
        }

        if($countErro > 0){
            if($countErro == 1){
                return "Existe um produto sem fornecedor.";
            } else {
                return "Existem {$countErro} produtos sem fornecedor.";
            }
        }

        $sqlT218 = mysql_query("SELECT T218_Grupo FROM T218 ORDER BY T218_Grupo DESC LIMIT 1");
        $resT218 = mysql_fetch_array($sqlT218);
        $grupo = $resT218['T218_Grupo'] + 1;

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        foreach ($arrayD024 as $D024_Id => $T006_Ids) {
            $D013_Id = 0;
            $sqlD013 = mysql_query("SELECT T218_D013_Id FROM T218 WHERE T218_C007_Id_Vendedor_Interno = '{$g['usuarioAtual']}' AND T218_D024_Id = '{$D024_Id}' AND T218_D013_Id > 0 ORDER BY T218_Id DESC LIMIT 1");
            if(mysql_num_rows($sqlD013) == 1){
                $rowD013 = mysql_fetch_array($sqlD013);
                $D013_Id = $rowD013['T218_D013_Id'];
            }
            $calcularST = ($g['C031']['comprasCalcularST'] == 'S') ? 'S' : 'N';
            $calcularIPI = ($g['C031']['comprasCalcularIPI'] == 'S') ? 'S' : 'N';
            $calcularICMS = ($g['C031']['comprasCalcularICMS'] == 'S') ? 'S' : 'N';
            mysql_query("INSERT INTO T218 (T218_D024_Id, T218_C004_Id, T218_D013_Id, T218_C007_Id_Vendedor_Interno, T218_Data_Emissao, T218_Flag_Acp, T218_Grupo, T218_Flag_Calcular_ST, T218_Flag_Calcular_IPI, T218_Flag_Calcular_ICMS) VALUES ('{$D024_Id}', '{$g['empresaAtual']}', '{$D013_Id}', '{$g['usuarioAtual']}', CURDATE(), '6', '{$grupo}', '{$calcularST}', '{$calcularIPI}', '{$calcularICMS}')");
            $erro = mysql_error();
            if(!empty($erro)){
                mysql_query("ROLLBACK");
                return "Erro: " . $erro;
            }
            $T218_Id = $g['mysqlLastId'];

            $sql = <<<EOT
                    SELECT T006_Id,
                    D001_Codigo_Produto,
                    D001_Descricao_Produto,
                    D009_Quantidade_Comprar_Calculada,
                    T006_Quantidade,
                    T006_Quantidade-if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0)-T006_Quantidade_OC as Qtde_Comprar,
                    D009_Id,
                    T006_Valor_Preco_Sem_Desconto_Unitario,
                    D009_C004_Id,
                    D005_Id,
                    D001_Id,
                    D037_Id,
                    D083_Codigo_Produto_Fornecedor,
                    ifnull(D049_Flag_Tipo,'CP') AS D049_Flag_Tipo,
                    T005_Id
                    from T006
                    LEFT JOIN D009 on D009_Id=T006_D009_Id
                    LEFT JOIN D049 on D049_Id=D009_D049_Id
                    LEFT JOIN D001 on D001_Id=D049_D001_Id
                    LEFT JOIN D082 on D082_Id=D049_D082_Id
                    LEFT JOIN D037 on D037_Id=T006_D037_Id
                    LEFT JOIN D005 on D005_Id=T006_D005_Id
                    LEFT JOIN D006 on D006_Id=T006_D006_Id
                    LEFT JOIN T005 ON T005_Id=T006_T005_Id
                    LEFT JOIN T003 ON T003_Id=T005_T003_Id
                    LEFT JOIN D024 ON D024_Id=T005_D024_Id
                    LEFT JOIN T004 ON T004_Id=T006_T004_Id
                    LEFT JOIN T219 ON T004_Id=T219_T004_Id OR T006_Id = T219_T006_Id
                    LEFT JOIN T218 ON T218_Id=T219_T218_Id
                    LEFT JOIN T224 ON T218_Id=T224_T218_Id
                    LEFT JOIN D083 ON D001_Id=D083_D001_Id AND D083_D024_Id='{$D024_Id}'
                    LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
                    LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
                    LEFT JOIN C007 AS marcou ON marcou.C007_Id = T006_C007_Id_Comprador
                    WHERE T006_Id IN ({$T006_Ids})
                    GROUP BY T006_Id
EOT;
            $retorno = mysql_query($sql);

            $i = 1;
            while ($row = mysql_fetch_array($retorno)) {
                if(!in_array($row['D049_Flag_Tipo'], array('PD', 'IN'), true)){
                    $row['D009_C004_Id'] = mysql_real_escape_string($row['D009_C004_Id']);
                    $row['D001_Codigo_Produto'] = mysql_real_escape_string($row['D001_Codigo_Produto']);
                    $row['D083_Codigo_Produto_Fornecedor'] = mysql_real_escape_string($row['D083_Codigo_Produto_Fornecedor']);
                    $row['D001_Descricao_Produto'] = mysql_real_escape_string($row['D001_Descricao_Produto']);
                    $row['Qtde_Comprar'] = mysql_real_escape_string($row['Qtde_Comprar']);
                    $row['T006_Valor_Preco_Sem_Desconto_Unitario'] = mysql_real_escape_string($row['T006_Valor_Preco_Sem_Desconto_Unitario']);
                    $row['D009_Id'] = mysql_real_escape_string($row['D009_Id']);
                    $row['D037_Id'] = mysql_real_escape_string($row['D037_Id']);
                    $row['D005_Id'] = mysql_real_escape_string($row['D005_Id']);
                    $row['T006_Id'] = mysql_real_escape_string($row['T006_Id']);
                    $T005_Id = $row['T005_Id'];

                    $sqlInsert = "insert into T219 ";
                    $sqlInsert .= "(T219_T218_Id, ";
                    $sqlInsert .= "T219_C004_Id, ";
                    $sqlInsert .= "T219_Codigo_Produto, ";
                    $sqlInsert .= "T219_Codigo_Substituto, ";
                    $sqlInsert .= "T219_Descricao_Produto, ";
                    $sqlInsert .= "T219_Quantidade, ";
                    $sqlInsert .= "T219_Valor_Custo_Unitario,  ";
                    $sqlInsert .= "T219_D009_Id, ";
                    $sqlInsert .= "T219_Item, ";
                    $sqlInsert .= "T219_D037_Id, ";
                    $sqlInsert .= "T219_D005_Id, ";
                    $sqlInsert .= "T219_T006_Id) ";
                    $sqlInsert .= "values  ";
                    $sqlInsert .= "('{$T218_Id}', ";
                    $sqlInsert .= "'{$row['D009_C004_Id']}', ";
                    $sqlInsert .= "'{$row['D001_Codigo_Produto']}', ";
                    $sqlInsert .= "'{$row['D083_Codigo_Produto_Fornecedor']}', ";
                    $sqlInsert .= "'{$row['D001_Descricao_Produto']}', ";
                    $sqlInsert .= "'{$row['Qtde_Comprar']}', ";
                    $sqlInsert .= "'{$row['T006_Valor_Preco_Sem_Desconto_Unitario']}', ";
                    $sqlInsert .= "'{$row['D009_Id']}', ";
                    $sqlInsert .= "'{$i}', ";
                    $sqlInsert .= "'{$row['D037_Id']}', ";
                    $sqlInsert .= "'{$row['D005_Id']}', ";
                    $sqlInsert .= "'{$row['T006_Id']}' ";
                    $sqlInsert .= "); ";

                    mysql_query($sqlInsert);
                    $erro = mysql_error();
                    if(!empty($erro)){
                        mysql_query("ROLLBACK");
                        return "Erro: " . $erro;
                    }
                    $i++;
                } else {
                    $sqlComponentes = mysql_query("SELECT D009_Id,
                                                          D009_C004_Id,
                                                          D037_Id,
                                                          D005_Id,
                                                          D083_Codigo_Produto_Fornecedor,
                                                          D001_Codigo_Produto,
                                                          D001_Descricao_Produto,
                                                          D074_Quantidade
                                                     FROM D074
                                                LEFT JOIN D001 ON D001_Id = D074_D001_Id_Composicao 
                                                LEFT JOIN D049 ON D001_Id = D049_D001_Id 
                                                LEFT JOIN D009 ON D049_Id = D009_D049_Id 
                                                LEFT JOIN D037 ON D037_Id = D001_D037_Id
                                                LEFT JOIN D005 ON D005_Id = D001_D005_Id
                                                LEFT JOIN D083 ON D001_Id=D083_D001_Id AND D083_D024_Id = '{$D024_Id}'
                                                    WHERE D074_D001_Id = '{$row['D001_Id']}' AND D009_C004_Id='{$g['empresaAtual']}'");
                    while($rowComponentes = mysql_fetch_array($sqlComponentes)){
                        $rowComponentes['Qtde_Comprar'] = $row['Qtde_Comprar']*$rowComponentes['D074_Quantidade'];
                        $rowComponentes['D001_Codigo_Produto'] = mysql_real_escape_string($rowComponentes['D001_Codigo_Produto']);
                        $rowComponentes['D083_Codigo_Produto_Fornecedor'] = mysql_real_escape_string($rowComponentes['D083_Codigo_Produto_Fornecedor']);
                        $rowComponentes['D001_Descricao_Produto'] = mysql_real_escape_string($rowComponentes['D001_Descricao_Produto']);

                        $sqlInsert = "insert into T219 ";
                        $sqlInsert .= "(T219_T218_Id, ";
                        $sqlInsert .= "T219_C004_Id, ";
                        $sqlInsert .= "T219_Codigo_Produto, ";
                        $sqlInsert .= "T219_Codigo_Substituto, ";
                        $sqlInsert .= "T219_Descricao_Produto, ";
                        $sqlInsert .= "T219_Quantidade, ";
                        $sqlInsert .= "T219_Valor_Custo_Unitario,  ";
                        $sqlInsert .= "T219_D009_Id, ";
                        $sqlInsert .= "T219_Item, ";
                        $sqlInsert .= "T219_D037_Id, ";
                        $sqlInsert .= "T219_D005_Id, ";
                        $sqlInsert .= "T219_T006_Id) ";
                        $sqlInsert .= "values  ";
                        $sqlInsert .= "('{$T218_Id}', ";
                        $sqlInsert .= "'{$rowComponentes['D009_C004_Id']}', ";
                        $sqlInsert .= "'{$rowComponentes['D001_Codigo_Produto']}', ";
                        $sqlInsert .= "'{$rowComponentes['D083_Codigo_Produto_Fornecedor']}', ";
                        $sqlInsert .= "'{$rowComponentes['D001_Descricao_Produto']}', ";
                        $sqlInsert .= "'{$rowComponentes['Qtde_Comprar']}', ";
                        $sqlInsert .= "'0', ";
                        $sqlInsert .= "'{$rowComponentes['D009_Id']}', ";
                        $sqlInsert .= "'{$i}', ";
                        $sqlInsert .= "'{$rowComponentes['D037_Id']}', ";
                        $sqlInsert .= "'{$rowComponentes['D005_Id']}', ";
                        $sqlInsert .= "'{$row['T006_Id']}' ";
                        $sqlInsert .= "); ";

                        mysql_query($sqlInsert);
                        $erro = mysql_error();
                        if(!empty($erro)){
                            mysql_query("ROLLBACK");
                            return "Erro: " . $erro;
                        }
                        $i++;
                    }
                }
            }
            //esses métodos agora serão realizados no grid em etapas - Lígia Nakazato 22/08/2018
            // $retorno = $this->est015atualizarCFOP($T218_Id);
            // $retorno = $this->atualizarAliquotas($T218_Id, $g['empresaAtual']);
            
            // $retorno = mysql_query("select T219_Id from T219 where T219_T218_Id='{$T218_Id}'");
            // $erro = mysql_error();
            // if(!empty($erro)){
            //     mysql_query("ROLLBACK");
            //     return "Erro: " . $erro;
            // }
            
            // while ($row = mysql_fetch_array($retorno)) {
            //     $cSql = mysql_query("call T219_Gravar_Totalizacao('{$row['T219_Id']}');");
            //     $erro = mysql_error();
            //     if(!empty($erro)){
            //         mysql_query("ROLLBACK");
            //         return "Erro: " . $erro;
            //     }
            // }
            
            // $cSql = mysql_query("call T218_Gravar_Totalizacao_3('{$T218_Id}');");
            // $erro = mysql_error();
            // if(!empty($erro)){
            //     mysql_query("ROLLBACK");
            //     return "Erro: " . $erro;
            // }
            
            // $cSql = mysql_query("call T005_Gravar_Totalizacao_4('{$T005_Id}');");
            // $erro = mysql_error();
            // if(!empty($erro)){
            //     mysql_query("ROLLBACK");
            //     return "Erro: " . $erro;
            // }
        }
        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");
        return array('grupo' => $grupo);
    }

    /**
     * gerarCotacaoEstoqueAutomatica
     *
     * @param string $extra
     * @return string/array
     */
    public function gerarCotacaoEstoqueAutomatica($extra)
    {
        log("entra no gerarCotacaoEstoqueAutomatica");
        global $g;

        mysql_query('SET SESSION group_concat_max_len = 2048');

        $extraOriginal = base64_decode($extra);
        $extra         = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "D009_Flag_Cotar='S' and D009_C004_Id='{$g['empresaAtual']}' AND D009_C007_Id_Comprar = '{$g['usuarioAtual']}'"));
        
        $countErro = 0;
        $arrayD024 = array();
        $sql = "SELECT fornecedor.D024_Id as fornecedorD024_Id, GROUP_CONCAT(DISTINCT D009_Id SEPARATOR ', ') AS D009_Ids, COUNT(DISTINCT D009_Id) AS count FROM D009 
                LEFT JOIN T187 ON T187_D009_Id=D009_Id 
                LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                LEFT JOIN D082 ON D082_Id=D049_D082_Id 
                LEFT JOIN D083 ON D083_D049_Id=D049_Id 
                LEFT JOIN D037 ON D037_Id=D001_D037_Id 
                LEFT JOIN C008 ON C008_Id=D001_C008_Id 
                LEFT JOIN D003 ON D003_Id=D001_D003_Id 
                LEFT JOIN D060 ON D001_D060_Id=D060_Id 
                LEFT JOIN D002 ON D001_D002_Id=D002_Id 
                LEFT JOIN D015 ON D001_D015_Id=D015_Id 
                LEFT JOIN T001 ON T001_Id=D009_T001_Id
                left join D024 as ultimaCompra on ultimaCompra.D024_Id = T001_D024_Id 
                LEFT JOIN D041 ON D041_D049_Id=D049_Id
                LEFT JOIN D005 ON D005_Id=D001_D005_Id
                LEFT JOIN C007 ON C007_Id=D009_C007_Id_Comprar
                LEFT JOIN T241 on T241_D001_Id=D001_Id
                LEFT JOIN D024 as fornecedor on fornecedor.D024_Id=T241_D024_Id
			    LEFT JOIN D009A ON D009A_D009_Id = D009_Id
                WHERE {$extra}";
        $sql = explode("LIMIT", $sql);
        $sql = $sql[0];
        $sql .= " GROUP BY fornecedor.D024_Id";

        $retorno = mysql_query($sql);
        while($row = mysql_fetch_array($retorno)){
            if(empty($row['fornecedorD024_Id']) || $row['fornecedorD024_Id'] == 0){
                $countErro = $row['count'];
            }
            $arrayD024[$row['fornecedorD024_Id']] = $row['D009_Ids'];
        }

        if($countErro > 0){
            if($countErro == 1){
                return "Existe um produto sem fornecedor.";
            } else {
                return "Existem {$countErro} produtos sem fornecedor.";
            }
        }

        $arrayGrupo = array();

        $sqlT218 = mysql_query("SELECT T218_Grupo FROM T218 ORDER BY T218_Grupo DESC LIMIT 1");
        $resT218 = mysql_fetch_array($sqlT218);
        $grupo = $resT218['T218_Grupo'] + 1;


        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        foreach ($arrayD024 as $D024_Id => $D009_Ids) {
            $D013_Id = 0;
            $sqlD013 = mysql_query("SELECT T218_D013_Id FROM T218 WHERE T218_C007_Id_Vendedor_Interno = '{$g['usuarioAtual']}' AND T218_D024_Id = '{$D024_Id}' AND T218_D013_Id > 0 ORDER BY T218_Id DESC LIMIT 1");
            if(mysql_num_rows($sqlD013) == 1){
                $rowD013 = mysql_fetch_array($sqlD013);
                $D013_Id = $rowD013['T218_D013_Id'];
            }
            $calcularST = ($g['C031']['comprasCalcularST'] == 'S') ? 'S' : 'N';
            $calcularIPI = ($g['C031']['comprasCalcularIPI'] == 'S') ? 'S' : 'N';
            $calcularICMS = ($g['C031']['comprasCalcularICMS'] == 'S') ? 'S' : 'N';
            mysql_query("INSERT INTO T218 (T218_D024_Id, T218_C004_Id, T218_D013_Id, T218_C007_Id_Vendedor_Interno, T218_Data_Emissao, T218_Flag_Acp, T218_Grupo, T218_Flag_Calcular_ST, T218_Flag_Calcular_IPI, T218_Flag_Calcular_ICMS) VALUES ('{$D024_Id}', '{$g['empresaAtual']}', '{$D013_Id}', '{$g['usuarioAtual']}', CURDATE(), '6', '{$grupo}', '{$calcularST}', '{$calcularIPI}', '{$calcularICMS}')");
            $erro = mysql_error();
            if(!empty($erro)){
                mysql_query("ROLLBACK");
                return "Erro: " . $erro;
            }            
            $T218_Id = $g['mysqlLastId'];

            $hora  = date("H:i:s");
            $T218A = mysql_query("INSERT INTO T218A(T218A_T218_Id,T218A_Hora_Emissao)VALUES('{$T218_Id}','{$hora}')");
            $erro  = mysql_error();
            if(!empty($erro)){
                mysql_query("ROLLBACK");
                return "Erro: " . $erro;
            }

            $sql = <<<EOT
                SELECT 
                D001_Codigo_Produto,
                D001_Descricao_Produto,
                D009_Quantidade_Comprar_Calculada,
                D009_Quantidade_Comprar,
                D009_Id,
                D041_Valor_Base,
                D009_C004_Id,
                D005_Id,
                D083_Codigo_Produto_Fornecedor
                from D009 
                LEFT JOIN T187 ON T187_D009_Id=D009_Id 
                LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                LEFT JOIN D082 ON D082_Id=D049_D082_Id 
                LEFT JOIN D037 ON D037_Id=D001_D037_Id 
                LEFT JOIN C008 ON C008_Id=D001_C008_Id 
                LEFT JOIN D003 ON D003_Id=D001_D003_Id 
                LEFT JOIN D060 ON D001_D060_Id=D060_Id 
                LEFT JOIN D002 ON D001_D002_Id=D002_Id 
                LEFT JOIN D015 ON D001_D015_Id=D015_Id 
                LEFT JOIN T001 ON T001_Id=D009_T001_Id 
                LEFT JOIN D024 ON D024_Id=T001_D024_Id
                LEFT JOIN D041 ON D041_D049_Id=D049_Id
                LEFT JOIN D005 ON D005_Id=D001_D005_Id
                LEFT JOIN C007 ON C007_Id=D009_C007_Id_Comprar
                LEFT JOIN D083 ON D001_Id=D083_D001_Id AND D083_D024_Id = '{$D024_Id}'
                WHERE D009_Id IN ({$D009_Ids})
                GROUP BY D009_Id
EOT;
            $retorno = mysql_query($sql);
            $erro  = mysql_error();
            if(!empty($erro)){
                mysql_query("ROLLBACK");
                return "Erro: " . $erro;
            }

            $i = 1;
            while ($row = mysql_fetch_array($retorno)) {
                $QuantidadeComprar = ($row['D009_Quantidade_Comprar'] > 0) ? $row['D009_Quantidade_Comprar'] : $row['D009_Quantidade_Comprar_Calculada'];
                if($QuantidadeComprar < 0){
                    $QuantidadeComprar = 0;
                }

                $row['D009_C004_Id'] = mysql_real_escape_string($row['D009_C004_Id']);
                $row['D001_Codigo_Produto'] = mysql_real_escape_string($row['D001_Codigo_Produto']);
                $row['D083_Codigo_Produto_Fornecedor'] = mysql_real_escape_string($row['D083_Codigo_Produto_Fornecedor']);
                $row['D001_Descricao_Produto'] = mysql_real_escape_string($row['D001_Descricao_Produto']);
                $row['D041_Valor_Base'] = mysql_real_escape_string($row['D041_Valor_Base']);
                $row['D009_Id'] = mysql_real_escape_string($row['D009_Id']);
                $row['D037_Id'] = mysql_real_escape_string($row['D037_Id']);
                $row['D005_Id'] = mysql_real_escape_string($row['D005_Id']);

                $sqlInsert = "insert into T219 ";
                $sqlInsert .= "(T219_T218_Id, ";
                $sqlInsert .= "T219_C004_Id, ";
                $sqlInsert .= "T219_Codigo_Produto, ";
                $sqlInsert .= "T219_Codigo_Substituto, ";
                $sqlInsert .= "T219_Descricao_Produto, ";
                $sqlInsert .= "T219_Quantidade, ";
                $sqlInsert .= "T219_Valor_Custo_Unitario,  ";
                $sqlInsert .= "T219_D009_Id, ";
                $sqlInsert .= "T219_Item, ";
                $sqlInsert .= "T219_D037_Id, ";
                $sqlInsert .= "T219_D005_Id) ";
                $sqlInsert .= "values  ";
                $sqlInsert .= "('{$T218_Id}', ";
                $sqlInsert .= "'{$row['D009_C004_Id']}', ";
                $sqlInsert .= "'{$row['D001_Codigo_Produto']}', ";
                $sqlInsert .= "'{$row['D083_Codigo_Produto_Fornecedor']}', ";
                $sqlInsert .= "'{$row['D001_Descricao_Produto']}', ";
                $sqlInsert .= "'{$QuantidadeComprar}', ";
                $sqlInsert .= "'{$row['D041_Valor_Base']}', ";
                $sqlInsert .= "'{$row['D009_Id']}', ";
                $sqlInsert .= "'{$i}', ";
                $sqlInsert .= "'{$row['D037_Id']}', ";
                $sqlInsert .= "'{$row['D005_Id']}' ";
                $sqlInsert .= "); ";

                mysql_query($sqlInsert);
                $erro = mysql_error();
                if(!empty($erro)){
                    mysql_query("ROLLBACK");
                    return "Erro: " . $erro;
                }
                $i++;
                mysql_query("UPDATE D009 SET D009_Flag_Cotar = 'N', D009_C007_Id_Comprar = '0' WHERE D009_Id = '{$row['D009_Id']}'");
            }
            //esses métodos agora são realizados em etapa no grid - Lígia Nakazato: 22/08/2018
            // $retorno = $this->est015atualizarCFOP($T218_Id);
            // $retorno = $this->atualizarAliquotas($T218_Id, $g['empresaAtual']);
            
            // $retorno = mysql_query("select T219_Id from T219 where T219_T218_Id='{$T218_Id}'");
            // $erro = mysql_error();
            // if(!empty($erro)){
            //     mysql_query("ROLLBACK");
            //     return "Erro: " . $erro;
            // }
            
            // while ($row = mysql_fetch_array($retorno)) {
            //     $cSql = mysql_query("call T219_Gravar_Totalizacao('{$row['T219_Id']}');");
            //     $erro = mysql_error();
            //     if(!empty($erro)){
            //         mysql_query("ROLLBACK");
            //         return "Erro: " . $erro;
            //     }
            // }
            
            // $cSql = mysql_query("call T218_Gravar_Totalizacao_3('{$T218_Id}');");
            // $erro = mysql_error();
            // if(!empty($erro)){
            //     mysql_query("ROLLBACK");
            //     return "Erro: " . $erro;
            // }

        }
        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");
        return array('grupo' => $grupo);
    }

    /**
     * verificaDadosCotacao
     *
     * @param String $extra
     * @return String/Bool
     */ 
    public function verificaDadosCotacao($extra)
    {
        global $g;
        $retorno = '';
        $origensValidas = array(0,1,2,3,4,5,6,7,8);

        $sql = mysql_query("SELECT T219_Codigo_Produto, IFNULL(T219_D006_Id,0) AS T219_D006_Id, IFNULL(T219_D005_Id,0) AS T219_D005_Id, T219_Flag_Origem_Produto FROM T219 WHERE {$extra}");
        while($row = mysql_fetch_array($sql)){
            $temp = '';
            if($row['T219_D006_Id'] <= 0){
                $temp .= "<b>".$row['T219_Codigo_Produto'] . "</b> está com a CFOP em branco";
            }
            if($row['T219_D005_Id'] <= 0){
                $temp .= ($temp == '') ? "<b>".$row['T219_Codigo_Produto'] . "</b> está com a NCM em branco" : ", com a NCM em branco";
            }
            if(!is_numeric($row['T219_Flag_Origem_Produto']) || $row['T219_Flag_Origem_Produto'] == '' || !in_array($row['T219_Flag_Origem_Produto'], $origensValidas)){
                $temp .= ($temp == '') ? "<b>".$row['T219_Codigo_Produto'] . "</b> a origem da mercadoria não é valida " : ", e a origem da mercadoria não é valida ";
                $temp .= "(origens válidas ".implode(", ", $origensValidas).")";
            }
            $temp .= ($temp != '') ? '.<br>' : '';
            if($temp != ''){
                $retorno .= $temp;
            }
        }
        
        if($retorno != ''){
            return $retorno;
        }
        return true;
    }

     /**
     * pesquisaGridItemCotacao
     *
     * @param array  $mT218
     * @param  int   $T218_Id
     * @return array
     */
    public function pesquisaGridItemCotacao($T218_Id, $mT218 = array())
    {
        global $g;

        // Campo: Procura_Produto
		//configuração global de separar produto por empresa
		$filtro = '';
		if($g['C031']['SeparaProdutoPorEmpresa']=='S'){
			$filtro = "AND IF(D001_C004_Id > 0, D001_C004_Id=".$g['empresaAtual'].", (1=1))";
		}else{
			$filtro = '';
		}
	
		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id  
			left join D041 on D041_D049_Id=D049_Id  
			left join D009 on D009_D049_Id=D049_Id  
			left join D082 on D082_Id=D049_D082_Id  
			left join D037 on D037_Id=D001_D037_Id   
			left join D060 on D060_Id=D001_D060_Id
			left join C008 on C008_Id=D001_C008_Id
			left join D005 on D005_Id=D001_D005_Id
			left join T066 on T066_D009_Id=D009_Id
			left join D004 on D004_Id=T066_D004_Id
			LEFT JOIN D083 on D083_D001_Id=D001_Id AND D083_D024_Id={$mT218['T218_D024_Id']}
			LEFT JOIN D144 ON D144_D049_Id=D049_Id  
			left join C007 on C007_Id=D009_C007_Id_Comprador
			where (D009_C004_Id = '{$g['empresaAtual']}'  and D001_Flag_Ativo='S' and ifnull(D049_Modelo,'')!='0' and ifnull(D049_Flag_Ativo,'')!='N')  
			{$filtro}
			group by D009_Id  
			order by D001_Codigo_Produto ASC
EOT;

		$pesquisaGrid = array(
			'D001',																											              // tabela
			'D009_Id,D144_Id',																										      // chave primaria
			'D009_Id||C008_Id||D060_Id||foto||D001_Codigo_Produto||D049_Modelo||D083_Codigo_Produto_Fornecedor||D001_Descricao_Produto||D001_Aplicacao||substr(D082_Marca,1,15) as D082_Marca||D037_Unidade||concat(D005_Classificacao_Fiscal,"-",D005_Flag_Aplicacao) as D005_Classificacao_Fiscal||D009_Quantidade_Estoque_Real||D009_Quantidade_Estoque_Liquido as Pedido||(select sum(T066_Quantidade_Estoque) from T066 left join D004 on D004_Id=T066_D004_Id  left join D146 ON D146_Id = D004_D146_Id where T066_D009_Id=D009_Id and UPPER(D146_Descricao)="LOJA") as Estoque||D009_Quantidade_Estoque_Fora||T066_Quantidade_Estoque||D009_Quantidade_OC||D004_Local||substr(C007_Primeiro_Nome,1,10) as C007_Primeiro_Nome||D001_Especificacoes||D001_Descricao_Ingles||D049_Id||D001_Flag_Pre_Cadastro||D144_Id||D060_Descricao_Grupo||D001_Id||D009_Quantidade_Estoque_Tabela(D009_Id)',	// campos
			$pesquisaGridExtra,																								              // extras sql
			'Pesquisa de Produto',																										  // título da janela
			'Id,Dep,Setor,Foto,Código,Sub-item,Cód.Cli,Descrição,Referência,Marca,UN,NCM,Estoque Real,Estoque Líq,Est. Loja,Estoque Fora,Estoque Locação,OC,Locação,Comprador,Observações,Descrição Inglês,D049_Id,D001_Flag_Pre_Cadastro,D144_Id,Setor,T003_Id,Est.Tab',	  // descrição dos campos
			'est015CampoProcuraProduto',																					              // callback js
			$g['divId'],																									              // divId
			array((isset($mT218['est015FormProdutosForm']) ? $mT218['est015FormProdutosForm'] : $T218_Id), $mT218['T218_D024_Id']),       // parametro extra
			array(																											              // parametros para cada campo do grid
				array('chavePrimaria' => true, 'colunaExibir' => false),
				array('colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
				array('colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
				array('colunaTipo' => 'livre', 'callback' => 'mostrarImagemProdutoPequena', 'colunaExibir' => false, 'style' => 'width:0px'),				
				array('colunaAlinhar' => 'center', 'style' => 'width:1%;font-weight:bold'),
				array('colunaAlinhar' => 'left', 'style' => 'width:1%;font-weight:bold'),
				array('colunaAlinhar' => 'center', 'style' => 'width:1%'),
				array('style' => 'font-weight:bold;white-space:normal;'),
				array('colunaAlinhar' => 'left', 'style' => 'width:1%'),
				array('style' => 'width:1%'),
				array('style' => 'width:1%'),
				array('style' => 'width:1%'),
				array('callback' => 'validaCampoEstoque', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold', 'colunaExibir' => false),
				array('callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%'),
				array('style' => 'width: 1%;', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 2),
				array('callback' => 'estoqueFora', 'style' => 'width: 1%;', 'colunaAlinhar' => 'right'),
				array('colunaAlinhar' => 'center', 'colunaCasasDecimais' => 2, 'colunaExibir' => false),
				array('colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%'),
				array('style' => 'width:1%'),
				array('style' => 'width:1%'),
				array('colunaExibir' => false),
				array('colunaExibir' => false),
				array('colunaExibir' => false),
				array('colunaExibir' => false),
				array('chavePrimaria' => true, 'colunaExibir' => false),
				array('colunaExibir' => false),
				array('colunaExibir' => false),
				array('colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Tabelas Fornecedor', '/cad/cad002/tab/cad002TabTabelaFornVenda/', 'D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%','colunaExibir'=>false),
			),
			'D009_Id||D049_Id||foto||D009_Preco_1||D009_Preco_2||D009_Preco_3||D009_Quantidade_Estoque||D009_Quantidade_Estoque_Liquido||D144_Id||C008_Id||D060_Id||D009_Quantidade_Estoque_Real||D009_Quantidade_Estoque_Fora||T066_Quantidade_Estoque||D009_Quantidade_OC||D001_Flag_Pre_Cadastro||D144_Id||D001_Id||D009_Quantidade_Estoque_Tabela(D009_Id)||D004_Local||D049_Modelo', // Campos que são ignorados na cláusula where do pesquisaGrid
			null,
			null,
			null,
			null,
			null,
			'D001_Descricao_Produto,Descricao,texto||D001_Codigo_Produto,Codigo,texto||D009_Quantidade_Estoque_Liquido,Estoque,numero', // Paramentro para montar os filtros
		);

        return $pesquisaGrid;
    }


    /**
     * adicionarProdutosOrdemCompra
     *
     * @param String $T224_Id
     * @param String $listaT219
     * @return String/Bool
     */
    public function adicionarProdutosOrdemCompra($T224_Id, $listaT219)
    {
        global $g;

        $sqlT224 = mysql_query("SELECT * FROM T224 WHERE T224_Id = '{$T224_Id}'");
        $rowT224 = mysql_fetch_array($sqlT224);

        if($rowT224['T224_Flag_Status'] != '' && $rowT224['T224_Flag_Status'] != '0' && $rowT224['T224_Flag_Status'] != '2'){
            return "A ordem de compra informada deve estar pendente para adicionar novos produtos";
        }

        $sqlFornecedor = mysql_query("SELECT DISTINCT T218_D024_Id FROM T219 LEFT JOIN T218 ON T218_Id=T219_T218_Id WHERE T219_Id IN({$listaT219})");
        if(mysql_num_rows($sqlFornecedor) > 1){
            return "Foram selecionados produtos de fornecedores diferentes";
        } else {
            $rowT218 = mysql_fetch_array($sqlFornecedor);
            if($rowT218['T218_D024_Id'] != $rowT224['T224_D024_Id']){
                return "O fornecedor da ordem de compra informada é diferente do fornecedor dos produtos cotados";
            }
        }

        $sqlPreCadastro  = mysql_query("SELECT T219_Id 
                                       FROM T219 
                                  LEFT JOIN T055 on T055_D009_Id=T219_D009_Id AND T055_T075_Id=T219_T075_Id
                                  LEFT JOIN D009 on D009_Id=T219_D009_Id 
                                  LEFT JOIN D049 on D049_Id=D009_D049_Id
                                  LEFT JOIN D001 on D001_Id=D049_D001_Id
                                  LEFT JOIN T225 on T225_T219_Id=T219_Id 
                                      WHERE T219_Flag_Comprar='S'
                                        AND D001_Flag_Pre_Cadastro='S'
                                        AND T219_Id IN({$listaT219})");
        $rowsPreCadastro = mysql_num_rows($sqlPreCadastro);

        if ($rowsPreCadastro > 0 && $g['C031']['permiteOrdemCompraProdutoPreCadastro'] == 'N') {
            return "Não é possível gerar OC para itens em pré-cadastro";
        }

        //Verifica se há itens da cotação sem CFOP, NCM ou com origem em branco
        $retornoRestricao = $this->verificaDadosCotacao("T219_Id IN({$listaT219}) AND T219_Flag_Comprar='S'");
        if($retornoRestricao !== true){
            return $retornoRestricao;
        }  

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        //inserir na T225 todos da T219 selecionados
        $sql = <<<EOT
            SELECT * FROM T219 
            LEFT JOIN T225 ON T225_T219_Id=T219_Id 
            LEFT JOIN D009 ON D009_Id=T219_D009_Id
            LEFT JOIN D005 on D005_Id=T219_D005_Id
            WHERE T219_Quantidade>0 and T219_Flag_Comprar='S' AND T219_Id IN({$listaT219})
EOT;
        $retornoConsulta = mysql_query($sql);
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return '8 Erro: ' . mysql_error();
        }

        // variavel que soma quantos itens do orçamento foram inseridos no pedido
        $countConfirmados = 0;
        $restricaoProduto = '';
        while ($linha = mysql_fetch_assoc($retornoConsulta)) {
            $linha['T219_Valor_Preco_Original'] = ($linha['T219_Valor_Preco_Original'] > 0 ? $linha['T219_Valor_Preco_Original'] : $linha['T219_Valor_Preco_Sem_Desconto_Unitario']);
            $countConfirmados++;
            $sql  = "INSERT INTO T225 (T225_Codigo_Substituto,
                                      T225_Flag_Status,
                                      T225_Item,
                                      T225_T219_Id,
                                      T225_D037_Id,
                                      T225_D005_Id,
                                      T225_D006_Id,
                                      T225_Classificacao_Fiscal,
                                      T225_D009_Id,
                                      T225_Codigo_Produto,
                                      T225_Marca,
                                      T225_Descricao_Produto,
                                      T225_Quantidade,
                                      T225_Valor_Preco_Bruto,
                                      T225_Valor_Custo_Unitario,
                                      T225_Valor_Preco_Unitario,
                                      T225_Valor_Preco_Sem_Desconto_Unitario,
                                      T225_Valor_Preco_Original_Bruto,
                                      T225_Percentual_Desconto_Bruto,
                                      T225_IPV,
                                      T225_T224_Id,
                                      T225_C004_Id,
                                      T225_Numero_Pedido_Compra,
                                      T225_Item_Pedido_Compra,
                                      T225_Aliquota_IPI,
                                      T225_Aliquota_ICMS,
                                      T225_Percentual_Reducao_ICMS,
                                      T225_Percentual_Reducao_ICMS_ST,
                                      T225_ST_VA,
                                      T225_Flag_ST,
                                      T225_Flag_Situacao_Tributaria,
                                      T225_ICMS_Inter_Estadual,
                                      T225_ST_ICMS_Interno,
                                      T225_Flag_Isento_PIS,
                                      T225_Aliquota_PIS,
                                      T225_Situacao_Tributaria_PIS,
                                      T225_Flag_Isento_COFINS,
                                      T225_Aliquota_COFINS,
                                      T225_Flag_Preco_Especial,
                                      T225_Situacao_Tributaria_COFINS,
                                      T225_Situacao_Tributaria_IPI,
                                      T225_D059_Id,
                                      T225_Valor_Preco_Original,
                                      T225_Percentual_Desconto,
                                      T225_Liberou_Alcada_Saldo_Futuro,
                                      T225_Quantidade_Estoque,
                                      T225_T075_Id,
                                      T225_T206_Id,
                                      T225_Cubagem_Unitaria,
                                      T225_Total_Cubagem,
                                      T225_Peso_Bruto,
                                      T225_Peso_Bruto_Total,
                                      T225_Peso_Unitario,
                                      T225_Peso_Total,
                                      T225_Valor_Trib_Transparencia,
                                      T225_Percentual_Trib_Transparencia,
                                      T225_Flag_Pre_Cadastro,
                                      T225_D144_Id,
                                      T225_Observacao_Entrega,
                                      T225_Observacao,
                                      T225_Flag_Origem_Produto,
                                      T225_Valor_PReco_Venda_Negociado,
                                      T225_Valor_Preco_Venda,
                                      T225_Percentual_Frete,
                                      T225_Percentual_Financeiro,
                                      T225_D006_Id_Info,
                                      T225_D005_Id_Info,
                                      T225_Valor_ICMS_Substituicao_Retencao,
                                      T225_Percentual_ST
                                      )
                                  VALUES
                                     ('" . mysql_real_escape_string($linha['T219_Codigo_Substituto']) . "',
                                      '',
                                      '" . mysql_real_escape_string($linha['T219_Item']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Id']) . "',
                                      '" . mysql_real_escape_string($linha['T219_D037_Id']) . "',
                                      '" . mysql_real_escape_string($linha['T219_D005_Id']) . "',
                                      '" . mysql_real_escape_string($linha['T219_D006_Id']) . "',
                                      '" . mysql_real_escape_string($linha['D005_Classificacao_Fiscal']) . "',
                                      '" . mysql_real_escape_string($linha['T219_D009_Id']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Codigo_Produto']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Marca']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Descricao_Produto']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Quantidade']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Valor_Preco_Bruto']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Valor_Custo_Unitario']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Valor_Preco_Unitario']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Valor_Preco_Unitario']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Valor_Preco_Original_Bruto']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Percentual_Desconto_Bruto']) . "',
                                      '" . mysql_real_escape_string($linha['T219_IPV']) . "',
                                      '" . mysql_real_escape_string($T224_Id) . "',
                                      '" . mysql_real_escape_string($linha['T219_C004_Id']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Numero_Pedido_Compra']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Item_Pedido_Compra']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Aliquota_IPI']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Aliquota_ICMS']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Percentual_Reducao_ICMS']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Percentual_Reducao_ICMS_ST']) . "',
                                      '" . mysql_real_escape_string($linha['T219_ST_VA']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Flag_ST']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Flag_Situacao_Tributaria']) . "',
                                      '" . mysql_real_escape_string($linha['T219_ICMS_Inter_Estadual']) . "',
                                      '" . mysql_real_escape_string($linha['T219_ST_ICMS_Interno']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Flag_Isento_PIS']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Aliquota_PIS']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Situacao_Tributaria_PIS']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Flag_Isento_COFINS']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Aliquota_COFINS']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Flag_Preco_Especial']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Situacao_Tributaria_COFINS']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Situacao_Tributaria_IPI']) . "',
                                      '" . mysql_real_escape_string($linha['T219_D059_Id']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Valor_Preco_Original']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Percentual_Desconto']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Liberou_Alcada_Saldo_Futuro']) . "',
                                      '" . mysql_real_escape_string($linha['D009_Quantidade_Estoque_Liquido']) . "',
                                      '" . mysql_real_escape_string($linha['T219_T075_Id']) . "',
                                      '" . mysql_real_escape_string($linha['T219_T206_Id']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Cubagem_Unitaria']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Total_Cubagem']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Peso_Bruto']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Peso_Bruto_Total']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Peso_Unitario']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Peso_Total']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Valor_Trib_Transparencia']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Percentual_Trib_Transparencia']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Flag_Pre_Cadastro']) . "',
                                      '" . mysql_real_escape_string($linha['T219_D144_Id']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Observacao_Entrega']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Observacao']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Flag_Origem_Produto']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Valor_Preco_Venda_Negociado']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Valor_Preco_Venda']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Percentual_Frete']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Percentual_Financeiro']) . "',
                                      '" . mysql_real_escape_string($linha['T219_D006_Id_Info']) . "',
                                      '" . mysql_real_escape_string($linha['T219_D005_Id_Info']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Valor_ST']) . "',
                                      '" . mysql_real_escape_string($linha['T219_Percentual_ST']) . "'
                                      )";
            $erro = mysql_error();
            //return $sql;
            $ret  = mysql_query($sql);
            log($sql);
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return '9 Erro: ' . $erro;
            }
            
            $T225_Id = $g['mysqlLastId'];
            mysql_query("UPDATE T219 set T219_Flag_Comprar='N' where T219_Id='{$linha['T219_Id']}'");
            
            mysql_query("CALL T225_Gravar_Totalizacao('$T225_Id')");
            
            log("call T225_Gravar_Totalizacao('$T225_Id')");
            $erro = mysql_error();
            if (!empty($erro)) {
                log($erro);
                mysql_query("ROLLBACK");
                return '10 Erro: ' . $erro;
            }
        }            

        /* Snapshot da Reforma Tributária da cotação. Esta cópia em lote não
         * consulta nem recalcula cada item da OC; o cálculo próprio da OC é da Task 07. */
        $sqlSnapshotItens = "INSERT INTO T225A (
                T225A_T225_Id,
                T225A_Situacao_Tributaria_CBS_IBS,
                T225A_Classificacao_Tributaria_CBS_IBS,
                T225A_Situacao_Tributaria_CBS_IBS_Regular,
                T225A_Classificacao_Tributaria_CBS_IBS_Regular,
                T225A_Aliquota_CBS,
                T225A_Percentual_Reducao_CBS,
                T225A_Aliquota_CBS_Efetiva,
                T225A_Valor_CBS,
                T225A_Percentual_Diferimento_CBS,
                T225A_Valor_Diferimento_CBS,
                T225A_Aliquota_CBS_Regular,
                T225A_Percentual_Reducao_CBS_Regular,
                T225A_Aliquota_CBS_Efetiva_Regular,
                T225A_Valor_CBS_Regular,
                T225A_Aliquota_IBS_Municipal,
                T225A_Percentual_Reducao_IBS_Municipal,
                T225A_Aliquota_IBS_Municipal_Efetiva,
                T225A_Valor_IBS_Municipal,
                T225A_Percentual_Diferimento_IBS_Municipal,
                T225A_Valor_Diferimento_IBS_Municipal,
                T225A_Aliquota_IBS_Municipal_Regular,
                T225A_Percentual_Reducao_IBS_Municipal_Regular,
                T225A_Aliquota_IBS_Municipal_Efetiva_Regular,
                T225A_Valor_IBS_Municipal_Regular,
                T225A_Aliquota_IBS_UF,
                T225A_Percentual_Reducao_IBS_UF,
                T225A_Aliquota_IBS_UF_Efetiva,
                T225A_Valor_IBS_UF,
                T225A_Percentual_Diferimento_IBS_UF,
                T225A_Valor_Diferimento_IBS_UF,
                T225A_Aliquota_IBS_UF_Regular,
                T225A_Percentual_Reducao_IBS_UF_Regular,
                T225A_Aliquota_IBS_UF_Efetiva_Regular,
                T225A_Valor_IBS_UF_Regular,
                T225A_Valor_Base_CBS_IBS,
                T225A_Valor_Total_Reforma,
                T225A_Valor_IBS
            )
            SELECT T225_Id,
                T219A_Situacao_Tributaria_CBS_IBS,
                T219A_Classificacao_Tributaria_CBS_IBS,
                T219A_Situacao_Tributaria_CBS_IBS_Regular,
                T219A_Classificacao_Tributaria_CBS_IBS_Regular,
                T219A_Aliquota_CBS,
                T219A_Percentual_Reducao_CBS,
                T219A_Aliquota_CBS_Efetiva,
                T219A_Valor_CBS,
                T219A_Percentual_Diferimento_CBS,
                T219A_Valor_Diferimento_CBS,
                T219A_Aliquota_CBS_Regular,
                T219A_Percentual_Reducao_CBS_Regular,
                T219A_Aliquota_CBS_Efetiva_Regular,
                T219A_Valor_CBS_Regular,
                T219A_Aliquota_IBS_Municipal,
                T219A_Percentual_Reducao_IBS_Municipal,
                T219A_Aliquota_IBS_Municipal_Efetiva,
                T219A_Valor_IBS_Municipal,
                T219A_Percentual_Diferimento_IBS_Municipal,
                T219A_Valor_Diferimento_IBS_Municipal,
                T219A_Aliquota_IBS_Municipal_Regular,
                T219A_Percentual_Reducao_IBS_Municipal_Regular,
                T219A_Aliquota_IBS_Municipal_Efetiva_Regular,
                T219A_Valor_IBS_Municipal_Regular,
                T219A_Aliquota_IBS_UF,
                T219A_Percentual_Reducao_IBS_UF,
                T219A_Aliquota_IBS_UF_Efetiva,
                T219A_Valor_IBS_UF,
                T219A_Percentual_Diferimento_IBS_UF,
                T219A_Valor_Diferimento_IBS_UF,
                T219A_Aliquota_IBS_UF_Regular,
                T219A_Percentual_Reducao_IBS_UF_Regular,
                T219A_Aliquota_IBS_UF_Efetiva_Regular,
                T219A_Valor_IBS_UF_Regular,
                T219A_Valor_Base_CBS_IBS,
                T219A_Valor_Total_Reforma,
                T219A_Valor_IBS
              FROM T225
        INNER JOIN T219A ON T219A_T219_Id=T225_T219_Id
             WHERE T225_T224_Id='{$T224_Id}' AND T225_T219_Id IN({$listaT219})";
        mysql_query($sqlSnapshotItens);
        if ($erro = mysql_error()) { mysql_query("ROLLBACK"); return 'Erro ao copiar snapshot da Reforma para itens da OC: ' . $erro; }

        mysql_query("call T224_Gravar_Totalizacao_3($T224_Id)");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return '11 Erro: ' . mysql_error();
        }
        
        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");
        
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();
        $sqlT225 = mysql_query("SELECT T225_D009_Id FROM T225 WHERE T225_T224_Id = '{$T224_Id}'");
        while($T225 = mysql_fetch_array($sqlT225)){
            $CAD002->reprocessaOCPedidos($T225['T225_D009_Id']);
        }
        return true;
    }

    public function atualizarCodigoFornecedorCotacao($T218_Id)
    {
        global $g;
        $rowT218 = mysql_fetch_array(mysql_query("SELECT * FROM T218 WHERE T218_Id = '{$T218_Id}'"));
        $sqlT219 = mysql_query("SELECT T219_Id, D083_Codigo_Produto_Fornecedor FROM T219 LEFT JOIN D009 ON D009_Id = T219_D009_Id LEFT JOIN D049 ON D049_Id = D009_D049_Id LEFT JOIN D001 ON D001_Id = D049_D001_Id LEFT JOIN D083 ON D083_D001_Id = D001_Id AND D083_D024_Id = '{$rowT218['T218_D024_Id']}' WHERE T219_T218_Id = '{$T218_Id}'");
        while($rowT219 = mysql_fetch_array($sqlT219)){
            if(empty($rowT219['D083_Codigo_Produto_Fornecedor'])) { continue; }
            mysql_query("UPDATE T219 SET T219_Codigo_Substituto = '{$rowT219['D083_Codigo_Produto_Fornecedor']}' WHERE T219_Id = '{$rowT219['T219_Id']}'");
        }
        return true;
    }

    public function est015TriggerAlteraValorItemTabelaFornecedor($Valor_Item_Tabela_Fornecedor, $T219_Id)
    {
        // Caso der erro retorne uma string de erro
        return floatval($Valor_Item_Tabela_Fornecedor);
    }

    public function est015RetornaArrayItensCotacao($QuantidadeComprar, $custoOriginal, $D009_Id)
    {
        /* Se quiser que seja adicionado mais itens, é só adicionar mais arrays
        dentro desse array, se não mantenha 1 para adicionar 1 padrão mesmo */
        return array(
            array("qtd_itens" => $QuantidadeComprar)
        );
    }

    public function est015TriggerPersonalizacaoCriaT219A($ultimoIdInsert, $value)
    {        
        $qT219A = <<<EOT
        insert into T219A
        (T219A_T219_Id)
        values 
        ('{$ultimoIdInsert}');
EOT;
        mysql_query($qT219A); 
        if($erro = mysql_error())
        {
            $erro = "Erro->'$erro' na query '$qT219A' em est015TriggerPersonalizacaoCriaT219A";
            return $erro;
        }
    
        return true;
    }
    
    public function PersonalizacaoTriggerVerificaItemCotacaoEmail($T218_Id){
        return true;
    }

    public function PersonalizacaoTriggerVerificaItemCotacaoImprimir($T218_Id){
        return true;
    }
    
    public function PersonalizacaoTriggerVerificarItemGerarOC($extra){
        return true;
    }
}
