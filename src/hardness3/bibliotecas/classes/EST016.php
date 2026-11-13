<?php
namespace hardness;

require_once('bibliotecas/classes/MYTCPDF.PHP');
require_once "old/lib/funcoes.php";

definirClasse('EST016');
class EST016_
{
    /**
     * excluir
     *
     * @param int $T218_Id
     */
    public function excluir($T218_Id)
    {
        $T218_Id = mysql_real_escape_string($T218_Id);
        $sql     = "delete from T218 where T218_Id='$T218_Id'";
        mysql_query($sql);
        return true;
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
     * @param int $T224_Id
     */
    public function montarEmail($T224_Id)
    {
        global $g, $confUsuario;
        $T224_Id = mysql_real_escape_string($T224_Id);
        $sql     = "SELECT * FROM T224 LEFT JOIN D013 ON D013_Id=T224_D013_Id WHERE T224_Id='{$T224_Id}'";
        $T224    = mysql_query($sql);
        $T224    = mysql_fetch_array($T224);
        
        $sql  = "SELECT D024_E_Mail FROM D024 WHERE D024_Id='{$T224['T224_D024_Id']}'";
        $D024 = mysql_query($sql);
        $D024 = mysql_fetch_array($D024);
        
        $array = array();
        
        $array['Para']    = (!empty($T224['D013_E_Mail'])) ? $T224['D013_E_Mail'] : $D024['D024_E_Mail'];
        $array['Assunto'] = "Ordem de Compra Nº {$T224_Id}";
        
        
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
        } else {
            $caminhoImagem = "";
        }
        
        $dir = $g['pathDados'] . 'tmp/';
        is_dir($dir) or mkdir($dir, 0777, true);
        $filename = "ordem-compra-{$T224_Id}.pdf";
        $file     = $dir . $filename;
        $fileFull = $g['pathRaiz'] . '/' . $file;
        
        $r_link = '/est/est016/form_func-ajax/est016ImprimirPedido/?ajax=true&gerarHtml=1&T224_Id=' . $T224_Id . '&alcadaUpdate=' . $alcadaUpdate;
        $r_link = str_replace(' ', '+', $r_link);
        $comandoPdf = "xvfb-run --auto-servernum -s \"-screen 0 640x480x16\" wkhtmltopdf --quiet --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 \"{$g['urlRaiz']}{$r_link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"";
        exec($comandoPdf . ' 2>&1', $saidaPdf, $codigoPdf);
        
        $array['Anexos'] = ($codigoPdf === 0 && is_file($fileFull) && filesize($fileFull) > 0) ? array($fileFull) : array();
        $array['ErroAnexo'] = empty($array['Anexos'])
            ? 'Não foi possível gerar o PDF da ordem de compra. Código: ' . $codigoPdf . '. ' . trim(implode(' ', $saidaPdf))
            : '';
        $array['AnexosWeb'] = array(
            '/hardness3/' . $file
        );
        
        $array['Corpo']   = <<<EOT
            <!--<img src='{$caminhoImagem}' style='margin-left:10px'><br /><br /><br /><br />-->
            <font style='font-size:16px'>Segue nossa ordem de Compra <b>$T224_Id</b> em anexo.</font><br /><br /><br />
EOT;
        $array['D024_Id'] = $T224['T224_D024_Id'];
        return $array;
    }

    /**
     * Envia ao fornecedor um lembrete da data de entrega da ordem de compra.
     * O controle de um único envio é feito em T224A_Data_Hora_Email.
     */
    public function enviarEmailLembreteEntrega($T224_Id, $dataEntrega, $avisoAtraso = false)
    {
        global $g;

        require_once('bibliotecas/phpmailer/class.phpmailer.php');
        $T224_Id = (int) $T224_Id;
        $monta = $this->montarEmail($T224_Id);
        if (empty($monta['Para'])) {
            return 'E-mail do fornecedor não está preenchido.';
        }
        if (empty($monta['Anexos'])) {
            return !empty($monta['ErroAnexo']) ? $monta['ErroAnexo'] : 'O PDF da ordem de compra não foi localizado.';
        }

        if (empty($g['C031']['OC_Alerta_Email']) || empty($g['C031']['OC_Alerta_Email_SMTP_Servidor'])) {
            return 'E-mail de alertas automáticos de OC não está configurado.';
        }
        $C007 = array(
            'C007_Email' => $g['C031']['OC_Alerta_Email'],
            'C007_Primeiro_Nome' => 'Compras',
            'C007_Email_SMTP_Senha' => $g['C031']['OC_Alerta_Email_SMTP_Senha'],
            'C007_Email_SMTP_Servidor' => $g['C031']['OC_Alerta_Email_SMTP_Servidor'],
            'C007_Email_SMTP_Autenticacao' => $g['C031']['OC_Alerta_Email_SMTP_Autenticacao'],
            'C007_Email_SMTP_Usuario' => $g['C031']['OC_Alerta_Email_SMTP_Usuario'],
            'C007_Email_SMTP_Porta' => $g['C031']['OC_Alerta_Email_SMTP_Porta'],
            'C007_Email_SMTP_Seguranca' => $g['C031']['OC_Alerta_Email_SMTP_Seguranca']
        );

        $mensagem = $this->montarMensagemEmailLembreteEntrega($T224_Id, $dataEntrega, $avisoAtraso);
        $monta['Assunto'] = $mensagem['Assunto'];
        $monta['Corpo'] = $mensagem['Corpo'];

        $mail = new \phpmailer;
        $mail->CharSet = 'utf-8';
        $mail->From = $C007['C007_Email'];
        $mail->FromName = $C007['C007_Primeiro_Nome'];
        $mail->Host = $C007['C007_Email_SMTP_Servidor'];
        $mail->Mailer = 'smtp';
        $mail->SMTPAuth = $C007['C007_Email_SMTP_Autenticacao'];
        $mail->Username = $C007['C007_Email_SMTP_Usuario'];
        $mail->Password = $C007['C007_Email_SMTP_Senha'];
        $mail->Subject = '=?UTF-8?B?' . base64_encode($monta['Assunto']) . '?=';
        $mail->Body = $monta['Corpo'];
        $mail->AltBody = strip_tags($monta['Corpo']);
        $mail->SetLanguage('br');
        $mail->IsHTML(true);
        $mail->Timeout = 60;
        if (!empty($C007['C007_Email_SMTP_Porta'])) {
            $mail->Port = $C007['C007_Email_SMTP_Porta'];
        }
        if ($C007['C007_Email_SMTP_Servidor'] == 'smtp.gmail.com') {
            $mail->SMTPSecure = 'ssl';
            $mail->Port = '465';
        }
        if ($C007['C007_Email_SMTP_Seguranca'] == '1') {
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPSecurity = 'ssl';
        } elseif ($C007['C007_Email_SMTP_Seguranca'] == '2') {
            $mail->SMTPSecure = 'tls';
            $mail->SMTPSecurity = 'tls';
        }

        $destinatarios = preg_split('/[;,]+/', $monta['Para']);
        foreach ($destinatarios as $destinatario) {
            $destinatario = trim($destinatario);
            if ($destinatario !== '') {
                $mail->AddAddress($destinatario);
            }
        }
        foreach ((array) $monta['Anexos'] as $anexo) {
            if (file_exists($anexo)) {
                $mail->AddAttachment($anexo, basename($anexo));
            }
        }

        if (!$mail->Send()) {
            return trim(strip_tags($mail->ErrorInfo));
        }

        $estrutura = serialize(array('anexosManual' => array_map('basename', (array) $monta['Anexos'])));
        $de = mysql_real_escape_string("\"{$C007['C007_Primeiro_Nome']}\" <{$C007['C007_Email']}>");
        mysql_query("INSERT INTO T139 (T139_Pasta, T139_Uid, T139_Titulo, T139_De, T139_Para, T139_Data_Hora, T139_Estrutura, T139_Mensagem_Corpo, T139_Flag_Lido, T139_D024_Id, T139_C007_Id, T139_T224_Id) VALUES ('S', 0, '" . mysql_real_escape_string($monta['Assunto']) . "', '{$de}', '" . mysql_real_escape_string($monta['Para']) . "', NOW(), '" . mysql_real_escape_string($estrutura) . "', '" . mysql_real_escape_string($monta['Corpo']) . "', '1', '" . mysql_real_escape_string($monta['D024_Id']) . "', '" . mysql_real_escape_string($g['usuarioAtual']) . "', '{$T224_Id}')");
        $T139_Id = mysql_insert_id();
        $dirAnexos = $g['pathDados'] . 'anexosEmail/';
        is_dir($dirAnexos) or mkdir($dirAnexos, 0777, true);
        foreach ((array) $monta['Anexos'] as $indice => $anexo) {
            if (file_exists($anexo)) {
                copy($anexo, "{$dirAnexos}{$T139_Id}-{$indice}");
            }
        }
        return true;
    }

    /**
     * Retorna a mensagem padrão dos alertas automáticos de entrega da OC.
     * Pode ser sobrescrito por personalização de cliente, sem alterar o envio do e-mail.
     */
    public function montarMensagemEmailLembreteEntrega($T224_Id, $dataEntrega, $avisoAtraso = false)
    {
        $dataEntregaFormatada = gCorrigeData($dataEntrega);
        if ($avisoAtraso) {
            return array(
                'Assunto' => "Aviso de atraso de entrega - Ordem de Compra Nº {$T224_Id}",
                'Corpo' => "<font style='font-size:16px'>Olá,<br /><br />A data de entrega prevista para a Ordem de Compra <b>{$T224_Id}</b> era <b>{$dataEntregaFormatada}</b> e encontra-se em atraso.<br /><br />Pedimos, por gentileza, uma posição sobre a entrega. A ordem de compra segue anexada.</font>"
            );
        }

        return array(
            'Assunto' => "Lembrete de prazo de entrega - Ordem de Compra Nº {$T224_Id}",
            'Corpo' => "<font style='font-size:16px'>Olá,<br /><br />A data de entrega prevista para a Ordem de Compra <b>{$T224_Id}</b> é <b>{$dataEntregaFormatada}</b>.<br /><br />Pedimos, por gentileza, a confirmação do atendimento do prazo. A ordem de compra segue anexada.</font>"
        );
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
     * @param itn $T224_Id
     */
    public function montarEmailPdf($T224_Id)
    {
        global $g, $confUsuario, $sys;

        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();        

        $colspan = 14;
        $colspan += ($g['C031']['exibirMarcaImpressaoCompra'] == 'S') ? 1 : 0;
        $colspan += ($g['C031']['exibirFotoImpressaoCompra'] == 'S') ? 1 : 0;
        
        $colspanCab = 8;
        $colspanCab += ($g['C031']['exibirMarcaImpressaoCompra'] == 'S') ? 1 : 0;
        $colspanCab += ($g['C031']['exibirFotoImpressaoCompra'] == 'S') ? 1 : 0;

        /**
         * SELECT's
         */
        $C004  = mysql_query("SELECT * FROM C004 left join D018 on D018_Id=C004_D018_Id left join D020 on D020_Id=C004_D020_Id WHERE C004_Id = '{$g['empresaAtual']}'");
        $mC004 = mysql_fetch_array($C004);
        
        $T224_Id = mysql_real_escape_string($T224_Id);
        $T224    = <<<EOT
        SELECT *,
        comprador.C007_Primeiro_Nome as compradorC007_Primeiro_Nome,
        comprador.C007_Email as compradorC007_Email,
        comprador.C007_DDD_Telefone as compradorC007_DDD_Telefone,
        comprador.C007_Telefone as compradorC007_Telefone,
        analista.C007_Primeiro_Nome as analistaC007_Primeiro_Nome,
        T224_Nome_Status as status,
        T224_Prazos(T224_Id) as T224_Prazos_Pagamentos
        FROM T224 
        LEFT JOIN D013 ON D013_Id=T224_D013_Id 
        LEFT JOIN D116 ON D116_Id=T224_D116_Id 
        LEFT JOIN D133 ON D133_Id=D116_D133_Id 
        LEFT JOIN D134 ON D134_Id=D116_D134_Id 
        LEFT JOIN D024 ON D024_Id=T224_D024_Id 
        LEFT JOIN D020 ON D020_Id=D024_D020_Id
        LEFT JOIN D018 ON D018_Id=D024_D018_Id 
        LEFT JOIN D022 ON D022_Id=T224_D022_Id
        LEFT JOIN C007 AS comprador ON comprador.C007_Id=T224_C007_Id_Vendedor_Interno
        LEFT JOIN C007 AS analista ON analista.C007_Id=T224_Liberou_Alcada_C007_Id
        WHERE T224_Id='{$T224_Id}';
EOT;
        
        $T224  = mysql_query($T224);
        $mT224 = mysql_fetch_array($T224);
        
        
        $T225 = <<<EOT
        SELECT * FROM T225 
        LEFT JOIN D009 ON D009_Id=T225_D009_Id 
        LEFT JOIN D049 ON D049_Id=D009_D049_Id
        LEFT JOIN D082 ON D082_Id=D049_D082_Id
        LEFT JOIN D001 ON D001_Id=D049_D001_Id
        LEFT JOIN D003 ON D003_Id=D001_D003_Id
        LEFT JOIN T144 ON T144_D001_Id=D001_Id
        LEFT JOIN D037 ON D037_Id=T225_D037_Id
        LEFT JOIN D005 ON D005_Id=T225_D005_Id
        LEFT JOIN D006 ON D006_Id=T225_D006_Id
        LEFT JOIN T225A ON T225_Id=T225A_T225_Id
        WHERE T225_T224_Id = '{$T224_Id}' 
        AND T225_Flag_Cancelada != 'S' 
        GROUP BY T225_Id
        ORDER BY T225_Item,T225_Id
EOT;
        $T225 = mysql_query($T225);
        
        
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
        
        $mT224['T224_Data_Emissao'] = gCorrigeData($mT224['T224_Data_Emissao']);
        $T224_Numero_Ordem_Compra   = str_pad($T224_Id, 6, "0", STR_PAD_LEFT);
        
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
                                <b>Ordem compra<br>Nº {$T224_Numero_Ordem_Compra}<br>{$mT224['T224_Data_Emissao']}<b>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 2px solid #000;width:27%" >
                                <table class="tabela">
                                    <tr>
                                        <td><b><u>COMPRADOR:</u></b></td>
                                    </tr>
                                    <tr>
                                        <td>{$mT224['compradorC007_Primeiro_Nome']}</td>
                                    </tr>
                                    <tr>
                                        <td>{$mT224['compradorC007_Email']}</td>
                                    </tr>
                                    <tr>
                                        <td>{$mT224['compradorC007_DDD_Telefone']}-{$mT224['compradorC007_Telefone']}</td>
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
                                                <td><b>{$mT224['D024_Nome_Empresa']}</b></td>
                                            </tr>
                                            <tr>
                                                <td>{$mT224['D024_Endereco']}, {$mT224['D024_Numero']} - {$mT224['D024_Bairro']}</td>
                                            </tr>
                                            <tr>
                                                <td><b>CEP:</b> {$mT224['D024_Cep']} {$mT224['D020_Nome_Cidade']} - {$mT224['D018_UF']}</td>
                                            </tr>
                                            <tr>
                                                <td><b>CNPJ/CPF:</b> {$mT224['D024_Cnpj']}{$mT224['D024_Cpf']}</td>
                                            </tr>
                                            </table>
                                        </td>
                                        <td width="50%">
                                            <table class="tabela" width="100%">
                                            <tr>
                                                <td><b>Incr. Estadual:</b> {$mT224['D024_Inscricao_Estadual']}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Contato: </b>{$mT224['D013_Nome_Contato']}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Telefone: </b>{$mT224['D013_DDD_Telefone_1']}-{$mT224['D013_Telefone_1']}</td>
                                            </tr>
                                            <tr>
                                                <td><b>E-mail: </b>{$mT224['D013_E_Mail']}</td>
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
              <td class="tdTitulo" style="background:lightblue" colspan="2">
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
        while ($mT225 = mysql_fetch_array($T225)) {
            
            $mT225['T225_Quantidade']                        = gCorrigeNumero($mT225['T225_Quantidade']);
            $mT225['T225_Valor_Preco_Sem_Desconto_Unitario'] = number_format($mT225['T225_Valor_Preco_Sem_Desconto_Unitario'], 5, ',', '.');
            $mT225['T225_Valor_Total_Preco']                 = gCorrigeNumero($mT225['T225_Valor_Total_Preco']);
            $mT225['T225_Aliquota_IPI']                      = gCorrigeNumero($mT225['T225_Aliquota_IPI']);
            $mT225['T225_Valor_IPI']                         = gCorrigeNumero($mT225['T225_Valor_IPI']);
            $mT225['T225_Aliquota_ICMS']                     = number_format($mT225['T225_Aliquota_ICMS'], 0);
            $mT225['T225_Valor_ICMS']                        = gCorrigeNumero($mT225['T225_Valor_ICMS']);
            $mT225['T225_Valor_ICMS_Substituicao_Retencao']  = gCorrigeNumero($mT225['T225_Valor_ICMS_Substituicao_Retencao']);
            $mT225['T225_ST_VA_Ajustada']                    = gCorrigeNumero($mT225['T225_ST_VA_Ajustada']);
            $valorTotalFCP += $mT225['T225A_Valor_FCP_ST'];
            $observacaoItem                                  = '<font></font>';
            if (!empty($mT225['T225_Observacao'])) {
                $observacaoItem = '<font style="background:#FCF9AE"><b>Observação:</b> ' . $mT225['T225_Observacao'] . '</font><br>';
            }
            if (!empty($mT225['T225_Codigo_Substituto'])) {
                //$mT225['T225_Codigo_Produto']=$mT225['T225_Codigo_Substituto'];
            }
            
            if($g['C031']['exibirFotoImpressaoCompra'] == 'S'){

                $foto = $CAD002->cad002FotoProduto($mT225['D001_Id'],60);

            }

            $html .= <<<EOT
      <tr>
EOT;
            if (!empty($mT225['T225_Codigo_Substituto'])) {
                $mT225['T225_Codigo_Substituto'] = '<b>' . $mT225['T225_Codigo_Substituto'] . '</b><br />';
            }
            $html .= <<<EOT
        <td align="center" class="tdBordaL">
          {$mT225['T225_Item']}
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
          {$mT225['T225_Codigo_Substituto']}
          {$mT225['T225_Codigo_Produto']}
        </td>
        <td class="tdBorda" colspan="2">
          <b>{$mT225['T225_Descricao_Produto']} {$mT225['D049_Modelo']}</b><br>
          {$observacaoItem}
          <font style='color:#0F079E'><i>Prazo entrega em dias: <b>{$mT225['T225_Observacao_Entrega']}</b></i></font>
        </td>
EOT;
    if($g['C031']['exibirMarcaImpressaoCompra'] == 'S'){
        $html .= <<<EOT
        <td align="center" class="tdBorda">
          {$mT225['D082_Marca']}
        </td>
EOT;
    }
        $html .= <<<EOT
        <td align="center" class="tdBorda">
          {$mT225['D037_Unidade']}
        </td>
        <td align="center" class="tdBorda">
          {$mT225['D005_Classificacao_Fiscal']}
        </td>
        <td align="center" class="tdBorda">
          {$mT225['D006_Codigo_CFOP']}
        </td>
        <td align="center" class="tdBorda">
          <b>{$mT225['T225_Quantidade']}</b>
        </td>
        <td align="right" class="tdBorda">
          <b>{$mT225['T225_Valor_Preco_Sem_Desconto_Unitario']}</b>
        </td>
        <td align="right" class="tdBorda">
          {$mT225['T225_Valor_Total_Preco']}
        </td>
        <td align="center" class="tdBorda">
          {$mT225['T225_Aliquota_IPI']}
        </td>
        <td align="center" class="tdBorda">
          {$mT225['T225_Aliquota_ICMS']}
        </td>
        <td align="right" class="tdBorda">
          {$mT225['T225_ST_VA_Ajustada']}
        </td>
        <td align="right" class="tdBordaR">
          {$mT225['T225_Valor_ICMS_Substituicao_Retencao']}
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
        <td colspan="{$colspan}" style="padding:0px 0px 0px 0px; border-top: 2px solid #000;">

EOT;
        
        /**
         * PDF - TOTAIS
         */
        $mT224['T224_Valor_Total_Produtos']             = gCorrigeNumero($mT224['T224_Valor_Total_Produtos']);
        $mT224['T224_Valor_Frete']                      = gCorrigeNumero($mT224['T224_Valor_Frete']);
        $mT224['T224_Valor_Total_IPI']                  = gCorrigeNumero($mT224['T224_Valor_Total_IPI']);
        $mT224['T224_Valor_Total_ICMS']                 = gCorrigeNumero($mT224['T224_Valor_Total_ICMS']);
        $mT224['T224_Valor_Total']                      = gCorrigeNumero($mT224['T224_Valor_Total']);
        $mT224['T224_Data_Entrega']                     = gCorrigeData($mT224['T224_Data_Entrega']);
        $mT224['T224_Valor_ICMS_Substituicao_Retencao'] = gCorrigeNumero($mT224['T224_Valor_ICMS_Substituicao_Retencao']);
        $mT224['T224_Valor_Desconto_Manual']            = gCorrigeNumero($mT224['T224_Valor_Desconto_Manual']);
        
        $html .= <<<EOT
      <table class="tabela" cellspacing="0px" cellpadding="4px">
        <tr>
          <td align="left" width="105">Pagamento: </td><td align="left" width="330" style="border-right:2px solid black"><b>{$mT224['T224_Prazos_Pagamentos']}</b></td>
          <td align="left" width="105">Valor produtos: </td><td  align="left" width="100"><b>R$ {$mT224['T224_Valor_Total_Produtos']}</b></td>
        </tr>
EOT;
        switch ($mT224['T224_Flag_Frete']) {
            case '0':
                $T224_Frete = "Por conta do emitente";
                break;
            case '1':
                $T224_Frete = "Por conta do destinatário";
                break;
            case '2':
                $T224_Frete = "Terceiros";
                break;
            case '9':
                $T224_Frete = "Sem frete";
                break;
            default:
                $T224_Frete = "Não informado";
                break;
        }
        
        $html .= <<<EOT
        <tr>
          <td align="left" width="105">Frete: </td><td align="left" style="border-right:2px solid black"><b>{$T224_Frete}</b></td>
          <td align="left" width="105">Valor IPI: </td><td  align="left"><b>R$ {$mT224['T224_Valor_Total_IPI']}</b></td>
        </tr>
        <tr>
          <td align="left" width="105">Transportadora: </td><td align="left" style="border-right:2px solid black"><b>{$mT224['D022_Nome_Empresa']}</b></td>
          <td align="left" width="105">Valor frete: </td><td  align="left"><b>R$ {$mT224['T224_Valor_Frete']}</b></td>
        </tr>
        <tr>
          <td align="left" width="105"></td><td align="left" width="330" style="border-right:2px solid black"></td>
          <td align="left" width="105">Valor ST: </td><td align="left"><b>R$ {$mT224['T224_Valor_ICMS_Substituicao_Retencao']}</b></td>
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
          <td align="left" width="105">Desconto: </td><td align="left"><b>R$ {$mT224['T224_Valor_Desconto_Manual']}</b></td>
        </tr>
        <tr>
          <td align="left" width="105"></td><td align="left" width="330" style="border-right:2px solid black"></td>
          <td align="left" width="105">Valor total: </td><td align="left" style="font-size:18px"><b>R$ {$mT224['T224_Valor_Total']}</b></td>
        </tr>
        <tr>
          <td align="left" width="105"></td><td align="left"  style="border-right:2px solid black"></td>
          <td align="left" width="105">Valor ICMS: </td><td align="left"><b>R$ {$mT224['T224_Valor_Total_ICMS']}</b></td>
        </tr>
      
EOT;
        
        $html .= "</table>";
        
        $html .= <<<EOT
          </td>
      </tr>

        <tr>
        <td colspan="{$colspan}" style="background:lightblue;font-weight:bold; border-top: 2px solid #000; border-bottom: 2px solid #000;" align="center">
          OBSERVAÇÕES 
          </td>
      </tr>
        <tr>
        <td colspan="{$colspan}">
EOT;
        
        if (!empty($mT224['T224_Observacao'])) {
            $html .= '<br>' . str_replace(chr(10), '<br>', '<b>' . $mT224['T224_Observacao']) . '</b><br><br>';
        }
        
        $xPed     = htmlentities('<xPed>');
        $nItemPed = htmlentities('<nItemPed>');
        
        if(!empty($mC004['C004_Observacao_Ordem_Compra'])){
            $observacaoOrdemCompra = ((!empty($mT224['T224_Observacao'])) ? "<font style='color:red;font-weight:bold'>Observações:</font><br><br>" : "") . str_replace("\n", "<br>", $mC004['C004_Observacao_Ordem_Compra']);
        }

        $nomeSiteEmpresa = (!empty($sys['nomeEmpresa']) && !empty($sys['siteEmpresa'])) ? strToUpper($sys['nomeEmpresa']) .' - '. strToUpper($sys['siteEmpresa']) : 'HARDNESS SISTEMAS - WWW.HARDNESS.COM.BR';

        $html .= <<<EOT
        
          </td>
      </tr>
        <tr>
        <td colspan="{$colspan}" height="100%" style="vertical-align:top;">
          {$observacaoOrdemCompra}
          <table class="tabela">
            <tr>
              <td><b><u>AUTORIZAÇÃO:</u></b></td>
            </tr>
            <tr>
              <td>{$mT224['analistaC007_Primeiro_Nome']}</td>
            </tr>
            <tr>
              <td>{$mT224['T224_Liberou_Alcada_Data']}</td>
            </tr>
            <tr>
              <td>{$mT224['status']}</td>
            </tr>
          </table>          

          </td>
      </tr>

    </table>
    <font style="font-size:10px;">{$nomeSiteEmpresa}</font>

EOT;

        return $html;
    }
    
    /**
     * excluirProduto
     *
     * @param int $T225_Id
     */
    public function excluirProduto($T225_Id)
    {
        $sqlT224  =  mysql_query("SELECT T225_T224_Id, T225_D009_Id FROM T225 WHERE T225_Id = '{$T225_Id}'");
        $rowT224 = mysql_fetch_array($sqlT224);
        
        $sql = mysql_query("SELECT T006_Id FROM T006 WHERE T006_T225_Id='{$T225_Id}'");
        while ($T006 = mysql_fetch_array($sql)) {
            mysql_query("UPDATE T006 SET T006_T225_Id = 0 WHERE T006_Id='{$T006['T006_Id']}'");
            if (mysql_error()) {
                return 'Erro ao desvincular o item da ordem de compra com o pedido. Por favor contate o suporte técnico';
            }
        }
        
        mysql_query("DELETE FROM T225 WHERE T225_Id='{$T225_Id}'");
        if (mysql_error()) {
            return 'Erro ao excluir o item da ordem de compra. Por favor contate o suporte técnico';
        }
        
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();
        $CAD002->reprocessaOCPedidos($rowT224['T225_D009_Id']);

        mysql_query("call T224_Gravar_Totalizacao_3({$rowT224['T225_T224_Id']});");
        if (mysql_error()) {
            return 'Erro no T224_Gravar_Totalização' . mysql_error();
        }
        return true;
    }
    
    /**
     * excluirServico
     *
     * @param int $T230_Id
     */
    public function excluirServico($T230_Id)
    {
        global $g;
        
        $sql  = "SELECT T230_Id FROM T230 LEFT JOIN T224 ON T224_Id=T230_T224_Id WHERE T230_Id = '{$T230_Id}' AND (T224_Flag_Status>0 AND T224_Flag_Status!='' AND T224_Flag_Status IS NOT NULL)";
        $res  = mysql_query($sql);
        $rows = mysql_numrows($res);
        if ($rows > 0) {
            return 'Este pedido já foi faturado.';
        }
        // exclui primeiro os materiais e itens do serviço
        mysql_query("DELETE FROM T148 WHERE T148_T230_Id='$T230_Id'");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return 'falha na exclusão dos itens do serviço...';
        }
        mysql_query("DELETE FROM T149 WHERE T149_T230_Id='$T230_Id'");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return 'falha na exclusão dos materiais do serviço...';
        }
        
        // verificar exclusao da tabela de entrega de veiculos
        $sql    = "SELECT * FROM T230 WHERE T230_Id='{$T230_Id}'";
        $result = mysql_query($sql);
        $linha8 = mysql_fetch_array($result);
        
        $sql     = "SELECT * FROM T230 WHERE T230_D116_Id='{$linha8['T230_D116_Id']}' AND T230_T224_Id='{$linha8['T230_T224_Id']}'";
        $result1 = mysql_query($sql);
        if (mysql_num_rows($result1) == 1) {
            $sql     = "SELECT * FROM T231 WHERE T231_T224_Id='{$linha8['T230_T224_Id']}' AND T231_D116_Id='{$linha8['T230_D116_Id']}'";
            $result3 = mysql_query($sql);
            $linha2  = mysql_fetch_array($result3);
            $sql     = "DELETE FROM T231 WHERE T231_Id='{$linha2['T231_Id']}'";
            mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return "Erro 1.1 " . mysql_error();
            } else {
                $sql = "DELETE FROM T230 WHERE T230_Id='{$T230_Id}'";
                mysql_query($sql);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return "Erro 1.2 " . mysql_error();
                }
            }
        } else {
            $sql = "DELETE FROM T230 WHERE T230_Id='{$T230_Id}'";
            mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return "Erro 1.3 " . mysql_error();
            }
        }
        
        return true;
    }
    
    /**
     * excluirPedido
     *
     * @param int $T224_Id
     */
    public function excluirPedido($T224_Id)
    {
        if (empty($T224_Id)) {
            return "Não foi passado o Id do Pedido, contate o suporte técnico";
        }
        
        $cClausula = " select ";
        $cClausula .= " * ";
        $cClausula .= " from T224 ";
        $cClausula .= " where T224_Id='{$T224_Id}' ";
        $cSql_T224 = mysql_query($cClausula);
        $mT224     = mysql_fetch_array($cSql_T224);
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        $cClausula = "delete from T226 where T226_T224_Id='{$T224_Id}'";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro <br /><br /> $cClausula";
        }
        
        $T225 = mysql_query("select T225_Id from T225 where T225_T224_Id='{$T224_Id}'");
        while ($mT225 = mysql_fetch_array($T225)) {
            $cClausula = "delete from T225 where T225_Id='{$mT225['T225_Id']}'";
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: $cErro <br /> <br /> $cClausula";
            }
        }
        
        $cClausula = "delete from T224 where T224_Id='{$T224_Id}'";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro<br /><br />$cClausula";
        }
        
        $cClausula = "delete from T230 where T230_T224_Id='{$T224_Id}'";
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
     * insereServico
     *
     * @param Form $form
     * @param int $T230_Id
     */
    public function insereServico($form, $T230_Id)
    {
        $D112_Id = $form->campoValorEnviado('T230_D112_Id');
        $sql     = "SELECT * FROM D113 WHERE D113_D112_Id='{$D112_Id}'";
        $array1  = mysql_query($sql);
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        while ($row = mysql_fetch_assoc($array1)) {
            // D113_D112_Id, D113_D110_Id, D113_Titulo, D113_Descricao
            $sql = "INSERT INTO T148 (T148_T230_Id, T148_Titulo, T148_Descricao) VALUES ('" . mysql_real_escape_string($T230_Id) . "', '"
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
            $sql = "INSERT INTO T149 (T149_D037_Id, T149_D009_Id, T149_T230_Id, T149_Quantidade) VALUES ('" . mysql_real_escape_string($row['D114_D037_Id']) . "', '" . mysql_real_escape_string($row['D114_D009_Id']) . "', '" . mysql_real_escape_string($T230_Id) . "', '" . mysql_real_escape_string($row['D114_Quantidade']) . "');";
            mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return mysql_error();
            }
        }
        
        $sql    = "SELECT * FROM T230 WHERE T230_Id='{$T230_Id}'";
        $result = mysql_query($sql);
        $linha  = mysql_fetch_array($result);
        
        $sql     = "SELECT * FROM T231 WHERE T231_D116_Id='{$linha['T230_D116_Id']}' AND T231_T224_Id='{$linha['T230_T224_Id']}'";
        $result2 = mysql_query($sql);
        if (mysql_num_rows($result2) == 0) {
            $sql = "INSERT INTO T231
            (T231_D116_Id, T231_T224_Id, T231_T230_Id)
                VALUES
            ('{$linha['T230_D116_Id']}', '{$linha['T230_T224_Id']}', '{$T230_Id}')";
            
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
     * est016aplicarIpvMinimo
     *
     * @param int $T224_Id
     * @param string $IPV_Minimo
     */
    public function est016aplicarIpvMinimo($T224_Id, $IPV_Minimo)
    {
        if ($T224_Id == 0) {
            return ("Nao encontrado o id da entidade principal");
        }
        
        //O valor já veio formatado o form_func-ajax.php
        //$IPV_Minimo=formata_valor_gravar($IPV_Minimo,5);
        
        if ($IPV_Minimo == 0) {
            
            return "Digite o IPV Mínimo";
        }
        
        $cSql_T224 = mysql_query("select * from T224 where T224_Id='{$T224_Id}'");
        $mT224     = mysql_fetch_array($cSql_T224);
        if ($mT224['T224_Flag_Devolucao'] == 'S') {
            return "ISTO É UMA DEVOLUÇÃO";
        }
        if ($mT224['T224_Flag_Travar_Pedido'] == 1) {
            return "PEDIDO TRAVADO";
        }
        
        $cSql_T225 = mysql_query("select T225_Id,
                                 T225_Valor_Custo_Unitario
                            from T225
                           where T225_T224_Id='{$T224_Id}'");
        
        if (mysql_num_rows($cSql_T225) == 0) {
            return ("Não existem itens para aplicar o IPV");
        }
        
        while ($mT225 = mysql_fetch_array($cSql_T225)) {
            
            $Valor_Preco_Unitario = round($mT225['T225_Valor_Custo_Unitario'] * $IPV_Minimo, 2);
            
            mysql_query("update T225 
                          set T225_Valor_Preco_Sem_Desconto_Unitario='{$Valor_Preco_Unitario}'
                        where T225_Id='{$mT225['T225_Id']}'");
            
            mysql_query("call T225_Gravar_Totalizacao({$mT225['T225_Id']});");
        }
        
        return true;
    }
    
    /**
     * importarItens
     *
     * @param int $T224_Id
     * @param int $T224_Id_Origem
     */
    public function importarItens($T224_Id, $T224_Id_Origem)
    {
        $T224_Id        = trim($T224_Id);
        $T224_Id_Origem = trim($T224_Id_Origem);
        
        if (empty($T224_Id)) {
            return "Faltou o parametro T224_Id";
        }
        if (empty($T224_Id_Origem)) {
            return "Digite o numero do pedido";
        }
        
        if ($T224_Id == $T224_Id_Origem) {
            return "Voce não pode importar o pedido para ele mesmo";
        }
        
        $cSql_T224 = mysql_query("select * from T224 where T224_Id='{$T224_Id}'");
        $mT224     = mysql_fetch_array($cSql_T224);
        
        $cSql_T224_Origem = mysql_query("select * from T224 where T224_Id='{$T224_Id_Origem}'");
        $mT224_Origem     = mysql_fetch_array($cSql_T224_Origem);
        
        if ($mT224['T224_Flag_Travar_Pedido'] == 'S') {
            return "PEDIDO ESTA TRAVADO, NÃO PODE SER ALTERADO!";
        }
        
        If ($mT224['T224_Flag_Status'] != $mT224_Origem['T224_Flag_Status']) {
            return "IMPORTAÇÃO CANCELADA! Os pedidos estão com Status diferentes.";
        }
        
        If ($mT224['T224_C004_Id'] != $mT224_Origem['T224_C004_Id']) {
            return "IMPORTAÇÃO CANCELADA! Os pedidos são de Empresas diferentes.";
        }
        
        If ($mT224['T224_D024_Id'] != $mT224_Origem['T224_D024_Id']) {
            return "IMPORTAÇÃO CANCELADA! Os pedidos são de Clientes diferentes.";
        }
        If ($mT224['T224_D006_Id'] != $mT224_Origem['T224_D006_Id']) {
            return "IMPORTAÇÃO CANCELADA! Os pedidos possuem CFOP diferentes.";
        }
        /////////////////////////////////////////////////////////////////////////////////
        //-- VERIFICA SE O ITEM EXISTEM NA EMPRESA DE DESTINO
        /////////////////////////////////////////////////////////////////////////////////
        $cSql_T225 = mysql_query("select t2.D009_Id,
                                 D001_Codigo_Produto,
                                 D002_Descricao_Produto
                            from T225
                       left join D009 as t1 on T225_D009_Id=t1.D009_Id
                       left join D049 on D049_Id=t1.D009_D049_Id
                       left join D001 on D001_Id=D049_D001_Id
                       left join D002 on D002_Id=D001_D002_Id
                       left join D009 as t2 on D049_Id=t2.D009_D049_Id and t2.D009_C004_Id='{$mT224['T224_C004_Id']}'
                           where T225_T224_Id='{$T224_Id}'
                             and t2.D009_Id=0");
        while ($mT225 = mysql_fetch_array($cSql_T225)) {
            $mSemCadastro[] = "{$mT225['D001_Codigo_Produto']} - {$mT225['D002_Descricao_Produto']}";
        }
        if (mysql_num_rows($cSql_T225) > 0) {
            $SemCadastro = implode("/p", $mSemCadastro);
            return "1. Existem itens sem cadastro na empresa $mT224[T224_C004_Id]/pque estao sendo importados do pedido $T224_Id_Origem/p/p$SemCadastro";
        }
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        mysql_query("update T225 set T225_T224_Id='{$T224_Id}',T225_Numero_Pedido_Venda='{$T224_Id_Origem}' where T225_T224_Id='{$T224_Id_Origem}'");
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "2. Erro: $cErro/p/pClausula:$cClausula";
        }
        
        mysql_query("update T224 set T224_Flag_Status='0', T224_Nome_Status = T224_Status_Pedido(T224_Flag_Status, T224_Id), T224_Flag_Almoxarifado='N' where T224_Id='{$T224_Id_Origem}'");
        
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "3. Erro: $cErro/p/pClausula:$cClausula";
        }
        
        mysql_query("call T224_Gravar_Totalizacao_3($T224_Id)");
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "4. Erro: $cErro/p/pClausula:$cClausula";
        }
        
        mysql_query("call T224_Gravar_Totalizacao_3($T224_Id_Origem)");
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "5. Erro: $cErro/p/pClausula:$cClausula";
        }
        
        mysql_query("COMMIT");

        return true;
    }

    public function atualizarAliquotasPedidosCrontab($T224_Id){
        global $g;

        // Atualizando o numero do item! Da forma manual é atualiza o item quando entra no grid de produtos
        $T225 = mysql_query("SELECT T225_Item,
                                    T225_Id 
                               FROM T225 
                              WHERE T225_T224_Id='{$T224_Id}' 
                           ORDER BY T225_Item,T225_Id");
		$item = 1;
		while ($mT225=mysql_fetch_array($T225)){
			if ($mT225['T225_Item'] != $item){
			  	mysql_query("update T225 set T225_Item='{$item}' where T225_Id='{$mT225['T225_Id']}'");
			}
			$item++;
		}

        $T225 = mysql_query("SELECT D009_Id,
                                    T219_Id,
                                    T225_Id,
                                    D001_Codigo_Produto,
                                    T225_Item,
                                    T219_Flag_Origem_Produto,
                                    D001_D037_Id,
                                    D024_Situacao_Tributaria_PIS_COFINS,
                                    D024_Flag_Isento_PIS_COFINS,
                                    D001_Peso_Unitario_Kg,
                                    T224_Flag_Devolucao,
                                    T224_Flag_Calcular_IPI,
                                    T224_Flag_Calcular_ICMS,
                                    T224_Flag_Calcular_ST       
                               FROM T224
                          LEFT JOIN D006 ON D006_Id=T224_D006_Id 
                          LEFT JOIN T225 ON T224_Id=T225_T224_Id
                          LEFT JOIN D024 ON D024_Id=T225_D024_Id
                          LEFT JOIN D009 ON D009_Id=T225_D009_Id
                          LEFT JOIN D049 ON D049_Id=D009_D049_Id
                          LEFT JOIN D001 ON D001_Id=D049_D001_Id
                          LEFT JOIN T219 ON T219_Id=T225_T219_Id 
                              WHERE T224_Id='{$T224_Id}'
                           ORDER BY T225_Item,T225_Id");

        while($mT225 = mysql_fetch_array($T225)){
            $retorno = $this->atualizarAliquotasPedidos($T224_Id, false, false, $mT225);

            if($retorno !== true){
                return $retorno;
            }
        }

        return true;
    }
    
    /**
     * atualizarAliquotasPedidos
     *
     * @param int $T224_Id
     * @param int $C004_Id
     */
    public function atualizarAliquotasPedidos($T224_Id, $C004_Id = false, $r_usarTabelaFornecedor = false, $arrayParametros =null)
    {
        log("entrou no atualizarAliquotasPedidos");
        global $g;
        require_once('bibliotecas/classes/FIS003.php');
        $FIS003 = new FIS003();
        require_once("bibliotecas/classes/EST015.php");
        $EST015 = new EST015();

        $C004_Id = ($C004_Id == false) ? $g['empresaAtual'] : $C004_Id;

        $D009_Id= $arrayParametros['D009_Id'];
        $T219_Id= $arrayParametros['T219_Id'];
        $T225_Id= $arrayParametros['T225_Id'];
        $D001_Codigo_Produto= $arrayParametros['D001_Codigo_Produto'];
        $T225_Item = $arrayParametros['T225_Item'];   
        $T219_Flag_Origem_Produto = $arrayParametros['T219_Flag_Origem_Produto']; 
        $D001_D037_Id = $arrayParametros['D001_D037_Id']; 
        $D024_Situacao_Tributaria_PIS_COFINS = $arrayParametros['D024_Situacao_Tributaria_PIS_COFINS'];   
        $D024_Flag_Isento_PIS_COFINS = $arrayParametros['D024_Flag_Isento_PIS_COFINS'];   
        $D001_Peso_Unitario_Kg = $arrayParametros['D001_Peso_Unitario_Kg'];
        $T224_Flag_Devolucao = $arrayParametros['T224_Flag_Devolucao'];   
        $T224_Flag_Calcular_IPI = $arrayParametros['T224_Flag_Calcular_IPI']; 
        $T224_Flag_Calcular_ICMS = $arrayParametros['T224_Flag_Calcular_ICMS'];   
        $T224_Flag_Calcular_ST= $arrayParametros['T224_Flag_Calcular_ST'];   
        
        if ($T224_Flag_Devolucao == 'S') {
            return "Este é um pedido para emissão de nota de devolução, não é possivel executar a atualização de aliquotas";
        }
        
        $Item = 1;

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        $produtoSemCFOP = '';
        $produtoSemOrigem = '';
        $produtoSemTabela = '';

        if($r_usarTabelaFornecedor == true){
            mysql_query("UPDATE T224 SET T224_Flag_Calcular_ST='S',T224_Flag_Calcular_IPI='S',T224_Flag_Calcular_ICMS='S' WHERE T224_Id='{$T224_Id}'");
        }

        $Aliquotas = $EST015->retornaAliquotas($D009_Id, false, $T224_Id, false, false, $T225_Id, false, false, false, $r_usarTabelaFornecedor );
        if($r_usarTabelaFornecedor == true){
            if($Aliquotas['D069_Id'] <= 0){
                log("entra aqui");
                $produtoSemTabela = (empty($produtoSemTabela)) ? $D001_Codigo_Produto : ', '.$D001_Codigo_Produto;
                log("produtos sem tabela= ".$produtoSemTabela);
            }else if($Aliquotas['D006_Id'] <= 0){
                $produtoSemCFOP = (empty($produtoSemCFOP)) ? $D001_Codigo_Produto : ', '.$D001_Codigo_Produto;
                log('produto sem CFOP: ' . $produtoSemCFOP);
            }else if($Aliquotas['Origem_Mercadoria'] == ''){
                $produtoSemOrigem = (empty($produtoSemOrigem)) ? $D001_Codigo_Produto : ', '.$D001_Codigo_Produto;
            }
        }

        if ($T225_Item <= 0) {
            $T225_Item = $Item;
        }
        if (!($T219_Flag_Origem_Produto === '') and (!$r_usarTabelaFornecedor)) {
            $Aliquotas['Origem_Mercadoria'] = $T219_Flag_Origem_Produto;
        }
        $Item++;

        $demonstrativoNCM = $FIS003->demonstrativoNCM($Aliquotas['D005_Id']);
        $demonstrativoNCM = mysql_real_escape_string($demonstrativoNCM);

        if($r_usarTabelaFornecedor){
            $cClausula = " update T225 ";
            $cClausula .= " LEFT JOIN D006 on D006_Id=T225_D006_Id ";
            $cClausula .= " LEFT JOIN D059 on D059_Id=D006_D059_Id ";
            $cClausula .= " LEFT JOIN T224 on T224_Id=T225_T224_Id ";
            $cClausula .= " LEFT JOIN C004 on C004_Id=T224_C004_Id ";
            $cClausula .= " LEFT JOIN D005 on D005_Id=T225_D005_Id ";
            $cClausula .= " LEFT JOIN D149 on D005_Id=D149_D005_Id AND D149_C004_Id=C004_Id";
            $cClausula .= " set ";
            $cClausula .= " T225_Item='{$T225_Item}',";
            $cClausula .= " T225_D037_Id='{$D001_D037_Id}',";
            $cClausula .= " T225_D006_Id='{$Aliquotas['D006_Id']}',";
            $cClausula .= " T225_Flag_Origem_Produto='{$Aliquotas['Origem_Mercadoria']}',";
            $cClausula .= " T225_Aliquota_IPI='{$Aliquotas['Aliquota_IPI']}',";
            $cClausula .= " T225_Aliquota_PIS='{$Aliquotas['Aliquota_PIS']}',";
            $cClausula .= " T225_Aliquota_COFINS='{$Aliquotas['Aliquota_COFINS']}',";
            $cClausula .= " T225_Aliquota_IPI='{$Aliquotas['Aliquota_IPI']}',";
            $cClausula .= " T225_Percentual_Reducao_IPI='{$Aliquotas['Percentual_Reducao_IPI']}',";
            $cClausula .= " T225_Situacao_Tributaria_IPI='{$Aliquotas['Situacao_Tributaria_IPI']}', ";
            $cClausula .= " T225_Aliquota_ICMS='{$Aliquotas['Aliquota_ICMS']}',";
            $cClausula .= " T225_Percentual_Reducao_ICMS='{$Aliquotas['Percentual_Reducao_ICMS']}',";
            $cClausula .= " T224_Aliquota_ICMS='{$Aliquotas['Aliquota_ICMS']}',";
            $cClausula .= " T224_Percentual_Reducao_ICMS='{$Aliquotas['Percentual_Reducao_ICMS']}',";
            $cClausula .= " T225_Percentual_Reducao_ICMS_ST='{$Aliquotas['Percentual_Reducao_ICMS_ST']}',";
            $cClausula .= " T225_Flag_ST=D006_Flag_Substituicao_Tributaria,";
            $cClausula .= " T225_ST_VA='{$Aliquotas['ST_VA']}', ";
            $cClausula .= " T225_ST_ICMS_Interno='{$Aliquotas['ST_ICMS_Interno']}',";
            $cClausula .= " T225_ICMS_Inter_Estadual='{$Aliquotas['ICMS_Inter_Estadual']}',";
            $cClausula .= " T225_ICMS_Inter_Estadual_MVA='{$Aliquotas['ST_ICMS_Inter_Estadual_MVA']}',";
            $cClausula .= " T225_ST_ICMS_Interno_MVA='{$Aliquotas['ST_ICMS_Interno_MVA']}',";
            $cClausula .= " T225_D005_Id='{$Aliquotas['D005_Id']}',";
            $cClausula .= " T225_D059_Id='{$Aliquotas['D059_Id']}', ";
            if (!empty($D024_Situacao_Tributaria_PIS_COFINS)) {
                $cClausula .= " T225_Situacao_Tributaria_PIS='{$D024_Situacao_Tributaria_PIS_COFINS}',";
                $cClausula .= " T225_Situacao_Tributaria_COFINS='{$D024_Situacao_Tributaria_PIS_COFINS}',";
                if ($D024_Flag_Isento_PIS_COFINS == 'S') {
                    $cClausula .= " T225_Flag_Isento_PIS='{$D024_Flag_Isento_PIS_COFINS}',";
                    $cClausula .= " T225_Aliquota_PIS='0',";
                    $cClausula .= " T225_Flag_Isento_COFINS='{$D024_Flag_Isento_PIS_COFINS}',";
                    $cClausula .= " T225_Aliquota_COFINS='0',";
                } else {
                    $cClausula .= " T225_Flag_Isento_PIS=D006_Flag_Isento_PIS,";
                    // $cClausula .= " T225_Aliquota_PIS=if(D149_Aliquota_PIS>0,D149_Aliquota_PIS,C004_Percentual_PIS),";
                    $cClausula .= " T225_Flag_Isento_COFINS=D006_Flag_Isento_COFINS,";
                    // $cClausula .= " T225_Aliquota_COFINS=if(D149_Aliquota_COFINS>0,D149_Aliquota_COFINS,C004_Percentual_COFINS),";
                }
            } else {
                $cClausula .= " T225_Flag_Isento_PIS=D006_Flag_Isento_PIS,";
                // $cClausula .= " T225_Aliquota_PIS=if(D149_Aliquota_PIS>0,D149_Aliquota_PIS,C004_Percentual_PIS),";
                $cClausula .= " T225_Situacao_Tributaria_PIS=D006_Situacao_Tributaria_PIS,";
                $cClausula .= " T225_Flag_Isento_COFINS=D006_Flag_Isento_COFINS,";
                // $cClausula .= " T225_Aliquota_COFINS=if(D149_Aliquota_COFINS>0,D149_Aliquota_COFINS,C004_Percentual_COFINS),";
                $cClausula .= " T225_Situacao_Tributaria_COFINS=D006_Situacao_Tributaria_COFINS, ";
            }
            $cClausula .= " T225_Classificacao_Fiscal='{$Aliquotas['Classificacao_Fiscal']}',";
            $cClausula .= " T225_Flag_Situacao_Tributaria='{$Aliquotas['Flag_Situacao_Tributaria']}', ";
            $cClausula .= " T225_Peso_Unitario='{$D001_Peso_Unitario_Kg}', ";
            $cClausula .= " T225_Percentual_Trib_Transparencia='{$Aliquotas['Percentual_Trib_Transparencia']}', ";
            $cClausula .= " T225_D005_Id_Info='{$demonstrativoNCM}', ";
            $cClausula .= " T225_Percentual_ST='{$Aliquotas['Percentual_ST']}',";
            $cClausula .= " T225_Valor_Preco_Unitario='{$Aliquotas['Valor_Item_Tabela_Fornecedor']}'";
            $cClausula .= " where T225_Id='{$T225_Id}'";

        } else {
            
            $cClausula = " update T225 ";
            $cClausula .= " LEFT JOIN D006 on D006_Id=T225_D006_Id ";
            $cClausula .= " LEFT JOIN D059 on D059_Id=D006_D059_Id ";
            $cClausula .= " LEFT JOIN T224 on T224_Id=T225_T224_Id ";
            $cClausula .= " LEFT JOIN C004 on C004_Id=T224_C004_Id ";
            $cClausula .= " LEFT JOIN D005 on D005_Id=T225_D005_Id ";
            $cClausula .= " LEFT JOIN D149 on D005_Id=D149_D005_Id AND D149_C004_Id=C004_Id";
            $cClausula .= " set ";
            $cClausula .= " T225_Item='{$T225_Item}',";
            $cClausula .= " T225_D037_Id='{$D001_D037_Id}',";
            if($T224_Flag_Calcular_IPI == 'S'){
                $cClausula .= " T225_Aliquota_IPI='{$Aliquotas['Aliquota_IPI']}',";
                $cClausula .= " T225_Percentual_Reducao_IPI='{$Aliquotas['Percentual_Reducao_IPI']}',";
                $cClausula .= " T225_Situacao_Tributaria_IPI='{$Aliquotas['Situacao_Tributaria_IPI']}', ";
            }
            if($T224_Flag_Calcular_ICMS == 'S'){
                $cClausula .= " T225_Aliquota_ICMS='{$Aliquotas['Aliquota_ICMS']}',";
                $cClausula .= " T225_Percentual_Reducao_ICMS='{$Aliquotas['Percentual_Reducao_ICMS']}',";
                $cClausula .= " T224_Aliquota_ICMS='{$Aliquotas['Aliquota_ICMS']}',";
                $cClausula .= " T224_Percentual_Reducao_ICMS='{$Aliquotas['Percentual_Reducao_ICMS']}',";
            }
            if($T224_Flag_Calcular_ST == 'S'){
                $cClausula .= " T225_Percentual_Reducao_ICMS_ST='{$Aliquotas['Percentual_Reducao_ICMS_ST']}',";
                $cClausula .= " T225_Flag_ST=D006_Flag_Substituicao_Tributaria,";
                $cClausula .= " T225_ST_VA='{$Aliquotas['ST_VA']}', ";
                $cClausula .= " T225_ST_ICMS_Interno='{$Aliquotas['ST_ICMS_Interno']}',";
                $cClausula .= " T225_ICMS_Inter_Estadual='{$Aliquotas['ICMS_Inter_Estadual']}',";
                $cClausula .= " T225_ICMS_Inter_Estadual_MVA='{$Aliquotas['ST_ICMS_Inter_Estadual_MVA']}',";
                $cClausula .= " T225_ST_ICMS_Interno_MVA='{$Aliquotas['ST_ICMS_Interno_MVA']}',";
            }
            $cClausula .= " T225_D005_Id='{$Aliquotas['D005_Id']}',";
            $cClausula .= " T225_D059_Id='{$Aliquotas['D059_Id']}', ";
            if (!empty($D024_Situacao_Tributaria_PIS_COFINS)) {
                $cClausula .= " T225_Situacao_Tributaria_PIS='{$D024_Situacao_Tributaria_PIS_COFINS}',";
                $cClausula .= " T225_Situacao_Tributaria_COFINS='{$D024_Situacao_Tributaria_PIS_COFINS}',";
                if ($D024_Flag_Isento_PIS_COFINS == 'S') {
                    $cClausula .= " T225_Flag_Isento_PIS='{$D024_Flag_Isento_PIS_COFINS}',";
                    $cClausula .= " T225_Aliquota_PIS='0',";
                    $cClausula .= " T225_Flag_Isento_COFINS='{$D024_Flag_Isento_PIS_COFINS}',";
                    $cClausula .= " T225_Aliquota_COFINS='0',";
                } else {
                    $cClausula .= " T225_Flag_Isento_PIS=D006_Flag_Isento_PIS,";
                    // $cClausula .= " T225_Aliquota_PIS=if(D149_Aliquota_PIS>0,D149_Aliquota_PIS,C004_Percentual_PIS),";
                    $cClausula .= " T225_Flag_Isento_COFINS=D006_Flag_Isento_COFINS,";
                    // $cClausula .= " T225_Aliquota_COFINS=if(D149_Aliquota_COFINS>0,D149_Aliquota_COFINS,C004_Percentual_COFINS),";
                }
            } else {
                $cClausula .= " T225_Flag_Isento_PIS=D006_Flag_Isento_PIS,";
                // $cClausula .= " T225_Aliquota_PIS=if(D149_Aliquota_PIS>0,D149_Aliquota_PIS,C004_Percentual_PIS),";
                $cClausula .= " T225_Situacao_Tributaria_PIS=D006_Situacao_Tributaria_PIS,";
                $cClausula .= " T225_Flag_Isento_COFINS=D006_Flag_Isento_COFINS,";
                // $cClausula .= " T225_Aliquota_COFINS=if(D149_Aliquota_COFINS>0,D149_Aliquota_COFINS,C004_Percentual_COFINS),";
                $cClausula .= " T225_Situacao_Tributaria_COFINS=D006_Situacao_Tributaria_COFINS, ";
            }
            $cClausula .= " T225_Classificacao_Fiscal='{$Aliquotas['Classificacao_Fiscal']}',";
            $cClausula .= " T225_Flag_Situacao_Tributaria='{$Aliquotas['Flag_Situacao_Tributaria']}', ";
            $cClausula .= " T225_Peso_Unitario='{$D001_Peso_Unitario_Kg}', ";
            $cClausula .= " T225_Percentual_Trib_Transparencia='{$Aliquotas['Percentual_Trib_Transparencia']}', ";
            $cClausula .= " T225_Percentual_ST='0',";
            $cClausula .= " T225_D005_Id_Info='{$demonstrativoNCM}' ";
            $cClausula .= " where T225_Id='{$T225_Id}'";
        }
        //die($cClausula);
        mysql_query($cClausula);
         $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "Erro atualizando item da ordem de compra: ID {$T225_Id} - $erro";
        }

        $T225A = mysql_query("SELECT T225A_Id FROM T225A WHERE T225A_T225_Id='$T225_Id'");
        if (mysql_num_rows($T225A) > 0) {
            $sql = "UPDATE T225A 
                       SET T225A_Percentual_Reducao_ICMS_IE_ST  = '{$Aliquotas['Percentual_Reducao_ICMS_IE_ST']}',
                           T225A_Percentual_Reducao_ICMS_INT_ST = '{$Aliquotas['Percentual_Reducao_ICMS_INT_ST']}',
                           T225A_Aliquota_FCP                   = '{$Aliquotas['Percentual_FCP']}' 
                     WHERE T225A_T225_Id='$T225_Id'";
        } else {
            $sql = "INSERT INTO T225A (T225A_T225_Id,
                                       T225A_Percentual_Reducao_ICMS_IE_ST,
                                       T225A_Percentual_Reducao_ICMS_INT_ST,
                                       T225A_Aliquota_FCP)
                               VALUES ('$T225_Id', 
                                       '{$Aliquotas['Percentual_Reducao_ICMS_IE_ST']}',
                                       '{$Aliquotas['Percentual_Reducao_ICMS_INT_ST']}',
                                       '{$Aliquotas['Percentual_FCP']}')";
        }
        mysql_query($sql);
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "Erro atualizando item da OC T225A ID T225_Id '$T225_Id': $erro";
        }      
        
        $T224A = mysql_query("SELECT T224A_Id FROM T224A WHERE T224A_T224_Id='{$T224_Id}'");
        if (mysql_num_rows($T224A) <= 0) {
            $sql = "INSERT INTO T224A (T224A_T224_Id) VALUES ('{$T224_Id}')";
            mysql_query($sql);
            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return "Erro ao fazer registro da tabela estendida T224A: {$erro}";
            }        
        }          
        
        mysql_query("call T225_Gravar_Totalizacao('{$T225_Id}')");

        if (!empty($produtoSemCFOP)){
            mysql_query("ROLLBACK");
            return 'O produto ' . $produtoSemCFOP . ' está com CFOP em branco na Tabela do Fornecedor.';
        }
        if (!empty($produtoSemOrigem)){
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
     * atualizarAliquotasPedidos
     *
     * @param int $T224_Id
     * @param int $C004_Id
     */
    public function xatualizarAliquotasPedidos($T224_Id, $C004_Id = false, $r_usarTabelaFornecedor = false)
    {
        global $g;
        require_once('bibliotecas/classes/FIS003.php');
        $FIS003 = new FIS003();

        $C004_Id = ($C004_Id == false) ? $g['empresaAtual'] : $C004_Id;
        
        require_once("bibliotecas/classes/EST015.php");
        $EST015 = new EST015();
        
        if (empty($T224_Id)) {
            return 'O variável T224_Id nao foi passada para o programa: atualizar_aliquotas.php3';
        }
        $cSql_T224 = mysql_query("select * from T224 left join D006 on D006_Id=T224_D006_Id left join D024 on D024_Id=T224_D024_Id where T224_Id='{$T224_Id}'");
        $mT224     = mysql_fetch_array($cSql_T224);
        
        if ($mT224['T224_Flag_Devolucao'] == 'S') {
            return "Este é um pedido para emissão de nota de devolução, não é possivel executar a atualização de aliquotas";
        }
        
        //=====================================================================
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //-- RODA A REGRA CFOP
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        
        $cSql_T225 = mysql_query("SELECT * FROM T224
                                    left join T225 on T224_Id=T225_T224_Id
                                    LEFT JOIN D024 on D024_Id=T225_D024_Id
                                    left join D009 on D009_Id=T225_D009_Id
                                    left join D049 on D049_Id=D009_D049_Id
                                    left join D001 on D001_Id=D049_D001_Id
                                    left join T219 on T219_Id=T225_T219_Id 
                                    where T224_Id='{$T224_Id}'");
        
        $Item = 1;

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        $produtosIncompletos = '';

        if($r_usarTabelaFornecedor==true){
            mysql_query("UPDATE T224 SET T224_Flag_Calcular_ST='S',T224_Flag_Calcular_IPI='S',T224_Flag_Calcular_ICMS='S' WHERE T224_Id='{$T224_Id}'");
        }

        while ($mT225 = mysql_fetch_array($cSql_T225)) {
            
            $Aliquotas = $EST015->retornaAliquotas($mT225['D009_Id'], false, $T224_Id, false, false, $mT225['T225_Id'], false, false, false, $r_usarTabelaFornecedor );

             if($r_usarTabelaFornecedor == true){
                if($Aliquotas['D006_Id']<=0 || $Aliquotas['Origem_Mercadoria'] == ''){
                    $produtosIncompletos .= (empty($produtosIncompletos)) ? $mT225['D001_Codigo_Produto'] : ', '.$mT225['D001_Codigo_Produto'];
                    log('produtosIncompletos: ' . $produtosIncompletos);
                }
                if (!empty($produtosIncompletos)){
                    continue;  
                }
            }

            if ($mT225['T225_Item'] <= 0) {
                $mT225['T225_Item'] = $Item;
            }
            if (!($mT225['T219_Flag_Origem_Produto'] === '') and (!$r_usarTabelaFornecedor)) {
                $Aliquotas['Origem_Mercadoria'] = $mT225['T219_Flag_Origem_Produto'];
            }
            $Item++;

            $demonstrativoNCM = $FIS003->demonstrativoNCM($Aliquotas['D005_Id']);
            $demonstrativoNCM = mysql_real_escape_string($demonstrativoNCM);

            if($r_usarTabelaFornecedor == true){
                $cClausula = " update T225 ";
                $cClausula .= " LEFT JOIN D006 on D006_Id=T225_D006_Id ";
                $cClausula .= " LEFT JOIN D059 on D059_Id=D006_D059_Id ";
                $cClausula .= " LEFT JOIN T224 on T224_Id=T225_T224_Id ";
                $cClausula .= " LEFT JOIN C004 on C004_Id=T224_C004_Id ";
                $cClausula .= " LEFT JOIN D005 on D005_Id=T225_D005_Id ";
                $cClausula .= " LEFT JOIN D149 on D005_Id=D149_D005_Id AND D149_C004_Id=C004_Id";
                $cClausula .= " set ";
                $cClausula .= " T225_Item='{$mT225['T225_Item']}',";
                $cClausula .= " T225_D037_Id='{$mT225['D001_D037_Id']}',";
                $cClausula .= " T225_D006_Id='{$Aliquotas['D006_Id']}',";
                $cClausula .= " T225_Flag_Origem_Produto='{$Aliquotas['Origem_Mercadoria']}',";
                $cClausula .= " T225_Aliquota_IPI='{$Aliquotas['Aliquota_IPI']}',";
                $cClausula .= " T225_Aliquota_PIS='{$Aliquotas['Aliquota_PIS']}',";
                $cClausula .= " T225_Aliquota_COFINS='{$Aliquotas['Aliquota_COFINS']}',";
                $cClausula .= " T225_Aliquota_IPI='{$Aliquotas['Aliquota_IPI']}',";
                $cClausula .= " T225_Percentual_Reducao_IPI='{$Aliquotas['Percentual_Reducao_IPI']}',";
                $cClausula .= " T225_Situacao_Tributaria_IPI='{$Aliquotas['Situacao_Tributaria_IPI']}', ";
                $cClausula .= " T225_Aliquota_ICMS='{$Aliquotas['Aliquota_ICMS']}',";
                $cClausula .= " T225_Percentual_Reducao_ICMS='{$Aliquotas['Percentual_Reducao_ICMS']}',";
                $cClausula .= " T224_Aliquota_ICMS='{$Aliquotas['Aliquota_ICMS']}',";
                $cClausula .= " T224_Percentual_Reducao_ICMS='{$Aliquotas['Percentual_Reducao_ICMS']}',";
                $cClausula .= " T225_Percentual_Reducao_ICMS_ST='{$Aliquotas['Percentual_Reducao_ICMS_ST']}',";
                $cClausula .= " T225_Flag_ST=D006_Flag_Substituicao_Tributaria,";
                $cClausula .= " T225_ST_VA='{$Aliquotas['ST_VA']}', ";
                $cClausula .= " T225_ST_ICMS_Interno='{$Aliquotas['ST_ICMS_Interno']}',";
                $cClausula .= " T225_ICMS_Inter_Estadual='{$Aliquotas['ICMS_Inter_Estadual']}',";
                $cClausula .= " T225_ICMS_Inter_Estadual_MVA='{$Aliquotas['ST_ICMS_Inter_Estadual_MVA']}',";
                $cClausula .= " T225_ST_ICMS_Interno_MVA='{$Aliquotas['ST_ICMS_Interno_MVA']}',";
                $cClausula .= " T225_D005_Id='{$Aliquotas['D005_Id']}',";
                $cClausula .= " T225_D059_Id='{$Aliquotas['D059_Id']}', ";
                if (!empty($mT225['D024_Situacao_Tributaria_PIS_COFINS'])) {
                    $cClausula .= " T225_Situacao_Tributaria_PIS='{$mT225['D024_Situacao_Tributaria_PIS_COFINS']}',";
                    $cClausula .= " T225_Situacao_Tributaria_COFINS='{$mT225['D024_Situacao_Tributaria_PIS_COFINS']}',";
                    if ($mT225['D024_Flag_Isento_PIS_COFINS'] == 'S') {
                        $cClausula .= " T225_Flag_Isento_PIS='{$mT225['D024_Flag_Isento_PIS_COFINS']}',";
                        $cClausula .= " T225_Aliquota_PIS='0',";
                        $cClausula .= " T225_Flag_Isento_COFINS='{$mT225['D024_Flag_Isento_PIS_COFINS']}',";
                        $cClausula .= " T225_Aliquota_COFINS='0',";
                    } else {
                        $cClausula .= " T225_Flag_Isento_PIS=D006_Flag_Isento_PIS,";
                        // $cClausula .= " T225_Aliquota_PIS=if(D149_Aliquota_PIS>0,D149_Aliquota_PIS,C004_Percentual_PIS),";
                        $cClausula .= " T225_Flag_Isento_COFINS=D006_Flag_Isento_COFINS,";
                        // $cClausula .= " T225_Aliquota_COFINS=if(D149_Aliquota_COFINS>0,D149_Aliquota_COFINS,C004_Percentual_COFINS),";
                    }
                } else {
                    $cClausula .= " T225_Flag_Isento_PIS=D006_Flag_Isento_PIS,";
                    // $cClausula .= " T225_Aliquota_PIS=if(D149_Aliquota_PIS>0,D149_Aliquota_PIS,C004_Percentual_PIS),";
                    $cClausula .= " T225_Situacao_Tributaria_PIS=D006_Situacao_Tributaria_PIS,";
                    $cClausula .= " T225_Flag_Isento_COFINS=D006_Flag_Isento_COFINS,";
                    // $cClausula .= " T225_Aliquota_COFINS=if(D149_Aliquota_COFINS>0,D149_Aliquota_COFINS,C004_Percentual_COFINS),";
                    $cClausula .= " T225_Situacao_Tributaria_COFINS=D006_Situacao_Tributaria_COFINS, ";
                }
                $cClausula .= " T225_Classificacao_Fiscal='{$Aliquotas['Classificacao_Fiscal']}',";
                $cClausula .= " T225_Flag_Situacao_Tributaria='{$Aliquotas['Flag_Situacao_Tributaria']}', ";
                $cClausula .= " T225_Peso_Unitario='{$mT225['D001_Peso_Unitario_Kg']}', ";
                $cClausula .= " T225_Percentual_Trib_Transparencia='{$Aliquotas['Percentual_Trib_Transparencia']}', ";
                $cClausula .= " T225_D005_Id_Info='{$demonstrativoNCM}', ";
                $cClausula .= " T225_Percentual_ST='{$Aliquotas['Percentual_ST']}',";
                $cClausula .= " T225_Valor_Preco_Unitario='{$Aliquotas['Valor_Item_Tabela_Fornecedor']}'";
                $cClausula .= " where T225_Id='{$mT225['T225_Id']}'";

            } else {
                
                $cClausula = " update T225 ";
                $cClausula .= " LEFT JOIN D006 on D006_Id=T225_D006_Id ";
                $cClausula .= " LEFT JOIN D059 on D059_Id=D006_D059_Id ";
                $cClausula .= " LEFT JOIN T224 on T224_Id=T225_T224_Id ";
                $cClausula .= " LEFT JOIN C004 on C004_Id=T224_C004_Id ";
                $cClausula .= " LEFT JOIN D005 on D005_Id=T225_D005_Id ";
                $cClausula .= " LEFT JOIN D149 on D005_Id=D149_D005_Id AND D149_C004_Id=C004_Id";
                $cClausula .= " set ";
                $cClausula .= " T225_Item='{$mT225['T225_Item']}',";
                $cClausula .= " T225_D037_Id='{$mT225['D001_D037_Id']}',";
                if($mT224['T224_Flag_Calcular_IPI'] == 'S'){
                    $cClausula .= " T225_Aliquota_IPI='{$Aliquotas['Aliquota_IPI']}',";
                    $cClausula .= " T225_Percentual_Reducao_IPI='{$Aliquotas['Percentual_Reducao_IPI']}',";
                    $cClausula .= " T225_Situacao_Tributaria_IPI='{$Aliquotas['Situacao_Tributaria_IPI']}', ";
                }
                if($mT224['T224_Flag_Calcular_ICMS'] == 'S'){
                    $cClausula .= " T225_Aliquota_ICMS='{$Aliquotas['Aliquota_ICMS']}',";
                    $cClausula .= " T225_Percentual_Reducao_ICMS='{$Aliquotas['Percentual_Reducao_ICMS']}',";
                    $cClausula .= " T224_Aliquota_ICMS='{$Aliquotas['Aliquota_ICMS']}',";
                    $cClausula .= " T224_Percentual_Reducao_ICMS='{$Aliquotas['Percentual_Reducao_ICMS']}',";
                }
                if($mT224['T224_Flag_Calcular_ST'] == 'S'){
                    $cClausula .= " T225_Percentual_Reducao_ICMS_ST='{$Aliquotas['Percentual_Reducao_ICMS_ST']}',";
                    $cClausula .= " T225_Flag_ST=D006_Flag_Substituicao_Tributaria,";
                    $cClausula .= " T225_ST_VA='{$Aliquotas['ST_VA']}', ";
                    $cClausula .= " T225_ST_ICMS_Interno='{$Aliquotas['ST_ICMS_Interno']}',";
                    $cClausula .= " T225_ICMS_Inter_Estadual='{$Aliquotas['ICMS_Inter_Estadual']}',";
                    $cClausula .= " T225_ICMS_Inter_Estadual_MVA='{$Aliquotas['ST_ICMS_Inter_Estadual_MVA']}',";
                    $cClausula .= " T225_ST_ICMS_Interno_MVA='{$Aliquotas['ST_ICMS_Interno_MVA']}',";
                }
                $cClausula .= " T225_D005_Id='{$Aliquotas['D005_Id']}',";
                $cClausula .= " T225_D059_Id='{$Aliquotas['D059_Id']}', ";
                if (!empty($mT225['D024_Situacao_Tributaria_PIS_COFINS'])) {
                    $cClausula .= " T225_Situacao_Tributaria_PIS='{$mT225['D024_Situacao_Tributaria_PIS_COFINS']}',";
                    $cClausula .= " T225_Situacao_Tributaria_COFINS='{$mT225['D024_Situacao_Tributaria_PIS_COFINS']}',";
                    if ($mT225['D024_Flag_Isento_PIS_COFINS'] == 'S') {
                        $cClausula .= " T225_Flag_Isento_PIS='{$mT225['D024_Flag_Isento_PIS_COFINS']}',";
                        $cClausula .= " T225_Aliquota_PIS='0',";
                        $cClausula .= " T225_Flag_Isento_COFINS='{$mT225['D024_Flag_Isento_PIS_COFINS']}',";
                        $cClausula .= " T225_Aliquota_COFINS='0',";
                    } else {
                        $cClausula .= " T225_Flag_Isento_PIS=D006_Flag_Isento_PIS,";
                        // $cClausula .= " T225_Aliquota_PIS=if(D149_Aliquota_PIS>0,D149_Aliquota_PIS,C004_Percentual_PIS),";
                        $cClausula .= " T225_Flag_Isento_COFINS=D006_Flag_Isento_COFINS,";
                        // $cClausula .= " T225_Aliquota_COFINS=if(D149_Aliquota_COFINS>0,D149_Aliquota_COFINS,C004_Percentual_COFINS),";
                    }
                } else {
                    $cClausula .= " T225_Flag_Isento_PIS=D006_Flag_Isento_PIS,";
                    // $cClausula .= " T225_Aliquota_PIS=if(D149_Aliquota_PIS>0,D149_Aliquota_PIS,C004_Percentual_PIS),";
                    $cClausula .= " T225_Situacao_Tributaria_PIS=D006_Situacao_Tributaria_PIS,";
                    $cClausula .= " T225_Flag_Isento_COFINS=D006_Flag_Isento_COFINS,";
                    // $cClausula .= " T225_Aliquota_COFINS=if(D149_Aliquota_COFINS>0,D149_Aliquota_COFINS,C004_Percentual_COFINS),";
                    $cClausula .= " T225_Situacao_Tributaria_COFINS=D006_Situacao_Tributaria_COFINS, ";
                }
                $cClausula .= " T225_Classificacao_Fiscal='{$Aliquotas['Classificacao_Fiscal']}',";
                $cClausula .= " T225_Flag_Situacao_Tributaria='{$Aliquotas['Flag_Situacao_Tributaria']}', ";
                $cClausula .= " T225_Peso_Unitario='{$mT225['D001_Peso_Unitario_Kg']}', ";
                $cClausula .= " T225_Percentual_Trib_Transparencia='{$Aliquotas['Percentual_Trib_Transparencia']}', ";
                $cClausula .= " T225_Percentual_ST='0',";
                $cClausula .= " T225_D005_Id_Info='{$demonstrativoNCM}' ";
                $cClausula .= " where T225_Id='{$mT225['T225_Id']}'";
            }
            //die($cClausula);
            mysql_query($cClausula);
             $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return "Erro atualizando item do orcamento ID {$mT219['T219_Id']}: $erro";
            }
            
            mysql_query("call T225_Gravar_Totalizacao('{$mT225['T225_Id']}')");
        }
        if (!empty($produtosIncompletos)){
            mysql_query("ROLLBACK");
            return 'Os produtos ' . $produtosIncompletos . ' estão com CFOP ou Origem em branco na tabela do fornecedor.';
            log("entrei");
        }
        mysql_query("COMMIT");
        return "Atualizado com êxito!";
    }
    
    /**
     * faturarPedido
     *
     * @param int $T224_Id
     */
    public function faturarPedido($T224_Id)
    {
        global $g;
        global $Perfil_Usuario, $mensagem;
        
        $permitirVenda = $g['C031']['permitirVendaSemEstoque'];
        $permitirVenda = (!empty($permitirVenda) ? $permitirVenda : 'N');
        $sqlEstoque    = mysql_query("SELECT T225_Id FROM T225 WHERE T225_T224_Id='{$T224_Id}' AND T225_Quantidade>T225_Quantidade_Estoque AND '{$permitirVenda}'='N' AND (T225_T206_Id IS NULL OR T225_T206_Id = '')");
        $rowsEstoque   = mysql_num_rows($sqlEstoque);
        if ($rowsEstoque > 0) {
            $msg = ($rowsEstoque > 1 ? "Há itens sem estoque suficiente, favor verificar" : "Existe item sem estoque suficiente, favor verificar");
            return $msg;
        }
        
        //PARA SETAR A EXPEDIÇÃO PADRÃO DO PEDIDO, EXISTE A VARIÁVEL GLOBAL ABAIXO QUE É CONFIGURADA DENTRO DE _GLOBAL
        $statusPedido = $g['C031']['statusInicialPedido'];
        $sql          = "SELECT T224_D024_Id,T224_D022_Id,T224_Flag_Status,T224_Ordem_Compra, D006_Flag_Gera_Contas, T224_Valor_Total_Produtos, T224_Valor_Total FROM T224 LEFT JOIN D006 on D006_Id=T224_D006_Id WHERE T224_Id='{$T224_Id}'";
        $resultado    = mysql_query($sql);
        $row          = mysql_fetch_array($resultado);
        
        $sql2       = "SELECT D024_Flag_Liberado,D024_Flag_Restricao FROM D024 WHERE D024_Id='{$row['T224_D024_Id']}'";
        $resultado2 = mysql_query($sql2);
        $row2       = mysql_fetch_array($resultado2);
        
        $restricaoProduto = mysql_query("SELECT T225_Id FROM T225 WHERE T225_T224_Id='{$T224_Id}' AND (T225_D009_Id='' OR T225_D009_Id IS NULL OR T225_D009_Id=0)");
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
        if ($row['T224_Flag_Status'] == $statusPedido) {
            return "Este pedido já foi faturado!";
        }
        
        /**
         * Verifica se é obrigatório forma de pagamento
         */
        $faturarSemPreco = $g['C031']['permissaoFaturarSemPreco'];
        if ($row['D006_Flag_Gera_Contas'] == "R" && $faturarSemPreco == 'N') {
            $T226 = mysql_query("SELECT SUM(T226_Valor_Parcela) as TotalParcelas FROM T226 WHERE T226_T224_Id='{$T224_Id}'");
            if (mysql_num_rows($T226) <= 0) {
                return "É necessário preencher a forma de pagamento";
            } else {
                $resT226 = mysql_fetch_array($T226);
                if ($row['T224_Valor_Total'] != $resT226['TotalParcelas']) {
                    return "O valor total da forma de pagamento difere do valor total do pedido";
                }
            }
        }
        
        /**
         *  Verifica se foi preenchido a transportadora
         */
        $usarTransportadora = $g['C031']['usarTransportadoraPedido'];
        if ((empty($row['T224_D022_Id']) || $row['T224_D022_Id'] == 0 || $row['T224_D022_Id'] == null) AND $usarTransportadora == 'S') {
            return "Por favor preencha o campo \"Transportadora\" no cabeçalho do pedido!";
        }
        
        //Verifica se a empresa usa o campo Ordem de Comprar como obrigatório
        $usarOrdemCompra = $g['C031']['usarOrdemCompra'];
        if ($usarOrdemCompra == 'S') {
            if (empty($row['T224_Ordem_Compra'])) {
                return "Por favor preencha o campo \"Ordem de Compra\" no cabeçalho do pedido!";
            }
        }
        
        $T225Pre = mysql_query("SELECT T225_Id FROM T225 WHERE T225_T224_Id='{$T224_Id}' AND T225_Flag_Pre_Cadastro='S'");
        if (mysql_num_rows($T225Pre) > 0) {
            return "Existem itens em pré-cadastro. Por favor conclua o cadastro dos itens.<br />Caso já tenha sido incluído, pressione o botão 'Atualizar Alíquotas', logo em seguida 'Atualizar CFOP' e por fim 'Aplicar regras CFOP'.";
        }
        
        if ($statusPedido == "1") {
            $fraseStatus = "Aguardando Impressão";
        }
        if ($statusPedido == "10") {
            $fraseStatus = "Aguardando Conferência";
        }
        if ($statusPedido == "3") {
            $fraseStatus = "Aguardando Emissão de NF";
        }
        
        /**
         * Fatura o Pedido
         */
        $duplicarPedido = $g['conf']->ini('faturamentoMultiEmpresa');
        
        
        $creditoLiberado = $this->verificaCreditoLiberado($T224_Id);
        
        if ($creditoLiberado == false) {
            
            mysql_query("update T224 set T224_Flag_Status=6, T224_Nome_Status = T224_Status_Pedido(T224_Flag_Status, T224_Id), T224_Flag_Separacao_Liberar='S', T224_Flag_Separacao='S' where T224_Id='{$T224_Id}'");
            //Insere registros no histórico do pedido (T228)
            mysql_query("insert into T228(T228_T224_Id,T228_C007_Id,T228_Data,T228_Descricao,T228_Observacao) values ({$T224_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Aguardando Liberação','')");
            $mensagem       = "ATENÇÂO!!!<br />O cliente possui restrições! Pedido foi enviado para análise de crédito.";
            $duplicarPedido = false;
            
        } else {
            
            mysql_query("update T224 set T224_Flag_Status='{$statusPedido}', T224_Nome_Status = T224_Status_Pedido(T224_Flag_Status, T224_Id), T224_Flag_Separacao='S', T224_Flag_Separacao_Liberar='N' where T224_Id='{$T224_Id}'");
            //Insere registros no histórico do pedido (T228)
            mysql_query("insert into T228(T228_T224_Id,T228_C007_Id,T228_Data,T228_Descricao,T228_Observacao) values ({$T224_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'{$fraseStatus}','')");
            if ($statusPedido == 3)
                $mensagem = "Pedido enviado para emissão de NF";
            else
                $mensagem = "Pedido enviado para expedição";
        }
        
        // T185
        include('prog/outros/distribuicaoItemPedidoEstoque.php');
        
        if ($duplicarPedido) {
            $duplica = $this->enviarPedidoMatriz($T224_Id);
            if ($duplica !== true)
                $mensagem = $duplica;
        }
        return true;
    }
    
    /**
     * enviarPedidoMatriz
     *
     * @param int $T224_Id
     */
    public function enviarPedidoMatriz($T224_Id)
    {
        global $g;
        global $Perfil_Usuario, $mensagem;
        
        if ($g['conf']->ini('faturamentoMultiEmpresaIdMatriz') > 0) {
            $matriz = $g['conf']->ini('faturamentoMultiEmpresaIdMatriz');
        } else {
            return 'não foi definida a empresa matriz na flag faturamentoMultiEmpresaIdMatriz, favor verificar (ARQUIVO GLOBAL)';
        }
        
        if ($g['C004']['C004_D024_Id'] > 0) {
            $D024_Id_Atual = $g['C004']['C004_D024_Id'];
        } else {
            return 'Esta empresa não está associada a nenhum cliente<br />favor fazer esta associação em Configuração -> Empresas';
        }
        mysql_query("START TRANSACTION");
        
        // efetuar a cópia do pedido para a matriz                                                                                                              
        mysql_query(gRetornaSqlDuplicacao('T224', $T224_Id));
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return "Erro ao duplicar pedido";
        }
        $T224_Id_Duplicado = $g['mysqlLastId'];
        
        $T224_orig = mysql_query("SELECT * FROM T224 LEFT JOIN D024 ON D024_Id=T224_D024_Id LEFT JOIN D020 ON D020_Id=D024_D020_Id LEFT JOIN D018 ON D018_Id=D024_D018_Id WHERE T224_Id='{$T224_Id}'");
        $T224_orig = mysql_fetch_array($T224_orig);
        $obsFilial = <<<EOT
        Pedido filial: {$T224_Id}<br />
        Cliente: {$T224_orig['D024_Nome_Empresa']}<br />
        Fantasia: {$T224_orig['D024_Nome_Fantasia']}<br />
        CNPJ: {$T224_orig['D024_Cnpj']}<br />
        Cidade/UF: {$T224_orig['D020_Nome_Cidade']}/{$T224_orig['D018_UF']}<br />
        CEP/Bairro: {$T224_orig['D024_Bairro']}/{$T224_orig['D024_Cep']}<br />
        Obs almoxarifado: {$T224_orig['T224_Observacao_1']}
EOT;
        $obsFilial = str_replace("'", "", $obsFilial);
        
        mysql_query("UPDATE T224 set T224_C004_Id='{$matriz}', T224_Flag_Status=1, T224_Nome_Status = T224_Status_Pedido(T224_Flag_Status, T224_Id), T224_Flag_Almoxarifado='S',T224_Observacao_1='{$obsFilial}',T224_D024_Id='{$D024_Id_Atual}', T224_T224_Id_Origem='{$T224_Id}' WHERE T224_Id='{$T224_Id_Duplicado}'");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return "Erro ao editar pedido (duplicado)";
        }
        
        $result = mysql_query("select T225_Id, D009_D049_Id from T225 left join D009 on D009_Id=T225_D009_Id where T225_T224_Id='{$T224_Id}'");
        while ($linha = mysql_fetch_array($result)) {
            mysql_query(gRetornaSqlDuplicacao('T225', $linha['T225_Id']));
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return "Erro ao duplicar item do pedido";
            }
            
            $T225_Id_Duplicado = $g['mysqlLastId'];
            // busca o D009 do item na matriz (D009_Valor_Custo_Compra trocado por D009_Valor_Custo_Unitario)
            $retorno           = mysql_query("select D009_Id,D009_Quantidade_Estoque_Liquido,D009_Valor_Custo_Unitario from D009 where D009_D049_Id='{$linha['D009_D049_Id']}' and D009_C004_Id=$matriz");
            $ret               = mysql_fetch_array($retorno);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return "Erro ao buscar D009_Id da matriz";
            }
            if (mysql_num_rows($retorno) != 1) {
                mysql_query("ROLLBACK");
                return "Há itens não cadastrado na Matriz, verifique";
            }
            if ($ret['D009_Valor_Custo_Unitario'] <= 0) {
                mysql_query("ROLLBACK");
                return "Há itens com custo zero na Matriz, verifique";
            }
            
            $precoReduzido = $ret['D009_Valor_Custo_Unitario'] * ($g['conf']->ini('faturamentoMultiEmpresaPercentual') / 100);
            mysql_query("UPDATE T225 set T225_T224_Id=$T224_Id_Duplicado,T225_Valor_Custo_Unitario={$ret['D009_Valor_Custo_Unitario']},T225_Quantidade_Estoque={$ret['D009_Quantidade_Estoque_Liquido']},T225_D009_Id='{$ret['D009_Id']}', T225_Quantidade_Separacao=0, T225_C004_Id=$matriz, T225_Valor_Preco_Sem_Desconto_Unitario=$precoReduzido WHERE T225_Id=$T225_Id_Duplicado");
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return "Erro ao editar item do pedido (duplicado)";
            }
            
            //Duplicação dos Agregados do Produto
            $sqlT204 = mysql_query("SELECT * FROM T204 WHERE T204_T225_Id = '{$linha['T225_Id']}'");
            while ($T204 = mysql_fetch_array($sqlT204)) {
                mysql_query(gRetornaSqlDuplicacao('T204', $T204['T204_Id']));
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return "Erro ao duplicar os agregados do item do pedido";
                }
                
                $T204_Id_Duplicado = $g['mysqlLastId'];
                mysql_query("UPDATE T204 SET T204_T225_Id = '{$T225_Id_Duplicado}' WHERE T204_Id = '{$T204_Id_Duplicado}'");
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return "Erro ao editar agregado do item (duplicado)";
                }
            }
        }
        // apenas para chamar a trigger para corrigir os totais deste pedido (aproveitando pra setar flag almoxarifado)
        mysql_query("UPDATE T224 set T224_C004_Id=T224_C004_Id,T224_Flag_Almoxarifado='S', T224_T224_Id_Origem='{$T224_Id_Duplicado}' WHERE T224_Id=$T224_Id");
        $T224_d  = mysql_query("select T224_Valor_Total from T224 where T224_Id=$T224_Id_Duplicado");
        $mT224_d = mysql_fetch_array($T224_d);
        
        // insere parcelas para pagamento da filial (dinheiro a vista)
        $cClausula = "insert into T226 (";
        $cClausula .= "T226_T224_Id,";
        $cClausula .= "T226_D031_Id,";
        $cClausula .= "T226_Numero_Parcela,";
        $cClausula .= "T226_Prazos_Dias,";
        $cClausula .= "T226_Data_Vencimento,";
        $cClausula .= "T226_Valor_Parcela";
        $cClausula .= ") values (";
        $cClausula .= "'$T224_Id_Duplicado',";
        $cClausula .= "'3',";
        $cClausula .= "'1',";
        $cClausula .= "'1',";
        $cClausula .= "CURDATE()+1,";
        $cClausula .= "'{$mT224_d['T224_Valor_Total']}'";
        $cClausula .= ")";
        mysql_query($cClausula);
        
        //Insere registros no histórico do pedido (T228)
        $fraseStatus = "Pedido Gerado (via filial)";
        mysql_query("insert into T228(T228_T224_Id,T228_C007_Id,T228_Data,T228_Descricao,T228_Observacao) values ({$T224_Id_Duplicado},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'{$fraseStatus}','')");
        mysql_query("COMMIT");
        $retorno = $this->est016atualizarCFOP($T224_Id_Duplicado);
        if ($retorno !== true) {
            return "Pedido Matriz: " . $retorno;
        }
        //mysql_query("COMMIT");                        
        
        // T185
        $T224_Id = $T224_Id_Duplicado;
        include('prog/outros/distribuicaoItemPedidoEstoque.php');
        
        return true;
    }
    
    /**
     * faturarEmergencia
     *
     * @param int $T224_Id
     * @param Form $form
     */
    public function faturarEmergencia($T224_Id, $form)
    {
        $Observacao                = $form->campoValorEnviado('T224_Observacao_Fatura_Emergencia');
        $T224_Liberacao_Credito_Id = $form->campoValorEnviado('T224_Liberacao_Credito_Id');
        
        global $g;
        global $Perfil_Usuario;
        
        mysql_query("update T224 set T224_Flag_Status=3, T224_Nome_Status = T224_Status_Pedido(T224_Flag_Status, T224_Id), T224_Liberacao_Credito_Id='{$T224_Liberacao_Credito_Id}',T224_Observacao_Fatura_Emergencia='{$Observacao}' where T224_Id='{$T224_Id}'");
        
        //Insere registros no histórico do pedido (T228)
        mysql_query("insert into T228(T228_T224_Id,T228_C007_Id,T228_Data,T228_Descricao,T228_Observacao) values ({$T224_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Aguardando Emissão NF','')");
        
        return true;
    }
    
    /**
     * atualizarParcelas
     *
     * @param Form $form
     */
    public function atualizarParcelas($form)
    {
        global $g;
        
        $T224_Id                = $form->campoValorEnviado('T224_Id');
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
        $prazosPagamento        = "";
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
        
        
        $cSql_T224 = mysql_query("select * from T224 where T224_Id='{$T224_Id}'");
        $mT224     = mysql_fetch_array($cSql_T224);
        
        // se for "a combinar" remove as parcelas que ja existem
        if ($numeroParcelas == '0') {
            mysql_query("delete T226 from T226 where T226_T224_Id='{$T224_Id}'");
            mysql_query("UPDATE T224 SET T224_Prazos_Pagamentos = '' WHERE T224_Id='{$T224_Id}'");
            if (mysql_error())
                return mysql_error();
            else
                return true;
        }
        
        $cSql_T226 = mysql_query("select * from T226 left join D031 on D031_Id=T226_D031_Id where T226_T224_Id='{$T224_Id}'");
        while ($mT226 = mysql_fetch_array($cSql_T226)) {
            $mPrazoAntigo[$mT226['T226_Numero_Parcela']]    = $mT226['T226_Prazos_Dias'];
            $mPortadorAntigo[$mT226['T226_Numero_Parcela']] = $mT226['T226_D031_Id'];
        }
        
        mysql_query("delete T226 from T226 where T226_T224_Id='{$T224_Id}'");
        
        
        $Valor_Total_IPI       = $mT224['T224_Valor_Total_IPI'];
        $Valor_Total_Orcamento = $mT224['T224_Valor_Total'];
        
        /**
         * Caso a variável global usarFretePrimeiraParcela for igual a SIM, está tirando o frete do valor total para adicionar a primeira parcela
         */
        $usarFretePrimeiraParcela = $g['C031']['usarFretePrimeiraParcela'];
        if ($usarFretePrimeiraParcela == "S" && $campoUsarFreteParcela == "on") {
            $Valor_Total_Orcamento = ($Valor_Total_Orcamento - $mT224['T224_Valor_Frete']);
        }
        
        if ($campoUsarSTParcela == "on") {
            $Valor_Total_Orcamento = ($Valor_Total_Orcamento - $mT224['T224_Valor_ICMS_Substituicao_Retencao']);
        }
        if ($campoUsarIPIParcela == "on") {
            $Valor_Total_Orcamento = ($Valor_Total_Orcamento - $mT224['T224_Valor_Total_IPI']);
        }
        
        if ($Total_Parcelas > 0) {
            $Valor_Titulo_Parcela = ($Valor_Total_Orcamento - $Valor_Total_IPI) / $Total_Parcelas;
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
                    $Valor_Titulo = $Valor_Titulo_Parcela + $Diferenca_Primeira_Parcela;
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
                    $Valor_Titulo += $mT224['T224_Valor_Frete'];
                }
                
                if ($x == 1 && $campoUsarSTParcela == "on") {
                    $Valor_Titulo += $mT224['T224_Valor_ICMS_Substituicao_Retencao'];
                }
                if ($x == 1 && $campoUsarIPIParcela == "on") {
                    $Valor_Titulo += $mT224['T224_Valor_Total_IPI'];
                }
                
                $cClausula = "insert into T226 (";
                $cClausula .= "T226_T224_Id,";
                $cClausula .= "T226_D031_Id,";
                $cClausula .= "T226_Numero_Parcela,";
                $cClausula .= "T226_Prazos_Dias,";
                $cClausula .= "T226_Data_Vencimento,";
                $cClausula .= "T226_Taxa_Juros,";
                $cClausula .= "T226_Valor_Juros,";
                $cClausula .= "T226_Valor_Parcela";
                $cClausula .= ") values (";
                $cClausula .= "'$T224_Id',";
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
                $prazosPagamento .= $mPrazoAntigo[$x]."/";
                log($prazosPagamento);
            }
            // ou se foi colocado um dia fixo   
        } else if ($diaFixo > 0) {
            if ($diaFixo > 31)
                return "Por favor, insira um dia válido";
            $mes = date("m");
            $ano = date("Y");
            
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
                    // poder calcular os dias até o Vencimento da parcela (T226_Prazos_Dias)                        
                    $date           = strtotime("+$numMeses month", strtotime($dataVencimento));
                    $dataPrazoDias  = date("Y-m-d", $date);
                    $numMeses       = $x - 1;
                }
                
                if ($usarFretePrimeiraParcela == "S" && $x == 1) {
                    $Valor_Titulo += $mT224['T224_Valor_Frete'];
                }
                
                if ($campoUsarSTParcela == "on" && $x == 1) {
                    $Valor_Titulo += $mT224['T224_Valor_ICMS_Substituicao_Retencao'];
                }
                
                $cClausula = "insert into T226 (";
                $cClausula .= "T226_T224_Id,";
                $cClausula .= "T226_D031_Id,";
                $cClausula .= "T226_Numero_Parcela,";
                $cClausula .= "T226_Data_Vencimento,";
                $cClausula .= "T226_Prazos_Dias,";
                $cClausula .= "T226_Taxa_Juros,";
                $cClausula .= "T226_Valor_Juros,";
                $cClausula .= "T226_Valor_Parcela";
                $cClausula .= ") values (";
                $cClausula .= "'$T224_Id',";
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
                $prazosPagamento .= $mPrazoAntigo[$x]."/";
                log($prazosPagamento);
            }
        }
        $prazosPagamento = substr($prazosPagamento, 0, -1);
        $sqlT224    = <<<EOT
            UPDATE T224 SET
            T224_Pgto_Parcelas='{$Total_Parcelas}',
            T224_Pgto_Primeiro_Vcto_Tipo='{$primeiroVencimentoTipo}',
            T224_Pgto_Primeiro_Vcto_Valor='{$primeiroVencimentoValor}',
            T224_Pgto_Dias_Vcto_Tipo='{$diasVctoTipo}',
            T224_Pgto_Dias_Vcto_Valor='{$diasVctoValor}',
            T224_Pgto_Portador='{$D031_Id}',
            T224_Pgto_Data=CURRENT_TIMESTAMP(),
            T224_Pgto_C007_Id='{$g['usuarioAtual']}'
            WHERE T224_Id='{$T224_Id}'
EOT;
        $resultT224 = mysql_query($sqlT224);
        if (mysql_error()) {
            return mysql_error();
        }
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
        $T222_T224_Id              = $form->campoValorEnviado('T222_T224_Id');
        $T222_C004_Id              = $form->campoValorEnviado('T222_C004_Id');
        $T222_D009_Id              = $form->campoValorEnviado('T222_D009_Id');
        
        if ($T222_D009_Id <= 0)
            return "nenhum produto foi selecionado";
        
        $cClausula = " insert into T222 (";
        $cClausula .= " T222_T224_Id, ";
        $cClausula .= " T222_C004_Id, ";
        $cClausula .= " T222_D009_Id, ";
        $cClausula .= " T222_Operacao, ";
        $cClausula .= " T222_Valor_Preco_Unitario, ";
        $cClausula .= " T222_Quantidade, ";
        $cClausula .= " T222_Observacao ";
        $cClausula .= " ) values ( ";
        $cClausula .= " '$T222_T224_Id', ";
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
     * @param boolean $tipo
     */
    public function incluirProduto($form, $tipo = null)
    {
        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();
        require_once('bibliotecas/classes/EST015.php');
        $EST015 = new EST015();
        
        global $g, $T225_Id;
        
        $T225_D009_Id              = $form->campoValorEnviado('T225_D009_Id');
        $T225_D005_Id              = $form->campoValorEnviado('T225_D005_Id');
        $T225_D006_Id              = $form->campoValorEnviado('T225_D006_Id');
        $T225_Quantidade           = $form->campoValorEnviado('T225_Quantidade');
        $T225_Valor_Preco_Unitario = $form->campoValorEnviado('T225_Valor_Preco_Sem_Desconto_Unitario');
        $T225_D037_Id              = $form->campoValorEnviado('T225_D037_Id');
        $T225_Descricao_Produto    = mysql_real_escape_string($form->campoValorEnviado('T225_Descricao_Produto'));
        $T225_Codigo_Produto       = $form->campoValorEnviado('T225_Codigo_Produto');
        $T225_IPV                  = $form->campoValorEnviado('T225_IPV');
        $T224_Id                   = $form->campoValorEnviado('T225_T224_Id');
        $T225_Codigo_Substituto    = $form->campoValorEnviado('T225_Codigo_Substituto');
        $T225_Numero_Pedido_Compra = $form->campoValorEnviado('T225_Numero_Pedido_Compra');
        $T225_Item_Pedido_Compra   = $form->campoValorEnviado('T225_Item_Pedido_Compra');
        $T225_Observacao           = $form->campoValorEnviado('T225_Observacao');
        $T225_Aliquota_IPI         = $form->campoValorEnviado('T225_Aliquota_IPI');
        $idVeiculoPedido           = $form->campoValorEnviado('idVeiculoPedido');
        $T225_Flag_Origem_Produto  = $form->campoValorEnviado('T225_Flag_Origem_Produto');
        $T225_Observacao_Entrega   = $form->campoValorEnviado('T225_Observacao_Entrega');
        
        $T225_D022_Id                 = $form->campoValorEnviado('T225_D022_Id');
        $T225_D144_Id                 = $form->campoValorEnviado('T225_D144_Id');
        $T225_T208_Id                 = $form->campoValorEnviado('T225_T208_Id');
        $T225_Hora_Entrega            = $form->campoValorEnviado('T225_Hora_Entrega');
        $T225_Flag_Destino_Produto    = $form->campoValorEnviado('T225_Flag_Destino_Produto');
        $T225_Percentual_Desconto     = gCorrigeNumeroInverte($form->campoValorEnviado('T225_Percentual_Desconto'));
        $T225A_Data_Entrega           = gCorrigeDataInverte($form->campoValorEnviado('T225A_Data_Entrega'));
        $T225A_Prazo_Faturamento      = gCorrigeDataInverte($form->campoValorEnviado('T225A_Prazo_Faturamento'));
        $T225A_Valor_Dolar_Unitario   = gCorrigeNumeroInverte($form->campoValorEnviado('T225A_Valor_Dolar_Unitario'));
        //$NcmProduto=$form->campoValorEnviado('NcmProduto');
        //$T225_Percentual_Desconto=0;
        $T225_Valor_Desconto_Unitario = 0;
        $T225_Id                      = '';
        
        $T225_Quantidade           = gCorrigeNumeroInverte($T225_Quantidade);
        $T225_Valor_Preco_Unitario = gCorrigeNumeroInverte($T225_Valor_Preco_Unitario);

        $T225A_Valor_Dolar_Total = $T225_Quantidade * $T225A_Valor_Dolar_Unitario;
        
        $Aliquotas = $EST015->retornaAliquotas($T225_D009_Id, false, $T224_Id);
        
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
                            where D009_Id='$T225_D009_Id'");
        $mD001 = mysql_fetch_array($D001);
        
        $T225_D001_Id        = $mD001['D001_Id'];
        $T225_Codigo_Produto = $mD001['D001_Codigo_Produto'];
        
        $C007  = mysql_query("select *
                             from C007
                            where C007_Id={$g['usuarioAtual']}");
        $mC007 = mysql_fetch_array($C007);
        
        $T225  = mysql_query("select T225_Item
                                  from T225
                                 where T225_T224_Id='$T224_Id'
                              order by T225_Item desc
                                 limit 1");
        $mT225 = mysql_fetch_array($T225);
        
        $T225_Item = $mT225['T225_Item'] + 1;
        
        $cClausula = " select ";
        $cClausula .= " D009_Id, ";
        $cClausula .= " D005_Flag_ST, ";
        $cClausula .= " D009_Valor_Custo_Unitario, ";
        $cClausula .= " D009_Flag_Preco_Tabelado,
                        D009_Quantidade_Estoque_Liquido ";
        $cClausula .= " from D001 ";
        $cClausula .= " left join D049 on D001_Id=D049_D001_Id ";
        $cClausula .= " left join D009 on D049_Id=D009_D049_Id ";
        $cClausula .= " left join D005 on D001_D005_Id=D005_Id ";
        $cClausula .= " where D009_Id='$T225_D009_Id' ";
        $T225  = mysql_query($cClausula);
        $mT225 = mysql_fetch_array($T225);
        
        $erro = mysql_error();
        if (!empty($erro)) {
            return "Erro consulta custo produto: $erro";
        }
        
        if (($mT225['D009_Id']) <= 0) {
            return "Erro: Produto não encontrado";
        }
        
        $cClausula = " select *, ";
        $cClausula .= " D053_ST_ICMS_Interno, D053_ST_ICMS_Inter_Estadual, D053_Flag_ST, D018_Id, C004_D018_Id, C004_CFOP_ST_Venda_Dentro_Estado, C004_CFOP_ST_Venda_Fora_Estado, D006_Flag_Venda_Compra_Outros ";
        $cClausula .= " from T224 ";
        $cClausula .= " left join D024 on D024_Id=T224_D024_Id ";
        $cClausula .= " left join D018 on D018_Id=D024_D018_Id ";
        $cClausula .= " left join D053 on D053_D018_Id=D024_D018_Id and D053_C004_Id=T224_C004_Id ";
        $cClausula .= " left join C004 on C004_Id=T224_C004_Id ";
        $cClausula .= " left join D006 on D006_Id=T224_D006_Id ";
        $cClausula .= " where T224_Id='$T224_Id' ";
        $T224  = mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "$cErro/p/p$cClausula";
            exit;
        }
        $mT224 = mysql_fetch_array($T224);
        
        $D024_Id = $mT224['T224_D024_Id'];
        
        // verifica qual preço usar, baseado na lista preço do orçamento ou do cliente
        $listPreco    = mysql_query("SELECT T224_Flag_Lista_Preco FROM T224 WHERE T224_Id='{$T224_Id}'");
        $mLista_Preco = mysql_fetch_array($listPreco);
        
        if (!empty($mLista_Preco['T224_Flag_Lista_Preco'])) {
            $Preco_Tabelado = $CAD002->cad002RetornaPrecoCompra($mT225['D009_Id'], $mLista_Preco['T224_Flag_Lista_Preco'], null);
        } else {
            $Preco_Tabelado = $CAD002->cad002RetornaPrecoCompra($mT225['D009_Id'], null, $D024_Id);
        }
        $Preco_Tabelado = gCorrigeNumeroInverte($Preco_Tabelado);
                
        $demonstrativoCFOP = $EST015->retornaCFOP($D024_Id, $mT225['D009_Id'], $mT224['C004_Id'], $T225_Flag_Origem_Produto, true,false,$T225_Flag_Destino_Produto);
        $T225_D006_Id = empty($T225_D006_Id) ? $EST015->retornaCFOP($D024_Id, $mT225['D009_Id'], $mT224['C004_Id'], $T225_Flag_Origem_Produto, false,false,$T225_Flag_Destino_Produto) : $T225_D006_Id;
        $demonstrativoCFOP = mysql_real_escape_string($demonstrativoCFOP);

        if ($T225_Valor_Preco_Unitario <= 0) {
            $T225_Valor_Preco_Unitario = $Preco_Tabelado;
        }
        
        if ($T225_Percentual_Desconto > 0) {
            
            $T225_Valor_Preco_Unitario = $T225_Valor_Preco_Unitario - ($T225_Valor_Preco_Unitario * ($T225_Percentual_Desconto / 100));
            
            if ($Preco_Tabelado > 0) {
                $T225_Percentual_Desconto     = ((1 - ($T225_Valor_Preco_Unitario / $Preco_Tabelado)) * 100);
                $T225_Valor_Desconto_Unitario = (($Preco_Tabelado * ($T225_Valor_Preco_Unitario / 100)));
            } else {
                $Preco_Tabelado = $T225_Valor_Preco_Unitario;
            }
        } else {
            
            if ($Preco_Tabelado > 0) {
                $T225_Percentual_Desconto     = ((1 - ($T225_Valor_Preco_Unitario / $Preco_Tabelado)) * 100);
                $T225_Valor_Desconto_Unitario = (($Preco_Tabelado * ($T225_Valor_Preco_Unitario / 100)));
            } else {
                $Preco_Tabelado = $T225_Valor_Preco_Unitario;
            }
        }
        
        // Buscando o percentual de tributos (lei transparências)           
        $D149  = mysql_query("SELECT D149_Percentual_Trib_Transparencia FROM D149 WHERE D149_Id='{$Aliquotas['D149_Id']}'");
        $mD149 = mysql_fetch_array($D149);
        
        $D006  = mysql_query("SELECT D006_Percentual_Trib_Transparencia FROM D006 WHERE D006_Id='{$T225_D006_Id}'");
        $mD006 = mysql_fetch_array($D006);
        
        if (!empty($mD149['D149_Percentual_Trib_Transparencia']) AND $mD149['D149_Percentual_Trib_Transparencia'] > 0) {
            
            $T225_Percentual_Trib_Transparencia = $mD149['D149_Percentual_Trib_Transparencia'];
            
        } else if (!empty($mD006['D006_Percentual_Trib_Transparencia']) AND $mD006['D006_Percentual_Trib_Transparencia'] > 0) {
            
            $T225_Percentual_Trib_Transparencia = $mD006['D006_Percentual_Trib_Transparencia'];
            
        } else {
            
            $T225_Percentual_Trib_Transparencia = 0;
        }
        
        $cClausula = " insert into T225 (";
        $cClausula .= " T225_C004_Id, ";
        $cClausula .= " T225_Quantidade_Estoque, ";
        $cClausula .= " T225_Item, ";
        $cClausula .= " T225_Codigo_Produto, ";
        $cClausula .= " T225_Codigo_Substituto, ";
        $cClausula .= " T225_Descricao_Produto, ";
        $cClausula .= " T225_T224_Id, ";
        $cClausula .= " T225_D024_Id, ";
        $cClausula .= " T225_D009_Id, ";
        $cClausula .= " T225_D037_Id, ";
        $cClausula .= " T225_D082_Id, ";
        $cClausula .= " T225_Quantidade, ";
        $cClausula .= " T225_Valor_Custo_Unitario, ";
        $cClausula .= " T225_Valor_Preco_Unitario, ";
        $cClausula .= " T225_Valor_Preco_Sem_Desconto_Unitario, ";
        $cClausula .= " T225_Valor_Preco_Original, ";
        $cClausula .= " T225_Valor_Preco_Original_Bruto, ";
        $cClausula .= " T225_Percentual_Desconto, ";
        $cClausula .= " T225_Valor_Desconto_Unitario, ";
        $cClausula .= " T225_Percentual_Desconto_Bruto, ";
        $cClausula .= " T225_Valor_Desconto_Unitario_Bruto, ";
        $cClausula .= " T225_IPV, ";
        $cClausula .= " T225_Peso_Unitario, ";
        $cClausula .= " T225_Peso_Bruto, ";
        $cClausula .= " T225_D005_Id,";
        $cClausula .= " T225_Classificacao_Fiscal,";
        $cClausula .= " T225_D006_Id,";
        $cClausula .= " T225_Aliquota_IPI,";
        $cClausula .= " T225_Aliquota_ICMS,";
        $cClausula .= " T225_Percentual_Reducao_ICMS,";
        $cClausula .= " T225_Percentual_Reducao_ICMS_ST,";
        $cClausula .= " T225_ST_VA,";
        $cClausula .= " T225_Flag_ST,";
        $cClausula .= " T225_Flag_Situacao_Tributaria,";
        $cClausula .= " T225_ICMS_Inter_Estadual,";
        $cClausula .= " T225_ST_ICMS_Interno,";
        $cClausula .= " T225_ICMS_Inter_Estadual_MVA,";
        $cClausula .= " T225_ST_ICMS_Interno_MVA,";
        $cClausula .= " T225_Flag_Isento_PIS,";
        $cClausula .= " T225_Aliquota_PIS,";
        $cClausula .= " T225_Situacao_Tributaria_PIS,";
        $cClausula .= " T225_Flag_Isento_COFINS,";
        $cClausula .= " T225_Aliquota_COFINS, ";
        $cClausula .= " T225_Flag_Preco_Especial, ";
        $cClausula .= " T225_Situacao_Tributaria_COFINS, ";
        $cClausula .= " T225_Situacao_Tributaria_IPI, ";
        $cClausula .= " T225_D059_Id, ";
        $cClausula .= " T225_Observacao, ";
        $cClausula .= " T225_Numero_Pedido_Compra, ";
        $cClausula .= " T225_D022_Id, ";
        $cClausula .= " T225_T208_Id, ";
        $cClausula .= " T225_Hora_Entrega, ";
        if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
            $cClausula .= " T225_Item_Pedido_Compra, ";
            $cClausula .= " T225_Valor_Preco_Bruto, ";
        } else {
            $cClausula .= " T225_Item_Pedido_Compra, ";
        }
        $cClausula .= " T225_Cubagem_Unitaria, ";
        $cClausula .= " T225_Percentual_Trib_Transparencia, ";
        $cClausula .= " T225_D144_Id, ";
        $cClausula .= " T225_Flag_Status, ";
        $cClausula .= " T225_Flag_Origem_Produto, ";
        $cClausula .= " T225_Observacao_Entrega, ";
        $cClausula .= " T225_D006_Id_Info, ";
        $cClausula .= " T225_Flag_Destino_Produto ";
        $cClausula .= " ) values ( ";
        $cClausula .= " '{$g['empresaAtual']}', ";
        $cClausula .= " '{$mT225['D009_Quantidade_Estoque_Liquido']}', ";
        $cClausula .= " '$T225_Item', ";
        $cClausula .= " '$T225_Codigo_Produto', ";
        $cClausula .= " '$T225_Codigo_Substituto', ";
        $cClausula .= " '$T225_Descricao_Produto', ";
        $cClausula .= " '$T224_Id', ";
        $cClausula .= " '$D024_Id', ";
        $cClausula .= " '{$mT225['D009_Id']}', ";
        $cClausula .= " '$T225_D037_Id', ";
        $cClausula .= " '{$Aliquotas['D082_Id']}', ";
        $cClausula .= " '$T225_Quantidade', ";
        $cClausula .= " '{$Aliquotas['Valor_Custo_Unitario']}', ";
        //Se o produto for incluido pelo coletor, o valor unit. deve ser zero
        if ($tipo == 2) {
            $cClausula .= " '0', ";
        } else {
            $cClausula .= " '$T225_Valor_Preco_Unitario', ";
        }
        $cClausula .= " '$T225_Valor_Preco_Unitario', ";
        $cClausula .= " '{$Preco_Tabelado}', ";
        $cClausula .= " '{$Preco_Tabelado}', ";
        $cClausula .= " '$T225_Percentual_Desconto', ";
        $cClausula .= " '$T225_Valor_Desconto_Unitario', ";
        $cClausula .= " '$T225_Percentual_Desconto', ";
        $cClausula .= " '$T225_Valor_Desconto_Unitario', ";
        $cClausula .= " '$T225_Valor_Preco_Unitario/{$Aliquotas['Valor_Custo_Unitario']}', ";
        $cClausula .= " '{$Aliquotas['D001_Peso_Unitario_Kg']}', ";
        $cClausula .= " '{$Aliquotas['D001_Peso_Unitario_Bruto']}', ";
        if ($Aliquotas['D005_Id'] == '') {
            $cClausula .= " '{$T225_D005_Id}',";
        } else {
            $cClausula .= " '{$Aliquotas['D005_Id']}',";
        }
        $cClausula .= " '{$Aliquotas['Classificacao_Fiscal']}',";
        $cClausula .= " '{$T225_D006_Id}',";
        //$cClausula .= " '{$mT225['Aliquota_IPI']}',";
        if ($T225_Aliquota_IPI <= 0) {
            $cClausula .= " '{$Aliquotas['Aliquota_IPI']}',";
        } else {
            $cClausula .= " '{$T225_Aliquota_IPI}',";
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
        $cClausula .= " '$T225_Observacao', ";
        $cClausula .= " '$T225_Numero_Pedido_Compra', ";
        $cClausula .= " '$T225_D022_Id', ";
        $cClausula .= " '$T225_T208_Id', ";
        $cClausula .= " '$T225_Hora_Entrega', ";
        if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
            $cClausula .= " '$T225_Item_Pedido_Compra', ";
            $cClausula .= " '$T225_Valor_Preco_Unitario', ";
        } else {
            $cClausula .= " '$T225_Item_Pedido_Compra', ";
        }
        $cClausula .= " '{$mD001['D001_Cubagem_Unitaria']}', ";
        $cClausula .= " '{$T225_Percentual_Trib_Transparencia}', ";
        $cClausula .= " '{$T225_D144_Id}', ";
        $cClausula .= " '', ";
        $cClausula .= " '{$T225_Flag_Origem_Produto}', ";
        $cClausula .= " '{$T225_Observacao_Entrega}', ";
        $cClausula .= " '{$demonstrativoCFOP}', ";
        $cClausula .= " '{$T225_Flag_Destino_Produto}' ";
        $cClausula .= " ); ";
        
        mysql_query($cClausula);
        $T225_Id = $g['mysqlLastId'];
        $cErro   = mysql_error();
        if (!empty($cErro)) {
            return "Erro: $cErro/p/p$cClausula";
        } else {
            mysql_query("UPDATE T189 SET T189_Flag_Status = 'N' WHERE T189_T225_Id = '{$g['mysqlLastId']}'");

            mysql_query("INSERT INTO T225A (T225A_T225_Id, T225A_Data_Entrega, T225A_Prazo_Faturamento, T225A_Valor_Dolar_Unitario, T225A_Valor_Dolar_Total) VALUES ('{$T225_Id}','{$T225A_Data_Entrega}','{$T225A_Prazo_Faturamento}', '{$T225A_Valor_Dolar_Unitario}', '{$T225A_Valor_Dolar_Total}')");                        
            
            if ($g['C031']['controleProdutoPedido'] == 'S') {
                if ($tipo == 1) {
                    $this->atualizarControleProduto($T225_Id);
                    $quantidadeSeparada = '';
                } else if ($tipo == 2) {
                    $this->inserirControleProduto($T225_Id);
                    $quantidadeSeparada = $T225_Quantidade;
                }
                $T066 = mysql_query("SELECT * FROM T066 WHERE T066_D009_Id ='{$mT225['D009_Id']}'");
                $res  = mysql_fetch_array($T066);
                $sql  = "INSERT INTO T185(
                        T185_T225_Id,
                        T185_T066_Id,
                        T185_Qtd_Para_Separar,
                        T185_Qtd_Separado
                    )VALUES(
                        '{$T225_Id}',
                        '{$res['T066_Id']}',
                        '{$T225_Quantidade}',
                        '{$quantidadeSeparada}'
                    )";
                mysql_query($sql);
            }
            
            if (!empty($T225_Codigo_Substituto)) {
                /*
                $D083 = mysql_query("select D083_Id,D083_Flag_Cliente,D083_Codigo_Produto_Fornecedor from D083 where D083_D001_Id='{$mT225['D001_Id']}' and D083_D049_Id='{$mT225['D049_Id']}' and D083_D024_Id='$D024_Id'");
                if (mysql_num_rows($D083) <= 0) {
                    mysql_query("insert into D083 (D083_D049_Id,D083_D001_Id,D083_D024_Id,D083_Data_Cadastro,D083_Flag_Cliente,D083_Codigo_Produto_Fornecedor) values ('{$mT225['D049_Id']}','{$mT225['D001_Id']}','$D024_Id',curdate(),'S','$T225_Codigo_Substituto')");
                } else {
                    $mD083 = mysql_fetch_array($D083);
                    if (trim($mD083['D083_Codigo_Produto_Fornecedor']) == '') {
                        mysql_query("update D083 set D083_Flag_Cliente='S',D083_Codigo_Produto_Fornecedor='$T225_Codigo_Substituto' where D083_Id='{$mD083['D083_Id']}'");
                    }
                }
                */
                require_once("bibliotecas/classes/CAD002.php");
                $CAD002 = new CAD002();
                $CAD002->definirCodigoFornecedor($mT225['D009_Id'], $D024_Id, $T225_Codigo_Substituto, '', true, false);
            }
            $this->atualizarAliquotasProduto($T224_Id, $T225_Id);
            $cSql = mysqli_query("call T225_Gravar_Totalizacao('$T225_Id');");
            $cSql = mysqli_query("call T224_Gravar_Totalizacao_3('$T224_Id');");
            
        }
        
        // caso o produto possua serviços associados, insere estes serviços na T230         
        $result  = mysql_query("SELECT D001_Id FROM D009 LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id WHERE D009_Id='{$T225_D009_Id}'");
        $retorno = mysql_fetch_array($result);
        $D001_Id = $retorno['D001_Id'];
        $result  = mysql_query("SELECT * FROM D135 LEFT JOIN D112 ON D112_Id=D135_D112_Id WHERE D135_D001_Id='{$D001_Id}'");
        
        if (mysql_num_rows($result) > 0) {
            mysql_query("SET AUTOCOMMIT=0");
            mysql_query("START TRANSACTION");
            while ($D135 = mysql_fetch_array($result)) {
                $cClausula = " insert into T230 (";
                $cClausula .= " T230_T224_Id, ";
                $cClausula .= " T230_D112_Id, ";
                $cClausula .= " T230_D110_Id, ";
                $cClausula .= " T230_T225_Id, ";
                $cClausula .= " T230_Titulo, ";
                $cClausula .= " T230_Descricao, ";
                $cClausula .= " T230_Tempo_Estimado, ";
                $cClausula .= " T230_Quantidade, ";
                $cClausula .= " T230_Status, ";
                $cClausula .= " T230_Valor_Unitario, ";
                $cClausula .= " T230_Valor_Total, ";
                $cClausula .= " T230_D116_Id ";
                $cClausula .= " ) values ( ";
                $cClausula .= " '$T224_Id', ";
                $cClausula .= " '{$D135['D135_D112_Id']}', ";
                $cClausula .= " '{$D135['D112_D110_Id']}', ";
                $cClausula .= " '$T225_Id', ";
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
                    mysql_query("ROLLBACK");
                    return 'falha ao copiar os serviços do produto';
                }
                
                $T230_Id = $g['mysqlLastId'];
                
                $sql    = "SELECT * FROM T230 WHERE T230_Id='{$T230_Id}'";
                $result = mysql_query($sql);
                $linha  = mysql_fetch_array($result);
                
                $sql     = "SELECT * FROM T231 WHERE T231_D116_Id='{$linha['T230_D116_Id']}' AND T231_T224_Id='{$linha['T230_T224_Id']}'";
                $result2 = mysql_query($sql);
                if (mysql_num_rows($result2) == 0) {
                    $sql = "INSERT INTO T231
                    (T231_D116_Id, T231_T224_Id, T231_T230_Id)
                        VALUES
                    ('{$linha['T230_D116_Id']}', '{$linha['T230_T224_Id']}', '{$T230_Id}')";
                    
                    mysql_query($sql);
                    
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return mysql_error();
                    }
                }
                
                $sql   = "SELECT * FROM D113 WHERE D113_D112_Id='{$D135['D135_D112_Id']}'";
                $array = mysql_query($sql);
                
                while ($row = mysql_fetch_assoc($array)) {
                    // D113_D112_Id, D113_D110_Id, D113_Titulo, D113_Descricao
                    $sql = "INSERT INTO T148 (T148_T230_Id, T148_Titulo, T148_Descricao) VALUES ('" . mysql_real_escape_string($T230_Id) . "', '"
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
                    $sql = "INSERT INTO T149 (T149_D037_Id, T149_D009_Id, T149_T230_Id, T149_Quantidade) VALUES ('" . mysql_real_escape_string($row['D114_D037_Id']) . "', '" . mysql_real_escape_string($row['D114_D009_Id']) . "', '" . mysql_real_escape_string($T230_Id) . "', '" . mysql_real_escape_string($row['D114_Quantidade']) . "');";
                    mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return 'Erro: ' . mysql_error();
                    }
                }
                
                
            }
            mysql_query("COMMIT");
        }
        
        if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
            mysql_query("UPDATE T225 SET T225_Valor_Preco_Sem_Desconto_Unitario = '{$T225_Valor_Preco_Unitario}' WHERE T225_Id = '{$T225_Id}'");
            mysqli_query("CALL T225_Gravar_Totalizacao('{$T225_Id}')");
            mysql_query("UPDATE T225 SET T225_Valor_Preco_Sem_Desconto_Unitario = T225_Valor_Preco_Sem_Desconto_Unitario/((T225_Valor_Preco_Sem_Desconto_Unitario+(T225_Valor_IPI/T225_Quantidade)+(T225_Valor_ICMS_Substituicao_Retencao/T225_Quantidade))/T225_Valor_Preco_Sem_Desconto_Unitario) WHERE T225_Id = '{$T225_Id}'");
            mysqli_query("CALL T225_Gravar_Totalizacao('{$T225_Id}')");
        }
        
        return true;
    }
    
    /**
     * est016AtualizarParcelas
     *
     * @param int $T226_Id
     * @param string $campoEditado
     * @param string $valorAntigo
     * @param string $novoValor
     */
    public function est016AtualizarParcelas($T226_Id, $campoEditado, $valorAntigo, $novoValor)
    {
        global $g;
        
        $query            = "SELECT * FROM T226 left join T224 on T226_T224_Id='{$T226_Id}' WHERE T226_Id='{$T226_Id}'";
        $retorno          = mysql_query($query);
        $linha            = mysql_fetch_assoc($retorno);
        $dataVelhaT224    = $linha['T224_Data_Emissao'];
        $prazosDiasAntigo = $linha['T226_Prazos_Dias'];
        
        if ($campoEditado == 'T226_Prazos_Dias') {
            // if($valorAntigo == $novoValor){
            // return "O prazo é o mesmo";
            // }
            
            $date = new \DateTime($dataVelhaT224);
            $date->modify('+' . $novoValor . ' days');
            $query = "UPDATE T226 SET T226_Data_Vencimento='{$date->format('Y-m-d')}' WHERE T226_Id='{$T226_Id}'";
            mysql_query($query);
            
        } else if ($campoEditado == 'T226_Data_Vencimento') {
            
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
                $query = "UPDATE T226 SET T226_Prazos_Dias='{$total}' WHERE T226_Id='{$T226_Id}'";
                mysql_query($query);
                return true;
            } else if ($timeStampDataAntiga > $timeStampDataNova) {
                $novaData = ($timeStampDataAntiga - $timeStampDataNova);
                $dias     = date('d', $novaData);
                $total    = ($prazosDiasAntigo - $dias);
                $query    = "UPDATE T226 SET T226_Prazos_Dias='{$total}' WHERE T226_Id='{$T226_Id}'";
                mysql_query($query);
                return true;
            }
            
        } else if ($campoEditado == 'T226_Valor_Parcela') {
            
            $query      = "SELECT * FROM T226 WHERE T226_Id='{$T226_Id}'";
            $retorno    = mysql_query($query);
            $linha      = mysql_fetch_assoc($retorno);
            $T224_Id    = $linha['T226_T224_Id'];
            $numParcela = $linha['T226_Numero_Parcela'];
            
            /* total antigo, pegando o valor do pedido
            $query = "SELECT T224_Valor_Total FROM T224 WHERE T224_Id='{$T224_Id}'";
            $retorno=mysql_query($query);       
            $linha = mysql_fetch_assoc($retorno);   
            $valorTotal=$linha['T224_Valor_Total'];
            */
            
            // pega o valor total com juros (se houver)
            // sem a parcela alterada, e no fim soma com o valor antigo, para pegar o valor total com juros original
            $query      = "SELECT SUM(T226_Valor_Parcela) FROM T226 WHERE T226_T224_Id='{$T224_Id}' AND T226_Id!='{$T226_Id}'";
            $retorno    = mysql_query($query);
            $linha      = mysql_fetch_assoc($retorno);
            $valorTotal = $linha['SUM(T226_Valor_Parcela)'] + gCorrigeNumeroInverte($valorAntigo);
            
            $query                  = "SELECT SUM(T226_Valor_Parcela) FROM T226 WHERE T226_T224_Id='{$T224_Id}' AND T226_Numero_Parcela<='{$numParcela}'";
            $retorno                = mysql_query($query);
            $linha                  = mysql_fetch_assoc($retorno);
            $valorParcelasRestantes = $linha['SUM(T226_Valor_Parcela)'];
            $valorRestante          = $valorTotal - $valorParcelasRestantes;
            
            $query             = "SELECT * FROM T226 WHERE T226_T224_Id='{$T224_Id}' AND T226_Numero_Parcela>'{$numParcela}'";
            $retorno           = mysql_query($query);
            $parcelasRestantes = mysql_num_rows($retorno);
            //se ainda tiverem parcelas depois da editada, divide o valor restante entre elas
            if ($parcelasRestantes != 0) {
                $valorParcelasRestantes = $valorRestante / $parcelasRestantes;
                if ($valorParcelasRestantes < 0) {
                    $query = "UPDATE T226 SET T226_Valor_Parcela='{$valorAntigo}' WHERE T226_Id='{$T226_Id}'";
                    mysql_query($query);
                    return 'O valor total das parcelas é maior que o valor do pedido.<br>Favor alterar novamente';
                }
                while ($linha = mysql_fetch_assoc($retorno)) {
                    $query = "UPDATE T226 SET T226_Valor_Parcela='{$valorParcelasRestantes}' WHERE T226_Id='{$linha['T226_Id']}'";
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
     */
    public function inserirParcela($form)
    {
        
        $T224_Id        = $form['T224_Id'];
        $intervaloDias  = $form['IntervalorDias'];
        $portador       = $form['D031_Id'];
        $Total_Parcelas = $form['Total_Parcelas'];
        
        $diaFixo                = $form['diaFixo'];
        $primeiroVencimentoNum  = $form['primeiroVencimentoNum'];
        $primeiroVencimentoData = $form['primeiroVencimentoData'];
        
        // se foi definido um intervalo de dias
        if ($intervaloDias > 0) {
            $valorNovo  = 0;
            $query      = "SELECT * FROM T224 WHERE T224_Id='{$T224_Id}'";
            $retorno    = mysql_query($query);
            $linha      = mysql_fetch_assoc($retorno);
            $valorTotal = $linha['T224_Valor_Total'];
            
            $query            = "SELECT * FROM T226 WHERE T226_T224_Id='{$T224_Id}' ORDER BY T226_Id DESC";
            $retorno          = mysql_query($query);
            $linha            = mysql_fetch_assoc($retorno);
            $prazoUltima      = $linha['T226_Prazos_Dias'];
            $numeroUltima     = $linha['T226_Numero_Parcela'];
            $numeroNova       = $numeroUltima + 1;
            $VencimentoUltima = $linha['T226_Data_Vencimento'];
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
            
            $cClausula = "insert into T226 (";
            $cClausula .= "T226_T224_Id,";
            $cClausula .= "T226_D031_Id,";
            $cClausula .= "T226_Numero_Parcela,";
            $cClausula .= "T226_Prazos_Dias,";
            $cClausula .= "T226_Data_Vencimento,";
            $cClausula .= "T226_Valor_Parcela";
            $cClausula .= ") values (";
            $cClausula .= "'$T224_Id',";
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
            $query      = "SELECT * FROM T224 WHERE T224_Id='{$T224_Id}'";
            $retorno    = mysql_query($query);
            $linha      = mysql_fetch_assoc($retorno);
            $valorTotal = $linha['T224_Valor_Total'];
            
            $query            = "SELECT * FROM T226 WHERE T226_T224_Id='{$T224_Id}' ORDER BY T226_Id DESC";
            $retorno          = mysql_query($query);
            $linha            = mysql_fetch_assoc($retorno);
            $prazoUltima      = $linha['T226_Prazos_Dias'];
            $numeroUltima     = $linha['T226_Numero_Parcela'];
            $numeroNova       = $numeroUltima + 1;
            $VencimentoUltima = $linha['T226_Data_Vencimento'];
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
                $dataNova = "$ano-$mes-$diaFixo";
                
                $valorNovo = 0;
            }
            
            $cClausula = "insert into T226 (";
            $cClausula .= "T226_T224_Id,";
            $cClausula .= "T226_D031_Id,";
            $cClausula .= "T226_Numero_Parcela,";
            $cClausula .= "T226_Data_Vencimento,";
            $cClausula .= "T226_Prazos_Dias,";
            $cClausula .= "T226_Valor_Parcela";
            $cClausula .= ") values (";
            $cClausula .= "'$T224_Id',";
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
     * calcularParcelaSimulacao
     *
     * @param Form $form
     */
    public function calcularParcelaSimulacao($form)
    {
        global $g;
        $T224_Id     = $form['T224_Id'];
        $numParcelas = $form['Total_Parcelas'];
        
        if ($numParcelas == "0")
            return '';
        
        $query      = "SELECT * FROM T224 WHERE T224_Id='{$T224_Id}'";
        $retorno    = mysql_query($query);
        $linha      = mysql_fetch_assoc($retorno);
        $valorTotal = $linha['T224_Valor_Total'];
        
        return gCorrigeNumero($valorTotal / $numParcelas);
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
     * calculaTotalServicos - Calcula o valor total de serviços do orçamento
     *
     * @param int $T224
     */
    public function calculaTotalServicos($T224_Id)
    {
        $T224_Id = mysql_real_escape_string($T224_Id);
        $sql     = "SELECT * FROM T224 where T224_Id='{$T224_Id}'";
        $T224    = mysql_query($sql);
        $T224    = mysql_fetch_array($T224);
        
        $sql      = "SELECT T230_Valor_Unitario FROM T230 where T230_T224_Id='{$T224_Id}'";
        $T230_sql = mysql_query($sql);
        
        $total = 0;
        while ($T230 = mysql_fetch_array($T230_sql)) {
            $total += $T230['T230_Valor_Unitario'];
        }
        return $total;
    }
    
    /**
     * calculaTotalGeral - Atualiza e retorna o total geral do pedido (com os serviços)
     *
     * @param int $T224
     */
    public function calculaTotalGeral($T224_Id)
    {
        
        $totalServicos = $this->calculaTotalServicos($T224_Id);
        $sql           = "SELECT * FROM T224 where T224_Id='{$T224_Id}'";
        // echo $sql;
        // die();
        $T224          = mysql_query($sql);
        $T224          = mysql_fetch_array($T224);
        
        return $T224['T224_Valor_Total_Servicos'] + $T224['T224_Valor_Total'];
    }
    
    /**
     * gravarObservacao - Grava a Observação do pagamento
     *
     * @param Form $form
     * @param int $T224_Id
     */
    public function gravarObservacao($form, $T224_Id)
    {
        $obs    = $form['T224_Observacao_Pagamento'];
        $sql    = "update T224 SET T224_Observacao_Pagamento='{$obs}' WHERE T224_Id='$T224_Id'";
        $result = mysql_query($sql);
        if (mysql_error()) {
            return mysql_error();
        } else {
            return true;
        }
        
    }
    
    /**
     * gravarDescontoPedido
     *
     * @param int $T224_Id
     * @param string $T224_Valor_Desconto
     * @param string $Campo
     */
    public function gravarDescontoPedido($T224_Id, $T224_Valor_Desconto, $Campo)
    {
        
        require_once "old/lib/funcoes.php";
        
        if (empty($T224_Id)) {
            return "Nao encontrado o T224_Id.";
        }
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        $Valor = $T224_Valor_Desconto;
        
        $cSql_T224 = mysql_query("select * from T224 where T224_Id='$T224_Id'");
        $mT224     = mysql_fetch_array($cSql_T224);
        
        if (($mT224['T224_Flag_Travar_Pedido'] == 1) and ($mT224['T224_Flag_Status'] != '22')) {
            return "PEDIDO TRAVADO";
        }
        
        if ($Campo == 'T224_Percentual_Desconto') {
            $Valor = formata_valor_gravar($Valor, 5);
            mysql_query("update T224
                           set T224_Percentual_Desconto='$Valor',
                               T224_Valor_Desconto=(T224_Percentual_Desconto/100)*T224_Valor_Total_Produtos_Sem_Desconto
                         where T224_Id='$T224_Id'");
            
        } else if ($Campo == 'T224_Valor_Desconto') {
            $Valor = formata_valor_gravar($Valor, 3);
            $sql   = <<<EOT
           update T224
                           set T224_Valor_Desconto='$Valor',
                               T224_Percentual_Desconto=(T224_Valor_Desconto/T224_Valor_Total_Produtos_Sem_Desconto)*100
                         where T224_Id='$T224_Id'
EOT;
            mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return '1. Erro: ' . mysql_error();
            }
            
        }
        $cSql_T225 = mysql_query("select T225_Id from T225 where T225_T224_Id='$T224_Id'");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return '2. Erro: ' . mysql_error();
        }
        while ($mT225 = mysql_fetch_array($cSql_T225)) {
            mysql_query("call T225_Gravar_Totalizacao('{$mT225['T225_Id']}')");
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return '2. Erro: ' . mysql_error();
            }
        }
        mysql_query("call T224_Gravar_Totalizacao_3('$T224_Id')");
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
     * @param int $T224_Id
     */
    public function calculaPercentualDescontoPedido($T224_Id)
    {
        global $g;
        
        if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
            $desconto = ''; // percentual de desconto
            
            $val      = '0.00';
            $retorno  = mysql_query("SELECT sum(T225_Valor_Preco_Original_Bruto*T225_Quantidade) as Total_Pedido, sum(T225_Valor_Preco_Bruto*T225_Quantidade) as Total_Tabela from T225 WHERE T225_T224_Id='{$T224_Id}'");
            $mT225    = mysql_fetch_array($retorno);
            $desconto = 1 - ($mT225['Total_Tabela'] / $mT225['Total_Pedido']); // calcular o percentual de desconto dos itens confirmados           
            $desconto = $desconto * 100;
            
            return $desconto;
        } else {
            $desconto = ''; // percentual de desconto
            
            $val      = '0.00';
            $retorno  = mysql_query("SELECT sum(T225_Valor_Preco_Original*T225_Quantidade) as Total_Pedido, sum(T225_Valor_Preco_Sem_Desconto_Unitario*T225_Quantidade) as Total_Tabela from T225 WHERE T225_T224_Id='{$T224_Id}'");
            $mT225    = mysql_fetch_array($retorno);

            if($mT225['Total_Pedido'] > 0){
                $desconto = 1 - ($mT225['Total_Tabela'] / $mT225['Total_Pedido']); // calcular o percentual de desconto dos itens confirmados 
            } else {
                $desconto = 0;
            }          

            $desconto = $desconto * 100;
            
            return $desconto;
        }
    }
    
    /**
     * calculaPrazoMedioAlcada
     *
     * @param int $T224_Id
     */
    public function calculaPrazoMedioAlcada($T224_Id)
    {
        global $g;
        $retorno = mysql_query("SELECT * FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'");
        $C007    = mysql_fetch_array($retorno);
        
        $retorno = mysql_query("SELECT * FROM T224 WHERE T224_Id='{$T224_Id}'");
        $T224    = mysql_fetch_array($retorno);
        $total   = $T224['T224_Valor_Total_Produtos'];
        
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
     * @param int $T224_Id
     */
    public function calculaPrazoMedioAlcadaVendedor($T224_Id)
    {
        global $g;
        
        $retorno = mysql_query("SELECT * FROM T224 WHERE T224_Id='{$T224_Id}'");
        $T224    = mysql_fetch_array($retorno);
        $total   = $T224['T224_Valor_Total_Produtos'];
        
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
     * @param int $T224_Id
     */
    public function calculaPrazoMedioAlcadaSupervisor($T224_Id)
    {
        global $g;
        
        $retorno = mysql_query("SELECT * FROM T224 WHERE T224_Id='{$T224_Id}'");
        $T224    = mysql_fetch_array($retorno);
        $total   = $T224['T224_Valor_Total_Produtos'];
        
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
     * retornaMaiorDescontoPedido - Método que retorna o maior desconto dado no orçamento
     *
     * @param int $T224_Id
     */
    public function retornaMaiorDescontoPedido($T224_Id)
    {
        global $g;
        
        if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
            
            $T224MaiorDesconto  = mysql_query("SELECT T225_Percentual_Desconto_Bruto FROM T224 LEFT JOIN T225 on T225_T224_Id=T224_Id WHERE T225_T224_Id='{$T224_Id}' ORDER BY T225_Percentual_Desconto_Bruto DESC LIMIT 1");
            $mT224MaiorDesconto = mysql_fetch_array($T224MaiorDesconto);
            $maiorDesconto      = $mT224MaiorDesconto['T225_Percentual_Desconto_Bruto'];
        } else {
            $T224MaiorDesconto  = mysql_query("SELECT T225_Percentual_Desconto FROM T224 LEFT JOIN T225 on T225_T224_Id=T224_Id WHERE T225_T224_Id='{$T224_Id}' ORDER BY T225_Percentual_Desconto_Bruto DESC LIMIT 1");
            $mT224MaiorDesconto = mysql_fetch_array($T224MaiorDesconto);
            $maiorDesconto      = $mT224MaiorDesconto['T225_Percentual_Desconto'];
        }
        return $maiorDesconto;
    }
    
    /**
     * verificaAlcada
     *
     * @param int $T224_Id
     */
    public function verificaAlcada($T224_Id)
    {
        global $g;

 		if($g['C031']['controleAlcadasOrdemCompra'] == 'N'){
            // Não utiliza controle de alçada, finaliza a função
			return true;
		}  

        $T224       = mysql_query("SELECT * FROM T224 WHERE T224_Id='{$T224_Id}'");
        $mCabecalho = mysql_fetch_array($T224);
        
        $Flag_Status_Liberacao      = $mCabecalho['T224_Flag_Status_Liberacao'];
        $Liberou_Alcada_Desconto    = $mCabecalho['T224_Liberou_Alcada_Desconto'];
        $Liberou_Alcada_Valor_Total = $mCabecalho['T224_Liberou_Alcada_Valor_Total'];
        $Liberou_Alcada_Prazo_Medio = $mCabecalho['T224_Liberou_Alcada_Prazo_Medio'];
        $Valor_Total_Venda          = $mCabecalho['T224_Valor_Total'];
        
        $T225   = mysql_query("SELECT * FROM T225 WHERE T225_T224_Id='{$T224_Id}'");
        $mItens = mysql_fetch_array($T225);
        
        $T225            = mysql_query("SELECT AVG(T225_Percentual_Desconto) FROM T225 WHERE T225_T224_Id='{$T224_Id}'");
        $mTotal_Desconto = mysql_fetch_array($T225);
        $Total_Desconto  = $this->calculaPercentualDescontoPedido($T224_Id);
        $Total_Desconto  = number_format($Total_Desconto, 2);
        
        $T225            = mysql_query("SELECT T225_Percentual_Desconto FROM T225 WHERE T225_T224_Id='{$T224_Id}' ORDER BY T225_Percentual_Desconto DESC;");
        $mMaior_Desconto = mysql_fetch_array($T225);
        $Maior_Desconto  = number_format($mMaior_Desconto['T225_Percentual_Desconto'], 2);
        
        $T226       = mysql_query("SELECT AVG(T226_Prazos_Dias) AS PrazoMedio FROM T226 WHERE T226_T224_Id='{$T224_Id}'");
        $mParcelas  = mysql_fetch_array($T226);
        $prazoMedio = number_format($mParcelas['PrazoMedio'], 4);
        
        $retorno = true;    
        //OC ja passou pela analise?
        if ($Flag_Status_Liberacao == "2") {
            // Valor total da Venda liberado é diferente do valor total da Venda atual?
            if ($Valor_Total_Venda != $Liberou_Alcada_Valor_Total) {
                $retorno = false;
            }
            
            // Valor total do desconto liberado é diferente do total do desconto atual?
            if ($Total_Desconto != $Liberou_Alcada_Desconto) {
                $retorno = false;
            }
            
            // prazo médio liberado é diferente do prazo médio atual?
            if ($prazoMedio != $Liberou_Alcada_Prazo_Medio) {
                $retorno = false;
            }
            
            if ($retorno === true) {
                return true;
            }
        }

        mysql_query("UPDATE T224 SET T224_Flag_Status_Liberacao = 4 WHERE T224_Id='{$T224_Id}'");
        return true;
    }
    
    /**
     * verificaAlcada
     *
     * @param int $T224_Id
     * @param string $alcada
     */
    public function enviarParaAnalise($T224_Id, $alcada, $coleta = 'N')
    {
        mysql_query("UPDATE T224 set T224_Flag_Status_Liberacao='1', T224_Flag_Status='1', T224_Nome_Status = T224_Status_Pedido(T224_Flag_Status, T224_Id), T224_Flag_Coleta='{$coleta}' WHERE T224_Id='{$T224_Id}'");
        if (mysql_error()){
            return mysql_error();
        } else {
            return true;
        }
    }
    
    /**
     * est016AplicarPercentualDesconto
     *
     * @param int $T225_Id
     * @param string $campoEditado
     * @param string $valorAntigo
     * @param string $novoValor
     */
    public function est016AplicarPercentualDesconto($T225_Id, $campoEditado, $valorAntigo, $novoValor)
    {
        global $g;
        if ($T225_Id <= 0) {
            return "T225_Id nao existe!";
        }
        $Campo_Editado          = $campoEditado;
        $Conteudo_Campo_Editado = $novoValor;
        
        $T224    = mysql_query("SELECT T225_T224_Id FROM T225 WHERE T225_Id='{$T225_Id}'");
        $mT224   = mysql_fetch_array($T224);
        $T224_Id = $mT224['T225_T224_Id'];
        
        if (empty($Campo_Editado)) {
            return "Campo_Editado nao existe!";
        }
        
        if ($T225_Id <= 0) {
            return "T225_Id nao existe!";
        }
        $C007  = mysql_query("select * from C007 where C007_Id='{$g['usuarioAtual']}'");
        $mC007 = mysql_fetch_array($C007);
        
        $cC004 = mysql_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
        $mC004 = mysql_fetch_array($cC004);
        
        $cClausula = " select ";
        $cClausula .= " *  ";
        $cClausula .= " from T225 ";
        $cClausula .= " left join D009 on D009_Id=T225_D009_Id ";
        $cClausula .= " left join D049 on D049_Id=D009_D049_Id ";
        $cClausula .= " left join D001 on D001_Id=D049_D001_Id ";
        $cClausula .= " left join D037 on D037_Id=T225_D037_Id ";
        $cClausula .= " where T225_Id='$T225_Id' ";
        $T225  = mysql_query("$cClausula");
        $mT225 = mysql_fetch_array($T225);
        
        if ($Campo_Editado == 'T225_Valor_Preco_Unitario') {
            $Conteudo_Campo_Editado = gCorrigeNumeroInverte($Conteudo_Campo_Editado);
            
            if ($Conteudo_Campo_Editado < $mT225['T225_Valor_Custo_Unitario']) {
                //return 'Preço inválido';
            }
            $sql = "update T225 set T225_Valor_Preco_Sem_Desconto_Unitario='$Conteudo_Campo_Editado',
                                    T225_Valor_Preco_Unitario=T225_Valor_Preco_Sem_Desconto_Unitario,
                                    T225_Percentual_Desconto=if(T225_Valor_Preco_Original>0,(1-(T225_Valor_Preco_Sem_Desconto_Unitario/T225_Valor_Preco_Original))*100,0),
                                    T225_Valor_Desconto_Unitario=if(T225_Valor_Preco_Original>0,(T225_Valor_Preco_Original*($Conteudo_Campo_Editado/100)),0)
                              where T225_Id='$T225_Id'";
            mysql_query($sql);
            if (mysql_error()) {
                $erro = mysql_error();
                return "1 .Erro: " . $erro . ' - ' . str_replace("\n", ' ', $sql);
            }
            
        } else if ($Campo_Editado == 'T225_Valor_Preco_Sem_Desconto_Unitario') {
            
            $Conteudo_Campo_Editado = gCorrigeNumeroInverte($Conteudo_Campo_Editado, $mC004['C004_Casas_Decimais_Preco'] + 1);
            
            if ($Conteudo_Campo_Editado < $mT225['T225_Valor_Custo_Unitario']) {
                //return 'Preço inválido';
            }
            $sql = "update T225 set T225_Valor_Preco_Sem_Desconto_Unitario='$Conteudo_Campo_Editado',
                                    T225_Valor_Preco_Unitario=T225_Valor_Preco_Sem_Desconto_Unitario,
                                    T225_Percentual_Desconto=if(T225_Valor_Preco_Original>0,(1-(T225_Valor_Preco_Sem_Desconto_Unitario/T225_Valor_Preco_Original))*100,0),
                                    T225_Valor_Desconto_Unitario=if(T225_Valor_Preco_Original>0,(T225_Valor_Preco_Original*($Conteudo_Campo_Editado/100)),0)
                              where T225_Id='$T225_Id'";
            mysql_query($sql);
            if (mysql_error()) {
                $erro = mysql_error();
                return "1 .Erro: " . $erro . ' - ' . str_replace("\n", ' ', $sql);
            }
            
        } else if ($Campo_Editado == 'T225_Valor_Preco_Bruto') {
            
            $Conteudo_Campo_Editado = gCorrigeNumeroInverte($Conteudo_Campo_Editado, $mC004['C004_Casas_Decimais_Preco'] + 1);
            
            $sql = "update T225 set T225_Valor_Preco_Bruto='$Conteudo_Campo_Editado',
                                        T225_Percentual_Desconto_Bruto=if(T225_Valor_Preco_Original_Bruto>0,(1-(T225_Valor_Preco_Bruto/T225_Valor_Preco_Original_Bruto))*100,0),
                                        T225_Valor_Desconto_Unitario_Bruto=if(T225_Valor_Preco_Original_Bruto>0,(T225_Valor_Preco_Original_Bruto*($Conteudo_Campo_Editado/100)),0)
                              where T225_Id='$T225_Id'";
            mysql_query($sql);
            if (mysql_error()) {
                $erro = mysql_error();
                return "1 .Erro: " . $erro;
            }
        } elseif ($Campo_Editado == 'T225_Percentual_Desconto') {
            if (!empty($Conteudo_Campo_Editado)) {
                $Conteudo_Campo_Editado = gCorrigeNumeroInverte($Conteudo_Campo_Editado, 5);
            } else {
                $Conteudo_Campo_Editado = 0;
            }
            //return "1: ".number_format($Conteudo_Campo_Editado,2)."<br />2: ".number_format($mT225['T225_Percentual_Desconto'],2);
            if (($mT225['T225_Valor_Preco_Original'] - ($mT225['T225_Valor_Preco_Original'] * ($Conteudo_Campo_Editado / 100))) < $mT225['T225_Valor_Custo_Unitario']) {
                // return "Desconto inválido";
            }
            
            $sql = "update T225 set T225_Percentual_Desconto='$Conteudo_Campo_Editado',
                                        T225_Valor_Preco_Sem_Desconto_Unitario=T225_Valor_Preco_Original-(T225_Valor_Preco_Original*($Conteudo_Campo_Editado/100)),
                                        T225_Valor_Preco_Unitario=T225_Valor_Preco_Sem_Desconto_Unitario,
                                        T225_Valor_Desconto_Unitario=(T225_Valor_Preco_Original*($Conteudo_Campo_Editado/100))
                                  where T225_Id='$T225_Id'";
            //return $sql;
            mysql_query($sql);
            if (mysql_error()) {
                $erro = mysql_error();
                return "2 .Erro: " . $erro;
            }
        } elseif ($Campo_Editado == 'T225_Percentual_Desconto_Bruto') {
            if (!empty($Conteudo_Campo_Editado)) {
                $Conteudo_Campo_Editado = gCorrigeNumeroInverte($Conteudo_Campo_Editado, 5);
            } else {
                $Conteudo_Campo_Editado = 0;
            }
            //return "1: ".number_format($Conteudo_Campo_Editado,2)."<br />2: ".number_format($mT225['T225_Percentual_Desconto_Bruto'],2);
            if (number_format($Conteudo_Campo_Editado, 2) != number_format($mT225['T225_Percentual_Desconto_Bruto'], 2)) {
                
                if (($mT225['T225_Valor_Preco_Original_Bruto'] - ($mT225['T225_Valor_Preco_Original_Bruto'] * ($Conteudo_Campo_Editado / 100))) < $mT225['T225_Valor_Preco_Original_Bruto']) {
                    //return "Desconto inválido";
                }
                
                $sql = "update T225 set T225_Percentual_Desconto_Bruto='$Conteudo_Campo_Editado',
                                            T225_Valor_Preco_Sem_Desconto_Unitario_Bruto=T225_Valor_Preco_Original_Bruto-(T225_Valor_Preco_Original_Bruto*($Conteudo_Campo_Editado/100)),
                                            T225_Valor_Preco_Unitario=T225_Valor_Preco_Sem_Desconto_Unitario,
                                            T225_Valor_Desconto_Unitario_Bruto=(T225_Valor_Preco_Original_Bruto*($Conteudo_Campo_Editado/100))
                                      where T225_Id='$T225_Id'";
                //return $sql;
                mysql_query($sql);
                if (mysql_error()) {
                    $erro = mysql_error();
                    return "2 .Erro: " . $erro;
                }
            }
        } else {
            
            $Conteudo_Campo_Editado = strtoupper($Conteudo_Campo_Editado);
            $Conteudo_Campo_Editado = addslashes($Conteudo_Campo_Editado);
            
            mysql_query("update T225 set $Campo_Editado='$Conteudo_Campo_Editado'
                                  where T225_Id='$T225_Id'");
            
        }
        
        $cSql = mysql_query("call T225_Gravar_Totalizacao('$T225_Id');");
        //$cSql=sql_query("call T218_Gravar_Totalizacao('$CRM_T218_Id');");
        return true;
    }
    
    /**
     * est016aplicarDesconto
     *
     * @param int $T224_Id
     * @param string $Desconto
     * @param string $CampoAplicarDesconto
     */
    public function est016aplicarDesconto($T224_Id, $Desconto, $CampoAplicarDesconto)
    {
        global $g;
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        if (empty($Desconto)) {
            return "Insira um Desconto";
        }
        if (empty($T224_Id)) {
            return "Insira um Desconto";
        }
        $Conteudo_Campo_Editado = $Desconto;
        $sql                    = "SELECT * FROM T225 WHERE T225_T224_Id='{$T224_Id}'";
        $result                 = mysql_query($sql);
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return "Erro: " . mysql_error();
        }
        if (!empty($Conteudo_Campo_Editado)) {
            $Conteudo_Campo_Editado = formata_valor_gravar($Conteudo_Campo_Editado, 5);
        } else {
            $Conteudo_Campo_Editado = 0;
        }
        if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
            
            while ($mT225 = mysql_fetch_array($result)) {
                
                if ($CampoAplicarDesconto == "tabela") {
                    
                    // se o desconto for em cima do preço de tabela, será aplicado o desconto com base no campo T225_Valor_Preco_Original_Bruto
                    
                    if (number_format($Conteudo_Campo_Editado, 2) != number_format($mT225['T225_Percentual_Desconto_Bruto'], 2)) {
                        if (($mT225['T225_Valor_Preco_Original_Bruto'] - ($mT225['T225_Valor_Preco_Original_Bruto'] * ($Conteudo_Campo_Editado / 100))) < $mT225['T225_Valor_Preco_Unitario']) {
                            //return "Desconto inválido";
                        }
                        $sql = "update T225 set T225_Percentual_Desconto_Bruto='$Conteudo_Campo_Editado',
                                T225_Valor_Preco_Bruto=T225_Valor_Preco_Original_Bruto-(T225_Valor_Preco_Original_Bruto*($Conteudo_Campo_Editado/100)),
                                T225_Valor_Desconto_Unitario_Bruto=(T225_Valor_Preco_Original_Bruto*($Conteudo_Campo_Editado/100))
                          where T225_Id='{$mT225['T225_Id']}'";
                        mysql_query($sql);
                        if (mysql_error()) {
                            mysql_query("ROLLBACK");
                            return "Erro: " . mysql_error();
                        }
                    }
                    $cSql = mysql_query("call T225_Gravar_Totalizacao('{$mT225['T225_Id']}');");
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return "Erro: " . mysql_error();
                    }
                    
                } else {
                    
                    // se o desconto for em cima do preço do pedido, será aplicado o desconto com base no campo T225_Valor_Preco_Bruto
                    
                    //                  if (($mT225['T225_Valor_Preco_Bruto']-($mT225['T225_Valor_Preco_Bruto']*($Conteudo_Campo_Editado/100)))<$mT225['T225_Valor_Preco_Unitario']){
                    //                      return "Desconto inválido";
                    //                  }
                    $sql = "update T225 set                                 
                            T225_Valor_Preco_Bruto=T225_Valor_Preco_Bruto-(T225_Valor_Preco_Bruto*($Conteudo_Campo_Editado/100)),
                            T225_Percentual_Desconto_Bruto=if(T225_Valor_Preco_Original_Bruto>0,(1-(T225_Valor_Preco_Bruto/T225_Valor_Preco_Original_Bruto))*100,0),
                            T225_Valor_Desconto_Unitario_Bruto=if(T225_Valor_Preco_Original_Bruto>0,(T225_Valor_Preco_Original_Bruto-T225_Valor_Preco_Bruto),0)
                      where T225_Id='{$mT225['T225_Id']}'";
                    mysql_query($sql);
                    $erro = mysql_error();
                    if (!empty($erro)) {
                        mysql_query("ROLLBACK");
                        return "Erro: " . mysql_error();
                    }
                    $cSql = mysql_query("call T225_Gravar_Totalizacao('{$mT225['T225_Id']}');");
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return "Erro: " . mysql_error();
                    }
                }
            }
            
        } else {
            
            while ($mT225 = mysql_fetch_array($result)) {
                
                if ($CampoAplicarDesconto == "tabela") {
                    
                    // se o desconto for em cima do preço de tabela, será aplicado o desconto com base no campo T225_Valor_Preco_Original
                    if (number_format($Conteudo_Campo_Editado, 2) != number_format($mT225['T225_Percentual_Desconto'], 2)) {
                        if (($mT225['T225_Valor_Preco_Original'] - ($mT225['T225_Valor_Preco_Original'] * ($Conteudo_Campo_Editado / 100))) < $mT225['T225_Valor_Custo_Unitario']) {
                            //return "Desconto inválido";
                        }
                        $sql = "update T225 set T225_Percentual_Desconto='$Conteudo_Campo_Editado',
                                T225_Valor_Preco_Sem_Desconto_Unitario=T225_Valor_Preco_Original-(T225_Valor_Preco_Original*($Conteudo_Campo_Editado/100)),
                                T225_Valor_Preco_Unitario=T225_Valor_Preco_Sem_Desconto_Unitario,
                                T225_Valor_Desconto_Unitario=(T225_Valor_Preco_Original*($Conteudo_Campo_Editado/100))
                          where T225_Id='{$mT225['T225_Id']}'";
                        //return $sql;
                        mysql_query($sql);
                        if (mysql_error()) {
                            mysql_query("ROLLBACK");
                            return "Erro: " . mysql_error();
                        }
                    }
                    $cSql = mysql_query("call T225_Gravar_Totalizacao('{$mT225['T225_Id']}');");
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return "Erro: " . mysql_error();
                    }
                    
                } else {
                    
                    // se o desconto for em cima do preço do pedido, será aplicado o desconto com base no campo T225_Valor_Preco_Sem_Desconto_Unitario
                    
                    $sql = "update T225 set                                 
                            T225_Valor_Preco_Sem_Desconto_Unitario=T225_Valor_Preco_Sem_Desconto_Unitario-(T225_Valor_Preco_Sem_Desconto_Unitario*($Conteudo_Campo_Editado/100)),
                            T225_Valor_Preco_Unitario=T225_Valor_Preco_Sem_Desconto_Unitario,
                            T225_Percentual_Desconto=if(T225_Valor_Preco_Original>0,(1-(T225_Valor_Preco_Sem_Desconto_Unitario/T225_Valor_Preco_Original))*100,0),
                            T225_Valor_Desconto_Unitario=if(T225_Valor_Preco_Original>0,(T225_Valor_Preco_Original-T225_Valor_Preco_Sem_Desconto_Unitario),0)
                      where T225_Id='{$mT225['T225_Id']}'";
                    mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return "Erro: " . mysql_error();
                    }
                    $cSql = mysql_query("call T225_Gravar_Totalizacao('{$mT225['T225_Id']}');");
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
     * est016aplicarDescontoReais
     *
     * @param int $T224_Id
     * @param string $DescontoReais
     */
    public function est016aplicarDescontoReais($T224_Id, $DescontoReais)
    {
        global $g;
        $this->est016aplicarDesconto($T224_Id, '0.00000', 'tabela');
        $T224               = mysql_query("SELECT T224_Valor_Total FROM T224 WHERE T224_Id='{$T224_Id}'");
        $mT224              = mysql_fetch_array($T224);
        $desconto           = gCorrigeNumeroInverte($DescontoReais);
        $percentualDesconto = ($desconto / $mT224['T224_Valor_Total']) * 100;
        return $this->est016aplicarDesconto($T224_Id, $percentualDesconto, 'tabela');
    }
    
    /**
     * est016aplicarDescontoTotal
     *
     * @param int $T224_Id
     * @param string $DescontoTotal
     */
    public function est016aplicarDescontoTotal($T224_Id, $DescontoTotal)
    {
        global $g;
        $this->est016aplicarDesconto($T224_Id, '0.00000', 'tabela');
        $T224     = mysql_query("SELECT T224_Valor_Total FROM T224 WHERE T224_Id='{$T224_Id}'");
        $mT224    = mysql_fetch_array($T224);
        $desconto = gCorrigeNumeroInverte($DescontoTotal);
        if ($desconto > 0) {
            $desconto           = $mT224['T224_Valor_Total'] - $desconto;
            $percentualDesconto = ($desconto / $mT224['T224_Valor_Total']) * 100;
        }
        return $this->est016aplicarDesconto($T224_Id, $percentualDesconto, 'tabela');
    }
    
    /**
     * importarDocsPedido
     *
     * @param Form $form
     */
    public function importarDocsPedido($form)
    {
        global $g;
        $T224_Id               = $form->campoValorEnviado('T227_T224_Id');
        $T227_C007_Id          = $form->campoValorEnviado('T227_C007_Id');
        $T227_Data_Hora_UpLoad = $form->campoValorEnviado('T227_Data_Hora_UpLoad');
        $T224_Id_Importar      = $form->campoValorEnviado('Numero_Pedido');
        //Insere os documentos do orçamento no pedido.
        $sqlDocumentos         = "SELECT * FROM T227 WHERE T227_T224_Id='{$T224_Id_Importar}'";
        $resultDocumentos      = mysql_query($sqlDocumentos);
        if (mysql_num_rows($resultDocumentos) <= 0) {
            return "Não existe nenhum pedido com o número informado ou o pedido não possuí nenhum documento. Favor verificar.";
        }
        while ($linhaDocumentos = mysql_fetch_array($resultDocumentos)) {
            
            $sql            = <<<EOT
            INSERT INTO T227 
            (T227_T224_Id, T227_C007_Id, T227_Nome_Arquivo, T227_Descricao, T227_Data_Hora_Upload)
            VALUES
            ('{$T224_Id}','{$T227_C007_Id}','{$linhaDocumentos['T227_Nome_Arquivo']}','{$linhaDocumentos['T227_Descricao']}','{$T227_Data_Hora_UpLoad}')
EOT;
            $resultCopiaDoc = mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return 'Não foi possível importar os arquivos, erro SQL: ' . mysql_error();
            }
            
            //----Copia os arquivos para o pedido atual
            //Pega a extensão do arquivo
            $pathinfo              = pathinfo($linhaDocumentos['T227_Nome_Arquivo']);
            $pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
            
            //Pega o caminho do arquivo no orçamento
            $dirOrigem = "{$g['pathDados']}pedido";
            is_dir($dirOrigem) or mkdir($dirOrigem, 0777, true);
            $fileOrigem = $dirOrigem . '/' . $linhaDocumentos['T227_Id'] . '.' . $pathinfo['extension'];
            
            $dirDestino = "{$g['pathDados']}pedido";
            is_dir($dirDestino) or mkdir($dirDestino, 0777, true);
            $fileDestino = $dirDestino . '/' . $g['mysqlLastId'] . '.' . $pathinfo['extension'];
            copy($fileOrigem, $fileDestino);
        }
        return true;
    }
    
    /**
     * est016VerificaCancelarPedido
     *
     * @param Form $form
     */
    public function est016VerificaCancelarPedido($form)
    {
        global $g;
        
        $T224_Id = $form->campoValorEnviado('T224_Id');
        
        $erro    = "";
        $retorno = $this->est016CancelarPedido($form);
        if ($retorno !== true) {
            $erro = "Pedido " . $form->campoValorEnviado('T224_Id') . " Erro: " . $retorno;
        }
        
        $T224 = mysql_query("SELECT T224_Id from T224 where T224_T224_Id_Origem='{$T224_Id}'");
        if (mysql_num_rows($T224) > 0) {
            
            $mT224 = mysql_fetch_array($T224);
            $form->campoSetarValor('T224_Id', $mT224['T224_Id'], true);
            $retorno = $this->est016CancelarPedido($form);
            
            if ($retorno !== true) {
                $erro .= "<br />Pedido " . $form->campoValorEnviado('T224_Id') . " Erro: " . $retorno;
            }
        }
        if ($erro == "") {
            return true;
        } else {
            return $erro;
        }
    }
    
    /**
     * est016CancelarPedido
     *
     * @param Form $form
     */
    public function est016CancelarPedido($form)
    {
        global $g;
        $T224_Id                        = $form->campoValorEnviado('T224_Id');
        $T224_D047_Id                   = $form->campoValorEnviado('T224_D047_Id');
        $T224_Observacao_Pedido_Perdido = $form->campoValorEnviado('T224_Observacao_Pedido_Perdido');
        
        /**
         * Verificações iniciais
         */
        mysql_query("START TRANSACTION");
        mysql_query("SET AUTOCOMMIT=0");
        
        // Nâo está usando na função
        //$cSql_T224 = mysql_query("SELECT * FROM T224 WHERE T224_Id='{$T224_Id}'");
        //$mT224     = mysql_fetch_array($cSql_T224);
        
        $T224_D047_Id                   = AddSlashes($T224_D047_Id);
        $T224_Observacao_Pedido_Perdido = AddSlashes($T224_Observacao_Pedido_Perdido);
        $T224_D047_Id                   = Trim($T224_D047_Id);
        $T224_Observacao_Pedido_Perdido = Trim($T224_Observacao_Pedido_Perdido);
        $T224_D047_Id                   = StrToMaiusculo($T224_D047_Id);
        $T224_Observacao_Pedido_Perdido = StrToMaiusculo($T224_Observacao_Pedido_Perdido);
        
        if (strlen(trim($T224_Observacao_Pedido_Perdido)) == 0) {
            return "Necessario o preenchimento do campo: Observação";
        }
        
        $cClausula = " UPDATE T224 SET ";
        $cClausula .= " T224_D047_Id='{$T224_D047_Id}', ";
        $cClausula .= " T224_Observacao_Pedido_Perdido='{$T224_Observacao_Pedido_Perdido}', ";
        $cClausula .= " T224_Flag_Status='11', ";
        $cClausula .= " T224_Nome_Status=T224_Status_Pedido(T224_Flag_Status, T224_Id), ";
        $cClausula .= " T224_Flag_Travar_Pedido='1', ";
        $cClausula .= " T224_Flag_Situacao='', ";
        $cClausula .= " T224_Flag_Almoxarifado='',  ";
        $cClausula .= " T224_Flag_Status_Servicos=3  ";
        $cClausula .= " WHERE T224_Id='{$T224_Id}' ";
        $cSql  = mysql_query($cClausula);
        $cErro = mysql_error();

        if (empty($cErro)) {
            mysql_query("INSERT INTO T228 (T228_T224_Id,
                                            T228_C007_Id,
                                            T228_Data,
                                            T228_Descricao,
                                            T228_Observacao
                                  ) VALUES ('{$T224_Id}',
                                            '{$g['usuarioAtual']}',
                                            CURRENT_TIMESTAMP(),
                                            'Pedido Cancelado',
                                            '{$T224_Observacao_Pedido_Perdido}')");
        } else {
            mysql_query("ROLLBACK");
            return "1. Erro ao cancelar a ordem de compra. Por favor, contate o suporte técnico.";
        }
        
        mysqli_query("UPDATE T225 SET T225_T219_Id = 0 WHERE T225_T224_Id='{$T224_Id}'");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return "2. Erro ao canelar o pedido. Por favor, contate o suporte técnico.";
        }        
        
        //Atualiza estoque dos produtos do pedido
        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();
        
        $sqlT225 = mysql_query("SELECT T225_Id, T225_D009_Id FROM T225 WHERE T225_T224_Id='{$T224_Id}'");        
        while ($rowT225 = mysql_fetch_array($sqlT225)) {
            mysql_query("UPDATE T006 SET T006_T225_Id = 0 WHERE T006_T225_Id='{$rowT225['T225_Id']}'");
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return 'Erro ao desvincular o item da ordem de compra com o pedido. Por favor, contate o suporte técnico.';
            }

            $atualizarEstoque = $CAD002->D001_reprocessa_historico(false, $rowT225['T225_D009_Id'], 20);
            if ($atualizarEstoque !== true) {
                mysql_query("ROLLBACK");
                return "Erro ao atualizar estoque dos produtos. Por favor, contate o suporte técnico.";
            }
        }
        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");
        return true;
    }
    
    /**
     * verificaFuturoSaldoProduto
     *
     * @param int $T224_Id
     * @param boolean $restricaoAtual
     */
    public function verificaFuturoSaldoProduto($T224_Id, $restricaoAtual = false)
    {
        global $g;
        $saldoMinimo = $g['C031']['alcadaQuantidadeMinimaEstoque'];
        if ($saldoMinimo <= 0) {
            return 0;
        }
        $sqlT224 = mysql_query("SELECT  T225_Quantidade, 
                                        D009_Quantidade_Estoque_Liquido,
                                        T225_Liberou_Alcada_Saldo_Futuro, 
                                        C007_Flag_Vendedor,
                                        (D009_Quantidade_Estoque_Liquido-T225_Quantidade) as SaldoFuturo 
                                        FROM T225
                                        LEFT JOIN D009 on D009_Id=T225_D009_Id
                                        LEFT JOIN T224 on T224_Id=T225_T224_Id 
                                        LEFT JOIN C007 on C007_Id=T224_C007_Id_Vendedor_Interno 
                                        WHERE T225_T224_Id='{$T224_Id}'");
        $retorno = 0;
        if ($restricaoAtual == false) {
            while ($rowT224 = mysql_fetch_array($sqlT224)) {
                if (($rowT224['SaldoFuturo'] < $saldoMinimo) && ($rowT224['T225_Liberou_Alcada_Saldo_Futuro'] != $rowT224['SaldoFuturo']) && $rowT224['C007_Flag_Vendedor'] == 'V') {
                    $retorno += 1;
                }
            }
        } else {
            while ($rowT224 = mysql_fetch_array($sqlT224)) {
                if (($rowT224['SaldoFuturo'] < $saldoMinimo) && $rowT224['C007_Flag_Vendedor'] == 'V') {
                    $retorno += 1;
                }
            }
        }
        
        return $retorno;
    }
    
    /**
     * gerarControleProdutosPedido
     *
     * @param int $T224_Id
     */
    public function gerarControleProdutosPedido($T224_Id)
    {
        global $g;
        //INSERT de todos os produtos do pedido na T207 (Controle de Produtos)
        $T225 = mysql_query("SELECT * FROM T225 WHERE T225_T224_Id='{$T224_Id}'");
        while ($row = mysql_fetch_array($T225)) {
            mysql_query("DELETE FROM T207 WHERE T207_T225_Id = '{$row['T225_Id']}'");
            $quantidadeSeparada = $row['T225_Quantidade_Separacao'];
            for ($i = 0; $i < $row['T225_Quantidade']; $i++) {
                $sql = "INSERT INTO T207(";
                $sql .= "T207_D009_Id,";
                $sql .= "T207_T225_Id,";
                $sql .= "T207_T224_Id,";
                $sql .= "T207_C007_Id,";
                $sql .= "T207_Flag_Status,";
                $sql .= "T207_Data";
                $sql .= ") VALUES (";
                $sql .= "'{$row['T225_D009_Id']}',";
                $sql .= "'{$row['T225_Id']}',";
                $sql .= "'{$row['T225_T224_Id']}',";
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
     * @param int $T225_Id
     */
    public function atualizarControleProduto($T225_Id)
    {
        global $g;
        mysql_query("DELETE FROM T207 WHERE T207_T225_Id = '{$T225_Id}'");
        //INSERT de todos os produtos do pedido na T207 (Controle de Produtos)
        $T225               = mysql_query("SELECT * FROM T225 WHERE T225_Id='{$T225_Id}'");
        $row                = mysql_fetch_array($T225);
        $quantidadeSeparada = $row['T225_Quantidade_Separacao'];
        for ($i = 0; $i < $row['T225_Quantidade']; $i++) {
            $sql = "INSERT INTO T207(";
            $sql .= "T207_D009_Id,";
            $sql .= "T207_T225_Id,";
            $sql .= "T207_T224_Id,";
            $sql .= "T207_C007_Id,";
            $sql .= "T207_Flag_Status,";
            $sql .= "T207_Data";
            $sql .= ") VALUES (";
            $sql .= "'{$row['T225_D009_Id']}',";
            $sql .= "'{$row['T225_Id']}',";
            $sql .= "'{$row['T225_T224_Id']}',";
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
     * @param int $T225_Id
     */
    public function inserirControleProduto($T225_Id)
    {
        global $g;
        //INSERT de todos os produtos do pedido na T207 (Controle de Produtos)
        $T225 = mysql_query("SELECT * FROM T225 WHERE T225_Id='{$T225_Id}'");
        $row  = mysql_fetch_array($T225);
        for ($i = 0; $i < $row['T225_Quantidade']; $i++) {
            $sql = "INSERT INTO T207(";
            $sql .= "T207_D009_Id,";
            $sql .= "T207_T225_Id,";
            $sql .= "T207_T224_Id,";
            $sql .= "T207_C007_Id,";
            $sql .= "T207_Flag_Status,";
            $sql .= "T207_Data";
            $sql .= ") VALUES (";
            $sql .= "'{$row['T225_D009_Id']}',";
            $sql .= "'{$row['T225_Id']}',";
            $sql .= "'{$row['T225_T224_Id']}',";
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
     * @param int $T225_Id
     */
    public function excluirControleProduto($T225_Id)
    {
        $T207 = mysql_query("SELECT * FROM T207 WHERE T207_T225_Id = '{$T225_Id}' AND T207_Flag_Status = '2'");
        if (mysql_num_rows($T207) > 0) {
            mysql_query("UPDATE T207 SET T207_Flag_Status = '5', T207_T225_Id = '' WHERE T207_T225_Id = '{$T225_Id}'");
            return true;
        } else {
            mysql_query("DELETE FROM T207 WHERE T207_T225_Id = '{$T225_Id}'");
            return false;
        }
    }
    
    /**
     * alteraLista
     *
     * @param int $T225_Id
     * @param string $lista
     */
    public function alteraLista($T224_Id, $lista)
    {
        global $g;
        mysql_query("UPDATE T224 SET T224_Flag_Lista_Preco = '{$lista}' WHERE T224_Id = '{$T224_Id}'");
        $T225 = mysql_query("SELECT * FROM T225 WHERE T225_T224_Id = '{$T224_Id}'");
        
        while ($row = mysql_fetch_array($T225)) {
            $sql  = "SELECT (D009_Preco_{$lista}) as preco FROM D009 WHERE D009_Id = '{$row['T225_D009_Id']}'";
            $D009 = mysql_query($sql);
            $res  = mysql_fetch_array($D009);
            if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
                $sql = "UPDATE T225 SET T225_Valor_Preco_Original_Bruto = '{$res['preco']}',
                                    T225_Percentual_Desconto_Bruto=if(T225_Valor_Preco_Original_Bruto>0,(1-(T225_Valor_Preco_Bruto/T225_Valor_Preco_Original_Bruto))*100,0),
                                    T225_Valor_Desconto_Unitario_Bruto=if(T225_Valor_Preco_Original_Bruto>0,(T225_Valor_Preco_Original_Bruto*('{$res['preco']}'/100)),0)
                                    WHERE T225_Id='{$row['T225_Id']}'";
                mysql_query($sql);
            } else {
                $sql = "UPDATE T225 SET T225_Valor_Preco_Original = '{$res['preco']}',
                                    T225_Percentual_Desconto=if(T225_Valor_Preco_Original>0,(1-(T225_Valor_Preco_Sem_Desconto_Unitario/T225_Valor_Preco_Original))*100,0),
                                    T225_Valor_Desconto_Unitario=if(T225_Valor_Preco_Original>0,(T225_Valor_Preco_Original*('{$res['preco']}'/100)),0)
                                    WHERE T225_Id='{$row['T225_Id']}'";
                
                mysql_query($sql);
            }
        }
        mysqli_query("CALL T224_Gravar_Totalizacao_3('{$T224_Id}')");
        return true;
    }
    
    /**
     * est016AgruparPedidosSelecionados
     */
    public function est016AgruparPedidosSelecionados()
    {
        global $g;
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        /**
         * Verificações
         **/
        $T224_Base  = mysql_query("SELECT * FROM T224 left join T007 on T007_T224_Id=T224_Id and T007_Flag_Cancelada!='S' WHERE T224_Flag_Duplicar='S'");
        $mT224_Base = mysql_fetch_array($T224_Base);
        
        $AllT224 = mysql_query("SELECT * FROM T224 left join T007 on T007_T224_Id=T224_Id and T007_Flag_Cancelada!='S' WHERE T224_Flag_Duplicar='S'");
        while ($mAllT224 = mysql_fetch_array($AllT224)) {
            if (($mT224_Base['T224_Flag_Status'] != $mAllT224['T224_Flag_Status']) || ($mAllT224['T224_Flag_Status'] == 8)) {
                return "Não é possível agrupar pedidos com status diferente ou cancelado. Por favor, verifique.";
            } else if ($mAllT224['T007_Numero_Nota_Fiscal'] > 0) {
                return "Já existe nota fiscal para o pedido {$mAllT224['T224_Id']}. Por favor, verifique.";
            }
        }
        
        /**
         * Início do looping
         **/
        // Agrupa os campos de ordem de compra e observacao de todos os pedidos
        $T224 = mysql_query("SELECT * FROM T224 WHERE T224_Flag_Duplicar='S'");
        while ($mT224 = mysql_fetch_assoc($T224)) {
            $T224_Ordem_Compra .= $mT224[T224_Ordem_Compra] . ' ';
            $T224_Observacao .= $mT224[T224_Observacao] . ' ';
            $T224_Observacao_Comercial .= $mT224[T224_Observacao_Comercial] . ' ';
            $T224_Observacao_Nota_Fiscal .= $mT224[T224_Observacao_Nota_Fiscal] . ' ';
            $T224_Observacao_1 .= $mT224[T224_Observacao_1] . ' ';
        }
        
        // Cria o novo cabecalho
        $T224  = mysql_query("SELECT * FROM T224 WHERE T224_Flag_Duplicar='S' LIMIT 1");
        $mT224 = mysql_fetch_assoc($T224);
        
        $T224_Id_Antigo = $mT224['T224_Id'];
        $campos         = "";
        $valores        = "";
        foreach ($mT224 as $key => $value) {
            if ($key == 'T224_Id') {
                continue;
            } elseif ($key == "T224_T218_Id") {
                $campos .= $key . ", ";
                $valores .= "'',";
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
            return "Erro : " . mysql_error();
        }
        $T224_Id_Novo = $g['mysqlLastId'];
        
        // Grava o agrupamento dos campos de observacao e ordem de compra
        mysql_query("update T224 set T224_Ordem_Compra='{$T224_Ordem_Compra}',
                                     T224_Observacao='{$T224_Observacao}',
                                     T224_Observacao_Comercial='{$T224_Observacao_Comercial}',
                                     T224_Observacao_Nota_Fiscal='{$T224_Observacao_Nota_Fiscal}',
                                     T224_Observacao_1='{$T224_Observacao_1}'
                               where T224_Id='{$T224_Id_Novo}'");
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "Erro 1: " . mysql_error();
        }
        
        
        // Selecionando todos os itens dos pedidos duplicados
        $T225 = mysql_query("SELECT T225.* FROM T225 LEFT JOIN T224 on T224_Id=T225_T224_Id WHERE T224_Flag_Duplicar='S'");
        $item = 1;
        while ($mT225 = mysql_fetch_assoc($T225)) {
            $campos  = "";
            $valores = "";
            foreach ($mT225 as $key => $value) {
                if ($key == 'T225_Id') {
                    continue;
                } elseif ($key == "T225_T224_Id") {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($T224_Id_Novo) . "',";
                } elseif ($key == "T225_Item") {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($item) . "',";
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
            $item++;
        }
        mysql_query("CALL T224_Gravar_Totalizacao_3('{$T224_Id_Novo}')");
        mysql_query("UPDATE T224 set T224_T224_Id_Agrupado='{$T224_Id_Novo}' WHERE T224_Flag_Duplicar='S' AND T224_Id != $T224_Id_Novo");
        mysql_query("update T224 set T224_Flag_Duplicar='N'");
        mysql_query("COMMIT");
        return true;
    }
    
    /**
     * duplicar_est016form02
     *
     * @param string $divId
     * @param string $divIdRoot
     * @param string $tabela
     * @param int $T224_Id_Antigo
     */
    public function duplicar_est016form02($divId, $divIdRoot, $tabela, $T224_Id_Antigo)
    {
        global $g;
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        // Selecionando o pedido
        $T224  = mysql_query("select * from T224 where T224_Id='{$T224_Id_Antigo}'");
        $mT224 = mysql_fetch_assoc($T224);
        
        if (mysql_num_rows($T224) == 0) {
            $retorno = <<<EOT
                var \$buttons = {
                    "Fechar": function() { $(this).dialog("destroy").remove(); }
                };
                dialogConfirm("Erro", "Pedido não encontrado", \$buttons);
EOT;
            return $retorno;
        }
        
        $campos  = "";
        $valores = "";
        foreach ($mT224 as $key => $value) {
            if ($key == 'T224_Id') {
                continue;
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
        $T224_Id = $T224_Id_Novo;
        mysql_query("COMMIT");
        $novoId = uniqid();
        return "$('#{$divId}').closest('.ui-dialog-content').dialog('close'); abrirJanela(false, '{$divIdRoot}', '{$divId}' ,'{$novoId}', '', 'Editar Pedido', '/est/est016/content/est016content02/', '&acaoId=' + encodeURIComponent('{$T224_Id}') + '&tabela=T224', [990,605]);";
    }
    
    /**
     * verificaCreditoLiberado
     *
     * @param int $T224_Id
     */
    public function verificaCreditoLiberado($T224_Id)
    {
        global $g;
        $usarControleCredito = $g['C031']['usarControleCredito'];
        
        $T224  = mysql_query("SELECT * FROM T224 LEFT JOIN D024 on D024_Id=T224_D024_Id WHERE T224_Id='{$T224_Id}'");
        $mT224 = mysql_fetch_array($T224);
        
        if (($mT224['D024_Flag_Liberado'] == "N" || $mT224['D024_Flag_Restricao'] == "S") AND $usarControleCredito == 'S') {
            
            $msg = "";
            if ($mT224['D024_Flag_Liberado'] == "N") {
                $msg .= "O cliente possui o campo 'Crédito Liberado' definido como NÃO. ";
            }
            if ($mT224['D024_Flag_Restricao'] == "S") {
                $msg .= "O cliente possui o campo 'Cliente possui restrições' definido como SIM.";
            }
            
            mysql_query("update T224 set T224_Motivo_Pedido_Liberacao='{$msg}' WHERE T224_Id='{$T224_Id}'");
            
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * cotarProduto
     *
     * @param Form $form
     */
    public function cotarProduto($form)
    {
        global $g;
        $T225_Id         = $form->campoValorEnviado('T189_T225_Id');
        $D009_Id         = $form->campoValorEnviado('T189_D009_Id');
        $T189_Observacao = $form->campoValorEnviado('T189_Observacao');
        $C007_Id         = $g['usuarioAtual'];
        
        //Busca dados da T219 para inserir na Tabela de Requisição
        $sql = "SELECT * FROM T225 
                LEFT JOIN T224 ON T224_Id = T225_T224_Id
                LEFT JOIN D024 ON D024_Id = T224_D024_Id
                WHERE T225_Id = {$T225_Id}";
        $row = mysql_fetch_assoc(mysql_query($sql));
        //Insere dados na Tabela de Requisição 
        $sql = <<<EOT
            INSERT INTO 
            T189 
                (T189_T219_Id, T189_T225_Id, T189_D009_Id, T189_C007_Id, T189_Data_Criado, T189_Descricao_Produto, T189_Quantidade, T189_Observacao, T189_Detalhes, T189_Flag_Tipo) 
            VALUES 
                ('', '{$T225_Id}', '{$D009_Id}', '{$C007_Id}', CURRENT_TIMESTAMP(), '{$row['T225_Descricao_Produto']}', '{$row['T225_Quantidade']}', '{$T189_Observacao}', 'Pedido Nº: {$row['T224_Id']} - {$row['D024_Nome_Empresa']}', 'C');
EOT;
        mysql_query($sql);
        
        //Seta a Flag comprar = N, para caso esteja selecionada, não permita comprar sem um fornecedor
        mysql_query("UPDATE T225 SET T225_Flag_Comprar = 'N' WHERE T225_Id = '{$T225_Id}'");
        return true;
    }
    
    /**
     * comprarProduto
     *
     * @param Form $form
     */
    public function comprarProduto($form)
    {
        global $g;
        $T225_Id         = $form->campoValorEnviado('T189_T225_Id');
        $D009_Id         = $form->campoValorEnviado('T189_D009_Id');
        $T189_Observacao = $form->campoValorEnviado('T189_Observacao');
        $C007_Id         = $g['usuarioAtual'];
        
        $sql  = "SELECT * FROM T225
                LEFT JOIN T224 ON T224_Id = T225_T224_Id
                LEFT JOIN D024 ON D024_Id = T224_D024_Id
                WHERE T225_Id = {$T225_Id}";
        $row  = mysql_fetch_assoc(mysql_query($sql));
        // Verifica se já existe uma Requisição de cotação
        $sql  = "SELECT * FROM T189 WHERE T189_T225_Id = '{$T225_Id}' AND T189_Flag_Tipo = 'C'";
        $T189 = mysql_query($sql);
        if (mysql_num_rows($T189) > 0) {
            $T189    = mysql_fetch_array($T189);
            $T189_Id = $T189[0];
            mysql_query("UPDATE T189 
                        LEFT JOIN T059 ON T059_T189_Id=T189_Id 
                        LEFT JOIN T174 ON T059_Id=T174_T059_Id 
                        SET 
                        T189_Flag_Tipo = 'P', 
                        T189_Detalhes = 'Pedido Nº: {$row['T224_Id']} - {$row['D024_Nome_Empresa']}', 
                        T189_Flag_Status = 'D',
                        T174_Flag_Gerar_OC='1' 
                        WHERE 
                        T189_T225_Id = '{$T225_Id}'");
            return true;
        } else {
            //Insere dados na Tabela de Requisição 
            $sql = <<<EOT
                INSERT INTO T189 (
                        T189_T225_Id, 
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
                        '{$T225_Id}',
                        '{$D009_Id}', 
                        '{$C007_Id}', 
                        CURRENT_TIMESTAMP(),
                        '{$row['T225_Descricao_Produto']}', 
                        '{$row['T225_Quantidade']}', 
                        '{$T189_Observacao}', 
                        'Pedido Nº: {$row['T224_Id']} - {$row['D024_Nome_Empresa']}', 
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
     */
    public function modificarProdutosSelecionados($form)
    {
        global $g;
        $T225_Id         = $form->campoValorEnviado('T189_T225_Id');
        $D009_Id         = $form->campoValorEnviado('T189_D009_Id');
        $T189_Observacao = $form->campoValorEnviado('T189_Observacao');
        $C007_Id         = $g['usuarioAtual'];
        
        $sql  = "SELECT * FROM T225
                LEFT JOIN T224 ON T224_Id = T225_T224_Id
                LEFT JOIN D024 ON D024_Id = T224_D024_Id
                WHERE T225_Id = {$T225_Id}";
        $row  = mysql_fetch_assoc(mysql_query($sql));
        // Verifica se já existe uma Requisição de cotação
        $sql  = "SELECT * FROM T189 WHERE T189_T225_Id = '{$T225_Id}' AND T189_Flag_Tipo = 'C'";
        $T189 = mysql_query($sql);
        if (mysql_num_rows($T189) > 0) {
            $T189    = mysql_fetch_array($T189);
            $T189_Id = $T189[0];
            mysql_query("UPDATE T189 
                        SET 
                        T189_Flag_Tipo = 'M', 
                        T189_Detalhes = 'Pedido Nº: {$row['T224_Id']} - {$row['D024_Nome_Empresa']}', 
                        T189_Flag_Status = 'D'
                        WHERE 
                        T189_T225_Id = '{$T225_Id}'");
            return true;
        } else {
            //Insere dados na Tabela de Requisição 
            $sql = <<<EOT
                INSERT INTO T189 (
                        T189_T225_Id, 
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
                        '{$T225_Id}',
                        '{$D009_Id}', 
                        '{$C007_Id}', 
                        CURRENT_TIMESTAMP(),
                        '{$row['T225_Descricao_Produto']}', 
                        '{$row['T225_Quantidade']}', 
                        '{$T189_Observacao}', 
                        'Pedido Nº: {$row['T224_Id']} - {$row['D024_Nome_Empresa']}', 
                        'M',
                        'D' 
                    );
EOT;
            mysql_query($sql);
            return true;
        }
    }
    
    /**
     * mudarEmpresaPedidosSelecionados
     *
     * @param int $C004_Id_Novo
     */
    public function mudarEmpresaPedidosSelecionados($C004_Id_Novo)
    {
        global $g;
        $retorno = true;
        require_once('bibliotecas/classes/est012.php');
        $est012 = new est012();
        
        $sqlT224        = mysql_query("SELECT * FROM T224 ORDER BY T224_Processo_Id DESC LIMIT 1");
        $resT224        = mysql_fetch_array($sqlT224);
        $sqlC007        = mysql_query("SELECT * FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}'");
        $resC007        = mysql_fetch_array($sqlC007);
        $sqlC004Origem  = mysql_query("SELECT * FROM C004 WHERE C004_Id = '{$g['empresaAtual']}'");
        $resC004Origem  = mysql_fetch_array($sqlC004Origem);
        $sqlC004Destino = mysql_query("SELECT * FROM C004 WHERE C004_Id = '{$C004_Id_Novo}'");
        $resC004Destino = mysql_fetch_array($sqlC004Destino);
        
        $processoLog = 'Mudança de Empresa: ' . $resC004Destino['C004_Nome_Abreviado'] . '<br />';
        $processoLog .= 'Usuario: ' . $resC007['C007_Primeiro_Nome'] . '(' . $g['usuarioAtual'] . ')<br />';
        $processoLog .= 'Empresa Origem: ' . $resC004Origem['C004_Nome_Abreviado'] . '(' . $g['empresaAtual'] . ')<br />';
        $processoLog .= 'Data: ' . date('d/m/Y', time()) . ' - Hora: ' . date('H:i:s', time());
        
        $lastProcessoId = $resT224['T224_Processo_Id'] + 1;
        
        //Utilizamos o mesmo campo Flag Duplicar, porém com o campo T224_C007_Marcou_Pedido
        $sql = mysql_query("SELECT * FROM T224 WHERE T224_Flag_Duplicar = 'S'");
        while ($row = mysql_fetch_array($sql)) {
            $retorno = $est012->est012MudarFilial($row['T224_Id'], $C004_Id_Novo);
            if ($retorno === true) {
                mysql_query("SET AUTOCOMMIT=1");
                mysql_query("UPDATE T224 SET T224_Processo_Id = '{$lastProcessoId}', T224_Processo_Log = '{$processoLog}' WHERE T224_Id = '{$row['T224_Id']}'");
            }
        }
        return $retorno;
    }
    
    /**
     * est016atualizarCFOP
     *
     * @param int $T224_Id
     */
    public function est016atualizarCFOP($T224_Id)
    {
        require_once("bibliotecas/classes/EST015.php");
        $EST015 = new EST015();
        global $g;
        
        $sqlT224 = mysql_query("SELECT * FROM T224 LEFT JOIN T225 ON T224_Id = T225_T224_Id WHERE T224_Id = '{$T224_Id}'");
        
        while ($T224 = mysql_fetch_array($sqlT224)) {                        
            $D006_Id = $EST015->retornaCFOP($T224['T224_D024_Id'], $T224['T225_D009_Id'], $C004_Id, $T224['T225_Flag_Origem_Produto'],false,false,$T224['T225_Flag_Destino_Produto']);            
            $demonstrativoCFOP = $EST015->retornaCFOP($T224['T224_D024_Id'], $T224['T225_D009_Id'], $C004_Id, $T224['T225_Flag_Origem_Produto'], true,false,$T224['T225_Flag_Destino_Produto']);
            $demonstrativoCFOP = mysql_real_escape_string($demonstrativoCFOP);
            mysql_query("UPDATE T225 
                LEFT JOIN D001 ON D001_Codigo_Produto=T225_Codigo_Produto
                SET T225_D006_Id = '{$D006_Id}', T225_D006_Id_Info = '{$demonstrativoCFOP}', T225_Flag_Pre_Cadastro=D001_Flag_Pre_Cadastro WHERE T225_Id = '{$T224['T225_Id']}'");
        }
        return true;
    }
    
    /**
     * atualizarAliquotasProduto
     *
     * @param int $T224_Id
     * @param int $T225_Id
     */
    public function atualizarAliquotasProduto($T224_Id, $T225_Id)
    {
        global $g;
        require_once("bibliotecas/classes/EST015.php");
        $EST015  = new EST015();
        $sqlT225 = mysql_query("SELECT * FROM T225
                                LEFT JOIN T224 ON T224_Id = T225_T224_Id
                                LEFT JOIN D009 ON D009_Id = T225_D009_Id
                                LEFT JOIN D049 ON D049_Id = D009_D049_Id
                                LEFT JOIN D001 ON D001_Id = D049_D001_Id
                                WHERE T225_Id = '{$T225_Id}'");
        $T225    = mysql_fetch_array($sqlT225);
        
        $Aliquotas = $EST015->retornaAliquotas($T225['D009_Id'], false, $T224_Id, false, false, $T225_Id);
        
        $cClausula = " UPDATE T225 ";
        $cClausula .= " LEFT JOIN D006 on D006_Id=T225_D006_Id ";
        $cClausula .= " LEFT JOIN D059 on D059_Id=D006_D059_Id ";
        $cClausula .= " LEFT JOIN T224 on T224_Id=T225_T224_Id ";
        $cClausula .= " LEFT JOIN C004 on C004_Id=T224_C004_Id ";
        $cClausula .= " LEFT JOIN D005 on D005_Id=T225_D005_Id ";
        $cClausula .= " LEFT JOIN D149 on D005_Id=D149_D005_Id AND D149_C004_Id=C004_Id";
        $cClausula .= " SET ";
        if($T225['T224_Flag_Calcular_IPI'] == 'S'){
            $cClausula .= " T225_Aliquota_IPI='{$Aliquotas['Aliquota_IPI']}',";
            $cClausula .= " T225_Percentual_Reducao_IPI='{$Aliquotas['Percentual_Reducao_IPI']}',";
            $cClausula .= " T225_Situacao_Tributaria_IPI='{$Aliquotas['Situacao_Tributaria_IPI']}', ";
        }
        if($T225['T224_Flag_Calcular_ICMS'] == 'S'){
            $cClausula .= " T225_Aliquota_ICMS='{$Aliquotas['Aliquota_ICMS']}',";
            $cClausula .= " T225_Percentual_Reducao_ICMS='{$Aliquotas['Percentual_Reducao_ICMS']}',";
            $cClausula .= " T224_Aliquota_ICMS='{$Aliquotas['Aliquota_ICMS']}',";
            $cClausula .= " T224_Percentual_Reducao_ICMS='{$Aliquotas['Percentual_Reducao_ICMS']}',";
        }
        if($T225['T224_Flag_Calcular_ST'] == 'S'){
            $cClausula .= " T225_Percentual_Reducao_ICMS_ST='{$Aliquotas['Percentual_Reducao_ICMS_ST']}',";
            $cClausula .= " T225_Flag_ST=D006_Flag_Substituicao_Tributaria,";
            $cClausula .= " T225_ST_VA='{$Aliquotas['ST_VA']}', ";
            $cClausula .= " T225_ST_ICMS_Interno='{$Aliquotas['ST_ICMS_Interno']}',";
            $cClausula .= " T225_ICMS_Inter_Estadual='{$Aliquotas['ICMS_Inter_Estadual']}',";
            $cClausula .= " T225_ICMS_Inter_Estadual_MVA='{$Aliquotas['ST_ICMS_Inter_Estadual_MVA']}',";
            $cClausula .= " T225_ST_ICMS_Interno_MVA='{$Aliquotas['ST_ICMS_Interno_MVA']}',";
        }
        $cClausula .= " T225_D005_Id='{$Aliquotas['D005_Id']}',";
        $cClausula .= " T225_D059_Id='{$Aliquotas['D059_Id']}', ";
        if (!empty($T225['D024_Situacao_Tributaria_PIS_COFINS'])) {
            $cClausula .= " T225_Situacao_Tributaria_PIS='{$T225['D024_Situacao_Tributaria_PIS_COFINS']}',";
            $cClausula .= " T225_Situacao_Tributaria_COFINS='{$T225['D024_Situacao_Tributaria_PIS_COFINS']}',";
            if ($T225['D024_Flag_Isento_PIS_COFINS'] == 'S') {
                $cClausula .= " T225_Flag_Isento_PIS='{$T225['D024_Flag_Isento_PIS_COFINS']}',";
                $cClausula .= " T225_Aliquota_PIS='0',";
                $cClausula .= " T225_Flag_Isento_COFINS='{$T225['D024_Flag_Isento_PIS_COFINS']}',";
                $cClausula .= " T225_Aliquota_COFINS='0',";
            } else {
                $cClausula .= " T225_Flag_Isento_PIS=D006_Flag_Isento_PIS,";
                $cClausula .= " T225_Aliquota_PIS=if(D149_Aliquota_PIS>0,D149_Aliquota_PIS,C004_Percentual_PIS),";
                $cClausula .= " T225_Flag_Isento_COFINS=D006_Flag_Isento_COFINS,";
                $cClausula .= " T225_Aliquota_COFINS=if(D149_Aliquota_COFINS>0,D149_Aliquota_COFINS,C004_Percentual_COFINS),";
            }
        } else {
            $cClausula .= " T225_Flag_Isento_PIS=D006_Flag_Isento_PIS,";
            $cClausula .= " T225_Aliquota_PIS=if(D149_Aliquota_PIS>0,D149_Aliquota_PIS,C004_Percentual_PIS),";
            $cClausula .= " T225_Situacao_Tributaria_PIS=D006_Situacao_Tributaria_PIS,";
            $cClausula .= " T225_Flag_Isento_COFINS=D006_Flag_Isento_COFINS,";
            $cClausula .= " T225_Aliquota_COFINS=if(D149_Aliquota_COFINS>0,D149_Aliquota_COFINS,C004_Percentual_COFINS),";
            $cClausula .= " T225_Situacao_Tributaria_COFINS=D006_Situacao_Tributaria_COFINS, ";
        }
        $cClausula .= " T225_Classificacao_Fiscal='{$Aliquotas['Classificacao_Fiscal']}',";
        $cClausula .= " T225_Flag_Situacao_Tributaria='{$Aliquotas['Flag_Situacao_Tributaria']}', ";
        $cClausula .= " T225_Peso_Unitario='{$T225['D001_Peso_Unitario_Kg']}' ";
        $cClausula .= " where T225_Id='{$T225['T225_Id']}'";
        mysql_query($cClausula);

        $T225A = mysql_query("SELECT T225A_Id FROM T225A WHERE T225A_T225_Id='{$T225['T225_Id']}'");
        if (mysql_num_rows($T225A) > 0) {
            $sql = "UPDATE T225A 
                       SET T225A_Percentual_Reducao_ICMS_IE_ST  ='{$Aliquotas['Percentual_Reducao_ICMS_IE_ST']}',
                           T225A_Percentual_Reducao_ICMS_INT_ST ='{$Aliquotas['Percentual_Reducao_ICMS_INT_ST']}',
                           T225A_Aliquota_FCP                   ='{$Aliquotas['Percentual_FCP']}'  
                     WHERE T225A_T225_Id='{$T225['T225_Id']}'";
        } else {
            $sql = "INSERT INTO T225A (T225A_T225_Id,
                                       T225A_Percentual_Reducao_ICMS_IE_ST,
                                       T225A_Percentual_Reducao_ICMS_INT_ST,
                                       T225A_Aliquota_FCP)
                         VALUES ('{$T225['T225_Id']}', 
                                 '{$Aliquotas['Percentual_Reducao_ICMS_IE_ST']}',
                                 '{$Aliquotas['Percentual_Reducao_ICMS_INT_ST']}',
                                 '{$Aliquotas['Percentual_FCP']}')";
        }
        mysqli_query($sql);
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "Erro atualizando item da cotação T225A ID T225_Id '{$T225['T225_Id']}': $erro";
        }        
        return true;
    }
    
    /**
     * insereOCItensViaRequisicao
     */
    public function insereOCItensViaRequisicao()
    {
        global $g, $T224_Id;
        
        $T224       = <<<EOT
        INSERT INTO T224 (
            T224_C007_Id_Vendedor_Interno,
            T224_C004_Id,
            T224_Data_Emissao
        ) VALUES (
            '{$g['usuarioAtual']}',
            '{$g['empresaAtual']}',
            CURDATE()
        );
EOT;
        $resultT224 = mysql_query($T224);
        if (!$resultT224) {
            $erro = mysql_error();
            return "Erro: " . $erro;
        }
        $T224_Id = $g['mysqlLastId'];
        
        $this->insereItemEstoqueRequisicao($T224_Id);
        
        return true;
    }
    
    /**
     * insereItemEstoqueRequisicao
     *
     * @param int $T224_Id
     */
    public function insereItemEstoqueRequisicao($T224_Id)
    {
        global $g;
        $sql = <<<EOT
                select *,
                D001_Codigo_Produto,
                D001_Descricao_Produto,
                D009_Quantidade_Comprar_Calculada,D009_Id,T189_Quantidade,T189_Flag_Cotar,T189_Id,D041_Valor_Base,T189_Descricao_Produto,T189_D009_Id 
                from D001 
                left join D049 on D049_D001_Id=D001_Id
                left join D009 on D009_D049_Id=D049_Id
                left join D082 on D082_Id=D049_D082_Id
                left join D041 on D041_D049_Id=D049_Id
                left join D005 on D005_Id=D001_D005_Id
                LEFT JOIN T189 on T189_D009_Id=D009_Id
                left join D037 ON D037_Id=D001_D037_Id
                where T189_Flag_Comprar='S'
                AND T189_C007_Id_Comprar='{$g['usuarioAtual']}'
EOT;
        $retorno = mysql_query($sql);
        
        $count = 1;
        while ($row = mysql_fetch_array($retorno)) {
            $sqlInsert = <<<EOT
                insert into T225
                (T225_T224_Id,
                T225_Codigo_Produto,
                T225_Descricao_Produto,
                T225_Quantidade,
                T225_Valor_Custo_Unitario,
                T225_D009_Id,
                T225_T189_Id,
                T225_D037_Id,
                T225_Item)
                values 
                ('{$T224_Id}',
                '{$row['D001_Codigo_Produto']}',
                '{$row['T189_Descricao_Produto']}',
                '{$row['T189_Quantidade']}',
                '{$row['D041_Valor_Base']}',
                '{$row['T189_D009_Id']}',
                '{$row['T189_Id']}',
                '{$row['D037_Id']}',
                '{$count}')
EOT;
            $count++;
            mysql_query($sqlInsert);
            mysql_query("UPDATE T189 SET T189_Flag_Comprar = 'N' WHERE T189_C007_Id_Comprar='{$g['usuarioAtual']}';");
            
            $cSql = mysql_query("call T225_Gravar_Totalizacao('{$g['mysqlLastId']}');");
            $cSql = mysql_query("call T224_Gravar_Totalizacao_3('{$T224_Id}');");
        }
    }
    
    /**
     * est016NaoLiberarPedido
     *
     * @param Form $form
     */
    public function est016NaoLiberarPedido($form)
    {
        global $g;
        $T224_Id                                     = $form->campoValorEnviado('T224_Id');
        $T224_Liberou_Alcada_Observacao_Nao_Liberado = $form->campoValorEnviado('T224_Liberou_Alcada_Observacao_Nao_Liberado');
        
        if (empty($T224_Id)) {
            return "T224_Id não existe";
        }
        $T224_Liberou_Alcada_Observacao_Nao_Liberado = trim($T224_Liberou_Alcada_Observacao_Nao_Liberado);
        $T224_Liberou_Alcada_Observacao_Nao_Liberado = addslashes($T224_Liberou_Alcada_Observacao_Nao_Liberado);
        
        if (empty($T224_Liberou_Alcada_Observacao_Nao_Liberado)) {
            return "Preencha o campo Motivo.";
        }
        
        $cSql  = mysql_query("update T224 set T224_Liberou_Alcada_C007_Id='{$g['usuarioAtual']}',
                                      T224_Liberou_Alcada_Data=now(),
                                      T224_Flag_Status='2',
                                      T224_Nome_Status = T224_Status_Pedido(T224_Flag_Status, T224_Id),
                                      T224_Liberou_Alcada_IPV=0,
                                      T224_Liberou_Alcada_Prazo_Medio=0,
                                      T224_Liberou_Alcada_Desconto=0,
                                      T224_Flag_Status_Liberacao='3',
                                      T224_Liberou_Alcada_Observacao_Nao_Liberado='{$T224_Liberou_Alcada_Observacao_Nao_Liberado}',
                                      T224_Flag_Visualizou_Liberacao='N'
                                where T224_Id='{$T224_Id}'");
        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "Ocorreu um erro ao tentar alterar o registro/p/pErro:$cErro/p/pClausula:$cClausula";
        } else {
            return true;
        }
    }
    
    /**
     * est016NaoLiberarPedido
     *
     * @param int $T224_Id
     * @param boolean $viaTablet
     */
    public function est016AprovarPedido($T224_Id, $viaTablet = false)
    {
        global $g, $mensagem;
        
        $retorno = mysql_query("SELECT * FROM T224 WHERE T224_Id='{$T224_Id}'");
        $T224    = mysql_fetch_array($retorno);
        $total   = $T224['T224_Valor_Total_Produtos'];
        
        if ($viaTablet == false) {
            $desconto = $this->calculaPercentualDescontoPedido($T224_Id);
        } else {
            // PEGA O VALOR DO DESCONTO MANUAL QUE É APLICADO NO VALOR TOTAL DO ORÇAMENTO
            $total    = $T224['T224_Valor_Total'];
            $desconto = ($T224['T224_Valor_Desconto_Manual'] * 100) / ($T224['T224_Valor_Desconto_Manual'] + $T224['T224_Valor_Total']);
        }
        
        $total = $T224['T224_Valor_Total'];
        
        $status = '3';
        if($T224['T224_Flag_Coleta'] == 'S'){
            $status = '5';
        }

        $retorno = mysql_query("SELECT AVG(T226_Prazos_Dias) AS PrazoMedio FROM T226 WHERE T226_T224_Id='{$T224_Id}'");
        $T226    = mysql_fetch_array($retorno);
        
        $cSql  = mysql_query("update T224 set T224_Liberou_Alcada_C007_Id='{$g['usuarioAtual']}',
                                      T224_Liberou_Alcada_Data=now(),
                                      T224_Liberou_Alcada_IPV=T224_IPV,
                                      T224_Flag_Status='{$status}',
                                      T224_Nome_Status = T224_Status_Pedido(T224_Flag_Status, T224_Id),
                                      T224_Flag_Status_Liberacao='2',
                                      T224_Liberou_Alcada_Valor_Total='{$total}',                                      
                                      T224_Liberou_Alcada_Prazo_Medio='{$T226['PrazoMedio']}',
                                      T224_Liberou_Alcada_Desconto='{$desconto}',
                                      T224_Flag_Visualizou_Liberacao='N',
                                      T224_Liberou_Alcada_Observacao_Nao_Liberado=''
                                where T224_Id='{$T224_Id}'");
        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "Ocorreu um erro ao tentar alterar o registro<br />Erro:$cErro<br />Clausula:$cClausula";
        }
        
        $sqlT225 = mysql_query("UPDATE T225 LEFT JOIN D009 on D009_Id=T219_D009_Id SET T225_Liberou_Alcada_Saldo_Futuro=D009_Quantidade_Estoque_Liquido-T225_Quantidade WHERE T225_T224_Id='{$T224_Id}'");
        
        $this->PersonalizacaoTriggerPosAprovacaoOc(["T224_Id" => $T224_Id]);

        return "Ordem de compra aprovada com sucesso!";
        /*$FaturarPedidoAposAnalisePedido = $g['C031']['FaturarPedidoAposAnalisePedido'];
        
        if ($FaturarPedidoAposAnalisePedido == "S") {
            $mensagem = '';
            $retorno  = $VEN002->faturarPedido($T224_Id);
            $msg      = "<b>Análise de pedido:</b><br />Pedido Aprovado com sucesso!<br /><br /><b>Faturamento automático:</b><br />";
            $msg .= $retorno;
            if (empty($mensagem)) {
                return $msg;
            } else {
                $msg .= $mensagem;
                return $msg;
            }
            
        } else {
        }*/
    }
    
    /**
     * insereItemEstoque
     *
     * @param int $T224_Id
     */
    public function insereItemEstoque($T224_Id)
    {
        global $g;
        $sql     = <<<EOT
                select 
                *
                from D001 
                left join D049 on D049_D001_Id=D001_Id
                left join D009 on D009_D049_Id=D049_Id
                left join D082 on D082_Id=D049_D082_Id
                left join D041 on D041_D049_Id=D049_Id
                left join D005 on D005_Id=D001_D005_Id
                left join D037 on D037_Id=D001_D037_Id
                where D009_Flag_Comprar='S'
                AND D009_C004_Id={$g['empresaAtual']}
EOT;
        $retorno = mysql_query($sql);
        
        $i = 1;
        while ($row = mysql_fetch_array($retorno)) {
            
            
            // Caso tenha sido preenchido a quantidade de compra manualmente será usado esse valor, se não, 
            // será usado o valor que o sistema calculou automaticamente no reprocessa_historico();
            $quantidadeComprar = ($row['D009_Quantidade_Comprar'] > 0) ? $row['D009_Quantidade_Comprar'] : $row['D009_Quantidade_Comprar_Calculada'];
            
            $sqlInsert = <<<EOT
                insert into T225
                (T225_T224_Id,
                T225_Codigo_Produto,
                T225_Descricao_Produto,
                T225_Quantidade,
                T225_Valor_Custo_Unitario, 
                T225_D009_Id, 
                T225_D037_Id, 
                T225_D005_Id,
                T225_Item) 
                values 
                ('{$T224_Id}',
                '{$row['D001_Codigo_Produto']}',
                '{$row['D001_Descricao_Produto']}',
                '{$quantidadeComprar}',
                '{$row['D041_Valor_Base']}',
                '{$row['D009_Id']}',
                '{$row['D037_Id']}',
                '{$row['D005_Id']}',
                '{$i}');
EOT;
            mysql_query($sqlInsert);
            
            $cSql = mysql_query("call T225_Gravar_Totalizacao('{$g['mysqlLastId']}');");
            $cSql = mysql_query("call T224_Gravar_Totalizacao('{$T224_Id}');");
            $i++;
        }
    }
    
    /**
     * EST016ProcessarOCVencida
     */
    public function EST016ProcessarOCVencida($T224_Id = false)
    {
        global $g;

        if($T224_Id){
            $T224_Id = implode(",", $T224_Id);
            $sql = mysql_query("SELECT T224_Id 
                                  FROM T224
                             LEFT JOIN T225 ON T224_Id = T225_T224_Id 
                             LEFT JOIN T014 ON T014_T225_Id = T225_Id 
                             LEFT JOIN T013 ON T013_Id = T014_T013_Id
                                 WHERE T224_Id IN ({$T224_Id})
                                   AND DATEDIFF(CURDATE(), T224_Data_Emissao) > '{$g['C031']['PrazoVencerOrdemCompra']}'
                                   AND (T014_T225_Id IS NULL OR T013_Flag_Travar_Nota!='S')
                                   AND T224_Flag_Status != 8 
                                   AND T224_Flag_Status != 12 
                              GROUP BY T224_Id");

        } else {
        
            $sql = mysql_query("SELECT T224_Id 
                                  FROM T224
                             LEFT JOIN T225 ON T224_Id = T225_T224_Id 
                             LEFT JOIN T014 ON T014_T225_Id = T225_Id 
                             LEFT JOIN T013 ON T013_Id = T014_T013_Id
                                 WHERE T224_C004_Id = '{$g['empresaAtual']}' 
                                   AND DATEDIFF(CURDATE(), T224_Data_Emissao) > '{$g['C031']['PrazoVencerOrdemCompra']}'
                                   AND (T014_T225_Id IS NULL OR T013_Flag_Travar_Nota!='S')
                                   AND T224_Flag_Status != 8 
                                   AND T224_Flag_Status != 12 
                              GROUP BY T224_Id");
        }

        if(mysql_num_rows($sql)<=0){
            return 'Não foram encontrada ordens de compra para processar';
        }

        $count = 0;
        while ($row = mysql_fetch_array($sql)) {
            mysql_query("UPDATE T224 SET T224_Flag_Status = '12',
                                         T224_Nome_Status = T224_Status_Pedido(T224_Flag_Status, T224_Id) 
                                   WHERE T224_Id = '{$row['T224_Id']}'");

            $cErro = mysql_error();
            if (!empty($cErro)) {
                return $cErro;
            }
            $count++;
        }
        return array('totalProcessada' => $count);
    }

    /**
     * gerarOT - Gera uma Ordem de Transferência
     * 
     * @param $C004_Id - Empresa Origem
     * @param $D049s - array(
     *    array(
     *      'D049_Id' => 
     *      'Quantidade' =>
     *    )
     * )
     */ 
    public function gerarOT($C004_Id, $D049s = false, $gerarM5 = 'N') {
        global $g, $T224_Id;
        $arrayRetorno = array();
        $retorno = "";

        // gerado pelo Crontab, não recebe o array D049
        if($D049s === false){
            $D049s = array();
            $D009 = mysql_query("SELECT D009_Quantidade_Comprar,
                                        D049_Id
                                   FROM D009
                              LEFT JOIN D049 ON D049_Id = D009_D049_Id
                                  WHERE D009_C004_Id = '{$g['empresaAtual']}'
                                    AND D009_Quantidade_Comprar > 0
                                    AND D009_C007_Id_Comprar = '{$g['usuarioAtual']}'");

            if(mysql_num_rows($D009)<=0){
                return "Não tem itens para a reposição automática";
            }               

            while($mD009 = mysql_fetch_array($D009)){
                $D049s[] = array('D049_Id'    => $mD009['D049_Id'],
                                 'Quantidade' => $mD009['D009_Quantidade_Comprar']);
            }
        }

        // FAZENDO AS VALIDAÇÕES ANTES DE INICIAR A TRANSAÇÃO.
        foreach ($D049s as $item) {
            // Verificações
            $sql = <<<EOT
                SELECT 
                    D009_Quantidade_Estoque_Liquido,
                    D001_Codigo_Produto
                FROM
                    D049
                LEFT JOIN D009 ON D009_D049_Id = D049_Id
                LEFT JOIN D001 ON D001_Id = D049_D001_Id
                WHERE 
                    D049_Id = '{$item['D049_Id']}'
                    AND D009_C004_Id = '{$C004_Id}'
EOT;
            $resD009Distribuidora = mysql_query($sql);
            if ($erro = mysql_error()) {
                
                return 'Erro: ' . $erro;
            }
            if (mysql_num_rows($resD009Distribuidora) <= 0) {
                
                $retorno.= "O item {$item['D049_Id']} não foi encontrado na empresa {$C004_Id}. Por favor, contate o suporte.<br/><br/>";
            }
            $rowD009Distribuidora = mysql_fetch_assoc($resD009Distribuidora);
            if ($rowD009Distribuidora['D009_Quantidade_Estoque_Liquido'] < $item['Quantidade'] && $gerarOTM5 == 'N') {
                
                $item['Quantidade'] = gCorrigeNumero($item['Quantidade']);
                $rowD009Distribuidora['D009_Quantidade_Estoque_Liquido'] = gCorrigeNumero($rowD009Distribuidora['D009_Quantidade_Estoque_Liquido']);
                $retorno.= "O produto {$rowD009Distribuidora['D001_Codigo_Produto']} não possui estoque suficiente na empresa {$rowC004['C004_Nome_Abreviado']}.<br/>Estoque disponível: {$rowD009Distribuidora['D009_Quantidade_Estoque_Liquido']}<br/>Estoque requerido: {$item['Quantidade']}<br/><br/>";
            }

            // D009 local
            $sql = <<<EOT
                SELECT 
                    D009_Id,
                    D009_Quantidade_Estoque_Liquido,
                    D001_Descricao_Produto,
                    D001_Codigo_Produto,
                    D001_D037_Id,
                    D009_D001_Id,
                    D049_Origem_Mercadoria,
                    IFNULL(D009_Origem_Mercadoria,'') as D009_Origem_Mercadoria
                FROM
                    D049
                LEFT JOIN D009 ON D009_D049_Id = D049_Id
                LEFT JOIN D001 ON D001_Id = D049_D001_Id
                WHERE 
                    D049_Id = '{$item['D049_Id']}'
                    AND D009_C004_Id = '{$g['empresaAtual']}'
EOT;
            $resD009 = mysql_query($sql);
            if ($erro = mysql_error()) {
                mysql_query("ROLLBACK");
                return 'Erro: ' . $erro;
            }
            if (mysql_numrows($resD009) <= 0) {
                
                $retorno.= "O item {$item['D049_Id']} não foi encontrado na empresa {$g['empresaAtual']}. Por favor, contate o suporte.<br/><br/>";
            }

        }
        if(!empty($retorno)){
            return $retorno;
        }

         //// ------------------------------------------ FIM VALIDAÇÃO

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        // Empresa Destino
        $sql = "SELECT * FROM C004 LEFT JOIN D024 ON D024_Id = C004_D024_Id WHERE C004_Id = '{$C004_Id}'";
        $resC004 = mysql_query($sql);
        if ($erro = mysql_error()) {
            mysql_query("ROLLBACK");
            return 'Erro: ' . $erro;
        }
        $rowC004 = mysql_fetch_assoc($resC004);

        // Cria o cabeçalho da OC
        $insertT224 = array(
            'T224_Flag_Tipo' => 2,
            // 'T224_T218_Id' => 
            //'T224_D013_Id' => 
            'T224_D024_Id' => $rowC004['C004_D024_Id'],
            //'T224_D021_Id' => 
            'T224_C004_Id' => $g['empresaAtual'],
            //'T224_D006_Id' => 
            'T224_C007_Id_Vendedor_Interno' => $g['usuarioAtual'],
            //'T224_Observacao' => 
            //'T224_Observacao_Nota_Fiscal' => 
            'T224_Flag_ACP' => 6,
            'T224_Flag_Status' => 0,
            'T224_Data_Emissao' => 'CURDATE()',
            'T224_Flag_Situacao' => 1,
            'T224_Flag_Revenda_Consumidor' => $rowC004['D024_Flag_Revenda_Consumidor'],
            //'T224_D022_Id' => 
            //'T224_D022_Id_Default' => 
            //'T224_Observacao_Pagamento' => 
            //'T224_Pgto_Parcelas' => 
            //'T224_Pgto_Primeiro_Vcto_Tipo' => 
            //'T224_Pgto_Primeiro_Vcto_Valor' => 
            //'T224_Pgto_Dias_Vcto_Tipo' => 
            //'T224_Pgto_Dias_Vcto_Valor' => 
            //'T224_Pgto_Portador' => 
            //'T224_Pgto_Data' => 
            //'T224_Pgto_C007_Id' => 
            //'T224_D116_Id' => 
            //'T224_Liberou_Alcada_C007_Id' => 
            //'T224_Liberou_Alcada_Data' => 
            //'T224_Liberou_Alcada_IPV' => 
            //'T224_Liberou_Alcada_Valor_Total' => 
            //'T224_Liberou_Alcada_Prazo_Medio' => 
            //'T224_Liberou_Alcada_Desconto' => 
            //'T224_ST_ICMS_Interno' => 
            //'T224_ICMS_Inter_Estadual' => 
            //'T224_Flag_Frete' => 
            //'T224_Flag_Lista_Preco' => 
            //'T224_Valor_Frete' => 
            //'T224_Flag_Expedicao' => 
            //'T224_Percentual_Comissao' => 
            //'T224_Valor_Desconto' => 
            //'T224_Valor_Desconto_Manual' => 
            //'T224_Flag_Almoxarifado' => 
            //'T224_Total_Cubagem' => 
            //'T224_Peso_Total' => 
            //'T224_Peso_Bruto_Total' => 
            //'T224_Valor_Total_Trib_Transparencia' => 
            //'T224_Flag_Status_Liberacao' => 
            //'T224_Prazo_Entrega' => 
            //'T224_Prazos_Pagamentos' => 
        );
        $sqlChaves = '';
        $sqlDados = '';
        foreach ($insertT224 as $key => $value) {
            $sqlChaves .= $sqlChaves ? ',' : '';
            $sqlChaves .= $key;
            $sqlDados .= $sqlDados ? ',' : '';
            if ($key == 'T224_Data_Emissao') { 
                $sqlDados .= $value;
            } else {
                $sqlDados .= "'" . mysql_real_escape_string($value) . "'";
            }
        }
        $sql = "INSERT INTO T224 ( {$sqlChaves} ) VALUES ( {$sqlDados} )";
        mysql_query($sql);
        if ($erro = mysql_error()) {
            mysql_query("ROLLBACK");
            return 'Erro: ' . $erro;
        }
        $T224_Id = $g['mysqlLastId'];
        $arrayRetorno['T224_Id'] = $T224_Id;

        foreach ($D049s as $item) {
            // Verificações
            $sql = <<<EOT
                SELECT 
                    D009_Quantidade_Estoque_Liquido,
                    D001_Codigo_Produto
                FROM
                    D049
                LEFT JOIN D009 ON D009_D049_Id = D049_Id
                LEFT JOIN D001 ON D001_Id = D049_D001_Id
                WHERE 
                    D049_Id = '{$item['D049_Id']}'
                    AND D009_C004_Id = '{$C004_Id}'
EOT;
            $resD009Distribuidora = mysql_query($sql);
            if ($erro = mysql_error()) {
                mysql_query("ROLLBACK");
                return 'Erro: ' . $erro;
            }
            if (mysql_numrows($resD009Distribuidora) <= 0) {
                mysql_query("ROLLBACK");
                //$retorno.= "O item {$item['D049_Id']} não foi encontrado na empresa {$C004_Id}. Por favor, contate o suporte.<br/><br/>";
            }
            $rowD009Distribuidora = mysql_fetch_assoc($resD009Distribuidora);
            if ($rowD009Distribuidora['D009_Quantidade_Estoque_Liquido'] < $item['Quantidade'] && $gerarOTM5=='N') {
                mysql_query("ROLLBACK");
                $item['Quantidade'] = gCorrigeNumero($item['Quantidade']);
                $rowD009Distribuidora['D009_Quantidade_Estoque_Liquido'] = gCorrigeNumero($rowD009Distribuidora['D009_Quantidade_Estoque_Liquido']);
                //$retorno.= "O produto {$rowD009Distribuidora['D001_Codigo_Produto']} não possui estoque suficiente na empresa {$rowC004['C004_Nome_Abreviado']}.<br/>Estoque disponível: {$rowD009Distribuidora['D009_Quantidade_Estoque_Liquido']}<br/>Estoque requerido: {$item['Quantidade']}<br/><br/>";
            }

            // D009 local
            $sql = <<<EOT
                SELECT 
                    D009_Id,
                    D009_Quantidade_Estoque_Liquido,
                    D001_Descricao_Produto,
                    D001_Codigo_Produto,
                    D001_D037_Id,
                    D009_D001_Id,
                    D049_Origem_Mercadoria,
                    IFNULL(D009_Origem_Mercadoria,'') as D009_Origem_Mercadoria
                FROM
                    D049
                LEFT JOIN D009 ON D009_D049_Id = D049_Id
                LEFT JOIN D001 ON D001_Id = D049_D001_Id
                WHERE 
                    D049_Id = '{$item['D049_Id']}'
                    AND D009_C004_Id = '{$g['empresaAtual']}'
EOT;
            $resD009 = mysql_query($sql);
            if ($erro = mysql_error()) {
                mysql_query("ROLLBACK");
                return 'Erro: ' . $erro;
            }
            if (mysql_numrows($resD009) <= 0) {
                mysql_query("ROLLBACK");
                //$retorno.= "O item {$item['D049_Id']} não foi encontrado na empresa {$g['empresaAtual']}. Por favor, contate o suporte.<br/><br/>";
            }
            $rowD009 = mysql_fetch_assoc($resD009);

            if($rowD009['D009_Origem_Mercadoria'] != ''){
                $rowD009['D049_Origem_Mercadoria'] = $rowD009['D009_Origem_Mercadoria'];
            }
            
            // Cria o item da OC
            $insertT225 = array(
                //'T225_Codigo_Substituto' => 
                //'T225_Flag_Status' => 
                //'T225_Item' => 
                //'T225_T219_Id' => 
                'T225_D037_Id' => $rowD009['D001_D037_Id'],
                'T225_D005_Id' => $rowD009['D009_D001_Id'],
                //'T225_D006_Id' => 
                //'T225_Classificacao_Fiscal' => 
                'T225_D009_Id' => $rowD009['D009_Id'],
                'T225_Codigo_Produto' => $rowD009['D001_Codigo_Produto'],
                //'T225_Marca' => 
                'T225_Descricao_Produto' => $rowD009['D001_Descricao_Produto'],
                'T225_Quantidade' => $item['Quantidade'],
                //'T225_Valor_Preco_Bruto' => 
                //'T225_Valor_Custo_Unitario' => 
                //'T225_Valor_Preco_Unitario' => 
                //'T225_Valor_Preco_Sem_Desconto_Unitario' => 
                //'T225_Valor_Preco_Original_Bruto' => 
                //'T225_Percentual_Desconto_Bruto' => 
                //'T225_IPV' => 
                'T225_T224_Id' => $T224_Id,
                'T225_C004_Id' => $g['empresaAtual'],
                //'T225_Numero_Pedido_Compra' => 
                //'T225_Item_Pedido_Compra' => 
                //'T225_Aliquota_IPI' => 
                //'T225_Aliquota_ICMS' => 
                //'T225_Percentual_Reducao_ICMS' => 
                //'T225_Percentual_Reducao_ICMS_ST' => 
                //'T225_ST_VA' => 
                //'T225_Flag_ST' => 
                //'T225_Flag_Situacao_Tributaria' => 
                //'T225_ICMS_Inter_Estadual' => 
                //'T225_ST_ICMS_Interno' => 
                //'T225_Flag_Isento_PIS' => 
                //'T225_Aliquota_PIS' => 
                //'T225_Situacao_Tributaria_PIS' => 
                //'T225_Flag_Isento_COFINS' => 
                //'T225_Aliquota_COFINS' => 
                //'T225_Flag_Preco_Especial' => 
                //'T225_Situacao_Tributaria_COFINS' => 
                //'T225_Situacao_Tributaria_IPI' => 
                //'T225_D059_Id' => 
                //'T225_Valor_Preco_Original' => 
                //'T225_Percentual_Desconto' => 
                //'T225_Liberou_Alcada_Saldo_Futuro' => 
                //'T225_Quantidade_Estoque' => 
                //'T225_T075_Id' => 
                //'T225_T206_Id' => 
                //'T225_Cubagem_Unitaria' => 
                //'T225_Total_Cubagem' => 
                //'T225_Peso_Bruto' => 
                //'T225_Peso_Bruto_Total' => 
                //'T225_Peso_Unitario' => 
                //'T225_Peso_Total' => 
                //'T225_Valor_Trib_Transparencia' => 
                //'T225_Percentual_Trib_Transparencia' => 
                //'T225_Flag_Pre_Cadastro' => 
                //'T225_D144_Id' => 
                //'T225_Observacao_Entrega' => 
                //'T225_Observacao' => 
                'T225_Flag_Origem_Produto' => $rowD009['D049_Origem_Mercadoria'],
                //'T225_Valor_PReco_Venda_Negociado' => 
                //'T225_Percentual_Frete' => 
                //'T225_Percentual_Financeiro' => 
                //'T225_D006_Id_Info' => 
                //'T225_D005_Id_Info' => 
            );
            $sqlChaves = '';
            $sqlDados = '';
            foreach ($insertT225 as $key => $value) {
                $sqlChaves .= $sqlChaves ? ',' : '';
                $sqlChaves .= $key;
                $sqlDados .= $sqlDados ? ',' : '';
                if ($key == 'T225_Data_Emissao') { 
                    $sqlDados .= $value;
                } else {
                    $sqlDados .= "'" . mysql_real_escape_string($value) . "'";
                }
            }
            $sql = "INSERT INTO T225 ( {$sqlChaves} ) VALUES ( {$sqlDados} )";
            mysql_query($sql);
            if ($erro = mysql_error()) {
                mysql_query("ROLLBACK");
                return 'Erro: ' . $erro;
            }
            mysql_query("UPDATE D009 SET D009_Flag_Cotar = 'N', D009_C007_Id_Comprar = '0', D009_Quantidade_Comprar = '0' WHERE D009_Id = '{$rowD009['D009_Id']}'");
            $T225_Id = $g['mysqlLastId'];

            // Se vier de uma compra de emergência, é feito a associação ITEM PEDIDO - ITEM OC
            if (isset($item['T006_Id'])) {
                $sql = "UPDATE T006 SET T006_T225_Id = '{$T225_Id}' where T006_Id = '{$item['T006_Id']}'";
                mysql_query($sql);
                if ($erro = mysql_error()) {
                    mysql_query("ROLLBACK");
                    return 'Erro: ' . $erro;
                }
            }
        }

        // Atualiza a CFOP dos itens e as aliquotas
        //esse método agora é executado em etapas no form - Lígia Nakazato 28/08/2018
        //$this->est016atualizarCFOP($T224_Id);

        // Já gera o pedido também
        /*$retorno = $this->gerarOTPedido($T224_Id);
        if ($retorno !== true) {
            return $retorno;
        }*/

        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");
        //return true;
        return $arrayRetorno;
    }

    /**
     * gerarOTPedido - Gerar um Pedido a partir de um Ordem de Transferência
     *
     * @param $T224_Id 
     */
    public function gerarOTPedido($T224_Id, $estoqueDisponivel = true) {
        global $g, $T005_Id;
        $arrayRetorno = array();

        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();
        require_once('bibliotecas/classes/VEN001.php');
        $VEN001 = new VEN001();

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        // Encontra o C004 para o D024 da OC
        $sql = <<<EOT
            SELECT
                *
            FROM T224
            LEFT JOIN C004 ON T224_D024_Id=C004_D024_Id
            LEFT JOIN D024 ON T224_D024_Id=D024_Id
            WHERE T224_Id = '{$T224_Id}'
EOT;
        $resT224 = mysql_query($sql);
        if ($erro = mysql_error()) {
            mysql_query("ROLLBACK");
            return 'Erro: ' . $erro;
        }
        $rowT224 = mysql_fetch_assoc($resT224);
        if (!$rowT224['C004_Id']) {
            return "O cliente selecionado não é um cliente do grupo: {$rowT224['D024_Nome_Empresa']}";
        }

        // Verificações
        if (empty($g['C004']['C004_D024_Id'])) {
            return "Não existe cliente/fornecedor associado a emrpesa atual. Por favor, verifique no cadastro da empresa.";
        }

        if ($estoqueDisponivel) {
            $status = 1;
            $sqlStatusInicialPedido = "SELECT C031_Valor
                                         FROM C031
                                        WHERE C031_Campo = 'statusInicialPedido'
                                          AND C031_C004_Id = '{$rowT224['C004_Id']}'
                                        LIMIT 1";
            $resStatusInicialPedido = mysql_query($sqlStatusInicialPedido);
            if ($erro = mysql_error()) {
                mysql_query("ROLLBACK");
                return 'Erro ao consultar o status inicial do pedido na empresa de destino: ' . $erro;
            }
            $rowStatusInicialPedido = mysql_fetch_assoc($resStatusInicialPedido);
            if (isset($rowStatusInicialPedido['C031_Valor']) && $rowStatusInicialPedido['C031_Valor'] !== '') {
                $status = $rowStatusInicialPedido['C031_Valor'];
            }
        } else {
            $status = 0;
        }

        //Faz o insert de acordo com a global, um pedido por item ou um pedido para todos os itens, "N" um pedido todos os itens
        if($g['C031']['gerarPedidoPorItemOT'] == 'N'){
            // retorna o T005_Id
            $retorno = $this->insereCabecalhoPedido($rowT224, $status);
            $T005_Id = $retorno;
            $arrayRetorno['T005_Id'] = $T005_Id;
        }
        
        // Gera o itens do pedido
        $sql = <<<EOT
            SELECT 
               *
            FROM
                T225
            LEFT JOIN D009 ON D009_Id = T225_D009_Id
            LEFT JOIN D049 ON D009_D049_Id = D049_Id
            LEFT JOIN D001 ON D049_D001_Id = D001_Id
            WHERE 
                T225_T224_Id = '{$T224_Id}' AND T225_Flag_Cancelada != 'S'
EOT;
        $resT225 = mysql_query($sql);
        if ($erro = mysql_error()) {
            mysql_query("ROLLBACK");
            return 'Erro: ' . $erro;
        }
        $item = 1;
        while($rowT225 = mysql_fetch_assoc($resT225)) {

            // Verificações
            $sql = <<<EOT
                SELECT 
                    D009_Id,
                    D009_Quantidade_Estoque_Liquido,
                    D001_Codigo_Produto,
                    T001_Id
                FROM
                    D049
                LEFT JOIN D009 ON D049_Id = D009_D049_Id
                LEFT JOIN D001 ON D001_Id = D049_D001_Id
                LEFT JOIN T001 ON T001_Id = D009_T001_Id
                WHERE 
                    D049_Id = '{$rowT225['D049_Id']}'
                    AND D009_C004_Id = '{$rowT224['C004_Id']}'
EOT;
            $resD009Distribuidora = mysql_query($sql);
            if ($erro = mysql_error()) {
                mysql_query("ROLLBACK");
                return 'Erro: ' . $erro;
            }
            if (mysql_numrows($resD009Distribuidora) <= 0) {
                mysql_query("ROLLBACK");
                return "O produto {$rowT225['D001_Codigo_Produto']} não foi encontrado na empresa {$rowT224['C004_Nome_Abreviado']}. Por favor, contate o suporte.";
            }
            $rowD009Distribuidora = mysql_fetch_assoc($resD009Distribuidora);

/*            if ($rowD009Distribuidora['D009_Quantidade_Estoque_Liquido'] < $rowT225['T225_Quantidade'] &&) {
                $rowT225['T225_Quantidade'] = gCorrigeNumero($rowT225['T225_Quantidade']);
                $rowD009Distribuidora['D009_Quantidade_Estoque_Liquido'] = gCorrigeNumero($rowD009Distribuidora['D009_Quantidade_Estoque_Liquido']);
                mysql_query("ROLLBACK");
                return "O produto {$rowD009Distribuidora['D001_Codigo_Produto']} não possui estoque suficiente na empresa {$rowT224['C004_Nome_Abreviado']}.<br/>Estoque disponível: {$rowD009Distribuidora['D009_Quantidade_Estoque_Liquido']}<br/>Estoque requerido: {$rowT225['T225_Quantidade']}";
            }*/

            if ($rowD009Distribuidora['D009_Quantidade_Estoque_Liquido'] < $rowT225['T225_Quantidade']) {
                if ($estoqueDisponivel) {
                    continue;
                }
            } else {
                if (!$estoqueDisponivel) {
                    continue;
                }
            }

            //Faz o insert de acordo com a global, um pedido por item ou um pedido para todos os itens, "S" um pedido por item
            if($g['C031']['gerarPedidoPorItemOT'] == 'S'){
                // retorna o T005_Id
                $retorno = $this->insereCabecalhoPedido($rowT224, $status);
                $T005_Id = $retorno;
                $arrayRetorno['T005_Id'] = $T005_Id;
            }            

            $T006_D006_Id      = $VEN001->retornaCFOP($g['C004']['C004_D024_Id'], $rowD009Distribuidora['D009_Id'], $rowT224['C004_Id'], 2);
            $Aliquotas         = $VEN001->retornaAliquotas($rowD009Distribuidora['D009_Id'], false, $T005_Id, false, false, false, false, $T006_D006_Id, false);
            
            $dadosPrecoVenda   = $CAD002->retornaPrecoVenda($rowD009Distribuidora['D009_Id'], 'T', $g['C004']['C004_D024_Id'], false, $T006_D006_Id, $Aliquotas['Aliquota_ICMS'], false, false, 4, $Aliquotas['Aliquota_PIS'], $Aliquotas['Aliquota_COFINS'], $Aliquotas['Aliquota_IPI'], $Aliquotas['ST_VA'], $Aliquotas['Percentual_Reducao_ICMS_ST'], $Aliquotas['ST_ICMS_Interno'], $Aliquotas['Percentual_Reducao_ICMS'], $Aliquotas['ICMS_Inter_Estadual'], $Aliquotas['Aliquota_IRPJ'], $Aliquotas['Aliquota_CSLL']);
            $Preco_Tabelado    = gCorrigeNumeroInverte($dadosPrecoVenda[0]);
            $demonstrativo     = serialize($dadosPrecoVenda[1]);
            
            $demonstrativoT001 = $rowD009Distribuidora['T001_Id'];

            // Cria o item do Pedido
            $insertT006 = array(
                'T006_Item' => $item,
                'T006_D037_Id' => $rowT225['T225_D037_Id'],
                'T006_D005_Id' => $rowT225['T225_D005_Id'],
                'T006_D009_Id' => $rowD009Distribuidora['D009_Id'],
                'T006_Quantidade_Estoque' => $rowD009Distribuidora['D009_Quantidade_Estoque_Liquido'],
                'T006_Codigo_Produto' => $rowT225['T225_Codigo_Produto'],
                'T006_Descricao_Produto' => $rowT225['T225_Descricao_Produto'],
                'T006_Quantidade' => $rowT225['T225_Quantidade'],
                'T006_Valor_Preco_Sem_Desconto_Unitario' => $Preco_Tabelado,
                'T006_Valor_Preco_Compra' => $Preco_Tabelado,
                'T006_T005_Id' => $T005_Id,
                'T006_C004_Id' => $rowT224['C004_Id'],
                'T006_Flag_Origem_Produto' => $rowT225['T225_Flag_Origem_Produto'],
                'T006_Demonstrativo_Preco_Venda' => $demonstrativo,
                'T006_T001_Id' => $demonstrativoT001,
                'T006_Flag_Destino_Produto' => 2,
                'T006_Valor_Preco_Original' => $Preco_Tabelado,
                'T006_Numero_Pedido_Compra' => $T224_Id,
                'T006_Item_Pedido_Compra' => $rowT225['T225_Item']
            );
            $sqlChaves = '';
            $sqlDados = '';
            foreach ($insertT006 as $key => $value) {
                $sqlChaves .= $sqlChaves ? ',' : '';
                $sqlChaves .= $key;
                $sqlDados .= $sqlDados ? ',' : '';
                $sqlDados .= "'" . mysql_real_escape_string($value) . "'";
            }
            $sql = "INSERT INTO T006 ( {$sqlChaves} ) VALUES ( {$sqlDados} )";
            mysql_query($sql);
            if ($erro = mysql_error()) {
                mysql_query("ROLLBACK");
                return 'Erro: ' . $erro;
            }
            $T006_Id = $g['mysqlLastId'];
            $item++;
            mysql_query("UPDATE T225 SET T225_Valor_Preco_Unitario = '{$Preco_Tabelado}' WHERE T225_Id = '{$rowT225['T225_Id']}'");
        }

        if ($item<=1) {
            mysql_query("ROLLBACK");
            if ($estoqueDisponivel) {
                return 'Erro: Não existem itens com estoque disponível.' ;
            } else {
                return 'Erro: Não existem itens com estoque indisponível.' ;
            }
        }

        // Executar metódo CFOP/aliquotas no pedido
        // esses métodos agora serão executados em etapas no form - Lígia Nakazato 28/08/2018
        // require_once("bibliotecas/classes/VEN002.php");
        // $VEN002 = new VEN002();
        // $VEN002->ven002atualizarCFOP($T005_Id);
        // $VEN002->atualizarAliquotasPedidos($T005_Id);

        // //
        // $this->est016atualizarCFOP($T224_Id);

        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");

        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();
        $sqlT006 = mysql_query("SELECT T006_D009_Id FROM T006 WHERE T006_T005_Id = '{$T005_Id}'");
        while($rowT006 = mysql_fetch_array($sqlT006)){
            // Processa estoque por locação
            $CAD002->processaEstoqueLiquidoPorLocacao($rowT006['T006_D009_Id']);
        }
        //return true;
        return $arrayRetorno;
    }

     /**
     * solicitarColeta
     *
     * @param int $T224_Id
     */
    public function solicitarColeta($T224_Id)
    {
        global $g;
        mysql_query("UPDATE T224 SET T224_Flag_Status = '5', T224_Nome_Status = T224_Status_Pedido(T224_Flag_Status, T224_Id) WHERE T224_Id = '{$T224_Id}'");
        $erro = mysql_error();
        if(!empty($erro)){
            return "Erro: " .$erro;
        }
        return true;
    }

     /**
     * aplicarAlcadaOC
     *
     * @param int $T224_Id
     */
    public function aplicarAlcadaOC($T224_Id)
    {
        global $g;
        
        if($g['C031']['controleAlcadasOrdemCompra'] == 'N'){
            // Não utiliza controle de alçada, finaliza a função
            return true;
        }

        $sqlT224 = mysql_query("SELECT * 
                                  FROM T224 
                             LEFT JOIN C007 ON T224_C007_Id_Vendedor_Interno = C007_Id 
                                 WHERE T224_Id = '{$T224_Id}'");
        $T224    = mysql_fetch_array($sqlT224);

        $dataInicial        = date("Y-m-1 00:00:00");
        $dataFinal          = date("Y-m-t 23:59:59");
        $alcada             = 'D';
        // C007_Alcada_OC_Empresa_Grupo, E = empresa atual | G = grupo (todas as empresas)
        $validaEmpresaGrupo = ($T224['C007_Alcada_OC_Empresa_Grupo'] == 'E') ? "AND T224_C004_Id = '{$g['empresaAtual']}'" : "";
        
        if($T224['C007_Flag_Vendedor'] != 'D'){

            // monta tabela de mensagens de alçada
            $msg = <<<EOT
            <style>
                .xtdTitulo {
                    border:1px solid #000000;
                    font-size:12px;
                    background:#4F5F6E;
                    color:black;
                }  
                .xtdValores {
                    border:1px solid #000000;
                    font-size:11px;
                    background:#000000;
                    color:dark;
                }
            </style>

            <tr>
                <td colspan="1" style="padding:0px 0px 0px 0px">
                    <table cellspacing="1" cellpadding="4px" width="100%" height="100%" style="border:0px;" class="xtdTitulo">
                        <td align="center" width="100px" style="background:#C9DFEA">
                            Alçada
                        </td>
                        <td align="center" width="100px" style="background:#C9DFEA">
                            Limite / Comprado
                        </td>
                        <td align="center" width="100px" style="background:#C9DFEA">
                            Total mês
                        </td>
                        <td align="center" width="100px" style="background:#C9DFEA">
                            Total fornecedor mês
                        </td>
                        <td align="center" width="100px" style="background:#C9DFEA">
                            Total dia
                        </td>
                        <td align="center" width="100px" style="background:#C9DFEA">
                            Total OC
                        </td>
                    </table>
                </td>
            </tr>   
EOT; 
            
            if($T224['C007_Flag_Vendedor'] == 'C' || $T224['C007_Flag_Vendedor'] == 'V'){

                // Verifica o total aprovado no mês pelo comprador                
                $sqlTotalCompradorMes = mysql_query("SELECT IFNULL(SUM(T224_Valor_Total),0) as TotalComprador 
                                                       FROM T224
                                                      WHERE T224_Flag_Status_Liberacao = '2' 
                                                        AND T224_C007_Id_Vendedor_Interno = '{$T224['T224_C007_Id_Vendedor_Interno']}' 
                                                        AND T224_Liberou_Alcada_Data >= '{$dataInicial}' 
                                                        AND T224_Liberou_Alcada_Data <= '{$dataFinal}'
                                                            {$validaEmpresaGrupo}");
                $row1 = mysql_fetch_array($sqlTotalCompradorMes);
                
                 // Verifica o total aprovado no mês para o forncedor pelo comprador  
                $sqlTotalFornecedorMes = mysql_query("SELECT IFNULL(SUM(T224_Valor_Total),0) as TotalFornecedorMes 
                                                        FROM T224 
                                                       WHERE T224_Flag_Status_Liberacao = '2' 
                                                         AND T224_C007_Id_Vendedor_Interno = '{$T224['T224_C007_Id_Vendedor_Interno']}' 
                                                         AND T224_D024_Id='{$T224['T224_D024_Id']}' 
                                                         AND T224_Liberou_Alcada_Data >= '{$dataInicial}' 
                                                         AND T224_Liberou_Alcada_Data <= '{$dataFinal}'
                                                             {$validaEmpresaGrupo}");
                $row2  = mysql_fetch_array($sqlTotalFornecedorMes);
                
                // Verifica o total aprovado no dia pelo comprador  
                $sqlTotalCompradorDia = mysql_query("SELECT IFNULL(SUM(T224_Valor_Total),0) as TotalCompradorDia
                                                       FROM T224
                                                      WHERE T224_Flag_Status_Liberacao = '2' 
                                                        AND T224_C007_Id_Vendedor_Interno = '{$T224['T224_C007_Id_Vendedor_Interno']}' 
                                                        AND T224_Liberou_Alcada_Data LIKE concat(curdate(),'%')
                                                            {$validaEmpresaGrupo}");
                $row3 = mysql_fetch_array($sqlTotalCompradorDia);

                //alçadas comprador
                $C007_Valor_Maximo_OC_Mes        = gCorrigeNumero($T224['C007_Valor_Maximo_OC_Mes']);
                $C007_Valor_Maximo_OC_Fornecedor = gCorrigeNumero($T224['C007_Valor_Maximo_OC_Fornecedor']);
                $C007_Valor_Maximo_OC_Dia        = gCorrigeNumero($T224['C007_Valor_Maximo_OC_Dia']);
                $C007_Valor_Maximo_OC            = gCorrigeNumero($T224['C007_Valor_Maximo_OC']);

                // soma os valores das OCs aprovadas + a OC que está sendo analisada
                $row1['TotalComprador']     += $T224['T224_Valor_Total']; 
                $row2['TotalFornecedorMes'] += $T224['T224_Valor_Total']; 
                $row3['TotalCompradorDia']  += $T224['T224_Valor_Total'];

                // Total OCs aprovadas comprador, retorna em vermelho se não tiver alçada, retorna em verde se tiver alçada
                $T224_Valor_Total   = ($T224['T224_Valor_Total']   > $T224['C007_Valor_Maximo_OC'])            ? '<span style="color: red;">' . gCorrigeNumero($T224['T224_Valor_Total'])   . '</span>' : '<span style="color: darkgreen ;">' . gCorrigeNumero($T224['T224_Valor_Total'])  . '</span>';
                $TotalComprador     = ($row1['TotalComprador']     > $T224['C007_Valor_Maximo_OC_Mes'])        ? '<span style="color: red;">' . gCorrigeNumero($row1['TotalComprador'])     . '</span>' : '<span style="color: darkgreen ;">' . gCorrigeNumero($row1['TotalComprador'] )   . '</span>';
                $TotalFornecedorMes = ($row2['TotalFornecedorMes'] > $T224['C007_Valor_Maximo_OC_Fornecedor']) ? '<span style="color: red;">' . gCorrigeNumero($row2['TotalFornecedorMes']) . '</span>' : '<span style="color: darkgreen ;">' . gCorrigeNumero($row2['TotalFornecedorMes']). '</span>';
                $TotalCompradorDia  = ($row3['TotalCompradorDia']  > $T224['C007_Valor_Maximo_OC_Dia'])        ? '<span style="color: red;">' . gCorrigeNumero($row3['TotalCompradorDia'])  . '</span>' : '<span style="color: darkgreen ;">' . gCorrigeNumero($row3['TotalCompradorDia']) . '</span>';

                $msg .= <<<EOT
                <tr>
                    <td colspan="1" >
                        <table cellspacing="1" cellpadding="4px" width="100%" height="40px" style="border:0px" class="xtdValores">
                            <tr>
                                <td align="center" width="100px" style="background:#FFFFFF">
                                    Comprador
                                </td>
                                <td align="center" width="100px" style="background:#FFFFFF">
                                    Limite
                                </td>
                                <td align="center" width="100px" style="background:#FFFFFF">
                                    <b>{$C007_Valor_Maximo_OC_Mes}</b>
                                </td>
                                <td align="center" width="100px" style="background:#FFFFFF">
                                    <b>{$C007_Valor_Maximo_OC_Fornecedor}</b>
                                </td>
                                <td align="center" width="100px" style="background:#FFFFFF">
                                    <b>{$C007_Valor_Maximo_OC_Dia}</b>
                                </td>
                                <td align="center" width="100px" style="background:#FFFFFF">
                                    <b>{$C007_Valor_Maximo_OC}</b>
                                </td>
                            </tr>             
                            <tr>
                                <td align="center" width="100px" style="background:#FFFFFF">
                                    <b>{$T224['C007_Primeiro_Nome']}</b>
                                </td>
                                <td align="center" width="100px" style="background:#FFFFFF" >
                                    Comprado
                                </td>
                                <td align="center" width="100px" style="background:#FFFFFF" >
                                    <b>{$TotalComprador}</b>
                                </td>
                                <td align="center" width="100px" style="background:#FFFFFF" >
                                    <b>{$TotalFornecedorMes}</b>
                                </td>
                                <td align="center" width="100px" style="background:#FFFFFF" >
                                    <b>{$TotalCompradorDia}</b>
                                </td>
                                <td align="center" width="100px" style="background:#FFFFFF" >
                                    <b>{$T224_Valor_Total}</b>
                                </td>                            
                            </tr>   
                        </table>
                    </td>
                </tr>
EOT;                                
            
                if($T224['T224_Valor_Total'] <= $T224['C007_Valor_Maximo_OC'] && $row1['TotalComprador'] <= $T224['C007_Valor_Maximo_OC_Mes'] && $row2['TotalFornecedorMes'] <= $T224['C007_Valor_Maximo_OC_Fornecedor'] && $row3['TotalCompradorDia'] <= $T224['C007_Valor_Maximo_OC_Dia']){
                    // Se comprador tiver alçada
                    $alcada = 'C';  
                } else {
                    //Caso o comprador não tenha alçada suficiente, verificamos a alçada do seu supervisor
                    $sqlC007 = mysql_query("SELECT * FROM C007 WHERE C007_Id = '{$T224['C007_C007_Id']}'");
                    
                    $C007 = mysql_fetch_array($sqlC007);
                    // C007_Alcada_OC_Empresa_Grupo, E = empresa atual | G = grupo (todas as empresas)
                    $validaEmpresaGrupo = ($C007['C007_Alcada_OC_Empresa_Grupo'] == 'E') ? "AND T224_C004_Id = '{$g['empresaAtual']}'" : "";

                    $T224['C007_Valor_Maximo_OC']            = $C007['C007_Valor_Maximo_OC'];
                    $T224['C007_Valor_Maximo_OC_Mes']        = $C007['C007_Valor_Maximo_OC_Mes'];
                    $T224['C007_Valor_Maximo_OC_Fornecedor'] = $C007['C007_Valor_Maximo_OC_Fornecedor'];
                    $T224['C007_Valor_Maximo_OC_Dia']        = $C007['C007_Valor_Maximo_OC_Dia'];
                    $T224['C007_Id']                         = $C007['C007_Id'];
                }
            }
            
            //Caso o usuario seja um supervisor ou o comprador tenha um supervisor
            if($alcada == 'D' && $T224['C007_Id'] > 0){
                
                // Verifica o total aprovado no mês pelo supervisor
                $sqlTotalSupervisorMes = mysql_query("SELECT IFNULL(SUM(T224_Valor_Total),0) as TotalSupervisorMes 
                                                        FROM T224 
                                                   LEFT JOIN C007 ON T224_C007_Id_Vendedor_Interno=C007_Id
                                                       WHERE T224_Flag_Status_Liberacao = '2' 
                                                         AND (T224_C007_Id_Vendedor_Interno = '{$T224['C007_Id']}' OR T224_Liberou_Alcada_C007_Id = '{$T224['C007_Id']}') 
                                                         AND T224_Liberou_Alcada_Data >= '{$dataInicial}' 
                                                         AND T224_Liberou_Alcada_Data <= '{$dataFinal}'
                                                             {$validaEmpresaGrupo}");
                $row1 = mysql_fetch_array($sqlTotalSupervisorMes);
                
                // Verifica o total aprovado no mês para o forncedor pelo supervisor
                $sqlTotalFornecedorSupervisorMes = mysql_query("SELECT IFNULL(SUM(T224_Valor_Total),0) as TotalFornecedorSupervisorMes 
                                                                  FROM T224 LEFT JOIN C007 ON T224_C007_Id_Vendedor_Interno=C007_Id 
                                                                 WHERE T224_Flag_Status_Liberacao = '2' 
                                                                   AND (T224_C007_Id_Vendedor_Interno = '{$T224['C007_Id']}' OR T224_Liberou_Alcada_C007_Id = '{$T224['C007_Id']}') 
                                                                   AND T224_D024_Id='{$T224['T224_D024_Id']}' 
                                                                   AND T224_Liberou_Alcada_Data >= '{$dataInicial}' 
                                                                   AND T224_Liberou_Alcada_Data <= '{$dataFinal}'
                                                                       {$validaEmpresaGrupo}");
                $row2  = mysql_fetch_array($sqlTotalFornecedorSupervisorMes);
                
                // Verifica o total aprovado no dia pelo supervisor
                $sqlTotalSupervisorDia = mysql_query("SELECT IFNULL(SUM(T224_Valor_Total),0) as TotalSupervisorDia
                                                        FROM T224 
                                                   LEFT JOIN C007 ON T224_C007_Id_Vendedor_Interno=C007_Id 
                                                       WHERE T224_Flag_Status_Liberacao = '2' 
                                                         AND T224_Liberou_Alcada_Data LIKE concat(curdate(),'%') 
                                                         AND (T224_C007_Id_Vendedor_Interno = '{$T224['C007_Id']}' OR T224_Liberou_Alcada_C007_Id = '{$T224['C007_Id']}')
                                                             {$validaEmpresaGrupo}");
                $row3 = mysql_fetch_array($sqlTotalSupervisorDia);

                //alçadas supervisor
                $C007_Valor_Maximo_OC_Mes        = gCorrigeNumero($T224['C007_Valor_Maximo_OC_Mes']);
                $C007_Valor_Maximo_OC_Fornecedor = gCorrigeNumero($T224['C007_Valor_Maximo_OC_Fornecedor']);
                $C007_Valor_Maximo_OC_Dia        = gCorrigeNumero($T224['C007_Valor_Maximo_OC_Dia']);
                $C007_Valor_Maximo_OC            = gCorrigeNumero($T224['C007_Valor_Maximo_OC']);

                // soma os valores das OCs aprovadas + a OC que está sendo analisada
                $row1['TotalSupervisorMes']           += $T224['T224_Valor_Total']; 
                $row2['TotalFornecedorSupervisorMes'] += $T224['T224_Valor_Total']; 
                $row3['TotalSupervisorDia']           += $T224['T224_Valor_Total'];

                // Total OCs aprovadas supervisor, retorna em vermelho se não tiver alçada, retorna em verde se tiver alçada
                $T224_Valor_Total             = ($T224['T224_Valor_Total']             > $T224['C007_Valor_Maximo_OC'])            ? '<span style="color: red;">' . gCorrigeNumero($T224['T224_Valor_Total'])             . '</span>' : '<span style="color: darkgreen;">'  . gCorrigeNumero($T224['T224_Valor_Total'])            . '</span>';
                $TotalSupervisorMes           = ($row1['TotalSupervisorMes']           > $T224['C007_Valor_Maximo_OC_Mes'])        ? '<span style="color: red;">' . gCorrigeNumero($row1['TotalSupervisorMes'])           . '</span>' : '<span style="color: darkgreen ;">' . gCorrigeNumero($row1['TotalSupervisorMes'] )         . '</span>';
                $TotalFornecedorSupervisorMes = ($row2['TotalFornecedorSupervisorMes'] > $T224['C007_Valor_Maximo_OC_Fornecedor']) ? '<span style="color: red;">' . gCorrigeNumero($row2['TotalFornecedorSupervisorMes']) . '</span>' : '<span style="color: darkgreen ;">' . gCorrigeNumero($row2['TotalFornecedorSupervisorMes']). '</span>';
                $TotalSupervisorDia           = ($row3['TotalSupervisorDia']           > $T224['C007_Valor_Maximo_OC_Dia'])        ? '<span style="color: red;">' . gCorrigeNumero($row3['TotalSupervisorDia'])           . '</span>' : '<span style="color: darkgreen ;">' . gCorrigeNumero($row3['TotalSupervisorDia'])          . '</span>';

                if($g['nivelAlcada'] > 1){
                    // monta a tabela de alçadas supervisor
                    $msg .= <<<EOT
                        <tr>
                            <td colspan="1" >
                                <table cellspacing="1" cellpadding="4px" width="100%" height="40px" style="border:0px" class="xtdValores">
                                    <tr>
                                        <td align="center" width="100px" style="background:#FFFFFF" >
                                            Supervisor
                                        </td>
                                        <td align="center" width="100px" style="background:#FFFFFF">
                                            Limite
                                        </td>
                                        <td align="center" width="100px" style="background:#FFFFFF">
                                            <b>{$C007_Valor_Maximo_OC_Mes}</b>
                                        </td>
                                        <td align="center" width="100px" style="background:#FFFFFF">
                                            <b>{$C007_Valor_Maximo_OC_Fornecedor}</b>
                                        </td>
                                        <td align="center" width="100px" style="background:#FFFFFF">
                                            <b>{$C007_Valor_Maximo_OC_Dia}</b>
                                        </td>
                                        <td align="center" width="100px" style="background:#FFFFFF">
                                            <b>{$C007_Valor_Maximo_OC}</b>
                                        </td>
                                    </tr>             
                                    <tr>
                                        <td align="center" width="100px" style="background:#FFFFFF">
                                            <b>{$C007['C007_Primeiro_Nome']}</b>
                                        </td>
                                        <td align="center" width="100px" style="background:#FFFFFF" >
                                            Comprado
                                        </td>
                                        <td align="center" width="100px" style="background:#FFFFFF" >
                                            <b>{$TotalSupervisorMes}</b>
                                        </td>
                                        <td align="center" width="100px style="background:#FFFFFF" >
                                            <b>{$TotalFornecedorSupervisorMes}</b>
                                        </td>
                                        <td align="center" width="100px" style="background:#FFFFFF" >
                                            <b>{$TotalSupervisorDia}</b>
                                        </td>
                                        <td align="center" width="100px" style="background:#FFFFFF" >
                                            <b>{$T224_Valor_Total}</b> 
                                        </td>                            
                                    </tr>   
                                </table>
                            </td>
                        </tr>
EOT;    
                }

                if($T224['T224_Valor_Total'] <= $T224['C007_Valor_Maximo_OC'] && $row1['TotalSupervisorMes'] <= $T224['C007_Valor_Maximo_OC_Mes'] && $row2['TotalFornecedorSupervisorMes'] <= $T224['C007_Valor_Maximo_OC_Fornecedor'] && $row3['TotalSupervisorDia'] <= $T224['C007_Valor_Maximo_OC_Dia']){
                    // Se supervisor tiver alçada
                    $alcada = 'S';
                }
            }
        }

        /*
        NÃO VAMOS MOSTRAR OS TOTAIS GERAL PARA O DIRETOR, APENAS O TOTAL DO COMPRADOR E SUPERVISOR, FELIPE 07/02/22
        if(($alcada == 'D' && $g['nivelAlcada'] == 4)) {

            $sqlC007 = mysql_query("SELECT C007_Alcada_OC_Empresa_Grupo FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}'");
            $mC007   = mysql_fetch_array($sqlC007);

            // C007_Alcada_OC_Empresa_Grupo, E = empresa atual | G = grupo (todas as empresas)
            $validaEmpresaGrupo = ($mC007['C007_Alcada_OC_Empresa_Grupo'] == 'E') ? "AND T224_C004_Id = '{$g['empresaAtual']}'" : "";

            $sqlTotalMes = mysql_query("SELECT IFNULL(SUM(T224_Valor_Total),0) as Total 
                                          FROM T224 
                                         WHERE T224_Flag_Status_Liberacao = '2' 
                                           AND T224_Liberou_Alcada_Data >= '{$dataInicial}'
                                           AND T224_Liberou_Alcada_Data <= '{$dataFinal}'
                                               {$validaEmpresaGrupo}");
            $row1 = mysql_fetch_array($sqlTotalMes);
            
            $sqlTotalFornecedorMes = mysql_query("SELECT IFNULL(SUM(T224_Valor_Total),0) as TotalFornecedorMes 
                                                    FROM T224
                                                   WHERE T224_Flag_Status_Liberacao = '2' 
                                                     AND T224_D024_Id='{$T224['T224_D024_Id']}' 
                                                     AND T224_Liberou_Alcada_Data >= '{$dataInicial}' 
                                                     AND T224_Liberou_Alcada_Data <= '{$dataFinal}'
                                                         {$validaEmpresaGrupo}");
            $row2  = mysql_fetch_array($sqlTotalFornecedorMes);
            
            $sqlTotalDia = mysql_query("SELECT IFNULL(SUM(T224_Valor_Total),0) as TotalDia 
                                          FROM T224 
                                         WHERE T224_Flag_Status_Liberacao = '2' 
                                           AND T224_Liberou_Alcada_Data LIKE concat(curdate(),'%')
                                               {$validaEmpresaGrupo}");
            $row3 = mysql_fetch_array($sqlTotalDia);


            $T224_Valor_Total   = gCorrigeNumero($T224['T224_Valor_Total']);
            $Total              = gCorrigeNumero($row1['Total'] + $T224['T224_Valor_Total']);
            $TotalFornecedorMes = gCorrigeNumero($row2['TotalFornecedorMes'] + $T224['T224_Valor_Total']);
            $TotalDia           = gCorrigeNumero($row3['TotalDia'] + $T224['T224_Valor_Total']);

            $msg .= <<<EOT
            <tr>
                <td colspan="1" >
                    <table cellspacing="1" cellpadding="4px" width="100%" height="20px" style="border:0px" class="xtdTitulo">
                            <td align="center" width="100px" style="background:#C9DFEA" >
                                Total geral
                            </td>
                            <td align="center" width="100px" style="background:#C9DFEA">
                                Comprado + OC {$T224['T224_Id']}
                            </td>
                            <td align="center" width="100px" style="background:#C9DFEA">
                                <b>{$Total}</b>
                            </td>
                            <td align="center" width="100px" style="background:#C9DFEA">
                                <b>{$TotalFornecedorMes}</b>
                            </td>
                            <td align="center" width="100px" style="background:#C9DFEA">
                                <b>{$TotalDia}</b>
                            </td>
                            <td align="center" width="100px" style="background:#C9DFEA">
                                <b>{$T224_Valor_Total}</b>
                            </td>
                    </table>
                </td>
            </tr>
EOT;                
        }*/

        // Update na alçada e msg
        mysqli_query("UPDATE T224 SET T224_Flag_Alcada             = '{$alcada}', 
                                      T224_Motivo_Pedido_Liberacao = '{$msg}' 
                                WHERE T224_Id = '{$T224_Id}'");

        return true;
    }

    /**
     * verificaDadosOrdemCompra
     *
     * @param String $T224_Id
     * @return String/Bool
     */ 
    public function verificaDadosOrdemCompra($T224_Id)
    {
        global $g;
        $retorno = '';
        $origensValidas = array(0,1,2,3,4,5,6,7,8);

        $sql = mysql_query("SELECT T225_Codigo_Produto, IFNULL(T225_D006_Id,0) AS T225_D006_Id, IFNULL(T225_D005_Id,0) AS T225_D005_Id, T225_Flag_Origem_Produto FROM T225 WHERE T225_T224_Id = '{$T224_Id}' AND IFNULL(T225_Flag_Cancelada,'') != 'S'");
        while($row = mysql_fetch_array($sql)){
            $temp = '';
            if($row['T225_D006_Id'] <= 0){
                $temp .= "<b>".$row['T225_Codigo_Produto'] . "</b> está com a CFOP em branco";
            }
            if($row['T225_D005_Id'] <= 0){
                $temp .= ($temp == '') ? "<b>".$row['T225_Codigo_Produto'] . "</b> está com a NCM em branco" : ", com a NCM em branco";
            }
            if(!is_numeric($row['T225_Flag_Origem_Produto']) || $row['T225_Flag_Origem_Produto'] == '' || !in_array($row['T225_Flag_Origem_Produto'], $origensValidas)){
                $temp .= ($temp == '') ? "<b>".$row['T225_Codigo_Produto'] . "</b> a origem da mercadoria não é valida " : ", e a origem da mercadoria não é valida ";
                $temp .= "(origens válidas ".implode(", ", $origensValidas).")";
            }
            $temp .= ($temp != '') ? '.<br>' : '';
            if($temp != ''){
                $retorno .= $temp;
            }
        }

        if($retorno != ''){
            return "Verifique os itens abaixo: <br><br>" . $retorno;
        }
        return true;
    }

    /**
     * cancelarProduto
     *
     * @param String $T225_Id
     * @return String/Bool
     */
    public function cancelarProduto($T225_Id)
    {
        $sqlT224  =  mysql_query("SELECT T225_T224_Id, T225_D009_Id FROM T225 WHERE T225_Id = '{$T225_Id}'");
        $rowT224 = mysql_fetch_array($sqlT224);

        $sql = mysql_query("SELECT T006_Id FROM T006 WHERE T006_T225_Id='{$T225_Id}'");
        while ($T006 = mysql_fetch_array($sql)) {
            mysql_query("UPDATE T006 SET T006_T225_Id = 0 WHERE T006_Id='{$T006['T006_Id']}'");
            if (mysql_error()) {
                return 'Erro ao desvincular o item da ordem de compra com o pedido. Por favor, contate o suporte técnico';
            }
        }

        mysql_query("UPDATE T225 SET T225_Flag_Cancelada = 'S', T225_T219_Id = 0 WHERE T225_Id = '{$T225_Id}'");
        if (mysql_error()) {
            return 'Erro ao cancelar o item da ordem de compra. Por favor, contate o suporte técnico: ' . mysql_error();
        }

        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();
        $CAD002->reprocessaOCPedidos($rowT224['T225_D009_Id']);

        mysql_query("call T224_Gravar_Totalizacao_3({$rowT224['T225_T224_Id']});");
        if (mysql_error()) {
            return 'Erro no T224_Gravar_Totalização: ' . mysql_error();
        }

        mysql_query("UPDATE T224 SET T224_Nome_Status = T224_Status_Pedido(T224_Flag_Status, T224_Id) WHERE T224_Id = '{$rowT224['T225_T224_Id']}'");
        if (mysql_error()) {
            return 'Erro na atualização do status da OC: ' . mysql_error();
        }

        return true;
    }

    /**
     * dividirOrdemCompra
     *
     * @param String $T224_Id_Antigo
     * @param String $C004_Id_Novo
     * @param String $sql
     * @return String/Bool
     */
    public function dividirOrdemCompra($T224_Id_Antigo, $C004_Id_Novo, $sqlGrid)
    {
        log("entra no dividirOrdemCompra");
        global $g, $novoT224_Id;
        require_once('bibliotecas/classes/EST015.php');
        $EST015 = new EST015();
        $arrayRetorno = array();

        log("T224_Id= ".$T224_Id_Antigo);
        log("C004_Id_Novo= ".$C004_Id_Novo);
        log("sqlGrid= ".$sqlGrid);

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        $sqlGrid = str_replace('LIMIT 0,100', '', $sqlGrid);
        if (strpos($sqlGrid, 'ORDER BY') !== false) {
            $sqlExtra = explode('ORDER BY', $sqlGrid);
            $sqlGrid = $sqlExtra[0] . ' AND T225_Quantidade_Dividir > 0';
            $sqlGrid .= ' ORDER BY '.$sqlExtra[1];
        } else {
            $sqlGrid = $sqlGrid . ' AND T225_Quantidade_Dividir > 0';
        }
        $sqlT225 = mysql_query($sqlGrid);
        $T225           = mysql_fetch_array($sqlT225);
        $T224_Id_Antigo = $T225['T224_Id'];

        // Seleciona o cabeçalho da OC para inserir um cópia dele com o novo C004
        $sqlT224    = "SELECT * FROM T224 WHERE T224_Id='{$T224_Id_Antigo}'";
        $resultT224 = mysql_query($sqlT224);
        $linhaT224  = mysql_fetch_array($resultT224);

        $T224  = mysql_query("SELECT * FROM T224 WHERE T224_Id='{$T224_Id_Antigo}'");
        $mT224 = mysql_fetch_assoc($T224);

        // Buscando a CFOP para o cabeçalho da OC
        $D006_Id = $EST015->retornaCFOP($linhaT224['T224_D024_Id'], false, $C004_Id_Novo);

        $T224_Id_Antigo = $mT224['T224_Id'];
        $campos         = "";
        $valores        = "";
        foreach ($mT224 as $key => $value) {
            if ($key == 'T224_Id') {
                continue;
            } elseif ($key == "T224_D006_Id") {
                $campos .= $key . ", ";
                $valores .= "'{$D006_Id}',";
            } elseif ($key == "T224_C004_Id") {
                $campos .= $key . ", ";
                $valores .= "'{$C004_Id_Novo}',";
            } elseif ($key == "T224_Flag_Dividido") {
                $campos .= $key . ", ";
                $valores .= "'S',";
            } elseif ($key == "T224_Flag_Status") {
                $campos .= $key . ", ";
                $valores .= "'0',";
            } elseif ($key == "T224_Nome_Status") {
                $campos .= $key . ", ";
                $valores .= "'Pendente',";
            } elseif ($key == "T224_Data_Emissao") {
                $campos .= $key . ", ";
                $valores .= "'" . date("Y-m-d") . "',";
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
        $novoT224_Id = $g['mysqlLastId'];

        // Seleciona os itens da OC para ver se existe algum marcado
        $sqlT225 = explode("FROM T225", $sqlGrid);
        $sqlT225 = "SELECT T225.* FROM T225 {$sqlT225[1]}";
        $resultT225 = mysql_query($sqlT225);
        $arrayRetorno['T224_Id'] = $novoT224_Id;
        $arrayRetorno['T224_Id_Antigo'] = $T224_Id_Antigo;
        log("SQL ***=".$sqlT225);

        // Usa a SQL da T225 feita no início do método para fazer update dos itens que foram selecionados para gerar outra ordem de compra
        while ($linhaT225 = mysql_fetch_assoc($resultT225)) {
            if ($linhaT225['T225_Quantidade_Dividir'] <= 0) { continue; }

            // Caso seja para dividr a OC conforme a quantidade, entra no IF. senão, no ELSE
            if ($linhaT225['T225_Quantidade_Dividir'] > 0) {
                $campos  = "";
                $valores = "";

                $T224_Id = $linhaT225['T225_T224_Id'];
                $antigoT224_Id[$T224_Id] = $T224_Id;

                // Caso a quantidade á dividir seja igual a quantidade da OC, será feito um update mudando o item da oc para o novo cabeçalho
                if ($linhaT225['T225_Quantidade_Dividir'] == $linhaT225['T225_Quantidade']) {
                    // Daqui para baixo (dentro desse IF) faz o processo normal de alteração de cabeçalho da OC
                    $sqlD049    = "SELECT D009_D049_Id FROM D009 WHERE D009_Id='{$linhaT225['T225_D009_Id']}'";
                    $resultD049 = mysql_query($sqlD049);
                    $linhaD049  = mysql_fetch_array($resultD049);
                    $erroSQL    = mysql_error();
                    if (!empty($erroSQL)) {
                        mysql_query("ROLLBACK");
                        return '2...' . $erroSQL;
                    }
                    /**
                     * Buscando o D009 correto para a empresa selecionada.
                     */
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
                    $D006_Id = $EST015->retornaCFOP($linhaT224['T224_D024_Id'], $linhaD009['D009_Id'], $C004_Id_Novo, $linhaT225['T225_Flag_Origem_Produto'],false,false,$linhaT225['T225_Flag_Destino_Produto']);

                    /**
                     * Faz update nos itens do pedido alterando o T224, C004 (id da empresa nova) e o D009 da nova empresa
                     */
                    $upT225 = "UPDATE T225 SET T225_T224_Id='{$novoT224_Id}', T225_C004_Id='{$C004_Id_Novo}', T225_Flag_Dividir='N', T225_D006_Id='{$D006_Id}', T225_D009_Id='{$linhaD009['D009_Id']}', T225_Quantidade_Dividir=0 WHERE T225_Id='{$linhaT225['T225_Id']}'";
                    mysql_query($upT225);
                    $erroSQL = mysql_error();
                    if (!empty($erroSQL)) {
                        mysql_query("ROLLBACK");
                        return '4' . $erroSQL;
                    }
                } else { // Caso a quantidade seja menor que a quantidade anterior, é feito um insert com a nova OC e um update no item antigo diminuindo a quantidade
                    $quantidadeAntiga = $linhaT225['T225_Quantidade'] - $linhaT225['T225_Quantidade_Dividir'];
                    $quantidadeNova   = $linhaT225['T225_Quantidade_Dividir'];

                    // Atualizando a quantidade do item antigo.
                    $upT225Antigo = mysql_query("UPDATE T225 SET T225_Quantidade='{$quantidadeAntiga}', T225_Quantidade_Dividir='', T225_Quantidade_Dividido = 0 WHERE T225_Id='{$linhaT225['T225_Id']}'");

                    $sqlD049    = "SELECT D009_D049_Id FROM D009 WHERE D009_Id='{$linhaT225['T225_D009_Id']}'";
                    $resultD049 = mysql_query($sqlD049);
                    $linhaD049  = mysql_fetch_array($resultD049);
                    $erroSQL    = mysql_error();
                    if (!empty($erroSQL)) {
                        mysql_query("ROLLBACK");
                        return '2...' . $erroSQL;
                    }
                    /**
                     * Buscando o D009 correto para a empresa selecionada.
                     */
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
                    $D006_Id = $EST015->retornaCFOP($linhaT224['T224_D024_Id'], $linhaD009['D009_Id'], $C004_Id_Novo, $linhaT225['T225_Flag_Origem_Produto'],false,false,$linhaT225['T225_Flag_Destino_Produto']);

                    // Selecionando todos os dados do T225 atual para fazer um insert do novo T225 com a quantidade nova
                    foreach ($linhaT225 as $key => $value) {
                        if ($key == 'T225_Id') {
                            continue;
                        } elseif ($key == "T225_T224_Id") {
                            $campos .= $key . ", ";
                            $valores .= "'" . mysql_real_escape_string($novoT224_Id) . "',";
                        } elseif ($key == "T225_T225_Id_Dividir") {
                            $campos .= $key . ", ";
                            if($linhaD009['D009_Id'] == $linhaT225['T225_D009_Id']){
                                $valores .= "'" . mysql_real_escape_string($linhaT225['T225_Id']) . "',";
                            } else {
                                $valores .= "'0',";
                            }
                        } elseif ($key == "T225_C004_Id") {
                            $campos .= $key . ", ";
                            $valores .= "'" . mysql_real_escape_string($C004_Id_Novo) . "',";
                        } elseif ($key == "T225_Quantidade_Dividir" || $key == "T225_Quantidade_Dividido") {
                            $campos .= $key . ", ";
                            $valores .= "'',";
                        } elseif ($key == "T225_Quantidade") {
                            $campos .= $key . ", ";
                            $valores .= "'" . mysql_real_escape_string($quantidadeNova) . "',";
                        } elseif ($key == "T225_D006_Id") {
                            $campos .= $key . ", ";
                            $valores .= "'" . mysql_real_escape_string($D006_Id) . "',";
                        } elseif ($key == "T225_D009_Id") {
                            $campos .= $key . ", ";
                            $valores .= "'" . mysql_real_escape_string($linhaD009['D009_Id']) . "',";
                        } else {
                            $campos .= $key . ", ";
                            $valores .= "'" . mysql_real_escape_string($value) . "',";
                        }
                    }
                    $campos  = trim($campos, ', ');
                    $valores = trim($valores, ',');

                    // Monta o Insert e executa
                    $insT225    = "INSERT INTO T225 ({$campos}) VALUES ({$valores})";
                    $resT225    = mysql_query($insT225);
                    $lastT225Id = $g['mysqlLastId'];
                    $erroSQL    = mysql_error();
                    if (!empty($erroSQL)) {
                        mysql_query("ROLLBACK");
                        return '1.1...' . $erroSQL;
                    }
                }
            } else if ($linhaT225['T225_Flag_Dividir'] == "S") {
                $sqlD049    = "SELECT D009_D049_Id FROM D009 WHERE D009_Id='{$linhaT225['T225_D009_Id']}'";
                $resultD049 = mysql_query($sqlD049);
                $linhaD049  = mysql_fetch_array($resultD049);
                $erroSQL    = mysql_error();
                if (!empty($erroSQL)) {
                    mysql_query("ROLLBACK");
                    return '2...' . $erroSQL;
                }
                /**
                 * Buscando o D009 correto para a empresa selecionada.
                 */
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
                $D006_Id = $EST015->retornaCFOP($linhaT224['T224_D024_Id'], $linhaD009['D009_Id'], $C004_Id_Novo, $linhaT225['T225_Flag_Origem_Produto'], false,false, $linhaT225['T225_Flag_Destino_Produto']);

                /**
                 * Faz update nos itens do pedido alterando o T224, C004 (id da empresa nova) e o D009 da nova empresa
                 */
                $upT225 = "UPDATE T225 SET T225_T224_Id='{$novoT224_Id}', T225_C004_Id='{$C004_Id_Novo}', T225_Flag_Dividir='N', T225_D006_Id='{$D006_Id}', T225_D009_Id='{$linhaD009['D009_Id']}' WHERE T225_Id='{$linhaT225['T225_Id']}'";
                mysql_query($upT225);
                $erroSQL = mysql_error();
                if (!empty($erroSQL)) {
                    mysql_query("ROLLBACK");
                    return '4' . $erroSQL;
                }
            }

            // Executa reprocessa histórico do produto
            //esses métodos agora serão executados em etapas no form  - Lígia Nakazato 27/08/2018
            // $sqlD001 = mysql_query("SELECT D049_D001_Id FROM D049 LEFT JOIN D009 ON D049_Id=D009_D049_Id WHERE D009_Id = '{$linhaT225['T225_D009_Id']}'");
            // $resD001 = mysql_fetch_array($sqlD001);

            // require_once("bibliotecas/classes/CAD002.php");
            // $CAD002 = new CAD002();
            // $CAD002->D001_reprocessa_historico($resD001['D049_D001_Id']);
            // mysql_query("CALL T225_Gravar_Totalizacao_4('{$linhaT225['T225_Id']}')");
        }

        $antigoT224_Id = array_keys($antigoT224_Id);
        for ($i = 0; $i < count($antigoT224_Id); $i++) {
            $T224_Id_Antigo = $antigoT224_Id[$i];
            //esse método agora será realizado em etapas no form - Lígia Nakazato 27/08/2018
            // mysql_query("CALL T224_Gravar_Totalizacao_4('{$T224_Id_Antigo}')");
            mysql_query("INSERT INTO T228 (T228_T224_Id, T228_C007_Id, T228_Data, T228_Descricao, T228_Observacao) VALUES ('{$T224_Id_Antigo}', '{$g['usuarioAtual']}', CURRENT_TIMESTAMP(), 'Ordem de compra gerou divisão para ordem de compra {$novoT224_Id}', '')");
            $pedidosAntigos .= $T224_Id_Antigo . ', ';
        }
        $pedidosAntigos = substr($pedidosAntigos, 0, -2);

        //esses métodos agora serão executados em etapas no form  - Lígia Nakazato 27/08/2018
        // $this->atualizarAliquotasPedidos($novoT224_Id);
        // mysql_query("CALL T224_Gravar_Totalizacao_4('{$novoT224_Id}')");

        mysql_query("INSERT INTO T228 (T228_T224_Id, T228_C007_Id, T228_Data, T228_Descricao, T228_Observacao) VALUES ('{$novoT224_Id}', '{$g['usuarioAtual']}', CURRENT_TIMESTAMP(), 'Ordem de compra gerada da divisão da ordem de compra {$pedidosAntigos}', '')");

        mysql_query("COMMIT");
        return $arrayRetorno;
        //return true;
    }

    /**
     * processarNumeroItemOrdemCompra
     *
     * @param String $T225_Id
     * @return Bool
     */
    public function processarNumeroItemOrdemCompra($T225_Id, $novoNumeroItem)
    {
        global $g;
        $ultimoItem = 0;
        $contadorItem = 1;
        $sql = mysql_query("SELECT T225_Item, T225_Id FROM T225 WHERE T225_T224_Id = (SELECT T225_T224_Id FROM T225 WHERE T225_Id = '{$T225_Id}') AND T225_Id != '{$T225_Id}' ORDER BY T225_Item");
        while($row = mysql_fetch_array($sql)){
            if($contadorItem == $novoNumeroItem){ $contadorItem++; }
            mysqli_query("UPDATE T225 SET T225_Item = '{$contadorItem}' WHERE T225_Id = '{$row['T225_Id']}'");
            $contadorItem++;
        }
        return true;
    }

    /**
     * pesquisaGridItemOC
     *
     * @param array  $rowT224
     * @param  int   $T224_Id
     * @return int
     */
    public function pesquisaGridItemOC($T224_Id, $mT224 = array())
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
			LEFT JOIN D083 on D083_D001_Id=D001_Id AND D083_D024_Id={$mT224['T224_D024_Id']}
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
			'D009_Id||C008_Id||D060_Id||foto||D001_Codigo_Produto||D049_Modelo||D083_Codigo_Produto_Fornecedor||D001_Descricao_Produto||D001_Aplicacao||substr(D082_Marca,1,15) as D082_Marca||D037_Unidade||D005_Classificacao_Fiscal||D009_Quantidade_Estoque_Real||D009_Quantidade_Estoque_Liquido as Pedido||(select sum(T066_Quantidade_Estoque) from T066 left join D004 on D004_Id=T066_D004_Id  left join D146 ON D146_Id = D004_D146_Id where T066_D009_Id=D009_Id and UPPER(D146_Descricao)="LOJA") as Estoque||D009_Quantidade_Estoque_Fora||T066_Quantidade_Estoque||D009_Quantidade_OC||D004_Local||substr(C007_Primeiro_Nome,1,10) as C007_Primeiro_Nome||D001_Especificacoes||D001_Descricao_Ingles||D049_Id||D001_Flag_Pre_Cadastro||D144_Id||D060_Descricao_Grupo||D001_Id||D009_Quantidade_Estoque_Tabela(D009_Id)',	// campos
			$pesquisaGridExtra,																								              // extras sql
			'Pesquisa de Produto',																										  // título da janela
			'Id,Dep,Setor,Foto,Código,Sub-item,Cód.Cli,Descrição,Referência,Marca,UN,NCM,Estoque Real,Estoque Líq,Est. Loja,Estoque Fora,Estoque Locação,OC,Locação,Comprador,Observações,Descrição Inglês,D049_Id,D001_Flag_Pre_Cadastro,D144_Id,Setor,T003_Id,Est.Tab',	  // descrição dos campos
			'campoProcuraProduto',																							              // callback js
			$g['divId'],																									              // divId
			array($T224_Id,$mT224['T224_D024_Id']),																										              // parametro extra
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
			'D009_Id||D049_Id||foto||D009_Preco_1||D009_Preco_2||D009_Preco_3||D009_Quantidade_Estoque||D009_Quantidade_Estoque_Liquido||D144_Id||C008_Id||D060_Id||D009_Quantidade_Estoque_Real||D009_Quantidade_Estoque_Fora||T066_Quantidade_Estoque||D009_Quantidade_OC||D001_Flag_Pre_Cadastro||D144_Id||D001_Id||D005_Classificacao_Fiscal||D009_Quantidade_Estoque_Tabela(D009_Id)||D037_Unidade||D004_Local||D049_Modelo', // Campos que são ignorados na cláusula where do pesquisaGrid
			null,
			null,
			null,
			null,
			null,
			'D001_Descricao_Produto,Descricao,texto||D001_Codigo_Produto,Codigo,texto||D009_Quantidade_Estoque_Liquido,Estoque,numero', // Filtros para produtos
		);

        return $pesquisaGrid;
    }

    /**
     * insereCabecalhoPedido
     *
     * @param array  $rowT224
     * @param  int   $status
     * @return int
     */
    public function insereCabecalhoPedido($rowT224, $status)
    {
        global $g;

        $D024 = mysql_query("SELECT C004_D024_Id,C004_Nome_Abreviado FROM C004 WHERE C004_Id = '{$rowT224['T224_C004_Id']}'");
        $mD024 = mysql_fetch_array ($D024);
        // Gera o cabeçalho do pedido
        $insertT005 = array(
            'T005_C004_Id'                 => $rowT224['C004_Id'],
            //'T005_D024_Id'                 => $g['C004']['C004_D024_Id'],
            'T005_D024_Id'                 => $mD024['C004_D024_Id'],
            'T005_Flag_Status'             => $status,
            'T005_Nome_Status'             => 'T005_Status_Pedido (T005_Flag_Status, 1)',
            'T005_T224_Id'                 => $rowT224['T224_Id'],
            'T005_C007_Id_Vendedor_Interno'=> $rowT224['T224_C007_Id_Vendedor_Interno'],
            'T005_Flag_ACP'                => $rowT224['T224_Flag_ACP'],
            'T005_Data_Emissao'            => 'CURDATE()',
            'T005_Flag_Situacao'           => $rowT224['T224_Flag_Situacao'],
            'T005_Flag_Revenda_Consumidor' => $rowT224['T224_Flag_Revenda_Consumidor'],
            'T005_Flag_Lista_Preco'        => '4',
            'T005_Flag_Destino_Produto'    => '2',
            'T005_Flag_Separacao'          => 'S',
            'T005_Flag_Separacao_Liberar'  => 'N',
            'T005_Data_Entrega'            => 'CURDATE()'
        );
        $sqlChaves = '';
        $sqlDados = '';
        foreach ($insertT005 as $key => $value) {
            $sqlChaves .= $sqlChaves ? ',' : '';
            $sqlChaves .= $key;
            $sqlDados .= $sqlDados ? ',' : '';
            if ($key == 'T005_Data_Emissao' || $key == 'T005_Data_Entrega' || $key == 'T005_Nome_Status') { 
                $sqlDados .= $value;
            } else {
                $sqlDados .= "'" . mysql_real_escape_string($value) . "'";
            }
        }
        $sql = "INSERT INTO T005 ( {$sqlChaves} ) VALUES ( {$sqlDados} )";
        mysql_query($sql);
        if ($erro = mysql_error()) {
            mysql_query("ROLLBACK");
            return 'Erro: ' . $erro;
        }
        $T005_Id = $g['mysqlLastId'];

        // Histórico do pedido
        $msg = mysql_real_escape_string("Pedido Gerado via Ordem de Transferência número {$rowT224['T224_Id']} pela empresa {$mD024['C004_Nome_Abreviado']}");
        mysql_query("INSERT INTO T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'{$msg}','')");
        mysql_query("INSERT INTO T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Aguardando Impressão','')");

        return $T005_Id;
    }

    public function PersonalizacaoTriggerPosAprovacaoOc($params){}
}
