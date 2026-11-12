<?php
namespace hardness;

switch ($g['r'][3]) {
	/**
	 * Cabeçalho Orçamento
	 * Chamado em: ven001TabEditarOrcamento
	 */
	case 'pedidoAlmoxarifado':
        global $g;
        
        //??
        //$cArquivo_Cabecalho_Relatorio = $confUsuario['pathRaiz']."dados_usuarios/".$confUsuario['dbDatabase']."/cabecalho_relatorio.php";
        $cArquivo_Cabecalho_Relatorio = "hardness/cabecalho_impressoes.php";
    
        $Linhas_Normais=30;
        $Linhas_Ultima_Pagina=25;
        $Tamanho_Maximo_Tabela=580;
        $Tamanho_Minimo_Tabela=400;
        $Width_Relatorio='98%';

        if(isset($_GET['T005_Id_64']))
        {
            $T005_Id_Array = explode(';', base64_decode($_GET['T005_Id_64']));
        }
        else
        {
            $T005_Id_Array = explode(';', $_GET['T005_Id']);
        }
        

        /* HTML START */

        echo "<html>";
        echo "<head>";
        echo "<title>Impressão Pedidos</title>";
        echo "<meta http-equiv='Content-Type' content='text/html' charset='UTF-8'>";
        echo "<style>

        .fonte
        {
            font-family:arial,verdana;
            font-size:11px;
        }

        @media print {
            .pagebreak { page-break-before: always;} /* page-break-after works, as well */
        }


        </style>";

        echo "</head>";
        echo "<body bgcolor=white leftmargin='0' topmargin='0'>";

        /* PARA CADA PEDIDO(T005_Id) */
        foreach($T005_Id_Array as $T005_Id) 
        {
            /* PEGA TODAS AS INFOs DO PEDIDO */
            $cSql_T005=mysql_query("select * from T005
            left join D024 on D024_Id=T005_D024_Id
            left join D020 on D020_Id=D024_D020_Id
            left join D018 on D018_Id=D020_D018_Id
            left join D021 on D021_Id=T005_D021_Id
            left join D022 on D022_Id=T005_D022_Id
            left join D036 ON D036_Id=T005_D036_Id
            left join C007 on C007_Id=T005_C007_Id_Vendedor_Interno
                where T005_Id='{$T005_Id}'");

            $mT005 = mysql_fetch_assoc($cSql_T005);

            // PEGA INFO
            $cSql_C004=mysqli_query("select * from C004 where C004_Id='{$Empresa_Atual}'");
            $mC004=mysql_fetch_array($cSql_C004);
            $mC004['C004_Observacao_Promocao']=str_replace("\n","<br>",$mC004['C004_Observacao_Promocao']);

            $mT005['T005_Data_Emissao']=gCorrigeData($mT005['T005_Data_Emissao']);
            $mT005['T005_Observacao']=str_replace("\n","<br>",trim($mT005['T005_Observacao']));

            $cSql_Prazos=mysqli_query("select T005_Prazos('$T005_Id') as Prazos");
            $mPrazos=mysql_fetch_array($cSql_Prazos);

            // PEGA INFO
            if ($mConfig['C020_Expedicao_Direcionar_Compra']=='S')
            {
                $cClausula="select count(*)
                            from T006
                        left join T005 on T005_Id=T006_T005_Id
                        left join D009 on D009_Id=T006_D009_Id
                        left join T066 on T066_D009_Id=D009_Id
                        left join D004 on D004_Id=T066_D004_Id
                        left join D049 on D049_Id=D009_D049_Id
                        left join C004 on C004_Id=T006_C004_Id
                        left join D024 on D024_Id=D009_D024_Id
                        left join D005 on D005_Id=T006_D005_Id
                        left join D082 on D082_Id=D049_D082_Id
                            where T006_T005_Id='$T005_Id'
                            and (T006_T006_Id is null or T006_T006_Id<=0)
                            and (T006_Quantidade>0)
                            and (T006_Flag_Status!=3)
                            and (T006_Flag_Tipo_Produto='AA' or T006_Flag_Tipo_Produto='' or T006_Flag_Tipo_Produto is null)
                            and (T006_Quantidade>T006_Quantidade_Separacao or T006_Quantidade_Separacao is null)
                            and T006_Flag_Divergencia!='S'
                            and T006_Quantidade>T006_Quantidade_Estoque
                        group by T006_Id
                        order by D004_Local,T006_Id";
                $Resultado=mysqli_query($cClausula);
            }
            // PEGA INFO
            $cClausula="select T006_Item,
                            T006_Quantidade,
                            if(T006_Codigo_Substituto!='',T006_Codigo_Substituto,T006_Codigo_Produto) as T006_Codigo_Produto_Substituto,
                            T006_Codigo_Produto,
                            T006_Codigo_Substituto,
                            T006_Descricao_Produto,
                            D082_Marca,
                            D049_Modelo,
                            D009_Quantidade_Estoque,
                            T006_Quantidade_Separacao Separado,
                            T006_Quantidade_Estoque,
                            D009_Quantidade_Estoque_Real,
                            T006_Peso_Unitario,
                            T006_Id,
                            D003_Imagem,
                            group_concat(distinct T066_Id SEPARATOR '|') as T066_Id_Mais,
                            DATE_FORMAT(D049_Data_Fabricacao,'%d/%m/%Y') AS D049_Data_Fabricacao,
                            DATE_FORMAT(D049_Data_Validade,'%d/%m/%Y') AS D049_Data_Validade,
                            D049_Lote,
                            D037_Unidade,
                            D049_Id,
                            T006_Quantidade_Separacao,
                            T006_T066_Id,
                            T006_D009_Id,
                            D001_Id,
                            (SELECT T014_Codigo_Produto from T014 LEFT JOIN T013 on T013_Id=T014_T013_Id LEFT JOIN D006 ON D006_Id = T014_D006_Id WHERE T014_D009_Id=D009_Id AND T013_Flag_Estoque_Finalizado = 'S' AND (D006_Flag_Venda_Compra_Outros = 'C' OR D006_Flag_Venda_Compra_Outros = 'T') ORDER BY T013_Data_Emissao DESC LIMIT 1) as Cod_Forn
                        from T006
                    left join T005 on T005_Id=T006_T005_Id
                    left join D009 on D009_Id=T006_D009_Id
                    left join T066 on T066_Id=T006_T066_Id
                    left join D004 on D004_Id=T066_D004_Id
                    left join D049 on D049_Id=D009_D049_Id
                    left join D001 on D001_Id=D049_D001_Id
                    left join D003 on D003_Id=D001_D003_Id
                    left join C004 on C004_Id=T006_C004_Id
                    left join D024 on D024_Id=D009_D024_Id
                    left join D005 on D005_Id=T006_D005_Id
                    left join D082 on D082_Id=D049_D082_Id
                    left join D037 on D037_Id=D001_D037_Id
                        where T006_T005_Id='$T005_Id'
                        and (T006_T006_Id is null or T006_T006_Id<=0)
                        and (T006_Quantidade>0)
                        and (T006_Flag_Status!=3)
                        and IF(IFNULL(T006_C004_Id_Estoque,0) > 0, T006_C004_Id_Estoque, T005_C004_Id) = T005_C004_Id
                    group by T006_Id
                    order by D004_Local,T066_Id ASC,T006_Id";
            $Resultado=mysqli_query($cClausula);
            echo mysql_error();

            // PEGA INFO
            $sqlTotais = mysqli_query("SELECT COUNT(DISTINCT T006_Id) as totalItens,
                                            SUM(T006_Quantidade) as quantidadeTotal,
                                            SUM(D001_Cubagem_Unitaria*T006_Quantidade) as cubagemTotal,
                                            T005_Peso_Liquido as pesoliquidoTotal,
                                            T005_Peso_Bruto as pesobrutoTotal
                                            FROM T006
                                        LEFT JOIN T005 ON T005_Id = T006_T005_Id     
                                        LEFT JOIN D009 ON D009_Id = T006_D009_Id
                                        LEFT JOIN D049 ON D049_Id = D009_D049_Id
                                        LEFT JOIN D001 ON D001_Id = D049_D001_Id
                                            WHERE T006_T005_Id='{$T005_Id}'");
            $rowTotais = mysql_fetch_assoc($sqlTotais);
            $rowTotais['pesobrutoTotal'] = number_format($rowTotais['pesobrutoTotal'],2,',','.');
            $rowTotais['pesoliquidoTotal'] = number_format($rowTotais['pesoliquidoTotal'],2,',','.');
            $rowTotais['quantidadeTotal'] = number_format($rowTotais['quantidadeTotal'],2,',','.');
            $rowTotais['cubagemTotal'] = number_format($rowTotais['cubagemTotal'],2,',','.');

            ////////////////////////////////////////////////////////////
            // VERIFICA QUANTAS PAGINAS SERÃO IMPRESSAS
            ////////////////////////////////////////////////////////////
            // Encontra o total das linhas a ser impressa
            $Total_Linhas=mysql_num_rows($Resultado);
            if (!empty($mT005['T005_Mao_Obra_1'])) {
                $Total_Linhas+=4;
            }

            if ($Total_Linhas==0) {
            //-- SE NAO RETORNAR NADA PARA IMPRIMIR NO CORPO DO RELATORIO, TOTAL_LINHAS É DEFINIDO PARA 1,
            //-- PARA QUE SEJA IMPRESSO O CABECALHO.
            $Total_Linhas=1;
            }

            if ($Total_Linhas>=1) {

                if (count($mLimit)==0) {
                    // Encontra o numero de paginas cheias
                    $Total_Paginas = intval($Total_Linhas / $Linhas_Normais);
                    // Encontra o numero de itens restantes
                    if ($Total_Paginas>=1) {
                    $Resto = ($Total_Linhas - ($Total_Paginas * $Linhas_Normais));
                    } else {
                    $Resto = $Total_Linhas;
                    }

                    // Verifica caso o resto seja maior que o número máximo de itens que cabe na última folha
                    if ($Resto > $Linhas_Ultima_Pagina) {
                    // Sendo maior soma mais duas folhas
                    $Total_Paginas += 2 ;
                    } else {
                    // Sendo menor soma mais uma folha
                    $Total_Paginas += 1 ;
                    }
                } else {
                    $Total_Paginas=count($mLimit);

                    $Linhas_Normais=$mLimit[0];
                }
            }

            //////////////////////////////////////////////////////////////

            // Inicia com o tamanho maximo estabelecido
            $Tamanho_Tabela_Itens=$Tamanho_Maximo_Tabela;

            // Inicializa Variavel utilizada no limit da consulta dos itens
            $Inicio=0;
            $Pagina_Atual=0;
            $Pagina_Atual++;

            echo "<br><br><br>";
            echo "<table align=center border='0' valign=top cellpading=0 cellspacing=0 width='735' bgcolor=white bgcolor=white style='border:0px solid black;'>";
                echo "<tr bgcolor=white>";
                echo "<td valign=top>";
                echo "<table align=center border='0' cellspacing=0 cellpading=0 width='100%' height=10 valign=top style='border:0px solid'>";
                echo "<tr>";
                    echo "<td bgcolor=white width=80%>";

                    if(file_exists($cArquivo_Cabecalho_Relatorio)){
                        ob_start();
                        include $cArquivo_Cabecalho_Relatorio;
                        echo ob_get_clean();
                    }else{
                        echo "Arquivo cabeçalho não encontrado: $cArquivo_Cabecalho_Relatorio<br>";
                    }

                    echo "</td>";
                    echo "<td bgcolor=white align=center width=20%>";
                        echo "<font class='fonte' style='font-size:18px;font-weight:bold'>";
                    echo "PEDIDO<BR>Nr. $T005_Id";
                        echo "</font>";
                    echo "</font>";
                    echo "<br><font class=fonte>".date("d/m/Y")."</font>";
                    echo "</td>";
                echo "</tr>";
                echo "<tr bgcolor=white>";
                    echo "<td colspan=2>";
                    echo "<table cellpadding=7 width=100% border=0>";
                        echo "<tr bgcolor=white>";
                        echo "<td colspan=3>";
                        echo "<table width=100% border=0>";
                        echo "<tr><td style='width:50%'>";
                            if ($mT005['D024_Id']>0) {
                                echo "<font class='fonte' style='font-size:12px'>Cliente : <b>{$mT005['D024_Id']}-{$mT005['D024_Nome_Empresa']}</b></font><br>";
                                echo "<font class='fonte' style='font-size:12px'>Fantasia: <b>{$mT005['D024_Nome_Fantasia']}</b></font><br>";
                                echo "<font class='fonte' style='font-size:12px'>Telefone: <b>{$mT005['D024_DDD_Telefone_1']} {$mT005['D024_Telefone_1']}</b></font><br>";
                                echo "<font class='fonte' style='font-size:12px'>CNPJ/CPF: <b>{$mT005['D024_Cnpj']}{$mT005['D024_Cpf']}</b></font><br>";
                                echo "<font class='fonte' style='font-size:12px'>Endereco: <b>{$mT005['D024_Endereco']}, {$mT005['D024_Numero']} {$mT005['D024_Complemento']}</font></b><br>";
                                echo "<font class='fonte' style='font-size:12px'>Cep/Bairro: <b>{$mT005['D024_Cep']} - {$mT005['D024_Bairro']}</font></b><br>";
                                echo "<font class='fonte' style='font-size:12px'>Cidade/UF: <b>{$mT005['D020_Nome_Cidade']} / {$mT005['D018_UF']}</font></b><br>";
                                if (!empty($mT005['T005_Nome_Comprador'])) {
                                    echo "<font class='fonte'>Comprador:</font> ";
                                    echo "<font class='fonte' style='font-size:12px'>{$mT005['T005_Nome_Comprador']}</font> <font class=fonte>{$mT005['T005_E_mail_Comprador']}</font><br>";
                                }
                            } else {
                                echo "<font class='fonte' style='font-size:12px'><b>{$mT005['T005_Nome_Cliente']}</b></font><br>";
                                echo "<font class='fonte' style='font-size:12px'>TELEFONE: {$mT005['T005_Telefone']} - FAX: {$mT005['T005_Fax']}</font><br>";
                                if (!empty($mT005['T005_Nome_Comprador'])) {
                                    echo "<font class='fonte'>Comprador:</font> ";
                                    echo "<font class='fonte' style='font-size:12px'>{$mT005['T005_Nome_Comprador']}</font> <font class=fonte>{$mT005['T005_E_mail_Comprador']}</font>";
                                }
                            }
                            //if (!empty($mT005['T005_Observacao'])) {
                                //echo "<br><font class='fonte' style='font-size:12px'><b>$mT005['T005_Observacao']</b></font>";
                            //}
                        echo "</td>";
                        echo "<td align=left valign=top style='width:50%'>";
                                    echo "<font class='fonte' style='font-size:12px'>Vendedor: <b>{$mT005['C007_Nome']}</b></font><br>";
                                    echo "<font class='fonte' style=\"color: var(--BGVermelho);font-size:18px;font-weight:bold\">{$mT005['D022_Nome_Empresa']}</font>";
                            if (!empty($mT005['D036_Tipo_Transporte'])) {
                            echo "<br /><font class=fonte style=\"font-size:12px;\">Tipo Frete: <b>".$mT005['D036_Tipo_Transporte']."</b></font>";
                            }
                            
                            switch($mT005['T005_Flag_Frete']){
                            case '0':
                            $T005_Frete = "EMITENTE";
                            break;
                            case '1':
                            $T005_Frete = "DESTINATÁRIO";
                            break;
                            case '2':
                            $T005_Frete = "TERCEIROS";
                            break;
                            case '9':
                            $T005_Frete = "SEM FRETE";
                            break;
                            default:
                            $T005_Frete = "NãO INFORMADO";
                            break;
                            }
                            echo "<br /><font class=fonte style=\"font-size:12px\">Frete: <b>{$T005_Frete}</b></font>";
                            if (!empty($mT005['T005_Prazo_Entrega'])) {
                            echo "<br /><font class=fonte style=\"font-size:12px\">Entrega: <b>".$mT005['T005_Prazo_Entrega']."</b></font>";
                            }
                            if (!empty($mT005['T005_Ordem_Compra'])) {
                            echo "<br /><font class=fonte style=\"font-size:12px\">OC Cliente: <b>".$mT005['T005_Ordem_Compra']."</b></font>";
                            }
                            if (!empty($mT005['T005_Data_Entrega'])) {
                            $mT005['T005_Data_Entrega']=gCorrigeData($mT005['T005_Data_Entrega']);
                            echo "<br /><font class=fonte style=\"font-size:12px;\">Entrega: <b>".$mT005['T005_Data_Entrega']."</b></font>";
                            }
                            if (!empty($mT005['T005_Canal_Vendas_Ecommerce'])) {
                                echo "<br /><font class=fonte style=\"font-size:12px;\">Canal de vendas: <b>".$mT005['T005_Canal_Vendas_Ecommerce']."</b></font>";
                            }
                            
                        echo "</td></tr>";
                        echo "<tr><td colspan=2>";
                            if (!empty($mT005['T005_Observacao_1'])) {
                                echo "<br><font class='fonte' style='font-size:14px;background:yellow;'><b>{$mT005['T005_Observacao_1']}</b></font>";
                            }
                        echo "</td></tr>";
                        echo "<tr><td colspan=2>";
                            echo "<br /><font class=fonte style=\"font-size:12px\"><b>Itens: {$rowTotais['totalItens']} - Quantidade total: {$rowTotais['quantidadeTotal']} - Cubagem: {$rowTotais['cubagemTotal']} m3 - Peso Líquido: {$rowTotais['pesoliquidoTotal']} - Peso Bruto: {$rowTotais['pesobrutoTotal']}</b></font>";
                        echo "</td></tr>";
                        echo "</table>";
                        echo "</td>";
                    echo "</tr>";
                    echo "</table>";
                echo "</td>";
                echo "</tr>";
            echo "</table>";
            echo "</td>";
            echo "</tr>";

            //////////////////////////////////////////////////////////////////////////////////////////////
            // CORPO DO RELATORIO
            //////////////////////////////////////////////////////////////////////////////////////////////
            echo "<tr valign=top height=100% bgcolor=white>";
            echo "<td valign=top>";

                echo "<table align=center cellspacing=1 cellpadding=3 border=0 width=100% bgcolor=black valign=top>";

                echo "<tr bgcolor=#f4f4f4>";
                    echo "<td align='center' valign='center' width='30'";
                    echo "<font class='fonte'><b>Id</b></font>";
                    echo "</td>";

                    if($g['C031']['exibirFotoImpressaoVenda'] == 'S'){
                        echo "<td align='center' valign='center' width='30'";
                        echo "<font class='fonte'><b>Foto</b></font>";
                        echo "</td>";
                    }

                    echo "<td align='center' valign='center' width='30'";
                    echo "<font class='fonte'><b>Código</b></font>";
                    echo "</td>";

                    echo "<td align='center' valign='center' width='30'";
                    echo "<font class='fonte'><b>Cód.Cli</b></font>";
                    echo "</td>";

                    echo "<td align='center' valign='center' width='30'";
                    echo "<font class='fonte'><b>Cód.Forn</b></font>";
                    echo "</td>";

                    echo "<td align='left' valign='center'>";
                    echo "<font class='fonte'><b>Descrição Produto</b></font>";
                    echo "</td>";

                    echo "<td align='left' valign='center'>";
                    echo "<font class='fonte'><b>Marca</b></font>";
                    echo "</td>";

                    echo "<td align='center' valign='center' width='20'>";
                    echo "<font class='fonte'><b>Un</b></font>";
                    echo "</td>";

                    echo "<td align='center' valign='center' width='40'>";
                    echo "<font class='fonte'><b>Quant.</b></font>";
                    echo "</td>";

                    echo "<td align='center' valign='center' width='40'>";
                    echo "<font class='fonte'><b>Quant. Separado</b></font>";
                    echo "</td>";

                    echo "<td align='center' valign='center' width='40'>";
                    echo "<font class='fonte'><b>Estoque Atual</b></font>";
                    echo "</td>";

                    if($mT005['T005_Flag_Status'] == '10'){
                        echo "<td align='center' valign='center' width='40'>";
                        echo "<font class='fonte'><b>Separado</b></font>";
                        echo "</td>";
                    }
                    
                    echo "<td align='center' valign='center' width='100'>";
                    echo "<font class='fonte'><b>Local</b></font>";
                    echo "</td>";

                echo "</tr>";


                $cClausula_Limit = $cClausula;
                $Resultado_Itens = mysqli_query($cClausula_Limit);

                $Sequencial=0;
                $nTotal_Linhas_Impressas=0;

                while ($mResultado=mysql_fetch_array($Resultado_Itens)) {

                $mResultado['T006_Descricao_Produto']=stripslashes($mResultado['T006_Descricao_Produto']);

                echo "<tr bgcolor=white height=25>";

                    echo "<td align='center' valign='center'>";
                    echo "<font class='fonte'>{$mResultado['T006_Id']}</font>";
                    echo "</td>";

                    if($g['C031']['exibirFotoImpressaoVenda'] == 'S'){
                    $foto = cad002FotoProduto($mResultado['D001_Id'],60);
                    echo "<td align='center' valign='center'>";
                        echo $foto;
                    echo "</td>";
                    }


                    echo "<td align='center' valign='center' width='30'>";
                    echo "<font class='fonte'>{$mResultado['T006_Codigo_Produto']}<br>{$mResultado['D049_Modelo']}</font>";
                    echo "</td>";

                    echo "<td align='center' valign='center' width='30'>";
                    echo "<font class='fonte'>{$mResultado['T006_Codigo_Substituto']}</font>";
                    echo "</td>";

                    echo "<td align='center' valign='center' width='30'>";
                    echo "<font class='fonte'>{$mResultado['Cod_Forn']}</font>";
                    echo "</td>";

                    echo "<td align='left' valign='center'>";
                    echo "<font class='fonte'><b>{$mResultado['T006_Descricao_Produto']}</b></font>";
                    $sqlT204 = mysqli_query("SELECT * FROM T204 WHERE T204_T006_Id = '{$mResultado['T006_Id']}'");
                    if(mysql_num_rows($sqlT204) > 0){
                        while($rowT204 = mysql_fetch_array($sqlT204)){
                        echo "<br><font class='fonte'>{$rowT204['T204_Descricao']} - ".number_format($rowT204['T204_Valor_Unitario'],2,',','.')." - {$rowT204['T204_Observacao']}</font>";
                        }	
                    }
                    /*
                    if (!empty($mResultado['D003_Imagem'])) {
                        $mResultado['D003_Imagem'] = explode(".", $mResultado['D003_Imagem']);
                        $dir = "/hardness3/dados_usuarios/{$Banco_Dados}/produtos/linhas/{$mResultado['D003_Imagem'][0]}-100.{$mResultado['D003_Imagem'][1]}";
                        echo "<br /><img src=\"{$dir}\" />";
                    }
                    */
                    
                    echo "</td>";

                    echo "<td align='left' valign='center'>";
                    echo "<font class='fonte'>{$mResultado['D082_Marca']}</font>";
                    echo "</td>";

                    echo "<td align='left' valign='center'>";
                    echo "<font class='fonte'>{$mResultado['D037_Unidade']}</font>";
                    echo "</td>";

                    echo "<td align='center' valign='center' width='40'>";
                    echo "<font class='fonte'><b><big>".number_format($mResultado['T006_Quantidade'],2,',','.')."</big></b></font>";
                    echo "</td>";

                    echo "<td align='center' valign='center' width='40'>";
                    if ($mResultado['T006_Quantidade_Separacao'] > 0) {
                        $separado = true;
                        echo "<font class='fonte' style='font-size: 15px; font-weight: bold;'>".number_format($mResultado['T006_Quantidade_Separacao'],2,',','.')."</font>";
                    } else {
                        echo "<font class='fonte'>".number_format($mResultado['T006_Quantidade_Separacao'],2,',','.')."</font>";
                    }
                    echo "</td>";

                    echo "<td align='center' valign='center' width='40'>";
                    echo "<font class='fonte'>".number_format($mResultado['D009_Quantidade_Estoque_Real'],2,',','.')."</font>";
                    echo "</td>";

                    if($mT005['T005_Flag_Status'] == '10'){
                        echo "<td align='center' valign='center' width='40'>";
                        echo "<font class='fonte'><b><big>".number_format($mResultado['Separado'],2,',','.')."</big></b></font>";
                        echo "</td>";
                    }

                    echo "<td align='left' valign='center'>";
                    echo "<font class='fonte'>";
                        $T066Ids = $mResultado['T066_Id_Mais'];
                        $T066=mysqli_query("SELECT T066_Id,
                                                   T066_Quantidade_Estoque,
                                                   D004_Local,
                                                   T066A_Data_Validade,
                                                   D001A_Flag_Validade 
                                              FROM T066 
                                         LEFT JOIN D004 ON D004_Id=T066_D004_Id 
                                         LEFT JOIN T066A ON T066_Id = T066A_T066_Id
                                         LEFT JOIN D009 ON D009_Id = T066_D009_Id
                                         LEFT JOIN D049 ON D049_Id = D009_D049_Id
                                         LEFT JOIN D001 ON D001_Id = D049_D001_Id
                                         LEFT JOIN D001A ON D001_Id = D001A_D001_Id
                                             WHERE T066_D009_Id='{$mResultado['T006_D009_Id']}' 
                                          ORDER BY D004_Local");
                        if(mysql_num_rows($T066) > 1){

                            echo "<table cellspacing=0 border=0 cellpadding=0 class=fonte>";    

                            while($mT066=mysql_fetch_array($T066)){

                                $dataValidade = '';
                                if($mT066['D001A_Flag_Validade'] == 'S'){
                                    log("qual é a validade ".$mT066['T066A_Data_Validade']." e o local".$mT066['D004_Local']);
                                   if($mT066['T066A_Data_Validade'] != '0000-00-00' || $mT066['T066A_Data_Validade'] != ''){
                                        $dataValidade = '(Val:'.gCorrigeData($mT066['T066A_Data_Validade']).')';
                                    } else{
                                        $dataValidade = '(Val:Não inform.)';
                                    }
                                }

                                $mT066['T066_Quantidade_Estoque'] = number_format($mT066['T066_Quantidade_Estoque'],2,',','.');
                                $style = ($mResultado['T006_T066_Id'] == $mT066['T066_Id']) ? 'font-weight: bold;' : '';
                                echo "<tr style='{$style}'><td>{$mT066['D004_Local']}<td><td>&nbsp;&nbsp;&nbsp;</td><td>{$mT066['T066_Quantidade_Estoque']}</td><td>&nbsp;{$dataValidade}</td>";
                            }
                            echo "</table>";
                        } else {
     
                            $mT066=mysql_fetch_array($T066);

                            $dataValidade = '';
                            if($mT066['D001A_Flag_Validade'] == 'S'){
                                log("qual é a validade ".$mT066['T066A_Data_Validade']." e o local".$mT066['D004_Local']);
                               if($mT066['T066A_Data_Validade'] != '0000-00-00' || $mT066['T066A_Data_Validade'] != ''){
                                    $dataValidade = '(Val:'.gCorrigeData($mT066['T066A_Data_Validade']).')';
                                } else{
                                    $dataValidade = '(Val:Não inform.)';
                                }
                            }
                            echo $mT066['D004_Local'].' '.$dataValidade;
                        }
                    echo "</font>";
                    echo "</td>";
                echo "</tr>";
                $nTotal_Linhas_Impressas++;
                }
            echo "</table></td></tr>";

            if ($Moeda=='D') {
                $mT005['T005_Valor_Total_Produtos']=$mT005['T005_Valor_Total_Produtos']/$mT005['T005_Valor_Dolar'];
                $mT005['T005_Valor_Frete']=$mT005['T005_Valor_Frete']/$mT005['T005_Valor_Dolar'];
                $mT005['T005_Valor_Mao_Obra']=$mT005['T005_Valor_Mao_Obra']/$mT005['T005_Valor_Dolar'];
                $mT005['T005_Valor_Despesas_Acessorias']=$mT005['T005_Valor_Despesas_Acessorias']/$mT005['T005_Valor_Dolar'];
                $mT005['T005_Valor_Desconto']=$mT005['T005_Valor_Desconto']/$mT005['T005_Valor_Dolar'];
                $mT005['T005_Valor_Total']=$mT005['T005_Valor_Total']/$mT005['T005_Valor_Dolar'];
                $mT005['T005_Valor_Total_IPI']=$mT005['T005_Valor_Total_IPI']/$mT005['T005_Valor_Dolar'];
            }

            if ($mT005['T005_Valor_Mao_Obra']>0) {

                echo "<tr bgcolor=white><td>";
                echo "<table align=center border='0' valign=top cellpading=0 cellspacing=0 width='$Width_Relatorio' bgcolor='white'>";

                if (!empty($mT005['T005_Mao_Obra_1'])) {
                echo "<tr bgcolor=white>";
                    echo "<td valign=top colspan=10>";
                        echo "<font class='fonte'>{$mT005['T005_Mao_Obra_1']}<br></font>";
                        echo "<font class='fonte'>{$mT005['T005_Mao_Obra_2']}<br></font>";
                        echo "<font class='fonte'>{$mT005['T005_Mao_Obra_3']}<br></font>";
                        echo "<font class='fonte'>{$mT005['T005_Mao_Obra_4']}</font>";
                    echo "</td>";
                echo "</tr>";

                }

                echo "</table>";

                echo "</td></tr>";

            }

            echo "<tr bgcolor=white><td>";


            ///////////////////////////////////////////////////////////////////////////////////
            // TOTALIZACAO
            ///////////////////////////////////////////////////////////////////////////////////

            $mC004['C004_Observacao_Rodape_Orcamento']=str_replace("\n","<br>",$mC004['C004_Observacao_Rodape_Orcamento']);

            echo "<font class=fonte>Sistema Hardness - www.hardness.com.br</font>";

            if ($separado && !empty($mT005['T005_Estoque_Box'])) {
                echo "<br/><br/><div style='text-align: center; font-size: 16px; font-weight: bold;'>Itens separados no box: {$mT005['T005_Estoque_Box']}</div>";
            }

            // codbarra

            /*include('../class/php-barcode.php');
            @mkdir('../dados_usuarios/codbarras');
            $im     = imagecreatetruecolor(733, 50);  
            $black  = ImageColorAllocate($im,0x00,0x00,0x00);  
            $white  = ImageColorAllocate($im,0xff,0xff,0xff);  
            imagefilledrectangle($im, 0, 0, 733, 50, $white);  
            $data = Barcode::gd($im, $black, 370, 60, 0, "code128", $T005_Id, 2, 110);  
            imagepng($im, "../dados_usuarios/codbarras/{$T005_Id}.barcode.png");
            */


            if(!empty($mT005['T005_C007_Id_Separou'])){
            $sqlC007Separador = mysqli_query("SELECT C007_Nome, T005_Data_Separacao_Pecas FROM T005 LEFT JOIN C007 ON C007_Id = T005_C007_Id_Separou WHERE T005_Id = '{$T005_Id}'");
            $rowC007Separador = mysql_fetch_array($sqlC007Separador);
            $dataHora = date('d/m/Y H:i:s', strtotime($rowC007Separador['T005_Data_Separacao_Pecas']));
            $separador = $rowC007Separador['C007_Nome'];
            } else {
            $sql = "SELECT * FROM T178 LEFT JOIN C007 ON C007_Id=T178_C007_Id WHERE T178_T005_Id='{$T005_Id}' AND T178_Descricao = 'Aguardando Separação' ORDER BY T178_Data DESC LIMIT 1";
            $resT178 = mysqli_query($sql);
            $rowT178 = mysql_fetch_assoc($resT178);
            $dataHora = date('d/m/Y H:i:s', strtotime($rowT178['T178_Data']));
            $separador = $rowT178['C007_Nome'];
            }


            echo <<<EOT
                <div class="fonte" style="fonts-size: 12px">
                <hr style="border-top: dashed 2px; margin: 55px 0 0px;" />
                <div style="margin: 15 0 0 0; padding: 0; font-weight: bold; font-size: 12px; text-align: center;">CANHOTO DE SEPARAÇÃO PARA CONFERÊNCIA</div><br/>
                <!--<img src="/hardness/class/barcodegen/html/image.php?filetype=PNG&dpi=72&scale=2&rotation=0&font_family=Arial.ttf&font_size=8&text={$T005_Id}&thickness=17&start=NULL&code=BCGcode128" style="margin: 10px 0 0 40%;" />-->
                <div style="float: right; margin: 20px 60px;">
EOT;
            b237_fbarcode(str_pad($T005_Id, 10, "0", STR_PAD_LEFT));
            echo <<<EOT
                </div>
                <table cellspacing=6>
                <tr>
                    <td class="fonte" style="font-size: 12px">Pedido:&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td class="fonte" style="font-size: 12px; font-weight: bold;">{$T005_Id}</td>
                </tr>
                <tr>
                    <td class="fonte" style="font-size: 12px">Separador:&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td class="fonte" style="font-size: 12px; font-weight: bold;">{$separador}</td>
                </tr>
                <tr>
                    <td class="fonte" style="font-size: 12px">Data/Hora Separação:&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td class="fonte" style="font-size: 12px; font-weight: bold;">{$dataHora}</td>
                </tr>
                </table>
                </div>
                </table>
                </table>
                </table>                
EOT;
            if(count($T005_Id_Array) > 1 && end($T005_Id_Array) !== $T005_Id) {
                echo <<<EOT
                    <div class="pagebreak"> </div>
                EOT;
            }

            if($g['C031']['modeloExpedicao'] == 2){
                // Só da UPDATE no modelo 2 porque só lista no grid de expedicao esta campo JSON para o modelo 2
                $T005A = mysql_query("SELECT T005A_Id FROM T005A WHERE T005A_T005_Id = '{$T005_Id}'");
                if(mysql_num_rows($T005A) <= 0 ){
                    mysql_query("insert into T005A(T005A_T005_Id) VALUES ('{$T005_Id}')");
                }
                mysql_query("UPDATE T005A
                                SET T005A_JSON = JSON_SET(IFNULL(T005A_JSON, '{}'), -- Se o campo JSON for nulo ou ausente, cria um JSON vazio
                                          '$.Quantidade_Impressao',
                                          CAST(IFNULL(JSON_UNQUOTE(JSON_EXTRACT(T005A_JSON, '$.Quantidade_Impressao')), 0) + 1 AS SIGNED)
                                          )
                              WHERE T005A_T005_Id = '{$T005_Id}'");
            }
        }
        
        echo "</body></html>";
    break;

    case 'minutaDespacho':
        /*
        *	Raquel Pazini Cardoso - 05/04/2019
        *	Imprimir Minuta de Despacho
        */
    
        $T007_Id = isset($_REQUEST['T007_Id']) ? $_REQUEST['T007_Id'] : false;
        $Cor_Normal="#ffffff";
    
        $Linhas_Normais=30;
        $Linhas_Ultima_Pagina=25;
        $Tamanho_Maximo_Tabela=580;
        $Tamanho_Minimo_Tabela=400;
        $Width_Relatorio='98%';
    
    
        ////////////////////////////////////////////////////////////
        // VERIFICA QUANTAS PAGINAS SERÃO IMPRESSAS
        ////////////////////////////////////////////////////////////
        // Encontra o total das linhas a ser impressa
        $Total_Linhas = 1;
    
        // Encontra o numero de paginas cheias
        $Total_Paginas = intval($Total_Linhas / $Linhas_Normais);
        // Encontra o numero de itens restantes
        if ($Total_Paginas>=1) {
          $Resto = ($Total_Linhas - ($Total_Paginas * $Linhas_Normais));
        } else {
          $Resto = $Total_Linhas;
        }
    
        // Verifica caso o resto seja maior que o número máximo de itens que cabe na última folha
        if ($Resto > $Linhas_Ultima_Pagina) {
          // Sendo maior soma mais duas folhas
          $Total_Paginas += 2 ;
        } else {
          // Sendo menor soma mais uma folha
          $Total_Paginas += 1 ;
        }
    
        // Inicializa Variavel utilizada no limit da consulta dos itens
        $Inicio=0;
        $Pagina_Atual=0;
    
        $Pagina_Atual++;
    
        $cSql=mysql_query("SELECT C004_Logo_Marca,
                                  C004_Observacao_Logo,
                                  C004_Nome_Empresa,
                                  C004_Logradouro,
                                  C004_Numero,
                                  C004_Complemento,
                                  C004_Cep,
                                  C004_Cidade,
                                  C004_Estado,
                                  C004_Telefone,
                                  C004_Fax,
                                  C004_Site,
                                  C004_Bairro,
                                  D018_UF, 
                                  D020_Nome_Cidade
                             FROM C004 
                        LEFT JOIN D018 ON D018_Id = C004_D018_Id
                        LEFT JOIN D020 ON D020_Id = C004_D020_Id
                            WHERE C004_Id = {$g['empresaAtual']}");
        $mEmpresa=mysql_fetch_array($cSql);
    
        $sqlT007 = <<<EOT
                    SELECT
                        T007_T005_Id,
                        D022_Nome_Empresa,
                        D024_Cnpj,
                        D024_Nome_Empresa,
                        D024_Endereco,
                        D024_Bairro,
                        D024_Numero,
                        D024_Cep,
                        D024_DDD_Telefone_1,
                        D024_Telefone_1,
                        T007_Numero_Nota_Fiscal,
                        T007_Peso_Bruto,
                        T007_Valor_Frete,
                        T007_Quantidade_Volumes,
                        T007_D148_Id_Entrega,
                        D020D.D020_Nome_Cidade AS D020D_D020_Nome_Cidade,
                        D020T.D020_Nome_Cidade AS D020T_D020_Nome_Cidade,
                        D018T.D018_UF AS D018T_D018_UF,
                        D018D.D018_UF AS D018D_D018_UF,
                        D148_Rua,
                        D148_Numero,
                        D148_Bairro,
                        D148_Cep,
                        D020E.D020_Nome_Cidade AS D020E_D020_Nome_Cidade,
                        D018E.D018_UF AS D018E_D018_UF
                    FROM T007
                    LEFT JOIN D024 ON D024_Id=T007_D024_Id
                    LEFT JOIN D148 ON D148_Id=T007_D148_Id_Entrega
                    LEFT JOIN D018 AS D018D ON D018D.D018_Id=D024_D018_Id
                    LEFT JOIN D020 AS D020D ON D020D.D020_Id=D024_D020_Id
                    LEFT JOIN D006 ON D006_Id=T007_D006_Id
                    LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
                    LEFT JOIN D021 ON D021_Id=D024_D021_Id
                    LEFT JOIN D022 ON D022_Id=T007_D022_Id
                    LEFT JOIN D018 AS D018T ON D018T.D018_ID=D022_D018_ID
                    LEFT JOIN D020 AS D020T ON D020T.D020_ID=D022_D020_ID
                    LEFT JOIN D018 AS D018E ON D018E.D018_ID=D148_D018_ID
                    LEFT JOIN D020 AS D020E ON D020E.D020_ID=D148_D020_ID
                    WHERE T007_Id = '{$T007_Id}'
EOT;
    
        $resT007 = mysql_query($sqlT007);
        $rowT007 = mysql_fetch_assoc($resT007);
    
        if($rowT007['T007_D148_Id_Entrega']<=0){
            $ruaDestinatario    = $rowT007['D024_Endereco'];
            $numeroDestinatario = $rowT007['D024_Numero'];
            $bairroDestinatario = $rowT007['D024_Bairro'];
            $cepDestinatario    = $rowT007['D024_Cep'];
            $UFDestinatario     = $rowT007['D018D_D018_UF'];
            $cidadeDestinatario = $rowT007['D020D_D020_Nome_Cidade'];
        }else{
            $ruaDestinatario    = $rowT007['D148_Rua'];
            $numeroDestinatario = $rowT007['D148_Numero'];
            $bairroDestinatario = $rowT007['D148_Bairro'];
            $cepDestinatario    = $rowT007['D148_Cep'];
            $UFDestinatario     = $rowT007['D018E_D018_UF'];
            $cidadeDestinatario = $rowT007['D020E_D020_Nome_Cidade'];
        }
    
        echo "<html>";
        echo "<head>";
        echo "<title>Minuta de Despacho - Pedido Nr {$rowT007['T007_T005_Id']}</title>";
        echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
        echo "<link rel=stylesheet href=\"../classes/classe_tamanho_pequeno_firefox.css\" type=\"text/css\">";
    
        echo "<style>
            .fonte
            {
                font-family:arial,verdana;
                font-size:11px;
                font-weight:bold;
            }
        </style>";
    
        $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
        $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
               echo "<table align=center border='0' valign=top cellpading=0 cellspacing=0 width='1000' style='border-style: dashed; border-color:black; border-bottom-width: 2px; border-top-width: 0;border-right-width: 0; border-left-width: 0;'>";
                  echo "<tr bgcolor=white>";
                     echo "<td valign=top>";
                           echo "<table align=center border='0' cellspacing=0 cellpading=0 width='100%' height=10 valign=top style='border:0px solid'>";
                            echo "<tr>";
                                 echo "<td bgcolor=white width=80%>";
                                    echo "<table cellspacing=0 cellpading=0 border='0' align=center width=100% height=10>";
                                        echo "<tr>";
                                            //-- $Diretorio_Imagens é definido no arquivo include/pasta_usuario.inc
                                            echo "<td bgcolor=white align=center valign=center width=200 style='border:0px solid'>";
                                                echo "<img src='$caminhoImagem' align=center width=95>";
                                                echo "<BR>";
                                                echo "<font class=fonte>$mEmpresa[C004_Observacao_Logo]</font>";
                                            echo "</td>";
                                            echo "<td align=left style='border:0px solid'>";
                                                echo "<table cellspacing=0 cellpading=0 border='0' align=left height=20 >";
                                                    echo "<tr>";
                                                        echo "<td><font class=fonte style='font-size:12px'><b>$mEmpresa[C004_Nome_Empresa]</b></font></td>";
                                                    echo "</tr>";
                                                    echo "<tr>";
                                                        echo "<td><font class=fonte>$mEmpresa[C004_Logradouro], $mEmpresa[C004_Numero] $mEmpresa[C004_Complemento]</font></td>";
                                                    echo "</tr>";
                                                    echo "<tr>";
                                                        echo "<td><font class=fonte>$mEmpresa[C004_Cep] - $mEmpresa[D020_Nome_Cidade] - $mEmpresa[D018_UF]</font></td>";
                                                    echo "</tr>";
                                                    echo "<tr>";
                                                        echo "<td><font class=fonte>Fone/Fax: $mEmpresa[C004_Telefone] Fax: $mEmpresa[C004_Fax]</font></td>";
                                                    echo "</tr>";
                                                    echo "<tr>";
                                                        echo "<td><font class=fonte>$mEmpresa[C004_Site]</font></td>";
                                                    echo "</tr>";
                                                echo "</table>";
                                            echo "</td>";
                                        echo "</tr>";
                                    echo "</table>";
                                echo "</td>";
                            echo "</tr>";
    
                            echo "<table align=left border='0' cellspacing=0 cellpading=0 width='70%' height=10 valign=top style='border:0px solid'>";
                                echo "<tr>";
                                    echo "<td bgcolor=white width=50%>";
                                        echo "<table cellspacing=0 cellpading=0 border='0' align=center width=100% height=10>";
                                            echo "<tr>";
                                                echo "<td style='text-align: center;'><font class='fonte' style='font-size:16px'><br/><b> MINUTA DE DESPACHO</b><br/></font></td>";
                                            echo "</tr>";
                                        echo "</table>";
                                    echo "</td>";
                                echo "</tr>";
                            echo "</table>";
    
                            //echo "<td style='text-align: center;'><font class='fonte' style='font-size:14px'><br/><b> MINUTA DE DESPACHO</b><br/></font></td>";
                            echo "<tr bgcolor=white>";
                                echo "<td colspan=2>";
                                    echo "<table cellpadding=7 width=100% border=0>";
                                        echo "<tr bgcolor=white>";
                                            echo "<td colspan=3>";
                                                   echo "<table width=100% border=0>";
                                                    echo "<tr>";
                                                        echo "<td style='width:10%'><font class='fonte' style='font-size:13px'><b>Transportadora: </b></font></td>";
                                                        echo "<td style='width:90%'><font class='fonte' style='font-size:14px'>{$rowT007['D022_Nome_Empresa']}</font></td>";
                                                       echo "</tr>";
                                                       echo "<tr/><td> <br/></td></tr>";
    
                                                       echo "<tr>";
                                                           echo "<td font class='fonte' style='font-size:13px'><b>Remetente: </b></font></td>";
                                                        echo "<td  font class='fonte' style='font-size:14px'>{$mEmpresa['C004_Nome_Empresa']} </td>";
                                                    echo "</tr>";
                                                    echo "<tr>";
                                                        echo "<td font class='fonte' style='font-size:13px'></font></td>";
                                                        echo "<td font class='fonte' style='font-size:14px'>{$mEmpresa['C004_Logradouro']} {$mEmpresa['C004_Numero']}</font><br> </td>";
                                                    echo "</tr>";
                                                    echo "<tr>";
                                                        echo "<td font class='fonte' style='font-size:13px'></font></td>";
                                                        echo "<td font class='fonte' style='font-size:14px'>{$mEmpresa['C004_Bairro']} - {$mEmpresa['C004_Cep']}</font><br></td>";
                                                    echo "</tr>";
                                                    echo "<tr>";
                                                        echo "<td font class='fonte' style='font-size:13px'></font></td>";
                                                        echo "<td><font class='fonte' style='font-size:14px'>{$mEmpresa['D020_Nome_Cidade']} - {$mEmpresa['D018_UF']}</font><br></td>";
                                                       echo "</tr>";
                                                       echo "<tr/><td> <br/></td></tr>";
    
                                                       echo "<tr>";
                                                           echo "<td font class='fonte' style='font-size:13px'>Destinatário</font></td>";
                                                           echo "<td font class='fonte' style='font-size:14px'>{$rowT007['D024_Nome_Empresa']}</font><tr>";
                                                       echo "</tr>";
                                                       echo "<tr>";
                                                           echo "<td font class='fonte' style='font-size:13px'><b>CNPJ / CPF: </b></font></td>";
                                                           echo "<td font class='fonte' style='font-size:14px'>{$rowT007['D024_Cnpj']} {$rowT007['D024_Cpf']}</font><br></td>";
                                                       echo "</tr>";
                                                       echo "<tr>";
                                                           echo "<td font class='fonte' style='font-size:13px'><b>Área: </b></font></td>";
                                                           echo "<td font class='fonte' style='font-size:14px'></font><tr>";
                                                       echo "</tr>";
                                                       echo "<tr>";
                                                           echo "<td font class='fonte' style='font-size:13px'><b>Endereço: </b> </font></td>";
                                                           echo "<td font class='fonte' style='font-size:14px'>{$ruaDestinatario}, {$numeroDestinatario}</font><tr>";
                                                       echo "</tr>";
                                                       echo "<tr>";
                                                           echo "<td font class='fonte' style='font-size:13px'><b>Bairro: </b> </font></td>";
                                                           echo "<td font class='fonte' style='font-size:14px'>{$bairroDestinatario}</font><tr>";
                                                       echo "</tr>";
                                                       echo "<tr>";
                                                           echo "<td font class='fonte' style='font-size:13px'><b>Cidade: </b> </font></td>";
                                                           echo "<td font class='fonte' style='font-size:14px'>{$cidadeDestinatario}</font><tr>";
                                                       echo "</tr>";
                                                       echo "<tr>";
                                                           echo "<td font class='fonte' style='font-size:13px'><b>UF: </b> </font></td>";
                                                           echo "<td font class='fonte' style='font-size:14px'>{$UFDestinatario}</font><tr>";
                                                       echo "</tr>";
                                                       echo "<tr>";
                                                           echo "<td font class='fonte' style='font-size:13px'><b>CEP: </b> </font></td>";
                                                           echo "<td font class='fonte' style='font-size:14px'>{$cepDestinatario}</font><tr>";
                                                       echo "</tr>";
                                                       echo "<tr>";
                                                           echo "<td font class='fonte' style='font-size:13px'><b>Telefone: </b> </font></td>";
                                                           echo "<td font class='fonte' style='font-size:14px'>({$rowT007['D024_DDD_Telefone_1']}) {$rowT007['D024_Telefone_1']}</font><tr>";
                                                       echo "</tr>";
                                                       echo "<tr/><td> <br/></td></tr>";
                                                       echo "<tr>";
                                                           echo "<td font class='fonte' style='font-size:13px'><b>Notas Fiscais: </b> </font></td>";
                                                           echo "<td font class='fonte' style='font-size:17px'>{$rowT007['T007_Numero_Nota_Fiscal']}</font><tr>";
                                                       echo "</tr>";
                                                       echo "<tr>";
                                                           echo "<td font class='fonte' style='font-size:13px'><b>Peso: </b> </font></td>";
                                                           echo "<td font class='fonte' style='font-size:17px'>{$rowT007['T007_Peso_Bruto']}</font><tr>";
                                                       echo "</tr>";
                                                       echo "<tr>";
                                                           echo "<td font class='fonte' style='font-size:13px'><b>Frete: </b> </font></td>";
                                                           echo "<td font class='fonte' style='font-size:17px'>{$rowT007['T007_Valor_Frete']}</font><tr>";
                                                       echo "</tr>";
                                                       echo "<tr>";
                                                           echo "<td font class='fonte' style='font-size:13px'><b>Volumes: </b> </font></td>";
                                                           echo "<td font class='fonte' style='font-size:17px'>{$rowT007['T007_Quantidade_Volumes']}</font><tr>";
                                                       echo "</tr>";
                                                       echo "<tr/><td> <br/></td></tr>";
                                                       echo "<tr/><td> <br/></td></tr>";
                                                echo "</table>";
                                                echo "<table>";
                                                    echo "<tr>";
                                                           echo "<td font class='fonte' style='font-size:13px'><b>Data: ___ / ___ / ______</b></font></td>";
                                                           echo "<td font class='fonte' style='font-size:13px'><b>&nbsp;&nbsp;Assinatura: ___________________</b></font><tr>";
                                                           echo "<tr/><td> <br/></td></tr>";
                                                       echo "</tr>";
                                                   echo "</table>";
                                            echo "</td>";
                                        echo "</tr>";
                                    echo "</table>";
                                echo "</td>";
                            echo "</tr>";
                        echo "</table>";
                    echo "</td>";
                echo "</tr>";
            echo "</table>";

    break;

	/**
	 * Etiqueta de volume
	 */
	case 'etiquetaVolume':
        $impressao = "";
        $T005_Id   = isset($_REQUEST['T005_Id']) ? $_REQUEST['T005_Id'] : false;
        $T314_Id   = isset($_REQUEST['T314_Id']) ? $_REQUEST['T314_Id'] : false;

        if (true) {
            $impressao .= "<html>";
            $impressao .=   "<head>";
            $impressao .=     "<title>Etiquetas de Volume</title>";
            $impressao .=     "<meta http-equiv='Content-Type' content='text/html' charset='UTF-8'>";
            $impressao .=     "<style>";
            $impressao .=       "@media print {";
            $impressao .=         ".pagebreak { page-break-before: always;}";
            $impressao .=       "} ";
            $impressao .=       ".linha { ";
            $impressao .=         " width: 100%; ";
            $impressao .=       "} ";
            $impressao .=       ".direita { ";
            $impressao .=         " position: absolute; ";
            $impressao .=         " right: 15px; ";
            $impressao .=       "} ";
            $impressao .=       ".titulo { ";
            $impressao .=         " font-weight: bold; ";
            $impressao .=       "} ";
            $impressao .=       ".divisor { ";
            $impressao .=         " border-top: 1px solid; ";
            $impressao .=       "} ";
            $impressao .=       "body { ";
            $impressao .=         " font-size: 12px; ";
            $impressao .=         " font-family: Arial, Helvetica, sans-serif; ";
            $impressao .=       "} ";
            $impressao .=       "table { ";
            $impressao .=         " font-size: 12px; ";
            $impressao .=         " font-family: Arial, Helvetica, sans-serif; ";
            $impressao .=       "} ";
            $impressao .=       ".pedido { ";
            $impressao .=         " font-size: 25px; ";
            $impressao .=         " font-family: Arial, Helvetica, sans-serif; ";
            $impressao .=       "} ";
            $impressao .=       ".cliente { ";
            $impressao .=         " font-size: 16px; ";
            $impressao .=         " font-family: Arial, Helvetica, sans-serif; ";
            $impressao .=       "} ";
            $impressao .=     "</style>";
            $impressao .=   "</head>";
            $impressao .=   "<body>";

            if ($T005_Id > 0) {
                $impressao .= etiquetaVolumePedido($T005_Id);
            }
    
            if ($T314_Id > 0) {
                $impressao .= etiquetaVolume($T314_Id);
            }

            $impressao .=   "</body>";
            $impressao .= "</html>";
        }
        echo $impressao;
    break;
}

function etiquetaVolumePedido($T005_Id) {
    $impressao = "";

    $sqlVolumes  = " SELECT T314.T314_Id ";
    $sqlVolumes .=   " FROM T314 ";
    $sqlVolumes .=  " WHERE T314.T314_T005_Id = '{$T005_Id}' ";
    $sqlVolumes .= "  ORDER BY T314.T314_Sequencia ASC ";
    $queryVolumes = mysql_query($sqlVolumes);
    $numEtiquetas = mysql_num_rows($queryVolumes);

    if ($numEtiquetas > 0) {
        $etiqueta   = 0;

        while ($dadosVolume = mysql_fetch_array($queryVolumes)) {
            $etiqueta += 1;
            $impressao .= etiquetaVolume($dadosVolume['T314_Id']);

            if ($etiqueta < $numEtiquetas) {
                $impressao .= '<div class="pagebreak" style="margin-bottom: 5px;"></div>';
            }
        }
    }

    return $impressao;
}

function etiquetaVolume($T314_Id) {
    $dadosEtiqueta = dadosEtiqueta($T314_Id);
    $cidade = !empty($dadosEtiqueta[0]['ENTREGA_CIDADE']) ? $dadosEtiqueta[0]['ENTREGA_CIDADE'] : $dadosEtiqueta[0]['CLIENTE_CIDADE'];

    $etiqueta  = "<div style='padding: 0px 10px;'>";

    $etiqueta .=   "<div class='linha'>";
    $etiqueta .=     "<span class='titulo'>{$dadosEtiqueta[0]['EMPRESA_NOME']}</span>";
    $etiqueta .=     "<span class='direita pedido'><span class='titulo'>PED: </span><span>{$dadosEtiqueta[0]['PEDIDO_NUMERO']}</span><span> - {$dadosEtiqueta[0]['VOLUME_SEQUENCIA']}/{$dadosEtiqueta[0]['VOLUME_TOTAL']}</span></span>";
    $etiqueta .=   "</div>";
    $etiqueta .=   "<div class='linha'>";
    $etiqueta .=     "<span><span class='titulo'>Roteiro: </span><span>{$dadosEtiqueta[0]['PEDIDO_ROTEIRO']}</span></span>";
    $etiqueta .=   "</div>";
    $etiqueta .=   "<div class='divisor'></div>";
    $etiqueta .=   "<div class='linha'>";
    $etiqueta .=     "<span><span class='titulo'>Transportadora: </span><span>{$dadosEtiqueta[0]['TRANSPORTADORA_NOME']}</span></span>";
    $etiqueta .=   "</div>";
    $etiqueta .=   "<div class='divisor'></div>";
    $etiqueta .=   "<div class='linha'>";
    $etiqueta .=     "<span><span class='titulo'>Cliente: </span><span class='cliente'>{$dadosEtiqueta[0]['CLIENTE_NOME']}</span></span>";
    $etiqueta .=   "</div>";
    $etiqueta .=   "<div class='linha'>";
    $etiqueta .=     "<span><span class='titulo'>Cidade: </span><span>{$cidade}</span></span>";
    $etiqueta .=   "</div>";
    $etiqueta .=   "<table cellspacing='0' class='linha' style='padding: 0px;'>";
    $etiqueta .=     "<tr>";
    $etiqueta .=       "<td style='text-align: left;'><span class='titulo'>Separador 1: </span>{$dadosEtiqueta[0]['SEPARADOR_1']}</td>";
    $etiqueta .=       "<td style='text-align: left;'><span class='titulo'>Separador 2: </span>{$dadosEtiqueta[0]['SEPARADOR_2']}</td>";
    $etiqueta .=     "</tr>";
    $etiqueta .=   "</table>";
    $etiqueta .=   "<div class='divisor'></div>";

    $etiqueta .=   "<table class='linha'>";
    $etiqueta .=     "<thead>";
    $etiqueta .=       "<tr>";
    $etiqueta .=         "<th style='text-align: left;'>Codigo</th>";
    $etiqueta .=         "<th style='text-align: left;'>Produto</th>";
    $etiqueta .=         "<th style='text-align: right;'>Qtde</th>";
    $etiqueta .=       "</tr>";
    $etiqueta .=     "</thead>";

    $etiqueta .=     "<tbody>";
    foreach ($dadosEtiqueta as $produto) {
        $descricao  = !empty($produto['PRODUTO_DESCRICAO_RESUMIDA']) ? $produto['PRODUTO_DESCRICAO_RESUMIDA'] : $produto['PRODUTO_DESCRICAO_COMPLETA'];
        $quantidade = $produto['USA_CONVERSAO'] == 'S' ? intval($produto['PRODUTO_QUANTIDADE_CONVERSAO']) . " UN" : $produto['PRODUTO_QUANTIDADE'] . " " . $produto['UNIDADE_MEDIDA'];

        $etiqueta .=   "<tr>";
        $etiqueta .=     "<td style='text-align: left;'>{$produto['PRODUTO_CODIGO']}</td>";
        $etiqueta .=     "<td style='text-align: left;'>{$descricao}</td>";
        $etiqueta .=     "<td style='text-align: right;'>{$quantidade}</td>";
        $etiqueta .=   "</tr>";
    }
    $etiqueta .=     "</tbody>";
    $etiqueta .=   "</table>";

    $etiqueta .= "</div>";

    return $etiqueta;
}

function dadosEtiqueta($T314_Id) {
    global $g;
    $dadosEtiqueta = [];

    $sqlEtiqueta  = " SELECT C004.C004_Nome_Abreviado                    AS EMPRESA_NOME, ";
    $sqlEtiqueta .=        " T005.T005_Id                                AS PEDIDO_NUMERO, ";
    $sqlEtiqueta .=        " CAST('' AS CHAR)                            AS PEDIDO_ROTEIRO, ";
    $sqlEtiqueta .=        " D024.D024_Nome_Empresa                      AS CLIENTE_NOME, ";
    $sqlEtiqueta .=        " D020_Cliente.D020_Nome_Cidade               AS CLIENTE_CIDADE, ";
    $sqlEtiqueta .=        " D020_Entrega.D020_Nome_Cidade               AS ENTREGA_CIDADE, ";
    $sqlEtiqueta .=        " D022.D022_Nome_Empresa                      AS TRANSPORTADORA_NOME, ";
    $sqlEtiqueta .=        " T314.T314_Volume                            AS VOLUME_CODIGO, ";
    $sqlEtiqueta .=        " T314.T314_Sequencia                         AS VOLUME_SEQUENCIA, ";
    $sqlEtiqueta .=        " D109_Primeiro.D109_Nome                     AS SEPARADOR_1, ";
    $sqlEtiqueta .=        " D109_Segundo.D109_Nome                      AS SEPARADOR_2, ";
    $sqlEtiqueta .=        " D001.D001_Codigo_Produto                    AS PRODUTO_CODIGO, ";
    $sqlEtiqueta .=        " D001.D001_Descricao_Produto                 AS PRODUTO_DESCRICAO_COMPLETA, ";
    $sqlEtiqueta .=        " D001.D001_Descricao_Comercial               AS PRODUTO_DESCRICAO_RESUMIDA, ";
    $sqlEtiqueta .=        " T315.T315_Quantidade                        AS PRODUTO_QUANTIDADE, ";
    $sqlEtiqueta .=        " T315.T315_Quantidade_Conversao              AS PRODUTO_QUANTIDADE_CONVERSAO, ";
    $sqlEtiqueta .=        " D037.D037_Unidade                           AS UNIDADE_MEDIDA, ";
    $sqlEtiqueta .=        " IFNULL(D002A.D002A_Utiliza_Conversao, 'N')  AS USA_CONVERSAO, ";
    $sqlEtiqueta .=        " (SELECT COUNT(VOLUMES.T314_Id) AS VOLUMES ";
    $sqlEtiqueta .=        "    FROM T314 VOLUMES ";
    $sqlEtiqueta .=        "   WHERE VOLUMES.T314_T005_ID = T314.T314_T005_ID) AS VOLUME_TOTAL ";
    $sqlEtiqueta .=   " FROM T314 ";
    $sqlEtiqueta .=        " LEFT JOIN T005                  ON T005.T005_Id          = T314.T314_T005_Id ";
    $sqlEtiqueta .=        " LEFT JOIN D024                  ON D024.D024_Id          = T005.T005_D024_Id ";
    $sqlEtiqueta .=        " LEFT JOIN D148                  ON D148.D148_Id          = T005.T005_D148_Id_Entrega ";
    $sqlEtiqueta .=        " LEFT JOIN D022                  ON D022.D022_Id          = T005.T005_D022_Id ";
    $sqlEtiqueta .=        " LEFT JOIN D020 as D020_Cliente  ON D020_Cliente.D020_Id  = D024.D024_D020_Id ";
    $sqlEtiqueta .=        " LEFT JOIN D020 as D020_Entrega  ON D020_Entrega.D020_Id  = D148.D148_D020_Id ";
    $sqlEtiqueta .=        " LEFT JOIN T315                  ON T314.T314_Id          = T315.T315_T314_Id ";
    $sqlEtiqueta .=        " LEFT JOIN D009                  ON D009.D009_Id          = T315.T315_D009_Id ";
    $sqlEtiqueta .=        " LEFT JOIN D001                  ON D001.D001_Id          = D009.D009_D001_Id ";
    $sqlEtiqueta .=        " LEFT JOIN D002                  ON D002.D002_Id          = D001.D001_D002_Id ";
    $sqlEtiqueta .=        " LEFT JOIN D037                  ON D037.D037_Id          = D001.D001_D037_Id ";
    $sqlEtiqueta .=        " LEFT JOIN D002A                 ON D002.D002_Id          = D002A.D002A_D002_Id ";
    $sqlEtiqueta .=        " LEFT JOIN T238A                 ON T315.T315_Id          = T238A.T238A_T315_Id ";
    $sqlEtiqueta .=        " LEFT JOIN D109 as D109_Primeiro ON D109_Primeiro.D109_Id = T238A.T238A_D109_Id_Primeiro ";
    $sqlEtiqueta .=        " LEFT JOIN D109 as D109_Segundo  ON D109_Segundo.D109_Id  = T238A.T238A_D109_Id_Segundo ";
    $sqlEtiqueta .=        " LEFT JOIN C004                  ON C004.C004_Id          = '{$g['empresaAtual']}' ";
    $sqlEtiqueta .=    " WHERE T314_Id = '{$T314_Id}' ";
    $queryEtiqueta = mysql_query($sqlEtiqueta);

    while ($dados = mysql_fetch_array($queryEtiqueta)) {
       array_push($dadosEtiqueta, $dados);
    }

    return $dadosEtiqueta;
}

function b237_fbarcode($valor){

    $fino = 1 ;
    $largo = 3 ;
    $altura = 50 ;

    $barcodes[0] = "00110" ;
    $barcodes[1] = "10001" ;
    $barcodes[2] = "01001" ;
    $barcodes[3] = "11000" ;
    $barcodes[4] = "00101" ;
    $barcodes[5] = "10100" ;
    $barcodes[6] = "01100" ;
    $barcodes[7] = "00011" ;
    $barcodes[8] = "10010" ;
    $barcodes[9] = "01010" ;
    for($f1=9;$f1>=0;$f1--){ 
        for($f2=9;$f2>=0;$f2--){  
            $f = ($f1 * 10) + $f2 ;
            $texto = "" ;
            for($i=1;$i<6;$i++){ 
                $texto .=  substr($barcodes[$f1],($i-1),1) . substr($barcodes[$f2],($i-1),1);
            }
            $barcodes[$f] = $texto;
        }
    }
    //Desenho da barra
    //Guarda inicial
    echo <<<EOT
        <img src="{$g['pathRaiz']}/hardness/boleto/imagens/p.png" width="{$fino}" height="{$altura}" border=0>
        <img src="{$g['pathRaiz']}/hardness/boleto/imagens/b.png" width="{$fino}" height="{$altura}" border=0>
        <img src="{$g['pathRaiz']}/hardness/boleto/imagens/p.png" width="{$fino}" height="{$altura}" border=0>
        <img src="{$g['pathRaiz']}/hardness/boleto/imagens/b.png" width="{$fino}" height="{$altura}" border=0>
EOT;
    $texto = $valor ;
    if((strlen($texto) % 2) <> 0){
        $texto = "0" . $texto;
    }

    // Draw dos dados
    while (strlen($texto) > 0) {
        $i = round(b237_esquerda($texto,2));
        $texto = b237_direita($texto,strlen($texto)-2);
        $f = $barcodes[$i];
        for($i=1;$i<11;$i+=2){
            if (substr($f,($i-1),1) == "0") {
                $f1 = $fino ;
            }else{
                $f1 = $largo ;
            }
            echo "<img src='{$g['pathRaiz']}/hardness/boleto/imagens/p.png' width='{$f1}' height='{$altura}' border=0>";
            if (substr($f,$i,1) == "0") {
                $f2 = $fino ;
            }else{
                $f2 = $largo ;
            }
            echo "<img src='{$g['pathRaiz']}/hardness/boleto/imagens/b.png' width='{$f2}' height='{$altura}' border=0>";
        }
    }

    // Draw guarda final
    echo <<<EOT
        <img src="{$g['pathRaiz']}/hardness/boleto/imagens/p.png" width="{$largo}" height="{$altura}" border=0>
        <img src="{$g['pathRaiz']}/hardness/boleto/imagens/b.png" width="{$fino}" height="{$altura}" border=0>
        <img src="{$g['pathRaiz']}/hardness/boleto/imagens/p.png" width="1" height="{$altura}" border=0> 
EOT;

} //Fim da função

function b237_esquerda($entra,$comp){
    return substr($entra,0,$comp);
}

function b237_direita($entra,$comp){
    return substr($entra,strlen($entra)-$comp,$comp);
}

function cad002FotoProduto($D001_Id, $tamanhoPx=30){
    global $g, $confUsuario;

    $T172=mysql_query("SELECT * FROM T172 WHERE T172_D001_Id='{$D001_Id}' ORDER BY T172_Id desc limit 10;");

    $T144 = mysql_query("SELECT T144_Id, T144_Data_Cadastro, D001_Descricao_Produto, T144_Url, T144_Descricao, T144_Ultimo_Acesso, T144_Versao_Cache 
                            FROM T144 
                      LEFT JOIN D001 ON D001_Id=T144_D001_Id 
                          WHERE (T144_D001_Id = '{$D001_Id}') 
                        ORDER BY T144_Id DESC LIMIT 1;");
            
    if (mysql_num_rows($T172)>0) {
        $mT172=mysql_fetch_array($T172);

        $idFoto = $mT172['T172_Id'];
        $idProduto = $mT172['T172_D001_Id'];
        $pathinfo = pathinfo($mT172['T172_Nome_Arquivo']);
        $cArquivo = "{$g['pathRaiz']}dados_usuarios/{$confUsuario['dbDatabase']}/produtos/{$idProduto}/fotos/{$idFoto}.{$pathinfo['extension']}";
        
        $dir = $g['pathRaiz'] . "dados_usuarios/{$confUsuario['dbDatabase']}/produtos/{$idProduto}/fotos/{$idFoto}.{$pathinfo['extension']}";
        $dirFull = "/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/produtos/{$idProduto}/fotos/{$idFoto}.{$pathinfo['extension']}";
        $dirMini = "/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/produtos/{$idProduto}/fotos/{$idFoto}.{$pathinfo['extension']}";
        if (!file_exists($dir)){
            $dirMini = "/hardness3/static/img/indisponivel100x100.jpg";
            $dirFull = "/hardness3/static/img/indisponivel100x100.jpg";
        }
    } else if ($g['C031']['ImagemPrincipalB2B']=='3' && mysql_num_rows($T144)>0) {
        $mT144 = mysql_fetch_array($T144);

        $idProduto = $mT144['T144_Id'];
        $pathinfo = 'img';
        $dir     = $mT144['T144_Url'];
        $dirFull = $mT144['T144_Url'];
        $dirMini = $mT144['T144_Url'];
    } else {
        $D003=mysql_query("SELECT D003_Imagem FROM D001 left join D003 on D003_Id=D001_D003_Id WHERE D001_Id='{$D001_Id}'");
        $mD003=mysql_fetch_array($D003);
        
        $idProduto = $mD003['D003_Imagem'];
        $pathinfo = pathinfo($mD003['D003_Imagem']);
        $dir     = $g['pathRaiz'] . "dados_usuarios/{$confUsuario['dbDatabase']}/produtos/linhas/{$mD003['D003_Imagem']}";
        $dirFull = "/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/produtos/linhas/{$mD003['D003_Imagem']}";
        $dirMini = "/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/produtos/linhas/{$mD003['D003_Imagem']}";
        if (!file_exists($dir)){
            $dirMini = "/hardness3/static/img/indisponivel100x100.jpg";
            $dirFull = "/hardness3/static/img/indisponivel100x100.jpg";
        }
    }

  if (!empty($idProduto) && $pathinfo['extension']=='pdf') {
        $dirMini = "/hardness3/static/img/format-pdf-file.png";
    }

    if (empty($idProduto)) {
        $dirMini = "/hardness3/static/img/indisponivel100x100.jpg";
        $dirFull = "/hardness3/static/img/indisponivel100x100.jpg";
    }

    $foto = "<img src='{$dirMini}' style='max-width:{$tamanhoPx}px;max-height:{$tamanhoPx}px' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Imagem Original', '/crm/crm001/outro/crm001ImagemOriginal/', '&urlImagem=' + encodeURIComponent('{$dirFull}'), [900,600])\"><br>";

    return $foto;
}


