<?php
namespace hardness;
if(empty($this->getErro())){
?>
    <html>
        <head>
            <title><?php echo  $xBoleto->nome_cedente; ?></title>
            <meta http-equiv=Content-Type content=text/html charset=UTF-8>
            <link rel=stylesheet href='style_padrao_2.css' type='text/css'>
        </head>
        <body>
            <?php
            if($this->getPageBreak()){
            ?>
                <p style='page-break-after: always'></p>
            <?php
                }           
            ?>
            <table class="tb-5">
                <tr>
                     <!-- parte esquerda do boleto -->
                    <td class="tb-2">
                        <table class="tb-5">
                            <tr>
                                <td class="w30 pt-5 pb-5" valign=top><img src=<?php echo "imagens/logo".$this->codigo_banco.".jpg"?> width="86" height="30"></td>
                                <td class="w30 pb-5 ta-c cod-b" valign=bottom><?php echo $codigo_banco_com_dv ?></td>
                                <td class="w30"></td>
                            </tr>
                        </table>
                        <table class="tb-5">
                            <tr>
                                <th class="ct-l br bt w100">Vencimento</th>
                            </tr>
                            <tr>
                                <td class="cp-r bl bb br w10-2 p3" valign=top><?php echo $this->data_vencimento?></td>
                            </tr>
                            <tr>
                                <th class="ct-l br w100">Agência/Código Beneficiário</th>
                            </tr>
                            <tr>
                                <td class="cp-r bl bb br w10-2 p3" valign=top><?php echo $this->numero_agencia."-".$this->digito_verificador_agencia." / ". $this->numero_conta ."-". $this->digito_verificador_conta; ?></td>
                            </tr>
                        </table>
                        
                        <!-- Para fazer a divisão em dois campos foi necessário criar uma outra tabela -->
                        <table class="tb-5">
                            <tr>
                                <th class="ct-l w50">Nosso número</th>
                                <th class="ct-l br w50">N° Documento</th>
                            </tr>
                            <tr class="w50">
                                <td class="cp-r bl bb p3" valign=top><?php echo $nossonumero;?></td>
                                <td class="cp-r bl bb br p3" valign=top><?php echo $this->numero_documento?></td>
                            </tr>
                        </table>
                        
                        <table class="tb-5">
                            <tr>
                                <th class="ct-l w50">Espécie</th>
                                <th class="ct-l br w50">Quantidade</th>
                            </tr>
                            <tr>
                                <td class="cp-r bl bb w50 p3" valign=top><?php echo $this->dados_especie;?></td>
                                <td class="cp-r bl bb br w50 p3" valign=top><?php echo $this->dados_quantidade?></td>
                            </tr>
                        </table>

                        <table class="tb-5">
                            <tr>
                                <th class="ct-l br w100">Valor documento</th>
                            </tr>
                            <tr>
                                <td class="cp-r bl br w10-2 p3" valign=top><?php echo number_format($this->valor_duplicata,2,',','.');?></td>
                            </tr>
                            
                            <tr>
                                <th class="ct-l br bt w100">(-) Desconto / Abatimentos</th>
                            </tr>
                            <tr>
                                <td class="cp-r bl bb br w10-2 p3" valign=top><br/></td>
                            </tr>
                            
                            <tr>
                                <th class="ct-l br w100">(-) Outras deduções</th>
                            </tr>
                            <tr>
                                <td class="cp-r bl bb br w10-2 p3" valign=top><br/></td>
                            </tr>
                            
                            <tr>
                                <th class="ct-l br w100">(+) Mora / Multa</th>
                            </tr>
                            <tr>
                                <td class="cp-r bl bb br w10-2 p3" valign=top><br/></td>
                            </tr>
                            
                            <tr>
                                <th class="ct-l br w100">(+) Outros acréscimos</th>
                            </tr>
                            <tr>
                                <td class="cp-r bl bb br w10-2 p3" valign=top><br/></td>
                            </tr>
                            
                            <tr>
                                <th class="ct-l br w100">(=) Valor cobrado</th>
                            </tr>
                            <tr>
                                <td class="cp-r bl bb br w10-2 p3" valign=top><br/></td>
                            </tr>
                            
                            <tr>
                                <th class="ct-l br w100">Pagador</th>
                            </tr>
                            <tr>
                                <td class="cp-l bl bb br w10-2 p3" valign=top height="55"><?php echo $this->nome_sacado?></td>
                            </tr>
                        </table>   
                    </td>
                    <!-- FIM PRIMEIRA PARTE -->
                    <td class="w3"></td>
                    
                    <!-- INÍCIO Terceira parte do boleto -->
                    <td class="tb-3">
                        
                        <!-- Linha digitavel e código de barras -->
                        <table class="tb-5">
                            <tr>
                                <td class="w5 pt-5 pb-5" valign=top><img src=<?php echo "imagens/logo".$this->codigo_banco.".jpg"?> width="86" height="30"></td>
                                <td class="w8 cod-b pb-5 cod-b ta-c" valign=bottom><?php echo $codigo_banco_com_dv ?></td>
                                <td class="w60 pb-5 ta-r ld" valign=bottom><?php echo $linha_digitavel?></td>
                            </tr>
                        </table>
                        
                        <!-- Primeira e segunda linha lado esquerdo -->
                        <table class="tb-5">
                            <tr>
                                <th class="ct-l bl bt w75">Local de Pagamento</th>
                                <th class="ct-l br bt bl w20">Vencimento</th>
                            </tr>
                            <tr>
                                <td class="cp-l bl bb w75 p3 ta-l" valign=top><?php echo $mensagemLocalPagamento?></td> <!--Criar fun??o para mudar esse texto-->
                                <td class="cp-r bl bb br w20 p3 ta-l" valign=top><?php echo $this->data_vencimento?></td>
                            </tr>

                            <tr>
                                <th class="ct-l bl w75">Beneficiário</th>
                                <th class="ct-l br bl w20">Agência/Código Beneficiário</th>
                            </tr>
                            <tr>
                                <td class="cp-l bl bb w75 p3" valign=top><?php echo $this->nome_cedente;?> - <?php echo $this->cpf_cnpj_cedente; ?><br><?php echo $this->endereco_cedente; ?> - <?php echo $this->getEndereco2Cedente() ?></td>
                                <td class="cp-r bl bb br w20 pb-15" valign=top><?php echo $this->numero_agencia."-".$this->digito_verificador_agencia." / ". $this->numero_conta ."-". $this->digito_verificador_conta; ?></td>
                            </tr>
                        </table>   

                        <!-- Terceira e Quarta linha do lado esquerdo -->
                        <table class="tb-5">
                            <tr>
                                <th class="ct-l bl w20">Data do documento</th>
                                <th class="ct-l w14-45">N° documento</th>
                                <th class="ct-l w14-45">Espécie doc.</th>
                                <th class="ct-l w15">Aceite</th>
                                <th class="ct-l w15">Data processamento</th>
                                <th class="ct-l br w55">Nosso número</th>
                            </tr>
                            <tr>
                                <td class="cp-r bl bb w20 p3" valign=top><?php echo $this->getDataEmissaoBoleto();?></td> 
                                <td class="cp-r bl bb w14-45 p3" valign=top><?php echo $this->numero_documento?></td>
                                <td class="cp-r bl bb w14-45 p3" valign=top><?php echo $this->dados_especie_doc;?></td>
                                <td class="cp-r bl bb w15 p3" valign=top><?php echo  $this->dados_aceite; ?></td>
                                <td class="cp-r bl bb w15 p3" valign=top><?php echo $this->data_processamento;?></td>
                                <td class="cp-r bl bb br w55 p3" valign=top><?php echo $nossonumero;?></td>
                            </tr>

                            <tr>
                                <th class="ct-l bl w20" valign=top>Uso do banco</th>
                                <th class="ct-l w14-45 p3" valign=top>Carteira</th>
                                <th class="ct-l w14-45 p3" valign=top>Espécie</th>
                                <th class="ct-l w15" valign=top>Quantidade</th>
                                <th class="ct-l w15" valign=top>Valor Documento</th>
                                <th class="ct-l br w55" valign=top>(=) Valor documento</th>
                            </tr>
                            <tr>
                                <td class="cp-r bl bb w20 p3" valign=top></td>
                                <td class="cp-r bl bb w15 p3" valign=top><?php echo $this->numero_carteira?></td>
                                <td class="cp-r bl bb w14-45 p3" valign=top><?php echo $this->dados_especie?></td>
                                <td class="cp-r bl bb w14-45 p3" valign=top><?php echo $this->dados_quantidade?></td>
                                <td class="cp-r bl bb w15 p3" valign=top><?php echo $this->dados_valor_unitario?></td>
                                <td class="cp-r bl bb br w55 p3" valign=top><?php echo number_format($this->valor_duplicata,2,',','.');?></td>
                            </tr> 
                        </table>

                        <!-- Quinta e Sexta linha do lado esquerdo -->
                        <table class="tb-5">
                            <tr>
                                <th rowspan="1" class="ct-l w75">Instruções (Texto de responsabilidade do beneficiário)</th> 
                                <th rowspan="1" class="ct-l br w32" valign=top>(-) Desconto / Abatimento</th>
                            </tr>
                            <tr><td rowspan="6" class="ct-l bb w75 pt-10" valign=top><?php echo $this->instrucoes1; ?><br><?php echo $this->instrucoes2; ?><br><?php echo $this->instrucoes3; ?><br><?php echo $this->instrucoes4; ?></td></tr>
                            <tr><td rowspan="1" class="cp-l bl bb br p5 w20" valign=top><br/></td></tr>         
                            <tr><th rowspan="1" class="ct-l br w20" valign=top>(-) Outras deduções</th></tr>         
                            <tr><td rowspan="1" class="cp-l bl bb br p5 w20" valign=top><br/></td></tr>
                            <tr><th rowspan="1" class="ct-l br w20" valign=top>(+) Mora / Multa</th></tr>         
                            <tr><td rowspan="1" class="cp-l bl bb br p5 w20" valign=top><br/></td></tr>

                            <tr>
                                <th rowspan="1" class="ct-l w75">Pagador</th> 
                                <th rowspan="1" class="ct-l br w32" valign=top>(+) Outros acréscimos</th>
                            </tr>
                            <tr><td rowspan="6" class="ct-l bb w75 pt-10" valign=top><?php echo $this->nome_sacado.' - '.$this->cpf_cnpj_sacado;?><br><?php echo $this->endereco_sacado;?><br><?php echo $this->getEndereco2Sacado();?></td></tr>
                            <tr><td rowspan="1" class="cp-l bl bb br p5 w20" valign=top><br/></td></tr>         
                            <tr><th rowspan="1" class="ct-l br w20" valign=top>(+) Valor cobrado</th></tr>         
                            <tr><td rowspan="1" class="cp-l bl bb br p5 w20" valign=top><br/></td></tr>
                            <tr><th rowspan="1" class="ct-l br w20" valign=top>Código Baixa</th></tr>         
                            <tr><td rowspan="1" class="cp-l bl bb br p5 w20" valign=top><br/></td></tr>
                            
                        </table>

                        <!-- Monta o código de barras -->
                        <table class="tb-5">
                            <tr>
                                <th class="ct-l nl w60 p5" valign=top>Sacador/Avalista</th>
                                <th class="ta-r nl w30 p5 fwb" valign=top>Autenticação mecânica - Ficha de Compensação</th>
                            </tr>
                            <tr>
                                <td height=50 class="pl-5" colspan="2"><?php fbarcode($codigo_barras); ?></td>
                            </tr>
                        </table>

                    </tr>
                <tr>
            </table>

            <!-- Monta  a linha para ser cortada--> 
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

    


