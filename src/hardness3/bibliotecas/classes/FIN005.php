<?php
namespace hardness;
definirClasse('FIN005');
require_once('bibliotecas/boletoProtegido.php');
class FIN005_
{
    public function usarBoletoProtegido($C004_Id = false)
    {
        return boletoProtegidoHabilitado($C004_Id);
    }

    public function montarLinkBoletoProtegido($T117_Id, $T117_D024_Id = false)
    {
        global $confUsuario;

        $T117_Id = mysql_real_escape_string($T117_Id);
        if (empty($T117_D024_Id)) {
            $sql = mysql_query("SELECT T117_D024_Id FROM T117 WHERE T117_Id = '{$T117_Id}'");
            $row = mysql_fetch_assoc($sql);
            $T117_D024_Id = $row['T117_D024_Id'];
        }

        $cQuery = md5($T117_Id . $T117_D024_Id);
        return boletoMontarLinkProtegido($cQuery);
    }

    /**
     * cancelar
     *
     * @param String $T117_Id
     * @return true
     */ 
    public function cancelar($T117_Id)
    {
        global $g;
        $T117_Id = mysql_real_escape_string($T117_Id);
        $sql     = "UPDATE T117 SET T117_Flag_Cancelado='S' WHERE T117_Id='$T117_Id'";
        mysql_query($sql);
        $sql = "UPDATE T002 SET T002_T117_Id='' WHERE T002_T117_Id='$T117_Id'";
        mysql_query($sql);
        return true;
    }
    
    /**
     * updateT002_Flag_Gerar_Boleto
     *
     * @param String $T002_Id
     * @param String $selected
     * @return true
     */
    public function updateT002_Flag_Gerar_Boleto($T002_Id, $selected)
    {
        global $g;
        $T002_Id  = mysql_real_escape_string($T002_Id);
        $selected = mysql_real_escape_string($selected);
        $sql      = "UPDATE T002 SET T002_Flag_Gerar_Boleto='$selected' WHERE T002_Id='$T002_Id'";
        mysql_query($sql);
        return true;
    }
    
    /**
     * gerarBoleto
    *
    * @param Form $form
    * @param array $dadosAgrupamentoBoleto Dicion�rio de dados, usado na gera��o autom�tica de boletos durante o agrupamento de t�tulos (FIN001 - agrupar).
    * @return string/bool
    */
    public function gerarBoleto($form, $dadosAgrupamentoBoleto=false)
    {
        global $g;

        // Verifica se a requisi��o de gerar o boleto veio do "form" ou da gera��o autom�tica, resultante do agrupamento de t�tulos.
        if (!$dadosAgrupamentoBoleto) {
            $T117_D024_Id              = $form->campoValorEnviado('T117_D024_Id');
            $T117_D007_Id              = $form->campoValorEnviado('T117_D007_Id');
            $T117_Percentual_Multa     = $form->campoValorEnviado('T117_Percentual_Multa');
            $T117_Percentual_Juros_Dia = gCorrigeNumeroInverte($form->campoValorEnviado('T117_Percentual_Juros_Dia'));
            $T117_Data_Vencimento      = gCorrigeDataInverte($form->campoValorEnviado('T117_Data_Vencimento'));
            $T117_Instrucoes           = $form->campoValorEnviado('T117_Instrucoes');
            $T002_Id                   = $form->campoValorEnviado('T002_Id');
            $mensagemJurosMultas       = $form->campoValorEnviado('mensagemJurosMultas');
        } else {
            $T117_D024_Id              = $dadosAgrupamentoBoleto['D024_Id'];
            $T117_D007_Id              = $dadosAgrupamentoBoleto['D007_Id'];
            $T117_Percentual_Multa     = $dadosAgrupamentoBoleto['Percentual_Multa'];
            $T117_Percentual_Juros_Dia = $dadosAgrupamentoBoleto['Percentual_Juros_Dia'];
            $T117_Data_Vencimento      = $dadosAgrupamentoBoleto['Data_Vencimento'];
            $T117_Instrucoes           = $dadosAgrupamentoBoleto['Instrucoes_Boleto'];
            $T002_Id                   = 0;
            $mensagemJurosMultas       = '';
        }
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        $cClausula = " SELECT ";
        $cClausula .= " datediff(T002_Data_Vencimento,current_date()) Dias, ";
        $cClausula .= " T002_Id, ";
        $cClausula .= " T002_Numero_Duplicata, ";
        $cClausula .= " T002_Valor_Saldo, ";
        $cClausula .= " T002_Flag_Gerar_Boleto, ";
        $cClausula .= " T002_Data_Vencimento, ";
        $cClausula .= " T002_Data_Vencimento_1, ";
        $cClausula .= " T002_Data_Vencimento_2, ";
        $cClausula .= " T002_Data_Vencimento_3, ";
        $cClausula .= " T002_Data_Vencimento_4, ";
        $cClausula .= " T002_Data_Vencimento_5, ";
        $cClausula .= " T002_Data_Recebimento, ";
        $cClausula .= " T007_Id, ";
        $cClausula .= " T007_Flag_ACP, ";
        $cClausula .= " T002_Numero_Parcela ";
        $cClausula .= " FROM T002 ";
        $cClausula .= " LEFT JOIN D024 on D024_Id=T002_D024_Id ";
        $cClausula .= " LEFT JOIN C004 on C004_Id=T002_C004_Id ";
        $cClausula .= " LEFT JOIN D020 on D020_Id=D024_D020_Id ";
        $cClausula .= " LEFT JOIN D018 on D018_Id=D020_D018_Id ";
        $cClausula .= " LEFT JOIN D027 on D027_Id=T002_D027_Id ";
        $cClausula .= " LEFT JOIN T007 on T007_Id=T002_T007_Id ";
        $cClausula .= " LEFT JOIN T117 on T002_T117_Id=T117_Id ";
        // $cClausula .= " LEFT JOIN D007 on D007_Id=T117_D007_Id ";
        // $cClausula .= " LEFT JOIN D062 on D062_Id=D007_D062_Id ";
        $cClausula .= " WHERE T002_Flag_Cancelada='N' ";
        $cClausula .= " AND T117_Id is null ";
        $cClausula .= " AND T002_Data_Recebimento='0000-00-00' ";
        $cClausula .= " AND T002_D024_Id='$T117_D024_Id' ";
        $cClausula .= " AND T002_C004_Id='{$g['empresaAtual']}' ";
        if($T002_Id > 0){
            $cClausula .= " AND T002_Id='{$T002_Id}'";
        } 
        $cClausula .= " AND T002_Flag_Gerar_Boleto='1'";
        
        $cSql_T002 = mysql_query($cClausula);
        $cErro     = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "ERRO: $cErro<br />Clausula: $cClausula";
        }
        
        //-- PEGA O ULTIMO NUMERO DO "NOSSO NUMERO"
        $cClausula = " SELECT *";
        $cClausula .= " FROM D007 ";
        $cClausula .= " LEFT JOIN D062 on D062_Id=D007_D062_Id ";
        $cClausula .= " WHERE D007_Id='$T117_D007_Id' ";
        $cSql_D007 = mysql_query($cClausula);

        $cErro     = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "ERRO: $cErro<br />Clausula: $cClausula";
        }
        $mD007 = mysql_fetch_array($cSql_D007);
        
        if (empty($mD007['D062_Numero_Banco'])) {
            mysql_query("ROLLBACK");
            return "Numero do banco nao foi definido";
        }
        
        if ($mD007['D062_Numero_Banco'] == '409' AND strlen($mD007['D007_Nosso_Numero']) > 10) {
            mysql_query("ROLLBACK");
            return "Campo: Nosso numero definido na conta bancaria não pode ultrapassar 10 caracteres: {$mD007['D007_Nosso_Numero']}";
        }
        if ($mD007['D062_Numero_Banco'] == '104' AND strlen($mD007['D007_Nosso_Numero']) > 9) {
            mysql_query("ROLLBACK");
            return "Campo: Nosso numero definido na conta bancaria não pode ultrapassar 9 caracteres:{$mD007['D007_Nosso_Numero']}";
        }
        
        if ($mD007['D062_Numero_Banco'] == '399') {
            if (strlen($mD007['D007_Numero_Range']) != 5) {
                mysql_query("ROLLBACK");
                return "O campo Range definido na conta bancária, não pode ser diferente de 5 caracteres: {$mD007['D007_Numero_Range']}";
            }
            if (strlen($mD007['D007_Nosso_Numero']) != 5) {
                mysql_query("ROLLBACK");
                return "O campo Nosso Numero definido na conta bancaria, não pode ser diferente de 5 caracteres: {$mD007['D007_Nosso_Numero']}";
            }
        }
        
        if (!empty($mD007['D007_Codigo_Cedente'])) {
            $xCodigo_Cedente = $mD007['D007_Codigo_Cedente'];
        } else {
            $xCodigo_Cedente = $T117_D024_Id;
        }
        
        $T117_Flag_Layout = 'N';
        if ($mD007['D007_Flag_Layout'] != 2) {
            $T117_Flag_Layout = 'S';
        }
        
        if ($mD007['D007_Flag_Tipo_Registro'] == '1') {
            $D007_Flag_Com_Registro = 'S';
        } elseif ($mD007['D007_Flag_Tipo_Registro'] == '2') {
            $D007_Flag_Com_Registro = 'N';
        } else {
            return "Tipo de cobrança deve ser definido como: COM REGISTRO ou SEM REGISTRO no cadatro de contas";
        }
        
        $cClausula = " INSERT into T117 ( ";
        $cClausula .= " T117_C004_Id, ";
        $cClausula .= " T117_D024_Id, ";
        $cClausula .= " T117_D007_Id, ";
        $cClausula .= " T117_Data_Emissao, ";
        $cClausula .= " T117_Data_Vencimento, ";
        $cClausula .= " T117_Valor_Total, ";
        $cClausula .= " T117_Percentual_Juros_Dia, ";
        $cClausula .= " T117_Percentual_Multa, ";
        $cClausula .= " T117_Instrucoes, ";
        $cClausula .= " T117_Nosso_Numero, ";
        $cClausula .= " T117_Flag_Layout, ";
        $cClausula .= " T117_Flag_Com_Registro, ";
        $cClausula .= " T117_Codigo_Cedente, ";
        $cClausula .= " T117_Aceite, ";
        $cClausula .= " T117_Especie, ";
        $cClausula .= " T117_Especie_Documento, ";
        $cClausula .= " T117_Uso_Banco, ";
        $cClausula .= " T117_T002_Id ";
        $cClausula .= " ) values (";
        $cClausula .= " '{$g['empresaAtual']}', ";
        $cClausula .= " '$T117_D024_Id', ";
        $cClausula .= " '$T117_D007_Id', ";
        $cClausula .= " current_date(), ";
        $cClausula .= " '$T117_Data_Vencimento', ";
        $cClausula .= " '0', ";
        $cClausula .= " '$T117_Percentual_Juros_Dia', ";
        $cClausula .= " '$T117_Percentual_Multa', ";
        $cClausula .= " '$T117_Instrucoes', ";
        $cClausula .= " '{$mD007['D007_Nosso_Numero']}', ";
        $cClausula .= " '$T117_Flag_Layout', ";
        $cClausula .= " '$D007_Flag_Com_Registro', ";
        $cClausula .= " '$xCodigo_Cedente', ";
        $cClausula .= " '{$mD007['D007_Aceite']}', ";
        $cClausula .= " '{$mD007['D007_Especie']}', ";
        $cClausula .= " '{$mD007['D007_Especie_Documento']}', ";
        $cClausula .= " '{$mD007['D007_Uso_Banco']}', ";
        if (mysql_num_rows($cSql_T002) == 1) {
            $T002 = mysql_fetch_array($cSql_T002);
            mysql_data_seek($cSql_T002, 0);
            $cClausula .= " '{$T002['T002_Id']}' ";
        } else {
            $cClausula .= "''";
        }
        $cClausula .= " )";
        mysql_query($cClausula);
        $cErro    = mysql_error();
        $nT117_Id = $g['mysqlLastId'];
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "ERRO: $cErro<br />Clausula: $cClausula";
        }

        mysql_query("INSERT INTO T117A (T117A_T117_Id, T117A_Mensagem_Juros_Multa) VALUES ('{$nT117_Id}', '{$mensagemJurosMultas}')");
        
        $nIncluido    = 0;
        $nValor_Total = 0;
        while ($mT002 = mysql_fetch_array($cSql_T002)) {
            if ($mT002['T002_Flag_Gerar_Boleto'] == '1') {
                $nValor_Total += $mT002['T002_Valor_Saldo'];
                $nIncluido++;
                $cClausula = " UPDATE T002 SET ";
                if (empty($mT002['T002_Data_Vencimento_1'])) {
                    $cClausula .= " T002_Data_Vencimento_1 = T002_Data_Vencimento, ";
                } elseif (empty($mT002['T002_Data_Vencimento_2'])) {
                    $cClausula .= " T002_Data_Vencimento_2 = T002_Data_Vencimento, ";
                } elseif (empty($mT002['T002_Data_Vencimento_3'])) {
                    $cClausula .= " T002_Data_Vencimento_3 = T002_Data_Vencimento, ";
                } elseif (empty($mT002['T002_Data_Vencimento_4'])) {
                    $cClausula .= " T002_Data_Vencimento_4 = T002_Data_Vencimento, ";
                } elseif (empty($mT002['T002_Data_Vencimento_5'])) {
                    $cClausula .= " T002_Data_Vencimento_5 = T002_Data_Vencimento, ";
                }
                //	   $cClausula .= " T002_Data_Vencimento='$cT117_Data_Vencimento', ";
                $cClausula .= " T002_T117_Id='$nT117_Id', ";
                $cClausula .= " T002_D007_Id='$T117_D007_Id' ";
                // clausula para tirar a flag de gera��o de boleto
                $cClausula .= " WHERE T002_Id='{$mT002['T002_Id']}' ";
                mysql_query($cClausula);
                $cErro = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "ERRO: $cErro<br />Clausula: $cClausula";
                }
            }
        } // fecha o while
        
        $cClausula = " UPDATE T117 SET ";
        $cClausula .= " T117_Valor_Total='$nValor_Total' ";
        $cClausula .= " WHERE T117_Id='$nT117_Id' ";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "ERRO: $cErro<br />Clausula: $cClausula";
        }

        //Valida se o Novo Nosso Número já existe
        $validaNossoNumero = $mD007['D007_Nosso_Numero'] + 1 ;
        $sqlValidaNossoNumero = "SELECT T117_Nosso_Numero 
                                   FROM T117 
                              LEFT JOIN D007 ON T117_D007_Id = D007_Id
                                  WHERE T117_Nosso_Numero = '$validaNossoNumero'                               
                                    AND D007_Id='$T117_D007_Id'";
        $resultValidaNossoNumero = mysql_num_rows(mysql_query($sqlValidaNossoNumero));
        log("sql = ".$sqlValidaNossoNumero);

        //-- Para qualquer banco:
        if (empty($mD007['D007_Nosso_Numero'])) {
            $mD007['D007_Nosso_Numero'] = 1;
        } else if(!empty($mD007['D007_Nosso_Numero']) && ($resultValidaNossoNumero == 0)) {
            $mD007['D007_Nosso_Numero']++;
            log("Nosso Numero = ".$mD007['D007_Nosso_Numero']);
        } else {
            return "Erro ao atualizar Nosso Número";
        }        
        
        if ($mD007['D062_Numero_Banco'] == '399') {
            if (strlen($mD007['D007_Nosso_Numero']) > 5) {
                mysql_query("ROLLBACK");
                return "O campo Nosso Numero ultrapassou limite: 99999 entre em contato com o banco e solicite um novo Range + Nosso Numero e grave-os no cadastro de contas.";
            }
            $mD007['D007_Nosso_Numero'] = STR_PAD($mD007['D007_Nosso_Numero'], 5, "0", STR_PAD_LEFT);
        }
        
        $cClausula = " UPDATE D007 SET ";
        $cClausula .= " D007_Nosso_Numero='{$mD007['D007_Nosso_Numero']}' ";
        $cClausula .= " WHERE D007_Id='$T117_D007_Id' ";
        $cSql_D007 = mysql_query($cClausula);
        $cErro     = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "ERRO: $cErro<br />Clausula: $cClausula";
        }
        
        //-- ESTE ARQUIVO IR? GERAR O CODIGO DE BARRAS A LINHA DIGITAVEL E O NOSSO NUMERO COM DIGITO VERIFICADOR
        
        chdir($g['pathRaiz'] . 'hardness/boleto/');
        $cArquivo_Gerar = "./include/gerar_" . $mD007['D062_Numero_Banco'] . ".php";
        if (file_exists($cArquivo_Gerar)) {
            $arquivo = include($cArquivo_Gerar);
            if (!empty($arquivo) && $arquivo !== 1) {
                chdir($g['pathRaiz']);
                return $arquivo;
            }
        } else {
            mysql_query("ROLLBACK");
            chdir($g['pathRaiz']);
            return "Arquivo inválido: $cArquivo_Gerar";
        }
        
        chdir($g['pathRaiz']);
        
        if ($nIncluido > 0) {
            mysql_query("COMMIT");
            return true;
        } else {
            mysql_query("ROLLBACK");
            return "Selecione os títulos para gerar o boleto";
        }
        
        /*
        global $g;
        $T002_Id = mysql_real_escape_string($T002_Id);
        $selected = mysql_real_escape_string($selected);
        $sql = "UPDATE T002 SET T002_Flag_Gerar_Boleto='$selected' WHERE T002_Id='$T002_Id'";
        mysql_query($sql);
        return true;
        */
    }
    
    /**
     * montarEmail
     *
     * @param String $T117_Id
     * @return array
     */     
    public function montarEmail($T117_Id)
    {
        global $g, $confUsuario;
        $T117_Id     = mysql_real_escape_string($T117_Id);
        $sql         = "SELECT * FROM T117 LEFT JOIN D024 ON D024_Id=T117_D024_Id WHERE T117_Id='{$T117_Id}'";
        $Consulta    = mysql_query($sql);
        $Consulta    = mysql_fetch_array($Consulta);
        $array['cc'] = '';
        
        $sql          = "SELECT C004_Nome_Empresa,C004_Site,C004_Logo_Marca,C004_Nome_Abreviado FROM C004 WHERE C004_Id='{$g['empresaAtual']}'";
        $empresaAtual = mysql_query($sql);
        $empresaAtual = mysql_fetch_array($empresaAtual);
        
        $dataVencimento = gCorrigeData($Consulta['T117_Data_Vencimento']);
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
        }
        
        $array['Para']    = (!empty($Consulta['D024_E_Mail_Boleto'])) ? $Consulta['D024_E_Mail_Boleto'] : $Consulta['D024_E_Mail'];
        $array['Assunto'] = "Boleto Cobrança";

        $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
        $caminhoLogo   = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";

        $dadosEmpresa = <<<EOT
                <b>{$g['C004']['C004_Nome_Empresa']}</b><br />
                {$g['C004']['C004_Logradouro']}, {$g['C004']['C004_Numero']} {$g['C004']['C004_Complemento']}<br />
                {$g['C004']['C004_Cep']} - {$g['C004']['D020_Nome_Cidade']} - {$g['C004']['D018_UF']}<br />
                Fone: {$g['C004']['C004_Telefone']}<br />
                {$g['C004']['C004_Site']}<br />
EOT;

        $blocoBoleto = <<<EOT
                <div style="clear:both;padding:10px">
                    <img style="float:left; margin-right:10px;" src="http://nfe.hardness.com.br/hardness/imagens/jpg/boleto.jpg" />
                    <span style="font-size:14px">
                        Olá! Segue anexo boleto para pagamento. <br />
                        Vencimento em <b>{$dataVencimento}</b><br />
                    </span>
                </div>
EOT;

        if ($this->usarBoletoProtegido($Consulta['T117_C004_Id'])) {
            $linkBoleto = $this->montarLinkBoletoProtegido($Consulta['T117_Id'], $Consulta['T117_D024_Id']);
            $blocoBoleto = <<<EOT
                <div style="clear:both;padding:10px">
                    <img style="float:left; margin-right:10px;" src="http://nfe.hardness.com.br/hardness/imagens/jpg/boleto.jpg" />
                    <span style="font-size:14px">
                        Olá! O boleto para pagamento está disponível no link abaixo. <br />
                        Vencimento em <b>{$dataVencimento}</b><br />
                    </span>
                </div>
                <div style="clear:both;padding:10px">
                    <a target="_blank" href="{$linkBoleto}" style="color:#1f6feb;font-size:14px;font-weight:bold;text-decoration:none;">
                        Abrir boleto com validação de CPF/CNPJ
                    </a>
                </div>
EOT;
        }

        $array['Corpo'] = <<<EOT
                <div style="font-family:arial,verdana;font-size:10px;font-weight:bold;float: left; padding:10px">
                    <img style="float:left; margin-right:10px;" src="$caminhoLogo" />
                </div>
                <div style="font-family:arial,verdana;font-size:10px;font-weight:bold;float: left;padding:10px">
                    {$dadosEmpresa}
                </div>
			    {$blocoBoleto}
			    <div style="clear:both;padding:10px">
                    <span style="font-size:14px">
                         O pagamento pode ser realizado pela internet ou em qualquer agência bancária até a data do vencimento.<br /> <br />
                        Att.<br /><br />
                    </span>
				</div>
			<div style="font-size:10px">
				E-mail enviado por <br /> {$empresaAtual['C004_Nome_Empresa']} <br /> <a href="{$empresaAtual['C004_Site']}">{$empresaAtual['C004_Site']}<a ></a>
			</div>
EOT;
        //Link para imagem relativa a empresa logada
        //<img style="float:left; margin-right:10px;" src="{$caminhoImagem}" /> E-mail enviado por <br /> {$empresaAtual['C004_Nome_Empresa']} <br /> <a href="{$empresaAtual['C004_Site']}">{$empresaAtual['C004_Site']}<a ></a>
        return $array;   
    }
    
    /**
     * montarEmailPdf
     *
     * @param String $T117_Id
     * @return array
     */     
    public function montarEmailPdf($T117_Id)
    {
        global $g, $confUsuario;
        $link = '/hardness3/hardness/boleto/gerar_boleto.php?T117_Id=' . $T117_Id;
        $dir  = $g['pathDados'] . 'pdf/';
        is_dir($dir) or mkdir($dir, 0777, true);
        $filename = 'boleto_' . $T117_Id . '.pdf';
        $file     = $dir . $filename;
        $fileFull = $g['pathRaiz'] . '/' . $file;

        $link = str_replace(' ', '+', $link);
        //shell_exec("/usr/bin/htmltopdf.sh --zoom 1.5 \"{$g['urlRaiz']}{$link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}\" \"{$fileFull}\"");
        //shell_exec("xvfb-run --server-num " . mt_rand(0, 98) . " -s \"-screen 0 640x480x16\" wkhtmltopdf --quiet --margin-left 0 --margin-right 0 --margin-top 1 --margin-bottom 1 \"{$g['urlRaiz']}{$link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"");
        $chmod = "xvfb-run --server-num " . mt_rand(0, 98) . " -s \"-screen 0 640x480x16\" wkhtmltopdf --lowquality --margin-bottom 0 \"{$g['urlRaiz']}{$link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"";
        //die($chmod);
        shell_exec($chmod);
        log("{$g['urlRaiz']}{$link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}");
        $array['Anexos']    = array(
            $file
        );
        $array['AnexosWeb'] = array(
            '/hardness3/' . $file
        );
        return $array;
    }
    
    /**
     * enviarMultiEmailBoleto
     *
     * @param String $T117_Id
     * @param bool $enviarDanfe
     * @return bool
     */     
    public function enviarMultiEmailBoleto($T117_Id, $enviarDanfe = false)
    {
        global $g;
        require_once("bibliotecas/phpmailer/class.phpmailer.php");
        $C007 = mysql_query("SELECT * FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}';");
        $C007 = mysql_fetch_assoc($C007);
        if (empty($C007['C007_Email'])) {
                $C007['C007_Email'] = 'nfe@hardness.com.br';
                $C007['C007_Primeiro_Nome'] = $C004['C004_Nome_Abreviado'];
                $C007['C007_Email_SMTP_Senha'] = 'hgrw nvpe xliv cluz';
                $C007['C007_Email_SMTP_Servidor'] = 'smtp.gmail.com';
                $C007['C007_Email_SMTP_Autenticacao'] = 'true';
                $C007['C007_Email_SMTP_Usuario'] = 'nfe@hardness.com.br';
                $C007['C007_Email_SMTP_Porta'] = '465';
        }
        if (empty($C007) || empty($C007['C007_Email_SMTP_Servidor'])) {
            return "Seu email de usuário deve estar configurado, favor verificar.";
        }
        $monta_           = $this->montarEmail($T117_Id);
        $monta_['Anexos'] = array();
        if (empty($monta_['Para'])) {
            return "Email do cadastro do cliente deve estar preenchido.";
        }

        if (!empty($C007['C007_Email_Assinatura']) && trim($C007['C007_Email_Assinatura']) != '<br>') {
            $monta_['Corpo'] .= "<br /><br />{$C007['C007_Email_Assinatura']}";
        }

        //Pega vinculo do boleto com o titulo, também pega T117_D024_Id que é utilizado na linha 637
        $queryTitulo = "SELECT T002_Id,
                               T117_D024_Id,
                               T117_C004_Id
                          FROM T117
                     LEFT JOIN T002 ON T002_T117_Id = T117_Id
                         WHERE T117_Id = '$T117_Id'";
        //log("Query Titulo -> $queryTitulo");
        $T117 = mysql_query($queryTitulo);
        if($erro = mysql_error()){log("[FIN005.php -> enviarMultiEmailBoleto] Erro query ->" . $erro);}

        $mT117 = mysql_fetch_assoc($T117);
        $T002_Id = $mT117['T002_Id'];

        // Pega vinculo dos títulos com a nota fiscal do título, importante perceber que, um título pode ser agrupado então, em caso de boleto com título agrupado é preciso pegar todas as danfes do título em questão por isso
        // é utilizado o T002_T002_Id_Agrupado para ver quais títulos foram agrupados
        $notasTitulos = "SELECT T007_Numero_Protocolo_Nfe,
                                T007_Data_Emissao,
                                T007_Id
                           FROM T002
                      LEFT JOIN T007 ON T002_T007_Id = T007_Id
                          WHERE T002_T002_Id_Agrupado = '$T002_Id'
                             OR T002_Id = '$T002_Id'
                       GROUP BY T007_Id";
        //log("Notas Titulos -> $notasTitulos");
        $T002 = mysql_query($notasTitulos);
        if($erro = mysql_error()){log("[FIN005.php -> enviarMultiEmailBoleto] Erro query 2 -> $erro");}
        
        // Laço para cada título encontrado
        while($mT002 = mysql_fetch_assoc($T002))
        {


            $cAno_Mes_NFe  = substr($mT002['T007_Data_Emissao'], 0, 7);
            $Diretorio_NFe = $g['pathDadosAntigo'] . 'nfe';
            $cArquivo      = "$Diretorio_NFe/xml/autorizado/{$cAno_Mes_NFe}/" . $mT002['T007_Numero_Protocolo_Nfe'] . ".xml";
            $file          = $g['pathDadosAntigo'] . 'nfe/danfe/danfe_' . $mT002['T007_Numero_Protocolo_Nfe'] . '.pdf';
            // Se o caminho da nota fiscal do título existir
            // puxa as infos da nota
            if ($enviarDanfe === true && !file_exists($file) && !empty($mT002['T007_Numero_Protocolo_Nfe'])) {
                //Geramos o arquivo PDF das notas fiscais
                require_once('bibliotecas/classes/VEN003.php');
                $VEN003 = new VEN003;
                $file   = $VEN003->gerarDanfe($mT002['T007_Id'], $cArquivo);
            }
            
            // Se a danfe existir
            // adiciona no anexo do e-mail
            if ($enviarDanfe === true && file_exists($file)) {
                $monta_['Anexos'][] = $file;
                $monta_['Anexos'][] = $cArquivo;
            }
        }

        if (!$this->usarBoletoProtegido($mT117['T117_C004_Id'])) {
            $montaPDF = $this->montarEmailPdf($T117_Id);
            if (is_array($montaPDF['Anexos'])) {
                $monta_['Anexos'][] = $montaPDF['Anexos'][0];
            }
        }
        
        $mail           = new \phpmailer;
        // Mail
        $mail->From     = $C007['C007_Email'];
        $mail->FromName = $C007['C007_Primeiro_Nome'];
        $mail->Host     = $C007['C007_Email_SMTP_Servidor'];
        $mail->Mailer   = "smtp";
        $mail->SMTPAuth = $C007['C007_Email_SMTP_Autenticacao'];
        $mail->Username = $C007['C007_Email_SMTP_Usuario'];
        $mail->Password = $C007['C007_Email_SMTP_Senha'];
        $mail->Subject  = '=?UTF-8?B?'.base64_encode($monta_['Assunto']).'?=';
        $mail->Body     = $monta_['Corpo'];
        $mail->AltBody  = "ALT BODY";
        $mail->CharSet  = 'utf-8';
        $mail->SetLanguage("br");
        $mail->IsHTML(true);
        if ($C007['C007_Email_SMTP_Servidor'] == "smtp.gmail.com")
        {
            $mail->SMTPSecure   = "ssl";
            $mail->Port         = "465";
        }
        if ($C007['C007_Email_SMTP_Servidor'] == "smtp.zoho.com")
        {
            $mail->SMTPSecurity = "ssl";
            $mail->SMTPSecure   = "ssl";
            $mail->Port         = "465";
        }
        if ($C007['C007_Email_SMTP_Servidor'] == "smtp.live.com")
        {
            $mail->SMTPSecure   = "ssl";
        }
        if ($C007['C007_Email_SMTP_Servidor'] == "smtp.office365.com")
        {
            $mail->SMTPSecure   = "tls";
            $mail->SMTPSecurity = 'tls';
        }
        if (strpos($C007['C007_Email_SMTP_Servidor'], "istyle.com.br") !== false)
        {
            $mail->SMTPSecure   = "tls";
        }
        if (!empty($C007['C007_Email_SMTP_Porta']))
        {
            $mail->Port         = $C007['C007_Email_SMTP_Porta']; 
        }
        if (strpos($C007['C007_Email_SMTP_Servidor'], 'yahoo') !== false || strpos($C007['C007_Email_SMTP_Servidor'], 'comercialdex') !== false)
        {
            $mail->SMTPSecure   = "tls";
        }
        if(strpos($C007['C007_Email_SMTP_Servidor'], 'gpimports') !== false or strpos($C007['C007_Email_SMTP_Servidor'], '.gp') !== false) {
                $mail->SMTPSecure = "tls";
                $mail->SMTPSecurity = 'tls';
                //$mail->Port = '465';
        }

        switch ($C007['C007_Email_SMTP_Seguranca']) {
            case 1:
                $mail->SMTPSecure   = "ssl";
                $mail->SMTPSecurity = 'ssl';
                break;
            case 2:
                $mail->SMTPSecure   = "tls";
                $mail->SMTPSecurity = 'tls';
                break;
        }

        if ($C007['C007_Email']=='nfe@hardness.com.br') {
                $mail->SMTPSecure = "ssl";
        }
        if (empty($monta_['Para']) && !empty($monta_['cc'])) {
            $monta_['Para'] = $monta_['cc'];
            $monta_['cc']   = '';
        }
        $todosEmailsEnviados = array();
        $mPara               = preg_split("/[;,]+/", $monta_['Para']);
        for ($x = 0; $x < count($mPara); $x++) {
            $email = $mPara[$x];
            if (strpos($email, '<') !== false) {
                $email = explode('<', $email);
                $email = explode('>', $email[1]);
                $email = $email[0];
            }
            $todosEmailsEnviados[] = strtolower(trim($email));
            $mail->AddAddress($email);
        }
        if (isset($monta_['cc'])) {
            $mPara = preg_split("/[;,]+/", $monta_['cc']);
            for ($x = 0; $x < count($mPara); $x++) {
                $email = $mPara[$x];
                if (strpos($email, '<') !== false) {
                    $email = explode('<', $email);
                    $email = explode('>', $email[1]);
                    $email = $email[0];
                }
                $todosEmailsEnviados[] = strtolower(trim($email));
                $mail->AddCC($email);
            }
        }
        
        $anexosGravar   = array();
        $T139_Estrutura = array(
            'anexosManual' => array()
        );
        if (isset($monta_['Anexos'])) {
            foreach ($monta_['Anexos'] as $path) {
                $anexosGravar[]                   = $path;
                $T139_Estrutura['anexosManual'][] = basename($path);
                $mail->AddAttachment($path, basename($path));
            }
        }
        
        if ($mail->Send()) {
            mysql_query("UPDATE T117 SET T117_Data_Envio_Email=now(), T117_Flag_Atualizar = 'N' WHERE T117_Id='{$T117_Id}'");
            
            // Gravar como e-mail enviado
            $T139_De        = mysql_real_escape_string("\"{$C007['C007_Primeiro_Nome']}\" <{$C007['C007_Email']}>");
            $T139_Estrutura = serialize($T139_Estrutura);
            $T139_Para      = !empty($monta_['cc']) ? ($monta_['Para'] . '; ' . $monta_['cc']) : $monta_['Para'];
            $sql            = "INSERT INTO T139 (T139_Pasta, T139_Uid, T139_Titulo, T139_De, T139_Para, T139_Data_Hora, T139_Estrutura, T139_Mensagem_Corpo, T139_Flag_Lido, T139_D024_Id, T139_C007_Id,T139_T007_Id) VALUES ('S', 0, '" . mysql_real_escape_string($monta_['Assunto']) . "', '{$T139_De}', '" . mysql_real_escape_string($T139_Para) . "', '" . date('Y-m-d H:i:s') . "', '" . mysql_real_escape_string($T139_Estrutura) . "', '" . mysql_real_escape_string($monta_['Corpo']) . "', '1', '" . mysql_real_escape_string($mT117['T117_D024_Id']) . "', '" . mysql_real_escape_string($g['usuarioAtual']) . "', '" . mysql_real_escape_string($T117_Id) . "');";
            mysqli_query($sql);
            $g['mysqlLastId'] = mysql_insert_id();
            
            // Salvar anexos
            $dir = $g['pathDados'] . 'anexosEmail/';
            is_dir($dir) or mkdir($dir, 0777, true);
            foreach ($anexosGravar as $key => $anexo) {
                $arquivo = "{$dir}{$g['mysqlLastId']}-{$key}";
                copy($anexo, $arquivo);
            }

            if (isset($monta_['Anexos'])) {
                foreach ($monta_['Anexos'] as $path) {
                    gDeletarArquivo($path);
                }
            }

            return true;
        } else {
            $cErro=str_replace(array("'",'"',"\r","\n"),"",$mail->ErrorInfo);
            $cErro=str_replace(array("<p>","</p>"),"<br />",$cErro);
            $cErro=$cErro . ' ' . $C007['C007_Email_SMTP_Usuario'];
            return $cErro;
        }
    }
    
    /**
     * enviarMultiEmailBoleto
     *
     * @param String $T117_Id
     * @param bool $enviarDanfe
     * @return bool
     */     
    public function enviarMultiEmailBoletoACP($T117_Id, $enviarACP = false)
    {
        global $g;
        require_once("bibliotecas/phpmailer/class.phpmailer.php");
        $C007 = mysql_query("SELECT * FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}';");
        $C007 = mysql_fetch_assoc($C007);
        if (empty($C007['C007_Email'])) {
                $C007['C007_Email'] = 'nfe@hardness.com.br';
                $C007['C007_Primeiro_Nome'] = $C004['C004_Nome_Abreviado'];
                $C007['C007_Email_SMTP_Senha'] = 'hgrw nvpe xliv cluz';
                $C007['C007_Email_SMTP_Servidor'] = 'smtp.gmail.com';
                $C007['C007_Email_SMTP_Autenticacao'] = 'true';
                $C007['C007_Email_SMTP_Usuario'] = 'nfe@hardness.com.br';
                $C007['C007_Email_SMTP_Porta'] = '465';
        }
        if (empty($C007) || empty($C007['C007_Email_SMTP_Servidor'])) {
            return "Seu email de usuário deve estar configurado, favor verificar.";
        }
        $monta_           = $this->montarEmail($T117_Id);
        $monta_['Anexos'] = array();
        if (empty($monta_['Para'])) {
            return "Email do cadastro do cliente deve estar preenchido.";
        }
        
        if (!empty($C007['C007_Email_Assinatura']) && trim($C007['C007_Email_Assinatura']) != '<br>') {
            $monta_['Corpo'] .= "<br /><br />{$C007['C007_Email_Assinatura']}";
        }
        $T117 = mysql_query("SELECT T007_Id,
                                    T007_Numero_Nota_Fiscal,
                                    T117_C004_Id,
                                    T117_D024_Id
                               FROM T117 
                                 LEFT JOIN T002 
                                        ON T002_T117_Id = T117_Id 
                                 LEFT JOIN T007 
                                        ON T007_Id = T002_T007_Id 
                                     WHERE T117_Id = '{$T117_Id}'");
        $T117 = mysql_fetch_array($T117);
        $file = $g['pathDadosAntigo'] . 'pdf/acp_' . $T117['T007_Numero_Nota_Fiscal'] . '.pdf';
        if (!file_exists($file)) {
            //Geramos o arquivo PDF ACP
            require_once('bibliotecas/classes/VEN003.php');
            $VEN003 = new VEN003();
            $file   = $VEN003->gerarACP($T117['T007_Id']);
        }
        
        if ($enviarACP === true && file_exists($file)) {
            $monta_['Anexos'][] = $file;
        }

        if (!$this->usarBoletoProtegido($T117['T117_C004_Id'])) {
            $montaPDF = $this->montarEmailPdf($T117_Id);
            if (is_array($montaPDF['Anexos'])) {
                $monta_['Anexos'][] = $montaPDF['Anexos'][0];
            }
        }
        
        $mail           = new \phpmailer;
        // Mail
        $mail->From     = $C007['C007_Email'];
        $mail->FromName = $C007['C007_Primeiro_Nome'];
        $mail->Host     = $C007['C007_Email_SMTP_Servidor'];
        $mail->Mailer   = "smtp";
        $mail->SMTPAuth = $C007['C007_Email_SMTP_Autenticacao'];
        $mail->Username = $C007['C007_Email_SMTP_Usuario'];
        $mail->Password = $C007['C007_Email_SMTP_Senha'];
        $mail->Subject  = '=?UTF-8?B?'.base64_encode($monta_['Assunto']).'?=';
        $mail->Body     = $monta_['Corpo'];
        $mail->AltBody  = "ALT BODY";
        $mail->CharSet  = 'utf-8';
        $mail->SetLanguage("br");
        $mail->IsHTML(true);
        if ($C007['C007_Email_SMTP_Servidor'] == "smtp.gmail.com")
        {
            $mail->SMTPSecure   = "ssl";
            $mail->Port         = "465";
        }
        if ($C007['C007_Email_SMTP_Servidor'] == "smtp.zoho.com")
        {
            $mail->SMTPSecurity = "ssl";
            $mail->SMTPSecure   = "ssl";
            $mail->Port         = "465";
        }
        if ($C007['C007_Email_SMTP_Servidor'] == "smtp.live.com")
        {
            $mail->SMTPSecure   = "ssl";
        }
        if ($C007['C007_Email_SMTP_Servidor'] == "smtp.office365.com")
        {
            $mail->SMTPSecure   = "tls";
            $mail->SMTPSecurity = 'tls';
        }
        if (strpos($C007['C007_Email_SMTP_Servidor'], "istyle.com.br") !== false)
        {
            $mail->SMTPSecure   = "tls";
        }
        if (!empty($C007['C007_Email_SMTP_Porta']))
        {
            $mail->Port         = $C007['C007_Email_SMTP_Porta']; 
        }
        if (strpos($C007['C007_Email_SMTP_Servidor'], 'yahoo') !== false || strpos($C007['C007_Email_SMTP_Servidor'], 'comercialdex') !== false)
        {
            $mail->SMTPSecure   = "tls";
        }
        if(strpos($C007['C007_Email_SMTP_Servidor'], 'gpimports') !== false or strpos($C007['C007_Email_SMTP_Servidor'], '.gp') !== false) {
                $mail->SMTPSecure = "tls";
                $mail->SMTPSecurity = 'tls';
                //$mail->Port = '465';
        }
        if ($C007['C007_Email']=='nfe@hardness.com.br') {
                $mail->SMTPSecure = "ssl";
        }

        switch ($C007['C007_Email_SMTP_Seguranca']) {
            case 1:
                $mail->SMTPSecure   = "ssl";
                $mail->SMTPSecurity = 'ssl';
                break;
            case 2:
                $mail->SMTPSecure   = "tls";
                $mail->SMTPSecurity = 'tls';
                break;
        }

        if (empty($monta_['Para']) && !empty($monta_['cc'])) {
            $monta_['Para'] = $monta_['cc'];
            $monta_['cc']   = '';
        }
        $todosEmailsEnviados = array();
        $mPara               = preg_split("/[;,]+/", $monta_['Para']);
        for ($x = 0; $x < count($mPara); $x++) {
            $email = $mPara[$x];
            if (strpos($email, '<') !== false) {
                $email = explode('<', $email);
                $email = explode('>', $email[1]);
                $email = $email[0];
            }
            $todosEmailsEnviados[] = strtolower(trim($email));
            $mail->AddAddress($email);
        }
        if (isset($monta_['cc'])) {
            $mPara = preg_split("/[;,]+/", $monta_['cc']);
            for ($x = 0; $x < count($mPara); $x++) {
                $email = $mPara[$x];
                if (strpos($email, '<') !== false) {
                    $email = explode('<', $email);
                    $email = explode('>', $email[1]);
                    $email = $email[0];
                }
                $todosEmailsEnviados[] = strtolower(trim($email));
                $mail->AddCC($email);
            }
        }
        
        $anexosGravar   = array();
        $T139_Estrutura = array(
            'anexosManual' => array()
        );
        if (isset($monta_['Anexos'])) {
            foreach ($monta_['Anexos'] as $path) {
                $anexosGravar[]                   = $path;
                $T139_Estrutura['anexosManual'][] = basename($path);
                $mail->AddAttachment($path, basename($path));
            }
        }
        
        if ($mail->Send()) {
            mysqli_query("UPDATE T117 SET T117_Data_Envio_Email=now(), T117_Flag_Atualizar = 'N' WHERE T117_Id='{$T117_Id}'");
            
            // Gravar como e-mail enviado
            $T139_De        = mysql_real_escape_string("\"{$C007['C007_Primeiro_Nome']}\" <{$C007['C007_Email']}>");
            $T139_Estrutura = serialize($T139_Estrutura);
            $T139_Para      = !empty($monta_['cc']) ? ($monta_['Para'] . '; ' . $monta_['cc']) : $monta_['Para'];
            $sql            = "INSERT INTO T139 (T139_Pasta, T139_Uid, T139_Titulo, T139_De, T139_Para, T139_Data_Hora, T139_Estrutura, T139_Mensagem_Corpo, T139_Flag_Lido, T139_D024_Id, T139_C007_Id,T139_T007_Id) VALUES ('S', 0, '" . mysql_real_escape_string($monta_['Assunto']) . "', '{$T139_De}', '" . mysql_real_escape_string($T139_Para) . "', '" . date('Y-m-d H:i:s') . "', '" . mysql_real_escape_string($T139_Estrutura) . "', '" . mysql_real_escape_string($monta_['Corpo']) . "', '1', '" . mysql_real_escape_string($T117['T117_D024_Id']) . "', '" . mysql_real_escape_string($g['usuarioAtual']) . "', '" . mysql_real_escape_string($T117_Id) . "');";
            mysqli_query($sql);
            $g['mysqlLastId'] = mysql_insert_id();
            
            // Salvar anexos
            $dir = $g['pathDados'] . 'anexosEmail/';
            is_dir($dir) or mkdir($dir, 0777, true);
            foreach ($anexosGravar as $key => $anexo) {
                $arquivo = "{$dir}{$g['mysqlLastId']}-{$key}";
                copy($anexo, $arquivo);
            }

            if (isset($monta_['Anexos'])) {
                foreach ($monta_['Anexos'] as $path) {
                    gDeletarArquivo($path);
                }
            }

            return true;
        } else {
            $cErro=str_replace(array("'",'"',"\r","\n"),"",$mail->ErrorInfo);
            $cErro=str_replace(array("<p>","</p>"),"<br />",$cErro);
            $cErro=$cErro . ' ' . $C007['C007_Email_SMTP_Usuario'];
            return $cErro;
        }
    }
    
    /**
     * desfazerCancelar
     *
     * @param String $T117_Id
     * @param String $T002_Id
     * @return bool
     */     
    public function desfazerCancelar($T117_Id, $T002_Id)
    {
        mysql_query("UPDATE T117 SET T117_Flag_Cancelado='N' WHERE T117_Id='{$T117_Id}'");
        mysql_query("UPDATE T002 SET T002_T117_Id='{$T117_Id}' WHERE T002_Id='{$T002_Id}'");
        return true;
    }
    
    /**
     * atualizarVencimentoBoleto
     *
     * @param Form $form
     * @return bool
     */     
    public function atualizarVencimentoBoleto($form, $index = false)
    {
        global $g,$sys;
        if($index === true){
            //-- CONEXAO DIRETA COM O BANCO DE DADOS
            $con =@mysqli_connect($sys['dbHost'], $sys['dbUser'], $sys['dbPass'],$sys['dbDatabase']);
            // $dbs =@mysql_select_db($sys['dbDatabase'], $con) or die (mysql_error());
            $g['conexaoBanco'] = $con;
        }

        $T117_Id                              = $form->campoValorEnviado('T117_Id');
        $T117_Percentual_Multa_Atualizado     = gCorrigeNumeroInverte($form->campoValorEnviado('T117_Percentual_Multa'));
        $T117_Percentual_Juros_Dia_Atualizado = gCorrigeNumeroInverte($form->campoValorEnviado('T117_Percentual_Juros_Dia'));
        $T117_Data_Vencimento_Atualizado      = gCorrigeDataInverte($form->campoValorEnviado('T117_Data_Vencimento'));
        $T117_Data_Emissao_Atualizado         = gCorrigeDataInverte($form->campoValorEnviado('T117_Data_Emissao'));
        $T117_Valor_Total_Atualizado          = gCorrigeNumeroInverte($form->campoValorEnviado('T117_Valor_Total'));
        $gerarJurosMulta                      = $form->campoValorEnviado('gerarJurosMulta');
        $atualizarValor                       = $form->campoValorEnviado('atualizarValor');

        if(empty($gerarJurosMulta)){
            return "Informe o campo 'Gerar Juros e Multa'";
        } else if($gerarJurosMulta == 'S' && empty($atualizarValor)) {
            return "Informe o campo 'Atualizar c/base no'";
        }

        //T117 do boleto Vencido
        //$dataVencimentoComparativo = strtotime("-1 day", strtotime($T117_Data_Vencimento_Atualizado));
        //$dataVencimentoComparativo = date("Y-m-d", $dataVencimentoComparativo);

        $dataVencimentoComparativo = $T117_Data_Vencimento_Atualizado;
        $sqlT117  = mysql_query("SELECT *, DATEDIFF('{$dataVencimentoComparativo}', T117_Data_Vencimento) as diasVencido FROM T117 WHERE T117_Id = '{$T117_Id}'");
        $T117     = mysql_fetch_array($sqlT117);
        
        $sqlT002  = mysql_query("SELECT *, DATEDIFF('{$T117_Data_Vencimento_Atualizado}', T002_Data_Vencimento) as diasVencido 
                                   FROM T002 
                              LEFT JOIN T117 ON T002_T117_Id = T117_Id  OR T002_Id=T117_T002_Id 
                                  WHERE T117_Id = '{$T117_Id}'");
        $T002     = mysql_fetch_array($sqlT002);

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");


        if($T002['T117_T002_Id'] <= 0 &&  $T002['T002_T117_Id'] <= 0){
            return "O campo T117_T002_Id e o T002_T117_Id desse título está zerado ou em branco, informe o suporte técnico.";
        }
        
        //Busca o nosso numero e atualiza o valor no D007
        $sqlD007 = mysql_query("SELECT * FROM D007 WHERE D007_Id = '{$T117['T117_D007_Id']}'");
        $D007    = mysql_fetch_array($sqlD007);

        //Valida se o Novo Nosso Número já existe
        $validaNossoNumero = $D007['D007_Nosso_Numero'] + 1 ;
        $sqlValidaNossoNumero = "SELECT T117_Nosso_Numero
                                   FROM T117 
                              LEFT JOIN D007 ON T117_D007_Id = D007_Id
                                  WHERE T117_Nosso_Numero = '$validaNossoNumero'                               
                                    AND D007_Id='{$T002['T117_D007_Id']}'";
        $resultValidaNossoNumero = mysql_num_rows(mysql_query($sqlValidaNossoNumero));

        //-- Para qualquer banco:
        if (empty($D007['D007_Nosso_Numero'])) {
            $D007['D007_Nosso_Numero'] = 1;
        } else if(!empty($D007['D007_Nosso_Numero']) && ($resultValidaNossoNumero == 0)) {
            $D007['D007_Nosso_Numero']++;
        } else {
            return "Erro ao atualizar Nosso Número";
        }
        
        if ($g['C031']['atualizarNossoNumeroBoleto']  == 'S') {
            if($T002['T002_T035_Id'] > 0){
                return "<p style='line-height:1.5;'>A configuração de boleto está setada para <b>Atualizar nosso número do boleto</b>.<br>
                Não é possível gerar um novo NOSSO NÚMERO, pois esse boleto está registrado no banco na remessa <b>nº {$T002['T002_T035_Id']}.</b><br><br>
                Para realizar alterações será necessário baixá-lo no seu home banking, limpar o número da remessa no título a receber e tentar atualizar o vencimento novamente<br>
                ou alterar a configuração para não gerar um novo nosso número em <b>CONFIGURAÇÃO->OPÇÕES->FINANCEIRO->BOLETO->Atualizar nosso número do boleto.</b><br></p>";
            }
        

            //**********************************************************************************************************************************************
            //Duplica o registro do boleto no banco para alterar as informações posteriormente
/*             $sqlDuplicacao = gRetornaSqlDuplicacao('T117', $T117_Id);
            mysql_query($sqlDuplicacao);
            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return "ERRO: {$erro} Clausula: {$sqlDuplicacao}";
            } */

            $queryT117   = mysql_query("SELECT * FROM T117 WHERE T117_Id = '{$T117_Id}'");
            $dadosBoleto = mysql_fetch_assoc($queryT117);

            $camposT117  = "";
            $valoresT117 = "";
            foreach ($dadosBoleto as $key => $value) {
                if ($key == 'T117_Id') {
                    continue;
                } elseif($key == 'T117_Nosso_Numero'){  
                    continue; 
                } else {
                    $camposT117  .= $key . ", ";
                    $valoresT117 .= "'" . mysql_real_escape_string($value) . "',";
                }
            }
            $camposT117  = trim($camposT117, ', ');
            $valoresT117 = trim($valoresT117, ',');
    
            $insertT117 = "INSERT INTO T117 ({$camposT117}) VALUES ({$valoresT117})";
            $queryT117  = mysql_query($insertT117);
            $error = mysql_error();
            if (!empty($error)) {
                mysql_query("ROLLBACK");
                return "Erro ao duplicar boleto: " . $error. " SQL: " . $insertT117;
            }

            $T117_Id_Atualizado = $g['mysqlLastId'];
            
            $cClausula = " UPDATE D007 SET ";
            $cClausula .= " D007_Nosso_Numero='{$D007['D007_Nosso_Numero']}' ";
            $cClausula .= " WHERE D007_Id='{$D007['D007_Id']}' ";
            $cSql_D007 = mysql_query($cClausula);
            $cErro     = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "ERRO: $cErro Clausula: $cClausula";
            }
            //Cancela o boleto vencido
            $sqlCancelamento = "UPDATE T117 SET T117_Flag_Cancelado = 'S' WHERE T117_Id = '{$T117_Id}'";
            mysql_query($sqlCancelamento);
            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return "ERRO: {$erro} Clausula: {$sqlCancelamento}";
            }
            //Atualiza a duplicata com o boleto gerado
            $sqlAtualizaDuplicata = "UPDATE T002 SET T002_T117_Id = '{$T117_Id_Atualizado}' WHERE T002_T117_Id = '{$T117_Id}'";
            mysql_query($sqlAtualizaDuplicata);
            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return "ERRO: {$erro} Clausula: {$sqlAtualizaDuplicata}";
            }
            //**********************************************************************************************************************************************
        } else {
            $T117_Id_Atualizado = $T117_Id;
        }
       

        log("update T117");

        //Atualiza a data de vencimento do boleto e os valor de Juros e Multa
        $sqlAtualizaBoleto = "UPDATE T117 SET
                                     T117_Percentual_Multa = '{$T117_Percentual_Multa_Atualizado}',
                                     T117_Percentual_Juros_Dia = '{$T117_Percentual_Juros_Dia_Atualizado}',
									 T117_Data_Vencimento = '{$T117_Data_Vencimento_Atualizado}',
                                     T117_Data_Emissao    = '{$T117_Data_Emissao_Atualizado}',
									 T117_Flag_Atualizar  = 'N'
							   WHERE T117_Id = '{$T117_Id_Atualizado}'";

        mysql_query($sqlAtualizaBoleto);
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "ERRO: {$erro} Clausula: {$sqlAtualizaBoleto}";
        }

        //Atualiza o valor 
/*        if ($T117_Valor_Total_Atualizado > 0) {
            $sqlAtualizaValorBoleto = "UPDATE T117 SET T117_Valor_Total = '{$T117_Valor_Total_Atualizado}' WHERE T117_Id = '{$T117_Id_Atualizado}'";
            mysql_query($sqlAtualizaValorBoleto);
            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return "ERRO: {$erro} Clausula: {$sqlAtualizaValorBoleto}";
            }
        } */
        
        if ($atualizarValor == '2') { // Base valor boleto
            $valorTotal      = $T002['T002_Valor_Total'];
            $diasVencido     = $T002['diasVencido'];
        } else {                    // base valor titulo
            $valorTotal      = $T117_Valor_Total_Atualizado;
            $diasVencido     = $T117['diasVencido'];
        }        
        
        if ($gerarJurosMulta == 'S') {
            //Atualiza o valor caso o boleto vencido tenha juros ou multa
            $jurosDiario     = (($T117_Percentual_Juros_Dia_Atualizado / 30) / 100) * $T002['T002_Valor_Total'];
            $multaVencimento = ($T117_Percentual_Multa_Atualizado / 100) * $T002['T002_Valor_Total'];

            if ($multaVencimento > 0 && $T117_Data_Vencimento_Atualizado > $T002['T002_Data_Vencimento']) {
                $valorTotal += $multaVencimento;
            }

            if ($jurosDiario > 0) {
                $jurosDiarioTotal = $jurosDiario * $diasVencido;
                $valorTotal += $jurosDiarioTotal;
            }
        }
        
        $sqlAtualizaValorBoleto = "UPDATE T117 SET T117_Valor_Total = '{$valorTotal}' WHERE T117_Id = '{$T117_Id_Atualizado}'";
        mysql_query($sqlAtualizaValorBoleto);
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "ERRO: {$erro} Clausula: {$sqlAtualizaValorBoleto}";
        }

        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");

        $sqlD007              = mysql_query("SELECT * FROM T117 
								LEFT JOIN D007 ON D007_Id = T117_D007_Id 
								LEFT JOIN D062 ON D062_Id=D007_D062_Id 
								LEFT JOIN T002 ON T002_T117_Id = T117_Id
								WHERE T117_Id = '{$T117_Id_Atualizado}'");
        $mD007                = mysql_fetch_array($sqlD007);
        $T117_Data_Vencimento = $mD007['T117_Data_Vencimento'];
        $nValor_Total         = $mD007['T117_Valor_Total'];
        $nT117_Id             = $T117_Id_Atualizado;


        if ($g['C031']['atualizarNossoNumeroBoleto']  != 'S') {
            $mD007['D007_Nosso_Numero'] = $mD007['T117_Nosso_Numero'];
            if ($mD007['D062_Numero_Banco']=='085'){
                $mD007['D007_Nosso_Numero'] = floor(substr($mD007['D007_Nosso_Numero'],-6));
            }
            log($mD007['D007_Nosso_Numero']);
        }

        chdir($g['pathRaiz'] . 'hardness/boleto/');
        include('./include/gerar_' . $mD007['D062_Numero_Banco'] . '.php');
        chdir($g['pathRaiz']);
                
        if ($atualizarNossoNumero == 'N') {
            mysql_query("UPDATE T117 SET T117_Nosso_Numero = '{$T002['T117_Nosso_Numero']}' WHERE T117_Id = '{$nT117_Id}'");
        }

        return true;
    }
    
    /**
     * atualizarVencimentoBoletosMarcados
     *
     * @param Form $form
     * @param String $extra
     * @return String/bool
     */     
    public function atualizarVencimentoBoletosMarcados($form, $extra)
    {
        global $g;
        $extra = base64_decode($extra);
        $extra = gInsertExtraWhere($extra, "T117_Flag_Atualizar = 'S'");
        
        $array                              = array();
        $array['gerarJurosMulta']           = $form->campoValorEnviado('gerarJurosMulta');
        $array['atualizarValor']            = $form->campoValorEnviado('atualizarValor');
        $array['T117_Percentual_Multa']     = $form->campoValorEnviado('T117_Percentual_Multa');
        $array['T117_Percentual_Juros_Dia'] = $form->campoValorEnviado('T117_Percentual_Juros_Dia');
        $array['T117_Data_Vencimento']      = $form->campoValorEnviado('T117_Data_Vencimento');
        $array['T117_Valor_Total']          = $form->campoValorEnviado('T117_Valor_Total');
        
        require_once('bibliotecas/classes/GFormWrap.php');
        $sqlT117 = mysql_query("SELECT * FROM T117 {$extra}");
        while ($T117 = mysql_fetch_array($sqlT117)) {
            $array['T117_Id'] = $T117['T117_Id'];
            $formulario       = new GFormWrap($array);
            $retorno          = $this->atualizarVencimentoBoleto($formulario);
            if ($retorno !== true) {
                return $retorno;
            }
            
            mysql_query("UPDATE T117 SET T117_Flag_Atualizar = 'N' WHERE T117_Id = '{$T117['T117_Id']}'");
        }
        return true;
    }

    /**
     * enviarMultiEmailBoleto
     *
     * @param String $T117_Id
     * @param bool $enviarDanfe
     * @return bool
     */     
    public function enviarMultiEmailBoletoCobranca($T117_Id, $enviarDanfe = false)
    {

        global $g;
        require_once("bibliotecas/phpmailer/class.phpmailer.php");
        $C007 = mysql_query("SELECT * FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}';");
        $C007 = mysql_fetch_assoc($C007);
        if (empty($C007['C007_Email'])) {
                $C007['C007_Email'] = 'nfe@hardness.com.br';
                $C007['C007_Primeiro_Nome'] = $C004['C004_Nome_Abreviado'];
                $C007['C007_Email_SMTP_Senha'] = 'hgrw nvpe xliv cluz';
                $C007['C007_Email_SMTP_Servidor'] = 'smtp.gmail.com';
                $C007['C007_Email_SMTP_Autenticacao'] = 'true';
                $C007['C007_Email_SMTP_Usuario'] = 'nfe@hardness.com.br';
                $C007['C007_Email_SMTP_Porta'] = '465';
        }
        if (empty($C007) || empty($C007['C007_Email_SMTP_Servidor'])) {
            return "Seu email de usuário deve estar configurado, favor verificar.";
        }
        $monta_           = $this->montarEmailCobranca($T117_Id);
        $monta_['Anexos'] = array();
        if (empty($monta_['Para'])) {
            return "Email do cadastro do cliente deve estar preenchido.";
        }
        
        if (!empty($C007['C007_Email_Assinatura']) && trim($C007['C007_Email_Assinatura']) != '<br>') {
            $monta_['Corpo'] .= "<br /><br />{$C007['C007_Email_Assinatura']}";
        }
        $T117 = mysql_query("SELECT * FROM T117 LEFT JOIN T002 ON T002_T117_Id = T117_Id LEFT JOIN T007 ON T007_Id = T002_T007_Id WHERE T117_Id = '{$T117_Id}'");
        $T117 = mysql_fetch_array($T117);
        
        $file = $g['pathDadosAntigo'] . 'nfe/danfe/danfe_' . $T117['T007_Numero_Protocolo_Nfe'] . '.pdf';
        if (!file_exists($file)) {
            //Geramos o arquivo PDF das notas fiscais
            require_once('bibliotecas/classes/VEN003.php');
            $VEN003 = new VEN003;
            $cAno_Mes_NFe  = substr($T117['T007_Data_Emissao'], 0, 7);
            $Diretorio_NFe = $g['pathDadosAntigo'] . 'nfe';
            $cArquivo      = "$Diretorio_NFe/xml/autorizado/{$cAno_Mes_NFe}/" . $T117['T007_Numero_Protocolo_Nfe'] . ".xml";
            $file          = $VEN003->gerarDanfe($T117['T007_Id'], $cArquivo);
        }
        
        if ($enviarDanfe === true && file_exists($file)) {
            $monta_['Anexos'][] = $file;
        }
        
        if (!$this->usarBoletoProtegido($T117['T117_C004_Id'])) {
            $montaPDF = $this->montarEmailPdf($T117_Id);
            if (is_array($montaPDF['Anexos'])) {
                $monta_['Anexos'][] = $montaPDF['Anexos'][0];
            }
        }
        
        $mail           = new \phpmailer;
        // Mail
        $mail->From     = $C007['C007_Email'];
        $mail->FromName = $C007['C007_Primeiro_Nome'];
        $mail->Host     = $C007['C007_Email_SMTP_Servidor'];
        $mail->Mailer   = "smtp";
        $mail->SMTPAuth = $C007['C007_Email_SMTP_Autenticacao'];
        $mail->Username = $C007['C007_Email_SMTP_Usuario'];
        $mail->Password = $C007['C007_Email_SMTP_Senha'];
        $mail->Subject  = '=?UTF-8?B?'.base64_encode($monta_['Assunto']).'?=';
        $mail->Body     = $monta_['Corpo'];
        $mail->AltBody  = "ALT BODY";
        $mail->CharSet  = 'utf-8';
        $mail->SetLanguage("br");
        $mail->IsHTML(true);
        if ($C007['C007_Email_SMTP_Servidor'] == "smtp.gmail.com")
        {
            $mail->SMTPSecure   = "ssl";
            $mail->Port         = "465";
        }
        if ($C007['C007_Email_SMTP_Servidor'] == "smtp.zoho.com")
        {
            $mail->SMTPSecurity = "ssl";
            $mail->SMTPSecure   = "ssl";
            $mail->Port         = "465";
        }
        if ($C007['C007_Email_SMTP_Servidor'] == "smtp.live.com")
        {
            $mail->SMTPSecure   = "ssl";
        }
        if ($C007['C007_Email_SMTP_Servidor'] == "smtp.office365.com")
        {
            $mail->SMTPSecure   = "tls";
            $mail->SMTPSecurity = 'tls';
        }
        if (strpos($C007['C007_Email_SMTP_Servidor'], "istyle.com.br") !== false)
        {
            $mail->SMTPSecure   = "tls";
        }
        if (!empty($C007['C007_Email_SMTP_Porta']))
        {
            $mail->Port         = $C007['C007_Email_SMTP_Porta']; 
        }
        if (strpos($C007['C007_Email_SMTP_Servidor'], 'yahoo') !== false || strpos($C007['C007_Email_SMTP_Servidor'], 'comercialdex') !== false)
        {
            $mail->SMTPSecure   = "tls";
        }
        if(strpos($C007['C007_Email_SMTP_Servidor'], 'gpimports') !== false or strpos($C007['C007_Email_SMTP_Servidor'], '.gp') !== false) {
                $mail->SMTPSecure = "tls";
                $mail->SMTPSecurity = 'tls';
                //$mail->Port = '465';
        }
        if ($C007['C007_Email']=='nfe@hardness.com.br') {
                $mail->SMTPSecure = "ssl";
        }

        switch ($C007['C007_Email_SMTP_Seguranca']) {
            case 1:
                $mail->SMTPSecure   = "ssl";
                $mail->SMTPSecurity = 'ssl';
                break;
            case 2:
                $mail->SMTPSecure   = "tls";
                $mail->SMTPSecurity = 'tls';
                break;
        }

        if (empty($monta_['Para']) && !empty($monta_['cc'])) {
            $monta_['Para'] = $monta_['cc'];
            $monta_['cc']   = '';
        }
        $todosEmailsEnviados = array();
        $mPara               = preg_split("/[;,]+/", $monta_['Para']);
        for ($x = 0; $x < count($mPara); $x++) {
            $email = $mPara[$x];
            if (strpos($email, '<') !== false) {
                $email = explode('<', $email);
                $email = explode('>', $email[1]);
                $email = $email[0];
            }
            $todosEmailsEnviados[] = strtolower(trim($email));
            $mail->AddAddress($email);
        }
        if (isset($monta_['cc'])) {
            $mPara = preg_split("/[;,]+/", $monta_['cc']);
            for ($x = 0; $x < count($mPara); $x++) {
                $email = $mPara[$x];
                if (strpos($email, '<') !== false) {
                    $email = explode('<', $email);
                    $email = explode('>', $email[1]);
                    $email = $email[0];
                }
                $todosEmailsEnviados[] = strtolower(trim($email));
                $mail->AddCC($email);
            }
        }
        
        $anexosGravar   = array();
        $T139_Estrutura = array(
            'anexosManual' => array()
        );
        if (isset($monta_['Anexos'])) {
            foreach ($monta_['Anexos'] as $path) {
                $anexosGravar[]                   = $path;
                $T139_Estrutura['anexosManual'][] = basename($path);
                $mail->AddAttachment($path, basename($path));
            }
        }
        
        if ($mail->Send()) {
            mysql_query("UPDATE T117 SET T117_Data_Envio_Email=now(), T117_Flag_Atualizar = 'N' WHERE T117_Id='{$T117_Id}'");
            
            // Gravar como e-mail enviado
            $T139_De        = mysql_real_escape_string("\"{$C007['C007_Primeiro_Nome']}\" <{$C007['C007_Email']}>");
            $T139_Estrutura = serialize($T139_Estrutura);
            $T139_Para      = !empty($monta_['cc']) ? ($monta_['Para'] . '; ' . $monta_['cc']) : $monta_['Para'];
            $sql            = "INSERT INTO T139 (T139_Pasta, T139_Uid, T139_Titulo, T139_De, T139_Para, T139_Data_Hora, T139_Estrutura, T139_Mensagem_Corpo, T139_Flag_Lido, T139_D024_Id, T139_C007_Id,T139_T007_Id) VALUES ('S', 0, '" . mysql_real_escape_string($monta_['Assunto']) . "', '{$T139_De}', '" . mysql_real_escape_string($T139_Para) . "', '" . date('Y-m-d H:i:s') . "', '" . mysql_real_escape_string($T139_Estrutura) . "', '" . mysql_real_escape_string($monta_['Corpo']) . "', '1', '" . mysql_real_escape_string($T117['T117_D024_Id']) . "', '" . mysql_real_escape_string($g['usuarioAtual']) . "', '" . mysql_real_escape_string($T117_Id) . "');";
            mysqli_query($sql);
            $g['mysqlLastId'] = mysql_insert_id();
            
            // Salvar anexos
            $dir = $g['pathDados'] . 'anexosEmail/';
            is_dir($dir) or mkdir($dir, 0777, true);
            foreach ($anexosGravar as $key => $anexo) {
                $arquivo = "{$dir}{$g['mysqlLastId']}-{$key}";
                copy($anexo, $arquivo);
            }

            if (isset($monta_['Anexos'])) {
                foreach ($monta_['Anexos'] as $path) {
                    gDeletarArquivo($path);
                }
            }

            return true;
        } else {
            $cErro=str_replace(array("'",'"',"\r","\n"),"",$mail->ErrorInfo);
            $cErro=str_replace(array("<p>","</p>"),"<br />",$cErro);
            $cErro=$cErro . ' ' . $C007['C007_Email_SMTP_Usuario'];
            return $cErro;
        }
    
    }

    /**
     * enviarMultiEmailBoleto
     *
     * @param String $T117_Id
     * @param bool $enviarDanfe
     * @return bool
     */     
    public function enviarMultiEmailBoletoLembrete($T117_Id, $enviarDanfe = false){

        global $g;
        $C007 = mysql_query("SELECT * FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}';");
        $C007 = mysql_fetch_assoc($C007);
        if (empty($C007['C007_Email'])) {
                $C007['C007_Email'] = 'nfe@hardness.com.br';
                $C007['C007_Primeiro_Nome'] = $C004['C004_Nome_Abreviado'];
                $C007['C007_Email_SMTP_Senha'] = 'hgrw nvpe xliv cluz';
                $C007['C007_Email_SMTP_Servidor'] = 'smtp.gmail.com';
                $C007['C007_Email_SMTP_Autenticacao'] = 'true';
                $C007['C007_Email_SMTP_Usuario'] = 'nfe@hardness.com.br';
                $C007['C007_Email_SMTP_Porta'] = '465';
        }
        if (empty($C007) || empty($C007['C007_Email_SMTP_Servidor'])) {
            return "Seu email de usuário deve estar configurado, favor verificar.";
        }
        $monta_           = $this->montarEmailLembrete($T117_Id);
        $monta_['Anexos'] = array();
        if (!empty($monta_['erro'])) {
            return $monta_['erro'];
        }
        if (empty($monta_['Para'])) {
            return "Email do cadastro do cliente deve estar preenchido.";
        }
        
        if (!empty($C007['C007_Email_Assinatura']) && trim($C007['C007_Email_Assinatura']) != '<br>') {
            $monta_['Corpo'] .= "<br /><br />{$C007['C007_Email_Assinatura']}";
        }
        $T117 = mysql_query("SELECT * FROM T117 LEFT JOIN T002 ON T002_T117_Id = T117_Id LEFT JOIN T007 ON T007_Id = T002_T007_Id WHERE T117_Id = '{$T117_Id}'");
        $T117 = mysql_fetch_array($T117);
        
        $file = $g['pathDadosAntigo'] . 'nfe/danfe/danfe_' . $T117['T007_Numero_Protocolo_Nfe'] . '.pdf';
         if (!file_exists($file)) {
            //Geramos o arquivo PDF das notas fiscais
            require_once('bibliotecas/classes/VEN003.php');
            $VEN003 = new VEN003;
            $cAno_Mes_NFe  = substr($T117['T007_Data_Emissao'], 0, 7);
            $Diretorio_NFe = $g['pathDadosAntigo'] . 'nfe';
            $cArquivo      = "$Diretorio_NFe/xml/autorizado/{$cAno_Mes_NFe}/" . $T117['T007_Numero_Protocolo_Nfe'] . ".xml";
            $file          = $VEN003->gerarDanfe($T117['T007_Id'], $cArquivo);
        } 
        
        if ($enviarDanfe === true && file_exists($file)) {
            $monta_['Anexos'][] = $file;
        }

        if (!$this->usarBoletoProtegido($T117['T117_C004_Id'])) {
            $montaPDF = $this->montarEmailPdf($T117_Id);
            if (is_array($montaPDF['Anexos'])) {
                $monta_['Anexos'][] = $montaPDF['Anexos'][0];
            }
        }

        require_once("bibliotecas/phpmailer/class.phpmailer.php");
        $mail           = new \phpmailer;
        // Mail
        $mail->From     = $C007['C007_Email'];
        $mail->FromName = $C007['C007_Primeiro_Nome'];
        $mail->Host     = $C007['C007_Email_SMTP_Servidor'];
        $mail->Mailer   = "smtp";
        $mail->SMTPAuth = $C007['C007_Email_SMTP_Autenticacao'];
        $mail->Username = $C007['C007_Email_SMTP_Usuario'];
        $mail->Password = $C007['C007_Email_SMTP_Senha'];
        $mail->Subject  = '=?UTF-8?B?'.base64_encode($monta_['Assunto']).'?=';
        $mail->Body     = $monta_['Corpo'];
        $mail->AltBody  = "ALT BODY";
        $mail->CharSet  = 'utf-8';
        $mail->SetLanguage("br");
        $mail->IsHTML(true);
        if ($C007['C007_Email_SMTP_Servidor'] == "smtp.gmail.com")
        {
            $mail->SMTPSecure   = "ssl";
            $mail->Port         = "465";
        }
        if ($C007['C007_Email_SMTP_Servidor'] == "smtp.zoho.com")
        {
            $mail->SMTPSecurity = "ssl";
            $mail->SMTPSecure   = "ssl";
            $mail->Port         = "465";
        }
        if ($C007['C007_Email_SMTP_Servidor'] == "smtp.live.com")
        {
            $mail->SMTPSecure   = "ssl";
        }
        if ($C007['C007_Email_SMTP_Servidor'] == "smtp.office365.com")
        {
            $mail->SMTPSecure   = "tls";
            $mail->SMTPSecurity = 'tls';
        }
        if (strpos($C007['C007_Email_SMTP_Servidor'], "istyle.com.br") !== false)
        {
            $mail->SMTPSecure   = "tls";
        }
        if (!empty($C007['C007_Email_SMTP_Porta']))
        {
            $mail->Port         = $C007['C007_Email_SMTP_Porta']; 
        }
        if (strpos($C007['C007_Email_SMTP_Servidor'], 'yahoo') !== false || strpos($C007['C007_Email_SMTP_Servidor'], 'comercialdex') !== false)
        {
            $mail->SMTPSecure   = "tls";
        }
        if(strpos($C007['C007_Email_SMTP_Servidor'], 'gpimports') !== false or strpos($C007['C007_Email_SMTP_Servidor'], '.gp') !== false) {
                $mail->SMTPSecure = "tls";
                $mail->SMTPSecurity = 'tls';
                //$mail->Port = '465';
        }
        if ($C007['C007_Email']=='nfe@hardness.com.br') {
                $mail->SMTPSecure = "ssl";
        }

        switch ($C007['C007_Email_SMTP_Seguranca']) {
            case 1:
                $mail->SMTPSecure   = "ssl";
                $mail->SMTPSecurity = 'ssl';
                break;
            case 2:
                $mail->SMTPSecure   = "tls";
                $mail->SMTPSecurity = 'tls';
                break;
        }

        if (empty($monta_['Para']) && !empty($monta_['cc'])) {
            $monta_['Para'] = $monta_['cc'];
            $monta_['cc']   = '';
        }
        $todosEmailsEnviados = array();
        $mPara               = preg_split("/[;,]+/", $monta_['Para']);
        for ($x = 0; $x < count($mPara); $x++) {
            $email = $mPara[$x];
            if (strpos($email, '<') !== false) {
                $email = explode('<', $email);
                $email = explode('>', $email[1]);
                $email = $email[0];
            }
            $todosEmailsEnviados[] = strtolower(trim($email));
            $mail->AddAddress($email);
        }
        if (isset($monta_['cc'])) {
            $mPara = preg_split("/[;,]+/", $monta_['cc']);
            for ($x = 0; $x < count($mPara); $x++) {
                $email = $mPara[$x];
                if (strpos($email, '<') !== false) {
                    $email = explode('<', $email);
                    $email = explode('>', $email[1]);
                    $email = $email[0];
                }
                $todosEmailsEnviados[] = strtolower(trim($email));
                $mail->AddCC($email);
            }
        }
        
        $anexosGravar   = array();
        $T139_Estrutura = array(
            'anexosManual' => array()
        );
        if (isset($monta_['Anexos'])) {
            foreach ($monta_['Anexos'] as $path) {
                $anexosGravar[]                   = $path;
                $T139_Estrutura['anexosManual'][] = basename($path);
                $mail->AddAttachment($path, basename($path));
            }
        }
        
        if ($mail->Send()) {
            mysql_query("UPDATE T117 SET T117_Data_Envio_Email=now(), T117_Flag_Atualizar = 'N' WHERE T117_Id='{$T117_Id}'");
            
            // Gravar como e-mail enviado
            $T139_De        = mysql_real_escape_string("\"{$C007['C007_Primeiro_Nome']}\" <{$C007['C007_Email']}>");
            $T139_Estrutura = serialize($T139_Estrutura);
            $T139_Para      = !empty($monta_['cc']) ? ($monta_['Para'] . '; ' . $monta_['cc']) : $monta_['Para'];
            $sql            = "INSERT INTO T139 (T139_Pasta, T139_Uid, T139_Titulo, T139_De, T139_Para, T139_Data_Hora, T139_Estrutura, T139_Mensagem_Corpo, T139_Flag_Lido, T139_D024_Id, T139_C007_Id,T139_T007_Id) VALUES ('S', 0, '" . mysql_real_escape_string($monta_['Assunto']) . "', '{$T139_De}', '" . mysql_real_escape_string($T139_Para) . "', '" . date('Y-m-d H:i:s') . "', '" . mysql_real_escape_string($T139_Estrutura) . "', '" . mysql_real_escape_string($monta_['Corpo']) . "', '1', '" . mysql_real_escape_string($T117['T117_D024_Id']) . "', '" . mysql_real_escape_string($g['usuarioAtual']) . "', '" . mysql_real_escape_string($T117_Id) . "');";
            mysqli_query($sql);
            $g['mysqlLastId'] = mysql_insert_id();
            
            // Salvar anexos
            $dir = $g['pathDados'] . 'anexosEmail/';
            is_dir($dir) or mkdir($dir, 0777, true);
            foreach ($anexosGravar as $key => $anexo) {
                $arquivo = "{$dir}{$g['mysqlLastId']}-{$key}";
                copy($anexo, $arquivo);
            }

            if (isset($monta_['Anexos'])) {
                foreach ($monta_['Anexos'] as $path) {
                    gDeletarArquivo($path);
                }
            }

            return true;
        } else {
            $cErro=str_replace(array("'",'"',"\r","\n"),"",$mail->ErrorInfo);
            $cErro=str_replace(array("<p>","</p>"),"<br />",$cErro);
            $cErro=$cErro . ' ' . $C007['C007_Email_SMTP_Usuario'];
            return $cErro;
        }
    }

        /**
     * montarEmail
     *
     * @param String $T117_Id
     * @return array
     */     
    public function montarEmailLembrete($T117_Id)
    {
        global $g, $confUsuario;
        $T117_Id     = mysql_real_escape_string($T117_Id);
        $sql         = "SELECT T117_Data_Vencimento,
                               T117_Id,
                               T117_D024_Id,
                               D024_E_Mail_Boleto,
                               D024_E_Mail,
                               T007_Numero_Nota_Fiscal 
                          FROM T117 
                     LEFT JOIN D024 ON D024_Id=T117_D024_Id 
                     LEFT JOIN T002 ON T117_Id=T002_T117_Id
                     LEFT JOIN T007 ON T007_Id=T002_T007_Id
                         WHERE T117_Id='{$T117_Id}'";
        $Consulta    = mysql_query($sql);
        $Consulta    = mysql_fetch_array($Consulta);
        $array['cc'] = '';

        $erro = mysql_error();
        if (!empty($erro)) {
            $array['erro'] = $erro;
        }
               
        $sql          = "SELECT C004_Nome_Empresa,C004_Site,C004_Logo_Marca,C004_Nome_Abreviado FROM C004 WHERE C004_Id='{$g['empresaAtual']}'";
        $empresaAtual = mysql_query($sql);
        $empresaAtual = mysql_fetch_array($empresaAtual);
        
        $dataVencimento = gCorrigeData($Consulta['T117_Data_Vencimento']);
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
        }
        
        $array['Para']    = (!empty($Consulta['D024_E_Mail_Boleto'])) ? $Consulta['D024_E_Mail_Boleto'] : $Consulta['D024_E_Mail'];
        $array['Assunto'] = "Lembrete de vencimento";

        $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
        $caminhoLogo   = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";

        $dadosEmpresa = <<<EOT
                <b>{$g['C004']['C004_Nome_Empresa']}</b><br />
                {$g['C004']['C004_Logradouro']}, {$g['C004']['C004_Numero']} {$g['C004']['C004_Complemento']}<br />
                {$g['C004']['C004_Cep']} - {$g['C004']['D020_Nome_Cidade']} - {$g['C004']['D018_UF']}<br />
                Fone: {$g['C004']['C004_Telefone']}<br />
                {$g['C004']['C004_Site']}<br />
EOT;

        $mensagemNota = '';
        if(!empty($Consulta['T007_Numero_Nota_Fiscal'])){
            $mensagemNota = 'referente a NFe '. $Consulta['T007_Numero_Nota_Fiscal'];
        }

$array['Corpo'] = <<<EOT
        '<div style="font-family:arial,verdana;font-size:10px;font-weight:bold;float: left; padding:10px">
            <img style="float:left; margin-right:10px;" src="$caminhoLogo" />
        </div>
        <div style="font-family:arial,verdana;font-size:10px;font-weight:bold;float: left;padding:10px">
            {$dadosEmpresa}
        </div>
        <div style="clear:both;padding:10px">
            Olá! Viemos lembrar do vencimento do seu título para data {$dataVencimento} {$mensagemNota}. <br />Caso o pagamento já tenha sido efetuado favor desconsiderar este e-mail. <br /><br />
            <img style="float:left; margin-right:10px;" src="http://nfe.hardness.com.br/hardness/imagens/jpg/boleto.jpg" />
            <span style="font-size:14px">
                Segue boleto anexo. <br />
                Vencimento em <b>{$dataVencimento}</b><br />
            </span>
        </div>
        <div style="clear:both;padding:10px">
            <span style="font-size:14px">
                O pagamento pode ser realizado pela internet ou em qualquer agência bancária até a data do vencimento.<br /> <br />
                Att.<br /><br />
            </span>
        </div>
        <div style="font-size:10px">
            E-mail enviado por <br /> {$empresaAtual['C004_Nome_Empresa']} <br /> <a href="{$empresaAtual['C004_Site']}">{$empresaAtual['C004_Site']}<a ></a>
        </div>
EOT;

        if ($this->usarBoletoProtegido($Consulta['T117_C004_Id'])) {
            $linkBoleto = $this->montarLinkBoletoProtegido($Consulta['T117_Id'], $Consulta['T117_D024_Id']);
            $array['Corpo'] = <<<EOT
        '<div style="font-family:arial,verdana;font-size:10px;font-weight:bold;float: left; padding:10px">
            <img style="float:left; margin-right:10px;" src="$caminhoLogo" />
        </div>
        <div style="font-family:arial,verdana;font-size:10px;font-weight:bold;float: left;padding:10px">
            {$dadosEmpresa}
        </div>
        <div style="clear:both;padding:10px">
            Olá! Viemos lembrar do vencimento do seu título para data {$dataVencimento} {$mensagemNota}. <br />Caso o pagamento já tenha sido efetuado favor desconsiderar este e-mail. <br /><br />
            <img style="float:left; margin-right:10px;" src="http://nfe.hardness.com.br/hardness/imagens/jpg/boleto.jpg" />
            <span style="font-size:14px">
                Para visualizar o boleto, utilize o link abaixo. <br />
                Vencimento em <b>{$dataVencimento}</b><br />
            </span>
        </div>
        <div style="clear:both;padding:10px">
            <a target="_blank" href="{$linkBoleto}" style="color:#1f6feb;font-size:14px;font-weight:bold;text-decoration:none;">
                Abrir boleto com validação de CPF/CNPJ
            </a>
        </div>
        <div style="clear:both;padding:10px">
            <span style="font-size:14px">
                O pagamento pode ser realizado pela internet ou em qualquer agência bancária até a data do vencimento.<br /> <br />
                Att.<br /><br />
            </span>
        </div>
        <div style="font-size:10px">
            E-mail enviado por <br /> {$empresaAtual['C004_Nome_Empresa']} <br /> <a href="{$empresaAtual['C004_Site']}">{$empresaAtual['C004_Site']}<a ></a>
        </div>
EOT;
        }

        return $array;   
    }

     /**
     * montarEmail
     *
     * @param String $T117_Id
     * @return array
     */     
    public function montarEmailCobranca($T117_Id)
    {
        global $g, $confUsuario;
        $T117_Id     = mysql_real_escape_string($T117_Id);
        $sql         = <<<EOT
        SELECT *,datediff(if(T002_Data_Recebimento='0000-00-00',curdate(),T002_Data_Recebimento),T002_Data_Vencimento) as Atraso FROM T117 
        LEFT JOIN D024 ON D024_Id=T117_D024_Id 
        LEFT JOIN T002 ON T002_T117_Id=T117_Id 
        LEFT JOIN T007 ON T007_Id=T002_T007_Id
        WHERE T117_Id='{$T117_Id}';
EOT;
        $Consulta    = mysql_query($sql);
        $Consulta    = mysql_fetch_array($Consulta);
        $array['cc'] = '';
        /*$ccEmail = mysql_query("SELECT * FROM D013 WHERE D013_D024_Id = '{$Consulta['T117_D024_Id']}'");
        while($resCC = mysql_fetch_array($ccEmail)){
        $array['cc'] .= $resCC['D013_E_Mail'].';';
        }
        $array['cc'] = substr($array['cc'], 0, -1);*/
        
        $sql          = "SELECT C004_Nome_Empresa,C004_Site,C004_Logo_Marca,C004_Nome_Abreviado FROM C004 WHERE C004_Id='{$g['empresaAtual']}'";
        $empresaAtual = mysql_query($sql);
        $empresaAtual = mysql_fetch_array($empresaAtual);
        
        $dataVencimento = gCorrigeData($Consulta['T117_Data_Vencimento']);
        $dataVencimentoOrig = gCorrigeData($Consulta['T002_Data_Vencimento']);
        $ValorTitulo = gCorrigeNumero($Consulta['T117_Valor_Total']);
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
        }
        
        $array['Para']    = (!empty($Consulta['D024_E_Mail_Boleto'])) ? $Consulta['D024_E_Mail_Boleto'] : $Consulta['D024_E_Mail'];
        $array['Assunto'] = "Boleto vencido";
        $dadosNF1 = $dadosNF2 = '';
        if(!empty($Consulta['T007_Id'])){
            $dadosNF1 = <<<EOT
                <td align="center"><b>NF</b></td>
EOT;
            $dadosNF2 = <<<EOT
                <td align="center">{$Consulta['T007_Numero_Nota_Fiscal']}</td>
EOT;
        }

        $dadosTitulo = <<<EOT
        <table cellpadding='5' border='1' style='border-style:solid; border-collapse: collapse;'>
            <tr>
                {$dadosNF1}
                <td align="center"><b>Vencimento Original</b></td>
                <td align="center"><b>Dias em atraso</b></td>
                <td align="center"><b>Valor Atual</b></td>
            </tr>
            <tr>
                {$dadosNF2}
                <td align="center">{$dataVencimento}</td>
                <td align="center">{$Consulta['Atraso']}</td>
                <td align="center">R$ {$ValorTitulo}</td>
            </tr>
        </table>
EOT;

        $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
        $caminhoLogo   = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";

        $dadosEmpresa = <<<EOT
                <b>{$g['C004']['C004_Nome_Empresa']}</b><br />
                {$g['C004']['C004_Logradouro']}, {$g['C004']['C004_Numero']} {$g['C004']['C004_Complemento']}<br />
                {$g['C004']['C004_Cep']} - {$g['C004']['D020_Nome_Cidade']} - {$g['C004']['D018_UF']}<br />
                Fone: {$g['C004']['C004_Telefone']}<br />
                {$g['C004']['C004_Site']}<br />
EOT;

        $array['Corpo'] = <<<EOT
        <div style="font-family:arial,verdana;font-size:10px;font-weight:bold;float: left; padding:10px">
            <img style="float:left; margin-right:10px;" src="$caminhoLogo" />
        </div>
        <div style="font-family:arial,verdana;font-size:10px;font-weight:bold;float: left;padding:10px">
            {$dadosEmpresa}
        </div>
        <div style="clear:both;padding:10px">
            <font style='font-size:14px;'>Olá! Não identificamos em nosso sistema o pagamento do(s) boleto(s) abaixo:</font><br /><br />
                {$dadosTitulo}<br />
                Lembrando que o pagamento pode ser realizado pela internet ou em qualquer agência bancária até a data do vencimento.<br/>
                <div style="clear:both; padding-top:16px;">
                    Caso o pagamento já tenha sido efetuado favor desconsiderar este e-mail.
                </div>
            </div>
            <div style="font-size:10px; line-height:16px;">
                E-mail enviado por <br /> {$empresaAtual['C004_Nome_Empresa']} <br /> <a href="{$empresaAtual['C004_Site']}">{$empresaAtual['C004_Site']}<a ></a>
            </div>
EOT;

        if ($this->usarBoletoProtegido($Consulta['T117_C004_Id'])) {
            $linkBoleto = $this->montarLinkBoletoProtegido($Consulta['T117_Id'], $Consulta['T117_D024_Id']);
            $array['Corpo'] = <<<EOT
        <div style="font-family:arial,verdana;font-size:10px;font-weight:bold;float: left; padding:10px">
            <img style="float:left; margin-right:10px;" src="$caminhoLogo" />
        </div>
        <div style="font-family:arial,verdana;font-size:10px;font-weight:bold;float: left;padding:10px">
            {$dadosEmpresa}
        </div>
        <div style="clear:both;padding:10px">
            <font style='font-size:14px;'>Olá! Não identificamos em nosso sistema o pagamento do(s) boleto(s) abaixo:</font><br /><br />
                {$dadosTitulo}<br />
                <div style="padding-top:12px;padding-bottom:6px;">
                    <a target="_blank" href="{$linkBoleto}" style="color:#1f6feb;font-size:14px;font-weight:bold;text-decoration:none;">
                        Abrir boleto com validação de CPF/CNPJ
                    </a>
                </div>
                Lembrando que o pagamento pode ser realizado pela internet ou em qualquer agência bancária até a data do vencimento.<br/>
                <div style="clear:both; padding-top:16px;">
                    Caso o pagamento já tenha sido efetuado favor desconsiderar este e-mail.
                </div>
                <div style="clear:both; padding-top:16px;">
                    Atenciosamente,
                </div>
            </font>
        </div>
EOT;
        }
        return $array;   
    }
    
    
}
