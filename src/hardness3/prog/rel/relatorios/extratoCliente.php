<?php
namespace hardness;
global $g;

switch ($g['r'][3]) {
    case 'imprimir':
        if (isset($r_acaoId) && !empty($r_acaoId)) {
            $cabecalhoId = uniqid();
            echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
            $actions = array(
                // identificador, url, paramentros get (opcional)
                array($cabecalhoId, '/cad/cad001/outro/cad001CabecalhoCliente/', '&habilitarLinkCadastro=S&acaoId=' . urlencode($r_D024_Id), true),
            );
            echo gProcessaAcoes($actions);
        }

        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
            array(uniqid(), '/rel/relatorios/extratoCliente/relatorio/', 'Extrato', ""),
        );
        
        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);
        
        // Carrega template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;

    case 'contentRelatorio':
        /*
        * Divisão da Area em Conteúdos
        */
        // Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
        $content1 = uniqid();
        $g['smarty']->assign('content1', $content1);
        $g['smarty']->assign('tipo', 'unico');
        // O que será carregado em cada conteúdo?
        $actions = array(
            // identificador, url, paramentros get (opcional)
            array($content1, '/rel/relatorios/extratoCliente/imprimir/', '&acaoId=' . urlencode($r_acaoId))
        );
        echo gProcessaAcoes($actions);

        // Definição dos botões
        $botoesDir = $botoesEsq = $botoesMid = '';        
        $botoesEsq .= '<button type="submit" id="imprimirRelatorio">Imprimir</button>';
        $g['smarty']->assign('botoesEsq', $botoesEsq);
        $g['smarty']->assign('botoesMid', $botoesMid);
        $g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');

        echo <<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            $('#imprimirRelatorio').unbind('click').bind('click', function(data) {
                nova_janela('/rel/relatorios/extratoCliente/relatorio/?ajax=true&D024_Id={$r_D024_Id}&Empresa={$r_Empresa}&impCabecalho=S',0,0,900,750,'no','no','no','no','yes','yes',0);
            });
        } </script></div>
EOT;
    break;

    case 'relatorio':
        $Empresa = $_REQUEST['Empresa'];
        // Busca os dados da empresa usu?ia
        $cSql=mysql_query("select * from C004 where C004_Id='{$Empresa}'");
        $mC004=mysql_fetch_array($cSql);

        set_time_limit(0);

        $mArq = explode(".", $mC004['C004_Logo_Marca']);
        $nExt = count($mArq) - 1;
        $cExtensao_Arquivo = $mArq[$nExt];
        $Arquivo_Logo = "/hardness3/".$g['pathDados'] . "/imagens/logo/{$g['empresaAtual']}" . ".$cExtensao_Arquivo";

        $D024_Id = $_REQUEST['D024_Id'];
        $dataHora = date('d/m/Y H:i:s');

        $sqlD024 = mysql_query("SELECT * FROM D024 
                                LEFT JOIN D020 ON D020_Id=D024_D020_Id
                                LEFT JOIN D018 ON D018_Id=D024_D018_Id
                                WHERE D024_Id = '{$D024_Id}'");
        $rowD024 = mysql_fetch_array($sqlD024);
        if($rowD024['D024_Flag_Pessoa_Fisica_Juridica'] == 'J'){
            $pessoaFisicaJuridica = "CNPJ: <b>{$rowD024['D024_Cnpj']}</b>";
        } else if($rowD024['D024_Flag_Pessoa_Fisica_Juridica'] == 'F'){
            $pessoaFisicaJuridica = "CPF: <b>{$rowD024['D024_Cpf']}</b>";
        } else {
            $pessoaFisicaJuridica = "Empresa do Exterior";
        }

        mysql_query("CALL D024_Gravar_Totalizacao('{$D024_Id}')");

        echo <<<EOT
        <div id="contentRelatorio">
        <html>
        <head>
        <title>Extrato do Cliente</title>
         
        <meta name='ROBOTS' content='NOINDEX,NOFOLLOW'>  <meta http-equiv='Pragma' content='no-cache'>  <meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1'>
        <link rel=stylesheet href="/hardness/classes/classe_tamanho_medio_firefox.css" type="text/css">
        <!-- Funções Genéricas em Javascript -->
        <script src='/hardness/func/funcoes_genericas.js'></script>
        <script src='/hardness/func/ajax.js'></script>
        <style type="text/css">
        #contentRelatorio { 
            font-family: arial, sans-serif, verdana, helvetica, sans-serif; 
            color:black; 
        }
        #contentRelatorio td {
            font-size:12px;
        }

        #contentDadosCRMCliente td {
            font-size: 11px;
        }
        #contentObservacaoCRMCliente td {
            font-size: 11px;
        }
EOT;
    if($_REQUEST['impCabecalho'] != 'S'){
        echo ".linhaRegistro td { cursor: pointer; }";
    }
    echo <<<EOT
        </style>
        </head>
        <body bottommargin=0 topmargin=0 rightmargin=0 leftmargin=0 bgcolor='white'>
        <table align=center border='0' valign=top cellpadding=3 cellspacing=0 bgcolor=white width=100%>
EOT;

        if($_REQUEST['impCabecalho'] == 'S'){
        echo <<<EOT
            <tr>
                <td valign=top colspan=2>
                    <table align=center border='0' cellspacing=0 cellpading=0 width='100%' height=10 valign=top style='border-bottom:1px dashed black'>
                    <tr>
                        <td bgcolor=white width=80%>
                            <table cellspacing=0 cellpading=0 border='0' align=center width=100% height=70>
                            <tr>                
                                <td bgcolor=white align=center valign=center width=22>
                                    <img src='{$Arquivo_Logo}' align=center><br>{$mC004['C004_Observacao_Logo']}
                                </td>
                                <td align=center>
                                    <table cellspacing=0 cellpading=0 border='0' align=center height=70>
                                        <tr><th><font class=fontemed>{$mC004['C004_Nome_Empresa']}</font></th></tr>
                                        <tr><td align=center><font class=fontemed>{$mC004['C004_Logradouro']}, {$mC004['C004_Numero']} {$mC004['C004_Complemento']}</font></td></tr>
                                        <tr><td align=center><font class=fontemed>{$mC004['C004_Cep']} - {$mC004['C004_Cidade']} - {$mC004['C004_Estado']}</font></td></tr>
                                        <tr><td align=center><font class=fontemed>Fone/Fax: {$mC004['C004_Telefone']} Fax: {$mC004['C004_Fax']}</font></td></tr>
                                        <tr><td align=center><font class=fontemed>{$mC004['C004_Site']}</font></td></tr>
                                    </table>
                                </td>
                            </tr>
                            </table>
                        </td>
                        <td bgcolor=white align=center width=20% rowspan=2>
                            <font class='titulo_relatorio'>Extrato do Cliente</font><br><b></b></font><br>
                        </td>
                    </tr>
                    <tr bgcolor=white>
                        <td colspan=2>
                            <font class=fontemed>Impresso</b> <b>{$dataHora}</b></font>
                        </td>
                    </tr>
                    <tr bgcolor=white>
                        <td colspan=2>
                            <font class=fontemed>NOME EMPRESA: <b>{$rowD024['D024_Nome_Empresa']}</b></font>
                            <font class=fontemed style="margin-left: 15px;">{$pessoaFisicaJuridica}</b></font>
                            <font class=fontemed style="margin-left: 15px;">Cidade/UF: </b><b>{$rowD024['D020_Nome_Cidade']}/{$rowD024['D018_UF']}</b></font>
                        </td>
                    </tr>
                    </table>
                </td>
            </tr>
EOT;
        }
        $ocultarDadosCRM = ($r_ocultarDadosCRM != true) ? '' : 'display: none;';
        echo <<<EOT
            <tr>
                <td class='dadosCRMCliente'>
                    <div id="contentDadosCRMCliente" style="width:53.5%; height: 104px; float: left; margin-top: 4px; font-size: 11px; {$ocultarDadosCRM}"></div>
                    <div id="contentObservacaoCRMCliente" style="width:45.5%; height: 104px; float: left; margin-top: 4px; font-size: 11px; {$ocultarDadosCRM}"></div>
                </td>
            </tr>
            <!--<tr>
                <td colspan=2>
                    <table cellpadding=2 style="width: 100%">
                        <tr bgcolor='lightblue'>
                            <td align=center colspan=5>
                                CONTATOS DA EMPRESA
                            </td>
                        </tr>
                        <tr bgcolor='lightblue'>
                            <td align=left>
                                Nome Contato
                            </td>
                            <td align=left>
                                Email
                            </td>
                            <td align=center>
                                Telefone 1
                            </td>
                            <td align=center>
                                Telefone 2
                            </td>
                            <td align=center>
                                Celular
                            </td>
                        </tr>
EOT;
                $sqlD013 = mysql_query("SELECT * FROM D013 WHERE D013_D024_Id = '{$D024_Id}'");
                while ($rowD013=mysql_fetch_array($sqlD013)) {
                    echo <<<EOT
                        <tr bgcolor='#f4f4f4' class="linhaRegistro" id="D024_Id-{$D024_Id}">
                            <td align=left>
                                {$rowD013['D013_Nome_Contato']}
                            </td>
                            <td align=left>
                                {$rowD013['D013_E_Mail']}
                            </td>
                            <td align=center>
                                {$rowD013['D013_DDD_Telefone_1']}-{$rowD013['D013_Telefone_1']}
                            </td>
                            <td align=center>
                                {$rowD013['D013_DDD_Telefone_2']}-{$rowD013['D013_Telefone_2']}
                            </td>
                            <td align=center>
                                {$rowD013['D013_DDD_Celular']}-{$rowD013['D013_Celular']}
                            </td>
                        </tr>
EOT;
                }
        echo <<<EOT
                    </table>
                </td>
            </tr>-->
            <tr>
                <td style="vertical-align: top">
                    <table cellpadding=2 style="width: 100%">
                        <tr bgcolor='lightblue'>
                            <td align=center colspan=15>
                                <b>CRÉDITO ANTECIPADO</b>
                            </td>
                        </tr>
                        <tr bgcolor='lightblue'>
                            <td align=center>
                                Duplicata
                            </td>
                            <td align=center>
                                Emissão
                            </td>
                            <td align=center>
                                Vencimento
                            </td>
                            <td align=center>
                                Recebimento
                            </td>
                            <td align=center>
                                Prazo
                            </td>
                            <td align=center>
                                Atraso
                            </td>
                            <td align=right>
                                Duplicata
                            </td>
                            <td align=right>
                                Desconto
                            </td>
                            <td align=right>
                                Juros
                            </td>
                            <td align=right>
                                Total
                            </td>
                            <td align=right>
                                Recebido
                            </td>
                            <td align=right>
                                Saldo
                            </td>
                            <td align=right>
                                Saldo Crédito
                            </td>
                            <td align=center>
                                Portador
                            </td>
                            <td align=center>
                                Vendedor
                            </td>
                        </tr>
EOT;
                $sqlT002 = mysql_query("SELECT *, 
                                        datediff(if(T002_Data_Recebimento='0000-00-00',curdate(),T002_Data_Recebimento),T002_Data_Vencimento) as Atraso,
                                        datediff(T002_Data_Vencimento,T002_Data_Emissao) as Prazo
                                        FROM T002 
                                        LEFT JOIN D027 ON D027_Id=T002_D027_Id 
                                        LEFT JOIN T117 ON T117_Id=T002_T117_Id
                                        LEFT JOIN T007 ON T007_Id=T002_T007_Id
                                        LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
                                        WHERE T002_D024_Id = '{$D024_Id}' 
                                        AND T002_Flag_Cancelada!='S'
                                        AND (D027_Flag_Credito_Antecipado = '1' OR D027_Flag_Credito_Antecipado = '3') 
                                        AND (T002_T002_Id_Agrupado=0 OR T002_T002_Id_Agrupado='' OR T002_T002_Id_Agrupado IS NULL)
                                        ORDER BY T002_Id DESC LIMIT 30");
                while($rowT002 = mysql_fetch_array($sqlT002)){
                    if(empty($rowT002['T002_T002_Id_Agrupado'])){
                        
                        if($rowT002['D027_Flag_Credito_Antecipado']==1){ $mTotal['Valor_Duplicata'] += $rowT002['T002_Valor_Duplicata'];}else{ $mTotal['Valor_Duplicata'] -= $rowT002['T002_Valor_Duplicata']; }
                        if($rowT002['D027_Flag_Credito_Antecipado']==1){ $mTotal['Valor_Desconto']  += $rowT002['T002_Valor_Desconto']; }else{ $mTotal['Valor_Desconto'] -= $rowT002['T002_Valor_Desconto']; }
                        if($rowT002['D027_Flag_Credito_Antecipado']==1){ $mTotal['Valor_Juros']     += $rowT002['T002_Valor_Juros'];    }else{ $mTotal['Valor_Juros'] -= $rowT002['T002_Valor_Juros']; }
                        if($rowT002['D027_Flag_Credito_Antecipado']==1){ $mTotal['Valor_Total']     += $rowT002['T002_Valor_Total'];    }else{ $mTotal['Valor_Total'] -= $rowT002['T002_Valor_Total']; }
                        if($rowT002['D027_Flag_Credito_Antecipado']==1){ $mTotal['Valor_Recebido']  += $rowT002['T002_Valor_Recebido']; }else{ $mTotal['Valor_Recebido'] -= $rowT002['T002_Valor_Recebido']; }
                        if($rowT002['D027_Flag_Credito_Antecipado']==1){ $mTotal['Valor_Saldo']     += $rowT002['T002_Valor_Saldo'];    }else{ $mTotal['Valor_Saldo'] -= $rowT002['T002_Valor_Saldo']; }
                    }

                    if($rowT002['T002_T002_Id_Agrupado'] > 0){
                        $corLinha = 'style="color: #B6833F"';
                    } else if($rowT002['T002_Data_Recebimento']!='0000-00-00'){
                        $corLinha = 'style="color: #605F5F"';
                    } else if ($rowT002['T002_Flag_Status']=='2') {
                        $corLinha = 'style="color: var(--BGVermelho)"';
                    } else if ($rowT002['T002_Flag_Status']=='3') {
                        $corLinha = 'style="color: blue"';
                    } else if($rowT002['D027_Flag_Credito_Antecipado'] == '1'){
                        $corLinha = 'style="color: green"';
                    } else {
                        $corLinha = '';
                    }

                    $rowT002['T002_Data_Emissao']        = gCorrigeData($rowT002['T002_Data_Emissao']);
                    $rowT002['T002_Data_Vencimento']     = gCorrigeData($rowT002['T002_Data_Vencimento']);
                    $rowT002['T002_Data_Recebimento']    = gCorrigeData($rowT002['T002_Data_Recebimento']);
                    $rowT002['T002_Valor_Duplicata']     = gCorrigeNumero($rowT002['T002_Valor_Duplicata']);
                    $rowT002['T002_Valor_Desconto']      = gCorrigeNumero($rowT002['T002_Valor_Desconto']);
                    $rowT002['T002_Valor_Juros']         = gCorrigeNumero($rowT002['T002_Valor_Juros']);
                    $rowT002['T002_Valor_Total']         = gCorrigeNumero($rowT002['T002_Valor_Total']);
                    $rowT002['T002_Valor_Recebido']      = gCorrigeNumero($rowT002['T002_Valor_Recebido']);
                    $rowT002['T002_Valor_Saldo']         = gCorrigeNumero($rowT002['T002_Valor_Saldo']);
                    $rowT002['T002_Valor_Saldo_Credito'] = gCorrigeNumero($rowT002['T002_Valor_Saldo_Credito']);

                    echo <<<EOT
                        <tr bgcolor='#f4f4f4' class="linhaRegistro" id="T002_Id-{$rowT002['T002_Id']}">
                            <td align=center {$corLinha}>
                                {$rowT002['T002_Numero_Duplicata']}
                            </td>
                            <td align=center {$corLinha}>
                                {$rowT002['T002_Data_Emissao']}
                            </td>
                            <td align=center {$corLinha}>
                                {$rowT002['T002_Data_Vencimento']}
                            </td>
                            <td align=center {$corLinha}>
                                {$rowT002['T002_Data_Recebimento']}
                            </td>
                            <td align=center {$corLinha}>
                                {$rowT002['Prazo']}
                            </td>
                            <td align=center {$corLinha}>
                                {$rowT002['Atraso']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT002['T002_Valor_Duplicata']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT002['T002_Valor_Desconto']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT002['T002_Valor_Juros']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT002['T002_Valor_Total']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT002['T002_Valor_Recebido']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT002['T002_Valor_Saldo']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT002['T002_Valor_Saldo_Credito']}
                            </td>
                            <td align=left {$corLinha}>
                                {$rowT002['D027_Portador']}
                            </td>
                            <td align=left {$corLinha}>
                                {$rowT002['C007_Primeiro_Nome']}
                            </td>
                        </tr>
EOT;
                }
                $mTotal['Valor_Duplicata'] = gCorrigeNumero($mTotal['Valor_Duplicata']);
                $mTotal['Valor_Desconto'] = gCorrigeNumero($mTotal['Valor_Desconto']);
                $mTotal['Valor_Juros'] = gCorrigeNumero($mTotal['Valor_Juros']);
                $mTotal['Valor_Total'] = gCorrigeNumero($mTotal['Valor_Total']);
                $mTotal['Valor_Recebido'] = gCorrigeNumero($mTotal['Valor_Recebido']);
                $mTotal['Valor_Saldo'] = gCorrigeNumero($mTotal['Valor_Saldo']);
                echo <<<EOT
                        <tr bgcolor='#f4f4f4'>
                            <td align=center>
                                <b>Total
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Total']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Desconto']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Juros']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Total']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Recebido']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Saldo']}</b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top">
                    <table cellpadding=2 style="width: 100%">
                        <tr bgcolor='lightblue'>
                            <td align=center colspan=15>
                                <b>CRÉDITO TROCA</b>
                            </td>
                        </tr>
                        <tr bgcolor='lightblue'>
                            <td align=center>
                                Duplicata
                            </td>
                            <td align=center>
                                Emissão
                            </td>
                            <td align=center>
                                Vencimento
                            </td>
                            <td align=center>
                                Recebimento
                            </td>
                            <td align=center>
                                Prazo
                            </td>
                            <td align=center>
                                Atraso
                            </td>
                            <td align=right>
                                Duplicata
                            </td>
                            <td align=right>
                                Desconto
                            </td>
                            <td align=right>
                                Juros
                            </td>
                            <td align=right>
                                Total
                            </td>
                            <td align=right>
                                Recebido
                            </td>
                            <td align=right>
                                Saldo
                            </td>
                            <td align=right>
                                Saldo Crédito
                            </td>
                            <td align=center>
                                Portador
                            </td>
                            <td align=center>
                                Vendedor
                            </td>
                        </tr>
EOT;
                $sqlT002 = mysql_query("SELECT *, 
                                               datediff(if(T002_Data_Recebimento='0000-00-00',curdate(),T002_Data_Recebimento),T002_Data_Vencimento) as Atraso,
                                               datediff(T002_Data_Vencimento,T002_Data_Emissao) as Prazo 
                                        FROM T002 
                                        LEFT JOIN D027 ON D027_Id=T002_D027_Id 
                                        LEFT JOIN T117 ON T117_Id=T002_T117_Id
                                        LEFT JOIN T007 ON T007_Id=T002_T007_Id
                                        LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
                                        WHERE T002_D024_Id = '{$D024_Id}' 
                                        AND T002_Flag_Cancelada!='S'
                                        AND (D027_Flag_Credito_Antecipado = '2' OR D027_Flag_Credito_Antecipado = '4') 
                                        AND (T002_T002_Id_Agrupado=0 OR T002_T002_Id_Agrupado='' OR T002_T002_Id_Agrupado IS NULL)
                                        ORDER BY T002_Id DESC LIMIT 30");
                unset($mTotal);
                while($rowT002 = mysql_fetch_array($sqlT002)){
                    if(empty($rowT002['T002_T002_Id_Agrupado'])){

                        if($rowT002['D027_Flag_Credito_Antecipado']==2){ $mTotal['Valor_Duplicata'] += $rowT002['T002_Valor_Duplicata'];}else{ $mTotal['Valor_Duplicata'] -= $rowT002['T002_Valor_Duplicata']; }
                        if($rowT002['D027_Flag_Credito_Antecipado']==2){ $mTotal['Valor_Desconto']  += $rowT002['T002_Valor_Desconto']; }else{ $mTotal['Valor_Desconto'] -= $rowT002['T002_Valor_Desconto']; }
                        if($rowT002['D027_Flag_Credito_Antecipado']==2){ $mTotal['Valor_Juros']     += $rowT002['T002_Valor_Juros'];    }else{ $mTotal['Valor_Juros'] -= $rowT002['T002_Valor_Juros']; }
                        if($rowT002['D027_Flag_Credito_Antecipado']==2){ $mTotal['Valor_Total']     += $rowT002['T002_Valor_Total'];    }else{ $mTotal['Valor_Total'] -= $rowT002['T002_Valor_Total']; }
                        if($rowT002['D027_Flag_Credito_Antecipado']==2){ $mTotal['Valor_Recebido']  += $rowT002['T002_Valor_Recebido']; }else{ $mTotal['Valor_Recebido'] -= $rowT002['T002_Valor_Recebido']; }
                        if($rowT002['D027_Flag_Credito_Antecipado']==2){ $mTotal['Valor_Saldo']     += $rowT002['T002_Valor_Saldo'];    }else{ $mTotal['Valor_Saldo'] -= $rowT002['T002_Valor_Saldo']; }
                    }

                    if($rowT002['T002_T002_Id_Agrupado'] > 0){
                        $corLinha = 'style="color: #B6833F"';
                    } else if($rowT002['T002_Data_Recebimento']!='0000-00-00'){
                        $corLinha = 'style="color: #605F5F"';
                    } else if ($rowT002['T002_Flag_Status']=='2') {
                        $corLinha = 'style="color: var(--BGVermelho)"';
                    } else if ($rowT002['T002_Flag_Status']=='3') {
                        $corLinha = 'style="color: blue"';
                    } else if($rowT002['D027_Flag_Credito_Antecipado'] == '1'){
                        $corLinha = 'style="color: green"';
                    } else {
                        $corLinha = '';
                    }

                    $rowT002['T002_Data_Emissao']        = gCorrigeData($rowT002['T002_Data_Emissao']);
                    $rowT002['T002_Data_Vencimento']     = gCorrigeData($rowT002['T002_Data_Vencimento']);
                    $rowT002['T002_Data_Recebimento']    = gCorrigeData($rowT002['T002_Data_Recebimento']);
                    $rowT002['T002_Valor_Duplicata']     = gCorrigeNumero($rowT002['T002_Valor_Duplicata']);
                    $rowT002['T002_Valor_Desconto']      = gCorrigeNumero($rowT002['T002_Valor_Desconto']);
                    $rowT002['T002_Valor_Juros']         = gCorrigeNumero($rowT002['T002_Valor_Juros']);
                    $rowT002['T002_Valor_Total']         = gCorrigeNumero($rowT002['T002_Valor_Total']);
                    $rowT002['T002_Valor_Recebido']      = gCorrigeNumero($rowT002['T002_Valor_Recebido']);
                    $rowT002['T002_Valor_Saldo']         = gCorrigeNumero($rowT002['T002_Valor_Saldo']);
                    $rowT002['T002_Valor_Saldo_Credito'] = gCorrigeNumero($rowT002['T002_Valor_Saldo_Credito']);
                    
                    echo <<<EOT
                        <tr bgcolor='#f4f4f4' class="linhaRegistro" id="T002_Id-{$rowT002['T002_Id']}">
                            <td align=center {$corLinha}>
                                {$rowT002['T002_Numero_Duplicata']}
                            </td>
                            <td align=center {$corLinha}>
                                {$rowT002['T002_Data_Emissao']}
                            </td>
                            <td align=center {$corLinha}>
                                {$rowT002['T002_Data_Vencimento']}
                            </td>
                            <td align=center {$corLinha}>
                                {$rowT002['T002_Data_Recebimento']}
                            </td>
                            <td align=center {$corLinha}>
                                {$rowT002['Prazo']}
                            </td>
                            <td align=center {$corLinha}>
                                {$rowT002['Atraso']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT002['T002_Valor_Duplicata']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT002['T002_Valor_Desconto']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT002['T002_Valor_Juros']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT002['T002_Valor_Total']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT002['T002_Valor_Recebido']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT002['T002_Valor_Saldo']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT002['T002_Valor_Saldo_Credito']}
                            </td>
                            <td align=left {$corLinha}>
                                {$rowT002['D027_Portador']}
                            </td>
                            <td align=left {$corLinha}>
                                {$rowT002['C007_Primeiro_Nome']}
                            </td>
                        </tr>                
EOT;
                }
                $mTotal['Valor_Duplicata'] = gCorrigeNumero($mTotal['Valor_Duplicata']);
                $mTotal['Valor_Desconto'] = gCorrigeNumero($mTotal['Valor_Desconto']);
                $mTotal['Valor_Juros'] = gCorrigeNumero($mTotal['Valor_Juros']);
                $mTotal['Valor_Total'] = gCorrigeNumero($mTotal['Valor_Total']);
                $mTotal['Valor_Recebido'] = gCorrigeNumero($mTotal['Valor_Recebido']);
                $mTotal['Valor_Saldo'] = gCorrigeNumero($mTotal['Valor_Saldo']);
                echo <<<EOT
                        <tr bgcolor='#f4f4f4'>
                            <td align=center>
                                <b>Total
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=center>
                                <b></b>
                             </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Total']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Desconto']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Juros']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Total']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Recebido']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Saldo']}</b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top">
                    <table cellpadding=2 style="width: 100%">
                        <tr bgcolor='lightblue'>
                            <td align=center colspan=12>
                                <b>CONTAS A PAGAR - EM ABERTO</b>
                            </td>
                        </tr>
                        <tr bgcolor='lightblue'>
                            <td align=center>
                                Duplicata
                            </td>
                            <td align=center>
                                Emissão
                            </td>
                            <td align=center>
                                Vencimento
                            </td>
                            <td align=center>
                                Prazo
                             </td>
                            <td align=center>
                                Atraso
                            </td>
                            <td align=right>
                                Duplicata
                            </td>
                            <td align=right>
                                Desconto
                            </td>
                            <td align=right>
                                Juros
                            </td>
                            <td align=right>
                                Total
                            </td>
                            <td align=right>
                                Pago
                            </td>
                            <td align=right>
                                Saldo
                            </td>
                            <td align=center>
                                Portador
                            </td>
                        </tr>
EOT;
                $sqlT015 = mysql_query("SELECT *,
                                               datediff(T015_Data_Vencimento,T015_Data_Emissao) as Prazo,
                                               datediff(if(T015_Data_Pagamento='0000-00-00',curdate(),T015_Data_Pagamento),T015_Data_Vencimento) as Atraso 
                                          FROM T015 
                                     LEFT JOIN D031 ON D031_Id=T015_D031_Id 
                                         WHERE T015_D024_Id = '{$D024_Id}' 
                                           AND T015_Data_Pagamento = '0000-00-00' 
                                           AND (T015_T015_Id_Agrupado <=0 or T015_T015_Id_Agrupado is null)
                                           AND T015_Flag_Cancelada != 'S'
                                      ORDER BY T015_Data_Vencimento");
                unset($mTotal);
                while($rowT015 = mysql_fetch_array($sqlT015)){
                    $mTotal['Valor_Duplicata']  += $rowT015['T015_Valor_Duplicata'];
                    $mTotal['Valor_Desconto']   += $rowT015['T015_Valor_Desconto'];
                    $mTotal['Valor_Juros']      += $rowT015['T015_Valor_Juros'];
                    $mTotal['Valor_Total']      += $rowT015['T015_Valor_Total'];
                    $mTotal['Valor_Pago']       += $rowT015['T015_Valor_Pago'];
                    $mTotal['Valor_Saldo']      += $rowT015['T015_Valor_Saldo'];

                    if($rowT002['T015_Data_Pagamento']!='0000-00-00'){
                        $corLinha = 'style="color: #605F5F"';
                    } else {
                        $corLinha = '';
                    }

                    $rowT015['T015_Data_Emissao']    = gCorrigeData($rowT015['T015_Data_Emissao']);
                    $rowT015['T015_Data_Vencimento'] = gCorrigeData($rowT015['T015_Data_Vencimento']);
                    $rowT015['T015_Valor_Duplicata'] = gCorrigeNumero($rowT015['T015_Valor_Duplicata']);
                    $rowT015['T015_Valor_Desconto']  = gCorrigeNumero($rowT015['T015_Valor_Desconto']);
                    $rowT015['T015_Valor_Juros']     = gCorrigeNumero($rowT015['T015_Valor_Juros']);
                    $rowT015['T015_Valor_Total']     = gCorrigeNumero($rowT015['T015_Valor_Total']);
                    $rowT015['T015_Valor_Pago']      = gCorrigeNumero($rowT015['T015_Valor_Pago']);
                    $rowT015['T015_Valor_Saldo']     = gCorrigeNumero($rowT015['T015_Valor_Saldo']);
                    echo <<<EOT
                        <tr bgcolor='#f4f4f4' class="linhaRegistro" id="T015_Id-{$rowT015['T015_Id']}">
                            <td align=center {$corLinha}>
                                {$rowT015['T015_Numero_Duplicata']}
                            </td>
                            <td align=center {$corLinha}>
                                {$rowT015['T015_Data_Emissao']}
                            </td>
                            <td align=center {$corLinha}>
                                {$rowT015['T015_Data_Vencimento']}
                            </td>
                            <td align=center {$corLinha}>
                                {$rowT015['Prazo']}
                            </td>
                            <td align=center {$corLinha}>
                                {$rowT015['Atraso']}
                             </td>
                            <td align=right {$corLinha}>
                                {$rowT015['T015_Valor_Duplicata']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT015['T015_Valor_Desconto']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT015['T015_Valor_Juros']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT015['T015_Valor_Total']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT015['T015_Valor_Pago']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT015['T015_Valor_Saldo']}
                            </td>
                            <td align=left {$corLinha}>
                                {$rowT015['D031_Portador']}
                            </td>
                        </tr>
EOT;
                }
                $mTotal['Valor_Duplicata'] = gCorrigeNumero($mTotal['Valor_Duplicata']);
                $mTotal['Valor_Desconto'] = gCorrigeNumero($mTotal['Valor_Desconto']);
                $mTotal['Valor_Juros'] = gCorrigeNumero($mTotal['Valor_Juros']);
                $mTotal['Valor_Total'] = gCorrigeNumero($mTotal['Valor_Total']);
                $mTotal['Valor_Pago'] = gCorrigeNumero($mTotal['Valor_Pago']);
                $mTotal['Valor_Saldo'] = gCorrigeNumero($mTotal['Valor_Saldo']);
                echo <<<EOT
                        <tr bgcolor='#f4f4f4'>
                            <td align=center>
                                <b>Total
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Total']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Desconto']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Juros']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Total']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Pago']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Saldo']}</b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top">
                    <table cellpadding=2 style="width: 100%">
                        <tr bgcolor='lightblue'>
                            <td align=center colspan=13>
                                <b>CONTAS PAGAS - ÚLTIMOS 100 REGISTROS</b>
                            </td>
                        </tr>
                        <tr bgcolor='lightblue'>
                            <td align=center>
                                Duplicata
                            </td>
                            <td align=center>
                                Emissão
                            </td>
                            <td align=center>
                                Vencimento
                            </td>
                            <td align=center>
                                Pagamento
                            </td>
                            <td align=center>
                                Prazo
                            </td>
                            <td align=center>
                                Atraso
                            </td>
                            <td align=right>
                                Duplicata
                            </td>
                            <td align=right>
                                Desconto
                            </td>
                            <td align=right>
                                Juros
                            </td>
                            <td align=right>
                                Total
                            </td>
                            <td align=right>
                                Pago
                            </td>
                            <td align=right>
                                Saldo
                            </td>
                            <td align=center>
                                Portador
                            </td>
                        </tr>
EOT;
                $sqlT015 = mysql_query("SELECT *, 
                                               datediff(T015_Data_Vencimento,T015_Data_Emissao) as Prazo,
                                               datediff(if(T015_Data_Pagamento='0000-00-00',curdate(),T015_Data_Pagamento),T015_Data_Vencimento) as Atraso 
                                          FROM T015 
                                     LEFT JOIN D031 ON D031_Id=T015_D031_Id 
                                         WHERE T015_D024_Id = '{$D024_Id}' 
                                           AND T015_Data_Pagamento != '0000-00-00'
                                           AND (T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null) 
                                           AND T015_Flag_Cancelada != 'S'
                                      ORDER BY T015_Data_Pagamento DESC LIMIT 100");
                unset($mTotal);
                while($rowT015 = mysql_fetch_array($sqlT015)){
                    $mTotal['Valor_Duplicata']  += $rowT015['T015_Valor_Duplicata'];
                    $mTotal['Valor_Desconto']   += $rowT015['T015_Valor_Desconto'];
                    $mTotal['Valor_Juros']      += $rowT015['T015_Valor_Juros'];
                    $mTotal['Valor_Total']      += $rowT015['T015_Valor_Total'];
                    $mTotal['Valor_Pago']       += $rowT015['T015_Valor_Pago'];
                    $mTotal['Valor_Saldo']      += $rowT015['T015_Valor_Saldo'];

                    if($rowT002['T015_Data_Pagamento']!='0000-00-00'){
                        $corLinha = 'style="color: #605F5F"';
                    } else {
                        $corLinha = '';
                    }

                    $rowT015['T015_Data_Emissao']    = gCorrigeData($rowT015['T015_Data_Emissao']);
                    $rowT015['T015_Data_Vencimento'] = gCorrigeData($rowT015['T015_Data_Vencimento']);
                    $rowT015['T015_Data_Pagamento']  = gCorrigeData($rowT015['T015_Data_Pagamento']);                    
                    $rowT015['T015_Valor_Duplicata'] = gCorrigeNumero($rowT015['T015_Valor_Duplicata']);
                    $rowT015['T015_Valor_Desconto']  = gCorrigeNumero($rowT015['T015_Valor_Desconto']);
                    $rowT015['T015_Valor_Juros']     = gCorrigeNumero($rowT015['T015_Valor_Juros']);
                    $rowT015['T015_Valor_Total']     = gCorrigeNumero($rowT015['T015_Valor_Total']);
                    $rowT015['T015_Valor_Pago']      = gCorrigeNumero($rowT015['T015_Valor_Pago']);
                    $rowT015['T015_Valor_Saldo']     = gCorrigeNumero($rowT015['T015_Valor_Saldo']);
                    echo <<<EOT
                        <tr bgcolor='#f4f4f4' class="linhaRegistro" id="T015_Id-{$rowT015['T015_Id']}">
                            <td align=center {$corLinha}>
                                {$rowT015['T015_Numero_Duplicata']}
                            </td>
                            <td align=center {$corLinha}>
                                {$rowT015['T015_Data_Emissao']}
                            </td>
                            <td align=center {$corLinha}>
                                {$rowT015['T015_Data_Vencimento']}
                            </td>
                            <td align=center {$corLinha}>
                                {$rowT015['T015_Data_Pagamento']}
                            </td>
                            <td align=center {$corLinha}>
                                {$rowT015['Prazo']}
                            </td>
                            <td align=center {$corLinha}>
                                {$rowT015['Atraso']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT015['T015_Valor_Duplicata']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT015['T015_Valor_Desconto']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT015['T015_Valor_Juros']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT015['T015_Valor_Total']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT015['T015_Valor_Pago']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT015['T015_Valor_Saldo']}
                            </td>
                            <td align=left {$corLinha}>
                                {$rowT015['D031_Portador']}
                            </td>
                        </tr>
EOT;
                }
                    $mTotal['Valor_Duplicata'] = gCorrigeNumero($mTotal['Valor_Duplicata']);
                    $mTotal['Valor_Desconto'] = gCorrigeNumero($mTotal['Valor_Desconto']);
                    $mTotal['Valor_Juros'] = gCorrigeNumero($mTotal['Valor_Juros']);
                    $mTotal['Valor_Total'] = gCorrigeNumero($mTotal['Valor_Total']);
                    $mTotal['Valor_Pago'] = gCorrigeNumero($mTotal['Valor_Pago']);
                    $mTotal['Valor_Saldo'] = gCorrigeNumero($mTotal['Valor_Saldo']);
                    echo <<<EOT
                        <tr bgcolor='#f4f4f4'>
                            <td align=center>
                                <b>Total
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Total']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Desconto']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Juros']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Total']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Pago']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Saldo']}</b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top">
                    <table cellpadding=2 style="width: 100%">
                        <tr bgcolor='lightblue'>
                            <td align=center colspan=13>
                                <b>CONTAS A RECEBER - EM ABERTO</b>
                            </td>
                        </tr>
                        <tr bgcolor='lightblue'>
                            <td align=center>
                                Duplicata
                            </td>
                            <td align=center>
                                Emissão
                            </td>
                            <td align=center>
                                Vencimento
                            </td>          
                            <td align=center>
                                Prazo
                            </td>                  
                            <td align=center>
                                Atraso
                            </td>
                            <td align=right>
                                Duplicata
                            </td>
                            <td align=right>
                                Desconto
                            </td>
                            <td align=right>
                                Juros
                            </td>
                            <td align=right>
                                Total
                            </td>
                            <td align=right>
                                Recebido
                            </td>
                            <td align=right>
                                Saldo
                            </td>
                            <td align=center>
                                Portador
                            </td>
                            <td align=center>
                                Vendedor
                            </td>
                        </tr>
EOT;
                $sqlT002 = mysql_query("SELECT *, 
                                               datediff(if(T002_Data_Recebimento='0000-00-00',curdate(),T002_Data_Recebimento),T002_Data_Vencimento) as Atraso,
                                               datediff(T002_Data_Vencimento,T002_Data_Emissao) as Prazo 
                                        FROM T002 
                                        LEFT JOIN D027 ON D027_Id=T002_D027_Id 
                                        LEFT JOIN T117 ON T117_Id=T002_T117_Id
                                        LEFT JOIN T007 ON T007_Id=T002_T007_Id
                                        LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
                                        WHERE T002_D024_Id = '{$D024_Id}' 
                                        AND T002_Flag_Cancelada!='S'
                                        AND T002_Data_Recebimento = '0000-00-00'
                                        AND (T002_T002_Id_Agrupado=0 OR T002_T002_Id_Agrupado='' OR T002_T002_Id_Agrupado IS NULL)
                                        ORDER BY T002_Data_Vencimento");
                unset($mTotal);
                while($rowT002 = mysql_fetch_array($sqlT002)){
                    if(empty($rowT002['T002_T002_Id_Agrupado'])){
                        $mTotal['Valor_Duplicata']  += $rowT002['T002_Valor_Duplicata'];
                        $mTotal['Valor_Desconto']   += $rowT002['T002_Valor_Desconto'];
                        $mTotal['Valor_Juros']      += $rowT002['T002_Valor_Juros'];
                        $mTotal['Valor_Total']      += $rowT002['T002_Valor_Total'];
                        $mTotal['Valor_Recebido']   += $rowT002['T002_Valor_Recebido'];
                        $mTotal['Valor_Saldo']      += $rowT002['T002_Valor_Saldo'];
                    }

                    if($rowT002['T002_T002_Id_Agrupado'] > 0){
                        $corLinha = 'style="color: #B6833F"';
                    } else if($rowT002['T002_Data_Recebimento']!='0000-00-00'){
                        $corLinha = 'style="color: #605F5F"';
                    } else if ($rowT002['T002_Flag_Status']=='2') {
                        $corLinha = 'style="color: var(--BGVermelho)"';
                    } else if ($rowT002['T002_Flag_Status']=='3') {
                        $corLinha = 'style="color: blue"';
                    } else if($rowT002['D027_Flag_Credito_Antecipado'] == '1'){
                        $corLinha = 'style="color: green"';
                    } else {
                        $corLinha = '';
                    }

                    $rowT002['T002_Data_Emissao']    = gCorrigeData($rowT002['T002_Data_Emissao']);
                    $rowT002['T002_Data_Vencimento'] = gCorrigeData($rowT002['T002_Data_Vencimento']);
                    $rowT002['T002_Valor_Duplicata'] = gCorrigeNumero($rowT002['T002_Valor_Duplicata']);
                    $rowT002['T002_Valor_Desconto']  = gCorrigeNumero($rowT002['T002_Valor_Desconto']);
                    $rowT002['T002_Valor_Juros']     = gCorrigeNumero($rowT002['T002_Valor_Juros']);
                    $rowT002['T002_Valor_Recebido']  = gCorrigeNumero($rowT002['T002_Valor_Recebido']);
                    $rowT002['T002_Valor_Total']     = gCorrigeNumero($rowT002['T002_Valor_Total']);
                    $rowT002['T002_Valor_Saldo']     = gCorrigeNumero($rowT002['T002_Valor_Saldo']);
                    echo <<<EOT
                        <tr bgcolor='#f4f4f4' class="linhaRegistro" id="T002_Id-{$rowT002['T002_Id']}">
                            <td align=center {$corLinha}>
                                {$rowT002['T002_Numero_Duplicata']}
                            </td>
                            <td align=center {$corLinha}>
                                {$rowT002['T002_Data_Emissao']}
                            </td>
                            <td align=center {$corLinha}>
                                {$rowT002['T002_Data_Vencimento']}
                            </td>
                            <td align=center {$corLinha}>
                                {$rowT002['Prazo']}
                            </td>
                            <td align=center {$corLinha}>
                                {$rowT002['Atraso']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT002['T002_Valor_Duplicata']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT002['T002_Valor_Desconto']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT002['T002_Valor_Juros']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT002['T002_Valor_Total']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT002['T002_Valor_Recebido']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT002['T002_Valor_Saldo']}
                            </td>
                            <td align=left {$corLinha}>
                                {$rowT002['D027_Portador']}
                            </td>
                            <td align=left {$corLinha}>
                                {$rowT002['C007_Primeiro_Nome']}
                            </td>
                        </tr>
EOT;
                }
                $mTotal['Valor_Duplicata'] = gCorrigeNumero($mTotal['Valor_Duplicata']);
                $mTotal['Valor_Desconto'] = gCorrigeNumero($mTotal['Valor_Desconto']);
                $mTotal['Valor_Juros'] = gCorrigeNumero($mTotal['Valor_Juros']);
                $mTotal['Valor_Total'] = gCorrigeNumero($mTotal['Valor_Total']);
                $mTotal['Valor_Recebido'] = gCorrigeNumero($mTotal['Valor_Recebido']);
                $mTotal['Valor_Saldo'] = gCorrigeNumero($mTotal['Valor_Saldo']);
                echo <<<EOT
                        <tr bgcolor='#f4f4f4'>
                            <td align=center>
                                <b>Total
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Total']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Desconto']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Juros']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Total']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Recebido']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Saldo']}</b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td> 
                        </tr>                
                    </table>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top">
                    <table cellpadding=2 style="width: 100%">
                        <tr bgcolor='lightblue'>
                            <td align=center colspan=14>
                                <b>CONTAS RECEBIDAS - ÚLTIMOS 100 REGISTROS</b>
                            </td>
                        </tr>
                        <tr bgcolor='lightblue'>
                            <td align=center>
                                Duplicata
                            </td>
                            <td align=center>
                                Emissão
                            </td>
                            <td align=center>
                                Vencimento
                            </td>
                            <td align=center>
                                Recebimento
                            </td>
                            <td align=center>
                                Prazo
                            </td>
                            <td align=center>
                                Atraso
                            </td>
                            <td align=right>
                                Duplicata
                            </td>
                            <td align=right>
                                Desconto
                            </td>
                            <td align=right>
                                Juros
                            </td>
                            <td align=right>
                                Total
                            </td>
                            <td align=right>
                                Recebido
                            </td>
                            <td align=right>
                                Saldo
                            </td>
                            <td align=center>
                                Portador
                            </td>
                            <td align=center>
                                Vendedor
                            </td>
                        </tr>
EOT;
                $sqlT002 = mysql_query("SELECT *, 
                                               datediff(if(T002_Data_Recebimento='0000-00-00',curdate(),T002_Data_Recebimento),T002_Data_Vencimento) as Atraso,
                                               datediff(T002_Data_Vencimento,T002_Data_Emissao) as Prazo
                                        FROM T002 
                                        LEFT JOIN D027 ON D027_Id=T002_D027_Id 
                                        LEFT JOIN T117 ON T117_Id=T002_T117_Id
                                        LEFT JOIN T007 ON T007_Id=T002_T007_Id
                                        LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
                                        WHERE T002_D024_Id = '{$D024_Id}' 
                                        AND T002_Flag_Cancelada!='S'
                                        AND T002_Data_Recebimento != '0000-00-00'
                                        AND (T002_T002_Id_Agrupado=0 OR T002_T002_Id_Agrupado='' OR T002_T002_Id_Agrupado IS NULL)
                                        AND (D027_Flag_Credito_Antecipado != '3' AND D027_Flag_Credito_Antecipado != '4')
                                        ORDER BY T002_Data_Recebimento DESC LIMIT 100");
                unset($mTotal);
                while($rowT002 = mysql_fetch_array($sqlT002)){
                    if(empty($rowT002['T002_T002_Id_Agrupado'])){
                        $mTotal['Valor_Duplicata']  += $rowT002['T002_Valor_Duplicata'];
                        $mTotal['Valor_Desconto']   += $rowT002['T002_Valor_Desconto'];
                        $mTotal['Valor_Juros']      += $rowT002['T002_Valor_Juros'];
                        $mTotal['Valor_Total']      += $rowT002['T002_Valor_Total'];
                        $mTotal['Valor_Recebido']   += $rowT002['T002_Valor_Recebido'];
                        $mTotal['Valor_Saldo']      += $rowT002['T002_Valor_Saldo'];
                    }

                    if($rowT002['T002_T002_Id_Agrupado'] > 0){
                        $corLinha = 'style="color: #B6833F"';
                    } else if($rowT002['T002_Data_Recebimento']!='0000-00-00'){
                        $corLinha = 'style="color: #605F5F"';
                    } else if ($rowT002['T002_Flag_Status']=='2') {
                        $corLinha = 'style="color: var(--BGVermelho)"';
                    } else if ($rowT002['T002_Flag_Status']=='3') {
                        $corLinha = 'style="color: blue"';
                    } else if($rowT002['D027_Flag_Credito_Antecipado'] == '1'){
                        $corLinha = 'style="color: green"';
                    }

                    $rowT002['T002_Data_Emissao']     = gCorrigeData($rowT002['T002_Data_Emissao']);
                    $rowT002['T002_Data_Vencimento']  = gCorrigeData($rowT002['T002_Data_Vencimento']);
                    $rowT002['T002_Data_Recebimento'] = gCorrigeData($rowT002['T002_Data_Recebimento']);                    
                    $rowT002['T002_Valor_Duplicata']  = gCorrigeNumero($rowT002['T002_Valor_Duplicata']);
                    $rowT002['T002_Valor_Desconto']   = gCorrigeNumero($rowT002['T002_Valor_Desconto']);
                    $rowT002['T002_Valor_Juros']      = gCorrigeNumero($rowT002['T002_Valor_Juros']);
                    $rowT002['T002_Valor_Total']      = gCorrigeNumero($rowT002['T002_Valor_Total']);
                    $rowT002['T002_Valor_Recebido']   = gCorrigeNumero($rowT002['T002_Valor_Recebido']);
                    $rowT002['T002_Valor_Saldo']      = gCorrigeNumero($rowT002['T002_Valor_Saldo']);
                    echo <<<EOT
                        <tr bgcolor='#f4f4f4' class="linhaRegistro" id="T002_Id-{$rowT002['T002_Id']}">
                            <td align=center {$corLinha}>
                                {$rowT002['T002_Numero_Duplicata']}
                            </td>
                            <td align=center {$corLinha}>
                                {$rowT002['T002_Data_Emissao']}
                            </td>
                            <td align=center {$corLinha}>
                                {$rowT002['T002_Data_Vencimento']}
                            </td>                            
                            <td align=center {$corLinha}>
                                {$rowT002['T002_Data_Recebimento']}
                            </td>
                            <td align=center {$corLinha}>
                                {$rowT002['Prazo']}
                            </td>
                            <td align=center {$corLinha}>
                                {$rowT002['Atraso']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT002['T002_Valor_Duplicata']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT002['T002_Valor_Desconto']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT002['T002_Valor_Juros']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT002['T002_Valor_Total']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT002['T002_Valor_Recebido']}
                            </td>
                            <td align=right {$corLinha}>
                                {$rowT002['T002_Valor_Saldo']}
                            </td>
                            <td align=left {$corLinha}>
                                {$rowT002['D027_Portador']}
                            </td>
                            <td align=left {$corLinha}>
                                {$rowT002['C007_Primeiro_Nome']}
                            </td>
                        </tr>                
EOT;
                }
                $mTotal['Valor_Duplicata'] = gCorrigeNumero($mTotal['Valor_Duplicata']);
                $mTotal['Valor_Desconto'] = gCorrigeNumero($mTotal['Valor_Desconto']);
                $mTotal['Valor_Juros'] = gCorrigeNumero($mTotal['Valor_Juros']);
                $mTotal['Valor_Total'] = gCorrigeNumero($mTotal['Valor_Total']);
                $mTotal['Valor_Recebido'] = gCorrigeNumero($mTotal['Valor_Recebido']);
                $mTotal['Valor_Saldo'] = gCorrigeNumero($mTotal['Valor_Saldo']);
                echo <<<EOT
                        <tr bgcolor='#f4f4f4'>
                            <td align=center>
                                <b>Total
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Total']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Desconto']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Juros']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Total']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Recebido']}</b>
                            </td>
                            <td align=right>
                                <b>{$mTotal['Valor_Saldo']}</b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                            <td align=center>
                                <b></b>
                            </td>
                        </tr>                
                    </table>
                </td>
            </tr>
        </table>
        </body>
        </html>
        </div>
        <div class="divHidden"><script type="text/javascript">
            carregar_ajax('/crm/crm001/outro/crm001DadosFinanceiro/&ajax=true&D024_Id=' + encodeURIComponent('{$D024_Id}'), 'POST', '', 'contentDadosCRMCliente');
            carregar_ajax('/crm/crm001/outro/crm001Observacoes/&ajax=true&D024_Id=' + encodeURIComponent('{$D024_Id}'), 'POST', '', 'contentObservacaoCRMCliente');

            if (typeof jQuery != 'undefined') {
                $('#contentRelatorio table tr td').unbind('click').unbind('click').bind('click', function(data) {
                    console.log(data.target.nodeName);
                    if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                    var id = $(this).closest('tr').attr('id').split('-');
                    var tabela = id[0];
                    id = id[1];
                    if(tabela == 'T002_Id'){
                        abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Título a Receber - ID: ' + id, '/fin/fin001/content/fin001content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela=T002', [1050,540]);
                    } else if(tabela == 'T015_Id'){                    
                        abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Título a Pagar - ID: ' + id, '/fin/fin002/content/fin002content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela=T015', [800,500]);
                    } else if(tabela == 'D024_Id'){
                        abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Cliente/Fornecedor - ID: ' + id, '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent(id) + '&tabela=D024' + '&tab=contatos', [950,553]);
                    }
                });
        } </script></div>
EOT;
    break;
}
