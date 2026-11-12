<?php
namespace hardness;
if(empty($this->getErro())){
    header ('Content-type: text/html; charset=UTF-8');
?>
    <html>
        <head>
            <title><?php echo  $xBoleto->nome_cedente; ?></title>
            <meta http-equiv=Content-Type content=text/html charset=UTF-8>
            <link rel=stylesheet href='style_padrao.css' type='text/css'>
        </head>
        <body>
            <?php
            if($this->getPageBreak()){
            ?>
                <p style='page-break-after: always'></p>
            <?php
            }
            if($this->getMostrarLogo()){
                ?>
                <p style='page-break-after: always'></p>
                <table class="tb-5">
                    <tr>
                        <td class="w10 pb-15">
                            <img src='<?php echo $this->getArquivoLogo()?>' class="logo pt-5 pr-15"/>       
                        </td>                                             
                        <td>
                            <div class="info-empresa">
                                <?php echo $this->nome_cedente;?><?php echo isset($this->cpf_cnpj_cedente) ? "<br>".$this->cpf_cnpj_cedente : '' ?><br>
                                <?php echo $this->endereco_cedente; ?><br>
                                <?php echo $this->cidade_cedente." / ".$this->estado_cedente; ?><br>
                            </div>
                        </td>
                    </tr>
                </table>
                <?php            }
            ?>
            <!-- Recibo do pagador  -->
            <table class="tb-5">
                <tr>
                    <td class="border-dashed"></td>
                </tr>
                <tr>
                    <td><div class="receipt pb-5">Recibo do Pagador</div></td>
                </tr>
            </table>
            <table class="tb-5">
                <tr>
                    <td class="bb w20 pt-5 pb-5" valign=top><img src=<?php echo "imagens/logo".$this->codigo_banco.".jpg"?> width="140" height="35"></td>
                    <td class="bb w10 cod-b" valign=bottom><?php echo $codigo_banco_com_dv ?></td>
                    <td class="bb w70 ta-r ld" valign=bottom><?php echo $linha_digitavel?></td>
                </tr>
            </table>  
            <table class="tb-5">
                <tr>
                    <th class="ct-l w46-6" valign=top>Beneficiário</th>
                    <th class="ct-l w20" valign=top>Agência/Cód. do Beneficiário</th>
                    <th class="ct-l w5 pr-5" valign=top>Espécie</th>
                    <th class="ct-l w5 pr-5" valign=top>Quantidade</th>
                    <th class="ct-l br w20" valign=top>Nosso número</th>
                </tr>
                <tr>
                    <?php
                    $conta = $this->numero_conta ."-". $this->digito_verificador_conta;
                    $agencia = $this->numero_agencia."-".$this->digito_verificador_agencia;
                    if($codigo_banco_com_dv == '104-0'){
                        $conta = $codigoBeneficiarioCaixa;
                        $agencia       = $this->numero_agencia;
                    } elseif($codigo_banco_com_dv == '756-0'){
                        $codigoClienteSicoob = preg_replace('/\D/', '', $this->codigo_cliente);
                        $codigoCliente       = substr($codigoClienteSicoob, 0, -1);
                        $digitoCodigoCliente = substr($codigoClienteSicoob,-1);
                        $conta               = $codigoCliente.'-'.$digitoCodigoCliente;
                    }elseif($codigo_banco_com_dv == '748-X'){
                        $agencia             = $this->numero_agencia;
                        $postoCopeterativa   = $this->posto_cooperativa;
                        $conta               = $postoCopeterativa.'.'.$this->codigo_cliente;
                    }
                    ?>  
                    <td class="cp-l bl bb w46-6 p5" valign=top><?php echo $this->nome_cedente;?><br><?php echo $this->cpf_cnpj_cedente; ?><br><?php echo $this->endereco_cedente; ?><br><?php echo $this->getEndereco2Cedente() ?></td>
                    <td class="cp-l bl bb w20 p5" valign=top><?php echo $agencia." / ". $conta; ?></td>
                    <td class="cp-l bl bb w5 p5" valign=top><?php echo $this->dados_especie?></td>
                    <td class="cp-l bl bb w5 p5" valign=top><?php echo $this->dados_quantidade?></td>
                    <td class="cp-l bl bb br w20 p5" valign=top><?php echo $nossonumero?></td>
                </tr>
            </table>    
            <table class="tb-5">
                <tr>
                    <th class="ct-l w10" valign=top>Número do documento</th>
                    <th class="ct-l w10" valign=top>Contrato</th>
                    <th class="ct-l w10 pr-5" valign=top>CPF/CEI/CNPJ</th>
                    <th class="ct-l w10-2 pr-5" valign=top>Vencimento</th>
                    <th class="ct-l br w16" valign=top>Valor documento</th>
                </tr>
                <tr>
                    <td class="cp-l bl bb w10 p5" valign=top><?php echo $this->numero_documento?></td>
                    <td class="cp-l bl bb w10 p5" valign=top><?php echo $this->numero_contrato?></td>
                    <td class="cp-l bl bb w10 p5" valign=top><?php echo $this->cpf_cnpj_cedente?></td>
                    <td class="cp-l bl bb w10-2 p5" valign=top><?php echo $this->data_vencimento?></td>
                    <td class="cp-l bl bb br w16 p5" valign=top><?php echo $this->valor_duplicata?></td>
                </tr>
            </table> 
            <table class="tb-5">
                <tr>
                    <th class="ct-l w10" valign=top>(-) Desconto / Abatimentos</th>
                    <th class="ct-l w10" valign=top>(-) Outras deduções</th>
                    <th class="ct-l w10 pr-5" valign=top>(+) Mora / Multa</th>
                    <th class="ct-l w10-2 pr-5" valign=top>(+) Outros acréscimos</th>
                    <th class="ct-l br w15-3" valign=top>(=) Valor cobrado</th>
                </tr>
                <tr>
                    <td class="cp-l bl bb w10 p5" valign=top></td>
                    <td class="cp-l bl bb w10 p5" valign=top></td>
                    <td class="cp-l bl bb w10 p5" valign=top></td>
                    <td class="cp-l bl bb w10-2 p5" valign=top></td>
                    <td class="cp-l bl bb br w15-3 p5" valign=top></td>
                </tr>
            </table>  
            <table class="tb-5">
                <tr>
                    <th class="ct-l br w100" valign=top>Pagador</th>
                </tr>
                <tr>
                    <td class="cp-l bl bb br w10 p5" valign=top><?php echo  $this->nome_sacado.' - '.$this->cpf_cnpj_sacado;?></td>
                </tr>
            </table>
            <table class="tb-5">
                <tr>
                    <th class="ct-l nl br w46-6 p5" valign=top>Demonstrativo</th>
                    <th class="ta-r nl br w46-6 p5" valign=top>Autenticação mecânica</th>
                </tr>
                <tr>
                    <table class="tb-2 p5 pb-30">
                    <?php
                    $T002=mysqli_query("SELECT T002_Numero_Duplicata,T002_Data_Vencimento,T002_Valor_Total FROM T002 WHERE T002_T117_Id='$nT117_Id'");
                    while ($mT002=mysql_fetch_assoc($T002)) {
                        echo "<tr>";
                            echo "<th class='ta-c bt bl bb w10'>Número</th>";
                            echo "<th class='ta-c bt bl bb w10'>Vencimento</th>";
                            echo "<th class='ta-c bt bl br bb w10'>Valor</th>";
                        echo "</tr>";
                        echo "<tr>";
                            echo "<td class='ta-c bb bl w10 nb'>".$mT002['T002_Numero_Duplicata']."</td>";
                            echo "<td class='ta-c bb bl w10 nb'>".gCorrigeData($mT002['T002_Data_Vencimento'])."</td>";
                            echo "<td class='ta-c bb bl br w10 nb'>R$ ".$mT002['T002_Valor_Total']."</td>";
                        echo "</tr>";
                    }
                    ?>
                    </table>
                </tr>
            </table>  
            <table class="tb-5">
                <tr>
                    <th class="ct-l nl br w46-6 p5" valign=top><?php echo $mensagemSacado?></th>
                </tr>
            </table>  
            <table class="tb-5">
                <tr>
                    <td class="border-dashed pb-30"></td>
                </tr>
            </table>
            <!-- Fim do Recibo do Pagador --> 
            
            <!-- In?cio Boleto -->
            <table class="tb-5">
                <tr>
                    <td class="bb w20 pb-5" valign=top><img src=<?php echo "imagens/logo".$this->codigo_banco.".jpg"?> width="140" height="35"></td>
                    <td class="bb w10 cod-b" valign=bottom><?php echo $codigo_banco_com_dv?></td>
                    <td class="bb w70 ta-r ld" valign=bottom><?php echo $linha_digitavel?></td>
                </tr>
            </table>
            <table class="tb-5">
                <tr>
                    <th class="ct-l w72-2" valign=top>Local de Pagamento</th>
                    <th class="ct-l w32 br" valign=top>Vencimento</th>
                </tr>
                <tr>
                    <td class="cp-l bl bl bb w70 p5" valign=top><?php echo $mensagemLocalPagamento?></td> <!--Criar fun??o para mudar esse texto-->
                    <td class="cp-l bl bb br w30 p5" valign=top><?php echo $this->data_vencimento?></td>
                </tr>
            </table>   
            <table class="tb-5">
                <tr>
                    <th class="ct-l w72-2" valign=top>Beneficiário</th>
                    <th class="ct-l w32 br" valign=top>Agência/Código Beneficiário</th>
                </tr>
                <tr>
                    <?php
                    $conta = $this->numero_conta ."-". $this->digito_verificador_conta;
                    $agencia = $this->numero_agencia."-".$this->digito_verificador_agencia;
                    if($codigo_banco_com_dv == '104-0'){
                        $conta = $codigoBeneficiarioCaixa;
                        $agencia = $this->numero_agencia;
                    } elseif($codigo_banco_com_dv == '756-0'){
                        $codigoClienteSicoob = preg_replace('/\D/', '', $this->codigo_cliente);
                        $codigoCliente       = substr($codigoClienteSicoob, 0, -1);
                        $digitoCodigoCliente = substr($codigoClienteSicoob,-1);
                        $conta = $codigoCliente.'-'.$digitoCodigoCliente;
                    }elseif($codigo_banco_com_dv == '748-X'){
                        $agencia             = $this->numero_agencia;
                        $postoCopeterativa   = $this->posto_cooperativa;
                        $conta = $postoCopeterativa.'.'.$this->codigo_cliente;
                    }
                    ?>
                    <td class="cp-l bl bb w70 p5" valign=top><?php echo $this->nome_cedente;?> - <?php echo $this->cpf_cnpj_cedente; ?><br><?php echo $this->endereco_cedente; ?><br><?php echo $this->getEndereco2Cedente() ?></td>
                    <td class="cp-l bl bb br w30 p5" valign=top><?php echo $agencia." / ". $conta; ?></td>
                </tr>
            </table> 
            <table class="tb-5">
                <tr>
                    <th class="ct-l w10-8" valign=top>Data do documento</th>
                    <th class="ct-l w10" valign=top>Nº documento</th>
                    <th class="ct-l w5 pr-5" valign=top>Espécie doc.</th>
                    <th class="ct-l w5 pr-5" valign=top>Aceite</th>
                    <th class="ct-l w16" valign=top>Data processamento</th>
                    <th class="ct-l br w18" valign=top>Nosso número</th>
                </tr>
                <tr>
                    <td class="cp-l bl bb w10-8 p5" valign=top><?php echo $this->getDataEmissaoBoleto();?></td>
                    <td class="cp-l bl bb w10 p5" valign=top><?php echo $this->numero_documento?></td>
                    <td class="cp-l bl bb w5 p5" valign=top><?php echo $this->dados_especie_doc;?></td>
                    <td class="cp-l bl bb w5 p5" valign=top><?php echo  $this->dados_aceite; ?></td>
                    <td class="cp-l bl bb w16 p5" valign=top><?php echo $this->data_processamento;?></td>
                    <td class="cp-l bl bb br w18 pt-5 pb-5" valign=top><?php echo $nossonumero;?></td>
                </tr>
            </table>   
            <table class="tb-5">
                <tr>
                    <th class="ct-l w10-8" valign=top>Uso do banco</th>
                    <th class="ct-l w8" valign=top>Carteira</th>
                    <th class="ct-l w5 pr-5" valign=top>Espécie</th>
                    <th class="ct-l w5 pr-5" valign=top>Quantidade</th>
                    <th class="ct-l w16-6" valign=top>Valor Documento</th>
                    <th class="ct-l br w18" valign=top>(=) Valor documento</th>
                </tr>
                <tr>
                    <td class="cp-l bl bb w10-8 p5" valign=top></td>
                    <td class="cp-l bl bb w8 p5" valign=top><?php echo $this->numero_carteira?></td>
                    <td class="cp-l bl bb w5 p5" valign=top><?php echo $this->dados_especie?></td>
                    <td class="cp-l bl bb w5 p5" valign=top><?php echo $this->dados_quantidade?></td>
                    <td class="cp-l bl bb w16-6 p5" valign=top><?php echo $this->dados_valor_unitario?></td>
                    <td class="cp-l bl bb br w18 p5" valign=top><?php echo number_format($this->valor_duplicata,2,',','.');?></td>
                </tr>
            </table>  
            <table class="tb-5">
                <tr>
                    <th rowspan="1" class="ct-l w72-2">Instruções (Texto de responsabilidade do beneficiário)</th> 
                    <th rowspan="1" class="ct-l br w32" valign=top>(-) Desconto / Abatimento</th>
                </tr>         
                <tr><td rowspan="10" class="ct-l bb w72 pt-10" valign=top><?php echo $this->instrucoes1; ?><br><?php echo $this->instrucoes2; ?><br><?php echo $this->instrucoes3; ?><br><?php echo $this->instrucoes4; ?></td></tr>
                <tr><td rowspan="1" class="cp-l bl bb br p5 w32" valign=top></td></tr>         
                <tr><th rowspan="1" class="ct-l br w32" valign=top>(-) Outras deduções</th></tr>         
                <tr><td rowspan="1" class="cp-l bl bb br p5 w32" valign=top></td></tr>
                <tr><th rowspan="1" class="ct-l br w32" valign=top>(+) Mora / Multa</th></tr>         
                <tr><td rowspan="1" class="cp-l bl bb br p5 w32" valign=top></td></tr>
                <tr><th rowspan="1" class="ct-l br w32" valign=top>(+) Outros acréscimos</th></tr>         
                <tr><td rowspan="1" class="cp-l bl bb br p5 w32" valign=top></td></tr>
                <tr><th rowspan="1" class="ct-l br w32" valign=top>(=) Valor cobrado</th></tr> 
                <tr><td rowspan="1" class="cp-l bl bb br p5 w32" valign=top></td></tr>
            </table>
            <table class="tb-5">
                <tr>
                    <th class="ct-l w72-2" valign=top>Pagador</th>
                    <th class="ct-l w32 br" valign=top>Código Baixa</th>
                </tr>
                <tr>
                    <td class="cp-l bl bb w70 p5" valign=top><?php echo $this->nome_sacado.' - '.$this->cpf_cnpj_sacado;?><br><?php echo $this->endereco_sacado;?><br><?php echo $this->getEndereco2Sacado();?></td>
                    <td class="cp-l bl bb br w30 p5" valign=top></td>
                </tr>
            </table> 
            <table class="tb-5">
                <tr>
                    <?php
                        $mensagem = 'Sacador/Avalista';
                        if($codigo_banco_com_dv == '422-7'){
                        $mensagem = 'Beneficiário final';
                        }
                    ?>
                    <th class="ct-l nl br w46-6 p5" valign=top><?php echo $mensagem?></th>
                    <th class="ta-r nl br w46-6 p5 fwb" valign=top>Autenticação mecânica - Ficha de Compensação</th>
                </tr>
                <tr>
                    <td height=50 class="pl-5"><?php fbarcode($codigo_barras); ?></td>
                </tr>
            </table>     
            <table class="tb-5 pt-10">
                <tr>
                    <td class="border-dashed"></td>
                </tr>
                <tr>
                    <td><div class="receipt pb-5 pr-5">Corte na linha pontilhada</div></td>
                </tr>
            </table>
                <!-- Fim Boleto -->
        </body>
    </html>
<?php
}else{
?>
    <html>
        <head>
            <title><?php echo  $xBoleto->nome_cedente; ?></title>
            <meta http-equiv=Content-Type content=text/html charset=UTF-8>
            <link rel=stylesheet href='style_padrao.css' type='text/css'>
        </head>
        <body>
            <table>
                <tr>
                    <td>
                        <table>
                            <tr>
                                <td>
                                    <fieldset>
                                        <table>
                                            <tr>
                                                <td>
                                                    <?php
                                                    echo $this->getErro();
                                                    ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </fieldset>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
    </html>
<?php
}
?>

    


